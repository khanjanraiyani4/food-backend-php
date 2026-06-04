<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php $this->load->view('header');
$menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
} 
//get System Option Data
/*$this->db->select('OptionValue');
$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
$currency_symbol = $currency_symbol->currency_symbol;*/
//get System Option Data
$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/tiny-slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/tiny-style.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
	<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<style>
	/*.custom-checkbox.filter-width{width: 30%;}*/
	#map_direction,#share_res_icon{
		height: 25px;
		margin-right: 12px;
	}
	.bookmark-btn, .bookmark-btn a{
		background-color: #000 !important;
		color: #fff;
	}
	@media screen and (max-width:575px){
		.booking-date-font.booking-option-text.calendar .bootstrap-datetimepicker-widget{
			left: 0px!important;
		}
	}
</style>
<?php $rest_background_image = (file_exists(FCPATH.'uploads/'.$restaurant_details['restaurant'][0]['background_image']) && $restaurant_details['restaurant'][0]['background_image']!='') ? image_url.$restaurant_details['restaurant'][0]['background_image'] : ''; ?>
<section class="inner-banner <?php echo ($rest_background_image != '')?'':'restaurant-detail-banner' ?>" <?php echo ($rest_background_image != '')?'style="background-image: url('.$rest_background_image.');"':''; ?> >
	<div class="container">
		<div class="inner-pages-banner">
		</div>
	</div>
