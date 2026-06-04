<?php $this->load->view(ADMIN_URL.'/header');?>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/sumoselect.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/highchart.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/css/daterangepicker.css" />
<style type="text/css">
    .dashboard_statnew{
        padding-top :0px !important;
    }
    .SumoSelect>.optWrapper>.options li label{
        white-space: break-spaces;
    }
</style>
<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
//get System Option Data
/*$this->db->select('OptionValue');
$currency_id = $this->db->get_where('system_option',array('OptionSlug'=>'currency'))->first_row();
$currency_symbol = $this->common_model->getCurrencySymbol($currency_id->OptionValue);*/ ?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content admin-dashboard">          
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('dashboard') ?> <small><?php echo $this->lang->line('statistics') ?></small></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li><?php echo $this->lang->line('dashboard') ?> </li>                        
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div> 
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense" style="padding: 0px !important;">
                        <div class="visual" style="height: 40px !important; width: 40px !important;">
                            <i class="fa fa-cutlery" aria-hidden="true"></i>
                        </div>
                        <div class="details">
                            <div class="number dashboard_statnew"><?php echo $restaurantCount ?></div>                           
                            <div class="desc"><?php echo $this->lang->line('total_restaurant') ?></div>
                        </div>
                        <?php if(in_array('restaurant~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <a class="more" href="<?php echo base_url().ADMIN_URL ?>/restaurant/view">
                                <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        <?php } else { ?>
                            <a class="more" href="javascript:void(0);">
                            </a>
                        <?php } ?>
                    </div>
                </div> 
                <?php if(in_array('users~view',$this->session->userdata("UserAccessArray"))) { ?> 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat purple-plum" style="padding: 0px !important;">
                        <div class="visual" style="height: 40px !important; width: 40px !important;">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="details">
                            <div class="number dashboard_statnew"> <?php echo $user['user_count'] ?></div>                           
                            <div class="desc"><?php echo $this->lang->line('total') ?> <?php echo $this->lang->line('customer') ?></div>
                        </div>
                        <?php if(in_array('users~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <a class="more" href="<?php echo base_url().ADMIN_URL ?>/users/view">
                                <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        <?php } else { ?>
                            <a class="more" href="javascript:void(0);">
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?> 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison" style="padding: 0px !important;">
                        <div class="visual" style="height: 40px !important; width: 40px !important;">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </div>
                        <div class="details" id="dashboard_order_count">
                            <div class="number dashboard_statnew"><?php echo $totalOrder ?></div>
                            <div class="desc"><?php echo $this->lang->line('total_order') ?></div>
                        </div>
                        <?php if(in_array('order~view',$this->session->userdata("UserAccessArray"))) { ?>
                            <a class="more" href="<?php echo base_url().ADMIN_URL ?>/order/view">
                                <?php echo $this->lang->line('view_more') ?> <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        <?php } else { ?>
                            <a class="more" href="javascript:void(0);">
                            </a>
                        <?php } ?>
                    </div>
                </div> 
            </div>

            <!-- Graph bar start -->
            <?php if(in_array('order~view',$this->session->userdata("UserAccessArray"))) { ?>
            <div class="row" id="dashboard_statistics">
                <div class="col-md-4">
                    <!-- lifetimmesale start -->
                        <div class="dashboard-stat grey-cascade">
                            <div class="visual" style="height: 60px !important; width: 60px !important;">
                            </div>
                            <div class="details"><?php
                            $currency_symbol = $this->common_model->getCurrencySymbol(DEFAULT_CURRENCY_ID); ?>
                            <div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($sale[0]->total,'2','.',','),$currency_symbol->currency_symbol); ?></div>                                                          
                                <div class="desc"><?php echo $this->lang->line('l_sale') ?></div>
                            </div>
                        </div>
                    <!-- lifetiesale end -->

                    <!-- for average sale start-->
                    <div class="dashboard-stat purple-plum">
                        <div class="visual" style="height: 60px !important; width: 60px !important;">
                        </div>
                        <div class="details"><?php
                            if($currency_symbol=='')
                            {
                                $currency_symbol = $this->common_model->getCurrencySymbol(DEFAULT_CURRENCY_ID);    
                            } ?>
                            <div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($last_month[0]->last_month,'2','.',','),$currency_symbol->currency_symbol); ?></div>
                            <div class="desc"><?php echo $this->lang->line('past_sale') ?></div>
                        </div>
                    </div>
                    <!-- average sale end-->

                    <!-- for tax start-->
                    <div class="dashboard-stat blue-madison">
                        <div class="visual" style="height: 60px !important; width: 60px !important;">
                        </div>
                        <div class="details"><?php 
                            if($currency_symbol=='')
                            {
                                $currency_symbol = $this->common_model->getCurrencySymbol(DEFAULT_CURRENCY_ID); 
                            } ?>
                            <div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($this_month[0]->this_month,'2','.',','),$currency_symbol->currency_symbol); ?></div>
                            <div class="desc"><?php echo $this->lang->line('current_sale') ?></div>
                        </div>
                    </div>
                    <!-- tax end -->
                </div>
                <!-- Graph bar start -->
                <div class="col-md-8 col-sm-12 bar-strategy" style="height:200px;">
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('orders') ?></div>
                            <div class="actions" style="margin-top:0px !important;"> 
                                      <input type="hidden" type="date" class="form-control form-filter input-sm d-inline"name="daterange" id="daterange"  />
                                <a href="javascript:void(0);" class="icon daterangeicon">
                                    <i class="fa fa-bars" style="color:white;font-size: 22px;line-height:normal;margin-top: 0px"></i>
                                </a>
                            </div>
                        </div>
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                        </figure>
                    </div>
                </div>
                <!-- Graph bar end -->
            </div>
            <?php } ?>

            <?php if(in_array('email_template~view',$this->session->userdata("UserAccessArray"))) { ?>
                <!-- quick email -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo $this->lang->line('quick_email') ?></div>
                                <div class="actions"></div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger alerttimerclose">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <form method="post" action="<?php echo base_url().ADMIN_URL ?>/dashboard" name="send_email" id="send_email">
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="template_id" placeholder="<?php echo $this->lang->line('select_template') ?>" class="form-control sumo" id="template_id">
                                                    <option value=""><?php echo $this->lang->line('select_template') ?></option>
                                                    <?php if(!empty($template)){
                                                        foreach ($template as $key => $value) { ?>
                                                           <option value="<?php echo $value->entity_id ?>"><?php echo $value->title ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="user_id[]" placeholder="<?php echo $this->lang->line('select_') ?> <?php echo $this->lang->line('customers') ?>" multiple="multiple" class="form-control sumo" id="user_id">
                                                    <?php if(!empty($user['users'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all_customers') ?>">
                                                        <?php foreach ($user['users'] as $key => $value) { ?>
                                                           <option value="<?php echo $value->entity_id ?>"><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                        <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                    <?php if(!empty($admin['res_admin'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all_res_admin') ?>">
                                                            <?php foreach ($admin['res_admin'] as $reskey => $resvalue) {  ?>
                                                                <option value="<?php echo $resvalue->entity_id ?>"><?php echo $resvalue->first_name.' '.$resvalue->last_name ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                    <?php if(!empty($admin['branch_admin'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all') ?>&nbsp;<?php echo $this->lang->line('restaurant_admin_mobile') ?>">
                                                            <?php foreach ($admin['branch_admin'] as $branchkey => $branchvalue) {  ?>
                                                                <option value="<?php echo $branchvalue->entity_id ?>"><?php echo $branchvalue->first_name.' '.$branchvalue->last_name ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-offset-3">
                                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success theme-btn default-btn"><?php echo $this->lang->line('submit') ?></button>
                                            </div>
                                        </div>       
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('notification~view',$this->session->userdata("UserAccessArray"))) { ?>
                <!-- notifications -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo $this->lang->line('notification'); ?></div>
                                <div class="actions"></div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <?php
                                    if(isset($_SESSION['NotificationMSG']))
                                    { ?>
                                        <div class="alert alert-success">
                                             <?php echo $_SESSION['NotificationMSG'];
                                             unset($_SESSION['NotificationMSG']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <form method="post" action="<?php echo base_url().ADMIN_URL ?>/dashboard" name="send_noti" id="send_noti">
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <input type="text" name="notification_title" id="notification_title" value="" placeholder="<?php echo $this->lang->line('label_notification');?>&nbsp;<?php echo $this->lang->line('title'); ?>" maxlength="249" data-required="1" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <textarea class="form-control" name="notification_description" id="notification_description" rows="1" placeholder="<?php echo $this->lang->line('label_notification');?>&nbsp;<?php echo $this->lang->line('message'); ?>" data-required="1" ></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="user_id_noti[]" placeholder="<?php echo $this->lang->line('select_').' '.$this->lang->line('customers') ?>" multiple="multiple" class="form-control sumo" id="user_id_noti">
                                                    <?php if(!empty($user['users'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all_customers') ?>">
                                                        <?php foreach ($user['users'] as $key => $value) { ?>
                                                           <option value="<?php echo $value->entity_id ?>"><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                        <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                    <?php if(!empty($user['drivers'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all_drivers') ?>">
                                                        <?php foreach ($user['drivers'] as $driver_key => $driver_value) { ?>
                                                           <option value="<?php echo $driver_value->entity_id ?>"><?php echo $driver_value->first_name.' '.$driver_value->last_name ?></option>
                                                        <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                    <?php if(!empty($admin['res_admin'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all_res_admin') ?>">
                                                            <?php foreach ($admin['res_admin'] as $reskey => $resvalue) {  ?>
                                                                <option value="<?php echo $resvalue->entity_id ?>"><?php echo $resvalue->first_name.' '.$resvalue->last_name ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                    <?php if(!empty($admin['branch_admin'])){ ?>
                                                        <optgroup label="<?php echo $this->lang->line('all') ?>&nbsp;<?php echo $this->lang->line('restaurant_admin_mobile') ?>">
                                                            <?php foreach ($admin['branch_admin'] as $branchkey => $branchvalue) {  ?>
                                                                <option value="<?php echo $branchvalue->entity_id ?>"><?php echo $branchvalue->first_name.' '.$branchvalue->last_name ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-offset-3">
                                                <button type="submit" name="submit_notification" id="submit_notification" value="Submit" class="btn btn-success theme-btn default-btn"><?php echo $this->lang->line('submit') ?></button>
                                            </div>
                                        </div>       
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <?php if(in_array('restaurant~view',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="col-md-12 col-lg-6">
                    <div class="portlet box red restaurant_table">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('restaurant') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/restaurant/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-hover">
                                    <thead>
                                    <tr> 
                                        <th><?php echo $this->lang->line('s_no') ?></th>
                                        <th><?php echo $this->lang->line('name') ?></th>
                                        <th><?php echo $this->lang->line('phone_number') ?></th>
                                        <th><?php echo $this->lang->line('email') ?></th>                                        
                                    </tr>                                    
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($restaurant)){
                                        $i = 1;
                                        foreach($restaurant as $key => $value){ ?>
                                             <tr>
                                                 <td data-title="<?php echo $this->lang->line('s_no') ?>"><?php echo $i; ?></td>
                                                 <td data-title="<?php echo $this->lang->line('name') ?>">
                                                    <a alt="<?php echo $value->name; ?>" title="<?php echo $value->name; ?>" href="<?php echo base_url().ADMIN_URL.'/restaurant/edit/'.$value->language_slug.'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value->entity_id)); ?>" title="<?php echo $this->lang->line('click_edit'); ?>" style="text-decoration:underline;" > <?php echo $value->name; ?> </a>
                                                 </td>
                                                 <?php $mobile_number = ($value->phone_code) ? ('+'.$value->phone_code.$value->phone_number) : ($value->phone_number)  ?>
                                                 <td data-title="<?php echo $this->lang->line('phone_number') ?>"><?php echo $mobile_number; ?></td>
                                                 <td data-title="<?php echo $this->lang->line('email') ?>"><?php echo $value->email; ?></td>
                                             </tr>                                         
                                    <?php $i++; } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } 
                if(in_array('order~view',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="col-md-12 col-lg-6" id="dashboard_order_grid">
                    <div class="portlet box red order_dashboard">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('order') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/order/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>                            
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('orderid') ?></th>
                                <th><?php echo $this->lang->line('customer') ?></th>
                                <th><?php echo $this->lang->line('order_total') ?></th>
                                <th><?php echo $this->lang->line('status') ?></th>
                                <th><?php echo $this->lang->line('date') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($orders)){
                                $i = 1;
                                foreach  ($orders as $key => $val) { 
                                    $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);                         ?>
                                     <tr>
                                         <td data-title="<?php echo $this->lang->line('orderid') ?>">
                                            <?php $href = base_url().ADMIN_URL.'/order/view/order_id/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id));
                                            if($val->order_delivery == 'DineIn'){ 
                                                $href = base_url().ADMIN_URL.'/order/dine_in_orders/order_id/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id));
                                            } ?>
                                            <a alt="<?php echo $this->lang->line('orders') ?>" title="<?php echo $this->lang->line('orders')?>" href="<?php echo $href; ?>" style="text-decoration:underline;" ><?php echo $val->entity_id; ?></a>
                                         </td>
                                         <td data-title="<?php echo $this->lang->line('customer') ?>"><?php echo $val->user_name ?></td>
                                         <td data-title="<?php echo $this->lang->line('order_total') ?>"><?php echo currency_symboldisplay(number_format_unchanged_precision($val->rate,$currency_symbol->currency_code),$currency_symbol->currency_symbol); ?></td>
                                         <td data-title="<?php echo $this->lang->line('status') ?>"><?php echo ucfirst($val->ostatus); ?></td>
                                         <td data-title="<?php echo $this->lang->line('date') ?>"><?php echo (isset($val->order_date) && date('d-m-Y',strtotime($val->order_date))!=='01-01-1970')?($this->common_model->datetimeFormat($val->order_date)):''; ?></td>
                                     </tr>
                                 
                            <?php $i++; } } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
                <?php } ?> 
            </div>   
            <!-- event and coupon start -->
            <div class="row">
                <?php if(in_array('event~view',$this->session->userdata("UserAccessArray"))) {  ?>
                <div class="col-md-12 col-lg-6">
                    <div class="portlet box red event_dashboard">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('title_admin_event') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/event/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('eventid') ?></th>
                                <th><?php echo $this->lang->line('customer') ?></th>
                                <th><?php echo $this->lang->line('amount') ?></th>
                                <th><?php echo $this->lang->line('status') ?></th>
                                <th><?php echo $this->lang->line('booking_date') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($events)){
                                $i = 1;
                                foreach  ($events as $key => $val) { 
                                    $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);                         ?>
                                     <tr>
                                         <td data-title="<?php echo $this->lang->line('eventid') ?>">
                                            <a alt="<?php echo $this->lang->line('title_admin_event') ?>" title="<?php echo $this->lang->line('title_admin_event')?>" href="<?php echo base_url().ADMIN_URL.'/event/view/event_id/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)); ?>" style="text-decoration:underline;" ><?php echo $val->entity_id; ?></a>    
                                         </td>
                                         <td data-title="<?php echo $this->lang->line('customer') ?>"><?php echo $val->fname.' '.$val->lname ?></td>
                                         <td data-title="<?php echo $this->lang->line('amount') ?>"><?php echo currency_symboldisplay(number_format_unchanged_precision($val->rate,$currency_symbol->currency_code),$currency_symbol->currency_symbol); ?></td>
                                         <td data-title="<?php echo $this->lang->line('status') ?>"><?php echo ($val->ostatus)?ucfirst($val->ostatus):'-'; ?></td>
                                         <td data-title="<?php echo $this->lang->line('booking_date') ?>"><?php echo (isset($val->booking_date) && date('d-m-Y',strtotime($val->booking_date))!=='01-01-1970')?($this->common_model->datetimeFormat($val->booking_date)):''; ?></td>
                                     </tr>
                                 
                            <?php $i++; } } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php }  ?>
                <?php if(in_array('coupon~view',$this->session->userdata("UserAccessArray"))) {  ?>
                <div class="col-md-12 col-lg-6">
                    <div class="portlet box red coupon_dashboard">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('title_admin_coupon') ?></div>
                            <div class="actions">
                                <a href="<?php echo base_url().ADMIN_URL?>/coupon/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>                                
                            </div>                            
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('s_no') ?></th>
                                <th><?php echo $this->lang->line('title_admin_coupon') ?></th>
                                <th><?php echo $this->lang->line('coupon_discount') ?></th>
                                <th><?php echo $this->lang->line('end_date') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($coupons)){
                                $i = 1;
                                foreach  ($coupons as $key => $val) { 
                                    $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id); ?>
                                     <tr>
                                         <td data-title="<?php echo $this->lang->line('s_no') ?>"><?php echo $i; ?></td>
                                         <td data-title="<?php echo $this->lang->line('title_admin_coupon') ?>">
                                            <a alt="<?php echo $val->name; ?>"  href="<?php echo base_url().ADMIN_URL.'/coupon/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)); ?>" title="<?php echo $val->name; ?>" style="text-decoration:underline;" ><?php echo $val->name ?></a>
                                         </td>
                                         <td data-title="<?php echo $this->lang->line('coupon_discount') ?>"><?php echo ($val->amount_type == "Percentage")?$val->amount."%":currency_symboldisplay(number_format_unchanged_precision($val->amount,$currency_symbol->currency_code),$currency_symbol->currency_symbol); ?></td>
                                         <td data-title="<?php echo $this->lang->line('end_date') ?>"><?php echo (isset($val->end_date) && date('d-m-Y',strtotime($val->end_date))!=='01-01-1970')?($this->common_model->datetimeFormat($val->end_date)):''; ?></td>
                                     </tr>
                                 
                            <?php $i++; } } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>  
                <?php } ?>
            </div>        
            <!-- event and coupon end -->                       
        </div>            
        <div class="clearfix">
        </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<div id="show_popup" class="modal fade" tabindex="-1" data-width="600">
    <div class="modal-dialog">
        <form id="accept_notification" method="post">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <?php if(!empty($Notifications)) { 
                                foreach ($Notifications as $key => $value) { ?>
                                    <p><?php echo ucfirst(nl2br($value['message'])); ?></p>
                                    <?php $btn_label =  ucfirst(nl2br($value['button_label'])); ?>

                                <?php } 
                        } ?>                            
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" id="submit_accept_notification" name="submit" class="btn btn-danger default-btn" value="<?php echo (isset($btn_label)) ? $btn_label : ''; ?>"> 

            </div>
        </div>
        </form>
    </div>
</div>
<!-- graph js start-->
<script src="<?php echo base_url();?>assets/admin/plugins/highchart/highcharts.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/charts/loader.js"></script>
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/daterangepicker/daterangepicker.min.js"></script>
<!-- graph js end -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {    
    <?php if(!empty($Notifications)) { ?>
        setTimeout(function(){
          $('#show_popup').modal('show');
        },3000); // 5000 to load it after 5 seconds from page load  
    <?php } ?>

    Metronic.init();
    Layout.init(); // init layout 
    var select = $('.sumo').SumoSelect({
        search: true,
        forceCustomRendering: true,
        searchText: "<?php echo $this->lang->line('search'); ?>"+' '+"<?php echo $this->lang->line('here'); ?>...",
        captionFormatAllSelected: '{0} <?php echo $this->lang->line('selected');?>!',locale: ['OK', 'Cancel', "<?php echo $this->lang->line('all').' '.$this->lang->line('select_');?>"],
        selectAll: true
    });
    $("html").on('click', '.select-all', function(){
        var myObj = $(this).closest('.SumoSelect.open').children()[0];
        if ($(this).hasClass("selected")) {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", true);
            $(myObj)[0].sumo.selectAll();
            $(this).parent().find("ul.options>li").addClass("selected");
        }
        else {
            $(this).parents(".SumoSelect").find("select>option").prop("selected", false);
            $(myObj)[0].sumo.unSelectAll();
            $(this).parent().find("ul.options>li").removeClass("selected");
        }
    });
});
</script>
<!-- END JAVASCRIPTS -->
<!-- Graph bar start -->
<script type="text/javascript">
// script for daterange
function load_monthwise_data(daterange)
{
    $.ajax({
        url:"<?php echo base_url(); ?>backoffice/dashboard/fetch_data",
        method:"POST",
        data:{daterange:daterange},
        dataType:"JSON",
        success:function(data)
        {
            drawMonthwiseChart(data);

        }
    })
}
</script>
<!-- for apply button in graph section-->
<script>
// range for daterange
var datepicker_format = "<?php echo daterangepicker_format; ?>";
$(document).ready(function(){
    var start = moment().subtract(29, 'days');
    var end = moment();
    function cb(start, end) {
        $('input[name="daterange"]').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('input[name="daterange"]').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        var daterange = $('#daterange').val();
        load_monthwise_data(daterange);
    }
    $('.daterangeicon').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
          format: datepicker_format
        },
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
    }, cb);
    cb(start, end);
});
//highchart js file start
function drawMonthwiseChart(data)
{
    var output_total = [];
    var output_day = [];
    for(var i = 0; i < data.length; i++){
        output_day.push(data[i].day);
        output_total.push(parseFloat(data[i].total));
    }
    var colors = Highcharts.getOptions().colors;
    Highcharts.setOptions({
        lang: {
        thousandsSep: ""
      }
    })
     window.chart = new Highcharts.chart('container', {
        chart: {
            type: 'spline',
        },

        legend: {
            symbolWidth: 0
        },

        yAxis: {
            title: {
                text: "<?php echo $this->lang->line('sales'); ?>"
            }
        },

        xAxis: {
            // title: {
            //     text: 'Time'
            // },
            categories: output_day
        },

        tooltip: {
            valueSuffix: ' <?php echo $currency_symbol->currency_symbol; ?>'
        },
        /*tooltip: {
            valuePrefix: '<?php //echo $currency_symbol->currency_symbol; ?>'
        },*/
        title: {
              text: ''
        },

        series: [
            {
                name: "<?php echo $this->lang->line('time'); ?>",
                data: output_total,
                color: colors[2],
                accessibility: {
                    description: "<?php echo $this->lang->line('order_graph'); ?>"
                }
            }
        ],
        credits: {
            enabled: false
        },

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 550
                },
                chartOptions: {
                    legend: {
                        itemWidth: 150
                    },
                    xAxis: {
                        categories: output_day
                    },
                    yAxis: {
                        title: {
                            enabled: false
                        },
                        /*labels: {
                            format: '{value}%'
                        }*/
                    }
                }
            }]
        },

    });
     window.chart.hideLoading();
}

$('.page-sidebar, .page-header').on('click', '.sidebar-toggler', function (e){
    var daterange = $('#daterange').val();
    if(daterange != '')
    {
        window.chart.showLoading();
        load_monthwise_data(daterange);
        //window.chart.hideLoading();
    }   
});
// highchart end   

// on accepting notifications
$("#submit_accept_notification").on( "click",function(){ 
    jQuery.ajax({
        dataType : "html",
        url : "<?php echo base_url();?>backoffice/dashboard/notification_accepted",
        //async: true,
        beforeSend: function() {
            $('#loading-image').show(); 
        },
        success: function(response) { 
            $('#loading-image').hide(); 
            if(response == 'success'){
                $('#show_popup').modal('hide');
            }else{
                alert("Something went wrong, try after some time.");
            }
            return false;
        },
        error: function(XMLHttpRequest, textstatus, errorThrown) {    
        $('#loading-image').hide();        
            alert(errorThrown);
        }
    });
});
$(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>