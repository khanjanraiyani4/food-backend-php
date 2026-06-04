<!DOCTYPE html>
<?php $default_lang = $this->common_model->getdefaultlang();?>
<html lang="<?php echo $default_lang->language_slug; ?>">
	<head>
		<!-- Required meta tags -->
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	    <meta name="robots" content="nofollow, noindex">
	    <title><?php echo $page_title; ?></title>
	    <!-- SEO and SMO meta tags -->
	    <meta name="description" content="">
	    <meta name="keywords" content="">
	    <!-- Required Stylesheet -->
	    <link rel='stylesheet' href='<?php echo base_url(); ?>assets/front/css/animate.min.css'>
	    <link rel='stylesheet' href='<?php echo base_url(); ?>assets/front/css/owl.carousel.min.css'>
	    <link href="<?php echo base_url();?>assets/admin/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/style.php" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/slick.css" type="text/css">
		<?php if ($current_page == "HomePage") { ?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/slick-theme.css" type="text/css">
		<?php } ?>
		<?php if($this->session->userdata('language_slug')  == 'ar'){?>
	    	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/bootstrap-rtl.min.css" type="text/css">
	    	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/main-rtl.css" type="text/css">
    		<link href="<?php echo base_url();?>assets/front/css/style-rtl.css" rel="stylesheet" type="text/css"/>
    		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/responsive-rtl.css" type="text/css">
		<?php } else { ?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/bootstrap.min.css" type="text/css">
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/main.css" type="text/css">
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/style.css" type="text/css">
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/responsive.css" type="text/css">
		<?php } ?>
		<?php if ($current_page == "HomePage") { ?>
			<link rel="preload" href="<?php echo base_url();?>assets/front/images/doorstep.jpg" as="image"></head>
		<?php } ?>
	    <!-- Required jQuery -->
	     <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/jquery.min.js'></script> 
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/wow.min.js'></script>
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/modernizr-custom.js'></script>
	    <script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/popper.min.js" defer></script>
		 <script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/bootstrap.min.js"></script> 
	    <script type="text/javascript" src='<?php echo base_url(); ?>assets/front/js/owl.carousel.min.js' defer></script>
	    <script type="text/javascript" src="<?php echo base_url();?>assets/front/js/jquery.validate.min.js"></script>
	    <?php if ($current_page == "HomePage") { ?>
	    	<script type="text/javascript">
	    		var SELECTED_LANG = '<?php echo $this->session->userdata('language_slug') ?>';
	    	</script>
			<script type="text/javascript" src="<?php echo base_url();?>assets/front/js/slick.js"></script>
		<?php } ?>
	    <!-- Favicons -->
	    <link rel="shortcut icon"  sizes="40x40" href="<?php echo base_url();?>assets/front/images/favicon.png"/><?php
	    if(!empty(website_header_script)){
	    	echo website_header_script;
	    }
	    ?>
	</head>
	<?php
		$distance_inarr = $this->db->get_where('system_option',array('OptionSlug'=>'distance_in'))->first_row();
		$distance_inVal = $this->lang->line('in_km');
		if($distance_inarr && !empty($distance_inarr))
		{
		    if($distance_inarr->OptionValue==0){
		        $distance_inVal = $this->lang->line('in_mile');
		    }
		}
	?>

	<script>
		var distance_inVal = '<?php echo $distance_inVal; ?>';
	    var BASEURL = '<?php echo base_url();?>';
	    var USER_ID = '<?php echo $this->session->userdata('UserID'); ?>';
	    var IS_USER_LOGIN = '<?php echo $this->session->userdata('is_user_login'); ?>';
	    var IS_GUEST_CHECKOUT = '<?php echo $this->session->userdata('is_guest_checkout'); ?>';
	    var SEARCHED_LAT = '<?php echo ($this->session->userdata('searched_lat'))?$this->session->userdata('searched_lat'):''; ?>';
	    var SEARCHED_LONG = '<?php echo ($this->session->userdata('searched_long'))?$this->session->userdata('searched_long'):''; ?>';
	    var SEARCHED_ADDRESS = '<?php echo ($this->session->userdata('searched_address'))?$this->session->userdata('searched_address'):''; ?>';
	    var ADD = "<?php echo $this->lang->line('add') ?>";
	    var ADDED = "<?php echo $this->lang->line('added') ?>";
	    var ORDER_FOR_LATER = "<?php echo $this->lang->line('order_for_later') ?>";
	    var EDIT = "<?php echo $this->lang->line('edit') ?>";
	    var SELECTED_LANG = '<?php echo $this->session->userdata('language_slug') ?>';
	    var ORDER_CANCELED = "<?php echo $this->lang->line('cancel_order_success_msg') ?>";
	    var OK_TEXT = "<?php echo $this->lang->line('ok') ?>";
	    var VALID_ADDRESS = "<?php echo $this->lang->line('add_valid_location') ?>";
	    var ADDRESS_ERR = "<?php echo $this->lang->line('address_err') ?>";
	    var current_pagejs = '<?php echo $current_page ?>';
	    var maximum_range_for_slider = <?php echo NEAR_KM ?>;
	    var maximum_range_pickup_for_slider = <?php echo maximum_range_pickup ?>;
	    var RADIO_CHECKTEXT = "<?php echo $this->lang->line('radio_check') ?>";
	    var EDIT_CARDEXT = "<?php echo $this->lang->line('edit_card') ?>";
	    var VALID_CARD_NO = "<?php echo $this->lang->line('add_valid_card_number') ?>";
	    var VALID_CARD_CVV = "<?php echo $this->lang->line('add_valid_cvv') ?>";
	    var VALID_CARD_MONTH = "<?php echo $this->lang->line('add_valid_card_month') ?>";
	    var VALID_CARD_YEAR = "<?php echo $this->lang->line('add_valid_card_year') ?>";
		var CANCEL_REFUND_ERROR = "<?php echo $this->lang->line('refund_canceled_error') ?>";
	    var tip_greaterthan_zero = "<?php echo $this->lang->line('tip_greaterthan_zero') ?>";
	    var custom_tip_decimal_error = "<?php echo $this->lang->line('custom_tip_decimal_error') ?>";
	    var ORDER_CANCELED_REFUNDED = "<?php echo $this->lang->line('cancel_order_success_msg_refunded') ?>";
	    var TIP_PAID = "<?php echo $this->lang->line('tip_already_paid') ?>";
	    var freedelivery_offer = "<?php echo $this->lang->line('freedelivery_offer') ?>";
	    var availability_filter_txt = "<?php echo $this->lang->line('availability') ?>";
	    var ADD_MOBILE_NUMBER = "<?php echo $this->lang->line('add_phone_number') ?>";
	    var ENTER_YOUR_MOBILE_NUMBER = "<?php echo $this->lang->line('enter_your_phn_no') ?>";
	    var default_country_fromheader = '<?php echo country;?>';
	</script>
	<?php $lang_class = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') . '-lang' : $default_lang->language_slug.'-lang';?>
	<?php $lang_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : $default_lang->language_slug;
	$cmsPages = $this->common_model->getCmsPages($lang_slug); 
	$order_mode_frm_dropdown = ($this->session->userdata('order_mode_frm_dropdown')) ? $this->session->userdata('order_mode_frm_dropdown'):''; ?>
	<body class="<?php echo $lang_class; ?> for_header_popup">
		<?php
	    if(!empty(website_body_script)){
	    	echo website_body_script;
	    }
	    ?>
		<?php if ($current_page != "Login" && $current_page != "Registration" && $current_page != "JsonOrderDetails") { ?>
			<header class="header-area">
				<div class="container">
					<div class="header-inner">
						<div class="logo">
							<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/logo.svg" alt=""></a>
						</div>
						<?php if($current_page == 'OrderFood') { ?>
							<form id="order_food_form" class="inner-pages-form">
								<div class="form-group search-restaurant">
									<select id="order_mode" class="order_mode" name="order_mode">
										<option value="Delivery" <?php echo ($order_mode_frm_dropdown == 'Delivery')?'selected':''; ?>><?php echo $this->lang->line('delivery_word') ?></option>
										<option value="PickUp" <?php echo ($order_mode_frm_dropdown == 'PickUp')?'selected':''; ?>><?php echo $this->lang->line('pickup_word') ?></option>
									</select>
									<div class="home_auto_location delivery-address">
										<a href="javascript:;" class="auto_location" onclick="getLocation('order_food');"></a>
										<input type="text" name="address" id="address" onFocus="geolocate('order_food')" placeholder="<?php echo $this->lang->line("enter_address"); ?>" value="">
										<a href="javascript:;" class="clear_icon" id="for_address" onclick="clearField('address','order_food',this.id);"></a>
										<input type="hidden" name="latitude" id="latitude" value="">
										<input type="hidden" name="longitude" id="longitude" value="">
									</div>
									<div class="order_food_search">
										<input type="text" name="resdishes" id="resdishes" value="" placeholder="<?php echo $this->lang->line('search_res') ?>">
										<a href="javascript:;" class="clear_icon" id="for_res_search" onclick="clearField('resdishes','order_food',this.id);"></a>
									</div>

									<?php $err_msg = $this->lang->line('add_valid_location');
									$oktext = $this->lang->line('ok'); ?>
									<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" id="fillInAddressBtn" onclick="fillInAddress('order_food','<?php echo $err_msg; ?>','<?php echo $oktext; ?>')">
								</div>
							</form>
						<?php } ?>
						<nav>
							<?php if($current_page != 'OrderFood') { ?>
							<ul id="example-one">
								<li class="<?php echo ($current_page == 'HomePage') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('home') ?></a></li>
								<?php /* ?><li class="<?php echo ($current_page == 'OrderFood') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'restaurant'; ?>"><?php echo $this->lang->line('order_food') ?></a></li><?php */ ?>
								<li class="<?php echo ($current_page == 'Recipe') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'recipe'; ?>"><?php echo $this->lang->line('recipies') ?></a></li>
								<?php /* if($this->session->userdata('UserType') != 'Agent' || $this->session->userdata('UserType') == '') { ?>
									<li class="<?php echo ($current_page == 'EventBooking') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'restaurant/event-booking'; ?>"><?php echo $this->lang->line('online_reservation') ?></a></li>
								<?php } */ ?>
								<?php if (!empty($cmsPages)) {
									foreach ($cmsPages as $key => $value) { 
										if($value->CMSSlug == "contact-us") { ?>
											<li class="<?php echo ($current_page == 'ContactUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'contact-us'; ?>"><?php echo $this->lang->line('contact_us') ?></a></li>
										<?php }
										else if ($value->CMSSlug == "about-us") { ?>
											<li class="<?php echo ($current_page == 'AboutUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'about-us'; ?>"><?php echo $this->lang->line('about_us') ?></a></li>
										<?php }
									}
								} ?>
								<li class="<?php echo ($current_page == 'faqs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'faqs'; ?>"><?php echo $this->lang->line('faqs') ?></a></li>
							</ul>
							<?php } ?>
							<div class="header-right">
								<div class="noti-cart">
									<ul>
										<?php if ($this->session->userdata('is_user_login') && !empty($this->session->userdata('UserID'))) {
											if($this->session->userdata('UserType') == 'Agent'){
									        	$userUnreadNotifications = $this->common_model->getAgentNotification($this->session->userdata('UserID'),'unread');
									        	$userNotifications = $this->common_model->getAgentNotification($this->session->userdata('UserID'));
									      	}else {
												$userUnreadNotifications = $this->common_model->getUsersNotification($this->session->userdata('UserID'),'unread');
												$userNotifications = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
											}
											$reminder = $this->common_model->EventBookingReminderNoti();
											$table_reminder = $this->common_model->TableBookingReminderNoti();
											$currentDateTime = date("Y-m-d H:i:s");
											$newDateTime = date("Y-m-d H:i:s", strtotime("+2 hours")); 
											$table_booking_reminder = array();
											foreach ($reminder as $key => $value) {
											 	$hourdiff = round((strtotime($value['booking_date'] ) - strtotime($currentDateTime))/3600, 2);
											 	if($hourdiff <= 2  && $hourdiff > 0)
											    {
											    	$event_booking_reminder[] = $value;
											    }
											}
											foreach ($table_reminder as $key => $value) {
											 	$date_time = $value['booking_date']." ".$value['start_time'];
									            $hourdiff = round((strtotime($date_time ) - strtotime($currentDateTime))/3600, 1);
											 	if($hourdiff <= 2  && $hourdiff > 0)
											    {
											    	$table_booking_reminder[] = $value;
											    }
											}
											$event_noti = (!empty($event_booking_reminder))?count($event_booking_reminder):0;
											$user_noticnt = (!empty($userUnreadNotifications))?count($userUnreadNotifications):0;
											$table_noti = (!empty($table_booking_reminder))?count($table_booking_reminder):0;
											$notification_count = $user_noticnt + $event_noti + $table_noti;
											?>
											<li class="notification" alt="<?php echo $this->lang->line('notification'); ?>" title="<?php echo $this->lang->line('notification'); ?>">
												<div id="notifications_list">
													<?php if (!empty($userNotifications) || !empty($event_booking_reminder)) { ?>
														<a href="javascript:void(0)" class="notification-btn"><i class="iicon-icon-01"></i><span class="notification_count"><?php echo $notification_count; ?></span></a>
														<div class="noti-popup">
															<div class="noti-title">
																<h5><?php echo $this->lang->line('notification') ?></h5>
																<div class="bell-icon">
																	<i class="iicon-icon-01"></i>
																	<span class="notification_count"><?php echo $notification_count; ?></span>
																</div>
															</div>
															<div class="noti-list">
																<?php if (!empty($event_booking_reminder)) {
																    foreach ($event_booking_reminder as $key => $value) {
																        ?>
																		<div class="noti-list-box">
																			<?php 
																				$event_msg = $this->lang->line('reminder');
																				$value['booking_date'] = $this->common_model->getZonebaseDateMDY($value['booking_date']);
																				$time = $this->common_model->timeFormat($value['booking_date']);
																			?>
																			<?php $message =  sprintf($event_msg, $time , $value['rname'] , $value['address'] , $value['no_of_people']); ?>
																			<div class="noti-list-text">
																				<h6><?php echo $this->lang->line('event_reminder'); ?></h6>
																				<p><?php echo $message; ?></p>
																			</div>
																		</div>
																	<?php }
																}?>
																<?php  if (!empty($table_booking_reminder)) {
																    foreach ($table_booking_reminder as $key => $value) {
																        ?>
																		<div class="noti-list-box">
																			<?php 
																				$table_msg = $this->lang->line('table_reminder');
																				$start_time = $this->common_model->getZonebaseTime($value['start_time']);
																				$time = $this->common_model->timeFormat($start_time);
																			 ?>
																			<?php $message =  sprintf($table_msg, $time , $value['rname'] , $value['address'] , $value['no_of_people']); ?>
																			<div class="noti-list-text">
																				<h6><?php echo $this->lang->line('table_booking'); ?></h6>
																				<p><?php echo $message; ?></p>
																			</div>
																		</div>
																	<?php }
																}  ?>
																<?php if (!empty($userNotifications)) {
																    foreach ($userNotifications as $key => $value) {
																        if (date("Y-m-d", strtotime($value['datetime'])) == date("Y-m-d")) {
																            //$noti_time = date("H:i:s") - date("H:i:s", strtotime($value['datetime']));
																            //$noti_time = abs($noti_time) . ' '.$this->lang->line('mins_ago');
																            $noti_time_cal = strtotime(date('Y-m-d h:i:s'))-strtotime($value['datetime']);
																            $noti_time_round = round(abs($noti_time_cal/60));
																            //in hours
																            if($noti_time_round>59){
																            	$noti_time_r= (round($noti_time_round/60));
																            	$hour_msg = ($noti_time_r>1)?$this->lang->line('hours_ago'):$this->lang->line('hour_ago');
																            	$noti_time = $noti_time_r .' '. $hour_msg;
																            }
																            //in mins
																            else{
																            	$min_msg = ($noti_time_round>1)?$this->lang->line('mins_ago'):$this->lang->line('min_ago');
																            	$noti_time =$noti_time_round .' '. $min_msg;
																            }
																        } else {
																            $d1 = strtotime(date("Y-m-d",strtotime($value['datetime'])));
																			$d2 = strtotime(date("Y-m-d"));
																			$noti_time = ($d2 - $d1)/86400;
																			$noti_time = ($noti_time > 1 )?$noti_time.' '.$this->lang->line('days_ago'):$noti_time.' '.$this->lang->line('day_ago');
																        }
																        ?>
																		<div class="noti-list-box">
																			<?php $view_class = ($value['view_status'] == 0)?'unread':'read'; ?>
																			<div class="noti-list-text <?php echo $view_class; ?>">
																				<h6><?php echo ($value['notification_type'] == "order")?$this->lang->line('orderid'):(($value['notification_type'] == "event") ? ($this->lang->line('eventid')) : ($this->lang->line('tableid'))); ?>: #<?php echo $value['entity_id']; ?></h6>
																				<?php if($value['notification_slug'] == "order_rejected_refunded" || $value['notification_slug'] == "order_canceled_refunded" || $value['notification_slug'] == "order_initiated"){ ?>
																					<p><?php echo ($value['notification_slug'] == "order_rejected_refunded")?sprintf($this->lang->line('refund_reject_noti'),$value['entity_id']):(($value['notification_slug'] == "order_canceled_refunded")?sprintf($this->lang->line('refund_cancel_noti'),$value['entity_id']):sprintf($this->lang->line('refund_initiated_noti'),$value['entity_id']));  ?></p>
																				<?php } else if($value['notification_slug'] == "order_refund_failed" || $value['notification_slug'] == "order_refund_canceled" || $value['notification_slug'] == "tip_refund_initiated" || $value['notification_slug'] == "tip_refund_failed" || $value['notification_slug'] == "tip_refund_canceled" || $value['notification_slug'] == "order_refund_pending" || $value['notification_slug'] == "tip_refund_pending") { ?>
																					<p><?php echo ($value['notification_slug'] == 'tip_refund_initiated')?sprintf($this->lang->line($value['notification_slug']),$value['entity_id']):(($value['transaction_id'])?sprintf($this->lang->line($value['notification_slug']),$value['entity_id'],$value['transaction_id']):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($value['notification_slug'])),$value['entity_id'])); ?></p>
																				<?php } else if($value['notification_slug'] == "order_auto_cancelled") { ?>
																					<p><?php echo sprintf($this->lang->line('order_autocancelled_notimsg'),$value['entity_id']); ?></p>
																				<?php } else { ?>
																					<p><?php echo ($value['notification_slug'] == "event_cancelled")?$this->lang->line('event_cancelled_noti'):sprintf($this->lang->line($value['notification_slug']),$value['entity_id']); ?></p>
																				<?php } ?>
																				<span class="min"><?php echo $noti_time; ?></span>
																			</div>
																		</div>
																	<?php }
																}?>
															</div>
														</div>
													<?php } 
													else { ?>
														<a href="javascript:void(0)" class="notification-btn"><i class="iicon-icon-01"></i><span>0</span></a>
														<div class="noti-popup">
															<div class="noti-title">
																<h5><?php echo $this->lang->line('notification') ?></h5>
																<div class="bell-icon">
																	<i class="iicon-icon-01"></i>
																	<span>0</span>
																</div>
															</div>
															<div class="viewall-btn">
																<a href="javascript:void(0)" class="btn"><?php echo $this->lang->line('no_notifications') ?></a>
															</div>
														</div>
													<?php }?>
												</div>
											</li>
										<?php }?>
										<?php $cart_details = get_cookie('cart_details');
										$cart_restaurant = get_cookie('cart_restaurant');
										$cart = $this->common_model->getCartItems($cart_details,$cart_restaurant);
										/*$arraydetails = json_decode($cart_details);
										$newcartarray =array();
										foreach ($data['cart_items'] as $key => $value) {
											foreach ($arraydetails as $key => $val) {
												if($value['menu_id']==$val->menu_id){
													$newcartarray[] = $val;
												}else{
													$item_outofdtock=1;
												}
											}
										}
										if($item_outofdtock==1 && !empty($newcartarray)){
											delete_cookie('cart_details');
											delete_cookie('cart_restaurant');
											$cart_res = get_cookie('cart_restaurant');
											$this->input->set_cookie('cart_details',json_encode($newcartarray),60*60*24*1); // 1 day
		            						$this->input->set_cookie('cart_restaurant',$cart_res,60*60*24*1); // 1 day
										}*/
										$count = count($cart['cart_items']); 
										/*if($count == 0){
											delete_cookie('cart_details');
											delete_cookie('cart_restaurant');
										}*/
										?>
										<li  class="cart" alt="<?php echo $this->lang->line('title_cart'); ?>" title="<?php echo $this->lang->line('title_cart'); ?>"><a href="<?php echo base_url() . 'cart'; ?>"><i class="iicon-icon-02"></i><span id="cart_count"><?php echo $count; ?></span></a></li>
										<?php if($this->session->userdata('UserID')){ ?>
										<li title="<?php echo $this->lang->line('bookmarks'); ?>"><a id="bookmark_head" href="<?php echo base_url() ?>myprofile/#bookmarks"><i class="iicon-icon-28"></i></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="dropdown">
									<?php $language = $this->common_model->getLang($this->session->userdata('language_slug'));?>
							    	<button class="dropbtn"><img src="<?php echo base_url(); ?>assets/front/images/translate.png"><?php echo ($language) ? strtoupper($language->language_slug) : strtoupper($default_lang->language_slug); ?></button>
							    	<div class="dropdown-content">
										<?php $langs = $this->common_model->getLanguages();
    									foreach ($langs as $slug => $language) {
    									    $langname = ($language->language_name == 'English')?$this->lang->line('english'):(($language->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
    									?>
					                        <div onclick="setLanguage('<?php echo $language->language_slug ?>')"><a href="javascript:void(0)"><i class="glyphicon bfh-flag-<?php echo $language->language_slug ?>"></i><?php echo $langname; ?>
					                        </a></div>
					                    <?php }?>
					                </div>
				                </div>
								<?php if ($this->session->userdata('is_user_login')) {?>
									<div class="header-user">
										<?php /* ?><div class="user-img">
											<?php $u_image =  $this->common_model->getUserImage($this->session->userdata('UserID'));
												$image = (file_exists(FCPATH.'uploads/'.$u_image->image) && $u_image->image !='')?image_url.$u_image->image:default_user_img;
												 ?>
                        					<img src="<?php echo $image; ?>">
										</div><?php */ ?>
										<span class="user-menu-btn"><?php echo $this->session->userdata('userFirstname'); ?></span>
										<div class="header-user-menu">
											<ul>
												<li class="active"><a href="<?php echo base_url() . 'myprofile'; ?>"><i class="iicon-icon-31"></i><?php echo $this->lang->line('my_profile') ?></a></li>
												<li onclick="logout();"><a href="javascript:void(0)"><i class="iicon-icon-32"></i><?php echo $this->lang->line('logout') ?></a></li>
											</ul>
										</div>
									</div>
									<div class="signin-btn">
										<a href="<?php echo base_url().ADMIN_URL.'/home'; ?>" target="_blank" class="btn"><?php echo $this->lang->line('partner_login') ?></a>
									</div>
								<?php } else {?>
									<div class="signin-btn">
										<a href="<?php echo base_url() . 'home/login'; ?>" class="btn"><?php echo $this->lang->line('sign_in') ?></a>
										<a href="<?php echo base_url().ADMIN_URL.'/home'; ?>" target="_blank" class="btn"><?php echo $this->lang->line('partner_login') ?></a>
									</div>
								<?php }?>
								<?php if($current_page != 'OrderFood') { ?>
									<div class="mobile-icon">
										<button class="" id="nav-icon2"></button>
									</div>
								<?php } ?>
							</div>
						</nav>
					</div>
				</div>
			</header>
		<?php }?>