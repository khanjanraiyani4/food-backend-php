<?php
class User_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // verify forgot password
    public function forgotpassowrdVerify($verificationCode){
        return $this->db->get_where('users',array('email_verification_code'=>$verificationCode))->first_row();
    }
    //Update password
    public function updatePassword($updatePassword,$verificationCode)
    {
        $this->db->where('email_verification_code',$verificationCode);
        $this->db->update('users',$updatePassword);
        return $this->db->affected_rows();
    }
}