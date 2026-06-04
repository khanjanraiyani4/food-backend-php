<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); 
$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
?>
<style type="text/css">
	.borderClass{
		border-color: #17161a;
		border-width:3px;
		border-style: solid;
	}
</style>
<!-- <link rel="stylesheet" href="<?php //echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" /> -->
<!-- <script type="text/javascript" src="<?php //echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script> -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<section class="home-banner webp">
	<div class="container">
		<div class="your-doorstep">
			<!-- <h1><?php //echo $this->lang->line('always_at_door'); ?></h1> -->
			<div class="your-doorstep-logo">
				<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/logo.svg" alt=""></a>
			</div>
			<p><?php echo $this->lang->line('order_fav_rest'); ?></p>
			<form id="home_search_form" class="search-form">
				<div class="form-group">
					<select id="order_mode" class="order_mode" name="order_mode">
						<option value="Delivery"><?php echo $this->lang->line('delivery_word') ?></option>
						<option value="PickUp"><?php echo $this->lang->line('pickup_word') ?></option>
						<?php /* ?><option value="Both"><?php echo $this->lang->line('both') ?></option><?php */ ?>
					</select>
					<div class="home_auto_location">
						<a href="javascript:;" class="auto_location" onclick="getLocation('home_page');"></a>
						<input type="text" name="address" id="address" onFocus="geolocate('home_page')" placeholder = "<?php echo $this->lang->line('enter_address'); ?>" value="">
						<a href="javascript:;" class="clear_icon" id="for_address" onclick="clearField('address','home_page',this.id);"></a>
						<input type="hidden" name="latitude" id="latitude" value="">
						<input type="hidden" name="longitude" id="longitude" value="">
					</div>
					<?php $err_msg = $this->lang->line('add_valid_location');
					$oktext = $this->lang->line('ok'); ?>
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="fillInAddress('home_page','<?php echo $err_msg; ?>','<?php echo $oktext; ?>')" id="fillInAddressBtn">
				</div>
			</form>
		</div>
	</div>
