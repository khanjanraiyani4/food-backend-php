<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); ?>
<section class="inner-banner recipe-banner">
	<div class="container">
		<div class="inner-pages-banner">
			<h1><?php echo $this->lang->line('recipe_text1') ?></h1>
			<form id="recipe_search_form" class="inner-pages-form">
				<div class="form-group search-restaurant">
					<?php $err_msg = $this->lang->line('add_valid_message');
					$oktext = $this->lang->line('ok'); ?>
					<input type="text" name="recipe" id="recipe" required placeholder="<?php echo $this->lang->line('search_recipe') ?>" value="">
					<input type="button" name="Search" id="searchRecipesBtn" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="searchRecipes('recipe','<?php echo $err_msg; ?>','<?php echo $oktext; ?>')">
				</div>
			</form>
		</div>
	</div>
</section>
<section class="inner-pages-section order-food-section" id="order-food-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('popular_recipe') ?></h2>
				</div>
			</div>
		</div>
		<div class="row rest-box-row" id="sort_recipies">
			<?php if (!empty($recipies)) {
				foreach ($recipies as $key => $value) { ?>
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="popular-rest-box">
							<a href="<?php echo base_url().'recipe/recipe-detail/'.$value['slug'];?>">
								<div class="popular-rest-img">
									
									<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img;  ?>
									<img src="<?php echo $rest_image ; ?>" alt="<?php echo $value['name']; ?>" title="<?php echo $value['name']; ?>">
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
		</div>
	</div>
</section>
<script type="text/javascript">
	// pagination function
	function getData(page=0, noRecordDisplay=''){
		var recipe = $('#recipe').val();
		var page = page ? page : 0;
		$.ajax({
			url: "<?php echo base_url().'recipe/ajax_recipies'; ?>/"+page,
			data: {'recipe':recipe,'page':page},
			type: "POST",
			success: function(result){
				$('#sort_recipies').html(result);
				$('html, body').animate({
			        scrollTop: $("#order-food-section").offset().top
			    }, 800);
			}
		});
	}

//search recipe 
$('#recipe').keyup(function(){
	if(event.keyCode == 13){
		$("#searchRecipesBtn").click();
    }
});
</script>
<?php $this->load->view('footer'); ?>