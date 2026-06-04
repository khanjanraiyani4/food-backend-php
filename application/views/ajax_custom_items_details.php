<style type="text/css">
	.ViewRecipe{
		padding: 5px;margin-left: 5px; font-size: 14px;
	}
</style>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    	<!-- Modal Header -->
		<!-- <div class="modal-header">
			<h4 class="modal-title"><?php //echo $this->lang->line('menu_details') ?></h4>
			<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		</div> -->
		<!-- Modal body -->
		<div class="modal-body">
			<div class="actual-content">
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		    	<figure>
		    		<img src="<?php echo (file_exists(FCPATH.'uploads/'.$result[0]['items'][0]['image']) &&  $result[0]['items'][0]['image']!='')?image_url.$result[0]['items'][0]['image']:default_icon_img; ?>">
		    	</figure>
			    <form id="custom_items_form1">
					<!-- <h4 class="modal-title"><?php echo $this->lang->line('menu_details') ?></h4>
					<h5 id="product_title"><?php echo ($result[0])?$result[0]['items'][0]['name']:''; ?></h5> -->
					<h4 class="modal-title"><?php echo ($result[0])?$result[0]['items'][0]['name']:$this->lang->line('menu_details'); ?></h4>
					<h5 id="product_title"></h5>
	      			<h6 class="menu_detail"><?php echo $this->lang->line('description') ?></h6>
					<p id="product_description1"><?php echo $result[0]['items'][0]['menu_detail']; ?></p>
	      			<?php /* ?><div style="font-weight:600;"><?php echo $this->lang->line('ingredients') ?></div><br><div><?php echo $result[0]['items'][0]['ingredients']; ?></div><br><?php */ ?>
		      		<div class="popup-radio-btn-main">
			      		<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $result[0]['items'][0]['restaurant_id']; ?>">
			      		<input type="hidden" name="is_closed" id="is_closed" value="<?php echo $is_closed; ?>">
			      		<input type="hidden" name="is_addon" id="is_addon" value="<?php echo "addon"; ?>">
			      		<input type="hidden" name="user_id" id="user_id" value="<?php echo ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):''; ?>"><br>
			      		<div class="item-price-label">
	      					<span><?php echo $this->lang->line('item') ?></span>
	      					<span><?php echo $this->lang->line('price') ?></span>
	      				</div>
			      		<?php if (!empty($result[0]['items'][0]['addons_category_list'])) {
			      			foreach ($result[0]['items'][0]['addons_category_list'] as $key => $value) { ?>
			      				<?php if(!empty($value['addons_category_id'])){ ?>
			      				<?php $select_note = ($value['is_multiple'] == 1 && !is_null($value['display_limit'])) ? ' ('.$this->lang->line('select_any').' '.$value['display_limit'].')' : ''; ?>
			      				<div class="radio-btn-box  max-selection" <?php echo ($value['is_multiple'] == 1 && !is_null($value['display_limit'])) ? 'data-max-selection ="'.$value['display_limit'].'"' : ''; ?>>
						      		<div class="customizable-title">
						      			<h5><?php echo $value['addons_category']; ?> <?php if($value['mandatory'] == '1'){ ?><span style="color: red;">*</span><?php } ?><span><?php echo $select_note; ?></span></h5>
						      		</div>
						      		<?php if (!empty($value['addons_list'])) {
						      			foreach ($value['addons_list'] as $key => $addvalue) { ?>
								      		<div class="radio-btn-list">
								      			<label>
								      				<?php if ($value['is_multiple'] == 1) { ?>
								      					<input type="checkbox" class="check_addons1" name="<?php echo $value['addons_category'].'-'.$key; ?>" id="<?php echo $addvalue['add_ons_name'].'-'.$key; ?>" value="1" onchange="getaddonsItemPrice(this.id,'<?php echo $addvalue['add_ons_price']; ?>','<?php echo $value['is_multiple']; ?>',<?php echo $result[0]['items'][0]['menu_id']; ?>)" amount1="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>"  addons_category_id1="<?php echo $value['addons_category_id']; ?>" >
								      				<?php } 
								      				else
							      					{ ?>
							      						<input type="radio" class="radio_addons1" name="<?php echo $value['addons_category']; ?>" id="<?php echo $addvalue['add_ons_name'].'-'.$key; ?>" value="1" onchange="getaddonsItemPrice(this.id,'<?php echo $addvalue['add_ons_price']; ?>','<?php echo $value['is_multiple']; ?>',<?php echo $result[0]['items'][0]['menu_id']; ?>)" amount1="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>" addons_category_id1="<?php echo $value['addons_category_id']; ?>" >
							      					<?php } ?>
								      				<span><?php echo $addvalue['add_ons_name']; ?> </span>
								      			</label>
								      		<span><?php echo $currency_symbol->currency_symbol; ?><?php echo $addvalue['add_ons_price']; ?></span>
								      		</div>
						      			<?php }
						      		} ?>
					      		</div>
			      			<?php }	}
			      		} ?>
		      		</div>
			      	<div class="popup-total1">
			      		<?php 
						$priceval = $result[0]['items'][0]['price'];
						if($result[0]['items'][0]['offer_price']>0){ 
							$priceval = $result[0]['items'][0]['offer_price'];	
						} ?>
			      		<h5><?php echo $this->lang->line('total') ?></h5>
			      		<div class="total-price" style="display: flex;align-items: center;">
			      			<input type="hidden" name="subTotal_for_cal" id="subTotal_for_cal" value="<?php if($result[0]['items'][0]['offer_price']>0){  echo ($result[0]['items'][0]['offer_price'])?$result[0]['items'][0]['offer_price']:0; } else{  echo ($result[0]['items'][0]['price'])?$result[0]['items'][0]['price']:0; } ?>">
							<input type="hidden" name="subTotal1" id="subTotal1" value="0">
							<div class="add-cart-item">
								<div class="number">
									<span class="minus" id="minusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','minus','<?php echo $priceval;?>')"><i class="iicon-icon-22"></i></span>
									<input type="text" value="1" id="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onblur="pricecalwithqty(this.id,this.value,'yes','yes',<?php echo $priceval;?>);" name="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" class="QtyNumberval" placeholder="" maxlength="3" />
									<span class="plus" id="plusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','plus','<?php echo $priceval;?>')"><i class="iicon-icon-21"></i></span>
								</div>
							</div>
							<?php if($result[0]['items'][0]['offer_price']>0){ ?>
								<strong <?php if($result[0]['items'][0]['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?> id="price"><?php echo $currency_symbol->currency_symbol; ?><?php echo $result[0]['items'][0]['price'];  ?></strong>
								<strong class="price"><?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice1"><?php echo $result[0]['items'][0]['offer_price'];  ?></span></strong>
							<?php }else{ ?>
								<strong class="price"><?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice1"><?php echo $result[0]['items'][0]['price'];  ?></span></strong>
							<?php } ?>
							
			      		</div>
			      		<div class="detail-add-btn">
			      			<?php if($is_closed!='Closed' && $result[0]['items'][0]['restaurant_status'] == 1){ ?>
			      			<?php $mandatory = 0;
			      			$mandatory_arr = array();
							foreach ($result[0]['items'][0]['addons_category_list'] as $key => $value) {
								if($value['mandatory'] == '1') {
						           array_push($mandatory_arr, $value['addons_category_id']);
							    }
							} 
							$mandatory = (!empty($mandatory_arr))?1:0;
							$mandatory_arr = json_encode($mandatory_arr); ?>
							<?php if($result[0]['items'][0]['stock'] == 1 || $result[0]['items'][0]['allow_scheduled_delivery'] == '1') { ?>
							<div class="add-btn">
			      				<button type="button" class="addtocart btn addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" id="addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" <?php echo ($is_closed=='Closed')?'disabled':""; ?> onclick="checkaddonsRestaurantinCart('<?php echo $result[0]['items'][0]['menu_id']; ?>','addons',this.id,'<?php echo $is_closed ?>',<?php echo $mandatory; ?>,<?php echo htmlspecialchars(json_encode($mandatory_arr)); ?>,'<?php echo $recipe_page ?>')" <?php echo ($is_closed=='Closed')?'disabled':""; ?> order-for-later="<?php echo ($result[0]['items'][0]['allow_scheduled_delivery'] == '1' && $result[0]['items'][0]['stock'] == 0) ? '1' : '0'; ?>" ><?php echo ($cart_rest == 1) ? $this->lang->line('added') : (($result[0]['items'][0]['allow_scheduled_delivery'] == '1' && $result[0]['items'][0]['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?></button>
			      				<?php if($result[0]['items'][0]['allow_scheduled_delivery'] == '1' && $result[0]['items'][0]['stock'] == 0) { ?>
									<strong class="text-danger">
										<?php echo $this->lang->line('out_stock'); ?>
									</strong>
								<?php } ?>
			      			</div>
			      			<?php }else{ ?>
			      					<strong class="text-danger">
										<?php echo $this->lang->line('out_stock'); ?>
									</strong>
			      			<?php } } ?>
						</div>
			      	</div>
			      	<?php if(!(empty($mandatory))) { ?>
			      		<div style="color: red"><span>* </span><?php echo $this->lang->line('required_field'); ?></div>
			      	<?php } ?>	
      		</form>
	    	</div>
	    </div>
	</div>
</div>
<script type="text/javascript">
	//get item price
	var totalPrice_addons = 0;
	var radiototalPrice_addons = 0;
	var checktotalPrice_addons = 0;
	/*flow - on check get max selectio value get it's total check box checked compare if checked greater display error*/
	$('.radio-btn-list .check_addons1').change(function() {
		if(this.checked) {
			var selection_length = $(this).closest('.max-selection').attr('data-max-selection');
			if(selection_length != '' && $.isNumeric(selection_length) && selection_length > 0){
				var checkbox_count = $(this).closest('.max-selection').find('.check_addons1').filter(':checked').length;
				if(checkbox_count > selection_length){
					$(this).prop("checked", false);
					var category_name = $(this).attr('addons_category');
					if(SELECTED_LANG == 'en') {
						bootbox.alert({
							message: "Please select any "+ selection_length + " from " + category_name + ".",
							buttons: {
								ok: {
									label: "Ok",
								}
							}
						});
					} else if(SELECTED_LANG == 'fr') {
						bootbox.alert({
							message: "Veuillez sélectionner n'importe quel "+selection_length+" from " +category_name +".",
							buttons: {
								ok: {
									label: "D'accord",
								}
							}
						});
					} else {
						bootbox.alert({
							message: "الرجاء تحديد أي"+selection_length+" from "+category_name+".",
							buttons: {
								ok: {
									label: "نعم",
								}
							}
						});
					}
				}
			}
		}
	});
</script>