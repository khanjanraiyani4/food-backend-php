<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<style type="text/css">
.successfully-saved {
    -webkit-transition: opacity 3s ease-in-out;
    -moz-transition: opacity 3s ease-in-out;
    -ms-transition: opacity 3s ease-in-out;
    -o-transition: opacity 3s ease-in-out;
    opacity: 1;
}
</style>
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
                        <?php echo $this->lang->line('title_reason_management') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('title_reason_management') ?>
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
                            <div class="caption"><?php echo $this->lang->line('title_reason_management') ?> <?php echo $this->lang->line('list') ?></div>
                            
                            <div class="actions c-dropdown">
                                <?php if(in_array('reason_management~add',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button type="button" class="btn btn-sm default-btn danger-btn theme-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><?php echo $this->lang->line('add') ?><span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <?php foreach ($Languages as $lang) {
                                            $langname = ($lang->language_name == 'English')?$this->lang->line('english'):(($lang->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
                                        ?>
                                        <li><a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug?>"><?php echo $langname; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>                                
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
                                        <?php foreach ($Languages as $lang) {?>
                                            <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('reason_type') ?></th>
                                        <th><?php echo $this->lang->line('user_type') ?></th>
                                        <th><?php echo $this->lang->line('status') ?></th>
                                        <th><?php echo $this->lang->line('action') ?></th>
                                    </tr>
                                    <tr role="row" class="filter">
                                        <td></td>
                                        <?php foreach ($Languages as $lang) {?>
                                            <td><input type="text" class="form-control form-filter input-sm" name="title_<?php echo $lang->language_slug;?>"></td>
                                        <?php } ?>
                                        <td>
                                            <select name="reason_type" class="form-control form-filter input-sm">
                                                <option value=""><?php echo $this->lang->line('all')?></option>
                                                <option value="cancel"><?php echo $this->lang->line('cancel')?></option>
                                                <option value="reject"><?php echo $this->lang->line('reject')?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="user_type" class="form-control form-filter input-sm">
                                                <option value=""><?php echo $this->lang->line('all')?></option>
                                                <option value="Admin"><?php echo $this->lang->line('admin')?></option>
                                                <option value="Driver"><?php echo $this->lang->line('driver')?></option>
                                                <option value="Customer"><?php echo $this->lang->line('customer')?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="status" class="form-control form-filter input-sm">
                                                <option value=""><?php echo $this->lang->line('all')?></option>
                                                <option value="1"><?php echo $this->lang->line('active')?></option>
                                                <option value="0"><?php echo $this->lang->line('inactive')?></option>
                                            </select>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search" ></i></button>
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
<script type="text/javascript">
var grid;
var reason_count = <?php echo ($reason_count)?$reason_count:0; ?>;
jQuery(document).ready(function() {
    Layout.init(); // init current layout    
    // $('.alert-success').delay(3000).addClass("successfully-saved");
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
                <?php foreach ($Languages as $lang) {?>
                { "bSortable": false },
                <?php } ?>
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
                if(oSettings.aoData.length == 0 && reason_count != 0 && oData.iStart >= reason_count){
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
                oData.aaSorting = [[ 7, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "ajaxview", // ajax source
            "aaSorting": [[ 7, "desc" ]] // set first column as a default sort by asc
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
function disable_enable_reason(content_id,status)
{
    var StatusVar = (status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
    bootbox.confirm({
        message: StatusVar,
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
                  url : 'ajax_disable',
                  data : {'content_id':content_id,'status':status},
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
function delete_detail(entity_id,content_id)
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
                  url : 'ajax_delete',
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
function delete_reason(content_id,message)
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
                url : 'ajax_delete_all',
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
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>