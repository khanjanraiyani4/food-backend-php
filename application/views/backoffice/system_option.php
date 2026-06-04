<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<style type="text/css">
    .repeater_wrap{
        padding: 15px 20px 15px;
    }
    .repeater_wrap .btn-black{
        background-color: #000000;
        color: #FFFFFF;
    }
</style>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL.'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    <?php echo $this->lang->line('titleadmin_systemoptions') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('titleadmin_systemoptions') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <!-- <div class="portlet box red"> -->
                        <!-- <div class="portlet-title">
                            <div class="caption"> <?php //echo $this->lang->line('titleadmin_systemoptions') ?></div>
                            <div class="actions"></div>
                        </div> -->
                        <!-- <div class="portlet-body form"> -->
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url().ADMIN_URL;?>/system_option/view" method="post" id="SystemOption" name="SystemOption" class="form-horizontal" enctype="multipart/form-data">
                                <div class="form-body">                                    
                                    <?php if(isset($_SESSION['SystemOptionMSG']))
                                    { ?>
                                        <div class="alert alert-success alerttimerclose">
                                             <?php echo $_SESSION['SystemOptionMSG'];
                                             unset($_SESSION['SystemOptionMSG']);
                                             ?>
                                        </div>
                                    <?php } ?>                                    
                                    <?php if(isset($_SESSION['file_error']))
                                    { ?>
                                        <div class="alert alert-danger alerttimerclose">
                                             <?php echo $_SESSION['file_error'];
                                             unset($_SESSION['file_error']);
                                             ?>
                                        </div>
                                    <?php } ?>                                    
                                    <?php 
                                    foreach ($arrSystemOptions as $group_lang_var => $SystemOptionList) {
                                        ?><div class="row">
                                            <div class="col-sm-12 app--configurations">
                                                <div class="portlet box red">
                                                    <div class="portlet-title">
                                                        <div class="caption"><?php echo $this->lang->line($group_lang_var);?></div>
                                                        <div class="actions"></div>
                                                    </div>
                                                    <div class="portlet-body"><?php    
                                                        // Only for app versions                         
                                                        if($group_lang_var == "sg_live_app_version_configurations" || $group_lang_var == "sg_schedule_mode_configurations" ){    
                                                            ?><div class="row"><?php
                                                        }

                                                        foreach ($SystemOptionList as $key => $OptionDet){ 
                                                            $distance_inVal = $this->lang->line('in_km');
                                                            if($distance_inarr && !empty($distance_inarr))
                                                            {
                                                                if($distance_inarr->OptionValue==0){
                                                                    $distance_inVal = $this->lang->line('in_mile');
                                                                }
                                                            }

                                                            $optionName = $this->lang->line($OptionDet->OptionName);
                                                            $OptionSlugArr = array('driver_near_km','maximum_range','driver_commission_less','driver_commission_more','user_near_km','maximum_range_pickup');
                                                            if(in_array(strtolower($OptionDet->OptionSlug), $OptionSlugArr))
                                                            {
                                                                $optionName = sprintf($optionName,$distance_inVal);                 
                                                            }
                                                            
                                                            // Only for app versions
                                                            if($OptionDet->FieldType == "text" && ($group_lang_var == "sg_live_app_version_configurations" || $group_lang_var == "sg_schedule_mode_configurations")){
                                                                $strClass = "";
                                                                $readonly = "";                
                                                                ?>                        
                                                                <div class="col-md-6">
                                                                    <div class="form-group">                
                                                                        <label class="control-label col-md-4"><?php echo $optionName; ?><span class="required">*</span></label>
                                                                        <div class="col-md-4">
                                                                            <div class="system-option-fields">
                                                                                <input type="text" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" value="<?php echo htmlentities($OptionDet->OptionValue); ?>" maxlength="250" class="<?php echo $strClass;?> form-control required" <?php echo $readonly;?>>
                                                                            
                                                                                <?php 
                                                                                if(!empty($OptionDet->Description) && !empty($this->lang->line($OptionDet->Description))){
                                                                                    ?><div class="custom--tooltip">
                                                                                        <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                                                        <span class="tooltiptext tooltip-right"><?php echo $this->lang->line($OptionDet->Description);?>
                                                                                        </span>
                                                                                    </div><?php
                                                                                }
                                                                            ?></div>
                                                                        </div>   
                                                                    </div>
                                                                </div><?php
                                                            } else if($OptionDet->FieldType == "text" && $group_lang_var == "sg_driver_tip_configurations"){ ?>
                                                                <div class="form-group" style="display:flex;">
                                                                    <label class="control-label col-md-3" style="display:flex;align-items: center;"><?php echo $optionName; ?><span class="required">*</span></label>
                                                                    <div id="item_name" class="col-md-6 repeater_wrap item_name" >
                                                                        <div data-repeater-list="<?php echo $OptionDet->OptionSlug ?>[]" class="driver_tip_detail">
                                                                            <div class="form-group">
                                                                                <div class="col-md-5 col-xs-5">
                                                                                    <label class="control-label"><?php echo $this->lang->line('tip_percent') ?><span class="required">*</span></label>
                                                                                </div>                              
                                                                                <div class="col-md-4 col-xs-4">
                                                                                    <label class="control-label"><?php echo $this->lang->line('is_default') ?></label> 
                                                                                </div>
                                                                                <div class="col-md-3 col-xs-3 delete-repeat" >
                                                                                </div>
                                                                            </div>
                                                                            <?php if(!empty(htmlentities($OptionDet->OptionValue))) {
                                                                                    $driver_tip_detail = explode("\r\n",htmlentities($OptionDet->OptionValue));
                                                                                    $driver_tip_detail = array_filter($driver_tip_detail);
                                                                                    for ($i=0;$i <= count($driver_tip_detail)-1;$i++) { ?>
                                                                            <div data-repeater-item class="outer-repeater">
                                                                                <div class="form-group">
                                                                                    <div class="col-md-5 col-xs-5" style="margin-bottom: 10px;">
                                                                                        <input type="text" name="tip_amount" id="tip_amount<?php echo $i; ?>" class="form-control drivertip_input repeater_field first-field" value="<?php echo (!empty($driver_tip_detail[$i])) ? $driver_tip_detail[$i] : ''; ?>" required="required" min="1" max="100" maxlength="6" >
                                                                                    </div>
                                                                                    <div class="col-md-4 col-xs-4">
                                                                                        <input type="radio" class="default_driver_tip" id="default_driver_tip_<?php echo $i ?>" name="default_driver_tip" value='<?php echo $driver_tip_detail[$i] ?>' <?php echo ($default_drivertip_opt->OptionValue == $driver_tip_detail[$i])?"checked":'' ?> onclick="selectDefaultDriverTip(this.name);" >
                                                                                    </div>
                                                                                    <div class="col-md-3 col-xs-3 delete-repeat" >
                                                                                        <input data-repeater-delete class="btn btn-danger" type="button" value="<?php echo $this->lang->line('delete') ?>"/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?php } } ?>
                                                                        </div>
                                                                        <div class="form-group">
                                                                        <div class="col-md-12">
                                                                            <input data-repeater-create class="btn btn-black" type="button" value="<?php echo $this->lang->line('add') ?>"/>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4" style="display:flex;align-items: center;">
                                                                        <?php if(!empty($OptionDet->Description) && !empty($this->lang->line($OptionDet->Description))){
                                                                            ?><div class="custom--tooltip">
                                                                                <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                                                <span class="tooltiptext tooltip-right"><?php echo $this->lang->line($OptionDet->Description);?>
                                                                                </span>
                                                                            </div><?php
                                                                        } ?>
                                                                    </div>
                                                                </div>
                                                                <script>
                                                                    jQuery(document).ready(function() {
                                                                        jQuery('.repeater_wrap').repeater({
                                                                            <?php if($OptionDet->OptionValue != ''){ ?>
                                                                            isFirstItemUndeletable: true,
                                                                            <?php } ?>
                                                                            show: function () {
                                                                                var count = $('.outer-repeater').length;
                                                                                var new_id = count + 1;
                                                                                $(this).slideDown();
                                                                                $(this).find('.delete-repeat').show();
                                                                                $(this).find('.repeater_field').attr('required',true);
                                                                                $(this).find('.repeater_field').addClass('error');
                                                                                $(this).find('.drivertip_input').attr('id','tip_amount'+new_id);
                                                                            },
                                                                            hide: function (deleteElement) {
                                                                                $(this).slideUp(deleteElement);
                                                                            }
                                                                        });
                                                                    });
                                                                </script>
                                                            <?php } else if ($OptionDet->FieldType == "file" && $group_lang_var == "sg_language_file_configurations"){
                                                            ?>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><?php echo $optionName; ?></label>
                                                                    <div class="col-md-4">
                                                                        <div class="system-option-fields">
                                                                            <input type="file" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <?php if(!empty($OptionDet->OptionValue) || !is_null($OptionDet->OptionValue)){ ?>
                                                                        <label class="control-label col-md-3">
                                                                            <a href="<?php echo base_url().$OptionDet->OptionValue ?>" target="_blank" title="<?php echo $this->lang->line('download_file'); ?>">
                                                                            <?php echo $this->lang->line('previously_uploaded_file'); ?>
                                                                            </a>
                                                                        </label>
                                                                    <?php } ?>
                                                                    <div class="col-md-2">
                                                                        <?php if(!empty($OptionDet->Description) && !empty($this->lang->line($OptionDet->Description))){
                                                                            ?><div class="custom--tooltip">
                                                                                <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                                                <span class="tooltiptext tooltip-right"><?php echo $this->lang->line($OptionDet->Description);?>
                                                                                </span>
                                                                            </div><?php
                                                                        } ?>
                                                                    </div>
                                                                </div>
                                                            <?php } else if($OptionDet->FieldType == "radio" && $group_lang_var == "sg_user_verification_configurations"){ ?>

                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><?php echo $optionName; ?></label>
                                                                    <div class="col-md-4" style="    padding-top: 5px;">
                                                                        <div class="col-md-12">
                                                                            <input type="radio" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>_email" value="email" <?php echo ($OptionDet->OptionValue) ? ($OptionDet->OptionValue == 'email') ? 'checked' : '' : 'checked' ?>>&nbsp;<label for="<?php echo $OptionDet->OptionSlug ?>_email"><?php echo $this->lang->line('email') ?></label>&ensp;
                                                                            <input type="radio" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>_mobile" value="mobile" <?php echo ($OptionDet->OptionValue && $OptionDet->OptionValue == 'mobile') ? 'checked' : '' ?>>&nbsp;<label for="<?php echo $OptionDet->OptionSlug ?>_mobile"><?php echo $this->lang->line('phone_number') ?></label>&ensp;
                                                                            <input type="radio" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>_none" value="none" <?php echo ($OptionDet->OptionValue && $OptionDet->OptionValue == 'none') ? 'checked' : '' ?>>&nbsp;<label for="<?php echo $OptionDet->OptionSlug ?>_none"><?php echo $this->lang->line('none') ?></label>&ensp;
                                                                            <div id="user_type_error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            <?php } else {
                                                                // other than app versions group
                                                                $optiongrparr =array('website_body_script','website_header_script','website_footer_script');
                                                                ?><div class="form-group">
                                                                    <label class="control-label col-md-3"><?php echo $optionName; ?>
                                                                    <?php if(!in_array($OptionDet->OptionSlug, $optiongrparr)){ ?><span class="required">*</span><?php } ?></label>
                                                                    <div class="col-md-4">
                                                                    <div class="system-option-fields"><?php                 
                                                                    // Field Type - Text
                                                                        if($OptionDet->FieldType == "text"){
                                                                            $strClass = "";
                                                                            $readonly = "";
                                                                            if($OptionDet->OptionSlug == 'phone_code'){
                                                                                $strClass = $OptionDet->OptionSlug;
                                                                            }
                                                                            if($OptionDet->OptionSlug == 'phone_code' || $OptionDet->OptionSlug == 'minimum_range'){
                                                                                $readonly = "readonly";
                                                                            }
                                                                            ?>                                
                                                                            <input type="text" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" value="<?php echo htmlentities($OptionDet->OptionValue); ?>" maxlength="250" class="<?php echo $strClass;?> form-control required" <?php echo $readonly;?>><?php
                                                                        } 
                                                                        // Field Type - textarea
                                                                        if ($OptionDet->FieldType == "textarea"){
                                                                            ?><textarea class="form-control" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" placeholder="" rows="4" cols="50" style="resize:none;"><?php echo htmlentities($OptionDet->OptionValue); ?></textarea><?php
                                                                        }
                                                                        // Field Type - radio
                                                                        if ($OptionDet->FieldType == "radio"){
                                                                            $option_name = $this->lang->line('yes');
                                                                            $option_name1 = $this->lang->line('no');
                                                                            if($OptionDet->OptionSlug=='distance_in')
                                                                            {
                                                                                $option_name = $this->lang->line('in_km');
                                                                                $option_name1 = $this->lang->line('in_mile');
                                                                            }
                                                                            ?><input type="radio" <?php echo ($OptionDet->OptionValue)?($OptionDet->OptionValue == '1')?'checked':'':'checked' ?>  name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>_true" value="1">&nbsp;<label for="<?php echo $OptionDet->OptionSlug ?>_true"><?php echo $option_name; ?></label>&ensp;
                                                                            <input type="radio" <?php echo ($OptionDet->OptionValue == '0')?'checked':'' ?>  name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>_false" value="0">&nbsp;<label for="<?php echo $OptionDet->OptionSlug ?>_false"><?php echo $option_name1; ?></label><?php
                                                                        }
                                                                        // Field Type - Toggle
                                                                        if ($OptionDet->FieldType == "toggle"){
                                                                            ?><a style="cursor:pointer;" class="togglebutton">
                                                                                <i class="i_toggle fa fa-toggle-<?php if($OptionDet->OptionValue == 1) { echo "on"; } else{echo "off";} ?> fa-2x"  id="i_<?php echo $OptionDet->OptionSlug ?>_on_off_toggle" style="    vertical-align: bottom;"></i>
                                                                            </a>
                                                                            <input type="hidden" name="<?php echo $OptionDet->OptionSlug ?>" value="<?php echo htmlentities($OptionDet->OptionValue); ?>" id="<?php echo $OptionDet->OptionSlug ?>" class="toggle_hidden_value"><?php
                                                                        }
                                                                        if($OptionDet->FieldType == "dropdown"){
                                                                            if($OptionDet->OptionSlug == 'default_language'){
                                                                                ?><select class="form-control sumo select_default_language" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" required >
                                                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                                                    <?php 
                                                                                    if(!empty($languageArray)){
                                                                                        foreach ($languageArray as $lang_key => $lang_value) { 
                                                                                            ?>          
                                                                                            <option value="<?php echo $lang_value->language_slug; ?>"  <?php echo ($lang_value->language_slug == $OptionDet->OptionValue)?'selected':''; ?>><?php echo $lang_value->language_name;?></option>    
                                                                                        <?php } 
                                                                                    } ?>
                                                                                </select><?php
                                                                            }
                                                                            if($OptionDet->OptionSlug == 'country'){
                                                                                ?><select class="form-control sumo select_country" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" required >
                                                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                                                    <?php 
                                                                                    if(!empty($countryArray)){
                                                                                        foreach ($countryArray as $country_key => $country_value) { 
                                                                                            ?>          
                                                                                            <option value="<?php echo $country_value->name; ?>" data-id="+<?php echo $country_value->phonecode; ?>" <?php echo ($country_value->name == $OptionDet->OptionValue)?'selected':''; ?>><?php echo $country_value->name;?></option>    
                                                                                        <?php } 
                                                                                    } ?>
                                                                                </select><?php
                                                                            }
                                                                            if($OptionDet->OptionSlug == 'currency'){
                                                                                $currency = (isset($res_currency_id))?$res_currency_id:$OptionDet->OptionValue; 
                                                                                $point = "style='pointer-events: none;'"; 
                                                                                ?><select class="form-control sumo" name="<?php echo $OptionDet->OptionSlug ?>" id="<?php echo $OptionDet->OptionSlug ?>" <?php //echo ($currency)?"readonly ".$point:"" ?> required >
                                                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                                                    <?php if (!empty($currencies)) {
                                                                                        foreach ($currencies as $key => $value) {
                                                                                                ?>
                                                                                                <option value="<?php echo $value['currency_id'];?>" <?php echo ($currency==$value['currency_id'])?"selected":""?>><?php echo $value['country_name'].' - '.$value['currency_code'];?></option>    
                                                                                                <?php 
                                                                                        } 
                                                                                    } ?>
                                                                                </select><?php
                                                                            }
                                                                        } ?>
                                                                        <?php 
                                                                        if(!empty($OptionDet->Description) && !empty($this->lang->line($OptionDet->Description))){
                                                                            ?><div class="custom--tooltip">
                                                                                <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                                                                <span class="tooltiptext tooltip-right"><?php echo $this->lang->line($OptionDet->Description);?>
                                                                                    <?php if($OptionDet->Description=='sod_default_country')
                                                                                    { 
                                                                                        echo "<br>".$this->lang->line('sod_admin_address');
                                                                                    } ?>
                                                                                    </span>
                                                                            </div>
                                                                            <?php
                                                                        }                               
                                                                        ?>
                                                                    </div>
                                                                    </div>                        
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        // Only for app versions
                                                        if($group_lang_var == "sg_live_app_version_configurations" ){    
                                                            ?></div><?php
                                                        }
                                                    ?></div>
                                                </div>
                                            </div>
                                        </div><?php
                                    }?>
                                </div>
                                <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin') { ?>
                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="SubmitSystemSetting" id="SubmitSystemSetting" class="btn default-btn danger-btn theme-btn" value="Submit"><?php echo $this->lang->line('submit') ?></button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </form>
                            <!-- END FORM-->
                        <!-- </div> -->
                    <!-- </div> -->
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/system-autofill.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
     $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
    $('.select_country').change(function(){
       var code =  $(this).find(':selected').attr('data-id');
       $('.phone_code').val(code);
    });
});
jQuery('#SystemOption').validate({
    ignore:[],
    rules:{
        driver_commission_less: {
          required: true,
          number: true,
          min:1
        },
        driver_commission_more: {
          required: true,
          number: true,
          min:1
        },
        maximum_range: {
          required: true,
          number: true,
          min:1
        },
        maximum_range_pickup: {
          required: true,
          number: true,
          min:1
        },
        earning_1_point: {
          required: true,
          number: true,
          min:1
        },
        min_order_amount: {
          required: true,
          number: true,
          min:1
        },
        USER_NEAR_KM: {
          required: true,
          number: true,
          min:1
        },
        minimum_subtotal: {
          required: true,
          number: true,
          min:1
        },
        min_redeem_point: {
          required: true,
          number: true,
          min:1
        },
        referral_amount: {
          required: true,
          number: true,
          min:1
        },
        language_file_mobile_app:{
            required: false,
            extension: "xlsx"
        },
        user_verification_type:{
            required: true
        },
        cancel_order_timer : {
            required: true,
            digits: true,
            min:1,
            lessThanDelayedTimeForCancelByUser : '#delayed_order_timer',
            lessThanAutoCancelTime : '#auto_cancel_order_timer'
        },
        automated_call_timer : {
            required: true,
            digits: true,
            min:1,
            lessThanAutoCancelTime: '#auto_cancel_order_timer',
            greaterThanCancelOrderTime : '#cancel_order_timer'
        },
        auto_cancel_order_timer : {
            required: true,
            digits: true,
            min:1,
            lesserThanDelayedOrderTime : '#delayed_order_timer',
            greaterThanCancelOrderTime : '#cancel_order_timer'
        },
        delayed_order_timer : {
            required: true,
            digits: true,
            min:1,
            greaterThanAutoCancellationTime : '#auto_cancel_order_timer',
            greaterThanCancelTimeForDelayedTimeField : '#cancel_order_timer'
        },
        time_interval_for_scheduling : {
            required: true,
            digits: true,
            min:1
        }
    },
    errorPlacement: function(error, element) 
    {
        if(element.next('p').length > 0){
            error.insertAfter(element.next('p'));
        }
        else if( element.attr("name") == "user_verification_type"){
            error.insertAfter('#user_type_error'); 
        }
        else 
        {
            error.insertAfter(element);
        }
    }
});
$(".togglebutton").on("click",function(){    
    $(this).find('.i_toggle').toggleClass('fa-toggle-off fa-toggle-on');
    if($(this).find('.i_toggle').hasClass("fa-toggle-off")){
        $(this).next(".toggle_hidden_value").val("0");
    }
    if($(this).find('.i_toggle').hasClass("fa-toggle-on")){
        $(this).next(".toggle_hidden_value").val("1");
    }
});
function selectDefaultDriverTip(name_element){
    $(".default_driver_tip").attr("checked", false);
    $('input[name="'+name_element+'"]').attr('checked', true);
}

