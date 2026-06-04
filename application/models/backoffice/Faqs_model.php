<?php
class Faqs_model extends CI_Model
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
                    $where_titleserch .= " faqs.question like '%".$this->common_model->escapeString($lang_title_val)."%' AND faqs.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('status') != ''){
            $this->db->where('faqs.status', trim($this->input->post('status')));
        }
        $this->db->group_by('content_id');
        $result['total'] = $this->db->count_all_results('faqs');
        
        if($where_titleserch=='')
        {
            if($this->input->post('status') != ''){
                $this->db->where('faqs.status', trim($this->input->post('status')));
            }
            $this->db->select('content_general_id,faqs.*');
            $this->db->join('faqs','faqs.content_id = content_general.content_general_id','left');
            $this->db->group_by('faqs.content_id');
            $this->db->where('content_type','faqs');
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
        }else
        {          
            if($where_titleserch!='')
            {
                $this->db->where($where_titleserch);
            }
            if($this->input->post('status') != ''){
                $this->db->where('faqs.status', trim($this->input->post('status')));
            }
            $this->db->select('content_general_id,faqs.*');
            $this->db->join('content_general','content_general.content_general_id = faqs.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('faqs.created_by',$this->session->userdata('AdminUserID'));
            } 
            $this->db->where('content_type','faqs');
            $this->db->group_by('faqs.content_id');
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $cmsData = $this->db->get('faqs')->result();
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
            $this->db->where('faqs.status', trim($this->input->post('status')));
        }
        $cmdData = $this->db->get('faqs')->result_array();
        $cmsLang = array();
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'question' => $value['question'],
                        'status' => $value['status'],
                        'created_by' => $value['created_by']
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'question' => $value['question'],
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
        return $this->db->get_where('faqs',array('entity_id'=>$entity_id))->first_row();
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

    public function get_faq_categories($language_slug = 'en')
    {
        return $this->db->select('entity_id,name,content_id')->get_where('faq_category',array(
            'language_slug' => $language_slug,
            'status' => 1
        ))->result();
    }

    public function get_faq_category_id($content_id, $language_slug)
    {
        $result =  $this->db->select('entity_id')->get_where('faq_category',array(
            'content_id' => $content_id,
            'language_slug' => $language_slug
        ))->first_row();
        return $result->entity_id;
    }

    public function get_faq_category_content_id($entity_id)
    {
        $result =  $this->db->select('content_id')->get_where('faq_category',array(
            'entity_id' => $entity_id
        ))->first_row();
        return $result->content_id;
    }

    // update data common function
    public function update_faq_category_id_for_other_faq_content($content_id,$language_slug,$data)
    {        
        $this->db->where('content_id',$content_id);
        $this->db->where('language_slug',$language_slug);
        $this->db->update('faqs',$data);
        return $this->db->affected_rows();
    }
    public function getFaqQuestion($entity_id = '', $content_id = '', $language_slug = ''){
        $this->db->select('question');
        if ($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if ($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('faqs')->first_row();
        return ($return->question) ? $return->question : '';
    }
}