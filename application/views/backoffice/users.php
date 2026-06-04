<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<!-- Embed the intl-tel-input plugin -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
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
                    <?php echo $this->lang->line('manage_customers');?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home')?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                           <?php echo $this->lang->line('customers')?> </a>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <ul id="myTab" class="nav nav-tabs">
                        <li class="<?php echo ($this->uri->segment('4') != 'user_address')?'active':'' ?>"><a href="#user" data-toggle="tab"> <?php echo $this->lang->line('customers')?></a></li>
                        <?php if(in_array('users~view_address',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="<?php echo ($this->uri->segment('4') == 'user_address')?'active':'' ?>"><a href="#address" data-toggle="tab"> <?php echo $this->lang->line('address')?></a></li>
                        <?php } ?>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade <?php echo ($this->uri->segment('4') != 'user_address')?'in active':'' ?>" id="user">
                            <!-- BEGIN VALIDATION STATES-->
                            <div class="page-content-wrapper">
                                <!-- BEGIN PAGE header-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                        <h3 class="page-title">
                                        <?php echo $this->lang->line('customers')?>
                                        </h3>
                                        <!-- END PAGE TITLE & BREADCRUMB-->
                                    </div>
                                </div>  
                                <div class="row">
                                    <div class="col-md-12">    
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet box red">
                                            <div class="portlet-title">
                                                <div class="caption"><?php echo $this->lang->line('customer')?> <?php echo $this->lang->line('list') ?></div>
                                                <div class="actions">
                                                    <?php if(in_array('users~add',$this->session->userdata("UserAccessArray"))) { ?>
                                                        <a class="btn danger-btn btn-sm theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <?php if(isset($_SESSION['page_MSG'])) { ?>
                                                        <div class="alert alert-success">
                                                             <?php echo $_SESSION['page_MSG'];
                                                                unset($_SESSION['page_MSG']); ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="delete-msg" class="alert alert-success hidden success_msg_txt"><?php echo $this->lang->line('success_delete');?></div>
                                                    <table class="table table-striped table-bordered table-hover table-data" id="datatable_ajax">
                                                        <thead>
                                                            <tr role="row" class="heading">
                                                                <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                                <th><?php echo $this->lang->line('customers')?></th>
                                                                <th><?php echo $this->lang->line('phone_number')?></th>
                                                                <th><?php echo $this->lang->line('status')?></th>
                                                                <th><?php echo $this->lang->line('action')?></th>
                                                            </tr>
                                                            <tr role="row" class="filter">
                                                                <td></td>                                       
                                                                <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="phone"></td>
                                                                <td>
                                                                    <select name="Status" class="form-control form-filter input-sm">
                                                                        <option value=""><?php echo $this->lang->line('all')?></option>
                                                                        <option value="1"><?php echo $this->lang->line('active')?></option>
                                                                        <option value="0"><?php echo $this->lang->line('inactive')?></option>
                                                                        <option value="2"><?php echo $this->lang->line('deleted')?></option>                                             
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm danger-btn theme-btn  margin-bottom filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                                    <button class="btn btn-sm danger-btn theme-btn  margin-bottom filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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
                        <?php if(in_array('users~view_address',$this->session->userdata("UserAccessArray"))) { ?>
                            <div class="tab-pane fade <?php echo ($this->uri->segment('4') == 'user_address')?'in active':'' ?>" id="address">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="page-content-wrapper">
                                    <!-- BEGIN PAGE header-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                                            <h3 class="page-title">
                                                <?php echo $this->lang->line('address')?>
                                            </h3>
                                            <!-- END PAGE TITLE & BREADCRUMB-->
                                        </div>
                                    </div>  
                                    <div class="row">
                                        <div class="col-md-12">    
                                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                            <div class="portlet box red">
                                                <div class="portlet-title">
                                                    <div class="caption"><?php echo $this->lang->line('address')?> <?php echo $this->lang->line('list') ?></div>
                                                    <div class="actions">
                                                        <?php if(in_array('users~add_address',$this->session->userdata("UserAccessArray"))) { ?>
                                                            <a class="btn danger-btn btn-sm theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/add_address"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <?php if(isset($_SESSION['add_page_MSG'])) { ?>
                                                            <div class="alert alert-success">
                                                                <?php echo $_SESSION['add_page_MSG'];
                                                                    unset($_SESSION['add_page_MSG']);
                                                                 ?>
                                                            </div>
                                                        <?php } ?>
                                                        <div id="delete-msg" class="alert alert-success hidden">
                                                            <strong>Success!</strong> <?php echo $this->lang->line('success_delete');?>
                                                        </div>
                                                        <table class="table table-striped table-bordered table-hover table-data" id="address_ajax">
                                                            <thead>
                                                                <tr role="row" class="heading">
                                                                    <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                                    <th><?php echo $this->lang->line('customers') ?></th>
                                                                    <th><?php echo $this->lang->line('address') ?></th>
                                                                    <th><?php echo $this->lang->line('action') ?></th>
                                                                </tr>
                                                                <tr role="row" class="filter">
                                                                    <td></td>                                       
                                                                    <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                                                    <td><input type="text" class="form-control form-filter input-sm" name="address"></td>
                                                                    <td>
                                                                        <button class="btn btn-sm danger-btn theme-btn  margin-bottom filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                                        <button class="btn btn-sm danger-btn theme-btn  margin-bottom filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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
                        <?php } ?>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<div id="add_phone_number" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_phone_number') ?></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form id="form_add_phone_number" name="form_add_phone_number" method="post" class="form-horizontal" enctype="multipart/form-data" >
                    <div class="row">
                        <div class="col-sm-12" id="add_phone_number_section">
                            <div class="alert alert-danger display-no" id="err_message"></div>
                            <div class="form-group">
                                <input type="hidden" name="user_id" id="user_id" value="">
                                <input type="hidden" name="user_type" id="user_type" class="form-control" value="">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('phone_number') ?><span class="required">*</span></label>
                                <div class="col-sm-8">
                                    <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
                                    <input type="tel" onblur="checkExist(this.value)" name="mobile_number" id="mobile_number" value="" data-required="1" class="form-control" placeholder=" " maxlength='12'/>
                                    <div class="phn_err" style="display: none; color: #9d0400;"></div>
                                    <div class="phoneExist" style="display: none; color: #9d0400;"></div>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Submit"><?php echo $this->lang->line('submit')  ?></button>
                                    <a class="btn btn-sm default-btn filter-submit margin-bottom" id="close_add_number_modal" ><?php echo $this->lang->line('cancel')  ?></a>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
var grid;
var gridaddress;
var address_count = <?php echo ($address_count)?$address_count:0; ?>;
var user_count = <?php echo ($user_count)?$user_count:0; ?>;
jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    //for address datatable
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
                if(oSettings.aoData.length == 0 && user_count != 0 && oData.iStart >= user_count){
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
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxview", // ajax source
            "aaSorting": [[ 4, "desc" ]] // set first column as a default sort by asc
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('#datatable_ajax input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
    <?php if(in_array('users~view_address',$this->session->userdata("UserAccessArray"))) { ?>
        gridaddress = new Datatable();
        gridaddress.init({
            src: $("#address_ajax"),
            onSuccess: function(gridaddress) {
                // execute some code after table records loaded
            },
            onError: function(gridaddress) {
                // execute some code on network or other general error  
            },
            dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
                "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
               "aoColumns": [
                    { "bSortable": false },
                    null,
                    null,
                    { "bSortable": false }
                  ],
                "sPaginationType": "bootstrap_full_number",
                <?php if($this->session->userdata("language_slug")=='ar'){ ?>
                  "oLanguage":{
                    "sProcessing": '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;جارٍ التحميل...</span>',
                    "sLengthMenu": "أظهر _MENU_ مدخلات",
                    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "sInfoEmpty": '', //"لم يتم العثور على أي سجلات",
                    "sGroupActions": "_TOTAL_ records selected:  ",
                    "sAjaxRequestGeneralError": "لا يمكن إكمال الطلب. الرجاء التحقق من اتصال الانترنت الخاص بك",
                    "sEmptyTable":  "لا توجد بيانات متاحة في الجدول",
                    "sZeroRecords": "لم يتم العثور على سجلات متطابقة",
                    "oPaginate": {
                       "sFirst":    "الأول",
                        "sPrevious": "السابق",
                        "sNext":     "التالي",
                        "sLast":     "الأخير"
                    }
                  },
                 <?php } else if($this->session->userdata("language_slug")=='fr') { ?>
                "oLanguage":{
                    "sProcessing": '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Chargement...</span>',
                    "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
                    "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                    "sInfoEmpty": '', //"Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                    "sGroupActions": "_TOTAL_ records selected:  ",
                    "sAjaxRequestGeneralError": "Impossible de terminer la demande. S'il vous plait, vérifiez votre connexion internet",
                    "sEmptyTable":  "Aucune donn&eacute;e disponible dans le tableau",
                    "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    "oPaginate": {
                       "sFirst":    "Premier",
                        "sPrevious": "Pr&eacute;c&eacute;dent",
                        "sNext":     "Suivant",
                        "sLast":     "Dernier"
                    }
                },
                <?php }else{ ?>
                "oLanguage": {  // language settings 
                    "sProcessing": '<img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Loading...</span>',
                    //"sProcessing": '<img src="' + Metronic.getGlobalImgPath() + 'loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Loading...</span>',
                    "sLengthMenu": "_MENU_ records",
                    "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "sInfoEmpty": '', //"No records found to show",
                    "sGroupActions": "_TOTAL_ records selected:  ",
                    "sAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",
                    "sEmptyTable":  "No data available in table",
                    "sZeroRecords": "No matching records found",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next",
                        "sPage": "Page",
                        "sPageOf": "of"
                    }
                },
                <?php } ?>
                "bStateSave": true,
                "fnStateSave": function (oSettings, oData) {
                    if(oSettings.aoData.length == 0 && address_count != 0 && oData.iStart >= address_count){
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
                    oData.aaSorting = [[ 3, "desc" ]];
                },
                "bServerSide": true, // server side processing
                "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxViewAddress", // ajax source
                "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
            }
        });
        $('#address_ajax_filter').addClass('hide');
        $('#address_ajax input.form-filter, select.form-filter').keydown(function(e) 
        {
            if (e.keyCode == 13) 
            {
                gridaddress.addAjaxParam($(this).attr("name"), $(this).val());
                gridaddress.getDataTable().fnDraw(); 
            }
        });
    <?php } ?>
});
// method for active/deactive 
function disableDetail(entity_id,status,is_masterdata)
{
    if(is_masterdata=='1')
    {
        return false;
    }
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
        }
    });
}
// method for deleting user
function deleteDetail(entity_id)
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
                  data : {'entity_id':entity_id,'table':'users'},
                  success: function(response) {
                    //grid.getDataTable().fnDraw();
                    //gridaddress.getDataTable().fnDraw(); 
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
// method for deleting user address
function deleteAddress(entity_id, message)
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
                  dataType : "html",
                  url : "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/ajaxDeleteAddress",
                  data : {'entity_id':entity_id,'table':'user_address'},
                  success: function(response) {                                        
                    //gridaddress.getDataTable().fnDraw();
                    window.location.href = "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/view/user_address";
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
                });
            }
        }
    });
}
function deleteUser(user_id, message,is_masterdata)
{ 
    if(is_masterdata=='1')    
    {
        return false;
    }
       
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
                url : 'ajaxDelete',
                data : {'entity_id':user_id,'table':'users'},
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
// method for verify user account
function ActiveUserAccount(entity_id,is_masterdata,user_type)
{
    if(is_masterdata=='1')
    {
        return false;
    }
    var StatusVar = "<?php echo $this->lang->line('verify_account_module'); ?>";
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
                  url : 'VerifyAccount',
                  data : {'entity_id':entity_id},
                  success: function(response) {
                        if(response.stat_txt == 'add_phone_number') {
                            //open add phone number modal
                            $('#user_id').val(entity_id);
                            $('#user_type').val(user_type);
                            $("#add_phone_number").modal('show');
                        } else {
                            grid.getDataTable().fnDraw();
                        }
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
$("#close_add_number_modal").click(function(){
    $("#add_phone_number").modal('hide');
});
$('#form_add_phone_number').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        jQuery.ajax({
            type: "POST",
            dataType : "json",
            url: BASEURL+"<?php echo ADMIN_URL ?>/users/save_phone_number",
            data: $('#form_add_phone_number').serialize(),
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(response) {
                $('#quotes-main-loader').hide();
                var user_entity_id = $('#user_id').val();
                var user_type_val = $('#user_type').val();
                if(response.success_msg){
                    $('#err_message').hide();
                    $('#add_phone_number_section').hide();
                    $('.success_msg_txt').html(response.success_msg);
                    $('.success_msg_txt').show();
                    $("#add_phone_number").modal('hide');
                    $('#add_phone_number_section').show();
                    jQuery.ajax({
                        type : "POST",
                        dataType : "json",
                        url : BASEURL+"<?php echo ADMIN_URL ?>/users/VerifyAccount",
                        data : {'entity_id':user_entity_id},
                        success: function(response) {
                            if(response.stat_txt == 'add_phone_number') {
                                //open add phone number modal
                                $('#user_id').val(user_entity_id);
                                $('#user_type').val(user_type_val);
                                $("#add_phone_number").modal('show');
                            } else {
                                grid.getDataTable().fnDraw();
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {           
                            alert(errorThrown);
                        }
                    });
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
$('#add_phone_number').on('hidden.bs.modal', function () {
    $('#mobile_number').val('');
    $('#phone_code').val('');
    $('#user_id').val('');
    $('#user_type').val('');
    $('.phoneExist').css('display','none');
    $(':input[type="submit"]').prop("disabled",false);
    if($('.success_msg_txt').is(':visible')) {
        setTimeout(function(){
            $(".success_msg_txt").hide();
        }, 5000);
    }
    $('#form_add_phone_number').validate().resetForm();
});
//check phone number exist
function checkExist(mobile_number,is_masterdata){
    var entity_id = $('#user_id').val();
    var phone_code = $('#phone_code').val();
    var user_type = $('#user_type').val();
    $.ajax({
        type: "POST",
        url: BASEURL+"<?php echo ADMIN_URL ?>/users/checkExist",
        data: 'mobile_number=' + mobile_number +'&entity_id='+entity_id+'&phone_code='+phone_code+'&selected_role_name='+user_type,
        cache: false,
        success: function(html) {
            if(html > 0){
                $('.phoneExist').show();
                $('.phoneExist').html("<?php echo $this->lang->line('phone_exist'); ?>");
                $(':input[type="submit"]').prop("disabled",true);
            } else {
                $('.phoneExist').html("");
                $('.phoneExist').hide();
                $(':input[type="submit"]').prop("disabled",false);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('.phoneExist').show();
            $('.phoneExist').html(errorThrown);
        }
  });
}
var onedit_iso = '';
<?php if(isset($phone_code)) {
    $onedit_iso = $this->common_model->getIsobyPhnCode($phone_code); ?>
    onedit_iso = <?php echo json_encode($onedit_iso); ?>;
<?php } 
$iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>
var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
var initial_preferred_iso = (onedit_iso)?onedit_iso:default_iso;
// Initialize the intl-tel-input plugin
const phoneInputField = document.querySelector("#mobile_number");
const phoneInput = window.intlTelInput(phoneInputField, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    formatOnDisplay:false,
    autoPlaceholder:"polite",
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js'
    //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
});
phoneInputField.addEventListener("close:countrydropdown",function() {
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});
$(document).on('input','#mobile_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});
$(document).on('focusout','#mobile_number',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number').val(phoneNumber);
    }
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>