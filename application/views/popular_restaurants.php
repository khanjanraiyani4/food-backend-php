<?php
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

<div class="row rest-box-row">
	<?php if (!empty($nearbyRestaurants)) {
		foreach ($nearbyRestaurants as $key => $value) { ?>
			<div class="col-sm-12 col-md-6 col-lg-4">
				<div class="popular-rest-box">
					<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
						<div class="popular-rest-img">
							<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img; ?>
							<img src="<?php echo $rest_image ;?>" alt="<?php echo $value['name']; ?>">
							<div class="openclose-btn">
								<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
								<!-- <?php //echo $value['timings']['closing']; ?> -->
							</div>
							<?php if(isset($value['distance'])) { ?>
								<div class="display_distance">
									<strong><?php echo round($value['distance'],2); ?> <?php echo $distance_inVal; ?></strong>
								</div>
							<?php } ?>
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
							<?php if ($show_restaurant_reviews) { 
								$rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
								<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
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
<script type="text/javascript">
	$(document).on('ready', function() {
	//for coupon slider for in restaurant
	var rtl = (SELECTED_LANG == 'ar')?true:false;
	$(".variable").slick({
		dots: false,
		infinite: false,
		autoplay: true,
		variableWidth: true,
		arrow: false,
		autoplaySpeed: 0,
		speed: 8000,
		pauseOnHover: false,
		cssEase: 'linear',
		rtl: rtl,
	});
});
</script>