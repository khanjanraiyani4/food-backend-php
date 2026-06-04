<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL.'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    <?php echo $this->lang->line('restaurant_error_reports'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('restaurant_error_reports'); ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('restaurant_error_reports'); ?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">                            
                            <?php if(isset($_SESSION['errorReport_MSG']))
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['errorReport_MSG'];
                                     unset($_SESSION['errorReport_MSG']);
                                     ?>
                                </div>
                            <?php } ?>
                            <div id="delete-msg" class="alert alert-success hidden">
                                <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                            <th><?php echo $this->lang->line('restaurant_error_report');?></th>
                                            <th><?php echo $this->lang->line('email');?></th>
                                            <th><?php echo $this->lang->line('message');?></th>
                                            <th><?php echo $this->lang->line('action');?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>                                       
                                            <td><input type="text" class="form-control form-filter input-sm" name="error_report_title"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="error_report_email"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="error_report_message"></td>
                                            <td style="white-space: nowrap;">
                                                <button class="btn btn-sm  danger-btn filter-submit margin-bottom" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm danger-btn filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
                                            </td>
                                        </tr>
                                        </thead>                                        
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<div id="view_error_report" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('restaurant_error_report') ?></h4>
      </div>
      <div class="modal-body">
        <div class="portlet-body">
            <div class="table-container">
                <table class="table table-striped table-bordered table-hover" id="reportview">
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script>
var grid;
jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    grid = new Datatable();
    grid.init({
        src: $("#datatable_ajax"),
        onSuccess: function(grid) {
            // execute some code after table records loaded
        },
        onError: function(grid) {
            // execute some code on network or other general error  
        },
        dataTable: { 
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },                
                null,
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            "oLanguage":{
                "sProcessing": sProcessing,
                "sLengthMenu": sLengthMenu,
                "sInfo": sInfo,
                "sInfoEmpty":sInfoEmpty,
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
                oData.aaSorting = [[ 4, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "ajaxview", // ajax source
            "aaSorting": [[ 4, "desc" ]] // set first column as a default sort by asc
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
function deleteErrorReport(entity_id)
{    
    bootbox.confirm("<?php echo $this->lang->line('delete_module') ?>",function(deleteConfirm) {    
        if (deleteConfirm) {
            jQuery.ajax({
              type : "POST",
              dataType : "json",
              url : 'ajaxdeleteReport',
              data : {'entity_id':entity_id},
              success: function(response) {
                   //grid.getDataTable().fnDraw(); 
                   location.reload();
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
              }
           });
        }
    });
}
function viewErrorReport(entity_id)
{    
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : 'ajaxviewReport',
      data : {'entity_id':entity_id},
      success: function(response) {
        console.log(response.entity_id);
        $('#view_error_report #reportview').html(response);
        $('#view_error_report').modal('show');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>