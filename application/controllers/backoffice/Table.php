<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Table extends CI_Controller { 
    public $controller_name = 'table';
    public $prefix = '_tb';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');        
        require_once APPPATH."libraries/phpqrcode/qrlib.php";
        $this->load->helper('url');
        $this->load->model(ADMIN_URL.'/restaurant_model');
        $this->load->model(ADMIN_URL.'/table_model');
        redirect(ADMIN_URL.'/home');
    }
    //view data
    public function view() {
        if(in_array('table~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_tables').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();     
            $this->load->view(ADMIN_URL.'/table',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function reservation_request_view(){
        $data['meta_title'] = $this->lang->line('title_reservation_request').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL.'/reservation_request',$data);
    }
    public function reservation_view(){
        if(in_array('table~reservation_view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('currentreservation_lists').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            $this->load->view(ADMIN_URL.'/reservation_list',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function pastreservation_view(){
        if(in_array('table~pastreservation_view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('pastreservation_lists').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            $this->load->view(ADMIN_URL.'/pastreservation_list',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //check data
    function check_table($field1,$field2) {
        $this->db->select('table_number');
        $this->db->from('table_master');
        $this->db->where('table_number', $field1);
        $this->db->where('resto_entity_id', $field2);
        $query = $this->db->get();
        $num = $query->num_rows();
        if ($num > 0) {
            $this->form_validation->set_message('check_table', 'Table number exist with this restaurant');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    //add data
    public function add() {
        if(in_array('table~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_tableadd').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
                $this->form_validation->set_rules('table_no', 'Table Number', 'trim|numeric|required|callback_checkTableNameExist');
                $this->form_validation->set_rules('capacity', 'Capacity', 'trim|numeric|required');
                $table_no = $this->input->post('table_no');
                $restaurant_id = $this->input->post('restaurant_id');
                $this->form_validation->set_rules('table_no', 'Table Number', 'trim|required|callback_check_table['.$restaurant_id.']');// call callback function
                
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>'table',
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->table_model->addData('content_general',$add_content);
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                    }
                    $add_data = array(                  
                        'table_number'=>$this->input->post('table_no'),
                        'resto_entity_id' =>$this->input->post('restaurant_id'),
                        'capacity' =>($this->input->post('capacity'))?$this->input->post('capacity'):NULL,
                        'content_id'=>$ContentID,
                        'language_slug'=>($this->uri->segment('4'))?$this->uri->segment('4'):'en',
                    ); 
                    if(empty($data['Error'])){
                        $table_id = $this->table_model->addData('table_master',$add_data);
                        $restaurant = $this->common_model->getSingleRowMultipleWhere('restaurant',array('content_id'=>$this->input->post('restaurant_id'),'language_slug'=>'en' ) );
                        $data = $restaurant->name.'-'.$this->input->post('table_no');
                        $path = $this->qrcodeGenerator($table_id,$data);
                        $updateData = array(                   
                            'qr_code'=> $path,
                        ); 
                        $this->table_model->updateData($updateData,'table_master','entity_id',$table_id);
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');               
                    }                                        
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);            
            $this->load->view(ADMIN_URL.'/table_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit data
    public function edit() {
        if(in_array('table~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_tableedit').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {  
                $this->form_validation->set_rules('capacity','Capacity', 'trim|required');
                $this->form_validation->set_rules('table_no', 'Table Number', 'trim|required|callback_checkTableNameExist');
                $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
                $this->form_validation->set_rules('qr_code', 'QR Code', 'trim|required');
                $table_no = $this->input->post('table_no');
                $restaurant_id = $this->input->post('restaurant_id');
                $this->form_validation->set_rules('table_no', 'Table Number', 'trim|required|callback_check_table['.$restaurant_id.']');// call callback function
                //check form validation using codeigniter
                if ($this->form_validation->run()) { 
                    $edit_data = array(                  
                        'table_number'=>$this->input->post('table_no'),
                        'resto_entity_id' =>$this->input->post('restaurant_id'),
                        'capacity' =>$this->input->post('capacity'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    if(empty($data['Error'])){                  
                        $entity_id = ($this->uri->segment(5))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
                        $this->table_model->updateData($edit_data,'table_master','entity_id',(int)$entity_id);
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');       
                    }    
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $records = $this->table_model->getEditDetail($entity_id);
            $data['edit_records']->table_no = $records->table_number;
            $data['edit_records']->entity_id = $records->entity_id;
            $data['edit_records']->restaurant_id = $records->resto_entity_id;
            $data['edit_records']->capacity = $records->capacity;
            $data['edit_records']->qr_code = $records->qr_code;
            $data['edit_records']->content_id = $records->content_id;
            $data['edit_records']->language_slug = $records->language_slug;
            $data['edit_records']->created_at = $records->created_at;
            $data['edit_records']->updated_at = $records->updated_at;
            $this->load->view(ADMIN_URL.'/table_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view table
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'res.name',3=>'cast(table.table_number as UNSIGNED)',4=>'table.capacity');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->table_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo '<pre>'; print_r($grid_data); exit;
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        foreach ($grid_data['data'] as $key => $value)
        {
            $deleteName = addslashes($value['restaurant']);
            $table_no = addslashes($value['table_no']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_table')),$table_no,$deleteName)."'";
            $qr_code = (file_exists(FCPATH.'uploads/'.$value['qr_code']) && $value['qr_code']!='') ? '<img id="qr_code" class="sliderimg" width="70" src="'.base_url().'uploads/'.$value['qr_code'].'">' : '';
            $total = 0;
            $edit_active_access = (in_array('table~download_qrcode',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().'uploads/'.$value['qr_code'].'"  title="'.$this->lang->line('download').' '.$this->lang->line('qr').'" class="btn btn-sm margin-bottom red theme-btn" download><i class="fa fa-download"></i></a>' : '';
            $edit_active_access .= (in_array('table~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll(\''.$value['content_id'].'\',\''.$value['status'].'\')"  title="'.($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $edit_active_access .= (in_array('table~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll(\''.$value['content_id'].'\','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm danger-btn theme-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $value['restaurant'],
                $qr_code,
                $value['table_no'],
                $value['capacity'],
                $edit_active_access,
            ); 
            $cusLan = array();           
            // add multilanguage column to specific position in table
            array_splice( $records["aaData"][$cnt], 3, 0, $cusLan); // it will place after 4th column 
            $cnt++;
            $nCount++;
        }                
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //ajax view reservation request
    public function ajaxReservationRequestview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'cast(table.table_no as UNSIGNED)',3=>'res.name');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->table_model->getReservationRequestGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo '<pre>'; print_r($grid_data); exit;
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        foreach ($grid_data['data'] as $key => $value) {
            $total = 0;
            $edit_active_access = '<button onclick="approveAll('.$value['content_id'].')"  title="'.$this->lang->line('click_approve').'" class="btn btn-sm margin-bottom red theme-btn"><i class="fa fa-check"></i> '.$this->lang->line('approve').'</button>';
            $edit_active_access .= '<button onclick="deleteAll('.$value['content_id'].')"  title="'.$this->lang->line('click_reject').'" class="btn btn-sm margin-bottom red theme-btn"><i class="fa fa-close"></i> '.$this->lang->line('reject').'</button>';
            //$price = ($total && $total > 0)?"<strike>".number_format_unchanged_precision($value['price'])."</strike> ".number_format_unchanged_precision($total):number_format_unchanged_precision($value['price']);
            $records["aaData"][] = array(
                $nCount,
                $value['table_no'],
                $value['restaurant'],
                $edit_active_access,
            ); 
            $cusLan = array();
            // foreach ($Languages as $lang) { 
            //     if(array_key_exists($lang->language_slug,$value['translations'])){
            //         // var_dump($value['translations'][$lang->language_slug]['translation_id']);
            //         $cusLan[] = '( '.$value['translations'][$lang->language_slug]['table_no'].' )';
            //     }else{
            //         $cusLan[] = '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
            //     }                    
            // }
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
    //ajax view reservation list
    public function ajaxReservationview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'cast(table.table_number as UNSIGNED)', 2=>'cast(table.capacity as UNSIGNED)',3=>'ord.entity_id',4=>'users.first_name',5=>'res.name',6=>'tb.created_at');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->table_model->getReservationGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        foreach ($grid_data['data'] as $key => $value) {

            //Code for Order status :: Start
            if($value['order_status'] == "placed" && $value['ostatus']!='1')
            {
                $ostatuslng = $this->lang->line('placed');
            }
            if(($value['order_status'] == "placed" && $value['ostatus']=='1') || $value['order_status'] == "accepted")
            {
                $ostatuslng = $this->lang->line('accepted');
            }
            if($value['order_status'] == "cancel"){
                $ostatuslng = $this->lang->line('cancel');
            }
            if($value['order_status'] == "complete"){
                $ostatuslng = $this->lang->line('complete');
            }            
            if($value['order_status'] == "ready"){
                $ostatuslng = $this->lang->line('served');
            }            
            if($value['order_status'] == "onGoing")
            {
                $ostatuslng = $this->lang->line('ready');
            }            
            if($value['order_status'] == "preparing"){
                $ostatuslng = $this->lang->line('preparing');
            }
            if($value['order_status'] == "rejected"){
                $ostatuslng = $this->lang->line('rejected');
            }             
            //Code for Order status :: End

            $edit_active_access = (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button class="btn btn-sm  danger-btn theme-btn margin-bottom-5" onclick="openOrderDetails('.$value['order_id'].')" title="'.$this->lang->line('order_details').'"><i class="fa fa-eye"></i></button>' : '';

            $edit_active_access .= (in_array('order~edit_delivery_pickup_order_details',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm danger-btn theme-btn margin-bottom cart-btn" href="'.base_url().ADMIN_URL.'/order/edit_dinein_order_details/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])).'" title="'.$this->lang->line('edit').'" target="_blank"><i class="fa fa-cart-plus"></i><span class="notify blink"></span></a>&nbsp;' : '';

            $deleteName = addslashes($value['restaurant']);
            $table_no = addslashes($value['table_no']);
            $user_phn_no = ($value['user_phn_no'])?'('.$value['user_phn_no'].')':'';
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_reservation')),$table_no,$deleteName)."'";
            $total = 0;
            $edit_active_access .= (in_array('table~reservation_view',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll(\''.$value['table_status_id'].'\','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm danger-btn theme-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $value['table_no'],
                $value['capacity'],
                $value['order_id'],
                ($value['fname'] || $value['lname'])?$value['fname'].' '.$value['lname'].' '.$user_phn_no:'',
                $value['restaurant'],
                ($value['reservation_date'])?date('m/d/Y g:i A',strtotime($value['reservation_date'])):'',
                $this->lang->line('unpaid'),
                $ostatuslng,
                $edit_active_access,
            ); 
            $cusLan = array();
            // foreach ($Languages as $lang) { 
            //     if(array_key_exists($lang->language_slug,$value['translations'])){
            //         // var_dump($value['translations'][$lang->language_slug]['translation_id']);
            //         $cusLan[] = '<a style="cursor:pointer;" onclick="deleteDetail('.$value['translations'][$lang->language_slug]
            //         ['translation_id'].','.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'">
            //         <i class="fa fa-times"></i> </a>( '.$value['translations'][$lang->language_slug]['table_no'].' )';
            //     }else{
            //         $cusLan[] = '<a href="#" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
            //     }                    
            // }
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
    public function ajaxPastReservationview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'cast(table.table_number as UNSIGNED)', 2=>'cast(table.capacity as UNSIGNED)',3=>'ord.entity_id',4=>'users.first_name',5=>'res.name',6=>'tb.created_at');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->table_model->getReservationGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,'past');        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        foreach ($grid_data['data'] as $key => $value) {

            //Code for Order status :: Start
            if($value['order_status'] == "placed" && $value['ostatus']!='1')
            {
                $ostatuslng = $this->lang->line('placed');
            }
            if(($value['order_status'] == "placed" && $value['ostatus']=='1') || $value['order_status'] == "accepted")
            {
                $ostatuslng = $this->lang->line('accepted');
            }
            if($value['order_status'] == "cancel"){
                $ostatuslng = $this->lang->line('cancel');
            }
            if($value['order_status'] == "complete"){
                $ostatuslng = $this->lang->line('complete');
            }            
            if($value['order_status'] == "ready"){
                $ostatuslng = $this->lang->line('served');
            }            
            if($value['order_status'] == "onGoing")
            {
                $ostatuslng = $this->lang->line('ready');
            }            
            if($value['order_status'] == "preparing"){
                $ostatuslng = $this->lang->line('preparing');
            }
            if($value['order_status'] == "rejected"){
                $ostatuslng = $this->lang->line('rejected');
            }             
            //Code for Order status :: End

            $edit_order = (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button class="btn btn-sm margin-bottom red theme-btn" onclick="openOrderDetails('.$value['order_id'].')" title="'.$this->lang->line('order_details').'"><i class="fa fa-eye"></i></button>' : '';

            $deleteName = addslashes($value['restaurant']);
            $table_no = addslashes($value['table_no']);
            $user_phn_no = ($value['user_phn_no'])?'('.$value['user_phn_no'].')':'';
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_reservation')),$table_no,$deleteName)."'";
            $total = 0;
            $edit_active_access = (in_array('table~pastreservation_view',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll(\''.$value['table_status_id'].'\','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm danger-btn theme-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $value['table_no'],
                $value['capacity'],
                $value['order_id'],
                ($value['fname'] || $value['lname'])?$value['fname'].' '.$value['lname'].' '.$user_phn_no:'',
                $value['restaurant'],
                ($value['reservation_date'])?date('m/d/Y g:i A',strtotime($value['reservation_date'])):'',
                $this->lang->line('paid'),
                $ostatuslng,
                $edit_order.$edit_active_access,
            ); 
            $cusLan = array();
            // foreach ($Languages as $lang) { 
            //     if(array_key_exists($lang->language_slug,$value['translations'])){
            //         // var_dump($value['translations'][$lang->language_slug]['translation_id']);
            //         $cusLan[] = '<a style="cursor:pointer;" onclick="deleteDetail('.$value['translations'][$lang->language_slug]
            //         ['translation_id'].','.$value['content_id'].')"  title="'.$this->lang->line('click_delete').'">
            //         <i class="fa fa-times"></i> </a>( '.$value['translations'][$lang->language_slug]['table_no'].' )';
            //     }else{
            //         $cusLan[] = '<a href="#" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>';
            //     }                    
            // }
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
    // method for deleting a table
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->table_model->ajaxDelete('table_master',$this->input->post('content_id'),$entity_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method for deleting reservation
    public function ajaxDeleteAllReservation(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->table_model->ajaxDeleteAllReservation('table_status',$this->input->post('content_id'),$entity_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method for deleting table
    public function ajaxDeleteAll()
    {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $this->table_model->ajaxDeleteAll('table_master',$content_id);
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // method to change table status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->table_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
        }
    }
    //Update status for All
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->table_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
        }
    }
    //ajax approve reservation request
    public function ajaxApproveAllRequest() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->table_model->ApproveAll($this->input->post('tblname'),$content_id,'approve');
        }
    }
    //Generate QR code  
    public function qrcodeGenerator($tbl_id,$data) {
        $qrtext = $tbl_id;
        if(isset($qrtext)) {
            //$SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/uploads/table/';
            if (!@is_dir('uploads/table')) {
              @mkdir('./uploads/table', 0777, TRUE);
            }
            $SERVERFILEPATH = './uploads/table/';
            $text = $qrtext;
            $folder = $SERVERFILEPATH;
            $file_name1 = str_replace(' ', '-', $data).".png";
            $file_name = $folder.$file_name1;
            QRcode::png($text,$file_name);
            return 'table/'.$file_name1;
        } else {
            return null;
        }   
    }
    public function checkTableNameExist()
    {
        $table_no = ($this->input->post('table_no') != '')?trim($this->input->post('table_no')):'';
        $table_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';        
        if($call_from == 'CI_callback')
        {
            if($table_no && $restaurant_id)
            {
                $check = $this->table_model->checkTableNameExist($table_no,$table_entity_id,$restaurant_id);                
                if($check > 0) {
                    $this->form_validation->set_message('checkTableNameExist', $this->lang->line('tablename_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }
        else
        {
            if($table_no && $restaurant_id) {
                $check = $this->table_model->checkTableNameExist($table_no,$table_entity_id,$restaurant_id);                
                echo $check;
            }
        }
    }
}
