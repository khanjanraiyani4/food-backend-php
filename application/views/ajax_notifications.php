<?php if (!empty($userNotifications) || !empty($event_booking_reminder) || !empty($table_booking_reminder)) { ?>
	<a href="#" class="notification-btn"><i class="iicon-icon-01"></i><span class="notification_count"><?php echo $notification_count; ?></span></a>
	<div class="noti-popup">
		<div class="noti-title">
			<h5><?php echo $this->lang->line('notification') ?></h5>
			<div class="bell-icon">
				<i class="iicon-icon-01"></i>
				<span class="notification_count"><?php echo $notification_count; ?></span>
			</div>
		</div>
		<div class="noti-list">
			<?php if (!empty($event_booking_reminder)) {
			    foreach ($event_booking_reminder as $key => $value) { ?>
				<div class="noti-list-box">
				<?php 
					$event_msg = $this->lang->line('reminder');
					$value['booking_date'] = $this->common_model->getZonebaseDateMDY($value['booking_date']);
					$time = $this->common_model->timeFormat($value['booking_date']);
				?>
				<?php $message =  sprintf($event_msg, $time , $value['rname'] , $value['address'] , $value['no_of_people']); ?>
					<div class="noti-list-text">
						<h6><?php echo $this->lang->line('event_reminder'); ?></h6>
						<p><?php echo $message; ?></p>
					</div>
				</div>
				<?php }
			} ?>
			<?php if (!empty($table_booking_reminder)) {
			    foreach ($table_booking_reminder as $key => $value) { ?>
				<div class="noti-list-box">
				<?php 
					$table_msg = $this->lang->line('table_reminder'); 
					$start_time = $this->common_model->getZonebaseTime($value['start_time']);
					$time = $this->common_model->timeFormat($start_time);
				?>
				<?php $message =  sprintf($table_msg, $time , $value['rname'] , $value['address'] , $value['no_of_people']); ?>
					<div class="noti-list-text">
						<h6><?php echo $this->lang->line('table_booking'); ?></h6>
						<p><?php echo $message; ?></p>
					</div>
				</div>
				<?php }
			} ?>
			<?php if (!empty($userNotifications)) {
			    foreach ($userNotifications as $key => $value) {
			        if (date("Y-m-d", strtotime($value['datetime'])) == date("Y-m-d")) {
			            //$noti_time = date("H:i:s") - date("H:i:s", strtotime($value['datetime']));
			            //$noti_time = abs($noti_time) . ' '.$this->lang->line('mins_ago');
			            $noti_time_cal = strtotime(date('Y-m-d h:i:s'))-strtotime($value['datetime']);
			            $noti_time_round = round(abs($noti_time_cal/60));
			            //in hours
			            if($noti_time_round>59){
			            	$noti_time_r= (round($noti_time_round/60));
			            	$hour_msg = ($noti_time_r>1)?$this->lang->line('hours_ago'):$this->lang->line('hour_ago');
			            	$noti_time = $noti_time_r .' '. $hour_msg;
			            }
			            //in mins
			            else{
			            	$min_msg = ($noti_time_round>1)?$this->lang->line('mins_ago'):$this->lang->line('min_ago');
			            	$noti_time =$noti_time_round .' '. $min_msg;
			            }
			        } else {
			            $d1 = strtotime(date("Y-m-d",strtotime($value['datetime'])));
						$d2 = strtotime(date("Y-m-d"));
						$noti_time = ($d2 - $d1)/86400;
						$noti_time = ($noti_time > 1 )?$noti_time.' '.$this->lang->line('days_ago'):$noti_time.' '.$this->lang->line('day_ago');
			        }
			        ?>
					<div class="noti-list-box">
						<?php $view_class = ($value['view_status'] == 0)?'unread':'read'; ?>
						<div class="noti-list-text <?php echo $view_class; ?>">
							<h6><?php echo ($value['notification_type'] == "order")?$this->lang->line('orderid'):(($value['notification_type'] == "event") ? ($this->lang->line('eventid')) : ''); ?>: #<?php echo $value['entity_id']; ?></h6> <?php //($this->lang->line('tableid'))?>
							<?php if($value['notification_slug'] == "order_rejected_refunded" || $value['notification_slug'] == "order_canceled_refunded" || $value['notification_slug'] == "order_initiated"){ ?>
								<p><?php echo ($value['notification_slug'] == "order_rejected_refunded")?sprintf($this->lang->line('refund_reject_noti'),$value['entity_id']):(($value['notification_slug'] == "order_canceled_refunded")?sprintf($this->lang->line('refund_cancel_noti'),$value['entity_id']):sprintf($this->lang->line('refund_initiated_noti'),$value['entity_id']));  ?></p>
							<?php } else if($value['notification_slug'] == "order_refund_failed" || $value['notification_slug'] == "order_refund_canceled" || $value['notification_slug'] == "tip_refund_initiated" || $value['notification_slug'] == "tip_refund_failed" || $value['notification_slug'] == "tip_refund_canceled" || $value['notification_slug'] == "order_refund_pending" || $value['notification_slug'] == "tip_refund_pending") { ?>
								<p><?php echo ($value['notification_slug'] == 'tip_refund_initiated')?sprintf($this->lang->line($value['notification_slug']),$value['entity_id']):(($value['transaction_id'])?sprintf($this->lang->line($value['notification_slug']),$value['entity_id'],$value['transaction_id']):sprintf(str_replace(" with transaction_id %s",".",$this->lang->line($value['notification_slug'])),$value['entity_id'])); ?></p>
							<?php } else if($value['notification_slug'] == "order_auto_cancelled") { ?>
								<p><?php echo sprintf($this->lang->line('order_autocancelled_notimsg'),$value['entity_id']); ?></p>
							<?php } else { ?>
								<p><?php echo ($value['notification_slug'] == "event_cancelled")?$this->lang->line('event_cancelled_noti'):sprintf($this->lang->line($value['notification_slug']),$value['entity_id']); ?></p>
							<?php } ?>
							<span class="min"><?php echo $noti_time; ?></span>
						</div>
					</div>
				<?php }
			} ?>
		</div>
	</div>
<?php }
else { ?>
	<a href="#" class="notification-btn"><i class="iicon-icon-01"></i><span>0</span></a>
	<div class="noti-popup">
		<div class="noti-title">
			<h5><?php echo $this->lang->line('notification') ?></h5>
			<div class="bell-icon">
				<i class="iicon-icon-01"></i>
				<span>0</span>
			</div>
		</div>
		<div class="viewall-btn">
			<a href="javascript:void(0)" class="btn"><?php echo $this->lang->line('no_notifications') ?></a>
		</div>
	</div>
<?php } ?> 
<script type="text/javascript">
	$(".notification-btn").on("click", function(e){
		$(".noti-popup").toggleClass("open");
		if($(".noti-popup").hasClass("open") === true){
			$(".mobile-icon  button").removeClass("open");
			$("#example-one").removeClass("open");
			$(".header-user-menu").removeClass("open");
		}
		e.stopPropagation();
		// unread the notifications
		jQuery.ajax({
            type : "POST",
			dataType : "html",
            url : '<?php echo base_url() . 'home/unreadNotifications' ?>',
            success: function(response) {
				//$('.notification_count').html(0);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
	});
</script>
