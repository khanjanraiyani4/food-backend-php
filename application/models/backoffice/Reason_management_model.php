<?php
class Reason_management_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //add to db
    public function add_data($tbl_name, $data)
    {
        $this->db->insert($tbl_name, $data);            
        return $this->db->insert_id();
    }
    public function update_data($data,$tbl_name,$field_name,$id)
    {
        $this->db->where($field_name,$id);
        $this->db->update($tbl_name,$data);
        return $this->db->affected_rows();
    }
    // updating the status
    public function update_status($content_id,$status){
        if($status == 0){
            $user_data = array('status' => 1);
        } else {
            $user_data = array('status' => 0);
        }
        $this->db->where('content_id',$content_id);
        $this->db->update('cancel_reject_reasons',$user_data);
        return $this->db->affected_rows();
    }
    //get data for edit
    public function get_edit_detail($entity_id)
    {
        return $this->db->get_where('cancel_reject_reasons', array('entity_id' => $entity_id))->first_row();
    }
    // delete 
    public function ajax_delete($tbl_name,$content_id,$entity_id)
    {
        if($content_id){
            $vals = $this->db->get_where($tbl_name,array('content_id'=>$content_id))->num_rows();    
            if($vals==1){
                $this->db->where(array('content_general_id' => $content_id));
                $this->db->delete('content_general');
            }            
        } 
        $this->db->where('entity_id',$entity_id);
        $this->db->delete($tbl_name);
    }
    public function ajax_delete_all($tblname,$content_id)
    {
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');
        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);
    }
    // method for getting all
    public function get_grid_list($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
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
                    $where_titleserch .= " cancel_reject_reasons.reason like '%".$this->common_model->escapeString($lang_title_val)."%' AND cancel_reject_reasons.language_slug ='".$lang_name."' ";
                }
            }
        }
        //New code for search with multi language title :: End
        if($where_titleserch!='')
        {
            $this->db->where($where_titleserch);
        }
        if($this->input->post('status') != ''){
            $this->db->like('status', trim($this->input->post('status')));
        }
        if($this->input->post('reason_type') != ''){
            $this->db->where('reason_type', trim($this->input->post('reason_type')));
        }
        if($this->input->post('user_type') != ''){
            $this->db->where('user_type', trim($this->input->post('user_type')));
        }
        $this->db->group_by('content_id');
        $result['total'] = $this->db->count_all_results('cancel_reject_reasons');
        if($where_titleserch=="" && $this->input->post('status') == '' && $this->input->post('reason_type') == '' && $this->input->post('user_type') == '')
        {
            $this->db->where('content_type','cancel_reject_reason');
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
                $this->db->like('status', trim($this->input->post('status')));
            }
            if($this->input->post('reason_type') != ''){
                $this->db->where('reason_type', trim($this->input->post('reason_type')));
            }
            if($this->input->post('user_type') != ''){
                $this->db->where('user_type', trim($this->input->post('user_type')));
            }
            $this->db->select('content_general_id,cancel_reject_reasons.user_type,cancel_reject_reasons.*');   
            $this->db->join('content_general','cancel_reject_reasons.content_id = content_general.content_general_id','left');
            $this->db->where('content_type','cancel_reject_reason');
            $this->db->group_by('cancel_reject_reasons.content_id');
            if($displayLength>1){
                $this->db->limit($displayLength,$displayStart);
            }
            $cmsData = $this->db->get('cancel_reject_reasons')->result();                      
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
                if($this->input->post('status') != ''){
                    $this->db->like('status', trim($this->input->post('status')));
                }
                if($this->input->post('reason_type') != ''){
                    $this->db->where('reason_type', trim($this->input->post('reason_type')));
                }
                if($this->input->post('user_type') != ''){
                    $this->db->where('user_type', trim($this->input->post('user_type')));
                }
            }
        } 
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);        
        }
        $cmdData = $this->db->get('cancel_reject_reasons')->result_array(); 
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value) {                
                if(!array_key_exists($value['content_id'],$cmsLang))
                {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id'=>$value['entity_id'],
                        'content_id' => $value['content_id'],
                        'reason' => $value['reason'],  
                        'reason_type' => $value['reason_type'],
                        'user_type' => $value['user_type'],
                        'status' => $value['status']
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'reason' => $value['reason'],
                    'reason_type' => $value['reason_type'],
                    'user_type' => $value['user_type'],
                    'status' => $value['status']
                );
            }
        }         
        $result['data'] = $cmsLang;        
        return $result;
    }
    public function getReasonName($entity_id = '', $content_id = '', $language_slug = ''){
        $this->db->select('reason');
        if ($entity_id) {
            $this->db->where('entity_id',$entity_id);
        }
        if ($content_id) {
            $this->db->where('content_id',$content_id);
            $this->db->where('language_slug',$language_slug);
        }
        $return = $this->db->get('cancel_reject_reasons')->first_row();
        return ($return->reason) ? $return->reason : '';
    }
}