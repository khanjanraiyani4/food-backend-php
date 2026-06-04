<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Home extends CI_Controller {   
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/home_model');    
	}
	public function index() {
		if ($this->session->userdata('is_admin_login')) {
			redirect(base_url().ADMIN_URL.'/dashboard');
		} else {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug',$data['lang']->language_slug);
			$this->lang->load('messages_lang', $data['lang']->language_directory);
			$this->load->view(ADMIN_URL.'/loginnew',$data);
		}
	}  
	public function do_login() {
		if ($this->session->userdata('is_admin_login')) {     
			redirect(ADMIN_URL.'/dashboard');
		} else {
			if($this->session->userdata('AdminUserID') != "" && $this->session->userdata('is_admin_login'))
			{
				// $this->session->set_flashdata('loginError', $this->lang->line('merchant_login_error_msg'));
				$_SESSION['loginError'] = $this->lang->line('merchant_login_error_msg');
				redirect(base_url().ADMIN_URL.'/home'); exit;                
			}
			else
			{          
				$user = $this->input->post('username');
				$password = $this->input->post('password');
				$this->form_validation->set_rules('username', $this->lang->line('user_name'), 'required');
				$this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
				if ($this->form_validation->run() == FALSE) {
					$this->load->view(ADMIN_URL.'/loginnew');
				} 
				else 
				{
					$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
					$enc_pass  = md5($salt.$password); 
					$roles = array('User','Driver','Agent');
					$this->db->where('email',strtolower($user));
					$this->db->where('password',$enc_pass);
            		$this->db->where_not_in('user_type',$roles);
					//$this->db->where("(user_type='Restaurant Admin' OR user_type='MasterAdmin' OR user_type='Branch Admin')");
					$val = $this->db->get('users')->first_row();  
					if(!empty($val))
					{
						if($val->status!='0' && $val->email==strtolower($user)) 
						{
							$getAdminRoleName = $this->common_model->getRoleName($val->role_id);
							if($getAdminRoleName) {
								//Code for find the master admin id :: Start
								$parent_adminid = $val->entity_id;
								if($val->user_type == 'Branch Admin') {
						            $parent_adminid = '';
						            $parent_adminid = $val->parent_user_id;
					            } else if($val->user_type == 'Restaurant Admin') {
						            $parent_adminid = '';
						            $parent_adminid = $val->parent_user_id;
					            }
		            			//Code for find the master admin id :: End
								$lang = $this->common_model->getdefaultlang();
								$this->session->set_userdata(
									array(
										'AdminUserID' => $val->entity_id,
										'adminFirstname' => $val->first_name,
										'adminLastname' => $val->last_name,
										'adminemail' => $val->email,
										'admincountry' => (isset($val->country)) ? $val->country : '',
										'is_admin_login' => true,
										'AdminUserType' => $val->user_type,
										'AdminRoleId' => $val->role_id,
										'language_slug'=>$lang->language_slug,
										'parent_adminid'=>$parent_adminid,
										'language_directory'=>$lang->language_directory
									)
								);
								$this->session->set_userdata('AdminRoleName', $getAdminRoleName);
								$this->session->set_userdata('UserAccessArray', $this->common_model->checkAdminAccessforView($val->role_id));
								//get restaurant
								$restaurant = $this->common_model->getRestaurantinSession('restaurant',$this->session->userdata('AdminUserID'));
								if(!empty($restaurant)) {
									$restaurant = array_column($restaurant, 'entity_id');
									$this->session->set_userdata('restaurant',$restaurant);
								} else {
									$this->session->set_userdata('restaurant',array());
								}

								// remember ME
								$cookie_name = "adminAuth";
								if($this->input->post('rememberMe')==1){                    
									$this->input->set_cookie($cookie_name, 'usr='.$user.'&hash='.$password, 60*60*24*5); // 5 days
								} else {
									delete_cookie($cookie_name);
								}
								redirect(base_url().ADMIN_URL.'/dashboard');
							} else {
								$data['loginError'] = $this->lang->line('roletype_inactive');
								$this->load->view(ADMIN_URL.'/loginnew', $data);
							}
						} 
						else if($val->status=='0' && $val->email==$user)
						{                
							$data['loginError'] = $this->lang->line('login_deactivate');
							$this->load->view(ADMIN_URL.'/loginnew', $data);
						} 
						else 
						{
							$data['loginError'] = $this->lang->line('login_error');
							$this->load->view(ADMIN_URL.'/loginnew', $data);
						}
					}else{
						$data['loginError'] = $this->lang->line('login_error');
						$this->load->view(ADMIN_URL.'/loginnew', $data);
					}
				}
			}
		}
	}
	public function forgotpassword(){
		// when click submit button
		if($this->input->post('Submit')=="Submit"){  
			$this->form_validation->set_rules('email_address', $this->lang->line('email'), 'trim|required|valid_email');          
			if($this->form_validation->run()){
				$checkEx = $this->home_model->checkemailExist($this->input->post('email_address'));         
				if(!empty($checkEx))
				{
					// confirmation link
					$verificationCode = random_string('alnum', 20).$checkEx->UserID.random_string('alnum', 5);
					$confirmationLink = '<a href="'.base_url().ADMIN_URL.'/home/newpassword/'.$verificationCode.'" style="text-decoration:underline;">here</a>'; 
					$default_lang = $this->common_model->getdefaultlang();
					$email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password','language_slug'=>$default_lang->language_slug))->first_row();        
					$arrayData = array('FirstName'=>$checkEx->first_name,'ForgotPasswordLink'=>$confirmationLink);
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
					$this->email->to($this->input->post('email_address'));      
					$this->email->subject($email_template->subject);  
					$this->email->message($EmailBody);            
					$this->email->send();
					
					// update verification code
					// $this->session->set_flashdata('successflag', "success");
					$_SESSION['successflag'] = "success";
					$this->home_model->updateVerificationCode($this->input->post('email_address'),$verificationCode,$checkEx->entity_id);
					redirect(base_url().ADMIN_URL.'/home/forgotpasswordsent');
					exit();
				}else{
					// $this->session->set_flashdata('emailNotExist', $this->lang->line('email_not_exist'));
					$_SESSION['emailNotExist'] = $this->lang->line('email_not_exist');
					redirect(base_url().ADMIN_URL.'/home/forgotpassword'); 
				}
			}
		}
		$arr['meta_title'] = $this->lang->line('title_merchant_fogottPass').' | '.$this->lang->line('site_title');
		$arr['captchaData'] = '';
		$arr['lang'] = $this->common_model->getdefaultlang();
		$this->session->set_userdata('language_directory',$arr['lang']->language_directory);
		$this->config->set_item('language', $arr['lang']->language_directory);
		$this->load->view(ADMIN_URL.'/forgot_password',$arr);  
	}
	// verify (unique code) when reach from mail
	public function newPassword($verificationCode=NULL,$whichone = NULL) {  
		// when click submit button
		if($this->input->post('submit')=="Submit"){
			$this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|min_length[8]');  
			$this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_pass'), 'trim|required|min_length[8]|matches[password]');   
			$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
			$enc_pass  = md5($salt.$this->input->post('password'));
			if ($this->form_validation->run())
			{
					$changePswData = array(        
					 'password'  => $enc_pass
				 );
				$this->home_model->updateData($changePswData,'users',$this->input->post('verificationCode')); // data,table,where
				// $this->session->set_flashdata('PasswordChange', $this->lang->line('success_password_change'));
				$_SESSION['PasswordChange'] = $this->lang->line('success_password_change');
				redirect(base_url().ADMIN_URL);
				exit();
			}
		}
		$chkverify = $this->home_model->forgotEmailVerify($verificationCode);
		if(!empty($chkverify)){
			$arr['verificationCode'] = $verificationCode;
			$arr['meta_title'] = $this->lang->line('title_newpassword').' | '.$this->lang->line('site_title');
			$arr['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$arr['lang']->language_directory);
			$this->config->set_item('language', $arr['lang']->language_directory);
			$this->load->view(ADMIN_URL.'/reset_password',$arr); 
		}else{
			// $this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
			$_SESSION['verifyerr'] = $this->lang->line('invalid_url_verify');
			redirect(base_url().ADMIN_URL.'/not_found'); 
		}
	}
	public function forgotpasswordsent(){
		$this->load->view(ADMIN_URL.'/forgot_passwordsent'); 
	}  
	public function logout() {
		$this->session->unset_userdata('AdminUserID');
		$this->session->unset_userdata('adminFirstname');
		$this->session->unset_userdata('adminLastname');
		$this->session->unset_userdata('adminemail');
		$this->session->unset_userdata('is_admin_login');  
		$this->session->unset_userdata('AdminUserType');  
    	$this->session->unset_userdata('UserAccessArray');
		$this->session->unset_userdata('parent_adminid');
		$this->session->unset_userdata('admincountry');  
		$this->session->unset_userdata('restaurant'); 
		//$this->session->sess_destroy();
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		redirect(base_url().ADMIN_URL.'/home', 'refresh');
	}
	public function not_found()
	{
		$this->load->view('error_404');
	}
	// verify account
	public function accountactivate($verificationText=NULL) {     
		$noOfRecords = $this->home_model->verifyEmailAddress($verificationText);
		if($noOfRecords > 0){
			// $this->session->set_flashdata('UserVerifySuccess', $this->lang->line('account_activated'));
			$_SESSION['UserVerifySuccess'] = $this->lang->line('account_activated');
			redirect(base_url().ADMIN_URL);
		}else{
			// $this->session->set_flashdata('UserVerifyError', $this->lang->line('invalid_url_verify'));
			$_SESSION['UserVerifyError'] = $this->lang->line('invalid_url_verify');
			redirect(base_url().ADMIN_URL);
		}
	}
	// get restuarent currency
	public function getCurrencySymbol(){
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$currency_symbol = 	$default_currency;
		}else{
			$currency_symbol = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));
		}
		//get System Option Data
		/*$this->db->select('OptionValue');
		$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
		$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		echo $currency_symbol->currency_symbol;
	}
	// get event currency
	public function getEventCurrencySymbol(){
		$currency_symbol = $this->common_model->getEventCurrencySymbol($this->input->post('entity_id'));
		//get System Option Data
	 /* $this->db->select('OptionValue');
		$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
		$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		
		echo $currency_symbol->currency_symbol;
	}
}