</section>
<?php if (!empty($food_type) && !empty($restaurants)) { ?>
	<section class="quick-searches" id="foodtype_quicksearch">
		<div class="container">
			<div class="heading-title">
				<h2><?php echo $this->lang->line('quick_search'); ?></h2>
			</div>
			<div class="slider-new-add">
				<div class="slider-arrow">   
					<div id="customNav" class="arrow"></div>
				</div>
				<div class="quick-searches-slider owl-carousel">
					<?php foreach ($food_type as $ftkey => $ftvalue) { ?>
						<div class="quick-searches-box" id="foodtype_<?php echo $ftkey; ?>" onclick="getRestaurantsOnFilter('apply','quicksearch_foodtype','',<?php echo $ftkey; ?>)">
							<?php $food_type_image = (file_exists(FCPATH.'uploads/'.$ftvalue->food_type_image) && $ftvalue->food_type_image != '') ? image_url.$ftvalue->food_type_image : default_img;  ?>
							<input type="hidden" name="quicksearch_foodtype" id="quicksearch_foodtype" value="<?php echo $ftvalue->entity_id; ?>">
							<img src="<?php echo $food_type_image; ?>" alt="<?php echo $ftvalue->name ?>" title="<?php echo $ftvalue->name ?>">
							<h5><?php echo $ftvalue->name ?></h5>
						</div>					
					<?php } ?>				
				</div>
			</div>
		</div>
	</section>
<?php } ?>
<section class="best-offers" id="coupon_section" style="display: none;">
<?php if (!empty($coupons) && !empty($restaurants)) { ?>	
		<div class="container">
			<div class="heading-title">
				<h2><?php echo $this->lang->line('latest_coupons'); ?></h2>
			</div>
			<div class="slider-new-add">
				<div class="slider-arrow">
					<div id="customNav2" class="arrow"></div>
				</div>
				<div class="best-offers-slider owl-carousel">
					<?php foreach ($coupons as $key => $value) {
						$redirect_flag = (count($value->restaurant_ids) == 1) ? '1':'0';
						$rest_image = (file_exists(FCPATH.'uploads/'.$value->image) && $value->image!='') ? image_url.$value->image : default_img;
						if($redirect_flag == '1') { ?>
							<div class="best-offers-box">
								<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value->restaurant_slug; ?>"><img src="<?php echo $rest_image ?>" alt="coupon"></a>
							</div>
						<?php } else { ?>
							<div class="best-offers-box">
								<img src="<?php echo $rest_image ?>" alt="coupon">
							</div>
						<?php }
					} ?>
				</div>
			</div>
		</div>	
<?php } ?>
</section>
<?php if (!empty($restaurants)) { ?>
	<section class="popular-restaurants">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="heading-title">
						<h2><?php echo $this->lang->line('nearby_restaurants') ?></h2>
					</div>
				</div>
				<?php //search restaurants :: start ?>
				<div class="col-lg-4 search_col">
					<div class="filter_search-home">
						<div class="filt-inp">
							<input class="input-tags form-control" type="text" name="resdishes" placeholder="<?php echo $this->lang->line('search_res') ?>" id="resdishes">
							<a href="javascript:;" class="clear_icon" id="for_res_search" onclick="getRestaurantsOnFilter('clear','res_search');"></a>
						</div>
						<input type="button" name="SearchResHome" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="getRestaurantsOnFilter('apply')" id="search_res_home">
					</div>
				</div>
				<?php //search restaurants :: end ?>
			</div>
			<?php //restaurant sort/filter section :: start ?>
			<div class="home_filter_sidebar" style="width: 100%; max-width: 100%">
				<div class="row">
					<?php //sort by distance-rating-freeDeliveryCoupon-availability :: start ?>
					<div class="col-sm-12 col-md-6 col-lg-3 dis_col">
						<div id="accordion">
							<div class="accordian-card">
								<div class="filter-title-main" id="headingOne">
									<h5 data-toggle="collapse" title="<?php echo $this->lang->line('sort') ?>" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" id="sort_heading_txt" class="collapsed tooltip"  >
										<span><?php echo $this->lang->line('sort') ?></span>
									</h5>
								</div>
								<div id="collapseOne" class="filter_sidebar-open collapse" aria-labelledby="headingOne" >
									<div class="filter_sidebar_in">
										<div class="radio-btn-box">
											<div class="radio-btn-list">
												<label>
													<input type="radio" name="filter_by" class="" value="rating">
													<span><?php echo $this->lang->line('rating') ?></span>
												</label>
											</div>
											<div class="radio-btn-list" id="distance_sort">
												<label>
													<input type="radio" name="filter_by" class="" value="distance">
													<span><?php echo $this->lang->line('distance') ?></span>
												</label>
											</div>
										</div>
										<div class="filter-box" id="distance_filter">
											<h6><?php echo $this->lang->line('by_distance') ?></h6>
											<div class="distance-slider">
												<div id="slider-range"></div>
											    <div class="distance-value value01"><span id="slider-range-value1"></span></div>
											    <div class="distance-value value02"><span id="slider-range-value2"></span></div>
											    <input type="hidden" name="minimum_range" id="minimum_range" class="form-control" value="<?php echo $minimum_range; ?>" />
											    <input type="hidden" name="maximum_range" id="maximum_range" class="form-control" value="<?php echo $maximum_range; ?>" />
											</div>
										</div>
										<div class="free_delivery_cpn radio-btn-list">
											<label>
												<input type="checkbox" name="offers_free_delivery" id="offers_free_delivery" value="1" >
												<span>
													<h6><?php echo $this->lang->line('offers_free_delivery') ?></h6>
												</span>
											</label>
										</div>
										<div class="sort_availability">
											<h6><?php echo $this->lang->line('sort_availability') ?></h6>
											<div class="availability_filter_div">
												<div class="custom-control custom-checkbox filter-width">
													<input type="checkbox" name="availability_filter" class="custom-control-input availability_filter" id="filter_breakfast" value="Breakfast">
													<label class="custom-control-label" for="filter_breakfast"><?php echo $this->lang->line('breakfast') ?></label>
												</div>
												<div class="custom-control custom-checkbox filter-width">
													<input type="checkbox" name="availability_filter" class="custom-control-input availability_filter" id="filter_lunch" value="Lunch">
													<label class="custom-control-label" for="filter_lunch"><?php echo $this->lang->line('lunch') ?></label>
												</div>
												<div class="custom-control custom-checkbox filter-width">
													<input type="checkbox" name="availability_filter" class="custom-control-input availability_filter" id="filter_dinner" value="Dinner">
													<label class="custom-control-label" for="filter_dinner"><?php echo $this->lang->line('dinner') ?></label>
												</div>
											</div>
										</div>
										<div class="filter_btns">
											<button class="btn" onclick="getRestaurantsOnFilter('apply','','<?php echo addslashes($this->lang->line('sorted_by')); ?>')" ><?php echo addslashes($this->lang->line('apply')) ?></button>
											<button class="btn clear_btnn" onclick="getRestaurantsOnFilter('clear','distance_rating','<?php echo addslashes($this->lang->line('sort')); ?>')" ><?php echo $this->lang->line('reset') ?></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php //sort by distance-rating-freeDeliveryCoupon-availability :: end ?>
					<?php //filter by category :: start ?>
					<div class="col-sm-12 col-md-6 col-lg-3 sort_col">
						<?php if(!empty($categories)){ ?>
							<div class="filter-box">
								<div class="filter-checkbox">
									<select name="category_id[]" multiple="" class="categoryid_sumo category_id" id="category_id" placeholder="<?php echo $this->lang->line('filter').' '.$this->lang->line('by_category'); ?>">
									<?php if(!empty($categories)){
										foreach ($categories as $categorykey => $categoryvalue) { ?>
											<option value="<?php echo $categoryvalue->entity_id ?>"><?php echo ucfirst($categoryvalue->name); ?></option>
										<?php } 
									} ?>
									</select>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php //filter by category :: end ?>
					<?php //filter by food_type :: start ?>
					<div class="col-sm-12 col-md-6 col-lg-3 sort_col">
						<?php if(!empty($food_type)){ ?>
							<div class="filter-box">
								<div class="filter-checkbox">
									<select name="food_type[]" multiple="" class="sumo food_type" id="food_type" placeholder="<?php echo $this->lang->line('filter').' '.$this->lang->line('by_food_type'); ?>">
									<?php if(!empty($food_type)){
										foreach ($food_type as $key => $value) { ?>
											<option value="<?php echo $value->entity_id ?>"><?php echo ucfirst($value->name); ?></option>
										<?php } 
									} ?>
									</select>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php //filter by food_type :: end ?>					
				</div>
			</div>
			<?php //restaurant sort/filter section :: end ?>
			<div id="popular-restaurants">
				<div class="row rest-box-row"> 
					<?php if (!empty($restaurants)) {	
						foreach ($restaurants as $key => $value) { ?>
							<div class="col-sm-12 col-md-6 col-lg-3"> 
								<div class="popular-rest-box">
									<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
										<div class="popular-rest-img">
											<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img; ?>
											<img src="<?php echo $rest_image; ?>" alt="<?php echo $value['name']; ?>">
											<div class="openclose-btn">
												<?php $closed = ($value['timings']['closing'] == "Closed") ? 'closed' : ''; ?>
												<div class="openclose <?php echo $closed; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
													<!-- <?php //echo $value['timings']['closing']; ?> -->
											</div>
											<?php if (!empty($value['restaurant_coupons'])) { ?>
												<div class="res-coupons-slider variable slider">
													<?php foreach ($value['restaurant_coupons'] as $cpnkey => $cpnvalue) { ?>
														<div><h5><?php echo $cpnvalue->name ?><?php echo (count($value['restaurant_coupons']) > 1) ? ',  &nbsp;': ''; ?></h5></div>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
										<div class="popular-rest-content">
											<h3><?php echo $value['name']; ?></h3>
											<?php if ($show_restaurant_reviews) { ?>
												<?php echo ($value['ratings'] > 0)?'':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
											<?php } ?>
											<div class="popular-rest-text">
												<p class="address-icon"><?php echo $value['address']; ?> </p>
											</div>
										</div>
									</a>
								</div>
							</div>
						<?php } ?>
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
						</div>
					<?php } else { ?>	
						<div class="empty_block">
							<figure>
								<img src="<?php echo no_res_found; ?>">
							</figure>
							<p class="no-found"><?php echo $this->lang->line('no_such_res_found') ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
<?php }else{ ?>
	<div class="empty_block">
		<figure>
			<img src="<?php echo no_res_found; ?>">
		</figure>
		<p class="no-found"><?php echo $this->lang->line('no_such_res_found') ?></p>
	</div>
<?php } ?>
<section class="restaurant-app">
	<div class="container">	
		<div class="restaurant-app-content">
			<div class="row">
				<div class="col-md-6 col-sm-12">
					<div class="restaurant-app-img wow pulse">
						<picture>							
							<source type="image/jpg" srcset="<?php echo base_url();?>assets/front/images/restaurant-app.png" />
							<img src="<?php echo base_url();?>assets/front/images/restaurant-app.png" alt="Restaurant app">
						</picture>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="restaurant-app-text">
						<div class="heading-title-02">
							<h4><?php echo $this->lang->line('welcome_to') ?> <br><span><?php echo $this->lang->line('site_title'); ?> <?php echo $this->lang->line('res_app') ?></span></h4>
						</div>	
						<p><?php echo $this->lang->line('home_text1') ?></p>
						<?php 
						//get System Option Data
						$this->db->select('OptionValue');
						$playstore_url = $this->db->get_where('system_option',array('OptionSlug'=>'playstore_url'))->first_row();
						$this->db->select('OptionValue');
						$app_store_url = $this->db->get_where('system_option',array('OptionSlug'=>'app_store_url'))->first_row();
						?>
						<div class="app-download">
							<a href="<?php echo ($playstore_url->OptionValue)?$playstore_url->OptionValue:'#'; ?>"><img src="<?php echo base_url();?>assets/front/images/google-play.png" alt="Google play"></a>
							<a href="<?php echo ($app_store_url->OptionValue)?$app_store_url->OptionValue:'#'; ?>"><img src="<?php echo base_url();?>assets/front/images/app-store.png" alt="App store"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php /* ?><section class="driver-app">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-lg-4">
				<div class="driver-app-content">	
					<div class="heading-title-02">
						<h4><?php echo $this->lang->line('download') ?> <span><?php echo $this->lang->line('driver_app') ?></span></h4>
					</div>							
					<p><?php echo $this->lang->line('site_title'); ?> <?php echo $this->lang->line('home_text2') ?></p>
					<?php //get System Option Data
					$this->db->select('OptionValue');
					$driver_playstore_url = $this->db->get_where('system_option',array('OptionSlug'=>'driver_playstore_url'))->first_row();
					$this->db->select('OptionValue');
					$driver_app_store_url = $this->db->get_where('system_option',array('OptionSlug'=>'driver_app_store_url'))->first_row();
					?>
					<div class="app-download">
						<a href="<?php echo ($driver_playstore_url->OptionValue)?$driver_playstore_url->OptionValue:'#'; ?>"><img src="<?php echo base_url();?>assets/front/images/google-play.png" alt="Google play"></a>
						<a href="<?php echo ($driver_app_store_url->OptionValue)?$driver_app_store_url->OptionValue:'#'; ?>"><img src="<?php echo base_url();?>assets/front/images/app-store.png" alt="App store"></a>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-8">
				<div class="driver-app-img wow pulse">
					<picture>						
						<source type="image/png" srcset="<?php echo base_url();?>assets/front/images/driver-app.png" />
						<img src="<?php echo base_url();?>assets/front/images/driver-app.png" alt="Restaurant app">
					</picture>
					<img src="">
				</div>
			</div>
		</div>
	</div>
</section><?php */ ?>
<?php if (!empty($restaurants)) { ?>
	<script type="text/javascript" src='<?php echo base_url();?>assets/front/js/range-slider.js'></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script>
var selected_food_type = [];
var selected_category_id = [];
$(document).on('ready', function() { 
	initAutocomplete('address');
	$('#distance_sort').hide();
  	$('#distance_filter').hide();
	// auto detect location if even searched once.
	<?php if (!empty($restaurants)) { ?>
		if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
			getLocation('home_page','from_home_page');
		}
		else
		{
			getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'home_page','from_home_page');
		}
	<?php } ?>
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});
	var rtl = (SELECTED_LANG == 'ar')?true:false;
	$('.quick-searches-slider').owlCarousel({
      loop: true,
      nav: true,
      rtl:rtl,
      autoplay: true,
      autoplayTimeout:3500,
      navSpeed:1300,
      touchDrag:true,
      slideBy:2,
      autoplaySpeed:1300,
      autoplayHoverPause: true,
      navContainer: '#customNav',
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 2,
        },
        600: {
          items: 3,
        },
        1000: {
          items: 5,
        },
         1300: {
          items: 6,
        },
        1550: {
          items: 7
        }
      }
    });
    $('.best-offers-slider').owlCarousel({
      loop: true,
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
          items: 1,
        },
        480: {
          items: 2,
        },
        600: {
          items: 3,
	        },
       
        1550:{
           items: 4
        }
      }
    });
    $('.owl-prev').attr('title',"<?php echo $this->lang->line('backward') ?>");
    $('.owl-prev').attr('alt',"<?php echo $this->lang->line('backward') ?>");
    $('.owl-next').attr('title',"<?php echo $this->lang->line('forward') ?>");
    $('.owl-next').attr('alt',"<?php echo $this->lang->line('forward') ?>");
});
$(document).on('ready', function() {
	//for coupon slider for in restaurant
	var rtl = (SELECTED_LANG == 'ar')?true:false;
	$(".variable").slick({
		dots: false,
		infinite: true,
		autoplay: true,
		variableWidth: true,
		arrow: false,
		autoplaySpeed: 0,
		speed: 8000,
		pauseOnHover: false,
		cssEase: 'linear',
		rtl: rtl,
	});
	if($('#address').val()!=''){
		$('#for_address').show();
	} else {
		$('#for_address').hide();
	}
	if($('#resdishes').val()!=''){
		$('#for_res_search').show();
	} else {
		$('#for_res_search').hide();
	}
	//food type sumo :: start
    $('.sumo').SumoSelect({search: true, selectAll: true, locale: ["<?php echo $this->lang->line('apply'); ?>", "<?php echo $this->lang->line('reset'); ?>", "<?php echo $this->lang->line('select_').' '.$this->lang->line('all');?>"],csvDispCount: 0, okCancelInMulti:true, triggerChangeCombined : true, forceCustomRendering: true, isClickAwayOk: false, searchText: "<?php echo $this->lang->line('by_food_type'); ?>" });
    
    $("html").on('click', '.sumo_food_type .select-all', function(){
    	$('.sumo_food_type .select-all').removeClass('partial');
    	
    	var myObj = $(this).closest('.SumoSelect.open').children()[0];
    	if ($('.sumo_food_type .select-all').hasClass("selected")) {
        	$('.sumo_food_type .select-all').parents(".SumoSelect").find("select>option").prop("selected", true);
            $(myObj)[0].sumo.selectAll();
            $('.sumo_food_type .select-all').parent().find("ul.options>li").addClass("selected");
        } else {
        	$('.sumo_food_type .select-all').parents(".SumoSelect").find("select>option").prop("selected", false);
            $(myObj)[0].sumo.unSelectAll();
            $('select.food_type')[0].sumo.unSelectAll();
            $('.sumo_food_type .select-all').parent().find("ul.options>li").removeClass("selected");
        }
    });
    $("html").on('click', '.sumo_food_type .btnCancel', function(){
        var obj = [];
	    $('.food_type option:selected').each(function(i_count) {
	        obj.push($(this).index());
	    });

	    for (var i = 0; i < obj.length; i++) {
	        $('.food_type')[0].sumo.unSelectItem(obj[i]);
	    }
		selected_food_type = [];
		$('.quick-searches-box').removeClass('selected');
		$('.quick-searches-box').removeClass('borderClass');
		getRestaurantsOnFilter('apply');
    });
    $("html").on('click', '.sumo_food_type .btnOk', function(){
		$('.quick-searches-box').removeClass('selected');
		$('.quick-searches-box').removeClass('borderClass');
		selected_food_type = [];
		$('.food_type option:selected').each(function(j) {
			selected_food_type.push($(this).index());
			var selected_food_type_val = $(this).val();
			$('#foodtype_'+selected_food_type_val).addClass('selected');
			$('#foodtype_'+selected_food_type_val).addClass('borderClass');
		});
		getRestaurantsOnFilter('apply');
    });
    //food type sumo :: end    
    //category sumo :: start
    $('.categoryid_sumo').SumoSelect({search: true, selectAll: true, locale: ["<?php echo $this->lang->line('apply'); ?>", "<?php echo $this->lang->line('reset'); ?>", "<?php echo $this->lang->line('select_').' '.$this->lang->line('all');?>"],csvDispCount: 0, okCancelInMulti:true, triggerChangeCombined : true, forceCustomRendering: true, isClickAwayOk: false, searchText: "<?php echo $this->lang->line('by_category'); ?>" });
    
    $("html").on('click', '.sumo_category_id .select-all', function(){
    	$('.sumo_category_id .select-all').removeClass('partial');
    	
    	var myObj_categoryid = $(this).closest('.SumoSelect.open').children()[0];
    	if ($('.sumo_category_id .select-all').hasClass("selected")) {
        	$('.sumo_category_id .select-all').parents(".SumoSelect").find("select>option").prop("selected", true);
            $(myObj_categoryid)[0].sumo.selectAll();
            $('.sumo_category_id .select-all').parent().find("ul.options>li").addClass("selected");
        } else {
        	$('.sumo_category_id .select-all').parents(".SumoSelect").find("select>option").prop("selected", false);
            $(myObj_categoryid)[0].sumo.unSelectAll();
            $('select.category_id')[0].sumo.unSelectAll();
            $('.sumo_category_id .select-all').parent().find("ul.options>li").removeClass("selected");
        }
    });
    $("html").on('click', '.sumo_category_id .btnCancel', function(){
        var obj_cat_id = [];
	    $('.category_id option:selected').each(function(categoryid_count) {
	        obj_cat_id.push($(this).index());
	    });

	    for (var a = 0; a < obj_cat_id.length; a++) {
	        $('.category_id')[0].sumo.unSelectItem(obj_cat_id[a]);
	    }
		selected_category_id = [];
		getRestaurantsOnFilter('apply');
    });
    $("html").on('click', '.sumo_category_id .btnOk', function(){
    	selected_category_id = [];
		$('.category_id option:selected').each(function(b) {
			selected_category_id.push($(this).index());
		});
		getRestaurantsOnFilter('apply');
    });
    //category sumo :: end
});
// pagination function
function getData(page=0, noRecordDisplay=''){

	var rtl = (SELECTED_LANG == 'ar')?true:false;
	var foodtype_quicksearch = [];
    $('.quick-searches-box.selected').each(function(){
      foodtype_quicksearch.push($(this).find("input[name=quicksearch_foodtype]").val());
    });
    var listed_foodtype = [];
    $('.quick-searches-box').each(function(){
      listed_foodtype.push($(this).find("input[name=quicksearch_foodtype]").val());
    });
	var resdishes = $('#resdishes').val();
    var order_mode = $('#order_mode').val();
    var latitude = $('#latitude').val();
    var longitude = $('#longitude').val();
    var minimum_range = $('#minimum_range').val();
    var maximum_range = $('#maximum_range').val();
    var filter_by = $("input[name='filter_by']:checked").val();
    var offers_free_delivery = $("input[name='offers_free_delivery']:checked").val();
    offers_free_delivery = (offers_free_delivery == '1') ? 1 : 0;
    var availability_filter = [];
    $("input[name='availability_filter']:checkbox:checked").each(function(i){
      availability_filter.push($(this).val());
    });
    var category_id = [];
    $('#category_id option:selected').each(function(i) {
        category_id.push($(this).val());
    });
    var food_type = [];
    $('#food_type option:selected').each(function(i) {
        food_type.push($(this).val());
    });    
	var page = page ? page : 0;
	$.ajax({
		url: "<?php echo base_url().'home/getRestaurantsOnFilter'; ?>/"+page,
		data : {'latitude':latitude,'longitude':longitude,'resdishes':$.trim(resdishes),'minimum_range':minimum_range,'maximum_range':maximum_range,'filter_by': filter_by,'order_mode':order_mode,'page':page, 'food_type': food_type.join(),'foodtype_quicksearch': foodtype_quicksearch.join(),'category_id': category_id.join(),'listed_foodtype':listed_foodtype.join(),'offers_free_delivery':offers_free_delivery,'availability_filter':availability_filter.join()},
		dataType :"json",
		type: "POST",
		success: function(result){
			var rtl = (SELECTED_LANG == 'ar')?true:false;
			$('#popular-restaurants').html(result.popular_restaurants);
			$('#foodtype_quicksearch').html(result.quick_searches);
			$('#coupon_section').html(result.coupon_section_html);
			$('.food_type').empty().append(result.foodtype_dropdown);
        	$('select.food_type')[0].sumo.reload();
			if(foodtype_quicksearch.length != 0 || food_type.length != 0) {
				if(food_type.length != 0) {
					$.each(food_type, function(ftindex, ftvalue) {
						$('select.food_type')[0].sumo.selectItem(ftvalue);
					});
				} else {
					$.each(foodtype_quicksearch, function(ftindex, ftvalue) {
						$('select.food_type')[0].sumo.selectItem(ftvalue);
					});
				}
			}
			if(result.quick_searches != '') {
				$('.quick-searches-slider').owlCarousel({
					loop: true,
					nav: true,
					rtl:rtl,
					autoplay: true,
					autoplayTimeout:3500,
					navSpeed:1300,
					touchDrag:true,
					slideBy:2,
					autoplaySpeed:1300,
					autoplayHoverPause: true,
					navContainer: '#customNav',
					responsive: {
						0: {
							items: 1,
						},
						480: {
							items: 2,
						},
						600: {
							items: 3,
						},
						1000: {
							items: 5,
						},
						1300: {
							items: 6,
						},
						1550: {
							items: 7
						}
					}
				});
			}
			if(result.coupon_section_html != '') {
				$('.best-offers-slider').owlCarousel({
					loop: true,
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
							items: 1,
						},
						480: {
							items: 2,
						},
						600: {
							items: 3,
						},
						1550:{
							items: 4
						}
					}
				});
			}
			if($('.res-coupons-slider').length){
				$(".variable").slick({
					dots: false,
					infinite: true,
					autoplay: true,
					variableWidth: true,
					arrow: false,
					rtl:rtl,
					autoplaySpeed: 0,
					speed: 8000,
					pauseOnHover: false,
					cssEase: 'linear',
					rtl: rtl,					
				});
			}
			$('html, body').animate({
				scrollTop: $(".popular-restaurants").offset().top
			}, 2000);
		}
	});
}
$('#address').keyup(function(){
	if($('#address').val()!=''){
		$('#for_address').show();
	} else {
		$('#for_address').hide();
	}
	if(event.keyCode == 13){
		$("#fillInAddressBtn").click();
    }
});
$('#resdishes').keyup(function() {
	if($('#resdishes').val()!=''){
		$('#for_res_search').show();
	} else {
		$('#for_res_search').hide();
	}
});
$(document).mouseup(function(e) 
{
    var container = $("#accordion");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        $('#sort_heading_txt').addClass('collapsed');
        $('#collapseOne').removeClass('show');
    }
	var sumo_container = $(".filter-checkbox");
	// if the target of the click isn't the container nor a descendant of the container
	if (!sumo_container.is(e.target) && sumo_container.has(e.target).length === 0) 
	{
		var count = 1;
		$('select.food_type').on('sumo:closed', function(sumo) {
			if(count == 1){
				var obj = [];
				$('.food_type option').each(function (){
					if(this.selected) {
						obj.push($(this).index());
					}
				});
				/*$('.food_type option:selected').each(function(i) {
					obj.push($(this).index());
				});
				for (var j = 0; j < obj.length; j++) {
					if(!inArray(obj[j], selected_food_type)){
						$('.food_type')[0].sumo.unSelectItem(obj[j]);
					}
				}
				for (var k = 0; k < selected_food_type.length; k++) {
					$('.food_type')[0].sumo.selectItem(selected_food_type[k]);
				}*/
				count++;
			}
		});		
		$('select.category_id').on('sumo:closed', function(sumo) {
			if(count == 1){
				var obj_category_id = [];
				$('.category_id option').each(function (){
					if(this.selected) {
						obj_category_id.push($(this).index());
					}
				});
				count++;
			}
		});
	}
});
function inArray(needle, haystack) {
	var length = haystack.length;
	for(var l = 0; l < length; l++) {
		if(haystack[l] == needle) return true;
	}
	return false;
}
</script>
<?php $this->load->view('footer'); ?>