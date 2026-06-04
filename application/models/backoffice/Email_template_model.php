<?php
class email_template_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    function addData($tblname,$addEmaildata)
    {
        $this->db->insert($tblname,$addEmaildata);
        return $this->db->insert_id();
    }
     // method for getting all
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
                    $where_titleserch .= " email_template.title like '%".$this->common_model->escapeString($lang_title_val)."%' AND email_template.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', trim($this->input->post('Status')));
        }
        $this->db->group_by('content_id');
                
        $result['total'] = $this->db->count_all_results('email_template');
        
        if($where_titleserch=="" && $this->input->post('Status') == ''){
            
            $this->db->where('content_type','email_template');
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
        }
        else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }
            if($this->input->post('Status') != ''){
                $this->db->like('status', trim($this->input->post('Status')));
            } 
            $this->db->select('content_general_id,email_template.*');   
            $this->db->join('content_general','email_template.content_id = content_general.content_general_id','left');
            
            $this->db->where('content_type','email_template');
            $this->db->group_by('email_template.content_id');
            if($displayLength>1)
                $this->db->limit($displayLength,$displayStart);
            $cmsData = $this->db->get('email_template')->result();                      
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
                if($this->input->post('Status') != ''){
                    $this->db->like('status', trim($this->input->post('Status')));
                }
            }
        }  
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('email_template')->result_array();       
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'title' => $value['title'],
                        'status' => $value['status'],                        
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'title' => $value['title'],
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }
    function getEditDetail($fldname,$entity_id)
    {
        return $this->db->get_where('email_template',array($fldname=>$entity_id))->first_row();
    }
    function editDetail($editData,$entity_id)
    {
        $this->db->where('entity_id',$entity_id);
        $this->db->update('email_template',$editData);
        return $this->db->affected_rows();        
    }
    function UpdatedStatus($tblname,$content_id,$Status){
        if($Status==0){
            $emailData = array('status' => 1);
        } else {
            $emailData = array('status' => 0);
        }        
        $this->db->where('content_id',$content_id);
        $this->db->update($tblname,$emailData);
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
    public function getTemplateName($entity_id = '', $content_id = '', $language_slug = ''){
        $this->db->select('title');
        if ($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if ($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('email_template')->first_row();
        return ($return->title) ? $return->title : '';
    }
}
?>