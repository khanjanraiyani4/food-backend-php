<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
		<div class="modal-header">
			<h4 class="modal-title"><?php echo ($result[0])?$result[0]['items'][0]['name']:''; ?></h4>
			<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		</div>
      <!-- Modal body -->
		<div class="modal-body">
      		<form id="custom_items_form">
	      	<div class="popup-radio-btn-main">
	      		<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $result[0]['items'][0]['restaurant_id']; ?>">
	      		<input type="hidden" name="user_id" id="user_id" value="<?php echo ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):''; ?>">
	      		<div class="item-price-label">
	      					<span><?php echo $this->lang->line('item') ?></span>
	      					<span><?php echo $this->lang->line('price') ?></span>
	      				</div>
	      		<?php if (!empty($result[0]['items'][0]['addons_category_list'])) {
	      			foreach ($result[0]['items'][0]['addons_category_list'] as $key => $value) { ?>
	      				<?php if(!empty($value['addons_category_id'])){ ?>
	      				<?php $select_note = ($value['is_multiple'] == 1 && !is_null($value['display_limit'])) ? ' ('.$this->lang->line('select_any').' '.$value['display_limit'].')' : ''; ?>
	      				<div class="radio-btn-box max-selection" <?php echo ($value['is_multiple'] == 1 && !is_null($value['display_limit'])) ? 'data-max-selection ="'.$value['display_limit'].'"' : ''; ?>>
				      		<div class="customizable-title">
				      			<h5><?php echo $value['addons_category']; ?> <?php if($value['mandatory'] == '1'){ ?><span style="color: red;">*</span><?php } ?><span><?php echo $select_note; ?></span></h5>
				      		</div>
				      		<?php if (!empty($value['addons_list'])) {
				      			foreach ($value['addons_list'] as $key => $addvalue) { ?>
						      		<div class="radio-btn-list">
						      			<label>
						      				<?php if ($value['is_multiple'] == 1) { ?>
						      					<input type="checkbox" class="check_addons" name="<?php echo $value['addons_category'].'-'.$key; ?>" id="<?php echo $addvalue['add_ons_name'].'-'.$key; ?>" value="1" onchange="getItemPrice(this.id,'<?php echo $addvalue['add_ons_price']; ?>','<?php echo $value['is_multiple']; ?>',<?php echo $result[0]['items'][0]['menu_id']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
						      				<?php } 
						      				else
					      					{ ?>
					      						<input type="radio" class="radio_addons" name="<?php echo $value['addons_category']; ?>" id="<?php echo $addvalue['add_ons_name'].'-'.$key; ?>" value="1" onchange="getItemPrice(this.id,'<?php echo $addvalue['add_ons_price']; ?>','<?php echo $value['is_multiple']; ?>',<?php echo $result[0]['items'][0]['menu_id']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
					      					<?php } ?>
						      				<span><?php echo $addvalue['add_ons_name']; ?></span>
						      			</label>
						      		<span><?php echo currency_symboldisplay($addvalue['add_ons_price'],$currency_symbol->currency_symbol); ?></span>
						      		</div>
				      			<?php }
				      		} ?>
			      		</div>
	      			<?php }	}
	      		} ?>
	      	</div>
	      	<div class="popup-total-main">
	      		<div class="popup-total">
	      			<h2><?php echo $this->lang->line('total') ?></h2>
	      		</div>
	      		<div class="total-price">
	      			<?php 
					$priceval = $result[0]['items'][0]['price'];
					if($result[0]['items'][0]['offer_price']>0){ 
						$priceval = $result[0]['items'][0]['offer_price'];	
					} ?>
	      			<input type="hidden" name="subTotal_for_cal" id="subTotal_for_cal" value="<?php if($result[0]['items'][0]['offer_price']>0){  echo ($result[0]['items'][0]['offer_price'])?$result[0]['items'][0]['offer_price']:0; } else{  echo ($result[0]['items'][0]['price'])?$result[0]['items'][0]['price']:0; } ?>">

	      			<input type="hidden" name="subTotal" id="subTotal" value="0">

	      			<div class="add-cart-item">
						<div class="number">
							<span class="minus" id="minusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','minus','<?php echo $priceval;?>')"><i class="iicon-icon-22"></i></span>
							<input type="text" value="1" onblur="pricecalwithqty(this.id,this.value,'yes','',<?php echo $priceval;?>);" id="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" name="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" class="QtyNumberval" placeholder="" maxlength="3" />
							<span class="plus" id="plusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','plus','<?php echo $priceval;?>')"><i class="iicon-icon-21"></i></span>
						</div>
					</div>
	      			<?php if($result[0]['items'][0]['offer_price']>0){ ?>
								<strong class="text-secondary" style="text-decoration: line-through;"><?php echo $currency_symbol->currency_symbol; ?><span><?php echo $result[0]['items'][0]['price'];  ?></span></strong>
								<strong><?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice"><?php echo $result[0]['items'][0]['offer_price'];  ?></span></strong>

							<?php }else{ ?>
								<strong><?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice"><?php echo $result[0]['items'][0]['price'];  ?></span></strong>
							<?php } ?>

	      			<!-- onclick="AddToCart('<?php //echo $result[0]['items'][0]['menu_id']; ?>')" -->
	      			<?php $mandatory = 0;
      				$mandatory_arr = array();
					foreach ($result[0]['items'][0]['addons_category_list'] as $key => $value) {
						if($value['mandatory'] == '1') {
			               array_push($mandatory_arr, $value['addons_category_id']);
				         }
					} 
					$mandatory = (!empty($mandatory_arr))?1:0;
					$mandatory_arr = json_encode($mandatory_arr); ?>
	      			<button type="button" class="addtocart btn addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" id="addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onclick="AddAddonsToCart('<?php echo $result[0]['items'][0]['menu_id']; ?>',this.id,'<?php echo $mandatory; ?>',<?php echo htmlspecialchars(json_encode($mandatory_arr)); ?>,'<?php echo (isset($from_checkout)) ? $from_checkout : '' ?>')"><?php echo $this->lang->line('add') ?></button>
	      		</div>
	      	</div>
	      	<?php if(!(empty($mandatory))) { ?>
			    <div style="color: red"><span>* </span><?php echo $this->lang->line('required_field'); ?></div>
			<?php } ?>	
      		</form>
      		</form>
		</div>
    </div>
</div>


<script type="text/javascript">
	//get item price
	var totalPrice = 0;
	var radiototalPrice = 0;
	var checktotalPrice = 0;
	/*flow - on check get max selectio value get it's total check box checked compare if checked greater display error*/
	$('.radio-btn-list .check_addons').change(function() {
		if(this.checked) {
			var selection_length = $(this).closest('.max-selection').attr('data-max-selection');
			if(selection_length != '' && $.isNumeric(selection_length) && selection_length > 0){
				var checkbox_count = $(this).closest('.max-selection').find('.check_addons').filter(':checked').length;
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
$('input.QtyNumberval').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
});
</script>