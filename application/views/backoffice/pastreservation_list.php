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
                        <?php echo $this->lang->line('pastreservation_lists'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('pastreservation_lists'); ?>
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
                            <div class="caption"><?php echo $this->lang->line('pastreservation_lists'); ?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                            <?php                                         
                            if(isset($_SESSION['page_MSG']))
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['page_MSG'];
                                     unset($_SESSION['page_MSG']);
                                     ?>
                                </div>
                            <?php } ?>
                            <div id="delete-msg" class="alert alert-success hidden">
                                 <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th class="table-checkbox">#</th>
                                        <th><?php echo $this->lang->line('table_no') ?></th>
                                        <th><?php echo $this->lang->line('capacity') ?></th>
                                        <th><?php echo $this->lang->line('orders') ?></th>
                                        <th><?php echo $this->lang->line('customer') ?></th>
                                        <th><?php echo $this->lang->line('res_name') ?></th>
                                        <th><?php echo $this->lang->line('reservation_date') ?></th>
                                        <th><?php echo $this->lang->line('payment_status') ?></th>
                                        <th><?php echo $this->lang->line('order_status') ?></th>
                                        <th><?php echo $this->lang->line('action') ?></th>
                                    </tr>
                                    <tr role="row" class="filter">
                                        <td></td>                                       
                                        <td><input type="text" class="form-control form-filter input-sm" name="table_no"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="capacity"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="order_id"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="customer"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="reservation_date"></td>
                                        <td></td>
                                        <td>
                                            <select name="order_status" class="form-control form-filter input-sm">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php $order_status = dinein_order_status($this->session->userdata('language_slug'));
                                                foreach ($order_status as $key => $value) { ?>
                                                     <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                <?php  } ?>                                   
                                            </select>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                            <button class="btn btn-sm default-btn filter-cancel"><i class="fa fa-refresh" title="<?php echo $this->lang->line('reset') ?>"></i></button>
                                        </td>
                                    </tr>
                                    </thead>                                        
                                    <tbody class="order-tbl-action">
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
<div id="view_order_detail" class="modal fade" role="dialog">
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
        dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },
                { "bSortable": true },
                { "bSortable": true },
                { "bSortable": true },
                { "bSortable": true },
                { "bSortable": true },
                { "bSortable": false },
                { "bSortable": false },
                { "bSortable": false },
                { "bSortable": false },
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
            "fnStateSave": function (oSettings, oData)
            {
                localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings)
            {
                var data = localStorage.getItem('DataTables_' + window.location.pathname);
                return JSON.parse(data);
            },
            "fnStateLoadParams": function (oSettings, oData) {
                oData.aaSorting = [[ 3, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "ajaxPastReservationview", // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
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
function deleteAll(content_id,message)
{    
    bootbox.confirm({
        message: message,
        buttons: {
            confirm: {
                label: "<?php echo $this->lang->line('ok'); ?>",
            },
            cancel: {
                label: "<?php echo $this->lang->line('cancel'); ?>",
            }
        },
        callback: function (deleteConfirm) {         
          if (deleteConfirm) {
              jQuery.ajax({
                type : "POST",
                dataType : "json",
                url : 'ajaxDeleteAllReservation',
                data : {'content_id':content_id},
                success: function(response) {
                  //grid.getDataTable().fnDraw(); 
                  location.reload();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {           
                  alert(errorThrown);
                }
             });
          }
        }
    });
}
// openOrderDetails
function openOrderDetails(entity_id){
    jQuery.ajax({
      type : "POST",                      
      url : BASEURL+"backoffice/order/orderDetail",
      data : {'entity_id':entity_id},
      cache: false,
      success: function(response) {   
        $('#view_order_detail').html(response);
        $('#view_order_detail').modal('show');      
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>