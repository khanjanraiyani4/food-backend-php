<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contact_us extends CI_Controller {
  
	public function __construct() {
		parent::__construct();        
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');    
	}
	// contact us page
	public function index()
	{
		$data['page_title'] = $this->lang->line('contact_us'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'ContactUs';
		if($this->input->post('submit_page') == "Submit"){
			$this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required'); 
			$this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required'); 
			$this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required'); 
			$this->form_validation->set_rules('res_phone_number', $this->lang->line('res_phone_number'), 'trim|required|numeric'); 
			$this->form_validation->set_rules('res_name', $this->lang->line('res_name'), 'trim|required'); 
			$this->form_validation->set_rules('res_zip_code', $this->lang->line('res_zip_code'), 'trim|required|numeric'); 
			$this->form_validation->set_rules('g-recaptcha-response', 'recaptcha validation', 'required|callback_validate_captcha');
            $this->form_validation->set_message('validate_captcha', $this->lang->line('invalid_captcha'));       
	        //$this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required');        
	        if ($this->form_validation->run())
	        {   
	        	//get System Option Data
				$this->db->select('OptionValue');
				$FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();
				$this->db->select('OptionValue');
				$FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();
				//email to user
				$this->db->select('subject,message');
                $Emaildata_user = $this->db->get_where('email_template',array('email_slug'=>'contact-us','status'=>1))->first_row();
				$res_phone_code = trim($this->input->post('res_phone_code'));
				$res_phone_number = ($res_phone_code) ? '+'.$res_phone_code : '+1';
				$res_phn_inp = trim($this->input->post('res_phone_number'));
				$res_phone_number = $res_phone_number.$res_phn_inp;

				$owners_phone_code = trim($this->input->post('owners_phone_code'));
				$owners_phone_code = ($owners_phone_code) ? '+'.$owners_phone_code : '+1';
				$owners_phn_inp = trim($this->input->post('owners_phone_number'));
				$owners_phone_number = $owners_phone_code.$owners_phn_inp;

                $arrayData_user = array('FirstName'=>trim($this->input->post('first_name')),'LastName'=>trim($this->input->post('last_name')),'Email'=>trim($this->input->post('email')),'res_phone_number'=>$res_phone_number,'res_name'=>trim($this->input->post('res_name')),'res_zip_code'=>trim($this->input->post('res_zip_code')),'Message'=>trim($this->input->post('message')),'owners_phone_number'=>$owners_phone_number,'user_ipaddress'=>$_SERVER['REMOTE_ADDR']);
                $EmailBody_user = generateEmailBody($Emaildata_user->message,$arrayData_user);  
	        	
	        	/*Conectoo Email api start : 18march2021*/
	        	$this->load->library('email');
				$config['charset'] = "utf-8";
				$config['mailtype'] = "html";
				$config['newline'] = "\r\n";
				$this->email->initialize($config);
				$this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
				$this->email->to(trim($this->input->post('email')));
				$this->email->subject($Emaildata_user->subject);
				$this->email->message($EmailBody_user);
				$this->email->send();

				//email to admin
				$this->db->select('subject,message');
                $Emaildata_admin = $this->db->get_where('email_template',array('email_slug'=>'contact-us-for-admin','status'=>1))->first_row();
				
				// admin email 
				$this->db->select('OptionValue');
				$AdminEmailAddress = $this->db->get_where('system_option',array('OptionSlug'=>'Admin_Email_Address'))->first_row();
                $arrayData_admin = array('FirstName'=>trim($this->input->post('first_name')),'LastName'=>trim($this->input->post('last_name')),'Email'=>trim($this->input->post('email')),'res_phone_number'=>$res_phone_number,'res_name'=>trim($this->input->post('res_name')),'res_zip_code'=>trim($this->input->post('res_zip_code')),'Message'=>trim($this->input->post('message')),'owners_phone_number'=>$owners_phone_number,'user_ipaddress'=>$_SERVER['REMOTE_ADDR']);
                $EmailBody_admin = generateEmailBody($Emaildata_admin->message,$arrayData_admin);
				/*Conectoo Email api start : 18march2021*/
				$this->load->library('email');
				$config['charset'] = "utf-8";
				$config['mailtype'] = "html";
				$config['newline'] = "\r\n";
				$this->email->initialize($config);
				$this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
				$this->email->to($AdminEmailAddress->OptionValue);
				$this->email->subject($Emaildata_admin->subject);
				$this->email->message($EmailBody_admin);
				$this->email->send();
                
                $data['success_msg'] = $this->lang->line('message_sent');
                //$this->session->set_flashdata('contactUsMSG', $this->lang->line('message_sent')); 
                $_SESSION['contactUsMSG'] = $this->lang->line('message_sent');

                 $add_content = array(
                  'first_name'=>trim($this->input->post('first_name')),
                  'last_name'=>trim($this->input->post('last_name')), 
                  'email '=>trim($this->input->post('email')), 
                  'rest_name'=>trim($this->input->post('res_name')), 
                  'res_zip_code'=>trim($this->input->post('res_zip_code')), 
                  'res_phone_number'=>$res_phone_number, 
                  'owners_phone_number'=>$owners_phone_number, 
                  'message'=>trim($this->input->post('message')), 
                  'created_date'=>date('Y-m-d H:i:s')                      
                );
                $this->common_model->addData('contactus_detail',$add_content);
                
                redirect(base_url().'contact_us');
	        }
	    }
		$language_slug = $this->session->userdata('language_slug');
		$data['contact_us'] = $this->common_model->getCmsPages($language_slug,'contact-us');
		$this->load->view('contact_us',$data);
	}

	function validate_captcha() {
        return validate_captcha_common($this->input->post('g-recaptcha-response'));
    }
}
?>
