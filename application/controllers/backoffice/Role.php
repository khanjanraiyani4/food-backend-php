<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Role extends CI_Controller {
	public $module_name = "Role";
	public $controller_name = "role";
	public $table_name = "role_master";
	//constructor
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/role_model');
	}
	//view
	public function view(){
		if(in_array('role~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('manage_roles').' | '.$this->lang->line('site_title');
			$this->db->select('role_id ');
			$this->db->group_by('role_id');
			$data['role_count'] = $this->db->get('role_master')->num_rows();
			$this->load->view(ADMIN_URL.'/roles',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//add
	public function add() {
		if(in_array('role~add',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('add_role').' | '.$this->lang->line('site_title');
			if($this->input->post('submitPage') == "Submit") {
				$this->form_validation->set_rules('role_name', $this->lang->line('role_name'), 'trim|required|callback_checkRoleNameExist');

				//check form validation using codeigniter
				if ($this->form_validation->run()) {
					$addData = array(
						'role_name'=>$this->input->post('role_name'),
						'status'=>1,
						'created_by'=>$this->session->userdata("AdminUserID")
					);
					$role_id = $this->common_model->addData($this->table_name,$addData);
					// Code for add role access
					$accessIds = $this->input->post('access_ids');
					if($accessIds != "") {
						$addUserData = array();
						$accessIdsData = explode(',', $accessIds);
						foreach ($accessIdsData as $accessId) {
							if($accessId == 'j1_1')
								continue;
							$addUserData[] = array(
								'role_id'=>$role_id,
								'access_id'=>$accessId                                                
							);
						}
						$this->role_model->addRoleAccess($addUserData,$role_id);
						//EOC for add role access
						//save user log
						$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added role - '.$this->input->post('role_name'));
						$this->session->set_flashdata('PageMSG', $this->lang->line('success_add'));
						redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
						exit;
					}
				}
			}
			$data['roleData'] = $this->common_model->getMultipleRows('role_access',1,1);
			$this->load->view(ADMIN_URL.'/roles_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	//edit
	public function edit(){
		if(in_array('role~edit',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('edit_role').' | '.$this->lang->line('site_title');
			$role_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('role_id');
			$data['edit_detail'] = $this->common_model->getSingleRow($this->table_name,'role_id',$role_id);
			if($this->input->post('submitPage') == "Submit") {
				$this->form_validation->set_rules('role_name', $this->lang->line('role_name'), 'trim|required|callback_checkRoleNameExist');
				//check form validation using codeigniter
				if ($this->form_validation->run()) {
					$role_id = $this->input->post('role_id');   
					$editData = array(
						'role_name'=>$this->input->post('role_name'),
						'updated_by'=>$this->session->userdata("AdminUserID"),
						'updated_at'=>date('Y-m-d h:i:s')
					);
					$this->common_model->updateData($this->table_name,$editData,'role_id',$this->input->post('role_id'));
					if($this->input->post('role_name') != 'Master Admin') {
						$this->common_model->updateData('users', array('user_type' => $this->input->post('role_name')), 'role_id', $this->input->post('role_id'));
					}
					$accessIds = $this->input->post('access_ids');
					if($accessIds != "") {
						$addUserData = array();
						$accessIdsData = explode(',', $accessIds);
						foreach ($accessIdsData as $accessId) {
							if($accessId == 'j1_1')
								continue;
							$addUserData[] = array(
								'role_id'=>$role_id,
								'access_id'=>$accessId
							);
						}
						$this->role_model->updateRoleAccess($addUserData,$role_id); 
						//save user log
						$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited role - '.$this->input->post('role_name'));
						$this->session->set_flashdata('PageMSG', $this->lang->line('success_update'));
						redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
					}
				}
			}
			$data['roleData'] = $this->common_model->getMultipleRows('role_access',1,1);
			$data['role_access'] = $this->role_model->getRoleAccessRights($role_id);			
			$this->load->view(ADMIN_URL.'/roles_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	public function ajaxview() {
		$searchTitleName = ($this->input->post('pageTitle') != '')?$this->input->post('pageTitle'):'';
		$displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
		$displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
		$sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
		$sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
		$sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';

		$sortfields = array(1=>'role_name',2=>'status',3=>'created_at');
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields)) {
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$data = $this->role_model->getPageList($searchTitleName,$sortFieldName,$sortOrder,$displayStart,$displayLength);
		$totalRecords = $data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '')?$displayStart+1:1;
		foreach ($data['data'] as $key => $details) {
			$action_button = '';
			$action_button .= (in_array('role~edit',$this->session->userdata("UserAccessArray")))?'<a class="btn btn-sm default-btn margin-bottom red" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($details->role_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a>':'';
			$action_button .= (in_array('role~ajaxDisable',$this->session->userdata("UserAccessArray")) && $details->role_name != 'Master Admin' && $details->role_name != 'Restaurant Admin' && $details->role_name != 'Branch Admin')?'<button onclick="disableRecord('.$details->role_id.','.$details->status.')"  title="' .($details->status?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($details->status?'ban':'check').'"></i></button>':'';

			$records["aaData"][] = array(
				$nCount,
				$details->role_name,
				($details->status == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
				$action_button
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
	public function ajaxTreeView() {
		$role_id = $_REQUEST["role_id"];
		$accessIdList = array();
		if($role_id != '') {
			$roleAccessList = $this->role_model->getRoleAccessListModel($role_id);
			foreach ($roleAccessList as $roleAccessData) {
				$accessIdList[] = $roleAccessData->access_id;
			}
		}

		$roleData = $this->role_model->getAdminAccessListModel();
		$treeData = array();
		foreach ($roleData as $key => $dataValue) {
			$treeData[$key]['access_id'] = $dataValue->access_id;
			$treeData[$key]['access_name'] = $this->lang->line($dataValue->access_name);
			$treeData[$key]['parent_access_id'] = $dataValue->parent_access_id;
			$treeData[$key]['selected'] = (in_array($dataValue->access_id, $accessIdList))?true:false; 
		}

		$finalData = array();
		$finalData['text'] = 'ALL';
		$finalData['state'] = array('opened'=>true);
		$finalData['children'] = $this->buildTree($treeData);

		header('Content-type: text/json');
		header('Content-type: application/json');
		echo json_encode($finalData);
	}
	function buildTree(array $elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as $element) {
			$treeInfo = array();
			if ($element['parent_access_id'] == $parentId) {
				$children = $this->buildTree($elements, $element['access_id']);
				if ($children) {
					$treeInfo['id'] =  $element['access_id'];
					$treeInfo['text'] = $element['access_name'];
					$treeInfo['icon'] = 'fa fa-folder icon-warning';
					$treeInfo['state'] = array('opened'=>true,'selected'=>$element['selected']);
					$treeInfo['children'] = $children;
				} else {
					$treeInfo['id'] =  $element['access_id'];
					$treeInfo['text'] =  $element['access_name'];
					$treeInfo['state'] = array('opened'=>true,'selected'=>$element['selected']);
				}
				$branch[] = $treeInfo;
			}
		}
		return $branch;
	}
	//disalbe/enable
	public function ajaxDisable() {
		$role_id = ($this->input->post('role_id') != '') ? $this->input->post('role_id') : '';
		if($role_id != '') {
			if($this->input->post('status') == 0) {
				$data = array('status' => 1);
				$status = 'activated';
			} else {
				$data = array('status' => 0);
				$status = 'deactivated';
			}
			//save user log
			$name = $this->common_model->getSingleRow('role_master','role_id',$this->input->post('role_id'));
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status.' role - '.$name->role_name);
			$this->common_model->updateData($this->table_name,$data,'role_id',$role_id);
		}
	}
	public function checkRoleNameExist(){
        $role_name = ($this->input->post('role_name') != '')?trim($this->input->post('role_name')):'';
        $role_id = ($this->input->post('role_id') != '')?$this->input->post('role_id'):'';
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';
        if($call_from == 'CI_callback'){
            if($role_name){
                $check = $this->role_model->checkRoleNameExist($role_name,$role_id);
                if($check > 0){
                    $this->form_validation->set_message('checkRoleNameExist', $this->lang->line('role_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }else{
            if($role_name){
                $check = $this->role_model->checkRoleNameExist($role_name,$role_id);
                echo $check;
            } 
        }       
    }
	public function role_management_script() {
		//role_master
		//existing user types that can access admin panel
		/*$current_user_types = array(
			array('role_name' => 'Master Admin', 'status' => 1),
			array('role_name' => 'Restaurant Admin', 'status' => 1),
			array('role_name' => 'Branch Admin', 'status' => 1),
		);
		$this->db->insert_batch('role_master',$current_user_types);
		//users
		$this->db->where('user_type', 'MasterAdmin');
		$this->db->update('users', array('role_id' => 1));
		$return1 = $this->db->affected_rows();
		$this->db->where('user_type', 'Admin');
		$this->db->update('users', array('user_type' => 'Restaurant Admin', 'role_id' => 2));
		$return2 = $this->db->affected_rows();
		$this->db->where('user_type', 'BranchAdmin');
		$this->db->update('users', array('user_type' => 'Branch Admin', 'role_id' => 3));
		$return3 = $this->db->affected_rows();*/
		//role_access
		/*$role_access_modules = array(
			//module 1 :: user management
				array('access_name' => 'user_management', 'controller_slug' =>'users', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'verify_user_account', 'controller_slug' => 'VerifyAccount', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'view_order_count', 'controller_slug' => 'view_orders', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'view_address', 'controller_slug' => 'view_address', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'add_address', 'controller_slug' => 'add_address', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'edit_address', 'controller_slug' => 'edit_address', 'parent_access_id' => 1, 'is_hidden' => 0),
				array('access_name' => 'delete_address', 'controller_slug' => 'ajaxDeleteAddress', 'parent_access_id' => 1, 'is_hidden' => 0),
			//module 2 :: admin management
				array('access_name' => 'admin_management', 'controller_slug' =>'admin', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'admin', 'parent_access_id' => 13, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 13, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 13, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 13, 'is_hidden' => 0),
			//module 3 :: driver management
				array('access_name' => 'driver_management', 'controller_slug' =>'driver', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'driver', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'export_report', 'controller_slug' => 'driver_generate_report', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'view_commission', 'controller_slug' => 'commission', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'view_review', 'controller_slug' => 'review', 'parent_access_id' => 18, 'is_hidden' => 0),
				array('access_name' => 'view_tips', 'controller_slug' => 'drivertip', 'parent_access_id' => 18, 'is_hidden' => 0),
			//module 4 :: agent management
				array('access_name' => 'agent_management', 'controller_slug' =>'agent', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'agent', 'parent_access_id' => 27, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 27, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 27, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 27, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 27, 'is_hidden' => 0),
			//module 5 :: restaurant management
				array('access_name' => 'restaurant_management', 'controller_slug' =>'restaurant', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'import_res', 'controller_slug' => 'import_restaurant', 'parent_access_id' => 33, 'is_hidden' => 0),
				array('access_name' => 'online_offline', 'controller_slug' => 'ajax_online_offline', 'parent_access_id' => 33, 'is_hidden' => 0),
			//module 6 :: food type management
				array('access_name' => 'food_type_management', 'controller_slug' =>'food_type', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 41, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 41, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 41, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 41, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 41, 'is_hidden' => 0),
			//module 7 :: menu category management
				array('access_name' => 'category_management', 'controller_slug' =>'category', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 47, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 47, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 47, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 47, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 47, 'is_hidden' => 0),
			//module 8 :: add-ons category management
				array('access_name' => 'addons_category_management', 'controller_slug' =>'addons_category', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 53, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 53, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 53, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 53, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 53, 'is_hidden' => 0),
			//module 9 :: reservation package management
				array('access_name' => 'reservation_package_management', 'controller_slug' =>'restaurant_package', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view_package', 'parent_access_id' => 59, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add_package', 'parent_access_id' => 59, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit_package', 'parent_access_id' => 59, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 59, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 59, 'is_hidden' => 0),
			//module 10 :: restaurant menu management
				array('access_name' => 'menu_management', 'controller_slug' =>'restaurant_menu', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view_menu', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add_menu', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit_menu', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'import_menu', 'controller_slug' => 'import_menu', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'stock_update', 'controller_slug' => 'ajaxStockUpdate', 'parent_access_id' => 65, 'is_hidden' => 0),
				array('access_name' => 'manage_item_suggestion', 'controller_slug' => 'menu_item_suggestion', 'parent_access_id' => 65, 'is_hidden' => 0),
			//module 11 :: rating/review Management
				array('access_name' => 'rating_review_management', 'controller_slug' =>'review', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 74, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' =>'ajaxDelete', 'parent_access_id' => 74, 'is_hidden' => 0),
			//module 12 :: delivery charge management
				array('access_name' => 'delivery_charge_management', 'controller_slug' =>'delivery_charge', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 77, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 77, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 77, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 77, 'is_hidden' => 0),
			//module 13 :: order management
				array('access_name' => 'order_management', 'controller_slug' =>'order', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' =>'add', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit_delivery_pickup_order_details', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'update_status', 'controller_slug' => 'updateOrderStatus', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'assign_driver', 'controller_slug' => 'assignDriver', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'refund', 'controller_slug' => 'ajaxinitiaterefund', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'print_receipt', 'controller_slug' => 'print_receipt', 'parent_access_id' => 82, 'is_hidden' => 0),
				array('access_name' => 'get_invoice', 'controller_slug' => 'getInvoice', 'parent_access_id' => 82, 'is_hidden' => 0),
			//module 14 :: table reservation management
				array('access_name' => 'table_reservation_management', 'controller_slug' =>'event', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 92, 'is_hidden' => 0),
				array('access_name' => 'export_report', 'controller_slug' =>'generate_report', 'parent_access_id' => 92, 'is_hidden' => 0),
				array('access_name' => 'update_status', 'controller_slug' => 'updateEventStatus', 'parent_access_id' => 92, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 92, 'is_hidden' => 0),
				array('access_name' => 'add_amount', 'controller_slug' => 'addAmount', 'parent_access_id' => 92, 'is_hidden' => 0),
			//module 15 :: coupon management
				array('access_name' => 'coupon_management', 'controller_slug' =>'coupon', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 98, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 98, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 98, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 98, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 98, 'is_hidden' => 0),
			//module 16 :: notification management
				array('access_name' => 'notification_management', 'controller_slug' =>'notification', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 104, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 104, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 104, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxdeleteNotification', 'parent_access_id' => 104, 'is_hidden' => 0),
			//module 17 :: slider image management
				array('access_name' => 'slider_image_management', 'controller_slug' =>'slider-image', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 109, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 109, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 109, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 109, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDelete', 'parent_access_id' => 109, 'is_hidden' => 0),
			//module 18 :: content management system
				array('access_name' => 'content_management_system', 'controller_slug' =>'cms', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' => 'view', 'parent_access_id' => 115, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 115, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 115, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 115, 'is_hidden' => 0),
			//module 19 :: system option management
				array('access_name' => 'system_option_management', 'controller_slug' =>'system_option', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 120, 'is_hidden' => 0),
			//module 20 :: role management
				array('access_name' => 'role_management', 'controller_slug' =>'role', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 122, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' =>'add', 'parent_access_id' => 122, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit', 'parent_access_id' => 122, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisable', 'parent_access_id' => 122, 'is_hidden' => 0),
			//module 21 :: email template management
				array('access_name' => 'email_template_management', 'controller_slug' =>'email_template', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 127, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' =>'add', 'parent_access_id' => 127, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit', 'parent_access_id' => 127, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 127, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajaxDeleteAll', 'parent_access_id' => 127, 'is_hidden' => 0),
			//module 22 :: country management
				array('access_name' => 'country_management', 'controller_slug' =>'country', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 133, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxdisable', 'parent_access_id' => 133, 'is_hidden' => 0),
			//module 23 :: reason management
				array('access_name' => 'reason_management', 'controller_slug' =>'reason_management', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 136, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' =>'add', 'parent_access_id' => 136, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit', 'parent_access_id' => 136, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajax_disable', 'parent_access_id' => 136, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' => 'ajax_delete_all', 'parent_access_id' => 136, 'is_hidden' => 0),
			//module 24 :: payment methods management
				array('access_name' => 'payment_method_management', 'controller_slug' =>'payment_method', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 142, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' => 'edit', 'parent_access_id' => 142, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisable', 'parent_access_id' => 142, 'is_hidden' => 0),
				array('access_name' => 'res_payment_method', 'controller_slug' => 'manage_payment_method', 'parent_access_id' => 142, 'is_hidden' => 0),
			//module 25 :: delivery methods management
				array('access_name' => 'delivery_method_management', 'controller_slug' =>'delivery_method', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 147, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisable', 'parent_access_id' => 147, 'is_hidden' => 0),
				array('access_name' => 'res_delivery_method', 'controller_slug' =>'manage_delivery_method', 'parent_access_id' => 147, 'is_hidden' => 0),
			//module 26 :: faq category management
				array('access_name' => 'faq_category_management', 'controller_slug' =>'faq_category', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 151, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 151, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit', 'parent_access_id' => 151, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 151, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' =>'ajaxDeleteAll', 'parent_access_id' => 151, 'is_hidden' => 0),
			//module 27 :: faq questions management
				array('access_name' => 'faq_questions_management', 'controller_slug' =>'faqs', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 157, 'is_hidden' => 0),
				array('access_name' => 'add', 'controller_slug' => 'add', 'parent_access_id' => 157, 'is_hidden' => 0),
				array('access_name' => 'edit', 'controller_slug' =>'edit', 'parent_access_id' => 157, 'is_hidden' => 0),
				array('access_name' => 'active_deactive', 'controller_slug' => 'ajaxDisableAll', 'parent_access_id' => 157, 'is_hidden' => 0),
				array('access_name' => 'delete', 'controller_slug' =>'ajaxDeleteAll', 'parent_access_id' => 157, 'is_hidden' => 0),
			//module 28 :: user log management
				array('access_name' => 'user_log_management', 'controller_slug' =>'user_log', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'view', 'parent_access_id' => 163, 'is_hidden' => 0),
			//module 29 :: order log management
				array('access_name' => 'order_log_management', 'controller_slug' =>'user_log', 'parent_access_id' => 0, 'is_hidden' => 0),
				array('access_name' => 'view', 'controller_slug' =>'order_log_view', 'parent_access_id' => 165, 'is_hidden' => 0),
		);
		$this->db->insert_batch('role_access',$role_access_modules);*/
	}
}