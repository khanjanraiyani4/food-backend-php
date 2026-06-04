<?php
class Order_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}	
	// method for getting all
	public function getGridList($sortFieldName = '', $sortOrder = 'DESC', $displayStart = 0, $displayLength = 10,$order_status,$user_id,$order_id)
	{
		if($this->input->post('page_title') != ''){
			//$user_mobile_no = "CONCAT('+',COALESCE(u.phone_code,''),COALESCE(u.mobile_number,''))";
			//$where_string="(((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString($this->input->post('page_title'))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString($this->input->post('page_title'))."%')";
			$user_mobile_no = "CONCAT('+',COALESCE(order_detail.user_mobile_number,''))";
			$where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%') OR (order_detail.user_name like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', trim($this->input->post('order')));
		}
		if($this->input->post('driver') != ''){
			/*$this->db->where("(driver.first_name LIKE '%".$this->input->post('driver')."%' OR driver.last_name LIKE '%".$this->input->post('driver')."%')");*/
			$driver_mobile_no = "CONCAT('+',COALESCE(driver.phone_code,''),COALESCE(driver.mobile_number,''))";
			$where_string="(((CASE WHEN driver.last_name is NULL THEN driver.first_name ELSE CONCAT(driver.first_name,' ',driver.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('driver')))."%') OR (".$driver_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('driver')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', trim($this->input->post('Status')));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString(trim($this->input->post('restaurant')));
            $where = "(restaurant.name LIKE '%".$this->common_model->escapeString(trim($this->input->post('restaurant')))."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->or_where($where);
        }
		if($this->input->post('order_total') != ''){
            $total_price = trim($this->input->post('order_total'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('o.total_rate', trim($total_search));
            }else{
                $this->db->like('o.total_rate', trim($this->input->post('order_total')));
            }
		}
		if($this->input->post('payment_method') != ''){
			/*$pay_opt_str = str_replace(' ', '', trim($this->input->post('payment_method')));
			$this->db->like('o.payment_option', $pay_opt_str);*/
			$payment_method = strtolower($this->common_model->escapeString(trim($this->input->post('payment_method'))));
			if($payment_method == 'cod (driver tip paid via stripe - refunded)'){
				$where_string="(o.payment_option = 'cod' AND tips.refund_status = 'refunded')";
				$this->db->where($where_string);
			}else if($payment_method == 'apple pay' || $payment_method == 'applepay'){
				$where_string="(o.payment_option = 'applepay')";
				$this->db->where($where_string);
			}else if($payment_method == 'cod (driver tip paid via stripe)' || $payment_method == 'driver tip paid via stripe'){
				$where_string="(o.payment_option = 'cod' AND tips.refund_status != 'refunded')";
				$this->db->where($where_string);
			} else if($payment_method == 'stripe (refunded)'){
				$where_string="(o.payment_option = 'stripe' AND tips.refund_status == 'refunded')";
				$this->db->where($where_string);
			} else{
				$where_string="((o.payment_option like '%".$payment_method."%') OR (o.payment_option = 'cod' AND (tips.refund_status) like '%".$payment_method."%') OR (o.payment_option = 'stripe' AND ((tips.tips_transaction_id != '' AND tips.refund_status like '%".$payment_method."%' AND o.refund_status like '%".$payment_method."%') OR (tips.tips_transaction_id IS NULL AND o.refund_status like '%".$payment_method."%'))))";
				$this->db->where($where_string);
			}
		}
		if($this->input->post('order_status') != '')
		{
			$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%')";
			if(preg_match("/accepted/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			else if(preg_match("/delayed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.is_delayed = 1)";
			}
			//$this->db->like('o.order_status', $this->input->post('order_status'));
			$this->db->where($where_status);
		}
		if($this->input->post('order_date') != ''){
			// $this->db->like('o.created_date', $this->input->post('order_date'));
			$explode_date = explode(' - ',trim($this->input->post('order_date')));
			$from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
        	$this->db->where('Date(o.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(o.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('scheduled_date') != ''){
			$explode_schdate = explode(' - ',trim($this->input->post('scheduled_date')));
			$from_schdate = str_replace('-', '/', $explode_schdate[0]);
			$to_schdate = str_replace('-', '/', $explode_schdate[1]);
			$from_schdate = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDateSEC($from_schdate.' 00.00.00')));
			$to_schdate = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDateSEC($to_schdate.' 23.59.59')));
			/*$this->db->where('Date(o.scheduled_date) >=', $from_schdate);
			$this->db->where('Date(o.scheduled_date) <=', $to_schdate);*/
			$this->db->where('DATE_FORMAT(concat(o.scheduled_date," ",o.slot_open_time),"%Y-%m-%d %T") >=', $from_schdate);
			$this->db->where('DATE_FORMAT(concat(o.scheduled_date," ",o.slot_open_time),"%Y-%m-%d %T") <=', $to_schdate);
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',trim($this->input->post('order_delivery')));
		}
		if($this->input->post('delivery_method_filter') != ''){
			if($this->input->post('delivery_method_filter') == 'internal_drivers') {
				$this->db->where('o.delivery_method',trim($this->input->post('delivery_method_filter')));
			} else if($this->input->post('delivery_method_filter') == 'thirdparty_delivery') {
				$this->db->where_in('o.delivery_method',array('doordash','relay'));
			}
		}
		$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus,o.payment_option,o.payment_status,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,o.extra_comment,u.first_name as fname,u.last_name as lname,CONCAT('+',COALESCE(u.phone_code,''),COALESCE(u.mobile_number,'')) AS 'user_phn_no',u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,CONCAT('+',COALESCE(driver.phone_code,''),COALESCE(driver.mobile_number,'')) AS 'driver_phn_no',driver.entity_id as driver_id,order_detail.restaurant_detail,order_detail.user_name,order_detail.user_mobile_number,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.address_id,o.invoice,o.user_id as o_user_id,o.delivery_method,o.transaction_id,o.paid_status,o.refund_status,tips.tips_transaction_id,tips.refund_status as tips_refund_status,o.order_date,o.is_delayed,delayed_status_check.order_status as check_order_status,delayed_status_check.time as check_status_time,o.scheduled_date,o.slot_open_time,o.slot_close_time,o.stripe_refund_id,o.refunded_amount");
		$this->db->join('users as u','o.user_id = u.entity_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('order_status as delayed_status_check','o.entity_id = delayed_status_check.order_id AND (delayed_status_check.order_status = "delivered" OR delayed_status_check.order_status = "complete" OR delayed_status_check.order_status = "rejected" OR delayed_status_check.order_status = "cancel")','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		$this->db->join('tips','o.entity_id = tips.order_id AND tips.amount > 0','left');
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			/*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
			$this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
		}
		if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
		$this->db->where('o.order_delivery !=','DineIn');
		if($order_status != '')
		{
			$where_status = "(o.order_status like '%".trim($order_status)."%')";
			if(preg_match("/accepted/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%".trim($order_status)."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%".trim($order_status)."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			else if(preg_match("/delayed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.is_delayed = 1)";
			}
			//$this->db->like('o.order_status', $this->input->post('order_status'));
			$this->db->where($where_status);
		}
		
		if($user_id && is_numeric($user_id) && $user_id > 0){
			$this->db->where('o.user_id',$user_id);
		}
		if($order_id && is_numeric($order_id) && $order_id > 0){
			$this->db->where('o.entity_id',$order_id);
		}
		$this->db->group_by('o.entity_id');
		$result['total'] = $this->db->count_all_results('order_master as o');

		if($sortFieldName != ''){
			if($sortFieldName == 'o.created_date'){
				$this->db->order_by("o.order_status = 'placed'", 'DESC');
			}
			$this->db->order_by($sortFieldName, $sortOrder);
		}
		if($this->input->post('page_title') != ''){
			//$user_mobile_no = "CONCAT('+',COALESCE(u.phone_code,''),COALESCE(u.mobile_number,''))";
			//$where_string="(((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString($this->input->post('page_title'))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString($this->input->post('page_title'))."%')";
			$user_mobile_no = "CONCAT('+',COALESCE(order_detail.user_mobile_number,''))";
			$where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%') OR (order_detail.user_name like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('driver') != ''){
			/*$this->db->where("(driver.first_name LIKE '%".$this->input->post('driver')."%' OR driver.last_name LIKE '%".$this->input->post('driver')."%')");*/
			$driver_mobile_no = "CONCAT('+',COALESCE(driver.phone_code,''),COALESCE(driver.mobile_number,''))";
			$where_string="(((CASE WHEN driver.last_name is NULL THEN driver.first_name ELSE CONCAT(driver.first_name,' ',driver.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('driver')))."%') OR (".$driver_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('driver')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', trim($this->input->post('Status')));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString(trim($this->input->post('restaurant')));
            $where = "(restaurant.name LIKE '%".$this->common_model->escapeString(trim($this->input->post('restaurant')))."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->where($where);
        }
		if($this->input->post('order_total') != ''){
            $total_price = trim($this->input->post('order_total'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('o.total_rate', trim($total_search));
            }else{
                $this->db->like('o.total_rate', trim($this->input->post('order_total')));
            }
		}
		if($this->input->post('payment_method') != ''){
			/*$pay_opt_str = str_replace(' ', '', trim($this->input->post('payment_method')));
			$this->db->like('o.payment_option', $pay_opt_str);*/
			$payment_method = strtolower($this->common_model->escapeString(trim($this->input->post('payment_method'))));
			if($payment_method == 'cod (driver tip paid via stripe - refunded)'){
				$where_string="(o.payment_option = 'cod' AND tips.refund_status = 'refunded')";
				$this->db->where($where_string);
			}else if($payment_method == 'apple pay' || $payment_method == 'applepay'){
				$where_string="(o.payment_option = 'applepay')";
				$this->db->where($where_string);
			}else if($payment_method == 'cod (driver tip paid via stripe)' || $payment_method == 'driver tip paid via stripe'){
				$where_string="(o.payment_option = 'cod' AND tips.refund_status != 'refunded')";
				$this->db->where($where_string);
			} else if($payment_method == 'stripe (refunded)'){
				$where_string="(o.payment_option = 'stripe' AND tips.refund_status == 'refunded')";
				$this->db->where($where_string);
			} else{
				$where_string="((o.payment_option like '%".$payment_method."%') OR (o.payment_option = 'cod' AND (tips.refund_status) like '%".$payment_method."%') OR (o.payment_option = 'stripe' AND ((tips.tips_transaction_id != '' AND tips.refund_status like '%".$payment_method."%' AND o.refund_status like '%".$payment_method."%') OR (tips.tips_transaction_id IS NULL AND o.refund_status like '%".$payment_method."%'))))";
				$this->db->where($where_string);
			}
		}
		if($this->input->post('order_status') != '')
		{
			$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%')";
			if(preg_match("/accepted/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			else if(preg_match("/delayed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.is_delayed = 1)";
			}
			//$this->db->like('o.order_status', $this->input->post('order_status'));
			$this->db->where($where_status);
		}

		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', trim($this->input->post('order')));
		}
		if($this->input->post('order_date') != ''){
			// $this->db->like('o.created_date', $this->input->post('order_date'));
			$explode_date = explode(' - ',trim($this->input->post('order_date')));
			$from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
        	$this->db->where('Date(o.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(o.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('scheduled_date') != ''){
			$explode_schdate = explode(' - ',trim($this->input->post('scheduled_date')));
			$from_schdate = str_replace('-', '/', $explode_schdate[0]);
			$to_schdate = str_replace('-', '/', $explode_schdate[1]);
			$from_schdate = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDateSEC($from_schdate.' 00.00.00')));
			$to_schdate = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDateSEC($to_schdate.' 23.59.59')));
			/*$this->db->where('Date(o.scheduled_date) >=', $from_schdate);
			$this->db->where('Date(o.scheduled_date) <=', $to_schdate);*/
			$this->db->where('DATE_FORMAT(concat(o.scheduled_date," ",o.slot_open_time),"%Y-%m-%d %T") >=', $from_schdate);
			$this->db->where('DATE_FORMAT(concat(o.scheduled_date," ",o.slot_open_time),"%Y-%m-%d %T") <=', $to_schdate);
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',trim($this->input->post('order_delivery')));
		}
		if($this->input->post('delivery_method_filter') != ''){
			if($this->input->post('delivery_method_filter') == 'internal_drivers') {
				$this->db->where('o.delivery_method',trim($this->input->post('delivery_method_filter')));
			} else if($this->input->post('delivery_method_filter') == 'thirdparty_delivery') {
				$this->db->where_in('o.delivery_method',array('doordash','relay'));
			}
		}
		if($displayLength>1)
			$this->db->limit($displayLength,$displayStart);  
		$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus,o.payment_option,o.payment_status,o.status,o.restaurant_id,o.created_date,o.entity_id as entity_id,o.user_id,o.extra_comment,u.first_name as fname,u.last_name as lname,u.phone_code,u.mobile_number,u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,driver.phone_code as dphone_code,driver.mobile_number as dmobile_number,driver.entity_id as driver_id,order_detail.restaurant_detail,order_detail.user_name,order_detail.user_mobile_number,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.address_id,o.invoice,o.user_id as o_user_id,o.delivery_method,o.transaction_id,o.paid_status,o.refund_status,tips.tips_transaction_id,tips.refund_status as tips_refund_status,o.order_date,o.is_delayed,delayed_status_check.order_status as check_order_status,delayed_status_check.time as check_status_time,o.scheduled_date,o.slot_open_time,o.slot_close_time,o.stripe_refund_id, u_admin.first_name as adminf_name,u_admin.last_name as adminl_name, o.updated_by as order_update, o.updated_date as order_updated_date,o.refunded_amount");
		$this->db->join('users as u','o.user_id = u.entity_id','left');
		$this->db->join('users as u_admin','o.updated_by = u_admin.entity_id','left');
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('order_status as delayed_status_check','o.entity_id = delayed_status_check.order_id AND (delayed_status_check.order_status = "delivered" OR delayed_status_check.order_status = "complete" OR delayed_status_check.order_status = "rejected" OR delayed_status_check.order_status = "cancel")','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		$this->db->join('tips','o.entity_id = tips.order_id AND tips.amount > 0','left');
		if($order_status != '')
		{
			$where_status = "(o.order_status like '%".trim($order_status)."%')";
			if(preg_match("/accepted/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%".trim($order_status)."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%".trim($order_status)."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", trim($order_status)))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			else if(preg_match("/delayed/", trim($this->input->post('order_status'))))
			{
				$where_status = "(o.is_delayed = 1)";
			}
			//$this->db->like('o.order_status', $this->input->post('order_status'));
			$this->db->where($where_status);
		}

		if($user_id && is_numeric($user_id) && $user_id > 0){
			$this->db->where('o.user_id',$user_id);
		}
		if($order_id && is_numeric($order_id) && $order_id > 0){
			$this->db->where('o.entity_id',$order_id);
		}
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			/*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
			$this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
		}
		if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
		$this->db->where('o.order_delivery !=','DineIn');
		$this->db->group_by('o.entity_id');
		$result['data'] = $this->db->get('order_master as o')->result();		
		return $result;
	}		
	// method for adding 
	public function addData($tblName,$Data)
	{   
		$this->db->insert($tblName,$Data);            
		return $this->db->insert_id();
	} 
	// method for adding 
	public function addBatch($tblName,$Data)
	{   
		$this->db->insert_batch($tblName,$Data);            
		return $this->db->insert_id();
	}
	// get the drivers to assign to the orders
	/*
		Author: Chirag Thoriya
		Update: $this->db->where('status','1');
		Description: updated query for fetching driver which has status 1
		Updated on: 18/12/2020
	*/
	/*public function getDrivers()
	{
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin') {
			$this->db->select('restaurant.entity_id,restaurant.content_id');
			$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
			$res_ids = $this->db->get('restaurant')->result_array();
			if(!empty($res_ids)){
				$this->db->select('driver_id');
				$this->db->where_in('restaurant_content_id',array_column($res_ids, 'content_id'));
				$drivers = $this->db->get('restaurant_driver_map')->result_array();
			}
			if(!empty($drivers)){
				$this->db->where_in('users.entity_id',array_column($drivers, 'driver_id'));
			}
		}  
		$this->db->select('users.entity_id,users.first_name,users.last_name');
		$this->db->where('user_type','Driver');
		$this->db->where('status','1');
		$this->db->where('availability_status','1');
		$this->db->where('active','1');
		return $this->db->get('users')->result();
	}*/
	//New code as per required :: Start
	public function getDrivers()
	{
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin') {
			$this->db->select('restaurant.entity_id,restaurant.content_id');
			$this->db->where('restaurant.created_by',$this->session->userdata('AdminUserID'));
			$res_ids = $this->db->get('restaurant')->result_array();
			if(!empty($res_ids)){
				$this->db->select('driver_id');
				$this->db->where_in('restaurant_content_id',array_column($res_ids, 'content_id'));
				$drivers = $this->db->get('restaurant_driver_map')->result_array();
			}
			if(!empty($drivers)){
				$this->db->where_in('users.entity_id',array_column($drivers, 'driver_id'));
			}
		} 
		$this->db->select('users.entity_id,users.first_name,users.last_name,users.active');
		$this->db->where('user_type','Driver');
		$this->db->where('status','1');
		$this->db->where('availability_status','1');
		$this->db->where('active','1');
		/*if($this->session->userdata('AdminUserType') == 'Restaurant Admin') {
			$this->db->where('created_by',$this->session->userdata('UserID'));  
		}  */
		$result = $this->db->get('users')->result();

		//Code for find the on way drvier :: Start
		$order_st = array('onGoing','onGoing');
		//$order_st = array('preparing','onGoing','onGoing');
		$this->db->select('driver_map.driver_id');
		$this->db->where('order_master.order_delivery','Delivery');
		$this->db->where('driver_map.driver_id!=','');
		$this->db->where_in('order_master.order_status',$order_st);
		$this->db->join('order_driver_map as driver_map','order_master.entity_id = driver_map.order_id','left');
		$this->db->group_by('driver_map.driver_id');
		$resultin = $this->db->get('order_master')->result();
		$ongoing_driver = array();
		if($resultin && !empty($resultin))
		{
			$ongoing_driver = array_column($resultin, 'driver_id');
		}
		//Code for find the on way drvier :: End

		$resultfinal = array();
		for($i=0;$i<count($result);$i++)
		{
			$result[$i]->ongoing = 'no';
			if(in_array($result[$i]->entity_id,$ongoing_driver))
			{
				$result[$i]->ongoing = 'yes';
			}
			else if($result[$i]->active=='1')
			{
				$result[$i]->ongoing = '';
			}
			$resultfinal[$i] = $result[$i];
		}

		$keys = array_column($resultfinal, 'ongoing');
		array_multisort($keys, SORT_ASC, $resultfinal);
		return $resultfinal;
	}
	//New code as per required :: End
	
	// assign driver 
	public function getOrderDetails($order_id){ 
		$this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians(user_address.latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians(user_address.longitude) ) + sin ( radians(user_address.latitude) ) * sin( radians( address.latitude )))) as distance,order_master.delivery_charge");
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $distance = $this->db->get('order_master')->result();
	}
	//get comments
	public function getOrderComment($order_id){ 
		$this->db->select("extra_comment");
        $this->db->where('entity_id',$order_id);
        return $comment = $this->db->get('order_master')->result();
	}
	// method to get details by id
	public function getEditDetail($entity_id)
	{
		$this->db->select('order.entity_id,order.order_date,order.order_delivery,order.subtotal,order.tax_rate,order.tax_type,order.service_fee,order.service_fee_type,order.creditcard_fee,order.creditcard_fee_type,order.delivery_charge,order.coupon_discount,order.coupon_name,order.total_rate,order.invoice,order.transaction_id,order.extra_comment,res.name,res.phone_code as r_phone_code,res.phone_number as r_phone_number,res.is_printer_available,res.printer_paper_width,res.printer_paper_height,address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode,tb.table_number,tips.amount as tip_amount,order.payment_option,order.refund_status,order.delivery_method,order.user_id,order.restaurant_id,tips.tips_transaction_id,tips.refund_status as tips_refund_status, tips.payment_option as tip_payment_option, order.user_id');
		$this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
		$this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
		$this->db->join('users as u','order.user_id = u.entity_id','left');
		$this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
		$this->db->join('table_master as tb','order.table_id = tb.entity_id','left');
		$this->db->join('tips','order.entity_id = tips.order_id AND tips.amount > 0','left');
		return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
	}
	// update data common function
	public function updateData($Data,$tblName,$fieldName,$ID)
	{        
			$this->db->where($fieldName,$ID);
			$this->db->update($tblName,$Data);            
			return $this->db->affected_rows();
	}
	 // updating status and send request to driver
	public function UpdatedStatus($tblname,$entity_id,$restaurant_id,$order_id,$dine_in='',$orders_user_id)
	{
		$this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
		$this->db->set('order_status','accepted')->where('entity_id',$order_id)->update('order_master');
		$this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');
		if($orders_user_id>0){
			//send notification to user
			$this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug,users.notification,order_master.paid_status');
	        $this->db->join('users','order_master.user_id = users.entity_id','left');
	        $this->db->where('order_master.entity_id',$order_id);
	        $this->db->where('users.status',1);
	        $device = $this->db->get('order_master')->first_row();
	        
	        if($device->device_id && $device->notification == 1){  
	        	//get langauge
	        	$languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
	        	$this->lang->load('messages_lang', $languages->language_directory);
	            #prep the bundle
	            $fields = array();            
	            $message = sprintf($this->lang->line('push_order_accept'),$order_id);
	            $fields['to'] = $device->device_id; // only one user to send push notification
	            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
	            $fields['notification']['title'] = $this->lang->line('customer_app_name');
	            $fields['data'] = array ('screenType'=>'order');
	            if($dine_in =='yes' && $device->paid_status == 'unpaid')
	            {
	                $fields['data'] = array ('screenType'=>'dinein');
	            }

	            $headers = array (
	                'Authorization: key=' . FCM_KEY,
	                'Content-Type: application/json'
	            );
	            #Send Reponse To FireBase Server    
	            $ch = curl_init();
	            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	            curl_setopt( $ch,CURLOPT_POST, true );
	            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	            $result = curl_exec($ch);
	            curl_close($ch);  
	        } 
		} else {
			$this->db->select('order_delivery,order_master.paid_status');
	        $this->db->where('order_master.entity_id',$order_id);
	        $device = $this->db->get('order_master')->first_row();
		}
		//send notification to driver
		$this->db->select('restaurant.entity_id,restaurant.content_id');
		$this->db->where('entity_id',$restaurant_id);
		$restaurant_content_id = $this->db->get('restaurant')->first_row();
	    if($device->order_delivery == 'Delivery')
	    {
	        /* drivers assigned to multiple restaurant - start */
	        $this->db->select('driver_id');
	        $this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
	        $driver = $this->db->get('restaurant_driver_map')->result_array();
	        /* drivers assigned to multiple restaurant - end */	          
	        
	        //New update query on 20-11-2020
	        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
	        //$this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
	        //driver tracking join last entry changes :: start
	        $this->db->join('(select max(traking_id) as max_id, driver_id 
	        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
	        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
	        //driver tracking join last entry changes :: end
	        $this->db->where('users.user_type','Driver');
	        $this->db->where('driver_traking_map.latitude!=','');
	        $this->db->where('driver_traking_map.longitude!=','');
	        $this->db->where('users.status',1);
	        $this->db->where('users.availability_status',1);
	        $this->db->where('users.active',1);
	        if(!empty($driver)){
	            $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
	        }
	        $this->db->group_by('driver_traking_map.driver_id');
	        $this->db->order_by('driver_traking_map.created_date','desc');

	        $detail = $this->db->get('users')->result();
	        $flag = false;
	        if(!empty($detail)){
	        	if($orders_user_id>0){
					$this->db->select('u_address.latitude, u_address.longitude');
					$this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
					$this->db->where('order_master.entity_id',$order_id);
					$user_details = $this->db->get('order_master')->first_row();
					$user_lat = $user_details->latitude;
					$user_long = $user_details->longitude;
				} else {
					$this->db->select('user_detail');
					$this->db->where('order_id',$order_id);
					$user_details = $this->db->get('order_detail')->first_row();
					$user_lat_long = unserialize($user_details->user_detail);
					$user_lat = $user_lat_long['latitude'];
					$user_long = $user_lat_long['longitude'];
				}
				/*Begin::Finding Distance between restaurant to user*/
				$this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
				$this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
				$this->db->where('order_master.entity_id',$order_id);
				$user_to_restaurant_distance = $this->db->get('order_master')->first_row();
				/*End::Finding Distance between restaurant to user*/
	            foreach ($detail as $key => $value) {
	                $longitude = $value->longitude;
	                $latitude = $value->latitude;
	                $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
	                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
	                $this->db->where('restaurant.entity_id',$restaurant_id);
	                $this->db->having('distance <',DRIVER_NEAR_KM);
	                $result = $this->db->get('restaurant')->result();
	                if(!empty($result)){
	                    if($value->device_id){
	                    	//get langauge
	                    	$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
        					$this->lang->load('messages_lang', $languages->language_directory); 
	                        $flag = true;   
	                        $array = array(
	                            'order_id'=>$order_id,
	                            'driver_id'=>$value->driver_id,
	                            'date'=>date('Y-m-d H:i:s'),
	                            'distance'=>$user_to_restaurant_distance->distance
	                        );
	                        $id = $this->addData('order_driver_map',$array);
	                        #prep the bundle
	                        $fields = array();            
	                        $message = sprintf($this->lang->line('push_new_order'),$order_id);
	                        $fields['to'] = $value->device_id; // only one user to send push notification
	                        $fields['notification'] = array ('body'  => $message,'sound'=>'default');
	                        $fields['notification']['title'] = $this->lang->line('driver_app_name');
	                        $fields['data'] = array ('screenType'=>'order');
	                       
	                        $headers = array (
	                            'Authorization: key=' . FCM_KEY,
	                            'Content-Type: application/json'
	                        );
	                        #Send Reponse To FireBase Server    
	                        $ch = curl_init();
	                        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	                        curl_setopt( $ch,CURLOPT_POST, true );
	                        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	                        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	                        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	                        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	                        $result = curl_exec($ch);
	                        curl_close($ch);            
	                    } 
	                }
	            }
	        }
	    }
	}
	// delete
	public function ajaxDelete($tblname,$entity_id)
	{
		$this->db->delete($tblname,array('entity_id'=>$entity_id));  
	}
	//get users detail
	public function getUsersDetail($user_id){
		$this->db->select('users.first_name');
		$this->db->where('entity_id',$user_id);
		return $this->db->get('users')->result();
	}
	//get list
	public function getListData($tblname,$language_slug=NULL){
		if($tblname == 'users'){
			$this->db->select("first_name,last_name,entity_id,mobile_number as mobile_number_chk,CONCAT('+',COALESCE(phone_code,''),COALESCE(mobile_number,'')) AS 'mobile_number'");
			$this->db->where('status',1);
			$this->db->where('user_type','User');
			// if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			// 	$this->db->where('created_by',$this->session->userdata('UserID'));
			// }
			$this->db->order_by('first_name', 'ASC');
			return $this->db->get($tblname)->result();
		}else if($tblname == 'restaurant'){
			$this->db->select('name,entity_id,amount_type,amount,is_service_fee_enable,service_fee,service_fee_type,restaurant_owner_id,branch_admin_id');
			$this->db->where('status',1);
			if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
				/*$this->db->where('created_by',$this->session->userdata('UserID'));*/
				$this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
			}
			if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            	$this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        	}
			if($language_slug){
				$this->db->where('language_slug',$language_slug);
			}
			$this->db->order_by('name', 'ASC');
			return $this->db->get($tblname)->result();
		}else{
		    $this->db->select('name,entity_id,amount_type,amount');
			$this->db->where('status',1);
			return $this->db->get($tblname)->result();
		}
	}
	//get items
	public function getItem($entity_id){
		$this->db->select('menu.entity_id, menu.name, menu.price, menu.check_add_ons, menu.content_id, menu.category_id,cat.name as cat_name,cat.content_id as cat_content_id');
		$this->db->join('category as cat','cat.entity_id = menu.category_id','left');
		$this->db->where('menu.restaurant_id',$entity_id);
		$this->db->where('menu.status',1);
		$this->db->order_by('cat.name', 'ASC');
		$this->db->order_by('menu.name', 'ASC');
		$result =  $this->db->get('restaurant_menu_item as menu')->result();
		if(!empty($result)){
			$item_not_appicable_for_item_discount = array();
			$restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$entity_id);
			$category_discount = '';
			if(!empty($restaurant_data) && $restaurant_data->content_id){
				$category_discount = $this->common_model->getCategoryDiscount($restaurant_data->content_id);
			}
			$ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
			foreach ($result as $key => $value) {
                if(!empty($category_discount)){
                    foreach ($category_discount as $key => $cat_value) {
                        if(!empty($cat_value['combined'])){
                            if(isset($cat_value['combined'][$value->cat_content_id])){
                                if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                	array_push($item_not_appicable_for_item_discount, $value->content_id);
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Percentage'){
                                        $value->price = $value->price - (($value->price * $cat_value['combined'][$value->cat_content_id]['discount_value'])/100);
                                    }
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Amount'){
                                        $value->price = $value->price - $cat_value['combined'][$value->cat_content_id]['discount_value'];
                                    }
                                }
                            }
                        }
                    }
                }
				if(!empty($ItemDiscount)) {
					foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
						if(!empty($cpnvalue['itemDetail'])) {
							if(in_array($value->content_id,$cpnvalue['itemDetail']) && !in_array($value->content_id, $item_not_appicable_for_item_discount)){
								if($cpnvalue['max_amount'] <= $value->price){ 
									if($cpnvalue['amount_type'] == 'Percentage'){
										$value->price = $value->price - (($value->price * $cpnvalue['amount'])/100);
									}else if($cpnvalue['amount_type'] == 'Amount'){
										$value->price = $value->price - $cpnvalue['amount'];
									}
								}
							}
						}
					}
				}
			}
		}
		$Menucategory =  $this->getItemCategory($entity_id);
		foreach ($Menucategory as $key => $value) {
           	foreach ($result as $ky => $val) {
               	if($value->cat_name == $val->cat_name){
                    $data[$key][] = $val;
                }
            }
        }
        $categories = $this->getCategories();
        foreach ($result as $key => $value) {
            foreach ($categories as $key => $val) {
                if($val['name']==$value->cat_name){
                    $category_exist=1;
                }
            }
            if($category_exist==1){
                break;
            }else{
                $data[''][] = $value;
            }
        } 

		return isset($data)?$data:[];
		//return $result;
	}
	public function getOrderItem($entity_id)
	{
		/*$this->db->select('cat.name as cat_name');
		$this->db->join('category as cat','cat.entity_id = menu.category_id','left');
		$this->db->where('menu.restaurant_id',$entity_id);
		$this->db->where('menu.status',1);
		$this->db->where('menu.stock',1);
		$this->db->where('cat.status',1);
		$this->db->group_by('cat_name');
		//$this->db->order_by('menu.name', 'ASC');
		$category = $this->db->get('restaurant_menu_item as menu')->result();*/
		$this->db->select('menu.entity_id, menu.name, menu.price, menu.check_add_ons, menu.content_id, menu.category_id,cat.name as cat_name,cat.content_id as cat_content_id');
		$this->db->join('category as cat','cat.entity_id = menu.category_id','left');
		$this->db->where('menu.restaurant_id',$entity_id);
		$this->db->where('menu.status',1);
		$this->db->where('menu.stock',1);
		$this->db->where('cat.status',1);
		$this->db->order_by('cat.name', 'ASC');
		$this->db->order_by('menu.name', 'ASC');
		$data = $this->db->get('restaurant_menu_item as menu')->result();
		//apply vategories/items coupon 
		if(!empty($data)){
			$item_not_appicable_for_item_discount = array();
			$restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$entity_id);
			$category_discount = '';
			if(!empty($restaurant_data) && $restaurant_data->content_id){
				$category_discount = $this->common_model->getCategoryDiscount($restaurant_data->content_id);
			}
			$ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
			foreach ($data as $key => $value) {
                if(!empty($category_discount)){
                    foreach ($category_discount as $key => $cat_value) {
                        if(!empty($cat_value['combined'])){
                            if(isset($cat_value['combined'][$value->cat_content_id])){
                                if($value->price >= $cat_value['combined'][$value->cat_content_id]['minimum_amount']){
                                	array_push($item_not_appicable_for_item_discount, $value->content_id);
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Percentage'){
                                        $value->price = $value->price - (($value->price * $cat_value['combined'][$value->cat_content_id]['discount_value'])/100);
                                    }
                                    if($cat_value['combined'][$value->cat_content_id]['discount_type'] == 'Amount'){
                                        $value->price = $value->price - $cat_value['combined'][$value->cat_content_id]['discount_value'];
                                    }
                                }
                            }
                        }
                    }
                }
				if(!empty($ItemDiscount)) {
					foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
						if(!empty($cpnvalue['itemDetail'])) {
							if(in_array($value->content_id,$cpnvalue['itemDetail']) && !in_array($value->content_id, $item_not_appicable_for_item_discount)){
								if($cpnvalue['max_amount'] <= $value->price){ 
									if($cpnvalue['amount_type'] == 'Percentage'){
										$value->price = $value->price - (($value->price * $cpnvalue['amount'])/100);
									}else if($cpnvalue['amount_type'] == 'Amount'){
										$value->price = $value->price - $cpnvalue['amount'];
									}
								}
							}
						}
					}
				}
			}
		}
		$Menucategory =  $this->getItemCategory($entity_id);
		foreach ($Menucategory as $key => $value) {
            foreach ($data as $ky => $val) {
                if($value->cat_name == $val->cat_name){
                    $result[$key][] = $val;
                }
            }
        }
		$categories = $this->getCategories();
        foreach ($data as $key => $value) {
            foreach ($categories as $key => $val) {
                if($val['name']==$value->cat_name){
                    $category_exist=1;
                }
            }
            if($category_exist==1){
                break;
            }else{
                $result[''][] = $value;
            }
        }   
        return $result;
	}
	//get address
	public function getAddress($entity_id){
		$this->db->select('entity_id,address');
		$this->db->where('user_entity_id',$entity_id);
		$this->db->order_by('address', 'ASC');
		return $this->db->get('user_address')->result();
	}
	//get invoice data
	public function getInvoiceMenuItem($entity_id){
		$this->db->select('restaurant_detail, user_detail, item_detail, user_mobile_number, user_name');
		$this->db->where('order_id',$entity_id);
		return $this->db->get('order_detail')->first_row();
	}
	//get user data
	public function getUserDate($entity_id){
		$this->db->select('device_id,language_slug');
		$this->db->where('entity_id',$entity_id);
		return $this->db->get('users')->first_row();
	}
	//delete multiple order
	public function deleteMultiOrder($order_id){
		$this->db->where_in('entity_id',$order_id);
		$this->db->delete('order_master');
		return $this->db->affected_rows();
	}
	//get item name
	public function getItemName($item_id){
		$this->db->where('entity_id',$item_id);
		return $this->db->get('restaurant_menu_item')->first_row();
	}
	//get order status history
	public function statusHistory($order_id){
		$this->db->select("order_status.*,CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) as user_full_name");
		$this->db->join('users','order_status.user_id = users.entity_id','left');
		$this->db->where('order_id',$order_id);
		return $this->db->get('order_status')->result();
	}
	public function statusOrderHistory($order_id){
		$this->db->select('order_status,order_delivery,status');
		$this->db->where('entity_id',$order_id);
		return $this->db->get('order_master')->first_row();
	}
	//get rest detail
	public function getRestaurantDetail($entity_id){
		$this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,restaurant.phone_code');
		$this->db->join('restaurant_address','restaurant.entity_id = restaurant_address.resto_entity_id','left');
		$this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
		$this->db->where('restaurant.entity_id',$entity_id);
		$result = $this->db->get('restaurant')->first_row();
		if(!empty($result)){
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$result->currency_symbol = $default_currency->currency_symbol;
				$result->currency_code = $default_currency->currency_code;
			}
		}
		return $result;
	}
	//get list of restaurant
	public function getRestaurantList($language_slug=NULL)
	{
		$this->db->select('entity_id, name');
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			/*$this->db->where('created_by',$this->session->userdata('UserID'));*/
			$this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
		}
		if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
		if($language_slug){
			$this->db->where('language_slug',$language_slug);	
		}   
		return $this->db->get('restaurant')->result();
	}
	//generate report data
	public function generate_report($restaurant_id,$order_type,$order_date){
		$this->db->select('order_master.*,restaurant.name,users.first_name,users.last_name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
		$this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
		$this->db->join('users','order_master.user_id = users.entity_id','left');
		$this->db->where('restaurant_id',$restaurant_id);
		if($order_type){
			$this->db->where('order_delivery',$order_type);
		}
		if($order_date != ''){
			$this->db->like('order_master.created_date', date('Y-m-d',strtotime($order_date))); 
		}
		/*if($order_date){
			$monthsplit = explode("-",$order_date);         
			$this->db->where('MONTH(order_master.created_date)',$monthsplit[0]);
			$this->db->where('YEAR(order_master.created_date)',$monthsplit[1]);
		}*/
		return $this->db->get('order_master')->result();
	}
	
	public function getDevice($user_id){
	    $this->db->select('users.entity_id,users.device_id,users.language_slug,users.notification');
        $this->db->where('users.entity_id',$user_id);
        $this->db->where('status',1);
        return $this->db->get('users')->first_row(); 
	}
	//Code add to find the branch admin user device id :: Start :: 12-10-2020
	public function getOrderUsers($user_id,$address_id='')
	{	    
        if($address_id!='')
        {
        	$this->db->select('users.first_name, users.last_name, users.phone_code, users.mobile_number, users_add.address, users_add.landmark, users_add.zipcode,
	    	users_add.city, users_add.address_label, users_add.latitude, users_add.longitude');
		    $this->db->join('user_address as users_add','users_add.user_entity_id = users.entity_id','left');
	        $this->db->where('users.entity_id',$user_id);
        	$this->db->where('users_add.entity_id',$address_id);
        	return $this->db->get('users')->row_array(); 
        }
        else
        {
        	$this->db->select('users.first_name, users.last_name, users.phone_code, users.mobile_number');
	        $this->db->where('users.entity_id',$user_id);
        	return $this->db->get('users')->row_array(); 
        }        
	}//End

	//Code add to find the branch admin user device id :: Start :: 12-10-2020
	public function getBranchAdminDevice($restaurant_id)
	{
		$this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res))
        {
            $this->db->select('users.device_id, users.language_slug, users.notification, users.status');
            $this->db->join('users','users.entity_id = restaurant.branch_admin_id','left');
            $this->db->where('restaurant.entity_id',$result_res->entity_id);
            $this->db->where('restaurant.branch_admin_id!=',NULL);            
            $result = $this->db->get('restaurant')->result(); 
            return $result;
        }
        return false;
	}
	//Code add to find the branch admin user device id :: End :: 12-10-2020
	//get order details
	public function orderDetails($entity_id){
		$this->db->select('order_detail.*,order_master.*,table.*, tips.amount as tip_amount,tips.tip_percentage');
		$this->db->where('order_master.entity_id',$entity_id);
		$this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
		$this->db->join('table_master as table','table.entity_id = order_master.table_id','left');
		$this->db->join('tips','order_master.entity_id = tips.order_id AND tips.amount > 0','left');
		return $this->db->get('order_master')->result();
	}
    // get latest order of logged in user
    public function getLatestOrder($order_id){
        $this->db->select('order_master.entity_id as master_order_id,order_master.*,order_detail.*,order_driver_map.driver_id,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1','left');
        //$this->db->join('driver_traking_map','users.entity_id = driver_traking_map.driver_id AND driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY created_date DESC LIMIT 1)','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
        $this->db->where('order_master.entity_id',$order_id);
        
        $result = $this->db->get('order_master')->first_row();
        if (!empty($result)) {
        	$default_currency = get_default_system_currency();
            $result->placed = $result->created_date;
            //$result->preparing = '';
            $result->onGoing = '';
            $result->delivered = '';
            // get order status
            $this->db->where('order_status.order_id',$result->master_order_id);
            $Ostatus = $this->db->get('order_status')->result_array();
            if (!empty($Ostatus)) {
                foreach ($Ostatus as $key => $ovalue) {
                    if ($ovalue['order_status'] == 'accepted_by_restaurant') {
                        $result->accepted_by_restaurant = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    /*if ($ovalue['order_status'] == 'preparing') {
                        $result->preparing = $this->common_model->getZonebaseTime($ovalue['time']);
                    }*/
                    if ($ovalue['order_status'] == 'onGoing') {
                        $result->onGoing = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                    if ($ovalue['order_status'] == 'delivered') {
                        $result->delivered = $this->common_model->getZonebaseTime($ovalue['time']);
                    }
                }
            }
            $user_detail = unserialize($result->user_detail);
            if (!empty($user_detail)) {
                $result->user_first_name = $user_detail['first_name'];
                $result->user_address = $user_detail['address'];
                $result->user_latitude = $user_detail['latitude'];
                $result->user_longitude = $user_detail['longitude'];
                $result->image = ($result->image)?image_url.$result->image:'';
            }
            if(!empty($default_currency)){
            	$result->currency_symbol = $default_currency->currency_symbol;
                $result->currency_code = $default_currency->currency_code;
            }
        }
        return $result;
    }
    //changes for addons start
	//get addon data
	public function addonDetails($field,$id,$restaurant_entity_id)
	{
		$restaurant_content_id = $this->common_model->getContentId($restaurant_entity_id,'restaurant');
		$this->db->select('add_ons_id,add_ons_name,add_ons_price,category_id,is_multiple,display_limit,add_ons_master.mandatory,add_ons_category.name as cat_name,add_ons_category.entity_id as addons_cat_id, (CASE WHEN  menumap.sequence_no is NULL THEN 1000 ELSE menumap.sequence_no END) as sequence_no');
		$this->db->join('add_ons_category','add_ons_category.entity_id = add_ons_master.category_id','left');
		$this->db->join('menu_addons_sequencemap as menumap',"menumap.add_ons_content_id = add_ons_category.content_id AND menumap.restaurant_owner_id = '".$this->session->userdata('UserID')."' AND menumap.restaurant_content_id = '".$restaurant_content_id."'",'left');
		$this->db->where($field,$id);
		$this->db->group_by('add_ons_master.add_ons_id');
		$this->db->order_by('sequence_no,add_ons_category.name', 'ASC');
		$result = $this->db->get('add_ons_master')->result_array();
        
        return $result;
	}
    public function getMenuDetail($entity_id,$language_slug='',$restaurant_id='') {
		$this->db->select('menu.entity_id, menu.name, menu.price, menu.check_add_ons, menu.content_id, menu.category_id,cat.name as cat_name,cat.content_id as cat_content_id,menu.restaurant_id,menu.item_slug,menu.sku,menu.menu_detail,menu.image,menu.recipe_time,menu.availability,menu.is_veg,menu.is_combo_item,menu.food_type,menu.status,menu.popular_item,menu.language_slug,menu.is_deal,menu.is_masterdata');
		$this->db->join('category as cat','cat.entity_id = menu.category_id','left');
		$this->db->where('menu.entity_id',$entity_id);
		$this->db->where('menu.status',1);
		$this->db->where('menu.stock',1);
		$this->db->order_by('cat.name', 'ASC');
		$this->db->order_by('menu.name', 'ASC');
		if($language_slug!='')
		{
			$this->db->where('menu.language_slug',$language_slug);
		}		
		$result = $this->db->get('restaurant_menu_item as menu')->first_row();
		if(!empty($result)){
			$item_not_appicable_for_item_discount = array();
			$restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$restaurant_id);
			$category_discount = '';
			if(!empty($restaurant_data) && $restaurant_data->content_id){
				$category_discount = $this->common_model->getCategoryDiscount($restaurant_data->content_id);
			}
			$ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
            if(!empty($category_discount)){
                foreach ($category_discount as $key => $cat_value) {
                    if(!empty($cat_value['combined'])){
                        if(isset($cat_value['combined'][$result->cat_content_id])){
                            if($result->price >= $cat_value['combined'][$result->cat_content_id]['minimum_amount']){
                            	array_push($item_not_appicable_for_item_discount, $result->content_id);
                                if($cat_value['combined'][$result->cat_content_id]['discount_type'] == 'Percentage'){
                                    $result->price = $result->price - (($result->price * $cat_value['combined'][$result->cat_content_id]['discount_value'])/100);
                                }
                                if($cat_value['combined'][$result->cat_content_id]['discount_type'] == 'Amount'){
                                    $result->price = $result->price - $cat_value['combined'][$result->cat_content_id]['discount_value'];
                                }
                            }
                        }
                    }
                }
            }
			if(!empty($ItemDiscount)) {
				foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
					if(!empty($cpnvalue['itemDetail'])) {
						if(in_array($result->content_id,$cpnvalue['itemDetail']) && !in_array($result->content_id, $item_not_appicable_for_item_discount)){
							if($cpnvalue['max_amount'] <= $result->price){ 
								if($cpnvalue['amount_type'] == 'Percentage'){
									$result->price = $result->price - (($result->price * $cpnvalue['amount'])/100);
								}else if($cpnvalue['amount_type'] == 'Amount'){
									$result->price = $result->price - $cpnvalue['amount'];
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}
	public function getAddonsDetail($addons_id) {
		$this->db->select('add_ons_name,add_ons_price,category_id');
		$this->db->where('add_ons_id',$addons_id);
		return $this->db->get('add_ons_master')->first_row();
	}
	public function getAddonsCatDetail($addonsCat_id) {
		$this->db->select('name');
		$this->db->where('entity_id',$addonsCat_id);
		return $this->db->get('add_ons_category')->first_row();
	}
	//changes for addons end
	public function getCoupon($subtotal,$restaurant_content_id,$user_id='',$order_delivery)
	{
		//Code to check the user is new or not :: Start
		$user_chkcpn = 'yes';
        if($user_id!='')
        {            
            $this->db->select('entity_id');
            $this->db->where('user_id',$user_id);
            $user_chk = $this->db->count_all_results('order_master');
            if($user_chk>0)
            {
                $user_chkcpn = 'no';
            }
        }
        //Code to check the user is new or not :: End
        $this->db->select('coupon.name,coupon.entity_id,coupon.amount_type,coupon.amount,coupon.coupon_type, coupon.maximaum_use_per_users, coupon.maximaum_use, coupon.use_with_other_coupons,coupon.coupon_for_newuser');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        $this->db->where('coupon.max_amount <=',$subtotal);
        $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
        $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        $this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        if($user_chkcpn=='no') {
             $this->db->where('coupon_type !=' , 'user_registration');
        }
        $this->db->where('(coupon_type != "discount_on_items")');
        $this->db->where('(coupon_type != "discount_on_categories")');
        
        if($order_delivery == 'Delivery')
        {
        	$this->db->where('coupon_type !=' , 'dine_in');
            //$this->db->or_where('coupon.coupon_type',"free_delivery");
        	if($user_chkcpn=='no') {
        		$coupontype_arr = array('free_delivery','discount_on_cart');
        	}
        	else
        	{
        		$coupontype_arr = array('free_delivery','discount_on_cart','user_registration');
        	}
        	$this->db->where_in('coupon_type',$coupontype_arr);
            $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
            $this->db->where('DATE_FORMAT(coupon.start_date,"%Y-%m-%d %T") <=',date('Y-m-d H:i:s'));
        	$this->db->where('DATE_FORMAT(coupon.end_date,"%Y-%m-%d %T") >=',date('Y-m-d H:i:s'));
        }
        else if($order_delivery == 'DineIn') {
        	$this->db->where('coupon.coupon_type != "free_delivery"');
            //$this->db->or_where('coupon.coupon_type',"dine_in");
            //$this->db->or_where('coupon.coupon_type',"dine_in");
            if($user_chkcpn=='no') {
        		$coupontype_arr = array('dine_in','discount_on_cart');
        	}
        	else
        	{
        		$coupontype_arr = array('dine_in','discount_on_cart','user_registration');
        	}
        	$this->db->where_in('coupon_type',$coupontype_arr);
            $this->db->where('coupon_restaurant_map.restaurant_id',$restaurant_content_id);
            $this->db->where('DATE(coupon.start_date) <=',date('Y-m-d H:i:s'));
            $this->db->where('DATE(coupon.end_date) >=',date('Y-m-d H:i:s'));
        }
        else {
            $this->db->where('coupon.coupon_type != "free_delivery"');
            $this->db->where('coupon_type!=',"dine_in");
        }
        $this->db->where('coupon.status',1);
        $this->db->order_by('name', 'ASC');
        $this->db->group_by('coupon.entity_id');
        return $this->db->get('coupon')->result();
	}
	//Code added for fetch the coupon detail :: 14-10-2020 :: Start
	public function getCouponData($coupon_id) {	
		$this->db->select('entity_id, name, amount_type, amount, coupon_type');	
		$this->db->where('entity_id',$coupon_id);	
		return $this->db->get('coupon')->first_row();	
	}
	//Code added for fetch the coupon detail :: 14-10-2020 :: End
	//export order 15-01-2021 vip.. start
	public function export_order($order_id,$restIds,$order_type,$start_date,$end_date)
	{
		$all = array('Delivery', 'PickUp');
		$this->db->select('o.transaction_id,o.coupon_name,o.coupon_discount,o.subtotal,o.delivery_charge,o.tax_rate,o.tax_type,o.service_fee,o.service_fee_type,o.creditcard_fee,o.creditcard_fee_type,o.order_delivery,o.total_rate as rate,o.order_status as ostatus,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,o.extra_comment,o.cancel_reason,o.reject_reason,o.delivery_method,u.first_name as fname,u.last_name as lname,u.entity_id as user_id,order_detail.user_detail,order_detail.item_detail,order_detail.restaurant_detail,restaurant.name,restaurant.contractual_commission,tips.amount as tip_amount, o.payment_option, o.refund_status, o.scheduled_date, o.slot_open_time, o.slot_close_time,tips.tips_transaction_id,tips.refund_status as tips_refund_status, o.delivery_instructions, o.refunded_amount, o.stripe_refund_id, o.refund_reason, o.updated_by as order_updated_by, u_admin.first_name as adminf_name,u_admin.last_name as adminl_name, o.updated_date as order_updated_date,restaurant.contractual_commission_type,restaurant.contractual_commission_type_delivery,restaurant.contractual_commission_delivery');
		$this->db->join('users as u','o.user_id = u.entity_id','left');
		$this->db->join('users as u_admin','o.updated_by = u_admin.entity_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left');
		$this->db->join('tips','o.entity_id = tips.order_id AND tips.amount > 0','left');
		if($restIds != 'all') {
			$this->db->where_in('restaurant_id',array_column($restIds, 'restaurant_id'));
		}
		if(!empty($order_id)) {
			$this->db->where_in('o.entity_id',$order_id);
		}
		if($order_type == 'Delivery' || $order_type == 'PickUp' || $order_type == 'DineIn'){
			$this->db->where('o.order_delivery',$order_type);
		}
		if($order_type == 'all'){
			$this->db->where_in('o.order_delivery',$all);	
		}
		if($start_date && $end_date){
			$this->db->where('Date(o.order_date) >=', $start_date);
			$this->db->where('Date(o.order_date) <=', $end_date);
		}
		$this->db->group_by('o.entity_id');
		return $this->db->get('order_master as o')->result_array();
	}
	//export order 15-01-2021 vip.. end

	//Dine in view grid list
	public function get_dine_in_grid_list($sortFieldName = '', $sortOrder = 'DESC', $displayStart = 0, $displayLength = 10,$user_id,$order_id)
	{
		if($this->input->post('page_title') != ''){
			$user_mobile_no = "CONCAT('+',COALESCE(u.phone_code,''),COALESCE(u.mobile_number,''))";
			$where_string="(((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', trim($this->input->post('order')));
		}
		if($this->input->post('driver') != ''){
			$this->db->where("(driver.first_name LIKE '%".trim($this->input->post('driver'))."%' OR driver.last_name LIKE '%".trim($this->input->post('driver'))."%')");
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString($this->input->post('restaurant'));
            $where = "(restaurant.name LIKE '%".$this->common_model->escapeString(trim($this->input->post('restaurant')))."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->or_where($where);
        }
		if($this->input->post('order_total') != ''){
			$total_price = trim($this->input->post('order_total'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('o.total_rate', trim($total_search));
            }else{
                $this->db->like('o.total_rate', trim($this->input->post('order_total')));
            }
		}
		if($this->input->post('payment_status') != '')
		{
			//$this->db->where('o.paid_status', $this->input->post('payment_status'));
			if($this->input->post('payment_status')=='paid')
	        {
	            $where_cond="((o.order_status In('delivered','cancel','rejected') OR o.admin_payment_option IS NOT NULL OR o.payment_option in ('stripe','paypal')) OR (o.order_status In('complete') AND o.paid_status='paid'))";
	            $this->db->where($where_cond);
	        }
	        else
	        {
	            $where_cond="((o.order_status Not In('delivered','cancel','rejected','complete') AND o.admin_payment_option IS NULL AND o.payment_option Not in ('stripe','paypal')) OR (o.order_status In('complete') AND o.paid_status!='paid') AND o.admin_payment_option IS NULL AND o.payment_option Not in ('stripe','paypal'))";
	            $this->db->where($where_cond);
	        }
		}
		if($this->input->post('admin_payment_option') != ''){
			$this->db->where('o.admin_payment_option', $this->input->post('admin_payment_option'));
		}
		if($this->input->post('order_status') != '')
		{
			$where_status = "(o.order_status like '%".trim($this->input->post('order_status'))."%')";
			if(preg_match("/accepted/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%".$this->input->post('order_status')."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%".$this->input->post('order_status')."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			$this->db->where($where_status);
		}
		if($this->input->post('order_date') != ''){
			$explode_date = explode('-',$this->input->post('order_date'));
			$from_date = $explode_date[0];
        	$to_date = $explode_date[1];
        	$this->db->where('Date(o.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(o.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',$this->input->post('order_delivery'));
		}
		$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus , o.paid_status as pstatus,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,o.extra_comment,u.first_name as fname,u.last_name as lname,u.phone_code,u.mobile_number,u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,driver.entity_id as driver_id,order_detail.restaurant_detail,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.address_id,o.invoice,o.payment_option,o.admin_payment_option,o.is_parcel_order, table.table_number,order_detail.user_mobile_number,o.user_id as o_user_id");
		//$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus , o.paid_status as pstatus,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,u.first_name as fname,u.last_name as lname,u.phone_code,u.mobile_number,u.entity_id as user_id,driver.first_name,driver.last_name,order_detail.restaurant_detail,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.payment_option,o.admin_payment_option,order_detail.user_mobile_number");
		$this->db->join('users as u','o.user_id = u.entity_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		$this->db->join('table_master as table','table.entity_id = o.table_id','left');
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			/*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
			$this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
		}
		if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
		
		$this->db->where('o.order_delivery','DineIn');
		
		if($user_id && is_numeric($user_id) && $user_id>0){
			$this->db->where('o.user_id',$user_id);
		}
		if($order_id && is_numeric($order_id) && $order_id>0){
			$this->db->where('o.entity_id',$order_id);
		}
		//Condition to find the unpaid order with restaurant		
		
		$this->db->group_by('o.entity_id');
		$result['total'] = $this->db->count_all_results('order_master as o');

		if($sortFieldName != ''){
			$this->db->order_by($sortFieldName, $sortOrder);
		}
		if($this->input->post('page_title') != ''){
			$user_mobile_no = "CONCAT('+',COALESCE(u.phone_code,''),COALESCE(u.mobile_number,''))";
			$where_string="(((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
			$this->db->where($where_string);
		}
		if($this->input->post('driver') != ''){
			$this->db->where("(driver.first_name LIKE '%".trim($this->input->post('driver'))."%' OR driver.last_name LIKE '%".trim($this->input->post('driver'))."%')");
		}
		if($this->input->post('Status') != ''){
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString($this->input->post('restaurant'));
            $where = "(restaurant.name LIKE '%".$this->common_model->escapeString(trim($this->input->post('restaurant')))."%' OR (order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*'))";
            $this->db->or_where($where);
        }
		if($this->input->post('order_total') != ''){
			$total_price = trim($this->input->post('order_total'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('o.total_rate', trim($total_search));
            }else{
                $this->db->like('o.total_rate', trim($this->input->post('order_total')));
            }
		}
		if($this->input->post('payment_status') != '')
		{
			//$this->db->where('o.paid_status', $this->input->post('payment_status'));
			if($this->input->post('payment_status')=='paid')
	        {
	            /*$where_cond="(o.order_status In('delivered','cancel','rejected','complete') OR o.admin_payment_option IS NOT NULL OR o.payment_option in ('stripe','paypal'))";
	            $this->db->where($where_cond);*/

	            $where_cond="((o.order_status In('delivered','cancel','rejected') OR o.admin_payment_option IS NOT NULL OR o.payment_option in ('stripe','paypal')) OR (o.order_status In('complete') AND o.paid_status='paid'))";
	            $this->db->where($where_cond);
	        }
	        else
	        {
	            //$this->db->where_not_in('o.order_status',array('delivered','cancel','rejected','complete'));	            
	            $where_cond="((o.order_status Not In('delivered','cancel','rejected','complete') AND o.admin_payment_option IS NULL AND o.payment_option Not in ('stripe','paypal')) OR (o.order_status In('complete') AND o.paid_status!='paid') AND o.admin_payment_option IS NULL AND o.payment_option Not in ('stripe','paypal'))";
	            $this->db->where($where_cond);	            
	        }
		}
		if($this->input->post('admin_payment_option') != ''){
			$this->db->where('o.admin_payment_option', $this->input->post('admin_payment_option'));
		}
		if($this->input->post('order_status') != '')
		{
			$where_status = "(o.order_status like '%".$this->input->post('order_status')."%')";
			if(preg_match("/accepted/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%".$this->input->post('order_status')."%' OR (o.order_status like 'placed' AND o.status ='1'))";
			}
			else if(preg_match("/placed/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%".$this->input->post('order_status')."%' AND o.status !='1')";
			}
			else if(preg_match("/orderready/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='PickUp')";
			}
			else if(preg_match("/onGoing/", $this->input->post('order_status')))
			{
				$where_status = "(o.order_status like '%onGoing%' AND o.order_delivery ='Delivery')";
			}
			$this->db->where($where_status);
		}

		if($this->input->post('order') != ''){
			$this->db->like('o.entity_id', trim($this->input->post('order')));
		}
		if($this->input->post('order_date') != ''){
			$explode_date = explode('-',$this->input->post('order_date'));
			$from_date = $explode_date[0];
        	$to_date = $explode_date[1];
        	$this->db->where('Date(o.created_date) >=', date('Y-m-d',strtotime($from_date)));
			$this->db->where('Date(o.created_date) <=', date('Y-m-d',strtotime($to_date)));
		}
		if($this->input->post('order_delivery') != ''){
			$this->db->where('o.order_delivery',$this->input->post('order_delivery'));
		}
		if($displayLength>1){
			$this->db->limit($displayLength,$displayStart);  
		}
		$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus, o.paid_status as pstatus, o.status, o.restaurant_id, o.created_date,o.entity_id as entity_id,o.user_id,o.extra_comment,u.first_name as fname,u.last_name as lname,u.phone_code,u.mobile_number,u.entity_id as user_id,order_status.order_status as orderStatus,driver.first_name,driver.last_name,driver.entity_id as driver_id,order_detail.restaurant_detail,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.address_id,o.invoice,o.payment_option,o.admin_payment_option,o.is_parcel_order,table.table_number,order_detail.user_mobile_number,o.user_id as o_user_id, o.coupon_amount, o.coupon_discount");
		//$this->db->select("o.order_delivery,o.total_rate as rate,o.order_status as ostatus , o.paid_status as pstatus,o.status,o.entity_id as entity_id,o.created_date,o.restaurant_id,u.first_name as fname,u.last_name as lname,u.phone_code,u.mobile_number,u.entity_id as user_id,driver.first_name,driver.last_name,order_detail.restaurant_detail,restaurant.is_printer_available,restaurant.name,restaurant.currency_id,o.payment_option,o.admin_payment_option,order_detail.user_mobile_number");
		$this->db->join('users as u','o.user_id = u.entity_id','left');   
		$this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
		$this->db->join('order_status','o.entity_id = order_status.order_id','left');
		$this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
		$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
		$this->db->join('table_master as table','table.entity_id = o.table_id','left');

		$this->db->where('o.order_delivery','DineIn');

		if($user_id && is_numeric($user_id) && $user_id>0){
			$this->db->where('o.user_id',$user_id);
		} 
		if($order_id && is_numeric($order_id) && $order_id>0){
			$this->db->where('o.entity_id',$order_id);
		}
		//Condition to find the unpaid order with restaurant		

		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			/*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
			$this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
		}
		if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
		$this->db->group_by('o.entity_id');
		$result['data'] = $this->db->get('order_master as o')->result();    
		return $result;
	}
	public function update_payment_data($payment_data){
		return $this->db->update_batch('order_master',$payment_data, 'entity_id');
	}
	public function check_order_paid_or_not($order_id){
		$this->db->select('entity_id,paid_status,order_status as ostatus,payment_option,admin_payment_option');
    	$this->db->where('entity_id',$order_id);
    	return $this->db->get('order_master')->first_row();
	}
	public function get_dinein_order($order_id)
	{
		$this->db->select('om.entity_id as order_id, om.user_id, om.restaurant_id, om.coupon_id, om.table_id, om.total_rate, om.subtotal, om.tax_rate, om.tax_type, om.coupon_discount, om.coupon_name, om.coupon_amount, om.coupon_type, om.extra_comment, od.item_detail, od.user_detail, res.name as restaurant_name, CONCAT(us.first_name, " ",us.last_name) as user_name,tb.table_number, us.device_id, us.notification, us.language_slug, om.service_fee_type, om.service_fee, om.order_status, om.paid_status,om.creditcard_fee_type,om.creditcard_fee,om.used_earning, om.delivery_charge, tips.amount as tip_amount, om.delivery_charge, om.creditcard_fee_type, om.creditcard_fee, om.refunded_amount, om.refund_reason, om.payment_option, om.transaction_id, od.user_mobile_number, od.user_name as order_user_name, us.phone_code, us.mobile_number, us.email');
        $this->db->join('order_detail as od','od.order_id = om.entity_id');
        $this->db->join('restaurant as res','om.restaurant_id = res.entity_id');
        $this->db->join('tips as tips','tips.order_id = om.entity_id AND tips.amount > 0','left');
        $this->db->join('users as us','om.user_id = us.entity_id','left');
        $this->db->join('table_master as tb','om.table_id = tb.entity_id','left');
        $this->db->where('om.entity_id',$order_id);
        $detail = $this->db->get('order_master as om')->first_row();
        $order_detailarr = array();
        if($detail && !empty($detail))
        {
        	$order_detailarr =  array(
	            'entity_id' => $detail->order_id,
	            'order_id' => $detail->order_id,
	            'user_id' => $detail->user_id,
	            'device_id' => $detail->device_id,
	            'notification' => $detail->notification,
	            'language_slug' => $detail->language_slug,
	            'restaurant_id' => $detail->restaurant_id,
	            'coupon_id' => $detail->coupon_id,
	            'table_id' => $detail->table_id,
	            'total_rate' => $detail->total_rate,
	            'subtotal' => $detail->subtotal,
	            'tax_rate' => $detail->tax_rate,
	            'tax_type' => $detail->tax_type,
	            'service_fee_type' => $detail->service_fee_type,
	            'service_fee' => $detail->service_fee,
	            'coupon_discount' => $detail->coupon_discount,
	            'coupon_name' => $detail->coupon_name,
	            'coupon_amount' => $detail->coupon_amount,
	            'coupon_type' => $detail->coupon_type,
	            'creditcard_fee_type' => $detail->creditcard_fee_type,
	            'creditcard_fee' => $detail->creditcard_fee,
	            'used_wallet_balance' => $detail->used_earning,
	            'delivery_charge' => $detail->delivery_charge,
	            'tip_amount' => $detail->tip_amount,
	            'extra_comment'=> $detail->extra_comment,
	            'restaurant_name' => $detail->restaurant_name,
	            'user_name' => $detail->user_name,
	            'table_number' => $detail->table_number,
	            'order_status' => $detail->order_status,
	            'paid_status' => $detail->paid_status,
	            'refunded_amount' => $detail->refunded_amount,
	            'refund_reason' => $detail->refund_reason,
	            'payment_option' => $detail->payment_option,
	            'transaction_id' => $detail->transaction_id,
	            'item_detail' => unserialize($detail->item_detail),
	            'user_detail' => unserialize($detail->user_detail),
	            'user_mobile_number' => $detail->user_mobile_number,
	            'order_user_name' => $detail->order_user_name,
	            'phone_code' => $detail->phone_code,
	            'mobile_number' => $detail->mobile_number,
	            'email' => $detail->email
	        );
        }
        return $order_detailarr;
	}
	public function get_is_update_order_detail()
    {
    	$in_statusarr = array('complete','delivered','cancel','rejected');
		$this->db->select('order_detail.order_id');
		$this->db->where_not_in('order_master.order_status',$in_statusarr);
		$this->db->where('is_updateorder','1');
		$this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
		$this->db->group_by('order_detail.order_id');
		$result = $this->db->get('order_master')->result();
		return $result;
    }
    public function update_is_update_attribute($order_id){
    	$this->db->where('order_id',$order_id);
    	$data = array('is_updateorder' => '0',);
        $this->db->update('order_detail',$data);
        return $this->db->affected_rows();	
    }
    public function checkExists($mobile_number)
    {
    	$this->db->select('entity_id, first_name, last_name');
        $this->db->where('mobile_number',$mobile_number);
        return $this->db->get('users')->first_row();
    }
    //get tables
	public function getTables($entity_id){
		$this->db->select('entity_id,table_number');
		$this->db->where('resto_entity_id',$entity_id);
		$this->db->where('status',1);
		$this->db->order_by('table_number', 'ASC');
		return $this->db->get('table_master')->result();
	}
	public function getusers($mobile_number,$alldata='no')
    {
    	if($alldata=='yes')
    	{
    		$this->db->select('entity_id, first_name, last_name, mobile_number,email');
    		$this->db->where('status',1);
    		$this->db->where('mobile_number',$mobile_number);
    		$this->db->where('user_type','User');
    		return $this->db->get('users')->first_row();
    	}
    	else
    	{
    		/*$this->db->select('CONCAT(mobile_number," (",UPPER(SUBSTRING(first_name,1,1)),LOWER(SUBSTRING(first_name,2))," ",last_name,") ") as name, mobile_number');*/
    		$this->db->select('first_name, last_name, mobile_number');
    		$this->db->like('mobile_number',$mobile_number);
    		$this->db->where('mobile_number!=',null);
    		$this->db->where('mobile_number!=','');
    		$this->db->where('user_type','User');
    		$this->db->where('status',1);
    		return $this->db->get('users')->result();
    	}
    }
    public function getAssignDrvier($order_id)
	{
        $this->db->select('driver_id');        
        $this->db->where('order_id',$order_id);
        $this->db->where('is_accept','1');        
        $result = $this->db->get('order_driver_map')->first_row();
        return $result;
    }
	public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->get($table)->first_row();
    }
	//get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
    // function to get users total wallet money
    public function getUsersWalletMoney($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    // delete wallet history by order_id
    public function deletewallethistory($order_id){
        $this->db->where('order_id',$order_id);
		$this->db->update('wallet_history',array('is_deleted'=> 1));
		return $this->db->affected_rows();
    }
    //update record with multiple where
    public function updateMultipleWhere($table,$whereArray,$data)
    {
        $this->db->where($whereArray);
        $this->db->update($table,$data);
    }
    // get systemOption
    public function getSystemOption($OptionSlug){
        $this->db->select('OptionValue');
        $this->db->where('system_option.OptionSlug',$OptionSlug);
        return $this->db->get('system_option')->first_row();
    }

    function cancel_reject_reasons($language_slug, $reason_type = 'cancel', $user_type="Restaurant Admin"){
		$this->db->select('reason');
		$this->db->where('status',1);
		$this->db->where('reason_type',$reason_type);
		if($this->session->userdata('AdminUserType') == 'Driver'){
			$this->db->where('user_type','Driver');
		}
		else{
			$this->db->where('user_type','Admin');
		}
		$this->db->where('language_slug',$language_slug);
		return $this->db->get('cancel_reject_reasons')->result();	
    }
    public function checkExistPhnNo($mobile_number,$phone_code){
    	$this->db->where('user_type','User');
        $this->db->where('mobile_number',$mobile_number);
        //$this->db->where('phone_code',$phone_code);
        if($phone_code)
        {
            if(strstr($phone_code,"+"))
            {
                $phone_codeval=str_replace("+", "", $phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }
            else
            {
                $phone_codeval="+".$phone_code;
                //$this->db->where('phone_code',$phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }            
        }
        $numrows = $this->db->get('users')->num_rows();
        $user_id = '';
        if($numrows){
        	$this->db->select('entity_id,mobile_number');
        	$this->db->where('user_type','User');
        	$this->db->where('mobile_number',$mobile_number);
        	if($phone_code)
	        {
	            if(strstr($phone_code,"+"))
	            {
	                $phone_codeval=str_replace("+", "", $phone_code);
	                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
	            }
	            else
	            {
	                $phone_codeval="+".$phone_code;
	                //$this->db->where('phone_code',$phone_code);
	                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
	            }            
	        }
        	$user = $this->db->get('users')->first_row();
        	$user_id = $user->entity_id;
        	$phone_number = $user->mobile_number;
        }
        return $arr = array('user_id'=>$user_id, 'numrows'=>$numrows, 'phone_number'=>$phone_number);
    }
    // get address latlong
    public function getAddressLatLng($entity_id){
    	$this->db->select('latitude,longitude');
    	return $this->db->get_where('user_address',array('entity_id'=>$entity_id))->first_row();
    }
    //get delivery charges by lat long
    public function checkGeoFence($restaurant_id)
    {
    	$this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);        
        $resultcont = $this->db->get('restaurant')->first_row();

        $this->db->where('restaurant_id',$resultcont->content_id);
        return $this->db->get('delivery_charge')->result();
    }
    public function notiToDriver($order_id,$restaurant_id){
    	//send notification to driver
		$this->db->select('restaurant.entity_id,restaurant.content_id');
		$this->db->where('entity_id',$restaurant_id);
		$restaurant_content_id = $this->db->get('restaurant')->first_row();
		    
        /* drivers assigned to multiple restaurant - start */
        $this->db->select('driver_id');
        $this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
        $driver = $this->db->get('restaurant_driver_map')->result_array();
        /* drivers assigned to multiple restaurant - end */
        //New update query on 20-11-2020
        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
        //$this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->where('users.user_type','Driver');
        $this->db->where('users.status',1);
        if(!empty($driver)){
            $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
        }
        $this->db->group_by('driver_traking_map.driver_id');
        $this->db->order_by('driver_traking_map.created_date','desc');

        $detail = $this->db->get('users')->result();
        $flag = false;
        if(!empty($detail)){
			/*Begin::Finding Distance between restaurant to user*/
			$this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians(u_address.latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians(u_address.longitude) ) + sin ( radians(u_address.latitude) ) * sin( radians( address.latitude )))) as distance");
				$this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
				$this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
			$this->db->where('order_master.entity_id',$order_id);
			$user_to_restaurant_distance = $this->db->get('order_master')->first_row();
			/*End::Finding Distance between restaurant to user*/
            foreach ($detail as $key => $value) {
                $longitude = $value->longitude;
                $latitude = $value->latitude;
                $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                $this->db->where('restaurant.entity_id',$restaurant_id);
                $this->db->having('distance <',DRIVER_NEAR_KM);
                $result = $this->db->get('restaurant')->result();
                if(!empty($result)){
                    if($value->device_id){
                    	//get langauge
                    	$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
    					$this->lang->load('messages_lang', $languages->language_directory); 
                        $flag = true;   
                        $array = array(
                            'order_id'=>$order_id,
                            'driver_id'=>$value->driver_id,
                            'date'=>date('Y-m-d H:i:s'),
                            'distance'=>$user_to_restaurant_distance->distance
                        );
                        $id = $this->addData('order_driver_map',$array);
                        #prep the bundle
                        $fields = array();            
                        $message = sprintf($this->lang->line('push_new_order'),$order_id);
                        $fields['to'] = $value->device_id; // only one user to send push notification
                        $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                        $fields['notification']['title'] = $this->lang->line('driver_app_name');
                        $fields['data'] = array ('screenType'=>'order');
                       
                        $headers = array (
                            'Authorization: key=' . Driver_FCM_KEY,
                            'Content-Type: application/json'
                        );
                        #Send Reponse To FireBase Server    
                        $ch = curl_init();
                        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                        curl_setopt( $ch,CURLOPT_POST, true );
                        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                        $result = curl_exec($ch);
                        curl_close($ch);            
                    } 
                }
            }
        }
    }

    //Delete order dirver relation before assign
	public function DelOrderbeforAssign($order_id,$user_id)
	{
		$this->db->where('order_id', $order_id);
        $this->db->where('is_accept !=',1);
        $this->db->where('is_accept !=',2);
        //$this->db->where('driver_id !=',$user_id);
        $this->db->delete('order_driver_map'); 
	}
	public function GetUsername($user_id)
	{
		$this->db->select('first_name, last_name');        
        $this->db->where('entity_id',$user_id);
        return  $this->db->get('users')->first_row();
	}
	public function getResContentId($restaurant_id){
		$this->db->select('content_id');
		$this->db->where('entity_id',$restaurant_id);
		$result = $this->db->get('restaurant')->first_row();
		return $result->content_id;
	}
	public function getResIds($restaurant_id){
		$content_id = $this->getResContentId($restaurant_id);
		$this->db->select('entity_id as restaurant_id');
		$this->db->where('content_id',$content_id);
		$result = $this->db->get('restaurant')->result_array();
		return $result;
	}

	public function checkEmailExist($email){
        $this->db->where('email',$email);
        $this->db->where('user_type','User');
        $numrows = $this->db->get('users')->num_rows();
        $user_id = '';
        if($numrows){
        	$this->db->select('entity_id,mobile_number');
        	$this->db->where('user_type','User');
        	$this->db->where('email',$email);
        	$user = $this->db->get('users')->first_row();
        	$user_id = $user->entity_id;
        	$phone_number = $user->mobile_number;
        }
        return $arr = array('user_id'=>$user_id, 'numrows'=>$numrows, 'phone_number'=>$phone_number);
    }
    public function getrestaurantData($language_slug=NULL)
    {
		$this->db->select('name,entity_id,amount_type,amount,is_service_fee_enable,service_fee,service_fee_type, timings, enable_hours, order_mode');
		$this->db->where('status',1);
		$this->db->where('enable_hours',1);
		/*if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
			$this->db->where('created_by',$this->session->userdata('UserID'));  
		}*/
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
            //$this->db->where('branch_admin_id',$this->session->userdata('parent_adminid'));
        }
		if($language_slug){
			$this->db->where('language_slug',$language_slug);
		}
		$this->db->order_by('name', 'ASC');

		$resultarr = $this->db->get('restaurant')->result();
		$result= [];
		if(!empty($resultarr))
		{
			foreach($resultarr as $key => $value)
			{
				$timing = $value->timings;
				if($timing)
				{
					$timing =  unserialize(html_entity_decode($timing));
					$day = date("l");
					foreach($timing as $keys=>$values)
					{ 
						$day = date("l");
						if($keys == strtolower($day))
						{
						    $close = 'close';
						    if($value->enable_hours=='1')
						    {   
						        $close = 'close';
						        if(!empty($values['open']) && !empty($values['close']))
						        {
						            $close = $this->openclose($values['open'],$values['close']);
						        }
						        $newTimingArr[strtolower($day)]['closing'] = $close;
						    }						    
						}
					}
				}
				if($close=='open')
				{
					$result[]= $value;
				}
			}
		}
		return $result;		
	}
	public function openclose($start_time,$end_time)
    {
        $current_time = date('h:i a');
        $start_time = date('h:i a', strtotime($start_time));
        $end_time = date('h:i a', strtotime($end_time));

        $date1 = DateTime::createFromFormat('H:i a', $current_time)->getTimestamp(); 
        $date2 = DateTime::createFromFormat('H:i a', $start_time)->getTimestamp();; 
        $date3 = DateTime::createFromFormat('H:i a', $end_time)->getTimestamp(); 
        if ($date3 < $date2) { 
            $date3 += 24 * 3600; 
            if ($date1 < $date2) { 
                $date1 += 24 *3600; 
            } 
        } 
        if ($date1 > $date2 && $date1 < $date3) { 
            return 'open'; 
        } else { 
            return 'close'; 
        }
    }
    public function getRestaurantAdminDevice($restaurant_id)
	{
		$this->db->select('entity_id,content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result_res = $this->db->get('restaurant')->first_row();
        if($result_res && !empty($result_res))
        {
        	$this->db->select('users.device_id, users.language_slug, users.notification');
        	$this->db->join('users','users.entity_id = restaurant.restaurant_owner_id','left');
        	$this->db->where('restaurant.entity_id',$result_res->entity_id);        	
        	$result = $this->db->get('restaurant')->first_row(); 
        	return $result;
        }
        return false;
	}

	public function get_delivery_pickup_order($order_id)
	{
		$this->db->select('om.entity_id as order_id, om.user_id, om.agent_id, om.restaurant_id, om.coupon_id, om.table_id, om.total_rate, om.subtotal, om.tax_rate, om.tax_type, om.coupon_discount, om.coupon_name, om.coupon_amount, om.coupon_type, om.extra_comment, od.item_detail, od.user_detail, res.name as restaurant_name, CONCAT(us.first_name, " ",us.last_name) as user_name, us.device_id, us.notification, us.language_slug, om.service_fee_type, om.service_fee, om.order_status, om.paid_status
			, tips.amount as tip_amount, om.delivery_charge, om.creditcard_fee_type, om.creditcard_fee, om.refunded_amount, om.refund_reason, om.payment_option, om.transaction_id, od.user_mobile_number, od.user_name as order_user_name, us.phone_code, us.mobile_number, us.email');
        $this->db->join('order_detail as od','od.order_id = om.entity_id');
        $this->db->join('restaurant as res','om.restaurant_id = res.entity_id');
        $this->db->join('tips as tips','tips.order_id = om.entity_id AND tips.amount > 0','left');
        $this->db->join('users as us','om.user_id = us.entity_id','left');
        $this->db->where('om.entity_id',$order_id);
        $detail = $this->db->get('order_master as om')->first_row();        
        $order_detailarr = array();
        if($detail && !empty($detail))
        {
        	$order_detailarr =  array(
	            'entity_id' => $detail->order_id,
	            'order_id' => $detail->order_id,
	            'user_id' => $detail->user_id,
	            'agent_id' => ($detail->agent_id)?$detail->agent_id:NULL,
	            'device_id' => $detail->device_id,
	            'notification' => $detail->notification,
	            'language_slug' => $detail->language_slug,
	            'restaurant_id' => $detail->restaurant_id,
	            'coupon_id' => $detail->coupon_id,
	            'total_rate' => $detail->total_rate,
	            'subtotal' => $detail->subtotal,
	            'tax_rate' => $detail->tax_rate,
	            'tax_type' => $detail->tax_type,
	            'service_fee_type' => $detail->service_fee_type,
	            'service_fee' => $detail->service_fee,
	            'creditcard_fee_type' => $detail->creditcard_fee_type,
	            'creditcard_fee' => $detail->creditcard_fee,
	            'coupon_discount' => $detail->coupon_discount,
	            'coupon_name' => $detail->coupon_name,
	            'coupon_amount' => $detail->coupon_amount,
	            'coupon_type' => $detail->coupon_type,
	            'extra_comment'=> $detail->extra_comment,
	            'restaurant_name' => $detail->restaurant_name,
	            'user_name' => $detail->user_name,
	            'order_status' => $detail->order_status,
	            'paid_status' => $detail->paid_status,
	            'tip_amount' => $detail->tip_amount,
	            'delivery_charge' => $detail->delivery_charge,
	            'refunded_amount' => $detail->refunded_amount,
	            'refund_reason' => $detail->refund_reason,
	            'payment_option' => $detail->payment_option,
	            'transaction_id' => $detail->transaction_id,
	            'item_detail' => unserialize($detail->item_detail),
	            'user_detail' => unserialize($detail->user_detail),
	            'user_mobile_number' => $detail->user_mobile_number,
	            'order_user_name' => $detail->order_user_name,
	            'phone_code' => $detail->phone_code,
	            'mobile_number' => $detail->mobile_number,
	            'email' => $detail->email
	        );
        }
        return $order_detailarr;
	}
	//get categories of restaurant menu items
	public function getItemCategory($entity_id){
		$restaurant_content_id = $this->common_model->getContentId($entity_id,'restaurant');
		$this->db->select('cat.name as cat_name,(CASE WHEN  catmap.sequence_no is NULL THEN 1000 ELSE catmap.sequence_no END) as sequence_no');
		$this->db->join('category as cat','cat.entity_id = menu.category_id');
		$this->db->join('menu_category_sequencemap as catmap',"catmap.category_content_id = cat.content_id AND catmap.restaurant_owner_id = '".$this->session->userdata('AdminUserID')."' AND catmap.restaurant_content_id = '".$restaurant_content_id."'",'left');
		$this->db->where('menu.restaurant_id',$entity_id);
		$this->db->where('menu.status',1);
		$this->db->where('menu.stock',1);
		$this->db->where('cat.status',1);
		$this->db->group_by('cat.entity_id');
		$this->db->order_by('sequence_no,cat.name', 'ASC');
		return $this->db->get('restaurant_menu_item as menu')->result(); 
	}
	//get categories
	public function getCategories(){
		$this->db->select('name');
		$this->db->where('category.status',1);
		$this->db->group_by('entity_id');
		//$this->db->order_by('menu.name', 'ASC');
		return $this->db->get('category')->result_array(); 
	}
	public function fill_user_namephn_in_orderdetails(){
		$this->db->select('entity_id,user_id');        
        $orderdata = $this->db->get('order_master')->result();

        if($orderdata && !empty($orderdata))
        {
            for($i=0;$i<count($orderdata);$i++)
            {
            	if($orderdata[$i]->user_id != 0 && $orderdata[$i]->user_id != NULL){
            		$this->db->select('entity_id, first_name, last_name, phone_code, mobile_number');
	                $this->db->where('entity_id',$orderdata[$i]->user_id);        
	                $userdata = $this->db->get('users')->first_row();
	                if($userdata && !empty($userdata))
	                {
	                    $updateData = array(
	                    	'user_name'=>$userdata->first_name.' '.$userdata->last_name,
	                    	'user_mobile_number'=>$userdata->phone_code.$userdata->mobile_number,
	                    ); 
	                    $this->updateData($updateData,'order_detail','order_id',$orderdata[$i]->entity_id);
	                }
            	}
            }
        }
	}
	public function check_delivery_method_map($restaurant_content_id){
        return $this->db->get_where('restaurant_delivery_method_map',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }
    public function UpdatedStatusForDeliveryOrders($tblname,$entity_id,$restaurant_id,$order_id,$dine_in='',$orders_user_id,$choose_delivery_method='')
	{
		$resp_arr = array('is_available' => 'no','error'=>'', 'delivery_method'=>'');
		$delivery_method_flag = 'no';
		if($orders_user_id>0){
			//send notification to user
			$this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug,users.notification,order_master.paid_status,order_master.payment_option,order_master.scheduled_date,order_master.slot_open_time');
	        $this->db->join('users','order_master.user_id = users.entity_id','left');
	        $this->db->where('order_master.entity_id',$order_id);
	        $this->db->where('users.status',1);
	        $device = $this->db->get('order_master')->first_row();
	    } else {
			$this->db->select('order_delivery,order_master.paid_status,order_master.payment_option,order_master.scheduled_date,order_master.slot_open_time');
	        $this->db->where('order_master.entity_id',$order_id);
	        $device = $this->db->get('order_master')->first_row();
		}
		if($device->order_delivery == 'Delivery' && $choose_delivery_method == 'internal_drivers'){
			$resp_arr = array('is_available' => 'yes', 'delivery_method'=>'internal_drivers');
			$delivery_method_flag = 'yes';

			$this->db->set('delivery_method','internal_drivers')->where('entity_id',$order_id)->update('order_master');

			$scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$device->scheduled_date $device->slot_open_time"));
			$order_scheduled_date = ($device->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
			$order_slot_open_time = ($device->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
			$current_time_chk = date('Y-m-d H:i:s');
			$current_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($current_time_chk)));

			if((empty($order_scheduled_date) && empty($order_slot_open_time)) || ($order_scheduled_date && $order_slot_open_time && date('Y-m-d',strtotime($order_scheduled_date)) == date('Y-m-d',strtotime($current_time_chk)))) {
				$combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_open_time"));
				$scheduleddatetime = new DateTime($combined_scheduled_date);
				$currentdatetime = new DateTime($current_time_chk);

				if((empty($device->scheduled_date) && empty($device->slot_open_time)) || $scheduleddatetime <= $currentdatetime) {
					$this->db->select('restaurant.entity_id,restaurant.content_id');
					$this->db->where('entity_id',$restaurant_id);
					$restaurant_content_id = $this->db->get('restaurant')->first_row();

					/* drivers assigned to multiple restaurant - start */
					$this->db->select('driver_id');
					$this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
					$driver = $this->db->get('restaurant_driver_map')->result_array();
					/* drivers assigned to multiple restaurant - end */

					$this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
					//driver tracking join last entry changes :: start
					$this->db->join('(select max(traking_id) as max_id, driver_id 
					from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
					$this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
					//driver tracking join last entry changes :: end
					$this->db->where('users.user_type','Driver');
					$this->db->where('driver_traking_map.latitude!=','');
					$this->db->where('driver_traking_map.longitude!=','');
					$this->db->where('users.status',1);
					$this->db->where('users.availability_status',1);
					$this->db->where('users.active',1);
					if(!empty($driver)){
						$this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
					}
					$this->db->group_by('driver_traking_map.driver_id');
					$this->db->order_by('driver_traking_map.created_date','desc');
					$detail = $this->db->get('users')->result();

					$flag = false;
					if(!empty($detail)){
						if($orders_user_id>0){
							$this->db->select('u_address.latitude, u_address.longitude');
							$this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
							$this->db->where('order_master.entity_id',$order_id);
							$user_details = $this->db->get('order_master')->first_row();
							$user_lat = $user_details->latitude;
							$user_long = $user_details->longitude;
						} else {
							$this->db->select('user_detail');
							$this->db->where('order_id',$order_id);
							$user_details = $this->db->get('order_detail')->first_row();
							$user_lat_long = unserialize($user_details->user_detail);
							$user_lat = $user_lat_long['latitude'];
							$user_long = $user_lat_long['longitude'];
						}
						/*Begin::Finding Distance between restaurant to user*/
						$this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
						$this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
						$this->db->where('order_master.entity_id',$order_id);
						$user_to_restaurant_distance = $this->db->get('order_master')->first_row();
						/*End::Finding Distance between restaurant to user*/
						foreach ($detail as $key => $value) {
							$longitude = $value->longitude;
							$latitude = $value->latitude;
							$this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
							$this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
							$this->db->where('restaurant.entity_id',$restaurant_id);
							$this->db->having('distance <',DRIVER_NEAR_KM);
							$result = $this->db->get('restaurant')->result();
							if(!empty($result)){
								if($value->device_id){
									//get langauge
									$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
									$this->lang->load('messages_lang', $languages->language_directory); 
									$flag = true;
									$array = array(
										'order_id'=>$order_id,
										'driver_id'=>$value->driver_id,
										'date'=>date('Y-m-d H:i:s'),
										'distance'=>$user_to_restaurant_distance->distance
									);
									$id = $this->addData('order_driver_map',$array);
									#prep the bundle
									$fields = array();
									$message = sprintf($this->lang->line('push_new_order'),$order_id);
									$fields['to'] = $value->device_id; // only one user to send push notification
									$fields['notification'] = array ('body'  => $message,'sound'=>'default');
									$fields['notification']['title'] = $this->lang->line('driver_app_name');
									$fields['data'] = array ('screenType'=>'order');

									$headers = array (
										'Authorization: key=' . FCM_KEY,
										'Content-Type: application/json'
									);
									#Send Reponse To FireBase Server
									$ch = curl_init();
									curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
									curl_setopt( $ch,CURLOPT_POST, true );
									curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
									curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
									curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
									curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
									$result = curl_exec($ch);
									curl_close($ch);
								}
							}
						}
					}
				}
			}
		} else if($device->order_delivery == 'Delivery' && $choose_delivery_method == 'thirdparty_delivery'){
			$res_content_id = $this->getResContentId($restaurant_id);
			$delivery_method_arr = $this->check_delivery_method_map($res_content_id);
			$delivery_method_ids = array_column($delivery_method_arr, 'delivery_method_id');
			$delivery_method_slug = $this->common_model->getDeliveryMethodName($delivery_method_ids);

			$is_scheduled = 0;
			if($device->scheduled_date && $device->slot_open_time) {
				$is_scheduled = 1;
			}

			$relay_check = (in_array('relay', $delivery_method_slug)) ? $this->common_model->checkDeliveryAvailableInRelay($order_id, $is_scheduled) : $resp_arr ;
			if($relay_check['is_available'] == 'no' && $device->payment_option != 'cod'){
				$door_dash_check = (in_array('doordash', $delivery_method_slug)) ? $this->common_model->checkDeliveryAvailableInDoorDash($order_id, $is_scheduled) : $resp_arr ;
				if($door_dash_check['is_available'] == 'yes'){
					$resp_arr = array('is_available' => 'yes', 'delivery_method'=>'doordash');
					$delivery_method_flag = 'yes';
				} else {
					$resp_arr = array('is_available' => 'no', 'delivery_method'=>'doordash', 'error'=>$door_dash_check['error']);
				}
			} else if($relay_check['is_available'] == 'no'){
				$resp_arr = array('is_available' => 'no', 'delivery_method'=>'relay', 'error'=>'');
			} else {
				$resp_arr = array('is_available' => 'yes', 'delivery_method'=>'relay');
				$delivery_method_flag = 'yes';
			}
		}
		if($delivery_method_flag == 'yes'){
			$this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
			$this->db->set('order_status','accepted')->where('entity_id',$order_id)->update('order_master');
			$this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');
			if($orders_user_id>0){
				//send notification to user	        
		        if($device->device_id && $device->notification == 1){  
		        	//get langauge
		        	$languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
		        	$this->lang->load('messages_lang', $languages->language_directory);
		            #prep the bundle
		            $fields = array();            
		            $message = sprintf($this->lang->line('push_order_accept'),$order_id);
		            $fields['to'] = $device->device_id; // only one user to send push notification
		            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
		            $fields['notification']['title'] = $this->lang->line('customer_app_name');
		            $fields['data'] = array ('screenType'=>'order');
		            if($dine_in =='yes' && $device->paid_status == 'unpaid')
		            {
		                $fields['data'] = array ('screenType'=>'dinein');
		            }

		            $headers = array (
		                'Authorization: key=' . FCM_KEY,
		                'Content-Type: application/json'
		            );
		            #Send Reponse To FireBase Server    
		            $ch = curl_init();
		            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		            curl_setopt( $ch,CURLOPT_POST, true );
		            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		            $result = curl_exec($ch);
		            curl_close($ch);  
		        } 
			}
		}
		return $resp_arr;
	}
	public function inserBatch($tblname,$data){
        $this->db->insert_batch($tblname,$data);
        return $this->db->insert_id();
    }
    // get the drivers to assign to the orders
	public function getRestaurantDriver($restaurant_content_id)
	{
		$this->db->select('users.entity_id,users.first_name,users.last_name');
		$this->db->where('user_type','Driver');
		$this->db->where('users.status',1);  
		$this->db->where('users.availability_status',1);
		if(intval($restaurant_content_id)>0)
        {
             $this->db->where('restaurant.content_id', $restaurant_content_id);
        }

		$this->db->join('restaurant_driver_map as restaurant_map','users.entity_id = restaurant_map.driver_id','left');
		$this->db->join('restaurant','restaurant_map.restaurant_content_id = restaurant.content_id','left');
		if($this->session->userdata('AdminUserType') == 'Restaurant Admin')
		{
			$this->db->where('restaurant.restaurant_owner_id', $this->session->userdata('AdminUserID'));
		}
		else if($this->session->userdata('AdminUserType') == 'Branch Admin')
		{
			$this->db->where('restaurant.branch_admin_id', $this->session->userdata('AdminUserID'));
		}

		$this->db->group_by('restaurant_map.driver_id');		
		$result = $this->db->get('users')->result();		

		/*//Coe for find the global driver :: Start
		//If require opne this code
		$driver_enti_id = array();
		if(!empty($result1) && $result1)
		{
			$driver_enti_id = array_column($result1, 'entity_id');
		}			
		$this->db->select('users.entity_id,users.first_name,users.last_name');
		$this->db->where('user_type','Driver');
		$this->db->where('users.status',1);
		if(!empty($driver_enti_id))
		{
			$this->db->where_not_in('users.entity_id',$driver_enti_id);
		} 
		$this->db->where('users.availability_status',1);
		$this->db->where('users.is_restaurantdriver','0');
		$this->db->group_by('users.entity_id');
		$result2 = $this->db->get('users')->result();	
		//Coe for find the global driver :: End
		$result = array_merge($result1, $result2);*/

		//Code for find the on way drvier :: Start
		$order_st = array('preparing','onGoing','ready');
		$this->db->select('driver_map.driver_id');
		$this->db->where('order_master.order_delivery','Delivery');
		$this->db->where('driver_map.driver_id!=','');
		$this->db->where_in('order_master.order_status',$order_st);
		$this->db->join('order_driver_map as driver_map','order_master.entity_id = driver_map.order_id','left');
		$this->db->group_by('driver_map.driver_id');
		$resultin = $this->db->get('order_master')->result();
		$ongoing_driver = array();
		if($resultin && !empty($resultin))
		{
			$ongoing_driver = array_column($resultin, 'driver_id');
		}
		//Code for find the on way drvier :: End

		$resultfinal = array();
		for($i=0;$i<count($result);$i++)
		{
			$result[$i]->ongoing = 'no';
			if(in_array($result[$i]->entity_id,$ongoing_driver))
			{
				$result[$i]->ongoing = 'yes';
			}
			else if($result[$i]->active=='1')
			{
				$result[$i]->ongoing = '';
			}
			$resultfinal[$i] = $result[$i];
		}

		$keys = array_column($resultfinal, 'ongoing');
		array_multisort($keys, SORT_ASC, $resultfinal);

		return $resultfinal;
	}
}
?>