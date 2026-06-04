<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Category extends CI_Controller { 
    public $controller_name = 'category';
    public $prefix = 'cg';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/category_model');
    }
    //view data
    public function view() {
        if(in_array('category~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('menu_category').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages(); 
            $data['restaurant_admins'] = $this->category_model->get_restaurant_admins();
            //category count
            $this->db->select('content_id');
            $this->db->group_by('content_id');
            $data['category_count'] = $this->db->get('category')->num_rows();
            $this->load->view(ADMIN_URL.'/category',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //add data
    public function add() {
        if(in_array('category~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('add').' '.$this->lang->line('menu').' '.$this->lang->line('title_category').' | '.$this->lang->line('site_title');

            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('cat_name'), 'trim|required|callback_checkCatNameExist');
                //$this->form_validation->set_rules('sequence', $this->lang->line('category_sequence'), 'trim|numeric|callback_checkExist');
                if ($this->form_validation->run())
                {
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->category_model->addData('content_general',$add_content);
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                    }
                    $add_data = array(                   
                        'name'=>$this->input->post('name'),
                        'sequence'=>($this->input->post('sequence'))?$this->input->post('sequence'):0,
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'status'=>1,
                        // 'deal_category'=>($this->input->post('deal_category'))?$this->input->post('deal_category'):0,
                        'created_by' => $this->session->userdata('AdminUserID')
                    ); 
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/category';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/category')) {
                          @mkdir('./uploads/category', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/category/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "category/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error'])){
                        $this->category_model->addData('category',$add_data); 
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added category - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');  
                    }         
                }
            }
            $this->load->view(ADMIN_URL.'/category_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit data
    public function edit() {
        if(in_array('category~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('menu').' '.$this->lang->line('title_category').' | '.$this->lang->line('site_title');
            //check add form is submit
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('cat_name'), 'trim|required|callback_checkCatNameExist');
                //$this->form_validation->set_rules('sequence', $this->lang->line('category_sequence'), 'trim|numeric|callback_checkExist');
                if ($this->form_validation->run())
                {
                    $updateData = array(                   
                        'name'=>$this->input->post('name'),
                        // 'deal_category'=>($this->input->post('deal_category'))?$this->input->post('deal_category'):0,
                        'sequence'=> ($this->input->post('sequence'))?$this->input->post('sequence'):0,
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('AdminUserID')
                    ); 
                    
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/category';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/category')) {
                          @mkdir('./uploads/category', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/category/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $updateData['image'] = "category/".$img['file_name'];   
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
                        $this->category_model->updateData($updateData,'category','entity_id',$this->input->post('entity_id'));
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited category - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                    }
                      
                }
            }        
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->category_model->getEditDetail($entity_id);
            $this->load->view(ADMIN_URL.'/category_add',$data);
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
        $grid_data = $this->category_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
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
            $doc = "'".@$val->image."'";
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) {
                $edit_active_access .= (in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$doc.','.$msgDelete.','.$value['is_masterdata'].')" '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>':'';
                $edit_active_access .= (in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].','.$value['is_masterdata'].')" '.$btndisable_master.' title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            } else if($value['created_by'] == $this->session->userdata('AdminUserID')) {
                $edit_active_access .= (in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$doc.','.$msgDelete.','.$value['is_masterdata'].')" '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
                $edit_active_access .= (in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].','.$value['is_masterdata'].')" '.$btndisable_master.' title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            } else {
                $edit_active_access .= '-';
            }
            if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || ($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')){
                $records["aaData"][] = array(
                    $nCount,
                    '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">
                    <input type="hidden" name="id" class="hidden-id" value="'.$value['content_id'].'">',
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    $edit_active_access
                );
            }else{
                $records["aaData"][] = array(
                    $nCount.'<input type="hidden" name="id" class="hidden-id" value="'.$value['content_id'].'">',
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    $edit_active_access
                ); 
            } 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(in_array('category~add',$this->session->userdata("UserAccessArray")) || in_array('category~edit',$this->session->userdata("UserAccessArray"))) {
                    if(array_key_exists($lang->language_slug,$value['translations'])){
                        $category_name_edit_btn = '';
                        $category_name_edit_btn .= (in_array('category~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $category_name_edit_btn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $category_name_edit_btn;
                    }else{
                        $cusLan[] = (in_array('category~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                    }          
                } else if($value['created_by'] == $this->session->userdata('AdminUserID')) {
                    if(array_key_exists($lang->language_slug,$value['translations'])){
                        $category_name_edit_disable_btn = '';
                        $category_name_edit_disable_btn .= (in_array('category~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>':'';
                        $category_name_edit_disable_btn .= (in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<a style="cursor:pointer;" onclick="disable_record('.$value['translations'][$lang->language_slug]['translation_id'].','.$value['translations'][$lang->language_slug]['status'].')"  title="' .($value['translations'][$lang->language_slug]['status']?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').'"><i class="fa fa-toggle-'.($value['translations'][$lang->language_slug]['status']?'on':'off').'"></i> </a>': '';
                        $category_name_edit_disable_btn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';

                        $cusLan[] = $category_name_edit_disable_btn;
                    }else{
                        $cusLan[] = (in_array('category~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
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
            if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || ($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')){
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
    // method for deleting a category
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $category_name = $this->category_model->getCategoryName($entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted category - '.$category_name);
        $this->category_model->ajaxDelete('category',$this->input->post('content_id'),$entity_id);
        @unlink(FCPATH.'uploads/'.$Image);
    }
    public function ajaxDeleteAll(){
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $language_slug = $this->session->userdata('language_slug');
        $category_name = $this->category_model->getCategoryName('',$content_id,$language_slug);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted category - '.$category_name);
        $this->category_model->ajaxDeleteAll('category',$content_id);
        @unlink(FCPATH.'uploads/'.$Image);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->category_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
            $category_name = $this->category_model->getCategoryName($entity_id);
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' category - '.$category_name);
        }
    }
    // Update status for All
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->category_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $language_slug = $this->session->userdata('language_slug');
            $category_name = $this->category_model->getCategoryName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' category - '.$category_name);
        }
    }
    public function activeDeactiveMultiCat(){
        $cat_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        $flag = $this->input->post('flag');
        if($cat_content_id){
            $content_id = explode(',', $cat_content_id);
            $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'category',1);
            $status_txt = '';
            if($flag == 'active') {
                $status_txt = 'activated';
            } else if($flag == 'deactive') {
                $status_txt = 'deactivated';
            }
            if(count($content_id) == 1) {
                $language_slug = $this->session->userdata('language_slug');
                $category_name = $this->category_model->getCategoryName('',$content_id[0],$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' category - '.$category_name);
            } else {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple categories');
            }
            echo json_encode($data);
        }
    }
    //check Category Sequence number already alotted  to other category
    public function checkExist(){
        $sequence = ($this->input->post('sequence') != '')?$this->input->post('sequence'):'';
        if($this->input->post('name')){
            if($mobile_number != ''){
                $check = $this->category_model->checkExist($sequence,$this->input->post('entity_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('sequence_exist_msg'));
                    return false;
                }
            } 
        }else{
            if($sequence != ''){
                $check = $this->category_model->checkExist($sequence,$this->input->post('entity_id'));
                echo $check;
            } 
        }       
    }
    //Ajax Reorder
    public function ajaxReorder()
    {
        $dataid = ($this->input->post('dataid') != '')?$this->input->post('dataid'):'';  
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?$this->input->post('restaurant_owner_id'):$this->session->userdata('AdminUserID');
        $restaurant_entity_id = ($this->input->post('restaurant_entity_id') != '') ? $this->input->post('restaurant_entity_id') : 0;
        $restaurant_content_id = ($restaurant_entity_id > 0) ? $this->common_model->getContentId($restaurant_entity_id,'restaurant') : 0;
        if(!empty($dataid) && $restaurant_content_id > 0)
        {
            $menu_category_seqmap = array();
            $sequence_no =1;
            foreach($dataid as $key => $value) {
                $menu_category_seqmap[] = array(
                    'restaurant_owner_id'=>$restaurant_owner_id,
                    'restaurant_content_id' => $restaurant_content_id,
                    'category_content_id'=>$value,
                    'sequence_no'=>$sequence_no
                );
                $sequence_no++;
            }
            $data = $this->category_model->insertBatch('menu_category_sequencemap',$menu_category_seqmap,$restaurant_owner_id,$restaurant_content_id);
        }
    }
    public function checkCatNameExist(){
        $category_name = ($this->input->post('name') != '')?trim($this->input->post('name')):'';
        $category_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        if($call_from == 'CI_callback'){
            if($category_name){
                $check = $this->category_model->checkCatNameExist($category_name,$category_entity_id,$language_slug);
                if($check > 0){
                    $this->form_validation->set_message('checkCatNameExist', $this->lang->line('category_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }else{
            if($category_name){
                $check = $this->category_model->checkCatNameExist($category_name,$category_entity_id,$language_slug);
                echo $check;
            } 
        }       
    }
    public function script_to_handle_duplicate_category()
    {
        /*
        - existing category count including duplicate entry :: 2231
        - actual category count should be :: 1949 
        - total duplicate entries :: 282
        - tables to take care of :: category, coupon_category_map, restaurant_menu_item, category_sequencemap, content_general
        */
        $this->db->select('name,entity_id,content_id');
        $this->db->order_by('name','ASC');
        $this->db->order_by('entity_id','ASC');
        $all_categories = $this->db->get('category')->result_array();
        $cat_names = array_column($all_categories, 'name');
        
        //get all duplicate category names
        $counts = array_count_values(array_map('strtolower', $cat_names));
        
        $duplicate_category = array();
        $dupe_cat = array_filter($all_categories, function ($value) use ($counts) {
            if($counts[trim(strtolower($value['name']))] > 1){
                return $value;
            }
        });
        
        foreach ($dupe_cat as $dup_key => $dup_value) {
            $dup_value['name'] = strtolower($dup_value['name']);
            array_push($duplicate_category, $dup_value);
        }
        $unique_arr_for_duplicate_cat = array();
        $tempArr = array_unique(array_column($duplicate_category, 'name'));
        $temp_final_arr = array_intersect_key($duplicate_category, $tempArr);
        foreach ($temp_final_arr as $temp_key => $temp_value) {
            array_push($unique_arr_for_duplicate_cat, $temp_value);
        }

        $duplicate_count=0;
        $dupe_cat_content_ids = array();
        foreach ($unique_arr_for_duplicate_cat as $key => $value) {
            $this->db->select('entity_id, content_id, name');
            $this->db->where('name',$value['name']);
            $this->db->order_by('entity_id','ASC');
            $check = $this->db->get('category')->result();

            if(!empty($check) && count($check) > 1){
                $original_cat_id = 0;
                $original_cat_content_id = 0;
                foreach ($check as $chkkey => $chkvalue) {
                    if($chkkey == 0){
                        $original_cat_id = $chkvalue->entity_id;
                        $original_cat_content_id = $chkvalue->content_id;
                    }
                    if($chkkey>0){
                        $duplicate_count++;
                        array_push($dupe_cat_content_ids, $chkvalue->content_id);
                        //restaurant_menu_item
                        if($original_cat_id>0){
                            $res_menu_cat = array('category_id'=>$original_cat_id);
                            $this->db->where('category_id',$chkvalue->entity_id);
                            $this->db->update('restaurant_menu_item',$res_menu_cat);
                            $return = $this->db->affected_rows();
                        }
                        //category_sequencemap
                        if($original_cat_content_id > 0){
                            $this->db->where('category_content_id',$original_cat_content_id);
                            $check_original_id = $this->db->get('menu_category_sequencemap')->first_row();

                            $this->db->where('category_content_id',$chkvalue->content_id);
                            $check_duplicate_id = $this->db->get('menu_category_sequencemap')->first_row();

                            if(!empty($check_original_id) && !empty($check_duplicate_id)){
                                if($check_duplicate_id->restaurant_owner_id == $check_original_id->restaurant_owner_id){
                                    $this->db->where('category_content_id',$chkvalue->content_id);
                                    $this->db->delete('menu_category_sequencemap');
                                } else {
                                    $cat_sequence_map = array('category_content_id'=>$original_cat_content_id);
                                    $this->db->where('category_content_id',$chkvalue->content_id);
                                    $this->db->update('menu_category_sequencemap',$cat_sequence_map);
                                    $return = $this->db->affected_rows();
                                }
                            } else {
                                if(empty($check_original_id)){
                                    $cat_sequence_map = array('category_content_id'=>$original_cat_content_id);
                                    $this->db->where('category_content_id',$chkvalue->content_id);
                                    $this->db->update('menu_category_sequencemap',$cat_sequence_map);
                                    $return = $this->db->affected_rows();   
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($dupe_cat_content_ids)){
            //category
            $this->db->where_in('content_id',$dupe_cat_content_ids);
            $this->db->delete('category');
            //content_general
            $this->db->where_in('content_general_id',$dupe_cat_content_ids);
            $this->db->where('content_type','category');
            $this->db->delete('content_general');
            //echo '<pre>total duplicate entries :: '; print_r(count($dupe_cat_content_ids)); echo '<br>';
            echo 'done!'; exit;
        }
    }
    public function getMenu_withno_category()
    {
        $this->db->select('menu.entity_id as menu_id, menu.name as menu_name,category_id');
        $this->db->order_by('category_id','ASC');
        $all_menus = $this->db->get('restaurant_menu_item as menu')->result();

        $this->db->select('name,entity_id,content_id');
        $this->db->order_by('entity_id','ASC');
        $all_categories = $this->db->get('category')->result_array();

        $cat_ids = array_column($all_categories, 'entity_id');
        $menus_without_categories = array();
        $missing_category_ids = array();
        foreach ($all_menus as $key => $value) {
            if(!in_array($value->category_id, $cat_ids)){
                array_push($missing_category_ids, $value->category_id);
                array_push($menus_without_categories, $value);
            }
        }
        $json_string = json_encode($menus_without_categories, JSON_PRETTY_PRINT);
        $json_stringfor_missing_cat_ids = json_encode(array_values(array_unique($missing_category_ids)), JSON_PRETTY_PRINT);

        echo '<pre>total menu :: '; print_r(count($menus_without_categories));  echo '<br>';
        echo '<pre>missing category ids :: '; print_r($json_stringfor_missing_cat_ids);  echo '<br>';
        echo '<pre>menu list :: '; print_r($json_string); exit;
    }
}