<?php
class Driver_api_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }
     /***************** General API's Function *****************/
    public function getLanguages($current_lang){
        $result = $this->db->select('*')->get_where('languages',array('language_slug'=>$current_lang))->first_row();
        return $result;
    }
    public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->get($table)->first_row();
    } 
    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
  
    // Login
    public function getLogin($password, $email = NULL, $phone = NULL, $phone_code = NULL)
    {        
        $enc_pass  = md5(SALT.$password);
        $this->db->select('entity_id,first_name,last_name,status,active,email,mobile_number,phone_code,image,notification,availability_status,driver_temperature,driver_traking_map.latitude');
        //$this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        if($phone){
            $this->db->where('mobile_number',$phone);
        }
        if($email){
            $this->db->where('email',$email);
        }
        if($phone_code)
        {
            if(strstr($phone_code,"+"))
            {
                $phone_codeval=str_replace("+", "", $phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }
            else
            {
                $phone_codeval="+".$phone_code;
                //$this->db->where('phone_code',$phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }            
        }
        $this->db->where('password',$enc_pass);
        $this->db->where('user_type','Driver');
        return $this->db->get('users')->first_row(); 
    }
    // get Driver details
    public function getDriverDetails($user_id)
    {        
        $this->db->select('email,entity_id,first_name,last_name,status,active,mobile_number,image,notification,availability_status,language_slug,driver_traking_map.latitude,driver_traking_map.longitude');
        //$this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row(); 
    }
    // Update User
    public function updateUser($tableName,$data,$fieldName,$UserID)
    {
        $this->db->where($fieldName,$UserID);
        $this->db->update($tableName,$data);
    }
    // check token for every API Call
    /*
        Author: Chirag Thoriya
        Update: 'status' => 1
        Description: added parameter in query to validate user must be active/status 1
        Updated on: 21/12/2020
    */
    public function checkToken($userid)
    {
        return $this->db->get_where('users', array(
                'entity_id'     => $userid,
                'status'        => 1,
                'user_type'     => 'Driver'
            )
        )->first_row();
    }
    // Common Add Records
    public function addRecord($table,$data)
    {
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }
    // Common Add Records Batch
    public function addRecordBatch($table,$data)
    {
        return $this->db->insert_batch($table, $data);
    }
    public function deleteRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->delete($table);
    }
    //get event
    public function getallOrder($user_id,$user_timezone='UTC',$language_slug='')
    {
        if(!$language_slug){
            $default_lang = $this->common_model->getdefaultlang();
            $language_slug = $default_lang->language_slug;
        }

        $currentDateTime = date('Y-m-d H:i:s');
        $currentDateTime = $this->common_model->getZonebaseCurrentTime($currentDateTime,$user_timezone);
        $default_currency = get_default_system_currency();
        //current
        $this->db->select('order_detail.restaurant_detail,order_detail.order_id,order_driver_map.driver_map_id,order_master.order_status,order_master.total_rate,order_master.subtotal,order_master.created_date,order_detail.user_detail,users.mobile_number,users.image,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code,order_master.transaction_id,order_master.delivery_instructions,order_driver_map.is_accept,order_detail.user_mobile_number,order_master.order_date,order_master.scheduled_date,order_master.slot_open_time, order_master.order_delivery, order_master.payment_option');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        $this->db->where('order_master.order_delivery','Delivery');
        $this->db->where('order_driver_map.is_accept !=',2);
        //$this->db->where('DATE(order_master.order_date)',date('Y-m-d'));
        $this->db->order_by('order_master.entity_id','desc');
        $current_order = $this->db->get('order_master')->result();
        
        $current = array();
        if(!empty($current_order)){
            foreach ($current_order as $key => $value) {
                if(!isset($value->order_id)){
                    $current[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $user_detail = unserialize($value->user_detail);
                    $current[$value->order_id]['name'] = $restaurant_detail->name;
                    $res_phone_number = $restaurant_detail->phone_number;
                    if(!empty($restaurant_detail->phone_number) && !empty($restaurant_detail->phone_code)){
                        $res_phone_number = '+'.$restaurant_detail->phone_code.$restaurant_detail->phone_number;
                    }
                    $current[$value->order_id]['res_phone_number'] = $res_phone_number;
                    $current[$value->order_id]['res_address'] = $restaurant_detail->address;
                    $current[$value->order_id]['image'] = ($restaurant_detail->image)?image_url.$restaurant_detail->image:'';
                    $current[$value->order_id]['res_latitude'] = $value->latitude;
                    $current[$value->order_id]['res_longitude'] = $value->longitude;
                    $current[$value->order_id]['order_id'] = $value->order_id;
                    $current[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $current[$value->order_id]['is_order_assigned'] = ($value->is_accept == 1)?1:0;
                    $current[$value->order_id]['subtotal'] = $value->subtotal;
                    $current[$value->order_id]['total_rate'] = $value->total_rate;
                    $current[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                    $current[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                    $current[$value->order_id]['order_status'] = $value->order_status;
                    $current[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $current[$value->order_id]['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                    $current[$value->order_id]['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                    // $current[$value->order_id]['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
                    $current[$value->order_id]['address'] = $user_detail['address'].','.$user_detail['landmark'];
                    $current[$value->order_id]['address_label'] = $user_detail['address_label'];
                    $current[$value->order_id]['phone_number'] = ($value->user_mobile_number)?'+'.$value->user_mobile_number:'';
                    $current[$value->order_id]['user_image'] = ($value->image)?image_url.$value->image:'';
                    if($value->scheduled_date && $value->slot_open_time) {
                        $order_scheduled_datetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_open_time"));
                        $current[$value->order_id]['date'] = $this->common_model->getZonebaseCurrentTime($order_scheduled_datetime,$user_timezone);
                    } else {
                        $current[$value->order_id]['date'] = ($value->order_date) ? $this->common_model->getZonebaseCurrentTime($value->order_date,$user_timezone) : (($value->created_date) ? $this->common_model->getZonebaseCurrentTime($value->created_date,$user_timezone) : '');
                    }
                    $current[$value->order_id]['transaction_id'] = $value->transaction_id;
                    $current[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                    $current[$value->order_id]['delivery_instructions'] = $value->delivery_instructions;
                    
                    $payment_option_val = '';
                    if($value->payment_option){
                        if(strtolower($value->order_delivery) == 'dinein' && $value->payment_option == 'cod'){
                            if($language_slug == 'en'){
                                $payment_option_val = $this->lang->line('pay_at_counter_en');
                            }
                            if($language_slug == 'fr'){
                                $payment_option_val = $this->lang->line('pay_at_counter_fr');
                            }
                            if($language_slug == 'ar'){
                                $payment_option_val = $this->lang->line('pay_at_counter_ar');
                            }
                        }else{
                            $payment_option_detail = $this->common_model->get_payment_method_detail(strtolower($value->payment_option));                            
                            if($language_slug == 'en'){
                                $payment_option_val = $payment_option_detail->display_name_en;
                            }
                            if($language_slug == 'fr'){
                                $payment_option_val = $payment_option_detail->display_name_fr;
                            }
                            if($language_slug == 'ar'){
                                $payment_option_val = $payment_option_detail->display_name_ar;
                            }
                        }
                    }
                    $current[$value->order_id]['payment_option']  = $payment_option_val;
                }
            }
        }
        $finalArray = array();
        foreach ($current as $key => $val) {
           $finalArray[] = $val; 
        }
        $data['current'] = $finalArray;
        //past
        $this->db->select('order_detail.restaurant_detail,order_detail.order_id,order_driver_map.driver_map_id,order_master.order_status,order_driver_map.cancel_reason,order_master.total_rate,order_master.subtotal,order_master.created_date,order_detail.user_detail,users.mobile_number,users.image,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code,order_master.transaction_id,order_master.delivery_instructions,order_driver_map.is_accept,order_detail.user_mobile_number,order_master.order_date,order_master.scheduled_date,order_master.slot_open_time, order_master.order_delivery, order_master.payment_option');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
         $this->db->where('order_master.order_delivery','Delivery');
        $this->db->order_by('order_master.entity_id','desc');
        $past_order = $this->db->get('order_master')->result();
        $past = array();
        if(!empty($past_order)){
            foreach ($past_order as $key => $value) {
                if(!isset($value->order_id)){
                    $past[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $user_detail = unserialize($value->user_detail);
                    $past[$value->order_id]['name'] = $restaurant_detail->name;        
                    $res_phone_number = $restaurant_detail->phone_number;
                    if(!empty($restaurant_detail->phone_number) && !empty($restaurant_detail->phone_code)){
                        $res_phone_number = '+'.$restaurant_detail->phone_code.$restaurant_detail->phone_number;
                    }    
                    $past[$value->order_id]['res_phone_number'] = $res_phone_number;
                    $past[$value->order_id]['res_address'] = $restaurant_detail->address;
                    $past[$value->order_id]['image'] = ($restaurant_detail->image)?image_url.$restaurant_detail->image:'';
                    $past[$value->order_id]['res_latitude'] = $value->latitude;
                    $past[$value->order_id]['res_longitude'] = $value->longitude;
                    $past[$value->order_id]['order_id'] = $value->order_id;
                    $past[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $past[$value->order_id]['is_order_assigned'] = ($value->is_accept == 1)?1:0;
                    $past[$value->order_id]['subtotal'] = $value->subtotal;
                    $past[$value->order_id]['total_rate'] = $value->total_rate;
                    $past[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                    $past[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                    $past[$value->order_id]['order_status'] = $value->order_status;
                    $past[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $past[$value->order_id]['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                    $past[$value->order_id]['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                    // $past[$value->order_id]['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
                    $past[$value->order_id]['address'] = $user_detail['address'];
                    $past[$value->order_id]['address_label'] = $user_detail['address_label'];
                    $past[$value->order_id]['phone_number'] =($value->user_mobile_number)?'+'.$value->user_mobile_number:'';
                    $past[$value->order_id]['user_image'] = ($value->image)?image_url.$value->image:'';
                    if($value->scheduled_date && $value->slot_open_time) {
                        $order_scheduled_datetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_open_time"));
                        $past[$value->order_id]['date'] = $this->common_model->getZonebaseCurrentTime($order_scheduled_datetime,$user_timezone);
                    } else {
                        $past[$value->order_id]['date'] = ($value->order_date) ? $this->common_model->getZonebaseCurrentTime($value->order_date,$user_timezone) : (($value->created_date) ? $this->common_model->getZonebaseCurrentTime($value->created_date,$user_timezone) : '');
                    }
                    $past[$value->order_id]['transaction_id'] = $value->transaction_id;
                    $past[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                    $past[$value->order_id]['delivery_instructions'] = $value->delivery_instructions;

                    $payment_option_val = '';
                    if($value->payment_option){
                        if(strtolower($value->order_delivery) == 'dinein' && $value->payment_option == 'cod'){
                            if($language_slug == 'en'){
                                $payment_option_val = $this->lang->line('pay_at_counter_en');
                            }
                            if($language_slug == 'fr'){
                                $payment_option_val = $this->lang->line('pay_at_counter_fr');
                            }
                            if($language_slug == 'ar'){
                                $payment_option_val = $this->lang->line('pay_at_counter_ar');
                            }
                        }else{
                            $payment_option_detail = $this->common_model->get_payment_method_detail(strtolower($value->payment_option));                            
                            if($language_slug == 'en'){
                                $payment_option_val = $payment_option_detail->display_name_en;
                            }
                            if($language_slug == 'fr'){
                                $payment_option_val = $payment_option_detail->display_name_fr;
                            }
                            if($language_slug == 'ar'){
                                $payment_option_val = $payment_option_detail->display_name_ar;
                            }
                        }
                    }
                    $past[$value->order_id]['payment_option']  = $payment_option_val;
                }
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
           $final[] = $val; 
        }
        $data['past'] = $final;
        //total earning of a driver
        $total_money_credited = 0;
        $this->db->select('order_master.order_status,order_driver_map.commission');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('order_master.order_status','delivered');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->where('order_driver_map.commission!=','');
        $this->db->group_by('order_driver_map.order_id');
        $driver_commission = $this->db->get('order_driver_map')->result();
        if(!empty($driver_commission)){
            foreach($driver_commission as $drivermap_key => $drivermap_value){
                $total_money_credited += $drivermap_value->commission;
            }
        }
        $data['past_order_total_commission'] = $total_money_credited;
        return $data;
    } 
    //accept order
    public function acceptOrder($order_id,$driver_map_id,$user_id,$current_order_status)
    {
        $count = $this->db->set('is_accept',1)->where('driver_id',$user_id)->where('order_id', $order_id)->where('driver_map_id',$driver_map_id)->update('order_driver_map');
        if($count == 1){
            $this->db->where('order_id', $order_id);
            $this->db->where('is_accept !=',1);
            $this->db->where('is_accept !=',2);
            $this->db->where('driver_id !=',$user_id);
            $this->db->delete('order_driver_map');
        }
        $this->db->set('driver_id',$user_id)->where('order_id', $order_id)->update('tips');
        if($current_order_status == 'placed' || $current_order_status == 'accepted') {
            //$this->db->set('order_status','preparing')->where('entity_id', $order_id)->update('order_master');
        }
        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug,users.first_name,users.last_name,users.mobile_number,order_detail.user_detail,restaurant_address.latitude,restaurant_address.longitude');
        $this->db->join('order_master','users.entity_id = order_master.user_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $this->db->where('users.status',1);
        $device = $this->db->get('users')->first_row();
        
        // load language
        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);
        
        $info = array();
        if($current_order_status == 'placed' || $current_order_status == 'accepted') {
            // if($device->device_id){  
            //     #prep the bundle
            //     $fields = array();            
            //     $message = $this->lang->line('order_preparing');
            //     $fields['to'] = $device->device_id; // only one user to send push notification
            //     $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            //     $fields['data'] = array ('screenType'=>'order');
               
            //     $headers = array (
            //         'Authorization: key=' . FCM_KEY,
            //         'Content-Type: application/json'
            //     );
            //     #Send Reponse To FireBase Server    
            //     $ch = curl_init();
            //     curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            //     curl_setopt( $ch,CURLOPT_POST, true );
            //     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            //     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            //     curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            //     $result = curl_exec($ch);
            //     curl_close($ch);  
            // }
        } 
        $user_detail = unserialize($device->user_detail);
        $info['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
        $info['latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
        $info['longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
        $info['phone_number'] = $device->mobile_number;
        $info['res_latitude'] = $device->latitude;
        $info['res_longitude'] = $device->longitude;
        $info['name'] = $device->first_name.' '.$device->last_name; 
        $info['order_user_id'] = $device->entity_id;
        return $info;
    }
    //order delivered
    public function deliveredOrder($order_id,$status,$subtotal,$driver_id='')
    {   
        $this->db->set('order_status',$status)->where('entity_id', $order_id)->update('order_master');
        if($status == 'delivered'){
            $this->db->select('order_driver_map.distance,order_master.delivery_charge');
            $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
            $this->db->where('order_master.entity_id',$order_id);
            $distance = $this->db->get('order_master')->first_row();
            
            $comsn = '';
            //check if commission of driver is enabled in system options
            $this->db->select('OptionValue');
            $is_enabled = $this->db->get_where('system_option',array('OptionSlug'=>'enable_commission_of_driver'))->first_row();
            $is_enabled = $is_enabled->OptionValue;
            if($is_enabled == 1){
                if($distance->distance > 3){
                    $this->db->select('OptionValue');
                    $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_more'))->first_row();
                    $comsn = $comsn->OptionValue;
                }else{
                    $this->db->select('OptionValue');
                    $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_less'))->first_row(); 
                    $comsn = $comsn->OptionValue;
                }
            } else {
                $comsn = $distance->delivery_charge;
            }
            if($comsn){
                $data = array('driver_commission'=>$comsn,'commission'=>$comsn);
                $this->db->where('order_id', $order_id);
                $this->db->update('order_driver_map',$data);
            } 
        }
        $this->db->select('item_detail,user_detail,currencies.currency_symbol,currencies.currency_code,order_master.restaurant_id');
        $this->db->join('order_master','order_detail.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('order_id',$order_id);
        $detail =  $this->db->get('order_detail')->first_row();
        $info = array();
        if(!empty($detail)){
            $default_currency = get_default_system_currency();
            $order_detail = unserialize($detail->item_detail);
            $user_detail = unserialize($detail->user_detail);
            $info['order_detail'] = $order_detail;
            $info['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $detail->currency_code;
            $info['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $detail->currency_symbol;
            // $info['address'] = $user_detail['address'].' '.$user_detail['landmark'].' '.$user_detail['zipcode'].' '.$user_detail['city'];
            $info['address'] = $user_detail['address'];
        }
        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug');
        $this->db->join('order_master','users.entity_id = order_master.user_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $this->db->where('users.status',1);
        $device = $this->db->get('users')->first_row();
        // load language
        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);
 
        if($device->device_id){  
            #prep the bundle
            $fields = array();            
            $message = sprintf($this->lang->line('push_order_delived'),$order_id);
            $fields['to'] = $device->device_id; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['notification']['title'] = $this->lang->line('customer_app_name');
            $fields['data'] = array ('screenType'=>'delivery','restaurant_id'=>$detail->restaurant_id,'order_id'=>$order_id,'driver_id'=>$driver_id);
           
            $headers = array (
                'Authorization: key=' . FCM_KEY,
                'Content-Type: application/json'
            );
            #Send Reponse To FireBase Server    
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result = curl_exec($ch);
            curl_close($ch);  
        } 
        return $info;
    }
    //get commission list
    public function getCommissionList($user_id,$user_timezone='UTC')
    {
        //last order
        $this->db->select('order_master.total_rate,order_master.order_status,order_master.cancel_reason,order_status.time,order_detail.restaurant_detail,order_detail.user_detail,order_driver_map.order_id,order_driver_map.driver_id,order_driver_map.commission,currencies.currency_symbol,currencies.currency_code,tips.amount as driver_tip');
        
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('order_status','order_driver_map.order_id = order_status.order_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('tips','order_master.entity_id = tips.order_id AND tips.amount > 0 AND (tips.refund_status != "refunded" OR tips.refund_status is NULL)','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('order_driver_map.is_accept','1');
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->order_by('order_master.entity_id','desc');
        //$this->db->limit(2);
        $details =  $this->db->get('order_master')->result();
        $last_address = array();
        $last_user_id = '';
        $finalArray = array();
        if(!empty($details)){
            $default_currency = get_default_system_currency();
            foreach ($details as $key => $value) {
                $last_user_id = $value->order_id;
                if(!isset($value->order_id)){
                    $last_address[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $address = unserialize($value->user_detail);
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $last_address[$value->order_id]['time'] = ($value->time)?$this->common_model->getZonebaseDate($value->time,$user_timezone):'';
                    $last_address[$value->order_id]['date'] =  ($value->time)?$this->common_model->getZonebaseDateLJM($value->time,$user_timezone):'';
                    $last_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    /*order status display :: start*/
                    $cancel_reason = '';
                    if(!empty($value->cancel_reason)){
                        $cancel_reason = ' ('.$value->cancel_reason.')';
                    }
                    $last_address[$value->order_id]['order_status_display'] = (strtolower($value->order_status) == "cancel") ? $this->lang->line('cancelled') : ((strtolower($value->order_status) == "complete")?$this->lang->line('completed'):$this->lang->line(strtolower($value->order_status))).$cancel_reason;
                    /*order status display :: end*/
                    $last_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $last_address[$value->order_id]['order_id'] = $value->order_id;
                    $last_address[$value->order_id]['commission'] = ($value->commission && $value->order_status!='cancel')?$value->commission:'';
                    $last_address[$value->order_id]['driver_tip'] = ($value->driver_tip && $value->driver_tip>0)?$value->driver_tip:'';
                    $last_address[$value->order_id]['name'] = $restaurant_detail->name;
                    $last_address[$value->order_id]['image'] = ($restaurant_detail->image)?image_url.$restaurant_detail->image:'';
                    // $last_address[$value->order_id]['address'] = $address['address'].' '.$address['landmark'].' '.$address['zipcode'].' '.$address['city'];
                    $last_address[$value->order_id]['address'] = $address['address'];
                    $last_address[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $restaurant_detail->currency_symbol;
                    $last_address[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $restaurant_detail->currency_code;
                }
            }
            foreach ($last_address as $key => $val) {
               $finalArray[] = $val; 
            }
        }
       
        $data['last'] = $finalArray;
        /*//previous order
        $this->db->select('order_master.total_rate,order_master.order_status,order_status.time,order_detail.restaurant_detail,order_detail.user_detail,order_driver_map.order_id,order_driver_map.driver_id,order_driver_map.commission,order_master.order_status,order_master.total_rate');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->join('order_status','order_driver_map.order_id = order_status.order_id','left');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        if($last_user_id){
             $this->db->where('order_driver_map.order_id !=',$last_user_id);
        }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->order_by('order_master.entity_id','desc');
        $details =  $this->db->get('order_master')->result();
        $user_address = array();
        $final = array();
        if(!empty($details)){
            foreach ($details as $key => $value) {
                if(!isset($value->order_id)){
                    $user_address[$value->order_id] = array();
                }
                if(isset($value->order_id)){
                    $address = unserialize($value->user_detail);
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $user_address[$value->order_id]['time'] = ($value->time)?$this->common_model->getZonebaseDate($value->time,$user_timezone):'';
                    $user_address[$value->order_id]['date'] =  ($value->time)?$this->common_model->getZonebaseDateLJM($value->time,$user_timezone):'';
                    $user_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    $user_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $user_address[$value->order_id]['order_id'] = $value->order_id;
                    $user_address[$value->order_id]['commission'] = ($value->commission && $value->order_status!='cancel')?$value->commission:'';
                    $user_address[$value->order_id]['name'] = $restaurant_detail->name;
                    $user_address[$value->order_id]['image'] = ($restaurant_detail->image)?image_url.$restaurant_detail->image:'';
                    // $user_address[$value->order_id]['address'] = $address['address'].' '.$address['landmark'].' '.$address['zipcode'].' '.$address['city'];
                    $user_address[$value->order_id]['address'] = $address['address'];
                    $user_address[$value->order_id]['currency_symbol'] = $restaurant_detail->currency_symbol;
                    $user_address[$value->order_id]['currency_code'] = $restaurant_detail->currency_code;
                }
            }
            foreach ($user_address as $key => $val) {
               $final[] = $val; 
            }
        }
        $data['previous'] = $final;*/
        //total earning of a driver
        $total_money_credited = 0;
        $this->db->select('order_master.order_status,order_driver_map.commission');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        $this->db->where('order_master.order_status','delivered');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->where('order_driver_map.commission!=','');
        $this->db->group_by('order_driver_map.order_id');
        $driver_commission = $this->db->get('order_driver_map')->result();
        if(!empty($driver_commission)){
            foreach($driver_commission as $drivermap_key => $drivermap_value){
                $total_money_credited += $drivermap_value->commission;
            }
        }
        $data['past_order_total_commission'] = $total_money_credited;
        return $data;
    }
    //get user of order
    public function getUserofOrder($order_id){
        $this->db->select('users.entity_id,users.device_id,users.language_slug,users.notification');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $this->db->get('order_master')->first_row();
    }
    // get config
    public function getSystemOption($OptionSlug)
    {        
        $this->db->select('OptionValue');                
        $this->db->where('OptionSlug',$OptionSlug);        
        return $this->db->get('system_option')->first_row();
    }
    //get remain driver detail from order_driver_map :: Start
    public function getOrderDriverMap($order_id,$driver_id,$restaurant_id, $orders_user_id)
    {
        $this->db->select('driver_map_id');
        $this->db->where('order_id',$order_id);
        $this->db->where('driver_id!=',$driver_id);
        $result = $this->db->get('order_driver_map')->result();
        if(!$result && empty($result))
        {
            $this->db->select('restaurant.entity_id,restaurant.content_id');
            $this->db->where('entity_id',$restaurant_id);
            $restaurant_content_id = $this->db->get('restaurant')->first_row();
            /* drivers assigned to multiple restaurant - start */
            $this->db->select('driver_id');
            $this->db->where('driver_id!=',$driver_id);
            $this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
            $driver = $this->db->get('restaurant_driver_map')->result_array();
            /* drivers assigned to multiple restaurant - end */

            if($driver && !empty($driver))
            {
                $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
                //$this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
                //driver tracking join last entry changes :: start
                $this->db->join('(select max(traking_id) as max_id, driver_id 
                from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
                $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
                //driver tracking join last entry changes :: end
                $this->db->where('users.user_type','Driver');
                $this->db->where('users.status',1);
                $this->db->where('users.availability_status',1);
                $this->db->where('users.active',1);
                $this->db->where('driver_traking_map.latitude!=','');
                $this->db->where('driver_traking_map.longitude!=','');
                if(!empty($driver)){
                    $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
                }
                $this->db->group_by('driver_traking_map.driver_id');
                $this->db->order_by('driver_traking_map.created_date','desc');
                $detail = $this->db->get('users')->result();
                if(!empty($detail))
                {
                    if($orders_user_id>0){
                        $this->db->select('u_address.latitude, u_address.longitude');
                        $this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
                        $this->db->where('order_master.entity_id',$order_id);
                        $user_details = $this->db->get('order_master')->first_row();
                        $user_lat = $user_details->latitude;
                        $user_long = $user_details->longitude;
                    } else {
                        $this->db->select('user_detail');
                        $this->db->where('order_id',$order_id);
                        $user_details = $this->db->get('order_detail')->first_row();
                        $user_lat_long = unserialize($user_details->user_detail);
                        $user_lat = $user_lat_long['latitude'];
                        $user_long = $user_lat_long['longitude'];
                    }
                    /*Begin::Finding Distance between restaurant to user*/
                    $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
                    $this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
                    $this->db->where('order_master.entity_id',$order_id);
                    $user_to_restaurant_distance = $this->db->get('order_master')->first_row();
                    /*End::Finding Distance between restaurant to user*/
                    foreach ($detail as $key => $value)
                    {
                        $longitude = $value->longitude;
                        $latitude = $value->latitude;
                        $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                        $this->db->where('restaurant.entity_id',$restaurant_id);
                        $this->db->having('distance <',NEAR_KM);
                        $result = $this->db->get('restaurant')->result();
                        if(!empty($result))
                        {
                            if($value->device_id){
                                //get langauge
                                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                                $this->lang->load('messages_lang', $languages->language_directory); 
                                $flag = true;   
                                $array = array(
                                    'order_id'=>$order_id,
                                    'driver_id'=>$value->driver_id,
                                    'date'=>date('Y-m-d H:i:s'),
                                    'distance'=>$user_to_restaurant_distance->distance
                                );
                                $id = $this->addRecord('order_driver_map',$array);
                                #prep the bundle
                                $fields = array();            
                                $message = sprintf($this->lang->line('push_new_order'),$oder_id);
                                $fields['to'] = $value->device_id; // only one user to send push notification
                                $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                                $fields['notification']['title'] = $this->lang->line('driver_app_name');
                                $fields['data'] = array ('screenType'=>'order');
                               
                                $headers = array (
                                    'Authorization: key=' . FCM_KEY,
                                    'Content-Type: application/json'
                                );
                                #Send Reponse To FireBase Server    
                                $ch = curl_init();
                                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                                curl_setopt( $ch,CURLOPT_POST, true );
                                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                                $result = curl_exec($ch);
                                curl_close($ch);            
                            } 
                        }
                    }
                }
            }
        }
    }
    //get remain driver detail from order_driver_map :: End
    // get order details
    public function getOrderDetails($order_id){
        $this->db->select('item_detail,user_detail,currencies.currency_symbol,currencies.currency_code,order_master.order_status,order_master.cancel_reason');
        $this->db->join('order_master','order_detail.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('order_id',$order_id);
        $detail =  $this->db->get('order_detail')->first_row();
        $info = array();
        if(!empty($detail)){
            $default_currency = get_default_system_currency();
            $order_detail = unserialize($detail->item_detail);
            $user_detail = unserialize($detail->user_detail);
            $info['order_status'] = $detail->order_status;
            if ($detail->order_status == "cancelled") {
                $info['cancel_reason'] = $detail->cancel_reason;
            }
            $info['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $detail->currency_code;
            $info['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $detail->currency_symbol;
            $info['products'] = $order_detail;
        }
        return $info;
    }
    //get active languages
    public function getActiveLanguages(){
        $result = $this->db->select('*')->get_where('languages',array('active'=>1))->result();
        $lang = array();
        foreach ($result as $key => $value) {
            $lang[$key]['language_code'] = $value->language_slug;
            $lang[$key]['language_name'] = $this->lang->line(strtolower($value->language_directory));
        }
        return $lang;
    }
    //update record with multiple where
    public function updateMultipleWhere($table,$whereArray,$data)
    {
        $this->db->where($whereArray);
        $this->db->update($table,$data);
    }
    // function to get users total wallet money
    public function getUsersWalletMoney($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    // delete wallet history by order_id
    public function deletewallethistory($order_id){
        $this->db->where('order_id',$order_id);
        $this->db->update('wallet_history',array('is_deleted'=> 1));
        return $this->db->affected_rows();
    }
    public function checkOrderStatus($order_id)
    {
        $current_status = '';
        $this->db->select('order_status');        
        $this->db->where('entity_id',$order_id);
        $res = $this->db->get('order_master')->first_row();
        if($res && !empty($res))
        {
            $current_status = $res->order_status;
        }
        return $current_status;
    }
    //Code for driver commisson :: Start
    public function setDriverCommission($order_id,$driver_id)
    {
        $this->db->select('order_driver_map.distance,order_master.delivery_charge');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $distance = $this->db->get('order_master')->first_row();
                      
        $comsn = 0;
        //check if commission of driver is enabled in system options
        $this->db->select('OptionValue');
        $is_enabled = $this->db->get_where('system_option',array('OptionSlug'=>'enable_commission_of_driver'))->first_row();
        $is_enabled = $is_enabled->OptionValue;
        if($is_enabled == 1){
            if($distance->distance > 3){
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_more'))->first_row();
                $comsn = $comsn->OptionValue;
            }else{
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_less'))->first_row(); 
                $comsn = $comsn->OptionValue;
            }
        } else {
            $comsn = $distance->delivery_charge;
        }
        if($comsn){
            $data = array('driver_commission'=>$comsn,'commission'=>$comsn);
            $this->db->where('order_id', $order_id);
            $this->db->update('order_driver_map',$data);
        }        
    }
    //Code for driver commisson :: End
}
?>