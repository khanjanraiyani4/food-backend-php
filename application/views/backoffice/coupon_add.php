<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<style type="text/css">
    .home-checkbox .custom--tooltip .tooltiptext2{
        top: 30px !important;
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
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','restaurant_id','name','description','amount_type','amount','start_date','end_date','max_amount','coupon_type','image','show_in_home' ,'use_with_other_coupons','maximaum_use_per_users','maximaum_use','coupon_for_newuser');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('title_admin_couponedit');        
    $user_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $restaurant_map = array_column($restaurant_map, 'restaurant_id');
    $item_map = ($coupon_type == 'discount_on_combo')?array_column($item_map,'package_id'):array_column($item_map,'item_id');
    $itemDetail = $this->coupon_model->getItemedit($restaurant_map,$coupon_type);
    $edit_recordsval = 'yes';
    $stored_categories = array_column($coupon_category_map, 'category_content_id');
}
else
{
    $add_label    = $this->lang->line('title_admin_couponadd');       
    $user_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $restaurant_map = array();
    $item_map = array();
    $itemDetail = array();
    $edit_recordsval = 'no';
}
$selected_restaurantids = '';
if(!(isset($edit_records) && $edit_records !="") && isset($res_entityarr) && !empty($res_entityarr)) {
  $selected_restaurantids = implode(",", $res_entityarr);
} ?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('admin_coupons'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('admin_coupons'); ?></a>
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
                            <form action="<?php echo $user_action;?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="display-no frame-load" style= "display: none;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"   />
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="coupon_type" class="form-control <?php echo ($entity_id)?'coupon_id_wrap':'' ?>" id="coupon_type" onchange="getCouponType(this.value)">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>  
                                                 <?php $coupon_types = coupon_type();
                                                    if(!empty($coupon_types)){
                                                    foreach ($coupon_types as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?php echo ($key == $coupon_type)?'selected':'' ?>><?php echo $value ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="coupon_area enable_coupon">
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon'); ?><span class="required">*</span></label>
                                          <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                          <input type="hidden" name="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                          <div class="col-md-8">
                                               <input type="text" maxlength="15" onblur="checkExist(this.value)" class="form-control upper-text"  name="name" id="name" value="<?php echo $name ?>"/>
                                                <div id="phoneExist"></div>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                            <?php if(isset($edit_records) && $edit_records !=""){?>
                                              <select name="restaurant" multiple="" class="form-control sumo restaurant_id" id="restaurant_id">
                                                   <?php if(!empty($restaurant)){
                                                      foreach ($restaurant as $key => $value) {
                                                        if(in_array($value['content_id'], $restaurant_map)){
                                                       ?>
                                                          <option value="<?php echo $value['entity_id'] ?>" <?php echo in_array($value['content_id'], $restaurant_map)?'selected':'' ?> disabled><?php echo $value['name'] ?><?php echo (in_array($value['content_id'], $restaurant_map) && $key<count($restaurant_map))?', ':'' ?></option>    
                                                  <?php } } } ?>
                                              </select>
                                            <?php }else{ ?>
                                              <select name="restaurant_id[]" multiple="multiple" class="form-control restaurant_id restaurant_idcls" id="restaurant_id">
                                                   <?php if(!empty($restaurant)){
                                                      foreach ($restaurant as $key => $value) { ?>
                                                          <option value="<?php echo $value['entity_id'] ?>" <?php echo in_array($value['entity_id'], $restaurant_map)?'selected':'' ?>><?php echo $value['name'] ?></option>    
                                                  <?php } } ?>
                                              </select>
                                              <input type="hidden" name="selected_restaurantids" id="selected_restaurantids" value="<?php echo $selected_restaurantids; ?>">
                                            <?php } ?>
                                          </div>
                                      </div>
                                        <div class="form-group category-field-row" style="<?php echo ($coupon_type == 'discount_on_categories') ? '' : 'display:none' ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('menu_category'); ?><span class="required">*</span></label>
                                            <div class="col-md-8" id="categoryloadid">
                                                <?php
                                                if(!empty($categories)){ 
                                                    $i = 1;
                                                    foreach ($categories as $value) { 
                                                        if(!empty($entity_id) && !empty($coupon_category_map)){
                                                            $edit_coupon_category = $this->common_model->getSingleRowMultipleWhere('coupon_category_map',array('coupon_id'=>$entity_id,'category_content_id'=>$value->content_id));
                                                        }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="checkbox" class="category_checkbox"  name="category_content_id[]" id="category_content_id<?php echo $value->content_id ?>" value="<?php echo $value->content_id ?>" onchange="addDetails('<?php echo $i ?>','<?php echo $value->content_id ?>',this.id)" <?php echo (in_array($value->content_id, $stored_categories))?'checked':'' ?>> <?php echo $value->name ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="coupon_category<?php echo $i; ?>" class="coupon_category<?php echo $value->content_id ?> <?php echo (in_array($value->content_id, $stored_categories))?'display-yes':'display-no' ?>">
                                                            <input type="hidden" name="coupon_category_detail[<?php echo $i; ?>][category_content_id]" value="<?php echo $value->content_id; ?>" class="hidden-cat-val" id="coupon_category_detail<?php echo $value->content_id; ?>">
                                                            <div class="coupon_category_detail<?php echo $value->content_id ?>" >
                                                                <div class="form-group">
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('discount_type') ?><span class="required">*</span></label>
                                                                        <select name="coupon_category_detail[<?php echo $i; ?>][discount_type]" class="form-control field-required coupon_category_detailsel<?php echo $value->content_id; ?>" id="discount_type<?php echo $i ?>">
                                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                                            <option value="Amount" <?php echo (!empty($edit_coupon_category->discount_type) && $edit_coupon_category->discount_type == 'Amount') ? 'selected' : ''?>><?php echo $this->lang->line('amount'); ?></option>
                                                                            <option value="Percentage" <?php echo (!empty($edit_coupon_category->discount_type) && $edit_coupon_category->discount_type == 'Percentage') ? 'selected' : ''?>><?php echo $this->lang->line('percentage'); ?></option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('discount_value') ?><span class="required">*</span></label>
                                                                        <input type="text" name="coupon_category_detail[<?php echo $i; ?>][discount_value]" id="discount_value<?php echo $i ?>" value="<?php echo (!empty($edit_coupon_category->discount_value)) ? $edit_coupon_category->discount_value : ''?>" class="form-control field-required coupon_category_detaildis<?php echo $value->content_id; ?>" maxlength="249">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('min_order_amount') ?><span class="required">*</span></label>
                                                                        <input type="text" name="coupon_category_detail[<?php echo $i; ?>][min_amount]" id="min_amount<?php echo $i ?>" value="<?php echo (!empty($edit_coupon_category->minimum_amount)) ? $edit_coupon_category->minimum_amount : ''?>" class="form-control field-required coupon_category_detailamt<?php echo $value->content_id; ?>" maxlength="249">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $i++;} } ?>
                                                <div id="checkbox_error"></div>
                                            </div>
                                        </div>
                                      <div class="form-group hidden-row" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'user_registration' || $coupon_type == 'discount_on_cart'||$coupon_type == 'discount_on_categories' ||$coupon_type == 'dine_in')?'display:none':'' ?>">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('menu_item'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                              <select name="item_id[]" multiple="" class="form-control sumo restaurant_itemcls" id="item_id">
                                                  <?php if(!empty($itemDetail)){
                                                      foreach ($itemDetail as $key => $value) { ?>
                                                          <optgroup label="<?php echo $value[0]->restaurant_name ?>">
                                                          <?php foreach ($value as $k => $val) { ?>
                                                              <option value="<?php echo $val->entity_id ?>" <?php echo in_array($val->content_id,$item_map)?'selected':'' ?>><?php echo $val->name ?></option>    
                                                          <?php } ?>
                                                          </optgroup>
                                                  <?php } } ?>
                                              </select>
                                              <span id="menu_error" style="color: red;font-weight: 700;display: none;">
                                                <?php echo $this->lang->line('coupon_restaurant_error'); ?>
                                              </span>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('description'); ?><span class="required">*</span></label>
                                          <div class="col-md-8">
                                             <textarea name="description" id="description" class="form-control ckeditor"><?php echo $description ?></textarea>
                                          </div>
                                      </div>                      
                                      <div class="form-group" id="imagedivid" style="<?php echo ($coupon_type == 'dine_in' || $coupon_type == 'discount_on_items' || $coupon_type == 'discount_on_categories')?'display:none':'' ?>">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('image'); ?><span class="img_required"></span></label>
                                          <div class="col-md-4">
                                              <div class="custom-file-upload">
                                                  <label for="image" class="custom-file-upload">
                                                      <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                  </label>
                                                  <input type="file" name="image" id="image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>&ensp;
                                                  <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                      <ul>
                                                        <li><?php echo $this->lang->line('img_allow') ?></li>
                                                        <li><?php echo $this->lang->line('max_file_size') ?></li>
                                                        <li><?php echo $this->lang->line('recommended_size').'400px * 240px.'; ?></li>
                                                      </ul>
                                                    </span>
                                                  </div>
                                              </div>
                                              <div class="coupon-img-error"></div>
                                              <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                              <img id="preview" height='100' width='150' class="display-no"/>
                                          </div>
                                      </div>
                                      <div class="form-group" id="old" style="<?php echo ($coupon_type == 'dine_in' || $coupon_type == 'discount_on_items' || $coupon_type == 'discount_on_categories')?'display:none':'' ?>">
                                          <label class="control-label col-md-3"></label>
                                          <div class="col-md-4">
                                            <input type="hidden" name="image_exist" id="image_exist" value="<?php echo (isset($image) && $image != '') ? 1 : 0?>">
                                              <?php if(isset($image) && $image != '' && file_exists(FCPATH.'uploads/'.$image)) {?>
                                                      <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                              <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                              <?php }  ?>
                                          </div>
                                      </div>
                                      <?php if ($coupon_type != 'free_delivery') { ?>
                                        <div class="form-group show-hidden-row" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'discount_on_categories')?'display:none':'' ?>">  
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('discount_type'); ?><span class="required">*</span></label>                        
                                            <div class="col-sm-4">
                                                    <p>
                                                      <input type="radio" name="amount_type" id="MPercentage"
                                                      <?php if (isset($amount_type) && $amount_type=="Percentage") echo "checked";?>
                                                      value="Percentage" checked="checked">&nbsp;&nbsp;<?php echo $this->lang->line('percentage'); ?>
                                                    </p>
                                                    <p>
                                                      <input type="radio" name="amount_type" id="MAmount"
                                                      <?php if (isset($amount_type) && $amount_type=="Amount") echo "checked";?>
                                                      value="Amount">&nbsp;&nbsp;<?php echo $this->lang->line('amount'); ?>
                                                    </p>
                                            </div>
                                        </div>
                                        <div class="form-group show-hidden-row" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'discount_on_categories')?'display:none':'' ?>"> 
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('amount'); ?><span class="required">*</span></label>
                                            <div class="col-sm-3 form-markup">
                                                  <label id="Percentage"><?php echo $this->lang->line('percentage'); ?> (%)</label>
                                                  <label id="Amount" style="display:none"><?php echo $this->lang->line('amount'); ?> ($) </label>
                                                  <br>
                                                  <input type="text" name="amount" id="amount" value="<?php echo ($amount)?$amount:'' ?>" maxlength="19" data-required="1" class="form-control"/>  
                                            </div>  
                                        </div> 
                                      <?php } ?>
                                      <div class="form-group max-hidden-row" style="<?php echo ($coupon_type == 'discount_on_categories') ? 'display:none' : '' ?>"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('min_amount'); ?><span class="required">*</span></label>
                                          <div class="col-sm-3 form-markup">
                                                <input type="text" name="max_amount" id="max_amount" value="<?php echo ($max_amount)?$max_amount:'' ?>" maxlength="19" data-required="1" class="form-control"/>  
                                          </div>  
                                      </div> 
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('start_date_time'); ?><span class="required">*</span></label>
                                          <div class="col-md-3">
                                                <input size="16" type="text" name="start_date" class="form-control date-picker" id="start_date" value="<?php echo ($start_date)?$this->common_model->datetimeFormat($start_date):"" ?>" readonly="" placeholder="<?php echo $this->lang->line('select_datetime'); ?>">
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('end_date_time'); ?><span class="required">*</span></label>
                                          <div class="col-md-3">
                                              <input size="16" type="text" name="end_date" class="form-control" id="end_date" value="<?php echo ($end_date)?$this->common_model->datetimeFormat($end_date):"" ?>" readonly="" placeholder="<?php echo $this->lang->line('select_datetime'); ?>">
                                          </div>
                                      </div> 
                                      <!-- show in home : start -->
                                      <div id="showhomeid" class="form-group home-checkbox" style="<?php echo ($coupon_type == 'dine_in' || $coupon_type == 'discount_on_items' || $coupon_type == 'discount_on_categories')?'display:none':'' ?>"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('show_in_home') ?></label>
                                          <div class="col-md-4">
                                              <input type="checkbox" name="show_in_home" id="show_in_home" value="1"  <?php echo (isset($show_in_home) && $show_in_home == 1)?'checked':'' ?> />&ensp;
                                               <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltiptext2 tooltip-right">
                                                        <?php echo $this->lang->line('show_in_home_msg') ?>
                                                    </span>
                                                  </div>
                                          </div>
                                      </div>
                                      <!-- show in home : end -->
                                      <?php //New code added as per requirement :: coupon use :: Start ?>
                                      <div class="form-group home-checkbox" id="coupon_for_newuserid" style="display: <?php echo (isset($coupon_type) && $coupon_type == 'free_delivery')?'':'none' ?>;"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_for_newuser') ?></label>
                                          <div class="col-md-4">
                                              <input type="checkbox" name="coupon_for_newuser" id="coupon_for_newuser" value="1"  <?php echo (isset($coupon_for_newuser) && $coupon_for_newuser == 1)?'checked':'' ?> />&ensp;
                                               <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltiptext2 tooltip-right">
                                                        <?php echo $this->lang->line('coupon_for_newuser_msg') ?>
                                                    </span>
                                                  </div>
                                          </div>
                                      </div>
                                      <div class="form-group home-checkbox" id="use_with_other_couponsid" style="display: <?php echo (isset($coupon_type) && ($coupon_type == 'discount_on_cart' || $coupon_type == 'user_registration' || $coupon_type == 'free_delivery' || $coupon_type == 'dine_in'))?'':'none' ?>;"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('use_with_other_coupons') ?></label>
                                          <div class="col-md-4">
                                              <input type="checkbox" name="use_with_other_coupons" id="use_with_other_coupons" value="1"  <?php echo (isset($use_with_other_coupons) && $use_with_other_coupons == 1)?'checked':'' ?> />&ensp;
                                               <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltiptext2 tooltip-right">
                                                        <?php echo $this->lang->line('use_with_other_coupons_msg') ?>
                                                    </span>
                                                  </div>
                                          </div>
                                      </div>
                                      <div class="form-group" id="maximaum_use_per_usersid" style="display: <?php echo (isset($coupon_type) && ($coupon_type == 'discount_on_cart' || $coupon_type == 'user_registration' || $coupon_type == 'free_delivery' || $coupon_type == 'dine_in'))?'':'none' ?>;"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('maximaum_use_per_users'); ?><span class="required">*</span></label>
                                          <div class="col-sm-3 form-markup">
                                                <input type="text" name="maximaum_use_per_users" id="maximaum_use_per_users" value="<?php echo ($maximaum_use_per_users!='')?$maximaum_use_per_users:'' ?>" maxlength="3" data-required="1" class="form-control"/>
                                                <i>(<?php echo $this->lang->line('enter_zero_use'); ?>)</i>
                                          </div>  
                                      </div>
                                      <div class="form-group" id="maximaum_useid" style="display: <?php echo (isset($coupon_type) && ($coupon_type == 'discount_on_cart' || $coupon_type == 'user_registration' || $coupon_type == 'free_delivery' || $coupon_type == 'dine_in'))?'':'none' ?>;"> 
                                          <label class="control-label col-md-3"><?php echo $this->lang->line('maximaum_use'); ?><span class="required">*</span></label>
                                          <div class="col-sm-3 form-markup">
                                                <input type="text" name="maximaum_use" id="maximaum_use" value="<?php echo ($maximaum_use!='')?$maximaum_use:'' ?>" maxlength="3" data-required="1" class="form-control"/>
                                                <i>(<?php echo $this->lang->line('enter_zero_use'); ?>)</i>
                                          </div>  
                                      </div>
                                      <?php //New code added as per requirement :: coupon use :: End ?>
                                    </div>
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL?>/coupon/view"><?php echo $this->lang->line('cancel'); ?></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
 <!-- <script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>  -->
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<script type="text/javascript">
    CKEDITOR.replace('description', {
      language: 'ar'
    });
