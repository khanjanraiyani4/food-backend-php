<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Contact_inquiries extends CI_Controller {
	public $controller_name = 'contact_inquiries';
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/contact_inquiries_model');
	}
	//view data
	public function view() {
		//if(in_array('contact_inquiries~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('contact_inquiries').' | '.$this->lang->line('site_title');
			//user_log count
			$this->db->select('contact_id');
			$this->db->group_by('contact_id');
			$data['contact_inquiries_count'] = $this->db->get('contactus_detail')->num_rows();
			$this->load->view(ADMIN_URL.'/contact_inquiries',$data);
		/*} else {
			redirect(base_url().ADMIN_URL);
		}*/
	}
	//ajax view
	public function ajaxview()
	{	
		$displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
		$displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
		$sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
		$sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
		$sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';

		$sortfields = array(1=>'first_name',2=>'last_name',3=>'email',4=>'rest_name',5=>'contact_id'); //3=>'user_ip',
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields)) {
			$sortFieldName = $sortfields[$sortCol];
		}		

		//Get Recored from model
		$grid_data = $this->contact_inquiries_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
		$totalRecords = $grid_data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '')?$displayStart+1:1;
		foreach ($grid_data['data'] as $key => $value) {

			$phonenumber = ($value->owners_phone_number)?$value->res_phone_number."<br/>".$value->owners_phone_number:$value->res_phone_number;
			$records["aaData"][] = array(
				$nCount,
				ucfirst($value->first_name),
				ucfirst($value->last_name),
				trim($value->email),
				trim($value->rest_name),				
				$phonenumber,
				$value->message,				
				($value->created_date) ? $this->common_model->datetimeFormat($this->common_model->getZonebaseDate($value->created_date)) : '-',
				'-'
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}	
}