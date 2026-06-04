<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $meta_title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<?php if($this->session->userdata('language_slug')  == 'ar'){?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>
    
<?php }else{ ?>
    <link href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<?php } ?>
<link href="<?php echo base_url();?>assets/admin/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<?php if($this->session->userdata('language_slug')  == 'ar'){?>
<link href="<?php echo base_url();?>assets/admin/css/components-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/plugins-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/layout-rtl.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?php echo base_url();?>assets/admin/css/default-rtl.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/layout/css/custom-rtl.css" rel="stylesheet" type="text/css"/>
<?php }else{ ?>
<link href="<?php echo base_url();?>assets/admin/css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/admin/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet">
<?php } ?>
<?php $this->load->view(ADMIN_URL.'/style_admin'); ?>
<!-- END THEME STYLES -->
<link rel="shortcut icon"  sizes="40x40" href="<?php echo base_url();?>assets/admin/img/favicon.png"/>
<script>
    var BASEURL = '<?php echo base_url();?>';
    var EXPAND = "<?php echo $this->lang->line('expand');?>";
    var COLLAPSE = "<?php echo $this->lang->line('collapse');?>";
</script>
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo base_url();?>assets/admin/plugins/respond.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
</head>
<body class="page-header-fixed">
<!-- BEGIN header -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN header INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo base_url().ADMIN_URL;?>">
                <img src="<?php echo base_url();?>assets/admin/img/logo.png" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </div>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li>
                    <?php if(in_array('order~view',$this->session->userdata("UserAccessArray"))) {
                        $count = $this->common_model->getNotificationCount(); 
                        $event_count = $this->common_model->getEventNotificationCount();
                        $tablebooking_count = $this->common_model->getTableBookigNotificationCount();
                        $data_tablebooking_count = (isset($tablebooking_count->tablebooking_count) && $tablebooking_count->tablebooking_count!=null && $tablebooking_count->tablebooking_count!='')?$tablebooking_count->tablebooking_count:0;
                        $data_event_count = (isset($event_count->event_count) && $event_count->event_count!=null && $event_count->event_count!='')?$event_count->event_count:0;
                        $data_order_count = (isset($count->order_count) && $count->order_count!=null && $count->order_count!='')?$count->order_count:0;
                        $count->order_count = $data_order_count + $data_event_count +$data_tablebooking_count;
                        $order_count = $this->common_model->get_delivery_pickup_order_notification_count();
                        $dinein_count = $this->common_model->get_dinein_order_notification_count();                        
                    ?>
                    <div class="notification">
                        <a style="cursor: pointer;">
                        <div class="notification-bell">
                            <span>
                                <svg viewbox="-10 0 35 35">
                                    <path class="notification--bell" d="M14 12v1H0v-1l0.73-0.58c0.77-0.77 0.81-3.55 1.19-4.42 0.77-3.77 4.08-5 4.08-5 0-0.55 0.45-1 1-1s1 0.45 1 1c0 0 3.39 1.23 4.16 5 0.38 1.88 0.42 3.66 1.19 4.42l0.66 0.58z"></path> <path class="notification--bellClapper" d="M7 15.7c1.11 0 2-0.89 2-2H5c0 1.11 0.89 2 2 2z"></path>
                                </svg>
                                <span class="count"><?php echo (!empty($count))?($count->order_count >= 100)?'99+':intval($count->order_count):'0' ?></span>
                            </span>
                            <div class="notification-dropdown-content">
                                <a class="test" onclick="changeDeliveryPickupOrderViewStatus();" href="<?php echo base_url().ADMIN_URL ?>/order/view"><?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?>
                                    <span class="order-count">
                                        <?php echo (!empty($order_count))?($order_count->order_count >= 100)?'99+':$order_count->order_count:'0'; ?>
                                    </span>
                                </a>
                                <a class="test event_count" onclick="changeEventStatus();" href="<?php echo base_url().ADMIN_URL ?>/event/view"><?php echo $this->lang->line('events').' '.$this->lang->line('notification'); ?>
                                    <span class="event-count"><?php echo (!empty($event_count))?($event_count->event_count >= 100)?'99+':$event_count->event_count:'0'; ?></span>
                                </a>
                                <a class="test tablebooking_count" onclick="changeTableBookingStatus();" href="<?php echo base_url().ADMIN_URL ?>/book_table/view"><?php echo $this->lang->line('table_booking').' '.$this->lang->line('notification'); ?>
                                    <span class="tablebooking-count"><?php echo (!empty($tablebooking_count))?($tablebooking_count->tablebooking_count >= 100)?'99+':$tablebooking_count->tablebooking_count:'0'; ?></span>
                                </a>
                            </div>
                        </div>
                        </a>
                    </div>
                    <?php } ?>
                </li>
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <span class="username">
                    <?php echo $this->session->userdata('adminFirstname')." ".$this->session->userdata('adminLastname');?> </span>
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">                              
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL;?>/myprofile/getUserProfile">
                            <i class="fa fa-user"></i> <?php echo $this->lang->line('my_profile')?> </a>
                        </li>
                        <li class="divider"></li>                 
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL;?>/home/logout">
                            <i class="fa fa-key"></i> <?php echo $this->lang->line('log_out')?> </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <ul class="nav navbar-nav pull-right language-change">                                
                    <?php 
                    $langs = $this->common_model->getLanguages();
                    foreach($langs as $slug => $language)
                    { 
                        $langname = ($language->language_name == 'English')?$this->lang->line('english'):(($language->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
                    ?>      
                        <li class="<?php if($this->session->userdata('language_slug') == $language->language_slug) echo 'active-lang'; ?>" onclick="setLanguage('<?php echo $language->language_slug ?>')"><i class="glyphicon bfh-flag-<?php echo $language->language_slug ?>"></i><?php echo $langname; ?></li>
                    <?php }
                    ?>
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END header INNER -->
</div>
<!-- END header -->
<div class="clearfix">
</div>
<script type="text/javascript">
    function setLanguage(language_slug){
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : '<?php echo base_url().ADMIN_URL ?>/lang_loader/setLanguage',
            data : {'language_slug':language_slug},
            success: function(response) {
                location.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
            }
        });
    }
    
    var lang_slug = '<?php echo $this->session->userdata('language_slug'); ?>';
 
    var sProcessing = "<img src='<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif'/><span>&nbsp;&nbsp;Loading...</span>";
    var sLengthMenu = "_MENU_ records";
    var sInfo = "Showing _START_ to _END_ of _TOTAL_ entries";
    /*var sInfoEmpty = "No records found to show";*/
    var sInfoEmpty = "";
    var sGroupActions = "_TOTAL_ records selected:  ";
    var sAjaxRequestGeneralError = "Could not complete request. The server encountered an internal error.";
    var sEmptyTable = "No data available in table";
    var sZeroRecords = "No matching records found";
    var sPrevious = "Prev";
    var sNext = "Next";
    var sPage = "Page";
    var sPageOf = "of";
    var sFirst = "First";
    var sLast = "Last";
    
    <?php if ($this->session->userdata('language_slug') == "fr"): ?>
        sProcessing = '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Chargement...</span>',
        sLengthMenu = "Afficher _MENU_ &eacute;l&eacute;ments",
        sInfo = "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
        /*sInfoEmpty = "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",*/
        sInfoEmpty = "",
        sGroupActions = "_TOTAL_ records selected:  ",
        sAjaxRequestGeneralError = "Impossible de terminer la demande. Le serveur a rencontré une erreur interne.",
        sEmptyTable =  "Aucune donn&eacute;e disponible dans le tableau",
        sZeroRecords = "Aucun &eacute;l&eacute;ment &agrave; afficher",
        sPrevious = "Pr&eacute;c&eacute;dent",
        sNext = "Suivant",
        sPage = "Page",
        sPageOf = "de",
        sFirst = "Premier",
        sLast = "Dernier"
    <?php endif ?>
    <?php if ($this->session->userdata('language_slug') == "ar"): ?>
        sProcessing = '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;جارٍ التحميل...</span>',
        sLengthMenu = "أظهر _MENU_ مدخلات",
        sInfo = "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
        /*sInfoEmpty = "لم يتم العثور على أي سجلات",*/
        sInfoEmpty = "",
        sGroupActions = "_TOTAL_ records selected:  ",
        sAjaxRequestGeneralError = "تعذر إكمال الطلب. واجه الخادم خطأ داخليًا.",
        sEmptyTable =  "لا توجد بيانات متاحة في الجدول",
        sZeroRecords = "لم يتم العثور على سجلات متطابقة",
        sPrevious = "السابق",
        sNext =     "التالي",
        sPage = "صفحة",
        sPageOf = "من",
        sFirst =    "الأول",
        sLast =     "الأخير"
    <?php endif ?>
</script>