</script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<script type="text/javascript">
    CKEDITOR.replace('description', {
      language: 'fr'
    });
</script>
<?php } ?>
<script>
var edit_recordsval = '<?php echo $edit_recordsval; ?>';
jQuery(document).ready(function() {       
  Layout.init(); // init current layout
  //Added on 19-10-2020
  $('.sumo').SumoSelect({placeholder:"<?php echo $this->lang->line('search'); ?>"+' '+"<?php echo $this->lang->line('here'); ?>",search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
  
  $('.restaurant_id').SumoSelect({placeholder:"<?php echo $this->lang->line('search'); ?>"+' '+"<?php echo $this->lang->line('here'); ?>",search: true, selectAll:true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});


  $("html").on('click', '.sumo_restaurant_id .select-all', function(){    
    $('#quotes-main-loader').show();
    $('.sumo_restaurant_id .select-all').removeClass('partial');
    var myObj = $('.sumo_restaurant_id').closest('.SumoSelect.open').children()[0];
    if ($('.sumo_restaurant_id .select-all').hasClass("selected")) {
      $('.sumo_restaurant_id .select-all').parents(".SumoSelect").find("select>option").prop("selected", true);
      $(myObj)[0].sumo.selectAll();
      $('.sumo_restaurant_id .select-all').parent().find("ul.options>li").addClass("selected");
      $('#quotes-main-loader').hide();      
    } else {
      $('.sumo_restaurant_id .select-all').parents(".SumoSelect").find("select>option").prop("selected", false);
      $(myObj)[0].sumo.unSelectAll();
      $('select.restaurant_id')[0].sumo.unSelectAll();
      $('.sumo_restaurant_id .select-all').parent().find("ul.options>li").removeClass("selected");
      $('#quotes-main-loader').hide();      
    }
  });

    /*if(edit_recordsval=='yes')
    {
        $('#restaurant_id')[0].sumo.disable();
        $('#restaurant_id').hide();
    }*/
});

$('select.restaurant_id').on('sumo:closed', function(sumo) {
    var restaurantids_str = '';
    $('.restaurant_id option:selected').each(function(i) {
        var selected_val = $(this).val();
        if(i==0){
            restaurantids_str += selected_val;
        } else {
            restaurantids_str += ','+selected_val;
        }
    });
    $('#selected_restaurantids').val(restaurantids_str);
});

//check coupon exist
function checkExist(coupon){
    var entity_id = $('#entity_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"backoffice/coupon/checkExist",
    data: 'coupon=' + coupon +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#phoneExist').show();
        $('#phoneExist').html("<?php echo $this->lang->line('coupon_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#phoneExist').html("");
        $('#phoneExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#phoneExist').show();
      $('#phoneExist').html(errorThrown);
    }
  });
}
// for datepicker
/*$(function() {
    $('#start_date').datetimepicker({
        format: 'mm/dd/yyyy hh:ii',
        autoclose: true,
    });
     $('#end_date').datetimepicker({
        format: 'mm/dd/yyyy hh:ii',
        autoclose: true,
    });
});*/
var datetimepicker_format = "<?php echo date_timepicker_format; ?>";
$(function() {
    $('#start_date').datetimepicker({
        format: datetimepicker_format,
        showMeridian: true,
        autoclose: true,
    }).on('hide', function(ev){
      var minDate_forendDate = $( "#start_date" ).val();
      $('#end_date').datetimepicker('setStartDate', minDate_forendDate);
    });
    $('#end_date').datetimepicker({
        format: datetimepicker_format,
        showMeridian: true,
        autoclose: true,
    }).on('hide', function(ev){
      var maxDate_forstartDate = $( "#end_date" ).val();
      $('#start_date').datetimepicker('setEndDate', maxDate_forstartDate);
    });
});
$("#amount,#max_amount").each(function(){
    $(this).keyup(function(){
        //this.value = this.value.replace(/[^0-9\.]/g,'');
    });
});
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
$(document).ready(function(){  
   markup();
   if($("input[name=amount_type]:checked").val() == "Percentage" ){        
      $("#max_amount").attr('greater','');    
    }else if($("input[name=amount_type]:checked").val() == "Amount" ){
      
      $("#max_amount").attr('greater','#amount');              
    }
});
$("input[name=amount_type]:radio").click(function(){
  markup();
  if($("input[name=amount_type]:checked").val() == "Percentage" ){    
    $("#amount").val('');      
    $("#max_amount").attr('greater','');    
  }else if($("input[name=amount_type]:checked").val() == "Amount" ){
    $("#amount").val(''); 
    $("#max_amount").attr('greater','#amount');              
  }
});
//get coupon type
function getCouponType(value)
{
  $('#submit_page').prop("disabled",false);
  $("#menu_error").css("display", "none");
  $('.coupon_area').removeClass('enable_coupon');
  $('#categoryloadid').html('');

  //New code for fetch the restaurant base on coupon type :: Start
  var coupon_type = $('#coupon_type').val();
  jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getRestaurantData',
      data : {'coupon_type':coupon_type},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {        
        $('#quotes-main-loader').hide();        
        $('.restaurant_idcls').empty().append(response);
        $('.restaurant_idcls')[0].sumo.reload();
        $('.restaurant_itemcls').empty();
        $('.restaurant_itemcls')[0].sumo.reload();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        //alert(errorThrown);
        $('#quotes-main-loader').hide();
      }
  });
  //New code for fetch the restaurant base on coupon type :: End

  if(value == 'free_delivery'){
    $('.hidden-row').hide();
    $('.show-hidden-row').hide();
    $('.category-field-row').hide();
    $('#restaurant_id').val('');
    $('#restaurant_id')[0].sumo.reload();
    $('#restaurant_id').SumoSelect({selectAll:true});
  }else if(value == 'discount_on_cart' || value == 'user_registration' || value == 'dine_in'){
    $('.hidden-row').hide();
    $('.show-hidden-row').show();
    $('.category-field-row').hide();
    $('#restaurant_id').val('');
    $('#restaurant_id')[0].sumo.reload();
    $('#restaurant_id').SumoSelect({selectAll:true});
  }else if(value == 'discount_on_categories'){
    $('.hidden-row').hide();
    $('.show-hidden-row').hide();
    $('.max-hidden-row').hide();
    $('.category-field-row').show();
    $('#max_amount').hide();
    $('#restaurant_id').val('');
    $('#restaurant_id')[0].sumo.reload();
    $('#restaurant_id').SumoSelect({selectAll:true});

    //New code set to load category ajax base :: Start
    var coupon_type = $('#coupon_type').val();
    jQuery.ajax({
        type : "POST",
        dataType :"html",
        url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getCategory',
        data : {'coupon_type':coupon_type,'restaurant_ids':[]},
        beforeSend: function(){
            $('#iframeloading').show();
        },
        success: function(response) {
          $('#categoryloadid').html(response);
          $('#iframeloading').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          //alert(errorThrown);
          $('#iframeloading').hide();
        }
    });
    //New code set to load category ajax base :: End

  }else{
    $('.hidden-row').show();
    $('.show-hidden-row').show();
    $('.category-field-row').hide();
    $('#amount').attr('required',true);
    $('#restaurant_id').val('');
    $('#restaurant_id')[0].sumo.reload();
    $('#restaurant_id').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('enter'); ?>"+' '+"<?php echo $this->lang->line('here'); ?>"});
  }
}
//get items of restaurant
$( document ).ready(function() {
  $('#restaurant_id').on('sumo:closed', function (event) {
    var coupon_type = $('#coupon_type').val();
    /*if(coupon_type == "dine_in" || coupon_type == "discount_on_items"){*/      
      if(coupon_type == "discount_on_items"){
        var items = [];
        if($(this).val() != null){
          items.push($(this).val());
        }
        jQuery.ajax({
            type : "POST",
            dataType :"html",
            url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getItem',
            data : {'entity_id':items,'coupon_type':coupon_type},
            success: function(response) {
              $('#item_id').empty().append(response);
              $('#item_id')[0].sumo.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
              alert(errorThrown);
            }
        });
    }

    //New code set to load category ajax base :: Start    
    var restaurant_ids = $('#restaurant_id').val();
    if(coupon_type == "discount_on_categories"){
      jQuery.ajax({
          type : "POST",
          dataType :"html",
          url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getCategory',
          data : {'coupon_type':coupon_type,'restaurant_ids':restaurant_ids},
          beforeSend: function(){
              $('#iframeloading').show();
          },
          success: function(response) {
            $('#categoryloadid').html(response);
            $('#iframeloading').hide();
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {           
            //alert(errorThrown);
            $('#iframeloading').hide();
          }
      });
      //New code set to load category ajax base :: End
    }

  });
});
function readURL(input) {
    $('#submit_page').prop("disabled",false);
    var fileInput = document.getElementById('image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    if(input.files[0].size <= 512000){ // 500 KB
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
            $('#image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $('#submit_page').prop("disabled",true);
        $('#image').val('');
        $("#old").show();
    }
}
$('#image').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#image')[0].files[0].name;
  $(this).prev('label').text(file);
});
$('#item_id').on('sumo:closed', function (event) {
    $("#menu_error").css("display", "none");
    var restaurant_ids = $('#restaurant_id').val();
    var entity_id = $('#entity_id').val();
    var items = [];
    if($(this).val() != null){
      items.push($(this).val());
    }
    if(restaurant_ids != null){
        jQuery.ajax({
            type : "POST",
            dataType :"html",
            url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/validate_menu_items',
            data : {'item_ids':items[0],'restaurant_ids':restaurant_ids,'entity_id':entity_id,'edit_recordsval':edit_recordsval},
            success: function(response) {
                if(response == 0){
                    $("#menu_error").css("display", "block");
                    $('#submit_page').prop('disabled', true);
                }else{
                    $("#menu_error").css("display", "none");
                    $('#submit_page').prop('disabled', false);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
              alert(errorThrown);
            }
        });
    }
});

function addDetails(key,entity_id,id){
    if($('#'+id).is(':checked')){
        $('#coupon_category'+key).show();
        $('#discount_type'+key).rules( "add", {
            required: true
        });
        $('#discount_value'+key).rules( "add", {
            required: true,
            number: true,
            max: {
                param:99,
                depends: function(){
                    if($('#discount_type'+key).val() == "Percentage" ){
                        return true;
                    }
                }
            },
            min: function(element){
                if($('#discount_type'+key).val() == "Percentage"){
                    return 1;
                }else{
                    return 0;
                }
            }
        });
        $('#min_amount'+key).rules( "add", {
            required: true,
            number: true,
            min: function(element){
                if($('#discount_type'+key).val() == "Amount"){
                    return parseFloat($('#discount_value'+key).val())+1;
                }
            }
        });
    }else{
        $('#coupon_category'+key).hide();
        $('.coupon_category'+entity_id).find('.field-required').val('');
        $('.coupon_category'+entity_id).find('.field-required').trigger('click');
        $('#discount_type'+key).rules("remove");
        $('#discount_value'+key).rules("remove");
        $('#min_amount'+key).rules("remove");
    }
}
$('#show_in_home').change(function() {
  if(this.checked) {
    $('.img_required').addClass('required');
    $('.img_required').html('*');
  } else {
    $('.img_required').removeClass('required');
    $('.img_required').html('');
  }
});
//Code for pass only selected checkbox value :: Start
$("#submit_page" ).click(function() {
  if($("#form_add_cpn").valid())
  {
    $('#iframeloading').show();
    $("#submit_page").attr('readonly', true);
  }
});
$("#form_add_cpn" ).submit(function(event){
    if($("#form_add_cpn").valid())
    {
      $('#iframeloading').show();
      $("#submit_page").attr('readonly', true);
    } 
    $('.category_checkbox:checkbox').each(function (){
       var chkbocval = $(this).val();        
       if(this.checked) {           
           $(this).attr("disabled", false);
       }
       else
       {           
           /*$('#category_content_id'+chkbocval).attr("disabled", true);
           $('#coupon_category_detail'+chkbocval).attr("disabled", true);
           $('.coupon_category_detailsel'+chkbocval).attr("disabled", true);
           $('.coupon_category_detaildis'+chkbocval).attr("disabled", true);
           $('.coupon_category_detailamt'+chkbocval).attr("disabled", true);*/
       }       
    });     
});
//Code for pass only selected checkbox value :: End


//Coupon feature hide show with coupon type :: Start
$('#coupon_type').change(function (event){
  var coupon_type = $(this).val();
  if(coupon_type=='discount_on_cart' || coupon_type=='user_registration' || coupon_type=='free_delivery' || coupon_type=='dine_in') 
  {
    if(coupon_type=='free_delivery')
    {
      $('#coupon_for_newuserid').show();
      $('#maximaum_use_per_usersid').show();
      $('#maximaum_useid').show();
      $('#use_with_other_couponsid').show();
      /*$('#maximaum_use_per_usersid').hide();
      $('#maximaum_useid').hide();
      $('#use_with_other_couponsid').hide();
      $("#maximaum_use").val('');
      $("#maximaum_use_per_users").val('');
      $('#use_with_other_coupons').attr('checked', false);*/
    }
    else
    {
      $('#coupon_for_newuserid').hide();
      $('#maximaum_use_per_usersid').show();
      $('#maximaum_useid').show();
      $('#use_with_other_couponsid').show();
      $('#coupon_for_newuser').attr('checked', false);  
    }    
  }  
  else
  {
    $('#coupon_for_newuserid').hide();
    $('#maximaum_use_per_usersid').hide();
    $('#maximaum_useid').hide();
    $('#use_with_other_couponsid').hide();
    $("#maximaum_use").val('');
    $("#maximaum_use_per_users").val('');
    $('#use_with_other_coupons').attr('checked', false);
    $('#coupon_for_newuser').attr('checked', false);
  }

  if(coupon_type == 'dine_in' || coupon_type == 'discount_on_items' || coupon_type == 'discount_on_categories'){
    $('#old').hide();
    $('#imagedivid').hide();
    $('#showhomeid').hide();
    $("#image").val('');
    $('#show_in_home').attr('checked', false);
  }
  else
  {
    $('#old').show();
    $('#imagedivid').show();
    $('#showhomeid').show();
  }
  
});

$('#maximaum_use').change(function (event){
  $('#maximaum_use_per_users').trigger("blur");
});
//Coupon feature hide show with coupon type :: End
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>