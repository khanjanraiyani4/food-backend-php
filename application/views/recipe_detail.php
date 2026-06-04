<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>
<?php if(empty($recipe_details)) {
    redirect(base_url().'recipe');
} ?>
<style type="text/css">
.recipe-view-menu
{
	font-size: 16px; background:var(--main-color); border:0px; border-radius: 5px; color: #fff; padding: 10px 0 10px 0;width: 100px;
}
</style>
		<section class="inner-banner recipe-detail-banner">
			<div class="container">
				<div class="inner-pages-banner">
				</div>
			</div>
		</section>
		<section class="inner-pages-section recipe-detail-section">
			<div class="rest-detail-main">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="rest-detail">
								<div class="rest-detail-img-main">
									<div class="rest-detail-img">
										
											<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$recipe_details[0]['image']) && $recipe_details[0]['image']) ? image_url.$recipe_details[0]['image'] : default_icon_img;  ?>
										<img src="<?php echo $rest_image ; ?>" alt="<?php echo ($recipe_details[0]['name'])?$recipe_details[0]['name']:''; ?>" title="<?php echo ($recipe_details[0]['name'])?$recipe_details[0]['name']:''; ?>">
									</div>
								</div>
								<div class="rest-detail-content">
									<h1><?php echo ($recipe_details[0]['name'])?$recipe_details[0]['name']:''; ?></h1>
									<p><?php echo ($recipe_details[0]['detail'])?$recipe_details[0]['detail']:''; ?></p>
									<ul>
										<li><i class="iicon-icon-18"></i><?php echo $this->lang->line('cooking_time') ?> : <?php echo ($recipe_details[0]['recipe_time'])?$recipe_details[0]['recipe_time']:''; ?> <?php echo $this->lang->line('minutes') ?></li>
									</ul>
								</div>
								<?php if (!empty($menu_details)) { 
								$recipe_page = $this->uri->segment(1);
								if($menu_details[0]->check_add_ons !=1){	?>
								<div class="">
									<button id="addtocart-<?php echo $menu_details[0]->entity_id ?>"onclick="checkCartRestaurantDetails('<?php echo $menu_details[0]->entity_id ?>','<?php echo $menu_details[0]->restaurant_id ?>','<?php echo $menu_details[0]->timings['closing']; ?>','',this.id,'yes','<?php echo $recipe_page; ?>')" class="recipe-view-menu"><?php echo $this->lang->line('view_menu'); ?></button>
								</div>
								<?php }else{
									?><div class="" style="margin-top: auto; margin-bottom: auto;">
									<button id="addtocart-<?php echo $menu_details[0]->entity_id ?>"onclick="checkCartRestaurantDetails('<?php echo $menu_details[0]->entity_id ?>','<?php echo $menu_details[0]->restaurant_id ?>','<?php echo $menu_details[0]->timings['closing']; ?>','addons',this.id,'yes','<?php echo $recipe_page; ?>')" class="recipe-view-menu"><?php echo $this->lang->line('view_menu'); ?></button>
								</div>
								<?php }	} ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="heading-title">
							<h2><?php echo $this->lang->line('recipe_text2') ?></h2>
						</div>
					</div>				
				</div>
				<div class="row recipe-detail-row">
					<?php if(!empty($recipe_details[0]['youtube_video'])){ ?>
					<div class="col-sm-12 col-md-6 col-lg-8">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h3 class="title_main"><?php echo $this->lang->line('video') ?></h3>
							</div>
							<div>
								<iframe width="760" height="415" src="<?php echo 'https://www.youtube.com/embed/'.$recipe_details[0]['youtube_video'] ?>"frameborder="0" allowfullscreen></iframe>
							</div>
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-4">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h3 class="ingredients"><i class="iicon-icon-29"></i><?php echo $this->lang->line('ingredients') ?></h3>
							</div>
							<ol class="bullet-style bullet-style-02">
								<?php echo ($recipe_details[0]['ingredients'])?$recipe_details[0]['ingredients']:''; ?>
							</ol>
						</div>
					</div>
					<?php } ?>	
				</div><br>
				<div class="row recipe-detail-row">
					<div class="col-sm-12 col-md-6 col-lg-8">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h3><?php echo $this->lang->line('directions') ?></h3>
							</div>
							<ol class="bullet-style">
								<?php echo ($recipe_details[0]['recipe_detail'])?$recipe_details[0]['recipe_detail']:''; ?>
							</ol>
						</div>
					</div>
					<?php if(empty($recipe_details[0]['youtube_video'])){ ?>
					<div class="col-sm-12 col-md-6 col-lg-4">
						<div class="recipe-detail-list">
							<div class="recipe-detail-title">
								<h3 class="ingredients"><i class="iicon-icon-29"></i><?php echo $this->lang->line('ingredients') ?></h3>
							</div>
							<ol class="bullet-style bullet-style-02">
								<?php echo ($recipe_details[0]['ingredients'])?$recipe_details[0]['ingredients']:''; ?>
							</ol>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
<div class="modal modal-main" id="anotherRestModal">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h3 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h3>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
	      <!-- Modal body -->
			<div class="modal-body">
	      		<form id="custom_cart_restaurant_form">
	      		<h5><?php echo $this->lang->line('res_details_text1') ?> <br><?php echo $this->lang->line('res_details_text2') ?></h5>
	      		<div class="popup-radio-btn-main">
		      		<div class="radio-btn-box">
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="hidden" name="rest_entity_id" id="rest_entity_id" value="">
			      				<input type="hidden" name="rest_restaurant_id" id="rest_restaurant_id" value="">
			      				<input type="hidden" name="is_addon" id="rest_is_addon" value="">
			      				<input type="hidden" name="item_id" id="item_id" value="">
			      				<input type="radio" checked="checked" class="radio_addon" name="addNewRestaurant" id="discardOld" value="discardOld">
			      				<span><?php echo $this->lang->line('discard_old') ?></span>
			      			</label>
			      		</div>
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="radio" class="radio_addon" name="addNewRestaurant" id="keepOld" value="keepOld">
			      				<span><?php echo $this->lang->line('keep_old') ?></span>
			      			</label>
			      		</div>
			      	</div>
		        </div>
		      	<div class="popup-total-main">
		      		<div class="total-price">
	      				<button type="button" class="cartrestaurant btn" id="cartrestaurant" onclick="ConfirmCartRestaurant('<?php echo (isset($recipe_page)) ? $recipe_page : ''; ?>')"><?php echo $this->lang->line('confirm') ?></button>
		      		</div>
		      	</div>
	      		</form>
			</div>
	    </div>
	</div>
</div>
<div class="modal modal-main" id="myconfirmModalDetails">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h3 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h3>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
	      <!-- Modal body -->
			<div class="modal-body">
	      		<form id="custom_items_form1">
	      		<h5><?php echo $this->lang->line('menu_already_added') ?> <br> <?php echo $this->lang->line('want_to_add_new_item') ?></h5>
	      		<div class="popup-radio-btn-main">
		      		<div class="radio-btn-box">
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="hidden" name="con_entity_id1" id="con_entity_id1" value="">
			      				<input type="hidden" name="is_closed1" id="is_closed1" value="">
			      				<input type="hidden" name="con_restaurant_id1" id="con_restaurant_id1" value="">
			      				<input type="hidden" name="con_item_id1" id="con_item_id1" value="">
			      				<input type="hidden" name="con_item_mandatory" id="con_item_mandatory" value="">
			      				<input type="radio" class="radio_addon" checked name="addedToCart1" id="addnewitem1" value="addnewitem">
			      				<span><?php echo $this->lang->line('as_new_item') ?></span>
			      			</label>
			      		</div>
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="radio" class="radio_addon" name="addedToCart1" id="increaseitem1" value="increaseitem1">
			      				<span><?php echo $this->lang->line('increase_quantity') ?></span>
			      			</label>
			      		</div>
			      	</div>
		        </div>
		      	<div class="popup-total-main">
		      		<div class="total-price">
	      				<button type="button" class="addtocart btn" id="addtocart1" onclick="ConfirmCartAddDetails('<?php echo (isset($recipe_page)) ? $recipe_page : '' ?>')"><?php echo $this->lang->line('add_to_cart') ?></button>
		      		</div>
		      	</div>
	      		</form>
			</div>
	    </div>
	</div>
</div>
	
<div class="modal modal-main modal-variation product-detail" id="menuDetailModal"></div>
<div class="modal modal-main modal-variation product-detail" id="addonsMenuDetailModal"></div>
<?php $this->load->view('footer'); ?>
