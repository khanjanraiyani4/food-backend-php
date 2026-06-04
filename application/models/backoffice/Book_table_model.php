<?php
class Book_table_model extends CI_Model {
    function __construct()
    {
        parent::__construct();              
    }
    // method for getting all users
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('user_name') != ''){
            $this->db->like('user_name', trim($this->input->post('user_name')));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('no_of_people') != ''){
            $this->db->like('no_of_people', trim($this->input->post('no_of_people')));
        }
        if($this->input->post('booking_date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('booking_date')));
            $from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
            $this->db->where('Date(booking_date) >=', date('Y-m-d',strtotime($from_date)));
            $this->db->where('Date(booking_date) <=', date('Y-m-d',strtotime($to_date)));
        }
        if($this->input->post('payment_status') != ''){
            $this->db->where('payment_status', trim($this->input->post('payment_status')));
        }
        if($this->input->post('booking_status') != ''){
            $this->db->where('booking_status', trim($this->input->post('booking_status')));
        }
        $this->db->select('table_booking.entity_id');
        $this->db->join('restaurant as res','table_booking.restaurant_content_id = res.content_id','left');
        $this->db->where('res.language_slug', $this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $result['total'] = $this->db->count_all_results('table_booking');
        
        if($sortFieldName != ''){
            if($sortFieldName == 'booking_date'){
                $this->db->order_by("booking_status = 'awaiting'", 'DESC');
            }
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        if($this->input->post('user_name') != ''){
            $this->db->like('user_name', trim($this->input->post('user_name')));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('no_of_people') != ''){
            $this->db->like('no_of_people', trim($this->input->post('no_of_people')));
        }
        if($this->input->post('booking_date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('booking_date')));
            $from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
            $this->db->where('Date(booking_date) >=', date('Y-m-d',strtotime($from_date)));
            $this->db->where('Date(booking_date) <=', date('Y-m-d',strtotime($to_date)));
        }
        if($this->input->post('payment_status') != ''){
            $this->db->where('payment_status', trim($this->input->post('payment_status')));
        }
        if($this->input->post('booking_status') != ''){
            $this->db->where('booking_status', trim($this->input->post('booking_status')));
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart); 
        }

        $this->db->select('table_booking.entity_id, table_booking.user_name, table_booking.no_of_people, table_booking.booking_date, table_booking.start_time, table_booking.end_time, table_booking.amount, table_booking.booking_status, table_booking.payment_status, table_booking.additional_request, res.name as rname, res.currency_id, table_booking.restaurant_content_id');
        $this->db->join('restaurant as res','table_booking.restaurant_content_id = res.content_id');
        $this->db->where('res.language_slug', $this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }        
        $result['data'] = $this->db->get('table_booking')->result();        
        return $result;
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // delete
    public function ajaxDelete($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
}
?>