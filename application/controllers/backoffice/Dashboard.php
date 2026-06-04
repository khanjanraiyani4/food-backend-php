<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Dashboard extends CI_Controller {
    public $controller_name = 'dashboard';    
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/dashboard_model');
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
    }
    public function index() {
        $arr['meta_title'] = $this->lang->line('title_admin_dashboard').' | '.$this->lang->line('site_title');   
        if (isset($_COOKIE['user_notifications'])) {
            if ($_COOKIE['user_notifications'] == $this->session->userdata('AdminUserID')) {
                if (isset($_COOKIE['accepted_notifications'])) {
                    $acceptedNotifications = json_decode($_COOKIE['accepted_notifications']);
                    foreach ($acceptedNotifications as $key => $value) {
                        $array_accepted[] = $value->alert_id;
                    }
                } 
            }
        }
        $getAllNotifications = $this->dashboard_model->getNotifications();  
        if (!empty($getAllNotifications)) {
            foreach ($getAllNotifications as $key => $value) {
                if (!empty($array_accepted)) {
                    if (in_array($value['alert_id'], $array_accepted)) {
                        unset($getAllNotifications[$key]);
                    }
                }
            }
        }
        $arr['Notifications'] = $getAllNotifications;
        if($this->input->post('submit_page') == "Submit")
        {            
            $this->form_validation->set_rules('user_id[]', $this->lang->line('users'), 'trim|required');
            $this->form_validation->set_rules('template_id', $this->lang->line('email_template'), 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {
                $lang_slug = $this->session->userdata('language_slug');
                $email_template = $this->db->get_where('email_template',array('entity_id'=>$this->input->post('template_id'),'language_slug'=>$lang_slug))->first_row();        
                //get System Option Data
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                $template_details = $this->dashboard_model->getEmailTempateDetails($this->input->post('template_id'));
                
                if($template_details->email_slug == 'promotional-email') {
                    $EmailBody = generateEmailBody($email_template->message,array());
                }
                $user_id = $this->input->post('user_id');
                if(!empty($user_id)){
                    foreach ($user_id as $key => $value) {
                        $userDetail = $this->dashboard_model->getUserEmail($value);
                        if($template_details->email_slug == 'account-verified') {
                            $arrayData = array('FirstName'=>$userDetail->first_name); //user name
                            $EmailBody = generateEmailBody($email_template->message,$arrayData);
                        } 
                        else if($template_details->email_slug == 'change-status-alert') {
                            if($userDetail->status == 1){ // user status
                                $status = 'activated';
                            } else {
                                $status = 'deactivated';
                            }
                            $arrayData = array('FirstName'=>$userDetail->first_name,'Status'=>$status);
                            $EmailBody = generateEmailBody($email_template->message,$arrayData);
                        } 
                        else if($template_details->email_slug == 'contact-us') {
                            $arrayData = array('FirstName'=>$userDetail->first_name);
                            $EmailBody = generateEmailBody($email_template->message,$arrayData);
                        } 
                        else if($template_details->email_slug == 'forgot-password') {
                            $verificationCode = random_string('alnum', 20).$userDetail->user_id.random_string('alnum', 5);
                            //for user type = 'User' and 'Driver' and 'Agent'
                            if($userDetail->user_type == 'User' || $userDetail->user_type == 'Driver' || $userDetail->user_type == 'Agent'){
                                $confirmationLink = '<a href="'.base_url().'user/reset/'.$verificationCode.'/'.$lang_slug.'" style="text-decoration:underline;">'.$this->lang->line('here').'</a>';
                            } //for user type = 'Admin', 'Master admin' or 'Branch admin'
                            else if($userDetail->user_type != 'User' && $userDetail->user_type != 'Driver' && $userDetail->user_type != 'Agent') {
                                $confirmationLink = '<a href="'.base_url().ADMIN_URL.'/home/newpassword/'.$verificationCode.'" style="text-decoration:underline;">here</a>'; 
                            }
                            $addata = array('email_verification_code'=>$verificationCode);
                            $this->common_model->updateData('users',$addata,'entity_id',$userDetail->user_id);
                            $arrayData = array('FirstName'=>$userDetail->first_name,'ForgotPasswordLink'=>$confirmationLink);
                            $EmailBody = generateEmailBody($email_template->message,$arrayData);
                        } 
                        $this->load->library('email');  
                        $config['charset'] = "utf-8";
                        $config['mailtype'] = "html";
                        $config['newline'] = "\r\n";      
                        $this->email->initialize($config);  
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                        $this->email->to($userDetail->email);      
                        $this->email->subject($email_template->subject);  
                        $this->email->message($EmailBody);            
                        $this->email->send();                        
                        redirect(base_url().ADMIN_URL.'/dashboard');             
                    }
                }
            }
        }
        if($this->input->post('submit_notification') == "Submit") { 
            $this->form_validation->set_rules('notification_title', $this->lang->line('notifi_title'), 'trim|required');
            if ($this->form_validation->run())
            {
                $addNotificationData = array(                   
                    'notification_title'=>utf8_encode($this->input->post('notification_title')),                    
                    'notification_description' =>utf8_encode($this->input->post('notification_description')),
                    'created_by'=>$this->session->userdata("AdminUserID")
                );                                            
                $NotificationID = $this->dashboard_model->addData('notifications',$addNotificationData);
                
                $UserIds = $this->input->post('user_id_noti');
                $NotificationDetail = array();
                if($this->input->post('save') == 1){
                    for ($u=0; $u < count($UserIds); $u++) { 
                        $NotificationDetail[] = array('notification_id' => $NotificationID, 'user_id'=>$UserIds[$u]);
                    }                
                    $this->dashboard_model->addRecordBatch('notifications_users',$NotificationDetail);
                }
                // START Push Notification
                $DeviceIds = $this->dashboard_model->getUserDevices($UserIds);               
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
                redirect(base_url().ADMIN_URL.'/dashboard');           
            }
        }
        $arr['restaurantCount'] = $this->dashboard_model->getRestaurantCount(); 
        $arr['user'] = $this->dashboard_model->gettotalAccount(); 
        $arr['totalOrder'] = $this->dashboard_model->getOrderCount();
        $arr['restaurant'] = $this->dashboard_model->restaurant();
        $arr['orders'] = $this->dashboard_model->getLastOrders();        
        $arr['count'] = $this->dashboard_model->getNotificationCount();
        $arr['template'] = $this->dashboard_model->getEmailTempate(); 
        if($arr['template']){
            foreach ($arr['template'] as $temp_key => $temp_value) {
                if($temp_value->email_slug != 'promotional-email' && $temp_value->email_slug != 'account-verified' && $temp_value->email_slug != 'change-status-alert' && $temp_value->email_slug != 'contact-us' && $temp_value->email_slug != 'forgot-password') {
                    unset($arr['template'][$temp_key]);
                }
            }
        }
        $arr['admin'] = $this->dashboard_model->getRestaurantAdmin();
        $arr['events'] = $this->dashboard_model->getLastEvents();
        $arr['coupons'] = $this->dashboard_model->getLastCoupons();
        //Graph related feature :: Start
        $arr['year'] = $this->dashboard_model->getYear(); 
        $arr['sale'] = $this->dashboard_model->getLifetimeSale(); 
        $arr['this_month'] = $this->dashboard_model->this_month(); 
        $arr['last_month'] = $this->dashboard_model->last_month();        
        //Graph related feature :: End
        $this->load->view(ADMIN_URL.'/dashboard',$arr);
    }
    public function ajaxNotification(){
        $count = $this->dashboard_model->ajaxNotification();
        if($count->delivery_pickup_count == null){
           $count->delivery_pickup_count=0; 
        }
        if($count->dinein_count == null){
           $count->dinein_count=0; 
        }
        if($count->order_count == null){
           $count->order_count=0; 
        }
        if($count->event_count == null){
           $count->event_count=0; 
        }
        if($count->tablebooking_count == null){
           $count->tablebooking_count=0; 
        }
        if($count->placed_order_count == null){
           $count->placed_order_count = 0; 
        }
        echo json_encode($count); 
    }
    public function change_delivery_pickup_order_view_status(){
        $this->dashboard_model->change_delivery_pickup_order_view_status();
    }
    public function change_dinein_order_view_status(){
        $this->dashboard_model->change_dinein_order_view_status();
    }
    public function ajaxEventNotification(){
        $count = $this->dashboard_model->ajaxEventNotification();
        echo json_encode($count);   
    }
    public function changeEventStatus(){
        $this->dashboard_model->changeEventStatus();
    }
    // Graph bar start
    public function fetch_data()
    {
        $var = explode(" - ", $this->input->post('daterange'));
        $from_date = str_replace('-', '/', $var[0]);
        $to_date = str_replace('-', '/', $var[1]);
        $startdate = date('Y-m-d', strtotime($from_date));
        $enddate = date('Y-m-d', strtotime($to_date));
        $result = $this->dashboard_model->fetch_chart_data($startdate, $enddate);
        $length = count($result);
        $output = array();
        for($i=0; $i < $length ; $i++) {
        $dateObj   = DateTime::createFromFormat('Y-m-d H:i:s', $result[$i]['day']);
        $monthName = $dateObj->format('m-d-Y');  
            $output[] = array(
                'day'  => $monthName,   
                'total' => $result[$i]['total']
            );
        }        
        echo json_encode($output);
    }
    // Graph bar end
    // user accepted the notification
    public function notification_accepted()
    {
      $allNotifications = $this->dashboard_model->getNotifications();
      if (!empty($allNotifications)) {
        foreach ($allNotifications as $key => $value) {
          $notificationData[] = array(                   
              'notification_id'=> $value['alert_id'],
              'UserID' => $this->session->userdata('AdminUserID'),
          ); 
        }
        setcookie("accepted_notifications", json_encode($notificationData), time() + (86400 * 30), "/"); 
        setcookie("user_notifications", $this->session->userdata('AdminUserID'), time() + (86400 * 30), "/");   
        echo 'success';  
      }
      else
      {
        echo 'fail';
      }
    }    
    public function autoCancelOrders() {
        //get all placed orders
        $response = array('reload_flag' => 0, 'status' => 0);
        $placed_orders = $this->dashboard_model->getPlacedOrders();
        $payment_methodarr = array('stripe','paypal','applepay');
        $is_cancelled = 0;
        if(!empty($placed_orders)) {
            foreach ($placed_orders as $key => $value) {
                $autocancelflag = 0;
                $current_time = date('Y-m-d H:i:s');
                $current_time = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($current_time)));

                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                $order_scheduled_date = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
                $order_slot_close_time = ($value->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;

                if($value->scheduled_date && $value->slot_close_time) {
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_close_time"));
                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($current_time);

                    if($scheduleddatetime <= $currentdatetime) {
                        $autocancelflag = 1;
                        $order_date = $combined_scheduled_date;
                    }
                } else if($value->scheduled_date=='' || $value->slot_close_time=='') {
                    $autocancelflag = 1;
                    $order_date = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($value->order_date)));
                }                
                
                if($autocancelflag == 1) {
                    //difference between current datetime and order datetime
                    $datetime_diff = $this->common_model->dateDifference($order_date, $current_time);
                    $diff_year = $datetime_diff['diff_year'];
                    $diff_month = $datetime_diff['diff_month'];
                    $diff_day = $datetime_diff['diff_day'];
                    $diff_hr = $datetime_diff['diff_hr'];
                    $diff_min = $datetime_diff['diff_min'];
                    //convert system option auto cancellation minutes to compare
                    $convert_mins = $this->common_model->convertMinutes('auto_cancel');
                    $compare_year = $convert_mins['compare_year'];
                    $compare_month = $convert_mins['compare_month'];
                    $compare_day = $convert_mins['compare_day'];
                    $compare_hour = $convert_mins['compare_hour'];
                    $compare_minute = $convert_mins['compare_minute'];
                    
                    if($diff_year > $compare_year || $diff_month > $compare_month || $diff_day > $compare_day || $diff_hr > $compare_hour || $diff_min > $compare_minute) {
                        //stripe/paypal refund amount :: start
                        if($value->refund_status != 'pending' && $value->tips_refund_status != 'pending')
                        {
                            if(($value->transaction_id != '' && in_array(strtolower($value->payment_option), $payment_methodarr) && $value->refund_status != 'refunded') || ($value->tips_transaction_id != '' && $value->tips_refund_status != 'refunded'))
                            {
                                $transaction_id = ($value->transaction_id != '' && ($value->refund_status == '' || strtolower($value->refund_status) == 'partial refunded')) ? $value->transaction_id : '';
                                $tips_transaction_id = ($value->tips_transaction_id != '' && $value->tips_refund_status == '') ? $value->tips_transaction_id:'';

                                $tip_payment_option = ($value->tip_payment_option!='' && $value->tip_payment_option!=null)?$value->tip_payment_option:'';
                                if($tip_payment_option=='' && $tips_transaction_id!='')
                                {
                                    $tip_payment_option = 'stripe';
                                }
                                $refund_reason = $this->lang->line('autocancel_refund_reason');
                                if(strtolower($value->payment_option)=='stripe' || strtolower($value->payment_option)=='applepay' || $tip_payment_option=='stripe')
                                {
                                    $response = $this->common_model->StripeRefund($transaction_id,$value->order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                                }
                                else if(strtolower($value->payment_option)=='paypal' || $tip_payment_option=='paypal')
                                {   
                                    $response = $this->common_model->PaypalRefund($transaction_id,$value->order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                                }
                                
                                //Mail send code Start
                                if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                                {
                                    $language_slug = $this->session->userdata('language_slug');
                                    $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                                    $this->common_model->refundMailsend($value->order_id,$value->user_id,0,'full',$updated_bytxt,$language_slug);
                                }                                
                                //Mail send code End
                                
                                if(in_array(strtolower($value->payment_option), $payment_methodarr))
                                {
                                    //Code for save updated by and date value 
                                    $update_array = array(
                                        'updated_by' => 0,
                                        'updated_date' => date('Y-m-d H:i:s')
                                    );
                                    $this->db->set($update_array)->where('entity_id',$value->order_id)->update('order_master');
                                    //Code for save updated by and date value
                                }
                            }
                        }
                        $this->common_model->save_user_log('Auto canceled an order - '.$value->order_id);
                        //stripe refund amount :: end
                        $this->db->set('order_status','cancel')->where('entity_id',$value->order_id)->update('order_master');
                        $cancel_reason = $this->lang->line('auto_cancelled_by_txt');
                        $this->db->set('cancel_reason',$cancel_reason)->where('entity_id',$value->order_id)->update('order_master');
                        $status_created_by = 'auto_cancelled';
                        $addData = array(
                            'order_id'=>$value->order_id,
                            'order_status'=>'cancel',
                            'time'=>date('Y-m-d H:i:s'),
                            'status_created_by'=>$status_created_by
                        );
                        $orderstatustbl_id = $this->dashboard_model->addData('order_status',$addData);

                        $user_id = $value->user_id;
                        if($user_id && $user_id > 0) {
                            //wallet changes :: start
                            //if order is cancelled both debit and credit should be removed from wallet history
                            $users_wallet = $this->common_model->getUsersWalletMoney($user_id);
                            $current_wallet = $users_wallet->wallet; //money in wallet
                            $credit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                            $credit_amount = $credit_walletDetails->amount;
                            $debit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                            $debit_amount = $debit_walletDetails->amount;
                            $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                            $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                            //delete order_id from wallet history and update users wallet
                            if(!empty($credit_amount) || !empty($debit_amount)){
                                $this->common_model->deletewallethistory($value->order_id); // delete by order id
                                $new_wallet = array(
                                    'wallet'=>$new_wallet_amount
                                );
                                $this->common_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                            }
                            //wallet changes :: end
                            $notification = array(
                                'order_id' => $value->order_id,
                                'user_id' => $user_id,
                                'notification_slug' => 'order_canceled',
                                'view_status' => 0,
                                'datetime' => date("Y-m-d H:i:s"),
                            );
                            $this->common_model->addData('user_order_notification',$notification);

                            //get langauge
                            $device = $this->common_model->getDevice($user_id);
                            if($device->notification == 1){
                                $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                                $this->lang->load('messages_lang', $languages->language_directory);
                                
                                //$message = sprintf($this->lang->line('order_canceled'),$value->order_id).'-'.$cancel_reason;
                                $message = sprintf($this->lang->line('order_autocancelled_notimsg'),$value->order_id);

                                $device_id = $device->device_id;
                                // Send Latest wallet balance               
                                $users_wallet = $this->common_model->getUsersWalletMoney($user_id);
                                $latest_wallet_balance = $users_wallet->wallet; 

                                $this->common_model->sendFCMRegistration($device_id,$message,'cancel',$value->restaurant_id,FCM_KEY,$value->order_delivery,'',$value->order_id,'',$latest_wallet_balance);
                            }
                            //send refund noti to user
                            if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                                $this->common_model->sendRefundNoti($value->order_id,$user_id,$value->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                            }
                        }
                        //send email and sms notification to user on order cancel
                        $langslugval = ($device->language_slug) ? $device->language_slug : '';
                        $useridval = ($user_id && $user_id > 0) ? $user_id : 0;
                        $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$value->order_id,'auto_cancelled');
                        if($value->agent_id){
                            $this->common_model->notificationToAgent($value->order_id, 'cancel');
                        }
                        $is_cancelled++;
                    }
                }
            }
        }
        if($is_cancelled > 0) {
            $response = array('reload_flag' => 1, 'status' => 1);
        }
        echo json_encode($response);
    }
    public function markDelayedOrders() { 
        $response = array('reload_flag' => 0, 'status' => 0);
        $get_all_orders = $this->dashboard_model->getOrderstoMarkDelayed();
        //echo "<pre>"; print_r($get_all_orders); exit;
        $delayed_count = 0;
        if(!empty($get_all_orders)) {
            foreach ($get_all_orders as $key => $val) {
                $markdelayedflag = 0;
                $compare_time_chk = ($val->check_status_time)?date('Y-m-d H:i:s',strtotime($val->check_status_time)):date('Y-m-d H:i:s');
                $compare_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($compare_time_chk)));

                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$val->scheduled_date $val->slot_close_time"));
                $order_scheduled_date = ($val->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
                $order_slot_close_time = ($val->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;

                if($val->scheduled_date && $val->slot_close_time) {                    
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_close_time"));
                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($compare_time_chk);

                    if($scheduleddatetime <= $currentdatetime) {                        
                        $markdelayedflag = 1;
                        $order_date_chk = $combined_scheduled_date;
                    }
                } elseif($val->scheduled_date=='' || $val->slot_close_time=='') {
                    $markdelayedflag = 1;                    
                    $order_date_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($val->order_date)));
                }

                if($markdelayedflag == 1) {
                    //difference between current datetime and order datetime
                    $datetime_diff = $this->common_model->dateDifference($order_date_chk , $compare_time_chk);
                    $diff_year = $datetime_diff['diff_year'];
                    $diff_month = $datetime_diff['diff_month'];
                    $diff_day = $datetime_diff['diff_day'];
                    $diff_hr = $datetime_diff['diff_hr'];
                    $diff_min = $datetime_diff['diff_min'];
                    //convert system option auto cancellation minutes to compare
                    $convert_mins = $this->common_model->convertMinutes('check_delayed');
                    $compare_year = $convert_mins['compare_year'];
                    $compare_month = $convert_mins['compare_month'];
                    $compare_day = $convert_mins['compare_day'];
                    $compare_hour = $convert_mins['compare_hour'];
                    $compare_minute = $convert_mins['compare_minute'];

                    if($diff_year > $compare_year || $diff_month > $compare_month || $diff_day > $compare_day || $diff_hr > $compare_hour || $diff_min > $compare_minute) {
                        $this->db->set('is_delayed',1)->where('entity_id',$val->order_id)->update('order_master');
                        $delayed_count++;
                    }
                }
            }
        }
        if($delayed_count > 0) {
            $response = array('reload_flag' => 1, 'status' => 1);
        }
        echo json_encode($response);
    }
    //update table booking view status
    public function changeTableBookingStatus(){
        $this->dashboard_model->changeTableBookingStatus();
    }
    public function refreshOrderData() {
        //dashboard statistics :: start
        $arr1['year'] = $this->dashboard_model->getYear(); 
        $arr1['sale'] = $this->dashboard_model->getLifetimeSale(); 
        $arr1['this_month'] = $this->dashboard_model->this_month(); 
        $arr1['last_month'] = $this->dashboard_model->last_month();
        $dashboard_statistics = $this->load->view(ADMIN_URL.'/dashboard_statistics',$arr1,true);
        //dashboard statistics :: end
        //dashboard order grid :: start
        $arr2['orders'] = $this->dashboard_model->getLastOrders();
        $dashboard_order_grid = $this->load->view(ADMIN_URL.'/dashboard_order_grid',$arr2,true);
        //dashboard order grid :: end
        //dashboard order count :: start
        $totalOrder = $this->dashboard_model->getOrderCount();
        $dashboard_order_count = '<div class="number dashboard_statnew">'.$totalOrder.'</div>
        <div class="desc">'.$this->lang->line('total_order').'</div>';
        //dashboard order count :: end

        $array_view = array(
            'dashboard_statistics'=>$dashboard_statistics,
            'dashboard_order_grid'=>$dashboard_order_grid,
            'dashboard_order_count' => $dashboard_order_count
        );
        echo json_encode($array_view); exit;
    }
}