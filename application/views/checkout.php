<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('header'); 
if ($this->session->userdata('is_user_login') != 1) {
	parse_str(get_cookie('adminAuth'), $adminCook);
	$this->load->view('social_login_css'); 
}
require APPPATH . 'libraries/PaypalExpress.php';
$paypal_obj = new PaypalExpress;
$paypal = $paypal_obj->paypal_details();
$stripe_info = stripe_details();

//driver tip changes :: start
$driver_tip_arr = get_driver_tip_amount();
$is_custom_tip = 'yes';
$default_driver_tip = get_default_driver_tip_amount();
$default_driver_tip = ($default_driver_tip > 0 && $default_driver_tip != '') ? (float)$default_driver_tip : 0;
$selected_tip = ($this->session->userdata('tip_amount')>0)?(float)$this->session->userdata('tip_amount'):$default_driver_tip;
//driver tip changes :: end 
$enabled_dates = array();
foreach ($enabled_date_timeslots as $endt_key => $endt_value) { 
	array_push($enabled_dates, $endt_key); 
}
//if out of stock items are in cart then disable current date
if($is_out_of_stock_item_in_cart) {
	$disable_current_date = date('Y-m-d');
	$pos = array_search($disable_current_date, $enabled_dates);
	if($pos) {
		unset($enabled_dates[$pos]);
		$enabled_dates = array_values($enabled_dates);
	}
} ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
<link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet">
<style type="text/css">
	#submit_order.btn-primary.disabled, #submit_order.btn-primary:disabled{
		background-color: #E6E6E6;
    	border-color: #E6E6E6;
	}
