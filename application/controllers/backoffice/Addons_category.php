<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Addons_category extends CI_Controller { 
    public $controller_name = 'addons_category';
    public $prefix = 'acg';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/addons_category_model');
    }
    //view data
    public function view() {
        if(in_array('addons_category~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('addons_category').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();     
            $data['restaurant_admins'] = $this->addons_category_model->get_restaurant_admins();
            //addons category count
            $this->db->select('content_id');
            $this->db->group_by('content_id');
            $data['addons_category_count'] = $this->db->get('add_ons_category')->num_rows();
            $this->load->view(ADMIN_URL.'/addons_category',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //add data
    public function add() {
        if(in_array('addons_category~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('title_addons_category').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('addons_category').' '.$this->lang->line('name'), 'trim|required|callback_checkAddonCatNameExist');
                if ($this->form_validation->run())
                {
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->addons_category_model->addData('content_general',$add_content);
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                    }
                    $add_data = array(                   
                        'name'=>preg_replace('!\s+!', ' ', $this->input->post('name')),
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'status'=>1,
                        'created_by' => $this->session->userdata('AdminUserID')
                    ); 
                    $this->addons_category_model->addData('add_ons_category',$add_data); 
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added Add-ons Category - '.$this->input->post('name'));
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                }
            }
            $this->load->view(ADMIN_URL.'/addons_category_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit data
    public function edit() {
        if(in_array('addons_category~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('title_addons_category').' | '.$this->lang->line('site_title');
            //check add form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('addons_category').' '.$this->lang->line('name'), 'trim|required|callback_checkAddonCatNameExist');
                if ($this->form_validation->run())
                {
                    $updateData = array(                   
                        'name'=>preg_replace('!\s+!', ' ', $this->input->post('name')),
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('AdminUserID')
                    ); 
                    $this->addons_category_model->updateData($updateData,'add_ons_category','entity_id',$this->input->post('entity_id'));
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited Add-ons Category - '.$this->input->post('name'));
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                      
                }
            }        
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->addons_category_model->getEditDetail($entity_id);
            $this->load->view(ADMIN_URL.'/addons_category_add',$data);
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
        
        $sortfields = array(1=>'name','2'=>'status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->addons_category_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '';
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $edit_active_access .= (in_array('addons_category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$msgDelete.','.$value['is_masterdata'].')" '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('addons_category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].','.$value['is_masterdata'].')" '.$btndisable_master.' title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">
                <input type="hidden" name="id" class="hidden-id" value="'.$value['content_id'].'">',
                ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])) {
                    $addonscat_editbtn = (in_array('addons_category~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                    $addonscat_editbtn .= ' ( '.$value['translations'][$lang->language_slug]['name'].' )';
                    $cusLan[] = $addonscat_editbtn;
                }else{
                    $cusLan[] = (in_array('addons_category~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                }
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
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
        $this->addons_category_model->ajaxDelete('add_ons_category',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $language_slug = $this->session->userdata('language_slug');
        $addons_name = $this->addons_category_model->getAddonsCategoryName('',$content_id,$language_slug);
        $this->addons_category_model->ajaxDeleteAll('add_ons_category',$content_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted Add-ons Category - '.$addons_name);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->addons_category_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
        }
    }
    /*
     * Update status for All
     */
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->addons_category_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
            $language_slug = $this->session->userdata('language_slug');
            $addons_name = $this->addons_category_model->getAddonsCategoryName('',$content_id,$language_slug);
            if($this->input->post('status') == 0){
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' Add-ons Category - '.$addons_name);
        }
    }
    public function activeDeactiveMultiAddonsCat(){
        $addonscat_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        $flag = $this->input->post('flag');
        if($addonscat_content_id){
            $content_id = explode(',', $addonscat_content_id);
            $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'add_ons_category',1);
            $status_txt = '';
            if($flag == 'deactive') {
                $status_txt = 'deactivated';
            } else {
                $status_txt = 'activated';
            }
            if(count($content_id) == 1) {
                $language_slug = $this->session->userdata('language_slug');
                $addons_name = $this->addons_category_model->getAddonsCategoryName('',$content_id[0],$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' Add-ons Category - '.$addons_name);
            } else {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple Add-ons Category');
            }
            echo json_encode($data);
        }
    }
    //Ajax Reorder
    public function ajaxReorder()
    {
        $dataid = ($this->input->post('dataid') != '')?$this->input->post('dataid'):'';
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?$this->input->post('restaurant_owner_id'):$this->session->userdata('AdminUserID');
        $restaurant_entity_id = ($this->input->post('restaurant_entity_id') != '') ? $this->input->post('restaurant_entity_id') : 0;;
        $restaurant_content_id = ($restaurant_entity_id > 0) ? $this->common_model->getContentId($restaurant_entity_id,'restaurant') : 0;
        if(!empty($dataid) && $restaurant_content_id > 0)
        {
            $menu_addon_seqmap = array();
            $sequence_no =1;
            foreach($dataid as $key => $value) {
                $menu_addon_seqmap[] = array(
                    'restaurant_owner_id'=>$restaurant_owner_id,
                    'restaurant_content_id' => $restaurant_content_id,
                    'add_ons_content_id'=>$value,
                    'sequence_no'=>$sequence_no
                );
                $sequence_no++;
            }            
            $data = $this->addons_category_model->insertBatch('menu_addons_sequencemap',$menu_addon_seqmap,$restaurant_owner_id,$restaurant_content_id);
        }
    }
    public function checkAddonCatNameExist(){
        $category_name = ($this->input->post('name') != '')?trim($this->input->post('name')):'';
        $category_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        if($call_from == 'CI_callback'){
            if($category_name){
                $check = $this->addons_category_model->checkAddonCatNameExist($category_name,$category_entity_id,$language_slug);
                if($check > 0){
                    $this->form_validation->set_message('checkAddonCatNameExist', $this->lang->line('addons_category_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }else{
            if($category_name){
                $check = $this->addons_category_model->checkAddonCatNameExist($category_name,$category_entity_id,$language_slug);
                echo $check;
            } 
        }       
    }
}