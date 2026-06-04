<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Myprofile extends CI_Controller {    	
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->helper('string');
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/myprofile_model');
    }        
    public function getUserProfile() {
        $data['meta_title'] = $this->lang->line('title_admin_myprofile').' | '.$this->lang->line('site_title');
        if($this->input->post('submitEditUser') == "Submit")
        {   
          $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required');
          $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required');
          $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|callback_checkEmailExist');
          $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|numeric|callback_checkExist');          
          //check form validation using codeigniter
          if ($this->form_validation->run())
          {
              $updateUserData = array(  
                  'first_name'=>$this->input->post('first_name'),
                  'last_name' =>$this->input->post('last_name'),
                  'email' =>$this->input->post('email'),
                  'phone_code' =>$this->input->post('phone_code'),
                  'mobile_number' =>$this->input->post('mobile_number'),
                  'notification_sound' =>$this->input->post('notification_sound'),
                  'status' =>1,
                  'updated_by'=>$this->session->userdata("AdminUserID"),
                  'updated_date'=>date('Y-m-d h:i:s')
              );
                           
              $this->myprofile_model->updateUserModel($updateUserData,$this->input->post('entity_id'));                 
              // $this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update'));
              $_SESSION['myProfileMSG'] = $this->lang->line('success_update');                  
              redirect(base_url().ADMIN_URL."/myprofile/getUserProfile");                  
          }            
        }
        if($this->input->post('ChangePassword') == "Submit")
        {
            $data['selected_tab'] = "ChangePassword";
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|min_length[8]');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_pass'), 'trim|required|min_length[8]|matches[password]');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
              if($this->input->post('password')){
                  $updateUserPassData['password'] = md5(SALT.$this->input->post('password'));
              }
              $this->myprofile_model->updateUserModel($updateUserPassData,$this->input->post('entity_id'));      
              // $this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update'));
              $_SESSION['myProfileMSG'] = $this->lang->line('success_update');
              redirect(base_url().ADMIN_URL."/myprofile/getUserProfile"); 
            }
        }        
        $UserID = ($this->session->userdata("AdminUserID"))?$this->session->userdata("AdminUserID"):$this->input->post('entity_id');                  
        $data['editUserDetail'] = $this->myprofile_model->getEditUserDetail($UserID);
        $this->load->view(ADMIN_URL.'/myprofile_edit',$data);
    }
    public function checkExist(){
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        if($this->input->post('first_name')){
            if($mobile_number != ''){
                $phone_code = $this->input->post('phone_code');
                $check = $this->myprofile_model->checkExist($mobile_number,$phone_code,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('number_already_registered'));
                    return false;
                }
            } 
        }
        else{
            if($mobile_number != ''){
                $phone_code = $this->input->post('phone_code');
                $check = $this->myprofile_model->checkExist($mobile_number,$phone_code,$this->input->post('entity_id'));
                //echo $check;
            } 
        }
    }
    public function checkEmailExist()
    { 
      $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
      if($this->input->post('first_name')){
        if($email != ''){
            $chkEmail = $this->myprofile_model->CheckExists($email,$this->input->post('entity_id'));
            if($chkEmail > 0){
                $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                return false;
            }
        } 
      }
      else{
        if($email != ''){
            $chkEmail = $this->myprofile_model->CheckExists($email,$this->input->post('entity_id'));
            //echo $chkEmail;
        } 
      }
    }
}