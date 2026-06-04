<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Truncate_database extends CI_Controller {
	public function __construct() {
		parent::__construct();     
		if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        } 
	}
	public function index()
    { 
    	
   		if($this->input->post('submit') == 'submit'){
    		$module = $this->input->post('module');
    		foreach ($module as $key => $value) {
    			//delete data from user
    			if($value == 'users'){
    				if ($this->db->table_exists('users') )
					{
						$users = $this->db->get('users')->result();
						if(!empty($restaurant)){
	    					foreach ($users as $key => $value) {
	    						@unlink(FCPATH.'uploads/'.$value->image);
	    						if ($this->db->table_exists('cart_detail') )
								{
	    							$this->db->where('user_id',$value->entity_id);
	    							$this->db->delete('cart_detail');
	    						}
	    					}
    					}
    					$this->db->where('user_type !=','MasterAdmin');
    					$this->db->delete('users');
    				}
    				if ($this->db->table_exists('user_login_map') )
					{
    					$this->db->empty_table('user_login_map');
    				}
    				if ($this->db->table_exists('user_order_notification') )
					{
    					$this->db->empty_table('user_order_notification');
    				}
    				if ($this->db->table_exists('driver_traking_map') )
					{	
    					$this->db->empty_table('driver_traking_map');	
    				}
    			}
    			//delete data of restaurant
    			if($value == 'restaurant'){
    				if ($this->db->table_exists('restaurant') )
					{
	    				$restaurant = $this->db->get('restaurant')->result();
	    				if(!empty($restaurant)){
	    					foreach ($restaurant as $key => $value) {
	    						if ($this->db->table_exists('coupon'))
								{
	    							$this->db->where('restaurant_id',$value->entity_id);
	    							$this->db->delete('coupon');
	    						}
	    						@unlink(FCPATH.'uploads/'.$value->image);
	    					}
	    				}
	    				//delete file of menu
	    				$folder_path = FCPATH.'uploads/menu'; 
	    				$files = glob($folder_path.'/*');  
	    				foreach($files as $file) {
						    if(is_file($file))  
						        // Delete the given file 
						        @unlink($file);  
						} 
	    				if ($this->db->table_exists('content_general'))
						{
		    				$this->db->where_in('content_type',array('restaurant','menu','package','branch'));
		    				$this->db->delete('content_general');
		    			}
    					$this->db->empty_table('restaurant');
    				}
    				if ($this->db->table_exists('deal_category') )
					{
    					$this->db->empty_table('deal_category');
    				}
    				if ($this->db->table_exists('add_ons_category') )
					{
    					$this->db->empty_table('add_ons_category');
    					if ($this->db->table_exists('content_general'))
						{
    						$this->db->where('content_type','addons_category');
	    					$this->db->delete('content_general');
	    				}
    				}
    				if ($this->db->table_exists('cart_detail') )
					{
    					$this->db->empty_table('cart_detail');
    				}
    				if ($this->db->table_exists('delivery_charge') )
					{
    					$this->db->empty_table('delivery_charge');
    				}
    			}
    			//delete data of category
    			if($value == 'category'){
    				if ($this->db->table_exists('category'))
					{
	    				//delete file of menu
    					$folder_path = FCPATH.'uploads/category'; 
	    				$files = glob($folder_path.'/*');  
	    				foreach($files as $file) {
						    if(is_file($file))  
						        // Delete the given file 
						        @unlink($file);  
						} 
	    				if ($this->db->table_exists('content_general'))
						{
	    					$this->db->where('content_type','category');
	    					$this->db->delete('content_general');
	    				}
    					$this->db->empty_table('category');
    				}
    			}
    			//delete data of order
    			if($value == 'order_master'){
    				if ($this->db->table_exists('order_master'))
					{
						if ($this->db->table_exists('order_driver_map'))
						{
							$this->db->empty_table('order_driver_map');
						}
    					$this->db->empty_table('order_master');
    					//delete file of menu
    					$folder_path = FCPATH.'uploads/invoice'; 
	    				$files = glob($folder_path.'/*');  
	    				foreach($files as $file) {
						    if(is_file($file))  
						        // Delete the given file 
						        @unlink($file);  
						} 
    				}
    				if ($this->db->table_exists('order_notification'))
					{
    					$this->db->empty_table('order_notification');
    				}
    				if ($this->db->table_exists('cart_detail'))
					{
    					$this->db->empty_table('cart_detail');
    				}
    			}
    			//delete data of event
    			if($value == 'event'){
    				if ($this->db->table_exists('event'))
					{
    					//delete file of event
    					$folder_path = FCPATH.'uploads/event'; 
	    				$files = glob($folder_path.'/*');  
	    				foreach($files as $file) {
						    if(is_file($file))  
						        // Delete the given file 
						        @unlink($file);  
						} 
						$this->db->empty_table('event');
    				}
    			}
    			//delete data of coupons
    			if($value == 'coupon'){
    				if ($this->db->table_exists('coupon'))
					{
	    				$coupons = $this->db->get('coupon')->result();
	    				if(!empty($coupons)){
	    					foreach ($coupons as $key => $value) {
	    						@unlink(FCPATH.'uploads/'.$value->image);
	    					}
	    				}
	    				$this->db->empty_table('coupon');
	    			}
    			}
    			//delete data of review and rating
    			if($value == 'review'){
    				if ($this->db->table_exists('review'))
					{
    					$this->db->empty_table('review');
    				}
    			}
    			//delete data of notifications
    			if($value == 'notifications'){
    				if ($this->db->table_exists('notifications'))
					{
    					$this->db->empty_table('notifications');
    				}
    			}
    		}
    		redirect(ADMIN_URL.'/dashboard');
    	}
    	
    	$html = '<html>';
    	$html .= '<form name="delete_module" id="delete_module" action="'.base_url().ADMIN_URL.'/truncate_database" method="post">';
    	$html .= "Please choose the modules to Clear data <br>";
    	$html .= '<input type="checkbox" name="module[]" id="user" value="users" checked> Users <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="restaurant" checked> Restaurant <br>';
    	$html .= '<input type="checkbox" name="module[]" id="menu_category" value="category" checked> Menu Category  <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="order_master" checked> Orders  <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="event" checked> Event Booking  <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="coupon" checked> Coupons  <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="review" checked> Review & Rating  <br>';
    	$html .= '<input type="checkbox" name="module[]" id="restaurant" value="notifications" checked> Notification  <br><br>';
    	$html .= '<input type="submit" name="submit" id="submit" value="submit"></form></html>';
    	echo $html;

    }
}