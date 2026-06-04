<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_method extends CI_Controller 
{
	public $controller_name = 'payment_method';
	public function __construct(){
		parent::__construct();
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		// if($this->session->userdata('AdminUserType') != 'MasterAdmin'){
		// 	redirect(ADMIN_URL.'/home');
		// }
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/payment_method_model');
	}
	public function view(){
		if(in_array('payment_method~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('payment_methods').' | '.$this->lang->line('site_title');
			//payment method count
			$this->db->select('payment_id');
	        $data['payment_method_count'] = $this->db->get('payment_method')->num_rows();
			$this->load->view(ADMIN_URL.'/payment_method',$data);
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
			1 => 'display_name_en',
			2 => 'status',
			3 => 'sorting',
		);
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->payment_method_model->get_grid_list($sortFieldName, $sortOrder, $displayStart, $displayLength);
		$totalRecords = $grid_data['total'];        
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '') ? $displayStart + 1 : 1;
		foreach ($grid_data['data'] as $key => $val) {
			$editbtn = (in_array('payment_method~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm default-btn margin-bottom red" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->payment_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a>' : '';
			$disablebtn = (in_array('payment_method~ajaxDisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disable_detail('.$val->payment_id.','.$val->status.')"  title="'.($val->status ? ' '.$this->lang->line('inactive').'' : ' '.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($val->status ? 'ban' : 'check').'"></i></button>' : '';
			$records["aaData"][] = array(
				$nCount,
				$val->display_name_en,
				($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
				$editbtn.$disablebtn
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
	//edit data
	public function edit() {
		if(in_array('payment_method~edit',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('edit_payment_method').' | '.$this->lang->line('site_title');
			if($this->input->post('submit_page') == "Submit"){
				$this->form_validation->set_rules('display_name_en', $this->lang->line('english').' '.$this->lang->line('name'), 'trim|required|max_length[255]');
				$this->form_validation->set_rules('display_name_fr', $this->lang->line('french').' '.$this->lang->line('name'), 'trim|required|max_length[255]');
				$this->form_validation->set_rules('display_name_ar', $this->lang->line('arabic').' '.$this->lang->line('name'), 'trim|required|max_length[255]');				
				$this->form_validation->set_rules('sorting', $this->lang->line('at_position'), 'trim|required|numeric|callback_validate_sorting');
				if($this->input->post('payment_gateway_slug') == 'paypal'){
					$this->form_validation->set_rules('sandbox_client_id', $this->lang->line('sandbox_client_id'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('sandbox_client_secret', $this->lang->line('sandbox_client_secret'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('live_client_id', $this->lang->line('live_client_id'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('live_client_secret', $this->lang->line('live_client_secret'), 'trim|required|max_length[255]');
				}
				if($this->input->post('payment_gateway_slug') == 'stripe'){
					$this->form_validation->set_rules('test_publishable_key', $this->lang->line('test_publishable_key'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('test_secret_key', $this->lang->line('test_secret_key'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('test_webhook_secret', $this->lang->line('test_webhook_secret'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('live_publishable_key', $this->lang->line('live_publishable_key'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('live_secret_key', $this->lang->line('live_secret_key'), 'trim|required|max_length[255]');
					$this->form_validation->set_rules('live_webhook_secret', $this->lang->line('live_webhook_secret'), 'trim|required|max_length[255]');	
				}
				if($this->input->post('payment_gateway_slug') == 'applepay'){
					$this->form_validation->set_rules('test_publishable_key', $this->lang->line('test_publishable_key'), 'trim|required|max_length[255]');					
					$this->form_validation->set_rules('live_publishable_key', $this->lang->line('live_publishable_key'), 'trim|required|max_length[255]');					
				}
				if ($this->form_validation->run()){
					$update_data = array(
						'display_name_en' => $this->input->post('display_name_en'),
						'display_name_fr' => $this->input->post('display_name_fr'),
						'display_name_ar' => $this->input->post('display_name_ar'),					
						'enable_live_mode' => $this->input->post('enable_live_mode'),
						'sorting' => $this->input->post('sorting')
					);
					if($this->input->post('payment_gateway_slug') == 'paypal'){
						$update_data ['sandbox_client_id'] = $this->input->post('sandbox_client_id');
						$update_data ['sandbox_client_secret'] = $this->input->post('sandbox_client_secret');
						$update_data ['live_client_id'] = $this->input->post('live_client_id');
						$update_data ['live_client_secret'] = $this->input->post('live_client_secret');
					}
					if($this->input->post('payment_gateway_slug') == 'stripe'){
						$update_data ['test_publishable_key'] = $this->input->post('test_publishable_key');
						$update_data ['test_secret_key'] = $this->input->post('test_secret_key');
						$update_data ['test_webhook_secret'] = $this->input->post('test_webhook_secret');
						$update_data ['live_publishable_key'] = $this->input->post('live_publishable_key');
						$update_data ['live_secret_key'] = $this->input->post('live_secret_key');
						$update_data ['live_webhook_secret'] = $this->input->post('live_webhook_secret');
					}
					if($this->input->post('payment_gateway_slug') == 'applepay'){
						$update_data ['test_publishable_key'] = $this->input->post('test_publishable_key');
						$update_data ['live_publishable_key'] = $this->input->post('live_publishable_key');
					}
					$this->payment_method_model->update_data($update_data,'payment_method','payment_id',$this->input->post('payment_id'));
					$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited payment method - '.$this->input->post('display_name_en'));
					// $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
					$_SESSION['page_MSG'] = $this->lang->line('success_update');
					redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
				}
			}
			$payment_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('payment_id');
			$data['edit_records'] = $this->payment_method_model->get_edit_detail($payment_id);
			$this->load->view(ADMIN_URL.'/payment_method_add',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
	}
	public function validate_sorting($sorting){
		$this->form_validation->set_message('validate_sorting',$this->lang->line('validate_sorting_error'));
		return ($this->common_model->check_valid_payment_sorting($sorting, $this->input->post('payment_id'))) ? FALSE : TRUE;
	}
	
	public function ajaxdisable() {
		$id = ($this->input->post('id') != '') ? $this->input->post('id') : '';
		$status = ($this->input->post('status') != '') ? $this->input->post('status') : '';
		if($id != '' && $status != ''){
			$this->payment_method_model->update_status($id,$status);
			$status_txt = '';
			if($status == 0) {
				$status_txt = 'activated';
			} else {
				$status_txt = 'deactivated';
			}
			$paymentmethodname = $this->payment_method_model->get_payment_method_name($id);
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' payment method - '.$paymentmethodname);
		}
	}
	// Restaurant Payment method start
    public function manage_payment_method(){
    	if(in_array('payment_method~manage_payment_method',$this->session->userdata("UserAccessArray"))) {
	        $data['meta_title'] = $this->lang->line('manage_payment_method').' | '.$this->lang->line('site_title');
	        $data['restaurant'] = $this->payment_method_model->get_active_restaurants();
	        $this->load->view(ADMIN_URL.'/restaurant_payment_method',$data);
        } else {
			redirect(base_url().ADMIN_URL);
		}
    }
    public function get_payment_methods(){
        $restaurant_id = $this->input->post('entity_id');
        $html = '';
        if(!empty($restaurant_id)){
            $res_content_id = $this->payment_method_model->getContentId($restaurant_id,'restaurant');
            //check if payment method already added for this restaurant.
            $chk_payment_method_suggestion = $this->payment_method_model->check_payment_method_suggestion($res_content_id->content_id);
            $result =  $this->payment_method_model->get_payment_methods();
            //if payment method already added then,
            if(!empty($chk_payment_method_suggestion)){
                $chk_payment_method_suggestion = array_column($chk_payment_method_suggestion, "payment_id");
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $selected = (in_array($value->payment_id, $chk_payment_method_suggestion))?'selected':'';
                        $name = ($this->session->userdata('language_slug') == 'en') ? $value->display_name_en : (($this->session->userdata('language_slug') == 'fr') ? $value->display_name_fr : $value->display_name_ar);
                        $html .= "<option value='".$value->payment_id."'".$selected." >".$name."</option>";
                    }
                }
            } else { 
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $name = ($this->session->userdata('language_slug') == 'en') ? $value->display_name_en : (($this->session->userdata('language_slug') == 'fr') ? $value->display_name_fr : $value->display_name_ar);
                        $html .= '<option value='.$value->payment_id.'>'.$name.'</option>';
                    }
                }
            }
        }
        echo $html;
    }
    public function add_payment_method_suggestion(){
    	$arr = array();
        if($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('payment_method_id[]', 'Payment Method', 'trim|required');
            
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $restaurant_id = $this->input->post('restaurant_id');
                $res_content_id = $this->payment_method_model->getContentId($restaurant_id,'restaurant');
                $payment_method_id = $this->input->post('payment_method_id');
                
                $add_suggestion = array();
                foreach ($payment_method_id as $key => $value) {
                    $add_suggestion[] = array(
                        'restaurant_content_id'=>$res_content_id->content_id,
                        'payment_id'=>$value
                    );
                }
                //check if payment method already added for this restaurant.
                $chk_payment_method_suggestion = $this->payment_method_model->check_payment_method_suggestion($res_content_id->content_id);
                if(!empty($chk_payment_method_suggestion)){
                    $map_id = $this->payment_method_model->deleteInsertMethodSuggestion($res_content_id->content_id,$add_suggestion);
                } else {
                    $map_id = $this->payment_method_model->deleteInsertMethodSuggestion('',$add_suggestion);
                }
                $res_name = $this->common_model->getResNametoDisplay($restaurant_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' updated payment methods for restaurant - '.$res_name);
                $arr['success_msg'] = $this->lang->line('success_add');
            } else {
            	$arr['validation_errors'] = validation_errors();
            }
        }
        echo json_encode($arr);
    }
    // payment method script
    public function PaymentMethodscript()
    {
        echo "Please contact to masteradmin"; exit;
        $data['restaurant'] = $this->payment_method_model->get_active_restaurants();
        $result =  $this->payment_method_model->get_payment_methods();
        foreach($result as $key=>$val){
        	$payment_method_id[] = $val->payment_id;
        }
        $add_suggestion = array();
        foreach($data['restaurant'] as $key=>$val){
        	foreach ($payment_method_id as $key => $value) {
	            $add_suggestion[] = array(
	                'restaurant_content_id'=>$val->content_id,
	                'payment_id'=>$value
	            );
        	}
        }
        $map_id = $this->payment_method_model->deleteInsertMethodSuggestion('',$add_suggestion);
    }
    //ajax view :: restaurant payment methods list
	public function ajax_res_payment_method_view(){
		$displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
		$displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
		$sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
		$sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
		$sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
		$sortfields = array(
			1 => 'res.name',
			2 => 'paymethod_name',
			3 => 'res.created_date',
		);
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->payment_method_model->get_res_pay_method_grid_list($sortFieldName, $sortOrder, $displayStart, $displayLength);
		$totalRecords = $grid_data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '') ? $displayStart + 1 : 1;
		foreach ($grid_data['data'] as $key => $val) {
			$records["aaData"][] = array(
				$nCount,
				$val->restaurant_name,
				($val->paymethod_name) ? $val->paymethod_name : '-',
				'<button onclick="open_pay_method_form('.$val->restaurant_id.')" title="'.$this->lang->line('edit').$this->lang->line('payment_methods').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-edit"></i></button>'
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
}