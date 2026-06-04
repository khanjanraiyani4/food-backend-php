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
                            <button class="btn add addtocart-<?php echo $value['menu_id']; ?>" id="addtocart-<?php echo $value['menu_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'',this.id,'<?php echo $page; ?>')"><?php echo $this->lang->line('add'); ?></button>
                        </div>
                    <?php } else {?>
                        <div class="add-btn" id="cart_item_<?php echo $value['menu_id']; ?>">                            
                            <button class="btn add addtocart-<?php echo $value['menu_id']; ?>" id="addtocart-<?php echo $value['menu_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'addons',this.id,'<?php echo $page; ?>')"> <?php echo $this->lang->line('add'); ?> </button>
                        </div>
                    <?php } ?>
                </div>
                
        </div>
    <?php } ?>
</div>
</div>
<?php } ?>