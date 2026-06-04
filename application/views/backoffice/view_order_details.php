<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('order_details')?></h4>
      </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('order_details')?></div>
                            <div class="actions"></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><?php echo $this->lang->line('s_no')?></th>
                                            <th><?php echo $this->lang->line('item_name')?></th>
                                            <th><?php echo $this->lang->line('quantity')?></th>
                                            <th><?php echo $this->lang->line('total_rate')?></th>
                                        </tr>
                                    </thead>                                        
                                    <tbody>
                                        <?php if(!empty($item_detail)){
                                        foreach ($item_detail as $key => $value) { ?>
                                            <tr role="row" class="heading">
                                                <td align="center" ><?php echo $key+1; ?></td>
                                                <td><?php echo $value['item_name']; ?>
                                                    <ul class="ul-disc">
                                                    <?php if (!empty($value['addons_category_list'])) {
                                                        foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
                                                            <li><h6><?php echo $cat_value['addons_category']; ?></h6></li>
                                                            <ul class="ul-cir">
                                                            <?php if (!empty($cat_value['addons_list'])) {
                                                                foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
                                                                    <li><?php echo $add_value['add_ons_name']; ?>  <?php echo currency_symboldisplay(number_format((float)$add_value['add_ons_price'],2,'.',''),$order_details[0]['currency_symbol']); ?></li>
                                                                <?php }
                                                            } ?>
                                                            </ul>
                                                        <?php }
                                                    } ?>
                                                    </ul>
                                                    <?php
                                                    if(!empty($value['comment'])){
                                                        ?><div><b><?php echo $this->lang->line('item_comment')?>:</b> <?php echo $value['comment']; ?></div><?php
                                                    }
                                                    ?>
                                                </td>
                                                <td align="center" ><?php echo $value['qty_no']; ?></td>
                                                <td align="center" ><?php echo currency_symboldisplay(number_format((float)$value['itemTotal'],2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php } } 
                                        else { ?>
                                            <td colspan="4"><?php echo $this->lang->line('not_found')?></td>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-container table-scrollable">
                                <table class="table table-striped table-bordered table-hover">
                                    <?php if(!empty($odetails)) { ?>
                                    <tr>
                                        <th><?php echo $this->lang->line('order')?>#</th>
                                        <td><?php echo $entity_id; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('sub_total')?></th>
                                        <td><?php echo currency_symboldisplay(number_format((float)$odetails[0]->subtotal,2,'.',''),$currency->currency_symbol); ?></td>
                                    </tr>
                                    <!-- service tax changes start -->
                                    <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin') {
                                        $taxes_fees = 0;
                                        if($odetails[0]->tax_rate != '' && $odetails[0]->tax_rate != NULL && $odetails[0]->tax_rate > 0) {  ?>
                                            <tr>
                                                <th><?php echo $this->lang->line('service_tax')?> <?php echo ($odetails[0]->tax_type == 'Percentage')?'('.$odetails[0]->tax_rate.'%)':''; ?></th>
                                                <?php  $tax_amountdis = 0;
                                                if($odetails[0]->tax_type == 'Percentage'){
                                                    $tax_amountdis = ($odetails[0]->subtotal * $odetails[0]->tax_rate) / 100;
                                                   
                                                }else{
                                                    $tax_amountdis = $odetails[0]->tax_rate; 
                                                    
                                                }
                                                //$tax_amountdis = number_format($tax_amountdis, 2, '.', '');
                                                $tax_amountdis = round($tax_amountdis,2);
                                                $taxes_fees = $taxes_fees + number_format((float)$tax_amountdis,2,'.','');  ?>
                                                <td>+<?php echo currency_symboldisplay(number_format((float)$tax_amountdis,2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php  } ?>
                                        <!-- service tax changes end -->
                                        <!-- service fee changes start -->
                                        <?php 
                                            if($odetails[0]->service_fee != 0) { 
                                                if($odetails[0]->service_fee_type == 'Percentage'){
                                                    $service_amount = ($odetails[0]->subtotal * $odetails[0]->service_fee) / 100; 
                                                } else {
                                                    $service_amount = $odetails[0]->service_fee; 
                                                }
                                                $service_amount = round($service_amount,2);
                                                $taxes_fees = $taxes_fees + number_format((float)$service_amount,2,'.','');
                                         ?>
                                        <tr>
                                            <th>
                                                <?php echo $this->lang->line('service_fee');?>&nbsp;
                                                <?php echo ($odetails[0]->service_fee_type == 'Percentage') ? '('.$odetails[0]->service_fee.'%)' : '';?>
                                            </th>
                                            <td>+<?php echo currency_symboldisplay(number_format((float)$service_amount,2,'.',''),$currency->currency_symbol); ?></td>
                                        </tr>
                                        <?php  } ?>
                                        <!-- service fee changes end -->
                                        <!-- creditcard fee start -->
                                        <?php if($odetails[0]->creditcard_fee != 0) {  ?>
                                            <tr>
                                                <th><?php echo $this->lang->line('creditcard_fee')?> <?php echo ($odetails[0]->creditcard_fee_type == 'Percentage')?'('.$odetails[0]->creditcard_fee.'%)':''; ?></th>
                                                <?php  $creditcard_feedis = 0;
                                                if($odetails[0]->creditcard_fee_type == 'Percentage'){
                                                    $creditcard_feedis = ($odetails[0]->subtotal * $odetails[0]->creditcard_fee) / 100;
                                                   
                                                }else{
                                                    $creditcard_feedis = $odetails[0]->creditcard_fee; 
                                                    
                                                }
                                                $creditcard_feedis = number_format(round($creditcard_feedis,2),2);
                                                $taxes_fees = $taxes_fees + number_format((float)$creditcard_feedis,2,'.','');  ?>
                                                <td>+<?php echo currency_symboldisplay(number_format((float)$creditcard_feedis,2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php  } ?>
                                        <!-- creditcard fee end -->
                                        <?php if($odetails[0]->delivery_charge && $odetails[0]->order_delivery!='DineIn') { ?>
                                            <tr> <!--for delivery charge-->
                                                <th><?php echo $this->lang->line('delivery_charge')?></th>
                                                <td>+<?php echo currency_symboldisplay(number_format((float)$odetails[0]->delivery_charge,2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php 
                                        if($coupon_array && !empty($coupon_array)){
                                            foreach ($coupon_array as $cp_key => $cp_value) { ?>
                                            <tr>
                                                <th><?php echo $this->lang->line('coupon_discount').' '.'('.$cp_value['coupon_name'].')'?></th>
                                                <td>-<?php echo currency_symboldisplay(number_format((float)$cp_value['coupon_discount'],2,'.',''),$currency->currency_symbol); ?>
                                                </td>
                                            </tr>    
                                            <?php }
                                        } 
                                        else if($odetails[0]->coupon_amount && $odetails[0]->coupon_amount>0) { ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('coupon_discount').' '.'('.$odetails[0]->coupon_name.')'?></th>
                                            <td>-<?php echo currency_symboldisplay(number_format((float)$odetails[0]->coupon_discount,2,'.',''),$currency->currency_symbol); ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php if($wallet_history) { ?>
                                            <tr> <!--for used earning points-->
                                                <th><?php echo $this->lang->line('wallet_discount')?></th>
                                                <td>-<?php echo currency_symboldisplay(number_format((float)$wallet_history->amount,2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if(!is_null($odetails[0]->tip_amount) && !empty($odetails[0]->tip_amount)) { 
                                            $tip_percentage = ($odetails[0]->tip_percentage)?$odetails[0]->tip_percentage:'';
                                            $tip_percent_txt = ($tip_percentage)?' ('. $tip_percentage.'%)':''; ?>
                                            <tr>
                                                <th><?php echo $this->lang->line('driver_tip').$tip_percent_txt; ?></th>
                                                <td>+<?php echo currency_symboldisplay(number_format((float)$odetails[0]->tip_amount,2,'.',''),$currency->currency_symbol); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <!-- taxes and fees :: start -->
                                        <?php /* ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('taxes_fees')?>
                                                <div class="custom--tooltip" style="position:absolute;padding-left:6px;">
                                                    <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                    <span class="tooltiptext tooltip-right">
                                                        <div id="servicetax_infodiv">
                                                            <span class="custom_service"><?php echo $this->lang->line('service_tax'); ?> <span id="servicetaxtype_info"><?php echo ($odetails[0]->tax_type == 'Percentage')?'('.$odetails[0]->tax_rate.'%)':''; ?></span></span> : <span class="service_price" id="servicetax_info"><?php echo currency_symboldisplay(number_format((float)$tax_amountdis,2,'.',''),$currency->currency_symbol); ?></span>
                                                        </div>
                                                        <div id="servicefee_infodiv">
                                                            <span class="custom_service"><?php echo $this->lang->line('service_fee'); ?> <span id="servicefeetype_info"><?php echo ($odetails[0]->service_fee_type == 'Percentage') ? '('.$odetails[0]->service_fee.'%)' : '';?></span></span> : <span class="service_price" id="servicefee_info"><?php echo currency_symboldisplay(number_format((float)$service_amount,2,'.',''),$currency->currency_symbol); ?></span>
                                                        </div>
                                                        <div id="creditcardfee_infodiv">
                                                            <span class="custom_service"><?php echo $this->lang->line('creditcard_fee'); ?> <span id="creditcardfeetype_info"><?php echo ($odetails[0]->creditcard_fee_type == 'Percentage')?'('.$odetails[0]->creditcard_fee.'%)':''; ?></span></span> : <span class="service_price" id="creditcardfee_info"><?php echo currency_symboldisplay(number_format((float)$creditcard_feedis,2,'.',''),$currency->currency_symbol); ?></span>
                                                        </div>
                                                    </span>
                                                </div>
                                            </th>
                                            <td>+<?php echo currency_symboldisplay($taxes_fees,$currency->currency_symbol); ?></td>

                                        </tr>
                                        <?php */ ?>
                                        <!-- taxes and fees :: end -->
                                        <tr>
                                            <th><?php echo $this->lang->line('total_rate')?></th>
                                            <td><?php echo currency_symboldisplay(number_format((float)$odetails[0]->total_rate,2,'.',''),$currency->currency_symbol); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if(!is_null($odetails[0]->transaction_id) && !empty($odetails[0]->transaction_id)) { ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('transaction_number')?></th>
                                            <td><?php echo $odetails[0]->transaction_id; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if(!is_null($odetails[0]->extra_comment) && !empty($odetails[0]->extra_comment) && false) { ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('view_comment')?></th>
                                            <td><?php echo $odetails[0]->extra_comment; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if(!is_null($odetails[0]->delivery_instructions) && !empty($odetails[0]->delivery_instructions)) { ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('delivery_instructions')?></th>
                                            <td><?php echo $odetails[0]->delivery_instructions; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                        <tr><?php echo $this->lang->line('not_found')?></tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <?php if(($odetails[0]->order_status == 'cancel' && $odetails[0]->cancel_reason != '') || ($odetails[0]->order_status == 'rejected' && $odetails[0]->reject_reason != '')) { 
                                $reason = ($odetails[0]->order_status == 'rejected') ? $odetails[0]->reject_reason : $odetails[0]->cancel_reason;
                            ?>
                            <div class="table-container table-scrollable">
                                <table class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th><?php echo $this->lang->line('status')?></th>
                                        <td><?php echo $this->lang->line($odetails[0]->order_status).' - '.$reason; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <?php } ?>
                            <?php 
                            $payment_methodarr = array('stripe','paypal','applepay');
                            if($odetails[0]->refund_reason != '' && in_array(strtolower($odetails[0]->payment_option), $payment_methodarr)) { ?>
                                <div class="table-container table-scrollable">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tr>
                                            <th><?php echo $this->lang->line('refund_reason')?></th>
                                            <td><?php echo $odetails[0]->refund_reason; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php } ?>
                            <?php //thirdparty delivery fee :: start
                            if($odetails[0]->delivery_method == 'doordash' || $odetails[0]->delivery_method == 'relay') { // && $this->session->userdata('AdminUserType') == 'MasterAdmin' ?>
                                <div class="table-container table-scrollable">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tr>
                                            <th><?php echo $this->lang->line('delivery_method')?></th>
                                            <td><?php echo ucfirst($odetails[0]->delivery_method); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('third_party_delivery_charge')?></th>
                                            <td><?php echo currency_symboldisplay($odetails[0]->third_party_delivery_charge,$currency->currency_symbol); ?></td>
                                        </tr>
                                        <?php $third_party_delivery_data = ($odetails[0]->third_party_delivery_data)?unserialize($odetails[0]->third_party_delivery_data):array();
                                        if(!empty($third_party_delivery_data) && $odetails[0]->delivery_method == 'doordash') {
                                            if($third_party_delivery_data['doordash_id']) { ?>
                                                <tr>
                                                    <th><?php echo $this->lang->line('doordash_id')?></th>
                                                    <?php if($third_party_delivery_data['delivery_tracking_url'] || $odetails[0]->delivery_tracking_url) { ?>
                                                        <td><a href="<?php echo ($third_party_delivery_data['delivery_tracking_url']) ? $third_party_delivery_data['delivery_tracking_url'] : (($odetails[0]->delivery_tracking_url) ? $odetails[0]->delivery_tracking_url : 'javascript:void(0)'); ?>" target='_blank' data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('track_this_order'); ?>"><?php echo $third_party_delivery_data['doordash_id']; ?></a></td>
                                                    <?php } else { ?>
                                                        <td><?php echo $third_party_delivery_data['doordash_id']; ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                            <?php if($third_party_delivery_data['estimated_pickup_time']) { ?>
                                                <tr>
                                                    <th><?php echo $this->lang->line('estimated_pickup_time')?></th>
                                                    <td><?php echo $this->common_model->datetimeFormat($this->common_model->getZonebaseDate($third_party_delivery_data['estimated_pickup_time'])); ?></td>
                                                </tr>
                                            <?php }
                                        } ?>
                                        
                                    </table>
                                </div>
                                <?php if(!empty($thirdparty_driver_details) && (!empty($thirdparty_driver_details['first_name']) || !empty($thirdparty_driver_details['dasher_phone_number_for_customer']))){ ?>
                                    <div class="table-container table-scrollable">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <th colspan="2"><?php echo $this->lang->line('driver_details')?></th>
                                            </tr>
                                            <?php if(!empty($thirdparty_driver_details['first_name'])){ ?>
                                                <tr>
                                                    <th><?php echo $this->lang->line('name')?></th>
                                                    <td><?php echo !empty($thirdparty_driver_details['last_name'])?ucfirst($thirdparty_driver_details['first_name']).' '.ucfirst($thirdparty_driver_details['last_name']):ucfirst($thirdparty_driver_details['first_name']); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if(!empty($thirdparty_driver_details['dasher_phone_number_for_customer'])){ ?>
                                                <tr>
                                                    <th><?php echo $this->lang->line('phone_number')?></th>
                                                    <td><?php echo $thirdparty_driver_details['dasher_phone_number_for_customer']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                <?php }
                            }
                            //thirdparty delivery fee :: end ?>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>