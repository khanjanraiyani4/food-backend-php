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
  $FieldsArray = array('content_id','entity_id','restaurant_id','name','price','detail','availability','image','is_masterdata');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('package');          
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_package/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
  
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('package');          
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add_package/".$this->uri->segment('4');
  
}
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('event_package') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view_package"><?php echo $this->lang->line('packages') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->package_prefix ?>" name="form_add<?php echo $this->package_prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div id="iframeloading" class="frame-load display-no" style= "display: none;">
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
                                        <select name="restaurant_id" class="form-control sumo" id="restaurant_id" onchange="getCurrency(this.value)">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php if(!empty($restaurant)) {
                                                foreach ($restaurant as $key => $value) { ?>
                                                   <option value="<?php echo $value->content_id ?>" <?php echo ($value->content_id == $restaurant_id) || ($event_restaurant[0]->restaurant_id == $value->content_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                            <?php } } ?>  
                                        </select></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('package_name') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('package_price') ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="price" id="price" value="<?php echo ($price)?$price:'' ?>" maxlength="19" min="0" data-required="1" class="form-control"/>
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('package_description') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <textarea name="detail" id="detail" class="form-control ckeditor"><?php echo $detail ?></textarea>
                                        </div>
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
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image) ? $image:''; ?>" />
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('availability') ?><span class="required">*</span></label>
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
                                        <button <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?> type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger default-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view_package"><?php echo $this->lang->line('cancel') ?></a>
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
    CKEDITOR.replace('detail', {
      language: 'ar'
    });
</script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<script type="text/javascript">
    CKEDITOR.replace('detail', {
      language: 'fr'
    });
</script>
<?php } ?>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
     $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
});
<?php
$date = new DateTime();
?>
//add ons functionality
$('.add_ons').change(function(){
    if($(this).is(':checked')){
        $('.category_wrap').show();
        $('.repeater_field').attr('required',true);
        $('.repeater_field').addClass('error');
    }else{
        $('.category_wrap').hide();
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
window.outerRepeater = $('.category_wrap').repeater({
    isFirstItemUndeletable: true,
    show: function() {
        var count = $('.outer-repeater').length;
        $(this).slideDown();
        $(this).find('.repeater_field').attr('required',true);
        $(this).find('.repeater_field').addClass('error');
        $(this).find('.title_repeater').attr('id','add_ons_title'+count);
        $(this).find('.name_repeater').attr('required',true);
        $(this).find('.name_repeater').addClass('error');
        var time = $.now();
        $(this).find('.name_repeater').attr('id','add_ons_name'+time);
    },
    hide: function(deleteElement) {
      $(this).slideUp(deleteElement);
    },
    repeaters: [{
      isFirstItemUndeletable: true,
      selector: '.inner-repeater',
      show: function() {
        $(this).slideDown();
        $(this).find('.name_repeater').attr('required',true);
        $(this).find('.name_repeater').addClass('error');
        var times = $.now();
        $(this).find('.name_repeater').attr('id','add_ons_name'+times);
      },
      hide: function(deleteElement) {
        $(this).slideUp(deleteElement);
      }
    }]
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
function readURL(input) {
    $('#submit_page').prop("disabled",false);
    var fileInput = document.getElementById('Image');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    var file_size = fileInput.size;
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
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>