<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Recipe extends CI_Controller {
    public $controller_name = 'recipe';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->model(ADMIN_URL.'/recipe_model');
        $this->load->library('form_validation');
    }
    public function view() {
        if(in_array('recipe~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('recipes').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            $this->load->view(ADMIN_URL.'/recipe',$data);
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
        $sortfields = array(4 => 'recipe.status', 5 => 'recipe.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->recipe_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        
        $Languages = $this->common_model->getLanguages();
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '') ? $displayStart+1 : 1;
        foreach ($grid_data['data'] as $key => $value)
        {
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';                    
            //Code for allow add/edit/delete permission :: End

            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $edit_active_access = (in_array('recipe~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="deleteAll(\''.$value['content_id'].'\','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('recipe~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="disableAll(\''.$value['content_id'].'\',\''.$value['status'].'\')"  title="'.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                ($value['status'] == 1) ? $this->lang->line('active') : $this->lang->line('inactive'),
                $edit_active_access
            );
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $recipe_editbtn = (in_array('recipe~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                    $recipe_editbtn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                    $cusLan[] = $recipe_editbtn;
                }else{
                    $cusLan[] = (in_array('recipe~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
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
    public function add(){
        if(in_array('recipe~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_recipe_add').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {   
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('ingredients', $this->lang->line('ingredients'), 'trim|required');
                $this->form_validation->set_rules('recipe_detail', $this->lang->line('recipe_detail'), 'trim|required');
                // $this->form_validation->set_rules('food_type', $this->lang->line('food_type'), 'trim|required');
                $this->form_validation->set_rules('detail', $this->lang->line('detail'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if(!$this->input->post('content_id')){
                        $add_content = array(
                            'content_type'=>$this->uri->segment('2'),
                            'created_by'=>$this->session->userdata("AdminUserID"),
                            'created_date'=>date('Y-m-d H:i:s'),
                            'updated_date'=>date('Y-m-d H:i:s'),
                            'updated_by'=>$this->session->userdata("AdminUserID"),
                        );
                        $content_id = $this->recipe_model->add_data('content_general',$add_content);
                        $recipe_slug = slugify($this->input->post('name'),'recipe','slug');
                    }else{                    
                        $content_id = $this->input->post('content_id');
                        $slug = $this->recipe_model->get_recipe_slug($this->input->post('content_id'));
                        $recipe_slug = $slug->slug;
                    }
                    $add_data = array(
                        'name' => $this->input->post('name'),
                        'slug' => $recipe_slug,
                        'detail' =>$this->input->post('detail'),
                        'ingredients' => $this->input->post('ingredients'),
                        'recipe_detail' => $this->input->post('recipe_detail'),
                        'recipe_time' => $this->input->post('recipe_time'),
                        'content_id' => $content_id,
                        'language_slug' => $this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type' => $this->input->post("food_type"),
                        'youtube_video' => $this->input->post('youtube_video'),
                        'meta_title' => ($this->input->post('meta_title')!='')?trim($this->input->post('meta_title')):'',
                        'meta_description' => ($this->input->post('meta_description')!='')?trim($this->input->post('meta_description')):''
                    );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/recipe';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['max_size'] = '500'; //in KB
                        $config['encrypt_name'] = TRUE;
                        // create directory if not exists
                        if (!@is_dir('uploads/recipe')) {
                          @mkdir('./uploads/recipe', 0777, TRUE);
                        }
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/recipe/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "recipe/".$img['file_name'];
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    $entity_id = '';
                    if(empty($data['Error'])){
                        $entity_id = $this->recipe_model->add_data('recipe',$add_data);
                        $menu_content_id = ($this->input->post('menu') != '')?$this->input->post('menu'):'';
                        if(intval($menu_content_id)>0){
                            $rest_menu_recipe_map = array();
                            //foreach ($menu_content_id as $key => $value) {
                                $rest_menu_recipe_map[] = array(
                                    'menu_content_id'=>$menu_content_id,
                                    'recipe_content_id'=> $content_id
                                );
                            //}
                        $map_id = $this->recipe_model->insertBatch('restaurant_menu_recipe_map',$rest_menu_recipe_map,$content_id);
                        
                        }
                        //add user log
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added Recipe - '.$this->input->post('name'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
                    }
                }
            }
            $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
            $data['menu_item'] = $this->recipe_model->get_menu($language_slug);
            $data['food_typearr'] = $this->recipe_model->get_food_type('food_type',$language_slug);
            $this->load->view(ADMIN_URL.'/recipe_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function edit(){
        if(in_array('recipe~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_recipe_edit').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {   
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('ingredients', $this->lang->line('ingredients'), 'trim|required');
                $this->form_validation->set_rules('recipe_detail', $this->lang->line('recipe_detail'), 'trim|required');
                // $this->form_validation->set_rules('food_type', $this->lang->line('food_type'), 'trim|required');
                $this->form_validation->set_rules('detail', $this->lang->line('detail'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                { 
                    $content_id = $this->recipe_model->get_content_id($this->input->post('entity_id'),'recipe');
                    $slug = $this->recipe_model->get_recipe_slug($this->input->post('content_id'));
                    if (!empty($slug->slug)) { 
                        $recipe_slug = $slug->slug;
                    }
                    else
                    {
                        $recipe_slug = slugify($this->input->post('name'),'recipe','slug','content_id',$content_id->content_id);
                    }
                    $edit_data = array(                  
                        'name' => $this->input->post('name'),
                        'slug' => $recipe_slug,
                        'detail' =>$this->input->post('detail'),
                        'ingredients' => $this->input->post('ingredients'),
                        'recipe_detail' => $this->input->post('recipe_detail'),
                        'recipe_time' => $this->input->post('recipe_time'),
                        'language_slug' => $this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type' => $this->input->post("food_type"),
                        'youtube_video' => $this->input->post('youtube_video'),
                        'updated_by' => $this->session->userdata('AdminUserID'),
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'meta_title' => ($this->input->post('meta_title')!='')?trim($this->input->post('meta_title')):'',
                        'meta_description' => ($this->input->post('meta_description')!='')?trim($this->input->post('meta_description')):''
                    );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/recipe';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/recipe')) {
                          @mkdir('./uploads/recipe', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/recipe/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $edit_data['image'] = "recipe/".$img['file_name'];   
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
                    if(empty($data['Error']))
                    {
                        $this->recipe_model->update_data($edit_data,'recipe','entity_id',$this->input->post('entity_id'));
                        $menu_content_id = ($this->input->post('menu') != '')?$this->input->post('menu'):'';
                        
                        if(intval($menu_content_id)>0){
                            $rest_menu_recipe_map = array();
                            //foreach ($menu_content_id as $key => $value) {
                                $rest_menu_recipe_map[] = array(
                                    'menu_content_id'=>$menu_content_id,
                                    'recipe_content_id'=>$this->input->post('recipe_content')
                                );
                            //}                            
                        $map_id = $this->recipe_model->insertBatch('restaurant_menu_recipe_map',$rest_menu_recipe_map,$this->input->post('recipe_content'));                        
                        }
                        else{
                            
                            $this->recipe_model->DeleteMenuContent($this->input->post('recipe_content'));
                        }
                        //add user log
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited Recipe - '.$this->input->post('name'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
                    }
                         
                }
            }
            $entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))) : $this->input->post('entity_id');
            $data['edit_records'] = $this->recipe_model->get_edit_detail('recipe',$entity_id);
            $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
            $data['menu_item'] = $this->recipe_model->get_menu($language_slug,$data['edit_records']->content_id);            
            $restaurant = array_values($data['menu_item']);
            usort($restaurant, function($a, $b) {
            return $b->name < $a->name;
            });
            $data['menu_item'] = $restaurant;
            
            $data['menu_item_arr'] = $this->recipe_model->getMenuItem($data['edit_records']->content_id);
            $data['food_typearr'] = $this->recipe_model->get_food_type('food_type',$language_slug);
            $this->load->view(ADMIN_URL.'/recipe_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function ajaxDelete() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        //add user log
        $recipe_data = $this->common_model->getSingleRowMultipleWhere('recipe',array('content_id'=>$entity_id,'language_slug'=>$this->session->userdata('language_slug')) );
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted Recipe - '.$recipe_data->name);
        $this->recipe_model->ajaxDelete('recipe',$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        //delete image
        $Images = $this->common_model->getImages('recipe',$content_id);
        foreach ($Images as $key => $value) {
            if(file_exists(FCPATH.'uploads/'.$value['image']))
            {
                @unlink(FCPATH.'uploads/'.$value['image']); 
            }
        }
        //add user log
        $recipe_data = $this->common_model->getSingleRowMultipleWhere('recipe',array('content_id'=>$content_id,'language_slug'=>$this->session->userdata('language_slug')) );
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted Recipe - '.$recipe_data->name);

        $this->recipe_model->ajax_delete_all('recipe',$content_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->recipe_model->updated_status_all('recipe',$content_id,$this->input->post('status'));
            //add user log
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            $recipe_data = $this->common_model->getSingleRowMultipleWhere('recipe',array('content_id'=>$content_id,'language_slug'=>$this->session->userdata('language_slug')) );
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' Recipe - '.$recipe_data->name);
        }
    }
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->recipe_model->updated_status('recipe',$entity_id,$this->input->post('status'));
        }
    }
}