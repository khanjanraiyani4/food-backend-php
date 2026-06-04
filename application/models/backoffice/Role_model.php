<?php
class Role_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
    //ajax data
    public function getPageList($searchTitleName = '', $sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('role_name') != '') {
            $role_name = $this->common_model->escapeString(trim($this->input->post('role_name')));
            $rolewhere = "(role_master.role_name LIKE '%".$role_name."%')";
            $this->db->where($rolewhere);
        }
        if($this->input->post('status') != ''){
            $this->db->where('role_master.status', trim($this->input->post('status')));
        }
        $result['total'] = $this->db->count_all_results('role_master');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('role_name') != '') {
            $role_name = $this->common_model->escapeString(trim($this->input->post('role_name')));
            $rolewhere = "(role_master.role_name LIKE '%".$role_name."%')";
            $this->db->where($rolewhere);
        }
        if($this->input->post('status') != ''){
            $this->db->where('role_master.status', trim($this->input->post('status')));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('role_master')->result();
        return $result;
    } 
    //get role access
    public function getRoleAccessRights($role_id){
        $this->db->where('role_id',$role_id);
        return $this->db->get('role_access_rights')->result_array();
    }
    public function getRoleAccessListModel($role_id)
    {
        return $this->db->get_where('role_access_rights',array('role_id'=>$role_id))->result();
    }    
    public function getAdminAccessListModel()
    {
        $this->db->where('is_hidden','0');
        //$this->db->order_by('access_id,access_name');
        $this->db->order_by('display_order', 'ASC');
        return $this->db->get("role_access")->result();
    }
    //Add role data
    function addRoleAccess($addUserData,$role_id)
    {
        $this->db->insert_batch('role_access_rights',$addUserData);
        
        //get inserted data of this role
        /*$this->db->select('role_access_rights.role_id,role_access_rights.access_id,role_access.access_name,role_access.parent_access_id');
        $this->db->join('role_access','role_access.access_id = role_access_rights.access_id');
        $getAddedAccessRights = $this->db->get_where('role_access_rights',array('role_access_rights.role_id' => $role_id))->result();
        //echo "<pre>";print_r($getAddedAccessRights);exit;
        //loop for check view is check and insert in the database
        foreach ($getAddedAccessRights as $key => $value) 
        {
            //condition for parent access 
            if($value->parent_access_id != 0)
            {
                //get the record of view access is inserted or not
                $this->db->join('role_access_rights','role_access_rights.access_id = role_access.access_id');
                $this->db->where(array('role_access_rights.role_id'=>$role_id,'role_access.parent_access_id'=>$value->parent_access_id));
                $this->db->where('(role_access.controller_slug = "index" OR role_access.access_name = "View")');
                $checkViewAdd = $this->db->get('role_access')->result();
                
                //if view of this access is empty then need to add
                if(empty($checkViewAdd))
                {
                    $this->db->select('access_id');
                    $insertingData = $this->db->get_where('role_access',array('parent_access_id'=>$value->parent_access_id,'controller_slug'=>'view'))->first_row();
                    
                    if(empty($insertingData))
                    {
                        $this->db->select('access_id');
                        $insertingData = $this->db->get_where('role_access',array('parent_access_id'=>$value->parent_access_id,'access_name'=>'View'))->first_row();
                    }

                    if(!empty($insertingData))
                    {
                        $addRoleAccessData = array(
                            'role_id'=>$role_id,
                            'access_id'=>$insertingData->access_id
                        );

                        $this->db->insert('role_access_rights',$addRoleAccessData);
                    }
                }
            }
        }*/
    }
    function addAdminRole($addAdminRole)
    {
        $this->db->insert('adminrole',$addAdminRole); 
        return $this->db->insert_id();
    }
    //Edit role data
    function updateRoleAccess($addUserData,$role_id = ''){
        if($role_id != '')
            $this->db->delete('role_access_rights', array('role_id' => $role_id));
        $this->db->insert_batch('role_access_rights',$addUserData);

        //get inserted data of this role
        /*$this->db->select('role_access_rights.role_id,role_access_rights.access_id,role_access.access_name,role_access.parent_access_id');
        $this->db->join('role_access','role_access.access_id = role_access_rights.access_id');
        $getAddedAccessRights = $this->db->get_where('role_access_rights',array('role_access_rights.role_id' => $role_id))->result();
        
        //loop for check view is check and insert in the database
        foreach ($getAddedAccessRights as $key => $value) 
        {
            //condition for parent access 
            if($value->parent_access_id != 0)
            {
                //get the record of view access is inserted or not
                $this->db->join('role_access_rights','role_access_rights.access_id = role_access.access_id');
                $checkViewAdd = $this->db->get_where('role_access',array('role_access_rights.role_id'=>$role_id,'role_access.parent_access_id'=>$value->parent_access_id,'role_access.controller_slug'=>'view'))->result();
                //if view of this access is empty then need to add
                if(empty($checkViewAdd))
                {
                    $this->db->select('access_id');
                    $insertingData = $this->db->get_where('role_access',array('parent_access_id'=>$value->parent_access_id,'controller_slug'=>'view'))->first_row();
                    if(empty($insertingData))
                    {
                        $this->db->select('access_id');
                        $insertingData = $this->db->get_where('role_access',array('parent_access_id'=>$value->parent_access_id,'access_name'=>'View'))->first_row();
                    }
                    if(!empty($insertingData))
                    {
                        $addRoleAccessData = array(
                            'role_id'=>$role_id,
                            'access_id'=>$insertingData->access_id
                        );
                        $this->db->insert('role_access_rights',$addRoleAccessData);                        
                    }
                }
            }
        }*/
    }
    function editAdminRole($editAdminRole,$role_id)
    {
        $this->db->where('Adminrole_id',$role_id);
        $this->db->update('adminrole',$editAdminRole);
        return $this->db->affected_rows(); 
    }
    public function checkRoleNameExist($role_name,$role_id){
        $this->db->select('role_master.role_id');
        $this->db->where('role_name',$role_name);
        if($role_id) {
            $this->db->where('role_id !=',$role_id);
        }
        return $this->db->get('role_master')->num_rows();
    }
}