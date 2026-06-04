<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Users extends CI_Controller {
    public $full_module = 'User Management System'; 
    public $module_name = 'User';
    public $controller_name = 'users';
    public $prefix = '_us';
    public $ad_prefix = '_ad';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/users_model');
    }
    // view users
    public function view(){
        if(in_array('users~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_customers').' | '.$this->lang->line('site_title');
            $data['selected'] = '';
            //user's count
            $this->db->select('entity_id');
            $this->db->where('user_type','User');
            $data['user_count'] =  $this->db->get('users')->num_rows();
            //user address count
            $this->db->select('address.entity_id');
            $this->db->join('users as u','address.user_entity_id = u.entity_id','left');
            $this->db->where('u.user_type !=','MasterAdmin');
            $data['address_count'] = $this->db->get('user_address as address')->num_rows();
            $this->load->view(ADMIN_URL.'/users',$data);
        }else{
            redirect(base_url().ADMIN_URL);
        }
    }
    // view users
    public function driver(){
        if(in_array('driver~driver',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_driver').' | '.$this->lang->line('site_title');
            //driver's count
            $this->db->select('users.entity_id');
            $this->db->join('restaurant_driver_map', 'users.entity_id = restaurant_driver_map.driver_id', 'left');
            $this->db->join('restaurant','restaurant_driver_map.restaurant_content_id = restaurant.content_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('users.user_type','Driver');
            $this->db->group_by('users.entity_id');
            $data['driver_count'] =  $this->db->get('users')->num_rows();            
            $this->load->view(ADMIN_URL.'/driver',$data);
        }
        else{
             redirect(base_url().ADMIN_URL);
        }
    }
    // add users
    public function add(){
        if(in_array('users~add',$this->session->userdata("UserAccessArray")) || in_array('admin~add',$this->session->userdata("UserAccessArray")) || in_array('driver~add',$this->session->userdata("UserAccessArray")) || in_array('agent~add',$this->session->userdata("UserAccessArray"))) {
            if($this->uri->segment(4)=='admin'){
                $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('admin').' | '.$this->lang->line('site_title');
            }
            else if($this->uri->segment(4)=='driver'){
                $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('driver').' | '.$this->lang->line('site_title');
            }
            else if($this->uri->segment(4)=='agent'){
                $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('call_agents').' | '.$this->lang->line('site_title');
            }
            else{
                $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('customer').' | '.$this->lang->line('site_title');
            }
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required');
                $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required');
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|valid_email|callback_checkEmailExist');
                $this->form_validation->set_rules('user_type', $this->lang->line('user_type'), 'trim|required');
                $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|numeric|callback_checkExist');
                //$this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|required|numeric|is_unique[users.mobile_number]');
                $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');
                $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_pass'), 'trim|required|matches[password]');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    $phone_code = $this->input->post('phone_code');
                    if($this->uri->segment(4) == 'admin') {
                        $usertypeval = $this->input->post('selected_role_name');
                        $roleid = (int)$this->input->post('user_type');
                    } else {
                        $usertypeval = $this->input->post('user_type');
                        $roleid = null;
                    }
                    $add_data = array(                  
                        'first_name'=>$this->input->post('first_name'),
                        'last_name' =>$this->input->post('last_name'),
                        'email' =>strtolower($this->input->post('email')),
                        'phone_code' =>$phone_code,
                        'mobile_number' =>str_replace(" ","",$this->input->post('mobile_number')),
                        'user_type' =>$usertypeval,
                        'role_id' => $roleid,
                        'status' =>1,
                        'active' =>1,
                        'password' =>md5(SALT.$this->input->post('password')),
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/profile';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/profile')) {
                          @mkdir('./uploads/profile', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/profile/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "profile/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                        }
                    }

                    if(in_array('admin~add',$this->session->userdata("UserAccessArray")) || in_array('driver~add',$this->session->userdata("UserAccessArray")) || in_array('agent~add',$this->session->userdata("UserAccessArray"))) {
                        if($usertypeval == 'Restaurant Admin'){
                            $add_data['parent_user_id'] = $this->session->userdata('AdminUserID');
                        }
                        if($usertypeval == 'Branch Admin'){
                            $parent_id = ($this->input->post('parent_id') != '')?$this->input->post('parent_id'):'';
                            $add_data['parent_user_id'] = $parent_id;
                            /*$get_restaurant_owner = $this->common_model->getSingleRowMultipleWhere('restaurant',array('content_id'=>$this->input->post('branch_entity_id')));
                            $add_data['parent_user_id'] = $get_restaurant_owner->restaurant_owner_id;*/
                        }
                        
                        if($usertypeval == 'Driver'){
                            $add_data['driver_temperature'] = $this->input->post('driver_temperature');
                        }
                        if($usertypeval == 'Agent'){
                            $add_data['login_type'] = "normal";
                        }
                    }
                    if(in_array('admin~add',$this->session->userdata("UserAccessArray")) && $this->session->userdata('AdminUserType') != 'MasterAdmin') {
                        if($usertypeval == 'Branch Admin'){
                            $add_data['parent_user_id'] = $this->session->userdata('AdminUserID');
                        }
                    }
                    //New code adde to assign branch admin :: Start :: 09-10-2020
                    $branch_entity_id = ($this->input->post('branch_entity_id') != '')?$this->input->post('branch_entity_id'):'';
                    $userid = $this->users_model->addData('users',$add_data);
                    if(intval($branch_entity_id)>0 && strtolower($usertypeval)=='driver')
                    {
                        $rest_driver_map = array();
                        foreach ($branch_entity_id as $key => $value) {
                            $rest_driver_map[] = array(
                                'restaurant_content_id'=>$value,
                                'driver_id'=>$userid
                            );
                        }
                        $map_id = $this->users_model->insertBatch('restaurant_driver_map',$rest_driver_map,$id='');
                    }
                    //New code adde to assign branch admin :: End :: 09-10-2020
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added '.$usertypeval.' - '.$this->input->post('first_name').' '.$this->input->post('last_name'));
                    if($this->input->post('email')){
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'user-added','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                        $arrayData = array('FirstName'=>$this->input->post('first_name'),'LoginLink'=>base_url().ADMIN_URL,'Email'=>$this->input->post('email'),'Password'=>$this->input->post('password'));
                        $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                        if(!empty($EmailBody)){     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to(trim($this->input->post('email'))); 
                            $this->email->subject($Emaildata->subject);  
                            $this->email->message($EmailBody);  
                            $this->email->send(); 
                            /*Conectoo Email api start : 18march2021*/
                            //$email_result = $this->common_model->conectooEmailApi(trim($this->input->post('email')),$Emaildata->subject,$FromEmailID->OptionValue,$FromEmailName->OptionValue,$EmailBody);
                            /*Conectoo Email api end : 18march2021*/
                        } 
                    }
                    //$this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    if($usertypeval == 'Driver'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/driver');  
                    }else if($this->uri->segment(4) == 'admin'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/admin');
                    }else if($usertypeval == 'Agent'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/agent');
                    }else{
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
                    }
                }
            }
            $data['restaurant'] = $this->users_model->getListRestaurantData('restaurant',$this->session->userdata('language_slug'));
            $data['parent_list'] = $this->users_model->getListParentData($this->session->userdata('UserID'));
            $data['roles'] = $this->users_model->getRolesList($this->session->userdata('AdminRoleId'),$this->session->userdata('AdminRoleName'), 'for_add_form');
            $this->load->view(ADMIN_URL.'/users_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // edit users
    public function edit(){
        if(in_array('users~edit',$this->session->userdata("UserAccessArray")) || in_array('admin~edit',$this->session->userdata("UserAccessArray")) || in_array('driver~edit',$this->session->userdata("UserAccessArray")) || in_array('agent~edit',$this->session->userdata("UserAccessArray"))) {
            if(end($this->uri->segments)=='admin'){
                $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('admin').' | '.$this->lang->line('site_title');
            }
            else if(end($this->uri->segments)=='driver'){
                $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('driver').' | '.$this->lang->line('site_title');
            }
            else if(end($this->uri->segments)=='agent'){
                $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('call_agents').' | '.$this->lang->line('site_title');
            }
            else{
                $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('customer').' | '.$this->lang->line('site_title');   
            }
            // check if form is submitted 
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required');
                $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required');
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|callback_checkEmailExist');
                $this->form_validation->set_rules('user_type', $this->lang->line('user_type'), 'trim|required');
                $this->form_validation->set_rules('mobile_number', $this->lang->line('phone_number'), 'trim|numeric|callback_checkExist');
                if($this->input->post('password')){
                     $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');
                     $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_pass'), 'trim|required|matches[password]');
                }
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {   
                    $data_edited = $this->users_model->getEditDetail('users',$this->input->post('entity_id'));
                    $phone_code = $this->input->post('phone_code');
                    if($this->uri->segment(5) == 'admin') {
                        $usertypeval = $this->input->post('selected_role_name');
                        $roleid = (int)$this->input->post('user_type');
                    } else {
                        $usertypeval = $this->input->post('user_type');
                        $roleid = null;
                    }
                    $edit_data = array(  
                        'first_name'=>$this->input->post('first_name'),
                        'last_name' =>$this->input->post('last_name'),
                        'email' =>strtolower($this->input->post('email')),
                        'phone_code' =>$phone_code,
                        'mobile_number' =>str_replace(" ","",$this->input->post('mobile_number')),
                        'user_type' =>$usertypeval,
                        'role_id' => $roleid,
                        'status' =>1,
                        'updated_by'=>$this->session->userdata("AdminUserID"),
                        'updated_date'=>date('Y-m-d h:i:s')
                    );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/profile';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/profile')) {
                          @mkdir('./uploads/profile', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/profile/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $edit_data['image'] = "profile/".$img['file_name'];   
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          }  
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                        }
                    }
                    if($this->input->post('password')){
                        $edit_data['password'] = md5(SALT.$this->input->post('password'));
                    }
                    if(in_array('admin~edit',$this->session->userdata("UserAccessArray")) || in_array('driver~edit',$this->session->userdata("UserAccessArray"))) {
                        if($usertypeval == 'Branch Admin'){
                            $parent_id = ($this->input->post('parent_id') != '')?$this->input->post('parent_id'):'';
                            $edit_data['parent_user_id'] = $parent_id;
                            /*$get_restaurant_owner = $this->common_model->getSingleRowMultipleWhere('restaurant',array('content_id'=>$this->input->post('branch_entity_id')));
                            $edit_data['parent_user_id'] = $get_restaurant_owner->restaurant_owner_id;*/
                        }
                        if($usertypeval == 'Driver'){
                            $edit_data['driver_temperature'] = $this->input->post('driver_temperature');
                        }
                    }
                    //New code adde to assign branch admin :: Start :: 09-10-2020
                    $branch_entity_id = ($this->input->post('branch_entity_id') != '')?$this->input->post('branch_entity_id'):'';
                    
                    $this->users_model->updateData($edit_data,'users','entity_id',$this->input->post('entity_id'));
                    if(intval($branch_entity_id)>0 && strtolower($usertypeval)=='driver')
                    {
                        $rest_driver_map = array();
                        foreach ($branch_entity_id as $key => $value) {
                            $rest_driver_map[] = array(
                                'restaurant_content_id'=>$value,
                                'driver_id'=>$this->input->post('entity_id')
                            );
                        }
                        $map_id = $this->users_model->insertBatch('restaurant_driver_map',$rest_driver_map,$this->input->post('entity_id'));
                    }
                    else
                    {
                        $this->users_model->deleteRelation($this->input->post('entity_id'));
                    }
                    /*if(intval($branch_entity_id)>0 && strtolower($usertypeval)=='branch admin')
                    {
                        $branch_adminchk = $this->users_model->getBrachAdminDetail($this->input->post('entity_id'));
                        $owner_Arr = array(
                                    'restaurant_content_id'=>$branch_entity_id,
                                    'branch_admin_id'=>$this->input->post('entity_id')
                                );
                        if($branch_adminchk && !empty($branch_adminchk))
                        {
                            $this->users_model->updateData($owner_Arr,'restaurant_branch_map','branch_admin_id',$this->input->post('entity_id'));
                        }
                        else
                        {
                            $this->users_model->addData('restaurant_branch_map',$owner_Arr);
                        }
                    }*/
                    //New code adde to assign branch admin :: End :: 09-10-2020
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited '.$usertypeval.' - '.$this->input->post('first_name').' '.$this->input->post('last_name'));
                    if($this->input->post('email') != $data_edited->email){
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'email-update-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                        $arrayData = array('FirstName'=>$this->input->post('first_name'),'Email'=>$this->input->post('email'),'Sender_Email'=>$data_edited->email);
                        $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                        if(!empty($EmailBody)){     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to(trim($this->input->post('email'))); 
                            $this->email->subject($Emaildata->subject);  
                            $this->email->message($EmailBody);  
                            $this->email->send(); 
                            /*Conectoo Email api start : 18march2021*/
                            //$email_result = $this->common_model->conectooEmailApi(trim($this->input->post('email')),$Emaildata->subject,$FromEmailID->OptionValue,$FromEmailName->OptionValue,$EmailBody);
                            /*Conectoo Email api end : 18march2021*/
                        } 
                    }
                    //$this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');
                    if($usertypeval == 'Driver'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/driver');  
                    }else if($this->uri->segment(5) == 'admin'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/admin');
                    }else if($usertypeval == 'Agent'){
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/agent');
                    }else{
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
                    }
                }
            }   
            $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
            $data['edit_records'] = $this->users_model->getEditDetail('users',$entity_id);
            $data['restaurant'] = $this->users_model->getListRestaurantData('restaurant',$this->session->userdata('language_slug'));
            $data['parent_list'] = $this->users_model->getListParentData($this->session->userdata('UserID'));
            //get rest content ids for user type driver :: start
            $restaurant_driver_map = $this->users_model->getRestDrivers($entity_id);
            $data['restaurant_driver_map'] = array_column($restaurant_driver_map, "restaurant_content_id");
            //get rest content ids for user type driver :: end
            $data['branch_adminval'] = $this->users_model->getBrachAdminDetail($data['edit_records']->entity_id);
            $data['roles'] = $this->users_model->getRolesList($this->session->userdata('AdminRoleId'),$this->session->userdata('AdminRoleName'), 'for_add_form');
            $this->load->view(ADMIN_URL.'/users_add',$data); 
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        // $sortfields = array(1=>'first_name',2=>'mobile_number',3=>'user_type',4=>'status');
        $sortfields = array(
            1 => 'first_name',
            2 => 'mobile_number',
            3 => 'status',
            4 => 'created_date'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_type = '');
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val)
        {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($val->is_masterdata,'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            $deleteName = addslashes($val->first_name.' '.$val->last_name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $allOrders = '';
            if($val->user_type == 'User' && in_array('order~view',$this->session->userdata("UserAccessArray")) && in_array('users~view_orders',$this->session->userdata("UserAccessArray"))) {
                $allOrders .= '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" alt="'.$this->lang->line('orders').'" title="'.$this->lang->line('orders').'" href="'.base_url().ADMIN_URL.'/order/view/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-shopping-cart"></i> '.'('.$val->total_order.')'.'</a> ';
            } else if($val->user_type == 'User' && in_array('users~view_orders',$this->session->userdata("UserAccessArray"))) { 
                $allOrders .= '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" alt="'.$this->lang->line('orders').'" title="'.$this->lang->line('orders').'" href="javascript:void(0);"><i class="fa fa-shopping-cart"></i> '.'('.$val->total_order.')'.'</a> ';
            }
            $delete_user_btn = (in_array('users~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteUser('.$val->entity_id.','.$msgDelete.','.$val->is_masterdata.')"  '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-trash"></i></button>' : '';
            $Active_user_btn = (in_array('users~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableDetail('.$val->entity_id.','.$val->status.','.$val->is_masterdata.')"  title="'.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " '.$btndisable_master.' class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button>' : '';
            $statusval = $this->lang->line('inactive');
            $btndisalbed = '';
            //code to verify user account :: start
            $Verify_user_btn = '';
            $usertypeval = ($val->user_type)?"'".$val->user_type."'":'';
            if($val->active == 0 && in_array('users~VerifyAccount',$this->session->userdata("UserAccessArray")))
            { 
                $Verify_user_btn = '<button onclick="ActiveUserAccount('.$val->entity_id.','.$val->is_masterdata.','.$usertypeval.')" title="'.$this->lang->line('user_verify').'" class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-check-square'.'"></i></button>';
            }
            //code to verify user account :: end
            if($val->status == 1)
            { $statusval = $this->lang->line('active'); $btndisalbed = '';}
            else if( $val->status == 2 )
            { $statusval = $this->lang->line('deleted'); $btndisalbed = '';}
            $mobile_number = ($val->phone_code)?('+'.$val->phone_code.$val->mobile_number):$val->mobile_number;
            $user_edit_btn = (in_array('users~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm default-btn margin-bottom" '.$btndisalbed.' title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i></a>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $mobile_number,
                $statusval,
                $user_edit_btn.$Active_user_btn.$Verify_user_btn.$delete_user_btn.$allOrders
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change user status
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $emailData = $this->users_model->getEditDetail('users',$entity_id);
            $this->users_model->UpdatedStatus($entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' '.$emailData->user_type.' - '.$emailData->first_name.' '.$emailData->last_name);
            if($emailData->email != ''){
                if($this->input->post('status')==0){
                    $status = 'activated';
                } else {
                    $status = 'deactivated';
                }   
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                $this->db->select('subject,message');
                $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'change-status-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                $arrayData = array('FirstName'=>$emailData->first_name,'Status'=>$status);
                $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                if(!empty($EmailBody)){     
                    $this->load->library('email');  
                    $config['charset'] = 'iso-8859-1';  
                    $config['wordwrap'] = TRUE;  
                    $config['mailtype'] = 'html';  
                    $this->email->initialize($config);  
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                    $this->email->to(trim($emailData->email)); 
                    $this->email->subject($Emaildata->subject);  
                    $this->email->message($EmailBody);  
                    $this->email->send(); 
                    /*Conectoo Email api start : 18march2021*/
                    //$email_result = $this->common_model->conectooEmailApi(trim($emailData->email),$Emaildata->subject,$FromEmailID->OptionValue,$FromEmailName->OptionValue,$EmailBody);
                    /*Conectoo Email api end : 18march2021*/
                } 
            }
        }
    }
    // method for deleting a user
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $userDataForLogs = $this->users_model->getEditDetail('users',$entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted '.$userDataForLogs->user_type.' - '.$userDataForLogs->first_name.' '.$userDataForLogs->last_name);
        //delete order
        $this->users_model->deleteUsersOrder($entity_id);
        //delete user
        $this->users_model->deleteUser($this->input->post('table'),$entity_id);
        //$this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }

    // method for deleting a user
    public function ajaxDeleteAddress(){        
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $userAddressDataForLogs = $this->users_model->getEditDetail('user_address',$entity_id);
        $userDataForLogs = $this->users_model->getEditDetail('users',$userAddressDataForLogs->user_entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted an address of '.$userDataForLogs->user_type.' - '.$userDataForLogs->first_name.' '.$userDataForLogs->last_name);
        //delete user address
        $this->users_model->deleteUser($this->input->post('table'),$entity_id);             
        //$this->session->set_flashdata('add_page_MSG', $this->lang->line('success_delete'));
        $_SESSION['add_page_MSG'] = $this->lang->line('success_delete');
    }

    // add address
    public function add_address(){
        if(in_array('users~add_address',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_userAddressAdd').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('user_entity_id', $this->lang->line('user'), 'trim|required');
                $this->form_validation->set_rules('address', $this->lang->line('address'), 'trim|required');
                $this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required');
                $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required|numeric');
                $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required');
                $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
                $this->form_validation->set_rules('city', $this->lang->line('city'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    $add_data = array(
                        'user_entity_id' =>  $this->input->post('user_entity_id'),                
                        'address'=>$this->input->post('address'),
                        'landmark' =>$this->input->post('landmark'),
                        'latitude' =>$this->input->post('latitude'),
                        'longitude' =>$this->input->post('longitude'),
                        'zipcode' =>$this->input->post('zipcode'),
                        'country' =>$this->input->post('country'),
                        'city' =>$this->input->post('city'),
                        'state' =>$this->input->post('state'),
                        'address_label'=>$this->input->post('address_label'),
                        'saved_status'=>($this->input->post('saved_status'))?$this->input->post('saved_status'):''
                    );                                           
                    $this->users_model->addData('user_address',$add_data);
                    $userDataForLogs = $this->users_model->getEditDetail('users',$this->input->post('user_entity_id'));
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added address for '.$userDataForLogs->user_type.' - '.$userDataForLogs->first_name.' '.$userDataForLogs->last_name);
                    //$this->session->set_flashdata('add_page_MSG', $this->lang->line('success_add'));
                    $_SESSION['add_page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view/user_address');                 
                }
            }
            $data['selected'] = 'user_address';
            $data['user_data'] = $this->users_model->getUsers();
            $this->load->view(ADMIN_URL.'/users_address_add',$data);
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
    }
    // edit address
    public function edit_address(){
        if(in_array('users~edit_address',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_userAddressEdit').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                // $this->form_validation->set_rules('user_entity_id', $this->lang->line('user'), 'trim|required');
                $this->form_validation->set_rules('address', $this->lang->line('address'), 'trim|required');
                $this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required');
                $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required|numeric');
                $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required');
                $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
                $this->form_validation->set_rules('city', $this->lang->line('city'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    $edit_data = array(        
                        // 'user_entity_id' =>  $this->input->post('user_entity_id'),   
                        'address'=>$this->input->post('address'),
                        'landmark' =>$this->input->post('landmark'),
                        'latitude' =>$this->input->post('latitude'),
                        'longitude' =>$this->input->post('longitude'),
                        'zipcode' =>$this->input->post('zipcode'),
                        'country' =>$this->input->post('country'),
                        'city' =>$this->input->post('city'),
                        'state' =>$this->input->post('state'),
                        'address_label'=>$this->input->post('address_label'),
                        'saved_status'=>($this->input->post('saved_status'))?$this->input->post('saved_status'):''
                    );                                           
                    $this->users_model->updateData($edit_data,'user_address','entity_id',$this->input->post('entity_id'));
                    $user_data = $this->common_model->getSingleRowMultipleWhere('user_address',array('entity_id'=>$this->input->post('entity_id')) );
                    $userDataForLogs = $this->users_model->getEditDetail('users',$user_data->user_entity_id);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited address for '.$userDataForLogs->user_type.' - '.$userDataForLogs->first_name.' '.$userDataForLogs->last_name);
                    //$this->session->set_flashdata('add_page_MSG', $this->lang->line('success_update'));
                    $_SESSION['add_page_MSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view/user_address');                 
                }
            }
            $data['user_data'] = $this->users_model->getUsers();
            $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
            $data['edit_records'] = $this->users_model->getEditDetail('user_address',$entity_id);
            $this->load->view(ADMIN_URL.'/users_address_add',$data);
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxViewAddress() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        // $sortfields = array(1=>'first_name',2=>'address',3=>'status');
        $sortfields = array(
            1 => 'first_name',
            2 => 'address',
            3 => 'entity_id'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getAddressGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $deleteName = addslashes($val->first_name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_address')),$deleteName)."'";
            $edit_address_btn = (in_array('users~edit_address',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_address/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-edit"></i></a>' : '';
            $delete_address_btn = (in_array('users~ajaxDeleteAddress',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAddress('.$val->entity_id.','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-trash"></i></button>' : '';

            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $val->address,
                $edit_address_btn.$delete_address_btn
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function checkExist(){
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        $phone_code = ($this->input->post('phone_code') != '')?$this->input->post('phone_code'):'';
        $user_type = ($this->input->post('selected_role_name') != '')?$this->input->post('selected_role_name'):'';
        if($this->input->post('first_name')){
            if($mobile_number != ''){
                $check = $this->users_model->checkExist($mobile_number,$this->input->post('entity_id'),$phone_code,$user_type);
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('phone_exist'));
                    return false;
                } else {
                    return true;
                }
            } 
        }else{
            if($mobile_number != ''){
                $check = $this->users_model->checkExist($mobile_number,$this->input->post('entity_id'),$phone_code,$user_type);
                echo $check;
                if($check>0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('phone_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }       
    }
    public function checkExistPhone(){ 
        $phone_number = ($this->input->post('phone_number') != '')?$this->input->post('phone_number'):'';
        if($this->input->post('first_name')){
            if($phone_number != ''){
                $check = $this->users_model->checkExistPhone($phone_number,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExistPhone', $this->lang->line('phone_exist'));
                    return false;
                }
            } 
        }else{
            if($phone_number != ''){
                $check = $this->users_model->checkExistPhone($phone_number,$this->input->post('entity_id'));
                echo $check;
            } 
        }
    }
    public function checkEmailExist(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
        $user_type = ($this->input->post('selected_role_name') != '')?$this->input->post('selected_role_name'):'';
        if($this->input->post('first_name')){
            if($email != ''){
                $check = $this->users_model->checkEmailExist($email,$this->input->post('entity_id'),$user_type);
                if($check > 0){
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                    return false;  
                } else {
                    return true;
                }
            }
        }else{
            if($email != ''){
                $check = $this->users_model->checkEmailExist($email,$this->input->post('entity_id'),$user_type);
                echo $check;
                if($check > 0){
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                    return false;  
                } else {
                    return true;
                }
            }  
        }
    }
    //driver view
    public function ajaxdriverview(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(
            1 => 'first_name',
            2 => 'mobile_number',
            3=>'driver_temperature',
            4=>'res_name',
            5 => 'status',
            6 => 'created_date'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_type = 'Driver');
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($val->is_masterdata,'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            $edit = (in_array('driver~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'/driver" title='.$this->lang->line('edit').'><i class="fa fa-edit"></i></a>':'';
            $driver_disable_btn = (in_array('driver~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="disableDetail('.$val->entity_id.','.$val->status.','.$val->is_masterdata.')"  title="'.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="btn btn-sm danger-btn theme-btn  margin-bottom"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button> ':'';
            $commission = ($val->user_type == 'Driver' && in_array('driver~commission',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/commission/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('commission').'"><i class="fa fa-money"></i></a>':'';
            $review = (in_array('driver~review',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/review/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('review').'"><i class="fa fa-star"></i> '.'('.$val->total_review.')'.'</a>' : '';
            //driver tip changes :: start
            $driver_tip = ($val->user_type == 'Driver' && in_array('driver~drivertip',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn  margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/drivertip/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('tips').'"><i class="fa fa-money" aria-hidden="true"></i></a>':'';
            //driver tip changes :: end
            $mobile_number = ($val->phone_code)?('+'.$val->phone_code.$val->mobile_number):$val->mobile_number;
            //show less/more res name :: start
            // $res_name_less_str = substr($val->res_name,0, 50);
            // $res_name_more_str = substr($val->res_name, 50);

            $temp1 = ($val->res_name) ? explode(',',$val->res_name) : array();
            $temp2 = ($temp1) ? array_slice($temp1, 0, 3) : array();
            $temp3 = ($temp1) ? array_slice($temp1, 3) : array();
            $res_name_less_str = ($temp2) ? implode(',', $temp2) : '';
            $res_name_more_str = ($temp3) ? ', '.implode(',', $temp3) : '';


            $res_name_less_str_val = ($res_name_more_str)?'<p>'.$res_name_less_str.'<span id="dots_'.$val->entity_id.'"></span><span id="more_'.$val->entity_id.'" class="hidden" style="display: none;">'.$res_name_more_str.'</span></p>':$res_name_less_str;

            $res_name_viewmorebtn = (count($temp1)>3)?'<a href = "javascript:void(0);" onClick = "show_res_name(this.id)" id="'.$val->entity_id.'" >'.$this->lang->line('view_more').'</a>':'';
            //show less/more res name :: end
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                //$val->name,
                $mobile_number,
                $val->driver_temperature,
                $res_name_less_str_val.' '.$res_name_viewmorebtn,
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit.$driver_disable_btn.$commission.$review.$driver_tip.''
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function commission(){
        if(in_array('driver~commission',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('driver_commissions').' | '.$this->lang->line('site_title');
            $data['entity_id'] = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):'';
            //commission count
            $this->db->select('order_driver_map.driver_map_id');
            $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
            $this->db->join('order_detail','order_driver_map.order_id = order_detail.order_id','left');
            $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
            $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
            $this->db->where('order_driver_map.driver_id',$data['entity_id']);
            // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            //     $this->db->where('users.entity_id',$this->session->userdata('AdminUserID'));
            // }
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
            $this->db->where('order_driver_map.is_accept',1);
            $this->db->group_by('order_driver_map.order_id');
            $data['commission_count'] = $this->db->get('order_driver_map')->num_rows();
            $this->load->view(ADMIN_URL.'/commission',$data);
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxcommission(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(
            1=>'first_name',
            2=>'restaurant.name',
            3=>'commission',
            4=> 'date',
            6=> 'order_id'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getCommissionDetail($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        foreach ($grid_data['data'] as $key => $val) {
            $restaurant = unserialize($val->restaurant_detail);
            $disableCheckbox = ($val->commission_status == 'Paid')?'disabled':'';
            $records["aaData"][] = array(
                '<input type="checkbox" '.$disableCheckbox.' name="ids[]" value="'.$val->driver_map_id.'">',
                $val->first_name.' '.$val->last_name,
                ($val->rest_name)?$val->rest_name:'',
                //($restaurant)?$restaurant->name:''
                ($val->commission)?currency_symboldisplay($val->commission,$currency_symbol->currency_symbol):'',
                ($val->date)?$this->common_model->dateFormat($val->date):'',
                ($val->commission_status)?$val->commission_status:'',
                $val->order_id,
                (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button class="btn btn-sm  danger-btn theme-btn margin-bottom-5" onclick="openOrderDetails('.$val->order_id.')" title="'.$this->lang->line('order_details').'"><i class="fa fa-eye"></i></button>' : '',
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function review(){
        if(in_array('driver~review',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('reviews').' | '.$this->lang->line('site_title');
            $data['entity_id'] = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):'';
            //driver review count
            $this->db->select('review.entity_id');
            $this->db->join('users as driver','review.order_user_id = driver.entity_id','left');
            $this->db->join('users as customer','review.user_id = customer.entity_id','left');
            $this->db->where('review.order_user_id',$data['entity_id']);
            $data['driver_review_count'] = $this->db->get('review')->num_rows();
            $this->load->view(ADMIN_URL.'/driver_review',$data);
        }
    }
    //ajax view
    public function ajaxDriverReview(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(1=>'first_name',2=>'review',3=>'rating',4=>'review.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getDriverReviewDetail($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $restaurant = unserialize($val->restaurant_detail);
            $records["aaData"][] = array(
                $nCount,
                $val->driver_fname.' '.$val->driver_lname,
                $val->customer_fname.' '.$val->customer_lname,
                utf8_decode($val->review),
                $val->rating,
                ($val->created_date)?$this->common_model->dateFormat($val->created_date):'',
                '-'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    /*
    * Multiple commission pay
    */
    public function commission_pay(){ 
        $commisionIDs = @explode(",",$this->input->post('arrayData'));
        if(!empty($commisionIDs)){
           $count = $this->users_model->payCommision($commisionIDs);
        }
    }
    //driver generate report
    public function driver_generate_report(){
        if(in_array('driver~driver_generate_report',$this->session->userdata("UserAccessArray"))) {
            $user_type = 'Driver';
            $results = $this->users_model->generate_report($user_type); 
            if(!empty($results)){
                // export as an excel sheet
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Reports');
                $headers = array(
                    $this->lang->line('user_name'),
                    $this->lang->line('phone_no'),
                    $this->lang->line('driver_temperature'),
                    $this->lang->line('restaurant'),
                    $this->lang->line('user_type'),
                    $this->lang->line('status')
                );
                for($h=0,$c='A'; $h<count($headers); $h++,$c++)
                {
                    $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                    $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
                }
                $row = 2;
                for($r=0; $r<count($results); $r++){ 
                    $status = ($results[$r]->status == 1) ? $this->lang->line('active') : $this->lang->line('inactive');
                    $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                    $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->mobile_number);
                    $this->excel->getActiveSheet()->setCellValue('C'.$row, $results[$r]->driver_temperature);
                    $this->excel->getActiveSheet()->setCellValue('D'.$row, $results[$r]->res_name);
                    $this->excel->getActiveSheet()->setCellValue('E'.$row, $results[$r]->user_type);
                    $this->excel->getActiveSheet()->setCellValue('F'.$row, $status);
                $row++;
                }
                $filename = 'report-export.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache   
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $writer = $this->excel->print_sheet($this->excel);
                $writer->save('php://output');
                //force user to download the Excel file without writing it to server's HD
                exit;
            }else{
                // $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
                $_SESSION['not_found'] = $this->lang->line('not_found');
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
            }    
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
        
    }
    // call for ajax data
    public function ajaxAdminview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(
            1 => 'first_name',
            2 => 'mobile_number',
            3 => 'user_type',
            4 => 'restaurant_name',
            5 => 'status',
            6 => 'created_date'
        );
        $sortFieldName = 'created_date';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getAdminGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val)
        {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($val->is_masterdata,'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End

            $edit = (in_array('admin~edit',$this->session->userdata("UserAccessArray")))?'<a class="btn btn-sm danger-btn theme-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'/admin" title='.$this->lang->line('edit').'><i class="fa fa-edit"></i></a>':'';

            $admindeactivebtn = (in_array('admin~ajaxdisable',$this->session->userdata("UserAccessArray")))?'<button onclick="disableDetail('.$val->entity_id.','.$val->status.','.$val->is_masterdata.')" '.$btndisable_master.' title="'.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button>' : '';
            $mobile_number = ($val->phone_code)?('+'.$val->phone_code.$val->mobile_number):$val->mobile_number;
            //show less/more res name :: start
            // $res_name_less_str = substr($val->restaurant_name,0, 50);
            // $res_name_more_str = substr($val->restaurant_name, 50);
            $temp1 = ($val->restaurant_name) ? explode(',',$val->restaurant_name) : '';
            $temp2 = ($temp1) ? array_slice($temp1, 0, 3) : '';
            $temp3 = ($temp1) ? array_slice($temp1, 3) : '';
            $res_name_less_str = ($temp2) ? implode(',', $temp2) : '';
            $res_name_more_str = ($temp3) ? ', '.implode(',', $temp3) : '';

            $res_name_less_str_val = ($res_name_more_str) ? '<p>'.$res_name_less_str.'<span id="dots_'.$val->entity_id.'">...</span><span id="more_'.$val->entity_id.'" class="hidden" style="display: none;">'.$res_name_more_str.'</span></p>' : $res_name_less_str;

            $res_name_viewmorebtn = ($res_name_more_str) ? '<a href = "javascript:void(0);" onClick = "show_res_name(this.id)" id="'.$val->entity_id.'" >'.$this->lang->line('view_more').'</a>' : '';
            //show less/more res name :: end
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $mobile_number,
                ($val->user_type) ? $val->user_type : '-',
                ($val->restaurant_name) ? $res_name_less_str_val.' '.$res_name_viewmorebtn : '-',
                ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit.$admindeactivebtn
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // view Admins
    public function admin(){
        if(in_array('admin~admin',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_admin').' | '.$this->lang->line('site_title');
            //admin's count
            $this->db->select('entity_id');
            //$this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin')");
            $roles = array('MasterAdmin', 'User', 'Driver','Agent');
            $this->db->where_not_in('user_type',$roles);
            $data['admin_count'] =  $this->db->get('users')->num_rows();
            $data['roles'] = $this->users_model->getRolesList($this->session->userdata('AdminRoleId'),$this->session->userdata('AdminRoleName'), 'for_admin_list');
            $this->load->view(ADMIN_URL.'/admin_list',$data);
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
        
    }
    //driver review report
    public function driver_review_report(){
        if(in_array('driver~review',$this->session->userdata("UserAccessArray"))) {
            $user_type = 'Driver';
            $driver_id = ($this->uri->segment('4'))?$this->uri->segment('4'):0;
            $results = $this->users_model->review_report($driver_id); 
            if(!empty($results)){
                // export as an excel sheet
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Reports');
                $headers = array("Name","Mobile Number","Customer","Review","Ratings");
                for($h=0,$c='A'; $h<count($headers); $h++,$c++)
                {
                    $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                    $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
                }
                $row = 2;
                for($r=0; $r<count($results); $r++){ 
                    $status = ($results[$r]->status == 1)?'Active':'Deactive';
                    $mobile_number = $results[$r]->mobile_number;
                    $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->driver_fname.' '.$results[$r]->driver_lname);
                    $this->excel->getActiveSheet()->setCellValue('B'.$row, $mobile_number);
                    $this->excel->getActiveSheet()->setCellValue('C'.$row, $results[$r]->customer_fname.' '.$results[$r]->customer_lname);
                    $this->excel->getActiveSheet()->setCellValue('D'.$row, $results[$r]->review);
                    $this->excel->getActiveSheet()->setCellValue('E'.$row, $results[$r]->rating);                
                $row++;
                }
                $filename = 'review-report.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache   
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $writer = $this->excel->print_sheet($this->excel);
                $writer->save('php://output'); 
                exit;
            }else{
                // $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
                $_SESSION['not_found'] = $this->lang->line('not_found');
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
            }    
        }
        else{
            redirect(base_url().ADMIN_URL);
        }
        
    }
    //driver tip changes :: start
    //tips view
    public function drivertip(){
        if(in_array('driver~drivertip',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('driver_tip').' | '.$this->lang->line('site_title');
            $data['entity_id'] = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):'';
            //driver tip count
            $this->db->select('tips.entity_id as tips_id');
            $this->db->join('users','tips.driver_id = users.entity_id','left');
            $this->db->join('order_detail','tips.order_id = order_detail.order_id','left');
            $this->db->join('order_master','tips.order_id = order_master.entity_id','left');
            $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
            $this->db->where('tips.driver_id',$data['entity_id']);
            $this->db->where('tips.amount >',0);
            $this->db->where('(tips.refund_status != "refunded" OR tips.refund_status is NULL)');
            // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            //     $this->db->where('users.created_by',$this->session->userdata('AdminUserID'));
            // }
            $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
            $this->db->group_by('tips.order_id');
            $data['driver_tip_count'] = $this->db->get('tips')->num_rows();
            $this->load->view(ADMIN_URL.'/tips',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxtips(){
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $user_id = $this->uri->segment(4);
        //$sortfields = array(1=>'first_name',2=>'last_name',3=>'date');
        $sortfields = array(
            1=>'first_name',
            2=>'restaurant.name',
            3=>'tips.amount',
            4=> 'tips.date',
            5=> 'tips.order_id'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->gettipsDetail($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        foreach ($grid_data['data'] as $key => $val) {
            $restaurant = unserialize($val->restaurant_detail);
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                ($val->rest_name)?$val->rest_name:'',
                //($restaurant)?$restaurant->name:'',
                ($val->amount)?currency_symboldisplay($val->amount,$currency_symbol->currency_symbol):'',
                ($val->date)?$this->common_model->dateFormat($val->date):'',
                $val->order_id,
                (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button class="btn btn-sm  danger-btn theme-btn margin-bottom-5" onclick="openOrderDetails('.$val->order_id.')" title="'.$this->lang->line('order_details').'"><i class="fa fa-eye"></i></button>' : '',
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //driver tip changes :: end
    // method to verify user account :: start
    public function VerifyAccount() {
        $arr = array('stat_txt' => '');
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $emailData = $this->users_model->getEditDetail('users',$entity_id);
            if($emailData->mobile_number != '') {
                $this->users_model->UpdatedAccount($entity_id);
                $userDataForLogs = $this->users_model->getEditDetail('users',$entity_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' verified account of '.$userDataForLogs->user_type.' - '.$userDataForLogs->first_name.' '.$userDataForLogs->last_name);
                if($emailData->email != ''){
                    $status = 'activated';  
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                    $this->db->select('subject,message');
                    $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'account-verified','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                    $arrayData = array('FirstName'=>$emailData->first_name);
                    $EmailBody = generateEmailBody($Emaildata->message,$arrayData); 
                    if(!empty($EmailBody)){     
                        $this->load->library('email');  
                        $config['charset'] = 'iso-8859-1';  
                        $config['wordwrap'] = TRUE;  
                        $config['mailtype'] = 'html';  
                        $this->email->initialize($config);  
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                        $this->email->to(trim($emailData->email)); 
                        $this->email->subject($Emaildata->subject);  
                        $this->email->message($EmailBody);  
                        $this->email->send(); 
                    } 
                }
            } else {
                $arr = array('stat_txt' => 'add_phone_number');
            }
        }
        echo json_encode($arr);
    }
    // method to verify user account :: end
    // view call center agents
    public function agent(){
        if(in_array('agent~agent',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('call_center_agent').' | '.$this->lang->line('site_title');
            //agent's count
            $this->db->select('entity_id');
            $this->db->where('user_type','Agent');
            $data['agent_count'] =  $this->db->get('users')->num_rows();
            $this->load->view(ADMIN_URL.'/agent',$data);
        }
        else{
             redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxagentview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        // $sortfields = array(1=>'first_name',2=>'mobile_number',3=>'user_type',4=>'status');
        $sortfields = array(
            1 => 'first_name',
            2 => 'mobile_number',
            3 => 'status',
            4 => 'created_date'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getAgentGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$user_type = '');
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val)
        {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($val->is_masterdata,'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End

            $deleteName = addslashes($val->first_name.' '.$val->last_name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $allOrders = ($val->user_type == 'User' && in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn margin-bottom" alt="'.$this->lang->line('orders').'" title="'.$this->lang->line('orders').'" href="'.base_url().ADMIN_URL.'/order/view/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'"><i class="fa fa-shopping-cart"></i> '.'('.$val->total_order.')'.'</a> ':'';
            $delete_user_btn = (in_array('agent~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteUser('.$val->entity_id.','.$msgDelete.','.$val->is_masterdata.')"  '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-trash"></i></button>' : '';
            $Active_user_btn = (in_array('agent~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableDetail('.$val->entity_id.','.$val->status.','.$val->is_masterdata.')"  title="'.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " '.$btndisable_master.' class="btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button>' : '';
            $statusval = $this->lang->line('inactive');
            $btndisalbed = '';
            //code to verify user account :: start
            $Verify_user_btn = '';
            $usertypeval = ($val->user_type)?"'".$val->user_type."'":'';
            if($val->active == 0 && in_array('agent~add',$this->session->userdata("UserAccessArray")) && in_array('agent~edit',$this->session->userdata("UserAccessArray")))
            { 
                $Verify_user_btn = '<button onclick="ActiveUserAccount('.$val->entity_id.','.$val->is_masterdata.','.$usertypeval.')" title="'.$this->lang->line('user_verify').'" class="btn btn-sm danger-btn theme-btn margin-bottom"><i class="fa fa-check-square'.'"></i></button>';
            }
            //code to verify user account :: end
            $agent_editbtn = (in_array('agent~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn margin-bottom" '.$btndisalbed.' title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'/agent'.'"><i class="fa fa-edit"></i></a>':'';
            if($val->status == 1)
            { $statusval = $this->lang->line('active'); $btndisalbed = '';}
            else if( $val->status == 2 )
            { $statusval = $this->lang->line('deleted'); $btndisalbed = '';}
            $mobile_number = ($val->phone_code)?('+'.$val->phone_code.$val->mobile_number):$val->mobile_number;
            $records["aaData"][] = array(
                $nCount,
                $val->first_name.' '.$val->last_name,
                $mobile_number,
                $statusval,
                $agent_editbtn.$Active_user_btn.$Verify_user_btn.$delete_user_btn.$allOrders
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function save_phone_number() {
        $arr = array();
        $user_id = $this->input->post('user_id');
        $user_type = $this->input->post('user_type');
        $mobile_number = $this->input->post('mobile_number');
        $phone_code = $this->input->post('phone_code');
        $add_phn_no = array('phone_code' => $phone_code, 'mobile_number' => $mobile_number);
        $this->common_model->updateData('users',$add_phn_no,'entity_id',$user_id);
        $arr['success_msg'] = $this->lang->line('success_add');
        echo json_encode($arr);
    }
}
?>