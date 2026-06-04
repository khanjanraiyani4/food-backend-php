<style type="text/css">
	.ViewRecipe{
		padding: 5px;margin-left: 5px; font-size: 13px;opacity: 0.7;
	}
</style>
<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<!-- Modal Header -->
<!-- 		<div class="modal-header">
			
		</div> -->
		<!-- Modal body -->
		<div class="modal-body">
			<div class="actual-content">
			<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
				
				<figure>
					
					<img src="<?php echo (file_exists(FCPATH.'uploads/'.$result[0]['items'][0]['image']) && $result[0]['items'][0]['image']!='') ? image_url.$result[0]['items'][0]['image'] : default_icon_img; ?>">
				</figure>
				<form>
					<!-- <h4 class="modal-title"><?php echo $this->lang->line('menu_details') ?></h4>
					<h5 id="product_title"><?php echo ($result[0])?$result[0]['items'][0]['name']:''; ?></h5> -->
					<h4 class="modal-title"><?php echo ($result[0])?$result[0]['items'][0]['name']:$this->lang->line('menu_details'); ?></h4>
					<h5 id="product_title"></h5>
					<h6 class="menu_detail"><?php echo $this->lang->line('description') ?></h6>
					<p id="product_description"><?php
					$menu_detail = $result[0]['items'][0]['menu_detail'];
					if($result[0]['items'][0]['is_combo_item']=='1')
					{
						$menu_detail = str_replace("\n", "<br>", $result[0]['items'][0]['menu_detail']);
					}

					 echo $menu_detail; ?></p>
					<?php /* ?><div style="font-weight:600;"><?php echo $this->lang->line('ingredients') ?></div><br><div><?php echo $result[0]['items'][0]['ingredients']; ?></div><br><?php */ ?>
					<div class="popup-total1">
						<h5><?php echo $this->lang->line('total') ?></h5>
						<div class="total-price" style="display: flex;align-items: center;">
							<!-- <input type="text" value="" id="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onblur="pricecalwithqty(this.id,this.value,'yes');" name="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" class="form-control QtyNumberval" style="width:75px; margin-bottom: 5px; margin-right:10px;" placeholder="<?php echo $this->lang->line('quantity') ?>" maxlength="3" /> -->

							<?php 
							$priceval = $result[0]['items'][0]['price'];
							if($result[0]['items'][0]['offer_price']>0){ 
								$priceval = $result[0]['items'][0]['offer_price'];	
							} ?>
							<div class="add-cart-item">
								<div class="number">
									<span class="minus" id="minusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','minus','<?php echo $priceval;?>')"><i class="iicon-icon-22"></i></span>
									<input type="text" value="1" id="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onblur="pricecalwithqty(this.id,this.value,'no','',<?php echo $priceval;?>);" name="qtyaddtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" class="QtyNumberval" placeholder="" maxlength="3" />
									<span class="plus" id="plusQuantity" onclick="ItemqtyPlusMinus('<?php echo $result[0]['items'][0]['menu_id']; ?>','plus','<?php echo $priceval;?>')"><i class="iicon-icon-21"></i></span>
								</div>
							</div>
							<?php if($result[0]['items'][0]['offer_price']>0){ ?>
							<strong <?php if($result[0]['items'][0]['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?> id="price"><?php echo $currency_symbol->currency_symbol; ?><?php echo $result[0]['items'][0]['price']; ?></strong>								
							<strong class="total-price">
							<?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice"><?php echo $result[0]['items'][0]['offer_price']; ?></span>
							<?php //echo ($result[0]['items'][0]['check_add_ons'] != 1)?currency_symboldisplay($result[0]['items'][0]['offer_price'],$currency_symbol->currency_symbol):(($result[0]['items'][0]['offer_price'])?currency_symboldisplay($result[0]['items'][0]['offer_price'],$currency_symbol->currency_symbol):''); ?>
							</strong>
							<?php } else{ ?>
								<strong <?php if($result[0]['items'][0]['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?> id="price"><?php echo $currency_symbol->currency_symbol; ?><span id="totalPrice"><?php echo $result[0]['items'][0]['price']; ?></span></strong>
							<?php } ?>

						</div>
						<div class="detail-add-btn">
						<?php if($is_closed!='Closed' && $result[0]['items'][0]['restaurant_status'] == 1){ ?>
							<?php if($result[0]['items'][0]['stock'] == 1 || $result[0]['items'][0]['allow_scheduled_delivery'] == '1') { ?>
							<div class="add-btn">
								<button type="button" class="addtocart btn addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" id="addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onclick="checkRestaurantinCart('<?php echo $result[0]['items'][0]['menu_id']; ?>','<?php echo $result[0]['items'][0]['restaurant_id']; ?>','',this.id,'','<?php echo $recipe_page ; ?>')" order-for-later="<?php echo ($result[0]['items'][0]['allow_scheduled_delivery'] == '1' && $result[0]['items'][0]['stock'] == 0) ? '1' : '0'; ?>" > <?php echo ($cart_rest == 1) ? $this->lang->line('added') : (($result[0]['items'][0]['allow_scheduled_delivery'] == '1' && $result[0]['items'][0]['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')) ?> </button>
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

						<?php if(!empty($recipe_name)){ ?>
						<a  class="btn ViewRecipe" href="<?php echo base_url().'recipe/recipe-detail/'.$recipe_name[0]->slug ?>" ><?php echo $this->lang->line('view_recipe'); ?></a>
							<?php } ?>
						</div>
					</div>
				</form>	
			</div>
		</div>
	</div>
</div>
