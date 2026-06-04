<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>
<!-- <link rel="stylesheet" href="<?php //echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<script type="text/javascript" src="<?php //echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script> -->
<style type="text/css">
	.filter_rating{
    font-size: 19px;
    font-weight: 500;
    color: #161212;
	}
	@media only screen and (max-width: 1440px){
	.filter_rating{
    font-size: 16px;
    font-weight: 500;
    color: #161212;
	}	
	}
</style>
<?php //$minimum_range = 0; $maximum_range = 50000; 

$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;

$distance_inarr = $this->db->get_where('system_option',array('OptionSlug'=>'distance_in'))->first_row();
$distance_inVal = $this->lang->line('in_km');
if($distance_inarr && !empty($distance_inarr))
{
    if($distance_inarr->OptionValue==0){
        $distance_inVal = $this->lang->line('in_mile');
    }
}
?>
<script type="text/javascript">
	var distance_inVal = '<?php echo $distance_inVal; ?>';
</script>
<section class="inner-pages-section order-food-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('select_fav_res') ?></h2>
				</div>
			</div>
			<?php //filter section :: start ?>
			<div class="col-md-5 col-lg-3">
				<div class="food-filter">
					<div class="filter-box-main">
						<div id="accordion">
							<div class="accordian-card">
								<div class="filter-title-main" id="headingOne">
									<h5 data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"><?php echo $this->lang->line('sort') ?></h5>
								</div>
								<div id="collapseOne" class="collapse" aria-labelledby="headingOne" >
									<?php if ($show_restaurant_reviews) { ?>
									<div class="filter-box">
										<div class="filter-checkbox">
											<div class="radio-btn-box">
												<div class="radio-btn-list">
													<label>
														<input type="radio" name="filter_by" class="" value="rating" onchange="getFavouriteResturants()">
														<span><?php echo $this->lang->line('rating') ?></span>
													</label>
												</div>
												<div class="radio-btn-list" id="distance_sort">
													<label>
														<input type="radio" name="filter_by" class="" value="distance" onchange="getFavouriteResturants()">
														<span><?php echo $this->lang->line('distance') ?></span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
							<div class="accordian-card">
								<div class="filter-title-main" id="headingTwo">
									<h5 data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><?php echo $this->lang->line('filter') ?></h5>
								</div>
								<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" >
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
									<?php if(!empty($food_type)){ ?>
									<div class="filter-box by_food_type">
										<h6><?php echo $this->lang->line('by_food_type') ?></h6>
										<div class="filter-checkbox">
											<?php for($fdt=0;$fdt<count($food_type);$fdt++)	{ ?>
			    							<div class="checkbox-box">
												<label>
													<input type="checkbox" class="food_typecls" name="food_type[]" id="food_veg_<?=$food_type[$fdt]->entity_id?>" value="<?=$food_type[$fdt]->entity_id?>" onchange="getFavouriteResturants()">
													<span><?php /* ?><i class="iicon-icon-15 <?php if($food_type[$fdt]->is_veg == '1') {echo 'veg';} else { echo 'non-veg';} ?>"></i><?php */ ?><?php echo ucfirst($food_type[$fdt]->name); ?></span>
												</label>
											</div>
											<?php } ?>        						
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php //filter section :: end ?>
			<?php //restaurant listing :: start ?>
			<div class="col-md-7 col-lg-9">
				<div class="row restaurant-box-row" id="order_from_restaurants">
					<?php if (!empty($restaurants)) {
						foreach ($restaurants as $key => $value) { ?>
							<div class="col-lg-6">
								<div class="restaurant-box">
									<div class="popular-rest-box">
										<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
											<div class="popular-rest-img">
												<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img;  ?>
												<img src="<?php echo $rest_image ;?>" alt="<?php echo $value['name']; ?>">
												<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
												<?php if(isset($value['distance'])) { ?>
													<div class="display_distance">
														<strong><?php echo round($value['distance'],2); ?> <?php echo $distance_inVal; ?></strong>
													</div>
												<?php } ?>
											</div>
										</a>
										<div class="popular-rest-content">
											<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>"><h3><?php echo $value['name']; ?></h3></a>
											<?php if ($show_restaurant_reviews) { 
												$rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
											<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
											<?php } ?>
											<div class="popular-rest-text">
												<p class="address-icon"><?php echo $value['address']; ?> </p>
												<div class="order-btn">
													<?php  if($value['timings']['closing'] != "Closed") {
													?>
														<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>" class="btn"><?php echo $this->lang->line('order') ?></a>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
						</div>
					<?php } 
					else { ?>
						<div class="empty_block">
							<figure>
								<img src="<?php echo no_res_found; ?>">
							</figure>
							<p class="no-found"><?php echo $this->lang->line('no_such_res_found') ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php //restaurant listing :: end ?>
		</div>
	</div>
</section>

<script type="text/javascript" src='<?php echo base_url();?>assets/front/js/range-slider.js?v1'></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key;?>&libraries=places"></script>
<script type="text/javascript">
$(document).on('ready', function() { 
	initAutocomplete('address');
	$('#distance_sort').hide();
  	$('#distance_filter').hide();
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
	// auto detect location if even searched once.
	if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
		getLocation('order_food');
	}
	else
	{
		getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'order_food');
	}
});

// pagination function
function getData(page=0, noRecordDisplay=''){
	var food_veg = ($('#food_veg').is(":checked"))?1:0;
	var food_non_veg = ($('#food_non_veg').is(":checked"))?1:0;
	var resdishes = $('#resdishes').val();
	var order_mode = $('#order_mode').val();
	var latitude = $('#latitude').val();
	var longitude = $('#longitude').val();
	var minimum_range = $('#minimum_range').val();
	var maximum_range = $('#maximum_range').val();
	var filter_by = $("input[name='filter_by']:checked").val();
	var page = page ? page : 0;
	var food_type = [];
    $('.food_typecls:checked').each(function(i, e) {
    	food_type.push($(this).val());
  	});
	$.ajax({
		url: "<?php echo base_url().'restaurant/ajax_restaurants'; ?>/"+page,
		data : {'latitude':latitude,'longitude':longitude,'resdishes':resdishes,'page':page,'minimum_range':minimum_range,'maximum_range':maximum_range,'food_veg':food_veg,'food_non_veg':food_non_veg,'food_type': food_type.join(),'order_mode':order_mode,'filter_by':filter_by},
		type: "POST",
		success: function(result){
			$('#order_from_restaurants').html(result);
			/*$('html, body').animate({
		        scrollTop: $("#order_from_restaurants").offset().top
		    }, 800);*/
		}
	});
}
$('#resdishes').keyup(function(){
	if($('#resdishes').val()!=''){
		$('#for_res_search').show();
	} else {
		$('#for_res_search').hide();
	}
	if(event.keyCode == 13){
		$("#fillInAddressBtn").click();
    }
});
$('#address').keyup(function(){
	if($('#address').val()!=''){
		$('#for_address').show();
	} else {
		$('#for_address').hide();
	}
});
</script>
<?php $this->load->view('footer'); ?>
