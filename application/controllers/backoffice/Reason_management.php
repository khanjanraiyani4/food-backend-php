<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reason_management extends CI_Controller 
{
	public $controller_name = 'reason_management';
	public $prefix = 'reson_mng';
	public function __construct(){
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		// if($this->session->userdata('AdminUserType') != 'MasterAdmin'){
		// 	redirect(ADMIN_URL.'/home');
		// }
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/reason_management_model');
	}
	public function view(){
		if(in_array('reason_management~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('title_reason_management').' | '.$this->lang->line('site_title');
			$data['Languages'] = $this->common_model->getLanguages();
			//reason count
			$this->db->select('content_id');
			$this->db->group_by('content_id');
	        $data['reason_count'] = $this->db->get('cancel_reject_reasons')->num_rows();
			$this->load->view(ADMIN_URL.'/reason_management',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//add data
	public function add() {
		if(in_array('reason_management~add',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('title_reason_management_add').' | '.$this->lang->line('site_title');
			if($this->input->post('submit_page') == "Submit")
			{
				$this->form_validation->set_rules('reason', $this->lang->line('reason'), 'trim|required|max_length[255]');
				$this->form_validation->set_rules('reason_type', $this->lang->line('reason_type'), 'trim|required|in_list[cancel,reject]');
				$this->form_validation->set_rules('user_type', $this->lang->line('user_type'), 'trim|required|in_list[Admin,Driver,Customer]');
				if ($this->form_validation->run())
				{
					if(!$this->input->post('content_id')){
						$add_content = array(
							'content_type' => 'cancel_reject_reason',
							'created_by' => $this->session->userdata("AdminUserID"),
							'created_date' => date('Y-m-d H:i:s')
						);
						$ContentID = $this->reason_management_model->add_data('content_general',$add_content);
					}else{
						$ContentID = $this->input->post('content_id');
					}
					$add_data = array(
						'reason' => $this->input->post('reason'),
						'reason_type' => $this->input->post('reason_type'),
						'user_type' => $this->input->post('user_type'),
						'content_id' => $ContentID,
						'language_slug' => $this->uri->segment('4'),
						'created_by' => $this->session->userdata('AdminUserID')
					);
					$this->reason_management_model->add_data('cancel_reject_reasons',$add_data);
					$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added reason - '.$this->input->post('reason'));
					// $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
					$_SESSION['page_MSG'] = $this->lang->line('success_add');
					redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
				}
			}
			$this->load->view(ADMIN_URL.'/reason_management_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//edit data
	public function edit() {
		if(in_array('reason_management~edit',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('title_reason_management_edit').' | '.$this->lang->line('site_title');
			if($this->input->post('submit_page') == "Submit"){
				$this->form_validation->set_rules('reason', $this->lang->line('reason'), 'trim|required|max_length[255]');
				$this->form_validation->set_rules('reason_type', $this->lang->line('reason_type'), 'trim|required|in_list[cancel,reject]');
				$this->form_validation->set_rules('user_type', $this->lang->line('user_type'), 'trim|required|in_list[Admin,Driver,Customer]');
				if ($this->form_validation->run()){
					$update_data = array(
						'reason'=>$this->input->post('reason'),
						'reason_type'=>$this->input->post('reason_type'),
						'user_type'=>$this->input->post('user_type'),
						'updated_by' => $this->session->userdata('AdminUserID')
					);
					$update_reason = array('reason_type' => $this->input->post('reason_type'),'user_type'=>$this->input->post('user_type'));
					$this->reason_management_model->update_data($update_data,'cancel_reject_reasons','entity_id',$this->input->post('entity_id'));
					$this->reason_management_model->update_data($update_reason,'cancel_reject_reasons','content_id',$this->input->post('content_id'));
					$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited reason - '.$this->input->post('reason'));
					// $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
					$_SESSION['page_MSG'] = $this->lang->line('success_update');
					redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
				}
			}
			$entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
			$data['edit_records'] = $this->reason_management_model->get_edit_detail($entity_id);
			$this->load->view(ADMIN_URL.'/reason_management_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	public function ajax_disable() {
		$content_id = ($this->input->post('content_id') != '') ? $this->input->post('content_id') : '';
		$status = ($this->input->post('status') != '') ? $this->input->post('status') : '';
		if($content_id != '' && $status != ''){
			$this->reason_management_model->update_status($content_id, $status);
			$status_txt = '';
			if($status == 0) {
				$status_txt = 'activated';
			} else {
				$status_txt = 'deactivated';
			}
			$language_slug = $this->session->userdata('language_slug');
			$reason_name = $this->reason_management_model->getReasonName('', $content_id,$language_slug);
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' reason - '.$reason_name);
		}
	}
	public function ajax_delete(){
		$entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
		$reason_name = $this->reason_management_model->getReasonName($entity_id);
		$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted reason - '.$reason_name);
		$this->reason_management_model->ajax_delete('cancel_reject_reasons',$this->input->post('content_id'),$entity_id);
	}
	public function ajax_delete_all(){
		$content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
		$language_slug = $this->session->userdata('language_slug');
		$reason_name = $this->reason_management_model->getReasonName('', $content_id,$language_slug);
		$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted reason - '.$reason_name);
		$this->reason_management_model->ajax_delete_all('cancel_reject_reasons',$this->input->post('content_id'),$entity_id);
		// $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
		$_SESSION['page_MSG'] = $this->lang->line('success_delete');
	}
	//ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(4=>'reason_type',5=>'reason_type',6=>'status',7=>'created_at');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->reason_management_model->get_grid_list($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $value) {
        	$deleteName = getModuleTilte($value['translations'],"reason");
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
        	$edit_active_access = (in_array('reason_management~ajax_delete_all',$this->session->userdata("UserAccessArray"))) ? '<button onclick="delete_reason('.$value['content_id'].','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('reason_management~ajax_disable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disable_enable_reason('.$value['content_id'].','.$value['status'].')"  title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $this->lang->line($value['reason_type']),
                $value['user_type'],
                ($value['status']) ? $this->lang->line('active') : $this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                	$reason_editbtn = (in_array('reason_management~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                	$reason_editbtn .= '( <span title="'.$value['translations'][$lang->language_slug]['reason'].'">'.$value['translations'][$lang->language_slug]['reason'].' </span>)';
                    $cusLan[] = $reason_editbtn;
                }else{
                    $cusLan[] = (in_array('reason_management~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 1, 0, $cusLan);
            $cnt++;
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
}