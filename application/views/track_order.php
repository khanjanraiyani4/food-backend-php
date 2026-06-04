<?php defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('header'); ?>

<section class="inner-pages-section">
	<div class="container">
		<div class="row" id="track_order_content">
			<?php if ((($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID'))) || $is_guest_track_order =='1') && !empty($latestOrder)) { ?>
				<div class="col-lg-12">
					<div class="heading-title">
						<h2><?php echo $this->lang->line('track_order') ?></h2>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="track-order-main">
						<?php if($latestOrder->delivery_method != 'relay' && $latestOrder->order_delivery != 'PickUp') { ?>
						<?php if(($latestOrder->delivery_method == 'doordash') && $latestOrder->delivery_tracking_url) { ?>
							<a href="<?php echo $latestOrder->delivery_tracking_url; ?>" class="btn" target='_blank' ><?php echo $this->lang->line('clickto_track_order') ?></a>
							<!-- <iframe src="<?php echo $latestOrder->delivery_tracking_url; ?>" style="height:500px;width:1057px;" title="<?php echo $this->lang->line('thirdparty_delivery'); ?>"></iframe> -->
						<?php } else { ?>
							<div class="track-order-map">
								<!-- <img src="<?php //echo base_url();?>assets/front/images/map.jpg"> -->
								<div class="row">
				                    <div class="col-md-12 modal_body_map">
				                        <div class="location-map" id="location-map">
				                            <div id="map_canvas"></div>
				                        </div>
				                    </div>
				                </div>
							</div>
						<?php } } ?>
						<div class="track-order-content">
							<div class="track-order-text">
								<div class="track-order-head">
									<h2><?php echo $this->lang->line('hey') ?> <?php echo ($this->session->userdata('UserType') == 'Agent')?$this->session->userdata('userFirstname').' '.$this->session->userdata('userLastname'):$latestOrder->user_name; ?>!</h2>
									<p><?php echo $this->lang->line('order_msg') ?></p>
									<!-- <p><?php //echo ($latestOrder->status == 1)?'Your order has been accepted!':''; ?></p> -->
								</div>
								<div class="order-id-details">
									<div class="order-id">
										<strong><?php echo $this->lang->line('orderid') ?> : #<?php echo $latestOrder->master_order_id; ?></strong>
									</div>
									<?php if($latestOrder->order_delivery != 'PickUp' || ($latestOrder->scheduled_date && $latestOrder->slot_open_time && $latestOrder->slot_close_time)) { ?>
										<div class="details-id">
											<?php if($latestOrder->scheduled_date && $latestOrder->slot_open_time && $latestOrder->slot_close_time) { ?>
												<p><i class="iicon-icon-18" style="color:#17161a;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('order_scheduled_for').$this->common_model->dateFormat($latestOrder->scheduled_date).' ('.$this->common_model->timeFormat($latestOrder->slot_open_time).' - '.$this->common_model->timeFormat($latestOrder->slot_close_time).' )'; ?></p>
											<?php } ?>
											<?php if($latestOrder->order_delivery != 'PickUp') { ?>
												<div class="details-id-content">
													<div class="details-id-text">
														<?php if($latestOrder->delivery_method != 'relay'){ 
															if(!empty($thirdparty_driver_details)){ ?>
																<p><?php echo ($thirdparty_driver_details['first_name']) ? $thirdparty_driver_details['first_name'] . $this->lang->line('order_msg2'):$this->lang->line('order_msg3'); ?></p>
															<?php }else{ ?>
																<p><?php echo ($latestOrder->driver_id) ? $latestOrder->first_name . $this->lang->line('order_msg2') : $this->lang->line('order_msg3'); ?></p>
															<?php } ?>
															<?php if (!empty($latestOrder->driver_temperature)) { ?>
																<p><?php echo $this->lang->line('driver_temperature') ?>  : <?php echo $latestOrder->driver_temperature; ?> </p>
															<?php }
														} ?>

														<div class="detail-list">
															<i class="iicon-icon-34"></i>
															<label><?php echo $this->lang->line('delivery_address') ?></label>
															<p><?php echo $latestOrder->user_address; ?> </p>
														</div>
														<?php /* ?><div class="detail-list">
															<i class="iicon-icon-33"></i>
															<label><?php echo $this->lang->line('cash_to_collect') ?></label>
															<p><?php echo currency_symboldisplay($latestOrder->total_rate,$latestOrder->currency_symbol); ?></p>
														</div><?php */ ?>
													</div>
													<div class="details-id-img">
														<?php $image = (file_exists(FCPATH.'uploads/'.$latestOrder->image) && $latestOrder->image!='') ? image_url.$latestOrder->image : default_img;?>
														<img src="<?php echo $image; ?>">
													</div>
												</div>
												<?php if($latestOrder->delivery_method != 'relay'){  ?>
												<?php if(!empty($thirdparty_driver_details) && !empty($thirdparty_driver_details['dasher_phone_number_for_customer']) && !empty($thirdparty_driver_details['first_name'])){ ?>
													<div class="call-btn">
														<?php $mobile_number = $thirdparty_driver_details['dasher_phone_number_for_customer']; ?>
														<button class="btn"><i class="iicon-icon-12"></i><?php echo $this->lang->line('call') .' '. ($thirdparty_driver_details['first_name']?$thirdparty_driver_details['first_name']:"").' '; ?><br><?php echo $mobile_number; ?></button>
													</div>
												<?php }else if($latestOrder->driver_id) {?>
													<div class="call-btn">
														<?php $mobile_number = !empty($latestOrder->phone_code)?('+'.$latestOrder->phone_code.$latestOrder->mobile_number):($latestOrder->mobile_number); ?>
														<button class="btn"><i class="iicon-icon-12"></i><?php echo $this->lang->line('call') .' '. $latestOrder->first_name.' '; ?><br><?php echo $mobile_number; ?></button>
													</div>
												<?php } } ?>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
								<?php if($this->session->userdata('UserType') == 'Agent') { ?>
				                <div class="order-summary-content">
				                	<div class="order-id-details">
										<div class="order-id">
											<strong><?php echo $this->lang->line('customer_details') ?></strong>
										</div>
										<div class="details-id">
						                    <table>
						                        <tbody>
					                            <tr>
					                                <td><?php echo $this->lang->line('customer') ?></td>
					                                <td><strong><?php echo ($latestOrder->user_name)?> </strong></td>
					                            </tr>
					                            <tr>
					                                <td><?php echo $this->lang->line('phone_number') ?></td>
					                                <td><strong>+<?php echo ($latestOrder->user_mobile_number)?> </strong></td>
					                            </tr>
					                            <?php if(!empty($latestOrder->user_email)){ ?>
					                            <tr>
				                                    <td><?php echo $this->lang->line('email') ?></td>
				                                    <td><strong><?php echo ($latestOrder->user_email)?> </strong></td>
			                                	</tr>
	                                			<?php } ?>
				                        		</tbody>
				                    		</table>
				                		</div>
				            		</div>
				            	</div>
				            	<?php } ?>
								<div class="item_details_div">
									<?php if (!empty($latestOrder->items)) { ?>
										<div class="order-id-details">
											<div class="order-id">
												<strong><?php echo $this->lang->line('item_details') ?></strong>
											</div>
											<div class="details-id">
				                                <table class="table table-striped table-bordered table-hover">
				                                    <thead>
				                                        <tr role="row" class="heading">
				                                            <th><?php echo $this->lang->line('s_no')?></th>
				                                            <th><?php echo $this->lang->line('item_name')?></th>
				                                            <th><?php echo $this->lang->line('quantity')?></th>
				                                            <th><?php echo $this->lang->line('total_rate')?></th>
				                                        </tr>
				                                    </thead>
				                                    <tbody>
				                                        <?php foreach ($latestOrder->items as $key => $value) { ?>
				                                            <tr role="row" class="heading">
				                                                <td align="center" ><?php echo $key+1; ?></td>
				                                                <td><?php echo $value['name']; ?>
				                                                    <ul class="ul-disc">
					                                                    <?php if (!empty($value['addons_category_list'])) {
					                                                        foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
					                                                            <?php /* ?><li><h6><?php echo $cat_value['addons_category']; ?></h6></li><?php */ ?>
					                                                            <ul class="ul-cir">
						                                                            <?php if (!empty($cat_value['addons_list'])) {
						                                                                foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
						                                                                    <li><?php echo $add_value['add_ons_name']; ?>  <?php echo currency_symboldisplay(number_format((float)$add_value['add_ons_price'],2,'.',''),$latestOrder->currency_symbol); ?></li>
						                                                                <?php }
						                                                            } ?>
					                                                            </ul>
					                                                        <?php }
					                                                    } ?>
				                                                    </ul>
				                                                    <?php if(!empty($value['comment'])) { ?>
				                                                        <div><b><?php echo $this->lang->line('item_comment')?>:</b> <?php echo $value['comment']; ?></div>
				                                                    <?php } ?>
				                                                </td>
				                                                <td align="center" ><?php echo $value['quantity']; ?></td>
				                                                <td align="center" ><?php echo currency_symboldisplay(number_format((float)$value['itemTotal'],2,'.',''),$currency->currency_symbol); ?></td>
				                                            </tr>
				                                        <?php } ?>
														<tr>
															<th colspan="3"><?php echo $this->lang->line('sub_total')?></th>
															<td><?php echo currency_symboldisplay(number_format((float)$latestOrder->subtotal,2,'.',''),$currency->currency_symbol); ?></td>
														</tr>
				                                    </tbody>
				                                </table>
					                        </div>
					                    </div>
			                        <?php } ?>
								</div>
							</div>
							<div class="order-status-main">
								<div class="order-status-title">
									<h4><?php echo $this->lang->line('order_status') ?></h4>
								</div>
								<div class="order-status-box">
									<div class="status-step-box">
										<?php $active = ($latestOrder->placed) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="placed") ? "current_order_status" : "";?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/order-placed.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('order_placed') ?></label>
												<p><?php echo ($latestOrder->placed) ? date("d M Y G:i A", strtotime($latestOrder->placed)) : ''; ?></p>
											</div>
										</div>
										<?php $active = ($latestOrder->accepted_by_restaurant || $latestOrder->order_status=="accepted" || $latestOrder->accept_order_time) ? "active" : "";?>
										<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="accepted") ? "current_order_status" : "";?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/accepted-by-restaurant.png">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('order_accepted_status') ?></label>
												<p><?php echo (isset($latestOrder) && !empty($latestOrder->accepted_by_restaurant)) ? date("d M Y G:i A", strtotime($latestOrder->accepted_by_restaurant)) : ((isset($latestOrder) && !empty($latestOrder->accept_order_time)) ? date("d M Y G:i A", strtotime($latestOrder->accept_order_time)) : '') ; ?></p>
											</div>
										</div>
										<?php /* $active = ($latestOrder->preparing) ? "active" : "";?>
										<!-- <div class="status-step <?php echo $active; ?>">
											<div class="status-step-img">
												<div class="step-img">
													<img src="<?php echo base_url(); ?>assets/front/images/preparing.svg">
												</div>
											</div>
											<div class="status-step-name">
												<label><?php echo $this->lang->line('preparing') ?></label>
												<p><?php echo ($latestOrder->preparing) ? date("d M Y G:i A", strtotime($latestOrder->preparing)) : ''; ?></p>
											</div>
										</div> --><?php */ ?>
										<?php if($latestOrder->order_delivery == 'Delivery') { 
											$active = ($latestOrder->onGoing) ? "active" : "";?>
											<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="onGoing" || $latestOrder->order_status=="ready") ? "current_order_status" : "";?>">
												<div class="status-step-img">
													<div class="step-img">
														<img src="<?php echo base_url(); ?>assets/front/images/on-the-way.svg">
													</div>
												</div>
												<div class="status-step-name">
													<label><?php echo $this->lang->line('on_the_way') ?></label>
													<p><?php echo ($latestOrder->onGoing) ? date("d M Y G:i A", strtotime($latestOrder->onGoing)) : ''; ?></p>
												</div>
											</div>
											<?php $active = ($latestOrder->delivered) ? "active" : "";?>
											<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="delivered" || $latestOrder->order_status=="complete") ? "current_order_status" : "";?>">
												<div class="status-step-img">
													<div class="step-img">
														<img src="<?php echo base_url(); ?>assets/front/images/order-delivered.svg">
													</div>
												</div>
												<div class="status-step-name">
													<label><?php echo $this->lang->line('order_delivered_status') ?></label>
													<p><?php echo ($latestOrder->delivered) ? date("d M Y G:i A", strtotime($latestOrder->delivered)) : ''; ?></p>
												</div>
											</div>
										<?php } else {
											$active = ($latestOrder->order_ready) ? "active" : ""; ?>
											<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="onGoing" || $latestOrder->order_status=="ready") ? "current_order_status" : "";?>">
												<div class="status-step-img">
													<div class="step-img">
														<img src="<?php echo base_url(); ?>assets/front/images/on-the-way.svg">
													</div>
												</div>
												<div class="status-step-name">
													<label><?php echo $this->lang->line('order_ready') ?></label>
													<p><?php echo ($latestOrder->order_ready) ? date("d M Y G:i A", strtotime($latestOrder->order_ready)) : ''; ?></p>
												</div>
											</div>
											<?php $active = ($latestOrder->completed) ? "active" : "";?>
											<div class="status-step <?php echo $active; ?> <?php echo ($latestOrder->order_status=="delivered" || $latestOrder->order_status=="complete") ? "current_order_status" : "";?>">
												<div class="status-step-img">
													<div class="step-img">
														<img src="<?php echo base_url(); ?>assets/front/images/order-delivered.svg">
													</div>
												</div>
												<div class="status-step-name">
													<label><?php echo $this->lang->line('order_completed_status') ?></label>
													<p><?php echo ($latestOrder->completed) ? date("d M Y G:i A", strtotime($latestOrder->completed)) : ''; ?></p>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } else if ((($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID'))) || $is_guest_track_order =='1') && empty($latestOrder)) {?>
				<div class="col-lg-12">
					<div class="track-order-text">
						<div class="track-order-head">
							<h2><?php echo $this->lang->line('hey_there') ?></h2>
							<p><?php echo $this->lang->line('no_latest_order') ?></p>
						</div>
					</div>
				</div>
			<?php } else {?>
				<div class="col-lg-12">
					<div class="track-order-text">
						<div class="track-order-head">
							<h2><?php echo $this->lang->line('hey_there') ?></h2>
							<p><?php echo $this->lang->line('login_to_track') ?></p>
						</div>
					</div>
				</div>
			<?php }?>
		</div>
	</div>
</section>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
<?php if(($latestOrder->delivery_method == 'doordash' && $latestOrder->delivery_tracking_url) || $latestOrder->delivery_method == 'relay' || $latestOrder->order_delivery == 'PickUp'){ ?>
	ajax_call_for_track_order(1);
<?php } else { ?>
    initMap();
    var errorindirection = 0;
    function initMap(){
        map = new google.maps.Map(document.getElementById('map_canvas'),
        {
            center: {
              lat: 20.055,
              lng: 20.968
            },
            zoom: 2
        });
		var directionsService = new google.maps.DirectionsService;
        var infowindow = new google.maps.InfoWindow();
        //var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsDisplay = new google.maps.DirectionsRenderer({
		    polylineOptions: {
		      strokeColor: "#17161a"
		    }
		  });
        directionsDisplay.setOptions( { suppressMarkers: true } );
        directionsDisplay.setMap(map);
        var bounds = new google.maps.LatLngBounds();
        var waypoints = Array();
        <?php if (!empty($latestOrder->user_latitude) && !empty($latestOrder->user_longitude)): ?>
	        //users location
	        var position = {lat: <?php echo $latestOrder->user_latitude; ?>,lng: <?php echo $latestOrder->user_longitude; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/user-home.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					<?php $user_content = $latestOrder->user_first_name . "<br>" . $latestOrder->user_address; ?>
					infowindow.setContent("<?php echo addslashes($user_content); ?>");
					infowindow.open(map, marker);
				}
			})(marker));
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>
        <?php if (!empty($latestOrder->resLat) && !empty($latestOrder->resLong)): ?>
	        // restaurant location
	        var position = {lat: <?php echo $latestOrder->resLat; ?>,lng: <?php echo $latestOrder->resLong; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/restaurant.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					<?php $res_content = $latestOrder->name . "<br>" . $latestOrder->address; ?>
					infowindow.setContent("<?php echo addslashes($res_content); ?>");
					infowindow.open(map, marker);
				}
			})(marker));
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>
        <?php if (!empty($latestOrder->latitude) && !empty($latestOrder->longitude)): ?>
	        // driver location
	        var position = {lat: <?php echo $latestOrder->latitude; ?>,lng: <?php echo $latestOrder->longitude; ?>};
	        var icon = '<?php echo base_url(); ?>'+'assets/front/images/driver.png';
	        marker = new google.maps.Marker({
	            position: position,
	            map: map,
				animation: google.maps.Animation.DROP,
				icon: icon
	        });
	        bounds.extend(marker.position);
	        waypoints.push({
	            location: marker.position,
	            stopover: true
	        });
        <?php endif ?>
        map.fitBounds(bounds);
        var locationCount = waypoints.length;
        if(locationCount > 0) {
            var start = waypoints[0].location;
            var end = waypoints[locationCount-1].location;
	        directionsService.route({
				origin: start,
				destination: end,
				waypoints: waypoints,
				optimizeWaypoints: true,
				travelMode: google.maps.TravelMode.DRIVING
			}, function(response, status) {
				if (status === 'OK') {
					directionsDisplay.setDirections(response);
					ajax_call_for_track_order(errorindirection);
				} else {
					errorindirection = 1;
					ajax_call_for_track_order(errorindirection);
					//window.alert('Problem in showing direction due to ' + response);
					if(errorindirection==1){
						var box = bootbox.alert({
				            message: "<?php echo $this->lang->line('unable_to_show_direction'); ?>",
				            buttons: {
				                ok: {
				                    label: "<?php echo $this->lang->line('ok'); ?>",
				                }
				            }
				        });
				        setTimeout(function() {
						    box.modal('hide');
						}, 10000);
					}
				}
			});
        }
    }
<?php }  ?>
    function ajax_call_for_track_order(errorindirection){
    	var request_url = "<?php echo ($is_guest_track_order =='1')?base_url().'order/ajax_guest_track_order':base_url().'order/ajax_track_order'; ?>";
    	//if(errorindirection){
    		var i = setInterval(function(){
		    	var order_id = '<?php echo $order_id; ?>';
				jQuery.ajax({
					type : "POST",
					dataType : "html",
					async: false,
					url : request_url,
					data : {"order_id":order_id},
					success: function(response) {
						$('#track_order_content').html(response);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {           
					}
				});
		    },10000);
    	//}
    }
});

/*setTimeout(function() {
  location.reload();
}, 10000);*/
</script>

<?php $this->load->view('footer');?>

