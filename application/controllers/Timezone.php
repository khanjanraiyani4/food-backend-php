<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timezone extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model(ADMIN_URL.'/common_model');  
		$this->load->model('/home_model');
	}
	// contact us page
	public function index()
	{
		echo "Current TimeZone: ". date_default_timezone_get();
		echo "<br>";
		echo "Current Date Time: ". date('d-m-Y g:i a');
		echo "<br>";
		echo "day: ". date('N');
echo "<br>---------------------------------------------------------<br>";
		date_default_timezone_set("asia/kolkata");

		echo "Current Date Time: ". date('d-m-Y g:i a');
		echo "<br>";
		echo "day: ". date('N');




exit;
	}
}
?>
