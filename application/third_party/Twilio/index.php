<?php

// Update the path below to your autoload.php,
// see https://getcomposer.org/doc/01-basic-usage.md
require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Find your Account SID and Auth Token at twilio.com/console
// and set the environment variables. See http://twil.io/secure
/*$sid = getenv("TWILIO_ACCOUNT_SID");
$token = getenv("TWILIO_AUTH_TOKEN");
$twilio = new Client($sid, $token);

$call = $twilio->calls
               ->create("+919327503468", // to
                        "+919586051684", // from
                        [
                            "twiml" => "<Response><Say>Hey, Gaurang Patel. How are you?</Say></Response>"
                        ]
               );*/


// Your Account SID and Auth Token from twilio.com/console
$account_sid = '';
$auth_token = '';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

// A Twilio number you own with Voice capabilities
$twilio_number = "+1711111111";

// Where to make a voice call (your cell phone?)
$to_number = "+918888888888";

$client = new Client($account_sid, $auth_token);
$client->account->calls->create(
    $to_number,
    $twilio_number,
    array(
        "twiml" => "<Response><Say>Hey!</Say></Response>"
    )
);


print($client);