<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Food_type extends CI_Controller { 
    public $controller_name = 'food_type';
    public $prefix = 'fdt';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/food_type_model');
    }
    //view data
    public function view() {
        if(in_array('food_type~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('food_type').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            //food type count
            $this->db->select('content_id');
            $this->db->group_by('content_id');
            $data['food_type_count'] = $this->db->get('food_type')->num_rows();
            $this->load->view(ADMIN_URL.'/food_type',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //add data
    public function add() {
        if(in_array('food_type~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_food_type_add').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('food_type_name'), 'trim|required|callback_checkFoodTypeNameExist');
                $this->form_validation->set_rules('is_veg', $this->lang->line('food_type'), 'trim|required');
                if ($this->form_validation->run())
                {
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->food_type_model->addData('content_general',$add_content);
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                    }
                    $add_data = array(                   
                        'name'=>$this->input->post('name'),
                        'is_veg'=>$this->input->post('is_veg'),
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'status'=>1,
                        'created_by' => $this->session->userdata('AdminUserID')
                    ); 
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/food_type';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/food_type')) {
                          @mkdir('./uploads/food_type', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/food_type/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['food_type_image'] = "food_type/".$img['file_name']; 
                          //delete old image
                          if($add_data['food_type_image'] != '' && $this->input->post('uploaded_image') != '' && file_exists(FCPATH . 'public/uploads/'.$this->input->post('uploaded_image'))) {
                            unlink(FCPATH . 'public/uploads/'.$this->input->post('uploaded_image'));
                          }
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error'])){
                        $this->food_type_model->addData('food_type',$add_data);
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added food type - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');  
                    }         
                }
            }
            $this->load->view(ADMIN_URL.'/food_type_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit data
    public function edit() {
        if(in_array('food_type~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_food_type_edit').' | '.$this->lang->line('site_title');
            //check add form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('food_type_name'), 'trim|required|callback_checkFoodTypeNameExist');
                $this->form_validation->set_rules('is_veg', $this->lang->line('food_type'), 'trim|required');
                if ($this->form_validation->run())
                {
                    $updateData = array(                   
                        'name'=>$this->input->post('name'),
                        'is_veg'=>$this->input->post('is_veg'),
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('AdminUserID')
                    ); 
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/food_type';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/food_type')) {
                          @mkdir('./uploads/food_type', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/food_type/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $updateData['food_type_image'] = "food_type/".$img['file_name'];
                          //delete old image
                          if($updateData['food_type_image'] != '' && $this->input->post('uploaded_image') != '' && file_exists(FCPATH . 'public/uploads/'.$this->input->post('uploaded_image'))) {
                            unlink(FCPATH . 'public/uploads/'.$this->input->post('uploaded_image'));
                          }
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error'])){
                        $this->food_type_model->updateData($updateData,'food_type','entity_id',$this->input->post('entity_id'));
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited food type - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                    }
                      
                }
            }        
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->food_type_model->getEditDetail($entity_id);
            $this->load->view(ADMIN_URL.'/food_type_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(1=>'name','2'=>'status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->food_type_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $value)
        {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            $edit_active_access = '';
            $doc = "'".@$value['food_type_image']."'";
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";

            $edit_active_access .= (in_array('food_type~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="deleteAll('.$value['content_id'].','.$doc.','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {
                $records["aaData"][] = array(
                    '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">',
                    $nCount,
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    $edit_active_access
                ); 
            }
            else{
                $records["aaData"][] = array(
                    $nCount,
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    $edit_active_access
                );   
            }
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(in_array('food_type~view',$this->session->userdata("UserAccessArray")) || in_array('food_type~add',$this->session->userdata("UserAccessArray")) || in_array('food_type~edit',$this->session->userdata("UserAccessArray"))) {
                    if(array_key_exists($lang->language_slug,$value['translations'])) {
                        $foodtype_editbtn = (in_array('food_type~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $foodtype_editbtn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $foodtype_editbtn;
                    }else{
                        $cusLan[] = (in_array('food_type~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                    }          
                } else if($value['created_by'] == $this->session->userdata('AdminUserID')) {
                    if(array_key_exists($lang->language_slug,$value['translations'])){
                        $foodtype_name_edit_disable_btn = (in_array('food_type~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $foodtype_name_edit_disable_btn .= (in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<a style="cursor:pointer;" onclick="disable_record('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>' : '';                        
                        $foodtype_name_edit_disable_btn .= ' ( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $foodtype_name_edit_disable_btn;
                    }else{
                        $cusLan[] = (in_array('food_type~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
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
            if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {
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
    // method for deleting a food type
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $foodtype_name = $this->food_type_model->getFoodTypeName($entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted food type - '.$foodtype_name);
        $this->food_type_model->ajaxDelete('food_type',$this->input->post('content_id'),$entity_id);
        @unlink(FCPATH.'uploads/'.$Image);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $foodtype_name = $this->food_type_model->getFoodTypeName('',$content_id,$language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted food type - '.$foodtype_name);
        //$Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $this->food_type_model->ajaxDeleteAll('food_type',$content_id);
       // @unlink(FCPATH.'uploads/'.$Image);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->food_type_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $foodtype_name = $this->food_type_model->getFoodTypeName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' food type - '.$foodtype_name);
        }
    }
    // Update status for All
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->food_type_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $language_slug = $this->session->userdata('language_slug');
            $foodtype_name = $this->food_type_model->getFoodTypeName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' food type - '.$foodtype_name);
        }
    }
    public function activeDeactiveMultiFoodType(){
        $foodtype_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        $flag = $this->input->post('flag');
        if($foodtype_content_id){
            $content_id = explode(',', $foodtype_content_id);
            $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'food_type',1);
            $status_txt = '';
            if($flag == 'active') {
                $status_txt = 'activated';
            } else if($flag == 'deactive') {
                $status_txt = 'deactivated';
            }
            if(count($content_id) == 1) {
                $language_slug = $this->session->userdata('language_slug');
                $foodtype_name = $this->food_type_model->getFoodTypeName('',$content_id[0],$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' food type - '.$foodtype_name);
            } else {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple food types');
            }
            echo json_encode($data);
        }
    }
    public function checkFoodTypeNameExist() {
        $foodtype_name = ($this->input->post('name') != '')?trim($this->input->post('name')):'';
        $foodtype_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        if($call_from == 'CI_callback') {
            if($foodtype_name) {
                $check = $this->food_type_model->checkFoodTypeNameExist($foodtype_name,$foodtype_entity_id,$language_slug);
                if($check > 0) {
                    $this->form_validation->set_message('checkFoodTypeNameExist', $this->lang->line('foodtype_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            if($foodtype_name) {
                $check = $this->food_type_model->checkFoodTypeNameExist($foodtype_name,$foodtype_entity_id,$language_slug);
                echo $check;
            }
        }
    }
}