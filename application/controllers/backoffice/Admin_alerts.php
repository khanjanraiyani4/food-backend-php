<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_alerts extends CI_Controller {
	public $module_name = "Admin alerts";
	public $controller_name = "admin_alerts";
	public $table_name = "admin_alerts";
    public $list_viewfile = ADMIN_URL."/admin_alerts";
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('AdminUserType') != "MasterAdmin") {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');    
        $this->load->model(ADMIN_URL.'/admin_alerts_model');     
    }
    public function index() {
        $data['MetaTitle'] = $this->lang->line('site_title');
        $this->load->view($this->list_viewfile,$data);
    }
    public function add() 
    {   
        $this->form_validation->set_rules('message', 'Notification Message', 'trim|required');
        $this->form_validation->set_rules('from_date', 'From Date', 'trim|required');
        $this->form_validation->set_rules('to_date', 'To Date', 'trim|required');
        $this->form_validation->set_rules('button_label', 'Button Label', 'trim|required');
        //check form validation using codeigniter 
        if ($this->form_validation->run())
        {	
        	if (!empty($this->input->post('alert_id'))) {
	            $updateData = array(                   
	                'message'=>$this->input->post('message'),
	                'from_date' =>date('Y-m-d',strtotime($this->input->post('from_date'))),
	                'to_date' => date('Y-m-d',strtotime($this->input->post('to_date'))),
	                'button_label' =>$this->input->post('button_label'),
	                'created_by'=>$this->session->userdata("AdminUserID")
	            );     
                $this->common_model->updateData($this->table_name,$updateData,'alert_id',$this->input->post('alert_id'));
	            // $this->session->set_flashdata('PageMSG', $this->lang->line('success_update'));
                $_SESSION['PageMSG'] = $this->lang->line('success_update');
	            echo 'success'; 
        	}
        	else
        	{
	            $addData = array(                   
	                'message'=>$this->input->post('message'),
	                'from_date' =>date('Y-m-d',strtotime($this->input->post('from_date'))),
	                'to_date' => date('Y-m-d',strtotime($this->input->post('to_date'))),
	                'button_label' =>$this->input->post('button_label'),
	                'created_by'=>$this->session->userdata("AdminUserID")
	            );      
	            $this->common_model->addData($this->table_name,$addData);
	            // $this->session->set_flashdata('PageMSG', $this->lang->line('success_add'));
                $_SESSION['PageMSG'] = $this->lang->line('success_add');
	            echo 'success';  
        	}           
        }
        else
        {
            echo validation_errors();
        }
    }
    public function edit()
    {
    	$alert_id = $this->input->post('alert_id');
    	$editNotificationData = '';
    	if (!empty($alert_id)) {
        	$editNotificationData = $this->common_model->getSingleRow('admin_alerts','alert_id',$alert_id);
        	if (!empty($editNotificationData)) {
        		$editNotificationData->from_date = date('d-M-Y',strtotime($editNotificationData->from_date));
        		$editNotificationData->to_date = date('d-M-Y',strtotime($editNotificationData->to_date));
        	}
    	}
    	echo json_encode($editNotificationData);
    }
    public function ajaxview() {
        $searchTitleName = ($this->input->post('pageTitle') != '')?$this->input->post('pageTitle'):'';
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'message',2=>'from_date',3=>'to_date',4=>'button_label',5=>'created_at');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }        
        //Get Recored from model
        $data = $this->admin_alerts_model->getPageList($searchTitleName,$sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($data['data'] as $key => $details) {
            $records["aaData"][] = array(
                $nCount,
                $details->message,
                date('d-M-Y', strtotime($details->from_date)),
                date('d-M-Y', strtotime($details->to_date)),
                $details->button_label,
                '<a class="btn default-btn btn-sm" data-toggle="modal" onclick="editNotification('.$details->alert_id.')"><i class="fa fa-eye"></i> Edit</a>'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
	
}