<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar'); 
if($this->input->post())
{
    foreach($this->input->post() as $key => $value){
        if(!is_array($value)){
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } 
}else
{
    $FieldsArray = array('entity_id','order_id','user_id','restaurant_id','coupon_id','table_id','total_rate','subtotal','tax_rate','tax_type','coupon_discount','coupon_name', 'coupon_amount', 'restaurant_name', 'user_name', 'item_detail', 'coupon_type','extra_comment', 'service_fee_type','service_fee','tip_amount','delivery_charge','creditcard_fee_type','creditcard_fee');

    foreach ($FieldsArray as $key) {
        if($key!='item_detail'){
            $$key = @htmlspecialchars($editorder_detail->$key);
        }
    }
}
if(isset($editorder_detail) && $editorder_detail !="")
{
    $add_label     = $this->lang->line('title_admin_delivery_pickup_order_edit');        
    $form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_delivery_pickup_order_details/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($editorder_detail->entity_id));

    $menu_item = 1;
    if($editorder_detail->item_detail && !empty($editorder_detail->item_detail))
    {
        $item_detail = $editorder_detail->item_detail;
    }
}
/*else
{
    $add_label    = $this->lang->line('title_admin_orderadd');
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $menu_item = 1;
}*/
$restaurant_id = isset($_POST['restaurant_id'])?$_POST['restaurant_id']:$restaurant_id;
$menu_detail     = $this->order_model->getOrderItem($restaurant_id);
$Menucategory =  $this->order_model->getItemCategory($restaurant_id);
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('delivery_word').' / '.$this->lang->line('pickup_word').' '.$this->lang->line('orders'); ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <?php
            if($_SESSION['outofstock'])
            { ?>
                <div class="alert alert-danger">
                     <?php echo $_SESSION['outofstock'];
                     unset($_SESSION['outofstock']);
                     ?>
                </div>
            <?php } ?>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label;?> (<?php echo $entity_id; ?>)</div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add_dinein" name="form_add_dinein" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" style= "display: none;" class="frame-load">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
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
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>">
                                                <input type="hidden" name="itemrefund_reason" id="itemrefund_reason" value="">
                                                <input type="hidden" name="is_formsubmit" id="is_formsubmit" value="">
                                                <input type="hidden" name="user_name" id="user_name" value="<?php echo $user_name ?>">
                                                <input type="hidden" name="restaurant_name" id="restaurant_name" value="<?php echo $restaurant_name ?>">
                                                <!-- <input type="hidden" name="tip_amount" id="tip_amount" value="<?php echo $tip_amount; ?>"> -->
                                                <label class="control-label" style="padding-left:15px; padding-top: 0px;min-height:1px"><?php echo $this->lang->line('user') ?></label>: <label class="align-middle"> <?php echo $user_name;?></label>
                                            </div>

                                            <div class="col-md-6 text-center">
                                                <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id;?>" id="restaurant_id">
                                                <label class="control-label" style="padding-left:15px; padding-top: 0px;min-height:1px"><?php echo $this->lang->line('title_admin_restaurant') ?></label>: <label class="align-middle"> <?php echo $restaurant_name;?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                     /*echo "<pre>"; print_r($menu_item);
                                     echo "<br>++++++++++++++++++++++<br>";
                                     echo "<pre>"; print_r($item_detail); exit;*/
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?></label>
                                        <div class="col-md-9">
                                            <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th><?php echo $this->lang->line('item')?>#</th>
                                                        <th><?php echo $this->lang->line('item_name')?></th>
                                                        <th><?php echo $this->lang->line('quantity')?></th>
                                                        <th><?php echo $this->lang->line('item_total')?></th>
                                                        <th><?php echo $this->lang->line('total')?></th>
                                                        <th><?php echo $this->lang->line('action')?></th>
                                                    </tr>
                                                </thead>                                        
                                                <tbody>
                                                    <?php if(!empty($item_detail)){
                                                    $cnti=1;
                                                    foreach ($item_detail as $key => $value) {
                                                        $subtotal_val = ($value['rate'])?$value['rate']:0;
                                                        $subtotal_val = ($value['offer_price'])?$value['offer_price']:$subtotal_val;
                                                        $cart_key = $key; ?>
                                                        <tr role="row" class="heading">
                                                            <td><?php echo $key+1; ?></td>
                                                            <td><?php echo $value['item_name']; ?>
                                                                <ul class="ul-disc">
                                                                <?php if (!empty($value['addons_category_list'])) {
                                                                    foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
                                                                        <li><span><?php echo $cat_value['addons_category']; ?>
                                                                        <?php if (!empty($cat_value['addons_list'])) {
                                                                            foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
                                                                                <?php echo $add_value['add_ons_name']; ?>  <?php echo $order_details[0]['currency_symbol']; ?><?php echo $add_value['add_ons_price'];
                                                                                $subtotal_val = $subtotal_val + $add_value['add_ons_price'];
                                                                                 ?>
                                                                            <?php }
                                                                        } ?>
                                                                        </span></li>
                                                                    <?php }
                                                                } ?>
                                                                </ul>
                                                                <?php
                                                                if(!empty($value['comment'])){
                                                                    ?><div><b><?php echo $this->lang->line('item_comment')?>:</b> <?php echo $value['comment']; ?></div><?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><input type="text" name="qty_no[<?php echo $cnti ?>]" id="qty_no<?php echo $cnti ?>" value="<?php echo $value['qty_no']; ?>" maxlength="3" data-required="1" onfocusout="qty(this.id,<?php echo $cnti ?>,1)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                                <input type="hidden" name="old_qty_no[<?php echo $cnti ?>]" id="old_qty_no<?php echo $cnti ?>" value="<?php echo $value['qty_no']; ?>"/>
                                                                <input type="hidden" name="item_id[<?php echo $cnti ?>]" id="item_id<?php echo $cnti ?>" value="<?php echo $value['item_id']; ?>"/>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="base_price[<?php echo $cnti ?>]" id="base_price<?php echo $cnti ?>" value="<?php echo $subtotal_val; ?>" maxlength="20" data-required="1" class="form-control base_price validate-class" readonly=""  />
                                                            </td>
                                                            <td>
                                                                <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $cnti ?>]" id="rate<?php echo $cnti ?>" value="<?php echo $value['itemTotal']; ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                            </td>
                                                            <td>
                                                            <?php if(count($item_detail)>1) { 
                                                                $orderflagval = ($value['order_flag'] != '') ? intval($value['order_flag']) : 0; ?>
                                                                <a onclick="deleteOrderItem(<?php echo $value['item_id']; ?>,<?php echo $orderflagval?>,<?php echo $entity_id;?>,<?php echo $cart_key;?>)"  title="<?php echo $this->lang->line('reject')?>" class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-ban" aria-hidden="true"></i></a>
                                                             <?php $cnti++;
                                                             } else { ?>   
                                                                -
                                                             <?php } ?>   
                                                            </td>
                                                        </tr>
                                                    <?php } } 
                                                     ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($coupon_array)){ ?>
                                        <input type="hidden" class="form-control" name="multiple_coupon" id="multiple_coupon" value="yes">
                                    <?php    
                                    foreach ($coupon_array as $cp_key => $cp_value) { ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?><?php echo ($cp_key+1);?></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="coupon_name[]" readonly id="coupon_name<?php echo ($cp_key);?>" value="<?php echo $cp_value['coupon_name']; ?>">
                                            <input type="hidden" class="form-control" name="coupon_id[]" readonly id="coupon_id<?php echo ($cp_key);?>" value="<?php echo $cp_value['coupon_id']; ?>">                                           
                                        </div>
                                    </div>                                    
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?><?php echo ($cp_key+1);?></label>
                                        <div class="col-md-5">
                                            <input class="coupon_amountcls" coupon_typeval="<?php echo $cp_value['coupon_type']; ?>" type="text" data-value="" name="coupon_amount[]" id="coupon_amount<?php echo ($cp_key);?>" value="<?php echo ($cp_value['coupon_amount'])?$cp_value['coupon_amount']:'' ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;cursor: not-allowed;
                                            background-color: #eeeeee;border: 1px solid #ABABAB;font-size:14px;color:#333333;height:34px"/><label class="coupon-type" style="display:inline-block;padding-left: 10px;"><?php echo ($cp_value['coupon_type'] == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type[]" id="coupon_type<?php echo ($cp_key);?>" value="<?php echo $cp_value['coupon_type']; ?>">
                                        </div>
                                    </div> 
                                    <?php } } else { ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="coupon_name[]" readonly id="coupon_name" value="<?php echo $coupon_name; ?>">                                                                          
                                                <input type="hidden" class="form-control" name="coupon_id[]" readonly id="coupon_id" value="<?php echo $coupon_id; ?>"> 
                                                <input type="hidden" class="form-control" name="multiple_coupon" id="multiple_coupon" value="no">
                                        </div>
                                    </div>                                    
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="coupon_amount[]" id="coupon_amount" value="<?php echo ($coupon_amount)?$coupon_amount:'' ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;"/><label class="coupon-type" style="display:inline-block;padding-left: 10px;"><?php echo ($coupon_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type[]" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div> 
                                    <?php } ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control" style="width:79%;display:inline-block;" readonly=""/><label class="amount-type" style="display:inline-block;padding-left: 10px;"><?php echo ($tax_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div> 

                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="service_fee" id="service_fee" value="<?php echo $service_fee ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;"/><label class="service-type" style="display:inline-block;padding-left: 10px;"><?php echo ($service_fee_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="service_fee_type" id="service_fee_type" value="<?php echo $service_fee_type; ?>">
                                        </div>
                                    </div>

                                    <?php if($creditcard_fee_type && $creditcard_fee > 0) { ?>
                                        <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('creditcard_fee') ?></label>
                                            <div class="col-md-5">
                                                <input type="text" data-value="" name="creditcard_fee" id="creditcard_fee" value="<?php echo $creditcard_fee ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;"/><label class="creditcard-fee-type" style="display:inline-block;padding-left: 10px;"><?php echo ($creditcard_fee_type == 'Percentage')?'%':'' ?></label>
                                                <input type="hidden" name="creditcard_fee_type" id="creditcard_fee_type" value="<?php echo $creditcard_fee_type; ?>">
                                            </div>
                                        </div> 
                                    <?php } ?>
                                    <?php if($delivery_charge>0 && $delivery_charge!=null) { ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('delivery_charge') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery_charge" id="delivery_charge" value="<?php echo ($delivery_charge); ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            
                                        </div>
                                    </div> 
                                    <?php }else {?>
                                        <input type="hidden" name="delivery_charge" id="delivery_charge" value="0" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                    <?php } ?>
                                    <?php if($tip_amount>0 && $tip_amount!=null) { ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('driver_tip') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="tip_amount" id="tip_amount" value="<?php echo ($tip_amount); ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            
                                        </div>
                                    </div> 
                                    <?php }else {?>
                                        <input type="hidden" name="tip_amount" id="tip_amount" value="0" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                    <?php } ?>

                                    <?php if($wallet_history) { ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('wallet_discount') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="wallet_history" id="wallet_history" value="<?php echo number_format((float)$wallet_history->amount,2,'.',''); ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <input type="hidden" name="wallet_to_be_refunded" id="wallet_to_be_refunded" value="" maxlength="10" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                        <input type="hidden" name="wallet_history" id="wallet_history" value="0"/>
                                    <?php } ?>                                    

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <input type="hidden" name="subtotal_old" id="subtotal_old" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <?php /* //taxes and fees :: start ?>
                                    <div class="form-group add_taxes_fees">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('taxes_fees') ?></label>
                                        <div class="col-md-4 add_tax_col">
                                            <input type="text" data-value="" name="taxes_fees" id="taxes_fees" value="0" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                        <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right">
                                                <div id="servicetax_infodiv">
                                                    <span class="custom_service"><?php echo $this->lang->line('service_tax'); ?> <span id="servicetaxtype_info"></span></span> : <span class="service_price" id="servicetax_info"></span>
                                                </div>
                                                <div id="servicefee_infodiv">
                                                    <span class="custom_service"><?php echo $this->lang->line('service_fee'); ?> <span id="servicefeetype_info"></span></span> : <span class="service_price" id="servicefee_info"></span>
                                                </div>
                                                <div id="creditcardfee_infodiv">
                                                    <span class="custom_service"><?php echo $this->lang->line('creditcard_fee'); ?> <span id="creditcardfeetype_info"></span></span> : <span class="service_price" id="creditcardfee_info"></span>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    <?php */ //taxes and fees :: end ?>
                                    <div class="form-group" style="<?php echo ($this->session->userdata('AdminUserType') == 'MasterAdmin') ? 'display: block;' : 'display: none;' ; ?>">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="total_rate" id="total_rate" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <input type="hidden" name="total_rate_old" id="total_rate_old" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div>
                                    <?php if(!empty($extra_comment) && false){ ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('view_comment') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="extra_comment" id="extra_comment" value="<?php echo ($extra_comment)?$extra_comment:''; ?>" class="form-control" readonly=""/>
                                            </div>
                                        </div> 
                                    <?php } ?>
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('update_order') ?></button>&ensp;
                                        <a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view"><?php echo $this->lang->line('back_to_delivery_pickup_view') ?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<div id="order_refund_reason" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title reject_refund_order_title"><?php echo $this->lang->line('reject_item'); ?></h4>
      </div>
      <div class="modal-body">        
        <div class="reject_refund_confirmation_section">
            <form id="form_order_refund_reason" name="form_order_refund_reason" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-12">
                        <input type="hidden" name="del_item_id" id="del_item_id" value="">
                        <input type="hidden" name="del_order_flag" id="del_order_flag" value="">
                        <input type="hidden" name="del_order_id" id="del_order_id" value="">
                        <input type="hidden" name="cart_key" id="cart_key" value="">
                        <div class="form-group other-reject-reason">
                            <label class="control-label col-md-4"><?php echo $this->lang->line('enter_reason') ?><span class="required">*</span></label>
                            <div class="col-sm-8">
                                <textarea name="refund_reasontext" id="refund_reasontext" class="form-control input-sm" maxlength="250"></textarea>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-12 text-center">
                             <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                             <button type="button" data-dismiss="modal" class="btn btn-sm default-btn filter-submit margin-bottom" value="Cancel"><span><?php echo $this->lang->line('cancel')?></span></button>
                             <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page1" id="submit_page1" value="Save"><span><?php echo $this->lang->line('ok')?></span></button>
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
<div id="edititem_refund_reason" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title reject_refund_order_title"><?php echo $this->lang->line('reject_item'); ?></h4>
      </div>
      <div class="modal-body">        
        <div class="reject_refund_confirmation_section">
            <form id="form_edititem_refund_reason" name="form_edititem_refund_reason" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-12">                                               
                        <div class="form-group other-reject-reason">
                            <label class="control-label col-md-4"><?php echo $this->lang->line('enter_reason') ?><span class="required">*</span></label>
                            <div class="col-sm-8">
                                <textarea name="itemrefund_reasontemp" id="itemrefund_reasontemp" class="form-control input-sm" maxlength="250"></textarea>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-12 text-center">
                             <div id="loadingModal" class="loader-c display-no"><img  src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"  ></div>
                             <button type="button" data-dismiss="modal" class="btn btn-sm default-btn filter-submit margin-bottom" value="Cancel"><span><?php echo $this->lang->line('cancel')?></span></button>
                             <button type="submit" class="btn btn-sm  default-btn filter-submit margin-bottom" name="submit_page2" id="submit_page2" value="Save"><span><?php echo $this->lang->line('ok')?></span></button>
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
<div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    calculation();
});

