<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/daterangepicker/css/daterangepicker.css" />
<style type="text/css">
   .page_refresh, .dien-in{
        position: relative;
    }
    .dien-in .notify{
        position: absolute;
        top: -10px;
        color: #fff;
        background: green;
        height: 15px;
        border-radius: 50% !important;
        display: inline-block;
        right: -4px;
        width: 15px;
        text-align: center;
    }
    .page_refresh{
       animation: blink 1s steps(1, end) infinite;
    }
    .blink {
  animation: blink 1s steps(1, end) infinite;
}
@keyframes blink {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
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
                        <?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <?php //New export code set as per required :: Start :: 25-01-2021 ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <!-- export order 15-01-2021 vip.. start -->
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('export_order') ?></div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">                                 
                                    <?php
                                    if($_SESSION['not_found'])
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['not_found'];
                                             unset($_SESSION['not_found']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/export_order" id="export_order" name="export_order" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('restaurant') ?><span class="required">*</span></label>
                                                <select name="restaurant_id" id="restaurant_id" class="form-control sumo required">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="all"><?php echo $this->lang->line('all') ?></option>
                                                    <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                         <option value="<?php echo $value->entity_id ?>"><?php echo $value->name ?></option>
                                                    <?php  } } ?>                           
                                                </select> 
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('order_type') ?><span class="required">*</span></label>
                                                <select name="order_delivery" class="form-control sumo">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="DineIn" selected><?php echo $this->lang->line('dinein') ?></option>
                                                </select> 
                                            </div>
                                        </div> -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden"  name="order_delivery" id="order_delivery" value="DineIn">
                                                <label class="control-label"><?php echo $this->lang->line('from_date') ?></label>
                                                <input type="text" class="form-control date-picker"readonly name="start_date" id="start_date" placeholder="<?php echo $this->lang->line('from') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo $this->lang->line('to_date') ?></label>
                                                <input type="text" class="form-control date-picker" readonly name="end_date" id="end_date" placeholder="<?php echo $this->lang->line('to') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" style="position: absolute;top: 30px;" name="submitPage" id="submitPage" value="Generate" class="btn btn-success default-btn danger-btn theme-btn"><i class="fa fa-download"></i> <?php echo $this->lang->line('download') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- export order 15-01-2021 vip.. end -->
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <?php //New export code set as per required :: End :: 25-01-2021 ?>
                        
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('orders') ?> <?php echo $this->lang->line('list') ?></div>
                            <div class="actions">
                                <button class="btn default-btn btn-sm danger-btn theme-btn page_refresh" id="page_refresh"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo $this->lang->line('refresh') ?></button>
                                <?php //if require open this ?>
                                <!-- <button class="btn default-btn btn-sm" id="exportSelectedOrder"><i class="fa fa-download"></i> <?php echo $this->lang->line('export_order') ?></button> -->
                                <a class="btn default-btn btn-sm danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/dinein_add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <button class="btn default-btn btn-sm danger-btn theme-btn" id="bluk_payment_update"><i class="fa fa-dollar"></i> <?php echo $this->lang->line('payment') ?></button>
                                <button class="btn default-btn btn-sm danger-btn theme-btn" id="delete_order"><i class="fa fa-times"></i> <?php echo $this->lang->line('delete') ?></button>
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
                            <?php
                            if($_SESSION['page_Error'])
                            { ?>
                                <div class="alert alert-danger">
                                     <?php echo $_SESSION['page_Error'];
                                     unset($_SESSION['page_Error']);
                                     ?>
                                </div>
                            <?php } ?>
                            <div id="delete-msg" class="alert alert-success hidden">
                                 <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover table-data" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <th><?php echo $this->lang->line('order') ?>#</th>
                                            <th><?php echo $this->lang->line('restaurant') ?>/<?php echo $this->lang->line('branch') ?></th>
                                            <th><?php echo $this->lang->line('customer') ?></th>
                                            <th><?php echo $this->lang->line('order_total') ?></th>
                                            <th><?php echo $this->lang->line('order_status') ?></th>
                                            <th><?php echo $this->lang->line('order_date') ?></th>
                                            <th><?php echo $this->lang->line('payment_status') ?></th>
                                            <th><?php echo $this->lang->line('pay_type') ?></th>
                                            <th><?php echo $this->lang->line('order_actions') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>  
                                            <td><input type="text" class="form-control form-filter input-sm" name="order"></td>                                
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>                                    
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="order_total"></td>
                                            <td>
                                                <select name="order_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $order_status = dinein_order_status($this->session->userdata('language_slug'));
                                                    foreach ($order_status as $key => $value) { ?>
                                                         <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php  } ?>                                   
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm order-date-picker" name="order_date" id="order_date">
                                            </td>
                                            <td>
                                                <select name="payment_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="paid"><?php echo $this->lang->line('paid') ?></option> 
                                                    <option value="unpaid"><?php echo $this->lang->line('unpaid') ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="admin_payment_option" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="cash"><?php echo $this->lang->line('cash_word') ?></option> 
                                                    <option value="card"><?php echo $this->lang->line('card_word') ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm red filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
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
<!-- Modal -->
<div id="add_status" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('update_status') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="entity_id" id="entity_id" value="">
                        <input type="hidden" name="invoice" id="invoice" value="">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <input type="hidden" name="order_type" id="order_type" value="">
                        <input type="hidden" name="order_statusval" id="order_statusval" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('status') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="order_status" id="order_status" class="form-control form-filter input-sm" required>
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php $order_status = dinein_order_status($this->session->userdata('language_slug'));
                                foreach ($order_status as $key => $value) { ?>
                                     <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php  } ?>                            
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-group cancel-reason" style="display:none;">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('cancel_reason') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="cancel_reason" id="cancel_reason" class="form-control input-sm">
                            </select>
                        </div>
                    </div>
                    <div class="form-group other-reason" style="display:none;">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('other') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <textarea name="other_reason" id="other_reason" class="form-control input-sm" style="resize:none;"></textarea>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm danger-btn theme-btn default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
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
<div id="assign_driver" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="assign_driver_text"><?php echo $this->lang->line('assign_driver') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_assign_driver" name="form_assign_driver" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="order_entity_id" id="order_entity_id" value="">
                        <input type="hidden" name="order_invoice" id="order_invoice" value="">
                        <input type="hidden" name="is_driver_assigned" id="is_driver_assigned" value="">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('driver') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="driver_id" id="driver_id" class="form-control required">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php if(!empty($drivers)){
                                foreach ($drivers as $key => $value) { ?>
                                     <option value="<?php echo $value->entity_id ?>"><?php echo $value->first_name.' '.$value->last_name; ?></option>
                                <?php  } } ?>                           
                            </select>                                               
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c" style="display: none;"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm danger-btn theme-btn default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div id="view_comment" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('view_comment') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_view_comment" name="form_view_comment" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('comment') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <textarea disabled class="form-control txt-extra-commment" name="extra_comment" id="extra_comment" rows="6" data-required="1"  ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div id="view_order_detail" class="modal fade" role="dialog">
</div>
<div id="view_status_history" class="modal fade" role="dialog">
</div>
<!-- reject reason -->
<div id="add_reject_reason" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('update_reject_reason') ?></h4>
      </div>
      <div class="modal-body">
        <form id="form_add_reject_reason" name="form_add_reject_reason" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="restaurant_id" id="restaurant_id_reject" value="">
                    <input type="hidden" name="user_id" id="user_id_reject" value="">
                    <input type="hidden" name="order_id" id="order_id" value="">
                    <div class="form-group reject-reason" style="display:none;">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('reason') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="reject_reason" id="reject_reason" class="form-control input-sm">
                            </select>
                        </div>
                    </div>
                    <div class="form-group other-reject-reason" style="display:none;">
                        <label class="control-label col-md-4"><?php echo $this->lang->line('other') ?><span class="required">*</span></label>
                        <div class="col-sm-8">
                            <textarea name="other_reject_reason" id="other_reject_reason" class="form-control input-sm" style="resize:none;"></textarea>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-12 text-center">
                         <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                         <button type="submit" class="btn btn-sm danger-btn theme-btn default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- reject reason -->
