<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Restaurant extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
        $this->load->library('ajax_pagination'); 
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/restaurant_model');    
	}
	// get the restaurants
	public function index()
	{
        $data['page_title'] = $this->lang->line('order_food').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'OrderFood';
		$order_mode = ($this->session->userdata('order_mode_frm_dropdown')) ? $this->session->userdata('order_mode_frm_dropdown') : '';
		$range = $this->common_model->getRange();
		if($order_mode == 'PickUp') {
			$data['maximum_range'] = (int)$range[2]->OptionValue;
		} else {
			$data['maximum_range'] = (int)$range[1]->OptionValue;
		}
		$data['minimum_range'] = (int)$range[0]->OptionValue;
		$latitude = ($this->session->userdata('latitude'))?$this->session->userdata('longitude'):'';
		$longitude = ($this->session->userdata('latitude'))?$this->session->userdata('longitude'):'';
		$page = 0; 
		$result = $this->restaurant_model->getRestaurantsForOrder(6,$page,'',$latitude,$longitude,'','','','','pagination','',$order_mode);
		if (!empty($result)) {
			foreach ($result as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['content_id']);
				$result[$key]['ratings'] = $ratings;
				$review_data = $this->restaurant_model->getReviewsPagination($value['content_id'],review_count,1);
				$result[$key]['restaurant_reviews'] = $review_data['reviews'];
				$result[$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$countResult = $this->restaurant_model->getRestaurantsForOrder(6,$page,'',$latitude,$longitude,'','','','','','',$order_mode);
		$data['restaurants'] = $result;
        $data['TotalRecord'] = count($result);
        $config = array();
        $config["base_url"] = base_url() . "restaurant/index";        
        $config["total_rows"] = count($countResult);
        $config["per_page"] = 6;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links();
        //Food type related code Start
        $getFoodType = $this->restaurant_model->getFoodType();
		$data['food_type'] = $getFoodType;
		//Food type related code End 
		$this->load->view('order_food',$data);
	}
	// ajax restaurants
	public function ajax_restaurants()
	{
        $data['page_title'] = $this->lang->line('order_food').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'OrderFood';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
		$resdishes = ($this->input->post('resdishes'))?$this->input->post('resdishes'):'';
		$order_mode = ($this->input->post('order_mode'))?$this->input->post('order_mode'):'';
		if($order_mode != ''){
			$this->session->set_userdata('order_mode_frm_dropdown', $order_mode);
		} else {
			$this->session->set_userdata('order_mode_frm_dropdown', '');
		}
		$latitude = ($this->input->post('latitude'))?$this->input->post('latitude'):'';
		$longitude = ($this->input->post('longitude'))?$this->input->post('longitude'):'';
		$minimum_range = ($this->input->post('minimum_range'))?str_replace( ',', '', $this->input->post('minimum_range') ):0;
		$maximum_range = ($this->input->post('maximum_range'))?str_replace( ',', '', $this->input->post('maximum_range') ):0;
		$food_veg = ($this->input->post('food_veg'))?$this->input->post('food_veg'):0;
		$food_non_veg = ($this->input->post('food_non_veg'))?$this->input->post('food_non_veg'):0;
		$food_type = ($this->input->post('food_type'))?$this->input->post('food_type'):'';		
		$result = $this->restaurant_model->getRestaurantsForOrder(6,$page,$resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_veg,$food_non_veg,'pagination',$food_type,$order_mode);
		/*if (!empty($result)) {
			foreach ($result as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['content_id']);
				$result[$key]['ratings'] = $ratings;
			}
		}*/		
		$countResult = $this->restaurant_model->getRestaurantsForOrder(6,$page,$resdishes,$latitude,$longitude,$minimum_range,$maximum_range,$food_veg,$food_non_veg,'',$food_type,$order_mode);
		$data['restaurants'] = $result;
        $data['TotalRecord'] = count($result);
        $config = array();
        $config["base_url"] = base_url() . "restaurant/index";        
        $config["total_rows"] = count($countResult);
        $config["per_page"] = 6;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
        $this->load->view('ajax_order_food',$data);
	}
	// get restaurant details
	public function restaurant_detail()
	{
        $data['page_title'] = $this->lang->line('restaurant_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'Restaurant Details';
		$data['restaurant_details'] = array();
		$this->session->set_userdata('tip_amount', 0);
		$this->session->unset_userdata('tip_percent_val');
		if (!empty($this->uri->segment('3'))) {
			$content_id = $this->restaurant_model->getContentID($this->uri->segment('3'));
			$data['restaurant_details'] = $this->restaurant_model->getRestaurantDetail($content_id->content_id);
			$data['restaurant_coupons'] = $this->restaurant_model->get_restaurant_coupons($content_id->content_id);
			$data['categories_count'] = count($data['restaurant_details']['categories']);

			$recipe_content_id = $this->restaurant_model->getRecipe();
			$data['recipe_menu_content'] = array_column($recipe_content_id,'menu_content_id'); 
			if (!empty($data['restaurant_details']['restaurant'])) {
				$ratings = $this->restaurant_model->getRestaurantReview($data['restaurant_details']['restaurant'][0]['content_id']);
				//$data['restaurant_reviews'] = $this->restaurant_model->getReviewsRatings($data['restaurant_details']['restaurant'][0]['content_id']);
				$review_data = $this->restaurant_model->getReviewsPagination($data['restaurant_details']['restaurant'][0]['content_id'],review_count,1);
				$data['restaurant_reviews'] = $review_data['reviews'];
				$data['restaurant_reviews_count'] = $review_data['review_count'];
				$data['restaurant_details']['restaurant'][0]['ratings'] = $ratings;
				$data['restaurant_details']['restaurant'][0]['is_rating_from_res_form'] = $review_data['is_rating_from_res_form'];
			}
			$this->session->set_userdata(array('package_id' => ''));
		} 
		$cart_details = get_cookie('cart_details');
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$data['cart_restaurant']);
		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;
		// for adding review functionality
		$total_orders = $this->restaurant_model->getTotalOrders($this->session->userdata('UserID'),$data['restaurant_details']['restaurant'][0]['restaurant_id']);
		$total_reviews = $this->restaurant_model->getTotalReviews($this->session->userdata('UserID'),$data['restaurant_details']['restaurant'][0]['content_id']);
		$data['remaining_reviews'] = $total_orders - $total_reviews;
		$data['get_bookmark'] = $this->restaurant_model->getBookmarkRestaurant($data['restaurant_details']['restaurant'][0]['restaurant_id']);		
		$this->load->view('restaurant_details',$data);
	}
	// get ajax restaurant details
	public function ajax_restaurant_details()
	{
        $data['page_title'] = $this->lang->line('restaurant_details').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'Restaurant Details';
		$searchDish = array(); $availability= $food_type = '';
		if (!empty($this->input->post('searchDish'))) {
			$searchDish = explode(",", $this->input->post('searchDish'));
		}
		if (!empty($this->input->post('food')) && $this->input->post('food')) {
			//$food_type = implode(",", $this->input->post('food'));
			$food_type = $this->input->post('food');
		}

		if (!empty($this->input->post('availability')) && $this->input->post('availability')) {
			$availability = $this->input->post('availability');
		}
		$data['restaurant_details'] = array();
		if (!empty($this->input->post('content_id'))) {
			$data['restaurant_details'] = $this->restaurant_model->getRestaurantDetail($this->input->post('content_id'),$searchDish,$food_type,$this->input->post('price'),$availability);
			$data['categories_count'] = count($data['restaurant_details']['categories']);
			if (!empty($data['restaurant_details']['restaurant'])) {
				$ratings = $this->restaurant_model->getRestaurantReview($data['restaurant_details']['restaurant'][0]['content_id']);
				$data['restaurant_details']['restaurant'][0]['ratings'] = $ratings;
				$review_data = $this->restaurant_model->getReviewsPagination($value['content_id'],review_count,1);
				$data['restaurant_details']['restaurant'][0]['restaurant_reviews'] = $review_data['reviews'];
				$data['restaurant_details']['restaurant'][0]['restaurant_reviews_count'] = $review_data['review_count'];
				$data['restaurant_details']['restaurant'][0]['is_rating_from_res_form'] = $review_data['is_rating_from_res_form'];
			}
		} 
		$cart_details = get_cookie('cart_details');
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$data['cart_restaurant']);
		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;

		$recipe_content_id = $this->restaurant_model->getRecipe();
		$data['recipe_menu_content'] = array_column($recipe_content_id,'menu_content_id');

		$this->load->view('ajax_restaurant_detail',$data);
	}
	// get Cart items
	public function getCartItems($cart_details,$cart_restaurant){
		$cartItems = array();
		$cartTotalPrice = 0;
		if (!empty($cart_details)) {
			foreach (json_decode($cart_details) as $key => $value) { 
				$details = $this->restaurant_model->getMenuItem($value->menu_id,$cart_restaurant);
				if (!empty($details)) {
					if ($details[0]['items'][0]['is_customize'] == 1) {
						$addons_category_id = $add_onns_id = array();
						if($value->addons && !empty($value->addons)){
							$addons_category_id = array_column($value->addons, 'addons_category_id');
							$add_onns_id = array_column($value->addons, 'add_onns_id');
						}
						
						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
									unset($details[0]['items'][0]['addons_category_list'][$key]);
								}
								else
								{
									if (!empty($cat_value['addons_list'])) {
										foreach ($cat_value['addons_list'] as $addkey => $add_value) {
											if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
												unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
											}
										}
									}
								}
							}
						}
					}
					// getting subtotal
					if ($details[0]['items'][0]['is_customize'] == 1) 
					{	$subtotal = 0;
						$offer_price = str_replace(",", "", $details[0]['items'][0]['offer_price']);
						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!empty($cat_value['addons_list'])) {
									foreach ($cat_value['addons_list'] as $addkey => $add_value) {
										$subtotal = $subtotal + $add_value['add_ons_price'];
									}
								}
							}
							if($details[0]['items'][0]['offer_price']>0)
							{
								$subtotal = ($details[0]['items'][0]['offer_price'])? $subtotal + $offer_price : $subtotal;
							}
							else
							{
								$subtotal = ($details[0]['items'][0]['price'])? $subtotal + $details[0]['items'][0]['price'] : $subtotal;
							}
						} else {
							if($details[0]['items'][0]['offer_price']>0)
							{
								$subtotal = ($details[0]['items'][0]['offer_price'])? $subtotal + $offer_price : $subtotal;
							}
							else
							{
								$subtotal = ($details[0]['items'][0]['price'])? $subtotal + $details[0]['items'][0]['price'] : $subtotal;
							}	
						}
					}
					else
					{	$subtotal = 0;
						if($details[0]['items'][0]['offer_price']>0){
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						else
						{
							$price = ($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0;
						}
						$mprice = str_replace(",","",$price);
						$subtotal = $subtotal + $mprice;
					}
					$cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
					$cartItems[] = array(
						'menu_id' => $details[0]['items'][0]['menu_id'],
						'restaurant_id' => $cart_restaurant,
						'name' => $details[0]['items'][0]['name'],
						'menu_detail' => $details[0]['items'][0]['menu_detail'],
						'is_combo_item' => $details[0]['items'][0]['is_combo_item'],
						'quantity' => $value->quantity,
						'is_customize' => $details[0]['items'][0]['is_customize'],
						'is_veg' => $details[0]['items'][0]['is_veg_food'],
						'is_deal' => $details[0]['items'][0]['is_deal'],
						'price' => $details[0]['items'][0]['price'],
						'offer_price' => $details[0]['items'][0]['offer_price'],
						'subtotal' => $subtotal,
						'totalPrice' => ($subtotal * $value->quantity),
						'cartTotalPrice' => $cartTotalPrice,
						'addons_category_list' => @$details[0]['items'][0]['addons_category_list'],
					);
				}
			}
		}
		$cart_details = array(
			'cart_items' => $cartItems,
			'cart_total_price' => $cartTotalPrice,
		);
		return $cart_details;
	}
	// event booking page
	public function event_booking()
	{
        $data['page_title'] = $this->lang->line('online_reservation').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = 0; 
		$result = $this->restaurant_model->getAllRestaurants(8,$page);
		if (!empty($result['data']) && is_array($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['res_content_id']);
				$result['data'][$key]['ratings'] = $ratings;
			}
		}
		// all table booking restaurants
		$result1 = $this->restaurant_model->getAllTableRestaurants(8,$page);
		if (!empty($result1['data']) && is_array($result1['data'])) {
			foreach ($result1['data'] as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['res_content_id']);
				$result1['data'][$key]['ratings'] = $ratings;
			}
		}
		$data['restaurants'] = $result['data'];
		$count = count($data['restaurants']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url()."restaurant/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 8;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
        // table pagination
		$data['table_restaurants'] = $result1['data'];
		$count1 = count($data['table_restaurants']);
        $data['TotalRecord'] = $count1;
        $config = array();
        $config["base_url"] = base_url()."restaurant/event-booking";        
        $config["total_rows"] = $result1['count'];
        $config["per_page"] = 8;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['table_PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('event_booking',$data);
	}
	// ajax events page
	public function ajax_events()
	{
        $data['page_title'] = $this->lang->line('online_reservation').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
        $result = $this->restaurant_model->getAllRestaurants(8,$page,NULL,$this->input->post('searchEvent'));
		if (!empty($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['res_content_id']);
				$result['data'][$key]['ratings'] = $ratings;
				$review_data = $this->restaurant_model->getReviewsPagination($value['res_content_id'],review_count,1);
				$result['data'][$key]['restaurant_reviews'] = $review_data['reviews'];
				$result['data'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$data['restaurants'] = $result['data'];
		$count = count($data['restaurants']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "restaurant/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 8;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('ajax_events',$data);
	}
	// get restaurant dishes
	public function getResturantsDish(){
        $data['page_title'] = $this->lang->line('menu_details').' | '.$this->lang->line('site_title');
		$searchDish = array();
		if (!empty($this->input->post('searchDish'))) {
			$searchDish = explode(",", $this->input->post('searchDish'));
		}
		$content_id = $this->restaurant_model->getRestContentID($this->input->post('restaurant_id'));
		$data['restaurant_details'] = $this->restaurant_model->getRestaurantDetail($content_id->content_id,$searchDish,$this->input->post('food'),$this->input->post('price'),$this->input->post('availability'));
		$data['categories_count'] = count($data['restaurant_details']['categories']);
		if (!empty($data['restaurant_details']['restaurant'])) {
			$ratings = $this->restaurant_model->getRestaurantReview($data['restaurant_details']['restaurant'][0]['content_id']);
			$data['restaurant_details']['restaurant'][0]['ratings'] = $ratings;
			$review_data = $this->restaurant_model->getReviewsPagination($data['restaurant_details']['restaurant'][0]['content_id'],review_count,1);
			$data['restaurant_details']['restaurant'][0]['restaurant_reviews'] = $review_data['reviews'];
			$data['restaurant_details']['restaurant'][0]['restaurant_reviews_count'] = $review_data['review_count'];
		}
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$menu_arr = array();
		if (!empty($data['cart_details']['cart_items'])) {
			foreach ($data['cart_details']['cart_items'] as $key => $value) {
				$menu_arr[] = array(
					'menu_id' => $value['menu_id'],
					'quantity' => $value['quantity'],
				);
			}
		}
		$data['menu_arr'] = $menu_arr;
		//$this->load->view('search_menu_details',$data); //not in use
		$this->load->view('ajax_restaurant_detail',$data);
	}
	// event booking detail page
	public function event_booking_detail(){
        $data['page_title'] = $this->lang->line('event_booking').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$this->session->set_userdata('previous_url', current_url());
		$data['restaurant_details'] = array();
		if (!empty($this->uri->segment('3'))) {
			$content_id = $this->restaurant_model->getContentID($this->uri->segment('3'));
			$data['restaurant_details'] = $this->restaurant_model->getRestaurantDetail($content_id->content_id);
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency->currency_symbol;
			}else{
				$data['currency_symbol'] = $data['restaurant_details']['restaurant'][0]['currency_symbol'];
			}
			$data['categories_count'] = count($data['restaurant_details']['categories']);
			if (!empty($data['restaurant_details']['restaurant'])) {
				$ratings = $this->restaurant_model->getRestaurantReview($data['restaurant_details']['restaurant'][0]['content_id']);
				//$data['restaurant_reviews'] = $this->restaurant_model->getReviewsRatings($data['restaurant_details']['restaurant'][0]['content_id']);
				$review_data = $this->restaurant_model->getReviewsPagination($data['restaurant_details']['restaurant'][0]['content_id'],review_count,1);
				$data['restaurant_reviews'] = $review_data['reviews'];
				$data['restaurant_reviews_count'] = $review_data['review_count'];
				$data['restaurant_details']['restaurant'][0]['ratings'] = $ratings;
			}
		} 
	    //if($this->session->userdata('is_user_login') != 1) {
    	$this->session->unset_userdata('no_of_people');
    	$this->session->unset_userdata('booking_date');
    	$this->session->unset_userdata('dining_time');
    	$this->session->unset_userdata('date_time');
    	$this->session->unset_userdata('package_id');
        //}
        if($this->session->userdata('event_user_request')){
			$this->session->unset_userdata('event_user_request');
		}
		$this->load->view('event_booking_detail',$data); 
	}
	// checkEventAvailability
	public function checkEventAvailability(){ 
		if (!empty($this->input->post('no_of_people')) && !empty($this->input->post('date_time'))) {
			$booking_date = date("Y-m-d H:i:s",strtotime($this->input->post('date_time')));
			$restaurant_capacity = $this->restaurant_model->getRestaurantBookingCapacity($this->input->post('event_restaurant_id'));
			if($restaurant_capacity->allow_event_booking == 0){
				$response = array(
					'allow_event_booking' => 0,
					'allow_event_booking_text' => $this->lang->line('booking_not_allow_msg'),
					'oktxt' => $this->lang->line('ok'),
					'canceltxt' => $this->lang->line('cancel')
				);
				echo json_encode((object) $response); exit;
			}
			if($restaurant_capacity && $this->input->post('no_of_people') > $restaurant_capacity->capacity){
				$response = array(
					'less_capacity' => 'yes',
					'restaurant_capacity' => $restaurant_capacity->capacity
				);
				echo json_encode((object) $response); exit;
			}

			if($restaurant_capacity && $this->input->post('no_of_people') < $restaurant_capacity->event_minimum_capacity){
				$response = array(
					'more_capacity' => 'yes',
					'restaurant_capacity' => $restaurant_capacity->event_minimum_capacity
				);
				echo json_encode((object) $response); exit;
			}
			$check = $this->restaurant_model->getBookingAvailability($booking_date,$this->input->post('no_of_people'),$this->input->post('event_restaurant_id'));
			$date_value = date("Y-m-d",strtotime($this->input->post('date_time')));
			$time_value = date("H:i:s",strtotime($this->input->post('date_time')));
            //check availability data in session
            $this->session->set_userdata('no_of_people', $this->input->post('no_of_people'));
            $this->session->set_userdata('date_time', $this->input->post('date_time'));
			$this->session->set_userdata('booking_date', $date_value);
			$this->session->set_userdata('dining_time', $time_value);
			//user additional request
			if($this->input->post('user_comment') !=" "){
				$this->session->set_userdata('event_user_request', $this->input->post('user_comment'));
			}
			if($this->input->post('package_id') !=" "){
				$this->session->set_userdata('package_id', $this->input->post('package_id'));
			}
			if ($check) {
				// echo 'success';
				$response = array(
					'result' => 'success'
				);
				echo json_encode((object) $response); exit;
			}
			else
			{
				// echo 'fail';
				$response = array(
					'result' => 'fail'
				);
				echo json_encode((object) $response); exit;
			}
			exit;
		}
		else
		{
			$response = array(
				'incorrect_info' => 1,
				'show_message' => $this->lang->line('booking_invalid_input'),
				'oktxt' => $this->lang->line('ok')
			);
			echo json_encode((object) $response); exit;
		}
	}
	// add package item to book event
	public function add_package(){
		if ($this->input->post('action') == "add") {
			$this->session->unset_userdata('package_id');
			$this->session->set_userdata('package_id', $this->input->post('entity_id'));
			echo 'success';
		}
		else
		{
			$this->session->set_userdata('package_id','');
			echo 'success';
		}
		exit;
	}
	// book event
	public function bookEvent()
	{
		if($this->input->post('date_time') != '' && $this->input->post('no_of_people') != ''){
			$restaurant = $this->restaurant_model->getBookingRestaurant($this->input->post('event_restaurant_id'));
			if($restaurant->allow_event_booking == 0){
				$response = array(
					'allow_event_booking' => 0,
					'allow_event_booking_text' => $this->lang->line('booking_not_allow_msg'),
					'oktxt' => $this->lang->line('ok'),
					'canceltxt' => $this->lang->line('cancel')
				);
				echo json_encode((object) $response); exit;
			}
			$booking_date = date("Y-m-d H:i:s",strtotime($this->input->post('date_time')));
            $booking_date = $this->common_model->setZonebaseDateTime($booking_date);
            $add_data = array(                   
                'name'=>$this->input->post('event_name'),
                'no_of_people'=>$this->input->post('no_of_people'),
                'booking_date'=>$booking_date,
                'restaurant_id'=>$this->input->post('event_restaurant_id'),
                'user_id'=>$this->input->post('event_user_id'),
                'package_id'=>(!empty($this->input->post('package_id'))) ? $this->input->post('package_id') : '',
                'status'=>1,
                'created_by' => $this->input->post('event_user_id'),
                'event_status'=>'pending'
            ); 
            //add data for additional request
            if($this->input->post('user_comment') != " "){
            	$add_data['additional_request'] = $this->input->post('user_comment');
            }
            $event_id = $this->common_model->addData('event',$add_data);
            //Code for session update after done the booking :: Start
            $this->session->unset_userdata('no_of_people'); 
            $this->session->unset_userdata('date_time'); 
            $this->session->unset_userdata('booking_date'); 
            $this->session->unset_userdata('dining_time'); 
            if($this->session->userdata('event_user_request')){
				$this->session->unset_userdata('event_user_request');
			}
            //Code for session update after done the booking :: End
            $users = array(
                'first_name'=>$this->session->userdata('userFirstname'),
                'last_name'=>($this->session->userdata('userLastname'))?$this->session->userdata('userLastname'):''
            );
            $taxdetail = $this->restaurant_model->getRestaurantTax('restaurant',$this->input->post('event_restaurant_id'),$flag="order");
            if(!empty($this->session->userdata('package_id'))) {
            	$package_id =$this->restaurant_model->getEventContentID($this->session->userdata('package_id'));
            	$package = $this->common_model->getSingleRow('restaurant_package','entity_id',$package_id->entity_id);
            }
            
            $package_detail = '';
            if(!empty($package)){
                $package_detail = array(
                    'package_price'=>$package->price,
                    'package_name'=>$package->name,
                    'package_detail'=>$package->detail,
                    'package_image'=>$package->image,
                );
            }
            $serialize_array = array(
                'restaurant_detail'=>(!empty($taxdetail))?serialize($taxdetail):'',
                'user_detail'=>(!empty($users))?serialize($users):'',
                'package_detail'=>(!empty($package_detail))?serialize($package_detail):'',
                'event_id'=>$event_id
            );
            $this->session->unset_userdata('package_id'); 
            $this->common_model->addData('event_detail',$serialize_array); 
            $response = array(
				'result' => 'success'
			);
			echo json_encode((object) $response); exit;
        }else{
			$response = array(
				'incorrect_info' => 1,
				'show_message' => $this->lang->line('booking_invalid_input'),
				'oktxt' => $this->lang->line('ok')
			);
			echo json_encode((object) $response); exit;
		}
	}
	// get Favourite Resturants
	public function getFavouriteResturants(){
        $data['page_title'] = $this->lang->line('fav_restaurants').' | '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		$page = 0; 
		$result = $this->restaurant_model->getAllRestaurants(6,$page);
		if (!empty($result['data'])) {
			foreach ($result['data'] as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['res_content_id']);
				$result['data'][$key]['ratings'] = $ratings;
				$review_data = $this->restaurant_model->getReviewsPagination($value['content_id'],review_count,1);
				$result['data'][$key]['restaurant_reviews'] = $review_data['reviews'];
				$result['data'][$key]['restaurant_reviews_count'] = $review_data['review_count'];
			}
		}
		$data['restaurants'] = $result['data'];
		$count = count($data['restaurants']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "restaurant/event-booking";        
        $config["total_rows"] = $result['count'];
        $config["per_page"] = 6;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');               
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('event_booking',$data);
	}
	// get add ons
	public function getCustomAddOns()
	{
		$data['old_cartqty'] = 1;
		$data['page_title'] = $this->lang->line('custom_addons').' | '.$this->lang->line('site_title');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			$data['result'] = $this->restaurant_model->getMenuItem($this->input->post('entity_id'),$this->input->post('restaurant_id'));
			//get System Option Data
	      /*  $this->db->select('OptionValue');
	        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
	        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency;
			}else{
				$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));
			}

			/*$count_list= count($data['result'][0]['items'][0]['addons_category_list']);
			$i=0;
			while($i<$count_list){
				$limit = $data['result'][0]['items'][0]['addons_category_list'][$i]['addons_list'][0]['display_limit'];
				if(!empty($limit)){
					array_splice($data['result'][0]['items'][0]['addons_category_list'][$i]['addons_list'],$limit);
				}
				$i++;
			}*/
			if($this->input->post('reload_page')){
				$data['from_checkout'] = $this->input->post('reload_page');
			}else{
				$data['from_checkout'] ='';
			}
			$this->load->view('ajax_custom_items',$data); 
		}
	}
	// add review
	public function addReview(){
		$add_data = array(                   
	        'restaurant_id'=>$this->input->post('review_restaurant_id'),
	        'user_id'=>$this->input->post('review_user_id'),
	        'review'=>utf8_encode($this->input->post('review_text')),
	        'rating'=>$this->input->post('rating'),
	        'order_id'=>$this->input->post('review_order_id'),
	        'status'=>1,
	        'created_by' => $this->input->post('review_user_id'),
	        'created_date' => date('Y-m-d H:i:s'),
	        'restaurant_content_id'=>$this->input->post('review_res_content_id'),
	    ); 
	    $review_id = $this->common_model->addData('review',$add_data); 
	    //$this->session->set_flashdata('review_added', $this->lang->line('review_added'));
	    $_SESSION['review_added'] = $this->lang->line('review_added');
	    echo 'success';
	}
	//new changes for menu details on image click :: start
	public function getCustomAddOnsDetails(){
		$data['page_title'] = $this->lang->line('custom_addons').' | '.$this->lang->line('site_title');
		$data['is_closed'] = $this->input->post('is_closed');
		$data['cart_rest'] = 0;
		$data['old_cartqty'] = 1;
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			$data['result'] = $this->restaurant_model->getMenuItem($this->input->post('entity_id'),$this->input->post('restaurant_id'));
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency;
			}else{
				$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));
			}
			$data['restaurant_id'] = $this->input->post('restaurant_id');

			/*$count_list= count($data['result'][0]['items'][0]['addons_category_list']);
			$i=0;
			while($i<=$count_list){
				$limit = $data['result'][0]['items'][0]['addons_category_list'][$i]['addons_list'][0]['display_limit'];
				if(!empty($limit)){
					array_splice($data['result'][0]['items'][0]['addons_category_list'][$i]['addons_list'],$limit);
				}
				$i++;
			}*/
			$cart_details = get_cookie('cart_details');
			$cart_restaurant = get_cookie('cart_restaurant');
			if ($cart_restaurant == $this->input->post('restaurant_id')) {
				if (!empty(json_decode($cart_details))) {
					foreach (json_decode($cart_details) as $key => $value) {
						if ($value->menu_id == $this->input->post('entity_id')) {
							$data['cart_rest'] = 1;
							$data['old_cartqty'] = $value->quantity;
						}
					}
				}
			}
			if($this->input->post('recipe')=='no'){
				$data['recipe_name'] = $this->restaurant_model->getRecipe_page($data['result'][0]['items'][0]['menu_id']);
			}
			if($this->input->post('recipe_page')=='recipe'){
				$data['recipe_page'] =$this->input->post('recipe_page');
			}else if($this->input->post('recipe_page')=='checkout_as_guest'){
				$data['recipe_page'] =$this->input->post('recipe_page');
			}
			$this->load->view('ajax_custom_items_details',$data); 
		}
	}
	//new changes for menu details on image click :: end
	//view recipe
	public function viewRecipe(){
		if($this->input->post('menu_item_id')!=''){
			$recipe_name = $this->restaurant_model->getRecipe_page($this->input->post('menu_item_id'));
		}
	}
	public function getReviewsPagination(){
		$page_no = $this->input->post('page_no');
		$count = review_count;
		$restaurant_content_id = $this->input->post('restaurant_content_id');
	    $data = $this->restaurant_model->getReviewsPagination($restaurant_content_id,$count,$page_no);
	    $html = '';
	    if(!empty($data['reviews'])) {
		    foreach ($data['reviews'] as $key => $value) {
		    	$user_img = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_icon_img;
			    $html .= '<input type="hidden" class="reviewid" id="'.$key.'">
			    		<div class="review-list">
			    			<div class="review-content">
								<div class="user-name-date">
									<div class="review-date">
										<h3>'.$value['first_name'].' '.$value['last_name'].'</h3>
										<span>'.$this->common_model->dateFormat($value['created_date']).'</span>
									</div>
									<div class="review-star">
										<span><i class="iicon-icon-05"></i>'. number_format($value['rating'],1).'</span>
									</div>
								</div>
								<p>"'.ucfirst($value['review']).'"</p>
							</div>
			    		</div>';
			}
		}
		$resp_arr = array('review_html'=> $html,'next_page_count'=>$data['next_page_count']);
		echo json_encode($resp_arr);
	}
	// ajax table booking page
	public function ajax_table_booking()
	{
        $data['page_title'] = $this->lang->line('online_reservation').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'TableBooking';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
        $result1 = $this->restaurant_model->getAllTableRestaurants(8,$page,NULL,$this->input->post('searchTable'));
		if (!empty($result1['data']) && is_array($result1['data'])) {
			foreach ($result1['data'] as $key => $value) {
				$ratings = $this->restaurant_model->getRestaurantReview($value['res_content_id']);
				$result1['data'][$key]['ratings'] = $ratings;
			}
		}
		$data['table_restaurants'] = $result1['data'];
		$count = count($data['table_restaurants']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url()."restaurant/event-booking";        
        $config["total_rows"] = $result1['count'];
        $config["per_page"] = 8;
        $config['first_link'] = $this->lang->line('first');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = $this->lang->line('last');
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = $this->lang->line('next');
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = $this->lang->line('previous');
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';        
        $config['cur_tag_open'] = '<li class="active"><a class="active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="table_pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['uri_segment'] = 3;
        $this->ajax_pagination->initialize($config);
        $data['table_PaginationLinks'] = $this->ajax_pagination->create_links(); 
		$this->load->view('ajax_events',$data);
	}
	// table booking detail page
	public function table_booking_detail(){
        $data['page_title'] = $this->lang->line('table_booking').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'EventBooking';
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$this->session->set_userdata('previous_url', current_url());
		$data['restaurant_details'] = array();
		if (!empty($this->uri->segment('3'))) {
			$content_id = $this->restaurant_model->getContentID($this->uri->segment('3'));
			$data['restaurant_details'] = $this->restaurant_model->getRestaurantDetail($content_id->content_id);
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency->currency_symbol;
			}else{
				$data['currency_symbol'] = $data['restaurant_details']['restaurant'][0]['currency_symbol'];
			}
			$data['categories_count'] = count($data['restaurant_details']['categories']);
			if (!empty($data['restaurant_details']['restaurant'])) {
				$ratings = $this->restaurant_model->getRestaurantReview($data['restaurant_details']['restaurant'][0]['content_id']);
				//$data['restaurant_reviews'] = $this->restaurant_model->getReviewsRatings($data['restaurant_details']['restaurant'][0]['content_id']);
				$review_data = $this->restaurant_model->getReviewsPagination($data['restaurant_details']['restaurant'][0]['content_id'],review_count,1);
				$data['restaurant_reviews'] = $review_data['reviews'];
				$data['restaurant_reviews_count'] = $review_data['review_count'];
				$data['restaurant_details']['restaurant'][0]['ratings'] = $ratings;
			}
		} 
	    //if($this->session->userdata('is_user_login') != 1) {
    	$this->session->unset_userdata('no_of_people');
    	$this->session->unset_userdata('booking_date');
    	$this->session->unset_userdata('start_time');
    	$this->session->unset_userdata('end_time');
    	$this->session->unset_userdata('user_request');
        //}
        if($this->session->userdata('user_request')){
			$this->session->unset_userdata('user_request');
		}
		$this->db->where('entity_id',$this->session->userdata('UserID'));
		$this->db->where("(user_type='User')");
		$val = $this->db->get('users')->first_row();
		$this->session->set_userdata('phone_codeval',$val->phone_code);
		$this->load->view('table_booking_detail',$data); 
	}
	// checkTableAvailability
	public function checkTableAvailability(){ 
		if (!empty($this->input->post('no_of_people')) && !empty($this->input->post('starttime')) && !empty($this->input->post('endtime'))) {
			$booking_date =  date_format(date_create($this->input->post('datepicker')),"y-m-d");
			$start_time = $this->input->post('starttime');
			$end_time= $this->input->post('endtime');

			if(date('H:i',strtotime($start_time)) >= date('H:i',strtotime($end_time))) {
				$start_time_less = "<p>".$this->lang->line('start_less_than_end_time')."</p>";
				$response = array(
					'start_time_less'=> '1',
					'start_time_less_html'=>$start_time_less
				);
				echo json_encode((object) $response); exit;
			}
			$restaurant_capacity = $this->restaurant_model->getRestaurantBookingCapacity($this->input->post('table_restaurant_id'));
			if($restaurant_capacity->enable_table_booking == 0){
				$response = array(
					'allow_table_booking' => 0,
					'allow_table_booking_text' => $this->lang->line('booking_not_allow_msg'),
					'oktxt' => $this->lang->line('ok'),
					'canceltxt' => $this->lang->line('cancel')
				);
				echo json_encode((object) $response); exit;
			}
			if($restaurant_capacity && $this->input->post('no_of_people') < $restaurant_capacity->table_minimum_capacity){
				$response = array(
					'less_capacity' => 'yes',
					'restaurant_capacity' => $restaurant_capacity->table_minimum_capacity
				);
				echo json_encode((object) $response); exit;
			}
			if($restaurant_capacity && $this->input->post('no_of_people') > $restaurant_capacity->table_booking_capacity){
				$response = array(
					'more_capacity' => 'yes',
					'restaurant_capacity' => $restaurant_capacity->table_booking_capacity
				);
				echo json_encode((object) $response); exit;
			}
			$check = $this->restaurant_model->getTableBookingAvailability($booking_date,$start_time,$end_time,$this->input->post('no_of_people'),$this->input->post('table_restaurant_id'));
			$date_value = $this->input->post('datepicker');
			$start_time_value = $this->input->post('starttime');
			$end_time_value = $this->input->post('endtime');
            //check availability data in session
            $this->session->set_userdata('no_of_people', $this->input->post('no_of_people'));
            $this->session->set_userdata('start_time', $start_time_value);
            $this->session->set_userdata('end_time', $end_time_value);
			$this->session->set_userdata('booking_date', $date_value);
			if($this->input->post('user_comment') != " "){
				$this->session->set_userdata('user_request', $this->input->post('user_comment'));
			}
			if ($check) {
				$response = array('result' => 'success');
				echo json_encode((object) $response); exit;
			} else {
				$response = array('result' => 'fail');
				echo json_encode((object) $response); exit;
			}
			exit;
		}
		else
		{
			$response = array(
				'incorrect_info' => 1,
				'show_message' => $this->lang->line('booking_invalid_input'),
				'oktxt' => $this->lang->line('ok')
			);
			echo json_encode((object) $response); exit;
		}
	}
	// book table
	public function bookTable()
	{
		if($this->input->post('datepicker') != '' && $this->input->post('no_of_people') != ''){
			$restaurant = $this->restaurant_model->getBookingRestaurant($this->input->post('table_restaurant_id'));
			if($restaurant->enable_table_booking == 0){
				$response = array(
					'allow_table_booking' => 0,
					'allow_table_booking_text' => $this->lang->line('booking_not_allow_msg'),
					'oktxt' => $this->lang->line('ok'),
					'canceltxt' => $this->lang->line('cancel')
				);
				echo json_encode((object) $response); exit;
			}
			$date = date('Y-m-d',strtotime($this->input->post('datepicker')));
			$new_end_time = date('H:i',strtotime($this->common_model->setZonebaseTime($this->input->post('endtime'))));
			$new_start_time = date('H:i',strtotime($this->common_model->setZonebaseTime($this->input->post('starttime'))));
            $add_data = array(                   
                'user_name'=>$this->input->post('table_name'),
                'no_of_people'=>$this->input->post('no_of_people'),
                'booking_date'=>$date,
                'restaurant_content_id'=>$this->input->post('table_restaurant_id'),
                'user_id'=>$this->input->post('table_user_id'),
                'booking_status'=>'awaiting',
                'start_time'=>$new_start_time,
                'end_time'=>$new_end_time,
                'created_by' => $this->input->post('table_user_id'),
            ); 
            if($this->input->post('user_comment') != " "){
            	$add_data['additional_request'] = $this->input->post('user_comment');
            }
            $table_id = $this->common_model->addData('table_booking',$add_data);
            //Code for session update after done the booking :: Start
            $this->session->unset_userdata('no_of_people'); 
            $this->session->unset_userdata('start_time'); 
            $this->session->unset_userdata('booking_date'); 
            $this->session->unset_userdata('end_time'); 
            if($this->session->userdata('user_request')){
				$this->session->unset_userdata('user_request');
			}
            //Code for session update after done the booking :: End 
            $response = array(
				'result' => 'success'
			);
			echo json_encode((object) $response); exit;
        }else{
        	$response = array(
				'incorrect_info' => 1,
				'show_message' => $this->lang->line('booking_invalid_input'),
				'oktxt' => $this->lang->line('ok')
			);
			echo json_encode((object) $response); exit;
		}
	}
	public function getTimeSlot(){
		if($this->input->post('is_date_changed')!=1){
			$result = $this->restaurant_model->getRestaurantTimings($this->input->post('restaurant_id'),$this->input->post('event_date'));
			$start_datetime = new DateTime(date('G:i',strtotime($result[0]['timings']['open'])));
        	$end_datetime = new DateTime(date('G:i',strtotime($result[0]['timings']['close'])));
        	$time_slots = $this->common_model->getTimeSlots(TIME_INTERVAL, $start_datetime->format('H:i'), $end_datetime->format('H:i'));
		}else{
			$result = $this->restaurant_model->getRestaurantTimings($this->input->post('restaurant_id'),$this->input->post('event_date'));
			$start_time = ($this->input->post('start_time') != '')?$this->input->post('start_time'):'';
			$end_time = ($this->input->post('end_time') != '')?$this->input->post('end_time'):'';

			if($this->input->post('start_end_flag') == 'is_start'){
				$start_time_value = date('H:i',strtotime('+'.TIME_INTERVAL,strtotime($start_time)));
				$end_time_value = date('H:i',strtotime($end_time));
			} else {
				$start_time_value = date('H:i',strtotime($start_time));
				$end_time_value = date('H:i',strtotime('-'.TIME_INTERVAL,strtotime($end_time)));
			}
			$time_slots = $this->common_model->getTimeSlots(TIME_INTERVAL, $start_time_value, $end_time_value);
		}
		$selected_start_time = ($this->input->post('selected_start_time') != '')?$this->input->post('selected_start_time'):'';
		$selected_end_time = ($this->input->post('selected_end_time') != '')?$this->input->post('selected_end_time'):'';
	    
	    $start_time_html = '';
	    $end_time_html = '';
	    $arry_size = sizeof($time_slots);
	    foreach ($time_slots as $key => $value) {
	        if($this->input->post('event_date') && $start_time == ''){
	        	$selected = ($key==$arry_size-1) ? 'selected' : "";
	        	$end_time_html .= '<option value="'.$value.'"'.$selected.'>'.$this->common_model->timeFormat($value).'</option>';
	        	$start_time_html .= '<option value="'.$value.'">'.$this->common_model->timeFormat($value).'</option>';
	        }else if($start_time){
	        	if($this->input->post('start_end_flag') == 'is_start'){
	        		$selected = ($selected_end_time)?$selected_end_time:'';
	        	} else {
	        		$selected = ($selected_start_time)?$selected_start_time:'';
	        	}
	        	$select_item = ($value == $selected)?'selected':'';
	        	if($selected==''){
	        		$select_item = (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('end_time') == $value)) ? 'selected' : "";
	        	}
	        	$end_time_html .= '<option value="'.$value.'"'.$select_item.'>'.$this->common_model->timeFormat($value).'</option>';
	        }
	    }
	    $array_view = array(
			'start_time_html'=>$start_time_html, //used when date is selected.
			'end_time_html'=>$end_time_html
		);
		echo json_encode($array_view);
    }
    //Code for fetch restaurant package :: Start
    public function show_restaurantpackage()
    {
    	$html = '';
    	$content_id = $this->input->post('content_id')?$this->input->post('content_id'):'';
    	if($content_id!='' && intval($content_id)>0)
    	{
    		$currency_symbol = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));    		
			if(!empty($currency_symbol)){
				$currency_symbol = $currency_symbol;
			}else{
				$currency_symbol = get_default_system_currency();		
			}
			$currency_symbol = isset($currency_symbol->currency_symbol) ? $currency_symbol->currency_symbol : '';

    		$package_id =$this->restaurant_model->getEventContentID($this->session->userdata('package_id'));
            $package = $this->restaurant_model->getPackageDetail($content_id);
            //echo "<pre>"; print_r($package); exit;
            if($package && !empty($package))
            {
            	$image_path = (file_exists(FCPATH.'uploads/'.$package->image) && $package->image!='')?image_url.$package->image:default_icon_img;
            	$html .= '<div class="detail-list">
                            <div class="detail-list-img">
					            <div class="list-img">
					                <img src="'.$image_path.'" alt="'.$package->name.'" title="'.$package->name.'" >
					            </div>
					        </div>
                            <div class="detail-list-content"> 
                                <div class="detail-list-text">
                                    <h4>'.$package->name.'</h4>
                                    <p>'.$package->detail.'</p>
                                    <strong>'.currency_symboldisplay($package->price,$currency_symbol).'</strong>
                                </div>
                            </div>
                        </div>';
            }
    	}
    	
		$resp_arr = array('package_html'=> $html);
		echo json_encode($resp_arr);
    }
    //Code for fetch restaurant package :: End
    public function addBookmark(){
    	$addData = array(
    		'restaurant_id' => $this->input->post('restaurant_id'),
    		'user_id' => $this->session->userdata('UserID')
    	);
    	$this->restaurant_model->addBookmark($addData);
    }
    public function restaurant_error_report()
    {
    	$this->load->library('form_validation');
    	$this->form_validation->set_rules('email_address',$this->lang->line('email'), 'trim|required|valid_email');
		$this->form_validation->set_rules('message',$this->lang->line('message'), 'trim|required');
		if (!$this->form_validation->run()) {
			$response = array(
				'error' => 1,
				'message' => validation_errors()
			);
			echo json_encode($response);exit;
		}
        $report_topic = ($this->input->post('report_topic')) ? $this->input->post('report_topic') : '';
        $reporter_email = $this->input->post('email_address');
        $reporter_message = $this->input->post('message');
        $add_data = array(
        	'report_topic' => ($report_topic != '') ? implode(',', $report_topic) :'',
        	'reporter_email' => $reporter_email,
        	'reporter_message' => $reporter_message
        );
        $result = $this->restaurant_model->store_restaurant_error_report($add_data);
        if(!is_null($result)){
        	$response = array(
				'success' => 1,
				'message' => $this->lang->line('report_error_success')
			);
			echo json_encode($response); exit;
        }else{
        	$response = array(
				'success' => 0,
				'message' => $this->lang->line('report_error_fail')
			);
			echo json_encode($response); exit;
        }
    }
}
?>
