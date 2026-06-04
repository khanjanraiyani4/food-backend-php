<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/restaurant_model');      
		$this->load->model('/cart_model');
	}
	// index function
	public function index()
	{
		$data['current_page'] = 'Cart';
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		$this->session->set_userdata('previous_url', current_url());
		$cart_details = get_cookie('cart_details');
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['restaurant_data'] = $this->common_model->checkResForCart($data['cart_restaurant']);
		$data['cart_details'] = $this->getCartItems($cart_details,$data['cart_restaurant']);
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
		}
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
        $data['allow_scheduled_delivery'] = $this->restaurant_model->getResAllowSchedulingFlag($data['cart_restaurant']);
		$this->load->view('cart',$data);
	}
	// checkout page
	public function checkout()
	{
		$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($cart_details,$cart_restaurant);
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
			$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		}
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$this->load->view('checkout',$data);
	}
	
	// add to cart
	public function addToCart()
	{
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		$qtyval = $this->input->post('qtyval')?$this->input->post('qtyval'):1;
		//if (!empty($this->input->post('menu_id')) && !empty($this->input->post('add_ons_array'))) {
		if (!empty($this->input->post('menu_id'))) {
			$itemArray = array();
			$data['another_restaurant'] = '';
			$menuDetails = $this->restaurant_model->getMenuItem($this->input->post('menu_id'),$this->input->post('restaurant_id'));
			foreach ($menuDetails as $key => $value) {
				$itemArray['name'] = $value['items'][0]['name'];
				$itemArray['image'] = $value['items'][0]['image'];
				$itemArray['menu_id'] = $value['items'][0]['menu_id'];
				$itemArray['price'] = $value['items'][0]['price'];
				$itemArray['offer_price'] = $value['items'][0]['offer_price'];
				$itemArray['is_veg'] = $value['items'][0]['is_veg_food'];
				$itemArray['is_customize'] = $value['items'][0]['is_customize'];
				$itemArray['is_deal'] = $value['items'][0]['is_deal'];
				$itemArray['availability'] = $value['items'][0]['availability'];
			}
			$itemArray['restaurant_id'] = $this->input->post('restaurant_id');
			$itemArray['itemTotal'] = $this->input->post('totalPrice');
			$itemArray['addons_category_list'] = $this->input->post('add_ons_array');
			$addons = array();
			if (!empty($itemArray) && is_array($itemArray)) {
				if (!empty($itemArray['addons_category_list']) && is_array($itemArray['addons_category_list'])) { 
					foreach ($itemArray['addons_category_list'] as $key => $value) {
						if (!empty($value['addons_list'])) {
							if (is_array(reset($value['addons_list']))) {
								foreach ($value['addons_list'] as $key => $addvalue) {
									$addons[] = array(
										'addons_category_id'=> $value['addons_category_id'],
										'add_onns_id' => $addvalue['add_ons_id']
									);
								}
							}
							else
							{
								$addons[] = array(
									'addons_category_id'=> $value['addons_category_id'],
									'add_onns_id' => $value['addons_list']['add_ons_id']
								);
							}
						}
					}
				}
				$cart_details = get_cookie('cart_details');
				$cart_restaurant = get_cookie('cart_restaurant');
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
			            $oldcookie = $value;
			 			$arrayDetails[] = $oldcookie;
				 	}
				} 
				if (empty($cookie)) {
					$cookie = array(
			            'menu_id'   => (!empty($itemArray['menu_id'])) ? $itemArray['menu_id'] : '', 
		            	'quantity' => $qtyval,//1, 
			            'addons'  => $addons,          
		            );
				}
	            $arrayDetails[] = $cookie;
				if (empty($cart_details) && empty($cart_restaurant)) { 
		            $this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else if ($cart_restaurant == $this->input->post('restaurant_id')) { 
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else
				{
					$data['another_restaurant'] = 'AnotherRestaurant';
					$data['cart_details'] = get_cookie('cart_details');
					$data['cart_restaurant'] = get_cookie('cart_restaurant');
				}
			}
		}  
		if (!empty($this->input->post('menu_item_id'))) {
			$cart_details = get_cookie('cart_details');
			$cart_restaurant = get_cookie('cart_restaurant');
			$arrayDetails = array();
			
			if (!empty(json_decode($cart_details))) {
				foreach (json_decode($cart_details) as $key => $value) {
		            if ($value->menu_id == $this->input->post('menu_item_id')) {
						$cookie = array(
				            'menu_id'   => $this->input->post('menu_item_id'),  
				            'quantity' => $qtyval,//($value->quantity)?($value->quantity+$qtyval):$qtyval,
				            'addons'  => '',               
			            );
		            }
		            else
		            {
		            	$oldcookie = $value;
			 			$arrayDetails[] = $oldcookie;
		            }
				}
			} 
			if (empty($cookie)) {
				$cookie = array(
		            'menu_id'   => $this->input->post('menu_item_id'), 
		            'quantity' => $qtyval, //1,
		            'addons'  => '',               
	            );
			}
            $arrayDetails[] = $cookie;
			if (empty($cart_details) && empty($cart_restaurant)) { 
	            $this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
	            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
	            $data['cart_details'] = $this->getcookie('cart_details');
				$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			}
			else if ($cart_restaurant == $this->input->post('restaurant_id')) { 
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
	            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
	            $data['cart_details'] = $this->getcookie('cart_details');
				$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			}
			else
			{	
				$data['another_restaurant'] = 'AnotherRestaurant';
				$data['cart_details'] = get_cookie('cart_details');
				$data['cart_restaurant'] = get_cookie('cart_restaurant');
			}
		}
		//new changes for menu details on image click :: start
		if (!empty($this->input->post('menu_id_m'))) {
			$itemArray = array();
			$data['another_restaurant'] = '';
			$menuDetails = $this->restaurant_model->getMenuItem($this->input->post('menu_id_m'),$this->input->post('restaurant_id'));
			foreach ($menuDetails as $key => $value) {
				$itemArray['name'] = $value['items'][0]['name'];
				$itemArray['image'] = $value['items'][0]['image'];
				$itemArray['menu_id_m'] = $value['items'][0]['menu_id'];
				$itemArray['price'] = $value['items'][0]['price'];
				$itemArray['offer_price'] = $value['items'][0]['offer_price'];
				$itemArray['is_veg'] = $value['items'][0]['is_veg'];
				$itemArray['is_customize'] = $value['items'][0]['is_customize'];
				$itemArray['is_deal'] = $value['items'][0]['is_deal'];
				$itemArray['availability'] = $value['items'][0]['availability'];
			}
			$itemArray['restaurant_id'] = $this->input->post('restaurant_id');
			$itemArray['itemTotal'] = $this->input->post('totalPrice');
			$itemArray['addons_category_list'] = (!empty($this->input->post('add_ons_array')))?$this->input->post('add_ons_array'):'';
			$addons = array();
			if (!empty($itemArray) && is_array($itemArray)) {
				if (!empty($itemArray['addons_category_list']) && is_array($itemArray['addons_category_list'])) { 
					foreach ($itemArray['addons_category_list'] as $key => $value) {
						if (!empty($value['addons_list'])) {
							if (is_array(reset($value['addons_list']))) {
								foreach ($value['addons_list'] as $key => $addvalue) {
									$addons[] = array(
										'addons_category_id'=> $value['addons_category_id'],
										'add_onns_id' => $addvalue['add_ons_id']
									);
								}
							}
							else
							{
								$addons[] = array(
									'addons_category_id'=> $value['addons_category_id'],
									'add_onns_id' => $value['addons_list']['add_ons_id']
								);
							}
						}
					}
				}
				$cart_details = get_cookie('cart_details');
				$cart_restaurant = get_cookie('cart_restaurant');
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
			            $oldcookie = $value;
			 			$arrayDetails[] = $oldcookie;
				 	}
				} 
				if (empty($cookie)) {
					$cookie = array(
			            'menu_id'   => $itemArray['menu_id_m'], 
		            	'quantity' => $qtyval,//1, 
			            'addons'  => ($addons)?$addons:'',          
		            );
				}
	            $arrayDetails[] = $cookie;
				if (empty($cart_details) && empty($cart_restaurant)) { 
		            $this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else if ($cart_restaurant == $this->input->post('restaurant_id')) { 
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
		            $data['cart_details'] = $this->getcookie('cart_details');
					$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
				}
				else
				{
					$data['another_restaurant'] = 'AnotherRestaurant';
					$data['cart_details'] = get_cookie('cart_details');
					$data['cart_restaurant'] = get_cookie('cart_restaurant');
				}
			}
		}
		//new changes for menu details on image click :: end
		
		$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
		//get System Option Data
        /*$this->db->select('OptionValue');
        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
		$default_currency = get_default_system_currency();
		if(!empty($default_currency)){
			$data['currency_symbol'] = $default_currency;
		}else{
        	$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
        }
        $data['order_mode'] = $this->restaurant_model->getRestaurantOrderMode($this->input->post('restaurant_id'));
        $data['allow_scheduled_delivery'] = $this->restaurant_model->getResAllowSchedulingFlag($this->input->post('restaurant_id'));
        $this->load->view('ajax_your_cart',$data);
	}
	// get Cart items
	public function getCartItems($cart_details,$cart_restaurant){
		$cartItems = array();
		$cartTotalPrice = 0;
		$addons_not_available_gl = 0;
		$cart_detailsarr = array();
		if(isset($cart_details)){
			$cart_detailsarr = (json_decode($cart_details))?json_decode($cart_details):array();
		}
		//exit;
		if (!empty($cart_detailsarr) && is_array($cart_detailsarr)) {
			foreach ($cart_detailsarr as $key => $value) { 
				$addons_not_available = 0;
				$details = $this->restaurant_model->getMenuItem($value->menu_id,$cart_restaurant);
				if (!empty($details)) {
					if ($details[0]['items'][0]['is_customize'] == 1) {
						$addons_category_id = $add_onns_id = array();
						if($value->addons && !empty($value->addons)){
							$addons_category_id = array_column($value->addons, 'addons_category_id');
							$add_onns_id = array_column($value->addons, 'add_onns_id');
						}
						
						if (!empty($details[0]['items'][0]['addons_category_list']) && is_array($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
									unset($details[0]['items'][0]['addons_category_list'][$key]);
								}
								else
								{
									if (!empty($cat_value['addons_list']) && is_array($cat_value['addons_list'])) {
										foreach ($cat_value['addons_list'] as $addkey => $add_value) {
											if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
												unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
											}
										}
									}
								}
							}
						}
						//re-order changes :: start
						//remove addons category if no addons id available in that.
						if (!empty($details[0]['items'][0]['addons_category_list']) && is_array($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (empty($cat_value['addons_list'])) {
									unset($details[0]['items'][0]['addons_category_list'][$key]);
									$addons_not_available = 1;
									$addons_not_available_gl = 1;
								}
							}
						}
						//re-order changes :: end
					}
					// getting subtotal
					if ($details[0]['items'][0]['is_customize'] == 1) 
					{	$subtotal = 0;
						$offer_price = str_replace(",", "", $details[0]['items'][0]['offer_price']);
						if (!empty($details[0]['items'][0]['addons_category_list']) && is_array($details[0]['items'][0]['addons_category_list'])) {
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
						if ($details[0]['items'][0]['is_deal'] == 1) {
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						else
						{
							//$price = ($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0;
							$price = ($details[0]['items'][0]['offer_price'])?$details[0]['items'][0]['offer_price']:(($details[0]['items'][0]['price'])?$details[0]['items'][0]['price']:0);
						}
						$mprice = str_replace(",","",$price);
						$subtotal = $subtotal + $mprice;
					}
					$cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
					if($addons_not_available != 1){
						$cartItems[] = array(
							'menu_id' => $details[0]['items'][0]['menu_id'],
							'restaurant_id' => $cart_restaurant,
							'name' => $details[0]['items'][0]['name'],
							'menu_detail' => $details[0]['items'][0]['menu_detail'],
							'is_combo_item' => $details[0]['items'][0]['is_combo_item'],
							'quantity' => $value->quantity,
							'comment' => ($value->comment)?$value->comment:'',
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
		}
		$cart_details = array(
			'cart_items' => $cartItems,
			'cart_total_price' => $cartTotalPrice,
			'addons_not_available'=>$addons_not_available_gl,
		);
		return $cart_details;
	}
	// get the cookies
	public function getcookie($name) { 
	    $cookies = [];
	    $headers = headers_list(); 
	    foreach($headers as $key => $header) { 
	        if (strpos($header, 'Set-Cookie: ') === 0) {
	            $value = str_replace('&', urlencode('&'), substr($header, 12));
	            parse_str(current(explode(';', $value)), $pair);
	            $cookies = array_merge_recursive($cookies, $pair);
	        }
	    }
	    return $cookies[$name];
	}
	public function checkMenuItem()
	{
		$menuItemExist = 0;
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) { 
			$cart_details = get_cookie('cart_details');
			$cart_restaurant = get_cookie('cart_restaurant');
			if ($cart_restaurant == $this->input->post('restaurant_id')) {
				if (!empty(json_decode($cart_details))) {
					foreach (json_decode($cart_details) as $key => $value) {
						if ($value->menu_id == $this->input->post('entity_id')) {
							$menuItemExist = 1;
						}
					}
				}
			}
		}
		echo $menuItemExist;
	}
	// get the custom items count
	public function customItemCount()
	{
		$cart_details = get_cookie('cart_details');
		$arr_cart_details = json_decode($cart_details);
		$cart_restaurant = get_cookie('cart_restaurant');
		$qtyval = $this->input->post('qtyval')?$this->input->post('qtyval'):1;
		$data['restaurant_data'] = $this->common_model->checkResForCart($cart_restaurant);
		$data['restaurant_name'] = $this->common_model->getResNametoDisplay($cart_restaurant);
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			if ($this->input->post('action') == "plus" && $this->input->post('cart_key') == "") { 
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $key => $value) {
							if ($value->menu_id == $this->input->post('entity_id')) {
								if((int)$value->quantity >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $value->quantity + $qtyval;
								}
								$menukey = $key;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => ($value->quantity)?(((int)$value->quantity>=999)?999:$value->quantity+$qtyval):$qtyval,
						            'comment' => $value->comment,
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day 
				}
			}
			else if ($this->input->post('action') == "plus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								if((int)$value->quantity >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $value->quantity + $qtyval;
								}
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => ($value->quantity)?(((int)$value->quantity>=999)?999:$value->quantity+1):1,
						            'comment' => ($value->comment)?$value->comment:'',
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			}
			else if ($this->input->post('action') == "minus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity - 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($value->quantity > 1) {
						 		if ($key == $menukey) {
									$cookie = array(
							            'menu_id'   => $value->menu_id,  
							            'quantity' => ($value->quantity)?($value->quantity - 1):1,
							            'comment' => ($value->comment)?$value->comment:'',
							            'addons'  => $value->addons,               
						            );
						 			$arrayDetails[] = $cookie;
					            }
					            else
					            {
					            	$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					            }
					 		}
					 		else
					 		{
					 			if ($key != $menukey) {
					 				$oldcookie = $value;
						 			$arrayDetails[] = $oldcookie;
					 			}
					 		}
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
					$cart_details = $this->getcookie('cart_details');
					if (empty(json_decode($cart_details))) {
		            	delete_cookie('cart_details');
						delete_cookie('cart_restaurant');
						unset($_COOKIE['cart_details']);
						unset($_COOKIE['cart_restaurant']);
						$this->session->unset_userdata('tip_amount');
						$this->session->unset_userdata('tip_percent_val');
						
						$this->session->unset_userdata('is_redeem');
				    	$this->session->unset_userdata('redeem_submit');
				    	$this->session->unset_userdata('redeem_amount');
					}
					else
					{
		            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
					}
				}
			}
			else if (!$this->input->post('action')) {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								if((int)$this->input->post('customQuantity') >= 999){
									$value->quantity = 999;
								} else {
									$value->quantity = $this->input->post('customQuantity');
								}
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => ($value->quantity)?(((int)$this->input->post('customQuantity') >= 999)?999:$this->input->post('customQuantity')):1,
						            'comment' => ($value->comment)?$value->comment:'',
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
			}
			else if ($this->input->post('action') == "remove" && $this->input->post('cart_key') != '') { 
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
				 	foreach (json_decode($cart_details) as $key => $value) {
				 		if ($key != $this->input->post('cart_key')) {
					 		$oldcookie = $value;
				 			$arrayDetails[] = $oldcookie;
				 		}
				 	}
				}
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
				$cart_details = $this->getcookie('cart_details');
				if (empty(json_decode($cart_details))) {
	            	/*delete_cookie('cart_details');
					delete_cookie('cart_restaurant');*/
					unset($_COOKIE['cart_details']);
					unset($_COOKIE['cart_restaurant']);

					$this->session->unset_userdata('tip_amount');
					$this->session->unset_userdata('tip_percent_val');

					$this->session->unset_userdata('is_redeem');
			    	$this->session->unset_userdata('redeem_submit');
			    	$this->session->unset_userdata('redeem_amount');
				}
				else
				{
	            	$this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
				}
				$cart_details = $this->getcookie('cart_details');
				$cart_restaurant = $this->getcookie('cart_restaurant');
			} 
			else if ($this->input->post('action') == "updatecomment") { 
				//$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {	

						foreach ($arr_cart_details as $ckey => $value) {							
							if($ckey == $this->input->post('cart_key')) {//if ($value->menu_id == $this->input->post('entity_id')) {
								$value->comment = ($this->input->post('comment'))?$this->input->post('comment'):'';
								$menukey = $ckey;
							}
						}
					}					
					if (!empty(json_decode($cart_details))) {
					 	foreach (json_decode($cart_details) as $key => $value) {
					 		if ($key == $menukey) {
								$cookie = array(
						            'menu_id'   => $value->menu_id,  
						            'quantity' => $value->quantity,
						            'comment' =>  $this->input->post('comment'),
						            'addons'  => $value->addons,               
					            );
					 			$arrayDetails[] = $cookie;
				            }
				            else
				            {
				            	$oldcookie = $value;
					 			$arrayDetails[] = $oldcookie;
				            }
					 	}
					}
					$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
		            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day 
				}
				$cart_details = $this->getcookie('cart_details');
				$cart_restaurant = $this->getcookie('cart_restaurant');
			}

			if(!is_null(get_cookie('cart_details'))){
			 	$cart_details = $this->getcookie('cart_details');
				$cart_restaurant = $this->getcookie('cart_restaurant');
			}
			$data['cart_details'] = $cart_details;
			$data['cart_restaurant'] = $cart_restaurant;
			$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
			
			//get System Option Data
	        /*$this->db->select('OptionValue');
	        $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
	        $data['currency_symbol'] = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/
	        $default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency;
			}else{
				$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
			}
			// get if a item is still added in the cart or not
			$added = 0;
			if (!empty($data['cart_details']['cart_items'])) {
				foreach ($data['cart_details']['cart_items'] as $key => $value) {
					if ($value['menu_id'] == $this->input->post('entity_id')) {
						$added = 1;
					}
				}
			}
			$data['allow_scheduled_delivery'] = $this->restaurant_model->getResAllowSchedulingFlag($this->input->post('restaurant_id'));
			if ($this->input->post('is_main_cart') == "yes") {
				$cart = $this->load->view('ajax_main_cart',$data,true);
			}
			else
			{
				$data['order_mode'] = $this->restaurant_model->getRestaurantOrderMode($this->input->post('restaurant_id'));
				$cart = $this->load->view('ajax_your_cart',$data,true);
			}
			$array_view = array(
				'cart'=>$cart,
				'added'=>$added,
				'item_count' => count($data['cart_details']['cart_items'])
			);
			echo json_encode($array_view);
		}
	}
	// check cart's restaurant id
	public function checkCartRestaurant(){
		$restaurant = 0;
		if (!empty($this->input->post('restaurant_id'))) {
			$cart_restaurant = get_cookie('cart_restaurant');
			if (!empty($cart_restaurant)) {
				if ($this->input->post('restaurant_id') == $cart_restaurant) {
					$restaurant = 1; // same restaurant
				}
				else
				{
					$restaurant = 0;  // another restaurant
				}
			}
			else
			{
				$restaurant = 1;
			}
		}
		echo $restaurant;
	}
	// empty the cart items
	public function emptyCart(){
		delete_cookie('cart_details');
		delete_cookie('cart_restaurant');
		unset($_COOKIE['cart_details']);
		unset($_COOKIE['cart_restaurant']);
		$this->session->unset_userdata('tip_amount');
		$this->session->unset_userdata('tip_percent_val');

		$this->session->unset_userdata('is_redeem');
    	$this->session->unset_userdata('redeem_submit');
    	$this->session->unset_userdata('redeem_amount');
	}
	//new changes for menu details on image click :: start
	public function checkCartRestaurantDetails(){
		$data['cart_rest'] = 0;
		$data['old_cartqty'] = 1;
		$data['is_closed'] = $this->input->post('is_closed');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) { 
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
			$data['result'] = $this->restaurant_model->getMenuItem($this->input->post('entity_id'),$this->input->post('restaurant_id'));
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency;
			}else{
				$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($this->input->post('restaurant_id'));
			}
			if($this->input->post('recipe')=='no'){
				$data['recipe_name'] = $this->restaurant_model->getRecipe_page($data['result'][0]['items'][0]['menu_id']);
			}
			if($this->input->post('recipe_page')=='recipe'){
				$data['recipe_page'] =$this->input->post('recipe_page');
			}else if($this->input->post('recipe_page')=='checkout_as_guest'){
				$data['recipe_page'] =$this->input->post('recipe_page');
			}
			$this->load->view('ajax_menu_items_details',$data);
		}
	}
	//new changes for menu details on image click :: end
	//re-order changes :: start
	//check if cart is empty or not
	public function checkCartOnReorder(){
		$data['cart_details'] = get_cookie('cart_details');
		$data['cart_restaurant'] = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
		$is_cart_empty = 0;
		if (count($data['cart_details']['cart_items'])>0) {
			$is_cart_empty = 1;
		}
		echo $is_cart_empty;
	}
	public function addReorderItemsToCart()
	{
		$data['page_title'] = $this->lang->line('title_cart'). ' | ' . $this->lang->line('site_title');
		if (!empty($this->input->post('menuDetailsArray'))) {
			$check_in_stock = array();
			$data['another_restaurant'] = '';
			$deactive_count = 0;
			$outofstock_count = 0;
			$total_item_count = count($this->input->post('menuDetailsArray'));
			$arrayDetails = array();
			foreach ($this->input->post('menuDetailsArray') as $menu_key => $menu_value) {
				$itemArray = array();
				$menuDetails = $this->restaurant_model->getMenuItem($menu_value['menu_id'],$this->input->post('restaurant_id'));
				if(empty($menuDetails)){
					$deactive_count++;
				}else if($menuDetails[0]['items'][0]['stock'] == '0' && $menuDetails[0]['items'][0]['allow_scheduled_delivery'] == '0'){
					$outofstock_count++;
					array_push($check_in_stock, $menuDetails[0]['items'][0]['name']);
				}else {
					if($menu_value['is_addon']=='0') { // for non - customized items
						$cookie = array(
				            'menu_id'   => $menu_value['menu_id'], 
				            'quantity' => $menu_value['menu_qty'], 
				            'comment' => $menu_value['comment'], 
				            'addons'  => '',               
			            );
			            $arrayDetails[] = $cookie;
						
					} else { // for customized items
						$itemArray['addons_category_list'] = $menu_value['addons_category_list'];
						$addons = array();
						if (!empty($itemArray)) {
							if (!empty($itemArray['addons_category_list'])) { 
								foreach ($itemArray['addons_category_list'] as $key => $value) {
									if (!empty($value['addons_list'])) {
										if (is_array(reset($value['addons_list']))) {
											foreach ($value['addons_list'] as $addkey => $addvalue) {
												$addons[] = array(
													'addons_category_id'=> $value['addons_category_id'],
													'add_onns_id' => $addvalue['add_ons_id']
												);
											}
										}
										else
										{
											foreach ($value['addons_list'] as $addkey => $addvalue) {
												$addons[] = array(
													'addons_category_id'=> $value['addons_category_id'],
													'add_onns_id' => $addvalue['add_ons_id']
												);
											}
										}
									}
								}
							}
							$cookie = array(
					            'menu_id'   => ($menu_value['menu_id'])?$menu_value['menu_id']:'', 
				            	'quantity' => $menu_value['menu_qty'], 
				            	'comment' => ($menu_value['comment'])?$menu_value['comment']:'',
					            'addons'  => $addons,          
				            );
				            $arrayDetails[] = $cookie;
						}
					}
				}
			}
			if(!empty($arrayDetails)){
				$this->input->set_cookie('cart_details',json_encode($arrayDetails),60*60*24*1); // 1 day
	            $this->input->set_cookie('cart_restaurant',$this->input->post('restaurant_id'),60*60*24*1); // 1 day
	            $data['cart_details'] = $this->getcookie('cart_details');
				$data['cart_restaurant'] = $this->getcookie('cart_restaurant');
			}
			if($outofstock_count>0){
				$menu_names = ''; // out of stock menu names
				foreach ($check_in_stock as $stock_key => $stock_value) {
					if($stock_key == 0){
						$menu_names .= $stock_value;
					} else {
						$menu_names .= ', '.$stock_value;
					}
				}
			}
			$data['cart_details'] = $this->getCartItems($data['cart_details'],$data['cart_restaurant']);
			$default_currency = get_default_system_currency();
			if(!empty($default_currency)){
				$data['currency_symbol'] = $default_currency;
			}else{
				$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($data['cart_restaurant']);
			}
			$cart_count = count($data['cart_details']['cart_items']);
			
			if($deactive_count>0 && $deactive_count<$total_item_count){
				//show msg : Your cart has been updated based upon availability
				$return_response = array('show_message'=>$this->lang->line('cart_updated'),'oktxt'=>$this->lang->line('ok'),'cart_count'=>$cart_count);
			} else if($deactive_count>0 && $deactive_count>=$total_item_count){
				//show msg : Request items are no longer available, Please proceed with a fresh order.
				$return_response = array('show_message'=>$this->lang->line('items_unavailable'),'oktxt'=>$this->lang->line('ok'),'cart_count'=>$cart_count);
			} else if($data['cart_details']['addons_not_available']==1){
				//show msg : Your cart has been updated based upon availability
				$return_response = array('show_message'=>$this->lang->line('cart_updated'),'oktxt'=>$this->lang->line('ok'),'cart_count'=>$cart_count);
			}else if($outofstock_count>0 && $outofstock_count<$total_item_count){
				//show msg : Your cart has been updated based upon availability
				$return_response = array('show_message'=>$this->lang->line('outofstock_text').' : '.$menu_names,'oktxt'=>$this->lang->line('ok'),'cart_count'=>$cart_count);
			}else if($outofstock_count>0 && $outofstock_count>=$total_item_count){
				//show msg : Requested items not available at the moment
				$return_response = array('show_message'=>$this->lang->line('outofstock_text').' : '.$menu_names,'oktxt'=>$this->lang->line('ok'),'cart_count'=>$cart_count);
			}else{
				$return_response = array('show_message'=>'','oktxt'=>'','cart_count'=>$cart_count);
			}
			echo json_encode($return_response);
		}
	}
	//re-order changes :: end
	//check restaurant : closed/offline/deactive
	public function checkResStat(){
		$restaurant_id = $this->input->post('restaurant_id');
		$scheduleddate_inp = ($this->input->post('scheduleddate_inp')) ? date('Y-m-d', strtotime(str_replace('-', '/', $this->input->post('scheduleddate_inp')))) : '';
		$scheduledtime_inp = ($this->input->post('scheduledtime_inp')) ? date('H:i:s', strtotime($this->input->post('scheduledtime_inp'))) : '';
		$combinedDT = ($this->input->post('scheduleddate_inp') && $this->input->post('scheduledtime_inp')) ? date('Y-m-d H:i:s', strtotime("$scheduleddate_inp $scheduledtime_inp")) : ''; 
		
		$allow_addorder = 1; //flag to check if scheduled addOrder should be allowed or not
		if($this->input->post('scheduleddate_inp') && $this->input->post('scheduledtime_inp')) {
			$scheduled_datetime_chk = $this->common_model->setZonebaseDateTime($combinedDT);
			$request_date = new DateTime($scheduled_datetime_chk);
			$now = new DateTime();
			if($request_date < $now) {
				$allow_addorder = 0;
			}
		}
		$check_res_stat = $this->common_model->checkResForCart($restaurant_id, $combinedDT);
		$menu_ids = $this->input->post('menu_ids');
		$check_in_stock = ($this->input->post('is_scheduling_allowed') == 0) ? $this->common_model->checkMenuInstock($menu_ids) : 'in_stock';
		if(!empty($check_res_stat)){
			if($check_res_stat->status != "1" || $check_res_stat->enable_hours != '1' || $check_res_stat->timings['off'] == "close" ) {
				$return_response = array('show_message'=>$this->lang->line('resto_not_accepting_orders'),'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
			} else if($check_res_stat->timings['closing'] == "Closed") {
				$return_response = array('show_message'=>$this->lang->line('restaurant_closemsg'),'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
			} else if(!empty($check_in_stock) && $check_in_stock != 'in_stock'){
				$menu_names = ''; // out of stock menu names
				foreach ($check_in_stock as $stock_key => $stock_value) {
					if($stock_key == 0){
						$menu_names .= $stock_value->name;
					} else {
						$menu_names .= ', '.$stock_value->name;
					}
				}
				$return_response = array('show_message'=>$this->lang->line('outofstock_text').' : '.$menu_names,'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
			} else if($check_res_stat->isallowed_scheduled_order == 'no' && $this->input->post('scheduleddate_inp') && $this->input->post('scheduledtime_inp')) {
				$return_response = array('show_message'=>$this->lang->line('restaurant_is_not_available'),'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
			} else if($allow_addorder == 0 && $this->input->post('scheduleddate_inp') && $this->input->post('scheduledtime_inp')) {
				$return_response = array('show_message'=>$this->lang->line('past_datetime_notallowed'),'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
			} else if ($check_in_stock=='in_stock'){
				$return_response = array('show_message'=>'','oktxt'=>'', 'status'=>'res_available');
			}
		} else {
			$return_response = array('show_message'=>$this->lang->line('resto_not_accepting_orders'),'oktxt'=>$this->lang->line('ok'), 'status'=>'res_unavailable');
		}
		echo json_encode($return_response);
	}
}
?>