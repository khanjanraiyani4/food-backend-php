<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cms extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model(ADMIN_URL.'/common_model');
		$this->load->model('/home_model');
	}
	// about us page
	public function about_us()
	{
		$data['page_title'] = $this->lang->line('about_us'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'AboutUs';
		// get about us
		$language_slug = $this->session->userdata('language_slug');
		$data['about_us'] = $this->common_model->getCmsPages($language_slug,'about-us');
		$this->load->view('about_us',$data);
	}
	//legal notice
	public function legal_notice()
	{
		$data['page_title'] = $this->lang->line('legal_notice'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'LegalNotice';
		// get legal notice
		$language_slug = $this->session->userdata('language_slug');
		$data['legal_notice'] = $this->common_model->getCmsPages($language_slug,'legal-notice');
		$this->load->view('legal_notice',$data);
	}
	//terms and conditions
	public function terms_and_conditions()
	{
		$data['page_title'] = $this->lang->line('terms_and_conditions'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'TermsAndConditions';
		// get terms and conditions
		$language_slug = $this->session->userdata('language_slug');
		$data['terms_and_conditions'] = $this->common_model->getCmsPages($language_slug,'terms-and-conditions');
		$this->load->view('terms_and_conditions',$data);
	}
	// privacy_policy page
	public function privacy_policy()
	{
		$data['page_title'] = $this->lang->line('privacy_policy'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'PrivacyPolicy';
		// get privacy policy
		$language_slug = $this->session->userdata('language_slug');
		$data['privacy_policy'] = $this->common_model->getCmsPages($language_slug,'privacy-policy');
		$this->load->view('privacy_policy_page',$data);
	}
	// login with facebook
	public function login_with_facebook()
	{
		$data['page_title'] = $this->lang->line('fb_login'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'LoginWithFacebook';
		// get privacy policy
		$language_slug = $this->session->userdata('language_slug');
		$data['login_with_fb'] = $this->common_model->getCmsPages($language_slug,'login-with-fb');
		$this->load->view('login_with_fb_cms',$data);
	}
	public function fatoorah()
	{
		//Test key
		define("test_fatoorah_api_key", "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL");
		define("test_fatoorah_api_url", "https://apitest.myfatoorah.com");

		$payment_method = $this->input->get('payment_method');
		$paymentId = ($this->input->get('paymentId'))?$this->input->get('paymentId'):'';

		$headers = array (
           'Authorization: Bearer '.test_fatoorah_api_key,                        
           'Content-Type: application/json'
       );
       $postFields = [
               'Key'     => $paymentId,
               'KeyType' => 'paymentId'
           ];

       $fatoorah_api_url = test_fatoorah_api_url.'/v2/getPaymentStatus';

       $chkpayment_status = $this->Curlcall_fun($headers,$fatoorah_api_url,$postFields);

       echo "<pre>"; print_r($chkpayment_status); exit;
	}
	public function Curlcall_fun($headers=[],$CURLOPT_URL,$fields=[],$payment_method='')
    {        
        $responsarr = array();
        if(!empty($headers) && trim($CURLOPT_URL)!='')
        {
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $CURLOPT_URL);
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            if(!empty($fields))
            {
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
            }            
            $result = curl_exec($ch);
            curl_close($ch);
            $responsarr = json_decode($result,true);             
            if(strpos($result,"error") || strpos($result,"field_errors"))
            {
                $responsarr['error'] = "error";
            }
            else
            {    
                $responsarr = json_decode($result,true);
            }
        }
        return $responsarr;
    }

	//FAQs page
	public function faqs()
	{
		$data['page_title'] = $this->lang->line('faqs'). ' | ' . $this->lang->line('site_title');
		$data['current_page'] = 'faqs';
		$language_slug = $this->session->userdata('language_slug');
		$data['result'] = $this->common_model->get_category_wise_faqs_list($language_slug);
		$this->load->view('faqs',$data);
	}
}
?>
