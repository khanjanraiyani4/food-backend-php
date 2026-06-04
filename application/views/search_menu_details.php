<?php $menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
}
if (!empty($restaurant_details['menu_items']) || !empty($restaurant_details['packages']) || !empty($restaurant_details['categories']))
{ /* ?>
	<div class="row option-filter-tab mr-0 ml-0">
		<div class="col-md-8">
        	<?php
        	$resfood_type = $restaurant_details['restaurant'][0]['resfood_type'];
        	if(!empty($resfood_type)>0 && count($resfood_type)>0)
        	{
        		for($fdt=0;$fdt<count($resfood_type);$fdt++) {
        	 	?>
        		<div class="custom-control custom-checkbox filter-width">
					<input type="radio" <?php if(count($resfood_type)==1) {?> checked <?php } ?> name="filter_food" class="custom-control-input" id="filter_<?=$resfood_type[$fdt]->food_type_id?>" value="<?=$resfood_type[$fdt]->food_type_id?>" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value,'yes')">
					<label class="custom-control-label" for="filter_<?=$resfood_type[$fdt]->food_type_id?>"><?php echo ucfirst($resfood_type[$fdt]->food_type_name); ?></label>
				</div>
        	<?php }
        	if(count($resfood_type)>1) { ?>
        	<div class="custom-control custom-checkbox filter-width">
				<input type="radio" checked="checked" name="filter_food" class="custom-control-input" id="all" value="all" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
				<label class="custom-control-label" for="all"><?php echo $this->lang->line('view_all') ?></label>
			</div>
			<?php } ?>
        	<?php  } ?>
    	</div>

    	<?php //New code add for availability :: Start ?>
			<div class="col-md-12 rest-detail-content" style="padding-top:15px;padding-left:15px"><h2 style="font-size: 16px;"><?php echo $this->lang->line('sort_availability') ?></h2></div>
				<div class="col-md-12">					        	
	        		<div class="custom-control custom-checkbox filter-width">
						<input type="radio"  name="filter_availibility" class="custom-control-input" id="filter_breakfast" value="Breakfast" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value,'no','yes')">
						<label class="custom-control-label" for="filter_breakfast"><?php echo $this->lang->line('breakfast') ?></label>
					</div>
					<div class="custom-control custom-checkbox filter-width">
						<input type="radio"  name="filter_availibility" class="custom-control-input" id="filter_lunch" value="Lunch" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value,'no','yes')">
						<label class="custom-control-label" for="filter_lunch"><?php echo $this->lang->line('lunch') ?></label>
					</div>
					<div class="custom-control custom-checkbox filter-width">
						<input type="radio" name="filter_availibility" class="custom-control-input" id="filter_dinner" value="Dinner" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value,'no','yes')">
						<label class="custom-control-label" for="filter_dinner"><?php echo $this->lang->line('dinner') ?></label>
					</div>
		        	<div class="custom-control custom-checkbox filter-width">
						<input type="radio" checked="checked" name="filter_availibility" class="custom-control-input" id="all_availibility" value="all" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
						<label class="custom-control-label" for="all_availibility"><?php echo $this->lang->line('view_all') ?></label>
					</div>					        	
    	</div>
    	<?php //New code add for availability :: End ?>	
    	
    	<div class="col-md-4">
			<div class="custom-control custom-checkbox">
			    <input type="radio" checked="checked" name="filter_price" class="custom-control-input" id="filter_high_price" value="filter_high_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
			    <label class="custom-control-label" for="filter_high_price"><?php echo $this->lang->line('sort_by_price_low') ?></label>
		  	</div>
			<div class="custom-control custom-checkbox">
				<input type="radio" name="filter_price" class="custom-control-input" id="filter_low_price" value="filter_low_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>,this.value)">
				<label class="custom-control-label" for="filter_low_price"><?php echo $this->lang->line('sort_by_price_high') ?></label>
			</div>
		</div> 
	</div> 
	<?php if (!empty($restaurant_details['categories'])) { ?>
		<div class="slider-checkbox-main">
		<nav>
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
			<div class="pn-ProductNav_Wrapper">
				<button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left" type="button"><i class="iicon-icon-16"></i></button>
				<nav id="pnProductNav" class="pn-ProductNav">
				    <div id="pnProductNavContents" class="pn-ProductNav_Contents">
		    			<?php foreach ($restaurant_details['categories'] as $key => $value) {?>
		    				<div class="slider-checkbox" aria-selected="true">
					    		<label>
					    			<input class="check-menu" type="checkbox" name="checkbox-option" id="checkbox-option-<?php echo $value['category_id']; ?>" onclick="menuSearch(<?php echo $value['category_id']; ?>)">
					    			<span><?php echo $value['name']; ?></span>
					    		</label>
					    	</div>
		    			<?php }?>
						<span id="pnIndicator" class="pn-ProductNav_Indicator"></span>
				    </div>
				</nav>
				<button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right" type="button"><i class="iicon-icon-17"></i></button>
			</div>
		</div>
	<?php } */ ?>
	
	<div id="res_detail_content">
		<?php if (!empty($restaurant_details['menu_items'])) {
	        $popular_count = 0;
	        foreach ($restaurant_details['menu_items'] as $key => $value) {
	            if ($value['popular_item'] == 1) {
	                $popular_count = $popular_count + 1;
	            }
	        }
	        if ($popular_count > 0) { ?>
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('popular_items') ?></h3>
					</div>
					<?php foreach ($restaurant_details['menu_items'] as $key => $value) {
						if ($value['popular_item'] == 1) { ?>
							<div class="detail-list-box">
							 	<div class="detail-list">
									<div class="detail-list-img">
										<div class="list-img">
											<img src="<?php echo (file_exists(FCPATH.'uploads/'.$value['image'])  && $value['image']!='') ? image_url.$value['image'] : default_icon_img; ?>">
											<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
										</div>
									</div>
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $value['name']; ?></h4>
											<p><?php echo $value['menu_detail']; ?></p>
											<strong <?php if($value['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>><?php echo ($value['check_add_ons'] != 1)?'$'.$value['price']:''; ?></strong>
											<?php if($value['offer_price']>0){ ?>
												<strong><?php echo ($value['check_add_ons'] != 1)?'$'.$value['offer_price']:''; ?></strong>
											<?php } ?>
										</div>
										<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
											if ($value['check_add_ons'] == 1) {?>
												<div class="add-btn">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
													<span class="cust" style="text-align:center;"><?php echo $this->lang->line('customizable') ?></span>
												</div>
											<?php } else {?>
												<div class="add-btn">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												</div>
										<?php } } ?>
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
				<div class="detail-list-box-main categories" id="category-<?php echo $value['category_id']; ?>" >
					<div class="detail-list-title">
						<h3><?php echo $value['name']; ?></h3>
					</div>
					<div class="detail-list-box">
						<?php if ($restaurant_details[$value['name']]) {
    						foreach ($restaurant_details[$value['name']] as $key => $mvalue) {?>
								<div class="detail-list">
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $mvalue['name']; ?></h4>
											<p><?php echo $mvalue['menu_detail']; ?></p>
											<strong <?php if($mvalue['offer_price']>0){ ?>class="text-secondary" style="text-decoration: line-through;" <?php } ?>><?php echo ($mvalue['check_add_ons'] != 1)?'$'.$mvalue['price']:''; ?></strong>
											<?php if($mvalue['offer_price']>0){ ?>
												<strong><?php echo ($mvalue['check_add_ons'] != 1)?'$'.$mvalue['offer_price']:''; ?></strong>
											<?php } ?>
										</div>
										<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
											if ($mvalue['check_add_ons'] == 1) {?>
												<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
												<div class="add-btn">
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
													<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
												</div>
											<?php } else {?>
												<div class="add-btn">
												<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												</div>
										<?php } } ?>
									</div>
								</div>
							<?php }
						}?>
					</div>
				</div>
			<?php }
		} ?>
	</div>
<?php } 
else { ?>
<div class="slider-checkbox-main">
	<div class="cart-empty text-center" style="padding:10px;">
		<img src="<?php echo base_url();?>assets/front/images/empty-cart.png">
		<h6><?php echo $this->lang->line('no_product_found') ?></h6>
	</div>
<?php } ?>
<script type="text/javascript">
	//menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>);
</script>
<script src="https://ganlanyuan.github.io/tiny-slider/dist/tiny-slider.js"/>
<script type="text/javascript">
$(document).on('ready', function() {
	var count = '<?php echo count($cart_details['cart_items']); ?>'; 
	$('#cart_count').html(count);
	if(count != '0'){
		$('body').addClass("cart_bottom");
		//$("#your_cart").addClass("cart_bottom");
	} else {
		$('body').addClass("cart_bottom");
		//$("#your_cart").removeClass("cart_bottom");
	}
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});	
});
</script>
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
var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight()+10;
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
nav.find('a').on('click', function () {
	var totalheight = $('.slider-checkbox-main').outerHeight()+$('.header-area').outerHeight();
  	var $el = $(this)
    , id = $el.attr('href');
  $('html, body').animate({
    scrollTop: $(id).offset().top - totalheight
  }, 500);
  
  return false;
});
//when search box is empty
$('#search_dish').keyup(function(){
	$('#Search_btn').prop('disabled', false);
	/*if($(this).val()==''){
		$('#Search_btn').prop('disabled', true);
	}*/
});
//New code for scroll item :: end
$('input.QtyNumberval').on('input', function() {		
    this.value = this.value.replace(/[^0-9]/g,'').replace(/(\..*)\./g, '$1');
});
</script>