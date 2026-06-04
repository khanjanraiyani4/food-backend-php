<?php
class Contact_inquiries_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	//ajax view
	public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
	{
		if($this->input->post('first_name') != ''){
			$this->db->like('first_name', trim($this->common_model->escapeString($this->input->post('first_name'))));
		}
		if($this->input->post('last_name') != ''){
			$this->db->like('last_name', trim($this->common_model->escapeString($this->input->post('last_name'))));
		}
		if($this->input->post('email') != ''){
			$this->db->like('email', trim($this->common_model->escapeString($this->input->post('email'))));
		}
		if($this->input->post('rest_name') != ''){
			$this->db->like('rest_name', trim($this->common_model->escapeString($this->input->post('rest_name'))));
		}
		if($this->input->post('phone_number') != ''){
			$where_string = "(res_phone_number like '%".$this->common_model->escapeString(trim($this->input->post('phone_number')))."%' OR owners_phone_number like '%".$this->common_model->escapeString(trim($this->input->post('phone_number')))."%')";
			$this->db->where($where_string);
		}

		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		$this->db->select("contact_id");		
		$result['total'] = $this->db->count_all_results('contactus_detail');

		//========		
		$this->db->select("*");

		if($this->input->post('first_name') != ''){
			$this->db->like('first_name', trim($this->common_model->escapeString($this->input->post('first_name'))));
		}
		if($this->input->post('last_name') != ''){
			$this->db->like('last_name', trim($this->common_model->escapeString($this->input->post('last_name'))));
		}
		if($this->input->post('email') != ''){
			$this->db->like('email', trim($this->common_model->escapeString($this->input->post('email'))));
		}
		if($this->input->post('rest_name') != ''){
			$this->db->like('rest_name', trim($this->common_model->escapeString($this->input->post('rest_name'))));
		}
		if($this->input->post('phone_number') != ''){
			$where_string = "(res_phone_number like '%".$this->common_model->escapeString(trim($this->input->post('phone_number')))."%' OR owners_phone_number like '%".$this->common_model->escapeString(trim($this->input->post('phone_number')))."%')";
			$this->db->where($where_string);
		}

		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		
		if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }

		if($displayLength>1)
			$this->db->limit($displayLength,$displayStart);
		
		$result['data'] = $this->db->get('contactus_detail')->result();
		return $result;
	}
}