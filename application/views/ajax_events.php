<?php $enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
if (!empty($restaurants)) {
	foreach ($restaurants as $key => $value) { ?>
		<div class="col-sm-12 col-md-6 col-lg-3">
			<div class="popular-rest-box">
				<a href="<?php echo base_url().'restaurant/event-booking-detail/'.$value['restaurant_slug'];?>">
					<div class="popular-rest-img">
						<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='')?(image_url.$value['image']):(default_img);?>" alt="<?php echo $value['name']; ?>">
						<?php if ($show_restaurant_reviews) { 
							$rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
						<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
						<?php } ?>
						<div class="openclose-btn">
							<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
							<!--<?php //echo $value['timings']['closing']; ?>-->
						</div>
					</div>
					<div class="popular-rest-content">
						<h3><?php echo $value['name']; ?></h3>
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
<?php } 
else if (!empty($table_restaurants)) {
	foreach ($table_restaurants as $key => $value) { ?>
		<div class="col-sm-12 col-md-6 col-lg-3">
			<div class="popular-rest-box">
				<a href="<?php echo base_url().'restaurant/event-booking-detail/'.$value['restaurant_slug'];?>">
					<div class="popular-rest-img">
						<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='')?(image_url.$value['image']):(default_img);?>" alt="<?php echo $value['name']; ?>">
						<?php if ($show_restaurant_reviews) { ?>
							<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
						<?php } ?>
						<div class="openclose-btn">
							<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
							<!--<?php //echo $value['timings']['closing']; ?>-->
						</div>
					</div>
					<div class="popular-rest-content">
						<h3><?php echo $value['name']; ?></h3>
						<div class="popular-rest-text">
							<p class="address-icon"><?php echo $value['address']; ?> </p>
						</div>
					</div>
				</a>
			</div>
		</div>
	<?php } ?>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div class="pagination" id="#pagination"><?php echo $table_PaginationLinks; ?></div>
	</div>
<?php } 
else { ?>
	<div class="empty_block">
		<figure>
			<img src="<?php echo no_res_found; ?>">
		</figure>
		<p class="no-found"><?php echo $this->lang->line('no_res_found') ?></p>
	</div>
<?php }?>