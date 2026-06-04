<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Recipe extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
        $this->load->library('ajax_pagination');   
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/recipe_model');    
	}
    // get reciepes index page
	public function index()
	{
        $data['page_title'] = $this->lang->line('recipies').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'Recipe';
		$page = 0; 
		$result = $this->recipe_model->get_all_recipies(8,$page);
		$data['recipies'] = $result['data'];
		$count = count($data['recipies']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "recipe/index";        
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
		$this->load->view('recipe',$data);
	}
    // get reciepes with ajax filters
	public function ajax_recipies()
	{
        $data['page_title'] = $this->lang->line('recipies').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'Recipe';
		$page = ($this->input->post('page') !="")?$this->input->post('page'):0;
		$result = $this->recipe_model->get_all_recipies(8,$page,$this->input->post('recipe'));
		$data['recipies'] = $result['data'];
		$count = count($data['recipies']);
        $data['TotalRecord'] = $count;
        $config = array();
        $config["base_url"] = base_url() . "recipe/index";        
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
		$this->load->view('ajax_recipe',$data);
	}
	// get reciepe details
	public function recipe_detail()
	{
        $data['page_title'] = $this->lang->line('recipe_detail').' - '.$this->lang->line('site_title');
		$data['current_page'] = 'Recipe';
        $data['recipe_details'] = array();
        if (!empty($this->uri->segment('3'))) { 
            $content_id = $this->recipe_model->get_content_id($this->uri->segment('3'));
            $data['recipe_details'] = $this->recipe_model->get_recipe_detail($content_id->content_id);
            $data['menu_details'] = $this->recipe_model->get_menu_detail($data['recipe_details'][0]['content_id']);
            $link = $data['recipe_details'][0]['youtube_video'];
            preg_match("/v=([^&]+)/i", $link, $matches);
            $id = (!empty($matches[1])) ? $matches[1] : '';
            $data['recipe_details'][0]['youtube_video'] = $id;
        }
		$this->load->view('recipe_detail',$data);
	}
}
?>