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
<?php if($lang->language_slug  == 'ar'){?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/admin/css/components-rtl.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/admin/layout/css/custom-rtl.css" rel="stylesheet" type="text/css"/>
<?php }else{ ?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/admin/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<?php } ?>
<link href="<?php echo base_url();?>assets/admin/css/login.css" rel="stylesheet" type="text/css"/>
<link rel="icon" href="<?php echo base_url();?>assets/admin/img/favicon.png" type="image/x-icon" />
</head>
<body class="login">
<div class="logo">
    <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt=""/>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content">
    <!-- BEGIN FORM -->
    <?php if(validation_errors()){?>
        <div class="alert alert-danger alerttimerclose">
            <?php echo validation_errors();?>
        </div>
    <?php } ?>
    <?php if(isset($_SESSION['PasswordChange']))
    { ?>
        <div class="alert alert-success alerttimerclose">
             <?php echo $_SESSION['PasswordChange'];
             unset($_SESSION['PasswordChange']);
             ?>
        </div>
    <?php } ?>
    <?php if(isset($_SESSION['verifyerr']))
    { ?>
        <div class="alert alert-success alerttimerclose">
             <?php echo $_SESSION['verifyerr'];
             unset($_SESSION['verifyerr']);
             ?>
        </div>
    <?php } ?>    
    <form action="<?php echo base_url().ADMIN_URL?>/home/newPassword" method="Post" id="newPasswordform" class="form-wrap">
    <h3><?php echo $this->lang->line('title_newpassword');?></h3>
    <p>
       <?php echo $this->lang->line('create_pass');?>
    </p>
      <div class="form-group">
         <label><?php echo $this->lang->line('password');?></label>
         <input type="hidden" value="<?php echo $verificationCode?>" name="verificationCode" id="verificationCode">
         <input type="hidden" value="adminreset" name="reset_from" id="reset_from">
         <input class="form-control" type="password" placeholder="<?php echo $this->lang->line('password');?>" id="password" name="password"/>
      </div>
      <div class="form-group">
         <label><?php echo $this->lang->line('confirm_pass');?></label>
         <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="<?php echo $this->lang->line('confirm_pass');?>">
      </div>
    <div class="action-wrp">
     <button class="btn btn-lg btn-signin theme-btn" type="submit" name="submit" id="submit" value="Submit"><?php echo $this->lang->line('submit');?></button>
    </div>
   </form>
    <!-- END  FORM -->
</div>
<!-- END -->
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-login-forgot-validation.js"></script>
<?php if($lang->language_slug  == 'ar'){?>
    <script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($lang->language_slug=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
</body>
</html>