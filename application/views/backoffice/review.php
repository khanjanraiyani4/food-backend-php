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
                        <?php echo $this->lang->line('rating_review'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('rating_review'); ?>
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
                            <div class="caption"><?php echo $this->lang->line('rating_review'); ?></div>
                            <!-- <div class="actions">
                                <a class="btn default-btn btn-sm" href="<?php echo base_url();?>admin/review/add"><i class="fa fa-plus"></i> Add</a>
                            </div> -->
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">                            
                            <?php if($_SESSION['userPageMSG'])
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['userPageMSG'];
                                     unset($_SESSION['userPageMSG']);
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
                                            <th><?php echo $this->lang->line('res_name'); ?></th>
                                            <th><?php echo $this->lang->line('customer'); ?>&nbsp;<?php echo $this->lang->line('name'); ?></th>
                                            <th><?php echo $this->lang->line('review'); ?></th>
                                            <th><?php echo $this->lang->line('rating'); ?></th>
                                            <th><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>                                       
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="customer"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="review"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="rating"></td>
                                            <td style="white-space: nowrap;">
                                                <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm red filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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
var review_count = <?php echo ($review_count)?$review_count:0; ?>;
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
                null,
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
                console.log(review_count);
                if(oSettings.aoData.length == 0 && review_count != 0 && oData.iStart >= review_count){
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
                oData.aaSorting = [[ 5, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "ajaxview", // ajax source
            "aaSorting": [[ 5, "desc" ]] // set first column as a default sort by asc
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

// method for active/deactive 
function disableDetail(entity_id,status)
{
    var statusVar = (status==0)?'active':'deactive';
    bootbox.confirm("Are you sure you want to "+statusVar+" this?", function(disableConfirm) {    
        if (disableConfirm) {
            jQuery.ajax({
              type : "POST",
              dataType : "json",
              url : 'ajaxdisable',
              data : {'entity_id':entity_id,'status':status},
              success: function(response) {
                   grid.getDataTable().fnDraw(); 
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
              }
           });
        }
    });
}
// method for deleting
function deleteDetail(entity_id, message)
{   
    bootbox.confirm(message, function(disableConfirm) {    
        if (disableConfirm) {
            jQuery.ajax({
              type : "POST",
              dataType : "html",
              url : 'ajaxDelete',
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

</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>