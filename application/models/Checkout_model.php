<?php
class Checkout_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get users address
    public function getUsersAddress($UserID){
        return $this->db->get_where('user_address',array('user_entity_id'=>$UserID))->result_array();
    }
    // get address latlong
    public function getAddressLatLng($entity_id){
        $this->db->select('latitude,longitude');
        return $this->db->get_where('user_address',array('entity_id'=>$entity_id))->first_row();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($restaurant_id)
    {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);        
        $resultcont = $this->db->get('restaurant')->first_row();

        $this->db->where('restaurant_id',$resultcont->content_id);
        return $this->db->get('delivery_charge')->result();
    }
    //get coupon list
    public function getCouponsList($subtotal,$restaurant_id,$order_delivery,$User_ID=0,$coupon_searchval='', $is_guest_checkout,$user_type)
    {
        //Code to check the user is new or not :: Start
        $user_chkcpn = 'yes';
        if($User_ID>0)
        {            
            $this->db->select('entity_id');
            $this->db->where('user_id',$User_ID);
            $user_chk = $this->db->count_all_results('order_master');
            if($user_chk>0)
            {
                $user_chkcpn = 'no';
            }            
        }
        if($is_guest_checkout == 1 || $user_type == 'Agent'){
            $user_chkcpn = 'no';
        }
        //Code to check the user is new or not :: End
        if($restaurant_id!='')
        {
            $restaurant_id = $this->getResContentId($restaurant_id);
        }
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code, coupon.maximaum_use_per_users, coupon.maximaum_use, coupon.use_with_other_coupons,coupon.coupon_for_newuser');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        if($coupon_searchval!='')
        {
            $where_titleserch .= " coupon.name like '%".$this->common_model->escapeString($coupon_searchval)."%' ";
            $this->db->where($where_titleserch);            
        }
        $this->db->where('max_amount <=',$subtotal);
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        if($user_chkcpn=='no') {
             $this->db->where('coupon_type !=' , 'user_registration');
        }
        $this->db->where_not_in('coupon_type ', array('discount_on_items','discount_on_categories'));
        //$this->db->where('(coupon_type != "discount_on_items")');
        //$this->db->where('(coupon_type != "discount_on_categories")');
        //$this->db->where('(coupon_type != "dine_in")');
        if($order_delivery == 'delivery') {            
            if($user_chkcpn=='yes') {
                 $this->db->where_in('coupon.coupon_type',array("free_delivery","discount_on_cart","user_registration"));
            }
            else
            {
                $this->db->where_in('coupon.coupon_type',array("free_delivery","discount_on_cart"));
            }
            //$this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);            
        } else {
            $this->db->where('coupon.coupon_type != "free_delivery"');
            if($user_chkcpn=='yes') {
                 $this->db->where_in('coupon.coupon_type',array("discount_on_cart","user_registration"));
            }
            else
            {
                $this->db->where_in('coupon.coupon_type',array("discount_on_cart"));
            }
        }

        $this->db->where('coupon.status',1);
        $this->db->group_by('coupon.entity_id');
        return $this->db->get('coupon')->result_array();
    }

    public function getResContentId($restaurant_id){
        $this->db->select('content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        return $result->content_id;
    }

    // get coupon details
    public function getCouponDetails($entity_id){
        return $this->db->get_where('coupon',array('entity_id'=>$entity_id))->first_row();
    }
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id',$UserID);
        return $this->db->get('order_master')->num_rows();
    }
    //get tax
    public function getRestaurantTax($restaurant_id, $scheduled_order_date = NULL,$slot_open_time=NULL,$slot_close_time=NULL) {
        $this->db->select('restaurant.name,restaurant.image,restaurant.timings,restaurant.phone_number,restaurant.phone_code,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant.is_service_fee_enable,restaurant.service_fee_type,restaurant.service_fee,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code,restaurant.enable_hours,restaurant.status as restaurant_status,restaurant.is_creditcard_fee_enable,restaurant.creditcard_fee_type,restaurant.creditcard_fee,restaurant.allow_scheduled_delivery');
        $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();        
        if(!empty($result) && $result)
        {
            if($scheduled_order_date) {
                //get time interval from system options
                $this->db->select('OptionValue');
                $this->db->where('OptionSlug','time_interval_for_scheduling');
                $time_interval_for_scheduling = $this->db->get('system_option')->first_row();
                $time_interval_for_scheduling = (int)$time_interval_for_scheduling->OptionValue;
                $half_interval = ceil($time_interval_for_scheduling / 2);
            }
            $timing = $result->timings;
            if($timing) {
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                if($scheduled_order_date) {
                    $day = date('l',strtotime($scheduled_order_date));
                    $datetoday = date( "Y-m-d", strtotime($scheduled_order_date) );
                } else {
                    $day = date("l");
                    $datetoday = date( "Y-m-d");
                }
                foreach($timing as $keys=>$values) {
                    if($keys == strtolower($day)) {
                        $close = 'Closed';
                        if($result->enable_hours=='1') {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'Closed';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                if($scheduled_order_date) {
                                    $slottime = date_create($slot_open_time);
                                    date_add($slottime,date_interval_create_from_date_string($half_interval." minutes"));
                                    $slottime = date_format($slottime,"H:i");
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']),'',$slottime);
                                } else {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']));
                                }
                            }
                            $newTimingArr[strtolower($day)]['closing'] = $close;
                        } else {
                            $newTimingArr[strtolower($day)]['open'] = '';
                            $newTimingArr[strtolower($day)]['close'] = '';
                            $newTimingArr[strtolower($day)]['off'] = 'close';
                            $newTimingArr[strtolower($day)]['closing'] = $close;
                        }
                    }
                }
                $result->timings = $newTimingArr[strtolower($day)];
            } else {
                $newTimingArr[strtolower($day)]['closing'] = 'close';
                $newTimingArr[strtolower($day)]['open'] = '';
                $newTimingArr[strtolower($day)]['close'] ='';
                $newTimingArr[strtolower($day)]['off'] = 'close';
            }
            
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)) {
                $result->currency_symbol = $default_currency->currency_symbol;
                $result->currency_code = $default_currency->currency_code;
            }
        }  
        return $result;
    }
    //get address
    public function getAddress($entity_id){
        $this->db->select('entity_id as address_id,address_label,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('user_address')->first_row();
    }
    //get menu content id
    public function getMenuContentID($menu_id){
        $this->db->select('content_id,is_combo_item,menu_detail');
        $this->db->where('entity_id',$menu_id);
        return $this->db->get('restaurant_menu_item')->first_row();
    }
    // function to get users total earning points
    public function getUsersEarningPoints($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    // Update User
    public function updateUser($tableName,$data,$fieldName,$UserID)
    {
        $this->db->where($fieldName,$UserID);
        $this->db->update($tableName,$data);
    }
    // Common Add Records
    public function addRecord($table,$data)
    {
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }
    //Code added to find the branch admin user device id :: Start :: 12-10-2020
    public function getBranchAdminDevice($restaurant_id)
    {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res))
        {
            $this->db->select('users.device_id, users.language_slug, users.notification, users.status');
            $this->db->join('users','users.entity_id = restaurant.branch_admin_id','left');
            $this->db->where('restaurant.entity_id',$result_res->entity_id);
            $this->db->where('restaurant.branch_admin_id!=',NULL);            
            $result = $this->db->get('restaurant')->first_row(); 
            return $result;
        }
        return false;
    }
    //Code added to find the branch admin user device id :: End :: 12-10-2020

    //get address
    public function getUsersDetail($user_id)
    {
        $this->db->select('users.first_name, users.mobile_number, users.email, address.address, address.address_label, address.city, address.state, address.country, address.zipcode');
        $this->db->join('user_address as address','users.entity_id = address.user_entity_id','left');
        $this->db->where('users.entity_id',$user_id);
        $this->db->order_by('address.is_main','desc');                
        return $this->db->get('users')->first_row();
    }

    public function getOrderRecords($order_id){
        $this->db->select('order.total_rate,order_detail.restaurant_detail,order_detail.user_detail');
        $this->db->join('order_detail','order.entity_id = order_detail.order_id','left');
        $this->db->where('order.entity_id', $order_id);
        return $this->db->get('order_master as order')->first_row();
    }
    public function getRestaurantAdminDevice($restaurant_id)
    {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res))
        {
            $this->db->select('users.device_id, users.language_slug, users.notification, users.status');
            $this->db->join('users','users.entity_id = restaurant.restaurant_owner_id','left');
            $this->db->where('restaurant.entity_id',$result_res->entity_id);            
            $result = $this->db->get('restaurant')->first_row(); 
            return $result;
        }
        return false;
    }
    //get items
    public function getMenuSuggestionItems($restaurant_id,$language_slug,$cart_items_array)
    {
        $res_content_id = $this->getContentId($restaurant_id,'restaurant');
        $this->db->select('menu_content_id');
        $this->db->where('restaurant_content_id',$res_content_id->content_id);
        if(!empty($cart_items_array)){
            $this->db->where_not_in('menu_content_id', $cart_items_array);
        }
        $menu_content_ids = $this->db->get('restaurant_menu_suggestion')->result_array();
        
        if(!empty($menu_content_ids)){
            $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
            $this->db->select('menu.is_deal,menu.entity_id as menu_id, menu.content_id as menu_content_id, menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.food_type,availability,c.name as category,c.entity_id as category_id,c.content_id as cat_content_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id, add_ons_master.is_multiple,add_ons_master.display_limit,add_ons_master.mandatory,restaurant.timings,restaurant.enable_hours');
            $this->db->join('restaurant','menu.restaurant_id = restaurant.entity_id','left');
            $this->db->join('category as c','menu.category_id = c.entity_id');
            $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
            $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
            $this->db->where('c.status',1);
            $this->db->where_in('menu.content_id',array_column($menu_content_ids,'menu_content_id'));
            $this->db->where('menu.language_slug',$language_slug);
            $this->db->where('menu.status',1);
            $this->db->where('menu.stock',1);
            $this->db->order_by('menu.price','asc');
            $result = $this->db->get('restaurant_menu_item as menu')->result();
           
            $menu = array();
            foreach ($result as $key => $value)
            {
                $timing = $value->timings;
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
                    $value->timings = $newTimingArr[strtolower($day)];
                }
                //Code for food type section Start                
                $food_type_id = ''; $food_type_name = '';
                if($value->food_type!='')
                {
                    $is_vegarr = explode(",", $value->food_type);
                    $this->db->select('entity_id as food_type_id, name as food_type_name');
                    $this->db->where_in('entity_id',$is_vegarr);
                    $resfood_type = $this->db->get('food_type')->result();
                    if($resfood_type && count($resfood_type)>0)
                    {
                        $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                        $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                    }
                }                
                //Code for food type section End
                $category_discount = '';
                if(!empty($res_content_id) && $res_content_id->content_id){
                    $category_discount = $this->common_model->getCategoryDiscount($res_content_id->content_id);
                }
                $item_not_appicable_for_item_discount = array();
                //offer price start
                $offer_price = '';
                //Begin::Category Discount Coupon Check
                if(!empty($category_discount)){
                    foreach ($category_discount as $key => $cat_value) {
                        if(!empty($cat_value['combined'])){
                            if(isset($cat_value['combined'][$value->cat_content_id])){
                                if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                    array_push($item_not_appicable_for_item_discount, $value->menu_content_id);
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Percentage'){
                                        $offer_price = $value->price - (($value->price * $cat_value['combined'][$value->cat_content_id]['discount_value'])/100);
                                    }
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Amount'){
                                        $offer_price = $value->price - $cat_value['combined'][$value->cat_content_id]['discount_value'];
                                    }
                                }
                            }
                        }
                    }
                }
                //End::Category Discount Coupon Check
                if(!empty($ItemDiscount)) {
                    foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                        if(!empty($cpnvalue['itemDetail'])) {
                            if(in_array($value->menu_content_id,$cpnvalue['itemDetail']) && !in_array($value->menu_content_id, $item_not_appicable_for_item_discount)){
                                if($cpnvalue['max_amount'] <= $value->price){ 
                                    if($cpnvalue['amount_type'] == 'Percentage'){
                                        $offer_price = $value->price - round(($value->price * $cpnvalue['amount'])/100,2);
                                    }else if($cpnvalue['amount_type'] == 'Amount'){
                                        $offer_price = $value->price - $cpnvalue['amount'];
                                    }
                                }
                            }
                        }
                    }
                }
                $offer_price = ($offer_price)?number_format($offer_price, 2):'';
                //offer price end
                if (!isset($menu[$value->category_id])) 
                {
                    $menu[$value->category_id] = array();
                    $menu[$value->category_id]['category_id'] = $value->category_id;
                    $menu[$value->category_id]['category_name'] = $value->category;  
                }
                $image = ($value->image)?$value->image:'';
                //$image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('restaurant_id'=>$restaurant_id,'menu_id'=>$value->menu_id, 'menu_content_id'=>$value->menu_content_id, 'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                    }
                    if($value->is_deal == 1){
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'] = array();
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['is_multiple'] = $value->is_multiple;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['max_selection_limit'] = $value->display_limit;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['is_mandatory'] = $value->mandatory;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name);
                        $i++;
                    }else{
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['max_selection_limit'] = $value->display_limit;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_mandatory'] = $value->mandatory;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
                        $i++;
                    }
                }else{
                    $menu[$value->category_id]['items'][]  = array('restaurant_id'=>$restaurant_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price, 'menu_content_id'=>$value->menu_content_id, 'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                }
            }
        } else {
            $menu = array();
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $new = array();
        foreach ($menu as $nm => $va) 
        {
            $final = array();
            foreach ($va['items'] as $kk => $items) 
            {
                if(!empty($items['addons_category_list']))
                {
                    $semifinal = array();
                    foreach ($items['addons_category_list'] as $addons_cat_list) 
                    {
                        array_push($semifinal, $addons_cat_list);
                    }
                    $items['addons_category_list'] = $semifinal;                  
                }
                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }

        $menu_suggestion = array();
        foreach ($finalArray as $menukey => $menuvalue) {
            foreach ($menuvalue['items'] as $key => $value) {
                $menu_suggestion[] = $value;
            }
        }
        return $menu_suggestion;     
    }
    //get item discount
    public function getItemDiscount($where){
        $this->db->select('entity_id,max_amount,amount_type,amount');
        $this->db->where($where);
        $this->db->where('end_date >',date('Y-m-d H:i:s'));
        $result = $this->db->get('coupon')->result_array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $this->db->select('item_id');
                $this->db->where('coupon_id',$value['entity_id']);
                $this->db->group_by('item_id');
                $item_ids = $this->db->get('coupon_item_map')->result_array();
                $item_ids = (!empty($item_ids))?array_column($item_ids, 'item_id'):array();
                $result[$key]['itemDetail'] = $item_ids;
            }
        }
        return $result;
    }
    // get content id
    public function getContentId($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    //get restaurant payment methods
    public function getPaymentMethodSuggestion($restaurant_id,$language_slug,$user_type='User')
    {
        $res_content_id = $this->getContentId($restaurant_id,'restaurant');
        $this->db->select('payment_id');
        $this->db->where('restaurant_content_id',$res_content_id->content_id);
        $payment_content_ids = $this->db->get('restaurant_payment_method_suggestion')->result_array();
        if(!empty($payment_content_ids)){
            $this->db->where_in('payment.payment_id',array_column($payment_content_ids,'payment_id'));
            if($user_type == 'Agent') {
                $this->db->or_where('payment.payment_gateway_slug','cod');
            }
            $this->db->where('payment.status',1);
            $result = $this->db->get('payment_method as payment')->result();
        } else {
            $result = array();
        }
        return $result;     
    }
    // method to get details by id :: for invoice
    public function getEditDetail($entity_id)
    {
        $this->db->select('order.*,res.name,res.phone_code as r_phone_code,res.phone_number as r_phone_number,res.is_printer_available,res.printer_paper_width,res.printer_paper_height,address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode,tb.table_number,tips.amount as tip_amount');
        $this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        $this->db->join('users as u','order.user_id = u.entity_id','left');
        $this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
        $this->db->join('table_master as tb','order.table_id = tb.entity_id','left');
        $this->db->join('tips','order.entity_id = tips.order_id AND tips.amount > 0','left');
        return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
    }
    //get invoice data :: for invoice
    public function getInvoiceMenuItem($entity_id){
        $this->db->where('order_id',$entity_id);
        return $this->db->get('order_detail')->first_row();
    }
    public function checkCouponAmount($entity_id,$subtotal){
        $this->db->where('max_amount<=',$subtotal);
        $data = $this->db->get_where('coupon',array('entity_id'=>$entity_id))->num_rows();
        if($data>0){
            return true;
        }else{
            return false;
        }
    }
    //insert batch
    public function inserBatch($tblname,$data){
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
}
?>