</section>
<section class="inner-pages-section rest-detail-section">
	<div class="rest-detail-main">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="rest-detail">
						<div class="rest-detail-img-main">
							<div class="rest-detail-img">
								<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$restaurant_details['restaurant'][0]['image']) && $restaurant_details['restaurant'][0]['image']!='') ? image_url.$restaurant_details['restaurant'][0]['image'] : default_icon_img;  ?>
								<img src="<?php echo $rest_image ; ?>" >
							</div>
						</div>
						<div class="rest-detail-content">
							<h2><?php echo $restaurant_details['restaurant'][0]['name']; ?> </h2>
							<p><i class="iicon-icon-20"></i><?php echo $restaurant_details['restaurant'][0]['address']; ?></p>
							<ul>
								<?php if ($show_restaurant_reviews) {
									$rating_txt = ($restaurant_reviews_count > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
								<li><i class="iicon-icon-05"></i><?php echo ($restaurant_details['restaurant'][0]['ratings'] > 0)?$restaurant_details['restaurant'][0]['ratings'].' ('.$restaurant_reviews_count.' '.strtolower($rating_txt).')':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?></li>
								<?php } ?>
								<li class="rtl-num-cod position-relative res_time_li" id="res_time_li"><i class="iicon-icon-18"></i>
									<?php 
										$courrent_day = strtolower($restaurant_details['restaurant'][0]['timings']['current_day']);
									?>

									<?php echo (!empty($restaurant_details['restaurant'][0]['timings']['open']) && !empty($restaurant_details['restaurant'][0]['timings']['close']))?$this->lang->line($courrent_day).' : '.$this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['open']) . '-' . $this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['close']) : $this->lang->line("close_txt"); ?><i class="iicon-icon-25 time_arrow" onclick="restaurantTimingsList()"></i>
									<?php if(!empty($restaurant_details['restaurant'][0]['week_timings'])){ ?>
										<div class="timings-list-grp bg-white">
											<ul class="timings-list toggle_close">
												<?php foreach ($restaurant_details['restaurant'][0]['week_timings'] as $week_key => $week_value) { ?>
													<li><span><?php echo $this->lang->line(strtolower($week_key)); ?></span><?php echo (!empty($week_value['open']) && !empty($week_value['close']))?': '.$this->common_model->timeFormat($week_value['open']).' - '.$this->common_model->timeFormat($week_value['close']) : ': '.$this->lang->line('close_txt'); ?></li>
												<?php } ?>
											</ul>
										</div>
									<?php } ?>
								</li>
								<li class="rtl-num-cod"><i class="iicon-icon-19"></i><a href="tel:<?php echo $restaurant_details['restaurant'][0]['phone_number']; ?>"><?php echo $restaurant_details['restaurant'][0]['phone_number']; ?></a></li>
								<li><img src="<?php echo base_url();?>assets/front/images/map_direction.png" id="map_direction"><a href="http://maps.google.com/?q=<?php echo $restaurant_details['restaurant'][0]['latitude']; ?>,<?php echo  $restaurant_details['restaurant'][0]['longitude']; ?>" target="_blank"><?php echo $this->lang->line('map')." ".$this->lang->line('directions'); ?></a></li>	
								<?php if($this->session->userdata('UserID')){ ?>
									<li class="<?php echo ($get_bookmark==0)?'bg-white':'bookmark-btn'; ?>"><a href="javascript:void(0)" onclick="addBookmark('<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>')"><i class="iicon-icon-28"></i><?php echo ($get_bookmark==0)? $this->lang->line('add_bookmark'):$this->lang->line('bookmarked'); ?></a></li><?php } ?>							
							</ul>
							<?php $closed = ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'closed':''; ?>
							<div class="openclose <?php echo $closed; ?>"><?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="resttabs">
					<a href="#" class="active" id="menu_link"><button class="btn res-menu"><?php echo $this->lang->line('order').' '.$this->lang->line('online'); ?></button></a>
					<a href="#" id="aboutus_link"><button class="btn res-aboutus"><?php echo $this->lang->line('about_us'); ?></button></a>
					<?php if ($show_restaurant_reviews) { ?>
					<a href="#" id="review_link"><button class="btn res-review"><?php echo $this->lang->line('review_ratings'); ?></button></a>
					<?php } ?>
					<?php if($restaurant_details['restaurant'][0]['allow_event_booking'] == 1 || $restaurant_details['restaurant'][0]['enable_table_booking'] == 1){ ?>
					<a href="#" id="online_reservation_link"><button class="btn online_reservation_link"><?php echo $this->lang->line('online_reservation'); ?></button></a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="row restaurant-detail-row">
			<!-- restaurant details start-->	
			<div class="col-sm-12 menu-col" id="menu" style="display: block;" >
				<div class="tab--boddy">
					<?php if($restaurant_coupons){ ?>
					<section class="best-offers">
						<div class="heading-title">
							<h2><?php echo $this->lang->line('latest_coupons'); ?></h2>
							<?php if(count($restaurant_coupons) > 3){ ?>
								<div class="slider-arrow">
									<div id="customNav2" class="arrow"></div>
								</div>
							<?php } ?>
						</div>
						<div class="best-offers-slider owl-carousel">
							<?php
							 foreach ($restaurant_coupons as $key => $value) { ?>
								<div class="best-offers-box">
									<div>
										<!-- <p><?php echo $this->lang->line('coupon_code')." - ".$value->name; ?></p> -->
										<!-- <p><?php echo $value->amount; ?><?php echo ($value->amount_type == 'Percentage') ? "% " : " " ?><?php echo strtoupper($this->lang->line('off'));?></p> -->
										<!-- <p><?php echo $this->lang->line('valid_till')." ".date("l jS \of F Y", strtotime($value->end_date)); ?></p> -->
										<!-- <p><?php echo $this->lang->line('min_order_amount')." - ".$value->max_amount; ?></p> -->
									</div>
									<?php  $cpn_img = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : default_img;  ?>
									<img src="<?php echo $cpn_img ?>" alt="<?php echo  $value->name ?>" title="<?php echo  $value->name ?>">
									
								</div>
							<?php } ?>
						</div>
					</section>
					<?php } ?>
					<!-- Element Outside -->
					<div class="row">
						<div class="col-sm-12">
							<div class="heading-title">
								<h2><?php echo $this->lang->line('order_food_from') ?> <?php echo $restaurant_details['restaurant'][0]['name']; ?></h2>
							</div>
							<!--  -->
							<div class="search-dishes search_with_filter">
								<div class="inner-pages-form">
									<div class="form-group search-restaurant">
										<input class="input-tags" type="text" name="search_dish" placeholder="<?php echo $this->lang->line('search_dishes') ?>" id="search_dish">
										<input type="hidden" name="srestaurant_id" id="srestaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>">
										<!-- <input type="button" name="Search" value="<?php echo $this->lang->line('search') ?>" class="btn" onclick="searchMenuDishes(<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>)"  id="Search_btn" disabled> -->
										<button class="btn filter-cancel" title="Reset" name="Reset" alt="Reset" id="search_dish_reset_btn" disabled><i class="iicon-icon-23"></i></button>
									</div>
								</div>

								<div class="acc-filter">						
			    					<div id="accordion">
										<div id="headingOne" class="acc-heading">
											<h5 data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"></h5>
										</div>
										<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
											<!--  -->
											<div class="row option-filter-tab mr-0 ml-0">
												<?php
									        	$resfood_type = $restaurant_details['restaurant'][0]['resfood_type'];
									        	if(!empty($resfood_type)>0 && count($resfood_type)>0)
									        	{ ?>
												<div class="col-md-12 rest-detail-content" style="padding-top:15px;padding-left:15px"><h2 style="font-size: 16px;"><?php echo $this->lang->line('sort_by_food_type') ?></h2></div>
							    				<div class="col-md-12">
										        <?php	
										        		for($fdt=0;$fdt<count($resfood_type);$fdt++) {
										        	 	?>
										        		<div class="custom-control custom-checkbox filter-width">
															<input type="radio" <?php if(count($resfood_type)==1) {?> checked <?php } ?> name="filter_food" class="custom-control-input" id="filter_<?=$resfood_type[$fdt]->food_type_id?>" value="<?=$resfood_type[$fdt]->food_type_id?>" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value,'yes','no')">
															<label class="custom-control-label" for="filter_<?=$resfood_type[$fdt]->food_type_id?>"><?php echo ucfirst($resfood_type[$fdt]->food_type_name); ?></label>
														</div>
										        	<?php }
										        	if(count($resfood_type)>1) { ?>
										        	<div class="custom-control custom-checkbox filter-width">
														<input type="radio" checked="checked" name="filter_food" class="custom-control-input" id="all" value="all" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value)">
														<label class="custom-control-label" for="all"><?php echo $this->lang->line('view_all') ?></label>
													</div>
													<?php } ?>
												</div>
										        	<?php  } ?>									        	
									        	<?php /*
									        	<div class="col-md-4">
													<div class="custom-control custom-checkbox">
													    <input type="radio" checked="checked" name="filter_price" class="custom-control-input" id="filter_high_price" value="filter_high_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
													    <label class="custom-control-label" for="filter_high_price"><?php echo $this->lang->line('sort_by_price_low') ?></label>
												  	</div>
													<div class="custom-control custom-checkbox">
														<input type="radio" name="filter_price" class="custom-control-input" id="filter_low_price" value="filter_low_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
														<label class="custom-control-label" for="filter_low_price"><?php echo $this->lang->line('sort_by_price_high') ?></label>
													</div>
												</div>
												*/?>

												<?php //New code add for availability :: Start ?>
												<div class="col-md-12 rest-detail-content" style="padding-top:15px;padding-left:15px"><h2 style="font-size: 16px;"><?php echo $this->lang->line('sort_availability') ?></h2></div>
												<div class="col-md-12">					        	
									        		<div class="custom-control custom-checkbox filter-width">
														<input type="radio"  name="filter_availibility" class="custom-control-input" id="filter_breakfast" value="Breakfast" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value,'no','yes')">
														<label class="custom-control-label" for="filter_breakfast"><?php echo $this->lang->line('breakfast') ?></label>
													</div>
													<div class="custom-control custom-checkbox filter-width">
														<input type="radio"  name="filter_availibility" class="custom-control-input" id="filter_lunch" value="Lunch" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value,'no','yes')">
														<label class="custom-control-label" for="filter_lunch"><?php echo $this->lang->line('lunch') ?></label>
													</div>
													<div class="custom-control custom-checkbox filter-width">
														<input type="radio" name="filter_availibility" class="custom-control-input" id="filter_dinner" value="Dinner" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value,'no','yes')">
														<label class="custom-control-label" for="filter_dinner"><?php echo $this->lang->line('dinner') ?></label>
													</div>
										        	<div class="custom-control custom-checkbox filter-width">
														<input type="radio" checked="checked" name="filter_availibility" class="custom-control-input" id="all_availibility" value="all" onclick="menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',this.value)">
														<label class="custom-control-label" for="all_availibility"><?php echo $this->lang->line('view_all') ?></label>
													</div>					        	
									        	</div>
									        	<?php //New code add for availability :: End ?>	
							    			</div>
							    			<!--  -->
							    		</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-5 col-lg-8">
							
							<div id="details_content">
								<?php if (!empty($restaurant_details['menu_items']) || !empty($restaurant_details['categories'])) { ?>
					    			<div id="res_detail_content">
					    				<?php if (!empty($restaurant_details['categories'])) {?>
										<div class="slider-checkbox-main">
												 <!-- <button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left move left" type="button"><i class="iicon-icon-16"></i></button>									 -->
												 <nav class="cat-loop">
													<ul class="autoWidth-non-loop" id="autoWidth-non-loop">	
														<?php if (!empty($restaurant_details['menu_items'])) {
													        $popular_count = 0;
													        foreach ($restaurant_details['menu_items'] as $key => $value) {
													            if ($value['popular_item'] == 1) {
													                $popular_count = $popular_count + 1;
													            }
													        }
													    }
													    $ccc=1;
													    if ($popular_count > 0) { ?>
													    	<li class="item" id="categorytop<?php echo $ccc; ?>"><a href="#popular_menu_item" class="active"><?php echo $this->lang->line('popular_items'); ?></a></li>
													    <?php $ccc=2; 
														} ?>
													    <?php										    
													    foreach ($restaurant_details['categories'] as $key => $value) {?>
													    	<li class="item" id="categorytop<?php echo $ccc; ?>"><a href="#category-<?php echo $value['category_id']; ?>" <?php if($ccc==1){?> class="active" <?php } ?>><?php echo $value['name']; ?></a></li>
										    			<?php
										    			$ccc++;
										    			 }?>
													</ul>
												</nav>
												<!-- <button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right move right" type="button"><i class="iicon-icon-17"></i></button> -->
											</div>
							    	<?php }?>
							    	<div class="is_close">	<?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'<span id="closedres">'.$this->lang->line('not_accepting_orders').'</span>':''; ?>
					    			</div>
										<?php if (!empty($restaurant_details['menu_items'])) {
									        $popular_count = 0;
									        foreach ($restaurant_details['menu_items'] as $key => $value) {
									            if ($value['popular_item'] == 1) {
									                $popular_count = $popular_count + 1;
									            }
									        }
									        if ($popular_count > 0) { ?>
									        	<div class="detail-list-title collapse-header">
													<a data-toggle="collapse" href="#popular_menu_item" role="button" aria-expanded="false" aria-controls="popular_menu_item"><h2 class="text-white p-3"><?php echo $this->lang->line('popular_items') ?></h2></a>
												</div>
												<div class="collapse detail-list-box-main sliderMenutoggle show" id="popular_menu_item">
													<?php foreach ($restaurant_details['menu_items'] as $key => $value) {
				                						if ($value['popular_item'] == 1) { ?>
															<div class="detail-list-box type-food-option">
															 	<div class="detail-list">
																	<div class="detail-list-img">
																		<div class="list-img">
																			<?php /* $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img; ?>
																			<img src="<?php echo $rest_image; ?>">
																			<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div><?php */ ?>
																		<?php $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_icon_img;
																		if ($value['check_add_ons'] == 1) { ?>
																			<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <img src="<?php echo $rest_image; ?>"> </a>

																			<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
																		<?php } else {?>
																			<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <img src="<?php echo $rest_image; ?>"> </a>

																			<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
																		<?php } ?>
																		</div>
																	</div>
																	<div class="detail-list-content">
																		<div class="detail-list-text">
																			<!-- <h4><?php //echo $value['name']; ?></h4> -->
																			<!-- menu details on item name click :: start -->
																			<?php if ($value['check_add_ons'] == 1) { ?>
																				<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <h4><?php echo $value['name']; ?></h4> </a>
																			<?php } else {?>
																				<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <h4><?php echo $value['name']; ?></h4> </a>
																			<?php } ?>
																			<?php
																			$food_type_name ='';
																			 foreach ($restaurant_details['restaurant'][0]['resfood_type'] as $key => $val) {
																				if($val->food_type_id == $value['food_type']){
																					$food_type_name = $val->food_type_name;break;
																				}
																			} if(!empty($food_type_name)){ ?>
																			<div><?php echo $this->lang->line('food_type')." : " ?><?php echo $food_type_name; ?></div> <?php } ?>
																			<div><?php echo $this->lang->line('availability')." : " ?><?php echo $value['availability']; ?></div>
																			<!-- menu details on item name click :: end -->
																			<p><?php echo $value['menu_detail']; ?></p>
																			<strong <?php if($value['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>>
																				<?php echo ($value['check_add_ons'] != 1)?currency_symboldisplay(number_format($value['price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):(($value['price'])?currency_symboldisplay(number_format($value['price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):''); ?>
																			</strong>
																			<?php if($value['offer_price']>0){ ?>
																				<strong class="pl-2">
																				<?php echo ($value['check_add_ons'] != 1)?currency_symboldisplay(number_format($value['offer_price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):(($value['offer_price'])?currency_symboldisplay(number_format($value['offer_price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):''); ?>
																			</strong>
																			<?php } ?>
																			
																		</div>
																		<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
																			if ($value['check_add_ons'] == 1) {?>
																				<div class="add-btn">
																					<?php if($value['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1') { ?>
																						<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
																						<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)" order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
																						<span class="cust" style="text-align:center;"><?php echo $this->lang->line('customizable') ?></span>
																						<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) { ?>
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																							
																						<?php } ?>
																					<?php }
																					else{ ?>
																						<div class="add-btn">
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																						</div>
																					<?php } ?>
																				</div>
																			<?php } else { ?>
																				<div class="add-btn">
																					<?php if($value['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
																						<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
																						<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
																						<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) { ?>
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																						<?php } ?>
																					<?php }else{ ?>
																						<div class="add-btn">
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																						</div>
																					<?php } ?>	
																				</div>
																			<?php } ?>
																		<?php } ?>
																	</div>
																</div>
															</div>
														<?php }
				            						}?>
												</div>
											<?php }?>
										<?php }?>
										<?php if (!empty($restaurant_details['categories'])) {
											$tottalcnt = count($restaurant_details['categories']);
									        foreach ($restaurant_details['categories'] as $key => $value) { ?>
									        	<div class="detail-list-title collapse-header">
									    			<a data-toggle="collapse" href="#category-<?php echo $value['category_id']; ?>" role="button" aria-expanded="false" aria-controls="category-<?php echo $value['category_id']; ?>"><h2 class="text-white p-3"><?php echo $value['name']; ?></h2></a>
												</div>
												<div class="collapse detail-list-box-main categories sliderMenutoggle show" id="category-<?php echo $value['category_id']; ?>" >
													<?php 
													$margin_text = '';
													if($restaurant_details[$value['name']]) {
														if(count($restaurant_details[$value['name']])==1){ // && $tottalcnt==($key+1)
															$margin_text = 'style="margin-bottom:60px !important;"';
														}
													}
													?>
													<div class="detail-list-box type-food-option" <?php echo $margin_text; ?>>
														<?php if ($restaurant_details[$value['name']]) {
					                						foreach ($restaurant_details[$value['name']] as $key => $mvalue) {?>
																<div class="detail-list">
																	<div class="detail-list-img">
																		<div class="list-img">
																			<?php /* $rest_image = (file_exists(FCPATH.'uploads/'.$mvalue['image']) && $mvalue['image']!='') ? image_url.$mvalue['image'] : default_img; ?>
																			<img src="<?php echo $rest_image; ?>"><?php */ ?>
																		<?php $rest_image = (file_exists(FCPATH.'uploads/'.$mvalue['image']) && $mvalue['image']!='') ? image_url.$mvalue['image'] : default_icon_img;
																		if ($mvalue['check_add_ons'] == 1) {?>
																			<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <img src="<?php echo $rest_image; ?>"> </a>
																		<?php } else {?>
																			<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <img src="<?php echo $rest_image; ?>"> </a>
																		<?php }  ?>
																		</div>
																	</div>
																	<div class="detail-list-content">
																		<div class="detail-list-text">
																			<!-- <h4><?php //echo $mvalue['name']; ?></h4> -->
																			<!-- menu details on item name click :: start -->
																			<?php if ($mvalue['check_add_ons'] == 1) {?>
																				<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <h4><?php echo $mvalue['name']; ?></h4> </a>
																			<?php } else {?>
																				<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <h4><?php echo $mvalue['name']; ?></h4> </a>
																			<?php }  ?>
																			<?php 
																			$mfood_type_name ='';
																			foreach ($restaurant_details['restaurant'][0]['resfood_type'] as $key => $mval){
																					if($mval->food_type_id == $mvalue['food_type']){
																					$mfood_type_name = $mval->food_type_name;break;
																				}
																			} if(!empty($mfood_type_name)){ ?>
																			<div><?php echo $this->lang->line('food_type')." : " ?><?php echo $mfood_type_name; ?></div> <?php } ?>
																			<div><?php echo $this->lang->line('availability')." : " ?><?php echo $mvalue['availability']; ?></div>
																			<!-- menu details on item name click :: end -->
																			<p><?php echo $mvalue['menu_detail']; ?></p>
																			<strong <?php if($mvalue['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>><?php echo ($mvalue['check_add_ons'] != 1)?currency_symboldisplay(number_format($mvalue['price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):(($mvalue['price'])?currency_symboldisplay(number_format($mvalue['price'],2),$restaurant_details['restaurant'][0]['currency_symbol']):''); ?></strong>
																			<?php if($mvalue['offer_price']>0){ ?>
																				<strong><?php echo ($mvalue['check_add_ons'] != 1)?currency_symboldisplay(number_format(str_replace(",","",$mvalue['offer_price']),2),$restaurant_details['restaurant'][0]['currency_symbol']):(($mvalue['offer_price'])?currency_symboldisplay(number_format(str_replace(",","",$mvalue['offer_price']),2),$restaurant_details['restaurant'][0]['currency_symbol']):''); ?></strong>
																			<?php } ?>
																		</div>
																		<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
																			if ($mvalue['check_add_ons'] == 1) {
																				if($mvalue['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
																					<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
																					<div class="add-btn">
																						<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)" order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
																						<span class="cust" style="text-align:center;"><?php echo $this->lang->line('customizable') ?></span>
																						<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) { ?>
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																						<?php } ?>
																					</div>
																				<?php }else{ ?>
																					<div class="add-btn">
																						<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																					</div>
																				<?php } ?>
																			<?php } else {
																				if($mvalue['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1') { ?>
																					<div class="add-btn">
																						<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
																						<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
																						<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) { ?>
																							<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																						<?php } ?>
																					</div>
																				<?php } else { ?>
																					<div class="add-btn">
																						<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
																					</div>
																				<?php } ?>
																			<?php } 
																		} ?>
																	</div>
																</div>
															<?php }
					            						}?>
													</div>
												</div>
											<?php }
				    					} ?>
				    				</div>
								<?php } 
								else {?>
								<div class="slider-checkbox-main">
									<div class="cart-empty text-center" style="padding:10px;">
										<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
										<h6><?php echo $this->lang->line('no_results_found') ?></h6>
									</div>
								</div>
								<?php }?>
							</div>
						</div>

						<div class="col-sm-12 col-md-5 col-lg-4 your_cart-c" id="your_cart">
							<div class="your-cart-main">
								<div class="your-cart-title">
									<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('your_cart') ?></h3>
									<h6><?php echo count($cart_details['cart_items']); ?> <?php echo $this->lang->line('items') ?></h6>
									<?php if(count($cart_details['cart_items']) > 0 ){ ?>
										<a class="btn res-view-all"  href="<?php echo base_url() . 'cart'; ?>"><?php echo $this->lang->line('view_cart') ?></a>
									<?php } ?>
								</div>
								<?php if (!empty($cart_details['cart_items'])) { ?>
								<div class="add-cart-list-main">
								    <?php foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
										<div class="add-cart-list">
											<div class="cart-list-content">
												<h5><?php echo $value['name']; ?></h5>
												<strong><?php echo currency_symboldisplay(number_format($value['totalPrice'],2), $restaurant_details['restaurant'][0]['currency_symbol']); ?></strong>
												<?php if ($value['is_combo_item']) {?>
													<p><?php echo nl2br($value['menu_detail']); ?></p>
												<?php }?>
												<?php if (!empty($value['addons_category_list'])) {?>
													<ul class="ul-disc">
			    									<?php foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
														<?php /* <li><h6><?php echo $cat_value['addons_category']; ?></h6></li> */ ?>
														
														<?php if (!empty($cat_value['addons_list'])) {?>
															<ul class="ul-cir">
			            									<?php foreach ($cat_value['addons_list'] as $key => $add_value) {?>
																<li><?php echo $add_value['add_ons_name']; ?> <?php echo currency_symboldisplay(number_format($add_value['add_ons_price'],2),$restaurant_details['restaurant'][0]['currency_symbol']); ?></li>
															<?php }?>
															</ul>
														<?php }?>
													<?php }?>
													</ul>
												<?php }?>									
												
											</div>
											<div class="add-cart-item">									
												<div class="number">
													<span class="minus" id="minusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
													<input type="text" class="QtyNumberval" maxlength="3" value="<?php echo $value['quantity']; ?>" onfocusout="EditcustomItemCount(this.value,<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,<?php echo $cart_key; ?>)" />
													<span class="plus" id="plusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
												</div>
											</div>
										</div>
									<?php }?>
								</div>
								<div class="cart-subtotal">
									<strong><?php echo $this->lang->line('sub_total') ?></strong>
									<strong class="price"><?php echo currency_symboldisplay(number_format($cart_details['cart_total_price'],2),$restaurant_details['restaurant'][0]['currency_symbol']); ?></strong>
								</div>
								<div class="continue-btn">
									<a href="javascript:void(0);" class="continue_btn" onclick="checkResStat();"><button class="btn"><?php echo $this->lang->line('continue') ?></button></a>
								</div>
								<div class="res_closed_err" style="display: none; color: red;"></div>
								<?php //get System Option Data
				                    $this->db->select('OptionValue');
				                    $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
				                    $min_order_amount = (float) $min_order_amount->OptionValue;
									$min_order_txt = sprintf($this->lang->line('min_order_msg'),$min_order_amount); ?>
								<div class="min_order_txt mt-3" style="<?php echo (!in_array('Delivery', $restaurant_details['restaurant'][0]['order_mode']))?'display: none;':(($cart_details['cart_total_price'] >= $min_order_amount)?'display: none;':'display: block;'); ?>">
									<p><?php echo $min_order_txt; ?></p>
								</div>
								<?php } else { ?>
									<div class="cart-empty text-center">
										<picture>											
											<source type="image/jpg" srcset="<?php echo base_url();?>assets/front/images/empty-cart.png">
											<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
										</picture>
										<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_dishes') ?> <br>
											
										</h6>
										
									</div>
									<?php 
									$class_text = 'display: none;';
									if($restaurant_details && !empty($restaurant_details['restaurant']))
									{
										$class_text = (!in_array('Delivery', $restaurant_details['restaurant'][0]['order_mode']))?'display: none;':'display: block';
									}
									?>
									<div class="min_order_txt mt-3" style="<?php echo $class_text; ?>">
								<?php //get System Option Data
				                    $this->db->select('OptionValue');
				                    $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
				                    $min_order_amount = (float) $min_order_amount->OptionValue;
									$min_order_txt = sprintf($this->lang->line('min_order_msg'),$min_order_amount); 
								?>
								<p><?php echo $min_order_txt; ?></p>
								</div>
				
								<?php } ?>		
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- restaurant details end -->
			<!-- restaurant overview : start-->
			<div class="col-sm-12" id="aboutus" style="display: none;" >
				<div class="tab--boddy">
					<div class="detail-list-box-main">
						<?php if(!empty($restaurant_details['restaurant'][0]['about_restaurant']) || !is_null($restaurant_details['restaurant'][0]['about_restaurant'])){ ?>
						<div class=" heading-title">
							<h2><?php echo $this->lang->line('about_restaurant') ?></h2>
						</div>
							<div>
								<?php echo $restaurant_details['restaurant'][0]['about_restaurant']; ?>
							</div>
						<?php } ?>
						<div class="overview_in">
							<h6><font style="border-bottom: 1.7px solid #000;"><?php echo $this->lang->line('report_res_msg1'); ?></font></h6>
							<p><?php echo $this->lang->line('report_res_msg2'); ?></p>
							<strong><a href="javascript:void(0)" id="report_restaurant"><u><?php echo $this->lang->line('report_now'); ?></u></a></strong>
						</div>
					</div>
				</div>
			</div>
			<!-- restaurant overview : end-->
			<!-- ratings and review start -->
			<div class="col-sm-12" id="review" style="display: none;" >
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('review_ratings') ?></h3>
					</div>
					<?php if ($show_restaurant_reviews) { ?>
					<div class="rating-review-main">
						<div class="review-progress">
							<div class="progress-main">
								<div class="review-all">
									<p class="text-center"><?php echo (!empty($restaurant_reviews_count))?$restaurant_reviews_count:0; ?> <?php echo (!empty($restaurant_reviews))?(($restaurant_reviews_count > 1)?$this->lang->line('reviews'):$this->lang->line('review')):$this->lang->line('review'); ?></p>
								</div>
									<?php for ($i=5; $i > 0 ; $i--) { ?>
									<div class="progress-box">
										<span class="star-icon"><?php echo $i; ?></span>
										<div class="progress">		
											<?php 
											$noOfReviews = $this->restaurant_model->getReviewsNumber($restaurant_details['restaurant'][0]['content_id'],$i);

											$percentage=0;
											if($restaurant_reviews_count>0){
												$percentage = ($restaurant_details['restaurant'][0]['is_rating_from_res_form'] == '1' && $noOfReviews == 1) ? '50' : ($noOfReviews * 100) / $restaurant_reviews_count;	
											} ?>
											<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentage.'%'; ?>">									
											</div>								  
										</div> 
										<span><?php echo ($restaurant_details['restaurant'][0]['is_rating_from_res_form'] == '1' && $noOfReviews == 1) ? $restaurant_reviews_count : $noOfReviews; ?></span>
									</div>
								<?php } ?>
							</div>	
						</div>	
						<div class="rate-restaurant">	
							<div class="star-rating-main">					
								<div class="star-rating">
									<?php for ($i=1; $i < 6; $i++) { 
										$activeClass = ''; 
										if ($i <= $restaurant_details['restaurant'][0]['ratings']) {
										$activeClass = 'active'; ?>
									<?php } ?>
									<button class="<?php echo $activeClass; ?>"><i class="iicon-icon-28"></i></button>
									<?php } ?>
								</div>
								<?php if ($show_restaurant_reviews) { ?>
								<div class="review-all">
									<span><i class="iicon-icon-05"></i><?php echo $restaurant_details['restaurant'][0]['ratings']; ?></span>
								</div>
								<?php } ?>
							</div>								
						</div>
					</div>					
					<div class="review-box-main">
						<div id="limited-reviews">
							<?php if (!empty($restaurant_reviews) && $show_restaurant_reviews) {
								foreach ($restaurant_reviews as $key => $value) { 
									if ($key <= (review_count-1)) { ?>
										<div class="review-list">
											<?php /* ?><div class="review-img">
												<div class="user-images">
													<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image']:default_icon_img; ?>">
												</div>
											</div><?php */ ?>
											<div class="review-content">
												<div class="user-name-date">
													<div class="review-date">
														<h3><?php echo $value['first_name'].' '.$value['last_name']; ?></h3>
														<span><?php echo $this->common_model->dateFormat($value['created_date']); ?></span>
													</div>
													<div class="review-star">
														<span><i class="iicon-icon-05"></i>(<?php echo number_format($value['rating'],1); ?>)</span>
													</div>
												</div>
												<p>"<?php echo ucfirst($value['review']); ?>"</p>
											</div>
										</div>
									<?php }
								}
							} else { 
								if($restaurant_details['restaurant'][0]['is_rating_from_res_form'] != '1') { ?>
									<div>
										<div class="cart-empty text-center" style="padding:10px;padding-top: 0px;">									
											<h6><?php echo $this->lang->line('no_review_found') ?></h6>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
						<div id="all_reviews" class="display-no" >
						<?php /* if (!empty($restaurant_reviews)) {
							foreach ($restaurant_reviews as $key => $value) {
								if ($key > (review_count-1)) { ?>
									<div class="review-list">
										<div class="review-img">
											<div class="user-images">
												<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image']:default_icon_img; ?>">
											</div>
										</div>
										<div class="review-content">
											<p>"<?php echo ucfirst($value['review']); ?>"</p>
											<div class="user-name-date">
												<div class="review-star">
													<span><i class="iicon-icon-05"></i><?php echo number_format($value['rating'],1); ?></span>
												</div>
												<div class="review-date">
													<h3><?php echo $value['first_name'].' '.$value['last_name']; ?></h3>
													<span><?php echo $this->common_model->dateFormat($value['created_date']); ?></span>
												</div>
											</div>
										</div>
									</div>
								<?php }
							}
						} */ ?>
						</div>
						<?php if (!empty($restaurant_reviews) && $restaurant_reviews_count > review_count && $show_restaurant_reviews) { ?>
							<input type="hidden" name="page_no" id="page_no" value="2">
							<input type="hidden" name="res_content_id_val" id="res_content_id_val" value="<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>">
							<button id="review_button" class="btn btn-success danger-btn" onclick="showAllReviews()"><?php echo $this->lang->line('load_more') ?></button>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<!-- ratings and review end -->
			<!-- Online Reservation: start-->
			<div class="col-sm-12" id="online_reservation" style="display: none;" >
				<div class="tab--boddy">
				<!-- <div class="detail-list-box-main"> -->
					<?php if($this->session->userdata('UserID')) { ?>
						<section class="inner-pages-section order-food-section">
							<div class="event_table">
								<div class="menu_review">
									<?php if($restaurant_details['restaurant'][0]['allow_event_booking'] == 1){ ?>
									<a href="#" class="<?php echo ($restaurant_details['restaurant'][0]['allow_event_booking'] == 1) ? "active" : "" ?>" id="event_link"><button class="btn res-event"><?php echo $this->lang->line('book_event'); ?></button></a>
								<?php } ?>
								<?php if($restaurant_details['restaurant'][0]['enable_table_booking'] == 1){ ?>
									<a href="#" id="table_link" class="<?php echo (($restaurant_details['restaurant'][0]['allow_event_booking'] != 1 || $restaurant_details['restaurant'][0]['allow_event_booking'] == '') && $restaurant_details['restaurant'][0]['enable_table_booking'] == 1) ? "active" : "" ?>"><button class="btn res-table"><?php echo $this->lang->line('book_table'); ?></button></a>
								<?php } ?>
								</div>
							</div>
							<?php if($restaurant_details['restaurant'][0]['allow_event_booking'] == 1){ ?>
							<div id="event_section" style="<?php echo ($restaurant_details['restaurant'][0]['allow_event_booking'] == 1) ? "display: block" : "" ?>">
								<div class="row">
									<div class="col-lg-12">
										<div class="heading-title">
											<h2><?php echo $this->lang->line('book_your_event') ?></h2>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="your-booking-main">
											<div class="detail-list-box">
												<form id="check_event_availability" class="form-horizontal" name="check_event_availability" method="post">
													<input type="hidden" name="event_restaurant_id" id="event_restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_content_id']; ?>">
													<input type="hidden" name="event_user_id" id="event_user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
													<input type="hidden" name="event_name" id="event_name" value="<?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?>">
													<div class="row">
														<div class="col-lg-6">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('pick_date') ?><span class="required">*</span></label>
																<div class="booking-date-font booking-option-text calendar" style="padding-left:0;">
																	<input type='text' class="form-control bg-white" name="date_time" id='datetimepicker1' placeholder="<?php echo $this->lang->line('pick_date') ?>"  readonly="readonly" value = "<?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('date_time')))? $this->session->userdata('date_time'): '' ?>" >
																</div>
															</div>
														</div>
														<div class="col-lg-6">
															<div class="form-group">
																<label class="control-label" style="display:inline-block;"><?php echo $this->lang->line('how_many_people') ?><span class="required">*</span></label>
																<?php $message = $this->lang->line('max_people');
																	  $event_capacity = sprintf($message,$restaurant_details['restaurant'][0]['event_minimum_capacity'],$restaurant_details['restaurant'][0]['capacity'])
																 ?>
																<input type="number" name="no_of_people" id="no_of_people" class="form-control" autocomplete="off" value="<?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('no_of_people')))? $this->session->userdata('no_of_people'): ' ' ?>">
																<div class="max-event-people" style="font-size: 13px;padding-bottom: 6px;display:inline-block;"><?php echo $event_capacity ?></div>
															</div>
														</div>
													</div>
													<!-- <div class="row">
														<div class="col-12">
						                                    
						                                </div>
						                                <div class="col-md-3">
						                                    <div class="form-group">
						                                       	<input type="text" name="first_name" id="event_first_name" class= "form-control" value="<?php echo ($this->session->userdata('userFirstname'))?($this->session->userdata('userFirstname')):"" ?>" placeholder="<?php echo $this->lang->line('enter_first_name'); ?>">
						                                    </div>  
						                                </div>
						                                <div class="col-md-3">
						                                    <div class="form-group">
						                                        <input type="text" name="last_name" id="event_last_name" class= "form-control" value="<?php echo ($this->session->userdata('userLastname'))?($this->session->userdata('userLastname')):"" ?>"  placeholder="<?php echo $this->lang->line('enter_last_name'); ?>">
						                                    </div>
						                                </div>
						                                <div class="col-md-3">
						                                    <div class="form-group table_class">
						                                    	<input type="hidden" name="phone_code" id="event_phone_code" class="form-control" value="">
						                                        <input type="tel" name="phone_number_inp" id="event_phone_number_inp" class="form-control" value="<?php echo ($this->session->userdata('userPhone'))?($this->session->userdata('userPhone')):"" ?>" maxlength="14" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>">
						                                    <div id="event_phone_number_error"></div>
						                                  	</div>
						                               	</div>
						                                <div class="col-md-3">  
						                                    <div class="form-group">
						                                        <input type="text" name="email" id="event_email" class= "form-control" value="<?php echo ($this->session->userdata('userEmail'))?($this->session->userdata('userEmail')):"" ?>" placeholder="<?php echo $this->lang->line('enter_email_address'); ?>">  
						                                        <div id="event_email_error"></div>
						                                    </div>
						                                </div>
													</div> -->
													<?php if (!empty($restaurant_details['packages']) && count($restaurant_details['packages']) > 0) {
													?>
														<div class="row">
															<div class="col-sm-12">
																<div class="form-group">
																	<label class="control-label" for="package"><?php echo $this->lang->line('package') ?></label>
																	<select name="package_id" class="form-control sumo" id="package_id" onchange="getPackageInfo(this.value,'<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>')">
																		<option value=""><?php echo $this->lang->line('select') ?></option>
																		<?php foreach ($restaurant_details['packages'] as $key => $value) { ?>
																			<option value="<?php echo $value['content_id']; ?>"><?php echo $value['name']; ?></option>
																		<?php } ?>
																	</select>
																</div>
															</div>
															
															<div class="package-content" id="package_section" data-id="" style="display: none;">
																<div class="col-md-12">
																	<div class="detail-list-box" id="package_detaildiv">
																		
																	</div>
																</div>
															</div>
															
														</div>
													<?php } ?>
													<div class="row">
														<div class="col-lg-12">
															<div class="form-group">
																<label class="control-label" style="display:inline-block;"><?php echo $this->lang->line('additional_comment') ?></label>
																<div id="max_people" style="display:inline-block;"><?php echo $this->lang->line('max_allowed') ?></div>
																<textarea class="form-control" name="user_comment" id="user_comment" rows="5" style="resize:none;"><?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('event_user_request'))) ? ($this->session->userdata('event_user_request')) : ' '; ?></textarea>
															</div>
														</div>
													</div>
													<div class="table-btn">
						                                <button type="submit" name="submit_page" id="submit_page" value="Check Availability" class="btn load_more_btn"><?php echo $this->lang->line('check_avail') ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
							<?php if($restaurant_details['restaurant'][0]['enable_table_booking'] == 1){ ?>
							<div id="table_section" style="<?php echo (($restaurant_details['restaurant'][0]['allow_event_booking'] != 1 || $restaurant_details['restaurant'][0]['allow_event_booking'] == '') && $restaurant_details['restaurant'][0]['enable_table_booking'] == 1) ? "display: block" : "display: none" ?>;">
								<div class="row">
									<div class="col-lg-12">
										<div class="heading-title">
											<h2><?php echo $this->lang->line('book_table') ?></h2>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="your-booking-main table_booking_code">
											<div class="detail-list-box">
												<form id="check_table_availability" class="form-horizontal" name="check_table_availability" method="post">
													<input type="hidden" name="table_restaurant_id" id="table_restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_content_id']; ?>">
													<input type="hidden" name="table_user_id" id="table_user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
													<input type="hidden" name="table_name" id="table_name" value="<?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?>">
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('what_day') ?><span class="required">*</span></label>
																<select name="datepicker" onchange="addSlot()" class="form-control sumo_date sumo" id="datepicker">
						                                            <option value=""><?php echo $this->lang->line('select_day') ?></option>
						                                            <?php foreach ($restaurant_details['timearr'] as $key => $value) { ?>
						                                            	<option value="<?php echo $value ?>" <?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('booking_date') == $value)) ? 'selected' : ' '; ?>><?php echo $value ?></option>
						                                            <?php } ?>
						                                        </select>
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('start_time') ?><span class="required">*</span></label>
																<select name="starttime" id="starttime" onchange="addEndTimeSlot('is_start')" class="form-control sumo_time sumo">
																    <?php foreach ($restaurant_details['timeslots'] as $key => $value) { ?>
																        <option value="<?php echo $value ?>" <?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('start_time') == $value)) ? 'selected' : ' '; ?>><?php echo $value;?></option>
																    <?php } ?>
																</select>
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('end_time') ?><span class="required">*</span></label>
																<select name="endtime" id="endtime" onchange="addEndTimeSlot('is_end')" class="form-control sumo_time sumo">
																	<?php $arry_size = sizeof($restaurant_details['timeslots']); ?>
																    <?php foreach ($restaurant_details['timeslots'] as $key => $value) {
																    $selected = (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('end_time') == $value)) ? ('selected') : (($this->session->userdata('is_user_login') != 1)&&($key==$arry_size-1) ? 'selected' : "");

																    ?>
																        <option value="<?php echo $value ?>" <?php echo $selected; ?>><?php echo $value;?></option>
																    <?php } ?>
																</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-12">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('how_many_people') ?><span class="required">*</span></label>
																<?php $message = $this->lang->line('max_people');
																	  $table_capacity = sprintf($message,$restaurant_details['restaurant'][0]['table_minimum_capacity'],$restaurant_details['restaurant'][0]['table_booking_capacity'])
																 ?>
																<div class="max-event-people" style="font-size: 13px;padding-bottom: 6px;"><?php echo $table_capacity ?></div>
																<input type="number" name="no_of_people" id="no_of_people" class="form-control" autocomplete="off" value="<?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('no_of_people')))? $this->session->userdata('no_of_people'): ' ' ?>">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12">
						                                    <label class="control-label" for="event_first_name"><?php echo $this->lang->line('personal_details') ?><span class="required">*</span></label>
						                                </div>
						                                <div class="col-md-6">
						                                    <div class="form-group">
						                                       	<input type="text" name="first_name" id="event_first_name" class= "form-control" value="<?php echo ($this->session->userdata('userFirstname'))?($this->session->userdata('userFirstname')):"" ?>" maxlength="20" placeholder="<?php echo $this->lang->line('enter_first_name'); ?>">
						                                    </div>  
						                                </div>
						                                <div class="col-md-6">
						                                    <div class="form-group">
						                                        <input type="text" name="last_name" id="event_last_name" class= "form-control" value="<?php echo ($this->session->userdata('userLastname'))?($this->session->userdata('userLastname')):"" ?>" maxlength="20" placeholder="<?php echo $this->lang->line('enter_last_name'); ?>">
						                                    </div>
						                                </div>
						                                <div class="col-md-6">
						                                    <div class="form-group table_class">
						                                    	<input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
						                                        <input type="tel" name="phone_number_inp" id="phone_number_inp" class="form-control" value="<?php echo ($this->session->userdata('userPhone'))?($this->session->userdata('userPhone')):"" ?>" maxlength="12" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>" style="color:#495057">
						                                    <div id="event_phone_number_error"></div>
						                                  	</div>
						                               	</div>
						                                <div class="col-md-6">  
						                                    <div class="form-group">
						                                        <input type="email" name="email" id="event_email" class="form-control" value="<?php echo ($this->session->userdata('userEmail'))?($this->session->userdata('userEmail')):"" ?>" placeholder="<?php echo $this->lang->line('enter_email_address'); ?>" maxlength="50" style="color:#495057" >  
						                                        <div id="event_email_error"></div>
						                                    </div>
						                                </div>
													</div>
													<div class="row">
														<div class="col-lg-12">
															<div class="form-group">
																<label class="control-label"><?php echo $this->lang->line('additional_comment') ?></label>
																<div id="max_people"><?php echo $this->lang->line('max_allowed') ?></div>
																<textarea class="form-control" name="user_comment" id="user_comment" rows="5" style="resize:none;"><?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('user_request'))) ? ($this->session->userdata('user_request')) : ' '; ?></textarea>
															</div>
														</div>
													</div>
													<div class="table_booking_note alert alert-info active">
														<p><?php echo $this->lang->line('table_booking_note') ?></p>
													</div>
													<div class="table-btn">
						                                <button type="submit" name="submit_page" id="submit_page" value="Check Availability" class="btn load_more_btn"><?php echo $this->lang->line('check_avail') ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</section>
					<?php }else{ ?>
						<p><?php echo $this->lang->line('please');?>&nbsp;<a href="<?php echo base_url();?>home/login" style="font-weight: 900;text-decoration: underline;"><?php echo $this->lang->line('title_login') ?></a>&nbsp;<?php echo $this->lang->line('to_continue_text');?></p>
					<?php } ?>
				<!-- </div> -->
				</div>
			</div>
			<!-- Online Reservation : end-->
		</div>
	</div>
