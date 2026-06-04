<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Event extends CI_Controller {
    public $controller_name = 'event';
    public $prefix = '_event'; 
    public function __construct() {
        parent::__construct();        
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/event_model');
    }
    // view event
    public function view(){        
        if(in_array('event~view',$this->session->userdata("UserAccessArray"))) {
            if($this->uri->segment('4')=='event_id') {
                $event_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):0;
            } else {
                $event_id = 0;
            }
            $data['event_id'] = $event_id;
            $data['meta_title'] = $this->lang->line('admin_event_booking').' | '.$this->lang->line('site_title');
            $data['restaurant'] = $this->event_model->getRestaurantList();
            //event count
            $this->db->select('event.entity_id');
            $this->db->join('event_detail','event.entity_id = event_detail.event_id');
            $this->db->join('restaurant as res','event.restaurant_id = res.content_id');
            $this->db->join('users as u','event.user_id = u.entity_id');
            $this->db->where('res.status',1);
            $this->db->where('res.language_slug',$this->session->userdata('language_slug')); 
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('res.status',1);
            $data['event_count'] = $this->db->get('event')->num_rows();
            $this->load->view(ADMIN_URL.'/event',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxview() {
        $event_id = ($this->uri->segment('5'))?$this->uri->segment('5'):0;
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(1=>'u.first_name','2'=>'res.name','3'=>'no_of_people','4'=>'booking_date','6'=>'amount','7'=>'event_status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->event_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$event_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        //$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $default_currency = get_default_system_currency();
        foreach ($grid_data['data'] as $key => $val) {
            if(!empty($default_currency)){
                $currency_symbol = $default_currency;
            }else{
                $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            }
            $coupon_type = ($val->coupon_type)?"'".$val->coupon_type."'":'0';
            $tax_rate = ($val->tax_rate)?$val->tax_rate:0;
            $tax_type = ($val->tax_type)? "'".$val->tax_type."'":0;
            $coupon_amount = ($val->coupon_amount)?$val->coupon_amount:'0';
            $entId = $val->entity_id;
            $disabled = ($val->event_status == 'cancel')?'disabled':'';
            $event_paid_btn = (!empty($val->event_status) && $val->event_status != 'paid' && $val->event_status != 'cancel' && in_array('event~addAmount',$this->session->userdata("UserAccessArray"))) ? '<button title="'.$this->lang->line('add').' '.$this->lang->line('amount').'" onclick="addAmount('.$entId.','.$tax_rate.','.$coupon_amount.','.$tax_type.','.$coupon_type.')" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-dollar"></i></button>' : '';
            $eventStatus = "'".$val->event_status."'";
            //$doc = "'".$val->invoice."'";
            if(!empty($val->package_detail)){
                $package_detail = @unserialize($val->package_detail);
                $package_price = (!empty($package_detail['package_price']))? currency_symboldisplay(number_format_unchanged_precision($package_detail['package_price'],@$currency_symbol->currency_code),@$currency_symbol->currency_symbol) :'';  
                $package_name = (!empty($package_detail['package_name']))?$package_detail['package_name']:'';
            } else {
                $package_price = "";
                $package_name = "";
            }
            $deleteName = '';
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_event')),$deleteName)."'";
            $additional_req_btn = ($val->additional_request && in_array('event~view',$this->session->userdata("UserAccessArray"))) ? '<button onclick="viewAdditionalRequest('.$val->entity_id.')"  title="'.$this->lang->line('additional_comment').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-eye"></i></button>':'';

            $updateEventStatusBtn = (in_array('event~updateEventStatus',$this->session->userdata("UserAccessArray"))) ? '<button onclick="updateStatus('.$val->entity_id.','.$eventStatus.')" '.$disabled.'  class="delete btn btn-sm default-btn margin-bottom red" title="'.$this->lang->line('click_change_status').'"><i class="fa fa-edit"></i></button>' : '';
            $deleteEventBtn = (in_array('event~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->entity_id.','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->fname.' '.$val->lname,
                $val->rname,
                $val->no_of_people,
                $this->common_model->getZonebaseDateMDY($val->booking_date),
                ($package_name)?$package_name:'',
                //($package_price) ? $package_price : (($val->amount)?currency_symboldisplay(number_format_unchanged_precision($val->amount,@$currency_symbol->currency_code),@$currency_symbol->currency_symbol):'-'),
                ($val->amount) ? currency_symboldisplay(number_format_unchanged_precision($val->amount,@$currency_symbol->currency_code),@$currency_symbol->currency_symbol) : (($package_price)?$package_price:'-'),
                ($val->event_status)?$val->event_status:'-',
                //($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                $additional_req_btn.$event_paid_btn.$updateEventStatusBtn.$deleteEventBtn
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //add status
    public function updateEventStatus(){
        $entity_id = ($this->input->post('event_entity_id'))?$this->input->post('event_entity_id'):''; 
        if($entity_id && $this->input->post('event_status') != ''){
            $cancel_reason = ($this->input->post('event_status') == 'cancel')?$this->input->post('cancel_reason'):NULL;
            $update_status = array(
                'event_status' => $this->input->post('event_status'),
                'cancel_reason' => $cancel_reason
            );
            //send notification to user on web and app
                if($this->input->post('event_status') == 'cancel'){
                    $event_status = 'event_cancelled';
                    $event_status_val = 'event_cancelled';
                }
                if($this->input->post('event_status') == 'paid'){
                    $event_status = 'event_paid';
                    $event_status_val ='event_paid' ;
                }
                if($this->input->post('event_status') == 'pending'){
                    $event_status = 'event_pending';
                    $event_status_val ='event_pending_noti' ;
                }
                if($this->input->post('event_status') == 'onGoing'){
                    $event_status = 'event_ongoing';
                    $event_status_val = 'event_ongoing_noti';
                }
                $event_detail = $this->common_model->getSingleRow('event','entity_id',$entity_id);
                $notification = array(
                    'event_id' => $entity_id,
                    'user_id' => $event_detail->user_id,
                    'notification_slug' => $event_status,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_event_notifications',$notification);
                // load language for mobile notification
                $userData = $this->common_model->getSingleRow('users','entity_id',$event_detail->user_id);
                $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$userData->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                $message = $this->lang->line($event_status_val);
                if(!empty($userData) && $userData->device_id && $userData->notification == 1){
                    #prep the bundle
                    $fields = array();            
                    //$message =  sprintf($this->lang->line('event_cancelled'), $event_detail->booking_date); 
                    $fields['to'] = $userData->device_id; // only one user to send push notification
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
                    $result = curl_exec($ch);
                    curl_close($ch);  
                }
            
            $data = $this->event_model->updateData($update_status,'event','entity_id',$entity_id); 
        }
    }
    // method to change status
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->event_model->UpdatedStatus('event',$entity_id,$this->input->post('status'));
        }
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $data['event_records'] = $this->event_model->getEditDetail($entity_id);
        if(!empty($data['event_records']->invoice)) {
            @unlink(FCPATH.'uploads/'.$data['event_records']->invoice);
        }
        $this->event_model->ajaxDelete('event',$entity_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    //get restaurant
    public function getRestuarantDetail(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $result = $this->event_model->getRestuarantDetail($entity_id);
        echo json_encode($result);
    }
    //add amount
    public function addAmount(){
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
        //$this->form_validation->set_rules('event_status', $this->lang->line('status'), 'trim|required');
        if($this->form_validation->run())
        {
            $add_data = array(
                'subtotal'     =>$this->input->post('subtotal'),
                'coupon_amount'=>$this->input->post('coupon_amount'),
                'amount'       =>$this->input->post('amount'),
                //'event_status' =>$this->input->post('event_status'),
            );
            $data = $this->event_model->updateData($add_data,'event','entity_id',$this->input->post('entity_id')); 
            echo json_encode($data);
        }  
    }
    public function generate_report(){
        $slug = $this->session->userdata('language_slug');
        $languages = $this->common_model->getFirstLanguages($slug);
        $this->lang->load('messages_lang', $languages->language_directory);
        $restaurant_id = $this->input->post('restaurant_id');
        $booking_date = $this->input->post('booking_date_export');
        $explode_date = explode(' - ',$booking_date);
        $from_date_trim = trim(str_replace('-', '/', $explode_date[0]));
        $to_date_trim = trim(str_replace('-', '/', $explode_date[1]));
        $from_date_array = explode('/', $from_date_trim);
        $to_date_array = explode('/', $to_date_trim);
        $from_date =  $from_date_array[2].'-'.$from_date_array[0].'-'.$from_date_array[1];
        $to_date =  $to_date_array[2].'-'.$to_date_array[0].'-'.$to_date_array[1];
        $results = $this->event_model->generate_report($restaurant_id,$from_date,$to_date);
        if(!empty($results)){
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array(
                $this->lang->line('restaurant'),
                $this->lang->line('customer'),
                $this->lang->line('no_of_people'),
                $this->lang->line('event_date'),
                $this->lang->line('package'),
                $this->lang->line('amount'),
                $this->lang->line('payment_status')
            );
            for($h=0,$c='A'; $h<count($headers); $h++,$c++)
            {
                $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
            }
            $row = 2;
            //get System Option Data
            /*$this->db->select('OptionValue');
            $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
            $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
            for($r=0; $r<count($results); $r++){
                $currency_symbol = $this->common_model->getCurrencySymbol($results[$r]->currency_id);
                if(!empty($results[$r]->package_detail)){
                    $package_detail = @unserialize($results[$r]->package_detail);
                    $package_price = (!empty($package_detail['package_price']))? currency_symboldisplay(number_format_unchanged_precision($package_detail['package_price'],@$currency_symbol->currency_code),@$currency_symbol->currency_symbol) :'';  
                    $package_name = (!empty($package_detail['package_name']))?$package_detail['package_name']:'';
                } else {
                    $package_price = "";
                    $package_name = "";
                }
                $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->name);
                $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('C'.$row, $results[$r]->no_of_people);
                $this->excel->getActiveSheet()->setCellValue('D'.$row, $this->common_model->datetimeFormat($results[$r]->booking_date));
                $this->excel->getActiveSheet()->setCellValue('E'.$row, $package_name);
                $this->excel->getActiveSheet()->setCellValue('F'.$row, ($package_price) ? $package_price : '-');
                $this->excel->getActiveSheet()->setCellValue('G'.$row, $results[$r]->event_status ?? '-');
            $row++;
            }
            $filename = 'event-report-export.xlsx'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache   
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $writer = $this->excel->print_sheet($this->excel);
            $writer->save('php://output');  
            
            //force user to download the Excel file without writing it to server's HD
            exit;
        }else{
            // $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            $_SESSION['not_found'] = $this->lang->line('not_found');
            redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
        }
    }
    public function EventBookingscript()
    {
        echo "Please contact to admin"; exit;
        $this->db->select('entity_id,restaurant_id,package_id');        
        $resdata = $this->db->get('event')->result();

        if($resdata && !empty($resdata))
        {
            for($i=0;$i<count($resdata);$i++)
            {
                $this->db->select('entity_id,content_id');
                $this->db->where('entity_id',$resdata[$i]->restaurant_id);        
                $resultcont = $this->db->get('restaurant')->first_row();
                if($resultcont && !empty($resultcont))
                {
                    $updateData = array('restaurant_id'=>$resultcont->content_id); 
                    $this->event_model->updateData($updateData,'event','entity_id',$resdata[$i]->entity_id);
                }  
                $this->db->select('entity_id,content_id');
                $this->db->where('entity_id',$resdata[$i]->package_id);        
                $resultpack = $this->db->get('restaurant_package')->first_row();
                if($resultpack && !empty($resultpack))
                {
                    $updateData1 = array('package_id'=>$resultpack->content_id); 
                    $this->event_model->updateData($updateData1,'event','entity_id',$resdata[$i]->entity_id);
                }                
            }
        }
    }
    //get additional_comment
    public function viewAdditionalRequest()
    {
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):''; 
        if($entity_id)
        {
            $data['additional_comment'] = $this->event_model->viewAdditionalRequest($entity_id);
            $data['entity_id'] = $entity_id;
            $this->load->view(ADMIN_URL.'/view_additional_request',$data);
        }
    }
}
?>