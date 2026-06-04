<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_log extends CI_Controller {
	public $controller_name = 'user_log';
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/user_log_model');
	}
	//view data
	public function view() {
		if(in_array('user_log~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('user_log').' | '.$this->lang->line('site_title');
			//user_log count
			$this->db->select('user_log_id');
			$this->db->group_by('user_log_id');
			$data['user_log_count'] = $this->db->get('user_log')->num_rows();
			$this->load->view(ADMIN_URL.'/user_log',$data);
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

		$sortfields = array(1=>'user_full_name',2=>'action',3=>'user_log.created_date',4=>'user_log_id'); //3=>'user_ip',
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields)) {
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->user_log_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
		$totalRecords = $grid_data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '')?$displayStart+1:1;
		foreach ($grid_data['data'] as $key => $value) {
			$records["aaData"][] = array(
				$nCount,
				$value->user_full_name,
				$value->action,
				//$value->user_ip,
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
	//order log view data
	public function order_log_view() {
		if(in_array('user_log~order_log_view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('order_log').' | '.$this->lang->line('site_title');
			//order_log count
			$this->db->select("order_status.status_id");
			$this->db->join('users','order_status.user_id = users.entity_id','left');
			$this->db->join('order_master','order_status.order_id = order_master.entity_id','left');
			$this->db->group_by('order_status.status_id');
			$data['order_log_count'] = $this->db->get('order_status')->num_rows();
			$this->load->view(ADMIN_URL.'/order_log',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//ajax view
	public function ajaxview_fororderlog() {
		$displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
		$displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
		$sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
		$sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
		$sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';

		$sortfields = array(1=>'order_status.order_id',2=>'order_status.order_status',3=>'order_status.time',4=>'users.first_name',5=>'order_status.status_id');
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields)) {
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->user_log_model->getGridListForOrderLog($sortFieldName,$sortOrder,$displayStart,$displayLength);
		$totalRecords = $grid_data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '')?$displayStart+1:1;
		foreach ($grid_data['data'] as $key => $value) {
			$ostatuslng = '';
			if($value->order_status == "placed" && $value->status!='1') {
				$ostatuslng = $this->lang->line('placed');
			} else if(($value->order_status == "placed" && $value->status=='1') || $value->order_status == "accepted" || $value->order_status == "accepted_by_restaurant") {
				$ostatuslng = $this->lang->line('accepted');
			} else if($value->order_status == "rejected") {
				$ostatuslng = $this->lang->line('rejected');
			} else if($value->order_status == "delivered") {
				$ostatuslng = $this->lang->line('delivered');
			} else if($value->order_status == "onGoing") {
				$ostatuslng = $this->lang->line('onGoing');
				if($value->order_mode == "PickUp") {
					$ostatuslng = $this->lang->line('order_ready');
				}
			} else if($value->order_status == "cancel") {
				$ostatuslng = $this->lang->line('cancel');
			} else if($value->order_status == "ready") {
				$ostatuslng = $this->lang->line('order_ready');
				if($value->order_mode == "DineIn") {
					$ostatuslng = $this->lang->line('served');
				}
			} else if($value->order_status == "complete") {
				$ostatuslng = $this->lang->line('complete');
			} else if($value->order_status == "pending") {
				$ostatuslng = $this->lang->line('pending');
			}
			if($ostatuslng == '') {
				$ostatuslng = ucfirst($value->order_status);
			}
			$status_created_by = '';
			if($value->status_created_by == 'DoorDash' || $value->status_created_by == 'Relay') {
				$status_created_by = $value->status_created_by;
			} else if($value->status_created_by == 'auto_cancelled') {
				$status_created_by = 'Auto Cancelled';
			} else {
				$status_created_by = ($value->user_full_name && trim($value->user_full_name) != '') ? $value->user_full_name : $value->status_created_by;
				if($status_created_by == 'MasterAdmin') {
					$status_created_by = 'Master Admin';
				}
			}
			$records["aaData"][] = array(
				$nCount,
				$value->order_id,
				$ostatuslng,
				($value->time) ? $this->common_model->datetimeFormat($this->common_model->getZonebaseDate($value->time)) : '-',
				$status_created_by,
				'-'
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
	public function export_logs() {
		$slug = $this->session->userdata('language_slug');
		$languages = $this->common_model->getFirstLanguages($slug);
		$this->lang->load('messages_lang', $languages->language_directory);
		$fromAjax = ($this->input->post('fromAjax')) ? $this->input->post('fromAjax') : "no";
		$start_datearr = explode("-", $this->input->post('start_date'));
		$end_datearr = explode("-", $this->input->post('end_date'));
		$start_date = '';
		if(!empty($start_datearr) && $this->input->post('start_date')!='') {
			$start_datetemp =  $start_datearr[1].'-'.$start_datearr[0].'-'.$start_datearr[2];
			$start_date = date('Y-m-d', strtotime($start_datetemp));
		}
		$end_date = '';
		if(!empty($end_datearr) && $this->input->post('start_date')!='') {
			$end_datetemp =  $end_datearr[1].'-'.$end_datearr[0].'-'.$end_datearr[2];
			$end_date = date('Y-m-d', strtotime($end_datetemp));
		}
		if ($start_date == '1970-01-01' AND $end_date == '1970-01-01') {
			$start_date = '';
			$end_date = '';
		}
		$data = $this->user_log_model->export_logs($start_date,$end_date);
		if(!empty($data)){
			$this->load->library("excel");
			$object = new Excel();
			$from = "A1";
			$to = "AA1";
			$object->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
			foreach(range("$from","$to") as $columnID) {
				$object->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
			}
			$object->setActiveSheetIndex(0);
			$table_columns = array(
				$this->lang->line('s_no'),
				$this->lang->line('updated_by'),
				$this->lang->line('logs'),
				$this->lang->line('date/time')
			);
			$column = 1;
			foreach($table_columns as $field) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
				$column++;
			}
			$excel_row = 2;
			for ($i=0; $i <count($data) ; $i++) {
				$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $i+1);
				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $data[$i]->user_full_name);
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $data[$i]->action);
				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, ($data[$i]->created_date) ? $this->common_model->datetimeFormat($this->common_model->getZonebaseDate($data[$i]->created_date)) : '-');
				$excel_row++;
			}
			$object_writer = $object->print_sheet($object);
			if ($fromAjax == 'yes') {
				// create directory if not exists
				if (!@is_dir('uploads/export_logs')) {
					@mkdir('./uploads/export_logs', 0777, TRUE);
				}
				$filename = 'uploads/export_logs/logs'.date('y-m-d').'.xlsx';
				$object_writer->save(str_replace(__FILE__,$filename ,__FILE__));
				echo $filename;
				exit;
			} else {
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="logs'.date('y-m-d').'.xlsx"');
				$object_writer->save('php://output');
			}
		} else {
			// $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
			$_SESSION['not_found'] = $this->lang->line('not_found');
			redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
		}
	}
	public function pickup_readystatus_script() {
		$this->db->select("order_status.status_id");
		$this->db->join('order_master','order_status.order_id = order_master.entity_id','left');
		$this->db->where('order_master.order_delivery','PickUp');
		$this->db->where('order_status.order_status','onGoing');
		$this->db->group_by('order_status.status_id');
		$result = $this->db->get('order_status')->result();
		if(!empty($result)) {
			foreach ($result as $key => $value) {
				$this->db->where('order_status.status_id',$value->status_id);
				$this->db->update('order_status',array('order_status'=>'ready'));
			}
		}
	}
	//thirdparty_delivery
	public function statcreatedby_relayordoordash_script() {
		$this->db->select("order_status.status_id,order_master.delivery_method");
		$this->db->join('order_master','order_status.order_id = order_master.entity_id','left');
		$this->db->where('order_status.status_created_by','thirdparty_delivery');
		$this->db->group_by('order_status.status_id');
		$result = $this->db->get('order_status')->result();
		if(!empty($result)) {
			foreach ($result as $key => $value) {
				$stat_created_by = '';
				if(strtolower(trim($value->delivery_method)) == 'relay') {
					$stat_created_by = 'Relay';
				} else if(strtolower(trim($value->delivery_method)) == 'doordash') {
					$stat_created_by = 'DoorDash';
				}
				if($stat_created_by != '') {
					$this->db->where('order_status.status_id',$value->status_id);
					$this->db->update('order_status',array('status_created_by'=>$stat_created_by));
				}
			}
		}
	}
}