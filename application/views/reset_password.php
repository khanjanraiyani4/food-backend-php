<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title ?></title>
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
<div class="content">
    <!-- BEGIN FORM -->
    <?php if(validation_errors()){?>
        <div class="alert alert-danger">
            <?php echo validation_errors();?>
        </div>
    <?php } ?>
    <?php /*if($this->session->flashdata('PasswordChange')){ ?>
    <div class="alert alert-success">
        <strong><?php echo $this->lang->line('success') ?>!</strong> <?php echo $this->session->flashdata('PasswordChange');?>
    </div>
    <?php }*/ ?>
    <?php 
    if($_SESSION['PasswordChange'])
    { ?>
        <div class="alert alert-success">
              <strong><?php echo $this->lang->line('success') ?>!</strong> <?php echo $_SESSION['PasswordChange'];
                unset($_SESSION['PasswordChange']);
              ?>
        </div>
    <?php } ?>
    <?php /*if($this->session->flashdata('verifyerr')){ ?>
    <div class="alert alert-success">
        <strong><?php echo $this->lang->line('success') ?>!</strong> <?php echo $this->session->flashdata('verifyerr');?>
    </div>
    <?php }*/ ?>
    <?php 
    if($_SESSION['verifyerr'])
    { ?>
        <div class="alert alert-success">
              <strong><?php echo $this->lang->line('success') ?>!</strong> <?php echo $_SESSION['verifyerr'];
                unset($_SESSION['verifyerr']);
              ?>
        </div>
    <?php } ?>
    <form action="<?php echo base_url()?>user/reset" method="Post" id="newPasswordform" class="form-wrap">
      <h3><?php echo $this->lang->line('title_newpassword') ?></h3>
      <p>
          <?php echo $this->lang->line('create_pass') ?>
      </p>
        <div class="form-group">
           <label><?php echo $this->lang->line('password') ?></label>
           <input type="hidden" value="<?php echo $verificationCode?>" name="verificationCode" id="verificationCode">
           <input type="hidden" value="userreset" name="reset_from" id="reset_from">
           <input class="form-control" type="password" placeholder="Password" id="password" name="password"/>
        </div>
        <div class="form-group">
           <label><?php echo $this->lang->line('confirm_pass') ?></label>
           <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Confirm Password">
        </div>
      <div class="action-wrp">
       <button class="btn btn-lg btn-signin" type="submit" name="submit" id="submit" value="Submit"><?php echo $this->lang->line('submit') ?></button>
      </div>
   </form>
    <!-- END  FORM -->
</div>
<!-- END -->
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-login-forgot-validation.js"></script>
<?php if($lang=='fr'){  ?>
  <script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } elseif ($lang=='ar') { ?>
  <script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php
if(!empty(website_footer_script)){
    echo website_footer_script;
}
?>
</body>
</html>