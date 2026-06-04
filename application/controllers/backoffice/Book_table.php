<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Book_table extends CI_Controller {
    public $controller_name = 'book_table';
    public $prefix = '_book_table'; 
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/book_table_model');
    }
    // view table booking
    public function view(){
        if(in_array('book_table~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('table_bookings').' | '.$this->lang->line('site_title');
            $this->load->view(ADMIN_URL.'/book_table',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(1=>'user_name','2'=>'res.name','3'=>'no_of_people','4'=>'booking_date','5'=>'booking_status'); //'5'=>'amount','6'=>'payment_status',
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->book_table_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;

        $default_currency = get_default_system_currency();
        foreach ($grid_data['data'] as $key => $val) {
            if(!empty($default_currency)){
                $currency_symbol = $default_currency;
            }else{
                $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            }
            //$table_paid_btn = (!empty($val->payment_status) && $val->payment_status != 'paid' && $val->payment_status != 'cancel') ? '<button title="'.$this->lang->line('add').' '.$this->lang->line('amount').'" onclick="addAmount('.$val->entity_id.')" class="delete btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-dollar"></i></button>' : '';
            $disabled = ($val->booking_status == 'cancelled')?'disabled':'';
            $bookingStatus = "'".$val->booking_status."'";

            $deleteName = '';
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_booking')),$deleteName)."'";
            $booking_time = ($val->start_time && $val->end_time) ? '<br> ('.$this->common_model->getZonebaseTime($val->start_time).' - '.$this->common_model->getZonebaseTime($val->end_time).')':'';
            $action_btn = (in_array('book_table~updateTableStatus',$this->session->userdata("UserAccessArray"))) ? '<button onclick="updateStatus(\''.$val->entity_id.'\','.$bookingStatus.')" '.$disabled.'  class="delete btn btn-sm danger-btn theme-btn margin-bottom" title="'.$this->lang->line('click_change_status').'"><i class="fa fa-edit"></i></button>' : '';
            $action_btn .= (in_array('book_table~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail(\''.$val->entity_id.'\','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->user_name,
                $val->rname,
                $val->no_of_people,
                ($val->booking_date)?date('Y-m-d',strtotime($val->booking_date)).$booking_time:'',
                //($val->amount)?$val->amount:'',
                //($val->payment_status)?ucfirst($val->payment_status):'-',
                ($val->booking_status)?ucfirst($val->booking_status):'-',
                $action_btn
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->book_table_model->ajaxDelete('table_booking',$entity_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    //add status
    public function updateTableStatus(){
        $entity_id = ($this->input->post('table_entity_id'))?$this->input->post('table_entity_id'):''; 
        if($entity_id && $this->input->post('table_booking_status') != ''){
            $cancel_reason = ($this->input->post('table_booking_status') == 'cancelled')?$this->input->post('cancel_reason'):NULL;
            $update_status = array(
                'booking_status' => $this->input->post('table_booking_status'),
                'cancel_reason' => $cancel_reason
            );
            $data = $this->book_table_model->updateData($update_status,'table_booking','entity_id',$entity_id); 
            //send notification to user on web and app
            if($this->input->post('table_booking_status') == 'cancelled'){
                $table_booking_status = 'table_cancelled';
                $table_status_val = 'table_cancelled';
            }
            if($this->input->post('table_booking_status') == 'awaiting'){
                $table_booking_status = 'table_awaiting';
                $table_status_val = 'table_awaiting';
            }
            if($this->input->post('table_booking_status') == 'confirmed'){
                $table_booking_status = 'table_confirmed';
                $table_status_val ='table_confirmed' ;
            }
            $table_booking_detail = $this->common_model->getSingleRow('table_booking','entity_id',$entity_id);
            $notification = array(
                'table_id' => $entity_id,
                'user_id' => $table_booking_detail->user_id,
                'notification_slug' => $table_booking_status,
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->common_model->addData('user_table_notifications',$notification);
            // load language for mobile notification
            $userData = $this->common_model->getSingleRow('users','entity_id',$table_booking_detail->user_id);
            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$userData->language_slug))->first_row();
            $this->lang->load('messages_lang', $languages->language_directory);
            $message = $this->lang->line($table_status_val);
            if(!empty($userData) && $userData->device_id && $userData->notification == 1){
                #prep the bundle
                $fields = array();            
                $fields['to'] = $userData->device_id; // only one user to send push notification
                $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                $fields['notification']['title'] = $this->lang->line('customer_app_name');
                $fields['data'] = array ('screenType'=>'table');
               
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
        }
    }
    //add amount
    /*public function addAmount(){
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
        if($this->form_validation->run())
        {
            $add_data = array(
                'amount' =>$this->input->post('amount'),
            );
            $data = $this->book_table_model->updateData($add_data,'table_booking','entity_id',$this->input->post('entity_id')); 
            echo json_encode($data);
        }  
    }*/
}
?>