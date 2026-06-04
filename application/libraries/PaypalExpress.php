<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
/* 
 * PaypalExpress Class 
 * This class is used to handle PayPal API related operations 
 * @author    CodexWorld.com 
 * @url        http://www.codexworld.com 
 * @license    http://www.codexworld.com/license 
 */ 
// Include configuration file  
require_once APPPATH.'/config/config.php';
class PaypalExpress{ 
    /*public $paypalEnv       = PAYPAL_SANDBOX?'sandbox':'production'; 
    public $paypalURL       = PAYPAL_SANDBOX?'https://api.sandbox.paypal.com/v2/':'https://api.paypal.com/v2/'; 
    public $paypalClientID  = PAYPAL_API_CLIENT_ID; 
    private $paypalSecret   = PAYPAL_API_SECRET; */
    
    public function paypal_details(){
        return paypal_details();
    }
    public function validate($paymentID, $paymentToken, $payerID, $productID){ 
        $paypal_details = $this->paypal_details();
        if($paypal_details->enable_live_mode == '1'){
            $paypal_url = LIVE_PAYPAL_URL_V1;
            $paypal_client_id = $paypal_details->live_client_id;
            $paypal_secret = $paypal_details->live_client_secret;
        }else{
            $paypal_url = SANDBOX_PAYPAL_URL_V1;
            $paypal_client_id = $paypal_details->sandbox_client_id;
            $paypal_secret = $paypal_details->sandbox_client_secret;
        }
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $paypal_url.'oauth2/token'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id.":".$paypal_secret); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
        $response = curl_exec($ch); 
        curl_close($ch); 
        if(empty($response)){ 
            return false; 
        }else{ 
            $jsonData = json_decode($response); 
            $curl = curl_init($paypal_url.'payments/payment/'.$paymentID); 
            curl_setopt($curl, CURLOPT_POST, false); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($curl, CURLOPT_HEADER, false); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($curl, CURLOPT_HTTPHEADER, array( 
                'Authorization: Bearer ' . $jsonData->access_token, 
                'Accept: application/json', 
                'Content-Type: application/xml' 
            )); 
            $response = curl_exec($curl); 
            curl_close($curl); 
            // Transaction data 
            $result = json_decode($response); 
            return $result; 
        }
    }
}