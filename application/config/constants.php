<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('SALT','5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId');
define('ADMIN_URL','backoffice');
define('FCM_KEY','ENTER_FCM_KEY'); 
define('IS_SMS_API_INTEGRATED',TRUE);
//sandbox url (paypal)
define('use_sandbox',TRUE);
define('SANDBOX_PAYPAL_URL_V1',"https://api.sandbox.paypal.com/v1/");
define('SANDBOX_PAYPAL_URL_V2',"https://api.sandbox.paypal.com/v2/");
// live url (paypal)
define('LIVE_PAYPAL_URL_V1',"https://api.paypal.com/v1/");
define('LIVE_PAYPAL_URL_V2',"https://api.paypal.com/v2/");

//driver tip
define('driver_tiparr',[1,2,3,4]);
define('review_count',5);
define('order_count',8);
//image default quality
define('IMAGE_QUALITY',60);
//for system currency display
define('ACTIVATE_SYSTEM_DEFAULT_CURRENCY',1);
/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);
/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
// Constants
/*Begin::Print Receipt Constants*/
define('PRINT_RECEIPT_EMAIL',"support@hausdesdoeners.com");
define('PRINT_RECEIPT_TELEPHONE',"(+1)000000000");
define('PRINT_RECEIPT_WEBSITE',"www.hausdesdoeners.com");
/*End::Print Receipt Constants*/
define('TIME_INTERVAL',"60 mins");

//Twilio SMS integration :: demo server credentials
define("TWILIO_SID", "ENTER_TWILIO_SID");
define("TWILIO_MSID", "ENTER_TWILIO_MSID");
define("TWILIO_AUTH_TOKEN", "ENTER_TWILIO_AUTH_TOKEN");
define("TWILIO_PHN_NO", "ENTER_TWILIO_PHN_NO");

//Date Format
define("datetimepicker_format", "MM-DD-YYYY LT"); //(24 hours : HH:mm) (12 hours : LT) :: front web
define("datepicker_format_front", "MM-DD-YYYY"); //front web
define("timepicker_format", "LT"); //front web
define("daterangepicker_format", "MM-DD-YYYY"); //backoffice
define("datepicker_format", "mm-dd-yyyy"); //backoffice
define("date_timepicker_format", "mm-dd-yyyy HH:ii P"); //backoffice