</section>
<div class="modal modal-main" id="myconfirmModal">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
				<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
	      <!-- Modal body -->
			<div class="modal-body">
	      		<form id="custom_items_form">
	      		<h5><?php echo $this->lang->line('menu_already_added') ?> <br> <?php echo $this->lang->line('want_to_add_new_item') ?></h5>
	      		<div class="popup-radio-btn-main">
		      		<div class="radio-btn-box">
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="hidden" name="con_entity_id" id="con_entity_id" value="">
			      				<input type="hidden" name="con_restaurant_id" id="con_restaurant_id" value="">
			      				<input type="hidden" name="con_item_id" id="con_item_id" value="">
			      				<input type="radio" class="radio_addon" checked name="addedToCart" id="addnewitem" value="addnewitem">
			      				<span><?php echo $this->lang->line('as_new_item') ?></span>
			      			</label>
			      		</div>
			      		<div class="radio-btn-list">
			      			<label>
			      				<input type="radio" class="radio_addon" name="addedToCart" id="increaseitem" value="increaseitem">
			      				<span><?php echo $this->lang->line('increase_quantity') ?></span>
			      			</label>
			      		</div>
			      	</div>
		        </div>
		      	<div class="popup-total-main">
		      		<div class="total-price">
	      				<button type="button" class="addtocart btn" id="addtocart" onclick="ConfirmCartAdd()"><?php echo $this->lang->line('add_to_cart') ?></button>
		      		</div>
		      	</div>
	      		</form>
			</div>
	    </div>
	</div>
