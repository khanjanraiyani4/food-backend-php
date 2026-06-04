<?php
class Delivery_method_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();		        
    }
    //ajax view
    public function get_grid_list($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('display_name_en') != ''){
            $this->db->like('display_name_en', trim($this->input->post('display_name_en')));
        }
        if($this->input->post('status') != ''){
            $this->db->like('status', trim($this->input->post('status')));
        }
        $this->db->select('delivery_method_id,display_name_en,status');
        $result['total'] = $this->db->count_all_results('delivery_method');
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        if($this->input->post('display_name_en') != ''){
            $this->db->like('display_name_en', trim($this->input->post('display_name_en')));
        }
        if($this->input->post('status') != ''){
            $this->db->like('status', trim($this->input->post('status')));
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart);     
        }
        $this->db->select('delivery_method_id,display_name_en,status');
        $result['data'] = $this->db->get('delivery_method')->result();
        return $result;
    }
    //get data for edit
    public function get_edit_detail($delivery_method_id)
    {
        return $this->db->get_where('delivery_method', array('delivery_method_id' => $delivery_method_id))->first_row();
    }
    //update data
    public function update_data($data,$tbl_name,$field_name,$id)
    {
        $this->db->where($field_name,$id);
        $this->db->update($tbl_name,$data);
        return $this->db->affected_rows();
    }
    // updating the status
    public function update_status($id,$status){
        if($status == 0){
            $data = array('status' => 1);
        } else {
            $data = array('status' => 0);
        }
        $this->db->where('delivery_method_id',$id);
        $this->db->update('delivery_method',$data);
        return $this->db->affected_rows();
    }
    //get delivery methods code start
    // get content id
    public function getContentId($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    public function get_active_restaurants(){
        $this->db->select('name,entity_id,content_id');
        $this->db->where('status',1);
        $this->db->where('language_slug',$this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('restaurant')->result();
    }
    public function get_delivery_methods(){
        $this->db->where('status',1);
        return $this->db->get('delivery_method')->result();
    }
    public function check_delivery_method_map($restaurant_content_id){
        return $this->db->get_where('restaurant_delivery_method_map',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }
    public function deleteInsertMethodMap($restaurant_content_id,$data){
        if($restaurant_content_id){
            $this->db->where('restaurant_content_id',$restaurant_content_id);
            $this->db->delete('restaurant_delivery_method_map');
        }
        if(!empty($data)){
            $this->db->insert_batch('restaurant_delivery_method_map',$data);
            return $this->db->insert_id();
        }
    }
    //ajax view :: restaurant delivery methods list
    public function get_res_delivery_method_grid_list($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //search restaurant or branch name
        if($this->input->post('res_branch_name') != ''){
            $this->db->like('res.name', trim($this->input->post('res_branch_name')));
        }
        //count query for restaurant - delivery method list
        $this->db->select('res.entity_id as restaurant_id,res.content_id as restaurant_content_id,res.name as restaurant_name, GROUP_CONCAT(delivery_method.display_name_en ORDER BY delivery_method.display_name_en ASC SEPARATOR ",") as deliverymethod_name');
        $this->db->join('restaurant_delivery_method_map','res.content_id = restaurant_delivery_method_map.restaurant_content_id','left');
        $this->db->join('delivery_method','restaurant_delivery_method_map.delivery_method_id = delivery_method.delivery_method_id AND delivery_method.status=1','left');

        //search delivery method name
        if($this->input->post('delivery_method') != ''){
            $delivery_method_search = $this->common_model->escapeString(trim($this->input->post('delivery_method')));
            $this->db->having("deliverymethod_name LIKE '%$delivery_method_search%' ");
        }
        $this->db->where('res.status',1);
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('res.content_id');
        $result['total'] = $this->db->count_all_results('restaurant as res');
        
        //sort field from datatables
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        //pagination from datatables
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart);     
        }
        //search restaurant or branch name
        if($this->input->post('res_branch_name') != ''){
            $this->db->like('res.name', trim($this->input->post('res_branch_name')));
        }
        //query for restaurant - delivery method list
        $this->db->select('res.entity_id as restaurant_id,res.content_id as restaurant_content_id,res.name as restaurant_name, GROUP_CONCAT(delivery_method.display_name_en ORDER BY delivery_method.display_name_en ASC SEPARATOR ",") as deliverymethod_name');
        $this->db->join('restaurant_delivery_method_map','res.content_id = restaurant_delivery_method_map.restaurant_content_id','left');
        $this->db->group_by('res.content_id');
        $this->db->join('delivery_method','restaurant_delivery_method_map.delivery_method_id = delivery_method.delivery_method_id AND delivery_method.status=1','left');
        //search delivery method name
        if($this->input->post('delivery_method') != ''){
            $delivery_method_search = $this->common_model->escapeString(trim($this->input->post('delivery_method')));
            $this->db->having("deliverymethod_name LIKE '%$delivery_method_search%' ");
        }
        $this->db->where('res.status',1);
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $result['data'] = $this->db->get('restaurant as res')->result();
        return $result;
    }
    public function get_delivery_method_name($entity_id='') {
        $this->db->select('display_name_en');
        if ($entity_id) {
            $this->db->where('delivery_method_id',$entity_id);
        }
        $return = $this->db->get('delivery_method')->first_row();
        return ($return->display_name_en) ? $return->display_name_en : '';
    }
}