<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Google API Configuration
| -------------------------------------------------------------------
|  client_id         string   Your Google API Client ID.
|  client_secret     string   Your Google API Client secret.
|  redirect_uri      string   URL to redirect back to after login.
|  application_name  string   Your Google application name.
|  api_key           string   Developer key.
|  scopes            string   Specify scopes
*/

$config['google']['client_id'] = 'ENTER_CLIENT_ID';
$config['google']['client_secret']    = 'ENTER_CLIENT_SECRET';
$config['google']['redirect_uri']     = base_url().'home/google/';
$config['google']['application_name'] = 'Login to Your website';
$config['google']['api_key']          = '';
$config['google']['scopes']           = array();