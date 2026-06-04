<?php
class Restaurant_model extends CI_Model {
    function __construct()
    {
        parent::__construct();              
    }   
    // method for getting all
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //New code for search with multi language title :: Start
        $LanguagesArr = $this->common_model->getLanguages();
        $where_titleserch = '';
        $this->db->select('restaurant.entity_id');
        $this->db->join('restaurant_address as res_add','restaurant.entity_id = res_add.resto_entity_id','left');
        if(!empty($LanguagesArr) && count($LanguagesArr)>0)
        {
            for($ln=0;$ln<count($LanguagesArr);$ln++)
            {
                $lang_name = $LanguagesArr[$ln]->language_slug;
                $lang_title_val = trim($this->input->post('title_'.$lang_name));
                if($lang_title_val!='')
                {
                    if($where_titleserch!='')
                    {
                        $where_titleserch .= ' OR ';
                    }    
                    $where_titleserch .= " restaurant.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND restaurant.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End

        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('city_search') != '') {
            $city_name = $this->common_model->escapeString(trim($this->input->post('city_search')));
            $citywhere = "(res_add.city LIKE '%".$city_name."%')";
            $this->db->where($citywhere);
        }
        if($this->input->post('status') != ''){
            $this->db->where('restaurant.status', trim($this->input->post('status')));
        }
        if($this->input->post('enable_hours') != ''){
            $this->db->where('restaurant.enable_hours', trim($this->input->post('enable_hours')));
        }
        if($this->input->post('schedule_mode') != ''){
            $this->db->where('restaurant.schedule_mode', trim($this->input->post('schedule_mode')));
        }
        $this->db->group_by('restaurant.content_id');
        //$this->db->where('restaurant.branch_entity_id','');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $result['total'] = $this->db->count_all_results('restaurant');
        
        if($where_titleserch=='')
        { 
            if($this->input->post('status') != ''){
                $this->db->where('restaurant.status', trim($this->input->post('status')));
            }
            if($this->input->post('city_search') != '') {
                $city_name = $this->common_model->escapeString(trim($this->input->post('city_search')));
                $citywhere = "(res_add.city LIKE '%".$city_name."%')";
                $this->db->where($citywhere);
            }
            if($this->input->post('enable_hours') != ''){
                $this->db->where('restaurant.enable_hours', trim($this->input->post('enable_hours')));
            }
            if($this->input->post('schedule_mode') != ''){
                $this->db->where('restaurant.schedule_mode', trim($this->input->post('schedule_mode')));
            }
            $this->db->select('content_general_id,restaurant.entity_id, restaurant.content_id, restaurant.name, restaurant.status, restaurant.enable_hours, restaurant.is_masterdata, restaurant.language_slug, res_add.city');   
            $this->db->join('restaurant','restaurant.content_id = content_general.content_general_id','left');
            $this->db->join('restaurant_address as res_add','restaurant.entity_id = res_add.resto_entity_id','left');

            $this->db->group_by('restaurant.content_id');
            $this->db->order_by('restaurant.entity_id','DESC');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            //$this->db->where('content_type','restaurant');
            $content_type = array('restaurant','branch');
            $this->db->where_in('content_type',$content_type);
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('restaurant.content_id',$content_general_id);    
            }            
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }

            if($this->input->post('status') != ''){
                $this->db->where('restaurant.status', trim($this->input->post('status')));
            }
            if($this->input->post('city_search') != '') {
                $city_name = $this->common_model->escapeString(trim($this->input->post('city_search')));
                $citywhere = "(res_add.city LIKE '%".$city_name."%')";
                $this->db->where($citywhere);
            }
            if($this->input->post('enable_hours') != ''){
                $this->db->where('restaurant.enable_hours', trim($this->input->post('enable_hours')));
            }
            if($this->input->post('schedule_mode') != ''){
                $this->db->where('restaurant.schedule_mode', trim($this->input->post('schedule_mode')));
            }
            $this->db->select('content_general_id,restaurant.entity_id, restaurant.content_id, restaurant.name, restaurant.status, restaurant.enable_hours, restaurant.is_masterdata, restaurant.language_slug, res_add.city');   
            $this->db->join('content_general','restaurant.content_id = content_general.content_general_id','left');
            $this->db->join('restaurant_address as res_add','restaurant.entity_id = res_add.resto_entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            //$this->db->where('content_type','restaurant');
            $content_type = array('restaurant','branch');
            $this->db->where_in('content_type',$content_type);
            $this->db->group_by('restaurant.content_id');
            $this->db->order_by('restaurant.entity_id','DESC');
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $cmsData = $this->db->get('restaurant')->result();                      
            $ContentID = array();               
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( restaurant.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('restaurant.content_id',$ContentID);
            }
            else
            {              
                if($where_titleserch!='')
                {
                    $this->db->where($where_titleserch);
                }
                if($this->input->post('status') != ''){
                    $this->db->where('restaurant.status', trim($this->input->post('status')));
                }
                if($this->input->post('city_search') != '') {
                    $city_name = $this->common_model->escapeString(trim($this->input->post('city_search')));
                    $citywhere = "(res_add.city LIKE '%".$city_name."%')";
                    $this->db->where($citywhere);
                }
                if($this->input->post('enable_hours') != ''){
                    $this->db->where('restaurant.enable_hours', trim($this->input->post('enable_hours')));
                }
                if($this->input->post('schedule_mode') != ''){
                    $this->db->where('restaurant.schedule_mode', trim($this->input->post('schedule_mode')));
                }
            }
        }
        $this->db->select('restaurant.*, res_add.city');   
        $this->db->join('restaurant_address as res_add','restaurant.entity_id = res_add.resto_entity_id','left');
        if($this->input->post('status') != ''){
            $this->db->where('restaurant.status', trim($this->input->post('status')));
        }
        if($this->input->post('city_search') != '') {
            $city_name = $this->common_model->escapeString(trim($this->input->post('city_search')));
            $citywhere = "(res_add.city LIKE '%".$city_name."%')";
            $this->db->where($citywhere);
        }
        if($this->input->post('enable_hours') != ''){
            $this->db->where('restaurant.enable_hours', trim($this->input->post('enable_hours')));
        }
        if($this->input->post('schedule_mode') != ''){
            $this->db->where('restaurant.schedule_mode', trim($this->input->post('schedule_mode')));
        }
        //$this->db->where('restaurant.branch_entity_id',''); 
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        $cmdData = $this->db->get('restaurant')->result_array(); 

        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'city' => $value['city'],
                        'status' => $value['status'],
                        'enable_hours' => $value['enable_hours'],
                        'is_masterdata' => $value['is_masterdata'],
                        'schedule_mode' => $value['schedule_mode'],
                        'order_mode' => $value['order_mode'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'status' => $value['status'],
                    'enable_hours' => $value['enable_hours'],
                    'schedule_mode' => $value['schedule_mode'],
                    'order_mode' => $value['order_mode'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }       
    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    // method to get details by id
    public function getEditDetail($tblname,$entity_id,$language_slug=NULL)
    {
        if($tblname == 'restaurant'){
            $check_res_cnt = 0;
            $this->db->select('branch_entity_id');
            $this->db->where('restaurant.entity_id',$entity_id);
            $check_branch = $this->db->get('restaurant')->first_row();
            if($check_branch->branch_entity_id != "0"){
                $this->db->select('entity_id');
                $this->db->where('restaurant.content_id',$check_branch->branch_entity_id);
                $this->db->where('restaurant.language_slug',$language_slug);
                $check_res_cnt = $this->db->get('restaurant')->num_rows();
            }
        }
        $this->db->select('res.*, res_add.address, res_add.landmark, res_add.zipcode, res_add.country, res_add.state, res_add.city, res_add.latitude, res_add.longitude');
        $this->db->join('restaurant_address as res_add','res.entity_id = res_add.resto_entity_id','left');
        if($tblname == 'restaurant' && $check_branch->branch_entity_id != "0" && $check_res_cnt > 0) {
            $this->db->select('res_alias.name as parent_res');
            $this->db->join('restaurant as res_alias','res.branch_entity_id = res_alias.content_id','left');
            $this->db->where('res_alias.language_slug',$language_slug);
        }
        $this->db->where('res.entity_id',$entity_id);
        return $this->db->get($tblname.' as res')->first_row();
    }
    // delete 
    public function ajaxDelete($tblname,$content_id,$entity_id)
    {
        // check  if last record
        if($content_id){
            $vals = $this->db->get_where($tblname,array('content_id'=>$content_id))->num_rows();    
            if($vals==1){
                $this->db->where(array('content_general_id' => $content_id));
                $this->db->delete('content_general');        
            }            
        } 
        $this->db->where('entity_id',$entity_id);
        $this->db->delete($tblname);    
    }
    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');                   
        $res_data = $this->db->select('image')
                             ->where('content_id',$content_id)
                             ->get($tblname)
                             ->row_array();
        $res_image = $res_data['image'];
        if (is_file(FCPATH.'uploads/'.$res_image)) {
            @unlink(FCPATH.'uploads/'.$res_image);
        }
        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);    
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatus($tblname,$entity_id,$status){
        if($status==0){
            $userData = array('status' => 1);
        } else {
            $userData = array('status' => 0);
        }        
        $this->db->where('entity_id',$entity_id);
        $this->db->update($tblname,$userData);
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatusAll($tblname,$ContentID,$Status){
        if($Status==0){
            $Data = array('status' => 1);
        } else {
            $Data = array('status' => 0);
        }

        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
    //get list
    public function getListData($tblname,$language_slug=NULL)
    {
        $this->db->select('name,entity_id');
        //Chage on 03-11-2020
        if($tblname=='restaurant')
        {
            $this->db->select('name,entity_id,food_type,restaurant_owner_id,branch_admin_id,content_id');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
            }            
        }        
        $this->db->where('status',1);

        if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && $tblname != 'restaurant'){
            //$this->db->where('created_by',$this->session->userdata('UserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin' && $tblname != 'restaurant'){
            //$this->db->where('created_by',$this->session->userdata('parent_adminid'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }
    public function getListforeventData($tblname,$language_slug=NULL,$contentt_id=NULL)
    {
        $this->db->select('name,entity_id,content_id');
        //Chage on 03-11-2020
        if($tblname=='restaurant')
        {
            $this->db->select('name,entity_id,food_type,restaurant_owner_id,branch_admin_id');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('allow_event_booking',1);
        }else if($tblname=='restaurant_package'){
            $this->db->select('name,entity_id,content_id,restaurant_id,price');
            if (!empty($contentt_id)) {
                $this->db->where('content_id',$contentt_id);  
            }
        }        
        $this->db->where('status',1);

        if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && $tblname != 'restaurant'){
            //$this->db->where('created_by',$this->session->userdata('UserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin' && $tblname != 'restaurant'){
            //$this->db->where('created_by',$this->session->userdata('parent_adminid'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }
    //menu grid
    public function getMenuGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?trim($this->input->post('restaurant_owner_id')):$this->session->userdata('AdminUserID');

        $restaurant_id = ($this->input->post('rest_id') != '')?trim($this->input->post('rest_id')):'';

        //New code for search with multi language title :: Start[change as per new datatable::23-09-2021]
        $where_titleserch = '';
        if(trim($this->input->post('sSearch_0'))!='')
        {            
            $lang_title_val = trim($this->input->post('sSearch_0'));
            $where_titleserch .= " (menu.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND menu.language_slug ='en') ";
        }
        if(trim($this->input->post('sSearch_1'))!='')
        {            
            $lang_title_val = trim($this->input->post('sSearch_1'));
            if($where_titleserch!='')
            {
                $where_titleserch .= " AND ";
            }
            $where_titleserch .= " (menu.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND menu.language_slug ='fr') ";
        }
        if(trim($this->input->post('sSearch_2'))!='')
        {            
            $lang_title_val = trim($this->input->post('sSearch_2'));
            if($where_titleserch!='')
            {
                $where_titleserch .= " AND ";
            }
            $where_titleserch .= " (menu.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND menu.language_slug ='ar') ";
        }
        //New code for search with multi language title :: End
        
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('sSearch_6') != ''){
            $this->db->where('menu.status', trim($this->input->post('sSearch_6')));
        }
        if($restaurant_id != ''){
            $this->db->where('menu.restaurant_id', $restaurant_id);
        }
        if($this->input->post('sSearch_4') != ''){
            $this->db->like('res.name', trim($this->input->post('sSearch_4')));
        }
        if($this->input->post('sSearch_3') != ''){
            $total_price = trim($this->input->post('sSearch_3'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('menu.price', trim($total_search));
            }else{
                $this->db->like('menu.price', trim($this->input->post('sSearch_3')));
            }
        }
        if($this->input->post('sSearch_5') != ''){
            $this->db->like('menu.is_combo_item', trim($this->input->post('sSearch_5')));
        }
        if($this->input->post('sSearch_7') != ''){
            $this->db->where('menu.stock', trim($this->input->post('sSearch_7')));
        }
        $this->db->select('menu.name as mname,res.name as rname,menu.entity_id,menu.status,res.currency_id');
        $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
        /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
            $this->db->where_in('menu.restaurant_id',$this->session->userdata('restaurant'));
        } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
            $this->db->where('res.created_by',$this->session->userdata('UserID'));
        }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('menu.content_id');
        $result['total'] = $this->db->count_all_results('restaurant_menu_item as menu');
        
        if($where_titleserch=="" && $this->input->post('sSearch_4') == '' && $this->input->post('sSearch_3') == '' && $this->input->post('sSearch_5') == '' && $this->input->post('sSearch_6') == '')
        {
            if($this->input->post('sSearch_6') != ''){
                $this->db->where('menu.status', trim($this->input->post('sSearch_6')));
            }
            if($restaurant_id != ''){
                $this->db->where('menu.restaurant_id', $restaurant_id);
            }
            if($this->input->post('sSearch_4') != ''){
                $this->db->like('res.name', trim($this->input->post('sSearch_4')));
            } 
            if($this->input->post('sSearch_3') != ''){
                $total_price = trim($this->input->post('sSearch_3'));
                if($total_price[0] == '$')
                {
                    $total_search = substr($total_price, 1);
                    $this->db->like('menu.price', trim($total_search));
                }else{
                    $this->db->like('menu.price', trim($this->input->post('sSearch_3')));
                }
            }
            if($this->input->post('sSearch_5') != ''){
                $this->db->like('menu.is_combo_item', trim($this->input->post('sSearch_5')));
            }
            if($this->input->post('sSearch_7') != ''){
                $this->db->where('menu.stock', trim($this->input->post('sSearch_7')));
            }
            if($restaurant_id != ''){
                $this->db->where('menu.restaurant_id', $restaurant_id);
            }
            $this->db->select('content_general_id,menu.content_id, menu.name, menu.price, menu.check_add_ons, menu.status, menu.is_combo_item, menu.is_masterdata, menu.stock, menu.entity_id, menu.language_slug,res.name as rname,res.currency_id,res.content_id as rcontent_id,(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no');   
            $this->db->join('restaurant_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
            $this->db->join('menu_item_sequencemap as menumap',"menumap.menu_content_id = menu.content_id
            AND menumap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');
            /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                $this->db->where_in('menu.restaurant_id',$this->session->userdata('restaurant'));
            } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                $this->db->where('res.created_by',$this->session->userdata('UserID'));
            }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('content_type','menu');
            $this->db->group_by('menu.content_id');
            $this->db->order_by('sequence_no', 'ASC');
            $this->db->order_by('menu.content_id', 'DESC');
            if($displayLength>1){
                if(empty($this->input->post('restaurant_owner_id')) && ($this->session->userdata('AdminUserType') != 'Restaurant Admin' && $this->session->userdata('AdminUserType') != 'Branch Admin')) {
                    $this->db->limit($displayLength,$displayStart);
                }
            }
            $dataCmsOnly = $this->db->get('content_general')->result(); 

            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('menu.content_id',$content_general_id);    
            }            
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }   
            if($this->input->post('sSearch_6') != ''){
                $this->db->where('menu.status', trim($this->input->post('sSearch_6')));
            }
            if($restaurant_id != ''){
                $this->db->where('menu.restaurant_id', $restaurant_id);
            }
            if($this->input->post('sSearch_4') != ''){
                $this->db->like('res.name', trim($this->input->post('sSearch_4')));
            } 
            if($this->input->post('sSearch_3') != ''){
                $total_price = trim($this->input->post('sSearch_3'));
                if($total_price[0] == '$')
                {
                    $total_search = substr($total_price, 1);
                    $this->db->like('menu.price', trim($total_search));
                }else{
                    $this->db->like('menu.price', trim($this->input->post('sSearch_3')));
                }
            }
            if($this->input->post('sSearch_5') != ''){
                $this->db->like('menu.is_combo_item', trim($this->input->post('sSearch_5')));
            }
            if($this->input->post('sSearch_7') != ''){
                $this->db->where('menu.stock', trim($this->input->post('sSearch_7')));
            }
            $this->db->select('content_general_id,menu.content_id,menu.content_id, menu.name, menu.price, menu.check_add_ons, menu.status, menu.is_combo_item, menu.is_masterdata, menu.stock, menu.entity_id, menu.language_slug,res.name as rname,res.currency_id,res.content_id as rcontent_id,(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no');   
            $this->db->join('restaurant_menu_item as menu','menu.content_id = content_general.content_general_id','left');
            $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
            $this->db->join('menu_item_sequencemap as menumap',"menumap.menu_content_id = menu.content_id
            AND menumap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');
            /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                $this->db->where_in('menu.restaurant_id',$this->session->userdata('restaurant'));
            } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                $this->db->where('res.created_by',$this->session->userdata('UserID'));
            }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('content_type','menu');
            $this->db->group_by('menu.content_id');
            $this->db->order_by('sequence_no', 'ASC');
            if($sortFieldName != ''){
                $this->db->order_by($sortFieldName, $sortOrder);
            }
            $this->db->order_by('menu.content_id', 'DESC');

