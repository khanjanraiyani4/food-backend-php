<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title"><?php echo $this->lang->line('order_details') ?></h4>
            <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="order-detail-head">      
                <div class="order-detail-img-main">
                    <div class="order-detail-img">
                        <?php $image = (file_exists(FCPATH.'uploads/'.$order_details[0]['restaurant_image']) && $order_details[0]['restaurant_image']!='')?(image_url.$order_details[0]['restaurant_image']):(default_icon_img); ?>
                        <img src="<?php echo $image;?>">  
                    </div>
                </div>
                <div class="detail-content">
                    <h6><?php echo $order_details[0]['restaurant_name'];
                        $rating_txt = ($order_details[0]['restaurant_reviews_count'] > 1)?$this->lang->line('ratings'):$this->lang->line('rating'); ?>
                        <?php echo ($order_details[0]['ratings'] > 0)?'<strong>'.$order_details[0]['ratings'].' ('.$order_details[0]['restaurant_reviews_count'].' '.strtolower($rating_txt).')'.'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
                    </h6>

                    <span>#<?php echo $this->lang->line('orderid') ?> - <?php echo $order_details[0]['order_id']; ?></span>
                    <?php if($order_details[0]['timings']['closing'] == "Closed"){ ?>
                        <div class="openclose closed"><?php echo $this->lang->line('closed'); ?></div>
                    <?php } ?>
                    <p><?php echo $order_details[0]['restaurant_address']; ?> </p>
                </div>
            </div>
            <div class="detail-content-middel">
                <div class="content-middel-title">
                    <h5><?php echo $this->lang->line('order_items') ?></h5>
                </div>
                <div class="detail-list-box">
                    <?php $menuids = array();
                    if (!empty($order_details[0]['items'])) {
                        foreach ($order_details[0]['items'] as $key => $item_value) {
                            $is_veg = ($item_value['is_veg'] == 1)?'veg':'non-veg'; 
                            $menu_arr = array();
                            $menu_arr['menu_id'] = $item_value['menu_id'];
                            $menu_arr['menu_qty'] = $item_value['quantity'];
                            $menu_arr['comment'] = $item_value['comment'];
                            $menu_arr['is_addon'] = ($item_value['is_customize']==1)?'1':'0';
                            $menu_arr['addonValue'] = (!empty($item_value['addons_category_list']))?json_encode($item_value['addons_category_list']):'';
                            $menu_arr['itemTotal'] = $item_value['itemTotal'];
                            array_push($menuids, $menu_arr); ?>
                            <div class="detail-list">
                                <div class="detail-list-content"> 
                                    <div class="detail-list-text">
                                        <h4><?php echo $item_value['name']; ?></h4>
                                        <ul class="ul-disc">
                                            <?php if (!empty($item_value['addons_category_list'])) {
                                                foreach ($item_value['addons_category_list'] as $key => $cat_value) { ?>
                                                    <?php /* ?><li><h6><?php echo $cat_value['addons_category']; ?></h6></li><?php */ ?>
                                                    <ul class="ul-cir">
                                                        <?php if (!empty($cat_value['addons_list'])) {
                                                            foreach ($cat_value['addons_list'] as $add_key => $add_value) { ?>
                                                                <li><?php echo $add_value['add_ons_name']; ?>  <?php echo $order_details[0]['currency_symbol']; ?><?php echo $add_value['add_ons_price']; ?></li>
                                                            <?php }
                                                        } ?>
                                                    </ul>
                                                <?php }
                                            } ?>
                                        </ul>
                                        <?php
                                        if(!empty($item_value['comment'])){
                                            ?><div class="menu-item-comment"><p><b><?php echo $this->lang->line('item_comment')?>:</b> <?php echo $item_value['comment']; ?></p></div><?php
                                        }
                                        ?>
                                    </div>
                                    <div class="right-price" id="subtotal" value="<?php echo $order_details[0]['currency_symbol']; ?><?php echo $item_value['itemTotal']; ?>">
                                        <strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo number_format_unchanged_precision($item_value['itemTotal']); ?></strong>
                                    </div>
                                </div>
                            </div>
                        <?php } 
                    } ?>
                </div>
            </div>
            <?php $subtotal = 0;
            $delivery_charges = 0;
            $total = 0;
            $coupon_amount = 0;
            $tax_amount = 0;
            if (!empty($order_details[0]['price'])) {
                foreach ($order_details[0]['price'] as $pkey => $pvalue) {
                    if (!empty($pvalue['label_key']) && $pvalue['label_key'] == "Sub Total") {
                        $subtotal = $pvalue['value'];
                    }
                }
            } ?>
            <form>
                <input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $order_details[0]['restaurant_id']; ?>">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo ($this->session->userdata('UserID'))?$this->session->userdata('UserID'):''; ?>">
                
                <input type="hidden" name="subTotal_for_cal" id="subTotal_for_cal" value="<?php echo $subtotal; ?>">
                <input type="hidden" id="totalPrice" value="<?php echo $total; ?>">
            </form>
            <div class="order-summary-content">
                <table>
                    <tbody>
                        <tr>
                            <td><?php echo $this->lang->line('sub_total') ?></td>
                            <td><strong><?php echo $order_details[0]['currency_symbol']; ?> <?php echo $subtotal; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="continue-btn">
            <?php if($order_details[0]['restaurant_status'] != "1" || $order_details[0]['enable_hours'] != '1' || $order_details[0]['timings']['off'] == "close" ) { ?>
                <!-- display error message -->
                <p style="color: red"><?php echo $this->lang->line('resto_not_accepting_orders'); ?></p>

            <?php } else {
                if($order_details[0]['timings']['closing'] != "Closed") { ?>
                    
                    <button class="btn addtocart" id="addtocart" onclick="checkCartOnReorder(<?php echo $order_details[0]['restaurant_id']; ?>,<?php echo htmlspecialchars(json_encode($menuids)); ?>)"> <?php echo $this->lang->line('continue'); ?> </button>

                <?php } else { ?>
                    <!-- display error message -->
                    <p style="color: red"><?php echo $this->lang->line('restaurant_closemsg'); ?></p>
                <?php }
            } ?>
            </div>
        </div>
    </div>
</div>