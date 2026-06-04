<?php
require_once  APPPATH.'/third_party/Twilio/vendor/autoload.php';
use Twilio\Rest\Client;
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_file extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(ADMIN_URL.'/common_model');              
    }
    //instock item function :: run at midnight
    public function instockitem()
    {
        $userData = array('stock' => 1);        
        $this->db->update('restaurant_menu_item',$userData);
        $this->db->affected_rows();     
    }
    //make restaurant online in given time :: run every minute
    public function onlinerestaurant()
    {
        $rest_Arr = $this->common_model->getOfflineRestaurant();
        if($rest_Arr && !empty($rest_Arr))
        {
            for($i=0;$i<count($rest_Arr);$i++)
            {
                $current_time = time();
                if($current_time>=$rest_Arr[$i]['offlinetime'])
                {
                    $updateData = array('enable_hours' => 1,'offlinetime'=>0);
                    $this->db->where('content_id',$rest_Arr[$i]['content_id']);     
                    $this->db->update('restaurant',$updateData);
                    $this->db->affected_rows(); 
                }               
            }           
        }
    }
    //make restaurant online in given time :: run every minute
    public function restaurant_orderschedule()
    {
        $rest_Arr = $this->common_model->getScheduledRestaurant();        
        if($rest_Arr && !empty($rest_Arr))
        {
            for($i=0;$i<count($rest_Arr);$i++)
            {
                $current_time = time();
                if($current_time>=$rest_Arr[$i]['schedule_time'])
                {
                    
                    $updateData = array('enable_schedule' => 0,'schedule_time'=>0,'schedule_mode'=>'0');
                    $this->db->where('content_id',$rest_Arr[$i]['content_id']);     
                    $this->db->update('restaurant',$updateData);
                    $this->db->affected_rows(); 
                }               
            }           
        }
    }
    //auto call order reminder to restaurant in given time :: run every minute
    public function autocallreminder()
    {
        $order_Arr = $this->common_model->getAllNeworders();
        if($order_Arr && !empty($order_Arr))
        {   
            //Code for find the auto call time :: Start
            $this->db->select('OptionValue,OptionSlug');            
            $this->db->where('OptionSlug','automated_call_timer');            
            $auotarr = $this->db->get('system_option')->first_row();
            $automated_call_timer = 10;
            if($auotarr && !empty($auotarr))
            {
                $automated_call_timer = $auotarr->OptionValue;
            }
            $automated_call_timer1 = $automated_call_timer+1;
            //Code for find the auto call time :: End
            
            for($i=0;$i<count($order_Arr);$i++)
            {
                //Code for time calculation :: Start
                $order_date = $order_Arr[$i]['order_date'];
                $to_time = strtotime($order_date);
                $from_time = strtotime(date('Y-m-d H:i:s'));
                $time_diff =  round(abs($to_time - $from_time) / 60,2); 
                //Code for time calculation :: End
                                
                if($time_diff>=$automated_call_timer && $time_diff<$automated_call_timer1)
                {
                    if($order_Arr[$i]['phone_number']!=null && $order_Arr[$i]['phone_number']!='')
                    {
                        $rphone_codeval = $order_Arr[$i]['phone_code'];
                        if($rphone_codeval!='') {
                            $rphone_codeval= str_replace("+","",$rphone_codeval);
                            $rphone_codeval = '+'.$rphone_codeval;                        
                        }
                        $rest_mobileno = $rphone_codeval.$order_Arr[$i]['phone_number'];
                        $res_name = $order_Arr[$i]['res_name'];
                        $user_name = $order_Arr[$i]['first_name']." ".$order_Arr[$i]['last_name'];

                        // $autocall_message = $this->lang->line('autocall_message');
                        // $autocall_message = sprintf($autocall_message, $res_name,$user_name);
                        $autocall_message = $this->lang->line('autocall_newmessage');
                        
                        //$rest_mobileno = 91**********;
                        //Code for stripe call :: Start
                        $sid = TWILIO_SID;
                        $token = TWILIO_AUTH_TOKEN;
                        $twilio_number = TWILIO_PHN_NO;
                        $twilio = new Client($sid, $token);

                        $twilio->account->calls->create(
                            $rest_mobileno,
                            $twilio_number,
                            array(
                                "twiml" => "<Response><Say>".$autocall_message."</Say></Response>"
                            )
                        );
                        //Code for stripe call :: End
                        //print($twilio);
                        //echo $time_diff."==".$automated_call_timer."==".$automated_call_timer1; exit;
                    }                   
                }
            }           
        }
    }
    //auto cancel orders :: run every min
    public function autoCancelOrders() {
        $this->db->select("order_master.order_delivery,order_master.order_status as ostatus,order_master.payment_option,order_master.entity_id as order_id,order_master.user_id,order_master.agent_id,order_master.restaurant_id,order_master.order_date,order_master.transaction_id,order_master.refund_status,tips.tips_transaction_id,tips.refund_status as tips_refund_status,order_master.scheduled_date,order_master.slot_open_time,order_master.slot_close_time, tips.payment_option as tip_payment_option");
        $this->db->join('tips','order_master.entity_id = tips.order_id AND tips.amount > 0','left');
        $this->db->where('order_master.order_status', 'placed');
        $this->db->group_by('order_master.entity_id');
        $result = $this->db->get('order_master')->result();
        $is_cancelled = 0;
        $payment_methodarr = array('stripe','paypal','applepay');
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $autocancelflag = 0;
                $current_time = date('Y-m-d H:i:s');
                //$current_time = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($current_time)));

                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                // $order_scheduled_date = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
                // $order_slot_close_time = ($value->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;
                $order_scheduled_date = ($value->scheduled_date) ? date('Y-m-d', strtotime($scheduledorderclosetime)) : NULL;
                $order_slot_close_time = ($value->slot_close_time) ? date('H:i:s', strtotime($scheduledorderclosetime)) : NULL;

                if($value->scheduled_date && $value->slot_close_time) {
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_close_time"));
                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($current_time);

                    if($scheduleddatetime <= $currentdatetime) {
                        $autocancelflag = 1;
                        $order_date = $combined_scheduled_date;
                    }
                } else if(!($value->scheduled_date && $value->slot_close_time)) {
                    $autocancelflag = 1;
                    //$order_date = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($value->order_date)));
                    $order_date = date('Y-m-d H:i:s', strtotime($value->order_date));
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
                        //stripe refund amount :: start
                        if($value->refund_status != 'pending' && $value->tips_refund_status != 'pending') {
                            if(($value->transaction_id != '' && in_array(strtolower($value->payment_option), $payment_methodarr) && $value->refund_status != 'refunded') || ($value->tips_transaction_id != '' && $value->tips_refund_status != 'refunded')) {
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
                                    $language_slug = 'en';
                                    $updated_bytxt = $this->lang->line('auto_cancelled_by_txt');
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
                        $orderstatustbl_id = $this->common_model->addData('order_status',$addData);

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
        if($is_cancelled > 0){
            echo '1';
        }  else {
            echo '0';
        }
    }
    //delete user logs :: run once every month
    public function deletelogs_olderthan_twomonths() {
        $this->db->select("user_log_id");
        $this->db->where('user_log.created_date <', date_format(date_create(date('Y-m-01 00:00:00')." -2 months"),'Y-m-d H:i:s'));
        $this->db->group_by('user_log_id');
        $result = $this->db->get('user_log')->result();
        if(!empty($result)) {
            $resultarr = array_column($result, 'user_log_id');
            $this->db->where_in('user_log_id', $resultarr);
            $this->db->delete('user_log');
        }
    }    
    //event booking reminder :: run every minute :: NA
    public function EventBookingReminder()
    {
        echo "Please contact to admin, Need to set in Cron when we go live"; exit;
        $currentDateTime = date("Y-m-d H:i:s");
        $newDateTime = date("Y-m-d H:i:s", strtotime("+2 hours"));      
        $result = $this->common_model->EventBookingReminderNoti();
        foreach ($result as $key => $value)
        {
            //Notification code require
            $hourdiff = round((strtotime($value['booking_date'] ) - strtotime($currentDateTime))/3600, 1);
            if($hourdiff <= 2 && $hourdiff >= 1.5)
            {
                $fields = array();  
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value['lang_slug']))->first_row();  
                $this->lang->load('messages_lang', $languages->language_directory);        
                if($value['udevice_id'] && $value['unoti'] == 1){
                    #prep the bundle
                    $fields = array();
                    $event_msg = $this->lang->line('reminder');   
                    $value['booking_date'] = $this->common_model->getZonebaseDate($value['booking_date']);
                    $time = date('g:i A',strtotime($value['booking_date']));         
                    $message =  sprintf($event_msg,$time,$value['rname'],$value['address'],$value['no_of_people']);
                    $fields['to'] = $value['udevice_id']; // only one user to send push notification
                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                    $fields['notification']['title'] = $this->lang->line('customer_app_name');
                    $fields['data'] = array ('screenType'=>'event');
                   
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
                    $res_notification = curl_exec($ch);
                    curl_close($ch);  
                }
                $lang_slug = $this->session->userdata('language_slug');
                $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'event-booking-reminder','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();      
                //get System Option Data
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                $arrayData = array('FirstName'=>$value['fname'],'time'=>$time,'Restaurant_name'=>$value['rname'],'Address'=>$value['address'],'peoples'=>$value['no_of_people']);
                $EmailBody = generateEmailBody($Emaildata->message,$arrayData);
                if(!empty($EmailBody)){
                    $this->load->library('email');  
                    $config['charset'] = "utf-8";
                    $config['mailtype'] = "html";
                    $config['newline'] = "\r\n";      
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to($value['uemail']);      
                    $this->email->subject($Emaildata->subject);  
                    $this->email->message($EmailBody);            
                    $this->email->send();
                }
            }
        } 
        exit;       
    }
    //send scheduled order notifications to internal drivers :: NA
    public function scheduledOrdersNoti() {
        $status_arr = array('delivered','cancel','complete','rejected');
        $this->db->select("order_master.entity_id as order_id,order_master.scheduled_date,order_master.slot_open_time,order_master.user_id as orders_user_id,order_master.order_delivery,order_master.payment_option,restaurant.entity_id as restaurant_id,restaurant.content_id");
        $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
        $this->db->where_not_in('order_master.order_status',$status_arr);
        $this->db->where('order_master.scheduled_date !=', NULL);
        $this->db->where('order_master.slot_open_time !=', NULL);
        $this->db->like('order_master.scheduled_date', date('Y-m-d'));
        $this->db->where('order_master.order_delivery', 'Delivery');
        $this->db->where('order_master.delivery_method','internal_drivers');
        $this->db->where('order_master.status', 1);
        $this->db->group_by('order_master.entity_id');
        $orders = $this->db->get('order_master')->result();
        $driver_count = 0;
        if(!empty($orders)) {
            foreach ($orders as $orderkey => $ordervalue) {
                $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$ordervalue->scheduled_date $ordervalue->slot_open_time"));
                $order_scheduled_date = ($ordervalue->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                $order_slot_open_time = ($ordervalue->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                $current_time_chk = date('Y-m-d H:i:s');
                $current_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($current_time_chk)));

                if($order_scheduled_date && $order_slot_open_time && date('Y-m-d',strtotime($order_scheduled_date)) == date('Y-m-d',strtotime($current_time_chk))) {
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_open_time"));

                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($current_time_chk);

                    if($scheduleddatetime <= $currentdatetime) {
                        $restaurant_content_id = $ordervalue->content_id;
                        // drivers assigned to multiple restaurant - start
                        $this->db->select('driver_id');
                        $this->db->where('restaurant_content_id',$restaurant_content_id);
                        $driver = $this->db->get('restaurant_driver_map')->result_array();
                        // drivers assigned to multiple restaurant - end
                        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
                        //driver tracking join last entry changes :: start
                        $this->db->join('(select max(traking_id) as max_id, driver_id 
                        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
                        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
                        //driver tracking join last entry changes :: end
                        $this->db->where('users.user_type','Driver');
                        $this->db->where('driver_traking_map.latitude!=','');
                        $this->db->where('driver_traking_map.longitude!=','');
                        $this->db->where('users.status',1);
                        $this->db->where('users.availability_status',1);
                        $this->db->where('users.active',1);
                        if(!empty($driver)){
                            $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
                        }
                        $this->db->group_by('driver_traking_map.driver_id');
                        $this->db->order_by('driver_traking_map.created_date','desc');
                        $detail = $this->db->get('users')->result();
                        $flag = false;
                        if(!empty($detail)) {
                            if($ordervalue->orders_user_id > 0) {
                                $this->db->select('u_address.latitude, u_address.longitude');
                                $this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
                                $this->db->where('order_master.entity_id',$ordervalue->order_id);
                                $user_details = $this->db->get('order_master')->first_row();
                                $user_lat = $user_details->latitude;
                                $user_long = $user_details->longitude;
                            } else {
                                $this->db->select('user_detail');
                                $this->db->where('order_id',$ordervalue->order_id);
                                $user_details = $this->db->get('order_detail')->first_row();
                                $user_lat_long = unserialize($user_details->user_detail);
                                $user_lat = $user_lat_long['latitude'];
                                $user_long = $user_lat_long['longitude'];
                            }
                            // Begin::Finding Distance between restaurant to user
                            $this->db->select("(3959 * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
                            $this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
                            $this->db->where('order_master.entity_id',$ordervalue->order_id);
                            $user_to_restaurant_distance = $this->db->get('order_master')->first_row();
                            // End::Finding Distance between restaurant to user
                            foreach ($detail as $key => $value) {
                                $longitude = $value->longitude;
                                $latitude = $value->latitude;
                                $this->db->select("(3959 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                                $this->db->where('restaurant.entity_id',$ordervalue->restaurant_id);
                                $this->db->having('distance <',DRIVER_NEAR_KM);
                                $result = $this->db->get('restaurant')->result();
                                if(!empty($result)){
                                    if($value->device_id){
                                        //get langauge
                                        $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                                        $this->lang->load('messages_lang', $languages->language_directory); 
                                        $flag = true;
                                        $array = array(
                                            'order_id'=>$ordervalue->order_id,
                                            'driver_id'=>$value->driver_id,
                                            'date'=>date('Y-m-d H:i:s'),
                                            'distance'=>$user_to_restaurant_distance->distance
                                        );
                                        $id = $this->addData('order_driver_map',$array);
                                        #prep the bundle
                                        $fields = array();
                                        $message = sprintf($this->lang->line('push_new_order'),$ordervalue->order_id);
                                        $fields['to'] = $value->device_id; // only one user to send push notification
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
                                        $curl_result = curl_exec($ch);
                                        curl_close($ch);
                                        $driver_count++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($driver_count > 0){
            echo '1';
        }  else {
            echo '0';
        }
    }
}
?>