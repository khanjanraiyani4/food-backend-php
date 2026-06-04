<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<!-- Embed the intl-tel-input plugin -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
<style type="text/css">
    .iti{
        width: 100%;
    }
</style>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script> -->
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','first_name','last_name','email','mobile_number','phone_code','phone_number','user_type','restaurant_content_id','image','driver_temperature','is_masterdata','parent_user_id','role_id');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}

$module =  ($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver')?$this->lang->line('driver'):(($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin')?$this->lang->line('admin'):(($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent')?$this->lang->line('call_agents'):$this->lang->line('customer')));

/*if(($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin') && $this->session->userdata('AdminUserType') == 'Restaurant Admin'){
    $module = $this->lang->line('branch_admin');
}*/

if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$module;        
    //$form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    if($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id)).'/driver';
    }else if($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id)).'/admin';
    }else if($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id)).'/agent';
    }else{
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    }
}
else
{
    $add_label    = $this->lang->line('add').' '.$module;       
    //$form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    if($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add/driver";
    }else if($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add/admin";
    }else if($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent'){
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add/agent";
    }else{
        $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    }
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'), $this->session->userdata('AdminUserType'));
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo ($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver')?$this->lang->line('drivers'):(($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin')?$this->lang->line('admins'):(($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent')?$this->lang->line('call_agents'):$this->lang->line('customer'))); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php 
                                if($this->session->userdata('AdminUserType') == 'MasterAdmin'){
                                    echo ($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver')?'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/driver/>'.$this->lang->line('drivers').'</a>':(($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin')?'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/admin />'.$this->lang->line('admins').'</a>':(($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent')?('<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/agent>'.$this->lang->line('call_agents').'</a>'):('<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/view>'.$this->lang->line('customers').'</a>'))); 
                                }else{
                                     echo ($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver')?'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/driver/>'.$this->lang->line('drivers').'</a>':(($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin')?'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/admin />'.$this->lang->line('admins').'</a>':'<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/view>'.$this->lang->line('customers').'</a>'); 
                                }
                            ?>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div id="iframeloading" class="frame-load display-no" style= "display: none;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"/>
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('first_name')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="first_name" id="first_name" value="<?php echo $first_name;?>" data-required="1" class="form-control" maxlength='20'/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('last_name')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="last_name" id="last_name" value="<?php echo $last_name;?>" data-required="1" class="form-control" maxlength='20'/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_number')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="<?php echo $phone_code; ?>">
                                            <input type="tel" onblur="checkExist(this.value,'<?php echo $is_masterdata ?>')" name="mobile_number" id="mobile_number" value="<?php echo str_replace(" ","",$mobile_number);?>" data-required="1" class="form-control" placeholder=" " maxlength='12'/>
                                            <div class="phn_err"  style="display: none; color: red;"></div>
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_email')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email" id="email" onblur="checkEmail(this.value,'<?php echo $entity_id ?>','<?php echo $is_masterdata ?>')" value="<?php echo $email;?>" maxlength="50" data-required="1" class="form-control"/>
                                        </div>
                                        <div id="EmailExist"></div>
                                    </div>
                                    <?php if($user_type == 'User' || $this->uri->segment(4) == ''){ ?> 
                                        <input type="hidden" name="user_type" id="user_type" value="User">
                                        <input type="hidden" name="selected_role_name" id="selected_role_name" value="User">
                                    <?php } ?>
                                    <?php if($user_type == 'Agent' || $this->uri->segment(4) == 'agent'){ ?> 
                                        <input type="hidden" name="user_type" id="user_type" value="Agent">
                                        <input type="hidden" name="selected_role_name" id="selected_role_name" value="Agent">
                                    <?php } ?>
                                    <?php if($user_type == 'Driver' || $this->uri->segment(4) == 'driver'){ ?> 
                                        <input type="hidden" name="user_type" id="user_type" value="Driver">
                                        <input type="hidden" name="selected_role_name" id="selected_role_name" value="Driver">
                                        <div class="form-group" id="branch_div_id" style="display: block;">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('res_name'); ?></label>
                                            <div class="col-md-4">
                                                <select name="branch_entity_id[]" id="branch_entity_id" multiple="multiple" class="form-control sumo">
                                                    <!-- <option value=""><?php //echo $this->lang->line('select'); ?></option> -->
                                                    <?php if(!empty($restaurant)){
                                                        $restaurant_driver_map = (isset($restaurant_driver_map) && !empty($restaurant_driver_map)) ? $restaurant_driver_map : array();
                                                        foreach ($restaurant as $key => $value) { ?>
                                                           <option value="<?php echo $value->content_id ?>" <?php echo (in_array($value->content_id, $restaurant_driver_map))?'selected':''; ?> ><?php echo $value->name ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('driver_temperature'); ?></label>
                                            <div class="col-md-4">
                                                <input type="text" name="driver_temperature" id="driver_temperature" data-required="1" value="<?php echo $driver_temperature ?>" class="form-control"/>
                                            </div>
                                        </div>
                                    <?php } if($user_type == 'Restaurant Admin' || $user_type == 'Branch Admin' || $this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin'){ 
                                        $selected_role_name = ''; ?>
                                        <div class="form-group">   
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('user_type')?> <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="user_type" id="user_type" onchange="getParentName();" >
                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                    <?php foreach ($roles as $roleskey => $rolesvalue) { 
                                                        if($selected_role_name == '' && $role_id == $rolesvalue->role_id) {
                                                            $selected_role_name = $rolesvalue->role_name;
                                                        } ?>
                                                        <option value="<?php echo $rolesvalue->role_id; ?>" role-name-attr = '<?php echo $rolesvalue->role_name; ?>' <?php echo ($role_id == $rolesvalue->role_id)?"selected":""?> ><?php echo $rolesvalue->role_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php /* if($this->session->userdata('AdminUserType') == 'MasterAdmin'){ ?>
                                                    <select class="form-control" name="user_type" id="user_type" onchange="getParentName(this.value);" > <!-- onchange="getBrachAdmin(this.value);" -->
                                                        <option value=""><?php echo $this->lang->line('select')?></option>
                                                        <?php foreach ($usertypes as $key => $value) {?>                                  
                                                            <option value="<?php echo $key;?>" <?php echo ($user_type==$key)?"selected":""?>><?php echo $value;?></option>    
                                                        <?php } ?>
                                                    </select>
                                                <?php } if ($this->session->userdata('AdminUserType') == 'Restaurant Admin'){ ?>
                                                    <select class="form-control" name="user_type" id="user_type">
                                                        <option value=""><?php echo $this->lang->line('select')?></option>
                                                        <?php foreach ($usertypes as $key => $value) {?>                                  
                                                            <option value="<?php echo $key;?>" <?php echo ($user_type==$key || $key=='BranchAdmin')?"selected":""?>><?php echo $value;?></option> 
                                                        <?php } ?>
                                                    </select>
                                                <?php } */ ?>
                                            </div>
                                        </div> 
                                        <input type="hidden" name="selected_role_name" id="selected_role_name" value="<?php echo $selected_role_name; ?>">
                                    <?php } ?>
                                    <?php //if($this->session->userdata('AdminUserType') == 'MasterAdmin' && ($user_type == 'Branch Admin' || $this->uri->segment(4) == 'admin' || $user_type == 'Restaurant Admin')) { ?>
                                    <?php if($user_type == 'Branch Admin' || $this->uri->segment(4) == 'admin' || $user_type == 'Order viewer' || $user_type == 'Restaurant Admin') { ?>
                                        <input type="hidden" name="loggedin_user_type" id="loggedin_user_type" value="MasterAdmin">
                                        <div class="form-group" id="parent_id_div" style="<?php echo ($user_type == 'Restaurant Admin' || $user_type == 'Admin' || $user_type == 'Order viewer')?'display: none' : 'display: block' ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('parent_name'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <select name="parent_id" id="parent_id" class="form-control sumo">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php if(!empty($parent_list)){
                                                        foreach ($parent_list as $key => $parent_dtl) { ?>
                                                           <option value="<?php echo $parent_dtl->entity_id ?>" <?php echo ($parent_dtl->entity_id == $parent_user_id)?"selected":"" ?>><?php echo $parent_dtl->first_name.' '.$parent_dtl->last_name ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                                <div class="parent_id_required"></div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php /*
                                    if($user_type != 'Driver' && $this->uri->segment(4) != 'driver'){ ?>
                                    <div class="form-group" id="branch_div_id" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? (strtolower($user_type) == 'branch admin') ? 'display: block':'display: none' : 'display: none' ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="branch_entity_id" id="branch_entity_id" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                       <option value="<?php echo $value->content_id ?>" <?php echo ($value->content_id == $branch_adminval->restaurant_content_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>    
                                    <?php } */ ?>
                                    <?php if(($this->uri->segment(4) != '' && $user_type != 'User') && ($this->uri->segment(4) != 'agent' && $this->uri->segment(5) != 'agent' && $user_type != 'Agent')) { ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image'); ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                </label>
                                                <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>&ensp;
                                                <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <ul>
                                                            <li><?php echo $this->lang->line('img_allow') ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size') ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'700px * 388px.'; ?></li>
                                                        </ul>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='200' width='290' class="display-no"/>
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '' && file_exists(FCPATH.'uploads/'.$image)) { ?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                    <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>" style="max-width: 100%;">
                                            <?php }  ?>
                                        </div>
                                    </div><?php } ?>
                                    <?php if($entity_id){ ?>
                                        <h3><?php echo $this->lang->line('change_pass')?></h3>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('password')?> <?php echo ($entity_id)?'':'<span class="required">*</span>' ?></label>
                                        <div class="col-md-4">
                                            <input type="password" name="password" id="password" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('confirm_pass')?><?php echo ($entity_id)?'':'<span class="required">*</span>' ?></label>
                                        <div class="col-md-4">
                                            <input type="password" name="confirm_password" id="confirm_password" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    </div>    
                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="submit_page" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?> id="submit_page" value="Submit" class="btn btn-success danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                            <?php if($this->uri->segment(4) == 'admin' || $this->uri->segment(5) == 'admin'){?>
                                                <a class="btn btn-success danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/admin"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php }elseif($this->uri->segment(4) == 'driver' || $this->uri->segment(5) == 'driver'){ ?>
                                                <a class="btn btn-success danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/driver"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php }elseif($this->uri->segment(4) == 'agent' || $this->uri->segment(5) == 'agent'){ ?>
                                                <a class="btn btn-success danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/agent"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php } else { ?>
                                                <a class="btn btn-success danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                            <?php } ?>
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
    $('.sumo').SumoSelect({search: true, selectAll: true,captionFormatAllSelected: '{0} <?php echo $this->lang->line('selected');?>!',locale: ['OK', 'Cancel', "<?php echo $this->lang->line('all').' '.$this->lang->line('select_');?>"], searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>...", placeholder : "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
});
//check phone number exist
function checkExist(mobile_number,is_masterdata){
    var entity_id = $('#entity_id').val();
    var phone_code = $('#phone_code').val();
    var user_type = $('#selected_role_name').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/users/checkExist",
    data: 'mobile_number=' + mobile_number +'&entity_id='+entity_id+'&phone_code='+phone_code+'&selected_role_name='+user_type,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#phoneExist').show();
        $('#phoneExist').html("<?php echo $this->lang->line('phone_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#phoneExist').html("");
        $('#phoneExist').hide();        
        if(is_masterdata!='1')
        {
            $(':input[type="submit"]').prop("disabled",false);    
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#phoneExist').show();
      $('#phoneExist').html(errorThrown);
    }
  });
}
// admin email exist check
function checkEmail(email,entity_id,is_masterdata)
{
  var user_type = $('#selected_role_name').val();
  $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/users/checkEmailExist",
    data: 'email=' + email +'&entity_id='+entity_id+'&selected_role_name='+user_type,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html("<?php echo $this->lang->line('alredy_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();        
        if(is_masterdata!='1')
        {
            $(':input[type="submit"]').prop("disabled",false);    
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}
function readURL(input) {
    $('#submit_page').prop("disabled",false);
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    var file_size = fileInput.size;
    if(input.files[0].size <= 500000){ // 500 KB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                    $("#old").hide();
                    $('#errormsg').html('').hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('img_allow') ?>").show();
            $('#submit_page').prop("disabled",true);
            $('#Image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $('#submit_page').prop("disabled",true);
        $('#Image').val('');
        $("#old").show();
    }
}

$('#Image').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#Image')[0].files[0].name;
  $(this).prev('label').text(file);
});
/*function getBrachAdmin(value)
{
    if(value=='BranchAdmin')
    {
        $('#branch_div_id').attr('style','display: block;');
    }
    else
    {
        $('#branch_div_id').attr('style','display: none;');
        $('#branch_entity_id').val('');
    }
}*/
function getParentName()
{
    var selected_user_type = $('#user_type option:selected').attr('role-name-attr');
    $('#selected_role_name').val(selected_user_type);
    if(selected_user_type == 'Branch Admin') {
        $('#parent_id_div').attr('style','display: block;');
    } else {
        $('#parent_id_div').attr('style','display: none;');
        $('#parent_id').val('');
    } 
}
</script>

<script type="text/javascript">
var onedit_iso = '';
<?php if($phone_code) {
    $onedit_iso = $this->common_model->getIsobyPhnCode($phone_code); ?>
    onedit_iso = <?php echo json_encode($onedit_iso); ?>;
<?php } 
$iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>

var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
var initial_preferred_iso = (onedit_iso)?onedit_iso:default_iso;

// Initialize the intl-tel-input plugin
const phoneInputField = document.querySelector("#mobile_number");
const phoneInput = window.intlTelInput(phoneInputField, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    formatOnDisplay:false,
    autoPlaceholder:"polite",
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js'
        //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
});

$(document).on('input','#mobile_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});
$(document).on('focusout','#mobile_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});
phoneInputField.addEventListener("close:countrydropdown",function() {
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});

</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>