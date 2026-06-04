<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Myprofile extends CI_Controller {
  
	public function __construct() {
		parent::__construct();      
        if (!$this->session->userdata('is_user_login')) {
            redirect('home');
        }  
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');     
		$this->load->model('/myprofile_model');    
	}
	// my profile index page
	public function index()
	{	
		$data['page_title'] = $this->lang->line('my_profile').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = ""; 
		$this->session->set_userdata('tip_amount', 0);
		$this->session->unset_userdata('tip_percent_val');
		/* mobilPay payment integration : start */
		//method 1
		if(!empty($url) && isset($url['query'])){
			parse_str($url['query'], $params);
		}
		//parse_str($url['query'], $params);
		if(!empty($params['order_id'])) { 
			$credit_walletDetails = $this->common_model->getRecordMultipleWhere('wallet_history',array('order_id' => $params['order_id'],'user_id' => $this->session->userdata('UserID'), 'credit'=>1, 'is_deleted'=>0));
			$earned_points = ($credit_walletDetails->amount)?$credit_walletDetails->amount:0.00;
			if($params['pay_status'] == 'confirmed/captured') {
				$data['payment'] = array(
	                'status'=>'paid',
	                'message'=>$params['error_msg'],
	                'order_id'=>$params['order_id'],
	                'order_delivery'=>$params['order_delivery'],
	                'pay_status'=>$params['pay_status'],
	                'earned_points' => $earned_points,
	            );
			} elseif($params['pay_status'] == 'pending' && ($params['error_msg']=='Transaction approved' || $params['error_msg']=='Tranzactia aprobata')){
				$data['payment'] = array(
	                'status'=>'paid',
	                'message'=>$params['error_msg'],
	                'order_id'=>$params['order_id'],
	                'order_delivery'=>$params['order_delivery'],
	                'pay_status'=>$params['pay_status'],
	                'earned_points' => $earned_points,
	            );
			}else {
				$data['payment'] = array(
	                'status'=>'paid',
	                'message'=>$params['error_msg'],
	                'order_id'=>$params['order_id'],
	                'order_delivery'=>$params['order_delivery'],
	                'pay_status'=>$params['pay_status'],
	                'earned_points' => $earned_points,
	            );
			}
		}
		/* mobilPay payment integration : end */
		
		if($this->input->post('submit_profile') == "Save"){ 
			$this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required'); 
			$this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|callback_checkPhone'); 
	        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|min_length[6]|callback_checkEmail'); 
		    if(!empty($this->input->post('password'))){
		        $this->form_validation->set_rules('password', $this->lang->line('title_newpassword'), 'trim|min_length[6]');
	            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_pass'), 'trim|min_length[6]|matches[password]');       
	        }
	        if ($this->form_validation->run())
	        { 
	        	$updateUserData = array(                  
					'first_name' =>$this->input->post('first_name'),
					'last_name' =>$this->input->post('last_name'),                  
					'mobile_number' =>$this->input->post('phone_number'),                  
					'email' =>$this->input->post('email'),              
					'updated_by'=>$this->session->userdata("UserID"),
					'updated_date'=>date('Y-m-d H:i:s')
				);                 
				if (!empty($this->input->post('password')) && !empty($this->input->post('confirm_password'))) {
					$newEncryptPass  = md5(SALT.$this->input->post('password'));
					$updateUserData['password'] = $newEncryptPass;
				}   
                if (!empty($_FILES['image']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = './uploads/profile';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir('uploads/profile')) {
                      @mkdir('./uploads/profile', 0777, TRUE);
                    }
                    $this->upload->initialize($config);                  
                    if ($this->upload->do_upload('image'))
                    {
                      $img = $this->upload->data();

                      //Code for compress image :: Start
                      $fileName = basename($img['file_name']);                   
                      $imageUploadPath = './uploads/profile/'. $fileName; 
                      $imageTemp = $_FILES["image"]["tmp_name"];
                      $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                      //Code for compress image :: End
                      
                      $updateUserData['image'] = "profile/".$img['file_name'];   
                      if($this->input->post('uploaded_image')){
                        @unlink(FCPATH.'uploads/'.$this->input->post('uploaded_image'));
                      }  
                    }
                    else
                    {
                      $data['Error'] = $this->upload->display_errors();
                      $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                      //$this->session->set_flashdata('myProfileMSGerror', $data['Error']);
                      $_SESSION['myProfileMSGerror'] = $data['Error']; 
                    }
                }
                if(empty($data['Error'])){
					$affected_rows = $this->common_model->updateData('users',$updateUserData,'entity_id',$this->input->post('entity_id'));  
					if ($affected_rows) {
						/*
							Issue: When profile is updated without image session data displaying default image till user is logged in
							Solution: Set session data for user image if user has uploaded image in profile update.
							Updated On : 28/10/2020
						*/
						$userdata_array = [];
						$userdata_array["UserID"] = $this->input->post('entity_id');
						$userdata_array["userFirstname"] = $this->input->post('first_name');
						$userdata_array["userLastname"] = $this->input->post('last_name');
						$userdata_array["userEmail"] = $this->input->post('email');
						$userdata_array["userPhone"] = $this->input->post('phone_number');
						if (!empty($_FILES['image']['name']))
                		{
                			$userdata_array["userImage"] = $updateUserData['image'];
                		}
                		$this->session->set_userdata($userdata_array);
					}           
					//$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update')); 
					$_SESSION['myProfileMSG'] = $this->lang->line('success_update');
					echo 'success'; 
                }
	        }
	        else
	        {
	        	echo validation_errors();
	        }
	        exit;
		}
		$logged_in_user_type = $this->session->userdata('UserType');
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'',order_count,1,$logged_in_user_type);
		$data['in_process_orders_count'] = $this->myprofile_model->getOrderCount('process',$this->session->userdata('UserID'),'','',$logged_in_user_type); 
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				$data['in_process_orders'][$key]['ratings'] = $ratings;
			}
		}
		$data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'',order_count,1,$logged_in_user_type); 		   
    	$data['past_orders_count'] = $this->myprofile_model->getOrderCount('past',$this->session->userdata('UserID'),'','',$logged_in_user_type);
	    if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				$total_orders = $this->myprofile_model->getTotalOrders($this->session->userdata('UserID'),$value['restaurant_id']);
				$total_reviews = $this->myprofile_model->getTotalReviews($this->session->userdata('UserID'),$value['res_content_id']);
				$data['past_orders'][$key]['remaining_reviews'] = $total_orders - $total_reviews;
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;
        if($this->session->userdata('UserType') != 'Agent' || $this->session->userdata('UserType') == '') {
			$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
			$data['wallet_history'] = $this->myprofile_model->getWalletHistory($this->session->userdata('UserID'));
			$data['savecard_detail'] = $this->myprofile_model->getsavecard_detail($this->session->userdata('UserID'));
			// bookings tab data
			$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
			$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
			if (!empty($data['upcoming_events'])) {
				foreach ($data['upcoming_events'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['upcoming_events'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['upcoming_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['upcoming_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			}
			if (!empty($data['past_events'])) {
				foreach ($data['past_events'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['past_events'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['past_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['past_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			}
			// table bookings tab data
			$data['upcoming_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'upcoming');  
			$data['past_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'past'); 

			if (!empty($data['upcoming_tables'])) {
				foreach ($data['upcoming_tables'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['upcoming_tables'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['upcoming_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['upcoming_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			}
			if (!empty($data['past_tables'])) {
				foreach ($data['past_tables'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['past_tables'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['past_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['past_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			}
			// my addressess tab data
			$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		}
		// my notifications tab data
		$data['users_notifications'] = $this->myprofile_model->getNotifications($this->session->userdata('UserID'));
		// cancel order timer 
		$data['cancel_order_timer'] = $this->db->get_where('system_option',array('OptionSlug'=>'cancel_order_timer'))->first_row();	
		//get bookmarked res
		$data['users_bookmarks'] = $this->myprofile_model->getBookmarks($this->session->userdata('UserID'));
		if (!empty($data['users_bookmarks'])){
			foreach ($data['users_bookmarks'] as $key => $value){
				$ratings = $this->common_model->getRestaurantReview($value['content_id']);
				$data['users_bookmarks'][$key]['ratings'] = $ratings;
			}
		}	
		$this->load->view('myprofile',$data);
	}
	// get order details
	public function getOrderDetails()
	{
		$data['page_title'] = $this->lang->line('order_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$order_details = array();
		if (!empty($this->input->post('order_id')))
		{
			$data['order_details'] = $this->myprofile_model->getOrderDetail('','',$this->input->post('order_id'));
			$data['coupon_array'] = $this->common_model->getCoupon_array($this->input->post('order_id'));			
			if (!empty($data['order_details'])) {
				foreach ($data['order_details'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['order_details'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['order_details'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['order_details'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			} 
		}	
		$this->load->view('ajax_order_details',$data);
	}
	// getAllOrders ajax call
	public function getOrderHistory(){
		$data['page_title'] = $this->lang->line('order_history').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;

        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		} 
		$this->load->view('ajax_order_history',$data);
	}
	// get booking details
	public function getBookingDetails(){
		$data['page_title'] = $this->lang->line('booking_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		//get System Option Data
       /* $this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$booking_details = array();
		if (!empty($this->input->post('event_id'))) {
			$data['booking_details'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'',$this->input->post('event_id'));
			if (!empty($data['booking_details'])) {
				foreach ($data['booking_details'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['booking_details'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['booking_details'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['booking_details'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			} 
		}		
		$this->load->view('ajax_booking_details',$data);
	}
	// edit address
	public function getEditAddress(){
		if (!empty($this->input->post('address_id'))) {
			$data = $this->myprofile_model->getAddress($this->session->userdata('UserID'),$this->input->post('address_id'));  
		}
		echo json_encode($data[0]);
	}
	// view users bookings
	public function view_my_bookings()
    { 
		$data['page_title'] = $this->lang->line('my_bookings').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = "bookings";   
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['wallet_history'] = $this->myprofile_model->getWalletHistory($this->session->userdata('UserID'));
		$data['savecard_detail'] = $this->myprofile_model->getsavecard_detail($this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;

        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		// table bookings tab data
		$data['upcoming_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_tables'])) {
			foreach ($data['upcoming_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_tables'])) {
			foreach ($data['past_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		//get bookmark res
		$data['users_bookmarks'] = $this->myprofile_model->getBookmarks($this->session->userdata('UserID'));
		if (!empty($data['users_bookmarks'])){
			foreach ($data['users_bookmarks'] as $key => $value){
				$ratings = $this->common_model->getRestaurantReview($value['content_id']);
				$data['users_bookmarks'][$key]['ratings'] = $ratings;
			}
		}
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		// my notifications tab data
		$data['users_notifications'] = $this->myprofile_model->getNotifications($this->session->userdata('UserID'));
		$this->load->view('myprofile',$data);
    }
    // view users addresses
	public function view_my_addresses()
    { 
		$data['page_title'] = $this->lang->line('my_addresses').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = "addresses";  
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['wallet_history'] = $this->myprofile_model->getWalletHistory($this->session->userdata('UserID'));
		$data['savecard_detail'] = $this->myprofile_model->getsavecard_detail($this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;

        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		// table bookings tab data
		$data['upcoming_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_tables'])) {
			foreach ($data['upcoming_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_tables'])) {
			foreach ($data['past_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		//get bookmark res
		$data['users_bookmarks'] = $this->myprofile_model->getBookmarks($this->session->userdata('UserID'));
		if (!empty($data['users_bookmarks'])){
			foreach ($data['users_bookmarks'] as $key => $value){
				$ratings = $this->common_model->getRestaurantReview($value['content_id']);
				$data['users_bookmarks'][$key]['ratings'] = $ratings;
			}
		}
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		// my notifications tab data
		$data['users_notifications'] = $this->myprofile_model->getNotifications($this->session->userdata('UserID'));
		// cancel order timer 
		$data['cancel_order_timer'] = $this->db->get_where('system_option',array('OptionSlug'=>'cancel_order_timer'))->first_row();
		$this->load->view('myprofile',$data);
    }
	// add user's address
	public function addAddress(){ 
		$data['page_title'] = $this->lang->line('add_address').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		if($this->input->post('submit_address') != ""){ 
			$this->form_validation->set_rules('address_field', $this->lang->line('address'), 'trim|required');
			$this->form_validation->set_rules('latitude', $this->lang->line('latitude'), 'trim|required'); 
	        $this->form_validation->set_rules('longitude', $this->lang->line('longitude'), 'trim|required');
			$this->form_validation->set_rules('zipcode', $this->lang->line('postal_code'), 'trim|required'); 
			$this->form_validation->set_rules('city', $this->lang->line('city'), 'trim|required');
			// $this->form_validation->set_rules('state', $this->lang->line('state'), 'trim|required');
			// $this->form_validation->set_rules('country', $this->lang->line('country'), 'trim|required'); 
            $this->form_validation->set_rules('user_entity_id', $this->lang->line('user'), 'trim|required');       
	        if ($this->form_validation->run())
	        { 
	        	$add_data = array(
	                'address'=>$this->input->post('address_field'),
	                'search_area'=>$this->input->post('add_address_area'),
	                'landmark'=>$this->input->post('landmark'),
	                'latitude'=>$this->input->post('latitude'),
	                'longitude'=>$this->input->post('longitude'),
	                'zipcode'=>$this->input->post('zipcode'),
	                'city'=>$this->input->post('city'),
	                'address_label'=>$this->input->post('address_label'),
	                'state'=>$this->input->post('state'),
	                'country'=>$this->input->post('country'),
	                'user_entity_id'=>$this->input->post('user_entity_id')
	            );
				if (!empty($this->input->post('add_entity_id'))) 
				{
					$this->common_model->updateData('user_address',$add_data,'entity_id',$this->input->post('add_entity_id')); // edit address
					//$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update'));
					$_SESSION['myProfileMSG'] = $this->lang->line('success_update'); 
				}
				else 
				{
					$address_id = $this->common_model->addData('user_address',$add_data); // add address
					//$this->session->set_flashdata('myProfileMSG', $this->lang->line('success_add'));
					$_SESSION['myProfileMSG'] = $this->lang->line('success_add'); 
				}
				echo 'success';
				exit;
	        }
	        else
	        {
	        	echo validation_errors();
				exit;
	        }
		}
	}
	// delete Address
	public function ajaxDeleteAddress(){
		if (!empty($this->input->post('address_id'))) {
			//check if address is default, if yes then set another recently added address as default.
            $this->myprofile_model->check_default_address($this->session->userdata('UserID'),$this->input->post('address_id'));
			$this->common_model->deleteData('user_address','entity_id',$this->input->post('address_id'));
		}
	}
	// set main address
	public function ajaxSetAddress(){
		if (!empty($this->input->post('address_id')) && !empty($this->session->userdata('UserID'))) {
			$updateData = array(
				'is_main' => 0 
			);
			$this->common_model->updateData('user_address',$updateData,'user_entity_id',$this->session->userdata('UserID'));
			$updateMainData = array(
				'is_main' => 1
			);
			$this->common_model->updateData('user_address',$updateMainData,'entity_id',$this->input->post('address_id'));
		}
	}
	// Server side validation check email exist
	public function checkPhone($str){
		$UserID = $this->input->post('entity_id');      
		$checkPhone = $this->myprofile_model->checkPhone($str,$this->input->post('entity_id')); 
		if($checkPhone>0){
			$this->form_validation->set_message('checkPhone',$this->lang->line('number_already_registered'));
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	// Server side validation check email exist
	public function checkEmail($str){
		$UserID = $this->input->post('entity_id');      
		$checkEmail = $this->myprofile_model->checkEmail($str,$this->input->post('entity_id'));       
		if($checkEmail>0){
			$this->form_validation->set_message('checkEmail','User have already registered with this email!');
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	// delete Address
	public function ajaxDeleteAccount(){
		if (!empty($this->input->post('user_id'))) {
			$is_deleted = $this->common_model->deleteAccount($this->input->post('user_id'));
		}
	}

	//re-order changes :: start
	public function getReOrderDetails(){
		$data['page_title'] = $this->lang->line('order_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$order_details = array();
		if (!empty($this->input->post('order_id'))) {
			$data['order_details'] = $this->myprofile_model->getOrderDetail('','',$this->input->post('order_id'));
			if (!empty($data['order_details'])) {
				foreach ($data['order_details'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['order_details'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['order_details'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['order_details'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			} 
		}
		$data1 = $data['order_details'][0]['items'];
		if(sizeof($data1)>1) {
			$data['is_multiple']=1;
		} else {
			$data['is_multiple']=0;
		}
		$this->load->view('ajax_reorder_details',$data);
	}
	//re-order changes :: end
	//cancel order changes :: start
	public function getCancelOrderReasons(){
		$data['page_title'] = $this->lang->line('cancel_order').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['is_cancel_order'] = "yes";
		$order_status = 0;
		$order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):0;				
		if($order_id && $order_id>0)
		{
			$order_status = $this->myprofile_model->getOrderstatusLast($order_id);
		}
		if($order_status=="placed") {
			$order_details = array();
			if (!empty($this->input->post('order_id'))) {
				$data['cancel_order_reasons'] = $this->myprofile_model->getCancelOrderReasons($this->input->post('order_id'));
				$data['order_id'] = $this->input->post('order_id');
			}
		} else {
			$data['is_cancel_order'] = "no";
		}
		
		//$this->load->view('ajax_cancel_reason',$data);
		$ajax_cancel_reason = $this->load->view('ajax_cancel_reason',$data,true);
		$array_view = array(
			'ajax_cancel_reason'=>$ajax_cancel_reason,
			'is_cancel_order'=>$data['is_cancel_order'],
			'cancel_msg'=>($data['is_cancel_order'] == 'yes')?'':(($order_status=="rejected")?$this->lang->line('order_already_rejected'):$this->lang->line('order_already_accepted')),
			'oktxt'=>($data['is_cancel_order'] == 'yes')?'':$this->lang->line('oktxt'),
		);
		echo json_encode($array_view); exit;
	}
	public function OrderCancel(){
		$current_order_status = 0;
		$is_cancel_order = 'yes';
		$order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):0;	
		$response = array("error"=>'');			
		if($order_id && $order_id>0)
		{
			$current_order_status = $this->myprofile_model->getOrderstatusLast($order_id);
		}
		if($current_order_status=="placed")
		{
			$payment_methodarr = array('stripe','paypal','applepay');
			$data['order_records'] = $this->common_model->getOrderTransactionIds($order_id);
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

	                	if(strtolower($data['order_records']->payment_option)=='stripe' || strtolower($data['order_records']->payment_option)=='applepay' || $tip_payment_option=='stripe')
                        {
                            $response = $this->common_model->StripeRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                        }
                        else if(strtolower($data['order_records']->payment_option)=='paypal' || $tip_payment_option=='paypal')
                        {   
                            $response = $this->common_model->PaypalRefund($transaction_id,$order_id,$tips_transaction_id,'',$tip_payment_option,'','full',0);
                        }
                        //Mail send code Start
						if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus']))
						{
						    $language_slug = $this->session->userdata('language_slug');
						    $updated_bytxt = $this->session->userdata("userFirstname").' '.$this->session->userdata("userLastname");
						    $this->common_model->refundMailsend($order_id,$data['order_records']->user_id,0,'full',$updated_bytxt,$language_slug);
						}
						//Mail send code End
                        	                    
						//send refund noti to user
						if($data['order_records']->user_id && $data['order_records']->user_id > 0){
							if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
								$this->common_model->sendRefundNoti($order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,$transaction_id,$tips_transaction_id,$response['paymentIntentstatus'],$response['tips_paymentIntentstatus'],$response['error']);
							}
						}
	            }
	        }
        	$order_status = 'cancel';
			$order_id = $this->input->post('order_id');
			$user_id = $this->input->post('user_id');
			$reason = $this->input->post('reason');
			$this->db->set('order_status',$order_status)->where('entity_id',$order_id)->update('order_master');
			$this->db->set('cancel_reason',$reason)->where('entity_id',$order_id)->update('order_master');
			$users_wallet = $this->myprofile_model->getUsersWalletMoney($user_id);
	        $current_wallet = $users_wallet->wallet; //money in wallet
	        $credit_walletDetails = $this->myprofile_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'credit'=>1, 'is_deleted'=>0));
	        $credit_amount = $credit_walletDetails->amount;
	        $debit_walletDetails = $this->myprofile_model->getRecordMultipleWhere('wallet_history',array('order_id' => $order_id,'user_id' => $user_id, 'debit'=>1, 'is_deleted'=>0));
	        $debit_amount = $debit_walletDetails->amount;
	        $new_wallet_amount = ($current_wallet + $debit_amount) - $credit_amount;
	        $new_wallet_amount = ($new_wallet_amount<0)?0:$new_wallet_amount;
	        //delete order_id from wallet history and update users wallet
	        if(!empty($credit_amount) || !empty($debit_amount)){
	            $this->myprofile_model->deletewallethistory($order_id); // delete by order id
	            $new_wallet = array(
	                'wallet'=>$new_wallet_amount
	            );
	        	$this->myprofile_model->updateMultipleWhere('users', array('entity_id'=>$user_id), $new_wallet);
	        }
	        //Code add for notification to admin/rest admin :: Start
	        $device = $this->common_model->getDevice($user_id);
	        $langslugval = ($device->language_slug) ? $device->language_slug : '';
            $useridval = ($user_id && $user_id > 0) ? $user_id : 0;
            $this->common_model->sendSMSandEmailToUserOnCancelOrder($langslugval,$useridval,$order_id,'User');
            //Code add for notification to admin/rest admin :: End

	        $status_created_by ='Customer';
	        $addData = array(
	                'order_id'=>$order_id,
	                'user_id'=> $this->session->userdata('UserID'),
	                'order_status'=>$order_status,
	                'time'=>date('Y-m-d H:i:s'),
	                'status_created_by'=>$status_created_by
	            );
	        $order_id = $this->myprofile_model->addData('order_status',$addData);
	        $order_status = 'order_canceled';
	        $this->db->set('is_updateorder','0')->where('entity_id',$order_id)->update('order_detail');
	    } else {
	    	$is_cancel_order = 'no';
	    }
	    $array_view = array(
			'is_cancel_order'=>$is_cancel_order,
			'cancel_msg'=>($is_cancel_order == 'yes')?'':(($current_order_status=="rejected")?$this->lang->line('order_already_rejected'):$this->lang->line('order_already_accepted')),
			'oktxt'=>($is_cancel_order == 'yes')?'':$this->lang->line('oktxt'),
		);
		$response['error_message']='';
		if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
            $response['error_message'] = $this->lang->line('refund_failed');
        }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
            $response['error_message'] = $this->lang->line('refund_canceled');
        }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
            $response['error_message'] = $this->lang->line('refund_pending');
        }
		echo json_encode(array_merge($array_view,$response)); exit;
	}
	//cancel order changes :: end
	// get table booking details
	public function getTableBookingDetails(){
		$data['page_title'] = $this->lang->line('booking_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$booking_details = array();
		if (!empty($this->input->post('table_id'))) {
			$data['booking_details'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'',$this->input->post('table_id'));
			if (!empty($data['booking_details'])) {
				foreach ($data['booking_details'] as $key => $value) {
					$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
					$data['booking_details'][$key]['ratings'] = $ratings;
					$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
			        $data['booking_details'][$key]['restaurant_reviews'] = $review_data['reviews'];
			        $data['booking_details'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
				}
			} 
		}	
		$this->load->view('ajax_table_booking_details',$data);
	}
	// view users table bookings
	public function view_my_tablebookings()
    { 
		$data['page_title'] = $this->lang->line('table_bookings').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = "table_bookings";   
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['wallet_history'] = $this->myprofile_model->getWalletHistory($this->session->userdata('UserID'));
		$data['savecard_detail'] = $this->myprofile_model->getsavecard_detail($this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;

        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		// table bookings tab data
		$data['upcoming_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_tables'])) {
			foreach ($data['upcoming_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_tables'])) {
			foreach ($data['past_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		//get bookmark res
		$data['users_bookmarks'] = $this->myprofile_model->getBookmarks($this->session->userdata('UserID'));
		if (!empty($data['users_bookmarks'])){
			foreach ($data['users_bookmarks'] as $key => $value){
				$ratings = $this->common_model->getRestaurantReview($value['content_id']);
				$data['users_bookmarks'][$key]['ratings'] = $ratings;
			}
		}
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		// my notifications tab data
		$data['users_notifications'] = $this->myprofile_model->getNotifications($this->session->userdata('UserID'));
		$this->load->view('myprofile',$data);
    }
    //New code for order pagination :: Start
	public function getOrderPagination() 
	{		
		$logged_in_user_type = $this->session->userdata('UserType');
		$order_flag = $this->input->post('order_flag');
		$page_no = $this->input->post('page_no');
		$data_orders = $this->myprofile_model->getOrderDetail($order_flag,$this->session->userdata('UserID'),'',order_count,$page_no,$logged_in_user_type);    
	    $next_page_count = $this->myprofile_model->getOrderCount($order_flag,$this->session->userdata('UserID'),order_count,$page_no+1,$logged_in_user_type);
	    $reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
	    $html = '';
	    if(!empty($data_orders))
	    {
	    	$html = '<div class="row orders-box-row display-flex">';
			foreach ($data_orders as $key => $value)
			{
				$order_drivertip = 0;
				$subtotal = 0;
				$delivery_charges = 0;
				$total = 0;
				$coupon_amount = 0;
				if (!empty($value['price'])) {
					foreach($value['price'] as $pkey => $pvalue){
						if(isset($pvalue['label_key']) && $pvalue['label_key'] == "Sub Total") {
							$subtotal = $pvalue['value'];
						}
						if(isset($pvalue['label_key']) && $pvalue['label_key'] == "Delivery Charge"){
							$delivery_charges = $pvalue['value'];
						}
						if(isset($pvalue['label_key']) && $pvalue['label_key'] == "Coupon Amount"){
							$coupon_amount = $pvalue['value'];
						}
						if(isset($pvalue['label_key']) && $pvalue['label_key'] == "Total"){
							$total = $pvalue['value'];
						}
						if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Driver Tip") {
							$order_drivertip = (float)$pvalue['value'];
						}
					}
				}
				$this->db->select('OptionValue');
				$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
				$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $restaurant_reviews = $review_data['reviews'];
		        $restaurant_reviews_count = $review_data['review_count'];
		        $rating_txt = ($restaurant_reviews_count > 1)?$this->lang->line('ratings'):$this->lang->line('rating');
				$image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image']) && $value['restaurant_image']!='') ?  image_url. $value['restaurant_image'] : default_icon_img;

				$staus_display = $this->lang->line($value['payment_status']);
				if($value['payment_status']=='paid' || $value['payment_status']== NULL)
				{
					$staus_display = ($value['order_status'] == "complete") ? $this->lang->line('completed') : (($value['order_status'] == "cancel") ? $this->lang->line('cancelled') : $this->lang->line($value['order_status']));
				}

				$html .= '
				<div class="col-xl-6 col-lg-12">
		            <div class="ordering-box-main">
		                <div class="ordering-box-top">
		                    <div class="ordering-box-img">
		                        <div class="ordering-img">
		                            <img src="'.$image.'">';
                $rattingval = '';
                if ($show_restaurant_reviews) { 
                 $rattingval = ($ratings > 0)?'<strong class="ratingtxt">'.$ratings.' ('.$restaurant_reviews_count.' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>';
                }
                if($this->session->userdata('UserType') == 'Agent') { 
                    $user_name = '<strong>'.$this->lang->line('customer') ." : <span> " . $value['user_name'].'</span></strong>';
                }else{
                	$user_name = "";
                }
				$html .= $rattingval;
				$html .= '</div>
	                    </div>
	                    <div class="ordering-box-text">
	                        <h6>'.$value['restaurant_name'].'</h6>';
	            $html .= $user_name;            
	            $html .= '<p>#'.$this->lang->line('orderid').' - '.$value['order_id'].'</p>
	                        <strong>'.$this->lang->line('price').' : <span>'.currency_symboldisplay($total,$value['currency_symbol']).'</span></strong>';

				if($value['refund_status'] != '' && $value['refund_status'] != null) {
					$html .= '<p class="mt-1">'.$this->lang->line('refund_status').' - '.ucfirst($this->lang->line(str_replace(" ", "_", $value['refund_status']))).'</p>';
				}
	            if($value['scheduled_date'] && $value['slot_open_time'] && $value['slot_close_time']) {
	            	$sched_date_inp = $value['scheduled_date'];
	            	$sched_slotopentime_inp = $value['slot_open_time'];
	            	$sched_slotclosetime_inp = $value['slot_close_time'];

		            $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$sched_date_inp $sched_slotopentime_inp"));
		            $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$sched_date_inp $sched_slotclosetime_inp"));
		            $order_scheduled_date = date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime)));
		            $order_slot_open_time = date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime)));
		            $order_slot_close_time = date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime)));

	                $html .= '<p><i class="iicon-icon-18" style="color:#17161a;"></i>&nbsp;&nbsp;'.$this->lang->line('order_scheduled_for').$this->common_model->dateFormat($order_scheduled_date).' ('.$this->common_model->timeFormat($order_slot_open_time).' - '.$this->common_model->timeFormat($order_slot_close_time).' )</p>';
	            }
	            $html .= '</div>
		                </div>
		                <div class="ordering-box-bottom">
		                    <span class="date-icon">'.$this->common_model->datetimeFormat($value['order_date']).'</span>
		                    <span class="relivered-icon">'.$staus_display.'</span>
		                    <div class="ordering-btn">
		                        <button class="btn" data-toggle="modal" onclick="order_details('.$value['order_id'].')">'.$this->lang->line('view_details').'</button>';
		        if($order_flag=='past' && $order_drivertip == 0 && $value['delivery_flag'] == "delivery" && (strtolower($value['order_status']) == "delivered" || strtolower($value['order_status'])=='complete') && $value['refund_status'] != "refunded") { 
                    $html .='<button class="btn" data-toggle="modal" onclick="tip_driver('.$value['order_id'].')">'.$this->lang->line('tip_driver').'</button>';
                }
                if ($order_flag == 'process') { //&& $value['delivery_flag'] == "delivery" 
                    $html .='<a href="'.base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])).'" class="btn">'.$this->lang->line('track_order').'</a>';
                }
		        if($order_flag=='past' && $this->session->userdata('UserType') != 'Agent'){
		        	$html .='<button class="btn" data-toggle="modal" onclick="reorder_details('.$value['order_id'].')">'.$this->lang->line('reorder').'</button>';
                    	if (!empty($this->session->userdata('UserID')) && !in_array($value['order_id'], $arrReviewOrderId) && (strtolower($value['order_status'])=='delivered' || strtolower($value['order_status'])=='complete')) { 
                                if($show_restaurant_reviews){
                    $html .='<button class="btn" onclick="addReview('.$value['restaurant_id'].','.$value['res_content_id'].','.$value['order_id'].')">'.$this->lang->line('title_admin_reviewadd').'</button>';
                    } }
		        }
		        $html .='</div>
		                </div>
		            </div>
		        </div>';
			}
			$html .= '</div>';
		}
		$resp_arr = array('order_html'=> $html,'next_page_count'=>$next_page_count);
		echo json_encode($resp_arr);
	}
	//New code for order pagination :: End
	public function ajaxNotification()
	{
		$html = '';
		$noti_id = ($this->input->post('noti_id'))?$this->input->post('noti_id'):0;
		$noti_title = '';
		if($noti_id && $noti_id>0)
		{
			$notifications = $this->myprofile_model->getNotificationsdtl($noti_id);
			$description = $notifications->notification_description;
			$noti_title = $notifications->notification_title;
			$html = str_replace("\n", '<br>', $description);				
		}	
		$html = str_replace("\n", '<br>', $html);			
		echo $html;
	}
   //Code for find the current order stauts :: Start
  	public function getlatestOrderstaus()
	{
		$order_status = 0;
		$order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):0;				
		if($order_id && $order_id>0)
		{
			$order_status = $this->myprofile_model->getOrderstatusLast($order_id);
		}				
		echo $order_status; exit;
  	}
  	//Code for find the current order stauts :: End
  	//Code for delete the stripe card :: Start
    public function removeStripeCard() {
    	$PaymentMethodid = ($this->input->post('PaymentMethodid'))?$this->input->post('PaymentMethodid'):'';
    	$stripecus_id = ($this->input->post('stripecus_id'))?$this->input->post('stripecus_id'):'';
    	$stripe_html = '';
    	if($PaymentMethodid != '' && $stripecus_id != '') {
    		$stripe_info = stripe_details();
	    	// Include the Stripe PHP bindings library 
	        require APPPATH .'third_party/stripe-php/init.php';
	        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
	        $stripe = new \Stripe\StripeClient($stripe_api_key);

    		try{
                $detach_card = $stripe->paymentMethods->detach(
                $PaymentMethodid,
                  []
                );
                if(!empty($detach_card) && $detach_card->id != ''){
                    //if default card deleted, then make recently added card as default.
                    try {
                        //get default payment method
                        $customer_obj = $stripe->customers->retrieve(
                            $stripecus_id,
                            []
                        );
                        $default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
                        if($PaymentMethodid == $default_payment_method || !$default_payment_method) {
                            try {
                                //list all cards
                                $all_card_details = $stripe->paymentMethods->all([
                                    'customer' => $stripecus_id,
                                    'type' => 'card',
                                ]);
                                if(!empty($all_card_details)) {
                                    //set recent card as default
                                    $this->common_model->set_default_card($stripe, $all_card_details->data[0]->id, $stripecus_id);
                                } else {
                                    //something went wrong
                                }
                            } catch (Exception $e) { 
                                // list all cards errors
                            }
                        }
                    } catch (Exception $e) {
                        //error while retrieving customer
                    }
                }
                $_SESSION['myProfileMSG'] = $this->lang->line('success_delete');                 
                http_response_code(200);
            	echo json_encode($detach_card); exit;   
            }
            catch(Stripe_CardError $e) {
			  $st_errormsg = $e->getMessage();
			} catch (Stripe_InvalidRequestError $e) {
			  // Invalid parameters were supplied to Stripe's API
			  $st_errormsg = $e->getMessage();
			} catch (Stripe_AuthenticationError $e) {
			  // Authentication with Stripe's API failed
			  $st_errormsg = $e->getMessage();
			} catch (Stripe_ApiConnectionError $e) {
			  // Network communication with Stripe failed
			  $st_errormsg = $e->getMessage();
			} catch (Stripe_Error $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
			  $st_errormsg = $e->getMessage();
			} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
			  $st_errormsg = $e->getMessage();
			}				
			if($st_errormsg!='')
			{
				$messagearr = ['error'=>'error','message'=>$st_errormsg];
				$_SESSION['delete_cardmessage'] = $data['Error'];         		
			}
    	}
    }
    //Code for delete the stripe card :: End
  	//Code for add/edit stripe card :: Start
	public function save_stripecard() {
		$is_editcard = ($this->input->post('is_editcard')) ? $this->input->post('is_editcard') : 'no';
		$set_as_default_stripecard = ($this->input->post('set_as_default_stripecard')) ? $this->input->post('set_as_default_stripecard') : 'no';
		$stripe_info = stripe_details();
    	// Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key : $stripe_info->test_secret_key;
        $stripe = new \Stripe\StripeClient($stripe_api_key);
        
        $stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
		$stripecus_id = '';
		if($stripecus_arr && !empty($stripecus_arr)) {
			$stripecus_id = $stripecus_arr->stripe_customer_id;
		}
		//Code for edit stripe card :: Start
		if($is_editcard == 'yes') {
			$payment_method_id = ($this->input->post('payment_method_id')) ? $this->input->post('payment_method_id') : '';
			$card_month = ($this->input->post('card_month'))?$this->input->post('card_month'):'';
			$card_year = ($this->input->post('card_year'))?$this->input->post('card_year'):'';
			$card_cvv = ($this->input->post('card_cvv'))?$this->input->post('card_cvv'):'';
			$card_zip = ($this->input->post('card_zip'))?$this->input->post('card_zip'):'';

			if($payment_method_id != '') {
				$update_card_arr = array();
                if($card_month) {
                    $update_card_arr['card']['exp_month'] = $card_month;
                }
                if($card_year){
                    $update_card_arr['card']['exp_year'] = $card_year;
                }               
                if($card_zip){
                    $update_card_arr['billing_details']['address']['postal_code'] = $card_zip;
                }
				if(!empty($update_card_arr)) {
                	try {
	                    $update_card = $stripe->paymentMethods->update(
	                      $payment_method_id,$update_card_arr
	                    );
	                    if(!empty($update_card) && $update_card->id != ''){
                            //set card as default
                            if($set_as_default_stripecard == 'yes') {
                                $this->common_model->set_default_card($stripe, $payment_method_id, $stripecus_id);
                            }
                        }
	                    $_SESSION['myProfileMSG'] = $this->lang->line('success_update');                    
	                    http_response_code(200);
			            echo json_encode('success'); exit;
			        }
			        catch(Stripe_CardError $e) {
					  $st_errormsg = $e->getMessage();
					} catch (Stripe_InvalidRequestError $e) {
					  // Invalid parameters were supplied to Stripe's API
					  $st_errormsg = $e->getMessage();
					} catch (Stripe_AuthenticationError $e) {
					  // Authentication with Stripe's API failed
					  $st_errormsg = $e->getMessage();
					} catch (Stripe_ApiConnectionError $e) {
					  // Network communication with Stripe failed
					  $st_errormsg = $e->getMessage();
					} catch (Stripe_Error $e) {
					  // Display a very generic error to the user, and maybe send
					  // yourself an email
					  $st_errormsg = $e->getMessage();
					} catch (Exception $e) {
					  // Something else happened, completely unrelated to Stripe
					  $st_errormsg = $e->getMessage();
					}				
					if($st_errormsg!='')
					{
						$messagearr = ['error'=>'error','message'=>$st_errormsg];
		        		echo json_encode($messagearr); exit;
					}
                }
			}
		} else { //Code for edit stripe card :: End
	        if($stripecus_id == '') {
	        	$stirpe_username = ($this->session->userdata('userFirstname') != '' && $this->session->userdata('userLastname') != '')?$this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'):$this->session->userdata('userFirstname');
				$user_phn = ($this->session->userdata('userPhone_code') != '' && $this->session->userdata('userPhone') != '')?$this->session->userdata('userPhone_code').$this->session->userdata('userPhone'):$this->session->userdata('userPhone');
				$stirpe_username = ''; $user_phn = '';
				try {
					//create customer :: start
					$CreateCustomer = $stripe->customers->create([
						'name' => $stirpe_username,
						'email' => ($this->session->userdata('userEmail'))?$this->session->userdata('userEmail'):'',
						'phone' => $user_phn
					]);
					//Code for store the sustomer id in database

					if(!empty($CreateCustomer) && $CreateCustomer->id){
						$stripecus_id = $CreateCustomer->id;                    
						$this->common_model->updateData('users',array('stripe_customer_id'=>$stripecus_id),'entity_id',$this->session->userdata('UserID'));
					}
				}
				catch(Stripe_CardError $e) {
				  $st_errormsg = $e->getMessage();
				} catch (Stripe_InvalidRequestError $e) {
				  // Invalid parameters were supplied to Stripe's API
				  $st_errormsg = $e->getMessage();
				} catch (Stripe_AuthenticationError $e) {
				  // Authentication with Stripe's API failed
				  $st_errormsg = $e->getMessage();
				} catch (Stripe_ApiConnectionError $e) {
				  // Network communication with Stripe failed
				  $st_errormsg = $e->getMessage();
				} catch (Stripe_Error $e) {
				  // Display a very generic error to the user, and maybe send
				  // yourself an email
				  $st_errormsg = $e->getMessage();
				} catch (Exception $e) {
				  // Something else happened, completely unrelated to Stripe
				  $st_errormsg = $e->getMessage();
				}				
				if($st_errormsg!='')
				{
					$messagearr = ['error'=>'error','message'=>$st_errormsg];
	        		echo json_encode($messagearr); exit;
				}
	        }
	        //Code for create payment method :: Start
	        $payment_method_id = '';
	        $headers = array (
	            'Authorization: Bearer '.$stripe_api_key,
	            'Content-type: application/x-www-form-urlencoded'
	        );

	        $fields['card'] = array('number' => $this->input->post('card_number'), 'exp_month' => $this->input->post('card_month'),'exp_year' =>$this->input->post('card_year'),'cvc' => $this->input->post('card_cvv'));
	        $fields['billing_details']['address'] = array('postal_code' => $this->input->post('card_zip'));
	        $fields['type'] = 'card';
	        $post = array();
	        $this->http_build_query_for_curl($fields,$post);        
	        $ch = curl_init();
	        curl_setopt( $ch,CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
	        curl_setopt( $ch,CURLOPT_POST, true );
	        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	        curl_setopt( $ch,CURLOPT_POSTFIELDS, http_build_query($post));
	        $result = curl_exec($ch);
	        $result_arr = json_decode($result,true);
	        if(strpos($result,"error") && !empty($result_arr['error'])){
	        	$messagearr = ['error'=>'error','message'=>$result_arr['error']['message']];
	        	echo json_encode($messagearr); exit;
	        }	        
	        $payment_method_id = $result_arr['id'];
	        $pay_method_fingerprint = $result_arr['card']['fingerprint'];
	        //Code for create payment method :: End
	        //Code for save card :: Start        
			if($payment_method_id != '') {
				$st_errormsg = '';
				//new tweaks :: start
				try {
	                //check if card already saved
	                $all_card_details = $stripe->paymentMethods->all([
	                    'customer' => $stripecus_id,
	                    'type' => 'card',
	                ]);
	                $existing_fingerprint = array();
	                foreach ($all_card_details->data as $cards_key => $cards_value) {
	                    array_push($existing_fingerprint, $cards_value->card->fingerprint);
	                } 
	                //if yes, then don't save again
	                if($pay_method_fingerprint != '' && in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
	                    //card already saved.
						//set card as default
						if($set_as_default_stripecard == 'yes') {
							$this->common_model->set_default_card($stripe, $payment_method_id, $stripecus_id);
						}
	                    $st_errormsg = $this->lang->line('card_already_saved');
	                } else { //if no, then save card
	                    try {
							$attach_card = $stripe->paymentMethods->attach(
								$payment_method_id,
								['customer' => $stripecus_id]
							);
							if(!empty($attach_card) && $attach_card->id != '') {
								//set card as default
								if($set_as_default_stripecard == 'yes') {
									$this->common_model->set_default_card($stripe, $payment_method_id, $stripecus_id);
								}
							}
							http_response_code(200);
							$_SESSION['myProfileMSG'] = $this->lang->line('success_add');
							echo json_encode('success'); exit;
						}
						catch(Stripe_CardError $e) {
							$st_errormsg = $e->getMessage();
						} catch (Stripe_InvalidRequestError $e) {
							// Invalid parameters were supplied to Stripe's API
							$st_errormsg = $e->getMessage();
						} catch (Stripe_AuthenticationError $e) {
							// Authentication with Stripe's API failed
							$st_errormsg = $e->getMessage();
						} catch (Stripe_ApiConnectionError $e) {
							// Network communication with Stripe failed
							$st_errormsg = $e->getMessage();
						} catch (Stripe_Error $e) {
							// Display a very generic error to the user, and maybe send
							// yourself an email
							$st_errormsg = $e->getMessage();
						} catch (Exception $e) {
							// Something else happened, completely unrelated to Stripe
							$st_errormsg = $e->getMessage();
						}
	                }
	            } catch (Exception $e) { // list all cards errors
	                // Something else happened, completely unrelated to Stripe
					$st_errormsg = $e->getMessage();
	            }
				//new tweaks :: end

				if($st_errormsg!='')
				{
					$messagearr = ['error'=>'error','message'=>$st_errormsg];
	        		echo json_encode($messagearr); exit;
				}
			}			
	        //Code for save card :: End
		}        
	}
	public function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null )
    {
        if(is_object($arrays)){
            $arrays = get_object_vars( $arrays );
        }
        foreach( $arrays AS $key => $value ){
            $k = isset($prefix)?$prefix.'['.$key.']':$key;
            if(is_array( $value ) OR is_object($value)){
                $this->http_build_query_for_curl($value,$new,$k);
            }
            else
            {
                $new[$k] = $value;
            }
        }
    }
  	//Code for add/edit stripe card :: End
    //Code for load card detail first after any action on card section :: Start
  	public function view_my_savecard()
    { 
		$data['page_title'] = $this->lang->line('card_detail').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'MyProfile';
		$data['selected_tab'] = "payment_card";  
		$data['profile'] = $this->common_model->getSingleRow('users','entity_id',$this->session->userdata('UserID'));
		$data['addresses'] = $this->common_model->getMultipleRows('user_address','user_entity_id',$this->session->userdata('UserID'));
		$data['wallet_history'] = $this->myprofile_model->getWalletHistory($this->session->userdata('UserID'));
		$data['savecard_detail'] = $this->myprofile_model->getsavecard_detail($this->session->userdata('UserID'));
		$data['in_process_orders'] = $this->myprofile_model->getOrderDetail('process',$this->session->userdata('UserID'),'');  
		if (!empty($data['in_process_orders'])) {
			foreach ($data['in_process_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['in_process_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['in_process_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['in_process_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$reviewOrderId = $this->myprofile_model->getUserOrderReview($this->session->userdata('UserID'));
        $arrReviewOrderId = array();
        if(!empty($reviewOrderId)){
        	$arrReviewOrderId = array_column($reviewOrderId, 'order_id');
        }
        $data['arrReviewOrderId'] = $arrReviewOrderId;

        $data['past_orders'] = $this->myprofile_model->getOrderDetail('past',$this->session->userdata('UserID'),'');
        if (!empty($data['past_orders'])) {
			foreach ($data['past_orders'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_orders'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_orders'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_orders'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		} 
		// bookings tab data
		$data['upcoming_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_events'] = $this->myprofile_model->getBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_events'])) {
			foreach ($data['upcoming_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_events'])) {
			foreach ($data['past_events'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_events'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_events'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_events'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		// table bookings tab data
		$data['upcoming_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'upcoming');  
		$data['past_tables'] = $this->myprofile_model->gettableBooking($this->session->userdata('UserID'),'past');  
		if (!empty($data['upcoming_tables'])) {
			foreach ($data['upcoming_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['upcoming_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['upcoming_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['upcoming_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		if (!empty($data['past_tables'])) {
			foreach ($data['past_tables'] as $key => $value) {
				$ratings = $this->common_model->getRestaurantReview($value['res_content_id']);
				$data['past_tables'][$key]['ratings'] = $ratings;
				$review_data = $this->myprofile_model->getReviewsPagination($value['res_content_id'],review_count,1);
		        $data['past_tables'][$key]['restaurant_reviews'] = $review_data['reviews'];
		        $data['past_tables'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		//get bookmarked res
		$data['users_bookmarks'] = $this->myprofile_model->getBookmarks($this->session->userdata('UserID'));
		if (!empty($data['users_bookmarks'])){
			foreach ($data['users_bookmarks'] as $key => $value){
				$ratings = $this->common_model->getRestaurantReview($value['content_id']);
				$data['users_bookmarks'][$key]['ratings'] = $ratings;
			}
		}
		
		// my addressess tab data
		$data['users_address'] = $this->myprofile_model->getAddress($this->session->userdata('UserID'));
		// my notifications tab data
		$data['users_notifications'] = $this->myprofile_model->getNotifications($this->session->userdata('UserID'));
		// cancel order timer 
		$data['cancel_order_timer'] = $this->db->get_where('system_option',array('OptionSlug'=>'cancel_order_timer'))->first_row();
		$this->load->view('myprofile',$data);
    }
    //Code for load card detail first after any action on card section :: End
    //driver tip changes :: start
    public function applyTipForOrders(){
    	$output_array = array('stripe_html' => '');
		if($this->input->post("action")=='apply'){
			$driver_tip = ($this->input->post("tip_amount"))?(float)$this->input->post("tip_amount"):0;
			$payment_option = ($this->input->post("payment_option"))?(float)$this->input->post("payment_option"):'stripe';			
			if($driver_tip > 0) {
				$this->session->set_userdata('tip_amount',$driver_tip);
				if($this->input->post("tip_percent_val") && intval($this->input->post("tip_percent_val"))>0) {
					$this->session->set_userdata('tip_percent_val', (float)$this->input->post("tip_percent_val"));
				} else {
					$this->session->unset_userdata('tip_percent_val');
				}
				//Check payment option
				if(strtolower($payment_option) == 'paypal') {
					$output_array = array('stripe_html' => '');
				} else {
					//stripe changes 
					$stripecus_id = '';
					$stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
					if($stripecus_arr && !empty($stripecus_arr)) {
						$stripecus_id = $stripecus_arr->stripe_customer_id;
					}
					if($stripecus_id) {
						$stripe_info = stripe_details();
				    	// Include the Stripe PHP bindings library 
				        require APPPATH .'third_party/stripe-php/init.php';
				        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
				        $stripe = new \Stripe\StripeClient($stripe_api_key);

						$default_payment_method = NULL;
						try {
							//get default payment method
							$customer_obj = $stripe->customers->retrieve(
								$stripecus_id,
								[]
							);
							$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
						} catch (Exception $e) {
							//error while retrieving customer
						}

						try {
							//list all cards
							$all_card_details = $stripe->paymentMethods->all([
								'customer' => $stripecus_id,
								'type' => 'card',
							]);

							if($all_card_details && !empty($all_card_details)) {
								if(!$default_payment_method) {
									//set recent card as default
									$this->common_model->set_default_card($stripe, $all_card_details->data[0]->id, $stripecus_id);
									//get default payment method
									try {
										$customer_obj = $stripe->customers->retrieve(
											$stripecus_id,
											[]
										);
										$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
									} catch (Exception $e) {
										//error while retrieving customer
									}
								}
								//sort payment method list :: default payment method on top
								if($default_payment_method) {
									if(in_array($default_payment_method, array_column($all_card_details->data, 'id'))) {
										usort($all_card_details->data,function($a,$b) use ($default_payment_method) {
											if ($a->id != $default_payment_method && $b->id == $default_payment_method) {
												return 1;
											} elseif ($a->id == $default_payment_method && $b->id != $default_payment_method) {
												return -1;
											} else {
												return 0;
											}
										});
									}
								}
								//add new key is_default_card in all payment method object
								foreach ($all_card_details->data as $allmethods_key => $allmethods_value) {
									if($allmethods_value->id == $default_payment_method) {
										$all_card_details->data[$allmethods_key]->is_default_card = '1';
									} else {
										$all_card_details->data[$allmethods_key]->is_default_card = '0';
									}
								}
								$all_card_detailaarr = $all_card_details->data;
								for($strid = 0; $strid < count($all_card_detailaarr); $strid++) {
									$PaymentMethodid = $all_card_detailaarr[$strid]->id;
									$card_brand = $all_card_detailaarr[$strid]->card->brand;
									$card_last4 = $all_card_detailaarr[$strid]->card->last4;
									$card_number = "************".$card_last4;
									$card_fingerprint = $all_card_detailaarr[$strid]->card->fingerprint;
									$exp_month = $all_card_detailaarr[$strid]->card->exp_month;
									$exp_year = $all_card_detailaarr[$strid]->card->exp_year;
									$is_default_card = $all_card_detailaarr[$strid]->is_default_card;

									$card_image = '';
									if($card_brand =='unionpay')
									{
										$card_image = 'assets/front/images/cardimg/unionpay.jpg';
									}
									else if($card_brand =='amex')
									{
										$card_image = 'assets/front/images/cardimg/american_express.jpg';
									}
									if($card_brand =='jcb')
									{
										$card_image = 'assets/front/images/cardimg/jcb.jpg';
									}
									if($card_brand =='diners')
									{
										$card_image = 'assets/front/images/cardimg/diners_club.jpg';
									}
									if($card_brand =='discover')
									{
										$card_image = 'assets/front/images/cardimg/discover.jpg';
									}
									if($card_brand =='mastercard')
									{
										$card_image = 'assets/front/images/cardimg/mastercard.jpg';
									}
									if($card_brand =='visa')
									{
										$card_image = 'assets/front/images/cardimg/visa.jpg';
									}
									$card_brand_name = '';
									if($card_brand == 'amex' || $card_brand == 'mastercard' || $card_brand == 'visa' || $card_brand == 'discover' || $card_brand == 'diners' || $card_brand == 'jcb' || $card_brand == 'unionpay'){
										$card_brand_name = $this->lang->line($card_brand);
									} else {
										$card_brand_name = ucfirst($card_brand);
									}

									$checkedval = '';
									if($strid == 0) {
										$checkedval = 'checked="checked"';
									}
									$default_card_class = ($is_default_card == '1') ? 'default-stripe-card' : '' ;
									$stripe_html .='<div class="payment_card_box '.$default_card_class.'">
										<div class="radio-btn-list">
										<label class="payment_label">
										<input type="radio" name="payment-source-btn" value="saved_card_'.($strid+1).'" card_fingerprint="'.$card_fingerprint.'" PaymentMethodid="'.$PaymentMethodid.'" '.$checkedval.' onclick="togglecardbutton(this.value);">
										<span></span>
										<div class="card_image"><img src="'.base_url().$card_image.'" width="60"></div>
										<div class="card-name-no">
										<h5 class="card_name">'.$value['card_brand_name'].'</h5>
										<h6 class="saved-card">'.$this->lang->line('ending_in').$card_last4.', '.$this->lang->line('expires').$exp_month.'/'.$exp_year.'</h6>
										</div>

										</label>
										</div>
										</div>';
								}
								$output_array = array('stripe_html' => $stripe_html);
							}
						}catch (Exception $e) {
							echo json_encode($output_array); exit;
						}
					}
				}	
			}
		} else {
			$driver_tip = ($this->input->post("tip_amount"))?(float)$this->input->post("tip_amount"):0;
			$tip_percent_val = ($this->input->post("tip_percent_val"))?(float)$this->input->post("tip_percent_val"):0;
			//$driver_tip = 0;
			$this->session->set_userdata('tip_amount', $driver_tip);
			if($tip_percent_val<=0){
				$this->session->unset_userdata('tip_percent_val');	
			}
		}
		echo json_encode($output_array); exit;
    }
    public function createintent_fordrivertip()
    {
		$driver_tip = ($this->session->userdata('tip_amount'))?(float)$this->session->userdata('tip_amount'):0;

		// wallet topup changes :: start
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str);
		$intent_for = (!empty($json_obj) && $json_obj->intent_for == 'wallet_topup') ? 'wallet_topup' : '';
		$topup_amount = (!empty($json_obj) && $json_obj->topup_amount != '') ? (float)$json_obj->topup_amount : '';
		$amount_intended = ($intent_for == 'wallet_topup') ? $topup_amount : $driver_tip;
		// wallet topup changes :: end
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$currency_symbol = $default_currency;
		}

		if($amount_intended > 0){
			$stripe_info = stripe_details();
			// Include the Stripe PHP bindings library 
			require APPPATH .'third_party/stripe-php/init.php';
			$stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
			
			$stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
			$stripecus_id = '';
			if($stripecus_arr && !empty($stripecus_arr))
			{
				$stripecus_id = $stripecus_arr->stripe_customer_id;
			}
			if($stripecus_id =='' && $this->session->userdata('UserType') == 'User')
			{
				$user_fname = ($this->session->userdata('userFirstname') != '')?$this->session->userdata('userFirstname'):'';
				$user_lname = ($this->session->userdata('userLastname') != '')?$this->session->userdata('userLastname'):'';
				$userphonecode = ($this->session->userdata('userPhone_code') != '')?$this->session->userdata('userPhone_code'):'';
				$userphoneno = ($this->session->userdata('userPhone') != '')?$this->session->userdata('userPhone'):'';
				$useremail = ($this->session->userdata('userEmail'))?$this->session->userdata('userEmail'):'';

				$stripe_customer_id = $this->common_model->add_new_customer_in_stripe($user_fname,$user_lname,$userphonecode,$userphoneno,$useremail);

				if($stripe_customer_id){
					$stripecus_id = $stripe_customer_id;
					$update = array(
						'stripe_customer_id'=>$stripe_customer_id
					);
					$this->common_model->updateData('users',$update,'entity_id',$val->entity_id);
				}
			}
			\Stripe\Stripe::setApiKey($stripe_api_key);
			header('Content-Type: application/json');
			try {
				$paymentIntent = \Stripe\PaymentIntent::create([
					'setup_future_usage'=> 'off_session',
					'amount' => $amount_intended * 100,
					'currency' => $currency_symbol->currency_code,
				]);
				$output = [
					'clientSecret' => $paymentIntent->client_secret,
					'stripecus_id' => $stripecus_id,
					'is_savecard' => 'yes',
					'amount' => $amount_intended,
					'trans_id' => $paymentIntent->id,
				];
				echo json_encode($output);
			} catch (Exception $e) {
				//http_response_code(500);
				echo json_encode(['error' => $e->getMessage()]);
			}
		}
    }
    public function updateOrderSummary()
    {
    	// retrieve JSON from POST body
        $json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str);
		$tip_order_id = $json_obj->tip_order_id_inp;
		$tip_transaction_id = $json_obj->tip_transaction_id;
		$payment_option = ($json_obj->payment_option)?strtolower($json_obj->payment_option):'';
		$driver_tip = (float)$json_obj->tip_amount_inp;
		$tip_percent_val = ($this->session->userdata('tip_percent_val') > 0)?(float)$this->session->userdata('tip_percent_val'):0;
		$thirdparty_tip_update = array('status'=>1);
		if($driver_tip && $driver_tip>0)
        {
        	$orderdetails = $this->common_model->getDoorDash_OrderDetails($tip_order_id);
			//update driver tip on thirdparty delivery method
			if($orderdetails->delivery_method == 'relay'){
				$thirdparty_tip_update = $this->common_model->updateRelayDriverTip($tip_order_id, $driver_tip);
			} else if($orderdetails->delivery_method == 'doordash') {
				$thirdparty_tip_update = $this->common_model->updateDoorDashDriverTip($tip_order_id, $driver_tip);
			}
			if($thirdparty_tip_update['status']==1){
				//update total in order master
				$total_amount = (float)$orderdetails->order_total;
				$total_amount = $total_amount + $driver_tip;
				$this->common_model->updateData('order_master',array('total_rate'=>$total_amount),'entity_id',$tip_order_id);

	        	//update driver tip table
	            $add_tip = array(
	                'order_id'=>$tip_order_id,
	                'user_id'=>($orderdetails->user_id)?$orderdetails->user_id:0,
	                'tips_transaction_id'=>$tip_transaction_id,
	                'tip_percentage' => ($tip_percent_val > 0)?$tip_percent_val:NULL,
	                'payment_option' => $payment_option,
	                'amount'=>$driver_tip,
	                'date'=>date('Y-m-d H:i:s')
	            );
	            if($orderdetails->delivery_method == 'internal_drivers'){
	            	$internaldriverid = $this->common_model->getInternalDriverId($tip_order_id);
	            	if($internaldriverid){
	            		$add_tip['driver_id'] = $internaldriverid;
	            	}
	            }
	            $tips_id = $this->common_model->addData('tips',$add_tip);
	            $echo_stat = array('status' => 'success');
	            http_response_code(200);
				echo json_encode($echo_stat);
			}else{
				//refund driver tip
	            if($tip_transaction_id != '') {
	            	$data['order_records'] = $this->common_model->getOrderTransactionIds($tip_order_id);
	            	//update driver tip table
		            $add_tip = array(
		                'order_id'=>$tip_order_id,
		                'user_id'=>($orderdetails->user_id)?$orderdetails->user_id:0,
		                'tips_transaction_id'=>$tip_transaction_id,
		                'tip_percentage' => NULL,
		                'payment_option' => $payment_option,
		                'amount'=>0.00,
		                'date'=>date('Y-m-d H:i:s')
		            );
		            $tips_id = $this->common_model->addData('tips',$add_tip);
	                $response = $this->common_model->StripeRefund('',$tip_order_id,$tip_transaction_id,$tips_id);
					//send refund noti to user
					if($data['order_records']->user_id && $data['order_records']->user_id > 0){
						if(!empty($response) && ($response['paymentIntentstatus'] || $response['tips_paymentIntentstatus'])) {
							$this->common_model->sendRefundNoti($tip_order_id,$data['order_records']->user_id,$data['order_records']->restaurant_id,'',$tip_transaction_id,'',$response['tips_paymentIntentstatus'],$response['error']);
						}
					}
	                $response['error_message']='';
					if($response['paymentIntentstatus']=='failed' || $response['tips_paymentIntentstatus']=='failed'){
			            $response['error_message'] = $this->lang->line('refund_failed');
			        }else if($response['paymentIntentstatus']=='canceled' || $response['tips_paymentIntentstatus']=='canceled'){
			            $response['error_message'] = $this->lang->line('refund_canceled');
			        }else if($response['paymentIntentstatus']=='pending' || $response['tips_paymentIntentstatus']=='pending'){
			            $response['error_message'] = $this->lang->line('refund_pending');
			        }
	                echo json_encode($response);
	            }
			}
        }
    }
    public function save_carddetail()
    {
    	$stripe_info = stripe_details();
    	// Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php';
        $stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
    	$stripe = new \Stripe\StripeClient($stripe_api_key);

    	$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str);
		$payment_method_id = $json_obj->payment_method;
		$stripecus_id = $json_obj->stripecus_id;

		try {
			//get card fingerprint (to check if card already saved)
			$get_fingerprint = $stripe->paymentMethods->retrieve(
				$payment_method_id,
				[]
			);
			$pay_method_fingerprint = $get_fingerprint->card->fingerprint;
			try {
	            //check if card already saved
	            $all_card_details = $stripe->paymentMethods->all([
	                'customer' => $stripecus_id,
	                'type' => 'card',
	            ]);
	            $existing_fingerprint = array();
	            foreach ($all_card_details->data as $cards_key => $cards_value) {
	                array_push($existing_fingerprint, $cards_value->card->fingerprint);
	            }
	            //if yes, then don't save again
                if($pay_method_fingerprint != '' && in_array($pay_method_fingerprint, array_unique($existing_fingerprint))) {
                    //card already saved.
                    echo json_encode(['error' => $this->lang->line('card_already_saved')]);
                } else { //if no, then save card
					try {
						$attach_card = $stripe->paymentMethods->attach(
							$payment_method_id,
							['customer' => $stripecus_id]
						);
						http_response_code(200);
						echo json_encode($attach_card);
					} catch (Exception $e) {
						echo json_encode(['error' => $e->getMessage()]);
					}
                }
	        } catch (Exception $e) {  // list all cards errors
				echo json_encode(['error' => $e->getMessage()]);
			}
		} catch (Exception $e) { //get payment method errors
			echo json_encode(['error' => $e->getMessage()]);
		}
    }
    public function create_paymentwithcard()
    {
		$driver_tip = ($this->session->userdata('tip_amount'))?(float)$this->session->userdata('tip_amount'):0;

		// wallet topup changes :: start
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str);
		$payment_for = (!empty($json_obj) && $json_obj->payment_for == 'wallet_topup') ? 'wallet_topup' : '';
		$topup_amount = (!empty($json_obj) && $json_obj->topup_amount != '') ? (float)$json_obj->topup_amount : '';
		$payment_amount = ($payment_for == 'wallet_topup') ? $topup_amount : $driver_tip;
		// wallet topup changes :: end
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$currency_symbol = $default_currency;
		}

		if($payment_amount > 0){
			$stripe_info = stripe_details();
			// Include the Stripe PHP bindings library 
			require APPPATH .'third_party/stripe-php/init.php';
			$stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;

			// retrieve JSON from POST body
			$payment_methodid = $json_obj->payment_method;

			$stripecus_id = '';
			$stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
			if($stripecus_arr && !empty($stripecus_arr))
			{
				$stripecus_id = $stripecus_arr->stripe_customer_id;
			}

			// Set API key 
			\Stripe\Stripe::setApiKey($stripe_api_key);
			header('Content-Type: application/json');
			try {
				$paymentIntent = \Stripe\PaymentIntent::create([
					'customer' => $stripecus_id,
					'payment_method_types' => ['card'],
					'payment_method' => $payment_methodid,
					'amount' => $payment_amount * 100,				
					'currency' => $currency_symbol->currency_code,
				]);

				$paymentconfirm = \Stripe\PaymentIntent::retrieve($paymentIntent->id);
				$paymentconfirm->confirm();

				$output = [
					'clientSecret' => $paymentIntent->client_secret,
					'stripecus_id' => $stripecus_id,
					'paymentIntentid' => $paymentIntent->id,
					'paymentIntentstatus' => 'succeeded',
					'paymentconfirm_status' => $paymentconfirm->status,
					'amount' => $payment_amount,
				];
				echo json_encode($output);
			} catch (Exception $e) {
				//http_response_code(500);
				echo json_encode(['error' => $e->getMessage()]);
			}
		}
    }
    //driver tip changes :: end
    //get order tip transaction id :: start
    public function gettransactionid()
	{
		$tips_transaction_id = 0;
		$order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):0;				
		if($order_id && $order_id>0)
		{
			$tips_transaction_id = $this->myprofile_model->getTransactionId($order_id);
		}				
		echo $tips_transaction_id; exit;
  	}
  	//get order tip transaction id :: end
  	public function checkTipPaid()
    {
    	$tippaid_array = array('tip_paid_status' => '');
    	$order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):0;
    	if($order_id && $order_id>0)
		{
      		$tips_paid_status = $this->common_model->checkDriverTipPaid($order_id);
      		if($tips_paid_status==1){
      			$tippaid_array = array('tip_paid_status' => 'tip_paid');
      		}
      	}
      	$order_subtotal = $this->common_model->getOrderSubtotal($order_id);
      	$data['order_subtotal'] = $order_subtotal->subtotal;
      	$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($order_subtotal->restaurant_id);
		}
		//Code for find the payment option :: Start		
		$payment_optionArr = $this->myprofile_model->getPaymentMethod($order_id,$this->session->userdata('language_slug'),$this->session->userdata('UserType'));
		$data['payment_option'] = $payment_optionArr;
		//Code for find the payment option :: End		
      	$ajax_driver_tips = $this->load->view('ajax_driver_tips_myprofile',$data,true);
      	$tippaid_array['ajax_driver_tips'] = $ajax_driver_tips;
      	echo json_encode($tippaid_array); exit;
    }
    //wallet topup changes :: start
    public function populateSavedCards() {
    	$output_array = array('stripe_html' => '');
		//stripe changes 
		$stripecus_id = '';
		$stripecus_arr = $this->db->select('stripe_customer_id')->get_where('users',array('entity_id'=>$this->session->userdata('UserID')))->first_row();
		if($stripecus_arr && !empty($stripecus_arr)) {
			$stripecus_id = $stripecus_arr->stripe_customer_id;
		}
		if($stripecus_id) {
			$stripe_info = stripe_details();
			// Include the Stripe PHP bindings library 
			require APPPATH .'third_party/stripe-php/init.php';
			$stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
			$stripe = new \Stripe\StripeClient($stripe_api_key);

			$default_payment_method = NULL;
			try {
				//get default payment method
				$customer_obj = $stripe->customers->retrieve(
					$stripecus_id,
					[]
				);
				$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
			} catch (Exception $e) {
				//error while retrieving customer
			}

			try {
				//list all cards
				$all_card_details = $stripe->paymentMethods->all([
					'customer' => $stripecus_id,
					'type' => 'card',
				]);

				if($all_card_details && !empty($all_card_details)) {
					if(!$default_payment_method) {
						//set recent card as default
						$this->common_model->set_default_card($stripe, $all_card_details->data[0]->id, $stripecus_id);
						//get default payment method
						try {
							$customer_obj = $stripe->customers->retrieve(
								$stripecus_id,
								[]
							);
							$default_payment_method = ($customer_obj->invoice_settings->default_payment_method) ? $customer_obj->invoice_settings->default_payment_method : NULL;
						} catch (Exception $e) {
							//error while retrieving customer
						}
					}
					//sort payment method list :: default payment method on top
					if($default_payment_method) {
						if(in_array($default_payment_method, array_column($all_card_details->data, 'id'))) {
							usort($all_card_details->data,function($a,$b) use ($default_payment_method) {
								if ($a->id != $default_payment_method && $b->id == $default_payment_method) {
									return 1;
								} elseif ($a->id == $default_payment_method && $b->id != $default_payment_method) {
									return -1;
								} else {
									return 0;
								}
							});
						}
					}
					//add new key is_default_card in all payment method object
					foreach ($all_card_details->data as $allmethods_key => $allmethods_value) {
						if($allmethods_value->id == $default_payment_method) {
							$all_card_details->data[$allmethods_key]->is_default_card = '1';
						} else {
							$all_card_details->data[$allmethods_key]->is_default_card = '0';
						}
					}
					$all_card_detailaarr = $all_card_details->data;
					for($strid = 0; $strid < count($all_card_detailaarr); $strid++) {
						$PaymentMethodid = $all_card_detailaarr[$strid]->id;
						$card_brand = $all_card_detailaarr[$strid]->card->brand;
						$card_last4 = $all_card_detailaarr[$strid]->card->last4;
						$card_number = "************".$card_last4;
						$card_fingerprint = $all_card_detailaarr[$strid]->card->fingerprint;
						$exp_month = $all_card_detailaarr[$strid]->card->exp_month;
						$exp_year = $all_card_detailaarr[$strid]->card->exp_year;
						$is_default_card = $all_card_detailaarr[$strid]->is_default_card;

						$card_image = '';
						if($card_brand =='unionpay')
						{
							$card_image = 'assets/front/images/cardimg/unionpay.jpg';
						}
						else if($card_brand =='amex')
						{
							$card_image = 'assets/front/images/cardimg/american_express.jpg';
						}
						if($card_brand =='jcb')
						{
							$card_image = 'assets/front/images/cardimg/jcb.jpg';
						}
						if($card_brand =='diners')
						{
							$card_image = 'assets/front/images/cardimg/diners_club.jpg';
						}
						if($card_brand =='discover')
						{
							$card_image = 'assets/front/images/cardimg/discover.jpg';
						}
						if($card_brand =='mastercard')
						{
							$card_image = 'assets/front/images/cardimg/mastercard.jpg';
						}
						if($card_brand =='visa')
						{
							$card_image = 'assets/front/images/cardimg/visa.jpg';
						}
						$card_brand_name = '';
						if($card_brand == 'amex' || $card_brand == 'mastercard' || $card_brand == 'visa' || $card_brand == 'discover' || $card_brand == 'diners' || $card_brand == 'jcb' || $card_brand == 'unionpay'){
							$card_brand_name = $this->lang->line($card_brand);
						} else {
							$card_brand_name = ucfirst($card_brand);
						}

						$checkedval = '';
						if($strid == 0) {
							$checkedval = 'checked="checked"';
						}
						$default_card_class = ($is_default_card == '1') ? 'default-stripe-card' : '' ;
						$stripe_html .='<div class="payment_card_box '.$default_card_class.'">
							<div class="radio-btn-list">
							<label class="payment_label">
							<input type="radio" name="payment-source-btn-forwallet" value="saved_card_'.($strid+1).'" card_fingerprint="'.$card_fingerprint.'" PaymentMethodid="'.$PaymentMethodid.'" '.$checkedval.' onclick="togglecardbutton_forwallet(this.value);">
							<span></span>
							<div class="card_image"><img src="'.base_url().$card_image.'" width="60"></div>
							<div class="card-name-no">
							<h5 class="card_name">'.$value['card_brand_name'].'</h5>
							<h6 class="saved-card">'.$this->lang->line('ending_in').$card_last4.', '.$this->lang->line('expires').$exp_month.'/'.$exp_year.'</h6>
							</div>

							</label>
							</div>
							</div>';
					}
					$output_array = array('stripe_html' => $stripe_html);
				}
			} catch (Exception $e) {
				echo json_encode($output_array); exit;
			}
		}
		echo json_encode($output_array); exit;
    }
    public function updateWalletHistory() {
		// retrieve JSON from POST body
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str);
		$topup_amount = (float)$json_obj->topup_amount;
		$wallet_transaction_id = $json_obj->wallet_transaction_id;

		if($topup_amount && $topup_amount > 0) {
			//update wallet in users table
			$users_wallet = $this->common_model->getUsersWalletMoney($this->session->userdata('UserID'));
			$new_wallet_amount = (float)$users_wallet->wallet + (float)$topup_amount;

			$walletdata = array('wallet' => $new_wallet_amount);
			$this->common_model->updateData('users',$walletdata,'entity_id',$this->session->userdata('UserID'));

			//update wallet history table
			$add_wallet_history = array(
				'user_id' => $this->session->userdata('UserID'),
				'amount' => (float)$topup_amount,
				'credit' => 1,
				'wallet_transaction_id' => $wallet_transaction_id,
				'reason' => 'credit_via_wallet_topup',
				'created_date' => date('Y-m-d H:i:s')
			);
			$this->common_model->addData('wallet_history',$add_wallet_history);
			$echo_stat = array('status' => 'success');
			http_response_code(200);
			echo json_encode($echo_stat);
        }
    }
    //wallet topup changes :: end
    //Code for tip with paypal :: Start
    public function tip_process()
    {
    	$data = array('transaction_id' => '');
		if(!empty($_GET['paymentID']) && !empty($_GET['token']) && !empty($_GET['payerID']))
		{ 
			// Include and initialize paypal class 
			require APPPATH . 'libraries/PaypalExpress.php';
			$paypal = new PaypalExpress; 
			// Get payment info from URL 
			$paymentID = $_GET['paymentID'];
			$token = $_GET['token'];
			$payerID = $_GET['payerID'];			
			// Validate transaction via PayPal API 
			$paymentCheck = $paypal->validate($paymentID, $token, $payerID, $productID);

			// If the payment is valid and approved 
			if($paymentCheck && $paymentCheck->state == 'approved')
			{
				// Get the transaction data 
				$transaction_id = $paymentCheck->transactions[0]->related_resources[0]->sale->id;
				$id = ($transaction_id && $transaction_id!='')?$transaction_id:$paymentCheck->id;  //transaction id
				$state = $paymentCheck->state; // transaction status
				$payerFirstName = $paymentCheck->payer->payer_info->first_name; 
				$payerLastName = $paymentCheck->payer->payer_info->last_name; 
				$payerName = $payerFirstName.' '.$payerLastName; 
				$payerEmail = $paymentCheck->payer->payer_info->email; 
				$payerID = $paymentCheck->payer->payer_info->payer_id; 
				$payerCountryCode = $paymentCheck->payer->payer_info->country_code; 
				$paidAmount = $paymentCheck->transactions[0]->amount->details->subtotal; 
				$currency = $paymentCheck->transactions[0]->amount->currency; 
				
				$data = array( 					
					'transaction_id' => $id
				);
				echo json_encode($data); exit;
			}			
		}		
		echo json_encode($data); exit;
    }
	//Code for tip with paypal :: End
	public function set_default_stripecard() {
		$PaymentMethodid = ($this->input->post('PaymentMethodid'))?$this->input->post('PaymentMethodid'):'';
		$stripecus_id = ($this->input->post('stripecus_id'))?$this->input->post('stripecus_id'):'';
		$is_saved = array('error'=>'');
		if($PaymentMethodid != '' && $stripecus_id != '') {
			$stripe_info = stripe_details();
			// Include the Stripe PHP bindings library 
			require APPPATH .'third_party/stripe-php/init.php';
			$stripe_api_key = ($stripe_info->enable_live_mode == '1') ? $stripe_info->live_secret_key:$stripe_info->test_secret_key;
			$stripe = new \Stripe\StripeClient($stripe_api_key);

			$is_saved = $this->common_model->set_default_card($stripe, $PaymentMethodid, $stripecus_id);
			if($is_saved) {
				if($is_saved['error'] == '') {
					$_SESSION['myProfileMSG'] = $this->lang->line('success_update');
				} else {
					$_SESSION['delete_cardmessage'] = $is_saved['Error'];
				}
			}
		}
		echo json_encode($is_saved); exit;
	}
	public function removeBookmark(){
		$this->myprofile_model->removeBookmark($this->session->userdata('UserID'),$this->input->post('restaurant_id'));
	}
} ?>