<?php
class Order_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // verify forgot password
    public function forgotpassowrdVerify($verificationCode){
        return $this->db->get_where('users',array('ActiveCode'=>$verificationCode))->first_row();
    }
    //Update password
    public function updatePassword($updatePassword,$verificationCode)
    {
        $this->db->where('ActiveCode',$verificationCode);
        $this->db->update('users',$updatePassword);
        
        $this->db->select('users.Password,users.Email');
        $this->db->where('ActiveCode',$verificationCode);
        return $this->db->get('users')->first_row();
    }
    // get latest order of logged in user
    public function getLatestOrder($user_id,$order_id=NULL){
        $this->db->select('order_master.entity_id as master_order_id,order_master.*,order_detail.*,order_driver_map.driver_id,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,users.driver_temperature,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id,order_master.scheduled_date,order_master.slot_open_time,order_master.slot_close_time');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        //$this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id AND driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY created_date DESC LIMIT 1)','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('(order_master.order_status != "complete" AND order_master.order_status != "delivered" AND order_master.order_status != "cancel" AND order_master.order_status != "rejected")');
        if (!empty($user_id)) {
            $this->db->where('order_master.user_id',$user_id);
        }
        if (!empty($order_id)) {
            $this->db->where('order_master.entity_id',$order_id);
        }
        $this->db->order_by('order_master.entity_id','desc');
        $result = $this->db->get('order_master')->first_row();
        if (!empty($result)) {
            $result->placed = $this->common_model->getZonebaseDate($result->created_date);
            //$result->preparing = '';
            $result->onGoing = '';
            $result->delivered = '';
            $result->order_ready = '';
            $result->completed = '';
            //get System Option Data
            /*$this->db->select('OptionValue');
            $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
            $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
           /* $result->currency_symbol = $currency_symbol->currency_symbol;
            $result->currency_code = $currency_symbol->currency_code;
            $result->currency_id = $currency_symbol->currency_id;*/
            // get order status
            $this->db->where('order_status.order_id',$result->master_order_id);
            $Ostatus = $this->db->get('order_status')->result_array();
            if (!empty($Ostatus)) {
                foreach ($Ostatus as $key => $ovalue) {
                    if ($ovalue['order_status'] == 'accepted_by_restaurant') {
                        $result->accepted_by_restaurant = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    if ($ovalue['order_status'] == 'ready') {
                        $result->order_ready = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    if ($ovalue['order_status'] == 'complete') {
                        $result->completed = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    // if ($ovalue['order_status'] == 'preparing') {
                    //     $result->preparing = $this->common_model->getZonebaseTime($ovalue['time']);
                    // }
                    if ($ovalue['order_status'] == 'onGoing') {
                        $result->onGoing = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    if ($ovalue['order_status'] == 'delivered') {
                        $result->delivered = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                }
            }
            $user_detail = unserialize($result->user_detail);
            if (!empty($user_detail)) {
                $result->user_first_name = $user_detail['first_name'];
                $result->user_address = $user_detail['address'];
                $result->user_email = $user_detail['email'];
                $result->user_latitude = $user_detail['latitude'];
                $result->user_longitude = $user_detail['longitude'];
                $result->image = ($result->image)?$result->image:'';
            }
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)){
                $result->currency_symbol = $default_currency->currency_symbol;
                $result->currency_code = $default_currency->currency_code;
            }
            $item_detail = unserialize($result->item_detail);
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
                    $valueee['comment'] = $valuee['comment'];
                    $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                    $valueee['is_customize'] = $valuee['is_customize'];
                    $valueee['is_deal'] = $valuee['is_deal'];
                    $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                    $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                    $value1[] =  $valueee; 
                } 
            }
            $result->items = $value1;
            $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$result->scheduled_date $result->slot_open_time"));
            $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$result->scheduled_date $result->slot_close_time"));
            $result->scheduled_date = ($result->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
            $result->slot_open_time = ($result->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
            $result->slot_close_time = ($result->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
        }
        return $result;
    }
}