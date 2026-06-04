<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/jquery.timepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar'); 
if($this->input->post()){
    foreach ($this->input->post() as $key => $value) {
        if($value && $value!=''){
            $$key = @htmlspecialchars($this->input->post($key));    
        }        
    }
} else {
    $FieldsArray = array('content_id','entity_id','name','slug','image','recipe_detail','ingredients','food_type','youtube_video','recipe_time','detail','is_masterdata','meta_title','meta_description');
    foreach ($FieldsArray as $key) {
        $$key = @htmlspecialchars($edit_records->$key);
    }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('recipe');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('recipe');
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
}
$usertypes = getUserTypeList($this->session->userdata('language_slug'));
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('recipes') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('recipes') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add_<?php echo $this->controller_name; ?>" name="form_add_<?php echo $this->controller_name; ?>" method="post" class="form-horizontal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div id="iframeloading" class="frame-load display-no">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('name') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="hidden" id="slug" name="slug" value="<?php echo ($slug) ? $slug : '';?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('detail') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="detail" id="detail" value="<?php echo $detail;?>" maxlength="249" data-required="1" class="form-control"/>
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('ingredients'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <textarea name="ingredients" id="ingredients" class="form-control ckeditor"><?php echo $ingredients ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('recipe_detail'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <textarea name="recipe_detail" id="recipe_detail" class="form-control ckeditor"><?php echo $recipe_detail ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('recipe_time'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                           <input type="number" class="form-control" name="recipe_time" id="recipe_time" value="<?php echo $recipe_time ?>">
                                        </div>
                                    </div>
                                    <input type="hidden" name="recipe_content" value="<?php echo(isset($edit_records->content_id) && !empty($edit_records->content_id)) ? $edit_records->content_id : '' ?>">
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('menu_item'); ?></label>
                                        <div class="col-md-4">
                                            <select name="menu" class="form-control sumo" id="menu" >
                                                <option value=""><?php echo $this->lang->line('select_').' '.$this->lang->line('menu'); ?></option>
                                                <?php if(!empty($menu_item)){
                                                    $menu_item_arr = (isset($menu_item_arr) && !empty($menu_item_arr)) ? $menu_item_arr : array();
                                                    foreach ($menu_item as $key => $value) { ?>
                                                        <option value="<?php echo $value->content_id ?>" <?php echo (in_array($value->content_id,array_column($menu_item_arr,'menu_content_id')))?'selected':'' ?> ><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- foodtype 15-12-2020 start -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('food_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="food_type" class="form-control sumo required" id="food_type">
                                                <?php if(!empty($food_typearr)){
                                                    foreach ($food_typearr as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $food_type)?'selected':'' ?> ><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>    
                                    <!-- foodtype 15-12-2020 end -->
                                    <!-- Youtude Video code :: start -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('youtube_link'); ?></label>
                                        <div class="col-md-4">
                                            <input type="text" name="youtube_video" id="youtube_video" value="<?php echo $youtube_video;?>" maxlength="249" data-required="1" class="form-control"/>     
                                        </div>
                                    </div>    
                                    <!-- Youtube Video Code :: end -->
                                    <!-- meta & desc start -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('meta_title')  ?></label>
                                        <div class="col-md-4">
                                            <input type="text" name="meta_title" id="meta_title" value="<?php echo $meta_title;?>" maxlength="70" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('meta_description')  ?></label>
                                        <div class="col-md-4">
                                            <textarea type="text" name="meta_description" id="meta_description" maxlength="160" class="form-control" rows="2" /><?php echo $meta_description;?></textarea>
                                        </div>
                                    </div>
                                    <!-- meta & desc End -->
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?> type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn theme-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel') ?></a>
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
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
       $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>...", selectAll: true });
});
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
            $('#Image').val('');
            $("#old").show();
        }
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $(':input[type="submit"]').prop("disabled",true);
        $('#Image').val('');
        $("#old").show();
    }
}
$('#Image').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#Image')[0].files[0].name;
  $(this).prev('label').text(file);
});
$(document).ready(function() {
    $('input[name=name]').on('focusout',function(){
        var name = $(this).val();
        $('#meta_title').val(name);
    });
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>