// addons changes (end)
function qty(id,num,frmqty)
{
    $('#'+id).keyup(function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });
    
    var myTag = parseFloat($('#base_price'+num).val()).toFixed(2);
    var qtydata = parseInt($('#qty_no'+num).val());    
    if(isNaN(qtydata) || qtydata==0){    
        //changes for base price start
        if(myTag){
            qtydata = 1;
        }
        //changes for base price end
        $('#qty_no'+num).val(qtydata);
    }
    //alert(myTag);

    var oldqtydata = parseInt($('#old_qty_no'+num).val()); 
    if(oldqtydata<qtydata)
    {
        bootbox.alert("<?php echo $this->lang->line('decrease_quantitymsg');?>");
        $('#qty_no'+num).val(oldqtydata);
        qtydata = oldqtydata;
    }
    var total = parseFloat(qtydata * myTag);

    if(!isNaN(total)){    
        $('#rate'+num).val(total.toFixed(2));
    }
    //changes for addons start
    var price = 0;    
    //changes for base price start
    if(myTag){
        price += parseFloat(myTag);
    }
    if(frmqty==1 || frmqty==2) // myTag == '' &&  - removed this as part of base price changes
    {
       var total = parseFloat(qtydata * price).toFixed(2);
       if(!isNaN(total)){    
            $('#rate'+num).val(total);
        }
    }
    var pricecal = total;
    pricecal = parseFloat(pricecal).toFixed(2);
    // changes for addons end
    calculation();
}

