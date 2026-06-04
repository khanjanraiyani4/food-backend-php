<?php
class Restaurant_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get restaurant details
    public function getRestaurantDetail($content_id,$searchArray=NULL,$food=NULL,$price=NULL,$availability=NULL){ 
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select("restaurant.entity_id as restaurant_id, restaurant.content_id as restaurant_content_id, restaurant.name,address.address, address.landmark,address.latitude, address.longitude, restaurant.image, restaurant.timings, CONCAT('+',COALESCE(restaurant.phone_code,''), COALESCE(restaurant.phone_number,'')) AS 'phone_number', restaurant.restaurant_slug,restaurant.content_id, currencies.currency_symbol, currencies.currency_code, restaurant.food_type, restaurant.enable_hours, restaurant.restaurant_owner_id,restaurant.table_online_availability,restaurant.allowed_days_table,restaurant.table_minimum_capacity,restaurant.table_booking_capacity,restaurant.event_online_availability,restaurant.event_minimum_capacity,restaurant.capacity,restaurant.allow_event_booking,restaurant.enable_table_booking,restaurant.order_mode, restaurant.background_image,restaurant.about_restaurant,restaurant.type_of_res,restaurant.allow_scheduled_delivery");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('restaurant.content_id',$content_id);
        $this->db->group_by('restaurant.entity_id');
        $result['restaurant'] = $this->db->get_where('restaurant',array('status'=>1))->result_array();
        $result['timeslots'] = array();

        $arr=array();
        if (!empty($result['restaurant'])) {
            //get System Option Data
           /* $this->db->select('OptionValue');
            $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
            $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
            $default_currency = get_default_system_currency();
            foreach ($result['restaurant'] as $key => $value) {
                if(!empty($default_currency)){
                    $result['restaurant'][$key]['currency_symbol'] = $default_currency->currency_symbol;
                    $result['restaurant'][$key]['currency_code'] = $default_currency->currency_code;
                }
                $result['restaurant'][$key]['about_restaurant'] = $value['about_restaurant'];
                // event/table online availability changes
                if(!empty($result['restaurant'][$key]['event_online_availability'])){
                    $result['restaurant'][$key]['capacity'] = floor(($value['capacity']*$value['event_online_availability'])/100) ;
                }
                if(!empty($result['restaurant'][$key]['table_online_availability'])){
                    $result['restaurant'][$key]['table_booking_capacity'] = floor(($value['table_booking_capacity']*$value['table_online_availability'])/100) ;
                }
                if(!empty($value['order_mode'])){
                    $result['restaurant'][$key]['order_mode'] = explode(',', $value['order_mode']);
                } else {
                    $result['restaurant'][$key]['order_mode'] = array();
                }
                //days allowed for event booking code :: start
                if(!empty($value['allowed_days_table'])){
                    $timing = $value['timings'];
                    $timing =  unserialize(html_entity_decode($timing)); 
                    for ($i=1; $i >0 ; $i++) { 
                        $date = date("Y-m-d",strtotime("+".$i." day", strtotime("now")));
                        $day = date("l",strtotime(' +'.$i.' day'));
                        foreach($timing as $keys=>$values) {
                            if((strtolower($day)==$keys) && $value['enable_hours']=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            }
                        }
                        if($newTimingArr[strtolower($day)]['off'] != 'close'){
                            $newdate = date_format(date_create($date),"F d,Y");
                            $arr[$i] = $newdate;
                        }
                        if(sizeof($arr) == $value['allowed_days_table']){
                            break;
                        }
                    }
                }
                //days allowed for event booking code :: end
                $timing = $value['timings'];
                if($timing){
                    $timing =  unserialize(html_entity_decode($timing));
                    $result['restaurant'][$key]['week_timings'] = ($value['enable_hours']=='1')?$timing:array();
                    $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day))
                        {
                            $close = 'Closed';
                            if($value['enable_hours']=='1')
                            {
                                $newTimingArr[strtolower($day)]['current_day'] = strtolower($day);
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'Closed';
                                if (!empty($values['open']) && !empty($values['close']))
                                {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']));
                                }
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }
                            else
                            {
                                $newTimingArr[strtolower($day)]['current_day'] = strtolower($day);
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
                $result['restaurant'][$key]['timings'] = $newTimingArr[strtolower($day)];
                foreach ($result['restaurant'][$key]['week_timings'] as $key1 => $week_value) {
                   $result['restaurant'][$key]['week_timings'][$key1]['open']=(!empty($week_value['open']))?$this->common_model->getZonebaseTime($week_value['open']):'';
                   $result['restaurant'][$key]['week_timings'][$key1]['close']=(!empty($week_value['close']))?$this->common_model->getZonebaseTime($week_value['close']):'';
                }
                $result['restaurant'][$key]['image'] = ($value['image'])?$value['image']:'';
                //Code for food type section Start                
                $food_type_id = ''; $food_type_name = ''; $resfood_type = array();
                if($value['food_type']!='')
                {
                    $is_vegarr = explode(",", $value['food_type']);
                    $this->db->select('entity_id as food_type_id, name as food_type_name');
                    $this->db->where_in('entity_id',$is_vegarr);
                    $this->db->where('food_type.status',1);
                    $this->db->where('food_type.language_slug',$language_slug);
                    $this->db->order_by('food_type.name', 'ASC');
                    $this->db->group_by('food_type.entity_id');
                    $resfood_type = $this->db->get('food_type')->result();
                    if($resfood_type && count($resfood_type)>0)
                    {
                        $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                        $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                    }
                }  
                $result['restaurant'][$key]['food_type_id'] = $food_type_id;
                $result['restaurant'][$key]['food_type_name'] = $food_type_name;
                $result['restaurant'][$key]['resfood_type'] = $resfood_type;
                //Code for food type section End
                $result['restaurant'][$key]['allow_scheduled_delivery'] = $value['allow_scheduled_delivery'];
            }
        } 
        //table booking time slots array
        $start_datetime = new DateTime(date('G:i',strtotime($result['restaurant'][0]['timings']['open'])));
        $end_datetime = new DateTime(date('G:i',strtotime($result['restaurant'][0]['timings']['close'])));
        $result['timeslots'] = $this->common_model->getTimeSlots(TIME_INTERVAL, $start_datetime->format('H:i'), $end_datetime->format('H:i'));
        $result['menu_items'] = array();
        $result['packages'] = array();
        $result['categories'] = array();
        if (!empty($result['restaurant']))
        {
            $restaurant_id = $result['restaurant'][0]['restaurant_id'];
            $restaurant_content_id = $result['restaurant'][0]['restaurant_content_id'];
            //Code for sort menu item :: Start
            $restaurant_owner_id = $result['restaurant'][0]['restaurant_owner_id'];            
            if($restaurant_owner_id==null || $restaurant_owner_id=='')
            {
                $this->db->select('entity_id');
                $this->db->where('user_type','MasterAdmin');
                $res_owner_idarr = $this->db->get('users')->first_row();
                $restaurant_owner_id = $res_owner_idarr->entity_id;                
            }
            
            $selectvalM='';
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $selectvalM = ",(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no";
            }
            //End

            $this->db->select('restaurant_menu_item.*,food_type.entity_id as food_type_id,food_type.is_veg,category.content_id as cat_content_id '.$selectvalM.'');
            $this->db->where('restaurant_menu_item.restaurant_id',$restaurant_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_menu_item.name LIKE '%" . trim($this->common_model->escapeString($value)) . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    //$like_statementsTwo[] = "restaurant_menu_item.menu_detail LIKE '%" . $this->common_model->escapeString($value) . "%'";
                    //$like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    //$like_statementsThree[] = "restaurant_menu_item.availability LIKE '%" . $this->common_model->escapeString($value) . "%'";
                    //$like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.')');
                // $this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->order_by('sequence_no', 'ASC');
            }
            else
            {
                if($price == "low")
                {
                    $this->db->order_by('restaurant_menu_item.price','asc');
                }
                else
                {
                    $this->db->order_by('restaurant_menu_item.price','desc');
                }
            }
            
            //New code for search food type Start
            if(trim($food) != '')
            {
                $foodarr = explode(",",$food);
                $foodarr = array_filter($foodarr);
                if(!empty($foodarr))
                {   
                    $fdtcnt=0; $wherefindcn = '(';
                    foreach($foodarr as $keyf=>$valuef) 
                    { 
                        if($fdtcnt>0){
                            $wherefindcn .= " OR ";
                        }
                        $wherefindcn .= "(find_in_set ($valuef, restaurant_menu_item.food_type))";

                        $fdtcnt++;
                    }
                    $wherefindcn .= ')';
                    //$where = "(res.name like '%".$searchItem."%')";
                    if($fdtcnt>0)
                    { $this->db->where($wherefindcn);  }
                }
            }
            //New code for search food type End

            //New code add for availability :: Start
            if($availability!='')
            {
                $availabilityarr = explode(",",$availability);
                $availabilityarr = array_filter($availabilityarr);
                if(!empty($availabilityarr))
                {   
                    $fdtcnt=0; $wherefindcn = '(';
                    foreach($availabilityarr as $keyf=>$valuef) 
                    { 
                        if($fdtcnt>0){
                            $wherefindcn .= " OR ";
                        }
                        $wherefindcn .= "(find_in_set ('".$valuef."', restaurant_menu_item.availability))";

                        $fdtcnt++;
                    }
                    $wherefindcn .= ')';
                    //$where = "(res.name like '%".$searchItem."%')";
                    if($fdtcnt>0)
                    { $this->db->where($wherefindcn);  }
                }
                /*$wherefindavblt = "(find_in_set ('".$availability."', restaurant_menu_item.availability))";
                $this->db->where($wherefindavblt);*/
            }
            //New code add for availability :: End
            $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
            //Code for sort menu item :: Start
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->join('menu_item_sequencemap as menumap',"menumap.menu_content_id = restaurant_menu_item.content_id AND menumap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');                
            }            
            //End
            $this->db->join('food_type','restaurant_menu_item.food_type = food_type.entity_id','left');
            $this->db->where('category.status',1);
            //$this->db->where('restaurant_menu_item.stock',1);
            $res_menu_items = $this->db->get_where('restaurant_menu_item',array('restaurant_menu_item.status'=>1))->result_array();
            $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
            $category_discount = $this->common_model->getCategoryDiscount($content_id);
            $result['menu_items'] = array();
            if(!empty($res_menu_items))
            {
                $item_not_appicable_for_discount = array();
                foreach ($res_menu_items as $key => $value)
                {
                    /*$result['menu_items'][$key]['image'] = ($value['image'])?$value['image']:'';*/
                    $value['image'] = ($value['image'])?$value['image']:'';
                    //offer price start
                    $offer_price = 0;
                    /*Begin::Category Discount Coupon Check*/
                    if(!empty($category_discount)){
                        foreach ($category_discount as $key => $cat_value) {
                            if(!empty($cat_value['combined'])){
                                if(isset($cat_value['combined'][$value['cat_content_id']])){
                                    if($value['price'] >= $cat_value['combined'][$value['cat_content_id']]['minimum_amount']){
                                        array_push($item_not_appicable_for_discount, $value['content_id']);
                                        if($cat_value['combined'][$value['cat_content_id']]['discount_type'] == 'Percentage'){
                                            $offer_price = $value['price'] - round(($value['price'] * $cat_value['combined'][$value['cat_content_id']]['discount_value'])/100,2);
                                        }
                                        if($cat_value['combined'][$value['cat_content_id']]['discount_type'] == 'Amount'){
                                            $offer_price = $value['price'] - $cat_value['combined'][$value['cat_content_id']]['discount_value'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*End::Category Discount Coupon Check*/
                    if(!empty($ItemDiscount)) {
                        foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                            if(!empty($cpnvalue['itemDetail'])) {
                                if(in_array($value['content_id'],$cpnvalue['itemDetail']) && !in_array($value['content_id'], $item_not_appicable_for_discount) ){
                                    if($cpnvalue['max_amount'] <= $value['price']){ 
                                        if($cpnvalue['amount_type'] == 'Percentage'){
                                            $offer_price = $value['price'] - round(($value['price'] * $cpnvalue['amount'])/100,2);
                                        }else if($cpnvalue['amount_type'] == 'Amount'){
                                            $offer_price = $value['price'] - $cpnvalue['amount'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $value['offer_price'] = ($offer_price)?number_format($offer_price, 2):'';
                    $result['menu_items'][]=$value;
                    //offer price changes end
                }
            }
            $this->db->select('restaurant_package.content_id, restaurant_package.name');
            $this->db->where('restaurant_package.restaurant_id',$restaurant_content_id);
            $this->db->where('restaurant_package.language_slug',$this->session->userdata('language_slug'));            
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_package.name LIKE '%" . trim($this->common_model->escapeString($value)) . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "restaurant_package.detail LIKE '%" . trim($this->common_model->escapeString($value)) . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "restaurant_package.availability LIKE '%" . trim($this->common_model->escapeString($value)) . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            $result['packages'] = $this->db->get_where('restaurant_package',array('status'=>1))->result_array();
            if (!empty($result['packages'])) {
                foreach ($result['packages'] as $key => $value) {
                    $result['packages'][$key]['image'] = (isset($value['image']))?$value['image']:'';
                }
            }
            
            //Code for sort addon category :: Start
            $selectval='';
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $selectval = ",(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no";
            }
            //End

            $this->db->select('restaurant_menu_item.category_id,category.name,category.sequence '.$selectval.'');
            $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
            //Code for sort addon category :: Start
            if($restaurant_owner_id != '' && intval($restaurant_owner_id) > 0 && $restaurant_content_id != '' && intval($restaurant_content_id) > 0) {
                $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = category.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');
            }
            //End
            //$this->db->order_by('category.sequence');
            $this->db->where('restaurant_menu_item.restaurant_id',$restaurant_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_menu_item.name LIKE '%" . trim($this->common_model->escapeString($value)) . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    //$like_statementsTwo[] = "restaurant_menu_item.menu_detail LIKE '%" . $this->common_model->escapeString($value) . "%'";
                    //$like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    //$like_statementsThree[] = "restaurant_menu_item.availability LIKE '%" . $this->common_model->escapeString($value) . "%'";
                    //$like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('('.$like_stringOne.')');
                //$this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
            }
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->order_by('sequence_no', 'ASC');
                $this->db->order_by('category.name', 'ASC');
            }
            else
            {
                if ($price == "low") {
                    $this->db->order_by('restaurant_menu_item.price','asc');
                }
                else
                {
                    $this->db->order_by('restaurant_menu_item.price','desc');
                }
            }
            //New code for search food type Start
            if(trim($food) != '')
            {
                $foodarr = explode(",",$food);
                $foodarr = array_filter($foodarr);
                if(!empty($foodarr))
                {   
                    $fdtcnt=0; $wherefindcn = '(';
                    foreach($foodarr as $keyf=>$valuef) 
                    { 
                        if($fdtcnt>0){
                            $wherefindcn .= " OR ";
                        }
                        $wherefindcn .= "(find_in_set ($valuef, restaurant_menu_item.food_type))";

                        $fdtcnt++;
                    }
                    $wherefindcn .= ')';
                    //$where = "(res.name like '%".$searchItem."%')";
                    if($fdtcnt>0)
                    { $this->db->where($wherefindcn);  }
                }
            }
            //New code for search food type End

            //New code add for availability :: Start
            if($availability!='')
            {
                $availabilityarr = explode(",",$availability);
                $availabilityarr = array_filter($availabilityarr);
                if(!empty($availabilityarr))
                {   
                    $fdtcnt=0; $wherefindcn = '(';
                    foreach($availabilityarr as $keyf=>$valuef) 
                    { 
                        if($fdtcnt>0){
                            $wherefindcn .= " OR ";
                        }
                        $wherefindcn .= "(find_in_set ('".$valuef."', restaurant_menu_item.availability))";

                        $fdtcnt++;
                    }
                    $wherefindcn .= ')';
                    //$where = "(res.name like '%".$searchItem."%')";
                    if($fdtcnt>0)
                    { $this->db->where($wherefindcn);  }
                }
                /*$wherefindavblt = "(find_in_set ('".$availability."', restaurant_menu_item.availability))";
                $this->db->where($wherefindavblt);*/
            }
            //New code add for availability :: End

            $this->db->group_by('restaurant_menu_item.category_id');
            $this->db->where('category.status',1);
            //$this->db->where('restaurant_menu_item.stock',1);
            $result['categories'] = $this->db->get_where('restaurant_menu_item',array('restaurant_menu_item.status'=>1))->result_array();
            if (!empty($result['categories']))
            {
                //Code for sort menu item :: Start
                $selectvalM='';
                if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
                {
                    $selectvalM = ",(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no";
                }
                //End

                foreach ($result['categories'] as $key => $value) {
                    $this->db->select('restaurant_menu_item.*,food_type.entity_id as food_type_id,food_type.is_veg,category.content_id as cat_content_id '.$selectvalM.'');
                    $this->db->where('restaurant_menu_item.restaurant_id',$restaurant_id);
                    $this->db->where('restaurant_menu_item.category_id',$value['category_id']);
                    $this->db->join('food_type','restaurant_menu_item.food_type = food_type.entity_id','left');
                    if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
                    {
                        $this->db->order_by('menumap.sequence_no', 'ASC');
                    }
                    else
                    {
                        if ($price == "low") {
                            $this->db->order_by('restaurant_menu_item.price','asc');
                        }
                        else
                        {
                            $this->db->order_by('restaurant_menu_item.price','desc');
                        }
                    }
                    
                    //New code for search food type Start
                    if(trim($food) != '')
                    {
                        $foodarr = explode(",",$food);
                        $foodarr = array_filter($foodarr);
                        if(!empty($foodarr))
                        {   
                            $fdtcnt=0; $wherefindcn = '(';
                            foreach($foodarr as $keyf=>$valuef) 
                            { 
                                if($fdtcnt>0){
                                    $wherefindcn .= " OR ";
                                }
                                $wherefindcn .= "(find_in_set ($valuef, restaurant_menu_item.food_type))";

                                $fdtcnt++;
                            }
                            $wherefindcn .= ')';
                            //$where = "(res.name like '%".$searchItem."%')";
                            if($fdtcnt>0)
                            { $this->db->where($wherefindcn);  }
                        }
                    }
                    //New code for search food type End

                    //New code add for availability :: Start
                    if($availability!='')
                    {
                        $availabilityarr = explode(",",$availability);
                        $availabilityarr = array_filter($availabilityarr);
                        if(!empty($availabilityarr))
                        {   
                            $fdtcnt=0; $wherefindcn = '(';
                            foreach($availabilityarr as $keyf=>$valuef) 
                            { 
                                if($fdtcnt>0){
                                    $wherefindcn .= " OR ";
                                }
                                $wherefindcn .= "(find_in_set ('".$valuef."', restaurant_menu_item.availability))";

                                $fdtcnt++;
                            }
                            $wherefindcn .= ')';
                            //$where = "(res.name like '%".$searchItem."%')";
                            if($fdtcnt>0)
                            { $this->db->where($wherefindcn);  }
                        }
                        /*$wherefindavblt = "(find_in_set ('".$availability."', restaurant_menu_item.availability))";
                        $this->db->where($wherefindavblt);*/
                    }
                    //New code add for availability :: End

                    if(!empty($searchArray)) {
                        $like_statementsOne = array();
                        $like_statementsTwo = array();
                        $like_statementsThree = array();
                        foreach($searchArray as $keyinn => $valueinn) {
                            $like_statementsOne[] = "restaurant_menu_item.name LIKE '%" . trim($this->common_model->escapeString($valueinn)) . "%'";
                            $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                            //$like_statementsTwo[] = "restaurant_menu_item.menu_detail LIKE '%" . $this->common_model->escapeString($valueinn) . "%'";
                            //$like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                            //$like_statementsThree[] = "restaurant_menu_item.availability LIKE '%" . $this->common_model->escapeString($valueinn) . "%'";
                            //$like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                        }
                        $this->db->where('('.$like_stringOne.')');
                        //$this->db->where('('.$like_stringOne.' OR '.$like_stringTwo.' OR '.$like_stringThree.')');
                    }
                    $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
                    //Code for sort menu item :: Start
                    if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
                    {
                        $this->db->join('menu_item_sequencemap as menumap',"menumap.menu_content_id = restaurant_menu_item.content_id AND menumap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');                
                    }            
                    //End
                    //$this->db->where('restaurant_menu_item.stock',1);
                    $result[$value['name']] = $this->db->get_where('restaurant_menu_item',array('restaurant_menu_item.status'=>1))->result_array();
                    //New code set for offer price to auto apply the coupon code :: Start
                    if($result[$value['name']] && !empty($result[$value['name']]))
                    {
                        $item_not_appicable_for_discount_on_item = array();
                        foreach($result[$value['name']] as $keyck=>$valueck) 
                        {
                            $result[$value['name']][$keyck]['image'] = ($valueck['image'])?$valueck['image']:'';
                            //offer price start
                            $offer_price = 0;
                            if(!empty($category_discount)){
                                foreach ($category_discount as $key => $cat_value) {
                                    if(!empty($cat_value['combined'])){
                                        if(isset($cat_value['combined'][$valueck['cat_content_id']])){
                                            if($valueck['price'] >= $cat_value['combined'][$valueck['cat_content_id']]['minimum_amount']){
                                                array_push($item_not_appicable_for_discount_on_item, $valueck['content_id']);
                                                if($cat_value['combined'][$valueck['cat_content_id']]['discount_type'] == 'Percentage'){
                                                    $offer_price = $valueck['price'] - round(($valueck['price'] * $cat_value['combined'][$valueck['cat_content_id']]['discount_value'])/100,2);
                                                }
                                                if($cat_value['combined'][$valueck['cat_content_id']]['discount_type'] == 'Amount'){
                                                    $offer_price = $valueck['price'] - $cat_value['combined'][$valueck['cat_content_id']]['discount_value'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if(!empty($ItemDiscount)) {
                                foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                                    if(!empty($cpnvalue['itemDetail'])) {
                                        if(in_array($valueck['content_id'],$cpnvalue['itemDetail']) && !in_array($valueck['content_id'], $item_not_appicable_for_discount_on_item)){
                                            if($cpnvalue['max_amount'] <= $valueck['price']){ 
                                                if($cpnvalue['amount_type'] == 'Percentage'){
                                                    $offer_price = $valueck['price'] - round(($valueck['price'] * $cpnvalue['amount'])/100,2);
                                                }else if($cpnvalue['amount_type'] == 'Amount'){
                                                    $offer_price = $valueck['price'] - $cpnvalue['amount'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $result[$value['name']][$keyck]['offer_price'] = ($offer_price)?number_format($offer_price, 2):'';
                            //offer price changes end
                        }
                    }                   
                    //New code set for offer price to auto apply the coupon code :: End
                }
            }
        }      
        $result['timearr'] = $arr;
        return $result;
    }
    // get restaurant reviews
    public function getRestaurantReview($restaurant_content_id){
        $avg_rating = 0;
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->select("review.restaurant_id,review.rating,review.review,users.first_name,users.last_name,users.image");
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.status',1);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $result =  $this->db->get('review')->result();
            if (!empty($result)) {
                $rating = array_column($result, 'rating');
                $a = array_filter($rating);
                if(count($a)) {
                    $average = array_sum($a)/count($a);
                    $avg_rating = number_format($average,1);
                }
            }
        } else {
            $avg_rating = number_format($res_rating->restaurant_rating,1);
        }
        return $avg_rating;
    }
    // get restaurant id
    public function getRestaurantID($restaurant_slug){
        $this->db->select('entity_id');
        return $this->db->get_where('restaurant',array('restaurant_slug'=>$restaurant_slug))->first_row();
    }
    // get content id from slug
    public function getContentID($restaurant_slug){
        $this->db->select('content_id');
        return $this->db->get_where('restaurant',array('restaurant_slug'=>$restaurant_slug))->first_row();
    }
    // get content id from restaurant id
    public function getRestContentID($restaurant_id){
        $this->db->select('content_id');
        return $this->db->get_where('restaurant',array('entity_id'=>$restaurant_id))->first_row();
    }
    // get All Restaurants
    public function getAllRestaurants($limit,$offset,$search_item=NULL,$search_event_res=NULL)
    {
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.content_id as res_content_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.enable_hours");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('restaurant.allow_event_booking',1);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_item)."%' OR restaurant_menu_item.name LIKE '%".$this->common_model->escapeString($search_item)."%'";
            $this->db->where($where);
        }
        if (!empty($search_event_res)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_event_res)."%'";
            $this->db->where($where);
        }
        $this->db->limit($limit,$offset);
        $result['data'] = $this->db->get_where('restaurant',array('restaurant.status'=>1))->result_array();

        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
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
                $result['data'][$key]['timings'] = $newTimingArr[strtolower($day)];
                $result['data'][$key]['image'] = ($value['image'])?$value['image']:'';
            }
        } 
        // sorting -- open/close
        usort($result['data'], function($a, $b) {
        return $b['timings']['closing'] > $a['timings']['closing'];
        });
        // total count
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('restaurant.allow_event_booking',1);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_item)."%' OR restaurant_menu_item.name LIKE '%".$this->common_model->escapeString($search_item)."%'";
            $this->db->where($where);
        }
        if (!empty($search_event_res)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_event_res)."%'";
            $this->db->where($where);
        }
        $result['count'] =  $this->db->get_where('restaurant',array('restaurant.status'=>1))->num_rows();
        return $result;
    }
    //get ratings and reviews of a restaurant
    public function getReviewsRatings($restaurant_content_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->select('review.*,users.first_name,users.last_name,users.image');
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $return = $this->db->get_where('review',array('review.status'=>1))->result_array();
        } else {
            $return = '';
        }
        return $return;
    }
    //check booking availability
    public function getBookingAvailability($date,$people,$restaurant_id){
        $date = date('Y-m-d H:i:s',strtotime($date));
        $datetime = date($date,strtotime('+1 hours'));
        $this->db->select('capacity,timings,enable_hours,event_online_availability');
        $this->db->where('content_id',$restaurant_id);
        $capacity =  $this->db->get('restaurant')->first_row();
        if ($capacity) {
            $timing = $capacity->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date('l', strtotime($date));
                foreach($timing as $keys=>$values) {
                    $day = date('l', strtotime($date));
                    if($keys == strtolower($day))
                    {
                        $close = 'Closed';
                        if($capacity->enable_hours=='1')
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
            $capacity->timings = $newTimingArr[strtolower($day)];
            $capacity->capacity = ($capacity->capacity>0 && $capacity->event_online_availability && $capacity->event_online_availability>0)?floor(($capacity->event_online_availability * $capacity->capacity)/100) : $capacity->capacity;
            //for booking
            $this->db->select('IFNULL(SUM(no_of_people),0) as people');
            $this->db->where('booking_date',$datetime);
            $this->db->where('restaurant_id',$restaurant_id);
            $event = $this->db->get('event')->first_row();
            //get event booking
            $peopleCount = $capacity->capacity - $event->people;       
            if($peopleCount >= $people && (date('H:i',strtotime($capacity->timings['close'])) >= date('H:i',strtotime($date))) && (date('H:i',strtotime($capacity->timings['open'])) <= date('H:i',strtotime($date)))){
                return true;
            }else{ 
                return false;
            }
        }
        else
        {   
            return false;
        }
    }
    //get tax
    public function getRestaurantTax($tblname,$restaurant_id,$flag){
        if($flag == 'order'){
            $this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code');
            $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
            $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        }else{
            $this->db->select('restaurant.name,restaurant.image,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant.amount_type,restaurant.amount,restaurant_address.latitude,restaurant_address.longitude');
            $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
            $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        }
        $this->db->where('restaurant.content_id',$restaurant_id);
        return $this->db->get($tblname)->first_row();
    }
    // get number of restaurant reviews
    public function getReviewsNumber($restaurant_content_id,$rating){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->where('restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->where('review.status',1);
            //$this->db->where('restaurant_id',$restaurant_id);
            $ratingPlus = $rating + 1;
            $this->db->where('(rating >= '.$rating.' AND rating < '.$ratingPlus.')');
            $this->db->group_by('entity_id');
            return $this->db->get('review')->num_rows();
        } else {
            if((int)$res_rating->restaurant_rating == $rating) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function getfoodtyepequery($food_type)
    {
        $wherefindcn = '';
        if(trim($food_type) != '')
        {
            $foodarr = explode(",",$food_type);
            $foodarr = array_filter($foodarr);
            if(!empty($foodarr))
            {   
                $fdtcnt=0; $wherefindcn = '(';
                foreach($foodarr as $key=>$value) 
                { 
                    if($fdtcnt>0){
                        $wherefindcn .= " OR ";
                    }
                    $wherefindcn .= "(find_in_set ($value, restaurant.food_type))";

                    $fdtcnt++;
                }
                $wherefindcn .= ')';
                //$where = "(res.name like '%".$searchItem."%')";
                if($fdtcnt>0)
                { //$this->db->where($wherefindcn);  
                    return $wherefindcn;
                }
            }
        }
        return $wherefindcn;
    }
    // get restaurants with pagination
    public function getRestaurantsForOrder($limit,$offset,$resdish=NULL,$latitude=NULL,$longitude=NULL,$minimum_range=NULL,$maximum_range=NULL,$food_veg=NULL,$food_non_veg=NULL,$pagination=NULL,$food_type='',$order_mode=NULL)
    {
        $language_slug = $this->session->userdata('language_slug');
        if (!empty($resdish))
        {
            //$where = "(restaurant_menu_item.name LIKE '%".$this->common_model->escapeString($resdish)."%' OR address.address LIKE '%".$this->common_model->escapeString($resdish)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($resdish)."%' OR category.name LIKE '%".$this->common_model->escapeString($resdish)."%')";
            $where = "(address.address LIKE '%".$this->common_model->escapeString($resdish)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($resdish)."%' OR restaurant.name LIKE '%".$this->common_model->escapeString($resdish)."%')";
            //$where = "(restaurant.name LIKE '%".$this->common_model->escapeString($resdish)."%')";
            //Query 1 code start
            $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode");
            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
            $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1 AND restaurant_menu_item.stock = 1','left');
            $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
            $this->db->like('restaurant.name',$this->common_model->escapeString($resdish));
            $this->db->like('restaurant.status',1);
            $this->db->where('restaurant.language_slug',$language_slug);
            //New code for search food type Start
            if(trim($food_type) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food_type);
                $this->db->where($wherefindcn);
            }
            //New code for search food type End
            //New code for search order mode start
            if (!empty($order_mode)){
                if($order_mode=='Both'){
                    $this->db->where('restaurant.order_mode','PickUp,Delivery');
                }else{
                    $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                    $this->db->where($wherefindom);
                }
            }
            //New code for search order mode End
            $this->db->group_by('restaurant.content_id');
            $this->db->from('restaurant');            
            $query1 = $this->db->get_compiled_select();

            //Query 2 code start
            $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode");
            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
            $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1 AND restaurant_menu_item.stock = 1','left');
            $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
            $this->db->where($where);
            $this->db->like('restaurant.status',1);
            $this->db->where('restaurant.language_slug',$language_slug);
            //New code for search food type Start
            if(trim($food_type) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food_type);
                $this->db->where($wherefindcn);
            }
            //New code for search food type End 
            //New code for search order mode start
            if (!empty($order_mode)){
                if($order_mode=='Both'){
                    $this->db->where('restaurant.order_mode','PickUp,Delivery');
                }else{
                    $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                    $this->db->where($wherefindom);
                }
            }
            //New code for search order mode End
            $this->db->group_by('restaurant.content_id'); 
            $this->db->like('restaurant.status',1);
            $this->db->from('restaurant');           
            $query2 = $this->db->get_compiled_select();

            $query = $this->db->query($query1 . ' UNION ' . $query2);
            $result = $query->result_array();                        
        }    
        else
        {
            $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode");
            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
            $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1 AND restaurant_menu_item.stock = 1','left');
            $this->db->where('restaurant.language_slug',$language_slug);
            $this->db->like('restaurant.status',1);
            //New code for search food type Start
            if(trim($food_type) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food_type);
                $this->db->where($wherefindcn);
            }
            //New code for search food type End
            //New code for search order mode start
            if (!empty($order_mode)){
                if($order_mode=='Both'){
                    $this->db->where('restaurant.order_mode','PickUp,Delivery');
                }else{
                    $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                    $this->db->where($wherefindom);
                }
            }
            //New code for search order mode End
            $this->db->group_by('restaurant.content_id');
            $result = $this->db->get_where('restaurant',array('restaurant.status'=>1))->result_array();
        }
        
        $finalData = array();
        if(!empty($result))
        {
            foreach ($result as $key => $value)
            {
                $timing = $value['timings'];
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values)
                    {
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
                $result[$key]['image'] = ($value['image'])?$value['image']:'';

                //Code for find the main restaurant name :: Start
                $rest_name = $value['name'];
                /*if($value['branch_entity_id']>0)
                {
                    $this->db->select("entity_id, name");
                    $this->db->where('language_slug',$language_slug);
                    $this->db->where('status',1);
                    $this->db->where('entity_id',$value['branch_entity_id']);
                    $rest_result =  $this->db->get('restaurant')->first_row();
                    if($rest_result->name!='' && $rest_result)
                    {
                        $rest_name = $rest_result->name.' ('.$value['name'].')';
                    }                    
                }*/                
                //Code for find the main restaurant name :: End

                $finalData[$value['content_id']] = array(
                    'MainRestaurantID'=> $value['restaurant_id'],
                    'name'=> $rest_name,
                    'address'=> $value['address'],
                    'landmark'=> $value['landmark'],
                    'latitude'=> $value['latitude'],
                    'longitude'=> $value['longitude'],
                    'image'=> $result[$key]['image'],                    
                    'timings'=> $result[$key]['timings'],
                    'distance'=> 0,                  
                    'language_slug'=> $value['language_slug'],
                    'content_id' =>$value['content_id'],
                    'restaurant_slug' =>$value['restaurant_slug'],
                    'restaurant_id'=>$value['restaurant_id']
                );

                $ratings = $this->restaurant_model->getRestaurantReview($value['content_id']);
                $review_data = $this->restaurant_model->getReviewsPagination($value['content_id'],review_count,1);
                $finalData[$value['content_id']]['restaurant_reviews'] = $review_data['reviews'];
                $finalData[$value['content_id']]['restaurant_reviews_count'] = $review_data['review_count'];
                $finalData[$value['content_id']]['ratings'] = $ratings;
            }            
        }
        
        $finalArray = array();
        if (!empty($finalData) && !empty($latitude) && !empty($longitude))
        { 
            foreach ($finalData as $key => $value)
            {
                $latitude1 = $latitude;
                $longitude1 = $longitude;
                $latitude2 = $value['latitude'];
                $longitude2 = $value['longitude'];
                $earth_radius = DISTANCE_CALCVAL;

                $dLat = deg2rad($latitude2 - $latitude1);  
                $dLon = deg2rad($longitude2 - $longitude1);  
        
                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
                $c = 2 * asin(sqrt($a));  
                $d = $earth_radius * $c;
                $finalData[$key]['distance'] = $d;
                if (isset($minimum_range) && isset($maximum_range)) { 
                    if ($minimum_range <= $d && $d <= $maximum_range) {
                        $finalArray[] = $value;
                    }
                    else
                    { 
                        unset($finalData[$key]);
                    }
                }
                else 
                {
                    $range = $this->common_model->getRange();
                    if($order_mode == 'PickUp') {
                        $maximum_range = (float)$range[2]->OptionValue;
                    } else {
                        $maximum_range = (float)$range[1]->OptionValue;
                    }
                    $minimum_range = (float)$range[0]->OptionValue;

                    if ($minimum_range <= $d && $d <= $maximum_range) { 
                        $finalArray[] = $value;
                    }
                    else
                    {
                        unset($finalData[$key]);
                    }
                }
            }
            //distance sorting for order_food page
            /*array_multisort(array_column($finalData, "distance"), SORT_ASC, $finalData );
            array_multisort(array_column($finalArray, "distance"), SORT_ASC, $finalArray );*/
        }
        
        // sorting -- open/close
        $filter_by = ($this->input->post('filter_by')) ? $this->input->post('filter_by') : 'distance';
        if($filter_by == 'rating'){
            array_multisort(array_column($finalData, "ratings"), SORT_DESC, $finalData );            
        }
        if($filter_by == 'distance'){
            array_multisort(array_column($finalData, "distance"), SORT_ASC, $finalData );
            //array_multisort(array_column($finalArray, "distance"), SORT_ASC, $finalArray );           
        }
        /*if($this->input->post('sort_by_ratings') == 1){
            array_multisort(array_column($finalData, "ratings"), SORT_DESC, $finalData );            
        }*/
        // usort($finalData, function($a, $b) {
        // return $b['timings']['closing'] > $a['timings']['closing'];
        // });
        usort($finalData, function($a, $b) use ($filter_by) {
            if ($a['timings']['closing']==$b['timings']['closing']) {
                if($filter_by=='rating') {
                    if($a['ratings']==$b['ratings']) {
                        //descending
                        return ($a['restaurant_reviews_count']>$b['restaurant_reviews_count'])?-1:1;
                    } else {
                        //descending
                        return ($a['ratings']>$b['ratings'])?-1:1;
                    }
                } else if($filter_by == 'distance') {
                    //ascending
                    return ($a['distance']>$b['distance'])?1:-1;
                }
            } else {
                //descending
                return ($a['timings']['closing']>$b['timings']['closing'])?-1:1;
            }
        });
        // sorting -- open/close
        $finalRestaurants = $finalData;
        if (!empty($pagination)) {
            $finalRestaurants = array_slice($finalData, $offset, $limit);
        }
        return $finalRestaurants;
    }
    //get item discount
    /*public function getItemDiscount($where){
        $this->db->where($where);
        $this->db->where('end_date >',date('Y-m-d H:i:s'));
        $result['couponAmount'] =  $this->db->get('coupon')->result_array();
        if(!empty($result['couponAmount'])){
            $res = array_column($result['couponAmount'], 'entity_id');
            $this->db->where_in('coupon_id',$res);
            $result['itemDetail'] = $this->db->get('coupon_item_map')->result_array();
        }
        return $result;
    }*/
    //get item discount
    public function getItemDiscount($where){
        $this->db->select('entity_id,max_amount,amount_type,amount');
        $this->db->where($where);
       /* $end_dateval = date('Y-m-d H:i:s');
        $end_dateval = $this->common_model->getZonebaseCurrentTime($end_dateval);
        $this->db->where('end_date >=',$end_dateval);*/
        $this->db->where('DATE_FORMAT(start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
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

    //get menu items
    public function getMenuItem($entity_id,$restaurant_id)
    {
        //Code for find the restaurant owner id :: Start
        $restaurant_owner_id='';
        $restaurant_content_id = $this->common_model->getContentId($restaurant_id,'restaurant');
        if($restaurant_id && $restaurant_id!='')
        {
            $rest_oid = $this->getRestownerid($restaurant_id);
            $restaurant_owner_id = $rest_oid->restaurant_owner_id;
            if($restaurant_owner_id==null || $restaurant_owner_id=='')
            {
                $this->db->select('entity_id');
                $this->db->where('user_type','MasterAdmin');
                $res_owner_idarr = $this->db->get('users')->first_row();
                $restaurant_owner_id = $res_owner_idarr->entity_id;                
            }              
        }       
        //Code for find the restaurant owner id :: End
        $language_slug = $this->session->userdata('language_slug');
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));

        //Code for sort addon category :: Start
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $selectval = ",(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no";
        }
        //End

        $this->db->select('menu.restaurant_id,menu.is_deal,menu.entity_id as menu_id,menu.content_id as menu_content_id,menu.status,menu.name,menu.price,menu.menu_detail,menu.is_combo_item,menu.image,menu.food_type,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.display_limit,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,add_ons_master.mandatory,c.content_id as cat_content_id,res.status as restaurant_status,res.allow_scheduled_delivery,res.content_id as res_content_id,menu.stock '.$selectval.'');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        //Code for sort addon category :: Start
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = add_ons_category.content_id AND menumap.restaurant_owner_id = '".$restaurant_owner_id."' AND menumap.restaurant_content_id = '".$restaurant_content_id."'",'left');
        }
        //End
        $this->db->where('menu.restaurant_id',$restaurant_id);
        $this->db->where('menu.language_slug',$language_slug);
        $this->db->where('menu.entity_id',$entity_id);
        $this->db->where('menu.status',1);
        $this->db->where('c.status',1);
        //$this->db->where('menu.stock',1);
        //Code for sort addon category :: Start
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->order_by('sequence_no', 'ASC');
        }
        //End

        $result = $this->db->get('restaurant_menu_item as menu')->result();
        $menu = array();
        if (!empty($result)) {
            $item_not_appicable_for_discount = array();
            foreach ($result as $key => $value)
            {
                //Code for food type section Start                
                $food_type_id = ''; $food_type_name = '';
                if($value->food_type!='')
                {
                    $is_vegarr = explode(",", $value->food_type);
                    $this->db->select('entity_id as food_type_id, name as food_type_name,is_veg');
                    $this->db->where_in('entity_id',$is_vegarr);
                    $this->db->where('food_type.status',1);
                    $this->db->where('food_type.language_slug',$language_slug);
                    $this->db->order_by('food_type.name', 'ASC');
                    $this->db->group_by('food_type.entity_id');
                    $resfood_type = $this->db->get('food_type')->result();
                    if($resfood_type && count($resfood_type)>0)
                    {
                        $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                        $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                        $is_veg_food = implode(",",array_column($resfood_type, 'is_veg'));
                    }
                }                
                //Code for food type section End
                //offer price start
                $offer_price = '';

                /*Begin::Category Discount Coupon Check*/
                $category_discount = $this->common_model->getCategoryDiscount($value->res_content_id);
                
                if(!empty($category_discount)){
                    foreach ($category_discount as $key => $cat_value) {
                        if(!empty($cat_value['combined'])){
                            if(isset($cat_value['combined'][$value->cat_content_id])){
                                if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                    array_push($item_not_appicable_for_discount, $value->menu_content_id);
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Percentage'){
                                        $offer_price = $value->price - round(($value->price * $cat_value['combined'][$value->cat_content_id]['discount_value'])/100,2);
                                    }
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Amount'){
                                        $offer_price = $value->price - $cat_value['combined'][$value->cat_content_id]['discount_value'];
                                    }
                                }
                            }
                        }
                    }
                }
                /*End::Category Discount Coupon Check*/
                if(!empty($ItemDiscount)) {
                    foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                        if(!empty($cpnvalue['itemDetail'])) {
                            if(in_array($value->menu_content_id,$cpnvalue['itemDetail']) && !in_array($value->menu_content_id, $item_not_appicable_for_discount)){
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
                //offer price changes end
                if (!isset($menu[$value->category_id])) 
                {
                    $menu[$value->category_id] = array();
                    $menu[$value->category_id]['category_id'] = $value->category_id;
                    $menu[$value->category_id]['category_name'] = $value->category;  
                }
                $image = ($value->image)?$value->image:'';
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('restaurant_id'=>$value->restaurant_id,'restaurant_status'=>$value->restaurant_status,'menu_id'=>$value->menu_id,'menu_content_id'=>$value->menu_content_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_veg_food'=>$is_veg_food,'is_combo_item'=>$value->is_combo_item,'stock'=>$value->stock,'allow_scheduled_delivery'=>$value->allow_scheduled_delivery);
                    }
                    if($value->is_deal == 1){
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['is_multiple'] = $value->is_multiple;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['mandatory'] = $value->mandatory;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['addons_list'][] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name);
                        $i++;
                    }else{
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['mandatory'] = $value->mandatory;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['display_limit'] = $value->display_limit;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
                        $i++;
                    }
                }else{
                    $menu[$value->category_id]['items'][]  = array('restaurant_id'=>$value->restaurant_id,'restaurant_status'=>$value->restaurant_status,'menu_id'=>$value->menu_id,'menu_content_id'=>$value->menu_content_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_veg_food'=>$is_veg_food,'is_combo_item'=>$value->is_combo_item,'stock'=>$value->stock,'allow_scheduled_delivery'=>$value->allow_scheduled_delivery);
                }
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $new = array();
        if (!empty($menu)) {
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
        }
        return $finalArray;     
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
    // get food type
    public function getFoodType()
    {
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select('food_type.name,food_type.entity_id,food_type.is_veg');
        $this->db->join('restaurant as res','(find_in_set (food_type.entity_id, res.food_type))');
        $this->db->where('food_type.status',1);
        $this->db->where('food_type.language_slug',$language_slug);  
        $this->db->order_by('food_type.name', 'ASC');
        $this->db->group_by('food_type.entity_id');
        return $this->db->get('food_type')->result();        
    }
    public function getRecipe_page($item_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('menu.content_id,map.recipe_content_id');
        $this->db->where('menu.entity_id',$item_id);
        $this->db->join('restaurant_menu_recipe_map as map','menu.content_id = map.menu_content_id','left');

        $recipe_content = $this->db->get('restaurant_menu_item as menu')->result();
        $this->db->select('slug');
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        $this->db->where('content_id',$recipe_content[0]->recipe_content_id);
        return $data= $this->db->get('recipe')->result();
    }
    public function getRecipe(){
        $this->db->select('map.menu_content_id,map.recipe_content_id');
        $this->db->where('recipe.status',1);
        $this->db->join('restaurant_menu_recipe_map as map','map.recipe_content_id= recipe.content_id');
        $data = $this->db->get('recipe')->result();
        return $data;
    }

    public function getRestaurantBookingCapacity($restaurant_id){
        $this->db->select('capacity,event_online_availability,event_minimum_capacity,table_online_availability,table_booking_capacity,table_minimum_capacity,allow_event_booking,enable_table_booking');
        $this->db->where('content_id',$restaurant_id);
        $this->db->where('language_slug',$this->session->userdata('language_slug'));
        $data = $this->db->get('restaurant')->first_row();
        $result = new \stdClass();
        if(!empty($data->event_online_availability)){
            $result->capacity = floor(($data->capacity*$data->event_online_availability)/100);
        }
        else{
            $result->capacity = $data->capacity;
        }
        if(!empty($data->table_online_availability)){
            $result->table_booking_capacity = floor(($data->table_booking_capacity*$data->table_online_availability)/100);
        }
        else{
            $result->table_booking_capacity = $data->table_booking_capacity;
        }
        $result->table_minimum_capacity = $data->table_minimum_capacity;
        $result->event_minimum_capacity = $data->event_minimum_capacity;
        $result->allow_event_booking = $data->allow_event_booking;
        $result->enable_table_booking = $data->enable_table_booking;
        return $result;
    }
    // get content id from entity id for event package
    public function getEventContentID($entity_id){
        $this->db->select('entity_id');
        return $this->db->get_where('restaurant_package',array('content_id'=>$entity_id,'language_slug'=>$this->session->userdata('language_slug')))->first_row();
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
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count) || !isset($res_rating->restaurant_rating)) {
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
    public function getRestownerid($restaurant_id){
        $this->db->select('restaurant_owner_id');
        $this->db->where('entity_id',$restaurant_id);        
        $data = $this->db->get('restaurant')->first_row();
        return $data;
    }
    // get All table booking Restaurants
    public function getAllTableRestaurants($limit,$offset,$search_item=NULL,$search_table_res=NULL)
    {
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.content_id as res_content_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.enable_hours");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('restaurant.enable_table_booking',1);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_item)."%' OR restaurant_menu_item.name LIKE '%".$this->common_model->escapeString($search_item)."%'";
            $this->db->where($where);
        }
        if (!empty($search_table_res)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_table_res)."%'";
            $this->db->where($where);
        }
        $this->db->limit($limit,$offset);
        $result['data'] = $this->db->get_where('restaurant',array('restaurant.status'=>1))->result_array();

        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
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
                $result['data'][$key]['timings'] = $newTimingArr[strtolower($day)];
                $result['data'][$key]['image'] = ($value['image'])?$value['image']:'';
            }
        } 
        // sorting -- open/close
        usort($result['data'], function($a, $b) {
        return $b['timings']['closing'] > $a['timings']['closing'];
        });
        // total count
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "'.$language_slug.'"','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('restaurant.enable_table_booking',1);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $where = "restaurant.name LIKE '%".$this->common_model->escapeString($search_item)."%' OR restaurant_menu_item.name LIKE '%".$this->common_model->escapeString($search_item)."%'";
            $this->db->where($where);
        }
        $result['count'] =  $this->db->get_where('restaurant',array('restaurant.status'=>1))->num_rows();
        return $result;
    }
    //check booking availability
    public function getTableBookingAvailability($date,$starttime,$endtime,$people,$restaurant_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('table_booking_capacity,timings,enable_hours,table_online_availability,enable_table_booking');
        $this->db->where('content_id',$restaurant_id);
        $this->db->where('language_slug',$language_slug);
        $capacity =  $this->db->get('restaurant')->first_row();
        if ($capacity) {
            $timing = $capacity->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date('l', strtotime($date));
                foreach($timing as $keys=>$values) {
                    $day = date('l', strtotime($date));
                    if($keys == strtolower($day))
                    {
                        $close = 'Closed';
                        if($capacity->enable_hours=='1')
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
            $capacity->timings = $newTimingArr[strtolower($day)];
            $capacity->table_booking_capacity = ($capacity->table_booking_capacity>0 && $capacity->table_online_availability && $capacity->table_online_availability>0)?floor(($capacity->table_online_availability * $capacity->table_booking_capacity)/100) : $capacity->table_booking_capacity;
            //for booking
            $new_starttime = date('H:i',strtotime($this->common_model->setZonebaseTime($starttime)));
            $new_endtime = date('H:i',strtotime($this->common_model->setZonebaseTime($endtime)));
            $this->db->select('IFNULL(SUM(no_of_people),0) as people');
            $this->db->where('booking_date',$date);
            $start_end_time_chk = "(NOT(start_time >= str_to_date('".$new_endtime."','%H:%i:%s') OR end_time <= str_to_date('".$new_starttime."','%H:%i:%s')))";
            $this->db->where($start_end_time_chk);
            $this->db->where('restaurant_content_id',$restaurant_id);
            $table_booking = $this->db->get('table_booking')->first_row();
            //get table booking
            $peopleCount = $capacity->table_booking_capacity - $table_booking->people; 
            if($peopleCount >= $people && (date('H:i',strtotime($capacity->timings['close'])) >= date('H:i',strtotime($endtime))) && (date('H:i',strtotime($capacity->timings['open'])) <= date('H:i',strtotime($starttime)))){
                return true;
            }else{ 
                return false;
            }
        }
        else
        {   
            return false;
        }
    }
    public function getRestaurantTimings($restaurant_id,$event_date)
    {
        $date = date('Y-m-d',strtotime($event_date));
        $this->db->select('timings,enable_hours');
        $this->db->where('content_id',$restaurant_id);
        $data =  $this->db->get('restaurant')->result_array();
        if ($data) {
            $timing = $data[0]['timings'];
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date('l', strtotime($date));
                foreach($timing as $keys=>$values) {
                    $day = date('l', strtotime($date));
                    if($keys == strtolower($day))
                    {
                        $close = 'Closed';
                        if($data[0]['enable_hours']=='1')
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
            $data[0]['timings'] = $newTimingArr[strtolower($day)];
        }
        return $data;
    }

    public function getBookingRestaurant($restaurant_id){
        $this->db->select('allow_event_booking,enable_table_booking');
        $this->db->where('content_id',$restaurant_id);
        $this->db->where('language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('restaurant')->first_row();
    }
    public function getRestaurantOrderMode($restaurant_id){
        $this->db->select('order_mode');
        $this->db->where('entity_id',$restaurant_id);
        $return = $this->db->get('restaurant')->first_row();
        if(!empty($return->order_mode)){
            $return->order_mode = explode(',', $return->order_mode);
        } else {
            $return->order_mode = array();
        }
        return $return->order_mode;
    }
    public function getResAllowSchedulingFlag($restaurant_id){
        $this->db->select('allow_scheduled_delivery');
        $this->db->where('entity_id',$restaurant_id);
        $return = $this->db->get('restaurant')->first_row();
        return ($return->allow_scheduled_delivery == 1) ? 1 : 0;
    }
    //bookmark restaurant
    public function addBookmark($data){
        $check = $this->db->get_where('bookmark_restaurant',$data)->num_rows();
        if($check<1){
            $this->db->insert('bookmark_restaurant',$data);
        }
        else{
            $this->db->delete('bookmark_restaurant',$data);   
        }

    }
    //get bookmark restaurant
    public function getBookmarkRestaurant($restaurant_id){
        $user_id = $this->session->userdata('UserID');
        if($user_id){
            $check = $this->db->get_where('bookmark_restaurant',array('restaurant_id'=>$restaurant_id,'user_id'=>$user_id))->num_rows();
            return ($check > 0)? '1' : '0';
        }
    }
    public function store_restaurant_error_report($data){
        $this->db->insert('restaurant_error_reports',$data);            
        $result = $this->db->insert_id();
        return ($result) ? $result : NULL;
    }

    public function get_restaurant_coupons($restaurant_content_id){
        $not_in_coupon = array("dine_in","discount_on_items","discount_on_categories");
        $this->db->select('coupon.entity_id,coupon.name,coupon.image,GROUP_CONCAT(coupon_restaurant_map.restaurant_id ORDER BY coupon_restaurant_map.restaurant_id ASC SEPARATOR ",") as restaurant_ids, coupon.maximaum_use_per_users,coupon.maximaum_use,coupon.coupon_for_newuser,coupon.coupon_type');
        $this->db->where('status',1);
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        $this->db->where_not_in('coupon_type',$not_in_coupon);
        // $this->db->order_by('created_by','desc');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
        $this->db->group_by('coupon.entity_id');
        $couponstemp = $this->db->get('coupon')->result();
        $return = array();
        $cntt=0;
        if($couponstemp && !empty($couponstemp))
        {
            for($i=0;$i<count($couponstemp);$i++)
            {
                $flag_cnt = 'yes';
                $UserID = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
                $UserType = ($this->session->userdata('UserType'))?$this->session->userdata('UserType'):'User';
                $checkCnt = $this->common_model->checkUserUseCountCoupon($UserID,$couponstemp[$i]->entity_id);
                if($checkCnt >= $couponstemp[$i]->maximaum_use_per_users && $couponstemp[$i]->maximaum_use_per_users>0 && $UserType=='User')
                {
                    $flag_cnt = 'no';
                }                    
                if($flag_cnt=='yes')
                {
                    $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($couponstemp[$i]->entity_id);
                    if($checkCnt1 >= $couponstemp[$i]->maximaum_use && $couponstemp[$i]->maximaum_use>0){
                        $flag_cnt = 'no';
                    }
                }
                if($flag_cnt=='yes')
                {
                    //Code for free delviery coupon falg check :: Start
                    $user_chkcpn = 'yes';                       
                    if($UserID>0)
                    {            
                        $this->db->select('entity_id');
                        $this->db->where('user_id',$UserID);
                        $user_chk = $this->db->count_all_results('order_master');
                        if($user_chk>0)
                        {
                            $user_chkcpn = 'no';
                        }            
                    }
                    if($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('UserType') == 'Agent'){
                        $user_chkcpn = 'no';
                    }                       
                    if(($couponstemp[$i]->coupon_type=='free_delivery' && $user_chkcpn=='no' && $couponstemp[$i]->coupon_for_newuser=='1') || $couponstemp[$i]->coupon_type=='user_registration' && $UserID==0)
                    {
                    }//Code for free delviery coupon falg check :: End
                    else
                    {
                        $return[$cntt] = $couponstemp[$i];
                        $cntt++;
                    }
                }
            }   
        }        
        //Code for filter array with requirement :: End         
        return $return;

    }
    public function getPackageDetail($content_id){
        $this->db->select('entity_id,content_id,image,name,detail,price,restaurant_id as rest_content_id');
        $this->db->where('content_id',$content_id);
        $this->db->where('language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('restaurant_package')->first_row();        
    }
}