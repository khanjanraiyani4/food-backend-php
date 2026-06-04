<?php
class Event_model extends CI_Model {
    function __construct()
    {
        parent::__construct();              
    }   
    //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = '', $displayStart = 0, $displayLength = 10,$event_id) 
    {
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('name') != ''){
            $this->db->like('name', trim($this->input->post('name')));
        }
        if($this->input->post('no_of_people') != ''){
            $this->db->like('no_of_people', trim($this->input->post('no_of_people')));
        }
        if($this->input->post('user_name') != ''){
            // $this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%".$this->input->post('user_name')."%'");
            $where_string="((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('user_name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('booking_date') != ''){
            // $this->db->like('booking_date', $this->input->post('booking_date'));
            $explode_date = explode(' - ',trim($this->input->post('booking_date')));
            $from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
            $this->db->where('Date(booking_date) >=', date('Y-m-d',strtotime($from_date)));
            $this->db->where('Date(booking_date) <=', date('Y-m-d',strtotime($to_date)));
        }
        if($this->input->post('end_date') != ''){
            $this->db->like('end_date', trim($this->input->post('end_date')));
        }
        if($this->input->post('amount') != ''){
            $total_price = trim($this->input->post('amount'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('event.amount', trim($total_search));
            }else{
                $this->db->like('event.amount', trim($this->input->post('amount')));
            }
        }
        if($this->input->post('event_status') != ''){
            $this->db->like('event_status', trim($this->input->post('event_status')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', trim($this->input->post('Status')));
        }
        $this->db->select('event.entity_id,event.event_status,event.coupon_type,event.tax_rate,event.tax_type,event.coupon_amount,event.no_of_people,event.booking_date,event.amount,event_detail.package_detail,res.name as rname,u.first_name as fname,u.last_name as lname,res.currency_id,event.additional_request');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id');
        $this->db->join('restaurant as res','event.restaurant_id = res.content_id');
        $this->db->join('users as u','event.user_id = u.entity_id');
        $this->db->where('res.status',1);
        $this->db->where('res.language_slug',$this->session->userdata('language_slug')); 
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('res.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if($event_id && $event_id>0){
            $this->db->where('event.entity_id',$event_id);
        }
        if($this->input->post('package') != ''){       
            $this->db->like('event_detail.package_detail', trim($this->input->post('package')));
        }
        $this->db->where('res.status',1);
        $result['total'] = $this->db->count_all_results('event');
        if($sortFieldName != ''){
            if($sortFieldName == 'booking_date'){
                $this->db->order_by("event_status = 'pending'", 'DESC');
            }
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }
        if($this->input->post('name') != ''){
            $this->db->like('name', trim($this->input->post('name')));
        }
        if($this->input->post('no_of_people') != ''){
            $this->db->like('no_of_people', trim($this->input->post('no_of_people')));
        }
        if($this->input->post('user_name') != ''){
            // $this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%".$this->input->post('user_name')."%'");
            $where_string="((CASE WHEN u.last_name is NULL THEN u.first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('user_name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('booking_date') != ''){
            // $this->db->like('booking_date', $this->input->post('booking_date'));
            $explode_date = explode(' - ',trim($this->input->post('booking_date')));
            $from_date = str_replace('-', '/', $explode_date[0]);
            $to_date = str_replace('-', '/', $explode_date[1]);
            $this->db->where('Date(booking_date) >=', date('Y-m-d',strtotime($from_date)));
            $this->db->where('Date(booking_date) <=', date('Y-m-d',strtotime($to_date)));
        }
        if($this->input->post('end_date') != ''){
            $this->db->like('end_date', trim($this->input->post('end_date')));
        }
        if($this->input->post('amount') != ''){
            $total_price = trim($this->input->post('amount'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('event.amount', trim($total_search));
            }else{
                $this->db->like('event.amount', trim($this->input->post('amount')));
            }
        }
        if($this->input->post('event_status') != ''){
            $this->db->like('event_status', trim($this->input->post('event_status')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('status', trim($this->input->post('Status')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart); 
        $this->db->select('event.entity_id,event.event_status,event.coupon_type,event.tax_rate,event.tax_type,event.coupon_amount,event.no_of_people,event.booking_date,event.amount,event_detail.package_detail,res.name as rname,u.first_name as fname,u.last_name as lname,res.content_id as rcontent_id,res.currency_id,event.package_id,event.restaurant_id,event.additional_request');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id');
        $this->db->join('restaurant as res','event.restaurant_id = res.content_id'); 
        $this->db->join('users as u','event.user_id = u.entity_id'); 
        $this->db->where('res.status',1);
        $this->db->where('res.language_slug',$this->session->userdata('language_slug'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('res.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if($event_id && $event_id>0){
            $this->db->where('event.entity_id',$event_id);
        }
        if($this->input->post('package') != ''){
            $this->db->like('event_detail.package_detail', trim($this->input->post('package')));
        }
        $result['data'] = $this->db->get('event')->result();   
        foreach ($result['data'] as $key => $value)
        {
            $this->db->select('price,name,detail,image,restaurant_id');
            $this->db->where('content_id',$value->package_id);
            $this->db->where('language_slug',$this->session->userdata('language_slug'));
            $result1 = $this->db->get('restaurant_package')->result_array(); 
            $package_detail = @unserialize($value->package_detail);
            if(!empty($result1)){
                $package_detail['package_price'] = $result1[0]['price'];
                $package_detail['package_name'] = $result1[0]['name'];
                $package_detail['package_detail'] = $result1[0]['detail'];
                $package_detail['package_image'] = $result1[0]['image'];
                $value->restaurant_id = $result1[0]['restaurant_id'];
                $value->package_detail = @serialize($package_detail);
             }
        }
        foreach ($result['data'] as $key => $value)
        {
            $rest_name=$value->rname;
            if($this->input->post('restaurant') == '')
            {
                $this->db->select('name');
                $this->db->where('content_id',$value->restaurant_id);
                $this->db->where('language_slug',$this->session->userdata('language_slug'));
                $res_result =  $this->db->get('restaurant')->first_row();         
                if($res_result)
                {
                    $rest_name=$res_result->name;
                }
            }
            $result['data'][$key]->rname = $rest_name; 
        }     
        return $result;
    }  
    // method for adding
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        //$this->db->select('event.invoice,event_detail.package_detail,event_detail.restaurant_detail,event_detail.user_detail,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->select('event.invoice');
        $this->db->select('event.*,event_detail.package_detail,event_detail.restaurant_detail,event_detail.user_detail,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        $this->db->join('restaurant','event.restaurant_id = restaurant.entity_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left'); 
        return $this->db->get_where('event',array('event.entity_id'=>$entity_id))->first_row();
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
    // delete
    public function ajaxDelete($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
    //get list
    public function getListData($tblname){
        if($tblname == 'users'){
            $this->db->select('first_name,last_name,entity_id');
            $this->db->where('status',1);
            $this->db->where('user_type !=','MasterAdmin');
            return $this->db->get($tblname)->result();
        }else{
            $this->db->select('name,entity_id,amount_type,amount,capacity,timings');
            $this->db->where('status',1);
            $result = $this->db->get($tblname)->result();
            foreach ($result as $key => $value) {
                $timing = $value->timings;
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day)){
                            $newTimingArr[strtolower($day)]['open'] = $values['open'];
                            $newTimingArr[strtolower($day)]['close'] = $values['close'];
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                        }
                    }
                }
                $value->timings = $newTimingArr[strtolower($day)];
            }
            return $result;
        }
    }
    public function getBookedDate(){
        $this->db->select('booking_date');
        $this->db->where('booking_date >=',date('Y-m-d H:i:s'));
        return $this->db->get('event')->result();
    }
    //get restaurant detail
    public function getRestuarantDetail($entity_id){
        $this->db->select('capacity');
        $this->db->where('entity_id',$entity_id);
        return $this->db->get('restaurant')->first_row();
    }
    //get list of restaurant
    public function getRestaurantList(){
        $this->db->select('entity_id,name,content_id');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where('language_slug',$this->session->userdata('language_slug'));
        $this->db->order_by('name', 'ASC');
        return $this->db->get('restaurant')->result();
    }
    //generate report data
    public function generate_report($restaurant_id,$from_date,$to_date){
        $this->db->select('event.*,event_detail.package_detail,restaurant.name,users.first_name,users.last_name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('event_detail','event.entity_id = event_detail.event_id','left');
        $this->db->join('restaurant','event.restaurant_id = restaurant.content_id','left');
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->join('users','event.user_id = users.entity_id','left');
        $this->db->where_in('restaurant_id',$restaurant_id);
        $this->db->where('restaurant.language_slug',$this->session->userdata('language_slug'));
        $this->db->order_by('restaurant_id','ASC');
        if($from_date != '' && $to_date != ''){
            //$this->db->like('event.created_date', date('Y-m-d',strtotime($booking_date))); 
            $this->db->where('Date(event.booking_date) >=', date('Y-m-d',strtotime($from_date)));
            $this->db->where('Date(event.booking_date) <=', date('Y-m-d',strtotime($to_date)));
        }
        /*if($order_date){
            $monthsplit = explode("-",$order_date);         
            $this->db->where('MONTH(event.created_date)',$monthsplit[0]);
            $this->db->where('YEAR(event.created_date)',$monthsplit[1]);
        }*/
        return $this->db->get('event')->result();
    }
    public function viewAdditionalRequest($entity_id){
        $this->db->select('additional_request');
        $this->db->where('entity_id',$entity_id);
        $return = $this->db->get('event')->first_row();
        return $return->additional_request;
    }
}
?>