<?php
class Coupon_model extends CI_Model {
    function __construct()
    {
        parent::__construct();              
    }   
      //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('coupon.name', trim($this->input->post('page_title')));
        }
        if($this->input->post('amount') != ''){
            $total_search = "CONCAT(coupon.amount,'%')";
            $where_totalserch = " (coupon.amount like '%".trim($this->input->post('amount'))."%' OR (".$total_search.") like '%".trim($this->input->post('amount'))."%') ";
            $this->db->where($where_totalserch);
        }
        if($this->input->post('Status') != ''){
            $this->db->like('coupon.status', trim($this->input->post('Status')));
        }
        $this->db->select('coupon.entity_id,coupon.name,coupon.image,coupon.amount,coupon.amount_type,coupon.status,coupon.created_date,restaurant.currency_id');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('coupon.entity_id');
        $result['total'] = $this->db->count_all_results('coupon');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
            $this->db->like('coupon.name', trim($this->input->post('page_title')));
        }
        if($this->input->post('amount') != ''){
            $total_search = "CONCAT(coupon.amount,'%')";
            $where_totalserch = " (coupon.amount like '%".trim($this->input->post('amount'))."%' OR (".$total_search.") like '%".trim($this->input->post('amount'))."%') ";
            $this->db->where($where_totalserch);
        }
        if($this->input->post('Status') != ''){
            $this->db->like('coupon.status', trim($this->input->post('Status')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);     
        $this->db->select('coupon.entity_id,coupon.name,coupon.image,coupon.amount,coupon.amount_type,coupon.status,coupon.created_date,restaurant.currency_id');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('coupon.entity_id');
        $result['data'] = $this->db->get('coupon')->result();       
        return $result;
    }  
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        $this->db->select('c.*');
        $return = $this->db->get_where('coupon as c',array('c.entity_id'=>$entity_id))->first_row();
        if(!empty($return)) {
            $return->start_date = date('Y-m-d H:i', strtotime($this->common_model->getZonebaseDate($return->start_date)));
            $return->end_date = date('Y-m-d H:i', strtotime($this->common_model->getZonebaseDate($return->end_date)));
        }
        return $return;
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatus($tblname,$entity_id,$status){
        if($status==0){
            $userData = array('status' => 1);
        } else {
            $userData = array('status' => 0);
        }        
        $this->db->where('entity_id',$entity_id);
        $this->db->update($tblname,$userData);
        return $this->db->affected_rows();
    }
    // delete user
    public function deleteUser($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
    //get list
    public function getListData($tblname,$where,$language_slug=''){
        $this->db->where($where);
        if($tblname == 'restaurant')
        {
            if($language_slug!='')
            {
                $this->db->where('language_slug', $language_slug);
            }
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->order_by('name', 'ASC');
        }
        return $this->db->get($tblname)->result_array();
    }
    public function checkExist($coupon,$entity_id){
        $this->db->where('name',$coupon);
        $this->db->where('entity_id !=',$entity_id);
        return $this->db->get('coupon')->num_rows();
    }
    //insert batch 
    public function insertBatch($tblname,$data,$id){
        if($id){
            $this->db->where('coupon_id',$id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname,$data);           
        return $this->db->insert_id();
    }
    //get items
    public function getItem($entity_id,$coupon_type){
        $this->db->select('restaurant_menu_item.entity_id,restaurant_menu_item.name,restaurant_menu_item.price,restaurant.name as restaurant_name,restaurant_menu_item.restaurant_id');
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        if(!empty($entity_id)) {
        $this->db->where_in('restaurant_menu_item.restaurant_id',$entity_id);
        }
        $this->db->where('restaurant_menu_item.status',1);
        if($coupon_type == 'discount_on_combo'){
            $this->db->where('restaurant_menu_item.is_deal',1);
        }
        $this->db->order_by('name', 'ASC');
        $result =  $this->db->get('restaurant_menu_item')->result();
        $res = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($res[$value->restaurant_id])){
                    $res[$value->restaurant_id] = array();
                }
                array_push($res[$value->restaurant_id], $value);
            }
        }
        return $res;
    }
    public function getItemedit($entity_id,$coupon_type)
    {
        if(!empty($entity_id))
        {
            $language_slug=$this->session->userdata('language_slug');            
            $entity_idarrtemp = $this->getResEntity_id($entity_id,$language_slug);
            $entity_idarr = array_column($entity_idarrtemp, 'entity_id');        
        }        
        $this->db->select('restaurant_menu_item.entity_id, restaurant_menu_item.content_id, restaurant_menu_item.name, restaurant_menu_item.price, restaurant.name as restaurant_name, restaurant_menu_item.restaurant_id');
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        if(!empty($entity_idarr)) {
        $this->db->where_in('restaurant_menu_item.restaurant_id',$entity_idarr);
        }
        $this->db->where('restaurant_menu_item.status',1);
        if($coupon_type == 'discount_on_combo'){
            $this->db->where('restaurant_menu_item.is_deal',1);
        }
        $this->db->order_by('name', 'ASC');
        $result =  $this->db->get('restaurant_menu_item')->result();        
        $res = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($res[$value->restaurant_id])){
                    $res[$value->restaurant_id] = array();
                }
                array_push($res[$value->restaurant_id], $value);
            }
        }
        return $res;
    }
    public function get_res_menu_items($restaurant_id,$items){
    
        return $this->db->select('entity_id')->where('restaurant_id',$restaurant_id)->where_in('entity_id',$items)->get('restaurant_menu_item')->result();
    }
    public function getResContentId($restaurant_id){
        $this->db->select('content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        return $result->content_id;
    }
    public function getResEntity_id($restaurant_id,$language_slug){
        if(!$language_slug){
            $default_lang = $this->common_model->getdefaultlang();
            $language_slug = $default_lang->language_slug;
        }
        $this->db->select('entity_id');
        if(!empty($restaurant_id))
        {
            $this->db->where_in('content_id',$restaurant_id);
        }
        $this->db->where('language_slug',$language_slug);
        return $result = $this->db->get('restaurant')->result();
        
    }
    public function getResMenuContentId($rest_menu_id){
        $this->db->select('content_id');
        $this->db->where('entity_id',$rest_menu_id);
        $result = $this->db->get('restaurant_menu_item')->first_row();
        return $result->content_id;
    }
    public function get_categories($res_entityarr=array())
    {
        $this->db->select('category.name,category.content_id');
        $this->db->join('restaurant_menu_item','restaurant_menu_item.category_id = category.entity_id');
        if(!empty($res_entityarr))
        {
            $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id');
            $this->db->where_in('restaurant_menu_item.restaurant_id',$res_entityarr);
        }
        $this->db->where('category.status',1);
        $this->db->group_by('category.entity_id');
        $this->db->order_by('category.content_id','desc');
        return $result = $this->db->get('category')->result();
    }
    public function getCouponName($coupon_entity_id = '') {
        $this->db->select('name');
        if ($coupon_entity_id) {
            $this->db->where('entity_id',$coupon_entity_id);
        }
        $return = $this->db->get('coupon')->first_row();
        return ($return->name) ? $return->name : '';
    }
    //New code for fetch the restaurant base on coupon type :: Start
    public function getRestaurantData($show_allrest='yes',$language_slug='')
    {
        $this->db->select('entity_id, name');
        if($show_allrest=='no')
        {
            $where_find_order_mode = "(find_in_set ('Delivery', order_mode))";
            $this->db->where($where_find_order_mode);
        }
        if($language_slug!='')
        {
            $this->db->where('language_slug', $language_slug);
        }
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('restaurant')->result_array();
    }
    //New code for fetch the restaurant base on coupon type :: End
}
?>