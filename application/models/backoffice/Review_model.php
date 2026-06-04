<?php
class Review_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    // method for getting all users
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', trim($this->input->post('Status')));
        }
        if($this->input->post('review') != ''){
            $this->db->like('review', trim($this->input->post('review')));
        }
        if($this->input->post('rating') != ''){
            $this->db->like('rating', trim($this->input->post('rating')));
        }
        if($this->input->post('customer') != ''){
            $where_string="((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%')";
            $this->db->where($where_string);
        }
        $this->db->select('review.entity_id,review.review,review.rating,review.created_date,res.name as rname, u.first_name,u.last_name');
        $this->db->join('restaurant as res','review.restaurant_content_id = res.content_id','left');
        $this->db->join('users as u','review.user_id = u.entity_id','left');
        $this->db->where('review.restaurant_id !=', '');
        $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
        $this->db->where('res.language_slug', $this->session->userdata('language_slug'));  
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('res.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $result['total'] = $this->db->count_all_results('review');
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', trim($this->input->post('Status')));
        }
        if($this->input->post('review') != ''){
            $this->db->like('review', trim($this->input->post('review')));
        }
        if($this->input->post('rating') != ''){
            $this->db->like('rating', trim($this->input->post('rating')));
        }
        if($this->input->post('customer') != ''){
            $where_string="((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%')";
            $this->db->where($where_string);
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart); 
        }
        $this->db->select('review.entity_id,review.review,review.rating,review.created_date,res.name as rname, u.first_name,u.last_name');
        $this->db->join('restaurant as res','review.restaurant_content_id = res.content_id','left');
        $this->db->join('users as u','review.user_id = u.entity_id','left');   
        $this->db->where('review.restaurant_id !=', '');    
        $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)'); 
        $this->db->where('res.language_slug', $this->session->userdata('language_slug'));     
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('res.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $result['data'] = $this->db->get('review')->result();        
        return $result;
    }
    // method for adding users
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
     //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('review',array('entity_id'=>$entity_id))->first_row();
    }
     // updating the changed
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
    // delete
    public function ajaxDelete($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
    public function getRestaurantByReviewId($review_id) {
        $this->db->select('res.name as rname');
        $this->db->join('restaurant as res','review.restaurant_content_id = res.content_id','left');
        $this->db->where('review.entity_id',$review_id);
        $return = $this->db->get('review')->first_row();
        return ($return->rname) ? $return->rname : '' ;
    }
}
?>