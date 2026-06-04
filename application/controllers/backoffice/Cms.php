<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Cms extends CI_Controller { 
    public $controller_name = 'cms';
    public $prefix = '_cms';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/cms_model');
    }
    public function view() {
        if(in_array('cms~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_cmspages').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            //cms count
            $this->db->group_by('content_id');
            $data['cms_count'] = $this->db->get('cms')->num_rows();
            $this->load->view(ADMIN_URL.'/cms',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function add() 
    {
        if(in_array('cms~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_cmspagesadd').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {            
                $this->form_validation->set_rules('name', $this->lang->line('cms_page_title'), 'trim|required');
                $this->form_validation->set_rules('description', $this->lang->line('cms_page_content'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->cms_model->addData('content_general',$add_content);
                        $CMSSlug = slugify($this->input->post('name'),'cms','CMSSlug');
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                        $slug = $this->cms_model->getCmsSlug($this->input->post('content_id'));
                        $CMSSlug = $slug->CMSSlug;
                    }
                    $add_data = array(                   
                        'name'=>$this->input->post('name'),
                        'CMSSlug'=>$CMSSlug,
                        'description' =>$this->input->post('description'),
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'status'=>1,
                        'created_by'=>$this->session->userdata("AdminUserID")
                    );                
                    if (!empty($_FILES['CMSImage']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/cms';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/cms')) {
                          @mkdir('./uploads/cms', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('CMSImage'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/cms/'. $fileName; 
                          $imageTemp = $_FILES["CMSImage"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "cms/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }   
                    }          
                    if (!empty($_FILES['cms_icon']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/cms';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB  ,i.e, 1MB
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/cms')) {
                          @mkdir('./uploads/cms', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('cms_icon'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/cms/'. $fileName; 
                          $imageTemp = $_FILES["cms_icon"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $add_data['cms_icon'] = "cms/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }   
                    }
                    if(empty($data['Error'])){
                        $this->cms_model->addData('cms',$add_data);
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a CMS page - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view'); 
                    }                
                }
            }
            $this->load->view(ADMIN_URL.'/cms_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function edit() 
    {
        if(in_array('cms~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_cmspagesedit').' | '.$this->lang->line('site_title');
            //check add role form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('cms_page_title'), 'trim|required');
                $this->form_validation->set_rules('description', $this->lang->line('cms_page_content'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $edit_data = array(
                        'name'=>$this->input->post('name'),
                        'description' =>$this->input->post('description'),
                        'updated_by'=>$this->session->userdata("AdminUserID"),
                        'updated_date'=>date('Y-m-d h:i:s')
                    );
                    if (!empty($_FILES['CMSImage']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/cms';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/cms')) {
                          @mkdir('./uploads/cms', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('CMSImage'))
                        {
                          $img = $this->upload->data();
                          $edit_data['image'] = "cms/".$img['file_name'];  
                          // code for delete existing image
                          if($this->input->post('uploadedCms_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploadedCms_image'));
                          }  
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }   
                    }     
                    if (!empty($_FILES['cms_icon']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/cms';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/cms')) {
                          @mkdir('./uploads/cms', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('cms_icon'))
                        {
                          $img = $this->upload->data();
                          $edit_data['cms_icon'] = "cms/".$img['file_name'];  
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
                    if(empty($data['Error'])){          
                        //$this->cms_model->editCMSPageModel($editCMSData,$this->input->post('CMSID'));
                        $this->cms_model->updateData($edit_data,'cms','entity_id',$this->input->post('entity_id'));
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a CMS page - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');   
                    }          
                }
            }        
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->cms_model->getEditDetail($entity_id);
            $this->load->view(ADMIN_URL.'/cms_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(4=>'status',5=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->cms_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $value) {
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_cms')),$deleteName)."'";
            $edit_active_access = '';
            $edit_active_access .= (in_array('cms~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('cms~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                ($value['status'])?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    //if($value['cms_slug'] != 'login-with-fb'){
                        $cmseditbtn = '';
                        $cmseditbtn .= (in_array('cms~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $cmseditbtn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $cmseditbtn;
                    /*} else {
                        $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>
                        ( '.$value['translations'][$lang->language_slug]['name'].' )';
                    }*/
                }else{
                    $cusLan[] = (in_array('cms~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
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
    // method for deleting a category
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $cms_name = $this->cms_model->getCmsName($entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted CMS page - '.$cms_name);
        $this->cms_model->ajaxDelete('cms',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $cms_name = $this->cms_model->getCmsName('',$content_id,$language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted CMS page - '.$cms_name);
        $this->cms_model->ajaxDeleteAll('cms',$content_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->cms_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $cms_name = $this->cms_model->getCmsName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' CMS page - '.$cms_name);
        }
    }
    // Update status for All
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->cms_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $language_slug = $this->session->userdata('language_slug');
            $cms_name = $this->cms_model->getCmsName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' CMS page - '.$cms_name);
        }
    }
}