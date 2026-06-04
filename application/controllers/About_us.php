<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class About_us extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');
	}
	// contact us page
	public function index()
	{
		$data['page_title'] = $this->lang->line('about_us'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'AboutUs';  
		// get about us
		$language_slug = $this->session->userdata('language_slug');
		$data['about_us'] = $this->common_model->getCmsPages($language_slug,'about-us');
		$this->load->view('about_us',$data);
	}
}
?>
