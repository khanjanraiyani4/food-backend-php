<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
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
  $FieldsArray = array('content_id','entity_id','name','description','meta_title','meta_keyword','meta_description','image','cms_icon');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('cms');           
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".$this->uri->segment('4')."/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('cms');        
    $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add/".$this->uri->segment('4');
}?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('cms')  ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home')  ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cms')  ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('cms_page_title')  ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="uploadedCms_image" value="<?php echo isset($image)?$image:''; ?>" />
                                            <input type="hidden" name="uploadedCms_icon" value="<?php echo isset($cms_icon)?$cms_icon:''; ?>" />
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id;?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id)?$content_id:$this->uri->segment('5');?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('banner_image')  ?></label>
                                        <div class="col-md-5">
                                            <div class="custom-file-upload">
                                                <label for="CMSImage" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                </label>
                                                <input type="file" name="CMSImage" id="CMSImage" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this);"/>&ensp;
                                                <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <ul>
                                                            <li><?php echo $this->lang->line('img_allow')  ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size') ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'1280px * 720px.'; ?></li>
                                                        </ul>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="error display-no" id="errormsg"></span>
                                            <img id="preview" height='100' width='150' class="display-no"/>
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($image) && $image != '' && file_exists(FCPATH.'uploads/'.$image)) {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                    <?php $path_info = pathinfo($image);
                                                        $type = $path_info['extension'];
                                                        if($type == 'png' || $type == 'jpg' || $type == 'jpeg' || $type == 'gif'){ ?>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$image;?>">
                                            <?php } } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('cms_page_content')  ?><span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <textarea class="ckeditor form-control" name="description" id="description" rows="6" data-required="1" ><?php echo $description;?></textarea>                                           
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('cms_page_icon')  ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="cms_icon" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                </label>
                                                <input type="file" class="<?php echo ($cms_icon == '')?'required':''; ?>" name="cms_icon" id="cms_icon" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readIconURL(this);"/>&ensp;
                                                <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <ul>
                                                            <li><?php echo $this->lang->line('img_allow')  ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size')  ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'20px * 20px.'; ?></li>
                                                        </ul>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="error display-no" id="icon_errormsg"></span>
                                            <img id="icon_preview" height='100' width='150' class="display-no"/>
                                        </div>
                                    </div>
                                    <div class="form-group" id="icon_old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if(isset($cms_icon) && $cms_icon != '' && file_exists(FCPATH.'uploads/'.$cms_icon)) {?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                    <?php $path_info = pathinfo($cms_icon);
                                                        $type = $path_info['extension'];
                                                        if($type == 'png' || $type == 'jpg' || $type == 'jpeg' || $type == 'gif'){ ?>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo base_url().'uploads/'.$cms_icon;?>">
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit')  ?></button>
                                        <a class="btn default" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel')  ?></a>
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
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
// previewing image when selected
function readURL(input) { 
    var fileInput = document.getElementById('CMSImage');
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
            $('#CMSImage').val('');
            $("#old").show();
        } 
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $('#CMSImage').val('');
        $("#old").show();
    }
}

$('#CMSImage').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#CMSImage')[0].files[0].name;
  $(this).prev('label').text(file);
});

function readIconURL(input) { 
    var fileInput = document.getElementById('cms_icon');
    var filePath = fileInput.value;
    var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
    if(input.files[0].size <= 512000){ // 500KB
        if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
            if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#icon_preview').attr('src', e.target.result).attr('style','display: inline-block;');
                $("#icon_old").hide();
                $('#icon_errormsg').html('').hide();
            }
            reader.readAsDataURL(input.files[0]);
            }
        }
        else{
            $('#icon_preview').attr('src', '').attr('style','display: none;');
            $('#icon_errormsg').html("<?php echo $this->lang->line('img_allow') ?>").show();
            $('#cms_icon').val('');
            $("#icon_old").show();
        } 
    }else{
        $('#icon_preview').attr('src', '').attr('style','display: none;');
        $('#icon_errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $('#cms_icon').val('');
        $("#icon_old").show();
    }
}

$('#cms_icon').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#cms_icon')[0].files[0].name;
  $(this).prev('label').text(file);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>