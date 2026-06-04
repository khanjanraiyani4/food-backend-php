<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','table_no','restaurant_id','capacity','qr_code');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('table');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('5').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('table');       
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
} ?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('tables') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/table/view"><?php echo $this->lang->line('tables') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal horizontal-form-deal" enctype="multipart/form-data" >
                                <div id="iframeloading" style= "display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" style="top: 50%; position: relative; left: 50%;"  />
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger alerttimerclose"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger alerttimerclose">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="restaurant_id" class="form-control sumo" id="restaurant_id" onchange="getCurrency(this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                       <option value="<?php echo $value->content_id ?>" <?php echo ($value->content_id == $restaurant_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('table_no') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="hidden" id="call_from" name="call_from" value="CI_callback" />
                                            <input type="text" name="table_no" id="table_no" oninput="checkTableNameExist(this.value);" value="<?php echo ($table_no)?$table_no:'' ?>" data-required="1" class="form-control"/>
                                            <div id="tablename_exist" class="text-danger"></div>
                                        </div>
                                    </div> 
                                    <div class="form-group capacity" style="display: block">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('capacity') ?> <span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="capacity" id="capacity" value="<?php echo ($capacity)?$capacity:'' ?>" maxlength="19" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <?php if(isset($qr_code) && $qr_code != '' && file_exists(FCPATH.'uploads/'.$qr_code)) {?>
                                        <div class="form-group" id="old">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('qr_code') ?> </label>
                                            <div class="col-md-4">
                                                <input type="hidden" name="qr_code" id="qr_code" value="<?php echo $qr_code ?>"/>
                                                <span class="block"><?php echo $this->lang->line('selected_image'); ?></span>
                                                <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$qr_code;?>">
                                            </div>
                                        </div>
                                    <?php }  ?>
                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn theme-btn"><?php echo $this->lang->line('submit'); ?></button>
                                            <a class="btn btn-danger danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel'); ?></a>
                                        </div>
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
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
});
$(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
function checkTableNameExist(value)
{
    var restaurant_id = $('#restaurant_id').val();    
    var entity_id = $('#entity_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/table/checkTableNameExist/<?php echo $this->uri->segment(4) ?>",
    data: 'table_no=' + value +'&call_from=ajax_call&entity_id='+entity_id+'&restaurant_id='+restaurant_id,
    cache: false,
    success: function(html)
    {
      if(html > 0){
        $('#tablename_exist').show();
        $('#tablename_exist').html("<?php echo $this->lang->line('tablename_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#tablename_exist').html("");
        $('#tablename_exist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#tablename_exist').show();
      $('#tablename_exist').html(errorThrown);
    }
  });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>