<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/jquery.timepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- Embed the intl-tel-input plugin -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
<style>
    .checkbox.chk-clicksame{
        display: inline;
        padding-left: 10px;
        vertical-align: text-bottom;
    }
    .ophrs.form-control{
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .clhrs.form-control{
        margin-left: 10px;
        margin-bottom: 10px;
    }
    .order-mode-checkbox{
        vertical-align: top;
        padding-left: 5px;
        padding-right: 20px;
        font-size: 14px;
    }
</style>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
//get System Option Data
/*$this->db->select('OptionValue');
$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue); */
$food_typechkarr = array();
$edit_timingsarray = array();
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    if($key=='food_type') {
        $food_typechkarr = $this->input->post("food_type");
    } else if($key=='branch_entity_id'){
        $name = $this->input->post('res_name');
        $$key = @htmlspecialchars($this->input->post($key)); 
    } if($key=='message') {
        $about_restaurant = $this->input->post("message");
    } else if($key=='timings'){
        $newTimingArr=array();
        foreach($this->input->post("timings") as $ky=>$value) {
            if(!empty($value['off']) && (empty($value['open']) && empty($value['close']))) {
                $newTimingArr[$ky]['open'] = '';
                $newTimingArr[$ky]['close'] = '';
                $newTimingArr[$ky]['off'] = '0';
            } else {
                if(!empty($value['open']) && !empty($value['close'])) {
                    $newTimingArr[$ky]['open'] =$value['open'];
                    $newTimingArr[$ky]['close'] = $value['close'];
                    $newTimingArr[$ky]['off'] = '1';
                } else {
                    $newTimingArr[$ky]['open'] = '';
                    $newTimingArr[$ky]['close'] = '';
                    $newTimingArr[$ky]['off'] = '0';
                }
            }
        }
        $edit_timingsarray = $newTimingArr;
    } else if($key=='order_mode'){
        $$key = implode(",", $this->input->post("order_mode"));
    } else{
        $$key = @htmlspecialchars($this->input->post($key));
    }
  } 
} else {
  $FieldsArray = array('branch_admin_id','branch_entity_id','content_id','entity_id','name','phone_number','email','capacity','address','landmark','latitude','longitude','state','country','city','zipcode','amount_type','amount','enable_hours','timings','image','food_type','driver_commission','currency_id','restaurant_slug','is_service_fee_enable','service_fee_type','service_fee','allow_event_booking','contractual_commission','restaurant_owner_id','is_masterdata','phone_code','is_printer_available','printer_paper_height','printer_paper_width','order_mode','enable_table_booking','table_booking_capacity','table_online_availability','table_minimum_capacity','allowed_days_table','event_online_availability','event_minimum_capacity','background_image','about_restaurant','contractual_commission_type','creditcard_fee_type','creditcard_fee','is_creditcard_fee_enable','allow_scheduled_delivery','allowed_days_for_scheduling','restaurant_rating','restaurant_rating_count','contractual_commission_type_delivery','contractual_commission_delivery');
  
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('title_admin_restaurant');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    if ($food_type != '') {
        $food_typechkarr = explode(',' , $food_type);
    }
}
else
{
    if($restaurant_owner_idval && intval($restaurant_owner_idval)>0){
        $restaurant_owner_id = $restaurant_owner_idval;    
    }
    $add_label    = $this->lang->line('add').' '.$this->lang->line('title_admin_restaurant');       
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'));
$available_content_id = ($content_id)?$content_id:$this->uri->segment('5');
if($available_content_id){
   //check if add branch/add restaurant should be enabled.
    $enable_arr = $this->restaurant_model->checkResOrBranch($available_content_id);
}
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('restaurant') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('restaurant') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div id="iframeloading" class="frame-load display-no">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
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
                                    <?php if($this->session->userdata('AdminUserType') == 'Branch Admin'){ ?>
                                        <input type="hidden" name="add_res_branch" value="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0') ? 'branch' : 'res' ?>">
                                        <input type="hidden" name="branch_entity_id" value="<?php echo $edit_records->branch_entity_id ?>">
                                    <?php } ?>
                                    <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin'){ ?>
                                        <div class="form-group">   
                                            <!-- <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant_admin') ?><span class="required">*</span></label> -->
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('branch_admin') ?></label>
                                            <div class="col-md-4">
                                                <select class="form-control sumo" name="branch_admin_id" id="branch_admin_id">
                                                     <option value=""><?php echo $this->lang->line('please'); ?> <?php echo $this->lang->line('select'); ?></option>
                                                <?php if(!empty($branchadmin)){
                                                    foreach ($branchadmin as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $branch_admin_id)?"selected":"" ?>><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- add restaurant or branch : 28jan2021 start -->
                                        <div class="form-group">   
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('add_res_branch') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <?php $disable_branch = ($enable_arr['isbranch_or_res']=='is_res')?'disabled':'';
                                                $disable_res = (isset($enable_arr) && $enable_arr['isbranch_or_res']=='is_branch')?'disabled':''; ?>
                                                <input type="radio" <?php echo (!empty($branch_entity_id) && $branch_entity_id == '0')?'checked':'' ?> name="add_res_branch" id="radioTrue" value="res" onclick="checkResNameExist()" class="add_res_branch" <?php echo $disable_res ?> <?php echo ($disable_res=='')?'checked':'' ?>  > <label for="radioTrue"><?php echo $this->lang->line('add').' '.$this->lang->line('title_admin_restaurant'); ?></label>
                                                <br>
                                                <input type="radio" <?php echo (!empty($branch_entity_id) && $branch_entity_id != '0')?'checked':'' ?>  name="add_res_branch" id="radioFalse" value="branch" onclick="checkResNameExist()" class="add_res_branch" <?php echo $disable_branch ?> <?php echo ($disable_res=='disabled')?'checked':'' ?> > <label for="radioFalse"><?php echo $this->lang->line('add').' '.$this->lang->line('branch'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group res-list" style="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0')?'display: block':'display: none'; ?>;" >
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('res_list'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <select name="branch_entity_id" id="branch_entity_id" class="form-control sumo">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php if(!empty($restaurant)){
                                                        foreach ($restaurant as $key => $value) { ?>
                                                           <option value="<?php echo $value->content_id ?>" <?php echo ( isset($edit_records->branch_entity_id) ? ($value->content_id == $edit_records->branch_entity_id)?"selected":"":"") ?>><?php echo $value->name ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group branch-name" style="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0')?'display: block':'display: none'; ?>;" >
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('branch_name'); ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                                <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $enable_hours ?>">
                                                <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                                <input type="text" name="branch_name" id="branch_name"  value="<?php echo $name; ?>" maxlength="100" data-required="1" class="form-control"/>
                                            </div>
                                        </div> -->
                                        <!-- add restaurant or branch : 28jan2021 end -->
                                    <?php } ?>
                                    <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin'){ ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant_owner') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="hidden" name="oldrestaurant_owner_id" id="oldrestaurant_owner_id" value="<?php echo $restaurant_owner_id ?>">
                                                <select class="form-control sumo" name="restaurant_owner_id" id="restaurant_owner_id">
                                                    <option value=""><?php echo $this->lang->line('please_select') ?></option>
                                                <?php if(!empty($restaurant_admins)){
                                                    foreach ($restaurant_admins as $key => $restaurant_admin) { ?>
                                                       <option value="<?php echo $restaurant_admin->entity_id ?>" <?php echo ($restaurant_admin->entity_id == $restaurant_owner_id)?"selected":"" ?>><?php echo $restaurant_admin->first_name.' '.$restaurant_admin->last_name ?></option>
                                                <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                    <input type="hidden" id="call_from" name="call_from" value="CI_callback" />
                                    <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                    <input type="hidden" id="restaurant_slug" name="restaurant_slug" value="<?php echo ($restaurant_slug)?$restaurant_slug:'';?>" />
                                    <?php if($this->session->userdata('AdminUserType') == 'Branch Admin'){ ?>
                                    <div class="form-group branch-name" style="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0')?'display: block':'display: none'; ?>;" >
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name'); ?></label>
                                        <div class="col-md-4">                                            
                                            <input type="text" value="<?php echo $edit_records->parent_res; ?>" readonly class="form-control"/>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <div class="form-group branch-name" style="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0')?'display: block':'display: none'; ?>;" >
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('branch_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">                      
                                            <input type="text" name="branch_name" id="branch_name" oninput="checkResNameExist(this.value)" value="<?php echo (!empty($branch_entity_id) && $branch_entity_id != '0') ? $name : ''; ?>" maxlength="100" data-required="1" class="form-control"/>
                                            <div id="branch_name_exist" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group res-name" style="<?php echo ($branch_entity_id == '0' || $this->uri->segment('3') == 'add')?'display: block':'display: none'; ?>;">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="res_name" id="res_name" oninput="checkResNameExist(this.value)" value="<?php echo ($branch_entity_id == '0' || $this->uri->segment('3') == 'add')? $name : ''; ?>" maxlength="249" data-required="1" class="form-control"/>
                                            <div id="res_name_exist" class="text-danger"></div>
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_number') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="<?php echo $phone_code; ?>">
                                            <input type="tel" name="phone_number" id="phone_number" value="<?php echo $phone_number;?>" data-required="1" maxlength='12' class="form-control" />
                                            <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                                <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('phoneuse_guide') ?>
                                                </span>
                                            </div>
                                            <div id="phoneExist"></div>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_email') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email" id="email"  value="<?php echo $email;?>" maxlength="50" data-required="1" class="form-control" onblur="checkEmail(this.value,'<?php echo $entity_id ?>','<?php echo $is_masterdata ?>')"/>
                                        </div>
                                        <div id="EmailExist"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('banner_image') ?></label>
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
                                            <img id="preview" height='100' width='150' class="display-no"/>
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '' && file_exists(FCPATH.'uploads/'.$image)) {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('background_image') ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="background_image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                </label>
                                                <input type="file" name="background_image" id="background_image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURLforBackgroundImage(this)"/>&ensp;
                                                <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <ul>
                                                            <li><?php echo $this->lang->line('img_allow') ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size') ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'1920px * 400px.'; ?></li>
                                                        </ul>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="error display-no" id="background_img_errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview_background_img" height='100' width='150' class="display-no"/>
                                            <input type="hidden" name="uploaded_background_image" id="uploaded_background_image" value="<?php echo isset($background_image)?$background_image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old_background_img">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($background_image) && $background_image != '' && file_exists(FCPATH.'uploads/'.$background_image)) {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$background_image;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="address" id="address" value="<?php echo $address ?>" maxlength="255" placeholder="<?php echo $this->lang->line('enter_location') ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('latitude') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude ?>" maxlength="50"/>
                                        <input type="hidden"  name="default_latitude" id="default_latitude" value="" />
                                        </div>
                                        <a href="#basic" data-toggle="modal" class="btn red default"> <?php echo $this->lang->line('pick_lat_long')?> </a>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('longitude') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude ?>" maxlength="50"/>
                                        <input type="hidden"  name="default_longitude" id="default_longitude" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('postal_code') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?php echo $zipcode ?>" minlength="5" maxlength="6" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('country') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="country" id="country" value="<?php echo $country; ?>" maxlength="50"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('state') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                             <input type="text" class="form-control" name="state" id="state" value="<?php echo $state ?>" maxlength="50" />
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city ?>" maxlength="50"/>
                                        </div>
                                    </div>
                                    <?php  ?> <div class="form-group">
                                        <label class="control-label col-md-3" ><?php echo $this->lang->line('currency')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <?php $currency = (isset($res_currency_id))?$res_currency_id:$currency_id; ?>
                                            <?php $point = "style='pointer-events: none;'";?>
                                            <select class="form-control sumo currency_id" name="currency_id" id="currency_id" <?php //echo ($currency)?"readonly ".$point:""?>  >
                                                <option value=""><?php echo $this->lang->line('select')?></option>
                                                <?php if (!empty($currencies)) {
                                                    foreach ($currencies as $key => $value) {?>                                  
                                                    <option value="<?php echo $value['currency_id'];?>" <?php echo ($currency==$value['currency_id'])?"selected":""?>><?php echo $value['country_name'].' - '.$value['currency_code'];?></option>    
                                                    <?php } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div> <?php  ?>
                                    <!-- foodtype 15-12-2020 start -->
                                    <div class="form-group">
                                       <label class="control-label col-md-3"><?php echo $this->lang->line('food_type'); ?><span class="required">*</span></label>
                                       <div class="col-md-4">
                                           <select name="food_type[]" multiple="" class="form-control sumo required food_type" id="food_type" placeholder="<?php echo $this->lang->line('select_').' '.$this->lang->line('here') ?>">
                                                <?php if(!empty($food_typearr)){
                                                   foreach ($food_typearr as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo in_array($value->entity_id, $food_typechkarr)?'selected':'' ?> ><?php echo $value->name ?></option>    
                                               <?php } } ?>
                                           </select>
                                       </div>
                                        <?php if(in_array('food_type~add',$this->session->userdata("UserAccessArray"))) { ?>
                                            <a class="btn red default" href="javascript:void(0);" onclick="food_typeopenfn('<?=$this->uri->segment('4');?>')"><?php echo $this->lang->line('title_food_type_add') ?></a>
                                        <?php } ?> 
                                    </div>     
                                    <?php /* ?>
                                    <!-- foodtype 15-12-2020 end -->                                     
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('food_type') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="radio" name="is_veg" id="is_veg" value="1" checked="" <?php echo ($is_veg)?($is_veg == '1')?'checked':'':'checked' ?>><?php echo $this->lang->line('veg') ?>
                                            <input type="radio" name="is_veg" id="non-veg" value="0" <?php echo ($is_veg == '0')?'checked':'' ?>><?php echo $this->lang->line('non_veg') ?>
                                            <input type="radio" name="is_veg" id="non-veg" value="" <?php echo ($is_veg == '')?'checked':'' ?>><?php echo $this->lang->line('both') ?>
                                        </div>
                                    </div> <?php */ ?>
                                    <?php if($this->session->userdata('AdminUserType') == "MasterAdmin") { ?>
                                        <!--service tax changes start-->
                                        <div class="form-group">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax_type') ?><span class="required">*</span></label>  
                                            <div class="col-sm-4">
                                                <input type="radio" name="amount_type" id="MPercentage" <?php if (isset($amount_type) && $amount_type=="Percentage") echo "checked";?> value="Percentage" checked="checked">&nbsp;&nbsp;<b><?php echo $this->lang->line('percentage') ?></b>&ensp;
                                                <input type="radio" name="amount_type" id="MAmount" <?php if (isset($amount_type) && $amount_type=="Amount") echo "checked";?> value="Amount" >&nbsp;&nbsp;<b><?php echo $this->lang->line('amount') ?></b>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax') ?><span class="required">*</span></label>
                                            <div class="col-sm-8 form-markup">
                                                <input type="text" name="amount" id="amount" value="<?php echo $amount ?>" maxlength="10" data-required="1" class="form-control" style="display: inline-block;width: 50%"/>&ensp;&ensp;
                                                <label id="Percentage"><?php echo $this->lang->line('percentage') ?> (%)</label>
                                                <label id="Amount" style="display:none"><?php echo $this->lang->line('amount') ?> ($)</label>
                                                <div class="service-tax-error"></div>
                                            </div>  
                                        </div>
                                        <!--service tax changes end-->
                                        <!--service fee changes start-->
                                        <div class="form-group">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee_enable') ?> <span class="required">*</span></label>
                                            <div class="col-sm-4">
                                                <a style="cursor:pointer;" id="servicefee_toggle" onclick="serviceFeeOnOff()" >
                                                    <i class="fa fa-toggle-<?php if(isset($is_service_fee_enable) && $is_service_fee_enable=="0") { echo "off"; } else{echo "on";} ?> fa-2x" id="on_off_toggle" style="vertical-align: bottom;" ></i>
                                                </a>
                                                <input type="hidden" name="is_service_fee_enable" id="is_service_fee_enable" value="<?php echo (isset($is_service_fee_enable) && $is_service_fee_enable=="0") ? '0' : '1'; ?>">
                                                <!-- <input type="checkbox" name="is_service_fee_enable" id="is_service_fee_enable" <?php if (isset($is_service_fee_enable) && $is_service_fee_enable=="1") echo "checked";?> value="1"> -->
                                            </div>
                                        </div>
                                        <div class="form-group">  
                                            <label class="control-label col-md-3" for="s_percentage"><?php echo $this->lang->line('service_fee_type') ?> <span class="required">*</span></label>  
                                            <div class="col-sm-4">
                                                <input type="radio" name="service_fee_type" id="s_percentage" <?php if (isset($service_fee_type) && $service_fee_type=="Percentage") echo "checked";?> value="Percentage" checked="checked" <?php if (isset($is_service_fee_enable) && $is_service_fee_enable=="0") echo "disabled";?>>&nbsp;&nbsp;<b><?php echo $this->lang->line('percentage') ?></b>&ensp;
                                                <input type="radio" name="service_fee_type" id="s_amount" <?php if (isset($service_fee_type) && $service_fee_type=="Amount") echo "checked";?> value="Amount" <?php if (isset($is_service_fee_enable) && $is_service_fee_enable=="0") echo "disabled";?>>&nbsp;&nbsp;<b><?php echo $this->lang->line('amount') ?></b>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee') ?> <span class="required">*</span></label>
                                            <div class="col-sm-8 form-markup">
                                                <input type="text" name="service_fee" id="service_fee" value="<?php echo $service_fee ?>" maxlength="10" data-required="1" class="form-control" style="display: inline-block;width: 50%" <?php if (isset($is_service_fee_enable) && $is_service_fee_enable=="0") echo "disabled";?> />&ensp;&ensp;
                                                <label id="SPercentage"><?php echo $this->lang->line('percentage') ?> (%)</label>
                                                <label id="SAmount" style="display:none"><?php echo $this->lang->line('amount') ?> ($)</label>
                                                <div class="service-fee-error"></div>
                                            </div>
                                        </div>
                                        <!--service fee changes end-->
                                    <?php } ?>
                                    <?php /* //allow event booking : start ?>
                                    <div class="form-group"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('allow_event_booking') ?></label>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="allow_event_booking" id="allow_event_booking" value="1"  <?php echo (isset($allow_event_booking) && $allow_event_booking == 1)?'checked':'' ?> />
                                        </div>
                                    </div> <?php */ ?>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('allow_event_booking') ?><span class="required">*</span></label>
                                        <div class="col-sm-4">
                                            <a style="cursor:pointer;" onclick="allow_event_booking();">
                                                <i class="fa fa-toggle-<?php if(isset($allow_event_booking) && $allow_event_booking=="0") { echo "off"; } else{echo "on";} ?> fa-2x" id="on_off_toggle_event" style="    vertical-align: bottom;"></i>
                                            </a>
                                            <input type="hidden" name="allow_event_booking" id="allow_event_booking" value="<?php echo (isset($allow_event_booking) && $allow_event_booking=="0") ? '0' : '1'; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group enable_event_cls" style="<?php if(isset($allow_event_booking) && $allow_event_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('event_booking_capacity'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="capacity" id="capacity" value="<?php echo $capacity ?>" greaterThanEventMinCapacity="#event_minimum_capacity" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group enable_event_cls" style="<?php if(isset($allow_event_booking) && $allow_event_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('event_online_availability') ?><span class="required">*</span></label>  
                                        <div class="col-md-4">
                                            <input type="text" name="event_online_availability" id="event_online_availability" value="<?php echo $event_online_availability ?>" maxlength="6" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group enable_event_cls" style="<?php if(isset($allow_event_booking) && $allow_event_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('event_minimum_capacity') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="event_minimum_capacity" id="event_minimum_capacity" value="<?php echo $event_minimum_capacity ?>" lesserThanEventBookingCapacity="#capacity" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('min_eventcapacity_txt') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php //allow event booking : end 
                                    //enable table booking :: start ?>
                                    <div class="form-group">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('enable_table_booking') ?><span class="required">*</span></label>
                                        <div class="col-sm-4">
                                            <a style="cursor:pointer;" onclick="enable_table_booking();">
                                                <i class="fa fa-toggle-<?php if(isset($enable_table_booking) && $enable_table_booking=="0") { echo "off"; } else{echo "on";} ?> fa-2x" id="on_off_toggle_table" style="    vertical-align: bottom;"></i>
                                            </a>
                                            <input type="hidden" name="enable_table_booking" id="enable_table_booking" value="<?php echo (isset($enable_table_booking) && $enable_table_booking=="0") ? '0' : '1'; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group enable_table_cls" style="<?php if(isset($enable_table_booking) && $enable_table_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('table_booking_capacity') ?><span class="required">*</span></label>  
                                        <div class="col-md-4">
                                            <input type="text" name="table_booking_capacity" id="table_booking_capacity" value="<?php echo $table_booking_capacity ?>" greaterThanTableMinCapacity="#table_minimum_capacity" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group enable_table_cls" style="<?php if(isset($enable_table_booking) && $enable_table_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('table_online_availability') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="table_online_availability" id="table_online_availability" value="<?php echo $table_online_availability ?>" maxlength="6" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group enable_table_cls" style="<?php if(isset($enable_table_booking) && $enable_table_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('table_minimum_capacity') ?><span class="required">*</span></label>  
                                        <div class="col-md-4">
                                            <input type="text" name="table_minimum_capacity" id="table_minimum_capacity" value="<?php echo $table_minimum_capacity ?>" lesserThanTableBookingCapacity="#table_booking_capacity" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('min_tablecapacity_txt') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group enable_table_cls" style="<?php if(isset($enable_table_booking) && $enable_table_booking=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('allowed_days_table') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="allowed_days_table" id="allowed_days_table" value="<?php echo $allowed_days_table ?>" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <?php //enable table booking :: end ?>

                                    <!--print receipt changes :: start-->
                                    <div class="form-group">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('printer_available') ?><span class="required">*</span></label>
                                        <div class="col-sm-4">
                                            <a style="cursor:pointer;" onclick="printReceiptOnOff()">
                                                <i class="fa fa-toggle-<?php if(isset($is_printer_available) && $is_printer_available=="0") { echo "off"; } else{echo "on";} ?> fa-2x" id="on_off_toggle_printer" style="    vertical-align: bottom;"></i>
                                            </a>
                                            <input type="hidden" name="is_printer_available" id="is_printer_available" value="<?php echo (isset($is_printer_available) && $is_printer_available=="0") ? '0' : '1'; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group print_receipt_cls" style="<?php if(isset($is_printer_available) && $is_printer_available=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>">  
                                        <label class="control-label col-md-3" for="s_percentage"><?php echo $this->lang->line('printer_paper_height') ?> <span><?php echo $this->lang->line('printer_paper_paper_note') ?></span><span class="required">*</span></label>  
                                        <div class="col-md-4">
                                            <input type="text" name="printer_paper_height" id="printer_paper_height" value="<?php echo $printer_paper_height ?>" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('printer_recommended_height') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group print_receipt_cls" style="<?php if(isset($is_printer_available) && $is_printer_available=='0') { echo 'display:none;'; } else{echo 'display:block;';} ?>"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('printer_paper_width') ?> <span><?php echo $this->lang->line('printer_paper_paper_note') ?></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="printer_paper_width" id="printer_paper_width" value="<?php echo $printer_paper_width ?>" maxlength="4" data-required="1" class="form-control"/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('printer_recommended_width') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <!--print receipt changes :: end-->
                                    <!--about restaurant changes :: start-->
                                    <div class="form-group"> 
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('about_restaurant') ?><span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <textarea class="ckeditor form-control" name="message" id="message" rows="6" data-required="1" ><?php echo $about_restaurant;?></textarea>           
                                        </div>
                                    </div>
                                    <!--about restaurant changes :: end-->
                                    <div class="form-group company-timing <?php echo ($enable_hours == '0')?'display-yes':'display-yes' ?>">
                                       <label class="control-label col-md-3"><?php echo $this->lang->line('res_time') ?></label>
                                        <?php if(empty($_POST['timings'])){
                                            $business_timings = unserialize(html_entity_decode($timings));
                                        }else if(!empty($edit_timingsarray)){
                                            $business_timings = $edit_timingsarray;
                                        }else{
                                            $timingsArr = $_POST['timings'];
                                            $newTimingArr = array();
                                            foreach($timingsArr as $key=>$value) {
                                                if(isset($value['off'])) {
                                                    $newTimingArr[$key]['open'] = '';
                                                    $newTimingArr[$key]['close'] = '';
                                                    $newTimingArr[$key]['off'] = '0';
                                                } else {
                                                    if(!empty($value['open']) && !empty($value['close'])) {
                                                        $newTimingArr[$key]['open'] = $value['open'];
                                                        $newTimingArr[$key]['close'] = $value['close'];
                                                        $newTimingArr[$key]['off'] = '1';
                                                    } else {
                                                        $newTimingArr[$key]['open'] = '';
                                                        $newTimingArr[$key]['close'] = '';
                                                        $newTimingArr[$key]['off'] = '0';
                                                    }
                                                }
                                            }
                                            $business_timings = $newTimingArr;
                                        }  ?>
                                        <div class="col-md-9">
                                            <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                <tr>
                                                    <td colspan="2">
                                                        <div style="margin-bottom: 20px;">
                                                            <input type="checkbox" id="clickSameHours">
                                                            <label class="checkbox chk-clicksame">
                                                            <?php echo $this->lang->line('time_msg') ?> </label><br/>
                                                            <span id="alertSpan" class="alert-spantg"></span>
                                                        </div>
                                                    </td>
                                                    <td><strong>&nbsp;</strong></td>
                                                </tr>

                                                <tr>
                                                    <td><strong>&nbsp;</strong></td>
                                                    <td colspan="2">
                                                        <label class="pb2">                      
                                                        <?php echo $this->lang->line('time_zone1').' <b>'.$_COOKIE['timezone_name'].'</b> '.$this->lang->line('time_zone2'); ?>
                                                        </label><br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('monday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control"  id="monday_open_hours" name="timings[monday][open]" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['monday']['open']; ?>" placeholder="<?php echo $this->lang->line('opening_hours') ?>" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['monday']['close']; ?>" name="timings[monday][close]" id="monday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'monday'; ?>" class="close_bar_check" id="monday_close" name="timings[monday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('tuesday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control"  placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['tuesday']['open']; ?>" name="timings[tuesday][open]" id="tuesday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control"  placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['tuesday']['close']; ?>" name="timings[tuesday][close]" id="tuesday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'tuesday'; ?>" class="close_bar_check" id="tuesday_close" name="timings[tuesday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('wednesday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control" placeholder="<?php echo $this->lang->line('opening_hours') ?>" value="<?php echo @$business_timings['wednesday']['open']; ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; } ?> name="timings[wednesday][open]" id="wednesday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" value="<?php echo @$business_timings['wednesday']['close']; ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; } ?> name="timings[wednesday][close]" id="wednesday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'wednesday'; ?>" class="close_bar_check" id="wednesday_close" name="timings[wednesday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('thursday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['thursday']['open']; ?>" name="timings[thursday][open]" id="thursday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['thursday']['close']; ?>" name="timings[thursday][close]" id="thursday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'thursday'; ?>" class="close_bar_check" id="thursday_close" name="timings[thursday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('friday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['friday']['open']; ?>" name="timings[friday][open]" id="friday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['friday']['close']; ?>" name="timings[friday][close]" id="friday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'friday'; ?>" class="close_bar_check" id="friday_close" name="timings[friday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('saturday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['saturday']['open']; ?>" name="timings[saturday][open]" id="saturday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['saturday']['close']; ?>" name="timings[saturday][close]" id="saturday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'saturday'; ?>" class="close_bar_check" id="saturday_close" name="timings[saturday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('sunday') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs form-control" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['sunday']['open']; ?>" name="timings[sunday][open]" id="sunday_open_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs form-control" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; } ?> value="<?php echo @$business_timings['sunday']['close']; ?>" name="timings[sunday][close]" id="sunday_close_hours" autocomplete="off">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="hidden" value="<?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'sunday'; ?>" class="close_bar_check" id="sunday_close" name="timings[sunday][off]">
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('closed_on') ?></label>
                                        <div class="col-md-4">
                                            <select name="" placeholder="<?php echo $this->lang->line('select_close_days') ?>" multiple="multiple" class="form-control sumo SlectBox" id="closed_day_select">
                                                <option value="monday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['monday']['off'])) ? '' : 'selected';} ?>><?php echo $this->lang->line('monday') ?></option>
                                                <option value="tuesday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'selected'; } ?>><?php echo $this->lang->line('tuesday') ?></option>
                                                <option value="wednesday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'selected'; } ?>><?php echo $this->lang->line('wednesday') ?></option>
                                                <option value="thursday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['thursday']['off'])) ? '' : 'selected'; } ?>><?php echo $this->lang->line('thursday') ?></option>
                                                <option value="friday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['friday']['off'])) ? '' : 'selected';} ?>><?php echo $this->lang->line('friday') ?></option>
                                                <option value="saturday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['saturday']['off'])) ? '' : 'selected'; } ?>><?php echo $this->lang->line('saturday') ?></option>
                                                <option value="sunday" <?php if(isset($edit_records) || !empty($edit_timingsarray)){ echo (intval(@$business_timings['sunday']['off'])) ? '' : 'selected'; } ?>><?php echo $this->lang->line('sunday') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if($this->session->userdata('AdminUserType') == "MasterAdmin") { ?>
                                        <div class="form-group">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contractual_commission_type') ?> <span class="required">*</span></label>  
                                            <div class="col-sm-4">
                                                <input type="radio" name="contractual_commission_type" id="C_Percentage" <?php if (isset($contractual_commission_type) && $contractual_commission_type=="Percentage") echo "checked";?> value="Percentage" checked="checked">&nbsp;&nbsp;<b><?php echo $this->lang->line('percentage') ?></b>&ensp;
                                                <input type="radio" name="contractual_commission_type" id="C_Amount" <?php if (isset($contractual_commission_type) && $contractual_commission_type=="Amount") echo "checked";?> value="Amount">&nbsp;&nbsp;<b><?php echo $this->lang->line('amount') ?></b>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contractual_commission') ?> <span class="required">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="contractual_commission" id="contractual_commission" value="<?php echo $contractual_commission ?>" maxlength="5" style="display: inline-block;width: 50%"/>&ensp;&ensp;
                                                <label id="CPercentage"><?php echo $this->lang->line('percentage') ?> (%)</label>
                                                <label id="CAmount" style="display:none"><?php echo $this->lang->line('amount') ?> ($)</label>
                                                <div class="commision-error"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contractual_commission_type_delivery') ?> <span class="required">*</span></label>  
                                            <div class="col-sm-4">
                                                <input type="radio" name="contractual_commission_type_delivery" id="C_Percentage_delivery" <?php if (isset($contractual_commission_type_delivery) && $contractual_commission_type_delivery=="Percentage") echo "checked";?> value="Percentage" checked="checked">&nbsp;&nbsp;<b><?php echo $this->lang->line('percentage') ?></b>&ensp;
                                                <input type="radio" name="contractual_commission_type_delivery" id="C_Amount_delivery" <?php if (isset($contractual_commission_type_delivery) && $contractual_commission_type_delivery=="Amount") echo "checked";?> value="Amount">&nbsp;&nbsp;<b><?php echo $this->lang->line('amount') ?></b>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contractual_commission_delivery') ?> <span class="required">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="contractual_commission_delivery" id="contractual_commission_delivery" value="<?php echo $contractual_commission_delivery ?>" maxlength="5" style="display: inline-block;width: 50%"/>&ensp;&ensp;
                                                <label id="CPercentage_delivery"><?php echo $this->lang->line('percentage') ?> (%)</label>
                                                <label id="CAmount_delivery" style="display:none"><?php echo $this->lang->line('amount') ?> ($)</label>
                                                <div class="commision-delivery-error"></div>
                                            </div>
                                        </div>
                                        <!--credit card fee changes start-->
                                        <div class="form-group">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('is_creditcard_fee_enable') ?><span class="required">*</span></label>
                                            <div class="col-sm-4">
                                                <a style="cursor:pointer;" onclick="creditcardFeeOnOff()">
                                                    <i class="fa fa-toggle-<?php if(isset($is_creditcard_fee_enable) && $is_creditcard_fee_enable=="0") { echo "off"; } else{echo "on";} ?> fa-2x" id="credit_on_off_toggle" style="    vertical-align: bottom;"></i>
                                                </a>
                                                <input type="hidden" name="is_creditcard_fee_enable" id="is_creditcard_fee_enable" value="<?php echo (isset($is_creditcard_fee_enable) && $is_creditcard_fee_enable=="0") ? '0' : '1'; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">  
                                            <label class="control-label col-md-3" for="creditcard_percentage"><?php echo $this->lang->line('creditcard_fee_type') ?><span class="required">*</span></label>  
                                            <div class="col-sm-4">
                                                <input type="radio" name="creditcard_fee_type" id="creditcard_percentage" <?php if (isset($creditcard_fee_type) && $creditcard_fee_type=="Percentage") echo "checked";?> value="Percentage" checked="checked" <?php if (isset($is_creditcard_fee_enable) && $is_creditcard_fee_enable=="0") echo "disabled";?>>&nbsp;&nbsp;<b><?php echo $this->lang->line('percentage') ?></b>&ensp;
                                                <input type="radio" name="creditcard_fee_type" id="creditcard_amount" <?php if (isset($creditcard_fee_type) && $creditcard_fee_type=="Amount") echo "checked";?> value="Amount" <?php if (isset($is_creditcard_fee_enable) && $is_creditcard_fee_enable=="0") echo "disabled";?>>&nbsp;&nbsp;<b><?php echo $this->lang->line('amount') ?></b>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('creditcard_fee') ?><span class="required">*</span></label>
                                            <div class="col-sm-8 form-markup">
                                                <input type="text" name="creditcard_fee" id="creditcard_fee" value="<?php echo $creditcard_fee ?>" maxlength="10" data-required="1" class="form-control" style="display: inline-block;width: 50%" <?php if (isset($is_creditcard_fee_enable) && $is_creditcard_fee_enable=="0") echo "disabled";?> />&ensp;&ensp;
                                                <label id="CreditCard_Percentage"><?php echo $this->lang->line('percentage') ?> (%)</label>
                                                <label id="CreditCard_Amount" style="display:none"><?php echo $this->lang->line('amount') ?> ($)</label>
                                                <div class="creditcard-fee-error"></div>
                                            </div>
                                        </div>
                                        <!--credit card fee changes end-->
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('order_mode'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <?php $order_mode = explode(',', @$order_mode); ?>
                                            <input type="checkbox" name="order_mode[]" id="order_mode" value="PickUp" <?php echo @in_array('PickUp',$order_mode)?'checked':''; ?>>
                                            <span class="order-mode-checkbox"><?php echo $this->lang->line('pickup_word') ?></span>
                                            <input type="checkbox" name="order_mode[]" id="order_mode" value="Delivery" <?php echo @in_array('Delivery',$order_mode)?'checked':''; ?>>
                                            <span class="order-mode-checkbox"><?php echo $this->lang->line('delivery_word') ?></span>
                                            <div id="checkbox_error">
                                            </div>
                                        </div>
                                    </div>                                    
                                    <?php //scheduled orders :: start ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('allow_scheduled_delivery') ?><span class="required">*</span></label>
                                        <div class="col-sm-4">
                                            <a style="cursor:pointer;" onclick="allow_scheduled_delivery();">
                                                <i class="fa fa-toggle-<?php if(isset($allow_scheduled_delivery) && $allow_scheduled_delivery == "0") { echo "off"; } else{echo "on";} ?> fa-2x" id="on_off_scheduled_delivery" style="vertical-align: bottom;"></i>
                                            </a>
                                            <input type="hidden" name="allow_scheduled_delivery" id="allow_scheduled_delivery" value="<?php echo (isset($allow_scheduled_delivery) && $allow_scheduled_delivery == "0") ? '0' : '1'; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group scheduled_delivery_content <?php echo (isset($allow_scheduled_delivery) && $allow_scheduled_delivery == '0' && isset($allowed_days_for_scheduling)) ? 'display-no' : '' ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('allowed_days_scheduling') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select class="form-control sumo" name="allowed_days_for_scheduling" id="allowed_days_for_scheduling">
                                                <option value=""><?php echo $this->lang->line('please'); ?> <?php echo $this->lang->line('select'); ?></option>
                                                <option value="1" <?php echo ($allowed_days_for_scheduling == '1') ? "selected" : "" ?> ><?php echo $this->lang->line('one_day'); ?></option>
                                                <option value="2" <?php echo ($allowed_days_for_scheduling == '2') ? "selected":"" ?> ><?php echo $this->lang->line('two_days'); ?></option>
                                                <option value="3" <?php echo ($allowed_days_for_scheduling == '3') ? "selected":"" ?> ><?php echo $this->lang->line('three_days'); ?></option>
                                                <option value="4" <?php echo ($allowed_days_for_scheduling == '4') ? "selected":"" ?> ><?php echo $this->lang->line('four_days'); ?></option>
                                                <option value="5" <?php echo ($allowed_days_for_scheduling == '5') ? "selected":"" ?> ><?php echo $this->lang->line('five_days'); ?></option>
                                                <option value="6" <?php echo ($allowed_days_for_scheduling == '6') ? "selected" : "" ?> ><?php echo $this->lang->line('six_days'); ?></option>
                                                <option value="7" <?php echo ($allowed_days_for_scheduling == '7') ? "selected":"" ?> ><?php echo $this->lang->line('seven_days'); ?></option>
                                                <option value="8" <?php echo ($allowed_days_for_scheduling == '8') ? "selected":"" ?> ><?php echo $this->lang->line('eight_days'); ?></option>
                                                <option value="9" <?php echo ($allowed_days_for_scheduling == '9') ? "selected":"" ?> ><?php echo $this->lang->line('nine_days'); ?></option>
                                                <option value="10" <?php echo ($allowed_days_for_scheduling == '10') ? "selected":"" ?> ><?php echo $this->lang->line('ten_days'); ?></option>
                                            </select>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right"><?php echo $this->lang->line('allowed_days_scheduling_desc') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php //scheduled orders :: end ?>
                                    <?php //restaurant ratings :: start ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant_rating'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="restaurant_rating" id="restaurant_rating" value="<?php echo ($restaurant_rating) ? $restaurant_rating : 0; ?>" data-required="1" minlength="1" maxlength="4" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant_rating_count'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="restaurant_rating_count" id="restaurant_rating_count" value="<?php echo ($restaurant_rating_count) ? $restaurant_rating_count : 0; ?>" minlength="1" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <?php //restaurant ratings :: end ?>
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?>  id="submit_page" value="Submit" class="btn btn-success default-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <?php if($this->uri->segment('3')=="add") { ?>
                                            <button type="submit" name="submit_page" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?>  id="submit_page" value="Save" class="btn btn-success default-btn theme-btn"><?php echo $this->lang->line('save') ?></button>
                                        <?php } ?>
                                        <a class="btn btn-danger default-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel') ?></a>
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
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo $this->lang->line('lat_long_msg') ?></h4>
            </div>
            <div class="modal-body">                                               
                <form class="form-inline margin-bottom-10" action="#">
                    <div class="input-group">
                        <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="<?php echo $this->lang->line('address') ?>">
                        <span class="input-group-btn">
                            <button class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div id="gmap_geocoding" class="gmaps">
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal"><?php echo $this->lang->line('close') ?></button>            
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="mansi_map"></div>
<?php //Code add for food type :: Start :: 30-01-2021 ?>
<div id="food_type_popup" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('food_type') ?></h4>
      </div>
      <div class="modal-body">
        <form id="food_type_form" name="food_type_form" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('food_type_name') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <input type="hidden" name="language_slug" id="language_slug" value="" />
                            <input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $entity_id ?>" />
                            <input type="text" name="name" id="name" value="" maxlength="249" data-required="1" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('food_type'); ?><span class="required">*</span></label>
                        <div class="col-md-8">
                            <input type="radio" name="is_veg" id="is_veg" value="1">&nbsp;&nbsp;<?php echo $this->lang->line('veg'); ?>&ensp;&ensp;
                            <input type="radio" name="is_veg" id="is_veg" value="0">&nbsp;&nbsp;<?php echo $this->lang->line('non_veg'); ?>
                            <div id="food_type_popup_error"></div>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php //Code add for food type :: End :: 30-01-2021 ?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/address-autofill.js"></script>
<script>
//Code add for food type :: Start :: 30-01-2021
$('#food_type_form').submit(function(){
    $("#food_type_form").validate();
    if (!$("#food_type_form").valid()) return false;
    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/restaurant/Addfoodtype",
      data: $('#food_type_form').serialize(),
      cache: false, 
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(html) {
        $('#quotes-main-loader').hide();
        $('#food_type_popup').modal('hide');
        $('.food_type').empty().append(html);
        $('.food_type')[0].sumo.reload();
        //grid.getDataTable().fnDraw();
      }
    });
    return false;
});
$('#food_type_popup').on('hidden.bs.modal', function () {
    $("#food_type_form").validate().resetForm();
    $('#food_type_popup #name').val('');
});

$('#restaurant_owner_id').change(function()
{
    var restaurant_owner_id = $('#restaurant_owner_id').val();
    console.log(restaurant_owner_id);
    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/restaurant/fetchbranchAdmin",
      data: 'restaurant_owner_id=' + restaurant_owner_id,
      cache: false, 
      beforeSend: function(){        
      },   
      success: function(html) {        
        $('#branch_admin_id').empty().append(html);
        $('#branch_admin_id')[0].sumo.reload();        
      }
    });
    return false;
});


function food_typeopenfn(language_slug)
{
    $("#language_slug").val(language_slug); 
    $('#food_type_popup').modal('show');
}
//Code add for food type :: End :: 30-01-2021
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
     $('.sumo').SumoSelect({search: true, selectAll: true, captionFormatAllSelected: '{0} <?php echo $this->lang->line('selected');?>!',locale: ['OK', 'Cancel', "<?php echo $this->lang->line('all').' '.$this->lang->line('select_');?>"], searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
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
$("#basic").on("shown.bs.modal", function () {
    if (navigator.geolocation){    
        // init geocoding Maps - calling success and fail function - mapGeocoding
        navigator.geolocation.getCurrentPosition(mapGeocoding,mapGeocoding);
    }
});
$("#closed_day_select").on('change',function() {
    var selected_days = $(this).val();
    $('#closed_day_select > option').each(function(index, value){
        var day = $(this).val();
        if(jQuery.inArray( day, selected_days )!= -1){
            $("#" + day + "_open_hours").val('');
            $("#" + day + "_close_hours").val('');
            $("#" + day + "_open_hours").attr('disabled', 'disabled');
            $("#" + day + "_close_hours").attr('disabled', 'disabled');
            $("#" + day + "_close").val(day);
        }else{
            $("#" + day + "_open_hours").removeAttr('disabled');
            $("#" + day + "_close_hours").removeAttr('disabled');
            $("#" + day + "_close").val('');
        }
    });
    if($("#clickSameHours").prop('checked') == true){
        $('#clickSameHours').prop('checked', false);    
    }
});

//New code add to find map base on default cuntry lat/long :: Start
var address = '<?php echo  country;?>';
if (address !== "undefined" && address !== null ) { 
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
        default_latitude = results[0].geometry.location.lat();
        default_longitude = results[0].geometry.location.lng();  
        $("#default_latitude").val(default_latitude);
        $("#default_longitude").val(default_longitude);
        } 
    }); 
}
//New code add to find map base on default cuntry lat/long :: End
var mapGeocoding = function (position) {
    var default_latitude = 0;
    var default_longitude = 0; 
    // when no permission of location   
    if ( typeof(position.coords) !== "undefined" && position.coords !== null ) {
       var default_latitude = position.coords.latitude;   
       var default_longitude = position.coords.longitude;
    }
    else
    {
       var default_latitude = $("#default_latitude").val();   
       var default_longitude =  $("#default_longitude").val();
    }

    var map = new GMaps({  
        div: '#gmap_geocoding',
        lat: default_latitude,
        lng: default_longitude,
        click: function (e) {           
           placeMarker(e.latLng);
        }       
    }); 
    map.addMarker({
        lat: default_latitude,
        lng: default_longitude,
        title: '',
        draggable: true,
        dragend: function(event) {
            $("#latitude").val(event.latLng.lat());
            $("#longitude").val(event.latLng.lng());
        }
    });   
    function placeMarker(location) {

        map.removeMarkers();
        $("#latitude").val(location.lat());
        $("#longitude").val(location.lng());
        map.addMarker({
            lat: location.lat(),
            lng: location.lng(),
            draggable: true,
            dragend: function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            }    
        })
    }
    var handleAction = function () {
        var text = $.trim($('#gmap_geocoding_address').val());
        GMaps.geocode({
            address: text,
            callback: function (results, status) {
                if (status == 'OK') { 
                    map.removeMarkers();                   
                    var latlng = results[0].geometry.location;                    
                    map.setCenter(latlng.lat(), latlng.lng());
                    map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),         
                        draggable: true,
                        dragend: function(event) {
                            $("#latitude").val(event.latLng.lat());
                            $("#longitude").val(event.latLng.lng());
                        }
                    });
                    $("#latitude").val(latlng.lat());
                    $("#longitude").val(latlng.lng());
                }
            }
        });
    }
    $('#gmap_geocoding_btn').click(function (e) {
        e.preventDefault();
        handleAction();
    });
    $("#gmap_geocoding_address").keypress(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            e.preventDefault();
            handleAction();
        }
    });
}
<?php if($this->session->userdata('AdminUserType') == "MasterAdmin") { ?>
    // Markup Radio Button Validation
    function markup () {
      if($("input[name=amount_type]:checked").val() == "Percentage" ){
              $("#Amount").hide();
              $("#Percentage").show();     
      }else if($("input[name=amount_type]:checked").val() == "Amount" ){
              $("#Percentage").hide();
              $("#Amount").show();
      }
    }
    function markupServiceFee () {
      if($("input[name=service_fee_type]:checked").val() == "Percentage" ){
              $("#SAmount").hide();
              $("#SPercentage").show();     
      }else if($("input[name=service_fee_type]:checked").val() == "Amount" ){
              $("#SPercentage").hide();
              $("#SAmount").show();
      }
    }
    function markupContractualCommission () {
      if($("input[name=contractual_commission_type]:checked").val() == "Percentage" ){
              $("#CAmount").hide();
              $("#CPercentage").show();     
      }else if($("input[name=contractual_commission_type]:checked").val() == "Amount" ){
              $("#CPercentage").hide();
              $("#CAmount").show();
      }
    }
    function markupContractualCommissionDelivery () {
      if($("input[name=contractual_commission_type_delivery]:checked").val() == "Percentage" ){
              $("#CAmount_delivery").hide();
              $("#CPercentage_delivery").show();     
      }else if($("input[name=contractual_commission_type_delivery]:checked").val() == "Amount" ){
              $("#CPercentage_delivery").hide();
              $("#CAmount_delivery").show();
      }
    }
    function markupCreditCardFee () {
      if($("input[name=creditcard_fee_type]:checked").val() == "Percentage" ){
              $("#CreditCard_Amount").hide();
              $("#CreditCard_Percentage").show();     
      }else if($("input[name=creditcard_fee_type]:checked").val() == "Amount" ){
              $("#CreditCard_Percentage").hide();
              $("#CreditCard_Amount").show();
      }
    }
    $(document).ready(function(){
        <?php if($this->session->userdata('AdminUserType') != 'MasterAdmin'){ ?>
            document.getElementById("C_Percentage").disabled = true;
            document.getElementById("C_Amount").disabled = true;
            document.getElementById("contractual_commission").disabled = true;

            document.getElementById("C_Percentage_delivery").disabled = true;
            document.getElementById("C_Amount_delivery").disabled = true;
            document.getElementById("contractual_commission_delivery").disabled = true;

            document.getElementById("s_percentage").disabled = true;
            document.getElementById("s_amount").disabled = true;
            document.getElementById("service_fee").disabled = true;
        <?php }else{ ?>
            document.getElementById("C_Percentage").disabled = false;
            document.getElementById("C_Amount").disabled = false;
            document.getElementById("contractual_commission").disabled = false;

            document.getElementById("C_Percentage_delivery").disabled = false;
            document.getElementById("C_Amount_delivery").disabled = false;
            document.getElementById("contractual_commission_delivery").disabled = false;

            if($("#on_off_toggle").hasClass("fa-toggle-on")){
                document.getElementById("s_percentage").disabled = false;
                document.getElementById("s_amount").disabled = false;
                document.getElementById("service_fee").disabled = false;
            }
        <?php } ?>
        markup();
        markupServiceFee();
        markupContractualCommission();
        markupContractualCommissionDelivery();
        markupCreditCardFee();
    });

    $("input[name=amount_type]:radio").click(function(){
      markup();
      if($("input[name=amount_type]:checked").val() == "Percentage" ){    
        $("#amount").val('');          
      }else if($("input[name=amount_type]:checked").val() == "Amount" ){
        $("#amount").val('');           
      }
    });
    $("input[name=service_fee_type]:radio").click(function(){
      markupServiceFee();
      if($("input[name=service_fee_type]:checked").val() == "Percentage" ){    
        $("#service_fee").val('');          
      }else if($("input[name=service_fee_type]:checked").val() == "Amount" ){
        $("#service_fee").val('');           
      }
    });
    $("input[name=contractual_commission_type]:radio").click(function(){
      markupContractualCommission();
      if($("input[name=contractual_commission_type]:checked").val() == "Percentage" ){    
        $("#contractual_commission").val('');          
      }else if($("input[name=contractual_commission_type]:checked").val() == "Amount" ){
        $("#contractual_commission").val('');           
      }
    });
    $("input[name=contractual_commission_type_delivery]:radio").click(function(){
      markupContractualCommissionDelivery();
      if($("input[name=contractual_commission_type_delivery]:checked").val() == "Percentage" ){    
        $("#contractual_commission_delivery").val('');          
      }else if($("input[name=contractual_commission_type_delivery]:checked").val() == "Amount" ){
        $("#contractual_commission_delivery").val('');           
      }
    });
    $("input[name=creditcard_fee_type]:radio").click(function(){
      markupCreditCardFee();
      if($("input[name=creditcard_fee_type]:checked").val() == "Percentage" ){    
        $("#creditcard_fee").val('');          
      }else if($("input[name=creditcard_fee_type]:checked").val() == "Amount" ){
        $("#creditcard_fee").val('');           
      }
    });
<?php } ?>
//for company timing
$(function () {
    $('#monday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#monday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#tuesday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#tuesday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#wednesday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#wednesday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#thursday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#thursday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#friday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#friday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#saturday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#saturday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#sunday_open_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $('#sunday_close_hours').timepicker({timeFormat: "HH:mm", controlType: 'select', ampm: true, stepMinute: 5,showButtonPanel:  false});
    $("#clickSameHours").change(function () {
        $('#alertSpan').html('');
        if (this.checked) {
            var ophrs = $('#monday_open_hours').val();
            var clhrs = $('#monday_close_hours').val();
            if (ophrs != '' && clhrs != '') {
                $('#alertSpan').html('');
                var num = $('#closed_day_select > option').length;
                for(var i=0; i<num; i++){
                    $('.SlectBox')[0].sumo.unSelectItem(i);
                }
                $('#closed_day_select > option').each(function(index, value){
                    var day = $(this).val();
                    $("#" + day + "_open_hours").removeAttr('disabled');
                    $("#" + day + "_close_hours").removeAttr('disabled');
                    $("#" + day + "_open_hours").val(ophrs);
                    $("#" + day + "_close_hours").val(clhrs);
                });
                $('#clickSameHours').prop('checked', true);
            } else {
                $('#alertSpan').html("<?php echo $this->lang->line('open_close_msg') ?>");
                $(this).removeAttr("checked");
            }
        } else {
            $('#alertSpan').html('');
        }
        return false;
    });
});
/*$('.company-hours').click(function(){
    if($(this).val() == '0'){
        $('.company-timing').hide();
        $('.hasDatepicker').each(function(){
            var id = $(this).attr('id');
            $('#'+id).val('');
        });
        $('#clickSameHours').prop('checked',false).attr('checked',false);
    }else{
        $('.company-timing').show();
    }
});*/
function readURL(input) {
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    var file_size = fileInput.size;
    if(input.files[0].size <= 512000){ // 500 KB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            $(':input[type="submit"]').prop("disabled",false);
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
            $(':input[type="submit"]').prop("disabled",true);
            $('#Slider_image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $(':input[type="submit"]').prop("disabled",true);
        $('#Slider_image').val('');
        $("#old").show();
    }
}
function readURLforBackgroundImage(input) {
    var fileInput = document.getElementById('background_image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    var file_size = fileInput.size;
    if(input.files[0].size <= 512000){ // 500 KB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            $(':input[type="submit"]').prop("disabled",false);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview_background_img').attr('src', e.target.result).attr('style','display: inline-block;');
                    $("#old_background_img").hide();
                    $('#background_img_errormsg').html('').hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview_background_img').attr('src', '').attr('style','display: none;');
            $('#background_img_errormsg').html("<?php echo $this->lang->line('img_allow') ?>").show();
            $(':input[type="submit"]').prop("disabled",true);
            $('#Slider_image').val('');
            $("#old_background_img").show();
        }
    }else{
        $('#preview_background_img').attr('src', '').attr('style','display: none;');
        $('#background_img_errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $(':input[type="submit"]').prop("disabled",true);
        $('#Slider_image').val('');
        $("#old_background_img").show();
    }
}
//check phone number exist
function checkExist(phone_number,is_masterdata){
    var entity_id = $('#entity_id').val();
    var content_id = $('#content_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/restaurant/checkExist",
    data: 'phone_number=' + phone_number +'&entity_id='+entity_id +'&content_id='+content_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#phoneExist').show();
        $('#phoneExist').html("<?php echo $this->lang->line('phones_exist'); ?>");        
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
function checkEmail(email,entity_id,is_masterdata){
  var content_id = $('#content_id').val();
  $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/restaurant/checkEmailExist",
    data: 'email=' + email +'&entity_id='+entity_id+'&content_id='+content_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html("<?php echo $this->lang->line('email_exist'); ?>");        
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
$('.add_res_branch').click(function(){
    if($(this).val() == 'res'){
        $('.res-list').hide();
        $('.branch-name').hide();
        $('.res-name').show();
    }else{
        $('.res-list').show();
        $('.branch-name').show();
        $('.res-name').hide();
    }
});
function serviceFeeOnOff() {
    $("#on_off_toggle").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle").hasClass("fa-toggle-off")){
        document.getElementById("is_service_fee_enable").value = "0";
        document.getElementById("s_percentage").disabled = true;
        document.getElementById("s_amount").disabled = true;
        document.getElementById("service_fee").disabled = true;
    }
    if($("#on_off_toggle").hasClass("fa-toggle-on")){
        document.getElementById("is_service_fee_enable").value = "1";
        document.getElementById("s_percentage").disabled = false;
        document.getElementById("s_amount").disabled = false;
        document.getElementById("service_fee").disabled = false;
    }
}
function creditcardFeeOnOff(){
    $("#credit_on_off_toggle").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#credit_on_off_toggle").hasClass("fa-toggle-off")){
        document.getElementById("is_creditcard_fee_enable").value = "0";
        document.getElementById("creditcard_percentage").disabled = true;
        document.getElementById("creditcard_amount").disabled = true;
        document.getElementById("creditcard_fee").disabled = true;
    }
    if($("#credit_on_off_toggle").hasClass("fa-toggle-on")){
        document.getElementById("is_creditcard_fee_enable").value = "1";
        document.getElementById("creditcard_percentage").disabled = false;
        document.getElementById("creditcard_amount").disabled = false;
        document.getElementById("creditcard_fee").disabled = false;
    }
}
</script>
<!-- intel plugin :: start -->
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
const phoneInputField = document.querySelector("#phone_number");
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

$(document).on('input','#phone_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number').val(phoneNumber);
    }
});
$(document).on('focusout','#phone_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number').val(phoneNumber);
    }
});
phoneInputField.addEventListener("close:countrydropdown",function() {
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number').val(phoneNumber);
    }
});
</script>
<!-- intel plugin :: end -->
<!-- print receipt changes :: start -->
<script type="text/javascript">
function printReceiptOnOff() {
    $("#on_off_toggle_printer").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_printer").hasClass("fa-toggle-off")){
        document.getElementById("is_printer_available").value = "0";
        $('.print_receipt_cls').css('display','none');
    }
    if($("#on_off_toggle_printer").hasClass("fa-toggle-on")){
        document.getElementById("is_printer_available").value = "1";
        $('.print_receipt_cls').css('display','block');
    }
}
function allow_event_booking(){
    $("#on_off_toggle_event").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_event").hasClass("fa-toggle-off")){
        document.getElementById("allow_event_booking").value = "0";
        $('.enable_event_cls').css('display','none');
        $('#event_online_availability').val('');
        $('#event_minimum_capacity').val('');
        $('#capacity').val('');
        var eventmincapacity_label = $("#event_minimum_capacity").next('label').attr("for", "event_minimum_capacity");
        eventmincapacity_label.css('display','none');
        var eventcapacity_label = $("#capacity").next('label').attr("for", "capacity");
        eventcapacity_label.css('display','none');
    }
    if($("#on_off_toggle_event").hasClass("fa-toggle-on")){
        document.getElementById("allow_event_booking").value = "1";
        $('.enable_event_cls').css('display','block');
    }
}
function enable_table_booking(){
    $("#on_off_toggle_table").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle_table").hasClass("fa-toggle-off")){
        document.getElementById("enable_table_booking").value = "0";
        $('.enable_table_cls').css('display','none');
        $('#table_booking_capacity').val('');
        $('#table_online_availability').val('');
        $('#table_minimum_capacity').val('');
        $('#allowed_days_table').val('');
        var tablemincapacity_label = $("#table_minimum_capacity").next('label').attr("for", "table_minimum_capacity");
        tablemincapacity_label.css('display','none');
        var tablecapacity_label = $("#table_booking_capacity").next('label').attr("for", "table_booking_capacity");
        tablecapacity_label.css('display','none');
    }
    if($("#on_off_toggle_table").hasClass("fa-toggle-on")){
        document.getElementById("enable_table_booking").value = "1";
        $('.enable_table_cls').css('display','block');
    }
}
function allow_scheduled_delivery() {
    $("#on_off_scheduled_delivery").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_scheduled_delivery").hasClass("fa-toggle-off")){
        document.getElementById("allow_scheduled_delivery").value = "0";
        $('.scheduled_delivery_content').addClass('display-no');
    }
    if($("#on_off_scheduled_delivery").hasClass("fa-toggle-on")){
        document.getElementById("allow_scheduled_delivery").value = "1";
        $('.scheduled_delivery_content').removeClass('display-no');
    }
}
// custom code for lesser than
jQuery.validator.addMethod('lesserThanTableBookingCapacity', function(value, element, param) {
    if($("#enable_table_booking").val() == "1"){
        if(value && jQuery(param).val() && $('#table_online_availability').val()) {
            var resultant = (parseInt(jQuery(param).val()) * $('#table_online_availability').val())/100;
            if(parseInt(value) < resultant){
                if(parseInt(jQuery(param).val()) > parseInt(value)) {
                    var tablecapacity_label = $("#table_booking_capacity").next('label').attr("for", "table_booking_capacity");
                    tablecapacity_label.css('display','none');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }
}, 'Must be less than Table Booking Capacity' );

// custom code for lesser than
$.validator.addMethod("greaterThanEventMinCapacity", function(value, element, param) {
    if($("#allow_event_booking").val() == "1"){
        if(value && jQuery(param).val()) {
            if(parseInt(value) > parseInt(jQuery(param).val())){
                var resultant_val = (parseInt(value) * $('#event_online_availability').val())/100;
                if(parseInt(jQuery(param).val()) < resultant_val){
                    var eventmincapacity_label = $("#event_minimum_capacity").next('label').attr("for", "event_minimum_capacity");
                    eventmincapacity_label.css('display','none');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }  
}, "Must be greater than Event Booking Minimum Capacity");

// custom code for greater than
$.validator.addMethod("greaterThanEventMinCapacity", function(value, element, param) {
    if($("#allow_event_booking").val() == "1"){
        if(value && jQuery(param).val()) {
            if(parseInt(value) > parseInt(jQuery(param).val())){
                var resultant_val = (parseInt(value) * $('#event_online_availability').val())/100;
                if(parseInt(jQuery(param).val()) < resultant_val){
                    var mincapacity_label = $("#event_minimum_capacity").next('label').attr("for", "event_minimum_capacity");
                    mincapacity_label.css('display','none');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }  
}, "Must be greater than Reservation Minimum Capacity");
// custom code for greater than
$.validator.addMethod("greaterThanTableMinCapacity", function(value, element, param) {
    if($("#enable_table_booking").val() == "1"){
        if(value && jQuery(param).val()) {
            if(parseInt(value) > parseInt(jQuery(param).val())){
                var resultant_val = (parseInt(value) * $('#table_online_availability').val())/100;
                if(parseInt(jQuery(param).val()) < resultant_val){
                    var tablemincapacity_label = $("#table_minimum_capacity").next('label').attr("for", "table_minimum_capacity");
                    tablemincapacity_label.css('display','none');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }
}, "Must be greater than Table Booking Minimum Capacity");

// custom code for lesser than
jQuery.validator.addMethod('lesserThanEventBookingCapacity', function(value, element, param) {  
    if($("#allow_event_booking").val() == "1"){
        if(value && jQuery(param).val() && $('#event_online_availability').val()) {
            var resultant = (parseInt(jQuery(param).val()) * $('#event_online_availability').val())/100;
            if(parseInt(value) < resultant){
                if(parseInt(jQuery(param).val()) > parseInt(value)) {
                    var eventcapacity_label = $("#capacity").next('label').attr("for", "capacity");
                    eventcapacity_label.css('display','none');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }
}, 'Must be less than Event Booking Capacity' );
</script>
<!-- print receipt changes :: end -->
<script type="text/javascript">
function checkResNameExist(value='') {
    var entity_id = $('#entity_id').val();
    var add_res_branch = ($("input[name='add_res_branch']:checked").val()) ? $("input[name='add_res_branch']:checked").val() : $('#add_res_branch').val();

    if(value == ''){
        if(add_res_branch=='res') {
            value = $('#res_name').val();
        } else if(add_res_branch=='branch') {
            value = $('#branch_name').val();
        } else {
            if($('#res_name').val() != '') {
                value = $('#res_name').val();
            } else if($('#branch_name').val() != '') {
                value = $('#branch_name').val();
            }
        }
    }
    var label_name = '';
    if(add_res_branch=='res' && $('#res_name').val() != '') {
        label_name = 'res_name';
    } else if(add_res_branch=='branch' && $('#branch_name').val() != '') {
        label_name = 'branch_name';
    } else {
        if($('#res_name').val() != '') {
            label_name = 'res_name';
        } else if($('#branch_name').val() != '') {
            label_name = 'branch_name';
        }
    }
    //var name_val = (add_res_branch=='res' && $('#res_name').val() != '')?$('#res_name').val():((add_res_branch=='branch' && $('#branch_name').val() != '')?$('#branch_name').val():'');
    if(value != '' && add_res_branch != ''){
        $.ajax({
            type: "POST",
            url: BASEURL+"<?php echo ADMIN_URL ?>/restaurant/checkResNameExist/<?php echo $this->uri->segment(4) ?>",
            data: label_name+'=' + value +'&call_from=ajax_call&entity_id='+entity_id+'&add_res_branch='+add_res_branch,
            cache: false,
            success: function(html) {
                if(add_res_branch == 'res'){
                    $('#branch_name_exist').html("");
                    $('#branch_name_exist').hide();
                    if(html > 0){
                        $('#res_name_exist').show();
                        $('#res_name_exist').html("<?php echo $this->lang->line('res_exist'); ?>");        
                        $(':input[type="submit"]').prop("disabled",true);
                    } else {
                        $('#res_name_exist').html("");
                        $('#res_name_exist').hide();
                        $(':input[type="submit"]').prop("disabled",false);
                    }
                } else {
                    $('#res_name_exist').html("");
                    $('#res_name_exist').hide();
                    if(html > 0){
                        $('#branch_name_exist').show();
                        $('#branch_name_exist').html("<?php echo $this->lang->line('branch_exist'); ?>");        
                        $(':input[type="submit"]').prop("disabled",true);
                    } else {
                        $('#branch_name_exist').html("");
                        $('#branch_name_exist').hide();        
                        $(':input[type="submit"]').prop("disabled",false);
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if(add_res_branch == 'res'){
                    $('#res_name_exist').show();
                    $('#res_name_exist').html(errorThrown);
                } else {
                    $('#branch_name_exist').show();
                    $('#branch_name_exist').html(errorThrown);
                }
            }
        });
    } else {
        $('#branch_name_exist').html("");
        $('#branch_name_exist').hide();
        $('#res_name_exist').html("");
        $('#res_name_exist').hide();
    }
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>