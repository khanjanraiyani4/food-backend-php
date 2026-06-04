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
                        <?php echo $this->lang->line('table_bookings')?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home')?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('table_bookings')?>
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
                            <div class="caption"><?php echo $this->lang->line('table_booking')?> <?php echo $this->lang->line('list')?></div>
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
                                            <th><?php echo $this->lang->line('customer')?></th>
                                            <th><?php echo $this->lang->line('restaurant')?></th>
                                            <th><?php echo $this->lang->line('no_of_people')?></th>
                                            <th><?php echo $this->lang->line('booking_date')?></th>
                                            <?php /* ?><th><?php echo $this->lang->line('amount')?></th><?php */ ?>
                                            <?php /* ?><th><?php echo $this->lang->line('payment_status')?></th><?php */ ?>
                                            <th><?php echo $this->lang->line('booking_status')?></th>
                                            <th><?php echo $this->lang->line('action')?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="user_name"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="no_of_people"></td>
                                            <td><input type="text" class="form-control form-filter input-sm booking-date-picker" name="booking_date" id="booking_date"></td>
                                            <?php /* ?><td></td><?php */ ?>
                                            <?php /* ?><td> 
                                                <select name="payment_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $event_status = event_status($this->session->userdata('language_slug'));
                                                    foreach ($event_status as $key => $value) { ?>
                                                         <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                                    <?php  } ?>                           
                                                </select>
                                            </td><?php */ ?>
                                            <td> 
                                                <select name="booking_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $book_status = booking_status($this->session->userdata('language_slug'));
                                                    foreach ($book_status as $book_key => $book_value) { ?>
                                                         <option value="<?php echo $book_key ?>"><?php echo $book_value; ?></option>
                                                    <?php  } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" title="<?php echo $this->lang->line('search')?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm danger-btn theme-btn filter-cancel" title="<?php echo $this->lang->line('reset')?>"><i class="fa fa-refresh"></i></button>
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
<?php /* ?>
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
                    <input type="number" class="form-control format-val" name="subtotal" id="subtotal" value="" maxlength="10" onkeyup="calculateSubtotal(this.value)" min="0"/>
                  </div>
                </div>  
                <div class="form-group">
                    <label class="control-label col-md-4"><?php echo $this->lang->line('discount')?><span class="currency-symbol"></span></label>
                    <div class="col-md-8">
                        <input type="hidden" name="entity_id" id="entity_id" value="">
                        <input type="hidden" name="invoice" id="invoice" value="">
                        <input type="number" data-value="" name="coupon_amount" id="coupon_amount" value="" maxlength="10" data-required="1" class="form-control" onkeyup="calculateDiscountSubtotal(this.value)" min="0"/><label class="coupon-type"></label>
                    </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-4"><?php echo $this->lang->line('total')?> <span class="currency-symbol"></span><span class="required">*</span></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control format-val" name="amount" id="amount" value="" maxlength="10" readonly="" />
                  </div>
                </div>
                <div class="form-actions fluid">
                    <div class="col-md-12 text-center">
                     <div id="loadingModal" class="loader-c display-no" ><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                     <button type="submit" class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                    </div>
                </div>
            </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php */ ?>
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
        <form id="form_table_status" name="form_table_status" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="table_entity_id" id="table_entity_id" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('status')?><span class="required">*</span></label>
                        <div class="event-status col-sm-8">
                            <select name="table_booking_status" id="table_booking_status" onchange="showCancelReason(this.value)" class="form-control form-filter input-sm">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php $book_status = booking_status($this->session->userdata('language_slug'));
                                foreach ($book_status as $book_key => $book_value) { ?>
                                     <option value="<?php echo $book_key ?>"><?php echo $book_value; ?></option>
                                <?php  } ?>
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
                         <button type="submit" class="btn btn-sm  danger-btn theme-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
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
$(function() {
  $('.date-picker').daterangepicker({
        opens: 'left',
        startDate: moment(),
        endDate: moment().add(7, 'day'),
    }, function(start, myDate, label) {
    });
});
var grid;
jQuery(document).ready(function() {
    $('.booking-date-picker').daterangepicker({
        opens: 'left',
        startDate: moment().subtract(10, 'day'),
        endDate: moment(),
    }, function(start, myDate, label) {
    });
    $('.booking-date-picker').val('');
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
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/ajaxview", // ajax source
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
function updateStatus(entity_id,table_booking_status){
    $('#table_entity_id').val(entity_id);
    if(table_booking_status == 'awaiting'){
        $('#table_booking_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="confirmed"><?php echo $this->lang->line('confirmed'); ?></option><option value="cancelled"><?php echo $this->lang->line('cancelled'); ?></option>'
        );
    }else if(table_booking_status == 'confirmed'){
        $('#table_booking_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="cancelled"><?php echo $this->lang->line('cancelled'); ?></option>'
        );
    }else if(table_booking_status == 'cancelled'){
        $('#table_booking_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'
        );
    }
    $('#table_booking_status').val('');
    $('#cancel_reason').val('');
    $(".cancel_reason").css("display", "none");
    $('#add_status').modal('show');
}
jQuery("#form_table_status").validate({  
  rules: {    
    table_booking_status: {
      required: true
    },
    cancel_reason:{
        required: {
        depends: function(){
          if($('#table_booking_status').val() == 'cancelled'){
              return true;
          }
        }
      },
      maxlength: 70,
    }
  }  
});
$('#form_table_status').submit(function(){
    $("#form_table_status").validate();
    if (!$("#form_table_status").valid()) { 
        return false;
    } else {
        $.ajax({
          type: "POST",
          dataType : "html",
          url: BASEURL+"backoffice/book_table/updateTableStatus",
          data: $('#form_table_status').serialize(),
          cache: false, 
          beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) {
            $('#quotes-main-loader').hide();
            $('#add_status').modal('hide');
            $('#table_booking_status').val('');
            $('#cancel_reason').val('');
            $(".cancel_reason").css("display", "none");
            grid.getDataTable().fnDraw();
          }
        });
        return false;
    }
    
});
function showCancelReason(table_booking_status){
    if(table_booking_status == 'cancelled'){
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
/*function addAmount(entity_id,tax,coupon,tax_type,coupon_type){
    $('#add_amount #entity_id').val(entity_id);
    coupon = (coupon == 0)?'':coupon;
    $('#add_amount #coupon_amount').val(coupon);
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
    var amount = $('#coupon_amount').val(); 
    $('#coupon_amount').attr('max',sum);
    if(sum<=amount){
        $('#coupon_amount').val(sum);
    }
    if(amount != '' && amount != 0){
        sum = sum - amount;
    }
    if(!isNaN(sum)){
        $('#amount').val(sum);
    }else{
        $('#amount').val(0);
    }
}
function calculateDiscountSubtotal(sum){
    var amount = $('#subtotal').val(); 
    if($('#amount').val()<=0){
        $('#coupon_amount').attr('max',amount);
    }
    if(amount != '' && amount != 0){
        sum = amount - sum;
    }
    if(!isNaN(sum) && sum >= 0){
        $('#amount').val(sum);
    }else{
        $('#amount').val(0);
    }
}*/
$(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>