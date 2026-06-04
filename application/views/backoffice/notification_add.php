<?php $this->load->view(ADMIN_URL.'/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
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
  $FieldsArray = array('entity_id','notification_title','notification_description');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editNotificationDetail->$key);
  }
}
if(isset($editNotificationDetail) && $editNotificationDetail !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('label_notification');        
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($editNotificationDetail->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('label_notification');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
}
if(!empty($users)){
    $selected_users_drivers = (!empty($NotificationDrivers)) ? array_unique( array_merge( $NotificationUsers , $NotificationDrivers ) ) : array();
    $selected_userids = implode(",", $selected_users_drivers);
} ?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('notification'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/notification/view"><?php echo $this->lang->line('notification'); ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add_notification" name="form_add_notification" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>

                                    <?php //New code add as per new CR :: Start ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant');?></label>
                                        <div class="col-md-4">
                                            <select name="restaurant" class="form-control sumo select_restaurant" id="restaurant">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant_arr)){
                                                    foreach($restaurant_arr as $key => $value) { ?>
                                                      <option value="<?php echo $value->entity_id;?>###<?php echo $value->latitude;?>###<?php echo $value->longitude;?>"><?php echo ucfirst($value->name);?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('distance');?></label>
                                        <div class="col-md-4">                                            
                                            <input type="text" name="distance" id="distance" value="" maxlength="5" class="form-control QtyNumberval"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">&nbsp;</label>
                                        <div class="col-md-4">
                                            <input type="radio" name="option_type" id="option_typecity" value="city" checked >&nbsp;&nbsp;<b><?php echo $this->lang->line('city') ?></b>&ensp;
                                            <input type="radio" name="option_type" id="option_typezip" value="zip">&nbsp;&nbsp;<b><?php echo $this->lang->line('zipcode') ?></b>&ensp;
                                        </div>
                                    </div>
                                    <div class="form-group" id="citydropdown_id">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city');?></label>
                                        <div class="col-md-4">
                                            <select name="city_name[]" multiple="multiple" class="form-control sumo select_city_name" id="city_name" onchange="getCustomerList('city')">
                                                <?php if(!empty($cities_arr)){
                                                    foreach($cities_arr as $key => $value) { ?>
                                                      <option value="<?php echo $value['city'] ?>"><?php echo ucfirst($value['city']);?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" id="zipdropdown_id" style="display: none;">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('zipcode');?></label>
                                        <div class="col-md-4">
                                            <select name="zipcode[]" multiple="multiple" class="form-control sumo select_zipcode_id" id="zipcode_id" onchange="getCustomerList('zipcode')">
                                                <?php if(!empty($zipcode_arr)){
                                                    foreach($zipcode_arr as $key => $value) { ?>
                                                      <option value="<?php echo $value['zipcode'] ?>"><?php echo $value['zipcode'];?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php //New code add as per new CR :: End ?>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('customers'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">     
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <select name="user_id[]" placeholder="<?php echo $this->lang->line('select_').' '.$this->lang->line('here') ?>" multiple="multiple" class="form-control sumo select_user_id" id="user_id">
                                                <?php if(!empty($users['users'])){ ?>
                                                    <optgroup label="<?php echo $this->lang->line('all_customers') ?>">
                                                    <?php foreach ($users['users'] as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo (isset($NotificationUsers) && !is_null($NotificationUsers) && in_array($value->entity_id, $NotificationUsers))?'selected':''; ?> ><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                    <?php } ?>
                                                    </optgroup>
                                                <?php } ?>
                                                <?php if(!empty($users['drivers'])){ ?>
                                                    <optgroup label="<?php echo $this->lang->line('all_drivers') ?>">
                                                    <?php foreach ($users['drivers'] as $driver_key => $driver_value) { ?>
                                                       <option value="<?php echo $driver_value->entity_id ?>" <?php echo (isset($NotificationDrivers) && !is_null($NotificationDrivers) && in_array($driver_value->entity_id, $NotificationDrivers))?'selected':''; ?> > <?php echo $driver_value->first_name.' '.$driver_value->last_name ?> </option>
                                                    <?php } ?>
                                                    </optgroup>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="selected_userids" id="selected_userids" value="<?php echo $selected_userids; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('label_notification');?>&nbsp;<?php echo $this->lang->line('title'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="text" name="notification_title" id="notification_title" value="<?php echo utf8_decode($notification_title);?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('label_notification');?>&nbsp;<?php echo $this->lang->line('message'); ?></label>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="notification_description" id="notification_description" rows="6" data-required="1" ><?php echo utf8_decode($notification_description);?></textarea>                                           
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('save_noti'); ?> <?php echo $this->lang->line('only_for_customers'); ?></label>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="save" id="save" value="1">                             
                                        </div>
                                    </div>                                       
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submitNotification" id="submitNotification" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL;?>/notification/view"><?php echo $this->lang->line('cancel') ?></a>
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
<!-- <script src="<?php //echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script> -->
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    $('.sumo').SumoSelect({selectAll:true, forceCustomRendering: true, captionFormatAllSelected: '{0} <?php echo $this->lang->line('selected');?>!',locale: ['OK', 'Cancel', "<?php echo $this->lang->line('all').' '.$this->lang->line('select_');?>"], search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
    
    $("html").on('click', '.select-all', function(){
        var myObj = $(this).closest('.SumoSelect.open').children()[0];
        if ($(this).hasClass("selected")) {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", true);
            $(myObj)[0].sumo.selectAll();
            $(this).parent().find("ul.options>li").addClass("selected");
        }
        else {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", false);
            $(myObj)[0].sumo.unSelectAll();
            $(this).parent().find("ul.options>li").removeClass("selected");
        }
    });
});
$('select#user_id').on('sumo:closed', function(sumo) {
    var userids_str = '';
    $('#user_id option:selected').each(function(i) {
        var selected_val = $(this).val();
        if(i==0){
            userids_str += selected_val;
        } else {
            userids_str += ','+selected_val;
        }
    });
    $('#selected_userids').val(userids_str);
});
$("#form_add_notification").submit(function() {
    if ($("#form_add_notification").valid()) {
        $("#user_id").attr("disabled", true);
    }
});
$("#option_typecity").click(function(){
    $("#citydropdown_id").css("display", "");    
    $('#zipdropdown_id').hide();
    //Code for reset value :: Start
    $("#zipcode_id").val('');    
    $('#zipcode_id')[0].sumo.unSelectAll();
    $('.select_zipcode_id')[0].sumo.reload();
    //Code for reset value :: End
});
$("#option_typezip").click(function(){
    $("#zipdropdown_id").css("display", "");    
    $('#citydropdown_id').hide();
    //Code for reset value :: Start
    $("#city_name").val('');    
    $('#city_name')[0].sumo.unSelectAll();
    $('.select_city_name')[0].sumo.reload();
    //Code for reset value :: End
});
function getCustomerList(list_for)
{
    var data_array = [];
    if(list_for=='city')
    {
        var data_array = $('#city_name').val();
    }
    else if(list_for=='zipcode')
    {
        var data_array = $('#zipcode_id').val();
    }    
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getCustomerList',
      data : {'data_array':data_array,'list_for':list_for},
      success: function(response) {
        $('.select_user_id').empty().append(response);
        $('.select_user_id')[0].sumo.reload();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
$('input.QtyNumberval').on('input', function() {        
    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
}); 
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>