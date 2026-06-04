<?php
class Home_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    //server side check email exist
    public function checkEmail($Email)
    {
        $this->db->where('email',$Email);
        $this->db->where('is_deleted',0);
        $this->db->where('user_type','User');
        return $this->db->get('users')->num_rows();   
    }
    public function checkAllEmail($Email)
    {
        $this->db->where('email',$Email);
        $this->db->where('user_type','User');
        return $this->db->get('users')->num_rows();   
    }
    //server side check email exist
    public function checkPhone($Phone)
    {
        $this->db->where('mobile_number',$Phone);
        $this->db->where('is_deleted',0);
        $this->db->where('user_type','User');
        return $this->db->get('users')->num_rows();  
    }
    // validation for mobile number 
    public function mobileCheck($mobile_number,$phncode){
        //return $this->db->get_where('users',array('is_deleted'=>0,'mobile_number'=>$mobile_number,'phone_code'=>$phncode))->num_rows();
        //return $this->db->get_where('users',array('mobile_number'=>$mobile_number,'phone_code'=>$phncode,'user_type'=>'User'))->num_rows();
        $roles = array('User','Agent');
        $this->db->where('mobile_number',$mobile_number);
        $this->db->where('phone_code',$phncode);
        $this->db->where_in('user_type',$roles);
        return $this->db->get('users')->num_rows();
    }
    // get restaurant details
    public function getRestaurants($order_mode=NULL){ 
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode");
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->group_by('restaurant.content_id');
        //New code for search order mode Start
        if (!empty($order_mode)){
            if($order_mode=='Both'){
                $this->db->where('restaurant.order_mode','PickUp,Delivery');
            }else{
                $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                $this->db->where($wherefindom);
            }
        }
        //New code for search order mode end
        $result = $this->db->get_where('restaurant',array('status'=>1))->result_array();
        $finalData = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day)){
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
            }
            $content_id = array();
            $RestDataArr = array();
            foreach ($result as $key => $value) { 
                $content_id[] = $value['content_id'];
                $RestDataArr[$value['content_id']] = array(
                    'content_id' =>$value['content_id'],
                    'restaurant_slug' =>$value['restaurant_slug'],
                    'restaurant_id'=>$value['restaurant_id']
                );
            }    
            if(!empty($content_id)){
                $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode");
                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                $this->db->where_in('restaurant.content_id',$content_id);
                $this->db->where('restaurant.language_slug',$language_slug);
                //New code for search order mode Start
                if (!empty($order_mode)){
                    if($order_mode=='Both'){
                        $this->db->where('restaurant.order_mode','PickUp,Delivery');
                    }else{
                        $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                        $this->db->where($wherefindom);
                    }
                }
                //New code for search order mode end
                $this->db->where('restaurant.status',1);
                $this->db->group_by('restaurant.content_id');
                $this->db->order_by('restaurant.entity_id');
                $restaurantLng = $this->db->get('restaurant')->result_array();
                foreach ($restaurantLng as $key => $value) {
                    $timing = $value['timings'];
                    if($timing){
                       $timing =  unserialize(html_entity_decode($timing));
                       $newTimingArr = array();
                        $day = date("l");
                        foreach($timing as $keys=>$values) {
                            $day = date("l");
                            if($keys == strtolower($day)){
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
                    $restaurantLng[$key]['timings'] = $newTimingArr[strtolower($day)];
                    $restaurantLng[$key]['image'] = ($value['image'])?$value['image']:'';
                }

                foreach ($restaurantLng as $key => $value)
                {
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
                        'image'=> $value['image'],                    
                        'timings'=> $value['timings'],                
                        'language_slug'=> $value['language_slug'],
                        'content_id' =>$RestDataArr[$value['content_id']]['content_id'],
                        'restaurant_slug' =>$RestDataArr[$value['content_id']]['restaurant_slug'],
                        'restaurant_id'=>$RestDataArr[$value['content_id']]['restaurant_id']
                    );
                }
            }
        } 
        return $finalData;
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
            $this->db->where('review.restaurant_content_id',$restaurant_content_id);
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
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
    // get all categories
    public function getAllCategories()
    {
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select("category.entity_id,category.name,category.image,category.image");
        $this->db->join('restaurant_menu_item','restaurant_menu_item.category_id = category.entity_id');
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id');
        $this->db->where('restaurant_menu_item.status',1);
        $this->db->where('restaurant_menu_item.stock',1);
        $this->db->where('restaurant.status',1);        
        $this->db->where('category.language_slug',$language_slug);
        $this->db->order_by('sequence','ASC');
        $this->db->group_by('category.content_id');
        return $this->db->get_where('category',array('category.status'=>1))->result();
    }
    // search restaurant details
    public function searchRestaurants($category_id,$order_mode){
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.content_id as res_content_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.enable_hours");
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->where('restaurant_menu_item.category_id',$category_id);
        $this->db->where('restaurant_menu_item.status',1);
        $this->db->where('restaurant_menu_item.stock',1);
        if (!empty($order_mode)){
            $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
            $this->db->where($wherefindom);
        }
        $this->db->where('restaurant.status',1);
        $this->db->group_by('restaurant.entity_id');
        $result = $this->db->get('restaurant_menu_item')->result_array();
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
                $result[$key]['image'] = ($value['image'])?$value['image']:'';
            }
        } 
        return $result;
    }
    // get coupons
    public function getAllCoupons($res_content_ids = array()) {
        $this->db->simple_query('SET SESSION group_concat_max_len=100000000');
        $this->db->select('coupon.entity_id,coupon.name,coupon.image,restaurant.restaurant_slug,GROUP_CONCAT(coupon_restaurant_map.restaurant_id ORDER BY coupon_restaurant_map.restaurant_id ASC SEPARATOR ",") as restaurant_ids, coupon.maximaum_use_per_users,coupon.maximaum_use,coupon.coupon_for_newuser,coupon.coupon_type');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        if(!empty($res_content_ids)) {
            $this->db->where_in('coupon_restaurant_map.restaurant_id',array_unique($res_content_ids));
        }
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        $this->db->where('(coupon.coupon_type != "discount_on_items")');
        $this->db->where('(coupon.coupon_type != "discount_on_categories")');
        $this->db->where('coupon.coupon_type != ',"dine_in");
        $this->db->where('coupon.status',1);
        $this->db->where('coupon.show_in_home',1);
        $this->db->order_by('coupon.entity_id','desc');
        $this->db->group_by('coupon.entity_id');
        $couponstemp = $this->db->get('coupon')->result();
        //Code for filter array with requirement :: Start
        $return = array();
        $cntt=0;
        if($couponstemp && !empty($couponstemp))
        {
            for($i=0;$i<count($couponstemp);$i++)
            {
                $flag_cnt = 'yes'; $user_chk =0;
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

                    if(($couponstemp[$i]->coupon_type=='free_delivery' && $user_chkcpn=='no' && $couponstemp[$i]->coupon_for_newuser=='1') || ($couponstemp[$i]->coupon_type=='user_registration' && $UserID==0) || ($couponstemp[$i]->coupon_type=='user_registration' && $user_chk>0))
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
        if(!empty($return)) {
            foreach ($return as $key => $value) {
                $value->restaurant_ids = ($value->restaurant_ids) ? explode(',', str_replace(' ', '', $value->restaurant_ids)) : array();
            }
        }        
        return $return;
    }
     /*
     * Insert / Update facebook profile data into the database
     * @param array the data for inserting into the table
     */
    //facebook login
    public function checkUser($userData = array()){
        if(!empty($userData)){
            //check whether user data already exists in database with same oauth info
            $this->db->select('entity_id');
            $this->db->from('users');
            $this->db->where('login_type',$userData['login_type']); 
            $this->db->where('social_media_id',$userData['social_media_id']);
            $prevQuery = $this->db->get();
            $prevCheck = $prevQuery->num_rows();
            
            if($prevCheck > 0){
                $prevResult = $prevQuery->row_array();
                //update user data
                $userData['updated_date'] = date("Y-m-d H:i:s");
                $update = $this->db->update('users', $userData, array('entity_id' => $prevResult['entity_id']));
                //get user ID
                $userID = $prevResult['entity_id'];
            }else{
                //insert user data
                $userData['mobile_number'] = '';
                $userData['status']  = 1;
                //$userData['active'] = '';
                $insert = $this->db->insert('users', $userData);
                //get user ID
                $userID = $this->db->insert_id();
            }
        }
        $this->db->where('entity_id',$userID);
        $this->db->where("user_type",'User');
        return $this->db->get('users')->first_row(); //return user ID
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
    //restaurant sort/filter section :: start
    public function getRestaurantsOnFilter($resdish = NULL, $latitude = NULL, $longitude = NULL, $minimum_range = NULL, $maximum_range = NULL, $food_type = '', $order_mode = NULL, $foodtype_quicksearch = '', $filter_by = 'distance', $limit,$offset,$pagination = NULL, $res_type = NULL, $category_id = NULL, $offers_free_delivery = 0, $availability_filter = NULL) {
        $language_slug = $this->session->userdata('language_slug');
        $foodtypesearch = '';
        if(trim($foodtype_quicksearch) != '' && trim($food_type) == '') {
            $foodtypesearch = trim($foodtype_quicksearch);
        } else if(trim($foodtype_quicksearch) == '' && trim($food_type) != '') {
            $foodtypesearch = trim($food_type);
        } else {
            if(trim($foodtype_quicksearch) != '') {
                $foodtypesearch = trim($foodtype_quicksearch);
            }
            if(trim($food_type) != '') {
                $foodtypesearch = trim($food_type);
            }
        }

        //Code for free delviery coupon falg check :: Start
        $UserID = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
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
        else
        {
            $user_chkcpn = 'no';
        }
        //Code for free delviery coupon falg check :: End

        if (!empty($resdish)) {
            $where = "(address.address LIKE '%".$this->common_model->escapeString($resdish)."%' OR address.landmark LIKE '%".$this->common_model->escapeString($resdish)."%' OR restaurant.name LIKE '%".$this->common_model->escapeString($resdish)."%')";
            //$where = "(restaurant.name LIKE '%".$this->common_model->escapeString($resdish)."%')";
            //Query 1 code start
            $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode,restaurant.food_type");
            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
            $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1','left');
            if($offers_free_delivery && $offers_free_delivery > 0) {
                $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
                $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
                $this->db->where('coupon.coupon_type',"free_delivery");
                $this->db->where("(find_in_set ('Delivery', restaurant.order_mode))");
                $this->db->where('coupon.start_date <=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.end_date >=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.status',1);
                if($user_chkcpn=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
            }
            $this->db->where($where);
            $this->db->where('restaurant.status',1);
            $this->db->where('restaurant.language_slug',$language_slug);
            if(trim($availability_filter) != '') {
                $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
                $this->db->where('category.status',1);
                $availabilityarr = explode(",",$availability_filter);
                $availabilityarr = array_filter($availabilityarr);
                if(!empty($availabilityarr)) {
                    $fdtcnt = 0; $availability_where = '(';
                    foreach($availabilityarr as $keyf => $valuef) {
                        if($fdtcnt > 0) {
                            $availability_where .= " OR ";
                        }
                        $availability_where .= "(find_in_set ('".$valuef."', restaurant_menu_item.availability))";
                        $fdtcnt++;
                    }
                    $availability_where .= ')';
                    if($fdtcnt > 0) {
                        $this->db->where($availability_where);
                    }
                }
            }
            if(trim($category_id) != '') {
                $category_ids = explode(',', trim($category_id));
                $this->db->where_in('restaurant_menu_item.category_id',$category_ids);
            }
            if($foodtypesearch != '') {
                $wherefindcn = $this->getfoodtyepequery($foodtypesearch);
                $this->db->where($wherefindcn);
            }            
            if (!empty($order_mode)) {
                if($order_mode=='Both') {
                    $this->db->where('restaurant.order_mode','PickUp,Delivery');
                } else {
                    $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                    $this->db->where($wherefindom);
                }
            }
            $this->db->group_by('restaurant.content_id');
            $this->db->from('restaurant');            
            $query1 = $this->db->get_compiled_select();
            $query = $this->db->query($query1);
            $result = $query->result_array();                        
        } else {
            $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug,restaurant.enable_hours,restaurant.branch_entity_id,restaurant.order_mode,restaurant.food_type");
            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
            $this->db->join('restaurant_menu_item','restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1','left');
            if($offers_free_delivery && $offers_free_delivery > 0) {
                $this->db->join('coupon_restaurant_map','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
                $this->db->join('coupon','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
                $this->db->where('coupon.coupon_type',"free_delivery");
                $this->db->where("(find_in_set ('Delivery', restaurant.order_mode))");
                $this->db->where('coupon.start_date <=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.end_date >=',date('Y-m-d H:i:s'));
                $this->db->where('coupon.status',1);
                if($user_chkcpn=='no'){
                  $this->db->where('coupon.coupon_for_newuser','0');  
                }
            }
            $this->db->where('restaurant.language_slug',$language_slug);
            $this->db->where('restaurant.status',1);
            if(trim($availability_filter) != '') {
                $this->db->join('category','restaurant_menu_item.category_id = category.entity_id','left');
                $this->db->where('category.status',1);
                $availabilityarr = explode(",",$availability_filter);
                $availabilityarr = array_filter($availabilityarr);
                if(!empty($availabilityarr)) {
                    $fdtcnt = 0; $availability_where = '(';
                    foreach($availabilityarr as $keyf => $valuef) {
                        if($fdtcnt > 0) {
                            $availability_where .= " OR ";
                        }
                        $availability_where .= "(find_in_set ('".$valuef."', restaurant_menu_item.availability))";
                        $fdtcnt++;
                    }
                    $availability_where .= ')';
                    if($fdtcnt > 0) {
                        $this->db->where($availability_where);
                    }
                }
            }
            if(trim($category_id) != '') {
                $category_ids = explode(',', trim($category_id));
                $this->db->where_in('restaurant_menu_item.category_id',$category_ids);
            }
            if($foodtypesearch != '') {
                $wherefindcn = $this->getfoodtyepequery($foodtypesearch);
                $this->db->where($wherefindcn);
            }            
            if (!empty($order_mode)) {
                if($order_mode=='Both') {
                    $this->db->where('restaurant.order_mode','PickUp,Delivery');
                } else {
                    $wherefindom = "(find_in_set ('".$order_mode."', restaurant.order_mode))";
                    $this->db->where($wherefindom);
                }
            }
            $this->db->group_by('restaurant.content_id');
            $result = $this->db->get_where('restaurant',array('restaurant.status'=>1))->result_array();
        }
        $finalData = array();
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
                if($timing) {
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day)) {
                            $close = 'Closed';
                            if($value['enable_hours']=='1') {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open']):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close']):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'Closed';
                                if (!empty($values['open']) && !empty($values['close'])) {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open']),$this->common_model->getZonebaseTime($values['close']));
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
                } else {
                    $newTimingArr[strtolower($day)]['closing'] = 'close';
                    $newTimingArr[strtolower($day)]['open'] = '';
                    $newTimingArr[strtolower($day)]['close'] ='';
                    $newTimingArr[strtolower($day)]['off'] = 'close';
                }
                $result[$key]['timings'] = $newTimingArr[strtolower($day)];
                $result[$key]['image'] = ($value['image'])?$value['image']:'';

                $rest_name = $value['name'];

                $finalData[$value['content_id']] = array(
                    'MainRestaurantID'=> $value['restaurant_id'],
                    'name'=> $rest_name,
                    'address'=> $value['address'],
                    'landmark'=> $value['landmark'],
                    'latitude'=> $value['latitude'],
                    'longitude'=> $value['longitude'],
                    'image'=> $result[$key]['image'],                    
                    'timings'=> $result[$key]['timings'],                
                    'language_slug'=> $value['language_slug'],
                    'content_id' =>$value['content_id'],
                    'restaurant_slug' =>$value['restaurant_slug'],
                    'restaurant_id'=>$value['restaurant_id'],
                    'distance'=>0,
                    'food_type'=>$value['food_type']
                );

                $ratings = $this->getRestaurantReview($value['content_id']);
                $review_data = $this->getReviewsPagination($value['content_id'],review_count,1);
                $finalData[$value['content_id']]['restaurant_reviews'] = $review_data['reviews'];
                $finalData[$value['content_id']]['restaurant_reviews_count'] = $review_data['review_count'];
                $finalData[$value['content_id']]['ratings'] = $ratings;
                $couponstemp = $this->getCouponsForHome($value['content_id'],$value['order_mode']);                               
                //Code for filter array with requirement :: Start
                $restaurant_couponsarr = array();
                $cntt=0;
                if($couponstemp && !empty($couponstemp))
                {
                    for($i=0;$i<count($couponstemp);$i++)
                    {   
                        $flag_cnt = 'yes'; $user_chk = 0;
                        $UserID = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
                        $UserType = ($this->session->userdata('UserType'))?$this->session->userdata('UserType'):'User';
                        $checkCnt = $this->common_model->checkUserUseCountCoupon($UserID,$couponstemp[$i]->coupon_id);
                        if($checkCnt >= $couponstemp[$i]->maximaum_use_per_users && $couponstemp[$i]->maximaum_use_per_users>0 && $UserType=='User')
                        {
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
                            if($this->session->userdata('UserType') == 'Agent'){
                                $user_chkcpn = 'no';
                            }
                            $order_mode_arr = explode(',', $value['order_mode']);                      
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
                //Code for filter array with requirement :: End               
                $finalData[$value['content_id']]['restaurant_coupons'] = $restaurant_couponsarr;
            }            
        }
        
        $finalArray = array();
        if (!empty($finalData) && !empty($latitude) && !empty($longitude)) { 
            foreach ($finalData as $key => $value) {
                $latitude1 = $latitude;
                $longitude1 = $longitude;
                $latitude2 = $value['latitude'];
                $longitude2 = $value['longitude'];
                $earth_radius = 3959;

                $dLat = deg2rad($latitude2 - $latitude1);  
                $dLon = deg2rad($longitude2 - $longitude1);  
        
                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
                $c = 2 * asin(sqrt($a));  
                $d = $earth_radius * $c;
                $finalData[$key]['distance'] = $d;
                if (isset($minimum_range) && isset($maximum_range)) { 
                    if ($minimum_range <= $d && $d <= $maximum_range) {
                        $finalArray[] = $value;
                    } else { 
                        unset($finalData[$key]);
                    }
                } else {
                    $range = $this->common_model->getRange();
                    if($order_mode == 'PickUp') {
                        $maximum_range = (float)$range[2]->OptionValue;
                    } else {
                        $maximum_range = (float)$range[1]->OptionValue;
                    }
                    $minimum_range = (float)$range[0]->OptionValue;

                    if ($minimum_range <= $d && $d <= $maximum_range) { 
                        $finalArray[] = $value;
                    } else {
                        unset($finalData[$key]);
                    }
                }
            }
        }        
        // sorting -- open/close
        if($filter_by == 'rating' && !empty($finalData)) {
            array_multisort(array_column($finalData, "ratings"), SORT_DESC, $finalData );            
        }
        if($filter_by == 'distance' && !empty($finalData)) {
            array_multisort(array_column($finalData, "distance"), SORT_ASC, $finalData );
        }
        usort($finalData, function($a, $b) use ($filter_by)
        {
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
    // get food type
    public function getFoodType($food_type = array())
    {
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
        $language_slug = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):'en';
        $this->db->select('food_type.name,food_type.entity_id,food_type.is_veg,food_type.food_type_image');
        $this->db->join('restaurant as res','(find_in_set (food_type.entity_id, res.food_type))');
        $this->db->where('food_type.status',1);
        $this->db->where('food_type.language_slug',$language_slug);
        if(!empty($food_type_arr)) {
            $this->db->where_in('food_type.entity_id',array_unique($food_type_arr));
        }
        $this->db->order_by('food_type.name', 'ASC');
        $this->db->group_by('food_type.entity_id');
        return $this->db->get('food_type')->result();        
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
    //get coupon list
    public function getCouponsForHome($restaurant_content_id,$order_mode) {
        $order_mode_arr = explode(',', $order_mode);
        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.coupon_type,coupon.max_amount, coupon.maximaum_use_per_users, coupon.maximaum_use, coupon.use_with_other_coupons,coupon.coupon_for_newuser');
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
    //restaurant sort/filter section :: end
}