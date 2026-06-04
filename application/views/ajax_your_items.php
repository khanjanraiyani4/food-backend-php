<?php if (!empty($cart_details['cart_items'])) { ?>
    <div class="card-header" id="headingOne">
        <div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
        	<img src="<?php echo base_url();?>assets/front/images/picnic-basket.svg">
        	<h3><?php echo $this->lang->line('your_items') ?></h3>
    	</div>	
    </div>
    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
        <div class="card-body">
        	<div class="cart-content-table">
                <table>
					<tbody>
						<?php if (!empty($cart_details['cart_items'])) { ?>
							<input type="hidden" name="total_cart_items" id="total_cart_items" value="<?php echo count($cart_details['cart_items']); ?>">
							<?php foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
								<tr>
									<?php /* ?><td class="item-img-main"><div><i class="iicon-icon-15 <?php echo ($value['is_veg'] == 1)?'veg':'non-veg'; ?>"></i></div></td><?php */ ?>
									<td class="item-name">
										<?php echo $value['name']; ?>
										<?php if ($value['is_combo_item']) {?>
											<p style="font-weight: 400;"><?php echo nl2br($value['menu_detail']); ?></p>
										<?php }?>
										<ul class="ul-disc">
											<?php if (!empty($value['addons_category_list'])) {
												foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
													<?php /*<li><h6><?php echo $cat_value['addons_category']; ?></h6></li>*/ ?>
													<ul class="ul-cir">
													<?php if (!empty($cat_value['addons_list'])) {
														foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
															<li><?php echo $add_value['add_ons_name']; ?> <?php echo currency_symboldisplay(number_format($add_value['add_ons_price'],2),$currency_symbol->currency_symbol); ?></li>
														<?php }
													} ?>
													</ul>
												<?php }
											} ?>
										</ul>
										<div class="item-comment-input"><input type="text" name="item_comment_<?php echo $value['menu_id']; ?>" id="item_comment_<?php echo $value['menu_id'].'_'.$cart_key; ?>" value="<?php echo $value['comment'];?>" placeholder="<?php echo $this->lang->line('add_item_comment'); ?>" class="form-control" onblur="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'updatecomment',<?php echo $cart_key; ?>)" maxlength="250"></div>
									</td>
									<td><strong><?php echo currency_symboldisplay(number_format($value['totalPrice'],2),$currency_symbol->currency_symbol); ?></strong></td>
									<td>
										<div class="add-cart-item">
											<div class="number">
												<span class="minus" id="minusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
												<input type="text" class="QtyNumberval" maxlength="3" name="item_count_check" id="item_count_check_<?php echo $cart_key; ?>" value="<?php echo $value['quantity']; ?>" onfocusout="EditCheckoutItemCount(this.value,<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,<?php echo $cart_key; ?>)" />
												<span class="plus" id="plusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
											</div>
										</div>
									</td>
									<td class="close-btn-cart"><button class="close-btn" alt="<?php echo $this->lang->line('delete'); ?>" title="<?php echo $this->lang->line('remove_item_txt'); ?>" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'remove',<?php echo $cart_key; ?>, '<?php echo $this->lang->line('delete_module'); ?>', '<?php echo $this->lang->line('ok'); ?>' , '<?php echo $this->lang->line('cancel'); ?>' )"><i class="iicon-icon-38"></i></button></td>
								</tr>
							<?php } 
						} 
						else
						{ ?>
							<div class="cart-empty text-center">
								<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
								<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_dishes') ?></h6>
							</div>	
						<?php }?>
					</tbody>
				</table>
				<?php if(!empty($cart_details['cart_items']) && $current_page=='Checkout' && !empty($restaurant_data->restaurant_slug) && $restaurant_data->status == 1 && $restaurant_data->enable_hours == 1 && $restaurant_data->timings['off'] == "open" && $restaurant_data->timings['closing'] == "Open"){ ?>
					<div class="add-more-item-section">
						<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$restaurant_data->restaurant_slug;?>">
							<button class="btn"><?php echo $this->lang->line('want_to_add_more_items') ?></button>
						</a>
					</div>
				<?php } ?>
			</div>
        </div>
    </div>
<?php } else { ?>
    <div class="card-header" id="headingOne">
        <div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
        	<img src="<?php echo base_url();?>assets/front/images/picnic-basket.svg">
        	<h3><?php echo $this->lang->line('your_items') ?></h3>
    	</div>	
    </div>
    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
        <div class="card-body">
        	<div class="cart-content-table">
                <table>
					<tbody>
						<div class="cart-empty text-center" >
							<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
							<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_dishes') ?></h6>
						</div>	
					</tbody>
				</table>
			</div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
	$('input.QtyNumberval').on('input', function() {		
	    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
	});
</script>