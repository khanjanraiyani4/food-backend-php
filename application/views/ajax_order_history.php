<div class="col-xl-3 col-lg-4">
    <div class="sidebar-menu-main">
        <div class="sidebar-menu">
            <div class="ordering-title">
                <h6><?php echo $this->lang->line('ordering') ?></h6>
            </div>
            <ul>
                <li class="active"><a href="javascript:void(0)" onclick="myOrderHistory()"><?php echo $this->lang->line('order_history') ?></a></li>
                <li><a href="javascript:void(0)" onclick="myBookings()"><?php echo $this->lang->line('my_bookings') ?></a></li>
                <li><a href="javascript:void(0)" onclick="myWallet()"><?php echo $this->lang->line('wallet_history') ?></a></li>
                <li><a href="javascript:void(0)" onclick="myAddresses()"><?php echo $this->lang->line('my_addresses') ?></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="col-xl-9 col-lg-8">
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
        <div class="profile-content-main">
            <div class="tab-content">
                <div id="past-orders" class="tab-pane">
                    <div class="row orders-box-row">
                        <?php if (!empty($past_orders)) {
                            foreach ($past_orders as $key => $value) {
                                if ($key <= 7) { ?>
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="ordering-box-main">
                                            <div class="ordering-box-top">
                                                <div class="ordering-box-img">
                                                    <div class="ordering-img">
                                                        <?php $image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image']) && $value['restaurant_image']!='')?(image_url.$value['restaurant_image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
                                                    </div>
                                                </div>
                                                <div class="ordering-box-text">
                                                    <h6><?php echo $value['restaurant_name']; ?></h6>
                                                    <?php $rating_txt = ($value['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
                                                    <p><?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].' ('.$value['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> #Order Id - <?php echo $value['order_id']; ?></p>
                                                    <strong><?php echo $this->lang->line('price') ?> : <span>$<?php echo $value['price'][3]['value']; ?></span></strong>
                                                </div>
                                            </div>
                                            <div class="ordering-box-bottom">
                                                <span class="date-icon"><?php echo date("d M Y",strtotime($value['order_date'])); ?></span>
                                                <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                <div class="ordering-btn">
                                                    <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }
                        } ?>
                    </div>
                    <?php if (count($past_orders) > 8) { ?>
                        <div class="row orders-box-row" id="all_past_orders">
                            <?php foreach ($past_orders as $key => $value) {
                                if ($key > 7) { ?>
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="ordering-box-main">
                                            <div class="ordering-box-top">
                                                <div class="ordering-box-img">
                                                    <div class="ordering-img">
                                                        <?php $image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image']) && $value['restaurant_image']!='')?(image_url.$value['restaurant_image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
                                                    </div>
                                                </div>
                                                <div class="ordering-box-text">
                                                    <h6><?php echo $value['restaurant_name']; ?></h6>
                                                    <p><?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> #Order Id - <?php echo $value['order_id']; ?></p>
                                                    <strong><?php echo $this->lang->line('price') ?> : <span>$<?php echo $value['price'][3]['value']; ?></span></strong>
                                                </div>
                                            </div>
                                            <div class="ordering-box-bottom">
                                                <span class="date-icon"><?php echo date("d M Y",strtotime($value['order_date'])); ?></span>
                                                <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                <div class="ordering-btn">
                                                    <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                        <div class="col-lg-12">
                            <div id="more_past_orders" class="load-more-btn">
                                <button class="btn" onclick="moreOrders('past')"><?php echo $this->lang->line('load_more') ?></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div id="current-orders" class="tab-pane show active">
                    <div class="row orders-box-row">
                        <?php if (!empty($in_process_orders)) {
                            foreach ($in_process_orders as $key => $value) {
                                if ($key <= 7) { ?>
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="ordering-box-main">
                                            <div class="ordering-box-top">
                                                <div class="ordering-box-img">
                                                    <div class="ordering-img">
                                                        <?php $image = (file_exists(FCPATH.'uploads/'.$value['restaurant_image'])&& $value['restaurant_image']!='')?(image_url.$value['restaurant_image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
                                                    </div>
                                                </div>
                                                <div class="ordering-box-text">
                                                    <h6><?php echo $value['restaurant_name']; ?></h6>
                                                    <p><?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> #Order Id - <?php echo $value['order_id']; ?></p>
                                                    <strong><?php echo $this->lang->line('price') ?> : <span>$<?php echo $value['price'][3]['value']; ?></span></strong>
                                                </div>
                                            </div>
                                            <div class="ordering-box-bottom">
                                                <span class="date-icon"><?php echo date("d M Y",strtotime($value['order_date'])); ?></span>
                                                <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                <div class="ordering-btn">
                                                    <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                    <a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])); ?>" class="btn"><?php echo $this->lang->line('track_order') ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }
                        } ?>
                    </div>
                    <?php if (count($in_process_orders) > 8) { ?>
                        <div class="row orders-box-row display-no" id="all_current_orders">
                            <?php foreach ($in_process_orders as $key => $value) {
                                if ($key > 7) { ?>
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="ordering-box-main">
                                            <div class="ordering-box-top">
                                                <div class="ordering-box-img">
                                                    <div class="ordering-img">
                                                        <?php $image = (file_exists($value['restaurant_image']) && $value['restaurant_image']!='')?(image_url.$value['restaurant_image']):(default_img); ?>
                                                        <img src="<?php echo $image;?>"> 
                                                    </div>
                                                </div>
                                                <div class="ordering-box-text">
                                                    <h6><?php echo $value['restaurant_name']; ?></h6>
                                                    <p><?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> #Order Id - <?php echo $value['order_id']; ?></p>
                                                    <strong><?php echo $this->lang->line('price') ?> : <span>$<?php echo $value['price'][3]['value']; ?></span></strong>
                                                </div>
                                            </div>
                                            <div class="ordering-box-bottom">
                                                <span class="date-icon"><?php echo date("d M Y",strtotime($value['order_date'])); ?></span>
                                                <span class="relivered-icon"><?php echo $value['order_status']; ?></span>
                                                <div class="ordering-btn">
                                                    <button class="btn" data-toggle="modal" onclick="order_details(<?php echo $value['order_id']; ?>)"><?php echo $this->lang->line('view_details') ?></button>
                                                    <a href="<?php echo base_url().'order/track_order/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['order_id'])); ?>" class="btn"><?php echo $this->lang->line('track_order') ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                        <div class="col-lg-12">
                            <div id="more_in_process_orders" class="load-more-btn">
                                <button class="btn" onclick="moreOrders('process')"><?php echo $this->lang->line('load_more') ?></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>