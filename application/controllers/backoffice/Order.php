<?php if (!defined('BASEPATH')) 
    exit('No direct script access allowed');
class Order extends CI_Controller { 
    public $module_name = 'Order';
    public $controller_name = 'order';
    public $prefix = '_order';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/order_model');
    }
    public function updatedeliverymethod_script() {
        $whereArray = array('status'=>1, 'delivery_method'=>NULL, 'order_delivery'=>'Delivery');
        $data = array('delivery_method'=>'internal_drivers');
        $this->db->where($whereArray);
        $this->db->update('order_master',$data);
        $return = $this->db->affected_rows();
    }
    // view order
    public function view()
    {
        if(in_array('order~view',$this->session->userdata("UserAccessArray"))) {
            if($this->uri->segment('4')=='order_id') {
                $order_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):0;
            } else {
                $user_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):0;
            }
            $data['customer_name'] = ''; 
            if($user_id)
            {
                $customer_namearr = $this->order_model->GetUsername($user_id);
                $data['customer_name'] = $customer_namearr->first_name.' '.$customer_namearr->last_name; 
            }
            $data['user_id'] = (isset($user_id)) ? $user_id : '';              
            $data['order_id'] = (isset($order_id)) ? $order_id : '';              
            $data['meta_title'] = $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders').' | '.$this->lang->line('site_title');
            $language_slug = $this->session->userdata('language_slug');
            $data['restaurant'] = $this->order_model->getRestaurantList($language_slug);
            //$data['drivers'] = $this->order_model->getDrivers();
            //order count
            $this->db->select('o.entity_id');
            $this->db->join('users as u','o.user_id = u.entity_id','left');
            $this->db->join('restaurant','o.restaurant_id = restaurant.entity_id','left');
            $this->db->join('order_status','o.entity_id = order_status.order_id','left');
            $this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
            $this->db->join('order_detail','o.entity_id = order_detail.order_id','left'); 
            $this->db->join('users as driver','order_driver_map.driver_id = driver.entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                /*$this->db->where('restaurant.created_by',$this->session->userdata('UserID'));*/
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('o.entity_id');
            $data['order_count'] = $this->db->get('order_master as o')->num_rows();
            $this->load->view(ADMIN_URL.'/order',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // add order
    public function add()
    {
        if(in_array('order~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_orderadd').' | '.$this->lang->line('site_title');
            $language_slug = $this->session->userdata('language_slug');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('user_id', $this->lang->line('user'), 'trim|required');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant'), 'trim|required');
                if($this->input->post('order_mode') == 'Delivery') {
                    $this->form_validation->set_rules('address_id', $this->lang->line('address'), 'trim|required');
                    $this->form_validation->set_rules('delivery_charge', $this->lang->line('delivery_charge'), 'trim|required');
                    if($this->input->post('address_id') == 'other'){
                        $this->form_validation->set_rules('ord_address_field', $this->lang->line('add_address'), 'trim|required');
                        $this->form_validation->set_rules('ord_zipcode', $this->lang->line('postal_code'), 'trim|required');
                    }
                }
                $this->form_validation->set_rules('order_status', $this->lang->line('order_status'), 'trim|required');
                //$this->form_validation->set_rules('order_date', $this->lang->line('date_of_order'), 'trim|required');
                $this->form_validation->set_rules('total_rate', $this->lang->line('total'), 'trim|required');
                $this->form_validation->set_rules('order_mode','Order Mode', 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $items = $this->input->post('item_id');
                    $itemoutofstock = 0;
                    foreach ($items as $key => $value) {
                        $item_detail = $this->order_model->getMenuDetail($value,$language_slug,$this->input->post('restaurant_id'));
                        if(empty($item_detail)){
                            $itemoutofstock++;
                        }
                    }
                    if($itemoutofstock>0){
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('outofstock_text').'.');
                        $_SESSION['page_MSG'] = $this->lang->line('outofstock_text').'.';
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/add');  
                    }else{
                        // add address changes start
                        $address_id = ($this->input->post('address_id'))?$this->input->post('address_id'):'';
                        if($this->input->post('address_id') == 'other'){
                            
                            $address_add = array(
                                'user_entity_id'=>$this->input->post('user_id'),
                                'address'=>$this->input->post('ord_address_field'),
                                'latitude'=>$this->input->post('ord_latitude'),
                                'longitude'=>$this->input->post('ord_longitude'),
                                'city'=>$this->input->post('ord_city'),
                                'zipcode'=>$this->input->post('ord_zipcode'),
                            );
                            $address_id = $this->order_model->addData('user_address',$address_add);
                            
                        }
                        // add address changes end
                        //Code added to find the coupon name and coupon discount :: Start
                        $coupon_idArr = ($this->input->post('coupon_id'))?$this->input->post('coupon_id'):[];
                        $coupon_discount = $coupon_discountup = 0; $coupon_name = $coupon_amount = '';
                        $coupon_id = '';
                        $ordder_cpnarr = array();
                        if(!empty($coupon_idArr))
                        {
                            $subtotalCal = ($this->input->post('subtotal'))?$this->input->post('subtotal'):0;
                            foreach ($coupon_idArr as $cp_key => $cp_value)
                            {
                                $coupondtl = $this->order_model->getCouponData($cp_value);                                
                                if($coupondtl && !empty($coupondtl) && $subtotalCal>0)
                                {
                                    if($coupondtl->amount_type == 'Percentage')
                                    {
                                        $coupon_discounttemp = round(($subtotalCal * $coupondtl->amount)/100,2);
                                    }
                                    else if($coupondtl->amount_type == 'Amount')
                                    {
                                        $coupon_discounttemp = $coupondtl->amount;                                
                                    }
                                    $coupon_discountup = abs($coupon_discounttemp);
                                    if($coupondtl->coupon_type == 'free_delivery'){
                                        $coupon_discountup = ($this->input->post('delivery_charge'))?$this->input->post('delivery_charge'):0;
                                    }
                                    //Array for multiple coupon
                                    $ordder_cpnarr[] = array(
                                        'order_id'=> '',
                                        'coupon_id'=> $cp_value,
                                        'coupon_type'=>$coupondtl->amount_type,
                                        'coupon_amount'=>$coupondtl->amount,
                                        'coupon_discount'=>$coupon_discountup,
                                        'coupon_name'=>$coupondtl->name
                                    );
                                }
                                if($cp_key==0)
                                {
                                    $coupon_discount = $coupon_discountup;
                                    $coupon_name = $coupondtl->name;
                                    $coupon_amount = $coupondtl->amount;
                                    $coupon_type = $coupondtl->amount_type;
                                    $coupon_id = $cp_value;
                                }
                            } 
                        }
                        //Code added to find the coupon name and coupon discount :: End 

                        /*$coupon_type = ($this->input->post('coupon_type')=='null' || empty($this->input->post('coupon_type')))?NULL:$this->input->post('coupon_type');*/
                        $stat_for_ordermaster = ($this->input->post('order_status') == 'orderready')?'ready':$this->input->post('order_status');
                        $add_data = array(              
                            'user_id'=>$this->input->post('user_id'),
                            'restaurant_id' =>$this->input->post('restaurant_id'),
                            'address_id' =>($address_id)?$address_id:NULL,
                            'coupon_id' =>$coupon_id,
                            'order_status' =>$stat_for_ordermaster,
                            // 'order_date' =>date('Y-m-d H:i:s',strtotime($this->input->post('order_date'))),
                            'order_date' =>date('Y-m-d H:i:s'),
                            'accept_order_time' => date('Y-m-d H:i:s'),
                            'created_date' => date('Y-m-d H:i:s'),
                            'subtotal' =>($this->input->post('subtotal'))?$this->input->post('subtotal'):0.00,
                            'tax_rate'=>($this->input->post('tax_rate'))?$this->input->post('tax_rate'):0.00,
                            'tax_type'=>$this->input->post('tax_type'),
                            'total_rate' =>($this->input->post('total_rate'))?$this->input->post('total_rate'):0.00,
                            'service_fee_type' => ($this->input->post('service_fee_type')) ? $this->input->post('service_fee_type') : '',
                            'service_fee' => ($this->input->post('service_fee')) ? $this->input->post('service_fee') : '',
                            'delivery_charge'=> ($this->input->post('delivery_charge'))?$this->input->post('delivery_charge'):NULL,
                            'coupon_name'=>$coupon_name,
                            'coupon_discount'=>$coupon_discount,
                            'coupon_type'=>($coupon_type)?$coupon_type:NULL,
                            'coupon_amount'=>($coupon_amount)?$coupon_amount:0.00,
                            'created_by'=>$this->session->userdata("AdminUserID"),
                            'status'=>1,
                            'order_delivery'=>$this->input->post('order_mode'),
                            'payment_option'=> 'cod',
                            'paid_status'=> 'paid'
                        );                                           
                        $order_id = $this->order_model->addData('order_master',$add_data);

                        //Code for add the coupon value in relation table and first value add in order master table :: Start
                        if(!empty($ordder_cpnarr))
                        {
                            foreach($ordder_cpnarr as $cp_key => $cp_value)
                            {
                                $ordder_cpnarr[$cp_key]['order_id'] = $order_id;                  
                            }
                            $this->order_model->inserBatch('order_coupon_use',$ordder_cpnarr);
                        }
                        //Code for add the coupon value in relation table and first value add in order master table :: End

                        //data for order_detail
                        //item detail
                        $items = $this->input->post('item_id');
                        foreach ($items as $key => $value) {
                            $itemTotal = 0;
                            $item_detail = $this->order_model->getMenuDetail($value,$language_slug,$this->input->post('restaurant_id'));
                            if($item_detail->is_combo_item == '1'){
                                $new_item_name = $item_detail->name.'('.substr(str_replace("\r\n"," + ",$item_detail->menu_detail),0,-3).')';
                            }else{
                                $new_item_name = $item_detail->name;
                            }
                            //base price changes start
                            $itemTotal = ($item_detail->price)? $itemTotal + $item_detail->price : $itemTotal;
                            //base price changes end
                            //if customized item
                            if($item_detail->check_add_ons == '1') {
                                $add_ons_cat_id = $this->input->post('addons_id_'.$key.'['.$value.']');
                                $customization = array(); //for addons category
                                foreach ($add_ons_cat_id as $catkey => $catvalue) {
                                    $addonsCat_detail = $this->order_model->getAddonsCatDetail($catkey);
                                    $addonscust = array(); // for addons items
                                    foreach ($catvalue as $addkey => $addonvalue) {
                                        $addons_detail = $this->order_model->getAddonsDetail($addonvalue);
                                        $addonscust[] = array(
                                            'add_ons_id'=>$addonvalue,
                                            'add_ons_name'=>$addons_detail->add_ons_name,
                                            'add_ons_price'=>$addons_detail->add_ons_price
                                        );
                                        //rate*qty 
                                        $itemTotal = $itemTotal + $addons_detail->add_ons_price;
                                    }
                                    $customization[] = array(
                                        'addons_category_id'=>$catkey,
                                        'addons_category'=>$addonsCat_detail->name,
                                        'addons_list'=>$addonscust
                                    );
                                } 
                                //rate*qty 
                                $itemTotal = $itemTotal * $this->input->post('qty_no')[$key];
                                $add_item[] = array(
                                    "item_name"=>$new_item_name,
                                    "item_id"=>$item_detail->entity_id,
                                    "qty_no"=>$this->input->post('qty_no')[$key],
                                    "comment"=>$this->input->post('item_comment')[$key],
                                    "rate"=>($item_detail->price)?$item_detail->price:'',
                                    "offer_price"=>($item_detail->offer_price)?$item_detail->offer_price:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>1,
                                    "is_combo_item"=>0,
                                    "combo_item_details" => '',
                                    "itemTotal"=>$itemTotal,
                                    "order_flag"=>1,
                                    "addons_category_list"=>$customization
                                );
                            } else {
                                $itemTotal = ($this->input->post('qty_no')[$key]*$item_detail->price);
                                $add_item[] = array(
                                    "item_name"=>$new_item_name,
                                    "item_id"=>$item_detail->entity_id,
                                    "qty_no"=>$this->input->post('qty_no')[$key],
                                    "comment"=>$this->input->post('item_comment')[$key],
                                    "rate"=>($item_detail->price)?$item_detail->price:'',
                                    "offer_price"=>($item_detail->offer_price)?$item_detail->offer_price:'',
                                    "order_id"=>$order_id,
                                    "is_customize"=>0,
                                    "is_combo_item"=>$item_detail->is_combo_item,
                                    "combo_item_details"=> ($item_detail->is_combo_item == '1') ? substr(str_replace("\r\n"," + ",$item_detail->menu_detail),0,-3) : '',
                                    "itemTotal"=>$itemTotal,
                                    "order_flag"=>1
                                );
                            }
                        } 
                        //get restaurant detail
                        $rest_detail = $this->order_model->getRestaurantDetail($this->input->post('restaurant_id'));
                        //Code added to store user detail in order table :: Start
                        $userdatastr='';
                        if($this->input->post('user_id'))
                        {
                            $address_id = ($address_id)?$address_id:'';
                            $userdataArr = $this->order_model->getOrderUsers($this->input->post('user_id'), $address_id);
                            if($userdataArr && count($userdataArr))
                            {
                                $userdatastr = serialize($userdataArr);
                            }
                        }
                        //Code added to store user detail in order table :: End
                        $order_detail = array(
                            'user_name'=>($userdataArr['first_name'])?$userdataArr['first_name'].' '.$userdataArr['last_name']:'',
                            'user_mobile_number'=>($userdataArr['mobile_number'])?$userdataArr['phone_code'].$userdataArr['mobile_number']:'',
                            'order_id'=>$order_id,
                            'user_detail' => $userdatastr,
                            'item_detail' => serialize($add_item),
                            'restaurant_detail' => serialize($rest_detail),
                        ); 
                        $this->order_model->addData('order_detail',$order_detail);

                        // adding order status
                        //Code for add all remain status :: Start
                        $order_status = order_status($this->session->userdata('language_slug'));
                        unset($order_status['placed']);
                        unset($order_status['cancel']);
                        unset($order_status['rejected']);
                        if($this->input->post('order_mode') == 'PickUp')
                        {
                            unset($order_status['onGoing']);
                            unset($order_status['delivered']);
                        }
                        else
                        {
                            unset($order_status['orderready']);
                        }
                        $status_created_by = $this->session->userdata('AdminUserType');
                        foreach($order_status as $key => $value) 
                        {
                            $ord_stat = ($key=='accepted')?'accepted_by_restaurant':(($key == 'orderready')?'ready':$key); 
                            
                            $addData = array(
                                'order_id'=>$order_id,
                                'user_id'=> $this->session->userdata('AdminUserID'),
                                'order_status'=>$ord_stat,
                                'time'=>date('Y-m-d H:i:s'),
                                'status_created_by'=>$status_created_by
                            );
                            $status_id = $this->order_model->addData('order_status',$addData);

                            if($this->input->post('order_status') == $key) break;                  
                        }
                        //Code for add all remain status :: End
                        
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        if($this->input->post('order_status')=='accepted'){
                            $this->order_model->UpdatedStatus('order_master',$this->input->post('user_id'),$this->input->post('restaurant_id'),$order_id,'', $this->input->post('user_id'));
                        }
                        //Code for send the notification to the Branch admin :: Start :: 12-10-2020
                        $restuser_device = $this->order_model->getBranchAdminDevice($this->input->post('restaurant_id'));
                        if($restuser_device)
                        {
                            for($nit=0;$nit<count($restuser_device);$nit++)
                            {
                                if(trim($restuser_device[$nit]->device_id)!='' && $restuser_device[$nit]->notification == 1)
                                {
                                    $brancha_device_id = $restuser_device[$nit]->device_id;
                                    $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$restuser_device[$nit]->language_slug))->first_row();
                                    $this->lang->load('messages_lang', $languages->language_directory);
                                    #prep the bundle
                                    $fields = array();            
                                    $message = sprintf($this->lang->line('push_new_order'),$order_id);
                                    
                                    $fields['to'] = $restuser_device[$nit]->device_id; // only one user to send push notification
                                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                                    $fields['notification']['title'] = $this->lang->line('admin_app_name');
                                    $fields['data'] = array ('screenType'=>'order');
                                   
                                    $headers = array (
                                        'Authorization: key=' .FCM_KEY,
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
                        //Code for send the notification to the Branch admin :: End

                        //Code for send the notification to the Restaurant admin :: Start
                        $restadmin_device = $this->order_model->getRestaurantAdminDevice($this->input->post('restaurant_id'));
                        if($restadmin_device && trim($restadmin_device->device_id)!='' && $restadmin_device->notification == 1)
                        {
                            $brancha_device_id = $restadmin_device->device_id;
                            $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$restadmin_device->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('push_new_order'),$order_id);
                            
                            $fields['to'] = $restadmin_device->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                            $fields['notification']['title'] = $this->lang->line('admin_app_name');
                            $fields['data'] = array ('screenType'=>'order');
                           
                            $headers = array (
                                'Authorization: key=' .FCM_KEY,
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
                        //Code for send the notification to the Restaurant admin :: End
                        //notification to driver
                        // if($this->input->post('order_status') == 'accepted' && $this->input->post('order_mode') == 'Delivery'){
                        //     $this->order_model->notiToDriver($order_id,$this->input->post('restaurant_id'));
                        // }
                        //order status notification to user
                        $this->notiToUser($order_id,$this->input->post('restaurant_id'),'admin_order_created',$this->input->post('order_mode'));
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added an order - '.$order_id);
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');                 
                    }
                }
            }
            $language_slug = $this->session->userdata('language_slug');
            $data['restaurant'] = $this->order_model->getrestaurantData($language_slug);
            $data['user'] = $this->order_model->getListData('users');
            //$data['coupon'] = $this->order_model->getListData('coupon');
            $this->load->view(ADMIN_URL.'/order_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }

    //ajax view
    public function ajaxview()
    {
        $user_id = ($this->uri->segment('5'))?$this->uri->segment('5'):0; 
        $order_id = ($this->uri->segment('6') && $this->uri->segment('6')!='order_id')?$this->uri->segment('6'):0;
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $order_status = ($this->uri->segment('4') && $this->uri->segment('4')!='all')?$this->uri->segment('4'):''; 
        $sortfields = array(1=>'o.entity_id','2'=>'restaurant.name','3'=>'order_detail.user_name','4'=>'o.total_rate','5'=>'driver.first_name','6'=>'o.order_status','7'=>'o.payment_option','8'=>'o.created_date','9'=>'o.scheduled_date','10'=>'o.order_delivery'); //,'11'=>'o.delivery_method'
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }        
        //Get Recored from model
        $grid_data = $this->order_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength,$order_status,$user_id,$order_id);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        //get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();*/
        $payment_methodarr = array('stripe','paypal','applepay');
        $default_currency = get_default_system_currency();
        foreach ($grid_data['data'] as $key => $val) {

            /*$currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);*/
            $disabled = ($val->ostatus == 'delivered' || $val->ostatus == 'cancel' || $val->ostatus == 'rejected' || $val->ostatus == 'complete' || $val->ostatus == 'onGoing' || $val->ostatus == 'ready')?'disabled':'';
            $disabled_update = ($val->ostatus == 'delivered' || $val->ostatus == 'cancel' || $val->ostatus == 'rejected' || $val->ostatus == 'complete')?'disabled':''; //|| $val->refund_status=='refunded' :: remove the refund conditon
            $assignDisabled = ($val->first_name != '' || $val->last_name != '' || $val->order_delivery != "Delivery")?'disabled':'';
            $trackDriver = (($val->first_name != '' || $val->last_name != '') && $val->order_delivery == "Delivery" && $val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->ostatus != 'rejected' && in_array('order~view',$this->session->userdata("UserAccessArray")))?'<a target="_blank" href="'.base_url().ADMIN_URL.'/order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('track_driver').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-map-marker"></i></a>':'';            
            $assignDisabledStatus = ($val->status != 1)?'disabled':'';
            $ostatus = ($val->ostatus)?"'".$val->ostatus."'":'';
            $ordermode = ($val->order_delivery)?"'".strtolower($val->order_delivery)."'":'';
            $deliverymethod = ($val->delivery_method)?"'".strtolower($val->delivery_method)."'":'';
            $restaurant = ($val->restaurant_detail)?unserialize($val->restaurant_detail):'';
            $order_user_id = ($val->user_id && $val->user_id>0)?$val->user_id:0;
            $accept = ($val->status != 1 && $val->restaurant_id && $val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->ostatus != 'rejected' && in_array('order~updateOrderStatus',$this->session->userdata("UserAccessArray")))?'<button onclick="disableDetail('.$val->entity_id.','.$val->restaurant_id.','.$val->entity_id.','.$val->o_user_id.','.$ordermode.')"  title="'.$this->lang->line('accept').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-check"></i></button>':'';
            $refundstatus = ($val->refund_status=='pending' || $val->tips_refund_status=='pending')?'refunded':(($val->tips_transaction_id!='' && $val->tips_refund_status!='refunded')?'refund_needed':(($val->payment_option=='cod' && $val->tips_transaction_id=='')?'refunded':$val->refund_status));
            /*$refundstatus = ($val->tips_transaction_id!='' && $val->tips_refund_status!='refunded')?'pending':(($val->payment_option=='cod' && $val->tips_transaction_id=='')?'refunded':$val->refund_status);*/
            $reject = ($val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->status != 1 && $val->ostatus != 'rejected' && in_array('order~updateOrderStatus',$this->session->userdata("UserAccessArray")))?'<button onclick="rejectOrder('.$order_user_id.','.$val->restaurant_id.','.$val->entity_id.',\''.$refundstatus.'\')"  title="'.$this->lang->line('reject').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-times"></i></button>':'';
            //$this->session->userdata('AdminUserType')=='MasterAdmin' &&  :: Note remove this condition for refund button
            
            $currency_symbolval = ($restaurant->currency_symbol)?$restaurant->currency_symbol:$default_currency->currency_symbol;
            if(($val->refund_status=='pending' || $val->tips_refund_status=='pending')){
                $initiate_refund = '';
            }else if($val->refund_status!='refunded' && in_array(strtolower($val->payment_option), $payment_methodarr) && !empty($val->transaction_id) && in_array('order~ajaxinitiaterefund',$this->session->userdata("UserAccessArray"))) {
                $initiate_refund = '<button onclick="initiateRefund(\''.$val->entity_id.'\',\''.$val->rate.'\',\''.$val->refunded_amount.'\',\''.$currency_symbolval.'\')"  title="'.$this->lang->line('initiate_refund').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-dollar"></i></button>';
            }else if($val->tips_transaction_id!='' && $val->tips_refund_status!='refunded' && in_array('order~ajaxinitiaterefund',$this->session->userdata("UserAccessArray"))) {
                $initiate_refund = '<button onclick="initiateRefund(\''.$val->entity_id.'\',\''.$val->rate.'\',\''.$val->refunded_amount.'\',\''.$currency_symbolval.'\')"  title="'.$this->lang->line('initiate_refund').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-dollar"></i></button>';
            }else{
                $initiate_refund = '';
            }
            $edit_order = '';
            $order_type = ($val->order_delivery)?"'".$val->order_delivery."'":'';
            $updateStatus = ($val->status == 1 && ($val->delivery_method == "internal_drivers" || $val->delivery_method == "") && in_array('order~updateOrderStatus',$this->session->userdata("UserAccessArray")))?'<button onclick="updateStatus('.$val->entity_id.','.$ostatus.','.$order_user_id.','.$order_type.',\''.$refundstatus.'\',\''.$val->payment_option.'\')" '.$disabled_update.' title="'.$this->lang->line('click_change_status').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-edit"></i></button>':''; 
            //$viewComment = (!empty($val->extra_comment))?'<button onclick="viewComment('.$val->entity_id.')" title="'.$this->lang->line('view_comment').'" class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-eye"></i></button>':''; 
            $assignDriver = '';
            $reAssignDriver = '';
            if(($val->delivery_method == "internal_drivers" || $val->delivery_method == "") && $val->order_delivery == "Delivery" && $val->status == 1 && $val->first_name == '' && $order_status != "delivered" && $order_status != "complete" && $order_status != 'cancel' && $val->ostatus != 'rejected' && ($val->driver_phn_no =='' || $val->driver_phn_no =='+') && in_array('order~assignDriver',$this->session->userdata("UserAccessArray"))) {
                $assignDriver = ($val->ostatus == 'delivered' || $val->ostatus == 'cancel' || $val->ostatus == 'rejected')?'':'<button onclick="updateDriver('.$val->entity_id.','.$ostatus.','.$val->restaurant_id.')" title="'.$this->lang->line('assign_driver').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-user"></i></button>';
            } elseif (($val->delivery_method == "internal_drivers" || $val->delivery_method == "") && $val->order_delivery == "Delivery" && $val->status == 1 && $val->first_name != '' && $val->ostatus != "delivered" && $order_status != "complete" && $val->ostatus != 'cancel' && $val->ostatus != 'rejected' && in_array('order~assignDriver',$this->session->userdata("UserAccessArray")))  {
                $reassign_driver_text = "'".$this->lang->line('reassign_driver')."'";
                $reAssignDriver = '<button onclick="updateNewDriver('.$val->entity_id.','.$reassign_driver_text.','.$ostatus.','.$val->restaurant_id.',\''.$val->driver_id.'\')" title="'.$this->lang->line('reassign_driver').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-user"></i></button>';
            } else {
                $assignDriver = '';
                $reAssignDriver = '';
            }
            $show_editbtn = 'yes';
            if($val->ostatus == "placed" && $val->status!='1')
            {
                $ostatuslng = $this->lang->line('placed');
                $initiate_refund = '';
                $show_editbtn = 'no';
            }
            if(($val->ostatus == "placed" && $val->status=='1') || $val->ostatus == "accepted")
            {
                $ostatuslng = $this->lang->line('accepted');
            }
            if($val->ostatus == "rejected")
            {
                $ostatuslng = $this->lang->line('rejected');
            }
            if($val->ostatus == "delivered"){
                $ostatuslng = $this->lang->line('delivered');
            }
            if($val->ostatus == "onGoing")
            {
                $ostatuslng = $this->lang->line('onGoing');
                if($val->order_delivery == "PickUp")
                {
                    $ostatuslng = $this->lang->line('order_ready');
                }
            }
            
            if($val->ostatus == "cancel"){
                $ostatuslng = $this->lang->line('cancel');
            }
            if($val->ostatus == "ready"){
                $ostatuslng = $this->lang->line('order_ready');
            }
            if($val->ostatus == "complete"){
                $ostatuslng = $this->lang->line('complete');
            }
            // if($val->ostatus == "preparing"){
            //     $ostatuslng = $this->lang->line('preparing');
            // }
            if($val->ostatus == "pending"){
                $ostatuslng = $this->lang->line('pending');
            }
            if($val->order_delivery == "Delivery"){
                $order_delivery = $this->lang->line('delivery_order');
            }
            if($val->order_delivery == "PickUp"){
                $order_delivery = $this->lang->line('pickup');
            }            
            //$payment_option = ($val->payment_option)?(($val->payment_option=='cod')?$this->lang->line('cod_display'):$this->lang->line('mobilpay')):'';
            if($val->payment_option=='cod' && $val->tips_transaction_id!='' && $val->tips_refund_status=='refunded'){
                $payment_option = $this->lang->line('cod_display').'<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">'.$this->lang->line('cod_initiate_refunded').'</span>';
            }else if($val->payment_option=='cod' && $val->tips_transaction_id!=''){
                $payment_option = $this->lang->line('cod_display')."<br>".$this->lang->line('cod_initiate');
            }else if($val->payment_option=='cod'){
                $payment_option = $this->lang->line('cod_display');
            }
            $is_showeditbutton = 'yes'; $is_showrefundedby = 'no';
            if($val->payment_option == 'stripe' && $val->transaction_id != '' && $val->refund_status == 'refunded') {
                $payment_option = 'Stripe<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                $is_showeditbutton = 'no';
                $is_showrefundedby = 'yes';
            } else if($val->payment_option == 'stripe' && $val->transaction_id != '' && $val->refund_status == 'partial refunded') {
                $payment_option = 'Stripe<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(partial refunded)</span>';
                $is_showrefundedby = 'yes'; 
                $is_showeditbutton = 'no';
            } else if($val->payment_option=='stripe' && $val->tips_transaction_id!='' && $val->tips_refund_status=='refunded'){
                $payment_option = 'Stripe<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                $is_showeditbutton = 'no';
                $is_showrefundedby = 'yes';
            }else if($val->payment_option=='stripe' && $val->tips_transaction_id=='' && $val->refund_status=='refunded'){
                $payment_option = 'Stripe<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                $is_showeditbutton = 'no';
                $is_showrefundedby = 'yes';
            }else if($val->payment_option=='stripe' && $val->stripe_refund_id!='' && $val->refund_status=='partial refunded'){
                $payment_option = 'Stripe<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(partial refunded)</span>';
                $is_showrefundedby = 'yes'; 
                $is_showeditbutton = 'no';               
            }else if($val->payment_option=='stripe'){
                $payment_option = 'Stripe';
            }
            //Code Apple Pay :: Start
            if($val->payment_option=='applepay' && $val->tips_transaction_id!='' && $val->tips_refund_status=='refunded'){
                $payment_option = 'Apple Pay<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                $is_showeditbutton = 'no';
                $is_showrefundedby = 'yes';
            }else if($val->payment_option=='applepay' && $val->tips_transaction_id=='' && $val->refund_status=='refunded'){
                $payment_option = 'Apple Pay<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                $is_showeditbutton = 'no';
                $is_showrefundedby = 'yes';
            }else if($val->payment_option=='applepay' && $val->stripe_refund_id!='' && $val->refund_status=='partial refunded'){
                $payment_option = 'Apple Pay<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(partial refunded)</span>';
                $is_showrefundedby = 'yes';
                $is_showeditbutton = 'no';                
            }else if($val->payment_option=='applepay'){
                $payment_option = 'Apple Pay';
            }
            //Code Apple Pay :: End
            if(strtolower($val->payment_option)=='paypal')
            {
                $payment_option = 'Paypal';
                if($val->refund_status=='refunded')
                {
                    $payment_option .= '<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(refunded)</span>';
                    $is_showeditbutton = 'no';
                    $is_showrefundedby = 'yes';
                }
                else if($val->refund_status=='partial refunded' && $val->stripe_refund_id!='')
                {
                    $payment_option .= '<br><span style="pointer-events: none;border:0px;color:#d9214e;font-weight:900;">(partial refunded)</span>'; 
                    $is_showrefundedby = 'yes';
                    $is_showeditbutton = 'no';                   
                }
            }

            //Code for display order updated by :: Start            
            $admin_namedis = '';
            if($val->adminf_name && $val->adminf_name!='' && $val->adminf_name!=null && $is_showrefundedby=='yes')
            {
                $admin_namedis = ($val->adminl_name && $val->adminl_name!='' && $val->adminl_name!=null)?$val->adminf_name.' '.$val->adminl_name:$val->adminf_name;
                $payment_option .= '<br> (Refunded by '.ucwords($admin_namedis).')'; 
            }
            //End

            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_order')),$val->entity_id)."'";
            $delete_order_btn = (in_array('order~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->entity_id.','.$msgDelete.','.$ordermode.','.$deliverymethod.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $getInvoicebtn = '';
            if($val->ostatus != 'cancel' && $val->ostatus != 'rejected')
            {
                if($val->is_printer_available==1 && in_array('order~getInvoice',$this->session->userdata("UserAccessArray"))){
                    $getInvoicebtn = '<button onclick="getInvoice('.$val->entity_id.')"  title="'.$this->lang->line('download_invoice').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-file-text"></i></button>';
                }
            }
            $print_receipt_btn = '';
            if($val->ostatus != 'cancel' && $val->ostatus != 'rejected')
            {
                if($val->is_printer_available==1 && in_array('order~print_receipt',$this->session->userdata("UserAccessArray"))){
                    $print_receipt_btn = '<button class="btn btn-sm danger-btn theme-btn default-btn margin-bottom-5" onclick="printReceipt('.$val->entity_id.')" title="'.$this->lang->line('print_receipt').'"><i class="fa fa-print"></i></button>';    
                }
            }
            $driver_phn_no = ($val->dphone_code && $val->dmobile_number)?('(+'.$val->dphone_code.$val->dmobile_number.')'):($val->dmobile_number?'('.$val->dmobile_number.')':'');

            $user_phn_no = ($val->phone_code && $val->mobile_number)?('(+'.$val->phone_code.$val->mobile_number.')'):($val->mobile_number?'('.$val->mobile_number.')':''); //user's table
            $user_order_phn_no = ($val->user_mobile_number)?'(+'.$val->user_mobile_number.')':''; //order detail table

            $user_name = ($val->fname)?$val->fname.' '.$val->lname:''; //user's table
            $order_user_name = ($val->user_name)?$val->user_name:''; //order detail table

            $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$val->scheduled_date $val->slot_open_time"));
            $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$val->scheduled_date $val->slot_close_time"));
            $order_scheduled_date = ($val->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
            $order_slot_open_time = ($val->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
            $order_slot_close_time = ($val->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;

            //delayed order changes :: start
            if($val->is_delayed == 0 && strtolower($val->order_delivery)!='dinein') {
                $markdelayedflag = 0;
                $compare_time_chk = ($val->check_status_time)?date('Y-m-d H:i:s',strtotime($val->check_status_time)):date('Y-m-d H:i:s');
                $compare_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($compare_time_chk)));

                if($val->scheduled_date && $val->slot_close_time) {
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_close_time"));
                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($compare_time_chk);

                    if($scheduleddatetime <= $currentdatetime) {
                        $markdelayedflag = 1;                          
                        $order_date_chk = $combined_scheduled_date;
                    }
                } elseif($val->scheduled_date=='' || $val->slot_close_time=='') {
                    $markdelayedflag = 1;                      
                    $order_date_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($val->order_date)));
                }
                if($markdelayedflag == 1) { 
                    //difference between current datetime and order datetime
                    $datetime_diff = $this->common_model->dateDifference($order_date_chk , $compare_time_chk);
                    $diff_year = $datetime_diff['diff_year'];
                    $diff_month = $datetime_diff['diff_month'];
                    $diff_day = $datetime_diff['diff_day'];
                    $diff_hr = $datetime_diff['diff_hr'];
                    $diff_min = $datetime_diff['diff_min'];
                    //convert system option auto cancellation minutes to compare
                    $convert_mins = $this->common_model->convertMinutes('check_delayed');
                    $compare_year = $convert_mins['compare_year'];
                    $compare_month = $convert_mins['compare_month'];
                    $compare_day = $convert_mins['compare_day'];
                    $compare_hour = $convert_mins['compare_hour'];
                    $compare_minute = $convert_mins['compare_minute'];

                    if($diff_year > $compare_year || $diff_month > $compare_month || $diff_day > $compare_day || $diff_hr > $compare_hour || $diff_min > $compare_minute) {
                        $this->db->set('is_delayed',1)->where('entity_id',$val->entity_id)->update('order_master');
                        $val->is_delayed = 1;
                    }
                }
            }            
            $delayed_label = ($val->is_delayed == 1)?'<br><button style="pointer-events: none;border:0px;color:white;background:#d9214e;font-weight:900;">'.$this->lang->line('delayed').'</button>':'';
            //delayed order changes :: end
            
            if($is_showeditbutton=='no' || $show_editbtn == 'no'){
                $disabled = 'disabled';
            }
            $edit_order = (($val->delivery_method == "internal_drivers" || $val->delivery_method == "") && in_array('order~edit_delivery_pickup_order_details',$this->session->userdata("UserAccessArray")))?'<a class="btn btn-sm default-btn margin-bottom red cart-btn" '.$disabled.' href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_delivery_pickup_order_details/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('edit').'" onclick="readDetail('.$val->entity_id.')" data-id="'.$val->entity_id.'"><i class="fa fa-cart-plus"></i></a>&nbsp;':'';

            $view_order_details_btn = (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button class="btn btn-sm  default-btn danger-btn theme-btn margin-bottom-5" onclick="openOrderDetails('.$val->entity_id.')" title="'.$this->lang->line('order_details').'"><i class="fa fa-eye"></i></button>' : '';
            $view_status_history_btn = (in_array('order~view',$this->session->userdata("UserAccessArray"))) ? '<button onclick="statusHistory('.$val->entity_id.')" title="'.$this->lang->line('status_history').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-history"></i></button>' : '';

            //Code for display order updated by :: Start
            $order_dispid = $val->entity_id;            
            if(trim($admin_namedis)!='')
            {
                $order_dispid .= '<br> (Updated by '.ucwords($admin_namedis).')'; 
            }
            //End

            $records["aaData"][] = array(
                '<input type="checkbox" name="ids[]" value="'.$val->entity_id.'">',
                $order_dispid, //$val->entity_id,
                ($restaurant)?$restaurant->name:$val->name,
                ($order_user_name)?$order_user_name.' '.$user_order_phn_no:'Order by Restaurant',
                ($val->rate) ? (!empty($default_currency)) ? currency_symboldisplay(number_format_unchanged_precision($val->rate,$default_currency->currency_code),$default_currency->currency_symbol) : currency_symboldisplay(number_format_unchanged_precision($val->rate,$restaurant->currency_code),$restaurant->currency_symbol) : '',
                $val->first_name.' '.$val->last_name.' '.$driver_phn_no,
                $ostatuslng.$delayed_label,
                // ($val->payment_option=='cod')?$payment_option:$payment_option.'<br>'.'('.$val->payment_status.')',
                //($val->payment_option)?$val->payment_option:'',
                $payment_option,
                ($val->created_date)?$this->common_model->getZonebaseDateMDY($val->created_date):'',
                ($order_scheduled_date && $order_slot_open_time && $order_slot_close_time) ? $this->common_model->dateFormat($order_scheduled_date).' <br>('.$this->common_model->timeFormat($order_slot_open_time).' - '.$this->common_model->timeFormat($order_slot_close_time).')' : '-',
                $order_delivery,
                //($val->status == 1 && $val->order_delivery == "Delivery" && ($val->delivery_method == "internal_drivers" || $val->delivery_method == "")) ? $this->lang->line('internal_drivers') : (($val->status == 1 && $val->order_delivery == "Delivery" && ($val->delivery_method == "doordash" || $val->delivery_method == "relay"))? $this->lang->line('thirdparty_delivery') : '-'),
                $view_order_details_btn.$edit_order.$accept.$reject.$updateStatus.$initiate_refund.$delete_order_btn.$view_status_history_btn.$assignDriver.''.$reAssignDriver.''.$trackDriver.''.$getInvoicebtn.$print_receipt_btn.''
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function track_order(){
        $data['meta_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
        $order_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment('4'))):'';
        if (!empty($order_id)) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($order_id);
            $data['order_id'] = $order_id;
            $this->load->view(ADMIN_URL.'/track_order',$data);
        } 
    }
    // ajax track user's order
    public function ajax_track_order(){
        $data['meta_title'] = $this->lang->line('track_order').' | '.$this->lang->line('site_title');
        $data['latestOrder'] = array();
        if (!empty($this->input->post('order_id'))) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($this->input->post('order_id'));
        }
        $data['order_id'] = $this->input->post('order_id');
        $this->load->view(ADMIN_URL.'/ajax_track_order',$data);
    }
    // updating status to reject a order
    public function ajaxReject() { 
        $user_id = ($this->input->post('user_id') != '')?$this->input->post('user_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $order_id = ($this->input->post('order_id') != '')?$this->input->post('order_id'):'';
        $reject_reason = (!empty($this->input->post('other_reject_reason'))) ? $this->input->post('other_reject_reason') : $this->input->post('reject_reason');
        $response = array('error'=>'');
        if($restaurant_id && $order_id && $reject_reason){
            $payment_methodarr = array('stripe','paypal','applepay');
            $data['order_records'] = $this->order_model->getEditDetail($order_id);
            //stripe refund amount
            if($data['order_records']->refund_status!='pending' && $data['order_records']->tips_refund_status!='pending'){
                if(($data['order_records']->transaction_id!='' && in_array(strtolower($data['order_records']->payment_option), $payment_methodarr) && $data['order_records']->refund_status!='refunded') || ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status!='refunded')){
                    $transaction_id = ($data['order_records']->transaction_id!='' && ($data['order_records']->refund_status=='' || strtolower($data['order_records']->refund_status)=='partial refunded'))?$data['order_records']->transaction_id:'';
                    $tips_transaction_id = ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status=='')?$data['order_records']->tips_transaction_id:'';

                    $tip_payment_option = ($data['order_records']->tip_payment_option!='' && $data['order_records']->tip_payment_option!=null)?$data['order_records']->tip_payment_option:'';
                    if($tip_payment_option=='' && $tips_transaction_id!='')
                    {
                        $tip_payment_option = 'stripe';
                    }

                    $refund_reason = $reject_reason;
                    if(strtolower($data['order_records']->payment_option)=='stripe' || strtolower($data['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                    {
                        $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                    }
                    else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                    {   
                        $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                    }

                    //Mail send code Start
                    if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                    {
                        $language_slug = $this->session->userdata('language_slug');
                        $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                        $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
                    }
                    //Mail send code End

                    if(in_array(strtolower($data['order_records']->payment_option), $payment_methodarr))
                    {
                        //Code for save updated by and date value 
                        $update_array = array(
                            'updated_by' => $this->session->userdata("AdminUserID"),
                            'updated_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->set($update_array)->where('entity_id',$order_id)->update('order_master');
                        //Code for save updated by and date value
                    }
                }
            }
            $reject_array = array(
                'order_status' => 'rejected',
                'reject_reason' => $reject_reason
            );
            $this->db->set($reject_array)->where('entity_id',$order_id)->update('order_master');

            /*wallet changes start*/
            if($user_id && $user_id>0){
                //if order is cancelled both debit and credit should be removed from wallet history
                $users_wallet = $this->order_model->getUsersWalletMoney($user_id);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $credit_walletDetails = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                $credit_amount = $credit_walletDetails->amount;
                $debit_walletDetails = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                $debit_amount = $debit_walletDetails->amount;
                $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                //delete order_id from wallet history and update users wallet
                if(!empty($credit_amount) || !empty($debit_amount)){
                    $this->order_model->deletewallethistory($order_id); // delete by order id
                    $new_wallet = array(
                        'wallet'=>$new_wallet_amount
                    );
                    $this->order_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                }
            }
            /*wallet changes end*/
            $status_created_by = $this->session->userdata('AdminUserType');
            $addData = array(
                'order_id'=>$order_id,
                'user_id'=> $this->session->userdata('AdminUserID'),
                'order_status'=>'rejected',
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>$status_created_by
            );
            $this->order_model->addData('order_status',$addData);
            if($user_id && $user_id>0){            
                //website notification :: start
                $web_notification = array(
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'notification_slug' => 'order_rejected',
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_order_notification',$web_notification);
                //website notification :: end
                //app notification
                $userdata = $this->order_model->getUserDate($user_id);
                $device = $this->order_model->getDevice($user_id);
                if($device->notification == 1){
                    $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    /*if order is rejected then append reason*/
                    $message = sprintf($this->lang->line('push_order_rejected'),$order_id).'-'.$reject_reason;
                    $device_id = $device->device_id;
                    // Send Latest wallet balance
                    $users_wallet = $this->order_model->getUsersWalletMoney($user_id);
                    $latest_wallet_balance = $users_wallet->wallet; 
                    $this->sendFCMRegistration($device_id, $message, 'rejected', $restaurant_id, FCM_KEY,'','',$order_id,'',$latest_wallet_balance);
                }
                //send refund noti to user
                if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                    $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                }

                $langslugval = ($device->language_slug) ? $device->language_slug : '';
                $useridval = ($user_id && $user_id > 0) ? $user_id : 0;
                $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$order_id,$this->session->userdata('AdminUserType'));
                
            }
            $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
            if($order_detail->agent_id){
                $this->common_model->notificationToAgent($order_id, 'rejected');
            }
            if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
                $response['error_message'] = $this->lang->line('admin_refund_failed');
            }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
                $response['error_message'] = $this->lang->line('admin_refund_canceled');
            }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
                $response['error_message'] = $this->lang->line('refund_pending_err_msg');
            }
            echo json_encode($response);
        }
    }
    // assign driver
    public function assignDriver()
    { 
        if (!empty($this->input->post('order_entity_id')) && !empty($this->input->post('driver_id')))
        {
            if ($this->input->post('is_driver_assigned') == 1)
            {
                $updateDriver = array('driver_id'=>$this->input->post('driver_id'));
                $this->order_model->updateData($updateDriver,'order_driver_map','order_id',$this->input->post('order_entity_id'));
                $this->order_model->updateData($updateDriver,'tips','order_id',$this->input->post('order_entity_id'));
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' reassigned driver for order - '.$this->input->post('order_entity_id'));
            }
            else
            {
                $assign_details = $this->order_model->getRecordMultipleWhere('order_driver_map',array('order_id' => $this->input->post('order_entity_id'),'is_accept' => 1));

                if(empty($assign_details)){
                    $distance = $this->order_model->getOrderDetails($this->input->post('order_entity_id'));
                    $comsn = 0;
                    //check if commission of driver is enabled in system options
                    $this->db->select('OptionValue');
                    $is_enabled = $this->db->get_where('system_option',array('OptionSlug'=>'enable_commission_of_driver'))->first_row();
                    $is_enabled = $is_enabled->OptionValue;
                    if($is_enabled == 1){
                        if($distance[0]->distance > 3){
                            $this->db->select('OptionValue');
                            $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_more'))->first_row();
                            $comsn = $comsn->OptionValue;
                        }else{
                            $this->db->select('OptionValue');
                            $comsn = $this->db->get_where('system_option',array('OptionSlug'=>'driver_commission_less'))->first_row(); 
                            $comsn = $comsn->OptionValue;
                        }
                    } else {
                        $comsn = $distance[0]->delivery_charge;
                    }
                    //Delete order dirver relation before assign
                    $this->order_model->DelOrderbeforAssign($this->input->post('order_entity_id'),$this->input->post('driver_id'));
                    $order_detail = array(
                        'driver_commission'=>$comsn,
                        'commission'=>$comsn,
                        'distance'=>$distance[0]->distance,
                        'driver_id'=>$this->input->post('driver_id'),
                        'order_id'=>$this->input->post('order_entity_id'),
                        'is_accept'=>1
                    );
                    $driver_map_id = $this->order_model->addData('order_driver_map',$order_detail);
                    
                    //driver tip changes :: start
                    $add_driver = array('driver_id'=>$this->input->post('driver_id'));
                    $this->order_model->updateData($add_driver,'tips','order_id',$this->input->post('order_entity_id'));
                    //driver tip changes :: end
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' assigned driver for order - '.$this->input->post('order_entity_id'));
                    
                    //if (!empty($driver_map_id)) {
                        // after assigning a driver need to update the order status
                        //if($this->input->post('current_order_status') == 'placed' || $this->input->post('current_order_status') == 'accepted'){
                            // $order_status = "preparing";
                            // $this->db->set('order_status',$order_status)->where('entity_id',$this->input->post('order_entity_id'))->update('order_master');
                            //$status_created_by = $this->session->userdata('AdminUserType');
                            // $addData = array(
                            //     'order_id'=>$this->input->post('order_entity_id'),
                            //     'user_id'=> $this->session->userdata('AdminUserID'),
                            //     'order_status'=>$order_status,
                            //     'time'=>date('Y-m-d H:i:s'),
                            //     'status_created_by'=>$status_created_by
                            // );
                            // $order_id = $this->order_model->addData('order_status',$addData);

                            // adding notification for website
                            //$order_status = 'order_preparing';
                            //$order_detail = $this->common_model->getSingleRow('order_master','entity_id',$this->input->post('order_entity_id'));
                            //if ($order_detail->user_id && $order_detail->user_id>0 && $order_status != '') {
                                // $notification = array(
                                //     'order_id' => $this->input->post('order_entity_id'),
                                //     'user_id' => $order_detail->user_id,
                                //     'notification_slug' => $order_status,
                                //     'view_status' => 0,
                                //     'datetime' => date("Y-m-d H:i:s"),
                                // );
                                // $this->common_model->addData('user_order_notification',$notification);
                                //notification to user
                                //$device = $this->order_model->getDevice($order_detail->user_id);
                                // if($device->notification == 1){
                                //     $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                                //     $this->lang->load('messages_lang', $languages->language_directory);
                                //     $message = $this->lang->line($order_status);
                                //     $device_id = $device->device_id;
                                //     $restaurant = $this->order_model->orderDetails($this->input->post('order_entity_id'));
                                //     $this->sendFCMRegistration($device_id,$message,'preparing',$restaurant[0]->restaurant_id,FCM_KEY);
                                // }
                            //}
                        //}
                    //}
                } else {
                    //assigned to other driver
                    $result_arr = array('result' => 'already_assigned', "message"=>$this->lang->line('already_accepted_by_driver'),"oktext"=>$this->lang->line('ok') );
                    echo json_encode($result_arr); exit;
                }
            }
            if (!empty($this->input->post('driver_id'))) {
                //notification to driver
                $device = $this->order_model->getDevice($this->input->post('driver_id'));
                if($device->device_id!='' && $device->notification == 1){
                    //get langauge
                    $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    #prep the bundle
                    $fields = array();            
                    $message = sprintf($this->lang->line('order_assigned'),$this->input->post('order_entity_id'));
                    $fields['to'] = $device->device_id; // only one user to send push notification
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
                $result_arr = array('result' => 'success', "message"=>'',"oktext"=>'');
                echo json_encode($result_arr); exit;
            }
        }
    }
    // view comment
    public function viewComment(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
            $comment = $this->order_model->getOrderComment($entity_id);
            echo ($comment[0]->extra_comment?$comment[0]->extra_comment:'');
        }
    }
    // updating status and send request to driver
    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $order_id = ($this->input->post('order_id') != '')?$this->input->post('order_id'):'';
        $status = ($this->input->post('status') != '')?$this->input->post('status'):'';
        //$dine_in = ($this->input->post('dine_in'))?$this->input->post('dine_in'):'';
        $orders_user_id = $this->input->post('orders_user_id');
        if($entity_id != '' && $restaurant_id != '' && $order_id != '')
        {
            $this->db->set('is_updateorder','0')->where('entity_id',$order_id)->update('order_detail');
            $this->db->set('accept_order_time',date('Y-m-d H:i:s'))->where('entity_id',$order_id)->update('order_master');
            $this->order_model->UpdatedStatus('order_master',$entity_id,$restaurant_id,$order_id,'',$orders_user_id);
            // adding order status
            $status_created_by = $this->session->userdata('AdminUserType');
            $addData = array(
                'order_id'=>$order_id,
                'user_id'=> $this->session->userdata('AdminUserID'),
                'order_status'=>'accepted_by_restaurant',
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>$status_created_by
            );
            $status_id = $this->order_model->addData('order_status',$addData);
            // adding notification for website
            $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
            if($order_detail->user_id && $order_detail->user_id > 0){
                $notification = array(
                    'order_id' => $order_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => 'order_accepted',
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_order_notification',$notification);
            }
            if($order_detail->agent_id){
                $this->common_model->notificationToAgent($order_id, 'accepted');
            }
            //add user log
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' accepted Order - '.$order_id);
        }
    }
    public function ajaxdisable_for_deliveryorders()
    {
        $delivery_method_error = array('error' => '');
        $entity_id = ($this->input->post('accept_entity_id') != '')?$this->input->post('accept_entity_id'):'';
        $restaurant_id = ($this->input->post('accept_restaurant_id') != '')?$this->input->post('accept_restaurant_id'):'';
        $order_id = ($this->input->post('accept_order_id') != '')?$this->input->post('accept_order_id'):'';
        $orders_user_id = $this->input->post('orders_user_id');
        $choose_delivery_method = $this->input->post('choose_delivery_method');
        if($entity_id != '' && $restaurant_id != '' && $order_id != '')
        {
            $delivery_method_resp_arr = $this->order_model->UpdatedStatusForDeliveryOrders('order_master',$entity_id,$restaurant_id,$order_id,'',$orders_user_id,$choose_delivery_method); //delivery_method_resp_arr to check delivery method errors
            if($delivery_method_resp_arr['is_available'] == 'yes') {
                $this->db->set('is_updateorder','0')->where('entity_id',$order_id)->update('order_detail');
                $this->db->set('accept_order_time',date('Y-m-d H:i:s'))->where('entity_id',$order_id)->update('order_master');
                // adding order status
                $status_created_by = $this->session->userdata('AdminUserType');
                $addData = array(
                    'order_id'=>$order_id,
                    'user_id'=> $this->session->userdata('AdminUserID'),
                    'order_status'=>'accepted_by_restaurant',
                    'time'=>date('Y-m-d H:i:s'),
                    'status_created_by'=>$status_created_by
                );
                $status_id = $this->order_model->addData('order_status',$addData);
                // adding notification for website
                $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
                if($order_detail->user_id && $order_detail->user_id > 0){
                    $notification = array(
                        'order_id' => $order_id,
                        'user_id' => $order_detail->user_id,
                        'notification_slug' => 'order_accepted',
                        'view_status' => 0,
                        'datetime' => date("Y-m-d H:i:s"),
                    );
                    $this->common_model->addData('user_order_notification',$notification);
                }
                if($order_detail->agent_id){
                    $this->common_model->notificationToAgent($order_id, 'accepted');
                }
            } else {
                $delivery_method_error = array(
                    'error' =>($delivery_method_resp_arr['error'])?$this->lang->line('thirdparty_api_errors'): $this->lang->line('delivery_not_available_via_thirdparty')
                );
            }
        }
        echo json_encode($delivery_method_error);
    }
    public function getResDeliveryMethods()
    {
        $return = array('check_thirdparty_available' => 'no');
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        if(!empty($restaurant_id)){
            $restaurant_content_id = $this->order_model->getResContentId($restaurant_id);
            //check if delivery method added for this restaurant.
            $chk_res_delivery_method = $this->order_model->check_delivery_method_map($restaurant_content_id);
            if(!empty($chk_res_delivery_method)){
                $return = array('check_thirdparty_available' => 'yes');
            }
        }
        echo json_encode($return);
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $data['order_records'] = $this->order_model->getEditDetail($entity_id);
        if(!empty($data['order_records']->invoice)) {
            @unlink(FCPATH.'uploads/'.$data['order_records']->invoice);
        }
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted order - '.$entity_id);
        $this->order_model->ajaxDelete('order_master',$entity_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    //delete third party delivery orders
    public function ajaxDeleteThirdpartyDeliveryOrders()
    {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $delivery_method = ($this->input->post('delivery_method') != '')?$this->input->post('delivery_method'):'';
        //thirdparty cancel order :: start
        $thirdparty_cancel_resp = array('status'=>'1', 'error'=>'');
        if($entity_id)
        {
            if($delivery_method == 'doordash'){
                $thirdparty_cancel_resp = $this->common_model->doordash_cancel_order($entity_id);
            } else if($delivery_method == 'relay'){
                $thirdparty_cancel_resp = $this->common_model->relay_cancel_order($entity_id);
            }
        }
        //thirdparty cancel order :: end
        if($thirdparty_cancel_resp['status'] == '1'){
            $data['order_records'] = $this->order_model->getEditDetail($entity_id);
            if(!empty($data['order_records']->invoice)) {
                @unlink(FCPATH.'uploads/'.$data['order_records']->invoice);
            }
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted order - '.$entity_id);
            $this->order_model->ajaxDelete('order_master',$entity_id);
            // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
            $_SESSION['page_MSG'] = $this->lang->line('success_delete');
        } else {
            //display thirdparty error.
            if($thirdparty_cancel_resp['status'] == '0'){
                $arrResponse = array();
                $arrResponse['status'] = "thirdparty_cancel_error";
                $arrResponse['status_message']  = $this->lang->line('thirdparty_cancel_error');
                echo json_encode($arrResponse);
                exit;
            }
        }
    }
    //get item of restro
    public function getItem(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
            $data =  $this->order_model->getItem($entity_id);
            $Menucategory =  $this->order_model->getItemCategory($entity_id);
            $html = '<option value="">'.$this->lang->line('select').'</option>';
            //$categories = $this->order_model->getCategories();          
            foreach ($data as $key => $value) {
                $html .='<optgroup label="'.htmlentities($Menucategory[$key]->cat_name).'">';
                foreach ($value as $ky => $var) {
                    $html .='<option value="'.$var->entity_id.'" data-id="'.$var->price.'" data-addOns="'.$var->check_add_ons.'">'.$var->name.'</option>';
                }
                $html .='</optgroup>';
            }
        }
        echo $html;
    }
    //get address
    public function getAddress(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id){
            $result =  $this->order_model->getAddress($entity_id);
            $html = '<option value="">'.$this->lang->line('select').'</option>';
            foreach ($result as $key => $value) {
                //$html .= '<option value="'.$value->entity_id.'">'.$value->address.' , '.$value->city.' , '.$value->state.' , '.$value->country.' '.$value->zipcode.'</option>';
                $html .= '<option value="'.$value->entity_id.'">'.$value->address.'</option>';
            }
            $html .= '<option value="other">'.$this->lang->line('other').'</option>';
        }
        echo $html;
    }
    //create invoice
    public function getInvoice(){
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):'';
        $data['order_records'] = $this->order_model->getEditDetail($entity_id);
        $data['coupon_array'] = $this->common_model->getCoupon_array($entity_id);
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
        $data['wallet_history'] = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $entity_id,'debit' => 1, 'is_deleted'=>0));
        $html = $this->load->view('backoffice/order_invoice',$data,true);              
        if (!@is_dir('uploads/invoice')) {
          @mkdir('./uploads/invoice', 0777, TRUE);
        } 
        $filepath = 'uploads/invoice/'.$entity_id.'.pdf';
        $this->load->library('M_pdf'); 
        //$mpdf=new mPDF('','Letter'); 
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetHTMLHeader('');
        $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">'.$this->lang->line('signature').' ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #cccccc;"><img src="'.base_url().'assets/admin/img/logo.png" alt="" width="80" /></div></body></html>');
        $mpdf->AddPage('', // L - landscape, P - portrait 
            '', '', '', '',
            0, // margin_left
            0, // margin right
            10, // margin top
            23, // margin bottom
            0, // margin header
            0 //margin footer
        );
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        //$mpdf->SetAutoFont();
        $mpdf->WriteHTML($html);
        $mpdf->output($filepath,'F');
        echo $filepath;
    }
    //add status
    public function updateOrderStatus()
    {
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):''; 
        $order_status = ($this->input->post('order_status'))?$this->input->post('order_status'):''; 
        $user_id = ($this->input->post('user_id'))?$this->input->post('user_id'):'';
        $order_typeval = ($this->input->post('order_type'))?$this->input->post('order_type'):'';
        $order_statusval = ($this->input->post('order_statusval'))?$this->input->post('order_statusval'):'';
        $driver_id = '';
        //thirdparty cancel order :: start
        $thirdparty_cancel_resp = array('status'=>'1', 'error'=>'');
        if($entity_id && $order_status)
        {
            $userorderdetail = $this->order_model->getRecord('order_master','entity_id',$entity_id);
            if($userorderdetail->delivery_method == 'doordash' && strtolower($this->input->post('order_status')) == 'cancel'){
                $thirdparty_cancel_resp = $this->common_model->doordash_cancel_order($entity_id);
            } else if($userorderdetail->delivery_method == 'relay' && strtolower($this->input->post('order_status')) == 'cancel') {
                $thirdparty_cancel_resp = $this->common_model->relay_cancel_order($entity_id);
            }
        }
        //thirdparty cancel order :: end
        if($entity_id && $order_status && $thirdparty_cancel_resp['status'] == '1')
        {
            // Start - check the order status already changed
            if($userorderdetail->order_status == 'delivered' || $userorderdetail->order_status == 'cancel' || $userorderdetail->order_status == 'rejected' || $userorderdetail->order_status == 'complete'){
                $arrResponse = array();
                $arrResponse['status'] = "order_status_already_changed";
                $arrResponse['status_message']  = sprintf($this->lang->line("order_status_already_changed_to"),$this->lang->line($userorderdetail->order_status));
                echo json_encode($arrResponse);
                exit;
            }
            // End - check the order status already changed
            // Start - stripe refund code
            $data['order_records'] = $this->order_model->getEditDetail($entity_id);
            $response = array('error'=>'');
            if($this->input->post('order_status') == 'cancel'){
                $payment_methodarr = array('stripe','paypal','applepay');
                //stripe refund amount
                if($data['order_records']->refund_status!='pending' && $data['order_records']->tips_refund_status!='pending'){
                    if(($data['order_records']->transaction_id!='' && in_array(strtolower($data['order_records']->payment_option), $payment_methodarr) && $data['order_records']->refund_status!='refunded') || ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status!='refunded')){
                        $transaction_id = ($data['order_records']->transaction_id!='' && ($data['order_records']->refund_status=='' || strtolower($data['order_records']->refund_status)=='partial refunded'))?$data['order_records']->transaction_id:'';
                        $tips_transaction_id = ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status=='')?$data['order_records']->tips_transaction_id:'';

                        $tip_payment_option = ($data['order_records']->tip_payment_option!='' && $data['order_records']->tip_payment_option!=null)?$data['order_records']->tip_payment_option:'';
                        if($tip_payment_option=='' && $tips_transaction_id!='')
                        {
                            $tip_payment_option = 'stripe';
                        }
                        $refund_reason = (!empty($this->input->post('other_reason'))) ? $this->input->post('other_reason') : $this->input->post('cancel_reason');

                        $response['error']='';
                        if(strtolower($data['order_records']->payment_option)=='stripe' || strtolower($data['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                        {
                            $response = $this->common_model->StripeRefund($transaction_id,$entity_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                        }
                        else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                        {   
                            $response = $this->common_model->PaypalRefund($transaction_id,$entity_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,'full',0);
                        }

                        //Mail send code Start
                        if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                        {
                            $language_slug = $this->session->userdata('language_slug');
                            $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                            $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
                        }
                        //Mail send code End

                        if(in_array(strtolower($data['order_records']->payment_option), $payment_methodarr))
                        {
                            //Code for save updated by and date value 
                            $update_array = array(
                                'updated_by' => $this->session->userdata("AdminUserID"),
                                'updated_date' => date('Y-m-d H:i:s')
                            );
                            $this->db->set($update_array)->where('entity_id',$order_id)->update('order_master');
                            //Code for save updated by and date value
                        }
                    }
                }
            }
            // End - stripe refund code

            //Code for update the order status withe selected and remain status :: Start
            $status_created_by = $this->session->userdata('AdminUserType');
            $cnt=0;
            $order_status_remain = array();
            //Code for Dinein Order :: Start
            if($order_typeval=='DineIn')
            {
                /*$order_statusarr = dinein_order_status($this->session->userdata('language_slug'));
                unset($order_statusarr['placed']);
                unset($order_statusarr['cancel']);
                unset($order_statusarr['rejected']);*/
                $order_statusarr = array();
                foreach ($order_statusarr as $key => $value)
                {  
                    if($cnt>0)
                    {
                        if($key == 'orderready' && $order_typeval =='DineIn')
                        {}
                        else
                        {
                            $order_status_remain[$key]= $value;                            
                        }
                    }
                    if(strtolower($key)==strtolower($order_statusval))
                    {
                        $cnt++;
                    }
                }
                if(!empty($order_status_remain) && count($order_status_remain)>0 && $this->input->post('order_status')!='cancel')
                {
                    foreach($order_status_remain as $key => $value) 
                    {
                        $ord_stat = ($key=='accepted')?'accepted_by_restaurant':(($key == 'orderready')?'ready':$key);
                        $ord_statchk = $ord_stat;
                        if(strtolower($this->input->post('order_status')) == strtolower($ord_statchk)) break;
                                                 
                        $addData = array(
                            'order_id'=>$entity_id,
                            'user_id'=> $this->session->userdata('AdminUserID'),
                            'order_status'=>$ord_stat,
                            'time'=>date('Y-m-d H:i:s'),
                            'status_created_by'=>$status_created_by
                        );
                        $status_id = $this->order_model->addData('order_status',$addData);                                          
                    }
                }
            }//Code for Dinein Order :: End :: //Code for Normal Order :: Start
            else
            {
                $order_statusarr = order_status($this->session->userdata('language_slug'));
                unset($order_statusarr['placed']);
                unset($order_statusarr['cancel']);
                unset($order_statusarr['rejected']);
                if($order_typeval == 'PickUp')
                {
                    unset($order_statusarr['onGoing']);
                    unset($order_statusarr['delivered']);
                }
                else
                {
                    unset($order_statusarr['orderready']);
                }
                
                foreach ($order_statusarr as $key => $value)
                {  
                    if($cnt>0)
                    {
                        if(($key == 'orderready' && $order_typeval =='Delivery'))
                        {                            
                        }else if($order_typeval =='PickUp' && strtolower($key)=='ongoing')
                        {
                        }
                        else{
                            $order_status_remain[$key]= $value;    
                        }
                    }
                    if(strtolower($key)==strtolower($order_statusval))
                    {
                        $cnt++;
                    }                    
                }                

                if(!empty($order_status_remain) && count($order_status_remain)>0 && $this->input->post('order_status')!='cancel')
                {
                    foreach($order_status_remain as $key => $value) 
                    {
                        $ord_stat = ($key=='accepted')?'accepted_by_restaurant':(($key == 'orderready')?'ready':$key);
                        $ord_statchk = $ord_stat;
                        if($order_typeval =='PickUp')
                        {
                           if($ord_stat=='ready' && strtolower($order_status)=='ongoing') 
                           {
                               $ord_statchk = 'ongoing';
                           }                            
                        }

                        if(strtolower($this->input->post('order_status')) == strtolower($ord_statchk)) break;
                                                 
                        $addData = array(
                            'order_id'=>$entity_id,
                            'user_id'=> $this->session->userdata('AdminUserID'),
                            'order_status'=>$ord_stat,
                            'time'=>date('Y-m-d H:i:s'),
                            'status_created_by'=>$status_created_by
                        );
                        $status_id = $this->order_model->addData('order_status',$addData);                                        
                    }
                }
            }//Code for Normal Order :: End
            //Code for update the order status withe selected and remain status :: End
            $this->db->set('order_status',$this->input->post('order_status'))->where('entity_id',$entity_id)->update('order_master');
            /*refer and earn changes start*/
            //credit amount to referer in first transaction (if referral code used while signup)
            //get user detail who placed the order
            $userorderdetail = $this->order_model->getRecord('order_master','entity_id',$entity_id);
            $order_user_id = $userorderdetail->user_id;
            if($order_user_id && $order_user_id>0){
                $getreferraldetail = $this->order_model->getRecord('users','entity_id',$order_user_id);
                if(strtolower($this->input->post('order_status')) == 'delivered' || strtolower($this->input->post('order_status')) == 'complete'){
                    if(!empty($getreferraldetail->referral_code_used)) {
                        //checking in wallet if there is any entry of order user as referee.
                        $check_wallet_history = $this->order_model->getRecordMultipleWhere('wallet_history',array('referee_id' => $order_user_id, 'is_deleted'=>0));
                        if(empty($check_wallet_history)){
                            //get referer details
                            $getUser = $this->order_model->getRecord('users','referral_code',$getreferraldetail->referral_code_used);
                            //add wallet money(credited) in users table
                            $wallet = $getUser->wallet;
                            $referral_amount = $this->order_model->getSystemOption('referral_amount');
                            $ref_credit_amount = ($userorderdetail->subtotal * $referral_amount->OptionValue)/100;
                            $addWallet_amount = array(
                                'wallet'=>$wallet+$ref_credit_amount
                            );
                            $this->order_model->updateMultipleWhere('users', array('entity_id'=>$getUser->entity_id,'referral_code'=>$getUser->referral_code), $addWallet_amount);
                            //add wallet history - amount credited
                            $updateWalletHistory = array(
                                'user_id'=>$getUser->entity_id, //referrer
                                'referee_id'=>$order_user_id, //referee (order user id)
                                'amount'=>$ref_credit_amount,
                                'credit'=>1,
                                'reason'=>'referral_bonus',
                                'created_date' => date('Y-m-d H:i:s')
                            );
                            $this->order_model->addData('wallet_history',$updateWalletHistory);
                        }
                    }
                }
            }
            /*refer and earn changes end*/
            /*wallet changes start*/
            //if order is cancelled both debit and credit should be removed from wallet history
            if($this->input->post('order_status') == 'cancel') {
                $cancel_reason = (!empty($this->input->post('other_reason'))) ? $this->input->post('other_reason') : $this->input->post('cancel_reason');
                $this->db->set('cancel_reason',$cancel_reason)->where('entity_id',$entity_id)->update('order_master');
                if($user_id && $user_id >0){
                    $users_wallet = $this->order_model->getUsersWalletMoney($user_id);
                    $current_wallet = $users_wallet->wallet; //money in wallet
                    $credit_walletDetails = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $entity_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                    $credit_amount = $credit_walletDetails->amount;
                    $debit_walletDetails = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $entity_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                    $debit_amount = $debit_walletDetails->amount;
                    $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                    $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                    //delete order_id from wallet history and update users wallet
                    if(!empty($credit_amount) || !empty($debit_amount)){
                        $this->order_model->deletewallethistory($entity_id); // delete by order id
                        $new_wallet = array(
                            'wallet'=>$new_wallet_amount
                        );
                        $this->order_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                    }
                }
            }
            /*wallet changes end*/
            if(trim($order_typeval) == 'PickUp' && trim($this->input->post('order_status')) == 'onGoing') {
                $statval = 'ready';
            } else {
                $statval = $this->input->post('order_status');
            }
            $status_created_by = $this->session->userdata('AdminUserType');
            $addData = array(
                'order_id'=>$entity_id,
                'user_id'=> $this->session->userdata('AdminUserID'),
                'order_status'=>$statval,
                'time'=>date('Y-m-d H:i:s'),
                'status_created_by'=>$status_created_by
            );
            $order_id = $this->order_model->addData('order_status',$addData);
            // adding notification for website
            $order_status = '';
            if ($this->input->post('order_status') == "complete") {
                $this->common_model->deleteData('user_order_notification','order_id',$entity_id);
                if($order_typeval =='DineIn' || $order_typeval =='PickUp')
                {
                    $order_status = 'order_completed';
                }
            }
            // else if ($this->input->post('order_status') == "preparing") {
            //     $order_status = 'order_preparing';
            // }
            else if ($this->input->post('order_status') == "onGoing") {
                $order_status = 'order_ongoing';
            }
            else if ($this->input->post('order_status') == "delivered") {
                $order_status = 'order_delivered';
                //Code for find the drvier id :: Start
                $driver_detail = $this->order_model->getAssignDrvier($entity_id);
                if($driver_detail)
                { $driver_id = $driver_detail->driver_id; }
                //Code for find the drvier id :: End
            }
            else if ($this->input->post('order_status') == "cancel") {
                $order_status = 'order_canceled';
            }
            else if ($this->input->post('order_status') == "ready") {
                $order_status = 'order_served';
            }
            //Code for upadte the update order status :: start
            $in_statusarr = array('complete','delivered','cancel');
            if(in_array($this->input->post('order_status'),$in_statusarr))
            {
                $this->db->set('is_updateorder','0')->where('order_id',$entity_id)->update('order_detail');
            }
            //Code for upadte the update order status :: end
            if ($order_status != '') {
                $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$entity_id);
                if($order_detail->user_id && $order_detail->user_id >0){
                    $notification = array(
                        'order_id' => $entity_id,
                        'user_id' => $order_detail->user_id,
                        'notification_slug' => $order_status,
                        'view_status' => 0,
                        'datetime' => date("Y-m-d H:i:s"),
                    );
                    $this->common_model->addData('user_order_notification',$notification);
                }
                if($order_detail->agent_id){
                    $this->common_model->notificationToAgent($entity_id, $this->input->post('order_status'));
                }
            }
            if($user_id && $user_id > 0){
                $userdata = $this->order_model->getUserDate($user_id);
                //get langauge
                $device = $this->order_model->getDevice($user_id);
                if($device->notification == 1){
                    $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    $message = sprintf($this->lang->line($order_status),$entity_id);
                    /*if order status is cancled then append reason*/
                    if($order_status == 'order_canceled'){
                        $message = sprintf($this->lang->line('order_canceled'),$entity_id).'-'.$cancel_reason;
                    }
                    if($order_status == 'order_ongoing')
                    {
                        if($order_typeval =='DineIn')
                        {
                           $message = sprintf($this->lang->line('food_is_ready_notification'),$entity_id);
                        }
                        else if($order_typeval =='PickUp')
                        {
                            $message = sprintf($this->lang->line('order_ready_notification'),$entity_id);
                        }
                        else if($order_typeval =='Delivery')
                        {
                            $message = sprintf($this->lang->line('on_going_notification'),$entity_id);
                        }
                    }
                    else if($this->input->post('order_status') == "ready")
                    {
                        if($order_typeval =='DineIn')
                        { 
                           $message = $this->lang->line('order_served');
                        }
                    }
                    else if($this->input->post('order_status') == "complete")
                    {
                        $message = sprintf($this->lang->line('order_completed'),$entity_id);
                    }
                    $device_id = $device->device_id;
                    $restaurant = $this->order_model->orderDetails($entity_id);
                    // Send Latest wallet balance               
                    if($this->input->post('order_status') == 'cancel') {
                        $users_wallet = $this->order_model->getUsersWalletMoney($user_id);
                        $latest_wallet_balance = $users_wallet->wallet; 
                    }
                    $this->sendFCMRegistration($device_id,$message,$this->input->post('order_status'),$restaurant[0]->restaurant_id,FCM_KEY,$order_typeval,$order_detail->paid_status,$entity_id,$driver_id,$latest_wallet_balance);
                }
                //send refund noti to user
                if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
                    $this->common_model->sendRefundNoti($entity_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
                }
            }
            //send email and sms notification to user on order cancel
            if($this->input->post('order_status') == 'cancel'){
                $langslugval = ($device->language_slug) ? $device->language_slug : '';
                $useridval = ($user_id && $user_id > 0) ? $user_id : 0;
                $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$entity_id,$this->session->userdata('AdminUserType'));
            }

            if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
                $response['error_message'] = $this->lang->line('admin_refund_failed');
            }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
                $response['error_message'] = $this->lang->line('admin_refund_canceled');
            }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
                $response['error_message'] = $this->lang->line('refund_pending_err_msg');
            }
            echo json_encode($response);
        } else {
            //display thirdparty error.
            if($thirdparty_cancel_resp['status'] == '0'){
                $arrResponse = array();
                $arrResponse['status'] = "thirdparty_cancel_error";
                $arrResponse['status_message']  = $this->lang->line('thirdparty_cancel_error');
                echo json_encode($arrResponse);
                exit;
            }
        }
    }
    // Send notification
    function sendFCMRegistration($registrationIds,$message,$order_status,$restaurant_id,$key=FCM_KEY,$order_typeval='',$paid_status='',$order_id='',$driver_id='',$wallet_amount='') {
        if($registrationIds){        
            #prep the bundle
            $fields = array();            
            $fields['to'] = $registrationIds; // only one user to send push notification
            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
            $fields['notification']['title'] = $this->lang->line('customer_app_name');
            if ($order_status == "delivered" || $order_status == "complete") {
                $fields['data'] = array ('screenType'=>'delivery','restaurant_id'=>$restaurant_id,'order_id'=>$order_id,'driver_id'=>$driver_id,'wallet_amount'=>$wallet_amount);
            }
            else
            {
                if($order_typeval =='DineIn' && $paid_status == 'unpaid')
                {
                    $fields['data'] = array ('screenType'=>'dinein','wallet_amount'=>$wallet_amount);
                }
                else
                {
                    $fields['data'] = array ('screenType'=>'order','wallet_amount'=>$wallet_amount);
                }                
            }            
            $headers = array (
                'Authorization: key=' . $key,
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
    public function deleteMultiOrder(){
        $orderId = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        if($orderId){
            $order_id = explode(',', $orderId);
            $data = $this->order_model->deleteMultiOrder($order_id);
            if(count($order_id) == 1) {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted order - '.$order_id[0]);
            } else {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted multiple orders');
            }
            echo json_encode($data);
        }
    }
    //get status history
    public function statusHistory(){
        $entity_id = ($this->input->post('order_id'))?$this->input->post('order_id'):''; 
        if($entity_id){
            $data['history'] = $this->order_model->statusHistory($entity_id);
            $data['order_history'] = $this->order_model->statusOrderHistory($entity_id);
            $this->load->view(ADMIN_URL.'/view_status_history',$data);
        }
    }
    //generate report
    public function generate_report(){
        $restaurant_id = $this->input->post('restaurant_id');
        $order_type = $this->input->post('order_delivery');
        $order_date = $this->input->post('order_date');
        $results = $this->order_model->generate_report($restaurant_id,$order_type,$order_date); 
        if(!empty($results)){
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array("Restaurant","User Name","Order Total","Order Delivery","Order Date","Order Status","Status");
            for($h=0,$c='A'; $h<count($headers); $h++,$c++)
            {
                $this->excel->getActiveSheet()->setCellValue($c.'1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c.'1')->getFont()->setBold(true);
            }
            $row = 2;
            //get System Option Data
            /*$this->db->select('OptionValue');
            $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
            $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
            for($r=0; $r<count($results); $r++){ 
                $currency_symbol = $this->common_model->getCurrencySymbol($results[$r]->currency_id);
                $status = ($results[$r]->status)?'Active':'Deactive';
                $this->excel->getActiveSheet()->setCellValue('A'.$row, $results[$r]->name);
                $this->excel->getActiveSheet()->setCellValue('B'.$row, $results[$r]->first_name.' '.$results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('C'.$row, number_format_unchanged_precision($results[$r]->total_rate,$currency_symbol->currency_code));
                $this->excel->getActiveSheet()->setCellValue('D'.$row, $results[$r]->order_delivery);
                $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->common_model->getZonebaseDateMDY($results[$r]->order_date));
                $this->excel->getActiveSheet()->setCellValue('F'.$row, ucfirst($results[$r]->order_status));            
                $this->excel->getActiveSheet()->setCellValue('G'.$row, $status);                
            $row++;
            }
            $filename = 'report-export.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache   
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
            
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');  
            exit;   
        }else{
            // $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            $_SESSION['not_found'] = $this->lang->line('not_found');
            redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
        }
    }
    //addons changes start
    //get Addons List
    public function getAddonsList()
    {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $num = ($this->input->post('num') != '')?$this->input->post('num'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        if($entity_id)
        {
            $result =  $this->order_model->addonDetails('menu_id',$entity_id,$restaurant_id);
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)){
                $currency = $default_currency;
            }else{
                $currency = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));
            }
           $cat_arraytemp = array();                      
           if(!empty($result))
           {
                //array_multisort( array_column($result, "cat_name"), SORT_ASC, $result ); //hide for addon sequence
                foreach ($result as $key => $value)
                {
                    $cat_arraytemp[$value['category_id']][] = $value;
                }
                foreach ($cat_arraytemp as $key => $value)
                {
                    $data_max = ($value[0]['is_multiple'] == 1 && !is_null($value[0]['display_limit'])) ? 'data-max-selection ="'.$value[0]['display_limit'].'"' : '';
                    $data_required = ($value[0]['mandatory'] == 1) ? '<span class="required">*</span>' : '' ;
                    $data_validate = ($value[0]['mandatory'] == 1) ? 'addon-validate' : '' ;
                    $select_note = ($value[0]['is_multiple'] == 1 && !is_null($value[0]['display_limit'])) ? ' ('.$this->lang->line('select_any').' '.$value[0]['display_limit'].')' : '';
                    $html .= '<div class="row max-selection '.$data_validate.'"'.$data_max.'>';
                    foreach ($value as $keyinn => $valueinn)
                    {
                        if($keyinn==0)
                        {
                            $cat_name = str_replace(' (s)', '', $valueinn['cat_name']);
                            $cat_name = str_replace(' (S)', '', $cat_name);
                            $cat_name = str_replace('(s)', '', $cat_name);
                            $cat_name = str_replace('(S)', '', $cat_name);
                            $cat_name = str_replace(' ','',$cat_name);
                            $cat_name = str_replace(array('.', ','), '' , $cat_name);

                            $html .= '
                            <input type="hidden" name="cat_arr_'.$num.'[]" value="'.$cat_name.'" maxlength="3" data-required="1" readonly class="form-control qty validate-class" />
                            <div class="col-md-12 control-label">'.$valueinn['cat_name'].$data_required.$select_note.'</div>';
                        }
                        
                        if($valueinn['is_multiple']=='1')
                        {
                            $html .= '<div class="col-md-12">
                                            <input type="checkbox" class="check_addons category_checkbox checkboxcls'.$num.'" data-price="'.$valueinn['add_ons_price'].'" onChange="calculate_rate('.$num.','.$valueinn['add_ons_id'].',0,\''.$cat_name.'\')" name="addons_id_'.$num.'['.$entity_id.']['.$valueinn['addons_cat_id'].'][]" id="addons_id_'.$num.''.$valueinn['add_ons_id'] .'" value="'.$valueinn['add_ons_id'].'" addons_category="'.$valueinn['cat_name'].'"> '.$valueinn['add_ons_name'].' 
                                            <span> ('.$currency->currency_symbol.$valueinn['add_ons_price'].') </span>
                                            <input type="hidden" name="add_qty_no_'.$num.'['.$valueinn['add_ons_id'].'][]" id="add_qty_no_'.$num.''.$valueinn['add_ons_id'] .'" value="" maxlength="3" data-required="1" readonly class="form-control qty validate-class" />
                                        </div>';
                        }
                        else
                        {
                            $html .= '<div class="col-md-12">
                                            <input type="radio" class="radio_addons category_checkbox get_value radioclass_'.$num.''.$cat_name.'" data-price="'.$valueinn['add_ons_price'].'" onChange="calculate_rate('.$num.','.$valueinn['add_ons_id'].',1,\''.$cat_name.'\')" name="addons_id_'.$num.'['.$entity_id.']['.$valueinn['addons_cat_id'].'][]" id="addons_id_'.$num.''.$valueinn['add_ons_id'] .'" value="'.$valueinn['add_ons_id'].'"> '.$valueinn['add_ons_name'].' 
                                            <span> ('.$currency->currency_symbol.$valueinn['add_ons_price'].') </span>
                                            
                                            <input type="hidden" name="add_qty_no_'.$num.'['.$valueinn['add_ons_id'].'][]" id="add_qty_no_'.$num.''.$valueinn['add_ons_id'] .'" value="" maxlength="3" data-required="1" readonly class="form-control qty validate-class radioclassq_'.$num.''.$cat_name.'" />
                                        </div>';
                        }
                        //End
                    }
                    $html .= '<div class="addon-error" style="margin-left:15px;"></div></div>'; 
                }
           }
           else
            {
                $html .= '<div class="col-md-6"></div></div>';
            }           
        }
        echo $html;
    }
    //addons changes end
     //get address
    public function getCoupon()
    {
        $subtotal = ($this->input->post('subtotal') != '')?$this->input->post('subtotal'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        if($restaurant_id!='')
        {
            $restaurant_id = $this->order_model->getResContentId($restaurant_id);
        }        
        $user_id = ($this->input->post('user_id') != '')?$this->input->post('user_id'):'';
        $order_delivery = ($this->input->post('order_delivery') != '')?$this->input->post('order_delivery'):'';
        if($subtotal && $restaurant_id)
        {
            //$html = '<option value="">'.$this->lang->line('select').'</option>';
            $html = '';
            $result =  $this->order_model->getCoupon($subtotal,$restaurant_id,$user_id,$order_delivery);

            //Code for filter array with requirement :: Start
            $coupons = array();
            $cntt=0;
            if($result && !empty($result))
            {
                foreach ($result as $res_key => $res_value)
                {
                    $flag_cnt = 'yes';            
                    $checkCnt = $this->common_model->checkUserUseCountCoupon($user_id,$res_value->entity_id);                    
                    if($checkCnt >= $res_value->maximaum_use_per_users && $res_value->maximaum_use_per_users>0){
                        $flag_cnt = 'no';
                    }
                    if($flag_cnt=='yes'){
                        $checkCnt1 = $this->common_model->checkTotalUseCountCoupon($res_value->entity_id);
                        if($checkCnt1 >= $res_value->maximaum_use && $res_value->maximaum_use>0){
                            $flag_cnt = 'no';
                        }    
                    }                    
                    if($flag_cnt=='yes')
                    {
                        //Code for free delviery coupon falg check :: Start
                        $user_chkcpn = 'yes';                       
                        if($user_id>0)
                        {
                            $this->db->select('entity_id');
                            $this->db->where('user_id',$user_id);
                            $user_chk = $this->db->count_all_results('order_master');
                            if($user_chk>0)
                            {
                                $user_chkcpn = 'no';
                            }            
                        }                                              
                        if($res_value->coupon_type=='free_delivery' && strtolower($order_delivery)=='delivery' && $user_chkcpn=='no' && $res_value->coupon_for_newuser=='1')
                        {
                        }//Code for free delviery coupon falg check :: End
                        else
                        {
                            $coupons[$cntt] = $res_value;
                            $cntt++;
                        }
                    }
                }    
            }
            //Code for filter array with requirement :: End
            
            if(!empty($coupons))
            {
                foreach($coupons as $key => $value)
                {
                    $html .= '<option value="'.$value->entity_id.'" amount="'.$value->amount.'" is_mutliple_coupons="'.$value->use_with_other_coupons.'" type="'.$value->amount_type.'" coupon_type="'.$value->coupon_type.'" c_name="'.$value->name.'">'.$value->name.'</option>';
                }
            }
        }
        echo $html;
    }
    //get order detail
    public function orderDetail()
    {
        $entity_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):''; 
        if($entity_id)
        {
            $data['odetails'] = $this->order_model->orderDetails($entity_id);
            $data['coupon_array'] = $this->common_model->getCoupon_array($entity_id);
            /*wallet money changes start*/
            $data['wallet_history'] = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $entity_id,'debit' => 1));
            /*wallet money changes end*/
            $data['entity_id'] = $entity_id;
            $default_currency = get_default_system_currency();
            if(!empty($default_currency)){
                $data['currency'] =  $default_currency;
            }else{
                $data['currency'] = $this->common_model->getRestaurantCurrencySymbol($data['odetails'][0]->restaurant_id);
            }
            $data['restaurant'] = unserialize($data['odetails'][0]->restaurant_detail);
            $data['user_detail'] = unserialize($data['odetails'][0]->user_detail);
            $data['item_detail'] = unserialize($data['odetails'][0]->item_detail);
            $data['thirdparty_driver_details'] = $this->common_model->getDoordashDriver($entity_id);
            $this->load->view(ADMIN_URL.'/view_order_details',$data);
        }
    }
    //export order 15-01-2021 vip.. start
    public function export_order()
    {
        if(in_array('order~export_order',$this->session->userdata("UserAccessArray")))
        {
            $slug = $this->session->userdata('language_slug');
            $languages = $this->common_model->getFirstLanguages($slug);
            $this->lang->load('messages_lang', $languages->language_directory);
            $orderId = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
            $fromAjax = ($this->input->post('fromAjax'))?$this->input->post('fromAjax'):"no";
            $order_id = '';
            if($orderId){
                $order_id = explode(',', $orderId);
            }
            $restaurant_id = $this->input->post('restaurant_id');  
            $order_type = $this->input->post('order_delivery');
            $start_datearr = explode("-", $this->input->post('start_date'));
            $end_datearr = explode("-", $this->input->post('end_date'));
            $start_date = '';
            if(!empty($start_datearr) && $this->input->post('start_date')!='')
            {
                $start_datetemp =  $start_datearr[1].'-'.$start_datearr[0].'-'.$start_datearr[2];
                $start_date = date('Y-m-d', strtotime($start_datetemp));
            }
            $end_date = '';
            if(!empty($end_datearr) && $this->input->post('start_date')!='')
            {
                $end_datetemp =  $end_datearr[1].'-'.$end_datearr[0].'-'.$end_datearr[2];
                $end_date = date('Y-m-d', strtotime($end_datetemp));
            }
            if ($start_date == '1970-01-01' AND $end_date == '1970-01-01') {
                $start_date = '';
                $end_date = '';
            }
            if($restaurant_id != 'all'){
                $restIds = $this->order_model->getResIds($restaurant_id);
            }
            else {
                $restIds = $restaurant_id;
            }
            $data = $this->order_model->export_order($order_id,$restIds,$order_type,$start_date,$end_date);
            if(!empty($data)){
                $this->load->library("excel");
                $object = new Excel();
                $from = "A1";
                $to = "AF1";
                $object->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
                foreach(range("$from","$to") as $columnID) {
                    $object->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                $object->setActiveSheetIndex(0);
                $table_columns = array(
                    $this->lang->line('order').' #',
                    $this->lang->line('transaction_id'),
                    $this->lang->line('customer'),
                    $this->lang->line('customer').' '.$this->lang->line('address'),
                    $this->lang->line('restaurant'),
                    $this->lang->line('restaurant').' '.$this->lang->line('address'),
                    $this->lang->line('item_name'),
                    $this->lang->line('item').''.$this->lang->line('quantity'),
                    $this->lang->line('sub_total'),
                    $this->lang->line('order_type'),
                    $this->lang->line('order_status'),
                    $this->lang->line('cancel_reason'),
                    $this->lang->line('reject_reason'),
                    $this->lang->line('payment_method'),
                    $this->lang->line('order_date'),
                    $this->lang->line('scheduled_date'),
                    $this->lang->line('delivery_instructions'),
                    $this->lang->line('refund_status'),
                    $this->lang->line('refunded_amount'),
                    $this->lang->line('refund_reason'),
                    $this->lang->line('refunded_by'),
                    $this->lang->line('updated_by')
                );
                if($this->session->userdata('AdminUserType') == 'MasterAdmin') {
                    $splice_arr1 = array (
                        $this->lang->line('service_tax'),
                        $this->lang->line('service_fee'),
                        $this->lang->line('creditcard_fee'),
                        $this->lang->line('delivery_charge'),
                        $this->lang->line('driver_tip'),
                        $this->lang->line('title_admin_coupon').''.$this->lang->line('name'),
                        $this->lang->line('coupon_discount'),
                        $this->lang->line('order_total'),
                        $this->lang->line('contractual_commission_txt'),
                    );
                    $splice_arr2 = array(
                        $this->lang->line('delivery_method')
                    );
                    array_splice( $table_columns, 9, 0, $splice_arr1 );
                    array_splice( $table_columns, 19, 0, $splice_arr2 );
                }

                $column = 1;
                foreach($table_columns as $field) {
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                    $column++;
                }
                $excel_row = 2;
                $payment_methodarr = array('stripe','paypal','applepay');
                for ($i=0; $i <count($data) ; $i++) {
                    $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $data[$i]['entity_id']);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $data[$i]['transaction_id']);
                    $user_name = unserialize($data[$i]['user_detail'])['first_name'].' '.unserialize($data[$i]['user_detail'])['last_name'];
                    if (empty($data[$i]['fname']) && unserialize($data[$i]['user_detail'])['first_name'] && $user_name!='') {
                       //$username = 'Order by Restaurant';
                        $username = $user_name;
                       $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $username);
                    } else {
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $data[$i]['fname'].' '.$data[$i]['lname']);
                    }
                    $user_adress = unserialize($data[$i]['user_detail'])['address'];
                    if($user_adress!='' && unserialize($data[$i]['user_detail'])['landmark']!='') {
                        $user_adress = $user_adress.", ".unserialize($data[$i]['user_detail'])['landmark'];
                    }
                    if($user_adress!='' && unserialize($data[$i]['user_detail'])['city']!='') {
                        $user_adress = $user_adress.", ".unserialize($data[$i]['user_detail'])['city'];
                    }
                    $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $user_adress);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $data[$i]['name']);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, unserialize($data[$i]['restaurant_detail'])->address.','.unserialize($data[$i]['restaurant_detail'])->landmark.','.unserialize($data[$i]['restaurant_detail'])->city);
                    if (count(unserialize($data[$i]['item_detail'])) > 1) {
                        for ($j=0; $j < count(unserialize($data[$i]['item_detail'])); $j++) { 
                           $item[$j] = unserialize($data[$i]['item_detail'])[$j]['item_name'];
                        }
                        $item_name = implode(",", $item);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $item_name);
                    } else {
                        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, unserialize($data[$i]['item_detail'])[0]['item_name']);
                    }
                    //Code for quantity count :: Start
                    $item_detail = unserialize($data[$i]['item_detail']);
                    $Quantity = 0;
                    if(!empty($item_detail) && count($item_detail)>0) {
                        foreach($item_detail as $keyll => $valll) {
                            $Quantity = $Quantity+$valll['qty_no'];
                        }
                    }
                    //Code for quantity count :: End
                    $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $Quantity);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $data[$i]['subtotal']);

                    //for cod
                    $is_showrefundedby = 'no';
                    if($data[$i]['payment_option'] == 'cod' && $data[$i]['tips_transaction_id'] != '' && $data[$i]['tips_refund_status'] == 'refunded') {
                        $payment_method = $this->lang->line('cod_display')." ".$this->lang->line('cod_initiate_refunded');
                    } else if($data[$i]['payment_option'] == 'cod' && $data[$i]['tips_transaction_id'] != '') {
                        $payment_method = $this->lang->line('cod_display')." ".$this->lang->line('cod_initiate');
                    } else if($data[$i]['payment_option'] == 'cod') {
                        $payment_method = $this->lang->line('cod_display');
                    }
                    //for stripe
                    if($data[$i]['payment_option'] == 'stripe' && $data[$i]['tips_transaction_id'] != '' && $data[$i]['tips_refund_status'] == 'refunded') {
                        $payment_method = 'Stripe (refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'stripe' && $data[$i]['tips_transaction_id'] == '' && $data[$i]['refund_status'] == 'refunded') {
                        $payment_method = 'Stripe (refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'stripe' && $data[$i]['stripe_refund_id'] != '' && $data[$i]['refund_status'] == 'partial refunded') {
                        $payment_method = 'Stripe (partial refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'stripe') {
                        $payment_method = 'Stripe';
                    }
                    //for applepay
                    if($data[$i]['payment_option'] == 'applepay' && $data[$i]['tips_transaction_id'] != '' && $data[$i]['tips_refund_status'] == 'refunded') {
                        $payment_method = 'Apple Pay (refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'applepay' && $data[$i]['tips_transaction_id'] == '' && $data[$i]['refund_status'] == 'refunded') {
                        $payment_method = 'Apple Pay (refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'applepay' && $data[$i]['stripe_refund_id'] != '' && $data[$i]['refund_status'] == 'partial refunded') {
                        $payment_method = 'Apple Pay (partial refunded)';
                        $is_showrefundedby = 'yes';
                    } else if($data[$i]['payment_option'] == 'applepay') {
                        $payment_method = 'Apple Pay';
                    }
                    //for paypal
                    if(strtolower($data[$i]['payment_option']) == 'paypal') {
                        $payment_method = 'Paypal';
                        if($data[$i]['refund_status'] == 'refunded') {
                            $payment_method .= ' (refunded)';
                            $is_showrefundedby = 'yes';
                        } else if($data[$i]['refund_status'] == 'partial refunded' && $data[$i]['stripe_refund_id'] != '') {
                            $payment_method .= ' (partial refunded)';
                            $is_showrefundedby = 'yes';
                        }
                    }
                    $scheduleddateval = ($data[$i]['scheduled_date']) ? $data[$i]['scheduled_date'] : NULL;
                    $slotopentimeval = ($data[$i]['slot_open_time']) ? $data[$i]['slot_open_time'] : NULL;
                    $slotclosetimeval = ($data[$i]['slot_close_time']) ? $data[$i]['slot_close_time'] : NULL;
                    $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$scheduleddateval $slotopentimeval"));
                    $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$scheduleddateval $slotclosetimeval"));
                    $order_scheduled_date = ($scheduleddateval) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                    $order_slot_open_time = ($slotopentimeval) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime))) : NULL;
                    $order_slot_close_time = ($slotclosetimeval) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime))) : NULL;

                    //order status display
                    if($data[$i]['ostatus'] == "placed" && $data[$i]['status'] != '1') {
                        $ostatuslng = $this->lang->line('placed');
                    } else if(($data[$i]['ostatus'] == "placed" && $data[$i]['status'] == '1') || $data[$i]['ostatus'] == "accepted") {
                        $ostatuslng = $this->lang->line('accepted');
                    } else if($data[$i]['ostatus'] == "rejected") {
                        $ostatuslng = $this->lang->line('rejected');
                    } else if($data[$i]['ostatus'] == "delivered") {
                        $ostatuslng = $this->lang->line('delivered');
                    } else if($data[$i]['ostatus'] == "onGoing") {
                        $ostatuslng = $this->lang->line('onGoing');
                        if($data[$i]['order_delivery'] == "PickUp") {
                            $ostatuslng = $this->lang->line('order_ready');
                        }
                    } else if($data[$i]['ostatus'] == "cancel") {
                        $ostatuslng = $this->lang->line('cancel');
                    } else if($data[$i]['ostatus'] == "ready") {
                        $ostatuslng = $this->lang->line('order_ready');
                    } else if($data[$i]['ostatus'] == "complete") {
                        $ostatuslng = $this->lang->line('complete');
                    }
                    if($data[$i]['ostatus'] == "pending") {
                        $ostatuslng = $this->lang->line('pending');
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin') {
                        // service tax, service fee and credit card fee :: start
                        $service_tax_type = ($data[$i]['tax_type'] == 'Percentage')?'%':'';
                        $service_fee_type = ($data[$i]['service_fee_type'] == 'Percentage')?'%':'';
                        $creditcard_fee_type = ($data[$i]['creditcard_fee_type'] == 'Percentage')?'%':'';
                        if($data[$i]['creditcard_fee'] != 0) { 
                            if($data[$i]['creditcard_fee_type'] == 'Percentage'){
                                $creditcard_fee = ($data[$i]['subtotal'] * $data[$i]['creditcard_fee']) / 100; 
                            } else {
                                $creditcard_fee = $data[$i]['creditcard_fee']; 
                            }
                            $creditcard_fee = round($creditcard_fee,2);
                        }
                        if($data[$i]['service_fee'] != 0) { 
                            if($data[$i]['service_fee_type'] == 'Percentage'){
                                $service_amount = ($data[$i]['subtotal'] * $data[$i]['service_fee']) / 100; 
                            } else {
                                $service_amount = $data[$i]['service_fee']; 
                            }
                            $service_amount = round($service_amount,2);
                        }
                        $tax_amountdis = 0;
                        if($data[$i]['tax_type'] == 'Percentage'){
                            $tax_amountdis = ($data[$i]['subtotal'] * $data[$i]['tax_rate']) / 100;
                        }else{
                            $tax_amountdis = $data[$i]['tax_rate'];
                        }
                        $tax_amountdis = round($tax_amountdis,2);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, ($tax_amountdis) ? $tax_amountdis : 0);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, ($service_amount) ? $service_amount : 0);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, ($creditcard_fee) ? $creditcard_fee : 0);
                        // service tax, service fee and credit card fee :: end
                        $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, ($data[$i]['delivery_charge']) ? $data[$i]['delivery_charge'] : '-');
                        $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, ($data[$i]['tip_amount']) ? $data[$i]['tip_amount'] : '-');

                        //Code for coupon name and coupon discount with multi use :: Start
                        $coupon_name = ($data[$i]['coupon_name'])?$data[$i]['coupon_name']:'';
                        $coupon_discount = ($data[$i]['coupon_discount'])?$data[$i]['coupon_discount']:0;
                        $coupon_array = $this->common_model->getCoupon_array($data[$i]['entity_id']);
                        if(!empty($coupon_array))
                        {
                           $coupon_name = implode("::", array_column($coupon_array, 'coupon_name'));
                           $coupon_discount = implode("::", array_column($coupon_array, 'coupon_discount'));                        
                        }
                        //Code for coupon name and coupon discount with multi use :: End

                        $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $coupon_name);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $coupon_discount);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $data[$i]['rate']);
                        //Commission Calculation begin
                        if($data[$i]['order_delivery'] == 'PickUp') {
                            if(!is_null($data[$i]['contractual_commission']) && !empty($data[$i]['contractual_commission']) && $data[$i]['contractual_commission'] > 0) {
                                if($data[$i]['coupon_discount'] > 0) {
                                    $commission_eligible_amount = $data[$i]['subtotal'] - $data[$i]['coupon_discount'];
                                    $commission = ($data[$i]['contractual_commission_type'] == 'Percentage') ? round(($commission_eligible_amount * $data[$i]['contractual_commission']) / 100,2) : $data[$i]['contractual_commission'];
                                } else {
                                    $commission = ($data[$i]['contractual_commission_type'] == 'Percentage') ? round(($data[$i]['subtotal'] * $data[$i]['contractual_commission']) / 100,2) : $data[$i]['contractual_commission'];
                                }
                            }else{
                                $commission = 0;
                            }
                        } else if($data[$i]['order_delivery'] == 'Delivery') {
                            if(!is_null($data[$i]['contractual_commission_delivery']) && !empty($data[$i]['contractual_commission_delivery']) && $data[$i]['contractual_commission_delivery'] > 0) {
                                if($data[$i]['coupon_discount'] > 0) {
                                    $commission_eligible_amount = $data[$i]['subtotal'] - $data[$i]['coupon_discount'];
                                    $commission = ($data[$i]['contractual_commission_type_delivery'] == 'Percentage') ? round(($commission_eligible_amount * $data[$i]['contractual_commission_delivery']) / 100,2) : $data[$i]['contractual_commission_delivery'];
                                } else {
                                    $commission = ($data[$i]['contractual_commission_type_delivery'] == 'Percentage') ? round(($data[$i]['subtotal'] * $data[$i]['contractual_commission_delivery']) / 100,2) : $data[$i]['contractual_commission_delivery'];
                                }
                            }else{
                                $commission = 0;
                            }
                        }
                        $delivery_method = ($data[$i]['delivery_method'] == "internal_drivers") ? $this->lang->line('internal_drivers') : (($data[$i]['delivery_method'] == "doordash" || $data[$i]['delivery_method'] == "relay") ? ucfirst($data[$i]['delivery_method']) : '-');

                        $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $commission);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $data[$i]['order_delivery']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $delivery_method);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $ostatuslng);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $data[$i]['cancel_reason']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $data[$i]['reject_reason']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $payment_method);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this->common_model->getZonebaseDateMDY($data[$i]['created_date']));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, ($order_scheduled_date && $order_slot_open_time && $order_slot_close_time) ? $this->common_model->dateFormat($order_scheduled_date)." (".$this->common_model->timeFormat($order_slot_open_time).' - '.$this->common_model->timeFormat($order_slot_close_time).')' : '-');
                        $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $data[$i]['delivery_instructions']);

                        //Code for refund :: Start
                        $admin_namedis = '';
                        if($data[$i]['adminf_name'] && $data[$i]['adminf_name']!='' && $data[$i]['adminf_name']!=null && $is_showrefundedby=='yes')
                        {
                            $admin_namedis = ($data[$i]['adminl_name'] && $data[$i]['adminl_name']!='' && $data[$i]['adminl_name']!=null)?$data[$i]['adminf_name'].' '.$data[$i]['adminl_name']:$data[$i]['adminf_name'];
                            $payment_option .= '<br> (Refunded by '.ucwords($admin_namedis).')'; 
                        }
                        $refunded_by = $update_by = '';
                        if($data[$i]['order_updated_by']==0)
                        {
                            $refunded_by = 'Auto Refund';
                            $update_by = 'Auto Updated';                        
                        }
                        if($data[$i]['order_updated_by']>0)
                        {
                            $update_by = $admin_namedis;
                            $refunded_by = $admin_namedis;                       
                        }
                        $refund_statusdis = ($data[$i]['refund_status'])?ucwords($data[$i]['refund_status']):'';
                        $refund_reasondis = ($data[$i]['refund_reason'])?ucwords($data[$i]['refund_reason']):'';
                        $refund_reasondis = str_replace("<br>", ", ", $refund_reasondis);
                        $refund_reasondis = str_replace("<br/>", ", ", $refund_reasondis);
                        if(!in_array(strtolower($data[$i]['payment_option']), $payment_methodarr))
                        {
                            $refunded_by = $refund_reasondis = '';
                        }                    
                        //Code for refund :: End

                        $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $refund_statusdis);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $data[$i]['refunded_amount']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(30, $excel_row, $refund_reasondis);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(31, $excel_row, $refunded_by);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(32, $excel_row, $update_by);

                    } else {
                        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $data[$i]['order_delivery']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $ostatuslng);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $data[$i]['cancel_reason']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $data[$i]['reject_reason']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $payment_method);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $this->common_model->getZonebaseDateMDY($data[$i]['created_date']));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, ($order_scheduled_date && $order_slot_open_time && $order_slot_close_time) ? $this->common_model->dateFormat($order_scheduled_date)." (".$this->common_model->timeFormat($order_slot_open_time).' - '.$this->common_model->timeFormat($order_slot_close_time).')' : '-');
                        $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $data[$i]['delivery_instructions']);

                        //Code for refund :: Start
                        $admin_namedis = '';
                        if($data[$i]['adminf_name'] && $data[$i]['adminf_name']!='' && $data[$i]['adminf_name']!=null)
                        {
                            $admin_namedis = ($data[$i]['adminl_name'] && $data[$i]['adminl_name']!='' && $data[$i]['adminl_name']!=null)?$data[$i]['adminf_name'].' '.$data[$i]['adminl_name']:$data[$i]['adminf_name'];
                            $payment_option .= '<br> (Refunded by '.ucwords($admin_namedis).')'; 
                        }
                        $refunded_by = $update_by = '';
                        if($data[$i]['order_updated_by']==0)
                        {
                            $refunded_by = 'Auto Refund';
                            $update_by = 'Auto Updated';                        
                        }
                        if($data[$i]['order_updated_by']>0)
                        {
                            $update_by = $admin_namedis;
                            $refunded_by = $admin_namedis;                       
                        }
                        if(!in_array(strtolower($data[$i]['payment_option']), $payment_methodarr))
                        {
                            $refunded_by = '';
                        }
                        $refund_statusdis = ($data[$i]['refund_status'])?ucwords($data[$i]['refund_status']):'';
                        $refund_reasondis = ($data[$i]['refund_reason'])?ucwords($data[$i]['refund_reason']):'';
                        $refund_reasondis = str_replace("<br/>", ", ", $refund_reasondis);
                        $refund_reasondis = str_replace("<br>", ", ", $refund_reasondis);
                        //Code for refund :: End

                        $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $refund_statusdis);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $data[$i]['refunded_amount']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $refund_reasondis);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $refunded_by);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $update_by);
                    }
                    $excel_row++;
                }
                $object_writer = $object->print_sheet($object);
                ob_end_clean();
                if ($fromAjax == 'yes') {
                    $filename = 'uploads/export_orders/orders'.date('y-m-d').'.xlsx';
                    $object_writer->save(str_replace(__FILE__,$filename ,__FILE__));
                    echo $filename;
                    exit;
                }
                else {
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="orders'.date('y-m-d').'.xlsx"');
                    $object_writer->save('php://output');
                }
            }else{
                // $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
                $_SESSION['not_found'] = $this->lang->line('not_found');
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
            }
        }            
    }
    //export order 15-01-2021 vip.. end
    
    public function add_order_payment(){
        $this->form_validation->set_rules('payment_method','Payment Method', 'trim|required');
        if($this->form_validation->run()){
            $order_ids = explode(",", $this->input->post('entity_id'));
            $update_order_data = array();
            foreach ($order_ids as $key => $order_id) {
                $update_order_data[] = array(
                    'entity_id' => $order_id,
                    'admin_payment_option' => $this->input->post('payment_method'),
                    'transaction_id' => $this->input->post('transaction_id') ? $this->input->post('transaction_id') : '',
                    'paid_status' => 'paid',
                    //'order_status' => 'complete'
                );
            }
            $result = $this->order_model->update_payment_data($update_order_data);
            if($result >  0){
                echo json_encode($result);
            }
        }
    }
    public function check_unpaid_payment(){
        $order_data = ($this->input->post('arrayData')) ? $this->input->post('arrayData') : "";
        if($order_data){
            $count = 0;
            $paymentpaidarr = array('stripe','paypal');
            $order_ids = explode(",",$order_data);
            foreach ($order_ids as $key => $order_id) {
                $order = $this->order_model->check_order_paid_or_not($order_id);

                $disabled = ($order->ostatus == 'delivered' || $order->ostatus == 'cancel' || $order->ostatus == 'rejected' || ($order->ostatus == 'complete' && $order->paid_status=='paid'))?'disabled':'';
                $paidstatus = (in_array($order->payment_option,$paymentpaidarr) || (!is_null($order->admin_payment_option) && !empty($order->admin_payment_option))) ? 'paid' : 'unpaid';
                $paidstatus = ($disabled == 'disabled' || $paidstatus == 'paid')?'paid':'unpaid'; 

                if($paidstatus == 'paid'){
                    $count++;
                }
            }
            echo json_encode($count);
        }
    }
    //Method for deleting order item :: Start
    public function ajaxOrderItemDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '')?intval($this->input->post('entity_id')):0;
        $item_id = ($this->input->post('item_id') != '')?intval($this->input->post('item_id')):0;
        $order_flag = ($this->input->post('order_flag') != '')?intval($this->input->post('order_flag')):0;
        $cart_key = ($this->input->post('cart_key') != '')?intval($this->input->post('cart_key')):0;

        if($entity_id>0 && $item_id>0)
        {
            $order_detailarr = $this->order_model->get_dinein_order($entity_id);

            $del_itemTotal = 0; $item_detailup = array();
            $removed_item_count = 0;
            if($order_detailarr && !empty($order_detailarr))
            {
                //Code for set all value :: Start
                $subtotal = $order_detailarr['subtotal'];
                $tax_rate = $order_detailarr['tax_rate'];
                $tax_type = $order_detailarr['tax_type'];
                $service_fee_type = $order_detailarr['service_fee_type'];
                $service_fee = $order_detailarr['service_fee'];
                $coupon_discount = $order_detailarr['coupon_discount'];
                $coupon_name = $order_detailarr['coupon_name'];
                $coupon_amount = $order_detailarr['coupon_amount'];
                $coupon_type = $order_detailarr['coupon_type'];
                $creditcard_fee_type = $order_detailarr['creditcard_fee_type'];
                $creditcard_fee = $order_detailarr['creditcard_fee'];
                $used_wallet_balance = $order_detailarr['used_wallet_balance'];
                $delivery_charge = $order_detailarr['delivery_charge'];
                $tip_amount = $order_detailarr['tip_amount'];
                $item_detail_old = $order_detailarr['item_detail'];
                //Code for set all value :: End
                //Code for remove order item :: Start
                $del_itmename = '';
                if(!empty($item_detail_old))
                {
                    for($iord=0;$iord<count($item_detail_old);$iord++)
                    {
                        if($item_detail_old[$iord]['item_id']==$item_id && $item_detail_old[$iord]['order_flag']==$order_flag && $iord == $cart_key && $removed_item_count == 0)
                        {
                            $del_itemTotal = $item_detail_old[$iord]['itemTotal'];
                            $del_itmename = $item_detail_old[$iord]['item_name'];
                            $removed_item_count++;
                        }
                        else
                        {
                            $item_detailup[] = $item_detail_old[$iord];
                        }   
                    }
                }
                //Code for remove order item :: End
            }
            $subtotalnew = $subtotal-$del_itemTotal;
            $subtotalsave = $subtotal-$del_itemTotal;
            //Code for coupon :: Code change as per multiple coupon :: Start
            //Code for find the find the coupon array
            $coupon_array = $this->common_model->getCoupon_array($entity_id);            
            
            $coupon_discount = $coupon_discounttotal = $coupon_discountup = 0;
            if(!empty($coupon_array))
            {
                foreach ($coupon_array as $cp_key => $cp_value)
                {
                    $coupon_type = $cp_value['coupon_type'];                    
                    $coupon_amount = $cp_value['coupon_amount']; 
                    $coupon_id = $cp_value['coupon_id']; 
                    
                    if(strtolower($coupon_type)=='percentage' && $coupon_amount>0)
                    {
                       $coupon_discountup = round(($subtotalsave * $coupon_amount)/100,2);
                    }
                    else
                    {
                        $coupon_discountup = $coupon_amount;
                    }
                    if($cp_key==0)
                    {
                        $coupon_discount = $coupon_discountup;
                    }

                    $coupon_discountup = round($coupon_discountup,2);

                    $coupondtl_chk = $this->order_model->getCouponData($coupon_id);
                    if($coupondtl_chk && !empty($coupondtl_chk) && $coupondtl_chk->coupon_type!='free_delivery')
                    {
                        $coupon_uparray = array(
                            'coupon_discount'=>$coupon_discountup
                        );
                        $this->order_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$entity_id,'coupon_id'=>$coupon_id), $coupon_uparray);

                        $subtotalnew = $subtotalnew - $coupon_discountup;
                    }
                    $coupon_discounttotal = $coupon_discounttotal + $coupon_discountup;                     
                }
            }    

            //wallet changes :: start
            $new_wallet_balance = 0;
            if($order_detailarr['user_id'] && $order_detailarr['user_id'] > 0 && $used_wallet_balance > 0 && $used_wallet_balance != NULL) {
                $new_wallet_balance = $subtotalnew;
                $wallet_to_be_refunded = $used_wallet_balance - $subtotalnew;
                //update wallet history
                $update_wallet = array('amount' => $new_wallet_balance);
                $this->order_model->updateMultipleWhere('wallet_history', array('order_id' => $entity_id, 'user_id' => $order_detailarr['user_id'], 'debit' => 1, 'is_deleted' => 0), $update_wallet);
                //update wallet amount
                $users_wallet = $this->order_model->getUsersWalletMoney($order_detailarr['user_id']);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $new_wallet_amount = $current_wallet + $wallet_to_be_refunded;
                $refund_wallet = array('wallet' => $new_wallet_amount);
                $this->order_model->updateData($refund_wallet, 'users', 'entity_id', $order_detailarr['user_id']);
            }
            //wallet changes :: end
            //Code for tax :: Start
            $tax_rateval=0;
            if(strtolower($tax_type)=='percentage')
            {
               $tax_rateval = ($subtotalsave * $tax_rate)/100;
            }
            else
            {
                $tax_rateval = $tax_rate;
            }
            $tax_rateval = round($tax_rateval,2);
            //Code for tax :: End
            //Code for service fee :: Start
            $service_feeval=0;
            if(strtolower($service_fee_type)=='percentage')
            {
               $service_feeval = ($subtotalsave * $service_fee)/100;
            }
            else
            {
                $service_feeval = $service_fee;
            }
            $service_feeval = round($service_feeval,2);
            //Code for service fee :: End
            //Code for credit card fee :: Start
            $creditcard_feeval=0;
            if(strtolower($creditcard_fee_type)=='percentage')
            {
               $creditcard_feeval = ($subtotalsave * $creditcard_fee)/100;
            }
            else
            {
                $creditcard_feeval = $creditcard_fee;
            }
            $creditcard_feeval = round($creditcard_feeval,2);
            //Code for credit card fee :: End
            //Final total code
            $total_rate = ($subtotalnew + $service_feeval + $tax_rateval + $creditcard_feeval + $delivery_charge + $tip_amount) - ($new_wallet_balance+$coupon_discounttotal);

            //===============CODE FOR REFUND :: START===================
            //Code for refund reason :: Start
            $new_refund_reason = ($this->input->post('refund_reasontext'))?trim($this->input->post('refund_reasontext')):'';
            $old_refund_reason = $order_detailarr['refund_reason'];
            $refund_reason = ($new_refund_reason!='')?$new_refund_reason:'';
            if($old_refund_reason!='')
            {
                $refund_reason = ($refund_reason!='')?$refund_reason.'<br/>'.$old_refund_reason:$old_refund_reason;
            }
            //Code for refund reason :: End

            $order_total_new = $total_rate;
            $order_total_old = $order_detailarr['total_rate'];
            $refunded_amount = $order_total_old-$order_total_new;//use to refund the amount
            $refunded_amount = round($refunded_amount,2);

            $old_refunded_amount = floatval($order_detailarr['refunded_amount']);
            $new_refunded_amount = $old_refunded_amount+$refunded_amount; // use to sotre in db 
            //Code for calculate the refund amount :: End            

            //echo $old_refunded_amount."===".$new_refunded_amount."===".$refunded_amount; exit;
            //Code for refund with payment option :: Start
            if(strtolower($order_detailarr['payment_option'])=='stripe' || strtolower($order_detailarr['payment_option'])=='applepay')
            { 
                $payment_optionval = strtolower($order_detailarr['payment_option']);
                $response = $this->common_model->Stripe_PartialRefund($order_detailarr['transaction_id'],$entity_id,$refunded_amount,$new_refunded_amount,$new_refund_reason,$payment_optionval);
            }
            else if(strtolower($order_detailarr['payment_option'])=='paypal')
            {
                $response = $this->common_model->Paypal_PartialRefund($order_detailarr['transaction_id'],$entity_id,$refunded_amount,$new_refunded_amount,$new_refund_reason);
            }
            if($response['error']=='yes')
            {
                echo json_encode($response); exit;
            }
            else
            {
                //Code for order update notification via mail OR SMS :: Start
                $payment_methodarr = array('stripe','paypal','applepay');
                $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                $order_refund_text = '';
                if(in_array(strtolower($order_detailarr['payment_option']), $payment_methodarr))
                {
                    $default_currency = get_default_system_currency();
                    $refunded_amountdis = currency_symboldisplay(number_format_unchanged_precision($refunded_amount,$default_currency->currency_code),$default_currency->currency_symbol);                    
                    $order_refund_text = sprintf($this->lang->line('order_refund_text'),$refunded_amountdis);
                }
                $user_email_id = ($order_detailarr['email'])?trim($order_detailarr['email']):'';
                $user_email_idtemp = ($order_detailarr['user_detail']['email'])?trim($order_detailarr['user_detail']['email']):'';
                $order_username = ($order_detailarr['user_detail']['first_name'])?trim($order_detailarr['user_detail']['first_name']).' '.trim($order_detailarr['user_detail']['last_name']):'';
                if($user_email_id!='' || $user_email_idtemp!='')
                {
                    if($user_email_id==''){
                        $user_email_id = $user_email_idtemp;
                    }
                }

                //Mail send code start
                if($user_email_id!='')
                {
                    $language_slug = $this->session->userdata('language_slug');
                    $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-updated','language_slug'=>$language_slug,'status'=>1))->first_row();
                                        
                    $arrayData = array('FirstName'=>$order_username,'order_id'=>$entity_id, 'updated_by'=>$updated_bytxt, 'order_refund_text'=>$order_refund_text);
                    $EmailBody = generateEmailBody($email_template->message,$arrayData);

                    //get System Option Data
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                    if(!empty($EmailBody))
                    {     
                        $this->load->library('email');  
                        $config['charset'] = 'iso-8859-1';  
                        $config['wordwrap'] = TRUE;  
                        $config['mailtype'] = 'html';  
                        $this->email->initialize($config);  
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                        $this->email->to($user_email_id); 
                        $this->email->subject($email_template->subject);  
                        $this->email->message($EmailBody);  
                        $this->email->send(); 
                    }
                }//End :: SMS send code start
                else
                {
                    $sms = 'Your order#'.$entity_id.' has been updated by '.$updated_bytxt;
                    if($order_refund_text!='')
                    {
                        $sms = $sms.'. '.$order_refund_text;
                    }
                    $mobile_numberT = ($order_detailarr['phone_code'])?$order_detailarr['phone_code']:'+1';
                    $mobile_numberT = $mobile_numberT.$order_detailarr['mobile_number'];
                    if($mobile_numberT == '' || $mobile_numberT == '+1') {
                        $mobile_numberT = ($order_detailarr['user_mobile_number']) ? '+'.$order_detailarr['user_mobile_number'] : '';
                    }
                    if($mobile_numberT != '' && $mobile_numberT != '+1') {
                      $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);  
                    }
                }//End
                //Code for order update notification via mail OR SMS :: End

                //Code save for refund log
                $resname = $this->common_model->getResNameWithOrderId($entity_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' partial refunded for order - '.$entity_id.' (ordered from: '.$resname.')');
            }
            //Code for refund with payment option :: End
            //===============CODE FOR REFUND :: END===================

            //Code for update order item :: Start
            $update_order = array(
                'item_detail' => serialize($item_detailup),
                'is_updateorder' => '1'
            );
            $this->order_model->updateData($update_order,'order_detail','order_id',$entity_id); 
            //Code for update order item :: end
            //Code for update order master :: Start
            $update_data = array(              
                    'total_rate'=>$total_rate,
                    'subtotal'=>$subtotalsave,
                    'coupon_discount'=>$coupon_discount,
                    'refund_reason'=>$refund_reason,
                    'used_earning' => $new_wallet_balance,
                    'updated_by'=>$this->session->userdata("AdminUserID"),
                    'updated_date'=>date('Y-m-d H:i:s')
                );
            $this->order_model->updateData($update_data,'order_master','entity_id',$entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited an order - '.$entity_id);
            //Code for update order master :: End
            //Code for order item remove notification send to user :: Start
            if($order_detailarr['notification'] == 1)
            {
                $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$order_detailarr['language_slug']))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                $message = $this->lang->line('order_item_rejected1').' '.$del_itmename.' '.$this->lang->line('order_item_rejected2');
                $device_id = $order_detailarr['device_id'];
                $this->sendFCMRegistration($device_id, $message, 'rejected', $restaurant_id, FCM_KEY,'DineIn',$order_detailarr['paid_status']);
            }
            //Code for order item remove notification send to user :: End
        }
        echo json_encode('success'); exit;      
    }
    //Method for deleting order item :: end
    public function ajax_dinein_order_update_notification(){
        //get all order details record with is_update 0
        $orders = $this->order_model->get_is_update_order_detail();
        if($orders){
            echo json_encode($orders);
        }
    }
    public function mark_as_read_dinein_notification(){
        if($this->input->post('entity_id')){
            $result = $this->order_model->update_is_update_attribute($this->input->post('entity_id'));
        }
        
        if($result >  0){
            echo json_encode($result);
        }
    }
    public function checkExist()
    {
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        $alldata = ($this->input->post('alldata') != '')?$this->input->post('alldata'):'no';
        $htmlval = '';
        if($mobile_number != '')
        {
            $check = $this->order_model->getusers($mobile_number,$alldata);
            if($check && !empty($check))
            {
                if($alldata=='yes')
                {
                    $htmlval = json_encode($check);
                }
                else
                {
                    $checkval = array();
                    foreach ($check as $key => $value)
                    {
                        $checkval[$value->mobile_number] = $value->mobile_number.' ('.ucfirst($value->first_name).' '.$value->last_name.')';
                        if($value->first_name=='' && $value->last_name=='')
                        {
                            $checkval[$value->mobile_number] = $value->mobile_number;
                        }                      
                    }
                    $htmlval = json_encode($checkval);
                    //Code for pass only mobile no
                    /*$checkval = array_column($check, 'mobile_number');
                    $htmlval = json_encode($checkval);*/
                }
            }
        }
        echo $htmlval;
    }
    //get restaurant tables
    public function getTable()
    {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id)
        {
           $restaurant_content_id = $this->common_model->getContentId($entity_id,'restaurant');           
           $result =  $this->order_model->getTables($restaurant_content_id);
                $html = '<option value="">'.$this->lang->line('select').'</option>';
           foreach ($result as $key => $value) {
                $html .= '<option value="'.$value->entity_id.'">'.$value->table_number.'</option>';
           }
        }
        echo $html;
    }
    //Code for dine in Order feature :: End
    public function show_cancel_reason(){
        $language = ($this->input->post('language') != '') ? $this->input->post('language') : '';
        if(!empty($language)){
            $reasons = $this->order_model->cancel_reject_reasons($language);
            $html = '<option value="">'.$this->lang->line('select').'</option>';
            foreach ($reasons as $reason) {
                $html .= '<option value="'.$reason->reason.'">'.$reason->reason.'</option>';
            }
            $html .= '<option value="other">'.$this->lang->line('other').'</option>';
            echo $html;
        }
    }
    public function show_reject_reason(){
        $language = ($this->input->post('language') != '') ? $this->input->post('language') : '';
        if(!empty($language)){
            $reasons = $this->order_model->cancel_reject_reasons($language, 'reject');
            $html = '<option value="">'.$this->lang->line('select').'</option>';
            foreach ($reasons as $reason) {
                $html .= '<option value="'.$reason->reason.'">'.$reason->reason.'</option>';
            }
            $html .= '<option value="other">'.$this->lang->line('other').'</option>';
            echo $html;
        }
    }
    public function add_new_user() {
        $arr_data = array('msg'=>'', 'status'=>0);
        if($this->input->post('submit_adduser') == "Submit") {
            $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
            $this->form_validation->set_rules('mobile_number','Phone Number', 'trim|required|numeric|callback_checkExistPhnNoCallbackfn');
            $this->form_validation->set_rules('email','Email', 'trim|required|callback_checkEmailExistCallbackfn');
            $this->form_validation->set_rules('address_field', 'Address', 'trim|required');
            $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            if ($this->form_validation->run()) {  
                $phone_code = $this->input->post('phone_code');
                if(!empty($phone_code))
                {
                    if(!strstr($phone_code,"+"))
                    {
                        $phone_code = $phone_code;
                    }
                }
                $user_password = generate_user_password();
                $add_data = array(
                    'first_name'=>$this->input->post('first_name'),
                    'last_name'=>$this->input->post('last_name'),
                    'phone_code' =>$phone_code,
                    'mobile_number' =>$this->input->post('mobile_number'),
                    'email' =>$this->input->post('email'),
                    'user_type' =>'User',
                    'status' =>1,
                    'active' =>1,
                    'password' =>md5(SALT.$user_password),
                    'created_by'=>$this->session->userdata("AdminUserID")
                );
                $userid = $this->order_model->addData('users',$add_data);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added User - '.$this->input->post('first_name').' '.$this->input->post('last_name'));
                if($userid){
                    $address_add = array(
                        'user_entity_id'=>$userid,
                        'address'=>$this->input->post('address_field'),
                        'zipcode'=>$this->input->post('zipcode'),
                        'latitude'=>$this->input->post('latitude'),
                        'longitude'=>$this->input->post('longitude'),
                        'city'=>$this->input->post('city'),
                    );
                    $address_id = $this->order_model->addData('user_address',$address_add);
                    /*Begin::send an email*/
                    if($this->input->post('email')){
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'user-added','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                        $arrayData = array('FirstName'=>$this->input->post('first_name'),'Email'=>$this->input->post('email'),'Password'=>$user_password);
                        $EmailBody = generateEmailBody($Emaildata->message,$arrayData);
                        if(!empty($EmailBody)){
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                            $this->email->to(trim($this->input->post('email')));
                            $this->email->subject($Emaildata->subject);
                            $this->email->message($EmailBody);
                            $this->email->send();
                        }
                    }
                    /*End::send an email*/
                    //send password in sms start 
                    //get System Option Data
                    $this->db->select('OptionValue');
                    $app_store = $this->db->get_where('system_option',array('OptionSlug'=>'app_store_url'))->first_row();
                    $app_store_url_shortened = $app_store->OptionValue;
                    //get System Option Data
                    $this->db->select('OptionValue');
                    $play_store = $this->db->get_where('system_option',array('OptionSlug'=>'playstore_url'))->first_row();
                    $playstore_url_shortened = $play_store->OptionValue;
                    $user_record = $this->order_model->getRecord('users','entity_id',$userid);
                    $sms = $this->lang->line('credentials_to_login')."\n".$this->lang->line('phone_number').' : '.$user_record->mobile_number."\n".$this->lang->line('email').' : '.$user_record->email."\n".$this->lang->line('password').' : '.$this->input->post('password')."\n \n".$this->lang->line('visit_our_website').base_url()."\n".$this->lang->line('dnld_our_apps')."\n".$this->lang->line('playtore').$playstore_url_shortened."\n".$this->lang->line('appstore').$app_store_url_shortened;
                    $mobile_numberT = ($user_record->phone_code)?$user_record->phone_code:'+1';
                    $mobile_numberT = $mobile_numberT.$user_record->mobile_number;
                    $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);
                    //send password in sms end 
                    $arr_data = array('user_id'=>$user_record->entity_id,'first_name'=>$user_record->first_name,'last_name'=>$user_record->last_name,'phone_code'=>$user_record->phone_code,'mobile_number'=>$mobile_numberT,'email'=>$user_record->email,'msg'=>$this->lang->line('success_add'), 'status'=>1,'sms'=>$sms);
                }
            }
        }
        echo json_encode($arr_data); 
    }
    public function checkExistPhnNo(){
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        $phone_code = ($this->input->post('phone_code') != '')?$this->input->post('phone_code'):'';
        if($mobile_number != ''){
            $check = $this->order_model->checkExistPhnNo($mobile_number,$phone_code);
            echo json_encode($check); 
        }
        else
        {
            $check = array('numrows'=>0);
            echo json_encode($check);
        }       
    }
    public function checkExistPhnNoCallbackfn(){
        $mobile_number = ($this->input->post('mobile_number') != '')?$this->input->post('mobile_number'):'';
        $phone_code = ($this->input->post('phone_code') != '')?$this->input->post('phone_code'):'';
        if($mobile_number != ''){
            $check = $this->order_model->checkExistPhnNo($mobile_number,$phone_code);
            if($check['numrows']>0){
              $this->form_validation->set_message('checkExistPhnNoCallbackfn',$this->lang->line('number_already_registered'));
              return FALSE;
            }
            else{
              return TRUE;
            }
        }
        else
        {
            return TRUE;
        }       
    }
    public function checkEmailExistCallbackfn(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
        if($email != ''){
            $check = $this->order_model->checkEmailExist($email);
            if($check['numrows']>0){
              $this->form_validation->set_message('checkEmailExistCallbackfn',$this->lang->line('user_email_exist_error_msg'));
              return FALSE;
            }
            else{
              return TRUE;
            }
        }
        else
        {
            return TRUE;
        }         
    }
    // get lat long from the address
    public function getAddressLatLng(){
        $latlong = array();
        if (!empty($this->input->post('entity_id'))) {
            $latlong = $this->order_model->getAddressLatLng($this->input->post('entity_id'));
        }
        echo json_encode($latlong);
    }
    // get the delivery charges
    public function getDeliveryCharges(){ 
        $check = '';
        if (!empty($this->input->post('action')) && $this->input->post('action') == "get") { 
            if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) { 
                $cart_restaurant = $this->input->post('restaurant_id'); 
                $check = $this->checkGeoFence($this->input->post('latitude'),$this->input->post('longitude'),$price_charge = true,$cart_restaurant);
                //get System Option Data
                $this->db->select('OptionValue');
                $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
                $min_order_amount = (float) $min_order_amount->OptionValue;
                 //cart_total
                if ($check['price_charge']) {
                    //depends on subtotal and min order amount
                    $additional_delivery_charge = ($check['additional_delivery_charge'])?$check['additional_delivery_charge']:0;
                    //based on location
                    $exist_delivery_charge = ($check['price_charge'])?$check['price_charge']:0;
                    if($this->input->post('cart_total') >= $min_order_amount) {
                        $deliveryCharge = $exist_delivery_charge;
                    } else {
                        $deliveryCharge = $exist_delivery_charge + $additional_delivery_charge;
                    }
                    $delivery_arr = array('checkDelivery' => 'available','deliveryCharge' => $deliveryCharge);
                }
                else
                {
                    $delivery_arr = array('checkDelivery' => 'notAvailable','deliveryCharge' => 0);
                }
            }
        }
        if (!empty($this->input->post('action')) && $this->input->post('action') == "remove") { 
            $check = 0;
            $delivery_arr = array('checkDelivery' => 'pickup','deliveryCharge' => 0);
        }
        echo json_encode($delivery_arr);
    }
    //check lat long exist in area
    public function checkGeoFence($latitude,$longitude,$price_charge,$restaurant_id)
    {
        $result = $this->order_model->checkGeoFence($restaurant_id); 
        $latlongs =  array($latitude,$longitude);
        if (!empty($result)) {
            foreach ($result as $reskey => $resvalue) {
                $coordinatesArr = array();
                if (!empty($resvalue->lat_long)) {
                    $lat_longs =  explode('~', $resvalue->lat_long);
                    foreach ($lat_longs as $key => $value) {
                        $val = str_replace(array('[',']'),array('',''),$value);
                        $coordinatesArr[] =  explode(',', $val);
                    }
                }
                $output = $this->checktestFence($latlongs, $coordinatesArr, $resvalue->price_charge, $resvalue->additional_delivery_charge);
                if(!empty($output['price_charge'])) {
                    return $output;
                    exit;
                }
            }
        }
        return $output;
    }
    // check geo fence area
    public function checkFence($point, $polygon, $price_charge, $additional_delivery_charge)
    {
        if($polygon[0] != $polygon[count($polygon)-1])
                $polygon[count($polygon)] = $polygon[0];
        $j = 0;
        $oddNodes = '';
        $x = $point[1];
        $y = $point[0];
        $n = count($polygon);
        for ($i = 0; $i < $n; $i++)
        {
            $j++;
            if ($j == $n)
            {
                $j = 0;
            }
            if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
                $y)))
            {
                if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
                    $polygon[$i][1]) < $x)
                {
                    $oddNodes = 'true';
                }
            }
        }
        $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
        $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
        return $price_arr;
    }
    public function checkEmailExist(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';
        if($email != ''){
            $check = $this->order_model->checkEmailExist($email);
            echo json_encode($check); 
        }
        else
        {
            $check = array('numrows'=>0);
            echo json_encode($check);
        }         
    }
    public function notiToUser($order_id, $restaurant_id, $order_status, $order_mode){
        // adding notification for website
        $order_status_val = '';
        if ($order_status == "complete") {
            $this->common_model->deleteData('user_order_notification','order_id',$order_id);
            /*if($order_mode =='DineIn' || $order_mode =='PickUp')
            {
                $order_status_val = 'order_completed';
            }*/
            $order_status_val = 'order_completed';
        }else if($order_status == 'admin_order_created'){
            $order_status_val = 'admin_order_created';
        }
        // else if ($order_status == "preparing") {
        //     $order_status_val = 'order_preparing';
        // }
        else if ($order_status == "onGoing") {
            $order_status_val = 'order_ongoing';
        }
        else if ($order_status == "delivered") {
            $order_status_val = 'order_delivered';
            //Code for find the drvier id :: Start
            /*$driver_detail = $this->order_model->getAssignDrvier($order_id);
            if($driver_detail)
            { $driver_id = $driver_detail->driver_id; }*/
            //Code for find the drvier id :: End
        }
        else if ($order_status == "cancel") {
            $order_status_val = 'order_canceled';
        }
        else if ($order_status == "ready") {
            $order_status_val = 'order_served';
        }
        else if ($order_status == "accepted") {
            $order_status_val = 'order_accepted';
        }
        else if ($order_status == "orderready") {
            $order_status_val = 'order_ready';
        }

        if ($order_status_val != '') {
            $order_detail = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
            if($order_detail->user_id && $order_detail->user_id > 0) {    
                $notification = array(
                    'order_id' => $order_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => $order_status_val,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_order_notification',$notification);
            }
        }
        //get langauge
        $device = $this->order_model->getDevice($order_detail->user_id);
        if($device->notification == 1){
            $languages = $this->db->select('language_directory')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
            $this->lang->load('messages_lang', $languages->language_directory);
            $message = sprintf($this->lang->line($order_status_val),$order_id);
            /*if order status is cancled then append reason*/
            if($order_status_val == 'order_canceled'){
                $message = sprintf($this->lang->line('order_canceled'),$order_id).'-'.$cancel_reason;
            }
            if($order_status_val == 'order_ongoing')
            {
                if($order_mode =='DineIn')
                {
                   $message = sprintf($this->lang->line('food_is_ready_notification'),$order_id);
                }
                else if($order_mode =='PickUp')
                {
                    $message = sprintf($this->lang->line('order_ready_notification'),$order_id);
                }
                else if($order_mode =='Delivery')
                {
                    $message = sprintf($this->lang->line('on_going_notification'),$order_id);
                }
            }
            else if($this->input->post('order_status') == "ready" || $this->input->post('order_status') == "orderready")
            {
                if($order_mode =='DineIn')
                { 
                   $message = $this->lang->line('order_served');
                }
                else if($order_mode =='PickUp')
                {
                    $message = sprintf($this->lang->line('order_ready_notification'),$order_id);
                }
            }
            if($order_status == 'admin_order_created'){
                $message = $this->lang->line('admin_order_created').' '.$this->lang->line('admin_order_created_1').$order_id;
            }
            $device_id = $device->device_id;
            $this->sendFCMRegistration($device_id,$message,$order_status,$restaurant_id,FCM_KEY,$order_mode,$order_detail->paid_status,$order_id,$driver_id);
        }
    }
    public function getdeliveryoption()
    {
        $order_mode = ($this->input->post('order_mode'))?$this->input->post('order_mode'):'';
        $order_status = order_status($this->session->userdata('language_slug'));
        unset($order_status['placed']);
        unset($order_status['cancel']);
        unset($order_status['rejected']);
        if($order_mode!='delivery')
        {
            unset($order_status['onGoing']);
            unset($order_status['delivered']);    
        }
        else
        {
            unset($order_status['orderready']);
        }        
        $html = '<option value="">'.$this->lang->line('select').'</option>';
        foreach ($order_status as $key => $value)
        {               
            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
        echo $html; exit;
    }
    // get address from lat long
    function getAddressFromLatLong(){ 
        $latitude = trim($this->input->post('latitude'));
        $longitude = trim($this->input->post('longitude'));
        $address_arr = array('address'=>'','city' => '','zipcode' => '');
        if(!empty($latitude) && !empty($longitude)){
            //Send request and receive json data by address
            $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&key='.google_key);

            $output = json_decode($geocodeFromLatLong);
            $status = $output->status;
            //Get address from json data
            $address = ($status=="OK")?(($output->results[0]->formatted_address)?$output->results[0]->formatted_address:$output->results[1]->formatted_address):'';
            //Return address of the given latitude and longitude
            if(!empty($address)) {
                $address_arr = array('address'=>$address,'city' => '','zipcode' => '');
                foreach ($output->results[0]->address_components as $key => $value) {
                    foreach ($value->types as $c_key => $c_value) {
                        if($c_value=='administrative_area_level_2'){
                            $address_arr = array('address'=>$address,'city' => $value->long_name,'zipcode' => '');
                        }
                        if($c_value=='postal_code'){
                            $address_arr['zipcode'] = $value->long_name;
                        }
                    }
                }
                if ($address_arr['city'] == '' || $address_arr['zipcode'] == '') {
                    foreach ($output->results[1]->address_components as $key1 => $value1) {
                        foreach ($value1->types as $c_key1 => $c_value1) {                      
                            if($address_arr['city'] == '' && $c_value1=='administrative_area_level_2'){
                                $address_arr['city'] = $value1->long_name;
                            }
                            if($address_arr['zipcode'] == '' && $c_value1=='postal_code'){
                                $address_arr['zipcode'] = $value1->long_name;
                            }
                        }
                    }
                }
            } else {
                $address_arr = array('address'=>'','city' => '','zipcode' => '');
            }
        } else {
            $address_arr = array('address'=>'','city' => '','zipcode' => '');
        }
        echo json_encode($address_arr);
    }
    //delivery zone issue :: start
    //test function to check a point in polygon
    public function checktestFence($latlongs, $coordinatesArr, $price_charge, $additional_delivery_charge){
        $oddNodes = '';
        $inside_polygon = $this->inside_polygon($latlongs, $coordinatesArr); //check 4 :: ray casting 
        if($inside_polygon == 1) {
            //check 3 :: start
            $polygon = $coordinatesArr;
            if($polygon[0] != $polygon[count($polygon)-1])
                $polygon[count($polygon)] = $polygon[0];
            
            $j = $i = 0;
            $x = $latlongs[1];
            $y = $latlongs[0];
            $n = count($polygon);
            
            for ($i = 0; $i < $n; $i++)
            {
                $j++;
                if ($j == $n)
                {
                    $j = 0;
                }
                if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
                    $y)))
                {
                    if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
                        $polygon[$i][1]) < $x)
                    {
                        $oddNodes = 'true';
                    }
                }
            }
            $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
            $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
            return $price_arr;
            //check 3 :: end
        }
        $oddNodes = ($oddNodes)?$price_charge:$oddNodes;
        $price_arr = array('price_charge'=>$oddNodes ,'additional_delivery_charge'=>$additional_delivery_charge);
        return $price_arr;
    }
    //ray casting algo :: to check if point is inside polygon (check 4)
    function inside_polygon($test_point, $points) {
        $p0 = end($points);
        $ctr = 0;
        foreach ( $points as $p1 ) {
    
            // there is a bug with this algorithm, when a point in "on" a vertex
            // in that case just add an epsilon
            if ($test_point[1] == $p0[1])
                $test_point[1]+=0.0000000001; #epsilon
        
            // ignore edges of constant latitude (yes, this is correct!)
            if ( $p0[1] != $p1[1] ) {
                // scale latitude of $test_point so that $p0 maps to 0 and $p1 to 1:
                $interp = ($test_point[1] - $p0[1]) / ($p1[1] - $p0[1]);
        
                // does the edge intersect the latitude of $test_point?
                // (note: use >= and < to avoid double-counting exact endpoint hits)
                if ( $interp >= 0 && $interp < 1 ) {
                    // longitude of the edge at the latitude of the test point:
                    // (could use fancy spherical interpolation here, but for small
                    // regions linear interpolation should be fine)
                    $long = $interp * $p1[0] + (1 - $interp) * $p0[0];
                    // is the intersection east of the test point?
                    if ( $long > $test_point[0] ) {
                        // if so, count it:
                        $ctr++;
        #echo "YES &$test_point[0],$test_point[1] ($p0[0],$p0[1])x($p1[0],$p1[1]) ; $interp,$long","\n";
                    }
                }
            }
            $p0 = $p1;
        }
        return ($ctr & 1);
    }
    //delivery zone issue :: end

    //Code for Delivery/Pickup Order Itemd Update :: Start
    public function edit_delivery_pickup_order_details()
    {
        $data['meta_title'] = $this->lang->line('title_admin_delivery_pickup_order_edit').' | '.$this->lang->line('site_title');
        $language_slug = $this->session->userdata('language_slug');
        $entity_id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4)));
        $order_detailarr = $this->order_model->get_delivery_pickup_order($entity_id);
        $order_id = ($this->input->post('entity_id'))?$this->input->post('entity_id'):$entity_id;
        if($this->input->post('submit_page') == "Submit")
        {
            //wallet changes :: start
            $new_wallet_balance = 0;
            if($this->input->post('wallet_history') && $this->input->post('wallet_history') > 0) {
                $new_wallet_balance = $this->input->post('wallet_history');
                $wallet_to_be_refunded = $this->input->post('wallet_to_be_refunded');
                //update wallet history
                $update_wallet = array('amount' => $new_wallet_balance);
                $this->order_model->updateMultipleWhere('wallet_history', array('order_id' => $order_id, 'user_id' => $order_detailarr['user_id'], 'debit' => 1, 'is_deleted' => 0), $update_wallet);
                //update wallet amount
                $users_wallet = $this->order_model->getUsersWalletMoney($order_detailarr['user_id']);
                $current_wallet = $users_wallet->wallet; //money in wallet
                $new_wallet_amount = $current_wallet + $wallet_to_be_refunded;
                $refund_wallet = array('wallet' => $new_wallet_amount);
                $this->order_model->updateData($refund_wallet, 'users', 'entity_id', $order_detailarr['user_id']);
            }
            //wallet changes :: end
            //Code for refund reason :: Start
            $new_refund_reason = ($this->input->post('itemrefund_reason'))?trim($this->input->post('itemrefund_reason')):'';
            $old_refund_reason = $order_detailarr['refund_reason'];            
            $refund_reason = ($new_refund_reason!='')?$new_refund_reason:'';
            if($old_refund_reason!='')
            {
                $refund_reason = ($refund_reason!='')?$refund_reason.'<br/>'.$old_refund_reason:$old_refund_reason;
            }
            //Code for refund reason :: End            
            //Code for calculate the refund amount :: Start
            $order_total_new = ($this->input->post('total_rate'))?$this->input->post('total_rate'):0;
            $order_total_old = ($this->input->post('total_rate_old'))?$this->input->post('total_rate_old'):0;
            $refunded_amount = $order_total_old-$order_total_new;//use to refund the amount
            $refunded_amount = round($refunded_amount,2);
            $old_refunded_amount = floatval($order_detailarr['refunded_amount']);
            $new_refunded_amount = $old_refunded_amount+$refunded_amount; // use to sotre in db 
            //Code for calculate the refund amount :: End

            $items = $order_detailarr['item_detail'];
            $itemoutofstock=0;
            foreach ($items as $key => $value)
            { 
                if($items[$key]!='')
                {
                    $Is_itemupdate = 'yes';
                    $itemTotal = 0;                    
                    $item_detail = $this->order_model->getMenuDetail($value['item_id'],$language_slug,$this->input->post('restaurant_id'));
                    if(empty($item_detail)){
                        $itemoutofstock++;
                    }
                }
            }
            if($itemoutofstock>0){
                // $this->session->set_flashdata('outofstock', $this->lang->line('outofstock_text').'.');
                $_SESSION['outofstock'] = $this->lang->line('outofstock_text').'.';
            }
            else
            {
                //Code for refund with payment option :: Start
                $response['error'] = '';
                if(strtolower($order_detailarr['payment_option'])=='stripe' || strtolower($order_detailarr['payment_option'])=='applepay')
                {
                    $payment_optionval = strtolower($order_detailarr['payment_option']);
                    $response = $this->common_model->Stripe_PartialRefund($order_detailarr['transaction_id'],$order_id,$refunded_amount,$new_refunded_amount,$new_refund_reason,$payment_optionval);
                }
                else if(strtolower($order_detailarr['payment_option'])=='paypal')
                {
                    $response = $this->common_model->Paypal_PartialRefund($order_detailarr['transaction_id'],$order_id,$refunded_amount,$new_refunded_amount,$new_refund_reason);
                }
                if($response['error']=='yes')
                {
                    $_SESSION['outofstock'] = $this->lang->line('admin_refund_failed')."<br>".$response['error_message'];
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_delivery_pickup_order_details/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($entity_id)));                    
                }
                else
                {
                    //Code for order update notification via mail OR SMS :: Start
                    $payment_methodarr = array('stripe','paypal','applepay');
                    $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                    $order_refund_text = '';
                    if(in_array(strtolower($order_detailarr['payment_option']), $payment_methodarr))
                    {
                        $default_currency = get_default_system_currency();
                        $refunded_amountdis = currency_symboldisplay(number_format_unchanged_precision($refunded_amount,$default_currency->currency_code),$default_currency->currency_symbol);                    
                        $order_refund_text = sprintf($this->lang->line('order_refund_text'),$refunded_amountdis);
                    }
                    $user_email_id = ($order_detailarr['email'])?trim($order_detailarr['email']):'';
                    $user_email_idtemp = ($order_detailarr['user_detail']['email'])?trim($order_detailarr['user_detail']['email']):'';
                    $order_username = ($order_detailarr['user_detail']['first_name'])?trim($order_detailarr['user_detail']['first_name']).' '.trim($order_detailarr['user_detail']['last_name']):'';
                    if($user_email_id!='' || $user_email_idtemp!='')
                    {
                        if($user_email_id==''){
                            $user_email_id = $user_email_idtemp;
                        }
                    }
                    //Code for order update notification via mail OR SMS :: End
                }
                //Code for refund with payment option :: End

                //Code for set item array with post detail :: Start
                $itematemp = array();
                $item_idtemp = ($this->input->post('item_id'))?$this->input->post('item_id'):array();
                $qty_notemp = ($this->input->post('qty_no'))?$this->input->post('qty_no'):array();
                $old_qty_notemp = ($this->input->post('old_qty_no'))?$this->input->post('old_qty_no'):array();
                $item_ratetemp = ($this->input->post('rate'))?$this->input->post('rate'):array();
                $base_pricetemp = ($this->input->post('base_price'))?$this->input->post('base_price'):array();
                $order_updateflg = 'no';
                if($item_idtemp && !empty($item_idtemp))
                {
                    foreach($item_idtemp as $item_key => $item_value)
                    {
                        $itematemp[$item_value]['qty_no'] = $qty_notemp[$item_key];
                        $itematemp[$item_value]['rate'] = $item_ratetemp[$item_key];
                        $itematemp[$item_value]['base_price'] = $base_pricetemp[$item_key];
                        if($qty_notemp[$item_key]<$old_qty_notemp[$item_key]){
                          $order_updateflg = 'yes';  
                        }
                    }
                }
                //Code for set item array with post detail :: End

                $item_detail_old = $order_detailarr['item_detail'];
                //Code for fid the last order flag :: Start
                if(!empty($item_detail_old))
                {
                    $order_flaglast = max(array_column($item_detail_old, 'order_flag'));                                      
                    if($order_updateflg=='yes'){
                        $order_flaglast = intval($order_flaglast)+1;    
                    }
                }
                //Code for fid the last order flag :: End
                
                //New added item detail :: Start
                $item_detailup = $item_detail_old;
                $Is_itemupdate = 'no';
                $item_namemsg = '';
                $ordcnt = 0;
                foreach ($items as $key => $value)
                {
                    if($items[$key]!='')
                    {
                        $Is_itemupdate = $order_updateflg;
                        $itemTotal = 0;
                        $item_detail = $this->order_model->getMenuDetail($value['item_id'],'',$this->input->post('restaurant_id'));

                        //base price changes start
                        $itemTotal = ($item_detail->price)? $itemTotal + $item_detail->price : $itemTotal;
                        //base price changes end
                        
                        if($value['qty_no']>$itematemp[$value['item_id']]['qty_no']){
                            $item_namemsg .= $item_detail->name.', ';    
                        }
                        
                        //if customized item
                        if($item_detail->check_add_ons == '1')
                        {
                            if($value['addons_category_list'] && !empty($value['addons_category_list']))
                            {
                                $customization=array();
                                foreach ($value['addons_category_list'] as $addon_key => $addon_value)
                                {
                                    $addonscust = array(); // for addons items
                                    $catvalue = $addon_value['addons_list'];                                    
                                    foreach ($catvalue as $addkey => $addonvalue)
                                    {
                                        $addonscust[] = array(
                                            'add_ons_id'=>$addonvalue['add_ons_id'],
                                            'add_ons_name'=>$addonvalue['add_ons_name'],
                                            'add_ons_price'=>$addonvalue['add_ons_price']
                                        );                                        
                                    }

                                    $customization[] = array(
                                        'addons_category_id'=>$addon_value['addons_category_id'],
                                        'addons_category'=>$addon_value['addons_category'],
                                        'addons_list'=>$addonscust
                                    );
                                }
                            }
                            $item_detailup[$ordcnt] = array(
                                "item_name"=>$value['item_name'],
                                "menu_content_id"=>$value['menu_content_id'],
                                "item_id"=>$value['item_id'],
                                "qty_no"=>$itematemp[$value['item_id']]['qty_no'],
                                "comment"=>$value['comment'],
                                "rate"=>$value['rate'],
                                "offer_price"=>$value['offer_price'],
                                "order_id"=>$order_id,
                                "is_customize"=>1,
                                "is_combo_item"=>0,
                                "combo_item_details" => '',
                                "subTotal"=>$itematemp[$value['item_id']]['base_price'],
                                "itemTotal"=>$itematemp[$value['item_id']]['rate'],
                                "order_flag"=>$order_flaglast,
                                "addons_category_list"=>$customization
                            );
                        }
                        else
                        {
                            $itemTotal = ($this->input->post('qty_no')[$key]*$item_detail->price);
                            $item_detailup[$ordcnt] = array(
                                "item_name"=>$value['item_name'],
                                "menu_content_id"=>$value['menu_content_id'],
                                "item_id"=>$value['item_id'],
                                "qty_no"=>$itematemp[$value['item_id']]['qty_no'],
                                "comment"=>$value['comment'],
                                "rate"=>$value['rate'],
                                "offer_price"=>$value['offer_price'],
                                "order_id"=>$order_id,
                                "is_customize"=>0,
                                "is_combo_item"=>$value['is_combo_item'],
                                "combo_item_details"=> $value['combo_item_details'],
                                "subTotal"=>$itematemp[$value['item_id']]['base_price'],
                                "itemTotal"=>$itematemp[$value['item_id']]['rate'],
                                "order_flag"=>$order_flaglast,
                            );
                        }
                        $ordcnt++;
                    }                
                }
                //New added item detail :: End
                
                if($Is_itemupdate=='yes')
                {
                    //Code for update order item :: Start
                    $update_order = array(
                        'item_detail' => serialize($item_detailup),
                        'is_updateorder' => '1'
                    );
                    $this->order_model->updateData($update_order,'order_detail','order_id',$this->input->post('entity_id')); 
                    //Code for update order item :: End

                    //Code for coupon :: Code change as per multiple coupon :: Start
                    $coupon_discount = $coupon_discountup = 0;
                    $coupon_id = $this->input->post('coupon_id');
                    $coupon_typetemp = $this->input->post('coupon_type');
                    $coupon_amounttemp = $this->input->post('coupon_amount');
                    $coupon_array = array();
                    if($coupon_id && !empty($coupon_id))
                    {
                        foreach ($coupon_id as $cp_key => $cp_value)
                        {
                            $coupon_type = $coupon_typetemp[$cp_key];                    
                            $coupon_amount = $coupon_amounttemp[$cp_key];                    
                            if(strtolower($coupon_type)=='percentage' && $coupon_amount>0)
                            {
                               $coupon_discountup = round(($this->input->post('subtotal') * $coupon_amount)/100,2);
                            }
                            else
                            {
                                $coupon_discountup = $coupon_amount;
                            }
                            if($cp_key==0)
                            {
                                $coupon_discount = $coupon_discountup;
                            }
                            $coupon_uparray = array(
                                'coupon_discount'=>$coupon_discountup
                            );
                            $this->order_model->updateMultipleWhere('order_coupon_use', array('order_id'=>$this->input->post('entity_id'),'coupon_id'=>$cp_value), $coupon_uparray);
                        }
                    }
                    //Code for coupon :: Code change as per multiple coupon :: End

                    //Code for update order master :: Start
                    $update_data = array(              
                            'total_rate'=>$this->input->post('total_rate'),
                            'subtotal'=>$this->input->post('subtotal'),
                            'coupon_discount'=>$coupon_discount,
                            'refund_reason'=>$refund_reason,
                            'used_earning' => $new_wallet_balance,
                            'updated_by'=>$this->session->userdata("AdminUserID"),
                            'updated_date'=>date('Y-m-d H:i:s')
                    );                    
                    $this->order_model->updateData($update_data,'order_master','entity_id',$this->input->post('entity_id'));
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited an order - '.$entity_id);
                    //Code for update order master :: End

                    //Mail send code start
                    if($user_email_id!='')
                    {
                        $email_template = $this->db->get_where('email_template',array('email_slug'=>'order-updated','language_slug'=>$language_slug,'status'=>1))->first_row();
                                            
                        $arrayData = array('FirstName'=>$order_username,'order_id'=>$order_id, 'updated_by'=>$updated_bytxt, 'order_refund_text'=>$order_refund_text);
                        $EmailBody = generateEmailBody($email_template->message,$arrayData);

                        //get System Option Data
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                        if(!empty($EmailBody))
                        {     
                            $this->load->library('email');  
                            $config['charset'] = 'iso-8859-1';  
                            $config['wordwrap'] = TRUE;  
                            $config['mailtype'] = 'html';  
                            $this->email->initialize($config);  
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                            $this->email->to($user_email_id); 
                            $this->email->subject($email_template->subject);  
                            $this->email->message($EmailBody);  
                            $this->email->send(); 
                        }
                    }//End :: SMS send code start
                    else
                    {
                        $sms = 'Your order#'.$order_id.' has been updated by '.$updated_bytxt;
                        if($order_refund_text!='')
                        {
                            $sms = $sms.'. '.$order_refund_text;
                        }
                        $mobile_numberT = ($order_detailarr['phone_code'])?$order_detailarr['phone_code']:'+1';
                        $mobile_numberT = $mobile_numberT.$order_detailarr['mobile_number'];
                        if($mobile_numberT == '' || $mobile_numberT == '+1') {
                            $mobile_numberT = ($order_detailarr['user_mobile_number']) ? '+'.$order_detailarr['user_mobile_number'] : '';
                        }
                        if($mobile_numberT != '' && $mobile_numberT != '+1') {
                          $sms_data = $this->common_model->sendSmsApi($mobile_numberT,$sms);  
                        }
                    }//End
                    //Code save for refund log
                    $resname = $this->common_model->getResNameWithOrderId($order_id);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' partial refunded for order - '.$order_id.' (ordered from: '.$resname.')');
                    //Notification code :: Start
                    $user_id = $order_detailarr['user_id'];
                    $device = $this->order_model->getDevice($user_id);                    
                    if($device->notification == 1)
                    {
                        $message_motofication = sprintf($this->lang->line('delivery_pickup_order_update1'),$this->input->post('entity_id')).' '.$this->lang->line('delivery_pickup_order_update2').' ';
                        if($item_namemsg!='')
                        {
                            $item_namemsg = rtrim($item_namemsg, ", ");
                        }
                        $message_motofication = $message_motofication.$item_namemsg;
                        $device_id = $device->device_id;
                        $this->sendFCMRegistration($device_id,$message_motofication,$order_detailarr['order_status'],$order_detailarr['restaurant_id'],FCM_KEY,'order',$order_detailarr['paid_status']);
                    }
                    //Notification code :: End

                    /*Begin::Notification for website*/
                    if($order_detailarr['user_id'] && $order_detailarr['user_id'] > 0) {
                        $website_notification = array(
                            'order_id' => $order_detailarr['order_id'],
                            'user_id' => $order_detailarr['user_id'],
                            'notification_slug' => 'order_updated',
                            'view_status' => 0,
                            'datetime' => date("Y-m-d H:i:s"),
                        );
                        $this->common_model->addData('user_order_notification',$website_notification);
                    }
                    //notification to agent
                    if($order_detailarr['agent_id']){
                        $this->common_model->notificationToAgent($order_detailarr['order_id'], 'order_updated');
                    }
                    /*End::Notification for website*/

                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');
                }
                else
                {
                    // $this->session->set_flashdata('page_Error', $this->lang->line('item_editmsg'));
                    $_SESSION['page_Error'] = $this->lang->line('item_editerrormsg');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_delivery_pickup_order_details/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($entity_id)));  
                    //redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/dine_in_orders');
                }
            }
        }
        
        //get order        
        $data['editorder_detail'] = (object) $order_detailarr;        
        $data['wallet_history'] = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_detailarr['order_id'],'debit' => 1, 'is_deleted'=>0));
        $data['coupon_array'] = $this->common_model->getCoupon_array($order_detailarr['order_id']);               
        $this->load->view(ADMIN_URL.'/edit_delivery_pickup_order',$data); //dinein_order_detail
    }
    //Code for Delivery/Pickup Order Itemd Update :: End

    /*Begin::Print Receipt*/
    public function print_receipt()
    {
        $slug = $this->session->userdata('language_slug');
        $languages = $this->common_model->getFirstLanguages($slug);
        $this->lang->load('messages_lang', $languages->language_directory);
        $entity_id = $this->input->post('entity_id');
        if($entity_id){
            $data['order_records'] = $this->order_model->getEditDetail($entity_id);
            $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
            $data['wallet_history'] = $this->order_model->getRecordMultipleWhere('wallet_history',array('order_id' => $entity_id,'debit' => 1));
            // boost the memory limit if it's low ;)
            ini_set('memory_limit', '256M');
            $this->load->library('M_pdf');
            if($data['order_records']->is_printer_available == 1 && !is_null($data['order_records']->printer_paper_width) && $data['order_records']->printer_paper_width > 0 && !is_null($data['order_records']->printer_paper_height) && $data['order_records']->printer_paper_height > 0){
                //$mpdf = new mPDF('',array($data['order_records']->printer_paper_width,$data['order_records']->printer_paper_height));
                $mpdf = new \Mpdf\Mpdf(['format' => array($data['order_records']->printer_paper_width,$data['order_records']->printer_paper_height) ]);
            }else{
                //default printer paper height width
                //$mpdf = new mPDF('',array(72,250));
                $mpdf = new \Mpdf\Mpdf(['format' => [72, 250]]);
            }
            $mpdf->allow_charset_conversion=true; // Set by default to TRUE
            /*$mpdf->charset_in = 'UTF-8';*/
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            //$mpdf->SetAutoFont();
            $html = $this->load->view('backoffice/order_receipt',$data,true);

            if (!@is_dir('uploads/order_receipt')) {
                @mkdir('./uploads/order_receipt', 0777, TRUE);
            }
            $mpdf->WriteHTML($html);
            $output = 'uploads/order_receipt/'.$entity_id.'.pdf';
            $mpdf->output($output,'F');
            echo $output;
        }
    }
    /*End::Print Receipt*/
    public function ajaxinitiaterefund()
    {
        $order_id = $this->input->post('refund_order_id')?$this->input->post('refund_order_id'):'';
        $refund_reason = $this->input->post('refund_reason')?$this->input->post('refund_reason'):'';
        //Code for add the amount for refund :: Start
        $refund_full_partial = $this->input->post('partial_refundedchk')?$this->input->post('partial_refundedchk'):'full';
        $partial_refundedamt = $this->input->post('partial_refundedamt')?$this->input->post('partial_refundedamt'):0;        
        if($partial_refundedamt==0)
        {
            $refund_full_partial = 'full';
        }
        //Code for add the amount for refund :: End

        $response = array('error' => 'no');
        if(!empty($order_id))
        {
            $data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
            $payment_methodarr = array('stripe','paypal','applepay');
            //stripe refund amount
            if($data['order_records']->refund_status!='pending' && $data['order_records']->tips_refund_status!='pending')
            {
                if(($data['order_records']->transaction_id!='' && in_array(strtolower($data['order_records']->payment_option), $payment_methodarr) && $data['order_records']->refund_status!='refunded') || ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status!='refunded'))
                {
                    $transaction_id = ($data['order_records']->transaction_id!='' && ($data['order_records']->refund_status=='' || strtolower($data['order_records']->refund_status)=='partial refunded'))?$data['order_records']->transaction_id:'';
                    $tips_transaction_id = ($data['order_records']->tips_transaction_id!='' && $data['order_records']->tips_refund_status=='')?$data['order_records']->tips_transaction_id:'';
                    $tip_payment_option = ($data['order_records']->tip_payment_option!='' && $data['order_records']->tip_payment_option!=null)?$data['order_records']->tip_payment_option:'';
                    if($tip_payment_option=='' && $tips_transaction_id!='')
                    {
                        $tip_payment_option = 'stripe';
                    }                    
                    if(strtolower($data['order_records']->payment_option)=='stripe' || strtolower($data['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                    {
                        $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,$refund_full_partial,$partial_refundedamt);
                    }
                    else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                    { 
                        $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,$refund_reason,$refund_full_partial,$partial_refundedamt);                        
                    }                    
                    //send refund noti to user
                    if($data['order_records']->user_id && $data['order_records']->user_id > 0){
                        if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
                        {
                            //Code for when full order refund that time order stauts set cancel :: Start
                            if($refund_full_partial=='full' || $response['refundreturn_status']=='refunded')
                            {
                                $this->db->set('order_status','cancel')->where('entity_id',$order_id)->update('order_master');

                                $status_created_by = $this->session->userdata('AdminUserType');
                                $addData = array(
                                    'order_id'=>$order_id,
                                    'order_status'=>'cancel',
                                    'time'=>date('Y-m-d H:i:s'),
                                    'status_created_by'=>$status_created_by
                                );
                                $orderstatustbl_id = $this->order_model->addData('order_status',$addData);

                                $user_id = $data['order_records']->user_id;
                                if($user_id && $user_id > 0) {
                                    //wallet changes :: start
                                    //if order is cancelled both debit and credit should be removed from wallet history
                                    $users_wallet = $this->common_model->getUsersWalletMoney($user_id);
                                    $current_wallet = $users_wallet->wallet; //money in wallet
                                    $credit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
                                    $credit_amount = $credit_walletDetails->amount;
                                    $debit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
                                    $debit_amount = $debit_walletDetails->amount;
                                    $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
                                    $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
                                    //delete order_id from wallet history and update users wallet
                                    if(!empty($credit_amount) || !empty($debit_amount)){
                                        $this->common_model->deletewallethistory($order_id); // delete by order id
                                        $new_wallet = array(
                                            'wallet'=>$new_wallet_amount
                                        );
                                        $this->common_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
                                    }
                                    //wallet changes :: end
                                }
                            }
                            //Code for when full order refund that time order stauts set cancel :: End

                            //Mail send code Start
                            $language_slug = $this->session->userdata('language_slug');
                            $updated_bytxt = $this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname");
                            $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,$partial_refundedamt,$refund_full_partial,$updated_bytxt,$language_slug);
                            //Mail send code End

                            $this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);

                            //Code for save updated by and date value 
                            $update_array = array(
                                'updated_by' => $this->session->userdata("AdminUserID"),
                                'updated_date' => date('Y-m-d H:i:s')
                            );
                            $this->db->set($update_array)->where('entity_id',$order_id)->update('order_master');
                            //Code for save updated by and date value 
                            $resname = $this->common_model->getResNameWithOrderId($order_id);
                            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' initiated refund for an order - '.$order_id.' (ordered from: '.$resname.')');
                        }
                    }
                }
            }
        }
        if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
            $response['error_message'] = $this->lang->line('admin_refund_failed');
        }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
            $response['error_message'] = $this->lang->line('admin_refund_canceled');
        }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
            $response['error_message'] = $this->lang->line('refund_pending_err_msg');
        }
        echo json_encode($response);
    }
    public function getRestaurantDriver()
    {
        $order_id = ($this->input->post('order_id') != '')?$this->input->post('order_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $driver_id = ($this->input->post('driver_id') != '')?$this->input->post('driver_id'):'';
        $html = '<option value="">'.$this->lang->line('select').'</option>';
        if (intval($restaurant_id)> 0)
        {
            $restaurant_content_id = $this->order_model->getResContentId($restaurant_id);            
            $result =  $this->order_model->getRestaurantDriver($restaurant_content_id);            
            foreach ($result as $key => $value)
            {
                $class_selected = '';
                 if ($value->entity_id==$driver_id && $driver_id!=''){
                     $class_selected = 'selected';
                 }

                $bgdriveclr = 'green';
                $bgfaicon = "<i class='fa fa-user'></i>";
                if($value->ongoing=='yes')
                {
                    $bgdriveclr = 'red';
                    $bgfaicon = "<i class='fa fa-map-marker' aria-hidden='true'></i>";
                }

                $html .= '<option '.$class_selected.' value="'.$value->entity_id.'" style="background:'.$bgdriveclr.'; border: 1px solid grey; color:#fff;" data-content="'.$bgfaicon.' '.$value->first_name.' '.$value->last_name.'">'.$value->first_name.' '.$value->last_name.'</option>';
            }
            echo $html;
        }        
    }
} 
?>