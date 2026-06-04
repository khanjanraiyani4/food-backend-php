<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Email_template extends CI_Controller { 
    public $module_name = 'Email Template';
    public $controller_name = 'email_template';
    public $prefix = '_email';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/email_template_model');
    }
    public function view() {
        if(in_array('email_template~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('titleadmin_email_template').' | '.$this->lang->line('site_title');   
            $data['Languages'] = $this->common_model->getLanguages();
            //email template count
            $this->db->select('content_id');
            $this->db->group_by('content_id');
            $data['template_count'] = $this->db->get('email_template')->num_rows();
            $this->load->view(ADMIN_URL.'/email_template',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function add() {
        if(in_array('email_template~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('titleadmin_email_template_add').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit") {
                $this->form_validation->set_rules('title', $this->lang->line('email').' '.$this->lang->line('title'), 'trim|required');
                $this->form_validation->set_rules('subject', $this->lang->line('email').' '.$this->lang->line('subject'), 'trim|required');
                $this->form_validation->set_rules('message', $this->lang->line('email_body'), 'trim|required');
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
                        $ContentID = $this->email_template_model->addData('content_general',$add_content);
                        $email_slug = slugify($this->input->post('title'),'email_template','email_slug');
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                        $email_slug = $this->input->post('email_slug');
                    }
                    $add_data = array(
                      'title'=>$this->input->post('title'),
                      'subject'=>$this->input->post('subject'),
                      'message'=>$this->input->post('message'),
                      'email_slug'=>$email_slug,
                      'content_id'=>$ContentID,
                      'language_slug'=>$this->uri->segment('4'),                  
                      'status'=>1
                    );         
                    $this->email_template_model->addData('email_template',$add_data);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added email template - '.$this->input->post('title'));
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/email_template/view');                     
                }
            }
            $content_id = ($this->uri->segment('5'))?$this->uri->segment('5'):'';
            $data['email_data'] = $this->email_template_model->getEditDetail('content_id',$content_id);
            $this->load->view(ADMIN_URL.'/email_template_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function edit() {
        if(in_array('email_template~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('titleadmin_email_template_edit').' | '.$this->lang->line('site_title');
            //check add role form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('title', $this->lang->line('email').' '.$this->lang->line('title'), 'trim|required');
                $this->form_validation->set_rules('subject', $this->lang->line('email').' '.$this->lang->line('subject'), 'trim|required');
                $this->form_validation->set_rules('message', $this->lang->line('email_body'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $edit_data = array(
                       'title'=>$this->input->post('title'),
                       'subject'=>$this->input->post('subject'),
                       'message'=>$this->input->post('message'),                          
                    );                    
                    $this->email_template_model->editDetail($edit_data,$this->input->post('entity_id'));
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited email template - '.$this->input->post('title'));
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/email_template/view'); 
                }
            }
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->email_template_model->getEditDetail('entity_id',$entity_id);
            $this->load->view(ADMIN_URL.'/email_template_add',$data);
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
        
        $sortfields = array(4=>'subject');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->email_template_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();     
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $value) {
            $deleteName = getModuleTilte($value['translations'],"title");
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $edit_active_access = (in_array('email_template~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('email_template~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableDetail('.$value['content_id'].','.$value['status'].')"  title="'.($value['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                ($value['status'])?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $template_editbtn = (in_array('email_template~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                    $template_editbtn .= '( '.$value['translations'][$lang->language_slug]['title'].' )';
                    $cusLan[] = $template_editbtn;
                }else{
                    $cusLan[] = (in_array('email_template~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
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
    // method to change status
    public function ajaxdisable() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->email_template_model->UpdatedStatus('email_template',$content_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $language_slug = $this->session->userdata('language_slug');
            $template_title = $this->email_template_model->getTemplateName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' email template - '.$template_title);
        }
    }
    // method for delete
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $template_title = $this->email_template_model->getTemplateName('',$entity_id,$language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted email template - '.$template_title);
        $this->email_template_model->ajaxDelete('email_template',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $template_title = $this->email_template_model->getTemplateName('',$content_id,$language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted email template - '.$template_title);
        $this->email_template_model->ajaxDeleteAll('email_template',$content_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
}
