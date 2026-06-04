<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Country extends CI_Controller 
{
	public function __construct(){
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		// if($this->session->userdata('AdminUserType') != 'MasterAdmin'){
		// 	redirect(ADMIN_URL.'/home');
		// }
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/country_model');
	}
	public function view(){
		if(in_array('country~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('country')." ".$this->lang->line('management').' | '.$this->lang->line('site_title');
			//country count
			$this->db->select('id');
	        $data['country_count'] = $this->db->get('country')->num_rows();
			$this->load->view(ADMIN_URL.'/country',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//ajax view
	public function ajaxview(){
		$displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
		$displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
		$sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
		$sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
		$sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
		$sortfields = array(
			1 => 'iso',
			2 => 'nicename',
			3 => 'phonecode',
			4 => 'status',
		);
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->country_model->get_grid_list($sortFieldName, $sortOrder, $displayStart, $displayLength);
		$totalRecords = $grid_data['total'];        
		$records = array();
		$records["aaData"] = array(); 
		$nCount = ($displayStart != '') ? $displayStart + 1 : 1;
		foreach ($grid_data['data'] as $key => $val) {
			$country_disablebtn = (in_array('country~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disable_detail('.$val->id.','.$val->status.')"  title="'.($val->status ? ' '.$this->lang->line('inactive').'' : ' '.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($val->status ? 'ban' : 'check').'"></i></button>':'';
			$set_default_countrybtn = (in_array('country~view',$this->session->userdata("UserAccessArray")) && $val->status==1) ? '<a title="'.($val->set_default==1 ? $this->lang->line('default'):$this->lang->line('set_default')).'" alt="'.($val->set_default==1 ? $this->lang->line('default'):$this->lang->line('set_default')).'" style="cursor:pointer;" onclick="onOffDetails('.$val->id.','.$val->set_default.')"><i class="fa fa-toggle-'.($val->set_default==1 ? 'on' : 'off').'" style="font-size: 25px;vertical-align: middle;color: green;"></i> </a>':'';

			$records["aaData"][] = array(
				$nCount,
				$val->iso,
				$val->nicename,
				$val->phonecode,
				($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
				$country_disablebtn.$set_default_countrybtn
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
	public function ajaxdisable() {
        $id = ($this->input->post('id') != '') ? $this->input->post('id') : '';
        $status = ($this->input->post('status') != '') ? $this->input->post('status') : '';
        if($id != '' && $status != ''){
            $this->country_model->update_status($id,$status);
            $status_txt = '';
            if($status == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $country_name = $this->country_model->getCountryName($id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' country - '.$country_name);
        }
    }
     /*set default country code*/
 	public function setDefault() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->country_model->setDefaultCountryCode($entity_id,$this->input->post('set_default'));
            $country_name = $this->country_model->getCountryName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' set '.$country_name.' as default country');
        }
    }
}