</div>
<div class="modal modal-main" id="anotherRestModal">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
				<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
	      				<button type="button" class="cartrestaurant btn" id="cartrestaurant" onclick="ConfirmCartRestaurant()"><?php echo $this->lang->line('confirm') ?></button>
		      		</div>
		      	</div>
	      		</form>
			</div>
	    </div>
	</div>
</div>
<!-- The Modal -->
<!-- new changes for menu details on image click :: start -->
<div class="modal modal-main" id="myconfirmModalDetails">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
				<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
			      				<input type="radio" class="radio_addon" name="addedToCart1" id="addnewitem1" value="addnewitem">
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
	      				<button type="button" class="addtocart btn" id="addtocart1" onclick="ConfirmCartAddDetails()"><?php echo $this->lang->line('add_to_cart') ?></button>
		      		</div>
		      	</div>
	      		</form>
			</div>
	    </div>
	</div>
</div>
<div class="modal modal-main modal-variation product-detail" id="menuDetailModal"></div>
<div class="modal modal-main modal-variation product-detail" id="addonsMenuDetailModal"></div>
<!-- new changes for menu details on image click :: end -->
<!-- report restaurant model Start-->
<div class="modal modal-main" id="reportRestaurantModal">
	<div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('report_error') ?></h4>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
	      <!-- Modal body -->
			<div class="modal-body">
				<style type="text/css">
					#report_error_message p{
						margin-bottom: 0;
					}
					#report_error_message div{
						text-align: left;
					}
				</style>
				<div id="report_error_message"></div>
				<form id="report_res_form">
					<div class="filter-box">
						<h5><?php echo $this->lang->line('whats_wrong'); ?>?</h5>
						<div class="custom-control custom-checkbox filter-width m-2">
							<input type="checkbox" name="report_topic[]" class="custom-control-input" id="phone_number" value="phone_number">
							<label class="custom-control-label" for="phone_number"><?php echo $this->lang->line('phone_no') ?></label>
						</div>
						<div class="custom-control custom-checkbox filter-width m-2">
							<input type="checkbox" name="report_topic[]" class="custom-control-input" id="res_address" value="address">
							<label class="custom-control-label" for="res_address"><?php echo $this->lang->line('address') ?></label>
						</div>
						<div class="custom-control custom-checkbox filter-width m-2">
							<input type="checkbox" name="report_topic[]" class="custom-control-input" id="menu_check" value="menu">
							<label class="custom-control-label" for="menu_check"><?php echo $this->lang->line('menu') ?></label>
						</div>
						<div class="custom-control custom-checkbox filter-width m-2">
							<input type="checkbox" name="report_topic[]" class="custom-control-input" id="report_other" value="other">
							<label class="custom-control-label" for="report_other"><?php echo $this->lang->line('other') ?></label>
						</div>
						<div class="form-group">
			                <input type="email" name="email_address" id="email_address" class="form-control" placeholder="<?php echo $this->lang->line('email') ?> (<?php echo $this->lang->line('required') ?>)">
			            </div>
			            <div class="form-group">
			                <textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line('message') ?> (<?php echo $this->lang->line('required') ?>)" style="resize:none;" rows="5"></textarea>
			            </div>
			            <div class="action-button">
			                <button type="submit" name="submit_report" id="submit_report" value="Submit" class="btn btn-primary btn-block"><?php echo $this->lang->line('submit') ?></button>
			            </div>
					</div>
				</form>
			</div>
	    </div>
	</div>
