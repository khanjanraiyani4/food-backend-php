<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/daterangepicker/css/daterangepicker.css" />
<style type="text/css">
    .page_refresh{
        position: relative;
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
                        <?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('orders') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            
            <?php //New export code set as per required :: Start :: 25-01-2021
            if(in_array('order~export_order',$this->session->userdata("UserAccessArray"))) { ?>
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
                                    <?php if(isset($_SESSION['not_found']))
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('order_type') ?><span class="required">*</span></label>
                                                    <select name="order_delivery" class="form-control sumo">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <option value="all"><?php echo $this->lang->line('all') ?></option>
                                                        <option value="Delivery"><?php echo $this->lang->line('delivery_order') ?></option>
                                                        <option value="PickUp"><?php echo $this->lang->line('pickup') ?></option>                          
                                                    </select> 
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
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
            <?php }
            //New export code set as per required :: End :: 25-01-2021 ?>
                        
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('orders') ?> <?php echo $this->lang->line('list') ?></div>
                            <div class="actions">
                                <button class="btn default-btn btn-sm danger-btn theme-btn page_refresh" id="page_refresh"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo $this->lang->line('refresh') ?></button>
                                <?php if(in_array('order~add',$this->session->userdata("UserAccessArray"))) { ?>
                                    <a class="btn default-btn btn-sm danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <?php } ?>
                                <?php if(in_array('order~ajaxDelete',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="delete_order"><i class="fa fa-times"></i> <?php echo $this->lang->line('delete') ?></button>
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
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <th><?php echo $this->lang->line('order') ?>#</th>
                                            <th><?php echo $this->lang->line('restaurant') ?>/<?php echo $this->lang->line('branch') ?></th>
                                            <th><?php echo $this->lang->line('customer') ?></th>
                                            <th><?php echo $this->lang->line('order_total') ?></th>
                                            <th><?php echo $this->lang->line('order_assign') ?></th>
                                            <th><?php echo $this->lang->line('order_status') ?></th>
                                            <th><?php echo $this->lang->line('payment_method') ?></th>
                                            <th><?php echo $this->lang->line('order_date') ?></th>
                                            <th><?php echo $this->lang->line('scheduled_date') ?></th>
                                            <th><?php echo $this->lang->line('order_type') ?></th>
                                            <?php /* ?><th><?php echo $this->lang->line('delivery_method') ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th><?php */ ?>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>  
                                            <td><input type="text" class="form-control form-filter input-sm" name="order"></td>                                
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>                                    
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title" value="<?=$customer_name;?>"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="order_total"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="driver"></td>
                                            <td>
                                                <select name="order_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                    foreach ($order_status as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php  } ?>
                                                    <option value="delayed"><?php echo $this->lang->line('delayed'); ?></option>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="payment_method"></td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm order-date-picker" name="order_date" id="order_date"  placeholder="<?php echo $this->lang->line('select_date'); ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm order-date-picker" name="scheduled_date" id="scheduled_date"  placeholder="<?php echo $this->lang->line('select_date'); ?>">
                                            </td>
                                            <td><select name="order_delivery" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="PickUp"><?php echo $this->lang->line('pickup') ?></option> 
                                                    <option value="Delivery"><?php echo $this->lang->line('delivery_order') ?></option>
                                                </select> 
                                            </td>
                                            <?php /* ?><td><select name="delivery_method_filter" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="internal_drivers"><?php echo $this->lang->line('internal_drivers') ?></option> 
                                                    <option value="thirdparty_delivery"><?php echo $this->lang->line('thirdparty_delivery') ?></option>
                                                </select> 
                                            </td><?php */ ?>
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
        <h4 class="modal-title cancel_refund_title"><?php echo $this->lang->line('update_status') ?></h4>
      </div>
      <div class="modal-body">
        <div class="cancel_refund_order_confirmation" style="display: none">
            <p><?php echo $this->lang->line('cancel_order_refund'); ?><p>
            <div class="action-btn">
                <input type="button" name="accept_delivery_order" id="accept_delivery_order" value="<?php echo $this->lang->line('ok') ?>" class="btn btn-primary" onclick="cancel_refund_order()">
                <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
            </div>
        </div>
        <div class="cancel_refund_order_section" style="display: block;">
            <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="hidden" name="entity_id" id="entity_id" value="">
                            <input type="hidden" name="invoice" id="invoice" value="">
                            <input type="hidden" name="user_id" id="user_id" value="">
                            <input type="hidden" name="order_type" id="order_type" value="">
                            <input type="hidden" name="order_statusval" id="order_statusval" value="">
                            <input type="hidden" name="refund_status" id="refund_status" value="">
                            <input type="hidden" name="payment_option" id="payment_option" value="">
                            <label class="control-label col-md-4"><?php echo $this->lang->line('status') ?><span class="required">*</span></label>
                            <div class="col-sm-8">
                                <select name="order_status" id="order_status" class="form-control form-filter input-sm" required>
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php $order_status = order_status($this->session->userdata('language_slug'));
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
        <form id="form_assign_driver" name="form_assign_driver" method="post" class="form-horizontal form_assign_driver" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="driver_de">
                        <label>  
                            <span style="background:green;color: #fff; margin-right: 5px;">&nbsp;&nbsp;</span><font><?php echo $this->lang->line('available_driver') ?></font>
                            <span style="background:red;color: #fff; margin-right: 5px;">&nbsp;&nbsp;</span><font><?php echo $this->lang->line('driver_on_way') ?></font>
                        </label>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="order_entity_id" id="order_entity_id" value="">
                        <input type="hidden" name="order_invoice" id="order_invoice" value="">
                        <input type="hidden" name="is_driver_assigned" id="is_driver_assigned" value="">
                        <input type="hidden" name="current_order_status" id="current_order_status" value="">
                        <div class="control-label driver_onway col-xs-2"><label><?php echo $this->lang->line('driver') ?><span class="required">*</span></label>  
                        </div>
                        <div class="col-xs-10">
                            <select name="driver_id" id="driver_id" class="form-control required">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php if(!empty($drivers)){
                                foreach ($drivers as $key => $value) { 
                                    $bgdriveclr = 'lightgreen';
                                    $bgfaicon = "<i class='fa fa-user'></i>";
                                    if($value->ongoing=='yes')
                                    {
                                        $bgdriveclr = 'lightblue';
                                        $bgfaicon = "<i class='fa fa-map-marker' aria-hidden='true'></i>";
                                    }
                                    else if($value->ongoing=='no')
                                    {
                                        $bgdriveclr = '';
                                        $bgfaicon = "";
                                    }
                                    ?>
                                    <option style="background:<?=$bgdriveclr;?>; border: 1px solid grey;" value="<?php echo $value->entity_id ?>"
                                        data-content="<?=$bgfaicon;?> <?php echo $value->first_name.' '.$value->last_name; ?>"
                                        ><?php echo $value->first_name.' '.$value->last_name; ?></option>
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
<div id="order_cancelled_model" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title ordercancel_modal_title"><?php echo $this->lang->line('order_status_already_changed') ?></h4>
      </div>
      <div class="modal-body" id="order_cancelled_model_body">
      </div>
    </div>
  </div>
</div>
<!-- reject reason :: start -->
<div id="add_reject_reason" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title reject_refund_order_title"><?php echo $this->lang->line('reject_order') ?></h4>
      </div>
      <div class="modal-body">
        <div class="reject_refundorder_section">
            <p><?php echo $this->lang->line('reject_order_refund'); ?><p>
            <div class="action-btn">
                <input type="button" name="accept_delivery_order" id="accept_delivery_order" value="<?php echo $this->lang->line('ok') ?>" class="btn btn-primary" onclick="reject_refund_order()">
                <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
            </div>
        </div>
        <div class="reject_refund_confirmation_section" style="display: none;">
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
</div>
<!-- reject reason :: end -->
<?php //accept order modal :: start ?>
<div class="modal fade accept-order" role="dialog" id="accept-order">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                <h4 class="modal-title accept_order_modal_title"><?php echo $this->lang->line('accept').' '.$this->lang->line('order') ?>?</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="accept_order_section">
                    <p><?php echo $this->lang->line('accept_order'); ?><p>
                    <div class="action-btn">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
                        <input type="button" name="accept_delivery_order" id="accept_delivery_order" value="<?php echo $this->lang->line('ok') ?>" class="btn btn-primary" onclick="accept_delivery_order()">
                    </div>
                </div>
                <div class="choose_delivery_method_section" style="display: none;">
                    <form id="form_choose_delivery_method" name="form_choose_delivery_method" method="post" class="form-horizontal" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group delivery-method">
                                    <input type="hidden" name="accept_entity_id" id="accept_entity_id" value="">
                                    <input type="hidden" name="accept_restaurant_id" id="accept_restaurant_id" value="">
                                    <input type="hidden" name="accept_order_id" id="accept_order_id" value="">
                                    <input type="hidden" name="orders_user_id" id="orders_user_id" value="">
                                    <label class="control-label col-md-5"><?php echo $this->lang->line('choose_delivery_method') ?><span class="required">*</span></label>
                                    <div class="col-md-7">
                                        <div class="form_choose_delivery">
                                            <input type="radio" checked="checked" class="internal_drivers" name="choose_delivery_method" id="choose_delivery_method" value="internal_drivers">
                                            <span id="internal_drivers_label"><?php echo $this->lang->line('internal_drivers') ?></span>
                                            <input type="radio" class="thirdparty_delivery display-no" name="choose_delivery_method" id="choose_delivery_method" value="thirdparty_delivery">
                                            <span id="thirdparty_delivery_label" class="display-no"><?php echo $this->lang->line('thirdparty_delivery') ?></span>
                                        </div>
                                        <div id="delivery_method_err" class="error"></div>
                                    </div>
                                    
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-12 text-center">
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
</div>
<?php //accept order modal :: end ?>
<?php //refund reason :: start ?>
<div id="add_refund_reason" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title refund_reason_title"><?php echo $this->lang->line('initiate_refund') ?></h4>
            </div>
            <div class="modal-body">
                <div class="refund_reason_section">
                    <p><?php echo $this->lang->line('intiate_stripe_refund'); ?><p>
                    <div class="action-btn">
                        <input type="button" name="initiate_refund" id="initiate_refund" value="<?php echo $this->lang->line('ok') ?>" class="btn btn-primary" onclick="initiate_refund_for_order()">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
                    </div>
                </div>
                <div class="refund_reason_confirmation_section" style="display: none; width: 100%;">
                    <form id="form_add_refund_reason" name="form_add_refund_reason" method="post" class="form-horizontal" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <?php //Code for add the amount for refund :: Start ?>
                                <div class="form_choose_delivery">
                                    <input type="radio" checked="checked" class="internal_drivers" name="partial_refundedchk" onclick="chkrefundoption(this.value);" id="partial_refundedid1" value="full">
                                    <span><?php echo $this->lang->line('full_refund') ?></span>
                                    <input type="radio" class="thirdparty_delivery" name="partial_refundedchk" onclick="chkrefundoption(this.value);" id="partial_refundedid2" value="partial">
                                    <span><?php echo $this->lang->line('partial_refund') ?></span>
                                </div>
                                <div class="form-group refund-reason" id="partial_refundedamtid" style="display: none">
                                    <label class="control-label col-md-12"><?php echo $this->lang->line('amount') ?><span class="required">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control input-sm" name="partial_refundedamt" id="partial_refundedamt" value="">
                                    </div>
                                </div>
                                <?php //Code for add the amount for refund :: End ?>
                                <input type="hidden" name="refund_order_id" id="refund_order_id" value="">
                                <input type="hidden" name="refund_order_total" id="refund_order_total" value="">
                                <input type="hidden" name="refund_order_totaldis" id="refund_order_totaldis" value="">
                                <div class="form-group refund-reason">
                                    <label class="control-label col-md-12"><?php echo $this->lang->line('reason') ?><span class="required">*</span></label>
                                    <div class="col-md-12">
                                        <textarea name="refund_reason" id="refund_reason" class="form-control input-sm" maxlength="250"></textarea>
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
</div>
<?php //refund reason :: end ?>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/css/bootstrap-select.min.css">
<script type="text/javascript">$('#driver_id').selectpicker();</script>
<script>
var grid;
var datepicker_format = "<?php echo datepicker_format; ?>";
var daterangepicker_format = "<?php echo daterangepicker_format; ?>";
var order_count = <?php echo ($order_count)?$order_count:0; ?>;
jQuery(document).ready(function() {

    $(".date-picker").datepicker( {
        //format: "dd-mm-yyyy",
        format: datepicker_format,
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
                    //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                },
                locale: {
                  format: daterangepicker_format
                }
            }, function(start, myDate, label) {
        });
    });
    
    $('.order-date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $('.order-date-picker').val('');
    });
    Layout.init(); // init current layout    
    $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
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
                if(oSettings.aoData.length == 0 && order_count != 0 && oData.iStart >= order_count){
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
                oData.aaSorting = [[ 8, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/ajaxview/all/<?php echo $user_id ?>/order_id/<?php echo $order_id ?>", // ajax source
            "aaSorting": [[ 8, "desc" ]] // set first column as a default sort by asc
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
// update driver for a order
function updateDriver(entity_id,order_status,restaurant_id)
{   
    $("#driver_id").val('');
    $("#driver_id").selectpicker("refresh");
    $('#order_entity_id').val(entity_id);

    $.ajax({
        type: "POST",
        dataType : "html",
        url: BASEURL+"backoffice/order/getRestaurantDriver",
        data : {'order_id':entity_id,'restaurant_id':restaurant_id},
        cache: false, 
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },   
        success: function(response) {                       
            $("#driver_id").html(response).selectpicker('refresh');
            $('#quotes-main-loader').hide();
            $('#assign_driver').modal('show');        
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
            $('#quotes-main-loader').hide();
        }
    });

    $('#is_driver_assigned').val(0);
    $('#current_order_status').val(order_status);    
}
// update driver for a order
function updateNewDriver(entity_id,html,order_status,restaurant_id,driver_id)
{
    $("#driver_id").val('');
    $("#driver_id").selectpicker("refresh");    
    $('#order_entity_id').val(entity_id);
    $.ajax({
        type: "POST",
        dataType : "html",
        url: BASEURL+"backoffice/order/getRestaurantDriver",
        data : {'order_id':entity_id,'restaurant_id':restaurant_id,'driver_id':driver_id},
        cache: false, 
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },   
        success: function(response) {            
            $("#driver_id").html(response).selectpicker('refresh');
            $('#quotes-main-loader').hide();
            $('#assign_driver').modal('show');        
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
            $('#quotes-main-loader').hide();
        }
    });
    $('#assign_driver_text').html(html);
    $('#is_driver_assigned').val(1);
    $('#current_order_status').val(order_status);    
}
// submitting the assigning driver popup
$('#form_assign_driver').submit(function(){ 
    var driver_id = $('#driver_id').val();
    if (driver_id != '') { 
        $.ajax({
          type: "POST",
          dataType : "json",
          url: BASEURL+"backoffice/order/assignDriver",
          data: $('#form_assign_driver').serialize(),
          cache: false, 
          beforeSend: function(){
            $('#quotes-main-loader').show();
          },   
          success: function(html) { 
            if (html.result == "success") {
                $('#quotes-main-loader').hide();
                $('#assign_driver').modal('hide');
                grid.getDataTable().fnDraw();
            } else if(html.result == "already_assigned"){
                var box = bootbox.alert({
                  message: html.message,
                  buttons: {
                      ok: {
                          label: html.oktext,
                      }
                  }
                });
                setTimeout(function() {
                  box.modal('hide');
                }, 10000);

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
function deleteDetail(entity_id,message,order_mode,delivery_method)
{
    if(order_mode == 'delivery' && (delivery_method == 'doordash' || delivery_method == 'relay')) {
        var url = BASEURL+"backoffice/order/ajaxDeleteThirdpartyDeliveryOrders";
    } else {
        var url = BASEURL+"backoffice/order/ajaxDelete";
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
                  url : url,
                  data : {'entity_id':entity_id,'delivery_method':delivery_method,'order_mode':order_mode},
                  success: function(response) {
                    if(!jQuery.isEmptyObject(response) && response.status == 'thirdparty_cancel_error'){
                        $('.ordercancel_modal_title').text("<?php echo $this->lang->line('thirdparty_delivery_errors'); ?>");
                        $('#order_cancelled_model_body').text(response.status_message);
                        $('#order_cancelled_model').modal('show');
                    } else {
                        location.reload();
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
/*// method for reject order 
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
}*/
// method for reject order 
function rejectOrder(user_id,restaurant_id,order_id,refund_status)
{
    $('#restaurant_id_reject').val(restaurant_id);
    $('#user_id_reject').val(user_id);
    $('#order_id').val(order_id);
    if(refund_status=='refunded'){
        reject_refund_order();
    }
    $('#add_reject_reason').modal('show');
}
/*Reject Reason*/
$('#reject_reason').on('change', function() {
    if(this.value === 'other'){
        $('.other-reject-reason').css('display','block');
        $( "#other_reject_reason" ).rules( "add", {
            required: true,
            maxlength: 255
        });
    }
    else{
        $('.other-reject-reason').css('display','none');
    }
});
/*$('#add_reject_reason').on('hidden.bs.modal', function () {
    $('#form_add_reject_reason').validate().resetForm();
    $('.other-reject-reason').css('display','none');
    $( "#reject_reason" ).rules( "remove" );
    $('.other-reject-reason').css('display','none');
    $( "#reject_reason" ).rules( "remove" );
});*/
$('#form_add_reject_reason').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        $.ajax({
            type: "POST",
            dataType : "json",
            url: BASEURL+"backoffice/order/ajaxReject",
            data: $('#form_add_reject_reason').serialize(),
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#add_reject_reason').modal('hide');
                if (response.error) {
                    var refundbox = bootbox.alert({
                      message: "<?php echo $this->lang->line('intiate_stripe_refunderror'); ?>",
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                }else if(response.error_message){
                    var refundbox = bootbox.alert({
                      message: response.error_message,
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                }
                grid.getDataTable().fnDraw();
            }
        });
    }
    return false;
});
//add status
function updateStatus(entity_id,status,user_id,order_type,refund_status,payment_option){
    $('#entity_id').val(entity_id);
    $('#user_id').val(user_id);
    $('#order_statusval').val(status);
    $('#order_type').val(order_type);
    $('#refund_status').val(refund_status);
    $('#payment_option').val(payment_option);
    var onGoingmsg = "<?php echo $this->lang->line('onGoing'); ?>";
    var delivereddis = '<option value="delivered"><?php echo $this->lang->line('delivered'); ?></option>';
    if(order_type=='PickUp')
    {
        onGoingmsg = "<?php echo $this->lang->line('order_ready'); ?>";
        var delivereddis = '<option value="complete"><?php echo $this->lang->line('complete'); ?></option>';
    }
    /*if(status == 'preparing')
    {
        $('#order_status').empty().append(
            '<option value=""><?php //echo $this->lang->line('select'); ?></option><option value="onGoing">'+onGoingmsg+'</option>'+delivereddis+'<option value="cancel"><?php //echo $this->lang->line('cancel'); ?></option>'
        );
    }*/
    if(status == 'onGoing')
    {
        if(order_type=='PickUp')
        {
            $('#order_status').empty().append(
                '<option value=""><?php echo $this->lang->line('select'); ?></option>'+delivereddis+'<option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
            );
        }
        else
        {
            $('#order_status').empty().append(
                '<option value=""><?php echo $this->lang->line('select'); ?></option>'+onGoingmsg+delivereddis+'<option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
            );
        }
        
    }
    if(status == 'ready'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'+onGoingmsg+delivereddis+'<option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
        );
    }
    if(status == 'placed'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="onGoing">'+onGoingmsg+'</option>'+onGoingmsg+'<option value="cancel"><?php echo $this->lang->line('complete'); ?></option><option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
        );
    }
    if(status == 'accepted'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="onGoing">'+onGoingmsg+'</option>'+onGoingmsg+delivereddis+'<option value="cancel"><?php echo $this->lang->line('cancel'); ?></option>'
        );
    }
    if(status == 'cancel'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'
        );
    }
    if(status == 'delivered'){
        $('#order_status').empty().append(
            '<option value=""><?php echo $this->lang->line('select'); ?></option>'
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
function accept_delivery_order() {
    var validator = $("#form_choose_delivery_method").validate();
    $( "#choose_delivery_method" ).rules( "add", {
        required: true
    });
    $('.accept_order_section').css('display','none');
    $('.accept_order_modal_title').text("<?php echo $this->lang->line('delivery_method') ?>");
    $('.choose_delivery_method_section').css('display','inline-block');
}
$('#form_choose_delivery_method').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid()){
        $.ajax({
            type: "POST",
            dataType : "json",
            url: BASEURL+"backoffice/order/ajaxdisable_for_deliveryorders",
            data: $('#form_choose_delivery_method').serialize(),
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(json_response) {
                $('#quotes-main-loader').hide();
                if(json_response.error != ''){
                    $('#delivery_method_err').text(json_response.error);
                    $(".internal_drivers").prop("checked", true);
                } else {
                    $('#accept-order').modal('hide');
                    grid.getDataTable().fnDraw();
                }
            }
        });
    }
    return false;
});
$('#accept-order').on('hidden.bs.modal', function () {
    $('#form_choose_delivery_method').validate().resetForm();
    $('.accept_order_section').css('display','inline-block');
    $('.accept_order_modal_title').text("<?php echo $this->lang->line('accept').' '.$this->lang->line('order') ?>?");
    $('#thirdparty_delivery_label').addClass('display-no');
    $('.thirdparty_delivery').addClass('display-no');
    $('#delivery_method_err').empty();
    $('.choose_delivery_method_section').css('display','none');
});
// method for update status 
function disableDetail(entity_id,restaurant_id,order_id, orders_user_id,order_mode)
{
    /*if(order_mode == 'delivery'){
        //send request directly to thirdparty delivery methods
        // jQuery.ajax({
        //     type : "POST",
        //     dataType : "json",
        //     url : BASEURL+"backoffice/order/getResDeliveryMethods",
        //     data : {'restaurant_id':restaurant_id},
        //     success: function(response) {
        //         if(response.check_thirdparty_available == 'no') {
        //             var box = bootbox.alert({
        //                 message: "<?php //echo $this->lang->line('assign_delivery_method'); ?>",
        //                 buttons: {
        //                     ok: {
        //                         label: "<?php //echo $this->lang->line('ok'); ?>",
        //                     }
        //                 }
        //             });
        //             setTimeout(function() {
        //                 box.modal('hide');
        //             }, 10000);
        //         } else {
        //             bootbox.confirm({
        //                 message: "<?php //echo $this->lang->line('accept_order'); ?>",
        //                 buttons: {
        //                     confirm: {
        //                         label: '<?php //echo $this->lang->line('ok'); ?>',
        //                     },
        //                     cancel: {
        //                         label: '<?php //echo $this->lang->line('cancel'); ?>',
        //                     }
        //                 },
        //                 callback: function (deleteConfirm) {
        //                     if (deleteConfirm) {
        //                         $.ajax({
        //                             type: "POST",
        //                             dataType : "json",
        //                             url: BASEURL+"backoffice/order/ajaxdisable_for_deliveryorders",
        //                             data: {'accept_entity_id' : entity_id, 'accept_restaurant_id' : restaurant_id, 'accept_order_id' : order_id, 'orders_user_id' : orders_user_id, 'choose_delivery_method' : 'thirdparty_delivery'},
        //                             beforeSend: function(){
        //                                 $('#quotes-main-loader').show();
        //                             },   
        //                             success: function(json_response) {
        //                                 $('#quotes-main-loader').hide();
        //                                 if(json_response.error != ''){
        //                                     var box = bootbox.alert({
        //                                         message: json_response.error,
        //                                         buttons: {
        //                                             ok: {
        //                                                 label: "<?php //echo $this->lang->line('ok'); ?>",
        //                                             }
        //                                         }
        //                                     });
        //                                     setTimeout(function() {
        //                                         box.modal('hide');
        //                                     }, 10000);
        //                                 } else {
        //                                     grid.getDataTable().fnDraw();
        //                                 }
        //                             }
        //                         });
        //                     }
        //                 }
        //             });
        //         }
        //     },
        //     error: function(XMLHttpRequest, textStatus, errorThrown) {
        //         alert(errorThrown);
        //     }
        // });

        //choose delivery method :: thirdparty or internal drivers
        // $('#accept_entity_id').val(entity_id);
        // $('#accept_restaurant_id').val(restaurant_id);
        // $('#accept_order_id').val(order_id);
        // $('#orders_user_id').val(orders_user_id);
        // $('#accept-order').modal('show');
        // var res_id = $('#accept_restaurant_id').val();
        // jQuery.ajax({
        //     type : "POST",
        //     dataType : "json",
        //     url : BASEURL+"backoffice/order/getResDeliveryMethods",
        //     data : {'restaurant_id':res_id},
        //     success: function(response) {
        //         if(response.check_thirdparty_available == 'no'){
        //             $('#thirdparty_delivery_label').addClass('display-no');
        //             $('.thirdparty_delivery').addClass('display-no');
        //             $(".internal_drivers").prop("checked", true);
        //         } else {
        //             $('#thirdparty_delivery_label').removeClass('display-no');
        //             $('.thirdparty_delivery').removeClass('display-no');
        //             $(".internal_drivers").prop("checked", true);
        //         }
        //     },
        //     error: function(XMLHttpRequest, textStatus, errorThrown) {
        //         alert(errorThrown);
        //     }
        // });
    } else {*/
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
                      data : {'entity_id':entity_id,'restaurant_id':restaurant_id,'order_id':order_id,'orders_user_id':orders_user_id},
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
    //}
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
    else{
        $('.cancel-reason').css('display','none');
        $('.other-reason').css('display','none');
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
    else{
        $('.other-reason').css('display','none');
    }
});
$('#form_add_status').submit(function(e){
    if(($('#order_status').val()=='cancel') && ($('#refund_status').val()!='refunded')){
        $('.cancel_refund_order_section').css('display','none');
        $('.cancel_refund_order_confirmation').css('display','block');
    }else{
        e.preventDefault();
        $(this).validate();
        if($(this).valid()){
            $.ajax({
                type: "POST",
                dataType : "json",
                url: BASEURL+"backoffice/order/updateOrderStatus",
                data: $('#form_add_status').serialize(),
                cache: false, 
                beforeSend: function(){
                    $('#quotes-main-loader').show();
                },   
                success: function(html) {
                    $('#order_cancelled_model').modal('hide');
                    if(!jQuery.isEmptyObject(html) && html.status == 'order_status_already_changed'){
                        $('.ordercancel_modal_title').text("<?php echo $this->lang->line('order_status_already_changed'); ?>");
                        $('#order_cancelled_model_body').text(html.status_message);    
                        $('#order_cancelled_model').modal('show');  
      
                    } else if(!jQuery.isEmptyObject(html) && html.status == 'thirdparty_cancel_error'){
                        $('.ordercancel_modal_title').text("<?php echo $this->lang->line('thirdparty_delivery_errors'); ?>");
                        $('#order_cancelled_model_body').text(html.status_message);
                        $('#order_cancelled_model').modal('show');
                    }
                    $('#quotes-main-loader').hide();
                    $('#add_status').modal('hide');
                    grid.getDataTable().fnDraw();
                }
            });
        }
    }
    return false;
});
$('#add_status').on('hidden.bs.modal', function () {
    $('#form_add_status').validate().resetForm();
    $('.cancel-reason').css('display','none');
    $( "#cancel_reason" ).rules( "remove" );
    $('.other-reason').css('display','none');
    $( "#other_reason" ).rules( "remove" );
    $('.cancel_refund_order_confirmation').css('display','none');
    $('.cancel_refund_order_section').css('display','block');
});

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
$('#page_refresh').click(function() {
    location.reload();
});
// cancel order and reject order refund changes
function cancel_refund_order() {
    $('#form_add_status').validate();
    if($('#form_add_status').valid()){
        $.ajax({
            type: "POST",
            dataType : "json",
            url: BASEURL+"backoffice/order/updateOrderStatus",
            data: $('#form_add_status').serialize(),
            cache: false, 
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },   
            success: function(html) {
                $('#order_cancelled_model').modal('hide');
                if (html.error) {
                    var refundbox = bootbox.alert({
                      message: "<?php echo $this->lang->line('intiate_stripe_refunderror'); ?>",
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                }else if(html.error_message){
                    var refundbox = bootbox.alert({
                      message: html.error_message,
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                }
                if(!jQuery.isEmptyObject(html) && html.status == 'order_status_already_changed'){
                    $('.ordercancel_modal_title').text("<?php echo $this->lang->line('order_status_already_changed'); ?>");
                    $('#order_cancelled_model_body').text(html.status_message);    
                    $('#order_cancelled_model').modal('show');  
  
                } else if(!jQuery.isEmptyObject(html) && html.status == 'thirdparty_cancel_error'){
                    $('.ordercancel_modal_title').text("<?php echo $this->lang->line('thirdparty_delivery_errors'); ?>");
                    $('#order_cancelled_model_body').text(html.status_message);
                    $('#order_cancelled_model').modal('show');
                }
                $('#quotes-main-loader').hide();
                $('#add_status').modal('hide');
                grid.getDataTable().fnDraw();
            }
        });
    }
    return false;
}
function reject_refund_order() {
    var language = '<?php echo $this->session->userdata('language_slug'); ?>';
    $('.reject_refundorder_section').css('display','none');
    $('.reject_refund_order_title').text("<?php echo $this->lang->line('update_reject_reason') ?>");
    $('.reject_refund_confirmation_section').css('display','inline-block');
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
$('#add_reject_reason').on('hidden.bs.modal', function () {
    $('.reject_refundorder_section').css('display','inline-block');
    $('.reject_refund_order_title').text("<?php echo $this->lang->line('reject_order'); ?>");
    $('.reject_refund_confirmation_section').css('display','none');
});
function initiateRefund(order_id,order_total=0,refunded_amount=0,currency_symbolval='$'){    
    if(refunded_amount=='' || refunded_amount==null || refunded_amount==undefined || refunded_amount==NaN)
    {
        refunded_amount =0;
    }
    order_total = parseFloat(order_total)-parseFloat(refunded_amount);
    order_total = parseFloat(order_total).toFixed(2);
    if(currency_symbolval==''){ currency_symbolval = '$'; }
    var order_totaldis = currency_symbolval+''+order_total;
    $('#refund_order_id').val(order_id);
    $('#refund_order_total').val(order_total);
    $('#refund_order_totaldis').val(order_totaldis);
    $('#add_refund_reason').modal('show');
    /*bootbox.confirm({
        message: "<?php //echo $this->lang->line('intiate_stripe_refund'); ?>",
        buttons: {
            confirm: {
                label: '<?php //echo $this->lang->line('ok'); ?>',
            },
            cancel: {
                label: '<?php //echo $this->lang->line('cancel'); ?>',
            }
        },
        callback: function (deleteConfirm) {          
            if (deleteConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "json",
                  url : BASEURL+"backoffice/order/ajaxinitiaterefund",
                  data : {'refund_order_id':order_id},
                  success: function(response) {
                        if (response.paymentIntentstatus == "refunded" || response.tips_paymentIntentstatus == "refunded") {
                            var refundbox = bootbox.alert({
                              message: "<?php //echo $this->lang->line('refund_initiated'); ?>",
                              buttons: {
                                  ok: {
                                      label: "<?php //echo $this->lang->line('ok'); ?>",
                                  }
                              }
                            });
                            setTimeout(function() {
                              refundbox.modal('hide');
                            }, 10000);
                            $('#quotes-main-loader').hide();
                            grid.getDataTable().fnDraw();
                        } else if(response.error){
                            var box = bootbox.alert({
                              message: "<?php //echo $this->lang->line('intiate_stripe_refunderror'); ?>",
                              buttons: {
                                  ok: {
                                      label:"<?php //echo $this->lang->line('ok'); ?>",
                                  }
                              }
                            });
                            setTimeout(function() {
                              box.modal('hide');
                            }, 10000);
                            $('#quotes-main-loader').hide();
                            grid.getDataTable().fnDraw();
                        }else if(response.error_message){
                            var refundbox = bootbox.alert({
                              message: response.error_message,
                              buttons: {
                                  ok: {
                                      label: "<?php //echo $this->lang->line('ok'); ?>",
                                  }
                              }
                            });
                            setTimeout(function() {
                              refundbox.modal('hide');
                            }, 10000);
                        }
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });*/
}
function initiate_refund_for_order() {
    var language = '<?php echo $this->session->userdata('language_slug'); ?>';
    $('.refund_reason_section').css('display','none');
    $('.refund_reason_title').text("<?php echo $this->lang->line('update_refund_reason') ?>");
    $('.refund_reason_confirmation_section').css('display','inline-block');    
    $('#refund_reason').val('');    
    $('#partial_refundedamtid').css('display','none');
    $('#partial_refundedamt').val('');
    $("#partial_refundedid1").prop("checked", true);
    $('#add_refund_reason').modal('show');
}
$('#add_refund_reason').on('hidden.bs.modal', function () {
    $('.refund_reason_section').css('display','inline-block');
    $('.refund_reason_title').text("<?php echo $this->lang->line('initiate_refund'); ?>");
    $('#form_add_refund_reason').validate().resetForm();
    $('.refund_reason_confirmation_section').css('display','none');
});
$('#form_add_refund_reason').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid())
    {
        jQuery.ajax({
            type : "POST",
            dataType : "json",
            url : BASEURL+"backoffice/order/ajaxinitiaterefund",
            data : $('#form_add_refund_reason').serialize(),
            cache: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#add_refund_reason').modal('hide');
                if (response.paymentIntentstatus == "refunded" || response.paymentIntentstatus == "partial refunded" || response.tips_paymentIntentstatus == "refunded") {
                    var refundbox = bootbox.alert({
                      message: "<?php echo $this->lang->line('refund_initiated'); ?>",
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                    $('#quotes-main-loader').hide();
                    grid.getDataTable().fnDraw();
                } else if(response.error){
                    var box = bootbox.alert({
                      message: "<?php echo $this->lang->line('intiate_stripe_refunderror'); ?>",
                      buttons: {
                          ok: {
                              label:"<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      box.modal('hide');
                    }, 10000);
                    $('#quotes-main-loader').hide();
                    grid.getDataTable().fnDraw();
                }else if(response.error_message){
                    var refundbox = bootbox.alert({
                      message: response.error_message,
                      buttons: {
                          ok: {
                              label: "<?php echo $this->lang->line('ok'); ?>",
                          }
                      }
                    });
                    setTimeout(function() {
                      refundbox.modal('hide');
                    }, 10000);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
            }
        });
    }
    return false;
});
//Code for add the amount for refund :: Start
function chkrefundoption(value)
{
    if(value=='partial')
    {
        $('#partial_refundedamtid').css('display','');
    }
    else
    {
        $('#partial_refundedamtid').css('display','none');
        $('#partial_refundedamt').val('');
    }    
}
//Code for add the amount for refund :: End
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>