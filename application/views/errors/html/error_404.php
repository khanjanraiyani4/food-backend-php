<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
	$ci = new CI_Controller();
	$ci =& get_instance();
	$ci->load->helper('url');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	    <title>404 Page Not Found</title>
	    	<link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	    <style>
	    	body {
			    background: url(/assets/admin/img/bg-login.png) no-repeat;
			    background-size: cover;
			    background-attachment: fixed;
			    overflow: hidden;
			    height: 100%;
			    position: fixed;
			    width: 100%;
			}
	    	.page-not-found .inside_content{
	    		max-width: 800px;
			    margin: 80px auto;
			    background-color: #fff;
			    text-align: center;
			    box-shadow: 0 0 15px rgba(0,0,0,0.2);
			    border-radius: 6px;
			    padding: 6em 20px;
			    opacity: 0.96;
	    	}
	    	.page-not-found .inside_content h1{
	    		color: #17161a;
    			font-size: 14em;
    			margin: 0px;
    			font-weight: bold;
	    	}
	    	.page-not-found .inside_content p{
	    		font-size: 26px;
    			color: #666;
    			letter-spacing: 2px;
    			font-weight: bold;
	    	}
	    	@media screen and (max-width: 992px) {
		    	.page-not-found .inside_content h1{
	    			font-size: 8em;
		    	}
			}
	    </style>
	</head>
	<body class="innerpage" style="overflow: auto; height: auto; position: relative;">
		<div class="main-container page-not-found">
			<div class="container">		
				<div class="inside_content">
					<h1>404</h1>
					<p>Opps ! Something went wrong...</p>
					<p><a href="<?php echo base_url(); ?>">Go Back to Home</a></p>
				</div>
			</div>
		</div>
	</body>
</html>