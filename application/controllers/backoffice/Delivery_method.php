<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_method extends CI_Controller 
{
	public $controller_name = 'delivery_method';
	public function __construct(){
		parent::__construct();
		redirect(ADMIN_URL.'/home');
		if (!$this->session->userdata('is_admin_login')) {
			redirect(ADMIN_URL.'/home');
		}
		// if($this->session->userdata('AdminUserType') != 'MasterAdmin'){
		// 	redirect(ADMIN_URL.'/home');
		// }
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/delivery_method_model');
	}
	public function view(){
		if(in_array('delivery_method~view',$this->session->userdata("UserAccessArray"))) {
			$data['meta_title'] = $this->lang->line('delivery_methods').' | '.$this->lang->line('site_title');
			//delivery method count
			$this->db->select('delivery_method_id');
	        $data['delivery_method_count'] = $this->db->get('delivery_method')->num_rows();
			$this->load->view(ADMIN_URL.'/delivery_method',$data);
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
			3 => 'delivery_method_id',
		);
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->delivery_method_model->get_grid_list($sortFieldName, $sortOrder, $displayStart, $displayLength);
		$totalRecords = $grid_data['total'];        
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '') ? $displayStart + 1 : 1;
		foreach ($grid_data['data'] as $key => $val) {
			$records["aaData"][] = array(
				$nCount,
				$val->display_name_en,
				($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
				//'<a class="btn btn-sm default-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->delivery_method_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a> <button onclick="disable_detail('.$val->delivery_method_id.','.$val->status.')"  title="'.($val->status ? ' '.$this->lang->line('inactive').'' : ' '.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-'.($val->status ? 'ban' : 'check').'"></i></button>'
				(in_array('delivery_method~ajaxDisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disable_detail('.$val->delivery_method_id.','.$val->status.')"  title="'.($val->status ? ' '.$this->lang->line('inactive').'' : ' '.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-'.($val->status ? 'ban' : 'check').'"></i></button>' : '',
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
			$this->delivery_method_model->update_status($id,$status);
			$status_txt = '';
			if($status == 0) {
				$status_txt = 'activated';
			} else {
				$status_txt = 'deactivated';
			}
			$deliverymethodname = $this->delivery_method_model->get_delivery_method_name($id);
			$this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' delivery method - '.$deliverymethodname);
		}
	}
	// Restaurant Delivery method start
    public function manage_delivery_method(){
    	if(in_array('delivery_method~manage_delivery_method',$this->session->userdata("UserAccessArray"))) {
	        $data['meta_title'] = $this->lang->line('manage_delivery_method').' | '.$this->lang->line('site_title');
	        $data['restaurant'] = $this->delivery_method_model->get_active_restaurants();
	        $this->load->view(ADMIN_URL.'/restaurant_delivery_method',$data);
		} else {
			redirect(base_url().ADMIN_URL);
		}
    }
    //fetch delivery method here
    public function get_delivery_methods(){
        $restaurant_id = $this->input->post('entity_id');
        $html = '';
        if(!empty($restaurant_id)){
            $res_content_id = $this->delivery_method_model->getContentId($restaurant_id,'restaurant');
            //check if delivery method already added for this restaurant.
            $chk_delivery_method_suggestion = $this->delivery_method_model->check_delivery_method_map($res_content_id->content_id);
            $result =  $this->delivery_method_model->get_delivery_methods();
            //if delivery method already added then,
            if(!empty($chk_delivery_method_suggestion)){
                $chk_delivery_method_suggestion = array_column($chk_delivery_method_suggestion, "delivery_method_id");
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $selected = (in_array($value->delivery_method_id, $chk_delivery_method_suggestion))?'selected':'';
                        $name = ($this->session->userdata('language_slug') == 'en') ? $value->display_name_en : (($this->session->userdata('language_slug') == 'fr') ? $value->display_name_fr : $value->display_name_ar);
                        $html .= "<option value='".$value->delivery_method_id."'".$selected." >".$name."</option>";
                    }
                }
            } else { 
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $name = ($this->session->userdata('language_slug') == 'en') ? $value->display_name_en : (($this->session->userdata('language_slug') == 'fr') ? $value->display_name_fr : $value->display_name_ar);
                        $html .= '<option value='.$value->delivery_method_id.'>'.$name.'</option>';
                    }
                }
            }
        }
        echo $html;
    }
    //modify function to add delivery method
    public function add_delivery_method(){
    	$arr = array();
        if($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant'), 'trim|required');
            $this->form_validation->set_rules('delivery_method_id[]', $this->lang->line('delivery_method'), 'trim|required');
            
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $restaurant_id = $this->input->post('restaurant_id');
                $res_content_id = $this->delivery_method_model->getContentId($restaurant_id,'restaurant');
                $delivery_method_id = $this->input->post('delivery_method_id');
                
                $add_suggestion = array();
                foreach ($delivery_method_id as $key => $value) {
                    $add_suggestion[] = array(
                        'restaurant_content_id'=>$res_content_id->content_id,
                        'delivery_method_id'=>$value
                    );
                }
                //check if delivery method already added for this restaurant.
                $chk_delivery_method_suggestion = $this->delivery_method_model->check_delivery_method_map($res_content_id->content_id);
                if(!empty($chk_delivery_method_suggestion)){
                    $map_id = $this->delivery_method_model->deleteInsertMethodMap($res_content_id->content_id,$add_suggestion);
                } else {
                    $map_id = $this->delivery_method_model->deleteInsertMethodMap('',$add_suggestion);
                }
                $res_name = $this->common_model->getResNametoDisplay($restaurant_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' updated delivery methods for restaurant - '.$res_name);
                $arr['success_msg'] = $this->lang->line('success_add');
            } else {
            	$arr['validation_errors'] = validation_errors();
            }
        }
        echo json_encode($arr);
    }
    //ajax view :: restaurant delivery methods list
	public function ajax_res_delivery_method_view(){
		$displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
		$displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
		$sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
		$sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
		$sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
		$sortfields = array(
			1 => 'res.name',
			2 => 'deliverymethod_name',
			3 => 'res.created_date',
		);
		$sortFieldName = '';
		if(array_key_exists($sortCol, $sortfields))
		{
			$sortFieldName = $sortfields[$sortCol];
		}
		//Get Recored from model
		$grid_data = $this->delivery_method_model->get_res_delivery_method_grid_list($sortFieldName, $sortOrder, $displayStart, $displayLength);
		$totalRecords = $grid_data['total'];
		$records = array();
		$records["aaData"] = array();
		$nCount = ($displayStart != '') ? $displayStart + 1 : 1;
		foreach ($grid_data['data'] as $key => $val) {
			$records["aaData"][] = array(
				$nCount,
				$val->restaurant_name,
				($val->deliverymethod_name) ? $val->deliverymethod_name : '-',
				'<button onclick="open_delivery_method_form('.$val->restaurant_id.')" title="'.$this->lang->line('edit').$this->lang->line('delivery_methods').'" class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-edit"></i></button>'
			);
			$nCount++;
		}
		$records["sEcho"] = $sEcho;
		$records["iTotalRecords"] = $totalRecords;
		$records["iTotalDisplayRecords"] = $totalRecords;
		echo json_encode($records);
	}
}