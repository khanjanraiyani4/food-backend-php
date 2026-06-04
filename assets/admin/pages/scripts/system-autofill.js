google.maps.event.addDomListener(window, 'load', function() {
    var places = new google.maps.places.Autocomplete(document.getElementById('admin_address'),{ types: ['address'] });
    google.maps.event.addListener(places, 'place_changed', function() {
        var place = places.getPlace();
        var  value = place.formatted_address.split(",");
        if(place.name == value[0]){
            document.getElementById("admin_address").value = place.formatted_address;    
        }else{
            document.getElementById("admin_address").value = place.name+', '+place.formatted_address;
        }
        document.getElementById("admin_latitude").value = '';
        document.getElementById("admin_longitude").value = '';
        $.each(place.address_components, function( index, value ) {
            $.each(value.types, function( index, types ) {                
            });
        });
        document.getElementById("admin_latitude").value = place.geometry.location.lat();
        document.getElementById("admin_longitude").value = place.geometry.location.lng();
    });
});