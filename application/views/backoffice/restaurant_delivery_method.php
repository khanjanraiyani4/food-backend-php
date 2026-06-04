<?php $this->load->view(ADMIN_URL.'/header');?>
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
    <?php $form_action  = base_url().ADMIN_URL.'/'.$this->controller_name."/add_delivery_method"; ?>
    <!-- END sidebar -->
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('res_delivery_method'); ?> </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home')  ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('res_delivery_method'); ?>
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
                            <div class="caption"><?php echo $this->lang->line('res_delivery_method'); ?> <?php echo $this->lang->line('list'); ?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <?php if(isset($_SESSION['page_MSG'])) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $_SESSION['page_MSG'];
                                        unset($_SESSION['page_MSG']); ?>
                                    </div>
                                <?php } ?>
                                <div class="alert alert-success display-no" id="success_message"></div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                            <th><?php echo $this->lang->line('restaurant') ?>/<?php echo $this->lang->line('branch') ?></th>
                                            <th><?php echo $this->lang->line('delivery_method') ?></th>
                                            <th><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="res_branch_name"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="delivery_method"></td>
                                            <td style="white-space: nowrap;">
                                                <button class="btn btn-sm  default-btn filter-submit margin-bottom" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm default-btn filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
<!-- Modal -->
<div id="add_delivery_method" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_delivery_method') ?></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form id="form_add_delivery_method" name="form_add_delivery_method" method="post" class="form-horizontal" enctype="multipart/form-data" >
                    <div class="row">
                        <div class="col-sm-12" id="add_delivery_method_section">
                            <div class="alert alert-danger display-no" id="err_message"></div>
                            <div class="form-group">
                                <input type="hidden" name="restaurant_id" id="restaurant_id" value="">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('delivery_methods') ?><span class="required">*</span></label>
                                <div class="col-sm-8">
                                    <select name="delivery_method_id[]" multiple=""  id="delivery_method_id" class="form-control form-filter input-sm sumo" required>
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                    </select>                                               
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Submit"><?php echo $this->lang->line('submit')  ?></button>
                                    <a class="btn btn-sm  default-btn filter-submit margin-bottom" onclick="close_delivery_method_form()"><?php echo $this->lang->line('cancel')  ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script>
var grid;
jQuery(document).ready(function() { 
    Layout.init();
    grid = new Datatable();
    grid.init({
        src: $("#datatable_ajax"),
        onSuccess: function(grid) {
        },
        onError: function(grid) { 
        },
        dataTable: {
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            "oLanguage":{
                "sProcessing": sProcessing,
                "sLengthMenu": sLengthMenu,
                "sInfo": sInfo,
                "sInfoEmpty":'', //sInfoEmpty,
                "sGroupActions":sGroupActions,
                "sAjaxRequestGeneralError": sAjaxRequestGeneralError,
                "sEmptyTable": sEmptyTable,
                "sZeroRecords":sZeroRecords,
                "oPaginate": {
                    "sPrevious": sPrevious,
                    "sNext": sNext,
                    "sPage": sPage,
                    "sPageOf":sPageOf,
                    "sFirst": sFirst,
                    "sLast": sLast
                }
            },
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                var data = localStorage.getItem('DataTables_' + window.location.pathname);
                return JSON.parse(data);
            },
            "fnStateLoadParams": function (oSettings, oData) {
                oData.aaSorting = [[ 3, "desc" ]];
            },
            "bServerSide": true,
            "sAjaxSource": "ajax_res_delivery_method_view" 
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
});
jQuery(document).ready(function() {
    $('.sumo').SumoSelect({search: true, triggerChangeCombined: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
});
function open_delivery_method_form(restaurant_id){
    $('#restaurant_id').val(restaurant_id);
    jQuery.ajax({
        type : "POST",
        dataType :"html",
        url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/get_delivery_methods',
        data : {'entity_id':restaurant_id},
        success: function(response) {
          $('#delivery_method_id').empty().append(response);
          $('#delivery_method_id')[0].sumo.reload();
          $('#add_delivery_method').modal('show');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
    });
}
function close_delivery_method_form(){
    $("#add_delivery_method").modal('hide');
}
$('#form_add_delivery_method').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        var delivery_method_id = $('#delivery_method_id').val();
        var restaurant_id = $('#restaurant_id').val();
        jQuery.ajax({
            type: "POST",
            dataType : "json",
            url: '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add_delivery_method',
            data: {"delivery_method_id":delivery_method_id,"restaurant_id":restaurant_id,"submit_page":'Submit'},
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(response) {
                grid.getDataTable().fnDraw();
                $('#quotes-main-loader').hide();
                if(response.success_msg){
                    $('#err_message').hide();
                    $('#add_delivery_method_section').hide();
                    $('#success_message').html(response.success_msg);
                    $('#success_message').show();
                    //setTimeout(function(){
                        $("#add_delivery_method").modal('hide');
                        //$('#success_message').hide();
                        $('#add_delivery_method_section').show();
                    //}, 1000);
                }
                if(response.validation_errors){
                    $('#err_message').html(response.validation_errors);
                    $('#err_message').show();
                }
            }
        });
    }
    return false;
});
$('#add_delivery_method').on('hidden.bs.modal', function () {
    if($('#success_message').is(':visible')) {
        setTimeout(function(){
            $("#success_message").hide();
        }, 5000);
    }
    $('#form_add_delivery_method').validate().resetForm();
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>