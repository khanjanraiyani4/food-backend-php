<?php
class Faq_category_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //ajax view
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
                $lang_title_val = trim($this->input->post('title_'.$lang_name));
                if($lang_title_val!='')
                {
                    if($where_titleserch!='')
                    {
                        $where_titleserch .= ' OR ';
                    }
                    $where_titleserch .= " faq_category.name like '%".$this->common_model->escapeString($lang_title_val)."%' AND faq_category.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('status') != ''){
            $this->db->where('faq_category.status', trim($this->input->post('status')));
        }
        $this->db->group_by('content_id');
        $result['total'] = $this->db->count_all_results('faq_category');
        
        if($where_titleserch=='')
        {
            if($this->input->post('status') != ''){
                $this->db->where('faq_category.status', trim($this->input->post('status')));
            }
            $this->db->select('content_general_id,faq_category.*');
            $this->db->join('faq_category','faq_category.content_id = content_general.content_general_id','left');
            $this->db->order_by('faq_category.sequence','ASC');
            $this->db->group_by('faq_category.content_id');
            $this->db->where('content_type','faq_category');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if($content_general_id){
                $this->db->where_in('content_id',$content_general_id);
            }
        }else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }    
            if($this->input->post('status') != ''){
                $this->db->where('faq_category.status', trim($this->input->post('status')));
            }
            $this->db->select('content_general_id,faq_category.*');
            $this->db->join('content_general','content_general.content_general_id = faq_category.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('faq_category.created_by',$this->session->userdata('AdminUserID'));
            } 
            $this->db->where('content_type','faq_category');
            $this->db->group_by('faq_category.content_id');
            $this->db->order_by('faq_category.sequence','ASC');
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $cmsData = $this->db->get('faq_category')->result();
            $ContentID = array();
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID.','.$value->entity_id;
                $ContentID[] = $value->content_id;
            }   
            if($OrderByID && $ContentID){
                $this->db->order_by('FIELD ( entity_id,'.trim($OrderByID,',').') DESC');
                $this->db->where_in('content_id',$ContentID);
            }
            else
            {
                if($where_titleserch!='')
                {
                    $this->db->where($where_titleserch);
                }
            }
        }
        if($this->input->post('status') != ''){
            $this->db->where('faq_category.status', trim($this->input->post('status')));
        }
        $this->db->order_by('faq_category.sequence','ASC');
        $cmdData = $this->db->get('faq_category')->result_array();
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
                        'created_by' => $value['created_by']
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
        return $this->db->get_where('faq_category',array('entity_id'=>$entity_id))->first_row();
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
        return $this->db->get('faq_category')->num_rows();
    }
    public function getFaqCategory($entity_id = '', $content_id = '', $language_slug = ''){
        $this->db->select('name');
        if ($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if ($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('faq_category')->first_row();
        return ($return->name) ? $return->name : '';
    }
}