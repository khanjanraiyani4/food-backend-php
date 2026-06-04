<?php
class Restaurant_error_reports_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    public function getErrorReportList($searchTitleName = '', $sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        $this->db->select('entity_id,report_topic,reporter_email,reporter_message,created_date');
        if($this->input->post('error_report_title') != ''){
            $this->db->like('report_topic', trim($this->input->post('error_report_title')));
        }
        if($this->input->post('error_report_email') != ''){
            $this->db->like('reporter_email', trim($this->input->post('error_report_email')));
        }
        if($this->input->post('error_report_message') != ''){
            $this->db->like('reporter_message', trim($this->input->post('error_report_message')));
        }
        $result['total'] = $this->db->count_all_results('restaurant_error_reports');
        $this->db->select('entity_id,report_topic,reporter_email,reporter_message,created_date');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('error_report_title') != ''){
            $this->db->like('report_topic', trim($this->input->post('error_report_title')));
        }
        if($this->input->post('error_report_email') != ''){
            $this->db->like('reporter_email', trim($this->input->post('error_report_email')));
        }
        if($this->input->post('error_report_message') != ''){
            $this->db->like('reporter_message', trim($this->input->post('error_report_message')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('restaurant_error_reports')->result();        
        return $result;
    }
    public function deleteRecord($entity_id){          
        $this->db->where('entity_id',$entity_id);
        $this->db->delete('restaurant_error_reports');
        return $this->db->affected_rows();
    }
    public function ajaxviewReport($entity_id){
        return $this->db->get_where('restaurant_error_reports',array('entity_id '=>$entity_id))->result();
    }
}
?>