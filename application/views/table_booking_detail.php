<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('header'); 
$this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0; ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<?php if(empty($restaurant_details['restaurant'])) {
    redirect(base_url().'restaurant/event-booking');
} ?>
<section class="inner-banner booking-detail-banner">
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
									$rating_txt = ($restaurant_reviews_count > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
								<li><i class="iicon-icon-05"></i><?php echo ($restaurant_details['restaurant'][0]['ratings'] > 0)?$restaurant_details['restaurant'][0]['ratings'].' ('.$restaurant_reviews_count.' '.strtolower($rating_txt).')':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?></li>
								<?php } ?>
								<li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['open']).'-'.$this->common_model->timeFormat($restaurant_details['restaurant'][0]['timings']['close']); ?></li>
								<li><i class="iicon-icon-19"></i><a href="tel:<?php echo $restaurant_details['restaurant'][0]['phone_number']; ?>"><?php echo $restaurant_details['restaurant'][0]['phone_number']; ?></a></li>
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
					<h2><?php echo $this->lang->line('book_table') ?></h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-12">
				<div class="your-booking-main">
					<div class="detail-list-box">
						<form id="check_table_availability" class="form-horizontal" name="check_table_availability" method="post">
							<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_content_id']; ?>">
							<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
							<input type="hidden" name="name" id="name" value="<?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?>">
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label class="control-label"><?php echo $this->lang->line('what_day') ?><span class="required">*</span></label>
										<select name="datepicker" onchange="addSlot()" class="form-control sumo" id="datepicker">
                                            <option value=""><?php echo $this->lang->line('select_day') ?></option>
                                            <?php foreach ($restaurant_details['timearr'] as $key => $value) { ?>
                                            	<option value="<?php echo $value ?>" <?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('booking_date') == $value)) ? 'selected' : ' '; ?>><?php echo $value ?></option>
                                            <?php } ?>
                                        </select>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="control-label"><?php echo $this->lang->line('start_time') ?><span class="required">*</span></label>
										<select name="starttime" id="starttime" onchange="addEndTimeSlot('is_start')" class="form-control sumo">
										    <?php foreach ($restaurant_details['timeslots'] as $key => $value) { ?>
										        <option value="<?php echo $value ?>" <?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('start_time') == $value)) ? 'selected' : ' '; ?>><?php echo $this->common_model->timeFormat($value);?></option>
										    <?php } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="control-label"><?php echo $this->lang->line('end_time') ?><span class="required">*</span></label>
										<select name="endtime" id="endtime" onchange="addEndTimeSlot('is_end')" class="form-control sumo">
											<?php $arry_size = sizeof($restaurant_details['timeslots']); ?>
										    <?php foreach ($restaurant_details['timeslots'] as $key => $value) {
										    $selected = (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && ($this->session->userdata('end_time') == $value)) ? ('selected') : (($this->session->userdata('is_user_login') != 1)&&($key==$arry_size-1) ? 'selected' : "");

										    ?>
										        <option value="<?php echo $value ?>" <?php echo $selected; ?>><?php echo $this->common_model->timeFormat($value);?></option>
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
                                       	<input type="text" name="first_name" id="event_first_name" class= "form-control" value="<?php echo ($this->session->userdata('userFirstname'))?($this->session->userdata('userFirstname')):"" ?>" placeholder="<?php echo $this->lang->line('enter_first_name'); ?>">
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="event_last_name" class= "form-control" value="<?php echo ($this->session->userdata('userLastname'))?($this->session->userdata('userLastname')):"" ?>"  placeholder="<?php echo $this->lang->line('enter_last_name'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group table_class">
                                    	<input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
                                        <input type="tel" name="phone_number_inp" id="phone_number_inp" class="form-control" value="<?php echo ($this->session->userdata('userPhone'))?($this->session->userdata('userPhone')):"" ?>" maxlength="14" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>">
                                    <div id="event_phone_number_error"></div>
                                  	</div>
                               	</div>
                                <div class="col-md-6">  
                                    <div class="form-group">
                                        <input type="email" name="email" id="event_email" class= "form-control" value="<?php echo ($this->session->userdata('userEmail'))?($this->session->userdata('userEmail')):"" ?>" placeholder="<?php echo $this->lang->line('enter_email_address'); ?>">  
                                        <div id="event_email_error"></div>
                                    </div>
                                </div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label"><?php echo $this->lang->line('additional_comment') ?></label>
										<div id="max_people"><?php echo $this->lang->line('max_allowed') ?></div>
										<textarea class="form-control" name="user_comment" id="user_comment" rows="5"><?php echo (!empty($this->session->userdata('UserID')) && $this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('user_request'))) ? ($this->session->userdata('user_request')) : ' '; ?></textarea>
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
      		<h2><?php echo $this->lang->line('table_booking_confirmed_text1') ?></h2>
      		<!-- <p><?php //echo $this->lang->line('booking_confirmed_text2') ?></p> -->
      		<a href="<?php echo base_url().'myprofile/view-my-tablebookings'; ?>" class="btn"><?php echo $this->lang->line('view_tablebookings') ?></a>
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
      		<span id="start_time_less" class="d-none"></span>
      		<button class="btn" data-dismiss="modal"><?php echo $this->lang->line('cancel') ?></button>
      	</div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {       
    Layout.init(); // init current layout
       $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>...", selectAll: true , placeholder : "<?php echo $this->lang->line('select_').' '.$this->lang->line('here'); ?>" });
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
//intl-tel-input plugin
var onedit_iso = '';
<?php if($this->session->userdata('phone_codeval')) {
    $onedit_iso = $this->common_model->getIsobyPhnCode($this->session->userdata('phone_codeval')); ?>
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
const phoneInputField = document.querySelector("#phone_number_inp");
const phoneInput = window.intlTelInput(phoneInputField, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    autoPlaceholder:"polite",
    formatOnDisplay:false,
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js',
        //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
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
//phone number login form :: end
</script>
<script type="text/javascript">
	function addEndTimeSlot(start_end_flag) {
		var restaurant_id = $('#restaurant_id').val();
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
		var restaurant_id = $('#restaurant_id').val();
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
</script>
<?php $this->load->view('footer'); ?>
