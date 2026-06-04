<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); 
$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
<?php if(empty($restaurant_details['restaurant'])) {
    redirect(base_url().'restaurant/event-booking');
} ?>
<?php $rest_background_image = (file_exists(FCPATH.'uploads/'.$restaurant_details['restaurant'][0]['background_image']) && $restaurant_details['restaurant'][0]['background_image']!='') ? image_url.$restaurant_details['restaurant'][0]['background_image'] : ''; ?>
<section class="inner-banner <?php echo ($rest_background_image != '')?'':'booking-detail-banner' ?>" <?php echo ($rest_background_image != '')?'style="background-image: url('.$rest_background_image.');"':''; ?> >
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
								<?php  $rest_image = (file_exists(FCPATH.'uploads/'.$restaurant_details['restaurant'][0]['image']) && $restaurant_details['restaurant'][0]['image']!='') ? image_url.$restaurant_details['restaurant'][0]['image'] : default_icon_img; ?>
								<img src="<?php echo $rest_image ;?>" alt="<?php echo $value['name']; ?>">
							</div>
						</div>
						<div class="rest-detail-content">
							<h2><?php echo $restaurant_details['restaurant'][0]['name']; ?> </h2>
							<p><i class="iicon-icon-20"></i><?php echo $restaurant_details['restaurant'][0]['address']; ?></p>
							<ul>
								<?php if ($show_restaurant_reviews) { 
									$rating_txt = ($restaurant_details['restaurant'][0]['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
								<li><i class="iicon-icon-05"></i><?php echo ($restaurant_details['restaurant'][0]['ratings'] > 0)?$restaurant_details['restaurant'][0]['ratings'].' ('.$restaurant_details['restaurant'][0]['restaurant_reviews_count'].' '.strtolower($rating_txt).')':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?></li>
								<?php } ?>
								<li class="rtl-num-cod position-relative res_time_li" id="res_time_li"><i class="iicon-icon-18"></i><?php echo (!empty($restaurant_details['restaurant'][0]['timings']['open']) && !empty($restaurant_details['restaurant'][0]['timings']['close']))?ucfirst($restaurant_details['restaurant'][0]['timings']['current_day']).' : '.$this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['open']) . '-' . $this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['close']) : $this->lang->line("close_txt"); ?><i class="iicon-icon-25 time_arrow" onclick="restaurantTimingsList()"></i>
									<?php if(!empty($restaurant_details['restaurant'][0]['week_timings'])){ ?>
										<div class="timings-list-grp bg-white">
											<ul class="timings-list toggle_close">
												<?php foreach ($restaurant_details['restaurant'][0]['week_timings'] as $week_key => $week_value) { ?>
													<li ><span><?php echo ucfirst($week_key); ?></span><?php echo (!empty($week_value['open']) && !empty($week_value['close']))?': '.$this->common_model->timeFormat($week_value['open']).' - '.$this->common_model->timeFormat($week_value['close']) : ': '.$this->lang->line('close_txt'); ?></li>
												<?php } ?>
											</ul>
										</div>
									<?php } ?>
								</li>
								<li class="rtl-num-cod"><i class="iicon-icon-19"></i><a href="tel:<?php echo $restaurant_details['restaurant'][0]['phone_number']; ?>"><?php echo $restaurant_details['restaurant'][0]['phone_number']; ?></a></li>
								<li><img src="<?php echo base_url();?>assets/front/images/map_direction.png" id="map_direction"><a href="http://maps.google.com/?q=<?php echo $restaurant_details['restaurant'][0]['latitude']; ?>,<?php echo  $restaurant_details['restaurant'][0]['longitude']; ?>" target="_blank"><?php echo $this->lang->line('map')." ".$this->lang->line('directions'); ?></a></li>
								
							</ul>
							<?php $closed = ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'closed':''; ?>
							<a href="#" class="openclose <?php echo $closed; ?>"><?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('select_package') ?></h2>
				</div>
			</div>
		</div>
		<div class="row restaurant-detail-row">	
			<div class="col-sm-12 col-md-5 col-lg-8">					
				<div class="detail-list-box-main">
					<!-- <div class="detail-list-title">
						<h3>Gold Packages</h3>
					</div> -->
					<div class="detail-list-box">
						<?php if (!empty($restaurant_details['packages'])) { 
							foreach ($restaurant_details['packages'] as $key => $value) { ?>
								<div class="detail-list">
									<div class="detail-list-img">
										<div class="list-img">	
											<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='')?image_url.$value['image']:default_icon_img; ?>">
										</div>
									</div>
									<div class="detail-list-content"> 
										<div class="detail-list-text">
											<h4><?php echo $value['name']; ?></h4>
											<p><?php echo $value['detail']; ?></p>
											<strong><?php echo currency_symboldisplay($value['price'],$currency_symbol); ?></strong>
										</div>
										<?php 
											$package_class = '';
											$add_lang = 'add';
											if((!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1) && !empty($this->session->userdata('package_id')) && $value['entity_id'] == $this->session->userdata('package_id')) {
												$package_class = 'inpackage';
												$add_lang = 'added';
											}
										?>
										<div class="add-btn">
											<div class="addpackage btn <?php echo $package_class; ?>" id="addpackage-<?php echo $value['content_id']; ?>" onclick="AddPackage('<?php echo $value['content_id']; ?>')"><?php echo $this->lang->line($add_lang) ?></div>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } 
						else { ?>
							<!-- <div class="detail-list-title">
								<h3 class="no-results"><?php //echo $this->lang->line('no_results_found') ?></h3>
							</div> -->
							<div class="empty_block">
								<figure>
									<img src="<?php echo no_res_found; ?>">
								</figure>
								<p class="no-found"><?php echo $this->lang->line('no_results_found') ?></p>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-5 col-lg-4">
				<div class="your-booking-main">
					<div class="your-booking-title">
						<h3><i class="iicon-icon-27"></i><?php echo $this->lang->line('your_booking') ?></h3>
					</div>
					<form id="check_event_availability" name="check_event_availability" method="post" class="form-horizontal float-form">
						<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_content_id']; ?>">
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
						<input type="hidden" name="name" id="name" value="<?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?>">
						<div class="booking-option-main">
							<div class="booking-option how-many-people">
								<div class="booking-option-cont">
									<div class="option-img">
										<img src="<?php echo base_url();?>assets/front/images/avatar-man.png">
									</div>
									<div class="booking-option-text">
										<span><?php echo $this->lang->line('how_many_people') ?></span>
										<?php $message = $this->lang->line('event_max_people');
											  $table_capacity = sprintf($message,$restaurant_details['restaurant'][0]['event_minimum_capacity'],$restaurant_details['restaurant'][0]['capacity'])
										 ?>
										<div class="max-event-people" style="font-size: 13px;padding-bottom: 6px;"><?php echo $table_capacity ?></div>
										<span id="peepid"><strong> <?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('no_of_people')))? $this->session->userdata('no_of_people'): ((intval($restaurant_details['restaurant'][0]['event_minimum_capacity'])>0) ? $restaurant_details['restaurant'][0]['event_minimum_capacity'] : 1); ?> <?php echo $this->lang->line('people') ?></strong></span>
										<!--<span id="peepid"><strong>1 <?php //echo $this->lang->line('people') ?></strong></span>-->
									</div>
								</div>
								<input type="hidden" name="min_people" id="min_people" value="<?php echo $restaurant_details['restaurant'][0]['event_minimum_capacity']; ?>">
								<input type="hidden" name="max_people" id="max_people" value="<?php echo $restaurant_details['restaurant'][0]['capacity']; ?>">
								<div class="add-cart-item">
									<div class="number">
										<span class="capacity_minus variant"><i class="iicon-icon-22"></i></span>
										<input type="number" min="1" max="9999" name="no_of_people" id="no_of_people" value="<?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('no_of_people')))? $this->session->userdata('no_of_people') : ((intval($restaurant_details['restaurant'][0]['event_minimum_capacity'])>0) ? $restaurant_details['restaurant'][0]['event_minimum_capacity'] : 1); ?>" maxlength="4">
										<!--<input type="text" name="no_of_people" id="no_of_people" value="1" onkeyup="getPeople(this.value)">-->
										<span class="capacity_plus variant"><i class="iicon-icon-21"></i></span>
									</div>
								</div>
							</div>
							<div class="booking-option pick-date">
								<div class="booking-option-cont">
									<div class="option-img">
										<img src="<?php echo base_url();?>assets/front/images/pick-date.png">
									</div>
									<div class="booking-date-font booking-option-text">
										<span><?php echo $this->lang->line('pick_date') ?></span>
										<div class="form-group">
								            <input type='text' class="form-control" name="date_time" id='datetimepicker1' placeholder="<?php echo $this->lang->line('pick_date') ?>"  readonly="readonly" value = "<?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('date_time')))? $this->session->userdata('date_time'): '' ?>" >
								        </div>
									</div>
								</div>
								<div class="add-cart-item">
								</div>
							</div>
							<div class="booking-option1">
								<div class="booking-option-cont1">
									<div class="booking-option-text">
										<span><?php echo $this->lang->line('additional_comment') ?></span>
										<label><?php echo $this->lang->line('max_allowed') ?></label>
										<div class="form-group">
										<textarea class="form-control" name="user_comment" id="user_comment" rows="6" value=""><?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('event_user_request'))) ? ($this->session->userdata('event_user_request')) : ' '; ?></textarea></div>
									</div>
								</div>
							</div>
							<div class="continue-btn">
                                <button type="submit" name="submit_page" id="submit_page" value="Check Availability" class="btn"><?php echo $this->lang->line('check_avail') ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section><!--/ end content-area section -->
