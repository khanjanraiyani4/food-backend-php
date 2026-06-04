<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lang_loader extends CI_Controller
{
    public function __construct() {
        parent::__construct();     
    }
    //set lang
    public function setLanguage(){ 
        $slug = $this->input->post('language_slug');
        $languages = $this->common_model->getFirstLanguages($slug);   
        $this->session->set_userdata('language_directory',$languages->language_directory);
        $this->session->set_userdata('language_slug',$slug);
        $this->config->set_item('language', $languages->language_directory);
    }
}
