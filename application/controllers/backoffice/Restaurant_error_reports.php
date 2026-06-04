<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Restaurant_error_reports extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->model(ADMIN_URL.'/restaurant_error_reports_model');  
    }
    // contact us page
    public function view(){
        $data['meta_title'] = $this->lang->line('restaurant_error_reports').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/restaurant_error_reports',$data);
    }
    public function ajaxview(){
        $searchTitleName = ($this->input->post('pageTitle') != '')?$this->input->post('pageTitle'):'';
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'report_topic',2=>'reporter_email',3=>'reporter_message',4=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $resReport = $this->restaurant_error_reports_model->getErrorReportList($searchTitleName,$sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $resReport['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($resReport['data'] as $key => $reportDetails) {
            $deletebtn = (in_array('restaurant_error_reports~ajaxdeleteReport',$this->session->userdata("UserAccessArray")))?'<button onclick="deleteErrorReport('.$reportDetails->entity_id.')" class="delete btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('delete').'"><i class="fa fa-trash"></i></button>':'';
            $deleteName = addslashes($reportDetails->report_topic);
            $records["aaData"][] = array(
                $nCount,
                utf8_decode($reportDetails->report_topic),                
                $reportDetails->reporter_email,   
                //utf8_decode($reportDetails->reporter_message),                
                $reportDetails->reporter_message,                
                '<button onclick="viewErrorReport('.$reportDetails->entity_id.')" class="delete btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('report').' '.$this->lang->line('detail').'"><i class="fa fa-eye"></i></button>'.$deletebtn,
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxdeleteReport(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->restaurant_error_reports_model->deleteRecord($entity_id);
        }
        $_SESSION['errorReport_MSG'] = $this->lang->line('success_delete');
    }
    public function ajaxviewReport(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
           $data = $this->restaurant_error_reports_model->ajaxviewReport($entity_id);
           $html ='<thead>
            <tr role="row" class="heading">
            <th>'. $this->lang->line('report').'</th>
            <th>'. $this->lang->line('email').'</th>
            <th>'. $this->lang->line('date').'</th>
            </tr></thead><tbody><tr>';
           foreach($data as $key => $value){
            $html.='<td>'.$value->report_topic.'</td>';
            $html.='<td>'.$value->reporter_email.'</td>';
            $html.='<td>'.date('d-m-Y g:i A', strtotime($this->common_model->getZonebaseDate($value->created_date))).'</td></tr>';
            $html.='<tr><td colspan="3"><strong>'.$this->lang->line('message').'</strong> : '.$value->reporter_message.'</td></tr>';
           }
           $html.='</tr></tbody>';
           echo json_encode($html);
        }
    }
}
?>