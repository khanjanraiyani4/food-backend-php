<?php
require_once  APPPATH.'/third_party/Twilio/vendor/autoload.php';
use Twilio\Rest\Client;

class Common_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
        $this->db->query("set session sql_mode=''");
        if (!defined('main_color'))
        {
            /*$this->db->where('OptionSlug','main_color');
            $this->db->or_where('OptionSlug','txt_color');*/
            /*$this->db->or_where('OptionSlug','default_latitude');
            $this->db->or_where('OptionSlug','default_longitude');*/
            //$this->db->or_where('OptionSlug','DRIVER_NEAR_KM');
            /*$this->db->or_where('OptionSlug','USER_NEAR_KM');*/
            $DISTANCE_CALCVAL = '6371'; //For Kilometer
            $this->db->select('OptionValue,OptionSlug');
            $this->db->where_in('OptionSlug',array('gradient_light_color','gradient_dark_color','google_key','google_webclient_id','country','maximum_range','maximum_range_pickup','facebook','twitter','linkedin','instagram','website_header_script','website_body_script','website_footer_script','currency','cancel_order_timer','distance_in'));
            $styles = $this->db->get('system_option')->result();
        
            foreach ($styles as $key => $value) {
                if($value->OptionSlug == "maximum_range" ){
                    define("NEAR_KM", $value->OptionValue);
                    define("DRIVER_NEAR_KM", $value->OptionValue);
                    define("USER_NEAR_KM", $value->OptionValue);
                }else if($value->OptionSlug == "currency"){
                    define("DEFAULT_CURRENCY_ID", $value->OptionValue);
                }else if($value->OptionSlug == "cancel_order_timer"){
                    define("SECONDS_TO_CANCEL", $value->OptionValue);
                }else if($value->OptionSlug == "distance_in"){
                    //Code for distance calculation value set dynamic :: Start
                    if($value->OptionValue=='0'){
                        $DISTANCE_CALCVAL = '3959'; //For mile
                    }//End                    
                    
                }else {
                    define($value->OptionSlug, $value->OptionValue);
                }
            }
            //Code for distance calculation value set dynamic :: Start
            define("DISTANCE_CALCVAL", $DISTANCE_CALCVAL);           
        }
        //Code for deactive user log out if loging :: Start
        if($this->session->userdata('AdminUserID'))
        {
            $UserTypeval = $this->session->userdata('AdminUserType');
            $this->db->select('entity_id,status,is_deleted');
            $this->db->where('entity_id',$this->session->userdata('AdminUserID'));                            
            $result = $this->db->get('users')->first_row();            
            if(($result->status=='0' || $result->is_deleted=='1') && !empty($result))
            {
                if($UserTypeval!='User')
                {
                    $this->session->unset_userdata('AdminUserID');
                    $this->session->unset_userdata('adminFirstname');
                    $this->session->unset_userdata('adminLastname');
                    $this->session->unset_userdata('adminemail');
                    $this->session->unset_userdata('is_admin_login');  
                    $this->session->unset_userdata('AdminUserType');  
                    $this->session->unset_userdata('parent_adminid');
                    $this->session->unset_userdata('admincountry');  
                    $this->session->unset_userdata('restaurant'); 
                    //$this->session->sess_destroy();
                    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
                    $this->output->set_header("Pragma: no-cache");
                    redirect(base_url().ADMIN_URL.'/home', 'refresh');
                    exit;
                }
                else
                {
                    $this->session->unset_userdata('AdminUserID');
                    $this->session->unset_userdata('userFirstname');
                    $this->session->unset_userdata('userLastname');
                    $this->session->unset_userdata('userEmail');
                    $this->session->unset_userdata('userPhone');
                    $this->session->unset_userdata('is_user_login'); 
                    $this->session->unset_userdata('package_id');
                    $this->session->unset_userdata('social_media_id');
                    $this->session->unset_userdata('previous_url');
                    delete_cookie('cart_details');
                    delete_cookie('cart_restaurant');          
                    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
                    $this->output->set_header("Pragma: no-cache");
                    redirect(base_url().'/home', 'refresh');
                    exit;
                }
            }
            //Code for deactive user log out if loging :: End
        }
        //Code for if user delete from admin then logout :: Start
        if($this->session->userdata('UserID'))
        {
            $UserTypeval = $this->session->userdata('UserType');
            $this->db->select('entity_id,status,is_deleted');
            $this->db->where('entity_id',$this->session->userdata('UserID'));
            $this->db->where('status',1);            
            $this->db->where('is_deleted',0);
            $res_loguserchk = $this->db->get('users')->first_row(); 
            if($res_loguserchk && !empty($res_loguserchk))
            {
            }
            else
            {
                $this->userlogout();
            }
        }
        //Code for if user delete from admin then logout :: End

        if (empty($this->session->userdata('language_slug'))) {
            $default_lang = $this->getdefaultlang();
            $this->session->set_userdata('language_directory',$default_lang->language_directory);
            $this->config->set_item('language', $default_lang->language_directory);
            $this->session->set_userdata('language_slug',$default_lang->language_slug);
        }
    }
    //get notification count 
    public function getNotificationCount(){
        $this->db->select('SUM(order_count) + SUM(dinein_count) as order_count');        
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('order_notification')->first_row();
    }
    public function get_delivery_pickup_order_notification_count(){
        $this->db->select('order_count');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('order_notification')->first_row();
    }
    public function get_dinein_order_notification_count(){
        $this->db->select('dinein_count');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('order_notification')->first_row();
    }
    //get notification count 
    public function getEventNotificationCount(){ 
        $this->db->select('event_count');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('event_notification')->first_row();
    }
    public function getLanguages()
    {
        $this->db->where('active',1);
        return $this->db->get_where('languages')->result();
    }    
    public function getCmsPages($language_slug,$cms_slug=NULL)
    {
        if (!empty($cms_slug)) {
            $array = array('language_slug'=>$language_slug,'status'=>1,'CMSSlug'=>$cms_slug);
        }
        else {
            $array = array('language_slug'=>$language_slug,'status'=>1);
        } 
        return $this->db->get_where('cms',$array)->result();
    }
    public function getFirstLanguages($slug){
        return $this->db->get_where('languages',array('language_slug'=>$slug))->first_row();
    }
    //get default lang
    public function getdefaultlang()
    {
        return $this->db->get_where('languages',array('language_default'=>1))->first_row();
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
        $this->db->where('end_date >=',date('Y-m-d H:i:s'));
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
    //get table data
    public function getRestaurantinSession($tblname,$UserID)
    {
        /*$this->db->where('created_by',$UserID);*/
        $this->db->where('restaurant_owner_id',$UserID);
        return $this->db->get($tblname)->result_array();
    }

    // get all the currencies
    public function getCountriesCurrency(){
        return $this->db->get('currencies')->result_array();
    }
    // get the currency id from currency name
    public function getCurrencyID($currency_name){
        return $this->db->get_where('currencies',array('currency_name'=>$currency_name))->first_row();
    }
    // get currency symbol
    public function getCurrencySymbol($currency_id) {
        return $this->db->get_where('currencies',array('currency_id'=>$currency_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrency($content_id) {
        return $this->db->get_where('restaurant',array('content_id'=>$content_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrencySymbol($restaurant_id) {
        $this->db->select('currencies.currency_symbol,currencies.currency_code');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        return $this->db->get_where('restaurant',array('entity_id'=>$restaurant_id))->first_row();
    }
    // get currency symbol
    public function getEventCurrencySymbol($entity_id) {
        $this->db->select('currencies.currency_symbol');
        $this->db->join('restaurant','event.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        return $this->db->get_where('event',array('event.entity_id'=>$entity_id))->first_row();
    }
    /****************************************
    Function: addData, Add record in table
    $tablename: Name of table    
    $data: array of data
    *****************************************/
    public function addData($tablename,$data)
    {   
        $this->db->insert($tablename,$data);            
        return $this->db->insert_id();
    }

    /****************************************
    Function: updateData, Update records in table
    $tablename: Name of table    
    $data: array of data
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function updateData($tablename,$data,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->update($tablename,$data);
        return $this->db->affected_rows();
    }

    /****************************************
    Function: updateData, Delete records from table
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function deleteData($tablename,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->delete($tablename);        
    }

    /****************************************
    Function: getSingleRow, get first row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getSingleRow($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->first_row();
    }

    /****************************************
    Function: getMultipleRows, get multiple row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getMultipleRows($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->result();
    }

    /****************************************
    Function: getRowsMultipleWhere, get row from table in Object format using multiple WHERE clause
    $tablename: Name of table        
    $wherearray: where field array    
    ****************************************/
    public function getRowsMultipleWhere($tablename,$wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->result();
    }

    public function getSingleRowMultipleWhere($tablename,$wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->first_row();
    }

      /****************************************
    Function: getAllRows, get row from table in array object format 
    $tablename: Name of table        
    $wherearray: where field array    
    ****************************************/
    public function getAllRows($tablename)
    {
        return $this->db->get($tablename)->result();
    }
        /****************************************
    Function: getAllRecordArray, get row from table in array format 
    $tablename: Name of table        
    ****************************************/
    public function getAllRecordArray($tablename)
    {
        return $this->db->get($tablename)->result_array();
    }
    /****************************************
    Function: deleteInsertRecord, Delete existing records and insert new records
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    $data: array of data that need to insert
    ****************************************/
    public function deleteInsertRecord($tablename,$wherefieldname,$wherefieldvalue,$data)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->delete($tablename);
        
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: insertBatch, Bulk insert new records
    $tablename: Name of table        
    $data: array of data that need to insert
    ****************************************/
    public function insertBatch($tablename,$data)
    {
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: updateBatch, Bulk update records
    $tablename: Name of table        
    $data: array of data that need to insert
    $fieldname: Field name used as WHERE Clause
    ****************************************/
    public function updateBatch($tablename,$data,$fieldname)
    {
        return $this->db->update_batch($tablename, $data, $fieldname);
    }
    
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
                }
                $avg_rating = number_format($average,1);
            }
        } else {
            $avg_rating = number_format($res_rating->restaurant_rating,1);
        }
        return $avg_rating;
    }

    public function getLang($language_slug){
        $this->db->select('language_name,language_slug');
        $this->db->where('language_slug',$language_slug);
        return $this->db->get('languages')->first_row();
    }

    public function getUsersNotification($user_id,$status=NULL)
    {
        $notifications = array();
        // orders
        $this->db->where('user_order_notification.user_id',$user_id);
        if ($status == 'unread') {
            $this->db->where('user_order_notification.view_status',0);
        }
        $this->db->join('order_master','order_master.entity_id=user_order_notification.order_id');
        $this->db->where('order_master.order_delivery != ','DineIn');
        $this->db->order_by('user_order_notification.datetime','desc');
        $orders = $this->db->get('user_order_notification')->result();
        if ( !empty($orders) ) {
            foreach ($orders as $key => $value)
            {
                $notification_slugval = $value->notification_slug;
                if($notification_slugval=='order_ongoing')
                { 
                    $this->db->select('order_delivery');
                    $this->db->where('entity_id',$value->order_id);
                    $orderarr = $this->db->get('order_master')->first_row();
                    if(strtolower($orderarr->order_delivery)=='pickup')
                    {
                        $order_notification_slug = 'order_is_readynoti';
                        $notification_slugval = $order_notification_slug;
                    }
                }
                if($notification_slugval == 'order_canceled') {
                    $this->db->select('cancel_reason');
                    $this->db->where('entity_id',$value->order_id);
                    $orderarr = $this->db->get('order_master')->first_row();
                    if(strtolower($orderarr->cancel_reason)=='due to a lack of supervision.')
                    {
                        $order_notification_slug = 'order_auto_cancelled';
                        $notification_slugval = $order_notification_slug;
                    }
                }
                $notifications[] = array(
                    'notification_type' => 'order',
                    'notification_type_id' => $value->user_notification_id,
                    'entity_id' => $value->order_id,
                    'user_id' => $value->user_id,
                    'transaction_id' => ($value->transaction_id)?$value->transaction_id:NULL,
                    'notification_slug' => $notification_slugval,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        // events
        $this->db->where('user_id',$user_id);
        if ($status == 'unread') {
            $this->db->where('view_status',0);
        }
        $this->db->order_by('datetime','desc');
        $events = $this->db->get('user_event_notifications')->result();
        if ( !empty($events) ) {
            foreach ($events as $key => $value) {
                $notifications[] = array(
                    'notification_type' => 'event',
                    'notification_type_id' => $value->event_notification_id,
                    'entity_id' => $value->event_id,
                    'user_id' => $value->user_id,
                    'notification_slug' => $value->notification_slug,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        //table booking notification
        $this->db->where('user_id',$user_id);
        if ($status == 'unread') {
            $this->db->where('view_status',0);
        }
        $tables = $this->db->get('user_table_notifications')->result();
        if ( !empty($tables) ) {
            foreach ($tables as $key => $value) {
                $notifications[] = array(
                    'notification_type' => 'table',
                    'notification_type_id' => $value->table_notification_id,
                    'entity_id' => $value->table_id,
                    'user_id' => $value->user_id,
                    'notification_slug' => $value->notification_slug,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        // sort array in descending order
        usort($notifications, function ($a, $b) {
            $dateA = date('Y-m-d H:i:s',strtotime($a['datetime']));
            $dateB = date('Y-m-d H:i:s',strtotime($b['datetime']));
            // descending ordering, use `<=` for ascending
            if($dateA == $dateB){
                //descending
                return ($a['notification_type_id']>$b['notification_type_id'])?-1:1;
            } else {
                //descending
                return ($dateA>$dateB)?-1:1;
            }
        });
        return $notifications;
    }
    
    //get menu items
    public function getMenuItem($entity_id,$restaurant_id){
        $language_slug = $this->session->userdata('language_slug');
        $ItemDiscount = $this->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));

        $this->db->select('menu.restaurant_id,menu.is_deal,menu.entity_id as menu_id,menu.content_id as menu_content_id,menu.status,menu.name,menu.price,menu.menu_detail,menu.image,menu.is_veg,availability,c.name as category,c.entity_id as category_id,add_ons_master.add_ons_name,add_ons_master.add_ons_price,add_ons_category.name as addons_category,menu.check_add_ons,add_ons_category.entity_id as addons_category_id,add_ons_master.add_ons_id,add_ons_master.is_multiple,c.content_id as cat_content_id,res.content_id as res_content_id');
        $this->db->join('category as c','menu.category_id = c.entity_id','left');
        $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
        $this->db->join('add_ons_master','menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1','left');
        $this->db->join('add_ons_category','add_ons_master.category_id = add_ons_category.entity_id','left');
        $this->db->where('menu.restaurant_id',$restaurant_id);
        $this->db->where('menu.language_slug',$language_slug);
        $this->db->where('menu.entity_id',$entity_id);
        $this->db->where('menu.status',1);
        $this->db->where('c.status',1);
        //$this->db->where('menu.stock',1);
        $result = $this->db->get('restaurant_menu_item as menu')->result();

        $restaurant_data = $this->getSingleRow('restaurant','entity_id',$restaurant_id);
        $category_discount = '';
        if(!empty($restaurant_data) && $restaurant_data->content_id){
            $category_discount = $this->getCategoryDiscount($restaurant_data->content_id);
        }
        $menu = array();
        $item_not_appicable_for_item_discount = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                //offer price start
                $offer_price = '';
                /*Begin::Category Discount Coupon Check*/
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
                /*End::Category Discount Coupon Check*/
                if(!empty($ItemDiscount)) {
                    foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                        if(!empty($cpnvalue['itemDetail'])) {
                            if(in_array($value->menu_content_id,$cpnvalue['itemDetail']) && !in_array($value->menu_content_id, $item_not_appicable_for_item_discount)){
                                if($cpnvalue['max_amount'] <= $value->price){ 
                                    if($cpnvalue['amount_type'] == 'Percentage'){
                                        $offer_price = $value->price - (($value->price * $cpnvalue['amount'])/100);
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
                $image = ($value->image)?image_url.$value->image:'';
                $total = 0;
                if($value->check_add_ons == 1){
                    if(!isset($menu[$value->category_id]['items'][$value->menu_id])){
                       $menu[$value->category_id]['items'][$value->menu_id] = array();
                       $menu[$value->category_id]['items'][$value->menu_id] = array('restaurant_id'=>$value->restaurant_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' => $value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
                    }
                    if($value->is_deal == 1){
                        if(!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'])){
                           $i = 0;
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'] = array();
                           $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['is_multiple'] = $value->is_multiple;
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
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id'=>$value->add_ons_id,'add_ons_name'=>$value->add_ons_name,'add_ons_price'=>$value->add_ons_price);
                        $i++;
                    }
                }else{
                    $menu[$value->category_id]['items'][]  = array('restaurant_id'=>$value->restaurant_id,'menu_id'=>$value->menu_id,'name' => $value->name,'price' =>$value->price,'offer_price'=>$offer_price,'menu_detail' => $value->menu_detail,'image'=>$image,'availability'=>$value->availability,'is_veg'=>$value->is_veg,'is_customize'=>$value->check_add_ons,'is_deal'=>$value->is_deal,'status'=>$value->status);
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
    // get Cart items
    public function getCartItems($cart_details,$cart_restaurant){
        $cartItems = array();
        $cartTotalPrice = 0;
        if (!empty($cart_details)) {
            foreach (json_decode($cart_details) as $key => $value) { 
                $details = $this->getMenuItem($value->menu_id,$cart_restaurant);
                if (!empty($details)) {
                    if ($details[0]['items'][0]['is_customize'] == 1) {
                        $addons_category_id = $add_onns_id = array();
                        if($value->addons && !empty($value->addons)){
                            $addons_category_id = array_column($value->addons, 'addons_category_id');
                            $add_onns_id = array_column($value->addons, 'add_onns_id');
                        }
                        
                        if (!empty($details[0]['items'][0]['addons_category_list']) && is_array($details[0]['items'][0]['addons_category_list'])) {
                            foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
                                if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
                                    unset($details[0]['items'][0]['addons_category_list'][$key]);
                                }
                                else
                                {
                                    if (!empty($cat_value['addons_list']) && is_array($cat_value['addons_list'])) {
                                        foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                            if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
                                                unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // getting subtotal
                    if ($details[0]['items'][0]['is_customize'] == 1) 
                    {   $subtotal = 0;
                        $offer_price = str_replace(",", "", $details[0]['items'][0]['offer_price']);
                        if (!empty($details[0]['items'][0]['addons_category_list']) && is_array($details[0]['items'][0]['addons_category_list'])) {
                            foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
                                if (!empty($cat_value['addons_list'])) {
                                    foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                       $subtotal = $subtotal + $add_value['add_ons_price'];
                                    }
                                }
                            }
                            if($details[0]['items'][0]['offer_price']>0)
                            {
                                $subtotal = ($details[0]['items'][0]['offer_price'])? $subtotal + $offer_price : $subtotal;
                            }
                            else
                            {
                                $subtotal = ($details[0]['items'][0]['price'])? $subtotal + $details[0]['items'][0]['price'] : $subtotal;
                            }
                        } else {
                            if($details[0]['items'][0]['offer_price']>0)
                            {
                                $subtotal = ($details[0]['items'][0]['offer_price'])? $subtotal + $offer_price : $subtotal;
                            }
                            else
                            {
                                $subtotal = ($details[0]['items'][0]['price'])? $subtotal + $details[0]['items'][0]['price'] : $subtotal;
                            }
                        }
                    }
                    else
                    {   $subtotal = 0;
                        if ($details[0]['items'][0]['is_deal'] == 1) {
                            $price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
                        }
                        else
                        {
                            $price = ($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0;
                        }
                        $mprice = str_replace(",","",$price);
                        $subtotal = $subtotal + $mprice;
                    }
                    $cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
                    $cartItems[] = array(
                        'menu_id' => $details[0]['items'][0]['menu_id'],
                        'restaurant_id' => $cart_restaurant,
                        'name' => $details[0]['items'][0]['name'],
                        'quantity' => $value->quantity,
                        'is_customize' => $details[0]['items'][0]['is_customize'],
                        'is_veg' => $details[0]['items'][0]['is_veg'],
                        'is_deal' => $details[0]['items'][0]['is_deal'],
                        'price' => $details[0]['items'][0]['price'],
                        'offer_price' => $details[0]['items'][0]['offer_price'],
                        'subtotal' => $subtotal,
                        'totalPrice' => ($subtotal * $value->quantity),
                        'cartTotalPrice' => $cartTotalPrice,
                        'addons_category_list' => @$details[0]['items'][0]['addons_category_list'],
                    );
                }
            }
        }
        $cart_details = array(
            'cart_items' => $cartItems,
            'cart_total_price' => $cartTotalPrice,
        );
        return $cart_details;
    }
    
    //get country
    public function getSelectedPhoneCode(){
        $this->db->where('OptionSlug','phone_code');
        return $this->db->get('system_option')->first_row();
    }

    // get system options
    public function getSystemOptions(){
        return $this->db->get('system_option')->result_array();
    }
    //restaurant open/close function
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
            return 'Open'; 
        } else { 
            return 'Closed'; 
        }
    }
    public function getRange() {
        $arr = array('minimum_range','maximum_range','maximum_range_pickup');
        $this->db->where_in('OptionSlug',$arr);
        $this->db->order_by("OptionSlug = 'minimum_range'",'DESC');
        $this->db->order_by("OptionSlug = 'maximum_range'",'DESC');
        $this->db->order_by("OptionSlug = 'maximum_range_pickup'",'DESC');
        return $this->db->get('system_option')->result();
    } 
    //active/deactive multiple entries
    public function activeDeactiveMulti($content_id,$flag,$tblname,$is_masterdata=0){
        if($flag == 'active'){
            $Data = array('status' => 1);
        } elseif($flag == 'deactive'){
            $Data = array('status' => 0);
        }
        if($is_masterdata==1)
        {
            $this->db->where_in('is_masterdata','0');    
        }
        $this->db->where_in('content_id',$content_id);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
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
    //Code for encrypt/decrypt :: Start
    public function decrypt_data($encrypted_data)
    {
        $key = hex2bin("5126b6af4f15d73a20c60676b0f226b2");
        $iv =  hex2bin("a8966e4702bb84f4ef37640cd4b46aa2");

        $decrypted_data = openssl_decrypt($encrypted_data, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
        return json_decode(trim($decrypted_data));
    }
    public function encrypt_data($decrypted_data)
    {
        $arr_to_str = json_encode($decrypted_data);
        $encoded_str = base64_encode($arr_to_str);
        return $encoded_str;
    }
    //Code for encrypt/decrypt :: End    
    
    public function getUserAddress($user_entity_id)
    {
        $this->db->where('user_entity_id',$user_entity_id);
        $this->db->where('is_main',1);
        $default_add = $this->db->get('user_address')->first_row();
        if(empty($default_add)){
            $this->db->where('user_entity_id',$user_entity_id);
            $default_add = $this->db->get('user_address')->first_row();
        }
        return $default_add;
    }
    public function conectooSmsApi($phone,$sms){
        $conectoo_sms_api_url = conectoo_sms_api_url;
        $conectoo_api_key = conectoo_api_key;
        $conectoo_sms_sender = conectoo_sms_sender;

        //get System Option Data
        /*$this->db->select('OptionValue');
        $phone_code = $this->db->get_where('system_option',array('OptionSlug'=>'phone_code'))->first_row();
        $phone_code = $phone_code->OptionValue;
        $phone = $phone_code.$phone;*/
        
        $headers = array (
            'Accept: application/json',
            'Accept-Language: ro',
            'Authorization: Bearer '.$conectoo_api_key,
        );
        $fields = array(
            'phone'=>$phone,
            'sms'=>$sms,
            'sender'=>$conectoo_sms_sender,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $conectoo_sms_api_url);   
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);
        return $result;
    }
    //twilio SMS code :: Start
    public function sendSmsApi($phone,$sms)
    {
        if(stristr($phone, '+') == true) {            
        } else{
            $phone = '+'.$phone;
        }
        try{
            $sid = TWILIO_SID;
            $mes_sid = TWILIO_MSID;
            $token = TWILIO_AUTH_TOKEN;
            $twilio_phn_no = TWILIO_PHN_NO;
            $twilio = new Client($sid, $token);
            $message = $twilio->messages
                              ->create($phone, // to
                               [
                                   "body" => $sms,
                                   "messagingServiceSid"=> $mes_sid,
                               ]
                    );      
        }catch(Exception $e){
            //echo $e->getCode() . ' : ' . $e->getMessage()."<br>"; exit;
        }
    }
    //twilio SMS code :: End
    public function generateOTP($user_id=0) {
        if(IS_SMS_API_INTEGRATED){
            $otp = mt_rand(100000, 999999);
        } else {
            $otp = 123456;
        }
        if($user_id > 0) {
            $update_otp = array('user_otp'=>$otp);
            $this->updateData('users',$update_otp,'entity_id',$user_id);
        }
        return $otp;
    }
    // check if already exists - social media login
    public function checksocial($social_media_id)
    {        
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.earning_points,users.wallet,users.user_otp,users.image,users.notification,users.email');
        $this->db->where('social_media_id',$social_media_id);
        $this->db->where('user_type','User');
        return $this->db->get('users')->first_row();
    }
    public function deleteAccount($user_id) {
        $is_deleted = array('is_deleted'=>1, 'user_otp'=>NULL, 'referral_code'=>NULL, 'status'=>2);
        $this->db->where('entity_id',$user_id);
        $this->db->update('users',$is_deleted);
        return $this->db->affected_rows();
    }
    function escapeString($val)
    {
        $db = get_instance()->db->conn_id;
        $val = mysqli_real_escape_string($db, $val);
        return $val;
    }

    //cancel/reject reason list
    public function list_cancel_reject_reasons($reason_type, $language_slug,$user_type = ''){
        $this->db->select('reason');
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        $this->db->where('reason_type',$reason_type);
        if($user_type!='')
        {
            $this->db->where('user_type',$user_type);
        }        
        return $this->db->get('cancel_reject_reasons')->result();
    }

    //get active country detail 
    public function list_country_codes($flagDefaultFirst = false){
        $this->db->select('name,nicename,phonecode,iso');
        $this->db->where('status',1);
        if($flagDefaultFirst){
            $this->db->order_by('set_default','DESC');
        }
        return $this->db->get('country')->result();
    }

    //get Particular language record
    public function get_languages($current_lang){
        $result = $this->db->select('*')->get_where('languages',array('language_slug'=>$current_lang))->first_row();
        return $result;
    }

    //get active languages detail 
    public function list_available_languages(){
        $this->db->where('active',1);
        return $this->db->get('languages')->result();
    }

    //get cms detail 
    public function getCMSRecord($tblname,$cms_slug,$language_slug){
        $this->db->select('content_id,entity_id,name,CMSSlug,description,cms_icon,image');
        if($cms_slug){
            $this->db->where('CMSSlug',$cms_slug);
            $this->db->where('CMSSlug !=','login-with-fb');
            $this->db->where('CMSSlug !=','cookie-policy');
        } else {
            $this->db->where('CMSSlug !=','login-with-fb');
            $this->db->where('CMSSlug !=','cookie-policy');
        }
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        $result = $this->db->get($tblname)->result();
        foreach ($result as $key => $value) {
            $value->cms_icon = ($value->cms_icon) ? image_url.$value->cms_icon:'';
            $value->image = ($value->image) ? image_url.$value->image:'';
        }
        return $result;
    }
    public function is_notification_sound_enable(){
        $this->db->select('notification_sound');
        $this->db->where('entity_id', $this->session->userdata('AdminUserID'));
        return $this->db->get('users')->first_row();
    }
    public function country_iso_for_dropdown(){
        $this->db->select('iso');
        $this->db->where('status',1);
        $country_list = $this->db->get('country')->result_array();
        $iso = array();
        foreach ($country_list as $key => $value) {
            array_push($iso, $value['iso']);
        }
        return $iso;
    }
    public function getIsobyPhnCode($phone_code){
        $this->db->select('iso');
        $this->db->where('status',1);
        $this->db->where('phonecode',$phone_code);
        $country_iso = $this->db->get('country')->first_row();
        return $country_iso->iso;
    }

    public function check_valid_payment_sorting($sorting, $payment_id){
        return $this->db->where('sorting', $sorting)->where('payment_id !=', $payment_id)->get('payment_method')->first_row();
    }

    public function get_payment_method_detail($slug){
        return $this->db->where('payment_gateway_slug', $slug)->where('status', 1)->get('payment_method')->first_row();
    }

    public function get_payment_methods(){
        $this->db->where('status',1);
        return $this->db->get('payment_method')->result();
    }
    public function getResNametoDisplay($restaurant_id = '',$content_id = '',$language_slug = '') {
        $this->db->select('name');
        if($restaurant_id) {
            $this->db->where('entity_id',$restaurant_id);
        }
        if($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $res_name = $this->db->get('restaurant')->first_row();
        return $res_name->name;
    }
    ############TIMEZONE CODE START#################
    //Code for set time in utc format :: Start
    public function setZonebaseDateTime($timevalue,$timezone_name='')
    {
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }       
        date_default_timezone_set($timezone_name);
        $dt = new DateTime($timevalue);
        $tz = new DateTimeZone(default_timezone); // or whatever zone you're after
        $dt->setTimezone($tz);
        date_default_timezone_set(default_timezone);
        return $dt->format('Y-m-d H:i:s');;
    }
    public function setZonebaseTime($timevalue,$timezone_name='')
    {
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }       
        date_default_timezone_set($timezone_name);

        if(strpos($timevalue, ':') != true){
            $timevalue = $timevalue.":00";
        }//End
        $timevalue = str_replace("::", ":", $timevalue);        
        $dt = new DateTime($timevalue);
        $tz = new DateTimeZone(default_timezone); // or whatever zone you're after
        $dt->setTimezone($tz);
        date_default_timezone_set(default_timezone);
        return $dt->format('G:i');;
    }
    public function setZonebaseTimeforEdit($timevalue,$timezone_name='')
    {
        date_default_timezone_set(default_timezone);   
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }       
        $datetime = new DateTime(date('G:i',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('G:i');
    }
    //Code for set time in utc format :: End

    public function setZonebaseTimeforAPI($timevalue,$timezone_name='')
    {
        date_default_timezone_set(default_timezone);   
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }        
        $datetime = new DateTime(date('Y-m-d g:i A',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('Y-m-d g:i A');
    }

    //Code for time display base on time zone :: Start
    public function getZonebaseTime($timevalue,$timezone_name='')
    {
        date_default_timezone_set(default_timezone);   
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }
        $datetime = new DateTime(date('g:i A',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('g:i A');
    }
    //Code for time display base on time zone :: Start
    public function getZonebaseDate($timevalue,$timezone_name='')
    {       
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }     
            $timezone_name = $_SESSION['timezone_name'];
        } 
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }       
        $datetime = new DateTime(date('Y-m-d g:i A',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('Y-m-d g:i A');
    }
    public function getZonebaseDateSEC($timevalue,$timezone_name='')
    {
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }     
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }
        
        date_default_timezone_set($timezone_name);
        $datetime = new DateTime(date('Y-m-d g:i A',strtotime($timevalue)));        
        $Newtimezone = new DateTimeZone(default_timezone);        
        $datetime->setTimezone($Newtimezone);  
        date_default_timezone_set(default_timezone);      
        return $datetime->format('Y-m-d g:i A');
    }
    public function getZonebaseDateLJM($timevalue,$timezone_name='')
    {       
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }     
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }        
        $datetime = new DateTime(date('l j M',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('l j M');
    }
    public function getZonebaseCurrentTime($timevalue,$timezone_name='')
    {       
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        } 
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }

        $datetime = new DateTime(date('Y-m-d H:i:s',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('Y-m-d H:i:s');
    }

    //Function check the open close time
    public function Checkopenclose($start_time,$end_time,$timezone_name = '',$slottime = NULL)
    {
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }       
        date_default_timezone_set($timezone_name);

        if($slottime){
            $current_time = date('h:i a', strtotime($slottime));
        } else {
            $current_time = date('h:i a');
        }
        $start_time = date('h:i a', strtotime($start_time));
        $end_time = date('h:i a', strtotime($end_time));

        $date1 = DateTime::createFromFormat('H:i a', $current_time)->getTimestamp(); 
        $date2 = DateTime::createFromFormat('H:i a', $start_time)->getTimestamp();; 
        $date3 = DateTime::createFromFormat('H:i a', $end_time)->getTimestamp(); 
        if ($date3 <= $date2) { 
            $date3 += 24 * 3600; 
            if ($date1 < $date2) { 
                $date1 += 24 *3600; 
            } 
        } 
        date_default_timezone_set(default_timezone);

        if ($date1 > $date2 && $date1 < $date3) { 
            return 'Open'; 
        } else { 
            return 'Closed'; 
        }
    }
    //Code for time display base on time zone :: End
    ############TIMEZONE CODE END#################
    //check restaurant : closed/offline/deactive
    public function checkResForCart($restaurant_id,$scheduled_order_datetime = ''){
        $this->db->select('restaurant.name,restaurant.restaurant_slug,restaurant.timings,restaurant.enable_hours,restaurant.status,restaurant.allow_scheduled_delivery');
        $this->db->where('entity_id', $restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        if($scheduled_order_datetime) {
            //get time interval from system options
            $this->db->select('OptionValue');
            $this->db->where('OptionSlug','time_interval_for_scheduling');
            $time_interval_for_scheduling = $this->db->get('system_option')->first_row();
            $time_interval_for_scheduling = (int)$time_interval_for_scheduling->OptionValue;
            $half_interval = ceil($time_interval_for_scheduling / 2);
        }
        if(!empty($result)) {
            $timing = $result->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                if($scheduled_order_datetime) {
                    $day = date('l',strtotime($scheduled_order_datetime));
                    $date_check = date('Y-m-d',strtotime($scheduled_order_datetime));
                } else {
                    $day = date("l");
                    $date_check = date('Y-m-d');
                }
                foreach($timing as $keys=>$values) {
                    if($keys == strtolower($day))
                    {
                        $close = 'Closed';
                        if($result->enable_hours=='1')
                        {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->getZonebaseTime($values['open']):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->getZonebaseTime($values['close']):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'Closed';
                            if (!empty($values['open']) && !empty($values['close']))
                            {
                                if($scheduled_order_datetime) {
                                    $slot_open_time = date('H:i', strtotime($scheduled_order_datetime));
                                    $slottime = date_create($slot_open_time);
                                    date_add($slottime,date_interval_create_from_date_string($half_interval." minutes"));
                                    $slottime = date_format($slottime,"H:i");
                                    $close = $this->Checkopenclose($this->getZonebaseTime($values['open']),$this->getZonebaseTime($values['close']),'',$slottime);
                                } else {
                                    $close = $this->Checkopenclose($this->getZonebaseTime($values['open']),$this->getZonebaseTime($values['close']));
                                }
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
            $result->timings = $newTimingArr[strtolower($day)];
            $isallowed_scheduled_order = 'yes';
            if(!empty($newTimingArr) && $scheduled_order_datetime != '') {
                if($date_check == date('Y-m-d') && $newTimingArr[strtolower($day)]['closing'] == 'Closed' && $newTimingArr[strtolower($day)]['off'] != 'close') {
                    $scheduled_time = ($scheduled_order_datetime) ? date('H:i', strtotime($scheduled_order_datetime)) : date('H:i');
                    $date1 = strtotime(date('H:i', strtotime($this->getZonebaseTime($scheduled_time))));
                    $date2 = strtotime(date('H:i', strtotime($newTimingArr[strtolower($day)]['open'])));
                    $date3 = strtotime(date('H:i', strtotime($newTimingArr[strtolower($day)]['close'])));

                    if($date2 > $date1 || $date3 > $date1){
                        $isallowed_scheduled_order = 'yes';
                    } else {
                        $isallowed_scheduled_order = 'no';
                    }
                }else if($result->timings['off'] == 'close'){
                    $isallowed_scheduled_order = 'no';
                } else {
                    $isallowed_scheduled_order = 'yes';
                }
                if($result->enable_hours == 0 || $result->allow_scheduled_delivery == 0){
                    $isallowed_scheduled_order = 'no';
                }
            }
            $result->isallowed_scheduled_order = $isallowed_scheduled_order;
            return $result;
        }
    }
    public function getDefaultIso(){
        $this->db->select('iso');
        $this->db->where('status',1);
        $this->db->where('set_default',1);
        $country_iso = $this->db->get('country')->first_row();
        return $country_iso->iso;
    }
    #################CODE FOR IMAGE COMPRESS START############
    function compressImage($source, $destination)
    {
        $quality = 60;
        if(IMAGE_QUALITY)
        {
            $quality = IMAGE_QUALITY;
        }
        // Get image info 
        $imgInfo = getimagesize($source); 
        $mime = $imgInfo['mime'];
        $width = $imgInfo[0];
        $height = $imgInfo[1];        

        //Create a new image from file 
        $imagetype= 'jpeg';
        switch($mime)
        { 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($source);            
                $imagetype= 'jpeg';                
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($source); 
                $imagetype= 'png';
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($source); 
                $imagetype= 'gif';
                break; 
            default: 
                $image = imagecreatefromjpeg($source); 
        } 

        //Save image 
        if($imagetype=='png')
        {
            //Create main image file 
            $removeColour = imagecolorallocate($image, 0, 0, 0);
            imagecolortransparent($image, $removeColour);
            imagesavealpha($image, true);
            $transColor = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transColor);
            $a=imagepng($image, $destination,9);            
        }
        else
        {
            $a = imagejpeg($image, $destination, $quality); 
        }        
        // Return compressed image 
        return $destination; 
    } 
    #################CODE FOR IMAGE COMPRESS END############

    function get_category_wise_faqs_list($language_slug){
        $this->db->select('entity_id,name,sequence,content_id,language_slug,status');
        $this->db->where('status',1);
        $this->db->where('language_slug',$language_slug);
        $query = $this->db->get('faq_category');
        foreach ($query->result() as $category) {
            $faqs = $this->get_faq_by_category($category->entity_id);
            if($faqs){
                $return[$category->name] = $category;
                $return[$category->name]->faqs = $faqs;
            }
        }
        return $return;
    }

    public function get_faq_by_category($category_id) {
        $this->db->select('entity_id,faq_category_id,question,answer,content_id,language_slug,status');
        $this->db->where('status',1);
        $this->db->where('faq_category_id', $category_id);
        $query = $this->db->get('faqs');
        return $query->result();
    }

    public function getLanguageFileMobileApp(){
        $this->db->select('OptionValue');
        $this->db->where('OptionSlug','language_file_mobile_app');
        $result = $this->db->get('system_option')->first_row();
        return (!empty($result->OptionValue)) ? base_url().$result->OptionValue : '';
    }

    public function getCategoryDiscount($restaurant_content_id = NULL, $user_timezone = NULL){
        $this->db->select('coupon.entity_id,name');
        $this->db->where(array('status'=>1,'coupon_type'=>'discount_on_categories'));
        $end_dateval = date('Y-m-d H:i:s');
        //$end_dateval = $this->getZonebaseCurrentTime($end_dateval,$user_timezone);
        $this->db->where('start_date <=',$end_dateval);
        $this->db->where('end_date >=',$end_dateval);
        if(!empty($restaurant_content_id)){
            $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
            $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        }
        $result = $this->db->get('coupon')->result_array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $this->db->select('coupon_id,category_content_id,discount_type,discount_value,minimum_amount');
                $this->db->where('coupon_id',$value['entity_id']);
                $category_content_result = $this->db->get('coupon_category_map')->result_array();
                $category_content_id = (!empty($category_content_result)) ? array_column($category_content_result, 'category_content_id') : array();
                $category_discount_type = (!empty($category_content_result)) ? array_column($category_content_result, 'discount_type') : array();
                $category_discount_value = (!empty($category_content_result)) ? array_column($category_content_result, 'discount_value') : array();
                $category_minimum_amount = (!empty($category_content_result)) ? array_column($category_content_result, 'minimum_amount') : array();
                $result[$key]['categoryDetail'] = $category_content_id;
                $type_value_min_amount = array_map(
                    function($type, $val, $amt) { 
                        return array('discount_type'=>$type, 'discount_value'=>$val, 'minimum_amount'=>$amt); 
                    }, $category_discount_type, $category_discount_value,$category_minimum_amount
                );
                $result[$key]['combined'] = array_combine($category_content_id, $type_value_min_amount);
            }
        }
        return $result;
    }
    //Event Booking Reminder Changes
    public function EventBookingReminderNoti()
    {
        $currentDateTime = date("Y-m-d H:i:s");  
        $this->db->select('event.entity_id,event.user_id,event.booking_date,event.event_status,event.no_of_people,res.name as rname,res.content_id as rcontent_id,res.entity_id as rentity_id,radd.address,u.first_name as fname,u.last_name as lname,u.email as uemail,u.device_id as udevice_id,u.notification as unoti');
        $this->db->join('restaurant as res','event.restaurant_id = res.content_id'); 
        $this->db->join('restaurant_address as radd','res.entity_id = radd.resto_entity_id'); 
        $this->db->join('users as u','event.user_id = u.entity_id');
        $this->db->where('event.user_id',$this->session->userdata('UserID'));
        $this->db->where('event.booking_date >',$currentDateTime);
        $this->db->where('event.event_status !=', 'cancel');
        $this->db->where('res.status',1); 
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('event')->result_array();    
    }

    public function get_restaurant_order_mode($restaurant_id) {
        $this->db->select('order_mode');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        return ($result->order_mode) ? explode(",", strtolower($result->order_mode)) : '';
    }
    public function send_email_to_guest($username, $restaurant_name, $order_id, $order_total, $guest_email, $language_slug, $order_records, $menu_item, $track_order_link,$user_timezone='') {
        //create invoice
        $data['order_records'] = $order_records;
        $data['menu_item'] = $menu_item;
        $data['user_timezone'] = $user_timezone;

        $html = $this->load->view('backoffice/order_invoice',$data,true);       
        if (!@is_dir('uploads/invoice')) {
          @mkdir('./uploads/invoice', 0777, TRUE);
        } 
        $filepath = 'uploads/invoice/'.$order_id.'.pdf';
        $this->load->library('M_pdf'); 
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetHTMLHeader('');
        $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">'.$this->lang->line('signature').' ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #cccccc;"><img src="'.base_url().'assets/admin/img/logo.png" alt="" width="80" /></div></body></html>');
        $mpdf->AddPage('', // L - landscape, P - portrait 
            '', '', '', '',
            0, // margin_left
            0, // margin right
            10, // margin top
            23, // margin bottom
            0, // margin header
            0 //margin footer
        );
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        //$mpdf->SetAutoFont();
        $mpdf->WriteHTML($html);
        $mpdf->output($filepath,'F');

        //send invoice in email
        $email_template = $this->db->get_where('email_template',array('email_slug'=>'guest-order-confirmation','language_slug'=>$language_slug,'status'=>1))->first_row();
        $arrayData = array('FirstName'=>$username,'restaurant_name'=>$restaurant_name,'order_id'=>$order_id, 'order_total'=>$order_total,'track_order'=>$track_order_link);
        $EmailBody = generateEmailBody($email_template->message,$arrayData);
        //get System Option Data
        $this->db->select('OptionValue');
        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

        $this->db->select('OptionValue');
        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
      
        $this->load->library('email');  
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";      
        $this->email->initialize($config);  
        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
        $this->email->to($guest_email);
        $this->email->subject($email_template->subject);  
        $this->email->message($EmailBody); 
        $this->email->attach($filepath);
        $this->email->send();
    }
    public function send_sms_to_guest($guest_phncode, $guest_phn_no, $order_id, $restaurant_name, $track_order_link) {
        $to_mobileno = ($guest_phncode && $guest_phn_no) ? '+'.$guest_phncode.$guest_phn_no : (($guest_phn_no) ? '+1'.$guest_phn_no : '');
        //send SMS
        if($to_mobileno != '') {
            $sms_txt = $this->lang->line('thankyou_for_ordering')."\n".sprintf($this->lang->line('order_placed_successfully'),$restaurant_name,$order_id)."\n".$this->lang->line('clickto_track_order').': '.$track_order_link."\n \n".$this->lang->line('best_regards')."\n".$this->lang->line('team_sitename');
            $sms_data = $this->sendSmsApi($to_mobileno,$sms_txt);
        }
    }
    // getting time slots for table booking
    public function getTimeSlots($interval, $start_time, $end_time) {
        $returnTimeFormat = 'h:i A';
        $current   = time(); 
        $addTime   = strtotime('+'.$interval, $current); 
        $diff      = $addTime - $current;
        $startTime = strtotime($start_time); 
        $endTime   = strtotime($end_time);
        $times = array(); 
        while ($startTime <= $endTime) { 
            $times[] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        } 
        return $times;
    }
    //get user profile 
    public function getUserImage($entity_id){
        $this->db->select('image');
        return $this->db->get_where('users',array('entity_id'=>$entity_id))->first_row();
    }
    //Table Booking Reminder Changes
    public function TableBookingReminderNoti()
    {
        $currentDateTime = date("Y-m-d H:i");
        $currentDate = date("Y-m-d");     
        $this->db->select('table.booking_date,table.start_time,table.end_time,table.booking_status,table.no_of_people,res.name as rname,res.content_id as rcontent_id,res.entity_id as rentity_id,radd.address,u.first_name as fname,u.last_name as lname,u.email as uemail,u.device_id as udevice_id,u.notification as unoti');
        $this->db->join('restaurant as res','table.restaurant_content_id = res.content_id'); 
        $this->db->join('restaurant_address as radd','res.entity_id = radd.resto_entity_id'); 
        $this->db->join('users as u','table.user_id = u.entity_id');
        $this->db->where('table.user_id',$this->session->userdata('UserID'));
        $this->db->where('table.booking_date >=',$currentDate);
        $this->db->where('table.booking_status !=', 'cancelled');
        $this->db->where('res.status',1);  
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('table_booking as table')->result_array();
    }

    public function getRestaurantSlug($restaurant_id) {
        $this->db->select('restaurant_slug');
        $this->db->where('entity_id',$restaurant_id);
        $restaurant = $this->db->get('restaurant')->first_row();
        return $restaurant->restaurant_slug;
    }
    //find the off line restaurant
    public function getOfflineRestaurant()
    {
        $this->db->select('entity_id, content_id, enable_hours, offlinetime');
        $this->db->where('enable_hours',0);
        $this->db->where('offlinetime>',0);
        $restaurant = $this->db->get('restaurant')->result_array();
        return $restaurant;
    }
    public function getAllNeworders()
    {
        $this->db->select('o.entity_id as order_id, o.user_id, o.restaurant_id, o.order_status, o.created_date as order_date, res.phone_code, res.phone_number,res.name as res_name, u.first_name,u.last_name');
        $this->db->join('restaurant as res','o.restaurant_id = res.entity_id');
        $this->db->join('users as u','o.user_id = u.entity_id','left');
        $this->db->like('o.created_date',date('Y-m-d'));
        $this->db->where('o.order_status','placed');
        $this->db->where('o.status!=',1);
        $this->db->group_by('o.entity_id');
        $order = $this->db->get('order_master as o')->result_array();
        return $order;
    }
    public function dateFormat($date,$format='m-d-Y'){
        return date($format,strtotime($date));
    }
    public function timeFormat($time,$format='g:i A'){
        return date($format,strtotime($time));
    }
    public function datetimeFormat($datetime,$format='m-d-Y g:i A'){
        return date($format,strtotime($datetime));
    }
    public function notificationToAgent($order_id, $order_status){
        // adding notification for website
        $order_status_val = '';
        if ($order_status == "complete") {
            $this->deleteData('agent_order_notification','order_id',$order_id);
            $order_status_val = 'order_completed';
        }
        else if($order_status == 'admin_order_created'){
            $order_status_val = 'admin_order_created';
        }
        else if ($order_status == "onGoing") {
            $order_status_val = 'order_ongoing';
        }
        else if ($order_status == "delivered") {
            $order_status_val = 'order_delivered';
        }
        else if ($order_status == "cancel") {
            $order_status_val = 'order_canceled';
        }
        else if ($order_status == "ready") {
            $order_status_val = 'order_served';
        }
        else if ($order_status == "accepted") {
            $order_status_val = 'order_accepted';
        }
        else if ($order_status == "orderready") {
            $order_status_val = 'order_ready';
        }
        else if ($order_status == "rejected") {
            $order_status_val = 'order_rejected';
        }
        else if($order_status == "order_updated") {
            $order_status_val = 'order_updated';
        }
        if($order_status_val=='')
        {
            $order_status_val = $order_status;
        }
        if ($order_status_val != '') {
            $order_detail = $this->getSingleRow('order_master','entity_id',$order_id);
            $notification = array(
                'order_id' => $order_id,
                'agent_id' => $order_detail->agent_id,
                'notification_slug' => $order_status_val,
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->addData('agent_order_notification',$notification);
        }
    }
    public function getAgentNotification($user_id,$status=NULL)
    {
        $notifications = array();
        // orders
        $this->db->where('agent_id',$user_id);
        if ($status == 'unread') {
            $this->db->where('view_status',0);
        }
        $this->db->order_by('datetime','desc');
        $orders = $this->db->get('agent_order_notification')->result();
        if ( !empty($orders) ) {
            foreach ($orders as $key => $value)
            {
                $notification_slugval = $value->notification_slug;
                if($notification_slugval=='order_ongoing')
                { 
                    $this->db->select('order_delivery');
                    $this->db->where('entity_id',$value->order_id);
                    $orderarr = $this->db->get('order_master')->first_row();
                    if(strtolower($orderarr->order_delivery)=='pickup')
                    {
                        $order_notification_slug = 'order_is_readynoti';
                        $notification_slugval = $order_notification_slug;
                    }
                }
                $notifications[] = array(
                    'notification_type' => 'order',
                    'notification_type_id' => $value->user_notification_id,
                    'entity_id' => $value->order_id,
                    'user_id' => $value->user_id,
                    'notification_slug' => $notification_slugval,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        // sort array in descending order
        usort($notifications, function ($a, $b) {
            $dateA = date('Y-m-d H:i:s',strtotime($a['datetime']));
            $dateB = date('Y-m-d H:i:s',strtotime($b['datetime']));
            // descending ordering, use `<=` for ascending
            return $dateA <= $dateB;
        });
        return $notifications;
    }
    //get table booking notification count 
    public function getTableBookigNotificationCount(){ 
        $this->db->select('tablebooking_count');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('table_booking_notification')->first_row();
    }
    //check item is in stock or not
    public function checkMenuInstock($menu_ids){
        if(!empty($menu_ids)){
            $out_of_stock_arr = array();
            $count = count($menu_ids);
            foreach ($menu_ids as $key => $value) {
                $this->db->select('entity_id as menu_id, stock, name');
                $this->db->where('entity_id',$value);
                $result = $this->db->get('restaurant_menu_item')->first_row();
                if($result->stock=='0'){
                    array_push($out_of_stock_arr, $result);
                }
            }
            if(!empty($out_of_stock_arr) && count($out_of_stock_arr) > 0){
                return $out_of_stock_arr;
            } else {
                return 'in_stock';
            }
        } else {
            return 'in_stock';
        }
    }
    public function sendVerifyOtpEmail($user_first_name, $user_email, $user_otp, $language_slug, $is_forgot_pwd = '0') {
        //in email
        if($is_forgot_pwd == '1') {
            $email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password-otp','language_slug'=>$language_slug))->first_row();
        } else {
            $email_template = $this->db->get_where('email_template',array('email_slug'=>'verify-account','language_slug'=>$language_slug))->first_row();
        }
        $arrayData = array('FirstName'=>$user_first_name,'your_otp'=>$user_otp);
        $EmailBody = generateEmailBody($email_template->message,$arrayData);

        //get System Option Data
        $this->db->select('OptionValue');
        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

        $this->db->select('OptionValue');
        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();

        $this->load->library('email');
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $this->email->initialize($config);
        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
        $this->email->to($user_email);
        $this->email->subject($email_template->subject);
        $this->email->message($EmailBody);
        $this->email->send();
    }
    public function getResAboutUs($user_id){
        $result = '';
        $this->db->where('entity_id',$user_id);
        $this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin' OR user_type='MasterAdmin')");
        $return = $this->db->get('users')->first_row();
        if(!empty($return)){
            $this->db->select('entity_id as res_id, about_restaurant');
            $this->db->where('restaurant_owner_id',$user_id);
            $this->db->where('about_restaurant !=','');
            $this->db->where('about_restaurant !=',NULL);
            $result = $this->db->get('restaurant')->first_row();
            if(empty($result)){
                $this->db->select('entity_id as res_id, about_restaurant');
                $this->db->where('branch_admin_id',$user_id);
                $this->db->where('about_restaurant !=','');
                $this->db->where('about_restaurant !=',NULL);
                $result = $this->db->get('restaurant')->first_row();
            }
        }
        return $result;
    }
    //stripe save card changes :: start
    public function add_new_customer_in_stripe($first_name,$last_name,$phone_code,$phone_number,$email)
    {
        // Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_info = $this->get_payment_method_detail('stripe');
        if($stripe_info && !empty($stripe_info)){
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);    
        }
        else
        {
            return false;
        }

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
    public function notiToUser($order_id, $restaurant_id, $order_status, $order_mode, $thirdparty_delivery_entityid=''){
        // adding notification for website
        $order_status_val = '';
        if ($order_status == "complete") {
            $this->deleteData('user_order_notification','order_id',$order_id);
            $order_status_val = 'order_completed';
        }else if($order_status == 'admin_order_created'){
            $order_status_val = 'admin_order_created';
        }
        else if ($order_status == "onGoing") {
            $order_status_val = 'order_ongoing';
        }
        else if ($order_status == "delivered") {
            $order_status_val = 'order_delivered';
        }
        else if ($order_status == "cancel") {
            $order_status_val = 'order_canceled';
        }
        else if ($order_status == "ready") {
            $order_status_val = 'order_served';
        }
        else if ($order_status == "accepted") {
            $order_status_val = 'order_accepted';
        }
        else if ($order_status == "orderready") {
            $order_status_val = 'order_ready';
        }

        if ($order_status_val != '') {
            $order_detail = $this->getSingleRow('order_master','entity_id',$order_id);
            if($order_detail->user_id && $order_detail->user_id > 0) {
                $notification = array(
                    'order_id' => $order_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => $order_status_val,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->addData('user_order_notification',$notification);
            }
        }
        //get langauge
        $device = $this->getDevice($order_detail->user_id);
        if($device->notification == 1){
            $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
            $this->lang->load('messages_lang', $languages->language_directory);
            $message = sprintf($this->lang->line($order_status_val),$order_id);
            /*if order status is cancled then append reason*/
            if($order_status_val == 'order_canceled'){
                $message = sprintf($this->lang->line('order_canceled'),$order_id).'-'.$order_detail->cancel_reason;
            }
            if($order_status_val == 'order_ongoing')
            {
                if($order_mode =='DineIn')
                {
                   $message = $this->lang->line('food_is_ready_notification');
                }
                else if($order_mode =='PickUp')
                {
                    $message = sprintf($this->lang->line('order_ready_notification'),$order_id);
                }
                else if($order_mode =='Delivery')
                {
                    $message = sprintf($this->lang->line('on_going_notification'),$order_id);
                }
            }
            else if($this->input->post('order_status') == "ready" || $this->input->post('order_status') == "orderready")
            {
                if($order_mode =='DineIn')
                { 
                   $message = $this->lang->line('order_served');
                }
                else if($order_mode =='PickUp')
                {
                    $message = sprintf($this->lang->line('order_ready_notification'),$order_id);
                }
            }
            if($order_status == 'admin_order_created'){
                $message = $this->lang->line('admin_order_created').' '.$this->lang->line('admin_order_created_1').$order_id;
            }
            $device_id = $device->device_id;
            $this->sendFCMRegistration($device_id,$message,$order_status,$restaurant_id,FCM_KEY,$order_mode,$order_detail->paid_status,$order_id,'','',$thirdparty_delivery_entityid);
        }
    }
    public function getDevice($user_id){
        $this->db->select('users.entity_id,users.device_id,users.language_slug,users.notification');
        $this->db->where('users.entity_id',$user_id);
        $this->db->where('status',1);
        return $this->db->get('users')->first_row(); 
    }
    public function sendFCMRegistration($registrationIds,$message,$order_status,$restaurant_id,$key=FCM_KEY,$order_typeval='',$paid_status='',$order_id='',$driver_id='',$wallet_amount='',$thirdparty_delivery_entityid='') {
        if($registrationIds){        
            #prep the bundle
            $fields = array();            
            $fields['to'] = $registrationIds; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['notification']['title'] = $this->lang->line('customer_app_name');
            if ($order_status == "delivered" || $order_status == "complete") {
                $fields['data'] = array ('screenType'=>'delivery','restaurant_id'=>$restaurant_id,'order_id'=>$order_id,'driver_id'=>$driver_id,'wallet_amount'=>$wallet_amount);
            }
            else
            {
                if($order_typeval =='DineIn' && $paid_status == 'unpaid')
                {
                    $fields['data'] = array ('screenType'=>'dinein','wallet_amount'=>$wallet_amount);
                }
                else
                {
                    $fields['data'] = array ('screenType'=>'order','wallet_amount'=>$wallet_amount);
                }                
            }            
            $headers = array (
                'Authorization: key=' . $key,
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
            if($thirdparty_delivery_entityid) {
                $noti_result = json_decode($result,true);
                $this->updateData('doordash_relay_details',array('callbacknoti_resp'=>serialize($noti_result)),'entity_id',$thirdparty_delivery_entityid);
            }
            curl_close($ch);            
        } 
    }
    public function generalCurlCall($request_type='POST', $request_url, $headers, $post_fields='')
    {
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $request_url);
        if($post_fields && $request_type=='POST'){
            curl_setopt($curl,CURLOPT_POST, true );
        }
        if($request_type=='PUT'){
            curl_setopt($curl,CURLOPT_PUT, true);
        }
        if($request_type=='PATCH'){
            //curl_setopt($curl,CURLOPT_PATCH, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers );
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false );
        if($post_fields && ($request_type=='POST' || $request_type=='PUT' || $request_type=='PATCH')) {
            curl_setopt($curl,CURLOPT_POSTFIELDS, $post_fields);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    //relay integration :: start
    public function checkDeliveryAvailableInRelay($order_id, $is_scheduled = '')
    {
        $resp_data = array('is_available' => 'no');
        if($order_id) {
            $order_dtl = $this->getDoorDash_OrderDetails($order_id);
            //dropoff details :: start
                $user_detail = json_encode(unserialize($order_dtl->user_detail));
                $user_detail = json_decode($user_detail,true);
                //coordinates
                $dropoff_latitude = ($user_detail['latitude'])?$user_detail['latitude']:(($order_dtl->user_latitude)?$order_dtl->user_latitude:'');
                $dropoff_longitude = ($user_detail['longitude'])?$user_detail['longitude']:(($order_dtl->user_longitude)?$order_dtl->user_longitude:'');
                $coordinates_arr = array();
                if($dropoff_latitude != '' && $dropoff_longitude != ''){
                    $coordinates_arr = array('latitude' => $dropoff_latitude, 'longitude' => $dropoff_longitude);
                }

                //consumer address
                $dropoff_full_address = $user_detail['address']?$user_detail['address']:(($order_dtl->user_address)?$order_dtl->user_address:'');
                $dropoff_street = '';
                $dropoff_apartment = ($user_detail['landmark'])?$user_detail['landmark']:(($order_dtl->user_landmark)?$order_dtl->user_landmark:'');
                $dropoff_city = ($user_detail['city'])?$user_detail['city']:(($order_dtl->user_city)?$order_dtl->user_city:'');
                $dropoff_state = ($user_detail['state'])?$user_detail['state']:(($order_dtl->user_state)?$order_dtl->user_state:'');
                $dropoff_zip_code = ($user_detail['zipcode'])?$user_detail['zipcode']:(($order_dtl->user_zipcode)?$order_dtl->user_zipcode:'');

                $user_address_arr = array();
                if($dropoff_city == '' || $dropoff_state == '' || $dropoff_street == '' || $dropoff_zip_code == ''){
                    $user_address_arr = $this->getAddressWithDetails($dropoff_latitude,$dropoff_longitude);

                    if($dropoff_full_address == ''){
                        $dropoff_full_address = $user_address_arr['address'];
                    }
                    if($dropoff_street == ''){
                        $dropoff_street = $user_address_arr['street'];
                    }
                    if($dropoff_city == ''){
                        $dropoff_city = $user_address_arr['city'];
                    }
                    if($dropoff_state == ''){
                        $dropoff_state = $user_address_arr['state'];
                    }
                    if($dropoff_zip_code == ''){
                        $dropoff_zip_code = $user_address_arr['zipcode'];
                    }
                }

                $dropoff_address = array();
                if($dropoff_street != ''){
                    $dropoff_address['address1'] = $dropoff_street;
                }
                if($dropoff_apartment != ''){
                    $dropoff_address['apartment'] = $dropoff_apartment;
                }
                if($dropoff_city != ''){
                    $dropoff_address['city'] = $dropoff_city;
                }
                if($dropoff_state != ''){
                    $dropoff_address['state'] = $dropoff_state;
                }
                if($dropoff_zip_code != ''){
                    $dropoff_address['zip'] = $dropoff_zip_code;
                }
                if(!empty($coordinates_arr)) {
                    $dropoff_address['coordinates'] = $coordinates_arr;
                }
            //dropoff details :: end
            //can-deliver api curl call :: start
                //can-deliver post fields
                $relay_candeliver_arr = array();
                $relay_candeliver_arr['producerLocationKey'] = producer_location_key;
                if(!empty($coordinates_arr)) {
                    $relay_candeliver_arr['coordinates'] = $coordinates_arr;
                } else if(!empty($dropoff_address)){
                    $relay_candeliver_arr['address'] = $dropoff_address;
                }
                $candeliver_headers = array (
                    'accept: application/json',
                    'x-relay-auth: '.relay_api_key_auth,
                    'Content-type: application/json'
                );

                $candeliver_api_url = (relay_sandbox)?relay_sandboxapi_url.'can-deliver':relay_api_url.'can-deliver';
                $candeliver_result = $this->generalCurlCall('POST',$candeliver_api_url, $candeliver_headers, json_encode($relay_candeliver_arr));
            //can-deliver api curl call :: end
            //can-deliver api response operations :: start
                $relay_candeliver_result = json_decode($candeliver_result,true);
                //save candeliver details in DB :: start
                    $candeliver_data = array(
                        'order_id' => $order_id,
                        'delivery_method' => 'relay',
                        'api_slug' => 'candeliver',
                        'api_request' => serialize($relay_candeliver_arr),
                        'api_response' => serialize($relay_candeliver_result),
                        'created_date' => date('Y-m-d H:i:s'),
                    );
                    $this->addData('doordash_relay_details',$candeliver_data);
                //save candeliver details in DB :: end
                if($relay_candeliver_result['canDeliver']){
                    //customer details :: start
                        $user_first_name = ($user_detail['first_name'])?$user_detail['first_name']:$order_dtl->first_name;
                        $user_last_name = ($user_detail['last_name'])?$user_detail['last_name']:$order_dtl->last_name;
                        $dropoff_phone_number = ($order_dtl->user_mobile_number)?'+'.$order_dtl->user_mobile_number:(($order_dtl->phone_code)?'+'.$order_dtl->phone_code.$order_dtl->mobile_number:$order_dtl->mobile_number);
                        $customer_details_arr = array(
                            'name' => ($user_last_name) ? $user_first_name.' '.$user_last_name : $user_first_name,
                            'phone' => $dropoff_phone_number,
                            'location' => $dropoff_address,
                        );
                        $delivery_instructions = ($order_dtl->delivery_instructions)?$order_dtl->delivery_instructions:NULL;
                    //customer details :: end
                    //item details :: start
                        $item_detail = json_encode(unserialize($order_dtl->item_detail));
                        $item_detail = json_decode($item_detail,true);
                        $total_items = count($item_detail); //total item count
                        $item_names_arr = array();

                        foreach ($item_detail as $itemkey => $itemvalue) {
                            $item_names_arr[$itemkey]['name'] = $itemvalue['item_name'];
                            $item_names_arr[$itemkey]['quantity'] = $itemvalue['qty_no'];
                            $item_names_arr[$itemkey]['price'] = $itemvalue['itemTotal'];
                        }
                    //item details :: end
                    //order total, driver tip, external delivery id, currency, tax, delivery fee :: start
                        $order_subtotal = (float)$order_dtl->order_value;
                        $order_total = $order_dtl->order_total;
                        $driver_tip = ($order_dtl->driver_tip)?(float)$order_dtl->driver_tip:0;
                        //$external_delivery_id = $order_id;
                        $external_delivery_id = time().$order_id;

                        $default_currency = get_default_system_currency();
                        if(!empty($default_currency)){
                            $currency_details = $default_currency;
                        }else{
                            $currency_details = $this->getRestaurantCurrencySymbol($order_dtl->restaurant_id);
                        }
                        $currency_code = $currency_details->currency_code;

                        //service tax :: start
                        $tax_amountdis = 0;
                        if($order_dtl->tax_type == 'Percentage'){
                            $tax_amountdis = ($order_subtotal * $order_dtl->tax_rate) / 100;
                        } else {
                            $tax_amountdis = $order_dtl->tax_rate; 
                        }
                        $tax_amountdis = number_format(round($tax_amountdis,2),2);
                        //service tax :: end
                        //service fee :: start
                        $service_feedis = 0;
                        if(!empty($order_dtl->service_fee) && !is_null($order_dtl->service_fee) && $order_dtl->service_fee > 0) {
                            if($order_dtl->service_fee_type == 'Percentage'){
                                $service_feedis = ($order_subtotal * $order_dtl->service_fee) / 100;
                            }else{
                                $service_feedis = $order_dtl->service_fee; 
                            }
                            $service_feedis = number_format(round($service_feedis,2),2);
                        }
                        //service fee :: end
                        //credit card fee :: start
                        $creditcard_feedis = 0;
                        if(!empty($order_dtl->creditcard_fee) && !is_null($order_dtl->creditcard_fee) && $order_dtl->creditcard_fee > 0) {
                            if($order_dtl->creditcard_fee_type == 'Percentage'){
                                $creditcard_feedis = ($order_subtotal * $order_dtl->creditcard_fee) / 100;
                            }else{
                                $creditcard_feedis = $order_dtl->creditcard_fee; 
                            }                        
                            $creditcard_feedis = number_format(round($creditcard_feedis,2),2);
                        }
                        //credit card fee :: end
                        $tax_total = $tax_amountdis + $service_feedis + $creditcard_feedis;
                        $tax_total = (float)$tax_total;

                        //discount calculation + delivery charge :: start
                        //wallet discount
                        $wallet_history = $this->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'debit' => 1, 'is_deleted'=>0));
                        $wallet_discount = ($wallet_history)?(float)$wallet_history->amount:0;
                        //coupon discount
                        if($order_dtl->coupon_name){
                            $cpn_discount = (float)$order_dtl->coupon_discount;
                        }else{
                            $cpn_discount = 0;
                        }
                        $total_discount = $wallet_discount + $cpn_discount;
                        //delivery charge
                        $order_delivery_charge = 0;
                        if((float)$order_dtl->delivery_charge > 0){
                            $order_delivery_charge = (float)$order_dtl->delivery_charge;
                        }
                        //discount calculation + delivery charge : end
                    //order total, driver tip, external delivery id, currency, tax, delivery fee :: end
            
                    //Code for add extra time with selected mode :: Start
                    $PREPARATION_MINUTES = (PREPARATION_MINUTES)?PREPARATION_MINUTES:0;
                    $orderschedule_mode = ($order_dtl->schedule_mode)?$order_dtl->schedule_mode:0;
                    $orderschedule_time = 0;
                    if($orderschedule_mode==0)
                    {
                        $time_slotvalarr = $this->getSystemOptoin('schedule_normal_end');
                        $orderschedule_time = $time_slotvalarr->OptionValue;
                    }
                    else if($orderschedule_mode==1)
                    {
                        $time_slotvalarr = $this->getSystemOptoin('schedule_busy_end');
                        $orderschedule_time = $time_slotvalarr->OptionValue;
                    }
                    else if($orderschedule_mode==2)
                    {
                        $time_slotvalarr = $this->getSystemOptoin('schedule_verybusy_end');
                        $orderschedule_time = $time_slotvalarr->OptionValue;
                    }
                    if($orderschedule_time > 0) {
                        $PREPARATION_MINUTES = $PREPARATION_MINUTES + $orderschedule_time;                
                    }
                    //Code for add extra time with selected mode :: End

                    //create order api curl call :: start
                        //create order post fields
                        $relay_createorder_arr['order'] = array();
                        $relay_createorder_arr['order']['externalId'] = $external_delivery_id;
                        $relay_createorder_arr['order']['producer']['producerLocationKey'] = producer_location_key;
                        $relay_createorder_arr['order']['consumer'] = $customer_details_arr;
                        $relay_createorder_arr['order']['price']['subTotal'] = $order_subtotal;
                        $relay_createorder_arr['order']['price']['tax'] = $tax_total;
                        $relay_createorder_arr['order']['price']['discount'] = $total_discount;
                        $relay_createorder_arr['order']['price']['deliveryFee'] = $order_delivery_charge;
                        if($driver_tip>0){
                            $relay_createorder_arr['order']['price']['tip'] = $driver_tip;
                        }
                        if($order_dtl->payment_option == 'cod'){
                            $relay_createorder_arr['order']['isCash'] = true;
                        }
                        if($is_scheduled == 1 && $order_dtl->scheduled_date && $order_dtl->slot_open_time) {
                            //Old code
                            /*$order_scheduled_datetime = date('Y-m-d H:i:s', strtotime("$order_dtl->scheduled_date $order_dtl->slot_open_time"));
                            $relay_createorder_arr['order']['time']['lateDelivery'] = $order_scheduled_datetime;
                            $relay_createorder_arr['order']['time']['isFutureOrder'] = true;*/
                            //New code
                            $order_scheduled_datetime = date('Y-m-d H:i:s', strtotime("$order_dtl->scheduled_date $order_dtl->slot_open_time"));
                            //$pickup_time = date_create($order_scheduled_datetime." + ".$PREPARATION_MINUTES." minutes");
                            $pickup_time = date_create($order_scheduled_datetime);
                            $pickup_time = date_format($pickup_time, "Y-m-d\TH:i:s\Z");
                            $relay_createorder_arr['order']['time']['lateDelivery'] = $pickup_time;
                            $relay_createorder_arr['order']['time']['isFutureOrder'] = true;
                        }//New code else part
                        else
                        {
                            $pickup_time = date_create("+ ".$PREPARATION_MINUTES." minutes");
                            $pickup_time = date_format($pickup_time, "Y-m-d\TH:i:s\Z");
                            $relay_createorder_arr['order']['time']['lateDelivery'] = $pickup_time;
                            $relay_createorder_arr['order']['time']['isFutureOrder'] = true;
                        }
                        if($delivery_instructions != '' && $delivery_instructions != NULL){
                            $relay_createorder_arr['order']['specialInstructions'] = $delivery_instructions;
                        }
                        $relay_createorder_arr['order']['items'] = $item_names_arr;
                        $relay_createorder_arr['order']['isReady'] = true;

                        $createorder_headers = array (
                            'accept: application/json',
                            'x-relay-auth: '.relay_api_key_auth,
                            'Content-type: application/json'
                        );

                        $createorder_api_url = (relay_sandbox)?relay_sandboxapi_url.'orders':relay_api_url.'orders';
                        $createorder_result = $this->generalCurlCall('POST',$createorder_api_url, $createorder_headers, json_encode($relay_createorder_arr));
                    //create order api curl call :: end
                    //create order api response operations :: start
                        $relay_createorder_result = json_decode($createorder_result,true);
                        //save create order details in DB :: start
                            $createorder_data = array(
                                'order_id' => $order_id,
                                'delivery_method' => 'relay',
                                'api_slug' => 'createorder',
                                'relay_order_key' => ($relay_createorder_result['order']['orderKey'])?$relay_createorder_result['order']['orderKey']:NULL,
                                'external_delivery_id' => $external_delivery_id,
                                'api_request' => serialize($relay_createorder_arr),
                                'api_response' => serialize($relay_createorder_result),
                                'created_date' => date('Y-m-d H:i:s'),
                            );
                            $this->addData('doordash_relay_details',$createorder_data);
                        //save create order details in DB :: end
                        if($relay_createorder_result['success'] && $relay_createorder_result['order']['orderKey'] && (!$relay_createorder_result['order']['cannotBeDelivered'] && !$relay_createorder_result['order']['isVoided'] && !$relay_createorder_result['order']['hasBeenRejectedByRelay'])) {
                            //order ready api curl call :: start
                                //order ready post fields
                                $relay_orderready_arr = array();
                                $relay_orderready_arr['orderKey'] = $relay_createorder_result['order']['orderKey'];
                                $relay_orderready_arr['isReady'] = true;
                                
                                $orderready_headers = array (
                                    'accept: application/json',
                                    'x-relay-auth: '.relay_api_key_auth,
                                    'Content-type: application/json'
                                );

                                $orderready_api_url = (relay_sandbox)?relay_sandboxapi_url.'orders/ready':relay_api_url.'orders/ready';
                                $orderready_result = $this->generalCurlCall('POST',$orderready_api_url, $orderready_headers, json_encode($relay_orderready_arr));
                                $relay_orderready_result = json_decode($orderready_result,true);
                            //order ready api curl call :: end
                            //save order ready details in DB :: start
                                $createorder_data = array(
                                    'order_id' => $order_id,
                                    'delivery_method' => 'relay',
                                    'api_slug' => 'order_ready',
                                    'relay_order_key' => ($relay_createorder_result['order']['orderKey'])?$relay_createorder_result['order']['orderKey']:NULL,
                                    'api_request' => serialize($relay_orderready_arr),
                                    'api_response' => serialize($relay_orderready_result),
                                    'created_date' => date('Y-m-d H:i:s'),
                                );
                                $this->addData('doordash_relay_details',$createorder_data);
                            //save order ready details in DB :: end
                            $third_party_delivery_charge = $relay_createorder_result['order']['deliveries'][0]['relayFee'];
                            $this->db->set('delivery_method','relay')->where('entity_id',$order_id)->update('order_master');
                            $this->db->set('third_party_delivery_charge',$third_party_delivery_charge)->where('entity_id',$order_id)->update('order_master');

                            $resp_data = array('is_available' => 'yes');
                        } else if($relay_createorder_result['order']['orderKey'] && !$relay_createorder_result['order']['isVoided']) {
                            $this->relay_cancel_order($order_id);
                            $resp_data = array('is_available' => 'no');
                        } else {
                            $resp_data = array('is_available' => 'no');
                        }
                    //create order api response operations :: end
                } else {
                    $resp_data = array('is_available' => 'no');
                }
            //can-deliver api response operations :: end
        }
        return $resp_data;
    }
    public function relay_cancel_order($order_id){
        $cancel_resp_arr = array('status'=>'1', 'error'=>'');
        $this->db->select('relay_order_key');
        $this->db->where('order_id', $order_id);
        $this->db->where('delivery_method', 'relay');
        $this->db->where('api_slug', 'createorder');
        $this->db->order_by('entity_id', 'DESC');
        $this->db->limit(1, 0);
        $relay_details = $this->db->get('doordash_relay_details')->first_row();
        if($relay_details->relay_order_key){
            $relay_cancel_api_arr['orderKey'] = $relay_details->relay_order_key;
            
            $cancel_api_headers = array (
                'accept: application/json',
                'x-relay-auth: '.relay_api_key_auth,
                'Content-type: application/json'
            );

            $cancel_api_url = (relay_sandbox)?relay_sandboxapi_url.'orders/void':relay_api_url.'orders/void';
            $cancel_api_result = $this->generalCurlCall('POST',$cancel_api_url, $cancel_api_headers, json_encode($relay_cancel_api_arr));
            
            $relay_cancel_api_result = json_decode($cancel_api_result,true);
            //save cancel details in DB :: start
                $relaycancel_data = array(
                    'order_id' => $order_id,
                    'delivery_method' => 'relay',
                    'api_slug' => 'cancel',
                    'relay_order_key' => $relay_details->relay_order_key,
                    'api_request' => serialize($relay_cancel_api_arr),
                    'api_response' => serialize($relay_cancel_api_result),
                    'created_date' => date('Y-m-d H:i:s'),
                );
                $this->addData('doordash_relay_details',$relaycancel_data);
            //save cancel details in DB :: end
            if($relay_cancel_api_result['success']){
                $cancel_resp_arr = array('status'=>'1', 'error'=>'');
            } else if($relay_cancel_api_result['message']) {
                $cancel_resp_arr = array('status'=>'0', 'error'=>$relay_cancel_api_result['message']);
            } else {
                $cancel_resp_arr = array('status'=>'0', 'error'=>'');
            }
        }
        return $cancel_resp_arr;
    }
    public function updateRelayDriverTip($order_id, $driver_tip)
    {
        $update_resp_arr = array('status'=>'1', 'error'=>'');
        $this->db->select('relay_order_key,external_delivery_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('delivery_method', 'relay');
        $this->db->where('api_slug', 'createorder');
        $this->db->order_by('entity_id', 'DESC');
        $this->db->limit(1, 0);
        $relay_details = $this->db->get('doordash_relay_details')->first_row();
        if($relay_details->relay_order_key){
            //bulk-tip-upload api curl call :: start
                //bulk-tip-upload post fields
                $relay_bulktipupload_arr['tips'][0] = array();
                $relay_bulktipupload_arr['tips'][0]['externalId'] = ($relay_details->external_delivery_id) ? $relay_details->external_delivery_id : $order_id;
                $relay_bulktipupload_arr['tips'][0]['date'] = date('Y-m-d');
                $relay_bulktipupload_arr['tips'][0]['producerLocationKey'] = producer_location_key;
                $relay_bulktipupload_arr['tips'][0]['tip'] = $driver_tip;

                $bulktipupload_headers = array (
                    'accept: application/json',
                    'x-relay-auth: '.relay_api_key_auth,
                    'Content-type: application/json'
                );
                $bulktipupload_api_url = (relay_sandbox)?relay_sandboxapi_url.'orders/bulk-tip-upload':relay_api_url.'orders/bulk-tip-upload';
                $bulktipupload_result = $this->generalCurlCall('POST',$bulktipupload_api_url, $bulktipupload_headers, json_encode($relay_bulktipupload_arr));
            //bulk-tip-upload api curl call :: end

            $relay_bulktipupload_result = json_decode($bulktipupload_result,true);
            //save update details in DB :: start
                $relay_bulktipupload_data = array(
                    'order_id' => $order_id,
                    'delivery_method' => 'relay',
                    'api_slug' => 'bulk_tip_upload',
                    'relay_order_key' => $relay_details->relay_order_key,
                    'api_request' => serialize($relay_bulktipupload_arr),
                    'api_response' => serialize($relay_bulktipupload_result),
                    'created_date' => date('Y-m-d H:i:s'),
                );
                $this->addData('doordash_relay_details',$relay_bulktipupload_data);
            //save update details in DB :: end

            if($relay_bulktipupload_result['success']){
                $update_resp_arr = array('status'=>'1', 'error'=>'');
            } else if($relay_bulktipupload_result['message']) {
                $update_resp_arr = array('status'=>'0', 'error'=>$relay_bulktipupload_result['message']);
            } else {
                $update_resp_arr = array('status'=>'0', 'error'=>'');
            }
        }
        return $update_resp_arr;
    }
    //relay integration :: end
    //doorDash integration :: start
    public function checkDeliveryAvailableInDoorDash($order_id, $is_scheduled = '')
    {
        $resp_data = array('is_available' => 'no');
        if($order_id) {
            $order_dtl = $this->getDoorDash_OrderDetails($order_id);
            //pickup details :: start
                $restaurant_detail = json_encode(unserialize($order_dtl->restaurant_detail));
                $restaurant_detail = json_decode($restaurant_detail,true);
                $pickup_phone_number = ($order_dtl->restaurant_phone_code)?'+'.$order_dtl->restaurant_phone_code.$order_dtl->restaurant_phone_number:$order_dtl->restaurant_phone_number;

                $pickup_city = ($restaurant_detail['city'])?$restaurant_detail['city']:(($order_dtl->restaurant_city)?$order_dtl->restaurant_city:'');
                $pickup_state = ($restaurant_detail['state'])?$restaurant_detail['state']:(($order_dtl->restaurant_state)?$order_dtl->restaurant_state:'');
                $pickup_street = '';
                $pickup_unit = ($restaurant_detail['landmark'])?$restaurant_detail['landmark']:(($order_dtl->restaurant_landmark)?$order_dtl->restaurant_landmark:''); //landmark
                $pickup_zip_code = ($restaurant_detail['zipcode'])?$restaurant_detail['zipcode']:(($order_dtl->restaurant_zipcode)?$order_dtl->restaurant_zipcode:'');
                $pickup_full_address = $restaurant_detail['address']?$restaurant_detail['address']:(($order_dtl->restaurant_address)?$order_dtl->restaurant_address:'');

                $res_address_arr = array();
                if($pickup_city == '' || $pickup_state == '' || $pickup_street == '' || $pickup_zip_code == ''){
                    $res_lat = ($restaurant_detail['latitude'])?$restaurant_detail['latitude']:$order_dtl->restaurant_latitude;
                    $res_long = ($restaurant_detail['longitude'])?$restaurant_detail['longitude']:$order_dtl->restaurant_longitude;
                    $res_address_arr = $this->getAddressWithDetails($res_lat,$res_long);
                    if($pickup_city == ''){
                        $pickup_city = $res_address_arr['city'];
                    }
                    if($pickup_state == ''){
                        $pickup_state = $res_address_arr['state'];
                    }
                    if($pickup_street == ''){
                        $pickup_street = $res_address_arr['street'];
                    }
                    if($pickup_zip_code == ''){
                        $pickup_zip_code = $res_address_arr['zipcode'];
                    }
                    if($pickup_full_address == ''){
                        $pickup_full_address = $res_address_arr['address'];
                    }
                }
                $pickup_address = array(
                    'city' => $pickup_city, 
                    'state' => $pickup_state,
                    'street' => $pickup_street,
                    'unit' => $pickup_unit,
                    'zip_code' => $pickup_zip_code,
                    'full_address' => $pickup_full_address,
                );
            //pickup details :: end
            //dropoff details :: start
                $user_detail = json_encode(unserialize($order_dtl->user_detail));
                $user_detail = json_decode($user_detail,true);
                $dropoff_phone_number = ($order_dtl->user_mobile_number)?'+'.$order_dtl->user_mobile_number:(($order_dtl->phone_code)?'+'.$order_dtl->phone_code.$order_dtl->mobile_number:$order_dtl->mobile_number);
                
                $dropoff_city = ($user_detail['city'])?$user_detail['city']:(($order_dtl->user_city)?$order_dtl->user_city:'');
                $dropoff_state = ($user_detail['state'])?$user_detail['state']:(($order_dtl->user_state)?$order_dtl->user_state:'');
                $dropoff_street = '';
                $dropoff_unit = ($user_detail['landmark'])?$user_detail['landmark']:(($order_dtl->user_landmark)?$order_dtl->user_landmark:''); //landmark
                $dropoff_zip_code = ($user_detail['zipcode'])?$user_detail['zipcode']:(($order_dtl->user_zipcode)?$order_dtl->user_zipcode:'');
                $dropoff_full_address = $user_detail['address']?$user_detail['address']:(($order_dtl->user_address)?$order_dtl->user_address:'');

                $user_address_arr = array();
                if($dropoff_city == '' || $dropoff_state == '' || $dropoff_street == '' || $dropoff_zip_code == ''){
                    $user_lat = ($user_detail['latitude'])?$user_detail['latitude']:$order_dtl->user_latitude;
                    $user_long = ($user_detail['longitude'])?$user_detail['longitude']:$order_dtl->user_longitude;
                    $user_address_arr = $this->getAddressWithDetails($user_lat,$user_long);
                    if($dropoff_city == ''){
                        $dropoff_city = $user_address_arr['city'];
                    }
                    if($dropoff_state == ''){
                        $dropoff_state = $user_address_arr['state'];
                    }
                    if($dropoff_street == ''){
                        $dropoff_street = $user_address_arr['street'];
                    }
                    if($dropoff_zip_code == ''){
                        $dropoff_zip_code = $user_address_arr['zipcode'];
                    }
                    if($dropoff_full_address == ''){
                        $dropoff_full_address = $user_address_arr['address'];
                    }
                }
                $dropoff_address = array(
                    'city' => $dropoff_city, 
                    'state' => $dropoff_state,
                    'street' => $dropoff_street,
                    'unit' => $dropoff_unit,
                    'zip_code' => $dropoff_zip_code,
                    'full_address' => $dropoff_full_address,
                );
            //dropoff details :: end
            //customer details :: start
                $customer_details_arr = array(
                    'phone_number' => $dropoff_phone_number,
                    'first_name' => ($user_detail['first_name'])?$user_detail['first_name']:$order_dtl->first_name,
                    'last_name' => ($user_detail['last_name'])?$user_detail['last_name']:$order_dtl->last_name,
                    'email' => ($user_detail['email'])?$user_detail['email']:$order_dtl->email,
                    'should_send_notifications' => true,
                    'locale' => 'en-US'
                );
                $delivery_instructions = ($order_dtl->delivery_instructions)?$order_dtl->delivery_instructions:NULL;
            //customer details :: end
            //item details :: start
                $item_detail = json_encode(unserialize($order_dtl->item_detail));
                $item_detail = json_decode($item_detail,true);
                $total_items = count($item_detail); //total item count
                $item_names_arr = array();

                foreach ($item_detail as $itemkey => $itemvalue) {
                    $item_names_arr[$itemkey]['name'] = $itemvalue['item_name'];
                    $item_names_arr[$itemkey]['quantity'] = $itemvalue['qty_no'];
                    $item_names_arr[$itemkey]['external_id'] = $itemvalue['item_id'];
                    $item_names_arr[$itemkey]['price'] = round($itemvalue['itemTotal'],2)*100;
                }
            //item details :: end

            //Code for add extra time with selected mode :: Start
            $PREPARATION_MINUTES = (PREPARATION_MINUTES)?PREPARATION_MINUTES:0;
            $orderschedule_mode = ($order_dtl->schedule_mode)?$order_dtl->schedule_mode:0;
            $orderschedule_time = 0;
            if($orderschedule_mode==0)
            {
                $time_slotvalarr = $this->getSystemOptoin('schedule_normal_end');
                $orderschedule_time = $time_slotvalarr->OptionValue;
            }
            else if($orderschedule_mode==1)
            {
                $time_slotvalarr = $this->getSystemOptoin('schedule_busy_end');
                $orderschedule_time = $time_slotvalarr->OptionValue;
            }
            else if($orderschedule_mode==2)
            {
                $time_slotvalarr = $this->getSystemOptoin('schedule_verybusy_end');
                $orderschedule_time = $time_slotvalarr->OptionValue;
            }
            if($orderschedule_time > 0) {
                $PREPARATION_MINUTES = $PREPARATION_MINUTES + $orderschedule_time;                
            }
            //Code for add extra time with selected mode :: End

            //pickup/dropoff time :: start
                if($is_scheduled == 1 && $order_dtl->scheduled_date && $order_dtl->slot_open_time) {
                    $order_scheduled_datetime = date('Y-m-d H:i:s', strtotime("$order_dtl->scheduled_date $order_dtl->slot_open_time"));
                    //$pickup_time = date_create($order_scheduled_datetime." + ".$PREPARATION_MINUTES." minutes");
                    $pickup_time = date_create($order_scheduled_datetime);
                    $pickup_time = date_format($pickup_time, "Y-m-d\TH:i:s\Z");
                } else {
                    $pickup_time = date_create("+ ".$PREPARATION_MINUTES." minutes");
                    $pickup_time = date_format($pickup_time, "Y-m-d\TH:i:s\Z");
                }
                
                $pickup_window_start_time = $pickup_time;
                $pickup_window_end_time = date_create($pickup_time." + 10 minutes");
                $pickup_window_end_time = date_format($pickup_window_end_time, "Y-m-d\TH:i:s\Z");

                $dropoff_time = date_create($pickup_time." + 30 minutes");
                $dropoff_time = date_format($dropoff_time, "Y-m-d\TH:i:s\Z");
                $delivery_window_start_time = $dropoff_time;
                $delivery_window_end_time = date_create($pickup_time." + 40 minutes");
                $delivery_window_end_time = date_format($delivery_window_end_time, "Y-m-d\TH:i:s\Z");
            //pickup/dropoff time :: end
            //order total, driver tip, external delivery id, currency, business name :: start
                // $order_value = round($order_dtl->order_value,2)*100;
                // $order_value = (int)$order_value;
                $order_total = round($order_dtl->order_total,2)*100;
                $order_value = (int)$order_total; //sending order total as subtotal
                $driver_tip = ($order_dtl->driver_tip)?round($order_dtl->driver_tip,2)*100:0;
                //$external_delivery_id = $order_id;
                $external_delivery_id = time().$order_id;
                $business_name = $order_dtl->restaurant_name;
                $default_currency = get_default_system_currency();
                if(!empty($default_currency)){
                    $currency_details = $default_currency;
                }else{
                    $currency_details = $this->getRestaurantCurrencySymbol($order_dtl->restaurant_id);
                }
                $currency_code = $currency_details->currency_code;
            //order total, driver tip, external delivery id, currency, business name :: end
            //estimates api curl call :: start
                //estimates post fields
                $doordash_estimates_arr = array('pickup_address'=> $pickup_address, 'dropoff_address'=>$dropoff_address, 'order_value'=>$order_value, 'pickup_time'=> $pickup_time, 'external_store_id'=> $order_dtl->restaurant_id, 'external_business_name'=> "Haus des Döners"); //'delivery_time'=>$dropoff_time, 
                
                $jwt = $this->createJWT(); //create JWT (JSON Web Tokens)
                
                $estimates_headers = array (
                    'Authorization: Bearer '.$jwt,
                    'Content-type: application/json',
                );
                $estimates_api_url = doordash_url.'estimates';
                $estimates_result = $this->generalCurlCall('POST',$estimates_api_url, $estimates_headers, json_encode($doordash_estimates_arr));
            //estimates api curl call :: end
            //estimates api response operations :: start
                $doordash_estimates_result = json_decode($estimates_result,true);
                //save estimate details in DB :: start
                    $estimate_data = array(
                        'order_id' => $order_id,
                        'delivery_method' => 'doordash',
                        'api_slug' => 'estimates',
                        'delivery_fee' => ($doordash_estimates_result['fee'])?$doordash_estimates_result['fee']:NULL,
                        'currency_code' => ($doordash_estimates_result['currency'])?$doordash_estimates_result['currency']:NULL,
                        'pickup_time' => ($doordash_estimates_result['pickup_time'])?$doordash_estimates_result['pickup_time']:NULL,
                        'delivery_time' => ($doordash_estimates_result['delivery_time'])?$doordash_estimates_result['delivery_time']:NULL,
                        'api_request' => serialize($doordash_estimates_arr),
                        'api_response' => serialize($doordash_estimates_result),
                        'created_date' => date('Y-m-d H:i:s'),
                    );
                    $this->addData('doordash_relay_details',$estimate_data);
                //save estimate details in DB :: end
                if($doordash_estimates_result['id'] && $doordash_estimates_result['fee'])
                {
                    //validations api curl call :: start
                        //validations post fields 
                        $doordash_validations_arr = array('pickup_address'=> $pickup_address, 'pickup_phone_number'=>$pickup_phone_number, 'dropoff_address'=>$dropoff_address, 'customer'=>$customer_details_arr, 'order_value'=>$order_value, 'pickup_time'=> $pickup_time, 'external_delivery_id'=>$external_delivery_id, 'barcode_scanning_required'=>false, 'num_items'=> $total_items, 'external_store_id'=> $order_dtl->restaurant_id, 'external_business_name'=> "Haus des Döners", 'signature_required'=>false); //'delivery_time'=>$dropoff_time,
                        if($driver_tip>0){
                            $doordash_validations_arr['tip'] = $driver_tip;
                        }
                        if($delivery_instructions != '' && $delivery_instructions != NULL){
                            $doordash_validations_arr['dropoff_instructions'] = $delivery_instructions;
                        }
                        $validations_headers = array (
                            'Authorization: Bearer '.$jwt,
                            'Content-type: application/json',
                        );
                        $validations_api_url = doordash_url.'validations';
                        $validations_result = $this->generalCurlCall('POST',$validations_api_url, $validations_headers, json_encode($doordash_validations_arr));
                    //validations api curl call :: end
                    //validations api response operations :: start
                        $doordash_validations_result = json_decode($validations_result,true);
                        //save validations details in DB :: start
                            $validations_data = array(
                                'order_id' => $order_id,
                                'delivery_method' => 'doordash',
                                'api_slug' => 'validations',
                                'external_delivery_id' => $external_delivery_id,
                                'api_request' => serialize($doordash_validations_arr),
                                'api_response' => serialize($doordash_validations_result),
                                'created_date' => date('Y-m-d H:i:s'),
                            );
                            $this->addData('doordash_relay_details',$validations_data);
                        //save validations details in DB :: end
                        if($doordash_validations_result['valid'] == 1){
                            //deliveries api curl call :: start
                                //deliveries post fields 
                                $doordash_deliveries_arr = array('pickup_address'=> $pickup_address, 'pickup_phone_number'=>$pickup_phone_number, 'dropoff_address'=>$dropoff_address, 'customer'=>$customer_details_arr, 'order_value'=>$order_value, 'pickup_time'=> $pickup_time, 'items'=>$item_names_arr, 'team_lift_required'=>false, 'barcode_scanning_required'=>false, 'pickup_business_name'=>$business_name, 'external_delivery_id'=>$external_delivery_id, 'external_business_name'=> "Haus des Döners", 'external_store_id'=> $order_dtl->restaurant_id, 'requires_catering_setup'=>false, 'num_items'=> $total_items, 'signature_required'=>false); //'delivery_time'=>$dropoff_time, 'pickup_window_start_time'=>$pickup_window_start_time, 'pickup_window_end_time'=>$pickup_window_end_time, 'delivery_window_start_time'=>$delivery_window_start_time, 'delivery_window_end_time'=>$delivery_window_end_time, 

                                if($driver_tip>0){
                                    $doordash_deliveries_arr['tip'] = $driver_tip;
                                }
                                if($delivery_instructions != '' && $delivery_instructions != NULL){
                                    $doordash_deliveries_arr['dropoff_instructions'] = $delivery_instructions;
                                }
                                if($order_dtl->payment_option == 'cod'){
                                    $doordash_deliveries_arr['cash_on_delivery'] = $order_total;
                                    $doordash_deliveries_arr['is_contactless_delivery'] = false;
                                } else {
                                    $doordash_deliveries_arr['is_contactless_delivery'] = true;
                                }
                                $deliveries_headers = array (
                                    'Authorization: Bearer '.$jwt,
                                    'Content-type: application/json',
                                );
                                $deliveries_api_url = doordash_url.'deliveries';
                                $deliveries_result = $this->generalCurlCall('POST',$deliveries_api_url, $deliveries_headers, json_encode($doordash_deliveries_arr));
                            //deliveries api curl call :: end
                            //deliveries api response operations :: start
                                $doordash_deliveries_result = json_decode($deliveries_result,true);
                                //save deliveries details in DB :: start
                                    $deliveries_data = array(
                                        'order_id' => $order_id,
                                        'delivery_method' => 'doordash',
                                        'api_slug' => 'deliveries',
                                        'doordash_delivery_id' => ($doordash_deliveries_result['id'])?$doordash_deliveries_result['id']:NULL,
                                        'external_delivery_id'=>$external_delivery_id,
                                        'delivery_fee' => ($doordash_deliveries_result['fee'])?$doordash_deliveries_result['fee']:NULL,
                                        'currency_code' => ($doordash_deliveries_result['currency'])?$doordash_deliveries_result['currency']:NULL,
                                        'pickup_time' => ($doordash_deliveries_result['estimated_pickup_time'])?$doordash_deliveries_result['estimated_pickup_time']:NULL,
                                        'delivery_time' => ($doordash_deliveries_result['estimated_delivery_time'])?$doordash_deliveries_result['estimated_delivery_time']:NULL,
                                        'delivery_tracking_url' => ($doordash_deliveries_result['delivery_tracking_url'])?$doordash_deliveries_result['delivery_tracking_url']:NULL,
                                        'api_request' => serialize($doordash_deliveries_arr),
                                        'api_response' => serialize($doordash_deliveries_result),
                                        'created_date' => date('Y-m-d H:i:s'),
                                    );
                                    $this->addData('doordash_relay_details',$deliveries_data);
                                //save deliveries details in DB :: end
                                if($doordash_deliveries_result['id'] && $doordash_deliveries_result['fee']){
                                    $third_party_delivery_charge = number_format(($deliveries_data['delivery_fee'] /100), 2, '.', ' ');
                                    $third_party_delivery_data = array('doordash_id' => $deliveries_data['doordash_delivery_id'], 'delivery_tracking_url' => $deliveries_data['delivery_tracking_url'], 'estimated_pickup_time' => $deliveries_data['pickup_time']);
                                    $this->db->set('delivery_method','doordash')->where('entity_id',$order_id)->update('order_master');
                                    $this->db->set('delivery_tracking_url',$deliveries_data['delivery_tracking_url'])->where('entity_id',$order_id)->update('order_master');
                                    $this->db->set('third_party_delivery_charge',$third_party_delivery_charge)->where('entity_id',$order_id)->update('order_master');
                                    $this->db->set('third_party_delivery_data',serialize($third_party_delivery_data))->where('entity_id',$order_id)->update('order_master');

                                    $resp_data = array('is_available' => 'yes');
                                } else if($doordash_deliveries_result['field_errors']) {
                                    $resp_data = array('is_available' => 'no', 'error'=>$doordash_deliveries_result['field_errors'][0]['error']);
                                }
                            //deliveries api response operations :: end
                        } else if($doordash_validations_result['errors']) {
                            $resp_data = array('is_available' => 'no', 'error'=>$doordash_validations_result['errors'][0][1]);
                        }
                    //validations api response operations :: end
                } 
                else if(!empty($doordash_estimates_result['field_errors']))
                {
                    $resp_data = array('is_available' => 'no', 'error'=>$doordash_quote_result['field_errors'][0]['error']);
                }
            //estimates api response operations :: end
        }
        return $resp_data;
    }
    public function getDoorDash_OrderDetails($order_id)
    {
        $this->db->select("order_detail.user_detail,order_detail.user_mobile_number, order_detail.restaurant_detail, order_detail.item_detail, order_master.order_date, users.first_name,users.last_name, users.phone_code, users.mobile_number, users.email, restaurant_address.address as restaurant_address, restaurant_address.landmark as restaurant_landmark, restaurant_address.zipcode as restaurant_zipcode, restaurant_address.state as restaurant_state, restaurant_address.city as restaurant_city, restaurant_address.latitude as restaurant_latitude, restaurant_address.longitude as restaurant_longitude, user_address.address as user_address, user_address.landmark as user_landmark, user_address.zipcode as user_zipcode, user_address.city  as user_city, user_address.state as user_state, user_address.latitude as user_latitude, user_address.longitude as user_longitude, order_master.subtotal as order_value, order_master.total_rate as order_total, order_master.payment_option, restaurant.phone_number as restaurant_phone_number, restaurant.phone_code as restaurant_phone_code, restaurant.email as restaurant_email, restaurant.entity_id as restaurant_id, restaurant.name as restaurant_name,tips.amount as driver_tip,order_master.coupon_name,order_master.coupon_discount,order_master.delivery_charge,order_master.tax_type,order_master.service_fee_type,order_master.creditcard_fee_type,order_master.tax_rate,order_master.service_fee,order_master.creditcard_fee,order_master.transaction_id,order_master.refund_status,order_master.payment_option,order_master.delivery_method,order_master.user_id,tips.tips_transaction_id,tips.refund_status as tips_refund_status, order_master.delivery_instructions,order_master.scheduled_date,order_master.slot_open_time,restaurant.schedule_mode");

        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->join('users','order_master.user_id = users.entity_id','left');
        $this->db->join('order_detail','order_detail.order_id = order_master.entity_id','left');
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $result = $this->db->get('order_master')->first_row();
    }
    public function base64UrlEncode(string $data)
    {
        $base64Url = strtr(base64_encode($data), '+/', '-_');
        return rtrim($base64Url, '=');
    }
    public function base64UrlDecode(string $base64Url)
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }
    public function createJWT(){
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
            'dd-ver' => 'DD-JWT-V1'
        ]);

        $payload = json_encode([
            'aud' => 'doordash',
            'iss' => doordash_developer_id,
            'kid' => doordash_key_id,
            'exp' => time() + 60,
            'iat' => time()
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->base64UrlDecode(doordash_signing_secret), true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }
    public function getAddressWithDetails($latitude,$longitude){ 
        $address_arr = array('address'=>'','city' => '','state' => '','country' => '', 'zipcode'=>'', 'street'=>'');
        if(!empty($latitude) && !empty($longitude)){
            //Send request and receive json data by address
            $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&key='.google_key); 
            $output = json_decode($geocodeFromLatLong);
            $status = $output->status;
            //Get address from json data
            $address = ($status=="OK")?(($output->results[0]->formatted_address)?$output->results[0]->formatted_address:$output->results[1]->formatted_address):'';
            //Return address of the given latitude and longitude
            if(!empty($address)) {
                $address_arr = array('address'=>$address,'city' => '','state' => '','country' => '', 'zipcode'=>'', 'street'=>'');
                foreach ($output->results[0]->address_components as $key => $value) {
                    foreach ($value->types as $c_key => $c_value) {                      
                        if($c_value=='administrative_area_level_2'){                            
                            $address_arr['city'] = $value->long_name;
                        }
                        if($c_value=='administrative_area_level_1'){
                            $address_arr['state'] = $value->long_name;
                        }
                        if($c_value=='country'){
                            $address_arr['country'] = $value->long_name;
                        }
                        if($c_value=='postal_code'){
                            $address_arr['zipcode'] = $value->long_name;
                        }
                        if($c_value=='street_number'){
                            $address_arr['street'] .= $value->long_name;
                        }
                        if ($c_value=='route') {
                            $address_arr['street'] .= ($address_arr['street'] != '')?' '.$value->long_name:$value->long_name;
                        }
                        if ($c_value=='neighborhood') {
                            $address_arr['street'] .= ($address_arr['street'] != '')?', '.$value->long_name:$value->long_name;
                        }
                    }
                }

                if ($address_arr['city'] == '' || $address_arr['state'] == '' || $address_arr['country'] == '' || $address_arr['zipcode'] == '' || $address_arr['street'] == '') {
                    foreach ($output->results[1]->address_components as $key1 => $value1) {
                        foreach ($value1->types as $c_key1 => $c_value1) {                      
                            if($address_arr['city'] == '' && $c_value1=='administrative_area_level_2'){
                                $address_arr['city'] = $value1->long_name;
                            }
                            if($address_arr['state'] == '' && $c_value1=='administrative_area_level_1'){
                                $address_arr['state'] = $value1->long_name;
                            }
                            if($address_arr['country'] == '' && $c_value1=='country'){
                                $address_arr['country'] = $value1->long_name;
                            }
                            if($address_arr['zipcode'] == '' && $c_value1=='postal_code'){
                                $address_arr['zipcode'] = $value1->long_name;
                            }
                            if($address_arr['street'] == ''){
                                if($c_value1=='street_number'){
                                    $address_arr['street'] .= $value1->long_name;
                                }
                                if ($c_value1=='route') {
                                    $address_arr['street'] .= ($address_arr['street'] != '')?' '.$value1->long_name:$value1->long_name;
                                }
                                if ($c_value1=='neighborhood') {
                                    $address_arr['street'] .= ($address_arr['street'] != '')?', '.$value1->long_name:$value1->long_name;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $address_arr;
    }
    public function doordash_cancel_order($order_id){
        $cancel_resp_arr = array('status'=>'1', 'error'=>'');
        $this->db->select('doordash_delivery_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('delivery_method', 'doordash');
        $this->db->where('api_slug', 'deliveries');
        $doordash_details = $this->db->get('doordash_relay_details')->first_row();
        if($doordash_details->doordash_delivery_id){
            $jwt = $this->createJWT(); //create JWT (JSON Web Tokens)
                
            $cancel_api_headers = array (
                'Authorization: Bearer '.$jwt,
                'Content-type: application/json',
            );
            $cancel_api_url = doordash_url.'deliveries/'.$doordash_details->doordash_delivery_id.'/cancel';
            $cancel_api_result = $this->generalCurlCall('PUT',$cancel_api_url, $cancel_api_headers);
            
            $doordash_cancel_api_result = json_decode($cancel_api_result,true);
            //save cancel details in DB :: start
                $deliveries_data = array(
                    'order_id' => $order_id,
                    'delivery_method' => 'doordash',
                    'api_slug' => 'cancel',
                    'doordash_delivery_id' => $doordash_details->doordash_delivery_id,
                    'api_response' => serialize($doordash_cancel_api_result),
                    'created_date' => date('Y-m-d H:i:s'),
                );
                $this->addData('doordash_relay_details',$deliveries_data);
            //save cancel details in DB :: end

            if($doordash_cancel_api_result['cancelled_at']){
                $cancel_resp_arr = array('status'=>'1', 'error'=>'');
            } else if($doordash_cancel_api_result['detail']) {
                $cancel_resp_arr = array('status'=>'0', 'error'=>$doordash_cancel_api_result['detail']);
            } else {
                $cancel_resp_arr = array('status'=>'0', 'error'=>'');
            }
        }
        return $cancel_resp_arr;
    }
    public function getDoordashDriver($order_id)
    {
        $this->db->select('driver_details');
        $this->db->where('order_id', $order_id);
        $this->db->where('driver_details !=', NULL);
        $this->db->where('delivery_method', 'doordash');
        $this->db->where('api_slug', 'callback_function');
        $this->db->order_by('entity_id', 'DESC');
        $this->db->limit(1, 0);
        $doordash_data = $this->db->get('doordash_relay_details')->first_row();
        if($doordash_data->driver_details) {
            return unserialize($doordash_data->driver_details);
        } else {
            return false;
        }
    }
    public function updateDoorDashDriverTip($order_id, $driver_tip)
    {
        $update_resp_arr = array('status'=>'1', 'error'=>'');
        $this->db->select('doordash_delivery_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('delivery_method', 'doordash');
        $this->db->where('api_slug', 'deliveries');
        $doordash_details = $this->db->get('doordash_relay_details')->first_row();
        if($doordash_details->doordash_delivery_id){
            $order_dtl = $this->getDoorDash_OrderDetails($order_id);
            //pickup details :: start
                $business_name = $order_dtl->restaurant_name;
                $pickup_phone_number = ($order_dtl->restaurant_phone_code)?'+'.$order_dtl->restaurant_phone_code.$order_dtl->restaurant_phone_number:$order_dtl->restaurant_phone_number;
            //pickup details :: end
            //dropoff details :: start
                $user_detail = json_encode(unserialize($order_dtl->user_detail));
                $user_detail = json_decode($user_detail,true);
                $dropoff_phone_number = ($order_dtl->user_mobile_number)?'+'.$order_dtl->user_mobile_number:(($order_dtl->phone_code)?'+'.$order_dtl->phone_code.$order_dtl->mobile_number:$order_dtl->mobile_number);
                
                $customer_fname = ($user_detail['first_name'])?$user_detail['first_name']:$order_dtl->first_name;
                $customer_lname = ($user_detail['last_name'])?$user_detail['last_name']:$order_dtl->last_name;
            //dropoff details :: end
            $doordash_update_arr = array(
                'pickup_business_name' => $business_name,
                'pickup_phone_number' => $pickup_phone_number,
                'first_name' => $customer_fname,
                'last_name' => $customer_lname,
                'customer_phone_number' => $dropoff_phone_number,
                'tip' => $driver_tip,
            );

            $jwt = $this->createJWT(); //create JWT (JSON Web Tokens)

            $update_api_headers = array (
                'Authorization: Bearer '.$jwt,
                'Content-type: application/json',
            );

            $update_api_url = doordash_url.'deliveries/'.$doordash_details->doordash_delivery_id;
            $update_api_result = $this->generalCurlCall('PATCH',$update_api_url, $update_api_headers, json_encode($doordash_update_arr));
            
            $doordash_update_api_result = json_decode($update_api_result,true);
            //save update details in DB :: start
                $deliveries_data = array(
                    'order_id' => $order_id,
                    'delivery_method' => 'doordash',
                    'api_slug' => 'update',
                    'doordash_delivery_id' => $doordash_details->doordash_delivery_id,
                    'api_request' => serialize($doordash_update_arr),
                    'api_response' => serialize($doordash_update_api_result),
                    'created_date' => date('Y-m-d H:i:s'),
                );
                $this->addData('doordash_relay_details',$deliveries_data);
            //save update details in DB :: end

            if($doordash_update_api_result['id'] && $doordash_update_api_result['fee']) {
                $update_resp_arr = array('status'=>'1', 'error'=>'');
            } else if($doordash_update_api_result['field_errors']) {
                $update_resp_arr = array('status'=>'0', 'error'=>$doordash_update_api_result['field_errors'][0]['error']);
            } else {
                $update_resp_arr = array('status'=>'0', 'error'=>'');
            }
        }
        return $update_resp_arr;
    }
    //doorDash integration :: end
    //refund stripe method :: start
    public function StripeRefund($transaction_id='',$order_id,$tips_transaction_id='',$tips_entityid='',$tip_payment_option='', $refund_reason = '',$refund_full_partial='full',$partial_refundedamt=0)
    {
        //Cheke full/partial refund :: Start
        if($partial_refundedamt==0)
        {
            $refund_full_partial = 'full';
        }
        //End

        unset($stripe);
        $resp_data = array('error' => 'yes');
        $flag = ($transaction_id!='' && $tips_transaction_id!='' && $transaction_id==$tips_transaction_id)?1:0;


        // Include the Stripe PHP bindings library 
        require_once(APPPATH .'third_party/stripe-php/init.php');
        $stripe_info = $this->get_payment_method_detail('stripe');
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
        $stripe = new \Stripe\StripeClient($stripe_api_key);
        $refundreturn_status = '';
        try{
            if($transaction_id!=''){
                $paymentconfirm = $stripe->paymentIntents->retrieve($transaction_id);
                if($paymentconfirm->id){                    
                    if($refund_full_partial=='partial' && $partial_refundedamt>0)
                    {
                        $paymentIntentstatus = $stripe->refunds->create([
                            'payment_intent' => $transaction_id,
                            'amount' => $partial_refundedamt * 100
                        ]);
                    }
                    else
                    {
                        $paymentIntentstatus = $stripe->refunds->create([
                            'payment_intent' => $transaction_id,
                            //'amount' => $order_total * 100
                        ]);
                    }
                    $refundstatus = $stripe->refunds->retrieve($paymentIntentstatus->id,[]);
                    if($refund_full_partial=='partial' && $partial_refundedamt>0)
                    {
                        $status = ($refundstatus->status=='succeeded') ? 'partial refunded' : $refundstatus->status;
                    }
                    else
                    {
                        $status = ($refundstatus->status=='succeeded') ? 'refunded' : $refundstatus->status;
                    }
                    $tipstatus = ($refundstatus->status=='succeeded') ? 'refunded' : $refundstatus->status;

                    //Code for refund amount calculation and reason :: Start
                    $refund_amount = round(($refundstatus->amount/100),2);
                    $oldrefund_amountarr = $this->db->select('refunded_amount,refund_reason,total_rate')->get_where('order_master',array('entity_id'=>$order_id))->first_row();
                    $refund_amountold = $total_rate = 0; $old_refund_reason='';
                    if($oldrefund_amountarr && !empty($oldrefund_amountarr))
                    {
                        $refund_amountold = ($oldrefund_amountarr->refunded_amount && floatval($oldrefund_amountarr->refunded_amount)>0)?$oldrefund_amountarr->refunded_amount:0;
                        $old_refund_reason = ($oldrefund_amountarr->refund_reason && $oldrefund_amountarr->refund_reason!='')?$oldrefund_amountarr->refund_reason:'';
                        $total_rate = $oldrefund_amountarr->total_rate;
                    }
                    $refund_amount = $refund_amount+$refund_amountold;
                    //Code for reason
                    $tiprefund_reason = $refund_reason;
                    if($old_refund_reason!='')
                    {
                        $refund_reason = ($refund_reason!='')?$refund_reason.'<br/>'.$old_refund_reason:$old_refund_reason;
                    }
                    //Code for refund amount calculation and reason :: End

                    if($status=='partial refunded' && round($total_rate,2)<=round($refund_amount,2))
                    {
                        $status = 'refunded';
                        $refundreturn_status = 'refunded';
                        $tipstatus = 'refunded';
                        $refund_full_partial='full';
                    }                    

                    $this->updateData('order_master',array('refunded_amount'=>$refund_amount, 'stripe_refund_id'=>$refundstatus->id, 'refund_status'=>$status),'entity_id',$order_id);
                    if($refund_reason != '') {
                        $this->updateData('order_master',array('refund_reason'=>$refund_reason),'entity_id',$order_id);
                    }
                    $resp_data = array(
                        'paymentIntentid' => $transaction_id,
                        'paymentIntentstatus' => $status,
                    );

                    //Code for data insert in refund log :: Start
                    $refund_log_array = array(
                        'order_id'=>$order_id,
                        'transaction_id'=>$refundstatus->id,
                        'payment_option'=>'stripe',
                        'refund_amount'=>round(($refundstatus->amount/100),2),
                        'refund_reason'=>$tiprefund_reason,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );
                    $this->db->insert("partial_refund_log",$refund_log_array);
                    //Code for data insert in refund log :: End

                    if($flag == 1 && $refund_full_partial == 'full') {
                        $this->updateData('tips',array('refunded_amount'=>round(($refundstatus->amount/100),2), 'stripe_refund_id'=>$refundstatus->id, 'refund_status'=>$tipstatus),'order_id',$order_id);


                        if($refund_reason != '') {
                            $this->updateData('tips',array('refund_reason'=>$tiprefund_reason),'order_id',$order_id);
                        }
                        $resp_data['tips_paymentIntentid'] = $transaction_id;
                        $resp_data['tips_paymentIntentstatus'] = $status;
                    }
                }
            }
            //Code for paypal refund :: Start
            $resp_data['refundreturn_status'] = $refundreturn_status;
            if($tips_transaction_id!='' && $flag==0 && strtolower($tip_payment_option)=='paypal')
            {
                $resp_datatemp = $this->PaypalTip_Refund($tips_transaction_id,$order_id,$tips_entityid,$refund_reason);
                $resp_data['tips_paymentIntentid'] = $resp_datatemp['tips_paymentIntentid'];
                $resp_data['tips_paymentIntentstatus'] = $resp_datatemp['tips_paymentIntentstatus'];
                $resp_data['error'] = '';
                return $resp_data;
            }//End
            else
            {
                try{
                    if($tips_transaction_id!='' && $flag==0)
                    {
                        $tips_paymentconfirm = $stripe->paymentIntents->retrieve($tips_transaction_id);
                        if($tips_paymentconfirm->id){
                            $tips_paymentIntentstatus = $stripe->refunds->create([
                                'payment_intent' => $tips_transaction_id,
                                //'amount' => $order_total * 100
                            ]);
                            $tips_refundstatus = $stripe->refunds->retrieve($tips_paymentIntentstatus->id,[]);
                            $refund_amount = round(($tips_refundstatus->amount/100),2);
                            $tip_status = ($tips_refundstatus->status=='succeeded') ? 'refunded' : $tips_refundstatus->status;
                            if($tips_entityid){
                                $this->updateData('tips',array('refunded_amount'=>$refund_amount, 'stripe_refund_id'=>$tips_refundstatus->id, 'refund_status'=>$tip_status),'entity_id',$tips_entityid);
                                if($refund_reason != '') {
                                    $this->updateData('tips',array('refund_reason'=>$refund_reason),'entity_id',$tips_entityid);
                                }
                            } else {
                                $this->updateData('tips',array('refunded_amount'=>$refund_amount, 'stripe_refund_id'=>$tips_refundstatus->id, 'refund_status'=>$tip_status),'order_id',$order_id);
                                if($refund_reason != '') {
                                    $this->updateData('tips',array('refund_reason'=>$refund_reason),'order_id',$order_id);
                                }
                            }
                            $resp_data['tips_paymentIntentid'] = $tips_transaction_id;
                            $resp_data['tips_paymentIntentstatus'] = $tip_status;
                        }
                    }
                }catch (Exception $e){
                    $resp_data = array('error' => $e->getMessage());
                    return $resp_data;
                }                
                $resp_data['error'] = '';
                return $resp_data;
            }
            
        } catch (Exception $e) {
            $resp_data = array('error' => $e->getMessage());
            return $resp_data;
        }
    }
    //Tip Refund paypal method :: Start
    public function PaypalTip_Refund($tips_transaction_id,$order_id,$tips_entityid='', $refund_reason = '')
    {
        $paypal_detail = $this->get_payment_method_detail('paypal');
        if($paypal_detail->enable_live_mode){
            $paypal_client_id = $paypal_detail->live_client_id;
            $paypal_client_secret = $paypal_detail->live_client_secret;
            $paypal_url_v1 = LIVE_PAYPAL_URL_V1;
            $paypal_url_v2 = LIVE_PAYPAL_URL_V2;
        }else{
            $paypal_client_id = $paypal_detail->sandbox_client_id;
            $paypal_client_secret = $paypal_detail->sandbox_client_secret;
            $paypal_url_v1 = SANDBOX_PAYPAL_URL_V1;
            $paypal_url_v2 = SANDBOX_PAYPAL_URL_V2;
        }

        $basic_token = base64_encode($paypal_client_id.":".$paypal_client_secret);
        $headers = array (
            'Authorization: Basic '. $basic_token,
            'Content-Type: application/x-www-form-urlencoded'
        );
        if($access_token=='')
        {
            #Send Reponse To FireBase Server
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $paypal_url_v1."oauth2/token" );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $resulttoken = curl_exec($ch);
            curl_close($ch);
            $resulttkn_arr = json_decode($resulttoken,true);            
            if((strpos($resulttoken,"400") || strpos($resulttoken,"Bad Request")) && $resulttkn_arr['access_token']!=''){
                $resulttkn_arr['error'] = "error";
                $resp_data['error'] = "error";
                $resp_data['error_message'] = $this->lang->line('intiate_stripe_refunderror');
                return $resp_data;
            }else{    
                $resulttkn_arr = json_decode($resulttoken,true);
                $access_token = $resulttkn_arr['access_token'];
            }            
        }
        if($access_token!='')
        {
            $chtt=curl_init();
            $headers=array('Content-Type: application/json','Authorization: Bearer '.$access_token);

            curl_setopt($chtt,CURLOPT_HTTPHEADER,$headers);

            $tiprefund_url=$paypal_url_v1."payments/sale/".$tips_transaction_id."/refund";
            curl_setopt($chtt, CURLOPT_URL, $tiprefund_url);
            curl_setopt($chtt, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chtt, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($chtt, CURLOPT_POST, true);
            curl_setopt($chtt, CURLOPT_POSTFIELDS, "{}");
            $resulttip = curl_exec($chtt);
            $tipresult_array=json_decode($resulttip,true);                      
            if(strtolower($tipresult_array['state'])=='completed')
            {
                $tip_status = 'refunded';
                if($tips_entityid){
                    $this->updateData('tips',array('refunded_amount'=>$tipresult_array['amount']['total'], 'paypal_refund_id'=>$tipresult_array['id'], 'refund_status'=>$tip_status),'entity_id',$tips_entityid);
                    if($refund_reason != '') {
                        $this->updateData('tips',array('refund_reason'=>$refund_reason),'entity_id',$tips_entityid);
                    }
                } else {
                    $this->updateData('tips',array('refunded_amount'=>$tipresult_array['amount']['total'], 'paypal_refund_id'=>$tipresult_array['id'], 'refund_status'=>$tip_status),'order_id',$order_id);
                    if($refund_reason != '') {
                        $this->updateData('tips',array('refund_reason'=>$refund_reason),'order_id',$order_id);
                    }
                }
                $resp_data['tips_paymentIntentid'] = $tips_transaction_id;
                $resp_data['tips_paymentIntentstatus'] = $tip_status;
                $resp_data['error'] = "";
                return $resp_data;
            }
            else
            {
                $resp_data['error'] = "error";
                $resp_data['error_message'] = $tipresult_array['message'];
                return $resp_data;
            }
        }
        else
        {
            $resp_data['error'] = "error";
            $resp_data['error_message'] = $this->lang->line('intiate_stripe_refunderror');
            return $resp_data;
        }
    }
    //Tip Refund paypal method :: End
    //Refund paypal method :: Start
    public function PaypalRefund($transaction_id='',$order_id,$tips_transaction_id='',$tips_entityid='',$tip_payment_option='', $refund_reason = '',$refund_full_partial='full',$partial_refundedamt=0)
    {
        //Cheke full/partial refund :: Start
        if($partial_refundedamt==0)
        {
            $refund_full_partial = 'full';
        }
        //End
        $refundreturn_status = '';
        $resp_data = array('error' => 'yes');
        if($transaction_id!='' || $tips_transaction_id!='')
        //if($transaction_id!='')
        {
            $flag = ($transaction_id!='' && $tips_transaction_id!='' && $transaction_id==$tips_transaction_id)?1:0;

            $paypal_detail = $this->get_payment_method_detail('paypal');
            if($paypal_detail->enable_live_mode){
                $paypal_client_id = $paypal_detail->live_client_id;
                $paypal_client_secret = $paypal_detail->live_client_secret;
                $paypal_url_v1 = LIVE_PAYPAL_URL_V1;
                $paypal_url_v2 = LIVE_PAYPAL_URL_V2;
            }else{
                $paypal_client_id = $paypal_detail->sandbox_client_id;
                $paypal_client_secret = $paypal_detail->sandbox_client_secret;
                $paypal_url_v1 = SANDBOX_PAYPAL_URL_V1;
                $paypal_url_v2 = SANDBOX_PAYPAL_URL_V2;
            }

            $basic_token = base64_encode($paypal_client_id.":".$paypal_client_secret);
            $headers = array (
                'Authorization: Basic '. $basic_token,
                'Content-Type: application/x-www-form-urlencoded'
            );
            #Send Reponse To FireBase Server
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $paypal_url_v1."oauth2/token" );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $resulttoken = curl_exec($ch);
            curl_close($ch);
            $resulttkn_arr = json_decode($resulttoken,true);
            if((strpos($resulttoken,"400") || strpos($resulttoken,"Bad Request")) && $resulttkn_arr['access_token']!=''){
                $resulttkn_arr['error'] = "error";
            }else{    
                $resulttkn_arr = json_decode($resulttoken,true);
            }

            if($resulttkn_arr['error']=='error')
            {
                $resp_data['error'] = "error";
                $resp_data['error_message'] = $this->lang->line('intiate_stripe_refunderror');
                return $resp_data;
            }
            else
            {
                //Code for main refund :: Start
                if($transaction_id!='')
                {
                    $access_token = $resulttkn_arr['access_token'];
                    
                    $ch=curl_init();
                    $headers=array('Content-Type: application/json','Authorization: Bearer '.$access_token);           
                    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);

                    if($refund_full_partial=='partial' && $partial_refundedamt>0)
                    {
                        $fileds['amount'] = array('total'=>round($partial_refundedamt,2),'currency'=>'USD');
                        $filedsjson = json_encode($fileds);
                        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
                        $refund_url=$paypal_url_v1."payments/sale/".$transaction_id."/refund";
                        curl_setopt($ch, CURLOPT_URL, $refund_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $filedsjson);
                        $result = curl_exec($ch);
                        $result_array=json_decode($result,true);
                        curl_close($ch);
                    }
                    else
                    {
                        $refund_url=$paypal_url_v1."payments/sale/".$transaction_id."/refund";
                        curl_setopt($ch, CURLOPT_URL, $refund_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");
                        $result = curl_exec($ch);
                        $result_array=json_decode($result,true);
                        curl_close($ch);
                    }

                    if(strtolower($result_array['state'])=='completed')
                    {
                        $status = 'refunded'; $tipstatus = 'refunded';
                        $refundreturn_status = 'refunded';
                        if($refund_full_partial=='partial' && $partial_refundedamt>0)
                        {
                            $status = 'partial refunded';
                            $refundreturn_status = '';
                        }

                        //Code for refund amount calculation and reason :: Start
                        $refund_amount = round($result_array['amount']['total'],2);
                        $oldrefund_amountarr = $this->db->select('refunded_amount,refund_reason,total_rate')->get_where('order_master',array('entity_id'=>$order_id))->first_row();
                        $refund_amountold = $total_rate = 0; $old_refund_reason='';
                        if($oldrefund_amountarr && !empty($oldrefund_amountarr))
                        {
                            $refund_amountold = ($oldrefund_amountarr->refunded_amount && floatval($oldrefund_amountarr->refunded_amount)>0)?$oldrefund_amountarr->refunded_amount:0;
                            $old_refund_reason = ($oldrefund_amountarr->refund_reason && $oldrefund_amountarr->refund_reason!='')?$oldrefund_amountarr->refund_reason:'';
                            $total_rate = $oldrefund_amountarr->total_rate;
                        }
                        $refund_amount = $refund_amount+$refund_amountold;
                        //Code for reason
                        $tiprefund_reason = $refund_reason;
                        if($old_refund_reason!='')
                        {
                            $refund_reason = ($refund_reason!='')?$refund_reason.'<br/>'.$old_refund_reason:$old_refund_reason;
                        }
                        //Code for refund amount calculation and reason :: End

                        if($status=='partial refunded' && round($total_rate,2)<=round($refund_amount,2))
                        {
                            $status = 'refunded';
                            $refund_full_partial = 'full';
                            $refundreturn_status = 'refunded';
                        }

                        $resp_data = array(
                            'paymentIntentid' => $transaction_id,
                            'paymentIntentstatus' => $status,
                        );
                        $this->updateData('order_master',array('refunded_amount'=>$refund_amount, 'stripe_refund_id'=>$result_array['id'], 'refund_status'=>$status),'entity_id',$order_id);
                        if($refund_reason != '') {
                            $this->updateData('order_master',array('refund_reason'=>$refund_reason),'entity_id',$order_id);
                        }

                        //Code for data insert in refund log :: Start
                        $refund_log_array = array(
                            'order_id'=>$order_id,
                            'transaction_id'=>$result_array['id'],
                            'payment_option'=>'paypal',
                            'refund_amount'=>round($result_array['amount']['total'],2),
                            'refund_reason'=>$tiprefund_reason,
                            'created_date'=>date('Y-m-d H:i:s'),
                            'created_by'=>$this->session->userdata("AdminUserID")
                        );
                        $this->db->insert("partial_refund_log",$refund_log_array);
                        //Code for data insert in refund log :: End

                        if($flag==1 && $refund_full_partial=='full')
                        {
                            $this->updateData('tips',array('refunded_amount'=>$result_array['amount']['total'], 'paypal_refund_id'=>$result_array['id'], 'refund_status'=>$tipstatus),'order_id',$order_id);
                            if($refund_reason != '') {
                                $this->updateData('tips',array('refund_reason'=>$tiprefund_reason),'order_id',$order_id);
                            }
                            $resp_data['tips_paymentIntentid'] = $transaction_id;
                            $resp_data['tips_paymentIntentstatus'] = $status;
                        }
                    }
                    else
                    {
                        $resp_data['error'] = "error";
                        $resp_data['error_message'] = $result_array['message'];
                        return $resp_data; 
                    }
                }
                //Code for main refund :: End

                //Code for paypal refund :: Start
                $resp_data['refundreturn_status'] = $refundreturn_status;
                if($tips_transaction_id!='' && $flag==0 && strtolower($tip_payment_option)=='paypal')
                {
                    $resp_datatemp = $this->PaypalTip_Refund($tips_transaction_id,$order_id,$tips_entityid,$refund_reason);
                    $resp_data['tips_paymentIntentid'] = $resp_datatemp['tips_paymentIntentid'];
                    $resp_data['tips_paymentIntentstatus'] = $resp_datatemp['tips_paymentIntentstatus'];
                    $resp_data['error'] = '';
                    return $resp_data;                    
                }//End
                else
                {
                    try{
                        if($tips_transaction_id!='' && $flag==0)
                        {
                            unset($stripe);                                                        
                            // Include the Stripe PHP bindings library 
                            require_once(APPPATH .'third_party/stripe-php/init.php');
                            $stripe_info = $this->get_payment_method_detail('stripe');
                            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
                            $stripe = new \Stripe\StripeClient($stripe_api_key);

                            $tips_paymentconfirm = $stripe->paymentIntents->retrieve($tips_transaction_id);
                            if($tips_paymentconfirm->id){
                                $tips_paymentIntentstatus = $stripe->refunds->create([
                                    'payment_intent' => $tips_transaction_id,
                                    //'amount' => $order_total * 100
                                ]);
                                $tips_refundstatus = $stripe->refunds->retrieve($tips_paymentIntentstatus->id,[]);
                                $tip_status = ($tips_refundstatus->status=='succeeded') ? 'refunded' : $tips_refundstatus->status;
                                if($tips_entityid){
                                    $this->updateData('tips',array('refunded_amount'=>$tips_refundstatus->amount, 'stripe_refund_id'=>$tips_refundstatus->id, 'refund_status'=>$tip_status),'entity_id',$tips_entityid);
                                    if($refund_reason != '') {
                                        $this->updateData('tips',array('refund_reason'=>$refund_reason),'entity_id',$tips_entityid);
                                    }
                                } else {
                                    $this->updateData('tips',array('refunded_amount'=>$tips_refundstatus->amount, 'stripe_refund_id'=>$tips_refundstatus->id, 'refund_status'=>$tip_status),'order_id',$order_id);
                                    if($refund_reason != '') {
                                        $this->updateData('tips',array('refund_reason'=>$refund_reason),'order_id',$order_id);
                                    }
                                }
                                $resp_data['tips_paymentIntentid'] = $tips_transaction_id;
                                $resp_data['tips_paymentIntentstatus'] = $tip_status;
                            }
                        }
                    }catch (Exception $e){
                        $resp_data = array('error' => $e->getMessage());
                        return $resp_data;
                    }
                    $resp_data['error'] = '';
                    return $resp_data;
                }
            }
        }
        $resp_data['error'] = '';
        return $resp_data;        
    }
    //Refund paypal method :: End
    //Code for Stripe Partial Refund with update order :: Start
    public function Stripe_PartialRefund($transaction_id='',$order_id,$refunded_amount=0,$new_refunded_amount=0,$refund_reason='',$payment_optionval='stripe')
    {
        if($refunded_amount>0 & $transaction_id!='')
        {
            unset($stripe);
            $resp_data = array('error' => 'yes');
            $flag = ($transaction_id!='' && $tips_transaction_id!='' && $transaction_id==$tips_transaction_id)?1:0;
            // Include the Stripe PHP bindings library 
            require_once(APPPATH .'third_party/stripe-php/init.php');
            $stripe_info = $this->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            try{
                $paymentconfirm = $stripe->paymentIntents->retrieve($transaction_id);
                if($paymentconfirm->id)
                {
                    $paymentIntentstatus = $stripe->refunds->create([
                        'payment_intent' => $transaction_id,
                        'amount' => $refunded_amount * 100
                    ]);
                    $refundstatus = $stripe->refunds->retrieve($paymentIntentstatus->id,[]);

                    //Code for update the order master table with refund value
                    $order_refunded_amount = ($new_refunded_amount>0)?$new_refunded_amount:$refunded_amount;
                    $status = ($refundstatus->status=='succeeded') ? 'partial refunded' : $refundstatus->status;
                    /*$this->updateData('order_master',array('refunded_amount'=>$order_refunded_amount, 'stripe_refund_id'=>$refundstatus->id, 'refund_status'=>$status),'entity_id',$order_id);*/
                    //Change th query if require change with abobe query
                    $this->updateData('order_master',array('stripe_refund_id'=>$refundstatus->id, 'refund_status'=>$status),'entity_id',$order_id);

                    //Code for data insert in refund log :: Start
                    $refund_log_array = array(
                        'order_id'=>$order_id,
                        'transaction_id'=>$refundstatus->id,
                        'payment_option'=>$payment_optionval,
                        'refund_amount'=>$refunded_amount,
                        'refund_reason'=>$refund_reason,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );
                    $this->db->insert("partial_refund_log",$refund_log_array);
                    //Code for data insert in refund log :: End

                    $resp_data = array(
                        'paymentIntentid' => $refundstatus->id,
                        'paymentIntentstatus' => $status,
                        'error' => ''
                    );
                }
                return $resp_data;
            }
            catch (Exception $e)
            {
                $resp_data = array('error' => 'yes','error_message' => $e->getMessage());                
                return $resp_data;
            }
        }
    }
    //Code for Stripe Partial Refund with update order :: End
    //Code for Paypal Partial Refund with update order :: Start
    public function Paypal_PartialRefund($transaction_id='',$order_id,$refunded_amount=0,$new_refunded_amount=0,$refund_reason='')
    {
        if($refunded_amount>0 & $transaction_id!='')
        {
            $paypal_detail = $this->get_payment_method_detail('paypal');
            if($paypal_detail->enable_live_mode){
                $paypal_client_id = $paypal_detail->live_client_id;
                $paypal_client_secret = $paypal_detail->live_client_secret;
                $paypal_url_v1 = LIVE_PAYPAL_URL_V1;
                $paypal_url_v2 = LIVE_PAYPAL_URL_V2;
            }else{
                $paypal_client_id = $paypal_detail->sandbox_client_id;
                $paypal_client_secret = $paypal_detail->sandbox_client_secret;
                $paypal_url_v1 = SANDBOX_PAYPAL_URL_V1;
                $paypal_url_v2 = SANDBOX_PAYPAL_URL_V2;
            }

            $basic_token = base64_encode($paypal_client_id.":".$paypal_client_secret);
            $headers = array (
                'Authorization: Basic '. $basic_token,
                'Content-Type: application/x-www-form-urlencoded'
            );
            #Send Reponse To FireBase Server
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $paypal_url_v1."oauth2/token" );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $resulttoken = curl_exec($ch);
            curl_close($ch);
            $resulttkn_arr = json_decode($resulttoken,true);

            if((strpos($resulttoken,"400") || strpos($resulttoken,"Bad Request")) && $resulttkn_arr['access_token']!=''){
                $resp_data = array('error' => 'error');                
                return $resp_data;
            }else{    
                $resulttkn_arr = json_decode($resulttoken,true);
            }

            if($resulttkn_arr['error']=='error')
            {
                $resp_data['error'] = "yes";
                $resp_data['error_message'] = $this->lang->line('intiate_stripe_refunderror');
                return $resp_data;
            }
            else
            {
                $access_token = $resulttkn_arr['access_token'];
                $ch=curl_init();
                $headers=array('Content-Type: application/json','Authorization: Bearer '.$access_token);           

                /*$fileds['amount'] = array('value'=>round($refunded_amount,2),'currency_code'=>'USD');*/
                $fileds['amount'] = array('total'=>round($refunded_amount,2),'currency'=>'USD');
                $filedsjson = json_encode($fileds);                

                curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
                $refund_url=$paypal_url_v1."payments/sale/".$transaction_id."/refund";
                curl_setopt($ch, CURLOPT_URL, $refund_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $filedsjson);
                $result = curl_exec($ch);
                $result_array=json_decode($result,true);
                curl_close($ch);
                
                if(strtolower($result_array['state'])=='completed')
                {
                    $status = 'partial refunded'; 
                    $order_refunded_amount = ($new_refunded_amount>0)?$new_refunded_amount:$refunded_amount;                   
                    /*$this->updateData('order_master',array('refunded_amount'=>$order_refunded_amount, 'stripe_refund_id'=>$result_array['id'], 'refund_status'=>$status),'entity_id',$order_id);*/
                    //Change th query if require change with abobe query
                    $this->updateData('order_master',array('stripe_refund_id'=>$result_array['id'],'refund_status'=>$status),'entity_id',$order_id);

                    //Code for data insert in refund log :: Start
                    $refund_log_array = array(
                        'order_id'=>$order_id,
                        'transaction_id'=>$result_array['id'],
                        'payment_option'=>'paypal',
                        'refund_amount'=>$refunded_amount,
                        'refund_reason'=>$refund_reason,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );
                    $this->db->insert("partial_refund_log",$refund_log_array);
                    //Code for data insert in refund log :: End
            
                    $resp_data = array(
                        'paymentIntentid' => $result_array['id'],
                        'paymentIntentstatus' => $status,
                        'error' => ''
                    );
                    return $resp_data; 
                }
                else
                {
                    $resp_data['error'] = "error";
                    $resp_data['error_message'] = $result_array['message'];
                    return $resp_data; 
                }
            }            
        }        
    }
    //Code for Paypal Partial Refund with update order :: End
    //get details for refund amount :: start
    public function getOrderTransactionIds($order_id)
    {
        $this->db->select("order_master.payment_option,order_master.transaction_id,order_master.refund_status,order_master.payment_option,order_master.delivery_method,order_master.user_id,tips.tips_transaction_id,tips.refund_status as tips_refund_status,order_master.restaurant_id, tips.payment_option as tip_payment_option");
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $result = $this->db->get('order_master')->first_row();
    }
    //get details for refund amount :: end
    public function getDeliveryMethodName($method_ids)
    {
        $this->db->select('delivery_method_slug');
        $this->db->where_in('delivery_method_id', $method_ids);
        $this->db->where('status',1);
        $return = $this->db->get('delivery_method')->result();
        return array_column($return, 'delivery_method_slug');
    }
    public function getInternalDriverId($order_id)
    {
        $this->db->select('driver_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('is_accept',1);
        $return = $this->db->get('order_driver_map')->first_row();
        return $return->driver_id;
    }
    public function checkDriverTipPaid($order_id)
    {
        $this->db->select("tips.amount");
        $this->db->where('tips.amount >',0);
        //$this->db->where('(tips.refund_status != "refunded" OR tips.refund_status is NULL)');
        $this->db->where('tips.order_id',$order_id);
        $this->db->limit(1);
        $this->db->order_by('tips.entity_id','desc');
        $result = $this->db->get('tips')->first_row();
        $tip_amount = (float)$result->amount;
        return ($tip_amount>0)?1:0;
    }
    public function getOrderWithTransactionId($transaction_id,$call_from = '')
    {
        $this->db->select('entity_id as order_id');
        $this->db->where('transaction_id',$transaction_id);
        if($call_from == ''){
            $this->db->where('(order_master.refund_status != "refunded" OR order_master.refund_status is NULL)');
        }
        $result = $this->db->get('order_master')->first_row();
        return ($result->order_id)?$result->order_id:NULL;
    }
    public function getTipWithTransactionId($transaction_id,$call_from = '')
    {
        $this->db->select("tips.order_id");
        $this->db->where('tips.amount >',0);
        if($call_from == ''){
            $this->db->where('(tips.refund_status != "refunded" OR tips.refund_status is NULL)');
        }
        $this->db->where('tips.tips_transaction_id',$transaction_id);
        $this->db->limit(1);
        $this->db->order_by('tips.entity_id','desc');
        $result = $this->db->get('tips')->first_row();
        return ($result->order_id)?$result->order_id:NULL;
    }
    public function checkUserNotiAlreadySent($order_id,$notification_slug)
    {
        $this->db->select("order_id");
        $this->db->where('order_id',$order_id);
        $this->db->where('notification_slug',$notification_slug);
        $this->db->limit(1);
        $this->db->order_by('user_notification_id ','desc');
        $result = $this->db->get('user_order_notification')->first_row();
        return ($result->order_id)?$result->order_id:false;
    }
    public function sendRefundNoti($order_id, $user_id, $restaurant_id, $transaction_id='', $tips_transaction_id='', $order_refund_status='', $tip_refund_status='', $refund_resp_error='')
    {
        //get order refund notification slug
        $order_refund_noti_slug = '';
        $order_noti_message = '';
        if($refund_resp_error == '' && $order_refund_status == 'refunded') {
            $order_refund_noti_slug = 'order_initiated';
            $order_noti_message = sprintf($this->lang->line('refund_initiated_noti'),$order_id);
        } else if($order_refund_status == 'failed'){
            $order_refund_noti_slug = 'order_refund_failed';
            $order_noti_message = ($transaction_id)?sprintf($this->lang->line($order_refund_noti_slug),$order_id,$transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($order_refund_noti_slug)),$order_id);
            //$order_noti_message = sprintf($this->lang->line($order_refund_noti_slug),$order_id,$transaction_id);
        } else if($order_refund_status == 'canceled') {
            $order_refund_noti_slug = 'order_refund_canceled';
            $order_noti_message = ($transaction_id)?sprintf($this->lang->line($order_refund_noti_slug),$order_id,$transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($order_refund_noti_slug)),$order_id);
        } else if($order_refund_status == 'pending') {
            $order_refund_noti_slug = 'order_refund_pending';
            $order_noti_message = ($transaction_id)?sprintf($this->lang->line($order_refund_noti_slug),$order_id,$transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($order_refund_noti_slug)),$order_id);
        }

        //get driver tip refund notification slug
        $tip_refund_noti_slug = '';
        $tip_noti_message = '';
        if($transaction_id != $tips_transaction_id && $refund_resp_error == '' && $tip_refund_status == 'refunded') {
            $tip_refund_noti_slug = 'tip_refund_initiated';
            $tip_noti_message = sprintf($this->lang->line($tip_refund_noti_slug),$order_id);
        } else if($transaction_id != $tips_transaction_id && $tip_refund_status == 'failed'){
            $tip_refund_noti_slug = 'tip_refund_failed';
            $tip_noti_message = ($tips_transaction_id)?sprintf($this->lang->line($tip_refund_noti_slug),$order_id,$tips_transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($tip_refund_noti_slug)),$order_id);
            //$tip_noti_message = sprintf($this->lang->line($tip_refund_noti_slug),$order_id,$tips_transaction_id);
        } else if($transaction_id != $tips_transaction_id && $tip_refund_status == 'canceled') {
            $tip_refund_noti_slug = 'tip_refund_canceled';
            $tip_noti_message = ($tips_transaction_id)?sprintf($this->lang->line($tip_refund_noti_slug),$order_id,$tips_transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($tip_refund_noti_slug)),$order_id);
        } else if($transaction_id != $tips_transaction_id && $tip_refund_status == 'pending') {
            $tip_refund_noti_slug = 'tip_refund_pending';
            $tip_noti_message = ($tips_transaction_id)?sprintf($this->lang->line($tip_refund_noti_slug),$order_id,$tips_transaction_id):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($tip_refund_noti_slug)),$order_id);
        }
        $orderdetail = $this->getSingleRow('order_master','entity_id',$order_id);
        if($user_id && $user_id > 0) {
            $userdetail = $this->getSingleRow('users','entity_id',$user_id);
            //send order refund notifications
            if($order_refund_noti_slug != ''){
                //website notification
                $refund_notification = array(
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'transaction_id' => ($transaction_id)?$transaction_id:NULL,
                    'notification_slug' => $order_refund_noti_slug,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->addData('user_order_notification',$refund_notification);
                //app notification
                if($userdetail->device_id && $userdetail->notification == 1 && $order_noti_message){
                    $device_id = $userdetail->device_id;
                    $this->sendFCMRegistration($device_id, $order_noti_message, $order_refund_noti_slug, $restaurant_id, FCM_KEY,'','',$order_id,'','','');
                }
            }
            //send tip refund notifications
            if($tip_refund_noti_slug != ''){
                //website notification
                $refund_notification = array(
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'transaction_id' => ($tips_transaction_id)?$tips_transaction_id:NULL,
                    'notification_slug' => $tip_refund_noti_slug,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->addData('user_order_notification',$refund_notification);
                //app notification
                if($userdetail->device_id && $userdetail->notification == 1 && $tip_noti_message){
                    $device_id = $userdetail->device_id;
                    $this->sendFCMRegistration($device_id, $tip_noti_message, $tip_refund_noti_slug, $restaurant_id, FCM_KEY,'','',$order_id,'','','');
                }
            }
        }
        //send order refund notifications to agent - website
        if($orderdetail->agent_id && $orderdetail->agent_id > 0 && $order_refund_noti_slug != '') {
            $agent_refund_notification = array(
                'order_id' => $order_id,
                'agent_id' => $orderdetail->agent_id,
                'transaction_id' => ($transaction_id)?$transaction_id:NULL,
                'notification_slug' => $order_refund_noti_slug,
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->addData('agent_order_notification',$agent_refund_notification);
        }
        //send tip refund notifications to agent - website
        if($orderdetail->agent_id && $orderdetail->agent_id > 0 && $tip_refund_noti_slug != '') {
            $agent_tiprefund_notification = array(
                'order_id' => $order_id,
                'agent_id' => $orderdetail->agent_id,
                'transaction_id' => ($tips_transaction_id)?$tips_transaction_id:NULL,
                'notification_slug' => $tip_refund_noti_slug,
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->addData('agent_order_notification',$agent_tiprefund_notification);
        }
    }
    public function sendSMSandEmailToUserOnCancelOrder($language_slug,$user_id,$order_id,$canceled_by,$call_from='')
    {
        if($order_id) {
            $default_lang = $this->getdefaultlang();
            if($user_id && $user_id > 0) {
                $userdetail = $this->getSingleRow('users','entity_id',$user_id);
                $username = $userdetail->first_name.' '.$userdetail->last_name;
                $to_email = ($userdetail->email)?trim($userdetail->email):'';
                $to_mobileno = ($userdetail->phone_code)?'+'.$userdetail->phone_code:'+1';
                $to_mobileno = $to_mobileno.$userdetail->mobile_number;
                $language_slug = ($language_slug) ? $language_slug : (($userdetail->language_slug) ? $userdetail->language_slug : $default_lang->language_slug);
            } else {
                $orderdetail = $this->getSingleRow('order_detail','order_id',$order_id);
                $userdetail = unserialize($orderdetail->user_detail);
                $username = $userdetail['first_name'].' '.$userdetail['last_name'];
                $to_email = ($userdetail['email'])?trim($userdetail['email']):'';
                $to_mobileno = ($userdetail['phone_code'])?'+'.$userdetail['phone_code']:'+1';
                $to_mobileno = $to_mobileno.$userdetail['mobile_number'];
                $language_slug = ($language_slug) ? $language_slug : $default_lang->language_slug;
            }

            if($canceled_by == '' || $canceled_by == 'MasterAdmin' || $canceled_by == 'Admin' || $canceled_by == 'Restaurant Admin' || $canceled_by == 'Branch Admin') {
                $canceled_by_txt = $this->lang->line('by_admin');   
            } else if($canceled_by == 'Driver') {
                $canceled_by_txt = $this->lang->line('by_driver');
            } else if($canceled_by == 'User') {
                $canceled_by_txt = $this->lang->line('by_driver');
            } else if($canceled_by == 'auto_cancelled') {
                $canceled_by_txt = $this->lang->line('auto_cancelled_by_txt');
            }

            //send email
            if($to_email != ''){
                $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-cancelled','language_slug'=>$language_slug,'status'=>1))->first_row();
                if($canceled_by == 'auto_cancelled') {
                    $cancelled_order_msg = sprintf($this->lang->line('order_autocancelled_notimsg'),$order_id);
                } else {
                    $cancelled_order_msg = sprintf($this->lang->line('cancelled_order_email_text'),$order_id,$canceled_by_txt);
                }
                $arrayData = array('FirstName'=>$username,'order_id'=>$order_id, 'canceled_by'=>$canceled_by_txt, 'cancelled_order_text' => $cancelled_order_msg);
                $EmailBody = generateEmailBody($email_template->message,$arrayData);
                //get System Option Data
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                if(!empty($EmailBody)) {
                    $this->load->library('email');  
                    $config['charset'] = "utf-8";
                    $config['mailtype'] = "html";
                    $config['newline'] = "\r\n";      
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to($to_email);
                    $this->email->subject($email_template->subject);  
                    $this->email->message($EmailBody);
                    $this->email->send();
                }
            }
            //send SMS
            if($to_mobileno != '' && $to_mobileno != '+1') {
                if($canceled_by == 'auto_cancelled') {
                    $sms_txt = sprintf($this->lang->line('order_autocancelled_notimsg'),$order_id)."\n \n".$this->lang->line('best_regards')."\n".$this->lang->line('team_sitename');
                } else {
                    $sms_txt = sprintf($this->lang->line('cancel_order_sms_text'),$order_id,$canceled_by)."\n \n".$this->lang->line('best_regards')."\n".$this->lang->line('team_sitename');
                }
                $sms_data = $this->sendSmsApi($to_mobileno,$sms_txt);
            }
            //send notification to admin app 
            //to branch admin            
            $order_master_detail = $this->getSingleRow('order_master','entity_id',$order_id);
            $restuser_device = $this->getBranchAdminDevice($order_master_detail->restaurant_id);
            if($restuser_device && trim($restuser_device->device_id)!='' && $restuser_device->notification == 1 && $restuser_device->status == 1 && $call_from=='')
            {
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$restuser_device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                #prep the bundle
                $fields = array();
                $message = sprintf($this->lang->line('order_cancel_noti_to_admin'),$order_id);
                $fields['to'] = $restuser_device->device_id; // only one user to send push notification
                $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                $fields['notification']['title'] = $this->lang->line('admin_app_name');
                $fields['data'] = array ('screenType'=>'order');

                $headers = array (
                    'Authorization: key=' .FCM_KEY,
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
            //to restaurant admin
            $restadmin_device = $this->getRestaurantAdminDevice($order_master_detail->restaurant_id);
            if($restadmin_device && trim($restadmin_device->device_id)!='' && $restadmin_device->notification == 1 && $restadmin_device->status == 1 && $call_from=='') {
                $languages_resadmin = $this->db->select('*')->get_where('languages',array('language_slug'=>$restadmin_device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages_resadmin->language_directory);
                #prep the bundle
                $fields = array();
                $message = sprintf($this->lang->line('order_cancel_noti_to_admin'),$order_id);
                $fields['to'] = $restadmin_device->device_id; // only one user to send push notification
                $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                $fields['notification']['title'] = $this->lang->line('admin_app_name');
                $fields['data'] = array ('screenType'=>'order');

                $headers = array (
                    'Authorization: key=' .FCM_KEY,
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
    public function getOrderSubtotal($order_id='')
    {
        $this->db->select('subtotal,restaurant_id');
        $this->db->where('entity_id', $order_id);
        $return = $this->db->get('order_master')->first_row();
        return ($return)?$return:0;
    }
    public function roundDriverTip($tip_amt=0) {
        $round_figure = 0;
        $tip_amt = round($tip_amt,2);
        $comp_num1 = (int)$tip_amt; //3
        $diff_num1 = ($tip_amt > $comp_num1) ? $tip_amt - $comp_num1 : $comp_num1 - $tip_amt;
        
        $comp_num2 = $comp_num1+0.5; //3.5
        $diff_num2 = ($tip_amt > $comp_num2) ? $tip_amt - $comp_num2 : $comp_num2 - $tip_amt;

        if($diff_num1 < $diff_num2) {
            $round_figure = floor($tip_amt * 2) / 2;
        }
        if($diff_num2 <= $diff_num1) {
            $round_figure = ceil($tip_amt * 2) / 2;
        }
        return $round_figure;
    }
    public function dateDifference($date_1 , $date_2) {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);

        $interval = date_diff($datetime1, $datetime2);

        $return_arr = array('diff_year' => $interval->format('%y'), 'diff_month' => $interval->format('%m'), 'diff_day' => $interval->format('%a'), 'diff_hr' => $interval->format('%h'), 'diff_min' => $interval->format('%i'));
        return $return_arr;
    }
    public function convertMinutes($action = 'auto_cancel') {
        if($action == 'auto_cancel') {
            $system_opt_time = $this->db->get_where('system_option',array('OptionSlug'=>'auto_cancel_order_timer'))->first_row();
        } else if($action == 'check_delayed'){
            $system_opt_time = $this->db->get_where('system_option',array('OptionSlug'=>'delayed_order_timer'))->first_row();
        }
        $minutes = (int)$system_opt_time->OptionValue;
        $periods = array(
            'year' => 525600,
            'month' => 43800,
            'week' => 10080,
            'day' => 1440,
            'hour' => 60,
            'minute' => 1
        );
        $return_val = array();
        //minutes to years
        $year = floor ($minutes / $periods['year']);
        $return_val['compare_year'] = $year;
        //minutes to months
        $month = floor ($minutes / $periods['month']);
        $return_val['compare_month'] = $month;
        //minutes to weeks
        $week = floor ($minutes / $periods['week']);
        $return_val['compare_week'] = $week;
        //minutes to days
        $day = floor ($minutes / $periods['day']);
        $return_val['compare_day'] = $day;
        //minutes to hours
        $hour = floor ($minutes / $periods['hour']);
        $return_val['compare_hour'] = $hour;
        //minutes to minutes
        $mins = floor ($minutes / $periods['minute']);
        $return_val['compare_minute'] = $mins;

        return $return_val;
    }
    public function getRestaurantTimings($restaurant_id, $scheduled_date = '', $scheduled_time = '', $user_timezone = '') {
        $this->db->select('restaurant.timings,restaurant.enable_hours,restaurant.allow_scheduled_delivery');
        $this->db->where('entity_id', $restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        if($scheduled_date) {
            //get time interval from system options
            $this->db->select('OptionValue');
            $this->db->where('OptionSlug','time_interval_for_scheduling');
            $time_interval_for_scheduling = $this->db->get('system_option')->first_row();
            $time_interval_for_scheduling = (int)$time_interval_for_scheduling->OptionValue;
            $half_interval = ceil($time_interval_for_scheduling / 2);
        }
        $return = 'not_available';
        if(!empty($result)) {
            $timing = $result->timings;
            if($timing){
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                if($scheduled_date){
                    $day = date('l',strtotime($scheduled_date));
                    $date_check = date('Y-m-d',strtotime($scheduled_date));
                } else {
                    $day = date("l");
                    $date_check = date('Y-m-d');
                }
                foreach($timing as $keys=>$values) {
                    if($keys == strtolower($day)) {
                        $close = 'Closed';
                        if($result->enable_hours=='1') {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->getZonebaseTime($values['open'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->getZonebaseTime($values['close'],$user_timezone):'';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                            $close = 'Closed';
                            if (!empty($values['open']) && !empty($values['close'])) {
                                if($scheduled_date) {
                                    $slot_open_time = ($scheduled_time) ? date('H:i', strtotime($scheduled_time)) : '';
                                    if($slot_open_time) {
                                        $slottime = date_create($slot_open_time);
                                        date_add($slottime,date_interval_create_from_date_string($half_interval." minutes"));
                                        $slottime = date_format($slottime,"H:i");
                                    } else {
                                        $slottime = '';
                                    }
                                    $close = $this->Checkopenclose($this->getZonebaseTime($values['open'],$user_timezone),$this->getZonebaseTime($values['close'],$user_timezone),$user_timezone,$slottime);
                                } else {
                                    $close = $this->Checkopenclose($this->getZonebaseTime($values['open'],$user_timezone),$this->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                                }
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
            $result->timings = $newTimingArr[strtolower($day)];

            if($date_check == date('Y-m-d') && $result->timings['closing'] == 'Closed' && $result->timings['off'] != 'close') {
                $scheduled_time = ($scheduled_time) ? date('H:i', strtotime($scheduled_time)) : date('H:i');
                $date1 = strtotime(date('H:i', strtotime($this->getZonebaseTime($scheduled_time,$user_timezone))));
                $date2 = strtotime(date('H:i', strtotime($result->timings['open'])));
                $date3 = strtotime(date('H:i', strtotime($result->timings['close'])));

                if($date2 > $date1 || $date3 > $date1) {
                    $return = $result->timings;
                } else {
                    $return = 'not_available';
                }
            } else if($result->timings['off'] == 'close') {
                $return = 'not_available';
            } else {
                return $result->timings;
            }
            if($scheduled_date && $result->allow_scheduled_delivery == 0) {
                $return = 'not_available';
            }
        }
        return $return;
    }
    public function getDateAndTimeSlotsForScheduling($restaurant_id, $scheduled_date = '', $slot_open_time = '', $user_timezone = '', $call_from = 'web', $is_out_of_stock_item_in_cart = false) {
        $return = array();
        $this->db->select('restaurant.allow_scheduled_delivery, restaurant.allowed_days_for_scheduling');
        $this->db->where('entity_id', $restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        if($result && !empty($result))
        {
            $result->allowed_days_for_scheduling = (int)$result->allowed_days_for_scheduling;
            

            $allowed_dates = array();
            $timeslotfordates = array();
            if($result->allow_scheduled_delivery == 1) {
                //create dates for scheduling based on allowed days for scheduling
                $push_arr = array();
                if($scheduled_date) {
                    $date = date('Y-m-d',strtotime($scheduled_date));
                    $time = ($slot_open_time) ? date('H:i:s',strtotime($slot_open_time)) : '';
                    $push_arr = array('date' => $date, 'time' => $time);
                    array_push($allowed_dates, $push_arr);
                } else if($result->allowed_days_for_scheduling > 1) {
                    for ($i = 0; $i < $result->allowed_days_for_scheduling; $i++) { 
                        $push_arr = array();
                        if($i == 0) {
                            if(!$is_out_of_stock_item_in_cart) {
                                $date = date( "Y-m-d");
                                $time = date('H:i:s');
                                $push_arr = array('date' => $date, 'time' => $time);
                            }
                        } else {
                            $date = date( "Y-m-d", strtotime( "+".$i." days" ) );
                            $push_arr = array('date' => $date, 'time' => '');
                        }
                        if(!empty($push_arr)) {
                            array_push($allowed_dates, $push_arr);
                        }
                    }
                } else if($result->allowed_days_for_scheduling = 1) {
                    for ($i = 0; $i <= $result->allowed_days_for_scheduling; $i++) { 
                        $push_arr = array();
                        if($i == 0) {
                            if(!$is_out_of_stock_item_in_cart) {
                                $date = date( "Y-m-d");
                                $time = date('H:i:s');
                                $push_arr = array('date' => $date, 'time' => $time);
                            }
                        } else {
                            $date = date( "Y-m-d", strtotime( "+".$i." days" ) );
                            $push_arr = array('date' => $date, 'time' => '');
                        }
                        if(!empty($push_arr)) {
                            array_push($allowed_dates, $push_arr);
                        }
                    }
                }
                //get time interval from system options
                $this->db->select('OptionValue');
                $this->db->where('OptionSlug','time_interval_for_scheduling');
                $time_interval_for_scheduling = $this->db->get('system_option')->first_row();
                $time_interval_for_scheduling = (int)$time_interval_for_scheduling->OptionValue;
                //create time intervals for allowed dates
                if(!empty($allowed_dates)) {
                    foreach ($allowed_dates as $aldates_key => $aldates_value) {
                        $check_date = $aldates_value['date'];
                        $checktime_val = ($aldates_value['time'])?$aldates_value['time']:'07:00';
                        $check_time = ($aldates_value['time'])?$aldates_value['time']:NULL;
                        $combinedDT = ($checktime_val) ? date('Y-m-d H:i:s', strtotime("$check_date $checktime_val")) : date('Y-m-d', strtotime($check_date));
                        $check_date = ($check_date && $checktime_val) ? date('Y-m-d', strtotime($this->getZonebaseDate($combinedDT,$user_timezone))) : date('Y-m-d', strtotime($combinedDT));
                        $check_time = ($check_time) ? date('H:i:s', strtotime($this->getZonebaseDate($combinedDT,$user_timezone))) : '';
                        $res_timing = $this->getRestaurantTimings($restaurant_id,$check_date,$check_time,$user_timezone);
                        if($res_timing != 'not_available') {
                            $timeslot = $this->getTimeSlotsForScheduling($time_interval_for_scheduling, $res_timing['open'], $res_timing['close'], $check_date, $check_time, $user_timezone, $restaurant_id);
                            if(!empty($timeslot)) {
                                $slot_key = date('Y-m-d', strtotime($check_date));
                                $timeslotfordates[$slot_key] = $timeslot;
                                $return = $timeslotfordates;
                            }
                        }
                    }
                }
            }
        }            
        return $return;
    }
    public function getTimeSlotsForScheduling($interval, $start, $end, $date, $time, $user_timezone='', $restaurant_id = '') {
        $start = date('Y-m-d H:i:s', strtotime("$date $start"));
        $end = date('Y-m-d H:i:s', strtotime("$date $end"));

        $discard_startend_time = false; //and consider start again and end again time
        $date_check = date('Y-m-d', strtotime($date));
        $current_datetime_val = date('Y-m-d H:i:s');
        $current_datetime_val = date('Y-m-d H:i:s', strtotime($this->getZonebaseDate($current_datetime_val,$user_timezone)));

        $start = new DateTime($start);
        $end = new DateTime($end);
        $slot_date = $start->format('Y-m-d');
        $date_in_loop = $slot_date;
        $start_time = $start->format('Y-m-d H:i'); // Get time Format in Hour and minutes
        $end_time = $end->format('Y-m-d H:i');

        //check if open and close time is in between 2 dates
        $starttimeval = date('h:i a', strtotime($start_time));
        $endtimeval = date('h:i a', strtotime($end_time));
        $date2 = DateTime::createFromFormat('H:i a', $starttimeval)->getTimestamp();
        $date3 = DateTime::createFromFormat('H:i a', $endtimeval)->getTimestamp(); 
        if ($date3 <= $date2) {
            $date3 += 24 * 3600;
            $end_time = new \DateTime($current_datetime_val);
            $end_time->setTimestamp($date3);
            $end_time->modify("+1 days");
            $end_time = $end_time->format('Y-m-d H:i');

            $this->db->select('restaurant.timings');
            $this->db->where('entity_id', $restaurant_id);
            $result = $this->db->get('restaurant')->first_row();
            $restimings = unserialize(html_entity_decode($result->timings));

            $given_date = new DateTime($start_time);
            $previous_date = $given_date->modify("-1 days")->format('Y-m-d');
            $previous_day = date('l', strtotime($previous_date));
            if(!empty($restimings) && !empty($restimings[strtolower($previous_day)]['open']) && !empty($restimings[strtolower($previous_day)]['close'])) {
                $previous_day_opentime = (!empty($restimings[strtolower($previous_day)]['open']))?$this->getZonebaseTime($restimings[strtolower($previous_day)]['open'],$user_timezone):'';
                $previous_day_closetime = (!empty($restimings[strtolower($previous_day)]['close']))?$this->getZonebaseTime($restimings[strtolower($previous_day)]['close'],$user_timezone):'';

                $previous_day_starttime = date('Y-m-d H:i:s', strtotime("$previous_date $previous_day_opentime"));
                $previous_day_endtime = date('Y-m-d H:i:s', strtotime("$previous_date $previous_day_closetime"));

                $previous_day_starttimeval = date('h:i a', strtotime($previous_day_starttime));
                $previous_day_endtimeval = date('h:i a', strtotime($previous_day_endtime));
                $previous_day_date2 = DateTime::createFromFormat('H:i a', $previous_day_starttimeval)->getTimestamp();
                $previous_day_date3 = DateTime::createFromFormat('H:i a', $previous_day_endtimeval)->getTimestamp();
                if ($previous_day_date3 <= $previous_day_date2) {
                    $previous_day_date3 += 24 * 3600;
                    $previous_day_endtime = new DateTime($date_check);
                    $previous_day_endtime->setTimestamp($previous_day_date3);
                    $previous_day_endtime_format = $previous_day_endtime->format('Y-m-d H:i');

                    if(date('Y-m-d', strtotime($previous_day_endtime_format)) != date('Y-m-d', strtotime($date_check))) {
                        $chkdt = strtotime(date('Y-m-d', strtotime($date_check))); // or your date as well
                        $prevdt = strtotime(date('Y-m-d', strtotime($previous_day_endtime_format)));
                        $datediffval = $chkdt - $prevdt;
                        $datediffval = round($datediffval / (60 * 60 * 24));
                        $datediffval = ($datediffval > 0) ? '+'.$datediffval : $datediffval;
                        $previous_day_endtime->modify($datediffval." days");
                    }
                    $previous_day_endtime = $previous_day_endtime->format('Y-m-d H:i');
                }
                if(date('Y-m-d', strtotime($previous_day_endtime)) == date('Y-m-d',strtotime($start_time))) {
                    //start-end again :: start
                    $start_again = new DateTime($start_time);
                    $endag_date_val = date('Y-m-d',strtotime($start_time));
                    $endag_time_val = date('H:i',strtotime('23:59'));
                    $endag_dt = date('Y-m-d H:i', strtotime("$endag_date_val $endag_time_val"));
                    $end_again = new DateTime($endag_dt);
                    $start_again = $start_again->format('Y-m-d H:i'); // Get time Format in Hour and minutes
                    $end_again = $end_again->format('Y-m-d H:i');
                    //start-end again :: end
                    $startdt_val = date('Y-m-d',strtotime($previous_day_endtime));
                    $starttm_val = date('H:i',strtotime('00:00'));
                    $start_dt = date('Y-m-d H:i', strtotime("$startdt_val $starttm_val"));
                    $start = new DateTime($start_dt);
                    $end = new DateTime($previous_day_endtime);
                    $start_time = $start->format('Y-m-d H:i'); // Get time Format in Hour and minutes
                    $end_time = $end->format('Y-m-d H:i');
                } else {
                    $start = new DateTime($start_time);
                    $enddt_val = date('Y-m-d',strtotime($start_time));
                    $endtm_val = date('H:i',strtotime('23:59'));
                    $end_dt = date('Y-m-d H:i', strtotime("$enddt_val $endtm_val"));
                    $end = new DateTime($end_dt);
                    $start_time = $start->format('Y-m-d H:i'); // Get time Format in Hour and minutes
                    $end_time = $end->format('Y-m-d H:i');
                }
            } else {
                $start = new DateTime($start_time);
                $enddt_val = date('Y-m-d',strtotime($start_time));
                $endtm_val = date('H:i',strtotime('23:59'));
                $end_dt = date('Y-m-d H:i', strtotime("$enddt_val $endtm_val"));
                $end = new DateTime($end_dt);
                $start_time = $start->format('Y-m-d H:i'); // Get time Format in Hour and minutes
                $end_time = $end->format('Y-m-d H:i');
            }
        } 
        //tweak start time for current day
        if($date_check == date('Y-m-d', strtotime($current_datetime_val))) {
            //time slot should not start with past time
            $dateparameter = ($time) ? strtotime(date('H:i', strtotime($time))) : strtotime(date('H:i', strtotime($current_datetime_val))); //current time
            $starttimeparameter = strtotime(date('H:i', strtotime($start_time))); //restaurant open time
            $endtimeparameter = strtotime(date('H:i', strtotime($end_time))); //restaurant close time
            if($dateparameter >= $endtimeparameter) {
                if($start_again && $end_again) {
                    $startagainparameter = strtotime(date('H:i', strtotime($start_again)));
                    $endagainparameter = strtotime(date('H:i', strtotime($end_again)));
                    if($dateparameter >= $endagainparameter) {
                        //disable current date
                        return array();
                    } else if($dateparameter >= $startagainparameter) {
                        $discard_startend_time = true;
                        $startagain = ($time) ? date('H:i', strtotime($time)) : date('H:i', strtotime($current_datetime_val));
                        $startagain = date('Y-m-d H:i:s', strtotime("$date_check $startagain"));
                    }
                } else {
                    return array();
                }
            } else if($dateparameter >= $starttimeparameter) {
                $start = ($time) ? date('H:i', strtotime($time)) : date('H:i', strtotime($current_datetime_val));
                $start = date('Y-m-d H:i:s', strtotime("$date_check $start"));
            } else {
                $start = date('H:i', strtotime($start_time));
                $start = date('Y-m-d H:i:s', strtotime("$date_check $start"));
            }
            //add 1 hour to current time
            if($discard_startend_time) {
                $start_again = new DateTime($startagain);
                $start_again->add(new DateInterval('PT1H'));
                $slot_date = $start_again->format('Y-m-d');
                $date_in_loop = $slot_date;
                $start_again = $start_again->format('Y-m-d H:i'); // Get time Format in Hour and minutes
            } else {
                $start = new DateTime($start);
                $start->add(new DateInterval('PT1H'));
                $slot_date = $start->format('Y-m-d');
                $date_in_loop = $slot_date;
            }
        }
        //minutes in multiple of 5
        $minute = $start->format("i");
        $minute = $minute % 5;
        if($minute != 0) {
            // Count difference
            $diff = 5 - $minute;
            // Add difference
            $start->add(new DateInterval("PT".$diff."M"));
        }
        $start_time = $start->format('Y-m-d H:i'); // Get time Format in Hour and minutes

        if($start_again && $end_again) {
            //minutes in multiple of 5
            $start_again = new DateTime($start_again);
            $start_againminute = $start_again->format("i");
            $start_againminute = $start_againminute % 5;
            if($start_againminute != 0) {
                // Count difference
                $start_againdiff = 5 - $start_againminute;
                // Add difference
                $start_again->add(new DateInterval("PT".$start_againdiff."M"));
            }
            $start_again = $start_again->format('Y-m-d H:i'); // Get time Format in Hour and minutes
        }
        $i=0;
        $time = array();
        if(!$discard_startend_time) {
            while(strtotime($start_time) <= strtotime($end_time) && $slot_date == $date_in_loop) {
                $start = $start_time;
                $end_datetime_inloop = date('Y-m-d H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
                $end = date('Y-m-d H:i',strtotime($end_datetime_inloop));
                $start_time = $end;

                $datetime_in_loop =  new DateTime($end_datetime_inloop);
                $date_in_loop =  $datetime_in_loop->format('Y-m-d');
                if(strtotime($start_time) <= strtotime($end_time) && $slot_date == $date_in_loop) {
                    $time[$i]['start'] = date('H:i',strtotime($start));
                    $time[$i]['end'] = date('H:i',strtotime($end));
                    $i++;
                }
            }
        }
        if($start_again && $end_again) {
            while(strtotime($start_again) <= strtotime($end_again) && $slot_date == $date_in_loop) {
                $start_againval = $start_again;
                $end_datetime_inloop_again = date('Y-m-d H:i',strtotime('+'.$interval.' minutes',strtotime($start_again)));
                $end_againval = date('Y-m-d H:i',strtotime($end_datetime_inloop_again));
                $start_again = $end_againval;

                $datetime_in_loop_again =  new DateTime($end_datetime_inloop_again);
                $date_in_loop =  $datetime_in_loop_again->format('Y-m-d');
                if(strtotime($start_again) <= strtotime($end_again) && $slot_date == $date_in_loop) {
                    $time[$i]['start'] = date('H:i',strtotime($start_againval));
                    $time[$i]['end'] = date('H:i',strtotime($end_againval));
                    $i++;
                }
            }
        }
        return $time;
    }
    /*public function getTimeSlotsForScheduling_old($interval, $start, $end, $date, $time) {
        $date_check = date('Y-m-d', strtotime($date));
        //tweak start time for current day
        if($date_check == date('Y-m-d')) {
            //time slot should not start with past time
            $dateparameter = ($time) ? strtotime(date('H:i', strtotime($time))) : strtotime(date('H:i')); //current time
            $starttimeparameter = strtotime(date('H:i', strtotime($start))); //restaurant open time
            if($dateparameter >= $starttimeparameter) {
                $start = ($time) ? date('H:i', strtotime($time)) : date('H:i', strtotime($this->getZonebaseTime(date('H:i')))) ;
            }
            //add 1 hour to current time
            $start = new DateTime($start);
            $start->add(new DateInterval('PT1H'));
            //minutes in multiple of 5
            $minute = $start->format("i");
            $minute = $minute % 5;
            if($minute != 0) {
                // Count difference
                $diff = 5 - $minute;
                // Add difference
                $start->add(new DateInterval("PT".$diff."M"));
            }
        } else {
            $start = new DateTime($start);
        }
        $end = new DateTime($end);
        $slot_date = $start->format('Y-m-d');
        $date_in_loop = $slot_date;
        $start_time = $start->format('H:i'); // Get time Format in Hour and minutes
        $end_time = $end->format('H:i');
        $i=0;
        $time = array();
        while(strtotime($start_time) <= strtotime($end_time) && $slot_date == $date_in_loop) {
            $start = $start_time;
            $end_datetime_inloop = date('Y-m-d H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
            $end = date('H:i',strtotime($end_datetime_inloop));
            $start_time = $end;

            $datetime_in_loop =  new DateTime($end_datetime_inloop);
            $date_in_loop =  $datetime_in_loop->format('Y-m-d');
            if(strtotime($start_time) <= strtotime($end_time) && $slot_date == $date_in_loop) {
                $time[$i]['start'] = $start;
                $time[$i]['end'] = $end;
            }
            $i++;
        }
        return $time;
    }*/
    //role management :: start
    public function checkAdminAccessforView($role_id) {
        $this->db->select('role_access.parent_access_id');
        $this->db->join('role_access_rights','role_access.access_id = role_access_rights.access_id');
        $this->db->from('role_access');
        $this->db->group_by('role_access.parent_access_id');
        $this->db->order_by('role_access.parent_access_id');
        $this->db->where('role_access_rights.role_id',$role_id);
        $this->db->where('role_access.parent_access_id !=',0);
        $ParentAccessList = $this->db->get()->result();

        $UserAccessRightsArray = array();
        $parentAccessArray = array();
        if(!empty($ParentAccessList)) {
            foreach ($ParentAccessList as $key => $value) {
                $parentAccessArray[] = $value->parent_access_id;
            }
            if(!empty($parentAccessArray)) {
                $this->db->select('controller_slug,access_id');
                $this->db->where_in('access_id',$parentAccessArray);
                $ContorllerID = $this->db->get('role_access')->result();            
            }
            $parentModuleArray = array();
            if(!empty($ContorllerID)) {
                foreach ($ContorllerID as $k => $v) {
                    $parentModuleArray[$k][] = $v->access_id;
                    $parentModuleArray[$k][] = $v->controller_slug;
                }
            }
            foreach($parentModuleArray as $key => $value) {
                $this->db->select('role_access.controller_slug');
                $this->db->join('role_access_rights','role_access.access_id = role_access_rights.access_id');
                $this->db->where(array('role_access_rights.role_id'=>$role_id,'role_access.parent_access_id'=>$value[0]));
                $Modalname = $this->db->get('role_access')->result();
                if(!empty($Modalname)) {
                    foreach ($Modalname as $k => $v) {
                        $UserAccessRightsArray[] = $value[1]."~".$v->controller_slug;
                    }
                } else {
                    $UserAccessRightsArray[] = $value[1];
                }
            }
        }
        return $UserAccessRightsArray;
    }
    public function getRoleName($role_id) {
        $this->db->select('role_name');
        $this->db->where('status',1);
        $this->db->where('role_id',$role_id);
        $this->db->order_by('role_name', 'ASC');
        $return = $this->db->get('role_master')->first_row();
        return ($return->role_name) ? $return->role_name : NULL;
    }
    //role management :: end
    public function save_user_log($action_string,$user_id = '') {
        $user_id = ($user_id) ? $user_id : $this->session->userdata("AdminUserID");
        $user_log_array = array(
            'user_id'=>$user_id,
            'action'=>$action_string,
            'controller'=>$this->router->fetch_class(),
            'function'=>$this->router->fetch_method(),
            'user_ip'=>$this->input->ip_address(),
            'created_date'=>date('Y-m-d H:i:s'),
        );
        $this->db->insert("user_log",$user_log_array);
    }
    //Code for find the payment option :: Start
    public function getPaymnetOption($order_id)
    {
        $this->db->select("payment_option");        
        $this->db->where('entity_id',$order_id);
        return $result = $this->db->get('order_master')->first_row();
    }
    //Code for find the payment option :: End
    public function getResNameWithOrderId($order_id) {
        $this->db->select('restaurant.name');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id');
        $this->db->where('order_master.entity_id',$order_id);
        $return = $this->db->get('order_master')->first_row();
        return ($return->name) ? $return->name : '';
    }
    public function getBranchAdminDevice($restaurant_id) {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res)) {
            $this->db->select('users.device_id, users.language_slug, users.notification, users.status');
            $this->db->join('users','users.entity_id = restaurant_branch_map.branch_admin_id','left');
            $this->db->where('restaurant_branch_map.restaurant_content_id',$result_res->content_id);
            $this->db->order_by('restaurant_branch_map.map_id','desc');
            $result = $this->db->get('restaurant_branch_map')->first_row(); 
            return $result;
        }
        return false;
    }
    public function getRestaurantAdminDevice($restaurant_id) {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res)) {
            $this->db->select('users.device_id, users.language_slug, users.notification, users.status');
            $this->db->join('users','users.entity_id = restaurant.restaurant_owner_id','left');
            $this->db->where('restaurant.entity_id',$result_res->entity_id);            
            $result = $this->db->get('restaurant')->first_row(); 
            return $result;
        }
        return false;
    }
    public function checkUserUseCountCoupon($UserID=0,$coupon_id=0)
    {
        $order_statusarr = array('cancel','rejected');
        $res_array = 0;
        if($coupon_id && $coupon_id>0)
        {
            $this->db->select('order_master.entity_id');            
            $this->db->where('user_id',intval($UserID));
            $this->db->join('order_coupon_use as ouc','ouc.order_id = order_master.entity_id');            
            $this->db->where('ouc.coupon_id',$coupon_id);
            $this->db->where_not_in('order_master.order_status',$order_statusarr);
            $this->db->group_by('order_master.entity_id');
            $res_array =  $this->db->get('order_master')->num_rows();            
        }
        return $res_array;
    }
    public function checkTotalUseCountCoupon($coupon_id=0)
    {
        $order_statusarr = array('cancel','rejected');
        $res_array = 0;
        if($coupon_id && $coupon_id>0)
        {
            $this->db->select('order_master.entity_id');
            $this->db->join('order_coupon_use as ouc','ouc.order_id = order_master.entity_id');
            $this->db->where('ouc.coupon_id',$coupon_id);
            $this->db->where_not_in('order_master.order_status',$order_statusarr);
            $this->db->group_by('order_master.entity_id');
            $res_array =  $this->db->get('order_master')->num_rows();                       
        }
        return $res_array;
    }
    public function getContentId($entity_id,$tblname) {
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        $return = $this->db->get($tblname)->first_row();
        return (!empty($return) && $return->content_id) ? $return->content_id : 0;
    }
    public function getCoupon_array($order_id)
    {
        $this->db->where('order_id',$order_id);        
        $this->db->order_by('coupon_id','ASC');
        return $this->db->get('order_coupon_use')->result_array();  
    }
    public function chkCouponforMUtliple($id_arr)
    {
        $this->db->select('name,entity_id');
        $this->db->where('use_with_other_coupons',0);
        $this->db->where_in('entity_id',$id_arr);
        $res =  $this->db->get('coupon')->first_row();
        return $res;
    }
    //find the scheduled restaurant
    public function getScheduledRestaurant()
    {
        $this->db->select('entity_id, content_id, enable_schedule, schedule_time');
        $this->db->where('enable_schedule',1);
        $this->db->where('schedule_time>',0);
        $restaurant = $this->db->get('restaurant')->result_array();
        return $restaurant;
    }
    public function getSystemOptoin($OptionSlug)
    {        
        $this->db->select('OptionValue');                
        $this->db->where('OptionSlug',$OptionSlug);        
        return $this->db->get('system_option')->first_row();
    }
    //to set a card as default in stripe :: for website
    public function set_default_card($stripe, $payment_method_id = '', $stripe_customer_id = '') {
        $return_arr = array('error' => '');
        if($payment_method_id != '' && $stripe_customer_id != '') {
            try{
                $stripe->customers->update(
                    $stripe_customer_id,
                    ['invoice_settings' => ['default_payment_method' => $payment_method_id]]
                );
                //return 'success';
            } catch (Exception $e) {
                $return_arr = array('error' => $e->getMessage());
            }
        } else {
            //payment method id or customer id missing
        }
        return $return_arr;
    }
    //Code for send the refund mail :: Start
    public function refundMailsend($order_id,$user_id,$partial_refundedamt=0,$refund_full_partial='full',$updated_bytxt,$language_slug='en')
    {
        $this->db->select('email,first_name,last_name,phone_code,mobile_number');                
        $this->db->where('entity_id',$user_id);        
        $res_arr = $this->db->get('users')->first_row();
        if($res_arr && !empty($res_arr))
        {
            //Code for find the email id :: Start
            $user_email_id = ($res_arr->email)?trim($res_arr->email):'';
            $order_username = ($res_arr->first_name)?trim($res_arr->first_name).' '.trim($res_arr->last_name):''; 
            $mobile_numberT = ($res_arr->phone_code)?$res_arr->phone_code:'+1';
            $mobile_numberT = $mobile_numberT.$res_arr->mobile_number; 
            if($user_email_id=='' )
            {
                $this->db->select('user_detail,user_name,user_mobile_number');                
                $this->db->where('order_id',$order_id);        
                $order_resarr = $this->db->get('order_detail')->first_row();
                if($order_resarr && !empty($order_resarr))
                {
                    $order_detailarr = unserialize($order_resarr->user_detail);
                    $user_email_id = ($order_detailarr['email'])?trim($order_detailarr['email']):'';
                    $order_username = ($order_username)?$order_username:$order_resarr->user_name;

                    if($mobile_numberT == '' || $mobile_numberT == '+1') {
                        $mobile_numberT = ($order_resarr->user_mobile_number)?'+'.$order_resarr->user_mobile_number:'';
                    }                    
                }
            }
            //Code for find the email id :: End
            
            //Mail send code start
            if($user_email_id!='')
            {
                if($refund_full_partial=='partial' && $partial_refundedamt>0)
                {
                    $default_currency = get_default_system_currency();
                    $currency_symbol = $default_currency->currency_symbol;
                    $order_refund_text = sprintf($this->lang->line('order_refund_text'),$currency_symbol.$partial_refundedamt);
                }
                else
                {
                    $order_refund_text = sprintf($this->lang->line('order_refund_text'),'');
                }
                
                $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-updated','language_slug'=>$language_slug,'status'=>1))->first_row();
                $arrayData = array('FirstName'=>$order_username,'order_id'=>$order_id, 'updated_by'=>$updated_bytxt, 'order_refund_text'=>$order_refund_text);
                $EmailBody = generateEmailBody($email_template->message,$arrayData);

                //get System Option Data
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                if(!empty($EmailBody))
                {     
                    $this->load->library('email');  
                    $config['charset'] = 'iso-8859-1';  
                    $config['wordwrap'] = TRUE;  
                    $config['mailtype'] = 'html';  
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to($user_email_id); 
                    $this->email->subject($email_template->subject);  
                    $this->email->message($EmailBody);  
                    $this->email->send(); 
                }
            }//End :: SMS send code start
            else
            {   
                if($mobile_numberT != '' && $mobile_numberT != '+1')
                {
                    $sms = 'Your order#'.$order_id.' has been updated by '.$updated_bytxt;
                    if($order_refund_text!='')
                    {
                        $sms = $sms.'. '.$order_refund_text;
                    }
                  $sms_data = $this->sendSmsApi($mobile_numberT,$sms);  
                }
            }//End
        }        
    }
    //Code for send the refund mail :: End
    public function getImages($tblname, $content_id)
    {
        $this->db->select('image');
        $this->db->where('content_id',$content_id);
        $this->db->where('image!=','');
        return $this->db->get($tblname)->result_array();
    }
    public function getZonebaseDateMDY($timevalue,$timezone_name='')
    {       
        if($timezone_name=='')
        {
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }     
            $timezone_name = $_SESSION['timezone_name'];
        }
        if($timezone_name=='')
        {
            $timezone_name = 'UTC';
        }        
        $datetime = new DateTime(date('Y-m-d g:i A',strtotime($timevalue)));
        $Newtimezone = new DateTimeZone($timezone_name);
        $datetime->setTimezone($Newtimezone);
        return $datetime->format('m-d-Y g:i A');
    }
    public function userlogout()
    { 
        $this->session->unset_userdata('UserID');
        $this->session->unset_userdata('userFirstname');
        $this->session->unset_userdata('userLastname');
        $this->session->unset_userdata('userEmail');   
        $this->session->unset_userdata('userPhone');
        $this->session->unset_userdata('userPhone_code');    
        $this->session->unset_userdata('is_user_login'); 
        $this->session->unset_userdata('package_id');
        $this->session->unset_userdata('social_media_id');
        $this->session->unset_userdata('previous_url');
        $this->session->unset_userdata('UserType');
        $this->session->set_userdata('order_mode_frm_dropdown', '');
        $this->session->unset_userdata('guestfirstname');
        $this->session->unset_userdata('guestlastname');
        $this->session->unset_userdata('guestemail');
        $this->session->unset_userdata('guest_mobile_number');
        $this->session->unset_userdata('guestphonecode');
        $this->session->unset_userdata('guest_otp');
        $this->session->set_userdata('guest_otp_verified','0');
        delete_cookie('cart_details');
        delete_cookie('cart_restaurant');          
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");

        redirect(base_url(), 'refresh');
        exit;
    }
    //New notificaiton's topic base code :: Start
    public function pre_push_notification($topic,$notification_arr,$data_arr)
    {
        $this->load->library('push_notification');

        $notification_message['topic']= $topic;
        $notification_message['notification']= $notification_arr;
        $notification_message['data']= $data_arr;

        $this->push_notification->send_push_notification($notification_message);
    }
    //New notificaiton's topic base code :: End
}
?>