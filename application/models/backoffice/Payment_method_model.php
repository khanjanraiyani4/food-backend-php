<?php
class Payment_method_model extends CI_Model
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
        $this->db->select('payment_id,display_name_en,status');
        $result['total'] = $this->db->count_all_results('payment_method');
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
        $this->db->select('payment_id,display_name_en,status');
        $result['data'] = $this->db->get('payment_method')->result();
        return $result;
    }
    //get data for edit
    public function get_edit_detail($payment_id)
    {
        return $this->db->get_where('payment_method', array('payment_id' => $payment_id))->first_row();
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
        $this->db->where('payment_id',$id);
        $this->db->update('payment_method',$data);
        return $this->db->affected_rows();
    }
    //get payment methods code start
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
    public function get_payment_methods(){
        $this->db->where('status',1);
        return $this->db->get('payment_method')->result();
    }
    public function getpaymentmethod($tablenm,$content_id){
        $this->db->select('payment_methods');
        $this->db->where('content_id',$content_id);
        return $this->db->get($tablenm)->result_array();
    }
    public function check_payment_method_suggestion($restaurant_content_id){
        return $this->db->get_where('restaurant_payment_method_suggestion',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }
    public function deleteInsertMethodSuggestion($restaurant_content_id,$data){
        if($restaurant_content_id){
            $this->db->where('restaurant_content_id',$restaurant_content_id);
            $this->db->delete('restaurant_payment_method_suggestion');
        }
        if(!empty($data)){
            $this->db->insert_batch('restaurant_payment_method_suggestion',$data);
            return $this->db->insert_id();
        }
    }
    //ajax view :: restaurant payment methods list
    public function get_res_pay_method_grid_list($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //search restaurant or branch name
        if($this->input->post('res_branch_name') != ''){
            $this->db->like('res.name', trim($this->input->post('res_branch_name')));
        }
        //count query for restaurant - payment method list
        $this->db->select('res.entity_id as restaurant_id,res.content_id as restaurant_content_id,res.name as restaurant_name, GROUP_CONCAT(payment_method.display_name_en ORDER BY payment_method.sorting ASC SEPARATOR ",") as paymethod_name');
        $this->db->join('restaurant_payment_method_suggestion','res.content_id = restaurant_payment_method_suggestion.restaurant_content_id','left');
        $this->db->join('payment_method','restaurant_payment_method_suggestion.payment_id = payment_method.payment_id AND payment_method.status=1','left');

        //search payment method name
        if($this->input->post('payment_method') != ''){
            $pay_method_search = $this->common_model->escapeString(trim($this->input->post('payment_method')));
            $this->db->having("paymethod_name LIKE '%$pay_method_search%' ");
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
        //query for restaurant - payment method list
        $this->db->select('res.entity_id as restaurant_id,res.content_id as restaurant_content_id,res.name as restaurant_name, GROUP_CONCAT(payment_method.display_name_en ORDER BY payment_method.sorting ASC SEPARATOR ",") as paymethod_name');
        $this->db->join('restaurant_payment_method_suggestion','res.content_id = restaurant_payment_method_suggestion.restaurant_content_id','left');
        $this->db->group_by('res.content_id');
        $this->db->join('payment_method','restaurant_payment_method_suggestion.payment_id = payment_method.payment_id AND payment_method.status=1','left');
        //search payment method name
        if($this->input->post('payment_method') != ''){
            $pay_method_search = $this->common_model->escapeString(trim($this->input->post('payment_method')));
            $this->db->having("paymethod_name LIKE '%$pay_method_search%' ");
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
    public function get_payment_method_name($entity_id='') {
        $this->db->select('display_name_en');
        if ($entity_id) {
            $this->db->where('payment_id',$entity_id);
        }
        $return = $this->db->get('payment_method')->first_row();
        return ($return->display_name_en) ? $return->display_name_en : '';
    }
}