<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Restaurant extends CI_Controller {
    public $controller_name = 'restaurant';
    public $prefix = '_re'; 
    public $menu_prefix = '_menu';
    public $package_prefix = '_pac';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/restaurant_model');
    }
    // view restaurant
    public function view() {
        if(in_array('restaurant~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_res').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            //restaurant count
            $this->db->select('content_id');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('content_id');
            $data['res_count'] = $this->db->get('restaurant')->num_rows();    
            $this->load->view(ADMIN_URL.'/restaurant',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // add restaurant
    public function add(){
        if(in_array('restaurant~add',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_restaurantadd').' | '.$this->lang->line('site_title');
            //get System Option Data
            /*$this->db->select('OptionValue');
            $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();*/
            if($this->input->post('submit_page') == "Submit")
            {   
                if($this->input->post('add_res_branch')=='res'){
                    $this->form_validation->set_rules('res_name', $this->lang->line('res_name'), 'trim|required|callback_checkResNameExist');
                }
                if($this->input->post('add_res_branch')=='branch'){
                    $this->form_validation->set_rules('branch_name', $this->lang->line('branch_name'), 'trim|required|callback_checkResNameExist');
                    $this->form_validation->set_rules('branch_entity_id', $this->lang->line('add_res_branch'), 'trim|required');
                }
                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                    $this->form_validation->set_rules('restaurant_owner_id', $this->lang->line('restaurant_owner'), 'trim|required|numeric');
                }
                $this->form_validation->set_rules('currency_id', $this->lang->line('currency'), 'trim|required');
                $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim'); //|callback_checkExist
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|callback_checkEmailExist');
                // $this->form_validation->set_rules('branch_admin_id', $this->lang->line('branch_admin'), 'trim|required|numeric');
                // $this->form_validation->set_rules('no_of_table', $this->lang->line('no_of_table'), 'trim|required');
                $this->form_validation->set_rules('address', $this->lang->line('address'), 'trim|required');
                $this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required');
                $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
                $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required');
                $this->form_validation->set_rules('city',$this->lang->line('city'), 'trim|required');
                $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required');
                // $this->form_validation->set_rules('enable_hours', $this->lang->line('enable_hours'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating', $this->lang->line('restaurant_rating'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating_count', $this->lang->line('restaurant_rating_count'), 'trim|required');
                // service fee changes start
                $this->form_validation->set_rules('message', $this->lang->line('about_restaurant'), 'trim|required');
                if($this->session->userdata('AdminUserType') == 'MasterAdmin') {
                    $this->form_validation->set_rules('amount_type', $this->lang->line('tax_amount'), 'trim|required');
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
                    $this->form_validation->set_rules('is_service_fee_enable',$this->input->post('is_service_fee_enable'), 'trim|required');
                    if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1'){
                        $this->form_validation->set_rules('service_fee_type',$this->lang->line('service_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('service_fee',$this->lang->line('service_fee'), 'trim|required');
                    }
                }
                // service fee changes end
                //print receipt changes :: start
                if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                    $this->form_validation->set_rules('printer_paper_height',$this->lang->line('printer_paper_height'), 'trim|required');
                    $this->form_validation->set_rules('printer_paper_width',$this->lang->line('printer_paper_width'), 'trim|required');
                }
                //print receipt changes :: end
                if($this->session->userdata('AdminUserType')=='MasterAdmin') {
                    $this->form_validation->set_rules('contractual_commission_type', $this->lang->line('contractual_commission_type'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission',$this->lang->line('contractual_commission'), 'trim|required|max_length[5]');
                    $this->form_validation->set_rules('contractual_commission_type_delivery', $this->lang->line('contractual_commission_type_delivery'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission_delivery',$this->lang->line('contractual_commission_delivery'), 'trim|required|max_length[5]');
                }
                $this->form_validation->set_rules('order_mode[]', $this->lang->line('order_mode'), 'trim|required|in_list[Delivery,PickUp]');

                if($this->input->post('allow_event_booking') && $this->input->post('allow_event_booking') == '1'){
                    $this->form_validation->set_rules('capacity', $this->lang->line('event_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('event_online_availability',$this->lang->line('event_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('event_minimum_capacity',$this->lang->line('event_minimum_capacity'), 'trim|required');
                }
                if($this->input->post('enable_table_booking') && $this->input->post('enable_table_booking') == '1'){
                    $this->form_validation->set_rules('table_booking_capacity',$this->lang->line('table_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('table_online_availability',$this->lang->line('table_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('table_minimum_capacity',$this->lang->line('table_minimum_capacity'), 'trim|required');
                    $this->form_validation->set_rules('allowed_days_table',$this->lang->line('allowed_days_table'), 'trim|required');
                }
                //credit card fee changes start
                if($this->session->userdata('AdminUserType') == 'MasterAdmin') {
                    $this->form_validation->set_rules('is_creditcard_fee_enable',$this->input->post('is_creditcard_fee_enable'), 'trim|required');
                    if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1'){
                        $this->form_validation->set_rules('creditcard_fee_type',$this->lang->line('creditcard_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('creditcard_fee',$this->lang->line('creditcard_fee'), 'trim|required');
                    }
                }
                //credit card fee changes end
                if($this->input->post('allow_scheduled_delivery') && $this->input->post('allow_scheduled_delivery') == '1'){
                    $this->form_validation->set_rules('allowed_days_for_scheduling',$this->lang->line('allowed_days_for_scheduling'), 'trim|required');
                }
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if($this->input->post('add_res_branch')=='res'){
                        $name = $this->input->post('res_name');
                        $content_type = 'restaurant';
                    } elseif($this->input->post('add_res_branch')=='branch'){
                        $name = $this->input->post('branch_name');
                        $content_type = 'branch';
                    } else {
                        $name = $this->input->post('res_name');
                        $content_type = 'restaurant';
                    }
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$content_type,  //$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                        $restaurant_slug = slugify($name,'restaurant','restaurant_slug');
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                        $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                        $restaurant_slug = $slug->restaurant_slug;
                    }
                    $add_data = array(
                        'name'=>$name,
                        'restaurant_slug'=>$restaurant_slug,
                        'currency_id' =>$this->input->post('currency_id'),
                        //'currency_id' =>$this->input->post('currency_id'),
                        'phone_code' =>$this->input->post('phone_code'),
                        'phone_number' =>$this->input->post('phone_number'),
                        'email' =>$this->input->post('email'),
                        // 'no_of_table' =>$this->input->post('no_of_table'),
                        // 'no_of_hall' =>$this->input->post('no_of_hall'),
                        // 'hall_capacity' =>$this->input->post('hall_capacity'),
                        //'enable_hours'=>$this->input->post("enable_hours"),
                        'is_printer_available'=>$this->input->post("is_printer_available"),
                        'enable_hours'=>1,
                        'status'=>1,
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type'=> implode(',', $this->input->post("food_type")),
                        'driver_commission'=>$this->input->post('driver_commission'),
                        'about_restaurant'=>$this->input->post('message'),
                    );
                    if($this->session->userdata('AdminUserType')=='MasterAdmin'){
                        $add_data['amount_type'] = $this->input->post("amount_type");
                        $add_data['amount'] = $this->input->post("amount");
                        $add_data['is_service_fee_enable'] = $this->input->post("is_service_fee_enable");
                        $add_data['is_creditcard_fee_enable'] = $this->input->post("is_creditcard_fee_enable");
                        $add_data['contractual_commission_type'] = $this->input->post('contractual_commission_type');
                        $add_data['contractual_commission'] = $this->input->post('contractual_commission');
                        $add_data['contractual_commission_type_delivery'] = $this->input->post('contractual_commission_type_delivery');
                        $add_data['contractual_commission_delivery'] = $this->input->post('contractual_commission_delivery');
                    }
                    if(!$this->input->post('content_id')){
                        $add_data['branch_entity_id'] = ($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0;
                        $add_data['capacity'] = ($this->input->post('capacity'))?$this->input->post('capacity'):NULL;
                        $add_data['order_mode'] = implode(',', $this->input->post('order_mode'));                        
                        $add_data['allow_event_booking']=($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0;
                        $add_data['event_online_availability']=($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL;
                        $add_data['event_minimum_capacity']=($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL;
                        $add_data['enable_table_booking']=($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0;
                        $add_data['table_booking_capacity']=($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL;
                        $add_data['table_online_availability']=($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL;
                        $add_data['table_minimum_capacity']=($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL;
                        $add_data['allowed_days_table']=($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL;
                        $add_data['allow_scheduled_delivery']=($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0;
                        $add_data['allowed_days_for_scheduling']=($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL;
                        $add_data['restaurant_rating'] = ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL;
                        $add_data['restaurant_rating_count'] = ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL;
                    } else {
                        $add_content_based_data = array(
                            'capacity' => ($this->input->post('capacity'))?$this->input->post('capacity'):NULL,
                            'branch_entity_id'=>($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0,
                            'order_mode' => implode(',', $this->input->post('order_mode')),                            
                            'allow_event_booking'=>($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0,
                            'event_online_availability'=>($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL,
                            'event_minimum_capacity'=>($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL,
                            'enable_table_booking'=>($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0,
                            'table_booking_capacity'=>($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL,
                            'table_online_availability'=>($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL,
                            'table_minimum_capacity'=>($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL,
                            'allowed_days_table'=>($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL,
                            'allow_scheduled_delivery' => ($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0,
                            'allowed_days_for_scheduling' => ($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL,
                            'restaurant_rating' => ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL,
                            'restaurant_rating_count' => ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL,
                        );
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if(!$this->input->post('content_id')){
                            $add_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                            $add_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        } else {
                            $add_content_based_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                            $add_content_based_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                        if(!$this->input->post('content_id')){
                            $add_data['restaurant_owner_id'] = $this->session->userdata('AdminUserID');
                            $add_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        } else {
                            $add_content_based_data['restaurant_owner_id'] = $this->session->userdata('AdminUserID');
                            $add_content_based_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1')
                        {
                            $add_data['service_fee_type'] = $this->input->post("service_fee_type");
                            $add_data['service_fee'] = $this->input->post("service_fee");
                        }
                        if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1')
                        {
                            $add_data['creditcard_fee_type'] = $this->input->post("creditcard_fee_type");
                            $add_data['creditcard_fee'] = $this->input->post("creditcard_fee");
                        }
                    }
                    //print receipt changes :: start
                    if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                        $add_data['printer_paper_height'] = $this->input->post("printer_paper_height");
                        $add_data['printer_paper_width'] = $this->input->post("printer_paper_width");
                    }
                    //print receipt changes :: end
                    if(!empty($this->input->post('timings'))){
                        $timingsArr = $this->input->post('timings');
                        $newTimingArr = array();
                        foreach($timingsArr as $key=>$value) {
                            if(!empty($value['off']) && (empty($value['open']) && empty($value['close']))) {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            } else {
                                if(!empty($value['open']) && !empty($value['close'])) {
                                    $newTimingArr[$key]['open'] = $this->common_model->setZonebaseTime($value['open']);
                                    $newTimingArr[$key]['close'] = $this->common_model->setZonebaseTime($value['close']);
                                    $newTimingArr[$key]['off'] = '1';
                                } else {
                                    $newTimingArr[$key]['open'] = '';
                                    $newTimingArr[$key]['close'] = '';
                                    $newTimingArr[$key]['off'] = '0';
                                }
                            }
                        }
                        if(!$this->input->post('content_id')){
                            $add_data['timings'] = serialize($newTimingArr); 
                        } else {
                            $add_content_based_data['timings'] = serialize($newTimingArr); 
                        }
                    }                                        
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant')) {
                          @mkdir('./uploads/restaurant', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "restaurant/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if (!empty($_FILES['background_image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant_background';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant_background')) {
                          @mkdir('./uploads/restaurant_background', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('background_image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant_background/'. $fileName; 
                          $imageTemp = $_FILES["background_image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['background_image'] = "restaurant_background/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    $entity_id = '';
                    if(empty($data['Error'])){
                        $entity_id = $this->restaurant_model->addData('restaurant',$add_data);
                        if(!empty($add_content_based_data)){
                            $this->restaurant_model->updateData($add_content_based_data,'restaurant','content_id',$ContentID);
                        }
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added restaurant - '.$name);
                         //for address
                        $add_data = array(
                            'resto_entity_id'=>$entity_id,
                            'address' =>$this->input->post('address'),
                            'latitude' =>$this->input->post('latitude'),
                            'longitude'=>$this->input->post("longitude"),
                            'state'=>$this->input->post("state"),
                            'country'=>$this->input->post("country"),
                            'city'=>$this->input->post("city"),
                            'zipcode'=>$this->input->post("zipcode"),
                            'content_id'=>$ContentID,
                            'language_slug'=>$this->uri->segment('4'),
                        );
                        $this->restaurant_model->addData('restaurant_address',$add_data);

                        //New code adde to assign branch admin :: Start :: 09-10-2020
                        $branch_admin_id = ($this->input->post('branch_admin_id') != '')?$this->input->post('branch_admin_id'):'';
                        if(intval($branch_admin_id)>0)
                        {
                            $owner_Arr = array(
                                'restaurant_content_id'=>$ContentID,
                                'branch_admin_id'=>$branch_admin_id
                            );
                            $this->restaurant_model->addData('restaurant_branch_map',$owner_Arr);
                        }                    
                        //New code adde to assign branch admin :: End :: 09-10-2020

                        if($this->session->userdata('adminemail')){
                            $this->db->select('OptionValue');
                            $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                            $this->db->select('OptionValue');
                            $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                            $this->db->select('subject,message');
                            $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'new-restaurant-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();

                            $arrayData = array('FirstName'=>$this->session->userdata('adminFirstname'),'restaurant_name'=>$name);
                            $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                            if(!empty($EmailBody)){     
                                $this->load->library('email');  
                                $config['charset'] = 'iso-8859-1';  
                                $config['wordwrap'] = TRUE;  
                                $config['mailtype'] = 'html';  
                                $this->email->initialize($config);  
                                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);  
                                $this->email->to(trim($this->session->userdata('adminemail'))); 
                                $this->email->subject($Emaildata->subject);  
                                $this->email->message($EmailBody);  
                                $this->email->send();
                            } 
                        }
                        //get restaurant ans set in session
                        $restaurant = $this->common_model->getRestaurantinSession('restaurant',$this->session->userdata('AdminUserID'));
                        if(!empty($restaurant))
                        {
                            $restaurant = array_column($restaurant, 'entity_id');
                            $this->session->set_userdata('restaurant',$restaurant);
                        }
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');             
                    }
                       
                }
            } elseif($this->input->post('submit_page') == "Save") {   
                if($this->input->post('add_res_branch')=='res'){
                    $this->form_validation->set_rules('res_name', $this->lang->line('res_name'), 'trim|required|callback_checkResNameExist');
                }
                if($this->input->post('add_res_branch')=='branch'){
                    $this->form_validation->set_rules('branch_name', $this->lang->line('branch_name'), 'trim|required|callback_checkResNameExist');
                    $this->form_validation->set_rules('branch_entity_id', $this->lang->line('add_res_branch'), 'trim|required');
                }
                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                    $this->form_validation->set_rules('restaurant_owner_id', $this->lang->line('restaurant_owner'), 'trim|required|numeric');
                }
                $this->form_validation->set_rules('currency_id', $this->lang->line('currency'), 'trim|required');
                $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim'); //|callback_checkExist
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|callback_checkEmailExist');
                // $this->form_validation->set_rules('branch_admin_id', $this->lang->line('restaurant_admin'), 'trim|required|numeric');
                // $this->form_validation->set_rules('no_of_table', $this->lang->line('no_of_table'), 'trim|required');
                $this->form_validation->set_rules('address', $this->lang->line('address'), 'trim|required');
                $this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required');
                $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
                $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required');
                $this->form_validation->set_rules('city',$this->lang->line('city'), 'trim|required');
                $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required');
                // $this->form_validation->set_rules('enable_hours', $this->lang->line('enable_hours'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating', $this->lang->line('restaurant_rating'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating_count', $this->lang->line('restaurant_rating_count'), 'trim|required');
                $this->form_validation->set_rules('message', $this->lang->line('about_restaurant'), 'trim|required');
                // service fee changes start
                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                    $this->form_validation->set_rules('amount_type', $this->lang->line('tax_amount'), 'trim|required');
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
                    $this->form_validation->set_rules('is_service_fee_enable',$this->input->post('is_service_fee_enable'), 'trim|required');
                    if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1'){
                        $this->form_validation->set_rules('service_fee_type',$this->lang->line('service_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('service_fee',$this->lang->line('service_fee'), 'trim|required');
                    }
                }
                // service fee changes end
                //print receipt changes :: start
                if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                    $this->form_validation->set_rules('printer_paper_height',$this->lang->line('printer_paper_height'), 'trim|required');
                    $this->form_validation->set_rules('printer_paper_width',$this->lang->line('printer_paper_width'), 'trim|required');
                }
                //print receipt changes :: end
                if($this->session->userdata('AdminUserType')=='MasterAdmin') {
                    $this->form_validation->set_rules('contractual_commission_type', $this->lang->line('contractual_commission_type'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission',$this->lang->line('contractual_commission'), 'trim|required|max_length[5]');
                    $this->form_validation->set_rules('contractual_commission_type_delivery', $this->lang->line('contractual_commission_type_delivery'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission_delivery',$this->lang->line('contractual_commission_delivery'), 'trim|required|max_length[5]');
                }
                $this->form_validation->set_rules('order_mode[]', $this->lang->line('order_mode'), 'trim|required|in_list[Delivery,PickUp]');

                if($this->input->post('allow_event_booking') && $this->input->post('allow_event_booking') == '1'){
                    $this->form_validation->set_rules('capacity', $this->lang->line('event_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('event_online_availability',$this->lang->line('event_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('event_minimum_capacity',$this->lang->line('event_minimum_capacity'), 'trim|required');
                }
                if($this->input->post('enable_table_booking') && $this->input->post('enable_table_booking') == '1'){
                    $this->form_validation->set_rules('table_booking_capacity',$this->lang->line('table_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('table_online_availability',$this->lang->line('table_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('table_minimum_capacity',$this->lang->line('table_minimum_capacity'), 'trim|required');
                    $this->form_validation->set_rules('allowed_days_table',$this->lang->line('allowed_days_table'), 'trim|required');
                }
                //credit card fee changes start
                if($this->session->userdata('AdminUserType')=='MasterAdmin') {
                    $this->form_validation->set_rules('is_creditcard_fee_enable',$this->input->post('is_creditcard_fee_enable'), 'trim|required');
                    if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1'){
                        $this->form_validation->set_rules('creditcard_fee_type',$this->lang->line('creditcard_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('creditcard_fee',$this->lang->line('creditcard_fee'), 'trim|required');
                    }
                }
                //credit card fee changes end
                if($this->input->post('allow_scheduled_delivery') && $this->input->post('allow_scheduled_delivery') == '1'){
                    $this->form_validation->set_rules('allowed_days_for_scheduling',$this->lang->line('allowed_days_for_scheduling'), 'trim|required');
                }
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if($this->input->post('add_res_branch')=='res'){
                        $name = $this->input->post('res_name');
                        $content_type = 'restaurant';
                    } elseif($this->input->post('add_res_branch')=='branch'){
                        $name = $this->input->post('branch_name');
                        $content_type = 'branch';
                    } else {
                        $name = $this->input->post('res_name');
                        $content_type = 'restaurant';
                    }
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>$content_type,  //$this->uri->segment('2'),
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                        $restaurant_slug = slugify($name,'restaurant','restaurant_slug');
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                        $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                        $restaurant_slug = $slug->restaurant_slug;
                    }
                    $add_data = array(                  
                        'name'=>$name,
                        'restaurant_slug'=>$restaurant_slug,
                        // 'currency_id' =>$currency_id->OptionValue,
                        'currency_id' =>$this->input->post('currency_id'),
                        'phone_code' =>$this->input->post('phone_code'),
                        'phone_number' =>$this->input->post('phone_number'),
                        'email' =>$this->input->post('email'),
                        // 'no_of_table' =>$this->input->post('no_of_table'),
                        // 'no_of_hall' =>$this->input->post('no_of_hall'),
                        // 'hall_capacity' =>$this->input->post('hall_capacity'),
                        //'enable_hours'=>$this->input->post("enable_hours"),
                        'is_printer_available'=>$this->input->post("is_printer_available"),
                        'enable_hours'=>0,
                        'status'=>0,
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type'=> implode(',', $this->input->post("food_type")),
                        'driver_commission'=>$this->input->post('driver_commission'),
                        'about_restaurant'=>$this->input->post('message'),
                    );
                    if($this->session->userdata('AdminUserType')=='MasterAdmin'){
                        $add_data['amount_type'] = $this->input->post("amount_type");
                        $add_data['amount'] = $this->input->post("amount");
                        $add_data['is_service_fee_enable'] = $this->input->post("is_service_fee_enable");
                        $add_data['is_creditcard_fee_enable'] = $this->input->post("is_creditcard_fee_enable");
                        $add_data['contractual_commission_type'] = $this->input->post('contractual_commission_type');
                        $add_data['contractual_commission'] = $this->input->post('contractual_commission');
                        $add_data['contractual_commission_type_delivery'] = $this->input->post('contractual_commission_type_delivery');
                        $add_data['contractual_commission_delivery'] = $this->input->post('contractual_commission_delivery');
                    }
                    if(!$this->input->post('content_id')){
                        $add_data['branch_entity_id'] = ($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0;
                        $add_data['capacity'] = ($this->input->post('capacity'))?$this->input->post('capacity'):NULL;
                        $add_data['order_mode'] = implode(',', $this->input->post('order_mode'));
                        $add_data['allow_event_booking']=($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0;
                        $add_data['event_online_availability']=($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL;
                        $add_data['event_minimum_capacity']=($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL;
                        $add_data['enable_table_booking']=($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0;
                        $add_data['table_booking_capacity']=($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL;
                        $add_data['table_online_availability']=($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL;
                        $add_data['table_minimum_capacity']=($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL;
                        $add_data['allowed_days_table']=($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL;
                        $add_data['allow_scheduled_delivery']=($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0;
                        $add_data['allowed_days_for_scheduling']=($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL;
                        $add_data['restaurant_rating'] = ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL;
                        $add_data['restaurant_rating_count'] = ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL;
                    } else {
                        $add_content_based_data = array(
                            'capacity' => ($this->input->post('capacity'))?$this->input->post('capacity'):NULL,
                            'branch_entity_id'=>($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0,
                            'order_mode' => implode(',', $this->input->post('order_mode')),
                            'allow_event_booking'=>($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0,
                            'event_online_availability'=>($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL,
                            'event_minimum_capacity'=>($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL,
                            'enable_table_booking'=>($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0,
                            'table_booking_capacity'=>($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL,
                            'table_online_availability'=>($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL,
                            'table_minimum_capacity'=>($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL,
                            'allowed_days_table'=>($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL,
                            'allow_scheduled_delivery'=>($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0,
                            'allowed_days_for_scheduling' => ($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL,
                            'restaurant_rating' => ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL,
                            'restaurant_rating_count' => ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL,
                        );
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if(!$this->input->post('content_id')){
                            $add_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                            $add_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        } else {
                            $add_content_based_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                            $add_content_based_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                        if(!$this->input->post('content_id')){
                            $add_data['restaurant_owner_id'] = $this->session->userdata('AdminUserID');
                            $add_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        } else {
                            $add_content_based_data['restaurant_owner_id'] = $this->session->userdata('AdminUserID');
                            $add_content_based_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1')
                        {
                            $add_data['service_fee_type'] = $this->input->post("service_fee_type");
                            $add_data['service_fee'] = $this->input->post("service_fee");
                        }
                        if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1')
                        {
                            $add_data['creditcard_fee_type'] = $this->input->post("creditcard_fee_type");
                            $add_data['creditcard_fee'] = $this->input->post("creditcard_fee");
                        }
                    }
                    //print receipt changes :: start
                    if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                        $add_data['printer_paper_height'] = $this->input->post("printer_paper_height");
                        $add_data['printer_paper_width'] = $this->input->post("printer_paper_width");
                    }
                    //print receipt changes :: end
                    if(!empty($this->input->post('timings'))){
                        $timingsArr = $this->input->post('timings');
                        $newTimingArr = array();
                        foreach($timingsArr as $key=>$value) {
                            if(!empty($value['off']) && (empty($value['open']) && empty($value['close']))) {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            } else {
                                if(!empty($value['open']) && !empty($value['close'])) {
                                    $newTimingArr[$key]['open'] = $this->common_model->setZonebaseTime($value['open']);
                                    $newTimingArr[$key]['close'] = $this->common_model->setZonebaseTime($value['close']);
                                    $newTimingArr[$key]['off'] = '1';
                                } else {
                                    $newTimingArr[$key]['open'] = '';
                                    $newTimingArr[$key]['close'] = '';
                                    $newTimingArr[$key]['off'] = '0';
                                }
                            }
                        }
                        if(!$this->input->post('content_id')){
                            $add_data['timings'] = serialize($newTimingArr); 
                        } else {
                            $add_content_based_data['timings'] = serialize($newTimingArr); 
                        }
                    }
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant')) {
                          @mkdir('./uploads/restaurant', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "restaurant/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if (!empty($_FILES['background_image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant_background';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant_background')) {
                          @mkdir('./uploads/restaurant_background', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('background_image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant_background/'. $fileName; 
                          $imageTemp = $_FILES["background_image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['background_image'] = "restaurant_background/".$img['file_name'];    
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    $entity_id = '';
                    if(empty($data['Error'])){
                        $entity_id = $this->restaurant_model->addData('restaurant',$add_data);
                        if(!empty($add_content_based_data)){
                            $this->restaurant_model->updateData($add_content_based_data,'restaurant','content_id',$ContentID);
                        }
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added restaurant - '.$name);
                         //for address
                        $add_data = array(
                            'resto_entity_id'=>$entity_id,
                            'address' =>$this->input->post('address'),
                            'latitude' =>$this->input->post('latitude'),
                            'longitude'=>$this->input->post("longitude"),
                            'state'=>$this->input->post("state"),
                            'country'=>$this->input->post("country"),
                            'city'=>$this->input->post("city"),
                            'zipcode'=>$this->input->post("zipcode"),
                            'content_id'=>$ContentID,
                            'language_slug'=>$this->uri->segment('4'),
                        );
                        $this->restaurant_model->addData('restaurant_address',$add_data);

                        //New code adde to assign branch admin :: Start :: 09-10-2020
                        $branch_admin_id = ($this->input->post('branch_admin_id') != '')?$this->input->post('branch_admin_id'):'';
                        if(intval($branch_admin_id)>0)
                        {
                            $owner_Arr = array(
                                'restaurant_content_id'=>$ContentID,
                                'branch_admin_id'=>$branch_admin_id
                            );
                            $this->restaurant_model->addData('restaurant_branch_map',$owner_Arr);
                        }                    
                        //New code adde to assign branch admin :: End :: 09-10-2020

                        if($this->session->userdata('adminemail')){
                            $this->db->select('OptionValue');
                            $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                            $this->db->select('OptionValue');
                            $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                            $this->db->select('subject,message');
                            $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'new-restaurant-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();

                            $arrayData = array('FirstName'=>$this->session->userdata('adminFirstname'),'restaurant_name'=>$name);
                            $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                            if(!empty($EmailBody)){     
                                $this->load->library('email');  
                                $config['charset'] = 'iso-8859-1';  
                                $config['wordwrap'] = TRUE;  
                                $config['mailtype'] = 'html';  
                                $this->email->initialize($config);  
                                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                                $this->email->to(trim($this->session->userdata('adminemail'))); 
                                $this->email->subject($Emaildata->subject);  
                                $this->email->message($EmailBody);  
                                $this->email->send(); 
                            } 
                        }
                        //get restaurant ans set in session
                        $restaurant = $this->common_model->getRestaurantinSession('restaurant',$this->session->userdata('AdminUserID'));
                        if(!empty($restaurant))
                        {
                            $restaurant = array_column($restaurant, 'entity_id');
                            $this->session->set_userdata('restaurant',$restaurant);
                        }
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');             
                    }
                       
                }
            }

            $data['branchadmin'] = $this->restaurant_model->get_brachadmin($this->session->userdata('AdminUserID'));
            $data['restaurant_admins'] = $this->restaurant_model->get_restaurant_admins($this->session->userdata('AdminUserID'));
            $data['currencies'] = $this->common_model->getCountriesCurrency();
            if (!empty($this->uri->segment('5'))) {
                $getRestaurantCurrency = $this->common_model->getRestaurantCurrency($this->uri->segment('5'));
                $data['res_currency_id'] = $getRestaurantCurrency->currency_id;
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getRestaurantData('restaurant',$language_slug);
            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $this->load->view(ADMIN_URL.'/restaurant_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // edit restaurant
    public function edit(){
        if(in_array('restaurant~edit',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_restaurantedit').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {   
                if($this->input->post('add_res_branch')=='res'){
                    $this->form_validation->set_rules('res_name', $this->lang->line('res_name'), 'trim|required|callback_checkResNameExist');
                }
                if($this->input->post('add_res_branch')=='branch'){
                    $this->form_validation->set_rules('branch_name', $this->lang->line('branch_name'), 'trim|required|callback_checkResNameExist');
                    $this->form_validation->set_rules('branch_entity_id', $this->lang->line('add_res_branch'), 'trim|required');
                }
                if($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                    // $this->form_validation->set_rules('branch_admin_id', $this->lang->line('restaurant_admin'), 'trim|required|numeric');
                }
                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                    $this->form_validation->set_rules('restaurant_owner_id', $this->lang->line('restaurant_owner'), 'trim|required|numeric');
                }
                $this->form_validation->set_rules('currency_id', $this->lang->line('currency'), 'trim|required');
                $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim'); //|callback_checkExist
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|callback_checkEmailExist');
                /*$this->form_validation->set_rules('branch_admin_id', $this->lang->line('restaurant_admin'), 'trim|required|numeric');*/
                // $this->form_validation->set_rules('no_of_table', $this->lang->line('no_of_table'), 'trim|required');
                $this->form_validation->set_rules('address', $this->lang->line('address'), 'trim|required');
                $this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required');
                $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
                $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
                $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required');
                $this->form_validation->set_rules('city',$this->lang->line('city'), 'trim|required');
                $this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required');
                $this->form_validation->set_rules('message', $this->lang->line('about_restaurant'), 'trim|required');
                // $this->form_validation->set_rules('enable_hours', $this->lang->line('enable_hours'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating', $this->lang->line('restaurant_rating'), 'trim|required');
                $this->form_validation->set_rules('restaurant_rating_count', $this->lang->line('restaurant_rating_count'), 'trim|required');
                // service fee changes start
                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                    $this->form_validation->set_rules('amount_type', $this->lang->line('tax_amount'), 'trim|required');
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required');
                    $this->form_validation->set_rules('is_service_fee_enable',$this->input->post('is_service_fee_enable'), 'trim|required');
                    if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1'){
                        $this->form_validation->set_rules('service_fee_type',$this->lang->line('service_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('service_fee',$this->lang->line('service_fee'), 'trim|required');
                    }
                }
                // service fee changes end
                //print receipt changes :: start
                if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                    $this->form_validation->set_rules('printer_paper_height',$this->lang->line('printer_paper_height'), 'trim|required');
                    $this->form_validation->set_rules('printer_paper_width',$this->lang->line('printer_paper_width'), 'trim|required');
                }
                //print receipt changes :: end
                if($this->session->userdata('AdminUserType')=='MasterAdmin'){
                    $this->form_validation->set_rules('contractual_commission_type', $this->lang->line('contractual_commission_type'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission',$this->lang->line('contractual_commission'), 'trim|required|max_length[5]');
                    $this->form_validation->set_rules('contractual_commission_type_delivery', $this->lang->line('contractual_commission_type_delivery'), 'trim|required');
                    $this->form_validation->set_rules('contractual_commission_delivery',$this->lang->line('contractual_commission_delivery'), 'trim|required|max_length[5]');
                }
                $this->form_validation->set_rules('order_mode[]', $this->lang->line('order_mode'), 'trim|required|in_list[Delivery,PickUp]');

                if($this->input->post('allow_event_booking') && $this->input->post('allow_event_booking') == '1'){
                    $this->form_validation->set_rules('capacity', $this->lang->line('event_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('event_online_availability',$this->lang->line('event_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('event_minimum_capacity',$this->lang->line('event_minimum_capacity'), 'trim|required');
                }
                if($this->input->post('enable_table_booking') && $this->input->post('enable_table_booking') == '1'){
                    $this->form_validation->set_rules('table_booking_capacity',$this->lang->line('table_booking_capacity'), 'trim|required');
                    $this->form_validation->set_rules('table_online_availability',$this->lang->line('table_online_availability'), 'trim|required');
                    $this->form_validation->set_rules('table_minimum_capacity',$this->lang->line('table_minimum_capacity'), 'trim|required');
                    $this->form_validation->set_rules('allowed_days_table',$this->lang->line('allowed_days_table'), 'trim|required');
                }
                //credit card fee changes start
                if($this->session->userdata('AdminUserType')=='MasterAdmin'){
                    $this->form_validation->set_rules('is_creditcard_fee_enable',$this->input->post('is_creditcard_fee_enable'), 'trim|required');
                    if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1'){
                        $this->form_validation->set_rules('creditcard_fee_type',$this->lang->line('creditcard_fee_type'), 'trim|required');
                        $this->form_validation->set_rules('creditcard_fee',$this->lang->line('creditcard_fee'), 'trim|required');
                    }
                }
                //credit card fee changes end
                if($this->input->post('allow_scheduled_delivery') && $this->input->post('allow_scheduled_delivery') == '1'){
                    $this->form_validation->set_rules('allowed_days_for_scheduling',$this->lang->line('allowed_days_for_scheduling'), 'trim|required');
                }
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if($this->input->post('add_res_branch')=='res'){
                        $name = $this->input->post('res_name');
                        $content_type = 'restaurant';
                        $branch_entity_id = 0;
                    } elseif($this->input->post('add_res_branch')=='branch'){
                        $name = $this->input->post('branch_name');
                        $content_type = 'branch';
                        $branch_entity_id = ($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0;
                    } else {
                        $name = trim($this->input->post('res_name'));
                        $branchname = trim($this->input->post('branch_name'));
                        $name = ($name) ? $name : $branchname;
                        $content_type =  ($name) ? 'restaurant' : (($branchname) ? 'branch' : 'restaurant');
                        $branch_entity_id = ($this->input->post('branch_entity_id'))?$this->input->post('branch_entity_id'):0;
                    }

                    $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'),'restaurant');
                    //edit content type in Content General
                    $edit_content = array(
                      'content_type'=>$content_type,  //$this->uri->segment('2'),
                      'updated_by'=>$this->session->userdata("AdminUserID"),  
                      'updated_date'=>date('Y-m-d H:i:s')                      
                    );
                    $ContentID = $this->restaurant_model->updateData($edit_content,'content_general','content_general_id',$content_id->content_id);

                    $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                    if (!empty($slug->restaurant_slug)) { 
                        $restaurant_slug = $slug->restaurant_slug;
                    }
                    else
                    {
                        $restaurant_slug = slugify($name,'restaurant','restaurant_slug','content_id',$content_id->content_id);
                    }

                    $edit_data = array(                  
                        'name'=>$name, //$this->input->post('name'),
                        'restaurant_slug'=>$restaurant_slug,
                        'currency_id' =>$this->input->post('currency_id'),
                        'phone_code' =>$this->input->post('phone_code'),
                        'phone_number' =>$this->input->post('phone_number'),
                        'email' =>$this->input->post('email'),
                        // 'no_of_table' =>$this->input->post('no_of_table'),
                        // 'no_of_hall' =>$this->input->post('no_of_hall'),
                        // 'hall_capacity' =>$this->input->post('hall_capacity'),
                        'is_printer_available'=>$this->input->post("is_printer_available"),
                        //'enable_hours'=>$this->input->post("enable_hours"),
                        'status'=>1,
                        'updated_by' => $this->session->userdata('AdminUserID'),
                        'updated_date'=>date('Y-m-d H:i:s'),
                        'food_type'=> implode(',', $this->input->post("food_type")),
                        'driver_commission'=>$this->input->post('driver_commission'),
                        'about_restaurant'=>$this->input->post('message')
                    ); 
                    if($this->session->userdata('AdminUserType')=='MasterAdmin'){
                        $edit_data['amount_type'] = $this->input->post("amount_type");
                        $edit_data['amount'] = $this->input->post("amount");
                        $edit_data['is_service_fee_enable'] = $this->input->post("is_service_fee_enable");
                        $edit_data['is_creditcard_fee_enable'] = $this->input->post("is_creditcard_fee_enable");
                        $edit_data['contractual_commission_type'] = $this->input->post('contractual_commission_type');
                        $edit_data['contractual_commission'] = $this->input->post('contractual_commission');
                        $edit_data['contractual_commission_type_delivery'] = $this->input->post('contractual_commission_type_delivery');
                        $edit_data['contractual_commission_delivery'] = $this->input->post('contractual_commission_delivery');
                    }
                    if(!$content_id->content_id){
                        $edit_data['branch_entity_id'] = $branch_entity_id;
                        $edit_data['capacity'] = ($this->input->post('capacity'))?$this->input->post('capacity'):NULL;
                        $edit_data['order_mode'] = implode(',', $this->input->post('order_mode'));
                        $edit_data['allow_event_booking']=($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0;
                        $edit_data['event_online_availability']=($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL;
                        $edit_data['event_minimum_capacity']=($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL;
                        $edit_data['enable_table_booking']=($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0;
                        $edit_data['table_booking_capacity']=($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL;
                        $edit_data['table_online_availability']=($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL;
                        $edit_data['table_minimum_capacity']=($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL;
                        $edit_data['allowed_days_table']=($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL;
                        $edit_data['allow_scheduled_delivery']=($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0;
                        $edit_data['allowed_days_for_scheduling']=($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL;
                        $edit_data['restaurant_rating'] = ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL;
                        $edit_data['restaurant_rating_count'] = ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL;
                    } else {
                        $edit_content_based_data = array(
                            'capacity' => ($this->input->post('capacity'))?$this->input->post('capacity'):NULL,
                            'branch_entity_id' =>$branch_entity_id,
                            'order_mode' => implode(',', $this->input->post('order_mode')),
                            'allow_event_booking'=>($this->input->post("allow_event_booking"))?$this->input->post("allow_event_booking"):0,
                            'event_online_availability'=>($this->input->post("event_online_availability"))?$this->input->post("event_online_availability"):NULL,
                            'event_minimum_capacity'=>($this->input->post("event_minimum_capacity"))?$this->input->post("event_minimum_capacity"):NULL,
                            'enable_table_booking'=>($this->input->post("enable_table_booking"))?$this->input->post("enable_table_booking"):0,
                            'table_booking_capacity'=>($this->input->post("table_booking_capacity"))?$this->input->post("table_booking_capacity"):NULL,
                            'table_online_availability'=>($this->input->post("table_online_availability"))?$this->input->post("table_online_availability"):NULL,
                            'table_minimum_capacity'=>($this->input->post("table_minimum_capacity"))?$this->input->post("table_minimum_capacity"):NULL,
                            'allowed_days_table'=>($this->input->post("allowed_days_table"))?$this->input->post("allowed_days_table"):NULL,
                            'allow_scheduled_delivery'=>($this->input->post("allow_scheduled_delivery"))?$this->input->post("allow_scheduled_delivery"):0,
                            'allowed_days_for_scheduling' => ($this->input->post("allow_scheduled_delivery") == '1' && $this->input->post("allowed_days_for_scheduling")) ? $this->input->post("allowed_days_for_scheduling") : NULL,
                            'restaurant_rating' => ($this->input->post("restaurant_rating") && $this->input->post("restaurant_rating") > 0) ? $this->input->post("restaurant_rating") : NULL,
                            'restaurant_rating_count' => ($this->input->post("restaurant_rating_count") && $this->input->post("restaurant_rating_count") > 0) ? $this->input->post("restaurant_rating_count") : NULL,
                        );
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if(!$content_id->content_id){
                            $edit_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                        } else {
                            $edit_content_based_data['restaurant_owner_id'] = $this->input->post('restaurant_owner_id');
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                        if(!$content_id->content_id){
                            $edit_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        } else {
                            $edit_content_based_data['branch_admin_id'] = ($this->input->post('branch_admin_id'))?$this->input->post('branch_admin_id'):NULL;
                        }
                    }
                    if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                        if($this->input->post('is_service_fee_enable') && $this->input->post('is_service_fee_enable') == '1')
                        {
                            $edit_data['service_fee_type'] = $this->input->post("service_fee_type");
                            $edit_data['service_fee'] = $this->input->post("service_fee");
                        }
                        if($this->input->post('is_creditcard_fee_enable') && $this->input->post('is_creditcard_fee_enable') == '1')
                        {
                            $edit_data['creditcard_fee_type'] = $this->input->post("creditcard_fee_type");
                            $edit_data['creditcard_fee'] = $this->input->post("creditcard_fee");
                        }
                    }
                    //print receipt changes :: start
                    if($this->input->post('is_printer_available') && $this->input->post('is_printer_available') == '1'){
                        $edit_data['printer_paper_height'] = $this->input->post("printer_paper_height");
                        $edit_data['printer_paper_width'] = $this->input->post("printer_paper_width");
                    }
                    //print receipt changes :: end
                    if(!empty($this->input->post('timings'))){
                        $timingsArr = $this->input->post('timings');
                        $newTimingArr = array();
                        foreach($timingsArr as $key=>$value) {
                            if(!empty($value['off']) && (empty($value['open']) && empty($value['close']))) {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            } else {
                                if(!empty($value['open']) && !empty($value['close'])) {
                                    $newTimingArr[$key]['open'] = $this->common_model->setZonebaseTime($value['open']);
                                    $newTimingArr[$key]['close'] = $this->common_model->setZonebaseTime($value['close']);
                                    $newTimingArr[$key]['off'] = '1';
                                } else {
                                    $newTimingArr[$key]['open'] = '';
                                    $newTimingArr[$key]['close'] = '';
                                    $newTimingArr[$key]['off'] = '0';
                                }
                            }
                        }
                        if(!$content_id->content_id){
                            $edit_data['timings'] = serialize($newTimingArr); 
                        } else {
                            $edit_content_based_data['timings'] = serialize($newTimingArr); 
                        }
                    }
                    
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant')) {
                          @mkdir('./uploads/restaurant', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $edit_data['image'] = "restaurant/".$img['file_name'];   
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          }  
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if (!empty($_FILES['background_image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/restaurant_background';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/restaurant_background')) {
                          @mkdir('./uploads/restaurant_background', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('background_image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/restaurant_background/'. $fileName; 
                          $imageTemp = $_FILES["background_image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $edit_data['background_image'] = "restaurant_background/".$img['file_name'];   
                          if($this->input->post('uploaded_background_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_background_image'));
                          }
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error']))
                    {
                        //New code adde to assign branch admin :: Start :: 09-10-2020
                        $branch_admin_id = ($this->input->post('branch_admin_id') != '')?$this->input->post('branch_admin_id'):'';
                        if(intval($branch_admin_id)>0)
                        {
                            $branch_adminchk = $this->restaurant_model->getBrachAdminDetail($this->input->post('content_id'));
                            $owner_Arr = array(
                                'restaurant_content_id'=>$this->input->post('content_id'),
                                'branch_admin_id'=>$branch_admin_id
                            );
                            if($branch_adminchk && !empty($branch_adminchk))
                            {
                                $this->restaurant_model->updateData($owner_Arr,'restaurant_branch_map','restaurant_content_id',$this->input->post('content_id'));
                            }
                            else
                            {
                                $this->restaurant_model->addData('restaurant_branch_map',$owner_Arr);
                            }
                        }
                        //New code adde to assign branch admin :: End :: 09-10-2020

                        $this->restaurant_model->updateData($edit_data,'restaurant','entity_id',$this->input->post('entity_id'));
                        if(!empty($edit_content_based_data)){
                            $this->restaurant_model->updateData($edit_content_based_data,'restaurant','content_id',$content_id->content_id);
                        }
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited restaurant - '.$name);
                         //for address
                        $edit_data = array(
                            'resto_entity_id'=>$this->input->post('entity_id'),
                            'address' =>$this->input->post('address'),
                            'latitude' =>$this->input->post('latitude'),
                            'longitude'=>$this->input->post("longitude"),
                            'state'=>$this->input->post("state"),
                            'country'=>$this->input->post("country"),
                            'city'=>$this->input->post("city"),
                            'zipcode'=>$this->input->post("zipcode"),
                        );
                        $this->restaurant_model->updateData($edit_data,'restaurant_address','resto_entity_id',$this->input->post('entity_id'));
                        if($this->session->userdata('adminemail')){
                            $this->db->select('OptionValue');
                            $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

                            $this->db->select('OptionValue');
                            $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
                            $this->db->select('subject,message');
                            $Emaildata = $this->db->get_where('email_template',array('email_slug'=>'restaurant-details-update-alert','language_slug'=>$this->session->userdata('language_slug'),'status'=>1))->first_row();
                            $arrayData = array('FirstName'=>$this->session->userdata('adminFirstname'),'restaurant_name'=>$name);
                            $EmailBody = generateEmailBody($Emaildata->message,$arrayData);  
                            if(!empty($EmailBody)){     
                                $this->load->library('email');  
                                $config['charset'] = 'iso-8859-1';  
                                $config['wordwrap'] = TRUE;  
                                $config['mailtype'] = 'html';  
                                $this->email->initialize($config);  
                                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                                $this->email->to(trim($this->session->userdata('adminemail'))); 
                                $this->email->subject($Emaildata->subject);  
                                $this->email->message($EmailBody);  
                                $this->email->send(); 
                            } 
                        }
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');           
                    }
                         
                }
            } 
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');

            //Code for timming array base on utc time :: Start
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $edit_records = $this->restaurant_model->getEditDetail('restaurant',$entity_id,$language_slug);
            $timingsarr = unserialize($edit_records->timings);       
            $newTimingArr = array();
            foreach($timingsarr as $key=>$value)
            {
                if(!empty($value['open']) && !empty($value['close']))
                {
                    $newTimingArr[$key]['open'] = $this->common_model->setZonebaseTimeforEdit($value['open']);
                    $newTimingArr[$key]['close'] = $this->common_model->setZonebaseTimeforEdit($value['close']);
                    $newTimingArr[$key]['off'] = '1';
                }
                else
                {
                    $newTimingArr[$key]['open'] = '';
                    $newTimingArr[$key]['close'] = '';
                    $newTimingArr[$key]['off'] = '0';
                }            
            }
            $edit_records->timings= serialize($newTimingArr);
            //Code for timming array base on utc time :: End
            
            $data['edit_records'] = $edit_records;
            $data['currencies'] = $this->common_model->getCountriesCurrency();
            $data['branchadmin'] = $this->restaurant_model->get_brachadmin($this->session->userdata('AdminUserID'));
            $data['restaurant_admins'] = $this->restaurant_model->get_restaurant_admins($this->session->userdata('AdminUserID'));
            $data['branch_adminval'] = $this->restaurant_model->getBrachAdminDetail($data['edit_records']->content_id);
            $data['restaurant'] = $this->restaurant_model->getRestaurantData('restaurant',$language_slug,$entity_id);
            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $this->load->view(ADMIN_URL.'/restaurant_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'name',2=>'res_add.city',3=>'status',4=>'enable_hours',5=>'created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->restaurant_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);        
        $Languages = $this->common_model->getLanguages();        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $cnt = 0;
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $schedule_modearr = array(0=>'Normal',1=>'Busy',2=>'Very Busy');        
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '';
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End

            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $edit_active_access .= (in_array('restaurant~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$msgDelete.','.$value['is_masterdata'].')" '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].','.$value['is_masterdata'].')" '.$btndisable_master.' title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $online_offline = (in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray"))) ? '<a style="cursor:pointer;" '.$btndisable_master.' onclick="onOffDetails('.$value['content_id'].','.$value['enable_hours'].','.$value['is_masterdata'].')"  title="'.($value['enable_hours'] ? $this->lang->line('offline'):$this->lang->line('online')).'"><i class="fa fa-toggle-'.($value['enable_hours'] ? 'on' : 'off').'" style="font-size: 25px;vertical-align: middle;color: green;"></i> </a>' : '';

            //Button for shedule
            $order_modearr = explode(",", $value['order_mode']);            
            $schedule_mode_button = (in_array('restaurant~order_schedule',$this->session->userdata("UserAccessArray"))) ? '<button onclick="shedulePopup('.$value['content_id'].','.$value['schedule_mode'].')" '.$btndisable_master.' title="'.$this->lang->line('restaurant_mode').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-spinner" aria-hidden="true"></i></a></button>' : '';

            if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray")) || in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {
                $records["aaData"][] = array(
                    '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">',
                    $nCount,
                    ($value['city'])?$value['city']:'-',
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    ($value['enable_hours'] == 1)?$this->lang->line('online'):$this->lang->line('offline'),
                    $schedule_modearr[$value['schedule_mode']],
                    $edit_active_access.$schedule_mode_button.$online_offline
                );
            } else {
                $records["aaData"][] = array(
                    $nCount,
                    ($value['city'])?$value['city']:'-',
                    ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                    ($value['enable_hours'] == 1)?$this->lang->line('online'):$this->lang->line('offline'),
                    $schedule_modearr[$value['schedule_mode']],
                    $edit_active_access.$schedule_mode_button.$online_offline
                );   
            }            
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $res_edit_btn = (in_array('restaurant~edit',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                    $res_edit_btn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                    $cusLan[] = $res_edit_btn;
                    
                }else{
                    $cusLan[] = (in_array('restaurant~add',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                }                    
            }
            // added to specific position
           if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray")) || in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {
                array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            }
            else{
              array_splice( $records["aaData"][$cnt], 1, 0, $cusLan);  
            }
            $cnt++;
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //Update status for Single 
    // method to change restaurant status
    public function ajaxDisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->restaurant_model->UpdatedStatus($this->input->post('tblname'),$entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            if($this->input->post('tblname') == 'restaurant') {
                $res_name = $this->common_model->getResNametoDisplay($entity_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant - '.$res_name);
            } elseif ($this->input->post('tblname') == 'restaurant_menu_item') {
                $resmenu_name = $this->restaurant_model->getRestaurantMenuName($entity_id);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant menu - '.$resmenu_name);
            }
        }
    }
    // method for deleting a restaurant
    public function ajaxDelete()
    {
        //New code added as per requested :: 09-10-2020 :: Start
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if(intval($content_id)>0)
        {
            $this->restaurant_model->ajaxDeleteMapdata('restaurant_branch_map',$content_id);
        }
        //New code added as per requested :: 09-10-2020 :: End
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';

        if($this->input->post('tblname') == 'restaurant') {
            $res_name = $this->common_model->getResNametoDisplay($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant - '.$res_name);
        } elseif ($this->input->post('tblname') == 'restaurant_menu_item') {
            $resmenu_name = $this->restaurant_model->getRestaurantMenuName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant menu - '.$resmenu_name);
        } elseif ($this->input->post('tblname') == 'restaurant_package') {
            $respkg_name = $this->restaurant_model->getRestaurantPackageName($entity_id);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant package - '.$respkg_name);
        }
        $this->restaurant_model->ajaxDelete($this->input->post('tblname'),$this->input->post('content_id'),$entity_id);
    }
    public function ajaxDeleteAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        $this->restaurant_model->ajaxDeleteMapdata('restaurant_branch_map',$content_id);//Added on 13-10-2020
        $language_slug = $this->session->userdata('language_slug');
        if($this->input->post('tblname') == 'restaurant') {
            $res_name = $this->common_model->getResNametoDisplay('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant - '.$res_name);
        } elseif ($this->input->post('tblname') == 'restaurant_menu_item') {
            $resmenu_name = $this->restaurant_model->getRestaurantMenuName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant menu - '.$resmenu_name);
        } elseif ($this->input->post('tblname') == 'restaurant_package') {
            $respkg_name = $this->restaurant_model->getRestaurantPackageName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant package - '.$respkg_name);
        }
        $this->restaurant_model->ajaxDeleteAll($this->input->post('tblname'),$content_id);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
    // view restaurant menu
    public function view_menu(){
        if(in_array('restaurant_menu~view_menu',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_res_menu').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$this->session->userdata('language_slug'));            
            $data['restaurant_adminarr'] = $this->restaurant_model->get_restaurant_adminsData();
            //menu count
            $this->db->select('menu.content_id');
            $this->db->join('restaurant as res','menu.restaurant_id = res.entity_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('menu.content_id');
            $data['menu_count'] = $this->db->get('restaurant_menu_item as menu')->num_rows();
            $this->load->view(ADMIN_URL.'/restaurant_menu',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //add menu
    public function add_menu(){
        if(in_array('restaurant_menu~add_menu',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_restaurantadd').' '.$this->lang->line('menu').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|callback_checkResMenuNameExist');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('category_id', $this->lang->line('category'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('menu_detail', $this->lang->line('detail'), 'trim|required');
                //$this->form_validation->set_rules('recipe_detail', $this->lang->line('recipe_detail'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if(!empty($this->input->post('content_id')))
                    {
                        $ContentID = $this->input->post('content_id');
                        $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                        $item_slug = $slug->item_slug;
                    }
                    else
                    {   
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>'menu',
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                        $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug');               
                    }
                    $add_data = array(                  
                        'name'=>$this->input->post('name'),
                        'item_slug'=>$item_slug,
                        'restaurant_id' =>$this->input->post('restaurant_id'),
                        'category_id' =>$this->input->post('category_id'),
                        'price' =>($this->input->post('price'))?$this->input->post('price'):NULL,
                        'menu_detail' =>$this->input->post('menu_detail'),
                        //'ingredients' =>$this->input->post('ingredients'),
                        'recipe_time'=>$this->input->post('recipe_time'),
                        'popular_item' =>($this->input->post('popular_item'))?$this->input->post('popular_item'):'0',
                        'availability'=>implode(',', $this->input->post("availability")),
                        'status'=>1,
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type'=>$this->input->post("food_type"),
                        'check_add_ons'=>($this->input->post('check_add_ons'))?$this->input->post('check_add_ons'):0,
                        'sku' =>$this->input->post('sku')
                    ); 
                    $add_content_data = array('price' => ($this->input->post('price'))?$this->input->post('price'):NULL );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/menu';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/menu')) {
                          @mkdir('./uploads/menu', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/menu/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "menu/".$img['file_name'];   
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error'])){
                        $menu_id = $this->restaurant_model->addData('restaurant_menu_item',$add_data);
                        if($this->input->post('check_add_ons') == 1){
                            if(!empty($this->input->post('add_ons_list'))){
                                $addons = array();
                                foreach ($this->input->post('add_ons_list') as $key => $value) {
                                    foreach ($value as $k => $val) {
                                        if($val['add_ons_name'] != '' && $val['add_ons_price'] != ''){
                                            $addons[] = array(
                                                'menu_id'=>$menu_id,
                                                'category_id'=>$key,
                                                'add_ons_name'=>$val['add_ons_name'],
                                                'add_ons_price'=>$val['add_ons_price'],
                                                'is_multiple'=>($this->input->post('is_multiple')[$key])?$this->input->post('is_multiple')[$key]:0,
                                                'mandatory'=>($this->input->post('mandatory')[$key])?$this->input->post('mandatory')[$key]:0,
                                                'display_limit'=>($this->input->post('display_limit_value')[$key])
                                            );
                                        }
                                    }
                                }
                            }
                            $this->restaurant_model->inserBatch('add_ons_master',$addons);
                        }
                        //$recipe_content_id = ($this->input->post('recipe') != '')?$this->input->post('recipe'):'';

                        /*if(intval($recipe_content_id)>0){
                            $rest_menu_recipe_map = array();
                            //foreach ($menu_entity_id as $key => $value) {
                                $rest_menu_recipe_map[] = array(
                                    'menu_content_id'=>$ContentID,
                                    'recipe_content_id'=>$recipe_content_id
                                );
                            //}
                        $map_id = $this->restaurant_model->insertBatch('restaurant_menu_recipe_map',$rest_menu_recipe_map,$ContentID);
                        
                        }*/
                        //updating price for other restaurants with same content id
                        $this->restaurant_model->updateData($add_content_data,'restaurant_menu_item','content_id',$ContentID);
                        $res_name = $this->common_model->getResNametoDisplay($this->input->post('restaurant_id'),'','');
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a menu for restaurant'.$res_name.' - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');               
                    }                                        
                     
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
            $data['category'] = $this->restaurant_model->getCategoryListData($language_slug);
            $data['addons_category'] = $this->restaurant_model->getAddonListData($language_slug);
            //$data['recipe_list'] = $this->restaurant_model->getRecipe('recipe',$language_slug);
            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $this->load->view(ADMIN_URL.'/restaurant_menu_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //edit menu
    public function edit_menu() {
        if(in_array('restaurant_menu~edit_menu',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_restaurantedit').' '.$this->lang->line('menu').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|callback_checkResMenuNameExist');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('category_id', $this->lang->line('category'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('menu_detail', $this->lang->line('detail'), 'trim|required');
                //$this->form_validation->set_rules('recipe_detail', $this->lang->line('recipe_detail'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'),'restaurant_menu_item');
                    $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                    if (!empty($slug->item_slug)) { 
                        $item_slug = $slug->item_slug;
                    }
                    else
                    {
                        $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug','content_id',$content_id->content_id);
                    }
                    $edit_data = array(                  
                        'name'=>$this->input->post('name'),
                        'item_slug'=>$item_slug,
                        'restaurant_id' =>$this->input->post('restaurant_id'),
                        'category_id' =>$this->input->post('category_id'),
                        'price' =>($this->input->post('price'))?$this->input->post('price'):NULL,
                        'menu_detail' =>$this->input->post('menu_detail'),
                        //'ingredients' =>$this->input->post('ingredients'),
                        'recipe_time'=>$this->input->post('recipe_time'),
                        'popular_item' =>($this->input->post('popular_item'))?$this->input->post('popular_item'):'0',
                        'availability'=>implode(',', $this->input->post("availability")),
                        'updated_by' => $this->session->userdata('AdminUserID'),
                        'updated_date' => date('Y-m-d H:i:s'),
                        'food_type'=>$this->input->post("food_type"),
                        'check_add_ons'=>($this->input->post('check_add_ons'))?$this->input->post('check_add_ons'):0,
                        'sku' =>$this->input->post('sku')
                    );
                    $edit_content_data = array('price'=>($this->input->post('price'))?$this->input->post('price'):NULL);
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/menu';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/menu')) {
                          @mkdir('./uploads/menu', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/menu/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $edit_data['image'] = "menu/".$img['file_name'];   
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          } 
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }    
                    if(empty($data['Error'])){                            
                        $this->restaurant_model->updateData($edit_data,'restaurant_menu_item','entity_id',$this->input->post('entity_id'));
                        $addons = array();
                        if($this->input->post('check_add_ons') == 1){
                            if(!empty($this->input->post('add_ons_list'))){
                                foreach ($this->input->post('add_ons_list') as $key => $value) {
                                    if(in_array($key,$this->input->post('addons_category_id'))){
                                        foreach ($value as $k => $val) {
                                            if($val['add_ons_name'] != '' && $val['add_ons_price'] != ''){
                                                $addons[] = array(
                                                    'menu_id'=>$this->input->post('entity_id'),
                                                    'category_id'=>$key,
                                                    'add_ons_name'=>$val['add_ons_name'],
                                                    'add_ons_price'=>$val['add_ons_price'],
                                                    'is_multiple'=>($this->input->post('is_multiple')[$key])?$this->input->post('is_multiple')[$key]:0,
                                                    'mandatory'=>($this->input->post('mandatory')[$key])?$this->input->post('mandatory')[$key]:0,
                                                    'display_limit'=>($this->input->post('display_limit_value')[$key])?($this->input->post('display_limit_value')[$key]):NULL
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        /*$recipe_content_id = ($this->input->post('recipe') != '')?$this->input->post('recipe'):'';
                        if(intval($recipe_content_id)>0){
                            $rest_menu_recipe_map = array();
                                $rest_menu_recipe_map[] = array(
                                    'menu_content_id'=>$this->input->post('content_id'),
                                    'recipe_content_id'=>$recipe_content_id
                                );
                       $map_id = $this->restaurant_model->insertBatch('restaurant_menu_recipe_map',$rest_menu_recipe_map,$this->input->post('content_id'));
                        }
                        else{
                            $this->restaurant_model->DeleteMenuContent($this->input->post('content_id'));
                        }*/
                        $this->restaurant_model->deleteinsertBatch('add_ons_master',$addons,$this->input->post('entity_id'));
                        //updating price for other stores with same content id
                        $this->restaurant_model->updateData($edit_content_data,'restaurant_menu_item','content_id',$this->input->post('content_id'));
                        $res_name = $this->common_model->getResNametoDisplay($this->input->post('restaurant_id'),'','');
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a menu for restaurant'.$res_name.' - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');       
                    }          
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
            $data['category'] = $this->restaurant_model->getCategoryListData($language_slug);
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_menu_item',$entity_id);
            $data['add_ons_detail'] = $this->restaurant_model->getAddonsDetail('add_ons_master',$entity_id);
            $data['addons_category'] = $this->restaurant_model->getAddonListData($language_slug);

            //$data['recipe_list'] = $this->restaurant_model->getRecipe('recipe',$language_slug,$data['edit_records']->content_id);

            //$data['recipe'] = $this->restaurant_model->getRecipeMenu($data['edit_records']->content_id);

            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $data['is_multiple_category'] = $this->restaurant_model->getIsMultipleCategory($entity_id);
            $data['mandatory_category'] = $this->restaurant_model->getMandatory($entity_id);
            $data['is_display_limit'] = $this->restaurant_model->getIsDisplayLimit($entity_id);
            $data['is_display_limit'] = $this->restaurant_model->getIsDisplayLimit($entity_id);
            $data['is_display_limit_value'] = $this->restaurant_model->getIsDisplayLimitValue($entity_id);
            $this->load->view(ADMIN_URL.'/restaurant_menu_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxviewMenu() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(
            3 => 'menu.price',
            4 => 'res.name',
            5 => 'menu.is_combo_item',
            6=>'menu.stock',
            7=>'menu.created_date'
        );
        $sortFieldName = '';

        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }

        //Get Recored from model
        $grid_data = $this->restaurant_model->getMenuGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);        
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        //$ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        //$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue)
        $default_currency = get_default_system_currency();
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '';
            if(!empty($default_currency)){
                $currency_symbol = $default_currency;
            }else{
                $currency_symbol = $this->common_model->getCurrencySymbol($value['currency_id']);
            }
            $total = 0;
            //offer price start
            /*$offer_price = 0;
            if(!empty($ItemDiscount)) {
                foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                    if(!empty($cpnvalue['itemDetail'])) {
                        if(in_array($value['content_id'],$cpnvalue['itemDetail'])){
                            if($cpnvalue['max_amount'] <= $value['price']){ 
                                if($cpnvalue['amount_type'] == 'Percentage'){
                                    $offer_price = $value['price'] - (($value['price'] * $cpnvalue['amount'])/100);
                                }else if($cpnvalue['amount_type'] == 'Amount'){
                                    $offer_price = $value['price'] - $cpnvalue['amount'];
                                }
                            }
                        }
                    }
                }
            }*/

            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            
            //$total = ($offer_price)?number_format($offer_price, 2):'';
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            //offer price changes end
            $edit_active_access .= (in_array('restaurant_menu~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteAll('.$value['content_id'].','.$msgDelete.','.$value['is_masterdata'].')" '.$btndisable_master.' title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('restaurant_menu~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableAll('.$value['content_id'].','.$value['status'].','.$value['is_masterdata'].')" '.$btndisable_master.' title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $edit_active_access .= (in_array('restaurant_menu~ajaxStockUpdate',$this->session->userdata("UserAccessArray"))) ? '<button title="' .($value['stock']?$this->lang->line('in_stock'):$this->lang->line('out_stock')).'" alt="' .($value['stock']?$this->lang->line('in_stock'):$this->lang->line('out_stock')).'" onclick="stockAll('.$value['content_id'].','.$value['stock'].')"   class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['stock']?'battery-full':'battery-empty').'"></i></button>' : '';
            $records["aaData"][] = array(
                $nCount,
                '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">
                <input type="hidden" name="id" class="hidden-id" value="'.$value['content_id'].'">',                
                //($value['check_add_ons'])?'Customized':currency_symboldisplay(number_format_unchanged_precision($value['price'],$currency_symbol->currency_code),$currency_symbol->currency_symbol),
                ($value['check_add_ons'])?currency_symboldisplay(number_format_unchanged_precision($value['price'],$currency_symbol->currency_code),$currency_symbol->currency_symbol).' (Customized)':currency_symboldisplay(number_format_unchanged_precision($value['price'],$currency_symbol->currency_code),$currency_symbol->currency_symbol),
                $value['rname'],
                ($value['is_combo_item'] == 1) ? $this->lang->line('yes') : $this->lang->line('no'),
                ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                ($value['stock'] == 0) ? $this->lang->line('out_stock') : $this->lang->line('in_stock'),
                $edit_active_access
            ); 
            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    if($value['is_combo_item'] == 1){
                        $menu_editbtn = ((in_array('restaurant_menu~edit_menu',$this->session->userdata("UserAccessArray")))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_combo_menu_item/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $menu_editbtn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $menu_editbtn;
                    }else{
                        $menu_editbtn = ((in_array('restaurant_menu~edit_menu',$this->session->userdata("UserAccessArray")))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_menu/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                        $menu_editbtn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                        $cusLan[] = $menu_editbtn;
                    }
                }else{
                    if($value['is_combo_item'] == 1){
                        $cusLan[] = (in_array('restaurant_menu~add_menu',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add_combo_menu_item/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                    }else{
                        $cusLan[] = (in_array('restaurant_menu~add_menu',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add_menu/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                    }
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }                
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //Update status for All
    public function ajaxDisableAll() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->restaurant_model->UpdatedStatusAll($this->input->post('tblname'),$content_id,$this->input->post('status'));
            $language_slug = $this->session->userdata('language_slug');
            $status_txt = '';
            if($this->input->post('status') == 0) {
                $status_txt = 'activated';
            } else {
                $status_txt = 'deactivated';
            }
            if($this->input->post('tblname') == 'restaurant') {
                $res_name = $this->common_model->getResNametoDisplay('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant - '.$res_name);
            } elseif ($this->input->post('tblname') == 'restaurant_menu_item') {
                $resmenu_name = $this->restaurant_model->getRestaurantMenuName('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant menu - '.$resmenu_name);
            } elseif ($this->input->post('tblname') == 'restaurant_package') {
                $respkg_name = $this->restaurant_model->getRestaurantPackageName('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant package - '.$respkg_name);
            }
        }
    }
    //add package
    public function view_package(){
        //if(false) { 
            //in_array('restaurant_package~view_package',$this->session->userdata("UserAccessArray"))
            $data['meta_title'] = $this->lang->line('event_package').' | '.$this->lang->line('site_title');
            $data['Languages'] = $this->common_model->getLanguages();
            //package count
            $this->db->select('package.content_id');
            $this->db->join('restaurant as res','package.restaurant_id = res.content_id','left');
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $this->db->group_by('package.content_id');
            $data['package_count'] = $this->db->get('restaurant_package as package')->num_rows();
            $this->load->view(ADMIN_URL.'/restaurant_package',$data); 
        /*} else {
            redirect(base_url().ADMIN_URL);
        }*/
    }
    //add package
    public function add_package(){
        //if(false) {
            //in_array('restaurant_package~add_package',$this->session->userdata("UserAccessArray"))
            $data['meta_title'] = $this->lang->line('title_admin_eventadd').' '.$this->lang->line('title_admin_restaurantPackage').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('name', $this->lang->line('package').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('package').' '.$this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('detail', $this->lang->line('package').' '.$this->lang->line('description'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    if(!$this->input->post('content_id')){
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>'package',
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                    }else{                    
                        $ContentID = $this->input->post('content_id');
                    }
                    $add_data = array(                  
                        'name'=>$this->input->post('name'),
                        //'restaurant_id' =>$this->input->post('restaurant_id'),
                        //'price' =>($this->input->post('price'))?($this->input->post('price')):NULL,
                        'detail' =>$this->input->post('detail'),
                        'availability'=>implode(',', $this->input->post("availability")),
                        'content_id'=>$ContentID,
                        'language_slug'=>$this->uri->segment('4'),
                        'status'=>1,
                        'created_by' => $this->session->userdata('AdminUserID'),
                    );
                    if(!$this->input->post('content_id')){
                        $add_data['restaurant_id'] = $this->input->post('restaurant_id');
                        $add_data['price'] = $this->input->post('price');
                    } else {
                        $add_content_based_data = array(
                            'restaurant_id' => $this->input->post('restaurant_id'),
                            'price' =>$this->input->post('price')
                        );
                    }
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/package';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['max_size'] = '500'; //in KB
                        $config['encrypt_name'] = TRUE;
                        // create directory if not exists
                        if (!@is_dir('uploads/package')) {
                          @mkdir('./uploads/package', 0777, TRUE);
                        }
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/package/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "package/".$img['file_name'];
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    $this->restaurant_model->addData('restaurant_package',$add_data);
                    if(!empty($add_content_based_data)){
                        $this->restaurant_model->updateData($add_content_based_data,'restaurant_package','content_id',$ContentID);
                    }
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added restaurant package - '.$this->input->post('name'));
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_package');                 
                }
            }
            if($this->uri->segment(5)){
                $content_id = $this->uri->segment(5);
                $language_slug = "";
                $data['event_restaurant'] = $this->restaurant_model->getListforeventData('restaurant_package',$language_slug,$content_id);
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListforeventData('restaurant',$language_slug);
            $this->load->view(ADMIN_URL.'/restaurant_package_add',$data);
        /*} else {
            redirect(base_url().ADMIN_URL);
        }*/
    }
    //edit package
    public function edit_package(){
        if(in_array('restaurant_package~edit_package',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('title_admin_eventedit').' '.$this->lang->line('title_admin_restaurantPackage').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('name', $this->lang->line('package').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('package').' '.$this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('detail', $this->lang->line('package').' '.$this->lang->line('description'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $ContentID =  $this->input->post('content_id');  
                    $edit_data = array(                  
                        'name'=>$this->input->post('name'),
                        //'restaurant_id' =>$this->input->post('restaurant_id'),
                        //'price' =>($this->input->post('price'))?($this->input->post('price')):NULL,
                        'detail' =>$this->input->post('detail'),
                        'availability'=>implode(',', $this->input->post("availability")),
                        'updated_by' => $this->session->userdata('AdminUserID'),
                        'updated_date'=>date('Y-m-d H:i:s'),
                    );
                    if(!$ContentID){
                        $edit_data['restaurant_id'] = $this->input->post('restaurant_id');
                        $edit_data['price'] = $this->input->post('price');
                    } else {
                        $edit_content_based_data = array(
                            'restaurant_id' => $this->input->post('restaurant_id'),
                            'price' => $this->input->post('price')
                        );
                    }
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/package';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/package')) {
                          @mkdir('./uploads/package', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/package/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $edit_data['image'] = "package/".$img['file_name'];   
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          }  
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error']))
                    {                                                
                        $this->restaurant_model->updateData($edit_data,'restaurant_package','entity_id',$this->input->post('entity_id'));
                        if(!empty($edit_content_based_data)){
                            $this->restaurant_model->updateData($edit_content_based_data,'restaurant_package','content_id',$ContentID);
                        }
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited restaurant package - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_package');
                    }                 
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListforeventData('restaurant',$language_slug);
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_package',$entity_id);
            $this->load->view(ADMIN_URL.'/restaurant_package_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // call for ajax data
    public function ajaxviewPackage() { 
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(3=>'package.created_date',5=>'package.price',6=>'res.name',7=>'package.status');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->restaurant_model->getPackageGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();   
        //get System Option Data
        //$this->db->select('OptionValue');
        //$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        // $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = '';
            //Code for allow add/edit/delete permission :: Start
            $btndisable_master = (Disabled_HideButton($value['is_masterdata'],'yes')=='1')?'disabled':'';
            //Code for allow add/edit/delete permission :: End
            $deleteName = getModuleTilte($value['translations']);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_admin_common')),$deleteName)."'";
            $currency_symbol = $this->common_model->getCurrencySymbol($value['currency_id']);
            $edit_active_access .= (in_array('restaurant_package~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="deleteAll('.$value['content_id'].','.$msgDelete.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
            $edit_active_access .= (in_array('restaurant_package~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) ? '<button '.$btndisable_master.' onclick="disableAll('.$value['content_id'].','.$value['status'].')"  title="' .($value['status']?$this->lang->line('inactive'):$this->lang->line('active')).'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($value['status']?'ban':'check').'"></i></button>' : '';
            $price = ($value['price'])? currency_symboldisplay(number_format_unchanged_precision($value['price'],@$currency_symbol->currency_code),@$currency_symbol->currency_symbol) :'';
            $records["aaData"][] = array(
                '<input type="checkbox" name="ids[]" '.$btndisable_master.' value="'.$value["content_id"].'">',
                $nCount,
                $price,
                $value['rname'],
                ($value['status'] == 1)?$this->lang->line('active'):$this->lang->line('inactive'),
                $edit_active_access
            ); 

            $cusLan = array();
            foreach ($Languages as $lang) { 
                if(array_key_exists($lang->language_slug,$value['translations'])){
                    $edit_pkg_btn = (in_array('restaurant_package~edit_package',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit_package/'.$lang->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])).'" title="'.$this->lang->line('click_edit').'"><i class="fa fa-edit"></i> </a>' : '';
                    $edit_pkg_btn .= '( '.$value['translations'][$lang->language_slug]['name'].' )';
                    $cusLan[] = $edit_pkg_btn;
                }else{
                    $cusLan[] = (in_array('restaurant_package~add_package',$this->session->userdata("UserAccessArray"))) ? '<a href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/add_package/'.$lang->language_slug.'/'.$value['content_id'].'" title="'.$this->lang->line('click_add').'"><i class="fa fa-plus"></i></a>' : '';
                }                    
            }
            // added to specific position
            array_splice( $records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }               
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function checkExist(){
        $phone_number = ($this->input->post('phone_number') != '')?$this->input->post('phone_number'):'';
        
        if($this->input->post('add_res_branch')=='res'){
            $name = $this->input->post('res_name');
        } elseif($this->input->post('add_res_branch')=='branch'){
            $name = $this->input->post('branch_name');
        } else {
            $name = trim($this->input->post('res_name'));
            $branchname = trim($this->input->post('branch_name'));
            $name = ($name) ? $name : $branchname;
        }
        
        if($name){
            if($phone_number != ''){
                $check = $this->restaurant_model->checkExist($phone_number,$this->input->post('entity_id'),$this->input->post('content_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkExist', $this->lang->line('phones_exist'));
                    return false;
                }
            } 
        }else{
            if($phone_number != ''){
                $check = $this->restaurant_model->checkExist($phone_number,$this->input->post('entity_id'),$this->input->post('content_id'));
                echo $check;
            } 
        }
       
    }
    public function checkEmailExist(){
        $email = ($this->input->post('email') != '')?$this->input->post('email'):'';

        if($this->input->post('add_res_branch')=='res'){
            $name = $this->input->post('res_name');
        } elseif($this->input->post('add_res_branch')=='branch'){
            $name = $this->input->post('branch_name');
        } else {
            $name = trim($this->input->post('res_name'));
            $branchname = trim($this->input->post('branch_name'));
            $name = ($name) ? $name : $branchname;
        }
        
        if($name){
            if($email != ''){
                $check = $this->restaurant_model->checkEmailExist($email,$this->input->post('entity_id'),$this->input->post('content_id'));
                if($check > 0){
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('email_exist'));
                    return false;  
                }
            }
        }else{
            if($email != ''){
                $check = $this->restaurant_model->checkEmailExist($email,$this->input->post('entity_id'),$this->input->post('content_id'));
                echo $check;
            }  
        }
    }

    public function import_menu_status(){
        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenu').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/import_menu_status',$data);
    }

    //import menu
    public function import_menu()
    {
        if($this->input->post('submit_page') == 'Submit')
        {
            if (!empty($this->session->userdata('import_data'))) {
                $this->session->unset_userdata('import_data');
            }
            $this->form_validation->set_rules('import_tax', $this->lang->line('menu_file'), 'trim|xss_clean');
            if ($this->form_validation->run()) 
            { 
                $test = $_FILES['import_tax']['name'];
                /*$this->load->library('old_excel');*/
                $this->load->library('excel');
                $this->load->library('upload');
                $config['upload_path'] = './uploads/menu_import';
                $config['allowed_types'] = 'xlsx|xls|csv'; 
                $config['encrypt_name'] = TRUE;         
                if (!@is_dir('uploads/menu_import')) {
                    @mkdir('./uploads/menu_import', 0777, TRUE);
                }
                $this->upload->initialize($config);
                // If upload failed, display error
                if (!$this->upload->do_upload('import_tax')) 
                {
                    // $this->session->set_flashdata('Import_Error', $this->upload->display_errors());
                    $_SESSION['Import_Error'] = $this->upload->display_errors();
                    redirect(ADMIN_URL.'/'.$this->controller_name.'/view_menu');
                } 
                else 
                {
                    $file_data = $this->upload->data();
                    $file_path =  './uploads/menu_import/'.$file_data['file_name'];
                    // Start excel read
                    if($file_data['file_ext'] == '.xlsx' || $file_data['file_ext'] == '.xls')
                    {
                        //read file from path
                        /*$objPHPExcel = PHPExcel_IOFactory::load($file_path);*/
                        $reader = $this->excel->load("Xlsx");
                        $reader->setReadDataOnly(true);
                        $reader->setReadEmptyCells(false);
                        $objPHPExcel = $reader->load($file_path);
                        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            foreach ($cellIterator as $cell) {
                                $column = $cell->getColumn();
                                $row = $cell->getRow();
                                $data_value = (string) $cell->getValue();
                                if ($row == 2)
                                {
                                    $header[$row][$column] = $data_value;
                                } 
                                else if ($row > 2)
                                {
                                    $arr_data[$row][$column] = $data_value;
                                }
                            }
                        }
                        //get only the Cell Collection
                        /*
                        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                        foreach ($cell_collection as $cell)
                        {
                            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                            $data_value = (string)$objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                            //header will/should be in row 1 only. of course this can be modified to suit your need.
                            if ($row == 2)
                            {
                                $header[$row][$column] = $data_value;
                            } 
                            else if ($row > 2)
                            {
                                $arr_data[$row][$column] = $data_value;
                            }
                        }
                        */
                        $row = 2;
                        $d=2;
                        $Import = array();
                        
                        $menu_language_arr = array();
                        $content_id_arr = array();
                        $allowed_types = array('gif','jpg','png','jpeg');
                        $availabilityarr = array('breakfast','lunch','dinner'); 
                        for($rowcount=1; $rowcount<=count($arr_data); $rowcount++)
                        {
                            $d++;
                            $mandatoryColumnBlank = 1;
                            $add_data = array();
                            $getAddons = array();

                            // check for language
                            if (trim($arr_data[$d]['C']) != '') {
                                $add_data['language_slug'] = trim(strtolower($arr_data[$d]['C']));
                                $getAddons = $this->restaurant_model->getAddons(trim(strtolower($arr_data[$d]['C'])));//get all addons category names.
                                $getAddons = array_map('strtolower', $getAddons);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['C'].' is required.';
                            }

                            // check for restaurant
                            if (trim($arr_data[$d]['B']) != '' && trim(strtolower($arr_data[$d]['C'])) != '') {
                                $restaurant = $this->restaurant_model->getRestaurantId(trim($arr_data[$d]['B']),trim(strtolower($arr_data[$d]['C'])));
                                if (!empty($restaurant)) {
                                    $add_data['restaurant_id'] = $restaurant->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['B'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['B'].' is required.';
                            }
                            //check for Category
                            if (trim($arr_data[$d]['D']) != '' && trim(strtolower($arr_data[$d]['C'])) != '') {
                                $category_inp = trim(urldecode(str_replace("%C2%A0"," ",urlencode($arr_data[$d]['D']))));
                                $category = $this->restaurant_model->getCategoryId(trim($category_inp),trim(strtolower($arr_data[$d]['C'])));
                                if (!empty($category)) {
                                    $add_data['category_id'] = $category->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['D'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['D'].' is required.';
                            }

                            // check for name
                            $is_update = 'no';
                            if (trim($arr_data[$d]['E']) != '') {
                                $add_data['name'] = trim($arr_data[$d]['E']);
                                $add_data['item_slug'] = slugify(trim($arr_data[$d]['E']),'restaurant_menu_item','item_slug');
                                //New code added for update the value if exist                                
                                $chk_item = $this->restaurant_model->chkRestaurantMenuName(trim($arr_data[$d]['E']),trim(strtolower($arr_data[$d]['C'])), $add_data['category_id'], $add_data['restaurant_id']);
                                if(!empty($chk_item))
                                {
                                    $is_update = 'yes';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['E'].' is required.';
                            }

                            // check for saku                            
                            if(trim($arr_data[$d]['F']) != '')
                            {
                                $add_data['sku'] = trim($arr_data[$d]['F']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['F'].' is required.';
                            }

                            // check for price
                            if(trim($arr_data[$d]['G']) != '')
                            {
                                if(is_numeric($arr_data[$d]['G']) && floatval($arr_data[$d]['G'] + 0.00)>0)
                                {
                                    $add_data['price'] = trim($arr_data[$d]['G']);
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = 'Kindly fill the proper value of '.$header[2]['G'].' column';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['G'].' is required.';
                            }

                            // check for details
                            if (trim($arr_data[$d]['H']) != '') {
                                $add_data['menu_detail'] = trim($arr_data[$d]['H']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['H'].' is required.';
                            }

                            //check for the image
                            if(!empty($arr_data[$d]['I']))
                            {
                                $url = trim($arr_data[$d]['I']);
                                /*$arrContextOptions=array(
                                    "ssl"=>array(
                                        "verify_peer"=>false,
                                        "verify_peer_name"=>false,
                                    ),
                                ); */
                                //$fdata = file_get_contents($url);
                                if(!empty($url))
                                {
                                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                                    if(in_array(strtolower($ext), $allowed_types))
                                    {
                                        $random_string = random_string('alnum',12);
                                        $new = 'uploads/menu/'.$random_string.'.'.$ext;                                        
                                        //file_put_contents($new, $fdata);
                                        copy($url, $new);
                                        $add_data['image'] = 'menu/'.$random_string.'.'.$ext;
                                    }
                                    else
                                    {
                                        $mandatoryColumnBlank = 0;                                 
                                        $Import[$rowcount][] = 'Only JPG, JPEG, PNG & GIF files are allowed.';
                                    }
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;       
                                    $Import[$rowcount][] = $header[2]['I'].' unable to locate image.';
                                }
                            }
                            /*else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['I'].' is required.'; 
                            }*/

                            //Average Meal Preparation Time (Minutes)
                            if (trim($arr_data[$d]['J']) != '') {
                                $add_data['recipe_time'] = trim($arr_data[$d]['J']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['J'].' is required.';
                            }

                            //check for popular_item
                            if (trim($arr_data[$d]['K']) != '') {
                                $add_data['popular_item'] = (trim(strtolower($arr_data[$d]['K'])) == "yes")?1:0;
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['K'].' is required.';
                            }

                            //check for Food Type
                            if(trim($arr_data[$d]['L']) != '')
                            {
                                $foodtype_inp = trim(urldecode(str_replace("%C2%A0"," ",urlencode($arr_data[$d]['L']))));
                                $food_typeval = $this->restaurant_model->getFood_TypeId(trim($foodtype_inp),trim(strtolower($arr_data[$d]['C'])),$restaurant->entity_id);                                
                                if (!empty($food_typeval)) {
                                    $add_data['food_type'] = $food_typeval;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['L'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['L'].' is required.';
                            }

                            //menu availability :: start
                            if(trim($arr_data[$d]['M']) != '')
                            {
                                //Change in availability code :: Start
                                //$add_data['availability'] = str_replace(" ",",",trim($arr_data[$d]['N']));
                                $availabilitystrarr = array();
                                $availabilitytemp = str_replace(" ",",",trim($arr_data[$d]['M']));
                                if($availabilitytemp!='' && !empty($availabilitytemp))
                                {    
                                    $availabilitytemparr = explode(",",$availabilitytemp);

                                    foreach ($availabilitytemparr as $avbkey => $avbvalue)
                                    {
                                        if(in_array(strtolower($avbvalue), $availabilityarr))
                                        {
                                            $availabilitystrarr[] = ucfirst($avbvalue);
                                        }
                                    }
                                }
                                if(!empty($availabilitystrarr))
                                {
                                   $add_data['availability'] = implode(",",$availabilitystrarr); 
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = 'Kindly fill the proper value of '.$header[2]['M'].' column';
                                }
                                //Change in availability code :: End
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['M'].' is required.';
                            }
                            //menu availability :: end
                            
                            //check for combo item :: Start
                            if(trim($arr_data[$d]['O']) != '')
                            {
                                $add_data['is_combo_item'] = (trim(strtolower($arr_data[$d]['O'])) == "yes")?1:0;
                                if(trim(strtolower($arr_data[$d]['N']))=='yes')
                                {
                                    $add_data['is_combo_item'] = 0;
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;                                
                                $Import[$rowcount][] = $header[2]['O'].' is required.';
                            }

                            if(trim($arr_data[$d]['P']) != '' && $add_data['is_combo_item'] == 1)
                            {
                                $add_data['menu_detail'] = trim(str_replace(",","\r\n",$arr_data[$d]['P']));
                            }
                            else
                            {
                                if($add_data['is_combo_item'] == 1 && trim(strtolower($arr_data[$d]['N']))!='yes'){
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['P'].' is required.';
                                }
                            }
                            //check for combo item :: End

                            $addons = array();
                            //check for check_add_ons
                            $addonsArray = array();
                            if (trim(strtolower($arr_data[$d]['N']))== 'yes' && $add_data['is_combo_item']=='0')
                            {
                                $add_data['check_add_ons'] = (trim(strtolower($arr_data[$d]['N'])) == "yes")?1:0;
                                $addonsArray = array_slice($header[2],16);                                
                                $addonsArray = array_filter($addonsArray);
                                $lang_addons = array();                                
                                if (!empty($addonsArray)) {
                                    $addons_added_for_atleast_onecategory = 0; //count
                                    foreach ($addonsArray as $arrkey => $arrvalue) {
                                        if (in_array(trim($arrvalue),$getAddons)) {
                                            $lang_addons[$arrkey] = $arrvalue;
                                        }                                        
                                    }                                    
                                    foreach ($addonsArray as $Akey => $Avalue)
                                    {
                                        if(in_array(trim(strtolower($Avalue)),$getAddons) && trim($arr_data[$d][$Akey])!='')
                                        {                                            
                                            $addons_added_for_atleast_onecategory++;
                                            $category_id = $this->restaurant_model->getAddonsId(trim($Avalue),trim(strtolower($arr_data[$d]['C'])));
                                            if(strpos(trim($arr_data[$d][$Akey]), '::'))
                                            {
                                                $addon = array();
                                                //New code add for Mandatory,Maximum Limit values :: Start
                                                $addon_mand = array();
                                                if(strpos(trim($arr_data[$d][$Akey]), '>>')){
                                                    $add_onstemp = explode(">>",trim($arr_data[$d][$Akey]));
                                                    $addon_mand =explode(",",trim($add_onstemp[0]));    
                                                    $add_ons = explode("::",trim($add_onstemp[1]));
                                                }else{                                                            
                                                    $add_ons = explode("::",trim($arr_data[$d][$Akey]));
                                                }                                                
                                                //End                                                
                                                
                                                if(!empty($addon_mand) && $addon_mand[0]=='')
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Mandatory Field should be either Yes or No.';
                                                }
                                                if(!empty($addon_mand) && $addon_mand[1]!='' && (intval($addon_mand[1])>count($add_ons) || intval($addon_mand[1])<0))
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Please Provide valid limit for Maximum Limit.';
                                                }
                                                if(!empty($addon_mand) && $addon_mand[2]=='')
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Multiple Selection Field should be either Yes or No.';
                                                }

                                                for ($i=0; $i < count($add_ons); $i++) { 
                                                    $addon[$i] = explode(",", $add_ons[$i]); 
                                                }
                                                for ($i=0; $i < count($addon); $i++) { 
                                                    $addons[] = array(
                                                        'category_id'=>$category_id->entity_id,
                                                        'add_ons_name'=> trim($addon[$i][0]),
                                                        'add_ons_price'=> trim($addon[$i][1]),
                                                        'is_multiple'=> (trim(strtolower($addon_mand[2])) == "yes")?1:0,
                                                        'display_limit'=> (intval($addon_mand[1])>0 && trim(strtolower($addon_mand[2])) == "yes")?intval($addon_mand[1]):NULL,
                                                        'mandatory'=> (trim(strtolower($addon_mand[0])) == "yes")?1:0
                                                    );
                                                }
                                            }
                                            else
                                            {
                                                //New code add for Mandatory,Maximum Limit values :: Start
                                                $addon_mand = array();
                                                if(strpos(trim($arr_data[$d][$Akey]), '>>')){
                                                    $add_onstemp = explode(">>",trim($arr_data[$d][$Akey]));
                                                    $addon_mand =explode(",",trim($add_onstemp[0]));    
                                                    $add_ons = explode(",",trim($add_onstemp[1]));
                                                }else{                                                            
                                                    $add_ons = explode(",",trim($arr_data[$d][$Akey]));
                                                }                                                
                                                //End

                                                if(!empty($addon_mand) && $addon_mand[0]=='')
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Mandatory Field should be either Yes or No.';
                                                }

                                                if(!empty($addon_mand) && $addon_mand[1]!='' && (intval($addon_mand[1])>count($add_ons) || intval($addon_mand[1])<0))
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Please Provide valid limit for Maximum Limit.';
                                                }
                                                if(!empty($addon_mand) && $addon_mand[2]=='')
                                                {
                                                    $mandatoryColumnBlank = 0;
                                                    $Import[$rowcount][] = 'Multiple Selection Field should be either Yes or No.';
                                                }
                                                
                                                if(!empty($add_ons))
                                                {
                                                    $addons[] = array(
                                                        'category_id'=>$category_id->entity_id,
                                                        'add_ons_name'=> trim($add_ons[0]),
                                                        'add_ons_price'=> trim($add_ons[1]),
                                                        'is_multiple'=> (trim(strtolower($addon_mand[2])) == "yes")?1:0,
                                                        'display_limit'=> (intval($addon_mand[1])>0 && trim(strtolower($addon_mand[2])) == "yes")?intval($addon_mand[1]):NULL,
                                                        'mandatory'=> (trim(strtolower($addon_mand[0])) == "yes")?1:0
                                                    );
                                                }
                                            }                                            
                                        }
                                        else
                                        {
                                            if(trim($arr_data[$d][$Akey])!='')
                                            {
                                                $mandatoryColumnBlank = 0;
                                                $Import[$rowcount][] = trim($Avalue).', such Add ons category does not exists for now.';
                                            }
                                        }
                                    }
                                    if(strtolower(trim($arr_data[$d]['O'])) == 'yes' && $add_data['check_add_ons']==1 && $addons_added_for_atleast_onecategory==0){
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = 'Please add details for atleast one Add-ons category.';
                                    }                                    
                                } 
                            }
                            else
                            {
                                if ($add_data['is_combo_item']=='0' && trim(strtolower($arr_data[$d]['O']))== '')
                                {
                                    $mandatoryColumnBlank = 0;                                
                                    $Import[$rowcount][] = $header[2]['O'].' is required.'; 
                                }                                
                            }
                            if(empty($addons))
                            {
                                $add_data['check_add_ons']=0; 
                            }

                            // add data to community_user_detail
                            if ($mandatoryColumnBlank == 1) {
                                // check for content id , if it is to be set same
                                //ADD DATA IN CONTENT SECTION
                                if($is_update=='no')
                                {
                                    if(trim($arr_data[$d]['A']) != '')
                                    {
                                        if (!empty($menu_language_arr)) {
                                            if (in_array($arr_data[$d]['A'], $menu_language_arr)) {
                                                //name exists in the lang name as before so get the content id to add same menu item
                                                $Dkey = '';
                                                foreach ($menu_language_arr as $mkey => $mvalue) {
                                                    if ($mvalue == $arr_data[$d]['A']) {
                                                        $Dkey = $mkey;
                                                    }
                                                }
                                                if ($Dkey != '') {
                                                    $ContentID = $content_id_arr[$Dkey];
                                                }
                                                else
                                                {
                                                    $add_content = array(
                                                      'content_type'=>'menu',
                                                      'created_by'=>$this->session->userdata("AdminUserID"),  
                                                      'created_date'=>date('Y-m-d H:i:s')                      
                                                    );
                                                    $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                                    $content_id_arr[$d] = $ContentID;
                                                }
                                            }
                                            else
                                            {
                                                $add_content = array(
                                                  'content_type'=>'menu',
                                                  'created_by'=>$this->session->userdata("AdminUserID"),  
                                                  'created_date'=>date('Y-m-d H:i:s')                      
                                                );
                                                $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                                $content_id_arr[$d] = $ContentID;
                                                $menu_language_arr[$d] = $arr_data[$d]['A'];
                                            }
                                        }
                                        else
                                        {
                                            $add_content = array(
                                              'content_type'=>'menu',
                                              'created_by'=>$this->session->userdata("AdminUserID"),  
                                              'created_date'=>date('Y-m-d H:i:s')                      
                                            );
                                            $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                            $content_id_arr[$d] = $ContentID;
                                            $menu_language_arr[$d] = $arr_data[$d]['A'];
                                        }                                        
                                    }

                                    $add_data['content_id'] = $ContentID; 
                                    $add_data['status']= 1;
                                    $add_data['created_by'] =  $this->session->userdata('AdminUserID');
                                    $menu_id = $this->restaurant_model->addData('restaurant_menu_item',$add_data);
                                    $status = "New Added";
                                }
                                else
                                {
                                    $ContentID = $chk_item->content_id;
                                    $add_data['content_id'] = $ContentID; 
                                    $menu_id = $chk_item->entity_id;
                                    $this->restaurant_model->updateData($add_data,'restaurant_menu_item','entity_id',$chk_item->entity_id);
                                    $status = "Updated";
                                }

                                if(!empty($addons))
                                {
                                    foreach ($addons as $key => $value)
                                    {
                                        $addons[$key]['menu_id'] = $menu_id;
                                    }
                                    if($is_update=='no')
                                    {
                                        $this->restaurant_model->inserBatch('add_ons_master',$addons);
                                    }
                                    else
                                    {
                                        $this->restaurant_model->deleteinsertBatch('add_ons_master',$addons,$menu_id);
                                    }                                   
                                }
                                $Import[$rowcount][] = $status;
                            }
                        }
                        $import_data['arr_data'] = $arr_data;
                        $import_data['header'] = $header;
                        $import_data['Import'] = $Import;
                        $import_data['restaurant'] = $this->restaurant_model->getRestaurantName($this->input->post('restaurant_id'));
                        $this->session->set_userdata('import_data', $import_data);
                        redirect(base_url().ADMIN_URL.'/restaurant/import_menu_status');
                    }
                }
            }
        }
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$this->session->userdata('language_slug'));
        $this->load->view(ADMIN_URL.'/restaurant_menu',$data);
    }
    //to download sample files
    public function download_sample()
    {
        $this->load->helper('download');
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  

        $pth = file_get_contents(config_item('base_url')."uploads/menu_import/sample_menu_import.xlsx", false, stream_context_create($arrContextOptions));
        //$pth    =   file_get_contents(config_item('base_url')."uploads/menu_import/sample_menu_import.xlsx",false);
        $nme    =   "sample_menu_import.xlsx";
        force_download($nme, $pth);
        exit;
    }
    //get food type 
    public function getFoodType()
    {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if (intval($entity_id)> 0)
        {
            $is_vegarr = $this->restaurant_model->getMenuType($entity_id);
            $is_vegrest = $is_vegarr->food_type;
            $is_vegres = explode(",", trim($is_vegrest));
        }
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $language_slug = ($this->input->post('language_slug'))?$this->input->post('language_slug'):$this->session->userdata('language_slug');
        $result =  $this->restaurant_model->getRestaurantFoodType($restaurant_id,$language_slug);

        $html = '<option value="">'.$this->lang->line('select').'</option>';
        foreach ($result as $key => $value) {
            $class_selected = '';
            if (!empty($is_vegres) && in_array($value->entity_id, $is_vegres)){
                $class_selected = 'selected';
            }
            $html .= '<option value="'.$value->entity_id.'" '.$class_selected.'>'.$value->name.'</option>';
        }
        echo $html;
    }
    public function activeDeactiveMultiRes(){
        $res_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        $flag = $this->input->post('flag');
        $tab = $this->input->post('tab');
        if($res_content_id){
            $content_id = explode(',', $res_content_id);
            $status_txt = '';
            if($flag == 'active') {
                $status_txt = 'activated';
            } elseif ($flag == 'deactive') {
                $status_txt = 'deactivated';
            }
            if(count($content_id) == 1) {
                $language_slug = $this->session->userdata('language_slug');
                if($tab == 'res'){
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant',1);
                    $res_name = $this->common_model->getResNametoDisplay('',$content_id[0],$language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant - '.$res_name);
                } elseif ($tab == 'menu') {
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant_menu_item',1);
                    $resmenu_name = $this->restaurant_model->getRestaurantMenuName('',$content_id[0],$language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant menu - '.$resmenu_name);
                } elseif ($tab == 'pkg') {
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant_package',1);
                    $respkg_name = $this->restaurant_model->getRestaurantPackageName('',$content_id[0],$language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' restaurant package - '.$respkg_name);
                }
            } else {
                if($tab == 'res'){
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant',1);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple restaurants');
                } elseif ($tab == 'menu') {
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant_menu_item',1);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple restaurant menus');
                } elseif ($tab == 'pkg') {
                    $data = $this->common_model->activeDeactiveMulti($content_id,$flag,'restaurant_package',1);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' multiple restaurant packages');
                }
            }
            echo json_encode($data);
        }
    }
    //New code for add food type from resturant section :: Start :: 01-02-2021
    public function Addfoodtype()
    {
        $language_slug = $this->input->post('language_slug');
        $name = ($this->input->post('name'))?trim($this->input->post('name')):'en';
        $restaurant_id = ($this->input->post('restaurant_id'))?trim($this->input->post('restaurant_id')):0;

        if($name!='')
        {
            //ADD DATA IN CONTENT SECTION
            $add_content = array(
              'content_type'=>'food_type',
              'created_by'=>$this->session->userdata("AdminUserID"),  
              'created_date'=>date('Y-m-d H:i:s')                      
            );
            $ContentID = $this->restaurant_model->addData('content_general',$add_content);
            $add_data = array(                   
                'name'=>$name,
                'is_veg'=>$this->input->post('is_veg'),
                'content_id'=>$ContentID,
                'language_slug'=>$language_slug,
                'status'=>1,
                'created_by' => $this->session->userdata('AdminUserID')
            );
            $this->restaurant_model->addData('food_type',$add_data);  
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added food type - '.$name);
            $is_vegres = array();
            if($restaurant_id>0)
            {
                $is_vegarr = $this->restaurant_model->getAllfoodType($restaurant_id);
                $is_vegrest = $is_vegarr->food_type;
                $is_vegres = explode(",", trim($is_vegrest));
            }

            $result = $this->restaurant_model->getFoodType('food_type',$language_slug);

            $html = '<option>'.$this->lang->line('select').'</option>';
            foreach ($result as $key => $value)
            {
                 $class_selected = '';
                 if (!empty($is_vegres) && in_array($value->entity_id, $is_vegres)){
                     $class_selected = 'selected';
                 }
                $html .= '<option value="'.$value->entity_id.'" '.$class_selected.'>'.$value->name.'</option>';
            }
            echo $html;
        }
    }
    //New code for add food type from resturant section :: End :: 01-02-2021

    //add combo menu item
    public function add_combo_menu_item(){
        if(in_array('restaurant_menu~add_menu',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('add_combo_item').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|callback_checkResMenuNameExist');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('category_id', $this->lang->line('category'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                $this->form_validation->set_rules('item_list[]',$this->lang->line('combo_items'), 'trim|required');
                if ($this->form_validation->run())
                {  
                    if(!empty($this->input->post('content_id')))
                    {
                        $ContentID = $this->input->post('content_id');
                        $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                        $item_slug = $slug->item_slug;
                    }
                    else
                    {   
                        //ADD DATA IN CONTENT SECTION
                        $add_content = array(
                          'content_type'=>'menu',
                          'created_by'=>$this->session->userdata("AdminUserID"),  
                          'created_date'=>date('Y-m-d H:i:s')                      
                        );
                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                        $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug');               
                    }
                    $add_data = array(                  
                        'name' => $this->input->post('name'),
                        'item_slug' => $item_slug,
                        'restaurant_id' => $this->input->post('restaurant_id'),
                        'category_id' => $this->input->post('category_id'),
                        'price' => $this->input->post('price'),
                        //'ingredients' => $this->input->post('ingredients'),
                        'recipe_time' => $this->input->post('recipe_time'),
                        'popular_item' => ($this->input->post('popular_item')) ? $this->input->post('popular_item') : '0',
                        'availability' => implode(',', $this->input->post("availability")),
                        'status' => 1,
                        'is_combo_item' => 1,
                        'content_id' => $ContentID,
                        'language_slug' => $this->uri->segment('4'),
                        'created_by' => $this->session->userdata('AdminUserID'),
                        'food_type' => $this->input->post("food_type"),
                        'check_add_ons' => ($this->input->post('check_add_ons')) ? $this->input->post('check_add_ons') : 0
                    ); 
                    $add_content_data = array('price' => ($this->input->post('price'))?$this->input->post('price'):NULL );
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/menu';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/menu')) {
                          @mkdir('./uploads/menu', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/menu/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End

                          $add_data['image'] = "menu/".$img['file_name'];   
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }
                    if(empty($data['Error'])){
                        if(!empty($this->input->post('item_list'))){
                            $add_data['menu_detail'] = '';
                            foreach ($this->input->post('item_list') as $key => $value) {
                                foreach ($value as $k => $val) {
                                    if($val['item_name'] != ''){
                                        if($k == 0) {
                                            $add_data['menu_detail'] .= $val['item_name'];
                                        } else {
                                            $add_data['menu_detail'] .= "\r\n".$val['item_name'];
                                        }
                                    }
                                }
                            }
                        }
                        $menu_id = $this->restaurant_model->addData('restaurant_menu_item',$add_data);
                        //updating price for other restaurants with same content id
                        $this->restaurant_model->updateData($add_content_data,'restaurant_menu_item','content_id',$ContentID);
                        $res_name = $this->common_model->getResNametoDisplay($this->input->post('restaurant_id'),'','');
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a menu for restaurant'.$res_name.' - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_add');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');               
                    }                                        
                     
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
            $data['category'] = $this->restaurant_model->getCategoryListData($language_slug);
            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $data['language_slug'] = $language_slug;
            $this->load->view(ADMIN_URL.'/restaurant_combo_menu_item_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }

    //edit combo menu item
    public function edit_combo_menu_item() {
        if(in_array('restaurant_menu~edit_menu',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('edit').' '.$this->lang->line('combo_item').' | '.$this->lang->line('site_title');
            if($this->input->post('submit_page') == "Submit")
            {
                $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|callback_checkResMenuNameExist');
                $this->form_validation->set_rules('restaurant_id', $this->lang->line('restaurant').' '.$this->lang->line('name'), 'trim|required');
                $this->form_validation->set_rules('category_id', $this->lang->line('category'), 'trim|required');
                $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required');
                $this->form_validation->set_rules('recipe_time', $this->lang->line('recipe_time'), 'trim|required');
                $this->form_validation->set_rules('availability[]', $this->lang->line('availability'), 'trim|required');
                $this->form_validation->set_rules('item_list[]',$this->lang->line('combo_items'), 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {  
                    $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'),'restaurant_menu_item');
                    $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                    if (!empty($slug->item_slug)) { 
                        $item_slug = $slug->item_slug;
                    }
                    else
                    {
                        $item_slug = slugify($this->input->post('name'),'restaurant_menu_item','item_slug','content_id',$content_id->content_id);
                    }
                    $edit_data = array(                  
                        'name'=>$this->input->post('name'),
                        'item_slug'=>$item_slug,
                        'restaurant_id' =>$this->input->post('restaurant_id'),
                        'category_id' =>$this->input->post('category_id'),
                        'price' => $this->input->post('price'),
                        //'ingredients' =>$this->input->post('ingredients'),
                        'recipe_time'=>$this->input->post('recipe_time'),
                        'popular_item' =>($this->input->post('popular_item'))?$this->input->post('popular_item'):'0',
                        'availability'=>implode(',', $this->input->post("availability")),
                        'updated_by' => $this->session->userdata('AdminUserID'),
                        'updated_date' => date('Y-m-d H:i:s'),
                        'food_type'=>$this->input->post("food_type"),
                        'check_add_ons'=>($this->input->post('check_add_ons'))?$this->input->post('check_add_ons'):0
                    );
                    $edit_content_data = array('price'=>($this->input->post('price'))?$this->input->post('price'):NULL);
                    if (!empty($_FILES['Image']['name']))
                    {
                        $this->load->library('upload');
                        $config['upload_path'] = './uploads/menu';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                        $config['max_size'] = '500'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir('uploads/menu')) {
                          @mkdir('./uploads/menu', 0777, TRUE);
                        }
                        $this->upload->initialize($config);                  
                        if ($this->upload->do_upload('Image'))
                        {
                          $img = $this->upload->data();

                          //Code for compress image :: Start
                          $fileName = basename($img['file_name']);                   
                          $imageUploadPath = './uploads/menu/'. $fileName; 
                          $imageTemp = $_FILES["Image"]["tmp_name"];
                          $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                          //Code for compress image :: End
                          
                          $edit_data['image'] = "menu/".$img['file_name'];   
                          if($this->input->post('uploaded_image')){
                            @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                          } 
                        }
                        else
                        {
                          $data['Error'] = $this->upload->display_errors();
                          $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                        }
                    }    
                    if(empty($data['Error'])){
                        if(!empty($this->input->post('item_list'))){
                            $edit_data['menu_detail'] = '';
                            foreach ($this->input->post('item_list') as $key => $value) {
                                foreach ($value as $k => $val) {
                                    if($val['item_name'] != ''){
                                        if($k == 0) {
                                            $edit_data['menu_detail'] .= $val['item_name'];
                                        } else {
                                            $edit_data['menu_detail'] .= "\r\n".$val['item_name'];
                                        }
                                    }
                                }
                            }
                        }
                        $this->restaurant_model->updateData($edit_data,'restaurant_menu_item','entity_id',$this->input->post('entity_id'));
                        //updating price for other stores with same content id
                        $this->restaurant_model->updateData($edit_content_data,'restaurant_menu_item','content_id',$this->input->post('content_id'));
                        $res_name = $this->common_model->getResNametoDisplay($this->input->post('restaurant_id'),'','');
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a menu for restaurant'.$res_name.' - '.$this->input->post('name'));
                        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                        $_SESSION['page_MSG'] = $this->lang->line('success_update');
                        redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu');       
                    }          
                }
            }
            $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
            $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$language_slug);
            $data['category'] = $this->restaurant_model->getCategoryListData($language_slug);
            $entity_id = ($this->uri->segment('5'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))):$this->input->post('entity_id');
            $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_menu_item',$entity_id);
            $data['food_typearr'] = $this->restaurant_model->getFoodType('food_type',$language_slug);
            $data['language_slug'] = $language_slug;
            $this->load->view(ADMIN_URL.'/restaurant_combo_menu_item_add',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // menu suggestion start
    public function menu_item_suggestion(){
        if(in_array('restaurant_menu~menu_item_suggestion',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('manage_item_suggestion').' | '.$this->lang->line('site_title');
            $data['restaurant'] = $this->restaurant_model->get_active_restaurants();
            $this->load->view(ADMIN_URL.'/restaurant_menu_item_suggestion',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function get_menu_items(){
        $restaurant_id = $this->input->post('entity_id');
        $html = '';
        if(!empty($restaurant_id)){
            $res_content_id = $this->restaurant_model->getContentId($restaurant_id,'restaurant');
            //check if menu suggestion already added for this restaurant.
            $chk_menu_suggestion = $this->restaurant_model->check_menu_suggestion($res_content_id->content_id);
            $result =  $this->restaurant_model->get_restaurant_menu_items($restaurant_id);
            
            //if menu suggestion already added then,
            if(!empty($chk_menu_suggestion)){
                $chk_menu_suggestion = array_column($chk_menu_suggestion, "menu_content_id");
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $selected = (in_array($value->content_id, $chk_menu_suggestion))?'selected':'';
                        $html .= "<option value='".$value->content_id."'".$selected." >".$value->name."</option>";
                    }
                }
            } else { 
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $html .= '<option value='.$value->content_id.'>'.$value->name.'</option>';
                    }
                }
            }
        }
        echo $html;
    }
    public function add_menu_suggestion(){
        if(in_array('restaurant_menu~menu_item_suggestion',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('menu_item_suggestion').' | '.$this->lang->line('site_title');
            $data['restaurant'] = $this->restaurant_model->get_active_restaurants();

            if($this->input->post('submit_page') == "Submit") {   
                $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
                //$this->form_validation->set_rules('item_id[]', 'Menu Item', 'trim|required');
                
                //check form validation using codeigniter
                if ($this->form_validation->run()) {
                    $restaurant_id = $this->input->post('restaurant_id');
                    $res_content_id = $this->restaurant_model->getContentId($restaurant_id,'restaurant');
                    $res_name = $this->common_model->getResNametoDisplay($restaurant_id);
                    $item_id = $this->input->post('item_id');
                    $add_suggestion = array();
                    foreach ($item_id as $key => $value) {
                        $add_suggestion[] = array(
                            'restaurant_content_id'=>$res_content_id->content_id,
                            'menu_content_id'=>$value
                        );
                    }
                    //check if menu suggestion already added for this restaurant.
                    $chk_menu_suggestion = $this->restaurant_model->check_menu_suggestion($res_content_id->content_id);
                    if(!empty($chk_menu_suggestion)){
                        $map_id = $this->restaurant_model->deleteInsertMenuSuggestion($res_content_id->content_id,$add_suggestion);
                    } else {
                        $map_id = $this->restaurant_model->deleteInsertMenuSuggestion('',$add_suggestion);
                    }
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' updated top recommendations for restaurant - '.$res_name);
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                    redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/menu_item_suggestion');
                }
            }
            $this->load->view(ADMIN_URL.'/restaurant_menu_item_suggestion',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    // menu suggestion end
    //delete multiple menu
    public function DeleteMultiRes(){
        $store_content_id = ($this->input->post('arrayData'))?$this->input->post('arrayData'):"";
        if($store_content_id){
            $content_id = explode(',', $store_content_id);
            if(count($content_id) == 1) {
                $language_slug = $this->session->userdata('language_slug');
                $resmenu_name = $this->restaurant_model->getRestaurantMenuName('',$content_id[0],$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted restaurant menu - '.$resmenu_name);
            } else {
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted multiple restaurant menus');
            }
            $data = $this->restaurant_model->deleteMultiMenu($content_id);
            echo json_encode($data);
        }
    }

    public function ajax_online_offline()
    {
        $is_online = ($this->input->post('is_online'))?$this->input->post('is_online'):'no';
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($is_online=='yes')
        {
            //Time sotre base on UTC time zone :: Start
            date_default_timezone_set(default_timezone);
            $offlinetime=time();

            $timezone_name = default_timezone;
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
            date_default_timezone_set($timezone_name);
            //Time sotre base on UTC time zone :: End

            $off_time = ($this->input->post('off_time'))?$this->input->post('off_time'):'0';            
            if($off_time>0)
            {
                $offlinetime=$offlinetime+$off_time*60;
            }            

            if($content_id != ''){
                $this->restaurant_model->online_offline_all('restaurant',$content_id,$this->input->post('rest_status'),$offlinetime);
                $language_slug = $this->session->userdata('language_slug');
                $online_offline_txt = '';
                if($this->input->post('rest_status') == 0) {
                    $online_offline_txt = 'online';
                } else {
                    $online_offline_txt = 'offline';
                }
                $res_name = $this->common_model->getResNametoDisplay('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made restaurant '.$res_name.' '.$online_offline_txt);
            }            
        }
        else
        {
            if($content_id != ''){
                $this->restaurant_model->online_offline_all('restaurant',$content_id,$this->input->post('status'),0);
                $language_slug = $this->session->userdata('language_slug');
                $online_offline_txt = '';
                if($this->input->post('status') == 0) {
                    $online_offline_txt = 'online';
                } else {
                    $online_offline_txt = 'offline';
                }
                $res_name = $this->common_model->getResNametoDisplay('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made restaurant '.$res_name.' '.$online_offline_txt);
            }
        }        
    }
    public function fetchbranchAdmin() {
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?$this->input->post('restaurant_owner_id'):'';
        $html = '<option>'.$this->lang->line('select').'</option>';
        if($restaurant_owner_id!='')
        {
            $branchadmin = $this->restaurant_model->get_brachadmin($this->session->userdata('AdminUserID'),$restaurant_owner_id);           
            foreach ($branchadmin as $key => $value)
            {
                $html .= '<option value="'.$value->entity_id .'">'.$value->first_name.' '.$value->last_name.'</option>';
            }
        }        
        echo $html; exit;
    }
    public function EventPackagescript()
    {
        echo "Please contact to admin"; exit;
        $this->db->select('entity_id,restaurant_id');        
        $resdata = $this->db->get('restaurant_package')->result();

        if($resdata && !empty($resdata))
        {
            for($i=0;$i<count($resdata);$i++)
            {
                $this->db->select('entity_id,content_id');
                $this->db->where('entity_id',$resdata[$i]->restaurant_id);        
                $resultcont = $this->db->get('restaurant')->first_row();
                if($resultcont && !empty($resultcont))
                {
                    $updateData = array('restaurant_id'=>$resultcont->content_id); 
                    $this->restaurant_model->updateData($updateData,'restaurant_package','entity_id',$resdata[$i]->entity_id);
                }                
            }
        }
    }
    //Code for menu set as out of stock :: Start
    public function ajaxStockUpdate() {
        $content_id = ($this->input->post('content_id') != '')?$this->input->post('content_id'):'';
        if($content_id != ''){
            $this->restaurant_model->UpdatedStockAll($this->input->post('tblname'),$content_id,$this->input->post('stock'));
            $language_slug = $this->session->userdata('language_slug');
            $resmenu_name = $this->restaurant_model->getRestaurantMenuName('',$content_id,$language_slug);
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' updated stock of item - '.$resmenu_name);
        }
    }
    //Code for menu set as out of stock :: End
    //Ajax Reorder
    public function ajaxReorder() {
        $dataid = ($this->input->post('dataid') != '')?$this->input->post('dataid'):'';
        $restaurant_owner_id = ($this->input->post('restaurant_owner_id') != '')?$this->input->post('restaurant_owner_id'):$this->session->userdata('AdminUserID');
        if(!empty($dataid))
        {
            $menu_addon_seqmap = array();
            $sequence_no =1;
            foreach($dataid as $key => $value) {
                $menu_addon_seqmap[] = array(
                    'restaurant_owner_id'=>$restaurant_owner_id,
                    'menu_content_id'=>$value,
                    'sequence_no'=>$sequence_no
                );
                $sequence_no++;
            }            
            $data = $this->restaurant_model->insertBatchReorder('menu_item_sequencemap',$menu_addon_seqmap,$restaurant_owner_id);            
            //echo json_encode($data);
        }
    }
    public function content_idchk()
    {
        /*$this->db->select('content_general_id as content_id');
        $this->db->where('content_type','menu');
        $result =  $this->db->get('content_general')->result();
        if($result && !empty($result))
        {
            for($i=0;$i<count($result);$i++)
            {
                $this->db->select('entity_id');
                $this->db->where('content_id',$result[$i]->content_id);
                $res_menu =  $this->db->get('restaurant_menu_item')->first_row();
                if($res_menu && !empty($res_menu))
                {
                }
                else
                {
                    $this->db->where('content_general_id',$result[$i]->content_id);
                    $this->db->delete('content_general'); 
                }                
            }
        }*/   
    }
    //restaurant import :: start
    public function download_restaurant_sample()
    {
        $this->load->helper('download');
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $pth = file_get_contents(config_item('base_url')."uploads/restaurant_import/sample_restaurant_import_file.xlsx", false, stream_context_create($arrContextOptions));
        //$pth    =   file_get_contents(config_item('base_url')."uploads/restaurant_import/sample_restaurant_import_file.xlsx");
        $nme    =   "sample_restaurant_import_file.xlsx";
        force_download($nme, $pth);
        exit;
    }
    //import menu
    public function import_restaurant()
    {
        if($this->input->post('submit_page') == 'Submit')
        {   
            $this->form_validation->set_rules('import_restaurant_file', $this->lang->line('res_file'), 'trim|xss_clean');
            $this->form_validation->set_rules('select_timezone', $this->lang->line('select_timezone'), 'trim|required');
            if ($this->form_validation->run()) 
            {
                $test = $_FILES['import_restaurant_file']['name'];
                $this->load->library('Excel');
                $this->load->library('upload');
                $config['upload_path'] = './uploads/restaurant_import';
                $config['allowed_types'] = 'xlsx|xls|csv'; 
                $config['encrypt_name'] = TRUE;         
                if (!@is_dir('uploads/restaurant_import')) {
                    @mkdir('./uploads/restaurant_import', 0777, TRUE);
                }
                $this->upload->initialize($config);               
                // If upload failed, display error
                if (!$this->upload->do_upload('import_restaurant_file')) 
                { 
                    // $this->session->set_flashdata('Import_Error', $this->upload->display_errors());
                    $_SESSION['Import_Error'] = $this->upload->display_errors();
                    redirect(ADMIN_URL.'/'.$this->controller_name.'/view');
                } 
                else 
                {
                    $file_data = $this->upload->data();            
                    $file_path =  './uploads/restaurant_import/'.$file_data['file_name'];
                    // Start excel read
                    if($file_data['file_ext'] == '.xlsx' || $file_data['file_ext'] == '.xls')
                    {
                        ini_set('memory_limit', -1);
                        set_time_limit(0);

                        //read file from path
                        $reader = $this->excel->load("Xlsx");
                        $reader->setReadDataOnly(true);
                        $reader->setReadEmptyCells(false);
                        $objPHPExcel = $reader->load($file_path);
                        //get only the Cell Collection
                        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            foreach ($cellIterator as $cell) {
                                $column = $cell->getColumn();
                                $row = $cell->getRow();
                                $data_value = (string) $cell->getValue();
                                if ($row == 2)
                                {
                                    $header[$row][$column] = $data_value;
                                } 
                                else if ($row > 2)
                                {
                                    $arr_data[$row][$column] = $data_value;
                                }
                            }
                        }
                        $row = 2;
                        $d=2;
                        $Import = array();
                        $res_language_arr = array();
                        $languagechk_arr= array('en');
                        $content_id_arr = array();
                        $allowed_types = array('gif','jpg','png','jpeg');                        
                        for($rowcount=1; $rowcount<=count($arr_data); $rowcount++)
                        {
                            $add_data = array(); $addaddress_data = array();
                            $d++;
                            $mandatoryColumnBlank = 1;
                            //unique entry in column A (restaurant language name) :: start
                            /*if(trim($arr_data[$d]['A']) != '') {
                                if(!empty($res_language_arr)) {
                                    if(in_array($arr_data[$d]['A'], $res_language_arr)) {
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = "Please add unique value in ".$header[2]['A'];
                                    }
                                }
                            }*/
                            //unique entry in column A (restaurant language name) :: end
                            
                            //Check for first column
                            if (trim($arr_data[$d]['A']) == '') {
                                $menu_lang = trim($arr_data[$d]['A']);
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['A'].' is required.';
                            }

                            //Restaurant Owner
                            if (trim($arr_data[$d]['B']) != '') { 
                                $restaurant_owner = $this->restaurant_model->getRestaurantOwner(trim($arr_data[$d]['B']));
                                if (!empty($restaurant_owner))
                                {
                                    $add_data['restaurant_owner_id'] = $restaurant_owner->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['B'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['B'].' is required.';
                            }

                            //Branch Admin
                            if (trim($arr_data[$d]['C']) != '') { 
                                $branch_admin = $this->restaurant_model->getBranchAdmin(trim($arr_data[$d]['C']),$this->session->userdata('AdminUserID'));
                                if (!empty($branch_admin))
                                {
                                    $add_data['branch_admin_id'] = $branch_admin->entity_id;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['C'].' details not found';
                                }
                            }

                            //Check for restaurant
                            $is_update = 'no'; $restaurant_id = 0;
                            if (trim($arr_data[$d]['D']) != '' && trim($arr_data[$d]['E']) != '') { 
                                $restaurant = $this->restaurant_model->getRestaurantID(trim($arr_data[$d]['D']),trim($arr_data[$d]['E']));
                                if (!empty($restaurant))
                                {
                                    $is_update = 'yes';
                                    $restaurant_id = $restaurant->entity_id;
                                }
                                else
                                {
                                    $add_data['restaurant_slug'] = slugify(trim($arr_data[$d]['D']),'restaurant','restaurant_slug');
                                    $add_data['name'] = trim($arr_data[$d]['D']);                                    
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['D'].' is required.';
                            }

                            //check for language
                            if (trim($arr_data[$d]['E']) != '') {
                                $add_data['language_slug'] = trim($arr_data[$d]['E']);
                                $addaddress_data['language_slug'] = trim($arr_data[$d]['E']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['E'].' is required.';
                            }
                            //phone code and contact number
                            if (trim($arr_data[$d]['F']) != '' && trim($arr_data[$d]['G']) != '') {
                                $add_data['phone_code'] = str_replace("+","",trim($arr_data[$d]['F']));
                                $add_data['phone_number'] = trim($arr_data[$d]['G']);
                            }
                            else
                            {
                                if(trim($arr_data[$d]['F']) == ''){
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['F'].' is required.';
                                } else {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['G'].' is required.';
                                }
                            }
                            //email
                            if (trim($arr_data[$d]['H']) != '') {
                                if (!empty($restaurant)){
                                    $email_exists = $this->restaurant_model->checkEmailExist(trim($arr_data[$d]['H']),$restaurant->entity_id,$restaurant->content_id,$arr_data[$d]['E']);
                                } else {
                                    $email_exists = $this->restaurant_model->checkEmailExist(trim($arr_data[$d]['H']),'','',$arr_data[$d]['E']);
                                }
                                if($email_exists > 0){
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = 'Email already exists!';                                    
                                } else {
                                    $add_data['email'] = trim($arr_data[$d]['H']);
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['H'].' is required.';
                            }
                            //check for the banner image
                            if(!empty($arr_data[$d]['I']))
                            {
                                $url = trim($arr_data[$d]['I']);
                                $arrContextOptions=array(
                                    "ssl"=>array(
                                        "verify_peer"=>false,
                                        "verify_peer_name"=>false,
                                    ),
                                ); 
                                //$fdata = file_get_contents($url, false, stream_context_create($arrContextOptions));
                                if(!empty($url))
                                {
                                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                                    if(in_array(strtolower($ext), $allowed_types))
                                    {
                                        $random_string = random_string('alnum',12);
                                        $new = 'uploads/restaurant/'.$random_string.'.'.$ext;
                                        //file_put_contents($new, $fdata);
                                        copy($url, $new);
                                        $add_data['image'] = 'restaurant/'.$random_string.'.'.$ext;
                                    }
                                    else
                                    {
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = 'Only JPG, JPEG, PNG & GIF files are allowed.';
                                    }
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['I'].' unable to locate image.';
                                }
                            }

                            //check for the background image
                            if(!empty($arr_data[$d]['J']))
                            {
                                $url = trim($arr_data[$d]['J']);
                                $arrContextOptions=array(
                                    "ssl"=>array(
                                        "verify_peer"=>false,
                                        "verify_peer_name"=>false,
                                    ),
                                ); 
                                //$fdata = file_get_contents($url, false, stream_context_create($arrContextOptions));
                                if(!empty($url))
                                {
                                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                                    if(in_array(strtolower($ext), $allowed_types))
                                    {
                                        $random_string = random_string('alnum',12);
                                        $new = 'uploads/restaurant_background/'.$random_string.'.'.$ext;
                                        //file_put_contents($new, $fdata);
                                        copy($url, $new);
                                        $add_data['background_image'] = 'restaurant_background/'.$random_string.'.'.$ext;
                                    }
                                    else
                                    {
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = 'Only JPG, JPEG, PNG & GIF files are allowed.';
                                    }
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['J'].' unable to locate image.';
                                }
                            }

                            //Check for address
                            if (trim($arr_data[$d]['K']) != ''){
                                $addaddress_data['address'] = trim($arr_data[$d]['K']);                                
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['K'].' is required.';
                            }
                            //Check for latitude
                            if (trim($arr_data[$d]['L']) != ''){
                                $addaddress_data['latitude'] = trim($arr_data[$d]['L']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['L'].' is required.';
                            }
                            //Check for longitude
                            if (trim($arr_data[$d]['M']) != ''){
                                $addaddress_data['longitude'] = trim($arr_data[$d]['M']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['M'].' is required.';
                            }
                            //country
                            if (trim($arr_data[$d]['N']) != ''){
                                $addaddress_data['country'] = trim($arr_data[$d]['N']);
                                $add_data['currency_id'] = DEFAULT_CURRENCY_ID;
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['N'].' is required.';
                            }
                            //Check for state
                            if (trim($arr_data[$d]['O']) != ''){
                                $addaddress_data['state'] = trim($arr_data[$d]['O']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['O'].' is required.';
                            }
                            //Check for city
                            if(trim($arr_data[$d]['P']) != ''){
                                $addaddress_data['city'] = trim($arr_data[$d]['P']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['P'].' is required.';
                            }
                            //Check for postal code
                            if(trim($arr_data[$d]['Q']) != ''){
                                $addaddress_data['zipcode'] = trim($arr_data[$d]['Q']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['Q'].' is required.';
                            }

                            //Food Type
                            if(trim($arr_data[$d]['R']) != '')
                            {
                                $foodtype_inp = trim(urldecode(str_replace("%C2%A0"," ",urlencode($arr_data[$d]['R']))));
                                $food_typeval = $this->restaurant_model->getFoodTypeIds_forRes(trim($foodtype_inp),trim($arr_data[$d]['E']));
                                if (!empty($food_typeval)) {
                                    $add_data['food_type'] = $food_typeval;
                                }
                                else
                                {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['R'].' details not found';
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['R'].' is required.';
                            }

                            //service tax
                            if(trim($arr_data[$d]['S']) != '' && trim($arr_data[$d]['T']) != ''){
                                $add_data['amount_type'] = (trim($arr_data[$d]['S']) == 'percentage')?'Percentage':'Amount';
                                $add_data['amount'] = trim($arr_data[$d]['T']);
                            }
                            else
                            {
                                if(trim($arr_data[$d]['S']) == ''){
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['S'].' is required.';
                                } else {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['T'].' is required.';
                                }
                            }
                            //service fee
                            if(trim($arr_data[$d]['U']) != ''){
                                if(trim(strtolower($arr_data[$d]['U'])) == 'yes') {
                                    $add_data['is_service_fee_enable'] = 1;
                                    if(trim($arr_data[$d]['V']) != '' && trim($arr_data[$d]['W']) != '') {
                                        $add_data['service_fee_type'] = (trim(strtolower($arr_data[$d]['V'])) == 'percentage')?'Percentage':'Amount';
                                        $add_data['service_fee'] = trim($arr_data[$d]['W']);
                                    } else {
                                        if(trim($arr_data[$d]['V']) == ''){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['V'].' is required.';
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['W'].' is required.';
                                        }
                                    }
                                } else {
                                    $add_data['is_service_fee_enable'] = 0;
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['U'].' is required.';
                            }
                            //Reservation
                            if(trim($arr_data[$d]['X']) != ''){
                                if(trim(strtolower($arr_data[$d]['X'])) == 'yes'){
                                    $add_data['allow_event_booking'] = 0;
                                    $add_data['capacity'] = NULL;
                                    $add_data['event_online_availability'] = NULL;
                                    $add_data['event_minimum_capacity'] = NULL;
                                    $add_data['allow_event_booking'] = 1;
                                    if(trim($arr_data[$d]['Y']) != '' && trim($arr_data[$d]['Z']) != '' && trim($arr_data[$d]['AA']) != '') {
                                        $resultant_val = (trim($arr_data[$d]['Y']) * trim($arr_data[$d]['Z']))/100;

                                        if(trim($arr_data[$d]['Y']) > trim($arr_data[$d]['AA'])) {
                                            if(trim($arr_data[$d]['AA']) < $resultant_val){
                                                $add_data['capacity'] = trim($arr_data[$d]['Y']);
                                                $add_data['event_online_availability'] = trim($arr_data[$d]['Z']);
                                                $add_data['event_minimum_capacity'] = trim($arr_data[$d]['AA']);
                                            } else {
                                                $mandatoryColumnBlank = 0;
                                                $Import[$rowcount][] = $header[2]['AA'].' must be less than '.$header[2]['Y'];
                                            }
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['Y'].' must be greater than '.$header[2]['AA'];
                                        }
                                    } else {
                                        if(trim($arr_data[$d]['Y']) == '') {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['Y'].' is required.';
                                        } else if(trim($arr_data[$d]['Z']) == '') {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['Z'].' is required.';
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AA'].' is required.';
                                        }
                                    }
                                } else {
                                    $add_data['allow_event_booking'] = 0;
                                    $add_data['capacity'] = NULL;
                                    $add_data['event_online_availability'] = NULL;
                                    $add_data['event_minimum_capacity'] = NULL;
                                }
                            }
                            else
                            {
                                // $mandatoryColumnBlank = 0;
                                // $Import[$rowcount][] = $header[2]['X'].' is required.';
                            }
                            
                            //printer available
                            if(trim($arr_data[$d]['AB']) != ''){
                                if(trim(strtolower($arr_data[$d]['AB'])) == 'yes') {
                                    $add_data['is_printer_available'] = 1;

                                    if(trim($arr_data[$d]['AC']) != '' && trim($arr_data[$d]['AD']) != '') {
                                        $add_data['printer_paper_height'] = trim($arr_data[$d]['AC']);
                                        $add_data['printer_paper_width'] = trim($arr_data[$d]['AD']);
                                    } else {
                                        if(trim($arr_data[$d]['AC']) == ''){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AC'].' is required.';
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AD'].' is required.';
                                        }
                                    }
                                } else {
                                    $add_data['is_printer_available'] = 0;
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AB'].' is required.';
                            }

                            //restaurant timings & closed days
                            $timingsArr = array();
                            if(trim($arr_data[$d]['AE']) != ''){
                                $add_data['enable_hours'] = 1;
                                $timingsArr = $this->restaurant_model->gettimingsArr($add_data['enable_hours'],trim($arr_data[$d]['AE']),trim($arr_data[$d]['AF']),$this->input->post('select_timezone'));
                                $add_data['timings'] = serialize($timingsArr);
                            }
                            else
                            {
                                $add_data['enable_hours'] = 0;
                                $timingsArr = $this->restaurant_model->gettimingsArr($add_data['enable_hours']);
                                $add_data['timings'] = serialize($timingsArr);
                            }
                            //contractual commission :: pickup
                            if(trim($arr_data[$d]['AG']) != ''){
                                $add_data['contractual_commission'] = trim($arr_data[$d]['AG']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AG'].' is required.';
                            }

                            //Contractual Commission Type :: pickup
                            if(trim($arr_data[$d]['AH']) != ''){                                
                                $add_data['contractual_commission_type'] = (trim(strtolower($arr_data[$d]['AH'])) == 'percentage')?'Percentage':'Amount';
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AH'].' is required.';
                            }
                            //contractual commission :: delivery
                            if(trim($arr_data[$d]['AI']) != ''){
                                $add_data['contractual_commission_delivery'] = trim($arr_data[$d]['AI']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AI'].' is required.';
                            }

                            //Contractual Commission Type :: delivery
                            if(trim($arr_data[$d]['AJ']) != ''){                                
                                $add_data['contractual_commission_type_delivery'] = (trim(strtolower($arr_data[$d]['AJ'])) == 'percentage')?'Percentage':'Amount';
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AJ'].' is required.';
                            }

                            //Credit Card Fee Applicable :: Start
                            if(trim($arr_data[$d]['AK']) != '')
                            {
                                if(trim(strtolower($arr_data[$d]['AK'])) == 'yes')
                                {
                                    $add_data['is_creditcard_fee_enable'] = 1;
                                    if(trim($arr_data[$d]['AL']) != '' && trim($arr_data[$d]['AM']) != '')
                                    {
                                        $add_data['creditcard_fee'] = trim($arr_data[$d]['AM']);
                                         $add_data['creditcard_fee_type'] = (trim(strtolower($arr_data[$d]['AL'])) == 'percentage')?'Percentage':'Amount';
                                    }
                                    else
                                    {
                                        if(trim($arr_data[$d]['AL']) == ''){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AL'].' is required.';
                                        }
                                        else
                                        {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AM'].' is required.';
                                        }
                                    }
                                }
                                else
                                {
                                    $add_data['is_creditcard_fee_enable'] = 0;
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AK'].' is required.';
                            }
                            //Credit Card Fee Applicable :: End

                            //order mode
                            $order_mode = array();
                            $order_mode_arr = array();
                            if(trim($arr_data[$d]['AN']) != ''){
                                $order_mode_arr = explode(',', trim($arr_data[$d]['AN']));
                                foreach ($order_mode_arr as $key => $value) {
                                    $order_mode[$key] = (strtolower(trim($value)) == 'pickup') ? 'PickUp' : ((strtolower(trim($value)) == 'delivery') ? 'Delivery' : '');
                                }
                                $order_mode = implode(',', $order_mode);
                                $add_data['order_mode'] = trim($order_mode);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AN'].' is required.';
                            }

                            //delivery charge 
                            $add_delivery_charge_data = array();
                            if(trim($arr_data[$d]['AO']) != '' && trim($arr_data[$d]['AP']) != '' && trim($arr_data[$d]['AQ']) != '' && trim($arr_data[$d]['AR']) != ''){
                                $add_delivery_charge_data = array( 
                                    'area_name'=>trim($arr_data[$d]['AO']),
                                    'lat_long'=>trim($arr_data[$d]['AP']),
                                    'price_charge'=>(trim($arr_data[$d]['AQ']))?trim($arr_data[$d]['AQ']):NULL,
                                    'additional_delivery_charge'=>(trim($arr_data[$d]['AR']))?trim($arr_data[$d]['AR']):NULL
                                );
                            }
                            else
                            {
                                // If Order Mode is delivery then validate Delivery charge
                                if(in_array('delivery', $order_mode_arr)){
                                    if(trim($arr_data[$d]['AO']) == ''){
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AO'].' is required.';
                                    }
                                    if(trim($arr_data[$d]['AP']) == ''){
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AP'].' is required.';
                                    }
                                    if(trim($arr_data[$d]['AQ']) == ''){
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AQ'].' is required.';
                                    }
                                    if(trim($arr_data[$d]['AR']) == ''){
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AR'].' is required.';
                                    }
                                }
                            }

                            //payment method
                            if(trim($arr_data[$d]['AS']) != ''){
                                $payment_mode_arr = explode(',', trim($arr_data[$d]['AS']));
                                $payment_method_id = array();
                                foreach ($payment_mode_arr as $key => $slug) {
                                    $payment_id = $this->common_model->get_payment_method_detail($slug);
                                    if(!empty($payment_id->payment_id)){
                                        $payment_method_id[] = $payment_id->payment_id;
                                    }                                    
                                }
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AS'].' is required.';
                            }

                            //type of restaurant                            
                            //allow_scheduled_delivery
                            if(trim($arr_data[$d]['AT']) != '') {
                                if(trim($arr_data[$d]['AT']) == 'yes') {
                                    $add_data['allow_scheduled_delivery'] = 1;
                                } else {
                                    $add_data['allow_scheduled_delivery'] = 0;
                                }
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AT'].' is required.';
                            }
                            //allowed_days_for_scheduling
                            if(trim($arr_data[$d]['AU']) != '') {
                                if(trim($arr_data[$d]['AT']) == 'yes') {
                                    $allowed_days_for_scheduling = (int)trim($arr_data[$d]['AU']);
                                    if($allowed_days_for_scheduling > 0 && $allowed_days_for_scheduling <= 10) {
                                        $add_data['allowed_days_for_scheduling'] = $allowed_days_for_scheduling;
                                    } else if($allowed_days_for_scheduling <= 0) {
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AU'].' '.$this->lang->line('greaterthan_zero');
                                    } else if($allowed_days_for_scheduling > 10) {
                                        $mandatoryColumnBlank = 0;
                                        $Import[$rowcount][] = $header[2]['AU'].' '.$this->lang->line('lessthanequalto_ten');
                                    }
                                } else {
                                    $add_data['allowed_days_for_scheduling'] = NULL;
                                }
                            } else {
                                if(trim($arr_data[$d]['AT']) == 'yes') {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['AU'].' is required.';
                                }
                            }
                            //restaurant rating
                            if(trim($arr_data[$d]['AV']) != ''){
                                $add_data['restaurant_rating'] = trim($arr_data[$d]['AV']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AV'].' is required.';
                            }
                            //restaurant rating count
                            if(trim($arr_data[$d]['AW']) != ''){
                                $add_data['restaurant_rating_count'] = trim($arr_data[$d]['AW']);
                            }
                            else
                            {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['AW'].' is required.';
                            }
                            //enable table booking
                            if(trim($arr_data[$d]['AX']) != ''){
                                if(trim(strtolower($arr_data[$d]['AX'])) == 'yes'){
                                    $add_data['enable_table_booking'] = 1; 
                                    $add_data['table_booking_capacity'] = NULL;
                                    $add_data['table_online_availability'] = NULL;
                                    $add_data['table_minimum_capacity'] = NULL; 
                                    $add_data['allowed_days_table'] = 0; 
                                    if(trim($arr_data[$d]['AY']) != '' && trim($arr_data[$d]['AZ']) != '' && trim($arr_data[$d]['BA']) != '' && trim($arr_data[$d]['BB']) != '' && trim($arr_data[$d]['BB'])!=0) {
                                        $resultant_val = (trim($arr_data[$d]['AY']) * trim($arr_data[$d]['AZ']))/100;

                                        if(trim($arr_data[$d]['AY']) > trim($arr_data[$d]['BB'])) {
                                            if(trim($arr_data[$d]['BA']) < $resultant_val){
                                                $add_data['table_booking_capacity'] = trim($arr_data[$d]['AY']);
                                                $add_data['table_online_availability'] = trim($arr_data[$d]['AZ']);
                                                $add_data['table_minimum_capacity'] = trim($arr_data[$d]['BA']);
                                                $add_data['allowed_days_table'] = trim($arr_data[$d]['BB']);
                                            } else {
                                                $mandatoryColumnBlank = 0;
                                                $Import[$rowcount][] = $header[2]['BA'].' must be less than '.$header[2]['AY'];
                                            }
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AY'].' must be greater than '.$header[2]['BA'];
                                        }
                                    } else {
                                        if(trim($arr_data[$d]['AY']) == '') {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AY'].' is required.';
                                        } else if(trim($arr_data[$d]['AZ']) == '') {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['AZ'].' is required.';
                                        } else if(trim($arr_data[$d]['BA']) == ''){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['BA'].' is required.';
                                        } else if(trim($arr_data[$d]['BB']) == ''){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['BB'].' is required.';
                                        } else if(trim($arr_data[$d]['BB']) == 0){
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = $header[2]['BB'].' must be greater than 0.';
                                        }
                                    }
                                } else {
                                    $add_data['enable_table_booking'] = 0;
                                    $add_data['table_booking_capacity'] = NULL; //AY
                                    $add_data['table_online_availability'] = NULL; //AZ
                                    $add_data['table_minimum_capacity'] = NULL; //BA
                                    $add_data['allowed_days_table'] = 0; //BB
                                }
                            }
                            else
                            {
                                // $mandatoryColumnBlank = 0;
                                // $Import[$rowcount][] = $header[2]['X'].' is required.';
                            }
                            // add data
                            if($mandatoryColumnBlank == 1)
                            {
                                //update data
                                if($is_update=='yes' && $restaurant_id>0)
                                {
                                    $ContentID = $restaurant->content_id;
                                    $this->restaurant_model->updateData($add_data,'restaurant','entity_id',$restaurant_id);
                                    $this->restaurant_model->updateData($addaddress_data,'restaurant_address','resto_entity_id',$restaurant_id);
                                    $res_language_arr[$d] = $arr_data[$d]['A'];
                                    $status = "Updated";
                                } else { //add data
                                    $oldcontent_id=0;
                                    $languagechk_arr = array_diff($languagechk_arr,array($arr_data[$d]['E']));
                                    $languagechk_arr = array_values($languagechk_arr);
                                    $rescontentid_chk=$this->restaurant_model->getRestaurantContentID(trim($arr_data[$d]['D']),$languagechk_arr[0]);
                                    if($rescontentid_chk && !empty($rescontentid_chk))
                                    {
                                        $oldcontent_id = $rescontentid_chk->content_id;
                                    }

                                    if(trim($arr_data[$d]['A']) != '')
                                    {
                                        if(!empty($res_language_arr))
                                        {
                                            if(in_array($arr_data[$d]['A'], $res_language_arr))
                                            {                                            
                                                $Dkey = '';
                                                foreach ($res_language_arr as $mkey => $mvalue){
                                                    if ($mvalue == $arr_data[$d]['A']) {
                                                        $Dkey = $mkey;
                                                    }
                                                }
                                                if ($Dkey != ''){
                                                    $ContentID = $content_id_arr[$Dkey];
                                                }
                                                else
                                                {
                                                    if($oldcontent_id>0)
                                                    {
                                                        $ContentID = $oldcontent_id;
                                                    }
                                                    else
                                                    {
                                                        $add_content = array(
                                                          'content_type'=>'restaurant',
                                                          'created_by'=>$this->session->userdata("AdminUserID"),
                                                          'created_date'=>date('Y-m-d H:i:s')
                                                        );
                                                        $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                                    }                                                    
                                                    $content_id_arr[$d] = $ContentID;
                                                }
                                            }
                                            else
                                            {
                                                if($oldcontent_id>0)
                                                {
                                                    $ContentID = $oldcontent_id;
                                                }
                                                else
                                                {
                                                    $add_content = array(
                                                        'content_type'=>'restaurant',
                                                        'created_by'=>$this->session->userdata("AdminUserID"),
                                                        'created_date'=>date('Y-m-d H:i:s')
                                                    );
                                                    $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                                }
                                                $content_id_arr[$d] = $ContentID;
                                                $res_language_arr[$d] = $arr_data[$d]['A'];
                                            }
                                        }
                                        else
                                        {
                                            if($oldcontent_id>0)
                                            {
                                                $ContentID = $oldcontent_id;
                                            }
                                            else
                                            {
                                                $add_content = array(
                                                    'content_type'=>'restaurant',
                                                    'created_by'=>$this->session->userdata("AdminUserID"),
                                                    'created_date'=>date('Y-m-d H:i:s')
                                                );
                                                $ContentID = $this->restaurant_model->addData('content_general',$add_content);
                                            }
                                            $content_id_arr[$d] = $ContentID;
                                            $res_language_arr[$d] = $arr_data[$d]['A'];
                                        }                                        
                                    }
                                    $add_data['content_id'] = $ContentID; 
                                    $add_data['status']= 1;
                                    $add_data['created_by'] = $this->session->userdata("AdminUserID");
                                    $restaurant_id = $this->restaurant_model->addData('restaurant',$add_data);

                                    $addaddress_data['content_id'] = $ContentID;
                                    $addaddress_data['resto_entity_id'] = $restaurant_id;
                                    $this->restaurant_model->addData('restaurant_address',$addaddress_data);
                                    $status = "New Added";
                                }
                                // Add Delivery charge data
                                if(!empty($add_delivery_charge_data)){
                                    // check for Delivery charge exists then update it 
                                    $chk_delivery_charge = $this->restaurant_model->chkDeliveryChargeName(trim(strtolower($arr_data[$d]['AO'])),$ContentID);
                                    $add_delivery_charge_data['restaurant_id'] = $ContentID;
                                    if(!empty($chk_delivery_charge->charge_id)){
                                        $add_delivery_charge_data['updated_date'] = date('Y-m-d H:i:s');
                                        $add_delivery_charge_data['updated_by'] = $this->session->userdata('AdminUserID');
                                        $this->restaurant_model->updateData($add_delivery_charge_data,'delivery_charge','charge_id',$chk_delivery_charge->charge_id);
                                    }
                                    else {
                                        $add_delivery_charge_data['created_date'] = date('Y-m-d H:i:s');
                                        $add_delivery_charge_data['created_by'] = $this->session->userdata('AdminUserID');
                                        $this->restaurant_model->addData('delivery_charge',$add_delivery_charge_data);   
                                    }
                                }
                                // Add payment method 
                                if(!empty($payment_method_id)){                                    
                                    $add_suggestion = array();
                                    foreach ($payment_method_id as $key => $value) {
                                        $add_suggestion[] = array(
                                            'restaurant_content_id'=>$ContentID,
                                            'payment_id'=>$value
                                        );
                                    }
                                
                                    $chk_payment_method_suggestion = $this->restaurant_model->check_payment_method_suggestion($ContentID);
                                    if(!empty($chk_payment_method_suggestion)){
                                        $this->restaurant_model->deleteInsertMethodSuggestion($ContentID,$add_suggestion);
                                    } else {
                                        $this->restaurant_model->deleteInsertMethodSuggestion('',$add_suggestion);
                                    }
                                }
                                $Import[$rowcount][] = $status;
                            }
                        } 
                        $import_data['arr_data'] = $arr_data;
                        $import_data['header'] = $header;
                        $import_data['Import'] = $Import;
                        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' imported restaurants');
                        $this->session->set_userdata('import_data', $import_data);
                        redirect(base_url().ADMIN_URL.'/restaurant/import_restaurant_status');
                    }
                }
            }
        }
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant',$this->session->userdata('language_slug'));
        //restaurant count
        $this->db->select('content_id');
        if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){     
            $this->db->where('restaurant.restaurant_owner_id',$this->session->userdata('AdminUserID'));
        }
        if($this->session->userdata('AdminUserType') == 'Branch Admin'){     
            $this->db->where('restaurant.branch_admin_id',$this->session->userdata('AdminUserID'));
        }
        $this->db->group_by('content_id');
        $data['res_count'] = $this->db->get('restaurant')->num_rows();
        $this->load->view(ADMIN_URL.'/restaurant',$data);
    }
    public function import_restaurant_status()
    {
        $data['meta_title'] = $this->lang->line('manage_res').' | '.$this->lang->line('site_title');
        $this->load->view(ADMIN_URL.'/import_restaurant_status',$data);
    }
    //restaurant import :: end
    public function checkResMenuNameExist(){
        $menu_name = ($this->input->post('name') != '')?trim($this->input->post('name')):'';
        $menu_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $category_id = ($this->input->post('category_id') != '')?$this->input->post('category_id'):'';
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';

        if($call_from == 'CI_callback'){
            if($menu_name!='' && $category_id!='' && $restaurant_id!=''){
                $check = $this->restaurant_model->checkResMenuNameExist($menu_name,$language_slug,$category_id,$restaurant_id,$menu_entity_id);
                if($check > 0){
                    $this->form_validation->set_message('checkResMenuNameExist', $this->lang->line('res_menu_exist'));
                    return false;
                } else {
                    return true;
                }
            }
        }else{
            if($menu_name!='' && $category_id!='' && $restaurant_id!=''){
                $check = $this->restaurant_model->checkResMenuNameExist($menu_name,$language_slug,$category_id,$restaurant_id,$menu_entity_id);
                echo $check;
            } 
        }       
    }

    public function checkResNameExist(){
        $res_or_branch = ($this->input->post('add_res_branch'))?trim($this->input->post('add_res_branch')):'';
        if($res_or_branch == '') {
            if($this->input->post('res_name') != '') {
                $res_or_branch = 'res';
            } else if($this->input->post('branch_name') != '') {
                $res_or_branch = 'branch';
            }
        }
        $res_name = ($res_or_branch=='res' && $this->input->post('res_name') != '')?trim($this->input->post('res_name')):(($res_or_branch=='branch' && $this->input->post('branch_name') != '')?trim($this->input->post('branch_name')):'');
        $res_entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        $call_from = ($this->input->post('call_from') != '')?$this->input->post('call_from'):'';

        if($call_from == 'CI_callback'){
            if($res_name!=''){
                $check = $this->restaurant_model->checkResNameExist($res_name,$language_slug,$res_entity_id);
                $err_msg = ($res_or_branch == 'res')?$this->lang->line('res_exist'):$this->lang->line('branch_exist');
                if($check > 0){
                    $this->form_validation->set_message('checkResNameExist', $err_msg);
                    return false;
                } else {
                    return true;
                }
            }
        }else{
            if($res_name!=''){
                $check = $this->restaurant_model->checkResNameExist($res_name,$language_slug,$res_entity_id);
                echo $check;
            } 
        }       
    }
    public function ajax_bulk_online_offline() {
        $bulk_action = $this->input->post('bulk_action');
        $bulk_ids = ($this->input->post('bulk_ids'))?$this->input->post('bulk_ids'):'';
        $off_time = ($this->input->post('off_time')) ? $this->input->post('off_time') : '0';

        if($bulk_action == 'offline') {
            //Time store base on UTC time zone :: Start
            date_default_timezone_set(default_timezone);
            $offlinetime = time();
            $timezone_name = default_timezone;
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name']) {
                if(!$_SESSION['timezone_name']) {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }
            $timezone_name = $_SESSION['timezone_name'];
            date_default_timezone_set($timezone_name);
            //Time store base on UTC time zone :: End

            if($off_time > 0) {
                $offlinetime = $offlinetime + $off_time * 60;
            }
            if($bulk_ids != ''){
                $this->restaurant_model->bulk_online_offline_all('restaurant',$bulk_ids,'offline',$offlinetime);

                $bulk_content_ids_arr = explode(',', $bulk_ids);
                if(count($bulk_content_ids_arr) == 1) {
                    $language_slug = $this->session->userdata('language_slug');
                    $res_name = $this->common_model->getResNametoDisplay('',$bulk_content_ids_arr[0],$language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made restaurant '.$res_name.' offline');
                } else {
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made multiple restaurants offline');
                }
            }
        } else if($bulk_action == 'online') {
            if($bulk_ids != ''){
                $this->restaurant_model->bulk_online_offline_all('restaurant',$bulk_ids,'online',0);

                $bulk_content_ids_arr = explode(',', $bulk_ids);
                if(count($bulk_content_ids_arr) == 1) {
                    $language_slug = $this->session->userdata('language_slug');
                    $res_name = $this->common_model->getResNametoDisplay('',$bulk_content_ids_arr[0],$language_slug);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made restaurant '.$res_name.' online');
                } else {
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' made multiple restaurants online');
                }
            }
        }
    }
    //Restauratn is normal/busy/very busy mode setting :: Start
    public function ajax_schedule_mode()
    {
        $restaurant_schedule_mode = ($this->input->post('restaurant_schedule_mode'))?$this->input->post('restaurant_schedule_mode'):'0';
        $content_id = ($this->input->post('shedule_content_id') != '')?$this->input->post('shedule_content_id'):'';
        if(intval($content_id)>0)
        {
            //Time sotre base on UTC time zone :: Start
            date_default_timezone_set(default_timezone);
            $offlinetime=time();

            $timezone_name = default_timezone;
            if($_SESSION['timezone_name'] || $_COOKIE['timezone_name'])
            {
                if(!$_SESSION['timezone_name'])
                {
                    $_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
                }
            }      
            $timezone_name = $_SESSION['timezone_name'];
            date_default_timezone_set($timezone_name);
            //Time sotre base on UTC time zone :: End

            $off_time = ($this->input->post('off_time'))?$this->input->post('off_time'):'0';            
            if($off_time>0)
            {
                $offlinetime=$offlinetime+$off_time*60;
            }            

            $update_arr = array('schedule_mode' => $restaurant_schedule_mode,'schedule_time' => $offlinetime,'enable_schedule' => 1);
            $this->restaurant_model->updateData($update_arr,'restaurant','content_id',$content_id);
            //Code for log :: Start
            $language_slug = $this->session->userdata('language_slug');
            $res_name = $this->common_model->getResNametoDisplay('',$content_id,$language_slug);
                $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' set estimate time per order for restaurant: '.$res_name);
            //Code for log :: End
        }                
    }
    //Restauratn is normal/busy/very busy mode setting :: End
}
?>