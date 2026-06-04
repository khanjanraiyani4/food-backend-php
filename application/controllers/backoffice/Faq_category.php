<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Faq_category extends CI_Controller 
{
	public $controller_name = 'faq_category';
	public $prefix = 'faq_cat';
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect('home');
		}
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/faq_category_model');
	}

	//view data
	public function view() {
		if(in_array('faq_category~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('faq_categories').' | '.$this->lang->line('site_title');
			$data['Languages'] = $this->common_model->getLanguages();
			//faq category count
			$this->db->select('content_id');
			$this->db->group_by('content_id');
	        $data['faq_category_count'] = $this->db->get('faq_category')->num_rows();
			$this->load->view(ADMIN_URL.'/faq_category',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}

	//add data
	public function add() {
		if(in_array('faq_category~add',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('add_faq_category').' | '.$this->lang->line('site_title');
			if($this->input->post('submit_page') == "Submit")
	        {
				$this->form_validation->set_rules('name', $this->lang->line('cat_name'), 'trim|required|max_length[128]');
				$this->form_validation->set_rules('sequence', $this->lang->line('category_sequence'), 'trim|numeric|callback_checkExist');
				if ($this->form_validation->run())
				{
					if(!$this->input->post('content_id')){
						//ADD DATA IN CONTENT SECTION
						$add_content = array(
							'content_type'=>$this->uri->segment('2'),
							'created_by'=>$this->session->userdata("AdminUserID"),
							'created_date'=>date('Y-m-d H:i:s')
						);
						$ContentID = $this->faq_category_model->addData('content_general',$add_content);
					}else{
						$ContentID = $this->input->post('content_id');
					}
					$add_data = array(
						'name'=>$this->input->post('name'),
						'sequence'=>$this->input->post('sequence'),
						'content_id'=>$ContentID,
						'language_slug'=>$this->uri->segment('4'),
						'created_by' => $this->session->userdata('AdminUserID')
					);
					$this->faq_category_model->addData('faq_category',$add_data); 
					$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added FAQ category - '.$this->input->post('name'));
					// $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
					$_SESSION['page_MSG'] = $this->lang->line('success_add');
					redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
				}
			}
			$this->load->view(ADMIN_URL.'/faq_category_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}

	//edit data
	public function edit() {
		if(in_array('faq_category~edit',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('edit_faq_category').' | '.$this->lang->line('site_title');
			//check add form is submit
			if($this->input->post('submit_page') == "Submit")
			{
				$this->form_validation->set_rules('name', $this->lang->line('cat_name'), 'trim|required|max_length[128]');
				$this->form_validation->set_rules('sequence', $this->lang->line('category_sequence'), 'trim|numeric|callback_checkExist');
				if ($this->form_validation->run())
				{
					$updateData = array(
						'name'=>$this->input->post('name'),
						'sequence'=> $this->input->post('sequence'),
						'updated_by' => $this->session->userdata('AdminUserID')
					);
					$this->faq_category_model->updateData($updateData,'faq_category','entity_id',$this->input->post('entity_id'));
					$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited FAQ category - '.$this->input->post('name'));
					// $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
					$_SESSION['page_MSG'] = $this->lang->line('success_update');
					redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
				}
			}
			$entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
			$data['edit_records'] = $this->faq_category_model->getEditDetail($entity_id);
			$this->load->view(ADMIN_URL.'/faq_category_add',$data);
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
		$sortfields = array(1=>'name',2=>'status',3=>'created_date');
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->faq_category_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
		$Languages = $this->common_model->getLanguages();        
		$totalRecords = $grid_data['total'];        
		$records = array();
		$records["aaData"] = array(); 
		$nCount = ($displayStart != '')?$displayStart+1:1;
		$cnt = 0;
		foreach ($grid_data['data'] as $key => $value) {
			if(in_array('faq_category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || in_array('faq_category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {
				$edit_active_access = (in_array('faq_category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].')" title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
				$edit_active_access .= (in_array('faq_category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
			} else if($value['created_by'] == $this->session->userdata('AdminUserID')) {
				$edit_active_access =  (in_array('faq_category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].')" title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
				$edit_active_access .= (in_array('faq_category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].')" title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            } else {
                $edit_active_access = '-';
            }
            if($this->session->userdata('AdminUserType') != 'Restaurant Admin'){
				$records["aaData"][] = array(
					'<input type="checkbox" name="ids[]" value="'.$value["content_id"].'">',
					$nCount,
					($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
					$edit_active_access
				);
			}else{
                $records["aaData"][] = array(
                $nCount,
                ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            );
            }
			$cusLan = array();
			foreach ($Languages as $lang) { 
				if(in_array('faq_category~view',$this->session->userdata("UserAccessArray"))) {
					if(array_key_exists($lang->language_slug,$value['translations'])){
						$editbtn = (in_array('faq_category~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
						$editbtn .= ' ( '.$value['translations'][$lang->language_slug]['name'].' )';
						$cusLan[] = $editbtn;
					}else{
						$cusLan[] = (in_array('faq_category~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
					}
				} else if($value['created_by'] == $this->session->userdata('AdminUserID')) {
					if(array_key_exists($lang->language_slug,$value['translations'])){
						$editbtn = (in_array('faq_category~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
						$editbtn .= (in_array('faq_category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<a style="cursor:pointer;" onclick="disable_record('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>' : '';
						$editbtn .= ' ( '.$value['translations'][$lang->language_slug]['name'].' )';
						$cusLan[] = $editbtn;
					}else{
						$cusLan[] =  (in_array('faq_category~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
					}
				} else {
					if(array_key_exists($lang->language_slug,$value['translations'])){
						$cusLan[] = '( '.$value['translations'][$lang->language_slug]['name'].' )';
					}else{
						$cusLan[] = '';
					}
				}
			}
			// added to specific position
			if($this->session->userdata('AdminUserType') != 'Restaurant Admin'){
				array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
			}
			else{
				array_splice( $records["aaData"][$cnt], 1, 0, $cusLan);
			}
			$cnt++;
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
    }

	// method for deleting a category
	public function ajaxDelete(){
		$entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
		$faq_category = $this->faq_category_model->getFaqCategory($entity_id);
		$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted FAQ category - '.$faq_category);
		$this->faq_category_model->ajaxDelete('faq_category',$this->input->post('content_id'),$entity_id);
	}

	public function ajaxDeleteAll(){
		$content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
		$language_slug = $this->session->userdata('language_slug');
		$faq_category = $this->faq_category_model->getFaqCategory('', $content_id, $language_slug);
		$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted FAQ category - '.$faq_category);
		$this->faq_category_model->ajaxDeleteAll('faq_category',$content_id);
		// $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
		$_SESSION['page_MSG'] = $this->lang->line('success_delete');
	}

	public function ajaxDisable() {
		$entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
		if($entity_id != ''){
			$this->faq_category_model->UpdatedStatus('faq_category',$entity_id,$this->input->post('status'));
			$status_txt = '';
			if($this->input->post('status') == 0) {
				$status_txt = 'activated';
			} else {
				$status_txt = 'deactivated';
			}
			$faq_category = $this->faq_category_model->getFaqCategory($entity_id);
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' FAQ category - '.$faq_category);
		}
	}

	//Update status for All
	public function ajaxDisableAll() {
		$content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
		if($content_id != ''){
			$this->faq_category_model->UpdatedStatusAll('faq_category',$content_id,$this->input->post('status'));
			$status_txt = '';
			if($this->input->post('status') == 0) {
				$status_txt = 'activated';
			} else {
				$status_txt = 'deactivated';
			}
			$language_slug = $this->session->userdata('language_slug');
			$faq_category = $this->faq_category_model->getFaqCategory('', $content_id, $language_slug);
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' FAQ category - '.$faq_category);
		}
	}

	public function activeDeactiveMultiCat(){
		$cat_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
		$flag = $this->input->post('flag');
		if($cat_content_id){
			$content_id = explode(',', $cat_content_id);
			$data = $this->common_model->activeDeactiveMulti($content_id,$flag,'faq_category');
			$status_txt = '';
			if($flag == 'active'){
				$status_txt = 'activated';
			} elseif($flag == 'deactive'){
				$status_txt = 'deactivated';
			}
			if(count($content_id) == 1) {
				$language_slug = $this->session->userdata('language_slug');
				$faq_category = $this->faq_category_model->getFaqCategory('', $content_id[0], $language_slug);
				$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' FAQ category - '.$faq_category);
			} else {
				$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple FAQ categories');
			}
			echo json_encode($data);
		}
	}

	//check Category Sequence number already alotted  to other category
	public function checkExist(){
		$sequence = ($this->input->post('sequence') != '')?$this->input->post('sequence'):'';
		if($this->input->post('name')){
			if($sequence != ''){
				$check = $this->faq_category_model->checkExist($sequence,$this->input->post('entity_id'));
				if($check > 0){
					$this->form_validation->set_message('checkExist', $this->lang->line('sequence_exist_msg'));
					return false;
				}
			}
		}else{
			if($sequence != ''){
				$check = $this->faq_category_model->checkExist($sequence,$this->input->post('entity_id'));
				echo $check;
			}
		}
	}
}