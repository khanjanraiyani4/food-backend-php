<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title><?php echo $this->lang->line('site_title');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
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
<link rel="shortcut icon"  sizes="40x40" href="<?php echo base_url();?>assets/admin/img/favicon.png"/>
</head>
<body class="login">
<div class="bg-login"></div>
<div class="overlay"></div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <div class="logo">
        <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt=""/>
    </div>    
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

    <?php if(isset($_SESSION['loginError'])){?>
    <div class="alert alert-danger alerttimerclose">
         <?php echo $_SESSION['loginError'];
            unset($_SESSION['loginError']);
         ?>
    </div>
    <?php } else if(isset($loginError) && $loginError !=""){?>
    <div class="alert alert-danger alerttimerclose">
         <?php echo $loginError;?>
    </div>
    <?php } ?>    
    <?php if(isset($_SESSION['ErrorPreventMultiLogin']))
    { ?>
        <div class="alert alert-danger alerttimerclose">
             <?php echo $_SESSION['ErrorPreventMultiLogin'];
             unset($_SESSION['ErrorPreventMultiLogin']);
             ?>
        </div>
    <?php } ?>
    <?php // get Cookies
    parse_str(get_cookie('adminAuth'), $adminCook); 
    ?>
    <form id="login_form" class="login-form" action="<?php echo base_url().ADMIN_URL; ?>/home/do_login" method="post">
        <h3 class="form-title"><?php echo $this->lang->line('login_acc'); ?></h3>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
           
            <div class="input-icon">
                <i class="fa fa-user"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="<?php echo $this->lang->line('email'); ?>" name="username" id="username" value="<?php echo isset($adminCook['usr'])?$adminCook['usr']:'';?>"/>
            </div>
        </div>
        <div class="form-group">
           
            <div class="input-icon">
                <i class="fa fa-lock"></i>
                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="<?php echo $this->lang->line('password'); ?>" name="password" id="password" value="<?php echo isset($adminCook['hash'])?$adminCook['hash']:'';?>" />
            </div>
        </div>
        <div class="form-group">
            <input type="checkbox" name="rememberMe" id="rememberMe" value="1" <?php echo ($adminCook)?"checked":""?>/> <?php echo $this->lang->line('remember'); ?>
            <input type="submit" class="btn danger-btn theme-btn pull-right" name="submit" value="Login">
        </div>
        <div class="forget-password">
            <h4><?php echo $this->lang->line('forgot_pass'); ?></h4>
            <p>
                 <?php echo $this->lang->line('first_click'); ?> <a href="<?php echo base_url().ADMIN_URL;?>/home/forgotpassword" id="forget-password">
                <?php echo $this->lang->line('here'); ?> </a>
                <?php echo $this->lang->line('reset_here'); ?>
            </p>
        </div>
    </form>
    <!-- END LOGIN FORM -->
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
     <?php echo $this->lang->line('copyright');?>&copy; <?php echo date('Y').'.'.$this->lang->line('copyright');?>  <?php echo $this->lang->line('site_footer');?>
</div>
</div>
<!--[if lt IE 9]>
    <script src="<?php echo base_url();?>assets/admin/plugins/respond.min.js"></script>
    <script src="<?php echo base_url();?>assets/admin/plugins/excanvas.min.js"></script> 
<![endif]-->
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