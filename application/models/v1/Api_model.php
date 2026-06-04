<?php
class Api_model extends CI_Model {
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

    // get user record based on phone or email
    public function getUserRecord($phone_number, $email = NULL)
    {
        if(!empty($email)){
            $this->db->where('(mobile_number = "'.$phone_number.'" OR email = "'.$email.'")');
        } else {
            $this->db->where('mobile_number',$phone_number);
        }
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }

    // get restaurant id based on language slug
    public function getRestaurant($language_slug,$store_content_id=NULL){
        $this->db->select('entity_id,content_id');
        $this->db->where('language_slug',$language_slug);
        if (!empty($store_content_id)) {
            $this->db->where('content_id',$store_content_id);
        }
        return $this->db->get('restaurant')->first_row();
    }

    //get menu id based on the content id and language slug
    public function getMenuID($language_slug,$menu_content_id){
        $this->db->select('entity_id');
        $this->db->where('language_slug',$language_slug);
        $this->db->where('content_id',$menu_content_id);
        return $this->db->get('restaurant_menu_item')->first_row();
    }

    //get menu content id
    public function getMenuContentID($menu_id){
        $this->db->select('content_id,menu_detail,is_combo_item');
        $this->db->where('entity_id',$menu_id);
        return $this->db->get('restaurant_menu_item')->first_row();
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
                    $wherefindcn .= "(find_in_set ($value, res.food_type))";

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
    //get home
    public function getHomeRestaurant($latitude,$longitude,$searchItem,$food,$rating,$distance,$language_slug,$count,$page_no = 1,$sortBy=0,$category_id = '',$user_timezone='UTC',$order_mode = 0,$res_type='',$offersFreeDelivery = false,$availability = 0,$user_id=0)
    {
        //Code for free delviery coupon falg check :: Start        
        $user_chkcpnchk = 'yes';                       
        if($user_id>0)
        {            
            $this->db->select('entity_id');
            $this->db->where('user_id',$user_id);
            $user_chk = $this->db->count_all_results('order_master');
            if($user_chk>0)
            {
                $user_chkcpnchk = 'no';
            }            
        }
        else
        {
            $user_chkcpn = 'no';
        }
        //Code for free delviery coupon falg check :: End

        $page_no = ($page_no > 0)?$page_no-1:0;
        //get restaurants
        if($searchItem)
        {
            $where = "(menu.name LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.address LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($searchItem)."%' OR category.name LIKE '%".$this->common_model->escapeString($searchItem)."%')";
            //Query 1 code start
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours,res.order_mode,res.food_type,res.restaurant_rating");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id AND menu.status = 1','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');
            if($offersFreeDelivery) {
                $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = res.content_id','left');
                $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
                $this->db->where('coupon.coupon_type',"free_delivery");
                $this->db->where("(find_in_set ('Delivery', res.order_mode))");
                $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
                $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.status',1);
                if($user_chkcpnchk=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
            }
            $this->db->where('res.status',1);
            $this->db->like('res.name',$searchItem); 
            if(trim($category_id) != '') {
                $category_ids = explode(',', trim($category_id));
                $this->db->where_in('category.entity_id',$category_ids);
            }
            if(trim($food) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food);
                $this->db->where($wherefindcn);
            }
            if($rating){
                $this->db->having('rating <=',$rating);
            }
            if(!empty($distance)){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            
            /*Begin::Code for Order Mode based restaurant list*/
            if($order_mode >= 0)
            {
                $order_mode_arr = array(0 => 'Delivery',1 => 'PickUp');
                $order_mode_val = $order_mode_arr[$order_mode];
                if(!empty($order_mode_val)){
                    $where_find_order_mode = "(find_in_set ('".$order_mode_val."', res.order_mode))";
                    $this->db->where($where_find_order_mode);
                }
            }
            /*end::Code for Order Mode based restaurant list*/
            if($availability > 0) {
                $this->db->where('category.status',1);
                $availability_arr = array(1=>'Breakfast',2=>'Lunch',3=>'Dinner');
                $availabilityval = $availability_arr[$availability];
                $wherefindavblt = "(find_in_set ('".$availabilityval."', menu.availability))";
                $this->db->where($wherefindavblt);
            }
            $this->db->where('res.language_slug',$language_slug);
            $this->db->group_by('res.entity_id');
            
            $this->db->from('restaurant as res');            
            $query1 = $this->db->get_compiled_select();

            //Query 2 code start
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours,res.order_mode,res.food_type,res.restaurant_rating");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id AND menu.status = 1','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');
            if($offersFreeDelivery) {
                $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = res.content_id','left');
                $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
                $this->db->where('coupon.coupon_type',"free_delivery");
                $this->db->where("(find_in_set ('Delivery', res.order_mode))");
                $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
                $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.status',1);
                if($user_chkcpnchk=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
            }
            $this->db->where('res.status',1);
            $this->db->where($where);
            if(trim($category_id) != '') {
                $category_ids = explode(',', trim($category_id));
                $this->db->where_in('category.entity_id',$category_ids);
            }
            if(trim($food) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food);
                $this->db->where($wherefindcn);
            }
            if($rating){
                $this->db->having('rating <=',$rating);
            }
            if(!empty($distance)){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            
            /*Begin::Code for Order Mode based restaurant list*/
            if($order_mode >= 0)
            {
                $order_mode_arr = array(0 => 'Delivery',1 => 'PickUp');
                $order_mode_val = $order_mode_arr[$order_mode];
                if(!empty($order_mode_val)){
                    $where_find_order_mode = "(find_in_set ('".$order_mode_val."', res.order_mode))";
                    $this->db->where($where_find_order_mode);
                }
            }
            /*end::Code for Order Mode based restaurant list*/
            if($availability > 0) {
                $this->db->where('category.status',1);
                $availability_arr = array(1=>'Breakfast',2=>'Lunch',3=>'Dinner');
                $availabilityval = $availability_arr[$availability];
                $wherefindavblt = "(find_in_set ('".$availabilityval."', menu.availability))";
                $this->db->where($wherefindavblt);
            }
            $this->db->where('res.language_slug',$language_slug);
            $this->db->group_by('res.entity_id');
            
            $this->db->from('restaurant as res');            
            $query2 = $this->db->get_compiled_select();
            /*if($count)
            {
                if($sortBy>0)
                {
                    $ifnullquery = "IFNULL(restaurant_rating,rating)";
                    $query = $this->db->query("(".$query1.")UNION(".$query2.") order by ".$ifnullquery." DESC limit " .$page_no*$count.",".$count);
                }
                else
                {
                    $query = $this->db->query("(".$query1.")UNION(".$query2.") order by distance ASC limit " .$page_no*$count.",".$count);
                }                
            }
            else
            {*/
                if($sortBy>0)
                {
                    $ifnullquery = "IFNULL(restaurant_rating,rating)";
                    $query = $this->db->query("(".$query1.")UNION(".$query2.") order by ".$ifnullquery." DESC");
                }
                else
                {
                    $query = $this->db->query("(".$query1.")UNION(".$query2.") order by distance ASC");
                } 
            //}
            $result['data'] = $query->result();                       
        }
        else
        {
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours,res.order_mode,res.food_type");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->where('res.status',1);
            if(trim($category_id) != '' || $availability > 0) {
                $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id AND menu.status = 1','left');
                $this->db->join('category','menu.category_id = category.entity_id','left');
            }
            if(trim($category_id) != '') {
                $category_ids = explode(',', trim($category_id));
                $this->db->where_in('category.entity_id',$category_ids);
            }
            if($offersFreeDelivery) {
                $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = res.content_id','left');
                $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
                $this->db->where('coupon.coupon_type',"free_delivery");
                $this->db->where("(find_in_set ('Delivery', res.order_mode))");
                $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
                $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.status',1);
                if($user_chkcpnchk=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
            }
            if(trim($food) != '')
            {
                $wherefindcn = $this->getfoodtyepequery($food);
                $this->db->where($wherefindcn);
            }
            if($rating){
                $this->db->having('rating <=',$rating);
            }
            if(!empty($distance)){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            
            /*Begin::Code for Order Mode based restaurant list*/
            if($order_mode >= 0)
            {
                $order_mode_arr = array(0 => 'Delivery',1 => 'PickUp');
                $order_mode_val = $order_mode_arr[$order_mode];
                if(!empty($order_mode_val)){
                    $where_find_order_mode = "(find_in_set ('".$order_mode_val."', res.order_mode))";
                    $this->db->where($where_find_order_mode);
                }
            }
            /*end::Code for Order Mode based restaurant list*/
            if($availability > 0) {
                $this->db->where('category.status',1);
                $availability_arr = array(1=>'Breakfast',2=>'Lunch',3=>'Dinner');
                $availabilityval = $availability_arr[$availability];
                $wherefindavblt = "(find_in_set ('".$availabilityval."', menu.availability))";
                $this->db->where($wherefindavblt);
            }
            $this->db->where('res.language_slug',$language_slug);
            $this->db->group_by('res.entity_id');
            if($sortBy>0)
            {
                $this->db->order_by('IFNULL(res.restaurant_rating,rating)', 'DESC', false);
            }
            else
            {
                $this->db->order_by("distance", "ASC");
            }
            
            /*if($count){
                $this->db->limit($count,$page_no*$count);
            }*/
            $result['data'] =  $this->db->get('restaurant as res')->result();
        }
        $default_currency = get_default_system_currency();
        foreach ($result['data'] as $key => $value) {
            unset($result['data'][$key]->restaurant_rating);
            if(!empty($default_currency)){
                $value->currency_symbol = $default_currency->currency_symbol;
                $value->currency_code = $default_currency->currency_code;                
            }
            $timing = $value->timings;            
            if($timing){
               $timing =  unserialize(html_entity_decode($timing));
               
               $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values) {
                    $day = date("l");
                    if($keys == strtolower($day))
                    {
                        $close = 'close';
                        if($value->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            //$newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']))?($values['close'] >= date('H:m') && date('H:m') >= $values['open'])?'open':'close':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            $value->timings = $newTimingArr[strtolower($day)];
            //$value->image = ($value->image)?image_url.$value->image:'';            
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';//default_img
            
            $rphone_codeval = $value->phone_number;
            if($rphone_codeval!='') {
                $rphone_codeval= str_replace("+","",$rphone_codeval);
                $rphone_codeval = '+'.$rphone_codeval;                        
            }
            $value->phone_number = $rphone_codeval;
            $chk_res_rating = $this->checkRestaurantRating($value->content_id,$language_slug);
            $value->rating = ($chk_res_rating['is_rating_from_res_form'] == '1' && $chk_res_rating['avg_rating']) ? $chk_res_rating['avg_rating'] : (($value->rating)?number_format((float)$value->rating, 1, '.', ''):null);
            $value->restaurant_review_count = $this->getRestaurantReviewCount($value->content_id, $language_slug);

            $couponstemp = $this->getCouponsForHome($value->content_id,$value->order_mode);            
            //Code for filter array with requirement :: Start
            $restaurant_couponsarr = array();
            $cntt=0;
            if($couponstemp && !empty($couponstemp))
            {
                for($i=0;$i<count($couponstemp);$i++)
                {   
                    $flag_cnt = 'yes'; $user_chk=0;
                    $UserID = ($user_id)?$user_id:0;
                    $checkCnt = $this->common_model->checkUserUseCountCoupon($UserID,$couponstemp[$i]->coupon_id);
                    if($checkCnt >= $couponstemp[$i]->maximaum_use_per_users && $couponstemp[$i]->maximaum_use_per_users>0){
                        $flag_cnt = 'no';
                    }                    
                    if($flag_cnt=='yes')
                    {
                        $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($couponstemp[$i]->coupon_id);
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

                        $order_mode_arr = explode(',', $value->order_mode);                      
                        if(($couponstemp[$i]->coupon_type=='free_delivery' && in_array('Delivery', $order_mode_arr) && $user_chkcpn=='no' && $couponstemp[$i]->coupon_for_newuser=='1') || ($couponstemp[$i]->coupon_type=='user_registration' && $UserID==0) || ($couponstemp[$i]->coupon_type=='user_registration' && $user_chk>0))
                        {
                        }//Code for free delviery coupon falg check :: End
                        else
                        {
                            $restaurant_couponsarr[$cntt] = $couponstemp[$i];
                            $cntt++;
                        }
                    }
                }   
            }
            $value->restaurant_coupons = $restaurant_couponsarr;
            //Code for filter array with requirement :: End 

            //Code for find the main restaurant name :: Start
            $rest_name = $value->name;
            /*if($value->branch_entity_id>0)
            {
                $this->db->select("entity_id, name");
                $this->db->where('language_slug',$language_slug);
                $this->db->where('status',1);
                $this->db->where('entity_id',$value->branch_entity_id);
                $rest_result =  $this->db->get('restaurant')->first_row();
                if($rest_result->name!='' && $rest_result)
                {
                    $rest_name = $rest_result->name.' ('.$value->name.')';
                }                    
            }*/
            $value->name = $rest_name;
            //Code for find the main restaurant name :: End
        }
        // sorting -- open/close
        if($sortBy>0){
            array_multisort(array_column($result['data'], "rating"), SORT_DESC, $result['data']);            
        }        
        // usort($result['data'], function($a, $b) {
        //     return $a->timings['closing'] < $b->timings['closing'] ;
        // });
        usort($result['data'], function($a, $b) use ($sortBy) {
            if ($a->timings['closing']==$b->timings['closing']) {
                if($sortBy>0) {
                    if($a->rating==$b->rating) {
                        //descending
                        return ($a->restaurant_review_count>$b->restaurant_review_count)?-1:1;
                    } else {
                        //descending
                        return ($a->rating>$b->rating)?-1:1;
                    }
                } else if($sortBy == 0) {
                    //ascending
                    return ($a->distance>$b->distance)?1:-1;
                }
            } else {
                //descending
                return ($a->timings['closing']>$b->timings['closing'])?-1:1;
            }
        });

        $finalRestaurants = $result['data'];
        if ($count) {
            $finalRestaurants = array_slice($result['data'], ($page_no*$count),$count);
        }        
        $result['data'] = $finalRestaurants;

        //to get restaurant count
        $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,address.address,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.order_mode,res.food_type");
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->where('res.status',1);
        if($searchItem || trim($category_id) != '' || $availability > 0) {
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id AND menu.status = 1','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');
        }
        if($searchItem){
            /*Old which created issue keyword like dessert's*/
            // $where = "(res.name like '%".$searchItem."%' OR category.name like '%".$searchItem."%')";

            /*new*/
            $where = "(res.name LIKE '%".$this->common_model->escapeString($searchItem)."%' OR menu.name LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.address LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($searchItem)."%' OR category.name LIKE '%".$this->common_model->escapeString($searchItem)."%')";
            
            $this->db->where($where);
        }
        if(trim($category_id) != '') {
            $category_ids = explode(',', trim($category_id));
            $this->db->where_in('category.entity_id',$category_ids);
        }
        if($offersFreeDelivery) {
            $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = res.content_id','left');
            $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
            $this->db->where('coupon.coupon_type',"free_delivery");
            $this->db->where("(find_in_set ('Delivery', res.order_mode))");
            $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
            $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
            $this->db->where('coupon.status',1);
            if($user_chkcpnchk=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
        }
        if(trim($food) != '') {
            $wherefindcn = $this->getfoodtyepequery($food);
            $this->db->where($wherefindcn);
        }
        if($rating){
            $this->db->having('rating <=',$rating);
        }
        if(!empty($distance)){
            $this->db->having('distance <=',$distance);
        }else{
            $this->db->having('distance <',NEAR_KM);
        }        
        /*Begin::Code for Order Mode based restaurant list*/
        if($order_mode >= 0)
        {
            $order_mode_arr = array(0 => 'Delivery',1 => 'PickUp');
            $order_mode_val = $order_mode_arr[$order_mode];
            if(!empty($order_mode_val)){
                $where_find_order_mode = "(find_in_set ('".$order_mode_val."', res.order_mode))";
                $this->db->where($where_find_order_mode);
            }
        }
        /*end::Code for Order Mode based restaurant list*/
        if($availability > 0) {
            $this->db->where('category.status',1);
            $availability_arr = array(1=>'Breakfast',2=>'Lunch',3=>'Dinner');
            $availabilityval = $availability_arr[$availability];
            $wherefindavblt = "(find_in_set ('".$availabilityval."', menu.availability))";
            $this->db->where($wherefindavblt);
        }
        $this->db->where('res.language_slug',$language_slug);
        $this->db->group_by('res.entity_id');
        if($sortBy>0)
        {
            $this->db->order_by('IFNULL(res.restaurant_rating,rating)', 'DESC', false);
        }
        else
        {
            $this->db->order_by("distance", "ASC");
        }
        $result['count'] =  $this->db->get('restaurant as res')->num_rows();
        return $result;
    }
    //get banner
    public function getbanner(){
        $this->db->select('image');
        $this->db->where('status',1);
        $images =  $this->db->get('slider_image')->result();
        foreach ($images as $key => $value) {
            //$value->image = ($value->image)?image_url.$value->image:'';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
        }
        return $images;
    }
    //get home page category
    public function getcategory($language_slug){
        $this->db->select('category.content_id,category.entity_id as category_id, category.name,category.image');
        $this->db->where('category.language_slug',$language_slug);
        //$this->db->order_by('category.entity_id','desc');
        $this->db->order_by('category.sequence','ASC');
        $this->db->group_by('category.content_id');
        $this->db->where('category.status',1);
        //$this->db->limit(4, 0);
        $result =  $this->db->get('category')->result(); 
        foreach ($result as $key => $value) {
            //$value->image = ($value->image)?image_url.$value->image:'';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
        }
        return $result;
    }
    //get restaurant
    public function getRestaurantDetail($content_id,$language_slug,$user_timezone='UTC'){
        $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.timings,res.image,res.background_image,address.address,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,currencies.currency_symbol,currencies.currency_code,res.branch_entity_id,res.enable_hours,res.allowed_days_table as allowed_days_for_booking,res.enable_table_booking,res.allow_event_booking,res.capacity as event_booking_capacity,res.event_minimum_capacity,res.event_online_availability,res.table_booking_capacity,res.table_online_availability,res.table_minimum_capacity,res.order_mode,res.about_restaurant,res.allow_scheduled_delivery");
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->where('res.content_id',$content_id);
        $this->db->where('res.language_slug',$language_slug);
        $this->db->group_by('res.entity_id');
        $result =  $this->db->get('restaurant as res')->result();
        $timeslotfordates = array();
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            if($value->allow_event_booking=='1'){
                $value->event_booking_capacity = ($value->event_online_availability>0 && $value->event_booking_capacity>0)?floor(($value->event_online_availability * $value->event_booking_capacity)/100) : NULL;
                $value->event_min_max_txt = sprintf($this->lang->line('event_max_people'),$value->event_minimum_capacity,$value->event_booking_capacity);
            }
            if($value->enable_table_booking=='1'){
                $value->table_booking_capacity = ($value->table_online_availability>0 && $value->table_booking_capacity>0)?floor(($value->table_online_availability * $value->table_booking_capacity)/100) : NULL;
                $value->table_min_max_txt = sprintf($this->lang->line('max_people'),$value->table_minimum_capacity,$value->table_booking_capacity);
            }
            if(!empty($default_currency)){
                $value->currency_symbol = $default_currency->currency_symbol;
                $value->currency_code = $default_currency->currency_code;                
            }
            // If Delivery Orders then flag_delivery_order
            $value->flag_delivery_order = false;
            if(!empty($value->order_mode)){
                $arrOrderMode = explode(",",$value->order_mode);
                if(in_array('Delivery', $arrOrderMode)){
                    $value->flag_delivery_order = true;
                }
            }
            //days allowed for event booking code :: start
            if(!empty($value->allowed_days_for_booking)) {
                $timing = $value->timings;
                $timing = unserialize(html_entity_decode($timing));
                for ($i=1; $i >0 ; $i++) { 
                    $date = date("Y-m-d",strtotime("+".$i." day", strtotime("now")));
                    $day = date("l",strtotime(' +'.$i.' day'));
                    foreach($timing as $keys=>$values) {
                        if((strtolower($day)==$keys) && $value->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        }
                    }
                    if($newTimingArr[strtolower($day)]['off'] != 'close'){
                        $newdate = date_format(date_create($date),"F d,Y");
                        //timeslots
                        $start_datetime = new DateTime(date('G:i',strtotime($newTimingArr[strtolower($day)]['open'])));
                        $end_datetime = new DateTime(date('G:i',strtotime($newTimingArr[strtolower($day)]['close'])));
                        $timeslots = $this->common_model->getTimeSlots(TIME_INTERVAL, $start_datetime->format('H:i'), $end_datetime->format('H:i'));
                        $timeslotfordates[date('d M, Y',strtotime($newdate))] = $timeslots;

                    }
                    if(sizeof($timeslotfordates) == $value->allowed_days_for_booking){
                        break;
                    }
                }
            }
            $value->time_slots = $timeslotfordates;
            //days allowed for event booking code :: end
            $timing = $value->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $result[$key]->week_timings = ($value->enable_hours=='1')?$timing:array();
                $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values){
                    $day = date("l");
                    
                    if($keys == strtolower($day))
                    {
                        $close = 'close';
                        if($value->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['current_day'] = strtolower($day);
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            $value->timings = $newTimingArr[strtolower($day)];
            foreach ($result[0]->week_timings as $key1 => $value1) {
                $result[0]->week_timings[$key1]['label']=$this->lang->line($key1);
                $result[0]->week_timings[$key1]['open']=(!empty($value1['open']))?$this->common_model->getZonebaseTime($value1['open'],$user_timezone):'';
                $result[0]->week_timings[$key1]['close']=(!empty($value1['close']))?$this->common_model->getZonebaseTime($value1['close'],$user_timezone):'';
            }
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
            $value->background_image = (file_exists(FCPATH.'uploads/'.$value->background_image) && $value->background_image!='') ? image_url.$value->background_image : '';
            $rphone_codeval = $value->phone_number;
            if($rphone_codeval!='') {
                $rphone_codeval= str_replace("+","",$rphone_codeval);
                $rphone_codeval = '+'.$rphone_codeval;                        
            }
            $value->phone_number = $rphone_codeval;
            $chk_res_rating = $this->checkRestaurantRating($content_id,$language_slug);
            $value->rating = ($chk_res_rating['is_rating_from_res_form'] == '1' && $chk_res_rating['avg_rating']) ? $chk_res_rating['avg_rating'] : (($value->rating)?number_format((float)$value->rating, 1, '.', ''):null);
            $value->is_rating_from_res_form = $chk_res_rating['is_rating_from_res_form'];
            //Code for find the main restaurant name :: Start
            $rest_name = $value->name;
            /*if($value->branch_entity_id>0)
            {
                $this->db->select("entity_id, name");
                $this->db->where('language_slug',$language_slug);
                $this->db->where('status',1);
                $this->db->where('entity_id',$value->branch_entity_id);
                $rest_result =  $this->db->get('restaurant')->first_row();
                if($rest_result->name!='' && $rest_result)
                {
                    $rest_name = $rest_result->name.' ('.$value->name.')';
                }                    
            }*/
            $value->name = $rest_name;
            //Code for find the main restaurant name :: End            
        }
        $result[0]->restaurant_review_count = $this->getRestaurantReviewCount($content_id,$language_slug);
        return $result;
    }
    //get populer item
    public function item_image($restaurant_id,$language_slug){
        $this->db->select('image');
        $this->db->where('popular_item !=',1);
        $this->db->where('image !=','');
        $this->db->where('status',1);
        $this->db->where('stock',1);
        if($restaurant_id){
            $this->db->where('restaurant_id',$restaurant_id);
        }
        $this->db->where('language_slug',$language_slug);
        $this->db->limit(10, 0);
        $result = $this->db->get('restaurant_menu_item')->result();
        foreach ($result as $key => $value) {
            //$value->image = ($value->image)?image_url.$value->image:'';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
        }
        return $result;
    }
    //get items
    public function getMenuItem($restaurant_id,$food,$price,$language_slug,$popular,$availability=0,$user_timezone = 'UTC',$filter_out_of_stock)
    {
        //Code for find the restaurant owner id :: Start
        $restaurant_content_id = $this->common_model->getContentId($restaurant_id,'restaurant');
        $restaurant_owner_id='';
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
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $selectval = ",(CASE WHEN catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no";
            $selectvaladd = ",(CASE WHEN menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_noaa";
            $selectvalM = ",(CASE WHEN  menuitemmap.sequence_no is NULL THEN 1000 ELSE menuitemmap.sequence_no END) as sequence_nomenu";
        }               
        //Code for find the restaurant owner id :: End

        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        $this->db->select('menu.is_deal,menu.entity_id as menu_id, menu.content_id as menu_content_id, menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.food_type,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id, add_ons_master.is_multiple,add_ons_master.display_limit,add_ons_master.mandatory,is_combo_item,menu.stock,c.content_id as cat_content_id '.$selectval.' '.$selectvaladd.' '.$selectvalM.''); //menu.ingredients, menu.recipe_detail,
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = c.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');
        }
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = add_ons_category.content_id AND menumap.restaurant_owner_id = '".$restaurant_owner_id."' AND menumap.restaurant_content_id = '".$restaurant_content_id."'",'left');
        }
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->join('menu_item_sequencemap as menuitemmap',"menuitemmap.menu_content_id = menu.content_id AND menuitemmap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');                
        }
        // $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu.restaurant_id',$restaurant_id);
        $this->db->where('menu.status',1);
        if($filter_out_of_stock == '1'){
            $this->db->where('menu.stock',1);
        }
        $this->db->where('c.status',1);
        //$this->db->order_by('c.sequence','ASC'); 
        //Code for sort addon category :: Start
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $this->db->order_by('sequence_no, c.name, sequence_nomenu, sequence_noaa', 'ASC');
        }
        //End

        //New code set as per required :: Start :: 03-02-2021 :: Start
        if($availability>0)
        {
            $availability_arr = array(1=>'Breakfast',2=>'Lunch',3=>'Dinner');
            $availabilityval = $availability_arr[$availability];
            $wherefindavblt .= "(find_in_set ('".$availabilityval."', menu.availability))";
            $this->db->where($wherefindavblt);
        }
        //New code set as per required :: Start :: 03-02-2021 :: End

        if($popular == 1){
            $this->db->where('popular_item',1);
            //$this->db->where('menu.image !=','');
        }
        
        if($price == 1)
        {
            $this->db->order_by('menu.price','desc');
        }else{
            $this->db->order_by('menu.price','asc');
        }
        //New code for search food type Start
        if(trim($food) != '')
        {
            $foodarr = explode(",",$food);
            $foodarr = array_filter($foodarr);

            if(!empty($foodarr))
            {   
                $fdtcnt=0; $wherefindcn = '(';
                foreach($foodarr as $key=>$value) 
                { 
                    if($fdtcnt>0){
                        $wherefindcn .= " OR ";
                    }
                    $wherefindcn .= "(find_in_set ($value, menu.food_type))";
                    $fdtcnt++;
                }
                $wherefindcn .= ')';
                //$where = "(res.name like '%".$searchItem."%')";
                if($fdtcnt>0)
                { $this->db->where($wherefindcn);  }
            }
        }
        //New code for search food type End
        $this->db->where('menu.language_slug',$language_slug);
        //$this->db->group_by('menu.content_id');
        $result = $this->db->get('restaurant_menu_item as menu')->result();        

        $restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$restaurant_id);
        $category_discount = '';
        if(!empty($restaurant_data) && $restaurant_data->content_id){
            $category_discount = $this->common_model->getCategoryDiscount($restaurant_data->content_id,$user_timezone);
        }
        $menu = array();
        $item_not_appicable_for_item_discount = array();
        foreach ($result as $key => $value)
        {
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
            //offer price start
            $offer_price = '';
            if(!empty($category_discount)){
                foreach ($category_discount as $key => $cat_value) {
                    if(!empty($cat_value['combined'])){
                        if(isset($cat_value['combined'][$value->cat_content_id])){
                            if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                array_push($item_not_appicable_for_item_discount, $value->menu_content_id);
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
            //offer price changes end
            if (!isset($menu[$value->category_id])) 
            {
                $menu[$value->category_id] = array();
                $menu[$value->category_id]['category_id'] = $value->category_id;
                $menu[$value->category_id]['category_name'] = $value->category;  
            }
            //$image = ($value->image)?image_url.$value->image:'';
            $image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
            $total = 0;
            $item_availibility_array = explode(",",$value->availability);
            $lang_availibility_txt = '';
            foreach ($item_availibility_array as $item_availibility_val){
                $lang_availibility_txt .= $this->lang->line(strtolower($item_availibility_val)).',';
            }
            if($lang_availibility_txt != ''){
                $lang_availibility_txt = substr($lang_availibility_txt, 0, -1);
            }

            //Code for find menu item recipe :: Start
            $recipes_menu = [];
            $is_recipes_menu = 0;
            //$menuitem_recipe = $this->getRecipe_detail($value->menu_content_id,$language_slug);
            if($menuitem_recipe && !empty($menuitem_recipe))
            {
                $is_recipes_menu = 1;                
            }
            //Code for find menu item recipe :: End

            if($value->check_add_ons == 1){
                if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                   $menu[$value->category_id]['items'][$value->menu_id] = array();
                   $menu[$value->category_id]['items'][$value->menu_id] = array('menu_id'=>$value->menu_id, 'menu_content_id'=>$value->menu_content_id, 'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$lang_availibility_txt,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_combo_item' => 0,'combo_item_details' => '','is_recipes_menu'=>$is_recipes_menu,'in_stock'=>$value->stock); //'ingredients'=>$value->ingredients, 'menuitem_recipe'=>$menuitem_recipe, 'recipe_detail'=>$value->recipe_detail,
                }
                if($value->is_deal == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'])){
                       $i = 0;
                       $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'] = array();
                    //   $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category'] = $value->deal_category_name;
                    //   $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->deal_category_id]['addons_category_id'] = $value->deal_category_id;
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
                $menu[$value->category_id]['items'][$value->menu_content_id]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price, 'menu_content_id'=>$value->menu_content_id, 'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$lang_availibility_txt,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_combo_item'=>$value->is_combo_item,'combo_item_details'=>($value->is_combo_item == '1') ? substr(str_replace("\r\n"," + ",$value->menu_detail),0,-3) : '','is_recipes_menu'=>$is_recipes_menu,'in_stock'=>$value->stock); //'ingredients'=>$value->ingredients, 'menuitem_recipe'=>$menuitem_recipe, 'recipe_detail'=>$value->recipe_detail,
            }
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
                        if($addons_cat_list['addons_list'] && !empty($addons_cat_list['addons_list']))
                        {
                            $addons_cat_list['addons_list'] = array_values($addons_cat_list['addons_list']);
                        }
                        array_push($semifinal, $addons_cat_list);
                    }
                    $items['addons_category_list'] = $semifinal;                  
                }
                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;     
    }
    //Code for find the recipe detail base on menu :: Start
    public function getRecipe_detail($menu_content_id,$language_slug)
    {        
        //Code for record :: Start
        $this->db->select('entity_id, name, detail, slug, image, ingredients, recipe_detail, recipe_time, language_slug, status, content_id, food_type,youtube_video');
        $this->db->join('restaurant_menu_recipe_map','restaurant_menu_recipe_map.recipe_content_id = recipe.content_id','left');
            $this->db->where('restaurant_menu_recipe_map.menu_content_id',$menu_content_id);
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);
        $this->db->order_by('entity_id','desc');
        $result = $this->db->get('recipe')->first_row();
        if($result && !empty($result))
        {
            $food_type_id = ''; $food_type_name = '';
            if($result->food_type!='')
            {
                $is_vegarr = explode(",", $result->food_type);
                $this->db->select('entity_id as food_type_id, name as food_type_name');
                $this->db->where_in('entity_id',$is_vegarr);
                $resfood_type = $this->db->get('food_type')->result();
                if($resfood_type && count($resfood_type)>0)
                {
                    $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                    $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                }
            }
            $result->food_type_id = $food_type_id;
            $result->food_type_name = $food_type_name;
            //Code for food type section End
            $result->image = (file_exists(FCPATH.'uploads/'.$result->image) && $result->image!='') ? image_url.$result->image : '';
        }        
        return $result;
    }
    //Code for find the recipe detail base on menu :: End
    //get popular item
    public function getPopularItem($restaurant_id,$language_slug){
        $this->db->select('menu.entity_id as menu_id,menu.name,menu.price,menu.menu_detail,menu.image,menu.food_type,menu.status,menu.content_id as menu_content_id,menu.is_deal,availability'); //menu.recipe_detail,
         //,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,menu.ingredients
        // $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        // $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->where('menu.restaurant_id',$restaurant_id);
        $this->db->where('popular_item',1);
        $this->db->where('menu.status',1);
        $this->db->where('menu.stock',1);
        $this->db->where('menu.image !=','');
        if($restaurant_id){
            $this->db->where('menu.restaurant_id',$restaurant_id);
        }
        $this->db->where('menu.language_slug',$language_slug);
        $result = $this->db->get('restaurant_menu_item as menu')->result();
        if($result)
        {
            foreach ($result as $key => $value)
            {
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
                $value->food_type_id = $food_type_id;
                $value->food_type_name = $food_type_name;
                //Code for food type section End
            }
        }

        // $menu = array();
        // foreach ($result as $key => $value) {
        //     $offer_price = '';
        //     if(in_array($value->menu_id,$ItemDiscount)){
        //         if(!empty($couponAmount)){
        //             if($couponAmount[0]['max_amount'] <= $value->price){ 
        //                 if($couponAmount[0]['amount_type'] == 'Percentage'){
        //                     $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount'])/100);
        //                 }else if($couponAmount[0]['amount_type'] == 'Amount'){
        //                     $offer_price = $value->price - $couponAmount[0]['amount'];
        //                 }
        //             }
        //         }
        //     }
        //     $offer_price = ($offer_price)?$offer_price:'';
        //     $image = ($value->image)?image_url.$value->image:'';
        //     $total = 0;
        //     if($value->check_add_ons == 1){
        //         if(!isset($menu[$value->category_id][$value->menu_id])){
        //            //$menu[$value->category_id][$value->menu_id] = array();
        //            $menu[$value->category_id][$value->menu_id] = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
        //         }
        //         if($value->is_deal == 1){
        //             if(!isset($menu[$value->category_id][$value->menu_id]['addons_category_list'])){
        //                $i = 0;
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list'] = array();
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list']['is_multiple'] = $value->is_multiple;
        //             }
        //             $menu[$value->category_id][$value->menu_id]['addons_category_list']['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name);
        //             $i++;
        //         }else{
        //             if(!isset($menu[$value->category_id][$value->menu_id]['addons_category_list'][$value->addons_category_id])){
        //                $i = 0;
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list'][] = array();
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
        //                $menu[$value->category_id][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
        //             }
        //             $menu[$value->category_id][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
        //             $i++;
        //         }
        //     }else{
        //         $menu[$value->category_id][]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'recipe_detail'=>$value->recipe_detail,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
        //     }
        // }

        // $finalArray = array();
        // $final = array();
        // $semifinal = array();
        // $new = array();
        // foreach ($menu as $nm => $va) 
        // {
        //     $final = array();
        //     foreach ($va[$value->category_id] as $kk => $popular_items) 
        //     {
        //         if(!empty($popular_items['addons_category_list']))
        //         {
        //             foreach ($popular_items['addons_category_list'] as $addons_cat_list) 
        //             {
        //                 array_push($semifinal, $addons_cat_list);
        //             }
        //             $popular_items['addons_category_list'] = $semifinal;                  
        //         }
        //         array_push($final, $popular_items);
        //     }
        //     $va[$value->category_id] = $final;
        //     array_push($finalArray, $va);
            
        // }
        // if (!empty(array_values($finalArray[0]))) {
        //     $finalA = array_values($finalArray[0]);
        //     foreach ($finalA as $key => $value) {
        //         if(isset($value['addons_category_list']){
        //             $addonarr = array_map('array_filter', $value['addons_category_list']);
        //             $finalarr = array_filter($addonarr);
        //             $finalarr = array_values($addonarr);
        //         }
        //     }
        // }
        //echo '<pre>'; print_r(array_values($finalArray)); exit;        
        return $result;     
        
    }
    
    //get resutarant review
    public function getRestaurantReview($restaurant_content_id,$user_timezone='UTC',$language_slug){
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->select("review.rating,review.review,users.first_name,users.last_name,users.image,review.created_date");
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.status',1);
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->order_by('review.created_date', 'DESC');
            $result =  $this->db->get('review')->result();        
            foreach ($result as $key => $value) { 
                $value->last_name = ($value->last_name)?$value->last_name:'';
                $value->first_name = ($value->first_name)?$value->first_name:'';
                $value->review = ($value->review)?utf8_decode($value->review):'';
                //$value->image = ($value->image)?image_url.$value->image:'';
                $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                $value->created_date = ($value->created_date)?date("d-m-Y",strtotime($value->created_date)):'';
            }
        } else {
            $result = array();
        }
        return $result;
    }
    //get event restuarant
    public function getEventRestaurant($latitude,$longitude,$searchItem,$distance,$language_slug,$count,$page_no = 1,$user_timezone='UTC',$res_type=''){
        $page_no = ($page_no > 0)?$page_no-1:0;
        if($searchItem)
        {
            $where = "(res.name LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.address LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($searchItem)."%')";

            //Query 1 code start
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,address.city,address.zipcode,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');            
            //$this->db->like('res.name',$searchItem); 
            $this->db->where('res.status',1);
            $this->db->where('res.language_slug',$language_slug);
            $this->db->where($where);
            //$this->db->where('res.allow_event_booking',1);
            $where1 = "(res.allow_event_booking = 1  OR res.enable_table_booking = 1)";            
            $this->db->where($where1);
            
            if($distance){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            
            $this->db->group_by('res.entity_id');
            $this->db->from('restaurant as res');            
            $query1 = $this->db->get_compiled_select();

            //Query 2 code start
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,address.city,address.zipcode,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id','left');
            $this->db->join('category','menu.category_id = category.entity_id','left'); 
            $this->db->where('res.status',1);
            $this->db->where($where);
            $where1 = "(res.allow_event_booking = 1  OR res.enable_table_booking = 1)";            
            $this->db->where($where1);
            if(!empty($distance)){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            
            $this->db->where('res.language_slug',$language_slug);
            $this->db->group_by('res.entity_id');
            
            $this->db->from('restaurant as res');            
            $query2 = $this->db->get_compiled_select();

            if($count)
            {
                $query = $this->db->query("(".$query1.")UNION(".$query2.") order by distance ASC limit " .$page_no*$count.",".$count);
            }
            else
            {
                $query = $this->db->query('('.$query1.')UNION('.$query2.') order by distance ASC');
            }
            $result['data'] = $query->result();
        }
        else
        {
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,CONCAT('+',COALESCE(res.phone_code,''),COALESCE(res.phone_number,'')) AS 'phone_number',res.image,address.address,address.city,address.zipcode,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code, res.branch_entity_id,res.enable_hours");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $this->db->join('restaurant_menu_item as menu','res.entity_id = menu.restaurant_id','left');
            $this->db->join('category','menu.category_id = category.entity_id','left');

            $this->db->where('res.status',1);
            $this->db->where('res.language_slug',$language_slug);
            //$this->db->where('res.allow_event_booking',1);
            $where1 = "(res.allow_event_booking = 1  OR res.enable_table_booking = 1)";            
            $this->db->where($where1);
            if($distance){
                $this->db->having('distance <=',$distance);
            }else{
                $this->db->having('distance <',NEAR_KM);
            }            

            if($count){
                $this->db->limit($count,$page_no*$count);
            }
            $this->db->group_by('res.entity_id');
            $this->db->order_by("distance", "ASC");
            $result['data'] =  $this->db->get('restaurant as res')->result();
        }
        $default_currency = get_default_system_currency();
        foreach ($result['data'] as $key => $value) {
            if(!empty($default_currency)){
                $value->currency_symbol = $default_currency->currency_symbol;
                $value->currency_code = $default_currency->currency_code;                
            }
            $timing = $value->timings;
            if($timing){
               $timing =  unserialize(html_entity_decode($timing));
               $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values) {
                    $day = date("l");
                    if($keys == strtolower($day))
                    {
                        $close = 'close';
                        if($value->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            $value->timings = $newTimingArr[strtolower($day)];
            //$value->image = ($value->image)?image_url.$value->image:'';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
            $rphone_codeval = $value->phone_number;
            if($rphone_codeval!='') {
                $rphone_codeval= str_replace("+","",$rphone_codeval);
                $rphone_codeval = '+'.$rphone_codeval;                        
            }
            $value->phone_number = $rphone_codeval;
            $chk_res_rating = $this->checkRestaurantRating($value->content_id,$language_slug);
            $value->rating = ($chk_res_rating['is_rating_from_res_form'] == '1' && $chk_res_rating['avg_rating']) ? $chk_res_rating['avg_rating'] : (($value->rating)?number_format((float)$value->rating, 1, '.', ''):null);
            $value->restaurant_review_count = $this->getRestaurantReviewCount($value->content_id, $language_slug);
            //Code for find the main restaurant name :: Start
            $rest_name = $value->name;
            /*if($value->branch_entity_id>0)
            {
                $this->db->select("entity_id, name");
                $this->db->where('language_slug',$language_slug);
                $this->db->where('status',1);
                $this->db->where('entity_id',$value->branch_entity_id);
                $rest_result =  $this->db->get('restaurant')->first_row();
                if($rest_result->name!='' && $rest_result)
                {
                    $rest_name = $rest_result->name.' ('.$value->name.')';
                }                    
            }*/
            $value->name = $rest_name;
            //Code for find the main restaurant name :: End
        }
        // usort($result['data'], function($a, $b) {
        //     return $a->timings['closing'] < $b->timings['closing'] ;
        // });
        usort($result['data'], function($a, $b) {
            if ($a->timings['closing']==$b->timings['closing']) {
                return ($a->distance>$b->distance)?1:-1;
            } else {
                return ($a->timings['closing']>$b->timings['closing'])?-1:1;
            }
        });

        //get count
        if($searchItem){
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,address.address,address.city,address.zipcode,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
            $where = "(res.name LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.address LIKE '%".$this->common_model->escapeString($searchItem)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($searchItem)."%')";
            //$where = "(res.name like '%".$searchItem."%')";
            $this->db->where($where);

        }else{
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,address.address,address.city,address.zipcode,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating, (".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
            $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
            $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        }
        $this->db->where('res.status',1);
        $this->db->where('res.language_slug',$language_slug);
        $where1 = "(res.allow_event_booking = 1  OR res.enable_table_booking = 1)";            
        $this->db->where($where1);
        if($distance){
            $this->db->having('distance <=',$distance);
        }else{
            $this->db->having('distance <',NEAR_KM);
        }        
        $this->db->group_by('res.entity_id');
        $result['count'] =  $this->db->get('restaurant as res')->num_rows();
        return $result;
    }
    // Login
    public function getLogin($password=NULL, $email=NULL, $phone=NULL, $phone_code=NULL)
    {
        if($password){
            $enc_pass  = md5(SALT.$password);
        }
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.referral_code,users.is_deleted,users.mobile_number,users.phone_code,users.earning_points,users.wallet,users.user_otp,users.image,users.notification,users.email,users.stripe_customer_id');
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
        if($password){
            $this->db->where('password',$enc_pass);
        }
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    //get rating of user
    public function getRatings($userid){
        $this->db->select('AVG(review.rating) as rating');
        $this->db->where('user_id',$userid);
        $this->db->group_by('review.user_id');
        return $this->db->get('review')->first_row();
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
                'user_type'     => 'User'
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
    public function checkEmailExist($emailID,$UserID)
    {
        $this->db->where('email',$emailID);
        $this->db->where('entity_id !=',$UserID);
        $roles = array('User','Agent');
        $this->db->where_in('user_type',$roles);
        //$this->db->where('deleteStatus',0);
        return $this->db->get('users')->num_rows();
    }
    // get config
    public function getSystemOptoin($OptionSlug)
    {        
        $this->db->select('OptionValue');                
        $this->db->where('OptionSlug',$OptionSlug);        
        return $this->db->get('system_option')->first_row();
    }
    //get record after registration
    public function getRegisterRecord($tblname,$UserID){
        $this->db->select("entity_id,first_name,last_name,mobile_number,phone_code,user_otp,referral_code,image,wallet,notification,email");
        $this->db->where('entity_id',$UserID);
        return $this->db->get($tblname)->first_row();
    }
    //check email for user edit
    public function getExistingEmail($table,$fieldName,$where,$UserID)
    {
        $this->db->where($fieldName,$where);
        $this->db->where('UserID !=',$UserID);
        return $this->db->get($table)->first_row();
    }
    //check booking availability
    public function getBookingAvailability($date,$people,$restaurant_id,$user_timezone='UTC'){
        $date = date('Y-m-d H:i:s',strtotime($date));
       // $time = date('g:i A',strtotime($date));
        $datetime = date($date,strtotime('+1 hours'));
        $this->db->select('allow_event_booking, capacity, event_online_availability, event_minimum_capacity, timings, enable_hours');
        $this->db->where('entity_id',$restaurant_id);
        $this->db->where('status',1);
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
                        $close = 'close';
                        if($capacity->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            $capacity->timings = $newTimingArr[strtolower($day)];
            $capacity->capacity = ($capacity->capacity>0 && $capacity->event_online_availability && $capacity->event_online_availability>0)?floor(($capacity->event_online_availability * $capacity->capacity)/100) : $capacity->capacity;
            //for booking
            $res_content_id = $this->getResContentId($restaurant_id);
            $this->db->select('SUM(no_of_people) as people');
            $this->db->where('booking_date',$datetime);
            $this->db->where('restaurant_id',$res_content_id);

            $event = $this->db->get('event')->first_row();
            date_default_timezone_set($user_timezone);//user time zone
            //get event booking
            if($people >= $capacity->event_minimum_capacity){
                $peopleCount = $capacity->capacity - $event->people;
                if($peopleCount >= $people){ 
                    //res capacity available
                    if($capacity->timings['off'] == 'open'){
                        if(date('H:i',strtotime($capacity->timings['close'])) < date('H:i',strtotime($capacity->timings['open']))){
                            if((date('H:i',strtotime($capacity->timings['close'])) > date('H:i',strtotime($date))) || (date('H:i',strtotime($capacity->timings['open'])) <= date('H:i',strtotime($date)))) {
                                //if close time > entered time && open time < entered time
                                return $msg = 'booking_available';
                            } else {
                                //Booking is not avilable for selected time
                                return $msg = 'booking_not_available_time';
                            }

                        }
                        else{
                            //res open for the day
                            if((date('H:i',strtotime($capacity->timings['close'])) > date('H:i',strtotime($date))) && (date('H:i',strtotime($capacity->timings['open'])) <= date('H:i',strtotime($date)))) {
                                //if close time > entered time && open time < entered time
                                return $msg = 'booking_available';
                            } else {
                                //Booking is not avilable for selected time
                                return $msg = 'booking_not_available_time';
                            }
                        }
                    } else {
                        //res closed for the day
                        //Booking is not available for selected date
                        return $msg = 'restaurant_closed';
                    }  
                } else {
                    //res capacity occupied
                    if($peopleCount == 0) {
                        $arr = array('err_msg'=>'res_is_full', 'msg'=>'booking_not_available_capacity');
                        return $arr; 
                    } else {
                        $arr = array('remaining_capacity'=>$peopleCount, 'err_msg'=>'booking_not_available_capacity', 'msg'=>'booking_not_available_capacity');
                        return $arr;
                    }
                }
            } else {
                $arr = array('minimum_capacity'=> $capacity->event_minimum_capacity, 'err_msg'=>'min_event_capacity_validation', 'msg'=>'min_capacity_validation');
                return $arr; 
            }
        }
        else
        {
            return false;
        }
    }
    //get package
    public function getPackage($restaurant_id,$language_slug){
        $this->db->select('entity_id as package_id,name,price,detail,availability,image');
        $this->db->where('restaurant_id',$restaurant_id);
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);
        $result =  $this->db->get('restaurant_package')->result();
        foreach ($result as $key => $value) {
            //$value->image = ($value->image) ? image_url.$value->image : '';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
        }
        return $result;
    }
    //get event
    public function getBooking($user_id,$user_timezone='UTC'){
        $currentDateTime = date('Y-m-d H:i:s');
        $status_array = array('completed','cancel');
        $default_currency = get_default_system_currency();
        //upcoming
        $this->db->select('event.entity_id as event_id,event.booking_date,event.event_status,event.cancel_reason,event.no_of_people,event_detail.package_detail,event_detail.restaurant_detail,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,currencies.currency_symbol,currencies.currency_code, restaurant.branch_entity_id, event.restaurant_id, event.additional_request, event.created_date');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        //$this->db->join('review','event.restaurant_id = review.restaurant_id and review.order_user_id=0','left');
        $this->db->join('restaurant','event.restaurant_id = restaurant.entity_id','left');
        $this->db->join('review','restaurant.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);
        $this->db->where('event.booking_date >=',$currentDateTime);
        $this->db->where_not_in('event.event_status',$status_array);
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $result = $this->db->get('event')->result();
        $upcoming = array();
        foreach ($result as $key => $value) {
            $package_detail = '';
            $restaurant_detail = '';
            if(!isset($value->event_id)){
                $upcoming[$value->event_id] = array();
            }
            if(isset($value->event_id)){
                $upcoming_cancel_reason = ($value->event_status == 'cancel')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';
                $package_detail = unserialize($value->package_detail);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $upcoming[$value->event_id]['entity_id'] =  $value->event_id;
                $upcoming[$value->event_id]['created_date'] = $this->common_model->getZonebaseDateMDY($value->created_date,$user_timezone);
                $upcoming[$value->event_id]['booking_date'] =  $this->common_model->getZonebaseDateMDY($value->booking_date,$user_timezone);
                $upcoming[$value->event_id]['event_status'] =  $this->lang->line(strtolower($value->event_status)).$upcoming_cancel_reason;
                $upcoming[$value->event_id]['event_status_key'] =  $value->event_status;
                $upcoming[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $upcoming[$value->event_id]['currency_code'] =  (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                $upcoming[$value->event_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;

                $upcoming[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $upcoming[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $upcoming[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';
                $upcoming[$value->event_id]['additional_request'] = ($value->additional_request)?$value->additional_request:'';

                //Code for find the main restaurant name :: Start
                $rest_name =  (!empty($restaurant_detail))?$restaurant_detail->name:'';
                /*if($value->branch_entity_id>0)
                {
                    $this->db->select("entity_id, name");
                    $this->db->where('language_slug',$this->current_lang);
                    $this->db->where('status',1);
                    $this->db->where('entity_id',$value->branch_entity_id);
                    $rest_result =  $this->db->get('restaurant')->first_row();
                    if($rest_result->name!='' && $rest_result && !empty($restaurant_detail))
                    {
                        $rest_nametemp = (!empty($restaurant_detail))?$restaurant_detail->name:'';
                        $rest_name = $rest_result->name.' ('.$rest_nametemp.')';
                    }                    
                }*/
                $upcoming[$value->event_id]['name'] =  $rest_name;
                //Code for find the main restaurant name :: End

                //$upcoming[$value->event_id]['name'] =  (!empty($restaurant_detail))?$restaurant_detail->name:'';
                $upcoming[$value->event_id]['image'] =  (!empty($restaurant_detail) && $restaurant_detail->image != '' && file_exists(FCPATH.'uploads/'.$restaurant_detail->image))?image_url.$restaurant_detail->image:'';
                $upcoming[$value->event_id]['address'] =  (!empty($restaurant_detail))?$restaurant_detail->address:'';
                //$upcoming[$value->event_id]['landmark'] =  (!empty($restaurant_detail))?$restaurant_detail->landmark:'';
                $upcoming[$value->event_id]['city'] =  (!empty($restaurant_detail))?$restaurant_detail->city:'';
                $upcoming[$value->event_id]['zipcode'] =  (!empty($restaurant_detail))?$restaurant_detail->zipcode:'';
                $upcoming[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $finalArray = array();
        foreach ($upcoming as $key => $val) {
           $finalArray[] = $val; 
        }
        $data['upcoming'] = $finalArray;
        //past
        $this->db->select('event.entity_id as event_id,event.booking_date,event.event_status,event.cancel_reason,event.no_of_people,event_detail.package_detail,event_detail.restaurant_detail,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,currencies.currency_symbol,currencies.currency_code, restaurant.branch_entity_id, event.restaurant_id, event.additional_request, event.created_date');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        //$this->db->join('review','event.restaurant_id = review.restaurant_id','left');
        $this->db->join('restaurant','event.restaurant_id = restaurant.entity_id','left');
        $this->db->join('review','restaurant.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('event.user_id',$user_id);
        //$this->db->where('event.booking_date <',$currentDateTime);
        //$this->db->where_in('event.event_status',$status_array);
        $this->db->where('(event.booking_date <', $currentDateTime)->or_where("event.event_status = 'cancel')");
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id','desc');
        $resultPast = $this->db->get('event')->result();
        $past = array();
        foreach ($resultPast as $key => $value) {
            if(!isset($value->event_id)){
                $past[$value->event_id] = array();
            }
            if(isset($value->event_id)){
                $past_cancel_reason = ($value->event_status == 'cancel')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';
                $package_detail = unserialize($value->package_detail);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $past[$value->event_id]['entity_id'] =  $value->event_id;
                $past[$value->event_id]['created_date'] = $this->common_model->getZonebaseDateMDY($value->created_date,$user_timezone);
                $past[$value->event_id]['booking_date'] =  $this->common_model->getZonebaseDateMDY($value->booking_date,$user_timezone);
                $past[$value->event_id]['event_status_key'] =  $value->event_status;
                $past[$value->event_id]['event_status'] =  $this->lang->line(strtolower($value->event_status)).$past_cancel_reason;
                $past[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $past[$value->event_id]['currency_code'] =  (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                $past[$value->event_id]['currency_symbol'] =  (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;

                $past[$value->event_id]['package_name'] =  (!empty($package_detail))?$package_detail['package_name']:'';
                $past[$value->event_id]['package_detail'] = (!empty($package_detail))?$package_detail['package_detail']:'';
                $past[$value->event_id]['package_price'] = (!empty($package_detail))?$package_detail['package_price']:'';
                $past[$value->event_id]['additional_request'] = ($value->additional_request)?$value->additional_request:'';
                //Code for find the main restaurant name :: Start
                $rest_name =  (!empty($restaurant_detail))?$restaurant_detail->name:'';
                /*if($value->branch_entity_id>0)
                {
                    $this->db->select("entity_id, name");
                    $this->db->where('language_slug',$this->current_lang);
                    $this->db->where('status',1);
                    $this->db->where('entity_id',$value->branch_entity_id);
                    $rest_result =  $this->db->get('restaurant')->first_row();
                    if($rest_result->name!='' && $rest_result && !empty($restaurant_detail))
                    {
                        $rest_nametemp = (!empty($restaurant_detail))?$restaurant_detail->name:'';
                        $rest_name = $rest_result->name.' ('.$rest_nametemp.')';
                    }                    
                }*/
                $past[$value->event_id]['name'] =  $rest_name;
                //Code for find the main restaurant name :: End
                //$past[$value->event_id]['name'] =  (!empty($restaurant_detail))?$restaurant_detail->name:'';
                $past[$value->event_id]['image'] =  (!empty($restaurant_detail) && $restaurant_detail->image != '' && file_exists(FCPATH.'uploads/'.$restaurant_detail->image))?image_url.$restaurant_detail->image:'';
                $past[$value->event_id]['address'] =  (!empty($restaurant_detail))?$restaurant_detail->address:'';
                //$past[$value->event_id]['landmark'] =  (!empty($restaurant_detail))?$restaurant_detail->landmark:'';
                $past[$value->event_id]['city'] =  (!empty($restaurant_detail))?$restaurant_detail->city:'';
                $past[$value->event_id]['zipcode'] =  (!empty($restaurant_detail))?$restaurant_detail->zipcode:'';
                $past[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
           $final[] = $val; 
        }
        $data['past'] = $final;
        return $data;
    } 
    //get recipe
    public function getRecipe($searchItem,$food,$timing,$language_slug,$restaurant_array)
    {
        $this->db->select('entity_id as item_id,name,image,recipe_detail,menu_detail,recipe_time,food_type');
        if($searchItem){
            $this->db->where("name like '%".$searchItem."%'");
        }
        // else if($food == '' && $timing == ''){
        //     $this->db->where("popular_item",1);
        //}
        if(trim($food) != '')
        {
            $foodarr = explode(",",$food);
            $foodarr = array_filter($foodarr);
            if(!empty($foodarr))
            {   
                $fdtcnt=0; $wherefindcn = '(';
                foreach($foodarr as $key=>$value) 
                { 
                    if($fdtcnt>0){
                        $wherefindcn .= " OR ";
                    }
                    $wherefindcn .= "(find_in_set ($value, restaurant_menu_item.food_type))";

                    $fdtcnt++;
                }
                $wherefindcn .= ')';
                //$where = "(res.name like '%".$searchItem."%')";
                if($fdtcnt>0)
                { $this->db->where($wherefindcn);  }
            }
        }
        //New code for search food type End
        if($timing){
            $this->db->where('recipe_time <=',$timing);
        }
        if(!empty($restaurant_array)){
            $this->db->where_in('restaurant_id',$restaurant_array);
        }
        $this->db->where('language_slug',$language_slug);
        $result =  $this->db->get('restaurant_menu_item')->result();
        foreach ($result as $key => $value)
        {
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
            
            $value->food_type_id = $food_type_id;
            $value->food_type_name = $food_type_name;

           //$value->image = ($value->image)?image_url.$value->image:'';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
        }
        return $result;
    } 
    //check if item exist
    public function checkExist($item_id, $allow_scheduled_delivery = 0)
    {
        $this->db->select('menu.price,menu.image,menu.name,menu.food_type,menu.menu_detail,menu.is_combo_item,menu.stock');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->where('menu.entity_id',$item_id);
        $this->db->where('menu.status',1);
        $this->db->where('c.status',1);
        if($allow_scheduled_delivery == 0) {
            $this->db->where('menu.stock',1);
        }
        return $this->db->get('restaurant_menu_item as menu')->first_row();
    } 
    //get tax
    public function getRestaurantTax($tblname,$restaurant_id,$flag,$user_timezone='UTC'){
        if($flag == 'order'){
            $this->db->select('restaurant.entity_id,restaurant.content_id,restaurant.name,restaurant.image,restaurant.phone_number,restaurant.phone_code,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code, restaurant.service_fee, restaurant.service_fee_type, restaurant.is_service_fee_enable, restaurant.creditcard_fee_type, restaurant.creditcard_fee, restaurant.is_creditcard_fee_enable, restaurant.allow_scheduled_delivery, restaurant.timings, restaurant.enable_hours');
            $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
            $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        }
        else
        {
            $this->db->select('restaurant.entity_id,restaurant.content_id,restaurant.name,restaurant.image,restaurant_address.address,restaurant_address.zipcode,restaurant_address.city,restaurant.amount_type,restaurant.amount,restaurant_address.latitude,restaurant_address.longitude,  restaurant.service_fee, restaurant.service_fee_type, restaurant.is_service_fee_enable, restaurant.creditcard_fee_type, restaurant.creditcard_fee, restaurant.is_creditcard_fee_enable, restaurant.allow_scheduled_delivery, restaurant.timings, restaurant.enable_hours');
            $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
            $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        }
        $this->db->where('restaurant.entity_id',$restaurant_id);
        $result = $this->db->get($tblname)->first_row();
        if(!empty($result)){
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)){
                $result->currency_symbol = $default_currency->currency_symbol;
                $result->currency_code = $default_currency->currency_code;
            }
            $timing = $result->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values){
                    $day = date("l");
                    
                    if($keys == strtolower($day))
                    {
                        $close = 'close';
                        if($result->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['current_day'] = strtolower($day);
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            $result->timings = $newTimingArr[strtolower($day)];
        }
        return $result;
    }
    //get address
    public function getAddress($tblname,$fieldName,$user_id,$showonly_main='')
    {
        $this->db->select('entity_id as address_id,address_label,address,landmark,latitude,longitude,city,country,state,zipcode,is_main');
        $this->db->where($fieldName,$user_id);
        if($showonly_main=='1')
        {
            $this->db->where('is_main',1);
        }
        $this->db->order_by('is_main','desc');
        $this->db->order_by('entity_id','desc');
        return $this->db->get($tblname)->result();
    }
    //get order detail
    public function getOrderDetail($flag,$user_id,$language_slug,$count,$page_no = 1,$user_timezone='UTC'){
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,users.driver_temperature,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant.timings,restaurant.image as restaurant_image,currencies.currency_symbol,currencies.currency_code,currencies.currency_id,restaurant.content_id,restaurant.status as restaurant_status,restaurant.enable_hours,tips.amount as driver_tip,tips.tip_percentage, review.rating, review.review, d_review.rating as driver_rating, d_review.review as driver_review,tips.tips_transaction_id,tips.refund_status as tips_refund_status,tips.refund_reason as tips_refund_reason,restaurant.allow_scheduled_delivery,order_master.refunded_amount');

        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('review as d_review','d_review.restaurant_content_id = restaurant.content_id and d_review.order_user_id=order_driver_map.driver_id and d_review.order_id = order_master.entity_id','left');
        $this->db->join('review','review.restaurant_content_id = restaurant.content_id and (review.order_user_id=0 OR review.order_user_id=NULL) and review.order_id = order_master.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $status_arr = array('delivered','cancel','rejected','complete');
        if($flag == 'process'){
            $this->db->where_not_in('order_master.order_status',$status_arr);
        } 
        if($flag == 'past'){
            $this->db->where_in('order_master.order_status',$status_arr);
        }
        $this->db->where('order_master.user_id',$user_id);
        $this->db->order_by('order_master.entity_id','desc');       
        $this->db->group_by(array("order_master.entity_id", "status.order_status"));

        /*if($flag == 'past'){
            $this->db->group_by('order_master.entity_id');
            $this->db->limit($count,$page_no*$count);
        }*/
        
        $result =  $this->db->get('order_master')->result();
        $items = array();
        $default_currency = get_default_system_currency();
        $this->db->select('OptionValue');
        $enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
        $show_restaurant_reviews = ($enable_review->OptionValue == '1')?true:false;
        foreach ($result as $key => $value) {
            /*$currency_symbol = $this->common_model->getCurrencySymbol($value->currency_id);*/
            
            if(!isset($items[$value->order_id])){
                $items[$value->order_id] = array();
                //$items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
                $items[$value->order_id]['ready'] = '';
                $items[$value->order_id]['complete'] = '';
            }
            if(isset($items[$value->order_id])) 
            {
               //Code chage as per requireed :: Start
                $percent_text = ''; $text_amount = 0;
                if($value->tax_rate && $value->tax_rate !='')
                {
                    $type = ($value->tax_type == 'Percentage')?'%':'';
                    $percent_text = ($value->tax_type == 'Percentage')?' ('.$value->tax_rate.$type.')':'';
                    if($value->tax_type == 'Percentage'){
                        $text_amount = round(($value->subtotal * $value->tax_rate) / 100,2);
                    }else{
                        $text_amount = $value->tax_rate; 
                    }
                }
                //Code chage as per requireed :: End

                //Code for table no :: Start
                $table_number = '';
                if($value->table_id!='')
                {
                    $this->db->select('table_number');
                    $this->db->where('entity_id',$value->table_id);
                    $tablearr =  $this->db->get('table_master')->first_row();
                    $table_number = $tablearr->table_number;                                     
                }
                //Code for table no :: End

                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['table_id'] = $value->table_id;
                $items[$value->order_id]['table_number'] = $table_number;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                $items[$value->order_id]['restaurant_content_id'] = $value->content_id;
                $items[$value->order_id]['show_restaurant_reviews'] = $show_restaurant_reviews;
                //Code for review and rating :: Start
                $items[$value->order_id]['rating'] = ($value->rating)?round($value->rating,2):'';
                $items[$value->order_id]['review'] = utf8_decode($value->review);
                $items[$value->order_id]['driver_rating'] = ($value->driver_rating)?round($value->driver_rating,2):'';
                $items[$value->order_id]['driver_review'] = utf8_decode($value->driver_review);
                //Code for review and rating :: End

                $items[$value->order_id]['order_accepted'] = ($value->status == 1)?1:0;
                $items[$value->order_id]['accept_order_time'] = $this->common_model->getZonebaseTime($value->accept_order_time,$user_timezone);
                $restaurant_detail = unserialize($value->restaurant_detail);

                //Code for find the main restaurant name :: Start
                $rest_name = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                $rest_phone_number = '';
                $rest_data = $this->db->select('phone_number,phone_code')->where('entity_id',$value->restaurant_id)->get('restaurant')->first_row();
                $rest_phone_number = $rest_data->phone_number;
                $rest_phone_code = $rest_data->phone_code;
                /*if($value->branch_entity_id>0)
                {
                    $this->db->select("entity_id,name,phone_number,phone_code");
                    $this->db->where('language_slug',$this->current_lang);
                    $this->db->where('status',1);
                    $this->db->where('entity_id',$value->branch_entity_id);
                    $rest_result =  $this->db->get('restaurant')->first_row();
                    if($rest_result->name!='' && $rest_result && isset($value->restaurant_name))
                    {
                        $rest_nametemp = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                        $rest_name = $rest_result->name.' ('.$rest_nametemp.')';
                    }
                    $rest_phone_number = $rest_result->phone_number;
                    $rest_phone_code = $rest_result->phone_code;
                }*/
                if($rest_phone_code!='')
                {
                    $rest_phone_code= str_replace("+","",$rest_phone_code);
                    $rest_phone_code = '+'.$rest_phone_code;                        
                }

                $items[$value->order_id]['restaurant_name'] = $rest_name;
                $items[$value->order_id]['restaurant_phone_number'] = $rest_phone_code.$rest_phone_number;
                $items[$value->order_id]['restaurant_image'] = (file_exists(FCPATH.'uploads/'.$value->restaurant_image) && $value->restaurant_image!='')?image_url.$value->restaurant_image:'';
                //$items[$value->order_id]['restaurant_name'] = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                //Code for find the main restaurant name :: End
                $items[$value->order_id]['restaurant_address'] = (isset($restaurant_detail->address))?$restaurant_detail->address:'';
                $items[$value->order_id]['restaurant_status'] = ($value->restaurant_status)?$value->restaurant_status:'';

                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }

                //Code chage as per requireed :: Start //Code for service fee :: Start
                $service_type = ''; $service_rate = 0;
                if($value->service_fee && $value->service_fee  !='')
                {
                    $s_type = ($value->service_fee_type == 'Percentage')?'%':'';
                    $service_type = ($value->service_fee_type == 'Percentage')?' ('.$value->service_fee.$s_type.')':'';
                    if($value->service_fee_type == 'Percentage'){
                        $service_rate = round(($value->subtotal * $value->service_fee) / 100,2);
                    }else{
                        $service_rate = $value->service_fee; 
                    }
                }
                //Code chage as per requireed :: End //Code for service fee :: End

                //Code for creditcard fee :: Start
                $creditcard_type = ''; $creditcard_rate = 0;
                if($value->creditcard_fee && $value->creditcard_fee  !='')
                {
                    $s_type = ($value->creditcard_fee_type == 'Percentage')?'%':'';
                    $creditcard_type = ($value->creditcard_fee_type == 'Percentage')?' ('.$value->creditcard_fee.$s_type.')':'';
                    if($value->creditcard_fee_type == 'Percentage'){
                        $creditcard_rate = round(($value->subtotal * $value->creditcard_fee) / 100,2);
                    }else{
                        $creditcard_rate = $value->creditcard_fee; 
                    }
                }
                //Code for creditcard fee :: End

                /*wallet money changes start*/
                $wallet_history = $this->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'debit' => 1));
                $wallet = ($wallet_history)?array('label'=>$this->lang->line('wallet_discount'),'value'=>$wallet_history->amount, 'label_key'=>'Wallet Discount'):'';
                /*wallet money changes end*/
                $text_amount = 0;
                if($value->tax_type == 'Percentage'){
                    $text_amount = ($value->subtotal * $value->tax_rate) / 100;
                }else{
                    $text_amount = $value->tax_rate; 
                }

                $percent_text = ($value->tax_type == 'Percentage')?' ('.$value->tax_rate.$type.')':'';
                $tip_percent_txt = ($value->tip_percentage)?' ('. $value->tip_percentage.'%)':'';
                //Code for multiple coupon :: Start
                $coupon_array = $this->common_model->getCoupon_array($value->order_id);
                $items[$value->order_id]['price'] = array();
                if(!empty($coupon_array))
                {
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee");
                    if($value->transaction_id!='' && $value->transaction_id!=null)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee");
                    }
                    if(!empty($coupon_array))
                    {
                        foreach($coupon_array as $cp_key => $cp_value){                        
                            $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('discount').'('.$cp_value['coupon_name'].')','value'=>abs($cp_value['coupon_discount']),'label_key'=>"Discount");
                        }
                    }
                    if($value->delivery_charge)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge");
                    }
                    if(!empty($wallet))
                    {
                        $items[$value->order_id]['price'][] = $wallet;
                    }
                    if($value->driver_tip)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip");
                    }
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total");
                }//End
                else if($discount)
                {
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                    array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                    ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'',
                    $discount,
                    ($value->delivery_charge)?array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"):'',
                    // array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                    $wallet,
                    ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                else
                {
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                    array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                    ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'', 
                    ($value->delivery_charge)?array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"):'',
                    // array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                    $wallet,
                    ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
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
                            $close = 'close';
                            if($value->enable_hours=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'close';
                                if (!empty($values['open']) && !empty($values['close'])) {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                                }
                                $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }


                if($value->order_status == "placed" && $value->status!='1')
                {
                    $order_statusval = 'placed';
                }
                else if(($value->order_status == "placed" && $value->status=='1') || $value->order_status == "accepted")
                {
                    $order_statusval = 'accepted';
                }
                else if($value->order_status == "rejected")
                {
                    $order_statusval = 'rejected';
                }
                else if($value->order_status == "ready")
                {
                    $order_statusval = 'served';
                }
                else
                {
                    $order_statusval = ucfirst($value->order_status);
                }

                $items[$value->order_id]['order_status_display'] = (strtolower($order_statusval) == "cancel") ? $this->lang->line('cancelled') : ((strtolower($order_statusval) == "complete")?$this->lang->line('completed'):$this->lang->line(strtolower($order_statusval)));
                if((strtolower($value->order_delivery)=='pickup' || strtolower($value->order_delivery)=='dinein') && $value->order_status == "onGoing")
                {
                    // $order_notification_slug = 'order_is_readynoti';
                    $items[$value->order_id]['order_status_display'] = $this->lang->line('order_ready');
                }
                if(strtolower($value->order_delivery)=='delivery' && $value->order_status == "onGoing")
                {
                    $items[$value->order_id]['order_status_display'] = $this->lang->line('onGoing');
                }
                if(strtolower($value->order_delivery)=='pickup' && $value->order_status == "ready") {
                    $items[$value->order_id]['order_status_display'] = $this->lang->line('order_ready');
                }
                if($order_statusval == "served")
                {
                    $order_statusval = 'ready';
                }                
                $items[$value->order_id]['order_status'] = ucfirst($order_statusval);
                $items[$value->order_id]['cancel_reason'] = (!empty($value->cancel_reason)) ? $value->cancel_reason : '';
                $items[$value->order_id]['reject_reason'] = (!empty($value->reject_reason)) ? $value->reject_reason : '';
                // $items[$value->order_id]['order_status'] = ucfirst($order_statusval);
                $items[$value->order_id]['payment_status'] = $this->lang->line($value->payment_status);
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['delivery_instructions'] =$value->delivery_instructions;

                //show cancel button :: start
                $created_date  = strtotime($value->created_date);
                $current_time = strtotime("now");
                $differenceInSeconds = $current_time - $created_date;
                //Code for time check from system option :: Start
                $cancel_order_timerarr = $this->db->get_where('system_option',array('OptionSlug'=>'cancel_order_timer'))->first_row();
                $cancel_order_timeval = ($cancel_order_timerarr->OptionValue)?$cancel_order_timerarr->OptionValue:180;                
                //Code for time check from system option :: End
                $remaining_time = $cancel_order_timeval - $differenceInSeconds;
                $items[$value->order_id]['show_cancel_order'] = ($remaining_time > 0)?'1':'0';
                $items[$value->order_id]['remaining_time'] = $remaining_time;
                //show cancel button :: end
                
                $items[$value->order_id]['placed'] = $this->common_model->getZonebaseTime($value->order_date,$user_timezone);;
                // if($value->ostatus == 'preparing')
                // {
                //     $items[$value->order_id]['preparing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                // }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }                
                if($value->ostatus == 'ready' || ($value->ostatus == 'onGoing' && strtolower($value->order_delivery)=='dinein'))
                {
                    $items[$value->order_id]['ready'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                if($value->ostatus == 'complete')
                {
                    $items[$value->order_id]['complete'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }

                $items[$value->order_id]['order_date'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);


                $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_open_time"));
                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                $order_scheduled_date = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
                $order_slot_open_time = ($value->slot_open_time) ? date('g:i A', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
                $order_slot_close_time = ($value->slot_close_time) ? date('g:i A', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime,$user_timezone))) : NULL;

                $items[$value->order_id]['scheduled_date'] = ($value->scheduled_date) ? $order_scheduled_date : '';
                $items[$value->order_id]['slot_open_time'] = ($value->slot_open_time) ? $order_slot_open_time : '';
                $items[$value->order_id]['slot_close_time'] = ($value->slot_close_time) ? $order_slot_close_time : '';

                $items[$value->order_id]['order_timestamp'] = $value->order_timestamp;
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $customization = array();
                        $this->db->select('image,food_type,status,stock');
                        $this->db->where('entity_id',$valuee['item_id']);
                        $data = $this->db->get('restaurant_menu_item')->first_row();
                        $valueee = array();
                        
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';                        
                        $data1['food_type'] = (!empty($data) && $data->food_type != '')?$data->food_type:'';
                        //$valueee['image'] = (!empty($data) && $data->image != '')?image_url.$data1['image']:'';
                        $valueee['image'] = (file_exists(FCPATH.'uploads/'.$data->image) && $data->image!='')?image_url.$data1['image']:'';
                        //$valueee['is_veg'] = (!empty($data) && $data->is_veg != '')?$data1['is_veg']:'';
                        $is_vegtemp = (!empty($data) && $data->food_type != '')?$data1['food_type']:'';

                        //Code for food type :: start
                        $food_type_id = ''; $food_type_name = '';
                        if($is_vegtemp!='')
                        {
                            $is_vegarr = explode(",", $is_vegtemp);
                            $this->db->select('entity_id as food_type_id, name as food_type_name');
                            $this->db->where_in('entity_id',$is_vegarr);
                            $resfood_type = $this->db->get('food_type')->result();
                            if($resfood_type && count($resfood_type)>0)
                            {
                                $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                                $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                            }
                        }
                        $valueee['food_type_id'] = $food_type_id;
                        $valueee['food_type_name'] = $food_type_name;
                        //Code for food type :: end
                        
                        if($valuee['is_customize'] == 1)
                        {
                            foreach ($valuee['addons_category_list'] as $k => $val)
                            {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if($valuee['is_deal'] == 1){
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
                        }
                        else
                        {
                            $customization = [];
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['menu_content_id'] = $valuee['menu_content_id'] ? $valuee['menu_content_id'] : '';
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['comment'] = $valuee['comment'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['in_stock'] = (!empty($data) && $data->stock != '')?$data->stock:0;
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_combo_item'] = $valuee['is_combo_item'];
                        $valueee['combo_item_details'] = $valuee['combo_item_details'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                        
                        if(!empty($customization)){
                            $valueee['addons_category_list'] = $customization;
                        }
                        $value1[] =  $valueee;
                    } 
                }

                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                // $items[$value->order_id]['resLat'] = $restaurant_detail->latitude;
                // $items[$value->order_id]['resLong'] = $restaurant_detail->longitude;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['transaction_id']  = $value->transaction_id;
                $items[$value->order_id]['is_parcel_order']  = $value->is_parcel_order;
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
                $items[$value->order_id]['payment_option']  = $payment_option_val;
                $items[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver_phone_code =  $value->phone_code;
                    if($driver_phone_code!='')
                    {
                        $driver_phone_code= str_replace("+","",$driver_phone_code);
                        $driver_phone_code = '+'.$driver_phone_code;                        
                    }
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $driver_phone_code.$value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    //$driver['image'] = ($value->image)?image_url.$value->image:'';
                    $driver['image'] = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $driver['driver_temperature'] = ($value->driver_temperature)?$value->driver_temperature : '';
                    $items[$value->order_id]['driver'] = $driver;
                }
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery)?strtolower($value->order_delivery):'pickup';
                $items[$value->order_id]['allow_scheduled_delivery'] = ($value->allow_scheduled_delivery)?$value->allow_scheduled_delivery:'0';
                $items[$value->order_id]['paid_status'] = $value->paid_status;
                // $items[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                // $items[$value->order_id]['currency_code'] = $value->currency_code;
                $items[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $restaurant_detail->currency_symbol;
                $items[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $restaurant_detail->currency_code;
                $items[$value->order_id]['SECONDS_TO_CANCEL'] = SECONDS_TO_CANCEL;
                $items[$value->order_id]['delivery_method'] = $value->delivery_method;
                $items[$value->order_id]['third_party_tracking_url'] = $value->delivery_tracking_url;
                if($value->delivery_method == 'doordash'){
                    $doordash_driver_details = $this->common_model->getDoordashDriver($value->order_id);
                    if($doordash_driver_details) {
                        $items[$value->order_id]['thirdparty_driver_details'] = $doordash_driver_details;
                    }
                }
                $items[$value->order_id]['refund_status'] = $value->refund_status;
                $items[$value->order_id]['refund_reason'] = str_replace("<br>", ", ", str_replace("<br/>", ", ", $value->refund_reason));
                if($value->refund_status=='refunded'){
                    $items[$value->order_id]['refund_flag'] = true;
                }else{
                    $items[$value->order_id]['refund_flag'] = false;
                }
                if($value->tips_transaction_id!=''){
                    $items[$value->order_id]['tips_refund_status'] = $value->tips_refund_status;
                    $items[$value->order_id]['tips_refund_reason'] = $value->tips_refund_reason;
                    if($value->tips_refund_status=='refunded'){
                        $items[$value->order_id]['tips_refund_flag'] = true;
                    }
                    else{
                        $items[$value->order_id]['tips_refund_flag'] = false;
                    }
                }
                $drivertip_val = ($value->driver_tip)?(float)$value->driver_tip:0;
                $tippercent_val = ($value->tip_percentage)?(float)$value->tip_percentage:0;
                $items[$value->order_id]['is_tip_paid'] = ($drivertip_val > 0)?true:false;
                $items[$value->order_id]['driver_tip'] = ($drivertip_val > 0)?$drivertip_val:null;
                $items[$value->order_id]['tip_percent_val'] = ($tippercent_val > 0)?$tippercent_val:null;
                $items[$value->order_id]['driver_tiparr'] = get_driver_tip_amount();
                if(!$items[$value->order_id]['is_tip_paid'] && $flag == 'past' && !empty($items[$value->order_id]['driver_tiparr'])) {
                    $tip_percent_val_arr = array();
                    foreach ($items[$value->order_id]['driver_tiparr'] as $tipkey => $tipvalue) {
                        $tip_percent_val_arr[$tipkey]['percentage'] = $tipvalue;
                        $tip_calculation = ($value->subtotal * (float)$tipvalue)/100;
                        $tip_percent_val_arr[$tipkey]['value'] = $this->common_model->roundDriverTip((float)$tip_calculation);
                    }
                    if(!empty($tip_percent_val_arr)) {
                        unset($items[$value->order_id]['driver_tiparr']);
                        $items[$value->order_id]['driver_tiparr'] = $tip_percent_val_arr;    
                    }
                }
                $items[$value->order_id]['refunded_amount'] = ($value->refunded_amount)?$value->refunded_amount:'0';
                $items[$value->order_id]['default_tip_percent_val'] = get_default_driver_tip_amount();
                $items[$value->order_id]['shouldShowTipButton'] = ($drivertip_val == 0 && $items[$value->order_id]['delivery_flag'] == "delivery" && (strtolower($items[$value->order_id]['order_status'])=='delivered' || strtolower($items[$value->order_id]['order_status'])=='complete') && $value->refund_status!='refunded') ? true : false;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        /*if($flag == 'process'){
            $res['in_process'] = $finalArray;
        }
        if($flag == 'past'){
            $res['past'] = $finalArray;
        }*/
        return $finalArray;
    }
    //check coupon
    public function checkCoupon($coupon){
        $this->db->where('name',$coupon);
        $this->db->where('status',1);
        return $this->db->get('coupon')->first_row();
    }
    public function checkCoupon_dinein($coupon,$subtotal,$restaurant_id)
    {
        $this->db->like('name',$coupon);
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
        $this->db->where('coupon.max_amount <=',$subtotal);
        $this->db->where('coupon_type','dine_in');
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        $this->db->where('status',1);
        return $this->db->get('coupon')->first_row();
    }
    public function getResContentId($restaurant_id){
        $this->db->select('content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        return $result->content_id;
    }
    //get coupon list
    public function getcouponList($subtotal,$restaurant_id,$order_delivery,$user_timezone='UTC',$user_id='',$isLoggedIn,$used_couponarr=[])
    {
        //Code to check the user is new or not :: Start
        $user_chkcpn = 'yes';
        if($user_id!='')
        {            
            $this->db->select('entity_id');
            $this->db->where('user_id',$user_id);
            $user_chk = $this->db->count_all_results('order_master');
            if($user_chk>0)
            {
                $user_chkcpn = 'no';
            }
        }
        if($isLoggedIn!=1){
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
        $this->db->where('coupon.max_amount <=',$subtotal);
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        if($user_chkcpn=='no') {
             $this->db->where('coupon_type !=' , 'user_registration');
        }
        $this->db->where('(coupon_type != "discount_on_items")');
        $this->db->where('(coupon_type != "discount_on_categories")');
        //Code for used coupon not display
        if(!empty($used_couponarr))
        {
            $this->db->where_not_in('coupon.entity_id',$used_couponarr);
        }//End
        if($order_delivery == 'Delivery') {
            $this->db->where('coupon_type !=' , 'dine_in');
            //$this->db->or_where('coupon.coupon_type',"free_delivery");
            if($user_chkcpn=='no') {
                $coupontype_arr = array('free_delivery','discount_on_cart');
            }
            else
            {
                $coupontype_arr = array('free_delivery','discount_on_cart','user_registration');
            }
            $this->db->where_in('coupon_type',$coupontype_arr);
            $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
            $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
            $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));

        } else if($order_delivery == 'DineIn') {
            $this->db->where('coupon.coupon_type != "free_delivery"');
            //$this->db->or_where('coupon.coupon_type',"dine_in");
            $coupontype_arr = array('dine_in');
            $this->db->where_in('coupon_type',$coupontype_arr);
            $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_id);
            $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
            $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
            
        } else {
            $this->db->where('coupon.coupon_type != "free_delivery"');
            $this->db->where('coupon_type!=',"dine_in");            
        }
        
        $this->db->where('coupon.status',1);
        $this->db->group_by('coupon.entity_id');
        $result = $this->db->get('coupon')->result();
        $default_currency = get_default_system_currency();
        if(!empty($default_currency)){
            foreach ($result as $key => $value) {
                $value->currency_symbol = $default_currency->currency_symbol;
                $value->currency_code = $default_currency->currency_code;
            }
        }        
        return $result;
    }
    //get notification
    public function getNotification($user_id,$count,$page_no = 1){
        $page_no = ($page_no > 0)?$page_no-1:0;
        $this->db->select('notifications.notification_title,notifications.notification_description,notifications_users.notification_id');
        $this->db->join('notifications','notifications_users.notification_id =  notifications.entity_id','left');
        $this->db->limit($count,$page_no*$count);
        $this->db->where('notifications_users.user_id',$user_id);
        $data['result'] =  $this->db->get('notifications_users')->result();
        foreach ($data['result'] as $key => $value) {
            $data['result'][$key]->notification_title = utf8_decode($value->notification_title);
            $data['result'][$key]->notification_description = utf8_decode($value->notification_description);
        }

        $this->db->select('notifications.notification_title,notifications.notification_description,notifications_users.notification_id');
        $this->db->join('notifications','notifications_users.notification_id =  notifications.entity_id','left');
        $this->db->where('notifications_users.user_id',$user_id);
        $data['count'] =  $this->db->count_all_results('notifications_users');
        return $data;
    }
    //check delivery is available
    public function checkOrderDelivery($users_latitude,$users_longitude,$user_id,$restaurant_id,$request,$order_id,$user_km=NULL,$driver_km=NULL){ 
        $this->db->select('users.entity_id');
        $this->db->where('user_type','Driver');
        $this->db->where('users.status',1);
        $driver = $this->db->get('users')->result_array();
        //Old query Hide :: 12-11-2020
        /*$this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
        $this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
        $this->db->where('users.status',1);
        $this->db->where('driver_traking_map.created_date = (SELECT
            driver_traking_map.created_date
        FROM
            driver_traking_map
        WHERE
            driver_traking_map.driver_id = users.entity_id
        ORDER BY
            driver_traking_map.created_date desc
        LIMIT 1)');
        if(!empty($driver)){
            $this->db->where_in('driver_id',array_column($driver, 'entity_id'));
        }
        $detail = $this->db->get('driver_traking_map')->result();*/

        //New update query on 12-11-2020
        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
        //$this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->where('users.status',1);
        $this->db->where('driver_traking_map.latitude != ', '');
        $this->db->where('driver_traking_map.longitude != ', '');
        if(!empty($driver)){
            $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'entity_id'));
        }
        $this->db->group_by('driver_traking_map.driver_id');
        $this->db->order_by('driver_traking_map.created_date','desc');

        $detail = $this->db->get('users')->result();

        $flag = false;  
        if(!empty($detail)){
            foreach ($detail as $key => $value) {
                $longitude = $value->longitude;
                $latitude = $value->latitude;
                $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                $this->db->where('restaurant.entity_id',$restaurant_id);
                /*if (!empty($driver_km)) {
                    $this->db->having('distance <',$driver_km);
                }
                else
                {
                    $this->db->having('distance <',DRIVER_NEAR_KM);
                }*/
                $result = $this->db->get('restaurant')->result();
                if($request == 1){
                    if(!empty($result)){
                        if($value->device_id){ 
                            $flag = true;   
                            //get langauge
                            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);
                            
                            $array = array(
                                'order_id'=>$order_id,
                                'driver_id'=>$value->driver_id,
                                'date'=>date('Y-m-d H:i:s')
                            );
                            $id = $this->addRecord('order_driver_map',$array);
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('push_new_order'),$order_id);
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
                if($request == ''){
                    if(!empty($result)){
                        if($value->device_id){ 
                            $flag = true;
                        }
                    }
                }
            }
        }
            
        
        if($flag == false && $request == 1){
            return true;
        }
        if($flag == true && $request == ''){
            return true;
        }
    }
    // check restaurant availability
    public function checkRestaurantAvailability($users_latitude,$users_longitude,$restaurant_id,$request,$order_id,$user_km=NULL,$driver_km=NULL){
        $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($users_latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($users_longitude) ) + sin ( radians($users_latitude) ) * sin( radians( address.latitude )))) as distance");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->where('restaurant.entity_id',$restaurant_id);
        $user_result = $this->db->get('restaurant')->result();
        if (!empty($user_result)) {
            if (!empty($user_km)) {
                if ($user_result[0]->distance <= $user_km ) {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                if ($user_result[0]->distance <= USER_NEAR_KM ) {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }
    //get driver location for traking
    public function getdriverTracking($order_id,$user_id){
        $this->db->select('order_driver_map.order_id,order_master.total_rate,order_master.order_status,driver_traking_map.latitude as driverLatitude,driver_traking_map.longitude as driverLongitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,user_address.latitude as userLat,user_address.longitude as userLong,user_address.address,user_address.landmark,user_address.zipcode,user_address.state,user_address.city,driver.first_name,driver.last_name,driver.image,driver.mobile_number');
        $this->db->join('order_driver_map','driver_traking_map.driver_id = order_driver_map.driver_id','left');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        $this->db->order_by('driver_traking_map.traking_id','desc');
        $detail = $this->db->get('driver_traking_map')->first_row();
        if(!empty($detail)){
            //$detail->image = ($detail->image )?$detail->image :'';
            $detail->image = (file_exists(FCPATH.'uploads/'.$detail->image) && $detail->image!='') ? image_url.$detail->image : '';
        }
        return $detail;
    }
    //get addos data
    public function getAddonsPrice($add_ons_id){
        $this->db->where('add_ons_id',$add_ons_id);
        return $this->db->get('add_ons_master')->first_row();
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
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id',$UserID);
        return $this->db->get('order_master')->num_rows();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($tblname,$fldname,$id)
    {
        $this->db->where($fldname,$id);
        return $this->db->get($tblname)->result();
    }
    // get restaurant currency
    public function getRestaurantCurrency($restaurant_id)
    {
        $this->db->select('currencies.currency_code,currencies.currency_symbol');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        if(!empty($result)){
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)){
                $result->currency_symbol = $default_currency->currency_symbol;
                $result->currency_code = $default_currency->currency_code;
            }
        }
        return $result;
    }
    // method to get details by id :: for invoice
    public function getEditDetail($entity_id)
    {
        $this->db->select('order.*,res.name,res.phone_code as r_phone_code,res.phone_number as r_phone_number,res.is_printer_available,res.printer_paper_width,res.printer_paper_height,address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode,tb.table_number,tips.amount as tip_amount,tips.tips_transaction_id,tips.refund_status as tips_refund_status');
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
    public function openclose($start_time,$end_time)
    {
        $current_time = date('h:i a');
        $start_time = date('h:i a', strtotime($start_time));
        $end_time = date('h:i a', strtotime($end_time));

        $date1 = DateTime::createFromFormat('H:i a', $current_time)->getTimestamp(); 
        $date2 = DateTime::createFromFormat('H:i a', $start_time)->getTimestamp();; 
        $date3 = DateTime::createFromFormat('H:i a', $end_time)->getTimestamp(); 
        if ($date3 < $date2) { 
            $date3 += 24 * 3600; 
            if ($date1 < $date2) { 
                $date1 += 24 *3600; 
            } 
        } 
        if ($date1 > $date2 && $date1 < $date3) { 
            return 'open'; 
        } else { 
            return 'close'; 
        }
    }
    public function getRange() {
        $arr = array('minimum_range','maximum_range');
        $this->db->where_in('OptionSlug',$arr);
        $this->db->order_by('OptionValue','asc');
        return $this->db->get('system_option')->result();
    } 
    public function getStoreUrl() {
        $arr = array('app_store_url','playstore_url');
        $this->db->where_in('OptionSlug',$arr);
        return $this->db->get('system_option')->result();
    }
    //Code aadd to find the branch admin user device id :: Start :: 12-10-2020
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
            $result = $this->db->get('restaurant')->result(); 
            return $result;
        }
        return false;
    }
    //Code aadd to find the branch admin user device id :: End :: 12-10-2020
    //get food_type
    public function getFoodType($language_slug, $food_type = array()) {
        $food_type_arr = array();
        $food_type_arrtemp = array();
        if(!empty($food_type)) {
            foreach ($food_type as $ft_value) {
                array_push($food_type_arrtemp, array_unique(explode(',', $ft_value)));
            }
            $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($food_type_arrtemp));
            foreach($it as $v) {
                array_push($food_type_arr, $v);
            }
        }
        $this->db->select('name, entity_id as food_type_id, content_id,food_type.food_type_image');
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);
        if(!empty($food_type_arr)) {
            $this->db->where_in('food_type.entity_id',array_unique($food_type_arr));
        }
        $this->db->order_by('entity_id','desc');
        $this->db->group_by('food_type.entity_id');
        //$this->db->limit(4, 0);
        $result =  $this->db->get('food_type')->result();
        foreach ($result as $key => $value) {
            $value->food_type_image = (file_exists(FCPATH.'uploads/'.$value->food_type_image) && $value->food_type_image != '') ? image_url.$value->food_type_image : '';
        }
        return $result;
    }

    public function get_recipes($recipe_name,$language_slug, $count = 10, $page_no = 1,$food='' ,$timing,$user_timezone='UTC')
    {
        $page_no = ($page_no > 0) ? $page_no-1 : 0;
        //Code for record :: Start
        $this->db->select('entity_id, name, detail, slug, image, ingredients, recipe_detail, recipe_time, language_slug, status, content_id, food_type, youtube_video');
        if($recipe_name){
            $where = "(name like '%".str_replace('\'', '', $recipe_name)."%')";
            $this->db->where($where);
        }
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);        
        //New code for search food type Start
        if(trim($food) != '')
        {
            $foodarr = explode(",",$food);
            $foodarr = array_filter($foodarr);
            if(!empty($foodarr))
            {   
                $fdtcnt=0; $wherefindcn = '(';
                foreach($foodarr as $key=>$value) 
                { 
                    if($fdtcnt>0){
                        $wherefindcn .= " OR ";
                    }
                    $wherefindcn .= "(find_in_set ($value, food_type))";
                    $fdtcnt++;
                }
                $wherefindcn .= ')';
                //$where = "(res.name like '%".$searchItem."%')";
                if($fdtcnt>0)
                { $this->db->where($wherefindcn);  }
            }
        }
        //New code for search food type End
        if($timing){
            $this->db->where('recipe_time <=',$timing);
        }

        if($count){
            $this->db->limit($count,$page_no*$count);
        }
        $this->db->order_by('entity_id','desc');
        $result['data'] = $this->db->get('recipe')->result();

        foreach ($result['data'] as $key => $value)
        {
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
            $value->food_type_id = $food_type_id;
            $value->food_type_name = $food_type_name;
            //Code for food type section End

            //$value->image = ($value->image) ? image_url.$value->image : '';
            $value->image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';

            //Code for find menu item doe recipe :: Start
            $value->recipes_menu = [];
            $value->is_recipes_menu = 0;
            $result_menu = $this->getRecipeMenuItem($value->content_id,$language_slug,$user_timezone);
            if($result_menu && !empty($result_menu))
            {
                $value->is_recipes_menu = 1;
                $value->restaurant = $result_menu[0]['restaurant'];
                unset($result_menu[0]['restaurant']);
                $value->recipes_menu = $result_menu;
            }
            //Code for find menu item doe recipe :: End
        }
        //Code for record :: End

        //Code for total count :: Start
        $this->db->select('entity_id,name,detail,slug,image,ingredients,recipe_detail,recipe_time,language_slug,status,content_id,youtube_video');
        if($recipe_name){
            $where = "(name like '%".str_replace('\'', '', $recipe_name)."%')";
            $this->db->where($where);
        }
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);

        //New code for search food type Start
        if(trim($food) != '')
        {
            $foodarr = explode(",",$food);
            $foodarr = array_filter($foodarr);
            if(!empty($foodarr))
            {   
                $fdtcnt=0; $wherefindcn = '(';
                foreach($foodarr as $key=>$value) 
                { 
                    if($fdtcnt>0){
                        $wherefindcn .= " OR ";
                    }
                    $wherefindcn .= "(find_in_set ($value, food_type))";
                    $fdtcnt++;
                }
                $wherefindcn .= ')';
                //$where = "(res.name like '%".$searchItem."%')";
                if($fdtcnt>0)
                { $this->db->where($wherefindcn);  }
            }
        }
        //New code for search food type End
        if($timing){
            $this->db->where('recipe_time <=',$timing);
        }

        $result['count'] =  $this->db->get('recipe')->num_rows();
        //Code for total count :: End

        return $result;
    }
    public function getRecipeMenuItem($recipe_content_id,$language_slug,$user_timezone='UTC')
    {
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        $this->db->select('menu.is_deal,menu.entity_id as menu_id, menu.content_id as menu_content_id, menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.food_type,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id, add_ons_master.is_multiple,add_ons_master.display_limit,add_ons_master.mandatory,menu.ingredients,is_combo_item,res.content_id as restaurant_content_id,res.entity_id as restuarant_id,res.timings,res.enable_hours,res.currency_id,res.status as restaurant_status,currencies.currency_symbol,currencies.currency_code,c.content_id as cat_content_id,(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_noaa ,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no'); 

        $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->join('restaurant_menu_recipe_map','restaurant_menu_recipe_map.menu_content_id = menu.content_id','left');

        $this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = add_ons_category.content_id
            AND menumap.restaurant_owner_id = res.restaurant_owner_id",'left');

        $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = c.content_id
            AND catmap.restaurant_owner_id = res.restaurant_owner_id",'left');

        $this->db->where('menu.status',1);
        $this->db->where('c.status',1);
        $this->db->where('menu.language_slug',$language_slug);
        $this->db->where('restaurant_menu_recipe_map.recipe_content_id',$recipe_content_id);
        //$this->db->order_by('c.sequence','ASC');
        $this->db->order_by('sequence_no, sequence_noaa','ASC');
        //$this->db->group_by('add_ons_master.add_ons_id');
        $result = $this->db->get('restaurant_menu_item as menu')->result();
        $menu = array();
        $item_not_appicable_for_item_discount = array();
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value)
        {
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
            //offer price start
            $offer_price = '';
            $category_discount = $this->common_model->getCategoryDiscount($value->restaurant_content_id,$user_timezone);
            if(!empty($category_discount)){
                foreach ($category_discount as $key => $cat_value) {
                    if(!empty($cat_value['combined'])){
                        if(isset($cat_value['combined'][$value->cat_content_id])){
                            if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                array_push($item_not_appicable_for_item_discount, $value->menu_content_id);
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

            //offer price changes end
            if (!isset($menu[$value->category_id])) 
            {
                $menu[$value->category_id] = array();
                $menu[$value->category_id]['category_id'] = $value->category_id;
                $menu[$value->category_id]['category_name'] = $value->category;  
            }
            //restaurant array :: start
            $timing = $value->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date("l");
                foreach($timing as $keys=>$values){
                    $day = date("l");
                    if($keys == strtolower($day)){
                        $close = 'close';
                        if($value->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                            $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                        }
                        $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
                        }else{
                            $newTimingArr[strtolower($day)]['open'] = '';
                            $newTimingArr[strtolower($day)]['close'] = '';
                            $newTimingArr[strtolower($day)]['off'] = 'close';
                            $newTimingArr[strtolower($day)]['closing'] = $close;
                        }
                    }
                }
            }
            $menu[$value->category_id]['restaurant'] = array(
                'restaurant_id' => $value->restuarant_id,
                'restaurant_content_id' => $value->restaurant_content_id,
                'restaurant_status' => $value->restaurant_status,
                'restaurant_timings' => $newTimingArr[strtolower($day)],
                'currency_symbol' => (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol,
                'currency_code' => (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code,
            );
            //restaurant array :: end
            $image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
            $total = 0;
            $item_availibility_array = explode(",",$value->availability);
            $lang_availibility_txt = '';
            foreach ($item_availibility_array as $item_availibility_val){
                $lang_availibility_txt .= $this->lang->line(strtolower($item_availibility_val)).',';
            }
            if($lang_availibility_txt != ''){
                $lang_availibility_txt = substr($lang_availibility_txt, 0, -1);
            }

            if($value->check_add_ons == 1){
                if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                   $menu[$value->category_id]['items'][$value->menu_id] = array();
                   $menu[$value->category_id]['items'][$value->menu_id] = array('menu_id'=>$value->menu_id, 'menu_content_id'=>$value->menu_content_id, 'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$lang_availibility_txt,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'ingredients'=>$value->ingredients,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_combo_item' => 0,'combo_item_details' => '');
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
                $menu[$value->category_id]['items'][$value->menu_content_id]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price, 'menu_content_id'=>$value->menu_content_id, 'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$lang_availibility_txt,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'ingredients'=>$value->ingredients,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'is_combo_item'=>$value->is_combo_item,'combo_item_details'=>($value->is_combo_item == '1') ? substr(str_replace("\r\n"," + ",$value->menu_detail),0,-3) : '');
            }
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
                        if($addons_cat_list['addons_list'] && !empty($addons_cat_list['addons_list']))
                        {
                            $addons_cat_list['addons_list'] = array_values($addons_cat_list['addons_list']);
                        }
                        array_push($semifinal, $addons_cat_list);
                    }
                    $items['addons_category_list'] = $semifinal;                  
                }
                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;
    }   
    //Code for check the restaturant open/close before order place :: Start
    public function checkRestauranttime($timing, $enable_hours = 0, $scheduled_order_date = NULL, $scheduled_order_time = NULL, $user_timezone = '')
    {
        if($scheduled_order_date && $scheduled_order_time) {
            //get time interval from system options
            $this->db->select('OptionValue');
            $this->db->where('OptionSlug','time_interval_for_scheduling');
            $time_interval_for_scheduling = $this->db->get('system_option')->first_row();
            $time_interval_for_scheduling = (int)$time_interval_for_scheduling->OptionValue;
            $half_interval = ceil($time_interval_for_scheduling / 2);
        }
        $restaurant_valid = 'no';
        $newTimingArr = array();
        if($timing){
            $timing =  unserialize(html_entity_decode($timing));
            if($scheduled_order_date) {
                $day = date('l',strtotime($scheduled_order_date));
                $date_check = date('Y-m-d',strtotime($scheduled_order_date));
            } else {
                $day = date("l");
                $date_check = date('Y-m-d');
            }
            foreach($timing as $keys=>$values) {
                if($keys == strtolower($day)) {
                    $close = 'close';
                    if($enable_hours == 1) {
                        $newTimingArr['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                        $newTimingArr['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                        $newTimingArr['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';

                        if (!empty($values['open']) && !empty($values['close'])) {
                            if($scheduled_order_time) {
                                $slot_open_time = date('H:i', strtotime($scheduled_order_time));
                                $slottime = date_create($slot_open_time);
                                date_add($slottime,date_interval_create_from_date_string($half_interval." minutes"));
                                $slottime = date_format($slottime,"H:i");
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone,$slottime);
                            } else {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                        }
                        $newTimingArr['closing'] = strtolower(str_replace("Closed", "close", $close));
                    }
                    else
                    {
                        $newTimingArr['open'] = '';
                        $newTimingArr['close'] = '';
                        $newTimingArr['off'] = 'close';
                        $newTimingArr['closing'] = $close;
                    }
                    
                }
            }
        }
        if(!empty($newTimingArr)) {
            if($date_check == date('Y-m-d') && $newTimingArr['closing'] == 'close' && $newTimingArr['off'] != 'close') {
                if($scheduled_order_time && $scheduled_order_time!='')
                {
                    $scheduled_time = ($scheduled_order_time) ? date('H:i', strtotime($scheduled_order_time)) : date('H:i');
                    $date1 = strtotime(date('H:i', strtotime($this->common_model->getZonebaseTime($scheduled_time,$user_timezone))));
                    $date2 = strtotime(date('H:i', strtotime($newTimingArr['open'])));
                    $date3 = strtotime(date('H:i', strtotime($newTimingArr['close'])));
                    //if($date2 > $date1 || $date3 > $date1) :: old condition
                    if($date2 < $date1 && $date3 > $date1)
                    {
                        $restaurant_valid = 'yes';
                    } else {
                        $restaurant_valid = 'no';
                    }
                }
                else
                {
                    if($newTimingArr['closing'] == 'close'){
                        $restaurant_valid = 'no';
                    } else {
                        $restaurant_valid = 'yes';
                    }
                }                
            }else if($newTimingArr['closing'] == 'close'){
                $restaurant_valid = 'no';
            } else {
                $restaurant_valid = 'yes';
            }
        }
        if($enable_hours == 0){
            $restaurant_valid = 'no';
        }
        return $restaurant_valid;
    }
    //Code for check the restaturant open/close before order place :: End
    //Code for mysqli real escape string use for search :: Start
    function escapeString($val)
    {
        $db = get_instance()->db->conn_id;
        $val = mysqli_real_escape_string($db, $val);
        return $val;
    }
    //Code for mysqli real escape string use for search :: End
    // function to get users total earning points
    public function getUsersEarningPoints($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    //update record with multiple where
    public function updateMultipleWhere($table,$whereArray,$data)
    {
        $this->db->where($whereArray);
        $this->db->update($table,$data);
    }
    //get wallet history
    public function getWalletHistory($user_id,$count,$page_no = 1,$user_timezone='UTC'){
        $result['total_money_credited'] = 0;
        $page_no = ($page_no > 0)?$page_no-1:0;
        $this->db->limit($count,$page_no*$count);
        $this->db->where('user_id',$user_id);
        $this->db->where('is_deleted',0);
        $this->db->order_by('wallet_id','desc');
        $result['result'] = $this->db->get('wallet_history')->result();
        foreach ($result['result'] as $reskey => $resvalue) {
            $result['result'][$reskey]->reason = $this->lang->line($resvalue->reason).' '.$resvalue->order_id;
            $result['result'][$reskey]->created_date = $this->common_model->getZonebaseDateMDY($resvalue->created_date,$user_timezone);
        } 

        $this->db->where('user_id',$user_id);
        $this->db->where('is_deleted',0);
        $data = $this->db->get('wallet_history')->result();
        foreach($data as $key => $value){
            if($value->credit == 1) {
                $result['total_money_credited'] += $value->amount;  
            }
        }
        
        $this->db->where('user_id',$user_id);
        $this->db->where('is_deleted',0);
        $result['count'] = $this->db->get('wallet_history')->num_rows();
        return $result;
    }
    // function to get users total wallet money
    public function getUsersWalletMoney($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    // get currency symbol
    public function getCurrencySymbol($currency_id) {
        return $this->db->get_where('currencies',array('currency_id'=>$currency_id))->first_row();
    }
    // check if already exists - social media login
    public function checksocial($social_media_id)
    {        
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.referral_code,users.mobile_number,users.phone_code,users.earning_points,users.wallet,users.user_otp,users.image,users.notification,users.email,users.is_deleted,users.stripe_customer_id');
        $this->db->where('social_media_id',$social_media_id);
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    // check if already exists - social media login
    public function getUserByEmail($email)
    {        
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.referral_code,users.mobile_number,users.phone_code,users.earning_points,users.wallet,users.image,users.notification,users.email,users.stripe_customer_id');
        $this->db->where('email',$email);
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    // get user by user id - social media login
    public function getUserByUserid($user_id)
    {        
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.referral_code,users.mobile_number,users.earning_points,users.wallet,users.image,users.notification,users.email,users.stripe_customer_id');
        $this->db->where('entity_id',$user_id);
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    public function deleteAccount($user_id) {
        $is_deleted = array('is_deleted'=>1, 'user_otp'=>NULL, 'referral_code'=>NULL, 'status'=>2);
        $this->db->where('entity_id',$user_id);
        $this->db->update('users',$is_deleted);
        return $this->db->affected_rows();
    }
    //get active languages
    public function getActiveLanguages(){
        $result = $this->db->select('*')->get_where('languages',array('active'=>1))->result();
        //$result = $this->db->select('*')->get('languages')->result();
        $lang = array();
        foreach ($result as $key => $value) {
            $lang[$key]['language_code'] = $value->language_slug;
            $lang[$key]['language_name'] = $this->lang->line(strtolower($value->language_name));
        }
        return $lang;
    }
    //Code for table reservation :: Start :: 08-02-2021
    //get unpaid order detail
    public function getUnpaidOrderDetail($flag,$user_id,$order_ids,$user_timezone='UTC'){
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong, restaurant.timings, currencies.currency_symbol, currencies.currency_code, currencies.currency_id, order_detail.restaurant_detail, restaurant.branch_entity_id, restaurant.name as restaurant_name,restaurant.enable_hours');
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
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        if($flag == 'process'){
            $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        } 
        if($flag == 'past'){
            //$this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
            $this->db->where('(order_master.order_status = "delivered" AND order_master.order_status != "cancel")');
        }
        $this->db->where('order_master.user_id',$user_id);
        $this->db->where('order_master.paid_status','unpaid');
        $this->db->where('order_master.order_delivery','DineIn'); //Added on 21-10-2020
        $this->db->where('order_master.transaction_id','');
        //$this->db->where_in('order_detail.order_id', $order_ids);
        $this->db->order_by('order_master.entity_id','desc');
        $result =  $this->db->get('order_master')->result();
        $items = array();
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
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }

                //Code chage as per requireed :: Start
                $percent_text = ''; $text_amount = 0;
                if($value->tax_rate && $value->tax_rate !='')
                {
                    $type = ($value->tax_type == 'Percentage')?'%':'';
                    $percent_text = ($value->tax_type == 'Percentage')?' ('.$value->tax_rate.$type.')':'';
                    if($value->tax_type == 'Percentage'){
                        $text_amount = round(($value->subtotal * $value->tax_rate) / 100,2);
                    }else{
                        $text_amount = $value->tax_rate; 
                    }
                }
                //Code chage as per requireed :: End

                //Code chage as per requireed :: Start //Code for service fee :: Start
                $service_type = ''; $service_rate = 0;
                if($value->service_fee && $value->service_fee  !='')
                {
                    $s_type = ($value->service_fee_type == 'Percentage')?'%':'';
                    $service_type = ($value->service_fee_type == 'Percentage')?' ('.$value->service_fee.$s_type.')':'';
                    if($value->service_fee_type == 'Percentage'){
                        $service_rate = round(($value->subtotal * $value->service_fee) / 100,2);
                    }else{
                        $service_rate = $value->service_fee; 
                    }
                }
                //Code chage as per requireed :: End //Code for service fee :: End

                //Code for creditcard fee :: Start
                $creditcard_type = ''; $creditcard_rate = 0;
                if($value->creditcard_fee && $value->creditcard_fee  !='')
                {
                    $s_type = ($value->creditcard_fee_type == 'Percentage')?'%':'';
                    $creditcard_type = ($value->creditcard_fee_type == 'Percentage')?' ('.$value->creditcard_fee.$s_type.')':'';
                    if($value->creditcard_fee_type == 'Percentage'){
                        $creditcard_rate = round(($value->subtotal * $value->creditcard_fee) / 100,2);
                    }else{
                        $creditcard_rate = $value->creditcard_fee; 
                    }
                }
                //Code for creditcard fee :: End
                //Code for multiple coupon :: Start
                $coupon_array = $this->common_model->getCoupon_array($value->order_id);
                $items[$value->order_id]['price'] = array();
                if(!empty($coupon_array))
                {
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total");
                    if(!empty($coupon_array))
                    {
                        foreach($coupon_array as $cp_key => $cp_value){                        
                            $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('discount').'('.$cp_value['coupon_name'].')','value'=>abs($cp_value['coupon_discount']),'label_key'=>"Discount");
                        }
                    }
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee");
                    if($value->transaction_id!='' && $value->transaction_id!=null)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee");
                    }
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total");
                    
                }//End
                else if($discount)
                {
                    $items[$value->order_id]['price'] = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                        $discount,
                        array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                        array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                        ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'',
                        array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                        array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                        array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                else
                {
                    $items[$value->order_id]['price'] = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                        array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                        array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                        ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'',
                        array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                        array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
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
                            $close = 'close';
                            if($value->enable_hours=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';

                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'close';
                                if (!empty($values['open']) && !empty($values['close'])) {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                                }
                                $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }
                $items[$value->order_id]['order_status'] = ucfirst($value->order_status);
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['delivery_instructions'] =$value->delivery_instructions;
                $items[$value->order_id]['placed'] = $this->common_model->getZonebaseTime($value->order_date,$user_timezone);
                // if($value->ostatus == 'preparing')
                // {
                //     $items[$value->order_id]['preparing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                // }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                $items[$value->order_id]['order_date'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();
                    $customization = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $this->db->select('image,is_veg,status');
                        $this->db->where('entity_id',$valuee['item_id']);
                        $data = $this->db->get('restaurant_menu_item')->first_row();
                        
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';
                        $data1['is_veg'] = (!empty($data) && $data->is_veg != '')?$data->is_veg:'';
                        //$valueee['image'] = (!empty($data) && $data->image != '')?image_url.$data1['image']:'';
                        $valueee['image'] = (file_exists(FCPATH.'uploads/'.$data->image) && $data->image!='') ? image_url.$data->image : '';
                        $valueee['is_veg'] = (!empty($data) && $data->is_veg != '')?$data1['is_veg']:'';
                        
                        if($valuee['is_customize'] == 1){
                            foreach ($valuee['addons_category_list'] as $k => $val) {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if($valuee['is_deal'] == 1){
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
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['comment'] = $valuee['comment'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_combo_item'] = $valuee['is_combo_item'];
                        $valueee['combo_item_details'] = $valuee['combo_item_details'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                        
                       
                        if(!empty($customization)){
                            $valueee['addons_category_list'] = $customization;
                        }
                        $value1[] =  $valueee;
                    } 
                }
         
                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['transaction_id']  = $value->transaction_id;
                $items[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $value->phone_code.$value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    //$driver['image'] = ($value->image)?image_url.$value->image:'';
                    $driver['image'] = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $items[$value->order_id]['driver'] = $driver;
                }
                //$items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery')?'delivery':'pickup';
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery)?strtolower($value->order_delivery):'pickup';
                $items[$value->order_id]['paid_status'] = $value->paid_status;
                $items[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                $items[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        if($flag == 'process'){
            $res['in_process'] = $finalArray;
        }
        if($flag == 'past'){
            $res['past'] = $finalArray;
        }
        return $res;
    } 
    // update pending orders
    public function updatePendingOrders($user_id,$data){        
        $this->db->where('user_id',$user_id);
        $this->db->where('transaction_id','');
        $this->db->where('paid_status','unpaid');
        $this->db->update('order_master',$data);
    }
    //get distance
    public function getDistance($latitude, $longitude, $restaurant_id) {
        $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
        $this->db->where('address.resto_entity_id',$restaurant_id);
        return $this->db->get('restaurant_address as address')->first_row();
    }
    public function getAllOrdersMultipleWhere($table,$whereArray)
    {
        $this->db->select('entity_id, user_id, restaurant_id, created_date, UNIX_TIMESTAMP(order_date) as order_datetemp,table_id');
        /*$this->db->select('entity_id, user_id, restaurant_id, created_date, order_date as order_datetemp');*/
        $this->db->where($whereArray);
        return $this->db->get($table)->result();
    }
    public function getRecordpaidstatus($user_id,$restaurant_id)
    {
        $this->db->select('entity_id,order_delivery,restaurant_id,table_id,subtotal');
        $this->db->where('user_id',$user_id);
        $this->db->where('paid_status','unpaid');
        $this->db->where('order_status!=','rejected');
        $this->db->where('order_status!=','cancel');
        $this->db->where('order_delivery',"DineIn");
        return $this->db->get('order_master')->result();
    }
    //Code for table reservation :: End :: 08-02-2021
    //get restaurant details
    public function getRestaurantForAddOrder($tblname,$restaurant_id,$user_timezone='UTC'){
        //get restaurants
        $this->db->select("address.address,res.content_id,currencies.currency_code,currencies.currency_symbol,res.image,res.name,res.phone_number,res.phone_code,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,res.entity_id as restaurant_id,res.timings,res.enable_hours");
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        //$this->db->join('review','res.entity_id = review.restaurant_id','left');
        $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->where('res.entity_id',$restaurant_id);
        $result = $this->db->get($tblname.' as res')->first_row();
        $default_currency = get_default_system_currency();
        $timing = $result->timings;
        if($timing){
           $timing =  unserialize(html_entity_decode($timing));
           $newTimingArr = array();
            $day = date("l");
            foreach($timing as $keys=>$values) {
                $day = date("l");
                if($keys == strtolower($day))
                {
                    $close = 'close';
                    if($result->enable_hours=='1')
                    {
                        $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                        $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';

                        $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        $close = 'close';
                        if (!empty($values['open']) && !empty($values['close'])) {
                            $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                        }
                        $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
        $result->timings = $newTimingArr[strtolower($day)];
        //$result->image = ($result->image)?image_url.$result->image:'';
        $result->image  = (file_exists(FCPATH.'uploads/'.$result->image) && $result->image!='') ? image_url.$result->image : '';
        $result->rating = ($result->rating)?number_format((float)$result->rating, 1, '.', ''):null;
        if(!empty($default_currency)){
            $result->currency_symbol = $default_currency->currency_symbol;
            $result->currency_code = $default_currency->currency_code;
        }
        return $result;
    }
    //get dinein order detail
    public function getDineInOrderDetail($flag,$user_id,$language_slug,$user_timezone='UTC')
    {
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant.timings,restaurant.image as restaurant_image,restaurant.content_id,restaurant.status as restaurant_status,order_master.entity_id as entity_id, order_detail.entity_id as oentity_id, restaurant.branch_entity_id, restaurant.name as restaurant_name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id,restaurant.enable_hours');

        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        //$this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
         $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('order_master.user_id',$user_id);
        $this->db->where('order_master.table_id!=',null);
        $this->db->where('order_master.order_delivery','DineIn');
        $this->db->where('order_master.paid_status','unpaid');
        $status_arr = array('delivered','cancel','rejected');
        $this->db->where_not_in('order_master.order_status',$status_arr);

        $this->db->order_by('order_master.entity_id','desc');
        $this->db->group_by(array("order_master.entity_id", "status.order_status"));
        $result =  $this->db->get('order_master')->result();
        $items = array();
        /*Later need to change based on restaurant currency*/
       /* $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $currencyDetails = $this->getCurrencySymbol($currency_id->OptionValue);*/
        /**/
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            if(!isset($items[$value->order_id])){
                $items[$value->order_id] = array();
                //$items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
            }
            if(isset($items[$value->order_id])) 
            {
                //Code chage as per requireed :: Start
                $percent_text = ''; $text_amount = 0;
                if($value->tax_rate && $value->tax_rate !='')
                {
                    $type = ($value->tax_type == 'Percentage')?'%':'';
                    $percent_text = ($value->tax_type == 'Percentage')?' ('.$value->tax_rate.$type.')':'';
                    if($value->tax_type == 'Percentage'){
                        $text_amount = round(($value->subtotal * $value->tax_rate) / 100,2);
                    }else{
                        $text_amount = $value->tax_rate; 
                    }
                }
                //Code chage as per requireed :: End

                //Code for table no :: Start
                $table_number = '';
                if($value->table_id!='')
                {
                    $this->db->select('table_number');
                    $this->db->where('entity_id',$value->table_id);
                    $tablearr =  $this->db->get('table_master')->first_row();
                    $table_number = $tablearr->table_number;                                     
                }
                //Code for table no :: End

                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['table_id'] = $value->table_id;
                $items[$value->order_id]['table_number'] = $table_number;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                $items[$value->order_id]['restaurant_content_id'] = $value->content_id;
                $items[$value->order_id]['order_accepted'] = ($value->status == 1)?1:0;
                $items[$value->order_id]['accept_order_time'] = $this->common_model->getZonebaseTime($value->accept_order_time,$user_timezone);
                $restaurant_detail = unserialize($value->restaurant_detail);

                //Code for find the main restaurant name :: Start
                $rest_name = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                /*if($value->branch_entity_id>0)
                {
                    $this->db->select("entity_id, name");
                    $this->db->where('language_slug',$this->current_lang);
                    $this->db->where('status',1);
                    $this->db->where('entity_id',$value->branch_entity_id);
                    $rest_result =  $this->db->get('restaurant')->first_row();
                    if($rest_result->name!='' && $rest_result && isset($value->restaurant_name))
                    {
                        $rest_nametemp = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                        $rest_name = $rest_result->name.' ('.$rest_nametemp.')';
                    }                           
                }*/
                $items[$value->order_id]['restaurant_name'] = $rest_name;
                $items[$value->order_id]['restaurant_image'] = (file_exists(FCPATH.'uploads/'.$value->restaurant_image) && $value->restaurant_image!='')?image_url.$value->restaurant_image:'';
                //Code for find the main restaurant name :: End

                $items[$value->order_id]['restaurant_address'] = (isset($restaurant_detail->address))?$restaurant_detail->address:'';
                $items[$value->order_id]['restaurant_status'] = ($value->restaurant_status)?$value->restaurant_status:'';

                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }

                //Code chage as per requireed :: Start //Code for service fee :: Start
                $service_type = ''; $service_rate = 0;
                if($value->service_fee && $value->service_fee  !='')
                {
                    $s_type = ($value->service_fee_type == 'Percentage')?'%':'';
                    $service_type = ($value->service_fee_type == 'Percentage')?' ('.$value->service_fee.$s_type.')':'';
                    if($value->service_fee_type == 'Percentage'){
                        $service_rate = round(($value->subtotal * $value->service_fee) / 100,2);
                    }else{
                        $service_rate = $value->service_fee; 
                    }
                }
                //Code chage as per requireed :: End //Code for service fee :: End

                //Code for creditcard fee :: Start
                $creditcard_type = ''; $creditcard_rate = 0;
                if($value->creditcard_fee && $value->creditcard_fee  !='')
                {
                    $cr_type = ($value->creditcard_fee_type == 'Percentage')?'%':'';
                    $creditcard_type = ($value->creditcard_fee_type == 'Percentage')?' ('.$value->creditcard_fee.$cr_type.')':'';
                    if($value->creditcard_fee_type == 'Percentage'){
                        $creditcard_rate = round(($value->subtotal * $value->creditcard_fee) / 100,2);
                    }else{
                        $creditcard_rate = $value->creditcard_fee; 
                    }
                }
                //Code for creditcard fee :: End
                
                /*wallet money changes start*/
                $wallet_history = $this->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'debit' => 1, 'is_deleted'=>0));
                $wallet = ($wallet_history)?array('label'=>$this->lang->line('wallet_discount'),'value'=>$wallet_history->amount, 'label_key'=>'Wallet Discount'):'';
                /*wallet money changes end*/

                //Code for multiple coupon :: Start
                $coupon_array = $this->common_model->getCoupon_array($value->order_id);
                $items[$value->order_id]['price'] = array();
                if(!empty($coupon_array))
                {
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total");
                    if(!empty($coupon_array))
                    {
                        foreach($coupon_array as $cp_key => $cp_value){                        
                            $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('discount').'('.$cp_value['coupon_name'].')','value'=>abs($cp_value['coupon_discount']),'label_key'=>"Discount");
                        }
                    }
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee");
                    if($value->transaction_id!='' && $value->transaction_id!=null)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee");
                    }                    
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total");
                }//End
                else if($discount)
                {
                    $items[$value->order_id]['price'] = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                        $discount,
                        array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                        array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                        array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                        ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'',
                        $wallet,
                        array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                else
                {
                    $items[$value->order_id]['price'] = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                        array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"),
                        array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                        array('label'=>$this->lang->line('service_fee').$service_type,'value'=>$service_rate,'label_key'=>"Service Fee"),
                        ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$creditcard_type,'value'=>$creditcard_rate,'label_key'=>"Credit Card Fee"):'',
                        $wallet,
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
                            $close = 'close';
                            if($value->enable_hours=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                if(!empty($values['open']) && !empty($values['close']))
                                {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                                }                            
                                $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }

                if($value->order_status == "placed" && $value->status!='1')
                {
                    $order_statusval = 'placed';
                }
                else if(($value->order_status == "placed" && $value->status=='1') || $value->order_status == "accepted")
                {
                    $order_statusval = 'accepted';
                }

                else if($value->order_status == "rejected")
                {
                    $order_statusval = 'rejected';
                }
                else if($value->order_status == "ready")
                {
                    $order_statusval = 'served';
                }
                else
                {
                    $order_statusval = ucfirst($value->order_status);
                }

                $items[$value->order_id]['order_status_display'] = (strtolower($order_statusval) == "cancel") ? $this->lang->line('cancelled') : ((strtolower($order_statusval) == "complete")?$this->lang->line('completed'):$this->lang->line(strtolower($order_statusval)));
                if((strtolower($value->order_delivery)=='pickup' || strtolower($value->order_delivery)=='dinein') && $value->order_status == "onGoing")
                {
                    // $order_notification_slug = 'order_is_readynoti';
                    $items[$value->order_id]['order_status_display'] = $this->lang->line('order_ready');
                }
                if($order_statusval == "served")
                {
                    $order_statusval = 'ready';
                }
                $items[$value->order_id]['order_status'] = ucfirst($order_statusval);
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['subtotal'] = $value->subtotal;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['delivery_instructions'] =$value->delivery_instructions;
                $items[$value->order_id]['placed'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);
                // if($value->ostatus == 'preparing')
                // {
                    // $items[$value->order_id]['preparing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                // }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                $items[$value->order_id]['order_date'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $customization = array();
                        $this->db->select('image,food_type,status');
                        $this->db->where('entity_id',$valuee['item_id']);
                        $data = $this->db->get('restaurant_menu_item')->first_row();
                        $valueee = array();
                        
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';
                        $data1['food_type'] = (!empty($data) && $data->food_type != '')?$data->food_type:'';
                        //$valueee['image'] = (!empty($data) && $data->image != '')?image_url.$data1['image']:'';
                        $valueee['image']  = (file_exists(FCPATH.'uploads/'.$data->image) && $data->image!='') ? image_url.$data->image :'';
                        $is_vegtemp = (!empty($data) && $data->food_type != '')?$data1['food_type']:'';

                        //Code for food type :: start
                        $food_type_id = ''; $food_type_name = '';
                        if($is_vegtemp!='')
                        {
                            $is_vegarr = explode(",", $is_vegtemp);
                            $this->db->select('entity_id as food_type_id, name as food_type_name');
                            $this->db->where_in('entity_id',$is_vegarr);
                            $resfood_type = $this->db->get('food_type')->result();
                            if($resfood_type && count($resfood_type)>0)
                            {
                                $food_type_id = implode(",",array_column($resfood_type, 'food_type_id'));
                                $food_type_name = implode(",",array_column($resfood_type, 'food_type_name'));
                            }
                        }
                        $valueee['food_type_id'] = $food_type_id;
                        $valueee['food_type_name'] = $food_type_name;
                        //Code for food type :: end
                        
                        if($valuee['is_customize'] == 1)
                        {
                            foreach ($valuee['addons_category_list'] as $k => $val)
                            {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if($valuee['is_deal'] == 1){
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
                        }
                        else
                        {
                            $customization = [];
                        }
                      
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['menu_content_id'] = $valuee['menu_content_id'] ? $valuee['menu_content_id'] : '';
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['comment'] = $valuee['comment'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_combo_item'] = $valuee['is_combo_item'];
                        $valueee['combo_item_details'] = $valuee['combo_item_details'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['itemTotal'] = ($valuee['itemTotal'])?$valuee['itemTotal']:'';
                        $valueee['order_flag'] = ($valuee['order_flag'])?$valuee['order_flag']:'';

                        if(!empty($customization)){
                            $valueee['addons_category_list'] = $customization;
                        }
                        $value1[] =  $valueee;
                    } 
                }

                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['transaction_id']  = $value->transaction_id;
                $items[$value->order_id]['is_parcel_order']  = $value->is_parcel_order;
                $payment_option_val = '';
                if($value->payment_option){
                    if($value->payment_option == 'cod'){
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
                $items[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                $items[$value->order_id]['payment_option'] = $payment_option_val;
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $value->phone_code.$value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    //$driver['image'] = ($value->image)?image_url.$value->image:'';
                    $driver['image'] = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $items[$value->order_id]['driver'] = $driver;
                }
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery)?strtolower($value->order_delivery):'pickup';
                $items[$value->order_id]['paid_status'] = $value->paid_status;
                $items[$value->order_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                $items[$value->order_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        return $finalArray;
    }
    //get items
    public function getMenuSuggestionItems($restaurant_id,$language_slug,$cart_items_array,$user_timezone)
    {
        $res_content_id = $this->getContentId($restaurant_id,'restaurant');
        $this->db->select('menu_content_id');
        $this->db->where('restaurant_content_id',$res_content_id->content_id);
        if(!empty($cart_items_array)){
            $this->db->where_not_in('menu_content_id', $cart_items_array);
        }
        $menu_content_ids = $this->db->get('restaurant_menu_suggestion')->result_array();

        //Code for find the restaurant owner id :: Start
        $restaurant_owner_id='';
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
        if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
        {
            $selectval = ",(CASE WHEN catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no";
            $selectvaladd = ",(CASE WHEN menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_noaa";
            $selectvalM = ",(CASE WHEN  menuitemmap.sequence_no is NULL THEN 1000 ELSE menuitemmap.sequence_no END) as sequence_nomenu";
        }               
        //Code for find the restaurant owner id :: End
        
        if(!empty($menu_content_ids)){
            $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
            $this->db->select('menu.is_deal,menu.entity_id as menu_id, menu.content_id as menu_content_id, menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.food_type,availability,menu.stock,c.name as category,c.entity_id as category_id,c.content_id as cat_content_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id, add_ons_master.is_multiple,add_ons_master.display_limit,add_ons_master.mandatory'.$selectval.' '.$selectvaladd.' '.$selectvalM.''); //,menu.ingredients
            $this->db->join('category as c','menu.category_id = c.entity_id');
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = c.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$res_content_id->content_id."'",'left');
            }
            $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
            $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
            $this->db->where('c.status',1);
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = add_ons_category.content_id AND menumap.restaurant_owner_id = '".$restaurant_owner_id."' AND menumap.restaurant_content_id = '".$res_content_id->content_id."'",'left');
            }
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->join('menu_item_sequencemap as menuitemmap',"menuitemmap.menu_content_id = menu.content_id AND menuitemmap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');                
            }
            $this->db->where_in('menu.content_id',array_column($menu_content_ids,'menu_content_id'));
            $this->db->where('menu.language_slug',$language_slug);
            $this->db->where('menu.status',1);
            $this->db->where('menu.stock',1);
            //Code for sort addon category :: Start
            if($restaurant_owner_id!='' && intval($restaurant_owner_id)>0)
            {
                $this->db->order_by('sequence_no, c.name, sequence_nomenu, sequence_noaa', 'ASC');
            }
            //End
            $this->db->order_by('menu.price','asc');
            $result = $this->db->get('restaurant_menu_item as menu')->result(); 

            $menu = array();
            foreach ($result as $key => $value)
            {
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
                    $category_discount = $this->common_model->getCategoryDiscount($res_content_id->content_id,$user_timezone);
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
                //$image = ($value->image)?image_url.$value->image:'';
                $image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : '';
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('menu_id'=>$value->menu_id, 'menu_content_id'=>$value->menu_content_id, 'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'in_stock'=>$value->stock); //'ingredients'=>$value->ingredients,
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
                    $menu[$value->category_id]['items'][]  = array('menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price, 'menu_content_id'=>$value->menu_content_id, 'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'food_type_id'=>$food_type_id,'food_type_name'=>$food_type_name,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status,'in_stock'=>$value->stock); //'ingredients'=>$value->ingredients,
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
    // get content id
    public function getContentId($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    //get getRestaurantID
    public function getRestaurantID($language_slug){
        $this->db->select('entity_id,content_id');
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);
        $result =  $this->db->get('restaurant')->first_row();
        return $result;
    }
    
    public function getunpaid_order($user_id)
    {
        $where_in = array('cancel','rejected');
        $this->db->where_not_in('order_status',$where_in);
        $this->db->where('order_delivery','DineIn');
        $this->db->where('paid_status','unpaid');
        $this->db->where('transaction_id','');
        $this->db->where('user_id',$user_id);
        $result =  $this->db->get('order_master')->first_row();
        return $result;
    }
    public function getRestaurantreviewId($entity_id,$user_timezone='UTC'){
        $this->db->select('entity_id');
        $this->db->where('entity_id',$entity_id);
        // $this->db->where('status',1);
        return $this->db->get('restaurant')->first_row();
    }


    // get Payment Method
    public function getPaymentMethod($currency_code = NULL, $is_dine_in = 0,$restuarant_content_id='',$all_method='no')
    { 
        $payment_methodsarr = array();
        if($restuarant_content_id!='')
        {
            $this->db->select('payment_id');
            $this->db->where('restaurant_content_id',$restuarant_content_id);
            $res_result = $this->db->get('restaurant_payment_method_suggestion')->result_array();
            $payment_methodsarr = array_map (function($value){
                return $value['payment_id'];
            } , $res_result);
        }
        $result = array();
        if(!empty($payment_methodsarr) || $all_method=='yes') {
            $this->db->where('status',1);
            if(!empty($currency_code) && !is_null($currency_code))
            {
                $wherefindavblt = "(find_in_set ('".strtolower($currency_code)."', valid_currency))";
                $this->db->where($wherefindavblt);
            }
            if(!empty($payment_methodsarr))
            {
                $this->db->where_in('payment_id',$payment_methodsarr);
            }
            $this->db->order_by("sorting", "ASC");
            $result = $this->db->get('payment_method')->result();

            foreach ($result as $key => $value)
            {
                if($value->payment_gateway_slug == 'cod'){
                    if($is_dine_in == 1){
                        $value->display_name_en = $this->lang->line('pay_at_counter_en');
                        $value->display_name_fr = $this->lang->line('pay_at_counter_fr');
                        $value->display_name_ar = $this->lang->line('pay_at_counter_ar');
                    }
                }
                $value->PaymentMethod = json_decode($value->PaymentMethod,true);
            }
        }
        return $result;
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
    public function getRecordreserveTable($table_id,$restaurant_id)
    {
        $this->db->select('table.entity_id');
        $this->db->join('table_master as table','tb.table_master_id = table.entity_id');
        $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id');        
        $this->db->join('order_master as ord','table.entity_id = ord.table_id');
        $where_string = "ord.user_id=tb.user_id";
        $this->db->where($where_string);
        $this->db->where('table.entity_id',$table_id);
        $this->db->where('res.content_id',$restaurant_id);
        $this->db->where('tb.status','approve');
        $this->db->where_not_in('ord.order_status',array('delivered','cancel','rejected','complete'));
        $this->db->where_not_in('ord.payment_option',array('stripe','paypal','applepay'));
        $this->db->where('ord.admin_payment_option',null);
        $this->db->group_by('table.entity_id');
        $this->db->group_by('ord.entity_id');
        $result = $this->db->count_all_results('table_status as tb');
        return $result;
    }

    public function get_faq_list($language_slug = 'en'){
        $this->db->select('entity_id,name,sequence,content_id,language_slug,status');
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        $query = $this->db->get('faq_category');
        $i = 0;
        foreach ($query->result() as $category) {
            $faqs = $this->get_faqs($category->entity_id);
            if($faqs){
                $return[$category->name] = $category;
                $return[$category->name]->faqs = $faqs;
                $i++;
            }
        }
        $result['data'] = $return;
        $result['count'] = $i;
        return $result;
    }

    public function get_faqs($category_id) {
        $this->db->select('entity_id,faq_category_id,question,answer,content_id,language_slug,status');
        $this->db->where('status',1);
        $this->db->where('faq_category_id', $category_id);
        $query = $this->db->get('faqs');
        return $query->result();
    }

    // delete wallet history by order_id
    public function deletewallethistory($order_id){
        $this->db->where('order_id',$order_id);
        $this->db->update('wallet_history',array('is_deleted'=> 1));
        return $this->db->affected_rows();
    }
    public function getRestaurantReviewCount($restaurant_content_id,$language_slug){
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $this->db->select('review.entity_id as review_id');
            $this->db->join('users','review.user_id = users.entity_id','left');
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $return = $this->db->get_where('review',array('review.status'=>1))->num_rows();
        } else {
            $return = $res_rating->restaurant_rating_count;
        }
        return $return;
    }
    public function getRestownerid($restaurant_id){
        $this->db->select('restaurant_owner_id');
        $this->db->where('entity_id',$restaurant_id);        
        $data = $this->db->get('restaurant')->first_row();
        return $data;
    }
    //table reservation changes :: start
    //check booking availability
    public function getTableBookingAvailability($booking_date, $start_time, $end_time, $no_of_people, $restaurant_id, $user_timezone='UTC', $language_slug){
       
        $res_content_id = $this->getResContentId($restaurant_id);
        $this->db->select('enable_table_booking,table_booking_capacity as capacity, table_online_availability, table_minimum_capacity, allowed_days_table, timings, enable_hours,');
        $this->db->where('content_id',$res_content_id);
        $this->db->where('language_slug',$language_slug);
        $this->db->where('status',1);
        $capacity =  $this->db->get('restaurant')->first_row();
        if ($capacity && $capacity->enable_table_booking == '1') {
            $timing = $capacity->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date('l', strtotime($booking_date));
                foreach($timing as $keys=>$values) {
                    if($keys == strtolower($day)) {
                        $close = 'close';
                        if($capacity->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'close';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                            }
                            $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
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
            //echo "<pre>"; print_r($newTimingArr); exit;
            $capacity->timings = $newTimingArr[strtolower($day)];
            $capacity->capacity = ($capacity->capacity>0 && $capacity->table_online_availability && $capacity->table_online_availability>0)?floor(($capacity->table_online_availability * $capacity->capacity)/100) : $capacity->capacity;
            //for booking
            $start_time_post = date('H:i',strtotime($this->common_model->setZonebaseDateTime($start_time,$user_timezone)));
            $end_time_post = date('H:i',strtotime($this->common_model->setZonebaseDateTime($end_time,$user_timezone)));
            $start_end_time_chk = "(NOT(start_time >= str_to_date('".$end_time_post."','%H:%i:%s') OR end_time <= str_to_date('".$start_time_post."','%H:%i:%s')))";
            $this->db->select('SUM(no_of_people) as people');
            $this->db->where('booking_date',$booking_date);
            $this->db->where($start_end_time_chk);
            $this->db->where('restaurant_content_id',$res_content_id);
            $table_booking = $this->db->get('table_booking')->first_row();

            date_default_timezone_set($user_timezone);//user time zone
            //get table booking
            if($no_of_people >= $capacity->table_minimum_capacity){
                $peopleCount = $capacity->capacity - $table_booking->people;
                if($peopleCount >= $no_of_people){ 
                    //res capacity available
                    if($capacity->timings['off'] == 'open'){
                        //res open for the day
                        $start_timeend = $start_time;
                        if((date('H:i',strtotime($capacity->timings['open'])))>(date('H:i',strtotime($capacity->timings['close']))))
                        {
                            $start_timett = strtotime($start_time)+(12*3600);
                            $start_timeend = date('H:i a',$start_timett);
                        }
                        $capacity_close = date('Y-m-d H:i',strtotime($capacity->timings['close']));
                        if(strtolower(date('a',strtotime($start_time))) == strtolower(date('a',strtotime($end_time))) && strtolower(date('a',strtotime($start_time))) =='pm')
                        {
                            $capacity_close = date('Y-m-d H:i',strtotime('+12 hour',strtotime($capacity->timings['close'])));  
                            $start_timeend = $start_time;              
                        }
                        $end_dt = new DateTime($capacity_close);
                       
                        if(($end_dt->getTimestamp() >= strtotime($start_time)) && (date('H:i',strtotime($capacity->timings['open'])) <= date('H:i',strtotime($start_timeend)))) {
                            //if close time > entered time && open time < entered time
                            return $msg = 'booking_available';
                        } else {
                            //Booking is not avilable for selected time
                            return $msg = 'booking_not_available_time';
                        }
                    } else {
                        //res closed for the day
                        //Booking is not available for selected date
                        return $msg = 'restaurant_closed';
                    }  
                } else {
                    //res table booking capacity occupied
                    if($peopleCount == 0) {
                        $arr = array('err_msg'=>'res_is_full', 'msg'=>'booking_not_available_capacity');
                        return $arr; 
                    } else {
                        $arr = array('remaining_capacity'=>$peopleCount, 'err_msg'=>'table_booking_not_avail_capacity', 'msg'=>'booking_not_available_capacity');
                        return $arr;
                    }
                }
            } else {
                $arr = array('minimum_capacity'=> $capacity->table_minimum_capacity, 'err_msg'=>'min_table_capacity_validation', 'msg'=>'min_capacity_validation');
                return $arr; 
            }
        }
        else
        {
            return false;
        }
    }
    //get table booking
    public function getTableBooking($user_id,$language_slug,$user_timezone='UTC'){
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDateTime = $this->common_model->getZonebaseCurrentTime($currentDateTime,$user_timezone);
        $status_array = array('cancelled');
        $default_currency = get_default_system_currency();
        //upcoming
        $this->db->select('table_booking.entity_id as booking_id,table_booking.booking_date,table_booking.start_time,table_booking.end_time,table_booking.booking_status,table_booking.cancel_reason,table_booking.no_of_people,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,currencies.currency_symbol,currencies.currency_code, restaurant.branch_entity_id, restaurant.name as res_name, restaurant.image as res_image, restaurant_address.address as res_address, restaurant_address.city as res_city, restaurant_address.zipcode as res_zipcode, table_booking.restaurant_content_id, table_booking.additional_request, table_booking.created_date');

        $this->db->join('restaurant','table_booking.restaurant_content_id = restaurant.content_id','left');
        $this->db->join('restaurant_address','restaurant_address.resto_entity_id = restaurant.entity_id','left');
        $this->db->join('review','restaurant.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('table_booking.user_id',$user_id);
        $this->db->where('table_booking.booking_date >=',date('Y-m-d', strtotime($currentDateTime)));
        $this->db->where_not_in('table_booking.booking_status',$status_array);
        $this->db->group_by('table_booking.entity_id');
        $this->db->order_by('table_booking.entity_id','desc');
        $result = $this->db->get('table_booking')->result();
        $upcoming = array();
        foreach ($result as $key => $value) {
            if(!isset($value->booking_id)){
                $upcoming[$value->booking_id] = array();
            }
            if(isset($value->booking_id)){
                $upcoming_cancel_reason = ($value->booking_status == 'cancelled')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';

                $upcoming[$value->booking_id]['is_table'] = 1;
                $upcoming[$value->booking_id]['entity_id'] = $value->booking_id;
                $upcoming[$value->booking_id]['created_date']=$this->common_model->getZonebaseDateMDY($value->created_date,$user_timezone);
                $upcoming[$value->booking_id]['booking_date'] = date('Y-m-d', strtotime($this->common_model->getZonebaseDate($value->booking_date,$user_timezone)));
                $upcoming[$value->booking_id]['start_time'] = $this->common_model->getZonebaseTime($value->start_time,$user_timezone);
                $upcoming[$value->booking_id]['end_time'] = $this->common_model->getZonebaseTime($value->end_time,$user_timezone);
                $upcoming[$value->booking_id]['booking_status'] = $this->lang->line(strtolower($value->booking_status)).$upcoming_cancel_reason;
                $upcoming[$value->booking_id]['booking_status_key'] = $value->booking_status;
                $upcoming[$value->booking_id]['no_of_people'] = $value->no_of_people;
                $upcoming[$value->booking_id]['currency_code'] = (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                $upcoming[$value->booking_id]['currency_symbol'] = (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                $upcoming[$value->booking_id]['additional_request'] = ($value->additional_request)?$value->additional_request:'';
                $upcoming[$value->booking_id]['name'] =  ($value->res_name)?$value->res_name:'';
                $upcoming[$value->booking_id]['image'] =  ($value->res_image != '' && file_exists(FCPATH.'uploads/'.$value->res_image))?image_url.$value->res_image:'';
                $upcoming[$value->booking_id]['address'] =  (!empty($value->res_address))?$value->res_address:'';
                $upcoming[$value->booking_id]['city'] =  (!empty($value->res_city))?$value->res_city:'';
                $upcoming[$value->booking_id]['zipcode'] =  (!empty($value->res_zipcode))?$value->res_zipcode:'';
                $upcoming[$value->booking_id]['rating'] =  (!empty($value->rating))?$value->rating:'';
            }
        }
        $finalArray = array();
        foreach ($upcoming as $key => $val) {
           $finalArray[] = $val; 
        }
        $data['upcoming'] = $finalArray;
        //past
        $this->db->select('table_booking.entity_id as booking_id,table_booking.booking_date,table_booking.start_time,table_booking.end_time,table_booking.booking_status,table_booking.cancel_reason,table_booking.no_of_people,CAST(AVG(review.rating) AS DECIMAL(10,2)) as rating,currencies.currency_symbol,currencies.currency_code, restaurant.branch_entity_id, restaurant.name as res_name, restaurant.image as res_image, restaurant_address.address as res_address, restaurant_address.city as res_city, restaurant_address.zipcode as res_zipcode, table_booking.restaurant_content_id, table_booking.additional_request, table_booking.created_date');
        
        $this->db->join('restaurant','table_booking.restaurant_content_id = restaurant.content_id','left');
        $this->db->join('restaurant_address','restaurant_address.resto_entity_id = restaurant.entity_id','left');
        $this->db->join('review','restaurant.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('restaurant.language_slug',$language_slug);
        $this->db->where('table_booking.user_id',$user_id);
        //$this->db->where('table_booking.booking_date <',date('Y-m-d', strtotime($currentDateTime)));
        //$this->db->where_in('table_booking.booking_status',$status_array);
        $this->db->where('(table_booking.booking_date <', date('Y-m-d', strtotime($currentDateTime)))->or_where("table_booking.booking_status = 'cancelled')");
        $this->db->group_by('table_booking.entity_id');
        $this->db->order_by('table_booking.entity_id','desc');
        $resultPast = $this->db->get('table_booking')->result();

        $past = array();
        foreach ($resultPast as $key => $value) {
            if(!isset($value->booking_id)){
                $past[$value->booking_id] = array();
            }
            if(isset($value->booking_id)){
                $past_cancel_reason = ($value->booking_status == 'cancelled')?(($value->cancel_reason)?' ('.$value->cancel_reason.')':''):'';

                $past[$value->booking_id]['is_table'] = 1;
                $past[$value->booking_id]['entity_id'] = $value->booking_id;
                $past[$value->booking_id]['created_date']=$this->common_model->getZonebaseDateMDY($value->created_date,$user_timezone);
                $past[$value->booking_id]['booking_date'] = date('Y-m-d', strtotime($this->common_model->getZonebaseDate($value->booking_date,$user_timezone)));
                $past[$value->booking_id]['start_time'] = $this->common_model->getZonebaseTime($value->start_time,$user_timezone);
                $past[$value->booking_id]['end_time'] = $this->common_model->getZonebaseTime($value->end_time,$user_timezone);
                $past[$value->booking_id]['booking_status_key'] = $value->booking_status;
                $past[$value->booking_id]['booking_status'] =  $this->lang->line(strtolower($value->booking_status)).$past_cancel_reason;
                $past[$value->booking_id]['no_of_people'] =  $value->no_of_people;
                $past[$value->booking_id]['currency_code'] =  (!empty($default_currency)) ? $default_currency->currency_code : $value->currency_code;
                $past[$value->booking_id]['currency_symbol'] =  (!empty($default_currency)) ? $default_currency->currency_symbol : $value->currency_symbol;
                $past[$value->booking_id]['additional_request'] = ($value->additional_request)?$value->additional_request:'';
                $past[$value->booking_id]['name'] =  ($value->res_name)?$value->res_name:'';
                $past[$value->booking_id]['image'] =  ($value->res_image != '' && file_exists(FCPATH.'uploads/'.$value->res_image))?image_url.$value->res_image:'';
                $past[$value->booking_id]['address'] =  (!empty($value->res_address))?$value->res_address:'';
                $past[$value->booking_id]['city'] =  (!empty($value->res_city))?$value->res_city:'';
                $past[$value->booking_id]['zipcode'] =  (!empty($value->res_zipcode))?$value->res_zipcode:'';
                $past[$value->booking_id]['rating'] =  $value->rating;
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
           $final[] = $val; 
        }
        $data['past'] = $final;
        return $data;
    } 
    //table reservation changes :: end
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
    //stripe save card changes :: start
    public function checkStripeCustomerId($user_id){
        $this->db->select('stripe_customer_id');
        $this->db->where('entity_id',$user_id);
        $result = $this->db->get('users')->first_row();
        if(!empty($result) && $result->stripe_customer_id != NULL && $result->stripe_customer_id != ''){
            return $result->stripe_customer_id;
        } else {
            return false;
        }
    }
    public function add_new_customer_in_stripe($first_name,$last_name,$phone_code,$phone_number,$email)
    {
        // Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_info = $this->common_model->get_payment_method_detail('stripe');
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
        $stripe = new \Stripe\StripeClient($stripe_api_key);

        //create customer
        $username = ($first_name != '' && $last_name != '')?$first_name.' '.$last_name:$first_name;
        $user_phn = ($phone_code != '' && $phone_number != '')?$phone_code.$phone_number:$phone_number;

        try {
            //create customer :: start
            $paymentIntent = $stripe->customers->create([
                'name' => $username,
                'email' => ($email)?$email:'',
                'phone' => $user_phn
            ]);
            //create customer :: end
            if(!empty($paymentIntent) && $paymentIntent->id){
                $cus_id = $paymentIntent->id;
                return $cus_id;
            } else {
                return false;
            }
        } catch (Exception $e){
            return false;
        }
    }
    //stripe save card changes :: end    
    //get coupon list
    public function getCouponsForHome($restaurant_content_id,$order_mode) {

        $order_mode_arr = explode(',', $order_mode);
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,coupon.max_amount, coupon.maximaum_use_per_users, coupon.maximaum_use, coupon.use_with_other_coupons,coupon.coupon_for_newuser');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        if(in_array('PickUp', $order_mode_arr) && !in_array('Delivery', $order_mode_arr)) {
            //$this->db->where('coupon.coupon_type != "free_delivery"');
            $this->db->where_in('coupon.coupon_type',array("discount_on_cart","user_registration"));
        } else if(!in_array('PickUp', $order_mode_arr) && in_array('Delivery', $order_mode_arr)){
            //$this->db->or_where('coupon.coupon_type',"free_delivery");
            $this->db->where_in('coupon.coupon_type',array("free_delivery","discount_on_cart","user_registration"));
        }
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        $this->db->where('(coupon_type != "discount_on_items")');
        $this->db->where('(coupon_type != "discount_on_categories")');
        $this->db->where('coupon_type != ',"dine_in");
        $this->db->where('coupon.status',1);
        $this->db->group_by('coupon.entity_id');
        $result = $this->db->get('coupon')->result();
        return $result;
    }
    public function checkRestaurantRating($restaurant_content_id, $language_slug) {
        $this->db->select('restaurant_rating, restaurant_rating_count');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->where('language_slug',$language_slug);
        $res_rating = $this->db->get('restaurant')->first_row();
        $is_rating_from_res_form = '0';
        if(!($res_rating->restaurant_rating && $res_rating->restaurant_rating_count)) {
            $return['is_rating_from_res_form'] = $is_rating_from_res_form;
        } else {
            $is_rating_from_res_form = '1';
            $return['is_rating_from_res_form'] = $is_rating_from_res_form;
            $return['avg_rating'] = number_format($res_rating->restaurant_rating,1);
        }
        return $return;
    }
    public function checkUserExistForSendOTP($table, $whereArray, $user_id=0) {
        if($user_id > 0) {
            $this->db->where('entity_id !=', $user_id);
        }
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
    public function getCouponIds($cp_name=array())
    {
        $this->db->select('entity_id');
        $this->db->where_in('name',$cp_name);
        $this->db->where('status',1);        
        $res =  $this->db->get('coupon')->result();
        return $res;
    }
    public function checkCouponwithid($coupon,$order_delivery='')
    {
        $this->db->where('entity_id',$coupon);
        $this->db->where('status',1);
        if(strtolower($order_delivery)=='dinein'){
            $this->db->where('coupon_type','dine_in');
        }
        return $this->db->get('coupon')->first_row();
    }
    public function inserBatch($tblname,$data){
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
    public function getrestaurant_foodtype($restaurant_id, $language_slug)
    {
        $this->db->select('food_type');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        $foodarr = array();
        if($result && !empty($result))
        {
            $food_type = $result->food_type;
            if($food_type!='')
            {
                $foodarr = explode(",",$food_type);
            }
        }
        $foodarr_res = array();
        if(!empty($foodarr))
        {
            $this->db->select('name, entity_id as food_type_id, content_id,food_type_image');
            $this->db->where_in('entity_id',$foodarr);
            $this->db->order_by("name", "ASC");
            $foodarr_res = $this->db->get('food_type')->result();
            if($foodarr_res && !empty($foodarr_res))
            {
                foreach ($foodarr_res as $key => $value) {
                    $value->food_type_image = (file_exists(FCPATH.'uploads/'.$value->food_type_image) && $value->food_type_image != '') ? image_url.$value->food_type_image : '';
                }
            }          
        }       
        return $foodarr_res;
    }
    public function getRestaurantCreditFee($content_id,$language_slug)
    {
        $this->db->select("res.content_id, res.entity_id, res.creditcard_fee, res.creditcard_fee_type,res.is_creditcard_fee_enable");
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id');
        $this->db->join('review','res.content_id = review.restaurant_content_id and (review.order_user_id=0 OR review.order_user_id is NULL) and review.status=1','left');
        $this->db->join('currencies','res.currency_id = currencies.currency_id','left');
        $this->db->where('res.content_id',$content_id);
        $this->db->where('res.language_slug',$language_slug);
        $this->db->group_by('res.entity_id');
        $result =  $this->db->get('restaurant as res')->first_row();
        return $result;
    }
}
?>