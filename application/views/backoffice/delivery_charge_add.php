<?php $this->load->view(ADMIN_URL.'/header');?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');?>
<!-- END sidebar -->
<?php if($this->input->post()){
    foreach ($this->input->post() as $key => $value) {
        $$key = @htmlspecialchars($this->input->post($key));
    } 
} else {
    $FieldsArray = array('charge_id','area_name','lat_long','price_charge','restaurant_id','additional_delivery_charge','is_masterdata');
    foreach ($FieldsArray as $key) {
        $$key = @htmlspecialchars($edit_records->$key);
    }
}
if(isset($edit_records) && $edit_records !="") {
    $add_label    = $this->lang->line('edit').' '.$this->lang->line('delivery_charge');        
    $form_action      = base_url().ADMIN_URL."/".$this->controller_name."/edit/".$this->uri->segment('4').'/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->charge_id));
    $data =  explode('~', $lat_long);
    $finalArray = array(); 
    foreach ($data as $key => $value) {
        $value = explode(',', $value);
        if($value){
            $i = 1; 
            foreach ($value as $k => $val) {
                $val = str_replace(array('[',']'),array('',''),$val);
                $finalArray[] = ($i%2 != 0)?'{lat: '.$val.'':'lng: '.$val.'}';
                $i++;
            } 
        }
    }
    $finalArray = json_encode(implode(',', $finalArray));
    $finalArray = str_replace('"','',$finalArray);
    $add_labelval    = 'edit';
}
else
{
    $add_label    = $this->lang->line('add').' '.$this->lang->line('delivery_charge');        
    $form_action      = base_url().ADMIN_URL."/".$this->controller_name."/add/".$this->uri->segment('4');
    $add_labelval    = 'add';
} ?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('title_delivery_charges') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('title_delivery_charges') ?></a>
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
                            <form action="<?php echo $form_action;?>" id="form_add_<?php echo $this->prefix ?>" name="form_add_<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?'onsubmit="return false"':"";?>>
                                <div class="form-body">                                     
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors();?>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="restaurant_id" class="form-control sumo" id="restaurant_id" onchange="getCurrency(this.value); getResLatLong(this.value);">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if(!empty($restaurant)){
                                                    foreach ($restaurant as $key => $value) { ?>
                                                       <option value="<?php echo $value->content_id ?>" <?php echo ($value->content_id == $restaurant_id)?"selected":"" ?>><?php echo $value->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('area_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="charge_id" id="charge_id" value="<?php echo $charge_id;?>" />                                         
                                            <input type="text" name="area_name" id="area_name" value="<?php echo $area_name;?>" maxlength="249" data-required="1" class="form-control required"/>
                                        </div>
                                    </div>

                                    <?php //New code add to search location with fill address :: Start ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('search_area'); ?></label>
                                        <div class="col-md-4">
                                             <div class="input-group">
                                                <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="<?php echo $this->lang->line('address') ?>">
                                                <span class="input-group-btn">
                                                    <a class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div> 
                                    <?php //New code add to search location with fill address :: End ?>

                                    <div class="form-group" onload="initialize()">
                                        <div class="col-md-12">
                                            <h3><?php echo $this->lang->line('drag_map'); ?></h3>
                                            <div id="map-canvas"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('latitude'); ?>/<?php echo $this->lang->line('longitude'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <textarea name="lat_long" id="lat_long" class="form-control required" readonly=""><?php echo $lat_long;?></textarea> 
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('price'); ?> <span id="currency-symbol"></span><span class="required">*</span></label>

                                        <div class="col-md-4">

                                            <input type="text" name="price_charge" id="price_charge" value="<?php echo ($price_charge)?$price_charge:'';?>" maxlength="19" min="0" data-required="1" class="form-control required"/>

                                        </div>

                                    </div> 
                                    <!-- additional_delivery_charge :: start --> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('additional_delivery_charge'); ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="additional_delivery_charge" id="additional_delivery_charge" value="<?php echo ($additional_delivery_charge)?$additional_delivery_charge:'';?>" maxlength="19" min="0" data-required="1" class="form-control required"/>
                                        </div>
                                        <div class="custom--tooltip">
                                            <i class="fa fa-info-circle tooltip-icon" aria-hidden="true"></i>
                                            <span class="tooltiptext tooltip-right">
                                                <?php /*$this->db->select('OptionValue');
                                                $min_order_amount = $this->db->get_where('system_option',array('OptionSlug'=>'min_order_amount'))->first_row();
                                                $min_order_amount = (float) $min_order_amount->OptionValue;
                                                $min_order_txt = sprintf($this->lang->line('min_order_msg'),$min_order_amount); */
                                                echo $this->lang->line('additional_charge_info'); ?>
                                            </span>
                                        </div>
                                    </div> 
                                    <!-- additional_delivery_charge :: end -->
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" <?php echo (Disabled_HideButton($is_masterdata,'yes')=='1')?"disabled":"";?> name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn default" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                    </div>                                    
                                </div>
                            </form>
                            <!-- END FORM-->
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?php echo google_key; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script>
    var map,myPolygon;

jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    //initialize();
    if (navigator.geolocation){    
        // init geocoding Maps - calling success and fail function - mapGeocoding
        navigator.geolocation.getCurrentPosition(initialize,initialize);
    }
    //Added on 19-10-2020
   $('.sumo').SumoSelect({search: true,searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."  });
    <?php if ($restaurant_id): ?>
        var restaurant_id = '<?php echo $restaurant_id; ?>';
        getResLatLong(restaurant_id);
    <?php endif ?>

    function initialize(position) {
        // when no permission of location
        default_latitude = 0;
        default_longitude = 0;
        if ( typeof(position.coords) !== "undefined" && position.coords !== null ) {
            default_latitude = position.coords.latitude;   
            default_longitude = position.coords.longitude;
        }
        
      // Map Center
      var myLatLng = new google.maps.LatLng(default_latitude, default_longitude);
      // General Options
      var mapOptions = {
        zoom: 12,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.RoadMap
      };
      map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
      // Polygon Coordinates
      var triangleCoords = [
        <?php if(!empty($finalArray)){ 
            echo $finalArray;
        }else{ ?>
            {lat: default_latitude, lng: default_longitude},
            {lat: default_latitude, lng: default_longitude},
        <?php } ?>
      ];
      // Styling & Controls
      myPolygon = new google.maps.Polygon({
        paths: triangleCoords,
        draggable: true, // turn off if it gets annoying
        editable: true,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
      });
      myPolygon.setMap(map);
      google.maps.event.addListener(myPolygon.getPath(), "insert_at", getPolygonCoords);
      google.maps.event.addListener(myPolygon.getPath(), "set_at", getPolygonCoords);
    }
});
//Display Coordinates below map
function getPolygonCoords() {
  var len = myPolygon.getPath().getLength();
  var htmlStr = "";
  for (var i = 0; i < len; i++) {
    htmlStr += "["+myPolygon.getPath().getAt(i).toUrlValue(5)+']';
    htmlStr += (i == len-1)?'':'~';
  }
  document.getElementById('lat_long').innerHTML = htmlStr;
}
function getResLatLong(value){
    if (value) {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: BASEURL+"backoffice/delivery_charge/getResLatLong",
          data: 'restaurant_id=' + value ,
          cache: false,
          success: function(response) {
            if (response) {
                newLocation(response.latitude,response.longitude);
                myPolygon.setMap(null);
                // Polygon Coordinates
                var triangleCoords = [
                <?php if(!empty($finalArray)){ 
                    echo $finalArray;
                }else{ ?>
                    new google.maps.LatLng(response.latitude, response.longitude),
                    new google.maps.LatLng(response.latitude, response.longitude),
                <?php } ?>
                ];
                // Styling & Controls
                myPolygon = new google.maps.Polygon({
                paths: triangleCoords,
                draggable: true, // turn off if it gets annoying
                editable: true,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35
                });
                myPolygon.setMap(map);
                google.maps.event.addListener(myPolygon.getPath(), "insert_at", getPolygonCoords);
                google.maps.event.addListener(myPolygon.getPath(), "set_at", getPolygonCoords);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {                 
            alert(errorThrown);
          }
        });
    }
}
function newLocation(lat,lng)
{   
    var newLatLng = new google.maps.LatLng(lat, lng);
    map.setCenter(newLatLng);
}

//New code add to search location with fill address :: Start
var geocoder = new google.maps.Geocoder();
$('#gmap_geocoding_btn').click(function (e) {        
    var address = $.trim($('#gmap_geocoding_address').val());
    geocoder.geocode( { 'address': address}, function(results, status) {

    if (status == google.maps.GeocoderStatus.OK) {
        var latitude = results[0].geometry.location.lat();
        var longitude = results[0].geometry.location.lng();
        
        newLocation(latitude,longitude);
        myPolygon.setMap(null);
        // Polygon Coordinates
        var triangleCoords = [
        <?php if(!empty($finalArray)){ 
            echo $finalArray;
        }else{ ?>
            new google.maps.LatLng(latitude, longitude),
            new google.maps.LatLng(latitude, longitude),
        <?php } ?>
        ];
        // Styling & Controls
        myPolygon = new google.maps.Polygon({
        paths: triangleCoords,
        draggable: true, // turn off if it gets annoying
        editable: true,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
        });
        myPolygon.setMap(map);
        google.maps.event.addListener(myPolygon.getPath(), "insert_at", getPolygonCoords);
        google.maps.event.addListener(myPolygon.getPath(), "set_at", getPolygonCoords);
      } 
    });
});
$('#gmap_geocoding_address').keydown(function(e){    
    if(e.keyCode == 13)
    {
        $('#gmap_geocoding_btn').click();
        return false;
    }    
});
//New code add to search location with fill address :: End
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>