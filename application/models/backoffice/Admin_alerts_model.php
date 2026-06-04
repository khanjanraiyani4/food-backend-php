<?php
class Admin_alerts_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }       
    public function getPageList($searchTitleName = '', $sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('message') != ''){
            $this->db->like('message', trim($this->input->post('message')));
        }
        if($this->input->post('from_date') != ''){
            $this->db->where('from_date >=', date('Y-m-d',strtotime(trim($this->input->post('from_date')))));
        }
        if($this->input->post('to_date') != ''){
            $this->db->where('to_date <=', date('Y-m-d',strtotime(trim($this->input->post('to_date')))));
        }
        if($this->input->post('button_label') != ''){
            $this->db->like('button_label', trim($this->input->post('button_label')));
        }
        $result['total'] = $this->db->count_all_results('admin_alerts');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('message') != ''){
            $this->db->like('message', trim($this->input->post('message')));
        }
        if($this->input->post('from_date') != ''){
            $this->db->where('from_date >=', date('Y-m-d',strtotime(trim($this->input->post('from_date')))));
        }
        if($this->input->post('to_date') != ''){
            $this->db->where('to_date <=', date('Y-m-d',strtotime(trim($this->input->post('to_date')))));
        }
        if($this->input->post('button_label') != ''){
            $this->db->like('button_label', trim($this->input->post('button_label')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('admin_alerts')->result();        
        return $result;
    } 
}
?>