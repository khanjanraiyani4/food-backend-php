<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class General_api extends REST_Controller {
    function __construct(){
        parent::__construct();
    }
    public function getLang($language_slug)
    {
        if($language_slug){
            $languages = $this->common_model->get_languages($language_slug);
            $this->current_lang = $languages->language_slug;
            $this->lang->load('messages_lang', $languages->language_directory);
        } else {
            $default_lang = $this->common_model->getdefaultlang();
            $this->current_lang = $default_lang->language_slug;
            $this->lang->load('messages_lang', $default_lang->language_directory);
        }
    }
    // Get active languages
    public function language_list_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
        }else{
            $this->getLang($this->post('language_slug'));
        }
        $language_list = $this->common_model->list_available_languages();
        $language_file = $this->common_model->getLanguageFileMobileApp();

        //Code for disatnace type :: Start
        $this->db->select('OptionValue');
        $distance_inarr = $this->db->get_where('system_option',array('OptionSlug'=>'distance_in'))->first_row();

        $distance_inVal = $this->lang->line('in_km');
        $distance_in ='0';
        if($distance_inarr && !empty($distance_inarr))
        {
            if($distance_inarr->OptionValue==0){
                $distance_inVal = $this->lang->line('in_mile');
                $distance_in ='1';
            }
        }
        //Code for disatnace type :: End

        if($language_list)
        {
            //Code for default language :: Start
            $default_language_slug = 'en'; $default_language_name = 'English';
            foreach($language_list as $key => $value)
            {
                if($value->language_default=='1'){
                   $default_language_slug = $value->language_slug; 
                   $default_language_name = $value->language_name;
                }
            }
            //Code for default language :: End

            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'language_list' => $language_list,
                    'language_file' => $language_file,
                    'use_mile' => $distance_in,
                    'default_language_slug' => $default_language_slug,
                    'default_language_name' => $default_language_name,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'language_list' => $language_list,
                    'language_file' => $language_file,
                    'use_mile' => $distance_in,
                    'default_language_slug' => $default_language_slug,
                    'default_language_name' => $default_language_name,
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
    // Get cancel/reject reasons
    public function cancel_reject_reason_list_post(){
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
            $reason_type = $decrypted_data->reason_type;
            $user_type = $decrypted_data->user_type;
        }else{
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
            $reason_type = $this->post('reason_type');
            $user_type = $this->post('user_type');
        }
        $reason_list = $this->common_model->list_cancel_reject_reasons($reason_type, $language_slug, $user_type);
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
    // Get CMS Pages
    public function getCMSPage_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $cms_slug = $decrypted_data->cms_slug;
            $language_slug = $decrypted_data->language_slug;
            $user_type = $decrypted_data->user_type;
            $user_id = $decrypted_data->user_id;
        }else{
            $this->getLang($this->post('language_slug'));
            $cms_slug = $this->post('cms_slug');
            $language_slug = $this->post('language_slug');
            $user_type = $this->post('user_type');
            $user_id = $this->post('user_id');
        }
        $language_slug = ($language_slug)?$language_slug:$this->current_lang;
        $cmsData = $this->common_model->getCMSRecord('cms', $cms_slug, $language_slug);
        $res_aboutus = '';
        $resp_status = 1;
        if(strtolower($user_type)=='admin' && $user_id){
            $res_aboutus = $this->common_model->getResAboutUs($user_id);
            foreach ($cmsData as $key => $value) {
                if($value->CMSSlug == 'about-us'){
                    $value->description = (!empty($res_aboutus) && $res_aboutus != '' && $res_aboutus->about_restaurant)?$res_aboutus->about_restaurant:'';
                    $resp_status = (!empty($res_aboutus) && $res_aboutus != '' && $res_aboutus->about_restaurant)?1:0;
                }
            }
        }
        $this->db->select('OptionValue');
        $phone_code = $this->db->get_where('system_option',array('OptionSlug'=>'phone_code'))->first_row();
        $phone_code = $phone_code->OptionValue; 
        $contactus_phone = $this->db->get_where('system_option',array('OptionSlug'=>'contactus_phone'))->first_row();
        $contact_us_phone = $contactus_phone->OptionValue; 
        if ($cmsData && $resp_status == 1){            
            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'cmsData'=>$cmsData,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'status' => 1,
                    'message' => $this->lang->line('found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'cmsData'=>$cmsData,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'status' => 1,
                    'message' => $this->lang->line('found')
                ], REST_Controller::HTTP_OK);
            }            
        } else if($resp_status == 0){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'message' =>  $this->lang->line('no_desc_in_res'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 0,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'message' =>  $this->lang->line('no_desc_in_res')], REST_Controller::HTTP_OK);
            }
        } else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 0,
                    'phone_code'=>$phone_code,
                    'contact_us_phone'=>$contact_us_phone,
                    'shouldAllowFacebookLogin'=>true,
                    'google_map_api_key'=>google_key,
                    'google_webclient_id'=>google_webclient_id,
                    'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK);
            }            
        }
    }
    // Get country code details which are active
    public function country_code_list_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $language_slug = $decrypted_data->language_slug;
        }
        else
        {
            $this->getLang($this->post('language_slug'));
            $language_slug = $this->post('language_slug');
        }
        $country_list = $this->common_model->list_country_codes(true);
        if($country_list){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'country_list' => $country_list,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'country_list' => $country_list,
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

    // Get Application Version
    public function getAppVersion_post()
    {    
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $platform = $decrypted_data->platform;
            $user_type = $decrypted_data->user_type;
            $language_slug = $decrypted_data->language_slug;
        }else{
            $this->getLang($this->post('language_slug'));
            $platform = $this->post('platform');
            $user_type = $this->post('user_type');
            $language_slug = $this->post('language_slug');
        }
        $option_slug_live = $user_type."_app_".$platform."_live_version";
        $option_slug_force = $user_type."_app_".$platform."_force_version";
        $this->db->select('OptionValue');
        $app_live_version = $this->db->get_where('system_option',array('OptionSlug'=>$option_slug_live))->first_row();
        $app_force_version = $this->db->get_where('system_option',array('OptionSlug'=>$option_slug_force))->first_row();
        // Application URL
        $platform_slug = "";        
        if($user_type == "customer"){
            $user_type_slug = "";   
        }        
        else {
            $user_type_slug = $user_type."_";
        }
        if($platform=="android"){
            $option_slug_url= $user_type_slug."playstore_url";
        }
        if($platform=="ios"){
            $option_slug_url= $user_type_slug."app_store_url";
        }

        $this->db->select('OptionValue');
        $app_url = $this->db->get_where('system_option',array('OptionSlug'=>$option_slug_url))->first_row();
        if (!empty($option_slug_live->OptionValue) || !empty($app_force_version->OptionValue)){
            if($this->post('isEncryptionActive') == TRUE){
                $response = array(
                    'app_live_version'=>$app_live_version->OptionValue,
                    'app_force_version'=>$app_force_version->OptionValue,
                    'app_url'=>$app_url->OptionValue,
                    'status' => 1,
                    'message' => $this->lang->line('found')
                );
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'app_live_version'=>$app_live_version->OptionValue,
                    'app_force_version'=>$app_force_version->OptionValue,
                    'app_url'=>$app_url->OptionValue,
                    'status' => 1,
                    'message' => $this->lang->line('found')
                ], REST_Controller::HTTP_OK);
            }            
        }else{
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 0,'message' =>  $this->lang->line('not_found'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 0,'message' =>  $this->lang->line('not_found')], REST_Controller::HTTP_OK);
            }            
        }
    }
    //Contact Us
    public function contactUs_post()
    {
        if($this->post('encryptedData') && $this->post('isEncryptionActive') == TRUE){
            $decrypted_data = $this->common_model->decrypt_data($this->post('encryptedData'));
            $this->getLang($decrypted_data->language_slug);
            $first_name = $decrypted_data->first_name;
            $last_name = $decrypted_data->last_name;
            $email = $decrypted_data->email;
            $res_phone_number = $decrypted_data->res_phone_number;
            $owner_phone_number = $decrypted_data->owner_phone_number;
            $res_name = $decrypted_data->res_name;
            $res_zip_code = $decrypted_data->res_zip_code;
            $message = $decrypted_data->message;
        }else{
            $this->getLang($this->post('language_slug'));
            $first_name = $this->post('first_name');
            $last_name = $this->post('last_name');
            $email = $this->post('email');
            $res_phone_number = $this->post('res_phone_number');
            $owner_phone_number = $this->post('owner_phone_number');
            $res_name = $this->post('res_name');
            $res_zip_code = $this->post('res_zip_code');
            $message = $this->post('message');
        }
        if($first_name != '' && $last_name != '' && $email !='' && $res_phone_number !='' && $owner_phone_number !='' && $res_name !='' && $res_zip_code !=''){
            //get System Option Data
            $this->db->select('OptionValue');
            $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
            $this->db->select('OptionValue');
            $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
            //email to user
            $this->db->select('subject,message');
            $Emaildata_user = $this->db->get_where('email_template',array('email_slug'=>'contact-us','status'=>1))->first_row();
            
            $arrayData_user = array('FirstName'=>trim($first_name),'LastName'=>trim($last_name),'Email'=>trim($email),'res_phone_number'=>trim($res_phone_number),'res_name'=>trim($res_name),'res_zip_code'=>trim($res_zip_code),'Message'=>trim($message));
            $EmailBody_user = generateEmailBody($Emaildata_user->message,$arrayData_user);  
            
            /*Conectoo Email api start : 18march2021*/
            $this->load->library('email');
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['newline'] = "\r\n";
            $this->email->initialize($config);
            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
            $this->email->to(trim($email));
            $this->email->subject($Emaildata_user->subject);
            $this->email->message($EmailBody_user);
            $this->email->send();

            //email to admin
            $this->db->select('subject,message');
            $Emaildata_admin = $this->db->get_where('email_template',array('email_slug'=>'contact-us-for-admin','status'=>1))->first_row();
            
            // admin email 
            $this->db->select('OptionValue');
            $AdminEmailAddress = $this->db->get_where('system_option',array('OptionSlug'=>'Admin_Email_Address'))->first_row();
            $arrayData_admin = array('FirstName'=>trim($first_name),'LastName'=>trim($last_name),'Email'=>trim($email),'res_phone_number'=>trim($res_phone_number),'res_name'=>trim($res_name),'res_zip_code'=>trim($res_zip_code),'Message'=>trim($message));
            $EmailBody_admin = generateEmailBody($Emaildata_admin->message,$arrayData_admin);
            /*Conectoo Email api start : 18march2021*/
            $this->load->library('email');
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['newline'] = "\r\n";
            $this->email->initialize($config);
            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
            $this->email->to($AdminEmailAddress->OptionValue);
            $this->email->subject($Emaildata_admin->subject);
            $this->email->message($EmailBody_admin);
            $this->email->send();
            
            $add_content = array(
              'first_name'=>$first_name,
              'last_name'=>$last_name, 
              'email '=>$email, 
              'rest_name'=>$res_name, 
              'res_zip_code'=>$res_zip_code, 
              'res_phone_number'=>'+'.$res_phone_number, 
              'owners_phone_number'=>'+'.$owner_phone_number,  
              'message'=> $message, 
              'created_date'=>date('Y-m-d H:i:s')                      
            );
            $this->common_model->addData('contactus_detail',$add_content);
            
            if($this->post('isEncryptionActive') == TRUE){
                $response = array('status' => 1,'message' => $this->lang->line('message_sent'));
                $encrypted_data = $this->common_model->encrypt_data($response);
                $this->response(['encryptedResponse'=>$encrypted_data, 'isEncryptionActive'=>true], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }else{
                $this->response(['status' => 1,'message' => $this->lang->line('message_sent')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
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
}