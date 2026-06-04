<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header');
$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
?>
<section class="inner-banner event-booking-banner">
	<div class="container" id="event_banner" style="display: block;">
		<div class="inner-pages-banner">
			<h1><?php echo $this->lang->line('book_table_venue') ?></h1>
			<form id="event_search_form" class="inner-pages-form">
				<div class="form-group search-restaurant">
					<?php $err_msg = $this->lang->line('add_valid_restaurant');
					$oktext = $this->lang->line('ok'); ?>
                  	<div class="event_search_restaurant">
						<input type="text" name="searchEvent" id="searchEvent" placeholder="<?php echo $this->lang->line("search_res"); ?>" value="">
						<a href="javascript:;" class="clear_icon" id="for_search_event" onclick="clearField('searchEvent','event_booking',this.id,'<?php echo $err_msg; ?>');"></a>
                    </div>
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" id="searchEventsBtn" class="btn" onclick="searchEvents('restaurant','<?php echo $err_msg; ?>','<?php echo $oktext; ?>')">
				</div>
			</form>
		</div>
	</div>
	<div class="container" id="table_banner" style="display: none;">
		<div class="inner-pages-banner">
			<h1><?php echo $this->lang->line('book_table_venue') ?></h1>
			<form id="event_search_form" class="inner-pages-form">
				<div class="form-group search-restaurant">
					<?php $err_msg = $this->lang->line('add_valid_restaurant');
					$oktext = $this->lang->line('ok'); ?>
					<input type="text" name="searchTable" id="searchTable" placeholder="<?php echo $this->lang->line("search_res"); ?>" value="">
					<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" id="searchTablesBtn" class="btn" onclick="searchTables('restaurant','<?php echo $err_msg; ?>','<?php echo $oktext; ?>')">
				</div>
			</form>
		</div>
	</div>
</section>
<section class="inner-pages-section order-food-section">
	<?php /* ?><div class="container">
		<div class="event_table">
			<div class="menu_review">
				<a href="#" class="active" id="event_link"><button class="btn res-event"><?php echo $this->lang->line('book_event'); ?></button></a>
				<?php //if ($show_restaurant_reviews) { ?>
				<a href="#" id="table_link"><button class="btn res-table"><?php echo $this->lang->line('book_table'); ?></button></a>
				<?php //} ?>
			</div>
		</div>
	</div><?php */ ?>
	<div class="container" id="event_section" style="display: block;">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('book_restaurant') ?></h2>
				</div>
			</div>
		</div>
		<div class="row rest-box-row" id="sort_events">
			<?php if (!empty($restaurants)) {
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
										<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"><?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?></div>
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
			<?php } else { ?>
				<div class="empty_block">
					<figure>
						<img src="<?php echo no_res_found; ?>">
					</figure>
					<p class="no-found"><?php echo $this->lang->line('no_res_found'); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="container" id="table_section" style="display: none;">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('book_table') ?></h2>
				</div>
			</div>
		</div>
		<div class="row rest-box-row" id="sort_tables">
			<?php if (!empty($table_restaurants)) {
				foreach ($table_restaurants as $key => $value) { ?>
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="popular-rest-box">
							<a href="<?php echo base_url().'restaurant/table-booking-detail/'.$value['restaurant_slug'];?>">
								<div class="popular-rest-img">
									<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='')?(image_url.$value['image']):(default_img);?>" alt="<?php echo $value['name']; ?>">
									<?php if ($show_restaurant_reviews) { ?>
									<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
									<?php } ?>
									<div class="openclose-btn">
										<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"><?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?></div>
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
			<?php } else { ?>
				<div class="empty_block">
					<figure>
						<img src="<?php echo no_res_found; ?>">
					</figure>
					<p class="no-found"><?php echo $this->lang->line('no_res_found'); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
<script type="text/javascript">
// pagination function
function getData(page=0, noRecordDisplay=''){
	var searchEvent = $('#searchEvent').val();
	var searchTable = $('#searchTable').val();
	var page = page ? page : 0;
	<?php //var curtab = jQuery('.menu_review').find('a.active').attr('id');
	//if(curtab == 'event_link'){ ?>
		$.ajax({
			url: "<?php echo base_url().'restaurant/ajax_events'; ?>/"+page,
			data: {'searchEvent':searchEvent,'page':page},
			type: "POST",
			success: function(result){
				$('#sort_events').html(result);
				$('html, body').animate({
			        scrollTop: $("#sort_events").offset().top
			    }, 800);
			}
		});
	<?php  /*}else if(curtab == 'table_link'){
		$.ajax({
			url: "<?php echo base_url().'restaurant/ajax_table_booking'; ?>/"+page,
			data: {'searchTable':searchTable,'page':page},
			type: "POST",
			success: function(result){
				$('#sort_tables').html(result);
				$('html, body').animate({
			        scrollTop: $("#sort_tables").offset().top
			    }, 800);
			}
		});
	}*/ ?>
}
//search event 
$('#searchEvent').keyup(function(){
	if($('#searchEvent').val()!=''){
		$('#for_search_event').show();
	} else {
		$('#for_search_event').hide();
	}
	if(event.keyCode == 13){
		$("#searchEventsBtn").click();
    }
});
//search table 
$('#searchTable').keyup(function(){
	if(event.keyCode == 13){
		$("#searchTablesBtn").click();
    }
});
$(function() {
    // Check Radio-box
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
	<?php if($this->session->userdata('redirect_from_table_detail') && $this->session->userdata('redirect_from_table_detail') == 1){  ?>
			$('#table_link').trigger('click');
	<?php 
			$this->session->unset_userdata('redirect_from_table_detail');
		} 
	?>
});
$(document).on('ready', function() {
	if($('#searchEvent').val()!=''){
		$('#for_search_event').show();
	} else {
		$('#for_search_event').hide();
	}
});
</script>
<?php $this->load->view('footer'); ?>