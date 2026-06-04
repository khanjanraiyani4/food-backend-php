<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
  public function __construct() {
    parent::__construct();        
    $this->load->library('form_validation');
    $this->load->library('ajax_pagination'); 
    $this->load->model(ADMIN_URL.'/common_model');  
    $this->load->model('/home_model'); 
    // Load facebook oauth library
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
  // get home page
  public function index()
  {
    $data['enter_otp'] = 'no';
    $this->session->set_userdata('enter_otp', $data['enter_otp']);
    $this->session->set_userdata('order_mode_frm_dropdown', '');
    $data['current_page'] = 'HomePage';
    $data['page_title'] = $this->lang->line('home_page'). ' | ' . $this->lang->line('site_title');
    $order_mode='Delivery';
    //$restaurants = $this->home_model->getRestaurants($order_mode);
    //restaurant sort/filter section :: start
    $range = $this->common_model->getRange();
    $data['maximum_range'] = (int)$range[1]->OptionValue;
    $data['minimum_range'] = (int)$range[0]->OptionValue;
    $latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
    $longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
    $filter_by = ($this->input->post('filter_by'))?$this->input->post('filter_by'):'distance';
    $page = 0;
    $restaurants = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$order_mode,'',$filter_by,8,$page,'pagination');
    $res_food_types = array_column($restaurants, 'food_type');
    $getFoodType = $this->home_model->getFoodType($res_food_types);
    $data['food_type'] = $getFoodType;
    //restaurant sort/filter section :: end
    //$data['restaurants'] = array_values($restaurants);
    $restaurant = array_values($restaurants);
    // usort($restaurant, function($a, $b) {
    //   return $b['timings']['closing'] > $a['timings']['closing'];
    // });
    $data['restaurants'] = $restaurant;
    if (!empty($data['restaurants'])) {
      foreach ($data['restaurants'] as $key => $value) {
        $ratings = $this->home_model->getRestaurantReview($value['content_id']);
        $data['restaurants'][$key]['ratings'] = $ratings;
        $review_data = $this->home_model->getReviewsPagination($value['content_id'],review_count,1);
        $data['restaurants'][$key]['restaurant_reviews'] = $review_data['reviews'];
        $data['restaurants'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
      }
    }
    $data['categories'] = $this->home_model->getAllCategories();
    $res_content_ids = array_column($restaurants, 'content_id');
    $data['coupons'] = $this->home_model->getAllCoupons($res_content_ids);
    //restaurant pagination :: start
    $countResult = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$order_mode,'',$filter_by,8,$page);
    $data['TotalRecord'] = count($restaurant);
    $config = array();
    $config["base_url"] = base_url().'home/index';        
    $config["total_rows"] = count($countResult);
    $config["per_page"] = 8;
    $config['first_link'] = $this->lang->line('first');
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_link'] = $this->lang->line('last');
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['next_link'] = $this->lang->line('next');
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_link'] = $this->lang->line('previous');               
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';        
    $config['cur_tag_open'] = '<li class="active"><a class="active">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['uri_segment'] = 3;
    $this->ajax_pagination->initialize($config);
    $data['PaginationLinks'] = $this->ajax_pagination->create_links();
    //restaurant pagination :: end
    $this->load->view('home_page',$data);
  }
  // frontend user login
  public function login()
  {
    $last_page_url = end(explode("/",$_SERVER['HTTP_REFERER']));
    if($last_page_url == 'checkout'){
      $this->session->set_userdata('sign_up_from_checkout_page', 1);
    }
    if ($this->session->userdata('is_user_login') == 1)
    {
      redirect(base_url().'myprofile');
    }
    $data['enter_otp'] = 'no';
    if(($this->input->get('frm_page')) && $this->input->get('frm_page')=='loginpage')
    {
      /*if($this->input->post('otp_verified') != 'yes'){
        $this->session->set_userdata('is_user_login', 1);
        redirect(base_url().'myprofile'); 
      }*/     
    }
    else
    {
      $this->session->set_userdata('enter_otp', $data['enter_otp']);
    }   
    $data['page_title'] = $this->lang->line('title_login').' | '. $this->lang->line('site_title');
    if($this->input->post('submit_page') == "Login"){
      if($this->input->post('login_with')=="phone_number") {
        $this->form_validation->set_rules('phone_number_inp', $this->lang->line('phone_number'), 'trim|required'); 
      }
      if($this->input->post('login_with')=="email") {
        $this->form_validation->set_rules('email_inp', $this->lang->line('email'), 'trim|required'); 
      }
      $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');        
      if ($this->form_validation->run()) {  
        $enc_pass = md5(SALT.trim($this->input->post('password')));
        if($this->input->post('login_with')=="phone_number") {
          $phone_number = trim($this->input->post('phone_number_inp'));
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
                  'userPhone' => $val->mobile_number,
                  'userPhone_code' => $val->phone_code,                                
                  'userImage' => $val->image,                            
                  //'is_admin_login' => 0,                           
                  'is_user_login' => 1,
                  'UserType' => $val->user_type,
                  //'package_id' => array(),
                )
              );
              // remember ME
              $cookie_name = "adminAuth";
              if($this->input->post('rememberMe')==1) {
                $this->input->set_cookie($cookie_name, 'usr='.$phone_number.'&phone_code='.$phone_code.'&hash='.trim($this->input->post('password')), 60*60*24*5); // 5 days
              } else {
                delete_cookie($cookie_name);
              }                
              $this->session->set_userdata('login_with', '');
              if($this->session->userdata('previous_url')) {
                redirect($this->session->userdata('previous_url'));
              } else {
                redirect(base_url().'myprofile');
              }
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
                    //'is_admin_login' => 0,                           
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
                $this->session->set_userdata('facebook_user', 'no');
                $this->session->set_userdata('google_user', 'no');
                //$data['loginError'] = $this->lang->line('front_login_deactivate');
                if($this->input->post('frm_page') && $this->input->post('frm_page')=='loginpage')
                {
                  redirect(base_url().'home/login?frm_page='.$this->input->post('frm_page'));
                  exit;
                }
                else
                {
                  redirect(base_url().'home/registration');
                  exit;
                }             
              } 
              else
              {
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
        } elseif ($this->input->post('login_with')=="email") {
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
                  'userPhone' => $val_by_email->mobile_number,
                  'userPhone_code' => $val_by_email->phone_code,                               
                  'userImage' => $val_by_email->image,                            
                  //'is_admin_login' => 0,                           
                  'is_user_login' => 1,
                  'UserType' => $val_by_email->user_type,
                  //'package_id' => array(),
                )
              );
              // remember ME
              $cookie_name = "adminAuth";
              if($this->input->post('rememberMe')==1) {                    
                $this->input->set_cookie($cookie_name, 'usr='.$email.'&hash='.trim($this->input->post('password')), 60*60*24*5); // 5 days
              } else {
                delete_cookie($cookie_name);
              }                
              $this->session->set_userdata('login_with', '');
              if($this->session->userdata('previous_url')) {
                    redirect($this->session->userdata('previous_url'));
                } else {
                  redirect(base_url().'myprofile');
                }
            } else if($val_by_email->active=='0' || $val_by_email->active=='' || $val_by_email->status=='0') {
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
                if($this->input->post('frm_page') && $this->input->post('frm_page')=='loginpage')
                {
                  redirect(base_url().'home/login?frm_page='.$this->input->post('frm_page'));
                  exit;
                }
                else
                {
                  redirect(base_url().'home/registration');
                  exit;
                }
              } else {
                $this->session->set_userdata('enter_otp', $data['enter_otp']);
                $this->session->set_userdata('facebook_user', 'no');
                $this->session->set_userdata('google_user', 'no');
                $this->db->select('OptionValue');                
                $this->db->where('OptionSlug','Admin_Email_Address');        
                $admin_email = $this->db->get('system_option')->first_row();
                $data['loginError'] = $this->lang->line('login_deactivedis').' '.$admin_email->OptionValue;
              }
            } else {
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
        redirect(base_url().'home/login');
        exit;
      }
    }
    // Facebook authentication url 
    $data['authURL'] =  $this->facebook->login_url();
    $data['google_login_url'] = $this->google->loginURL();
    $data['current_page'] = 'Login';
    $this->load->view('login',$data);
  }
  public function social() {
    // Authenticate user with facebook 
    if($this->facebook->is_authenticated()) { 
      // Get user info from facebook 
      $fbUser = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,gender,picture');
      /* As in facebook there is scenario that email may not available. Facebook allows user to register using phone number and as per graph api facebook will not return phone number in response. */
      if(!$fbUser['email']) {
        $data['loginError'] = $this->lang->line('facebook_login_email_error');
        //$this->session->set_flashdata('error_MSG', $data['loginError']);
        $_SESSION['error_MSG'] = $data['loginError'];
        redirect(base_url().'home/login');
      }

      // Preparing data for database insertion 
      $dataAdd['login_type'] = 'facebook'; 
      $dataAdd['social_media_id']    = !empty($fbUser['id'])?$fbUser['id']:'';; 
      $dataAdd['first_name']    = !empty($fbUser['first_name'])?$fbUser['first_name']:'';
      $dataAdd['last_name']    = !empty($fbUser['last_name'])?$fbUser['last_name']:''; 
      $dataAdd['email']        = !empty($fbUser['email'])?$fbUser['email']:''; 
      $dataAdd['image']    = !empty($fbUser['picture']['data']['url'])?$fbUser['picture']['data']['url']:''; 
      $dataAdd['user_type'] = 'User';
      
      //to download image from url
      $url = ($dataAdd['image'])?$dataAdd['image']:'';
      if(!empty($url)) { 
          $fdata = file_get_contents($url);
          $random_string = random_string('alnum',12);
          $new = 'uploads/profile/'.$random_string.'.png';
          file_put_contents($new, $fdata);
          $dataAdd['image'] = "profile/".$random_string.'.png';
      }
      // Insert or update user data to the database 
      $val = $this->home_model->checkUser($dataAdd); 
      // Check user data insert or update status 
      if(!empty($val)) { 
        if(empty($val->referral_code)) {
          $referral_code = random_string('alnum', 8);
          $update = array(
              'referral_code'=>$referral_code
          );
          $this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
          
          $this->db->where('entity_id',$val->entity_id);
          $val = $this->db->get('users')->first_row();
        }
        if(empty($val->stripe_customer_id)) {
          $stripe_customer_id = $this->common_model->add_new_customer_in_stripe($val->first_name,$val->last_name,$val->phone_code,$val->mobile_number,$val->email);
          if($stripe_customer_id){
              $update = array(
                  'stripe_customer_id'=>$stripe_customer_id
              );
              $this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
          }
        }
        if($val->is_deleted=='1') {
          $data['loginError'] = $this->lang->line('delete_acc_validation');
        } else if($val->active=='1' && $val->status=='1') {
          // Store the user profile info into session 
          $this->session->set_userdata(
            array(
              'UserID' => $val->entity_id,
              'userFirstname' => $val->first_name,                            
              'userLastname' => $val->last_name,                            
              'userEmail' => $val->email,                                   
              'userPhone' => $val->mobile_number, 
              'userPhone_code' => $val->phone_code,                               
              'userImage' => $val->image,                            
              //'is_admin_login' => 0,                           
              'is_user_login' => 1,
              'UserType' => $val->user_type,
              //'package_id' => array(),
            )
          );              
          if($this->session->userdata('previous_url')) {
            redirect($this->session->userdata('previous_url'));
          } else {
            redirect(base_url().'myprofile');
          } 
        } else if($val->active=='0' || $val->active=='' || $val->status=='0') {       
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
                //'is_admin_login' => 0,                           
                'is_user_login' => 0,
                'UserType' => $val->user_type,
                'social_media_id'=>$val->social_media_id,
                //'package_id' => array(),
              )
            );
            $data['enter_otp'] = 'yes';         
            $this->session->set_userdata('enter_otp', $data['enter_otp']);
            $this->session->set_userdata('facebook_user', 'yes');
            redirect(base_url().'home/registration');
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
            //$data['loginError'] = $this->lang->line('front_login_deactivate');
          }
        } else {
          $data['loginError'] = $this->lang->line('front_login_error');
        }
      } else {
        $data['loginError'] = $this->lang->line('front_login_error');
      }
    } else { 
        $data['loginError'] = $this->lang->line('facebook_login_error');
    } 
    //$this->session->set_flashdata('error_MSG', $data['loginError']);
    $_SESSION['error_MSG'] = $data['loginError'];
    redirect(base_url().'home/login');
  }
  //Server side validation check email exist
  public function checkPhone($str){   
    $phone_code = $this->input->post('phone_code');
    $phncode = ($phone_code)?$phone_code:NULL;
    $checkPhone = $this->home_model->mobileCheck(trim($this->input->post('phone_number')),$phncode);
    //$checkPhone = $this->home_model->checkPhone($str); 
    if($checkPhone > 0){
      $this->form_validation->set_message('checkPhone', $this->lang->line('number_already_registered'));
      return FALSE;
    }
    else{
      return TRUE;
    }
  }
  //Server side validation check email exist
  public function checkEmail($str){    
    $checkEmail = $this->home_model->checkEmail($str);       
    if($checkEmail>0){
      $this->form_validation->set_message('checkEmail',$this->lang->line('user_email_exist_error_msg'));
      return FALSE;
    }
    else{
      return TRUE;
    }
  }
  // frontend user registration
  public function registration()
  {
    $last_page_url = end(explode("/",$_SERVER['HTTP_REFERER']));
    if($last_page_url == 'checkout'){
      $this->session->set_userdata('sign_up_from_checkout_page', 1);
    }
    if($this->input->post('otp_verified') != 'yes') {
      $data['page_title'] = $this->lang->line('title_registration').' | '.$this->lang->line('site_title');
      if($this->input->post('submit_page') == "Register"){
        $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required'); 
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|callback_checkPhone');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|callback_checkEmail'); 
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|min_length[6]');
        if ($this->form_validation->run()) {   
          $phone_code = $this->input->post('phone_code');
          $phncode = ($phone_code)?$phone_code:NULL;
          $checkRecords = $this->home_model->mobileCheck(trim($this->input->post('phone_number')),$phncode);
          $checkEmailRecords = $this->home_model->checkAllEmail(trim($this->input->post('email')));
          if($checkRecords == 0 && $checkEmailRecords==0)
          {
            /*$name = trim($this->input->post('name'));      
            $namearr = explode(" ", $name);  
            if (!empty($namearr)) {
              foreach ($namearr as $key => $value) {
                if ($key != 0) {
                  $last_name[] = $value;
                }
              }
            }*/
            $first_name = ($this->input->post('first_name'))?trim($this->input->post('first_name')):NULL;
            $last_name = ($this->input->post('last_name'))?trim($this->input->post('last_name')):NULL;
            $email = ($this->input->post('email'))?trim($this->input->post('email')):NULL;
            $referral_code = random_string('alnum', 8);
            $stripe_customer_id = $this->common_model->add_new_customer_in_stripe(trim($first_name),trim($last_name),trim($phone_code),trim($phone_number),trim($email));
            $userData = array(
                "first_name"=>$first_name,
                "last_name"=>$last_name,
                "password"=>md5(SALT.$this->input->post('password')),
                "email"=>$email,
                "phone_code"=>$phncode,
                "mobile_number"=>trim($this->input->post('phone_number')),
                "user_type"=>"User",
                "status"=>1,                        
                'referral_code'=>$referral_code,
                'stripe_customer_id'=>($stripe_customer_id)?$stripe_customer_id:NULL,
            ); 
            if (!empty($_FILES['Image']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = './uploads/profile';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                $config['max_size'] = '5120'; //in KB    
                $config['encrypt_name'] = TRUE;               
                // create directory if not exists
                if (!@is_dir('uploads/profile')) {
                  @mkdir('./uploads/profile', 0777, TRUE);
                }
                $this->upload->initialize($config);                  
                if ($this->upload->do_upload('Image'))
                {
                    $img = $this->upload->data();

                    //Code for compress image :: Start
                    $fileName = basename($img['file_name']);                   
                    $imageUploadPath = './uploads/profile/'. $fileName; 
                    $imageTemp = $_FILES["Image"]["tmp_name"];
                    $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                    //Code for compress image :: End

                    $userData['image'] = "profile/".$img['file_name']; 
                }
                else
                {
                    $data['Error'] = $this->upload->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                    //$this->session->set_flashdata('myProfileMSGerror', $data['Error']); 
                    $_SESSION['myProfileMSGerror'] = $data['Error'];
                }
            }
            if(!empty($this->input->post('referral_code'))) {
              $getUser = $this->common_model->getSingleRow('users', 'referral_code',$this->input->post('referral_code'));
                if(!empty($getUser)){
                  $userData['referral_code_used'] = $this->input->post('referral_code');
                  $entity_id = $this->common_model->addData('users',$userData);
                  if ($entity_id) {
                    $user_data = $this->common_model->getSingleRow('users','entity_id',$entity_id);
                    $this->session->set_userdata(
                      array(
                        'UserID' => $user_data->entity_id,
                        'userFirstname' => $user_data->first_name,                            
                        'userLastname' => $user_data->last_name,                            
                        'userEmail' => $user_data->email,                                   
                        'userPhone' => $user_data->mobile_number,
                        'userPhone_code' => $user_data->phone_code,
                        'userImage' => $user_data->image,                            
                        //'is_admin_login' => 0,                           
                        'is_user_login' => 0,
                        'UserType' => $user_data->user_type,
                      )
                    );
                    //send otp start 
                    $this->common_model->generateOTP($user_data->entity_id);
                    $user_record = $this->common_model->getSingleRow('users','entity_id',$user_data->entity_id);
                    $sms = $user_record->user_otp.$this->lang->line('your_otp');
                    $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                    $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                    $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                    if($user_record->email){
                      $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug')); //send email
                    }
                    //send otp end 
                    $data['enter_otp'] = 'yes';
                    $phone_codeval = ($this->input->post('phone_code'))?$this->input->post('phone_code'):'';
                    $this->session->set_userdata('enter_otp', $data['enter_otp']);
                    $this->session->set_userdata('phone_codeval', $phone_codeval);
                    $data['success'] = $this->lang->line('registered_success');                         
                  }
                } else {
                  //wrong referral code.
                  $data['error'] = $this->lang->line('wrong_referral_code');
                }
            } else {
              $entity_id = $this->common_model->addData('users',$userData);
              if ($entity_id) {
                $user_data = $this->common_model->getSingleRow('users','entity_id',$entity_id);
                $this->session->set_userdata(
                  array(
                    'UserID' => $user_data->entity_id,
                    'userFirstname' => $user_data->first_name,                            
                    'userLastname' => $user_data->last_name,                            
                    'userEmail' => $user_data->email,                                   
                    'userPhone' => $user_data->mobile_number,
                    'userPhone_code' => $user_data->phone_code,
                    'userImage' => $user_data->image,                            
                    //'is_admin_login' => 0,                           
                    'is_user_login' => 0,
                    'UserType' => $user_data->user_type,
                  )
                );
                //send otp start 
                $this->common_model->generateOTP($user_data->entity_id);
                $user_record = $this->common_model->getSingleRow('users','entity_id',$user_data->entity_id);
                $sms = $user_record->user_otp.$this->lang->line('your_otp');
                $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                if($user_record->email){
                  $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug')); //send email
                }
                //send otp end 
                $data['enter_otp'] = 'yes';
                $phone_codeval = ($this->input->post('phone_code'))?$this->input->post('phone_code'):'';
                $this->session->set_userdata('enter_otp', $data['enter_otp']);
                $this->session->set_userdata('phone_codeval', $phone_codeval); 
                $data['success'] = $this->lang->line('registered_success');                       
              }
            }
          } else {
            $data['error'] = $this->lang->line('front_registration_fail');
            //$this->session->set_flashdata('error_MSG', $data['error']);
            $_SESSION['error_MSG'] = $data['error'];
          }
          if($data['error'] != $this->lang->line('wrong_referral_code')){
            redirect(base_url().'home/registration');
            exit;
          }
        }
        else
        {
          $data['enter_otp'] = 'no';
            $this->session->set_userdata('enter_otp', $data['enter_otp']);
        }  
      }
    } else {
      $this->session->set_userdata('is_user_login', 1);
      if($this->session->userdata('previous_url')) {
        redirect($this->session->userdata('previous_url'));
      } else {
        redirect(base_url().'myprofile');
      }
    }
    // Facebook authentication url 
    $data['authURL'] =  $this->facebook->login_url();
    $data['google_login_url'] = $this->google->loginURL();
    $data['current_page'] = 'Registration';
    $this->load->view('registration',$data);
  }
  // user forgot password
  public function forgot_password()
  { 
    if($this->input->post('forgot_submit_page') == "Submit")
    { 
        $this->form_validation->set_rules('mobile_number_first', $this->lang->line('phone_number'), 'trim|required');      
        if ($this->form_validation->run())
        {
          $this->db->where('mobile_number',strtolower($this->input->post('mobile_number_first')));
          $this->db->where('phone_code',strtolower($this->input->post('phone_code_first')));
          $this->db->where('status',1);
          $this->db->where("(user_type='User' OR user_type='Agent')");
          $checkRecord = $this->db->get('users')->result();
          
          //$checkRecord = $this->common_model->getRowsMultipleWhere('users', array('email'=>strtolower($this->input->post('email_forgot')),'user_type' => 'User','status'=>1));
          $arr['forgot_success'] = '';
          $arr['forgot_error'] = '';
          if(!empty($checkRecord[0])) {
            if($checkRecord[0]->is_deleted == 1){
              $arr['forgot_error'] = $this->lang->line('acc_validation_forgt_pass');
            }else if($checkRecord[0]->active == 1){
              // confirmation link
              $email_phn_cnt = 0;
              if($checkRecord[0]->email){
                $email_phn_cnt++;
                //send otp start
                $language_slug = $this->session->userdata('language_slug');
                $this->common_model->generateOTP($checkRecord[0]->entity_id);
                $user_record = $this->common_model->getSingleRow('users','entity_id',$checkRecord[0]->entity_id);
                //in phn no
                if($user_record->mobile_number!='' && $user_record->mobile_number != NULL){
                  $email_phn_cnt++;
                  $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
                  $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                  $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                  $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                }

                //in email
                $email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password-otp','language_slug'=>$language_slug))->first_row();        
                $arrayData = array('FirstName'=>$user_record->first_name,'your_otp'=>$user_record->user_otp);
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
                $this->email->to($user_record->email);
                $this->email->subject($email_template->subject);
                $this->email->message($EmailBody);
                $this->email->send();
                //send otp end
              }
              $arr['forgot_success'] = ($email_phn_cnt==2)?$this->lang->line('forgot_success_new'):$this->lang->line('forgot_success_new');
              $arr['verifyotp_modaltitle'] = $this->lang->line('otp_verification');
              $arr['enter_otp_text'] = $this->lang->line('enter_otp_forgot_pwd');
              $arr['forgot_pwd_userid'] = $user_record->entity_id;
            } else {
              $this->db->select('OptionValue');                
              $this->db->where('OptionSlug','Admin_Email_Address');        
              $admin_email = $this->db->get('system_option')->first_row();
              $arr['forgot_error'] = $this->lang->line('login_deactivedis').' '.$admin_email->OptionValue;
              //$arr['forgot_error'] = $this->lang->line('front_login_deactivate');
            }
                
          } else {
              $arr['forgot_error'] = $this->lang->line('phn_not_exist');
          }
        }
    }

    echo json_encode($arr);
  }
  // user otp verify
  public function verify_otp(){ 
    if($this->input->post('verifyotp_submit_page') == "Submit"){ 
      $this->form_validation->set_rules('user_otp', $this->lang->line('otp'), 'required');      
      if ($this->form_validation->run()) {
        if($this->input->post('verify_guest_number_from_checkout') == '1') {
          $arr['verifyotp_success'] = '';
          $arr['verifyotp_error'] = '';
          if($this->session->userdata('guest_otp') == (int)trim($this->input->post('user_otp'))) { //if otp verified
            $this->session->set_userdata('guest_otp_verified','1');
            $arr['verifyotp_success'] = $this->lang->line('otp_success');
            $arr['verify_guest_number_from_checkout'] = $this->input->post('verify_guest_number_from_checkout');
            $arr['guest_mobile_number'] = $this->session->userdata('guest_mobile_number');
            $select_iso_val = $this->common_model->getIsobyPhnCode($this->session->userdata('guestphonecode'));
            $arr['select_iso_val'] = $select_iso_val;
            $arr['guestphonecode'] = $this->session->userdata('guestphonecode');
          } else { //if entered wrong otp
            $arr['verifyotp_error'] = $this->lang->line('wrong_otp');
          }
        } else {
          $user_id = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):(($this->input->post('forgot_pwd_userid'))?$this->input->post('forgot_pwd_userid'):'');
          $checkRecord = $this->common_model->getRowsMultipleWhere('users', array('entity_id'=>$user_id,'user_otp'=>strtolower($this->input->post('user_otp')),'status'=>1));
          $arr['verifyotp_success'] = '';
          $arr['verifyotp_error'] = '';
          if(!empty($checkRecord[0])) {
            if($this->input->post('is_forgot_pwd') != '1'){
              if($this->input->post('add_number_from_checkout') == '1' && $this->session->userdata('addcheckout_phncode') != '' && $this->session->userdata('addcheckout_phn_no') != '') {
                $update_active = array('active'=>1, 'mobile_number'=>$this->session->userdata('addcheckout_phn_no'), 'phone_code'=>$this->session->userdata('addcheckout_phncode'));
              } else {
                $update_active = array('active'=>1);
              }
              $this->common_model->updateData('users',$update_active,'entity_id',$checkRecord[0]->entity_id);
            }
            $phn_no_updated_msg = '';
            if($checkRecord[0]->mobile_number != $this->session->userdata('resend_phn_no') && $this->input->post('is_forgot_pwd') != '1' && $this->session->userdata('resend_phn_no') != '') {
              $update_contact = array('mobile_number'=>$this->session->userdata('resend_phn_no'), 'phone_code'=>$this->session->userdata('resend_phncode'));
              $this->common_model->updateData('users',$update_contact,'entity_id',$checkRecord[0]->entity_id);
              $phn_no_updated = '+'.$this->session->userdata('resend_phncode').$this->session->userdata('resend_phn_no');
              $phn_no_updated_msg = sprintf($this->lang->line('phn_no_updated'),$phn_no_updated);
            }
            $data['enter_otp'] = 'no';
            $this->session->set_userdata('enter_otp', $data['enter_otp']);
            $this->session->unset_userdata('facebook_user');
            $this->session->unset_userdata('google_user');
            $arr['verifyotp_success'] = ($phn_no_updated_msg!='')?$this->lang->line('otp_success').' '.$phn_no_updated_msg:$this->lang->line('otp_success');
            $arr['is_forgot_pwd'] = $this->input->post('is_forgot_pwd');
            $arr['add_number_from_checkout'] = $this->input->post('add_number_from_checkout');
            $this->session->set_userdata('resend_phncode', '');
            $this->session->set_userdata('resend_phn_no', '');
            $this->session->set_userdata('addcheckout_phncode', '');
            $this->session->set_userdata('addcheckout_phn_no', '');

            if($this->input->post('is_forgot_pwd') != '1'){
              $this->session->set_userdata('is_user_login', 1);
              $this->session->set_userdata('login_with', '');
            }
          } else {
            $arr['verifyotp_error'] = $this->lang->line('wrong_otp');
          }
        }
      }
    } elseif ($this->input->post('verifyotp_submit_page')=="resend_submit") {
      $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|required');
      if ($this->form_validation->run()) {
        $arr['verifyotp_success'] = '';
        $arr['verifyotp_error'] = '';
        $phncode = ($this->input->post('phone_code_otp'))?$this->input->post('phone_code_otp'):NULL;
        $is_forgot_pwd = ($this->input->post('is_forgot_pwd') && $this->input->post('is_forgot_pwd') == '1') ? '1' : '0';
          
        if(!empty($this->session->userdata('UserID'))) {
          $checkRecord = $this->common_model->getRowsMultipleWhere('users', array('entity_id'=>$this->session->userdata('UserID'),'mobile_number'=>$this->input->post('mobile_number'),'phone_code'=>$phncode,'status'=>1));
        } else {
          $checkRecord = $this->common_model->getRowsMultipleWhere('users', array('mobile_number'=>$this->input->post('mobile_number'),'phone_code'=>$phncode,'status'=>1));
        }
        $this->session->set_userdata('resend_phncode', $phncode);
        $this->session->set_userdata('resend_phn_no', $this->input->post('mobile_number'));
        if(!empty($checkRecord[0])) {
          //send otp start 
          $this->common_model->generateOTP($checkRecord[0]->entity_id);
          $user_record = $this->common_model->getSingleRow('users','entity_id',$checkRecord[0]->entity_id);
          if($is_forgot_pwd == '1') {
            $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
          } else {
            $sms = $user_record->user_otp.$this->lang->line('your_otp');
          }
          $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
          $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
          $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
          if($user_record->email) {
            $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug'),$is_forgot_pwd); //send email
          }
          //send otp end 
          $arr['verifyotp_success'] = $this->lang->line('send_otp_resp_regi');
          $arr['verifyotp_sent'] = '1';
        } else if($this->session->userdata('facebook_user') == 'yes' || $this->session->userdata('google_user') == 'yes') {
          $social_media_id = $this->session->userdata('social_media_id');
          if(!empty($social_media_id)) {
            $checkRecords = $this->home_model->mobileCheck(trim($this->input->post('mobile_number')),$phncode);
            if($checkRecords == 0){
              $fb_user_update = array('phone_code'=>$phncode,'mobile_number'=>$this->input->post('mobile_number'));
                $this->common_model->updateData('users',$fb_user_update,'social_media_id',$social_media_id);
                $login = $this->common_model->checksocial($social_media_id);
                $user_id = $login->entity_id;
                //send otp start 
              $this->common_model->generateOTP($user_id);
              $user_record = $this->common_model->getSingleRow('users','entity_id',$user_id);
              $this->session->set_userdata('userPhone', $user_record->mobile_number);
              if($is_forgot_pwd == '1') {
                $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
              } else {
                $sms = $user_record->user_otp.$this->lang->line('your_otp');
              }
              $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
              $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
              $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
              if($user_record->email){
                $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug'),$is_forgot_pwd); //send email
              }
              $arr['verifyotp_success'] = $this->lang->line('send_otp_resp_regi');
              $arr['verifyotp_sent'] = '1';
              //send otp end
            } else {
              $arr['verifyotp_error'] = $this->lang->line('number_already_registered');
            }
          } else {
            //empty social media id
          }
        } else {
          if(!empty($this->session->userdata('UserID'))) {
            $data_with_usrid = $this->common_model->getRowsMultipleWhere('users', array('entity_id'=>$this->session->userdata('UserID'),'mobile_number'=>$this->input->post('mobile_number'),'phone_code'=>$phncode,'status'=>1));
            $data_without_usrid = $this->common_model->getRowsMultipleWhere('users', array('mobile_number'=>$this->input->post('mobile_number'),'phone_code'=>$phncode,'status'=>1));
            if(empty($data_with_usrid) && !empty($data_without_usrid)){
              $arr['verifyotp_error'] = $this->lang->line('number_already_registered');
            } else {
              if($this->input->post('is_forgot_pwd') != '1'){
                //send otp start 
                $this->common_model->generateOTP($this->session->userdata('UserID'));
                $user_record = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
                if($is_forgot_pwd == '1') {
                  $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
                } else {
                  $sms = $user_record->user_otp.$this->lang->line('your_otp');
                }
                $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                if($user_record->email){
                  $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug'),$is_forgot_pwd); //send email
                }
                //send otp end 
                $arr['verifyotp_success'] = $this->lang->line('send_otp_resp_regi');
                $arr['verifyotp_sent'] = '1';
              } else {
                $arr['verifyotp_error'] = $this->lang->line('phn_not_exist');
                $arr['phn_not_exist'] = '1';
              }
            }
          } else {
            $arr['verifyotp_error'] = $this->lang->line('phn_not_exist');
            $arr['phn_not_exist'] = '1';
          }
        }
      }
    } elseif ($this->input->post('verifyotp_submit_page')=="add_phn_no") {
      $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|required');
      if($this->form_validation->run()) {
        $arr['verifyotp_success'] = '';
        $arr['verifyotp_error'] = '';
        $phncode = ($this->input->post('phone_code_otp'))?$this->input->post('phone_code_otp'):NULL;
        $checkRecord = $this->home_model->mobileCheck($this->input->post('mobile_number'),$phncode);
        if($checkRecord == 0) {
          $this->session->set_userdata('addcheckout_phncode', $phncode);
          $this->session->set_userdata('addcheckout_phn_no', $this->input->post('mobile_number'));
          //send otp start 
          $this->common_model->generateOTP($this->session->userdata('UserID'));
          $user_record = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
          $sms = $user_record->user_otp.$this->lang->line('your_otp');
          $mobile_numberT = ($this->session->userdata('addcheckout_phncode'))?$this->session->userdata('addcheckout_phncode'):'+1';
          $mobile_numberT = $mobile_numberT.$this->session->userdata('addcheckout_phn_no');
          $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
          if($user_record->email){
            $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$this->session->userdata('language_slug')); //send email
          }
          //send otp end
          $arr['verifyotp_success'] = $this->lang->line('send_otp_resp_regi');
          $arr['verifyotp_sent'] = '1';
        } else {
          $arr['verifyotp_error'] = $this->lang->line('phone_exist');
        }
      }
    } elseif ($this->input->post('verifyotp_submit_page')=="add_guest_phn_no") {
      $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|required');
      if($this->form_validation->run()) {
        $arr['verifyotp_success'] = '';
        $arr['verifyotp_error'] = '';
        $phncode = ($this->input->post('phone_code_otp'))?$this->input->post('phone_code_otp'):NULL;
        // $checkRecord = $this->home_model->mobileCheck($this->input->post('mobile_number'),$phncode);
        if(true) { //$checkRecord == 0
          $guest_mobile_number = ($phncode) ? '+'.$phncode.$this->input->post('mobile_number') : '+1'.$this->input->post('mobile_number');
          $guestfirstname = ($this->input->post('guestfirstname')) ? $this->input->post('guestfirstname') : '';
          $guestlastname = ($this->input->post('guestlastname')) ? $this->input->post('guestlastname') : '';
          $guestemail = ($this->input->post('guestemail')) ? $this->input->post('guestemail') : '';
          //generate otp for guest
          $guestOTP = $this->common_model->generateOTP(0);
          //add values in session
          $this->session->set_userdata('guestfirstname', $guestfirstname);
          $this->session->set_userdata('guestlastname', $guestlastname);
          $this->session->set_userdata('guestemail', $guestemail);
          $this->session->set_userdata('guest_mobile_number', $this->input->post('mobile_number'));
          $this->session->set_userdata('guestphonecode', $phncode);
          $this->session->set_userdata('guest_otp', $guestOTP);
          //send otp
          $sms = $guestOTP.$this->lang->line('your_otp');
          $mobile_numberT = $guest_mobile_number;
          $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
          if($this->session->userdata('guestemail')){
            $this->common_model->sendVerifyOtpEmail($this->session->userdata('guestfirstname'),$this->session->userdata('guestemail'),$this->session->userdata('guest_otp'),$this->session->userdata('language_slug')); //send email
          }
          //send otp end
          $arr['verifyotp_success'] = $this->lang->line('send_otp_resp_regi');
          $arr['verifyotp_sent'] = '1';
        } else {
          $arr['verifyotp_error'] = $this->lang->line('phone_exist');
        }
      }
    }
    echo json_encode($arr);
  }
  // user logout
  public function logout(){ 
    $this->session->unset_userdata('UserID');
    $this->session->unset_userdata('userFirstname');
    $this->session->unset_userdata('userLastname');
    $this->session->unset_userdata('userEmail');   
    $this->session->unset_userdata('userPhone');
    $this->session->unset_userdata('userPhone_code');    
    $this->session->unset_userdata('is_user_login'); 
    $this->session->unset_userdata('package_id');
    $this->session->unset_userdata('social_media_id');
    $this->session->unset_userdata('previous_url');
    $this->session->unset_userdata('UserType');
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
    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
    $this->output->set_header("Pragma: no-cache");
  }
    // add lat long to session once if searched by user
  public function addLatLong(){
    if(!empty($this->input->post('lat')) && !empty($this->input->post('long')) && !empty($this->input->post('address'))){
      $this->session->set_userdata(
        array(
          'searched_lat' => $this->input->post('lat'),
          'searched_long' => $this->input->post('long'),  
          'searched_address' => $this->input->post('address'),  
        )
      );
    }
    $order_mode = ($this->input->post('order_mode'))?$this->input->post('order_mode'):'';
    if($order_mode != '' && $this->input->post('page') == 'HomePage') {
      $this->session->set_userdata('order_mode_frm_dropdown', $order_mode);
    }
  }
  // get Popular Resturants
  public function getPopularResturants(){
    $data['page_title'] = $this->lang->line('popular_restaurants').' | '.$this->lang->line('site_title');
    $order_mode = ($this->input->post('order_mode'))?$this->input->post('order_mode'):'';
    if($order_mode != ''){
      $this->session->set_userdata('order_mode_frm_dropdown', $order_mode);
    } else {
      $this->session->set_userdata('order_mode_frm_dropdown', '');
    }
    //$restaurants = $this->home_model->getRestaurants($order_mode);
    //restaurant sort/filter section :: start
    $range = $this->common_model->getRange();
    if ($this->session->userdata('order_mode_frm_dropdown') == 'PickUp') {
      $data['maximum_range'] = (int)$range[2]->OptionValue;
    } else {
      $data['maximum_range'] = (int)$range[1]->OptionValue;
    }
    $data['minimum_range'] = (int)$range[0]->OptionValue;
    $latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
    $longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
    $filter_by = ($this->input->post('filter_by'))?$this->input->post('filter_by'):'distance';
    $page = ($this->input->post('page') !="")?$this->input->post('page'):0;
    $restaurants = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$order_mode,'',$filter_by,8,$page,'pagination');
    $res_food_types = array_column($restaurants, 'food_type');
    $getFoodType = $this->home_model->getFoodType($res_food_types);
    $data['food_type'] = $getFoodType;
    $res_content_ids = array_column($restaurants, 'content_id');
    $data['coupons'] = $this->home_model->getAllCoupons($res_content_ids);
    $cuont_coupon = (!empty($data['coupons']))?count($data['coupons']):0;
    $coupon_section_html = '';
    if(!empty($data['coupons'])) {
      $coupon_section_html .= '<div class="container">
        <div class="heading-title">
          <h2>'.$this->lang->line('latest_coupons').'</h2>
        </div>
        <div class="slider-new-add">
          <div class="slider-arrow">
            <div id="customNav2" class="arrow"></div>
          </div>
          <div class="best-offers-slider owl-carousel">';
            foreach ($data['coupons'] as $homecpnkey => $homecpnvalue) {
              $redirect_flag = (count($homecpnvalue->restaurant_ids) == 1) ? '1':'0';
              $rest_image = (file_exists(FCPATH.'uploads/'.$homecpnvalue->image) && $homecpnvalue->image!='') ? image_url.$homecpnvalue->image : default_img;
              if($redirect_flag == '1') {
                $coupon_section_html .= '<div class="best-offers-box">
                                        <a href="'.base_url().'restaurant/restaurant-detail/'.$homecpnvalue->restaurant_slug.'"><img src="'.$rest_image.'" alt="'.$homecpnvalue->name.'" title="'.$homecpnvalue->name.'"></a>
                                      </div>';
              } else {
                $coupon_section_html .= '<div class="best-offers-box">
                                        <img src="'.$rest_image.'" alt="'.$homecpnvalue->name.'" title="'.$homecpnvalue->name.'">
                                      </div>';
              }
            }
          $coupon_section_html .= '</div>
        </div>
      </div>';
    }
    //restaurant sort/filter section :: end
    if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) { 
      $address = $this->getAddress($this->input->post('latitude'),$this->input->post('longitude'));
      if (!empty($restaurants)) {
        foreach ($restaurants as $key => $value) {
          // $distance = $this->getDistance($this->input->post('latitude'),$this->input->post('longitude'), $value['latitude'], $value['longitude']);
          // $range = $this->common_model->getRange();
          // $maximum_range = (float)$range[1]->OptionValue;
          // $restaurants[$key]['distance'] = $distance;
          // if ((int)$distance < $maximum_range) {
            $nearbyRestaurants[] = $restaurants[$key];
          // }
        }
      }

      //distance sorting for home page
      //array_multisort(array_column($nearbyRestaurants, "distance"), SORT_ASC, $nearbyRestaurants );
      
      if (!empty($nearbyRestaurants)) {
        // foreach ($nearbyRestaurants as $key => $value) {
        //   $ratings = $this->home_model->getRestaurantReview($value['content_id']);
        //   $nearbyRestaurants[$key]['ratings'] = $ratings;
        //   $review_data = $this->home_model->getReviewsPagination($value['content_id'],review_count,1);
        //   $nearbyRestaurants[$key]['restaurant_reviews'] = $review_data['reviews'];
        //   $nearbyRestaurants[$key]['restaurant_reviews_count'] = $review_data['review_count'];
        // }
        $nearbyRestaurant = array_values($nearbyRestaurants);
        // usort($nearbyRestaurant, function($a, $b) {
        //   return $b['timings']['closing'] > $a['timings']['closing'];
        // });
      }
      $data['nearbyRestaurants'] = $nearbyRestaurant;
    }
    else
    {
      if (!empty($restaurants)) {
        // foreach ($restaurants as $key => $value) {
        //   $ratings = $this->home_model->getRestaurantReview($value['content_id']);
        //   $restaurants[$key]['ratings'] = $ratings;
        //   $review_data = $this->home_model->getReviewsPagination($value['content_id'],review_count,1);
        //   $restaurants[$key]['restaurant_reviews'] = $review_data['reviews'];
        //   $restaurants[$key]['restaurant_reviews_count'] = $review_data['review_count'];
        // }
        $restaurant = array_values($restaurants);
        // usort($restaurant, function($a, $b) {
        //   return $b['timings']['closing'] > $a['timings']['closing'];
        // });
      }
      $data['nearbyRestaurants'] = $restaurant;
    }
    //restaurant pagination :: start
    $countResult = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$order_mode,'',$filter_by,8,$page);

    $data['TotalRecord'] = (!empty($result))?count($result):0;
    $config = array();
    $config["base_url"] = base_url();        
    $config["total_rows"] = count($countResult);
    $config["per_page"] = 8;
    $config['first_link'] = $this->lang->line('first');
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_link'] = $this->lang->line('last');
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['next_link'] = $this->lang->line('next');
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_link'] = $this->lang->line('previous');              
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';        
    $config['cur_tag_open'] = '<li class="active"><a class="active">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['uri_segment'] = 3;
    $this->ajax_pagination->initialize($config);
    $data['PaginationLinks'] = $this->ajax_pagination->create_links();
    //restaurant pagination :: end
    $ajax_popular_restaurants = $this->load->view('popular_restaurants',$data,true);
    //quick searches section :: start
    $quick_search_html = '';
    $foodtype_dropdown_html = '';
    if(!empty($data['food_type'])) {
      $quick_search_html .= '<div class="container">
        <div class="heading-title">
          <h2>'.$this->lang->line('quick_search').'</h2>
        </div>
        <div class="slider-new-add">
          <div class="slider-arrow">   
            <div id="customNav" class="arrow"></div>
          </div>
          <div class="quick-searches-slider owl-carousel">';

      foreach ($data['food_type'] as $ftkey => $ftvalue) {
        $food_type_image = (file_exists(FCPATH.'uploads/'.$ftvalue->food_type_image) && $ftvalue->food_type_image!='') ? image_url.$ftvalue->food_type_image : default_img;
        $quick_search_html .= '<div class="quick-searches-box" id="foodtype_'.$ftvalue->entity_id.'" onclick="getRestaurantsOnFilter(\'apply\',\'quicksearch_foodtype\',\'\','.$ftvalue->entity_id.')">
              <input type="hidden" name="quicksearch_foodtype" id="quicksearch_foodtype" value="'.$ftvalue->entity_id.'">
              <img src="'.$food_type_image.'" alt="'.$ftvalue->name.'" title="'.$ftvalue->name.'">
              <h5>'.$ftvalue->name.'</h5>
            </div>';
        $foodtype_dropdown_html .= '<option value="'.$ftvalue->entity_id.'">'.ucfirst($ftvalue->name).'</option>';
      }
      $quick_search_html .= '</div>
        </div>
      </div>';
    }
    //quick searches section :: end
    $array_view = array(
      'popular_restaurants'=>$ajax_popular_restaurants,
      'quick_searches'=>$quick_search_html,
      'foodtype_dropdown' => $foodtype_dropdown_html,
      'coupon_section_html' => $coupon_section_html,
      'countcoupon'=>$cuont_coupon
    );
    echo json_encode($array_view); exit;
  }
  // get user's address with lat long
  public function getUserAddress(){
    $this->session->set_userdata(
      array(
        'latitude' => $this->input->post('latitude'),
        'longitude' => $this->input->post('longitude'),
      )
    );
    if($this->input->post('page') == "my_profile" || $this->input->post('page') == "checkout"){
      $address = $this->getAddressWithDetails($this->input->post('latitude'),$this->input->post('longitude'));
    }
    else{
      $address = $this->getAddress($this->input->post('latitude'),$this->input->post('longitude'));
    }
    echo json_encode($address);
  }
  // get distance between two pair of coordinates
  function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
    $earth_radius = DISTANCE_CALCVAL;

    $dLat = deg2rad($latitude2 - $latitude1);  
    $dLon = deg2rad($longitude2 - $longitude1);  

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
    $c = 2 * asin(sqrt($a));  
    $d = $earth_radius * $c;  
    return $d;  
  }
  // get address from lat long
  function getAddress($latitude,$longitude){ 
      if(!empty($latitude) && !empty($longitude)){
          //Send request and receive json data by address
          $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&key='.google_key); 
          $output = json_decode($geocodeFromLatLong);
          $status = $output->status;
          //Get address from json data
          $address = ($status=="OK")?(($output->results[0]->formatted_address)?$output->results[0]->formatted_address:$output->results[1]->formatted_address):'';
          //Return address of the given latitude and longitude
          if(!empty($address)) {
              return $address;
          }
          else
          {
              return false;
          }
      }
      else
      {
          return false;   
      }
  }
  // get address from lat long
  function getAddressWithDetails($latitude,$longitude){ 
    $address_arr = array('address'=>'','city' => '','state' => '','country' => '', 'zipcode' => '');
    if(!empty($latitude) && !empty($longitude)){
      //Send request and receive json data by address
      $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&key='.google_key); 
      $output = json_decode($geocodeFromLatLong);
      $status = $output->status;
      //Get address from json data
      $address = ($status=="OK")?(($output->results[0]->formatted_address)?$output->results[0]->formatted_address:$output->results[1]->formatted_address):'';
      //Return address of the given latitude and longitude
      if(!empty($address)) {
        $address_arr = array('address'=>$address,'city' => '','state' => '','country' => '', 'zipcode' => '');
        foreach ($output->results[0]->address_components as $key => $value) {
            foreach ($value->types as $c_key => $c_value) {                      
                if($c_value=='administrative_area_level_2'){                            
                    $address_arr['city'] = $value->long_name;
                }
                if($c_value=='administrative_area_level_1'){
                    $address_arr['state'] = $value->long_name;
                }
                if($c_value=='country'){
                    $address_arr['country'] = $value->long_name;
                }
                if($c_value=='postal_code'){
                    $address_arr['zipcode'] = $value->long_name;
                }
            }
        }
        if ($address_arr['city'] == '' || $address_arr['state'] == '' || $address_arr['country'] == '' || $address_arr['zipcode'] == '') {
          foreach ($output->results[1]->address_components as $key1 => $value1) {
            foreach ($value1->types as $c_key1 => $c_value1) {                      
                if($address_arr['city'] == '' && $c_value1=='administrative_area_level_2'){
                    $address_arr['city'] = $value1->long_name;
                }
                if($address_arr['state'] == '' && $c_value1=='administrative_area_level_1'){
                    $address_arr['state'] = $value1->long_name;
                }
                if($address_arr['country'] == '' && $c_value1=='country'){
                    $address_arr['country'] = $value1->long_name;
                }
                if($address_arr['zipcode'] == '' && $c_value1=='postal_code'){
                    $address_arr['zipcode'] = $value1->long_name;
                }
            }
          }
        }
      }
    }
    return $address_arr;
  }
  // categories search
  public function quickCategorySearch(){
    $data['page_title'] = $this->lang->line('popular_restaurants').' | '.$this->lang->line('site_title');
    //restaurant sort/filter section :: start
    $range = $this->common_model->getRange();
    if($this->input->post('order_mode') == 'PickUp') {
      $data['maximum_range'] = (int)$range[2]->OptionValue;
    } else {
      $data['maximum_range'] = (int)$range[1]->OptionValue;
    }
    $data['minimum_range'] = (int)$range[0]->OptionValue;
    $latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
    $longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
    $filter_by = ($this->input->post('filter_by'))?$this->input->post('filter_by'):'distance';
    $page = ($this->input->post('page') !="")?$this->input->post('page'):0;
    $restaurants = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$this->input->post('order_mode'),$this->input->post('category_id'),$filter_by,8,$page,'pagination');
    $res_food_types = array_column($restaurants, 'food_type');
    $getFoodType = $this->home_model->getFoodType($res_food_types);
    $data['food_type'] = $getFoodType;
    //restaurant sort/filter section :: end
    if (!empty($restaurants)) {
      foreach ($restaurants as $key => $value) {
        $nearbyRestaurants[] = $restaurants[$key];
      }
    }
    $data['nearbyRestaurants'] = $nearbyRestaurants;
    //restaurant pagination :: start
    $countResult = $this->home_model->getRestaurantsOnFilter(NULL,$latitude,$longitude,$data['minimum_range'],$data['maximum_range'],NULL,$this->input->post('order_mode'),$this->input->post('category_id'),$filter_by,8,$page);

    $data['TotalRecord'] = count($result);
    $config = array();
    $config["base_url"] = base_url();        
    $config["total_rows"] = count($countResult);
    $config["per_page"] = 8;
    $config['first_link'] = $this->lang->line('first');
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_link'] = $this->lang->line('last');
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['next_link'] = $this->lang->line('next');
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_link'] = $this->lang->line('previous');               
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';        
    $config['cur_tag_open'] = '<li class="active"><a class="active">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['uri_segment'] = 3;
    $this->ajax_pagination->initialize($config);
    $data['PaginationLinks'] = $this->ajax_pagination->create_links();
    //restaurant pagination :: end
    $this->load->view('popular_restaurants',$data);
  }
  // function to get  the address
  function get_lat_long($address){
      $address = str_replace(" ", "+", $address);
      $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
      $json = json_decode($json);
      $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
      $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
      $latlng = array('latitude'=>$lat,'longitude'=>$long);
      return json_encode($latlng);
  }
  // get users notification
  public function getNotifications(){
    if (!empty($this->session->userdata('UserID'))) {
      $logged_in_usertype = $this->session->userdata('UserType');
      if($logged_in_usertype == 'Agent'){
        $data['userUnreadNotifications'] = $this->common_model->getAgentNotification($this->session->userdata('UserID'),'unread');
        $data['notification_count'] = count($data['userUnreadNotifications']);
        $data['userNotifications'] = $this->common_model->getAgentNotification($this->session->userdata('UserID'));
      } else {
        $data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
        $reminder = $this->common_model->EventBookingReminderNoti();
        //$table_reminder = $this->common_model->TableBookingReminderNoti();
        $currentDateTime = date("Y-m-d H:i:s");
        $newDateTime = date("Y-m-d H:i:s", strtotime("+2 hours")); 
        foreach ($reminder as $key => $value) {
          $hourdiff = round((strtotime($value['booking_date'] ) - strtotime($currentDateTime))/3600, 2);
          if($hourdiff <= 2 && $hourdiff>0)
          {
            $data['event_booking_reminder'][] = $value;
          }
        }
        /*foreach ($table_reminder as $key => $value) {
          $date_time = $value['booking_date']." ".$value['start_time'];
                $hourdiff = round((strtotime($date_time ) - strtotime($currentDateTime))/3600, 1);
          if($hourdiff <= 2  && $hourdiff > 0)
            {
              $data['table_booking_reminder'][] = $value;
            }
        }*/
        $data['notification_count'] = ((!empty($data['userUnreadNotifications']))?count($data['userUnreadNotifications']):0) + ((!empty($data['event_booking_reminder']))?count($data['event_booking_reminder']):0); // + count($data['table_booking_reminder'])
        $data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
      }
      $this->load->view('ajax_notifications',$data);
    }
  }
  // get unread notifications
  public function unreadNotifications() { 
    if (!empty($this->session->userdata('UserID'))) { 
      $updateData = array(
        'view_status' => 1,
      );
      $this->common_model->updateData('agent_order_notification',$updateData,'agent_id',$this->session->userdata('UserID'));
      $this->common_model->updateData('user_order_notification',$updateData,'user_id',$this->session->userdata('UserID'));
      $this->common_model->updateData('user_event_notifications',$updateData,'user_id',$this->session->userdata('UserID'));
      //$this->common_model->updateData('user_table_notifications',$updateData,'user_id',$this->session->userdata('UserID'));
      $data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
      $data['notification_count'] = (!empty($data['userUnreadNotifications']))?count($data['userUnreadNotifications']):0;
      $data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
    }
  }

  public function google()
  {
    if(isset($_GET['code']))
    {
      //authenticate user
      $this->google->getAuthenticate();
      //get user info from google
      $user_info = $this->google->getUserInfo();
      //preparing data for database insertion
      $userData['login_type']      = 'google';
      $userData['social_media_id'] = $user_info['id'];
      $userData['first_name']      = $user_info['given_name'];
      $userData['last_name']       = $user_info['family_name'];
      $userData['email']           = $user_info['email'];
      $userData['image']           = !empty($user_info['picture'])?$user_info['picture']:'';
      $userData['user_type']       = 'User';
      //to download image from url
      $url = ($userData['image']) ? $userData['image'] : '';
      if(!empty($url)) { 
        $fdata = file_get_contents($url);
        $random_string = random_string('alnum',12);
        $new = 'uploads/profile/'.$random_string.'.png';
        file_put_contents($new, $fdata);
        $userData['image'] = "profile/".$random_string.'.png';
      }
      //insert or update user data to the database
      $val = $this->home_model->checkUser($userData);
      // Check user data insert or update status 
      if(!empty($val))
      {
        if(empty($val->referral_code)) {
          $referral_code = random_string('alnum', 8);
          $update = array(
              'referral_code'=>$referral_code
          );
          $this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
          $this->db->where('entity_id',$val->entity_id);
          $val = $this->db->get('users')->first_row();
        }
        if(empty($val->stripe_customer_id)) {
          $stripe_customer_id = $this->common_model->add_new_customer_in_stripe($val->first_name,$val->last_name,$val->phone_code,$val->mobile_number,$val->email);
          if($stripe_customer_id){
              $update = array(
                  'stripe_customer_id'=>$stripe_customer_id
              );
              $this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
          }
        }
        if($val->is_deleted=='1') {
          $data['loginError'] = $this->lang->line('delete_acc_validation');
        } else if($val->active=='1' && $val->status=='1') {
          // Store the user profile info into session 
          $this->session->set_userdata(
            array(
              'UserID' => $val->entity_id,
              'userFirstname' => $val->first_name,                            
              'userLastname' => $val->last_name,                            
              'userEmail' => $val->email,                                   
              'userPhone' => $val->mobile_number,
              'userPhone_code' => $val->phone_code,
              'userImage' => $val->image,                            
              //'is_admin_login' => 0,                           
              'is_user_login' => 1,
              'UserType' => $val->user_type
            )
          );
          if($this->session->userdata('previous_url')) {
            redirect($this->session->userdata('previous_url'));
          } else {
            redirect(base_url().'myprofile');
          }
        } else if($val->active=='0' || $val->active=='' || $val->status=='0') {
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
                //'is_admin_login' => 0,                           
                'is_user_login' => 0,
                'UserType' => $val->user_type,
                'social_media_id'=>$val->social_media_id,
              )
            );
            $data['enter_otp'] = 'yes';         
            $this->session->set_userdata('enter_otp', $data['enter_otp']);
            $this->session->set_userdata('google_user', 'yes');
            redirect(base_url().'home/registration');
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
          $data['loginError'] = $this->lang->line('front_login_error');
        }
      } else {
        $data['loginError'] = $this->lang->line('front_login_error');
      }
    } else { 
        $data['loginError'] = $this->lang->line('google_login_error');
    } 
    //$this->session->set_flashdata('error_MSG', $data['loginError']);
    $_SESSION['error_MSG'] = $data['loginError'];
    redirect(base_url().'home/login');
  }
  public function cookie()
  {
        $data['page_title'] = $this->lang->line('cookie_policy'). ' | ' . $this->lang->line('site_title');
        $data['current_page'] = 'Cookie Policy';     
        $language_slug = $this->session->userdata('language_slug');
        $data['cookie_policy'] = $this->common_model->getCmsPages($language_slug,'cookie-policy');
        $this->load->view('cookie-policy',$data);
  }
  public function change_password(){ 
    if($this->input->post('change_pass_submit_page') == "Submit"){ 
      $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
      $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required');      
          if ($this->form_validation->run()) {
            $user_id = ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):(($this->input->post('change_pass_userid'))?$this->input->post('change_pass_userid'):'');
            if (!empty($this->input->post('password')) && !empty($this->input->post('confirm_password'))) {
              $newEncryptPass  = md5(SALT.$this->input->post('password'));
              $updateUserData['password'] = $newEncryptPass;
            }
            $this->common_model->updateData('users',$updateUserData,'entity_id',$user_id);

            $arr['change_pass_success'] = $this->lang->line('success_password_change');
          }
      } 
      echo json_encode($arr);
  }
  //restaurant sort/filter section :: start
  public function getRestaurantsOnFilter(){
    $resdishes = ($this->input->post('resdishes'))?$this->input->post('resdishes'):'';
    $order_mode = ($this->input->post('order_mode'))?$this->input->post('order_mode'):'';
    if($order_mode != ''){
      $this->session->set_userdata('order_mode_frm_dropdown', $order_mode);
    } else {
      $this->session->set_userdata('order_mode_frm_dropdown', '');
    }    
    $latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
    $longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
    $minimum_range = ($this->input->post('minimum_range'))?str_replace( ',', '', $this->input->post('minimum_range') ):0;
    $maximum_range = ($this->input->post('maximum_range'))?str_replace( ',', '', $this->input->post('maximum_range') ):0;
    $food_type = ($this->input->post('food_type'))?$this->input->post('food_type'):'';    
    $filter_by = ($this->input->post('filter_by'))?$this->input->post('filter_by'):'distance';
    $page = ($this->input->post('page') !="")?$this->input->post('page'):0;
    $foodtype_quicksearch = ($this->input->post('foodtype_quicksearch'))?$this->input->post('foodtype_quicksearch'):'';
    $category_id = ($this->input->post('category_id'))?$this->input->post('category_id'):'';
    $offers_free_delivery = ($this->input->post('offers_free_delivery') && $this->input->post('offers_free_delivery') > 0)?1:0;
    $availability_filter = ($this->input->post('availability_filter'))?$this->input->post('availability_filter'):'';
    $res_type ='';
    $result = $this->home_model->getRestaurantsOnFilter($resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_type,$order_mode,$foodtype_quicksearch,$filter_by,8,$page,'pagination',$res_type,$category_id,$offers_free_delivery,$availability_filter);
    $data['nearbyRestaurants'] = $result;
    $res_food_types = array_column($result, 'food_type');
    $listed_foodtype = ($this->input->post('listed_foodtype'))?$this->input->post('listed_foodtype'):'';
    if($listed_foodtype != '') {
      array_push($res_food_types, $listed_foodtype);
    }
    $getFoodType = $this->home_model->getFoodType($res_food_types);
    $data['food_type'] = $getFoodType;
    $res_content_ids = array_column($result, 'content_id');
    $data['coupons'] = array();
    if(!empty($res_content_ids)){
      $data['coupons'] = $this->home_model->getAllCoupons($res_content_ids);  
    }
    $cuont_coupon = (!empty($data['coupons']))?count($data['coupons']):0;
    $coupon_section_html = '';
    if(!empty($data['coupons'])) {
      $coupon_section_html .= '<div class="container">
        <div class="heading-title">
          <h2>'.$this->lang->line('latest_coupons').'</h2>
        </div>
        <div class="slider-new-add">
          <div class="slider-arrow">
            <div id="customNav2" class="arrow"></div>
          </div>
          <div class="best-offers-slider owl-carousel">';
            foreach ($data['coupons'] as $homecpnkey => $homecpnvalue) {
              $redirect_flag = (count($homecpnvalue->restaurant_ids) == 1) ? '1':'0';
              $rest_image = (file_exists(FCPATH.'uploads/'.$homecpnvalue->image) && $homecpnvalue->image!='') ? image_url.$homecpnvalue->image : default_img;
              if($redirect_flag == '1') {
                $coupon_section_html .= '<div class="best-offers-box">
                                        <a href="'.base_url().'restaurant/restaurant-detail/'.$homecpnvalue->restaurant_slug.'"><img src="'.$rest_image.'" alt="'.$homecpnvalue->name.'" title="'.$homecpnvalue->name.'"></a>
                                      </div>';
              } else {
                $coupon_section_html .= '<div class="best-offers-box">
                                        <img src="'.$rest_image.'" alt="'.$homecpnvalue->name.'" title="'.$homecpnvalue->name.'">
                                      </div>';
              }
            }
          $coupon_section_html .= '</div>
        </div>
      </div>';
    }
    //restaurant pagination :: start
    $countResult = $this->home_model->getRestaurantsOnFilter($resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_type,$order_mode,$foodtype_quicksearch,$filter_by,8,$page,'',$res_type,$category_id,$offers_free_delivery,$availability_filter);

    $data['TotalRecord'] = count($result);
    $config = array();
    $config["base_url"] = base_url();
    $config["total_rows"] = count($countResult);
    $config["per_page"] = 8;
    $config['first_link'] = $this->lang->line('first');
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_link'] = $this->lang->line('last');
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['next_link'] = $this->lang->line('next');
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_link'] = $this->lang->line('previous');               
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';        
    $config['cur_tag_open'] = '<li class="active"><a class="active">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['uri_segment'] = 3;
    $this->ajax_pagination->initialize($config);
    $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
    //restaurant pagination :: end
    $ajax_popular_restaurants = $this->load->view('popular_restaurants',$data,true);
    //quick searches section :: start
    $selectedfoodtype_arr = explode(',', $foodtype_quicksearch);
    $quick_search_html = '';
    $foodtype_dropdown_html = '';
    if(!empty($data['food_type'])) {
      $quick_search_html .= '<div class="container">
        <div class="heading-title">
          <h2>'.$this->lang->line('quick_search').'</h2>
        </div>
        <div class="slider-new-add">
          <div class="slider-arrow">   
            <div id="customNav" class="arrow"></div>
          </div>
          <div class="quick-searches-slider owl-carousel">';
      foreach ($data['food_type'] as $ftkey => $ftvalue) {
        $food_type_image = (file_exists(FCPATH.'uploads/'.$ftvalue->food_type_image) && $ftvalue->food_type_image!='') ? image_url.$ftvalue->food_type_image : default_img;
        $selected_classes = (in_array($ftvalue->entity_id, $selectedfoodtype_arr)) ? 'selected borderClass':'';
        $quick_search_html .= '<div class="quick-searches-box '.$selected_classes.'" id="foodtype_'.$ftvalue->entity_id.'" onclick="getRestaurantsOnFilter(\'apply\',\'quicksearch_foodtype\',\'\','.$ftvalue->entity_id.')">
              <input type="hidden" name="quicksearch_foodtype" id="quicksearch_foodtype" value="'.$ftvalue->entity_id.'">
              <img src="'.$food_type_image.'" alt="'.$ftvalue->name.'" title="'.$ftvalue->name.'">
              <h5>'.$ftvalue->name.'</h5>
            </div>';
        $foodtype_dropdown_html .= '<option value="'.$ftvalue->entity_id.'">'.ucfirst($ftvalue->name).'</option>';
      }
      $quick_search_html .= '</div>
        </div>
      </div>';
    }
    //quick searches section :: end
    $array_view = array(
      'popular_restaurants'=>$ajax_popular_restaurants,
      'quick_searches'=>$quick_search_html,
      'foodtype_dropdown' => $foodtype_dropdown_html,
      'coupon_section_html' => $coupon_section_html,
      'countcoupon'=>$cuont_coupon
    );
    echo json_encode($array_view); exit;
  }
  //restaurant sort/filter section :: end
}