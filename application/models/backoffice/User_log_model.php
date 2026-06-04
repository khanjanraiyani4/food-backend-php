<?php
class User_log_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	//ajax view
	public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10) {
		if($this->input->post('full_name_search') != ''){
			$where_string = "((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('full_name_search')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('action_search') != ''){
			$this->db->like('action', trim($this->input->post('action_search')));
		}
		// if($this->input->post('user_ip_search') != ''){
		// 	$this->db->like('user_ip', trim($this->input->post('user_ip_search')));
		// }
		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(user_log.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(user_log.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		$this->db->select("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) AS 'user_full_name', user_log.*");
		$this->db->join('users','users.entity_id = user_log.user_id','left');
		$this->db->group_by('user_log.user_log_id');
		$result['total'] = $this->db->count_all_results('user_log');

		if($sortFieldName != ''){
			$this->db->order_by($sortFieldName, $sortOrder);
		}
		if($this->input->post('full_name_search') != ''){
			$where_string = "((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('full_name_search')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('action_search') != ''){
			$this->db->like('action', trim($this->input->post('action_search')));
		}
		// if($this->input->post('user_ip_search') != ''){
		// 	$this->db->like('user_ip', trim($this->input->post('user_ip_search')));
		// }
		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(user_log.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(user_log.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		$this->db->select("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) AS 'user_full_name', user_log.*");
		$this->db->join('users','users.entity_id = user_log.user_id','left');
		if($displayLength>1)
			$this->db->limit($displayLength,$displayStart);
		$this->db->group_by('user_log.user_log_id');
		$result['data'] = $this->db->get('user_log')->result();
		return $result;
	}
	public function getGridListForOrderLog($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10) {
		if($this->input->post('order_id_search') != ''){
			$this->db->like('order_status.order_id', trim($this->input->post('order_id_search')));
		}
		if($this->input->post('status_search') != ''){
			$this->db->like('order_status.order_status', trim($this->input->post('status_search')));
		}
		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(order_status.time) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(order_status.time) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('changed_by_search') != ''){
			$ifnullquery = "IFNULL((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END),order_status.status_created_by)";
			$where_string = "(".$ifnullquery." like '%".$this->common_model->escapeString(trim($this->input->post('changed_by_search')))."%')";
			$this->db->where($where_string);
		}
		$this->db->select("order_status.status_id");
		$this->db->join('users','order_status.user_id = users.entity_id','left');
		$this->db->join('order_master','order_status.order_id = order_master.entity_id','left');
		$this->db->group_by('order_status.status_id');
		$result['total'] = $this->db->count_all_results('order_status');

		if($sortFieldName != ''){
			if($sortFieldName == 'users.first_name') {
				$this->db->order_by('IFNULL(users.first_name,order_status.status_created_by)', $sortOrder, false);
			} else {
				$this->db->order_by($sortFieldName, $sortOrder);
			}
		}
		if($this->input->post('order_id_search') != ''){
			$this->db->like('order_status.order_id', trim($this->input->post('order_id_search')));
		}
		if($this->input->post('status_search') != ''){
			$this->db->like('order_status.order_status', trim($this->input->post('status_search')));
		}
		if($this->input->post('created_date_search') != ''){
			$explode_date = explode(' - ',trim($this->input->post('created_date_search')));
			$from_date = str_replace('-', '/', $explode_date[0]);
			$to_date = str_replace('-', '/', $explode_date[1]);
			$this->db->where('Date(order_status.time) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(order_status.time) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('changed_by_search') != ''){
			$ifnullquery = "IFNULL((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END),order_status.status_created_by)";
			$where_string = "(".$ifnullquery." like '%".$this->common_model->escapeString(trim($this->input->post('changed_by_search')))."%')";
			$this->db->where($where_string);
		}
		$this->db->select("order_status.*,CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) as user_full_name,order_master.order_status as ostatus,order_master.order_delivery as order_mode,order_master.status");
		$this->db->join('users','order_status.user_id = users.entity_id','left');
		$this->db->join('order_master','order_status.order_id = order_master.entity_id','left');
		if($displayLength>1)
			$this->db->limit($displayLength,$displayStart);
		$this->db->group_by('order_status.status_id');
		$result['data'] = $this->db->get('order_status')->result();
		return $result;
	}
	public function export_logs($start_date,$end_date) {
		$this->db->select("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) AS 'user_full_name', user_log.*");
		$this->db->join('users','users.entity_id = user_log.user_id','left');
		if($start_date && $end_date) {
			$this->db->where('Date(user_log.created_date) >=', $start_date);
			$this->db->where('Date(user_log.created_date) <=', $end_date);
		}
		$this->db->group_by('user_log_id');
		$result = $this->db->get('user_log')->result();
		return $result;
	}
}