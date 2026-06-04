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
                        <?php echo $this->lang->line('manage_tables') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('tables') ?>
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
                            <div class="caption"><?php echo $this->lang->line('tables') ?></div>
                            <div class="actions c-dropdown">
                                <?php if(in_array('table~add',$this->session->userdata("UserAccessArray"))) { ?>
                                    <a class="btn danger-btn btn-sm theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                            <?php if(isset($_SESSION['page_MSG']))
                            { ?>
                                <div class="alert alert-success alerttimerclose">
                                     <?php echo $_SESSION['page_MSG'];
                                     unset($_SESSION['page_MSG']);
                                     ?>
                                </div>
                            <?php } ?>
                            <div id="delete-msg" class="alert alert-success hidden alerttimerclose">
                                 <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover table-data" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                        <th><?php echo $this->lang->line('res_name') ?></th>
                                        <th><?php echo $this->lang->line('qr_code') ?></th>
                                        <th><?php echo $this->lang->line('table_no') ?></th>
                                        <?php /*foreach ($Languages as $lang) {?>
                                            <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                        <?php }*/ ?>
                                        <th><?php echo $this->lang->line('capacity') ?></th>
                                        <th><?php echo $this->lang->line('action') ?></th>
                                    </tr>
                                    <tr role="row" class="filter">
                                        <td></td>                                       
                                        <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                        <td></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="table_no"></td>
                                        <?php /*foreach ($Languages as $lang) {?>
                                            <td><input type="text" class="form-control form-filter input-sm" disabled="" name="<?php echo $lang->language_slug;?>"></td>
                                        <?php }*/ ?> 
                                        <td><input type="text" class="form-control form-filter input-sm" name="capacity"></td>
                                        <td>
                                            <button class="btn btn-sm red theme-btn filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search" ></i></button>
                                            <button class="btn btn-sm red theme-btn filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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
                    { "bSortable": false },
                    { "bSortable": true },
                    <?php /*foreach ($Languages as $lang) {?>
                    { "bSortable": false },
                    <?php }*/ ?>
                    { "bSortable": false },
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
                "sAjaxSource": "ajaxview", // ajax source
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
    // method for deleting
    function deleteDetail(entity_id,content_id)
    {   
        bootbox.confirm({
            message: "<?php echo $this->lang->line('delete_module'); ?>",
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
                    dataType : "html",
                    url : 'ajaxDelete',
                    data : {'content_id':content_id,'entity_id':entity_id},
                    success: function(response) {
                        grid.getDataTable().fnDraw(); 
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {           
                        alert(errorThrown);
                    }
                });
                }
            }
        });
    }
    function deleteAll(content_id, message)
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
                    url : 'ajaxDeleteAll',
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
    function disable_record(entity_id,Status)
    {
        var StatusVar = (Status==0)?"<?php echo $this->lang->line('active'); ?>":"<?php echo $this->lang->line('inactive'); ?>";
        bootbox.confirm({
            message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('cancel'); ?>",
                }
            }, 
            callback: function (disableConfirm) {  
            if (disableConfirm) {
                jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : 'ajaxDisable',
                    data : {'entity_id':entity_id,'status':Status,'tblname':'table_master'},
                    success: function(response) {
                        grid.getDataTable().fnDraw(); 
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                    }
                });
            }
            }
        });
    }
    function disableAll(ContentID,Status)
    {
        var StatusVar = (Status==0)?"<?php echo $this->lang->line('active'); ?>":"<?php echo $this->lang->line('inactive'); ?>";
        bootbox.confirm({
            message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('cancel'); ?>",
                }
            }, 
            callback: function (disableConfirm) {  
            if (disableConfirm) {
                jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : 'ajaxDisableAll',
                    data : {'content_id':ContentID,'status':Status,'tblname':'table_master'},
                    success: function(response) {
                    grid.getDataTable().fnDraw(); 
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                    }
                });
            }
            }
        });
    }
    $(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>