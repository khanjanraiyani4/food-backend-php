<div class="your-cart-main">
	<div class="your-cart-title">
		<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('your_cart') ?></h3>
		<h6><?php echo count($cart_details['cart_items']); ?> <?php echo $this->lang->line('items') ?></h6>
		<?php if(count($cart_details['cart_items']) > 0 ){ ?>
			<a class="btn res-view-all"  href="<?php echo base_url() . 'cart'; ?>"><?php echo $this->lang->line('view_cart') ?></a>
		<?php } ?>
	</div>
	<?php if (!empty($cart_details['cart_items'])) { 
		$menuids = array(); ?>
		<div class="add-cart-list-main">
			<?php foreach ($cart_details['cart_items'] as $cart_key => $value) { 
				array_push($menuids, $value['menu_id']); ?>
				<div class="add-cart-list">
					<div class="cart-list-content">
						<h5><?php echo $value['name']; ?></h5>
						<strong><?php echo currency_symboldisplay(number_format($value['totalPrice'],2),$currency_symbol->currency_symbol); ?></strong>
						<?php if ($value['is_combo_item']) {?>
							<p><?php echo nl2br($value['menu_detail']); ?></p>
						<?php }?>
						<?php if (!empty($value['addons_category_list'])) {?>
							<ul class="ul-disc">
							<?php foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
								<?php /*<li><h6><?php echo $cat_value['addons_category']; ?></h6></li>*/ ?>								
								<?php if (!empty($cat_value['addons_list'])) {?>
									<ul class="ul-cir">
									<?php foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
										<li><?php echo $add_value['add_ons_name']; ?>  <?php echo currency_symboldisplay(number_format($add_value['add_ons_price'],2),$currency_symbol->currency_symbol); ?></li>
									<?php }?>
									</ul>
								<?php } ?>
							<?php }?>
							</ul>
						<?php } ?>
						
						
					</div>
					<div class="add-cart-item">
						
						<div class="number">
							<span class="minus" id="minusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
							<input type="text" class="QtyNumberval" maxlength="3" value="<?php echo $value['quantity']; ?>" onfocusout="EditcustomItemCount(this.value,<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,<?php echo $cart_key; ?>)" />
							<span class="plus" id="plusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php //get System Option Data
            $this->db->select('OptionValue');
            $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
            $min_order_amount = (float) $min_order_amount->OptionValue; ?>
		<div class="cart-subtotal">
			<strong><?php echo $this->lang->line('sub_total') ?></strong>
			<strong class="price"><?php echo currency_symboldisplay(number_format($cart_details['cart_total_price'],2),$currency_symbol->currency_symbol); ?></strong>
		</div>
		<div class="continue-btn">
			<a href="javascript:void(0);" class="continue_btn" onclick="checkResStat();"><button class="btn"><?php echo $this->lang->line('continue') ?></button></a>
		</div>
		<div class="res_closed_err" style="display: none; color: red;"></div>
		<div class="min_order_txt mt-3" style="<?php echo (!in_array('Delivery', $order_mode))?'display: none;':(($cart_details['cart_total_price'] >= $min_order_amount)?'display: none;':'display: block'); ?>">
			<?php $min_order_txt = sprintf($this->lang->line('min_order_msg'),$min_order_amount); ?>
			<p><?php echo $min_order_txt; ?></p>
		</div>
	<?php } 
	else { ?>
		<div class="cart-empty text-center">
			<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
			<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_dishes') ?> <br>
			</h6>
			<div class="min_order_txt mt-3" style="<?php echo (!in_array('Delivery', $order_mode))?'display: none;':'display: block'; ?>">
			<?php 
			$this->db->select('OptionValue');
            $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
            $min_order_amount = (float) $min_order_amount->OptionValue; 
			$min_order_txt = sprintf($this->lang->line('min_order_msg'),$min_order_amount); ?>
			<p><?php echo $min_order_txt; ?></p>
			</div>
		</div>		
	<?php } ?>
</div>
<script type="text/javascript">
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
	if(count != '0'){
		$('body').addClass("cart_bottom");
		//$("#your_cart").addClass("cart_bottom");
	} else {
		$('body').addClass("cart_bottom");
		//$("#your_cart").removeClass("cart_bottom");
	}
	$('input.QtyNumberval').on('input', function() {		
	    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
	});
	//check restaurant : closed/offline/deactive
	function checkResStat() {
		var restaurant_id = '<?php echo $cart_restaurant ?>';
		var menu_ids = <?php echo json_encode($menuids); ?>;
		var is_scheduling_allowed = <?php echo ($allow_scheduled_delivery == '1') ? 1 : 0; ?>;
		jQuery.ajax({
	        type : "POST",
	        dataType : "json",
	        url : BASEURL+'cart/checkResStat',
	        data : {'restaurant_id':restaurant_id, 'menu_ids':menu_ids, 'is_scheduling_allowed':is_scheduling_allowed},
	        beforeSend: function(){
	            $('#quotes-main-loader').show();
	        },
	        success: function(response) {
	            $('#quotes-main-loader').hide();
	            if(response.status == 'res_unavailable') {
	            	$('.continue_btn').attr("href", 'javascript:void(0)');
	            	var err_box = bootbox.alert({
						message: response.show_message,
						buttons: {
							ok: {
								label: response.oktxt,
							}
						}
					});
					setTimeout(function() {
						err_box.modal('hide');
					}, 10000);
	            	//$('.res_closed_err').text(response.show_message);
	            	//$('.res_closed_err').css("display", "block");
	            	return false;
	            } else {
	            	$('.continue_btn').attr("href", BASEURL+'checkout');
	            	//$('.res_closed_err').css("display", "none");
	            	window.location.href = BASEURL+'checkout';
	            	return true;
	            }
	        },
	        error: function(XMLHttpRequest, textStatus, errorThrown) {
	            alert(errorThrown);
	        }
	    });
	}
</script>
