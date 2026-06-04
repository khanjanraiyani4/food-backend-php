<?php 
 $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','image');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('title_slider_image_edit');        
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('title_slider_image_add');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
}
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"> <?php echo $this->lang->line('title_slider_image') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                             <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"> <?php echo $this->lang->line('title_slider_image') ?></a>
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
                                <div id="iframeloading" class="frame-load display-no">
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
                                    <div class="form-group"> 
                                        <label class="control-label col-md-3"> <?php echo $this->lang->line('title_slider_image') ?><span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <input type="hidden" name="uploadedSliderImage" id="uploadedSliderImage" value="<?php echo $image; ?>" />
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id;?>" />
                                            
                                            <div class="custom-file-upload">
                                                <label for="Slider_image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload_image') ?>
                                                </label>
                                                <input type="file" name="Slider_image" id="Slider_image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this);"/>&ensp;
                                                <div class="custom--tooltip">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <ul>
                                                            <li><?php echo $this->lang->line('img_allow')  ?></li>
                                                            <li><?php echo $this->lang->line('max_file_size') ?></li>
                                                            <li><?php echo $this->lang->line('recommended_size').'1024px * 500px.'; ?></li>
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
                                </div>   
	                            <div class="form-actions fluid">
	                                <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn default" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
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
    <?php if($image!=""){?>        
        jQuery( "#Slider_image" ).prop('required',false);
    <?php } else { ?>
        jQuery( "#Slider_image" ).prop('required',true);
    <?php } ?>
});
<?php if($image=="") { ?>
    jQuery('#form_add_slider').validate({
      ignore:[],
      rules: {
        Slider_image: {
          required: true,
        }
      },
      errorPlacement: function(error, element) 
      {
        if (element.attr("name") == "Slider_image") 
        {
          error.insertAfter('#errormsg');
        } 
        else 
        {
          error.insertAfter(element);
        }
      }
    });
<?php } ?>
// previewing image when selected
function readURL(input) { 
    var fileInput = document.getElementById('Slider_image');
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
            $('#Slider_image').val('');
            $("#old").show();
        } 
    }else{
        $('#preview').attr('src', '').attr('style','display: none;');
        $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
        $('#Slider_image').val('');
        $("#old").show();
    }
}
$('#Slider_image').change(function() {
  var i = $(this).prev('label').clone();
  var file = $('#Slider_image')[0].files[0].name;
  $(this).prev('label').text(file);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>