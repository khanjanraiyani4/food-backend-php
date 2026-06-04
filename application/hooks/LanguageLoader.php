<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
        $siteLang = $ci->session->userdata('language_directory');
        if ($siteLang) { 
			$ci->lang->load('messages',$siteLang);
        	$ci->config->set_item('language', $siteLang);
		} else { 
			$ci->lang->load('messages','english');
        	$ci->config->set_item('language', 'english');
		}

        /*$ci->lang->load('messages',$siteLang);
        $ci->config->set_item('language', $siteLang);*/
    }
}