//calculate total rate
function calculation()
{   
    var delivery_charge = $('#delivery_charge').val();
    var wallet_history = $('#wallet_history').val();
    var tip_amount = $('#tip_amount').val();
    var multiple_coupon = $('#multiple_coupon').val();    
    
    var sum = 0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != '')
        {
            sum += parseFloat($(this).val()); 
        }
    });

    $('#subtotal').val(sum.toFixed(2));
    //coupon
    var cpn_amt = 0;
    if(multiple_coupon =='yes')
    {
        $('.coupon_amountcls').each(function()
        {
            var amount = $(this).val();
            var type = $(this).attr('coupon_typeval'); 
            if(type == 'Percentage' && amount != '')
            {
                var cpn = (parseFloat(sum*amount)/100);
                cpn = cpn.toFixed(2);
                cpn_amt = parseFloat(cpn_amt)+parseFloat(cpn);
            }
            else if(type == 'Amount' && amount != '')
            {
                cpn_amt =  parseFloat(cpn_amt)+parseFloat(amount);
            }
            else if(amount != '')
            {
               cpn_amt =  parseFloat(cpn_amt)+parseFloat(amount);
            }            
        });
        cpn_amt = cpn_amt.toFixed(2);
    }
    else
    {
        var type = $('#coupon_type').val(); 
        var amount = $('#coupon_amount').val(); 
        var sing = (type == "Percentage") ? "%" : '';
        $('.coupon-type').html(sing);
        if(type == 'Percentage' && amount != ''){
            var cpn = parseFloat(sum*amount)/100;
            cpn_amt = cpn;
        }else if(type == 'Amount' && amount != ''){
            cpn_amt =  amount;
        }
        else if(amount != ''){
            cpn_amt =  amount;
        }
        cpn_amt = cpn_amt.toFixed(2);
    }

    //tax
    var tax = $('#tax_rate').val();
    var taxval = 0;
    if($('.amount-type').html() == '' && !isNaN(tax) && tax != '')
    {
        //sum = sum + parseFloat(tax); 
        taxval = tax;
    }
    else if(!isNaN(tax) && tax != '')
    {
        var taxval = Number(parseFloat(sum*tax)/100).toFixed(2);
        //sum += parseInt(taxs);
    }

    //service fee
    var service_fee = $('#service_fee').val();
    var service_feeval = 0;
    if($('.service-type').html() == '' && !isNaN(service_fee) && service_fee != '')
    {
        //sum = sum + parseFloat(tax); 
        service_feeval = service_fee;
    }
    else if(!isNaN(service_fee) && service_fee != '')
    {
        var service_feeval = Number(parseFloat(sum*service_fee)/100).toFixed(2);
        //sum += parseInt(taxs);
    }

    //credit card fee
    var creditcard_fee = $('#creditcard_fee').val();
    var creditcard_feeval = 0;
    if($('.creditcard-fee-type').html() == '' && !isNaN(creditcard_fee) && creditcard_fee != '')
    {
        creditcard_feeval = creditcard_fee;
    }
    else if(!isNaN(creditcard_fee) && creditcard_fee != '')
    {
        var creditcard_feeval = Number(parseFloat(sum*creditcard_fee)/100).toFixed(2);
    }
    //taxes and fees :: start
    /*var taxes_fees = parseFloat(taxval) + parseFloat(service_feeval) + parseFloat(creditcard_feeval);
    $('#taxes_fees').val(taxes_fees.toFixed(2));
    if(parseFloat(taxval) > 0) {
        if ($('.amount-type').html() != '' && !isNaN(tax) && tax != '') {
            $('#servicetaxtype_info').text('('+tax+'%)');
        } else {
            $('#servicetaxtype_info').text('');
        }
        $('#servicetax_info').text(parseFloat(taxval));
    } else {
        $('#servicetax_infodiv').css('display','none');
    }
    if(parseFloat(service_feeval) > 0) {
        if ($('.service-type').html() != '' && !isNaN(service_fee) && service_fee != '') {
            $('#servicefeetype_info').text('('+service_fee+'%)');
        } else {
            $('#servicefeetype_info').text('');
        }
        $('#servicefee_info').text(parseFloat(service_feeval));
    } else {
        $('#servicefee_infodiv').css('display','none');
    }
    if(parseFloat(creditcard_feeval) > 0) {
        if ($('.creditcard-fee-type').html() != '' && !isNaN(creditcard_fee) && creditcard_fee != '') {
            $('#creditcardfeetype_info').text('('+creditcard_fee+'%)');
        } else {
            $('#creditcardfeetype_info').text('');
        }
        $('#creditcardfee_info').text(parseFloat(creditcard_feeval));
    } else {
        $('#creditcardfee_infodiv').css('display','none');
    }*/
    //taxes and fees :: end

    
    var wallet_to_be_refunded = 0;
    var new_wallet_balance = 0;
    if(!isNaN(wallet_history) && wallet_history>0){
        new_wallet_balance = sum;
        wallet_to_be_refunded = wallet_history - sum;
        wallet_to_be_refunded = parseFloat(wallet_to_be_refunded);
        $('#wallet_history').val(new_wallet_balance);
        $('#wallet_to_be_refunded').val(wallet_to_be_refunded.toFixed(2));
    } 


    if(!isNaN(cpn_amt) && cpn_amt>0){
        sum = sum - parseFloat(cpn_amt);
    }

    if(!isNaN(service_feeval) && service_feeval>0){
        sum = sum + parseFloat(service_feeval);
    }
    if(!isNaN(creditcard_feeval) && creditcard_feeval>0){
        sum = sum + parseFloat(creditcard_feeval);
    }
    if(!isNaN(taxval) && taxval>0){
        sum = sum + parseFloat(taxval);
    }

    if(!isNaN(tip_amount) && tip_amount>0){
        sum = sum + parseFloat(tip_amount);
    }
    if(!isNaN(delivery_charge) && delivery_charge>0){
        sum = sum + parseFloat(delivery_charge);
    }    
    
    if(!isNaN(wallet_history) && wallet_history > 0 && new_wallet_balance > 0){
        sum = sum - parseFloat(new_wallet_balance);
    }
    console.log(sum);
    if(!isNaN(sum)){
        $('#total_rate').val(sum.toFixed(2));
    }
}

