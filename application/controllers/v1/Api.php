<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
class Api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/api_model');
        $this->load->library('form_validation');
    }
    //common lang fucntion
    public function getLang($language_slug)
    {
        if($language_slug){
            $languages = $this->api_model->getLanguages($language_slug);
            $this->current_lang = $languages->language_slug;
            $this->lang->load('messages_lang', $languages->language_directory);
        } else {
            $default_lang = $this->common_model->getdefaultlang();
            $this->current_lang = $default_lang->language_slug;
            $this->lang->load('messages_lang', $default_lang->language_directory);
        }
    }
    // Registration API
    public function registration_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $first_name = $decrypted_data->FirstName;
            $last_name = $decrypted_data->LastName;
            $phone_number = $decrypted_data->PhoneNumber;
            $phone_code = $decrypted_data->phone_code;
            $email = strtolower($decrypted_data->Email);
            $password = $decrypted_data->Password;
            $firebase_token = $decrypted_data->firebase_token;
            $language_slug = $decrypted_data->language_slug;
            $referral_code_post = $decrypted_data->referral_code;
        }else{
            $this->getLang($this->post('language_slug'));
            $first_name = $this->post('FirstName');
            $last_name = $this->post('LastName');
            $phone_number = $this->post('PhoneNumber');
            $phone_code = $this->post('phone_code');
            $email = strtolower($this->post('Email'));
            $password = $this->post('Password');
            $firebase_token = $this->post('firebase_token');
            $language_slug = $this->post('language_slug');
            $referral_code_post = $this->post('referral_code');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($first_name != "" && $last_name != "" && $phone_number != "" && $password != "" && $phone_code != "")
        {
            /*$checkRecord = $this->api_model->getRecord('users', 'mobile_number', $phone_number);
            $checkemail = $this->api_model->getRecord('users', 'email', $email);*/
            $checkRecord = (!empty($email)) ? $this->api_model->getUserRecord($phone_number,$email) : $this->api_model->getUserRecord($phone_number);
            if(empty($checkRecord))
            {   
                $referral_code = random_string('alnum', 8);
                $stripe_customer_id = $this->api_model->add_new_customer_in_stripe(trim($first_name),trim($last_name),trim($phone_code),trim($phone_number),trim($email));
                $addUser = array(
                    'mobile_number' => trim($phone_number),
                    'phone_code' => trim($phone_code),
                    'first_name' => trim($first_name),
                    'last_name' => trim($last_name),
                    'password' => md5(SALT.$password),
                    'user_type' => 'User',
                    'status' => 1,
                    'referral_code'=>$referral_code,
                    'stripe_customer_id'=>($stripe_customer_id)?$stripe_customer_id:NULL,
                );
                if(!empty($email)){
                    $addUser['email'] = trim($email);
                }
                if (!empty($_FILES['image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/profile';
                    $config['allowed_types'] = 'jpg|png|jpeg';
                    $config['encrypt_name'] = TRUE; 
                    // create directory if not exists
                    if (!@is_dir('uploads/profile')) {
                        @mkdir('./uploads/profile', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('image'))
                    {  
                        $img = $this->upload->data();
                        //Code for compress image :: Start
                        $fileName = basename($img['file_name']);                   
                        $imageUploadPath = './uploads/profile/'. $fileName; 
                        $imageTemp = $_FILES["image"]["tmp_name"];
                        $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                        //Code for compress image :: End
                        $addUser['image'] = "profile/".$img['file_name'];
                    }
                    else
                    {
                        $data['Error'] = $this->upload->display_errors(); 
                        $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                    }
                } 
                //referral code
                if ($referral_code_post) {
                    $getUser = $this->api_model->getRecord('users', 'referral_code',$referral_code_post);
                    if(!empty($getUser)){
                        $UserID = $this->api_model->addRecord('users', $addUser);
                        //send otp start 
                        $this->common_model->generateOTP($UserID);
                        $user_record = $this->api_model->getRecord('users','entity_id',$UserID);
                        $sms = $user_record->user_otp.$this->lang->line('your_otp');
                        $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                        $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                        $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                        if($user_record->email){
                            $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$language_slug); //send email
                        }
                        //send otp end 
                        $login = $this->api_model->getRegisterRecord('users', $UserID);
                        if($UserID)
                        {
                            if (!empty($firebase_token)) {
                                $data = array('device_id' => $firebase_token, 'referral_code_used'=>$referral_code_post);
                                $this->api_model->updateUser('users', $data, 'entity_id', $UserID);
                            } else {
                                $data = array('referral_code_used'=>$referral_code_post);
                                $this->api_model->updateUser('users', $data, 'entity_id', $UserID);
                            }
                            $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';
                            $rating = $this->api_model->getRatings($login->entity_id);
                            $review = (!empty($rating))?$rating->rating:'';
                            $login_detail = array(
                                'FirstName'=>$login->first_name,
                                'LastName'=>($login->last_name) ? $login->last_name : '',
                                'image'=>$image,
                                'PhoneNumber'=>$login->mobile_number,
                                'phone_code'=>$login->phone_code,
                                'referral_code'=>$login->referral_code,
                                'user_otp'=>$login->user_otp,
                                'UserID'=>$login->entity_id,
                                'user_earning_points'=>$login->wallet,
                                'notification'=>$login->notification,
                                'rating'=>$review,
                                'Email'=>$login->email
                            );
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('User' => $login_detail,'active'=>false,'status'=>1,'message' => $this->lang->line('registration_success'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['User' => $login_detail,'active'=>false,'status'=>1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        } else {
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 0, 'message' => $this->lang->line('registration_fail'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 0, 'message' => $this->lang->line('registration_fail')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                            }                    
                        }
                    } else {
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('status' => 0, 'message' => $this->lang->line('wrong_referral_code'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['status' => 0, 'message' => $this->lang->line('wrong_referral_code')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                            exit;
                        }
                    }
                } elseif(empty($referral_code_post)) {
                    $UserID = $this->api_model->addRecord('users', $addUser);
                    //send otp start 
                    $this->common_model->generateOTP($UserID);
                    $user_record = $this->api_model->getRecord('users','entity_id',$UserID);
                    $sms = $user_record->user_otp.$this->lang->line('your_otp');
                    $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                    $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                    $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                    if($user_record->email){
                        $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$language_slug); //send email
                    }
                    //send otp end 
                    $login = $this->api_model->getRegisterRecord('users', $UserID);
                    if($UserID) {
                        if (!empty($firebase_token)) {
                            $data = array('device_id' => $firebase_token);
                            $this->api_model->updateUser('users', $data, 'entity_id', $UserID);
                        }
                        $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';
                        $rating = $this->api_model->getRatings($login->entity_id);
                        $review = (!empty($rating))?$rating->rating:'';
                        $login_detail = array(
                            'FirstName'=>$login->first_name,
                            'LastName'=>($login->last_name) ? $login->last_name : '',
                            'image'=>$image,
                            'PhoneNumber'=>$login->mobile_number,
                            'phone_code'=>$login->phone_code,
                            'referral_code'=>$login->referral_code,
                            'user_otp'=>$login->user_otp,
                            'UserID'=>$login->entity_id,
                            'user_earning_points'=>$login->wallet,
                            'notification'=>$login->notification,
                            'rating'=>$review,
                            'Email'=>$login->email
                        );
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('User' => $login_detail,'active'=>false,'status'=>1,'message' => $this->lang->line('registration_success'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['User' => $login_detail,'active'=>false,'status'=>1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else {
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('status' => 0, 'message' => $this->lang->line('registration_fail'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['status' => 0, 'message' => $this->lang->line('registration_fail')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }                    
                    }
                }
            } else {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0,'message' => $this->lang->line('user_exist'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0,'message' => $this->lang->line('user_exist')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $this->lang->line('regi_validation'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $this->lang->line('regi_validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    // Login API
    public function login_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $phone_number = $decrypted_data->PhoneNumber;
            $password = $decrypted_data->Password;
            $firebase_token = $decrypted_data->firebase_token;
            $Email = $decrypted_data->Email;
            $phone_code = $decrypted_data->phone_code;
        }else{
            $this->getLang($this->post('language_slug'));
            $phone_number = $this->post('PhoneNumber');
            $password = $this->post('Password');
            $firebase_token = $this->post('firebase_token');
            $Email = $this->post('Email');
            $phone_code = $this->post('phone_code');
        }
        if($phone_number && $phone_code && $phone_code!='undefined') {
            $login = $this->api_model->getLogin($password, NULL, $phone_number, $phone_code);            
        } elseif ($Email) {
            $login = $this->api_model->getLogin($password, $Email, NULL);
        } 
        if(empty($login->referral_code) && !empty($login)) {
            $referral_code = random_string('alnum', 8);
            $update = array(
                'referral_code'=>$referral_code
            );
            $this->api_model->updateUser('users',$update,'entity_id',$login->entity_id);
            if($phone_number) {
                $login = $this->api_model->getLogin($password, NULL, $phone_number, $phone_code);
            } elseif ($Email) {
                $login = $this->api_model->getLogin($password, $Email, NULL);
            }
        }
        if(empty($login->stripe_customer_id) && !empty($login)){
            $stripe_customer_id = $this->api_model->add_new_customer_in_stripe($login->first_name,$login->last_name,$login->phone_code,$login->mobile_number,$login->email);
            if($stripe_customer_id){
                $update = array(
                    'stripe_customer_id'=>$stripe_customer_id
                );
                $this->api_model->updateUser('users',$update,'entity_id',$login->entity_id);
            }
        }
        if(!empty($login)){
            if($login->is_deleted == 1){
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('user_id'=>$login->entity_id, 'PhoneNumber'=>$login->mobile_number, 'phone_code'=>$login->phone_code, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('delete_acc_validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['user_id'=>$login->entity_id, 'PhoneNumber'=>$login->mobile_number, 'phone_code'=>$login->phone_code, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('delete_acc_validation')], REST_Controller::HTTP_OK);
                }
            }
            if($login->status == 1){
                if (!empty($firebase_token)) {
                    $data = array('language_slug'=>$this->current_lang,'device_id'=>$firebase_token);
                } else {
                    $data = array('language_slug'=>$this->current_lang);
                }
                // update device
                //$image = ($login->image)?image_url.$login->image:'';
                $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';               
                $this->api_model->updateUser('users',$data,'entity_id',$login->entity_id);
                //get rating
                $rating = $this->api_model->getRatings($login->entity_id);
                $review = (!empty($rating))?$rating->rating:'';
                
                $login->wallet = ($login->wallet)?$login->wallet:0;
                $login_detail = array(
                    'FirstName'=>$login->first_name,
                    'LastName'=>($login->last_name) ? $login->last_name : '',
                    'image'=>$image,
                    'PhoneNumber'=>$login->mobile_number,
                    'phone_code'=>$login->phone_code,
                    'referral_code'=>$login->referral_code,
                    'user_otp'=>$login->user_otp,
                    'UserID'=>$login->entity_id,
                    'user_earning_points'=>$login->wallet,
                    'notification'=>$login->notification,
                    'rating'=>$review,
                    'Email'=>$login->email
                );
                $res_message = ($login->active == 1) ? $this->lang->line('login_success') : $this->lang->line('login_api_otp_resp');
                $is_user_active = ($login->active == 1) ? true : false;
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('login' => $login_detail,'status'=>1,'active'=>$is_user_active,'message' =>$res_message);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['login' => $login_detail,'status'=>1,'active'=>$is_user_active,'message' =>$res_message], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
            if ($login->status == 0) {
                $adminEmail = $this->api_model->getSystemOptoin('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 2,'message' => $this->lang->line('login_deactivedis').' '.$adminEmail->OptionValue,'email'=>$adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 2,'message' => $this->lang->line('login_deactivedis').' '.$adminEmail->OptionValue,'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        }
        else
        {
            $emailexist = $this->api_model->getRecord('users','mobile_number',$PhoneNumber);
            //if($emailexist)
            if($phone_number)
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' =>$this->lang->line('app_phone_login_error'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' =>$this->lang->line('app_phone_login_error')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('app_email_login_error'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('app_email_login_error')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
        }        
    }
    //verify OTP
    public function verifyOTP_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $phone_number = $decrypted_data->PhoneNumber;
            $password = $decrypted_data->Password;
            $active = $decrypted_data->active;
            $otp = $decrypted_data->otp;
            $social_media_id = $decrypted_data->social_media_id;
            $phone_code = $decrypted_data->phone_code;
            $forPasswordRecovery = $decrypted_data->forPasswordRecovery;
        }else{
            $this->getLang($this->post('language_slug'));
            $phone_number = $this->post('PhoneNumber');
            $password = $this->post('Password');
            $active = $this->post('active');
            $otp = $this->post('otp');
            $social_media_id = $this->post('social_media_id');
            $phone_code = $this->post('phone_code');
            $forPasswordRecovery = $this->post('forPasswordRecovery');
        }
        if(!empty($social_media_id)) {
            $login = $this->api_model->checksocial($social_media_id);
        } else if($forPasswordRecovery == 1) {
            $login = $this->api_model->getLogin(NULL, NULL, $phone_number, $phone_code);
        } else {
            $login = $this->api_model->getLogin($password, NULL, $phone_number, $phone_code);
        }
        if(!empty($login)){
            if($active == 1){
                if(!empty($otp)){
                    if(!empty($social_media_id)) {
                        $whereArray = array('user_otp'=>$otp,'social_media_id'=>$social_media_id);
                    } else {
                        $whereArray = array('user_otp'=>$otp,'mobile_number'=>$phone_number, 'phone_code'=>$phone_code);
                    }
                    $verify_otp = $this->api_model->getRecordMultipleWhere('users',$whereArray);
                    if(!empty($verify_otp)){
                        //otp is verified
                        if(!empty($social_media_id) && $phone_code && $phone_number) {
                            $data = array('active' => 1, 'mobile_number' => $phone_number, 'phone_code' => $phone_code);
                        } else {
                            $data = array('active' => 1);
                        }
                        $this->api_model->updateUser('users',$data,'entity_id',$login->entity_id);
                        if(!empty($social_media_id) && $phone_code && $phone_number) {
                            $login = $this->api_model->checksocial($social_media_id);
                        }
                        //get rating
                        $rating = $this->api_model->getRatings($login->entity_id);
                        $review = (!empty($rating))?$rating->rating:'';
                        $login->wallet = ($login->wallet)?$login->wallet:0;
                        $login_detail = array(
                            'FirstName' => $login->first_name,
                            'LastName' =>($login->last_name) ? $login->last_name : '',
                            'image' => (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '',
                            'PhoneNumber' => $login->mobile_number,
                            'phone_code' => $login->phone_code,
                            'referral_code' => $login->referral_code,
                            'user_otp' => $login->user_otp,
                            'UserID' => $login->entity_id,
                            'user_earning_points' => $login->wallet,
                            'notification' => $login->notification,
                            'rating' => $review,
                            'Email' => $login->email
                        );
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('login' => $login_detail,'active'=>true,'status'=>1,'message' => $this->lang->line('success'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['login' => $login_detail,'active'=>true,'status'=>1,'message' => $this->lang->line('success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else {
                        //wrong otp entered
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('status' => 0, 'active' => false, 'message' => $this->lang->line('wrong_otp'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['status' => 0, 'active' => false, 'message' => $this->lang->line('wrong_otp')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }
                    }
                } else {
                    //otp not entered
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'active' => false, 'message' => $this->lang->line('otp_empty'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'active' => false, 'message' => $this->lang->line('otp_empty')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                }
                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'active' => false,'message' => $this->lang->line('login_deactive'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'active' => false,'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                }                
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //get homepage
    public function getHome_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $isEvent = $decrypted_data->isEvent;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
            $distance = $decrypted_data->distance;
            $itemSearch = $decrypted_data->itemSearch;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
            $food = $decrypted_data->food;
            $rating = $decrypted_data->rating;
            $sortBy = $decrypted_data->sortBy;
            $category_id = $decrypted_data->category_id;
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
            $order_mode = $decrypted_data->orderMode;
            $restaurant_type = $decrypted_data->restaurant_type;
            $offersFreeDelivery = $decrypted_data->offersFreeDelivery;
            $availability = $decrypted_data->availability;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $isEvent = $this->post('isEvent');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $distance = $this->post('distance');
            $itemSearch = $this->post('itemSearch');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
            $food = $this->post('food');
            $rating = $this->post('rating');
            $sortBy = $this->post('sortBy');
            $category_id = $this->post('category_id');
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
            $order_mode = $this->post('orderMode');
            $restaurant_type = $this->post('restaurant_type');
            $offersFreeDelivery = $this->post('offersFreeDelivery');
            $availability = $this->post('availability');
        }
        $language_slug = ($language_slug) ? $language_slug : $this->current_lang;
        //wallet money changes
        if($user_id){
            $checktoken = $this->api_model->getRecordMultipleWhere('users', array('entity_id'=>$user_id));
            if($checktoken) {
                $current_wallet = $checktoken->wallet;
            } else {
                $current_wallet = '';
            }
        } else {
            $current_wallet = '';
        }
        $currency = $this->api_model->getSystemOptoin('currency');
        $currency_id = $currency->OptionValue;
        $currency_symbol = $this->api_model->getCurrencySymbol($currency_id);
        $min_order_amount = $this->api_model->getSystemOptoin('min_order_amount');
        $min_order_amount = $min_order_amount->OptionValue;        
        $enable_reviewarr = $this->api_model->getSystemOptoin('enable_review');
        $enable_review = $enable_reviewarr->OptionValue;
        $range = $this->common_model->getRange();
        if($isEvent == 1)
        {
            $latitude = ($latitude) ? $latitude : '';
            $longitude = ($longitude) ? $longitude : '';
            if($order_mode=='1')
            {
                $distance = ($distance) ? $distance : $range[2]->OptionValue;
            }
            else
            {
                $distance = ($distance) ? $distance : $range[1]->OptionValue;
            }            
            $searchItem = ($itemSearch) ? trim($itemSearch) : '';
            $restaurant = $this->api_model->getEventRestaurant($latitude,$longitude,$searchItem,$distance,$language_slug,$count,$page_no,$user_timezone,$restaurant_type);
            $slider = $this->api_model->getbanner();
            if(!empty($restaurant))
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('date'=>date("Y-m-d g:i A"),
                        'restaurant'=>$restaurant['data'],
                        'total_restaurant'=>$restaurant['count'],
                        'slider'=>$slider,
                        'enable_review'=>$enable_review,
                        'status' => 1,
                        'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'date'=>date("Y-m-d g:i A"),
                        'restaurant'=>$restaurant['data'],
                        'total_restaurant'=>$restaurant['count'],
                        'slider'=>$slider,
                        'enable_review'=>$enable_review,
                        'status' => 1,
                        'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code 
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 1,'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 1,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }                
            }  
        }
        else
        { // for home page
            if($latitude !="" && $longitude != "")
            {
                if($order_mode=='1')
                {
                    $distance = ($distance) ? $distance : $range[2]->OptionValue;
                }
                else
                {
                    $distance = ($distance) ? $distance : $range[1]->OptionValue;
                }                
                $searchItem = ($itemSearch) ? trim($itemSearch) : '';
                $sortBy = ($sortBy) ? $sortBy : 0;
                $category_id = ($category_id) ? $category_id : '';
                $offersFreeDelivery = ($offersFreeDelivery) ? true : false;
                $availability = ($availability) ? $availability : 0;
                $restaurant = $this->api_model->getHomeRestaurant($latitude,$longitude,$searchItem,$food,$rating,$distance,$language_slug,$count,$page_no,$sortBy,$category_id,$user_timezone,$order_mode,$restaurant_type,$offersFreeDelivery,$availability,$user_id);
                $slider = $this->api_model->getbanner();
                // $category = $this->api_model->getcategory($language_slug);
                $getFoodType = array();
                if(!empty($restaurant['data'])) {
                    $res_food_types = array_column($restaurant['data'], 'food_type');
                    $getFoodType = $this->api_model->getFoodType($language_slug,$res_food_types);
                }
                $StoreUrl = $this->api_model->getStoreUrl();
                $active_langs = $this->api_model->getActiveLanguages();
                $payment_details = array(
                    'use_sandbox'=>use_sandbox,
                    'SANDBOX_PAYPAL_URL_V1'=>SANDBOX_PAYPAL_URL_V1,
                    'SANDBOX_PAYPAL_URL_V2'=>SANDBOX_PAYPAL_URL_V2,                    
                    'LIVE_PAYPAL_URL_V1'=>LIVE_PAYPAL_URL_V1,
                    'LIVE_PAYPAL_URL_V2'=>LIVE_PAYPAL_URL_V2,                    
                );
                $social_details = array(
                    'facebook' => facebook,
                    'twitter' => twitter,
                    'linkedin' => linkedin,
                    'instagram' => instagram
                );
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'date'=>date("Y-m-d g:i A"),
                        'wallet_money'=>$current_wallet,
                        'currency'=>$currency_symbol->currency_symbol,
                        'restaurant'=>$restaurant['data'],
                        'slider'=>$slider,
                        'category'=>$category,
                        'food_types' => $getFoodType,
                        'minFilterDistance'=>$range[0]->OptionValue,
                        'maxFilterDistance'=>$range[1]->OptionValue,
                        'google_map_api_key'=>google_key,
                        'google_webclient_id'=>google_webclient_id,
                        'app_store_url'=>$StoreUrl[0]->OptionValue,
                        'play_store_url'=>$StoreUrl[1]->OptionValue,
                        'payment_details'=>$payment_details,
                        'social_details'=>$social_details,
                        'lanugages'=>$active_langs,
                        'minimum_order_amount'=>$min_order_amount,
                        'total_restaurant'=>$restaurant['count'],
                        'enable_review'=>$enable_review,
                        'status' => 1,
                        'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'date'=>date("Y-m-d g:i A"),
                        'wallet_money'=>$current_wallet,
                        'currency'=>$currency_symbol->currency_symbol,
                        'restaurant'=>$restaurant['data'],
                        'slider'=>$slider,
                        'category'=>$category,
                        'food_types' => $getFoodType,
                        'minFilterDistance'=>$range[0]->OptionValue,
                        'maxFilterDistance'=>$range[1]->OptionValue,
                        'google_map_api_key'=>google_key,
                        'google_webclient_id'=>google_webclient_id,
                        'app_store_url'=>$StoreUrl[0]->OptionValue,
                        'play_store_url'=>$StoreUrl[1]->OptionValue,
                        'payment_details'=>$payment_details,
                        'social_details'=>$social_details,
                        'lanugages'=>$active_langs,
                        'minimum_order_amount'=>$min_order_amount,
                        'total_restaurant'=>$restaurant['count'],
                        'enable_review'=>$enable_review,
                        'status' => 1,
                        'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
        }
    }
    //add review
    public function add_OrderReview_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $rating = $decrypted_data->rating;
            $review = $decrypted_data->review;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_id = $decrypted_data->user_id;
            $driver_id = $decrypted_data->driver_id;
            $order_id = $decrypted_data->order_id;            
            $driver_rating = $decrypted_data->driver_rating;
            $driver_review = $decrypted_data->driver_review;
        } 
        else
        {
            $this->getLang($this->post('language_slug'));
            $rating = trim($this->post('rating'));
            $review = trim($this->post('review'));
            $restaurant_id = trim($this->post('restaurant_id'));
            $user_id = trim($this->post('user_id'));
            $driver_id = trim($this->post('driver_id'));
            $order_id = trim($this->post('order_id'));
            $driver_rating = trim($this->post('driver_rating'));
            $driver_review = trim($this->post('driver_review'));
        }
        
        $restaurant = $this->api_model->getRestaurantreviewId($restaurant_id);
        if(!empty($restaurant))
        {
            $resto_content_id = $this->api_model->getResContentId($restaurant_id);
            if(($rating != '' && $review != '') || ($driver_rating != '' && $driver_review != ''))
            {
                if($rating != '' && $review != '')
                {
                    $add_data = array(
                        'rating'=>trim($rating),
                        'review'=>utf8_encode($review),
                        'restaurant_id'=>$restaurant_id,
                        'user_id'=>$user_id,
                        'order_id' => ($order_id) ? $order_id : '',
                        'order_user_id'=>'0',                                            
                        'status'=>1,
                        'created_by' => $user_id,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'restaurant_content_id'=>$resto_content_id
                    );
                    $this->api_model->addRecord('review', $add_data);
                }
                if($driver_rating != '' && $driver_review != '')
                {
                    $add_data = array(
                        'rating'=>trim($driver_rating),
                        'review'=>utf8_encode($driver_review),
                        'restaurant_id'=>$restaurant_id,
                        'user_id'=>$user_id,
                        'order_id' => ($order_id) ? $order_id : '',
                        'order_user_id'=>($driver_id)?$driver_id:'',                    
                        'status'=>1,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'restaurant_content_id'=>$resto_content_id
                    );
                    $this->api_model->addRecord('review', $add_data);
                }
                
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status'=>1,'message' => $this->lang->line('success_add')
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status'=>1,'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' =>  $this->lang->line('validation')
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' =>  $this->lang->line('validation')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array(
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code    
            }  
        }
    }
    public function addReview_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $rating = $decrypted_data->rating;
            $review = $decrypted_data->review;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_id = $decrypted_data->user_id;            
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $rating = $this->post('rating');
            $review = $this->post('review');
            $restaurant_id = $this->post('restaurant_id');
            $user_id = $this->post('user_id');            
        }
        if($rating != '' && $review != '')
        {
            $resto_content_id = $this->api_model->getResContentId($restaurant_id);
            $add_data = array(
                'rating' => trim($rating),
                'review' => utf8_encode($review),
                'restaurant_id' => $restaurant_id,
                'user_id' => $user_id,                
                'status' => 1,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'restaurant_content_id'=>$resto_content_id
            );
            $this->api_model->addRecord('review', $add_data);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status'=>1,'message' => $this->lang->line('success_add'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status'=>1,'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' =>  $this->lang->line('validation'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' =>  $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }    
    public function editProfile_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $first_name = $decrypted_data->first_name;
            $last_name = $decrypted_data->last_name;
            $notification = $decrypted_data->notification;
            $email = $decrypted_data->Email;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $first_name = $this->post('first_name');
            $last_name = $this->post('last_name');
            $notification = $this->post('notification');
            $email = $this->post('Email');
        }
        $tokenusr = $this->api_model->checkToken($user_id);
        if($tokenusr){
            $add_data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'notification' => $notification,
            );
            if(!empty($email)){
                //$checkemail = $this->api_model->getRecord('users', 'email', $email);
                $checkemail = $this->api_model->checkEmailExist($email,$user_id);
                if(empty($checkemail)){
                    $add_data['email'] = strtolower(trim($email));
                }else{
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $this->lang->line('email_exist'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['status' => 0,'message' => $this->lang->line('email_exist')], REST_Controller::HTTP_OK);
                    }
                }
            } else {
                $add_data['email'] = NULL;
            }
            if (!empty($_FILES['image']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = './uploads/profile';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['encrypt_name'] = TRUE; 
                // create directory if not exists
                if (!@is_dir('uploads/profile')) {
                    @mkdir('./uploads/profile', 0777, TRUE);
                }
                $this->upload->initialize($config);                  
                if ($this->upload->do_upload('image'))
                {  
                    $img = $this->upload->data();

                    //Code for compress image :: Start
                    $fileName = basename($img['file_name']);                   
                    $imageUploadPath = './uploads/profile/'. $fileName; 
                    $imageTemp = $_FILES["image"]["tmp_name"];
                    $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                    //Code for compress image :: End
                    
                    $add_data['image'] = "profile/".$img['file_name'];
                    //delete old image
                    if($tokenusr->image){
                        @unlink(FCPATH.'uploads/'.$tokenusr->image);
                    }
                }
                else
                {
                    $data['Error'] = $this->upload->display_errors(); 
                    $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                }
            }
            $this->api_model->updateUser('users', $add_data, 'entity_id', $user_id);
            $token = $this->api_model->checkToken($user_id);
            $token->wallet = ($token->wallet)?$token->wallet:0;
            $login_detail = array(
                'FirstName' => $token->first_name,
                'LastName' =>($token->last_name) ? $token->last_name : '',
                'image' => (file_exists(FCPATH.'uploads/'.$token->image) && $token->image!='') ? image_url.$token->image : '',
                'Email' => $token->email,
                'PhoneNumber' => $token->mobile_number,
                'phone_code' => $token->phone_code,
                'referral_code'=>$token->referral_code,
                'UserID' => $token->entity_id,
                'user_earning_points'=>$token->wallet,
                'notification' => $token->notification
            );
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('profile' => $login_detail, 'status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['profile' => $login_detail, 'status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
            }
            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //package avalability
    public function checkBookingAvailability_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $booking_date = $decrypted_data->booking_date;
            $people = $decrypted_data->people;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $booking_date = $this->post('booking_date');
            $people = $this->post('people');
            $restaurant_id = $this->post('restaurant_id');
            $user_timezone = $this->post('user_timezone');
        }
        if($booking_date != '' && $people != ''){
            $time = date('Y-m-d H:i:s',strtotime($booking_date));
            $date = date('Y-m-d H:i:s');
            $date = $this->common_model->getZonebaseCurrentTime($date,$user_timezone);
            if(date('Y-m-d', strtotime($booking_date)) == date('Y-m-d') && date($time) < date($date))
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status'=>0,'message' => $this->lang->line('greater_than_current_time'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status'=>0,'message' => $this->lang->line('greater_than_current_time')], REST_Controller::HTTP_OK); // OK      
                }                
            }
            else
            {
                $check = $this->api_model->getBookingAvailability($booking_date, $people, $restaurant_id,$user_timezone);
                date_default_timezone_set(default_timezone);//set default time zone
                if($check)
                {
                    
                    if($check == 'booking_available'){
                        $status = 1;
                        $msg = $this->lang->line($check);
                    } else if($check == 'booking_not_available_time') {
                        $status = 0;
                        //$msg = sprintf($this->lang->line($check), $booking_date);
                        $msg = $this->lang->line($check);
                    } else if($check == 'restaurant_closed') {
                        $status = 0;
                        $msg = $this->lang->line($check);
                        //$msg = sprintf($this->lang->line($check), $booking_date);
                    } else if($check['msg'] == 'booking_not_available_capacity') {
                        $status = 0;
                        if(isset($check['remaining_capacity']) && $check['remaining_capacity']){
                            $msg = sprintf($this->lang->line($check['err_msg']), $check['remaining_capacity']);
                        } else {
                            $msg = $this->lang->line($check['err_msg']);
                        }
                    } else if($check['msg'] == 'min_capacity_validation') {
                        $status = 0;
                        $msg = sprintf($this->lang->line($check['err_msg']), $check['minimum_capacity']);
                    }
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status'=>$status,'message' => $msg);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status'=>$status,'message' => $msg], REST_Controller::HTTP_OK); // OK  
                    }                    
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status'=>0,'message' => $this->lang->line('booking_not_available'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status'=>0,'message' => $this->lang->line('booking_not_available')], REST_Controller::HTTP_OK); // OK  
                    }                    
                }  
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //book event
    public function bookEvent_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $booking_date = $decrypted_data->booking_date;
            $name = $decrypted_data->name;
            $people = $decrypted_data->people;
            $restaurant_id = $decrypted_data->restaurant_id;
            $package_id = $decrypted_data->package_id;
            $user_timezone = $decrypted_data->user_timezone;
            $additional_request = $decrypted_data->additional_request;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $booking_date = $this->post('booking_date');
            $name = $this->post('name');
            $people = $this->post('people');
            $restaurant_id = $this->post('restaurant_id');
            $package_id = $this->post('package_id');
            $user_timezone = $this->post('user_timezone');
            $additional_request = $this->post('additional_request');
        }
        $tokenres = $this->api_model->checkToken($user_id);    
        if($tokenres){
                if($booking_date != '' && $people != '')
                {
                    $current_date_time = date('Y-m-d H:i:s');
                    $booking_date = $this->common_model->setZonebaseDateTime($booking_date,$user_timezone);
                    $resto_content_id = $this->api_model->getResContentId($restaurant_id);
                    $pkg_content_id = $this->api_model->getContentId($package_id,'restaurant_package');
                    $add_data = array(                   
                        'name' => $name,
                        'no_of_people' => $people,
                        'booking_date' => date('Y-m-d H:i:s',strtotime($booking_date)),
                        'restaurant_id' => $resto_content_id,
                        'user_id' => $user_id,
                        'package_id' => (isset($pkg_content_id->content_id))?$pkg_content_id->content_id:NULL,
                        'status' => 1,
                        'created_by' => $user_id,
                        'event_status' => 'pending',
                        'additional_request' => ($additional_request)?$additional_request:NULL,
                        'created_date'=>$current_date_time
                    ); 
                    $event_id = $this->api_model->addRecord('event',$add_data); 
                    $users = array(
                        'first_name' => $tokenres->first_name,
                        'last_name' => ($tokenres->last_name) ? $tokenres->last_name : ''
                    );
                    $taxdetail = $this->api_model->getRestaurantTax('restaurant', $restaurant_id, $flag="order");
                    $package = $this->api_model->getRecord('restaurant_package', 'entity_id', $package_id);
                    $package_detail = '';
                    if(!empty($package)){
                        $package_detail = array(
                            'package_price' => $package->price,
                            'package_name' => $package->name,
                            'package_detail' => $package->detail
                        );
                    }
                    $serialize_array = array(
                        'restaurant_detail' => (!empty($taxdetail)) ? serialize($taxdetail) : '',
                        'user_detail' => (!empty($users)) ? serialize($users) : '',
                        'package_detail' => (!empty($package_detail)) ? serialize($package_detail) : '',
                        'event_id' => $event_id
                    );
                    $this->api_model->addRecord('event_detail', $serialize_array); 
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1, 'message' => $this->lang->line('success_add'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK  
                    }
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK  
                    }
                }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
            }            
        }    
    }
    //get booking
    public function getBooking_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($tokenres){
            $data['table_booking'] = $this->api_model->getTableBooking($user_id,$language_slug,$user_timezone);
            $data['event_booking'] = $this->api_model->getBooking($user_id,$user_timezone);

            $result['upcoming'] = array_merge($data['event_booking']['upcoming'], $data['table_booking']['upcoming']);
            $result['past'] = array_merge($data['event_booking']['past'], $data['table_booking']['past']);

            usort($result['upcoming'], function($a, $b) {
                if (date('Y-m-d',strtotime($a['booking_date'])) == date('Y-m-d',strtotime($b['booking_date']))) return 0;
                return (date('Y-m-d',strtotime($a['booking_date'])) < date('Y-m-d',strtotime($b['booking_date'])))?-1:1;
            });
            usort($result['past'], function($c, $d) {
                if (date('Y-m-d',strtotime($c['booking_date'])) == date('Y-m-d',strtotime($d['booking_date']))) return 0;
                return (date('Y-m-d',strtotime($c['booking_date'])) < date('Y-m-d',strtotime($d['booking_date'])))?-1:1;
            });

            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('upcoming_booking' => $result['upcoming'], 'past_booking' => $result['past'], 'status' => 1, 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['upcoming_booking' => $result['upcoming'], 'past_booking' => $result['past'], 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //delete address
    public function deleteAddress_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $address_id = $decrypted_data->address_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $address_id = $this->post('address_id');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres)
        {
            //check if address is default, if yes then set another recently added address as default.
            $this->api_model->check_default_address($user_id,$address_id);
            $this->api_model->deleteRecord('user_address', 'entity_id', $address_id);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1, 'message' => $this->lang->line('record_deleted'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1, 'message' => $this->lang->line('record_deleted')], REST_Controller::HTTP_OK); // OK
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //get recipe
    public function getRecipe_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $item_search = $decrypted_data->itemSearch;
            $food = $decrypted_data->food;
            $timing = $decrypted_data->timing;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
        }else{
            $this->getLang($this->post('language_slug'));
            $item_search = $this->post('itemSearch');
            $food = $this->post('food');
            $timing = $this->post('timing');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
        }
        $searchItem = ($item_search) ? $item_search : '';
        if($latitude != '' && $longitude != ''){
            $restaurant = $this->api_model->getHomeRestaurant($latitude, $longitude, $searchItem, $food, '', '', $this->current_lang, $count, $page_no);
            $restaurant_array = array();
            if(!empty($restaurant)){
                $restaurant_array = array_column($restaurant, 'restuarant_id');
            }
            $popular_item = $this->api_model->getRecipe($searchItem, $food, $timing, $this->current_lang, $restaurant_array);

            if($popular_item)
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('items' => $popular_item, 'status' => 1, 'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['items' => $popular_item, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
                }
                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK
            }            
        }
    }
    //delete booking
    public function deleteBooking_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $event_id = $decrypted_data->event_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $event_id = $this->post('event_id');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres){
            $this->api_model->deleteRecord('event', 'entity_id', $event_id);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1, 'message' => $this->lang->line('record_deleted'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1, 'message' => $this->lang->line('record_deleted')], REST_Controller::HTTP_OK); // OK
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //add address
    public function addAddress_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $address_id = $decrypted_data->address_id;
            $address = $decrypted_data->address;
            $landmark = $decrypted_data->landmark;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
            $zipcode = $decrypted_data->zipcode;
            $city = $decrypted_data->city;
            $state = $decrypted_data->state;
            $country = $decrypted_data->country;
            $address_label = $decrypted_data->address_label;
            $is_main = ($decrypted_data->is_main)?$decrypted_data->is_main:0;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $address_id = $this->post('address_id');
            $address = $this->post('address');
            $landmark = $this->post('landmark');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $zipcode = $this->post('zipcode');
            $city = $this->post('city');
            $state = $this->post('state');
            $country = $this->post('country');
            $address_label = $this->post('address_label');
            $is_main = ($this->post('is_main'))?intval($this->post('is_main')):0;
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres)
        {
            //New code add as per requirement :: Start                
            if($is_main>0)
            {
                $is_maindata = array('is_main'=>0);
                $this->api_model->updateUser('user_address',$is_maindata,'user_entity_id',$user_id); 
            }
            //New code add as per requirement :: End
            $add_data = array(
                'address' => $address,
                'landmark' => $landmark,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'zipcode' => $zipcode,
                'city' => $city,
                'state'=>$state,
                'country'=>$country,
                'address_label' => $address_label,
                'user_entity_id' => $user_id,
                'is_main' => $is_main
            );
            if($address_id){
                $this->api_model->updateUser('user_address', $add_data, 'entity_id', $address_id);
            }else{
                $address_id = $this->api_model->addRecord('user_address', $add_data);
            }
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('address_id' => $address_id, 'status' => 1, 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['address_id' => $address_id, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //get address
    public function getAddress_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $showonly_main = $decrypted_data->showonly_main;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $showonly_main = $this->post('showonly_main');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres){
            $address = $this->api_model->getAddress('user_address', 'user_entity_id', $user_id,$showonly_main);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('address' => $address, 'status' => 1, 'message' => $this->lang->line('success_add'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['address' => $address, 'status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }           
        }
    }
    //change address
    public function changePassword_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $old_password = $decrypted_data->old_password;
            $password = $decrypted_data->password;
            $confirm_password = $decrypted_data->confirm_password;
            $forPasswordRecovery = $decrypted_data->forPasswordRecovery;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $old_password = $this->post('old_password');
            $password = $this->post('password');
            $confirm_password = $this->post('confirm_password');
            $forPasswordRecovery = $this->post('forPasswordRecovery');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres){
            if($forPasswordRecovery == 1 || md5(SALT.$old_password) == $tokenres->password){
                if($confirm_password == $password){
                    $this->db->set('password',md5(SALT.$password));
                    $this->db->where('entity_id',$user_id);
                    $this->db->update('users');
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1, 'message' => $this->lang->line('success_password_change'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 1, 'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK
                    }                    
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 0, 'message' => $this->lang->line('confirm_password'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 0, 'message' => $this->lang->line('confirm_password')], REST_Controller::HTTP_OK); // OK
                    }                    
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('old_password'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('old_password')], REST_Controller::HTTP_OK); // OK
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //add order
    public function addOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));            
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $transaction_id = $decrypted_data->transaction_id;
            $restaurant_id = $decrypted_data->restaurant_id;
            $address_id = $decrypted_data->address_id;
            $coupon_name = $decrypted_data->coupon_name;
            $coupon_id = $decrypted_data->coupon_id;
            $coupon_type = $decrypted_data->coupon_type;
            $coupon_amount = $decrypted_data->coupon_amount;
            $coupon_discount = $decrypted_data->coupon_discount;
            $coupon_array = $decrypted_data->coupon_array;
            $subtotal = $decrypted_data->subtotal;
            $total_post = $decrypted_data->total;
            $delivery_charge = $decrypted_data->delivery_charge;
            $extra_comment = $decrypted_data->extra_comment;
            $delivery_instructions = $decrypted_data->delivery_instructions;
            $order_delivery = $decrypted_data->order_delivery;
            $order_date = $decrypted_data->order_date;
            $items = $decrypted_data->items;
            $used_earning = $decrypted_data->debited_amount;
            $is_wallet_applied = $decrypted_data->is_wallet_applied;
            $wallet_balance = $decrypted_data->wallet_balance;
            $payment_option = $decrypted_data->payment_option;
            $table_id = $decrypted_data->table_id;
            $is_parcel_order = $decrypted_data->is_parcel_order;
            $order_id = $decrypted_data->order_id;
            $payment_option_post = $decrypted_data->payment_option;
            $user_timezone = $decrypted_data->user_timezone;
            $order_timestamp = $decrypted_data->order_timestamp;
            //requests for guest checkout
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $first_name = $decrypted_data->first_name;
            $last_name = $decrypted_data->last_name;
            $phone_code = $decrypted_data->phone_code;
            $phone_number = $decrypted_data->phone_number;
            $email = $decrypted_data->email;
            $address_input = $decrypted_data->address_input;
            $landmark = $decrypted_data->landmark;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
            $zipcode = $decrypted_data->zipcode;
            $city = $decrypted_data->city;
            $state = $decrypted_data->state;
            $country = $decrypted_data->country;
            $address_label = $decrypted_data->address_label;
            $language_slug = $decrypted_data->language_slug;
            //creditcard fee
            $is_creditcard = $decrypted_data->is_creditcard;
            $is_service_fee_applied = $decrypted_data->is_service_fee_applied;
            $is_creditcard_fee_applied = $decrypted_data->is_creditcard_fee_applied;
            $service_taxval = $decrypted_data->service_taxval;
            $service_tax_typeval = $decrypted_data->service_tax_typeval;
            $service_feeval = $decrypted_data->service_feeval;
            $service_fee_typeval = $decrypted_data->service_fee_typeval;
            $creditcard_feeval = $decrypted_data->creditcard_feeval;
            $creditcard_fee_typeval = $decrypted_data->creditcard_fee_typeval;
            //driver tip
            $driver_tip = $decrypted_data->driver_tip;
            $tip_percent_val = $decrypted_data->tip_percent_val;
            //scheduled delivery
            $scheduled_date = $decrypted_data->scheduled_date;
            $slot_open_time = $decrypted_data->slot_open_time;
            $slot_close_time = $decrypted_data->slot_close_time;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $transaction_id = $this->post('transaction_id');
            $restaurant_id = $this->post('restaurant_id');
            $address_id = $this->post('address_id');
            $coupon_name = $this->post('coupon_name');
            $coupon_id = $this->post('coupon_id');
            $coupon_type = $this->post('coupon_type');
            $coupon_amount = $this->post('coupon_amount');
            $coupon_discount = $this->post('coupon_discount');
            $coupon_array = $this->post('coupon_array');
            $subtotal = $this->post('subtotal');
            $total_post = $this->post('total');
            $delivery_charge = $this->post('delivery_charge');
            $extra_comment = $this->post('extra_comment');
            $delivery_instructions = $this->post('delivery_instructions');
            $order_delivery = $this->post('order_delivery');
            $order_date = $this->post('order_date');
            $items = $this->post('items');
            $used_earning = $this->post('debited_amount');
            $is_wallet_applied = $this->post('is_wallet_applied');
            $wallet_balance = $this->post('wallet_balance');
            $payment_option = $this->post('payment_option');
            $table_id = $this->post('table_id');
            $is_parcel_order = $this->post('is_parcel_order');
            $order_id = $this->post('order_id');
            $payment_option_post = $this->post('payment_option');
            $user_timezone = $this->post('user_timezone');
            $order_timestamp = $this->post('order_timestamp');
            //requests for guest checkout
            $isLoggedIn = $this->post('isLoggedIn');
            $first_name = $this->post('first_name');
            $last_name = $this->post('last_name');
            $phone_code = $this->post('phone_code');
            $phone_number = $this->post('phone_number');
            $email = $this->post('email');
            $address_input = $this->post('address_input');
            $landmark = $this->post('landmark');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $zipcode = $this->post('zipcode');
            $city = $this->post('city');
            $state = $this->post('state');
            $country = $this->post('country');
            $address_label = $this->post('address_label');
            $language_slug = $this->post('language_slug');
            //creditcard fee
            $is_creditcard = $this->post('is_creditcard');
            $is_service_fee_applied = $this->post('is_service_fee_applied');
            $is_creditcard_fee_applied = $this->post('is_creditcard_fee_applied');
            $service_taxval = $this->post('service_taxval');
            $service_tax_typeval = $this->post('service_tax_typeval');
            $service_feeval = $this->post('service_feeval');
            $service_fee_typeval = $this->post('service_fee_typeval');
            $creditcard_feeval = $this->post('creditcard_feeval');
            $creditcard_fee_typeval = $this->post('creditcard_fee_typeval');
            //driver tip
            $driver_tip = $this->post('driver_tip');
            $tip_percent_val = $this->post('tip_percent_val');
            //scheduled delivery
            $scheduled_date = $this->post('scheduled_date');
            $slot_open_time = $this->post('slot_open_time');
            $slot_close_time = $this->post('slot_close_time');
        }
        $phone_number = ltrim($phone_number, '0');
       
        $scheduled_date = ($scheduled_date) ? date('Y-m-d',strtotime($scheduled_date)) : '';
        $slot_open_time = ($slot_open_time) ? date('H:i:s', strtotime($slot_open_time)) : '';
        $slot_close_time = ($slot_close_time) ? date('H:i:s', strtotime($slot_close_time)) : '';
        $scheduled_order_opentime = date('Y-m-d H:i:s', strtotime("$scheduled_date $slot_open_time"));
        $scheduled_order_closetime = date('Y-m-d H:i:s', strtotime("$scheduled_date $slot_close_time"));
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($isLoggedIn == 1) {
            $tokenres = $this->api_model->checkToken($user_id);
        } else {
            $tokenres = true;
            //validations on guest login
            if($first_name != "" && $phone_number != "" && $phone_code != "")
            {
                $tokenres = true;
                if($order_delivery == 'Delivery'){
                    if($address_input != "" && $latitude != "" && $longitude != "" && $zipcode != "") {
                        $tokenres = true;
                    } else {
                        $tokenres = false;
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('status' => 0,'message' => $this->lang->line('validation'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['status' => 0,'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }
                    }
                }
            } else {
                $tokenres = false;
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
        $coupon_amount = (empty($coupon_amount))?NULL:$coupon_amount;
        $coupon_id = ($coupon_id)?$coupon_id:NULL;
        $coupon_type = ($coupon_type=='null' || empty($coupon_type))?NULL:$coupon_type;
        $transaction_id = $transaction_id ? $transaction_id : '';

        //Code for check the restaturant open/close before order place :: Start
        $restaurantarr = $this->api_model->getRecordMultipleWhere('restaurant', array('entity_id' => $restaurant_id,'status' => 1));
        $restaurant_valid = 'na';
        if($restaurantarr && !empty($restaurantarr))
        {
            $restaurant_valid = $this->api_model->checkRestauranttime($restaurantarr->timings, $restaurantarr->enable_hours, $scheduled_date, $slot_open_time, $user_timezone);
        }
        //Code for check the restaturant open/close before order place :: End
        if($tokenres)
        {
            if($restaurant_valid == 'yes')
            {
                $coupon_array_apply = json_decode($coupon_array,true);
                $taxdetail = $this->api_model->getRestaurantTax('restaurant', $restaurant_id, $flag="order");
                $res_details = $this->api_model->getRestaurantForAddOrder('restaurant', $restaurant_id,$user_timezone);

                //Code for out of stock validaiton :: Start
                $itemDetailchk = json_decode($items,true);
                $isout_ofstock = "";
                if(!empty($itemDetailchk)){
                    foreach ($itemDetailchk['items'] as $key => $value)
                    {
                        $allow_scheduled_delivery = ($scheduled_date)?1:0;
                        $allow_scheduled_delivery = $taxdetail->allow_scheduled_delivery;
                        if($order_delivery=='DineIn')
                        {
                            $allow_scheduled_delivery = 0;
                        }                        
                        $data = $this->api_model->checkExist($value['menu_id'],$allow_scheduled_delivery);
                        if($data && !empty($data)){}
                        else{
                            $isout_ofstock .= $value['name'].",";                            
                        }
                    }
                    if($isout_ofstock!='')
                    {
                        $isout_ofstock = rtrim($isout_ofstock, ",");
                        $message_outofsock = sprintf($this->lang->line('outof_stockmessage'),$isout_ofstock);
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('status' => 3,'message' => $message_outofsock);                            
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['status' => 3,'message' => $message_outofsock], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                        }
                    }
                }
                //Code for out of stock validaiton :: End

                $total = 0;                
                if($order_id)
                {
                    //Code for coupon :: Code change as per multiple coupon :: Start
                    if(!empty($coupon_array_apply))
                    {
                        foreach ($coupon_array_apply as $cp_key => $cp_value)
                        {
                            if($cp_key==0)
                            {
                                $coupon_discount = $cp_value['coupon_discount'];
                                $coupon_name = $cp_value['coupon_name'];
                                $coupon_amount = $cp_value['coupon_amount'];
                                $coupon_type = $cp_value['coupon_type'];
                                $coupon_id = $cp_value['coupon_id'];
                            }
                            $coupon_uparray = array(
                                'coupon_discount'=>$cp_value['coupon_discount']
                            );
                            $this->api_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$order_id,'coupon_id'=>$cp_value['coupon_id']), $coupon_uparray);
                        }
                    }
                    //Code for coupon :: Code change as per multiple coupon :: End

                    $old_order_master = $this->api_model->getRecord('order_master','entity_id',$order_id);
                    //$new_service_fee = $old_order_master->tax_rate + $taxdetail->amount;
                    $new_subtotal = $old_order_master->subtotal + $subtotal;
                    //$new_total_rate = $old_order_master->total_rate + $total_post;
                    //New updated code for tax calculation :: Start
                    $new_service_taxcal = 0;
                    $new_total_rate = $new_subtotal;
                    $new_service_tax = $service_taxval;
                    if($service_tax_typeval == 'Percentage'){
                        $new_service_taxcal = round(($new_subtotal * $service_taxval) / 100,2);
                    }else{
                        $new_service_taxcal = $service_taxval; 
                    }
                    $new_total_rate = $new_subtotal + $new_service_taxcal;
                    //New updated code for tax calculation :: End
                    //New updated code for service fee calculation :: Start
                    $new_service_feecal = 0; $new_service_fee = 0;
                    if($is_service_fee_applied==true)
                    {
                        $new_service_fee = $service_feeval;
                        if($service_fee_typeval == 'Percentage'){
                            $new_service_feecal = round(($new_subtotal * $service_feeval) / 100,2);
                        }else{
                            $new_service_feecal = $service_feeval; 
                        }
                        $new_total_rate = $new_total_rate + $new_service_feecal;
                    }
                    //New updated code for service fee calculation :: End

                    //New updated code for service fee calculation :: Start
                    $new_creditcard_feecal = 0; $new_creditcard_fee = 0;
                    if($is_creditcard_fee_applied==true && $is_creditcard=='yes')
                    {
                        $new_creditcard_fee = $creditcard_feeval;
                        if($creditcard_fee_typeval == 'Percentage'){
                            $new_creditcard_feecal = round(($new_subtotal * $creditcard_feeval) / 100,2);
                        }else{
                            $new_creditcard_feecal = $creditcard_feeval; 
                        }
                        $new_total_rate = $new_total_rate + $new_creditcard_feecal;
                    }
                    //New updated code for service fee calculation :: End
                    $update_data = array(              
                        'user_id' => $user_id,
                        'restaurant_id' => $restaurant_id,
                        'transaction_id' => $transaction_id,
                        'address_id' => $address_id,
                        'coupon_id' => $coupon_id,
                        'order_status' => 'placed',
                        'order_date' => $this->common_model->setZonebaseDateTime($order_date,$user_timezone),
                        'scheduled_date' => ($scheduled_date) ? date('Y-m-d', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime,$user_timezone))) : null,
                        'slot_open_time' => ($slot_open_time) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime,$user_timezone))) : null,
                        'slot_close_time' => ($slot_close_time) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_closetime,$user_timezone))) : null,
                        'created_date' => date('Y-m-d H:i:s'),
                        'subtotal' => $new_subtotal,
                        'tax_rate' => $new_service_tax,
                        'tax_type' => $service_tax_typeval,
                        'service_fee' => $new_service_fee,
                        'service_fee_type' => $service_fee_typeval,
                        'creditcard_fee' => $new_creditcard_fee,
                        'creditcard_fee_type' => $creditcard_fee_typeval,
                        'coupon_type' => $coupon_type,
                        'coupon_amount' => ($coupon_amount) ? $coupon_amount : '',
                        'total_rate' => $new_total_rate,
                        'status' => 0,
                        'coupon_discount' => ($coupon_discount) ? $coupon_discount : '',
                        'delivery_charge' => ($delivery_charge) ? $delivery_charge : '',
                        'extra_comment' => $extra_comment,
                        'delivery_instructions' => $delivery_instructions,
                        'coupon_name' => $coupon_name,
                        'payment_option' => $payment_option_post,
                        'paid_status'=>($payment_option_post == 'paylater') ? 'unpaid' : 'paid'
                    );
                    if($table_id){
                        $update_data['table_id'] = $table_id;
                        $table_detail = $this->api_model->getRecord('table_master','entity_id',$table_id);
                        $taxdetail->table_number = $table_detail->table_number;
                    } 
                    if($order_delivery == 'Delivery')
                    {
                        $update_data['order_delivery'] = 'Delivery';
                    }
                    else if($order_delivery == 'DineIn')
                    {
                        $update_data['order_delivery'] = 'DineIn';
                        if($is_parcel_order == '1'){
                            $update_data['is_parcel_order'] = '1';
                        }
                    }
                    else
                    {
                        $update_data['order_delivery'] = 'PickUp';
                    }  
                    //to update existing order
                    $this->api_model->updateUser('order_master',$update_data,'entity_id',$order_id);
                    
                    //driver tip changes :: start
                    if($driver_tip && $driver_tip>0)
                    {
                        $tip_percent_val = (float)$tip_percent_val;
                        $update_tip = array(
                            'user_id'=>($user_id)?$user_id:0,
                            'tips_transaction_id'=>$transaction_id,
                            'tip_percentage' => ($tip_percent_val > 0)?$tip_percent_val:NULL,
                            'payment_option' => $payment_option,
                            'amount'=>$driver_tip,
                            'date'=>date('Y-m-d H:i:s')
                        );
                        $this->api_model->updateUser('tips',$update_tip,'order_id',$order_id);
                    }
                    //driver tip changes :: end

                    //append new items in old item details
                    $old_order_detail = $this->api_model->getRecord('order_detail','order_id',$order_id);
                    $old_item = unserialize($old_order_detail->item_detail);
                    $order_flag = $old_item[count($old_item) - 1]['order_flag'] + 1;
                    $itemDetail = json_decode($items,true);
                    $new_item = array();
                
                    if(!empty($itemDetail)){
                        foreach ($itemDetail['items'] as $key => $value) {
                            $menu_content_id = $this->api_model->getMenuContentID($value['menu_id']);
                            $menu_ids[] = $menu_content_id->content_id;
                            if($menu_content_id->is_combo_item == '1'){
                                //$new_item_name = $value['name'].'('.substr(str_replace("\r\n"," + ",$menu_content_id->menu_detail),0,-3).')';
                                $new_item_name = $value['name'].'('.str_replace("\r\n"," + ",$menu_content_id->menu_detail).')';
                            }else{
                                $new_item_name = $value['name'];
                            }
                            if($value['is_customize'] == 1){
                                $customization = array();
                                foreach ($value['addons_category_list'] as $k => $val) {
                                    $addonscust = array();
                                    foreach ($val['addons_list'] as $m => $mn) {
                                        if($value['is_deal'] == 1){
                                            $addonscust[] = array(
                                                'add_ons_id'=>$mn['add_ons_id'],
                                                'add_ons_name'=>$mn['add_ons_name'],
                                            );
                                        }else{
                                            $addonscust[] = array(
                                                'add_ons_id'=>$mn['add_ons_id'],
                                                'add_ons_name'=>$mn['add_ons_name'],
                                                'add_ons_price'=>$mn['add_ons_price']
                                            );
                                        }
                                    }
                                    $customization[] = array(
                                        'addons_category_id'=>$val['addons_category_id'],
                                        'addons_category'=>$val['addons_category'],
                                        'addons_list'=>$addonscust
                                    );
                                }
                               
                                $new_item = array(
                                    "item_name"=>$value['name'],
                                    "menu_content_id"=>$menu_content_id->content_id,
                                    "item_id"=>$value['menu_id'],
                                    "qty_no"=>$value['quantity'],
                                    "comment"=>$value['comment'],
                                    "rate"=>($value['price'])?$value['price']:'',
                                    "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>1,
                                    "is_combo_item"=>0,
                                    "combo_item_details" => '',
                                    "is_deal"=>$value['is_deal'],
                                    "subTotal"=>$value['subTotal'],
                                    "itemTotal"=>$value['itemTotal'],
                                    "order_flag"=>$order_flag,
                                    "addons_category_list"=>$customization
                                );
                                array_push($old_item, $new_item);
                            }else{
                                 $new_item = array(
                                    "item_name"=>$new_item_name,
                                    "menu_content_id"=>$menu_content_id->content_id,
                                    "item_id"=>$value['menu_id'],
                                    "qty_no"=>$value['quantity'],
                                    "comment"=>$value['comment'],
                                    "rate"=>($value['price'])?$value['price']:'',
                                    "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>0,
                                    "is_combo_item"=>$menu_content_id->is_combo_item,
                                    "combo_item_details"=> ($menu_content_id->is_combo_item == '1') ? str_replace("\r\n"," + ",$menu_content_id->menu_detail) : '',
                                    "is_deal"=>$value['is_deal'],
                                    "subTotal"=>$value['subTotal'],
                                    "itemTotal"=>$value['itemTotal'],
                                    "order_flag"=>$order_flag,
                                );
                                array_push($old_item, $new_item);
                            } 
                        }   
                    }
                    
                    $address = $this->api_model->getAddress('user_address', 'entity_id', $address_id);
                    $user_detail = array(
                        'first_name'=>$tokenres->first_name,
                        'last_name'=>($tokenres->last_name)?$tokenres->last_name:'',
                        'address_id'=>$address_id,
                        'address'=>($address)?$address[0]->address:'',
                        'landmark'=>($address)?$address[0]->landmark:'',
                        'zipcode'=>($address)?$address[0]->zipcode:'',
                        'city'=>($address)?$address[0]->city:'',
                        'address_label'=>($address)?$address[0]->address_label:'',
                        'latitude'=>($address)?$address[0]->latitude:'',
                        'longitude'=>($address)?$address[0]->longitude:'',
                    );
                    $order_detail = array(
                        //'order_id'=>$order_id,
                        'user_name'=>($isLoggedIn!='1') ? $first_name.' '.$last_name:$tokenres->first_name.' '.$tokenres->last_name,
                        'user_mobile_number'=>($isLoggedIn!='1') ? $phone_code.$phone_number: $tokenres->phone_code.$tokenres->mobile_number,
                        'user_detail' => serialize($user_detail),
                        'item_detail' => serialize($old_item),
                        'restaurant_detail' => serialize($taxdetail),
                        'is_updateorder' => '1'
                    );
                    $this->api_model->updateUser('order_detail',$order_detail,'order_id',$order_id);
                }
                else
                { 
                    //Code for add the coupon value in relation table and first value add in order master table :: Start  
                    if(!empty($coupon_array_apply))
                    {
                        foreach ($coupon_array_apply as $cp_key => $cp_value)
                        {
                            $ordder_cpnarr[] = array(
                                'order_id'=> '',
                                'coupon_id'=> $cp_value['coupon_id'],
                                'coupon_type'=> $cp_value['coupon_type'],
                                'coupon_amount'=> $cp_value['coupon_amount'],
                                'coupon_discount'=> $cp_value['coupon_discount'],
                                'coupon_name'=> $cp_value['coupon_name']
                            );
                            if($cp_key==0)
                            {
                                $coupon_discount = $cp_value['coupon_discount'];
                                $coupon_name = $cp_value['coupon_name'];
                                $coupon_amount = $cp_value['coupon_amount'];
                                $coupon_type = $cp_value['coupon_type'];
                                $coupon_id = $cp_value['coupon_id'];
                            }
                        }                        
                    }
                    //Code for add the coupon value in relation table and first value add in order master table :: End

                    $add_data = array(
                        'user_id' => ($user_id)?$user_id:0,
                        'restaurant_id' => $restaurant_id,
                        'transaction_id' => $transaction_id,
                        'address_id' => ($address_id)?$address_id:NULL,
                        'coupon_id' => $coupon_id,
                        'order_status' => 'placed',
                        'order_date' => $this->common_model->setZonebaseDateTime($order_date,$user_timezone),
                        'scheduled_date' => ($scheduled_date) ? date('Y-m-d', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime,$user_timezone))) : null,
                        'slot_open_time' => ($slot_open_time) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_opentime,$user_timezone))) : null,
                        'slot_close_time' => ($slot_close_time) ? date('H:i:s', strtotime($this->common_model->setZonebaseDateTime($scheduled_order_closetime,$user_timezone))) : null,
                        'created_date' => date('Y-m-d H:i:s'),
                        'subtotal' => $subtotal,
                        'used_earning'=>($used_earning)?$used_earning:0,
                        'tax_rate' => $service_taxval,
                        'tax_type' => $service_tax_typeval,
                        'service_fee' => ($is_service_fee_applied == true ) ? $service_feeval : 0,
                        'service_fee_type' => ($is_service_fee_applied == true) ? $service_fee_typeval : 0,
                        'creditcard_fee' => ($is_creditcard_fee_applied == true && $is_creditcard=='yes') ? $creditcard_feeval : 0,
                        'creditcard_fee_type' => ($is_creditcard_fee_applied == true && $is_creditcard=='yes') ? $creditcard_fee_typeval : '',
                        'coupon_type' => $coupon_type,
                        'coupon_amount' => $coupon_amount,
                        'total_rate' => $total_post,
                        'status' => 0,
                        'coupon_discount' => ($coupon_discount) ? $coupon_discount : NULL,
                        'delivery_charge' => ($delivery_charge) ? $delivery_charge : NULL,
                        'extra_comment' => $extra_comment,
                        'delivery_instructions' => $delivery_instructions,
                        'coupon_name' => $coupon_name,
                        'payment_option'=>$payment_option,
                        'payment_status'=>($payment_option == 'CardOnline')?'unpaid':NULL,                      
                        'paid_status'=>($payment_option == 'paylater') ? 'unpaid' : 'paid',
                        'order_timestamp'=>($order_timestamp)?$order_timestamp:NULL,
                    );

                    if($table_id){
                        $add_data['table_id'] = $table_id;
                        $table_detail = $this->api_model->getRecord('table_master','entity_id',$table_id);
                        $taxdetail->table_number = $table_detail->table_number;
                    }
                    if($order_delivery == 'Delivery')
                    {
                        $add_data['order_delivery'] = 'Delivery';
                    }
                    else if($order_delivery == 'DineIn')
                    {
                        $add_data['order_delivery'] = 'DineIn';
                        if($is_parcel_order == '1'){
                            $add_data['is_parcel_order'] = '1';
                        }
                    }
                    else
                    {
                        $add_data['order_delivery'] = 'PickUp';
                    }  
                    $order_id = $this->api_model->addRecord('order_master',$add_data); 

                    //Code for add the coupon value in relation table and first value add in order master table :: Start
                    if(!empty($ordder_cpnarr))
                    {
                        foreach($ordder_cpnarr as $cp_key => $cp_value)
                        {
                            $ordder_cpnarr[$cp_key]['order_id'] = $order_id;                  
                        }
                        $this->api_model->inserBatch('order_coupon_use',$ordder_cpnarr);
                    }
                    //Code for add the coupon value in relation table and first value add in order master table :: End
                     
                    //driver tip changes :: start
                    if($driver_tip && $driver_tip>0)
                    {
                        $tip_percent_val = (float)$tip_percent_val;
                        $add_tip = array(
                            'order_id'=>$order_id,
                            'user_id'=>($user_id)?$user_id:0,
                            'tips_transaction_id'=>$transaction_id,
                            'tip_percentage' => ($tip_percent_val > 0)?$tip_percent_val:NULL,
                            'payment_option'=>$payment_option,
                            'amount'=>$driver_tip,
                            'date'=>date('Y-m-d H:i:s')
                        );
                        $tips_id = $this->api_model->addRecord('tips',$add_tip);
                    }
                    //driver tip changes :: end
                    /*wallet money changes start*/
                    if($is_wallet_applied == 1) { 
                        //updating wallet in users table after deduction
                        $addWallet = array(
                            'wallet'=>$wallet_balance // remaining wallet balance
                        );
                        $this->api_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $addWallet);
                        //add wallet history - amount debited.
                        $addWalletHistory = array(
                            'user_id'=>$user_id,
                            'order_id'=>$order_id,
                            'amount'=>$used_earning,
                            'debit'=>1,
                            'reason'=>'money_debited_for_order',
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->api_model->addRecord('wallet_history',$addWalletHistory);
                    }
                    /*wallet money changes end*/
                    /*earning points changes start*/
                    if($total_post > 0){ 
                        // update the used and new earning points to the user's account
                        $users_wallet = $this->api_model->getUsersWalletMoney($user_id);
                        $current_wallet = $users_wallet->wallet;
                        // get earning to convert to earning points
                        $minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
                        $minimum_subtotalval =0;
                        if($minimum_subtotal && !empty($minimum_subtotal))
                        {
                            $minimum_subtotalval = $minimum_subtotal->OptionValue;
                        }

                        $new_earning_points = 0;
                        $points = $this->db->get_where('system_option',array('OptionSlug'=>'earning_1_point'))->first_row();
                        if($minimum_subtotalval>0 && intval($subtotal)>=$minimum_subtotalval)
                        {
                            $earned_points = round(($subtotal*$points->OptionValue)/100);
                            $new_earning_points = round($current_wallet + $earned_points); //new wallet money
                        }
                        if($new_earning_points > 0){
                            $data = array('wallet' => $new_earning_points);            
                            $this->api_model->updateUser('users',$data,'entity_id',$user_id);
                            //add wallet history - amount credited.
                            $addWalletHistory = array(
                                'user_id'=>$user_id,
                                'order_id'=>$order_id,
                                'amount'=>$earned_points,
                                'credit'=>1,
                                'reason'=>'money_credited_for_order',
                                'created_date' => date('Y-m-d H:i:s')
                            );
                            $this->api_model->addRecord('wallet_history',$addWalletHistory);
                        }
                    }
                    /*earning points changes end*/
                    //get wallet money
                    $getuserrecord = $this->api_model->getRecord('users','entity_id',$user_id);
                    $userwallet = $getuserrecord->wallet;
                    //add items
                    $itemDetail = json_decode($items,true);
                    $add_item = array();
                
                    if(!empty($itemDetail)){
                        foreach ($itemDetail['items'] as $key => $value) {
                            $menu_content_id = $this->api_model->getMenuContentID($value['menu_id']);
                            $menu_ids[] = $menu_content_id->content_id;
                            if($menu_content_id->is_combo_item == '1'){
                                //$new_item_name = $value['name'].'('.substr(str_replace("\r\n"," + ",$menu_content_id->menu_detail),0,-3).')';
                                $new_item_name = $value['name'].'('.str_replace("\r\n"," + ",$menu_content_id->menu_detail).')';
                            }else{
                                $new_item_name = $value['name'];
                            }
                            if($value['is_customize'] == 1){
                                $customization = array();
                                foreach ($value['addons_category_list'] as $k => $val) {
                                    $addonscust = array();
                                    foreach ($val['addons_list'] as $m => $mn) {
                                        if($value['is_deal'] == 1){
                                            $addonscust[] = array(
                                                'add_ons_id'=>$mn['add_ons_id'],
                                                'add_ons_name'=>$mn['add_ons_name'],
                                            );
                                        }else{
                                            $addonscust[] = array(
                                                'add_ons_id'=>$mn['add_ons_id'],
                                                'add_ons_name'=>$mn['add_ons_name'],
                                                'add_ons_price'=>$mn['add_ons_price']
                                            );
                                        }
                                    }
                                    $customization[] = array(
                                        'addons_category_id'=>$val['addons_category_id'],
                                        'addons_category'=>$val['addons_category'],
                                        'addons_list'=>$addonscust
                                    );
                                }
                               
                                $add_item[] = array(
                                    "item_name"=>$value['name'],
                                    "menu_content_id"=>$menu_content_id->content_id,
                                    "item_id"=>$value['menu_id'],
                                    "qty_no"=>$value['quantity'],
                                    "comment"=>(isset($value['comment'])) ? $value['comment'] : '',
                                    "rate"=>($value['price'])?$value['price']:'',
                                    "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>1,
                                    "is_combo_item"=>0,
                                    "combo_item_details" => '',
                                    "is_deal"=>$value['is_deal'],
                                    "subTotal"=>$value['subTotal'],
                                    "itemTotal"=>$value['itemTotal'],
                                    "order_flag"=>1,
                                    "addons_category_list"=>$customization
                                );
                            }else{
                                 $add_item[] = array(
                                    "item_name"=>$value['name'],
                                    "menu_content_id"=>$menu_content_id->content_id,
                                    "item_id"=>$value['menu_id'],
                                    "qty_no"=>$value['quantity'],
                                    "comment"=>(isset($value['comment'])) ? $value['comment'] : '',
                                    "rate"=>($value['price'])?$value['price']:'',
                                    "offer_price"=>($value['offer_price'])?$value['offer_price']:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>0,
                                    "is_combo_item"=>$menu_content_id->is_combo_item,
                                    "combo_item_details"=> ($menu_content_id->is_combo_item == '1') ? str_replace("\r\n"," + ",$menu_content_id->menu_detail) : '',
                                    "is_deal"=>$value['is_deal'],
                                    "subTotal"=>$value['subTotal'],
                                    "itemTotal"=>$value['itemTotal'],
                                    "order_flag"=>1,
                                );
                            } 
                        }   
                    }
                    if($isLoggedIn == 1) {
                        $address = $this->api_model->getAddress('user_address', 'entity_id', $address_id);
                        $user_detail = array(
                            'first_name'=>$tokenres->first_name,
                            'last_name'=>($tokenres->last_name)?$tokenres->last_name:'',
                            'address_id'=>$address_id,
                            'address'=>($address)?$address[0]->address:'',
                            'landmark'=>($address)?$address[0]->landmark:'',
                            'zipcode'=>($address)?$address[0]->zipcode:'',
                            'city'=>($address)?$address[0]->city:'',
                            'address_label'=>($address)?$address[0]->address_label:'',
                            'latitude'=>($address)?$address[0]->latitude:'',
                            'longitude'=>($address)?$address[0]->longitude:'',
                        );
                    } else {
                        $user_detail = array(
                            'first_name'=>$first_name,
                            'last_name'=>($last_name)?$last_name:'',
                            'phone_code'=>($phone_code)?$phone_code:'',
                            'phone_number'=>($phone_number)?$phone_number:'',
                            'email'=>($email)?trim($email):'',
                            'address'=>($address_input)?$address_input:'',
                            'landmark'=>($landmark)?$landmark:'',
                            'zipcode'=>($zipcode)?$zipcode:'',
                            'city'=>($city)?$city:'',
                            'address_label'=>($address_label)?$address_label:'',
                            'latitude'=>($latitude)?$latitude:'',
                            'longitude'=>($longitude)?$longitude:'',
                        );
                    }
                    $order_detail = array(
                        'order_id'=>$order_id,
                        'user_name'=>($isLoggedIn!='1') ? $first_name.' '.$last_name:$tokenres->first_name.' '.$tokenres->last_name,
                        'user_mobile_number'=>($isLoggedIn!='1') ? $phone_code.$phone_number: $tokenres->phone_code.$tokenres->mobile_number,
                        'user_detail' => serialize($user_detail),
                        'item_detail' => serialize($add_item),
                        'restaurant_detail' => serialize($taxdetail),
                    );
                    $this->api_model->addRecord('order_detail',$order_detail);

                    if($order_id) {
                        $encryptval = $this->common_model->base64UrlEncode($order_id);
                        if($isLoggedIn != 1) {
                            $guest_name = $first_name.' '.$last_name;
                            $guest_email = ($email) ? trim($email) : '';
                            $guest_phncode = ($phone_code) ? $phone_code : NULL;
                            $guest_phn_no = ($phone_number) ? $phone_number : NULL;
                            $track_order_link = "<a href='".base_url().'order/guest_track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('click_here') ."</a> ".$this->lang->line('to_track_order');
                            $track_order_link_viasms = base_url().'order/guest-track-order/sms/'.$encryptval;
                        } else {
                            $guest_name = $tokenres->first_name.' '.$tokenres->last_name;
                            $guest_email = ($tokenres->email) ? trim($tokenres->email) : '';
                            $guest_phncode = ($tokenres->phone_code) ? $tokenres->phone_code : NULL;
                            $guest_phn_no = ($tokenres->mobile_number) ? $tokenres->mobile_number : NULL;
                            $track_order_link = "<a href='".base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id))."' class='btn'>" . $this->lang->line('click_here') ."</a> ".$this->lang->line('to_track_order');
                            $track_order_link_viasms = base_url().'order/track-order/sms/'.$encryptval;
                        }
                        if($guest_email) {
                            $order_records_for_invoice = $this->api_model->getEditDetail($order_id);
                            $menu_item_for_invoice = $this->api_model->getInvoiceMenuItem($order_id);
                            $this->common_model->send_email_to_guest($guest_name, $taxdetail->name, $order_id, $total_post,$guest_email, $language_slug, $order_records_for_invoice, $menu_item_for_invoice, $track_order_link,$user_timezone);
                        }

                        if($guest_phncode && $guest_phn_no) {
                            $this->common_model->send_sms_to_guest($guest_phncode, $guest_phn_no, $order_id, $taxdetail->name,$track_order_link_viasms);
                        }
                    }
                }
                //Code for send the notification to the Branch admin :: Start :: 12-10-2020
                $restuser_device = $this->api_model->getBranchAdminDevice($restaurant_id);
                // if($restuser_device && trim($restuser_device->device_id)!='' && $restuser_device->notification == 1)
                if($restuser_device)
                {
                    for($nit=0;$nit<count($restuser_device);$nit++)
                    {
                        if($restuser_device && trim($restuser_device[$nit]->device_id)!='' && $restuser_device[$nit]->notification == 1 && $restuser_device[$nit]->status == 1)
                        {
                            $brancha_device_id = $restuser_device[$nit]->device_id;
                            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$restuser_device[$nit]->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('push_new_order'),$order_id);
                            $fields['to'] = $restuser_device[$nit]->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                            $fields['notification']['title'] = $this->lang->line('admin_app_name');
                            $fields['data'] = array ('screenType'=>'order','order_id'=>$order_id);
                           
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
                    }
                }
                //Code for send the notification to the Branch admin :: End :: 12-10-2020

                //Code for send the notification to the Restaurant admin :: Start
                $restadmin_device = $this->api_model->getRestaurantAdminDevice($restaurant_id);
                if($restadmin_device && trim($restadmin_device->device_id)!='' && $restadmin_device->notification == 1 && $restadmin_device->status == 1)
                {
                    $brancha_device_id = $restadmin_device->device_id;
                    $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$restadmin_device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    #prep the bundle
                    $fields = array();            
                    $message = sprintf($this->lang->line('push_new_order'),$order_id);                    
                    
                    $fields['to'] = $restadmin_device->device_id; // only one user to send push notification
                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                    $fields['notification']['title'] = $this->lang->line('admin_app_name');
                    $fields['data'] = array ('screenType'=>'order','order_id'=>$order_id);
                   
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
            
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                // $verificationCode = random_string('alnum',25);
                // $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-receive-alert','language_slug'=>$this->current_lang,'status'=>1))->first_row();                    
               
                // $this->db->select('OptionValue');
                // $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                //$this->db->select('OptionValue');
                // $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();  
                // if(!empty($email_template)){
                //     $this->load->library('email');  
                //     $config['charset'] = 'iso-8859-1';  
                //     $config['wordwrap'] = TRUE;  
                //     $config['mailtype'] = 'html';  
                //     $this->email->initialize($config);  
                //     $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                //     $this->email->to(trim($taxdetail->email)); 
                //     $this->email->subject($email_template->subject);  
                //     $this->email->message($email_template->message);  
                //     $this->email->send();
                // }
                $order_status = 'placed';
                $message = $this->lang->line('success_add');
                // send invoice to user
                // $data['order_records'] = $this->api_model->getEditDetail($order_id);
                // $data['menu_item'] = $this->api_model->getInvoiceMenuItem($order_id);
                // $html = $this->load->view(ADMIN_URL.'/order_invoice',$data,true);
                // if (!@is_dir('uploads/invoice')) {
                //   @mkdir('./uploads/invoice', 0777, TRUE);
                // } 
                // $filepath = 'uploads/invoice/'.$order_id.'.pdf';
                // $this->load->library('M_pdf'); 
                // $mpdf=new mPDF('','Letter'); 
                // $mpdf->SetHTMLHeader('');
                // $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="http://restaura.evdpl.com/~restaura/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
                // $mpdf->AddPage('', // L - landscape, P - portrait 
                //     '', '', '', '',
                //     0, // margin_left
                //     0, // margin right
                //     10, // margin top
                //     23, // margin bottom
                //     0, // margin header
                //     0 //margin footer
                // );
                // $mpdf->autoScriptToLang = true;
                // $mpdf->SetAutoFont();
                // $mpdf->WriteHTML($html);
                // $mpdf->output($filepath,'F');
                //send invoice as email
                // $user = $this->db->get_where('users',array('entity_id'=>$this->post('user_id')))->first_row();
                // $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                // $this->db->select('OptionValue');
                // $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                // $this->db->select('subject,message');
                // $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'new-order-invoice','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                // $arrayData = array('FirstName'=>$user->first_name,'Order_ID'=>$order_id);
                // $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                // if(!empty($EmailBody)){     
                //     $this->load->library('email');  
                //     $config['charset'] = 'iso-8859-1';  
                //     $config['wordwrap'] = TRUE;  
                //     $config['mailtype'] = 'html';  
                //     $this->email->initialize($config);  
                //     $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                //     $this->email->to(trim($user->email)); 
                //     $this->email->subject($Emaildata->subject);  
                //     $this->email->message($EmailBody);
                //     $this->email->attach($filepath);
                //     $this->email->send(); 
                // }
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('restaurant_detail'=>$taxdetail,'order_id'=>$order_id,'order_status'=>$order_status,'order_date'=>date('Y-m-d H:i:s',strtotime($order_date)),'earned_wallet_money'=> $earned_points,'wallet_money'=>$userwallet,'status'=>1,'message' => $message);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['restaurant_detail'=>$taxdetail,'order_id'=>$order_id,'order_status'=>$order_status,'order_date'=>date('Y-m-d H:i:s',strtotime($order_date)),'earned_wallet_money'=> $earned_points,'wallet_money'=>$userwallet,'status'=>1,'message' => $message], REST_Controller::HTTP_OK); // OK */
                }                
            }
            else
            {
                //Closed
                $message = $this->lang->line('restaurant_closemsg');
                if($restaurant_valid == 'na')
                {
                    //Deactivated
                    $message = $this->lang->line('restaurant_deactivemsg');
                }
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 3,'message' => $message);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 3,'message' => $message], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                }                
            }    
                
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
            }
        }  
    }
    //order detail
    public function orderListing_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
            $language_slug = $decrypted_data->language_slug;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
            $language_slug = $this->post('language_slug');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        $count = ($count) ? $count : 10;
        $page_no = ($page_no) ? $page_no : 1;
        if($tokenres){
            $result['in_process'] = $this->api_model->getOrderDetail('process', $user_id, $language_slug, $count, $page_no,$user_timezone);
            $result['past'] = $this->api_model->getOrderDetail('past', $user_id, $language_slug, $count, $page_no,$user_timezone);
            //Code for payment method show :: Start :: If needed open this
            /*$Payment_method = $this->api_model->getPaymentMethod($currency_code, $is_dine_in,'','yes');
            foreach ($Payment_method as $key => $value) {
                if($value->payment_gateway_slug == 'cod'){
                    array_splice($Payment_method, $key, 1);
                }
            }*/
            //End
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('in_process' => $result['in_process'],'past' => $result['past'], 'status' => 1, 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['in_process' => $result['in_process'], 'past' => $result['past'], 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }    
    
    //get promocode list
    public function couponList_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $subtotal = $decrypted_data->subtotal;
            $restaurant_id = $decrypted_data->restaurant_id;
            $order_delivery = $decrypted_data->order_delivery;
            $user_timezone = $decrypted_data->user_timezone;
            $used_coupon = $decrypted_data->used_coupon;
            $isLoggedIn = $decrypted_data->isLoggedIn;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $subtotal = $this->post('subtotal');
            $restaurant_id = $this->post('restaurant_id');
            $order_delivery = $this->post('order_delivery');
            $user_timezone = $this->post('user_timezone');
            $used_coupon = $this->post('used_coupon');
            $isLoggedIn = $this->post('isLoggedIn');
        }
        if($isLoggedIn == 1){
            $tokenres = $this->api_model->checkToken($user_id);
        } else {
            $tokenres = true;
        }        
        if($tokenres)
        {
            $subtotal = $subtotal;
            $used_couponarr = array();
            if($used_coupon && $used_coupon!='')
            {
                $used_couponarr = explode(",", $used_coupon);
            }            
            $couponstemp = $this->api_model->getcouponList($subtotal, $restaurant_id, $order_delivery,$user_timezone, $user_id, $isLoggedIn,$used_couponarr);

            //Code for filter array with requirement :: Start
            $coupon = array();
            $cntt=0;
            if($couponstemp && !empty($couponstemp))
            {
                for($i=0;$i<count($couponstemp);$i++)
                {
                    $flag_cnt = 'yes'; $user_chk =0;
                    $checkCnt = $this->common_model->checkUserUseCountCoupon($user_id,$couponstemp[$i]->coupon_id);
                    if($checkCnt >= $couponstemp[$i]->maximaum_use_per_users && $couponstemp[$i]->maximaum_use_per_users>0){
                        $flag_cnt = 'no';
                    }                    
                    if($flag_cnt=='yes')
                    {
                        $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($couponstemp[$i]->coupon_id);
                        if($checkCnt1 >= $couponstemp[$i]->maximaum_use && $couponstemp[$i]->maximaum_use>0){
                            $flag_cnt = 'no';
                        }
                    }
                    if($flag_cnt=='yes')
                    {
                        //Code for free delviery coupon falg check :: Start
                        $user_chkcpn = 'yes';                       
                        if($user_id>0)
                        {            
                            $this->db->select('entity_id');
                            $this->db->where('user_id',$user_id);
                            $user_chk = $this->db->count_all_results('order_master');
                            if($user_chk>0)
                            {
                                $user_chkcpn = 'no';
                            }            
                        }
                        if($isLoggedIn == 0){
                            $user_chkcpn = 'no';
                        }                       
                        if(($couponstemp[$i]->coupon_type=='free_delivery' && strtolower($order_delivery)=='delivery' && $user_chkcpn=='no' && $couponstemp[$i]->coupon_for_newuser=='1') || ($couponstemp[$i]->coupon_type=='user_registration' && $UserID==0) || ($couponstemp[$i]->coupon_type=='user_registration' && $user_chk>0))
                        {
                        }//Code for free delviery coupon falg check :: End
                        else
                        {
                            $coupon[$cntt] = $couponstemp[$i];
                            $cntt++;
                        }
                    }
                }   
            }
            //Code for filter array with requirement :: End
           
            if(!empty($coupon))
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('coupon_list' => $coupon,
                    'status' => 1,
                    'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'coupon_list' => $coupon,
                        'status' => 1,
                        'message' => $this->lang->line('record_found')
                    ],  REST_Controller::HTTP_OK);
                }
                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('promocode'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('promocode')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }  
    }
    //get notification list
    function getNotification_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
        }
        $count = ($count) ? $count : 10;
        $page_no = ($page_no) ? $page_no : 1;
        $tokenres = $this->api_model->checkToken($user_id); 
        if($tokenres){
            $notification = $this->api_model->getNotification($user_id,$count,$page_no);
            if(!empty($notification))
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('notification'=>$notification['result'],
                        'status' => 1,
                        'notificaion_count'=>$notification['count'],
                        'message' =>$this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'notification'=>$notification['result'],
                        'status' => 1,
                        'notificaion_count'=>$notification['count'],
                        'message' =>$this->lang->line('record_found')
                    ],  REST_Controller::HTTP_OK);
                }
                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            } 
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //check users order delivery
    public function checkServiceability_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_delivery = $decrypted_data->order_delivery;
            $users_latitude = $decrypted_data->users_latitude;
            $users_longitude = $decrypted_data->users_longitude;
            $user_km = $decrypted_data->user_km;
            $driver_km = $decrypted_data->driver_km;
            $restaurant_id = $decrypted_data->restaurant_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_delivery = $this->post('order_delivery');
            $users_latitude = $this->post('users_latitude');
            $users_longitude = $this->post('users_longitude');
            $user_km = $this->post('user_km');
            $driver_km = $this->post('driver_km');
            $restaurant_id = $this->post('restaurant_id');
            $isLoggedIn = $this->post('isLoggedIn');
        }
        if($isLoggedIn == 1){
            $tokenres = $this->api_model->checkToken($user_id); 
        } else {
            $tokenres = true; 
        }
        if($tokenres){
            if($order_delivery == 'Delivery')
            {
                $range = $this->common_model->getRange();
                $user_km = ($user_km) ? $user_km : $range[1]->OptionValue;
                $driver_km = ($driver_km) ? $driver_km : '';
                /*check if delivery charge available : start*/
                $checkDeliveryCharge = $this->checkGeoFence($users_latitude,$users_longitude,$price_charge = true,$restaurant_id);
                if($checkDeliveryCharge['delivery_charge']){ 
                /*check if delivery charge available : end*/
                //$detail = $this->api_model->checkOrderDelivery($users_latitude, $users_longitude, $user_id, $restaurant_id, $request = '', $order_id = '', $user_km, $driver_km);
                //if($detail){
                    $restaurantAvail = $this->api_model->checkRestaurantAvailability($users_latitude, $users_longitude, $restaurant_id, $request = '', $order_id = '', $user_km, $driver_km);
                    if($restaurantAvail){
                        $resstatus = 1;
                        $message = $this->lang->line('delivery_available');
                    } else {
                        $resstatus = 0;
                        $message = $this->lang->line('restaurant_delivery_not_available');
                    }
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => ($resstatus == 1) ? 1 : 0,
                        'default_tip_percent_val' => get_default_driver_tip_amount(),
                        'message' => $message);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response([
                            'status' => ($resstatus == 1) ? 1 : 0,
                            'default_tip_percent_val' => get_default_driver_tip_amount(),
                            'message' => $message
                        ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }                     
                } else {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 0,'default_tip_percent_val' => get_default_driver_tip_amount(),'message' => $this->lang->line('delivery_not_available'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 0,'default_tip_percent_val' => get_default_driver_tip_amount(),'message' => $this->lang->line('delivery_not_available')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }                    
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //get driver location
    public function driverTracking_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
        }
        $tokenres = $this->api_model->checkToken($user_id); 
        if($tokenres){
            $detail = $this->api_model->getdriverTracking($order_id,$user_id);
            if($detail)
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('detail'=>$detail,'status' => 1,'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['detail'=>$detail,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //check if order is delivered or not
    public function checkOrderDelivered_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $is_delivered = $decrypted_data->is_delivered;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $is_delivered = $this->post('is_delivered');
        }
        $tokenres = $this->api_model->checkToken($user_id); 
        if($tokenres){
            if($is_delivered != 1){
                $this->db->set('order_status','pending')->where('entity_id', $order_id)->update('order_master');
                $add_data = array('order_id' => $order_id, 'user_id'=>$user_id, 'order_status' => 'pending', 'time' => date('Y-m-d H:i:s'), 'status_created_by' => 'Customer');
                $this->api_model->addRecord('order_status', $add_data);
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //Logout USER
    public function logout_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
        }
        $tokenres = $this->api_model->getRecord('users', 'entity_id', $user_id);
        if($tokenres){
            $data = array('device_id' => "");
            $this->api_model->updateUser('users',$data,'entity_id', $tokenres->entity_id);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1,'message' => $this->lang->line('user_logout'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1,'message' => $this->lang->line('user_logout')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //check lat long exist in area
    /*public function checkGeoFence($latitude,$longitude,$price_charge,$restaurant_id)
    {
        $result = $this->api_model->checkGeoFence('delivery_charge','restaurant_id',$restaurant_id);
        $latlongs =  array($latitude,$longitude);
        $data = '';
        $oddNodes = false;
        $delivery_charge = '';
        foreach ($result as $key => $value) {
          
            $lat_longs = $value->lat_long;
            $lat_longs =  explode('~', $lat_longs);
            $polygon = array();
            foreach ($lat_longs as $key => $val) {
                if($val){
                    $val = str_replace(array('[',']'),array('',''),$val);
                    $polygon[] = explode(',', $val);
                }
            }
            if($polygon[0] != $polygon[count($polygon)-1])
                $polygon[count($polygon)] = $polygon[0];
            $j = 0;
            $x = $longitude;
            $y = $latitude;
            $n = count($polygon);
            for ( $i = 0; $i < $n; $i++)
            {
                $j++;
                if ($j == $n)
                {
                    $j = 0;
                }
                if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >=$y)))
                {
                    if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -$polygon[$i][1]) < $x)
                    {
                        $oddNodes = true;
                        $delivery_charge = $value->price_charge;
                        $additional_delivery_charge = $value->additional_delivery_charge;
                    }
                }
            } 
        }
        $oddNodes = ($price_charge)?$delivery_charge:$oddNodes;
        $price_arr = array('delivery_charge'=> $oddNodes, 'additional_delivery_charge'=>$additional_delivery_charge);
        return $price_arr;
    }*/
    //get user lang
    public function changeUserLanguage_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $language_slug = $this->post('language_slug');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $tokenres = $this->api_model->checkToken($user_id); 
        if($tokenres){
            $data = array('language_slug' => $language_slug);
            $this->api_model->updateUser('users', $data, 'entity_id', $user_id);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
     //change firebase token
    public function updateDeviceToken_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $firebase_token = $decrypted_data->firebase_token;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $firebase_token = $this->post('firebase_token');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres && !empty($firebase_token)){
            $data = array('device_id' => $firebase_token);
            $this->api_model->updateUser('users', $data, 'entity_id', $user_id);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    public function getFoodType_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_timezone = $this->post('user_timezone');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $food_type = $this->api_model->getFoodType($language_slug);
        if(!empty($food_type))
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('food_type'=>$food_type,'status' => 1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['food_type'=>$food_type,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    public function getRestaurantRating_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $restaurant_id = $this->post('restaurant_id');
            $user_timezone = $this->post('user_timezone');
        }
        if($restaurant_id){
            $resto_content_id = $this->api_model->getResContentId($restaurant_id);
            $reviews = $this->api_model->getRestaurantReview($resto_content_id,$user_timezone,$language_slug);
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('reviews'=>$reviews,'status'=>1,'message' => $this->lang->line('found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['reviews'=>$reviews,'status'=>1,'message' => $this->lang->line('found')], REST_Controller::HTTP_OK);
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK);
            }            
        }
    }
    public function getRestaurantMenu_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $restaurant_id = $decrypted_data->restaurant_id;
            $food = $decrypted_data->food;
            $price = $decrypted_data->price;
            $availability = $decrypted_data->availability;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $restaurant_id = $this->post('restaurant_id');
            $food = $this->post('food');
            $price = $this->post('price');
            $availability = $this->post('availability');
            $user_timezone = $this->post('user_timezone');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($restaurant_id){

            $assigned_food_types = $this->api_model->getrestaurant_foodtype($restaurant_id, $language_slug);
            $menu_item = $this->api_model->getMenuItem($restaurant_id, $food, $price, $language_slug, $popular = 0,$availability,$user_timezone,'0');
            $popular_item = $this->api_model->getMenuItem($restaurant_id, $food, $price, $language_slug, $popular = 1,$availability,$user_timezone,'0');
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('menu_item'=>$menu_item,'assigned_food_types'=>$assigned_food_types,'popular_item'=>$popular_item,'status'=>1,'message' => $this->lang->line('found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['menu_item'=>$menu_item,'assigned_food_types'=>$assigned_food_types,'popular_item'=>$popular_item,'status'=>1,'message' => $this->lang->line('found')],REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    public function getRestaurantDetail_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $restaurant_id = $decrypted_data->restaurant_id;
            $food = $decrypted_data->food;
            $price = $decrypted_data->price;
            $content_id = $decrypted_data->content_id;
            $is_event = $decrypted_data->isEvent;
            $availability = $decrypted_data->availability;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $restaurant_id = $this->post('restaurant_id');
            $food = $this->post('food');
            $price = $this->post('price');
            $content_id = $this->post('content_id');
            $is_event = $this->post('isEvent');
            $availability = $this->post('availability');
            $user_timezone = $this->post('user_timezone');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($restaurant_id){
            //get System Option Data
            $this->db->select('OptionValue');
            $enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
            $show_restaurant_reviews = ($enable_review->OptionValue == '1')?true:false;
            $details = $this->api_model->getRestaurantDetail($content_id, $language_slug,$user_timezone);
            //Code for find the food type array :: start
            $food_typearr = array();
            if($details[0]->food_type && $details[0]->food_type!='')
            {
                $food_typearr = $this->api_model->getFoodType($language_slug,$details[0]->food_type);
            }            
            //End
            //$popular_item = $this->api_model->getMenuItem($restaurant_id, $food, $price, $language_slug, $popular = 1,$availability,$user_timezone,'0');
            if($is_event == 1){
                $resto_content_id = $this->api_model->getResContentId($restaurant_id);
                $package = $this->api_model->getPackage($resto_content_id, $language_slug);
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('restaurant'=>$details,'package'=>$package,'food_type'=>$food_typearr,'show_restaurant_reviews'=>$show_restaurant_reviews,'table_booking_note'=> $this->lang->line('table_booking_note'),'status'=>1,'message' => $this->lang->line('found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['restaurant'=>$details,'package'=>$package,'food_type'=>$food_typearr,'show_restaurant_reviews'=>$show_restaurant_reviews,'table_booking_note'=> $this->lang->line('table_booking_note'),'status'=>1,'message' => $this->lang->line('found')],REST_Controller::HTTP_OK);
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('restaurant'=>$details,'show_restaurant_reviews'=>$show_restaurant_reviews,'status'=>1,'message' => $this->lang->line('found')); //,'popular_item'=>$popular_item
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['restaurant'=>$details,'show_restaurant_reviews'=>$show_restaurant_reviews,'status'=>1,'message' => $this->lang->line('found')],REST_Controller::HTTP_OK); //,'popular_item'=>$popular_item
                }                
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK);
            }            
        }
    }
    //add to cart
    public function addtoCart_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));            
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $restaurant_id_post = $decrypted_data->restaurant_id;
            $cart_id = $decrypted_data->cart_id;
            $coupon = $decrypted_data->coupon;
            $coupon_array = $decrypted_data->coupon_array;
            $order_delivery = $decrypted_data->order_delivery;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
            $items = $decrypted_data->items;
            $earning_points = $decrypted_data->earning_points;
            $is_wallet_applied = $decrypted_data->is_wallet_applied;
            $table_id = $decrypted_data->table_id;
            $language_slug = $decrypted_data->language_slug;
            $user_timezone = $decrypted_data->user_timezone;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $is_creditcard = $decrypted_data->is_creditcard;
            //driver tip
            $driver_tip = $decrypted_data->driver_tip;
            $tip_percent_val = $decrypted_data->tip_percent_val;
            //scheduled delivery
            $scheduled_date = $decrypted_data->scheduled_date;
            $slot_open_time = $decrypted_data->slot_open_time;
            $slot_close_time = $decrypted_data->slot_close_time;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $restaurant_id_post = $this->post('restaurant_id');
            $cart_id = $this->post('cart_id');
            $coupon = $this->post('coupon');
            $coupon_array = $this->post('coupon_array');
            $order_delivery = $this->post('order_delivery');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $items = $this->post('items');
            $earning_points = $this->post('earning_points');
            $is_wallet_applied = $this->post('is_wallet_applied');
            $table_id = $this->post('table_id');
            $language_slug = $this->post('language_slug');
            $user_timezone = $this->post('user_timezone');
            $isLoggedIn = $this->post('isLoggedIn');
            $is_creditcard = $this->post('is_creditcard');
            //driver tip
            $driver_tip = $this->post('driver_tip');
            $tip_percent_val = $this->post('tip_percent_val');
            //scheduled delivery
            $scheduled_date = $this->post('scheduled_date');
            $slot_open_time = $this->post('slot_open_time');
            $slot_close_time = $this->post('slot_close_time');
        }        

        //wallet money start
        if($user_id){
            $user_detail = $this->api_model->getRecord('users','entity_id',$user_id);
            $current_wallet_money =  $user_detail->wallet;
        } else {
            $current_wallet_money = 0;
        }
        //wallet money end
        if($isLoggedIn == 1){
            $tokenres = $this->api_model->checkToken($user_id);
        } else {
            $tokenres = true;
        }
        if($tokenres && !empty($restaurant_id_post))
        {
            $itemDetail = json_decode($items, true);
            $item = array();
            $subtotal = 0;
            $discount = 0;
            $total = 0;
            $cart_items_array = array();
            $is_service_fee_applied = false;
            $is_creditcard_fee_applied = false;
            $service_taxval = 0.00;
            $service_tax_typeval = NULL;
            $service_feeval = 0.00;
            $service_fee_typeval = NULL;
            $creditcard_feeval = 0.00;
            $creditcard_fee_typeval = NULL;
            
            //Code for check the restaturant open/close before order place :: Start
            $restaurantarr = $this->api_model->getRecordMultipleWhere('restaurant', array('entity_id' => $restaurant_id_post,'status' => 1));
            $restaurant_valid = 'na';
            if($restaurantarr && !empty($restaurantarr))
            {
                $restaurant_valid = $this->api_model->checkRestauranttime($restaurantarr->timings, $restaurantarr->enable_hours, $scheduled_date, $slot_open_time, $user_timezone);
            }
            //Code for check the restaturant open/close before order place :: End
            if($restaurant_valid == 'yes')
            { 
                $restaurant_id = $restaurantarr->entity_id;
                $taxdetail = $this->api_model->getRestaurantTax('restaurant',$restaurant_id,$flag='', $user_timezone);
                $currencyDetails  = $this->api_model->getRestaurantCurrency($restaurant_id);
                //get System Option Data
               /* $this->db->select('OptionValue');
                $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
                $currencyDetails = $this->api_model->getCurrencySymbol($currency_id->OptionValue);*/

                if(!empty($itemDetail)){
                    foreach ($itemDetail['items'] as $key => $value)
                    {
                        //Code for find the content_id :: Start
                        $menu_content_id = $value['menu_content_id'];
                        if(!$value['menu_content_id'] || $value['menu_content_id']=='')
                        {
                            $menu_content_id = $this->api_model->getMenuContentID($value['menu_id']);
                            $menu_content_id = $menu_content_id ->content_id;                            
                        }
                        //Code for find the content_id :: End

                        array_push($cart_items_array, $menu_content_id);
                        $getMenuID = $this->api_model->getMenuID($this->current_lang,$menu_content_id);
                        if (!empty($getMenuID)) {
                            $allow_scheduled_delivery = $taxdetail->allow_scheduled_delivery;
                            if($order_delivery=='DineIn')
                            {
                                $allow_scheduled_delivery = 0;
                            }
                            $data = $this->api_model->checkExist($getMenuID->entity_id,$allow_scheduled_delivery);
                            if(!empty($data)){
                                //$image = ($data->image) ? image_url.$data->image : '';
                                $image = (file_exists(FCPATH.'uploads/'.$data->image) && $data->image!='') ? image_url.$data->image : '';
                                $itemTotal = 0;
                                $subtotal_val = 0; //for each items array
                                $priceRate = ($value['offer_price']) ? $value['offer_price'] : $data->price;
                                $itemTotal = ($priceRate) ? $priceRate : 0;
                                if($value['is_customize'] == 1)
                                {
                                    $subtotal = ($priceRate) ? $subtotal + ($value['quantity'] * $priceRate) : $subtotal;
                                    $addons_not_available = 0;
                                    $customization = array();
                                    if(count($value['addons_category_list']) > 0){
                                        foreach ($value['addons_category_list'] as $k => $val) {
                                            $addonscust = array();
                                            if(count($val['addons_list']) > 0){
                                                foreach ($val['addons_list'] as $m => $mn) {
                                                   $add_ons_data = $this->api_model->getAddonsPrice($mn['add_ons_id']);
                                                    if($add_ons_data){
                                                        if($value['is_deal'] == 1){
                                                            $addonscust[] = array(
                                                                'add_ons_id'=>$mn['add_ons_id'],
                                                                'add_ons_name'=>$add_ons_data->add_ons_name,
                                                            );
                                                            $price = ($value['offer_price'])?$value['offer_price']:$data->price;
                                                            $subtotal = $subtotal + ($value['quantity'] * $price);
                                                        }else{
                                                            $addonscust[] = array(
                                                                'add_ons_id'=>$mn['add_ons_id'],
                                                                'add_ons_name'=>$add_ons_data->add_ons_name,
                                                                'add_ons_price'=>$add_ons_data->add_ons_price
                                                            );
                                                            $itemTotal += $add_ons_data->add_ons_price;
                                                            $subtotal = $subtotal + ($value['quantity'] * $add_ons_data->add_ons_price);
                                                        }
                                                    }
                                                }
                                            }
                                            $customization[] = array(
                                                'addons_category_id' => $val['addons_category_id'],
                                                'addons_category' => $val['addons_category'],
                                                'addons_list' => $addonscust
                                            );
                                        }
                                    }
                                    //re-order changes :: start
                                    //remove addons category if no addons id available in that.
                                    if (!empty($customization)) {
                                        foreach ($customization as $key => $cat_value) {
                                            if (empty($cat_value['addons_list'])) {
                                                unset($customization[$key]);
                                                $addons_not_available = 1;
                                                //$addons_not_available_gl = 1;
                                            }
                                        }
                                    }
                                    //re-order changes :: end
                                    if($addons_not_available != 1){
                                        if($itemTotal){
                                            $subtotal_val = $itemTotal;
                                            $itemTotal = ($value['quantity'])?$value['quantity'] * $itemTotal:$itemTotal;
                                        }else{
                                            $subtotal_val = $priceRate;
                                            $itemTotal = ($priceRate && $value['quantity'])?$value['quantity'] * $priceRate:'';
                                        }
                                        $item[] = array(
                                            'name' => $data->name,
                                            'image' => $image,
                                            'menu_id' => $value['menu_id'],
                                            'menu_content_id' => $menu_content_id,
                                            'quantity' => $value['quantity'],
                                            'comment' => $value['comment'],
                                            'price' => $data->price,
                                            'offer_price' => ($value['offer_price'])?$value['offer_price']:'',
                                            'is_veg' => $data->is_veg,
                                            'food_type_id' => $value['food_type_id'],
                                            'food_type_name' => $value['food_type_name'],
                                            'is_customize' => 1,
                                            'in_stock' => $data->stock,
                                            'is_combo_item' => 0,
                                            'combo_item_details' => '',
                                            'is_deal' => $value['is_deal'],
                                            'subTotal' => $subtotal_val,
                                            'itemTotal' => $itemTotal,
                                            'addons_category_list' => $customization
                                        );
                                    } else {
                                        $subtotal = ($priceRate)?$subtotal - ($value['quantity'] * $priceRate) : $subtotal;
                                        $priceRate = 0;
                                        $itemTotal = 0;
                                    }
                                }else{
                                    $subtotal_val = $priceRate;
                                    $itemTotal = ($priceRate && $value['quantity'])?$value['quantity'] * $priceRate:'';
                                    $item[] = array(
                                        'name' => $data->name,
                                        'image' => $image,
                                        'menu_id' => $value['menu_id'],
                                        'menu_content_id' => $menu_content_id,
                                        'quantity' => $value['quantity'],
                                        'comment' => $value['comment'],
                                        'price' => $data->price,
                                        'offer_price' => ($value['offer_price'])?$value['offer_price']:'',
                                        'is_veg' => $data->is_veg,
                                        'food_type_id' => $value['food_type_id'],
                                        'food_type_name' => $value['food_type_name'],
                                        'is_customize' => 0,
                                        'in_stock' => $data->stock,
                                        'is_combo_item' => $data->is_combo_item,
                                        'combo_item_details' => ($data->is_combo_item == '1') ? str_replace("\r\n"," + ",$data->menu_detail) : '',
                                        'is_deal' => $value['is_deal'],
                                        'subTotal' => $subtotal_val,
                                        'itemTotal' => $itemTotal
                                    );
                                    $price = ($value['offer_price']) ? $value['offer_price'] : $data->price;
                                    $subtotal = $subtotal + ($value['quantity'] * $price);
                                } 
                            }
                        } else{
                            $itemDetail['items'][$key] = array();
                        }
                    }
                }
                $messsage =  $this->lang->line('record_found');
                $subtotal = round($subtotal,2);
                $status = 1;
                $subtotalCal = $subtotal;
                $sub_plus_deliverycharge = $subtotal;
                $deliveryPrice = '';
                $total = 0;
                if($order_delivery == 'Delivery'){ 
                    //get System Option Data
                    $this->db->select('OptionValue');
                    $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
                    $min_order_amount = (float) $min_order_amount->OptionValue;
                    //check delivery charge available
                    $check = $this->checkGeoFence($latitude,$longitude,$price_charge = true,$restaurant_id);
                    if($check['delivery_charge']){ 
                        //depends on subtotal and min order amount
                        $additional_delivery_charge = ($check['additional_delivery_charge'])?$check['additional_delivery_charge']:0;
                        //based on location
                        $exist_delivery_charge = ($check['delivery_charge'])?$check['delivery_charge']:0;
                        if($subtotal >= $min_order_amount) {
                            $deliveryPrice = $exist_delivery_charge;
                        } else {
                            $deliveryPrice = $exist_delivery_charge + $additional_delivery_charge;
                        }
                        $total = $subtotal + $deliveryPrice;
                        $sub_plus_deliverycharge = $sub_plus_deliverycharge + $deliveryPrice;
                        //$deliveryPrice = $deliveryPrice;
                    }
                    else{
                        $total = $subtotal;
                    }
                }else{ 
                    $total = $subtotal;
                }
                $min_redeem_point_order = $this->db->get_where('system_option',array('OptionSlug'=>'min_redeem_point'))->first_row();

                ##################COUPON CODE START
                $coupon_arrayapply = array();
                $coupon_id = $coupon_amount = $coupon_type = $name  = $isApply = $coupon_discount = '';

                //Code for find the coupon id array :: Start
                $coupon_idarr = array();
                if(!empty($coupon_array))
                {
                    $coupon_idarr = $this->api_model->getCouponIds($coupon_array);
                }                
                //End

                //Coupon apply code :: Start
                $check_couponuse = array();
                $coupon_idarrtemp = array_column($coupon_idarr,'entity_id');
                if(!empty($coupon_idarrtemp))
                {
                    if(count($coupon_idarrtemp)>1)
                    {
                        $check_couponuse = $this->common_model->chkCouponforMUtliple($coupon_idarrtemp);
                    }
                }                
                if(!empty($check_couponuse))
                {
                    /*if(count($check_couponuse)>=1)
                    {*/
                        $c_name = $check_couponuse->name;
                        $messsage = $c_name." ".$this->lang->line('coupon_use_error');
                        $status = 2;
                        foreach ($coupon_idarr as $cp_key => $cp_value)
                        {
                            if($cp_value->entity_id==$check_couponuse->entity_id)
                            {
                                unset($coupon_idarr[$cp_key]);
                            }
                        }
                        $coupon_idarr = array_values($coupon_idarr);
                    //}
                } 
                
                if(!empty($coupon_array) && empty($coupon_idarr))
                {
                    $messsage = $this->lang->line('coupon_not_found');
                    $status = 2;
                }

                //Code for coupon validation :: Start
                $cnttttt=0; $coupon_chkarr = array();
                if(!empty($coupon_idarr))
                {
                    foreach($coupon_idarr as $cpi_key => $cpi_value)
                    {
                        $flag_cnt = 'yes';
                        $check = $this->api_model->checkCouponwithid($cpi_value->entity_id,$order_delivery);                        
                        $checkCnt = $this->common_model->checkUserUseCountCoupon($user_id,$check->entity_id);
                        if($checkCnt >= $check->maximaum_use_per_users && $check->maximaum_use_per_users>0){
                            $flag_cnt = 'no';
                        }                    
                        if($flag_cnt=='yes')
                        {
                            $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($check->entity_id);

                            if($checkCnt1 >= $check->maximaum_use && $check->maximaum_use>0)
                            {
                                $flag_cnt = 'no';
                            }
                        }
                        if($flag_cnt=='yes')
                        {
                            //Code for free delviery coupon falg check :: Start
                            $user_chkcpn = 'yes';                       
                            if($user_id>0)
                            {            
                                $this->db->select('entity_id');
                                $this->db->where('user_id',$user_id);
                                $user_chk = $this->db->count_all_results('order_master');
                                if($user_chk>0)
                                {
                                    $user_chkcpn = 'no';
                                }
                            }
                            if($isLoggedIn == 0){
                                $user_chkcpn = 'no';
                            }                    
                            if($check->coupon_type=='free_delivery' && strtolower($order_delivery)=='delivery' && $user_chkcpn=='no' && $check->coupon_for_newuser=='1')
                            {
                                $flag_cnt = 'no';
                            }//Code for free delviery coupon falg check :: End
                            else
                            {
                                $coupon_chkarr[$cnttttt]['entity_id'] = $check->entity_id;                                
                                $cnttttt++;
                            }
                        }
                        if($flag_cnt == 'no')
                        {
                            $messsage = $this->lang->line('coupon_not_found');
                            $status = 2;
                        }
                    }
                    $coupon_idarr = $coupon_chkarr;
                }
                //Code for coupon validation :: End                       

                $arr_cpcount = 0; $discount_total=0;
                if(!empty($coupon_idarr))
                {
                    foreach($coupon_idarr as $cpi_key => $cpi_value)
                    {
                        $discount =0;
                        $coupon_id = $coupon_amount = $coupon_type = $name  = $isApply = $coupon_discount = '';
                        $check = $this->api_model->checkCouponwithid($cpi_value['entity_id']);                        
                        if(!empty($check))
                        {
                            $end_dateval = $this->common_model->getZonebaseCurrentTime($check->end_date,$user_timezone);
                            if(strtotime($end_dateval) > strtotime(date('Y-m-d H:i:s')))
                            {
                                if($check->max_amount <= $subtotal)
                                { 
                                    if($check->coupon_type == 'discount_on_cart')
                                    {
                                        if($check->amount_type == 'Percentage')
                                        {
                                            $discount = round(($subtotalCal * $check->amount)/100,2);
                                           
                                        }else if($check->amount_type == 'Amount')
                                        {
                                            $discount = $check->amount;
                                        }
                                        
                                        $coupon_id = $check->entity_id;  
                                        $coupon_type = $check->amount_type;
                                        $coupon_amount = $check->amount;  
                                        $coupon_discount = ($discount);
                                        $name = $check->name;     
                                    }
                                    if($check->coupon_type == 'free_delivery')
                                    {  
                                        $discount = $deliveryPrice;
                                        $coupon_id = $check->entity_id;  
                                        $coupon_type = $check->amount_type;
                                        //$coupon_amount = $check->amount;  
                                        $coupon_amount = ($discount);  
                                        $coupon_discount = ($discount);
                                        $name = $check->name;     
                                    }
                                    if($check->coupon_type == 'user_registration' && $user_id)
                                    {
                                        $checkOrderCount = $this->api_model->checkUserCountCoupon($user_id);
                                        if($checkOrderCount > 0){
                                            $messsage = $this->lang->line('not_applied');
                                            $status = 2;
                                        }else{
                                            if($check->amount_type == 'Percentage'){
                                                $discount = round(($subtotalCal * $check->amount)/100,2);
                                                
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
                                    if($check->coupon_type == 'dine_in')
                                    {
                                        if($order_delivery=='DineIn')
                                        {
                                            if($check->amount_type == 'Percentage'){
                                                $discount = round(($subtotalCal * $check->amount)/100,2);
                                            }else if($check->amount_type == 'Amount'){
                                                $discount = $check->amount;
                                            } 
                
                                            $coupon_id = $check->entity_id;  
                                            $coupon_type = $check->amount_type;
                                            $coupon_amount = $check->amount;  
                                            $coupon_discount = abs($discount);
                                            $name = $check->name;
                                        }
                                        else
                                        {
                                            $messsage = $this->lang->line('coupon_not_found');
                                            $status = 2;
                                        }      
                                    }
                                }else{
                                    $messsage = $this->lang->line('not_applied');
                                    $status = 2;
                                }
                            }else{
                                $messsage = $this->lang->line('coupon_expire');
                                $status = 2;
                            }
                        }
                        else
                        {
                            $messsage = $this->lang->line('coupon_not_found');
                            $status = 2;
                        }

                        if($status==1)
                        {
                            $coupon_arrayapply[$arr_cpcount] = array('coupon_id' => $coupon_id,
                              'coupon_type' => $coupon_type,
                              'coupon_amount' => $coupon_amount,
                              'coupon_discount' => $coupon_discount,
                              'coupon_name' => $name);

                            $arr_cpcount++;
                        }
                        $discount_total = $discount_total+$discount;
                        $total = $total - $discount;
                    }
                }
                //Coupon apply code :: End
                ##################COUPON CODE END
                
                // check the used and remaining earning points
                $used_earning = 0;
                $minimum_subtotal = $this->db->get_where('system_option',array('OptionSlug'=>'minimum_subtotal'))->first_row();
                $flag = false;
                if($coupon_idarr && $discount_total){
                    $sub_minus_discount = $subtotalCal - $discount_total;
                    if($sub_minus_discount >= $minimum_subtotal->OptionValue && $is_wallet_applied == 1){
                        if($earning_points >= $min_redeem_point_order->OptionValue && !empty($earning_points) && $sub_minus_discount > 0){
                                $flag = true;
                        }
                    }
                }else {
                    if($subtotalCal >= $minimum_subtotal->OptionValue && $is_wallet_applied == 1){
                        if($earning_points >= $min_redeem_point_order->OptionValue && !empty($earning_points) && $subtotalCal > 0){
                                $flag = true;
                        }
                    }
                }
                if($flag == true){
                    if($coupon_idarr && $discount_total){
                        if($earning_points <= $sub_minus_discount) {
                            $used_earning = $earning_points;
                        } else { 
                            $used_earning = $sub_minus_discount;
                        }
                    } else{
                        if($earning_points <= $subtotalCal) {
                            $used_earning = $earning_points;
                        } else { 
                            $used_earning = $subtotalCal;
                        }
                    }                   
                    $total = $total - $used_earning;
                    $total = round($total,2);
                }
                
                if($flag == true && $used_earning != 0){
                    $wallet_discount =  array('label'=>$this->lang->line('wallet_discount'),'value'=>$used_earning,'label_key'=>"Wallet Discount");
                } else {
                    $wallet_discount = "";
                }
                //Code for check the pending unpain order :: Start
                $unpaid_orders = '0';$taxpaid_orders = '0'; $tax_orderslabel = '0';
                if($user_id) {
                    $paid_statusarr = $this->api_model->getRecordpaidstatus($user_id,$restaurant_id);
                    $unpaid_ordersstatus = '0';
                    if($paid_statusarr && count($paid_statusarr)>0)
                    {
                        if($order_delivery=='DineIn')
                        {
                            $unpaid_ordersstatus = '1';
                            if($paid_statusarr[0]->restaurant_id!=$restaurant_id)
                            {
                                //$unpaid_orders = '1';
                            }
                            else
                            {
                                if($table_id==$paid_statusarr[0]->table_id)
                                {
                                    //$taxpaid_orders = '1';
                                    $tax_orderslabel = '1';
                                    $sub_plus_deliverycharge = $sub_plus_deliverycharge+$paid_statusarr[0]->subtotal; 
                                }                            
                            }
                        }
                        else
                        {
                            $unpaid_ordersstatus = '1';
                            $unpaid_orders = '1';
                            //$taxpaid_orders = '0';        
                        }
                    }
                }
                //Code for check the pending unpain order :: End
                $ordertax_label = '';
                if($tax_orderslabel=='1')
                {
                    $ordertax_label = $this->lang->line('ordertax_label');
                }
               //get tax
                $text_amount = 0; $new_service_fee = 0; $new_creditcard_fee = 0;

                if($taxpaid_orders=='0')
                {
                    $tax_calsubtotal = $subtotalCal;
                    if($order_delivery=='DineIn')
                    {
                        $tax_calsubtotal = $sub_plus_deliverycharge;
                    }

                    if($taxdetail->amount_type == 'Percentage'){
                        $text_amount = round(($tax_calsubtotal * $taxdetail->amount) / 100,2);
                    }else{
                        $text_amount = $taxdetail->amount; 
                    }
                    $text_amount = round($text_amount,2);
                    $total = $total + $text_amount;
                    $service_taxval = $taxdetail->amount;
                    $service_tax_typeval = $taxdetail->amount_type;
                    
                    //New updated code for service fee calculation :: Start
                    if($taxdetail->is_service_fee_enable=='1')
                    {
                        $is_service_fee_applied = true;
                        if($taxdetail->service_fee_type == 'Percentage'){
                        $new_service_fee = round(($tax_calsubtotal * $taxdetail->service_fee) / 100,2);
                        }else{
                            $new_service_fee = $taxdetail->service_fee; 
                        }
                        $new_service_fee = round($new_service_fee,2);
                        $total = $total + $new_service_fee;
                        $service_feeval = $taxdetail->service_fee;
                        $service_fee_typeval = $taxdetail->service_fee_type;
                    }
                    //New updated code for service fee calculation :: End

                    //New updated code for creditcard fee calculation :: Start
                    if($taxdetail->is_creditcard_fee_enable=='1' && $is_creditcard=='yes')
                    {
                        $is_creditcard_fee_applied = true;
                        if($taxdetail->creditcard_fee_type == 'Percentage'){
                        $new_creditcard_fee = round(($subtotalCal * $taxdetail->creditcard_fee) / 100,2);
                        }else{
                            $new_creditcard_fee = $taxdetail->creditcard_fee; 
                        }
                        $new_creditcard_fee = round($new_creditcard_fee,2);
                        $total = $total + $new_creditcard_fee;
                        $creditcard_feeval = $taxdetail->creditcard_fee;
                        $creditcard_fee_typeval = $taxdetail->creditcard_fee_type;
                    }
                    //New updated code for creditcard fee calculation :: End
                }
                //New updated code for service fee calculation :: Start
                $service_type = $service_percent_text = '';
                if($taxdetail->is_service_fee_enable=='1')
                {
                    $service_type = ($taxdetail->service_fee_type == 'Percentage')?'%':'';
                    if($language_slug == 'ar'){
                        $service_percent_text = ($taxdetail->service_fee_type == 'Percentage') ? ' ('.$service_type.$taxdetail->service_fee.')' : '';
                    }else{
                        $service_percent_text = ($taxdetail->service_fee_type == 'Percentage') ? ' ('.$taxdetail->service_fee.$service_type.')' : '';
                    }
                }                
                //New updated code for service fee calculation :: End
                
                //New updated code for creditcard fee calculation :: Start
                $creditcard_type = $creditcard_percent_text = '';
                if($taxdetail->is_creditcard_fee_enable=='1' && $is_creditcard=='yes')
                {
                    $creditcard_type = ($taxdetail->creditcard_fee_type == 'Percentage')?'%':'';
                    if($language_slug == 'ar'){
                        $creditcard_percent_text = ($taxdetail->creditcard_fee_type == 'Percentage') ? ' ('.$creditcard_type.$taxdetail->creditcard_fee.')' : '';
                    }else{
                        $creditcard_percent_text = ($taxdetail->creditcard_fee_type == 'Percentage') ? ' ('.$taxdetail->creditcard_fee.$creditcard_type.')' : '';
                    }
                }                
                //New updated code for creditcard fee calculation :: End                
                
                $type = ($taxdetail->amount_type == 'Percentage')?'%':'';
                if($language_slug == 'ar'){
                    $percent_text = ($taxdetail->amount_type == 'Percentage') ? ' ('.$type.$taxdetail->amount.')' : '';
                }else{
                    $percent_text = ($taxdetail->amount_type == 'Percentage') ? ' ('.$taxdetail->amount.$type.')' : '';
                }                
                //$discount = ($discount) ? array('label'=>$this->lang->line('discount'),'value'=>abs($discount),'label_key'=>"Discount") : '';
                //driver tip changes :: start
                if($tip_percent_val && $tip_percent_val > 0) {
                    $driver_tip = ($subtotalCal * (float)$tip_percent_val)/100;
                    $driver_tip = $this->common_model->roundDriverTip((float)$driver_tip);
                    $driver_tip = round($driver_tip,2);
                }
                if($driver_tip && $driver_tip>0)
                {
                    $total = $total + $driver_tip;
                }
                //driver tip changes :: end
                $priceArray = array();
                if($coupon_arrayapply)
                {
                    $priceArray[] = array('label'=>$this->lang->line('sub_total'),'value'=>$subtotal,'label_key'=>"Sub Total");
                    if($taxdetail->amount_type){
                        $priceArray[] = array('label'=>$this->lang->line('service_tax').$percent_text,'label2'=>$ordertax_label,'value'=>$text_amount,'label_key'=>'Service Tax');
                    }
                    if($taxdetail->is_service_fee_enable=='1' && $taxdetail->service_fee){
                        $priceArray[] = array('label'=>$this->lang->line('service_fee').$service_percent_text,'label2'=>$ordertax_label,'value'=>$new_service_fee,'label_key'=>'Service Fee');
                    }
                    if($taxdetail->is_creditcard_fee_enable=='1' && $taxdetail->creditcard_fee && $is_creditcard=='yes'){
                        $priceArray[] = array('label'=>$this->lang->line('creditcard_fee').$creditcard_percent_text,'label2'=>$ordertax_label,'value'=>$new_creditcard_fee,'label_key'=>'Credit Card Fee');
                    }
                    if(!empty($coupon_arrayapply))
                    {
                        foreach($coupon_arrayapply as $cp_key => $cp_value){                        
                            $priceArray[] = array('label'=>$this->lang->line('discount').'('.$cp_value['coupon_name'].')','coupon_name'=>$cp_value['coupon_name'],'value'=>abs($cp_value['coupon_discount']),'label_key'=>"Discount");
                        }
                    }
                    if($deliveryPrice){
                        $priceArray[] = array('label'=>$this->lang->line('delivery_charge'),'value'=>$deliveryPrice,'label_key'=>"Delivery Charge");
                    }
                    $isApply = true;
                }else{
                    $priceArray = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$subtotal,'label_key'=>"Sub Total"),
                        ($taxdetail->amount_type)?array('label'=>$this->lang->line('service_tax').$percent_text,'label2'=>$ordertax_label,'value'=>$text_amount,'label_key'=>'Service Tax'):'',
                        ($taxdetail->is_service_fee_enable=='1' && $taxdetail->service_fee)?array('label'=>$this->lang->line('service_fee').$service_percent_text,'label2'=>$ordertax_label,'value'=>$new_service_fee,'label_key'=>'Service Fee'):'',
                        ($taxdetail->is_creditcard_fee_enable=='1' && $taxdetail->creditcard_fee && $is_creditcard=='yes')?array('label'=>$this->lang->line('creditcard_fee').$creditcard_percent_text,'label2'=>$ordertax_label,'value'=>$new_creditcard_fee,'label_key'=>'Credit Card Fee'):'',
                        ($deliveryPrice) ? array('label'=>$this->lang->line('delivery_charge'),'value'=>$deliveryPrice,'label_key'=>"Delivery Charge") : '',
                    ); 
                }
                if($used_earning > 0){
                    $priceArray[] = $wallet_discount;
                }
                //driver tip changes :: start
                if($driver_tip) {
                    $tip_percent_text = ($tip_percent_val) ? ' ('.$tip_percent_val.'%)':'';
                    $driver_tiparr = array('label'=>$this->lang->line('driver_tip').$tip_percent_text,'value'=>$driver_tip,'label_key'=>'Driver Tip');
                    array_push($priceArray, $driver_tiparr);
                }
                //driver tip changes :: end
                $priceArray[] = array('label'=>$this->lang->line('total'),'value'=>$total,'label_key'=>"Total");
                $add_data = array(
                    'user_id'=>($user_id) ? $user_id : '',
                    'items'=> serialize($item),
                    'table_id'=> ($table_id)?$table_id:'',
                    'restaurant_id'=>($restaurant_id) ? $restaurant_id : ''
                );
                if($cart_id == ''){
                    $cart_id = $this->api_model->addRecord('cart_detail', $add_data);
                }else{
                    $this->api_model->updateUser('cart_detail',$add_data,'cart_id',$cart_id);
                }
                //Code for check the pending unpain order :: Start
                /*$unpaid_orders = '0';
                if($order_delivery=='DineIn')
                {
                    $paid_statusarr = $this->api_model->getRecordpaidstatus($user_id,$restaurant_id);
                    if($paid_statusarr && count($paid_statusarr)>0)
                    {
                        if($paid_statusarr[0]->restaurant_id!=$restaurant_id)
                        {
                            $unpaid_orders = '1';
                        }
                    }                    
                }
                else
                {
                    $unpaid_orders = '1';
                } */
                //Code for check the pending unpain order :: End
                /*menu suggestion changes : start 19feb2021*/
                $language_slug = ($language_slug) ? $language_slug : $this->current_lang;
                $menu_item_suggestion = $this->api_model->getMenuSuggestionItems($restaurant_id_post,$language_slug,$cart_items_array,$user_timezone);
                /*menu suggestion changes : end 19feb2021*/
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'total' => $total,
                        'cart_id' => $cart_id,
                        'table_id'=>($table_id)?$table_id:'',
                        'wallet_money'=>$current_wallet_money,
                        'items' => $item,
                        'price' => $priceArray,
                        'coupon_id' => $coupon_id,
                        'coupon_amount' => ($coupon_amount)?$coupon_amount:'',
                        'coupon_type' => $coupon_type,
                        'coupon_name' => $name,
                        'coupon_discount' => ($coupon_discount)?$coupon_discount:'',
                        'coupon_array' => $coupon_array,
                        'coupon_arrayapply' => $coupon_arrayapply,
                        'subtotal' => $subtotal,
                        'currency_code' => $currencyDetails->currency_code,
                        'currency_symbol' => $currencyDetails->currency_symbol,
                        'delivery_charge' => ($deliveryPrice)?$deliveryPrice:'',
                        'driver_tip' => ($driver_tip)?$driver_tip:'',
                        'tip_percent_val' => ($tip_percent_val)?$tip_percent_val:'',
                        'driver_tiparr' => get_driver_tip_amount(),
                        'is_apply' => $isApply,
                        'status' => $status,
                        'minimum_subtotal'=>$minimum_subtotal->OptionValue,
                        'min_redeem_point_order'=>$min_redeem_point_order->OptionValue,
                        'is_redeem'=>$flag,
                        'pay_first'=>$taxdetail->pay_first,
                        'pay_later'=>$taxdetail->pay_later,
                        'unpaid_orders'=>$unpaid_orders,
                        'unpaid_orders_status'=>$unpaid_ordersstatus,
                        'menu_suggestion'=>$menu_item_suggestion,
                        'is_service_fee_applied'=>$is_service_fee_applied,
                        'is_creditcard_fee_applied'=>$is_creditcard_fee_applied,
                        'service_taxval'=>(float)$service_taxval,
                        'service_tax_typeval'=>$service_tax_typeval,
                        'service_feeval'=>(float)$service_feeval,
                        'service_fee_typeval'=>$service_fee_typeval,
                        'creditcard_feeval'=>(float)$creditcard_feeval,
                        'creditcard_fee_typeval'=>$creditcard_fee_typeval,
                        'restaurant_timings' => $taxdetail->timings,
                        'allow_scheduled_delivery' => $taxdetail->allow_scheduled_delivery,
                        'message' => $messsage);                    
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'total' => $total,
                        'cart_id' => $cart_id,
                        'table_id'=>($table_id)?$table_id:'',
                        'wallet_money'=>$current_wallet_money,
                        'items' => $item,
                        'price' => $priceArray,
                        'coupon_id' => $coupon_id,
                        'coupon_amount' => ($coupon_amount)?$coupon_amount:'',
                        'coupon_type' => $coupon_type,
                        'coupon_name' => $name,
                        'coupon_discount' => ($coupon_discount)?$coupon_discount:'',
                        'coupon_array' => $coupon_array,
                        'coupon_arrayapply' => $coupon_arrayapply,
                        'subtotal' => $subtotal,
                        'currency_code' => $currencyDetails->currency_code,
                        'currency_symbol' => $currencyDetails->currency_symbol,
                        'delivery_charge' => ($deliveryPrice)?$deliveryPrice:'',
                        'driver_tip' => ($driver_tip)?$driver_tip:'',
                        'tip_percent_val' => ($tip_percent_val)?$tip_percent_val:'',
                        'driver_tiparr' => get_driver_tip_amount(),
                        'is_apply' => $isApply,
                        'status' => $status,
                        'minimum_subtotal'=>$minimum_subtotal->OptionValue,
                        'min_redeem_point_order'=>$min_redeem_point_order->OptionValue,
                        'is_redeem'=>$flag,
                        'pay_first'=>$taxdetail->pay_first,
                        'pay_later'=>$taxdetail->pay_later,
                        'unpaid_orders'=>$unpaid_orders,
                        'unpaid_orders_status'=>$unpaid_ordersstatus,
                        'menu_suggestion'=>$menu_item_suggestion,
                        'is_service_fee_applied'=>$is_service_fee_applied,
                        'is_creditcard_fee_applied'=>$is_creditcard_fee_applied,
                        'service_taxval'=>(float)$service_taxval,
                        'service_tax_typeval'=>$service_tax_typeval,
                        'service_feeval'=>(float)$service_feeval,
                        'service_fee_typeval'=>$service_fee_typeval,
                        'creditcard_feeval'=>(float)$creditcard_feeval,
                        'creditcard_fee_typeval'=>$creditcard_fee_typeval,
                        'restaurant_timings' => $taxdetail->timings,
                        'allow_scheduled_delivery' => $taxdetail->allow_scheduled_delivery,
                        'message' => $messsage], REST_Controller::HTTP_OK); // OK
                }                
            }
            else
            {
                //Closed
                $message = $this->lang->line('restaurant_closemsg');
                if($restaurant_valid == 'na')
                {
                    //Deactivated
                    $message = $this->lang->line('restaurant_deactivemsg');
                }
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 3, 'message' => $message);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 3, 'message' => $message], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                }               
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
            }            
        }
    }
    //get recipe
    public function getRecipes_post()
    { 
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $recipe_search = $decrypted_data->recipe_search;
            $timing = $decrypted_data->timing;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
            $food = $decrypted_data->food;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $recipe_search = $this->post('recipe_search');
            $timing = $this->post('timing');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
            $food = $this->post('food');
            $user_timezone = $this->post('user_timezone');
        }

        $language_slug = ($language_slug)?$language_slug:$this->current_lang;        
        $recipes = $this->api_model->get_recipes($recipe_search,$language_slug,$count,$page_no,$food,$timing,$user_timezone);
        
        if($recipes)
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('recipes'=>$recipes['data'],'total_recipes'=>$recipes['count'],'status'=>1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['recipes'=>$recipes['data'],'total_recipes'=>$recipes['count'],'status'=>1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status'=>0,'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status'=>0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK  
            }            
        }
    }
    //Code for table reservation :: Start :: 08-02-2021
    //Update Pending orders
    public function updatePendingOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $transaction_id = $decrypted_data->transaction_id;
            
            $order_id = $decrypted_data->order_id;
            $total_rate = $decrypted_data->total_rate;
            $coupon_id = $decrypted_data->coupon_id;
            $coupon_discount = $decrypted_data->coupon_discount;
            $coupon_name = $decrypted_data->coupon_name;
            $coupon_amount = $decrypted_data->coupon_amount;
            $coupon_type = $decrypted_data->coupon_type;
            $payment_option_post = $decrypted_data->payment_option;
            $creditcard_feeval = $decrypted_data->creditcard_feeval;
            $creditcard_fee_typeval = $decrypted_data->creditcard_fee_typeval;
            $coupon_array = $decrypted_data->coupon_array;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $transaction_id = $this->post('transaction_id');
            
            $order_id = $this->post('order_id'); 
            $total_rate = $this->post('total_rate');
            $coupon_id = $this->post('coupon_id');
            $coupon_discount = $this->post('coupon_discount');
            $coupon_name = $this->post('coupon_name');
            $coupon_amount = $this->post('coupon_amount');
            $coupon_type = $this->post('coupon_type');
            $payment_option_post = $this->post('payment_option');
            $creditcard_feeval = $this->post('creditcard_feeval');
            $creditcard_fee_typeval = $this->post('creditcard_fee_typeval');
            $coupon_array = $this->post('coupon_array');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres) 
        {
            //Code for coupon :: Code change as per multiple coupon :: Start
            $coupon_array_apply = json_decode($coupon_array,true);
            if(!empty($coupon_array_apply))
            {
                foreach ($coupon_array_apply as $cp_key => $cp_value)
                {
                    if($cp_key==0)
                    {
                        $coupon_discount = $cp_value['coupon_discount'];
                        $coupon_name = $cp_value['coupon_name'];
                        $coupon_amount = $cp_value['coupon_amount'];
                        $coupon_type = $cp_value['coupon_type'];
                        $coupon_id = $cp_value['coupon_id'];
                    }
                    $coupon_uparray = array(
                        'coupon_discount'=>$cp_value['coupon_discount']
                    );
                    $this->api_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$order_id,'coupon_id'=>$cp_value['coupon_id']), $coupon_uparray);
                }
            }
            //Code for coupon :: Code change as per multiple coupon :: End
            if($transaction_id || $user_id){
                $update_data = array(              
                    //'order_status' =>'placed', //Hide on 21-10-2020
                    'transaction_id'=>$transaction_id,
                    'paid_status'=>'paid',
                    'order_status' => 'complete',
                    'payment_option' => $payment_option_post
                );
                $this->api_model->updatePendingOrders($user_id,$update_data);
                $message = $this->lang->line('success_update');                
            }
            //if($order_id && $coupon_discount) {
            if($order_id ) {
                $new_creditcard_fee = 0;
                if($creditcard_feeval!='')
                {
                    $new_creditcard_fee = $creditcard_feeval;
                }
                //credit fee : end
                $coupon_update = array(
                    'coupon_id'=>$coupon_id,
                    'coupon_name'=>$coupon_name,
                    'coupon_amount'=>$coupon_amount,
                    'coupon_type'=>$coupon_type,
                    'coupon_discount'=>$coupon_discount,
                    'total_rate'=>$total_rate,
                    'creditcard_fee' => $new_creditcard_fee,
                    'creditcard_fee_type' => $creditcard_fee_typeval,
                );
                //update order - apply coupon
                $this->api_model->updateUser('order_master',$coupon_update,'entity_id',$order_id);
                $message = $this->lang->line('success_update');
            }
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 1,'message' => $message);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 1,'message' => $message], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
        else
        {
            $message = $this->lang->line('unable_update');
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $message);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $message], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } 
    }
    //get all pay later orders
    public function getPayLaterOrders_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $unpaid_orders = $decrypted_data->unpaid_orders;
            $user_timezone = $decrypted_data->user_timezone;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $u_orders = $this->post('unpaid_orders');
            $user_timezone = $this->post('user_timezone');           
        }
        $unpaid_orders = explode(',', $u_orders);
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres)
        {
            $result['in_process'] = $this->api_model->getUnpaidOrderDetail('process',$user_id,$unpaid_orders,$user_timezone);  
            $result['past'] = $this->api_model->getUnpaidOrderDetail('past',$user_id,$unpaid_orders,$user_timezone); 
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('in_process'=>$result['in_process'],'past'=>$result['past'],'status'=>1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['in_process'=>$result['in_process'],'past'=>$result['past'],'status'=>1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }   
    }
    
    //Code for table reservation :: End :: 08-02-2021
    //new api for dineIn orders
    public function getCurrentDineInOrders_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres) 
        {
            $dinein_details = $this->api_model->getDineInOrderDetail('dinein', $user_id, $language_slug,$user_timezone);

            $creditcard_feeval = $creditcard_fee_typeval = $creditcard_fee_cal = '';
            $creditcard_apply = array();
            if($dinein_details && !empty($dinein_details))
            {
                $details = $this->api_model->getRestaurantCreditFee($dinein_details[0]['restaurant_content_id'], $language_slug);
                if($details && !empty($details))
                {
                    $creditcard_feeval = $details->creditcard_fee;
                    $creditcard_fee_typeval = $details->creditcard_fee_type;

                    if($details->is_creditcard_fee_enable==1)
                    {
                        $new_creditcard_fee = $details->creditcard_fee;
                        if($creditcard_fee_typeval == 'Percentage'){
                            $new_creditcard_feecal = round(($dinein_details[0]['subtotal'] * $new_creditcard_fee) / 100,2);
                        }else{
                            $new_creditcard_feecal = $creditcard_feeval; 
                        }
                        $new_creditcard_feecal = round($new_creditcard_feecal,2);
                    }
                    
                    $creditcard_fee_cal = $new_creditcard_feecal;
                }
                $creditcard_apply = array('creditcard_feeval'=>$creditcard_feeval, 'creditcard_fee_typeval'=>$creditcard_fee_typeval, 'creditcard_fee_cal'=>$creditcard_fee_cal);                
            }
            
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('dineIn' => $dinein_details, 'creditcard_apply' => $creditcard_apply, 'status' => 1, 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['dineIn' => $dinein_details, 'creditcard_apply' => $creditcard_apply, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK 
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } 
        } 
    }
    //new function to apply coupon for dine in orders
    public function applyCoupon_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $coupon = $decrypted_data->coupon;
            $coupon_arraytemp = $decrypted_data->coupon_array;
            $order_delivery = $decrypted_data->order_delivery;
            $subtotal = $decrypted_data->subtotal;
            $total = $decrypted_data->total;
            $language_slug = $decrypted_data->language_slug;
            $restaurant_id = $decrypted_data->restaurant_id;
            $wallet_amount = ($decrypted_data->wallet_amount)?$decrypted_data->wallet_amount:0;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $coupon = $this->post('coupon');
            $coupon_arraytemp = $this->post('coupon_array');
            $order_delivery = $this->post('order_delivery');
            $subtotal = $this->post('subtotal');
            $total = $this->post('total');
            $language_slug = $this->post('language_slug');
            $restaurant_id = $this->post('restaurant_id');
            $wallet_amount = ($this->post('wallet_amount'))?$this->post('wallet_amount'):0;
        } 
        $tokenres = $this->api_model->checkToken($user_id);
        if($tokenres)
        {
            $coupon_arrayapply = array();
            $coupon_array = json_decode($coupon_arraytemp,true);          

            $coupon_id = $coupon_amount = $coupon_type = $name  = $isApply = $coupon_discount = '';
            $coupon_name = ""; $status = 1; $arr_cpcount = 0; $discount_total=0;
            if($coupon_array && !empty($coupon_array))
            {
                foreach($coupon_array as $key => $value)
                {
                    $subtotalval = $subtotal-$wallet_amount;
                    $check = $this->api_model->checkCoupon_dinein($value,$subtotalval,$restaurant_id);                    
                    if(!empty($check))
                    {
                        $flag_cnt = 'yes';
                        $UserID = $user_id;
                        $UserType = 'User';
                        $checkCnt = $this->common_model->checkUserUseCountCoupon($UserID,$check->entity_id);
                        if($checkCnt >= $check->maximaum_use_per_users && $check->maximaum_use_per_users>0 && $UserType=='User')
                        {
                            $flag_cnt = 'no';
                        }
                        if($flag_cnt=='yes')
                        {
                            $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($check->entity_id);
                            if($checkCnt1 >= $check->maximaum_use && $check->maximaum_use>0){
                                $flag_cnt = 'no';
                            }                       
                        }
                        if($flag_cnt=='yes')
                        {
                            if(strtotime($check->end_date) > strtotime(date('Y-m-d H:i:s')))
                            {
                                if($check->max_amount <= $subtotalval) { 
                                    if($check->coupon_type == 'dine_in') {
                                        if($order_delivery=='DineIn') {
                                            if($check->amount_type == 'Percentage') {
                                                $discount = round(($subtotal * $check->amount)/100,2);
                                            }else if($check->amount_type == 'Amount') {
                                                $discount = $check->amount;
                                            } 
                                            $coupon_id = $check->entity_id;  
                                            $coupon_type = $check->amount_type;
                                            $coupon_amount = $check->amount;  
                                            $coupon_discount = ($discount);
                                            $name = $check->name;
                                            $messsage = $this->lang->line('coupon_applied');
                                            $status = 1;
                                        } else {
                                            $messsage = $this->lang->line('coupon_not_found');
                                            $status = 2;
                                        }      
                                    }
                                } else {
                                    $messsage = $this->lang->line('not_applied');
                                    $status = 2;
                                }
                            } else {
                                $messsage = $this->lang->line('coupon_expire');
                                $status = 2;
                            }

                            if($status==1)
                            {
                                $coupon_arrayapply[$arr_cpcount] = array('coupon_id' => $coupon_id,
                                  'coupon_type' => $coupon_type,
                                  'coupon_amount' => $coupon_amount,
                                  'coupon_discount' => $coupon_discount,
                                  'coupon_name' => $name);

                                $arr_cpcount++;
                            }
                            $discount_total = $discount_total+$discount;
                            $total = $total - $discount;
                        }
                        else
                        {
                            $coupon_name .= $value.",";
                            $status = 2;
                        }
                    }
                    else
                    {
                        $coupon_name .= $value.",";
                        $messsage = $this->lang->line('not_applied');
                        $status = 2;
                    }
                }
                if($coupon_name!='')
                {
                    $coupon_name = substr($coupon_name,0,-1);                    
                    $messsage = sprintf($this->lang->line('not_applied_namemsg'),$coupon_name);
                }

                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('coupon_arrayapply'=>$coupon_arrayapply,'coupon_discount'=>$discount_total,'subtotal'=>$subtotal,'total_rate'=>$total,'status' => $status,'message' => $messsage);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['coupon_arrayapply'=>$coupon_arrayapply,'coupon_discount'=>$discount_total,'subtotal'=>$subtotal,'total_rate'=>$total,'status' => $status,'message' => $messsage], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
                }                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => -1,'message' => '');
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
                }
            }
        } 
    }
    //get total earning points for a user
    public function getUsersEarningPoints_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $userid = $decrypted_data->user_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $userid = $this->post('user_id');
        }
        $tokenres = $this->api_model->checkToken($userid); 
        if($tokenres){
            $usersEarningPoints = $this->api_model->getUsersEarningPoints($userid);             
            $earning_points = (isset($usersEarningPoints->earning_points) && !empty($usersEarningPoints->earning_points)) ? intval ($usersEarningPoints->earning_points) : '';
            
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('EarningPoints' => $earning_points,'status' => 1);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['EarningPoints' => $earning_points,'status' => 1], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //get wallet history
    public function getWalletHistory_post() {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $count = ($decrypted_data->count)?$decrypted_data->count:10;
            $page_no = ($decrypted_data->page_no)?$decrypted_data->page_no:1;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $count = ($this->post('count'))?$this->post('count'):10;
            $page_no = ($this->post('page_no'))?$this->post('page_no'):1;
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        
        if($tokenres){
            $data = $this->api_model->getWalletHistory($user_id,$count,$page_no,$user_timezone);
            $currency = $this->api_model->getSystemOptoin('currency');
            $currency_id = $currency->OptionValue;
            $currency_symbol = $this->api_model->getCurrencySymbol($currency_id);
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('wallet_money'=>$tokenres->wallet,'currency'=>$currency_symbol->currency_symbol,'currency_code'=>$currency_symbol->currency_code,'wallet_history'=>$data['result'],'total_transactions'=>$data['count'],'total_money_credited'=>$data['total_money_credited'],'status'=>1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response(['wallet_money'=>$tokenres->wallet,'currency'=>$currency_symbol->currency_symbol,'currency_code'=>$currency_symbol->currency_code,'wallet_history'=>$data['result'],'total_transactions'=>$data['count'],'total_money_credited'=>$data['total_money_credited'],'status'=>1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //social media Login API
    public function social_post() {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $social_media_id = ($decrypted_data->social_media_id)?$decrypted_data->social_media_id:'';
            $firebase_token = $decrypted_data->firebase_token;
            $Email = $decrypted_data->Email;
            $login_type = $decrypted_data->login_type;
            $PhoneNumber = $decrypted_data->PhoneNumber;
            $FirstName = $decrypted_data->FirstName;
            $LastName = $decrypted_data->LastName;
            $image = $decrypted_data->image;
        }else{
            $this->getLang($this->post('language_slug'));
            $social_media_id = ($this->post('social_media_id'))?$this->post('social_media_id'):'';
            $firebase_token = $this->post('firebase_token');
            $Email = $this->post('Email');
            $login_type = $this->post('login_type');
            $PhoneNumber = $this->post('PhoneNumber');
            $FirstName = $this->post('FirstName');
            $LastName = $this->post('LastName');
            $image = $this->post('image');
        }
        
        $checksocial = $this->api_model->checksocial($social_media_id);
        if(!empty($checksocial)){
            if(empty($checksocial->referral_code)) {
                $referral_code = random_string('alnum', 8);
                $update = array(
                    'referral_code'=>$referral_code                                        
                );
                $this->api_model->updateUser('users',$update,'entity_id',$checksocial->entity_id);
                $checksocial = $this->api_model->checksocial($social_media_id);
            }
            if(empty($checksocial->stripe_customer_id)){
                $stripe_customer_id = $this->api_model->add_new_customer_in_stripe($checksocial->first_name,$checksocial->last_name,$checksocial->phone_code,$checksocial->mobile_number,$checksocial->email);
                if($stripe_customer_id){
                    $update = array(
                        'stripe_customer_id'=>$stripe_customer_id
                    );
                    $this->api_model->updateUser('users',$update,'entity_id',$checksocial->entity_id);
                }
            }
            $login = $checksocial;
            
            if($login->is_deleted == 1){
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('user_id'=>$login->entity_id, 'PhoneNumber'=>$login->mobile_number, 'phone_code'=>$login->phone_code, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('delete_acc_validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['user_id'=>$login->entity_id, 'PhoneNumber'=>$login->mobile_number, 'phone_code'=>$login->phone_code, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('delete_acc_validation')], REST_Controller::HTTP_OK);
                }
            }
            else if($login->active == 1)
            {
                if (!empty($firebase_token)) {
                    $data = array('active'=>1,'language_slug'=>$this->current_lang,'device_id'=>$firebase_token);
                } else {
                    $data = array('active'=>1,'language_slug'=>$this->current_lang);
                }
                //to download image from url
                $url = $image;
                if(!empty($url)) {
                    $fdata = file_get_contents($url);
                    $random_string = random_string('alnum',12);
                    if(file_exists(FCPATH.'uploads/'.$login->image)) {
                        @unlink(FCPATH.'uploads/'.$login->image);
                    }
                    // create directory if not exists
                    if (!@is_dir('uploads/profile')) {
                        @mkdir('./uploads/profile', 0777, TRUE);
                    }
                    $new = 'uploads/profile/'.$random_string.'.png';
                    file_put_contents($new, $fdata);
                    $data['image'] = "profile/".$random_string.'.png';
                }
                if($login->status==1)
                {
                    // update device 
                    $this->api_model->updateUser('users',$data,'entity_id',$login->entity_id);
                    $login = $this->api_model->checksocial($social_media_id);
                    $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';
                    //get rating
                    $rating = $this->api_model->getRatings($login->entity_id);
                    $review = (!empty($rating))?$rating->rating:'';
                    
                    $last_name = ($login->last_name)?$login->last_name:'';
                    $login->wallet = ($login->wallet)?$login->wallet:0;
                    $login_detail = array('FirstName'=>$login->first_name,'LastName'=>$last_name,'image'=>$image,'PhoneNumber'=>$login->mobile_number,'referral_code'=>$login->referral_code,'UserID'=>$login->entity_id,'user_earning_points'=>$login->wallet,'notification'=>$login->notification,'rating'=>$review,'Email'=>$login->email,'social_media_id'=>$social_media_id);
                    
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else if ($login->status==0){
                    $adminEmail = $this->api_model->getSystemOptoin('Admin_Email_Address');
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } 
            }else{
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                }
            }
        } else {
            $login = $this->api_model->getUserByEmail($Email);
            if(!empty($login)){
                if(empty($login->referral_code)) {
                    $referral_code = random_string('alnum', 8);
                    $update = array(
                        'referral_code'=>$referral_code
                    );
                    $this->api_model->updateUser('users',$update,'entity_id',$login->entity_id);
                    $login = $this->api_model->getUserByEmail($Email);
                }
                if(empty($login->stripe_customer_id)){
                    $stripe_customer_id = $this->api_model->add_new_customer_in_stripe($login->first_name,$login->last_name,$login->phone_code,$login->mobile_number,$login->email);
                    if($stripe_customer_id){
                        $update = array(
                            'stripe_customer_id'=>$stripe_customer_id
                        );
                        $this->api_model->updateUser('users',$update,'entity_id',$login->entity_id);
                    }
                }
                $data = array(
                    'social_media_id'=>$social_media_id,
                    'login_type'=>$login_type,
                    'active'=>1,
                    'status'=>1,
                    'device_id'=>$firebase_token,
                    'mobile_number'=>($PhoneNumber)?trim($PhoneNumber):$login->mobile_number,
                    'first_name'=>trim($FirstName),
                    'last_name'=>trim($LastName),
                    'email'=>trim(strtolower($Email)),
                    'user_type'=>'User',
                    'language_slug'=>$this->current_lang
                );
                //to download image from url
                $url = $image;
                if(!empty($url)) {
                    $fdata = file_get_contents($url);
                    $random_string = random_string('alnum',12);
                    if(file_exists(FCPATH.'uploads/'.$login->image)) {
                        @unlink(FCPATH.'uploads/'.$login->image);
                    }
                    // create directory if not exists
                    if (!@is_dir('uploads/profile')) {
                        @mkdir('./uploads/profile', 0777, TRUE);
                    }
                    $new = 'uploads/profile/'.$random_string.'.png';
                    file_put_contents($new, $fdata);
                    $data['image'] = "profile/".$random_string.'.png';
                }
                if($login->active == 1){
                    if (!empty($firebase_token)) {
                        $data = array('active'=>1,'language_slug'=>$this->current_lang,'device_id'=>$firebase_token);
                    } else {
                        $data = array('active'=>1,'language_slug'=>$this->current_lang);
                    }
                    if($login->status==1)
                    {
                        // update device 
                        $this->api_model->updateUser('users',$data,'entity_id',$login->entity_id);
                        $login = $this->api_model->getUserByEmail(trim(strtolower($Email)));
                        $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';
                        //get rating
                        $rating = $this->api_model->getRatings($login->entity_id);
                        $review = (!empty($rating))?$rating->rating:'';
                        
                        $last_name = ($login->last_name)?$login->last_name:'';
                        $login->wallet = ($login->wallet)?$login->wallet:0;
                        $login_detail = array('FirstName'=>$login->first_name,'LastName'=>$last_name,'image'=>$image,'PhoneNumber'=>$login->mobile_number,'referral_code'=>$login->referral_code,'UserID'=>$login->entity_id,'user_earning_points'=>$login->wallet,'notification'=>$login->notification,'rating'=>$review,'Email'=>$login->email,'social_media_id'=>$social_media_id);
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else if ($login->status==0){
                        $adminEmail = $this->api_model->getSystemOptoin('Admin_Email_Address');
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue);
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } 
                }else{
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                    }
                }
            } else {
                $referral_code = random_string('alnum', 8);
                $stripe_customer_id = $this->api_model->add_new_customer_in_stripe(trim($FirstName),trim($LastName),'',trim($PhoneNumber),trim(strtolower($Email)));
                
                $addUser = array(
                    'social_media_id'=>$social_media_id,
                    'login_type'=>$login_type,
                    'active'=>1,
                    'status'=>1,
                    'device_id'=>$firebase_token,
                    'mobile_number'=>trim($PhoneNumber),
                    'first_name'=>trim($FirstName),
                    'last_name'=>trim($LastName),
                    'email'=>trim(strtolower($Email)),
                    'user_type'=>'User',
                    'referral_code'=>$referral_code,
                    'stripe_customer_id'=> ($stripe_customer_id)?$stripe_customer_id:NULL,
                );
                //to download image from url
                $url = $image;
                if(!empty($url)) {
                    $timeout = 5;
                    $fdata = file_get_contents($url);
                    // create directory if not exists
                    if (!@is_dir('uploads/profile')) {
                        @mkdir('./uploads/profile', 0777, TRUE);
                    }
                    $random_string = random_string('alnum',12);
                    $new = 'uploads/profile/'.$random_string.'.png';
                    file_put_contents($new, $fdata);
                    $addUser['image'] = "profile/".$random_string.'.png';
                }
        
                $UserID = $this->api_model->addRecord('users', $addUser);
                $login = $this->api_model->getUserByUserid($UserID);
                if($login->active == 1){
                    if (!empty($firebase_token)) {
                        $data = array('active'=>1,'language_slug'=>$this->current_lang,'device_id'=>$firebase_token);
                    } else {
                        $data = array('active'=>1,'language_slug'=>$this->current_lang);
                    }
                    
                    if($login->status==1)
                    {
                        // update device 
                        //$image = ($login->image)?image_url.$login->image:'';
                        $image = (file_exists(FCPATH.'uploads/'.$login->image) && $login->image!='') ? image_url.$login->image : '';               
                        $this->api_model->updateUser('users',$data,'entity_id',$login->entity_id);
                        //get rating
                        $rating = $this->api_model->getRatings($login->entity_id);
                        $review = (!empty($rating))?$rating->rating:'';
                        
                        $last_name = ($login->last_name)?$login->last_name:'';
                        $login->wallet = ($login->wallet)?$login->wallet:0;
                        $login_detail = array('FirstName'=>$login->first_name,'LastName'=>$last_name,'image'=>$image,'PhoneNumber'=>$login->mobile_number,'referral_code'=>$login->referral_code,'UserID'=>$login->entity_id,'user_earning_points'=>$login->wallet,'notification'=>$login->notification,'rating'=>$review,'Email'=>$login->email,'social_media_id'=>$social_media_id);
                        
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['login' => $login_detail,'status'=>1,'active'=>true,'message' =>$this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else if ($login->status==0){
                        $adminEmail = $this->api_model->getSystemOptoin('Admin_Email_Address');
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue);
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 2,'message' => $this->lang->line('login_deactive'),'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }     
                    } 
                }else{
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['user_id'=>$login->entity_id, 'social_media_id'=>$social_media_id, 'status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                    }  
                }
            }
        }
    }        
    //sendOTP
    public function sendOTP_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $social_media_id = $decrypted_data->social_media_id;
            $phone_code = $decrypted_data->phone_code;
            $phone_number = $decrypted_data->PhoneNumber;
            $language_slug = $decrypted_data->language_slug;
            $forPasswordRecovery = $decrypted_data->forPasswordRecovery;
            $isForGuest = $decrypted_data->isForGuest;
            $guest_first_name = $decrypted_data->guest_first_name;
            $guest_email = $decrypted_data->guest_email;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $social_media_id = $this->post('social_media_id');
            $phone_code = $this->post('phone_code');
            $phone_number = $this->post('PhoneNumber');
            $language_slug = $this->post('language_slug');
            $forPasswordRecovery = $this->post('forPasswordRecovery');
            $isForGuest = $this->post('isForGuest');
            $guest_first_name = $this->post('guest_first_name');
            $guest_email = $this->post('guest_email');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $forPasswordRecovery = ($forPasswordRecovery && $forPasswordRecovery == '1') ? '1' : '0';
        $isForGuest = ($isForGuest && $isForGuest == '1') ? '1' : '0';
        if(!empty($social_media_id)) {
            // $fb_user_update = array('phone_code'=>$phone_code, 'mobile_number'=>$phone_number);
            // $this->api_model->updateUser('users',$fb_user_update,'social_media_id',$social_media_id);
            $login = $this->api_model->checksocial($social_media_id);
            $user_id = $login->entity_id;
        }
        if($isForGuest == '1') {
            $user_id = 0;
        }
        $checkphnno = array();
        if($user_id > 0 && $phone_code && $phone_number) {
            $checkphnno = $this->api_model->checkUserExistForSendOTP('users',array('mobile_number'=>$phone_number,'phone_code'=>$phone_code,'user_type'=>'User'),$user_id);
        }
        if(empty($checkphnno)) {
            //send otp start 
            $generated_otp = $this->common_model->generateOTP($user_id);
            $user_record = array();
            if($user_id > 0) {
                $user_record = $this->api_model->getRecord('users','entity_id',$user_id);
            }
            if($forPasswordRecovery == '1') {
                $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
            } else {
                if($user_id > 0) {
                    $sms = $user_record->user_otp.$this->lang->line('your_otp');
                } else if($isForGuest == '1') {
                    $sms = $generated_otp.$this->lang->line('your_otp');
                }
            }
            if(!empty($social_media_id) || $isForGuest == '1') {
                $mobile_numberT = ($phone_code) ? $phone_code : '+1';
                $mobile_numberT = $mobile_numberT.$phone_number;
            } else {
                $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
            }
            $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
            if(!empty($user_record) && $user_record->email){
                $this->common_model->sendVerifyOtpEmail($user_record->first_name,$user_record->email,$user_record->user_otp,$language_slug,$forPasswordRecovery); //send email
            } else if($guest_email && $guest_first_name) {
                $this->common_model->sendVerifyOtpEmail($guest_first_name,$guest_email,$generated_otp,$language_slug,$forPasswordRecovery);
            }
            //send otp end

            $message = $this->lang->line('send_otp_response');
            if($isForGuest == '1') {
                $message = $this->lang->line('send_otpreq_response');
            }

            if($this->post('isEncryptionActive') == TRUE)
            {                
                $response = array('OTP'=>(!empty($user_record) && $user_record->user_otp) ? $user_record->user_otp : $generated_otp,'status' => 1,'message' => $message);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['OTP'=>(!empty($user_record) && $user_record->user_otp) ? $user_record->user_otp : $generated_otp,'status' => 1,'message' => $message], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0,'message' => $this->lang->line('phone_exist'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 0,'message' => $this->lang->line('phone_exist')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }
    }
    //soft delete user account
    public function deleteAccount_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
        }
        $this->api_model->deleteAccount($user_id); 
        
        if($this->post('isEncryptionActive') == TRUE) {
            $response = array('status' => 1,'message' => $this->lang->line('success_delete'));
            $encrypted_data = $this->common_model->encrypt_data($response);
            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response(['status' => 1,'message' => $this->lang->line('success_delete')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    public function payment_method_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $currency_code = $decrypted_data->currency_code;
            $is_dine_in = $decrypted_data->is_dine_in;
            $restaurant_id = $decrypted_data->restaurant_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $user_timezone = $decrypted_data->user_timezone;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $currency_code = $this->post('currency_code');
            $is_dine_in = $this->post('is_dine_in');
            $restaurant_id = $this->post('restaurant_id');
            $isLoggedIn = $this->post('isLoggedIn');
            $user_timezone = $this->post('user_timezone');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = true;
        }
        if($tokenusr)
        {
            $Payment_method = [];
            $restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$restaurant_id);
            if(!empty($restaurant_data) && $restaurant_data->content_id){
                $Payment_method = $this->api_model->getPaymentMethod($currency_code, $is_dine_in,$restaurant_data->content_id);
            }
            $order_mode = (!empty($restaurant_data) && $restaurant_data->order_mode) ? $restaurant_data->order_mode : '';
            if($isLoggedIn != 1){
                foreach ($Payment_method as $key => $value) {
                    if($value->payment_gateway_slug == 'cod'){
                        array_splice($Payment_method, $key, 1);
                    }
                }
            }
            $datetimeslot_arr = array();
            if($restaurant_data->allow_scheduled_delivery == 1) {
                $datetimeslot_arr = $this->common_model->getDateAndTimeSlotsForScheduling($restaurant_id,'','', $user_timezone,'api');
            }

            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('Payment_method'=>$Payment_method,'orderMode' => $order_mode,'allow_scheduled_delivery'=>$restaurant_data->allow_scheduled_delivery,'datetimeslot_arr'=>$datetimeslot_arr,'status' => 1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['Payment_method'=>$Payment_method,'orderMode' => $order_mode,'allow_scheduled_delivery'=>$restaurant_data->allow_scheduled_delivery,'datetimeslot_arr'=>$datetimeslot_arr,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK);
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //BEGIN::Stripe Payment Method
    //add & save new card on myprofile :: start
    public function addNewCard_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $user_id = $decrypted_data->user_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $exp_month = $decrypted_data->exp_month;
            $exp_year = $decrypted_data->exp_year;
            $cvc = $decrypted_data->cvc;
            $card_number = $decrypted_data->card_number;
            $country_code = $decrypted_data->country_code;
            $zipcode = $decrypted_data->zipcode;
            $is_default_card = $decrypted_data->is_default_card;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $user_id = $this->post('user_id');
            $isLoggedIn = $this->post('isLoggedIn');
            $exp_month = $this->post('exp_month');
            $exp_year = $this->post('exp_year');
            $cvc = $this->post('cvc');
            $card_number = $this->post('card_number');
            $country_code = $this->post('country_code');
            $zipcode = $this->post('zipcode');
            $is_default_card = $this->post('is_default_card');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = false;
        }
        if($tokenusr){
            // Include the Stripe PHP bindings library 
            require APPPATH .'third_party/stripe-php/init.php';
            $stripe_info = $this->common_model->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            try {
                //create payment method and get payment method id from it.
                $payment_method_obj = $stripe->paymentMethods->create([
                  'type' => 'card',
                  'card' => [
                    'number' => $card_number,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                    'cvc' => $cvc,
                  ],
                  'billing_details' => [
                    'address' => [
                        'country' => $country_code,
                        'postal_code' => $zipcode
                    ]
                  ],
                ]);
                if(!empty($payment_method_obj) && $payment_method_obj->id !='') {
                    //check if stripe customer id already exist for user id (in request)
                    $cus_id = $this->api_model->checkStripeCustomerId($user_id);
                    $cus_id = ($cus_id)?$cus_id:'';
                    $payment_method_id = ($payment_method_obj->id)?$payment_method_obj->id:'';
                    $pay_method_fingerprint = ($payment_method_obj->card->fingerprint)?$payment_method_obj->card->fingerprint:'';
                    $this->save_card_after_payment($stripe, $cus_id, $payment_method_id, $pay_method_fingerprint, $user_id, 'from_addnewcard_api',$is_default_card);
                } else {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,
                            'message' => $this->lang->line('save_card_error'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'status' => 0,
                            'message' => $this->lang->line('save_card_error')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            } catch (Exception $e) { // create payment method errors
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $e->getMessage()
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //add & save new card on myprofile :: end
    //stripe save card changes :: start
    public function saveCard_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $user_id = $decrypted_data->user_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $payment_method_id = $decrypted_data->payment_method_id;
            $pay_method_fingerprint = $decrypted_data->pay_method_fingerprint;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $user_id = $this->post('user_id');
            $isLoggedIn = $this->post('isLoggedIn');
            $payment_method_id = $this->post('payment_method_id');
            $pay_method_fingerprint = $this->post('pay_method_fingerprint');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = false;
        }
        if($tokenusr){
            // Include the Stripe PHP bindings library 
            require APPPATH .'third_party/stripe-php/init.php';
            $stripe_info = $this->common_model->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            //check if stripe customer id already exist for user id (in request)
            $cus_id = $this->api_model->checkStripeCustomerId($user_id);
            //if yes then, save card
            if($cus_id){
                try {
                    //check if card already saved
                    $all_card_details = $stripe->paymentMethods->all([
                        'customer' => $cus_id,
                        'type' => 'card',
                    ]);
                    $existing_fingerprint = array();
                    foreach ($all_card_details->data as $cards_key => $cards_value) {
                        array_push($existing_fingerprint, $cards_value->card->fingerprint);
                    } 
                    //if yes, then don't save again
                    if(in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                        //card already saved.
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'status' => 0,
                                'message' => $this->lang->line('card_already_saved'),
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('card_already_saved'),
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else { //if no, then save card
                        try {
                            $attach_card = $stripe->paymentMethods->attach(
                              $payment_method_id,
                              ['customer' => $cus_id]
                            );
                            // http_response_code(200);
                            // echo json_encode($attach_card);
                            if(!empty($attach_card) && $attach_card->id != ''){
                                if($this->post('isEncryptionActive') == TRUE)
                                {
                                    $response = array(
                                        'status' => 1,
                                        'message' => $this->lang->line('card_saved'),
                                    );
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                                else
                                {
                                    $this->response([
                                        'status' => 1,
                                        'message' => $this->lang->line('card_saved'),
                                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                            } else {
                                if($this->post('isEncryptionActive') == TRUE){
                                    $response = array('status' => 0,
                                        'message' => $this->lang->line('payment_error'));
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                                }else{
                                    $this->response([
                                        'status' => 0,
                                        'message' => $this->lang->line('payment_error')
                                    ], REST_Controller::HTTP_OK);
                                }
                            }
                        } catch (Exception $e) { //attach card errors 
                            // http_response_code(500);
                            // echo json_encode(['error' => $e->getMessage()]);
                            if($this->post('isEncryptionActive') == TRUE)
                            {
                                $response = array(
                                    'status' => 0,
                                    'message' => $e->getMessage()
                                );
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                            else
                            {
                                $this->response([
                                    'status' => 0,
                                    'message' => $e->getMessage()
                                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        }
                    }
                } catch (Exception $e) { // list all cards errors
                    // http_response_code(500);
                    // echo json_encode(['error' => $e->getMessage()]);
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array(
                            'status' => 0,
                            'message' => $e->getMessage()
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response([
                            'status' => 0,
                            'message' => $e->getMessage()
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            } else {
                //if no then, create customer
                $username = ($tokenusr->first_name != '' && $tokenusr->last_name != '')?$tokenusr->first_name.' '.$tokenusr->last_name:$tokenusr->first_name;
                $user_phn = ($tokenusr->phone_code != '' && $tokenusr->mobile_number != '')?$tokenusr->phone_code.$tokenusr->mobile_number:$tokenusr->mobile_number;

                try {
                    //create customer :: start
                    $paymentIntent = $stripe->customers->create([
                        'name' => $username,
                        'email' => ($tokenusr->email)?$tokenusr->email:'',
                        'phone' => $user_phn
                    ]);
                    //create customer :: end
                    if(!empty($paymentIntent) && $paymentIntent->id){
                        $cus_id = $paymentIntent->id;
                        $this->api_model->updateUser('users',array('stripe_customer_id'=>$cus_id),'entity_id',$user_id);
                        //save card :: start
                        try {
                            //check if card already saved
                            $all_card_details = $stripe->paymentMethods->all([
                                'customer' => $cus_id,
                                'type' => 'card',
                            ]);
                            $existing_fingerprint = array();
                            foreach ($all_card_details->data as $cards_key => $cards_value) {
                                array_push($existing_fingerprint, $cards_value->card->fingerprint);
                            } 
                            //if yes, then don't save again
                            if(in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                                //card already saved.
                                if($this->post('isEncryptionActive') == TRUE)
                                {
                                    $response = array(
                                        'status' => 0,
                                        'message' => $this->lang->line('card_already_saved'),
                                    );
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                                else
                                {
                                    $this->response([
                                        'status' => 0,
                                        'message' => $this->lang->line('card_already_saved'),
                                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                            } else { //if no, then save card
                                try {
                                    $attach_card = $stripe->paymentMethods->attach(
                                      $payment_method_id,
                                      ['customer' => $cus_id]
                                    );
                                    // http_response_code(200);
                                    // echo json_encode($attach_card);
                                    if(!empty($attach_card) && $attach_card->id != ''){
                                        if($this->post('isEncryptionActive') == TRUE)
                                        {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $this->lang->line('card_saved'),
                                            );
                                            $encrypted_data = $this->common_model->encrypt_data($response);
                                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                        }
                                        else
                                        {
                                            $this->response([
                                                'status' => 1,
                                                'message' => $this->lang->line('card_saved'),
                                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                        }
                                    } else {
                                        if($this->post('isEncryptionActive') == TRUE){
                                            $response = array('status' => 0,
                                                'message' => $this->lang->line('payment_error'));
                                            $encrypted_data = $this->common_model->encrypt_data($response);
                                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                                        }else{
                                            $this->response([
                                                'status' => 0,
                                                'message' => $this->lang->line('payment_error')
                                            ], REST_Controller::HTTP_OK);
                                        }
                                    }
                                } catch (Exception $e) { //attach card errors 
                                    // http_response_code(500);
                                    // echo json_encode(['error' => $e->getMessage()]);
                                    if($this->post('isEncryptionActive') == TRUE)
                                    {
                                        $response = array(
                                            'status' => 0,
                                            'message' => $e->getMessage()
                                        );
                                        $encrypted_data = $this->common_model->encrypt_data($response);
                                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                    }
                                    else
                                    {
                                        $this->response([
                                            'status' => 0,
                                            'message' => $e->getMessage()
                                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                    }
                                }
                            }
                        } catch (Exception $e) { // list all cards errors
                            // http_response_code(500);
                            // echo json_encode(['error' => $e->getMessage()]);
                            if($this->post('isEncryptionActive') == TRUE)
                            {
                                $response = array(
                                    'status' => 0,
                                    'message' => $e->getMessage()
                                );
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                            else
                            {
                                $this->response([
                                    'status' => 0,
                                    'message' => $e->getMessage()
                                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        }
                        //save card :: end
                    } else {
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,
                                'message' => $this->lang->line('payment_error'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('payment_error')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                } catch (Exception $e) { //for create customer errors
                    // http_response_code(500);
                    // echo json_encode(['error' => $e->getMessage()]);
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array(
                            'status' => 0,
                            'message' => $e->getMessage()
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response([
                            'status' => 0,
                            'message' => $e->getMessage()
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //stripe save card changes :: end
    //stripe delete card changes :: start
    public function deleteCard_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $user_id = $decrypted_data->user_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $payment_method_id = $decrypted_data->payment_method_id;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $user_id = $this->post('user_id');
            $isLoggedIn = $this->post('isLoggedIn');
            $payment_method_id = $this->post('payment_method_id');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = false;
        }
        if($tokenusr){
            // Include the Stripe PHP bindings library 
            require APPPATH .'third_party/stripe-php/init.php';
            $stripe_info = $this->common_model->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            //check if stripe customer id already exist for user id (in request)
            $cus_id = $this->api_model->checkStripeCustomerId($user_id);
            //if yes then, save card
            if($cus_id){
                try{
                    $detach_card = $stripe->paymentMethods->detach(
                      $payment_method_id,
                      []
                    );
                    if(!empty($detach_card) && $detach_card->id != ''){
                        //if default card deleted, then make recently added card as default.
                        try {
                            //get default payment method
                            $customer_obj = $stripe->customers->retrieve(
                                $cus_id,
                                []
                            );
                            $default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
                            if($payment_method_id == $default_payment_method || !$default_payment_method) {
                                try {
                                    //list all cards
                                    $all_card_details = $stripe->paymentMethods->all([
                                        'customer' => $cus_id,
                                        'type' => 'card',
                                    ]);
                                    if(!empty($all_card_details)) {
                                        //set recent card as default
                                        $this->set_default_card($stripe, $all_card_details->data[0]->id, $cus_id);
                                    } else {
                                        //something went wrong
                                    }
                                } catch (Exception $e) { 
                                    // list all cards errors
                                }
                            }
                        } catch (Exception $e) {
                            //error while retrieving customer
                        }
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'status' => 1,
                                'message' => $this->lang->line('card_deleted'),
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 1,
                                'message' => $this->lang->line('card_deleted'),
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else {
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,
                                'message' => $this->lang->line('payment_error'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('payment_error')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                } catch (Exception $e) { //for detach payment method errors 
                    // http_response_code(500);
                    // echo json_encode(['error' => $e->getMessage()]);
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array(
                            'status' => 0,
                            'message' => $e->getMessage()
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response([
                            'status' => 0,
                            'message' => $e->getMessage()
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            } else {
                //card was not attached
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $this->lang->line('card_was_not_saved'),
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('card_was_not_saved'),
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //stripe delete card changes :: end
    //stripe get all saved cards :: start
    public function getAllSavedCards_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $user_id = $decrypted_data->user_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $user_id = $this->post('user_id');
            $isLoggedIn = $this->post('isLoggedIn');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = false;
        }
        if($tokenusr){
            // Include the Stripe PHP bindings library 
            require APPPATH .'third_party/stripe-php/init.php';
            $stripe_info = $this->common_model->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            //check if stripe customer id already exist for user id (in request)
            $cus_id = $this->api_model->checkStripeCustomerId($user_id);
            //if yes then, save card
            if($cus_id){
                $default_payment_method = NULL;
                try {
                    //get default payment method
                    $customer_obj = $stripe->customers->retrieve(
                        $cus_id,
                        []
                    );
                    $default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
                } catch (Exception $e) {
                    //error while retrieving customer
                }
                try {
                    //list all cards
                    $all_card_details = $stripe->paymentMethods->all([
                        'customer' => $cus_id,
                        'type' => 'card',
                    ]);
                    if(!empty($all_card_details)){
                        if(!$default_payment_method) {
                            //set recent card as default
                            $this->set_default_card($stripe, $all_card_details->data[0]->id, $cus_id);
                            //get default payment method
                            try {
                                $customer_obj = $stripe->customers->retrieve(
                                    $cus_id,
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
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'stripe_response'=>$all_card_details->data,
                                'status' => 1,
                                'message' => $this->lang->line('record_found'),
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'stripe_response'=>$all_card_details->data,
                                'status' => 1,
                                'message' => $this->lang->line('record_found'),
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    } else {
                        //something went wrong
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,
                                'message' => $this->lang->line('payment_error'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('payment_error')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                } catch (Exception $e) { // list all cards errors
                    // http_response_code(500);
                    // echo json_encode(['error' => $e->getMessage()]);
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array(
                            'status' => 0,
                            'message' => $e->getMessage()
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response([
                            'status' => 0,
                            'message' => $e->getMessage()
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            } else {
                // customer not created :: cards not saved
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $this->lang->line('cards_not_found'),
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('cards_not_found'),
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //stripe get all saved cards :: end
    //stripe update card changes :: start
    public function updateCard_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $user_id = $decrypted_data->user_id;
            $isLoggedIn = $decrypted_data->isLoggedIn;
            $payment_method_id = $decrypted_data->payment_method_id;
            $exp_month = $decrypted_data->exp_month;
            $exp_year = $decrypted_data->exp_year;
            $country_code = $decrypted_data->country_code;
            $zipcode = $decrypted_data->zipcode;
            $is_default_card = $decrypted_data->is_default_card;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $user_id = $this->post('user_id');
            $isLoggedIn = $this->post('isLoggedIn');
            $payment_method_id = $this->post('payment_method_id');
            $exp_month = $this->post('exp_month');
            $exp_year = $this->post('exp_year');
            $country_code = $this->post('country_code');
            $zipcode = $this->post('zipcode');
            $is_default_card = $this->post('is_default_card');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = true;
        }
        if($tokenusr){
            // Include the Stripe PHP bindings library 
            require APPPATH .'third_party/stripe-php/init.php';
            $stripe_info = $this->common_model->get_payment_method_detail('stripe');
            $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
            $stripe = new \Stripe\StripeClient($stripe_api_key);

            //check if stripe customer id already exist for user id (in request)
            $cus_id = $this->api_model->checkStripeCustomerId($user_id);
            //if yes then, save card
            if($cus_id){
                try {
                    $update_card_arr = array();
                    if($exp_month){
                        $update_card_arr['card']['exp_month'] = $exp_month;
                    }
                    if($exp_year){
                        $update_card_arr['card']['exp_year'] = $exp_year;
                    }
                    if($country_code){
                        $update_card_arr['billing_details']['address']['country'] = $country_code;
                    }
                    if($zipcode){
                        $update_card_arr['billing_details']['address']['postal_code'] = $zipcode;
                    }
                    if(!empty($update_card_arr)){
                        $update_card = $stripe->paymentMethods->update(
                          $payment_method_id,$update_card_arr
                        );
                        if(!empty($update_card) && $update_card->id != ''){
                            //set card as default
                            if($is_default_card) {
                                $this->set_default_card($stripe, $payment_method_id, $cus_id);
                            }
                            if($this->post('isEncryptionActive') == TRUE)
                            {
                                $response = array(
                                    'status' => 1,
                                    'message' => $this->lang->line('card_updated'),
                                );
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                            else
                            {
                                $this->response([
                                    'status' => 1,
                                    'message' => $this->lang->line('card_updated'),
                                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        } else {
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array('status' => 0,
                                    'message' => $this->lang->line('payment_error'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                            }else{
                                $this->response([
                                    'status' => 0,
                                    'message' => $this->lang->line('payment_error')
                                ], REST_Controller::HTTP_OK);
                            }
                        }
                    } else {
                        //add details to update
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,
                                'message' => $this->lang->line('add_details_to_update'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('add_details_to_update')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                } catch (Exception $e) { //for update payment method errors 
                    // http_response_code(500);
                    // echo json_encode(['error' => $e->getMessage()]);
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array(
                            'status' => 0,
                            'message' => $e->getMessage()
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response([
                            'status' => 0,
                            'message' => $e->getMessage()
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            } else {
                //card was not attached
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $this->lang->line('card_was_not_saved'),
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('card_was_not_saved'),
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
    }
    //stripe update card changes :: end
    public function createPaymentMethod_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $exp_month = $decrypted_data->exp_month;
            $exp_year = $decrypted_data->exp_year;
            $cvc = $decrypted_data->cvc;
            $card_number = $decrypted_data->card_number;
            $currency = $decrypted_data->currency;
            $amount = $decrypted_data->amount;       
            $payment_method_id = $decrypted_data->payment_method_id;
            $user_id = $decrypted_data->user_id;
            $save_card_flag = $decrypted_data->save_card_flag;
            $country_code = $decrypted_data->country_code;
            $zipcode = $decrypted_data->zipcode;
            $is_default_card = $decrypted_data->is_default_card;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $exp_month = $this->post('exp_month');
            $exp_year = $this->post('exp_year');
            $cvc = $this->post('cvc');
            $card_number = $this->post('card_number');
            $currency = $this->post('currency');
            $amount = $this->post('amount');
            $payment_method_id = $this->post('payment_method_id');
            $user_id = $this->post('user_id');
            $save_card_flag = $this->post('save_card_flag');
            $country_code = $this->post('country_code');
            $zipcode = $this->post('zipcode');
            $is_default_card = $this->post('is_default_card');
        }
        $stripe_detail = $this->common_model->get_payment_method_detail('stripe');
        $stripe_secret_key = ($stripe_detail->enable_live_mode) ? $stripe_detail->live_secret_key : $stripe_detail->test_secret_key;
        $headers = array (
            'Authorization: Bearer '.$stripe_secret_key,
            'Content-type: application/x-www-form-urlencoded'
        );
        //check if stripe customer id already exist for user id (in request)
        $cus_id = ($user_id)?$this->api_model->checkStripeCustomerId($user_id):false;
        if($cus_id && $payment_method_id){
            //Code for Create a PaymentIntent :: Start    
            $fields1 = array(
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['card'],
                //'setup_future_usage' => 'off_session'
                'customer' => $cus_id,
                'payment_method' => $payment_method_id,
            );
            $post1 = array();
            $this->http_build_query_for_curl($fields1,$post1);
           
            $ch1 = curl_init();
            curl_setopt( $ch1,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
            curl_setopt( $ch1,CURLOPT_POST, true );
            curl_setopt( $ch1,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch1,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch1,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch1,CURLOPT_POSTFIELDS, http_build_query($post1));
            $result1 = curl_exec($ch1);
            curl_close($ch1);
            //Code for Create a PaymentIntent :: End
            $result1_arr = json_decode($result1,true);
            if(strpos($result1,"error") && !empty($result1_arr['error'])){
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array(
                        'stripe_response'=>$result1_arr,
                        'status' => 0,
                        'message' => $this->lang->line('payment_error'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response([
                        'stripe_response'=>$result1_arr,
                        'status' => 0,
                        'message' => $this->lang->line('payment_error')
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }else{
                //Code for Confirm a PaymentIntent :: Start
                $trans_id = $result1_arr['id'];
                $payment_method = $payment_method_id;
                //$fields2 = array('return_url' => base_url(),'payment_method'=>'pm_card_visa');
                $fields2 = array('return_url' => base_url(),'payment_method'=>$payment_method,'setup_future_usage' => 'off_session');
                //for cvc verification
                //$fields2 = array('return_url' => base_url(),'payment_method'=>$payment_method,'setup_future_usage' => 'off_session','payment_method_options'=>['card'=>['cvc_token'=>$cvc]]);
                $post2 = array();
                $this->http_build_query_for_curl($fields2,$post2);
                $ch2 = curl_init();
                curl_setopt( $ch2,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/'.$trans_id.'/confirm');
                curl_setopt( $ch2,CURLOPT_POST, true );
                curl_setopt( $ch2,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch2,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch2,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch2,CURLOPT_POSTFIELDS, http_build_query($post2));
                $result2 = curl_exec($ch2);
                curl_close($ch2);
                //Code for Confirm a PaymentIntent :: End
                $result2_arr = json_decode($result2,true);
                if(strpos($result2,"error") && !empty($result2_arr['error'])){
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('stripe_response'=>$result2_arr,
                            'status' => 0,
                            'message' => $this->lang->line('payment_error'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'stripe_response'=>$result2_arr,
                            'status' => 0,
                            'message' => $this->lang->line('payment_error')
                        ], REST_Controller::HTTP_OK);
                    }
                }else{
                    //set card as default
                    if($is_default_card) {
                        // Include the Stripe PHP bindings library 
                        require APPPATH .'third_party/stripe-php/init.php';
                        $stripe = new \Stripe\StripeClient($stripe_secret_key);
                        $this->set_default_card($stripe, $payment_method_id, $cus_id);
                    }
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array(
                            'stripe_response'=>$result2_arr,
                            'status' => 1,
                            'message' => $this->lang->line('success')
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'stripe_response'=>$result2_arr,
                            'status' => 1,
                            'message' => $this->lang->line('success')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            }
        } else {
            //Code for Create a PaymentMethod :: Start
            $fields['card'] = array('number' => $card_number, 'exp_month' => $exp_month,'exp_year' =>$exp_year,'cvc' => $cvc);
            $fields['billing_details']['address'] = array('country' => $country_code, 'postal_code' => $zipcode);
            $fields['type'] = 'card';
            $post = array();
            $this->http_build_query_for_curl($fields,$post);
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, http_build_query($post));
            $result = curl_exec($ch);
            curl_close($ch);
            //Code for Create a PaymentMethod :: End
            $result_arr = json_decode($result,true);
            if(strpos($result,"error") && !empty($result_arr['error'])){
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'stripe_response'=>$result_arr,
                        'status' => 0,
                        'message' => $this->lang->line('payment_error')
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'stripe_response'=>$result_arr,
                        'status' => 0,
                        'message' => $this->lang->line('payment_error')
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }            
            }
            else
            {
                //Code for Create a PaymentIntent :: Start    
                $fields1 = array(
                    'amount' => $amount,
                    'currency' => $currency,
                    'payment_method_types' => ['card'],
                    'setup_future_usage' => 'off_session'
                );
                $post1 = array();
                $this->http_build_query_for_curl($fields1,$post1);
               
                $ch1 = curl_init();
                curl_setopt( $ch1,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
                curl_setopt( $ch1,CURLOPT_POST, true );
                curl_setopt( $ch1,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch1,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch1,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch1,CURLOPT_POSTFIELDS, http_build_query($post1));
                $result1 = curl_exec($ch1);
                curl_close($ch1);
                //Code for Create a PaymentIntent :: End
                $result1_arr = json_decode($result1,true);
                if(strpos($result1,"error") && !empty($result1_arr['error'])){
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array(
                            'stripe_response'=>$result1_arr,
                            'status' => 0,
                            'message' => $this->lang->line('payment_error'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response([
                            'stripe_response'=>$result1_arr,
                            'status' => 0,
                            'message' => $this->lang->line('payment_error')
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }else{
                    //Code for Confirm a PaymentIntent :: Start
                    $trans_id = $result1_arr['id'];
                    $payment_method = $result_arr['id'];                
                    //$fields2 = array('return_url' => base_url(),'payment_method'=>'pm_card_visa');
                    $fields2 = array('return_url' => base_url(),'payment_method'=>$payment_method,'setup_future_usage' => 'off_session');
                    $post2 = array();
                    $this->http_build_query_for_curl($fields2,$post2);
                    $ch2 = curl_init();
                    curl_setopt( $ch2,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/'.$trans_id.'/confirm');
                    curl_setopt( $ch2,CURLOPT_POST, true );
                    curl_setopt( $ch2,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch2,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch2,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch2,CURLOPT_POSTFIELDS, http_build_query($post2));
                    $result2 = curl_exec($ch2);
                    curl_close($ch2);
                    //Code for Confirm a PaymentIntent :: End
                    $result2_arr = json_decode($result2,true);
                    if(strpos($result2,"error") && !empty($result2_arr['error'])){
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('stripe_response'=>$result2_arr,
                                'status' => 0,
                                'message' => $this->lang->line('payment_error'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'stripe_response'=>$result2_arr,
                                'status' => 0,
                                'message' => $this->lang->line('payment_error')
                            ], REST_Controller::HTTP_OK);
                        }
                    }else{
                        if($save_card_flag == 1 && $user_id){
                            $cus_id = ($cus_id)?$cus_id:'';
                            $payment_method_id = ($result2_arr['charges']['data'][0]['payment_method'])?$result2_arr['charges']['data'][0]['payment_method']:(($result2_arr['payment_method'])?$result2_arr['payment_method']:'');
                            $pay_method_fingerprint = ($result2_arr['charges']['data'][0]['payment_method_details']['card']['fingerprint'])?$result2_arr['charges']['data'][0]['payment_method_details']['card']['fingerprint']:'';
                            // Include the Stripe PHP bindings library 
                            require APPPATH .'third_party/stripe-php/init.php';
                            $stripe = new \Stripe\StripeClient($stripe_secret_key);
                            $this->save_card_after_payment($stripe, $cus_id, $payment_method_id, $pay_method_fingerprint, $user_id, '', $is_default_card);
                        }
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array(
                                'stripe_response'=>$result2_arr,
                                'status' => 1,
                                'message' => $this->lang->line('success')
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'stripe_response'=>$result2_arr,
                                'status' => 1,
                                'message' => $this->lang->line('success')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                }
            }
        }
    }
    //function call :: if user selects save card while payment.
    public function save_card_after_payment($stripe, $cus_id, $payment_method_id, $pay_method_fingerprint, $user_id, $call_from='', $is_default_card = 0){
        $tokenusr = ($user_id)?$this->api_model->checkToken($user_id):false;
        //get fingerprint in case it is empty :: start
        if($pay_method_fingerprint == ''){
            try {
                $payment_method_obj = $stripe->paymentMethods->retrieve(
                    $payment_method_id,
                    []
                );
                if(!empty($payment_method_obj) && $payment_method_obj->id !='') {
                    $pay_method_fingerprint = $payment_method_obj->card->fingerprint;
                }
            } catch (Exception $e) { //get payment method errors
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $e->getMessage()
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        }
        //get fingerprint in case it is empty :: end
        if($cus_id){
            try {
                //check if card already saved
                $all_card_details = $stripe->paymentMethods->all([
                    'customer' => $cus_id,
                    'type' => 'card',
                ]);
                $existing_fingerprint = array();
                foreach ($all_card_details->data as $cards_key => $cards_value) {
                    array_push($existing_fingerprint, $cards_value->card->fingerprint);
                    if($cards_value->card->fingerprint == $pay_method_fingerprint) {
                        $payment_method_id = $cards_value->id;
                    }
                } 
                //if yes, then don't save again
                if(in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                    //card already saved.
                    //set card as default
                    if($is_default_card) {
                        $this->set_default_card($stripe, $payment_method_id, $cus_id);
                    }
                    if($call_from==''){
                        return true;
                    } else {
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'status' => 0,
                                'message' => $this->lang->line('card_already_saved'),
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('card_already_saved'),
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    }
                } else { //if no, then save card
                    try {
                        $attach_card = $stripe->paymentMethods->attach(
                          $payment_method_id,
                          ['customer' => $cus_id]
                        );
                        if(!empty($attach_card) && $attach_card->id != ''){
                            //set card as default
                            if($is_default_card) {
                                $this->set_default_card($stripe, $payment_method_id, $cus_id);
                            }
                            if($call_from==''){
                                return true;
                            } else {
                                if($this->post('isEncryptionActive') == TRUE)
                                {
                                    $response = array(
                                        'status' => 1,
                                        'message' => $this->lang->line('card_saved'),
                                    );
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                                else
                                {
                                    $this->response([
                                        'status' => 1,
                                        'message' => $this->lang->line('card_saved'),
                                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                            }
                        } else {
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array('status' => 0,
                                    'message' => $this->lang->line('save_card_error'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                            }else{
                                $this->response([
                                    'status' => 0,
                                    'message' => $this->lang->line('save_card_error')
                                ], REST_Controller::HTTP_OK);
                            }
                        }
                    } catch (Exception $e) { //attach card errors 
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'status' => 0,
                                'message' => $e->getMessage()
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 0,
                                'message' => $e->getMessage()
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    }
                }
            } catch (Exception $e) { // list all cards errors
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $e->getMessage()
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        } else if($tokenusr) {
            //if no then, create customer
            $username = ($tokenusr->first_name != '' && $tokenusr->last_name != '')?$tokenusr->first_name.' '.$tokenusr->last_name:$tokenusr->first_name;
            $user_phn = ($tokenusr->phone_code != '' && $tokenusr->mobile_number != '')?$tokenusr->phone_code.$tokenusr->mobile_number:$tokenusr->mobile_number;

            try {
                //create customer :: start
                $paymentIntent = $stripe->customers->create([
                    'name' => $username,
                    'email' => ($tokenusr->email)?$tokenusr->email:'',
                    'phone' => $user_phn
                ]);
                //create customer :: end
                if(!empty($paymentIntent) && $paymentIntent->id){
                    $cus_id = $paymentIntent->id;
                    $this->api_model->updateUser('users',array('stripe_customer_id'=>$cus_id),'entity_id',$user_id);
                    //save card :: start
                    try {
                        //check if card already saved
                        $all_card_details = $stripe->paymentMethods->all([
                            'customer' => $cus_id,
                            'type' => 'card',
                        ]);
                        $existing_fingerprint = array();
                        foreach ($all_card_details->data as $cards_key => $cards_value) {
                            array_push($existing_fingerprint, $cards_value->card->fingerprint);
                        } 
                        //if yes, then don't save again
                        if(in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                            //card already saved.
                            //set card as default
                            if($is_default_card) {
                                $this->set_default_card($stripe, $payment_method_id, $cus_id);
                            }
                            if($call_from==''){
                                return true;
                            } else {
                                if($this->post('isEncryptionActive') == TRUE)
                                {
                                    $response = array(
                                        'status' => 0,
                                        'message' => $this->lang->line('card_already_saved'),
                                    );
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                                else
                                {
                                    $this->response([
                                        'status' => 0,
                                        'message' => $this->lang->line('card_already_saved'),
                                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                            }
                        } else { //if no, then save card
                            try {
                                $attach_card = $stripe->paymentMethods->attach(
                                  $payment_method_id,
                                  ['customer' => $cus_id]
                                );
                                // http_response_code(200);
                                // echo json_encode($attach_card);
                                if(!empty($attach_card) && $attach_card->id != ''){
                                    //set card as default
                                    if($is_default_card) {
                                        $this->set_default_card($stripe, $payment_method_id, $cus_id);
                                    }
                                    if($call_from==''){
                                        return true;
                                    } else {
                                        if($this->post('isEncryptionActive') == TRUE)
                                        {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $this->lang->line('card_saved'),
                                            );
                                            $encrypted_data = $this->common_model->encrypt_data($response);
                                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                        }
                                        else
                                        {
                                            $this->response([
                                                'status' => 1,
                                                'message' => $this->lang->line('card_saved'),
                                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                        }
                                    }
                                } else {
                                    if($this->post('isEncryptionActive') == TRUE){
                                        $response = array('status' => 0,
                                            'message' => $this->lang->line('save_card_error'));
                                        $encrypted_data = $this->common_model->encrypt_data($response);
                                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                                    }else{
                                        $this->response([
                                            'status' => 0,
                                            'message' => $this->lang->line('save_card_error')
                                        ], REST_Controller::HTTP_OK);
                                    }
                                }
                            } catch (Exception $e) { //attach card errors 
                                if($this->post('isEncryptionActive') == TRUE)
                                {
                                    $response = array(
                                        'status' => 0,
                                        'message' => $e->getMessage()
                                    );
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                                else
                                {
                                    $this->response([
                                        'status' => 0,
                                        'message' => $e->getMessage()
                                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                }
                            }
                        }
                    } catch (Exception $e) { // list all cards errors
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array(
                                'status' => 0,
                                'message' => $e->getMessage()
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 0,
                                'message' => $e->getMessage()
                            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                    }
                    //save card :: end
                } else {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,
                            'message' => $this->lang->line('save_card_error'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'status' => 0,
                            'message' => $this->lang->line('save_card_error')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            } catch (Exception $e) { //for create customer errors
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array(
                        'status' => 0,
                        'message' => $e->getMessage()
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
        } else {
            if($call_from==''){
                return true;
            } else {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => '');
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response(['status' => 0,'message' => ''], REST_Controller::HTTP_OK);
                }
            }
        }
    }
    //Code for Retrieve a PaymentIntent :: Start
    public function checkCardPayment_post()
    {        
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;
            $trans_id = $decrypted_data->trans_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;
            $trans_id = $this->post('trans_id');            
        }        
        $stripe_detail = $this->common_model->get_payment_method_detail('stripe');
        $stripe_secret_key = ($stripe_detail->enable_live_mode) ? $stripe_detail->live_secret_key : $stripe_detail->test_secret_key;
        //Code for Create a PaymentMethod :: Start
        $headers = array (
            'Authorization: Bearer '.$stripe_secret_key,
            'Content-type: application/x-www-form-urlencoded'
        );
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/'.$trans_id);
        curl_setopt( $ch,CURLOPT_HTTPGET, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec($ch);
        curl_close($ch);
        //Code for Create a PaymentMethod :: End
        $result_arr = json_decode($result,true);
        if(strpos($result,"error") && !empty($result_arr['error'])){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('payment_error'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('payment_error')
                ], REST_Controller::HTTP_OK);
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('success'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('success')
                ], REST_Controller::HTTP_OK);
            }           
        }
    }
    //Code for Retrieve a PaymentIntent :: End
    public function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null )
    {
        if(is_object($arrays)){
            $arrays = get_object_vars( $arrays );
        }
        foreach( $arrays AS $key => $value ){
            $k = isset($prefix)?$prefix.'['.$key.']':$key;
            if(is_array( $value ) OR is_object($value)){
                $this->http_build_query_for_curl($value,$new,$k);
            }
            else
            {
                $new[$k] = $value;
            }
        }
    }
    //END::Stripe Payment Method
    /*BEGIN::Paypal Payment Method*/
    public function paypalAccessToken_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
        }
        $paypal_detail = $this->common_model->get_payment_method_detail('paypal');
        if($paypal_detail->enable_live_mode){
            $paypal_client_id = $paypal_detail->live_client_id;
            $paypal_client_secret = $paypal_detail->live_client_secret;
            $paypal_url_v1 = LIVE_PAYPAL_URL_V1;
            $paypal_url_v2 = LIVE_PAYPAL_URL_V2;
        }else{
            $paypal_client_id = $paypal_detail->sandbox_client_id;
            $paypal_client_secret = $paypal_detail->sandbox_client_secret;
            $paypal_url_v1 = SANDBOX_PAYPAL_URL_V1;
            $paypal_url_v2 = SANDBOX_PAYPAL_URL_V2;
        }
        $basic_token = base64_encode($paypal_client_id.":".$paypal_client_secret);
        $headers = array (
            'Authorization: Basic '. $basic_token,
            'Content-Type: application/x-www-form-urlencoded'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $paypal_url_v1."oauth2/token" );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result = curl_exec($ch);
        curl_close($ch);
        $result_arr = json_decode($result,true);
        if((strpos($result,"400") || strpos($result,"Bad Request")) && $result_arr['access_token']!=''){
            $result_arr['error'] = "error";
        }else{    
            $result_arr = json_decode($result,true);
        }
        if($result_arr['error']=='error'){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0,'payment_details' => $result,'message' => $this->lang->line('payment_error'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => 0,
                    'payment_details' => $result,
                    'message' => $this->lang->line('payment_error')
                ], REST_Controller::HTTP_OK);
            }
        }else{
            $payment_details = array(
                'PAYPAL_URL'=>$paypal_url_v2,
                'access_token'=>$result_arr['access_token'],
                'token_type'=>$result_arr['token_type'],
                'expires_in'=>$result_arr['expires_in']
            );
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'payment_details' => $payment_details,'message' => $this->lang->line('success'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 1,'payment_details' => $payment_details,'message' => $this->lang->line('success')], REST_Controller::HTTP_OK);
            }
        }
    }
    /*END::Paypal Payment Method*/
    public function online_payment_post()
    {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $payment_method = $decrypted_data->payment_method;
            $order_id = $decrypted_data->order_id;
            $total = $decrypted_data->amount;
            $currency = $decrypted_data->currency;
            $exp_month = $decrypted_data->exp_month;
            $exp_year = $decrypted_data->exp_year;
            $cvc = $decrypted_data->cvc;
            $card_number = $decrypted_data->card_number;
            $isLoggedIn = $decrypted_data->isLoggedIn;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $payment_method = $this->post('payment_method');
            $order_id = $this->post('order_id');
            $total = $this->post('amount');
            $currency = $this->post('currency');
            $exp_month = $this->post('exp_month');
            $exp_year = $this->post('exp_year');
            $cvc = $this->post('cvc');
            $card_number = $this->post('card_number');
            $isLoggedIn = $this->post('isLoggedIn');
        }
        if($isLoggedIn == 1){
            $tokenusr = $this->api_model->checkToken($user_id);
        } else {
            $tokenusr = true;
        }
        if($tokenusr)
        {
            // Code for Stripe Payment :: Start
            if(strtolower($payment_method) == 'stripe'){
                $stripe_detail = $this->common_model->get_payment_method_detail('stripe');
                $stripe_secret_key = ($stripe_detail->enable_live_mode) ? $stripe_detail->live_secret_key : $stripe_detail->test_secret_key;
                $headers = array (
                    'Authorization: Bearer '.$stripe_secret_key,
                    'Content-type: application/x-www-form-urlencoded'
                );
                $fields = array(
                    'type' => 'card',
                    'card' => [
                        'number' => $card_number,
                        'exp_month' => $exp_month,
                        'exp_year' => $exp_year,
                        'cvc' => $cvc 
                    ]
                );
                $post = array();
                $this->http_build_query_for_curl($fields,$post);    
                $stripe_card_details = $this->initiate_curl_call($headers, 'https://api.stripe.com/v1/payment_methods', $post);
                if(empty($stripe_card_details['error'])){
                    $fields1 = array(
                        'amount' => $total,
                        'currency' => $currency,
                        'payment_method_types' => ['card']
                    );
                    $post1 = array();
                    $this->http_build_query_for_curl($fields1,$post1);
                    $stripe_payment_initiate = $this->initiate_curl_call($headers, 'https://api.stripe.com/v1/payment_intents', $post1);
                    if(empty($stripe_payment_initiate['error'])){
                        $trans_id = $stripe_payment_initiate['id'];
                        $payment_method = $stripe_card_details['id'];
                        $fields2 = array('return_url' => base_url(),'payment_method'=>$payment_method);
                        $post2 = array();
                        $this->http_build_query_for_curl($fields2,$post2);
                        $stripe_payment_confirm = $this->initiate_curl_call($headers, 'https://api.stripe.com/v1/payment_intents/'.$trans_id.'/confirm', $post2);
                        if(empty($stripe_payment_confirm['error'])){
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array(
                                    'stripe_response'=>$stripe_payment_confirm,
                                    'status' => 1,
                                    'message' => $this->lang->line('success')
                                );
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                            }
                            else
                            {
                                $this->response([
                                    'stripe_response'=>$stripe_payment_confirm,
                                    'status' => 1,
                                    'message' => $this->lang->line('success')
                                ], REST_Controller::HTTP_OK);
                            }
                        }
                        else
                        {
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array('stripe_response'=>$stripe_payment_confirm,
                                    'status' => 1,
                                    'message' => $this->lang->line('payment_error'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                            }else{
                                $this->response([
                                    'stripe_response'=>$stripe_payment_confirm,
                                    'status' => 1,
                                    'message' => $this->lang->line('payment_error')
                                ], REST_Controller::HTTP_OK);
                            }
                        }
                    }else{
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array(
                                'stripe_response'=>$stripe_payment_initiate,
                                'status' => 1,
                                'message' => $this->lang->line('payment_error'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'stripe_response'=>$stripe_payment_initiate,
                                'status' => 1,
                                'message' => $this->lang->line('payment_error')
                            ], REST_Controller::HTTP_OK);
                        }
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array(
                            'stripe_response'=>$stripe_card_details,
                            'status' => 1,
                            'message' => $this->lang->line('payment_error')
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'stripe_response'=>$stripe_card_details,
                            'status' => 1,
                            'message' => $this->lang->line('payment_error')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            }else if(strtolower($payment_method) == 'paypal'){
                $paypal_detail = $this->common_model->get_payment_method_detail('paypal');
                if($paypal_detail->enable_live_mode){
                    $paypal_client_id = $paypal_detail->live_client_id;
                    $paypal_client_secret = $paypal_detail->live_client_secret;
                    $paypal_url_v1 = LIVE_PAYPAL_URL_V1;
                    $paypal_url_v2 = LIVE_PAYPAL_URL_V2;
                }else{
                    $paypal_client_id = $paypal_detail->sandbox_client_id;
                    $paypal_client_secret = $paypal_detail->sandbox_client_secret;
                    $paypal_url_v1 = SANDBOX_PAYPAL_URL_V1;
                    $paypal_url_v2 = SANDBOX_PAYPAL_URL_V2;
                }
                $basic_token = base64_encode($paypal_client_id.":".$paypal_client_secret);
                $headers = array (
                    'Authorization: Basic '. $basic_token,
                    'Content-Type: application/x-www-form-urlencoded'
                );
                $paypal_access_token = $this->initiate_curl_call($headers, $paypal_url_v1."oauth2/token", '', '', 'paypal');
                if(empty($paypal_access_token['error'])){
                    $payment_details = array(
                        'PAYPAL_URL'=>$paypal_url_v2,
                        'access_token'=>$paypal_access_token['access_token'],
                        'token_type'=>$paypal_access_token['token_type'],
                        'expires_in'=>$paypal_access_token['expires_in']
                    );
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 1,'payment_details' => $payment_details,'message' => $this->lang->line('success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['status' => 1,'payment_details' => $payment_details,'message' => $this->lang->line('success')], REST_Controller::HTTP_OK);
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'payment_details' => $result,'message' => $this->lang->line('payment_error'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                            'status' => 0,
                            'payment_details' => $result,
                            'message' => $this->lang->line('payment_error')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            }else{
                // $this->api_model->deleteData('order_master','entity_id',$order_id);
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'payment_details' => '','message' => $this->lang->line('payment_error'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                }else{
                    $this->response(['status' => 0,'payment_details' => '','message' => $this->lang->line('payment_error')], REST_Controller::HTTP_OK);
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK);
            }
        }
        
    }
    //Curl code :: Start
    public function initiate_curl_call($headers = [], $CURLOPT_URL, $post_fields = [], $fields = [], $payment_method = '')
    {   
        $response_array = array();
        if(!empty($headers) && trim($CURLOPT_URL)!=''){
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $CURLOPT_URL);
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            if(!empty($payment_method) && $payment_method == 'paypal'){
                curl_setopt( $ch,CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            }
            if(!empty($fields)){
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
            }
            if(!empty($post_fields)){
                curl_setopt( $ch,CURLOPT_POSTFIELDS, http_build_query($post_fields));
            }            
            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result,true);            
            if(!empty($payment_method) && $payment_method == 'paypal'){
                if($result['error'] || $result['Bad Request'] || $result['access_token'] == '')
                {
                    $response_array['error'] = "error";
                }
                else{
                    $response_array = $result;
                }
            }else{
                if($result['error'] || $result['field_errors']){
                    $response_array['error'] = $result['error'];
                }else{
                    $response_array = $result;
                }
            }
        }
        return $response_array;
    }
    //delivery zone issue :: start
    //check lat long exist in area
    public function checkGeoFence($latitude,$longitude,$price_charge,$restaurant_id)
    {
        $this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);        
        $resultcont = $this->db->get('restaurant')->first_row();
        
        $result = $this->api_model->checkGeoFence('delivery_charge','restaurant_id',$resultcont->content_id);
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
                if(!empty($output['delivery_charge'])) {
                    return $output;
                    exit;
                }
            }
        }
        return $output;
    }
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
            $price_arr = array('delivery_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
            return $price_arr;
            //check 3 :: end
        }
        $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
        $price_arr = array('delivery_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
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
    //cancel order :: start
    public function cancelOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $cancel_reason = $decrypted_data->cancel_reason;
            $other_reason = $decrypted_data->other_reason;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $cancel_reason = $this->input->post('cancel_reason');
            $other_reason = $this->input->post('other_reason');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        $language_slug = ($language_slug) ? $language_slug : $this->current_lang;
        if($tokenres){
            $order_exist = $this->api_model->getRecord('order_master','entity_id',$order_id);
            $reason = ($cancel_reason)?$cancel_reason:(($other_reason)?$other_reason:'');
            $response = array("error"=>'');
            $payment_methodarr = array('stripe','paypal','applepay');
            if(!empty($order_exist) && $reason!='') {
                //stripe refund amount
                $data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
                if($data['order_records']->refund_status!='pending' && $data['order_records']->tips_refund_status!='pending'){
                    if(($data['order_records']->transaction_id!='' && in_array(strtolower($data['order_records']->payment_option), $payment_methodarr) && $data['order_records']->refund_status!='refunded') || ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status!='refunded')){
                        $transaction_id = ($data['order_records']->transaction_id!='' && ($data['order_records']->refund_status=='' || strtolower($data['order_records']->refund_status)=='partial refunded'))?$data['order_records']->transaction_id:'';
                        $tips_transaction_id = ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status=='')?$data['order_records']->tips_transaction_id:'';
                        $tip_payment_option = ($data['order_records']->tip_payment_option!='' && $data['order_records']->tip_payment_option!=null)?$data['order_records']->tip_payment_option:'';
                        if($tip_payment_option=='' && $tips_transaction_id!='')
                        {
                            $tip_payment_option = 'stripe';
                        }

                        if(strtolower($data['order_records']->payment_option)=='stripe' || strtolower($data['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                        {
                            $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                        }
                        else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                        {   
                            $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                        }

                        //Mail send code Start
                        if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                        {
                            $updated_bytxt = $tokenres->first_name.' '.$tokenres->last_name;
                            $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
                        }
                        //Mail send code End

                        //send refund noti to user
                        if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                            $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                        }
                    }
                }
                $order_status = 'cancel';
                $this->db->set('order_status',$order_status)->where('entity_id',$order_id)->update('order_master');
                $this->db->set('cancel_reason',$reason)->where('entity_id',$order_id)->update('order_master');

                $current_date = $this->common_model->setZonebaseDateTime(date('Y-m-d H:i:s'),$user_timezone);
                $status_created_by ='Customer';
                $addData = array(
                        'order_id'=>$order_id,
                        'user_id'=>$user_id,
                        'order_status'=>$order_status,
                        'time'=>date('Y-m-d H:i:s',strtotime($current_date)),
                        'status_created_by'=>$status_created_by
                    );
                $order_detail_id = $this->api_model->addRecord('order_status',$addData);
                $order_status = 'order_canceled';
                $this->db->set('is_updateorder','0')->where('entity_id',$order_detail_id)->update('order_detail');
                //wallet changes :: start
                $users_wallet = $this->api_model->getUsersWalletMoney($user_id);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $credit_walletDetails = $this->api_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                $credit_amount = $credit_walletDetails->amount;
                $debit_walletDetails = $this->api_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                $debit_amount = $debit_walletDetails->amount;
                $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                //delete order_id from wallet history and update users wallet
                if(!empty($credit_amount) || !empty($debit_amount)){
                    $this->api_model->deletewallethistory($order_id); // delete by order id
                    $new_wallet = array(
                        'wallet'=>$new_wallet_amount
                    );
                    $this->api_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                }
                //wallet changes :: end

                //save other reason :: start
                if(!empty($other_reason)) {
                    $other_reason_content = array(
                        'content_type' => 'cancel_reject_reason',
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s',strtotime($current_date))
                    );
                    $ContentID = $this->api_model->addRecord('content_general',$other_reason_content);
                    $other_reason_arr = array(
                        'reason' => $other_reason,
                        'reason_type' => 'cancel',
                        'user_type' => 'Customer',
                        'content_id' => $ContentID,
                        'status'=>0,
                        'language_slug' => $language_slug,
                        'created_by' => $user_id
                    );
                    $this->api_model->addRecord('cancel_reject_reasons',$other_reason_arr);
                }
                $response['error_message'] ='';
                if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
                    $response['error_message'] = $this->lang->line('refund_failed');
                }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
                    $response['error_message'] = $this->lang->line('refund_canceled');
                }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
                    $response['error_message'] = $this->lang->line('refund_pending');
                }                
                if($response['error']!=''){
                    //save other reason :: end
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1,'message' => $this->lang->line('cancel_order_success_msg_refunded'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 1,'message' => $this->lang->line('cancel_order_success_msg_refunded')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                } else if($response['error_message']!=''){
                    //save other reason :: end
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1,'message' => $response['error_message']);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 1,'message' => $response['error_message'] ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                } else{
                    //save other reason :: end
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1,'message' => $this->lang->line('cancel_order_success_msg'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 1,'message' => $this->lang->line('cancel_order_success_msg')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                }
            } else {
                if(empty($order_exist)) {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK);
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $this->lang->line('select_cancel_reason'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['status' => 0,'message' => $this->lang->line('select_cancel_reason')], REST_Controller::HTTP_OK);
                    }
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //cancel order :: end

    /*Begin::FAQs API*/
    public function FAQs_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
        }
        $faq_data = $this->api_model->get_faq_list($language_slug);
        if($faq_data['data']){
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array(
                    'result'=>$faq_data['data'],
                    'total'=>$faq_data['count'],
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'result'=>$faq_data['data'],
                    'total'=>$faq_data['count'],
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                ], REST_Controller::HTTP_OK);
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    /*End::FAQs API*/
    //table reservation changes :: start
    //book table
    public function bookTable_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $booking_date = $decrypted_data->booking_date;
            $start_time = $decrypted_data->start_time;
            $end_time = $decrypted_data->end_time;
            $user_name = $decrypted_data->user_name;
            $no_of_people = $decrypted_data->no_of_people;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_timezone = $decrypted_data->user_timezone;
            $additional_request = $decrypted_data->additional_request;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $booking_date = $this->post('booking_date');
            $start_time = $this->post('start_time');
            $end_time = $this->post('end_time');
            $user_name = $this->post('user_name');
            $no_of_people = $this->post('no_of_people');
            $restaurant_id = $this->post('restaurant_id');
            $user_timezone = $this->post('user_timezone');
            $additional_request = $this->post('additional_request');
        }
        $tokenres = $this->api_model->checkToken($user_id);    
        if($tokenres){
            if($booking_date != '' && $no_of_people != '')
            {
                $current_date_time = date('Y-m-d H:i:s');
                $booking_date = date('Y-m-d',strtotime($booking_date));
                $start_time = date('H:i',strtotime($this->common_model->setZonebaseDateTime($start_time,$user_timezone)));
                $end_time = date('H:i',strtotime($this->common_model->setZonebaseDateTime($end_time,$user_timezone)));

                $resto_content_id = $this->api_model->getResContentId($restaurant_id);
                $add_data = array(
                    'user_id' => $user_id,
                    'restaurant_content_id' => $resto_content_id,
                    'user_name' => $user_name,
                    'no_of_people' => $no_of_people,
                    'booking_date' => $booking_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'booking_status' => 'awaiting',
                    'additional_request'=>($additional_request)?$additional_request:NULL,
                    'created_by' => $user_id,
                    'created_date'=> $current_date_time
                ); 
                $event_id = $this->api_model->addRecord('table_booking',$add_data); 
                
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 1, 'message' => $this->lang->line('success_add'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK  
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK  
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
            }            
        }    
    }
    //table avalability
    public function checkTableBookingAvailability_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $booking_date = $decrypted_data->booking_date;
            $start_time = $decrypted_data->start_time;
            $end_time = $decrypted_data->end_time;
            $no_of_people = $decrypted_data->no_of_people;
            $restaurant_id = $decrypted_data->restaurant_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $booking_date = $this->post('booking_date');
            $start_time = $this->post('start_time');
            $end_time = $this->post('end_time');
            $no_of_people = $this->post('no_of_people');
            $restaurant_id = $this->post('restaurant_id');
            $user_timezone = $this->post('user_timezone');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        if($booking_date != '' && $no_of_people != ''){
            $booking_date = date('Y-m-d',strtotime($booking_date));

            $current_time = date('H:i');
            $current_time = date('H:i',strtotime($this->common_model->getZonebaseCurrentTime($current_time,$user_timezone)));

            $current_date = date('Y-m-d');
            $current_date = date('Y-m-d',strtotime($this->common_model->getZonebaseCurrentTime($current_date,$user_timezone)));
            
            $end_timedate = date('Y-m-d H:i',strtotime($end_time));
            if(strtolower(date('a',strtotime($start_time))) == strtolower(date('a',strtotime($end_time))) && strtolower(date('a',strtotime($start_time))) =='pm')
            {
                $end_timedate = date('Y-m-d H:i',strtotime('+12 hour',strtotime($end_time)));                
            }
            $end_dt = new DateTime($end_timedate);
                   
            //if(date('H:i',strtotime($start_time)) >= date('H:i',strtotime($end_time)))
            if(strtotime($start_time) >=$end_dt->getTimestamp())
            { 
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status'=>0,'message' => $this->lang->line('start_less_than_end_time'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status'=>0,'message' => $this->lang->line('start_less_than_end_time')], REST_Controller::HTTP_OK); // OK      
                }
            } else if($booking_date == $current_date && date($start_time) < date($current_time)) {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status'=>0,'message' => $this->lang->line('greater_than_current_time'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status'=>0,'message' => $this->lang->line('greater_than_current_time')], REST_Controller::HTTP_OK); // OK      
                }                
            } else {
                $check = $this->api_model->getTableBookingAvailability($booking_date, $start_time, $end_time, $no_of_people, $restaurant_id, $user_timezone, $language_slug);

                date_default_timezone_set(default_timezone);//set default time zone
                if($check)
                {
                    if($check == 'booking_available'){
                        $status = 1;
                        $msg = $this->lang->line($check);
                    } else if($check == 'booking_not_available_time') {
                        $status = 0;
                        //$msg = sprintf($this->lang->line($check), $booking_date);
                        $msg = $this->lang->line($check);
                    } else if($check == 'restaurant_closed') {
                        $status = 0;
                        $msg = $this->lang->line($check);
                        //$msg = sprintf($this->lang->line($check), $booking_date);
                    } else if($check['msg'] == 'booking_not_available_capacity') {
                        $status = 0;
                        if($check['remaining_capacity']){
                            $msg = sprintf($this->lang->line($check['err_msg']), $check['remaining_capacity']);
                        } else {
                            $msg = $this->lang->line($check['err_msg']);
                        }
                    } else if($check['msg'] == 'min_capacity_validation') {
                        $status = 0;
                        $msg = sprintf($this->lang->line($check['err_msg']), $check['minimum_capacity']);
                    }
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status'=>$status,'message' => $msg);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status'=>$status,'message' => $msg], REST_Controller::HTTP_OK); // OK  
                    }                    
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status'=>0,'message' => $this->lang->line('booking_not_available'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status'=>0,'message' => $this->lang->line('booking_not_available')], REST_Controller::HTTP_OK); // OK  
                    }                    
                }  
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //table reservation changes :: end
    public function forgotpassword_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $phone_code = $decrypted_data->phone_code;
            $phone_number = $decrypted_data->phone_number;
        }else{
            $this->getLang($this->post('language_slug'));
            $phone_code = $this->post('phone_code');
            $phone_number = $this->post('phone_number');
        }
        if(!empty($phone_code) && !empty($phone_number)) { 
            $checkRecord = $this->api_model->getRecordMultipleWhere('users', array('phone_code' => $phone_code, 'mobile_number'=>$phone_number,'user_type' => 'User', 'status' => 1));
            if(!empty($checkRecord))
            {
                if($checkRecord->is_deleted == 1){
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('user_id'=>$checkRecord->entity_id, 'PhoneNumber'=>$checkRecord->mobile_number, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('acc_validation_forgt_pass_phn'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['user_id'=>$checkRecord->entity_id, 'PhoneNumber'=>$checkRecord->mobile_number, 'status' => 0, 'is_deleted'=>1, 'message' => $this->lang->line('acc_validation_forgt_pass_phn')], REST_Controller::HTTP_OK);
                    }
                }else if($checkRecord->active == 1){
                    // create random password.
                    /*$new_password = random_string('alnum', 6);
                    $update_pwd = array(
                        'password' => md5(SALT.$new_password),
                    );
                    $this->api_model->updateMultipleWhere('users', array('phone_code' => $phone_code, 'mobile_number'=>$phone_number,'user_type' => 'User', 'status' => 1), $update_pwd);*/

                    //send otp start
                    //in phn no
                    $this->common_model->generateOTP($checkRecord->entity_id);
                    $user_record = $this->api_model->getRecord('users','entity_id',$checkRecord->entity_id);
                    $sms = $user_record->user_otp.$this->lang->line('otp_forgot_pwd');
                    $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                    $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                    $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                    //in email
                    $user_lang_slug = $this->current_lang;
                    $email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password-otp','language_slug'=>$user_lang_slug))->first_row();        
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

                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('user_id'=>$checkRecord->entity_id, 'status' => 1, 'message' => $this->lang->line('send_otp_resp_forgot_pwd'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['user_id'=>$checkRecord->entity_id, 'status' => 1, 'message' => $this->lang->line('send_otp_resp_forgot_pwd')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                    }
                }                
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('user_not_found_phn'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('user_not_found_phn')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }               
            }
        } 
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0,'message' => $this->lang->line('enter_reg_phn'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0,'message' => $this->lang->line('enter_reg_phn')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
    }
    //doordash callback function :: start
    public function doordash_status_callback_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true);

        if(!empty($jsonArray))
        {
            $external_delivery_id = $jsonArray['delivery']['external_delivery_id'];
            $getOrderId = $this->api_model->getRecord('doordash_relay_details', 'external_delivery_id',$external_delivery_id);
            $order_id = $getOrderId->order_id;
            $add_data = array('order_id'=>$order_id, 'delivery_method' => 'doordash', 'api_slug'=> 'callback_function', 'external_delivery_id'=>$external_delivery_id, 'api_response'=>serialize($jsonArray),'created_date'=>date('Y-m-d H:i:s'));
            $insert_id = $this->api_model->addRecord('doordash_relay_details',$add_data);

            if(!empty($jsonArray['delivery']['id']) && !empty($jsonArray['delivery']['status']))
            {
                $order_id = $getOrderId->order_id;
                $checkOrder = $this->api_model->getRecord('order_master', 'entity_id',$order_id);
                $driver_details = '';

                if(!empty($checkOrder))
                {
                    //Code for set order status base on Doordash status :: Start
                    $webhook_status = $jsonArray['event_category'];
                    $webhook_status_ongoing_arr = array('dasher_confirmed_store_arrival', 'dasher_picked_up', 'dasher_confirmed_consumer_arrival', 'dasher_enroute_to_pickup', 'dasher_enroute_to_dropoff');
                    $webhook_status_cancel_arr = array('delivery_pending_return', 'dasher_confirmed_return_arrival', 'dasher_dropped_off_return', 'delivery_cancelled', 'delivery_attempted', 'dasher_enroute_to_return');
                    $current_order_status = $checkOrder->order_status;
                    $new_order_status = $checkOrder->order_status;

                    if($webhook_status=='dasher_confirmed')
                    {
                        //driver details
                        $driver_details = (!empty($jsonArray['delivery']['dasher']) && is_array($jsonArray['delivery']['dasher'])) ? serialize($jsonArray['delivery']['dasher']) : '';
                    }
                    else if(in_array($webhook_status,$webhook_status_ongoing_arr))
                    {
                        $new_order_status = 'onGoing';
                        $driver_details = (!empty($jsonArray['delivery']['dasher']) && is_array($jsonArray['delivery']['dasher'])) ? serialize($jsonArray['delivery']['dasher']) : '';
                    }
                    else if($webhook_status=='dasher_dropped_off')
                    {
                        $new_order_status = 'delivered';
                        $driver_details = (!empty($jsonArray['delivery']['dasher']) && is_array($jsonArray['delivery']['dasher'])) ? serialize($jsonArray['delivery']['dasher']) : '';
                    }
                    else if(in_array($webhook_status,$webhook_status_cancel_arr))
                    {
                        $new_order_status = 'cancel';
                        $cancel_reason = ($jsonArray['event_data']['reason_comments'])?$jsonArray['event_data']['reason_comments']:'';
                        $driver_details = (!empty($jsonArray['delivery']['dasher']) && is_array($jsonArray['delivery']['dasher'])) ? serialize($jsonArray['delivery']['dasher']) : '';
                    } else {
                        $driver_details = (!empty($jsonArray['delivery']['dasher']) && is_array($jsonArray['delivery']['dasher'])) ? serialize($jsonArray['delivery']['dasher']) : '';
                    }
                    //Code for set order status base on Doordash status :: End
                    //update driver details
                    if($driver_details != ''){
                        $driver_data = array('driver_details' => $driver_details);
                        $this->api_model->updateUser('doordash_relay_details',$driver_data,'entity_id',$insert_id);
                    }
                    //Code for update order status base on Doordash status :: Start
                    if($new_order_status != $current_order_status && $current_order_status != 'placed' && $checkOrder->status != 0) {
                        $data = array('order_status' => $new_order_status);
                        if($new_order_status == 'cancel' && $cancel_reason != ''){
                            $data['cancel_reason'] = $cancel_reason;
                        }
                        $this->api_model->updateUser('order_master',$data,'entity_id',$order_id);

                        if($new_order_status != '') {
                            $addData = array(
                                'order_id'=>$order_id,
                                'order_status'=>$new_order_status,
                                'time'=>date('Y-m-d H:i:s'),
                                'status_created_by'=>'DoorDash'
                            );
                            $orderid = $this->api_model->addRecord('order_status',$addData);
                        }
                        //website and app notifications to user
                        $this->common_model->notiToUser($order_id, $checkOrder->restaurant_id, $new_order_status, $checkOrder->order_delivery,$insert_id);
                        $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                        if($new_order_status == 'cancel') {
                            $useridval = ($order_detail->user_id && $order_detail->user_id > 0) ? $order_detail->user_id : 0;
                            $this->common_model->sendSMSandEmailToUserOnCancelOrder('',$useridval,$order_id,'Driver');
                        }

                        if($checkOrder->agent_id){
                            $this->common_model->notificationToAgent($order_id, $new_order_status);
                        }
                    }
                    //Code for update order status base on Doordash status :: End
                    $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('not_found')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
                }                          
            }       
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('validation')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code                        
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //doordash callback function :: end
    //relay callback function :: start
    public function relay_status_callback_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true);
        if(!empty($jsonArray))
        {
            $relay_event_status = $jsonArray['event'];
            if($relay_event_status == 'pickup_completed') {
                if(array_key_exists("deliveries",$jsonArray) && is_array($jsonArray['deliveries'])) {
                    foreach ($jsonArray['deliveries'] as $relayorderkey => $relayordervalue) {
                        $external_delivery_id = $relayordervalue['externalId'];
                        $getOrderId = $this->api_model->getRecord('doordash_relay_details', 'external_delivery_id',$external_delivery_id);
                        $order_id = $getOrderId->order_id;
                        $relay_order_key = $relayordervalue['orderKey'];

                        $add_data = array('order_id'=>$order_id, 'relay_order_key' => ($relay_order_key)?$relay_order_key:NULL, 'delivery_method' => 'relay', 'api_slug'=> 'callback_function', 'external_delivery_id'=>$external_delivery_id, 'api_response'=>serialize($jsonArray), 'created_date'=>date('Y-m-d H:i:s'));
                        $insert_id = $this->api_model->addRecord('doordash_relay_details',$add_data);

                        if($relay_order_key && $order_id)
                        {
                            $checkOrder = $this->api_model->getRecord('order_master', 'entity_id', $order_id);
                            if(!empty($checkOrder))
                            {
                                $current_order_status = $new_order_status = $checkOrder->order_status;
                                $cancel_reason = '';

                                if($relay_event_status == 'pickup_completed'){
                                    $new_order_status = 'onGoing';
                                }

                                if($new_order_status != $current_order_status && $current_order_status != 'placed' && $checkOrder->status != 0) {
                                    $data = array('order_status' => $new_order_status);
                                    $this->api_model->updateUser('order_master',$data,'entity_id',$order_id);

                                    if($new_order_status != '') {
                                        $addData = array(
                                            'order_id'=>$order_id,
                                            'order_status'=>$new_order_status,
                                            'time'=>date('Y-m-d H:i:s'),
                                            'status_created_by'=>'Relay'
                                        );
                                        $orderid = $this->api_model->addRecord('order_status',$addData);
                                    }
                                    //website and app notifications to user
                                    $this->common_model->notiToUser($order_id, $checkOrder->restaurant_id, $new_order_status, $checkOrder->order_delivery,$insert_id);
                                    if($checkOrder->agent_id){
                                        $this->common_model->notificationToAgent($order_id, $new_order_status);
                                    }
                                }
                            }
                        }
                    }
                    $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK);
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            } else {
                $external_delivery_id = (array_key_exists("externalIds",$jsonArray) && is_array($jsonArray['externalIds'])) ? $jsonArray['externalIds'][0] : $jsonArray['externalId'];
                $getOrderId = $this->api_model->getRecord('doordash_relay_details', 'external_delivery_id',$external_delivery_id);
                $order_id = $getOrderId->order_id;

                $relay_order_key = (array_key_exists("orderKeys",$jsonArray) && is_array($jsonArray['orderKeys'])) ? $jsonArray['orderKeys'][0] : $jsonArray['orderKey'];
                $add_data = array('order_id'=>$order_id, 'relay_order_key' => ($relay_order_key)?$relay_order_key:NULL, 'delivery_method' => 'relay', 'api_slug'=> 'callback_function', 'external_delivery_id'=>$external_delivery_id, 'api_response'=>serialize($jsonArray), 'created_date'=>date('Y-m-d H:i:s'));
                $insert_id = $this->api_model->addRecord('doordash_relay_details',$add_data);

                if($relay_order_key && $order_id)
                {
                    $checkOrder = $this->api_model->getRecord('order_master', 'entity_id', $order_id);
                    if(!empty($checkOrder))
                    {
                        $current_order_status = $new_order_status = $checkOrder->order_status;
                        $cancel_reason = '';

                        if ($relay_event_status == 'delivery_completed'){
                            $new_order_status = 'delivered';
                        } else if($relay_event_status == 'order_void') {
                            $new_order_status = 'cancel';
                            $cancel_reason = ($jsonArray['voidDescription'])?$jsonArray['voidDescription']:'';
                        }

                        if($new_order_status != $current_order_status && $current_order_status != 'placed' && $checkOrder->status != 0) {
                            $data = array('order_status' => $new_order_status);
                            if($new_order_status == 'cancel' && $cancel_reason != ''){
                                $data['cancel_reason'] = $cancel_reason;
                            }
                            $this->api_model->updateUser('order_master',$data,'entity_id',$order_id);

                            if($new_order_status != '') {
                                $addData = array(
                                    'order_id'=>$order_id,
                                    'order_status'=>$new_order_status,
                                    'time'=>date('Y-m-d H:i:s'),
                                    'status_created_by'=>'Relay'
                                );
                                $orderid = $this->api_model->addRecord('order_status',$addData);
                            }
                            //website and app notifications to user
                            $this->common_model->notiToUser($order_id, $checkOrder->restaurant_id, $new_order_status, $checkOrder->order_delivery,$insert_id);
                            $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                            if($new_order_status == 'cancel') {
                                $useridval = ($order_detail->user_id && $order_detail->user_id > 0) ? $order_detail->user_id : 0;
                                $this->common_model->sendSMSandEmailToUserOnCancelOrder('',$useridval,$order_id,'Driver');
                            }

                            if($checkOrder->agent_id){
                                $this->common_model->notificationToAgent($order_id, $new_order_status);
                            }
                        }
                        $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK);
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //relay callback function :: end
    public function add_driver_tip_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $driver_tip = $decrypted_data->driver_tip;
            $tip_transaction_id = $decrypted_data->transaction_id;
            $tip_percent_val = $decrypted_data->tip_percent_val;
            $payment_option = $decrypted_data->payment_option;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $driver_tip = $this->input->post('driver_tip');
            $tip_transaction_id = $this->input->post('transaction_id');
            $tip_percent_val = $this->input->post('tip_percent_val');
            $payment_option = $this->post('payment_option');
        }
        $tokenres = $this->api_model->checkToken($user_id);
        $thirdparty_tip_update = array('status'=>1);
        $language_slug = ($language_slug) ? $language_slug : $this->current_lang;
        if($tokenres){
            $driver_tip = (float)$driver_tip;
            $tip_percent_val = (float)$tip_percent_val;
            //update order summary
            if($driver_tip && $driver_tip>0)
            {
                $orderdetails = $this->common_model->getDoorDash_OrderDetails($order_id);
                //update driver tip on thirdparty delivery method
                if($orderdetails->delivery_method == 'relay'){
                    $thirdparty_tip_update = $this->common_model->updateRelayDriverTip($order_id, $driver_tip);
                } else if($orderdetails->delivery_method == 'doordash') {
                    $thirdparty_tip_update = $this->common_model->updateDoorDashDriverTip($order_id, $driver_tip);
                }
                if($thirdparty_tip_update['status']==1){
                    //update total in order master
                    $total_amount = (float)$orderdetails->order_total;
                    $total_amount = $total_amount + $driver_tip;
                    $this->common_model->updateData('order_master',array('total_rate'=>$total_amount),'entity_id',$order_id);

                    //update driver tip table
                    $add_tip = array(
                        'order_id'=>$order_id,
                        'user_id'=>($orderdetails->user_id)?$orderdetails->user_id:0,
                        'tips_transaction_id'=>$tip_transaction_id,
                        'tip_percentage' => ($tip_percent_val>0)?$tip_percent_val:NULL,
                        'amount'=>$driver_tip,
                        'payment_option'=>$payment_option,
                        'date'=>date('Y-m-d H:i:s')
                    );
                    if($orderdetails->delivery_method == 'internal_drivers'){
                        $internaldriverid = $this->common_model->getInternalDriverId($order_id);
                        if($internaldriverid){
                            $add_tip['driver_id'] = $internaldriverid;
                        }
                    }
                    $tips_id = $this->common_model->addData('tips',$add_tip);
                    if($tips_id){
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array(
                                'status' => 1,
                                'message' => $this->lang->line('drivertip_successmsg')
                            );
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                        }else{
                            $this->response([
                                'status' => 1,
                                'message' => $this->lang->line('drivertip_successmsg')
                            ], REST_Controller::HTTP_OK);
                        }
                    } else {
                        //error while updating order summary :: try again
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('status' => 0,'message' => $this->lang->line('login_empty_token'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['status' => 0,'message' => $this->lang->line('login_empty_token')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }
                    }
                } else{
                    //refund driver tip
                    $stripe_response = array('error'=>'');
                    if($tip_transaction_id != '') {
                        $data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
                        //update driver tip table
                        $add_tip = array(
                            'order_id'=>$order_id,
                            'user_id'=>($orderdetails->user_id)?$orderdetails->user_id:0,
                            'tips_transaction_id'=>$tip_transaction_id,
                            'tip_percentage'=>NULL,
                            'payment_option'=>$payment_option,
                            'amount'=>0.00,
                            'date'=>date('Y-m-d H:i:s')
                        );
                        $tips_id = $this->common_model->addData('tips',$add_tip);
                        if(strtolower($payment_option)=='stripe' || strtolower($payment_option)=='applepay')
                        {
                            $stripe_response = $this->common_model->StripeRefund('',$order_id,$tip_transaction_id,$tips_id,strtolower($payment_option),'','full',0);
                        }
                        else if(strtolower($payment_option)=='paypal')
                        {
                            $stripe_response = $this->common_model->PaypalRefund('',$order_id,$tip_transaction_id,$tips_id,strtolower($payment_option),'full',0);
                        }

                        //Mail send code Start
                        if(!empty($stripe_response) && ($stripe_response['paymentIntentstatus'] || $stripe_response['tips_paymentIntentstatus']))
                        {
                            $updated_bytxt = $tokenres->first_name.' '.$tokenres->last_name;
                            $this->common_model->refundMailsend($order_id,$orderdetails->user_id,0,'full',$updated_bytxt,$language_slug);
                        }
                        //Mail send code End

                        //send refund noti to user
                        if(!empty($stripe_response) && ($stripe_response['paymentIntentstatus'] || $stripe_response['tips_paymentIntentstatus'])) {
                            $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,'',$tip_transaction_id,'',$stripe_response['tips_paymentIntentstatus'],$stripe_response['error']);
                        }

                        $stripe_response['error_message'] ='';
                        if($stripe_response['paymentIntentstatus']=='failed' || $stripe_response['tips_paymentIntentstatus']=='failed'){
                            $stripe_response['error_message'] = $this->lang->line('refund_failed');
                        }else if($stripe_response['paymentIntentstatus']=='canceled' || $stripe_response['tips_paymentIntentstatus']=='canceled'){
                            $stripe_response['error_message'] = $this->lang->line('refund_canceled');
                        }else if($stripe_response['paymentIntentstatus']=='pending' || $stripe_response['tips_paymentIntentstatus']=='pending'){
                            $stripe_response['error_message'] = $this->lang->line('refund_pending');
                        }
                    }
                    if($stripe_response['error']=='' && $stripe_response['error_message']==''){
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('status' => 0,'message' => $this->lang->line('refund_err_frontmssg'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['status' => 0,'message' => $this->lang->line('refund_err_frontmssg')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }
                    }else{
                        if($this->post('isEncryptionActive') == TRUE)
                        {
                            $response = array('status' => 0,'message' => $this->lang->line('refund_err_mssg'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response(['status' => 0,'message' => $this->lang->line('refund_err_mssg')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }
                    }
                }
            } else {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('tip_greaterthan_zero'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0,'message' => $this->lang->line('tip_greaterthan_zero')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    
    public function stripe_status_callback_post()
    {
        $stripe_info = stripe_details();
        // Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';

        $endpoint_secret = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_webhook_secret:$stripe_info->test_webhook_secret;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        $response_id = null;
        $stripe_resp_obj = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            $stripedata = array(
                'stripe_resp_obj' => $e,
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->common_model->addData('stripe_callback_details',$stripedata);
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            $stripedata = array(
                'stripe_resp_obj' => $e,
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->common_model->addData('stripe_callback_details',$stripedata);
            // Invalid signature
            http_response_code(400);
            exit();
        }
        // Handle the event
        switch ($event->type) {
            case 'charge.refunded':
                $stripe_resp_obj = ($event->data->object)?$event->data->object:NULL;
                $response_id = ($stripe_resp_obj->payment_intent)?$stripe_resp_obj->payment_intent:NULL;
                if($stripe_resp_obj->refunds->data->status == 'succeeded') {
                    $order_id = $this->common_model->getOrderWithTransactionId($response_id);
                    $tip_order_id = $this->common_model->getTipWithTransactionId($response_id);
                    if ($order_id) {
                        //update refund status
                        $update_refund_stat = array('refunded_amount'=> $stripe_resp_obj->amount_refunded,'stripe_refund_id'=>$stripe_resp_obj->refunds->data->id,'refund_status' => 'refunded');
                        $this->common_model->updateData('order_master',$update_refund_stat,'entity_id',$order_id);
                        //send notification to user
                        $check_noti = $this->common_model->checkUserNotiAlreadySent($order_id,'order_initiated');
                        if(!$check_noti){
                            //send refund notification
                            $order_details = $this->common_model->getOrderTransactionIds($order_id);
                            if($order_details->user_id && $order_details->user_id > 0) {
                                $this->common_model->sendRefundNoti($order_id,$order_details->user_id,$order_details->restaurant_id,$response_id,'','refunded','','');
                            }
                        }
                    }
                    if ($tip_order_id) {
                        //update refund status
                        $update_tiprefund_stat = array('refunded_amount'=> $stripe_resp_obj->amount_refunded,'stripe_refund_id'=>$stripe_resp_obj->refunds->data->id,'refund_status' => 'refunded');
                        $tips_wherearray = array('order_id' => $tip_order_id, 'tips_transaction_id' => $response_id);
                        $this->common_model->updateMultipleWhere('tips',$tips_wherearray,$update_tiprefund_stat);
                        $checktip_noti = $this->common_model->checkUserNotiAlreadySent($tip_order_id,'tip_refund_initiated');
                        if(!$checktip_noti && $tip_order_id != $order_id) {
                            //send tip refund notification
                            $order_details = $this->common_model->getOrderTransactionIds($tip_order_id);
                            if($order_details->user_id && $order_details->user_id > 0) {
                                $this->common_model->sendRefundNoti($tip_order_id,$order_details->user_id,$order_details->restaurant_id,'',$response_id,'','refunded','');
                            }
                        }
                    }
                } else if($stripe_resp_obj->refunds->data->status) {
                    $order_id = $this->common_model->getOrderWithTransactionId($response_id);
                    $tip_order_id = $this->common_model->getTipWithTransactionId($response_id);
                    if ($order_id) {
                        //update refund status
                        $refundstat = ($stripe_resp_obj->refunds->data->status == 'succeeded')?'refunded': $stripe_resp_obj->refunds->data->status;
                        $update_refund_stat = array('refunded_amount'=> $stripe_resp_obj->amount_refunded,'stripe_refund_id'=>$stripe_resp_obj->refunds->data->id,'refund_status' => $refundstat);
                        $this->common_model->updateData('order_master',$update_refund_stat,'entity_id',$order_id);

                        $ordernoti_slug = '';
                        if($refundstat == 'refunded'){
                            $ordernoti_slug = 'order_initiated';
                        } else if ($refundstat == 'pending') {
                            $ordernoti_slug = 'order_refund_pending';
                        } else if($refundstat == 'failed') {
                            $ordernoti_slug = 'order_refund_failed';
                        } else if($refundstat == 'canceled') {
                            $ordernoti_slug = 'order_refund_canceled';
                        }
                        if($ordernoti_slug != ''){
                            $checkorder_noti = $this->common_model->checkUserNotiAlreadySent($order_id,$ordernoti_slug);
                            if(!$checkorder_noti){
                                //send refund notification
                                $order_details = $this->common_model->getOrderTransactionIds($order_id);
                                if($order_details->user_id && $order_details->user_id > 0) {
                                    $this->common_model->sendRefundNoti($order_id,$order_details->user_id,$order_details->restaurant_id,$response_id,'',$refundstat,'','');
                                }
                            }
                        }
                    }
                    if ($tip_order_id) {
                        //update refund status
                        $tiprefundstat = ($stripe_resp_obj->refunds->data->status == 'succeeded')?'refunded': $stripe_resp_obj->refunds->data->status;
                        $update_tiprefund_stat = array('refunded_amount'=> $stripe_resp_obj->amount_refunded,'stripe_refund_id'=>$stripe_resp_obj->refunds->data->id,'refund_status' => $tiprefundstat);
                        $tips_wherearray = array('order_id' => $tip_order_id, 'tips_transaction_id' => $response_id);
                        $this->common_model->updateMultipleWhere('tips',$tips_wherearray,$update_tiprefund_stat);

                        $tipnoti_slug = '';
                        if($tiprefundstat == 'refunded'){
                            $tipnoti_slug = 'tip_refund_initiated';
                        } else if ($tiprefundstat == 'pending') {
                            $tipnoti_slug = 'tip_refund_pending';
                        } else if($tiprefundstat == 'failed') {
                            $tipnoti_slug = 'tip_refund_failed';
                        } else if($tiprefundstat == 'canceled') {
                            $tipnoti_slug = 'tip_refund_canceled';
                        }
                        if($tipnoti_slug != ''){
                            $checktip_noti = $this->common_model->checkUserNotiAlreadySent($tip_order_id,$tipnoti_slug);
                            if(!$checktip_noti && $tip_order_id != $order_id) {
                                //send tip refund notification
                                $order_details = $this->common_model->getOrderTransactionIds($tip_order_id);
                                if($order_details->user_id && $order_details->user_id > 0) {
                                    $this->common_model->sendRefundNoti($tip_order_id,$order_details->user_id,$order_details->restaurant_id,'',$response_id,'',$tiprefundstat,'');
                                }
                            }
                        }
                    }
                }
                break;
            case 'charge.refund.updated':
                $stripe_resp_obj = ($event->data->object)?$event->data->object:NULL;
                $response_id = ($stripe_resp_obj->payment_intent)?$stripe_resp_obj->payment_intent:NULL;
                if($stripe_resp_obj->status == 'succeeded') {
                    $order_id = $this->common_model->getOrderWithTransactionId($response_id);
                    $tip_order_id = $this->common_model->getTipWithTransactionId($response_id);
                    if ($order_id) {
                        //update refund status
                        $update_refund_stat = array('refunded_amount'=> $stripe_resp_obj->amount,'stripe_refund_id'=>$stripe_resp_obj->id,'refund_status' => 'refunded');
                        $this->common_model->updateData('order_master',$update_refund_stat,'entity_id',$order_id);
                        //send notification to user
                        $check_noti = $this->common_model->checkUserNotiAlreadySent($order_id,'order_initiated');
                        if(!$check_noti){
                            //send refund notification
                            $order_details = $this->common_model->getOrderTransactionIds($order_id);
                            if($order_details->user_id && $order_details->user_id > 0) {
                                $this->common_model->sendRefundNoti($order_id,$order_details->user_id,$order_details->restaurant_id,$response_id,'','refunded','','');
                            }
                        }
                    }
                    if ($tip_order_id) {
                        //update refund status
                        $update_tiprefund_stat = array('refunded_amount'=> $stripe_resp_obj->amount,'stripe_refund_id'=>$stripe_resp_obj->id,'refund_status' => 'refunded');
                        $tips_wherearray = array('order_id' => $tip_order_id, 'tips_transaction_id' => $response_id);
                        $this->common_model->updateMultipleWhere('tips',$tips_wherearray,$update_tiprefund_stat);
                        $checktip_noti = $this->common_model->checkUserNotiAlreadySent($tip_order_id,'tip_refund_initiated');
                        if(!$checktip_noti && $tip_order_id != $order_id) {
                            //send tip refund notification
                            $order_details = $this->common_model->getOrderTransactionIds($tip_order_id);
                            if($order_details->user_id && $order_details->user_id > 0) {
                                $this->common_model->sendRefundNoti($tip_order_id,$order_details->user_id,$order_details->restaurant_id,'',$response_id,'','refunded','');
                            }
                        }
                    }
                } else if($stripe_resp_obj->status) {
                    $order_id = $this->common_model->getOrderWithTransactionId($response_id,'from_refund_update');
                    $tip_order_id = $this->common_model->getTipWithTransactionId($response_id,'from_refund_update');
                    if ($order_id) {
                        //update refund status
                        $refundstat = ($stripe_resp_obj->status == 'succeeded') ? 'refunded' : $stripe_resp_obj->status;
                        $update_refund_stat = array('refunded_amount'=> $stripe_resp_obj->amount,'stripe_refund_id'=>$stripe_resp_obj->id,'refund_status' => $refundstat);
                        $this->common_model->updateData('order_master',$update_refund_stat,'entity_id',$order_id);

                        $ordernoti_slug = '';
                        if($refundstat == 'refunded'){
                            $ordernoti_slug = 'order_initiated';
                        } else if ($refundstat == 'pending') {
                            $ordernoti_slug = 'order_refund_pending';
                        } else if($refundstat == 'failed') {
                            $ordernoti_slug = 'order_refund_failed';
                        } else if($refundstat == 'canceled') {
                            $ordernoti_slug = 'order_refund_canceled';
                        }
                        if($ordernoti_slug != ''){
                            $checkorder_noti = $this->common_model->checkUserNotiAlreadySent($order_id,$ordernoti_slug);
                            if(!$checkorder_noti){
                                //send refund notification
                                $order_details = $this->common_model->getOrderTransactionIds($order_id);
                                if($order_details->user_id && $order_details->user_id > 0) {
                                    $this->common_model->sendRefundNoti($order_id,$order_details->user_id,$order_details->restaurant_id,$response_id,'',$refundstat,'','');
                                }
                            }
                        }
                    }
                    if ($tip_order_id) {
                        //update refund status
                        $tiprefundstat = ($stripe_resp_obj->status == 'succeeded') ? 'refunded' : $stripe_resp_obj->status;
                        $update_tiprefund_stat = array('refunded_amount'=> $stripe_resp_obj->amount,'stripe_refund_id'=>$stripe_resp_obj->id,'refund_status' => $tiprefundstat);
                        $tips_wherearray = array('order_id' => $tip_order_id, 'tips_transaction_id' => $response_id);
                        $this->common_model->updateMultipleWhere('tips',$tips_wherearray,$update_tiprefund_stat);

                        $tipnoti_slug = '';
                        if($tiprefundstat == 'refunded'){
                            $tipnoti_slug = 'tip_refund_initiated';
                        } else if ($tiprefundstat == 'pending') {
                            $tipnoti_slug = 'tip_refund_pending';
                        } else if($tiprefundstat == 'failed') {
                            $tipnoti_slug = 'tip_refund_failed';
                        } else if($tiprefundstat == 'canceled') {
                            $tipnoti_slug = 'tip_refund_canceled';
                        }
                        if($tipnoti_slug != ''){
                            $checktip_noti = $this->common_model->checkUserNotiAlreadySent($tip_order_id,$tipnoti_slug);

                            if(!$checktip_noti && $tip_order_id != $order_id) {
                                //send tip refund notification
                                $order_details = $this->common_model->getOrderTransactionIds($tip_order_id);
                                if($order_details->user_id && $order_details->user_id > 0) {
                                    $this->common_model->sendRefundNoti($tip_order_id,$order_details->user_id,$order_details->restaurant_id,'',$response_id,'',$tiprefundstat,'');
                                }
                            }
                        }
                    }
                }
                break;

            // ... handle other event types
            default:
                //echo 'Received unknown event type ' . $event->type;
            http_response_code(200);
        }
        if($stripe_resp_obj){
            $stripedata = array(
                'event_slug' => $event->type,
                'response_id' => ($response_id)?$response_id:NULL,
                'stripe_resp_obj' => $stripe_resp_obj,
                'full_response' => $event,
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->common_model->addData('stripe_callback_details',$stripedata);
        }
    }
    //wallet topup
    public function walletTopUp_post() {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $transaction_id = $decrypted_data->transaction_id;
            $amount = $decrypted_data->amount;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $transaction_id = $this->post('transaction_id');
            $amount = $this->post('amount');
        }
        $tokenres = $this->api_model->checkToken($user_id);

        if($tokenres){
            $users_wallet = $this->api_model->getUsersWalletMoney($user_id);
            $new_wallet_amount = (float)$users_wallet->wallet + (float)$amount;

            $walletdata = array('wallet' => $new_wallet_amount);            
            $this->api_model->updateUser('users',$walletdata,'entity_id',$user_id);

            $wallet_topup = array(
                'user_id' => $user_id,
                'amount' => (float)$amount,
                'credit' => 1,
                'wallet_transaction_id' => $transaction_id,
                'reason' => 'credit_via_wallet_topup',
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->api_model->addRecord('wallet_history',$wallet_topup);

            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status'=>1,'message' => $this->lang->line('success_add'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response(['status'=>1,'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function checkScheduleDelivery_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $restaurant_id = $decrypted_data->restaurant_id;
            $scheduled_date = $decrypted_data->scheduled_date;
            $slot_open_time = $decrypted_data->slot_open_time;
            $slot_close_time = $decrypted_data->slot_close_time;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $restaurant_id = $this->post('restaurant_id');
            $scheduled_date = $this->post('scheduled_date');
            $slot_open_time = $this->post('slot_open_time');
            $slot_close_time = $this->post('slot_close_time');
            $user_timezone = $this->post('user_timezone');
        }
        $scheduled_date = date('Y-m-d', strtotime($scheduled_date));
        $slot_open_time = date('H:i:s',strtotime($slot_open_time));
        $slot_close_time = date('H:i:s',strtotime($slot_close_time));
        $combinedDT = date('Y-m-d H:i:s', strtotime("$scheduled_date $slot_open_time"));
        //past datetime validation
        $scheduled_datetime_chk = $this->common_model->setZonebaseDateTime($combinedDT,$user_timezone);
        $request_date = new DateTime($scheduled_datetime_chk);
        $now = new DateTime();
        if($request_date < $now) {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0,'message' => $this->lang->line('past_datetime_notallowed'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => 0,'message' => $this->lang->line('past_datetime_notallowed')], REST_Controller::HTTP_OK);
            }
        } else {
            $res_timing = $this->common_model->getRestaurantTimings($restaurant_id,$scheduled_date,$slot_open_time,$user_timezone);
            $res_timing = ($res_timing != 'not_available') ? 'available' : $res_timing;

            if($res_timing == 'available') {
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 1,'message' => $this->lang->line('restaurant_is_available'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response(['status' => 1,'message' => $this->lang->line('restaurant_is_available')], REST_Controller::HTTP_OK);
                }
            } else {
                //restaurant_closed
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0,'message' => $this->lang->line('restaurant_is_not_available'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                } else {
                    $this->response(['status' => 0,'message' => $this->lang->line('restaurant_is_not_available')], REST_Controller::HTTP_OK);
                }
            }
        }
    }
    public function getHomeFilters_post() {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
        }
        $language_slug = ($language_slug) ? $language_slug : $this->current_lang;
        $range = $this->common_model->getRange();
        $category = $this->api_model->getcategory($language_slug);
        if($this->post('isEncryptionActive') == TRUE) {
            $response = array(
                'date'=>date("Y-m-d g:i A"),
                'category'=>$category,
                'minFilterDistance'=>$range[0]->OptionValue,
                'maxFilterDistance'=>$range[1]->OptionValue,
                'maxFilterDistanceForPickup'=>$range[2]->OptionValue,
                'status' => 1,
                'message' => $this->lang->line('record_found'));
            $encrypted_data = $this->common_model->encrypt_data($response);
            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'date'=>date("Y-m-d g:i A"),
                'category'=>$category,
                'minFilterDistance'=>$range[0]->OptionValue,
                'maxFilterDistance'=>$range[1]->OptionValue,
                'status' => 1,
                'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    //New api for apple pay using stripe :: Start
    public function createApplePayment_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = ($decrypted_data->language_slug)?$decrypted_data->language_slug:$this->current_lang;            
            $currency = $decrypted_data->currency;
            $amount = $decrypted_data->amount;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = ($this->post('language_slug'))?$this->post('language_slug'):$this->current_lang;            
            $currency = $this->post('currency');
            $amount = $this->post('amount');
        }
        $stripe_detail = $this->common_model->get_payment_method_detail('stripe');
        $stripe_secret_key = ($stripe_detail->enable_live_mode) ? $stripe_detail->live_secret_key : $stripe_detail->test_secret_key;

        $headers = array (
            'Authorization: Bearer '.$stripe_secret_key,
            'Content-type: application/x-www-form-urlencoded'
        );

        $fields1 = array(
            'amount' => $amount,
            'currency' => $currency
        );
        $post1 = array();
        $this->http_build_query_for_curl($fields1,$post1);
       
        $ch1 = curl_init();
        curl_setopt( $ch1,CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
        curl_setopt( $ch1,CURLOPT_POST, true );
        curl_setopt( $ch1,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch1,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch1,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch1,CURLOPT_POSTFIELDS, http_build_query($post1));
        $result1 = curl_exec($ch1);
        curl_close($ch1);
        $result_arr = json_decode($result1,true);

        if(strpos($result,"error") && !empty($result_arr['error']))
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array(
                    'stripe_response'=>$result_arr,
                    'status' => 0,
                    'message' => $this->lang->line('payment_error')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response([
                    'stripe_response'=>$result_arr,
                    'status' => 0,
                    'message' => $this->lang->line('payment_error')
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }            
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'stripe_id'=>$result_arr['id'],
                    'client_secret'=>$result_arr['client_secret'],
                    'stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('success')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'stripe_id'=>$result_arr['id'],
                    'client_secret'=>$result_arr['client_secret'],
                    'stripe_response'=>$result_arr,
                    'status' => 1,
                    'message' => $this->lang->line('success')
                ], REST_Controller::HTTP_OK);
            }
        }        
    }
    //New api for apple pay using stripe :: End
    //to set a card as default in stripe
    public function set_default_card($stripe, $payment_method_id = '', $stripe_customer_id = '') {
        if($payment_method_id != '' && $stripe_customer_id != '') {
            try{
                $stripe->customers->update(
                    $stripe_customer_id,
                    ['invoice_settings' => ['default_payment_method' => $payment_method_id]]
                );
            } catch (Exception $e) {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array(
                        'status' => 0,
                        'message' => $e->getMessage()
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response([
                        'status' => 0,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                exit;
            }
        }
    }
    public function Restaurant_ErrorReport_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $report_topic = $decrypted_data->report_topic;
            $email = trim($decrypted_data->email);            
            $message = trim($decrypted_data->message);
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $report_topic = $this->post('report_topic');
            $email = trim($this->post('email'));
            $message = trim($this->post('message'));
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;

        if($email!='' && $message!='')
        {
            $addData = array(
                'report_topic' => $report_topic,
                'reporter_email' => $email,
                'reporter_message' => $message
            );
            $this->api_model->addRecord('restaurant_error_reports', $addData);

            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status'=>1,'message' => $this->lang->line('report_error_success'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status'=>1,'message' => $this->lang->line('report_error_success')], REST_Controller::HTTP_OK); // OK      
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => 0, 'message' => $this->lang->line('report_error_msg'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => 0, 'message' => $this->lang->line('report_error_msg')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
}