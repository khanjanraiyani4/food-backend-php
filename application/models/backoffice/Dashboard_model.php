<?php
class Dashboard_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    //get name count
    public function getRestaurantCount()
    {
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('content_id');
        return $this->db->get('restaurant')->num_rows();
    }
    //get restaurant
    public function restaurant(){
        $this->db->select('entity_id, name,phone_number,email,language_slug,phone_code');
        $this->db->order_by('entity_id','desc');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->limit(5, 0);
        $this->db->group_by('content_id');
        return $this->db->get('restaurant')->result(); 
    }
    //get total user account
    public function gettotalAccount()
    {
        //get user list
        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['users'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'User'))->result();
        //get drivers list
        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['drivers'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Driver'))->result();
        //get count
        $this->db->where('user_type','User');
        $data['user_count'] =  $this->db->get('users')->num_rows();
        
        return $data;
    }
    public function gettotalAccount_new()
    {
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin'){
            //get user list
            $this->db->select('users.entity_id, users.first_name, users.last_name, users.device_id');
            $this->db->join('order_master', 'users.entity_id = order_master.user_id');
            $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            $this->db->where('users.status','1');
            $this->db->where('users.user_type','User');
            $this->db->group_by('users.entity_id');
            $result['users'] = $this->db->get('users')->result();

            //get drivers list
            $this->db->select('users.entity_id, users.first_name, users.last_name, users.device_id');
            $this->db->join('restaurant_driver_map', 'users.entity_id = restaurant_driver_map.driver_id');
            $this->db->join('restaurant','restaurant_driver_map.restaurant_content_id = restaurant.content_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"');
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            $this->db->where('users.status','1');
            $this->db->where('users.user_type','Driver');
            $this->db->group_by('users.entity_id');
            $result['drivers'] = $this->db->get('users')->result();

            //get count
            $this->db->select('users.entity_id, users.first_name, users.last_name, users.device_id');
            $this->db->join('order_master', 'users.entity_id = order_master.user_id');
            $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id AND restaurant.language_slug="'.$this->session->userdata('language_slug').'"','left');
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));            
            $this->db->where('users.user_type','User');
            $this->db->group_by('users.entity_id');
            $data['user_count'] =  $this->db->get('users')->num_rows();
        }
        else
        {
            //get user list
            $this->db->select('entity_id, first_name, last_name, device_id');
            $data['users'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'User'))->result();
            //get drivers list
            $this->db->select('entity_id, first_name, last_name, device_id');
            $data['drivers'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Driver'))->result();
            //get count
            $this->db->where('user_type','User');
            $data['user_count'] =  $this->db->get('users')->num_rows();
        }
        
        return $data;
    }
    //get order count
    public function getOrderCount()
    {
        /*$this->db->select('o.total_rate as rate,o.order_date,o.order_status as ostatus,o.status,o.entity_id as entity_id,u.first_name as fname,u.last_name as lname');
        $this->db->join('users as u','o.user_id = u.entity_id','left');
        $this->db->join('restaurant','o.restaurant_id = restaurant.entity_id');*/
        $this->db->select('o.entity_id as entity_id');
        $this->db->join('users as u','o.user_id = u.entity_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->join('restaurant','o.restaurant_id = restaurant.entity_id');
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->join('restaurant','o.restaurant_id = restaurant.entity_id');
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        return $this->db->get('order_master as o')->num_rows();
    }
    //get last orders
    public function getLastOrders(){
        $this->db->select('o.total_rate as rate,o.order_date,o.order_status as ostatus,o.status,o.entity_id as entity_id,o.order_delivery,order_detail.user_name,u.first_name as fname,u.last_name as lname,restaurant.currency_id,restaurant.restaurant_owner_id,restaurant.branch_admin_id');
        $this->db->join('users as u','o.user_id = u.entity_id','left');
        $this->db->join('restaurant','o.restaurant_id = restaurant.entity_id');
        $this->db->join('order_detail','order_detail.order_id = o.entity_id','left');
        $this->db->where('o.order_status','placed');
        //$this->db->where('o.order_delivery !=','DineIn');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->order_by('o.entity_id','desc');
        $this->db->limit(5);
        $result = $this->db->get('order_master as o')->result(); 
        if($result && !empty($result)) 
        {
            for($oi=0;$oi<count($result);$oi++)
            {
                $result[$oi]->order_date= $this->common_model->getZonebaseDate($result[$oi]->order_date);
            }
        }
        return $result;
    }
    //get notification
    public function ajaxNotification()
    { 
        //get last orders
        $this->db->select('order_master.entity_id,order_master.order_delivery');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
        }
        //$this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
        $this->db->limit(1);
        $this->db->order_by('order_master.entity_id','desc');
        $count = $this->db->get('order_master')->first_row();
        //get notification count        
        $this->db->select('last_order_id,order_count,dinein_count,view_status,dinein_view_status,date');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $last_order = $this->db->get('order_notification')->first_row();

        $date = date('Y-m-d');
        if(!empty($count) && (!empty($last_order) && $last_order->last_order_id == 0))
        {
            $data = array('last_order_id'=>$count->entity_id,'date'=>$date);
            if($count->order_delivery == 'DineIn'){
                //$data['dinein_count'] = count($count);
                 $data['dinein_count'] = 0;
            }else{
                $data['order_count'] = count($count);
            }

            $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
            $this->db->update('order_notification',$data);
        }
        else if(!empty($count) && empty($last_order))
        {
            $this->db->select('order_master.entity_id as order_count');
            $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
            $arrayData = $this->db->get('order_master')->num_rows();

            $data = array(
                'last_order_id'=>$count->entity_id,
                'view_status'=>0,
                'dinein_view_status'=>0,
                'date'=>$date,
                'admin_id'=>$this->session->userdata('AdminUserID')
            );
            if($count->order_delivery == 'DineIn'){
                //$data['dinein_count'] = $arrayData;
                $data['dinein_count'] = 0;
            }else{
                $data['order_count'] = $arrayData;
                
            }

            $this->db->insert('order_notification',$data);
            $this->db->insert_id();
        }
        else
        {
            //New code add for check the order count andupdate the total count in notification table :: Start
            if(!empty($count) && (!empty($last_order)))
            {
                //Code for normal order :: Start
                $this->db->select('order_master.entity_id as order_count');
                $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
                if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                    /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                    $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
                }
                if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                    $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
                }
                $this->db->where('order_master.order_delivery!=','DineIn');
                $this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
                $arrayData = $this->db->get('order_master')->num_rows();
                //Code for normal order :: End

                //Code for dine in order :: Start
                $this->db->select('order_master.entity_id as order_count');
                $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
                if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                    //$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
                    $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
                }
                if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                    $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
                }
                $this->db->where('order_master.order_delivery','DineIn');
                $this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
                $arrayDineInData = $this->db->get('order_master')->num_rows();
                //Code for dine in order :: Start

                if(($arrayData<$last_order->order_count) || ($arrayDineInData<$last_order->dinein_count))
                {
                    $edit_data = array(
                        'last_order_id'=>$count->entity_id,
                        'view_status'=>0,
                        'dinein_view_status'=>0,
                        'date'=>$date,                        
                        'admin_id'=>$this->session->userdata('AdminUserID')
                    );
                    if($arrayData<$last_order->order_count)
                    {
                        $edit_data['order_count']= $arrayData;
                    }
                    if($arrayDineInData<$last_order->dinein_count)
                    {
                        //$edit_data['dinein_count']= $arrayDineInData;
                        $edit_data['dinein_count']= 0;
                    }
                    $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
                    $this->db->update('order_notification',$edit_data);                    
                }
            }
            //New code add for check the order count andupdate the total count in notification table :: End            
        }        

        if(!empty($count) && !empty($last_order))
        {   $order_count = 0;
            $dinein_count = 0;
            $this->db->select('order_master.entity_id as order_count');
            $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('order_master.entity_id >', $last_order->last_order_id);
            $this->db->where('order_master.entity_id <=', $count->entity_id);
            $this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
            $this->db->where('order_master.order_delivery !=', 'DineIn');
            $arrayData_order = $this->db->get('order_master')->num_rows();
            if(!empty($arrayData_order)){
                $order_count = $arrayData_order;
            }
            
            if($arrayData_order > 0 && $count->entity_id != $last_order->last_order_id && $last_order->last_order_id != 0){
                
                if($last_order->view_status == 0){
                    $order_count = $order_count + $last_order->order_count;
                    $date = ($last_order->date == date('Y-m-d'))?$last_order->date:date('Y-m-d');
                    $data = array('order_count'=>$order_count,'view_status'=>0,'date'=>$date,'last_order_id'=>$count->entity_id);
                }
                if($last_order->view_status == 1){
                    $date = ($last_order->date == date('Y-m-d'))?$last_order->date:date('Y-m-d');
                    $data = array('order_count'=>$order_count,'view_status'=>0,'date'=>$date,'last_order_id'=>$count->entity_id);
                }
                $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
                $this->db->update('order_notification',$data);
            }
            $this->db->select('order_master.entity_id as order_count');
            $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                //$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('order_master.entity_id >', $last_order->last_order_id);
            $this->db->where('order_master.entity_id <=', $count->entity_id);
            $this->db->where('DATE(order_master.created_date)',date('Y-m-d'));
            $this->db->where('order_master.order_delivery', 'DineIn');
            $arrayData_dinein = $this->db->get('order_master')->num_rows();
            if(!empty($arrayData_dinein)){
                //$dinein_count = $arrayData_dinein;
                $dinein_count = 0;
            }
            if($arrayData_dinein > 0 && $count->entity_id != $last_order->last_order_id && $last_order->last_order_id != 0){
                
                if($last_order->dinein_view_status == 0){
                    $dinein_count = $dinein_count + $last_order->dinein_count;
                    $date = ($last_order->date == date('Y-m-d')) ? $last_order->date : date('Y-m-d');
                    $data = array('dinein_count'=>$dinein_count,'dinein_view_status'=>0,'date'=>$date,'last_order_id'=>$count->entity_id);
                }
                if($last_order->dinein_view_status == 1){
                    $date = ($last_order->date == date('Y-m-d'))?$last_order->date:date('Y-m-d');
                    $data = array('dinein_count'=>$dinein_count,'dinein_view_status'=>0,'date'=>$date,'last_order_id'=>$count->entity_id);
                }
                $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
                $this->db->update('order_notification',$data);
            }
        }
        $this->db->select('SUM(order_count) as order_count,order_count as delivery_pickup_count,dinein_count');        
        //$this->db->where('date',date('Y-m-d'));
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $data = $this->db->get('order_notification')->first_row();
        $data2 = $this->ajaxEventNotification();
        $table_data = $this->ajaxTableBookingNotification();
        $data->tablebooking_count = (isset($table_data->tablebooking_count) && $table_data->tablebooking_count!=null && $table_data->tablebooking_count!='')?$table_data->tablebooking_count:0;
        $data->event_count = (isset($data2->event_count) && $data2->event_count!=null && $data2->event_count!='')?$data2->event_count:0;
        $data->order_count = $data->order_count + $data->event_count + $data->tablebooking_count;        
        $this->db->select('order_master.entity_id');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
        }
        $where_string="(order_master.order_status IN('placed') OR (order_master.order_status='accepted' AND order_master.status='0'))";
        $this->db->where($where_string);
        $this->db->order_by('order_master.entity_id','desc');
        $placed_order_count = $this->db->get('order_master')->num_rows();
        $data->placed_order_count = $placed_order_count;
        return $data;
    }
    //get notification
    public function ajaxEventNotification(){
        //get last orders
        $this->db->select('event.entity_id');
        $this->db->join('restaurant','event.restaurant_id = restaurant.content_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
        }
        $this->db->where('DATE(event.created_date)',date('Y-m-d'));
        $this->db->limit(1);
        $this->db->order_by('event.entity_id','desc');
        $count = $this->db->get('event')->first_row();
        //get notification count
        $this->db->select('last_event_id,event_count,view_status,date');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $last_event = $this->db->get('event_notification')->first_row();

        $date = date('Y-m-d');
        if(!empty($count) && (!empty($last_event) && $last_event->last_event_id == 0)){
            $data = array('last_event_id'=>$count->entity_id,'event_count'=>count($count),'date'=>$date);
            $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
            $this->db->update('event_notification',$data);
        }else if(!empty($count) && empty($last_event)){
            $this->db->select('event.entity_id as event_count');
            $this->db->join('restaurant','event.restaurant_id = restaurant.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('DATE(event.created_date)',date('Y-m-d'));
            $this->db->group_by('restaurant.content_id');
            $arrayData = $this->db->get('event')->num_rows();
            
            $data = array(
                'last_event_id'=>$count->entity_id,
                'event_count'=>$arrayData,
                'date'=>$date,
                'view_status'=>0,
                'admin_id'=>$this->session->userdata('AdminUserID')
            );
            $this->db->insert('event_notification',$data);
            $this->db->insert_id();
        }
        if(!empty($count) && !empty($last_event)){
            $event_count = 0;
            $this->db->select('event.entity_id as event_count');
            $this->db->join('restaurant','event.restaurant_id = restaurant.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('event.entity_id >', $last_event->last_event_id);
            $this->db->where('event.entity_id <=', $count->entity_id);
            $this->db->where('DATE(event.created_date)',date('Y-m-d'));
            $this->db->group_by('restaurant.content_id');
            $arrayData = $this->db->get('event')->num_rows();
            if(!empty($arrayData)){
                $event_count = $arrayData;
            }
            if($count->entity_id != $last_event->last_event_id && $last_event->last_event_id != 0){
                if($last_event->view_status == 0){
                    $event_count = $event_count + $last_event->event_count;
                    $date = ($last_event->date == date('Y-m-d'))?$last_event->date:date('Y-m-d');
                    $data = array('event_count'=>$event_count,'view_status'=>0,'date'=>$date,'last_event_id'=>$count->entity_id);
                }else if($last_event->view_status == 1){
                    $date = ($last_event->date == date('Y-m-d'))?$last_event->date:date('Y-m-d');
                    $data = array('event_count'=>$event_count,'view_status'=>0,'date'=>$date,'last_event_id'=>$count->entity_id);
                }
                $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
                $this->db->update('event_notification',$data);
            }
        }
        $this->db->select('event_count');
        //$this->db->where('date',date('Y-m-d'));
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('event_notification')->first_row();
    }
    //change view status
    public function changeViewStatus()
    {
        $data = array('order_count'=>0,'view_status'=>1);
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $this->db->update('order_notification',$data);
    }
    public function change_delivery_pickup_order_view_status()
    {
        //Code for update the notificaton count base on admin :: Start
        $resultadminarrmain = array();
        if($this->session->userdata('AdminUserType')=='MasterAdmin')
        {
            $this->db->select('entity_id');
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));                
            $resultadmin = $this->db->get('users')->result();
            $resultadminarr = array();
            if($resultadmin && !empty($resultadmin))
            {
                $resultadminarr = array_column($resultadmin, 'entity_id');
            }
            $resultadminarrses = array();
            if(!empty($resultadminarr))
            {
                $this->db->select('entity_id');
                $this->db->where_in('parent_user_id',$resultadminarr);                
                $resultadminsec = $this->db->get('users')->result();
                if($resultadmin && !empty($resultadmin))
                {
                    $resultadminarrses = array_column($resultadminsec, 'entity_id');
                }
            }
            $resultadminarrmain = array_merge($resultadminarr,$resultadminarrses);                
        }
        else if($this->session->userdata('AdminUserType') == 'Restaurant Admin')
        {
            $this->db->select('entity_id');
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));                
            $resultadmin = $this->db->get('users')->result();            
            if($resultadmin && !empty($resultadmin))
            {
                $resultadminarrmain = array_column($resultadmin, 'entity_id');
            }
        }

        if(!empty($resultadminarrmain))
        {
            $data = array('order_count'=>0,'view_status'=>1);
            $this->db->where_in('admin_id',$resultadminarrmain);
            $this->db->update('order_notification',$data);
        }
        //Code for update the notificaton count base on admin :: End
        $data = array('order_count'=>0,'view_status'=>1);
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $this->db->update('order_notification',$data);
    }
    public function change_dinein_order_view_status()
    {
        //Code for update the notificaton count base on admin :: Start
        $resultadminarrmain = array();
        if($this->session->userdata('AdminUserType')=='MasterAdmin')
        {
            $this->db->select('entity_id');
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));                
            $resultadmin = $this->db->get('users')->result();
            $resultadminarr = array();
            if($resultadmin && !empty($resultadmin))
            {
                $resultadminarr = array_column($resultadmin, 'entity_id');
            }
            $resultadminarrses = array();
            if(!empty($resultadminarr))
            {
                $this->db->select('entity_id');
                $this->db->where_in('parent_user_id',$resultadminarr);                
                $resultadminsec = $this->db->get('users')->result();
                if($resultadmin && !empty($resultadmin))
                {
                    $resultadminarrses = array_column($resultadminsec, 'entity_id');
                }
            }
            $resultadminarrmain = array_merge($resultadminarr,$resultadminarrses);                
        }
        else if($this->session->userdata('AdminUserType') == 'Restaurant Admin')
        {
            $this->db->select('entity_id');
            $this->db->where('parent_user_id',$this->session->userdata('AdminUserID'));                
            $resultadmin = $this->db->get('users')->result();            
            if($resultadmin && !empty($resultadmin))
            {
                $resultadminarrmain = array_column($resultadmin, 'entity_id');
            }
        }

        if(!empty($resultadminarrmain))
        {
            $data = array('dinein_count'=>0,'dinein_view_status'=>1);
            $this->db->where_in('admin_id',$resultadminarrmain);
            $this->db->update('order_notification',$data);
        }
        //Code for update the notificaton count base on admin :: End
        $data = array('dinein_count'=>0,'dinein_view_status'=>1);
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $this->db->update('order_notification',$data);
    }
    //get notification count 
    public function getNotificationCount(){
        $this->db->select('order_count');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('order_notification')->first_row();
    }
    //get user detail
    public function getUserEmail($user_id){
        $this->db->select('email, first_name, status, entity_id as user_id, user_type');
        $this->db->where('entity_id',$user_id);
        return $this->db->get('users')->first_row();
    }
    //get email template
    public function getEmailTempate(){
        $lang_slug = $this->session->userdata('language_slug');
        $this->db->select('entity_id,title,email_slug');
        $this->db->where('language_slug',$lang_slug);
        $this->db->where('status',1);
        $this->db->order_by('title', 'ASC');
        return $this->db->get('email_template')->result();
    }
    //change view status
    public function changeEventStatus(){
        $data = array('event_count'=>0,'view_status'=>1);
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $this->db->update('event_notification',$data);
    }
    //get Restaurant and branch Admin
    public function getRestaurantAdmin(){
        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['res_admin'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Restaurant Admin'))->result();

        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['branch_admin'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Branch Admin'))->result();
        return $data;
    }
    //get last events
    public function getLastEvents(){
        $this->db->select('o.amount as rate,o.booking_date,o.event_status as ostatus,o.status,o.entity_id as entity_id,u.first_name as fname,u.last_name as lname,restaurant.currency_id');
        $this->db->join('users as u','o.user_id = u.entity_id');
        $this->db->join('restaurant','o.restaurant_id = restaurant.content_id');
        $this->db->order_by('o.entity_id','desc');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where('restaurant.language_slug', $this->session->userdata('language_slug'));
        $this->db->limit(5);
        $result = $this->db->get('event as o')->result(); 
        if($result && !empty($result)) 
        {
            for($oi=0;$oi<count($result);$oi++)
            {
                $result[$oi]->booking_date= $this->common_model->getZonebaseDate($result[$oi]->booking_date);
            }
        }
        return $result;
    }
    //get last coupons
    public function getLastCoupons(){
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('coupon.created_by',$this->session->userdata('AdminUserID'));  
        }
        $this->db->select('coupon.name,coupon.amount_type,coupon.amount,coupon.end_date,restaurant.currency_id,coupon.entity_id');
        $this->db->join('coupon_restaurant_map','coupon.entity_id = coupon_restaurant_map.coupon_id','left');
        $this->db->join('restaurant','coupon_restaurant_map.restaurant_id = restaurant.content_id','left');
        $this->db->where_not_in('coupon.coupon_type',array('free_delivery','discount_on_categories'));
        $this->db->where('restaurant.language_slug',$this->session->userdata('language_slug'));
        $this->db->group_by('coupon.entity_id');
        $this->db->order_by('coupon.entity_id','desc');
        $this->db->limit(5);
        $result = $this->db->get('coupon')->result();
        if($result && !empty($result)) 
        {
            for($oi=0;$oi<count($result);$oi++)
            {
                $result[$oi]->end_date= $this->common_model->getZonebaseDate($result[$oi]->end_date);
            }
        }
        return $result;        
    }
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    }
    // Get DeviceID
    public function getUserDevices($userids)
    {
        $this->db->select('device_id');
        $this->db->where_in('entity_id',$userids);
        $this->db->where('users.status',1); // ACTIVE
        $this->db->where('users.notification',1);
        return $this->db->get('users')->result_array();
    }
    public function addRecordBatch($table,$data)
    {
        return $this->db->insert_batch($table, $data);
    }

    //Code for garph feature :: Start
    public function getYear(){
        $this->db->select('year(order_date) as year');
        $this->db->from('order_master');
        $this->db->group_by('year(order_date)');  
        return $this->db->get()->result_array();
        // return $query->result();
    }
    public function fetch_chart_data($startdate, $enddate){
        $status_arr = array('placed','accepted','delivered','onGoing','preparing','pending','complete','ready');
        $this->db->select('sum(total_rate) as total,order_date as day');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where('Date(order_date) >=', $startdate);
        $this->db->where('Date(order_date) <=', $enddate);
        $this->db->where_in('order_master.order_status',$status_arr);
        $this->db->where('order_master.stripe_refund_id',null);
        $this->db->group_by('date(order_date)'); 
        return $this->db->get('order_master')->result_array();
    }
    public function getLifetimeSale()
    {
        $status_arr = array('placed','accepted','delivered','onGoing','preparing','pending','complete','ready');
        $this->db->select('sum(total_rate) as total,restaurant.currency_id');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where_in('order_master.order_status',$status_arr);
        $this->db->where('order_master.stripe_refund_id',null);
        return $this->db->get('order_master')->result();
    }
    public function this_month()
    {
        $status_arr = array('placed','accepted','delivered','onGoing','preparing','pending','complete','ready');
        $this->db->select('sum(total_rate) as this_month,restaurant.currency_id');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id');
        $this->db->where('MONTH(order_master.created_date)', date('m'));
        $this->db->where('YEAR(order_master.created_date)', date('Y'));
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where_in('order_master.order_status',$status_arr);
        $this->db->where('order_master.stripe_refund_id',null);
        return $this->db->get('order_master')->result();
    }
    public function last_month()
    {
        $status_arr = array('placed','accepted','delivered','onGoing','preparing','pending','complete','ready');
        $first_date = date('Y-m-d', strtotime('first day of last month'));
        $last_date = date('Y-m-d', strtotime('last day of last month'));
        $this->db->select('sum(total_rate) as last_month,restaurant.currency_id');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id');
        $this->db->where('order_master.created_date BETWEEN "'.$first_date.'" AND "'.$last_date.'" ');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->where_in('order_master.order_status',$status_arr);
        $this->db->where('order_master.stripe_refund_id',null);
        return $this->db->get('order_master')->result();
    }
    //Code for garph feature :: End

    // get notifications for current date - Admin Alert
    public function getNotifications(){
        $this->db->select("admin_alerts.*");
        $customWhere = "from_date <= '".date('Y-m-d')."' AND to_date >= '".date('Y-m-d')."'";
        $this->db->where($customWhere);
        return $this->db->get("admin_alerts")->result_array();   
    }
    public function getEmailTempateDetails($template_id){
        $lang_slug = $this->session->userdata('language_slug');
        $this->db->select('email_slug');
        $this->db->where('language_slug',$lang_slug);
        $this->db->where('entity_id',$template_id);
        return $this->db->get('email_template')->first_row();
    }
    //get notification
    public function ajaxTableBookingNotification(){
        //get last orders
        $this->db->select('table.entity_id');
        $this->db->join('restaurant','table.restaurant_content_id = restaurant.content_id','left');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){            
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
        }
        $this->db->where('DATE(table.created_date)',date('Y-m-d'));
        $this->db->limit(1);
        $this->db->order_by('table.entity_id','desc');
        $count = $this->db->get('table_booking as table')->first_row();
        //get notification count
        $this->db->select('last_tablebooking_id,tablebooking_count,view_status,date');
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $last_booking = $this->db->get('table_booking_notification')->first_row();

        $date = date('Y-m-d');
        if(!empty($count) && (!empty($last_booking) && $last_booking->last_tablebooking_id == 0)){
            $data = array('last_tablebooking_id'=>$count->entity_id,'tablebooking_count'=>count($count),'date'=>$date);
            $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
            $this->db->update('table_booking_notification',$data);
        }else if(!empty($count) && empty($last_booking)){
            $this->db->select('table.entity_id as tablebooking_count');
            $this->db->join('restaurant','table.restaurant_content_id = restaurant.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){                
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('DATE(table.created_date)',date('Y-m-d'));
            $arrayData = $this->db->get('table_booking as table')->num_rows();
            
            $data = array(
                'last_tablebooking_id'=>$count->entity_id,
                'tablebooking_count'=>$arrayData,
                'date'=>$date,
                'view_status'=>0,
                'admin_id'=>$this->session->userdata('AdminUserID')
            );
            $this->db->insert('table_booking_notification',$data);
            $this->db->insert_id();
        }
        if(!empty($count) && !empty($last_booking)){
            $tablebooking_count = 0;
            $this->db->select('table.entity_id as tablebooking_count');
            $this->db->join('restaurant','table.restaurant_content_id = restaurant.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){                
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));  
            }
            $this->db->where('table.entity_id >', $last_booking->last_tablebooking_id);
            $this->db->where('table.entity_id <=', $count->entity_id);
            $this->db->where('DATE(table.created_date)',date('Y-m-d'));
            $arrayData = $this->db->get('table_booking as table')->num_rows();
            if(!empty($arrayData)){
                $tablebooking_count = $arrayData;
            }
            if($count->entity_id != $last_booking->last_tablebooking_id && $last_booking->last_tablebooking_id != 0){
                if($last_booking->view_status == 0){
                    $tablebooking_count = $tablebooking_count + $last_booking->tablebooking_count;
                    $date = ($last_booking->date == date('Y-m-d'))?$last_booking->date:date('Y-m-d');
                    $data = array('tablebooking_count'=>$tablebooking_count,'view_status'=>0,'date'=>$date,'last_tablebooking_id'=>$count->entity_id);
                }else if($last_booking->view_status == 1){
                    $date = ($last_booking->date == date('Y-m-d'))?$last_booking->date:date('Y-m-d');
                    $data = array('tablebooking_count'=>$tablebooking_count,'view_status'=>0,'date'=>$date,'last_tablebooking_id'=>$count->entity_id);
                }
                $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
                $this->db->update('table_booking_notification',$data);
            }
        }
        $this->db->select('tablebooking_count');
        $this->db->where('date',date('Y-m-d'));
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        return $this->db->get('table_booking_notification')->first_row();
    }
    //change view status
    public function changeTableBookingStatus(){
        $data = array('tablebooking_count'=>0,'view_status'=>1);
        $this->db->where('admin_id',$this->session->userdata('AdminUserID'));
        $this->db->update('table_booking_notification',$data);
    }
    public function getPlacedOrders() {
        $this->db->select("order_master.order_delivery,order_master.order_status as ostatus,order_master.payment_option,order_master.entity_id as order_id,order_master.user_id,order_master.agent_id,order_master.restaurant_id,order_master.order_date,order_master.transaction_id,order_master.refund_status,tips.tips_transaction_id,tips.refund_status as tips_refund_status,order_master.scheduled_date,order_master.slot_open_time,order_master.slot_close_time, tips.payment_option as tip_payment_option");
        $this->db->join('tips','order_master.entity_id = tips.order_id AND tips.amount > 0','left');
        $this->db->where('order_master.order_status', 'placed'); 
        $this->db->where('order_master.order_delivery!=', 'DineIn');       
        $this->db->group_by('order_master.entity_id');
        $result = $this->db->get('order_master')->result();
        return (!empty($result)) ? $result : array();
    }
    public function getOrderstoMarkDelayed() {
        $this->db->select("order_master.is_delayed,order_master.entity_id as order_id,order_master.order_date,delayed_status_check.order_status as check_order_status,delayed_status_check.time as check_status_time,order_master.scheduled_date,order_master.slot_open_time,order_master.slot_close_time");
        $this->db->join('order_status as delayed_status_check','order_master.entity_id = delayed_status_check.order_id AND (delayed_status_check.order_status = "delivered" OR delayed_status_check.order_status = "complete" OR delayed_status_check.order_status = "rejected" OR delayed_status_check.order_status = "cancel")','left');
        $this->db->where('order_master.is_delayed', 0);
        $this->db->where('order_master.order_delivery!=', 'DineIn');
        $this->db->group_by('order_master.entity_id');
        $result = $this->db->get('order_master')->result();
        return (!empty($result)) ? $result : array();
    }
}
?>