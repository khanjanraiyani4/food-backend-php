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
                                            <th><?php echo $this->lang->line('item')?>#</th>
                                            <th><?php echo $this->lang->line('item_name')?></th>
                                            <th><?php echo $this->lang->line('quantity')?></th>
                                            <th><?php echo $this->lang->line('total_rate')?></th>
                                        </tr>
                                    </thead>                                        
                                    <tbody>
                                        <?php if(!empty($item_detail)){
                                        foreach ($item_detail as $key => $value) { ?>
                                            <tr role="row" class="heading">
                                                <td><?php echo $key+1; ?></td>
                                                <td><?php echo $value['item_name']; ?>
                                                    <ul class="ul-disc">
                                                    <?php if (!empty($value['addons_category_list'])) {
                                                        foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
                                                            <li><h6><?php echo $cat_value['addons_category']; ?></h6></li>
                                                            <ul class="ul-cir">
                                                            <?php if (!empty($cat_value['addons_list'])) {
                                                                foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
                                                                    <li><?php echo $add_value['add_ons_name']; ?>  <?php echo $add_value['add_ons_price']; ?> <?php echo $order_details[0]['currency_symbol']; ?></li>
                                                                <?php }
                                                            } ?>
                                                            </ul>
                                                        <?php }
                                                    } ?>
                                                    </ul>
                                                </td>
                                                <td><?php echo $value['qty_no']; ?></td>
                                                <td><?php echo $value['itemTotal'].' '.$restaurant->currency_symbol; ?></td>
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
                                        <td><?php echo $odetails[0]->subtotal.' '.$restaurant->currency_symbol; ?></td>
                                    </tr>
                                    <!-- service tax changes start -->
                                    <?php if($odetails[0]->tax_rate != 0) { ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('service_tax')?> <?php echo ($odetails[0]->tax_type == 'Percentage')?'('.$odetails[0]->tax_rate.'%)':''; ?></th>
                                            <?php $tax_amountdis = 0;
                                            if($odetails[0]->tax_type == 'Percentage'){
                                                $tax_amountdis = ($odetails[0]->subtotal * $odetails[0]->tax_rate) / 100;
                                               
                                            }else{
                                                $tax_amountdis = $odetails[0]->tax_rate; 
                                                
                                            }
                                            //$tax_amountdis = number_format($tax_amountdis, 2, '.', '');
                                            $tax_amountdis = round($tax_amountdis,2).' '.$restaurant->currency_symbol;
                                            ?>
                                            <td><?php echo $tax_amountdis; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <!-- service tax changes end -->
                                    <!-- service fee changes start -->
                                    <?php 
                                        if($odetails[0]->service_fee != 0) { 
                                            if($odetails[0]->service_fee_type == 'Percentage'){
                                                $service_amount = ($odetails[0]->subtotal * $odetails[0]->service_fee) / 100; 
                                            } else {
                                                $service_amount = $odetails[0]->service_fee; 
                                            }
                                            $service_amount = round($service_amount,2).' '.$restaurant->currency_symbol;
                                    ?>
                                    <tr>
                                        <th>
                                            <?php echo $this->lang->line('service_fee');?>&nbsp;
                                            <?php echo ($odetails[0]->service_fee_type == 'Percentage') ? '('.$odetails[0]->service_fee.'%)' : '';?>
                                        </th>
                                        <td><?php echo $service_amount; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <!-- service fee changes end -->

                                    <!-- creditcard fee changes start -->
                                    <?php 
                                        if($odetails[0]->creditcard_fee != 0) { 
                                            if($odetails[0]->creditcard_fee_type == 'Percentage'){
                                                $creditcard_amount = ($odetails[0]->subtotal * $odetails[0]->creditcard_fee)/100; 
                                            } else {
                                                $creditcard_amount = $odetails[0]->creditcard_fee; 
                                            }
                                            $creditcard_amount = round($creditcard_amount,2).' '.$restaurant->currency_symbol;
                                    ?>
                                    <tr>
                                        <th>
                                            <?php echo $this->lang->line('creditcard_fee');?>&nbsp;
                                            <?php echo ($odetails[0]->creditcard_fee_type == 'Percentage') ? '('.$odetails[0]->creditcard_fee.'%)' : '';?>
                                        </th>
                                        <td><?php echo $creditcard_amount; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <!-- creditcard fee changes end -->


                                    <?php if($odetails[0]->delivery_charge) { ?>
                                        <tr> <!--for delivery charge-->
                                            <th><?php echo $this->lang->line('delivery_charge')?></th>
                                            <td><?php echo $odetails[0]->delivery_charge.' '.$restaurant->currency_symbol; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th><?php echo $this->lang->line('coupon_discount')?></th>
                                        <td><?php echo $odetails[0]->coupon_amount; ?><?php echo ($odetails[0]->coupon_type == 'Percentage')?'%':'' ?></td>
                                    </tr>
                                    <?php if($wallet_history) { ?>
                                        <tr> <!--for used earning points-->
                                            <th><?php echo $this->lang->line('wallet_discount')?></th>
                                            <td><?php echo $wallet_history->amount.' '.$restaurant->currency_symbol; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th><?php echo $this->lang->line('total_rate')?></th>
                                        <td><?php echo $odetails[0]->total_rate.' '.$restaurant->currency_symbol; ?></td>
                                    </tr>
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
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>