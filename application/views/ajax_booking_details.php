<?php $this->db->select('OptionValue');
$enable_review = $this->db->get_where('system_option',array('OptionSlug'=>'enable_review'))->first_row();
$show_restaurant_reviews = ($enable_review->OptionValue=='1')?1:0; ?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title"><?php echo $this->lang->line('booking_details') ?></h4>
            <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <?php if (!empty($booking_details[0])) {?>
                <div class="order-detail-head">      
                    <div class="order-detail-img-main">
                        <div class="order-detail-img"> 
                            <?php $image = (file_exists(FCPATH.'uploads/'.$booking_details[0]['image']) && $booking_details[0]['image']!='') ?  image_url. $booking_details[0]['image'] : default_icon_img; ?>
                            <img src="<?php echo $image;?>"> 
                        </div>
                    </div>
                    <div class="detail-content">
                        <h6><?php echo $booking_details[0]['name']; ?>  <?php if($show_restaurant_reviews) { 
                            $rating_txt = ($booking_details[0]['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); 
                            echo ($booking_details[0]['ratings'] > 0)?'<strong>'.$booking_details[0]['ratings'].' ('.$booking_details[0]['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; } ?> </h6>
                        <p><?php echo $booking_details[0]['address']; ?> </p>
                        <br><strong class="event_status"><?php echo $this->lang->line('booking_status') ?> : <?php echo $this->lang->line($booking_details[0]['event_status']).$booking_details[0]['event_cancel_reason'];?></strong>
                        <?php if($booking_details[0]['additional_request'] && $booking_details[0]['additional_request'] != " "){  ?>
                            <br><strong class="event_status"><?php echo $this->lang->line('additional_comment') ?> : <?php echo $booking_details[0]['additional_request'];?></strong>
                        <?php } ?>
                    </div>
                </div>
                <div class="detail-content-middel">
                    <?php if (!empty($booking_details[0]['package_name'])) { ?>
                        <div class="content-middel-title">
                            <h5><?php echo $this->lang->line('packages') ?></h5>
                        </div>
                        <div class="detail-list-box">
                            <div class="detail-list">
                                <div class="detail-list-img">
                                    <div class="list-img">  
                                         <?php $image = (file_exists(FCPATH.'uploads/'.$booking_details[0]['package_image']) && $booking_details[0]['package_image']!='') ?  image_url. $booking_details[0]['package_image'] : default_icon_img; ?>
                                        <img src="<?php echo $image;?>"> 
                                    </div>
                                </div>
                                <div class="detail-list-content"> 
                                    <div class="detail-list-text">
                                        <strong><?php echo $booking_details[0]['package_name']; ?></strong>
                                        <p><?php echo $booking_details[0]['package_detail']; ?></p>
                                        <strong><?php echo currency_symboldisplay($booking_details[0]['package_price'],$booking_details[0]['currency_symbol']); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="booking-option-main">
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/avatar-man.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('no_of_people') ?></span>
                                    <strong><?php echo $booking_details[0]['no_of_people']; ?> <?php echo $this->lang->line('people'); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/pick-date.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('event_date') ?></span>
                                    <strong><?php echo $this->common_model->dateFormat($booking_details[0]['booking_date']);?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/dining-time.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('dining_time') ?></span>
                                    <strong><?php echo $this->common_model->timeFormat($booking_details[0]['booking_date']);?></strong>
                                </div>
                            </div>
                            <div class="add-cart-item">
                            </div>
                        </div>
                        <div class="booking-option">
                            <div class="booking-option-cont">
                                <div class="option-img">
                                    <img src="<?php echo base_url();?>assets/front/images/pick-date.png">
                                </div>
                                <div class="booking-option-text">
                                    <span><?php echo $this->lang->line('booking_date_txt') ?></span>
                                    <strong><?php echo $this->common_model->dateFormat($booking_details[0]['created_date']);?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>