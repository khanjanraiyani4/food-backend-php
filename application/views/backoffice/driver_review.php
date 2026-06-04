<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css" />
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
                     <?php echo $this->lang->line('reviews') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo '<a href='.base_url().ADMIN_URL.'/'.$this->controller_name.'/driver/>'.$this->lang->line('driver').'</a>' ?>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('reviews') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="page-content-wrapper">
                        <!-- BEGIN PAGE header--> 
                        <div class="row">
                            <div class="col-md-12">    
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption"><?php echo $this->lang->line('review') ?> <?php echo $this->lang->line('list') ?></div>
                                        <div class="actions">
                                            <a class="btn default-btn btn-sm" href="<?php echo base_url().ADMIN_URL ?>/users/driver_review_report/<?php echo $entity_id ?>"><i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('export_review')?></a>
                                        </div>
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
                                                <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                <th><?php echo $this->lang->line('name') ?></th>
                                                <th><?php echo $this->lang->line('customer') ?></th>
                                                <th><?php echo $this->lang->line('review') ?></th>
                                                <th><?php echo $this->lang->line('rating') ?></th>
                                                <th><?php echo $this->lang->line('date') ?></th>
                                                <th><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td></td>                                       
                                                <td><input type="text" class="form-control form-filter input-sm" name="name"></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="customer_name"></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="review"></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="rating"></td>
                                                <td><input type="text" class="form-control form-filter input-sm datepicker" name="date" placeholder="<?php echo $this->lang->line('select_date'); ?>"></td>
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button class="btn btn-sm  default-btn filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                    </div>
                                                    <button class="btn btn-sm default-btn filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
                                                </td>
                                            </tr>
                                            </thead>                                        
                                            <tbody>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script>
var grid;
var driver_review_count = <?php echo ($driver_review_count)?$driver_review_count:0; ?>;
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
                { "bSortable": false },
                null,
                null,
                null,
                null,
                { "bSortable": false },
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
                if(oSettings.aoData.length == 0 && driver_review_count != 0 && oData.iStart >= driver_review_count){
                    oData.iStart = 0;
                    localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                    location.reload();
                    //grid.getDataTable().fnDraw();
                } else {
                    localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                }
            },
            "fnStateLoad": function (oSettings) {
                var data = localStorage.getItem('DataTables_' + window.location.pathname);
                return JSON.parse(data);
            },
            "fnStateLoadParams": function (oSettings, oData) {
                oData.aaSorting = [[ 4, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxDriverReview/<?php echo $entity_id ?>", // ajax source
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
    var datepicker_format = "<?php echo datepicker_format; ?>";
    $('.datepicker').datepicker({
        format: datepicker_format,
        autoclose: true,
    });
    
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>