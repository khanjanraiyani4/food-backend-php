    <?php defined('BASEPATH') or exit('No direct script access allowed');?>
    <?php $this->load->view('header'); 
    $stripe_info = stripe_details(); ?>
    <style type="text/css">
        .noti{
            display: flex;
            padding-bottom: 10px;
            border-bottom: none;
        }
    </style>
    <?php 
    require APPPATH . 'libraries/PaypalExpress.php';
    $paypal_obj = new PaypalExpress;
    $paypal = $paypal_obj->paypal_details();    

    //get System Option Data
    $this->db->select('OptionValue');
    $currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
    $currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue); 
    $currency_symboltemp = $currency_symbol;
    $currency_symbol = $currency_symbol->currency_symbol;    
    $this->db->select('OptionValue');
    $enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
    $show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0;
    ?> 
    <!-- Embed the intl-tel-input plugin :: start -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.css">
    <script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/intl_tel_input/intlTelInput.min.js"></script>
    <!-- Embed the intl-tel-input plugin :: end -->
    <link href="<?php echo base_url();?>assets/admin/layout/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/front/css/stripe/stripe_modal.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <section class="inner-pages-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="heading-title">
                        <h2><?php echo $this->lang->line('my_profile') ?></h2>
                    </div>
                </div>
                <div class="col-lg-12">               
                <?php 
                if(isset($_SESSION['myProfileMSG']))
                { ?>
                    <div class="alert alert-success">
                         <?php echo $_SESSION['myProfileMSG'];
                            unset($_SESSION['myProfileMSG']);
                         ?>
                    </div>
                <?php } ?>                
                <?php 
                if(isset($_SESSION['success_MSG']))
                { ?>
                    <div class="alert alert-success">
                         <?php echo $_SESSION['success_MSG'];
                            unset($_SESSION['success_MSG']);
                         ?>
                    </div>
                <?php } ?>                
                <?php 
                if(isset($_SESSION['myProfileMSGerror']))
                { ?>
                    <div class="alert alert-danger">
                         <?php echo $_SESSION['myProfileMSGerror'];
                            unset($_SESSION['myProfileMSGerror']);
                         ?>
                    </div>
                <?php } ?>
                </div>
                <div class="col-lg-12">
                    <div class="my-profile-head">                        
                        <div class="my-profile-detail">
                            <div class="my-profile-info">
                                <h3><?php echo $profile->first_name . ' ' . $profile->last_name; ?></h3>
                                <?php if (!empty($addresses)) {
                                    foreach ($addresses as $key => $value) {
                                        if ($value->is_main == 1) {?>
                                            <p><i class="iicon-icon-20"></i><?php echo $value->address; ?></p> 
                                            <?php //echo $value->address . ', ' . $value->city . ', ' . $value->zipcode; ?>
                                        <?php break;}
                                    }
                                }?>
                                <?php if($profile->mobile_number){ ?>
                                    <p class="rtl-num-cod"><i class="iicon-icon-35"></i> <?php echo '+'.$profile->phone_code.$profile->mobile_number; ?></p>
                                <?php } ?>
                                <?php if($this->session->userdata('UserType') != 'Agent' || $this->session->userdata('UserType') == '') { ?>
                                    <!-- earning points changes start -->
                                    <?php if(empty($profile->wallet)) {
                                        $profile->wallet = 0;
                                    } ?>
                                    <p><i class="wallet_icon"></i><?php echo $this->lang->line('wallet_balance'); echo ': '; echo currency_symboldisplay($profile->wallet,$currency_symbol); ?></p>
                                    <!-- earning points changes end -->
                                <?php } ?>
                            </div>
                            <div class="edit-pro-btn">
                                <button class="btn" data-toggle="modal" class="edit-profile" data-target="#edit-profile"><?php echo $this->lang->line('edit_profile') ?></button>
                                <?php if($this->session->userdata('UserType') != 'Agent' || $this->session->userdata('UserType') == '') { ?>
                                    <button class="btn delete_a" data-toggle="modal" onclick="showDeleteAcc();"><?php echo $this->lang->line('delete_acc') ?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="profile_page_content">
                <div class="col-xl-3 col-lg-4">
                    <div class="sidebar-menu-main">
                        <div class="sidebar-menu">
                            <div class="ordering-title">
                                <h6><?php echo $this->lang->line('ordering') ?></h6>
                            </div>
                           
                            <ul id="myTab" class="nav nav-tabs">
                                <li id="tab_order_history" class="tabs <?php echo ($selected_tab == '')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#order_history" data-toggle="tab"><?php echo $this->lang->line('order_history') ?></a></li>
                                <?php if($this->session->userdata('UserType') != 'Agent' || $this->session->userdata('UserType') == '') { ?>
                                <li id="tab_bookings" class="tabs <?php echo ($selected_tab == 'bookings')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#bookings" data-toggle="tab"><?php echo $this->lang->line('my_bookings') ?></a></li> 
                                <li id="tab_table_bookings" class="tabs <?php echo ($selected_tab == 'table_bookings')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#table_bookings" data-toggle="tab"><?php echo $this->lang->line('table_bookings') ?></a></li>
                                <li id="tab_wallet_history" class="tabs <?php echo ($selected_tab == 'wallet_history')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#wallet_history" data-toggle="tab"><?php echo $this->lang->line('wallet_history') ?></a></li>
                                <li id="tab_addresses" class="tabs <?php echo ($selected_tab == 'addresses')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#addresses" data-toggle="tab"><?php echo $this->lang->line('my_addresses') ?></a></li>
                                <li id="tab_notifications" class="tabs <?php echo ($selected_tab == 'notifications')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#notifications" data-toggle="tab"><?php echo $this->lang->line('my_notifications') ?></a></li>                                
                                <li id="tab_payment_card" class="tabs <?php echo ($selected_tab == 'payment_card')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#payment_card" data-toggle="tab"><?php echo $this->lang->line('card_detail') ?></a></li>
                                <?php } ?>
                                <li id="tab_bookmark" class="tabs <?php echo ($selected_tab == 'bookmark')?'active':'';?>" onclick="addActiveClass(this.id)"><a href="#bookmarks" data-toggle="tab"><?php echo $this->lang->line('my_bookmarks'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade <?php echo ($selected_tab == "")?"in active show":"";?>" id="order_history">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('order_history') ?></h5>
                                    <ul class="nav nav-tabs" role="tablist">
                                      <li class="nav-item">
                                        <a href="#current-orders" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('current_orders') ?></a>
                                      </li>
                                      <li class="nav-item">
                                        <a href="#past-orders" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('past_orders') ?></a>
                                      </li>
                                    </ul>
                                </div>                                
                                <?php 
                                if(isset($_SESSION['review_added']))
                                { ?>
                                    <div class="alert alert-success" id="review_success">
                                         <?php echo $_SESSION['review_added'];
                                            unset($_SESSION['review_added']);
                                         ?>
                                    </div>
                                <?php } ?>
                                <div class="profile-content-main">
                                    <div class="tab-content">
                                        <div id="past-orders" class="tab-pane">
                                            <?php if (!empty($past_orders)) { ?>
                                                <div class="row orders-box-row">
                                                    <?php if (!empty($past_orders)) {
                                                        foreach ($past_orders as $key => $value) {
                                                                $past_order_drivertip = 0;
                                                                $subtotal = 0;
                                                                $delivery_charges = 0;
                                                                $total = 0;
                                                                $coupon_amount = 0;
                                                                if (!empty($value['price'])) {
                                                                    foreach ($value['price'] as $pkey => $pvalue) {
                                                                        if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Sub Total") {
                                                                            $subtotal = $pvalue['value'];
                                                                        }
                                                                        if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Delivery Charge") {
                                                                            $delivery_charges = $pvalue['value'];
                                                                        }
                                                                        if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Coupon Amount") {
                                                                            $coupon_amount = $pvalue['value'];
                                                                        }
                                                                        if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Total") {
                                                                            $total = $pvalue['value'];
                                                                        }
                                                                        if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Driver Tip") {
                                                                            $past_order_drivertip = (float)$pvalue['value'];
                                                                        }
                                                                    } 
                                                                } ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">

                                                                                    <?php $image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image']) && $value['restaurant_image']!='') ?  image_url. $value['restaurant_image'] : default_icon_img;
                                                                                    $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); 
                                                                                    ?>

                                                                                    <img src="<?php echo $image; ?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?>
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['restaurant_name']; ?></a></h6>
                                                                                <?php if($this->session->userdata('UserType') == 'Agent') { ?>
                                                                                    <strong><?php echo $this->lang->line('customer') ?> : <span><?php echo $value['user_name']; ?></span></strong>
                                                                                <?php } ?>
                                                                                <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                                <strong><?php echo $this->lang->line('price') ?> : <span><?php echo currency_symboldisplay($total,$value['currency_symbol']);?></span></strong>

                                                                                <?php if($value['refund_status']!='' && $value['refund_status']!=null){ ?>
                                                                                    <p class="mt-1"><?php echo $this->lang->line('refund_status') ?> - <?php echo ucfirst($this->lang->line(str_replace(" ", "_", $value['refund_status']))) ?></p>
                                                                                <?php } ?>

                                                                                <?php if($value['scheduled_date'] && $value['slot_open_time'] && $value['slot_close_time']) { ?>
                                                                                    <p><i class="iicon-icon-18" style="color:#17161a;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('order_scheduled_for').$this->common_model->dateFormat($value['scheduled_date']).' ('.$this->common_model->timeFormat($value['slot_open_time']).' - '.$this->common_model->timeFormat($value['slot_close_time']).' )'; ?></p>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <span class="date-icon"><?php echo $this->common_model->datetimeFormat($value['order_date']); ?></span>
                                                                            <?php $order_status_txt = ($value['order_status'] == "complete") ? $this->lang->line('completed') : (($value['order_status'] == "cancel") ? $this->lang->line('cancelled') : $this->lang->line($value['order_status']));
                                                                            ?>
                                                                            <span class="relivered-icon"><?php echo ($value['payment_status']=='paid' || $value['payment_status']== NULL)?$order_status_txt:$this->lang->line($value['payment_status']); ?></span>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                                <input type="hidden" id="tip_orderid<?php echo $key ?>" name="tip_orderid<?php echo $key ?>" value="<?php echo $value['order_id']; ?>" />
                                                                                <?php if($past_order_drivertip == 0 && $value['delivery_flag'] == "delivery" && (strtolower($value['order_status'])=='delivered' || strtolower($value['order_status'])=='complete') && $value['refund_status']!='refunded') { ?>
                                                                                    <button class="btn" id="tipbtn<?php echo $value['order_id'] ?>" data-toggle="modal" onclick="tip_driver(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('tip_driver') ?></button>
                                                                                <?php } ?>
                                                                                <?php if($this->session->userdata('UserType') != 'Agent'){ ?>
                                                                                <button class="btn" data-toggle="modal" onclick="reorder_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('reorder') ?></button>
                                                                                <?php if (!empty($this->session->userdata('UserID')) && !in_array($value['order_id'], $arrReviewOrderId) && (strtolower($value['order_status'])=='delivered' || strtolower($value['order_status'])=='complete')) { ?>
                                                                                    <?php if($show_restaurant_reviews){ ?>
                                                                                <button class="btn" onclick="addReview(<?php echo $value['restaurant_id'] ?>,<?php echo $value['res_content_id'] ?>,<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('title_admin_reviewadd'); ?></button>
                                                                                <?php } } } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                            
                                                        <?php }
                                                    }?>
                                                </div>
                                                <?php if ($past_orders_count>8) { ?>
                                                <div class="display-no" id="all_past_orders" >
                                                </div>    
                                                    <div class="col-lg-12">
                                                        <div id="more_past_orders" class="load-more-btn">
                                                            <input type="hidden" name="pord_page_no" id="pord_page_no" value="2">
                                                            <button class="btn" id="pastorder_button" onclick="moreOrders('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                            <?php }
                                            else { ?>
                                                <!-- <div class="col-xl-6 col-lg-12"> -->
                                                <div class="empty_block">
                                                    <figure>
                                                        <img src="<?php echo no_res_found; ?>">
                                                    </figure>
                                                    <p class="no-found"><?php echo $this->lang->line('no_past_orders') ?></p>
                                                </div>
                                                <!-- </div> -->
                                            <?php }?>
                                        </div>
                                        <div id="current-orders" class="tab-pane show active">
                                            <?php if (!empty($in_process_orders)) { ?>
                                            <div class="row orders-box-row">
                                                <?php if (!empty($in_process_orders)) {
                                                    foreach ($in_process_orders as $key => $value) {
                                                            $subtotal = 0;
                                                            $delivery_charges = 0;
                                                            $total = 0;
                                                            $coupon_amount = 0;
                                                            if (!empty($value['price'])) {
                                                                foreach ($value['price'] as $pkey => $pvalue) {
                                                                    if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Sub Total") {
                                                                        $subtotal = $pvalue['value'];
                                                                    }
                                                                    if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Delivery Charge") {
                                                                        $delivery_charges = $pvalue['value'];
                                                                    }
                                                                    if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Coupon Amount") {
                                                                        $coupon_amount = $pvalue['value'];
                                                                    }
                                                                    if (isset($pvalue['label_key']) && $pvalue['label_key'] == "Total") {
                                                                        $total = $pvalue['value'];
                                                                    }
                                                                }
                                                            } ?>
                                                            <div class="col-xl-6 col-lg-12">
                                                                <div class="ordering-box-main">
                                                                    <div class="ordering-box-top">
                                                                        <div class="ordering-box-img">
                                                                            <div class="ordering-img">

                                                                                 <?php $image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image']) && $value['restaurant_image']!='') ?  image_url. $value['restaurant_image'] : default_icon_img;
                                                                                 $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>

                                                                                <img src="<?php echo $image; ?>">
                                                                                <?php if ($show_restaurant_reviews) { ?>
                                                                                <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                <?php } ?> 
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-text">
                                                                            <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['restaurant_name']; ?></a></h6>
                                                                            <?php if($this->session->userdata('UserType') == 'Agent') { ?>
                                                                                <strong><?php echo $this->lang->line('customer') ?> : <span><?php echo $value['user_name']; ?></span></strong>
                                                                            <?php } ?>
                                                                            <p>#<?php echo $this->lang->line('orderid') ?> - <?php echo $value['order_id']; ?></p>
                                                                            <strong><?php echo $this->lang->line('price') ?> : <span><?php echo currency_symboldisplay($total,$value['currency_symbol']);?></span></strong>
                                                                            <?php if($value['refund_status']!='' && $value['refund_status']!=null){ ?>
                                                                                <p class="mt-1"><?php echo $this->lang->line('refund_status') ?> - <?php echo ucfirst($this->lang->line(str_replace(" ", "_", $value['refund_status']))) ?></p>
                                                                            <?php } ?>
                                                                            <?php if($value['scheduled_date'] && $value['slot_open_time'] && $value['slot_close_time']) { ?>
                                                                                <p><i class="iicon-icon-18" style="color:#17161a;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('order_scheduled_for').$this->common_model->dateFormat($value['scheduled_date']).' ('.$this->common_model->timeFormat($value['slot_open_time']).' - '.$this->common_model->timeFormat($value['slot_close_time']).' )'; ?></p>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ordering-box-bottom">
                                                                        <span class="date-icon"><?php echo $this->common_model->datetimeFormat($value['order_date']); ?></span>
                                                                        <span class="relivered-icon"><?php echo ($value['payment_status']=='paid' || $value['payment_status']== NULL)?$this->lang->line($value['order_status']):$this->lang->line($value['payment_status']); ?></span>
                                                                        <div class="ordering-btn">
                                                                            <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            <input type="hidden" name="OrderStatus" id="OrderStatus<?php echo $key ?>" value="<?php echo $value['order_status']; ?>">
                                                                            <?php $newdate = date_format(date_create($value['timer_order_date']),"M d,Y H:i:s"); ?>
                                                                            <input type="hidden" name="OrderDate" id="OrderDate<?php echo $key ?>" value="<?php echo $value['order_dateorg']; ?>">
                                                                            <?php if($value['show_cancel_order'] == '1' && $value['order_status']=='placed') { ?>
                                                                                <button class="btn cancel_order" id="cancel_order<?php echo $key; ?>" data-toggle="modal" onclick="cancel_order(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('cancel_order') ?></button>
                                                                                <input type="hidden" id="orderid<?php echo $key ?>" name="orderid<?php echo $key ?>" value="<?php echo $value['order_id']; ?>" />
                                                                            <?php } ?>
                                                                            <?php //if ($value['delivery_flag'] == "delivery") { ?>
                                                                                <a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])); ?>" class="btn"><?php echo $this->lang->line('track_order') ?></a>
                                                                            <?php //} ?>
                                                                            <?php if($value['show_cancel_order'] == '1' && $value['order_status']=='placed') { ?>
                                                                                <div class="cancel_timer" id="cancel_timer<?php echo $key ?>"></div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <?php }
                                                }?>
                                            </div>
                                            <?php if ($in_process_orders_count>8){?>
                                                <div class=" display-no" id="all_current_orders" >  
                                                </div>
                                                <div class="col-lg-12">
                                                    <div id="more_in_process_orders" class="load-more-btn">
                                                        <input type="hidden" name="cord_page_no" id="cord_page_no" value="2">
                                                        <button class="btn" onclick="moreOrders('process')"><?php echo $this->lang->line('load_more') ?></button>
                                                    </div>
                                                </div>
                                            <?php }?>
                                            <?php }
                                            else { ?>
                                                <div class="empty_block">
                                                    <figure>
                                                        <img src="<?php echo no_res_found; ?>">
                                                    </figure>
                                                    <p class="no-found"><?php echo $this->lang->line('no_current_orders') ?></p>
                                                </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade <?php echo ($selected_tab == "bookings")?"in active show":"";?>" id="bookings">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('my_bookings') ?></h5>
                                    <ul class="nav nav-tabs" role="tablist">
                                      <li class="nav-item">
                                        <a href="#current-bookings" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('upcoming_bookings') ?></a>
                                      </li>
                                      <li class="nav-item">
                                        <a href="#past-bookings" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('past_bookings') ?></a>
                                      </li>
                                    </ul>
                                </div>
                                <div class="profile-content-main">
                                    <div class="tab-content">
                                        <div id="past-bookings" class="tab-pane">
                                            <?php if (!empty($past_events)) { ?>
                                                <div class="row orders-box-row">
                                                    <?php if (!empty($past_events)) {
                                                        foreach ($past_events as $key => $value) {
                                                            if ($key <= 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                   
                                                                                    <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                    $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>


                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['name'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <?php if(!empty($value['package_name'])) { ?>
                                                                                    <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                                <?php } ?>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['event_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } 
                                                        }
                                                    } ?>
                                                </div>
                                                <?php if (count($past_events) > 8) { ?>
                                                    <div class=" display-no" id="all_past_events">
                                                        <div class="row orders-box-row display-flex">
                                                        <?php foreach ($past_events as $key => $value) {
                                                            if ($key > 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                    
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>

                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['name'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <?php if(!empty($value['package_name'])) { ?>
                                                                                    <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                                <?php } ?>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['event_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                        } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div id="more_past_events" class="load-more-btn">
                                                            <button class="btn" onclick="moreEvents('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } 
                                            else { ?>
                                                <div class="empty_block">
                                                    <figure>
                                                        <img src="<?php echo no_res_found; ?>">
                                                    </figure>
                                                    <p class="no-found"><?php echo $this->lang->line("no_past_booking_found"); ?></p>
                                                </div>
                                            <?php }?>
                                        </div>
                                        <div id="current-bookings" class="tab-pane show active">
                                            <?php if (!empty($upcoming_events)) { ?>
                                                <div class="row orders-box-row">
                                                    <?php if (!empty($upcoming_events)) {
                                                        foreach ($upcoming_events as $key => $value) {
                                                            if ($key <= 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                    
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>

                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['name'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <?php if(!empty($value['package_name'])) { ?>
                                                                                    <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                                <?php } ?>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['event_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } 
                                                        }
                                                    } ?>
                                                </div>
                                                <?php if (count($upcoming_events) > 8) { ?>
                                                    <div class=" display-no" id="all_upcoming_events">
                                                        <div class="row orders-box-row">
                                                        <?php foreach ($upcoming_events as $key => $value) {
                                                            if ($key > 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
                                                                                     
                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><?php echo $value['name'];?></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <?php if(!empty($value['package_name'])) { ?>
                                                                                    <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                                <?php } ?>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['event_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                        } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div id="more_upcoming_events" class="load-more-btn">
                                                            <button class="btn" onclick="moreEvents('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php }  
                                            else { ?>
                                                    <div class="empty_block">
                                                        <figure>
                                                            <img src="<?php echo no_res_found; ?>">
                                                        </figure>
                                                        <p class="no-found"><?php echo $this->lang->line('no_upcoming_bookings') ?></p>
                                                    </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- wallet history : start -->
                        <div class="tab-pane fade <?php echo ($selected_tab == "wallet_history")?"in active show":"";?>" id="wallet_history">
                            <div class="referal-code-area">
                                <div class="referal-list">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="list_ref-inner">
                                                <span class="ref-img"><img src="<?php echo base_url();?>assets/front/images/share.png" alt="share"></span>
                                                <span><?php echo $this->lang->line('share_code') ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="list_ref-inner">
                                                <span class="ref-img"><img src="<?php echo base_url();?>assets/front/images/register.jpg" alt="register"></span>
                                                <span><?php echo $this->lang->line('once_reg_with_ref') ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="list_ref-inner">
                                                <span class="ref-img"><img src="<?php echo base_url();?>assets/front/images/earn.png" alt="earn"></span>
                                                <span><?php echo $this->lang->line('get_reward') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="referal-bottom">
                                        <h2 class="referal-heading"><?php echo $this->lang->line('your_referral_code') ?></h2>
                                        <button class="btn referal-code-btn" onclick="copyToClipboard()"><?php echo ($profile->referral_code)?$profile->referral_code:$this->lang->line('refcode') ?></button>
                                        <input type="hidden" name="ref_code" id="ref_code" value="<?php echo $profile->referral_code; ?>">
                                        <span class="copy-code-text"><a href="javascript:void(0)" onclick="copyToClipboard()"><?php echo $this->lang->line('tap_to_copy') ?></a>
                                            <div id="copied-success" class="copied" style="display: none;">
                                                <span><?php echo $this->lang->line('copied') ?></span>
                                            </div>
                                        </span>
                                        <?php /* ?><button class="btn Refer-now-btn"><?php echo $this->lang->line('refer_now') ?></button><?php */ ?>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('wallet_history') ?></h5>
                                    <?php /* if (!empty($this->session->userdata('UserID')) && $this->session->userdata('UserType') == 'User') { ?>
                                        <div class="add-wallet-money-btn">
                                            <button class="btn add-wallet-topup-btn" data-toggle="modal" onclick="add_wallet_money()"><?php echo $this->lang->line('add_money') ?></button>
                                        </div>
                                    <?php } */ ?>
                                </div>
                                <div class="profile-content-main">
                                    <?php if (!empty($wallet_history)) { ?>
                                        <div class="row">
                                            <?php if (!empty($wallet_history)) { ?>
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="my-wallet-transaction">
                                                        <div class="my-wallet-detail">
                                                            <h6><?php echo $this->lang->line('transactions'); ?></h6>
                                                            <p class="price_green"><?php echo $this->lang->line('total_earned'). ': ';?><?php echo currency_symboldisplay($wallet_history['total_money_credited'],$currency_symbol); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php foreach ($wallet_history['result'] as $key => $value) { ?>
                                                    <div class="col-xl-12 col-lg-12">
                                                        <div class="my-wallet-main">
                                                            <div class="my-wallet-box">
                                                                <div class="my-wallet-list">
                                                                    <span class="icons_money <?php echo ($value->credit=='1')?'credit-icon':'debit-icon'; ?>"></span>
                                                                    <h6><?php echo $this->lang->line($value->reason); ?> <?php echo $value->order_id; ?></h6>
                                                                    <p class="<?php echo ($value->credit=='1')?'price_green':'price_red'; ?>"><?php echo ($value->credit=='1')?'+':'-'; ?><?php echo currency_symboldisplay($value->amount,$currency_symbol); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                    <?php }
                                    else { ?>
                                        <div class="empty_block">
                                                <figure>
                                                    <img src="<?php echo no_res_found; ?>">
                                                </figure>
                                                <p><?php echo $this->lang->line('no_wallet_history_found') ?></p>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <!-- wallet history : end -->
                        <div class="tab-pane fade <?php echo ($selected_tab == "addresses")?"in active show":"";?>" id="addresses">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('my_addresses') ?></h5>
                                    <div class="add-address-btn">
                                        <button class="btn" data-toggle="modal" data-target="#add-address"><?php echo $this->lang->line('add_address') ?></button>
                                    </div>
                                </div>
                                <div class="profile-content-main">
                                    <?php if (!empty($users_address)) { ?>
                                        <div class="row orders-box-row">
                                            <?php if (!empty($users_address)) {
                                                foreach ($users_address as $key => $value) { 
                                                    $class = ($value->is_main == 1)?"primary-address":""; ?>
                                                    <div class="col-xl-6 col-lg-12">
                                                        <div class="my-address-main <?php echo $class; ?>">
                                                            <div class="my-address-box">
                                                                <div class="my-address-list">
                                                                    <h6><?php echo ($value->address_label)?$value->address_label:$this->lang->line('no_additional_info'); ?></h6> <?php echo ($value->is_main == 1)?"<span class='default-address'>". $this->lang->line('default') ."</span>":""; ?>
                                                                    <p><?php //echo $value->address.','.$value->landmark.','.$value->city.','.$value->zipcode.','.$value->search_area; ?></p>
                                                                    <p><?php echo $value->address; ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="address-btn">
                                                                <button class="btn" data-toggle="modal"  onclick="editAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('edit_address') ?></button>
                                                                <button class="btn" data-toggle="modal" onclick="showDeleteAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('delete_address') ?></button>
                                                                <?php if ($value->is_main == 0) { ?>
                                                                    <button class="btn" data-toggle="modal" onclick="showMainAddress(<?php echo $value->address_id; ?>);"><?php echo $this->lang->line('set_as_primary') ?></button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                <?php }
                                            } ?>                    
                                        </div>
                                    <?php } 
                                    else { ?>
                                        <div class="empty_block">
                                            <figure>
                                                <img src="<?php echo no_res_found; ?>">
                                            </figure>
                                            <p class="no-found"><?php echo $this->lang->line('no_address_found') ?></p>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade <?php echo ($selected_tab == "notifications")?"in active show":"";?>" id="notifications">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('my_notifications') ?></h5>
                                </div>
                                <div class="profile-content-main">
                                    <?php if (!empty($users_notifications)) { ?>
                                        <div class="row orders-box-row">
                                            <?php if (!empty($users_notifications)) {
                                                foreach ($users_notifications as $key => $value) {  
                                                    if($key<=7) { ?>
                                                        <div class="col-xl-6 col-lg-12">
                                                            <div class="my-address-main <?php echo $class; ?>">
                                                                <div class="noti">
                                                                    <div class="my-address-list">
                                                                        <h6><?php echo utf8_decode($value->notification_title); ?></h6>
                                                                        <?php 
                                                                        $view_more_btn = (strlen($value->notification_description)>250)?'<div class="ordering-btn"><a class="btn" href="javascript:void(0)" onclick="showNotification('.$value->entity_id.')">'.$this->lang->line('view_more').'</a></div>':'';
                                                                        $notification_description= mb_strimwidth($value->notification_description,0,250,'').' '.$view_more_btn;
                                                                         ?>
                                                                        <p><?php echo utf8_decode($notification_description); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } 
                                                }
                                            } ?>                    
                                        </div>
                                    <?php } 
                                    else { ?>
                                        <div class="empty_block">
                                            <figure>
                                                <img src="<?php echo no_res_found; ?>">
                                            </figure>
                                            <p class="no-found"><?php echo $this->lang->line('no_notifications_found') ?></p>
                                        </div>
                                    <?php }?>
                                </div>
                                <?php if (count($users_notifications) > 8) { ?>
                                    <div class="display-no" id="all_notifications" >
                                        <div class="row orders-box-row">
                                            <?php if (!empty($users_notifications)) {
                                                foreach ($users_notifications as $key => $value) {  
                                                    if($key>7) { ?>
                                                        <div class="col-xl-6 col-lg-12">
                                                            <div class="my-address-main <?php echo $class; ?>">
                                                                <div class="noti">
                                                                    <div class="my-address-list">
                                                                        <h6><?php echo $value->notification_title; ?></h6>
                                                                        <?php $view_more_btn = (strlen($value->notification_description)>250)?'<div class="ordering-btn"><a class="btn" href="javascript:void(0)" onclick="showNotification('.$value->entity_id.')">'.$this->lang->line('view_more').'</a></div>':'';
                                                                        $notification_description= mb_strimwidth($value->notification_description,0,250,'').' '.$view_more_btn;
                                                                         ?>
                                                                        <p><?php echo utf8_decode($notification_description); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } 
                                                }
                                            } ?>                    
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div id="load_more_notifications" class="load-more-btn">
                                            <button class="btn" onclick="moreNotifications()"><?php echo $this->lang->line('load_more') ?></button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- bookmark :start -->
                        <div class="tab-pane fade <?php echo ($selected_tab == "bookmarks")?"in active show":"";?>" id="bookmarks">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('my_bookmarks') ?></h5>
                                </div>
                                <div class="profile-content-main">
                                    <?php if (!empty($users_bookmarks)) { ?>
                                        <div class="row orders-box-row">
                                            <?php if (!empty($users_bookmarks)) {
                                                foreach ($users_bookmarks as $key => $value) {  
                                                    if($key<=5) { ?>
                                                        <div class="col-lg-6 mb-3">
                                                            <div class="restaurant-box">
                                                                <div class="popular-rest-box">
                                                                    <a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
                                                                    <div class="popular-rest-img">
                                                                        
                                                                            <?php $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image']: default_img;  ?>
                                                                            <img src="<?php echo $rest_image ;?>" alt="<?php echo $value['name']; ?>">
                                                                        
                                                                        <div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
                                                                        <?php if ($show_restaurant_reviews) { ?>
                                                                        <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
                                                                        <?php } ?>
                                                                    </div>
                                                                </a>
                                                                    <div class="popular-rest-content">
                                                                        <h3><a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>"><?php echo $value['name']; ?></a></h3>
                                                                        <div class="popular-rest-text">
                                                                        <p class="address-icon"><?php echo $value['address']; ?> </p>
                                                                        
                                                                        <div class="ordering-btn text-right">
                                                                            <?php  /*if($value['timings']['closing'] != "Closed") {
                                                                            ?>
                                                                                <a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>" class="btn"><?php echo $this->lang->line('order') ?></a>
                                                                            <?php }*/ ?>
                                                                            <a style="width: max-content;" alt="<?php echo $this->lang->line('remove_bookmarked'); ?>" title="<?php echo $this->lang->line('remove_bookmarked'); ?>" onclick="removeBookmark('<?php echo $value['entity_id'] ?>')" class="btn" href="javascript:void(0)"><i class="iicon-icon-38"></i> <?php echo $this->lang->line('remove'); ?></a>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } 
                                                }
                                            } ?>                    
                                        </div>
                                    <?php } 
                                    else { ?>
                                        <div class="empty_block">
                                            <figure>
                                                <img src="<?php echo no_res_found; ?>">
                                            </figure>
                                            <p class="no-found"><?php echo $this->lang->line('no_bookmarks_found') ?></p>
                                        </div>
                                    <?php }?>
                                </div>
                                <?php if (!empty($users_bookmarks) && isset($users_bookmarks)) { ?>
                                <?php if (count($users_bookmarks) > 6 ) { ?>
                                    <div class="display-no" id="all_bookmarks" >
                                        <div class="row orders-box-row">
                                            <?php if (!empty($users_bookmarks)) {
                                                foreach ($users_bookmarks as $key => $value) {  
                                                    if($key>5) { ?>
                                                        <div class="col-lg-6 mb-3">
                                                            <div class="restaurant-box">
                                                                <div class="popular-rest-box">
                                                                    <div class="popular-rest-img">
                                                                        <a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>">
                                                                            <?php $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image']: default_img;  ?>
                                                                            <img src="<?php echo $rest_image ;?>" alt="<?php echo $value->name; ?>">
                                                                        </a>
                                                                        <div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
                                                                        <?php if ($show_restaurant_reviews) { ?>
                                                                        <?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
                                                                        <?php } ?>
                                                                    </div>
                                                                    <div class="popular-rest-content">
                                                                        <h3><a href="<?php echo base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug'];?>"><?php echo $value['name']; ?></a></h3>
                                                                        <div class="popular-rest-text">
                                                                            <p></p>
                                                                        <div class="ordering-btn">
                                                                            <a alt="<?php echo $this->lang->line('remove_bookmarked'); ?>" title="<?php echo $this->lang->line('remove_bookmarked'); ?>" onclick="removeBookmark('<?php echo $value['entity_id'] ?>')" class="btn" href="javascript:void(0)"><i class="iicon-icon-38"></i> <?php echo $this->lang->line('remove'); ?></a>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } 
                                                }
                                            } ?>                    
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div id="load_more_bookmarks" class="load-more-btn">
                                            <button class="btn" onclick="moreBookmarkedRes()"><?php echo $this->lang->line('load_more') ?></button>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- bookmark :end -->
                        <!-- table bookings : start -->
                        <div class="tab-pane fade <?php echo ($selected_tab == "table_bookings")?"in active show":"";?>" id="table_bookings">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('table_bookings') ?></h5>
                                    <ul class="nav nav-tabs" role="tablist">
                                      <li class="nav-item">
                                        <a href="#current-table-bookings" class="nav-link active" data-toggle="tab"><?php echo $this->lang->line('upcoming_table_bookings') ?></a>
                                      </li>
                                      <li class="nav-item">
                                        <a href="#past-table-bookings" class="nav-link" data-toggle="tab"><?php echo $this->lang->line('past_table_bookings') ?></a>
                                      </li>
                                    </ul>
                                </div>
                                <div class="profile-content-main">
                                    <div class="tab-content">
                                        <div id="past-table-bookings" class="tab-pane">
                                            <?php if (!empty($past_tables)) { ?>
                                                <div class="row orders-box-row">
                                                    <?php if (!empty($past_tables)) {
                                                        foreach ($past_tables as $key => $value) {
                                                            if ($key <= 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                   
                                                                                    <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                    $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>


                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['rname'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['booking_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['start_time'])." - ".$this->common_model->timeFormat($value['end_time']);?></li>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="table_booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } 
                                                        }
                                                    } ?>
                                                </div>
                                                <?php if (count($past_tables) > 8) { ?>
                                                    <div class=" display-no" id="all_past_tables">
                                                        <div class="row orders-box-row display-flex">
                                                        <?php foreach ($past_tables as $key => $value) {
                                                            if ($key > 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                    
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>

                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['rname'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['booking_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['start_time'])." - ".$this->common_model->timeFormat($value['end_time']);?></li>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="table_booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                        } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div id="more_past_tables" class="load-more-btn">
                                                            <button class="btn" onclick="moreTables('past')"><?php echo $this->lang->line('load_more') ?></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } 
                                            else { ?>
                                                <div class="empty_block">
                                                    <figure>
                                                        <img src="<?php echo no_res_found; ?>">
                                                    </figure>
                                                    <p class="no-found"><?php echo $this->lang->line("no_past_table_booking_found"); ?></p>
                                                </div>
                                            <?php }?>
                                        </div>
                                        <div id="current-table-bookings" class="tab-pane show active">
                                            <?php if (!empty($upcoming_tables)) { ?>
                                                <div class="row orders-box-row">
                                                    <?php if (!empty($upcoming_tables)) {
                                                        foreach ($upcoming_tables as $key => $value) {
                                                            if ($key <= 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                    
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>

                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['rname'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['booking_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['start_time'])." - ".$this->common_model->timeFormat($value['end_time']);?></li>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="table_booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } 
                                                        }
                                                    } ?>
                                                </div>
                                                <?php if (count($upcoming_tables) > 8) { ?>
                                                    <div class=" display-no" id="all_upcoming_tables">
                                                        <div class="row orders-box-row">
                                                        <?php foreach ($upcoming_tables as $key => $value) {
                                                            if ($key > 7) { ?>
                                                                <div class="col-xl-6 col-lg-12">
                                                                    <div class="ordering-box-main">
                                                                        <div class="ordering-box-top">
                                                                            <div class="ordering-box-img">
                                                                                <div class="ordering-img">
                                                                                     <?php $image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ?  image_url. $value['image'] : default_icon_img; 
                                                                                     $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
                                                                                     
                                                                                    <img src="<?php echo $image;?>">
                                                                                    <?php if ($show_restaurant_reviews) { ?> 
                                                                                    <?php echo ($value['ratings'] > 0)?'<strong class="ratingtxt">'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ordering-box-text">
                                                                                <h6><a href="<?php echo ($value['restaurant_status']=='1')?base_url().'restaurant/restaurant-detail/'.$value['restaurant_slug']:'#';?>"><?php echo $value['rname'];?></a></h6>
                                                                                <p class="addresse-icon"><?php echo $value['address'];?></p>
                                                                                <?php if(!empty($value['package_name'])) { ?>
                                                                                    <strong><?php echo $this->lang->line('pkg') ?> : <?php echo $value['package_name'];?></strong>
                                                                                <?php } ?>
                                                                                <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($value['booking_status']); ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ordering-box-bottom">
                                                                            <ul>
                                                                                <li><i class="iicon-icon-18"></i><?php echo $this->common_model->timeFormat($value['start_time'])." - ".$this->common_model->timeFormat($value['end_time']);?></li>
                                                                                <li><i class="iicon-icon-26"></i><?php echo $this->common_model->dateFormat($value['booking_date']);?></li>
                                                                                <li><i class="iicon-icon-36"></i><?php echo $value['no_of_people'];?> <?php echo $this->lang->line('people') ?></li>
                                                                            </ul>
                                                                            <div class="ordering-btn">
                                                                                <button class="btn" data-toggle="modal" onclick="table_booking_details(<?php echo $value['entity_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                        } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div id="more_upcoming_tables" class="load-more-btn">
                                                            <button class="btn" onclick="moreTables('upcoming')"><?php echo $this->lang->line('load_more') ?></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php }  
                                            else { ?>
                                                    <div class="empty_block">
                                                        <figure>
                                                            <img src="<?php echo no_res_found; ?>">
                                                        </figure>
                                                        <p class="no-found"><?php echo $this->lang->line('no_upcoming_table_bookings') ?></p>
                                                    </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- table bookings : end -->
                        <div class="tab-pane fade <?php echo ($selected_tab == "payment_card")?"in active show":"";?>" id="payment_card">
                            <div class="profile-content-area">
                                <div class="profile-page-title">
                                    <h5><?php echo $this->lang->line('card_detail') ?></h5>   
                                    <div class="edit-pro-btn">
                                        <button class="btn newcard_add" data-toggle="modal" id="add-stripecardid" data-target="#add-stripecard"><?php echo $this->lang->line('add_card') ?></button>
                                        <button class="btn card_edit" data-toggle="modal" onclick="showeditcard();"><?php echo $this->lang->line('edit_card') ?></button>
                                    </div>
                                </div>
                                <div class="profile-content-main payment_card_div">
                                    <?php if (!empty($savecard_detail) && !isset($savecard_detail['error'])) { ?>
                                        <div class="row">
                                            <div class="col-md-12">                                         
                                                <div class="old-card-group">
                                                    <div class="edit-profile-img">
                                                    <span class="error">
                                                    <?php echo $_SESSION['delete_cardmessage'];
                                                        unset($_SESSION['delete_cardmessage']);
                                                     ?></span>
                                                </div>
                                            <?php if (!empty($savecard_detail)) { ?>
                                                <?php foreach ($savecard_detail as $key => $value) { 
                                                    $default_card_class = ($value['is_default_card'] == '1') ? 'default-stripe-card' : '' ; ?>
                                                <div class="payment_card_box <?php echo $default_card_class; ?>">
                                                    <div class="radio-btn-list">
                                                        <label class="payment_label">
                                                            <input type="radio" name="payment-source" value="saved_card_<?php echo $key; ?>" card_fingerprint="<?php echo $value['card_fingerprint'];?>" PaymentMethodid="<?php echo $value['PaymentMethodid'];?>" exp_month="<?php echo $value['exp_month'];?>" exp_year="<?php echo $value['exp_year'];?>" postal_code="<?php echo $value['postal_code'];?>" card_last4="<?php echo $value['card_last4'];?>">
                                                            <span></span>
                                                            <div class="card_image"><img src="<?php echo base_url().$value['card_image'];?>" width="60"></div>
                                                            <div class="card-name-no">
                                                                <h5 class="card_name"><?php echo $value['card_brand_name']; ?></h5>
                                                                <h6 class="saved-card"><?php echo $this->lang->line('ending_in').$value['card_last4'].', '.$this->lang->line('expires').$value['exp_month'].'/'.$value['exp_year'];?></h6>
                                                            </div>
                                                            <div class="card-btns">
                                                                <?php if($value['is_default_card'] != '1') { ?>
                                                                    <button type="button" class="btn remove-card" alt="set-as-default" title="<?php echo $this->lang->line('set_as_default'); ?>" onclick="set_as_default_stripecard('<?php echo $value['PaymentMethodid'];?>', '<?php echo $value['stripecus_id'];?>');"><svg id="Layer_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m456 80h-400c-30.878 0-56 25.122-56 56v240c0 30.878 25.122 56 56 56h400c30.878 0 56-25.122 56-56v-240c0-30.878-25.122-56-56-56zm-400 32h400c13.233 0 24 10.767 24 24v32h-448v-32c0-13.233 10.767-24 24-24zm400 288h-400c-13.233 0-24-10.767-24-24v-176h448v176c0 13.233-10.767 24-24 24z"/><path d="m112 352h-16c-8.836 0-16-7.164-16-16v-16c0-8.836 7.164-16 16-16h16c8.836 0 16 7.164 16 16v16c0 8.836-7.164 16-16 16z"/></g></svg></button>
                                                                <?php } ?>
                                                                <button type="button" class="btn remove-card" alt="remove-card" title="<?php echo $this->lang->line('remove_card'); ?>" onclick="showDeleteStipe('<?php echo $value['PaymentMethodid'];?>', '<?php echo $value['stripecus_id'];?>');"><i class="iicon-icon-23"></i></button>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php }
                                            } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    else { ?>
                                        <div class="empty_block">
                                                <figure>
                                                    <img src="<?php echo no_res_found; ?>">
                                                </figure>
                                                <?php if (!empty($savecard_detail) && isset($savecard_detail['error'])) { ?>
                                                    <p><?php echo $savecard_detail['message']; ?></p>
                                                <?php } else{ ?>
                                                <p><?php echo $this->lang->line('no_card_detail_found') ?></p>
                                                <?php } ?>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end content-area section -->
    <!-- Modal -->
    <!-- Edit Profile -->
    <div class="modal modal-main edit-profile" id="edit-profile">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title"><?php echo $this->lang->line('edit_profile') ?></h4>
            <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"  onclick="document.location.href='<?php echo base_url();?>myprofile';"><i class="iicon-icon-23"></i></button>
          </div>
          <!-- Modal body -->
          <div class="modal-body">
            <form id="form_my_profile" name="form_my_profile" method="post" class="form-horizontal float-form" enctype="multipart/form-data">
                <div id="error-msg" class="error display-no"></div>
                <div class="edit-profile-img">
                    <?php /* ?><div class="edit-img">
                         <?php $image = (file_exists(FCPATH.'uploads/'.$profile->image) && $profile->image!='') ?  image_url. $profile->image : default_user_img;?>
                        <img id='old' src="<?php echo $image; ?>">
                        <img id="preview" class="display-no"/>
                        <label>
                            <input type="file" name="image" id="image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)"/>
                            <i class="iicon-icon-37"></i>
                        </label>
                    </div><?php */ ?>
                    <span class="error display-no" id="errormsg"></span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $profile->entity_id; ?>">
                    <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($profile->image) ? $profile->image : ''; ?>" />
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " value="<?php echo $profile->first_name; ?>" maxlength='20'>
                    <label><?php echo $this->lang->line('first_name') ?></label>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " value="<?php echo $profile->last_name; ?>" maxlength='20'>
                    <label><?php echo $this->lang->line('last_name') ?></label>
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control email" placeholder=" " value="<?php echo $profile->email; ?>" maxlength='50'>
                    <label><?php echo $this->lang->line('email') ?></label>
                </div>
                <div class="form-group">
                    <input type="hidden" name="phone_code" id="phone_code" class="form-control" value="<?php echo $profile->phone_code; ?>">
                    <input type="tel" name="phone_number" id="phone_number" class="form-control digits required" readonly placeholder="" value="<?php echo $profile->mobile_number; ?>" maxlength='12'>
                    <?php //echo ($profile->login_type == 'facebook')?'':'readonly'; ?>
                    <label><?php echo $this->lang->line('phone_number') ?></label>
                    <div class="phn_err"></div>
                </div>
                <?php if($profile->login_type == 'facebook' || $profile->login_type == 'google') { ?>
                    <div class="form-group">
                        <input type="hidden" name="password" id="password" class="form-control" placeholder=" " value="">
                        <label><?php //echo $this->lang->line('password') ?></label>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="confirm_password" id="confirm_password" class="form-control" placeholder=" " value="">
                        <label><?php //echo $this->lang->line('confirm_pass') ?></label>
                    </div>
                <?php } else { ?>    
                    <div class="form-group password-icon">
                        <input type="password" name="password" id="password" class="form-control" placeholder=" ">
                        <label><?php echo $this->lang->line('password') ?></label>
                        <i id="togglePasswordshow" class="">
                            <img src="<?php echo base_url();?>assets/front/images/password-eye.svg" alt="">
                            <img src="<?php echo base_url();?>assets/front/images/password-eye-close.svg" alt="">
                        </i>
                    </div>
                    <div class="form-group password-icon">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder=" ">
                        <label><?php echo $this->lang->line('confirm_pass') ?></label>
                        <i id="togglePasswordshowconfirm" class="">
                            <img src="<?php echo base_url();?>assets/front/images/password-eye.svg" alt="">
                            <img src="<?php echo base_url();?>assets/front/images/password-eye-close.svg" alt="">
                        </i>
                    </div>
                <?php } ?>
                <div class="save-btn">
                    <button type="submit" name="submit_profile" id="submit_profile" value="Save" class="btn btn-primary"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Add Address -->
    <div class="modal modal-main add-address_" id="add-address">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><span id="address-form-title"><?php echo $this->lang->line('add') ?></span> <?php echo $this->lang->line('address') ?></h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map">
                                <div  id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                    <form id="form_add_address" name="form_add_address" method="post" class="form-horizontal float-form" enctype="multipart/form-data" >
                        <div id="error-msg" class="alert alert-danger display-no"></div>
                        <!--<div class="form-group">
                            <input type="text" name="add_address_area" id="add_address_area"  placeholder=" " onchange="getMarker('');" class="form-control">
                            <label><?php echo $this->lang->line('search_delivery_area') ?></label>
                        </div>-->
                        <div class="form-group home_auto_location checkout_loc">
                            <input type="hidden" name="user_entity_id" id="user_entity_id" value="<?php echo $this->session->userdata('UserID'); ?>">
                            <input type="hidden" name="add_entity_id" id="add_entity_id" value="">
                            <input type="hidden" name="latitude" id="latitude" value="">
                            <input type="hidden" name="longitude" id="longitude" value="">
                            <input type="hidden" name="default_latitude" id="default_latitude" value="">
                            <input type="hidden" name="default_longitude" id="default_longitude" value="">
                            <a href="javascript:;" class="auto_location" onclick="getLocation('my_profile');"></a>
                            <input type="text" name="address_field" id="address_field" class="form-control" onFocus="geolocate('')" placeholder=" " onchange="getMarker(this.value)">
                            <label><?php echo $this->lang->line('your_location') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="landmark" id="landmark" class="form-control" placeholder=" ">
                            <label><?php echo $this->lang->line('landmark_txt') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder=" " minlength="5" maxlength="6">
                            <label><?php echo $this->lang->line('postal_code') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="city" id="city" class="form-control" placeholder=" ">
                            <label><?php echo $this->lang->line('city') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="state" id="state" class="form-control" placeholder=" ">
                            <label><?php echo $this->lang->line('state') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="country" id="country" class="form-control" placeholder=" ">
                            <label><?php echo $this->lang->line('country') ?></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="address_label" id="address_label" class="form-control" placeholder=" ">
                            <label><?php echo $this->lang->line('city_txt') ?></label>
                        </div>
                        <div class="address-add-btn">
                            <input type="hidden" name="submit_address" id="submit_address" value="Add" class="btn btn-primary">
                            <button type="submit" name="save_address" id="save_address" value="Save" class="btn btn-primary"><?php echo $this->lang->line('save') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-main delete-address_" id="delete-address">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('delete_address') ?>?</h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?php echo $this->lang->line('delete_module'); ?><p>
                    <input type="hidden" name="delete_address_id" id="delete_address_id" value="">
                    <div class="action-btn">
                        <input type="button" name="delete_address" id="delete_address" value="<?php echo $this->lang->line('delete') ?>" class="btn btn-primary" onclick="deleteAddress()">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-main main-address_" id="main-address">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('set_main_address') ?>?</h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?php echo $this->lang->line('set_main_address_confirm') ?></p>
                    <input type="hidden" name="main_address_id" id="main_address_id" value="">
                    <div class="action-btn">
                        <input type="button" name="main_address" id="main_address" value="<?php echo $this->lang->line('ok'); ?>" class="btn" onclick="setMainAddress()">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="dialog-confirm" title="Delete this address?" class="display-no">
      <p><span class="ui-icon ui-icon-alert"></span><?php echo $this->lang->line('delete_module'); ?></p>
    </div>
    <div id="dialog-confirm-setmain" title="Set Main Address?" class="display-no">
      <p><span class="ui-icon ui-icon-alert"></span><?php echo $this->lang->line('set_main_address_confirm') ?></p>
    </div>
    <!-- Booking Details -->
    <div class="modal modal-main order-detail-popup" id="booking-details"></div>
    <!-- Table Booking Details -->
    <div class="modal modal-main order-detail-popup" id="table-booking-details"></div>
    <!-- Order Details -->
    <div class="modal modal-main order-detail-popup" id="order-details"></div>
    <!-- Cancel Order -->
    <div class="modal modal-main cancel-order-popup" id="cancel-order"></div>
    <!-- delete account modal : start -->
    <div class="modal modal-main delete-address_" id="delete-account">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('delete_acc') ?>?</h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?php echo $this->lang->line('delete_module'); ?><p>
                    <div class="action-btn">
                        <input type="button" name="delete_account" id="delete_account" value="<?php echo $this->lang->line('delete') ?>" class="btn btn-primary" onclick="deleteAccount()">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- delete account modal : end -->
    <!-- mobilPay payment integration changes : start -->
    <!-- Order Confirmation -->
    <div class="modal modal-main" id="order-confirmation">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title"><?php echo $this->lang->line('order_confirmation') ?></h4>
            <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal" onclick="document.location.href='<?php echo base_url();?>myprofile';"><i class="iicon-icon-23"></i></button>
          </div>
          <!-- Modal body -->
          <div class="modal-body">
            <div class="availability-popup">
                <?php if(isset($payment) && $payment['status'] == 'paid'){ ?>
                    <div class="availability-images">
                        <img src="<?php echo base_url();?>assets/front/images/order-confirmation.svg" alt="Booking availability">
                    </div>
                    <h2><?php echo $this->lang->line('thankyou_for_order') ?></h2>
                    <p><?php echo $this->lang->line('order_placed') ?></p>
                    <?php if($payment['earned_points'] && $payment['earned_points']>0){?>
                    <span id="earned_points"><p><?php echo $this->lang->line('points_earned_from_order').": ".$payment['earned_points'];?></p></span>
                    <?php } ?>
                    <p><?php echo ($payment['pay_status'])?ucfirst($payment['pay_status']):''; ?><?php echo ($payment['message'] && $payment['pay_status'])?' : ':''; ?><?php echo ($payment['message'])?$payment['message']:''; ?></p>
                    
                    <?php //if($payment['order_delivery'] == 'Delivery'){ ?>                    
                        <span id="track_order"><a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($payment['order_id'])); ?>" class="btn"><?php echo $this->lang->line("track_order"); ?></a></span>                
                    <?php //} else { ?>                    
                        <span id="track_order"><a href="<?php echo base_url().'myprofile' ?>" class="btn"><?php echo $this->lang->line("view_details"); ?></a></span>
                    <?php //} ?>
                <?php } else { ?>
                    <h2><?php echo $this->lang->line('sorry_not_placed') ?></h2>
                    <p><?php echo (isset($payment['pay_status']))?$payment['pay_status']:'' ?><?php echo (isset($payment['message']) && !empty($payment['message']))?' : '.$payment['message']:''; ?></p>  
                <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php if(!empty($payment)) {
            echo '<script>$("#order-confirmation").modal(\'show\');</script>'; 
    } ?>
    <!-- mobilPay payment integration changes : end -->
    <!-- re-order changes :: start -->
    <div class="modal modal-main order-detail-popup" id="reorder-details"></div>
    <div class="modal modal-main" id="cartNotEmpty">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
              <!-- Modal body -->
                <div class="modal-body">
                    <form id="custom_cart_restaurant_form">
                    <h5><?php echo $this->lang->line('items_already_in_cart') ?> <br><?php echo $this->lang->line('res_details_text2') ?></h5>
                    <div class="popup-radio-btn-main">
                        <div class="radio-btn-box">
                            <div class="radio-btn-list">
                                <label>
                                    <input type="hidden" name="rest_restaurant_id" id="rest_restaurant_id" value="">
                                    <input type="hidden" name="rest_user_id" id="rest_user_id" value="">
                                    <input type="hidden" name="menuDetailsArray" id="menuDetailsArray" value=""> 
                                    <input type="radio" checked="checked" class="radio_addon" name="addNewItems" id="discardOld" value="discardOld">
                                    <span><?php echo $this->lang->line('discard_old') ?></span>
                                </label>
                            </div>
                            <div class="radio-btn-list">
                                <label>
                                    <input type="radio" class="radio_addon" name="addNewItems" id="keepOld" value="keepOld">
                                    <span><?php echo $this->lang->line('keep_old') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="popup-total-main">
                        <div class="total-price">
                            <button type="button" class="cartrestaurant btn" id="cartrestaurant" onclick="ConfirmCartItemsOnReorder()"><?php echo $this->lang->line('confirm') ?></button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- add review modal changes -->
    <div class="modal modal-main" id="reviewModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $this->lang->line('review_ratings') ?></h4>
                <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
            </div>
          <!-- Modal body -->
            <div class="modal-body">
                <form id="review_form" name="review_form" method="post" class="form-horizontal float-form">
                    <div class="review-img">
                        <div class="user-images">
                            <picture>                                
                                <source type="image/jpg" srcset="<?php echo base_url();?>assets/front/images/review.png">
                                <img src="<?php echo base_url();?>assets/front/images/review.png">
                            </picture>
                        </div>
                    </div>
                <div class="rating">
                    <input type="hidden" name="review_user_id" id="review_user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
                    <input type="hidden" name="review_restaurant_id" id="review_restaurant_id" value="">
                    <input type="hidden" name="review_res_content_id" id="review_res_content_id" value="">
                    <input type="hidden" name="review_order_id" id="review_order_id" value="">
                    <span><input type="radio" name="rating" id="str5" value="5"><label for="str5"></label></span>
                    <span><input type="radio" name="rating" id="str4" value="4"><label for="str4"></label></span>
                    <span class="checked"><input type="radio" name="rating" id="str3" value="3"><label for="str3"></label></span>
                    <span><input type="radio" name="rating" id="str2" value="2"><label for="str2"></label></span>
                    <span><input type="radio" name="rating" id="str1" value="1"><label for="str1"></label></span>
                </div>
                <div>
                    <input type="text" name="review_text" id="review_text" class="form-control" placeholder="<?php echo $this->lang->line('write_review') ?>">
                </div>
                <div>
                    <button type="submit" name="submit_review" id="submit_review" class="btn btn-primary"><?php echo $this->lang->line('add_review') ?></button>
                </div>
                
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- re-order changes :: end -->
    <!-- show my notification :: start -->
    <div class="modal modal-main showNotifi" id="showNotifi">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="notification_description"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- show my notification ::end -->

    <!-- Show add stripe card page :: start -->
    <div class="modal modal-main add-stripecard" id="add-stripecard">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" id="add_stripetitle"><?php echo $this->lang->line('add_card') ?></h4>
            <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
          </div>
          <!-- Modal body -->
          <div class="modal-body">
            <form id="form_credit_card" name="form_credit_card" method="post" class="form-horizontal float-form" enctype="multipart/form-data">
                <div class="edit-profile-img">
                    <span class="error display-no" id="carderrormsg"></span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="is_editcard" id="is_editcard" value="no">
                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                    <input type="text" name="card_number" id="card_number" class="form-control" placeholder=" " value="" maxlength='20'>
                    <label><?php echo $this->lang->line('card_number'); ?></label>
                    <div id="card_number_err" class="error"></div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <select name="card_month" id="card_month" onchange="cardFormValidate();" class="form-control select_month_form">                      
                                <option value=""><?php echo $this->lang->line('select_month'); ?></option>
                                <option value="01"><?php echo $this->lang->line('january'); ?></option>
                                <option value="02"><?php echo $this->lang->line('february'); ?></option>
                                <option value="03"><?php echo $this->lang->line('march'); ?></option>
                                <option value="04"><?php echo $this->lang->line('april'); ?></option>
                                <option value="05"><?php echo $this->lang->line('may'); ?></option>
                                <option value="06"><?php echo $this->lang->line('june'); ?></option>
                                <option value="07"><?php echo $this->lang->line('july'); ?></option>
                                <option value="08"><?php echo $this->lang->line('august'); ?></option>
                                <option value="09"><?php echo $this->lang->line('september'); ?></option>
                                <option value="10"><?php echo $this->lang->line('october'); ?></option>
                                <option value="11"><?php echo $this->lang->line('november'); ?></option>
                                <option value="12"><?php echo $this->lang->line('december'); ?></option>
                            </select>
                            <div id="card_month_err" class="error"></div>
                        </div>
                        <div class="col-md-6">
                            <select name="card_year" id="card_year" onchange="cardFormValidate();" class="form-control select_year_form">
                                <?php $card_year = date('Y'); $card_yearmax = $card_year+10;
                                $card_yearstr = $card_year-1; ?>
                                <option value=""><?php echo $this->lang->line('select_year') ?></option>
                                <?php for($crdy=$card_yearstr;$crdy<$card_yearmax;$crdy++){ ?>
                                <option value="<?php echo ($crdy+1);?>"><?php echo ($crdy+1);?></option>                      
                                <?php } ?>                            
                            </select>
                            <div id="card_year_err" class="error"></div>
                        </div>
                    </div>
                </div>                
                <div class="form-group">
                    <input type="text" name="card_cvv" id="card_cvv" class="form-control" placeholder=" " value="" maxlength='4'>
                    <label><?php echo $this->lang->line('card_cvv') ?></label>
                    <div id="card_cvv_err" class="error"></div>
                </div>
                <div class="form-group">
                    <input type="text" name="card_zip" id="card_zip" class="form-control" placeholder=" " value="" maxlength='6' minlength="5" >
                    <label><?php echo $this->lang->line('card_zip') ?></label>                    
                </div>
                <div class="radio-btn-list">
                    <label for="set_as_default_stripecard">
                        <input type="checkbox" id="set_as_default_stripecard" name="set_as_default_stripecard" value="yes">
                        <span><?php echo $this->lang->line('set_as_default'); ?></span>
                    </label>
                </div>
                <div class="save-btn">
                    <button type="submit" name="submit_card" id="submit_card" value="Save" class="btn btn-primary"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Show add stripe card page :: End -->

    <!-- delete account modal : start -->
    <div class="modal modal-main delete-address_" id="delete-stripeaccount">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('delete_card') ?>?</h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <form id="form_card_delete" name="form_card_delete" method="post" class="form-horizontal float-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <p><?php echo $this->lang->line('delete_module'); ?><p>
                    <div class="action-btn">                        
                        <input type="hidden" name="delete_PaymentMethodid" id="delete_PaymentMethodid" value="">
                        <input type="hidden" name="delete_stripecus_id" id="delete_stripecus_id" value="">
                        <input type="submit" name="delete_cardbtn" id="delete_cardbtn" value="<?php echo $this->lang->line('delete') ?>" class="btn btn-primary">
                        <input type="button" name="cancel" id="cancel" value="<?php echo $this->lang->line('cancel') ?>" class="btn btn-primary" data-dismiss="modal">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- delete account modal : end -->
    <?php //driver tip modal :: start ?>
    <div class="modal payment_card_div" id="driver-tip">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line('driver_tip') ?></h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" onclick="applyTipForOrders('clear')" class="close" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="alert alert-success display-no" id="drivertip_successmsg"></div>
                    <div class="driver-tip-form" id="driver-tip-form">
                    </div>
                    <div class="stripediv display-no">
                        <form id="form_user_details" name="form_user_details" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- stripe new changes :: 28feb2022 start -->
                                    <div class="old-card-group" id="listall_card">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="payment_label payment-checkout-new">
                                                <div class="radio-btn-list">
                                                    <label class="payment_label">
                                                        <?php $singlepaymentstyle = '';
                                                        $singlepaymentdisable = '';
                                                        if($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('UserType') == 'Agent') {
                                                            $singlepaymentstyle = 'style="display: none;" checked="checked"';
                                                            $singlepaymentdisable = 'disabled';
                                                        } ?> 
                                                        <div class="radio-btn-list">
                                                            <label class="payment_label">
                                                                <input type="radio" name="payment-source-btn" <?php echo $singlepaymentstyle; ?> type="radio" name="payment-source-btn" value="newcard" id="new-card-radio" onclick="togglecardbutton(this.value);">
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
                                        <?php //go back to select driver tip :: start ?>
                                        <div class="col-md-12">
                                            <div id="change-tip-div" class="change-tip-div">
                                                <div class="radio-btn-list">
                                                    <label for="change_tip_val">
                                                        <input type="hidden" id="payment_option_val" name="payment_option_val" value="">
                                                        <b><a href="javascript:void(0)" style="color:#0060a9" onclick="backToSelectTip()"><?php echo $this->lang->line('change_tip_amount') ?></a></b>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php //go back to select driver tip :: end ?>
                                        <div class="col-md-12">
                                            <p id="card-error" role="alert" style="color:#fa755a;"></p>
                                            <p class="result-message hidden"><?php echo $this->lang->line('payment_succeeded'); ?></p>
                                        </div>
                                    </div>
                                    <!-- stripe new changes :: 28feb2022 end -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php //driver tip modal :: end
    //wallet topup modal :: start ?>
    <div class="modal payment_card_div" id="add-wallet-money">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><span id="wallet-topup-title"><?php echo $this->lang->line('add_money') ?></span></h4>
                    <button type="button" alt="<?php echo $this->lang->line('close') ?>" title="<?php echo $this->lang->line('close') ?>" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="alert alert-success display-no" id="wallet_topup_successmsg"></div>
                    <div id="error-msg" class="alert alert-danger display-no"></div>
                    <div class="wallet_topup_div">
                        <form id="form_wallet_topup" name="form_wallet_topup" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="text" name="topup_amount" id="topup_amount" class="form-control" placeholder="<?php echo $this->lang->line('amount'); ?>">
                                    <div id="topup_amount_err" class="error"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- stripe new changes :: 28feb2022 start -->
                                    <div class="old-card-group" id="listall_cards_fortopup">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="payment_label payment-checkout-new">
                                                <div class="radio-btn-list">
                                                    <label class="payment_label">
                                                        <?php $singlepaymentstyle = '';
                                                        $singlepaymentdisable = '';
                                                        if($this->session->userdata('is_guest_checkout') == 1 || $this->session->userdata('UserType') == 'Agent') {
                                                            $singlepaymentstyle = 'style="display: none;" checked="checked"';
                                                            $singlepaymentdisable = 'disabled';
                                                        } ?> 
                                                        <div class="radio-btn-list">
                                                            <label class="payment_label">
                                                                <input type="radio" name="payment-source-btn-forwallet" <?php echo $singlepaymentstyle; ?> type="radio" value="newcard" id="new-card-radio-forwallet" onclick="togglecardbutton_forwallet(this.value);">
                                                                <span <?php echo $singlepaymentstyle; ?>></span>
                                                            </label>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div id="card-element-topup"><!--Stripe.js injects the Card Element--></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <button id="submit_stripe_forwallet" <?php echo $singlepaymentdisable; ?>>
                                                <div class="spinner hidden" id="spinner_topup"></div>
                                                <span id="button-text-wallet"><?php echo $this->lang->line('pay'); ?></span>
                                            </button>
                                        </div>
                                        <?php if($this->session->userdata('UserType') == 'User') { ?>
                                            <div class="col-md-12">
                                                <div id="save_card_checkbox_forwallet" class="save_card_checkbox_forwallet">
                                                    <div class="radio-btn-list">
                                                        <label for="save_card_checkbox_val_forwallet">
                                                            <input type="checkbox" id="save_card_checkbox_val_forwallet" name="save_card_checkbox_val_forwallet" value="yes">
                                                            <span><?php echo $this->lang->line('do_you_want_to_save_card'); ?></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-12">
                                            <p id="card-error-forwallet" role="alert" style="color:#fa755a;"></p>
                                            <p class="result-message hidden"><?php echo $this->lang->line('payment_succeeded'); ?></p>
                                        </div>
                                    </div>
                                    <!-- stripe new changes :: 28feb2022 end -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php //wallet topup modal ::end ?>

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/front/js/scripts/admin-management-front.js"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script type="text/javascript">
    var map, marker;
    jQuery(document).ready(function() {
        initMap();
        // initAutocomplete('add_address_area');
        initAutocomplete('address_field');
        // auto detect location if even searched once.
        if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
            getLocation('my_profile');
        }
        else
        {
            getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'my_profile');
        }
        var address = default_country_fromheader;
        var default_latitude = 0;
        var default_longitude = 0;
        if (address !== "undefined" && address !== null ) { 
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    default_latitude = results[0].geometry.location.lat();
                    default_longitude = results[0].geometry.location.lng();  
                    $("#default_latitude").val(default_latitude);
                    $("#default_longitude").val(default_longitude);
                }
            });
        }
        function initMap(){
            var bounds = new google.maps.LatLngBounds();
            map = new google.maps.Map(document.getElementById('map_canvas'),
            {
                center: new google.maps.LatLng(default_latitude,default_longitude),
                zoom: 16
            });
            geocoder = new google.maps.Geocoder();
            var position = new google.maps.LatLng(default_latitude,default_longitude);
            marker = new google.maps.Marker({
                position: position,
                draggable: true,
                map: map,
            });
            bounds.extend(position);
            infowindow = new google.maps.InfoWindow({
              size: new google.maps.Size(150, 50)
            });
            google.maps.event.addListener(marker, 'dragend', function(evt) {
                geocodePosition(marker.getPosition());
                $('#latitude').val(evt.latLng.lat());
                $('#longitude').val(evt.latLng.lng());
            });
        }
        // google address autocomplete off 
        $('#address_field').on('focus',function(){
            $(this).attr('autocomplete', 'nope');           
        });
    });
    $("#edit-profile").click(function(){
      $('#preview').attr('src', '').attr('style','display: none;');
    });
    function readURL(input)
    {   
        var fileInput = document.getElementById('image');
        var filePath = fileInput.value;
        var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        if(input.files[0].size <= 10506316){ // 10 MB
            if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result).attr('style','display: inline-block;');
                    $("#old").hide();
                    $('#errormsg').html('').hide();
                }
                reader.readAsDataURL(input.files[0]);
                }
            }
            else{
                $('#preview').attr('src', '').attr('style','display: none;');
                $('#errormsg').html("<?php echo $this->lang->line('file_extenstion'); ?>").show();
                $('#image').val('');
                $("#old").show();
            }
        }else{
            $('#preview').attr('src', '').attr('style','display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
            $('#image').val('');
            $("#old").show();
        }
    }
    google.maps.event.addDomListener(window, 'load', function() {
        const optionsObj = {
            //componentRestrictions: { country: ["us","in","pk"]},
            fields: ["formatted_address","address_components", "geometry", "icon", "name"],
        };        
        var places = new google.maps.places.Autocomplete(document.getElementById('address_field'), optionsObj);
        //var places = new google.maps.places.Autocomplete(document.getElementById('address_field'),{ types: ['address'] },{ types: ['formatted_address'] });
        google.maps.event.addListener(places, 'place_changed', function() {
            var place = places.getPlace();
            var  value = place.formatted_address.split(",");
            if(place.name == value[0]){
                document.getElementById("address_field").value = place.formatted_address;    
            }else{
                document.getElementById("address_field").value = place.name+', '+place.formatted_address;
            }
            document.getElementById("city").value = '';
            document.getElementById("state").value = '';
            document.getElementById("country").value = '';
            document.getElementById("zipcode").value = '';
            $.each(place.address_components, function( index, value ) {
                $.each(value.types, function( index, types ) {
                    if(types == 'administrative_area_level_2'){
                       document.getElementById("city").value = value.long_name;
                    }
                    if(types == 'administrative_area_level_1'){
                       document.getElementById("state").value = value.long_name;
                    }
                    if(types == 'country'){
                       document.getElementById("country").value = value.long_name;
                    }
                    if(types == 'postal_code'){
                       document.getElementById("zipcode").value = value.long_name;
                    }
                });
            });
        });
    });
    // show delete address popup
    function showDeleteAcc(){
        //$('#delete_address_id').val(address_id);
        $('#delete-account').modal('show');
    }
    // delete address
    function deleteAccount(){
        var user_id = '<?php echo $this->session->userdata('UserID');?>';
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+ 'myprofile/ajaxDeleteAccount' ,
            data : {'user_id':user_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                //redirect to logout.
                logout();
                //window.location.href = BASEURL+"home/logout";
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                alert(errorThrown);
            }
        });
    }
    </script>
    <script type="text/javascript">
        function copyToClipboard() {
            var copyText = document.getElementById("ref_code").value;
            // Create a dummy input to copy the string array inside it
            var dummy = document.createElement("input");
            // Add it to the document
            document.body.appendChild(dummy);
            // Set its ID
            dummy.setAttribute("id", "dummy_id");
            // Output the array into it
            document.getElementById("dummy_id").value=copyText;
            // Select it
            dummy.select();
            // Copy its contents
            document.execCommand("copy");
            // Remove it as its not needed anymore
            document.body.removeChild(dummy);
            $('#copied-success').fadeIn(800);
            $('#copied-success').fadeOut(800);
        }
    </script>
    <script type="text/javascript">
    <?php if($profile->login_type != 'facebook' && $profile->login_type != 'google') { ?>
        /*document.querySelector("#password").classList.add("input-password");document.getElementById("toggle-password1").classList.remove("d-none");const passwordInput1=document.querySelector("#password");const togglePasswordButton1=document.getElementById("toggle-password1");togglePasswordButton1.addEventListener("click",togglePassword1);function togglePassword1(){if(passwordInput1.type==="password"){passwordInput1.type="text";togglePasswordButton1.setAttribute("aria-label","Hide password.")}else{passwordInput1.type="password";togglePasswordButton1.setAttribute("aria-label","Show password as plain text. "+"Warning: this will display your password on the screen.")}}
        document.querySelector("#confirm_password").classList.add("input-password");document.getElementById("toggle-password2").classList.remove("d-none");const passwordInput2=document.querySelector("#confirm_password");const togglePasswordButton2=document.getElementById("toggle-password2");togglePasswordButton2.addEventListener("click",togglePassword2);function togglePassword2(){if(passwordInput2.type==="password"){passwordInput2.type="text";togglePasswordButton2.setAttribute("aria-label","Hide password.")}else{passwordInput2.type="password";togglePasswordButton2.setAttribute("aria-label","Show password as plain text. "+"Warning: this will display your password on the screen.")}}*/
    <?php } ?>
    </script>
    <script type="text/javascript">
        //intl-tel-input plugin
        var onedit_iso = '';
        <?php if($profile->phone_code) {
            $onedit_iso = $this->common_model->getIsobyPhnCode($profile->phone_code); ?>
            onedit_iso = '<?php echo $onedit_iso; ?>'; //saved in session
            <?php $iso = $this->common_model->country_iso_for_dropdown();
            $default_iso = $this->common_model->getDefaultIso(); ?>

            var country_iso = <?php echo json_encode($iso); ?>; //all active countries
            var default_iso = <?php echo json_encode($default_iso); ?>; //default country
            default_iso = (default_iso)?default_iso:'';
            var initial_preferred_iso = (onedit_iso)?onedit_iso:default_iso;
            //editprofile form intel plugin on number :: start
            // Initialize the intl-tel-input plugin
            const phoneInputField = document.querySelector("#phone_number");
            const phoneInput = window.intlTelInput(phoneInputField, {
                initialCountry: onedit_iso,
                preferredCountries: [initial_preferred_iso],
                onlyCountries: country_iso,
                separateDialCode:true,
                autoPlaceholder:"polite",
                formatOnDisplay:false,
                utilsScript: BASEURL+'assets/admin/plugins/intl_tel_input/utils.js',
                    //"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });
            $(document).on('input','#phone_number',function(){
                event.preventDefault();
                var phoneNumber = phoneInput.getNumber();
                if (phoneInput.isValidNumber()) {
                    var countryData = phoneInput.getSelectedCountryData();
                    var countryCode = countryData.dialCode;
                    $('#phone_code').val(countryCode);
                    phoneNumber = phoneNumber.replace('+'+countryCode,'');
                    $('#phone_number').val(phoneNumber);
                }
            });
            $(document).on('focusout','#phone_number',function(){
                event.preventDefault();
                var phoneNumber = phoneInput.getNumber();
                if (phoneInput.isValidNumber()) {
                    var countryData = phoneInput.getSelectedCountryData();
                    var countryCode = countryData.dialCode;
                    $('#phone_code').val(countryCode);
                    phoneNumber = phoneNumber.replace('+'+countryCode,'');
                    $('#phone_number').val(phoneNumber);
                }
            });
            phoneInputField.addEventListener("close:countrydropdown",function() {
                var phoneNumber = phoneInput.getNumber();
                if (phoneInput.isValidNumber()) {
                    var countryData = phoneInput.getSelectedCountryData();
                    var countryCode = countryData.dialCode;
                    $('#phone_code').val(countryCode);
                    phoneNumber = phoneNumber.replace('+'+countryCode,'');
                    $('#phone_number').val(phoneNumber);
                }
            });
        <?php } ?>
        //editprofile form intel plugin on number :: end

    </script>
    <!-- for review/rating and menu -->
<script type="text/javascript">
$(function() {
    // Check Radio-box
    $(".rating input:radio").filter('[value=3]').prop('checked', true);
    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });
    $('input:radio').change(
      function(){
        var userRating = this.value;
    }); 
    $('#menu_link').click(function(e) {
        $("#menu").delay(100).fadeIn(100);
        $("#review").fadeOut(100);
        $('#review_link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });
    $('#review_link').click(function(e) {
        $("#review").delay(100).fadeIn(100);
        $("#menu").fadeOut(100);
        $('#menu_link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });
});
/*$(document).ready(function(){ 
    setTimeout(function() {
        $('.cancel_order').css("display", "none")
    }, 60000);
});*/

function showNotification(noti_id)
{
    jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+ 'myprofile/ajaxNotification',
    data : {'noti_id':noti_id }, 
    success: function(response) {            
      $('#showNotifi .modal-body .notification_description').html(response)
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        //alert(errorThrown);
    }
  });
  <?php 
    foreach($users_notifications as $key => $value){ ?>
        if('<?php echo $value->entity_id; ?>'== noti_id){
            $('#showNotifi .modal-header .modal-title').text("<?php echo $value->notification_title; ?>");            
        }
    <?php } ?>    
  $('#showNotifi').modal('show');
}
// cancel order button and timer code
$(document).ready(function(){
    //to hide success message after few seconds
    if($('.alert-success').is(':visible')) {
        setTimeout(function(){
            $(".alert-success").empty();
            $(".alert-success").hide();
        }, 5000);
    }

    var idArr = [];
    $(".cancel_timer").each(function(){
        idArr.push($(this).attr("id"));
    });
    if(idArr.length != 0){
        for (let i = 0; i < idArr.length; i++)
        {
            var order_id = idArr[i].slice(-1);
            var orderidval = $("#orderid"+order_id).val(); 
            var status = $('#OrderStatus'+order_id).val();
            var date = $('#OrderDate'+order_id).val();
            CancelOrder(date,status,order_id,orderidval);
            //TimeCounter(date,status,order_id);
        }        
    }
    // Join array elements and display in alert
});
function CancelOrder(time,status,order_id,orderidval){    
    // Set the date we're counting down to
    var countDownDate = new Date(time).getTime();
    // Update the count down every 1 second
    var x = setInterval(function() {
        // Get today's date and time
        var d1 = new Date();
        var udate = d1.toUTCString();
        var d2 = new Date(d1.getUTCFullYear(),d1.getUTCMonth(),d1.getUTCDate(), d1.getUTCHours(),d1.getUTCMinutes(), d1.getUTCSeconds() );
        var now = d2.getTime();
        var additional_time = <?php echo $cancel_order_timer->OptionValue ?> *1000;
        var new_countDownDate  = countDownDate + additional_time;
        // Find the distance between now and the count down date
        var distance = new_countDownDate - now;
        // If the count down is finished, write some text

        // Time calculations for days, hours, minutes and seconds
        var min = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var sec = Math.floor((distance % (1000 * 60)) / 1000) + min*60;

        //Code for find the current order stauts :: Start
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+ 'myprofile/getlatestOrderstaus',
            data : {'order_id':orderidval }, 
            success: function(response) {            
                status = response;

                //Display the result in the element with id="demo"
                if(min>=0 && sec!=0 && status=="placed"){
                    $("#cancel_timer"+order_id).html("<?php echo $this->lang->line("cancel_order_message") ?>"+sec+"<?php echo $this->lang->line("seconds") ?>");
                }            
                if (distance <= 0 || status!="placed") {
                    $('#cancel_order'+order_id).css("display", "none");
                    clearInterval(x);
                    $("#cancel_timer"+order_id).addClass('display-no');
                }              
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert(errorThrown);
            }
        });
        //Code for find the current order stauts :: End
        
    }, 1000);
}
$("#add-stripecard").click(function(){
  $('#preview').attr('src', '').attr('style','display: none;');
});

$("#add-stripecardid").click(function(){
  $('#form_credit_card')[0].reset();
  $('#carderrormsg').addClass('display-no');
  $('#carderrormsg').html('');
  $('#is_editcard').val('no'); 
  $('#payment_method_id').val('');    
  $("#card_number").prop("readonly", false);
  $("#add_stripetitle").html("<?php echo $this->lang->line("add_card"); ?>");  
});
</script>
<script src='<?php echo base_url(); ?>assets/front/js/creditCardValidator.js'></script>
<script type="text/javascript">
$(document).ready(function() {
    //card validation on input fields
    $('#form_credit_card input[type=text]').on('keyup',function(){
        cardFormValidate();
    });
});
$('#add-stripecard').on('hidden.bs.modal', function (e) {
  $('#form_credit_card').validate().resetForm();
  $('#carderrormsg').addClass('display-no');
  $('#carderrormsg').html('');
  $('#is_editcard').val('no'); 
  $('#payment_method_id').val('');    
  $("#card_number").prop("readonly", false);
  $("#submit_card").attr("disabled", false);
  $("#add_stripetitle").html("<?php echo $this->lang->line("add_card"); ?>");
});
</script>
<script type="text/javascript">
    const togglePassword = document.querySelector('#togglePasswordshow');
    const password = document.querySelector('#password');
      togglePassword.addEventListener('click', function (e) {
      // toggle the type attribute
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      // toggle the eye / eye slash icon
      this.classList.toggle('close-eye');
    });

    const togglePasswordconfirm = document.querySelector('#togglePasswordshowconfirm');
    const confirm_password = document.querySelector('#confirm_password');
      togglePasswordconfirm.addEventListener('click', function (e) {
      // toggle the type attribute
      const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
      confirm_password.setAttribute('type', type);
      // toggle the eye / eye slash icon
      this.classList.toggle('close-eye');
    });
</script>
<!-- Stripe JavaScript library -->
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    $('#driver-tip').on('hidden.bs.modal', function (e) {
        applyTipForOrders('clear');
    });

    //Code for paypal tip payment :: Start    
    function mount_paypal_element()
    {
        var tip_amount = $('#driver_tip').val();
        var payment_option = $("input[name='payment_option']:checked").val();        
        if($('.paypal-button').length <=0)
        {
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
                onInit: function(actions)
                {
                    paypalActions = actions;
                    paypalActions.enable();
                },
                validate: function(actions) {                    
                    actions.enable(); // Allow for validation in onClick()
                    paypalActions = actions; // Save for later enable()/disable() calls                    
                },
                onClick: function()
                {
                    $('#driver-tip').modal('hide');
                    paypalActions.enable();                  
                },
                // Set up a payment
                payment: function (data, actions) {
                    var tip_amount = $('#driver_tip').val();                    
                    return actions.payment.create({
                        transactions: [{
                            amount: {
                                total: tip_amount,
                                currency: '<?php echo $currency_symboltemp->currency_code; ?>'
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
                            url : BASEURL+"myprofile/tip_process?paymentID="+data.paymentID+"&token="+data.paymentToken+"&payerID="+data.payerID,
                            cache: false, 
                            processData: false,
                            contentType: false,
                            beforeSend: function(){
                                $('#quotes-main-loader').show();
                            },   
                            success: function(response)
                            {
                                if(response.transaction_id && response.transaction_id != '')
                                {
                                    //order summry start
                                    var str_paymentIntentid = response.transaction_id;
                                    var tip_order_id = $('#tip_order_id').val();
                                    //var tip_amount = $('#driver_tip').val();
                                    var updateordersummary_data = {
                                        tip_order_id_inp : tip_order_id,
                                        payment_option : payment_option,
                                        tip_amount_inp : tip_amount,
                                        tip_transaction_id : str_paymentIntentid,
                                    };
                                    
                                    fetch(BASEURL+"myprofile/updateOrderSummary", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify(updateordersummary_data)
                                    }).then(function(sresult) {
                                        return sresult.json();
                                    }).then(function(sdata) {
                                        
                                        if(sdata.status == 'success')
                                        {
                                            $('#driver-tip').modal('show');
                                            $('#driver-tip-form').addClass('display-no');
                                            $('#quotes-main-loader').hide();
                                            $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                                            $('#drivertip_successmsg').removeClass('display-no');
                                            setTimeout(function() {                                                    
                                                applyTipForOrders('clear');
                                            }, 2000);
                                            setTimeout(function() {
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 3000);
                                        }
                                        else if(sdata.error=='')
                                        {
                                            var refundbox = bootbox.alert({
                                            message: "<?php echo $this->lang->line('refund_err_frontmssg'); ?>",
                                                buttons: {
                                                    ok: {
                                                        label: "<?php echo $this->lang->line('ok'); ?>",
                                                    }
                                                },
                                                callback: function () {
                                                    $('#quotes-main-loader').hide();
                                                    applyTipForOrders('clear');
                                                    $('#driver-tip').modal('hide');
                                                    window.location.href = BASEURL+"myprofile";
                                                }
                                            });
                                            setTimeout(function() {
                                                $('#quotes-main-loader').hide();
                                                refundbox.modal('hide');                                                
                                                applyTipForOrders('clear');
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 10000);
                                        }
                                        else
                                        {
                                            var refundbox = bootbox.alert({
                                            message: "<?php echo $this->lang->line('refund_err_mssg'); ?>",
                                                buttons: {
                                                    ok: {
                                                        label: "<?php echo $this->lang->line('ok'); ?>",
                                                    }
                                                },
                                                callback: function () {
                                                    $('#quotes-main-loader').hide();
                                                    applyTipForOrders('clear');
                                                    $('#driver-tip').modal('hide');
                                                    window.location.href = BASEURL+"myprofile";
                                                }
                                            });
                                            setTimeout(function() {
                                                $('#quotes-main-loader').hide();
                                                refundbox.modal('hide');                                                
                                                applyTipForOrders('clear');
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 10000);
                                        }
                                    });
                                    //order summry end
                                }
                                else
                                {
                                    var refundbox = bootbox.alert({
                                    message: "<?php echo $this->lang->line('refund_err_frontmssg'); ?>",
                                        buttons: {
                                            ok: {
                                                label: "<?php echo $this->lang->line('ok'); ?>",
                                            }
                                        },
                                        callback: function () {
                                            $('#quotes-main-loader').hide();
                                            applyTipForOrders('clear');
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }
                                    });
                                    setTimeout(function() {
                                        $('#quotes-main-loader').hide();
                                        refundbox.modal('hide');                                        
                                        applyTipForOrders('clear');
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 10000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {           
                                //alert(errorThrown);
                                $('#quotes-main-loader').hide();
                                applyTipForOrders('clear');
                                $('#driver-tip').modal('hide');
                                //window.location.href = BASEURL+"myprofile";
                            }
                        });
                        //Redirect to the payment process page                        
                    });
                }
            }, '#paypal-button');
        }
    }
    //Code for paypal tip payment :: End
    var card = '';
    var stripe_info = '<?php echo ($stripe_info->live_publishable_key!= '1')?$stripe_info->live_publishable_key:$stripe_info->test_publishable_key; ?>';
    var stripe = Stripe('<?php echo ($stripe_info->enable_live_mode == '1')?$stripe_info->live_publishable_key:$stripe_info->test_publishable_key; ?>');
    function mount_stripe_element() {
        if(stripe_info!=''){
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
            card = elements.create("card", { hidePostalCode: false,style: style });
            // Stripe injects an iframe into the DOM
            card.mount("#card-element");
            if($("input[name='payment-source-btn']:checked").val() == 'newcard' || $("input[name='payment-source-btn']:checked").val() == undefined){
                $("#save_card_checkbox").show();
            } else {
                $("#save_card_checkbox").hide();
            }
            card.on("change", function (event) {
                // Disable the Pay button if there are no card details in the Element
                document.querySelector("#submit_stripe").disabled = event.empty;
                document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
            });
            card.on('focus', function(event) {
                $("#save_card_checkbox").show();
                document.querySelector('#new-card-radio').checked = true;
                document.querySelector("#submit_stripe").disabled = true;
            });
        }
    }
    $(document).ready(function(){        
        if(stripe_info!=''){
            var form = document.getElementById("form_user_details");
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                
                var radiopaymnetValue = $("input[name='payment-source-btn']:checked").val();
                var save_card_checkbox_val = $("input[name='save_card_checkbox_val']:checked").val();                               
                if(radiopaymnetValue=='newcard')
                {
                    loading(true);
                    //create intent and make payment.
                    fetch(BASEURL+"myprofile/createintent_fordrivertip", {
                        method: "POST",
                        dataType : "html",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        //body: JSON.stringify(intent_data)
                    }).then(function(result) {
                            return result.json();
                    }).then(function(data) {
                        if(data.error){
                            // Show error to your customer
                            showError(data.error);
                        } else {
                            payWithCard(stripe, card, data.clientSecret,data.stripecus_id,data.is_savecard,save_card_checkbox_val);
                        }
                    });
                }
                else
                {
                    loading(true);
                    var element_radio = $("input[name='payment-source-btn']:checked");
                    var radio_paymentmethodid = element_radio.attr("paymentmethodid");

                    var savecard_intentcrt = {
                        payment_method : radio_paymentmethodid,
                    };

                    fetch(BASEURL+"myprofile/create_paymentwithcard", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(savecard_intentcrt)
                    }).then(function(pn_result) {
                        return pn_result.json();
                    }).then(function(pn_data) {
                        if(pn_data.error) {
                            // Show error to your customer
                            showError(pn_data.error);
                        }
                        else if (pn_data.paymentconfirm_status =='requires_action') {
                            // Use Stripe.js to handle required card action
                            stripe.confirmCardPayment(
                            pn_data.clientSecret
                            ).then(function(resulthand) {
                              if (resulthand.error) {
                                // Show `result.error.message` in payment form
                                location.reload();
                                loading(false);                                
                                //document.querySelector("#submit_stripe").disabled = true;
                              }
                              else
                              {
                                    var payment_option =$('#payment_option_val').val(); 
                                    // The payment succeeded!
                                    var str_paymentIntentid = pn_data.paymentIntentid;
                                    var tip_order_id = $('#tip_order_id').val();
                                    var tip_amount = $('#driver_tip').val();
                                    var updateordersummary_data = {
                                        tip_order_id_inp : tip_order_id,
                                        payment_option : payment_option,
                                        tip_amount_inp : tip_amount,
                                        tip_transaction_id : str_paymentIntentid,
                                    };
                                        
                                    fetch(BASEURL+"myprofile/updateOrderSummary", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify(updateordersummary_data)
                                    }).then(function(sresult) {
                                        return sresult.json();
                                    }).then(function(sdata) {
                                        if(sdata.status == 'success'){
                                            loading(false);
                                            $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                                            $('#drivertip_successmsg').removeClass('display-no');
                                            setTimeout(function() {
                                                card.clear();
                                                applyTipForOrders('clear');
                                            }, 2000);
                                            setTimeout(function() {
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 3000);                                        
                                        }else if(sdata.error=='' && sdata.error_message==''){    
                                            var refundbox = bootbox.alert({
                                            message: "<?php echo $this->lang->line('refund_err_frontmssg'); ?>",
                                                buttons: {
                                                    ok: {
                                                        label: "<?php echo $this->lang->line('ok'); ?>",
                                                    }
                                                },
                                                callback: function () {
                                                    card.clear();
                                                    applyTipForOrders('clear');
                                                    $('#driver-tip').modal('hide');
                                                    window.location.href = BASEURL+"myprofile";
                                                }
                                            });
                                            setTimeout(function() {
                                                refundbox.modal('hide');
                                                card.clear();
                                                applyTipForOrders('clear');
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 10000);
                                        } else{
                                            var refundbox = bootbox.alert({
                                            message: "<?php echo $this->lang->line('refund_err_mssg'); ?>",
                                                buttons: {
                                                    ok: {
                                                        label: "<?php echo $this->lang->line('ok'); ?>",
                                                    }
                                                },
                                                callback: function () {
                                                    card.clear();
                                                    applyTipForOrders('clear');
                                                    $('#driver-tip').modal('hide');
                                                    window.location.href = BASEURL+"myprofile";
                                                }
                                            });
                                            setTimeout(function() {
                                                refundbox.modal('hide');
                                                card.clear();
                                                applyTipForOrders('clear');
                                                $('#driver-tip').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 10000);
                                        }
                                    });
                              } 

                            });
                        }
                        else {
                            // The payment succeeded!
                            var payment_option =$('#payment_option_val').val(); 
                            var str_paymentIntentid = pn_data.paymentIntentid;
                            var tip_order_id = $('#tip_order_id').val();
                            var tip_amount = $('#driver_tip').val();
                            var updateordersummary_data = {
                                tip_order_id_inp : tip_order_id,
                                payment_option : payment_option,
                                tip_amount_inp : tip_amount,
                                tip_transaction_id : str_paymentIntentid,
                            };
                                
                            fetch(BASEURL+"myprofile/updateOrderSummary", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(updateordersummary_data)
                            }).then(function(sresult) {
                                return sresult.json();
                            }).then(function(sdata) {
                                if(sdata.status == 'success'){
                                    loading(false);
                                    $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                                    $('#drivertip_successmsg').removeClass('display-no');
                                    setTimeout(function() {
                                        card.clear();
                                        applyTipForOrders('clear');
                                    }, 2000);
                                    setTimeout(function() {
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 3000);
                                }else if(sdata.error=='' && sdata.error_message==''){
                                    var refundbox = bootbox.alert({
                                    message: "<?php echo $this->lang->line('refund_err_frontmssg'); ?>",
                                        buttons: {
                                            ok: {
                                                label: "<?php echo $this->lang->line('ok'); ?>",
                                            }
                                        },
                                        callback: function () {
                                            card.clear();
                                            applyTipForOrders('clear');
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }
                                    });
                                    setTimeout(function() {
                                        refundbox.modal('hide');
                                        card.clear();
                                        applyTipForOrders('clear');
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 10000);
                                } else{
                                    var refundbox = bootbox.alert({
                                    message: "<?php echo $this->lang->line('refund_err_mssg'); ?>",
                                        buttons: {
                                            ok: {
                                                label: "<?php echo $this->lang->line('ok'); ?>",
                                            }
                                        },
                                        callback: function () {
                                            card.clear();
                                            applyTipForOrders('clear');
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }
                                    });
                                    setTimeout(function() {
                                        refundbox.modal('hide');
                                        card.clear();
                                        applyTipForOrders('clear');
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 10000);
                                }
                            });
                        }
                    });
                }
            });
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
                        if (result.error) {
                            // Show error to your customer
                            showError(result.error.message);
                        }
                        else
                        {
                            var payment_option =$('#payment_option_val').val(); 
                            // The payment succeeded!
                            var str_paymentIntentid = result.paymentIntent.id;
                            var tip_order_id = $('#tip_order_id').val();
                            var tip_amount = $('#driver_tip').val();
                            var updateordersummary_data = {
                                tip_order_id_inp : tip_order_id,
                                payment_option : payment_option,
                                tip_amount_inp : tip_amount,
                                tip_transaction_id : str_paymentIntentid,
                            };
                            
                            fetch(BASEURL+"myprofile/updateOrderSummary", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(updateordersummary_data)
                            }).then(function(sresult) {
                                return sresult.json();
                            }).then(function(sdata) {
                                if(sdata.status == 'success'){
                                    //save card
                                    var loggedin_usertype = '<?php echo $this->session->userdata('UserType') ?>';
                                    if(loggedin_usertype == 'User' && is_savecard=='yes' && (save_card_checkbox_val=='yes' && save_card_checkbox_val != undefined)) {
                                        save_carddetail(stripecus_id,result.paymentIntent.payment_method);
                                    } else {
                                        loading(false);
                                        $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                                        $('#drivertip_successmsg').removeClass('display-no');
                                        setTimeout(function() {
                                            card.clear();
                                            applyTipForOrders('clear');
                                        }, 2000);
                                        setTimeout(function() {
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }, 3000);
                                    }
                                } else if(sdata.error=='' && sdata.error_message==''){
                                    var refundbox = bootbox.alert({
                                    message: "<?php echo $this->lang->line('refund_err_frontmssg'); ?>",
                                        buttons: {
                                            ok: {
                                                label: "<?php echo $this->lang->line('ok'); ?>",
                                            }
                                        },
                                        callback: function () {
                                            card.clear();
                                            applyTipForOrders('clear');
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }
                                    });
                                    setTimeout(function() {
                                        refundbox.modal('hide');
                                        card.clear();
                                        applyTipForOrders('clear');
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 10000);
                                }else{
                                    var refundbox = bootbox.alert({
                                    message: "<?php echo $this->lang->line('refund_err_mssg'); ?>",
                                        buttons: {
                                            ok: {
                                                label: "<?php echo $this->lang->line('ok'); ?>",
                                            }
                                        },
                                        callback: function () {
                                            card.clear();
                                            applyTipForOrders('clear');
                                            $('#driver-tip').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }
                                    });
                                    setTimeout(function() {
                                        refundbox.modal('hide');
                                        card.clear();
                                        applyTipForOrders('clear');
                                        $('#driver-tip').modal('hide');
                                        window.location.href = BASEURL+"myprofile";
                                    }, 10000);
                                }
                            });
                        }
                    });
            };
            var save_carddetail = function(stripecus_id,payment_method_id) {
                var savecard_data = {
                    stripecus_id : stripecus_id,
                    payment_method : payment_method_id,
                };
                
                fetch(BASEURL+"myprofile/save_carddetail", {
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
                            loading(false);
                            $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                            $('#drivertip_successmsg').removeClass('display-no');
                        }, 4000);
                        setTimeout(function() {
                            card.clear();
                            applyTipForOrders('clear');
                        }, 6000);
                        setTimeout(function() {
                            $('#driver-tip').modal('hide');
                            window.location.href = BASEURL+"myprofile";
                        }, 7000);
                    } else {
                        $('#drivertip_successmsg').html("<?php echo $this->lang->line('drivertip_successmsg'); ?>");
                        $('#drivertip_successmsg').removeClass('display-no');
                        setTimeout(function() {
                            card.clear();
                            applyTipForOrders('clear');
                        }, 2000);
                        setTimeout(function() {
                            $('#driver-tip').modal('hide');
                            window.location.href = BASEURL+"myprofile";
                        }, 3000);
                    }
                });
            };
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
        }
    });
    function togglecardbutton(radiovalue) {
        if(radiovalue == "newcard") {
            $("#submit_stripe").prop("disabled",true);
            $("#save_card_checkbox").show();
        } else {
            card.clear();
            $("#submit_stripe").prop("disabled",false);
            $("#save_card_checkbox").hide();
        }
    }
    function backToSelectTip() {
        $('#tip_submit_btn').attr('disabled',false);
        $("#tip_clear_btn").attr("disabled", false);

        $('#driver-tip-form').removeClass('display-no');
        $('.stripediv').addClass('display-no');
    }
</script>
<?php //wallet topup changes :: start ?>
<script type="text/javascript">
    var card_topup = '';
    function mount_stripe_element_for_wallettopup() {
        if(stripe_info!=''){
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
            card_topup = elements.create("card", { hidePostalCode: false,style: style });
            // Stripe injects an iframe into the DOM
            card_topup.mount("#card-element-topup");
            if($("input[name='payment-source-btn-forwallet']:checked").val() == 'newcard' || $("input[name='payment-source-btn-forwallet']:checked").val() == undefined){
                $("#save_card_checkbox_forwallet").show();
            } else {
                $("#save_card_checkbox_forwallet").hide();
            }
            card_topup.on("change", function (event) {
                // Disable the Pay button if there are no card details in the Element
                document.querySelector("#submit_stripe_forwallet").disabled = event.empty;
                document.querySelector("#card-error-forwallet").textContent = event.error ? event.error.message : "";
            });
            card_topup.on('focus', function(event) {
                $("#save_card_checkbox_forwallet").show();
                document.querySelector('#new-card-radio-forwallet').checked = true;
                document.querySelector("#submit_stripe_forwallet").disabled = true;
            });
        }
    }

    $(document).ready(function() { 
        if(stripe_info!='') {
            var form = document.getElementById("form_wallet_topup");
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                $("#form_wallet_topup").validate();
                if (!$("#form_wallet_topup").valid()) { 
                    return false;
                } else {
                    var radiopayment_value_forwallet = $("input[name='payment-source-btn-forwallet']:checked").val();
                    var save_card_checkbox_val_forwallet = $("input[name='save_card_checkbox_val_forwallet']:checked").val();
                    var topup_amount = $('#topup_amount').val();
                    if(topup_amount > 0) {
                        $("#topup_amount_err").html('');
                        $("#topup_amount_err").hide();
                        $('#submit_stripe_forwallet').attr('disabled',false);
                        if(radiopayment_value_forwallet == 'newcard') {
                            loading_forwallet(true);
                            var intent_data = {
                                intent_for : 'wallet_topup',
                                topup_amount : topup_amount,
                            };
                            //create intent and make payment.
                            fetch(BASEURL+"myprofile/createintent_fordrivertip", {
                                method: "POST",
                                dataType : "html",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(intent_data)
                            }).then(function(result) {
                                    return result.json();
                            }).then(function(data) {
                                if(data.error){
                                    // Show error to your customer
                                    showErrorForWallet(data.error);
                                } else {
                                    payWithCardForWallet(stripe, card_topup, data.clientSecret,data.stripecus_id,data.is_savecard,save_card_checkbox_val_forwallet,topup_amount);
                                }
                            });
                        }
                        else
                        {
                            loading_forwallet(true);
                            var element_radio = $("input[name='payment-source-btn-forwallet']:checked");
                            var radio_paymentmethodid = element_radio.attr("paymentmethodid");

                            var savecard_intentcrt = {
                                payment_for : 'wallet_topup',
                                topup_amount : topup_amount,
                                payment_method : radio_paymentmethodid,
                            };

                            fetch(BASEURL+"myprofile/create_paymentwithcard", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(savecard_intentcrt)
                            }).then(function(pn_result) {
                                return pn_result.json();
                            }).then(function(pn_data) {
                                if(pn_data.error) {
                                    // Show error to your customer
                                    showErrorForWallet(pn_data.error);
                                }
                                else if (pn_data.paymentconfirm_status =='requires_action') {
                                    // Use Stripe.js to handle required card action
                                    stripe.confirmCardPayment(
                                    pn_data.clientSecret
                                    ).then(function(resulthand) {
                                      if (resulthand.error) {
                                        // Show `result.error.message` in payment form
                                        location.reload();
                                        loading_forwallet(false);                                
                                        //document.querySelector("#submit_stripe").disabled = true;
                                      } else {
                                            // The payment succeeded!
                                            var str_paymentIntentid = pn_data.paymentIntentid;
                                            var updatewallethistory_data = {
                                                topup_amount : topup_amount,
                                                wallet_transaction_id : str_paymentIntentid,
                                            };
                                            fetch(BASEURL+"myprofile/updateWalletHistory", {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type": "application/json"
                                                },
                                                body: JSON.stringify(updatewallethistory_data)
                                            }).then(function(sresult) {
                                                return sresult.json();
                                            }).then(function(sdata) {
                                                if(sdata.status == 'success') {
                                                    loading_forwallet(false);
                                                    $('#wallet_topup_successmsg').html("<?php echo $this->lang->line('wallet_topup_successmsg'); ?>");
                                                    $('#wallet_topup_successmsg').removeClass('display-no');
                                                    setTimeout(function() {
                                                        card_topup.clear();
                                                    }, 2000);
                                                    setTimeout(function() {
                                                        $('#add-wallet-money').modal('hide');
                                                        window.location.href = BASEURL+"myprofile";
                                                    }, 3000);
                                                }
                                            });
                                      } 

                                    });
                                }
                                else {
                                    // The payment succeeded!
                                    var str_paymentIntentid = pn_data.paymentIntentid;
                                    var updatewallethistory_data = {
                                        topup_amount : topup_amount,
                                        wallet_transaction_id : str_paymentIntentid,
                                    };
                                        
                                    fetch(BASEURL+"myprofile/updateWalletHistory", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify(updatewallethistory_data)
                                    }).then(function(sresult) {
                                        return sresult.json();
                                    }).then(function(sdata) {
                                        if(sdata.status == 'success') {
                                            loading_forwallet(false);
                                            $('#wallet_topup_successmsg').html("<?php echo $this->lang->line('wallet_topup_successmsg'); ?>");
                                            $('#wallet_topup_successmsg').removeClass('display-no');
                                            setTimeout(function() {
                                                card_topup.clear();
                                            }, 2000);
                                            setTimeout(function() {
                                                $('#add-wallet-money').modal('hide');
                                                window.location.href = BASEURL+"myprofile";
                                            }, 3000);
                                        }
                                    });
                                }
                            });
                        }
                    } else {
                        $("#topup_amount_err").html("<?php echo $this->lang->line('topup_greaterthan_zero'); ?>");
                        $("#topup_amount_err").show();
                        $('#submit_stripe_forwallet').attr('disabled',true);
                    }
                }
            });
            // Calls stripe.confirmCardPayment
            // If the card requires authentication Stripe shows a pop-up modal to
            // prompt the user to enter authentication details without leaving your page.
            var payWithCardForWallet = function(stripe, card_topup, clientSecret,stripecus_id,is_savecard,save_card_checkbox_val_forwallet,topup_amount) {
                loading_forwallet(true);
                stripe
                    .confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: card_topup
                        }
                    })
                    .then(function(result) {
                        if (result.error) {
                            // Show error to your customer
                            showErrorForWallet(result.error.message);
                        }
                        else
                        {
                            // The payment succeeded!
                            var str_paymentIntentid = result.paymentIntent.id;
                            var updatewallethistory_data = {
                                topup_amount : topup_amount,
                                wallet_transaction_id : str_paymentIntentid,
                            };
                            
                            fetch(BASEURL+"myprofile/updateWalletHistory", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(updatewallethistory_data)
                            }).then(function(sresult) {
                                return sresult.json();
                            }).then(function(sdata) {
                                if(sdata.status == 'success'){
                                    //save card
                                    var loggedin_usertype = '<?php echo $this->session->userdata('UserType') ?>';
                                    if(loggedin_usertype == 'User' && is_savecard=='yes' && (save_card_checkbox_val_forwallet == 'yes' && save_card_checkbox_val_forwallet != undefined)) {
                                        save_carddetail_fromwallettopup(stripecus_id,result.paymentIntent.payment_method);
                                    } else {
                                        loading_forwallet(false);
                                        $('#wallet_topup_successmsg').html("<?php echo $this->lang->line('wallet_topup_successmsg'); ?>");
                                        $('#wallet_topup_successmsg').removeClass('display-no');
                                        setTimeout(function() {
                                            card_topup.clear();
                                        }, 2000);
                                        setTimeout(function() {
                                            $('#add-wallet-money').modal('hide');
                                            window.location.href = BASEURL+"myprofile";
                                        }, 3000);
                                    }
                                }
                            });
                        }
                    });
            };
            var save_carddetail_fromwallettopup = function(stripecus_id,payment_method_id) {
                var savecard_data = {
                    stripecus_id : stripecus_id,
                    payment_method : payment_method_id,
                };
                
                fetch(BASEURL+"myprofile/save_carddetail", {
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
                        document.querySelector("#card-error-forwallet").textContent = sdata.error ? sdata.error : "";
                        setTimeout(function() {
                            document.querySelector("#card-error-forwallet").textContent = "";
                            loading_forwallet(false);
                            $('#wallet_topup_successmsg').html("<?php echo $this->lang->line('wallet_topup_successmsg'); ?>");
                            $('#wallet_topup_successmsg').removeClass('display-no');
                        }, 4000);
                        setTimeout(function() {
                            card_topup.clear();
                        }, 6000);
                        setTimeout(function() {
                            $('#add-wallet-money').modal('hide');
                            window.location.href = BASEURL+"myprofile";
                        }, 7000);
                    } else {
                        $('#wallet_topup_successmsg').html("<?php echo $this->lang->line('wallet_topup_successmsg'); ?>");
                        $('#wallet_topup_successmsg').removeClass('display-no');
                        setTimeout(function() {
                            card_topup.clear();
                        }, 2000);
                        setTimeout(function() {
                            $('#add-wallet-money').modal('hide');
                            window.location.href = BASEURL+"myprofile";
                        }, 3000);
                    }
                });
            };
            var showErrorForWallet = function(errorMsgText) {
                loading_forwallet(false);
                var errorMsg = document.querySelector("#card-error-forwallet");
                errorMsg.textContent = errorMsgText;
                setTimeout(function() {
                    errorMsg.textContent = "";
                }, 4000);
            };
            // Show a spinner on payment submission
            var loading_forwallet = function(isLoading) {
                if (isLoading) {
                    // Disable the button and show a spinner
                    document.querySelector("#submit_stripe_forwallet").disabled = true;
                    document.querySelector("#spinner_topup").classList.remove("hidden");
                    document.querySelector("#button-text-wallet").classList.add("hidden");
                } else {
                    document.querySelector("#submit_stripe_forwallet").disabled = false;
                    document.querySelector("#spinner_topup").classList.add("hidden");
                    document.querySelector("#button-text-wallet").classList.remove("hidden");
                }
            };
        }
    });
    function togglecardbutton_forwallet(radiovalue) {
        if(radiovalue == "newcard") {
            $("#submit_stripe_forwallet").prop("disabled",true);
            $("#save_card_checkbox_forwallet").show();
        } else {
            card_topup.clear();
            $("#submit_stripe_forwallet").prop("disabled",false);
            $("#save_card_checkbox_forwallet").hide();
        }
    }
    jQuery("#form_wallet_topup").validate({  
      rules: {    
        topup_amount: {
          required: true,
          number: true,
          twodecimalpoints:true
        }
      }  
    });
    $('#topup_amount').on('input', function() {
        var topup_amount_val = $('#topup_amount').val();        
        if(topup_amount_val != '') {
            if(topup_amount_val > 0) {
                $("#topup_amount_err").html("");
                $("#topup_amount_err").hide();
                $('#submit_stripe_forwallet').attr('disabled',false);
            } else {
                $("#topup_amount_err").html("<?php echo $this->lang->line('topup_greaterthan_zero'); ?>");
                $("#topup_amount_err").show();
                $('#submit_stripe_forwallet').attr('disabled',true);
            }
        } else {
            $("#topup_amount_err").html("");
            $("#topup_amount_err").hide();
            $('#submit_stripe_forwallet').attr('disabled',false);
        }
    });
    jQuery.validator.addMethod("twodecimalpoints", function(value, element, params) {
        // Convert to String
        const numStr = String(value);
        var no_of_decimal_points = 0;
        // String Contains Decimal
        if (numStr.includes('.')) {
            no_of_decimal_points = numStr.split('.')[1].length;
        }
        if(no_of_decimal_points > 2) {
            return false;
        } else {
            return true;
        }
    }, custom_tip_decimal_error);

</script>
<?php //wallet topup changes :: end ?>
<script type="text/javascript">
    $(document).ready(function(){
        var book_url = window.location.href;
        book_url = book_url.substring(book_url.lastIndexOf('/') + 1).substring(1);
        if(book_url=='bookmarks'){
            $('#tab_bookmark a').click();    
        }
        $("#bookmark_head").click(function(){
            event.preventDefault();
            $('#tab_bookmark a').click();
        });
        /*if(performance.navigation.type == performance.navigation.TYPE_RELOAD) {
            window.location = BASEURL+'myprofile';
        }*/
    });
</script>
<?php $this->load->view('footer'); ?>