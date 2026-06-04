<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
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
  $FieldsArray = array('content_id','entity_id','reason','reason_type','user_type');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label = $this->lang->line('title_reason_management_edit');
    $form_action = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('4')."/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label = $this->lang->line('title_reason_management_add');
    $form_action = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
}?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('title_reason_management')  ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home')  ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('title_reason_management')  ?></a>
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
                            <div class="caption"><?php echo $add_label;?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add_<?php echo $this->prefix ?>" name="form_add_<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                        <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('reason')  ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <textarea name="reason" id="reason" value="<?php echo $name;?>" maxlength="255" class="form-control" style="resize: none;"><?php echo $reason;?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('user_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="radio" name="user_type" class="user_type" id="user_type_admin" <?php if ((isset($user_type) && $user_type=="Admin") || empty($user_type)) echo "checked";?> value="Admin">&nbsp;<?php echo $this->lang->line('admin'); ?>&ensp;&ensp;
                                            <input type="radio" name="user_type" class="user_type" id="reason_type_driver" <?php if (isset($user_type) && $user_type=="Driver") echo "checked";?> value="Driver">&nbsp;<?php echo $this->lang->line('driver'); ?>&ensp;&ensp;
                                            <input type="radio" name="user_type" class="user_type" id="user_type_customer" <?php if (isset($user_type) && $user_type=="Customer") echo "checked";?> value="Customer">&nbsp;<?php echo $this->lang->line('customer'); ?>
                                        </div>
                                        <div class="col-md-9 col-md-offset-3">
                                            <div id="user_type_error"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('reason_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="radio" name="reason_type" id="reason_type_cancel" <?php if ((isset($reason_type) && $reason_type=="cancel") || empty($user_type)) echo "checked";?> value="cancel">&nbsp;<?php echo $this->lang->line('cancel'); ?>&ensp;&ensp;
                                            <span id="customer_display" class="display-none"><input type="radio" name="reason_type" id="reason_type_reject" <?php if (isset($reason_type) && $reason_type=="reject") echo "checked";?> value="reject">&nbsp;<?php echo $this->lang->line('reject'); ?></span>
                                        </div>
                                        <div class="col-md-9 col-md-offset-3">
                                            <div id="radiobtn_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn danger-btn theme-btn"><?php echo $this->lang->line('submit')  ?></button>
                                        <a class="btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel')  ?></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
</script>
<!-- hide reject radio button for customer -->
<script>
    $(document).ready(function(){
        if($("input[name='user_type']:checked").val() == "Customer"){
            $('#customer_display').hide(); 
        }else{
            $('#customer_display').show(); 
        }
    })
    $('.user_type').click(function(){
        if($(this).val() == "Customer"){
            $('#customer_display').hide();
            $('#reason_type_cancel').prop("checked", true);        }
        else{
            $('#customer_display').show();
        }
    })
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>