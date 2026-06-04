<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- Embed the intl-tel-input plugin : start -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
<!-- Embed the intl-tel-input plugin : end -->
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','user_id','restaurant_id','address_id','coupon_id','tax_rate','order_status','order_date','total_rate','coupon_amount','coupon_type','tax_type','subtotal','service_fee','service_fee_type','is_service_fee_enable');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label     = $this->lang->line('title_admin_orderedit');        
    $form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $address = $this->order_model->getAddress($user_id); 
}
else
{
    $add_label    = $this->lang->line('title_admin_orderadd');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $menu_item = array('1'=>'');
}
$restaurant_id = isset($_POST['restaurant_id'])?$_POST['restaurant_id']:$restaurant_id;
$menu_detail     = $this->order_model->getItem($restaurant_id);
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <?php                                         
            if(isset($_SESSION['page_MSG']))
            { ?>
                <div class="alert alert-success">
                     <?php echo $_SESSION['page_MSG'];
                     unset($_SESSION['page_MSG']);
                     ?>
                </div>
            <?php } ?>
            <!-- END PAGE HEADER-->
            <!-- New User Add :: start -->
            <?php if(in_array('users~add',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="row add_new_user_content" style="display: none;">
                    <div class="col-md-12">
                        <!-- BEGIN VALIDATION STATES-->
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo $this->lang->line('add').' '.$this->lang->line('customer');?></div>
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <form id="form_add_user_fororder" name="form_add_user_fororder" method="post" class="form-horizontal" enctype="multipart/form-data" >
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
                                                <input type="text" name="first_name" id="first_name" value="" maxlength="20" data-required="1" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('last_name')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="last_name" id="last_name" value="" maxlength="20" data-required="1" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contact_number')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
                                                <input type="tel" onblur="checkExistPhnNo(this.value)" name="mobile_number" id="mobile_number" value="" data-required="1" class="form-control" placeholder=" " maxlength='12' />
                                                <div class="phn_err"  style="display: none; color: red;"></div>
                                            </div>
                                            <div id="phoneExist"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contact_email')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="email" name="email_add" id="email" onblur="checkEmailExist(this.value)" value="" maxlength="50" data-required="1" class="form-control"/>
                                            </div>
                                            <div id="EmailExistAdd"></div>
                                        </div>
                                        <input type="hidden" name="user_type" id="user_type" value="User">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('address')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="hidden" name="latitude" id="latitude" value="">
                                                <input type="hidden" name="longitude" id="longitude" value="">
                                                <input type="text" class="form-control" name="address_field" id="address_field" placeholder=" " />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('postal_code')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder=" " minlength="5" maxlength="6" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('city')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="city" id="city" value="" maxlength="50"/>
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="submit_adduser" id="submit_adduser" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                            <button type="button" name="cancel_adduser" id="cancel_adduser" value="cancel_adduser" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('cancel') ?></button>
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        <!-- END VALIDATION STATES-->
                    </div>
                </div>
            <?php } ?>
            <!-- New User Add :: end -->
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" style= "display: none;" class="frame-load">
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
                                    <?php //if($this->session->userdata('AdminUserType') == 'MasterAdmin'){ ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('customers') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>">
                                            <select name="user_id" class="form-control sumo select_user_id" id="user_id" onchange="getAddress(this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($user)){
                                                    foreach ($user as $key => $value) { 
                                                        $mobile_nmbr = ($value->mobile_number_chk)?' ('.$value->mobile_number.')':''; ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $user_id)?"selected":"" ?>><?php echo $value->first_name.' ' .$value->last_name.$mobile_nmbr; ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <?php if(in_array('users~add',$this->session->userdata("UserAccessArray"))) { ?>
                                            <button type="button" name="add_new_user" id="add_new_user" value="add_new_user" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('add_new_user') ?></button>
                                        <?php } ?>
                                    </div> 
                                    <?php //}else{ ?>
                                        <!-- <input type="hidden" name="user_id" value="0" id="user_id"> -->
                                    <?php //} ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="restaurant_id" class="form-control sumo" id="restaurant_id" onchange="getItemDetail(this.id,this.value)">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>" <?php echo ($value->is_service_fee_enable) ? 'data-service-fee-type="'.$value->service_fee_type.'"' .' '.'data-service-fee="'.$value->service_fee.'"' : '';?> data-order-mode="<?php echo $value->order_mode ?>" ><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> 
                                    <?php if(isset($_POST['item_id'])){ ?>
                                        <div class="form-group">
                                            <?php for($i=1,$inc=1;$i<=count($_POST['item_id']);$inc++,$i++){ ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>,this.value)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option> 
                                                            <?php if($_POST['restaurant_id']){
                                                                if(!empty($menu_detail)){
                                                                foreach ($menu_detail as $key => $value) { ?>
                                                                    <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $_POST['item_id'][$i])?"selected":"" ?>><?php echo $value->name ?></option>    
                                                            <?php } } }?> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($_POST['qty_no'][$i])?$_POST['qty_no'][$i]:'' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>,1)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                    </div>
                                                    <!-- base price changes start -->
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('base_price') ?>" name="base_price[<?php echo $inc ?>]" id="base_price<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->rate)?$menu_item[$i]->rate:'' ?>" maxlength="20" data-required="1" class="form-control base_price validate-class" readonly="" />
                                                    </div>
                                                    <!-- base price changes end -->
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($_POST['rate'][$i])?$_POST['rate'][$i]:'' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if($inc > 1){ ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                    <!-- div to display addons (start)-->
                                                    <div class="col-md-12 addOns_wrapcls" id="addOns_wrap<?php echo $inc; ?>" style="margin-top: 12.5px; margin-bottom: 12.5px; padding: 0px;display: none;">
                                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label>
                                                        <div class="col-md-9" id="addOns<?php echo $inc; ?>"></div>
                                                    </div>
                                                    <!-- div to display addons (end) -->
                                                    <div class="col-md-3" style="clear: both;">
                                                    </div>
                                                    <div class="col-md-6" style="margin-top: 12.5px;">
                                                        <input type="text" name="item_comment[<?php echo $inc ?>]; ?>" id="item_comment<?php echo $inc ?>" value="<?php echo isset($_POST['item_comment'][$i])?$_POST['item_comment'][$i]:'' ?>" placeholder="<?php echo $this->lang->line('add_item_comment'); ?>" class="form-control" maxlength="250">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <?php for($i=0,$inc=1;$i<count($menu_item);$inc++,$i++){ ?>
                                                <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                    <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                    <div class="col-md-2">
                                                        <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>,this.value)">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option> 
                                                            <?php if($entity_id){
                                                                if(!empty($menu_detail)){
                                                                foreach ($menu_detail as $key => $value) { ?>
                                                                    <option value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" <?php echo ($value->entity_id == $menu_item[$i]->item_id)?"selected":"" ?>><?php echo $value->name ?></option>    
                                                            <?php } } }?> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->qty_no)?$menu_item[$i]->qty_no:'' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>,1)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                    </div>
                                                    <!-- base price changes start -->
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('base_price') ?>" name="base_price[<?php echo $inc ?>]" id="base_price<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->rate)?$menu_item[$i]->rate:'' ?>" maxlength="20" data-required="1" class="form-control base_price validate-class" readonly="" />
                                                    </div>
                                                    <!-- base price changes end -->
                                                    <div class="col-md-2">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->rate)?$menu_item[$i]->rate:'' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                    </div>
                                                    <div class="col-md-1 remove"><?php if($entity_id && $inc > 1){ ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                    <!-- div to display addons (start)-->
                                                    <div class="col-md-12 addOns_wrapcls" id="addOns_wrap<?php echo $inc; ?>" style="margin-top: 12.5px; margin-bottom: 12.5px; padding: 0px;display: none;">
                                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label>
                                                        <div class="col-md-9" id="addOns<?php echo $inc; ?>"></div>
                                                    </div>
                                                    <!-- div to display addons (end) -->
                                                    <div class="col-md-3" style="clear: both;">
                                                    </div>
                                                    <div class="col-md-6" style="margin-top: 12.5px;">
                                                        <input type="text" name="item_comment[<?php echo $inc ?>]; ?>" id="item_comment<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]->comment)?$menu_item[$i]->comment:'' ?>" placeholder="<?php echo $this->lang->line('add_item_comment'); ?>" class="form-control" maxlength="250">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                        </div>
                                    <?php } ?>
                                    <!-- choose_order_mode : start -->
                                    <div class="form-group">  
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('choose_order_mode') ?><span class="required">*</span>
                                        </label>
                                        <input type="text" name="check_order_mode_val" id="check_order_mode_val" style="width: 0;height: 0;border:0;opacity:0">
                                        <div class="col-sm-4">
                                            <span class="order-pickup">
                                            </span>
                                            <span class="order-delivery">
                                            </span>
                                            <span class="control-label order-mode-error-msg" style="color:red;"></span>
                                        </div>
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <div id="order_mode_err" style="color:red;"></div>
                                        </div>
                                    </div>
                                    <!-- choose_order_mode : end -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address') ?><span id="address_required_span"></span></label>
                                        <div class="col-md-4">
                                            <select name="address_id" class="form-control address-line" onchange="getAddLatLong(this.value)" id="address_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>  
                                                <?php if($entity_id){
                                                        if(!empty($address)){
                                                            foreach ($address as $key => $value) { ?>
                                                                <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $address_id)?"selected":"" ?>>
                                                                    <?php echo $value->address; ?></option>    
                                                <?php } } }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group add_address" style="display: none;">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_address')?><span class="other_address_required_span"></span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="ord_latitude" id="ord_latitude" value="">
                                            <input type="hidden" name="ord_longitude" id="ord_longitude" value="">
                                            <input type="hidden"  name="default_latitude" id="default_latitude" value="" />
                                            <input type="hidden"  name="default_longitude" id="default_longitude" value="" />
                                            <input type="hidden" name="ord_city" id="ord_city" value="">
                                            <input type="text" class="form-control" name="ord_address_field" id="ord_address_field" placeholder=" " />
                                        </div>
                                        <a href="#basic" data-toggle="modal" class="btn red default"><?php echo $this->lang->line('pick_address')?> </a>
                                    </div>
                                    <div class="form-group add_address" style="display: none;">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('postal_code')?><span class="other_address_required_span"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="ord_zipcode" id="ord_zipcode" placeholder=" " minlength="5" maxlength="6" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <select name="coupon_id[]" multiple="multiple" class="form-control sumo coupon_id" id="coupon_id">
                                                <?php if(!empty($coupon)){
                                                    foreach ($coupon as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $coupon_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>" coupon_type="<?php echo $value->coupon_type ?>" ><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="<?php echo ($coupon_amount)?$coupon_amount:'' ?>" maxlength="10" data-required="1" class="form-control" readonly=""/><label class="coupon-type"><?php echo ($coupon_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <?php $service_tax_typeval = ($tax_type == 'Percentage')?' ('.$tax_rate.'%)':''; ?>
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax') ?><span id="service_tax_typeval"><?php echo $service_tax_typeval ?></span><span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <input type="hidden" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control"/>
                                            <input type="text" name="tax_rate_display" id="tax_rate_display" value="" maxlength="10" data-required="1" class="form-control" readonly="" style="display: inline-block;width: 79%;margin-right: 10px;"/>
                                            <input type="hidden" class="amount-type" /><?php //echo ($tax_rate == 'Percentage')?'%':'' ?>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <?php $service_fee_typeval = ($service_fee_type == 'Percentage')?' ('.$service_fee.'%)':''; ?>
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee') ?><span id="service_fee_typeval"><?php echo $service_fee_typeval ?></span></label>
                                        <div class="col-md-5">
                                            <input type="hidden" data-value="" name="service_fee" id="service_fee" value="<?php echo $service_fee ?>" maxlength="10" data-required="1" class="form-control"/>
                                            <input type="text" name="service_fee_display" id="service_fee_display" value="" maxlength="10" data-required="1" class="form-control" readonly="" style="display: inline-block;width: 79%;margin-right: 10px;"/>
                                            <input type="hidden" class="service-fee-type" /><?php //echo ($tax_rate == 'Percentage')?'%':'' ?>
                                            <input type="hidden" name="service_fee_type" id="service_fee_type" value="<?php echo $service_fee_type; ?>">
                                        </div>
                                    </div>
                                    <!-- delivery charge -->
                                    <div class="form-group delivery_charge_div" style="display: none;">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('delivery_charge') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery_charge" id="delivery_charge" value="" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <div id="delivery_err" style="color: red;"></div>
                                        </div>
                                        
                                    </div>
                                    <?php /* //taxes and fees :: start ?>
                                    <div class="form-group add_taxes_fees">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('taxes_fees') ?></label>
                                        <div class="col-md-4 add_tax_col">
                                            <input type="text" data-value="" name="taxes_fees" id="taxes_fees" value="0" maxlength="10" data-required="1" class="form-control" style="width:100%;display:inline-block;" readonly=""/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right">
                                                <div id="servicetax_infodiv">
                                                   <span class="custom_service"> <?php echo $this->lang->line('service_tax'); ?> <span id="servicetaxtype_info"></span></span> : <span class="service_price" id="servicetax_info">0</span>
                                                </div>
                                                <div id="servicefee_infodiv">
                                                    <span class="custom_service"><?php echo $this->lang->line('service_fee'); ?> <span id="servicefeetype_info"></span></span> : <span class="service_price" id="servicefee_info">0</span>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    <?php */ //taxes and fees :: end ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="total_rate" id="total_rate" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('order_status') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="order_status" class="form-control" id="order_status">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                unset($order_status['placed']);
                                                unset($order_status['cancel']);
                                                unset($order_status['rejected']);
                                                unset($order_status['onGoing']);
                                                unset($order_status['delivered']);
                                                foreach ($order_status as $key => $value) { ?>
                                                     <option value="<?php echo $key ?>" <?php echo ($order_status == $key)?"selected":"" ?>><?php echo $value ?></option>
                                                <?php  } ?>               
                                            </select>
                                        </div>
                                    </div> 
                                    <?php /* ?><div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('date_of_order') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class='input-group date' id='datetimepicker' data-date-format="mm-dd-yyyy HH:ii P">
                                            <input size="16" type="text" name="order_date" class="form-control" id="order_date" value="<?php echo ($order_date)?date('Y-m-d H:i',strtotime($order_date)):'' ?>" readonly="">
                                            <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            </div>
                                        </div>
                                    </div><?php */ ?>     
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel') ?></a>
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
<!-- pick address :: start -->
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo $this->lang->line('pick_address') ?></h4>
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
<!-- pick address :: end -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<!-- <script src="<?php echo base_url();?>assets/admin/pages/scripts/address-autofill.js"></script> -->
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    //Added on 19-10-2020
    /*$('.sumo').SumoSelect({search: true, searchText: '<?php echo $this->lang->line('search'); ?>'+ ' ' + '<?php echo $this->lang->line('here'); ?>...'});*/
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
$(function() {
    var date = new Date();
    $('#order_date').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate : date
    });
});
//clone items
function cloneItem(){
    var divid = $(".clone:last").attr('id'); 
    var getnum = divid.split('cloneItem');
    var oldNum = parseInt(getnum[1]);
    var newNum = parseInt(getnum[1]) + 1;
    newElem = $('#' + divid).clone().attr('id', 'cloneItem' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
    newElem.find('#item_id'+oldNum).attr('id', 'item_id' + newNum).attr('name', 'item_id[' + newNum +']').attr('onchange','getItemPrice(this.id,'+newNum+',this.value)').prop('selected',false).attr('selected',false).val('').removeClass('error');
    newElem.find('#rate'+oldNum).attr('id', 'rate' + newNum).attr('name', 'rate[' + newNum +']').val('').removeClass('error');
    //base price changes start
    newElem.find('#base_price'+oldNum).attr('id', 'base_price' + newNum).attr('name', 'base_price[' + newNum +']').val('').removeClass('error');
    //base price changes end
    newElem.find('#qty_no'+oldNum).attr('id','qty_no'+newNum).attr('name','qty_no['+newNum+']').attr('onkeyup','qty(this.id,'+newNum+',1)').val(1).removeClass('error');
    newElem.find('#item_comment'+oldNum).attr('id', 'item_comment' + newNum).attr('name', 'item_comment[' + newNum +']').val('').removeClass('error');
    //addons changes start
    newElem.find('#addOns_wrap'+oldNum).attr('id', 'addOns_wrap' + newNum).removeClass('error');
    newElem.find('#addOns'+oldNum).empty();
    newElem.find('#addOns'+oldNum).attr('id', 'addOns' + newNum).removeClass('error');
    //addons changes end
    newElem.find('.error').remove();
    newElem.find('.clone-label').css('visibility','hidden');
    $(".clone:last").after(newElem);
    $('#cloneItem' + newNum +' .remove').html('<div class="item-delete" onclick="deleteItem('+newNum+')"><i class="fa fa-remove"></i></div>');
    $("#addOns_wrap"+newNum).empty();
    $("#addOns_wrap"+newNum).css("display","none");
    validateDynamicMenu();
}
function deleteItem(id){
    $('#cloneItem'+id).remove();
    var address_id = $( "#address_id" ).val();
    var restaurant_id = $( "#restaurant_id" ).val();
    if(address_id != '' && restaurant_id != ''){
        getAddLatLong(address_id);
    }
    calculation();
    getCoupon();
}
//change coupon
$('#coupon_id').change(function(){    
    calculation();
});
//get items
function getItemDetail(id,entity_id)
{
    // Flush all data when restaurant change
    $('#subtotal').val('');
    $('#coupon_amount').val('');
    $('#tax_rate_display').val('');
    $('#service_fee_display').val('');
    $('#total_rate').val('');
    $('#coupon_id').val('');
    $('#delivery_charge').val('');
    $('.order-pickup').empty();
    $('.order-delivery').empty();
    $('.order-mode-error-msg').empty();
    
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getItem',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('.item_id').empty().append(response);
        $(".addOns_wrapcls").empty();
        $(".addOns_wrapcls").css("display","none");
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
    var element = $('#'+id).find('option:selected'); 
    var amount = element.attr("amount");
    var amount_type = element.attr("type");
    var service_fee_type = element.attr("data-service-fee-type");
    var service_fee = element.attr("data-service-fee");
    $('#tax_rate').val(amount).attr('data-value',amount_type);
    $('#service_fee').val(service_fee).attr('data-service-fee',service_fee);
    
    var sing = (amount_type == "Percentage") ? "%" : '';
    $('.amount-type').val(sing);
    var service_fee_sign = (service_fee_type == "Percentage") ? "%" : '';
    $('.service-fee-type').val(service_fee_sign);
    $('#tax_type').val(amount_type);
    $('#service_fee_type').val(service_fee_type);
    
    if($('#tax_type').val() == 'Percentage'){
        var service_tax_typeval = '('+ $('#tax_rate').val() +'%)';
        $('#service_tax_typeval').html(service_tax_typeval);
    } else {
        $('#service_tax_typeval').html('');
    }
    if($('#service_fee_type').val() == 'Percentage'){
        var service_fee_typeval = '('+ $('#service_fee').val() +'%)';
        $('#service_fee_typeval').html(service_fee_typeval);
    } else {
        $('#service_fee_typeval').html('');
    }
    getCurrency(entity_id);    
    $('#form_add_order').find('.validate-class').each(function(){
        $(this).val('');
    });
    var order_mode_type = element.attr("data-order-mode");
    if(order_mode_type){
        $("#check_order_mode_val").rules("remove");
        $(".order-mode-error-msg").empty();
        var order_mode_array = order_mode_type.split(",");
        $.each(order_mode_array, function( index, value ) {
            if(value.toLowerCase() == "pickup"){
                $('.order-pickup').empty().append('<input type="radio" name="order_mode" id="order_mode" onclick="showpickup()" value="PickUp" checked >&nbsp;&nbsp;<b id="pickuplabel"><?php echo $this->lang->line('pickup') ?></b>&ensp;');
            }
            if(value.toLowerCase() == "delivery"){
                $(".order-delivery").empty().append('<input type="radio" name="order_mode" id="order_mode" onclick="showdelivery()" value="Delivery">&nbsp;&nbsp;<b><?php echo $this->lang->line('delivery_order') ?></b>');
            }
        });
    }else{
        $("#check_order_mode_val").rules( "add", {
            required: true
        });
        $(".order-mode-error-msg").empty().html("<?php echo $this->lang->line('order_mode_error_msg');?>");
    }
}
//get item price
function getItemPrice(id,num,value){
    var element = $('#'+id).find('option:selected'); 
    var myTag = element.attr("data-id"); 
     // addons changes (start)
    var checkAddOns = element.attr("data-addOns");
    if(checkAddOns == "1") {
        var html = '<label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label><div class="col-md-9" id="addOns'+num+'"></div>';
        $("#addOns_wrap"+num).empty().append(html);
        $("#addOns_wrap"+num).css("display","block");
        $('#qty_no' + num).prop("readonly", false);
        $('#qty_no' + num).val(1);
        //base price changes start
        if(myTag){
            $('#rate' + num).val(myTag);
            $('#base_price' + num).val(myTag);
        }
        //base price changes end
    } else {
        $("#addOns_wrap"+num).empty();
        $("#addOns_wrap"+num).css("display","none");
        $('#rate'+num).val(myTag);
        //base price changes start
        $('#base_price'+num).val(myTag);
        //base price changes end
        $('#qty_no' + num).prop("readonly", false);
        $('#qty_no' + num).val(1);
    }
    // addons changes (end)
    calculation();
    getAddonsList(value,num);
    getCoupon();
}
// addons changes (start)
function getAddonsList(menu_entity_id,num){
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getAddonsList',
      data : {'entity_id':menu_entity_id,'num':num,'restaurant_id':$( "#restaurant_id" ).val()},
      success: function(response) {
        $('#addOns'+num).empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
// addons changes (end)
function qty(id,num,frmqty) {
    $('#'+id).keyup(function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });
    var element = $('#item_id'+num).find('option:selected'); 
    var myTag = element.attr("data-id");
    var qtydata = parseInt($('#qty_no' + num).val());
    qtydata = Math.abs(qtydata);
    if(!isNaN(qtydata)){
        $('#qty_no' + num).val(qtydata);
    }
    else {
        $('#qty_no' + num).val('');
    }
    if(isNaN(qtydata)){    
        qtydata = 0;
        //changes for base price start
        if(myTag && frmqty==0){
            qtydata = 1;
        }
        //changes for base price end
    }
    var total = parseFloat(qtydata * myTag);
    
    if(!isNaN(total)){    
        $('#rate'+num).val(total.toFixed(2));
    }
    //changes for addons start
    var price = 0;
    var elements = $('#addOns'+num).find('input:checked');
    //changes for base price start
    if(myTag){
        price += parseFloat(myTag);
    }
    //changes for base price end
    elements.each(function()
    {
        if(!isNaN($(this).attr('data-price')) && (!isNaN($(this).attr('value'))) && $(this).attr('value') != '' && $(this).attr('data-price') != ''){
            var temp_addons_id = $(this).attr('value');
            var qt = parseInt($('#add_qty_no_'+num+''+temp_addons_id).val());
            price += parseFloat(qt) * parseFloat($(this).attr('data-price')); 
            $('#rate'+num).val(parseFloat(price).toFixed(2));
        }
    });
    if(frmqty==1 || frmqty==2) // base price changes
    {
       var total = parseFloat(qtydata * price).toFixed(2);
       if(!isNaN(total)){    
            $('#rate'+num).val(total);
        }
    }
    //Code added for the insurance calculation :: 26-09-2020 :: Start
    var pricecal = total;
    pricecal = parseFloat(pricecal).toFixed(2);
    var sum=0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != ''){
            sum += parseFloat($(this).val()); 
        }
    });
    $('#subtotal').val(sum.toFixed(2));
    // changes for addons end
    var address_id = $( "#address_id" ).val();
    var restaurant_id = $( "#restaurant_id" ).val();
    if(address_id != '' && restaurant_id != ''){
        getAddLatLong(address_id);
    }
    calculation();
    getCoupon();
}
// addons changes (start)
function calculate_rate(num,addons_id,chkradio,catname)
{
    if(chkradio=='1')
    {
        $('.radioclassq_'+num+''+catname).val('');
        $('.radioclassqp_'+num+''+catname).prop("readonly", true);
    }
    var value = $('#add_qty_no_'+num+''+addons_id).val();
    
    if(value=='' && ($('#addons_id_'+num+''+addons_id).prop("checked") == true))
    {
        $('#add_qty_no_'+num+''+addons_id).val('1');
    }
    else if($('#addons_id_'+num+''+addons_id).prop("checked") == false)
    {
        $('#add_qty_no_'+num+''+addons_id).val('');
    } 
    var val = 0;
    var elements = $('#addOns'+num).find('input:checked'); // find checked checkbox
    if(elements) {
        $('#qty_no'+num).val('');
    }
    elements.each(function()
    {
        if(!isNaN($(this).attr('value')) && $(this).attr('value') != '')
        {
            var temp_addons_id = $(this).attr('value'); // it will return add-on_ids
            val = parseInt($('#add_qty_no_'+num+''+temp_addons_id).val()); // get value of addon_quantity added
            $('#qty_no'+num).val(parseInt(val)); //set it to parent quantity
        }
    });
   qty('qty_no'+num,num,0); // to count final rate
   getCoupon();
}
// addons changes (end)
//calculate total rate
function calculation()
{
    var sum = 0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != ''){
            sum += parseFloat($(this).val()); 
        }
    });
    $('#subtotal').val(sum.toFixed(2));

    //Coupon :: Start
    var slected_cnt = 0;
    $('.coupon_id').children('option:selected').each(function()
    {
        slected_cnt++;
    });    
    var cpn_amt = 0; $('#coupon_amount').val('');
    $('.coupon_id').children('option:selected').each(function()
    {
        var coupon_type = $(this).attr("coupon_type");
        if(coupon_type=='free_delivery'){
            var deliverychargeval = $("#delivery_charge").val();
            $(this).attr("type",'Amount');
            $(this).attr("amount",deliverychargeval);
        }
        var is_mutliple_coupons = $(this).attr("is_mutliple_coupons");
        
        //Code for check the coupon use with another coupon :: Start
        if(is_mutliple_coupons==0 && slected_cnt>1)
        {
            var coupon_namemsg = $(this).attr("c_name");            
            var error_message = "<?php echo $this->lang->line('coupon_use_error');?>";
            bootbox.alert(coupon_namemsg+" "+error_message);

            $('.coupon_id')[0].sumo.unSelectItem($(this).index());
            $(this).prop('checked',false);
        }
        else
        {
            var type = $(this).attr("type");
            var amount = $(this).attr("amount");
            var cpn = 0;
            if(type == 'Percentage' && amount != ''){
                var cpn = (parseFloat(sum*amount)/100);
                cpn = cpn.toFixed(2);
                cpn_amt = parseFloat(cpn_amt)+parseFloat(cpn);
            }else if(type == 'Amount' && amount != ''){
                cpn_amt =  parseFloat(cpn_amt)+parseFloat(amount);
            }
        }
        //Code for check the coupon use with another coupon :: End        
    });
    cpn_amt = cpn_amt.toFixed(2);
    //Coupon :: End

    //tax
    var tax = $('#tax_rate').val();
    if(tax){
        if($('.amount-type').val() == '' && !isNaN(tax) && tax != ''){
            tax_amt = parseFloat(tax);
        }else if(!isNaN(tax) && tax != ''){
            //var taxs = Math.round(parseInt(sum*tax)/100);
            //tax_amt = parseInt(taxs);
            var tax_cal = (sum*tax)/100;
            var taxs = (Math.round(tax_cal*100))/100;
            tax_amt = taxs;
        }
    }
    service_fee_amt = '';
    $('#tax_rate_display').val(tax_amt);
    //service fee
    var service_fee = $('#service_fee').val();
    if(service_fee){
        if($('.service-fee-type').val() == '' && !isNaN(service_fee) && service_fee != ''){
            service_fee_amt = parseFloat(service_fee);
        }else if(!isNaN(service_fee) && service_fee != ''){
            //var service_fees = Math.round(parseInt(sum*service_fee)/100);
            //service_fee_amt = parseInt(service_fees);
            var servicefee_cal = (sum*service_fee)/100;
            var service_fees = (Math.round(servicefee_cal*100))/100;
            service_fee_amt = service_fees;
        }
    }
    $('#service_fee_display').val(service_fee_amt);
    //taxes and fees :: start
    /*var taxes_fees = parseFloat(tax_amt) + parseFloat(service_fee_amt);
    $('#taxes_fees').val(taxes_fees.toFixed(2));
    if(parseFloat(tax_amt) > 0) {
        if ($('.amount-type').val() != '' && !isNaN(tax) && tax != '') {
            $('#servicetaxtype_info').text('('+tax+'%)');
        } else {
            $('#servicetaxtype_info').text('');
        }
        $('#servicetax_info').text(parseFloat(tax_amt));
    } else {
        $('#servicetax_infodiv').css('display','none');
    }
    if(parseFloat(service_fee_amt) > 0) {
        if ($('.service-fee-type').val() != '' && !isNaN(service_fee) && service_fee != '') {
            $('#servicefeetype_info').text('('+service_fee+'%)');
        } else {
            $('#servicefeetype_info').text('');
        }
        $('#servicefee_info').text(parseFloat(service_fee_amt));
    } else {
        $('#servicefee_infodiv').css('display','none');
    }*/
    //taxes and fees :: end
    if(!isNaN(cpn_amt) && cpn_amt > 0){
        sum = sum - parseFloat(cpn_amt);
        $('#coupon_amount').val(cpn_amt);
        //$('#coupon_type').val(type);
    }
    if(!isNaN(tax) && tax != ''){
        sum += tax_amt;
    }
    if(!isNaN(service_fee) && service_fee != ''){
        sum += service_fee_amt;
    }
    // delivery charge : start
    var delivery_charge = $("#delivery_charge").val();
    if(delivery_charge != '') {
        if(delivery_charge >= 0){
            sum = sum + parseFloat(delivery_charge);
        } 
    }
    //delivery charge : end
    if(!isNaN(sum)){
        $('#total_rate').val(sum.toFixed(2));
    }
}
//get address
function getAddress(entity_id){
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getAddress',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('.address-line').empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
//validation for menu item
jQuery(document).ready(function() {    
    validateDynamicMenu();   
});    
function validateDynamicMenu(){
    $('#form_add_order').find('.validate-class').each(function(){
        $(this).rules("add", 
        {
            required: true
        });
    });
}
function format_indonesia_currency(amt) {
    var number = amt;       
    return  n =  number.toLocaleString('id-ID', {currency: 'IDR'});
}
function getCoupon(){
    var restaurant_id = $( "#restaurant_id" ).val();
    var subtotal = $( "input[name='subtotal']" ).val(); 
    var user_id = $( "#user_id" ).val();
    var radioValue = $("input[name='order_mode']:checked").val();
    
    if(restaurant_id != '' && subtotal != ''){
        jQuery.ajax({
          type : "POST",
          dataType :"html",
          url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getCoupon',
          data : {'subtotal':subtotal,'restaurant_id':restaurant_id,'user_id':user_id,'order_delivery':radioValue},
          success: function(response) {            
            $('.coupon_id').empty().append(response);
            $('.coupon_id')[0].sumo.reload();   
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {          
            alert(errorThrown);
          }
        });
    }
}
/*add address start*/
jQuery(document).ready(function() {
    /*initAutocomplete('address_field');*/
    //initAutocomplete('ord_address_field');
});
// autocomplete function
var autocomplete;
function initAutocomplete(id) {
    autocomplete = new google.maps.places.Autocomplete(
    document.getElementById(id), {
        componentRestrictions: { country: ["us","in","pk"]},
        fields: ["formatted_address","address_components", "geometry", "icon", "name"],
    });
    //autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', getAddressDetails);
}
function getAddressDetails(){
    var place = autocomplete.getPlace();   
    window.lat = place.geometry.location.lat();
    window.long = place.geometry.location.lng();
}
function geolocate(){
    initAutocomplete('ord_address_field');
  if (navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position){
        var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };
        var circle = new google.maps.Circle(
        {center: geolocation, radius: position.coords.accuracy});
        autocomplete.setBounds(circle.getBounds());
    });
  }
}
google.maps.event.addDomListener(window, 'load', function() {
    const optionsObj_placesone = {
      componentRestrictions: { country: ["us","in","pk"]},
      fields: ["formatted_address","address_components", "geometry", "icon", "name"],
    };
    var placesone = new google.maps.places.Autocomplete(document.getElementById("address_field"), optionsObj_placesone);
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
          center: geolocation, radius: 1000
        });
        placesone.setBounds(circle.getBounds());
      });
    }
    google.maps.event.addListener(placesone, 'place_changed', function() {
        var placeone = placesone.getPlace();
        var lat = placeone.geometry.location.lat();
        var long = placeone.geometry.location.lng();
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = long;        
        var  value = placeone.formatted_address.split(",");
        if(placeone.name == value[0]){
            document.getElementById("address_field").value = placeone.formatted_address;    
        }else{
            document.getElementById("address_field").value = placeone.name+', '+placeone.formatted_address;
        }
        document.getElementById("city").value = '';
        document.getElementById("zipcode").value = '';
        $.each(placeone.address_components, function( index, value ) {
            $.each(value.types, function( index, types ) {
               
                if(types == 'locality'){
                   document.getElementById("city").value = value.long_name;
                }
                if(types == 'postal_code'){
                   document.getElementById("zipcode").value = value.long_name;
                }
            });
        });
    });
    //ord_address_field
    const optionsObj_places = {
      componentRestrictions: { country: ["us","in","pk"]},
      fields: ["formatted_address","address_components", "geometry", "icon", "name"],
    };
    var places = new google.maps.places.Autocomplete(document.getElementById("ord_address_field"), optionsObj_places);
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
          center: geolocation, radius: 1000
        });
        places.setBounds(circle.getBounds());
      });
    }
    google.maps.event.addListener(places, 'place_changed', function() {
        var place = places.getPlace();

        var lat = place.geometry.location.lat();
        var long = place.geometry.location.lng();
        document.getElementById("ord_latitude").value = lat;
        document.getElementById("ord_longitude").value = long;
        
        var  value = place.formatted_address.split(",");
        if(place.name == value[0]){
            document.getElementById("ord_address_field").value = place.formatted_address;    
        }else{
            document.getElementById("ord_address_field").value = place.name+', '+place.formatted_address;
        }
        getAddLatLong('other');
        document.getElementById("ord_city").value = '';
        document.getElementById("ord_zipcode").value = '';
        $.each(place.address_components, function( index, value ) {
            $.each(value.types, function( index, types ) {
                /*if(types == 'administrative_area_level_2'){
                   document.getElementById("ord_city").value = value.long_name;
                }*/
                if(types == 'locality'){
                   document.getElementById("ord_city").value = value.long_name;
                }
                if(types == 'postal_code'){
                   document.getElementById("ord_zipcode").value = value.long_name;
                }
            });
        });
    });
});
/*add address end*/
/*add new user start*/
$("#add_new_user").click(function(){
    $(".add_new_user_content").css("display", "block");
    $('select.select_user_id')[0].sumo.selectItem(0);
    $('select#restaurant_id')[0].sumo.selectItem(0);
    $('.qty').val('');
    $('.base_price').val('');
    $('.rate').val('');
    $('#address_id').val('');
    $('#coupon_id').val('');
    $('#coupon_amount').val('');
    $('#subtotal').val('');
    $('#tax_rate').val('');
    $('#delivery_charge').val('');
    $('#ord_address_field').val('');
    $('#ord_zipcode').val('');
    $('#total_rate').val('');
    $('#order_status').val('');
    $('#scheduled_date').val('');
    $('#order_date').val('');
});
$("#cancel_adduser").click(function(){
    $(".add_new_user_content").css("display", "none");
    $("#form_add_user_fororder").validate().resetForm();
    $('#first_name').val('');
    $("#last_name").val('');
    $('#mobile_number').val('');
    $('#email').val('');
    $('#address_field').val('');
    $('#zipcode').val('');
    $('#city').val('');
});
//check phone number exist
function checkExistPhnNo(mobile_number){
    var phone_code = $('#phone_code').val();
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkExistPhnNo",
        data: {'mobile_number':mobile_number,'phone_code':phone_code},
        cache: false,
        success: function(response) {
          if(response.numrows > 0){
            if(response.user_id != ''){
                $('select.select_user_id')[0].sumo.selectItem(response.user_id);
                //$(".add_new_user_content").css("display", "none");
                
                $('#first_name').val('');
                $('#last_name').val('');
                $('#mobile_number').val('');
                $('#email').val('');
                $('#address_field').val('');
                $('#zipcode').val('');
                $('#city').val('');
                
                bootbox.confirm({
                    message: "<?php echo $this->lang->line('user_existmsg'); ?>",
                    buttons: {
                        confirm: {
                            label: "<?php echo $this->lang->line('ok'); ?>",
                        }
                    },
                    callback: function (userchkConfirm) {
                        if (userchkConfirm) {
                            $("#form_add_user_fororder").validate().resetForm();
                            $(".add_new_user_content").hide( "slow" );
                        }
                    }
                });
            } else {
                $('#phoneExist').show();
                $('#phoneExist').html("<?php //echo $this->lang->line('phone_exist'); ?>");
                $("#submit_adduser").prop('disabled', true); 
            }
          } else {
            $('#phoneExist').html("");
            $('#phoneExist').hide();
            $("#submit_adduser").prop('disabled', false); 
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {                 
          $('#phoneExist').show();
          $('#phoneExist').html(errorThrown);
        }
    });
}
function checkEmailExist(email){
  $.ajax({
    type: "POST",
    dataType: 'json',
    url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkEmailExist",
    data: {'email':email},
    cache: false,
    success: function(response) {
        if(response.numrows > 0){
            if(response.user_id != ''){
                $('select.select_user_id')[0].sumo.selectItem(response.user_id);
                //$(".add_new_user_content").css("display", "none");
                $('#first_name').val('');
                $('#last_name').val('');
                $('#mobile_number').val('');
                $('#email').val('');
                $('#address_field').val('');
                $('#zipcode').val('');
                $('#city').val('');
                
                bootbox.confirm({
                    message: "<?php echo $this->lang->line('user_existmsg'); ?>",
                    buttons: {
                        confirm: {
                            label: "<?php echo $this->lang->line('ok'); ?>",
                        }
                    },
                    callback: function (userchkConfirm) {
                        if (userchkConfirm) {
                            $("#form_add_user_fororder").validate().resetForm();
                            $(".add_new_user_content").hide( "slow" );
                        }
                    }
                });
            }else{
                $('#EmailExistAdd').show();
                $('#EmailExistAdd').html("<?php echo $this->lang->line('alredy_exist'); ?>");
                $("#submit_adduser").prop('disabled', true);
            }
        }else{
            $('#EmailExistAdd').html("");
            $('#EmailExistAdd').hide();
            $("#submit_adduser").prop('disabled', false);            
        }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExistAdd').show();
      $('#EmailExistAdd').html(errorThrown);
    }
  });
}
//generate password
function generatePassword() {
    var length = 8,
        charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}
