<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Driver_api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/driver_api_model');
        $this->load->library('form_validation');
    }
    //common lang fucntion
    public function getLang($language_slug)
    {
        if($language_slug){
            $languages = $this->driver_api_model->getLanguages($language_slug);
            $this->current_lang = $languages->language_slug;
            $this->lang->load('messages_lang', $languages->language_directory);
        } else {
            $default_lang = $this->common_model->getdefaultlang();
            $this->current_lang = $default_lang->language_slug;
            $this->lang->load('messages_lang', $default_lang->language_directory);
        }
    }
    // Login API
    public function login_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $phone_number = $decrypted_data->PhoneNumber;
            $phone_code = $decrypted_data->phone_code;
            $Email = $decrypted_data->Email;
            $password = $decrypted_data->Password;
            $firebase_token = $decrypted_data->firebase_token;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
        }else{
            $this->getLang($this->post('language_slug'));
            $phone_number = $this->post('PhoneNumber');
            $phone_code = $this->post('phone_code');
            $Email = $this->post('Email');
            $password = $this->post('Password');
            $firebase_token = $this->post('firebase_token');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
        }
        if($phone_number && $phone_code && $phone_code!='undefined') {
            $login = $this->driver_api_model->getLogin($password, NULL, $phone_number, $phone_code);
        } else if ($Email) {
            $login = $this->driver_api_model->getLogin($password, $Email, NULL);
        }
        if(!empty($login)){
            if (!empty($firebase_token)) {
                $data = array('device_id' => $firebase_token);
                $this->driver_api_model->updateUser('users', $data, 'entity_id', $login->entity_id);
            }
            if($login->status==1){
                // update device 
                $image = ($login->image) ? image_url.$login->image : '';
                if($latitude!='' && $longitude!='')
                {
                    $traking_data = array(
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'driver_id' => $login->entity_id                    
                    );  
                    $this->driver_api_model->addRecord('driver_traking_map', $traking_data);
                }

                $driver_data = array('availability_status' => 1);

                $this->driver_api_model->updateUser('users', $driver_data, 'entity_id', $login->entity_id);
                
                $login_detail = array(
                    'FirstName' => $login->first_name,
                    'LastName'=> $login->last_name,
                    'image' => $image,
                    'PhoneNumber' => $login->mobile_number,
                    'phone_code' => $login->phone_code,
                    'Email' => $login->email,
                    'availabilityStatus'=>true,
                    'driver_temperature' => $login->driver_temperature,
                    'UserID' => $login->entity_id
                );
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('login' => $login_detail, 'status' => 1, 'message' => $this->lang->line('login_success'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['login' => $login_detail, 'status' => 1, 'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code    
                }
                
            } else if ($login->status == 0){
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 2, 'message' => $this->lang->line('login_deactivedis').' '.$adminEmail->OptionValue, 'email' => $adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 2, 'message' => $this->lang->line('login_deactivedis').' '.$adminEmail->OptionValue, 'email' => $adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            } 
        }        
        else
        {
            if($phone_number){
                $emailexist = $this->driver_api_model->getRecord('users','mobile_number', $phone_number);
            }else{
                $emailexist =  $this->driver_api_model->getRecord('users', 'email', $Email);
            }
            if($emailexist){
                $message_type = ($phone_number) ? $this->lang->line('invalid_phone_password') : $this->lang->line('invalid_email_password');
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0, 'message' => $message_type);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response([
                        'status' => 0,
                        'message' => $message_type
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
            else
            {
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                $message_type = ($phone_number) ? $this->lang->line('new_drivermsg1') : $this->lang->line('new_drivermsg3');
                $message_dis = $message_type.' '.$adminEmail->OptionValue.' '.$this->lang->line('new_drivermsg2');
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 2, 'message' => $message_dis, 'email'=>$adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response([
                        'status' => 2,
                        'message' => $message_dis,
                        'email'=>$adminEmail->OptionValue
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }        
    }
    // Forgot Password
    public function forgotpassword_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $email = strtolower($decrypted_data->Email);
        }else{
            $this->getLang($this->post('language_slug'));
            $email = strtolower($this->post('Email'));
        }
        if(!empty($email)){
            $checkRecord = $this->driver_api_model->getRecordMultipleWhere('users', 
                array(
                    'email' => $email,
                    'user_type' => 'Driver',
                    'status' => 1
                )
            );
            if(!empty($checkRecord)){
                if($checkRecord->active == 1){
                    $verificationCode = random_string('alnum', 20).$checkRecord->entity_id.random_string('alnum', 5);
                    $user_lang_slug = $this->current_lang;
                    $confirmationLink = '<a href="'.base_url().'user/reset/'.$verificationCode.'/'.$user_lang_slug.'" style="text-decoration:underline;">'.$this->lang->line('here').'</a>';                    
                    $email_template = $this->db->get_where('email_template',
                        array(
                            'email_slug' => 'forgot-password',
                            'language_slug' => $user_lang_slug
                        )
                    )->first_row();        
                    $arrayData = array(
                        'FirstName' => $checkRecord->first_name,
                        'ForgotPasswordLink' => $confirmationLink
                    );
                    $EmailBody = generateEmailBody($email_template->message,$arrayData);
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
                    $this->email->to($email);
                    $this->email->subject($email_template->subject);
                    $this->email->message($EmailBody);
                    $this->email->send();
                    // update verification code
                    $addata = array('email_verification_code' => $verificationCode);
                    $this->driver_api_model->updateUser('users',$addata,'entity_id',$checkRecord->entity_id);
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 1,'message' => $this->lang->line('forgot_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    } else {
                        $this->response(['status' => 1,'message' => $this->lang->line('forgot_success')], REST_Controller::HTTP_OK);
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['status' => 0,'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                    }
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' => $this->lang->line('user_not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                }else{
                    $this->response(['status' => 0,'message' => $this->lang->line('user_not_found')], REST_Controller::HTTP_OK);
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0,'message' => $this->lang->line('enter_reg_email'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 0,'message' => $this->lang->line('enter_reg_email')], REST_Controller::HTTP_OK);
            }
        }
    }
    //add review
    public function addReview_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $rating = $decrypted_data->rating;
            $review = $decrypted_data->review;
            $order_user_id = $decrypted_data->order_user_id;
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $rating = $this->post('rating');
            $review = $this->post('review');
            $order_user_id = $this->post('order_user_id');
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
        }
        if($rating != '' && $review != ''){
            $add_data = array(
                'rating' => trim($rating),
                'review' => utf8_encode($review),
                'order_user_id' => $order_user_id,
                'user_id' => $user_id,
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->driver_api_model->addRecord('review', $add_data);
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'message' => $this->lang->line('success_add'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => 1,'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('validation')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
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
            $driver_temperature = $decrypted_data->driver_temperature;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $first_name = $this->post('first_name');
            $last_name = $this->post('last_name');
            $driver_temperature = $this->post('driver_temperature');
        }
        $tokenusr = $this->driver_api_model->checkToken($user_id);
        if($tokenusr){
            $add_data =array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'driver_temperature' => $driver_temperature
            );
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
            $this->driver_api_model->updateUser('users', $add_data, 'entity_id', $user_id);
            $token = $this->driver_api_model->checkToken($user_id);
            $image = ($token->image) ? image_url.$token->image : ''; 
            $login_detail = array(
                'FirstName' => $token->first_name,
                'LastName'=> $token->last_name,
                'image' => $image,
                'PhoneNumber' => $token->mobile_number,
                'phone_code' => $token->phone_code,
                'Email' => $token->email,
                'availabilityStatus' => $token->availability_status,
                'driver_temperature' => $token->driver_temperature,
                'UserID' => $token->entity_id
            );
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('profile' => $login_detail,'status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['profile' => $login_detail,'status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //change password
    public function changePassword_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $old_password = $decrypted_data->old_password;
            $password = $decrypted_data->password;
            $confirm_password = $decrypted_data->confirm_password;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $old_password = $this->post('old_password');
            $password = $this->post('password');
            $confirm_password = $this->post('confirm_password');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres){
            if(md5(SALT.$old_password) == $tokenres->password){
                if($confirm_password == $password){
                    $this->db->set('password',md5(SALT.$password));
                    $this->db->where('entity_id',$user_id);
                    $this->db->update('users');
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 1,'message' => $this->lang->line('success_password_change'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status' => 1,'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $this->lang->line('confirm_password'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status' => 0,'message' => $this->lang->line('confirm_password')], REST_Controller::HTTP_OK); // OK  
                    }
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' => $this->lang->line('old_password'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 0,'message' => $this->lang->line('old_password')], REST_Controller::HTTP_OK); // OK  
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    //accept order
    public function acceptOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $order_status = $decrypted_data->order_status;
            $driver_map_id = $decrypted_data->driver_map_id;
            $cancel_reason = $decrypted_data->cancel_reason;
            $language_slug = $decrypted_data->language_slug;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $order_status = $this->post('order_status');
            $driver_map_id = $this->post('driver_map_id');
            $cancel_reason = $this->post('cancel_reason');
            $language_slug = $this->post('language_slug');
            $user_timezone = $this->post('user_timezone');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres) {
            if($order_id)
            {
                //Code for check the order is deliver or not :: Start
                $current_status = $this->driver_api_model->checkOrderStatus($order_id);
                if($current_status!='' && (strtolower($current_status)=='delivered' || strtolower($current_status)=='complete'))
                {
                    $delivery_msg = sprintf($this->lang->line('already_delivered'),$order_id);
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $delivery_msg);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                    }
                }
                if($current_status!='' && strtolower($current_status)=='cancel')
                {
                    $delivery_msg = sprintf($this->lang->line('already_cancled'),$order_id);
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0,'message' => $delivery_msg);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                    }
                }            
                //Code for check the order is deliver or not :: End

                $details = $this->driver_api_model->getRecordMultipleWhere('order_driver_map',array('driver_map_id'=>$driver_map_id, 'driver_id'=>$user_id));
                //check if order already assigned to other driver.
                $assign_details = $this->driver_api_model->getRecordMultipleWhere('order_driver_map',array('order_id' => $order_id,'is_accept' => 1));

                if(!empty($details) && $details->is_accept!='1' && $details->is_accept!='2' && empty($assign_details)) {
                    if($order_status == 'cancel') {
                        $order_detail = $this->driver_api_model->getRecord('order_master','entity_id',$order_id);

                        //Code for update the driver startus in main relation table :: Start
                        $cancel_reason = ($cancel_reason)?$cancel_reason:'';
                        $add_data = array('cancel_reason'=>$cancel_reason,'is_accept'=>2,'status_created_by'=>'Driver');
                        $this->driver_api_model->updateUser('order_driver_map',$add_data,'driver_map_id',$driver_map_id);
                        //End

                        $this->db->set('order_status','accepted')->where('entity_id', $order_id)->update('order_master');

                        //Code for send notification again if no driver added in relation table
                        $driver_mapdata = $this->driver_api_model->getOrderDriverMap($order_id,$user_id,$order_detail->restaurant_id, $order_detail->user_id);
                        
                        $data = array('order_id'=>$order_id,'user_id'=>$user_id,'order_status'=>'rejected','time'=>date('Y-m-d H:i:s'),'status_created_by'=>'Driver');
                        $this->driver_api_model->addRecord('order_status',$data);

                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$language_slug))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);
                        
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status'=>1,'message' => $this->lang->line('push_order_reject'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status'=>1,'message' => $this->lang->line('push_order_reject')], REST_Controller::HTTP_OK); // OK */
                        }
                    }
                    else
                    {
                        $order_detail = $this->driver_api_model->getRecord('order_master','entity_id',$order_id);
                        if($order_detail->order_status == 'placed' || $order_detail->order_status == 'accepted'){
                            // $add_data = array('order_id'=>$order_id,'user_id'=>$user_id,'order_status'=>'preparing','time'=>date('Y-m-d H:i:s'),'status_created_by'=>'Driver');
                            // $this->driver_api_model->addRecord('order_status',$add_data);

                            // adding notification for website
                            // $notification = array(
                            //     'order_id' => $order_id,
                            //     'user_id' => $order_detail->user_id,
                            //     'notification_slug' => 'order_preparing',
                            //     'view_status' => 0,
                            //     'datetime' => date("Y-m-d H:i:s"),
                            // );
                            // $this->driver_api_model->addRecord('user_order_notification',$notification);
                        }
                        $detail = $this->driver_api_model->acceptOrder($order_id,$driver_map_id,$user_id,$order_detail->order_status);

                        //Code for driver commisson :: Start
                        $this->driver_api_model->setDriverCommission($order_id,$user_id);
                        //End

                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$language_slug))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);
                        
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('user_detail'=>$detail,'status'=>1,'message' => $this->lang->line('order_accept'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['user_detail'=>$detail,'status'=>1,'message' => $this->lang->line('order_accept')], REST_Controller::HTTP_OK); // OK
                        }
                    }
                }
                else
                {
                    $resmessage = $this->lang->line('driver_order_accepted');
                    if($details->is_accept=='2')
                    {
                        $resmessage = $this->lang->line('order_rejected');
                    }
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status'=>0,'message' => $resmessage);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status'=>0,'message' => $resmessage], REST_Controller::HTTP_OK); // OK
                    }
                } 
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' => $this->lang->line('not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //get order of driver
    public function getallOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
            $language_slug = $this->post('language_slug');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres){
            $detail = $this->driver_api_model->getallOrder($user_id,$user_timezone,$language_slug);
            $total_earning = $detail['past_order_total_commission'];
            $social_details = array(
                'facebook' => facebook,
                'twitter' => twitter,
                'linkedin' => linkedin,
                'instagram' => instagram
            );
            
            unset($detail['past_order_total_commission']);
            $default_currency = get_default_system_currency();
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('order_list' => $detail,'total_earning'=>$total_earning,'social_details'=>$social_details,'currency'=>$default_currency->currency_symbol,'status' => 1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['order_list' => $detail,'total_earning'=>$total_earning,'social_details'=>$social_details,'currency'=>$default_currency->currency_symbol,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }   
    }
    //change status after delivery
    public function deliverOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $status = $decrypted_data->status;
            $subtotal = $decrypted_data->subtotal;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $status = $this->post('status');
            $subtotal = $this->post('subtotal');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres){

            //Code for check the order is deliver or not :: Start
            $current_status = $this->driver_api_model->checkOrderStatus($order_id);
            if($current_status!='' && (strtolower($current_status)=='delivered' || strtolower($current_status)=='complete'))
            {
                $delivery_msg = sprintf($this->lang->line('already_delivered'),$order_id);
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' => $delivery_msg);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                }
            }
            if($current_status!='' && strtolower($current_status)=='cancel')
            {
                $delivery_msg = sprintf($this->lang->line('already_cancled'),$order_id);
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' => $delivery_msg);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                }
            }            
            //Code for check the order is deliver or not :: End

            /*refer and earn changes start*/
            //credit amount to referer in first transaction (if referral code used while signup)
            //get user detail who placed the order
            $userorderdetail = $this->driver_api_model->getRecord('order_master','entity_id',$order_id);
            $order_user_id = $userorderdetail->user_id;
            $getreferraldetail = $this->driver_api_model->getRecord('users','entity_id',$order_user_id);
            if(strtolower($status) == 'delivered' || strtolower($status) == 'complete'){
                if(!empty($getreferraldetail->referral_code_used)) {
                    //checking in wallet if there is any entry of order user as referee.
                    $check_wallet_history = $this->driver_api_model->getRecordMultipleWhere('wallet_history',array('referee_id'=>$order_user_id, 'is_deleted'=>0));
                    if(empty($check_wallet_history)){
                        //get referer details
                        $getUser = $this->driver_api_model->getRecord('users','referral_code',$getreferraldetail->referral_code_used);
                        //add wallet money(credited) in users table
                        $wallet = $getUser->wallet;
                        $referral_amount = $this->driver_api_model->getSystemOption('referral_amount');
                        $credit_amount = ($userorderdetail->subtotal * $referral_amount->OptionValue)/100;
                        $addWallet_amount = array(
                            'wallet'=>$wallet+$credit_amount
                        );
                        $this->driver_api_model->updateMultipleWhere('users', array('entity_id'=>$getUser->entity_id,'referral_code'=>$getUser->referral_code), $addWallet_amount);
                        
                        //add wallet history - amount credited
                        $updateWalletHistory = array(
                            'user_id'=>$getUser->entity_id, //referrer
                            'referee_id'=>$order_user_id, //referee (order user id)
                            'amount'=>$credit_amount,
                            'credit'=>1,
                            'reason'=>'referral_bonus',
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->driver_api_model->addRecord('wallet_history',$updateWalletHistory);
                    }
                }
            }
            /*refer and earn changes end*/

            $add_data = array('order_id' => $order_id,'user_id'=>$user_id,'order_status' => $status,'time' => date('Y-m-d H:i:s'),'status_created_by' => 'Driver');
            $this->driver_api_model->addRecord('order_status',$add_data);
            $detail = $this->driver_api_model->deliveredOrder($order_id,$status,$subtotal,$user_id);
            // adding notification for website
            $order_detail = $this->driver_api_model->getRecord('order_master','entity_id',$order_id);
            if($order_detail->user_id && $order_detail->user_id > 0) {
                $notification = array(
                    'order_id' => $order_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => 'order_delivered',
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->driver_api_model->addRecord('user_order_notification',$notification);
            }
            if($order_detail->agent_id){
                $this->common_model->notificationToAgent($order_id, "delivered");
            }
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('order_detail' => $detail,'status' => 1,'message' => $status);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['order_detail' => $detail,'status' => 1,'message' => $status], REST_Controller::HTTP_OK); // OK */
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //get order commission
    public function getCommissionList_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres){
            $detail = $this->driver_api_model->getCommissionList($user_id,$user_timezone);
            $total_earning = $detail['past_order_total_commission'];
            unset($detail['past_order_total_commission']);
            $default_currency = get_default_system_currency();
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('CommissionList' => $detail,'total_earning'=>$total_earning,'currency'=>$default_currency->currency_symbol,'status' => 1,'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['CommissionList' => $detail,'total_earning'=>$total_earning,'currency'=>$default_currency->currency_symbol,'status' => 1,'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //track driver location
    public function driverTracking_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $latitude = $decrypted_data->latitude;
            $longitude = $decrypted_data->longitude;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id); 
        if($tokenres){
            if($latitude && $longitude){
                //$data = array('latitude'=>$latitude,'longitude'=>$longitude);
               // $this->driver_api_model->updateUser('driver_traking_map',$data,'driver_id',$user_id);
                $traking_data = array(
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'driver_id'=>$user_id,
                );  
                $this->driver_api_model->addRecord('driver_traking_map',$traking_data);
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 0,'message' =>  $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response([
                        'status' => 0,
                        'message' =>  $this->lang->line('validation')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
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
        $tokenres = $this->driver_api_model->getRecord('users', 'entity_id',$user_id);
        if($tokenres){
            $data = array(
                'device_id' => "",
                'availability_status' => 0
            );
            $this->driver_api_model->updateUser('users',$data,'entity_id',$tokenres->entity_id);
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'message' => $this->lang->line('user_logout'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => 1,'message' => $this->lang->line('user_logout')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
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
        $tokenres = $this->driver_api_model->checkToken($user_id); 
        if($tokenres){
            $data = array('language_slug' => $language_slug);
            $this->driver_api_model->updateUser('users',$data,'entity_id',$user_id);
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
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
        $tokenusr = $this->driver_api_model->checkToken($user_id);
        if($tokenusr && !empty($firebase_token)){
            $data = array('device_id' => $firebase_token);
            $this->driver_api_model->updateUser('users',$data,'entity_id',$user_id);
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function cancel_order_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $driver_map_id = $decrypted_data->driver_map_id;
            $cancel_reason = $decrypted_data->cancel_reason;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $driver_map_id = $this->post('driver_map_id');
            $cancel_reason = $this->post('cancel_reason');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres)
        {
            if ($tokenres->status == 1)
            {
                if($order_id)
                {
                    //Code for check the order is deliver or not :: Start
                    $current_status = $this->driver_api_model->checkOrderStatus($order_id);
                    if($current_status!='' && (strtolower($current_status)=='delivered' || strtolower($current_status)=='complete'))
                    {
                        $delivery_msg = sprintf($this->lang->line('already_delivered'),$order_id);
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,'message' => $delivery_msg);
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                        }
                    }
                    if($current_status!='' && strtolower($current_status)=='cancel')
                    {
                        $delivery_msg = sprintf($this->lang->line('already_cancled'),$order_id);
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0,'message' => $delivery_msg);
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                        }
                    }            
                    //Code for check the order is deliver or not :: End

                    $details = $this->driver_api_model->getRecordMultipleWhere('order_driver_map',array('driver_map_id'=>$driver_map_id));
                    if(!empty($details))
                    {
                        $payment_methodarr = array('stripe','paypal','applepay');
                        $response = array('error'=>'');
                        //initiate refund
                        $orderdata['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
                        if(($orderdata['order_records']->transaction_id!='' && in_array(strtolower($orderdata['order_records']->payment_option), $payment_methodarr) && $orderdata['order_records']->refund_status!='refunded') || ($orderdata['order_records']->tips_transaction_id!='' && $orderdata['order_records']->tips_refund_status!='refunded')){
                            $transaction_id = ($orderdata['order_records']->transaction_id!='' && $orderdata['order_records']->refund_status!='refunded')?$orderdata['order_records']->transaction_id:'';
                            $tips_transaction_id = ($orderdata['order_records']->tips_transaction_id!='' && $orderdata['order_records']->tips_refund_status!='refunded')?$orderdata['order_records']->tips_transaction_id:'';

                            $tip_payment_option = ($orderdata['order_records']->tip_payment_option!='' && $orderdata['order_records']->tip_payment_option!=null)?$orderdata['order_records']->tip_payment_option:'';
                            if($tip_payment_option=='' && $tips_transaction_id!='')
                            {
                                $tip_payment_option = 'stripe';
                            }
                            if(strtolower($orderdata['order_records']->payment_option)=='stripe' || strtolower($orderdata['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                            {
                                $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                            }
                            else if(strtolower($orderdata['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                            {   
                                $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                            }

                            //Mail send code Start
                            if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                            {
                                $updated_bytxt = $tokenres->first_name.' '.$tokenres->last_name;
                                $this->common_model->refundMailsend($order_id,$orderdata['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
                            }
                            //Mail send code End

                            if(in_array(strtolower($orderdata['order_records']->payment_option), $payment_methodarr))
                            {
                                //Code for save updated by and date value 
                                $update_array = array(
                                    'updated_by' => $user_id,
                                    'updated_date' => date('Y-m-d H:i:s')
                                );
                                $this->db->set($update_array)->where('entity_id',$order_id)->update('order_master');
                                //Code for save updated by and date value
                            }
                        }
                        //get user of order
                        $userData = $this->driver_api_model->getUserofOrder($order_id);
                        /*wallet changes start*/
                        $users_wallet = $this->driver_api_model->getUsersWalletMoney($userData->entity_id);
                        $current_wallet = $users_wallet->wallet; //money in wallet

                        $credit_walletDetails = $this->driver_api_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $userData->entity_id, 'credit'=>1, 'is_deleted'=>0));
                        $credit_amount = $credit_walletDetails->amount;

                        $debit_walletDetails = $this->driver_api_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $userData->entity_id, 'debit'=>1, 'is_deleted'=>0));
                        $debit_amount = $debit_walletDetails->amount;

                        $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                        $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                        
                        //delete order_id from wallet history and update users wallet
                        if(!empty($credit_amount) || !empty($debit_amount)){
                            $this->driver_api_model->deletewallethistory($order_id);
                            $new_wallet = array(
                                'wallet'=>$new_wallet_amount
                            );
                            $this->driver_api_model->updateMultipleWhere('users', array('entity_id'=>$userData->entity_id), $new_wallet);
                        } 
                        /*wallet changes end*/

                        $data = array('order_id'=>$order_id,'user_id'=>$user_id,'order_status'=>'cancel','time'=>date('Y-m-d H:i:s'),'status_created_by'=>'Driver');
                        $this->driver_api_model->addRecord('order_status',$data);
                        // set order status cancel
                        $this->db->set('order_status','cancel')->where('entity_id', $order_id)->update('order_master');
                        if($cancel_reason != ''){
                            $this->db->set('cancel_reason',$cancel_reason)->where('entity_id', $order_id)->update('order_master');
                        } 
                        //get user of order
                        $userData = $this->driver_api_model->getUserofOrder($order_id);
                        // load language
                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$userData->language_slug))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);
                        
                        $userorder_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                        if($userorder_detail->user_id && $userorder_detail->user_id >0){
                            $notification = array(
                                'order_id' => $order_id,
                                'user_id' => $userorder_detail->user_id,
                                'notification_slug' => 'order_canceled',
                                'view_status' => 0,
                                'datetime' => date("Y-m-d H:i:s"),
                            );
                            $this->common_model->addData('user_order_notification',$notification);
                        }
                        if($userorder_detail->agent_id){
                            $this->common_model->notificationToAgent($order_id, 'cancel');
                        }
                        
                        if(!empty($userData) && $userData->device_id && $userData->notification == 1){
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('push_order_cancel'),$order_id);
                            if ($cancel_reason != '') {
                                $message .= ' - '.$cancel_reason;
                            }
                            $fields['to'] = $userData->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default','title'=>$this->lang->line('customer_app_name'));
                            $fields['data'] = array ('screenType'=>'order','title'=>$this->lang->line('customer_app_name'),'body'  => $message,'wallet_amount'=>$new_wallet_amount);
                            
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
                        //send email and sms notification to user on order cancel
                        $langslugval = ($userData->language_slug) ? $userData->language_slug : '';
                        $useridval = ($userData->entity_id && $userData->entity_id >0) ? $userData->entity_id : 0;
                        $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$order_id,'Driver');
                        if($userorder_detail->user_id && $userorder_detail->user_id >0){
                            //send refund noti to user
                            if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                                $this->common_model->sendRefundNoti($order_id,$orderdata['order_records']->user_id,$orderdata['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                            }
                        }
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status'=>1,'message' => $this->lang->line('push_order_reject'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status'=>1,'message' => $this->lang->line('push_order_reject')], REST_Controller::HTTP_OK); // OK */  
                        }
                    }
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response([
                            'status' => 0,
                            'message' => $this->lang->line('not_found')
                        ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
                    }
                }
            }
            else
            {
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 1000, 'message' => $this->lang->line('login_deactive'), 'email'=>$adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response([
                        'status' => 1000,
                        'message' => $this->lang->line('login_deactive'),
                        'email'=>$adminEmail->OptionValue
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
            }
        }   
    }    
    public function collect_order_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $driver_map_id = $decrypted_data->driver_map_id;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $driver_map_id = $this->post('driver_map_id');
            $language_slug = $this->post('language_slug');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres)
        {
            if ($tokenres->status == 1)
            {
                if($order_id)
                {
                    $details = $this->driver_api_model->getRecordMultipleWhere('order_driver_map',array('driver_map_id'=>$driver_map_id));
                    if(!empty($details))
                    {
                        //Code for check the order is deliver or not :: Start
                        $current_status = $this->driver_api_model->checkOrderStatus($order_id);
                        if($current_status!='' && strtolower($current_status)=='delivered')
                        {
                            $delivery_msg = sprintf($this->lang->line('already_delivered'),$order_id);
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array('status' => 0,'message' => $delivery_msg);
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }else{
                                $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                            }
                        }
                        if($current_status!='' && strtolower($current_status)=='cancel')
                        {
                            $delivery_msg = sprintf($this->lang->line('already_cancled'),$order_id);
                            if($this->post('isEncryptionActive') == TRUE){
                                $response = array('status' => 0,'message' => $delivery_msg);
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }else{
                                $this->response(['status' => 0,'message' => $delivery_msg], REST_Controller::HTTP_OK); // OK */
                            }
                        }
                        //Code for check the order is deliver or not :: End

                        $data = array('order_id'=>$order_id,'user_id'=>$user_id,'order_status'=>'onGoing','time'=>date('Y-m-d H:i:s'),'status_created_by'=>'Driver');
                        $this->driver_api_model->addRecord('order_status',$data);
                        // set order status cancel
                        $this->db->set('order_status','onGoing')->where('entity_id', $order_id)->update('order_master');
                        
                        //get user of order
                        $userData = $this->driver_api_model->getUserofOrder($order_id);
                        // load language
                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$userData->language_slug))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);

                        if($userData && $userData->entity_id > 0){
                            //add website notification
                            $notification = array(
                                'order_id' => $order_id,
                                'user_id' => $userData->entity_id,
                                'notification_slug' => 'order_ongoing',
                                'view_status' => 0,
                                'datetime' => date("Y-m-d H:i:s"),
                            );
                            $this->driver_api_model->addRecord('user_order_notification',$notification);
                        }
                        //app notification
                        if(!empty($userData) && $userData->device_id){
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('order_ongoing'),$order_id);                            
                            $fields['to'] = $userData->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default','title'=>$this->lang->line('customer_app_name'));
                            $fields['data'] = array ('screenType'=>'order','title'=>$this->lang->line('customer_app_name'),'body'  => $message);
                            
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
                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$language_slug))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status'=>1,'message' => $this->lang->line('push_order_collect'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status'=>1,'message' => $this->lang->line('push_order_collect')], REST_Controller::HTTP_OK); // OK */  
                        }
                    }
                    else
                    {
                        if($this->post('isEncryptionActive') == TRUE){
                            $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }else{
                            $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
                        }
                    }
                }
                else
                {
                    if($this->post('isEncryptionActive') == TRUE){
                        $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }else{
                        $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code   
                    }
                }
            }
            else
            {
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE){
                    $response = array('status' => 1000, 'message' => $this->lang->line('login_deactive'), 'email'=>$adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 1000, 'message' => $this->lang->line('login_deactive'), 'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => -1,'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => -1,'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
            }
        }   
    }
    // get order details for a order id
    public function get_order_detail_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE) {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
        } else {
            $this->getLang($this->post('language_slug'));          
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');        
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        if($tokenres) {
            if ($tokenres->status == 1) {
                $detail = $this->driver_api_model->getOrderDetails($order_id);
                $driverdetail = $this->driver_api_model->getDriverDetails($user_id);
                if (!empty($detail) || !empty($driverdetail)) {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array( 'order_detail'=>$detail, 'driver_detail'=>$driverdetail, 'status'=>1, 'message' => $this->lang->line('record_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['order_detail'=>$detail, 'driver_detail'=>$driverdetail, 'status'=>1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array( 'status'=> 0,'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status'=> 0,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK
                    }
                }
            } else {
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 1000, 'message' => $this->lang->line('login_deactive'), 'email'=>$adminEmail->OptionValue);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 1000, 'message' => $this->lang->line('login_deactive'), 'email'=>$adminEmail->OptionValue], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => '');
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => ''], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
            }
        }    
    }
    // get driver current lang + all active languages
    public function getDriverCurrentLang_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE) {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
        } else {
            $this->getLang($this->post('language_slug'));          
            $user_id = $this->post('user_id');
        }
        $tokenres = $this->driver_api_model->checkToken($user_id);
        $driver_lang = ($tokenres->language_slug)?$tokenres->language_slug:NULL;
        $active_langs = $this->driver_api_model->getActiveLanguages();

        if($this->post('isEncryptionActive') == TRUE) {
            $response = array('driver_current_lang'=>$driver_lang, 'languages'=>$active_langs, 'status'=>1, 'message' => $this->lang->line('record_found'));
            $encrypted_data = $this->common_model->encrypt_data($response);
            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response(['driver_current_lang'=>$driver_lang, 'languages'=>$active_langs, 'status'=>1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
        }
    }
    // update user availability status (Online/Offline)
    public function change_driver_availability_status_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $availability_status = $decrypted_data->availability_status;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $availability_status = $this->post('availability_status');
        }
        $tokenusr = $this->driver_api_model->checkToken($user_id);
        if($tokenusr){
            if ($tokenusr->status == 1) {
                $availability_status = ($availability_status) ? 1 : 0;
                $data = array(
                    'availability_status' => $availability_status
                );
                $this->driver_api_model->updateUser('users', $data, 'entity_id', $user_id);
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array(
                        'status' => 1,
                        'availability_status'=>$availability_status,
                        'message' => $this->lang->line('driver_status_updated')
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response([
                        'encryptedResponse'=>$encrypted_data,
                        'isEncryptionActive'=>true
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => 1,
                        'availability_status'=>$availability_status,
                        'message' => $this->lang->line('driver_status_updated')
                    ], REST_Controller::HTTP_OK);
                }
            }
            else
            {
                $adminEmail = $this->driver_api_model->getSystemOption('Admin_Email_Address');
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array(
                        'status' => 1000,
                        'message' => $this->lang->line('login_deactive'),
                        'email'=>$adminEmail->OptionValue
                    );
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response([
                        'encryptedResponse'=>$encrypted_data,
                        'isEncryptionActive'=>true
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => 1000,
                        'message' => $this->lang->line('login_deactive'),
                        'email'=>$adminEmail->OptionValue
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array(
                    'status' => -1,
                    'message' => ''
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response([
                    'encryptedResponse'=>$encrypted_data,
                    'isEncryptionActive'=>true
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function cancel_reject_reason_list_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $reason_type = $decrypted_data->reason_type;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $reason_type = $this->post('reason_type');
        }
        $reason_list = $this->common_model->list_cancel_reject_reasons($reason_type, $language_slug,'Driver');
        if($reason_list){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'reason_list' => $reason_list,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'reason_list' => $reason_list,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                ], REST_Controller::HTTP_OK);
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE)
            {
                $response = array('status' => -1,'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response(['status' => -1,'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code      
            }
        }
    }
}
