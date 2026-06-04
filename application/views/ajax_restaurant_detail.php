<?php $menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
}
//get System Option Data
/*$this->db->select('OptionValue');
$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);
$currency_symbol = $currency_symbol->currency_symbol;*/
//if (!empty($restaurant_details['menu_items']) && !empty($restaurant_details['categories'])) {
if (!empty($restaurant_details['menu_items'])) {
	if (!empty($restaurant_details['categories'])) {?>
		<div class="slider-checkbox-main">
			<!-- <button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left move left" type="button"><i class="iicon-icon-16"></i></button> -->
			<nav class="cat-loop">
				<ul class="autoWidth-non-loop" id="autoWidth-non-loop">	
					<?php if (!empty($restaurant_details['menu_items'])) {
				        $popular_count = 0;
				        foreach ($restaurant_details['menu_items'] as $key => $value) {
				            if ($value['popular_item'] == 1) {
				                $popular_count = $popular_count + 1;
				            }
				        }
				    }
				    $ccc=1;
				    if ($popular_count > 0) { ?>
				    	<li class="item" id="categorytop<?php echo $ccc; ?>"><a href="#popular_menu_item" class="active"><?php echo $this->lang->line('popular_items'); ?></a></li>
				    <?php $ccc=2; 
					} ?>
				    <?php										    
				    foreach ($restaurant_details['categories'] as $key => $value) {?>
				    	<li class="item" id="categorytop<?php echo $ccc; ?>"><a href="#category-<?php echo $value['category_id']; ?>" <?php if($ccc==1){?> class="active" <?php } ?>><?php echo $value['name']; ?></a></li>
	    			<?php
	    			$ccc++;
	    			 }?>
				</ul>
			</nav>
			<!-- <button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right move right" type="button"><i class="iicon-icon-17"></i></button> -->
		</div>
<?php }?>
<div class="is_close">	<?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'<span id="closedres">'.$this->lang->line('not_accepting_orders').'</span>':''; ?>
</div> <?php 
	if (!empty($restaurant_details['menu_items'])) {
	    $popular_count = 0;
	    foreach ($restaurant_details['menu_items'] as $key => $value) {
	        if ($value['popular_item'] == 1) {
	            $popular_count = $popular_count + 1;
	        }
	    }
	    if ($popular_count > 0) { ?>
	    	<div class="detail-list-title collapse-header">
				<a data-toggle="collapse" href="#popular_menu_item" role="button" aria-expanded="false" aria-controls="popular_menu_item"><h2 class="text-white p-3"><?php echo $this->lang->line('popular_items') ?></h2></a>
			</div>
			<div class="collapse detail-list-box-main sliderMenutoggle show" id="popular_menu_item">
				<?php foreach ($restaurant_details['menu_items'] as $key => $value) {
					if ($value['popular_item'] == 1) { ?>
						<div class="detail-list-box type-food-option">
						 	<div class="detail-list">
								<div class="detail-list-img">
									<div class="list-img">
										<?php /* $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_img; ?>
										<img src="<?php echo $rest_image ;?>" alt="<?php echo $value['name']; ?>">

										<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div> <?php */ ?>

									<?php $rest_image = (file_exists(FCPATH.'uploads/'.$value['image']) && $value['image']!='') ? image_url.$value['image'] : default_icon_img;
									if ($value['check_add_ons'] == 1) { ?>
										<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <img src="<?php echo $rest_image; ?>" alt="<?php echo $value['name']; ?>"> </a>

										<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
									<?php } else {?>
										<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <img src="<?php echo $rest_image; ?>" alt="<?php echo $value['name']; ?>"> </a>

										<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
									<?php } ?>
									</div>
								</div>
								<div class="detail-list-content">
									<div class="detail-list-text">
										<!-- <h4><?php //echo $value['name']; ?></h4> -->
										<!-- menu details on item name click :: start -->
										<?php if ($value['check_add_ons'] == 1) { ?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <h4><?php echo $value['name']; ?></h4> </a>
										<?php } else {?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <h4><?php echo $value['name']; ?></h4> </a>
										<?php } ?>
										<?php 
										$food_type_name = '';
										foreach ($restaurant_details['restaurant'][0]['resfood_type'] as $key => $val) {
												if($val->food_type_id == $value['food_type']){
															$food_type_name = $val->food_type_name;break;
														}
												} if(!empty($food_type_name)){ ?>
										<div><?php echo $this->lang->line('food_type')." : " ?><?php echo $food_type_name; ?></div> <?php } ?>
										<div><?php echo $this->lang->line('availability')." : " ?><?php echo $value['availability']; ?></div>
										<!-- menu details on item name click :: end -->
										<p><?php echo $value['menu_detail']; ?></p>
										
										<strong <?php if($value['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>><?php echo ($value['check_add_ons'] != 1)?currency_symboldisplay($value['price'],$restaurant_details['restaurant'][0]['currency_symbol']):(($value['price'])?currency_symboldisplay($value['price'],$restaurant_details['restaurant'][0]['currency_symbol']):''); ?></strong>
										<?php if($value['offer_price']>0){ ?>
										<strong class="pl-2">
										<?php echo ($value['check_add_ons'] != 1)?currency_symboldisplay($value['offer_price'],$restaurant_details['restaurant'][0]['currency_symbol']):(($value['offer_price'])?currency_symboldisplay($value['offer_price'],$restaurant_details['restaurant'][0]['currency_symbol']):''); ?>
										</strong>
										<?php } ?>

									</div>
									<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
										if ($value['check_add_ons'] == 1) {
											if($value['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
												<div class="add-btn" id="cart_item_<?php echo $value['entity_id']; ?>">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)" order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
													<span class="cust" style="text-align:center;"><?php echo $this->lang->line('customizable') ?></span>
													<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) { ?>
														<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
													<?php } ?>
												</div>
											<?php }else{ ?>
												<div class="add-btn">
													<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
												</div>
											<?php } 	
										} else {
											if($value['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
												<div class="add-btn" id="cart_item_<?php echo $value['entity_id']; ?>">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
													<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $value['stock'] == 0) { ?>
														<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
													<?php } ?>
												</div>
											<?php }else{ ?>
												<div class="add-btn">
													<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
												</div>
											<?php }
									} } ?>
								</div>
							</div>
						</div>
					<?php }
				}?>
			</div>
		<?php }?>
	<?php }?>
	<?php if (!empty($restaurant_details['categories'])) {
	    foreach ($restaurant_details['categories'] as $key => $value) { ?>
	    	<div class="detail-list-title collapse-header">
    			<a data-toggle="collapse" href="#category-<?php echo $value['category_id']; ?>" role="button" aria-expanded="false" aria-controls="category-<?php echo $value['category_id']; ?>"><h2 class="text-white p-3"><?php echo $value['name']; ?></h2></a>
			</div>
			<div class="collapse detail-list-box-main categories sliderMenutoggle show" id="category-<?php echo $value['category_id']; ?>" >
				<?php 
				$margin_text = '';
				if($restaurant_details[$value['name']]) {
					if(count($restaurant_details[$value['name']])==1){
						$margin_text = 'style="margin-bottom:60px !important;"';
					}
				}
				?>
				<div class="detail-list-box type-food-option" <?php echo $margin_text; ?>>
					<?php if ($restaurant_details[$value['name']]) {
						foreach ($restaurant_details[$value['name']] as $key => $mvalue) {?>
							<div class="detail-list">
								<div class="detail-list-img">
									<div class="list-img">
										<?php /* ?><img src="<?php echo ($mvalue['image']) ? base_url().'uploads/'.$mvalue['image'] : default_img; ?>"><?php */ ?>
										<?php if ($mvalue['check_add_ons'] == 1) {?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <img src="<?php echo (file_exists(FCPATH.'uploads/'.$mvalue['image']) && $mvalue['image']!='') ? image_url.$mvalue['image'] : default_icon_img; ?>"> </a>
										<?php } else {?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <img src="<?php echo (file_exists(FCPATH.'uploads/'.$mvalue['image']) && $mvalue['image']!='') ? image_url.$mvalue['image'] : default_icon_img; ?>"> </a>
										<?php }  ?>
									</div>
								</div>
								<div class="detail-list-content">
									<div class="detail-list-text">
										<!-- <h4><?php //echo $mvalue['name']; ?></h4> -->
										<!-- menu details on item name click :: start -->
										<?php if ($mvalue['check_add_ons'] == 1) {?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','addons',this.id,'no')"> <h4><?php echo $mvalue['name']; ?></h4> </a>
										<?php } else {?>
											<a href="javascript:void(0);" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurantDetails(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'<?php echo $restaurant_details['restaurant'][0]['timings']['closing']; ?>','',this.id,'no')" > <h4><?php echo $mvalue['name']; ?></h4> </a>
										<?php }  ?>
										<?php 
										$mfood_type_name = '';
										foreach ($restaurant_details['restaurant'][0]['resfood_type'] as $key => $mval) {
											if($mval->food_type_id == $mvalue['food_type']){
														$mfood_type_name = $mval->food_type_name;break;
												}
										} ?>
										<?php 
										if(!empty($mfood_type_name)){ ?>
										<div><?php echo $this->lang->line('food_type')." : " ?><?php echo $mfood_type_name; ?></div> <?php } ?>
										<div><?php echo $this->lang->line('availability')." : " ?><?php echo $mvalue['availability']; ?></div>
										<!-- menu details on item name click :: end -->
										<p><?php echo $mvalue['menu_detail']; ?></p>
										<strong <?php if($mvalue['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>><?php echo ($mvalue['check_add_ons'] != 1)?currency_symboldisplay($mvalue['price'],$restaurant_details['restaurant'][0]['currency_symbol']):(($mvalue['price'])?currency_symboldisplay($mvalue['price'],$restaurant_details['restaurant'][0]['currency_symbol']):''); ?></strong>
										<?php if($mvalue['offer_price']>0){ ?>
											<strong><?php echo ($mvalue['check_add_ons'] != 1)?currency_symboldisplay($mvalue['offer_price'],$restaurant_details['restaurant'][0]['currency_symbol']):(($mvalue['offer_price'])?currency_symboldisplay($mvalue['offer_price'],$restaurant_details['restaurant'][0]['currency_symbol']):''); ?></strong>
										<?php } ?>
										
									</div>
									<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
										if ($mvalue['check_add_ons'] == 1) {
											if($mvalue['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
												<div class="add-btn" id="cart_item_<?php echo $mvalue['entity_id']; ?>">
													<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)" order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
													<span class="cust" style="text-align:center;"><?php echo $this->lang->line('customizable') ?></span>
													<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) { ?>
														<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
													<?php } ?>
												</div>
											<?php }else{ ?>
												<div class="add-btn">
													<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
												</div>
											<?php }	
										} else { 
											if($mvalue['stock'] == 1 || $restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1'){ ?>
												<div class="add-btn" id="cart_item_<?php echo $mvalue['entity_id']; ?>">
													<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> order-for-later="<?php echo ($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? '1' : '0'; ?>" > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):(($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) ? $this->lang->line('order_for_later') : $this->lang->line('add')); ?> </button>
													<?php if($restaurant_details['restaurant'][0]['allow_scheduled_delivery'] == '1' && $mvalue['stock'] == 0) { ?>
														<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
													<?php } ?>
												</div>
											<?php }else{ ?>
												<div class="add-btn">
													<span class="cust text-danger ouofstockcls"><?php echo $this->lang->line('out_stock') ?></span>
												</div>
											<?php } 
									} } ?>
								</div>
							</div>
						<?php }
					}?>
				</div>
			</div>			
		<?php }
}
if(empty($restaurant_details['menu_items'])){ ?>
	<div class="slider-checkbox-main">
		<div class="cart-empty text-center" style="padding:10px;">
			<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
			<h6><?php echo $this->lang->line('no_results_found') ?></h6>
		</div>
	</div>
<?php }
}
else
{ ?>
	<div class="slider-checkbox-main">
		<div class="cart-empty text-center" style="padding:10px;">
			<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
			<h6><?php echo $this->lang->line('no_results_found') ?></h6>
		</div>
	</div>
<?php } ?>
<script>
    var doc = document,
      slideList = doc.querySelectorAll('.slider-checkbox-main > div'),
      toggleHandle = doc.querySelector('.nav-toggle-handle'),
      divider = window.innerHeight / 2,
      scrollTimer,
      resizeTimer; 

     if (window.addEventListener) {
     window.addEventListener('scroll', function () {
      clearTimeout(scrollTimer);

      scrollTimer = setTimeout(function () {
        [].forEach.call(slideList, function (el) {
          var rect = el.getBoundingClientRect();         
        });
      }, 100);
    });

    window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);

      resizeTimer = setTimeout(function () {
        divider = window.innerHeight / 2;
      }, 100);
    });
    
    }

    var mobile = 'false',
      isTestPage = false,
      isDemoPage = true,
      classIn = 'jello',
      classOut = 'rollOut',
      speed = 400,
      doc = document,
      win = window,
      ww = win.innerWidth || doc.documentElement.clientWidth || doc.body.clientWidth,
      fw = getFW(ww),
      initFns = {},
      sliders = new Object(),
      edgepadding = 50,
      gutter = 10;

    function getFW (width) {
    var sm = 400, md = 900, lg = 1400;
    return width < sm ? 150 : width >= sm && width < md ? 200 : width >= md && width < lg ? 300 : 400;
    }
    window.addEventListener('resize', function() { fw = getFW(ww); });
    </script>
    <script>

    // <script type="module">
    // import { tns } from '../src/tiny-slider.js';

    var options = {
    'autoWidth-non-loop': {
      autoWidth: true,
      loop: false,
      mouseDrag: true,
      nav: false,
    }
    
    };

    for (var i in options) {
    var item = options[i];
    item.container = '#' + i;
    item.swipeAngle = false;
    if (!item.speed) { item.speed = speed; }

    if (doc.querySelector(item.container)) {
      sliders[i] = tns(options[i]);

    // test responsive pages
    } else if (i.indexOf('responsive') >= 0) {
      if (isTestPage && initFns[i]) { initFns[i](); }
    }
}




    //New code for scroll item :: Start
