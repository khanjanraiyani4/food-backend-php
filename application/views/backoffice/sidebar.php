<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">        
        <ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">            
            <li class="sidebar-toggler-wrapper">                
                <div class="sidebar-toggler" title="<?php echo $this->lang->line('collapse') ?>" alt="<?php echo $this->lang->line('collapse') ?>">
                </div>                
            </li>
            <li>&nbsp;</li>
            <li class="start <?php echo ($this->uri->segment(2)=='dashboard')?"active":""; ?>">
                <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span class="title"><?php echo $this->lang->line('dashboard'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>
            <?php if(in_array('users~view',$this->session->userdata("UserAccessArray")) || in_array('admin~admin',$this->session->userdata("UserAccessArray")) || in_array('driver~driver',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='users' || $this->uri->segment(3)=='driver' || $this->uri->segment(3)=='commission')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/users/view">
                        <i class="fa fa-users"></i>
                        <span class="title"><?php echo $this->lang->line('users'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='users' || $this->uri->segment(3) == 'driver' || $this->uri->segment(4)=='driver' || $this->uri->segment(3) == 'admin' || $this->uri->segment(4)=='admin')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if(in_array('admin~admin',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='users' && $this->uri->segment(3)=='admin' || $this->uri->segment(4)=='admin' || $this->uri->segment(5)=='admin') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/users/admin">
                                    <i class="fa fa-user-circle"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_admin'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('users~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='users' && $this->uri->segment(3) != 'driver' && $this->uri->segment(3) != 'admin' && $this->uri->segment(4)!='driver' && $this->uri->segment(4)!='admin' && $this->uri->segment(3)!='commission' && $this->uri->segment(3)!='drivertip' && $this->uri->segment(5)!='driver' && $this->uri->segment(5)!='admin' && $this->uri->segment(3) != 'review' && $this->uri->segment(5) != 'agent')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/users/view">
                                    <i class="fa fa-users"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_customers'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('driver~driver',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='driver' || $this->uri->segment(4)=='driver' ||  $this->uri->segment(3)=='commission' || $this->uri->segment(3)== 'drivertip' || $this->uri->segment(5)=='driver' || $this->uri->segment(3) == 'review')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/users/driver">
                                    <i class="fa fa-motorcycle" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_driver'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>                        
                    </ul>
                </li>
            <?php } ?>
            <?php if(in_array('restaurant~view',$this->session->userdata("UserAccessArray")) || in_array('food_type~view',$this->session->userdata("UserAccessArray")) || in_array('category~view',$this->session->userdata("UserAccessArray")) || in_array('addons_category~view',$this->session->userdata("UserAccessArray")) || in_array('restaurant_package~view_package',$this->session->userdata("UserAccessArray")) || in_array('restaurant_menu~view_menu',$this->session->userdata("UserAccessArray")) || in_array('restaurant_menu~menu_item_suggestion',$this->session->userdata("UserAccessArray")) || in_array('review~view',$this->session->userdata("UserAccessArray")) || in_array('delivery_charge~view',$this->session->userdata("UserAccessArray")) || in_array('recipe~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='restaurant' || $this->uri->segment(2)=='delivery_charge' || $this->uri->segment(2)=='addons_category' || $this->uri->segment(2)=='category' || $this->uri->segment(2)=='food_type' || $this->uri->segment(2)=='review' || $this->uri->segment(2)=='recipe')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('restaurant'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='restaurant' || $this->uri->segment(2)=='delivery_charge' || $this->uri->segment(2)=='addons_category')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a> 
                    <ul class="sub-menu">
                        <?php if(in_array('restaurant~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='restaurant' && ($this->uri->segment(3) =='view' || $this->uri->segment(3) =='add' || $this->uri->segment(3) =='edit'))?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_res'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('food_type~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='food_type')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/food_type/view">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="title"><?php echo $this->lang->line('food_type'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('category~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='category')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/category/view">
                                    <i class="fa fa-list-alt"></i>
                                    <span class="title"><?php echo $this->lang->line('menu_category'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('addons_category~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'addons_category')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/addons_category/view">
                                    <i class="fa fa-list-alt"></i>
                                    <span class="title"><?php echo $this->lang->line('addons_category'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('restaurant_package~view_package',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='view_package' || $this->uri->segment(3) == 'add_package' || $this->uri->segment(3) == 'edit_package')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view_package">
                                    <i class="fa fa-gift"></i>
                                    <span class="title"><?php echo $this->lang->line('event_package'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php }  ?>
                        <?php if(in_array('restaurant_menu~view_menu',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='view_menu' || $this->uri->segment(3) == 'add_menu' || $this->uri->segment(3) == 'edit_menu' || $this->uri->segment(3) == 'add_combo_menu_item' || $this->uri->segment(3) == 'edit_combo_menu_item')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/view_menu">
                                    <i class="fa fa-bars"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_res_menu'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('recipe~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='recipe') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/recipe/view">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="title"><?php echo $this->lang->line('recipes'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('restaurant_menu~menu_item_suggestion',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='menu_item_suggestion')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/restaurant/menu_item_suggestion">
                                    <i class="fa fa-bars"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_item_suggestion'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('review~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='review')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/review/view">
                                    <i class="fa fa-star"></i>
                                    <span class="title"><?php echo $this->lang->line('rating_review'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('delivery_charge~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'delivery_charge')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/delivery_charge/view">
                                    <i class="fa fa-list-alt"></i>
                                    <span class="title"><?php echo $this->lang->line('title_delivery_charges'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            
            <?php if(in_array('order~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='order' && $this->uri->segment(3) != 'dine_in_orders' && $this->uri->segment(3) != 'edit_dinein_order_details' && $this->uri->segment(3) != 'dinein_add')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/order/view">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="title"><?php echo $this->lang->line('orders'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>                
            <?php } ?>
            <?php //online reservation changes
            if(in_array('event~view',$this->session->userdata("UserAccessArray")) || in_array('book_table~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='event' || $this->uri->segment(2)=='book_table')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/event/view">
                        <i class="fa fa-calendar"></i>
                        <span class="title"><?php echo $this->lang->line('online_reservation'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='event' || $this->uri->segment(2)=='book_table')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a> 
                    <ul class="sub-menu">
                        <?php if(in_array('event~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='event')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/event/view">
                                    <i class="fa fa-calendar"></i>
                                    <span class="title"><?php echo $this->lang->line('admin_event_booking'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('book_table~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='book_table')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/book_table/view">
                                    <i class="fa fa-university"></i>
                                    <span class="title"><?php echo $this->lang->line('table_bookings'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if(in_array('coupon~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='coupon')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/coupon/view">
                        <i class="fa fa-dollar"></i>
                        <span class="title"><?php echo $this->lang->line('admin_coupons'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('notification~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='notification')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/notification/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('notification'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('slider-image~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='slider-image')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/slider-image/view">
                        <i class="fa fa-image"></i>
                        <span class="title"><?php echo $this->lang->line('slider'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('cms~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='cms')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/cms/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('cms'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php //general_management
            if(in_array('system_option~view',$this->session->userdata("UserAccessArray")) || in_array('role~view',$this->session->userdata("UserAccessArray")) || in_array('user_log~view',$this->session->userdata("UserAccessArray")) || in_array('user_log~order_log_view',$this->session->userdata("UserAccessArray")) || in_array('contact_inquiries~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='system_option' || $this->uri->segment(2) == 'role' || $this->uri->segment(2) == 'contact_inquiries' || $this->uri->segment(2) == 'user_log')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/system_option/view">
                        <i class="fa fa-sliders"></i>
                        <span class="title"><?php echo $this->lang->line('general_management'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='system_option' || $this->uri->segment(2) == 'role' || $this->uri->segment(2) == 'user_log')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if(in_array('system_option~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='system_option')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/system_option/view">
                                    <i class="fa fa-cogs"></i>
                                    <span class="title"><?php echo $this->lang->line('titleadmin_systemoptions'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('role~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'role') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL; ?>/role/view">
                                    <i class="fa fa-user-circle-o"></i>
                                    <span class="title"><?php echo $this->lang->line('role_management'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('user_log~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'user_log' && $this->uri->segment(3) != 'order_log_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL; ?>/user_log/view">
                                    <i class="fa fa-history"></i>
                                    <span class="title"><?php echo $this->lang->line('user_log_management'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('user_log~order_log_view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'user_log' && $this->uri->segment(3) == 'order_log_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL; ?>/user_log/order_log_view">
                                    <i class="fa fa-history"></i>
                                    <span class="title"><?php echo $this->lang->line('order_log_management'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('contact_inquiries~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'contact_inquiries') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL; ?>/contact_inquiries/view">
                                    <i class="fa fa-address-book" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('contact_inquiries'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if(in_array('email_template~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=="email_template")?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/email_template/view">
                        <i class="fa fa-envelope-o"></i>
                        <span class="title"><?php echo $this->lang->line('titleadmin_email_template'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('country~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='country')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/country/view">
                        <i class="fa fa-globe" aria-hidden="true"></i>
                        <span class="title"><?php echo $this->lang->line('countries'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('reason_management~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='reason_management') ? "active" : ""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/reason_management/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('title_reason_management'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('restaurant_error_reports~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='restaurant_error_reports') ? "active" : ""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/restaurant_error_reports/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('restaurant_error_reports'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <?php if(in_array('payment_method~view',$this->session->userdata("UserAccessArray")) || in_array('payment_method~manage_payment_method',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='payment_method')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/payment_method/view">
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="title"><?php echo $this->lang->line('payment_methods'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='payment_method')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a> 
                    <ul class="sub-menu">
                        <?php if(in_array('payment_method~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) =='payment_method' && ($this->uri->segment(3)=='view' || $this->uri->segment(3)=='edit'))?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/payment_method/view">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_payment_method'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('payment_method~manage_payment_method',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='manage_payment_method')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/payment_method/manage_payment_method">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('res_payment_method'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php //Delivery Methods Management :: start ?>
            <?php /* if(in_array('delivery_method~view',$this->session->userdata("UserAccessArray")) || in_array('delivery_method~manage_delivery_method',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='delivery_method')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/delivery_method/view">
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="title"><?php echo $this->lang->line('delivery_methods'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='delivery_method')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a> 
                    <ul class="sub-menu">
                        <?php if(in_array('delivery_method~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) =='delivery_method' && $this->uri->segment(3)=='view')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/delivery_method/view">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_delivery_method'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('delivery_method~manage_delivery_method',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(3)=='manage_delivery_method')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/delivery_method/manage_delivery_method">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('res_delivery_method'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } */ ?>
            <?php //Delivery Methods Management :: end ?>
            <?php if(in_array('faq_category~view',$this->session->userdata("UserAccessArray")) || in_array('faqs~view',$this->session->userdata("UserAccessArray"))) { ?>
                <li class="start <?php echo ($this->uri->segment(2)=='faq_category' || $this->uri->segment(2)=='faqs')?"active":""; ?>">
                    <a href="<?php echo base_url().ADMIN_URL;?>/order/view">
                        <i class="fa fa-question-circle"></i>
                        <span class="title"><?php echo $this->lang->line('faq'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2)=='faq_category' || $this->uri->segment(2)=='faqs')?"open":""; ?>"></span>
                        <span class="selected"></span>
                    </a> 
                    <ul class="sub-menu">
                        <?php if(in_array('faq_category~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2)=='faq_category') ? "active" : ""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/faq_category/view">
                                    <i class="fa fa-list-alt"></i>
                                    <span class="title"><?php echo $this->lang->line('faq_categories'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(in_array('faqs~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'faqs')?"active":""; ?>">
                                <a href="<?php echo base_url().ADMIN_URL;?>/faqs/view">
                                    <i class="fa fa-question"></i>
                                    <span class="title"><?php echo $this->lang->line('faq_questions'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>        
    </div>
</div>