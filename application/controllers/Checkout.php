<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/restaurant_model');      
		$this->load->model('/cart_model');         
		$this->load->model('/home_model');         
		$this->load->model('/checkout_model'); 
		$this->load->library('facebook');
		// Load google oauth library
        $this->load->library('google');   
		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory',$data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug',$data['lang']->language_slug);
  		}   
	}
	// index chechout page
	public function index()
	{
		/*checking if user has sign up from checkout page and unset session variable after redirect to checkout page.*/
		if($this->session->userdata('sign_up_from_checkout_page') && $this->session->userdata('sign_up_from_checkout_page') == 1){
			$this->session->unset_userdata('sign_up_from_checkout_page');
		}
		$this->session->set_userdata('is_guest_checkout',0);
		$this->session->set_userdata('guest_otp_verified','0');
		$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_checkout'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');		
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['restaurant_order_mode'] = $this->common_model->get_restaurant_order_mode($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$data['is_out_of_stock_item_in_cart'] = $data['cart_details']['is_out_of_stock_item_in_cart'];
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}

		//menu suggestion :: start
		$cart_items_array = array();
		foreach ($data['cart_details']['cart_items'] as $cartkey => $cartvalue) {
			array_push($cart_items_array, $cartvalue['menu_content_id']);
		} 
		$data['menu_item_suggestion'] = $this->checkout_model->getMenuSuggestionItems($cart_restaurant,$this->session->userdata('language_slug'),$cart_items_array);
		//menu suggestion :: end
		//payment method :: start
        $data['paymentmethods'] = $this->checkout_model->getPaymentMethodSuggestion($cart_restaurant,$this->session->userdata('language_slug'),$this->session->userdata('UserType'));
		$lang_slug = $this->session->userdata('language_slug');
		foreach ($data['paymentmethods'] as $key => $value)
        {
			if($lang_slug == 'en'){
				$value->payment_name = $value->display_name_en;
			}
			if($lang_slug == 'fr'){
				$value->payment_name = $value->display_name_fr;
			}
			if($lang_slug == 'ar'){
				$value->payment_name = $value->display_name_ar;
			}
			if($value->payment_gateway_slug == 'applepay') {
				unset($data['paymentmethods'][$key]);
			}
        }
        //payment method :: end
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		$data['enabled_date_timeslots'] = $this->common_model->getDateAndTimeSlotsForScheduling($cart_restaurant,'','','','web',$data['cart_details']['is_out_of_stock_item_in_cart']);
		/*sales tax changes end*/
		$this->session->set_userdata('previous_url', current_url());
		if($this->input->post('submit_login_page') == "Login"){
			if($this->input->post('login_with')=="phone_number") {
				$this->form_validation->set_rules('login_phone_number', $this->lang->line('phone_number'), 'trim|required'); 
			}
			if($this->input->post('login_with')=="email") {
				$this->form_validation->set_rules('email_inp', $this->lang->line('email'), 'trim|required'); 
			}
	        $this->form_validation->set_rules('login_password', $this->lang->line('password'), 'trim|required');
	        if ($this->form_validation->run())
	        {  
	            $enc_pass = md5(SALT.trim($this->input->post('login_password')));
	            if($this->input->post('login_with')=="phone_number") {
		            $phone_number = trim($this->input->post('login_phone_number'));
		            $phone_code = trim($this->input->post('phone_code'));
		            
		            $this->db->where('phone_code',$phone_code);
		            $this->db->where('mobile_number',$phone_number);
					$this->db->where('password',$enc_pass);
					$this->db->where("(user_type='User' OR user_type='Agent')");
					$val = $this->db->get('users')->first_row();  
					if(!empty($val)) {
						if(empty($val->referral_code) && $val->user_type == 'User') {
				            $referral_code = random_string('alnum', 8);
				            $update = array(
				                'referral_code'=>$referral_code
				            );
				            $this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
				            
				            $this->db->where('phone_code',$phone_code);
		            		$this->db->where('mobile_number',$phone_number);
							$this->db->where('password',$enc_pass);
							$this->db->where("(user_type='User')");
							$val = $this->db->get('users')->first_row();
				        }
						if(empty($val->stripe_customer_id) && $val->user_type == 'User') {
							$stripe_customer_id = $this->common_model->add_new_customer_in_stripe($val->first_name,$val->last_name,$val->phone_code,$val->mobile_number,$val->email);
							if($stripe_customer_id){
								$update = array(
									'stripe_customer_id'=>$stripe_customer_id
								);
								$this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
							}
						}
						if($val->is_deleted=='1') {
							$this->session->set_userdata('login_with', 'phone_number');
							$data['loginError'] = $this->lang->line('delete_acc_validation');
						} else if($val->active=='1' && $val->status=='1') {
							$this->session->set_userdata(
								array(
								  'UserID' => $val->entity_id,
								  'userFirstname' => $val->first_name,                            
								  'userLastname' => $val->last_name,                            
								  'userEmail' => $val->email,
								  'userImage' => $val->image,
								  'userPhone' => $val->mobile_number,
								  'userPhone_code' => $val->phone_code,                            
								  //'is_admin_login' => 0,                           
								  'is_user_login' => 1,
								  'UserType' => $val->user_type,
								  //'package_id' => array(),
								)
							);
							// remember ME
							$cookie_name = "adminAuth";
							if($this->input->post('rememberMe')==1) {                    
								$this->input->set_cookie($cookie_name, 'usr='.$phone_number.'&phone_code='.$phone_code.'&hash='.trim($this->input->post('login_password')), 60*60*24*5); // 5 days
							} else {
								delete_cookie($cookie_name);
							}
							$this->session->set_userdata('login_with', '');
							redirect(base_url().'checkout');
						} else if($val->active=='0' || $val->active=='' || $val->status=='0') {       
							$this->session->set_userdata('login_with', 'phone_number');
							
							if($val->active=='0' || $val->active=='')
							{
								$this->session->set_userdata(
									array(
									  'UserID' => $val->entity_id,
									  'userFirstname' => $val->first_name,                            
									  'userLastname' => $val->last_name,                            
									  'userEmail' => $val->email,                                   
									  'userPhone' => $val->mobile_number,
									  'userPhone_code' => $val->phone_code,                                 
									  'userImage' => $val->image,                            
									  'is_admin_login' => 0,                           
									  'is_user_login' => 0,
									  'UserType' => $val->user_type,
									  //'package_id' => array(),
									)
								);
								if($val->user_type == 'User'){
									//send otp start 
							        $this->common_model->generateOTP($val->entity_id);
							        $user_record = $this->common_model->getSingleRow('users','entity_id',$val->entity_id);
							        $sms = $user_record->user_otp.$this->lang->line('your_otp');
							        $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                  					$mobile_numberT = $mobile_numberT.$user_record->mobile_number;
							        $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
							        if($user_record->email){
							        	$this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug')); //send email
							        }
							        //send otp end 
									$data['enter_otp'] = 'yes';         
									$this->session->set_userdata('enter_otp', $data['enter_otp']);
								}
								redirect(base_url().'checkout?frm_page='.$this->input->post('submit_login_page'));
								exit;
							}else{
								$data['enter_otp'] = 'no';
								$this->session->set_userdata('enter_otp', $data['enter_otp']);
								$this->session->set_userdata('facebook_user', 'no');
								$this->session->set_userdata('google_user', 'no');
								$this->db->select('OptionValue');                
						        $this->db->where('OptionSlug','Admin_Email_Address');        
						        $admin_email = $this->db->get('system_option')->first_row();
								$data['loginError'] = $this->lang->line('login_deactivedis').' '.$admin_email->OptionValue;
							}
						} else {
							$this->session->set_userdata('login_with', 'phone_number');
							$data['loginError'] = $this->lang->line('app_phone_login_error');
						}
					} else {
						$this->session->set_userdata('login_with', 'phone_number');
						$data['loginError'] = $this->lang->line('app_phone_login_error');
					}
				}elseif ($this->input->post('login_with')=="email") {
					$email = trim($this->input->post('email_inp'));

					$this->db->where('email',$email);
					$this->db->where('password',$enc_pass);
					$this->db->where("(user_type='User' OR user_type='Agent')");
					$val_by_email = $this->db->get('users')->first_row();

					if(!empty($val_by_email)) {       
						if(empty($val_by_email->referral_code) && $val_by_email->user_type == 'User') {
				            $referral_code = random_string('alnum', 8);
				            $update = array(
				                'referral_code'=>$referral_code
				            );
				            $this->common_model->updateData('users',$update,'entity_id',$val_by_email->entity_id);
				            
				            $this->db->where('email',$email);
							$this->db->where('password',$enc_pass);
							$this->db->where("(user_type='User')");
							$val_by_email = $this->db->get('users')->first_row();
				        }
				        if(empty($val_by_email->stripe_customer_id) && $val_by_email->user_type == 'User') {
							$stripe_customer_id = $this->common_model->add_new_customer_in_stripe($val_by_email->first_name,$val_by_email->last_name,$val_by_email->phone_code,$val_by_email->mobile_number,$val_by_email->email);
							if($stripe_customer_id){
								$update = array(
									'stripe_customer_id'=>$stripe_customer_id
								);
								$this->common_model->updateData('users',$update,'entity_id',$val_by_email->entity_id);
							}
						}
						if($val_by_email->is_deleted=='1') {
							$this->session->set_userdata('login_with', 'email');
							$data['loginError'] = $this->lang->line('delete_acc_validation');
						} else if($val_by_email->active=='1' && $val_by_email->status=='1') {
							$this->session->set_userdata(
								array(
								  'UserID' => $val_by_email->entity_id,
								  'userFirstname' => $val_by_email->first_name,                            
								  'userLastname' => $val_by_email->last_name,                            
								  'userEmail' => $val_by_email->email,
								  'userImage' => $val_by_email->image,
								  'userPhone' => $val_by_email->mobile_number,
								  'userPhone_code' => $val_by_email->phone_code,                            
								  //'is_admin_login' => 0,                           
								  'is_user_login' => 1,
								  'UserType' => $val_by_email->user_type,
								  //'package_id' => array(),
								)
							);
							// remember ME
							$cookie_name = "adminAuth";
							if($this->input->post('rememberMe')==1)
							{                    
								$this->input->set_cookie($cookie_name, 'usr='.$email.'&hash='.trim($this->input->post('login_password')), 60*60*24*5); // 5 days
							} 
							else 
							{
								delete_cookie($cookie_name);
							}
							$this->session->set_userdata('login_with', '');
							redirect(base_url().'checkout');
						} 
						else if($val_by_email->active=='0' || $val_by_email->active=='' || $val_by_email->status=='0')
						{       
							$this->session->set_userdata('login_with', 'email');
							if($val_by_email->active=='0' || $val_by_email->active=='')
							{
								$this->session->set_userdata(
									array(
									  'UserID' => $val_by_email->entity_id,
									  'userFirstname' => $val_by_email->first_name,                            
									  'userLastname' => $val_by_email->last_name,                            
									  'userEmail' => $val_by_email->email,                                   
									  'userPhone' => $val_by_email->mobile_number, 
									  'userPhone_code' => $val_by_email->phone_code,                               
									  'userImage' => $val_by_email->image,                            
									  //'is_admin_login' => 0,                           
									  'is_user_login' => 0,
									  'UserType' => $val_by_email->user_type,
									  //'package_id' => array(),
									)
								);
								if($val_by_email->user_type == 'User'){
									//send otp start 
							        $this->common_model->generateOTP($val_by_email->entity_id);
							        $user_record = $this->common_model->getSingleRow('users','entity_id',$val_by_email->entity_id);
							        $sms = $user_record->user_otp.$this->lang->line('your_otp');
							        $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                  					$mobile_numberT = $mobile_numberT.$user_record->mobile_number;
							        $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
							        if($user_record->email){
							        	$this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug')); //send email
							        }
							        //send otp end 
									$data['enter_otp'] = 'yes';         
									$this->session->set_userdata('enter_otp', $data['enter_otp']);
								}
								redirect(base_url().'checkout?frm_page='.$this->input->post('submit_login_page'));
								exit;
							}else{
								$data['enter_otp'] = 'no';
								$this->session->set_userdata('enter_otp', $data['enter_otp']);
								$this->session->set_userdata('facebook_user', 'no');
								$this->session->set_userdata('google_user', 'no');
								$this->db->select('OptionValue');                
						        $this->db->where('OptionSlug','Admin_Email_Address');        
						        $admin_email = $this->db->get('system_option')->first_row();
								$data['loginError'] = $this->lang->line('login_deactivedis').' '.$admin_email->OptionValue;
							}
						} 
						else 
						{
							$this->session->set_userdata('login_with', 'email');
							$data['loginError'] = $this->lang->line('app_email_login_error');
						}
					} else {
						$this->session->set_userdata('login_with', 'email');
						$data['loginError'] = $this->lang->line('app_email_login_error');
					}
				}
				//$this->session->set_flashdata('error_MSG', $data['loginError']);
				$_SESSION['error_MSG'] = $data['loginError'];
				redirect(base_url().'checkout');
				exit;
	        }
			$data['page'] = "login";
		}
		if ($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') != 'Agent') {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID')); //fetching user's wallet
		}
		$data['google_login_url'] = $this->google->loginURL();
		$data['authURL'] =  $this->facebook->login_url();
	    $this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));

	    //Code for user detail :: Start
	    $data['user_detail'] = array();
	    if($this->session->userdata('UserID'))
		{
			$data['user_detail'] = $this->checkout_model->getUsersDetail($this->session->userdata('UserID'));
		}
		//Code for user detail :: End
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['restaurant_data'] = $this->common_model->checkResForCart($data['cart_restaurant']);
		$this->load->view('checkout',$data);
	}
	// ajax checkout page for filters
	public function ajax_checkout()
	{
		$data['current_page'] = 'Checkout';
		$cart_details = get_cookie('cart_details');
		$arr_cart_details = json_decode($cart_details);
		$cart_restaurant = get_cookie('cart_restaurant');
		$this->session->unset_userdata('deliveryCharge');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			if ($this->input->post('action') == "plus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								if((int)$value->quantity >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $value->quantity + 1;
								}
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => ($value->quantity)?(((int)$value->quantity>=999)?999:$value->quantity+1):1,
						            'comment' => ($value->comment)?$value->comment:'',
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			}
			else if ($this->input->post('action') == "minus") {
				$menukey = '';
				$arrayDetails = array();				
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity - 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($value->quantity > 1) {
						 		if ($key == $menukey) {
									$cookie = array(
							            'menu_id'   => $value->menu_id,  
							            'quantity' => ($value->quantity)?($value->quantity - 1):1,
							            'comment' => ($value->comment)?$value->comment:'',
							            'addons'  => $value->addons,               
						            );
						 			$arrayDetails[] = $cookie;
					            }
					            else
					            {
					            	$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					            }
					 		}
					 		else
					 		{
					 			if ($key != $menukey) {
					 				$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					 			}
					 		}
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
					$cart_details = $this->getcookie('cart_details');
		            if (empty(json_decode($cart_details))) {
		            	delete_cookie('cart_details');
						delete_cookie('cart_restaurant');
						unset($_COOKIE['cart_details']);
						unset($_COOKIE['cart_restaurant']);
						
						$this->session->unset_userdata('is_redeem');
				    	$this->session->unset_userdata('redeem_submit');
				    	$this->session->unset_userdata('redeem_amount');
					}
					else
					{
		            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
					}
				}
			}
			else if (!$this->input->post('action')) {

				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								if((int)$this->input->post('customQuantity') >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $this->input->post('customQuantity');
								}
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		
						 		if ($key == $menukey) {
									$cookie = array(
							            'menu_id'   => $value->menu_id,  
							            'quantity' => ($value->quantity)?(((int)$this->input->post('customQuantity') >= 999)?999:$this->input->post('customQuantity')):1,
							            'comment' => ($value->comment)?$value->comment:'',
							            'addons'  => $value->addons,               
						            );
						 			$arrayDetails[] = $cookie;
					            }
					            else
					            {
					            	$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
					$cart_details = $this->getcookie('cart_details');
		            if (empty(json_decode($cart_details))) {
		            	delete_cookie('cart_details');
						delete_cookie('cart_restaurant');						
						unset($_COOKIE['cart_details']);
						unset($_COOKIE['cart_restaurant']);
						$this->session->unset_userdata('is_redeem');
				    	$this->session->unset_userdata('redeem_submit');
				    	$this->session->unset_userdata('redeem_amount');
					}
					else
					{
		            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
					}
				}
			}
			else if ($this->input->post('action') == "remove" && $this->input->post('cart_key') != '') { 
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
				 		if ($key != $this->input->post('cart_key')) {
					 		$oldcookie = $value;
				 			$arrayDetails[] = $oldcookie;
				 		}
				 	}
				}
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
				$cart_details = $this->getcookie('cart_details');
	            if (empty(json_decode($cart_details))) {
	            	delete_cookie('cart_details');
					delete_cookie('cart_restaurant');
					unset($_COOKIE['cart_details']);
					unset($_COOKIE['cart_restaurant']);
					$this->session->unset_userdata('is_redeem');
			    	$this->session->unset_userdata('redeem_submit');
			    	$this->session->unset_userdata('redeem_amount');
				}
				else
				{
	            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			} 
			else if ($this->input->post('action') == "updatecomment") {

				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {								
								$value->comment = ($this->input->post('comment'))?$this->input->post('comment'):'';
								/*if((int)$value->quantity >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $value->quantity + 1;
								}*/
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => $value->quantity,
						            'comment' => ($this->input->post('comment'))?$this->input->post('comment'):'',
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			}
			$cart_details = $this->getcookie('cart_details');
			$cart_restaurant = $this->getcookie('cart_restaurant');
		}
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$ajax_driver_tips = $this->load->view('ajax_driver_tips',$data,true);
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		//get System Option Data
       /* $this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$data['order_mode'] = $this->session->userdata('order_mode');
		/* $data['reset_coupon_discount_on_item_change']  if Plus minus action done in cart item this will be set to true*/
		$data['reset_coupon_discount_on_item_change'] = TRUE;
		$this->removeCouponOnReset();
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['restaurant_data'] = $this->common_model->checkResForCart($data['cart_restaurant']);
		$data['current_page']='Checkout';
		$ajax_your_items = $this->load->view('ajax_your_items',$data,true);
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$order_summary = $this->load->view('ajax_order_summary',$data,true);

		//menu suggestion :: start
		$cart_items_array = array();
		foreach ($data['cart_details']['cart_items'] as $cartkey => $cartvalue) {
			array_push($cart_items_array, $cartvalue['menu_content_id']);
		} 
		$data['menu_item_suggestion'] = $this->checkout_model->getMenuSuggestionItems($cart_restaurant,$this->session->userdata('language_slug'),$cart_items_array);
		$ajax_your_suggestion = $this->load->view('ajax_your_suggestion',$data,true);
		//menu suggestion :: end
		if($data['cart_details']['is_out_of_stock_item_in_cart']) {
			//scheduling mandatory
			$schedule_mandatory_html = '<div id="order_later_checkbox" >
				<label>
					<input type="hidden" name="schedule_order" id="schedule_order" value="yes">
					<span>'.$this->lang->line('order_later_mandatory').'</span>
				</label>
			</div>';
		} else {
			//scheduling not mandatory
			$schedule_mandatory_html = '<div id="order_later_checkbox" class="radio-btn-list" >
				<label>
					<input type="checkbox" name="schedule_order" id="schedule_order" value="yes">
					<span>'.$this->lang->line('order_later').'</span>
				</label>
			</div>';
		}
		$array_view = array(
			'ajax_your_items'=>$ajax_your_items,
			'ajax_driver_tips'=>$ajax_driver_tips,
			'ajax_order_summary'=>$order_summary,
			'ajax_your_suggestion'=>$ajax_your_suggestion,
			'cart_total'=>$data['cart_details']['cart_total_price'],
			'is_out_of_stock_item_in_cart' => $data['cart_details']['is_out_of_stock_item_in_cart'],
			'schedule_mandatory_html' => $schedule_mandatory_html
 		);
		echo json_encode($array_view); exit;
	}
	// get the recently added cookies
	public function getcookie($name) { 
	    $cookies = [];
	    $headers = headers_list(); 
	    foreach($headers as $key => $header) { 
	        if (strpos($header, 'Set-Cookie: ') === 0) {
	            $value = str_replace('&', urlencode('&'), substr($header, 12));
	            parse_str(current(explode(';', $value)), $pair);
	            $cookies = array_merge_recursive($cookies, $pair);
	        }
	    }
	    return $cookies[$name];
	}
	// get Cart items
	public function getCartItems($cart_details,$cart_restaurant){
		$cartItems = array();
		$cartTotalPrice = 0;
		$arrayDetails = array();
		$is_out_of_stock_item_in_cart = false;
		if (!empty($cart_details) && !is_array($cart_details)) {
			foreach (json_decode($cart_details) as $key => $value) { 
				$details = $this->restaurant_model->getMenuItem($value->menu_id,$cart_restaurant);
				if (!empty($details))
				{
					$oldcookie = $value;
				 	$arrayDetails[] = $oldcookie;
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
					{	$subtotal = 0;
						$offer_price = str_replace(",", "", $details[0]['items'][0]['offer_price']);
						if (!empty($details[0]['items'][0]['addons_category_list'])  && is_array($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!empty($cat_value['addons_list']) && is_array($cat_value['addons_list'])) {
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
					{	$subtotal = 0;
						if ($details[0]['items'][0]['is_deal'] == 1) {
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						else
						{
							//$price = ($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0;
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						$mprice = str_replace(",","",$price);
						$subtotal = $subtotal + $mprice;
					}
					$cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
					if($details[0]['items'][0]['stock'] == 0){
						$is_out_of_stock_item_in_cart = true;
					}
					$cartItems[] = array(
						'menu_id' => $details[0]['items'][0]['menu_id'],
						'menu_content_id'=>$details[0]['items'][0]['menu_content_id'],
						'restaurant_id' => $cart_restaurant,
						'name' => $details[0]['items'][0]['name'],
						'is_combo_item' => $details[0]['items'][0]['is_combo_item'],
						'in_stock' => $details[0]['items'][0]['stock'],
						'menu_detail' => $details[0]['items'][0]['menu_detail'],
						'quantity' => $value->quantity,
						'comment' => ($value->comment)?$value->comment:'',
						'is_customize' => $details[0]['items'][0]['is_customize'],
						'is_veg' => $details[0]['items'][0]['is_veg_food'],
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
		$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		if(empty($arrayDetails)){
			delete_cookie('cart_restaurant');
			delete_cookie('cart_details');
			unset($_COOKIE['cart_details']);
			unset($_COOKIE['cart_restaurant']);

			$this->session->unset_userdata('tip_amount');
			$this->session->unset_userdata('is_redeem');
	    	$this->session->unset_userdata('redeem_submit');
	    	$this->session->unset_userdata('redeem_amount');
		}
		$cart_details = array(
			'cart_items' => $cartItems,
			'cart_total_price' => $cartTotalPrice,
			'is_out_of_stock_item_in_cart' => $is_out_of_stock_item_in_cart
		);
		return $cart_details;
	}
	// get lat long from the address
	public function getAddressLatLng(){
		$latlong = array();
		if (!empty($this->input->post('entity_id'))) {
			$latlong = $this->checkout_model->getAddressLatLng($this->input->post('entity_id'));
		}
		echo json_encode($latlong);
	}
	// get the delivery charges
	public function getDeliveryCharges()
	{ 
		$check = '';
		if (!empty($this->input->post('action')) && $this->input->post('action') == "get") { 
			if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) { 
				$cart_restaurant = get_cookie('cart_restaurant'); 
				if(is_numeric($cart_restaurant))
				{
					$check = $this->checkGeoFence($this->input->post('latitude'),$this->input->post('longitude'),$price_charge = true,$cart_restaurant);
					//get System Option Data
					$this->db->select('OptionValue');
					$min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
					$min_order_amount = (float) $min_order_amount->OptionValue;
					 //cart_total
					if ($check['price_charge']) {
						//depends on subtotal and min order amount
						$additional_delivery_charge = ($check['additional_delivery_charge'])?$check['additional_delivery_charge']:0;
						//based on location
						$exist_delivery_charge = ($check['price_charge'])?$check['price_charge']:0;

						if($this->input->post('cart_total') >= $min_order_amount) {
							$deliveryCharge = $exist_delivery_charge;
						} else {
							$deliveryCharge = $exist_delivery_charge + $additional_delivery_charge;
						}
						
						$this->session->set_userdata(array('checkDelivery' => 'available','deliveryCharge' => $deliveryCharge));
					}
					else
					{
						$this->session->set_userdata(array('checkDelivery' => 'notAvailable','deliveryCharge' => 0));
					}
				}
			}
		}
		if (!empty($this->input->post('action')) && $this->input->post('action') == "remove") { 
			$check = 0;
			$this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
		}
		if ($check == '' || $check == 0) {
			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
			$this->session->unset_userdata('coupon_array');
		}
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->session->userdata('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
		$array_view = array(
			'check'=>(!empty($check)) ? $check['price_charge'] : '',
			'ajax_order_summary'=>$order_summary
		);
		echo json_encode($array_view);
	}
	// remove the delivery charges
	public function removeDeliveryOptions(){
		$this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
		$this->session->set_userdata('tip_amount', 0);
		$this->session->unset_userdata('tip_percent_val');
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
        $default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->session->userdata('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$this->load->view('ajax_order_summary',$data);
	}
    //check lat long exist in area
    public function checkGeoFence($latitude,$longitude,$price_charge,$restaurant_id)
    {
        $result = $this->checkout_model->checkGeoFence($restaurant_id); 
        $latlongs =  array($latitude,$longitude);
        if (!empty($result)) {
        	foreach ($result as $reskey => $resvalue) {
        		$coordinatesArr = array();
                if (!empty($resvalue->lat_long)) {
                    $lat_longs =  explode('~', $resvalue->lat_long);
                    foreach ($lat_longs as $key => $value) {
                        $val = str_replace(array('[',']'),array('',''),$value);
                        $coordinatesArr[] =  explode(',', $val);
                    }
                }
                $output = $this->checktestFence($latlongs, $coordinatesArr, $resvalue->price_charge, $resvalue->additional_delivery_charge);
                if(!empty($output['price_charge'])) {
                    return $output;
                    exit;
                }
            }
        }
        return $output;
    }
    // check geo fence area
    public function checkFence($point, $polygon, $price_charge, $additional_delivery_charge)
	{
	    if($polygon[0] != $polygon[count($polygon)-1])
	            $polygon[count($polygon)] = $polygon[0];
	    $j = 0;
	    $oddNodes = '';
	    $x = $point[1];
	    $y = $point[0];
	    $n = count($polygon);
	    for ($i = 0; $i < $n; $i++)
	    {
	        $j++;
	        if ($j == $n)
	        {
	            $j = 0;
	        }
	        if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
	            $y)))
	        {
	            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
	                $polygon[$i][1]) < $x)
	            {
	                $oddNodes = 'true';
	            }
	        }
	    }
	    $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
	    $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
	    return $price_arr;
	}
	// get the coupons
    public function getCoupons(){
    	$html = '';
    	$User_ID = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
    	$coupon_searchval = ($this->input->post('coupon_searchval'))?trim($this->input->post('coupon_searchval')):'';
    	$frmcoupon = ($this->input->post('frmcoupon'))?$this->input->post('frmcoupon'):'no';
    	$cart_restaurant = get_cookie('cart_restaurant');
    	if($frmcoupon=='no')
    	{
    		$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));			
			$this->session->unset_userdata('coupon_array');
    	}    	
    	if(!empty($this->input->post('subtotal')) && !empty($this->input->post('order_mode')))
    	{
    		$couponstemp = $this->checkout_model->getCouponsList($this->input->post('subtotal'),$cart_restaurant,$this->input->post('order_mode'),$User_ID,$coupon_searchval,$this->session->userdata('is_guest_checkout'),$this->session->userdata('UserType'));
    		//Code for filter array with requirement :: Start
    		$coupons = array();
    		$cntt=0;
    		if($couponstemp && !empty($couponstemp))
    		{
    			for($i=0;$i<count($couponstemp);$i++)
    			{
    				$flag_cnt = 'yes';
    				$UserID = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
    				$UserType = ($this->session->userdata('UserType'))?$this->session->userdata('UserType'):'User';
    				$checkCnt = $this->common_model->checkUserUseCountCoupon($UserID,$couponstemp[$i]['coupon_id']);
                    if($checkCnt >= $couponstemp[$i]['maximaum_use_per_users'] && $couponstemp[$i]['maximaum_use_per_users']>0 && $UserType=='User')
                    {
                        $flag_cnt = 'no';
                    }                    
                    if($flag_cnt=='yes')
                    {
                    	$checkCnt1 = $this->common_model->checkTotalUseCountCoupon($couponstemp[$i]['coupon_id']);
                    	if($checkCnt1 >= $couponstemp[$i]['maximaum_use'] && $couponstemp[$i]['maximaum_use']>0){
	                    	$flag_cnt = 'no';
	                    }	                    
                    }
                    if($flag_cnt=='yes')
                    {
                    	//Code for free delviery coupon falg check :: Start
                    	$user_chkcpn = 'yes';                    	
				        if($UserID>0)
				        {            
				            $this->db->select('entity_id');
				            $this->db->where('user_id',$UserID);
				            $user_chk = $this->db->count_all_results('order_master');
				            if($user_chk>0)
				            {
				                $user_chkcpn = 'no';
				            }            
				        }
				        if($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('UserType') == 'Agent'){
				            $user_chkcpn = 'no';
				        }				        
                    	if($couponstemp[$i]['coupon_type']=='free_delivery' && strtolower($this->input->post('order_mode'))=='delivery' && $user_chkcpn=='no' && $couponstemp[$i]['coupon_for_newuser']=='1')
                    	{
                    	}//Code for free delviery coupon falg check :: End
                    	else
                    	{
                    		$coupons[$cntt] = $couponstemp[$i];
                    		$cntt++;
                    	}
                    }
    			}	
    		}    		
    		//Code for filter array with requirement :: End

    		$order_mode = "'".$this->input->post('order_mode')."'";    		
    		if(!empty($coupons))
    		{
    			$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
    			$coupon_idarr = array();
    			if(!empty($coupon_array))
    			{
    				$coupon_idarr = array_column($coupon_array, 'coupon_id');
    			}

    			$html = '<h4>'.$this->lang->line('available_offers').'</h4>';
    			foreach ($coupons as $key => $value) 
    			{
    				$applybtnval = (!empty($coupon_idarr) && in_array($value["coupon_id"], $coupon_idarr))?$this->lang->line('applied'):$this->lang->line('apply');
    				$coupon_title = '';
    				//changes start
    				$coupon_title_str = strip_tags($value["description"]);
    				$coupon_title_str = rtrim(str_replace("&nbsp;", " ", $coupon_title_str));
    				//changes end
    				$coupon_title = substr($coupon_title_str,0, 50);
    				$coupon_title = (strlen($coupon_title_str)>50)?$coupon_title.'...':$coupon_title;
    				$coupon_detailbtn = (strlen($coupon_title_str)>50)?'<a href="#" id="show-hidden-menu'.$key.'" class="all-detail show-hidden-menu">'.$this->lang->line('view_more').'</a><a href="#" id="hhshow-hidden-menu'.$key.'" class="all-detail hhshow-hidden-menu" dataval="show-hidden-menu'.$key.'" style="display:none;">'.$this->lang->line('view_less').'</a>':'';    				
    				$coupon_description = $value["description"];

                	$html .= '<div class="coupon_detail_inner">
							<div class="coupon-head">
								<h2 class="coupon_title">'.$value["name"].'</h2>
								<button class="btn coupon_apply" value="'.$value["coupon_id"].'" onclick="getCouponDetails(this.value,'.$this->input->post('subtotal').','.$order_mode.',\'yes\')">'.$applybtnval.'</button>
							</div>
							<div class="coupon_description">
								<p><span class="spnshow-hidden-menu" id="spnshow-hidden-menu'.$key.'">'.$coupon_title.'</span>'.$coupon_detailbtn.'</p>
								<div class="hidden-menu" id="subshow-hidden-menu'.$key.'" style="display: none;">
									'.$coupon_description.'								
								</div>
							</div>
						</div>';    
                } 
    		}
    		else
    		{
    			$html = '<h5>'.$this->lang->line("no_coupons_available").'</h5>';
    			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));
    			$this->session->unset_userdata('coupon_array');
    		}
    	} 
    	$this->session->set_userdata(array('order_mode' => $this->input->post('order_mode')));
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
        $default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->input->post('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$order_summary = $this->load->view('ajax_order_summary',$data,true);			
		$array_view = array(
			'html'=>$html,
			'ajax_order_summary'=>$order_summary
		);
		echo json_encode($array_view);
    }
    // add a coupon for a order
    public function addCoupon()
    {	
		$data['page_title'] = $this->lang->line('add_coupon'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'Checkout';
    	if((!empty($this->input->post('coupon_id')) || !empty($this->session->userdata('coupon_array'))) && !empty($this->input->post('subtotal')))
    	{
    		$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
    		$coupon_idstr = ''; $coupon_idarr=array();
    		if(!empty($coupon_array))
			{
				$coupon_idarr = array_column($coupon_array, 'coupon_id');
				$coupon_idstr = implode(",", $coupon_idarr);
			}
    		$coupon_idval = ($this->input->post('coupon_id'))?$this->input->post('coupon_id'):$coupon_idstr;
    		//$this->session->set_userdata(array('coupon_id' => $coupon_idval,'coupon_applied' => 'yes'));

    		//Code forcoupon multiple use validation :: Start
    		$chk_coupon_id = ($this->input->post('coupon_id'))?$this->input->post('coupon_id'):0;
    		$frm_apply = ($this->input->post('frm_apply'))?$this->input->post('frm_apply'):'no';
    		if(intval($chk_coupon_id)>0 && $frm_apply=='yes' && !empty($coupon_idarr))
    		{
    			$coupon_chkarr = $coupon_idarr;
    			if(!in_array($chk_coupon_id, $coupon_chkarr))
    			{
    				array_push($coupon_idarr,$chk_coupon_id);
    			}    			
    			$check_couponuse = array();
    			if(!empty($coupon_idarr))
    			{
    				if(count($coupon_idarr)>1)
	    			{
	    				$check_couponuse = $this->common_model->chkCouponforMUtliple($coupon_idarr);    				
	    			}
    			}
    			if(!empty($check_couponuse))
    			{
    				//if(count($check_couponuse)>=1){
	    				$c_name = $check_couponuse->name;
	    				$resp_data = array('error'=>'yes','coupon_error'=>$c_name." ".$this->lang->line('coupon_use_error'));
						echo json_encode($resp_data); exit;
	    			//}
    			}    			
    		}
    		//Code forcoupon multiple use validation :: End

    		$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'yes'));
    		$coupon_idarr = explode(",", $coupon_idval);    		
    		if(!empty($coupon_idarr))
    		{
    			foreach($coupon_idarr as $cpi_key => $cpi_value)
    			{
    				$check = $this->checkout_model->getCouponDetails($cpi_value);
		    		$status = 1;
		    		if(!empty($check))
		    		{
		                if($check->coupon_type == 'discount_on_cart'){
		                    if($check->amount_type == 'Percentage'){
		                        $discount = (($this->input->post('subtotal') * $check->amount)/100);
		                       
		                    }else if($check->amount_type == 'Amount'){
		                        $discount = $check->amount;
		                        
		                    }
		                    $coupon_id = $check->entity_id;  
		                    $coupon_type = $check->amount_type;
		                    $coupon_amount = $check->amount;  
		                    $coupon_discount = ($discount);
		                    $name = $check->name;     
		                }
		                if($check->coupon_type == 'free_delivery'){
		                   
		                    $discount = $this->session->userdata('deliveryCharge');

		                    $coupon_id = $check->entity_id;  
		                    $coupon_type = $check->amount_type;
		                    //$coupon_amount = $check->amount;  
		                    $coupon_amount = $discount;
		                    $coupon_discount = ($discount);
		                    $name = $check->name;     
		                }
		                if($check->coupon_type == 'user_registration'){
		                    $checkOrderCount = $this->checkout_model->checkUserCountCoupon($this->session->userdata('UserID'));
		                    if($checkOrderCount > 0){
		                        $status = 2;
		                    }else{
		                        if($check->amount_type == 'Percentage'){
		                            $discount = (($this->input->post('subtotal') * $check->amount)/100);
		                            
		                        }else if($check->amount_type == 'Amount'){
		                            $discount = $check->amount;
		                        } 
		                        $coupon_id = $check->entity_id;  
		                        $coupon_type = $check->amount_type;
		                        $coupon_amount = $check->amount;  
		                        $coupon_discount = ($discount);
		                        $name = $check->name;     
		                    }
		                }
		            }
		            if($status == 1)
		            {
		            	$coupon_arrayold = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
		            	$arr_cpcount=0; $isnew_coupon = 'yes';
		            	if($coupon_arrayold && !empty($coupon_arrayold))
		            	{
		            		$arr_cpcount=count($coupon_arrayold);
		            		$coupon_array = $coupon_arrayold;
		            		foreach ($coupon_array as $cp_key => $cp_value)
		            		{
		            			if($cp_value['coupon_id']==$coupon_id)
		            			{
		            				$isnew_coupon = 'no';
		            			}            			
		            		}         		
		            	}	
		            	//echo "<pre>"; print_r($this->session->userdata()); exit();
		            	if($isnew_coupon=='yes')
		            	{
		            		$coupon_array[$arr_cpcount] = array('coupon_id' => $coupon_id,
				            	'coupon_type' => $coupon_type,
				            	'coupon_amount' => $coupon_amount,
				            	'coupon_discount' => $coupon_discount,
				            	'coupon_name' => $name);
		            	}
		            	$this->session->set_userdata('coupon_array',$coupon_array);
		            }
	            	$checkamount = $this->checkout_model->checkCouponAmount($cpi_value,$this->input->post('subtotal'));
		    		if(!$checkamount)
		    		{
		    			$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
						if(!empty($this->session->userdata('coupon_array')))
						{
							foreach ($coupon_array as $chk_key => $chk_value)
							{
								if($cpi_value==$chk_value['coupon_id'])
								{
									unset($coupon_array[$chk_key]);
								}
							}
							$coupon_array = array_values($coupon_array);
							if(!empty($coupon_array))
							{
								$this->session->set_userdata('coupon_array',$coupon_array);
							}
							else
							{
								$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));			
								$this->session->unset_userdata('coupon_array');
							}
						}
		    		}
    			}    			
    		}	
    	}
    	else
    	{
    		$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));    		
    		$this->session->unset_userdata('coupon_array');
    	}
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
        $default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->input->post('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
    	//$this->load->view('ajax_order_summary',$data);
    	$order_summary = $this->load->view('ajax_order_summary',$data,true);
    	$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
    	$coupon_discount = 0;
    	if(!empty($coupon_array))
    	{
    		foreach ($coupon_array as $cp_key => $cp_value) {
    			$coupon_discount += ($cp_value['coupon_discount'])?$cp_value['coupon_discount']:0;
    		}
    	}		
		$coupon_discount = round((float)$coupon_discount,2);
		$resp_data = array('ajax_order_summary'=>$order_summary,'coupon_discount'=>$coupon_discount);
		echo json_encode($resp_data);
    }
    //Code for remove coupon (also used for check payment options)
    public function removeCouponOptions()
    {
    	if($this->input->post('call_from') == 'remove_cpn_options')
    	{
    		//Code for delete coupon :: Start
    		$coupon_id = ($this->input->post('coupon_id'))?$this->input->post('coupon_id'):0;    		
			$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];			
			if(!empty($coupon_array) && $coupon_id>0)
			{
				foreach ($coupon_array as $chk_key => $chk_value)
				{
					if($coupon_id==$chk_value['coupon_id'])
					{
						unset($coupon_array[$chk_key]);
					}
				}
				$coupon_array = array_values($coupon_array);				
				if(!empty($coupon_array))
				{
					$this->session->set_userdata('coupon_array',$coupon_array);
				}
				else
				{
					$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));			
					$this->session->unset_userdata('coupon_array');
				}
			}
			//Code for delete coupon :: End
		}

    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->input->post('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
    	$this->load->view('ajax_order_summary',$data);
    }
	//add order
    public function addOrder()
    {
		$data['page_title'] = $this->lang->line('add_order'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'Checkout';
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$this->session->unset_userdata('order_id');
		$cart_item_details = $this->getCartItems($cart_details,$cart_restaurant);
    	if(($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID')) && !empty($cart_restaurant)) || ($this->session->userdata('is_guest_checkout')=='1' && !empty($cart_restaurant)))
    	{
    		if($this->input->post('res_allow_scheduled_delivery') == 1 && $this->input->post('schedule_order') == 'yes' && $this->input->post('scheduled_date') && $this->input->post('slot_open_time')) {
				$scheduled_date_inp = ($this->input->post('scheduled_date')) ? date('Y-m-d', strtotime(str_replace('-', '/', $this->input->post('scheduled_date')))) : '';
				$slot_open_time = ($this->input->post('slot_open_time')) ? $this->input->post('slot_open_time') : '';
				$slot_close_time = ($this->input->post('slot_close_time')) ? $this->input->post('slot_close_time') : '';
				$scheduled_order_opentime = date('Y-m-d H:i:s', strtotime("$scheduled_date_inp $slot_open_time"));
				$scheduled_order_closetime = date('Y-m-d H:i:s', strtotime("$scheduled_date_inp $slot_close_time"));
			}

			$restaurant_detail = $this->checkout_model->getRestaurantTax($cart_restaurant);
			if($this->session->userdata('is_guest_checkout')=='1' || $this->session->userdata('UserType') == 'Agent') {
	        	$guest_form = array();
				parse_str($this->input->post('guest_form'), $guest_form);
				$user_id_post = ($this->session->userdata('is_guest_checkout')=='1')?0:(($this->session->userdata('UserType') == 'Agent' && $guest_form['consider_guest'] == 'yes')?0:$guest_form['exist_user_id']);
			} else {
				$user_id_post = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):0;
			}
            if($restaurant_detail->timings['closing'] != 'Closed' && $restaurant_detail->enable_hours == '1' && $restaurant_detail->restaurant_status == '1' && $restaurant_detail->timings['off'] != 'close')
            {
            	$coupon_type = ($this->session->userdata('coupon_type')=='null' || empty($this->session->userdata('coupon_type')))?NULL:$this->session->userdata('coupon_type');

	    		$add_data = array(
	                'user_id'=> ($user_id_post)?$user_id_post:0,
	                'agent_id'=> ($this->session->userdata('UserType') == 'Agent')?(($this->session->userdata('UserID'))?$this->session->userdata('UserID'):NULL):NULL,
	                'restaurant_id' => $cart_restaurant,
	                'address_id' => ($this->input->post('your_address'))?$this->input->post('your_address'):NULL,
	                'order_status' =>'placed',
	                'order_date' =>date('Y-m-d H:i:s'),
	                'scheduled_date' => ($this->input->post('res_allow_scheduled_delivery') == 1 && $this->input->post('schedule_order') == 'yes' && $this->input->post('scheduled_date')) ? date('Y-m-d', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime))) : null,
					'slot_open_time' => ($this->input->post('res_allow_scheduled_delivery') == 1 && $this->input->post('schedule_order') == 'yes' && $this->input->post('slot_open_time')) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime))) : null,
					'slot_close_time' => ($this->input->post('res_allow_scheduled_delivery') == 1 && $this->input->post('schedule_order') == 'yes' && $this->input->post('slot_close_time')) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_closetime))) : null,
	                'created_date'=>date('Y-m-d H:i:s'),
	                'subtotal'=> ($this->input->post('subtotal'))?$this->input->post('subtotal'):0,
	                'tax_rate'=>($this->input->post('service_taxval'))?(float)$this->input->post('service_taxval'):0.00,
                	'tax_type'=>($this->input->post('service_tax_typeval'))?$this->input->post('service_tax_typeval'):NULL,
	                'total_rate' => ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0.00,
	                'status'=>0,
	                'delivery_charge'=> ($this->session->userdata('deliveryCharge'))?$this->session->userdata('deliveryCharge'):NULL,
	                'extra_comment'=> ($this->input->post('extra_comment'))?$this->input->post('extra_comment'):'',
	                'payment_option'=> ($this->input->post('payment_option'))?$this->input->post('payment_option'):'',
	                'paid_status'=> 'paid'
	            ); 
	            //tweaks in service fee :: start
				if($this->input->post('is_service_fee_applied') == 'yes'){
					$add_data['service_fee_type'] = ($this->input->post('service_fee_typeval'))?$this->input->post('service_fee_typeval'):NULL;
					$add_data['service_fee'] = ($this->input->post('service_feeval'))?(float)$this->input->post('service_feeval'):0.00;
				}
	            //tweaks in service fee :: end
				// if($restaurant_detail->is_service_fee_enable == '1'){
				// 	$add_data['service_fee_type'] = $restaurant_detail->service_fee_type;
				// 	$add_data['service_fee'] = $restaurant_detail->service_fee;
				// }

				//Code for creditcard fee :: Start
				//tweaks in credit card fee :: start
				if($this->input->post('is_creditcard_fee_applied') == 'yes'){
					$add_data['creditcard_fee_type'] = ($this->input->post('creditcard_fee_typeval'))?$this->input->post('creditcard_fee_typeval'):NULL;
					$add_data['creditcard_fee'] = ($this->input->post('creditcard_feeval'))?(float)$this->input->post('creditcard_feeval'):0.00;
				}
				//tweaks in credit card fee :: end
				$chk_transaction_id = '';
				if($this->input->post('payment_option') == 'stripe')
				{
					$chk_transaction_id = $this->input->post('paymentIntentId');
				}
				else if ($this->input->post('payment_option') == 'paypal')
				{
					$chk_transaction_id = $this->input->post('paypal_transaction_id');
				}
				// if($restaurant_detail->is_creditcard_fee_enable == '1' && $chk_transaction_id!=''){
				// 	$add_data['creditcard_fee_type'] = $restaurant_detail->creditcard_fee_type;
				// 	$add_data['creditcard_fee'] = $restaurant_detail->creditcard_fee;
				// }
				//Code for creditcard fee :: End

	            if($this->session->userdata('coupon_applied') == "yes")
	            {
	            	//Code for add the coupon value in relation table and first value add in order master table :: Start
		        	$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];		        	
		        	if(!empty($coupon_array))
		        	{
		        		$add_data['coupon_id'] = ($coupon_array[0]['coupon_id'])?$coupon_array[0]['coupon_id']:null;
		        		$add_data['coupon_type'] = ($coupon_array[0]['coupon_type'])?$coupon_array[0]['coupon_type']:null;
		        		$add_data['coupon_amount'] = ($coupon_array[0]['coupon_amount'])?$coupon_array[0]['coupon_amount']:null;
		        		$add_data['coupon_discount'] = ($coupon_array[0]['coupon_discount'])?$coupon_array[0]['coupon_discount']:null;
		        		$add_data['coupon_name'] = ($coupon_array[0]['coupon_name'])?$coupon_array[0]['coupon_name']:null;		        		
		        	}
		        	//Code for add the coupon value in relation table and first value add in order master table :: End
	            }
	            if($this->session->userdata('is_redeem') == 1){
	            	$add_data['used_earning'] = ($this->session->userdata('redeem_amount'))?$this->session->userdata('redeem_amount'):0;
	            }
				if($this->input->post('choose_order')=='delivery'){
	                $add_data['order_delivery'] = 'Delivery';
	                $add_data['delivery_instructions'] = ($this->input->post('delivery_instructions')) ? $this->input->post('delivery_instructions') : '';
	            } else {
	                $add_data['order_delivery'] = 'PickUp';
	                if($this->session->userdata('UserType') == 'Agent') {
	                	if($guest_form['consider_guest'] == 'no' && $guest_form['exist_user_id']>0) {
			                $default_address = $this->common_model->getSingleRowMultipleWhere('user_address',array('user_entity_id'=>$guest_form['exist_user_id'],'is_main'=>1));
			                if (!empty($default_address)) {
			                	$add_data['address_id'] = $default_address->entity_id;
			                }
		                }
		            } else {
		            	$default_address = $this->common_model->getSingleRowMultipleWhere('user_address',array('user_entity_id'=>$this->session->userdata('UserID'),'is_main'=>1));
		                if (!empty($default_address)) {
		                	$add_data['address_id'] = $default_address->entity_id;
		                }
		            }
	            } 
	            /*if($this->input->post('payment_option') == 'CardOnline'){
					$add_data['payment_status'] = 'unpaid';
				}*/
	            $order_id = $this->common_model->addData('order_master',$add_data);

	            //Code for add the coupon value in relation table and first value add in order master table :: Start
	        	if(!empty($coupon_array))
	        	{
	        		foreach($coupon_array as $cp_key => $cp_value)
	        		{
	        			$ordder_cpnarr[] = array(
	        				'order_id'=> $order_id,
	        				'coupon_id'=> $cp_value['coupon_id'],
	        				'coupon_type'=>$cp_value['coupon_type'],
	        				'coupon_amount'=>$cp_value['coupon_amount'],
	        				'coupon_discount'=>$cp_value['coupon_discount'],
	        				'coupon_name'=>$cp_value['coupon_name']
	        			);        			
	        		}
	        		$this->checkout_model->inserBatch('order_coupon_use',$ordder_cpnarr);
	        	}
	            //Code for add the coupon value in relation table and first value add in order master table :: End

	            //driver tip changes :: start
	            $driver_tip = ($this->input->post('driver_tip')>0)?$this->input->post('driver_tip'):0;
                if($driver_tip && $driver_tip>0 && $this->input->post('choose_order')=='delivery')
                {
                    $add_tip = array(
                        'order_id'=>$order_id,
                        'user_id'=>($user_id_post)?$user_id_post:0,
                        'tip_percentage'=>($this->session->userdata('tip_percent_val') > 0)? (float)$this->session->userdata('tip_percent_val'):NULL,
                        'amount'=>$driver_tip,
                        'date'=>date('Y-m-d H:i:s')
                    );
                    $tips_id = $this->common_model->addData('tips',$add_tip);
                }
                //driver tip changes :: end

	            //earning points changes start
	            if($this->session->userdata('is_guest_checkout')!='1' && ($this->session->userdata('total_price') || $this->session->userdata('total_price')==0) && $this->session->userdata('UserType') != 'Agent') { 
	                //update the used and new earning points to the user's account
					//points that user had
	                $usersEarningPoints = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID')); //user's wallet
	                $current_earning = $usersEarningPoints->wallet;
	                $remaining_earning = $current_earning;
	                
	                //remaining earning points that user have after redeem
	                if($current_earning > 0){
	                	if($this->session->userdata('is_redeem') == 1){
	                    	$remaining_earning = $remaining_earning - $this->session->userdata('redeem_amount');

	                    	//add wallet history - amount debited.
		                    $addWalletHistory = array(
		                        'user_id'=>$this->session->userdata('UserID'),
		                        'order_id'=>$order_id,
		                        'amount'=>($this->session->userdata('redeem_amount'))?$this->session->userdata('redeem_amount'):0,
		                        'debit'=>1,
		                        'reason'=>'money_debited_for_order',
		                        'created_date' => date('Y-m-d H:i:s')
		                    );
		                    $this->checkout_model->addRecord('wallet_history',$addWalletHistory);
	                    }
	                }
	               
	                //remaining earning points that user have after redeem and earned points from that order
	                $minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
	                $minimum_subtotalval =0;
	                if($minimum_subtotal && !empty($minimum_subtotal))
	                {
	                	$minimum_subtotalval = $minimum_subtotal->OptionValue;
	                }
	                $new_earning_points = 0;
	                $points = $this->db->get_where('system_option',array('OptionSlug'=>'earning_1_point'))->first_row();
	                if($minimum_subtotalval>0 && intval($this->input->post('subtotal'))>=$minimum_subtotalval)
	                {
	                	$earned_points = intval((intval($this->input->post('subtotal'))*$points->OptionValue)/100);
	                	$new_earning_points = intval($remaining_earning + $earned_points);
	                }
	                if($new_earning_points > 0){
	                    $update_userdata = array('wallet' => $new_earning_points);            
	                    $this->checkout_model->updateUser('users',$update_userdata,'entity_id',$this->session->userdata('UserID'));

	                    //add wallet history - amount credited.
                        $addWalletHistory = array(
                            'user_id'=>$this->session->userdata('UserID'),
                            'order_id'=>$order_id,
                            'amount'=>$earned_points,
                            'credit'=>1,
                            'reason'=>'money_credited_for_order',
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->checkout_model->addRecord('wallet_history',$addWalletHistory);
	                }
	            }
	            //earning points changes end 
	            // get user details array
	            if($this->session->userdata('is_guest_checkout')!='1' || ($this->session->userdata('UserType') == 'Agent' && $guest_form['consider_guest'] == 'no' && $guest_form['exist_user_id']>0)){
					$logged_in_user = $this->common_model->getSingleRowMultipleWhere('users',array('entity_id'=>$user_id_post));
				}
	            $user_detail = array();
		        if($this->session->userdata('is_guest_checkout')=='1' || ($this->session->userdata('UserType') == 'Agent' && $guest_form['consider_guest'] == 'yes')) {
		        	if($this->input->post('choose_order')!='delivery'){
						$user_detail = array(
			                'first_name'=>$guest_form['first_name'],
			                'last_name'=>$guest_form['last_name'],
			                'phone_code'=>$guest_form['phone_code'],
			                'phone_number'=>$guest_form['login_phone_number'],
			                'email'=>trim($guest_form['email_inp']),
			            );
					} else {
						$user_detail = array(
			                'first_name'=>$guest_form['first_name'],
			                'last_name'=>$guest_form['last_name'],
			                'phone_code'=>$guest_form['phone_code'],
			                'phone_number'=>$guest_form['login_phone_number'],
			                'email'=>trim($guest_form['email_inp']),
			                'address'=> $this->input->post('add_address'),
			                'zipcode'=> $this->input->post('zipcode'),
			                'landmark'=> $this->input->post('landmark'),
			                'address_label'=> $this->input->post('address_label'),
			                'latitude'=> $this->input->post('add_latitude'),
			                'longitude'=> $this->input->post('add_longitude'),
			            );
					}
		        } else {
		            if ($this->input->post('choose_order')=='delivery') {
			            if ($this->input->post('add_new_address') == "add_your_address" && !empty($this->input->post('your_address'))) {
				            $address = $this->checkout_model->getAddress($this->input->post('your_address'));
				            $user_detail = array(
				                'first_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->first_name:$this->session->userdata('userFirstname'),
				                'last_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->last_name:(($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''),
				                'email'=>$logged_in_user->email,
				                'address_id'=>$this->input->post('your_address'),
				                'address'=>($address)?$address->address:'',
				                'landmark'=>($address)?$address->landmark:'',
				                'zipcode'=>($address)?$address->zipcode:'',
				                'city'=>($address)?$address->city:'',
				                'address_label'=>($address)?$address->address_label:'',
				                'latitude'=>($address)?$address->latitude:'',
				                'longitude'=>($address)?$address->longitude:'',
				            );
			            }
			            else if ($this->input->post('add_new_address') == "add_new_address") {
			            	if($user_id_post>0){
				            	$add_address = array(
					                'address'=> $this->input->post('add_address'),
					                'landmark'=> $this->input->post('landmark'),
					                'latitude'=> $this->input->post('add_latitude'),
					                'longitude'=> $this->input->post('add_longitude'),
					                'zipcode'=> $this->input->post('zipcode'),
					                'address_label'=> $this->input->post('address_label'),
					                'user_entity_id'=> ($user_id_post)?$user_id_post:0
					            );
					            $insert_id = $this->common_model->addData('user_address',$add_address);
					            $this->db->set('address_id',$insert_id)->where('entity_id',$order_id)->update('order_master');
					        } 
				            $user_detail = array(
				                'first_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->first_name:$this->session->userdata('userFirstname'),
				                'last_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->last_name:(($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''),
				                'email'=>$logged_in_user->email,
				                'address_id'=>($insert_id)?$insert_id:'',
				                'address'=> $this->input->post('add_address'),
				                'landmark'=> $this->input->post('landmark'),
				                'zipcode'=> $this->input->post('zipcode'),
				                'address_label'=> $this->input->post('address_label'),
				                'latitude'=> $this->input->post('add_latitude'),
				                'longitude'=> $this->input->post('add_longitude'),
				            );
			            }
		            } else {
		            	if (!empty($add_data['address_id'])) {
			            	$address = $this->checkout_model->getAddress($add_data['address_id']);
			            	$user_detail = array(
			            		'first_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->first_name:$this->session->userdata('userFirstname'),
				                'last_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->last_name:(($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''),
				                'email'=>$logged_in_user->email,
				                'address_id'=>$add_data['address_id'],
				                'address'=>($address)?$address->address:'',
				                'landmark'=>($address)?$address->landmark:'',
				                'zipcode'=>($address)?$address->zipcode:'',
				                'city'=>($address)?$address->city:'',
				                'address_label'=>($address)?$address->address_label:'',
				                'latitude'=>($address)?$address->latitude:'',
				                'longitude'=>($address)?$address->longitude:'',
				            );
			            } else {
			            	$user_detail = array(
				                'first_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->first_name:$this->session->userdata('userFirstname'),
				                'last_name'=>($this->session->userdata('UserType') == 'Agent')?$logged_in_user->last_name:(($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''),
				                'email'=>$logged_in_user->email,
					        );
			            }
		            }
		        }
	            // get item details array
	            $add_item = array();
	            if (!empty($cart_details) && !empty($cart_item_details['cart_items'])) {
	            	foreach ($cart_item_details['cart_items'] as $key => $value) {
	            		$menu_content_id = $this->checkout_model->getMenuContentID($value['menu_id']);
						if($menu_content_id->is_combo_item == '1'){
							//$new_item_name = $value['name'].'('.substr(str_replace("\r\n"," + ",$menu_content_id->menu_detail),0,-3).')';
							$new_item_name = $value['name'].'('.str_replace("\r\n"," + ",$menu_content_id->menu_detail).')';
						}else{
							$new_item_name = $value['name'];
						}
	            		if($value['is_customize'] == 1){
	            			$customization = array();
	                        foreach ($value['addons_category_list'] as $k => $val) {
	                            $customization[] = array(
	                                'addons_category_id'=>$val['addons_category_id'],
	                                'addons_category'=>$val['addons_category'],
	                                'addons_list'=>$val['addons_list']
	                            );
	                        }
	                        $add_item[] = array(
	                            "item_name"=>$value['name'],
	                            "menu_content_id"=>$menu_content_id->content_id,
	                            "item_id"=>$value['menu_id'],
	                            "qty_no"=>$value['quantity'],
	                            "comment"=>($value['comment'])?$value['comment']:'',
	                            "rate"=>($value['price'])?$value['price']:'',
	                            "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
	                            "order_id"=>$order_id,
	                            "is_customize"=>1,
	                           	"is_combo_item"=>0,
                                "combo_item_details" => '',
	                            "is_deal"=>$value['is_deal'],
	                            "subTotal"=>$value['subtotal'],
	                            "itemTotal"=>$value['totalPrice'],
	                            "order_flag"=>1,
	                            "addons_category_list"=>$customization
	                        );
	            		}
	            		else
	            		{
							$add_item[] = array(
								"item_name"=>$new_item_name,
								"menu_content_id"=>$menu_content_id->content_id,
								"item_id"=>$value['menu_id'],
								"qty_no"=>$value['quantity'],
								"comment"=>($value['comment'])?$value['comment']:'',
								"rate"=>($value['price'])?$value['price']:'',
								"offer_price"=>($value['offer_price'])?$value['offer_price']:'',
								"order_id"=>$order_id,
								"is_customize"=>0,
								"is_combo_item"=>$menu_content_id->is_combo_item,
								"combo_item_details"=> ($menu_content_id->is_combo_item == '1') ? str_replace("\r\n"," + ",$menu_content_id->menu_detail) : '',
	                            "is_deal"=>$value['is_deal'],
	                            "subTotal"=>$value['subtotal'],
	                            "itemTotal"=>$value['totalPrice'],
	                            "order_flag"=>1
	                        );
	            		}
	            	}
	            }
	        } else {
	        	if($restaurant_detail->restaurant_status != "1" || $restaurant_detail->enable_hours != '1' || $restaurant_detail->timings['off'] == "close" ) {
					$arrdata = array('result'=> 'res_unavailable','show_message'=>$this->lang->line('resto_not_accepting_orders'),'oktxt'=>$this->lang->line('ok'),'order_id'=> '');
				} else if($restaurant_detail->timings['closing'] == "Closed") {
					$arrdata = array('result'=> 'res_unavailable','show_message'=>$this->lang->line('restaurant_closemsg'),'oktxt'=>$this->lang->line('ok'),'order_id'=> '');
				} else {
					$arrdata = array('result'=> 'res_available','show_message'=>'','order_id'=> '');
				}
        		echo json_encode($arrdata); exit;
	        }
    	}
    	$order_detail = array(
            'order_id'=>$order_id,
            'user_name'=>($this->session->userdata('is_guest_checkout')=='1' || ($this->session->userdata('UserType') == 'Agent' && $guest_form['consider_guest'] == 'yes')) ?$guest_form['first_name'].' '.$guest_form['last_name']:$logged_in_user->first_name.' '.$logged_in_user->last_name,
            'user_mobile_number'=>($this->session->userdata('is_guest_checkout')=='1' || ($this->session->userdata('UserType') == 'Agent' && $guest_form['consider_guest'] == 'yes')) ?$guest_form['phone_code'].$guest_form['login_phone_number']: $logged_in_user->phone_code.$logged_in_user->mobile_number,
            'user_detail' => serialize($user_detail),
            'item_detail' => serialize($add_item),
            'restaurant_detail' => serialize($restaurant_detail),
        );
        $this->common_model->addData('order_detail',$order_detail);
        //send order confirmation email-sms to guest
        $language_slug = $this->session->userdata('language_slug');
        $consider_guest = ($guest_form['consider_guest'])?trim($guest_form['consider_guest']):'no';
        
		if($order_id) {
			$encryptval = $this->common_model->base64UrlEncode($order_id);
			if($this->session->userdata('is_guest_checkout')=='1' || ($this->session->userdata('UserType') == 'Agent' && trim($guest_form['consider_guest']) == 'yes')) {
				$to_email = ($guest_form['email_inp']) ? trim($guest_form['email_inp']) : NULL;
				$guest_phncode = ($guest_form['phone_code']) ? $guest_form['phone_code'] : NULL;
        		$guest_phn_no = ($guest_form['login_phone_number']) ? $guest_form['login_phone_number'] : NULL;
				$guest_name = $guest_form['first_name'].' '.$guest_form['last_name'];
				$track_order_link = "<a href='".base_url().'order/guest_track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('click_here') ."</a> ".$this->lang->line('to_track_order');
				$track_order_link_viasms = base_url().'order/guest-track-order/sms/'.$encryptval;
			} else {
				$to_email = ($logged_in_user->email) ? trim($logged_in_user->email) : NULL;
				$guest_phncode = ($logged_in_user->phone_code) ? $logged_in_user->phone_code : NULL;
        		$guest_phn_no = ($logged_in_user->mobile_number) ? $logged_in_user->mobile_number : NULL;
				$guest_name = $logged_in_user->first_name.' '.$logged_in_user->last_name;
				$track_order_link = "<a href='".base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('click_here') ."</a> ".$this->lang->line('to_track_order');
				$track_order_link_viasms = base_url().'order/track-order/sms/'.$encryptval;
			}
			if($to_email) {
				$guest_order_total = ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0.00;
				$order_records_for_invoice = $this->checkout_model->getEditDetail($order_id);
	            $menu_item_for_invoice = $this->checkout_model->getInvoiceMenuItem($order_id);
				$this->common_model->send_email_to_guest($guest_name, $restaurant_detail->name, $order_id, $guest_order_total, $to_email, $language_slug, $order_records_for_invoice, $menu_item_for_invoice, $track_order_link,$_SESSION['timezone_name']);
			}

			if($guest_phncode && $guest_phn_no) {
				$this->common_model->send_sms_to_guest($guest_phncode, $guest_phn_no, $order_id, $restaurant_detail->name,$track_order_link_viasms);
			}
		}
		$verificationCode = random_string('alnum',25);
        $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-receive-alert','language_slug'=>$language_slug,'status'=>1))->first_row();                    
       
        $this->db->select('OptionValue');
        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

        $this->db->select('OptionValue');
        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();  
        if(!empty($email_template)){
            $this->load->library('email');  
            $config['charset'] = 'iso-8859-1';  
            $config['wordwrap'] = TRUE;  
            $config['mailtype'] = 'html';  
            $this->email->initialize($config);  
            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
            $this->email->to(trim($restaurant_detail->email)); 
            $this->email->subject($email_template->subject);  
            $this->email->message($email_template->message);  
            $this->email->send();
        }
        if ($order_id) {
			$this->session->unset_userdata('checkDelivery');
			$this->session->unset_userdata('deliveryCharge');
			$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));			
			$this->session->unset_userdata('coupon_array');
			$this->session->unset_userdata('is_redeem');
			$this->session->set_userdata('tip_amount', 0);
			$this->session->unset_userdata('tip_percent_val');
			$this->session->set_userdata(array('order_id' => $order_id));
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
    		if($this->session->userdata('is_guest_checkout')=='1') {
    			$order_id = "<a href = '".base_url()."' class = 'btn' >".$this->lang->line('continue')." ".$this->lang->line('ordering')."</a><a href='".base_url().'order/guest_track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('track_order') ."</a>";
    			//$order_id .= "<a href = '".base_url()."' class = 'btn' >".$this->lang->line('continue')." ".$this->lang->line('ordering')."</a>";
    		} else {
    			$order_id = "<a href='".base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('track_order') ."</a>";
    			$order_id .= "<a href = '". base_url() .'myprofile' . "' class = 'btn' >" . $this->lang->line('view_details') ."</a>";
    		}
        	$earned_pointsmsg = '';
        	if($earned_points && $earned_points>0)
			{
				$default_currency = get_default_system_currency();
				if(empty($default_currency)){
					$res_currency = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
					$default_currency = $res_currency->currency_symbol;
				} else {
					$default_currency = $default_currency->currency_symbol;
				}
				$earned_pointsmsg = ($this->session->userdata('is_guest_checkout')!='1')?"<p>".$this->lang->line('points_earned_from_order').": ".currency_symboldisplay($earned_points,$default_currency)."</p>":'';
			}
        	$this->session->set_userdata('is_guest_checkout',0);
        	$arrdata = array(
        		'result' => 'success',
        		'order_id' => $order_id,
        		'earned_points' => $earned_pointsmsg,
        		'payment_option' => $this->input->post('payment_option'),
        		'payment_status'=>'',
        		'paypal_btn' => ''
        	);

            if($this->input->post('payment_option') == 'stripe') {
                $update_data = array(              
                    'order_status' =>'placed',
                    'transaction_id'=>$this->input->post('paymentIntentId')
                );
                $this->common_model->updateData('order_master',$update_data,'entity_id',$this->session->userdata('order_id'));
                //update transaction id on tips table
                $update_transactionid_tip = array ('tips_transaction_id' => $this->input->post('paymentIntentId'),'payment_option' => $this->input->post('payment_option'));
                $this->common_model->updateData('tips',$update_transactionid_tip,'entity_id',$tips_id);

                $arrdata = array('result'=> 'success','order_id'=> $order_id, 'payment_status'=>$this->input->post('paymentStatus'), 'earned_points' => $earned_pointsmsg, 'payment_option'=>$this->input->post('payment_option'), 'paypal_btn'=>'');
                
            }else if ($this->input->post('payment_option') == 'paypal') { 
            	$update_data = array(              
                    'order_status' =>'placed',
                    'transaction_id'=>$this->input->post('paypal_transaction_id')
                );
                $this->common_model->updateData('order_master',$update_data,'entity_id',$this->session->userdata('order_id'));
                //update transaction id on tips table
                $update_transactionid_tip = array ('tips_transaction_id' => $this->input->post('paypal_transaction_id'),'payment_option' => $this->input->post('payment_option'));
                $this->common_model->updateData('tips',$update_transactionid_tip,'entity_id',$tips_id);

                $arrdata = array('result'=> 'success','order_id'=> $order_id, 'earned_points' => $earned_pointsmsg, 'payment_status'=>$this->input->post('paymentStatus'), 'payment_option'=>$this->input->post('payment_option'), 'paypal_btn'=>'');
            }else{
                $update_data = array(              
                    'order_status' =>'placed',
                );
                $this->common_model->updateData('order_master',$update_data,'entity_id',$this->session->userdata('order_id'));
            }
        	//Code for send the notification to the Branch admin :: Start :: 12-10-2020
	        $restuser_device = $this->checkout_model->getBranchAdminDevice($cart_restaurant);
	        if($restuser_device && trim($restuser_device->device_id)!='' && $restuser_device->notification == 1)
	        {
	            $branch_device_id = $restuser_device->device_id;
	            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$restuser_device->language_slug))->first_row();
	            $this->lang->load('messages_lang', $languages->language_directory);
	            #prep the bundle
	            $fields = array();            
	            $message = sprintf($this->lang->line('push_new_order'),$this->session->userdata('order_id'));
	           
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
	        //Code for send the notification to the Branch admin :: End :: 12-10-2020
	        //Code for send the notification to the Restaurant admin :: Start
            $restadmin_device = $this->checkout_model->getRestaurantAdminDevice($cart_restaurant);
            if($restadmin_device && trim($restadmin_device->device_id)!='' && $restadmin_device->notification == 1 && $restadmin_device->status == 1)
            {
                $languages_resadmin = $this->db->select('*')->get_where('languages',array('language_slug'=>$restadmin_device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages_resadmin->language_directory);
                #prep the bundle
                $fields = array();            
                $message = sprintf($this->lang->line('push_new_order'),$this->session->userdata('order_id'));
                
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
            //Code for send the notification to the Restaurant admin :: End
            //Code for send the notification to the Customer when order placed by agent :: Start
            if(!empty($logged_in_user) && $logged_in_user->device_id && $this->session->userdata('UserType') == 'Agent'){
                #prep the bundle
                $fields = array();            
                $message = sprintf($this->lang->line('push_agent_order_placed'),$this->session->userdata('order_id'));
                $fields['to'] = $logged_in_user->device_id; // only one user to send push notification
                $fields['notification'] = array ('body'  => $message,'sound'=>'default','title'=>$this->lang->line('customer_app_name'));
                $fields['data'] = array ('screenType'=>'order','title'=>$this->lang->line('customer_app_name'),'body'  => $message,'order_id'=>$this->session->userdata('order_id'));
                
                $headers = array (
                    'Authorization: key=' . FCM_KEY,
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
            //Code for send the notification to the Customer when order placed by agent :: End
        }
        else
        {
        	$arrdata = array('result'=> 'fail','order_id'=> '','earned_points'=>'','payment_status'=>'','payment_option'=>$this->input->post('payment_option'),'paypal_btn'=>'');
        }
        echo json_encode($arrdata);	
    }
    public function redeemPoints() {
    	$this->session->unset_userdata('is_redeem');
    	$this->session->unset_userdata('redeem_submit');
    	$this->session->unset_userdata('redeem_amount');

    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		
		$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		$earning_points = $data['earning_points']->wallet;
		$minimum_redeem_point = $this->db->get_where('system_option',array('OptionSlug'=>'min_redeem_point'))->first_row();
		$min_redeem_point_alert = '';
		if($this->input->post('redeem') == $this->lang->line('redeem')) {
			if($earning_points>=$minimum_redeem_point->OptionValue){
				$is_redeem = 1;
				$redeem = $this->lang->line("cancel_redeem");
				if($earning_points <= $this->input->post('temp_total')) {
	                $redeem_amount = $earning_points;
	            } else {
	                $redeem_amount = $this->input->post('temp_total');
	            }
	        } else {
	        	$is_redeem = 0;
	        	$redeem = $this->lang->line("redeem");
				$redeem_amount = 0;
				$min_amt = $data['currency_symbol']->currency_symbol.$minimum_redeem_point->OptionValue;
				$min_redeem_point_alert = sprintf($this->lang->line('wallet_greater_than_minredeempoint'),$min_amt);
	        }
        } else {
			$is_redeem = 0;
			$redeem = $this->lang->line("redeem");
			$redeem_amount = 0;
		}
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
		}
		$this->session->set_userdata('is_redeem',$is_redeem);
    	$this->session->set_userdata('redeem_submit',$redeem);
    	$this->session->set_userdata('redeem_amount',$redeem_amount);
    	/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
		
		$redeem_data = array('ajax_order_summary'=>$order_summary,'redeem_amount'=>$redeem_amount,'redeem_submit'=> $redeem,'min_redeem_point_alert'=>$min_redeem_point_alert,'oktxt'=>$this->lang->line("ok"));
		echo json_encode($redeem_data);
    }

	public function process(){
		$redirectStr = ''; 
		if(!empty($_GET['paymentID']) && !empty($_GET['token']) && !empty($_GET['payerID']) && !empty($_GET['pid']) ){ 
			// Include and initialize paypal class 
			require APPPATH . 'libraries/PaypalExpress.php';
			$paypal = new PaypalExpress; 
			// Get payment info from URL 
			$paymentID = $_GET['paymentID'];
			$token = $_GET['token'];
			$payerID = $_GET['payerID'];
			$productID = $_GET['pid'];
			// Validate transaction via PayPal API 
			$paymentCheck = $paypal->validate($paymentID, $token, $payerID, $productID);

			// If the payment is valid and approved 
			if($paymentCheck && $paymentCheck->state == 'approved'){
				// Get the transaction data 
				$transaction_id = $paymentCheck->transactions[0]->related_resources[0]->sale->id;
				$id = ($transaction_id && $transaction_id!='')?$transaction_id:$paymentCheck->id;  //transaction id
				$state = $paymentCheck->state; // transaction status
				$payerFirstName = $paymentCheck->payer->payer_info->first_name; 
				$payerLastName = $paymentCheck->payer->payer_info->last_name; 
				$payerName = $payerFirstName.' '.$payerLastName; 
				$payerEmail = $paymentCheck->payer->payer_info->email; 
				$payerID = $paymentCheck->payer->payer_info->payer_id; 
				$payerCountryCode = $paymentCheck->payer->payer_info->country_code; 
				$paidAmount = $paymentCheck->transactions[0]->amount->details->subtotal; 
				$currency = $paymentCheck->transactions[0]->amount->currency; 

				// Get product details 
				$orderDetail = $this->checkout_model->getOrderRecords($this->session->userdata('order_id')); 
				// If payment price is valid 
				//if($orderDetail->total_rate >= $paidAmount){  //uncomment this on live

					// Insert transaction data in the database 
					/*$data = array( 
					'product_id' => $productID, 
					'transaction_id' => $id, 
					'payment_gross' => $paidAmount, 
					'currency_code' => $currency, 
					'payer_id' => $payerID, 
					'payer_name' => $payerName, 
					'payer_email' => $payerEmail, 
					'payer_country' => $payerCountryCode, 
					'payment_status' => $state 
				);*/
				$data = array( 
					// 'transaction_id' => $id,  
					'transaction_id' => $id,  
					'order_status' =>'placed',
				);
				echo json_encode($data);
				/*$this->common_model->updateData('order_master',$data,'entity_id',$this->session->userdata('order_id'));
				//} 
				$redirectStr = '?order_id='.$this->session->userdata('order_id').'&txn_id='.$id; 
				// Redirect after payment successful 
				redirect(base_url().'myprofile'.$redirectStr);*/
			}else{
				$redirectStr = '?order_id='.$this->session->userdata('order_id');
				$data = array( 
					'transaction_id' => $id,  
					'order_status' =>'unpaid',
				);
				$this->common_model->updateData('order_master',$data,'entity_id',$this->session->userdata('order_id'));
				// Redirect after payment failed 
				redirect(base_url().'myprofile'.$redirectStr);
			}
		}else{
			$redirectStr = '?order_id='.$this->session->userdata('order_id');
			// Redirect after payment failed/cancelled
			redirect(base_url().'checkout'.$redirectStr);
		}
	}

	//Code for save the stripe card :: Start
	public function save_carddetail()
    {
    	$stripe_info = stripe_details();
    	// Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
    	$stripe = new \Stripe\StripeClient($stripe_api_key);

    	$json_str = file_get_contents('php://input');
		/*getting value from session*/
		$json_obj = json_decode($json_str);
		$payment_method_id = $json_obj->payment_method;
		$stripecus_id = $json_obj->stripecus_id;
		//new tweaks :: start
		try {
			//get card fingerprint (to check if card already saved)
			$get_fingerprint = $stripe->paymentMethods->retrieve(
				$payment_method_id,
				[]
			);
			$pay_method_fingerprint = $get_fingerprint->card->fingerprint;
			try {
	            //check if card already saved
	            $all_card_details = $stripe->paymentMethods->all([
	                'customer' => $stripecus_id,
	                'type' => 'card',
	            ]);
	            $existing_fingerprint = array();
	            foreach ($all_card_details->data as $cards_key => $cards_value) {
	                array_push($existing_fingerprint, $cards_value->card->fingerprint);
	            }
	            //if yes, then don't save again
                if($pay_method_fingerprint != '' && in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                    //card already saved.
                    echo json_encode(['error' => $this->lang->line('card_already_saved')]);
                } else { //if no, then save card
					try {
						$attach_card = $stripe->paymentMethods->attach(
							$payment_method_id,
							['customer' => $stripecus_id]
						);
						http_response_code(200);
						echo json_encode($attach_card);
					} catch (Exception $e) {
						echo json_encode(['error' => $e->getMessage()]);
					}
                }
	        } catch (Exception $e) {  // list all cards errors
				echo json_encode(['error' => $e->getMessage()]);
			}
		} catch (Exception $e) { //get payment method errors
			echo json_encode(['error' => $e->getMessage()]);
		}
		//new tweaks :: end
    }
    //Code for save the stripe card :: End
    //Code for create intent with card:: Start
    public function create_paymentwithcard()
    {
    	$total_price = ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0;
    	$stripe_info = stripe_details();
    	// Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;

        // retrieve JSON from POST body
        $json_str = file_get_contents('php://input');
		/*getting value from session*/
		$json_obj = json_decode($json_str);
		$stripecus_id = $json_obj->stripecus_id;
		$payment_methodid = $json_obj->payment_method;

        // Set API key 
        \Stripe\Stripe::setApiKey($stripe_api_key);
    	header('Content-Type: application/json');
		try {
			$paymentIntent = \Stripe\PaymentIntent::create([
				'customer' => $stripecus_id,
				'payment_method_types' => ['card'],
				'payment_method' => $payment_methodid,
				'amount' => $total_price * 100,				
				'currency' => $this->session->userdata('payment_currency'),				
			]);

			$paymentconfirm = \Stripe\PaymentIntent::retrieve($paymentIntent->id);
			$paymentconfirm->confirm();
			$output = [
				'clientSecret' => $paymentIntent->client_secret,
				'stripecus_id' => $stripecus_id,
				'paymentIntentid' => $paymentIntent->id,
				'paymentIntentstatus' => 'succeeded',
				'paymentconfirm_status' => $paymentconfirm->status,
				'total_price' => $total_price,
			];
			echo json_encode($output);
		} catch (Exception $e) {
		  //http_response_code(500);
		  echo json_encode(['error' => $e->getMessage()]);
		}		
    }
    //Code for create intent with card :: End

    //Code for delete the stripe card :: Start
    public function removeStripeCard()
    {
    	$PaymentMethodid = ($this->input->post('PaymentMethodid'))?$this->input->post('PaymentMethodid'):'';
    	$stripecus_id = ($this->input->post('stripecus_id'))?$this->input->post('stripecus_id'):'';
    	$stripe_html = '';
    	if($PaymentMethodid!='' && $stripecus_id!='')
    	{
    		$stripe_info = stripe_details();
	    	// Include the Stripe PHP bindings library 
	        require APPPATH .'third_party/stripe-php/init.php';
	        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
	        $stripe = new \Stripe\StripeClient($stripe_api_key);

    		try{
                $detach_card = $stripe->paymentMethods->detach(
                $PaymentMethodid,
                  []
                );
                if(!empty($detach_card) && $detach_card->id != '')
                {
                   try {
						//check if card already saved
						$all_card_details = $stripe->paymentMethods->all([
						    'customer' => $stripecus_id,
						    'type' => 'card',
						]);
						
						if($all_card_details && !empty($all_card_details))
						{
							$all_card_detailaarr = $all_card_details->data;
							for($strid=0;$strid<count($all_card_detailaarr);$strid++)
							{
								$PaymentMethodid = $all_card_detailaarr[$strid]->id;
								$card_brand = $all_card_detailaarr[$strid]->card->brand;
								$card_last4 = $all_card_detailaarr[$strid]->card->last4;
								$card_number = "************".$card_last4;
								$card_fingerprint = $all_card_detailaarr[$strid]->card->fingerprint;
								$exp_month = $all_card_detailaarr[$strid]->card->exp_month;
	                        	$exp_year = $all_card_detailaarr[$strid]->card->exp_year;

								$card_image = '';
		                        if($card_brand =='unionpay')
		                        {
		                            $card_image = 'assets/front/images/cardimg/unionpay.jpg';
		                        }
		                        else if($card_brand =='amex')
		                        {
		                            $card_image = 'assets/front/images/cardimg/american_express.jpg';
		                        }
		                        if($card_brand =='jcb')
		                        {
		                            $card_image = 'assets/front/images/cardimg/jcb.jpg';
		                        }
		                        if($card_brand =='diners')
		                        {
		                            $card_image = 'assets/front/images/cardimg/diners_club.jpg';
		                        }
		                        if($card_brand =='discover')
		                        {
		                            $card_image = 'assets/front/images/cardimg/discover.jpg';
		                        }
		                        if($card_brand =='mastercard')
		                        {
		                            $card_image = 'assets/front/images/cardimg/mastercard.jpg';
		                        }
		                        if($card_brand =='visa')
		                        {
		                            $card_image = 'assets/front/images/cardimg/visa.jpg';
		                        }
		                        $card_brand_name = '';
		                        if($card_brand == 'amex' || $card_brand == 'mastercard' || $card_brand == 'visa' || $card_brand == 'discover' || $card_brand == 'diners' || $card_brand == 'jcb' || $card_brand == 'unionpay'){
		                            $card_brand_name = $this->lang->line($card_brand);
		                        } else {
		                            $card_brand_name = ucfirst($card_brand);
		                        }

								$checkedval = '';
								if($strid==0){
									$checkedval = 'checked="checked"';
								}
								$stripe_html .='<div class="payment_card_box">
		                            <div class="radio-btn-list">
		                                <label class="payment_label">
		                                    <input type="radio" name="payment-source" value="saved_card_'.($strid+1).'" card_fingerprint="'.$card_fingerprint.'" PaymentMethodid="'.$PaymentMethodid.'" '.$checkedval.' onclick="togglecardbutton(this.value);">
		                                    <span></span>
		                                    <div class="card_image"><img src="'.base_url().$card_image.'" width="60"></div>
		                                    <div class="card-name-no">
		                                        <h5 class="card_name">'.$value['card_brand_name'].'</h5>
		                                        <h6 class="saved-card">'.$this->lang->line('ending_in').$card_last4.', '.$this->lang->line('expires').$exp_month.'/'.$exp_year.'</h6>
		                                    </div>
		                                    
		                                </label>
		                            </div>
		                        </div>';
	                            /*<button type="button" class="btn remove-card" alt="remove-card" title="remove-card"
						        	onclick="removeStripeCard(\''.$PaymentMethodid.'\',\''.$stripecus_id.'\');"><i class="iicon-icon-23"></i></button>*/
							}
						}
						$output = [					
							'stripe_html' => $stripe_html,
							'is_delete' => 'yes',
						];
						echo json_encode($output);
					} catch (Exception $e) {
						//http_response_code(500);
						echo json_encode(['error' => $e->getMessage(),'is_delete' => 'no']);
					}
                }
            } catch (Exception $e) {
			  //http_response_code(500);
			  echo json_encode(['error' => $e->getMessage(),'is_delete' => 'no']);
			}
    	}
    }
    //Code for delete the stripe card :: End

    //Code for create intent :: Start
    public function create_intent() {
    	$output = array();
    	$total_price = ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0;
    	//$json_str = file_get_contents('php://input');
    	$stripe_info = stripe_details();
    	// Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;

    	$is_guest_checkout=(($this->session->userdata('is_guest_checkout') && $this->session->userdata('is_guest_checkout')=='1') || $this->session->userdata('UserType') == 'Agent') ? 'yes' : 'no';
    	if($is_guest_checkout=='yes') {
    		//Code for create payment intent :: Start
	        // Set API key 
	        \Stripe\Stripe::setApiKey($stripe_api_key);
	    	header('Content-Type: application/json');
			try {
				$paymentIntent = \Stripe\PaymentIntent::create([
					/*'amount' => $json_obj->amount,
					'currency' => $json_obj->currency*/					
					'amount' => $total_price * 100,
					'currency' => $this->session->userdata('payment_currency'),					
				]);
				$output = [
					'clientSecret' => $paymentIntent->client_secret,
					'stripecus_id' => '',
					'stripe_html' => '',
					'is_savecard' => 'no',
				];
			} catch (Exception $e) {
				//http_response_code(500);
				$output = array('error' => $e->getMessage());
				//echo json_encode(['error' => $e->getMessage()]);
			}
			//Code for create payment intent :: End
    	} else {
    		//Code for create customer :: Start
	        $stripe = new \Stripe\StripeClient($stripe_api_key);

	        $stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
	        $stripecus_id = '';
	        if($stripecus_arr && !empty($stripecus_arr)) {
	        	$stripecus_id = $stripecus_arr->stripe_customer_id;
	        }
	        if($stripecus_id=='') {
	        	$stripe_username = ($this->session->userdata('userFirstname') != '' && $this->session->userdata('userLastname') != '')?$this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'):$this->session->userdata('userFirstname');
				$user_phn = ($this->session->userdata('userPhone_code') != '' && $this->session->userdata('userPhone') != '')?$this->session->userdata('userPhone_code').$this->session->userdata('userPhone'):$this->session->userdata('userPhone');
				try{
					//create customer :: start
					$CreateCustomer = $stripe->customers->create([
						'name' => $stripe_username,
						'email' => ($this->session->userdata('userEmail'))?$this->session->userdata('userEmail'):'',
						'phone' => $user_phn
					]);
					//Code for store the sustomer id in database
					if(!empty($CreateCustomer) && $CreateCustomer->id){
						$stripecus_id = $CreateCustomer->id;                    
						$this->common_model->updateData('users',array('stripe_customer_id'=>$stripecus_id),'entity_id',$this->session->userdata('UserID'));
					}
				}catch (Exception $e) { //for create customer errors
					//http_response_code(500);
					$output = array('error' => $e->getMessage());
					//echo json_encode(['error' => $e->getMessage()]);
				}
	        }
	        //Code for create customer :: End

	        //Code for card list :: Start
	        $stripe_html = '';
	        if($stripecus_id) {
				$default_payment_method = NULL;
				try {
					//get default payment method
					$customer_obj = $stripe->customers->retrieve(
						$stripecus_id,
						[]
					);
					$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
				} catch (Exception $e) {
					//error while retrieving customer
				}
				try {
					//check if card already saved
					$all_card_details = $stripe->paymentMethods->all([
					    'customer' => $stripecus_id,
					    'type' => 'card',
					]);
					if($all_card_details && !empty($all_card_details)) {
						if(!$default_payment_method) {
							//set recent card as default
							$this->common_model->set_default_card($stripe, $all_card_details->data[0]->id, $stripecus_id);
							//get default payment method
							try {
								$customer_obj = $stripe->customers->retrieve(
									$stripecus_id,
									[]
								);
								$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
							} catch (Exception $e) {
								//error while retrieving customer
							}
						}
						//sort payment method list :: default payment method on top
						if($default_payment_method) {
							if(in_array($default_payment_method, array_column($all_card_details->data, 'id'))) {
								usort($all_card_details->data,function($a,$b) use ($default_payment_method) {
									if ($a->id != $default_payment_method && $b->id == $default_payment_method) {
										return 1;
									} elseif ($a->id == $default_payment_method && $b->id != $default_payment_method) {
										return -1;
									} else {
										return 0;
									}
								});
							}
						}
						//add new key is_default_card in all payment method object
						foreach ($all_card_details->data as $allmethods_key => $allmethods_value) {
							if($allmethods_value->id == $default_payment_method) {
								$all_card_details->data[$allmethods_key]->is_default_card = '1';
							} else {
								$all_card_details->data[$allmethods_key]->is_default_card = '0';
							}
						}
						$all_card_detailaarr = $all_card_details->data;
						for($strid = 0; $strid < count($all_card_detailaarr); $strid++) {
							$PaymentMethodid = $all_card_detailaarr[$strid]->id;
							$card_brand = $all_card_detailaarr[$strid]->card->brand;
							$card_last4 = $all_card_detailaarr[$strid]->card->last4;
							$card_number = "************".$card_last4;
							$card_fingerprint = $all_card_detailaarr[$strid]->card->fingerprint;
							$exp_month = $all_card_detailaarr[$strid]->card->exp_month;
	                    	$exp_year = $all_card_detailaarr[$strid]->card->exp_year;
							$is_default_card = $all_card_detailaarr[$strid]->is_default_card;

							$card_image = '';
	                        if($card_brand =='unionpay')
	                        {
	                            $card_image = 'assets/front/images/cardimg/unionpay.jpg';
	                        }
	                        else if($card_brand =='amex')
	                        {
	                            $card_image = 'assets/front/images/cardimg/american_express.jpg';
	                        }
	                        if($card_brand =='jcb')
	                        {
	                            $card_image = 'assets/front/images/cardimg/jcb.jpg';
	                        }
	                        if($card_brand =='diners')
	                        {
	                            $card_image = 'assets/front/images/cardimg/diners_club.jpg';
	                        }
	                        if($card_brand =='discover')
	                        {
	                            $card_image = 'assets/front/images/cardimg/discover.jpg';
	                        }
	                        if($card_brand =='mastercard')
	                        {
	                            $card_image = 'assets/front/images/cardimg/mastercard.jpg';
	                        }
	                        if($card_brand =='visa')
	                        {
	                            $card_image = 'assets/front/images/cardimg/visa.jpg';
	                        }
	                        $card_brand_name = '';
	                        if($card_brand == 'amex' || $card_brand == 'mastercard' || $card_brand == 'visa' || $card_brand == 'discover' || $card_brand == 'diners' || $card_brand == 'jcb' || $card_brand == 'unionpay'){
	                            $card_brand_name = $this->lang->line($card_brand);
	                        } else {
	                            $card_brand_name = ucfirst($card_brand);
	                        }

							$checkedval = '';
							if($strid == 0) {
								$checkedval = 'checked="checked"';
							}
							$default_card_class = ($is_default_card == '1') ? 'default-stripe-card' : '' ;
							$stripe_html .='<div class="payment_card_box '.$default_card_class.'">
	                            <div class="radio-btn-list">
	                                <label class="payment_label">
	                                    <input type="radio" name="payment-source" value="saved_card_'.($strid+1).'" card_fingerprint="'.$card_fingerprint.'" PaymentMethodid="'.$PaymentMethodid.'" '.$checkedval.' onclick="togglecardbutton(this.value);">
	                                    <span></span>
	                                    <div class="card_image"><img src="'.base_url().$card_image.'" width="60"></div>
	                                    <div class="card-name-no">
	                                        <h5 class="card_name">'.$value['card_brand_name'].'</h5>
	                                        <h6 class="saved-card">'.$this->lang->line('ending_in').$card_last4.', '.$this->lang->line('expires').$exp_month.'/'.$exp_year.'</h6>
	                                    </div>
	                                    
	                                </label>
	                            </div>
	                        </div>';
						}
					}				
				
				} catch (Exception $e) {
					//http_response_code(500);
					$output = array('error' => $e->getMessage());
					//echo json_encode(['error' => $e->getMessage()]);
				}
	        }
	        //Code for card list :: End

	        //Code for create payment intent :: Start
	        // Set API key 
	        \Stripe\Stripe::setApiKey($stripe_api_key);
	    	header('Content-Type: application/json');
			try {
				$paymentIntent = \Stripe\PaymentIntent::create([
					/*'amount' => $json_obj->amount,
					'currency' => $json_obj->currency*/
					'setup_future_usage'=> 'off_session',
					//'customer' => $stripecus_id,
					'amount' => $total_price * 100,
					'currency' => $this->session->userdata('payment_currency'),
					/*'automatic_payment_methods' => [
						'enabled' => 'true',
					],*/ //if required open this code
				]);
				$output = [
					'clientSecret' => $paymentIntent->client_secret,
					'stripecus_id' => $stripecus_id,
					'stripe_html' => $stripe_html,
					'is_savecard' => 'yes',
					'total_price' => $total_price,
					'trans_id' => $paymentIntent->id,
				];
			} catch (Exception $e) {
				//http_response_code(500);
				$output = array('error' => $e->getMessage());
				//echo json_encode(['error' => $e->getMessage()]);
			}
			//Code for create payment intent :: End
    	}
    	echo json_encode($output);
    }
    //Code for create intent :: End
    //driver tip changes :: start
    public function applyTip(){
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		
		$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		$earning_points = $data['earning_points']->wallet;

		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
		}
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if($this->input->post("action")=='apply'){
			$driver_tip = ($this->input->post("tip_amount"))?(float)$this->input->post("tip_amount"):0;
			$this->session->set_userdata('tip_amount',$driver_tip);
		} else {
			$driver_tip = 0;
			$this->session->set_userdata('tip_amount', $driver_tip);
		}
		if($this->input->post("action")=='apply' && $this->input->post("tip_percent_val")) {
			$this->session->set_userdata('tip_percent_val', (float)$this->input->post("tip_percent_val"));
		} else {
			$this->session->unset_userdata('tip_percent_val');
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$this->load->view('ajax_order_summary',$data);
    }
    //driver tip changes :: end
    //delivery zone issue :: start
    //test function to check a point in polygon
    public function checktestFence($latlongs, $coordinatesArr, $price_charge, $additional_delivery_charge){
        $oddNodes = '';
        $inside_polygon = $this->inside_polygon($latlongs, $coordinatesArr); //check 4 :: ray casting 
        if($inside_polygon == 1) {
            //check 3 :: start
            $polygon = $coordinatesArr;
            if($polygon[0] != $polygon[count($polygon)-1])
                $polygon[count($polygon)] = $polygon[0];
            
            $j = $i = 0;
            $x = $latlongs[1];
            $y = $latlongs[0];
            $n = count($polygon);
            
            for ($i = 0; $i < $n; $i++)
            {
                $j++;
                if ($j == $n)
                {
                    $j = 0;
                }
                if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
                    $y)))
                {
                    if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
                        $polygon[$i][1]) < $x)
                    {
                        $oddNodes = 'true';
                    }
                }
            }
            $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
            $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
            return $price_arr;
            //check 3 :: end
        }
        $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
        $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
        return $price_arr;
    }
    //ray casting algo :: to check if point is inside polygon (check 4)
    function inside_polygon($test_point, $points) {
        $p0 = end($points);
        $ctr = 0;
        foreach ( $points as $p1 ) {
    
            // there is a bug with this algorithm, when a point in "on" a vertex
            // in that case just add an epsilon
            if ($test_point[1] == $p0[1])
                $test_point[1]+=0.0000000001; #epsilon
        
            // ignore edges of constant latitude (yes, this is correct!)
            if ( $p0[1] != $p1[1] ) {
                // scale latitude of $test_point so that $p0 maps to 0 and $p1 to 1:
                $interp = ($test_point[1] - $p0[1]) / ($p1[1] - $p0[1]);
        
                // does the edge intersect the latitude of $test_point?
                // (note: use >= and < to avoid double-counting exact endpoint hits)
                if ( $interp >= 0 && $interp < 1 ) {
                    // longitude of the edge at the latitude of the test point:
                    // (could use fancy spherical interpolation here, but for small
                    // regions linear interpolation should be fine)
                    $long = $interp * $p1[0] + (1 - $interp) * $p0[0];
                    // is the intersection east of the test point?
                    if ( $long > $test_point[0] ) {
                        // if so, count it:
                        $ctr++;
        #echo "YES &$test_point[0],$test_point[1] ($p0[0],$p0[1])x($p1[0],$p1[1]) ; $interp,$long","\n";
                    }
                }
            }
            $p0 = $p1;
        }
        return ($ctr & 1);
    }
    //delivery zone issue :: end
    //guest checkout changes :: start
    public function checkout_as_guest() {
    	$this->session->set_userdata('is_guest_checkout',1);

    	$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_checkout'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_data'] = $this->common_model->checkResForCart($cart_restaurant);
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['restaurant_order_mode'] = $this->common_model->get_restaurant_order_mode($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$data['is_out_of_stock_item_in_cart'] = $data['cart_details']['is_out_of_stock_item_in_cart'];
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}

		//menu suggestion :: start
		$cart_items_array = array();
		foreach ($data['cart_details']['cart_items'] as $cartkey => $cartvalue) {
			array_push($cart_items_array, $cartvalue['menu_content_id']);
		} 
		$data['menu_item_suggestion'] = (is_numeric($cart_restaurant))?$this->checkout_model->getMenuSuggestionItems($cart_restaurant,$this->session->userdata('language_slug'),$cart_items_array):array();;
		//menu suggestion :: end
        //payment method :: start
        $data['paymentmethods'] = (is_numeric($cart_restaurant))?$this->checkout_model->getPaymentMethodSuggestion($cart_restaurant,$this->session->userdata('language_slug')):array();
		$lang_slug = $this->session->userdata('language_slug');
		foreach ($data['paymentmethods'] as $key => $value)
        {
			if($lang_slug == 'en'){
				$value->payment_name = $value->display_name_en;
			}
			if($lang_slug == 'fr'){
				$value->payment_name = $value->display_name_fr;
			}
			if($lang_slug == 'ar'){
				$value->payment_name = $value->display_name_ar;
			}
			if($value->payment_gateway_slug == 'cod'){
				unset($data['paymentmethods'][$key]);
			}
			if($value->payment_gateway_slug == 'applepay') {
				unset($data['paymentmethods'][$key]);
			}
        }
        /*sales tax changes start*/
		$data['sales_tax'] = (is_numeric($cart_restaurant))?$this->checkout_model->getRestaurantTax($cart_restaurant):array();
		$data['enabled_date_timeslots'] = (is_numeric($cart_restaurant))?$this->common_model->getDateAndTimeSlotsForScheduling($cart_restaurant,'','','','web',$data['cart_details']['is_out_of_stock_item_in_cart']):array();
		/*sales tax changes end*/
		$this->session->set_userdata(array('checkDelivery' => 'pickup','deliveryCharge' => 0));
		//Code for user detail :: End
		$data['google_login_url'] = $this->google->loginURL();
		$data['authURL'] =  $this->facebook->login_url();
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$this->load->view('checkout',$data);
    }
    //guest checkout changes :: end

    public function check_user_email(){
		$count = $this->db->where(array('email' => $this->input->post('user_email'),'user_type' => 'User'))->get('users')->num_rows();
		echo ($count > 0) ? $this->lang->line('guest_checkout_email_alredy_exist') : "true" ;
	}

	public function check_user_phone(){
		$count = $this->db->where(array('phone_code' => $this->input->post('user_phone_code'),'mobile_number' => $this->input->post('user_phone_number'),'user_type' => 'User'))->get('users')->num_rows();
		echo ($count > 0) ? $this->lang->line('guest_checkout_phone_alredy_exist') : "true" ;

	}
	public function checkNumberExistforAgent(){
		$address_flag = 0;
		if($this->input->post('phn_code') != '' && $this->input->post('phone_no') != '') {
			$count = $this->db->where(array('phone_code' => $this->input->post('phn_code'),'mobile_number' => $this->input->post('phone_no'),'user_type' => 'User'))->get('users')->num_rows();
			if($count>0){
				$wherearray = array('phone_code' => $this->input->post('phn_code'),'mobile_number' => $this->input->post('phone_no'),'user_type' => 'User');
				$user_record = $this->common_model->getSingleRowMultipleWhere('users',$wherearray);
				$address = $this->checkout_model->getUsersAddress($user_record->entity_id);
				$address_html = '<option value="">'. $this->lang->line("select").'</option>';
				if(!empty($address)) {
					$address_flag = 1;
					foreach ($address as $key => $value) {
						$address_html .= '<option value="'.$value['entity_id'].'">'.$value['address'].','.$value['landmark'].','.$value['zipcode'].','.$value['city'].'</option>';
					}
				}
				$arr = array('count'=> $count, 'first_name'=>$user_record->first_name, 'last_name'=>$user_record->last_name, 'email'=>$user_record->email, 'mobile_number'=>$user_record->mobile_number, 'user_id'=>$user_record->entity_id, 'flag_email_phn'=>'phone', 'is_guest'=>'no', 'address_html'=>$address_html, 'address_flag'=>$address_flag);
			} else {
				$arr = array('count'=> $count, 'flag_email_phn'=>'phone', 'is_guest'=>'yes');
			}
		} elseif($this->input->post('user_email') != '') {
			$count = $this->db->where(array('email' => $this->input->post('user_email'),'user_type' => 'User'))->get('users')->num_rows();
			if($count>0){
				$wherearray = array('email' => $this->input->post('user_email'),'user_type' => 'User');
				$user_record = $this->common_model->getSingleRowMultipleWhere('users',$wherearray);
				$address = $this->checkout_model->getUsersAddress($user_record->entity_id);
				$address_html = '<option value="">'. $this->lang->line("select").'</option>';
				if(!empty($address)) {
					$address_flag = 1;
					foreach ($address as $key => $value) {
						$address_html .= '<option value="'.$value['entity_id'].'">'.$value['address'].','.$value['landmark'].','.$value['zipcode'].','.$value['city'].'</option>';
					}
				}
				$arr = array('count'=> $count, 'first_name'=>$user_record->first_name, 'last_name'=>$user_record->last_name, 'email'=>$user_record->email, 'mobile_number'=>$user_record->mobile_number, 'user_id'=>$user_record->entity_id, 'flag_email_phn'=>'email', 'is_guest'=>'no', 'address_html'=>$address_html, 'address_flag'=>$address_flag);
			} else {
				$arr = array('count'=> $count, 'flag_email_phn'=>'email', 'is_guest'=>'yes');
			}
		}
		echo json_encode($arr);
	}
	public function checkEmailExist(){
        $email = ($this->input->post('user_email') != '')?$this->input->post('user_email'):'';
        if($email != ''){
            $check = $this->db->where(array('email' => $this->input->post('user_email'),'user_type' => 'User'))->get('users')->num_rows();
            echo $check;
            if($check > 0){
                $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                return false;  
            } else {
                return true;
            }
        }
    }
    public function checkoutItem_reload()
	{
		$old_total_price = ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0;
	    $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):0;
	    $restaurant_id = ($this->input->post('restaurant_id'))?$this->input->post('restaurant_id'):0;
	    $cart_details = get_cookie('cart_details');
	    $cart_restaurant = get_cookie('cart_restaurant');
	    $arr_cart_details = json_decode($cart_details);
	    $data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
	    $data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
	    $ajax_driver_tips = $this->load->view('ajax_driver_tips',$data,true);
	    $default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['restaurant_data'] = $this->common_model->checkResForCart($cart_restaurant);
		$data['current_page']='Checkout';
	    $ajax_your_items = $this->load->view('ajax_your_items',$data,true);
	    //menu suggestion :: start
		$cart_items_array = array();
		foreach ($data['cart_details']['cart_items'] as $cartkey => $cartvalue) {
			array_push($cart_items_array, $cartvalue['menu_content_id']);
		} 
		$data['menu_item_suggestion'] = $this->checkout_model->getMenuSuggestionItems($cart_restaurant,$this->session->userdata('language_slug'),$cart_items_array);
		$ajax_your_suggestion = $this->load->view('ajax_your_suggestion',$data,true);
		//menu suggestion :: end
		$data['order_mode'] = $this->session->userdata('order_mode');
		/* $data['reset_coupon_discount_on_item_change']  if Plus minus action done in cart item this will be set to true*/
		if($this->input->post('call_from') != 'confirm_order'){
			$data['reset_coupon_discount_on_item_change'] = TRUE;
			$this->removeCouponOnReset();
		}
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
		$order_summary = $this->load->view('ajax_order_summary',$data,true);
	    $array_view = array(
	      'ajax_your_items'=>$ajax_your_items,
	      'ajax_your_suggestion'=>$ajax_your_suggestion,
	      'ajax_driver_tips'=>$ajax_driver_tips,
	      'ajax_order_summary'=>$order_summary,
	      'cart_total'=>$data['cart_details']['cart_total_price'],
	      'old_total_price'=> $old_total_price,
	      'is_out_of_stock_item_in_cart' => $data['cart_details']['is_out_of_stock_item_in_cart']
	    );
	    echo json_encode($array_view); exit;
	}
	//Code for remove coupon
    public function chkPaymentOptions()
    {
    	$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		$data['order_mode'] = $this->input->post('order_mode');
		/*sales tax changes start*/
		$data['sales_tax'] = $this->checkout_model->getRestaurantTax($cart_restaurant);
		/*sales tax changes end*/
		if ($this->session->userdata('is_user_login') == 1) {
			$minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
			$data['minimum_subtotal'] = $minimum_subtotal->OptionValue;
			$data['earning_points'] = $this->checkout_model->getUsersEarningPoints($this->session->userdata('UserID'));
		}
		$data['payment_optionval'] = ($this->input->post('payment_optionval'))?$this->input->post('payment_optionval'):'';
    	$this->load->view('ajax_order_summary',$data);
    }
    public function removeCouponOnReset(){
    	$this->session->set_userdata(array('coupon_id' => '','coupon_applied' => 'no'));		
		$this->session->unset_userdata('coupon_array');
    }
	public function manage_driver_tip()
	{
		$default_driver_tip = get_default_driver_tip_amount();
		$default_driver_tip = ($default_driver_tip > 0 && $default_driver_tip != '') ? (float)$default_driver_tip : 0;
		if(!($this->session->userdata('tip_percent_val'))) { 
			$this->session->set_userdata('tip_percent_val',$default_driver_tip);
		}

		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$ajax_driver_tips = $this->load->view('ajax_driver_tips',$data,true);
		$array_view = array(
			'ajax_driver_tips'=>$ajax_driver_tips,
		);
		echo json_encode($array_view); exit;
	}
	public function checkRestaurantAvailable() {
		$scheduled_date = ($this->input->post('scheduled_date')) ? date('Y-m-d', strtotime(str_replace('-', '/', $this->input->post('scheduled_date')))) : '';
		$scheduled_time = ($this->input->post('scheduled_time')) ? date('H:i:s', strtotime($this->input->post('scheduled_time'))) : '';
		$combinedDT = date('Y-m-d H:i:s', strtotime("$scheduled_date $scheduled_time"));
		$restaurant_id = ($this->input->post('restaurant_id')) ? $this->input->post('restaurant_id') : '';
        
        $return_txt = '';
		//past datetime validation
        $scheduled_datetime_chk = $this->common_model->setZonebaseDateTime($combinedDT);
        $request_date = new DateTime($scheduled_datetime_chk);
        $now = new DateTime();
        if($request_date < $now) {
        	$return_txt = 'past_date';
        } else {
        	$return_txt = ($combinedDT && $restaurant_id) ? $this->common_model->getRestaurantTimings($restaurant_id, $scheduled_date, $scheduled_time) : 'not_available';
        }
        echo ($return_txt != 'not_available') ? 'available' : $return_txt; exit;
	}
	public function getTimeSlotForSelectedDate() {
		$scheduled_date = ($this->input->post('scheduled_date')) ? date('Y-m-d', strtotime(str_replace('-', '/', $this->input->post('scheduled_date')))) : '';
		$restaurant_id = ($this->input->post('restaurant_id')) ? $this->input->post('restaurant_id') : '';
        
		$time_slot = $this->common_model->getDateAndTimeSlotsForScheduling($restaurant_id, $scheduled_date,'', '', 'web');
		$slot_html = 'not_available';
		if(!empty($time_slot)) {
			$slot_html = '<option value="">'.$this->lang->line("select").'</option>';
			foreach ($time_slot[$scheduled_date] as $js_slotkey => $js_slotvalue) {
				$slot_html .= '<option value="'.$js_slotkey.'" slot_open_time="'.$js_slotvalue['start'].'" slot_close_time="'.$js_slotvalue['end'].'">'.$js_slotvalue['start'].' - '.$js_slotvalue['end'].'</option>';
			}
		}
		echo $slot_html; exit;
	}
	public function checkUserVerified() {
		$return_arr = array('status' => 1, 'message' =>'');
		$user_id = ($this->input->post('order_user_id')) ? $this->input->post('order_user_id') : 0;
		$guest_mobile_number = ($this->input->post('guest_mobile_number')) ? $this->input->post('guest_mobile_number') : '';
		$guestphonecode = ($this->input->post('guestphonecode')) ? $this->input->post('guestphonecode') : '';
		$guestfirstname = ($this->input->post('guestfirstname')) ? $this->input->post('guestfirstname') : '';
		$guestlastname = ($this->input->post('guestlastname')) ? $this->input->post('guestlastname') : '';
		$guestemail = ($this->input->post('guestemail')) ? $this->input->post('guestemail') : '';
		if($this->session->userdata('UserType') != 'Agent') {
			if($user_id > 0) {
				$user_record = $this->common_model->getSingleRow('users','entity_id',$user_id);
				if($user_record->mobile_number == '' || $user_record->mobile_number == null) {
					//add mobile number
					$return_arr = array('status' => 0, 'message' =>'add_mobile_number');
				}
			} else if($guest_mobile_number != '' && $user_id == 0 && $this->session->userdata('guest_otp_verified') == '0') {
				//generate otp for guest
				$guestOTP = $this->common_model->generateOTP(0);
				//add values in session
				$this->session->set_userdata('guestfirstname', $guestfirstname);
				$this->session->set_userdata('guestlastname', $guestlastname);
				$this->session->set_userdata('guestemail', $guestemail);
				$this->session->set_userdata('guest_mobile_number', $guest_mobile_number);
				$this->session->set_userdata('guestphonecode', $guestphonecode);
				$this->session->set_userdata('guest_otp', $guestOTP);
				//send otp
				$sms = $guestOTP.$this->lang->line('your_otp');
				$mobile_numberT = ($guestphonecode) ? '+'.$guestphonecode.$guest_mobile_number : '+1'.$guest_mobile_number;
				$sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
				if($this->session->userdata('guestemail')){
					$this->common_model->sendVerifyOtpEmail($this->session->userdata('guestfirstname'),$this->session->userdata('guestemail'),$this->session->userdata('guest_otp'),$this->session->userdata('language_slug')); //send email
				}
				$return_arr = array('status' => 2, 'message' =>'add_guest_mobile_number', 'guest_mobile_number' => $this->session->userdata('guest_mobile_number'), 'guestphonecode' => $this->session->userdata('guestphonecode'));
			}
		}
		echo json_encode($return_arr);
	}
}
?>