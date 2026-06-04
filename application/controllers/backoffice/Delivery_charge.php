<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Delivery_charge extends CI_Controller { 
    public $controller_name = 'delivery_charge';
    public $prefix = 'delivery';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/delivery_charge_model');
    }
    //view data
    public function view() {
        if(in_array('delivery_charge~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('delivery_charges').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            //delivery charge count
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->select('delivery_charge.charge_id');
            $this->db->join('restaurant','delivery_charge.restaurant_id = restaurant.content_id','left'); 
            $this->db->where('restaurant.status',1);
            $this->db->group_by('delivery_charge.charge_id');
            $data['delivery_charge_count'] = $this->db->get('delivery_charge')->num_rows(); 
            $this->load->view(ADMIN_URL.'/delivery_charge',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //add data
    public function add() {
        if(in_array('delivery_charge~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('delivery_charge_add').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('area_name', $this->lang->line('area_name'), 'trim|required');
                $this->form_validation->set_rules('lat_long', $this->lang->line('latitude').'/'.$this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('price_charge', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('res_name'), 'trim|required');
                //additional_delivery_charge
                $this->form_validation->set_rules('additional_delivery_charge', $this->lang->line('additional_delivery_charge'), 'trim|required');
                if ($this->form_validation->run())
                {
                    $add_data = array(                   
                        'restaurant_id'=>$this->input->post('restaurant_id'),
                        'area_name'=>$this->input->post('area_name'),
                        'lat_long'=>$this->input->post('lat_long'),
                        'price_charge'=>($this->input->post('price_charge')!='')?$this->input->post('price_charge'):NULL,
                        'additional_delivery_charge'=>($this->input->post('additional_delivery_charge')!='')?$this->input->post('additional_delivery_charge'):NULL,
                        'created_by' => $this->session->userdata('AdminUserID')
                    ); 
                    $this->delivery_charge_model->addData('delivery_charge',$add_data); 
                    $language_slug = $this->session->userdata('language_slug');
                    $res_name = $this->common_model->getResNametoDisplay('', $this->input->post('restaurant_id'), $language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added delivery charge for restaurant - '.$res_name);
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->delivery_charge_model->getListData('restaurant',$language_slug);
            $this->load->view(ADMIN_URL.'/delivery_charge_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit data
    public function edit() {
        if(in_array('delivery_charge~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('delivery_charge_edit').' | '.$this->lang->line('site_title');
            //check add form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('area_name', $this->lang->line('area_name'), 'trim|required');
                $this->form_validation->set_rules('lat_long', $this->lang->line('latitude').'/'.$this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('price_charge', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('res_name'), 'trim|required');
                //additional_delivery_charge
                $this->form_validation->set_rules('additional_delivery_charge', $this->lang->line('additional_delivery_charge'), 'trim|required');
                if ($this->form_validation->run())
                {
                    $updateData = array(     
                        'restaurant_id'=>$this->input->post('restaurant_id'),              
                        'area_name'=>$this->input->post('area_name'),
                        'lat_long'=>$this->input->post('lat_long'),
                        'price_charge'=>($this->input->post('price_charge')!='')?$this->input->post('price_charge'):NULL,
                        'additional_delivery_charge'=>($this->input->post('additional_delivery_charge')!='')?$this->input->post('additional_delivery_charge'):NULL,
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('AdminUserID')
                    ); 
                    $this->delivery_charge_model->updateData($updateData,'delivery_charge','charge_id',$this->input->post('charge_id'));
                    $language_slug = $this->session->userdata('language_slug');
                    $res_name = $this->common_model->getResNametoDisplay('', $this->input->post('restaurant_id'), $language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited delivery charge for restaurant - '.$res_name);
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                      
                }
            }    
            $language_slug = $this->session->userdata('language_slug');
            $charge_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
            $data['edit_records'] = $this->delivery_charge_model->getEditDetail($charge_id);
            $data['restaurant'] = $this->delivery_charge_model->getListData('restaurant',$language_slug,$data['edit_records']->restaurant_id);
            $this->load->view(ADMIN_URL.'/delivery_charge_add',$data);
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
        
        $sortfields = array(
            1 => 'name',
            2 => 'area_name',
            3 => 'cast(price_charge as UNSIGNED)'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->delivery_charge_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        // $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        $default_currency = get_default_system_currency();
        foreach ($grid_data['data'] as $key => $val) {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($val->is_masterdata,'yes')=='1')?'disabled':'';                    
            //Code for allow add/edit/delete permission :: End
            $deleteName = addslashes($val->area_name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_deliverycharge')),$deleteName)."'";
            if(!empty($default_currency)){
                $currency_symbol = $default_currency;
            }else{
                $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            }
            $deliverycharge_edit = (in_array('delivery_charge~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm default-btn margin-bottom red" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->charge_id)).'"><i class="fa fa-edit"></i></a>' : '';
            $deliverycharge_delete = (in_array('delivery_charge~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->charge_id.','.$msgDelete.','.$val->is_masterdata.')"  title="'.$this->lang->line('delete').'" '.$btndisable_master.' class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                $val->area_name,
                currency_symboldisplay(number_format_unchanged_precision($val->price_charge,@$currency_symbol->currency_code),@$currency_symbol->currency_symbol),
                $deliverycharge_edit.$deliverycharge_delete
            );
            $nCount++;
        }   
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method for delete
    public function ajaxDeleteAll(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $res_content_id = $this->delivery_charge_model->getResIdByDeliveryCharge($entity_id);
        $res_name = $this->common_model->getResNametoDisplay('', $res_content_id, $language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted delivery charge for restaurant - '.$res_name);
        $this->delivery_charge_model->ajaxDeleteAll('delivery_charge',$entity_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // get restaurant lat long
    public function getResLatLong(){
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $reslatlong = $this->delivery_charge_model->getResLatLong($restaurant_id);
        echo json_encode($reslatlong);
    }
    public function DeliveryChargescript()
    {
        echo "Please contact to admin"; exit;
        $this->db->select('charge_id,restaurant_id');        
        $resdata = $this->db->get('delivery_charge')->result();

        if($resdata && !empty($resdata))
        {
            for($i=0;$i<count($resdata);$i++)
            {
                $this->db->select('entity_id,content_id');
                $this->db->where('entity_id',$resdata[$i]->restaurant_id);        
                $resultcont = $this->db->get('restaurant')->first_row();
                if($resultcont && !empty($resultcont))
                {
                    $updateData = array('restaurant_id'=>$resultcont->content_id); 
                    $this->delivery_charge_model->updateData($updateData,'delivery_charge','charge_id',$resdata[$i]->charge_id);
                }                
            }
        }
    }
}