jQuery(document).ready(function() {
    validateDynamicMenu();   
});    
function validateDynamicMenu(){
    $('#form_add_dinein').find('.validate-class').each(function(){
        $(this).rules("add", 
        {
            required: true
        });
    });
}

//Code for item delete from current order :: Start
function deleteOrderItem(item_id,order_flag,entity_id,cart_key)
{
    $('#del_item_id').val(item_id);
    $('#del_order_flag').val(order_flag);
    $('#cart_key').val(cart_key);
    $('#del_order_id').val(entity_id);
    $('#order_refund_reason').modal('show');
}

$('#form_add_dinein').submit(function(e){
    var is_formsubmit = $('#is_formsubmit').val();
    if(is_formsubmit=='yes'){

    }
    else
    {
        e.preventDefault();   
        $('#edititem_refund_reason').modal('show');
    }
});

$('#form_edititem_refund_reason').submit(function(e){ 
    e.preventDefault(); 
    $(this).validate();
    if($(this).valid())
    {
      var itemrefund_reason = $('#itemrefund_reasontemp').val();
      $('#itemrefund_reason').val(itemrefund_reason);
      $('#is_formsubmit').val('yes');
      //$("#form_add_dinein").submit();
      $( "#submit_page").trigger("click");
      $('#edititem_refund_reason').modal('hide');
    }
});

