<?php
class Myprofile_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();      
    }            
    public function getEditUserDetail($UserID)
    {
        return $this->db->get_where('users',array('entity_id'=>$UserID))->first_row();
    }
    public function updateUserModel($UserData,$UserID)
    {        
        $this->db->where('entity_id',$UserID);
        $this->db->update('users',$UserData);            
        return $this->db->affected_rows();
    }
    public function CheckExists($Email,$UserID=NULL)
    {
        $this->db->where('Email',$Email);
        $this->db->where('entity_id !=',$UserID);
        $roles = array('User','Driver','Agent');
        $this->db->where_not_in('user_type',$roles);
        //$this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin' OR user_type='MasterAdmin')");
        return $this->db->get('users')->num_rows();        
    }
    
    public function CheckExist($mobile_number,$phone_code,$UserID=NULL)
    {
        $this->db->where('mobile_number',$mobile_number);
        $this->db->where('phone_code',$phone_code);
        $this->db->where('entity_id !=',$UserID);
        $roles = array('User','Driver','Agent');
        $this->db->where_not_in('user_type',$roles);
        //$this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin' OR user_type='MasterAdmin')");
        return $this->db->get('users')->num_rows();        
    }
}
?>