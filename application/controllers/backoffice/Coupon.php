<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Coupon extends CI_Controller { 
    public $controller_name = 'coupon';
    public $prefix = '_cpn';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        // if($this->session->userdata('AdminUserType') != 'MasterAdmin'){
        //     redirect(ADMIN_URL.'/home');
        // }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/coupon_model');
    }
    // view coupon
    public function view(){
        if(in_array('coupon~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('admin_coupons').' | '.$this->lang->line('site_title');
            //coupon count
            $this->db->select('coupon.entity_id');
            $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
            $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('coupon.entity_id');
            $data['coupon_count'] = $this->db->get('coupon')->num_rows();
            $this->load->view(ADMIN_URL.'/coupon',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // add coupon
    public function add(){
        if(in_array('coupon~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_couponadd').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('title_admin_coupon'), 'trim|callback_checkExist');
                $this->form_validation->set_rules('coupon_type', $this->lang->line('coupon_type'), 'trim|required');
                $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required');
                $this->form_validation->set_rules('restaurant_id[]', $this->lang->line('restaurant'), 'trim|required');
                if($this->input->post('coupon_type') != 'free_delivery' && $this->input->post('coupon_type') != 'discount_on_categories'){
                    $this->form_validation->set_rules('amount_type', $this->lang->line('discount_type'), 'trim|required');
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
                }
                if($this->input->post('coupon_type') != 'discount_on_categories'){
                    $this->form_validation->set_rules('max_amount', $this->lang->line('min_amount'), 'trim|required');
                }
                $this->form_validation->set_rules('start_date', $this->lang->line('start_date_time'), 'trim|required');
                $this->form_validation->set_rules('end_date', $this->lang->line('end_date_time'), 'trim|required');

                if($this->input->post('coupon_type') == 'discount_on_cart'){
                    $this->form_validation->set_rules('maximaum_use_per_users', $this->lang->line('maximaum_use_per_users'), 'trim|required');
                    $this->form_validation->set_rules('maximaum_use', $this->lang->line('maximaum_use'), 'trim|required');
                }
                
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $start_date = str_replace('-', '/', $this->input->post('start_date'));
                    $end_date = str_replace('-', '/', $this->input->post('end_date'));
                    $add_data = array(  
                        'coupon_type'=>$this->input->post('coupon_type'),
                        'name' => strtoupper($this->input->post('name')),                 
                        'description'=>$this->input->post('description'),
                        'amount_type' =>($this->input->post('coupon_type') != 'free_delivery')?$this->input->post('amount_type'):NULL,
                        'amount' =>($this->input->post('amount'))?$this->input->post('amount'):NULL,
                        'max_amount' =>($this->input->post('max_amount'))?$this->input->post('max_amount'):0,
                        'start_date' =>$this->common_model->setZonebaseDateTime(date('Y-m-d H:i',strtotime($start_date))),
                        'end_date' =>$this->common_model->setZonebaseDateTime(date('Y-m-d H:i',strtotime($end_date))),
                        'show_in_home'=>($this->input->post('show_in_home'))?$this->input->post('show_in_home'):0,
                        'use_with_other_coupons'=>($this->input->post('use_with_other_coupons'))?$this->input->post('use_with_other_coupons'):0,
                        'maximaum_use_per_users'=>($this->input->post('maximaum_use_per_users'))?$this->input->post('maximaum_use_per_users'):0,
                        'maximaum_use'=>($this->input->post('maximaum_use'))?$this->input->post('maximaum_use'):0,
                        'coupon_for_newuser'=>($this->input->post('coupon_for_newuser'))?$this->input->post('coupon_for_newuser'):0,
                        'status' =>1,
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );
                     
                    if (!empty($_FILES['image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/coupons';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/coupons')) {
                          @mkdir('./uploads/coupons', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/coupons/'. $fileName; 
                          $imageTemp = $_FILES["image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "coupons/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }   
                    } 
                    if (empty($data['Error'])) {   
                        $entity_id = $this->coupon_model->addData('coupon',$add_data);
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a coupon - '.strtoupper($this->input->post('name')));
                        $selected_restaurantids = ($this->input->post('selected_restaurantids')) ? explode(',', $this->input->post('selected_restaurantids')) : array();
                        if(!empty($selected_restaurantids)) {
                            $res_data = array();
                            foreach ($selected_restaurantids as $key => $value) {
                                $content_id = $this->coupon_model->getResContentId($value);
                                $res_data[] = array(
                                    'restaurant_id'=>$content_id,
                                    'coupon_id'=>$entity_id
                                );
                            }
                            $this->coupon_model->insertBatch('coupon_restaurant_map',$res_data,$id = '');
                        }
                        if(!empty($this->input->post('item_id'))){
                            $item_data = array();
                            foreach ($this->input->post('item_id') as $key => $value) {
                                $content_id = $this->coupon_model->getResMenuContentId($value);
                                $item_data[] = array(
                                    'item_id'=>$content_id,
                                    'coupon_id'=>$entity_id
                                );
                            }
                            $this->coupon_model->insertBatch('coupon_item_map',$item_data,$id = '');
                        }
                        if($this->input->post('coupon_type') == "discount_on_categories"){
                            if(!empty($this->input->post('coupon_category_detail'))){
                                $category_details_array = array();
                                foreach ($this->input->post('coupon_category_detail') as $key => $value) {
                                    if($value['category_content_id'] != '' && $value['discount_type'] != '' && $value['discount_value'] != '' && $value['min_amount'] != ''){
                                        $category_details_array[] = array(
                                            'coupon_id' => $entity_id,
                                            'category_content_id' => $value['category_content_id'],
                                            'discount_type' => $value['discount_type'],
                                            'discount_value' => $value['discount_value'],
                                            'minimum_amount' => $value['min_amount'],
                                            'created_by' => $this->session->userdata("AdminUserID")
                                        );
                                    }
                                }
                                $this->coupon_model->insertBatch('coupon_category_map',$category_details_array,$id = '');
                            }
                        }
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');            
                    }                                  
                }
            }
            $language_slug=$this->session->userdata('language_slug');
            $data['restaurant'] = $this->coupon_model->getListData('restaurant',array('status'=>1),$language_slug);
            //$data['categories'] = $this->coupon_model->get_categories();
            $this->load->view(ADMIN_URL.'/coupon_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // edit coupon
    public function edit(){
        if(in_array('coupon~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_couponedit').' | '.$this->lang->line('site_title');
            // check if form is submitted 
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('title_admin_coupon'), 'trim|callback_checkExist');
                $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required');
                $this->form_validation->set_rules('coupon_type', $this->lang->line('coupon_type'), 'trim|required');
                if($this->input->post('coupon_type') != 'free_delivery' && $this->input->post('coupon_type') != 'discount_on_categories'){
                    $this->form_validation->set_rules('amount_type', $this->lang->line('discount_type'), 'trim|required');
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
                }
                if($this->input->post('coupon_type') != 'discount_on_categories'){
                    $this->form_validation->set_rules('max_amount', $this->lang->line('min_amount'), 'trim|required');
                }
                $this->form_validation->set_rules('start_date', $this->lang->line('start_date_time'), 'trim|required');
                $this->form_validation->set_rules('end_date', $this->lang->line('end_date_time'), 'trim|required');
                if($this->input->post('coupon_type') == 'discount_on_cart'){
                    $this->form_validation->set_rules('maximaum_use_per_users', $this->lang->line('maximaum_use_per_users'), 'trim|required');
                    $this->form_validation->set_rules('maximaum_use', $this->lang->line('maximaum_use'), 'trim|required');
                }
                
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $start_date = str_replace('-', '/', $this->input->post('start_date'));
                    $end_date = str_replace('-', '/', $this->input->post('end_date')); 
                    $edit_data = array(  
                        'name' =>strtoupper($this->input->post('name')),                 
                        'description'=>$this->input->post('description'),
                        'amount_type' =>($this->input->post('coupon_type') != 'free_delivery')?$this->input->post('amount_type'):NULL,
                        'amount' =>($this->input->post('amount'))?$this->input->post('amount'):NULL,
                        'max_amount' =>($this->input->post('max_amount'))?$this->input->post('max_amount'):0,
                        'start_date' =>$this->common_model->setZonebaseDateTime(date('Y-m-d H:i',strtotime($start_date))),
                        'end_date' =>$this->common_model->setZonebaseDateTime(date('Y-m-d H:i',strtotime($end_date))),
                        'show_in_home'=>($this->input->post('show_in_home'))?$this->input->post('show_in_home'):0,
                        'use_with_other_coupons'=>($this->input->post('use_with_other_coupons'))?$this->input->post('use_with_other_coupons'):0,
                        'maximaum_use_per_users'=>($this->input->post('maximaum_use_per_users'))?$this->input->post('maximaum_use_per_users'):0,
                        'maximaum_use'=>($this->input->post('maximaum_use'))?$this->input->post('maximaum_use'):0,
                        'coupon_for_newuser'=>($this->input->post('coupon_for_newuser'))?$this->input->post('coupon_for_newuser'):0,
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('AdminUserID')
                    ); 
                    if (!empty($_FILES['image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/coupons';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/coupons')) {
                          @mkdir('./uploads/coupons', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/coupons/'. $fileName; 
                          $imageTemp = $_FILES["image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $edit_data['image'] = "coupons/".$img['file_name'];  
                          // code for delete existing image
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          }  
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }   
                    } 
                    if (empty($data['Error'])) {                        
                        $this->coupon_model->updateData($edit_data,'coupon','entity_id',$this->input->post('entity_id')); 
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a coupon - '.strtoupper($this->input->post('name')));
                        $selected_restaurantids = ($this->input->post('selected_restaurantids')) ? explode(',', $this->input->post('selected_restaurantids')) : array();
                        if(!empty($selected_restaurantids)) {
                            $res_data = array();
                            foreach ($selected_restaurantids as $key => $value) {
                                $content_id = $this->coupon_model->getResContentId($value);
                                $res_data[] = array(
                                    'restaurant_id'=>$content_id,
                                    'coupon_id'=>$this->input->post('entity_id')
                                );
                            }
                            $this->coupon_model->insertBatch('coupon_restaurant_map',$res_data,$this->input->post('entity_id'));
                        }
                        if(!empty($this->input->post('item_id'))){
                            $item_data = array();
                            foreach ($this->input->post('item_id') as $key => $value) {
                                 $content_id = $this->coupon_model->getResMenuContentId($value);
                                if($this->input->post('coupon_type') == 'discount_on_combo'){
                                    $item_data[] = array(
                                        'package_id'=>$content_id,
                                        'coupon_id'=>$this->input->post('entity_id')
                                    );
                                }else{
                                    $item_data[] = array(
                                        'item_id'=>$content_id,
                                        'coupon_id'=>$this->input->post('entity_id')
                                    );
                                }
                            }
                            $this->coupon_model->insertBatch('coupon_item_map',$item_data,$this->input->post('entity_id'));
                        }
                        if($this->input->post('coupon_type') == "discount_on_categories"){
                            if(!empty($this->input->post('coupon_category_detail'))){
                                $category_details_array = array();
                                foreach ($this->input->post('coupon_category_detail') as $key => $value) {
                                    if($value['category_content_id'] != '' && $value['discount_type'] != '' && $value['discount_value'] != '' && $value['min_amount'] != ''){
                                        $category_details_array[] = array(
                                            'coupon_id' => $this->input->post('entity_id'),
                                            'category_content_id' => $value['category_content_id'],
                                            'discount_type' => $value['discount_type'],
                                            'discount_value' => $value['discount_value'],
                                            'minimum_amount' => $value['min_amount'],
                                            'created_by' => $this->session->userdata("AdminUserID")
                                        );
                                    }
                                }
                                $this->coupon_model->insertBatch('coupon_category_map',$category_details_array,$this->input->post('entity_id'));
                            }
                        }
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');    
                    }                                                
                }
            }      
            $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
            $data['edit_records'] = $this->coupon_model->getEditDetail($entity_id);
            $language_slug=$this->session->userdata('language_slug');        
            $data['restaurant'] = $this->coupon_model->getListData('restaurant',array('status'=>1),$language_slug);        
            $data['restaurant_map'] = $this->coupon_model->getListData('coupon_restaurant_map',array('coupon_id'=>$entity_id));        
            $res_entityarr = array(); $data['res_entityarr'] = array();
            if(!empty($data['restaurant_map']) && $data['restaurant_map']) {
                $restaurant_map = array_column($data['restaurant_map'], 'restaurant_id'); 
                if(!empty($restaurant_map)) {
                    $this->db->select('entity_id');
                    $this->db->where_in('restaurant.content_id',$restaurant_map);
                    $res_data = $this->db->get('restaurant')->result();
                    if($res_data && !empty($res_data)) {
                        $res_entityarr = array_column($res_data, 'entity_id');
                        $data['res_entityarr'] = $res_entityarr;
                    }
                }
            }
            $data['item_map'] = $this->coupon_model->getListData('coupon_item_map',array('coupon_id'=>$entity_id));
            $data['categories'] = array();
            $data['coupon_category_map'] = array();
            if($data['edit_records']->coupon_type=='discount_on_categories')
            {
                $restaurant_maparr = array(); $res_entityarr = array();
                if(!empty($data['restaurant_map']) && $data['restaurant_map'])
                {
                    $restaurant_map = array_column($data['restaurant_map'], 'restaurant_id'); 
                    if(!empty($restaurant_map))
                    {
                        $this->db->select('entity_id');
                        $this->db->where_in('restaurant.content_id',$restaurant_map);            
                        $res_data = $this->db->get('restaurant')->result();
                        if($res_data && !empty($res_data))
                        {
                            $res_entityarr = array_column($res_data, 'entity_id');
                        }            
                    }
                }
                $data['categories'] = $this->coupon_model->get_categories($res_entityarr);                       
                $data['coupon_category_map'] = $this->coupon_model->getListData('coupon_category_map',array('coupon_id'=>$entity_id));
            }        
            $this->load->view(ADMIN_URL.'/coupon_add',$data);
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
        
        $sortfields = array(1=>'name',2=>'amount',3=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->coupon_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $doc = "'".$val->image."'";
            $amount_type = ($val->amount_type == 'Percentage')?'%':'';
            $deleteName = addslashes($val->name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_coupon')),$deleteName)."'";
            $cpn_edit_btn = (in_array('coupon~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a>' : '';
            $cpn_delete_btn = (in_array('coupon~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->entity_id.','.$doc.','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-trash"></i></button>' : '';
            $cpn_disable_btn = (in_array('coupon~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disable_record('.$val->entity_id.','.$val->status.')"  title="'.($val->status?' '.$this->lang->line('inactive').'':' '.$this->lang->line('active').'').' " class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                ($val->amount)?number_format_unchanged_precision($val->amount).$amount_type:'',
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                $cpn_edit_btn.$cpn_delete_btn.$cpn_disable_btn
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change coupon status
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->coupon_model->UpdatedStatus('coupon',$entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $cpn_name = $this->coupon_model->getCouponName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' a coupon - '.$cpn_name);
        }
    }
    // method for deleting a coupon
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $cpn_name = $this->coupon_model->getCouponName($entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted a coupon - '.$cpn_name);
        $this->coupon_model->deleteUser('coupon',$entity_id);
        @unlink(FCPATH.'uploads/'.$Image);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    public function checkExist(){
        $coupon = ($this->input->post('coupon') != '')?$this->input->post('coupon'):'';
        if($this->input->post('amount')){
            if($coupon != ''){
                $check = $this->coupon_model->checkExist($coupon,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('coupon_exist'));
                    return false;
                }
            } 
        }else{
            if($coupon != ''){
                $check = $this->coupon_model->checkExist($coupon,$this->input->post('entity_id'));
                echo $check;
            } 
        }
    }
    public function getItem()
    {
        $language_slug=$this->session->userdata('language_slug');
        $entity_id = $this->input->post('entity_id');
        $coupon_type = $this->input->post('coupon_type');
        $html = '';
        if(!empty($entity_id)){
            $result =  $this->coupon_model->getItem($entity_id[0],$coupon_type);
            if(!empty($result)){
                foreach ($result as $key => $value) {
                    $html .= '<optgroup label="'.$value[0]->restaurant_name.'">';
                    foreach ($value as $k => $val) {
                        $html .= '<option value='.$val->entity_id.'>'.$val->name.'</option>';
                    }
                    $html .= '</optgroup>';
                }
            } 
        }
        echo $html;
    }
    public function validate_menu_items(){
        $restaurant_ids = $this->input->post('restaurant_ids');
        $item_ids = $this->input->post('item_ids');
        $entity_id=($this->input->post('entity_id'))?$this->input->post('entity_id'):'';
        $edit_recordsval=($this->input->post('edit_recordsval'))?$this->input->post('edit_recordsval'):'no';
        if($edit_recordsval=='yes' && $entity_id!='')
        {
            $entity_idarr = $this->coupon_model->getListData('coupon_restaurant_map',array('coupon_id'=>$entity_id));
            $entity_idarr = array_column($entity_idarr, 'restaurant_id');
            $language_slug=$this->session->userdata('language_slug');
            $entity_idarrtemp =$this->coupon_model->getResEntity_id($entity_idarr,$language_slug);
            $restaurant_ids = array_column($entity_idarrtemp, 'entity_id');            
        }        
        if(!empty($restaurant_ids) && !empty($item_ids)){
            $items_array = array();
            $invalid_count = 0;
            $valid_count = 0;
            $flag = '';
            foreach ($restaurant_ids as $key => $restaurant_id) {
                $restaurant_items = $this->coupon_model->get_res_menu_items($restaurant_id, $item_ids);
                if($restaurant_items && !empty($restaurant_items))
                {
                    $flag = 1;
                }
                else
                {
                    $flag = 0;
                    break;   
                }                
            }
            $res = ($flag == 0) ? 0 : 1;
            echo $res;
        }   
    }
    public function coupon_relationup()
    {
        echo "exit"; exit;
        //Code for restaurant related data update
        $this->db->select('entity_id');
        $returncpn =  $this->db->get('coupon_restaurant_map')->result_array();
        for($i=0;$i<count($returncpn);$i++)
        {
            $restaurant_map = $this->coupon_model->getListData('coupon_restaurant_map',array('entity_id'=>$returncpn[$i]['entity_id']));
            if(!empty($restaurant_map))
            {
                foreach ($restaurant_map as $key => $value)
                {
                    $content_id = $this->coupon_model->getResContentId($value['restaurant_id']);
                    if($content_id)
                    {
                        $res_data = array('restaurant_id'=>$content_id);
                        $this->coupon_model->updateData($res_data,'coupon_restaurant_map','entity_id',$returncpn[$i]['entity_id']);
                        echo $sql = $this->db->last_query(); echo "<br>"; 
                    }
                    
                }
            }
        }
        //Code for restaurant menu related data update
        $this->db->select('entity_id');
        $returncpn =  $this->db->get('coupon_item_map')->result_array();
        for($i=0;$i<count($returncpn);$i++)
        {
            $restaurant_map = $this->coupon_model->getListData('coupon_item_map',array('entity_id'=>$returncpn[$i]['entity_id']));
            if(!empty($restaurant_map))
            {
                foreach ($restaurant_map as $key => $value)
                {
                    $content_id = $this->coupon_model->getResMenuContentId($value['item_id']);
                    if($content_id)
                    {
                        $res_data = array('item_id'=>$content_id);
                        $this->coupon_model->updateData($res_data,'coupon_item_map','entity_id',$returncpn[$i]['entity_id']);
                        echo $sql = $this->db->last_query(); echo "<br>"; 
                    }
                    
                }
            }
           
        }
        echo "record update done"; exit;
    }
    public function getCategory()
    {
        $language_slug=$this->session->userdata('language_slug');        
        $coupon_type = $this->input->post('coupon_type');
        $restaurant_ids = ($this->input->post('restaurant_ids'))?$this->input->post('restaurant_ids'):array();
        $html = '';        
        $result = $this->coupon_model->get_categories($restaurant_ids);
        $ival=1;
        if(!empty($result)){
            foreach ($result as $key => $value)
            {
                $html .= ' <div class="row">
                <div class="col-md-12">
                    <input type="checkbox" class="category_checkbox"  name="category_content_id[]" id="category_content_id'.$value->content_id.'" value="'.$value->content_id.'" onchange="addDetails(\''.$ival.'\',\''.$value->content_id.'\',this.id)"> '.$value->name.'
                </div>
                <div class="col-md-12">
                    <div id="coupon_category'.$ival.'" class="coupon_category'.$value->content_id.' display-no" >
                        <input type="hidden" name="coupon_category_detail['.$ival.'][category_content_id]" value="'.$value->content_id.'" class="hidden-cat-val" id="coupon_category_detail'.$value->content_id.'">
                        <div class="coupon_category_detail'.$value->content_id.'" >
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="control-label">'.$this->lang->line('discount_type').'<span class="required">*</span></label>
                                    <select name="coupon_category_detail['.$ival.'][discount_type]" class="form-control field-required coupon_category_detailsel'.$value->content_id.'" id="discount_type'.$ival.'">
                                        <option value="">'.$this->lang->line('select').'</option>
                                        <option value="Amount">'.$this->lang->line('amount').'</option>
                                        <option value="Percentage" >'.$this->lang->line('percentage').'</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">'.$this->lang->line('discount_value').'<span class="required">*</span></label>
                                    <input type="text" name="coupon_category_detail['.$ival.'][discount_value]" id="discount_value'.$ival.'" value="" class="form-control field-required coupon_category_detaildis'.$value->content_id.'" maxlength="249">
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">'.$this->lang->line('min_order_amount').'<span class="required">*</span></label>
                                    <input type="text" name="coupon_category_detail['.$ival.'][min_amount]" id="min_amount'.$ival.'" value="" class="form-control field-required coupon_category_detailamt'.$value->content_id.'" maxlength="249">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>';
                $ival++;
            }
        }
        echo $html;
    }
    //New code for fetch the restaurant base on coupon type :: Start
    public function getRestaurantData()
    {
        $language_slug=$this->session->userdata('language_slug');
        $coupon_type = ($this->input->post('coupon_type'))?trim($this->input->post('coupon_type')):'';
        $show_allrest = ($coupon_type=='free_delivery')?'no':'yes';
        $restaurant_res = $this->coupon_model->getRestaurantData($show_allrest,$language_slug);
        $html = '';
        if($restaurant_res && !empty($restaurant_res))
        {
            foreach ($restaurant_res as $key => $value)
            {
                $html .= '<option value="'.$value['entity_id'].'">'.$value['name'].'</option>';
            }
        }
        echo $html;
    }
    //New code for fetch the restaurant base on coupon type :: End
}
?>