</div>
<!-- report restaurant model End -->
<div class="modal modal-main" id="myModal"></div>
<?php if (!empty($restaurant_details['categories'])) {?>
	<!-- <script type="text/javascript" src="<?php //echo base_url(); ?>assets/front/js/tab-slider.js"></script> -->
	
<?php }?>
<!-- booking_availability -->
<div class="modal modal-main" id="table-booking-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_available')?>
			</h2>
			<input type="hidden" id="comment" name="comment" value="">
      		<?php if (!empty($this->session->userdata('UserID')) && ($this->session->userdata('is_user_login') == 1)) { ?>
      			<p><?php echo $this->lang->line('proceed_further') ?></p>
      			<button class="btn" data-dismiss="modal" data-toggle="modal" onclick="confirmTableBooking()"><?php echo $this->lang->line('request') ?></button>
      			<button class="btn" data-dismiss="modal" data-toggle="modal"><?php echo $this->lang->line('cancel') ?></button>
      		<?php } 
      		else { ?>
      			<p><?php echo $this->lang->line('please') ?> <a href="<?php echo base_url();?>home/login"><u><?php echo $this->lang->line('title_login') ?></u></a> <?php echo $this->lang->line('book_avail_text') ?></p>
      		<?php }?>
      		
      	</div>
      </div>
    </div>
  </div>
