<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<style type="text/css">
    .selection-error{
        color: red;
        font-weight: 600;
        display: none;
    }
</style>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
<?php $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add_menu_suggestion"; ?>
<!-- END sidebar -->
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('manage_item_suggestion'); ?> </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home')  ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('manage_item_suggestion'); ?>
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
                            <div class="caption"><?php echo $this->lang->line('add_menu_suggestion'); ?></div>
                        </div>
                        <div class="portlet-body form">
                            <?php                                         
                            if(isset($_SESSION['page_MSG']))
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['page_MSG'];
                                     unset($_SESSION['page_MSG']);
                                     ?>
                                </div>
                            <?php } ?>
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add_menu_suggestion" name="form_add_menu_suggestion" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body">                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                    	<label class="control-label col-md-3">
                                    		<?php echo $this->lang->line('restaurant'); ?>
                                    		<span class="required">*</span>
                                    	</label>
                                        <div class="col-md-8">
                                            <select name="restaurant_id" class="form-control sumo" id="restaurant_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id; ?>"><?php echo $value->name ?></option>    
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group hidden-row" >
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('menu_item'); ?></label>  
                                        <div class="col-md-8">
                                            <select name="item_id[]" multiple="" class="form-control sumo" id="item_id">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>    
                                            </select>
                                            <h5 class="selection-error"><?php echo $this->lang->line('select_limit_three'); ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn default"><?php echo $this->lang->line('submit')  ?></button>
                                        <a class="btn default" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/menu_item_suggestion"><?php echo $this->lang->line('cancel')  ?></a>
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
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
     $('.sumo').SumoSelect({search: true, triggerChangeCombined: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
    $('#restaurant_id').change(function (event) {
        var restaurant_id = $(this).val();
        jQuery.ajax({
            type : "POST",
            dataType :"html",
            url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/get_menu_items',
            data : {'entity_id':restaurant_id},
            success: function(response) {
              $('#item_id').empty().append(response);
              $('#item_id')[0].sumo.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
              alert(errorThrown);
            }
        });
    });
    var last_valid_selection = null;
    $('#item_id').change(function (event) {
        if ($(this).val() != null && $(this).val().length > 3) {
            bootbox.alert({
                message: "<?php echo $this->lang->line('select_limit_three'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
            var $this = $(this);
            $this[0].sumo.unSelectAll();
            $(".selection-error").css("display", "block");
        } else {
            last_valid_selection = $(this).val();
            $(".selection-error").css("display", "none");
        }
    });
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>