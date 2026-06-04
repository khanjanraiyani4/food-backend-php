 var input = document.getElementById('address');
  google.maps.event.addDomListener(input, 'keydown', function(event) { 
    if (event.keyCode === 13) { 
        event.preventDefault(); 
    }
  });
google.maps.event.addDomListener(window, 'load', function() {
    //var places = new google.maps.places.Autocomplete(document.getElementById('address'),{ types: ['address'] });
    var places;
    const inputaddressval = document.getElementById("address");
    const optionsObj = {
      // components: "country:us",
      // language: lang_slug,
      // location: defaultlat_val+','+defaultlong_val,
      // radius:1000,
      //componentRestrictions: { country: ["us","in","pk"]},
      fields: ["formatted_address","address_components", "geometry", "icon", "name"],
      //types: ['(regions)'], //'address','geocode','establishment','postal_code','cities'
      //bounds: defaultBoundsObj,
      //locationBias: {radius: 900, center: {lat: defaultlat_val, lng: defaultlong_val}},
      //strictBounds: true,
    };
    places = new google.maps.places.Autocomplete(inputaddressval, optionsObj);

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
          center: geolocation, radius: 1000
        });
        places.setBounds(circle.getBounds());
      });
    }
    google.maps.event.addListener(places, 'place_changed', function() {
        var place = places.getPlace();
        var  value = place.formatted_address.split(",");
        if(place.name == value[0]){
            document.getElementById("address").value = place.formatted_address;    
        }else{
            document.getElementById("address").value = place.name+', '+place.formatted_address;
        }
        document.getElementById("city").value = '';
        document.getElementById("state").value = '';
        document.getElementById("country").value = '';
        var zipcodeElement = document.getElementById("zipcode");
        if(zipcodeElement){
            zipcodeElement.value = '';
        }
        document.getElementById("latitude").value = '';
        document.getElementById("longitude").value = '';
        $.each(place.address_components, function( index, value ) {
            $.each(value.types, function( index, types ) {
                if(types == 'locality'){
                   document.getElementById("city").value = value.long_name;
                }
                if(types == 'administrative_area_level_1'){
                   document.getElementById("state").value = value.long_name;
                }
                if(types == 'country'){
                   document.getElementById("country").value = value.long_name;
                }
                if(types == 'postal_code'){
                    if(zipcodeElement){
                        zipcodeElement.value = value.long_name;
                    }
                }
            });
        });
        document.getElementById("latitude").value = place.geometry.location.lat();
        document.getElementById("longitude").value = place.geometry.location.lng();
    });
});