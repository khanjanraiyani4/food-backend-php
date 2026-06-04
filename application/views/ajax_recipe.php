<?php if (!empty($recipies)) {
	foreach ($recipies as $key => $value) { ?>
		<div class="col-sm-12 col-md-6 col-lg-3">
			<div class="popular-rest-box">
				<a href="<?php echo base_url().'recipe/recipe-detail/'.$value['slug'];?>">
					<div class="popular-rest-img">
						<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='' )?image_url.$value['image']:default_img; ?>" alt="<?php echo $value['name']; ?>" title="<?php echo $value['name']; ?>">
					</div>
					<div class="popular-rest-content type-food-option">
						<div class="detail-list <?php echo ($value['is_veg'] == 1)?'veg':'non-veg'; ?>"><h3><?php echo $value['name']; ?></h3></div>
					</div>
				</a>
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
	<p class="no-found"><?php echo $this->lang->line('no_recipe_found') ?></p>
	</div>
<?php }?>