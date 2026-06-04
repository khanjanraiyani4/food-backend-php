<?php 
if(!($this->session->userdata('tip_percent_val')) && $this->session->userdata('tip_percent_val') > 0) {
	$this->session->set_userdata('tip_amount',0);
}
$driver_tip_arr = get_driver_tip_amount();
$is_custom_tip = 'yes';
$selected_tip = 0; ?>
<div class="driver_tip_title">
	<p><?php echo $this->lang->line('driver_tip') ?></p>
</div>
<div class="driver_tip_content">
	<div class="tip_row">
		<?php $oneselectedclass = '';
		foreach($driver_tip_arr as $key_tip=>$value_tip) { 
			$tip_percent_val = (float)$value_tip;
			$calculated_value_tip = ((float)$cart_details['cart_total_price'] * (float)$value_tip)/100;
			$calculated_value_tip = $this->common_model->roundDriverTip((float)$calculated_value_tip);

			$selectedclass = '';
			if($this->session->userdata('tip_percent_val') == (float)$tip_percent_val && $oneselectedclass == ''){
				$is_custom_tip = 'no';
				$selected_tip = $calculated_value_tip;
				$selectedclass = 'tip_selected';
				$oneselectedclass = 'tip_selected';
			}

			if(!($this->session->userdata('tip_amount')>0) && $selectedclass == 'tip_selected'){
				$this->session->set_userdata('tip_amount',$selected_tip);
			} else if(!($this->session->userdata('tip_percent_val')) && $this->session->userdata('tip_amount') > 0) {
				$selected_tip = (float)$this->session->userdata('tip_amount');
			} else if($selected_tip > 0 && $selectedclass == 'tip_selected') {
				$this->session->set_userdata('tip_amount',$selected_tip);
			} ?>
			<div class="tip_column">
				<div class="tip_card">
				<a href="javascript:void(0);" onclick="tip_selected(<?php echo $calculated_value_tip; ?>, this.id);" data-val="<?php echo $tip_percent_val; ?>" id="tip_<?php echo $key_tip?>" class="<?php echo $selectedclass; ?>" ><h6 class="form-control"><?php echo $value_tip.'%'; ?></h6></a>
				</div>
			</div>
		<?php } ?>
		<div class="tip_column">
			<div class="tip_card">
				<input type="text" oninput="tip_selected(this.value, this.id);" class="form-control" id="custom_tip" value="<?php echo ($selected_tip>0 && $is_custom_tip == 'yes')?$selected_tip:''; ?>" placeholder="<?php echo $this->lang->line('custom_tip') ?>" >
			</div>
			<div id="custom_tip_error" class="error" style="display: none;"><?php echo $this->lang->line('custom_tip_decimal_error') ?></div>
		</div>
	</div>
</div>
<div class="driver_tip_btns" >
	<input type="hidden" name="driver_tip" id="driver_tip" value="<?php echo ($selected_tip>0)?$selected_tip:''; ?>">
	<button type="button" disabled="disabled" id="tip_clear_btn" class="btn" onclick="applyTip('clear');"><?php echo $this->lang->line('clear') ?></button>
	<button type="button" disabled="disabled" id="tip_submit_btn" class="btn" onclick="applyTip('apply')"><?php echo $this->lang->line('submit') ?></button>
</div>
<script type="text/javascript">
	var selected_tip = <?php echo $selected_tip; ?>;
	if(selected_tip>0){
		$('#tip_submit_btn').attr('disabled',false);
        $("#tip_clear_btn").attr("disabled", false);
	}
</script>