</style>
<!-- Embed the intl-tel-input plugin -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/front/css/stripe/stripe_modal.css">
<section class="inner-pages-section cart-section">
	<div class="container" id="ajax_checkout">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('checkout') ?></h2>
				</div>
			</div>
		</div>
		<div class="row cart-row">
			<div class="col-lg-8">
				<div class="checkout-account">
					<div class="account-title">
						<?php /* ?> <img src="<?php echo base_url();?>assets/front/images/boy.svg"><?php */ ?>
						<h3><?php echo ($this->session->userdata('UserType') == 'Agent')?($this->lang->line('customer_details')):($this->lang->line('account')); ?><?php echo ($this->session->userdata('is_guest_checkout') == 1)?' ('.$this->lang->line('checkout_as_guest').')':''; ?></h3>
					</div>
					<?php if ($this->session->userdata('is_user_login') != 1 && $this->session->userdata('is_guest_checkout') != 1) { ?>
						<div class="account-tag-line">
							<p><?php echo $this->lang->line('acc_tag_line') ?></p>
						</div>
						<div id="login_form">
							<form action="<?php echo base_url().'checkout';?>" id="form_front_login_checkout" name="form_front_login_checkout" method="post" class="form-horizontal float-form">
		                        <div class="form-body">
		                        	<?php /*if(!empty($this->session->flashdata('error_MSG'))) {?>
			                            <div class="alert alert-danger">
			                                <?php echo $this->session->flashdata('error_MSG');?>
			                            </div>
		                            <?php }*/ ?>
		                            <?php 
                                        if($_SESSION['error_MSG'])
                                        { ?>
                                            <div class="alert alert-danger">
                                                 <?php echo $_SESSION['error_MSG'];
                                                    unset($_SESSION['error_MSG']);
                                                 ?>
                                            </div>
                                        <?php } ?>
		                            <?php if(!empty($loginError)){?>
			                            <div class="alert alert-danger">
			                                <?php echo $loginError;?>
			                            </div>
		                            <?php } ?>
		                            <?php if(validation_errors()){?>
			                            <div class="alert alert-danger login-validations">
			                                <?php echo validation_errors();?>
			                            </div>
		                            <?php } ?>
									<div class="login-details">
										<!-- radio btns for email and phone number :: start -->
			                            <div class="form-group login-select"> 
			                                <input type="hidden" name="frm_page" id="frm_page" value="loginpage">
			                                <div class="login_radio radiophn <?php echo ($this->session->userdata('login_with')=='phone_number')?'radio_login_checked':''; ?>">
			                                    <label for="phone_number">
			                                        <input type="radio" name="login_with" checked="checked" value="phone_number" id="phone_number"><span><?php echo $this->lang->line('signin_with').$this->lang->line('phone_number') ?></span>
			                                    </label> 
			                                </div>
			                                <div class="login_radio radioemail <?php echo ($this->session->userdata('login_with')=='email')?'radio_login_checked':''; ?>">
			                                    <label for="email">
			                                        <input type="radio" name="login_with" value="email" id="email"><span><?php echo $this->lang->line('signin_with').$this->lang->line('email') ?></span>
			                                    </label>
			                                </div>
			                            </div>
			                            <!-- radio btns for email and phone number :: end -->
			                            
			                        	<div class="form-group phn_num_container phone_number_div">
			                                <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="<?php if(isset($adminCook['phone_code'])) { echo $adminCook['phone_code']; } ?>">
			                                <input type="tel" name="login_phone_number" id="login_phone_number" class="form-control" placeholder="" value="<?php if(isset($adminCook['usr'])) { echo $adminCook['usr']; } ?>" maxlength='12'>
			                                <!-- <label><?php //echo $this->lang->line('phone_number') ?></label> -->

			                                <div class="phn_err"  style="display: none; color: red;"></div>
			                            </div>
			                            <div class="form-group email_div" style="display: none;">
			                                <input type="email" name="email_inp" id="email_inp" class="form-control" placeholder=" " value="<?php if(isset($adminCook['usr'])) { echo $adminCook['usr']; } ?>" maxlength='50'>
			                                <label><?php echo $this->lang->line('email') ?></label>
			                            </div>
			                            <div class="form-group mb-0 password-icon">
			                                <input type="password" name="login_password" id="login_password" class="form-control" placeholder=" " value="<?php  if(isset($adminCook['hash'])) { echo $adminCook['hash']; } ?>" >
			                                <label><?php echo $this->lang->line('password') ?></label>
			                                <i id="toggle-password" class="">
			                                    <img src="<?php echo base_url();?>assets/front/images/password-eye.svg" alt="">
			                                    <img src="<?php echo base_url();?>assets/front/images/password-eye-close.svg" alt="">
			                                </i>
			                            </div>
			                            <div class="links text-right">
			                                <div class="check-box">
			                                    <label>
			                                        <input type="checkbox" name="rememberMe" id="rememberMe" value="1" <?php echo ($adminCook)?"checked":""?> />
			                                        <span><?php echo $this->lang->line('remember') ?></span>
			                                    </label>
			                                </div>
			                                <a href="" class="link" data-toggle="modal" data-target="#forgot-pass-modal"><?php echo $this->lang->line('forgot_pass') ?></a> 
			                            </div>
			                        </div>
		                            <div class="action-button account-btn">
		                            	<button type="submit" name="submit_login_page" id="submit_login_page" value="Login" class="btn btn-primary"><?php echo $this->lang->line('title_login') ?></button>
		                            </div>
		                            <p class="already-acc">
		                                <?php echo $this->lang->line('dont_have_account') ?> <a href="<?php echo base_url().'home/registration';?>" class=""><?php echo $this->lang->line('sign_up') ?></a>
		                                <div class="or-underline"><span><?php echo $this->lang->line('or') ?></span></div>
		                               	<!-- social media login - start :: 11_march_2021 -->
			                            <div class="text-center login_social">
			                                <!-- facebook -->
			                                <a href="<?php echo $authURL; ?>"  class="btn-social btn-facebook"><?php echo $this->lang->line('fb_login') ?></a>
                                			<a href="<?php echo $google_login_url; ?>"  class="btn-social btn-google"><?php echo $this->lang->line('google_login') ?></a>
			                            </div>
			                            <!-- social media login - end :: 11_march_2021  -->
		                                <div class="text-center"><a href="<?php echo base_url().'checkout/checkout_as_guest';?>" class="btn btn-primary guest_chckout_btn"><?php echo $this->lang->line('checkout_as_guest') ?></a>
		                                </div>
		                            </p>
		                        </div>
		                    </form>
						</div>
					<?php } else if($this->session->userdata('is_guest_checkout') == 1){ ?>
					<div class="guest_checkout_is">
						<div id="guest_checkout_form_div" class="guest_checkout_form">
							<form action="" id="guest_checkout_form" name="guest_checkout_form" method="post" class="form-horizontal float-form">
		                        <div class="form-body">
		                        	<?php /*if(!empty($this->session->flashdata('error_MSG'))) {?>
			                            <div class="alert alert-danger">
			                                <?php echo $this->session->flashdata('error_MSG');?>
			                            </div>
		                            <?php }*/ ?>
		                            <?php 
                                        if($_SESSION['error_MSG'])
                                        { ?>
                                            <div class="alert alert-danger">
                                                 <?php echo $_SESSION['error_MSG'];
                                                    unset($_SESSION['error_MSG']);
                                                 ?>
                                            </div>
                                        <?php } ?>
		                            <?php if(!empty($loginError)){?>
			                            <div class="alert alert-danger">
			                                <?php echo $loginError;?>
			                            </div>
		                            <?php } ?>
		                            <?php if(validation_errors()){?>
			                            <div class="alert alert-danger login-validations">
			                                <?php echo validation_errors();?>
			                            </div>
		                            <?php } ?>
									<div class="guest-checkout-details row">
										<div class="form-group col-md-6">
			                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " maxlength='20'>
			                                <label><?php echo $this->lang->line('first_name') ?></label>
			                            </div>
			                            <div class="form-group col-md-6">
			                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " maxlength='20'>
			                                <label><?php echo $this->lang->line('last_name') ?></label>
			                            </div>
			                            <div class="form-group email_div col-md-6">
			                                <input type="email" name="email_inp" id="email_inp" class="form-control" placeholder=" " maxlength='50'>
			                                <label><?php echo $this->lang->line('email') ?></label>
			                            </div>
			                        	<div class="form-group col-md-6 phn_num_container phone_number_div">
			                                <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
			                                <input type="tel" name="login_phone_number" id="login_phone_number" class="form-control" placeholder="" maxlength='12'>
			                                <div class="phn_err"  style="display: none; color: red;"></div>
			                            </div>
			                        </div>
		                            <div class="action-button account-btn">
		                            	<input type="hidden" name="submit_guest_checkout" id="submit_guest_checkout" value="GuestCheckout">
		                            </div>
		                        </div>
		                    </form>
						</div>
					</div>
					<?php } else if($this->session->userdata('UserType') == 'Agent') { ?>
					<br>
					<div class="agent_order">
						<div id="agent_order_form_div" class="agent_order_form">
							<form action="" id="agent_order_form" name="agent_order_form" method="post" class="form-horizontal float-form">
		                        <div class="form-body">
		                        	<?php /*if(!empty($this->session->flashdata('error_MSG'))) {?>
			                            <div class="alert alert-danger">
			                                <?php echo $this->session->flashdata('error_MSG');?>
			                            </div>
		                            <?php }*/ ?>
		                            <?php 
                                    if($_SESSION['error_MSG'])
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['error_MSG'];
                                                unset($_SESSION['error_MSG']);
                                             ?>
                                        </div>
                                    <?php } ?>
		                            <?php if(!empty($loginError)){?>
			                            <div class="alert alert-danger">
			                                <?php echo $loginError;?>
			                            </div>
		                            <?php } ?>
		                            <?php if(validation_errors()){?>
			                            <div class="alert alert-danger login-validations">
			                                <?php echo validation_errors();?>
			                            </div>
		                            <?php } ?>
									<div class="agent-order-details row">
										<div class="form-group col-md-6 phn_num_container phone_number_div">
			                                <input type="hidden" name="consider_guest" id="consider_guest" class="form-control" value="yes">
			                                <input type="hidden" name="exist_user_id" id="exist_user_id" class="form-control" value="0">
			                                <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="">
			                                <input type="tel" name="login_phone_number" id="login_phone_number" class="form-control" onchange="checkNumberExistforAgent(this.value,'')" placeholder="" maxlength='12'>
			                                <label><?php echo $this->lang->line('phone_number') ?></label>
			                                <div class="phn_err"  style="display: none; color: red;"></div>
			                                <span id="phn_err_existuser"  style="color: red;"></span>
			                            </div>
										<div class="form-group col-md-6 first_name_ord">
			                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " maxlength='20' autocomplete="off">
			                                <label><?php echo $this->lang->line('first_name') ?></label>
			                            </div>
			                            <div class="form-group col-md-6 last_name_ord">
			                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " maxlength='20' autocomplete="off">
			                                <label><?php echo $this->lang->line('last_name') ?></label>
			                            </div>
			                            <div class="form-group email_div col-md-6">
			                                <input type="email" name="email_inp" id="email_inp" oninput="checkEmail(this.value)" class="form-control" placeholder=" " maxlength='50' autocomplete="off">
			                                <label><?php echo $this->lang->line('email') ?></label>
			                                <div id="EmailExist" class="display-no" style="color: red;"></div>
			                            </div>
			                        </div>
		                        </div>
		                    </form>
						</div>
					</div>
					<?php } else { ?>		
                        <div class="login-complete">
                        	<?php /* ?><div class="login-img-main">
                        		<div class="user-img">
                        		    <?php $u_image = $this->common_model->getUserImage($this->session->userdata('UserID'));
										$image = (file_exists(FCPATH.'uploads/'.$u_image->image) && $u_image->image !='')?image_url.$u_image->image:default_user_img; ?>
                        			<img src="<?php echo $image; ?>">
                        		</div>
                        	</div><?php */ ?>
                        	<div class="logged-in">
                        		<strong><?php echo $this->lang->line('logged_in') ?></strong>
                        		<p><?php echo $this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'); ?></p>
                        	</div>
                        </div>
					<?php } ?>
				</div>
				<div class="account-accordion">
					<div class="accordion" id="accordionExampleOne">
				        <div class="card" id="ajax_your_items">
				            <div class="card-header" id="headingOne">
				                <div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
				                	<img src="<?php echo base_url();?>assets/front/images/picnic-basket.svg">
				                	<h3><?php echo $this->lang->line('your_items') ?></h3>
				            	</div>	
				            </div>
				            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
				                <div class="card-body">
				                	<div class="cart-content-table">
					                    <table>
											<tbody>
												<?php if (!empty($cart_details['cart_items'])) { ?>
													<?php $menuids = array(); ?>
													<input type="hidden" name="total_cart_items" id="total_cart_items" value="<?php echo count($cart_details['cart_items']); ?>">
													<?php foreach ($cart_details['cart_items'] as $cart_key => $value) {
														array_push($menuids, $value['menu_id']); 
														$item_id = $value['menu_id']; ?>
														<tr>
															<?php /* ?><td class="item-img-main"><div><i class="iicon-icon-15 <?php echo ($value['is_veg'] == 1)?'veg':'non-veg'; ?>"></i></div></td><?php */ ?>
															<td class="item-name">
																<?php echo $value['name']; ?>
																<?php if ($value['is_combo_item']) {?>
																	<p style="font-weight: 400;"><?php echo nl2br($value['menu_detail']); ?></p>
																<?php }?>
																<ul class="ul-disc">
																	<?php if (!empty($value['addons_category_list'])) {
																		foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
																			<?php /*<li><h6><?php echo $cat_value['addons_category']; ?></h6></li> */ ?>
																			<ul class="ul-cir">
																			<?php if (!empty($cat_value['addons_list'])) {
																				foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
																					<li><?php echo $add_value['add_ons_name']; ?> <?php echo currency_symboldisplay(number_format($add_value['add_ons_price'],2),$currency_symbol->currency_symbol); ?></li>
																				<?php }
																			} ?>
																			</ul>
																		<?php } 
																	} ?>
																</ul>
																<div class="item-comment-input"><input type="text" name="item_comment_<?php echo $value['menu_id']; ?>" id="item_comment_<?php echo $value['menu_id'].'_'.$cart_key; ?>" value="<?php echo $value['comment'];?>" placeholder="<?php echo $this->lang->line('add_item_comment'); ?>" class="form-control" onblur="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'updatecomment',<?php echo $cart_key; ?>)" maxlength="250"></div>
															</td>
															<td><strong><?php echo currency_symboldisplay(number_format($value['totalPrice'],2),$currency_symbol->currency_symbol); ?></strong></td>
															<td>
																<div class="add-cart-item">
																	<div class="number">
																		<span class="minus" id="minusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
																		<input type="text" class="QtyNumberval" maxlength="3" name="item_count_check" id="item_count_check_<?php echo $cart_key; ?>" value="<?php echo $value['quantity']; ?>" onfocusout="EditCheckoutItemCount(this.value,<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,<?php echo $cart_key; ?>)" />
																		<span class="plus" id="plusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
																	</div>
																</div>
															</td>
															<td class="close-btn-cart"><button class="close-btn" alt="<?php echo $this->lang->line('delete'); ?>" title="<?php echo $this->lang->line('remove_item_txt'); ?>" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'remove',<?php echo $cart_key; ?>,'<?php echo addslashes($this->lang->line('delete_module')); ?>', '<?php echo addslashes($this->lang->line('ok')); ?>' , '<?php echo addslashes($this->lang->line('cancel')); ?>')"><i class="iicon-icon-38"></i></button></td>
														</tr>
													<?php } 
												} 
												else
												{ ?>
													<div class="cart-empty text-center">
														<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
														<h6><?php echo $this->lang->line("cart_empty"); ?><br> <?php $this->lang->line("add_some_dishes"); ?></h6>
													</div>	
												<?php }?>
											</tbody>
										</table>
										<?php if(!empty($cart_details['cart_items']) && !empty($restaurant_data->restaurant_slug) && $restaurant_data->status == 1 && $restaurant_data->enable_hours == 1 && $restaurant_data->timings['off'] == "open" && $restaurant_data->timings['closing'] == "Open"){ ?>
											<div class="add-more-item-section">
												<a href="<?php echo base_url().'restaurant/restaurant-detail/'.$restaurant_data->restaurant_slug;?>">
													<button class="btn"><?php echo $this->lang->line('want_to_add_more_items') ?></button>
												</a>
											</div>
										<?php } ?>
									</div>
				                </div>
				            </div>
				        </div>
				    </div>
				    <div id="order_mode_method">
					    <form id="checkout_form" name="checkout_form" method="post" action ='' class="form-horizontal float-form">	
					        <?php if (($this->session->userdata('is_user_login') == 1 && !empty($cart_details['cart_items'])) || ($this->session->userdata('is_guest_checkout') == 1 && !empty($cart_details['cart_items']))) { ?>
					        	<div class="accordion" id="accordionExampleTwo">
							        <div class="card" id="order_mode_content">
							            <div class="card-header" id="headingTwo">
							                <div class="card-header-title" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true">
							                	<img src="<?php echo base_url();?>assets/front/images/order-mode.svg">
							                	<h3><?php echo $this->lang->line('order_mode') ?></h3>
							                </div>
							            </div>
							            <div id="collapseTwo" class="collapse in show" aria-labelledby="headingTwo" data-parent="#accordionExampleTwo">
							                <div class="card-body">
							                    <div class="choose-order-mode">
							                    	<div class="choose-order-title">
							                    		<h6><?php echo $this->lang->line('choose_order_mode') ?></h6>
							                    	</div>
							                    	<div class="order-mode">
							                    		<div class="card">
									                    	<div class="radio-btn-list" id="order_mode_btn">
									                    		<?php if(empty($restaurant_order_mode)){ ?>
									                    			<label style="color:red;font-weight: 500;font-size: 15px;">
									                    				<?php echo $this->lang->line('front_end_order_mode_error_msg'); ?>
									                    			</label>
									                    			<input type="text" name="check_order_mode_val" id="check_order_mode_val" style="width: 0;height: 0;border:0;opacity:0">
									                    		<?php }?>
									                    		<input type="hidden" name="cart_restaurant" id="cart_restaurant" value="<?php echo $cart_restaurant; ?>">
																<input type="hidden" name="is_creditcard_fee_applied" id="is_creditcard_fee_applied" value="no">
																<input type="hidden" name="creditcard_feeval" id="creditcard_feeval" value="0">
																<input type="hidden" name="creditcard_fee_typeval" id="creditcard_fee_typeval" value="">
																<input type="hidden" name="is_service_fee_applied" id="is_service_fee_applied" value="no">
																<input type="hidden" name="service_feeval" id="service_feeval" value="0">
																<input type="hidden" name="service_fee_typeval" id="service_fee_typeval" value="">
																<input type="hidden" name="service_taxval" id="service_taxval" value="0">
																<input type="hidden" name="service_tax_typeval" id="service_tax_typeval" value="">
																<input type="hidden" name="create_intent_stripe" id="create_intent_stripe" value="no">
																<input type="hidden" name="menuids" id="menuids" value="<?php echo implode(',',$menuids); ?>">
									                    		<label>
									                    			<input type="hidden" name="is_agent" id="is_agent" value="<?php echo ($this->session->userdata('UserType') == 'Agent')?'yes':'no'; ?>">
									                    			<input type="hidden" name="subtotal" id="subtotal" value="<?php echo $cart_details['cart_total_price']; ?>">
									                    			<?php if(!empty($restaurant_order_mode) && in_array("delivery", $restaurant_order_mode)){ ?>
									                    				<input type="radio" name="choose_order" id="delivery" checked="checked" value="delivery" onclick="showDelivery(<?php echo $cart_details['cart_total_price']; ?>,'no');">
									                    				<span><?php echo $this->lang->line('delivery') ?></span>
									                    			<?php }?>
									                    		</label>
									                    		<label>
									                    			<?php if(count($restaurant_order_mode)==1 && in_array("pickup", $restaurant_order_mode)){ ?>
									                    				<input type="radio" name="choose_order" id="pickup" value="pickup" onclick="showPickup(<?php echo $cart_details['cart_total_price']; ?>);" checked="checked">
									                    				<span><?php echo $this->lang->line('pickup') ?></span>
									                    			<?php }else{ 
									                    				if(!empty($restaurant_order_mode) && in_array("pickup", $restaurant_order_mode)){ ?>
									                    				<input type="radio" name="choose_order" id="pickup" value="pickup" onclick="showPickup(<?php echo $cart_details['cart_total_price']; ?>);">
									                    				<span><?php echo $this->lang->line('pickup') ?></span>
									                    			<?php }} ?>
									                    		</label>
									                    	</div>
															<?php //schedule delivery changes :: start 
															if($sales_tax->allow_scheduled_delivery == 1) { ?>
															<div class="schedule-delivery-form" id="schedule-delivery-form">
																<div class="scheduling_mandatory_div" >
																	<?php if($is_out_of_stock_item_in_cart) { ?>
																		<div id="order_later_checkbox" >
																			<label>
																				<input type="hidden" name="schedule_order" id="schedule_order" value="yes">
																				<span><?php echo $this->lang->line('order_later_mandatory'); ?></span>
																			</label>
																		</div>
																	<?php } else { ?>
																		<div id="order_later_checkbox" class="radio-btn-list" >
																			<label>
																				<input type="checkbox" name="schedule_order" id="schedule_order" value="yes">
																				<span><?php echo $this->lang->line('order_later'); ?></span>
																			</label>
																		</div>
																	<?php } ?>
																</div>
																<input type="hidden" name="res_allow_scheduled_delivery" id="res_allow_scheduled_delivery" value="<?php echo $sales_tax->allow_scheduled_delivery; ?>">
																<div id="schedule_delivery_content" class="display-no" >
																	<br>
																	<div class="booking-option pick-date">
																		<div class="booking-option-cont">
																			<div class="booking-date-font booking-option-text">
																				<label><i class="fa fa-calendar-o" style="color:#17161a;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('select_date') ?></label>
																				<div class="form-group">
																					<input type='text' class="form-control" name="scheduled_date" id='datetimepicker1' placeholder="<?php echo $this->lang->line('select_date') ?>"  readonly="readonly" value ="" >
																				</div>
																				<div style="color:red;" id="scheduled_date_err" ></div>
																			</div>
																			<div class="booking-date-font booking-option-text timeslot">
																				<label><i class="fa fa-clock-o" style="color:#17161a;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('select_timeslot') ?></label>
																				<div class="form-group">
																					<input type="hidden" name="slot_open_time" id="slot_open_time" value="">
									                                                <input type="hidden" name="slot_close_time" id="slot_close_time" value="">
									                                                <select class="form-control" name="time_slot" onchange="addSlot()" id="time_slot">
									                                                    <option value=""><?php echo $this->lang->line('select')?></option>
									                                                    <?php foreach ($enabled_date_timeslots[$enabled_dates[0]] as $slotkey => $slotvalue) { ?>
																							<option value="<?php echo $slotkey ;?>" slot_open_time="<?php echo $slotvalue['start'] ?>" slot_close_time="<?php echo $slotvalue['end'] ?>"><?php echo $slotvalue['start'].' - '.$slotvalue['end'];?></option>    
									                                                    <?php } ?>
									                                                </select>
													                            </div>
													                            <div id="scheduled_time_err" style="color: red;"></div>
													                            <div class="res_closed_err" style="display: none; color: red;"></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<?php } 
															//schedule delivery changes :: end ?>
									                    	<div class="delivery-form display-no" id="delivery-form">
									                    	<?php if ($this->session->userdata('is_guest_checkout') !='1') { ?>
									                    		<div class="current-location">
									                    			<p><img src="<?php echo base_url();?>assets/front/images/current-location.svg"> <?php echo $this->lang->line('choose_delivery_address') ?></p>
									                    		</div>	
									                    		<div class="radio-btn-list">
										                    		<label>
										                    			<input type="radio" name="add_new_address" value="add_new_address" class="add_new_address" onclick="showAddAdress();">
										                    			<span><?php echo $this->lang->line('add_address') ?></span>
										                    		</label>
										                    	</div>
										                    	<input type="hidden" name="is_guest_checkout" id="is_guest_checkout" value="no">
									                    	<?php } else { ?>
									                    		<div class="current-location">
									                    			<p><img src="<?php echo base_url();?>assets/front/images/current-location.svg"> <?php echo $this->lang->line('add_address') ?></p>
									                    		</div>
										                    	<input type="hidden" name="add_new_address" class="add_new_address" value="add_new_address">
										                    	<input type="hidden" name="is_guest_checkout" id="is_guest_checkout" value="yes">
										                    <?php } ?>
										                    	<div id="add_address_content" class="<?php echo ($this->session->userdata('is_guest_checkout') =='1')?'':'display-no'; ?>">
									                    			<?php /* ?><h5><?php echo $this->lang->line('add_address') ?></h5><?php */ ?>
																	<div class="login-details">
											                            <div class="form-group home_auto_location checkout_loc">
											                            	<input type="hidden" name="add_latitude" id="add_latitude">
											                            	<input type="hidden" name="add_longitude" id="add_longitude">
											                            	<a href="javascript:;" class="auto_location" onclick="getLocation('checkout');"></a>
											                            	<input type="text" name="add_address" id="add_address" placeholder=" " class="form-control" value=""> <?php /* onFocus="geolocate('')" onchange="getLatLong('<?php echo $cart_details['cart_total_price']; ?>')" */ ?>
											                            	<span id="add_address_error" class="error" style="display: none;"><?php echo $this->lang->line('select_valid_location') ?></span>
											                                <label><?php echo $this->lang->line('your_location_txt') ?></label>
											                            </div>
											                            <div class="form-group">
											                                <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder=" " minlength="5" maxlength="6">
											                                <label><?php echo $this->lang->line('postal_code') ?></label>
											                            </div>
											                            <div class="form-group">
											                                <input type="text" name="landmark" id="landmark" class="form-control" placeholder=" ">
											                                <label><?php echo $this->lang->line('landmark_txt') ?></label>
											                            </div>
											                            <div class="form-group">
											                                <input type="text" name="address_label" id="address_label" class="form-control" placeholder=" ">
											                                <label><?php echo $this->lang->line('city_txt') ?></label>
											                            </div>
										                        	</div>	
										                        </div>	
									                    		<?php  $address = ($this->session->userdata('UserType')=='Agent')?'':$this->checkout_model->getUsersAddress($this->session->userdata('UserID')); 
									                    		if (!empty($address) || $this->session->userdata('UserType')=='Agent') { ?>
										                    		<div class="radio-btn-list your_address_inp <?php echo (empty($address))?'display-no':''; ?>">
											                    		<label>
											                    			<input type="radio" name="add_new_address" value="add_your_address" class="add_new_address" onclick="showYourAdress();">
											                    			<span><?php echo $this->lang->line('choose_your_address') ?></span>
											                    		</label>
											                    	</div>
										                        	<div id="your_address_content" class="display-no">	
											                        	<?php /* ?><h5><?php echo $this->lang->line('choose_your_address') ?></h5><?php */ ?>
																		<div class="login-details">
												                            <div class="form-group">
								                                                <select class="form-control" name="your_address" id="your_address" onchange="getAddLatLong(this.value,<?php echo $cart_details['cart_total_price']; ?>)">
								                                                    <option value=""><?php echo $this->lang->line('select')?></option>
								                                                    <?php foreach ($address as $key => $value) {?>
								                                                    	<option value="<?php echo $value['entity_id'];?>"><?php echo $value['address'].','.$value['landmark'].','.$value['zipcode'].','.$value['city'];?></option>    
								                                                    <?php } ?>
								                                                </select>
												                                <label><?php echo $this->lang->line('your_address') ?></label>
												                            </div>
											                        	</div>	
										                    		</div>
									                    		<?php }?>	
									                    	</div>
									                    	<!-- driver tip changes :: start -->
									                    	<div class="driver-tip-form display-no" id="driver-tip-form">
									                    		<div class="driver_tip_title">
									                    			<p><?php echo $this->lang->line('driver_tip') ?></p>
									                    		</div>	
									                    		<div class="driver_tip_content">
										                    		<div class="tip_row">
										                    			<?php $oneselectedclass = '';
										                    			if(!empty($restaurant_order_mode) && in_array("delivery", $restaurant_order_mode) && $this->session->userdata('is_user_login') == 1)
										                    			{	
										                    			foreach($driver_tip_arr as $key_tip=>$value_tip) { 
																			$tip_percent_val = (float)$value_tip;
																			$calculated_value_tip = ((float)$cart_details['cart_total_price'] * (float)$value_tip)/100;
                    														$calculated_value_tip = $this->common_model->roundDriverTip((float)$calculated_value_tip);

																			$selectedclass = '';
																			if($this->session->userdata('tip_percent_val') == (float)$tip_percent_val && $oneselectedclass == ''){
																				$is_custom_tip = 'no';
																				$selected_tip = $calculated_value_tip;
																				$selectedclass = 'tip_selected';
																				$oneselectedclass = 'tip_selected';
																			} else if($selected_tip == (float)$value_tip && $oneselectedclass == ''){
																				$is_custom_tip = 'no';
																				$selected_tip = $calculated_value_tip;
																				$selectedclass = 'tip_selected';
																				$oneselectedclass = 'tip_selected';
																			}
																			if(!($this->session->userdata('tip_amount')>0) && $selectedclass == 'tip_selected'){
																				$this->session->set_userdata('tip_amount',$selected_tip);
																			}
																			if(!($this->session->userdata('tip_percent_val')) && $selectedclass == 'tip_selected'){ 
																				$this->session->set_userdata('tip_percent_val',$tip_percent_val);
																			} ?>
											                    			<div class="tip_column">
																				<div class="tip_card">
																					<a href="javascript:void(0);" onclick="tip_selected(<?php echo $calculated_value_tip; ?>, this.id);" data-val="<?php echo $tip_percent_val; ?>" id="tip_<?php echo $key_tip?>" class="<?php echo $selectedclass; ?>" ><h6 class="form-control"><?php echo $value_tip.'%'; ?></h6></a>
																				</div>
																			</div>
																		<?php } } ?>
																		<div class="tip_column">
																			<div class="tip_card">
																				<input type="text" oninput="tip_selected(this.value, this.id);" class="form-control" id="custom_tip" value="<?php echo ($selected_tip>0 && $is_custom_tip == 'yes')?$selected_tip:''; ?>" placeholder="<?php echo $this->lang->line('custom_tip') ?>" >
																			</div>
																			<div id="custom_tip_error" class="error" style="display: none;"><?php echo $this->lang->line('custom_tip_decimal_error') ?></div>
																		</div>
										                    		</div>
										                    	</div>
									                    		<div class="driver_tip_btns" >
									                    			<input type="hidden" name="driver_tip" id="driver_tip" value="<?php echo ($selected_tip>0)?$selected_tip:''; ?>">
									                    			<button type="button" disabled="disabled" id="tip_clear_btn" class="btn" onclick="applyTip('clear');"><?php echo $this->lang->line('clear') ?></button>
									                    			<button type="button" disabled="disabled" id="tip_submit_btn" class="btn" onclick="applyTip('apply')"><?php echo $this->lang->line('submit') ?></button>
									                    		</div>
									                    	</div>
									                    	<!-- driver tip changes :: end -->
								                    	</div>
								                    	<div class="card card2" id="coupon_select">
															<div>
																<div class="current-location coupon_select" >
																<h5><?php echo $this->lang->line('apply_coupon') ?></h5>	
																	<a href="javascript:void(0)" class="btn" id="all_coupon_btn" onclick="all_couponShow(<?php echo $cart_details['cart_total_price']; ?>)"><?php echo $this->lang->line('all_coupon') ?></a>
																</div>		
																<p><?php echo $this->lang->line('choose_coupon') ?></p>
															</div>
														</div><?php
														/*
								                    	<div class="card">
															<div>
																<div class="current-location">
																	<h5><?php echo $this->lang->line('extra_comment') ?></h5> <span class="small">(<?php echo $this->lang->line('maxlength_msg'); ?>)</span>
																</div>		
														    	<div>	
										                            <div class="form-group">
										                                <input type="text" name="extra_comment" id="extra_comment" class="form-control" placeholder=" " maxlength="250">
										                                <label><?php echo $this->lang->line('extra_comment_txt') ?></label>
										                            </div>
																</div>
															</div>
														</div>*/
														?>
								                    	<div class="card delivery-instructions">
															<div>
																<div class="current-location">
																	<h5><?php echo $this->lang->line('delivery_instructions') ?></h5> <span class="small">(<?php echo $this->lang->line('maxlength_msg'); ?>)</span>
																</div>		
														    	<div>	
										                            <div class="form-group">
										                                <input type="text" name="delivery_instructions" id="delivery_instructions" class="form-control" placeholder=" " maxlength="250">
										                                <label><?php echo $this->lang->line('delivery_instructions_txt') ?></label>
										                            </div>
																</div>
															</div>
														</div>
							                    	</div>			  
							                    </div>
							                </div>
							            </div>
							        </div>	
							    </div>
						        <div class="accordion" id="accordionExampleThree">
							        <div class="card">
							            <div class="card-header" id="headingThree">
							                <div class="card-header-title" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true">
							                	<img src="<?php echo base_url();?>assets/front/images/payment.png">
							                	<h3><?php echo $this->lang->line('payment_method') ?></h3>
							            	</div>	
							            </div>
							            <div id="collapseThree" class="collapse in show" aria-labelledby="headingThree" data-parent="#accordionExampleThree">
							                <div class="card-body">
							                	<div class="payment-mode">
							                    	<div class="payment-title">
							                    		<h6><?php echo $this->lang->line('choose_payment_method') ?></h6>
							                    	</div>

							                    	<div class="order-mode">
							                    		<div class="card">
									                    	<div class="radio-btn-list">
								                    			<?php 
								                    			$cnt = 1;
								                    			if(!empty($paymentmethods)) {
									                    			foreach($paymentmethods as $payment_method){ ?>
									                    				<label>
									                    					<input type="radio" name="payment_option" class="payment_option" id="payment_option<?php echo $cnt; ?>" value="<?php echo $payment_method->payment_gateway_slug; ?>" /><span><?php echo $payment_method->payment_name ?></span>
									                    				</label>
									                    			<?php $cnt++; }
									                    		} else { ?>
									                    			<span style="color:red;"><?php echo $this->lang->line('payment_method_notavailable'); ?></span>
									                    		<?php } ?>
									                    		<label style="color:red;" id="blankmsg"></label>
									                    	</div>
								                    	</div>
							                    	</div>
							                    </div>
							                    <div class="proceed-btn" id="proceed-btn">
							                    	<div id="stripe_cod_btn">
                        								<button type="submit" name="submit_order" id="submit_order" value="Proceed" class="btn btn-primary"><?php echo $this->lang->line('proceed_btn') ?></button>
                        							</div>
                        							<div id='paypal-button'></div>
						                    	</div>
												<div class="checkout_note">
													<?php echo $this->lang->line('checkout_note'); ?>
												</div>
							                </div>
							            </div>
							        </div>
							    </div>
					        <?php } ?>	
					    </form>	
				    </div>	     
				</div>
				<!-- menu suggestion :: start -->
				<div id="ajax_your_suggestion">
				<?php if(!empty($menu_item_suggestion)){ ?>
					<div class="card suggestion-menu-item popular-rest-box">
						<div class="card-header bg-white">
							<h5><?php echo $this->lang->line('people_also_like'); ?></h5>
						</div>
						<div class="card-body row">
							<?php foreach($menu_item_suggestion as $key => $value){
								$addons = ($value['is_customize']==1)?'addons':''; 
								$page = ($this->session->userdata('is_guest_checkout') == 1) ? 'checkout_as_guest':'checkout'; ?>
								<div class="col-md-4 col-sm-6"> 
									<a id="addtocart-<?php echo $value['menu_id']; ?>" href="javascript:void(0)" onclick="checkCartRestaurantDetails(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'<?php echo $value['timings']['closing']; ?>','<?php echo $addons; ?>',this.id,'no', '<?php echo $page; ?>')">
										
										<div class="popular-rest-img">
											<?php $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_icon_img; ?>
											<img src="<?php echo $rest_image; ?>">
										</div>
									</a>
										<div class="popular-rest-content">
											<p class="h6"><?php echo $value['name']; ?></p>
									
											<?php if(!empty($value['offer_price'])){ ?>
												<p style="text-decoration: line-through;" class="h6"><?php echo currency_symboldisplay($value['price'],$currency_symbol->currency_symbol); ?></p>
												<p class="h6"><?php echo currency_symboldisplay($value['offer_price'],$currency_symbol->currency_symbol); ?></p>
											<?php } else { ?>
												<p class="h6"><?php echo currency_symboldisplay($value['price'],$currency_symbol->currency_symbol); ?></p>
											<?php } ?>
										
											<?php if ($addons == '') { ?>
												<div class="add-btn" id="cart_item_<?php echo $value['menu_id']; ?>">
													<?php $add = (in_array($value['menu_id'], $menuids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['menu_id']; ?>" id="addtocart-<?php echo $value['menu_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'',this.id,'<?php echo $page; ?>')"> <?php echo (!in_array($value['menu_id'], $menuids) || empty($menuids))?$this->lang->line('add'):$this->lang->line('added'); ?> </button>
												</div>
											<?php } else {?>
												<div class="add-btn" id="cart_item_<?php echo $value['menu_id']; ?>">
													<?php $add = (in_array($value['menu_id'], $menuids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['menu_id']; ?>" id="addtocart-<?php echo $value['menu_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'addons',this.id,'<?php echo $page; ?>')"> <?php echo (!in_array($value['menu_id'], $menuids) || empty($menuids))?$this->lang->line('add'):$this->lang->line('added'); ?> </button>
												</div>
											<?php } ?>
										</div>
										
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				</div>
				<!-- menu suggestion :: end -->
			</div>
			<?php if (!empty($cart_details['cart_items'])) { ?>
				<div class="col-lg-4" id="ajax_order_summary">             
					<div class="order-summary">
						<div class="order-summary-title">
							<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('order_summary') ?></h3>
						</div>
						<div class="order_from_res">
							<h5><?php echo $this->lang->line('order').' '.$this->lang->line('from') ?> : <?php echo $restaurant_name; ?></h5>
						</div>
						<div class="order-summary-content">
							<table>
								<tbody>
									<tr>
										<td><?php echo $this->lang->line('no_of_items') ?></td>
										<td><strong><?php echo count($cart_details['cart_items']); ?></strong></td>
									</tr>
									<tr>
										<td><?php echo $this->lang->line('sub_total') ?></td>
										<td><strong><?php echo currency_symboldisplay(number_format($cart_details['cart_total_price'],2),$currency_symbol->currency_symbol); ?></strong></td>
									</tr>
									<!--service tax changes start-->
									<?php $taxes_fees = 0;
									if($sales_tax->amount != '' && $sales_tax->amount != NULL && $sales_tax->amount > 0) {
                                        $tax_amount = 0;
                                        if($sales_tax->amount_type == 'Percentage')
                                        {
                                            $tax_amount = ($cart_details['cart_total_price'] * $sales_tax->amount) / 100;
                                        }else{
                                            $tax_amount = $sales_tax->amount; 
                                        }
                                        //$tax_amount = number_format($tax_amount, 2, '.', '');
                                        $tax_amount = round($tax_amount,2);
                                        $type = ($sales_tax->amount_type == 'Percentage')?'%':'';
        								$percent_text = ($sales_tax->amount_type == 'Percentage')?' ('.$sales_tax->amount.$type.')':'';
        								$taxes_fees = $taxes_fees + number_format($tax_amount,2); /* ?>
										<tr>
											<td><?php echo $this->lang->line('service_tax').$percent_text;?></td>
											<td><strong>+ <?php echo currency_symboldisplay(number_format($tax_amount,2),$currency_symbol->currency_symbol); ?></strong></td>
										</tr>
									<?php */ } ?>
									<!--service tax changes end-->
									<!--service fee changes start-->
									<?php $is_service_fee_applied = 'no';
									if($sales_tax->is_service_fee_enable == '1'){
										$is_service_fee_applied = 'yes';
										$service_fee_amount = 0;
                                        if($sales_tax->service_fee_type == 'Percentage') {
                                        	$service_fee_amount = ($cart_details['cart_total_price'] * $sales_tax->service_fee) / 100;
                                        }else{
                                            $service_fee_amount = $sales_tax->service_fee; 
                                        }
                                        $service_fee_amount = round($service_fee_amount,2);
                                    	$fee_type = ($sales_tax->service_fee_type == 'Percentage')?'%':'';
    									$fee_percent_text = ($sales_tax->service_fee_type == 'Percentage')?' ('.$sales_tax->service_fee.$fee_type.')':'';
        								$taxes_fees = $taxes_fees + number_format($service_fee_amount,2); /* ?>
										<tr>
											<td><?php echo $this->lang->line('service_fee').$fee_percent_text;?></td>
											<td><strong>+ <?php echo currency_symboldisplay(number_format($service_fee_amount,2),$currency_symbol->currency_symbol); ?></strong></td>
										</tr>
        							<?php */ } ?>
									<!--service fee changes end-->
									<!-- earning points changes start -->
									<?php //$temp_total = $cart_details['cart_total_price'] + @$delivery_charges + $tax_amount; 
									$temp_total = $cart_details['cart_total_price'];
									if ($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') != 'Agent') { 
										$earning_points =  $earning_points->wallet; 
										if(empty($earning_points)) { 
											$earning_points = 0;
										 } ?>
										<tr>
											<td><?php echo $this->lang->line('wallet_balance'); echo ': '; echo currency_symboldisplay($earning_points,$currency_symbol->currency_symbol); ?></td>
											<?php if($cart_details['cart_total_price'] >= $minimum_subtotal && $earning_points != 0) {  ?>
												<td>
													<input type="button" name="submit_redeem" class="btn" id="submit_redeem" value="<?php echo $this->lang->line('redeem') ?>" onclick="redeemPoints(<?php echo $temp_total; ?>)">
												</td>
											<?php } else {  ?>
												<td></td>
											<?php } ?>
										</tr>
									<?php } ?>
									<!-- earning points changes end -->
									<!-- taxes and fees :: start -->
									<tr>
										<td><?php echo $this->lang->line('taxes_fees'); ?>
											<div class="custom--tooltip" style="position:absolute;padding-left:6px;">
												<i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
												<span class="tooltiptext tooltip-right">
													<div id="servicetax_infodiv">
														<span class="custom_service"><?php echo $this->lang->line('service_tax'); ?> <span id="servicetaxtype_info"><?php echo $percent_text; ?></span></span> : <span class="service_price" id="servicetax_info"><?php echo currency_symboldisplay(number_format($tax_amount,2),$currency_symbol->currency_symbol); ?></span>
													</div>
													<div id="servicefee_infodiv">
														<span class="custom_service"><?php echo $this->lang->line('service_fee'); ?> <span id="servicefeetype_info"><?php echo $fee_percent_text; ?></span></span> : <span class="service_price" id="servicefee_info"><?php echo currency_symboldisplay(number_format($service_fee_amount,2),$currency_symbol->currency_symbol); ?></span>
													</div>
												</span>
											</div>
										</td>
										<td><strong>+<?php echo currency_symboldisplay($taxes_fees,$currency_symbol->currency_symbol);?></strong></td>
									</tr>
									<!-- taxes and fees :: end -->
								</tbody>
								<tfoot>
									<tr>
										<td><?php echo $this->lang->line('to_pay') ?></td>
										<?php $to_pay = $cart_details['cart_total_price'] + @$delivery_charges + $tax_amount + $service_fee_amount;  //added sales tax 
										$to_pay = ($to_pay > 0) ? round($to_pay,2) : 0;
										$this->session->set_userdata(array('payment_currency' => $currency_symbol->currency_code)); 
										$this->session->set_userdata(array('total_price' => $to_pay)); 
										?>
										<td><strong><?php echo currency_symboldisplay(number_format($to_pay,2),$currency_symbol->currency_symbol); ?></strong></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
<!--/ end content-area section -->
<!-- add items from menu suggestion :: start -->
<div class="modal modal-main modal-variation product-detail" id="menuDetailModal"></div>
<div class="modal modal-main modal-variation product-detail" id="addonsMenuDetailModal"></div>
<div class="modal modal-main" id="myModal"></div>
<!-- add items from menu suggestion :: end -->
<!-- Order Confirmation -->
<div class="modal modal-main" id="order-confirmation">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('order_confirmation') ?></h4>
        <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" onclick="document.location.href='<?php echo base_url();?>myprofile';"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
      			<picture>	                
	                <source type="image/svg" srcset="<?php echo base_url();?>assets/front/images/order-confirmation.svg">
      				<img src="<?php echo base_url();?>assets/front/images/order-confirmation.svg" alt="Booking availability">
            	</picture>
      		</div>
      		<h2><?php echo $this->lang->line('thankyou_for_order') ?></h2>
      		<p><?php echo $this->lang->line('order_placed') ?></p>
      		<span id="earned_points"></span>
      		<span id="track_order"><a href="<?php echo base_url();?>myprofile" class="btn"><?php echo $this->lang->line("track_order"); ?></a></span>
      	</div>
      </div>
    </div>
  </div>
</div>
<!-- order delivery not available start -->
<div class="modal modal-main" id="delivery-not-avaliable">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('delivery_not_available') ?></h4>
        <button type="button" class="close" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<div class="availability-popup">
      		<div class="availability-images">
	            <picture>	                
	                <source type="image/jpg" srcset="<?php echo base_url();?>assets/front/images/no-delivery.png">
      				<img src="<?php echo base_url();?>assets/front/images/no-delivery.png" alt="Booking availability">
            	</picture>
      		</div>
      		<h2><?php echo $this->lang->line('avail_text1') ?></h2>
      		<p><?php echo $this->lang->line('avail_text2') ?></p>
      	</div>
      </div>
    </div>
  </div>
</div>
<!-- order delivery not available end -->
<!--stripe modal start -->
<div id="user_details" class="modal fade payment_card_div" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title"><?php echo $this->lang->line('card_details') ?></h4>
        <button type="button" id="user_detailsid" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body stripe-modal-body">
        <form id="form_user_details" name="form_user_details" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <!-- stripe new changes :: 2feb2021 start -->
                    <!-- <div class="row" id="listall_card">
				    </div> -->
				    <div class="old-card-group" id="listall_card">                    				    
				    </div>
				    <div class="row">
                    	<div class="col-md-10">
                    		<div class="payment_label payment-checkout-new">
                    			<div class="radio-btn-list">
                    				<label class="payment_label">
                    					<?php 
				                    	$singlepaymentstyle = '';
				                    	$singlepaymentdisable = '';
				                    	if($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('UserType') == 'Agent')
				                    	{
				                    		$singlepaymentstyle = 'style="display: none;" checked="checked"';
				                    		$singlepaymentdisable = 'disabled';
				                    	}
				                    	?> 
                    					<div class="radio-btn-list">
		                    				<label class="payment_label">
					                    		<input type="radio" name="payment-source" <?php echo $singlepaymentstyle; ?> type="radio" name="payment-source" value="newcard" id="new-card-radio" onclick="togglecardbutton(this.value);">
					                    		<span <?php echo $singlepaymentstyle; ?>></span>
					                    	</label>
				                    	</div>
			                    	</label>
		                    	</div>
		                    	<div id="card-element"><!--Stripe.js injects the Card Element--></div>
	                    	</div>
                    	</div>
                        <div class="col-md-2 text-center">
		                    <button id="submit_stripe" <?php echo $singlepaymentdisable; ?>>
								<div class="spinner hidden" id="spinner"></div>
								<span id="button-text"><?php echo $this->lang->line('pay'); ?></span>
							</button>
                        </div>
                        <?php if($this->session->userdata('UserType') == 'User') { ?>
                        <div class="col-md-12">
                        	<div id="save_card_checkbox" class="save_card_checkbox">
                        		<div class="radio-btn-list">
                        			<label for="save_card_checkbox_val">
			                    		<input type="checkbox" id="save_card_checkbox_val" name="save_card_checkbox_val" value="yes">
										<span><?php echo $this->lang->line('do_you_want_to_save_card'); ?></span>
									</label>
								</div>
	                    	</div>
                        </div>
                    	<?php } ?>
                        <div class="col-md-12">
                        	<p id="card-error" role="alert" style="color:#fa755a;"></p>
							<p class="result-message hidden">Payment succeeded.</p>
                        </div>
                    </div>
                    <!-- stripe new changes :: 2feb2021 end -->
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--stripe modal end -->
<!-- restaurant closed start -->
<div class="modal modal-main" id="restaurant-closed">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('order_confirmation') ?></h4>
				<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
			<!-- Modal body -->
			<div class="modal-body">
				<div class="availability-popup">
					<h2><?php echo $this->lang->line('sorry_not_placed') ?></h2>
					<p><?php echo $this->lang->line('restaurant_closed') ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- restaurant closed end -->
<!-- coupon modal start -->
<div class="modal modal-main coupon_modal" id="coupon_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header start -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('apply_coupon') ?></h4>
				<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>
			<!-- Modal Header end -->
			<!-- Modal body start -->
			<div class="modal-body">
				<?php $err_msg = $this->lang->line('add_valid_coupon');
					$oktext = $this->lang->line('ok'); ?>
				<div class="coupon_search">
					<input type="text" name="coupon_searchval" id="coupon_searchval" placeholder="<?php echo $this->lang->line('coupon_code') ?>" class="form-control">
					<input type="button" name="search" onclick="showsearchcoupon(<?php echo $cart_details['cart_total_price']; ?>,'<?php echo $err_msg; ?>','<?php echo $oktext; ?>','yes');" value="<?php echo $this->lang->line('search') ?>" class="btn">
				</div>
				<div class="coupon_detail" id="coupon_detailid" >
				</div>
			</div>
			<!-- Modal body end -->
		</div>
	</div>
</div>
<!-- coupon modal end -->
<?php //cart total updated modal popup :: start ?>
<div class="modal modal-main delete-address_" id="cart_total_updated">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('cart_updated') ?>?</h4>
                <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" onclick="closeCartUpdatedModal()"><i class="iicon-icon-23"></i></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <p><?php echo $this->lang->line('cart_total_updated_text'); ?><p>
                <div class="action-btn">
                    <input type="button" name="place_order" id="place_order" value="<?php echo $this->lang->line('place_order') ?>" class="btn btn-primary" onclick="placeOrder()">
                    <input type="button" name="review_cart" id="review_cart" value="<?php echo $this->lang->line('review_cart') ?>" class="btn btn-primary" onclick="closeCartUpdatedModal()">
                </div>
            </div>
        </div>
    </div>
</div>
<?php //cart total updated modal popup :: end ?>
<!-- verify OTP modal :: start -->
<div class="modal modal-main std-modal" tabindex="-1" role="dialog" id="verify-otp-modal">
    <div class="modal-dialog modal-dialog-centered verify_otp_model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyotp_modaltitle"><?php echo $this->lang->line('verify_otp') ?></h5>
                <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal" aria-label="Close"> <i class="iicon-icon-23"></i></button>
            </div>
            <div class="modal-body">
	            <div class="row align-items-center">
	                <div class="col-md-6 col-sm-12">
	                   	<div class="text-center forgot-image"> <img src="<?php echo base_url();?>assets/front/images/fp-popup-image.png" alt="Verify OTP Image"></div>
	                </div>
	                <div class="col-md-6 col-sm-12">
	                    <div class="modal-form">
	                        <div class="alert alert-success display-no" id="verifyotp_success"></div>
	                        <div id="verify_otp_section">
	                            <h2 class="text-left" id="enter_otp_text"><?php echo $this->lang->line('enter_otp') ?></h2>
	                            <form id="form_front_verifyotp" name="form_front_verifyotp" method="post" class="form-horizontal float-form"  data-autosubmit="false" autocomplete="off">
	                                <div class="alert alert-danger display-no" id="verifyotp_error"></div>
	                                <?php if(validation_errors()){?>
	                                <div class="alert alert-danger">
	                                    <?php echo validation_errors();?>
	                                </div>
	                                <?php } ?>
	                                <div class="form-group otp-form user_otp_divmodal digit-group" style="<?php echo ($this->session->userdata('facebook_user') == 'yes' || $this->session->userdata('google_user') == 'yes')?'display: none;': ''; ?>">
	                                    
	                                    <input type="text" id="digit-1" name="digit-1" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-next="digit-2" class="smsCode" required />
	                                    
	                                    <input type="text" id="digit-2" name="digit-2" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-next="digit-3" data-previous="digit-1" class="smsCode" required />
	                                    
	                                    <input type="text" id="digit-3" name="digit-3" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-next="digit-4" data-previous="digit-2" class="smsCode" required />
	                                    
	                                    <input type="text" id="digit-4" name="digit-4" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-next="digit-5" data-previous="digit-3" class="smsCode" required />
	                                    
	                                    <input type="text" id="digit-5" name="digit-5" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-next="digit-6" data-previous="digit-4" class="smsCode" required />
	                                    
	                                    <input type="text" id="digit-6" name="digit-6" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}" data-previous="digit-5" class="smsCode" required />
	                                    <input type="hidden" name="user_otp" id="user_otp">
	                                    <div class="resend_btn_div">
                                            <span><?php echo $this->lang->line('having_trouble') ?></span>
                                            <button type="button" name="verifyotp_resend" id="verifyotp_resend" value="Submit_resend" class="btn resend_otp red"><?php echo $this->lang->line('resend_otp') ?></button>
                                        </div>
                                        <div class="otp_error_div"></div>
	                                </div>
	                                <div class="form-group phn_num_container mobile_number_divmodal" style="<?php echo ($this->session->userdata('facebook_user') == 'yes' || $this->session->userdata('google_user') == 'yes')?'display: inline-block;':'display: none;'; ?>" >
	                                    <input type="hidden" name="phone_code_otp" id="phone_code_otp" class="form-control" value="">
	                                    <input type="hidden" name="is_forgot_pwd" id="is_forgot_pwd" class="form-control" value="0">
	                                    <input type="hidden" name="add_number_from_checkout" id="add_number_from_checkout" class="form-control" value="0">
	                                    <input type="hidden" name="verify_guest_number_from_checkout" id="verify_guest_number_from_checkout" class="form-control" value="0">
	                                    <input type="hidden" name="forgot_pwd_userid" id="forgot_pwd_userid" class="form-control" value="0">
	                                    <input type="tel" name="mobile_number" id="mobile_number" class="form-control" placeholder="" value="<?php if(isset($_POST["mobile_number"])){ echo $_POST["mobile_number"]; } ?>" >
	                                    <div id="start_with_zero" style="display: none; color: red;"></div>
	                                </div>
	                                <div class="action-button">                                    
	                                    <button type="submit" name="verifyotp_submit_page" id="verifyotp_submit_page" value="<?php echo ($this->session->userdata('facebook_user') == 'yes' || $this->session->userdata('google_user') == 'yes')?'resend_submit':'Submit'; ?>" class="btn red"><?php echo $this->lang->line('continue') ?></button>
	                                </div>
	                            </form>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
        </div>
    </div>
</div>
<!-- verify OTP modal :: end -->
<!-- forgot password modal :: start -->
<div class="modal modal-main std-modal" tabindex="-1" role="dialog" id="forgot-pass-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('forgot_password') ?></h5>
               	<button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal" aria-label="Close"> <i class="iicon-icon-23"></i> </button>
            </div>
            <div class="modal-body">
	            <div class="row align-items-center">
	                <div class="col-md-5 col-sm-12">
	                    <div class="text-center forgot-image"> <img src="<?php echo base_url();?>assets/front/images/fp-popup-image.png" alt="Forgot Password Image"> </div>
	                </div>
	                <div class="col-md-7 col-sm-12">
	                    <div class="modal-form">
	                        <div class="alert alert-success display-no" id="forgot_success"></div>
	                        <div id="forgot_password_section">
	                            <h2 class="text-left"><?php echo $this->lang->line('enter_your_phn_no') ?></h2>
	                            <form  id="form_front_forgotpass" name="form_front_forgotpass" method="post" class="form-horizontal float-form">
	                                <div class="form-body">
		                                <div class="alert alert-danger display-no" id="forgot_error"></div>
		                                <?php if(validation_errors()){?>
			                                <div class="alert alert-danger">
			                                    <?php echo validation_errors();?>
			                                </div>
		                                <?php } ?>
		                                <div class="form-group">
		                                    <!-- <input type="email" name="email_forgot" id="email_forgot" class="form-control" placeholder=" " maxlength='50'> -->
		                                    <input type="hidden" name="phone_code_first" id="phone_code_first" class="form-control" value="">
                                            <input type="tel" name="mobile_number_first" id="mobile_number_first" class="form-control" placeholder="" value="<?php if(isset($_POST["mobile_number"])){ echo $_POST["mobile_number"]; } ?>" maxlength='12'> <div id="start_with_zero_first" style="display: none; color: red;"></div>
		                                </div>
		                                <div class="action-button">                                    
		                                    <button type="submit" name="forgot_submit_page" id="forgot_submit_page" disabled value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
		                                </div>
		                            </div>
	                            </form>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
        </div>
    </div>
</div>
<!-- forgot password modal :: end -->
<!-- change password modal :: start -->
<div class="modal modal-main std-modal" tabindex="-1" role="dialog" id="change-pass-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('change_pass') ?></h5>
                <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal" aria-label="Close"> <i class="iicon-icon-23"></i> </button>
            </div>
            <div class="modal-body">
                <div class="row align-items-center">
                    <div class="col-md-5 col-sm-12">
                        <div class="text-center change-pass-image"> <img src="<?php echo base_url();?>assets/front/images/fp-popup-image.png" alt="Change Password Image"> </div>
                    </div>
                    <div class="col-md-7 col-sm-12">
                        <div class="modal-form">
                            <div class="alert alert-success display-no" id="change_pass_success"></div>
                            <div id="change_pass_section">
                                <h2 class="text-left"><?php echo $this->lang->line('enter_pwd') ?></h2>
                                <!-- action="<?php //echo base_url().'home/forgot_password';?>" -->
                                <form id="form_front_change_pass" name="form_front_change_pass" method="post" class="form-horizontal float-form">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-no" id="change_pass_error"></div>
                                        <?php if(validation_errors()){?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors();?>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group">                                            
                                            <div>
                                                <button id="toggle-password1" type="button" class="d-none" aria-label="<?php echo $this->lang->line('password_hide_show') ?>"></button>
                                            </div>
                                            <input type="password" name="password_forgot_pwd" id="password_forgot_pwd" class="form-control" placeholder=" ">
                                            <label><?php echo $this->lang->line('password') ?></label>
                                            <input type="hidden" name="change_pass_userid" id="change_pass_userid" class="form-control" value="0">
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <button id="toggle-password2" type="button" class="d-none" aria-label="<?php echo $this->lang->line('password_hide_show') ?>"></button>
                                            </div>
                                            <input type="password" name="confirm_password_forgot_pwd" id="confirm_password_forgot_pwd" class="form-control" placeholder=" ">
                                            <label><?php echo $this->lang->line('confirm_pass') ?></label>
                                        </div>
                                        <div class="action-button">
                                            <button type="submit" name="change_pass_submit_page" id="change_pass_submit_page" value="Submit" class="btn red">
                                                <?php echo $this->lang->line('submit') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- change password modal :: end -->
<?php if($this->session->userdata('enter_otp')=='yes' && ($this->input->get('frm_page')) && $this->input->get('frm_page')=='Login') { ?>
    <script> $("#verify-otp-modal").modal('show');</script> 
<?php } elseif ($this->session->userdata('enter_otp')=='no') {?>
    <script> $("#verify-otp-modal").modal('hide');</script>
<?php } ?>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<?php if ($this->session->userdata('is_user_login') == 1 || $this->session->userdata('is_guest_checkout') == 1) { ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<!-- Stripe JavaScript library -->
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
var card = '';
var need_create_intent= need_create_intenttemp ='';
var stripe_info = '<?php echo ($stripe_info->live_publishable_key!= '1')?$stripe_info->live_publishable_key:$stripe_info->test_publishable_key; ?>';
$( "#checkout_form" ).on("submit", function( event ) {
	event.preventDefault();
	if(stripe_info!=''){
		var stripe = Stripe('<?php echo ($stripe_info->enable_live_mode == '1')?$stripe_info->live_publishable_key:$stripe_info->test_publishable_key; ?>');
		var radioValue = $("input[name='payment_option']:checked").val();
		var create_intent_stripe = $('#create_intent_stripe').val();
		if(radioValue == "stripe" && create_intent_stripe == 'yes')
		{
			if(need_create_intent=='')
			{
				var intent_data = {
					currency : '<?php echo $currency_symbol->currency_code; ?>',
					amount : <?php echo $this->session->userdata('total_price')*100; ?>,
					subtotal : $('#subtotal').val(),
					is_service_fee_applied : $('#is_service_fee_applied').val(),
					is_creditcard_fee_applied: $('#is_creditcard_fee_applied').val(),
					cart_restaurant: $('#cart_restaurant').val(),
				};
				// Disable the button until we have Stripe set up on the page
				//document.querySelector("#submit_stripe").disabled = true;
				$('#listall_card').html('');
				fetch(BASEURL+"checkout/create_intent", {
					method: "POST",
					dataType : "html",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify(intent_data)
				}).then(function(result) {
						return result.json();
				}).then(function(data) {
					var elements = stripe.elements();
					var style = {
						base: {
						color: "#32325d",
						fontFamily: 'Arial, sans-serif',
						fontSmoothing: "antialiased",
						fontSize: "16px",
							"::placeholder": {
							color: "#32325d"
							}
						},
						invalid: {
							fontFamily: 'Arial, sans-serif',
							color: "#fa755a",
							iconColor: "#fa755a"
						}
					};
					need_create_intenttemp='no';
					card = elements.create("card", { hidePostalCode: false,style: style });
					// Stripe injects an iframe into the DOM
					card.mount("#card-element");
					$('#listall_card').html(data.stripe_html);
					if(!data.stripe_html) {
						$("input[name='payment-source'][value='newcard']").attr('checked', 'checked');
					}
					if($("input[name='payment-source']:checked").val() == 'newcard' || $("input[name='payment-source']:checked").val() == undefined){
						$("#save_card_checkbox").show();
					} else {
						$("#save_card_checkbox").hide();
					}
					card.on("change", function (event) {
						//$("#new-card-radio").attr('checked', true);						
						// Disable the Pay button if there are no card details in the Element
						document.querySelector("#submit_stripe").disabled = event.empty;
						document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
					});
					card.on('focus', function(event) {
						$("#save_card_checkbox").show();
					  document.querySelector('#new-card-radio').checked = true;
					  document.querySelector("#submit_stripe").disabled = true;
					});

					//Add event listner code :: Start 
					var form = document.getElementById("form_user_details");
					form.addEventListener("submit", function(event)
					{
						event.preventDefault();
						//console.log(event); return false;

						//Code for payment with new/old card :: Start
						var radiopaymnetValue = $("input[name='payment-source']:checked").val();
						var save_card_checkbox_val = $("input[name='save_card_checkbox_val']:checked").val();
						if(radiopaymnetValue=='newcard')
						{
							//code for payment with new card							
							//Complete payment when the submit button is clicked
							payWithCard(stripe, card, data.clientSecret,data.stripecus_id,data.is_savecard,save_card_checkbox_val);
						}
						else
						{
							loading(true);
							//code for payment with save card
							var element_radio = $("input[name='payment-source']:checked") 						    
						    var card_fingerprintval = element_radio.attr("card_fingerprint");
						    var radio_paymentmethodid = element_radio.attr("paymentmethodid");

						    var savecard_intentcrt = {
								stripecus_id : data.stripecus_id,
								payment_method : radio_paymentmethodid,
								subtotal : $('#subtotal').val(),
								is_service_fee_applied : $('#is_service_fee_applied').val(),
								is_creditcard_fee_applied: $('#is_creditcard_fee_applied').val(),
								cart_restaurant: $('#cart_restaurant').val(),
							};

						    //Code for create paymentintent with save card :: Start
						    fetch(BASEURL+"checkout/create_paymentwithcard", {
								method: "POST",
								headers: {
									"Content-Type": "application/json"
								},
								//use for subtotal value :: Not in use
								body: JSON.stringify(savecard_intentcrt)
							}).then(function(pn_result) {
									return pn_result.json();
							}).then(function(pn_data) 
							{	
								if(pn_data.error) {
								    // Show error from server on payment form
								  } else if (pn_data.paymentconfirm_status =='requires_action') {
								    // Use Stripe.js to handle required card action
								    stripe.confirmCardPayment(
										pn_data.clientSecret
								    ).then(function(resulthand) {								    	
								      if (resulthand.error) {
								        // Show `result.error.message` in payment form	
								        location.reload();							        
								        loading(false);
								        /*document.querySelector(".result-message").classList.remove("hidden");
										document.querySelector("#submit_stripe").disabled = true;*/
										
								      }
								      else
								      {
								      	orderComplete(pn_data.paymentIntentid,pn_data.paymentIntentstatus);
								      }
								      
								    });
								  }else {
								    // Show success message
								    orderComplete(pn_data.paymentIntentid,pn_data.paymentIntentstatus);
								  }						
								//Complete payment when the submit button is clicked
								
							});
						    //Code for create paymentintent with save card :: End
						}						
						//Code for payment with new/old card :: End						
					});
					//Add event listner code :: End
					
					//Check flag to check the intent create or not :: Start
					if(need_create_intenttemp=='no')
					{
						need_create_intent=need_create_intenttemp;
					}//End
				});
			}
			else
			{
				$('#user_details').modal('show');
			}

			// Calls stripe.confirmCardPayment
			// If the card requires authentication Stripe shows a pop-up modal to
			// prompt the user to enter authentication details without leaving your page.
			var payWithCard = function(stripe, card, clientSecret,stripecus_id,is_savecard,save_card_checkbox_val) {
				loading(true);
				stripe
					.confirmCardPayment(clientSecret, {
						payment_method: {
							card: card
						}
					})
					.then(function(result) {
						//console.log(JSON.stringify(result));
						if (result.error) {
							// Show error to your customer
							showError(result.error.message);
						}
						else
						{
							// The payment succeeded!
							var str_paymentIntentid = result.paymentIntent.id;
							var str_paymentIntentstatus = result.paymentIntent.status;

							if(is_savecard=='yes' && (save_card_checkbox_val=='yes' && save_card_checkbox_val != undefined))
							{
								//Code for save card :: Start
								var savecard_data = {
									stripecus_id : stripecus_id,
									payment_method : result.paymentIntent.payment_method,
								};
								
								fetch(BASEURL+"checkout/save_carddetail", {
									method: "POST",
									headers: {
										"Content-Type": "application/json"
									},
									//use for subtotal value :: Not in use
									body: JSON.stringify(savecard_data)
								}).then(function(sresult) {
										return sresult.json();
								}).then(function(sdata) {
									if(sdata.error != '' && sdata.error != undefined){
										document.querySelector("#card-error").textContent = sdata.error ? sdata.error : "";
										setTimeout(function() {
											document.querySelector("#card-error").textContent = "";
											orderComplete(str_paymentIntentid,str_paymentIntentstatus);
										}, 4000);
									} else {
										// order complete code 	
										orderComplete(str_paymentIntentid,str_paymentIntentstatus);
									}
								});
								//Code for save card :: End
							}
							else
							{
								orderComplete(str_paymentIntentid,str_paymentIntentstatus);
							}
						}
					});
			};
			/* ------- UI helpers ------- */
			// Shows a success message when the payment is complete
			var orderComplete = function(paymentIntentId,paymentStatus) {
				loading(false);
				document.querySelector(".result-message").classList.remove("hidden");
				document.querySelector("#submit_stripe").disabled = true;
				var formData = new FormData($("#checkout_form")[0]);
        		formData.append('paymentIntentId', paymentIntentId);
        		formData.append('paymentStatus', paymentStatus);
        		stripeAddOrder(formData);
			};
			// Show the customer the error from Stripe if their card fails to charge
			var showError = function(errorMsgText) {
				loading(false);
				var errorMsg = document.querySelector("#card-error");
				errorMsg.textContent = errorMsgText;
				setTimeout(function() {
					errorMsg.textContent = "";
				}, 4000);
			};
			// Show a spinner on payment submission
			var loading = function(isLoading) {
				if (isLoading) {
					// Disable the button and show a spinner
					document.querySelector("#submit_stripe").disabled = true;
					document.querySelector("#spinner").classList.remove("hidden");
					document.querySelector("#button-text").classList.add("hidden");
				} else {
					document.querySelector("#submit_stripe").disabled = false;
					document.querySelector("#spinner").classList.add("hidden");
					document.querySelector("#button-text").classList.remove("hidden");
				}
			};
          	/*stripe new changes :: 2feb2021 end*/
		}
	}
});
</script>
<script type="text/javascript">
$(document).ready(function(){ 
	<?php if(empty($paymentmethods)) { ?>
		$('#submit_order').attr('disabled',true);
	<?php } ?>
	<?php if($is_out_of_stock_item_in_cart) { ?>
		$('#schedule_delivery_content').removeClass('display-no');
	<?php } ?>
	$('#is_service_fee_applied').val('<?php echo $is_service_fee_applied; ?>');
	$('#service_feeval').val('<?php echo $sales_tax->service_fee; ?>');
	$('#service_fee_typeval').val('<?php echo $sales_tax->service_fee_type; ?>');

	$('#service_taxval').val('<?php echo $sales_tax->amount; ?>');
	$('#service_tax_typeval').val('<?php echo $sales_tax->amount_type; ?>');
	//driver tip changes :: start
	var selected_tip = <?php echo $selected_tip; ?>;
	if(selected_tip>0){
		$('#tip_submit_btn').attr('disabled',false);
        $("#tip_clear_btn").attr("disabled", false);
	}
	//driver tip changes :: end
    jQuery( "#payment_option" ).prop('required',true);
	$('#signup_form').hide();
	var page = '<?php echo (isset($page) && !empty($page)) ? $page : ''; ?>'
	if(page == "login"){
		$('#login_form').show();
		$('#signup_form').hide();
	}
	if (page == "register") {
		$('#login_form').hide();
		$('#signup_form').show();
	}
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});

	//Code for hide show user detail :: Start	
	/*$('input[type=radio][name=payment_option]').change(function() {
	    if (this.value == 'CardOnline') {
	        $(".card_dtl").show();        
	    }
	    else if (this.value == 'cod') {
	       $(".card_dtl").hide();        
	    }
	});*/
	//Code for hide show user detail :: End
	$('#add_address').on('change',function(){
		$("#add_latitude").val('');
		$("#add_longitude").val('');
	});
	// google address autocomplete off 
	$('#add_address').on('focus',function(){
		$("#add_address_error").hide();
       	$(this).attr('autocomplete', 'nope');       	
    });
});
var coupon_applied = 'no';
var jscoupon_id = '';
</script>
<?php 
$coupon_array = (!empty($this->session->userdata('coupon_array')))?$this->session->userdata('coupon_array'):[];
if (!empty($coupon_array) && $this->session->userdata('coupon_applied')=='yes')	
{
	$coupon_idarr = array_column($coupon_array, 'coupon_id');
	$coupon_idstr = implode(",", $coupon_idarr);
?>
<script type="text/javascript">
	var coupon_applied = 'yes';
	var jscoupon_id = '<?php echo $this->session->userdata('coupon_id'); ?>';
</script>	
<?php }
if ($cart_details['cart_total_price']) {
?>
<script type="text/javascript">
$(document).ready(function()
{
	var choose_order = $("input[name='choose_order']:checked").val();
	var order_mode_frm_dropdown = '<?php echo ($this->session->userdata('order_mode_frm_dropdown') && $this->session->userdata('order_mode_frm_dropdown') != 'Both')?$this->session->userdata('order_mode_frm_dropdown'):''; ?>';
	if(choose_order=="pickup"){
		showPickup(<?php echo $cart_details['cart_total_price']; ?>);
	}else{
		if(order_mode_frm_dropdown != '' && order_mode_frm_dropdown == 'PickUp'){
			showPickup(<?php echo $cart_details['cart_total_price']; ?>);
		} 
		else if(order_mode_frm_dropdown != '' && order_mode_frm_dropdown == 'Delivery'){
			showDelivery(<?php echo $cart_details['cart_total_price']; ?>,coupon_applied);
		} 
		else if(choose_order!='' && choose_order!=undefined)
		{
			showDelivery(<?php echo $cart_details['cart_total_price']; ?>,coupon_applied);
		}
	}
	if(coupon_applied=='yes')
	{
		getCouponDetails('<?php echo $coupon_idstr; ?>',$('#subtotal').val(),choose_order,'no')
	}			
});
<?php if(empty($restaurant_order_mode)){ ?>
	$('#submit_order').attr('disabled',true);
<?php } ?>
</script>
<?php } ?>
<script>
var field_required ="<?php echo $this->lang->line('field_required');?>";
$("input[type='radio']").click(function(){
	var radioValue = $("input[name='payment_option']:checked").val();
	if(radioValue == "paypal") {
		// $("#paypal-button").hide();
		if($('.paypal-button').length <=0){
			paypal.Button.render({
			    // Configure environment
			    env: '<?php echo ($paypal->enable_live_mode == 1) ? 'production' : 'sandbox'; ?>',
			    client: {
			        sandbox: '<?php echo $paypal->sandbox_client_id; ?>',
			        production: '<?php echo $paypal->live_client_id; ?>'
			    },
			    // Customize button (optional)
			    locale: 'en_US',
			    style: {
			        size: 'small',
			        color: 'gold',
			        shape: 'pill',
			        label: 'paypal',
			        tagline: false,
			    },
			    onInit: function(actions){
			    	var guest_otp_verified_sessval = "<?php echo $this->session->userdata('guest_otp_verified'); ?>";
			    	paypalActions = actions;
			    	if (($('#is_guest_checkout').val()=='yes' && $('#guest_checkout_form').valid()==false) || ($('#is_agent').val() == 'yes' && $('#agent_order_form').valid() == false) || $("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
			     		paypalActions.disable();
			     	}
			     	else if($("#checkout_form").valid()==false){
			     		paypalActions.disable();
			     	} else if(guest_otp_verified_sessval == '0' && $('#is_guest_checkout').val() == 'yes') {
						paypalActions.disable();
					}
					//check schedule delivery :: start
			     	else if(!$("#schedule_delivery_content").hasClass('display-no') && $('#time_slot').val() == ''){
						paypalActions.disable();
						$('html, body').animate({
							scrollTop: $("#order_mode_content").offset().top
						});
						$('#scheduled_time_err').html(field_required);
					}
					//check schedule delivery :: End
					else {
			    		var restaurant_id = $('#cart_restaurant').val();
			    		var menu_ids = $('#menuids').val();
    					var menu_ids_arr = menu_ids.split(',');
						var scheduleddate_inp = ($('#datetimepicker1').val()) ? $('#datetimepicker1').val() : '';
						var scheduledtime_inp = ($('#slot_open_time').val()) ? $('#slot_open_time').val() : '';
						var is_scheduling_allowed = <?php echo ($sales_tax->allow_scheduled_delivery == '1') ? 1 : 0; ?>;						
						jQuery.ajax({
						type : "POST",
						dataType : "json",
						url : BASEURL+'cart/checkResStat',
						data : {'restaurant_id':restaurant_id,'menu_ids':menu_ids_arr, 'scheduleddate_inp':scheduleddate_inp, 'scheduledtime_inp':scheduledtime_inp, 'is_scheduling_allowed':is_scheduling_allowed},
						success: function(response) {
							if(response.status == 'res_unavailable') {
								// Disable the buttons
								paypalActions.disable();
								
							} else {
								// enables the buttons
								paypalActions.enable();
							}
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							alert(errorThrown);
						}
						});
			    	}
			    },
			    validate: function(actions) {
			    	var guest_otp_verified_sessval = "<?php echo $this->session->userdata('guest_otp_verified'); ?>";
					paypalActions = actions;
					if (($('#is_guest_checkout').val()=='yes' && $('#guest_checkout_form').valid()==false) || ($('#is_agent').val() == 'yes' && $('#agent_order_form').valid() == false) || $("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
			     		paypalActions.disable();
			     	} else if($("#checkout_form").valid()==false){
			     		paypalActions.disable();
			     	} else if(guest_otp_verified_sessval == '0' && $('#is_guest_checkout').val() == 'yes') {
						paypalActions.disable();
					}
					//check schedule delivery :: start
			     	else if(!$("#schedule_delivery_content").hasClass('display-no') && $('#time_slot').val() == ''){
						paypalActions.disable();
						$('html, body').animate({
							scrollTop: $("#order_mode_content").offset().top
						});
						$('#scheduled_time_err').html(field_required);
					}
					//check schedule delivery :: End
					 else{
			    		var restaurant_id = $('#cart_restaurant').val();
			    		var menu_ids = $('#menuids').val();
    					var menu_ids_arr = menu_ids.split(',');
    					var scheduleddate_inp = ($('#datetimepicker1').val()) ? $('#datetimepicker1').val() : '';
						var scheduledtime_inp = ($('#slot_open_time').val()) ? $('#slot_open_time').val() : '';
						var is_scheduling_allowed = <?php echo ($sales_tax->allow_scheduled_delivery == '1') ? 1 : 0; ?>;
						jQuery.ajax({
							type : "POST",
							dataType : "json",
							url : BASEURL+'cart/checkResStat',
							data : {'restaurant_id':restaurant_id,'menu_ids':menu_ids_arr, 'scheduleddate_inp':scheduleddate_inp, 'scheduledtime_inp':scheduledtime_inp, 'is_scheduling_allowed':is_scheduling_allowed},
							success: function(response) {
								if(response.status == 'res_unavailable') {
									// Disable the buttons
									paypalActions.disable();
									
								} else {
									// enables the buttons
									paypalActions.enable();
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								alert(errorThrown);
							}
						});
			    	}
			    },
			    onClick: function() {
			    	var guest_otp_verified_sessval = "<?php echo $this->session->userdata('guest_otp_verified'); ?>";
			    	if (($('#is_guest_checkout').val()=='yes' && $('#guest_checkout_form').valid()==false) || ($('#is_agent').val() == 'yes' && $('#agent_order_form').valid() == false) || $("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
			     		paypalActions.disable();
						$('html, body').animate({
							scrollTop: $("#ajax_checkout").offset().top
						});
					} else if($("#checkout_form").valid()==false){
			     		paypalActions.disable();
						$('html, body').animate({
							scrollTop: $("#order_mode_btn").offset().top
						});
			     	}
			     	//check schedule delivery :: start
			     	else if(!$("#schedule_delivery_content").hasClass('display-no') && $('#time_slot').val() == ''){
						paypalActions.disable();
						$('html, body').animate({
							scrollTop: $("#order_mode_content").offset().top
						});
						$('#scheduled_time_err').html(field_required);
					}
					//check schedule delivery :: End
			     	 else if(guest_otp_verified_sessval == '0' && $('#is_guest_checkout').val() == 'yes') {
						paypalActions.disable();
						//check if user mobile number is verified
						var order_user_id = 0;
						if(IS_GUEST_CHECKOUT == 1 || $('#consider_guest').val() == 'yes') {
							order_user_id = 0;
						} else if($('#consider_guest').val() == 'no' && $('#exist_user_id').val() != 0) {
							order_user_id = $('#exist_user_id').val();
						} else {
							order_user_id = (USER_ID) ? USER_ID : 0;
						}
						var guest_mobile_number = '';
						var guestphonecode = '';
						var guestfirstname = '';
						var guestlastname = '';
						var guestemail = '';
						if(($('#consider_guest').val() == 'yes' && $('#exist_user_id').val() == 0) || IS_GUEST_CHECKOUT == 1) {
							guest_mobile_number = $('#login_phone_number').val();
							guestphonecode = $('#phone_code').val();
							guestfirstname = $('#first_name').val();
							guestlastname = $('#last_name').val();
							guestemail = $('#email_inp').val();
						}
						jQuery.ajax({
							type : "POST",
							dataType : "json",
							url : BASEURL+'checkout/checkUserVerified',
							data : {'order_user_id':order_user_id, 'guest_mobile_number':guest_mobile_number, 'guestphonecode':guestphonecode, 'guestfirstname':guestfirstname, 'guestlastname':guestlastname, 'guestemail': guestemail},
							beforeSend: function(){
								$('#quotes-main-loader').show();
							},
							success: function(response) {
								$('#quotes-main-loader').hide();
								if(response.status == 0 && response.message == 'add_mobile_number') {
									$('#user_otp').val('');
									$('#digit-1').val('');
									$('#digit-2').val('');
									$('#digit-3').val('');
									$('#digit-4').val('');
									$('#digit-5').val('');
									$('#digit-6').val('');
									$('#verifyotp_success').hide();
									$('#verifyotp_submit_page').val('add_phn_no');
									$('#add_number_from_checkout').val(1);
									$("#user_otp").css("display", "none");
									$(".user_otp_divmodal").css("display", "none");
									$("#digit-1").removeAttr("required");
									$("#digit-2").removeAttr("required");
									$("#digit-3").removeAttr("required");
									$("#digit-4").removeAttr("required");
									$("#digit-5").removeAttr("required");
									$("#digit-6").removeAttr("required");
									$('#verifyotp_error').text('');
									$('#verifyotp_error').hide();
									$('#verifyotp_modaltitle').text(ADD_MOBILE_NUMBER);
									$('#enter_otp_text').text(ENTER_YOUR_MOBILE_NUMBER);
									$("#verifyotp_submit_page").css("display", "block");
									$("#mobile_number").attr("required", "true");
									$(".mobile_number_divmodal").css("display", "inline-block");
									$('#verify-otp-modal').modal('show');
								} else if(response.status == 2 && response.message == 'add_guest_mobile_number') {
									$('#user_otp').val('');
									$('#digit-1').val('');
									$('#digit-2').val('');
									$('#digit-3').val('');
									$('#digit-4').val('');
									$('#digit-5').val('');
									$('#digit-6').val('');
									$("#mobile_number").removeAttr("required");
									$("#digit-1").attr("required", "true");
									$("#digit-2").attr("required", "true");
									$("#digit-3").attr("required", "true");
									$("#digit-4").attr("required", "true");
									$("#digit-5").attr("required", "true");
									$("#digit-6").attr("required", "true");

									$('#verifyotp_submit_page').val('Submit');
									$('#verify_guest_number_from_checkout').val(1);
									$(".user_otp_divmodal").css("display", "block");
									$("#verifyotp_submit_page").css("display", "block");
									$(".mobile_number_divmodal").css("display", "none");
									$('#verifyotp_error').text('');
									$('#verifyotp_error').hide();

									var set_mobile_number = (response.guestphonecode) ? '+'+response.guestphonecode+response.guest_mobile_number : '+1'+response.guest_mobile_number;
									$('#mobile_number').val('');
									$('#phone_code_otp').val('');
									// $('#login_phone_number').val(set_mobile_number);
									// $('#phone_code').val(response.guestphonecode);
									$("#login_phone_number").attr("readonly", "true");
									$('#verify-otp-modal').modal('show');
								} else if(response.status == 1) {
									// enables the buttons
									paypalActions.enable();
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								alert(errorThrown);
							}
						});
					} else {
			    		var restaurant_id = $('#cart_restaurant').val();
			    		var menu_ids = $('#menuids').val();
    					var menu_ids_arr = menu_ids.split(',');
    					var scheduleddate_inp = ($('#datetimepicker1').val()) ? $('#datetimepicker1').val() : '';
						var scheduledtime_inp = ($('#slot_open_time').val()) ? $('#slot_open_time').val() : '';
						var is_scheduling_allowed = <?php echo ($sales_tax->allow_scheduled_delivery == '1') ? 1 : 0; ?>;
						jQuery.ajax({
							type : "POST",
							dataType : "json",
							url : BASEURL+'cart/checkResStat',
							data : {'restaurant_id':restaurant_id,'menu_ids':menu_ids_arr, 'scheduleddate_inp':scheduleddate_inp, 'scheduledtime_inp':scheduledtime_inp, 'is_scheduling_allowed':is_scheduling_allowed},
							beforeSend: function(){
								$('#quotes-main-loader').show();
							},
							success: function(response) {
								$('#quotes-main-loader').hide();
								if(response.status == 'res_unavailable') {
									var box = bootbox.alert({
										message: response.show_message,
										buttons: {
											ok: {
												label: response.oktxt,
											}
										},
									});
									setTimeout(function() {
										box.modal('hide');
									}, 10000);

									// Disable the buttons
									paypalActions.disable();
									
								} else {
									// enables the buttons
									paypalActions.enable();
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								alert(errorThrown);
							}
						});
			    	}
			    },
			    // Set up a payment
			    payment: function (data, actions) {
			    	var total_pricetemp = <?php echo ($this->session->userdata('total_price'))?$this->session->userdata('total_price'):0; ?>;
			    	var total_price = (new_total_price && new_total_price>0)?new_total_price:total_pricetemp;			    	
					return actions.payment.create({
			            transactions: [{
			                amount: {
			                    total: total_price,
			                    currency: '<?php echo $currency_symbol->currency_code; ?>'
			                }
			            }]
			      	});
			    },
			    // Execute the payment
			    onAuthorize: function (data, actions) {
			        return actions.payment.execute()
			        .then(function () {
						jQuery.ajax({
							type : "POST",
							dataType: 'json',
							url : BASEURL+"checkout/process?paymentID="+data.paymentID+"&token="+data.paymentToken+"&payerID="+data.payerID+"&pid=<?php echo $item_id; ?>",
							cache: false, 
							processData: false,
							contentType: false,
							beforeSend: function(){
								$('#quotes-main-loader').show();
							},   
							success: function(response) {								
								if(response.transaction_id && response.transaction_id != ''){
									var formData = new FormData($("#checkout_form")[0]);
		        					formData.append('paypal_transaction_id', response.transaction_id);
		        					stripeAddOrder(formData);
								}else{
									var errorMsg = document.querySelector("#card-error");
									errorMsg.textContent = errorMsgText;
									setTimeout(function() {
										errorMsg.textContent = "";
									}, 4000);
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {           
								alert(errorThrown);
							}
					    });
			            // Redirect to the payment process page
			            // window.location = BASEURL+"checkout/process?paymentID="+data.paymentID+"&token="+data.paymentToken+"&payerID="+data.payerID+"&pid=<?php echo $item_id; ?>";
			        });
			    }
			}, '#paypal-button');
		}
		
	} else {
		$("#paypal-button").empty();
	}
});

//changes
$('.payment_option').click(function()
{
	var radioValue = $("input[name='payment_option']:checked").val();
	if(radioValue == "stripe")
	{
		$('#stripe_cod_btn').show();
		$('#blankmsg').html('');
	}else if(radioValue == "paypal")
	{
		if(!$('#submit_order').is(':disabled'))
		{
			$('#stripe_cod_btn').hide();
			$('#blankmsg').html('');
		}
		else{
			$("#paypal-button").empty();
		}		
	}else{
		$('#stripe_cod_btn').show();
		$('#blankmsg').html('');
	}
	chkPaymentOptions();
});
/*$('#payment_option2').click(function(){
	$('#stripe_cod_btn').show();
	$('#blankmsg').html('');
});
$('#payment_option3').click(function(){
	$('#stripe_cod_btn').show();
	$('#blankmsg').html('');
});*/

$('#checkout_form').submit(function(){
	<?php if(empty($paymentmethods)) { ?>
		$('#submit_order').attr('disabled',true);
	<?php } else { ?>
		if(!$('.payment_option').is(':checked')){
			$('#blankmsg').html(field_required);
		}
	<?php } ?>
	if(($("input[name='schedule_order']:checked").val() == 'yes' || $("input[name='schedule_order'][type='hidden']").val() == 'yes') && $('#datetimepicker1').val() == '') {
		$('#scheduled_date_err').html(field_required);
	} else if(($("input[name='schedule_order']:checked").val() == 'yes' || $("input[name='schedule_order'][type='hidden']").val() == 'yes') && $('#datetimepicker1').val() != '') {
		$('#scheduled_date_err').html('');
	}
	if(($("input[name='schedule_order']:checked").val() == 'yes' || $("input[name='schedule_order'][type='hidden']").val() == 'yes') && $('#time_slot').find('option:selected').val() == '') {
		$('#scheduled_time_err').html(field_required);
	} else if(($("input[name='schedule_order']:checked").val() == 'yes' || $("input[name='schedule_order'][type='hidden']").val() == 'yes') && $('#time_slot').find('option:selected').val() != '') {
		$('#scheduled_time_err').html('');
	}
});
//changes

$('input.QtyNumberval').on('input', function() {		
	    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
	});

/*$("#all_coupon_btn").click(function()
{
	showDelivery(<?php //echo $cart_details['cart_total_price']; ?>,'no');
    $('#coupon_modal').modal('show');
});*/
</script>
<?php if ($this->session->userdata('is_user_login') != 1 && $this->session->userdata('is_guest_checkout') != 1) { ?>
<script type="text/javascript">
	var password_plain = "<?php echo $this->lang->line('cookie_policy');?>";
	var password_plain1 = "<?php echo $this->lang->line('cookie_policytxt');?>";
	document.querySelector("[type='password']").classList.add("input-password");document.getElementById("toggle-password").classList.remove("d-none");const passwordInput=document.querySelector("[type='password']");const togglePasswordButton=document.getElementById("toggle-password");togglePasswordButton.addEventListener("click",togglePassword);function togglePassword(){if(passwordInput.type==="password"){passwordInput.type="text";togglePasswordButton.setAttribute("aria-label","Hide password.")}else{passwordInput.type="password";togglePasswordButton.setAttribute("aria-label",password_plain+" "+password_plain1)}
	this.classList.toggle('close-eye');
}
</script>
<?php } ?>
<script type="text/javascript">
$('#order-confirmation').on('hidden.bs.modal', function () {
	<?php if($this->session->userdata('is_guest_checkout') != 1) { ?>
		window.location.href = BASEURL+"checkout";
	<?php } else { ?>
		window.location.href = BASEURL+"checkout/checkout_as_guest";
	<?php } ?>
});
$('#menuDetailModal').on('hidden.bs.modal', function () {
	<?php if($this->session->userdata('is_guest_checkout') != 1) { ?>
		window.location.href = BASEURL+"checkout";
	<?php } else { ?>
		window.location.href = BASEURL+"checkout/checkout_as_guest";
	<?php } ?>
});
$('#addonsMenuDetailModal').on('hidden.bs.modal', function () {
	<?php if($this->session->userdata('is_guest_checkout') != 1) { ?>
		window.location.href = BASEURL+"checkout";
	<?php } else { ?>
		window.location.href = BASEURL+"checkout/checkout_as_guest";
	<?php } ?>
});
</script>
<script type="text/javascript">
//intl-tel-input plugin
var onedit_iso = '';
<?php if($this->session->userdata('phone_codeval')) {
    $onedit_iso = $this->common_model->getIsobyPhnCode($this->session->userdata('phone_codeval')); ?>
    onedit_iso = '<?php echo $onedit_iso; ?>'; //saved in session
<?php }
if(isset($adminCook['phone_code']) && $this->session->userdata('is_user_login') != 1 && $this->session->userdata('is_guest_checkout') != 1) { // for remember me
    $onedit_iso = $this->common_model->getIsobyPhnCode($adminCook['phone_code']); ?>
    onedit_iso = '<?php echo $onedit_iso; ?>';
<?php }
$iso = $this->common_model->country_iso_for_dropdown();
$default_iso = $this->common_model->getDefaultIso(); ?>

var country_iso = <?php echo json_encode($iso); ?>; //all active countries
var default_iso = <?php echo json_encode($default_iso); ?>; //default country
default_iso = (default_iso)?default_iso:'';
var initial_preferred_iso = (onedit_iso)?onedit_iso:default_iso;

<?php if ($this->session->userdata('is_user_login') != 1 || ($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') == 'Agent')) { ?>
	//phone number login form :: start
	// Initialize the intl-tel-input plugin
	const phoneInputField = document.querySelector("#login_phone_number");
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
	$(document).on('input','#login_phone_number',function(){
	    event.preventDefault();
	    var phoneNumber = phoneInput.getNumber();
	    if (phoneInput.isValidNumber()) {
	        var countryData = phoneInput.getSelectedCountryData();
	        var countryCode = countryData.dialCode;
	        $('#phone_code').val(countryCode);
	        phoneNumber = phoneNumber.replace('+'+countryCode,'');
	        $('#login_phone_number').val(phoneNumber);
			<?php if($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') == 'Agent') { ?>
				checkNumberExistforAgent($('#login_phone_number').val(),'');
			<?php } ?>
		} else {
			$('#phone_code').val('');
		}
		if (event.keyCode == 13) {
	        $("#form_front_verifyotp").submit();   
	        return false;
	    }
	});
	$(document).on('focusout','#login_phone_number',function(){
	    event.preventDefault();
	    var phoneNumber = phoneInput.getNumber();
	    if (phoneInput.isValidNumber()) {
	        var countryData = phoneInput.getSelectedCountryData();
	        var countryCode = countryData.dialCode;
	        $('#phone_code').val(countryCode);
	        phoneNumber = phoneNumber.replace('+'+countryCode,'');
	        $('#login_phone_number').val(phoneNumber);
			<?php if($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') == 'Agent') { ?>
				checkNumberExistforAgent($('#login_phone_number').val(),'');
			<?php } ?>
		} else {
			$('#phone_code').val('');
		}
	});
	phoneInputField.addEventListener("close:countrydropdown",function() {
	    var phoneNumber = phoneInput.getNumber();
	    if (phoneInput.isValidNumber()) {
	        var countryData = phoneInput.getSelectedCountryData();
	        var countryCode = countryData.dialCode;
	        $('#phone_code').val(countryCode);
	        phoneNumber = phoneNumber.replace('+'+countryCode,'');
	        $('#login_phone_number').val(phoneNumber);
			<?php if($this->session->userdata('is_user_login') == 1 && $this->session->userdata('UserType') == 'Agent') { ?>
				checkNumberExistforAgent($('#login_phone_number').val(),'');
			<?php } ?>
		} else {
			$('#phone_code').val('');
		}
	});
	//phone number login form :: end
<?php } ?>
//OTP modal intel plugin on number :: start
// Initialize the intl-tel-input plugin
const phoneInputFieldOTP = document.querySelector("#mobile_number");
const phoneInputOTP = window.intlTelInput(phoneInputFieldOTP, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    autoPlaceholder:"polite",
    formatOnDisplay:false,
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js',
        //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
});
$(document).on('input','#mobile_number',function(){
    event.preventDefault();
    var mobileNumber = phoneInputOTP.getNumber();
    if (phoneInputOTP.isValidNumber()) {
        $("#start_with_zero").css("display", "none");
        //$("#verifyotp_submit_page").prop('disabled', false);
        $("button[value='resend_submit']").prop('disabled', false);
        var countryDataOTP = phoneInputOTP.getSelectedCountryData();
        var countryCodeOTP = countryDataOTP.dialCode;
        $('#phone_code_otp').val(countryCodeOTP);
        mobileNumber = mobileNumber.replace('+'+countryCodeOTP,'');
        $('#mobile_number').val(mobileNumber);
    } else {
        $('#start_with_zero').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero").css("display", "block");
         //$("#verifyotp_submit_page").prop('disabled', true);
         $("button[value='resend_submit']").prop('disabled', true);
    }
});
$(document).on('focusout','#mobile_number',function(){
    event.preventDefault();
    var mobileNumber = phoneInputOTP.getNumber();
    if (phoneInputOTP.isValidNumber()) {
        $("#start_with_zero").css("display", "none");
        //$("#verifyotp_submit_page").prop('disabled', false);
        $("button[value='resend_submit']").prop('disabled', false);
        var countryDataOTP = phoneInputOTP.getSelectedCountryData();
        var countryCodeOTP = countryDataOTP.dialCode;
        $('#phone_code_otp').val(countryCodeOTP);
        mobileNumber = mobileNumber.replace('+'+countryCodeOTP,'');
        $('#mobile_number').val(mobileNumber);
    } else {
        $('#start_with_zero').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero").css("display", "block");
         //$("#verifyotp_submit_page").prop('disabled', true);
         $("button[value='resend_submit']").prop('disabled', true);
    }
});
phoneInputFieldOTP.addEventListener("close:countrydropdown",function() {
    var mobileNumber = phoneInputOTP.getNumber();
    if (phoneInputOTP.isValidNumber()) {
        $("#start_with_zero").css("display", "none");
        //$("#verifyotp_submit_page").prop('disabled', false);
        $("button[value='resend_submit']").prop('disabled', false);
        var countryDataOTP = phoneInputOTP.getSelectedCountryData();
        var countryCodeOTP = countryDataOTP.dialCode;
        $('#phone_code_otp').val(countryCodeOTP);
        mobileNumber = mobileNumber.replace('+'+countryCodeOTP,'');
        $('#mobile_number').val(mobileNumber);
    } else {
        $('#start_with_zero').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero").css("display", "block");
         //$("#verifyotp_submit_page").prop('disabled', true);
        $("button[value='resend_submit']").prop('disabled', true);
    }
});
//OTP modal intel plugin on number :: end

//#########################################
// Initialize the intl-tel-input plugin
const phoneInputFieldfirst = document.querySelector("#mobile_number_first");
const phoneInputfirst = window.intlTelInput(phoneInputFieldfirst, {
    initialCountry: initial_preferred_iso,
    preferredCountries: [initial_preferred_iso],
    onlyCountries: country_iso,
    separateDialCode:true,
    formatOnDisplay:false,
    autoPlaceholder:"polite",
    utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js'
        //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
});

$(document).on('input','#mobile_number_first',function(){
    event.preventDefault();
    var phoneNumber = phoneInputfirst.getNumber();
    if (phoneInputfirst.isValidNumber()) {
        $("#start_with_zero_first").css("display", "none");
        $("#start_with_zero_first").val('');
        $("#forgot_submit_page").prop('disabled', false);
        var countryData = phoneInputfirst.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code_first').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number_first').val(phoneNumber);
    }
    else
    {
        $('#start_with_zero_first').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero_first").css("display", "block");
        $("#forgot_submit_page").prop('disabled', true);
    }

});
$(document).on('focusout','#mobile_number_first',function(){
    event.preventDefault();
    var phoneNumber = phoneInputfirst.getNumber();
    if (phoneInputfirst.isValidNumber()) {
        $("#start_with_zero_first").css("display", "none");
        $("#start_with_zero_first").val('');
        $("#forgot_submit_page").prop('disabled', false);
        var countryData = phoneInputfirst.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code_first').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number_first').val(phoneNumber);
    }
    else {
        $('#start_with_zero_first').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero_first").css("display", "block");
        $("#forgot_submit_page").prop('disabled', true);
    }
});
phoneInputFieldfirst.addEventListener("close:countrydropdown",function() {    
    var phoneNumber = phoneInputfirst.getNumber();
    if (phoneInputfirst.isValidNumber()) {
        $("#start_with_zero_first").css("display", "none");
        $("#start_with_zero_first").val('');
        $("#forgot_submit_page").prop('disabled', false);
        var countryData = phoneInputfirst.getSelectedCountryData();
        var countryCode = countryData.dialCode;
        $('#phone_code_first').val(countryCode);
        phoneNumber = phoneNumber.replace('+'+countryCode,'');
        $('#mobile_number_first').val(phoneNumber);
    }else {
        $('#start_with_zero_first').text("<?php echo $this->lang->line('enter_valid_phn'); ?>");
        $("#start_with_zero_first").css("display", "block");
        $("#forgot_submit_page").prop('disabled', true);
    }
});
$('#forgot-pass-modal').on('hidden.bs.modal', function (e) {  
  $('#mobile_number_first').val('');
  $("#start_with_zero_first").css("display", "none");
  $("#start_with_zero_first").val('');
  $("#forgot_submit_page").prop('disabled', true);
});
//#########################################

// radio btns js :: start
<?php if ($this->session->userdata('is_user_login') != 1 && $this->session->userdata('is_guest_checkout') != 1 ) { ?>	
	$("input[name=login_with]:radio").click(function(){
	  $('#form_front_login_checkout').validate().resetForm();
	  if($("input[name=login_with]:checked").val() == "phone_number" ){
	    $("#email_inp").val('');
	    $(".email_div").css("display", "none");
	    $("#email").removeAttr("checked");
	    $(".radioemail").removeClass("radio_login_checked");

	    $(".phone_number_div").css("display", "block");
	    $("#phone_number").attr("checked","checked");
	    $(".radiophn").addClass("radio_login_checked");
	    
	  }else if($("input[name=login_with]:checked").val() == "email" ){
	    $("#login_phone_number").val('');
	    $(".phone_number_div").css("display", "none");
	    $("#phone_number").removeAttr("checked");
	    $(".radiophn").removeClass("radio_login_checked");

	    $(".email_div").css("display", "block");
	    $("#email").attr("checked","checked");
	    $(".radioemail").addClass("radio_login_checked");
	  }
	});
	$(document).ready(function() {
	    var sess_login_with = '<?php echo $this->session->userdata('login_with'); ?>';
	    $('#form_front_login_checkout').validate().resetForm();
	    if(sess_login_with == 'email'){
	        $("#phone_number_inp").val('');
	        $(".phone_number_div").css("display", "none");
	        $("#phone_number").removeAttr("checked");
	        $(".radiophn").removeClass("radio_login_checked");

	        $(".email_div").css("display", "block");
	        $("#email").attr("checked","checked");
	        $(".radioemail").addClass("radio_login_checked");
	    }else {
	        $("#email_inp").val('');
	        $(".email_div").css("display", "none");
	        $("#email").removeAttr("checked");
	        $(".radioemail").removeClass("radio_login_checked");

	        $(".phone_number_div").css("display", "block");
	        $("#phone_number").attr("checked","checked");
	        $(".radiophn").addClass("radio_login_checked");
	    }
	});
<?php } ?>
// radio btns js :: end
</script>
<script type="text/javascript">
// submit verify otp form
$("#form_front_verifyotp").on("submit", function(event) { 
  event.preventDefault();
  var otp_entered = $('#digit-1').val() + $('#digit-2').val() + $('#digit-3').val() + $('#digit-4').val() + $('#digit-5').val() + $('#digit-6').val();
  $('#user_otp').val(otp_entered);
  var is_forgot_pwd = '';
  var forgot_pwd_userid = '';
  if($('#is_forgot_pwd').val() == '1'){
    is_forgot_pwd = $('#is_forgot_pwd').val();
    forgot_pwd_userid = $('#forgot_pwd_userid').val();;
  }
  var add_number_from_checkout = '';
  if($('#add_number_from_checkout').val() == '1') {
  	add_number_from_checkout = $('#add_number_from_checkout').val();
  }
  var verify_guest_number_from_checkout = '';
  if($('#verify_guest_number_from_checkout').val() == '1') {
  	verify_guest_number_from_checkout = $('#verify_guest_number_from_checkout').val();
  }
  var guestnumber = '';
  var guestphonecode = '';
  var guestfirstname = '';
  var guestlastname = '';
  var guestemail = '';
  if(($('#consider_guest').val() == 'yes' && $('#exist_user_id').val() == 0) || IS_GUEST_CHECKOUT == 1) {
	guestnumber = $('#login_phone_number').val();
	guestphonecode = $('#phone_code').val();
	guestfirstname = $('#first_name').val();
	guestlastname = $('#last_name').val();
	guestemail = $('#email_inp').val();
  }
  jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'home/verify_otp',
    data : {'user_otp':$('#user_otp').val(),'phone_code_otp': $('#phone_code_otp').val(),'mobile_number': $('#mobile_number').val(), 'verifyotp_submit_page':$('#verifyotp_submit_page').val(), 'is_forgot_pwd': is_forgot_pwd, 'forgot_pwd_userid':forgot_pwd_userid, 'add_number_from_checkout':add_number_from_checkout, 'verify_guest_number_from_checkout':verify_guest_number_from_checkout, 'guestfirstname':guestfirstname, 'guestlastname':guestlastname, 'guestemail': guestemail},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#verifyotp_error').hide();
      $('#verifyotp_success').hide();
      $('#quotes-main-loader').hide();
      if (response) {
        if (response.verifyotp_error != '') { 
            $('#verifyotp_error').html(response.verifyotp_error);
            $('#verifyotp_success').hide();
            $('#verifyotp_error').show();
            /*if(response.phn_not_exist != '1'){
                $("#verifyotp_resend").css("display", "inline-block");
            }*/
            $('#user_otp').val('');
            $('#digit-1').val('');
            $('#digit-2').val('');  
            $('#digit-3').val('');
            $('#digit-4').val('');
            $('#digit-5').val('');
            $('#digit-6').val('');
            //$("#verifyotp_submit_page").css("display", "none");
        }
        if (response.verifyotp_success != '') { 
            if(response.verifyotp_sent=='1'){ //resend otp
              $('#verifyotp_submit_page').val('Submit');
              //$("#user_otp").css("display", "block");
              $(".user_otp_divmodal").css("display", "block");
              $("#mobile_number").removeAttr("required");
              $("#digit-1").attr("required", "true");
              $("#digit-2").attr("required", "true");
              $("#digit-3").attr("required", "true");
              $("#digit-4").attr("required", "true");
              $("#digit-5").attr("required", "true");
              $("#digit-6").attr("required", "true");
              
			  $('#verifyotp_modaltitle').text("<?php echo $this->lang->line('verify_otp') ?>");
			  $('#enter_otp_text').text("<?php echo $this->lang->line('enter_otp') ?>");
              
              $("#verifyotp_submit_page").css("display", "block");
              $(".mobile_number_divmodal").css("display", "none");
              $('#mobile_number').val('');
            } else { //otp verified
              if(response.is_forgot_pwd == '1'){
                $("#verify-otp-modal").modal('hide');
                $("#change_pass_userid").val(forgot_pwd_userid);
                $("#change-pass-modal").modal('show');
                return;
              } else if(response.add_number_from_checkout == '1') {
              	$('#verify_otp_section').hide();
              	$('#verifyotp_success').html(response.verifyotp_success);
	            $('#verifyotp_error').hide();
	            $('#verifyotp_success').show();
	            window.setTimeout(function() { 
					$("#verify-otp-modal").modal('hide');
				}, 5000);
                return;
              } else if(response.verify_guest_number_from_checkout == '1') {
              	$('#login_phone_number').val(response.guest_mobile_number);
				$('#phone_code').val(response.guestphonecode);
              	var set_mobile_number = (response.guestphonecode) ? '+'+response.guestphonecode+response.guest_mobile_number : '+1'+response.guest_mobile_number;
              	phoneInput.setNumber(set_mobile_number);
				$("#login_phone_number").attr("readonly", "true");
              	$('#verify_otp_section').hide();
              	$('#verifyotp_success').html(response.verifyotp_success);
	            $('#verifyotp_error').hide();
	            $('#verifyotp_success').show();
	            window.setTimeout(function() { 
					$("#verify-otp-modal").modal('hide');
				}, 5000);
                return;
              } else {
	              $('#verify_otp_section').hide();
	              $('#verifyotp_success').html(response.verifyotp_success);
	              $('#verifyotp_success').show();
	              $("#name").removeAttr("required");
	              $("#email").removeAttr("required");
	              $("#phone_number").removeAttr("required");
	              $("#password").removeAttr("required");              
	              location.reload();
	              //$('#form_front_registration').submit();
	              window.setTimeout(function() { 
	                $('#form_front_login').submit(); 
	              }, 5000);
	              //$("#verify-otp-modal").modal('hide');
	          }
            }
            $('#verifyotp_success').html(response.verifyotp_success);
            $('#verifyotp_error').hide();
            $('#verifyotp_success').show();
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
    }
  });
});
// submit verify OTP form hidden
$('#verify-otp-modal').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=number]").val('').end();
  $('#form_front_verifyotp').validate({
  	errorPlacement: function(error, element) {
		if(element.attr("name") == "digit-1" || element.attr("name") == "digit-2" || element.attr("name") == "digit-3" || element.attr("name") == "digit-4" || element.attr("name") == "digit-5" || element.attr("name") == "digit-6"){
			error.appendTo($('.otp_error_div'));
		} else {
			error.insertAfter(element);
		}
    }
  }).resetForm();
  $('#verifyotp_success').text('');
  $('#verifyotp_error').text('');
  $('#verifyotp_success').hide();
  $('#verifyotp_error').hide();
  $('#verify_otp_section').show();
  $('#user_otp').val('');
});
$("#verifyotp_resend").click(function()
{
	$('#user_otp').val('');
    $('#digit-1').val('');
    $('#digit-2').val('');  
    $('#digit-3').val('');
    $('#digit-4').val('');
    $('#digit-5').val('');
    $('#digit-6').val('');
    $('#verifyotp_success').hide();
    if($('#add_number_from_checkout').val() == '1') {
    	$('#verifyotp_submit_page').val('add_phn_no');
        $('#add_number_from_checkout').val(1);
    } else if($('#verify_guest_number_from_checkout').val() == '1') {
    	$('#verifyotp_submit_page').val('add_guest_phn_no');
        $('#verify_guest_number_from_checkout').val(1);
    } else {
    	$('#verifyotp_submit_page').val('resend_submit');
    }
    //$("#verifyotp_resend").css("display", "none");
    $("#user_otp").css("display", "none");
    $(".user_otp_divmodal").css("display", "none");
    $("#digit-1").removeAttr("required");
    $("#digit-2").removeAttr("required");
    $("#digit-3").removeAttr("required");
    $("#digit-4").removeAttr("required");
    $("#digit-5").removeAttr("required");
    $("#digit-6").removeAttr("required");
    $('#verifyotp_error').text('');
    $('#verifyotp_error').hide();
    $('#verifyotp_modaltitle').text("<?php echo $this->lang->line('resend_otp') ?>");
    if($('#add_number_from_checkout').val() == '1') {
		$('#enter_otp_text').text(ENTER_YOUR_MOBILE_NUMBER);
	}else if(SELECTED_LANG == 'fr'){
		$('#enter_otp_text').text('Veuillez entrer votre numéro de téléphone.');
	} else if(SELECTED_LANG == 'ar'){
		$('#enter_otp_text').text('يرجى إدخال رقم الهاتف الخاص بك.');
	} else {
		$('#enter_otp_text').text('Please enter your mobile number.');
	}
	$("#verifyotp_submit_page").css("display", "block");
	$("#mobile_number").attr("required", "true");
	$(".mobile_number_divmodal").css("display", "inline-block");
});
$('.digit-group').find('input').each(function() {
    //restricting to enter more than 10 digits
    if($(this).attr('id') == 'mobile_number' ){
        /*$('input[type=number][max]:not([max=""])').on('input', function(ev) {
            var phn_no_maxlength = $(this).attr('max').length;
            var value = $(this).val();
            if (value && value.length >= phn_no_maxlength) {
              $(this).val(value.substr(0, phn_no_maxlength));
            }
        });*/
    } else {
        $(this).attr('maxlength', 1);
    }
    $(this).on('keyup', function(e) {
        e.preventDefault();
        var initial_input = $(this).val();
        $(this).val(initial_input.replace(/\D/g, ""));
        var final_input_val = $(this).val().substr(0,1);
        $(this).val(final_input_val);
        var input_ascii_code = $(this).val().charCodeAt(0);

        var parent = $($(this).parent());
        if(e.keyCode === 8 || e.keyCode === 37) {
            var prev = parent.find('input#' + $(this).data('previous'));
            if(prev.length) {
                $(prev).select();
            }
        } else if((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode === 39 || (e.keyCode >= 96 && e.keyCode <= 105) || (input_ascii_code != NaN && input_ascii_code >= 48 && input_ascii_code <= 57)) {
            var next = parent.find('input#' + $(this).data('next'));
            if(next.length) {
                $(next).select();
            } else {
                if(parent.data('autosubmit')) {                    
                    parent.submit();
                }
            }
        } else  {
            //$(this).val('');
            e.preventDefault();
            return false;
        }
    });
});
</script>
<?php if($this->session->userdata('is_guest_checkout') == 1){ ?>
<script type="text/javascript">
/*$('#guest_checkout_form #email_inp').rules( "add", {
	remote:{
		url: BASEURL+'checkout/check_user_email',
		type: "POST",
		dataType : "html",
		data: {
			user_email: function() {
				return $( "#guest_checkout_form #email_inp" ).val();
			}
		}
	}
});*/

/*$('#guest_checkout_form #login_phone_number').rules( "add", {
	// ignore: [],
	remote:{
		url: BASEURL+'checkout/check_user_phone',
		type: "POST",
		dataType : "html",
		data: {
			user_phone_number: function() {
				return $( "#guest_checkout_form #login_phone_number" ).val();
			},
			user_phone_code: function() {
				return $( "#guest_checkout_form #phone_code" ).val();
			}
		}
	}
});*/
</script>
<?php } ?>
<?php if($this->session->userdata('UserType') == 'Agent') { ?>
<script type="text/javascript">
function checkNumberExistforAgent(phn_no,email){
	var phn_code = $( "#agent_order_form #phone_code" ).val();
	if((phn_code != '' && phn_no != '') || email != ''){
		jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : BASEURL+'checkout/checkNumberExistforAgent',
			data : {'phone_no':phn_no, 'phn_code': phn_code, 'user_email': email},
			success: function(response) {
				$('#consider_guest').val(response.is_guest);
				if(response.count>0){ //user already exist
					$('#first_name').val(response.first_name);
					$('#last_name').val(response.last_name);
					$('#email_inp').val(response.email);
					$('#login_phone_number').val(response.mobile_number);
					$('#agent_order_form #exist_user_id').val(response.user_id);
					$('#first_name').attr('readonly',true);
					$('#last_name').attr('readonly',true);
					$('#email_inp').attr('readonly',true);
					//address elements
					$('#EmailExist').hide();
					if(response.address_flag==1){
						$('#your_address').empty().append(response.address_html);
						$('.your_address_inp').css('display','block'); 
					} else {
						$('#your_address').empty();
						$('.your_address_inp').css('display','none');
						$('#your_address_content').hide();
						jQuery("#add_address").prop('required',true);
						//jQuery("#landmark").prop('required',true);
						$("input[name='add_new_address']").prop("checked", true);
						$('input[name="add_new_address"]:radio:first' ).click();
					}
					$(':input[type="submit"]').prop("disabled",false);
				} else{ // guest user
					$('#first_name').val('');
					$('#last_name').val('');
					$('#email_inp').val('');
					$('#exist_user_id').val('0');
					$('#first_name').attr('readonly',false);
					$('#last_name').attr('readonly',false);
					$('#email_inp').attr('readonly',false);
					//address elements
					$('#EmailExist').hide();
					$('#your_address').empty();
					$('.your_address_inp').css('display','none');
					$('#your_address_content').hide();
					jQuery("#add_address").prop('required',true);
					//jQuery("#landmark").prop('required',true);
					$(':input[type="submit"]').prop("disabled",false);
					$("input[name='add_new_address']").prop("checked", true);
					$('input[name="add_new_address"]:radio:first' ).click();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {           
				alert(errorThrown);
			}
		});
	} else {
		$('#first_name').val('');
		$('#last_name').val('');
		$('#email_inp').val('');
		$('#exist_user_id').val('0');
		$('#first_name').attr('readonly',false);
		$('#last_name').attr('readonly',false);
		$('#email_inp').attr('readonly',false);
		//address elements
		$('#EmailExist').hide();
		$('#your_address').empty();
		$('.your_address_inp').css('display','none');
		$('#your_address_content').hide();
		jQuery("#add_address").prop('required',true);
		//jQuery("#landmark").prop('required',true);
		$(':input[type="submit"]').prop("disabled",false);
		$("input[name='add_new_address']").prop("checked", true);
		$('input[name="add_new_address"]:radio:first' ).click();
	}
}
</script>
<?php } ?>
<script type="text/javascript">
//change password :: start
document.querySelector("#password_forgot_pwd").classList.add("input-password");
document.getElementById("toggle-password1").classList.remove("d-none");
const passwordInput1=document.querySelector("#password_forgot_pwd");
const togglePasswordButton1=document.getElementById("toggle-password1");
togglePasswordButton1.addEventListener("click",togglePassword1);
function togglePassword1(){
    if(passwordInput1.type==="password"){
        passwordInput1.type="text";
        togglePasswordButton1.setAttribute("aria-label","Hide password.")
    } else {
        passwordInput1.type="password";
        togglePasswordButton1.setAttribute("aria-label","Show password as plain text. "+"Warning: this will display your password on the screen.")
    }
}
document.querySelector("#confirm_password_forgot_pwd").classList.add("input-password");
document.getElementById("toggle-password2").classList.remove("d-none");
const passwordInput2=document.querySelector("#confirm_password_forgot_pwd");
const togglePasswordButton2=document.getElementById("toggle-password2");
togglePasswordButton2.addEventListener("click",togglePassword2);
function togglePassword2(){
    if(passwordInput2.type==="password"){
        passwordInput2.type="text";
        togglePasswordButton2.setAttribute("aria-label","Hide password.")
    } else {
        passwordInput2.type="password";
        togglePasswordButton2.setAttribute("aria-label","Show password as plain text. "+"Warning: this will display your password on the screen.")
    }
}
//change password
$("#form_front_change_pass").on("submit", function(event) { 
  event.preventDefault();
  if($('#form_front_change_pass').valid()){
      jQuery.ajax({
        type : "POST",
        dataType :"json",
        url : BASEURL+'home/change_password',
        data : {'password':$('#password_forgot_pwd').val(),'confirm_password': $('#confirm_password_forgot_pwd').val(), 'change_pass_submit_page': $('#change_pass_submit_page').val(), 'change_pass_userid': $("#change_pass_userid").val()},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            $('#quotes-main-loader').hide();
            $('#change_pass_section').hide();
            $('#change_pass_success').text(response.change_pass_success);
            $('#change_pass_success').show();
            setTimeout(function(){
            	location.reload();
                $("#change-pass-modal").modal('hide');
            }, 1000);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
        }
      });
    }
});
//change password :: end
// agent email exist check
function checkEmail(email)
{
  var num = $('#login_phone_number').val();
  $.ajax({
    type: "POST",
    url: BASEURL+'checkout/checkEmailExist',
    data: {
		user_email: function() {
			return $( "#agent_order_form #email_inp" ).val();
		}
	},
    cache: false,
    success: function(html) {
      if(html > 0 && num!=''){
        $('#EmailExist').show();
        $('#EmailExist').html("<?php echo $this->lang->line('alredy_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}

//Code for save card :: Start

function togglecardbutton(radionvalue)
{
	if(radionvalue == "newcard")
	{
		$("#submit_stripe").prop("disabled",true);
		$("#save_card_checkbox").show();
	}
	else
	{
		card.clear();
		$("#submit_stripe").prop("disabled",false);
		$("#save_card_checkbox").hide();
	}	
}
//Code for remove card from stripe :: Start
function removeStripeCard(PaymentMethodid,stripecus_id)
{
	$('#quotes-main-loader').show();
	jQuery.ajax({
		type : "POST",
		dataType : 'json',
		url : BASEURL+'checkout/removeStripeCard',
		data : {'PaymentMethodid':PaymentMethodid,'stripecus_id':stripecus_id},
		success: function(response) {			
			if(response.is_delete=='yes')
			{
				$('#listall_card').html(response.stripe_html);
			}
			$('#quotes-main-loader').hide();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
}
//End
//Code for save card :: End
</script>
<script type="text/javascript">
//scheduled order changes :: start 
$(function () {
	var enabledDates = <?php echo ($enabled_dates) ? json_encode($enabled_dates) : array(); ?>;
	var defaultDate = '<?php echo ($enabled_dates) ? $enabled_dates[0] : ''; ?>';
	var datepicker_format = "<?php echo datepicker_format_front; ?>";

	$('#datetimepicker1').datetimepicker({ 
		ignoreReadonly: true,
		useCurrent: false,
		defaultDate:defaultDate,
		enabledDates: enabledDates,
		format: datepicker_format
	}).on('dp.hide', function(e){ 
		//get time slot
		var scheduleddate = $('#datetimepicker1').val();
		if(scheduleddate != '') {
			$('#scheduled_date_err').html('');
		}
		var restaurant_id = $('#cart_restaurant').val();
		if(restaurant_id && scheduleddate) {
			jQuery.ajax({
				type : "POST",
				dataType :"html",
				url : BASEURL+'checkout/getTimeSlotForSelectedDate',
				data : {'scheduled_date':scheduleddate, 'restaurant_id':restaurant_id },
				beforeSend: function(){
					$('#quotes-main-loader').show();
				},
				success: function(response) { 
					if(response == 'not_available') {
						//res_closed_err
						$('#time_slot').empty();
						$('.res_closed_err').text("<?php echo $this->lang->line('restaurant_closed'); ?>");
						$(".res_closed_err").css("display", "block");
						$("#submit_order").attr("disabled", true);
					} else {
						$(".res_closed_err").css("display", "none");
						$('.res_closed_err').text("<?php echo $this->lang->line('restaurant_closed'); ?>");
						$('#time_slot').empty().append(response);
						$("#submit_order").attr("disabled", false);
					}
					$('#quotes-main-loader').hide();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {           
					alert(errorThrown);
				}
			});
		}
	});
});
$(document).on("change", "input[name='schedule_order']", function () {
	if($("input[name='schedule_order']").is(':checked') && $("input[name='schedule_order']:checked").val() == 'yes') {
		$('#schedule_delivery_content').removeClass('display-no');
	} else {
		$('#schedule_delivery_content').addClass('display-no');
	}
});
function addSlot() {
	var element = $('#time_slot').find('option:selected');
	var slot_open_time = element.attr("slot_open_time");
	var slot_close_time = element.attr("slot_close_time");
	$('#slot_open_time').val(slot_open_time);
	$('#slot_close_time').val(slot_close_time);
	if($('#time_slot').find('option:selected').val() != '') {
		$('#scheduled_time_err').html('');
	}
	//check restaurant available
	var restaurant_id = $('#cart_restaurant').val();
	var scheduleddate = $('#datetimepicker1').val();
	if(scheduleddate != '') {
		$('#scheduled_date_err').html('');
	}
	var scheduledtime = $('#slot_open_time').val();
	if(restaurant_id && scheduleddate && scheduledtime) {
		jQuery.ajax({
			type : "POST",
			dataType :"html",
			url : BASEURL+'checkout/checkRestaurantAvailable',
			data : {'scheduled_date':scheduleddate,'scheduled_time':scheduledtime, 'restaurant_id':restaurant_id },
			beforeSend: function(){
				$('#quotes-main-loader').show();
			},
			success: function(response) { 
				$('#quotes-main-loader').hide();
				if(response == 'not_available') {
					$("#submit_order").attr("disabled", true);
					var scheduling_notallowed = bootbox.alert({
						message: "<?php echo $this->lang->line('restaurant_is_not_available'); ?>",
						buttons: {
							ok: {
								label: OK_TEXT,
							}
						},
					});
					$('html, body').animate({
						scrollTop: $("#order_mode_btn").offset().top
					}, 2000);
					setTimeout(function() {
						scheduling_notallowed.modal('hide');
					}, 10000);
				} else if(response == 'past_date') {
					$("#submit_order").attr("disabled", true);
					var scheduling_notallowed = bootbox.alert({
						message: "<?php echo $this->lang->line('past_datetime_notallowed'); ?>",
						buttons: {
							ok: {
								label: OK_TEXT,
							}
						},
					});
					$('html, body').animate({
						scrollTop: $("#order_mode_btn").offset().top
					}, 2000);
					setTimeout(function() {
						scheduling_notallowed.modal('hide');
					}, 10000);
				} else if(response == 'available') {
					$("#submit_order").attr("disabled", false);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {           
				alert(errorThrown);
			}
		});
	}
}
//scheduled order changes :: end
</script>
<?php $this->load->view('footer'); ?>