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
}
$add_label    = $this->lang->line('title_admin_orderdineadd');       
$form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/dinein_add";
$menu_item = array('1'=>'');
$restaurant_id = isset($_POST['restaurant_id'])?$_POST['restaurant_id']:$restaurant_id;
$menu_detail     = $this->order_model->getItem($restaurant_id);
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/dine_in_orders"><?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?></a>
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
            <!-- New User Add :: start -->
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
                                            <input type="text" name="first_name_add" id="first_name_add" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('last_name')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="last_name_add" id="last_name_add" value="" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_number')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
                                            <input type="tel" onblur="checkExistPhnNo(this.value)" name="mobile_number_add" id="phone_number" value="" data-required="1" class="form-control" placeholder=" " maxlength="12"/>
                                            <div class="phn_err"  style="display: none; color: red;"></div>
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_email')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email_add" id="email_add" onblur="checkEmailExist(this.value)" value="" maxlength="99" data-required="1" class="form-control"/>
                                        </div>
                                        <div id="EmailExistAdd"></div>
                                    </div>
                                    <input type="hidden" name="user_type" id="user_type" value="User">
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="latitude" id="latitude" value="">
                                            <input type="hidden" name="longitude" id="longitude" value="">
                                            <input type="text" class="form-control" name="address_field" id="address_field" placeholder=" " onFocus="geolocate()" />
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
                            <form action="<?php echo $form_action;?>" id="form_adddine<?php echo $this->prefix ?>" name="form_adddine<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('contact_number') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="user_id" id="user_id" value="0">                                            
                                            <input type="text" placeholder="<?php echo $this->lang->line('search_with_contact') ?>" name="mobile_number" id="mobile_number" onchange="filldetail(this.value);" autocomplete="off" value="<?php echo $mobile_number;?>" data-required="1" class="form-control" maxlength="14"/>
                                        </div>
                                        <button type="button" name="add_new_user" id="add_new_user" value="add_new_user" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('add_new_user') ?></button>
                                    </div>
                                   <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('first_name') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="first_name" id="first_name" value="" maxlength="249" class="form-control"/>
                                        </div>                                        
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('last_name') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" name="last_name" id="last_name" value="" maxlength="249" class="form-control"/>
                                        </div>                                        
                                    </div>
                                    <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('contact_email')?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="email" name="email" id="email" onblur="checkEmail(this.value)" value="" maxlength="99" data-required="1" class="form-control"/>
                                            </div>
                                            <div id="EmailExist"></div>
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
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>" <?php echo ($value->is_service_fee_enable) ? 'data-service-fee-type="'.$value->service_fee_type.'"' .' '.'data-service-fee="'.$value->service_fee.'"' : '';?> ><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('table_no') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="table_id" class="form-control" id="table_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
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
                                                </div>
                                            <?php } ?>
                                            <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <select name="coupon_id" class="form-control coupon_id" id="coupon_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>  
                                                <?php if(!empty($coupon)){
                                                    foreach ($coupon as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $coupon_id)?"selected":"" ?> amount="<?php echo $value->amount ?>" type="<?php echo $value->amount_type ?>"><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="<?php echo ($coupon_amount)?$coupon_amount:'' ?>" maxlength="10" data-required="1" class="form-control" readonly=""/ style="display: inline-block;width: 79%;margin-right: 10px;"><label class="coupon-type"><?php echo ($coupon_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax') ?><span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="display: inline-block;width: 79%;margin-right: 10px;"/><label class="amount-type"><?php echo ($tax_rate == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="service_fee" id="service_fee" value="<?php echo $service_fee ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="display: inline-block;width: 79%;margin-right: 10px;"/><label class="service-fee-type"><?php echo ($tax_rate == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="service_fee_type" id="service_fee_type" value="<?php echo $service_fee_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
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
                                                <?php $order_status = dinein_order_status($this->session->userdata('language_slug'));
                                                unset($order_status['placed']);
                                                unset($order_status['cancel']);
                                                unset($order_status['rejected']);
                                                foreach ($order_status as $key => $value) { ?>
                                                     <option value="<?php echo $key ?>" <?php echo ($order_status == $key)?"selected":"" ?>><?php echo $value ?></option>
                                                <?php  } ?>               
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('date_of_order') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class='input-group date' id='datetimepicker' data-date-format="mm-dd-yyyy HH:ii P">
                                            <input size="16" type="text" name="order_date" class="form-control" id="order_date" value="<?php echo ($order_date)?date('Y-m-d H:i',strtotime($order_date)):'' ?>" readonly="">
                                            <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            </div>
                                        </div>
                                    </div>      
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/dine_in_orders"><?php echo $this->lang->line('cancel') ?></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/jquery-ui.css">
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui.js"></script>
<?php //Style for auto suggest :: start ?>
<style>
    .ui-autocomplete {z-index:1;overflow: scroll;overflow-x: hidden;height: auto; max-height: 240px; }    
</style>
<?php //Style for auto suggest :: end ?>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    //Added on 19-10-2020
       $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
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
    //addons changes start
    newElem.find('#addOns_wrap'+oldNum).attr('id', 'addOns_wrap' + newNum).removeClass('error');
    newElem.find('#addOns'+oldNum).empty();
    newElem.find('#addOns'+oldNum).attr('id', 'addOns' + newNum).removeClass('error');
    //addons changes end
    newElem.find('.error').remove();
    newElem.find('.clone-label').css('visibility','hidden');
    $(".clone:last").after(newElem);
    $('#cloneItem' + newNum +' .remove').html('<div class="item-delete" onclick="deleteItem('+newNum+')"><i class="fa fa-remove"></i></div>');  
    validateDynamicMenu();
}
function deleteItem(id){
    $('#cloneItem'+id).remove();
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
    $('#tax_rate').val('');
    $('#service_fee').val('');
    $('#total_rate').val('');
    $('#coupon_id').val('');
    
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getItem',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('.item_id').empty().append(response);
        $(".addOns_wrapcls").empty();
        $(".addOns_wrapcls").css("display","none");
        //Code for find the table no
        gettable(entity_id);
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
    $('.amount-type').html(sing);
    $('#tax_type').val(amount_type);
    $('#service_fee_type').val(service_fee_type);
    var service_fee_sign = (service_fee_type == "Percentage") ? "%" : '';
    $('.service-fee-type').html(service_fee_sign);
    
    getCurrency(entity_id);    
    $('#form_adddine_order').find('.validate-class').each(function(){
         $(this).val('');
    });
}
//get table
function gettable(entity_id)
{
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getTable',
      data : {'entity_id':entity_id,},
      success: function(response) {
        $('#table_id').empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });    
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
        // $('#qty_no' + num).val('');
        // $('#rate' + num).val('');
        //base price changes start
        if(myTag){
            $('#rate' + num).val(myTag);
            $('#base_price' + num).val(myTag);
        }
        //base price changes end
    } else {
        //var html = '<label class="control-label col-md-3"><?php //echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label><div class="col-md-9" id="addOns'+num+'"></div>';
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
    //$('#rate'+num).val(myTag);
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
    if(frmqty==1 || frmqty==2) // myTag == '' &&  - removed this as part of base price changes
    {
       var total = parseFloat(qtydata * price).toFixed(2);
       if(!isNaN(total)){    
            $('#rate'+num).val(total);
        }
    }
    //Code added for the insurance calculation :: 26-09-2020 :: Start
    var pricecal = total;
    pricecal = parseFloat(pricecal).toFixed(2);
    // changes for addons end
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
    // console.log('value =' + value);
    
    // console.log('addons =' + $('#addons_id_'+num+''+addons_id).prop("checked"));
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
    var element = $('#coupon_id').find('option:selected');
    var type = element.attr("type"); 
    var amount = element.attr("amount"); 
    $('#coupon_amount').val(amount);
    $('#coupon_type').val(type);
    var sing = (type == "Percentage") ? "%" : '';
    $('.coupon-type').html(sing);
    var sum = 0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != ''){
            sum += parseFloat($(this).val()); 
        }
    });
    $('#subtotal').val(sum.toFixed(2));
       
    //tax
    var tax = $('#tax_rate').val();
    if(tax){
        if($('.amount-type').html() == '' && !isNaN(tax) && tax != ''){
            tax_amt = parseFloat(tax);
        }else if(!isNaN(tax) && tax != ''){
            var taxs = Math.round(parseInt(sum*tax)/100);
            tax_amt = parseInt(taxs);
        }
    }
    var service_fee = $('#service_fee').val();
    if(service_fee){
        if($('.service-fee-type').html() == '' && !isNaN(service_fee) && service_fee != ''){
            service_fee_amt = parseFloat(service_fee);
        }else if(!isNaN(service_fee) && service_fee != ''){
            var service_fees = Math.round(parseInt(sum*service_fee)/100);
            service_fee_amt = parseInt(service_fees);
        }
    }
    //coupon
    var cpn_amt = 0;
    if(type == 'Percentage' && amount != ''){
        var cpn = parseFloat(sum*amount)/100;
        cpn_amt = cpn;
    }else if(type == 'Amount' && amount != ''){
        cpn_amt =  amount;
    }
    if(!isNaN(cpn_amt) && cpn_amt > 0){
        sum = sum - parseFloat(cpn_amt);
    }
    if(!isNaN(tax) && tax != ''){
        sum += tax_amt;
    }
    if(!isNaN(service_fee) && service_fee != ''){
        sum += service_fee_amt;
    }
    if(!isNaN(sum)){
        $('#total_rate').val(sum.toFixed(2));
    }
}
//validation for menu item
jQuery(document).ready(function() {    
    validateDynamicMenu();   
});    
function validateDynamicMenu(){
    $('#form_adddine_order').find('.validate-class').each(function(){
        $(this).rules("add", 
        {
            required: true
        });
    });
}
$('#form_adddine_order').bind('submit',function(e){
    $('.validate-class').each(function(){
        var id = $(this).attr('id');
        if($('#'+id).val() == ''){
            $('#'+id).attr('required',true);
            $('#'+id).addClass('error');
        }
    });
});
function format_indonesia_currency(amt) {
    var number = amt;       
    return  n =  number.toLocaleString('id-ID', {currency: 'IDR'});
}
function getCoupon(){
    var restaurant_id = $( "#restaurant_id" ).val();
    var subtotal = $( "input[name='subtotal']" ).val(); 
    var user_id = $( "#user_id" ).val();
    
    if(restaurant_id != '' && subtotal != ''){
        jQuery.ajax({
          type : "POST",
          dataType :"html",
          url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getCoupon',
          data : {'subtotal':subtotal,'restaurant_id':restaurant_id,'user_id':user_id,'order_delivery':'DineIn'},
          success: function(response) {
            $('.coupon_id').empty().append(response);
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {          
            alert(errorThrown);
          }
        });
    }
}
<?php //code for auto suggest :: start ?>
//New auto complete code :: Start
$("#mobile_number").autocomplete({
    source: function (request, response)
    {
       var friendsArray = [];
       var mobile_number =  $("#mobile_number").val();
       //alert(mobile_number);
        $.ajax({
            type: "POST",
            url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkExist",
            data:'mobile_number='+mobile_number,
            beforeSend: function(){
                $("#mobile_number").css("background","#FFF no-repeat 165px");
            },
            success: function(data)
            {
                if(data=='')
                {
                    friendsArray = [];
                    $("#first_name").val('');
                    $("#last_name").val('');
                    $("#email").val('');
                    $("#user_id").val('');
                    $("#first_name").prop( "disabled", false);
                    $("#last_name").prop( "disabled", false);
                    $("#email").prop( "disabled", false); 
                    response(friendsArray);
                    return; 
                }
                else
                {
                    var items = JSON.parse(data);
                     response($.map(items, function (value, key) {
                        return {
                            label: value,
                            value: key
                        };
                    }));
                }                                        
            }
        });
    },
    select: function (e, ui){
       /*var mobile_number =  $("#mobile_number").val();
        filldetail(mobile_number);*/
    },
    change: function (e, ui)
    {
        /*var mobile_number =  $("#mobile_number").val();
        filldetail(mobile_number);*/
    },
    close: function( event, ui ) {
        var mobile_number =  $("#mobile_number").val();
        autofilldetail(mobile_number);        
    }
});
$('#mobile_number').on('change',function(e){
    if(e.keyCode == 8){
        var mobile_number =  $("#mobile_number").val();
        filldetail(mobile_number)
    }
}); 
/*$("#mobile_number").focusout(function ()
{
    if($("#first_name").val()=='' && $("#email").val()=='' && $("#mobile_number").val()!='')
    {
        $("#first_name").prop( "disabled", false);
        $("#last_name").prop( "disabled", false);
        $("#email").prop( "disabled", false);
        bootbox.alert({
            message: "<?php echo $this->lang->line('new_useralert'); ?>",
            buttons: {
                ok: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                }
            }
        });
    }
});*/
function autofilldetail(mobile_number)
{
    $.ajax({
        type: "POST",
        url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkExist",
        data:{"mobile_number":mobile_number,'alldata':'yes'},        
        success: function(data)
        {
            if(data!='')
            {
                var obj = JSON.parse(data);                
                $("#first_name").val(obj.first_name);
                $("#last_name").val(obj.last_name);
                $("#email").val(obj.email);
                $("#user_id").val(obj.entity_id);
                $("#first_name").prop( "disabled", true);
                $("#last_name").prop( "disabled", true);
                $("#email").prop( "disabled", true);                
            }
            else
            {
                $("#first_name").val('');
                $("#last_name").val('');
                $("#email").val('');
                $("#user_id").val('');
                $("#first_name").prop( "disabled", false);
                $("#last_name").prop( "disabled", false);
                $("#email").prop( "disabled", false);       
            }            
        }
    });
}
function filldetail(mobile_number)
{
    $.ajax({
        type: "POST",
        url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkExist",
        data:{"mobile_number":mobile_number,'alldata':'yes'},        
        success: function(data)
        {
            if(data!='')
            {
                var obj = JSON.parse(data);                
                $("#first_name").val(obj.first_name);
                $("#last_name").val(obj.last_name);
                $("#email").val(obj.email);
                $("#user_id").val(obj.entity_id);
                $("#first_name").prop( "disabled", true);
                $("#last_name").prop( "disabled", true);
                $("#email").prop( "disabled", true);                
            }
            else
            {
                $("#first_name").val('');
                $("#last_name").val('');
                $("#email").val('');
                $("#user_id").val('');
                $("#first_name").prop( "disabled", false);
                $("#last_name").prop( "disabled", false); 
                $("#email").prop( "disabled", false);    
            }
            if($("#first_name").val()=='' && $("#email").val()=='' && $("#mobile_number").val()!='')
            {
                $("#first_name").prop( "disabled", false);
                $("#last_name").prop( "disabled", false);
                $("#email").prop( "disabled", false);
                bootbox.alert({
                    message: "<?php echo $this->lang->line('new_useralert'); ?>",
                    buttons: {
                        ok: {
                            label: "<?php echo $this->lang->line('ok'); ?>",
                        }
                    }
                });
            }              
        }
    });
}
//New auto complete code :: End
<?php //code for auto suggest :: end ?>
/*add address start*/
jQuery(document).ready(function() {
    initAutocomplete('address_field');
});
// autocomplete function
var autocomplete;
function initAutocomplete(id) {
    autocomplete = new google.maps.places.Autocomplete(
    document.getElementById(id), {
        fields: ["formatted_address", "geometry", "name"],
        types: ['address'] //'geocode','address','establishment','regions','cities'
    });
    autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', getAddressDetails);
}
function getAddressDetails(){
    var place = autocomplete.getPlace();   
    window.lat = place.geometry.location.lat();
    window.long = place.geometry.location.lng();
}
function geolocate(){
    initAutocomplete('address_field');
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
    var places = new google.maps.places.Autocomplete(document
            .getElementById('address_field'));
    google.maps.event.addListener(places, 'place_changed', function() {
        var place = places.getPlace();
        var lat = place.geometry.location.lat();
        var long = place.geometry.location.lng();
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = long;
        
        var  value = place.formatted_address.split(",");
        if(place.name == value[0]){
            document.getElementById("address_field").value = place.formatted_address;    
        }else{
            document.getElementById("address_field").value = place.name+', '+place.formatted_address;
        }
        document.getElementById("city").value = '';
        $.each(place.address_components, function( index, value ) {
            $.each(value.types, function( index, types ) {
                /*if(types == 'administrative_area_level_2'){
                   document.getElementById("city").value = value.long_name;
                }*/
                if(types == 'locality'){
                   document.getElementById("city").value = value.long_name;
                }
            });
        });
    });
});
/*add address end*/
/*add new user start*/
$("#add_new_user").click(function(){
    $(".add_new_user_content").css("display", "block");
    // $('select.select_user_id')[0].sumo.selectItem(0);
    $('select#restaurant_id')[0].sumo.selectItem(0);
    $('.qty').val('');
    $('.base_price').val('');
    $('.rate').val('');
    $('#address_id').val('');
    $('#mobile_number').val('');
    $('#first_name').val('');
    $('#last_name').val('');
    $('#email').val('');
    $('#coupon_id').val('');
    $('#coupon_amount').val('');
    $('#subtotal').val('');
    $('#tax_rate').val('');
    $('#delivery_charge').val('');
    $('#total_rate').val('');
    $('#order_status').val('');
    $('#scheduled_date').val('');
    $('#order_date').val('');
});
$("#cancel_adduser").click(function(){
    $(".add_new_user_content").css("display", "none");
    $("#form_add_user_fororder").validate().resetForm();
    $('#first_name_add').val('');
    $("#last_name_add").val('');
    $('#phone_number').val('');
    $('#email_add').val('');
    $('#address_field').val('');
    $('#city').val('');
});
function checkExistPhnNo(phone_number){
    var phone_code = $('#phone_code').val();
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASEURL+"<?php echo ADMIN_URL ?>/order/checkExistPhnNo",
        data: {'mobile_number':phone_number,'phone_code':phone_code},
        cache: false,
        success: function(response) {
          if(response.numrows > 0){
            if(response.user_id != ''){
                if(response.phone_number != ''){
                    $('#mobile_number').val(response.phone_number);
                    filldetail(response.phone_number);
                }
                //$(".add_new_user_content").css("display", "none");
                $('#first_name_add').val('');
                $('#last_name_add').val('');
                $('#phone_number').val('');
                $('#email_add').val('');
                $('#address_field').val('');
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
                if(response.phone_number != ''){
                    $('#mobile_number').val(response.phone_number);
                    filldetail(response.phone_number)
                }
               //$(".add_new_user_content").css("display", "none"); 
               $('#first_name_add').val('');
                $('#last_name_add').val('');
                $('#phone_number').val('');
                $('#email_add').val('');
                $('#address_field').val('');
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
    if($('#first_name_add').val() != '' && $("#last_name_add").val() !='' && $('#phone_number').val() != '' && $('#email_add').val() != '' && $('#address_field').val() != '' && $('#city').val() != '') {
        //call generate password here
        var password = generatePassword();
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASEURL+"<?php echo ADMIN_URL ?>/order/add_new_user",
            data: {'submit_adduser':$('#submit_adduser').val(), 'first_name':$('#first_name_add').val(), 'last_name':$('#last_name_add').val(), 'email':$('#email_add').val() ,'mobile_number':$('#phone_number').val(), 'address_field':$('#address_field').val(), 'city': $('#city').val(), 'password':password, 'latitude':$('#latitude').val(), 'longitude':$('#longitude').val(), 'phone_code':$('#phone_code').val() },
            cache: false,
            success: function(response) {
                if(response.status == 1){
                    var selectvalue = response.user_id;
                    var selecttext = response.first_name+' '+response.last_name+' (+'+response.phone_code+response.phone_number+')';
                    $('#mobile_number').val(response.mobile_number);
                    filldetail(response.mobile_number)
                    $(".add_new_user_content").css("display", "none");
                    $("#first_name_add").val("");
                    $("#last_name_add").val("");
                    $("#phone_number").val("");
                    $("#email_add").val("");
                    $("#address_field").val("");
                    $("#city").val("");
                    $('#latitude').val("");
                    $('#longitude').val("");
                    // $('select.select_user_id')[0].sumo.selectItem(response.user_id);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {                 
              
            }
        });
    } 
});
/*add new user end*/
</script>
<script type="text/javascript">
<?php $iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>

var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
// Initialize the intl-tel-input plugin :: start
const phoneInputField = document.querySelector("#phone_number");
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
//intl-tel-input plugin :: end
$(document).ready(function(){
    $('#form_adddine_order').submit(function() {
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
        if(jQuery('#form_adddine_order').valid()) {
            document.getElementById("form_adddine_order").submit();
        }else{
            return false;
        }
    });
});
</script>
<?php $this->load->view(ADMIN_URL.'/addons_max_selection');?>
<?php $this->load->view(ADMIN_URL.'/footer');?>