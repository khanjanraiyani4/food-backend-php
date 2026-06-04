<?php
class Category_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    } 
    //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        $category_ids = '';
        $restaurant_content_id = (!empty($this->input->post('restaurant_id_for_category'))) ? $this->common_model->getContentId(trim($this->input->post('restaurant_id_for_category')),'restaurant') : 0;
        $this->db->select('category.entity_id');
        if(!empty($this->input->post('restaurant_id_for_category'))){
            $category_ids = $this->get_categories_from_restaurant_owner_id(trim($this->input->post('restaurant_id_for_category')));
        }
        //New code for search with multi language title :: Start[change as per new datatable::23-09-2021]
        $where_titleserch = '';
        if(trim($this->input->post('sSearch_0'))!='')
        {            
            $lang_title_val = trim($this->input->post('sSearch_0'));
            $where_titleserch .= " (category.name like '%".$this->common_model->escapeString(trim($lang_title_val))."%' AND category.language_slug ='en') ";
        }
        if(trim($this->input->post('sSearch_1'))!='')
        {
            $lang_title_val = $this->input->post('sSearch_1');
            if($where_titleserch!='')
            {
                $where_titleserch .= ' OR ';
            }
            $where_titleserch .= " (category.name like '%".$this->common_model->escapeString(trim($lang_title_val))."%' AND category.language_slug ='fr') ";
        }

        if(trim($this->input->post('sSearch_2'))!='')
        {
            $lang_title_val = $this->input->post('sSearch_2');
            if($where_titleserch!='')
            {
                $where_titleserch .= ' OR ';
            }
            $where_titleserch .= " (category.name like '%".$this->common_model->escapeString(trim($lang_title_val))."%' AND category.language_slug ='ar') ";
        }
        //New code for search with multi language title :: End

        if($where_titleserch!='')
        {
            $this->db->where("(".$where_titleserch.")");
        }
        if($this->input->post('sSearch_3') != ''){
            $this->db->where('category.status', trim($this->input->post('sSearch_3')));
        }
        $this->db->group_by('content_id');
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
        //     $this->db->where('category.created_by',$this->session->userdata('AdminUserID'));   
        // }
        if(!empty($this->input->post('restaurant_id_for_category'))){
            $this->db->where_in('category.content_id',$category_ids);
        }
        $result['total'] = $this->db->count_all_results('category');
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?$this->input->post('restaurant_owner_id'):$this->session->userdata('AdminUserID');
        if($where_titleserch=='')
        {
            if($this->input->post('sSearch_3') != ''){
                $this->db->where('category.status', trim($this->input->post('sSearch_3')));
            }
            $this->db->select('content_general_id,category.entity_id, category.content_id, category.name, category.status, category.created_by, category.is_masterdata, category.language_slug,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');   
            $this->db->join('category','category.content_id = content_general.content_general_id','left');
            $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = category.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');
            //$this->db->order_by('category.sequence','ASC');
            $this->db->order_by('sequence_no', 'ASC');
            $this->db->order_by('category.name', 'ASC');
            $this->db->group_by('category.content_id');
            if(!empty($this->input->post('restaurant_id_for_category'))){
                $this->db->where_in('category.content_id',$category_ids);
            }
            // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            //     $this->db->where('category.created_by',$this->session->userdata('AdminUserID'));
            // } 
            $this->db->where('content_type','category');
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
            $this->db->select('category.entity_id, category.content_id, category.name, category.status, category.created_by, category.is_masterdata, category.language_slug,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');             
            $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = category.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');  
            if($content_general_id){
                $this->db->where_in('content_id',$content_general_id);    
            }
            if(!empty($this->input->post('restaurant_id_for_category'))){
                $this->db->where_in('category.content_id',$category_ids);
            }
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where("(".$where_titleserch.")");
            }    
            if($this->input->post('sSearch_3') != ''){
                $this->db->where('category.status', trim($this->input->post('sSearch_3')));
            }
            $this->db->select('content_general_id,category.*,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');   
            $this->db->join('content_general','content_general.content_general_id = category.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('category.created_by',$this->session->userdata('AdminUserID'));
            }
            $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = category.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');
            if(!empty($this->input->post('restaurant_id_for_category'))){
                $this->db->where_in('category.content_id',$category_ids);
            }
            $this->db->where('content_type','category');
            $this->db->group_by('category.content_id');
            //$this->db->order_by('category.entity_id','DESC');
            //$this->db->order_by('category.sequence','ASC');
            $this->db->order_by('sequence_no', 'ASC');
            $this->db->order_by('category.name', 'ASC');
            if($displayLength>1){
                if(empty($this->input->post('restaurant_owner_id')) && ($this->session->userdata('AdminUserType') != 'Restaurant Admin' && $this->session->userdata('AdminUserType') != 'Branch Admin')) {
                    $this->db->limit($displayLength,$displayStart);
                }
            }
            $cmsData = $this->db->get('category')->result();

            $this->db->select('category.entity_id, category.content_id, category.name, category.status, category.created_by, category.is_masterdata, category.language_slug,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');
            $this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = category.content_id AND catmap.restaurant_owner_id = '".$restaurant_owner_id."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left'); 

            $ContentID = array();               
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){            
                $this->db->order_by('FIELD (category.entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('content_id',$ContentID);
            }
            else
            {              
                if($where_titleserch!='')
                {
                    $this->db->where("(".$where_titleserch.")");
                } 
            }
        }
        if($this->input->post('sSearch_3') != ''){
            $this->db->where('category.status', trim($this->input->post('sSearch_3')));
        }  
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
        //     $this->db->where('category.created_by',$this->session->userdata('AdminUserID'));
        // }   
        //$this->db->order_by('category.entity_id','DESC');
        $this->db->order_by('sequence_no','ASC');
        $this->db->order_by('category.name', 'ASC');
        if(!empty($this->input->post('restaurant_id_for_category'))){
            $this->db->where_in('category.content_id',$category_ids);
        }
        $cmdData = $this->db->get('category')->result_array();
                         
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],          
                        'status' => $value['status'],
                        'created_by' => $value['created_by'],
                        'is_masterdata' => $value['is_masterdata']
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],        
                    'status' => $value['status'],    
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('category',array('entity_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // updating the changed
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
        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);  
    }
    //check  caregory sequence alotted 
    public function checkExist($sequence,$entity_id){
        $this->db->where('sequence',$sequence);
        $this->db->where('entity_id !=',$entity_id);
        return $this->db->get('category')->num_rows();
    }
    //only for menu category sequence map
    public function insertBatch($tblname,$data,$res_id,$restaurant_content_id) {
        $this->db->where('restaurant_owner_id',$res_id);
        $where_string="(restaurant_content_id IN(0,".$restaurant_content_id."))";
        $this->db->where($where_string);
        $this->db->delete($tblname);
    
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
    //get restaurant admins
    public function get_restaurant_admins()
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
        $this->db->where('res.restaurant_owner_id!=','');  
        $this->db->order_by('res.name', 'ASC'); 
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        return $this->db->get('restaurant as res')->result();
    }
    public function checkCatNameExist($category_name,$category_entity_id,$language_slug){
        $this->db->select('category.entity_id');
        $this->db->where('name',$category_name);
        $this->db->where('language_slug',$language_slug);
        if($category_entity_id) {
            $this->db->where('entity_id !=',$category_entity_id);
        }
        return $this->db->get('category')->num_rows();
    }

    public function get_categories_from_restaurant_owner_id($restaurant_id){
        $this->db->select('restaurant_menu_item.category_id, category.content_id');
        $this->db->where('restaurant_menu_item.restaurant_id',$restaurant_id); 
        $this->db->join('category','category.entity_id = restaurant_menu_item.category_id');
        $categories = $this->db->get('restaurant_menu_item')->result();
        return (!empty($categories)) ? array_column($categories, 'content_id') : '';
    }
    public function getCategoryName($category_entity_id = '', $category_content_id = '', $language_slug = '') {
        $this->db->select('name');
        if ($category_entity_id) {
            $this->db->where('entity_id',$category_entity_id);
        }
        if ($category_content_id) {
            $this->db->where('content_id',$category_content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('category')->first_row();
        return ($return->name) ? $return->name : '';
    }
}
?>