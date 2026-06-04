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
                        <?php echo $this->lang->line('manage_res'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('restaurant') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->
            <?php //restaurant import :: start
            if(in_array('restaurant~import_restaurant',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box red">
                            <div class="portlet-title portlet-titlecenter">
                                <div class="caption"><?php echo $this->lang->line('restaurant') ?>&nbsp;(<?php echo $this->lang->line('title_multiple_items') ?>)</div>
                                <div class="actions c-dropdown">
                                    <a href="<?php  echo base_url().ADMIN_URL.'/restaurant/download_restaurant_sample'; ?>" name="download_sample" id="download_sample" value="Download Sample File" class="btn btn-success default-btn theme-btn" style="text-decoration: underline; font-weight: bold; font-size: 14px;" ><?php echo $this->lang->line('sample_file_download'); ?></a>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <?php if(isset($_SESSION['Import_Error']))
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['Import_Error'];
                                             unset($_SESSION['Import_Error']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors();?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/import_restaurant" id="form_add_restaurant_import" name="form_add_restaurant_import" method="post" class="horizontal-form form-add-restaurant" enctype="multipart/form-data" >
                                        <div class="row">
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('res_file') ?><span class="required">*</span></label>
                                                    <input type="file" name="import_restaurant_file" id="import_restaurant_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"> 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('select_timezone') ?><span class="required">*</span></label>
                                                    <select name="select_timezone" class="form-control sumo">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <option value="US/Eastern">US/Eastern</option>
                                                        <option value="US/Central">US/Central</option>
                                                        <option value="Asia/Karachi">Asia/Karachi</option>
                                                        <option value="Asia/Kolkata">Asia/Kolkata</option>
                                                    </select> 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label blank_label hidden-991" style="display: block;">&nbsp;</label>
                                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn theme-btn" ><?php echo $this->lang->line('import_res'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            <?php }
            //restaurant import :: end ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title portlet-titlecenter">
                            <div class="caption"><?php echo $this->lang->line('restaurant') ?></div>
                            <div class="actions c-dropdown">
                                <?php if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="online_res"><i class="fa fa-toggle-on"></i> <?php echo $this->lang->line('online') ?></button>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="offline_res"><i class="fa fa-toggle-off"></i> <?php echo $this->lang->line('offline') ?></button>
                                <?php } ?>
                                <?php if(in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="active_res"><i class="fa fa-check"></i> <?php echo $this->lang->line('active') ?></button>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="deactive_res"><i class="fa fa-ban"></i> <?php echo $this->lang->line('inactive') ?></button>
                                <?php } ?>
                                <?php if(in_array('restaurant~add',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button type="button" class="btn btn-sm default-btn theme-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo $this->lang->line('add') ?><span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu theme-btn pull-right" role="menu">
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
                                <table class="table table-striped table-bordered table-hover table-data" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <?php if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray")) || in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {  ?>
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <?php } ?>
                                            <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                            <?php foreach ($Languages as $lang) {?>
                                                <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('city_name') ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('online').'/'.$this->lang->line('offline') ?></th>
                                            <th><?php echo $this->lang->line('order_schedule_mode') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                              <?php if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray")) || in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                                            <td></td>
                                        <?php } ?>
                                            <td></td>
                                            <?php foreach ($Languages as $lang) {?>
                                                <td><input type="text" class="form-control form-filter input-sm" name="title_<?php echo $lang->language_slug;?>"></td>
                                            <?php } ?>
                                            <td><input type="text" class="form-control form-filter input-sm" name="city_search"></td>
                                            <td>
                                                <select name="status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('all')?></option>
                                                    <option value="1"><?php echo $this->lang->line('active')?></option>
                                                    <option value="0"><?php echo $this->lang->line('inactive')?></option>                                                
                                                </select>
                                            </td>
                                            <td>
                                                <select name="enable_hours" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('all')?></option>
                                                    <option value="1"><?php echo $this->lang->line('online')?></option>
                                                    <option value="0"><?php echo $this->lang->line('offline')?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="schedule_mode" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('all')?></option>
                                                    <option value="0"><?php echo $this->lang->line('normal')?></option>
                                                    <option value="1"><?php echo $this->lang->line('busy')?></option>
                                                    <option value="2"><?php echo $this->lang->line('very_busy')?></option>                                                
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm danger-btn theme-btn filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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

<?php //restaurant offline with time :: Start ?>
<div id="onoff_restaurant_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hold_delivery')?></h4>
            </div>
            <div class="modal-body">
                <form id="form_onoff_restaurant" name="form_onoff_restaurant" method="post" class="form-horizontal">
                    <input type="hidden" name="content_id" id="content_id" value="">
                    <input type="hidden" name="rest_status" id="rest_status" value="">
                    <input type="hidden" name="is_online" id="is_online" value="yes">
                    <input type="hidden" name="is_bulk_action" id="is_bulk_action" value="no">
                    <input type="hidden" name="bulk_action" id="bulk_action" value="">
                    <input type="hidden" name="bulk_ids" id="bulk_ids" value="">
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('time')?><span class="required">*</span></label>
                                <div class="col-md-5">
                                    <select name="off_time" id="off_time" class="form-control">
                                        <option value="15" selected="selected">15 minutes</option> 
                                        <option value="30">30 minutes</option> 
                                        <option value="45">45 minutes</option> 
                                        <option value="60">60 minutes</option> 
                                    </select>
                                </div>
                            </div>                            
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-10 col-md-offset-1 text-center">
                                <div id="loadingModal" class="loader-c display-no" >
                                    <img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle" />
                                </div>
                                <button type="submit" class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php //restaurant offline with time :: End ?>

<?php //Restauratn is normal/busy/very busy mode setting :: Start 
$normal_start = $this->common_model->getSystemOptoin('schedule_normal_start');
$normal_end = $this->common_model->getSystemOptoin('schedule_normal_end');
$busy_start = $this->common_model->getSystemOptoin('schedule_busy_start');
$busy_end = $this->common_model->getSystemOptoin('schedule_busy_end');
$verybusy_start = $this->common_model->getSystemOptoin('schedule_verybusy_start');
$verybusy_end = $this->common_model->getSystemOptoin('schedule_verybusy_end');

$time_slotnormal = $normal_start->OptionValue.'-'.$normal_end->OptionValue.' '.$this->lang->line('minutes');
$time_slotbusy = $busy_start->OptionValue.'-'.$busy_end->OptionValue.' '.$this->lang->line('minutes');
$time_slotverybusy = $verybusy_start->OptionValue.'-'.$verybusy_end->OptionValue.' '.$this->lang->line('minutes');
?>
<div id="shedule_restaurant_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('estimate_slotnote')?></h4>
            </div>
            <div class="modal-body">
                <form id="form_shedule_restaurant" name="form_shedule_restaurant" method="post" class="form-horizontal">
                    <input type="hidden" name="shedule_content_id" id="shedule_content_id" value="">                    
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('time')?><span class="required">*</span></label>
                                <div class="col-md-5">
                                    <select name="off_time" id="off_time" class="form-control">
                                        <option value="15" selected="selected">15 minutes</option> 
                                        <option value="30">30 minutes</option> 
                                        <option value="45">45 minutes</option> 
                                        <option value="60">60 minutes</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="form_choose_delivery">
                                <div class="selector-del">
                                    <div class="selecotr-item">

                                        <input type="radio" class="internal_drivers" name="restaurant_schedule_mode" id="restaurant_schedule_mode1" value="0">
                                        <label for="restaurant_schedule_mode1" class="selector-item_label"><?php echo $this->lang->line('normal') ?> <br> <?php echo $time_slotnormal; ?></label>
                                    </div>
                                    <div class="selecotr-item">
                                        <input type="radio" class="thirdparty_delivery" name="restaurant_schedule_mode" id="restaurant_schedule_mode2" value="1">
                                        <label for="restaurant_schedule_mode2" class="selector-item_label"><?php echo $this->lang->line('busy') ?> <br> <?php echo $time_slotbusy; ?></label>
                                    </div>
                                    <div class="selecotr-item">
                                        <input type="radio" class="thirdparty_delivery" name="restaurant_schedule_mode" id="restaurant_schedule_mode3" value="2">
                                        <label for="restaurant_schedule_mode3" class="selector-item_label"><?php echo $this->lang->line('very_busy') ?> <br> <?php echo $time_slotverybusy; ?></label>
                                    </div>
                                </div>
                            </div>          
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-10 col-md-offset-1 text-center">
                                <div id="loadingModal" class="loader-c display-no" >
                                    <img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle" />
                                </div>
                                <button type="submit" class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php //Restauratn is normal/busy/very busy mode setting :: End ?>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script>
var grid;
var res_count = <?php echo ($res_count)?$res_count:0; ?>;
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
            <?php if(in_array('restaurant~ajax_online_offline',$this->session->userdata("UserAccessArray")) || in_array('restaurant~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                { "bSortable": false },
            <?php } ?>
                { "bSortable": false },
                <?php foreach ($Languages as $lang) {?>
                { "bSortable": false },
                <?php } ?>
                { "bSortable": false },
                { "bSortable": false },
                { "bSortable": false },
                { "bSortable": false },
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
                if(oSettings.aoData.length == 0 && res_count != 0 && oData.iStart >= res_count){
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
                  data : {'tblname':'restaurant','entity_id':entity_id,'content_id':content_id},
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
function deleteAll(content_id, message,is_masterdata)
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
                data : {'tblname':'restaurant','content_id':content_id},
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
function disablePage(ID,Status)
{
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
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
                  url : 'ajaxDisable',
                  data : {'entity_id':ID,'status':Status,'tblname':'restaurant'},
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
function disableAll(ContentID,Status,is_masterdata)
{
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
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
                  url : 'ajaxDisableAll',
                  data : {'content_id':ContentID,'status':Status,'tblname':'restaurant'},
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
//Restauratn online/offline with given time period :: Start
function onOffDetails(ID,Status,is_masterdata)
{
    if(Status!=0)
    {
        $('#onoff_restaurant_modal #content_id').val(ID);
        $('#onoff_restaurant_modal #rest_status').val(Status);
        $("#onoff_restaurant_modal #off_time").val($("#off_time option:first").val());
        $('#onoff_restaurant_modal').modal('show');
    }
    else
    {
        var StatusVar = (Status == 0) ? "<?php echo $this->lang->line('restaurant_online'); ?>" : "<?php echo $this->lang->line('restaurant_offline'); ?>";
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
                      url : 'ajax_online_offline',
                      data : {'content_id':ID,'status':Status},
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
    
}
jQuery("#form_onoff_restaurant").validate({
  rules: {
    off_time: {
      required: true
    }
  }
});
$("#form_onoff_restaurant").submit(function(event)
{
    $("#form_onoff_restaurant").validate();
    if (!$("#form_onoff_restaurant").valid()) return false;

    var Status = $('#rest_status').val();
    var form = $("#form_onoff_restaurant").serialize();
    var StatusVar = (Status == 0) ? "<?php echo $this->lang->line('restaurant_online'); ?>" : "<?php echo $this->lang->line('restaurant_offline'); ?>";
    var is_bulk_action = $('#is_bulk_action').val();
    StatusVar = (is_bulk_action == 'yes') ? "<?php echo $this->lang->line('restaurant_offline'); ?>" : StatusVar;
    var action_url = (is_bulk_action == 'yes') ? 'ajax_bulk_online_offline' : 'ajax_online_offline';
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
                  url : action_url,
                  data: form,
                  beforeSend: function(){
                    jQuery('#onoff_restaurant_modal #loadingModal').show();
                  },
                  success: function(response) {
                    jQuery('#onoff_restaurant_modal #loadingModal').hide();
                    $('#onoff_restaurant_modal').modal('hide');
                    grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
    return false;
});
$('#onoff_restaurant_modal').on('hidden.bs.modal', function () {
    $("#form_onoff_restaurant").validate().resetForm();
    $('#onoff_restaurant_modal #content_id').val('');
    $('#onoff_restaurant_modal #rest_status').val('');
    $('#onoff_restaurant_modal #off_time').val('');
});
//Restauratn online/offline with given time period :: End

//activate multiple restaurants
$('#active_res').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        var amount = '0.00';
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");
        bootbox.confirm({
            message: "<?php echo $this->lang->line('active_module'); ?>",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('cancel'); ?>",
                }
            },   
            callback: function (activeConfirm) {  
                if (activeConfirm) {
                    jQuery.ajax({
                      type : "POST",
                      url : 'activeDeactiveMultiRes',
                      data : {'arrayData':CommissionIdComma, 'flag':'active','tab':'res'},
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
    }else{
        bootbox.alert({
            message: "<?php echo $this->lang->line('checkbox'); ?>",
            buttons: {
                ok: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                }
            }
        });
    }        
});
//deactivate multiple restaurants
$('#deactive_res').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        var amount = '0.00';
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");
        bootbox.confirm({
            message: "<?php echo $this->lang->line('deactive_module'); ?>",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('cancel'); ?>",
                }
            },   
            callback: function (deactiveConfirm) {  
                if (deactiveConfirm) {
                    jQuery.ajax({
                      type : "POST",
                      url : 'activeDeactiveMultiRes',
                      data : {'arrayData':CommissionIdComma, 'flag':'deactive','tab':'res'},
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
    }else{
        bootbox.alert({
            message: "<?php echo $this->lang->line('checkbox'); ?>",
            buttons: {
                ok: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                }
            }
        });
    }        
});
//restaurant import
jQuery('#form_add_restaurant_import').validate({
  rules:{
    import_restaurant_file:{
      required:true
    },
    select_timezone:{
      required:true
    },
  },
  errorPlacement: function (error, element) {
      var elm = $(element);
      if(elm.next('p').length > 0){
          error.insertAfter(elm.next('p'));
      }
      else {
          error.insertAfter(elm);
      }
  }
});
//make multiple restaurants online
$('#online_res').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){
        var CommissionIds = Array();
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");

        var StatusVar = "<?php echo $this->lang->line('restaurant_online'); ?>";
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
                      url : 'ajax_bulk_online_offline',
                      data : {'bulk_ids':CommissionIdComma, 'bulk_action':'online'},
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
    }else{
        bootbox.alert({
            message: "<?php echo $this->lang->line('checkbox'); ?>",
            buttons: {
                ok: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                }
            }
        });
    }        
});
//make multiple restaurants offline
$('#offline_res').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");

        $('#onoff_restaurant_modal #is_bulk_action').val('yes');
        $('#onoff_restaurant_modal #bulk_action').val('offline');
        $('#onoff_restaurant_modal #bulk_ids').val(CommissionIdComma);
        $("#onoff_restaurant_modal #off_time").val($("#off_time option:first").val());
        $('#onoff_restaurant_modal').modal('show');
    }else{
        bootbox.alert({
            message: "<?php echo $this->lang->line('checkbox'); ?>",
            buttons: {
                ok: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                }
            }
        });
    }        
});

//Restauratn is normal/busy/very busy mode setting :: Start
function shedulePopup(ID,schedule_mode)
{
    $('#shedule_restaurant_modal #shedule_content_id').val(ID);
    if(schedule_mode==1)
    {
        $("#restaurant_schedule_mode2").prop("checked", true);
    }
    else if(schedule_mode==2)
    {
        $("#restaurant_schedule_mode3").prop("checked", true);
    }
    else
    {
        $("#restaurant_schedule_mode1").prop("checked", true);
    }
    $('#shedule_restaurant_modal').modal('show');
}
jQuery("#form_shedule_restaurant").validate({
  rules: {
    sheduleoff_time: {
      required: true
    }
  }
});
$("#form_shedule_restaurant").submit(function(event)
{
    $("#form_shedule_restaurant").validate();
    if (!$("#form_shedule_restaurant").valid()) return false;
    
    var form = $("#form_shedule_restaurant").serialize();
    bootbox.confirm({
        message: "<?php echo $this->lang->line('estimation_timechange'); ?>",
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
                  url : BASEURL+"backoffice/restaurant/ajax_schedule_mode",
                  data: form,
                  beforeSend: function(){
                    jQuery('#shedule_restaurant_modal #loadingModal').show();
                  },
                  success: function(response) {
                    jQuery('#shedule_restaurant_modal #loadingModal').hide();
                    $('#shedule_restaurant_modal').modal('hide');
                    grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
    
    return false;
});
$('#shedule_restaurant_modal').on('hidden.bs.modal', function () {
    $("#form_shedule_restaurant").validate().resetForm();
    $('#shedule_restaurant_modal #shedule_content_id').val('');    
});
//Restauratn is normal/busy/very busy mode setting :: End
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>