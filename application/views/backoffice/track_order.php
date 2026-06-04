<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
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
                        <?php echo $this->lang->line('track_order') ?> 
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('order') ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('track_order') ?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('track_order') ?> </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container" id="track_order_content">
                            	<?php if (!empty($this->session->userdata('AdminUserID')) && !empty($latestOrder)) {?>
									<div class="track-order-main">
										<div class="track-order-map">
											<div class="row">
								                <div class="col-md-12 modal_body_map">
								                    <div class="location-map" id="location-map">
								                        <div id="map_canvas"></div>
								                    </div>
								                </div>
								            </div>
										</div>
									</div>
								<?php } else if (!empty($this->session->userdata('AdminUserID')) && empty($latestOrder)) {?>
									<h2><?php echo $this->lang->line("hey_there"); ?></h2>
									<p><?php echo $this->lang->line("no_latest_order"); ?></p>
								<?php } else {?>
									<h2><?php echo $this->lang->line("hey_there"); ?></h2>
									<p><?php echo $this->lang->line("login_to_track"); ?></p>
								<?php }?>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>
	</div>
    <!-- END CONTENT -->
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    initMap();
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
					infowindow.setContent('<?php echo addslashes($user_content); ?>');
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
					infowindow.setContent('<?php echo addslashes($res_content); ?>');
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
			} else {
				window.alert('Problem in showing direction due to ' + status);
			}
			});
        }
    }
    var i = setInterval(function(){
    	var order_id = '<?php echo $order_id; ?>';
		jQuery.ajax({
			type : "POST",
			dataType : "html",
			async: false,
			url : BASEURL+'backoffice/order/ajax_track_order',
			data : {"order_id":order_id},
			success: function(response) {
				$('#track_order_content').html(response);
			},
				error: function(XMLHttpRequest, textStatus, errorThrown) {           
			}
		});
    },10000);  
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>
