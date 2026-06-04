<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
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
                    <?php echo $this->module_name;?>
                    </h3>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->module_name;?></div>                            
                            <div class="actions">
                                <a class="btn default-btn btn-sm" data-toggle="modal" data-target="#addnotificaton"><i class="fa fa-plus"></i> Add Notification</a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                            <div class="alert alert-success" id="notification-alert" style="display: none;"><strong>Success! </strong><span id="notification-alert-msg"></span></div>                            
                            <?php
                            if($_SESSION['PageMSG'])
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['PageMSG'];
                                     unset($_SESSION['PageMSG']);
                                     ?>
                                </div>
                            <?php } ?>
                            <div id="delete-msg" class="alert alert-success hidden">
                                <strong>Success!</strong> <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">&nbsp;#&nbsp;</th>
                                            <th>Message</th>                                           
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Button Label</th>                                            
                                            <th>Action</th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>                                       
                                            <td><input type="text" class="form-control form-filter input-sm" name="message"></td>
                                            <td>
                                                <div class="input-group date margin-bottom-5 input-daterange">
                                                    <input type="text" class="form-control form-filter input-sm date-picker" readonly name="from_date" placeholder="From" id="from_date_s">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group date margin-bottom-5 input-daterange">
                                                    <input type="text" class="form-control form-filter input-sm date-picker" readonly name="to_date" placeholder="To" id="to_date_s">
                                                </div>
                                            </td>                                            
                                            <td><input type="text" class="form-control form-filter input-sm" name="button_label"></td>
                                            <td><div class="margin-bottom-5">
                                                    <button class="btn btn-sm  default-btn filter-submit margin-bottom"><i class="fa fa-search"></i> Search</button>
                                                </div>
                                                <button class="btn btn-sm default-btn filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
<div id="approvexpense" class="modal fade" tabindex="-1" data-width="900">
    <div class="modal-dialog">
        <form>
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Approve</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                       Are you sure you want to approve?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger default-btn" data-dismiss="modal">Aprpove</button>
            </div>
        </div>
        </form>
    </div>
</div>
<div id="rejectexpense" class="modal fade" tabindex="-1" data-width="900">
    <div class="modal-dialog">
        <form>
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Reject</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Reason</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">                                                
                            <input type="text" name="emp_name" id="emp_name" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger default-btn" data-dismiss="modal">Reject</button>
            </div>
        </div>
        </form>
    </div>
</div>
<div id="addnotificaton" class="modal fade" tabindex="-1" data-width="600">
    <div class="modal-dialog">
        <form id="form_add_notification" name="form_add_notification" action="" method="post" class="isautovalid">
        <input type="hidden" name="alert_id" id="alert_id">
        <div class="modal-content">
           
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">Add Notification</div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body"> 
                            <div class="error_div alert-danger"></div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="control-label col-md-4">Message<span class="required">*</span></label>
                                    <div class="col-md-8">
                                        <textarea name="message" id="message" class="form-control required"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="control-label col-md-4">Date<span class="required">*</span></label>
                                    <div class="col-md-8">
                                        <div class="input-group input-large date-picker input-daterange">
                                            <input type="text" class="form-control required" name="from_date" id="from_date" readonly="">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control required" name="to_date" id="to_date" readonly="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="control-label col-md-4">Button Label<span class="required">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="button_label" id="button_label">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" name="submitPage" id="submitPage" value="Submit" class="btn btn-danger default-btn">  
            </div>
        </div>
        </form>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script>
$('#from_date').datepicker({               
    autoclose:true,
    format: "dd-M-yyyy"
}).on('changeDate', function(){      
    var date = changeDateFormat($(this).val());     
    $('#to_date').datepicker('setStartDate', new Date(date));
}); 
$('#to_date').datepicker({
    autoclose:true,
    format: "dd-M-yyyy"
}).on('changeDate', function(){
    var date = changeDateFormat($(this).val());    
    $('#from_date').datepicker('setEndDate', new Date(date));
});   
$('#from_date_s').datepicker({               
    autoclose:true,
    format: "dd-M-yyyy"
}).on('changeDate', function(){  
    var date = changeDateFormat($(this).val());           
    $('#to_date_s').datepicker('setStartDate', new Date(date));
}); 
$('#to_date_s').datepicker({
    autoclose:true,
    format: "dd-M-yyyy"
}).on('changeDate', function(){ 
    var date = changeDateFormat($(this).val());        
    $('#from_date_s').datepicker('setEndDate', new Date(date));
});   
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
                null,
                null,
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            "oLanguage": {  // language settings
                "sProcessing": '<img src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Loading...</span>',
                "sLengthMenu": "_MENU_ records",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoEmpty": "No records found to show",
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
            "bServerSide": true, // server side processing
            "sAjaxSource": "admin_alerts/ajaxview", // ajax source
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
//add Notification
$('#form_add_notification').submit(function(){
    if(! $('#form_add_notification').valid()) return false;
    var alert_id = $('#alert_id').val();
    jQuery.ajax({
        dataType : "html",
        type: 'POST',
        url : "admin_alerts/add",
        data : $('#form_add_notification').serialize(),
        beforeSend: function() {
            $('#loading-image').show(); 
        },
        success: function(response) { 
            if (alert_id != '') {
                $('#notification-alert-msg').text("<?php echo $this->lang->line('success_update'); ?>");
                $('#notification-alert').show();
            }
            else
            {
                $('#notification-alert-msg').text("<?php echo $this->lang->line('success_add'); ?>");
                $('#notification-alert').show();
            }
            $('#loading-image').hide(); 
            if(response == 'success'){
                $('#addnotificaton').modal('hide');
                grid.getDataTable().fnDraw(); 
            }else{
                $('.error_div').html(response);
            }
        },
        error: function(XMLHttpRequest, textstatus, errorThrown) {           
            alert(errorThrown);
        }
    });
    return false;
});
//clear add notification popup data
$('#addnotificaton').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=text],input[type=hidden],textarea,select").val('').end();
  $("#message").text('');
  $('#form_add_notification').validate().resetForm();
  //$('select.branch_id')[0].sumo.unSelectAll();
  $(".error_div").html('');
});
function  downloadPolicy(expense_id) {
    bootbox.confirm("Are you sure you want to download policy?", function(disableConfirm) { 
        if (disableConfirm) {   
        }
    });
}
function editNotification(alert_id){
    jQuery.ajax({
        dataType : "json",
        type: 'POST',
        url : "admin_alerts/edit",
        data : {"alert_id":alert_id},
        beforeSend: function() {
            $('#loading-image').show(); 
        },
        success: function(response) {
            $('#loading-image').hide(); 
            $("#alert_id").val(response.alert_id);
            $("#branch_id").val(response.branch_id);
            $("textarea#message").val(response.message);
            $("#from_date").val(response.from_date);
            $('#from_date').datepicker("setDate", new Date(response.from_date) );
            $("#to_date").val(response.to_date);
            $('#to_date').datepicker("setDate", new Date(response.to_date) );
            $("#button_label").val(response.button_label);
            $('#addnotificaton').modal('show');
        },
        error: function(XMLHttpRequest, textstatus, errorThrown) {           
            alert(errorThrown);
        }
    });
    return false;     
}
function changeDateFormat(value){
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];
    var dateSelected = value.split('-');
    var month = months.indexOf(dateSelected[1]);
    month = month ? month + 1 : 0;
    var day = dateSelected[0];
    var month = (month < 10)?"0" + month:month;
    var year = ''+dateSelected[2]+'';
    year = year.replace('-','');
    var date = year + "-" + month + "-" + day;
    return date;
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>