<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Notification extends CI_Controller { 
    public $full_module = 'Notification'; 
    public $module_name = 'Notification';
    public $controller_name = 'notification';

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/notification_model');
    }
    public function view() {
        if(in_array('notification~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('notification').' | '.$this->lang->line('site_title');
            //notification count
            $this->db->select('entity_id');
            $data['noti_count'] = $this->db->get('notifications')->num_rows();
            $this->load->view(ADMIN_URL.'/notification',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function add() {
        if(in_array('notification~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_notificationadd').' | '.$this->lang->line('site_title');
            if($this->input->post('submitNotification') == "Submit")
            {
                $this->form_validation->set_rules('notification_title', $this->lang->line('notifi_title'), 'trim|required');
                if ($this->form_validation->run())
                {
                    $addNotificationData = array(                   
                        'notification_title'=>utf8_encode($this->input->post('notification_title')),                    
                        'notification_description' =>utf8_encode($this->input->post('notification_description')),
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );                                            
                    $NotificationID = $this->notification_model->addData('notifications',$addNotificationData);
                    
                    //$UserIds = explode(',', $this->input->post('selected_userids'));
                    //Code for find the userids base on distance between user and restaurant :: Start
                    $UserIdstemp = explode(',', $this->input->post('selected_userids'));
                    $restaurant_str = ($this->input->post('restaurant'))?$this->input->post('restaurant'):'';
                    $distance = ($this->input->post('distance'))?$this->input->post('distance'):'';                    
                    $restaurant_arr = array();
                    $rest_lat = $rest_long = '';
                    if($restaurant_str!='')
                    {
                        $restaurant_arr = explode("###",$restaurant_str);
                        $rest_lat = $restaurant_arr[1];
                        $rest_long = $restaurant_arr[2];
                    }
                    if($rest_lat!='' && $rest_long!='' && intval($distance)>0 && !empty($UserIdstemp))
                    {
                        $UserIds = $this->notification_model->getUserIdswithDistance($rest_lat,$rest_long,$distance,$UserIdstemp);
                    }
                    else
                    {
                        $UserIds = explode(',', $this->input->post('selected_userids'));
                    }                  
                    //Code for find the userids base on distance between user and restaurant :: End
                    //$UserIds = $this->input->post('user_id');

                    $NotificationDetail = array();
                    if($this->input->post('save') == 1  && !empty($UserIds)){
                        for ($u=0; $u < count($UserIds); $u++) { 
                            $NotificationDetail[] = array('notification_id' => $NotificationID, 'user_id'=>$UserIds[$u]);
                        }                
                        $this->notification_model->addRecordBatch('notifications_users',$NotificationDetail);
                    }
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a notification - '.$this->input->post('notification_title'));
                    // START Push Notification
                    $DeviceIds = array();
                    if(!empty($UserIds)){
                        $DeviceIds = $this->notification_model->getUserDevices($UserIds);
                    }
                    
                    $registrationIds = array_column($DeviceIds, 'device_id');
                    $return = array_chunk($registrationIds,800);    
                    foreach ($return as $key => $registrationId) {
                        #prep the bundle
                        $fields = array();            
                        if(is_array($registrationId) && count($registrationId) > 1){
                            $fields['registration_ids'] = $registrationId; // multiple user to send push notification
                        }else{
                            $fields['to'] = $registrationId[0]; // only one user to send push notification
                        }          
                        $fields['notification']['title'] = $this->input->post('notification_title');
                        $fields['notification']['body'] = $this->input->post('notification_description');
                        $fields['notification']['sound'] = 'default';
                        if($this->input->post('save') == 1){
                            $fields['data'] = array ('screenType'=>'noti');
                        }else{
                            $fields['data'] = array ('screenType'=>'noti');
                        }
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
                   
                    // END Push Notification
                    // $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_add'));
                    $_SESSION['NotificationMSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/notification/view');                 
                }
            }            
            $data['users'] =  $this->notification_model->getUserNotification();
            $data['cities_arr'] =  $this->notification_model->getUserCitiesandZip('city');
            $data['zipcode_arr'] =  $this->notification_model->getUserCitiesandZip('zip');
            $data['restaurant_arr'] = $this->notification_model->get_restaurants($this->session->userdata('language_slug'));
            $this->load->view(ADMIN_URL.'/notification_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function edit() {
        if(in_array('notification~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_notificationedit').' | '.$this->lang->line('site_title');
            if($this->input->post('submitNotification') == "Submit")
            {   
                $this->form_validation->set_rules('notification_title', $this->lang->line('notifi_title'), 'trim|required');
                if ($this->form_validation->run())
                {
                    $edit_data = array(                   
                        'notification_title'=>utf8_encode($this->input->post('notification_title')),                    
                        'notification_description' =>utf8_encode($this->input->post('notification_description')),
                    );                                            
                    $this->notification_model->updateData($edit_data,'notifications','entity_id',$this->input->post('entity_id'));

                    $UserIds = explode(',', $this->input->post('selected_userids'));
                    //$UserIds = $this->input->post('user_id');
                    $NotificationDetail = array();
                    if($this->input->post('save') == 1){
                        for ($u=0; $u < count($UserIds); $u++) { 
                            $NotificationDetail[] = array('notification_id' => $this->input->post('entity_id'), 'user_id'=>$UserIds[$u]);
                        }                
                        $this->notification_model->deleteInsertRecord('notifications_users','notification_id',$this->input->post('entity_id'),$NotificationDetail);
                    }
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a notification - '.$this->input->post('notification_title'));
                    // START Push Notification
                    $DeviceIds = $this->notification_model->getUserDevices($UserIds);             
                    $registrationIds = array_column($DeviceIds, 'device_id');
                    $return = array_chunk($registrationIds,800);    
                    foreach ($return as $key => $registrationId) {
                        #prep the bundle
                        $fields = array();            
                        if(is_array($registrationId) && count($registrationId) > 1){
                            $fields['registration_ids'] = $registrationId; // multiple user to send push notification
                        }else{
                            $fields['to'] = $registrationId[0]; // only one user to send push notification
                        }          
                        $fields['notification']['title'] = $this->input->post('notification_title');
                        $fields['notification']['body'] = $this->input->post('notification_description');
                        $fields['notification']['sound'] = 'default';
                        if($this->input->post('save') == 1){
                            $fields['data'] = array ('screenType'=>'noti');
                        }else{
                            $fields['data'] = array ('screenType'=>'noti');
                        }
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
                   
                    // END Push Notification
                    // $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_update'));
                    $_SESSION['NotificationMSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/notification/view');                 
                }
            }
            $data['users'] =  $this->notification_model->getUserNotification();
            $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
            $data['editNotificationDetail'] = $this->notification_model->getEditDetail('notifications',$entity_id);
            $data['NotificationUsers'] = array();
            $data['NotificationDrivers'] = array();
            $NotificationUsers = array();
            if (!empty($data['editNotificationDetail'])) {
                if ($data['editNotificationDetail']->entity_id) {
                    $NotificationUsers = $this->notification_model->getNotificationUsers($data['editNotificationDetail']->entity_id);
                    $NotificationUsers = array_column($NotificationUsers, "user_id");
                    
                    foreach ($NotificationUsers as $key => $value) { 
                        $checkUser = $this->notification_model->checkUser($value);
                        $checkDriver = $this->notification_model->checkDriver($value);
                        if($checkUser){
                            array_push($data['NotificationUsers'],$value);
                        } elseif($checkDriver){
                            array_push($data['NotificationDrivers'],$value);
                        }
                    } 
                }
            }
            $this->load->view(ADMIN_URL.'/notification_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function ajaxview() {
        $searchTitleName = ($this->input->post('pageTitle') != '')?$this->input->post('pageTitle'):'';
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'notification_title',2=>'Status',3=>'CreatedDate');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $NotificationData = $this->notification_model->getNotificationList($searchTitleName,$sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $NotificationData['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($NotificationData['data'] as $key => $notificationDetails) {
            $deleteName = addslashes($notificationDetails->notification_title);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_notification')),$deleteName)."'";
            $noti_edit_btn = (in_array('notification~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm default-btn margin-bottom red" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($notificationDetails->entity_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a>' : '';
            $noti_delete_btn = (in_array('notification~ajaxdeleteNotification',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteNotification('.$notificationDetails->entity_id.','.$msgDelete.')" class="delete btn btn-sm default-btn margin-bottom red" title="'.$this->lang->line('delete').'"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                utf8_decode($notificationDetails->notification_title),                
                $noti_edit_btn.$noti_delete_btn
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxdeleteNotification() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $notidetails = $this->notification_model->getEditNotificationDetail($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted a notification - '.$notidetails->notification_title);
            $this->notification_model->deleteRecord($entity_id);
        }
        // $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_delete'));
        $_SESSION['NotificationMSG'] = $this->lang->line('success_delete');
    }
    //New code for find the customer list base on city/zipcode :: Start
    public function getCustomerList()
    {
        $data_array = (!empty($this->input->post('data_array')))?$this->input->post('data_array'):[];
        $list_for = (!empty($this->input->post('list_for')))?$this->input->post('list_for'):'';
        $customer_list = $this->notification_model->getUserswitCityZip($data_array,$list_for);

        $html = '';
        if(!empty($data_array) && !empty($customer_list['users']))
        {
            foreach ($customer_list['users'] as $key => $value){                
                $html .= '<option value="'.$value->entity_id.'">'.$value->first_name.' '.$value->last_name.'</option>';
            }
        }
        else
        {
            if(!empty($customer_list['users']))
            {
                $html .= '<optgroup label="'.$this->lang->line('all_customers').'">';
                foreach ($customer_list['users'] as $key => $value){                
                    $html .= '<option value="'.$value->entity_id.'">'.$value->first_name.' '.$value->last_name.'</option>';
                }
                $html .= '</optgroup>';
            }
            if(!empty($customer_list['drivers']))
            {
                $html .= '<optgroup label="'.$this->lang->line('all_drivers').'">';
                foreach ($customer_list['drivers'] as $driver_key => $driver_value){                
                    $html .= '<option value="'.$driver_value->entity_id.'">'.$driver_value->first_name.' '.$driver_value->last_name.'</option>';
                }
                $html .= '</optgroup>';
            }
        }
        echo $html;
    }
    //New code for find the customer list base on city/zipcode :: End
}