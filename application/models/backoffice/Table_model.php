<?php
class Table_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    } 
    //ajax view       
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        if($this->input->post('table_no') != ''){
             $this->db->where('table.table_number', $this->input->post('table_no'));
         }
         if($this->input->post('restaurant') != ''){
             $this->db->like('res.name', trim($this->input->post('restaurant')));
         }
         if($this->input->post('capacity') != ''){
             $this->db->where('table.capacity', trim($this->input->post('capacity')));
         } 
         $this->db->select('table.table_number as table_no,res.name as rname,table.entity_id,table.status,table.qr_code');
         $this->db->join('restaurant as res','table.resto_entity_id = res.content_id','left');
         /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
             $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
         } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
             $this->db->where('res.created_by',$this->session->userdata('UserID'));
         }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
         $this->db->group_by('table.content_id');
         $result['total'] = $this->db->count_all_results('table_master as table');

         if($this->input->post('table_no')=="" && $this->input->post('restaurant') == '' && $this->input->post('capacity') == ''){
             $this->db->select('content_general_id,table.content_id,table.entity_id,table.table_number,table.capacity,table.qr_code,table.status,res.name as rname');   
             $this->db->join('table_master as table','table.content_id = content_general.content_general_id','left');
             $this->db->join('restaurant as res','table.resto_entity_id = res.content_id','left');
             /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                 $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
             } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                 $this->db->where('res.created_by',$this->session->userdata('UserID'));
             }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
             $this->db->where('content_type','table');
             $this->db->group_by('table.content_id');
             /*if($displayLength>1){
                 $this->db->limit($displayLength,$displayStart);
             }*/
             $dataCmsOnly = $this->db->get('content_general')->result(); 
            if($displayLength>1){
             $this->db->limit($displayLength,$displayStart);
            }
             $content_general_id = array();
             foreach ($dataCmsOnly as $key => $value) {
                 $content_general_id[] = $value->content_general_id;
             }
             if($content_general_id){
                 $this->db->where_in('table.content_id',$content_general_id);    
             }            
         }else{          
             if($this->input->post('table_no') != ''){
                 $this->db->where('table.table_number', $this->input->post('table_no'));
             }   
             if($this->input->post('restaurant') != ''){
                 $this->db->like('res.name', trim($this->input->post('restaurant')));
             } 
             if($this->input->post('capacity') != ''){
                 $this->db->where('table.capacity', trim($this->input->post('capacity')));
             } 
             $this->db->select('content_general_id,table.content_id,table.entity_id,table.table_number,table.capacity,table.qr_code,table.status,table.language_slug,res.name as rname');   
             $this->db->join('table_master as table','table.content_id = content_general.content_general_id','left');
             $this->db->join('restaurant as res','table.resto_entity_id = res.content_id','left');
             /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                 $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
             } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                 $this->db->where('res.created_by',$this->session->userdata('UserID'));
             }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
             $this->db->where('content_type','table');
             $this->db->group_by('table.content_id');
             /*if($displayLength>1){
                 $this->db->limit($displayLength,$displayStart);
             }*/
             if($sortFieldName != ''){
                 $this->db->order_by($sortFieldName, $sortOrder);
             }
             $dataCmsOnly = $this->db->get('content_general')->result(); 
             if($displayLength>1){
                 $this->db->limit($displayLength,$displayStart);
             }
             $ContentID = array();
             $OrderByID = '';
             foreach ($dataCmsOnly as $key => $value) {
                 $OrderByID = $OrderByID.','.$value->entity_id;
                 $ContentID[] = $value->content_id;
             }   
             if($OrderByID && $ContentID){            
                 $this->db->order_by('FIELD ( table.entity_id,'.trim($OrderByID,',').') DESC');                
                 $this->db->where_in('table.content_id',$ContentID);
             }else{              
                 if($this->input->post('table_no') != ''){
                     $this->db->where('table.table_number', trim($this->input->post('table_no')));
                 } 
                 if($this->input->post('restaurant') != ''){
                     $this->db->like('res.name', trim($this->input->post('restaurant')));
                 } 
                 if($this->input->post('capacity') != ''){
                     $this->db->where('table.capacity', $this->input->post('capacity'));
                 } 
             }
         }  
         $this->db->select('content_general_id,table.content_id,table.entity_id,table.table_number,table.capacity,table.qr_code,table.status,table.language_slug,res.name as rname,res.content_id as res_content_id,res.currency_id');   
         $this->db->join('content_general','table.content_id = content_general.content_general_id','left');
         $this->db->join('restaurant as res','table.resto_entity_id = res.content_id','left');
         /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
             $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
         } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
             $this->db->where('res.created_by',$this->session->userdata('UserID'));
         }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
         if($sortFieldName != ''){
             $this->db->order_by($sortFieldName, $sortOrder);
         }
         $this->db->group_by('table.content_id');
         $cmdData = $this->db->get('table_master as table')->result_array();

         $cmsLang = array();        
         if(!empty($cmdData)){
             foreach ($cmdData as $key => $value) {                
                 if(!array_key_exists($value['content_id'],$cmsLang))
                 {
                     $cmsLang[$value['content_id']] = array(
                         'entity_id'=>$value['entity_id'],
                         'content_id' => $value['content_id'],
                         'table_no' => $value['table_number'],
                         'restaurant' =>$value['rname'],
                         'res_content_id' =>$value['res_content_id'],
                         'capacity' => $value['capacity'], 
                         'qr_code' => $value['qr_code'],
                         'status' => $value['status']                    
                     );
                 }
                 $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'table_no' => $value['table_number'],
                    'restaurant' =>$value['rname'],
                    'res_content_id' =>$value['res_content_id'],
                    'capacity' => $value['capacity'], 
                    'qr_code' => $value['qr_code'],
                    'status' => $value['status']   
                 );
             }
        }     
        $result['data'] = $cmsLang;  
        foreach ($result['data'] as $key => $value)
        {
            $rest_name = $value['restaurant'];
            $this->db->select('name');
            $this->db->where('content_id',$value['res_content_id']);
            $this->db->where('language_slug',$this->session->userdata('language_slug'));
            $res_result =  $this->db->get('restaurant')->first_row();               
            if($res_result)
            {
                $rest_name = $res_result->name;
            }
            $result['data'][$key]['restaurant'] = $rest_name; 
        }
        return $result; 
    }

    public function getReservationRequestGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10){
        // print_r($this->session->userdata('restaurant'));exit;
         if($this->input->post('table_no') != ''){
             $this->db->where('table.table_number', $this->input->post('table_no'));
         }
         if($this->input->post('restaurant') != ''){
             $this->db->like('res.name', trim($this->input->post('restaurant')));
         }
         $this->db->select('table.table_number as table_no, table.content_id, res.name as rname,table.entity_id,table.resto_entity_id,tb.status');
         $this->db->join('table_master as table','tb.table_master_id = table.entity_id','left');
         $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id','left');
         /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
             $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
         } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
             $this->db->where('res.created_by',$this->session->userdata('UserID'));
         }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
         $this->db->where('tb.status','pending');
         $this->db->group_by('tb.content_id');
         $this->db->order_by('tb.created_at','DESC');
         $result['total'] = $this->db->count_all_results('table_status as tb');
         
         if($this->input->post('table_no')=="" && $this->input->post('restaurant') == ''){
             $this->db->select('content_general_id,table.table_number as table_no,table.content_id,tb.status,res.name as rname');   
             $this->db->join('table_status as tb','tb.content_id = content_general_id','left');
             $this->db->join('table_master as table','tb.table_master_id = table.entity_id','left');
             $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id','left');
             /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
             } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                $this->db->where('res.created_by',$this->session->userdata('UserID'));
             }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
             $this->db->where('content_type','table');
             $this->db->where('tb.status','pending');
             $this->db->group_by('tb.content_id');
             $this->db->order_by('tb.created_at','DESC');
             if($displayLength>1)
                 $this->db->limit($displayLength,$displayStart);
             $dataCmsOnly = $this->db->get('content_general')->result(); 

             $content_general_id = array();
             foreach ($dataCmsOnly as $key => $value) {
                 $content_general_id[] = $value->content_general_id;
             }
             if($content_general_id){
                 $this->db->where_in('tb.content_id',$content_general_id);    
             }            
         }else{          
             if($this->input->post('table_no') != ''){
                 $this->db->where('table.table_number', $this->input->post('table_no'));
             }   
             if($this->input->post('restaurant') != ''){
                 $this->db->like('res.name', trim($this->input->post('restaurant')));
             }  
             $this->db->select('content_general_id,table.table_number as table_no,table.content_id,tb.status,res.name as rname');   
             $this->db->join('table_status as tb','tb.content_id = content_general_id','left');
             $this->db->join('table_master as table','tb.table_master_id = table.entity_id','left');
             $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id','left');
             
             /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
                 $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
             } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
                 $this->db->where('res.created_by',$this->session->userdata('UserID'));
             }*/
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
             $this->db->where('content_type','table');
             $this->db->where('tb.status','pending');
             $this->db->group_by('tb.content_id');
             $this->db->order_by('tb.created_at','DESC');
             if($displayLength>1)
                 $this->db->limit($displayLength,$displayStart);
             $dataCmsOnly = $this->db->get('content_general')->result(); 
             $ContentID = array();               
             foreach ($cmsData as $key => $value) {
                 $OrderByID = $OrderByID.','.$value->entity_id;
                 $ContentID[] = $value->content_id;
             }   
             if($OrderByID && $ContentID){            
                 $this->db->order_by('FIELD ( tb.entity_id,'.trim($OrderByID,',').') DESC');                
                 $this->db->where_in('tb.content_id',$ContentID);
             }else{              
                 if($this->input->post('table_no') != ''){
                     $this->db->where('table.table_number', trim($this->input->post('table_no')));
                 } 
                 if($this->input->post('restaurant') != ''){
                     $this->db->like('res.name', trim($this->input->post('restaurant')));
                 } 
             }
         }  
         $this->db->select('content_general_id,table.table_number as table_no,table.content_id,tb.status,res.name as rname');   
         $this->db->join('table_master as table','table.entity_id = tb.table_master_id','left');
         $this->db->join('content_general','content_general.content_general_id = tb.content_id','left');
         $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id','left');
         /*if($this->session->userdata('AdminUserType') == 'Restaurant Admin' && !empty($this->session->userdata('restaurant'))){     
             $this->db->where_in('table.resto_entity_id',$this->session->userdata('restaurant'));
         } elseif($this->session->userdata('AdminUserType') != 'MasterAdmin'){
             $this->db->where('res.created_by',$this->session->userdata('UserID'));
         }*/
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
         if($sortFieldName != '')
            $this->db->order_by('tb.created_at','DESC');
         $this->db->where('tb.status','pending');
         $cmdData = $this->db->get('table_status as tb')->result_array();            
         $cmsLang = array();        
         if(!empty($cmdData)){
             foreach ($cmdData as $key => $value) {                
                 if(!array_key_exists($value['content_id'],$cmsLang))
                 {
                     $cmsLang[$value['content_id']] = array(
                         'entity_id'=>$value['entity_id'],
                         'content_id' => $value['content_id'],
                         'table_no' => $value['table_number'],
                         'restaurant' =>$value['rname'],
                         'status' => $value['status']                    
                     );
                 }
                //  $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                //     'translation_id' => $value['entity_id'],
                //     'table_no' => $value['table_number'],
                //     'restaurant' =>$value['rname'],
                //     'status' => $value['status']   
                //  );
             }
        }     
        // var_dump($cmsLang);    exit;    
         $result['data'] = $cmsLang;  
         return $result; 
    }

    public function getReservationGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10,$currentornot='current'){
        // print_r($this->session->userdata('restaurant'));exit;
        if($this->input->post('table_no') != ''){
            $this->db->where('table.table_number', $this->input->post('table_no'));
        }
        if($this->input->post('restaurant') != ''){
            $this->db->like('res.name', trim($this->input->post('restaurant')));
        }

        if($this->input->post('order_id') != ''){
         $this->db->like('ord.entity_id', trim($this->input->post('order_id')));
        }
        if($this->input->post('capacity') != ''){
         $this->db->like('table.capacity', trim($this->input->post('capacity')));
        }
        if($this->input->post('customer') != ''){            
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="(((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%')";
            $this->db->where($where_string);
        }

        if($this->input->post('reservation_date') != ''){
            $this->db->like('tb.created_at', trim($this->input->post('reservation_date')));
        } 
        $this->db->select('table.table_number as table_no, table.content_id, res.name as rname,table.entity_id,table.resto_entity_id,tb.status');
        $this->db->join('table_master as table','tb.table_master_id = table.entity_id');
        $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id');
        $this->db->join('users as users','tb.user_id = users.entity_id','left');
        $this->db->join('order_master as ord','table.entity_id = ord.table_id');
        $where_string = "ord.user_id=tb.user_id";
        $this->db->where($where_string);
         
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }

        if($this->input->post('order_status') != '')
        {
            $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%')";
            if(preg_match("/accepted/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%' OR (ord.order_status like 'placed' AND ord.status ='1'))";
            }
            else if(preg_match("/placed/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%' AND ord.status !='1')";
            }
            else if(preg_match("/orderready/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%onGoing%' AND ord.order_delivery ='PickUp')";
            }
            else if(preg_match("/onGoing/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%onGoing%' AND ord.order_delivery ='Delivery')";
            }
            //$this->db->like('o.order_status', $this->input->post('order_status'));
            $this->db->where($where_status);
        }

        $this->db->where('tb.status','approve');

        if($currentornot=='past')
        {
            $where_cond="(ord.order_status In('delivered','cancel','rejected','complete') OR ord.admin_payment_option IS NOT NULL OR ord.payment_option in ('stripe','paypal'))";
            $this->db->where($where_cond);
        }
        else
        {
            $this->db->where_not_in('ord.order_status',array('delivered','cancel','rejected','complete'));
            $this->db->where_not_in('ord.payment_option',array('stripe','paypal'));
            $this->db->where('ord.admin_payment_option',null);
        }

        $this->db->group_by('table.entity_id');
        $this->db->group_by('ord.entity_id');
        $result['total'] = $this->db->count_all_results('table_status as tb');
         
        $this->db->select("table.content_id,table.table_number,table.capacity,tb.entity_id as table_status_id,tb.user_id,tb.table_master_id,tb.resto_entity_id,tb.status,tb.created_at,tb.updated_at,res.name as rname, tb.created_at as reservation_date,table.entity_id,users.first_name as fname,users.last_name as lname,CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,'')) as user_phn_no,ord.entity_id as order_id, ord.order_status, ord.payment_option, ord.admin_payment_option, ord.status as ostatus");   
        $this->db->join('table_master as table','table.entity_id = tb.table_master_id');        
        $this->db->join('restaurant as res','table.resto_entity_id = res.entity_id');
        $this->db->join('users as users','tb.user_id = users.entity_id','left');
        $this->db->join('order_master as ord','table.entity_id = ord.table_id');
        $where_string = "ord.user_id=tb.user_id";
        $this->db->where($where_string);        

        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
            $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){
            $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
              
        if($this->input->post('table_no') != ''){
         $this->db->where('table.table_number', trim($this->input->post('table_no')));
        } 
        if($this->input->post('restaurant') != ''){
         $this->db->like('res.name', trim($this->input->post('restaurant')));
        }

        if($this->input->post('order_id') != ''){
         $this->db->like('ord.entity_id', trim($this->input->post('order_id')));
        }
        if($this->input->post('capacity') != ''){
         $this->db->like('table.capacity', trim($this->input->post('capacity')));
        }
        if($this->input->post('customer') != ''){            
            $user_mobile_no = "CONCAT('+',COALESCE(users.phone_code,''),COALESCE(users.mobile_number,''))";
            $where_string="(((CASE WHEN users.last_name is NULL THEN users.first_name ELSE CONCAT(users.first_name,' ',users.last_name) END) like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%') OR (".$user_mobile_no.") like '%".$this->common_model->escapeString(trim($this->input->post('customer')))."%')";
            $this->db->where($where_string);
        }

        if($this->input->post('reservation_date') != ''){
         $this->db->like('tb.created_at', trim($this->input->post('reservation_date')));
        }
        if($this->input->post('order_status') != '')
        {
            $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%')";
            if(preg_match("/accepted/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%' OR (ord.order_status like 'placed' AND ord.status ='1'))";
            }
            else if(preg_match("/placed/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%".$this->input->post('order_status')."%' AND ord.status !='1')";
            }
            else if(preg_match("/orderready/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%onGoing%' AND ord.order_delivery ='PickUp')";
            }
            else if(preg_match("/onGoing/", $this->input->post('order_status')))
            {
                $where_status = "(ord.order_status like '%onGoing%' AND ord.order_delivery ='Delivery')";
            }
            //$this->db->like('o.order_status', $this->input->post('order_status'));
            $this->db->where($where_status);
        }


        if($sortFieldName != '')
        {
            $this->db->order_by($sortFieldName, $sortOrder);
        }
        else
        {
            $this->db->order_by('tb.created_at','DESC');
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);  
        
        $this->db->where('tb.status','approve');

        if($currentornot=='past')
        {
            $where_cond="(ord.order_status In('delivered','cancel','rejected','complete') OR ord.admin_payment_option IS NOT NULL OR ord.payment_option in ('stripe','paypal'))";
            $this->db->where($where_cond);
        }
        else
        {
            $this->db->where_not_in('ord.order_status',array('delivered','cancel','rejected','complete'));
            $this->db->where_not_in('ord.payment_option',array('stripe','paypal'));
            $this->db->where('ord.admin_payment_option',null);
        }

        $this->db->group_by('table.entity_id');
        $this->db->group_by('ord.entity_id');        
        $cmdData = $this->db->get('table_status as tb')->result_array();
        $cmsLang = array();        
        if(!empty($cmdData)){
            foreach ($cmdData as $key => $value)
            {
                    $cmsLang[] = array(
                         'entity_id'=>$value['entity_id'],
                         'content_id' => $value['content_id'],
                         'table_no' => $value['table_number'],
                         'table_status_id' => $value['table_status_id'],
                         'restaurant' =>$value['rname'],
                         'status' => $value['status'],
                         'reservation_date' => $value['reservation_date'],
                         'capacity' => $value['capacity'],
                         'fname' => $value['fname'],
                         'lname' => $value['lname'],
                         'user_phn_no' => $value['user_phn_no'],
                         'order_id' => $value['order_id'],
                         'order_status' => $value['order_status'],
                         'ostatus' => $value['ostatus'],
                     );
                 
            }
        }     
        // var_dump($cmsLang);    exit;    
        $result['data'] = $cmsLang;  
        return $result; 
    }
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 

    public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->get($table)->first_row();
    } 
    //get single data
    public function getEditDetail($entity_id)
    {
        // $this->db->select('restaurant.*');
        // $this->db->join('res.entity_id = res_add.resto_entity_id','left');
        // $this->db->where('res.entity_id',$entity_id);
        return $this->db->get_where('table_master',array('entity_id'=>$entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data,$tblName,$fieldName,$ID)
    {        
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);            
        return $this->db->affected_rows();
    }

    // updating the changed status
    public function UpdatedStatusAll($tblname,$ContentID,$Status){
        if($Status==0){
            $Data = array('status' => 1);
        } else {
            $Data = array('status' => 0);
        }

        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
        return $this->db->affected_rows();
    }

    // approve request
    public function ApproveAll($tblname,$ContentID,$Status){
        $Data = array('status' => $Status);
        $this->db->where('content_id',$ContentID);
        $this->db->update($tblname,$Data);
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
    
    // delete tabble
    public function ajaxDelete($tblname,$content_id,$entity_id)
    {
        // check  if last record
        if($content_id){
            $vals = $this->db->get_where($tblname,array('content_id'=>$content_id))->num_rows();    
            if($vals==1){
                $this->db->where(array('content_general_id' => $content_id));
                $this->db->delete('content_general');        
            }            
        } 
        $this->db->where('entity_id',$entity_id);
        $this->db->delete($tblname);     
    }

    // delete All
    public function ajaxDeleteAllReservation($tblname,$content_id)
    {
        //$this->db->where(array('content_general_id' => $content_id));
       // $this->db->delete('content_general');                   
        $this->db->where('entity_id',$content_id);
        $this->db->delete($tblname);  
    }

    // delete all records
    public function ajaxDeleteAll($tblname,$content_id)
    {
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');                   

        $this->db->where('content_id',$content_id);
        $this->db->delete($tblname);  
    }
    public function checkTableNameExist($table_no,$table_entity_id,$restaurant_id)
    {
        $this->db->select('entity_id');
        $this->db->where('table_number',$table_no);
        $this->db->where('resto_entity_id',$restaurant_id);
        if($table_entity_id){
            $this->db->where('entity_id !=',$table_entity_id);            
        }
        return $this->db->get('table_master')->num_rows();
    }
}
?>