var sections = $('.sliderMenutoggle')
  , nav = $('nav')
  , nav_height = nav.outerHeight();

var lastScrollTop = 0;
var lastCat = "";
// var $dots = $('.owl-carousel');
$(window).on('scroll', function () {

var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight();

  var cur_pos = $(this).scrollTop()+totalheight;
  var curScroll = $(this).scrollTop();
  
  sections.each(function() {
    var top = $(this).offset().top,
        bottom = top + $(this).outerHeight();
    var curCat = $(this).attr('id');
    if (cur_pos >= top && cur_pos <= bottom)
    {
      nav.find('a').removeClass('active');
      sections.removeClass('active');      
      $(this).addClass('active');
      nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('active');

    if(curCat != lastCat && lastCat !=""){
      if (curScroll > lastScrollTop){
          //scroll down
          sliders['autoWidth-non-loop'].goTo('next');
      } else {
          //scroll up          
          sliders['autoWidth-non-loop'].goTo('prev');
      }
      lastScrollTop = curScroll;  
    }
    lastCat = curCat;      
    }
  });
});

/*nav.find('a').on('click', function () {
	var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight();
  	var $el = $(this)
    , id = $el.attr('href');

  $('html, body').animate({
    scrollTop: $(id).offset().top - totalheight
  }, 500);
  
  return false;
});*/
$('.collapse-header').click(function(){
	if($(this).hasClass('active')){
		$(this).removeClass('active');
	}
	else{
		$(this).addClass('active');
	}
});
$(nav).find('a').on('click', function () {
	var collapse_c = $('.collapse-header a[href*='+$(this).attr('href').substring(1)+']');
	if(collapse_c.hasClass('collapsed')){
		$(collapse_c).click();
	}
	var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight()+40;
	var $el = $(this)
	, id = $el.attr('href');
	$('html, body').animate({
	scrollTop: $(id).offset().top - totalheight
	}, 500);
	return false;
});
//New code for scroll item :: end

</script>