<!-- booking_availability -->
<div class="modal modal-main" id="booking-available">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_availability') ?></h4>
        <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
        <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
<div class="modal modal-main" id="booking-confirmation">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('booking_confirmation') ?></h4>
        <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
        <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
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
      		<button class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel') ?></button>
      	</div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
$(function () {
    var dateToday = new Date();
    dateToday.setMinutes( dateToday.getMinutes() + 15 );
    var maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 3, 0);
	maxDate = new Date(maxDate);
	var datetimepicker_format = "<?php echo datetimepicker_format; ?>";
    $('#datetimepicker1').datetimepicker({ 
		minDate: dateToday,
		ignoreReadonly: true,
		useCurrent: false,
		defaultDate: dateToday,
		maxDate: maxDate,
		format: datetimepicker_format
   });
});
//restricting to enter more than 4 digits in input type number
$(document).on('input','#no_of_people',function(){
	$('input[type=number][max]:not([max=""])').on('input', function(ev) {
        var people_maxlength = $(this).attr('max').length;
        var value = $(this).val();
        if (value && value.length >= people_maxlength) {
          $(this).val(value.substr(0, people_maxlength));
        }
    });
});
$(document).ready(function() {
    $('.capacity_minus').on("click", function () {
      var min_capacity = $('#min_people').val();
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) - 1;
      if(count >= min_capacity){
        /*count = count < 1 ? 1 : count;
        if(count < 1 || isNaN(parseInt(count)) ){
          count = 1;
        }      */
        $input.val(count);
        $input.change();
        $('#peepid').html('<strong>'+count+' People</strong>');
        $('#no_of_people').valid();
      }else{
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Minimum capacity allowed for restaurant is "+ min_capacity +".",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "La capacité minimale autorisée pour le restaurant est"+" "+min_capacity +".",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "الحد الأدنى للسعة المسموح بها للمطعم هو" +" "+min_capacity+".",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
      return false;
    });
    $('.capacity_plus').on("click", function () {
      var max_capacity = $('#max_people').val();
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) + 1;
      if(count <= max_capacity){
        /*if(count < 1 || isNaN(parseInt(count)) ){
          count = 1;
        }
        if(count > 9999){
          count = 9999;
        }*/
        $input.val(count);
        $input.change();
        $('#peepid').html('<strong>'+count+' People</strong>');
        $('#no_of_people').valid();
      }else{
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Maximum capacity allowed for restaurant is "+ max_capacity +".",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "La capacité maximale autorisée pour le restaurant est"+" "+max_capacity +".",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "الحد الأقصى للسعة المسموح بها للمطعم هو" +" "+max_capacity+".",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
      return false;
    });
});
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
<?php $this->load->view('footer'); ?>
