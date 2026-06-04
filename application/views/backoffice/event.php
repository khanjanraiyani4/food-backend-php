<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/daterangepicker/css/daterangepicker.css" />
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
                        <?php echo $this->lang->line('admin_event_booking')?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home')?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('admin_event_booking')?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <?php if(in_array('event~generate_report',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo $this->lang->line('export_report') ?></div>
                            </div>
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <?php                                         
                                    if(isset($_SESSION['not_found']))
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['not_found'];
                                             unset($_SESSION['not_found']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL ?>/event/generate_report" id="event_generate_report" name="event_generate_report" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('restaurant') ?><span class="required">*</span></label>
                                                    <select name="restaurant_id[]" multiple="multiple" id="restaurant_id" class="form-control required sumo">
                                                        <?php if(!empty($restaurant)){
                                                        foreach ($restaurant as $key => $value) { ?>
                                                             <option value="<?php echo $value->content_id ?>"><?php echo $value->name ?></option>
                                                        <?php  } } ?>                           
                                                    </select> 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('booking_date') ?></label>
                                                    <input type="text" class="form-control date-picker" readonly name="booking_date_export" id="booking_date_export" placeholder="<?php echo $this->lang->line('booking_date') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" name="submitPage" id="submitPage" value="Generate" class="btn btn-success default-btn btn-genrate danger-btn theme-btn"><i class="fa fa-download"></i> <?php echo $this->lang->line('download') ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            <?php } ?>
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('events')?> <?php echo $this->lang->line('list')?></div>
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
                                            <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                            <th><?php echo $this->lang->line('customer')?></th>
                                            <th><?php echo $this->lang->line('restaurant')?></th>
                                            <th><?php echo $this->lang->line('no_of_people')?></th>
                                            <th><?php echo $this->lang->line('event_date')?></th>
                                            <th><?php echo $this->lang->line('package')?></th>
                                            <th><?php echo $this->lang->line('amount')?></th>
                                            <th><?php echo $this->lang->line('payment_status')?></th>
                                            <!-- <th><?php //echo $this->lang->line('status')?></th> -->
                                            <th><?php echo $this->lang->line('action')?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>     
                                            <td><input type="text" class="form-control form-filter input-sm" name="user_name"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="no_of_people"></td>
                                            <td><input type="text" class="form-control form-filter input-sm booking-date-picker" name="booking_date" id="booking_date"  placeholder="<?php echo $this->lang->line('select_date') ?>"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="package"></td>
                                            <td></td>
                                            <td> 
                                                <select name="event_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $event_status = event_status($this->session->userdata('language_slug'));
                                                    foreach ($event_status as $key => $value) { ?>
                                                         <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                                    <?php  } ?>                           
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search')?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm red filter-cancel" title="<?php echo $this->lang->line('reset')?>"><i class="fa fa-refresh"></i></button>
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
<!-- Modal -->
<div id="add_amount" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('add')?> <?php echo $this->lang->line('amount')?></h4>
      </div>
      <div class="modal-body">
        <form id="form_add_amount" name="form_add_amount" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                <div class="form-group">
                  <label class="control-label col-md-4"><?php echo $this->lang->line('amount')?> <span class="currency-symbol"></span><span class="required">*</span></label>
                  <div class="col-sm-8">
                    <input type="number" class="form-control format-val" name="subtotal" id="subtotal" value="" maxlength="10" onfocusout="calculateSubtotal(this.value)" autocomplete="off" min="0"/>
                  </div>
                </div>  
                <div class="form-group">
                    <label class="control-label col-md-4"><?php echo $this->lang->line('discount')?><span class="currency-symbol"></span></label>
                    <div class="col-md-8">
                        <input type="hidden" name="entity_id" id="entity_id" value="">
                        <input type="hidden" name="invoice" id="invoice" value="">
                        <input type="number" data-value="" name="coupon_amount" id="coupon_amount" value="" maxlength="10" data-required="1" class="form-control" onfocusout="calculateDiscountSubtotal(this.value)" autocomplete="off" min="0"/><label class="coupon-type"></label>
                    </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-4"><?php echo $this->lang->line('total')?> <span class="currency-symbol"></span></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control format-val" name="amount" id="amount" value="" maxlength="10" readonly="" />
                  </div>
                </div>
                <div class="form-actions fluid">
                    <div class="col-md-12 text-center">
                     <div id="loadingModal" class="loader-c display-no" ><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                     <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                    </div>
                </div>
            </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="add_status" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('update_status')?></h4>
      </div>
      <div class="modal-body">
        <form id="form_event_status" name="form_event_status" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="event_entity_id" id="event_entity_id" value="">
                        <input type="hidden" name="event_invoice" id="event_invoice" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('status')?><span class="required">*</span></label>
                        <div class="event-status col-sm-8">
                            <select name="event_status" id="event_status" onchange="showCancelReason(this.value)" class="form-control form-filter input-sm">
                                <option value=""><?php echo $this->lang->line('select')?></option>
                                <option value="pending"><?php echo $this->lang->line('pending')?></option>
                                <option value="paid"><?php echo $this->lang->line('paid')?></option>        
                                <option value="cancel"><?php echo $this->lang->line('cancel')?></option>                                           
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-group cancel_reason"  style="display: none;">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('cancel_reason') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="cancel_reason" maxlength="70" id="cancel_reason" placeholder=" "></textarea>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c" style="display: none;"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div id="view_additional_request" class="modal fade" role="dialog"></div>
<div class="wait-loader display-no" id="quotes-main-loader" ><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<!-- daterangepicker(start) -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/daterangepicker/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/daterangepicker/js/daterangepicker.min.js"></script>
<!-- daterangepicker(end) -->
<script>
    var daterangepicker_format = "<?php echo daterangepicker_format; ?>";
