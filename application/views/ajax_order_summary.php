<?php if (!empty($cart_details['cart_items'])) { ?>
	<div class="order-summary">
		<div class="order-summary-title">
			<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('order_summary') ?></h3>
		</div>
		<div class="order_from_res">
			<h5><?php echo $this->lang->line('order').' '.$this->lang->line('from') ?> : <?php echo $restaurant_name; ?></h5>
		</div>
		<div class="order-summary-content">
			<table>
				<tbody>
					<tr>
						<td><?php echo $this->lang->line('no_of_items') ?></td>
						<td><strong><?php echo count($cart_details['cart_items']); ?></strong></td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('sub_total') ?></td>
						<td><strong><?php echo currency_symboldisplay(number_format($cart_details['cart_total_price'],2),$currency_symbol->currency_symbol); ?></strong></td>
					</tr>
					<!--service tax changes start-->
					<?php $taxes_fees = 0;
					if($sales_tax->amount != '' && $sales_tax->amount != NULL && $sales_tax->amount > 0) {
					 	$tax_amount = 0;
                        if($sales_tax->amount_type == 'Percentage')
                        {
                            $tax_amount = ($cart_details['cart_total_price'] * $sales_tax->amount) / 100;  
                        }else{
                            $tax_amount = $sales_tax->amount; 
                        }
                        //$tax_amount = number_format($tax_amount, 2, '.', '');
                       	$tax_amount = round($tax_amount,2);
                        $type = ($sales_tax->amount_type == 'Percentage')?'%':'';
                		$percent_text = ($sales_tax->amount_type == 'Percentage')?' ('.$sales_tax->amount.$type.')':''; 
                		$taxes_fees = $taxes_fees + number_format($tax_amount,2); /* ?>
						<tr>
							<td><?php echo $this->lang->line('service_tax').$percent_text; ?></td>
							<td><strong>+ <?php echo currency_symboldisplay(number_format($tax_amount,2),$currency_symbol->currency_symbol); ?> </strong></td>
						</tr>
					<?php */ } ?>
					<!--service tax changes end-->
					<!--service fee changes start-->
					<?php $is_service_fee_applied = 'no';
					if($sales_tax->is_service_fee_enable == '1'){
						$is_service_fee_applied = 'yes';
							$service_fee_amount = 0;
                            if($sales_tax->service_fee_type == 'Percentage')
                            {
                            	$service_fee_amount = ($cart_details['cart_total_price'] * $sales_tax->service_fee) / 100;
                            }else{
                                $service_fee_amount = $sales_tax->service_fee; 
                            }
                            $service_fee_amount = round($service_fee_amount,2);
                        	$fee_type = ($sales_tax->service_fee_type == 'Percentage')?'%':'';
							$fee_percent_text = ($sales_tax->service_fee_type == 'Percentage')?' ('.$sales_tax->service_fee.$fee_type.')':''; 
							$taxes_fees = $taxes_fees + number_format($service_fee_amount,2); /* ?>
					<tr>
						<td><?php echo $this->lang->line('service_fee').$fee_percent_text;?></td>
						<td><strong>+ <?php echo currency_symboldisplay(number_format($service_fee_amount,2),$currency_symbol->currency_symbol); ?></strong></td>
					</tr>
					<?php */ } ?>
					<!--service fee changes end-->
					<?php 
					$is_creditcard = 'no';
					$is_creditcard_fee_applied = 'no'; //flag used
					$creditcard_fee_amount = 0;
					if($payment_optionval=='stripe' || $payment_optionval=='paypal')
					{
						$is_creditcard = 'yes';
					}					
					if($sales_tax->is_creditcard_fee_enable == '1' && $is_creditcard == 'yes'){
						$is_creditcard_fee_applied = 'yes';
                        if($sales_tax->creditcard_fee_type == 'Percentage')
                        {
                        	$creditcard_fee_amount = ($cart_details['cart_total_price'] * $sales_tax->creditcard_fee) / 100;
                        }else{
                            $creditcard_fee_amount = $sales_tax->creditcard_fee; 
                        }
                        $creditcard_fee_amount = round($creditcard_fee_amount,2);
                    	$crdfee_type = ($sales_tax->creditcard_fee_type == 'Percentage')?'%':'';
						$crdfee_percent_text = ($sales_tax->creditcard_fee_type == 'Percentage')?' ('.$sales_tax->creditcard_fee.$crdfee_type.')':''; 
						$taxes_fees = $taxes_fees + number_format($creditcard_fee_amount,2); /* ?>
					<tr>
						<td><?php echo $this->lang->line('creditcard_fee').$crdfee_percent_text;?></td>
						<td><strong>+ <?php echo currency_symboldisplay(number_format($creditcard_fee_amount,2),$currency_symbol->currency_symbol); ?></strong></td>
					</tr>
					<?php */ } ?>
					<!--creditcard fee changes end-->
					<?php if ($order_mode != 'pickup' && $this->session->userdata('deliveryCharge') > 0) { ?>
						<tr>
							<td><?php echo $this->lang->line('delivery_charges') ?></td>
							<?php $delivery_charges = ($this->session->userdata('deliveryCharge')) ? $this->session->userdata('deliveryCharge') : 0.00; 
							$delivery_charges = number_format($delivery_charges,2); ?>
							<td><span id="delivery_charges"><strong><?php echo ($delivery_charges > 0)?'+':''; ?> <?php echo currency_symboldisplay($delivery_charges,$currency_symbol->currency_symbol); ?></strong></span></td>
						</tr>
					<?php } ?>
					<?php if ($this->session->userdata('coupon_applied') == "yes" && !isset($reset_coupon_discount_on_item_change)) {  ?>
						<!-- <tr>
							<td><?php //echo $this->lang->line('coupon_applied') ?></td>
							<td><strong><?php //echo $this->session->userdata('coupon_name'); ?></strong></td>
						</tr> -->
						<?php 
						//Code for multiple coupon use in same cart :: Start
						$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
						if(!empty($coupon_array)){
							$coupon_discount = 0;
							foreach($coupon_array as $cp_key => $cp_value)
							{ ?>
							<tr>
								<td><a href="javascript:void(0);" title="<?=$this->lang->line('remove_coupon');?>" alt="<?=$this->lang->line('remove_coupon');?>"><i class="fa fa-trash" style="font-family:FontAwesome !important; color:#17161a;" onclick="removeCouponOptions(<?php echo $cp_value['coupon_id'];?>)"></i></a> <?php echo $this->lang->line('coupon_discount').' ('.$cp_value['coupon_name'].')' ?></td>
								<?php $coupon_discounttemp = ($cp_value['coupon_discount'])?$cp_value['coupon_discount']:0; 
								$coupon_discounttemp = round($coupon_discounttemp,2); 
								$coupon_discount = $coupon_discount+$coupon_discounttemp; ?>
								<td><strong><?php echo ($coupon_discounttemp > 0)?'-':''; ?> <?php echo currency_symboldisplay(number_format($coupon_discounttemp,2),$currency_symbol->currency_symbol); ?></strong></td>
							</tr>
						<?php } }
						//Code for multiple coupon use in same cart :: Start ?>
					<?php } else {
						$coupon_discount = 0;
					} ?>
					<!-- earning points changes start -->
					<?php //$temp_total = ($cart_details['cart_total_price'] + $delivery_charges + $tax_amount) - $coupon_discount;
					$temp_total = $cart_details['cart_total_price'] - $coupon_discount;
					if ($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') != 'Agent') { 
						$earning_points =  $earning_points->wallet; 
						$is_redeem = ($this->session->userdata('is_redeem'))?$this->session->userdata('is_redeem'):0;
						if(!empty($earning_points)){
							if($is_redeem == 1){
								$redeem_submit = $this->lang->line('cancel_redeem');
								if($earning_points <= $temp_total) {
		                            $redeem_amount = $earning_points;
		                        } else {
		                            $redeem_amount = $temp_total;
		                        }
							} else {
								$redeem_amount = 0;
								$redeem_submit = $this->lang->line('redeem');
							}
						} else {
							$redeem_amount = 0;
							$earning_points = 0;
						} ?>
						<tr>
							<td><?php echo $this->lang->line('wallet_balance'); echo ': '; echo currency_symboldisplay($earning_points,$currency_symbol->currency_symbol); ?></td>
							<?php if($temp_total >= $minimum_subtotal && $earning_points != 0) {  ?>
								<td>
									<input type="button" name="submit_redeem" class="btn" id="submit_redeem" value="<?php echo $redeem_submit ?>" onclick="redeemPoints(<?php echo $temp_total; ?>)">
								</td>
							<?php } else { 
								$this->session->set_userdata('is_redeem',0); 
								$redeem_amount = 0; ?>
								<td></td>
							<?php } ?>
						</tr>
						<?php if($this->session->userdata('is_redeem') == 1 && $earning_points != 0){ ?>
							<tr>
								<td><?php echo $this->lang->line('wallet_money_used_web') ?></td>
								<td><strong><?php echo ($redeem_amount > 0)?'-':''; 
								$this->session->set_userdata('redeem_amount',$redeem_amount);
								?> <?php echo currency_symboldisplay($redeem_amount,$currency_symbol->currency_symbol); ?></strong></td>
							</tr>
						<?php } ?>
					<?php } ?>
					<!-- earning points changes end -->
					<!-- driver tip :: start -->
					<?php $driver_tip = ($this->session->userdata('tip_amount')>0)?$this->session->userdata('tip_amount'):0;
					$driver_tip=($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('is_user_login') == 1)?$driver_tip:0;

					$driver_tip = (float)round($driver_tip,2);
					if ($driver_tip && $driver_tip>0) { 
						$driver_tip = number_format($driver_tip,2); ?>
						<tr>
							<td><?php echo $this->lang->line('driver_tip'); ?></td>
							<td><strong>+ <?php echo currency_symboldisplay($driver_tip,$currency_symbol->currency_symbol); ?></strong></td>
						</tr>
					<?php } ?>
					<!-- driver tip :: end -->
					<!-- taxes and fees :: start -->
					<tr>
						<td><?php echo $this->lang->line('taxes_fees'); ?>
							<div class="custom--tooltip" style="position:absolute;padding-left:6px;">
								<i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
								<span class="tooltiptext tooltip-right">
									<div id="servicetax_infodiv">
										<span class="custom_service"><?php echo $this->lang->line('service_tax'); ?> <span id="servicetaxtype_info"><?php echo $percent_text; ?></span></span> : <span class="service_price" id="servicetax_info"><?php echo currency_symboldisplay(number_format($tax_amount,2),$currency_symbol->currency_symbol); ?></span>
									</div>
									<div id="servicefee_infodiv">
										<span class="custom_service"><?php echo $this->lang->line('service_fee'); ?> <span id="servicefeetype_info"><?php echo $fee_percent_text; ?></span></span> : <span class="service_price" id="servicefee_info"><?php echo currency_symboldisplay(number_format($service_fee_amount,2),$currency_symbol->currency_symbol); ?></span>
									</div>
									<div id="creditcardfee_infodiv">
										<span class="custom_service"><?php echo $this->lang->line('creditcard_fee'); ?> <span id="creditcardfeetype_info"><?php echo $crdfee_percent_text; ?></span></span> : <span class="service_price" id="creditcardfee_info"><?php echo currency_symboldisplay(number_format($creditcard_fee_amount,2),$currency_symbol->currency_symbol); ?></span>
									</div>
								</span>
							</div>
						</td>
						<td><strong>+<?php echo currency_symboldisplay($taxes_fees,$currency_symbol->currency_symbol);?></strong></td>
					</tr>
					<!-- taxes and fees :: end -->
				</tbody>
				<tfoot>
					<tr>
						<td><?php echo $this->lang->line('to_pay') ?></td>
						<?php $to_pay = ($temp_total + $delivery_charges + $tax_amount + $service_fee_amount + $driver_tip+$creditcard_fee_amount) - $redeem_amount;  //added sales tax
						$to_pay = ($to_pay > 0)?$to_pay:0;
						$this->session->set_userdata(array('payment_currency' => $currency_symbol->currency_code));
						$this->session->set_userdata(array('total_price' => round($to_pay,2))); ?>
						<td><strong><?php echo currency_symboldisplay(number_format($to_pay,2),$currency_symbol->currency_symbol); ?></strong></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
<?php } ?>
<script type="text/javascript">
var new_total_price = <?php echo ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0; ?>;
$(document).ready(function() {
	$('#is_creditcard_fee_applied').val('<?php echo $is_creditcard_fee_applied; ?>');
	$('#creditcard_feeval').val('<?php echo $sales_tax->creditcard_fee; ?>');
	$('#creditcard_fee_typeval').val('<?php echo $sales_tax->creditcard_fee_type; ?>');

	$('#is_service_fee_applied').val('<?php echo $is_service_fee_applied; ?>');
	$('#service_feeval').val('<?php echo $sales_tax->service_fee; ?>');
	$('#service_fee_typeval').val('<?php echo $sales_tax->service_fee_type; ?>');

	$('#service_taxval').val('<?php echo $sales_tax->amount; ?>');
	$('#service_tax_typeval').val('<?php echo $sales_tax->amount_type; ?>');
});
</script>