<?php
class Recipe_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();              
    }
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //New code for search with multi language title :: Start
        $LanguagesArr = $this->common_model->getLanguages();
        $where_titleserch = '';
        if(!empty($LanguagesArr) && count($LanguagesArr)>0)
        {
            for($ln=0;$ln<count($LanguagesArr);$ln++)
            {
                $lang_name = $LanguagesArr[$ln]->language_slug;
                $lang_title_val = $this->input->post('title_'.$lang_name);
                if($lang_title_val!='')
                {
                    if($where_titleserch!='')
                    {
                        $where_titleserch .= ' OR ';
                    }    
                    $where_titleserch .= " recipe.name like '%".$this->common_model->escapeString(trim($lang_title_val))."%' AND recipe.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('status') != ''){
            $this->db->where('recipe.status', $this->input->post('status'));
        }
        $this->db->group_by('content_id');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('recipe.created_by',$this->session->userdata('AdminUserID'));
        }           
        $result['total'] = $this->db->count_all_results('recipe');
        
        //if($this->input->post('page_title')=="")
        if($where_titleserch=='')
        { 
            if($this->input->post('status') != ''){
                $this->db->where('recipe.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,recipe.*');   
            $this->db->join('recipe','recipe.content_id = content_general.content_general_id','left');
            $this->db->group_by('recipe.content_id');
            //$this->db->order_by('recipe.entity_id', 'DESC');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('recipe.created_by',$this->session->userdata('AdminUserID'));
            } 
            $this->db->where('content_type','recipe');
            if($sortFieldName != ''){
                $this->db->order_by($sortFieldName, $sortOrder);    
            }            
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $dataCmsOnly = $this->db->get('content_general')->result();    
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('content_id',$content_general_id);    
            }            
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }    
            if($this->input->post('status') != ''){
                $this->db->where('recipe.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,recipe.*');   
            $this->db->join('content_general','recipe.content_id = content_general.content_general_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('recipe.created_by',$this->session->userdata('AdminUserID'));
            } 
            $this->db->where('content_type','recipe');
            $this->db->group_by('recipe.content_id');
            //$this->db->order_by('recipe.entity_id', 'DESC');
            if($sortFieldName != ''){
                $this->db->order_by($sortFieldName, $sortOrder);    
            }
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $cmsData = $this->db->get('recipe')->result();                      
            $ContentID = array();
            $OrderByID = '';
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID)
            {                        
                $this->db->order_by('FIELD ( entity_id,'.trim($OrderByID,',').') DESC');                
                $this->db->where_in('content_id',$ContentID);
            }
            else
            {                         
                if($where_titleserch!='')
                {
                    $this->db->where($where_titleserch);
                } 
                if($this->input->post('status') != ''){
                    $this->db->where('recipe.status', $this->input->post('status'));
                }
            }
        } 
        if($this->input->post('status') != ''){
            $this->db->where('recipe.status', $this->input->post('status'));
        }
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('recipe.created_by',$this->session->userdata('AdminUserID'));
        }   
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);    
        }
        $cmdData = $this->db->get('recipe')->result_array();
        
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id' => $value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'status' => $value['status'],
                        'is_masterdata' => $value['is_masterdata']           
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'status' => $value['status']   
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }
    public function add_data($tbl_name,$data)
    {   
        $this->db->insert($tbl_name,$data);            
        return $this->db->insert_id();
    }
    // get recipe slug
    public function get_recipe_slug($content_id){
        $this->db->select('slug');
        $this->db->where('content_id',$content_id);
        return $this->db->get('recipe')->first_row();
    }
    // get food type
    public function get_food_type($tblname,$language_slug=NULL){
        $this->db->select('name,entity_id');
        $this->db->where('status',1);
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }
    // get content id
    public function get_content_id($entity_id,$tblname){
        $this->db->select('content_id');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
    }
    public function update_data($data,$tblName,$field_name,$id)
    {        
        $this->db->where($field_name,$id);
        $this->db->update($tblName,$data);            
        return $this->db->affected_rows();
    }
    // method to get details by id
    public function get_edit_detail($tblname,$entity_id)
    {
        $this->db->where('entity_id',$entity_id);
        return $this->db->get($tblname)->first_row();
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
    public function ajax_delete_all($tblname,$content_id)
    {
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');                   
        /*$recipe_data = $this->db->select('image')
            ->where('content_id',$content_id)
            ->get($tblname)
            ->row_array();
        $recipe_image = $recipe_data['image'];
        if (is_file(FCPATH.'uploads/'.$recipe_image)) {
            @unlink(FCPATH.'uploads/'.$recipe_image);
        }*/
        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);    
    }
    // updating the changed status
    public function updated_status($tblname,$entity_id,$status){
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
    public function updated_status_all($tblname,$ContentID,$Status){
        if($Status==0){
            $Data = array('status' => 1);
        } else {
            $Data = array('status' => 0);
        }
        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }
    public function get_menu($language_slug,$recipe_content_id=NULL){
        $this->db->select('menu_content_id,recipe_content_id');
        $menu_content= $this->db->get('restaurant_menu_recipe_map')->result();
        $this->db->select('name,entity_id,content_id');
        if(!empty($menu_content)){
            $this->db->where_not_in('content_id',array_column($menu_content,'menu_content_id'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug); 
        }
        $this->db->where('status',1);
        $this->db->order_by('name', 'ASC');
        $data =  $this->db->get('restaurant_menu_item')->result();        
        if($recipe_content_id){
            $this->db->select('menu_content_id,recipe_content_id');
            $this->db->where('recipe_content_id',$recipe_content_id);
            $menu_content= $this->db->get('restaurant_menu_recipe_map')->result();            
            
            if($menu_content){
                $this->db->select('name,entity_id,content_id');
                if (!empty($language_slug)) {
                    $this->db->where('language_slug',$language_slug); 
                }
                $this->db->where_in('content_id',array_column($menu_content,'menu_content_id'));
                $this->db->where('restaurant_menu_item.status',1);
                $this->db->group_by('restaurant_menu_item.content_id');
                
                $result2 = $this->db->get('restaurant_menu_item')->result();
                $data = array_merge($data,$result2);
            }
        }
        return $data;
    }
    public function insertBatch($tblname,$data,$recipe_content){
        if($recipe_content){
            $this->db->where('recipe_content_id',$recipe_content);
            $this->db->delete($tblname);
        }
        if($data[0]['menu_content_id']!=''){
            $this->db->insert_batch($tblname,$data); 
            return $this->db->insert_id();
        }
    }
    public function getMenuItem($content_id){
        $this->db->select('menu_content_id');
        $this->db->where('recipe_content_id',$content_id);
        return $this->db->get('restaurant_menu_recipe_map')->result();
    }
    
    public function DeleteMenuContent($content_id){
        $this->db->where('recipe_content_id',$content_id);
        
        $this->db->delete('restaurant_menu_recipe_map');
    }
}
?>