$(function() {
  $('.date-picker').daterangepicker({
        opens: 'left',
        startDate: moment(),
        endDate: moment().add(7, 'day'),
        locale: {
          format: daterangepicker_format
        }
    }, function(start, myDate, label) {
        //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + myDate.format('YYYY-MM-DD'));
    });
});
var grid;
var event_count = <?php echo ($event_count)?$event_count:0; ?>;
jQuery(document).ready(function() {
    $('.booking-date-picker').daterangepicker({
        opens: 'left',
        startDate: moment().subtract(10, 'day'),
        endDate: moment(),
        locale: {
          format: daterangepicker_format
        }
    }, function(start, myDate, label) {
    });
    
    $('.booking-date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $('.booking-date-picker').val('');
    });
    
    Layout.init(); // init current layout 
    //Added on 19-10-2020   
    $('.sumo').SumoSelect({selectAll:true,  captionFormatAllSelected: '{0} <?php echo $this->lang->line('selected');?>!',locale: ['OK', 'Cancel', "<?php echo $this->lang->line('all').' '.$this->lang->line('select_');?>"], search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
    
    $("html").on('click', '.select-all', function(){
        var myObj = $(this).closest('.SumoSelect.open').children()[0];
        if ($(this).hasClass("selected")) {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", true);
            $(myObj)[0].sumo.selectAll();
            $(this).parent().find("ul.options>li").addClass("selected");
        }
        else {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", false);
            $(myObj)[0].sumo.unSelectAll();
            $(this).parent().find("ul.options>li").removeClass("selected");
        }
    });
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
                { "bSortable": false },
                { "bSortable": false },
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
                if(oSettings.aoData.length == 0 && event_count != 0 && oData.iStart >= event_count){
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
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/ajaxview/all/<?php echo $event_id ?>", // ajax source
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
//update status
function updateStatus(entity_id,event_status){
    $('#event_entity_id').val(entity_id);
    if(event_status == 'pending'){
        $('#event_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="paid"><?php echo $this->lang->line('paid'); ?></option><option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
        );
    }else if(event_status == 'paid'){
        $('#event_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
        );
    } else if(event_status == 'cancel'){
        $('#event_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'
        );
    }
    $('#event_status').val('');
    $('#cancel_reason').val('');
    $(".cancel_reason").css("display", "none");
    $('#add_status').modal('show');
}
jQuery("#form_event_status").validate({  
  rules: {    
    event_status: {
      required: true
    },
    cancel_reason:{
        required: {
        depends: function(){
          if($('#event_status').val() == 'cancel'){
              return true;
          }
        }
      },
      maxlength: 70,
    }
  }  
});
$('#form_event_status').submit(function(){
    $("#form_event_status").validate();
    if (!$("#form_event_status").valid()) { 
        return false;
    } else {
        $.ajax({
          type: "POST",
          dataType : "html",
          url: BASEURL+"backoffice/event/updateEventStatus",
          data: $('#form_event_status').serialize(),
          cache: false, 
          beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) {
            $('#quotes-main-loader').hide();
            $('#add_status').modal('hide');
            $('#event_status').val('');
            $('#cancel_reason').val('');
            $(".cancel_reason").css("display", "none");
            grid.getDataTable().fnDraw();
          }
        });
        return false;
    }
    
});
function showCancelReason(event_status){
    if(event_status == 'cancel'){
        $(".cancel_reason").css("display", "block");
    } else {
        $(".cancel_reason").css("display", "none");
    }
}
// method for deleting
function deleteDetail(entity_id, message)
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
        }
    });
}
//add amount
function addAmount(entity_id,tax,coupon,tax_type,coupon_type){
    $('#add_amount #entity_id').val(entity_id);
    coupon = (coupon == 0)?'':coupon;
    //$('#add_amount #coupon_amount').val(coupon);
    coupon_type = (coupon_type == 'Percentage')?'%':'';
    $('#add_amount .coupon-type').html(coupon_type);
    $('#add_amount').modal('show');
    getEventCurrency(entity_id);
}
//submit add amount form
$("#form_add_amount").submit(function(event) {
    $("#form_add_amount").validate();
    if (!$("#form_add_amount").valid()) return false;
    var url = BASEURL+"backoffice/event/addAmount";
    var form = $("#form_add_amount").serialize();
    $.ajax({
      type: "POST",
      url: url,
      data: form,
      dataType: 'json',
      beforeSend: function(){
        jQuery('#add_amount #loadingModal').show();
      },
      success: function(html) {
        jQuery('#add_amount #loadingModal').hide();
        grid.getDataTable().fnDraw(); 
        $('#add_amount').modal('hide');
      }
    });
    return false;
});
function calculation(sum){
    //tax
    var amount = $('#coupon_amount').val(); 
    var type = $('.coupon-type').html();
    //coupon
    if(type == 'Percentage' && amount != '' && amount != 0){
        var cpn = (sum*amount)/100;
        sum = sum - cpn;
    }else if(type == 'Amount' && amount != '' && amount != 0){
        sum = sum - amount;
    }
    if(!isNaN(sum)){
        $('#amount').val(sum);
    }else{
        $('#amount').val(0);
    }
}
$('#add_amount').on('hidden.bs.modal', function () {
    $(".modal-dialog .form-control").removeClass("error");
    $(".modal-dialog label.error").remove();
    $('#form_add_amount option').prop('selected', false);
    $('#form_add_amount input').val('');
});
function calculateSubtotal(sum){
    //tax calculateDiscountSubtotal
    sum=parseFloat(sum);
    var amount = $('#coupon_amount').val();
    amount = parseFloat(amount);
    $('#coupon_amount').attr('max',sum);
    if(sum<=amount){
        $('#coupon_amount').val(sum);
    }else if(isNaN(sum)){
       $('#coupon_amount').val(0); 
    }
    if(amount != '' && amount != 0){
        if(!isNaN(amount)){
            sum = sum - amount;
        }
    }
    if(!isNaN(sum) && sum >= 0){
        $('#amount').val(sum);
    }else{
        $('#amount').val(0);
    }
}
function calculateDiscountSubtotal(sum){
    sum=parseFloat(sum);
    var amount = $('#subtotal').val();
    amount = parseFloat(amount);
    if($('#amount').val()<=0){
        $('#coupon_amount').attr('max',amount);
    }
    if(amount != '' && amount != 0){
        if(!isNaN(sum)){
            sum = amount - sum;
        }else{
            sum = amount;
        }
    }
    if(!isNaN(sum) && sum >= 0){
        $('#amount').val(sum);
    }else{
        $('#amount').val(0);
    }
}
function viewAdditionalRequest(entity_id){
    jQuery.ajax({
      type : "POST",                      
      url : BASEURL+"backoffice/event/viewAdditionalRequest",
      data : {'entity_id':entity_id},
      cache: false,
      success: function(response) {
        $('#view_additional_request').html(response);
        $('#view_additional_request').modal('show');      
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>