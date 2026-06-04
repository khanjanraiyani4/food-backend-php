<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<style>
    .availability-checkbox{
        vertical-align: top;
        padding-left: 5px;
        padding-right: 20px;
        font-size: 14px;
    }
    .dis_lim{
        width: 31.8%;
    }
</style>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  //$FieldsArray = array('content_id','entity_id','name','ingredients','restaurant_id','category_id','price','menu_detail','popular_item','availability','image','food_type','recipe_time','check_add_ons','item_slug','sku','is_masterdata');
  $FieldsArray = array('content_id','entity_id','name','restaurant_id','category_id','price','menu_detail','popular_item','availability','image','food_type','recipe_time','check_add_ons','item_slug','sku','is_masterdata');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('menu');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_menu/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    $add_ons = array_keys($add_ons_detail);
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('menu');       
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add_menu/".$this->uri->segment('4');
    $addons_detail = array();
    $add_ons = array();
    $add_ons_detail = array();
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'));
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('menus') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view_menu"><?php echo $this->lang->line('menus') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->menu_prefix ?>" name="form_add<?php echo $this->menu_prefix ?>" method="post" class="form-horizontal horizontal-form-deal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div id="iframeloading"  class="frame-load display-no">
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="restaurant_id" class="form-control sumo required" id="restaurant_id" onchange="getCurrency(this.value);checkResMenuNameExist();">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                       <option food-data="<?php echo $value->food_type ?>" value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $restaurant_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('menu_category') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="category_id" class="form-control sumo" onchange="checkResMenuNameExist();" id="category_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                 <?php if(!empty($category)){
                                                    foreach ($category as $key => $value) { ?>
                                                       <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $category_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="hidden" id="item_slug" name="item_slug" value="<?php echo ($item_slug)?$item_slug:'';?>" />
                                            <input type="hidden" id="call_from" name="call_from" value="CI_callback" />
                                            <input type="text" name="name" id="name" oninput="checkResMenuNameExist();" value="<?php echo $name;?>" maxlength="249" data-required="1" class="form-control"/>
                                            <div id="res_menu_exist" class="text-danger"></div>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sku') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="sku" id="sku" value="<?php echo $sku ?>" maxlength="20" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?></label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="check_add_ons" id="check_add_ons" value="1" <?php echo ($check_add_ons == 1)?'checked':'' ?> class="add_ons">
                                        </div>
                                    </div>
                                    <?php if(!empty($addons_category)){ ?> 
                                    <div class="form-group category_wrap <?php echo ($check_add_ons == 1)?'display-yes':'display-no' ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('addons_category') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                        <?php  $is_multiple = ''; $mandatory = ''; $display_limit = ""; $j = 1;

                                        $is_multiple_category = (isset($is_multiple_category) && !empty($is_multiple_category)) ? $is_multiple_category : array();
                                        $mandatory_category = (isset($mandatory_category) && !empty($mandatory_category)) ? $mandatory_category : array();
                                        $is_display_limit = (isset($is_display_limit) && !empty($is_display_limit)) ? $is_display_limit : array();

                                        foreach ($addons_category as $key => $value) {  
                                            $addons_detail = (array_key_exists($value->entity_id, $add_ons_detail))?$add_ons_detail[$value->entity_id]:array(); 
                                            $is_multiple = (in_array($value->entity_id, $is_multiple_category))?1:0;
                                            $mandatory = (in_array($value->entity_id, $mandatory_category))?1:0;
                                            $display_limit = (in_array($value->entity_id, $is_display_limit))?1:0;
                                             ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input type="checkbox" class="category_checkbox" <?php echo (in_array($value->entity_id, $add_ons))?'checked':'' ?> name="addons_category_id[]" id="addons_category_id<?php echo $value->entity_id ?>" value="<?php echo $value->entity_id ?>" onchange="addAddons('<?php echo $j ?>','<?php echo $value->entity_id ?>',this.id)"> <?php echo $value->name ?>
                                                    
                                                    <div id="add_ons_category<?php echo $j; ?>" class="repeater_wrap add_ons_category<?php echo $value->entity_id ?> <?php echo (in_array($value->entity_id, $add_ons))?'display-yes':'display-no' ?>" >
                                                         <input type="checkbox" class="is_multiple" name="is_multiple[<?php echo $value->entity_id ?>]" id="is_multiple<?php echo $value->entity_id ?>" value="1" onchange="showDisplayLimit('<?php echo $value->entity_id ?>',this.id)" <?php echo ($is_multiple)?'checked':'' ?>> <?php echo $this->lang->line('is_multiple') ?>
                                                         <div class="row">
                                                            <div class="col-md-4">
                                                                <input type="checkbox" class="mandatory" name="mandatory[<?php echo $value->entity_id ?>]" id="mandatory<?php echo $value->entity_id ?>" value="1" <?php echo ($mandatory)?'checked':'' ?>> <?php echo $this->lang->line('mandatory') ?>
                                                            </div>
                                                        </div>
                                                        <div class="row <?php echo ($is_multiple == 1)?'display-yes':'display-no' ?>" id="showDisplayLimit<?php echo $value->entity_id ?>">
                                                        <div class="col-md-4">
                                                         <input type="checkbox" class="display_limit" name="display_limit" id="display_limit<?php echo $value->entity_id ?>" value="<?php echo $value->entity_id ?>" <?php echo ($display_limit)?'checked':'' ?> onchange="addDisplayLimit('<?php echo $value->entity_id ?>',this.id)"> <?php echo $this->lang->line('display_limit') ?>
                                                          
                                                            <?php 
                                                                foreach ($is_display_limit_value as $m => $n) 
                                                                {
                                                                   $val = ($value->entity_id==$n['category_id'])?$n['display_limit']:'';
                                                                   if(!empty($val))
                                                                   {
                                                                    break;
                                                                   }
                                                                }
                                                                
                                                            ?>
                                                        </div>                         
                                                        <div id="display_list<?php echo $value->entity_id ?>" class="form-group display_list <?php echo ($display_limit == 1)?'display-yes':'display-no' ?>">
                                                            <div class="col-md-4 dis_lim">
                                                                <input type="number" name="display_limit_value[<?php echo $value->entity_id ?>]" id="display_limit_value<?php echo $value->entity_id ?>" value="<?php echo ($val)?$val:'' ?>" class="form-control" min="1">
                                                            </div>
                                                            
                                                         
                                                        </div>   
                                                        </div>
                                                        <div data-repeater-list="add_ons_list[<?php echo $value->entity_id ?>]" class="add_ons_detail<?php echo $value->entity_id ?>"> 
                                                            <?php
                                                            $addons_detailcnt = 1;
                                                            if(!empty($addons_detail))
                                                            {
                                                               $addons_detailcnt = count($addons_detail); 
                                                            }
                                                            for ($i=0;$i < $addons_detailcnt;$i++) { 
                                                                $is_multiple = (array_key_exists($value->entity_id, $add_ons_detail))?$addons_detail[$i]->is_multiple:'';
                                                                ?> 
                                                            <div data-repeater-item class="outer-repeater">
                                                                <div class="form-group">
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                        <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i]))?$addons_detail[$i]->add_ons_name:''; ?>" class="form-control repeater_field name_repeater add_ons_name<?php echo $value->entity_id ?>" maxlength="249">
                                                                    </div>                                        
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                        <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i]))?$addons_detail[$i]->add_ons_price:''; ?>" class="form-control repeater_field price_repeater add_ons_price<?php echo $value->entity_id ?>" min="0" maxlength="19">
                                                                    </div>
                                                                    <div class="col-sm-2 delete-repeat <?php echo ($i > 0 && !empty($add_ons_detail))?'display-yes':'display-no'; ?>" >
                                                                        <label class="control-label">&nbsp;</label>
                                                                        <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail))?'delete_repeater':'' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>"/>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <?php } ?>  
                                                        </div> 
                                                        <?php //if($i == 0){ ?>
                                                        <div class="form-group">
                                                            <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                    <input data-repeater-create class="btn btn-green" type="button" value="<?php echo $this->lang->line('add') ?>"/>
                                                            </div> 
                                                        </div>
                                                        <?php //} ?>
                                                    </div>  
                                                </div>
                                            </div>
                                           
                                    <?php $j++;}   ?>
                                    <div id="checkbox_error2"></div>
                                    </div> 
                                    </div>
                                    <?php } ?>
                                    <div class="form-group price_tag">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('price') ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="price" id="price" value="<?php echo ($price)?$price:'' ?>" maxlength="19" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('detail') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="menu_detail" id="menu_detail" value="<?php echo $menu_detail;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image') ?></label>
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
                                                            <li><?php echo $this->lang->line('img_allow'); ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size'); ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'290px * 210px.'; ?></li>
                                                        </ul>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="error display-no" id="errormsg"></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='100' width='150' class="display-no"/>
                                            <video controls id="v-control" class="display-no">
                                                <source id="source" src="" type="video/mp4">
                                            </video>
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image)?$image:''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '' && file_exists(FCPATH.'uploads/'.$image)) {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image'); ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <?php /* ?><div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('ingredients'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <textarea name="ingredients" id="ingredients" class="form-control ckeditor"><?php echo $ingredients ?></textarea>
                                        </div>
                                    </div><?php */ ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('recipe_time'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <input type="number" class="form-control" name="recipe_time" id="recipe_time" value="<?php echo $recipe_time ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('popular_item'); ?></label>
                                        <div class="col-md-1">
                                           <input type="checkbox" name="popular_item" id="popular_item" value="1"  <?php echo (isset($popular_item) && $popular_item == 1)?'checked':'' ?>/>
                                        </div>
                                    </div>  
                                    <?php /* ?><div class="form-group">
                                       <label class="control-label col-md-3"><?php echo $this->lang->line('recipe'); ?></label>
                                       <div class="col-md-8">
                                           <select name="recipe" class="form-control sumo recipe" id="recipe">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php if(!empty($recipe_list)){
                                                foreach ($recipe_list as $key => $value) { ?>
                                                    <option value="<?php echo $value->content_id ?>" <?php echo ($value->content_id==$recipe[0]->recipe_content_id) ?'selected':'' ?> ><?php echo $value->name ?></option>    
                                            <?php } } ?>
                                           </select>
                                       </div>
                                    </div><?php */ ?>                                  
                                    <!-- foodtype 16-12-2020 start -->
                                    <div class="form-group">
                                       <label class="control-label col-md-3"><?php echo $this->lang->line('food_type'); ?><span class="required">*</span></label>
                                       <div class="col-md-8">
                                           <select name="food_type" class="form-control sumo food_type" id="food_type">
                                            
                                           </select>
                                       </div>
                                    </div>     
                                    <!-- foodtype 16-12-2020 end -->    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('availability'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <?php $availability = explode(',', @$availability); ?>
                                            <input type="checkbox" name="availability[]" id="availability" value="Breakfast" <?php echo (@in_array('Breakfast',$availability) || @in_array('breakfast',$availability))?'checked':''; ?>>
                                            <span class="availability-checkbox"><?php echo $this->lang->line('breakfast') ?></span>
                                            <input type="checkbox" name="availability[]" id="availability" value="Lunch" <?php echo (@in_array('Lunch',$availability)|| @in_array('lunch',$availability))?'checked':''; ?>>
                                            <span class="availability-checkbox"><?php echo $this->lang->line('lunch') ?></span>
                                            <input type="checkbox" name="availability[]" id="availability" value="Dinner" <?php echo (@in_array('Dinner',$availability) || @in_array('dinner',$availability))?'checked':''; ?>>
                                            <span class="availability-checkbox"><?php echo $this->lang->line('dinner') ?></span>
                                            <div id="checkbox_error">
                                            </div>
                                        </div>
                                    </div> 
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?> id="submit_page" value="Submit" class="btn btn-success default-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger default-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view_menu"><?php echo $this->lang->line('cancel'); ?></a>
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
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<script type="text/javascript">
    /*CKEDITOR.replace('ingredients', {
      language: 'ar'
    });*/
</script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<script type="text/javascript">
    /*CKEDITOR.replace('ingredients', {
      language: 'fr'
    });*/
</script>
<?php } ?>
<script>
jQuery(document).ready(function() 
{       
    Layout.init(); // init current layout
    $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
    chkveg_nonvegvalFn();
});
function readURL(input){
    $('#submit_page').prop("disabled",false);
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    if(input.files[0].size <= 512000){ // 500 KB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if(extension == 'mp4'){
                    $('#source').attr('src', e.target.result);
                    $('#v-control').show();
                    $('#preview').attr('src','').hide();
                }else{
                    $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                    $('#v-control').hide();
                    $('#source').attr('src', '');
                }
                $("#uploaded_image").hide();
                $('#errormsg').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('img_allow'); ?>").show();
            $('#submit_page').prop("disabled",true);
            $('#Image').val('');
            $("#uploaded_image").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
        $('#submit_page').prop("disabled",true);
        $('#Image').val('');
        $('#source').attr('src', '');
        $('#v-control').hide();
        $("#uploaded_image").show();
    }
}
//repeater 
$('.repeater_wrap').repeater({
    <?php if($entity_id == ''){ ?>            
    isFirstItemUndeletable: true,
    <?php } ?>
    show: function () {
        var count = $('.outer-repeater').length;
        $(this).slideDown();
        $(this).find('.delete-repeat').show();
        $(this).find('.repeater_field').attr('required',true);
        $(this).find('.repeater_field').addClass('error');
        $(this).find('.name_repeater').attr('id','add_ons_name'+count+1);
        $(this).find('.price_repeater').attr('id','add_ons_price'+count+1);
    },
    hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
    }
});
//add add ons
function addAddons(key,entity_id,id){
    if($('#'+id).is(':checked')){
        $('#add_ons_category'+key).show();
        $('.add_ons_category'+entity_id).find('.repeater_field').attr('required',true);
        $('.add_ons_category'+entity_id).find('.repeater_field').addClass('error');
    }else{
        $('#add_ons_category'+key).hide();
        $('.add_ons_category'+entity_id).find('.repeater_field').val('');
        $('.add_ons_category'+entity_id).find('.delete_repeater').trigger('click');
        $('.add_ons_category'+entity_id).find('.repeater_field').attr('required',false);
        $('.add_ons_category'+entity_id).find('.repeater_field').removeClass('error');
        $('#is_multiple'+entity_id).attr('checked',false);
        $('label.error').remove();
    }
}
$('.add_ons').change(function(){
    if($(this).is(':checked')){
        $('.category_wrap').show();
        // $('.price_tag').hide();
        // $('#price').val('');
    }else{
        $('.category_wrap').hide();
        $('.price_tag').show();
        $('.repeater_field').val('');
        $('.delete_repeater').trigger('click');
        $('.category_checkbox').attr('checked',false);
        $('.repeater_wrap').hide();
        $('.repeater_field').attr('required',false);
        $('.repeater_field').removeClass('error');
        $('label.error').remove();
        $('.is_multiple').attr('checked',false);
    }
});
//New code for as per suggestion :: 03-11-2020 :: Start
$('#restaurant_id').change(function(){
    chkveg_nonvegvalFn();
});
function chkveg_nonvegvalFn()
{
    var element = $('#restaurant_id').find('option:selected'); 
    var veg_type = element.attr("food-data");
    var restaurant_id = element.val();
    var entity_id = $('#entity_id').val();
    var language_slug = '<?php echo $this->uri->segment(4); ?>';
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getFoodType',
      data : {'restaurant_id':restaurant_id,'entity_id':entity_id,'language_slug':language_slug},
      success: function(response) {
        $('.food_type').empty().append(response);
        $('.food_type')[0].sumo.reload();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
//New code for as per suggestion :: 03-11-2020 :: End
function addDisplayLimit(entity_id,id){
    if($('#'+id).is(':checked')){
        $('#display_list'+entity_id).show();
            }else{
                $('#display_list'+entity_id).hide();
                $('#display_limit_value'+entity_id).val('');
                $('label.error').remove();
    }
}

function showDisplayLimit(entity_id,id){
    if($('#'+id).is(':checked')){
        $('#showDisplayLimit'+entity_id).show();
    }else{
        $('#showDisplayLimit'+entity_id).hide();
        $('#display_limit'+entity_id).attr('checked',false);
        $('#display_limit_value'+entity_id).val('');
        $('label.error').remove();
    }
}
function checkResMenuNameExist() {
    var entity_id = $('#entity_id').val();
    var category_id = $('#category_id').val();
    var restaurant_id = $('#restaurant_id').val();
    var menu_name = $('#name').val();
    if(category_id!='' && restaurant_id!='' && menu_name!=''){
        $.ajax({
            type: "POST",
            url: BASEURL+"<?php echo ADMIN_URL ?>/restaurant/checkResMenuNameExist/<?php echo $this->uri->segment(4) ?>",
            data: 'name=' + menu_name +'&call_from=ajax_call&entity_id='+entity_id +'&category_id='+category_id +'&restaurant_id='+restaurant_id,
            cache: false,
            success: function(html) {
              if(html > 0){
                $('#res_menu_exist').show();
                $('#res_menu_exist').html("<?php echo $this->lang->line('res_menu_exist'); ?>");        
                $(':input[type="submit"]').prop("disabled",true);
              } else {
                $('#res_menu_exist').html("");
                $('#res_menu_exist').hide();
                $(':input[type="submit"]').prop("disabled",false);
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
              $('#res_menu_exist').show();
              $('#res_menu_exist').html(errorThrown);
            }
        });
    } else {
        $('#res_menu_exist').html("");
        $('#res_menu_exist').hide();
        $(':input[type="submit"]').prop("disabled",false);
    }
}

//Code for pass only selected checkbox value :: Start
$("#form_add_menu" ).submit(function(event){
    var cnt= 1;
    $('.category_checkbox:checkbox').each(function (){
        var chkbocval = $(this).val();        
       if(this.checked) {           
           $(this).attr("disabled", false);
       }
       else
       {
           //$(this).attr("disabled", true);
           $('#is_multiple'+chkbocval).attr("disabled", true);
           $('#mandatory'+chkbocval).attr("disabled", true);
           $('#display_limit'+chkbocval).attr("disabled", true);
           $('#display_limit_value'+chkbocval).attr("disabled", true);
           $('.add_ons_name'+chkbocval).attr("disabled", true);
           $('.add_ons_price'+chkbocval).attr("disabled", true);           
           //$('.add_ons_detail'+chkbocval).hide();
       }
       cnt++;
    });  
});
//Code for pass only selected checkbox value :: End

</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>