//ajax call on submitting add new user
$( "#form_add_user_fororder" ).on("submit", function( event ) {
    event.preventDefault();
    if($('#first_name').val() != '' && $("#last_name").val() !='' && $('#mobile_number').val() != '' && $('#email').val() != '' && $('#address_field').val() != '' && $('#zipcode').val() != '' && $('#city').val() != '') {
        //call generate password here
        var password = generatePassword();
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASEURL+"<?php echo ADMIN_URL ?>/order/add_new_user",
            data: {'submit_adduser':$('#submit_adduser').val(), 'first_name':$('#first_name').val(), 'last_name':$('#last_name').val(), 'mobile_number':$('#mobile_number').val(), 'email':$('#email').val(), 'address_field':$('#address_field').val(), 'zipcode':$('#zipcode').val(), 'city': $('#city').val(), 'password':password, 'latitude':$('#latitude').val(), 'longitude':$('#longitude').val(), 'phone_code':$('#phone_code').val() },
            cache: false,
            success: function(response) {
                if(response.status == 1){
                    var selectvalue = response.user_id;
                    var selecttext = response.first_name+' '+response.last_name+' (+'+response.phone_code+response.mobile_number+')';
                    $('select.select_user_id')[0].sumo.add(selectvalue,selecttext);
                    $(".add_new_user_content").css("display", "none");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#mobile_number").val("");
                    $("#email").val("");
                    $("#address_field").val("");
                    $("#zipcode").val("");
                    $("#city").val("");
                    $('#latitude').val("");
                    $('#longitude').val("");
                    $('select.select_user_id')[0].sumo.selectItem(response.user_id);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {                 
              
            }
        });
    } 
});
/*add new user end*/
/*order mode changes start*/
function showdelivery() {
    $('#address_required_span').addClass('required');
    $('#address_required_span').html('*');
    <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin') { ?>
        $(".delivery_charge_div").css("display", "block");
    <?php } ?>
    var address_id = $( "#address_id" ).val();
    var restaurant_id = $( "#restaurant_id" ).val();

    if(address_id == 'other'){
        $('.other_address_required_span').addClass('required');
        $('.other_address_required_span').html('*');
    }
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'backoffice/order/getdeliveryoption',
        data : {"order_mode":'delivery'},
        success: function(html) {
            $('#order_status').empty().append(html);            
          }
    });

    getCoupon();
    if(address_id != '' && restaurant_id != ''){
        getAddLatLong(address_id);
    }
}
function showpickup() {
    $('#address_required_span').removeClass('required');
    $('#address_required_span').html('');
    $('.other_address_required_span').removeClass('required');
    $('.other_address_required_span').html('');
    $("#delivery_charge").val('');
    $(".delivery_charge_div").css("display", "none");
    $("#submit_page").attr("disabled", false);

    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'backoffice/order/getdeliveryoption',
        data : {"order_mode":'pickup'},
        success: function(html) {
            $('#order_status').empty().append(html);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        //alert(errorThrown);
        }
    });

    getCoupon();
    calculation();
}
// get delivery charges
function getDeliveryCharges(latitude,longitude,cart_total,restaurant_id){
    var radioValue = $("input[name='order_mode']:checked").val();
    if(radioValue == 'Delivery') { var action = 'get'; } else { var action = 'remove'; }
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'backoffice/order/getDeliveryCharges',
    data : {"latitude":latitude,"longitude":longitude,"action":action,'cart_total':cart_total,'restaurant_id':restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
        if (response.checkDelivery == 'available') {
            if(action == "get") {
                if (response.deliveryCharge != '' || response.deliveryCharge >= 0) {
                    //delivery available
                    var delivery_charge = parseFloat(response.deliveryCharge);
                    $("#submit_page").attr("disabled", false);
                    $("#delivery_charge").val(delivery_charge.toFixed(2));
                    $('#delivery_err').hide();
                    calculation();
                } else {
                    //delivery not available
                    $("#submit_page").attr("disabled", true);
                    $("#delivery_charge").val('');
                    $("#delivery_err").html("<?php echo $this->lang->line('restaurant_delivery_not_available'); ?>");
                    $('#delivery_err').show();
                    calculation();
                }
            } else {
                $("#delivery_charge").val('');
                $("#submit_page").attr("disabled", false);
                $('#delivery_err').hide();
                calculation();
            }
        } else if(response.checkDelivery == 'notAvailable') {
            //disable submit btn : delivery not available.
            $("#submit_page").attr("disabled", true);
            $("#delivery_err").html("<?php echo $this->lang->line('restaurant_delivery_not_available'); ?>");
            $('#delivery_err').show();
            calculation();
        } else {
            $("#delivery_charge").val('');
            $("#submit_page").attr("disabled", false);
            $('#delivery_err').hide();
            calculation();
        }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get delivery charges from the address
function getAddLatLong(address_id){
    var restaurant_id = $( "#restaurant_id" ).val();
    $("#delivery_charge").val('');
    if(address_id == 'other'){
        var ord_address_field = $('#ord_address_field').val();
        var ord_latitude = $('#ord_latitude').val();
        var ord_longitude = $('#ord_longitude').val();

        $('.add_address').css('display','block');
        var selectedordermode = $("input[name='order_mode']:checked").val();
        if(selectedordermode == 'Delivery') {
            $('.other_address_required_span').addClass('required');
            $('.other_address_required_span').html('*');
        }

        if(address_id == 'other' && restaurant_id !='' && ord_address_field !='' && ord_latitude != '' && ord_longitude != '') {
            var subtotal = $('#subtotal').val();
            getDeliveryCharges(ord_latitude,ord_longitude,subtotal,restaurant_id);
        }
    } else {
        $('.add_address').css('display','none');
        $('#ord_address_field').val('');
        $('#ord_zipcode').val('');

        if(restaurant_id !='' && address_id != ''){
            jQuery.ajax({
                type : "POST",
                dataType : "json",
                url : BASEURL+'backoffice/order/getAddressLatLng',
                data : {"entity_id":address_id},
                success: function(response) {
                    var subtotal = $('#subtotal').val();
                    getDeliveryCharges(response.latitude,response.longitude,subtotal,restaurant_id);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
                }
            });
        }    
    }
}
/*order mode changes end*/
</script>
<script type="text/javascript">
<?php $iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>

var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
// Initialize the intl-tel-input plugin :: start
const phoneInputField = document.querySelector("#mobile_number");
const phoneInput = window.intlTelInput(phoneInputField, {
    initialCountry: default_iso,
    preferredCountries: [default_iso],
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
//intl-tel-input plugin :: end
//pick address :: start
$("#basic").on("shown.bs.modal", function () {
    $('#gmap_geocoding_address').val('');    
    if (navigator.geolocation){    
        // init geocoding Maps - calling success and fail function - mapGeocoding
        navigator.geolocation.getCurrentPosition(mapGeocoding,mapGeocoding);
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
    // when no permission of location
    var default_latitude = 0;
    var default_longitude = 0;
    if ( typeof(position.coords) !== "undefined" && position.coords !== null ) {
        default_latitude = position.coords.latitude;   
        default_longitude = position.coords.longitude;
    }
    else
    {
       var default_latitude = $("#default_latitude").val();   
       var default_longitude =  $("#default_longitude").val();
    }
        
    var map = new GMaps({
        div: '#gmap_geocoding',
        lat: <?php echo ($latitude) ? $latitude : default_latitude;?>,
        lng: <?php echo ($longitude) ? $longitude : default_longitude;?>,
        click: function (e) {           
           placeMarker(e.latLng);
        }       
    }); 
    map.addMarker({
        lat: <?php echo ($latitude) ? $latitude : default_latitude;?>,
        lng: <?php echo ($longitude) ? $longitude : default_longitude;?>,
        title: 'Ahmedabad',
        draggable: true,
        dragend: function(event) {
            // $("#ord_latitude").val(event.latLng.lat());
            // $("#ord_longitude").val(event.latLng.lng());
            getAddressFromLatLong(event.latLng.lat(),event.latLng.lng());
        }
    });   
    function placeMarker(location) {                       
        map.removeMarkers();
        // $("#ord_latitude").val(location.lat());
        // $("#ord_longitude").val(location.lng());
        getAddressFromLatLong(location.lat(),location.lng());
        map.addMarker({
            lat: location.lat(),
            lng: location.lng(),
            draggable: true,
            dragend: function(event) {
                // $("#ord_latitude").val(event.latLng.lat());
                // $("#ord_longitude").val(event.latLng.lng());
                getAddressFromLatLong(event.latLng.lat(),event.latLng.lng());
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
                            // $("#ord_latitude").val(event.latLng.lat());
                            // $("#ord_longitude").val(event.latLng.lng());
                            getAddressFromLatLong(event.latLng.lat(),event.latLng.lng());
                        }
                    });
                    // $("#ord_latitude").val(latlng.lat());
                    // $("#ord_longitude").val(latlng.lng());
                    getAddressFromLatLong(latlng.lat(),latlng.lng());
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
function getAddressFromLatLong(latitude,longitude){
    $("#ord_latitude").val(latitude);
    $("#ord_longitude").val(longitude);
    jQuery.ajax({
        type : "POST",
        dataType : "json",
        url :  BASEURL+'backoffice/order/getAddressFromLatLong',
        data : {"latitude":latitude,"longitude":longitude},
        success: function(response) {
            if(response.address){
                $('#gmap_geocoding_address').val(response.address);
                $('#ord_address_field').val(response.address);
                $('#ord_city').val(response.city);
                $('#ord_zipcode').val(response.zipcode);
                getAddLatLong('other');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
        }
    });
}
//pick address :: end
$(document).ready(function(){
    $('#form_add_order').submit(function() {
        $('.addon-validate').each(function() {
            var checkbox_count = $(this).find('.check_addons').filter(':checked').length;
            var radio_count = $(this).find('.radio_addons').filter(':checked').length;
            if(checkbox_count == 0){
                $(this).find('.check_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
            if(radio_count == 0){
                $(this).find('.radio_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
        });
        if(jQuery('#form_add_order').valid()) {
            document.getElementById("form_add_order").submit();
        }else{
            return false;
        }
    });
});
</script>
<?php $this->load->view(ADMIN_URL.'/addons_max_selection');?>
<?php $this->load->view(ADMIN_URL.'/footer');?>