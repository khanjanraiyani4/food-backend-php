<?php
class Users_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    // method for getting all users
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10,$user_type)
    {
        if($this->input->post('page_title') != ''){
            // $this->db->like('first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('driver_temperature') != ''){
            $this->db->like('driver_temperature', trim($this->input->post('driver_temperature')));
        }
        if($this->input->post('user_type') != ''){
            $this->db->like('user_type', trim($this->input->post('user_type')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        if($this->input->post('restaurant_name') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant_name')));
        }
        $this->db->where('user_type !=','MasterAdmin');
        $this->db->select('users.first_name,users.last_name,users.entity_id,users.user_type,users.is_masterdata,users.status,users.active,users.mobile_number,users.driver_temperature,users.created_date');
        if($user_type){
            $this->db->simple_query('SET SESSION group_concat_max_len=100000000');
            $this->db->select('GROUP_CONCAT(restaurant.name ORDER BY restaurant.name ASC SEPARATOR ",") as res_name, count(restaurant.name) as res_cnt');
            $this->db->join('restaurant_driver_map', 'users.entity_id = restaurant_driver_map.driver_id', 'left');
            $this->db->join('restaurant','restaurant_driver_map.restaurant_content_id = restaurant.content_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            if($this->input->post('driver_restaurant') != ''){
                $driver_res_search = $this->common_model->escapeString(trim($this->input->post('driver_restaurant')));
                $this->db->having("res_name LIKE '%$driver_res_search%' ");
            }
            $this->db->where('user_type','Driver');
            $this->db->group_by('users.entity_id');
        }else{
            $this->db->where('user_type','User');
            $this->db->group_by('users.entity_id');
        }
        $result['total'] = $this->db->count_all_results('users');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
            // $this->db->like('first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('driver_temperature') != ''){
            $this->db->like('driver_temperature', trim($this->input->post('driver_temperature')));
        }
        if($this->input->post('user_type') != ''){
            $this->db->like('user_type', trim($this->input->post('user_type')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        if($this->input->post('restaurant_name') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant_name')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart); 

        $this->db->where('user_type !=','MasterAdmin');
        $this->db->select('users.first_name,users.last_name,users.entity_id,users.user_type,users.is_masterdata,users.status,users.active,users.mobile_number,users.driver_temperature,users.created_date,users.phone_code');  
        if($user_type){
            $this->db->select('GROUP_CONCAT(restaurant.name ORDER BY restaurant.name ASC SEPARATOR ",") as res_name, count(restaurant.name) as res_cnt');
            $this->db->join('restaurant_driver_map', 'users.entity_id = restaurant_driver_map.driver_id', 'left');
            $this->db->join('restaurant','restaurant_driver_map.restaurant_content_id = restaurant.content_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            if($this->input->post('driver_restaurant') != ''){
                $driver_res_search = $this->common_model->escapeString(trim($this->input->post('driver_restaurant')));
                $this->db->having("res_name LIKE '%$driver_res_search%' ");
            }
            $this->db->group_by('users.entity_id');
            $this->db->where('user_type','Driver');
        }else{
            $this->db->where('user_type','User');
            $this->db->group_by('users.entity_id');
        }     
        $result['data'] = $this->db->get('users')->result();
        foreach ($result['data'] as $key => $value)
        {
            $this->db->where('o.user_id',$value->entity_id);
            $this->db->where('o.order_delivery !=','DineIn');
            $result['data'][$key]->total_order = $this->db->get('order_master as o')->num_rows();
            $result['data'][$key]->total_review = $this->db->select('order_user_id')->where('order_user_id',$value->entity_id)->get('review')->num_rows();            
        }         
        return $result;
    }
    // method for adding users
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    // method to get user details by id
    public function getEditDetail($tblname,$entity_id)
    {
        return $this->db->get_where($tblname,array('entity_id'=>$entity_id))->first_row();
    }
    // delete user
    public function deleteUser($tblname,$entity_id)
    {
        $this->db->delete($tblname,array('entity_id'=>$entity_id));  
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatus($entity_id,$status){
        if($status==0){
            $userData = array('status' => 1,'is_deleted'=>0);
        } else {
            $userData = array('status' => 0,'is_deleted'=>0);
        }        
        $this->db->where('entity_id',$entity_id);
        $this->db->update('users',$userData);
        return $this->db->affected_rows();
    }
    //get users
    public function getUsers(){
        $this->db->select('first_name,last_name,entity_id');
        $this->db->where('user_type','User');
        $this->db->where('status','1');
        $this->db->order_by('first_name', 'ASC');
        return $this->db->get('users')->result();
    }
    //address grid
    public function getAddressGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            // $this->db->like('u.first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        // if($this->input->post('page_title') != ''){
        //     $this->db->like('u.last_name', $this->input->post('page_title'));
        // }
        if($this->input->post('address') != ''){
            $this->db->like('address.address', trim($this->input->post('address')));
        }
        $this->db->select('address.entity_id,address.address,u.first_name,u.last_name');
        $this->db->join('users as u','address.user_entity_id = u.entity_id','left');
        $this->db->where('u.user_type !=','MasterAdmin');
        $result['total'] = $this->db->count_all_results('user_address as address');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
            // $this->db->like('u.first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(u.first_name,' ',u.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        // if($this->input->post('page_title') != ''){
        //     $this->db->like('u.last_name', $this->input->post('page_title'));
        // }
        if($this->input->post('address') != ''){
            $this->db->like('address.address', trim($this->input->post('address')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);

        $this->db->select('address.entity_id,address.address,u.first_name,u.last_name');
        $this->db->join('users as u','address.user_entity_id = u.entity_id','left'); 
        $this->db->where('u.user_type !=','MasterAdmin');       
        $result['data'] = $this->db->get('user_address as address')->result();        
        return $result;
    }   
    public function checkExist($mobile_number,$entity_id,$phone_code,$user_type='User'){
        $this->db->where('mobile_number',$mobile_number);
        $this->db->where('phone_code',$phone_code);
        $this->db->where('entity_id !=',$entity_id);
        if($user_type != 'User' && $user_type != 'Driver' && $user_type != 'Agent'){
            $roles = array('User','Driver','Agent');
            $this->db->where_not_in('user_type',$roles);
            //$this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin' OR user_type='MasterAdmin')");
        } else if($user_type == 'User' || $user_type == 'Agent') {
            $roles = array('User','Agent');
            $this->db->where_in('user_type',$roles);
        } else {
            $this->db->where('user_type',$user_type);
        }
        return $this->db->get('users')->num_rows();
    }
    public function checkExistPhone($phone_number,$entity_id){
        $this->db->where('phone_number',$phone_number);
        $this->db->where('entity_id !=',$entity_id);
        return $this->db->get('users')->num_rows();
    }
    public function checkEmailExist($email,$entity_id,$user_type='User'){
        $this->db->where('email',$email);
        $this->db->where('entity_id !=',$entity_id);
        if($user_type != 'User' && $user_type != 'Driver' && $user_type != 'Agent'){
            $roles = array('User','Driver','Agent');
            $this->db->where_not_in('user_type',$roles);
            //$this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin' OR user_type='MasterAdmin')");
        } else {
            $this->db->where('user_type',$user_type);
        }
        return $this->db->get('users')->num_rows();
    }
    //get commission
    public function getCommissionDetail($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10,$user_id){
        if($this->input->post('name') != ''){
            // $this->db->like("CONCAT(first_name,' ',last_name) like '%".$this->input->post('name')."%'");
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".trim($this->input->post('name'))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('commission_rate') != ''){
           $this->db->like('order_driver_map.driver_commission',trim($this->input->post('commission_rate')));
        }
        if($this->input->post('order_id') != ''){
           $this->db->where('order_driver_map.order_id',trim($this->input->post('order_id')));
        }
        /*if($this->input->post('restaurant') != ''){
            $name = $this->input->post('restaurant');
            $where = "(order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*')";
            $this->db->where($where);
        }*/
        if($this->input->post('commission') != ''){
            $total_price = trim($this->input->post('commission'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('order_driver_map.commission', trim($total_search));
            }else{
                $this->db->like('order_driver_map.commission', trim($this->input->post('commission')));
            }
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('order_driver_map.date', date('Y-m-d',strtotime($date)));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant')));
        }
        $this->db->select('order_master.order_status,users.first_name,users.last_name,order_driver_map.commission,order_driver_map.driver_commission,order_detail.restaurant_detail,order_driver_map.commission_status,order_driver_map.driver_map_id,order_driver_map.order_id,restaurant.name as rest_name');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        $this->db->join('order_detail','order_driver_map.order_id = order_detail.order_id','left');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
        //     $this->db->where('users.entity_id',$this->session->userdata('AdminUserID'));
        // }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->group_by('order_driver_map.order_id');
        $result['total'] = $this->db->count_all_results('order_driver_map');
        
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('name') != ''){
            // $this->db->like("CONCAT(first_name,' ',last_name) like '%".$this->input->post('name')."%'");
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".trim($this->input->post('name'))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('commission_rate') != ''){
           $this->db->like('order_driver_map.driver_commission',trim($this->input->post('commission_rate')));
        }
        if($this->input->post('order_id') != ''){
           $this->db->where('order_driver_map.order_id',trim($this->input->post('order_id')));
        }
        /*if($this->input->post('restaurant') != ''){
            $name = $this->input->post('restaurant');
            $where = "(order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*')";
            $this->db->where($where);
        }*/
        if($this->input->post('commission') != ''){
            $total_price = trim($this->input->post('commission'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('order_driver_map.commission', trim($total_search));
            }else{
                $this->db->like('order_driver_map.commission', trim($this->input->post('commission')));
            }
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('order_driver_map.date', date('Y-m-d',strtotime($date)));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);

        $this->db->select('order_master.order_status,users.first_name,users.last_name,order_driver_map.commission,order_driver_map.driver_commission,order_driver_map.date,order_detail.restaurant_detail,order_driver_map.commission_status,order_driver_map.driver_map_id,order_driver_map.order_id,restaurant.name as rest_name');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        $this->db->join('order_detail','order_driver_map.order_id = order_detail.order_id','left');
        $this->db->join('order_master','order_driver_map.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
        $this->db->where('order_driver_map.driver_id',$user_id);
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
        //     $this->db->where('users.entity_id',$this->session->userdata('AdminUserID'));
        // }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->group_by('order_driver_map.order_id');
        $result['data'] = $this->db->get('order_driver_map')->result();
        return $result;
    }

    //get commission
    public function getDriverReviewDetail($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10,$user_id){
        if($this->input->post('name') != ''){
            $where_string="((CASE WHEN driver.last_name is NULL THEN driver.first_name ELSE CONCAT(driver.first_name,' ',driver.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('customer_name') != ''){
            $where_string="((CASE WHEN customer.last_name is NULL THEN customer.first_name ELSE CONCAT(customer.first_name,' ',customer.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer_name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('review') != ''){
            $this->db->like('review', trim($this->input->post('review')));
        }
        if($this->input->post('rating') != ''){
            $this->db->like('rating', trim($this->input->post('rating')));
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('review.created_date', date('Y-m-d',strtotime($date)));
        }
        $this->db->select('review.review,review.rating,review.created_date,driver.first_name as driver_fname,driver.last_name as driver_lname,customer.first_name as customer_fname,customer.last_name as customer_lname');
        $this->db->join('users as driver','review.order_user_id = driver.entity_id','left');
        $this->db->join('users as customer','review.user_id = customer.entity_id','left');
        $this->db->where('review.order_user_id',$user_id);
        $result['total'] = $this->db->count_all_results('review');

        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        if($this->input->post('name') != ''){
            $where_string="((CASE WHEN driver.last_name is NULL THEN driver.first_name ELSE CONCAT(driver.first_name,' ',driver.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('customer_name') != ''){
            $where_string="((CASE WHEN customer.last_name is NULL THEN customer.first_name ELSE CONCAT(customer.first_name,' ',customer.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer_name')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('review') != ''){
            $this->db->like('review', trim($this->input->post('review')));
        }
        if($this->input->post('rating') != ''){
            $this->db->like('rating', trim($this->input->post('rating')));
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('review.created_date', date('Y-m-d',strtotime($date)));
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart);
        }

        /*new changes in query: 27jan2021 start*/
        $this->db->select('review.review,review.rating,review.created_date,driver.first_name as driver_fname,driver.last_name as driver_lname,customer.first_name as customer_fname,customer.last_name as customer_lname');
        $this->db->join('users as driver','review.order_user_id = driver.entity_id','left');
        $this->db->join('users as customer','review.user_id = customer.entity_id','left');
        /*new changes in query: 27jan2021 end*/
        $this->db->where('review.order_user_id',$user_id);
        $result['data'] = $this->db->get('review')->result();
        return $result;
    }
    public function payCommision($driver_map_id){
        $data = array('commission_status'=>"Paid");
        $this->db->where_in('driver_map_id',$driver_map_id);
        $this->db->update('order_driver_map',$data);
        return $this->db->affected_rows();
    }

    //generate report data
    public function generate_report($user_type){
        $this->db->select('users.first_name,users.status,users.last_name,users.mobile_number,users.user_type,users.driver_temperature');         
        if($user_type){
            $this->db->simple_query('SET SESSION group_concat_max_len=100000000');
            $this->db->select('GROUP_CONCAT(restaurant.name ORDER BY restaurant.name ASC SEPARATOR ",") as res_name, count(restaurant.name) as res_cnt');
            $this->db->join('restaurant_driver_map', 'users.entity_id = restaurant_driver_map.driver_id', 'left');
            $this->db->join('restaurant','restaurant_driver_map.restaurant_content_id = restaurant.content_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
        }
        $this->db->where('user_type',$user_type);
        $this->db->group_by('users.entity_id');
        return $this->db->get('users')->result();
    }
    //New code added as per requesrted :: Start
    public function getListRestaurantData($tblname,$language_slug){
        $this->db->select('name,entity_id,content_id');
        $this->db->where('status',1);
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where('language_slug',$language_slug);  
        return $this->db->get($tblname)->result();
    }

    public function getBrachAdminDetail($entity_id){
        $this->db->select('restaurant_content_id');
        $this->db->where('branch_admin_id',$entity_id);
        return $this->db->get('restaurant_branch_map')->first_row();
    }
    //New code added as per requesrted :: End

    // method for getting all admins
    public function getAdminGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        // get restaurants assigned to logged in user
        $assigned_restaurants = array();
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin') {
            $this->db->select('entity_id as restaurant_id, content_id as restaurant_content_id');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $assigned_restaurants = $this->db->get('restaurant')->result();
            $res_ids = array_column($assigned_restaurants, 'restaurant_id');
        }
        if($this->input->post('page_title') != ''){
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('user_type')){
            $this->db->where('role_id', trim($this->input->post('user_type')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        $roles = array('MasterAdmin', 'User', 'Driver','Agent');
        $this->db->where_not_in('user_type',$roles);

        $this->db->simple_query('SET SESSION group_concat_max_len=100000000');
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.mobile_number,users.status,users.is_masterdata,users.created_date,users.user_type,GROUP_CONCAT(restaurant.name ORDER BY restaurant.name ASC SEPARATOR ",") as restaurant_name, count(restaurant.name) as res_cnt');
        $this->db->join('restaurant',"users.entity_id= (CASE WHEN users.user_type ='Restaurant Admin' THEN restaurant.restaurant_owner_id ELSE restaurant.branch_admin_id END)",'left');
        if(!empty($assigned_restaurants)) {
            $this->db->where_in('restaurant.entity_id', $res_ids);
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('users.user_type !=','Restaurant Admin');
            }
        }
        if($this->input->post('restaurant_name') != ''){
            $admin_res_search = $this->common_model->escapeString(trim($this->input->post('restaurant_name')));
            $this->db->having("restaurant_name LIKE '%$admin_res_search%' ");
        }
        $this->db->group_by('users.entity_id');
        $result['total'] = $this->db->count_all_results('users');
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }        
        if($this->input->post('page_title') != ''){
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
           $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('user_type')){
            $this->db->where('role_id', trim($this->input->post('user_type')));
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart); 
        }
        $roles = array('MasterAdmin', 'User', 'Driver','Agent');
        $this->db->where_not_in('user_type',$roles);
        
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.mobile_number,users.status,users.is_masterdata,users.created_date,users.user_type,users.phone_code,GROUP_CONCAT(restaurant.name ORDER BY restaurant.name ASC SEPARATOR ",") as restaurant_name, count(restaurant.name) as res_cnt');
        $this->db->join('restaurant',"users.entity_id= (CASE WHEN users.user_type ='Restaurant Admin' THEN restaurant.restaurant_owner_id ELSE restaurant.branch_admin_id END)",'left');
        if(!empty($assigned_restaurants)) {
            $this->db->where_in('restaurant.entity_id', $res_ids);
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('users.user_type !=','Restaurant Admin');
            }
        }
        if($this->input->post('restaurant_name') != ''){
            $admin_res_search = $this->common_model->escapeString(trim($this->input->post('restaurant_name')));
            $this->db->having("restaurant_name LIKE '%$admin_res_search%' ");
        }
        $this->db->group_by('users.entity_id');
        $result['data'] = $this->db->get('users')->result();
        return $result;
    }

    //insert batch 
    public function insertBatch($tblname,$data,$id){
        if($id){
            $this->db->where('driver_id',$id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname,$data);           
        return $this->db->insert_id();
    }
    public function getRestDrivers($driver_id){
        $this->db->select('restaurant_content_id');
        $this->db->where('driver_id',$driver_id);
        return $this->db->get('restaurant_driver_map')->result();
    }
    public function deleteUsersOrder($user_id) {
        //deleting user's orders
        $this->db->where('user_id',$user_id);
        $this->db->delete('order_master');
        //deleting user's wallet history
        $this->db->where('user_id',$user_id);
        $this->db->delete('wallet_history');
    }
    public function review_report($driver_id){
        /*new changes in query: 27jan2021 start*/
        $this->db->select('review.order_user_id as driver_id,review.review,review.rating,driver.first_name as driver_fname,driver.last_name as driver_lname,driver.phone_code,driver.mobile_number,review.user_id as customer_id,customer.first_name as customer_fname,customer.last_name as customer_lname');
        $this->db->join('users as driver','review.order_user_id = driver.entity_id','left');
        $this->db->join('users as customer','review.user_id = customer.entity_id','left');
        /*new changes in query: 27jan2021 end*/
        $this->db->where('review.order_user_id',$driver_id);
        return $result = $this->db->get('review')->result();
    }
    //driver tips changes :: start
    public function gettipsDetail($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10,$user_id){
        if($this->input->post('name') != ''){
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('name')))."%')";
            $this->db->where($where_string);
        }
        /*if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString($this->input->post('restaurant'));
            $where = "(order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*')";
            $this->db->where($where);
        }*/
        if($this->input->post('tips') != ''){
            $total_price = trim($this->input->post('tips'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('tips.amount', trim($total_search));
            }else{
                $this->db->like('tips.amount', trim($this->input->post('tips')));
            }
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('tips.date', date('Y-m-d',strtotime($date)));
        }
        if($this->input->post('order_id') != ''){
           $this->db->where('tips.order_id',trim($this->input->post('order_id')));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant')));
        }
        $this->db->select('order_master.order_status,users.first_name,users.last_name,tips.amount,tips.date,order_detail.restaurant_detail,tips.entity_id as tips_id,tips.order_id,restaurant.name as rest_name');
        $this->db->join('users','tips.driver_id = users.entity_id','left');
        $this->db->join('order_detail','tips.order_id = order_detail.order_id','left');
        $this->db->join('order_master','tips.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
        $this->db->where('tips.driver_id',$user_id);
        $this->db->where('tips.amount >',0);
        $this->db->where('(tips.refund_status != "refunded" OR tips.refund_status is NULL)');
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
        //     $this->db->where('users.created_by',$this->session->userdata('AdminUserID'));
        // }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->group_by('tips.order_id');
        $result['total'] = $this->db->count_all_results('tips');
        
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('name') != ''){
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('name')))."%')";
            $this->db->where($where_string);
        }
        /*if($this->input->post('restaurant') != ''){
            $name = $this->common_model->escapeString($this->input->post('restaurant'));
            $where = "(order_detail.restaurant_detail REGEXP '.*".'"'."name".'"'.";s:[0-9]+:".'"'."$name".'"'.".*')";
            $this->db->where($where);
        }*/
        if($this->input->post('tips') != ''){
            $total_price = trim($this->input->post('tips'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('tips.amount', trim($total_search));
            }else{
                $this->db->like('tips.amount', trim($this->input->post('tips')));
            }
        }
        if($this->input->post('date') != ''){
            $explode_date = explode(' - ',trim($this->input->post('date')));
            $date = str_replace('-', '/', $explode_date[0]);
            $this->db->like('tips.date', date('Y-m-d',strtotime($date)));
        }
        if($this->input->post('order_id') != ''){
           $this->db->where('tips.order_id',trim($this->input->post('order_id')));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);

        $this->db->select('order_master.order_status,users.first_name,users.last_name,tips.amount,tips.date,order_detail.restaurant_detail,tips.entity_id as tips_id,tips.order_id,restaurant.name as rest_name');
        $this->db->join('users','tips.driver_id = users.entity_id','left');
        $this->db->join('order_detail','tips.order_id = order_detail.order_id','left');
        $this->db->join('order_master','tips.order_id = order_master.entity_id','left');
        $this->db->join('restaurant','restaurant.entity_id = order_master.restaurant_id','left');
        $this->db->where('tips.driver_id',$user_id);
        $this->db->where('tips.amount >',0);
        $this->db->where('(tips.refund_status != "refunded" OR tips.refund_status is NULL)');
        // if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
        //     $this->db->where('users.created_by',$this->session->userdata('AdminUserID'));
        // }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->group_by('tips.order_id');
        $result['data'] = $this->db->get('tips')->result();
        return $result;
    }
    //driver tips changes :: end
    //verify user account :: start
    public function UpdatedAccount($entity_id){
        $userData = array('active' => 1);       
        $this->db->where('entity_id',$entity_id);
        $this->db->update('users',$userData);
        return $this->db->affected_rows();
    }
    //verify user account :: end
    public function getListParentData($entity_id){
        $this->db->select('first_name,last_name,entity_id');
        $this->db->where('user_type','Restaurant Admin');
        $this->db->where('status',1);
        $this->db->or_where('entity_id',$entity_id);
        $this->db->where('user_type !=','MasterAdmin');
        $this->db->order_by('first_name', 'ASC'); 
        return $this->db->get('users')->result();
    }
    // method for getting all call agents
    public function getAgentGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            // $this->db->like('first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        $this->db->where('user_type ','Agent');
        $this->db->select('users.first_name,users.last_name,users.entity_id,users.user_type,users.is_masterdata,users.status,users.active,users.mobile_number,users.driver_temperature,users.created_date');
        $this->db->group_by('users.entity_id');
        $result['total'] = $this->db->count_all_results('users');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
            // $this->db->like('first_name', $this->input->post('page_title'));
            $where_string="((CASE WHEN last_name is NULL THEN first_name ELSE CONCAT(first_name,' ',last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('page_title')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('phone') != ''){
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="((".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('phone')))."%')";
            $this->db->where($where_string);
        }
        if($this->input->post('Status') != ''){
            $this->db->like('users.status', trim($this->input->post('Status')));
        }
        if($this->input->post('restaurant_name') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('restaurant_name')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart); 

        $this->db->where('user_type !=','MasterAdmin');
        $this->db->select('users.first_name,users.last_name,users.entity_id,users.user_type,users.is_masterdata,users.status,users.active,users.mobile_number,users.driver_temperature,users.created_date,users.phone_code');  
        $this->db->where('user_type','Agent');
        $this->db->group_by('users.entity_id');   
        $result['data'] = $this->db->get('users')->result();
        foreach ($result['data'] as $key => $value)
        {
            $this->db->where('o.user_id',$value->entity_id);
            $this->db->where('o.order_delivery !=','DineIn');
            $result['data'][$key]->total_order = $this->db->get('order_master as o')->num_rows();
            $result['data'][$key]->total_review = $this->db->select('order_user_id')->where('order_user_id',$value->entity_id)->get('review')->num_rows();            
        }         
        return $result;
    }
    public function getRolesList($role_id,$role_name,$action = 'for_add_form'){
        $this->db->select('role_id,role_name');
        $this->db->where('role_name !=','Master Admin');
        if($action == 'for_add_form') {
            $this->db->where('status',1);
            if($role_name == 'Branch Admin') {
                $this->db->where('role_name !=','Restaurant Admin');
            }
            /*$this->db->where('role_id !=',$role_id);
            if($role_name == 'Branch Admin') {
                $this->db->where('role_name !=','Restaurant Admin');
            } else if($role_name != 'Master Admin' && $role_name != 'Restaurant Admin' && $role_name != 'Branch Admin') {
                $this->db->where('role_name !=','Restaurant Admin');
                $this->db->where('role_name !=','Branch Admin');
            }*/
        }
        $this->db->order_by('role_name', 'ASC');
        return $this->db->get('role_master')->result();
    }
    public function deleteRelation($driver_id)
    {
        $this->db->where('driver_id',$driver_id);
        $this->db->delete('restaurant_driver_map'); 
    }
} ?>