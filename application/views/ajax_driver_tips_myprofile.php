<?php $driver_tip_arr = get_driver_tip_amount();
$is_custom_tip = 'yes';
$default_driver_tip = get_default_driver_tip_amount();
$default_driver_tip = ($default_driver_tip > 0 && $default_driver_tip != '') ? (float)$default_driver_tip : 0;
$selected_tip = $default_driver_tip; ?>
<script type="text/javascript">
    var field_required ="<?php echo $this->lang->line('field_required');?>";
</script>
<div class="driver_tip_content">
    <div class="tip_row">
        <?php $oneselectedclass = '';
        foreach($driver_tip_arr as $key_tip=>$value_tip){ 
            $tip_percent_val = $value_tip;
            $calculated_value_tip = ((float)$order_subtotal * (float)$value_tip)/100;
            $calculated_value_tip = $this->common_model->roundDriverTip((float)$calculated_value_tip);

            $selectedclass = '';
            if($selected_tip == (float)$value_tip && $oneselectedclass == ''){
                $is_custom_tip = 'no';
                $selected_tip = $calculated_value_tip;
                $selectedclass = 'tip_selected';
                $oneselectedclass = 'tip_selected';
            } ?>
            <div class="tip_column">
                <div class="tip_card">
                    <a href="javascript:void(0);" onclick="tip_selected(<?php echo $calculated_value_tip; ?>, this.id,'<?php echo $currency_symbol->currency_symbol; ?>');" id="tip_<?php echo $key_tip?>" data-val="<?php echo $tip_percent_val; ?>" class="<?php echo $selectedclass; ?>" ><h6 class="form-control"><?php echo $value_tip.'%'; ?></h6></a>
                </div>
            </div>
        <?php } ?>
        <div class="tip_column">
            <div class="tip_card">
                <input type="text" oninput="tip_selected(this.value, this.id,'<?php echo $currency_symbol->currency_symbol; ?>');" class="form-control" id="custom_tip" value="<?php echo ($selected_tip>0 && $is_custom_tip == 'yes')?$selected_tip:''; ?>" placeholder="<?php echo $this->lang->line('custom_tip') ?>" >
            </div>
            <div id="custom_tip_error" class="error" style="display: none;"><?php echo $this->lang->line('custom_tip_decimal_error') ?></div>
        </div>
    </div>
</div>
<div class="driver_tip_btns row" >
    <div class="col-md-12">
        <h6 class="text-left"><?php echo $this->lang->line('sod_driver_tip_amount').': '; ?><span id="display_tip" ><?php echo ($selected_tip>0)?currency_symboldisplay($selected_tip,$currency_symbol->currency_symbol):currency_symboldisplay(0,$currency_symbol->currency_symbol);  ?></span></h6>
    </div>
    <div class="card">
        <div class="radio-btn-list">
            <?php 
            $cnt = 1;
            if(!empty($payment_option)) {
                foreach($payment_option as $payment_method){ ?>
                    <label>
                        <input type="radio" name="payment_option" class="payment_option" id="payment_option<?php echo $cnt; ?>" value="<?php echo $payment_method->payment_gateway_slug; ?>" /><span><?php echo $payment_method->payment_name ?></span>
                    </label>
                <?php $cnt++; }
            } else { ?>
                <span style="color:red;"><?php echo $this->lang->line('tippayment_method_msg'); ?></span>
            <?php } ?>
            <label style="color:red;" id="blankmsg"></label>
        </div>
    </div>
    <div class="col-md-12 text-center">
        <input type="hidden" name="driver_tip" id="driver_tip" value="<?php echo ($selected_tip>0)?$selected_tip:''; ?>">
        <input type="hidden" name="tip_order_id" id="tip_order_id" value="">
        <button type="button" disabled="disabled" id="tip_clear_btn" class="btn" onclick="applyTipForOrders('clear','<?php echo $currency_symbol->currency_symbol; ?>');"><?php echo $this->lang->line('clear') ?></button>
        <button type="button" disabled="disabled" id="tip_submit_btn" class="btn" onclick="applyTipForOrders('apply','')"><?php echo $this->lang->line('submit') ?></button>
        <div id='paypal-button'></div>
    </div>
</div>
<script type="text/javascript">
    $("#paypal-button").empty();
    var selected_tip = <?php echo $selected_tip; ?>;
    if(selected_tip>0){
        $('#tip_submit_btn').attr('disabled',false);
        $("#tip_clear_btn").attr("disabled", false);
    }
    <?php if(empty($payment_option)) { ?>
        $('#tip_submit_btn').attr('disabled',true);
    <?php } ?>

    $('.payment_option').click(function()
    {
        var radioValue = $("input[name='payment_option']:checked").val();
        if(radioValue == "stripe")
        {
            $('#tip_submit_btn').show();
            $('#paypal-button').hide();
            $('#blankmsg').html('');
            $('#paypal-button').html('');
            $('#tip_submit_btn').attr('disabled',false);
        }
        else if(radioValue == "paypal")
        {
            $('#tip_submit_btn').hide();
            $('#paypal-button').show();
            $('#blankmsg').html('');
            $('#tip_submit_btn').attr('disabled',true);            
            mount_paypal_element();
        }        
    });    
</script>