</div>
<div class="modal modal-main" id="booking-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_available')?>
			</h2>
			<input type="hidden" id="comment" name="comment" value="">
      		<?php if (!empty($this->session->userdata('UserID')) && ($this->session->userdata('is_user_login') == 1)) { ?>
      			<p><?php echo $this->lang->line('proceed_further') ?></p>
      			<button class="btn" data-dismiss="modal" data-toggle="modal" onclick="confirmBooking()"><?php echo $this->lang->line('request') ?></button>
      			<button class="btn" data-dismiss="modal" data-toggle="modal"><?php echo $this->lang->line('cancel') ?></button>
      		<?php } 
      		else { ?>
      			<p><?php echo $this->lang->line('please') ?> <a href="<?php echo base_url();?>home/login"><u><?php echo $this->lang->line('title_login') ?></u></a> <?php echo $this->lang->line('book_avail_text') ?></p>
      		<?php }?>
      		
      	</div>
      </div>
    </div>
  </div>
</div>
<!-- Booking Not Availability -->
<div class="modal modal-main" id="booking-not-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_not_available') ?></h2>
      		<p><?php echo $this->lang->line('no_bookings_avail') ?></p>
      		<button class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel') ?></button>
      	</div>
      </div>
    </div>
  </div>
</div>
<!-- Booking Confirmation -->
<div class="modal modal-main" id="table-booking-confirmation">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_confirmation') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-confirmation.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('table_booking_confirmed_text1') ?></h2>
      		<!-- <p><?php //echo $this->lang->line('booking_confirmed_text2') ?></p> -->
      		<a href="<?php echo base_url().'myprofile/view-my-tablebookings'; ?>" class="btn"><?php echo $this->lang->line('view_tablebookings') ?></a>
      	</div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-main" id="booking-confirmation">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_confirmation') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-confirmation.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_confirmed_text1') ?></h2>
      		<!-- <p><?php //echo $this->lang->line('booking_confirmed_text2') ?></p> -->
      		<a href="<?php echo base_url().'myprofile/view-my-bookings'; ?>" class="btn"><?php echo $this->lang->line('view_bookings') ?></a>
      	</div>
      </div>
    </div>
  </div>
</div>
<!-- Booking Capicity modal -->
<div class="modal modal-main" id="booking-not-available-capicity">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<img src="<?php echo base_url();?>assets/front/images/booking-availability.svg" alt="<?php echo $this->lang->line('booking_availability') ?>">
      		</div>
      		<h2><?php echo $this->lang->line('booking_not_available') ?></h2>
      		<p id="less" class="display-yes"><?php echo $this->lang->line('less_bookings_avail_capacity') ?> <span></span>.</p>
      		<p id="more" class="display-no"><?php echo $this->lang->line('no_bookings_avail_capacity') ?> <span></span>.</p>
      		 <span id="start_time_less" class="d-none"></span>
      		<button class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel') ?></button>
      	</div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js?v3"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/bootstrap-tagsinput.js"></script>