// for cancel_order_timer
jQuery.validator.addMethod('lessThanDelayedTimeForCancelByUser', function(value, element, param) {
    if(value && jQuery(param).val()) {
        var value_in_minutes = Math.floor(parseInt(value) / 60);
        if(parseInt(value_in_minutes) < parseInt(jQuery(param).val())) {
            var delayedorder_label = $("#delayed_order_timer").next('label').attr("for", "delayed_order_timer");
            delayedorder_label.css('display','none');
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}, 'Must be less than Delayed Order Time');
jQuery.validator.addMethod('lessThanAutoCancelTime', function(value, element, param) {
    if(value && jQuery(param).val()) {
        if(element.name == 'cancel_order_timer') {
            var value_in_minutes = Math.floor(parseInt(value) / 60);
        } else {
            var value_in_minutes = value;
        }
        if(parseInt(value_in_minutes) < parseInt(jQuery(param).val())) {
            if(element.name == 'cancel_order_timer') {
                var autocancel_label = $("#auto_cancel_order_timer").next('label').attr("for", "auto_cancel_order_timer");
                autocancel_label.css('display','none');
            }
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}, 'Must be less than Order Auto Cancellation Time');

//for auto_cancel_order_timer
jQuery.validator.addMethod('lesserThanDelayedOrderTime', function(value, element, param) {
    if(value && jQuery(param).val()) {
        if(parseInt(value) < parseInt(jQuery(param).val())) {
            var delayedorder_label = $("#delayed_order_timer").next('label').attr("for", "delayed_order_timer");
            delayedorder_label.css('display','none');
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}, 'Must be less than Delayed Order Time');
$.validator.addMethod("greaterThanCancelOrderTime", function(value, element, param) {
    if(value && jQuery(param).val()) {
        var param_in_minutes = Math.floor(parseInt(jQuery(param).val()) / 60);
        if(parseInt(value) > parseInt(param_in_minutes)) {
            var cancel_label = $("#cancel_order_timer").next('label').attr("for", "cancel_order_timer");
            cancel_label.css('display','none');
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }  
}, "Must be greater than Cancel Order Timer");

//for delayed_order_timer
$.validator.addMethod("greaterThanAutoCancellationTime", function(value, element, param) {
    if(value && jQuery(param).val()) {
        if(parseInt(value) > parseInt(jQuery(param).val())) {
            var autocancel_label = $("#auto_cancel_order_timer").next('label').attr("for", "auto_cancel_order_timer");
            autocancel_label.css('display','none');
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }  
}, "Must be greater than Order Auto Cancellation Time");
$.validator.addMethod("greaterThanCancelTimeForDelayedTimeField", function(value, element, param) {
    if(value && jQuery(param).val()) {
        var param_in_minutes = Math.floor(parseInt(jQuery(param).val()) / 60);
        if(parseInt(value) > parseInt(param_in_minutes)) {
            var cancel_label = $("#cancel_order_timer").next('label').attr("for", "cancel_order_timer");
            cancel_label.css('display','none');
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }  
}, "Must be greater than Cancel Order Timer");

/*function positiveInt(input_value,element_id){
    $('#'+element_id).val(input_value.replace(/\D/g, ""));
}
function positiveIntandDecimal(input_value,element_id){
    $('#'+element_id).val(input_value.replace(/[^0-9\.]/g, ''));
    if(input_value == 0 || input_value == '-0'){
        $('#'+element_id).val(input_value.replace(input_value,''));
    } else if(input_value < 0 || input_value == '-'){
        $('#'+element_id).val(input_value.replace('-',''));
    } 
}
function latlongvalidation(input_value,element_id){
    $('#'+element_id).val(input_value.replace(/[^0-9\.-]/g, ''));
}
function onoff() {
    $("#on_off_toggle").toggleClass('fa-toggle-off fa-toggle-on');
    if($("#on_off_toggle").hasClass("fa-toggle-off")){
        document.getElementById("enable_commission_of_driver").value = "0";
    }
    if($("#on_off_toggle").hasClass("fa-toggle-on")){
        document.getElementById("enable_commission_of_driver").value = "1";
    }
}*/

/*// custom code for greater than
$.validator.addMethod("greater", function(value, element, param) {
  return ( parseInt(value) > parseInt(jQuery(param).val()) );    
}, "Must be greater than Minimum Redeem Points");
// custom code for greater than
$.validator.addMethod("greaterThan", function(value, element, param) {
  return ( parseInt(value) > parseInt(jQuery(param).val()) );    
}, "Must be greater than Maximum Earning Points");
// custom code for lesser than
jQuery.validator.addMethod('lesserThan', function(value, element, param) {  
  return ( parseInt(value) <= parseInt(jQuery(param).val()) );
}, 'Must be less than Minimum Subtotal' );*/

$(document).ready(function() {
    setTimeout(function() {
        $("div.alerttimerclose").alert('close');
    }, 5000);
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>