<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
class Branch_admin_api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/api_branch_admin_model');               
        $this->load->library('form_validation');
    }
    //common lang fucntion
    public function getLang($language_slug)
    {
        if($language_slug){
            $languages = $this->api_branch_admin_model->getLanguages($language_slug);
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
            $email = $decrypted_data->Email;
            $password = $decrypted_data->Password;
            $firebase_token = $decrypted_data->firebase_token;
        }else{
            $this->getLang($this->post('language_slug'));
            $phone_number = $this->post('PhoneNumber');
            $phone_code = $this->post('phone_code');
            $email = $this->post('Email');
            $password = $this->post('Password');
            $firebase_token = $this->post('firebase_token');
        }
        if($phone_number && $phone_code && $phone_code!='undefined') {
            $login = $this->api_branch_admin_model->getLogin($password, NULL, $phone_number, $phone_code);
        } else if ($email) {
            $login = $this->api_branch_admin_model->getLogin($password, $email, NULL, NULL);
        }
        if(!empty($login)){
            if($login->active == 1){
                if (!empty($firebase_token)) {
                    $data = array('active' => 1,'language_slug'=>$this->current_lang,'device_id' => $firebase_token);
                } else {
                    $data = array('active' => 1,'language_slug'=>$this->current_lang);
                }
                if($login->status == 1)
                {
                    // update device
                    $this->common_model->updateData('users', $data, 'entity_id', $login->entity_id);
                    $login_detail = array(
                        'FirstName' => $login->first_name,
                        'LastName' =>($login->last_name) ? $login->last_name : '',
                        'image' => ($login->image) ? image_url.$login->image : '',
                        'PhoneNumber' => $login->mobile_number,
                        'phone_code' => $login->phone_code,
                        'UserID' => $login->entity_id,
                        'notification' => $login->notification,
                        'Email' => $login->email
                    );
                    
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('login' => $login_detail, 'status' => 1, 'active' => true, 'message' => $this->lang->line('login_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    else
                    {
                        $this->response(['login' => $login_detail, 'status' => 1, 'active' => true, 'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else if ($login->status == 0){
                    $adminEmail = $this->api_branch_admin_model->getSystemOptoin('Admin_Email_Address');
                    
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
            }else{
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'active' => false, 'message' => $this->lang->line('login_deactive'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'active' => false, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                }
            }
        }
        else
        {
            if($phone_number){
                $emailexist = $this->common_model->getSingleRow('users','mobile_number', $phone_number);
            }else{
                $emailexist =  $this->common_model->getSingleRow('users', 'email', $email);
            }
            if($emailexist){
                $message_type = ($phone_number) ? $this->lang->line('invalid_phone_password') : $this->lang->line('invalid_email_password');
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $message_type);
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $message_type], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            } else {
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
        $tokenres = $this->api_branch_admin_model->checkToken($user_id);
        if($tokenres){
            if(md5(SALT.$old_password) == $tokenres->password){
                if($confirm_password == $password){
                    $this->db->set('password', md5(SALT.$password));
                    $this->db->where('entity_id', $user_id);
                    $this->db->update('users');
                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $response = array('status' => 1, 'message' => $this->lang->line('password_change_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 1, 'message' => $this->lang->line('password_change_success')],REST_Controller::HTTP_OK); // OK
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'message' => $this->lang->line('confirm_password'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('confirm_password')],REST_Controller::HTTP_OK); // OK
                    }
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('old_password'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('old_password')], REST_Controller::HTTP_OK); // OK
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
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
        if(!empty($email)) {
            $checkRecord = $this->api_branch_admin_model->checkAdminRecord($email);
            if(!empty($checkRecord))
            {
                if($checkRecord->active == 1){
                    // confirmation link
                    if($email){
                        $verificationCode = random_string('alnum', 20).$checkRecord->entity_id.random_string('alnum', 5);
                        $user_lang_slug = $this->current_lang;
                        $confirmationLink = '<a href="'.base_url().'user/reset/'.$verificationCode.'/'.$user_lang_slug.'" style="text-decoration:underline;">'.$this->lang->line('here').'</a>';                        
                        $email_template = $this->db->get_where('email_template', array('email_slug' => 'forgot-password','language_slug' => $user_lang_slug))->first_row();
                        $arrayData = array('FirstName' => $checkRecord->first_name,'ForgotPasswordLink' => $confirmationLink);
                        $EmailBody = generateEmailBody($email_template->message, $arrayData);
                        
        
                        //get System Option Data
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();
        
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                      
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
                        $this->common_model->updateData('users', $addata, 'entity_id', $checkRecord->entity_id);                        
                    }
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 1,'message' => $this->lang->line('forgot_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 1,'message' => $this->lang->line('forgot_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'message' => $this->lang->line('login_deactive'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('login_deactive')], REST_Controller::HTTP_OK);
                    }
                } 
            }
            else {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('user_not_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('user_not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0, 'message' => $this->lang->line('enter_reg_email'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('enter_reg_email')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    // Edit Profile
    public function editProfile_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $email = $decrypted_data->email;
            $first_name = $decrypted_data->first_name;
            $last_name = $decrypted_data->last_name;
            $notification = $decrypted_data->notification;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $email = $this->post('email');
            $first_name = $this->post('first_name');
            $last_name = $this->post('last_name');
            $notification = $this->post('notification');
        }
        $tokenusr = $this->api_branch_admin_model->checkToken($user_id);
        if($tokenusr){
            $emailExists = $this->api_branch_admin_model->checkEmailExists('users', 'email', $email, $user_id);
            if($emailExists == 0){
                $add_data =array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'notification' => $notification,
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
                $this->common_model->updateData('users',$add_data,'entity_id', $user_id);
                $token = $this->api_branch_admin_model->checkToken($user_id);
                $login_detail = array(
                    'FirstName' => $token->first_name,
                    'LastName' =>($token->last_name) ? $token->last_name : '',
                    'image' => ($token->image) ? image_url.$token->image : '',
                    'Email' => $token->email,
                    'PhoneNumber' => $token->mobile_number,
                    'phone_code' => $token->phone_code,
                    'UserID' => $token->entity_id,
                    'notification' => $token->notification
                );
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('profile' => $login_detail, 'status' => 1, 'message' => $this->lang->line('success_update'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['profile' => $login_detail, 'status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
                }
            }
            else {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('email_exist'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('email_exist')], REST_Controller::HTTP_OK);
                }
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    // Get CMS Pages
    public function getCMSPage_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $cms_slug = $decrypted_data->cms_slug;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $cms_slug = $this->post('cms_slug');
            $language_slug = $this->post('language_slug');
        }
        $cmsData = $this->common_model->getCmsPages($language_slug, $cms_slug);
        if($cmsData && !empty($cmsData))
        {
            foreach ($cmsData as $key => $value) {
                if($value->image!='')
                {
                    $value->image = image_url.$value->image;
                }
                if($value->cms_icon!='')
                {
                    $value->cms_icon = image_url.$value->cms_icon;
                }
            }
        }
        if ($cmsData)
        {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('cmsData'=>$cmsData, 'status' => 1, 'message' => $this->lang->line('found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['cmsData'=>$cmsData, 'status' => 1, 'message' => $this->lang->line('found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0, 'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 0, 'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //order detail
    public function orderListing_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $count = $decrypted_data->count;
            $page_no = $decrypted_data->page_no;
            $tab_type = $decrypted_data->tabType;
            $user_timezone = $decrypted_data->user_timezone;
            $start_date =  $decrypted_data->start_date;
            $end_date =  $decrypted_data->end_date;
            $search_data = $decrypted_data->search_data;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $count = $this->post('count');
            $page_no = $this->post('page_no');
            $tab_type = $this->post('tabType');
            $user_timezone = $this->post('user_timezone');
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');
            $search_data = $this->post('search_data');
        }
        $tokenres = $this->api_branch_admin_model->checkToken($user_id);        
        $count = ($count) ? $count : 10;
        $page_no = ($page_no) ? $page_no : 1;
        if($tokenres)
        {
            $start_date = ($start_date)?$start_date:'';
            $end_date = ($end_date)?$end_date:'';
            $search_data = ($search_data)?$search_data:'';
            $result = $this->api_branch_admin_model->getOrderDetail($user_id, $count, $page_no, $tab_type, $language_slug,$user_timezone,$start_date,$end_date,$search_data);
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('orders' => $result['orders'], 'status' => 1, 'total_order_count' => $result['count'], 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['orders' => $result['orders'], 'status' => 1, 'total_order_count' => $result['count'], 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
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
        $tokenres = $this->common_model->getSingleRow('users', 'entity_id', $user_id);
        if($tokenres){
            $data = array('device_id' => "");
            $this->common_model->updateData('users',$data,'entity_id',$tokenres->entity_id);
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 1,'message' => $this->lang->line('user_logout'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 1,'message' => $this->lang->line('user_logout')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function getOrderHistory_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $language_slug = $decrypted_data->language_slug;
            $order_id_post = $decrypted_data->order_id;
            $restaurant_id = $decrypted_data->restaurant_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $language_slug = $this->post('language_slug');
            $order_id_post = $this->post('order_id');
            $restaurant_id = $this->post('restaurant_id');
        }
        $language_slug = ($language_slug) ? $language_slug : '';
        $tokenres = $this->common_model->getSingleRow('users', 'entity_id', $user_id);
        if($tokenres)
        {
            $drivers = $this->api_branch_admin_model->getDrivers($restaurant_id,$user_id,$tokenres->user_type);
            $order_status = order_status($language_slug);
            unset($order_status['rejected']);
            //New Code add as per requested :: Start :: 13-10-2020
            $order_id = ($order_id_post) ? $order_id_post : '';
            $ordercurrent_status = ''; $order_status_remain = array_merge();
            $order_deliveryval = '';
            if(intval($order_id)>0)
            {
                $ordercurrent_statusArr = $this->api_branch_admin_model->getOrdercurrent_status($order_id);
                $ordercurrent_status = $ordercurrent_statusArr->order_status;
                $order_deliveryval = $ordercurrent_statusArr->order_delivery;
            }
            if($ordercurrent_status=='ready' || strtolower($ordercurrent_status)=='ongoing')
            {
                $ordercurrent_status='orderready'; 
                if($order_deliveryval =='Delivery')
                {
                    $ordercurrent_status='onGoing';
                }
            }
            if($order_deliveryval =='PickUp' || $order_deliveryval == 'DineIn')
            {
                unset($order_status['onGoing']);
                unset($order_status['delivered']);
            } 
            if($order_deliveryval =='Delivery')
            {
                unset($order_status['complete']);
            }
            if($ordercurrent_status!='')
            {
                $cnt=0;
                foreach ($order_status as $key => $value)
                {  
                    if(strtolower($key)=='ongoing')
                    {
                        if($order_deliveryval =='DineIn')
                        {
                           $value = $this->lang->line('food_is_ready');
                        }
                        else if($order_deliveryval =='PickUp')
                        {
                            $value = $this->lang->line('order_ready');
                        }
                        else if($order_deliveryval =='Delivery')
                        {
                            $value = $this->lang->line('onGoing');
                        }    
                    }
                    if(strtolower($key)=='orderready' && $order_deliveryval =='DineIn')
                    {
                        $value = $this->lang->line('served'); 
                    }
                    $order_status[$key] = $value ;
                    if($cnt>0) {
                        if($key == 'orderready' && $order_deliveryval =='Delivery')
                        {                            
                        }elseif($ordercurrent_status == 'delivered' || $ordercurrent_status == 'cancel'){
                            
                        }else{
                            if($order_deliveryval =='PickUp' && strtolower($key)=='ongoing')
                            {
                            } elseif ($order_deliveryval =='DineIn' && $ordercurrent_status=='orderready' && strtolower($key)=='cancel'){
                            }else {
                                $order_status_remain[$key]= $value;    
                            }
                        }
                    }
                    if(strtolower($key)==strtolower($ordercurrent_status))
                    {
                        $cnt++;
                    }
                }
            }            
            //New Code add as per requested :: End :: 13-10-2020
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('drivers' => $drivers, 'order_status' => $order_status,'should_use_star_printer' => true, 'order_status_remain' => $order_status_remain, 'order_id' => $order_id, 'status' => 1, 'message' => $this->lang->line('record_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            } else {
                $this->response(['drivers' => $drivers, 'order_status' => $order_status,'should_use_star_printer' => true, 'order_status_remain' => $order_status_remain, 'order_id' => $order_id, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK);
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function assignDriver_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $driver_id = $decrypted_data->driver_id;
            $order_id = $decrypted_data->order_id;
            $is_reassign = $decrypted_data->is_reassign;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $driver_id = $this->post('driver_id');
            $order_id = $this->post('order_id');
            $is_reassign = $this->post('is_reassign');
        }              
        $tokenres = $this->common_model->getSingleRow('users', 'entity_id',$user_id);
        if($tokenres){
            if(!empty($order_id) && !empty($driver_id)) {
                $checkRecord = $this->common_model->getSingleRowMultipleWhere('order_master', array('entity_id' => $order_id));                
                if(!empty($checkRecord))
                {
                    if ($is_reassign == 1)
                    {
                        $updateDriverId = array('driver_id'=>$driver_id);
                        $assign_newdriver = $this->common_model->updateData('order_driver_map',$updateDriverId,'order_id',$order_id);
                        $this->common_model->updateData('tips',$updateDriverId,'order_id',$order_id);
                        $this->common_model->save_user_log($tokenres->first_name.' '.$tokenres->last_name.' reassigned driver for order - '.$order_id, $user_id);

                        //notification to driver
                        $device = $this->common_model->getSingleRow('users', 'entity_id',$driver_id);
                        if($device->device_id){
                            //get langauge
                            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('order_assigned'),$order_id);
                            $fields['to'] = $device->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                            $fields['notification']['title'] = $this->lang->line('driver_app_name');
                            $fields['data'] = array ('screenType'=>'order');
                           
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
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('status' => 1,'message' => $this->lang->line('driver_reassign_success'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['status' => 1,'message' => $this->lang->line('driver_reassign_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response cod
                        }
                    } else {
                        $assign_details = $this->api_branch_admin_model->checkOrderAssigned($order_id); 
                        if(empty($assign_details)) { 
                            $distance = $this->api_branch_admin_model->getOrderDetails($order_id);
                            $comsn = 0;
                            //check if commission of driver is enabled in system options
                            $this->db->select('OptionValue');
                            $is_enabled = $this->db->get_where('system_option',array('OptionSlug'=>'enable_commission_of_driver'))->first_row();
                            $is_enabled = $is_enabled->OptionValue;
                            if($is_enabled == 1){
                                if($distance[0]->distance > 3){
                                    $comsn = $this->api_branch_admin_model->getSystemOptoin('driver_commission_more');
                                    $comsn = $comsn->OptionValue;
                                }else{
                                    $comsn = $this->api_branch_admin_model->getSystemOptoin('driver_commission_less');
                                    $comsn = $comsn->OptionValue;
                                }
                            } else {
                                $comsn = $distance[0]->delivery_charge;
                            }
                            //Delete order dirver relation before assign
                            $this->api_branch_admin_model->DelOrderbeforAssign($order_id,$driver_id);
                            $order_detail = array(
                                'driver_commission' => $comsn,
                                'commission' => $comsn,
                                'distance' => $distance[0]->distance,
                                'driver_id' => $driver_id,
                                'order_id' => $order_id,
                                'is_accept' => 1
                            );
                            $driver_map_id = $this->common_model->addData('order_driver_map',$order_detail);
                            //driver tip changes :: start
                            $updateDriverId = array('driver_id'=>$driver_id);
                            $this->common_model->updateData('tips',$updateDriverId,'order_id',$order_id);
                            //driver tip changes :: end
                            $this->common_model->save_user_log($tokenres->first_name.' '.$tokenres->last_name.' assigned driver for order - '.$order_id, $user_id);
                            if (!empty($driver_map_id)) {
                                // after assigning a driver need to update the order status
                                if($checkRecord->order_status=="placed" || $checkRecord->order_status=="accepted"){
                                    // $order_status = "preparing";
                                    // $this->db->set('order_status',$order_status)->where('entity_id',$order_id)->update('order_master');
                                    // $addData = array(
                                    //     'order_id' => $order_id,
                                    //     'user_id' => $user_id,
                                    //     'order_status' => $order_status,
                                    //     'time' => date('Y-m-d H:i:s'),
                                    //     'status_created_by' => $tokenres->user_type
                                    // );
                                    // $order_status_id = $this->common_model->addData('order_status',$addData);
                                    // adding notification for website
                                    // $order_status = 'order_preparing';
                                    // if ($order_status != '') {
                                    //     $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                                    //     $notification = array(
                                    //         'order_id' => $order_id,
                                    //         'user_id' => $order_detail->user_id,
                                    //         'notification_slug' => $order_status,
                                    //         'view_status' => 0,
                                    //         'datetime' => date("Y-m-d H:i:s"),
                                    //     );
                                    //     $this->common_model->addData('user_order_notification',$notification);
                                    // }
                                    //notification to user
                                    // $device = $this->common_model->getSingleRow('users', 'entity_id',$order_detail->user_id);
                                    // $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                                    // $this->lang->load('messages_lang', $languages->language_directory);
                                    // $message = $this->lang->line($order_status);
                                    // $device_id = $device->device_id;
                                    // $restaurant = $this->api_branch_admin_model->orderDetails($order_id);
                                    // $this->sendFCMRegistration($device_id,$message,'preparing',$restaurant[0]->restaurant_id,FCM_KEY);
                                }
                                //notification to driver
                                $device = $this->common_model->getSingleRow('users', 'entity_id',$driver_id);
                                if($device->device_id){
                                    //get langauge
                                    $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                                    $this->lang->load('messages_lang', $languages->language_directory);
                                    #prep the bundle
                                    $fields = array();            
                                    $message = sprintf($this->lang->line('order_assigned'),$order_id);
                                    $fields['to'] = $device->device_id; // only one user to send push notification
                                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                                    $fields['notification']['title'] = $this->lang->line('driver_app_name');
                                    $fields['data'] = array ('screenType'=>'order');
                                   
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
                                if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
                                    $this->getLang($decrypted_data->language_slug);
                                }else{
                                    $this->getLang($this->post('language_slug'));   
                                }
                                if($this->post('isEncryptionActive') == TRUE) {
                                    $response = array('status' => 1,'message' => $this->lang->line('driver_assign_success'));
                                    $encrypted_data = $this->common_model->encrypt_data($response);
                                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                                } else {
                                    $this->response(['status' => 1,'message' => $this->lang->line('driver_assign_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response cod
                                }
                            }                  
                        }
                        else
                        {
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 2, 'message' => $this->lang->line('already_accepted_by_driver'), 'assigned_driver_name'=>$assign_details->driver_name);
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 2, 'message' => $this->lang->line('already_accepted_by_driver'), 'assigned_driver_name'=>$assign_details->driver_name], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                            }                                    
                        }
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }                                    
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                      
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function acceptOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $restaurant_id = $decrypted_data->restaurant_id;
            $choose_delivery_method = $decrypted_data->choose_delivery_method;
            $order_mode = $decrypted_data->order_mode;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $restaurant_id = $this->post('restaurant_id');
            $choose_delivery_method = $this->post('choose_delivery_method');
            $order_mode = $this->post('order_mode');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->common_model->getSingleRow('users', 'entity_id',$user_id);
        if($tokenres)
        {
            $restaurant_id = ($restaurant_id != '') ? $restaurant_id : '';
            $order_id = ($order_id != '') ? $order_id : '';
            if( !empty($restaurant_id) && !empty($order_id))
            {
                // Start - check the order status already changed
                $userorderdetail = $this->api_branch_admin_model->getRecord('order_master','entity_id',$order_id);
                if($userorderdetail->order_status == 'delivered' || $userorderdetail->order_status == 'cancel' || $userorderdetail->order_status == 'rejected' || $userorderdetail->order_status == 'complete' || $userorderdetail->order_status == 'accepted' || $userorderdetail->order_status == 'onGoing' || $userorderdetail->order_status == 'pending' || $userorderdetail->order_status == 'ready') {
                    $status_changed_message  = sprintf($this->lang->line("order_status_already_changed_to"),$this->lang->line($userorderdetail->order_status));
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 0,'message' => $status_changed_message);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 0,'message' => $status_changed_message], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    exit;
                }
                // End - check the order status already changed
                //if(strtolower(trim($order_mode)) == 'pickup'){
                    $delivery_method_resp_arr = $this->api_branch_admin_model->UpdatedStatus('order_master',$restaurant_id,$order_id);
                // } else {
                //     $delivery_method_resp_arr = $this->api_branch_admin_model->UpdatedStatusForDeliveryOrders('order_master',$restaurant_id,$order_id,$choose_delivery_method,$user_timezone);
                // }
                if($delivery_method_resp_arr['is_available'] == 'yes') {
                    // adding order status
                    $addData = array(
                        'order_id'=>$order_id,
                        'user_id' => $user_id,
                        'order_status'=>'accepted_by_restaurant',
                        'time'=>date('Y-m-d H:i:s'),
                        'status_created_by'=>$tokenres->user_type
                    );
                    $status_id = $this->common_model->addData('order_status',$addData);
                    // adding notification for website
                    $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                    if($order_detail->user_id && $order_detail->user_id > 0) {
                        $notification = array(
                            'order_id' => $order_id,
                            'user_id' => $order_detail->user_id,
                            'notification_slug' => 'order_accepted',
                            'view_status' => 0,
                            'datetime' => date("Y-m-d H:i:s"),
                        );
                        $this->common_model->addData('user_order_notification',$notification);
                    }
                    if($order_detail->agent_id){
                        $this->common_model->notificationToAgent($order_id, 'accepted');
                    }

                    if($this->post('isEncryptionActive') == TRUE)
                    {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 1,'message' => $this->lang->line('order_accepted_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
                    }
                    else
                    {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 1,'message' => $this->lang->line('order_accepted_success')], REST_Controller::HTTP_OK);
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array(
                            'status' => 0,
                            'message' => ($delivery_method_resp_arr['error'])?$this->lang->line('thirdparty_api_errors'): $this->lang->line('delivery_not_available_via_thirdparty')
                        );
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response([
                            'status' => 0,
                            'message' => ($delivery_method_resp_arr['error'])?$this->lang->line('thirdparty_api_errors'): $this->lang->line('delivery_not_available_via_thirdparty')
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function rejectOrder_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $restaurant_id = $decrypted_data->restaurant_id;
            $reject_reason = $decrypted_data->reject_reason;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $restaurant_id = $this->post('restaurant_id');
            $reject_reason = $this->post('reject_reason');
        }
        $tokenres = $this->common_model->getSingleRow('users', 'entity_id', $user_id);
        if($tokenres){
            $response = array('error'=>'');
            $restaurant_id = ($restaurant_id != '') ? $restaurant_id : '';
            $order_id = ($order_id != '') ? $order_id : '';
            if(!empty($user_id) && !empty($restaurant_id) && !empty($order_id)){
                $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                // Start - check the order status already changed
                if($order_detail->order_status == 'delivered' || $order_detail->order_status == 'cancel' || $order_detail->order_status == 'rejected' || $order_detail->order_status == 'complete' || $order_detail->order_status == 'accepted' || $order_detail->order_status == 'onGoing' || $order_detail->order_status == 'pending' || $order_detail->order_status == 'ready') {
                    $status_changed_message  = sprintf($this->lang->line("order_status_already_changed_to"),$this->lang->line($order_detail->order_status));
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 0,'message' => $status_changed_message);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 0,'message' => $status_changed_message], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                    exit;
                }
                // End - check the order status already changed
                $order_user_id = $order_detail->user_id;
                //stripe refund amount
                $payment_methodarr = array('stripe','paypal','applepay');
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

                        if(in_array(strtolower($data['order_records']->payment_option), $payment_methodarr))
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
                }                
                $reject_array = array(
                    'order_status' => 'rejected',
                    'reject_reason' =>($reject_reason) ? $reject_reason : '',
                );
                $this->db->set($reject_array)->where('entity_id',$order_id)->update('order_master');
                /*wallet changes start*/
                //if order is cancelled both debit and credit should be removed from wallet history
                $users_wallet = $this->api_branch_admin_model->getUsersWalletMoney($order_user_id);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $credit_walletDetails = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $order_user_id, 'credit'=>1, 'is_deleted'=>0));
                $credit_amount = $credit_walletDetails->amount;
                $debit_walletDetails = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $order_user_id, 'debit'=>1, 'is_deleted'=>0));
                $debit_amount = $debit_walletDetails->amount;
                $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                //delete order_id from wallet history and update users wallet
                if(!empty($credit_amount) || !empty($debit_amount)){
                    $this->api_branch_admin_model->deletewallethistory($order_id); // delete by order id
                    $new_wallet = array(
                        'wallet'=>$new_wallet_amount
                    );
                    $this->api_branch_admin_model->updateMultipleWhere('users', array('entity_id'=>$order_user_id), $new_wallet);
                }
                /*wallet changes end*/
                $addData = array(
                    'order_id'=>$order_id,
                    'user_id' => $user_id,
                    'order_status'=>'rejected',
                    'time'=>date('Y-m-d H:i:s'),
                    'status_created_by'=>$tokenres->user_type
                );
                $this->common_model->addData('order_status',$addData);
                // send website notification
                if($order_user_id && $order_user_id > 0) {
                    $notification = array(
                        'order_id' => $order_id,
                        'user_id' => $order_user_id,
                        'notification_slug' => 'order_rejected',
                        'view_status' => 0,
                        'datetime' => date("Y-m-d H:i:s"),
                    );
                    $this->common_model->addData('user_order_notification',$notification);
                }
                $userdata = $this->common_model->getSingleRow('users','entity_id',$order_user_id);
                if(!empty($reject_reason)){
                    $message = sprintf($this->lang->line('push_order_rejected'),$order_id).'-'.$reject_reason;
                }else{
                    $message = sprintf($this->lang->line('push_order_rejected'),$order_id);
                }
                if($order_detail->agent_id){
                    $this->common_model->notificationToAgent($order_id, 'rejected');
                }
                $device_id = $userdata->device_id;
                // Send Latest wallet balance
                $users_wallet = $this->api_branch_admin_model->getUsersWalletMoney($order_user_id);
                $latest_wallet_balance = $users_wallet->wallet;
                //send app notification
                $this->sendFCMRegistration($device_id,$message,'rejected',$restaurant_id,FCM_KEY,$order_id,'',$latest_wallet_balance);
                //send refund noti to user
                if($data['order_records']->user_id && $data['order_records']->user_id > 0) {
                    if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                        $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                    }
                }
                if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0,'message' => $this->lang->line('admin_refund_failed'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_failed')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0,'message' => $this->lang->line('admin_refund_canceled'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_canceled')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0,'message' => $this->lang->line('refund_pending_err_msg'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0,'message' => $this->lang->line('refund_pending_err_msg')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 1,'message' => $this->lang->line('order_rejected_success'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 1,'message' => $this->lang->line('order_rejected_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            }
            else
            {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                }                       
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }
    public function updateOrderStatus_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $order_id = $decrypted_data->order_id;
            $order_status_post = $decrypted_data->order_status;
            $cancel_reason = $decrypted_data->cancel_reason;
            $user_timezone = $decrypted_data->user_timezone;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $order_id = $this->post('order_id');
            $order_status_post = $this->post('order_status');
            $cancel_reason = $this->post('cancel_reason');
            $user_timezone = $this->post('user_timezone');
            $language_slug = $this->post('language_slug');
        }
        /*$currentDateTime = date("Y-m-d g:i A");
        $currentDateTime = $this->common_model->getZonebaseDate($currentDateTime,$user_timezone);*/      

        $tokenres = $this->common_model->getSingleRow('users', 'entity_id', $user_id);
        if($tokenres)
        {
            $order_id = ($order_id) ? $order_id : ''; 
            $order_status = ($order_status_post) ? $order_status_post : ''; 
            $user_id = ($user_id) ? $user_id : '';
            $driver_id = '';
            if(!empty($order_id) && !empty($order_status_post))
            {
                // Start - check the order status already changed
                $userorderdetail = $this->api_branch_admin_model->getRecord('order_master','entity_id',$order_id);
                if($userorderdetail->order_status == 'delivered' || $userorderdetail->order_status == 'cancel' || $userorderdetail->order_status == 'rejected' || $userorderdetail->order_status == 'complete'){
                    $arrResponse['status'] = "order_status_already_changed";
                    $status_changed_message  = sprintf($this->lang->line("order_status_already_changed_to"),$this->lang->line($userorderdetail->order_status));
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 0,'message' => $status_changed_message);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 0,'message' => $status_changed_message], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
                // End - check the order status already changed
                $userorderdetail = $this->api_branch_admin_model->getRecord('order_master','entity_id',$order_id);
                $stripe_response = array('error'=>'');
                //stripe refund amount
                $payment_methodarr = array('stripe','paypal','applepay');
                $data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
                if($order_status_post == 'cancel') {
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
                                $stripe_response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                            }
                            else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                            {   
                                $stripe_response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                            }

                            //Mail send code Start
                            if(!empty($stripe_response) && ($stripe_response['paymentIntentstatus'] || $stripe_response['tips_paymentIntentstatus']))
                            {
                                $updated_bytxt = $tokenres->first_name.' '.$tokenres->last_name;
                                $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
                            }
                            //Mail send code End

                            if(in_array(strtolower($data['order_records']->payment_option), $payment_methodarr))
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
                    }
                }
                //Code for update the order status withe selected and remain status :: Start
                $status_created_by = $tokenres->user_type;
                $order_typevalarr = $this->db->select('order_delivery,order_status')->get_where('order_master',array('entity_id'=>$order_id))->first_row();
                $order_typeval = $order_typevalarr->order_delivery;
                $order_statusval = $order_typevalarr->order_status;

                $cnt=0;
                $order_status_remain = array();
                //Code for Dinein Order :: Start
                if($order_typeval=='DineIn')
                {
                    $order_statusarr = dinein_order_status($language_slug);
                    unset($order_statusarr['placed']);
                    unset($order_statusarr['cancel']);
                    unset($order_statusarr['rejected']);
                    foreach ($order_statusarr as $key => $value)
                    {  
                        if($cnt>0)
                        {
                            if(($key == 'orderready' && $order_typeval =='Delivery') || ($key == 'orderready' && $order_typeval =='DineIn'))
                            {                            
                            }else if($order_typeval =='PickUp' && strtolower($key)=='ongoing')
                            {
                            }
                            else {
                                $order_status_remain[$key]= $value;    
                            }
                            
                        }
                        if(strtolower($key)==strtolower($order_statusval))
                        {
                            $cnt++;
                        }
                    }

                    if(!empty($order_status_remain) && count($order_status_remain)>0)
                    {
                        foreach($order_status_remain as $key => $value) 
                        {
                            $ord_stat = ($key=='accepted')?'accepted_by_restaurant':(($key == 'orderready')?'ready':$key);
                            $ord_statchk = $ord_stat;
                            if(strtolower($order_status_post) == strtolower($ord_statchk)) break;
                                                     
                            $addData = array(
                                'order_id'=>$order_id,
                                'user_id' => $user_id,
                                'order_status'=>$ord_stat,
                                'time'=>date('Y-m-d H:i:s'),
                                'status_created_by'=>$status_created_by
                            );
                            $status_ids = $this->common_model->addData('order_status',$addData);                                          
                        }
                    }
                }//Code for Dinein Order :: End :: //Code for Normal Order :: Start
                else
                { 
                    $order_statusarr = order_status($language_slug);
                    unset($order_statusarr['placed']);
                    unset($order_statusarr['cancel']);
                    unset($order_statusarr['rejected']);
                    if($order_typeval == 'PickUp')
                    {
                        unset($order_statusarr['onGoing']);
                        unset($order_statusarr['delivered']);
                    }
                    else
                    {
                        unset($order_statusarr['orderready']);
                    }
                    
                    foreach ($order_statusarr as $key => $value)
                    {  
                        if($cnt>0)
                        {
                            if(($key == 'orderready' && $order_typeval =='Delivery'))
                            {                            
                            }else if($order_typeval =='PickUp' && strtolower($key)=='ongoing')
                            {
                            }
                            else{
                                $order_status_remain[$key]= $value;    
                            }
                        }
                        if(strtolower($key)==strtolower($order_statusval))
                        {
                            $cnt++;
                        }
                    }
                    if(!empty($order_status_remain) && count($order_status_remain)>0)
                    {
                        foreach($order_status_remain as $key => $value) 
                        {
                            $ord_stat = ($key=='accepted')?'accepted_by_restaurant':(($key == 'orderready')?'ready':$key);
                            $ord_statchk = $ord_stat;
                            if($order_typeval =='PickUp')
                            {
                               if($ord_stat=='ready' && strtolower($order_status_post)=='ongoing') 
                               {
                                   $ord_statchk = 'ongoing';
                               } 
                               if($ord_stat=='ready' && strtolower($order_status_post)=='orderready') 
                               {
                                   $ord_statchk = 'orderready';
                               }                            
                            }
                            
                            if(strtolower($order_status_post) == strtolower($ord_statchk)) break;

                            $addData = array(
                                'order_id'=>$order_id,
                                'user_id' => $user_id,
                                'order_status'=>$ord_stat,
                                'time'=>date('Y-m-d H:i:s'),
                                'status_created_by'=>$status_created_by
                            );
                            $status_ids = $this->common_model->addData('order_status',$addData);                                          
                        }
                    }
                }//Code for Normal Order :: End
                //Code for update the order status withe selected and remain status :: End

                if($order_status_post == 'cancel'){
                    $cancel_array = array(
                        'order_status' => 'cancel',
                        'cancel_reason' => ($cancel_reason) ? $cancel_reason : '',
                    );
                    $this->db->set($cancel_array)->where('entity_id', $order_id)->update('order_master');
                }else
                {
                    $order_status_post = ($order_status_post=='orderready')?'ready':$order_status_post;
                    $this->db->set('order_status', $order_status_post)->where('entity_id', $order_id)->update('order_master');
                }
                if(strtolower($order_status_post) == 'accepted'){
                    $this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
                    $this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');
                }
                /*refer and earn changes start*/
                //credit amount to referer in first transaction (if referral code used while signup)
                //get user detail who placed the order
                $userorderdetail = $this->api_branch_admin_model->getRecord('order_master','entity_id',$order_id);
                $order_user_id = $userorderdetail->user_id;
                $getreferraldetail = $this->api_branch_admin_model->getRecord('users','entity_id',$order_user_id);
                if(strtolower($order_status_post) == 'delivered' || strtolower($order_status_post) == 'complete'){
                    if(!empty($getreferraldetail->referral_code_used)) {
                        //checking in wallet if there is any entry of order user as referee.
                        $check_wallet_history = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('referee_id' => $order_user_id, 'is_deleted'=>0));
                        if(empty($check_wallet_history)){
                            //get referer details
                            $getUser = $this->api_branch_admin_model->getRecord('users','referral_code',$getreferraldetail->referral_code_used);
                            //add wallet money(credited) in users table
                            $wallet = $getUser->wallet;
                            $referral_amount = $this->api_branch_admin_model->getSystemOption('referral_amount');
                            $ref_credit_amount = ($userorderdetail->subtotal * $referral_amount->OptionValue)/100;
                            $addWallet_amount = array(
                                'wallet'=>$wallet+$ref_credit_amount
                            );
                            $this->api_branch_admin_model->updateMultipleWhere('users', array('entity_id'=>$getUser->entity_id,'referral_code'=>$getUser->referral_code), $addWallet_amount);
                            
                            //add wallet history - amount credited
                            $updateWalletHistory = array(
                                'user_id'=>$getUser->entity_id, //referrer
                                'referee_id'=>$order_user_id, //referee (order user id)
                                'amount'=>$ref_credit_amount,
                                'credit'=>1,
                                'reason'=>'referral_bonus',
                                'created_date' => date('Y-m-d H:i:s')
                            );
                            $this->common_model->addData('wallet_history',$updateWalletHistory);
                        }
                    }
                }
                /*refer and earn changes end*/
                /*wallet changes start*/
                //if order is cancelled both debit and credit should be removed from wallet history
                if($order_status_post == 'cancel') {
                    $users_wallet = $this->api_branch_admin_model->getUsersWalletMoney($order_user_id);
                    $current_wallet = $users_wallet->wallet; //money in wallet
                    $credit_walletDetails = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $order_user_id, 'credit'=>1, 'is_deleted'=>0));
                    $credit_amount = $credit_walletDetails->amount;
                    $debit_walletDetails = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $order_user_id, 'debit'=>1, 'is_deleted'=>0));
                    $debit_amount = $debit_walletDetails->amount;
                    $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                    $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                    //delete order_id from wallet history and update users wallet
                    if(!empty($credit_amount) || !empty($debit_amount)){
                        $this->api_branch_admin_model->deletewallethistory($order_id); // delete by order id
                        $new_wallet = array(
                            'wallet'=>$new_wallet_amount
                        );
                        $this->api_branch_admin_model->updateMultipleWhere('users', array('entity_id'=>$order_user_id), $new_wallet);
                    }
                }
                /*wallet changes end*/
                if(strtolower(trim($order_status_post)) == 'ongoing' && $order_typeval == 'PickUp') {
                    $statval = 'ready';
                } else {
                    $statval = $order_status_post;
                }
                $addData = array(
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'order_status' => $statval,
                    'time' => date('Y-m-d H:i:s'),
                    'status_created_by' => $status_created_by
                );
                $order_status_id = $this->common_model->addData('order_status',$addData);
                // adding notification for website
                $order_status = '';
                if ($order_status_post == "complete") {
                    $this->common_model->deleteData('user_order_notification','order_id',$order_id);
                    if($userorderdetail->order_delivery =='DineIn' || $userorderdetail->order_delivery =='PickUp')
                    {
                        $order_status = 'order_completed';
                    }
                }
                // else if ($order_status_post == "preparing") {
                //     $order_status = 'order_preparing';
                // }
                else if ($order_status_post == "onGoing" || $order_status_post == "ready")
                {
                    $order_status = 'order_ongoing';
                }
                else if ($order_status_post == "delivered") {
                    $order_status = 'order_delivered';
                    //Code for find the drvier id :: Start
                    $driver_detail = $this->api_branch_admin_model->getAssignDrvier($order_id);
                    if($driver_detail){ 
                        $driver_id = $driver_detail->driver_id; 
                    }
                    //Code for find the drvier id :: End
                }
                else if ($order_status_post == "cancel") {
                    $order_status = 'order_canceled';
                }
                if($order_status=='')
                {
                    $order_status = $order_status_post;
                }                               
                if ($order_status != '')
                {
                    $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                    if($order_detail->user_id && $order_detail->user_id > 0) {
                        $notification = array(
                            'order_id' => $order_id,
                            'user_id' => $order_detail->user_id,
                            'notification_slug' => $order_status,
                            'view_status' => 0,
                            'datetime' => date("Y-m-d H:i:s"),
                        );
                        $this->common_model->addData('user_order_notification',$notification);
                    }
                }
                if($userorderdetail->agent_id){
                    $this->common_model->notificationToAgent($order_id, $order_status_post);
                }
                $userdata = $this->common_model->getSingleRow('users','entity_id',$order_user_id);
                //get langauge
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$userdata->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                $message = sprintf($this->lang->line($order_status),$order_id);
                if(strtolower($order_detail->order_delivery)=='pickup' && $order_status == 'order_ongoing')
                {
                    $message = sprintf($this->lang->line('order_is_readynoti'),$order_id);
                }
                //New code add as per set notification bae on order delivery :: Start :: 22-10-2020
                $order_statusArr = $this->api_branch_admin_model->getOrdercurrent_status($order_id); 
                $order_deliveryval = $order_statusArr->order_delivery;
                if ($order_status == 'order_canceled') {
                    if(!empty($cancel_reason)){
                        $message = sprintf($this->lang->line('order_canceled'),$order_id).'-'.$cancel_reason;
                    }else{
                        $message = sprintf($this->lang->line('order_canceled'),$order_id);
                    }
                }
                if(strtolower($order_status)=='order_ongoing' || strtolower($order_status)=='ready' || strtolower($order_status)=='orderready')
                {
                    if($order_deliveryval =='DineIn')
                    {
                       $message = sprintf($this->lang->line('food_is_ready_notification'),$order_id);
                    }
                    else if($order_deliveryval =='PickUp')
                    {
                        $message = sprintf($this->lang->line('order_ready_notification'),$order_id);
                    }
                    else if($order_deliveryval =='Delivery')
                    {
                        $message = sprintf($this->lang->line('on_going_notification'),$order_id);
                    }
                } 
                else if($order_status_post == "complete")
                {
                    $message = sprintf($this->lang->line('order_completed'),$order_id);
                }               
                //New code add as per set notification bae on order delivery :: Start :: 22-10-2020
                $device_id = $getreferraldetail->device_id;
                $restaurant = $this->api_branch_admin_model->orderDetails($order_id);
                // Send Latest wallet balance
                if ($order_status_post == "cancel") {
                    $users_wallet = $this->api_branch_admin_model->getUsersWalletMoney($order_user_id);
                    $latest_wallet_balance = $users_wallet->wallet;
                }
                $this->sendFCMRegistration($device_id, $message, $order_status_post, $restaurant[0]->restaurant_id,FCM_KEY,$order_id,$driver_id,$latest_wallet_balance);

                //send email and sms notification to user on order cancel
                if($order_status_post == 'cancel'){
                    $langslugval = ($userdata->language_slug) ? $userdata->language_slug : '';
                    $useridval = ($userdata->entity_id && $userdata->entity_id > 0) ? $userdata->entity_id : 0;
                    $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$order_id,'Admin','from_admin');
                }
                //send refund noti to user
                if($data['order_records']->user_id && $data['order_records']->user_id > 0) {
                    if(!empty($stripe_response) && ($stripe_response['paymentIntentstatus'] || $stripe_response['tips_paymentIntentstatus'])) {
                        $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$stripe_response['paymentIntentstatus'],$stripe_response['tips_paymentIntentstatus'],$stripe_response['error']);
                    }
                }
                if($stripe_response['paymentIntentstatus']=='failed' || $stripe_response['tips_paymentIntentstatus']=='failed'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 0,'message' => $this->lang->line('admin_refund_failed'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_failed')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }else if($stripe_response['paymentIntentstatus']=='canceled' || $stripe_response['tips_paymentIntentstatus']=='canceled'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 0,'message' => $this->lang->line('admin_refund_canceled'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_canceled')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }else if($stripe_response['paymentIntentstatus']=='pending' || $stripe_response['tips_paymentIntentstatus']=='pending'){
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0,'message' => $this->lang->line('refund_pending_err_msg'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0,'message' => $this->lang->line('refund_pending_err_msg')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                } else {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $this->getLang($decrypted_data->language_slug);
                        $response = array('status' => 1,'message' => $this->lang->line('order_status_changed'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->getLang($this->post('language_slug'));
                        $this->response(['status' => 1,'message' => $this->lang->line('order_status_changed')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }else{
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                }                    
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }
    // Send notification
    function sendFCMRegistration($registrationIds,$message,$order_status,$restaurant_id,$key=FCM_KEY,$order_id='',$driver_id='',$wallet_amount='')
    { 
        if($registrationIds)
        {
            $fields = array();            
            $fields['to'] = $registrationIds; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['notification']['title'] = $this->lang->line('customer_app_name');
            if ($order_status == "delivered" || $order_status == "complete") {
                $fields['data'] = array ('screenType'=>'delivery','restaurant_id'=>$restaurant_id,'order_id'=>$order_id,'driver_id'=>$driver_id,'wallet_amount'=>$wallet_amount);
            }
            else
            {
                $fields['data'] = array ('screenType'=>'order','wallet_amount'=>$wallet_amount);
            }
            $headers = array (
                'Authorization: key=' . $key,
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
        $tokenres = $this->api_branch_admin_model->checkToken($user_id); 
        if($tokenres){
            $data = array('language_slug' => $language_slug);
            $this->common_model->updateData('users', $data, 'entity_id', $user_id);
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
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
        $tokenres = $this->api_branch_admin_model->checkToken($user_id); 
        if($tokenres && !empty($user_id)){
            $data = array('device_id' => $firebase_token);
            $this->common_model->updateData('users', $data, 'entity_id', $user_id);  
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }
    public function pay_order_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $language_slug = $decrypted_data->language_slug;
            $order_id = $decrypted_data->order_id;
            $payment_method = $decrypted_data->payment_method;
            $transaction_id = $decrypted_data->transaction_id;

        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $language_slug = $this->post('language_slug');
            $order_id = $this->post('order_id');
            $payment_method = $this->post('payment_method');
            $transaction_id = $this->post('transaction_id');
        }
        $tokenres = $this->api_branch_admin_model->checkToken($user_id); 

        if($tokenres)
        {
            $data = array(
                 'entity_id' => $order_id,
                    'admin_payment_option' => $payment_method,
                    'transaction_id' => $transaction_id ? $transaction_id : '',
                    'paid_status' => 'paid',
                    //'order_status' => 'complete'
            );
            $this->common_model->updateData('order_master', $data, 'entity_id', $order_id);

            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 1,'message' => $this->lang->line('success_update'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 1,'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }
    //New code for print order recipt :: Start
    public function printOrderReceipt_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $user_id = $decrypted_data->user_id;
            $language_slug = $decrypted_data->language_slug;
            $order_id = ($decrypted_data->order_id)?$decrypted_data->order_id:'';
            $phone_number = $decrypted_data->phone_number;
            $user_timezone = $decrypted_data->user_timezone;
        }else{
            $this->getLang($this->post('language_slug'));
            $user_id = $this->post('user_id');
            $language_slug = $this->post('language_slug');
            $order_id = ($this->post('order_id'))?$this->post('order_id'):'';
            $phone_number = $this->post('phone_number');
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_branch_admin_model->checkToken($user_id); 
        if($tokenres && $order_id!='') {
            //Code for fetch data to generate pdf :: Start
            $data['order_records'] = $this->api_branch_admin_model->getorderPrintDetail($order_id);
            $data['menu_item'] = $this->api_branch_admin_model->getInvoiceMenuItem($order_id);
            $data['wallet_history'] = $this->api_branch_admin_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'debit' => 1, 'is_deleted'=>0));
            $data['user_timezone'] = $user_timezone;
            //Code for fetch data to generate pdf :: End

            //Code for generate pdf file :: Start
            ini_set('memory_limit', '256M');
            $this->load->library('M_pdf');

            $mpdf = new \Mpdf\Mpdf(['format' => [72, 250]]); //default printer paper height width
            if($data['order_records']->is_printer_available == 1 && !is_null($data['order_records']->printer_paper_width) && $data['order_records']->printer_paper_width > 0 && !is_null($data['order_records']->printer_paper_height) && $data['order_records']->printer_paper_height > 0)
            {
                 $mpdf = new \Mpdf\Mpdf(['format' => array($data['order_records']->printer_paper_width,$data['order_records']->printer_paper_height) ]);
            }
            $mpdf->allow_charset_conversion=true; // Set by default to TRUE
            /*$mpdf->charset_in = 'UTF-8';*/
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $html = $this->load->view('backoffice/order_receipt',$data,true);

            if (!@is_dir('uploads/order_receipt')) {
                @mkdir('./uploads/order_receipt', 0777, TRUE);
            }            
            $mpdf->WriteHTML($html);
            $output = 'uploads/order_receipt/'.$order_id.'.pdf';
            $mpdf->output($output,'F');            
            $order_receipt_link = base_url().$output;
            //Code for generate pdf file :: End
            //pdf to image :: start
            $imagick = new Imagick();
            $pdfpath = FCPATH.'uploads/order_receipt/'.$order_id.'.pdf';
            //Sets the image resolution
            $imagick->setResolution(150,150);
            //Reads image from PDF
            $imagick->readImage($pdfpath);
            //Merges a sequence of images
            $imagick = $imagick->flattenImages();
            $jpgfilename = FCPATH.'uploads/order_receipt/img_'.$order_id.'.jpg';
            //Writes an image
            $imagick->writeImages($jpgfilename, false);
            $receipt_img_path = base_url().'uploads/order_receipt/img_'.$order_id.'.jpg';
            //pdf to image :: end

            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 1,'message' => $this->lang->line('success'),'order_receipt_link' => $order_receipt_link, 'receipt_img_path' => $receipt_img_path);
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 1,'message' => $this->lang->line('success'),'order_receipt_link' => $order_receipt_link, 'receipt_img_path' => $receipt_img_path], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }else{
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }
        }
    }
    //New code for print order recipt :: End
    //New code for stripe refund :: Start
    public function initiateRefund_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $order_id = ($decrypted_data->order_id)?$decrypted_data->order_id:'';
            $user_id = $decrypted_data->user_id;
            $refund_reason = $decrypted_data->refund_reason;
            $partial_refundedchk = $decrypted_data->partial_refundedchk;
            $partial_refundedamt = $decrypted_data->partial_refundedamt;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $order_id = ($this->post('order_id'))?$this->post('order_id'):'';
            $user_id = $this->post('user_id');
            $refund_reason = $this->post('refund_reason');
            $partial_refundedchk = $this->post('partial_refundedchk');
            $partial_refundedamt = $this->post('partial_refundedamt');
        }

        //Code for add the amount for refund :: Start
        $refund_full_partial = ($partial_refundedchk)?$partial_refundedchk:'full';
        $partial_refundedamt = ($partial_refundedamt)?$partial_refundedamt:0;
        if($partial_refundedamt==0)
        {
            $refund_full_partial = 'full';
        }
        //Code for add the amount for refund :: End

        if(!empty($order_id)){
            //stripe refund amount
            $data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
            $payment_methodarr = array('stripe','paypal','applepay');
            if($data['order_records']->refund_status != 'refunded'){
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
                            $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option, $refund_reason,$refund_full_partial,$partial_refundedamt);
                        }
                        else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                        {
                            $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option, $refund_reason,$refund_full_partial,$partial_refundedamt);
                        }

                        //Mail send code Start
                        if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                        {
                            $tokenrestest = $this->common_model->getSingleRow('users', 'entity_id',$user_id);
                            $updated_bytxt = $tokenrestest->first_name.' '.$tokenrestest->last_name;
                            $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,$partial_refundedamt,$refund_full_partial,$updated_bytxt,$language_slug);
                        }
                        //Mail send code End

                        if(in_array(strtolower($data['order_records']->payment_option), $payment_methodarr))
                        {
                            //Code for save updated by and date value 
                            $update_array = array(
                                'updated_by' => $user_id,
                                'updated_date' => date('Y-m-d H:i:s')
                            );
                            $this->db->set($update_array)->where('entity_id',$order_id)->update('order_master');
                            //Code for save updated by and date value
                        }

                        //send refund noti to user
                        if($data['order_records']->user_id && $data['order_records']->user_id > 0)
                        {
                            if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                            {
                                $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                                if($user_id) {
                                    $tokenres = $this->common_model->getSingleRow('users', 'entity_id',$user_id);
                                    if($tokenres) {
                                        $resname = $this->common_model->getResNameWithOrderId($order_id);
                                        $this->common_model->save_user_log($tokenres->first_name.' '.$tokenres->last_name.' initiated refund for an order - '.$order_id.' (ordered from: '.$resname.')', $user_id);
                                    }
                                }

                                //Code for when full order refund that time order stauts set cancel :: Start
                                if($refund_full_partial=='full' || $response['refundreturn_status']=='refunded')
                                {
                                    $this->db->set('order_status','cancel')->where('entity_id',$order_id)->update('order_master');
                                    $addData = array(
                                        'order_id'=>$order_id,
                                        'order_status'=>'cancel',
                                        'time'=>date('Y-m-d H:i:s'),
                                        'status_created_by'=>$tokenres->user_type
                                    );
                                    $orderstatustbl_id = $this->common_model->addData('order_status',$addData);

                                    if($user_id && $user_id > 0) {                                        
                                        //wallet changes :: start
                                        //if order is cancelled both debit and credit should be removed from wallet history
                                        $users_wallet = $this->common_model->getUsersWalletMoney($user_id);
                                        $current_wallet = $users_wallet->wallet; //money in wallet
                                        $credit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                                        $credit_amount = $credit_walletDetails->amount;
                                        $debit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                                        $debit_amount = $debit_walletDetails->amount;
                                        $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                                        $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                                        //delete order_id from wallet history and update users wallet
                                        if(!empty($credit_amount) || !empty($debit_amount)){
                                            $this->common_model->deletewallethistory($order_id); // delete by order id
                                            $new_wallet = array(
                                                'wallet'=>$new_wallet_amount
                                            );
                                            $this->common_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                                        }
                                        //wallet changes :: end
                                    }
                                }
                                //Code for when full order refund that time order stauts set cancel :: End
                            }
                        }
                        if($response['error'] == '' && ($response['paymentIntentstatus'] == 'refunded' || $response['paymentIntentstatus'] == 'partial refunded' || $response['tips_paymentIntentstatus'] == 'refunded')) {
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 1,'message' => $this->lang->line('refund_initiated_cus'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 1,'message' => $this->lang->line('refund_initiated_cus')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        } else if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 0,'message' => $this->lang->line('admin_refund_failed'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_failed')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        } else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 0,'message' => $this->lang->line('admin_refund_canceled'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 0,'message' => $this->lang->line('admin_refund_canceled')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 0,'message' => $this->lang->line('refund_pending_err_msg'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 0,'message' => $this->lang->line('refund_pending_err_msg')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        } else {
                            if($this->post('isEncryptionActive') == TRUE) {
                                $response = array('status' => 0,'message' => $this->lang->line('stripe_refund_error'));
                                $encrypted_data = $this->common_model->encrypt_data($response);
                                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            } else {
                                $this->response(['status' => 0,'message' => $this->lang->line('stripe_refund_error')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                            }
                        }  
                    }else{
                        if($this->post('isEncryptionActive') == TRUE) {
                            $response = array('status' => 0, 'message' => $this->lang->line('wrong_payment_method'));
                            $encrypted_data = $this->common_model->encrypt_data($response);
                            $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        } else {
                            $this->response(['status' => 0, 'message' => $this->lang->line('wrong_payment_method')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                        
                        }
                    }
                }else{
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'message' => $this->lang->line('refund_already_initiated'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('refund_already_initiated')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                    
                    }
                }
            }else{
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('already_refunded'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('already_refunded')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
                
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code 
            }                       
        }
    }
    //New code for stripe refund :: END
    //edit order api
    public function edit_order_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $updated_items = $decrypted_data->updated_items;
            $order_id = ($decrypted_data->order_id) ? $decrypted_data->order_id : '';
            $user_id = $decrypted_data->user_id;
            $partial_refund_reason = $decrypted_data->partial_refund_reason;
        } else {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $updated_items = $this->post('updated_items');
            $order_id = ($this->post('order_id')) ? $this->post('order_id') : '';
            $user_id = $this->post('user_id');
            $partial_refund_reason = $this->post('partial_refund_reason');
        }        

        $tokenres = $this->common_model->getSingleRow('users', 'entity_id',$user_id);
        $order_detailarr = $this->api_branch_admin_model->get_delivery_pickup_order($order_id);
        $del_itemTotal = 0; $item_detailup = array(); $Is_itemupdate = 'no';
        if($order_detailarr && !empty($order_detailarr)) {
            $subtotal = $order_detailarr['subtotal'];
            $tax_rate = $order_detailarr['tax_rate'];
            $tax_type = $order_detailarr['tax_type'];
            $service_fee_type = $order_detailarr['service_fee_type'];
            $service_fee = $order_detailarr['service_fee'];
            $coupon_discount = $order_detailarr['coupon_discount'];
            $coupon_name = $order_detailarr['coupon_name'];
            $coupon_amount = $order_detailarr['coupon_amount'];
            $coupon_type = $order_detailarr['coupon_type'];
            $creditcard_fee_type = $order_detailarr['creditcard_fee_type'];
            $creditcard_fee = $order_detailarr['creditcard_fee'];
            $used_wallet_balance = ($order_detailarr['used_wallet_balance']) ? $order_detailarr['used_wallet_balance'] : 0;
            $delivery_charge = ($order_detailarr['delivery_charge']) ? $order_detailarr['delivery_charge'] : 0;
            $tip_amount = ($order_detailarr['tip_amount']) ? $order_detailarr['tip_amount'] : 0;
            $item_detail_old = $order_detailarr['item_detail'];
            //update item_detail array :: start
            $del_item_name = '';
            $itematemp = array();
            $itemDetail = json_decode($updated_items,true); //updated items array from request
            $updated_item_ids = (array_column($itemDetail,'menu_id')) ? array_column($itemDetail,'menu_id') : array();
            $qty_notemp = (array_column($itemDetail,'quantity')) ? array_column($itemDetail,'quantity') : array();
            $old_qty_notemp = (array_column($item_detail_old,'qty_no')) ? array_column($item_detail_old,'qty_no') : array();
            $base_pricetemp = (array_column($itemDetail,'subTotal')) ? array_column($itemDetail,'subTotal') : array();

            if(!empty($item_detail_old)) {
                for($iord = 0; $iord < count($item_detail_old); $iord++) {
                    if($item_detail_old[$iord]['item_id'] != $itemDetail[$iord]['menu_id']) { 
                        //1 customized item added twice and removed only once
                        $Is_itemupdate = 'yes';
                        $del_itemTotal = $del_itemTotal + $item_detail_old[$iord]['itemTotal'];
                        if($del_item_name == '') {
                            $del_item_name .= $item_detail_old[$iord]['item_name'];
                        } else {
                            $del_item_name .= ', '.$item_detail_old[$iord]['item_name'];
                        }
                        unset($item_detail_old[$iord]);
                        $item_detail_old = array_values($item_detail_old);
                        $iord = $iord - 1;
                    } else if(!in_array($item_detail_old[$iord]['item_id'], $updated_item_ids)) {
                        //if an item is removed from existing cart items
                        $Is_itemupdate = 'yes';
                        $del_itemTotal = $del_itemTotal + $item_detail_old[$iord]['itemTotal'];
                        if($del_item_name == '') {
                            $del_item_name .= $item_detail_old[$iord]['item_name'];
                        } else {
                            $del_item_name .= ', '.$item_detail_old[$iord]['item_name'];
                        }
                        unset($item_detail_old[$iord]);
                    } else {
                        //in case quantity is decreased
                        if($item_detail_old[$iord]['item_id'] == $itemDetail[$iord]['menu_id']) {
                            if($qty_notemp[$iord] != $old_qty_notemp[$iord]) {
                                $Is_itemupdate = 'yes';
                                $updated_itemtotal = $qty_notemp[$iord] * $base_pricetemp[$iord];
                                $old_itemtotal = $item_detail_old[$iord]['itemTotal'];
                                $diff_itemtotal = $old_itemtotal - $updated_itemtotal;
                                $del_itemTotal = $del_itemTotal + $diff_itemtotal;
                            }

                            $itematemp[$iord]['qty_no'] = $qty_notemp[$iord];
                            $itematemp[$iord]['rate'] = $qty_notemp[$iord] * $base_pricetemp[$iord];
                            $itematemp[$iord]['base_price'] = $base_pricetemp[$iord];
                        }
                    }
                }
                $item_detail_old = array_values($item_detail_old);
                //Code for find the last order flag :: Start
                $order_flaglast = max(array_column($item_detail_old, 'order_flag'));
                if($Is_itemupdate == 'yes') {
                    $order_flaglast = intval($order_flaglast) + 1;
                }
                //Code for find the last order flag :: End
            }
            $subtotalnew = $subtotal - $del_itemTotal;
            $subtotalsave = $subtotal - $del_itemTotal;

            $item_detailup = $item_detail_old;
            $item_namemsg = '';
            $ordcnt = 0;
            $items = $item_detail_old;
            foreach ($items as $key => $value) {
                if($items[$key] != '') {
                    $item_detail = $this->api_branch_admin_model->getMenuDetail($value['item_id'],$language_slug,$order_detailarr['restaurant_id']);
                    if($value['qty_no'] > $itematemp[$key]['qty_no']) {
                        $item_namemsg .= $item_detail->name.', ';
                    }
                    //if customized item
                    if($item_detail->check_add_ons == '1') {
                        if($value['addons_category_list'] && !empty($value['addons_category_list'])) {
                            $customization = array();
                            foreach ($value['addons_category_list'] as $addon_key => $addon_value) {
                                $addonscust = array(); // for addons items
                                $catvalue = $addon_value['addons_list'];
                                foreach ($catvalue as $addkey => $addonvalue) {
                                    $addonscust[] = array(
                                        'add_ons_id'=>$addonvalue['add_ons_id'],
                                        'add_ons_name'=>$addonvalue['add_ons_name'],
                                        'add_ons_price'=>$addonvalue['add_ons_price']
                                    );
                                }

                                $customization[] = array(
                                    'addons_category_id'=>$addon_value['addons_category_id'],
                                    'addons_category'=>$addon_value['addons_category'],
                                    'addons_list'=>$addonscust
                                );
                            }
                        }
                        $item_detailup[$ordcnt] = array(
                            "item_name"=>$value['item_name'],
                            "menu_content_id"=>$value['menu_content_id'],
                            "item_id"=>$value['item_id'],
                            "qty_no"=>$itematemp[$key]['qty_no'],
                            "comment"=>$value['comment'],
                            "rate"=>$value['rate'],
                            "offer_price"=>$value['offer_price'],
                            "order_id"=>$order_id,
                            "is_customize"=>1,
                            "is_combo_item"=>0,
                            "combo_item_details" => '',
                            "subTotal"=>$itematemp[$key]['base_price'],
                            "itemTotal"=>$itematemp[$key]['rate'],
                            "order_flag"=>$order_flaglast,
                            "addons_category_list"=>$customization
                        );
                    } else {
                        $item_detailup[$ordcnt] = array(
                            "item_name"=>$value['item_name'],
                            "menu_content_id"=>$value['menu_content_id'],
                            "item_id"=>$value['item_id'],
                            "qty_no"=>$itematemp[$key]['qty_no'],
                            "comment"=>$value['comment'],
                            "rate"=>$value['rate'],
                            "offer_price"=>$value['offer_price'],
                            "order_id"=>$order_id,
                            "is_customize"=>0,
                            "is_combo_item"=>$value['is_combo_item'],
                            "combo_item_details"=> $value['combo_item_details'],
                            "subTotal"=>$itematemp[$key]['base_price'],
                            "itemTotal"=>$itematemp[$key]['rate'],
                            "order_flag"=>$order_flaglast,
                        );
                    }
                    $ordcnt++;
                }
            }
            //update item_detail array :: end
            //Code for coupon :: Code change as per multiple coupon :: Start
            //Code to the find the coupon array
            $coupon_array = $this->common_model->getCoupon_array($order_id); 
            $coupon_discount = $coupon_discounttotal = $coupon_discountup = 0;
            if(!empty($coupon_array)) {
                foreach ($coupon_array as $cp_key => $cp_value) {
                    $coupon_type = $cp_value['coupon_type'];                    
                    $coupon_amount = $cp_value['coupon_amount']; 
                    $coupon_id = $cp_value['coupon_id'];                    
                    if(strtolower($coupon_type) == 'percentage' && $coupon_amount > 0) {
                       $coupon_discountup = round(($subtotalsave * $coupon_amount) / 100,2);
                    } else {
                        $coupon_discountup = $coupon_amount;
                    }
                    if($cp_key == 0) {
                        $coupon_discount = $coupon_discountup;
                    }
                    $coupon_uparray = array(
                        'coupon_discount' => $coupon_discountup
                    );
                    $this->api_branch_admin_model->updateMultipleWhere('order_coupon_use', array('order_id' => $order_id, 'coupon_id' => $coupon_id), $coupon_uparray);
                    $coupon_discountup = round($coupon_discountup,2);
                    
                    $coupondtl_chk = $this->api_branch_admin_model->getCouponData($coupon_id);
                    if($coupondtl_chk && !empty($coupondtl_chk) && $coupondtl_chk->coupon_type!='free_delivery')
                    {
                        $coupon_uparray = array(
                            'coupon_discount'=>$coupon_discountup
                        );
                        $this->order_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$entity_id,'coupon_id'=>$coupon_id), $coupon_uparray);

                        $subtotalnew = $subtotalnew - $coupon_discountup;
                    }
                    $coupon_discounttotal = $coupon_discounttotal + $coupon_discountup;
                }
            }
            //wallet changes :: start
            $new_wallet_balance = 0;
            if($order_detailarr['user_id'] && $order_detailarr['user_id'] > 0 && $used_wallet_balance > 0 && $used_wallet_balance != NULL) {
                $new_wallet_balance = $subtotalnew;
                $wallet_to_be_refunded = $used_wallet_balance - $subtotalnew;
                //update wallet history
                $update_wallet = array('amount' => $new_wallet_balance);
                $this->api_branch_admin_model->updateMultipleWhere('wallet_history', array('order_id' => $order_id, 'user_id' => $order_detailarr['user_id'], 'debit' => 1, 'is_deleted' => 0), $update_wallet);
                //update wallet amount
                $users_wallet = $this->api_branch_admin_model->getUsersWalletMoney($order_detailarr['user_id']);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $new_wallet_amount = $current_wallet + $wallet_to_be_refunded;
                $refund_wallet = array('wallet' => $new_wallet_amount);
                $this->api_branch_admin_model->updateData($refund_wallet, 'users', 'entity_id', $order_detailarr['user_id']);
            }
            //wallet changes :: end
            //Code for tax :: Start
            $tax_rateval = 0;
            if(strtolower($tax_type) == 'percentage') {
               $tax_rateval = ($subtotalsave * $tax_rate) / 100;
            } else {
                $tax_rateval = $tax_rate;
            }
            $tax_rateval = round($tax_rateval, 2);
            //Code for tax :: End
            //Code for service fee :: Start
            $service_feeval = 0;
            if(strtolower($service_fee_type) == 'percentage') {
               $service_feeval = ($subtotalsave * $service_fee) / 100;
            } else {
                $service_feeval = $service_fee;
            }
            $service_feeval = round($service_feeval, 2);
            //Code for service fee :: End
            //Code for credit card fee :: Start
            $creditcard_feeval = 0;
            if(strtolower($creditcard_fee_type) == 'percentage') {
               $creditcard_feeval = ($subtotalsave * $creditcard_fee) / 100;
            } else {
                $creditcard_feeval = $creditcard_fee;
            }
            $creditcard_feeval = round($creditcard_feeval, 2);
            //Code for credit card fee :: End
            //Final total code
            $total_rate = ($subtotalnew + $service_feeval + $tax_rateval + $creditcard_feeval + $delivery_charge + $tip_amount) - ($new_wallet_balance+$coupon_discounttotal);

            //refund reason :: start
            $refund_reason = '';
            if(strtolower($order_detailarr['payment_option']) == 'stripe' || strtolower($order_detailarr['payment_option']) == 'applepay' || strtolower($order_detailarr['payment_option']) == 'paypal') {
                $new_refund_reason = ($partial_refund_reason) ? trim($partial_refund_reason) : '';
                $old_refund_reason = $order_detailarr['refund_reason'];            
                $refund_reason = ($new_refund_reason != '') ? $new_refund_reason : '';
                if($old_refund_reason != '') {
                    $refund_reason = ($refund_reason != '') ? $refund_reason.'<br/>'.$old_refund_reason : $old_refund_reason;
                }
            }
            //refund reason :: end
            //refund amount calculation :: start
            $order_total_new = $total_rate;
            $order_total_old = $order_detailarr['total_rate'];
            $refunded_amount = $order_total_old - $order_total_new; //to refund the amount in current process
            $refunded_amount = round($refunded_amount, 2);

            $old_refunded_amount = floatval($order_detailarr['refunded_amount']);
            $new_refunded_amount = $old_refunded_amount + $refunded_amount; //to store total refunded amount in DB
            //refund amount calculation :: end
            if($Is_itemupdate == 'yes') {
                //partial refund :: start
                $response['error'] == '';
                if(strtolower($order_detailarr['payment_option']) == 'stripe' || strtolower($order_detailarr['payment_option']) == 'applepay') {
                    $payment_optionval = strtolower($order_detailarr['payment_option']);
                    $response = $this->common_model->Stripe_PartialRefund($order_detailarr['transaction_id'], $order_id, $refunded_amount, $new_refunded_amount, $new_refund_reason, $payment_optionval);
                } else if(strtolower($order_detailarr['payment_option']) == 'paypal') {
                    $response = $this->common_model->Paypal_PartialRefund($order_detailarr['transaction_id'], $order_id, $refunded_amount, $new_refunded_amount, $new_refund_reason);
                }
                //partial refund :: end
                if($response['error'] == 'yes') {
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('status' => 0, 'message' => $this->lang->line('admin_refund_failed')."<br>".$response['error_message']);
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['status' => 0, 'message' => $this->lang->line('admin_refund_failed')."<br>".$response['error_message']], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                } else {
                    if(strtolower($order_detailarr['payment_option']) == 'stripe' || strtolower($order_detailarr['payment_option']) == 'applepay' || strtolower($order_detailarr['payment_option']) == 'paypal') {
                        //save user log :: partial refund
                        $resname = $this->common_model->getResNameWithOrderId($order_id);
                        $this->common_model->save_user_log($tokenres->first_name.' '.$tokenres->last_name.' partial refunded for order - '.$order_id.' (ordered from: '.$resname.')', $user_id);
                    }
                    //update coupon discount
                    $coupon_uparray = array(
                        'coupon_discount'=>$coupon_discountup
                    );
                    $this->api_branch_admin_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$order_id,'coupon_id'=>$coupon_id), $coupon_uparray);
                    //update item details
                    $update_order = array(
                        'item_detail' => serialize($item_detailup),
                        'is_updateorder' => '1'
                    );
                    $this->api_branch_admin_model->updateData($update_order,'order_detail','order_id',$order_id);
                    //update price fields in order master
                    $update_data = array(              
                        'total_rate' => $total_rate,
                        'subtotal' => $subtotalsave,
                        'coupon_discount' => $coupon_discount,
                        'refund_reason' => (strtolower($order_detailarr['payment_option']) == 'stripe' || strtolower($order_detailarr['payment_option']) == 'applepay' || strtolower($order_detailarr['payment_option']) == 'paypal') ? $refund_reason : NULL,
                        'used_earning' => $new_wallet_balance,
                        'updated_by' => $user_id,
                        'updated_date' => date('Y-m-d H:i:s')
                    );
                    $this->api_branch_admin_model->updateData($update_data,'order_master','entity_id',$order_id);
                    //save user log :: edit order
                    $this->common_model->save_user_log($tokenres->first_name.' '.$tokenres->last_name.' edited an order - '.$order_id, $user_id);
                    //notification via sms and email :: start
                    $payment_methodarr = array('stripe','paypal','applepay');
                    $updated_bytxt = $tokenres->first_name.' '.$tokenres->last_name;
                    $order_refund_text = '';
                    if(in_array(strtolower($order_detailarr['payment_option']), $payment_methodarr)) {
                        $default_currency = get_default_system_currency();
                        $refunded_amountdis = currency_symboldisplay(number_format_unchanged_precision($refunded_amount,$default_currency->currency_code),$default_currency->currency_symbol);
                        $order_refund_text = sprintf($this->lang->line('order_refund_text'),$refunded_amountdis);
                    }
                    $user_email_id = ($order_detailarr['email']) ? trim($order_detailarr['email']) : '';
                    $user_email_idtemp = ($order_detailarr['user_detail']['email']) ? trim($order_detailarr['user_detail']['email']) : '';
                    $order_username = ($order_detailarr['user_detail']['first_name']) ? trim($order_detailarr['user_detail']['first_name']).' '.trim($order_detailarr['user_detail']['last_name']) : '';
                    if($user_email_id != '' || $user_email_idtemp != '') {
                        if($user_email_id == '') {
                            $user_email_id = $user_email_idtemp;
                        }
                    }
                    if($user_email_id != '') {
                        $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-updated','language_slug'=>$language_slug,'status'=>1))->first_row();
                        $arrayData = array('FirstName'=>$order_username,'order_id'=>$order_id, 'updated_by'=>$updated_bytxt, 'order_refund_text'=>$order_refund_text);
                        $EmailBody = generateEmailBody($email_template->message,$arrayData);

                        //get System Option Data
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        if(!empty($EmailBody)) {     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to($user_email_id); 
                            $this->email->subject($email_template->subject);  
                            $this->email->message($EmailBody);  
                            $this->email->send(); 
                        }
                    } else {
                        $sms = 'Your order#'.$order_id.' has been updated by '.$updated_bytxt;
                        if($order_refund_text!='') {
                            $sms = $sms.'. '.$order_refund_text;
                        }
                        $mobile_numberT = ($order_detailarr['phone_code']) ? $order_detailarr['phone_code'] : '+1';
                        $mobile_numberT = $mobile_numberT.$order_detailarr['mobile_number'];
                        if($mobile_numberT == '' || $mobile_numberT == '+1') {
                            $mobile_numberT = ($order_detailarr['user_mobile_number']) ? '+'.$order_detailarr['user_mobile_number'] : '';
                        }
                        if($mobile_numberT != '' && $mobile_numberT != '+1') {
                            $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);  
                        }
                    }
                    //notification for customer app
                    if($order_detailarr['notification'] == 1 && ($item_namemsg != '' || $del_item_name != '')) {
                        $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$order_detailarr['language_slug']))->first_row();
                        $this->lang->load('messages_lang', $languages->language_directory);
                        if($del_item_name != '') {
                            $message = $this->lang->line('order_item_rejected1').' '.$del_item_name.' '.$this->lang->line('order_item_rejected2');
                        }
                        if($item_namemsg != '') {
                            $message = sprintf($this->lang->line('delivery_pickup_order_update1'),$order_id).' '.$this->lang->line('delivery_pickup_order_update2').' ';
                            $item_namemsg = rtrim($item_namemsg, ", ");
                            $message = $message.$item_namemsg;
                        }
                        $device_id = $order_detailarr['device_id'];
                        $this->sendFCMRegistration($device_id, $message, $order_detailarr['order_status'], $order_detailarr['restaurant_id'], FCM_KEY);
                    }
                    //notification for website
                    if($order_detailarr['user_id'] && $order_detailarr['user_id'] > 0) {
                        $website_notification = array(
                            'order_id' => $order_detailarr['order_id'],
                            'user_id' => $order_detailarr['user_id'],
                            'notification_slug' => 'order_updated',
                            'view_status' => 0,
                            'datetime' => date("Y-m-d H:i:s"),
                        );
                        $this->common_model->addData('user_order_notification',$website_notification);
                    }
                    //notification to agent
                    if($order_detailarr['agent_id']) {
                        $this->common_model->notificationToAgent($order_detailarr['order_id'], 'order_updated');
                    }
                    if($this->post('isEncryptionActive') == TRUE) {
                        $response = array('updated_items' => $item_detailup, 'total_rate' => $total_rate, 'subtotal' => $subtotalsave, 'status' => 1, 'message' => $this->lang->line('success_update'));
                        $encrypted_data = $this->common_model->encrypt_data($response);
                        $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    } else {
                        $this->response(['updated_items' => $item_detailup, 'total_rate' => $total_rate, 'subtotal' => $subtotalsave, 'status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                }
            } else {
                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 0, 'message' => $this->lang->line('item_editerrormsg'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('item_editerrormsg')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => 0, 'message' => $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //Restaurant Order schedule code :: Start
    public function getAssignedRestaurants_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;            
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');            
        }
        $tokenres = $this->api_branch_admin_model->checkToken($user_id);
        
        if($tokenres)
        {
            $timeout_steps = array(15, 30, 45, 60);
            $schedule_modearr = array(0=>$this->lang->line('normal'),1=>$this->lang->line('busy'),2=>$this->lang->line('very_busy'));

            $normal_start = $this->api_branch_admin_model->getSystemOptoin('schedule_normal_start');
            $normal_end = $this->api_branch_admin_model->getSystemOptoin('schedule_normal_end');

            $busy_start = $this->api_branch_admin_model->getSystemOptoin('schedule_busy_start');
            $busy_end = $this->api_branch_admin_model->getSystemOptoin('schedule_busy_end');

            $verybusy_start = $this->api_branch_admin_model->getSystemOptoin('schedule_verybusy_start');
            $verybusy_end = $this->api_branch_admin_model->getSystemOptoin('schedule_verybusy_end');

            $available_modearr = array();
            foreach ($schedule_modearr as $sh_key => $sh_value)
            {
                $available_modearr[$sh_key]['id'] = $sh_key;
                $available_modearr[$sh_key]['title'] = $sh_value;
                if($sh_key==0)
                {
                    $time_slotval = $normal_start->OptionValue.'-'.$normal_end->OptionValue.' '.$this->lang->line('minutes');
                }
                else if($sh_key==1)
                {
                    $time_slotval = $busy_start->OptionValue.'-'.$busy_end->OptionValue.' '.$this->lang->line('minutes');
                }
                else if($sh_key==2)
                {
                    $time_slotval = $verybusy_start->OptionValue.'-'.$verybusy_end->OptionValue.' '.$this->lang->line('minutes');
                }
                $available_modearr[$sh_key]['time'] = $time_slotval;
            }
            
            $result = $this->api_branch_admin_model->getRestaurantmode_detail($user_id, $tokenres->user_type);
            $result_arr = array();
            if($result && !empty($result))
            {
                foreach ($result as $key => $value)
                {
                    $result_arr[$key]['restaurant_id'] = $value->entity_id;
                    $result_arr[$key]['restaurant_content_id'] = $value->content_id;
                    $result_arr[$key]['restaurant_name'] = $value->name;
                    $result_arr[$key]['currentMode'] = $value->schedule_mode;
                    $result_arr[$key]['currentMode_Name'] = $schedule_modearr[$value->schedule_mode];
                }

                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('arrayRestaurants' => $result_arr, 'status' => 1, 'available_mode' => $available_modearr, 'timeout_steps' => $timeout_steps, 'message' => $this->lang->line('record_found'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['arrayRestaurants' => $result_arr, 'status' => 1, 'available_mode' => $available_modearr, 'timeout_steps' => $timeout_steps, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
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
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function updateRestaurantMode_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE)
        {
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));            
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $user_id = $decrypted_data->user_id;
            $restaurant_content_id = $decrypted_data->restaurant_content_id;
            $off_time = $decrypted_data->off_time;
            $restaurant_schedule_mode = ($decrypted_data->restaurant_schedule_mode)?$decrypted_data->restaurant_schedule_mode:'0';
            $user_timezone = $decrypted_data->user_timezone;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $user_id = $this->post('user_id');
            $restaurant_content_id = $this->post('restaurant_content_id');
            $off_time = $this->post('off_time');
            $restaurant_schedule_mode = ($this->post('restaurant_schedule_mode'))?$this->post('restaurant_schedule_mode'):'0';
            $user_timezone = $this->post('user_timezone');
        }
        $tokenres = $this->api_branch_admin_model->checkToken($user_id);
        
        if($tokenres)
        {
            if(intval($restaurant_content_id)>0 )
            {
                //Time sotre base on UTC time zone :: Start
                date_default_timezone_set(default_timezone);
                $offlinetime=time();
                $timezone_name = ($user_timezone)?$user_timezone:default_timezone;
                date_default_timezone_set($timezone_name);
                //Time sotre base on UTC time zone :: End
                
                if($off_time>0)
                {
                    $offlinetime=$offlinetime+$off_time*60;
                }
                $update_arr = array('schedule_mode' => trim($restaurant_schedule_mode),'schedule_time' => $offlinetime,'enable_schedule' => 1);
                $this->common_model->updateData('restaurant', $update_arr, 'content_id', $restaurant_content_id);                

                if($this->post('isEncryptionActive') == TRUE) {
                    $response = array('status' => 1,'message' => $this->lang->line('success_schedule_msg'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 1,'message' => $this->lang->line('success_schedule_msg')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }
            else
            {   
                if($this->post('isEncryptionActive') == TRUE)
                {
                    $response = array('status' => 0, 'message' => $this->lang->line('validation'));
                    $encrypted_data = $this->common_model->encrypt_data($response);
                    $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response(['status' => 0, 'message' => $this->lang->line('validation')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
        else
        {
            if($this->post('isEncryptionActive') == TRUE) {
                $response = array('status' => -1, 'message' => $this->lang->line('sess_expired'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response(['status' => -1, 'message' => $this->lang->line('sess_expired')], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //Restaurant Order schedule code :: End
}