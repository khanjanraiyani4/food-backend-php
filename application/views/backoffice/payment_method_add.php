<?php $this->load->view(ADMIN_URL.'/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/sumoselect.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
<!-- END sidebar -->
<?php
if($this->input->post()){
    foreach ($this->input->post() as $key => $value) {
        $$key = @htmlspecialchars($this->input->post($key));
    } 
} else {
    $FieldsArray = array('payment_id','payment_gateway_slug','display_name_en','display_name_fr','display_name_ar','sandbox_client_id','sandbox_client_secret','live_client_id','live_client_secret','test_publishable_key','test_secret_key','test_webhook_secret','live_publishable_key','live_secret_key','live_webhook_secret','enable_live_mode','sorting');
    foreach ($FieldsArray as $key) {
        $$key = @htmlspecialchars($edit_records->$key);
    }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label  = $this->lang->line('edit_payment_method');
    $form_action = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->payment_id));
}
/*else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('payment_method');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
}*/
?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('payment_methods'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/payment_method/view"><?php echo $this->lang->line('payment_methods'); ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label .' ('.ucfirst($payment_gateway_slug).')'; ?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_payment_method" name="form_payment_method" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <input type="hidden" id="payment_id" name="payment_id" value="<?php echo $payment_id;?>" />
                                    <input type="hidden" id="payment_gateway_slug" name="payment_gateway_slug" value="<?php echo $payment_gateway_slug;?>" />
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('english').' '.$this->lang->line('name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="display_name_en" id="display_name_en" value="<?php echo $display_name_en ?>" maxlength="249" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('french').' '.$this->lang->line('name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="display_name_fr" id="display_name_fr" value="<?php echo $display_name_fr ?>" maxlength="249" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('arabic').' '.$this->lang->line('name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="display_name_ar" id="display_name_ar" value="<?php echo $display_name_ar ?>" maxlength="249" class="form-control"/>
                                        </div>
                                    </div>                                    
                                    <?php if($payment_gateway_slug == 'paypal'){?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('sandbox_client_id'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="sandbox_client_id" id="sandbox_client_id" value="<?php echo $sandbox_client_id ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('sandbox_client_secret'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="sandbox_client_secret" id="sandbox_client_secret" value="<?php echo $sandbox_client_secret ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_mode');?></label>
                                            <div class="col-md-4">
                                                <a style="cursor:pointer;" onclick="onoffPaypal()">
                                                    <i class="fa fa-toggle-<?php if($enable_live_mode == 1) { echo "on"; } else{echo "off";} ?> fa-2x" id="on_off_toggle_paypal" style="vertical-align: bottom;"></i>
                                                </a>
                                                <input type="hidden" name="enable_live_mode" value="<?php echo $enable_live_mode; ?>" id="enable_live_mode_paypal">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_client_id'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_client_id" id="live_client_id" value="<?php echo $live_client_id ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_client_secret'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_client_secret" id="live_client_secret" value="<?php echo $live_client_secret ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                    <?php } else if ($payment_gateway_slug == 'stripe'){?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('test_publishable_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="test_publishable_key" id="test_publishable_key" value="<?php echo $test_publishable_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('test_secret_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="test_secret_key" id="test_secret_key" value="<?php echo $test_secret_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('test_webhook_secret').' '.$this->lang->line('signing_secret'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="test_webhook_secret" id="test_webhook_secret" value="<?php echo $test_webhook_secret ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_mode');?></label>
                                            <div class="col-md-4">
                                                <a style="cursor:pointer;" onclick="onoffStripe()">
                                                    <i class="fa fa-toggle-<?php if($enable_live_mode == 1) { echo "on"; } else{echo "off";} ?> fa-2x" id="on_off_toggle_stripe" style="vertical-align: bottom;"></i>
                                                </a>
                                                <input type="hidden" name="enable_live_mode" value="<?php echo $enable_live_mode; ?>" id="enable_live_mode_stripe">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_publishable_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_publishable_key" id="live_publishable_key" value="<?php echo $live_publishable_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_secret_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_secret_key" id="live_secret_key" value="<?php echo $live_secret_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_webhook_secret').' '.$this->lang->line('signing_secret'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_webhook_secret" id="live_webhook_secret" value="<?php echo $live_webhook_secret ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                    <?php } else if ($payment_gateway_slug == 'applepay'){?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('test_publishable_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="test_publishable_key" id="test_publishable_key" value="<?php echo $test_publishable_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 1) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_mode');?></label>
                                            <div class="col-md-4">
                                                <a style="cursor:pointer;" onclick="onoffApple()">
                                                    <i class="fa fa-toggle-<?php if($enable_live_mode == 1) { echo "on"; } else{echo "off";} ?> fa-2x" id="on_off_toggle_stripe" style="vertical-align: bottom;"></i>
                                                </a>
                                                <input type="hidden" name="enable_live_mode" value="<?php echo $enable_live_mode; ?>" id="enable_live_mode_stripe">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('live_publishable_key'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="live_publishable_key" id="live_publishable_key" value="<?php echo $live_publishable_key ?>" maxlength="249" class="form-control" <?php echo ($enable_live_mode == 0) ? 'readonly':''; ?>/>
                                            </div>
                                        </div>                                                                                
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('at_position'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" name="sorting" id="sorting" value="<?php echo $sorting ?>" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL;?>/payment_method/view"><?php echo $this->lang->line('cancel') ?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
var paymentSlug = '<?php echo $payment_gateway_slug; ?>';
jQuery(document).ready(function() {       
    Layout.init();
});
function onoffPaypal() {
    $("#on_off_toggle_paypal").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_paypal").hasClass("fa-toggle-off")){
        document.getElementById("enable_live_mode_paypal").value = "0";
        document.getElementById("sandbox_client_id").readOnly = false;
        document.getElementById("sandbox_client_secret").readOnly = false;
        document.getElementById("live_client_id").readOnly = true;
        document.getElementById("live_client_secret").readOnly = true;
    }
    if($("#on_off_toggle_paypal").hasClass("fa-toggle-on")){
        document.getElementById("enable_live_mode_paypal").value = "1";
        document.getElementById("sandbox_client_id").readOnly = true;
        document.getElementById("sandbox_client_secret").readOnly = true;
        document.getElementById("live_client_id").readOnly = false;
        document.getElementById("live_client_secret").readOnly = false;
    }
}
function onoffStripe() {
    $("#on_off_toggle_stripe").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_stripe").hasClass("fa-toggle-off")){
        document.getElementById("enable_live_mode_stripe").value = "0";
        document.getElementById("test_publishable_key").readOnly = false;
        document.getElementById("test_secret_key").readOnly = false;
        document.getElementById("test_webhook_secret").readOnly = false;
        document.getElementById("live_publishable_key").readOnly = true;
        document.getElementById("live_secret_key").readOnly = true;
        document.getElementById("live_webhook_secret").readOnly = true;
    }
    if($("#on_off_toggle_stripe").hasClass("fa-toggle-on")){
        document.getElementById("enable_live_mode_stripe").value = "1";
        document.getElementById("test_publishable_key").readOnly = true;
        document.getElementById("test_secret_key").readOnly = true;
        document.getElementById("test_webhook_secret").readOnly = true;
        document.getElementById("live_publishable_key").readOnly = false;
        document.getElementById("live_secret_key").readOnly = false;
        document.getElementById("live_webhook_secret").readOnly = false;
    }
}
function onoffApple() {
    $("#on_off_toggle_stripe").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_stripe").hasClass("fa-toggle-off")){
        document.getElementById("enable_live_mode_stripe").value = "0";
        document.getElementById("test_publishable_key").readOnly = false;               
        document.getElementById("live_publishable_key").readOnly = true;  
    }
    if($("#on_off_toggle_stripe").hasClass("fa-toggle-on")){
        document.getElementById("enable_live_mode_stripe").value = "1";
        document.getElementById("test_publishable_key").readOnly = true;   
        document.getElementById("live_publishable_key").readOnly = false;
    }
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>