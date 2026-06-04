<?php
class Notification_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }       
    public function getNotificationList($searchTitleName = '', $sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        $this->db->select('entity_id, notification_title');
        if($this->input->post('notification_title') != ''){
            $this->db->like('notification_title', trim($this->input->post('notification_title')));
        }
        $result['total'] = $this->db->count_all_results('notifications');
        $this->db->select('entity_id, notification_title');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('notification_title') != ''){
            $this->db->like('notification_title', trim($this->input->post('notification_title')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('notifications')->result();        
        return $result;
    }  
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    public function getEditNotificationDetail($entity_id)
    {
        return $this->db->get_where('notifications',array('entity_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }  
    public function deleteRecord($entity_id){          
        $this->db->where('entity_id',$entity_id);
        $this->db->delete('notifications');
        return $this->db->affected_rows();
    }
    public function deleteInsertRecord($tablename,$wherefieldname,$wherefieldvalue,$data)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->delete($tablename);
        
        return $this->db->insert_batch($tablename,$data);
    }
    // Get user for notification
    public function getUserNotification()
    {
        //get user list
        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['users'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'User','notification'=>1))->result();
        //get drivers list
        $this->db->select('entity_id, first_name, last_name, device_id');
        $data['drivers'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Driver','notification'=>1))->result();
        return $data;
    }
    // Get DeviceID
    public function getUserDevices($userids)
    {
        $this->db->select('device_id');
        $this->db->where_in('entity_id',$userids);
        $this->db->where('users.status',1); // ACTIVE
        $this->db->where('users.notification',1);
        $this->db->where('users.device_id!=','');
        $this->db->group_by('users.device_id');
        return $this->db->get('users')->result_array();
    }
    public function addRecordBatch($table,$data)
    {
        return $this->db->insert_batch($table, $data);
    }
    // method to get user details by id
    public function getEditDetail($tblname,$entity_id)
    {
        return $this->db->get_where($tblname,array('entity_id'=>$entity_id))->first_row();
    }
    // method to get user details by id
    public function getNotificationUsers($entity_id)
    {
        $this->db->select('user_id');
        return $this->db->get_where('notifications_users',array('notification_id'=>$entity_id))->result();
    }
    // check user
    public function checkUser($entity_id)
    {
        $this->db->select('entity_id');
        return $this->db->get_where('users',array('entity_id'=>$entity_id,'user_type'=>'User'))->result();
    }
    // check driver
    public function checkDriver($entity_id)
    {
        $this->db->select('entity_id');
        return $this->db->get_where('users',array('entity_id'=>$entity_id,'user_type'=>'Driver'))->result();
    }
    //Code for find the users city name and zipcode :: Start
    public function getUserCitiesandZip($list_for)
    {
        if($list_for=='city'){
            $this->db->select('address.city');    
        }
        else if($list_for=='zip'){
            $this->db->select('address.zipcode');    
        }
        $this->db->join('users as u','address.user_entity_id = u.entity_id','left');
        $this->db->where('u.notification',1);
        $this->db->where('u.status',1);
        $this->db->where('u.user_type','User');
        if($list_for=='city'){
            $this->db->where('address.city!=',null);
            $this->db->where('address.city!=','');
            $this->db->order_by('address.city', 'ASC');
            $this->db->group_by('address.city');
        }
        else if($list_for=='zip'){
            $this->db->where('address.zipcode!=',null);
            $this->db->where('address.zipcode!=','');
            $this->db->order_by('address.zipcode', 'ASC');
            $this->db->group_by('address.zipcode');
        }        
        $res = $this->db->get('user_address as address')->result_array();
        return $res;
    }
    public function getUserswitCityZip($data_array,$list_for)
    {
        if(!empty($data_array))
        {
            //Code for order with anget and user exist :: Start
            $this->db->select('u.entity_id');
            $this->db->join('users as u','order.user_id = u.entity_id');
            $this->db->where('u.entity_id!=','');
            $this->db->where('order.entity_id!=','');
            $this->db->where('order.agent_id!=','');
            $res_userarr = $this->db->get('order_master as order')->result();
            $user_ids = array();
            if($res_userarr && !empty($res_userarr))
            {
                $user_ids = array_column($res_userarr,'entity_id');
            }            
            //Code for order with anget and user exist :: End

            $this->db->select('u.entity_id, u.first_name, u.last_name, u.device_id');
            $this->db->join('users as u','address.user_entity_id = u.entity_id','left');
            $this->db->where('u.entity_id!=','');
            $this->db->where('u.device_id!=','');
            if($list_for=='city')
            {
                if(!empty($user_ids))
                {
                    $where_string="(u.entity_id in (".implode(",", $user_ids).") OR address.city in ('".implode("','", $data_array)."'))";
                    $this->db->where($where_string);
                }
                else
                {
                    $this->db->where_in('address.city',$data_array);    
                }                
            }
            else if($list_for=='zipcode')
            {
                if(!empty($user_ids))
                {
                    $where_string="(u.entity_id in (".implode(",", $user_ids).") OR address.zipcode in('".implode("','", $data_array)."'))";
                    $this->db->where($where_string);
                }
                else
                {
                    $this->db->where_in('address.zipcode',$data_array);
                }                
            }
            $this->db->where('u.notification',1);
            $this->db->where('u.status',1);
            $this->db->group_by('u.entity_id');
            $res = $this->db->get('user_address as address')->result();
            $data['users'] = $res;
            $data['drivers'] = [];
        }
        else
        {
            //get user list
            $this->db->select('entity_id, first_name, last_name, device_id');
            $data['users'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'User','notification'=>1))->result();
            //get drivers list
            $this->db->select('entity_id, first_name, last_name, device_id');
            $data['drivers'] = $this->db->order_by('first_name', 'ASC')->get_where('users',array('status'=>1,'user_type'=>'Driver','notification'=>1))->result();
        }        
        return $data;
    }
    public function get_restaurants($language_slug)
    {
        $this->db->select('res.name,res.content_id,res.entity_id,address.latitude,address.longitude');
        $this->db->join('restaurant_address as address','address.resto_entity_id = res.entity_id');
        $this->db->where('address.latitude!=','');
        $this->db->where('address.latitude!=',null);
        $this->db->where('address.longitude!=','');        
        $this->db->where('address.longitude!=',null);
        $this->db->where('res.language_slug','en');

        $this->db->order_by('res.name', 'ASC');
        $this->db->group_by('res.content_id');
        $res = $this->db->get('restaurant as res')->result();
        return $res;
    }
    public function getUserIdswithDistance($rest_lat,$rest_long,$distance,$UserIds)
    {
        $user_idarr = array();
        $this->db->select("(3959*acos(cos(radians($rest_lat))*cos(radians(address.latitude))*cos(radians(address.longitude)-radians($rest_long))+sin(radians($rest_lat))*sin(radians(address.latitude)))) as distance, u.entity_id");
        $this->db->join('user_address as address','u.entity_id = address.user_entity_id');
        $this->db->where('address.latitude!=','');
        $this->db->where('address.latitude!=',null);
        $this->db->where('address.longitude!=','');        
        $this->db->where('address.longitude!=',null);
        $this->db->where('u.notification',1);
        $this->db->where('u.status',1);
        $this->db->where_in('u.entity_id',$UserIds);
        $this->db->group_by('u.entity_id');
        $this->db->having('distance <=',$distance);
        $result = $this->db->get('users as u')->result();
        if($result && !empty($result))
        {
            $user_idarr = array_column($result, 'entity_id');
        }
        return $user_idarr;
    }
    //Code for find the users city name and zipcode :: End    
}
?>