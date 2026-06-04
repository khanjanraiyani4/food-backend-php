<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller { 
	function __construct()
    {
        // Construct the parent class
        parent::__construct();
        if($this->uri->segment('4'))
        {
        	$default_lang = $this->common_model->getdefaultlang();
	        $slug = $this->uri->segment('4')?$this->uri->segment('4'):$default_lang->language_slug;
	        $languages = $this->common_model->getFirstLanguages($slug);   
	        $this->session->set_userdata('language_directory',$languages->language_directory);
	        $this->session->set_userdata('language_slug',$slug);
	        $this->config->set_item('language', $languages->language_directory);
	    }
        $this->load->model('user_model');                
        $this->load->library('form_validation');
    }
    // reset users password
    public function reset($verificationCode=NULL)
    {	
	    $data['page_title'] = $this->lang->line('reset_password').' | '.$this->lang->line('site_title');
	    $default_lang = $this->common_model->getdefaultlang();
	    $data['lang'] = ($this->session->userdata('language_slug'))?$this->session->userdata('language_slug'):$default_lang->language_slug;
		if($this->input->post('submit') == "Submit"){
	        $this->form_validation->set_rules('password', $this->lang->line('password'),'trim|required');
	        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_pass'),'trim|required|matches[password]');
	        if($this->form_validation->run())
	        {
	          $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
	          $enc_pass  = md5($salt.$this->input->post('password'));
	          $updatePassword = array(
	              'Password' => $enc_pass ,
				  'email_verification_code' => ''
	          );
	          $Detail = $this->user_model->updatePassword($updatePassword,$this->input->post('verificationCode'));
	          //$this->session->set_flashdata('PasswordChange', $this->lang->line('success_password_change')); 
	          $_SESSION['PasswordChange'] = $this->lang->line('success_password_change');       
	          redirect(base_url()."user/thankYou");
	          exit();
	        }
	    }
		if($verificationCode){
		    $chkverify = $this->user_model->forgotpassowrdVerify($verificationCode); 
			if(!empty($chkverify)){ 
		        $data['verificationCode'] = $verificationCode;
		        $data['page_title'] = $this->lang->line('title_newpassword').' | '.$this->lang->line('site_title');
		        $this->load->view('reset_password',$data); 
		    }else{ 
		        //$this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
		        $_SESSION['verifyerr'] = $this->lang->line('invalid_url_verify');
		        redirect(base_url()."user/thankYou");
	         	exit();
		    }
		}else{
			 //$this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
			 $_SESSION['verifyerr'] = $this->lang->line('invalid_url_verify');
		     $this->load->view('reset_password',$data);
		}
	}
	//verify account
	public function verify_account($verificationCode=NULL)
    {	
    	if($verificationCode){
		    $chkverify = $this->user_model->forgotpassowrdVerify($verificationCode); 
		    if(empty($chkverify)){
		        //$this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
		        $_SESSION['verifyerr'] = $this->lang->line('invalid_url_verify');
		        redirect(base_url().'user/thankYou'); exit;
		    }
		}
        $update = array(
          'active'=>1,
          'email_verification_code'=>''
        );
	    $this->user_model->updatePassword($update,$verificationCode);
	    //$this->session->set_flashdata('activate', $this->lang->line('verify_account')); 
	    $_SESSION['activate'] = $this->lang->line('verify_account');       
	    redirect(base_url()."user/thankYou");
	    exit();
	   
	}
	//cron job for expiry date
	public function expireAccout(){
		$where = date('Y-m-d');
        $this->db->select('entity_id');
        $this->db->where('end_date <= ',$where);
        $arrData =  $this->db->get('coupon')->result();
        if(!empty($arrData)){
        	foreach ($arrData as $key => $value) {
        		$this->db->set('status',0)->where('entity_id',$value->entity_id)->update('coupon');
        	}
        }
	}
	// thank you page
	public function thankYou(){
		$data['page_title'] = $this->lang->line('thank_you').' | '.$this->lang->line('site_title');
		$this->load->view('thank_you',$data); 
	}
}