$('#form_order_refund_reason').submit(function(e){
    e.preventDefault();
    $(this).validate();
    if($(this).valid())
    {
        var item_id = $('#del_item_id').val();
        var order_flag = $('#del_order_flag').val();
        var cart_key = $('#cart_key').val();
        var entity_id = $('#del_order_id').val();
        var entity_id = $('#del_order_id').val();
        var refund_reasontext = $('#refund_reasontext').val();

        jQuery.ajax({
            type : "POST",
            dataType : "json",
            url : BASEURL+"backoffice/order/ajaxOrderItemDelete",
            data : {'entity_id':entity_id,'item_id':item_id,'order_flag':order_flag,'refund_reasontext':refund_reasontext,'cart_key':cart_key},
            beforeSend: function(){
                $('#quotes-main-loader').show();
                $('#order_refund_reason').modal('hide');
            },
            success: function(response)
            {
                $('#quotes-main-loader').hide();
                $('#order_refund_reason').modal('hide');
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
                else
                {
                    location.reload();
                }
                
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
                alert(errorThrown);
            }
       });
    }
    return false;
});

//Code for item delete from current order :: End
$(document).ready(function(){
    $('#form_add_dinein').submit(function() {
        $('.validate-class').each(function(){
            var id = $(this).attr('id');
            if($('#'+id).val() == ''){
                $('#'+id).attr('required',true);
                $('#'+id).addClass('error');
            }
        });
        $('.addon-validate').each(function() {
            var checkbox_count = $(this).find('.check_addons').filter(':checked').length;
            var radio_count = $(this).find('.radio_addons').filter(':checked').length;
            if(checkbox_count == 0){
                $(this).find('.check_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
            if(radio_count == 0){
                $(this).find('.radio_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
        });
        if(jQuery('#form_add_dinein').valid()) {
            //form.submit();
            //$('#edititem_refund_reason').modal('show');
        }else{
            return false;
        }
    });
});
</script>
<?php $this->load->view(ADMIN_URL.'/addons_max_selection');?>
<?php $this->load->view(ADMIN_URL.'/footer');?>