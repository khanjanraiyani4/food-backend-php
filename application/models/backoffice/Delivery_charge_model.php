<?php
class Delivery_charge_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    } 
    //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('area_name', trim($this->input->post('page_title')));
        } 
        if($this->input->post('res_name') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('res_name')));
        } 
        if($this->input->post('price') != ''){
            $total_price = trim($this->input->post('price'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('price_charge', trim($total_search));
            }else{
                $this->db->like('price_charge', trim($this->input->post('price')));
            }
        }      
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->select('restaurant.name,delivery_charge.area_name,delivery_charge.price_charge,delivery_charge.charge_id,restaurant.currency_id');
        $this->db->join('restaurant','delivery_charge.restaurant_id = restaurant.content_id','left'); 
        $this->db->where('restaurant.status',1);
        $this->db->group_by('delivery_charge.charge_id');
        $result['total'] = $this->db->count_all_results('delivery_charge'); 
        if($sortFieldName != ''){
            $this->db->order_by($sortFieldName, $sortOrder);
        }

        if($displayLength>1){
            $this->db->limit($displayLength,$displayStart);
        }
        if($this->input->post('page_title') != ''){
            $this->db->like('area_name', trim($this->input->post('page_title')));
        } 
        if($this->input->post('res_name') != ''){
            $this->db->like('restaurant.name', trim($this->input->post('res_name')));
        } 
        if($this->input->post('price') != ''){
            $total_price = trim($this->input->post('price'));
            if($total_price[0] == '$')
            {
                $total_search = substr($total_price, 1);
                $this->db->like('price_charge', trim($total_search));
            }else{
                $this->db->like('price_charge', trim($this->input->post('price')));
            }
        }   
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->select('restaurant.name,restaurant.content_id,delivery_charge.area_name,delivery_charge.price_charge,delivery_charge.charge_id,restaurant.currency_id,delivery_charge.is_masterdata');
        $this->db->join('restaurant','delivery_charge.restaurant_id = restaurant.content_id','left');
        $this->db->where('restaurant.status',1);
        $this->db->group_by('delivery_charge.charge_id');
        $result['data'] = $this->db->get('delivery_charge')->result();

        foreach ($result['data'] as $key => $value)
        {
            $rest_name=$value->name;
            if($this->input->post('res_name') == '')
            {
                $this->db->select('name');
                $this->db->where('content_id',$value->content_id);
                $this->db->where('language_slug',$this->session->userdata('language_slug'));
                $res_result =  $this->db->get('restaurant')->first_row();               
                if($res_result)
                {
                    $rest_name=$res_result->name;
                }
            }
            $value->name = $rest_name; 
        }
        return $result;
    }  
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('delivery_charge',array('charge_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }
    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {           
        $this->db->where('charge_id',$content_id);
        $this->db->delete($tblname);    
    }
    //get list
    public function getListData($tblname,$language_slug=NULL,$res_content_id=NULL){
        $this->db->select('name,entity_id,content_id');
        $this->db->where('status',1);
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            // $this->db->where('created_by',$this->session->userdata('UserID'));
            $this->db->where('restaurant_owner_id',$this->session->userdata('AdminUserID'));  
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug',$language_slug);  
        }
        if($tblname == 'restaurant'){
            $this->db->like('restaurant.order_mode','Delivery');
            if($res_content_id){
                $this->db->or_where('restaurant.content_id',$res_content_id);
            }
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get($tblname)->result();
    }
    public function getResLatLong($restaurant_id){
        $this->db->select('entity_id');
        $this->db->where('content_id',$restaurant_id);
        $res_return = $this->db->get('restaurant')->first_row();

        $this->db->select('latitude,longitude');
        $this->db->where('resto_entity_id',$res_return->entity_id);
        return $this->db->get('restaurant_address')->first_row();

    }
    public function getResIdByDeliveryCharge($delivery_charge_id){
        $this->db->select('restaurant_id');
        $this->db->where('charge_id',$delivery_charge_id);
        $res_return = $this->db->get('delivery_charge')->first_row();
        return ($res_return->restaurant_id) ? $res_return->restaurant_id : '';
    }
}
?>