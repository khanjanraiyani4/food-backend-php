<?php
class Myprofile_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    //server side check email exist
    public function checkEmail($Email,$UserID)
    {
        $this->db->where('email',$Email);
        $this->db->where('entity_id !=',$UserID);
        $this->db->where('user_type','User');
        return $this->db->get('users')->num_rows();   
    }
    //server side check email exist
    public function checkPhone($Phone,$UserID)
    {
        $this->db->where('mobile_number',$Phone);
        $this->db->where('entity_id !=',$UserID);
        $this->db->where('user_type','User');
        return $this->db->get('users')->num_rows();  
    }
    //get order detail
    public function getOrderDetail($flag,$user_id,$order_id,$count=8,$page_no=1,$user_type='User')
    {
        $page_no = ($page_no > 0)?$page_no-1:0;
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id, restaurant.enable_hours,restaurant.status as restaurant_status,tips.amount as driver_tip,tips.tip_percentage,restaurant.content_id as res_content_id,tips.tips_transaction_id,tips.refund_status as tips_refund_status,restaurant.restaurant_slug');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        //$this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        if($flag == 'process'){
            $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel" AND order_master.order_status != "complete" AND order_master.order_status != "rejected")');
        } 
        if($flag == 'past'){
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel" OR order_master.order_status = "complete" OR order_master.order_status = "rejected")');
        }
        if ($user_id != '') {
            if($user_type == 'Agent'){
                $this->db->where('order_master.agent_id',$user_id);
            } else {
                $this->db->where('order_master.user_id',$user_id);
            }
        }
        if ($order_id != '') {
            $this->db->where('order_master.entity_id',$order_id);
        }
        $this->db->order_by('order_master.entity_id','desc');
        if($user_id != ''){
            $this->db->limit($count,$page_no*$count);
            $this->db->group_by('order_master.entity_id');
        }
        else
        {
            $this->db->group_by(array("order_master.entity_id", "status.order_status"));
        }
        $this->db->where('order_master.order_delivery !=','DineIn');
        $result =  $this->db->get('order_master')->result();
        $items = array();
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        //$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            /*$currency_symbol = $this->common_model->getCurrencySymbol($value->currency_id);*/
            if(!isset($items[$value->order_id])){
                $items[$value->order_id] = array();
                //$items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
            }
            if(isset($items[$value->order_id])) 
            {        
                $type = ($value->tax_type == 'Percentage')?'%':'';
                $service_type = ($value->service_fee_type == 'Percentage')?'%':'';
                $creditcard_type = ($value->creditcard_fee_type == 'Percentage')?'%':'';
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                $items[$value->order_id]['res_content_id'] = $value->res_content_id;
                $items[$value->order_id]['restaurant_name'] = $value->name;
                $items[$value->order_id]['restaurant_slug'] = $value->restaurant_slug;
                $items[$value->order_id]['restaurant_image'] = $value->rest_image;
                $items[$value->order_id]['user_name'] = $value->user_name;
                $items[$value->order_id]['user_mobile_number'] = $value->user_mobile_number;
                $items[$value->order_id]['restaurant_address'] = $value->address;
                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }
                /*wallet money changes start*/
                $wallet_history = $this->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'debit' => 1));
                $wallet = ($wallet_history)?array('label'=>$this->lang->line('wallet_discount'),'value'=>$wallet_history->amount, 'label_key'=>'Wallet Discount'):'';
                /*wallet money changes end*/
                if($discount){
                $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    $discount,
                    array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                    array('label'=>$this->lang->line('service_tax'),'value'=>$value->tax_rate.$type,'label_key'=>"Service Tax"),
                    array('label'=>$this->lang->line('service_fee'),'value'=>$value->service_fee.$service_type,'label_key'=>"Service Fee"),
                    array('label'=>$this->lang->line('creditcard_fee'),'value'=>$value->creditcard_fee.$creditcard_type,'label_key'=>"Credit Card Fee"),
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_discount,'label_key'=>"Coupon Amount"),
                    $wallet,
                    ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip'),'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }else{
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                    array('label'=>$this->lang->line('service_tax'),'value'=>$value->tax_rate.$type,'label_key'=>"Service Tax"),
                    array('label'=>$this->lang->line('service_fee'),'value'=>$value->service_fee.$service_type,'label_key'=>"Service Fee"),
                    array('label'=>$this->lang->line('creditcard_fee'),'value'=>$value->creditcard_fee.$creditcard_type,'label_key'=>"Credit Card Fee"),
                    array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_discount,'label_key'=>"Coupon Amount"),
                    $wallet,
                    ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip'),'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                $timing =  $value->timings;
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day))
                        {
                            $close = 'Closed';
                            if($value->enable_hours=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                if(!empty($values['open']) && !empty($values['close']))
                                {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']));
                                }                            
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }
                            else
                            {
                                $newTimingArr[strtolower($day)]['open'] = '';
                                $newTimingArr[strtolower($day)]['close'] = '';
                                $newTimingArr[strtolower($day)]['off'] = 'close';
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }
                        }
                    }
                    //$items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }
                else
                {
                    $newTimingArr[strtolower($day)]['closing'] = 'close';
                    $newTimingArr[strtolower($day)]['open'] = '';
                    $newTimingArr[strtolower($day)]['close'] ='';
                    $newTimingArr[strtolower($day)]['off'] = 'close';
                }
                //user details code :: start
                $user_details =  $value->user_detail;
                if($user_details){
                    $user_details =  unserialize(html_entity_decode($user_details));
                }
                //user details code :: end
                $items[$value->order_id]['user_details'] = $user_details;
                $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                $items[$value->order_id]['enable_hours'] = $value->enable_hours;
                $items[$value->order_id]['restaurant_status'] = $value->restaurant_status;
                $items[$value->order_id]['order_status'] = $value->order_status;                
                $items[$value->order_id]['payment_status'] = $value->payment_status;
                $items[$value->order_id]['payment_method'] = $value->payment_option;
                $items[$value->order_id]['transaction_id'] = $value->transaction_id;
                $items[$value->order_id]['reject_reason'] = $value->reject_reason;
                $items[$value->order_id]['cancel_reason'] = $value->cancel_reason;
                if((strtolower($value->order_delivery)=='pickup' && $value->order_status == "onGoing") || $value->order_status == "ready") {
                    $items[$value->order_id]['order_status'] = "order_ready";
                }
                if(($value->order_status == "placed" && $value->status=='1') || $value->order_status == "accepted")
                {
                    $value->ostatus == "accepted";
                    $items[$value->order_id]['order_status'] = "accepted";                    
                }
                
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['delivery_instructions'] =$value->delivery_instructions;
                //show cancel button :: start
                $created_date = date('Y-m-d H:i:s', strtotime($value->created_date));
                $current_time = date('Y-m-d H:i:s');

                //difference between current datetime and order datetime
                $datetime_diff = $this->common_model->dateDifference($created_date , $current_time);
                $diff_day = $datetime_diff['diff_day'];
                $diff_hr = $datetime_diff['diff_hr'];
                $diff_min = $datetime_diff['diff_min'];

                //Code for time check from system option :: Start
                $cancel_order_timerarr = $this->db->get_where('system_option',array('OptionSlug'=>'cancel_order_timer'))->first_row();
                $cancel_order_timeval = ($cancel_order_timerarr->OptionValue)?$cancel_order_timerarr->OptionValue:180;
                $cancel_order_timeval = $cancel_order_timeval/60;
                //Code for time check from system option :: End
                
                $items[$value->order_id]['show_cancel_order'] = ($diff_day>0 || $diff_hr>0 || $diff_min>=$cancel_order_timeval)?'0':'1';
                $items[$value->order_id]['time_diff'] = $diff_day.' '.$diff_hr.' '.$diff_min;
                $items[$value->order_id]['timer_order_date'] = $this->common_model->getZonebaseCurrentTime($value->order_date);
                //show cancel button :: end
                $order_time = ($value->time)?$value->time:$value->created_date;
                $items[$value->order_id]['placed'] = $this->common_model->getZonebaseTime($order_time);
                // if($value->ostatus == 'preparing')
                // {
                //     $items[$value->order_id]['preparing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time):'';
                // }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time):'';
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time):'';
                }
                $items[$value->order_id]['order_date'] = $this->common_model->getZonebaseDate($value->order_date);
                $items[$value->order_id]['order_dateorg'] = $value->order_date;
                $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_open_time"));
                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                $items[$value->order_id]['scheduled_date'] = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                $items[$value->order_id]['slot_open_time'] = ($value->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                $items[$value->order_id]['slot_close_time'] = ($value->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $valueee = array();
                        $this->db->select('image,restaurant_menu_item.status,restaurant_menu_item.food_type,food_type.entity_id as food_type_id,food_type.is_veg');
                        $this->db->where('restaurant_menu_item.entity_id',$valuee['item_id']);
                        $this->db->join('food_type','restaurant_menu_item.food_type = food_type.entity_id','left');
                        $data = $this->db->get('restaurant_menu_item')->first_row();
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';
                        $data1['is_veg'] = (!empty($data) && $data->is_veg != '')?$data->is_veg:'';
                        $valueee['image'] = (!empty($data) && $data->image != '')?$data1['image']:'';
                        $valueee['is_veg'] = (!empty($data) && $data->is_veg != '')?$data1['is_veg']:'';                        
                        if($valuee['is_customize'] == 1){
                            $customization = array();
                            foreach ($valuee['addons_category_list'] as $k => $val) {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if(isset($valuee['is_deal']) && $valuee['is_deal'] == 1){
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                        );
                                    }else{
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                            'add_ons_price'=>$mn['add_ons_price']
                                        );
                                       
                                    }
                                }
                                $customization[] = array(
                                    'addons_category_id'=>$val['addons_category_id'],
                                    'addons_category'=>$val['addons_category'],
                                    'addons_list'=>$addonscust
                                );
                            }
                            $valueee['addons_category_list'] = $customization;
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        //$valueee['comment'] = $valuee['comment'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                        $valueee['comment'] = ($valuee['comment'])?$valuee['comment']:'';
                        $value1[] =  $valueee; 
                    } 
                }
         
                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $value->phone_code.$value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    $driver['image'] = ($value->image)?$value->image:'';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $items[$value->order_id]['driver'] = $driver;
                }
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery')?'delivery':'pickup';
                $res_detail = unserialize($value->restaurant_detail);
                $items[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $res_detail->currency_symbol ;
                $items[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $res_detail->currency_code ;
                // $items[$value->order_id]['currency_symbol'] = $currency_symbol->currency_symbol;
                // $items[$value->order_id]['currency_code'] = $currency_symbol->currency_code;
                $items[$value->order_id]['tax_type'] = $value->tax_type;
                $items[$value->order_id]['service_fee_type'] = $value->service_fee_type;
                $items[$value->order_id]['creditcard_fee_type'] = $value->creditcard_fee_type;
                $items[$value->order_id]['refund_status'] = $value->refund_status;
                $items[$value->order_id]['tips_transaction_id'] = $value->tips_transaction_id;
                if($value->tips_transaction_id!=''){
                    $items[$value->order_id]['tips_refund_status'] = $value->tips_refund_status;
                }
                $items[$value->order_id]['tip_percentage'] = ($value->tip_percentage)?$value->tip_percentage:'';
            }
        }

        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        return $finalArray;
    }
    //Total order count code :: Start
    public function getOrderCount($flag,$user_id,$count='',$page_no='',$user_type='User')
    {   
        $this->db->select('order_master.entity_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        if($flag == 'process'){
            $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel" AND order_master.order_status != "complete" AND order_master.order_status != "rejected")');
        } 
        if($flag == 'past'){
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel" OR order_master.order_status = "complete" OR order_master.order_status = "rejected")');
        }        
        if($count!='' && $page_no!='')
        {
            $page_no = ($page_no > 0)?$page_no-1:0;
            $this->db->limit($count,$page_no*$count);
        }
        if($user_type == 'Agent'){
            $this->db->where('order_master.agent_id',$user_id);
        } else {
            $this->db->where('order_master.user_id',$user_id);
        }
        $this->db->group_by('order_master.entity_id');
        $this->db->where('order_master.order_delivery !=','DineIn');
        $result =  $this->db->get('order_master')->num_rows();
        return $result;
    }
    //Total order count code :: End
    //get event
    public function getBooking($user_id,$event_flag,$event_id=NULL){
        $currentDateTime = date('Y-m-d H:i:s');
        //$currentDateTime = $this->common_model->getZonebaseCurrentTime($currentDateTime);
        //upcoming
        $this->db->select('event.entity_id as event_id,event.booking_date,event.additional_request,event.cancel_reason,event.no_of_people,event_detail.package_detail,event_detail.restaurant_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code,restaurant.entity_id as restaurant_id,restaurant.content_id as res_content_id,restaurant.image as rest_image,event.created_date,event.event_status,restaurant.restaurant_slug, restaurant.status as restaurant_status');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        
        $this->db->join('restaurant','event.restaurant_id = restaurant.content_id','left');
        $this->db->join('review','restaurant.entity_id = review.restaurant_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);
        if ($event_flag == "upcoming") {
            $this->db->where('event.booking_date >=',$currentDateTime);
            $this->db->where('event.event_status !=', 'cancel');
        }
        if ($event_flag == "past") {
            $this->db->where('(event.booking_date <', $currentDateTime)->or_where("event.event_status = 'cancel')");
        }
        if ($event_id != '') {
            $this->db->where('event.entity_id',$event_id);
        }
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $result = $this->db->get('event')->result();
        $events = array();
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        //$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            $package_detail = '';
            $restaurant_detail = '';
            if(!isset($value->event_id)){
                $events[$value->event_id] = array();
            }
            if(isset($value->event_id))
            {
                $package_image ='';
                if($value->res_content_id!='' && $value->res_content_id!=null)
                {
                    $this->db->select('image as package_image');
                    $this->db->where('restaurant_id',$value->res_content_id);
                    $result_img = $this->db->get('restaurant_package')->first_row();
                    if($result_img && !empty($result_img))
                    {
                        $package_image =$result_img->package_image;
                    }
                }
                
                $event_cancel_reason = ($value->event_status == 'cancel')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';
                $package_detail = unserialize($value->package_detail);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $events[$value->event_id]['entity_id'] =  $value->event_id;
                $events[$value->event_id]['package_image'] =  $package_image;
                $events[$value->event_id]['booking_date'] =  $this->common_model->getZonebaseDate($value->booking_date);
                $events[$value->event_id]['event_status'] =  $value->event_status;
                $events[$value->event_id]['additional_request'] =  $value->additional_request;
                $events[$value->event_id]['event_cancel_reason'] =  $event_cancel_reason;
                $events[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $events[$value->event_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $restaurant_detail->currency_code;
                $events[$value->event_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $restaurant_detail->currency_symbol;
                $events[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $events[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $events[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';
                
                $events[$value->event_id]['restaurant_id'] =  $value->restaurant_id;
                $events[$value->event_id]['res_content_id'] =  $value->res_content_id;
                $events[$value->event_id]['name'] =  (!empty($restaurant_detail))?$restaurant_detail->name:'';
                $events[$value->event_id]['image'] =  (!empty($value->rest_image))?$value->rest_image:'';
                $events[$value->event_id]['address'] =  (!empty($restaurant_detail))?$restaurant_detail->address:'';
                $events[$value->event_id]['landmark'] =  (!empty($restaurant_detail))?$restaurant_detail->landmark:'';
                $events[$value->event_id]['city'] =  (!empty($restaurant_detail))?$restaurant_detail->city:'';
                $events[$value->event_id]['zipcode'] =  (!empty($restaurant_detail))?$restaurant_detail->zipcode:'';
                $events[$value->event_id]['rating'] =  $value->rating;
                $events[$value->event_id]['restaurant_slug'] =  $value->restaurant_slug;
                $events[$value->event_id]['created_date'] =  $value->created_date;
                $events[$value->event_id]['restaurant_status'] =  $value->restaurant_status;
            }
        }
        $finalArray = array();
        foreach ($events as $key => $val) {
           $finalArray[] = $val; 
        }
        return $finalArray;
    } 
    //get address
    public function getAddress($user_id,$address_id=NULL){
        $this->db->select('entity_id as address_id,address_label,user_entity_id,address,landmark,latitude,longitude,city,state,country,zipcode,is_main,search_area');
        $this->db->where('user_entity_id',$user_id);
        if ($address_id != '') {
            $this->db->where('entity_id',$address_id);
        }
        $this->db->order_by('is_main','desc');
        $this->db->order_by('entity_id','desc');
        return $this->db->get('user_address')->result();
    }
    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
    //get wallet history
    public function getWalletHistory($user_id){
        $result['total_money_credited'] = 0;
        $this->db->select('wallet_id,user_id,order_id,referee_id,amount,credit,debit,reason');
        $this->db->where('user_id',$user_id);
        $this->db->where('amount>',0);
        $this->db->where('is_deleted',0);
        $this->db->order_by('wallet_id','desc');
        $result['result'] = $this->db->get('wallet_history')->result();
        
        $this->db->where('user_id',$user_id);
        $this->db->where('is_deleted',0);
        $data = $this->db->get('wallet_history')->result();
        foreach($data as $key => $value){
            if($value->credit == 1) {
                $result['total_money_credited'] += $value->amount;  
            }
        }
        $this->db->where('user_id',$user_id);
        $this->db->where('amount>',0);
        $this->db->where('is_deleted',0);
        $result['count'] = $this->db->get('wallet_history')->num_rows();
        //get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $result['currency_symbol'] = $currency_symbol->currency_symbol;*/
        return $result;
    }
    // get total orders of a user
    public function getTotalOrders($user_id,$restaurant_id){
        $this->db->select('entity_id');
        $this->db->where('user_id',$user_id);
        $this->db->where('restaurant_id',$restaurant_id);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "complete")');
        return $this->db->get('order_master')->num_rows();
    }
    // get total reviews of a user
    public function getTotalReviews($user_id,$restaurant_content_id){
        $this->db->select('entity_id');
        $this->db->where('user_id',$user_id);
        $this->db->where('restaurant_content_id',$restaurant_content_id);
        $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
        $this->db->where('review.status',1);
        return $this->db->get('review')->num_rows();
    }
    // get cancel order reasons
    public function getCancelOrderReasons($order_id){
        $lang_slug = $this->session->userdata('language_slug');
        $this->db->select('entity_id,reason');
        $this->db->where('user_type','Customer');
        $this->db->where('language_slug',$lang_slug);
        $this->db->where('status',1);
        return $this->db->get('cancel_reject_reasons')->result_array();
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
    //update record with multiple where
    public function updateMultipleWhere($table,$whereArray,$data)
    {
        $this->db->where($whereArray);
        $this->db->update($table,$data);
    }
    // method for adding 
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    public function getUserOrderReview($user_id){
        $this->db->select('order_id');
        $this->db->where('user_id',$user_id);
        $this->db->where('order_id is NOT NULL');
        $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
        $this->db->where('status',1);
        return $this->db->get('review')->result_array();
    }
    //get notifications
    public function getNotifications($user_id){
        $this->db->select('entity_id,notification_title,notification_description,usersnoti.notification_id');
        $this->db->join('notifications_users as usersnoti','entity_id = usersnoti.notification_id');
        $this->db->where('usersnoti.user_id',$user_id);
        return $this->db->get('notifications')->result();
    }
    public function getNotificationsdtl($noti_id){
        $this->db->select('entity_id,notification_description,notification_title');        
        $this->db->where('entity_id',$noti_id);
        return $this->db->get('notifications')->first_row();
    }
    //get table bookings
    public function gettableBooking($user_id,$table_flag,$table_id=NULL){
        $currentDateTime = date('Y-m-d');
        $currentTime = date('H:i');
        $currentDateTime = $this->common_model->getZonebaseCurrentTime($currentDateTime);
        $status_array = array('cancelled');
        //upcoming
        $this->db->select('table.entity_id as table_id,table.booking_date,table.cancel_reason,table.no_of_people,table.start_time,table.end_time,table.additional_request,AVG (review.rating) as ratings,currencies.currency_symbol,currencies.currency_code,restaurant.entity_id as restaurant_id,restaurant.content_id as res_content_id,restaurant.image as rest_image,table.created_date,table.booking_status,restaurant.name as rname,restaurant.image,radd.address,radd.landmark,radd.city,radd.zipcode,restaurant.restaurant_slug, restaurant.status as restaurant_status');
        $this->db->join('restaurant','table.restaurant_content_id = restaurant.content_id','left');
        $this->db->join('restaurant_address as radd','restaurant.entity_id = radd.resto_entity_id'); 
        $this->db->join('review','restaurant.entity_id = review.restaurant_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.language_slug',$this->session->userdata('language_slug'));
        $this->db->where('table.user_id',$user_id);
        if ($table_flag == "upcoming") {
            $this->db->where('table.booking_date >=',date('Y-m-d', strtotime($currentDateTime)));
            $this->db->where_not_in('table.booking_status',$status_array);
            /*$time_chk = "(table.booking_date >= '".date('Y-m-d', strtotime($currentDateTime))."' OR (table.booking_date = '".$currentDateTime."' AND table.start_time > '".$currentTime."'))";
            $this->db->where($time_chk);
            $this->db->where('table.booking_status !=', 'cancelled');*/
        }
        if ($table_flag == "past") {
            $this->db->where('(table.booking_date <', date('Y-m-d', strtotime($currentDateTime)))->or_where("table.booking_status = 'cancelled')");
            /*$time_chk = "(table.booking_date < '".$currentDateTime."' OR (table.booking_date = '".$currentDateTime."' AND table.start_time <= '".$currentTime."'))";
            $this->db->where($time_chk)->or_where("table.booking_status = 'cancelled'");*/
        }
        if ($table_id != '') {
            $this->db->where('table.entity_id',$table_id);
        }
        $this->db->group_by('table.entity_id');
        $this->db->order_by('table.entity_id','desc');
        $result = $this->db->get('table_booking as table')->result();
        $tables = array();
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        //$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            $restaurant_detail = '';
            if(!isset($value->table_id)){
                $tables[$value->table_id] = array();
            }
            if(isset($value->table_id))
            {
                $table_cancel_reason = ($value->booking_status == 'cancelled')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';
                $restaurant_detail = unserialize($value->restaurant_detail);
                $tables[$value->table_id]['entity_id'] =  $value->table_id;
                $tables[$value->table_id]['booking_date'] =  $value->booking_date;
                $tables[$value->table_id]['start_time'] =  $this->common_model->getZonebaseTime($value->start_time);
                $tables[$value->table_id]['end_time'] =  $this->common_model->getZonebaseTime($value->end_time);
                $tables[$value->table_id]['booking_status'] =  $value->booking_status;
                $tables[$value->table_id]['additional_request'] =  $value->additional_request;
                $tables[$value->table_id]['table_cancel_reason'] =  $table_cancel_reason;
                $tables[$value->table_id]['no_of_people'] =  $value->no_of_people;
                $tables[$value->table_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $restaurant_detail->currency_code;
                $tables[$value->table_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $restaurant_detail->currency_symbol;
                
                $tables[$value->table_id]['restaurant_id'] =  $value->restaurant_id;
                $tables[$value->table_id]['res_content_id'] =  $value->res_content_id;
                $tables[$value->table_id]['rname'] =  $value->rname;
                $tables[$value->table_id]['image'] =  $value->image;
                $tables[$value->table_id]['address'] =  $value->address;
                $tables[$value->table_id]['landmark'] =  $value->landmark;
                $tables[$value->table_id]['city'] =  $value->city;
                $tables[$value->table_id]['zipcode'] =  $value->zipcode;
                $tables[$value->table_id]['ratings'] =  $value->ratings;
                $tables[$value->table_id]['restaurant_slug'] =  $value->restaurant_slug;
                $tables[$value->table_id]['created_date'] =  $value->created_date;
                $tables[$value->table_id]['restaurant_status'] =  $value->restaurant_status;
            }
        }
        $finalArray = array();
        foreach ($tables as $key => $val) {
           $finalArray[] = $val; 
        }
        return $finalArray;
    }
    //get ratings and reviews of a restaurant
    public function getReviewsPagination($restaurant_content_id,$count,$page_no=1){
        $page_no = ($page_no > 0)?$page_no-1:0;
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        $is_rating_from_res_form = '0';
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->select('review.*,users.first_name,users.last_name,users.image');
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->limit($count,$page_no*$count);
            $this->db->order_by('review.created_date', 'DESC');
            $result['reviews'] =  $this->db->get_where('review',array('review.status'=>1))->result_array();
            foreach($result['reviews'] as $key => $value){
                $result['reviews'][$key]['review']=utf8_decode($value['review']);
            }

            $this->db->select('review.*,users.first_name,users.last_name,users.image');
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $result['review_count'] =  $this->db->get_where('review',array('review.status'=>1))->num_rows();

            $page_no = $page_no + 1;
            $this->db->select('review.*,users.first_name,users.last_name,users.image');
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->limit($count,$page_no*$count);
            $result['next_page_count'] =  $this->db->get_where('review',array('review.status'=>1))->num_rows();
            $result['is_rating_from_res_form'] = $is_rating_from_res_form;
        } else {
            $is_rating_from_res_form = '1';
            $result['reviews'] = '';
            $result['review_count'] = $res_rating->restaurant_rating_count;
            $result['next_page_count'] = 0;
            $result['is_rating_from_res_form'] = $is_rating_from_res_form;
        }
        return $result;
    }
    //Code for find the current order stauts :: Start
    public function getOrderstatusLast($order_id)
    {
        $this->db->select('order_master.order_status,order_master.status,order_master.order_delivery');
        $this->db->where('order_master.entity_id',$order_id);                
        $result =  $this->db->get('order_master')->first_row();        
        $order_status = $result->order_status;       
        if(($result->order_status == "placed" && $result->status=='1') || $result->order_status == "accepted")
        {
            $order_status = "accepted";                         
        }
        return $order_status;
    }
    //Code for find the current order stauts :: End
    public function check_default_address($user_id,$address_id){
        $this->db->select('entity_id as address_id,is_main');
        $this->db->where('user_entity_id',$user_id);
        $this->db->where('entity_id',$address_id);
        $result = $this->db->get('user_address')->first_row();
        if($result->is_main == 1){
            $data = array('is_main' => 1);
            $this->db->where('user_entity_id',$user_id);
            $this->db->where('entity_id !=',$address_id);
            $this->db->order_by('entity_id','DESC');
            $this->db->limit(1, 0);
            $this->db->update('user_address',$data);
        }
    }
    //Code for list all save card :: Start
    public function getsavecard_detail($user_id) {
        $stripe_info = stripe_details();
        // Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;

        $stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$user_id))->first_row();
        $stripecus_id = ''; $card_details_arr= array();
        if($stripecus_arr && !empty($stripecus_arr)) {
            $stripecus_id = $stripecus_arr->stripe_customer_id;
        }
        if($stripecus_id) {
            $stripe = new \Stripe\StripeClient($stripe_api_key);
            $default_payment_method = NULL;
            try {
                //get default payment method
                $customer_obj = $stripe->customers->retrieve(
                    $stripecus_id,
                    []
                );
                $default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
            } catch (Exception $e) {
                //error while retrieving customer
            }
            try {
                //list all cards
                $all_card_details = $stripe->paymentMethods->all([
                    'customer' => $stripecus_id,
                    'type' => 'card',
                ]);
                if($all_card_details && !empty($all_card_details)) {
                    if(!$default_payment_method) {
                        //set recent card as default
                        $this->common_model->set_default_card($stripe, $all_card_details->data[0]->id, $stripecus_id);
                        //get default payment method
                        try {
                            $customer_obj = $stripe->customers->retrieve(
                                $stripecus_id,
                                []
                            );
                            $default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
                        } catch (Exception $e) {
                            //error while retrieving customer
                        }
                    }
                    //sort payment method list :: default payment method on top
                    if($default_payment_method) {
                        if(in_array($default_payment_method, array_column($all_card_details->data, 'id'))) {
                            usort($all_card_details->data,function($a,$b) use ($default_payment_method) {
                                if ($a->id != $default_payment_method && $b->id == $default_payment_method) {
                                    return 1;
                                } elseif ($a->id == $default_payment_method && $b->id != $default_payment_method) {
                                    return -1;
                                } else {
                                    return 0;
                                }
                            });
                        }
                    }
                    //add new key is_default_card in all payment method object
                    foreach ($all_card_details->data as $allmethods_key => $allmethods_value) {
                        if($allmethods_value->id == $default_payment_method) {
                            $all_card_details->data[$allmethods_key]->is_default_card = '1';
                        } else {
                            $all_card_details->data[$allmethods_key]->is_default_card = '0';
                        }
                    }
                    $all_card_detailaarr = $all_card_details->data;
                    for($strid = 0; $strid < count($all_card_detailaarr); $strid++) {
                        $PaymentMethodid = $all_card_detailaarr[$strid]->id;
                        $card_brand = $all_card_detailaarr[$strid]->card->brand;
                        $card_last4 = $all_card_detailaarr[$strid]->card->last4;
                        $card_number = "************".$card_last4;
                        $card_fingerprint = $all_card_detailaarr[$strid]->card->fingerprint;
                        $exp_month = $all_card_detailaarr[$strid]->card->exp_month;
                        $exp_year = $all_card_detailaarr[$strid]->card->exp_year;
                        $postal_code = $all_card_detailaarr[$strid]->billing_details->address->postal_code;

                        $card_brand_name = '';
                        if($card_brand == 'amex' || $card_brand == 'mastercard' || $card_brand == 'visa' || $card_brand == 'discover' || $card_brand == 'diners' || $card_brand == 'jcb' || $card_brand == 'unionpay') {
                            $card_brand_name = $this->lang->line($card_brand);
                        } else {
                            $card_brand_name = ucfirst($card_brand);
                        }

                        $card_image = '';
                        if($card_brand =='unionpay') {
                            $card_image = 'assets/front/images/cardimg/unionpay.jpg';
                        } else if($card_brand =='amex') {
                            $card_image = 'assets/front/images/cardimg/american_express.jpg';
                        } else if($card_brand =='jcb') {
                            $card_image = 'assets/front/images/cardimg/jcb.jpg';
                        } else if($card_brand =='diners') {
                            $card_image = 'assets/front/images/cardimg/diners_club.jpg';
                        } else if($card_brand =='discover') {
                            $card_image = 'assets/front/images/cardimg/discover.jpg';
                        } else if($card_brand =='mastercard') {
                            $card_image = 'assets/front/images/cardimg/mastercard.jpg';
                        } else if($card_brand =='visa') {
                            $card_image = 'assets/front/images/cardimg/visa.jpg';
                        }

                        $card_details_arr[$strid]['PaymentMethodid'] = $PaymentMethodid;
                        $card_details_arr[$strid]['card_brand'] = strtolower($card_brand);
                        $card_details_arr[$strid]['card_brand_name'] = $card_brand_name;
                        $card_details_arr[$strid]['card_last4'] = $card_last4;
                        $card_details_arr[$strid]['card_number'] = $card_number;
                        $card_details_arr[$strid]['card_fingerprint'] = $card_fingerprint;
                        $card_details_arr[$strid]['stripecus_id'] = $stripecus_id;
                        $card_details_arr[$strid]['exp_month'] = $exp_month;
                        $card_details_arr[$strid]['exp_year'] = $exp_year;
                        $card_details_arr[$strid]['postal_code'] = $postal_code;
                        $card_details_arr[$strid]['card_image'] = $card_image;
                        $card_details_arr[$strid]['is_default_card'] = $all_card_detailaarr[$strid]->is_default_card;
                    }
                }
            }catch (Exception $e){
                //http_response_code(500);
                $card_details_arr=['error'=>'error','message'=>$e->getMessage()];
            }
        }
        return $card_details_arr;
    }
    //Code for list all save card :: End
    //get tip transaction id :: start
    public function getTransactionId($order_id='')
    {
        $this->db->select('tips.tips_transaction_id,order.refund_status');
        $this->db->where('order.entity_id',$order_id);
        $this->db->join('tips','order.entity_id = tips.order_id AND tips.amount > 0','left');
        $result =  $this->db->get('order_master as order')->first_row();
        $tips_transaction_id = $result->tips_transaction_id; 
        $refund_status = $result->refund_status;
        if($tips_transaction_id!='' || $refund_status=='refunded')
        {
            return 1;                         
        }else{
            return 0;
        }
    }
    //Code for find the all active payment method :: Start
    public function getPaymentMethod($order_id,$language_slug='en')
    {
        //Code for find the rest contant id :: Start
        $this->db->select('content_id');
        $this->db->join('order_master','order_master.restaurant_id = restaurant.entity_id');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('order_master.entity_id',$order_id);
        $result =  $this->db->get('restaurant')->first_row();

        if($result && !empty($result))
        {
            $this->db->select('payment_id');
            $this->db->where('restaurant_content_id',$result->content_id);
            $payment_content_ids = $this->db->get('restaurant_payment_method_suggestion')->result_array();
        }
        
        if(!empty($payment_content_ids))
        {
            $this->db->select('payment_id, payment_gateway_slug, display_name_en as payment_name');
            $this->db->where_in('payment.payment_id',array_column($payment_content_ids,'payment_id'));            
            $this->db->where('payment.status',1);
            $this->db->where('payment.payment_gateway_slug!=','cod');
            $this->db->where('payment.payment_gateway_slug!=','applepay');
            $result_res = $this->db->get('payment_method as payment')->result();
        } else {
            $result_res = array();
        }
        return $result_res;
    }
    //Code for find the all active payment method :: End
    public function getBookmarks($user_id){
        $lang_slug = $this->session->userdata('language_slug');
        $this->db->select('res.entity_id,res.name,res.image,res.restaurant_slug,res.content_id,res.timings,res.enable_hours,address.address');
        $this->db->join('bookmark_restaurant as b_mark','b_mark.restaurant_id=res.entity_id');
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        $this->db->where('res.language_slug',$lang_slug);
        $this->db->where('b_mark.user_id',$user_id);
        $this->db->order_by('b_mark.entity_id','desc');
        $result = $this->db->get_where('restaurant as res',array('res.status'=>1))->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day))
                        {
                            $close = 'Closed';
                            if($value['enable_hours']=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'Closed';
                                if (!empty($values['open']) && !empty($values['close'])) {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']));
                                }
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }
                            else
                            {
                                $newTimingArr[strtolower($day)]['open'] = '';
                                $newTimingArr[strtolower($day)]['close'] = '';
                                $newTimingArr[strtolower($day)]['off'] = 'close';
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }                            
                        }
                    }
                }
                else
                {
                    $newTimingArr[strtolower($day)]['closing'] = 'close';
                    $newTimingArr[strtolower($day)]['open'] = '';
                    $newTimingArr[strtolower($day)]['close'] ='';
                    $newTimingArr[strtolower($day)]['off'] = 'close';
                }
                $result[$key]['timings'] = $newTimingArr[strtolower($day)];
            }
        }
        return $result;
    }
    //remove bookmark restaurant
    public function removeBookmark($user_id,$restaurant_id){
        $this->db->where('user_id',$user_id);
        $this->db->where('restaurant_id',$restaurant_id);
        $this->db->delete('bookmark_restaurant');
    }
}