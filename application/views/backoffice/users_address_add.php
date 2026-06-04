<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('entity_id','user_entity_id','address','address_label','landmark','latitude','longitude','zipcode','country','state','city','saved_status');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($edit_records->$key);
  }
}
if(isset($edit_records) && $edit_records !="")
{
    $add_label    = $this->lang->line('title_admin_userAddressEdit');        
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_address/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
}
else
{
    $add_label    = $this->lang->line('title_admin_userAddressAdd');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add_address";
}
?>
<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('customer') ?> <?php echo $this->lang->line('address') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('customer') ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label;?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add<?php echo $this->ad_prefix ?>" name="form_add<?php echo $this->ad_prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" class="frame-load display-no" style= "display: none;">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading"/>
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('customer')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" value="<?php echo $entity_id;?>" />
                                            <select name="user_entity_id" class="form-control sumo" id="user_entity_id" <?php if(isset($edit_records) && $edit_records !="") echo "disabled"; ?>>
                                                <option value=""><?php echo $this->lang->line('select')?></option> 
                                                <?php if(!empty($user_data)){
                                                    foreach ($user_data as $key => $value) { ?>
                                                        <option value="<?php echo $value->entity_id ?>" <?php echo ($value->entity_id == $user_entity_id)?'selected':'' ?>><?php echo $value->first_name.' '.$value->last_name ?></option>
                                                <?php } } ?> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="address" id="address" value="<?php echo $address ?>" maxlength="255"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('landmark_txt')?></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="landmark" id="landmark" value="<?php echo $landmark ?>" maxlength="255"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('postal_code')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control"  name="zipcode" id="zipcode" value="<?php echo $zipcode;?>" minlength="5" maxlength="6" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('latitude')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude ?>" maxlength="50"/>
                                        <input type="hidden"  name="default_latitude" id="default_latitude" value="" />
                                        </div>
                                        <div class="col-md-offset-3">
                                            <a href="#basic" data-toggle="modal" class="btn red default"><?php echo $this->lang->line('pick_lat_long')?> </a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('longitude')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude ?>" maxlength="50"/>
                                        <input type="hidden"  name="default_longitude" id="default_longitude" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city ?>" maxlength="50"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('state')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                             <input type="text" class="form-control"  name="state" id="state" value="<?php echo $state ?>" maxlength="50"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('country')?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control"  name="country" id="country" value="<?php echo $country ?>" maxlength="50"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city_txt')?></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control"  name="address_label" id="address_label" value="<?php echo $address_label ?>" maxlength="50"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('is_saved')?></label>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="saved_status" id="saved_status" value="1" <?php echo ($saved_status == 1)?'checked':'' ?>>
                                        </div>
                                    </div>
                                </div>   
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn"><?php echo $this->lang->line('submit')?></button>
                                        <a class="btn btn-danger default-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/view/user_address"><?php echo $this->lang->line('cancel')?></a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- <div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php //echo $this->lang->line('lat_long_msg') ?></h4>
            </div>
            <div class="modal-body">                                               
                <form class="form-inline margin-bottom-10" action="#">
                    <div class="input-group">
                        <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="<?php //echo $this->lang->line('address') ?>">
                        <span class="input-group-btn">
                            <button class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div id="gmap_geocoding" class="gmaps">
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal"><?php //echo $this->lang->line('close') ?></button>            
            </div>
        </div>
    </div>
</div> -->
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo $this->lang->line('lat_long_msg') ?></h4>
            </div>
            <div class="modal-body">                                               
                <form class="form-inline margin-bottom-10" action="#">
                    <div class="input-group">
                        <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="<?php echo $this->lang->line('address') ?>">
                        <span class="input-group-btn">
                            <button class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div id="gmap_geocoding" class="gmaps">
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal"><?php echo $this->lang->line('close') ?></button>            
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="mansi_map"></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url();?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>&libraries=places" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/address-autofill.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {       
        Layout.init(); // init current layout
        $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..." });
    });
    $("#basic").on("shown.bs.modal", function () {    
        if (navigator.geolocation){    
            // init geocoding Maps - calling success and fail function - mapGeocoding
            navigator.geolocation.getCurrentPosition(mapGeocoding,mapGeocoding);
        }
    });

    //New code add to find map base on default country lat/long :: Start
    var address = '<?php echo  country;?>';
    if (address !== "undefined" && address !== null ) { 
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            default_latitude = results[0].geometry.location.lat();
            default_longitude = results[0].geometry.location.lng();  
            $("#default_latitude").val(default_latitude);
            $("#default_longitude").val(default_longitude);
            } 
        }); 
    }
    //New code add to find map base on default country lat/long :: End

    var mapGeocoding = function (position) {
        // when no permission of location
        var default_latitude = 0;
        var default_longitude = 0; 
        if ( typeof(position.coords) !== "undefined" && position.coords !== null ) {
            default_latitude = position.coords.latitude;   
            default_longitude = position.coords.longitude;
        }
        else
        {
           var default_latitude = $("#default_latitude").val();   
           var default_longitude =  $("#default_longitude").val();
        }
        <?php if(isset($edit_records)){ ?>
        var map = new GMaps({
            div: '#gmap_geocoding',
            lat: <?php echo ($latitude) ? $latitude : default_latitude;?>,
            lng: <?php echo ($longitude) ? $longitude : default_longitude;?>,
            click: function (e) {           
               placeMarker(e.latLng);
            }       
        });
        map.addMarker({
            lat: <?php echo ($latitude) ? $latitude : default_latitude;?>,
            lng: <?php echo ($longitude) ? $longitude : default_longitude;?>,
            title: '',
            draggable: true,
            dragend: function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            }
        }); 
        <?php }else{ ?> 
            var map = new GMaps({
                div: '#gmap_geocoding',
                lat: default_latitude,
                lng: default_longitude,
                click: function (e) {           
                   placeMarker(e.latLng);
                }       
            });
            map.addMarker({
            lat: default_latitude,
            lng: default_longitude,
            title: '',
            draggable: true,
            dragend: function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            }
        });
        <?php } ?> 
        function placeMarker(location) {                       
            map.removeMarkers();
            $("#latitude").val(location.lat());
            $("#longitude").val(location.lng());
            map.addMarker({
                lat: location.lat(),
                lng: location.lng(),
                draggable: true,
                dragend: function(event) {
                    $("#latitude").val(event.latLng.lat());
                    $("#longitude").val(event.latLng.lng());
                }    
            })
        }
        var handleAction = function () {
            var text = $.trim($('#gmap_geocoding_address').val());
            GMaps.geocode({
                address: text,
                callback: function (results, status) {
                    if (status == 'OK') { 
                        map.removeMarkers();                   
                        var latlng = results[0].geometry.location;                    
                        map.setCenter(latlng.lat(), latlng.lng());
                        map.addMarker({
                            lat: latlng.lat(),
                            lng: latlng.lng(),         
                            draggable: true,
                            dragend: function(event) {
                                $("#latitude").val(event.latLng.lat());
                                $("#longitude").val(event.latLng.lng());
                            }
                        });
                        $("#latitude").val(latlng.lat());
                        $("#longitude").val(latlng.lng());
                    }
                }
            });
        }
        $('#gmap_geocoding_btn').click(function (e) {
            e.preventDefault();
            handleAction();
        });
        $("#gmap_geocoding_address").keypress(function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == '13') {
                e.preventDefault();
                handleAction();
            }
        });
    }
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>