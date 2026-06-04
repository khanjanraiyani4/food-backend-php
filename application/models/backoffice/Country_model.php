<?php
class Country_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //ajax view      
    public function get_grid_list($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('iso') != ''){
            $this->db->like('iso', trim($this->input->post('iso')));
        }
        if($this->input->post('nicename') != ''){
            $this->db->like('nicename', trim($this->input->post('nicename')));
        }
        if($this->input->post('phonecode') != ''){
            $this->db->like('phonecode', trim($this->input->post('phonecode')));
        }
        if($this->input->post('status') != ''){
            $this->db->like('status', trim($this->input->post('status')));
        }
        $this->db->select('id,iso,nicename,phonecode,status,set_default');
        $result['total'] = $this->db->count_all_results('country');
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        if($this->input->post('iso') != ''){
            $this->db->like('iso', trim($this->input->post('iso')));
        }
        if($this->input->post('nicename') != ''){
            $this->db->like('nicename', trim($this->input->post('nicename')));
        }
        if($this->input->post('phonecode') != ''){
            $this->db->like('phonecode', trim($this->input->post('phonecode')));
        }
        if($this->input->post('status') != ''){
            $this->db->like('status', trim($this->input->post('status')));
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart);     
        }
        $this->db->select('id,iso,nicename,phonecode,status,set_default');
        $result['data'] = $this->db->get('country')->result();
        return $result;
    }
    // updating the status
    public function update_status($id,$status){
        if($status == 0){
            $user_data = array('status' => 1);
        } else {
            $user_data = array('status' => 0);
        }
        $this->db->where('id',$id);
        $this->db->update('country',$user_data);
        return $this->db->affected_rows();
    }
    public function setDefaultCountryCode($entity_id,$setDefault){
        $this->db->set('set_default',1);
        $this->db->where('id',$entity_id);
        $this->db->update('country');
        $this->db->set('set_default',0);
        $this->db->where('id!=',$entity_id);
        $this->db->update('country');
    }
    public function getCountryName($entity_id = ''){
        $this->db->select('name,nicename');
        if ($entity_id) {
            $this->db->where('id',$entity_id);
        }
        $return = $this->db->get('country')->first_row();
        return ($return->nicename) ? $return->nicename : '';
    }
}