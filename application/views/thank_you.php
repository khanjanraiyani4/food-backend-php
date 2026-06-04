<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title><?php echo $MetaTitle ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/login.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/admin/img/favicon.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url(); ?>assets/admin/img/favicon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/admin/img/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/admin/img/favicon.png">
<link rel="manifest" href="<?php echo base_url(); ?>assets/front/images/favicons/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/admin/img/favicon.png">
<meta name="theme-color" content="#ffffff">
<?php
if(!empty(website_header_script)){
  echo website_header_script;
}
?>
</head>
<body class="login">
<?php
if(!empty(website_body_script)){
  echo website_body_script;
}
?>
<div class="logo">
    <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt=""/>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content content-width">
    <!-- BEGIN FORM -->
   	<?php /*if($this->session->flashdata('activate')){ ?>
        <h3><?php echo $this->session->flashdata('activate');?></h3>
    <?php }*/ ?>
    <?php 
    if($_SESSION['activate'])
    { ?>
        <h3>
             <?php echo $_SESSION['activate'];
                unset($_SESSION['activate']);
             ?>
        </h3>
    <?php } ?>
    <?php /*if ($this->session->flashdata('PasswordChange')){ ?>
    	<h3><?php echo $this->session->flashdata('PasswordChange')?></h3>
    	<!-- <div class="click_to_login"> <?php //echo $this->lang->line('first_click') ?> <a href="<?php //echo base_url().'home/login';?>" class="btn btn-secondary"><?php// echo $this->lang->line('here') ?></a> <?php //echo $this->lang->line('to_login') ?> </div> -->
    <?php }*/ ?>
    <?php 
    if($_SESSION['PasswordChange'])
    { ?>
        <h3>
             <?php echo $_SESSION['PasswordChange'];
                unset($_SESSION['PasswordChange']);
             ?>
        </h3>
    <?php } ?>
    <?php /*if ($this->session->flashdata('verifyerr')){ ?>
    	<h3><?php echo $this->session->flashdata('verifyerr')?></h3>
    <?php }*/ ?>
    <?php 
    if($_SESSION['verifyerr'])
    { ?>
        <h3>
             <?php echo $_SESSION['verifyerr'];
                unset($_SESSION['verifyerr']);
             ?>
        </h3>
    <?php } ?>
    <!-- END  FORM -->
</div>
<!-- END -->
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<?php if($lang->language_slug=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<?php
if(!empty(website_footer_script)){
    echo website_footer_script;
}
?>
</body>
</html>