<!-- for review/rating and menu -->
<script type="text/javascript">
$('#booking-confirmation').on('hidden.bs.modal', function () {
	window.location.href = BASEURL+"myprofile/view-my-bookings";
});
$('#table-booking-confirmation').on('hidden.bs.modal', function () {
	window.location.href = BASEURL+"myprofile/view-my-tablebookings";
});
$(function () {
    var dateToday = new Date();
    dateToday.setMinutes( dateToday.getMinutes() + 15 );
    var maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 3, 0);
	maxDate = new Date(maxDate);
    $('#datetimepicker1').datetimepicker({ 
		minDate: dateToday,
		ignoreReadonly: true,
		useCurrent: false,
		defaultDate: dateToday,
		maxDate: maxDate,
   });
});
jQuery(document).ready(function() {
    $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>...", selectAll: true , placeholder : "<?php echo $this->lang->line('select_').' '.$this->lang->line('here'); ?>" });
$('.collapse-header').click(function(){
	if($(this).hasClass('active')){
		$(this).removeClass('active');
	}
	else{
		$(this).addClass('active');
	}
});
$('nav.cat-loop').find('a').on('click', function () {
	var collapse_c = $('.collapse-header a[href*='+$(this).attr('href').substring(1)+']');
	if(collapse_c.hasClass('collapsed')){
		$(collapse_c).click();
	}
	var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight()+40;
	var $el = $(this)
	, id = $el.attr('href');
	$('html, body').animate({
	scrollTop: $(id).offset().top - totalheight
	}, 500);
	return false;
});
});
//restricting to enter more than 4 digits in input type number
//$(document).on('input','#no_of_people',function(){
$('#no_of_people').keydown(function(event){
	this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
	if(event.keyCode == 190 || event.keyCode == 110) {
		return false;
	}
	$('input[type=number][max]:not([max=""])').on('input', function(ev) {
        var people_maxlength = $(this).attr('max').length;
        var value = $(this).val();
        if (value && value.length >= people_maxlength) {
          $(this).val(value.substr(0, people_maxlength));
        }
    });
});
</script>
<script type="text/javascript">
$(function() {
    // Check Radio-box
    $(".rating input:radio").filter('[value=3]').prop('checked', true);
    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });
    $('input:radio').change(
      function(){
        var userRating = this.value;
    }); 
    $('#menu_link').click(function(e) {
		$("#menu").delay(100).fadeIn(100);
 		$("#review").fadeOut(100);
 		$('#review_link').removeClass('active');
 		$("#aboutus").fadeOut(100);
		$('#aboutus_link').removeClass('active');
 		$("#online_reservation").fadeOut(100);
		$('#online_reservation_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#review_link').click(function(e) {
		$("#review").delay(100).fadeIn(100);
 		$("#menu").fadeOut(100);
 		$("#aboutus").fadeOut(100);
 		$("#online_reservation").fadeOut(100);
		$('#menu_link').removeClass('active');		
		$('#aboutus_link').removeClass('active');
		$('#online_reservation_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#aboutus_link').click(function(e) {
		$("#aboutus").delay(100).fadeIn(100);
 		$("#menu").fadeOut(100);
 		$("#review").fadeOut(100);
 		$("#online_reservation").fadeOut(100);
 		$('#review_link').removeClass('active');
		$('#menu_link').removeClass('active');
		$('#online_reservation_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#online_reservation_link').click(function(e) {
		$("#online_reservation").delay(100).fadeIn(100);
 		$("#aboutus").fadeOut(100);
 		$("#menu").fadeOut(100);
 		$("#review").fadeOut(100);
 		$('#review_link').removeClass('active');
		$('#menu_link').removeClass('active');
		$('#aboutus_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#event_link').click(function(e) {
		$("#event_banner").delay(100).fadeIn(100);
		$("#event_section").delay(100).fadeIn(100);
		$("#table_banner").fadeOut(100);
 		$("#table_section").fadeOut(100);
		$('#table_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#table_link').click(function(e) {
		$("#table_banner").delay(100).fadeIn(100);
		$("#table_section").delay(100).fadeIn(100);
		$("#event_banner").fadeOut(100);
 		$("#event_section").fadeOut(100);
		$('#event_link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	//Coupon slider
	var rtl = (SELECTED_LANG == 'ar')?true:false;
	$('.best-offers-slider').owlCarousel({
	  loop: false,
	  margin: 20,
	  nav: true,
	  autoplay: true,
	  rtl:rtl,
	  autoplayTimeout:3500,
	  navSpeed:1300,
	  autoplaySpeed:1300,
	  autoplayHoverPause: true,
	  navContainer: '#customNav2',
	  responsive: {
	    0: {
	      items: 2,
	      margin: 10
	    },
	    600: {
	      items: 3,
	      margin: 20
	    },
	   
	    1550:{
	       items: 4
	    }
	  }
	});
});
//check restaurant : closed/offline/deactive
function checkResStat() {
	var restaurant_id = '<?php echo $cart_restaurant; ?>';
	var is_scheduling_allowed = <?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1') ? 1 : 0; ?>;
	var menu_ids = <?php echo json_encode($menu_ids); ?>;
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
<script src="<?php echo base_url();?>assets/front/js/tiny-slider.js"/>
<script type="text/javascript">
$(document).on('ready', function() {
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
	if(count != '0'){
		$('body').addClass("cart_bottom");
		//$("#your_cart").addClass("cart_bottom");
	} else {
		$('body').addClass("cart_bottom");
		//$("#your_cart").removeClass("cart_bottom");
	}
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});	
});
</script>
<?php $this->load->view('footer');?>
<script type="text/javascript">
/*$(document).on('ready', function() {
	$(".footer-area").addClass("cart_footer");
});*/
</script>
    <script>
    var doc = document,
      slideList = doc.querySelectorAll('.slider-checkbox-main > div'),
      toggleHandle = doc.querySelector('.nav-toggle-handle'),
      divider = window.innerHeight / 2,
      scrollTimer,
      resizeTimer; 
     if (window.addEventListener) {
     window.addEventListener('scroll', function () {
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function () {
        [].forEach.call(slideList, function (el) {
          var rect = el.getBoundingClientRect();         
        });
      }, 100);
    });
    window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        divider = window.innerHeight / 2;
      }, 100);
    });    
    }
    var mobile = 'false',
      isTestPage = false,
      isDemoPage = true,
      classIn = 'jello',
      classOut = 'rollOut',
      speed = 400,
      doc = document,
      win = window,
      ww = win.innerWidth || doc.documentElement.clientWidth || doc.body.clientWidth,
      fw = getFW(ww),
      initFns = {},
      sliders = new Object(),
      edgepadding = 50,
      gutter = 10;
    function getFW (width) {
    var sm = 400, md = 900, lg = 1400;
    return width < sm ? 150 : width >= sm && width < md ? 200 : width >= md && width < lg ? 300 : 400;
    }
    window.addEventListener('resize', function() { fw = getFW(ww); });
    </script>
    <script>
    // <script type="module">
    // import { tns } from '../src/tiny-slider.js';
    var options = {
    'autoWidth-non-loop': {
      autoWidth: true,
      loop: false,
      mouseDrag: true,
      nav: false,
    }
    
    };
    for (var i in options) {
    var item = options[i];
    item.container = '#' + i;
    item.swipeAngle = false;
    if (!item.speed) { item.speed = speed; }
    if (doc.querySelector(item.container)) {
      sliders[i] = tns(options[i]);
    // test responsive pages
    } else if (i.indexOf('responsive') >= 0) {
      if (isTestPage && initFns[i]) { initFns[i](); }
    }
}
//New code for scroll item :: Start
var sections = $('.sliderMenutoggle')
  , nav = $('nav.cat-loop')
  , nav_height = nav.outerHeight();
var lastScrollTop = 0;
var lastCat = "";
// var $dots = $('.owl-carousel');
$(window).on('scroll', function () {
  var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight();
  var cur_pos = $(this).scrollTop()+totalheight;
  var curScroll = $(this).scrollTop();
  //sections.each(function() {
  $('.collapse-header a').each(function() {
    var top = $(this).offset().top,
        bottom = top + $(this).outerHeight();
    var curCat = $(this).attr('href').substring(1);
    if (cur_pos >= top && cur_pos <= bottom)
    {
      nav.find('a').removeClass('active');
      //sections.removeClass('active');      
      $('.collapse-header a').removeClass('active');      
      $(this).addClass('active');
      nav.find('a[href="'+$(this).attr('href')+'"]').addClass('active');
    if(curCat != lastCat && lastCat !=""){
      if (curScroll > lastScrollTop){
          //scroll down
          sliders['autoWidth-non-loop'].goTo('next');
      } else {
          //scroll up          
          sliders['autoWidth-non-loop'].goTo('prev');
      }
      lastScrollTop = curScroll;
    }
    lastCat = curCat;      
    }
  });
});
/*$("#details_content").find('a').on('click', function () {
	var clickid= this.id;
	clickid.toLowerCase();
	if(clickid.indexOf('addtocart')==-1){

		var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight();
  		var $el = $(this)
    	, id = $el.attr('href');
  		$('html, body').animate({
    		scrollTop: $(id).offset().top - totalheight
  		}, 500);
  	}
  
  return false;
});*/
//when search box is empty
$('#search_dish').keyup(function(){
	$('#Search_btn').prop('disabled', false);
	$('#search_dish_reset_btn').prop('disabled', false);
	if(event.keyCode == 13){
		$("#Search_btn").click();
    }
	if($(this).val()==''){
		$('#Search_btn').prop('disabled', true);
		$('#search_dish_reset_btn').prop('disabled', true);
	}
});
$('#search_dish_reset_btn').on('click', function() {
	$('#search_dish').val("");
	var restaurant_id = '<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>';
	if(restaurant_id){
		searchMenuDishes(restaurant_id);
		$('#Search_btn').prop('disabled', true);
		$('#search_dish_reset_btn').prop('disabled', true);
	}
});
//New code for scroll item :: end
$('input.QtyNumberval').on('input', function() {		
    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
});
$('#report_restaurant').click(function(){
	$('#reportRestaurantModal').modal('show');
});
$('#report_res_form').validate({
	rules: { 
        email_address: {
        	required: true
        },
        message :{
        	required: true
        }
     }
});
$('#report_res_form').on("submit", function(event){
	var report_topic_checkbox = new Array();
	$("input[name='report_topic[]']:checked").each(function() {
   		report_topic_checkbox.push($(this).val());
	});

	var email_address = $("input[name='email_address']").val();
	var message = $("textarea#message").val();
	if($('#report_res_form').valid()){
		event.preventDefault();
		jQuery.ajax({
		    type : "POST",
		    dataType : "json",
		    url : BASEURL+ 'restaurant/restaurant_error_report',
		    data : {"report_topic":report_topic_checkbox,"email_address":email_address,"message":message},
		    beforeSend: function(){
		    	$('#report_error_message').html('');
		        $('#quotes-main-loader').show();
		    },
		    success: function(response) {
		    	if(response.error == 1){
		    		$("#reportRestaurantModal .modal-content").scrollTop(0);
		    		$('#quotes-main-loader').hide();
		    		var error_message = $('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
		    		$('#report_error_message').html(error_message);
		    	}
		    	if(response.success == 1){
		    		$("#reportRestaurantModal .modal-content").scrollTop(0);
		    		/*$('#report_res_form').find("input,textarea,select").val('').end().find("input[type=checkbox], input[type=radio]").prop("checked", "").end();*/
		    		document.getElementById("report_res_form").reset();
		    		$('#quotes-main-loader').hide();
		    		var success_message = $('<div class="alert alert-success" role="alert"><p>'+response.message+'</p></div>');
		    		$('#report_error_message').html(success_message);
		    		setTimeout(function(){ $('#reportRestaurantModal').modal('hide'); }, 5000);
		    	}
		    	if(response.success == 0){
		    		$("#reportRestaurantModal .modal-content").scrollTop(0);
		    		/*$('#report_res_form').find("input,textarea,select").val('').end().find("input[type=checkbox], input[type=radio]").prop("checked", "").end();*/
		    		document.getElementById("report_res_form").reset();
		    		$('#quotes-main-loader').hide();
		    		var fail_message = $('<div class="alert alert-danger" role="alert"><p>'+response.message+'</p></div>');
		    		$('#report_error_message').html(fail_message);
		    		setTimeout(function(){ $('#reportRestaurantModal').modal('hide'); }, 5000);
		    	}
		    },
		    error: function(XMLHttpRequest, textStatus, errorThrown) {
		        alert(errorThrown);
		    }
		});
	}
});
$('#reportRestaurantModal').on('hidden.bs.modal', function () {
	//$(this).find("input,textarea,select").val('').end().find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
	document.getElementById("report_res_form").reset();
	$('#report_res_form').validate().resetForm();
  	$('#report_error_message').html('');
});
</script>
<script type="text/javascript">
	//New code for multiple select :: start
	$(".filter_food").on("click",function(){
		var idArr = [];
		$(".filter_food_all").attr("checked", false);
		$('.filter_food:checked').each(function() {
	        idArr.push($(this).val());
	    });
	    menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',idArr,'yes','no')
	});
	$(".filter_food_all").on("click",function(){
		$(".filter_food").attr("checked", false);
		var idArr = $(".filter_food_all").val();
	    menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',idArr)
	});
	$(".filter_availibility").on("click",function(){
		var idArr = [];
		$(".filter_availibility_all").attr("checked", false);
		$('.filter_availibility:checked').each(function() {
	        idArr.push($(this).val());
	    });
	    menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',idArr,'no','yes')
	});
	$(".filter_availibility_all").on("click",function(){
		$(".filter_availibility").attr("checked", false);
		var idArr = $(".filter_availibility_all").val();
	    menuFilter('<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>',idArr)
	});
	//New code for multiple select :: end
	function restaurantTimingsList(){
		//$('.timings-list-grp').slideToggle();
		if($(".timings-list").hasClass("toggle_open")){
			$(".timings-list").addClass("toggle_close");
			$(".timings-list").removeClass("toggle_open");
			$(".timings-list-grp").slideUp("slow");
		} else {
			$(".timings-list").addClass("toggle_open");
			$(".timings-list").removeClass("toggle_close");
			$(".timings-list-grp").stop().slideDown("slow");
		}
	}
</script>
<script type="text/javascript">
window.addEventListener('click', function(e){   
  if (!document.getElementById('res_time_li').contains(e.target)){
    // Clicked outside the box
    if($(".timings-list").hasClass("toggle_open")){
	    $(".timings-list").addClass("toggle_close");
		$(".timings-list").removeClass("toggle_open");
		$(".timings-list-grp").slideUp("slow");
	}
  } 
});
</script>
<script type="text/javascript">
//intl-tel-input plugin
var onedit_iso = '';
<?php if($this->session->userdata('userPhone_code')) {
    $onedit_iso = $this->common_model->getIsobyPhnCode($this->session->userdata('userPhone_code')); ?>
    onedit_iso = '<?php echo $onedit_iso; ?>'; //saved in session
<?php }
$iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>

var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
var initial_preferred_iso = (onedit_iso)?onedit_iso:default_iso;
//phone number login form :: start
// Initialize the intl-tel-input plugin
<?php if($restaurant_details['restaurant'][0]['enable_table_booking'] == 1 && $this->session->userdata('UserID')){ ?>
const phoneInputField = document.querySelector("#phone_number_inp");
const phoneInput = window.intlTelInput(phoneInputField, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    autoPlaceholder:"polite",
    formatOnDisplay:false,
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js',
});
$(document).on('input','#phone_number_inp',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number_inp').val(phoneNumber);
    }
});
$(document).on('focusout','#phone_number_inp',function(){
    event.preventDefault();
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number_inp').val(phoneNumber);
    }
});
phoneInputField.addEventListener("close:countrydropdown",function() {
    var phoneNumber = phoneInput.getNumber();
    if (phoneInput.isValidNumber()) {
        var countryData = phoneInput.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#phone_number_inp').val(phoneNumber);
    }
});
<?php } ?>
function addEndTimeSlot(start_end_flag) {
	var restaurant_id = $('#table_restaurant_id').val();
	var event_date = $('#datepicker').val();
	if(start_end_flag=='is_start'){
		var start_time = $('#starttime').val();
		var end_time = '<?php echo $restaurant_details['restaurant'][0]['timings']['close'] ?>';
	} else {
		var start_time = '<?php echo $restaurant_details['restaurant'][0]['timings']['open'] ?>';
		var end_time = $('#endtime').val();
	}
	var selected_start_time = $('#starttime').val();
	var selected_end_time = $('#endtime').val();
	$.ajax({
		type: "POST",
		dataType: "html",
		url: BASEURL+'restaurant/getTimeSlot',
		data: {'start_time':start_time,'end_time':end_time, 'restaurant_id':restaurant_id ,'event_date' : event_date, 'is_date_changed' :1, 'start_end_flag':start_end_flag, 'selected_start_time': selected_start_time, 'selected_end_time':selected_end_time },
		beforeSend: function(){
			$('#quotes-main-loader').show();
		},
		success: function(response) { 
			$('#quotes-main-loader').hide();
			var arr = JSON.parse(response);
			//time_slot
			if(start_end_flag=='is_start'){
				$('#endtime').empty().append(arr.end_time_html);
				$('#endtime')[0].sumo.reload();
			} else {
				$('#starttime').empty().append(arr.end_time_html);
				$('#starttime')[0].sumo.reload();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {           
			alert(errorThrown);
		}
	});
}
function addSlot() {
	var restaurant_id = $('#table_restaurant_id').val();
	var event_date = $('#datepicker').val();
	$.ajax({
		type: "POST",
		dataType: "html",
		url: BASEURL+'restaurant/getTimeSlot',
		data: {'start_time':$('#starttime').val(),'end_time':'<?php echo $restaurant_details['restaurant'][0]['timings']['close'] ?>', 'restaurant_id':restaurant_id,'event_date' : event_date },
		beforeSend: function(){
			$('#quotes-main-loader').show();
		},
		success: function(response) { 
			$('#quotes-main-loader').hide();
			var arr = JSON.parse(response);
			$('#starttime').empty().append(arr.start_time_html);
			$('#starttime')[0].sumo.reload();

			$('#endtime').empty().append(arr.end_time_html);
			$('#endtime')[0].sumo.reload();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {           
			alert(errorThrown);
		}
	});
}
function getPackageInfo(value,restaurant_id)
{
	if(value!='' && value!=undefined)
	{
		jQuery.ajax({
	        type : "POST",
	        dataType : "json",
	        url : BASEURL+'restaurant/show_restaurantpackage',
	        data : {'content_id':value,'restaurant_id':restaurant_id},
	        beforeSend: function(){
	            $('#quotes-main-loader').show();
	            $('#package_detaildiv').html('');
	        },
	        success: function(response) {
	        	$('#package_detaildiv').html(response.package_html);
	        	$('#package_section').show();
	        	$('#quotes-main-loader').hide();
	        },
	        error: function(XMLHttpRequest, textStatus, errorThrown) {
	            //alert(errorThrown);
	        }
	    });
	}
	else
	{
		$('#package_section').hide();
	}
}
function restaurantShare(){
	$('.social-icon-grp').slideToggle();
}
$(document).click(function(event) {
  if($(event.target).closest(".share-icons").length === 0 && $('.social-icon-grp').is(":visible")) {
  	$('.social-icon-grp').slideUp();
  }
});
</script>