            if($displayLength>1){
                if(empty($this->input->post('restaurant_owner_id')) && ($this->session->userdata('AdminUserType') != 'Restaurant Admin' && $this->session->userdata('AdminUserType') != 'Branch Admin')) {
                    $this->db->limit($displayLength,$displayStart);
                }
            }
            $dataCmsOnly = $this->db->get('content_general')->result();

            $ContentID = array();               
            foreach ($dataCmsOnly as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }
            $this->db->order_by('sequence_no', 'ASC');   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( menu.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('menu.content_id',$ContentID);
            }
            else
            {              
                if($where_titleserch!='')
                {
                    $this->db->where($where_titleserch);
                } 
                if($this->input->post('sSearch_4') != ''){
                    $this->db->like('res.name', trim($this->input->post('sSearch_4')));
                } 
                if($this->input->post('sSearch_3') != ''){
                    $total_price = trim($this->input->post('sSearch_3'));
                    if($total_price[0] == '$')
                    {
                        $total_search = substr($total_price, 1);
                        $this->db->like('menu.price', trim($total_search));
                    }else{
                        $this->db->like('menu.price', trim($this->input->post('sSearch_3')));
                    } 
                }
                if($this->input->post('sSearch_5') != ''){
                    $this->db->like('menu.is_combo_item', trim($this->input->post('sSearch_5')));
                }
                if($this->input->post('sSearch_7') != ''){
                    $this->db->where('menu.stock', trim($this->input->post('sSearch_7')));
                }
            }
        }  
        if($this->input->post('sSearch_6') != ''){
            $this->db->where('menu.status', trim($this->input->post('sSearch_6')));
        }
        if($restaurant_id != ''){
            $this->db->where('menu.restaurant_id', $restaurant_id);
        }
        $this->db->select('content_general_id,menu.content_id,menu.content_id, menu.name, menu.price, menu.check_add_ons, menu.status, menu.is_combo_item, menu.is_masterdata, menu.stock, menu.entity_id,, menu.language_slug, res.name as rname,res.currency_id, res.content_id as rcontent_id, (CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no');
        $this->db->join('content_general','menu.content_id = content_general.content_general_id','left');
        $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
        $this->db->join('menu_item_sequencemap as menumap',"menumap.menu_content_id = menu.content_id
            AND menumap.restaurant_owner_id = '".$restaurant_owner_id."'",'left');
        /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
            $this->db->where_in('menu.restaurant_id',$this->session->userdata('restaurant'));
        } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
            $this->db->where('res.created_by',$this->session->userdata('UserID'));
        }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('sequence_no', 'ASC');
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }        
        $cmdData = $this->db->get('restaurant_menu_item as menu')->result_array();        
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value)
            {
                $rest_name=$value['rname'];
                if($this->input->post('sSearch_4') == '')
                {
                    $this->db->select('name');
                    $this->db->where('content_id',$value['rcontent_id']);
                    $this->db->where('language_slug',$this->session->userdata('language_slug'));
                    $res_result =  $this->db->get('restaurant')->first_row();
                    if($res_result)
                    {
                        $rest_name=$res_result->name;
                    }
                }                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' =>$rest_name,
                        'price' => $value['price'], 
                        'check_add_ons' => $value['check_add_ons'], 
                        'currency_id' =>$value['currency_id'],
                        'status' => $value['status'],
                        'is_combo_item' => $value['is_combo_item'],
                        'is_masterdata' => $value['is_masterdata'],
                        'stock' => $value['stock']
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' =>$rest_name,
                    'price' =>$value['price'],
                    'status' =>$value['status'],
                    'is_combo_item' => $value['is_combo_item']
                );
            }
        }        
        $result['data'] = $cmsLang;        
        return $result; 
    }
    //package grid
    public function getPackageGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //New code for search with multi language title :: Start
        $LanguagesArr = $this->common_model->getLanguages();
        $where_titleserch = '';
        if(!empty($LanguagesArr) && count($LanguagesArr)>0)
        {
            for($ln=0;$ln<count($LanguagesArr);$ln++)
            {
                $lang_name = $LanguagesArr[$ln]->language_slug;
                $lang_title_val = trim($this->input->post('title_'.$lang_name));
                if($lang_title_val!='')
                {
                    if($where_titleserch!='')
                    {
                        $where_titleserch .= ' OR ';
                    }    
                    $where_titleserch .= " package.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND package.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End

        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }

        if($this->input->post('status') != ''){
            $this->db->where('package.status', trim($this->input->post('status')));
        }

        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('price') != ''){
            $total_price = trim($this->input->post('price'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('package.price', trim($total_search));
            }else{
                $this->db->like('package.price', trim($this->input->post('price')));
            }
        } 
        $this->db->select('package.name as mname,res.name as rname,package.entity_id,package.status,res.currency_id');
        $this->db->join('restaurant as res','package.restaurant_id = res.content_id','left');
        /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where_in('package.restaurant_id',$this->session->userdata('restaurant'));
        }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('package.content_id');
        $result['total'] = $this->db->count_all_results('restaurant_package as package');
        
        if($where_titleserch =="" && $this->input->post('restaurant') == '' && $this->input->post('price') == '')
        {
            if($this->input->post('status') != ''){
                $this->db->where('package.status', trim($this->input->post('status')));
            }
            $this->db->select('content_general_id,package.*,res.name as rname,res.currency_id');   
            $this->db->join('restaurant_package as package','package.content_id = content_general.content_general_id','left');
            $this->db->join('restaurant as res','package.restaurant_id = res.content_id','left');
            /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where_in('res.created_by',$this->session->userdata('restaurant'));
            }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->where('content_type','package');
            $this->db->group_by('package.content_id');
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('package.content_id',$content_general_id);    
            }            
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }               
            if($this->input->post('status') != ''){
                $this->db->where('package.status', trim($this->input->post('status')));
            }
            if($this->input->post('restaurant') != ''){
                $this->db->like('res.name', trim($this->input->post('restaurant')));
            }
            if($this->input->post('price') != ''){
                $total_price = trim($this->input->post('price'));
                if($total_price[0] == '$')
                {
                    $total_search = substr($total_price, 1);
                    $this->db->like('package.price', trim($total_search));
                }else{
                    $this->db->like('package.price', trim($this->input->post('price')));
                }
            }  
            $this->db->select('content_general_id,package.*,res.name as rname,res.currency_id');   
            $this->db->join('restaurant_package as package','content_general.content_general_id = package.content_id','left');
            $this->db->join('restaurant as res','package.restaurant_id = res.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('package.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('content_general')->result();                      
            $ContentID = array();               
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD ( package.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('package.content_id',$ContentID);
            }
            else
            {              
                if($where_titleserch!='')
                {
                    $this->db->where($where_titleserch);
                } 
                if($this->input->post('restaurant') != ''){
                    $this->db->like('res.name', trim($this->input->post('restaurant')));
                } 
                if($this->input->post('price') != ''){
                    $total_price = trim($this->input->post('price'));
                    if($total_price[0] == '$')
                    {
                        $total_search = substr($total_price, 1);
                        $this->db->like('package.price', trim($total_search));
                    }else{
                        $this->db->like('package.price', trim($this->input->post('price')));
                    }
                }
            }
        }  
        $this->db->select('content_general_id,package.*,res.name as rname,res.content_id as rcontent_id,res.currency_id');   
        $this->db->join('content_general','package.content_id = content_general.content_general_id','left');
        $this->db->join('restaurant as res','package.restaurant_id = res.content_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if($this->input->post('status') != ''){
            $this->db->where('package.status', trim($this->input->post('status')));
        }
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        $cmdData = $this->db->get('restaurant_package as package')->result_array();  
        foreach ($cmdData as $key => $value)
        {
            $rest_name=$value['rname'];
            if($this->input->post('restaurant') == '')
            {
                $this->db->select('name');
                $this->db->where('content_id',$value['rcontent_id']);
                $this->db->where('language_slug',$this->session->userdata('language_slug'));
                $res_result =  $this->db->get('restaurant')->first_row();         
                if($res_result)
                {
                    $rest_name=$res_result->name;
                }
            }
            $cmdData[$key]['rname'] = $rest_name; 
        }  
        $cmsLang = array();    
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'status' => $value['status'],
                        'name' => $value['name'],
                        'rname' =>$value['rname'],
                        'price' =>$value['price'],
                        'check_add_ons' =>@$value['check_add_ons'], 
                        'created_by' => $value['created_by'], 
                        'currency_id' =>$value['currency_id'],
                        'is_masterdata' =>$value['is_masterdata'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' =>$value['rname'],
                );
            }
        }      
        $result['data'] = $cmsLang;
        return $result;
    }
    public function checkExist($phone_number,$entity_id,$content_id){
        $this->db->where('phone_number',$phone_number);
        $this->db->where('entity_id !=',$entity_id);
        $this->db->where('content_id !=',$content_id);
        return $this->db->get('restaurant')->num_rows();
    }
    public function checkEmailExist($email,$entity_id,$content_id,$lang_slug='en'){
        $this->db->where('email',$email);
        $this->db->where('entity_id !=',$entity_id);
        $this->db->where('content_id !=',$content_id);
        $this->db->where('language_slug',$lang_slug);
        return $this->db->get('restaurant')->num_rows();
    }
    //insert batch
    public function inserBatch($tblname,$data){
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
    //get add ons detail
    public function getAddonsDetail($tblname,$menu_id){
        $this->db->where('menu_id',$menu_id);
        $result = $this->db->get($tblname)->result();
        $addons = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                if(!isset($addons[$value->category_id])){
                    $addons[$value->category_id] = array();
                }
                if(isset($addons[$value->category_id])){
                    array_push($addons[$value->category_id], $value);
                }
            }
        }
        return $addons;
    }
    //delete insert data
    public function deleteinsertBatch($tblname,$data,$menu_id){
        $this->db->where('menu_id',$menu_id);
        $this->db->delete($tblname);
        if(!empty($data)){
            $this->db->insert_batch($tblname,$data);
            return $this->db->insert_id();
        }
    }
    // get restaurant slug
    public function getRestaurantSlug($content_id){
        $this->db->select('restaurant_slug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('restaurant')->first_row();
    }
    // get item slug
    public function getItemSlug($content_id){
        $this->db->select('item_slug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('restaurant_menu_item')->first_row();
    }
    // get restaurants name
    public function getRestaurantName($entity_id){
        $this->db->select('name');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('restaurant')->first_row();
    }
    // get content id
    public function getContentId($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    // get category id
    public function getCategoryId($name,$lang_slug){
        $this->db->select('entity_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        $this->db->limit(1,0);
        return $this->db->get('category')->first_row();
    }
    // get addons for language
    public function getAddons($lang_slug){
        $this->db->select('name');
        $this->db->where('language_slug',$lang_slug);
        $addons = $this->db->get('add_ons_category')->result_array();
        return array_column($addons, 'name');
    }
    // check addons category exist or not
    public function getAddonsId($name,$lang_slug){
        $this->db->select('entity_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        return $this->db->get('add_ons_category')->first_row();
    }
    // check addons category exist or not
    public function getRestaurantId($name,$lang_slug){
        $this->db->select('entity_id,content_id');
        $this->db->where('name',$name);
        $this->db->where('language_slug',$lang_slug);
        $this->db->limit(1,0);
        return $this->db->get('restaurant')->first_row();
    }
    //get all list 
    public function getAllListData($tblname,$language_slug=NULL){
        $this->db->select('name,entity_id');
        //$this->db->where('status',1);

        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        if($tblname=='category'){
            $this->db->order_by('sequence', 'ASC');    
        }
        else{
            $this->db->order_by('name', 'ASC');
        }
        return $this->db->get($tblname)->result();
    }
    //New code added to show the branch admin user :: Start :: 13-10-2020
    public function get_brachadmin($entity_id,$restaurant_owner_id='')
    {
        $this->db->select('first_name,last_name,entity_id');
        $this->db->where('user_type','Branch Admin');
        $this->db->where('status',1);
        /*$this->db->or_where('user_type','Restaurant Admin');*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin')
        {
            // $this->db->where('created_by',$this->session->userdata('UserID'));  
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));
            /*$this->db->or_where('entity_id',$entity_id);*/
        }
        if($restaurant_owner_id!='')
        {
            $this->db->where('parent_user_id',$restaurant_owner_id);
        }
        /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')
        {
            $this->db->or_where('entity_id',$entity_id);
        }*/
        $this->db->order_by('first_name', 'ASC'); 
        return $this->db->get('users')->result();
    }
    public function get_restaurant_admins($entity_id)
    {
        $this->db->select('first_name,last_name,entity_id');
        $this->db->where('user_type','Restaurant Admin');
        $this->db->where('status',1);
        $this->db->or_where('entity_id',$entity_id);
        $this->db->where('user_type !=','MasterAdmin');
        $this->db->order_by('first_name', 'ASC'); 
        return $this->db->get('users')->result();
    }
    public function getBrachAdminDetail($content_id){
        $this->db->select('branch_admin_id');
        $this->db->where('restaurant_content_id',$content_id);
        return $this->db->get('restaurant_branch_map')->first_row();
    }
    public function ajaxDeleteMapdata($tblname,$content_id)
    {
        $this->db->where('restaurant_content_id',$content_id);
        $this->db->delete($tblname);    
    }
    //New code added to show the branch admin user :: End :: 13-10-2020
    // get food type
    public function getFoodType($tblname,$language_slug=NULL){
        $this->db->select('name,entity_id');
        $this->db->where('status',1);

        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }

    public function getRestaurantFoodType($restaurant_id,$language_slug)
    {
        $this->db->select('food_type');
        $this->db->where('entity_id', $restaurant_id);
        $id = $this->db->get('restaurant')->first_row();
        $food_id = explode(',', $id->food_type);

        $this->db->select('name,entity_id');
        if (!empty($food_id && $id)) {
        $this->db->where_in('entity_id',$food_id);
        }
        $this->db->where('status',1);

        if (!empty($language_slug)) {
        $this->db->where('language_slug',$language_slug);
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('food_type')->result();
    }

    // for menuitem food type
    public function getMenuType($entity_id)
    {
        $this->db->select('food_type');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get('restaurant_menu_item')->first_row();
    }
    public function getAllfoodType($entity_id)
    {
        $this->db->select('food_type');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get('restaurant')->first_row();
    }
    //get restaurant menu name
    public function chkRestaurantMenuName($menu_name,$language_slug,$category_id,$restaurant_id){
        $this->db->select('entity_id,name,content_id');
        $this->db->where('name',$menu_name);
        $this->db->where('restaurant_id',$restaurant_id);
        $this->db->where('category_id', $category_id);
        $this->db->where('language_slug',$language_slug);
        $this->db->limit(1,0);
        return $result = $this->db->get('restaurant_menu_item')->first_row();
    }
    // for menuitem food type
    public function getFood_TypeId($name,$language_slug,$restaurant_id='')
    {
        $name = str_replace(" , ", ",", $name);
        $name = str_replace(", ", ",", $name);
        $name = str_replace(" ,", ",", $name);
        $this->db->select('entity_id');
        $this->db->where_in('name', explode(",", $name));
        $this->db->where('language_slug',$language_slug);
        $this->db->limit(1,0);
        $food_typeArr = $this->db->get('food_type')->result();

        $food_typechk = '';
        if($food_typeArr && !empty($food_typeArr))
        {
            $food_typechk = implode(",", array_column($food_typeArr, 'entity_id'));
        }

        if($restaurant_id!='' && $food_typechk!='')
        {
            $this->db->select("entity_id");            
            $this->db->group_by('restaurant.content_id');
            $wherefindom = "(find_in_set ('".$food_typechk."', food_type))";
            $this->db->where($wherefindom);           
            $this->db->where('entity_id',$restaurant_id);
            $result = $this->db->get('restaurant')->result_array();            
            if(empty($result))
            {
                $food_typechk='';
            }            
        }
        return $food_typechk;
    }
    //get restaurant list 
    public function getRestaurantData($tblname,$language_slug=NULL,$entity_id=NULL)
    {
        $this->db->select('name,entity_id');
        //Chage on 03-11-2020
        if($tblname=='restaurant')
        {
            $this->db->select('name,entity_id,food_type,content_id');
        }
        $this->db->where('status',1);
        $this->db->where('branch_entity_id',0);
        $this->db->where('entity_id !=',$entity_id);
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }
    // get is multiple selected categories
    public function getIsMultipleCategory($menu_id){
        $this->db->select('menu_id,category_id,is_multiple');
        $this->db->where('menu_id',$menu_id);
        $this->db->where('is_multiple',1);
        $this->db->group_by('category_id');
        $categoryDetails = $this->db->get('add_ons_master')->result_array();
        return $is_multiple = array_column($categoryDetails, 'category_id');
    }
    // menu suggestion changes start
    public function get_active_restaurants(){
        $this->db->select('name,entity_id,content_id');
        $this->db->where('status',1);
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('created_by',$this->session->userdata('AdminUserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('restaurant')->result();
    }
    public function check_menu_suggestion($restaurant_content_id){
        return $this->db->get_where('restaurant_menu_suggestion',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }
    public function get_restaurant_menu_items($restaurant_id){
        $this->db->select('restaurant_menu_item.entity_id,restaurant_menu_item.name,restaurant.name as restaurant_name,restaurant_menu_item.restaurant_id,restaurant_menu_item.content_id');
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        $this->db->where('restaurant_menu_item.restaurant_id',$restaurant_id);
        $this->db->where('restaurant_menu_item.status',1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('restaurant_menu_item')->result();
    }
    public function deleteInsertMenuSuggestion($restaurant_content_id,$data){
        if($restaurant_content_id){
            $this->db->where('restaurant_content_id',$restaurant_content_id);
            $this->db->delete('restaurant_menu_suggestion');
        }
        if(!empty($data)){
            $this->db->insert_batch('restaurant_menu_suggestion',$data);
            return $this->db->insert_id();
        }
    }
    // menu suggestion changes end
    //delete multiple order
    public function deleteMultiMenu($content_id){
        $this->db->where_in('content_id',$content_id);
        $this->db->delete('restaurant_menu_item');
        return $this->db->affected_rows();
    }

    public function online_offline_all($tblname,$ContentID,$Status,$offlinetime=0)
    {
        if($Status==0){
            $Data = array('enable_hours' => 1,'offlinetime' => $offlinetime);
        }else{
            $Data = array('enable_hours' => 0,'offlinetime' => $offlinetime);
        }

        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
    /*public function getRecipe($tblname,$language_slug=NULL,$menu_content_id=NULL){
        $this->db->select('menu_content_id,recipe_content_id');
        $recipe_content= $this->db->get('restaurant_menu_recipe_map')->result();

        $this->db->select('name,entity_id,content_id');
        if(!empty($recipe_content)){
            $this->db->where_not_in('content_id',array_column($recipe_content,'recipe_content_id'));
        }

        $this->db->where('status',1);
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        $data = $this->db->get($tblname)->result();

        if($menu_content_id){
            $this->db->select('menu_content_id,recipe_content_id');
            $this->db->where('menu_content_id',$menu_content_id);
            $recipe_content= $this->db->get('restaurant_menu_recipe_map')->result();
            //print_r($recipe_content);exit;
            if(!empty($recipe_content)){
                $this->db->select('name,entity_id,content_id');

                if (!empty($language_slug)) {
                    $this->db->where('language_slug',$language_slug);
                }
                $this->db->where_in('content_id',array_column($recipe_content,'recipe_content_id'));
                $result2 = $this->db->get('recipe')->result();
                $data = array_merge($data,$result2);
            }
        }
        return $data;
    }
    public function getRecipeMenu($content_id){
        $this->db->select('recipe_content_id');
        $this->db->where('menu_content_id',$content_id);
        return $this->db->get('restaurant_menu_recipe_map')->result();
    }*/
    public function insertBatch($tblname,$data,$menu_content){
        if($menu_content){
            $this->db->where('menu_content_id',$menu_content);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname,$data);           
        return $this->db->insert_id();
    }
    /*public function DeleteMenuContent($content_id){
        $this->db->where('menu_content_id',$content_id);
        $this->db->delete('restaurant_menu_recipe_map');
    }*/
    //This function use in timezonechange script
    public function getAllRestaurantDetail()
    {
        //$countryarr = array('Ghana','UAE');
        //$countryarr = array('60','207');
        $this->db->select('res.entity_id, res.timings, res.name, res_add.zipcode, res_add.country');
        $this->db->join('restaurant_address as res_add','res.entity_id = res_add.resto_entity_id','left');        
        //$this->db->where_in('res_add.country',$countryarr);    
        //$this->db->where_in('res.entity_id',$countryarr); 
        $this->db->where('res.timezone_update',0);        
        //return $this->db->get('restaurant_bk15062021 as res')->result_array();
        return $this->db->get('restaurant as res')->result_array();
    }
    public function getIsDisplayLimit($menu_content_id){
        $this->db->select('menu_id,category_id,display_limit');
        $this->db->where('menu_id',$menu_content_id);
        $this->db->where('display_limit IS NOT NULL');
        $this->db->group_by('category_id');
        
        $categoryDetails = $this->db->get('add_ons_master')->result_array();
        return $display_limit = array_column($categoryDetails, 'category_id');
    }
    public function getIsDisplayLimitValue($menu_content_id){
        $this->db->select('menu_id,category_id,display_limit');
        $this->db->where('menu_id',$menu_content_id);
        $this->db->where('display_limit IS NOT NULL');
        $this->db->group_by('category_id');
        
        $categoryDetails = $this->db->get('add_ons_master')->result_array();
        return $categoryDetails;
    }
    public function getMandatory($menu_id){
        $this->db->select('menu_id,mandatory,category_id,is_multiple');
        $this->db->where('menu_id',$menu_id);
        $this->db->where('mandatory',1);
        $this->db->group_by('category_id');
        $categoryDetails = $this->db->get('add_ons_master')->result_array();
        return $is_multiple = array_column($categoryDetails, 'category_id');
    }
    public function checkResOrBranch($restaurant_content_id){
        $this->db->select('branch_entity_id');
        $this->db->where('content_id',$restaurant_content_id);
        $this->db->order_by('created_date','ASC');
        $check_branch = $this->db->get('restaurant')->first_row();
        $resp_arr = array();
        if(!empty($check_branch)){
            if($check_branch->branch_entity_id == "0"){
                $resp_arr['isbranch_or_res'] = 'is_res';
            } else {
                $resp_arr['isbranch_or_res'] = 'is_branch';
            }
        }
        return $resp_arr;
    }
    public function getAddonListData($language_slug='en')
    {
        $this->db->select('addon.entity_id,addon.name,addon.content_id,(CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no');
        $this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = addon.content_id
            AND menumap.restaurant_owner_id = '".$this->session->userdata('AdminUserID')."'",'left');
        //$this->db->where('menumap.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        $this->db->where('addon.status',1);
        $this->db->where('addon.language_slug',$language_slug);  
        $this->db->order_by('sequence_no,addon.name', 'ASC');
        $result = $this->db->get('add_ons_category as addon')->result();
        return $result;
    }
    public function getCategoryListData($language_slug='en')
    {
        $this->db->select('cat.entity_id,cat.name,cat.content_id,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');
        $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = cat.content_id
            AND catmap.restaurant_owner_id = '".$this->session->userdata('AdminUserID')."'",'left');
        $this->db->where('cat.status',1);
        $this->db->where('cat.language_slug',$language_slug);  
        $this->db->order_by('sequence_no,cat.name', 'ASC');
        //$this->db->order_by('sequence_no,,category.sequence', 'ASC');
        $result = $this->db->get('category as cat')->result();
        return $result;
    }
    public function UpdatedStockAll($tblname,$content_id,$stock)
    {
        if($stock==0){
            $Data = array('stock' => 1);
        } else {
            $Data = array('stock' => 0);
        }
        $this->db->where('content_id',$content_id);
        $this->db->update($tblname,$Data);        
        return $this->db->affected_rows();
    }
    public function insertBatchReorder($tblname,$data,$restaurant_owner_id)
    {            
        $this->db->where('restaurant_owner_id',$restaurant_owner_id);
        $this->db->delete($tblname);

        $this->db->insert_batch($tblname,$data);           
        return $this->db->insert_id();
    }
    //get restaurant admins
    public function get_restaurant_adminsData()
    {
        $this->db->select('res.entity_id,res.name,res.restaurant_owner_id');            
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->join('users as u',"u.entity_id = res.restaurant_owner_id");
            $this->db->where('u.user_type','Restaurant Admin');
            $this->db->where('u.status',1);    
            $this->db->where('u.entity_id',$this->session->userdata('AdminUserID'));
        }
        else if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->join('users as u',"u.entity_id = res.branch_admin_id");
            $this->db->where('u.user_type','Branch Admin');
            $this->db->where('u.status',1);    
            $this->db->where('u.entity_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('res.content_id', 'ASC');
        $this->db->where('res.status',1); 
        $this->db->order_by('res.name', 'ASC'); 
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('restaurant as res')->result();
    }
    //restaurant import :: start
    //get restaurant admins
    public function getRestaurantOwner($owner_name)
    {
        $this->db->select("first_name,last_name,entity_id");
        $this->db->where('user_type','Restaurant Admin');
        $this->db->where('status',1);
        $this->db->where("CONCAT_WS(' ',first_name,last_name) LIKE '".$owner_name."'", NULL, FALSE);
        //$this->db->or_where('entity_id',$this->session->userdata('AdminUserID'));
        $this->db->where('user_type !=','MasterAdmin');
        $this->db->order_by('first_name', 'ASC');
        return $this->db->get('users')->first_row();
    }
    public function getBranchAdmin($branch_admin_name)
    {
        $this->db->select('first_name,last_name,entity_id');
        $this->db->where('user_type','Branch Admin');
        $this->db->where('status',1);
        $this->db->where("CONCAT_WS(' ',first_name,last_name) LIKE '".$branch_admin_name."'", NULL, FALSE);
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin')
        {
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('first_name', 'ASC'); 
        return $this->db->get('users')->first_row();
    }
    public function getFoodTypeIds_forRes($food_type_str,$language_slug)
    {
        $food_type_str = str_replace(" , ", ",", $food_type_str);
        $food_type_str = str_replace(", ", ",", $food_type_str);
        $food_type_str = str_replace(" ,", ",", $food_type_str);
        $this->db->select('entity_id');
        $this->db->where_in('name', explode(",", $food_type_str));
        $this->db->where('language_slug',$language_slug);
        $food_typeArr = $this->db->get('food_type')->result();
        
        $food_typechk = '';
        if($food_typeArr && !empty($food_typeArr))
        {
            $food_typechk = implode(",", array_column($food_typeArr, 'entity_id'));
        }
        return $food_typechk;
    }
    public function gettimingsArr($enable_hours=0,$timings='',$closed_days='',$timezone_name='')
    {
        $emptytimingsArr = array('monday'=>array('open' => '','close' => '','off' => '0'),
            'tuesday'=>array('open' => '','close' => '','off' => '0'),
            'wednesday'=>array('open' => '','close' => '','off' => '0'),
            'thursday'=>array('open' => '','close' => '','off' => '0'),
            'friday'=>array('open' => '','close' => '','off' => '0'),
            'saturday'=>array('open' => '','close' => '','off' => '0'),
            'sunday'=>array('open' => '','close' => '','off' => '0')
        );
        if($enable_hours == 0){
            $timingsArr = $emptytimingsArr;
        } else {
            if($timings!='') {
                $closed_days_arr = ($closed_days!='')?explode(',', $closed_days):array();
                $timingsarr = explode("::",$timings);
                foreach ($timingsarr as $key => $value)
                {
                    $timingsarrtemp1 = explode(">>", $value);
                    $timingsarrtemp2 = explode(",", $timingsarrtemp1[1]);
                    if(!empty($closed_days_arr) && in_array(ucfirst($timingsarrtemp1[0]), $closed_days_arr)) {
                        $open = '';
                        $close = '';
                        $offval = 0;
                    } else {
                        $open = ($timingsarrtemp2[0]=='na')?'':$this->common_model->setZonebaseTime($timingsarrtemp2[0],$timezone_name);
                        $close = ($timingsarrtemp2[1]=='na')?'':$this->common_model->setZonebaseTime($timingsarrtemp2[1],$timezone_name);
                        $offval = ($open!='' && $close!='')?1:0;
                    }
                    $timingsArr[$timingsarrtemp1[0]] = array('open' => $open,'close' => $close,'off' => $offval);
                }
            } else {
                $timingsArr = $emptytimingsArr;
            }
        }
        return $timingsArr;
    }
    public function getRestaurantContentID($res_name='',$language_slug='en')
    {
        if($res_name!='')
        {
            $this->db->select('content_id');
            $this->db->like('name',$res_name);
            $this->db->where('language_slug',$language_slug);
            return $this->db->get('restaurant')->first_row();
        }
        return array();        
    }
    public function check_payment_method_suggestion($restaurant_content_id){
        return $this->db->get_where('restaurant_payment_method_suggestion',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }    
    public function deleteInsertMethodSuggestion($restaurant_content_id,$data){
        if($restaurant_content_id){
            $this->db->where('restaurant_content_id',$restaurant_content_id);
            $this->db->delete('restaurant_payment_method_suggestion');
        }
        if(!empty($data)){
            $this->db->insert_batch('restaurant_payment_method_suggestion',$data);
            return $this->db->insert_id();
        }
    }
    //check delivery charge name
    public function chkDeliveryChargeName($area_name,$restaurant_id){
        $this->db->select('charge_id,area_name,restaurant_id');
        $this->db->where('area_name',$area_name);
        $this->db->where('restaurant_id',$restaurant_id);
        return $result = $this->db->get('delivery_charge')->first_row();
    }
    //restaurant import :: end
    //check menu already exist for validation in admin panel
    public function checkResMenuNameExist($menu_name,$language_slug,$category_id,$restaurant_id,$menu_entity_id){
        $this->db->select('entity_id');
        $this->db->where('name',$menu_name);
        $this->db->where('restaurant_id',$restaurant_id);
        $this->db->where('category_id', $category_id);
        $this->db->where('language_slug',$language_slug);
        if($menu_entity_id) {
            $this->db->where('entity_id !=',$menu_entity_id);
        }
        return $this->db->get('restaurant_menu_item')->num_rows();
    }
    //check restaurant already exist for validation in admin panel
    public function checkResNameExist($res_name,$language_slug,$restaurant_id){
        $this->db->select('entity_id');
        $this->db->where('name',$res_name);
        $this->db->where('language_slug',$language_slug);
        if($restaurant_id) {
            $this->db->where('entity_id !=',$restaurant_id);
        }
        return $this->db->get('restaurant')->num_rows();
    }
    public function bulk_online_offline_all($tblname,$bulk_content_ids,$bulk_action='',$offlinetime=0) {
        $bulk_content_ids_arr = explode(',', $bulk_content_ids);
        if($bulk_action == 'online') {
            $Data = array('enable_hours' => 1,'offlinetime' => $offlinetime);
        } else {
            $Data = array('enable_hours' => 0,'offlinetime' => $offlinetime);
        }
        $this->db->where_in('content_id',$bulk_content_ids_arr);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
    // get restaurant menu name
    public function getRestaurantMenuName($entity_id = '',$content_id = '',$language_slug = '') {
        $this->db->select('name');
        if($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('restaurant_menu_item')->first_row();
        return ($return->name) ? $return->name : '';
    }
    public function getRestaurantPackageName($entity_id = '',$content_id = '',$language_slug = '') {
        $this->db->select('name');
        if($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('restaurant_package')->first_row();
        return ($return->name) ? $return->name : '';
    }
}
?>