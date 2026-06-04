<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('vendor/autoload.php');
use Google\Auth\ApplicationDefaultCredentials;
class Push_notification
{
	/*
		generate access token.
	*/
	function generate_token(){
		putenv('GOOGLE_APPLICATION_CREDENTIALS='.APPPATH.'/libraries/push_notification_json/key.json');
		$scope = 'https://www.googleapis.com/auth/firebase.messaging';
		$credentials = ApplicationDefaultCredentials::getCredentials($scope);
		$result = $credentials->fetchAuthToken();
		return (!empty($result) && array_key_exists("access_token",$result)) ? $result['access_token'] : NULL;
	}

	/*
		Send notification to particular token/device or topic/subscribed devices. 
		@param $message and $apns (optional)
		NOTE::Check push notification guide file or visit https://firebase.google.com/docs/cloud-messaging/migrate-v1 for the $message & $apn format.
	*/
	function send_push_notification($message, $apns = NULL){
		$access_token = $this->generate_token();
		if(!is_null($access_token)){
			$this->prepare_push_notification_and_send($access_token,$message,$apns);
		}
	}
	/*
		Send notification to multiple devices. 
		@param @device_ids,$message and $apns (optional)
		NOTE:: $device_ids must be an array format.
	*/
	function send_push_notification_multiple($device_ids,$message,$apns = NULL){
		$access_token = $this->generate_token();
		if(!is_null($access_token)){
			if(is_array($device_ids) == 1){
				foreach ($device_ids as $device_id) {
					$message["token"] = $device_id;
					$this->prepare_push_notification_and_send($access_token,$message,$apns);
				}
			}
		}
	}
	function prepare_push_notification_and_send($access_token, $message)
	{
		$fields = array();
		$fields["message"] = $message;
		/*if(!is_null($apns) && $apns != ''){
			$fields["apns"] = $apns;
		}*/
		$headers = array (
			'Authorization: Bearer '.$access_token,
			'Content-Type: application/json; UTF-8'
		);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/iladary-ma/messages:send');
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
		$response = curl_exec($ch);
		curl_close($ch);
		if(!empty($response)){
			$response = json_decode($response);
			//print_r($response);
			if(isset($response->error) && !empty($response->error)){
				log_message('error', 'Error while sending notification.');
			}
		}
	}
}