<div id="add_payment_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add')?> <?php echo $this->lang->line('payment')?></h4>
            </div>
            <div class="modal-body">
                <form id="form_add_payment" name="form_add_payment" method="post" class="form-horizontal">
                    <input type="hidden" name="entity_id" id="entity_id" value="">
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('payment_method')?><span class="required">*</span></label>
                                <div class="col-md-5">
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="cash"><?php echo $this->lang->line('cash_word') ?></option> 
                                        <option value="card"><?php echo $this->lang->line('card_word') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group transaction_idcls">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('transaction_number') ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="transaction_id" id="transaction_id"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-10 col-md-offset-1 text-center">
                                <div id="loadingModal" class="loader-c display-no" >
                                    <img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle" />
                                </div>
                                <button type="submit" class="btn btn-sm danger-btn theme-btn default-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save')?></span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 <div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/admin/plugins/uniform/jquery.uniform.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/admin/plugins/uniform/css/uniform.default.min.css">
<!--<script type="text/javascript" src="<?php //echo base_url() ?>assets/admin/plugins/uniform/css/uniform.default.min.css"></script>-->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
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
var grid;
jQuery(document).ready(function() {
    
    $(".date-picker").datepicker( {
        //format: "dd-mm-yyyy",
        format: "mm-dd-yyyy",
        endDate: '+0d',
        /*startView: "months", 
        minViewMode: "months",*/
        autoclose: true    
    });
    $('.order-date-picker').focus(function() {
        $(this).daterangepicker({
                opens: 'left',
                // startDate: moment().subtract(10, 'day'),
                startDate: moment(),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                },
            }, function(start, myDate, label) {
        });
    });
    
    $('.order-date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $('.order-date-picker').val('');
    });
    Layout.init(); // init current layout    
     $('.sumo').SumoSelect({search:true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."}); 
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
                oData.aaSorting = [[ 6, "desc" ]];
            },            
            "bServerSide": true, // server side processing
            // "sAjaxSource": "ajaxview/dine_in", // ajax source
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/ajax_dine_in_view/all/<?php echo $user_id ?>/order_id/<?php echo $order_id ?>", // ajax source
            "aaSorting": [[ 6, "desc" ]] // set first column as a default sort by asc
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

$('#payment_method').on('change', function() {    
    if(this.value === 'cash')
    {        
        $('.transaction_idcls').hide();        
    }
    else
    {
        $('.transaction_idcls').show();
    }
});

// update driver for a order
function updateDriver(entity_id){
    $('#order_entity_id').val(entity_id);
    $('#is_driver_assigned').val(0);
    $('#assign_driver').modal('show');
}
// update driver for a order
function updateNewDriver(entity_id,html){
    $('#order_entity_id').val(entity_id);
    $('#assign_driver_text').html(html);
    $('#is_driver_assigned').val(1);
    $('#assign_driver').modal('show');
}
// submitting the assigning driver popup
$('#form_assign_driver').submit(function(){ 
    var driver_id = $('#driver_id').val();
    if (driver_id != '') { 
        $.ajax({
          type: "POST",
          dataType : "html",
          url: BASEURL+"backoffice/order/assignDriver",
          data: $('#form_assign_driver').serialize(),
          cache: false, 
          beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) { 
            if (html == "success") {
                $('#quotes-main-loader').hide();
                $('#assign_driver').modal('hide');
                grid.getDataTable().fnDraw();
            }
            return false;
          }
        }); 
    }
    return false;
});
// method for deleting
function deleteDetail(entity_id,message)
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
                  url : BASEURL+"backoffice/order/ajaxDelete",
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
//get invoice
function getInvoice(entity_id){
    $.ajax({
      type: "POST",
      dataType : "html",
      url: BASEURL+"backoffice/order/getInvoice",
      data: {'entity_id': entity_id},
      cache: false, 
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(html) {
            $('#quotes-main-loader').hide();
            var redirectWindow = window.open('<?php echo base_url() ?>'+html, '_blank');
            redirectWindow.location;
            //Old code
            /*var WinPrint = window.open('<?php echo base_url() ?>'+html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');*/
      }
    });
}
// method for reject order 
function rejectOrder(user_id,restaurant_id,order_id)
{
    $('#restaurant_id_reject').val(restaurant_id);
    $('#user_id_reject').val(user_id);
    $('#order_id').val(order_id);
    var language = '<?php echo $this->session->userdata('language_slug'); ?>';
    jQuery.ajax({
        type : "POST",                      
        url : BASEURL+"backoffice/order/show_reject_reason",
        data : {'language':language},
        success: function(response) {
            $('#reject_reason').empty().append(response);
            $('.reject-reason').css('display','block');
            var validator = $("#form_add_reject_reason").validate();
            $( "#reject_reason" ).rules( "add", {
                required: true
            });
            $('#add_reject_reason').modal('show');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
/*Reject Reason*/
$('#reject_reason').on('change', function() {
    if(this.value === 'other'){
        $('.other-reject-reason').css('display','block');
        $( "#other_rejcet_reason" ).rules( "add", {
            required: true,
            maxlength: 255
        });
    }
});
$('#add_reject_reason').on('hidden.bs.modal', function () {
    $('#form_add_reject_reason').validate().resetForm();
    $('.other-reject-reason').css('display','none');
    $( "#reject_reason" ).rules( "remove" );
    $('.other-reject-reason').css('display','none');
    $( "#reject_reason" ).rules( "remove" );
})
$('#form_add_reject_reason').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        $.ajax({
            type: "POST",
            dataType : "html",
            url: BASEURL+"backoffice/order/ajaxReject",
            data: $('#form_add_reject_reason').serialize(),
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(html) {
                $('#quotes-main-loader').hide();
                $('#add_reject_reason').modal('hide');
                grid.getDataTable().fnDraw();
            }
        });
    }
    return false;
});
//add status
function updateStatus(entity_id,status,user_id,order_type){
    $('#entity_id').val(entity_id);
    $('#user_id').val(user_id);
    $('#order_type').val(order_type);
    $('#order_statusval').val(status);
    var onGoingmsg = "<?php echo $this->lang->line('onGoing'); ?>";
    if(order_type=='PickUp')
    {
        onGoingmsg = "<?php echo $this->lang->line('order_ready'); ?>";
    }
    if(status == 'preparing')
    {
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="ready"><?php echo $this->lang->line('served'); ?></option><option value="complete"><?php echo $this->lang->line('complete'); ?></option>'
        );
    }
    if(status == 'accepted')
    {
        //<option value="preparing"><?php echo $this->lang->line('preparing'); ?></option>
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="ready"><?php echo $this->lang->line('served'); ?></option><option value="cancel"><?php echo $this->lang->line('cancel'); ?></option><option value="complete"><?php echo $this->lang->line('complete'); ?></option>'
        );
    }
    if(status == 'onGoing'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="ready"><?php echo $this->lang->line('served'); ?></option><option value="complete"><?php echo $this->lang->line('complete'); ?></option>'
        );
    }
    if(status == 'placed'){
        //<option value="preparing"><?php echo $this->lang->line('preparing'); ?></option>
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="ready"><?php echo $this->lang->line('served'); ?></option><option value="cancel"><?php echo $this->lang->line('cancel'); ?></option><option value="complete"><?php echo $this->lang->line('complete'); ?></option>'
        );
    }
    if(status == 'cancel' || status == 'complete'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'
        );
    }
    if(status == 'delivered' || status == 'ready'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="complete"><?php echo $this->lang->line('complete'); ?></option>'
        );
    }
    $('#add_status').modal('show');
}
//view comment
function viewComment(entity_id){
    $.ajax({
      type: "POST",
      url: BASEURL+"backoffice/order/viewComment",
      data: {"entity_id":entity_id},
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('textarea#extra_comment').val(response);
        $('#view_comment').modal('show');
      }
    });
}
//delete multiple
$('#delete_order').click(function(e){
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
                      url : BASEURL+"backoffice/order/deleteMultiOrder",
                      data : {'arrayData':CommissionIdComma},
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
function statusHistory(order_id){
    jQuery.ajax({
      type : "POST",                      
      url : BASEURL+"backoffice/order/statusHistory",
      data : {'order_id':order_id},
      cache: false,
      success: function(response) {      
        $('#view_status_history').html(response);
        $('#view_status_history').modal('show');      
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
}
// method for update status 
function disableDetail(entity_id,restaurant_id,order_id)
{
    bootbox.confirm({
        message: "<?php echo $this->lang->line('accept_order'); ?>",
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
                  url : BASEURL+"backoffice/order/ajaxdisable",
                  data : {'entity_id':entity_id,'restaurant_id':restaurant_id,'order_id':order_id,'dine_in':'yes'},
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
$('#assign_driver').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=select]").val('').end();
  $('#form_assign_driver').validate().resetForm();
});
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
$('#start_date').change(function()
{
    jQuery( "#end_date" ).prop('required',true);
    $("#end_date").rules('add', { greaterThanDate: "#start_date" });
});
// export selected orders 20/1/2021
$('#exportSelectedOrder').click(function(e)
{
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
            message: "<?php echo $this->lang->line('export_module'); ?>",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('ok'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('cancel'); ?>",
                }
            },
            callback: function (exportConfirm) {         
                if (exportConfirm) {
                    jQuery.ajax({
                      type : "POST",                      
                      url : BASEURL+"backoffice/order/export_order",
                      data : {'arrayData':CommissionIdComma,'fromAjax':'yes'},
                      success: function(response) {  
                        url = "<?php echo base_url();?>"+response;
                        window.location.href = url;                      
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
function updatePaymentStatus(entity_id){
    $('#add_payment_modal #entity_id').val(entity_id);
    $('#add_payment_modal').modal('show');
}
$("#form_add_payment").submit(function(event) {
    $("#form_add_payment").validate();
    if (!$("#form_add_payment").valid()) return false;
    var url = BASEURL+"backoffice/order/add_order_payment";
    var form = $("#form_add_payment").serialize();
    $.ajax({
      type: "POST",
      url: url,
      data: form,
      dataType: 'json',
      beforeSend: function(){
        jQuery('#add_payment_modal #loadingModal').show();
      },
      success: function(result) {
        if(result == null){
            jQuery('#add_payment_modal #loadingModal').hide();
            $('#add_payment_modal').modal('hide');
            bootbox.alert({
                message: "<?php echo $this->lang->line('admin_dinein_payment_error'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
        }else{
           jQuery('#add_payment_modal #loadingModal').hide();
           grid.getDataTable().fnDraw();
           $('#add_payment_modal').modal('hide');
        }
      }
    });
    return false;
});
$('#add_payment_modal').on('hidden.bs.modal', function () {
    $("#form_add_payment").validate().resetForm();
    $("#transaction_id").val('');
});
$('#bluk_payment_update').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var order_ids = Array();
        for (var i in records) {  
            var val = records[i]["value"];            
            order_ids.push(val);                        
        }
        var order_id_comma = order_ids.join(",");
        bootbox.confirm({
            message: "<?php echo $this->lang->line('admin_dinein_payment_multiple_warning'); ?>",
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
                      url : 'check_unpaid_payment',
                      data : {'arrayData':order_id_comma},
                      success: function(response) {
                        if(response > 0){
                            bootbox.alert({
                                message: "<?php echo $this->lang->line('admin_dinein_payment_multiple_error'); ?>",
                                buttons: {
                                    ok: {
                                        label: "<?php echo $this->lang->line('ok'); ?>",
                                    }
                                }
                            });
                        } else{
                            $('#add_payment_modal').modal('show');
                            $('#add_payment_modal #entity_id').val(order_id_comma);
                        }
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
//Send ajax request for dine in update order notification.
var i = setInterval(function(){
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    async: false,
    url : '<?php echo base_url().ADMIN_URL?>/order/ajax_dinein_order_update_notification',
    success: function(response) {
        if(response != null)
        {
            for(var i = 0; i < response.length; i++) {
                var obj = response[i];
                $("a[data-id='" + obj.order_id +"']").addClass('dien-in');
            }
        }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
    }
  });
},10000);
function readDetail(entity_id){
    jQuery.ajax({
        type : "POST",
        url : '<?php echo base_url().ADMIN_URL?>/order/mark_as_read_dinein_notification',
        data : {'entity_id':entity_id},
        async: false,
        success: function(response) {
            if(response > 0){
                $("a[data-id='" + entity_id +"']").removeClass('dien-in');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
$('#page_refresh').click(function() {
    location.reload();
});
/*Cancel Reason*/
$('#order_status').on('change', function() {
    if(this.value === 'cancel'){
        var language = '<?php echo $this->session->userdata('language_slug'); ?>';
        jQuery.ajax({
            type : "POST",                      
            url : BASEURL+"backoffice/order/show_cancel_reason",
            data : {'language':language},
            success: function(response) {
                $('#cancel_reason').empty().append(response);
                $('.cancel-reason').css('display','block');
                var validator = $("#form_add_status").validate();
                $( "#cancel_reason" ).rules( "add", {
                    required: true
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
});
$('#cancel_reason').on('change', function() {
    if(this.value === 'other'){
        $('.other-reason').css('display','block');
        $( "#other_reason" ).rules( "add", {
            required: true,
            maxlength: 255
        });
    }
});
$('#form_add_status').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        $.ajax({
            type: "POST",
            dataType : "html",
            url: BASEURL+"backoffice/order/updateOrderStatus",
            data: $('#form_add_status').serialize(),
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(html) {
                $('#quotes-main-loader').hide();
                $('#add_status').modal('hide');
                grid.getDataTable().fnDraw();
            }
        });
    }
    return false;
});
$('#add_status').on('hidden.bs.modal', function () {
    $('#form_add_status').validate().resetForm();
    $('.cancel-reason').css('display','none');
    $( "#cancel_reason" ).rules( "remove" );
    $('.other-reason').css('display','none');
    $( "#other_reason" ).rules( "remove" );
});
function printReceipt(entity_id){
    $.ajax({
        type: "POST",
        dataType : "html",
        url: BASEURL+"backoffice/order/print_receipt",
        data: {'entity_id': entity_id},
        cache: false, 
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },   
        success: function(html) { 
            $('#quotes-main-loader').hide();
            var WinPrint = window.open('<?php echo base_url() ?>'+html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');
            WinPrint.window.print();
        }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>