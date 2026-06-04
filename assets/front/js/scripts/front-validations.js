"use strict";
//setLanguage
function setLanguage(language_slug){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'/backoffice/lang_loader/setLanguage',
        data : {'language_slug':language_slug},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            location.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
//logout
function logout(){
  jQuery.ajax({
        type : "POST",
        url : BASEURL+'home/logout',
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            location.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
// click on notification icon
$(".notification-btn").on("click", function(e){
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'home/unreadNotifications',
    success: function(response) {
      //$('.notification_count').html(0);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
    }
  });
});
// submit forgot password form
$("#form_front_forgotpass").on("submit", function(event) { 
    event.preventDefault();

    $('#user_otp').val('');
    $('#digit-1').val('');
    $('#digit-2').val('');  
    $('#digit-3').val('');
    $('#digit-4').val('');
    $('#digit-5').val('');
    $('#digit-6').val('');    
    jQuery.ajax({
        type : "POST",
        dataType :"json",
        url : BASEURL+'home/forgot_password',
        data : {'mobile_number_first':$('#mobile_number_first').val(), 'phone_code_first':$('#phone_code_first').val(),'forgot_submit_page':$('#forgot_submit_page').val() },
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            $('#mobile_number_first').val('');
            $('#forgot_error').hide();
            $('#forgot_success').hide();
             $('#quotes-main-loader').hide();
            if (response) {
              if (response.forgot_error != '') { 
                  $('#forgot_error').html(response.forgot_error);
                  $('#forgot_success').hide();
                  $('#forgot_error').show();
              }
              if (response.forgot_success != '') { 
                  $('#forgot_success').html(response.forgot_success);
                  $('#forgot_error').hide();
                  $('#forgot_success').show();
                  $('#forgot_password_section').hide();

                  setTimeout(function(){
                    $("#forgot-pass-modal").modal('hide');
                    $('#verifyotp_modaltitle').text(response.verifyotp_modaltitle);
                    $('#enter_otp_text').text(response.enter_otp_text);
                    $('#is_forgot_pwd').val('1');
                    $("#forgot_pwd_userid").val(response.forgot_pwd_userid);
                    $("#verify-otp-modal").modal('show');
                  }, 2000);
              }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
        }
    });
});
// submit forgot password form hidden
$('#forgot-pass-modal').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=number]").val('').end();
  $('#form_front_forgotpass').validate().resetForm();
  $('#forgot_success').text('');
  $('#forgot_error').text('');
  $('#forgot_success').hide();
  $('#forgot_error').hide();
  $('#forgot_password_section').show();
  $('#email_forgot').val('');
});

// menu filter function
function menuFilter(content_id,value,food_type='no',availability='no')
{  
  var food = '';
  var price = '';
  var idArr=[];
  var idArrF=[];
  //New code add for availability :: Start
  var availabilityval = '';
  if(availability=='yes')
  {
    availabilityval = value;
  }
  else{ 
    if(availabilityval=='all')
    {
      availabilityval = '';
    }else
    {
      $('.filter_availibility:checked').each(function() {
        idArr.push($(this).val());
      });
      if(idArr!=""){
        availabilityval = idArr;  
      }     
    }
  } 
  //New code add for availability :: End 
  var searchDish = $('#search_dish').val();
  if(food_type=='yes')
  {
    food = value;
  }
  else
  {
    if ($('input[name="filter_food"]:checked').val() == "all") {
      food = "";
    } 
    else{
      $('.filter_food:checked').each(function() {
          idArrF.push($(this).val());
      });
      if(idArrF!=""){
        food = idArrF;  
      }
    }    
  }

  if ($('input[name="filter_price"]:checked').val() == "filter_high_price") {
      price = "high";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_low_price") {
    price = "low";
  }

  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/ajax_restaurant_details',
    data : {"content_id":content_id,"food":food,"availability":availabilityval,"price":price,"searchDish":$.trim(searchDish)},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#quotes-main-loader').hide();
      $('#res_detail_content').html(response);
      //$('#search_dish').val($.trim(searchDish))
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// decrease the menu quantity
function minusQuantity(restaurant_id,menu_id,cart_key){
  customItemCount(menu_id,restaurant_id,'minus',cart_key,1);
}
// increase the menu quantity
function plusQuantity(restaurant_id,menu_id,cart_key){
  customItemCount(menu_id,restaurant_id,'plus',cart_key,1);
}
// custom item count
function customItemCount(entity_id,restaurant_id,action,cart_key,recipe_page,qtyval){
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'cart/customItemCount',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'no','qtyval':qtyval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#your_cart').html(response.cart);
      if (response.added == 0) {
        if($('.addtocart-'+entity_id).attr('order-for-later') == '1') {
          $('.addtocart-'+entity_id).html(ORDER_FOR_LATER);
        } else {
          $('.addtocart-'+entity_id).html(ADD);
        }
        $('.addtocart-'+entity_id).removeClass('added');
        $('.addtocart-'+entity_id).addClass('add');
      }
      if (recipe_page=='recipe'){
            window.location.replace(BASEURL+'cart');
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}

function EditcustomItemCount(customQuantity1,entity_id,restaurant_id,cart_key){
  if(customQuantity1!='')
  {
    var customQuantity=(customQuantity1=='0')?'1':customQuantity1;
    jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'cart/customItemCount',
    data : {"customQuantity":customQuantity,"entity_id":entity_id,"restaurant_id":restaurant_id,"cart_key":cart_key,'is_main_cart':'no'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#your_cart').html(response.cart);
      if (response.added == 0) {
        $('.addtocart-'+entity_id).html(ADD);
        $('.addtocart-'+entity_id).removeClass('added');
        $('.addtocart-'+entity_id).addClass('add');

      }
      //location.reload(true);
      $('#quotes-main-loader').hide();
      $( "#QtyNumberval" ).focus();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
  }  
}
// check cart restaurant before adding menu item
function checkCartRestaurant(entity_id,restaurant_id,is_addon,item_id,check_reload='') {  
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkCartRestaurant',
    data : {"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 0) {
        // another restaurant
        $('#rest_entity_id').val(entity_id);
        $('#rest_restaurant_id').val(restaurant_id);
        $('#rest_is_addon').val(is_addon);
        $('#item_id').val(item_id);
        $('#anotherRestModal').modal('show');
      }
      if (response == 1) {
        // same restaurant
        if (is_addon == '') {
          AddToCart(entity_id,restaurant_id,item_id,check_reload,1);
        }
        else
        {
          checkMenuItem(entity_id,restaurant_id,item_id,check_reload);
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// confirm to add menu item
function ConfirmCartRestaurant(recipe_page){
  var entity_id = $('#rest_entity_id').val();
  var restaurant_id = $('#rest_restaurant_id').val();
  var is_addon = $('#rest_is_addon').val();
  var item_id = $('#item_id').val();
  var restaurant = $('input[name="addNewRestaurant"]:checked').val();
  $('#anotherRestModal').modal('hide');
  if (restaurant == "discardOld") {
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'cart/emptyCart',
      data : {"entity_id":entity_id,'restaurant_id':restaurant_id},
      success: function(response) { 
        if (is_addon == '') {
          AddToCart(entity_id,restaurant_id,item_id,recipe_page);
        }
        else
        {
          $('.addtocart-'+entity_id).click();
          checkMenuItem(entity_id,restaurant_id,item_id,recipe_page);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
      });
  }
  return false;
}
// add to cart
function AddToCart(entity_id,restaurant_id,item_id,recipe_page,qtyval){  
  var action;
  if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
    action = "remove";
  }
  else
  {
    action = "add";
  }

  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/addToCart',
    data : {"menu_item_id":entity_id,'restaurant_id':restaurant_id,'qtyval':qtyval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      if (recipe_page=='recipe'){
            window.location.replace(BASEURL+'cart');
      }
      else if (recipe_page=='checkout')
      {
          //Added to load checkout cart item
          checkoutItem_reload(entity_id,restaurant_id);

      } else if (recipe_page=='checkout_as_guest'){
          //window.location.replace(BASEURL+'checkout/checkout_as_guest');
          //Added to load checkout cart item
          checkoutItem_reload(entity_id,restaurant_id);
      }
      $('#quotes-main-loader').hide();
      $('#menuDetailModal').modal('hide');
      $('#your_cart').html(response);
      $('.'+item_id).html(ADDED);
      $('.'+item_id).removeClass('add');
      $('.'+item_id).addClass('added');
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
  return false;
}
//Code for reload the item detail on checkout page :: Start
function checkoutItem_reload(entity_id,restaurant_id)
{
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/checkoutItem_reload' ,
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"payment_optionval":payment_optionval,call_from:'add_to_cart'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();       
      $('#ajax_your_items').html(response.ajax_your_items);
      if($("input[name='choose_order']:checked").val() == 'delivery') {
        $('#driver-tip-form').html(response.ajax_driver_tips);
      }
      $('#ajax_your_suggestion').html(response.ajax_your_suggestion);
      $('#ajax_order_summary').html(response.ajax_order_summary);
      $('#subtotal').val(response.cart_total);
      if($("input[name='choose_order']:checked").val() == 'delivery') {
        if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
          if($("#add_address").val() != '' && $("#add_latitude").val() != '' && $("#add_longitude").val() != '') {
            getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'get',response.cart_total);
          }
        }
        else if($("input[name='add_new_address']:checked").val() == 'add_your_address' && $('#your_address').val() != ''){
          getAddLatLong($('#your_address').val(),response.cart_total)
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
//Code for reload the item detail on checkout page :: End

// check menu item availability
function checkMenuItem(entity_id,restaurant_id,item_id,recipe_page){
  // check the item in cart if it's already added
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkMenuItem' ,
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,'reload_page':recipe_page},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 1) {
        $('#con_entity_id').val(entity_id);
        $('#con_restaurant_id').val(restaurant_id);
        $('#con_item_id').val(item_id);
        $('#myconfirmModal').modal('show');
      }
      else
      {
        customMenu(entity_id,restaurant_id,item_id,recipe_page);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// confirm to add to cart
function ConfirmCartAdd(){
  var entity_id = $('#con_entity_id').val();
  var restaurant_id = $('#con_restaurant_id').val();
  var item_id = $('#con_item_id').val();
  var cart = $('input[name="addedToCart"]:checked').val();
  var qtyval = $('#qty'+item_id).val();
  $('#myconfirmModal').modal('hide');
  if (cart == "increaseitem") {
    customItemCount(entity_id,restaurant_id,'plus','',qtyval);
  }
  else
  {
    customMenu(entity_id,restaurant_id,item_id);
  }
  return false;
}
// custom menu page
function customMenu(entity_id,restaurant_id,item_id,recipe_page){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/getCustomAddOns',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,'reload_page':recipe_page},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#myModal').html(response);
      $('#myModal').modal('show');
      if (recipe_page=='recipe'){
            window.location.replace(BASEURL+'cart');
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}

$("#search_dish").keyup(function() {
  var restaurant_id = $('#srestaurant_id').val();
  searchMenuDishes(restaurant_id);
});

// search the users dishes
function searchMenuDishes(restaurant_id) {
  var searchDish = $('#search_dish').val();
  var food = '';
  var price = '';

  //New code add for availability :: Start
  var availabilityval = '';  
  if ($('input[name="filter_availibility"]:checked').val())
  {
    availabilityval = $('input[name="filter_availibility"]:checked').val();
  }
  if(availabilityval=='all')
  {
    availabilityval = '';
  } 
  //New code add for availability :: End

  if ($('input[name="filter_food"]:checked').val() == "filter_veg") {
    food = "veg";
  }
  if ($('input[name="filter_food"]:checked').val() == "filter_non_veg") {
    food = "non_veg";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_high_price") {
    price = "high";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_low_price") {
    price = "low";
  }
  if ($('input[name="filter_food"]:checked').val() == "all") {
    food = "";
  } 
  else{
    food = $('input[name="filter_food"]:checked').val();
  }
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'restaurant/getResturantsDish',
    data : {'restaurant_id':restaurant_id,'searchDish':$.trim(searchDish),"food":food,"price":price,"availability":availabilityval},
    beforeSend: function(){
        //$('#quotes-main-loader').show();
    },
    success: function(response) {
      //$('#details_content').html(response);
      $('#res_detail_content').html(response);
      //$('#quotes-main-loader').hide();
      //$('#search_dish').val($.trim(searchDish));
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// get address from lat long
function getAddress(latitude,longitude,page,flagedit =false){
  jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'home/getUserAddress',
    data : {'latitude':latitude,'longitude':longitude,'page':page},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      if (page == 'restaurant_details') {
        $('#delivery_address').val(response);
      }
      else if (page == 'my_profile') {
        if(!flagedit){
          //$('#address_field').val(response);
          $('#address_field').val(response.address);
          $('#city').val(response.city);
          $('#state').val(response.state);
          $('#country').val(response.country);
          $('#zipcode').val(response.zipcode);
        }
      }
      else if (page == 'checkout') {
        $('#add_latitude').val(latitude);
        $('#add_longitude').val(longitude);
        $('#add_address').val(response.address);
        $('#zipcode').val(response.zipcode);
        var cart_total=$('#subtotal').val();
        getDeliveryCharges(latitude,longitude,'get',cart_total);
      }
      else
      {
        $('#address').val(response);
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// search restaurant menu
function menuSearch(category_id){
  if ($('#checkbox-option-'+category_id+'').is(':checked')) {
    $('.check-menu').prop("checked", false);
    $('#checkbox-option-'+category_id+'').prop("checked", true);
    if ( $(window).width() < 1199) { //Set here window width accourding to your need

      jQuery('html, body').animate({
        scrollTop: $('#category-'+category_id+'').offset().top - 120
      }, 500);
      
    }
    else{
      jQuery('html, body').animate({
        scrollTop: $('#category-'+category_id+'').offset().top - 150
        }, 500);
    }
  }
}
function scrollToPopularItems(){
  if ($('#checkbox-option-0').is(':checked')) {
    $('.check-menu').prop("checked", false);
    $('#checkbox-option-0').prop("checked", true);
    $('html, body').animate({
          scrollTop: $('#popular_menu_item').offset().top - 150
      }, 2000);
  }
}
// autocomplete function
var autocomplete;
function initAutocomplete(id) {
    const optionsObj = {
      //componentRestrictions: { country: ["us","in","pk"]},
      fields: ["formatted_address","address_components", "geometry", "icon", "name"],
    };
    autocomplete = new google.maps.places.Autocomplete(document.getElementById(id), optionsObj);
    //autocomplete = new google.maps.places.Autocomplete(document.getElementById(id), { types: ['address'] },{ types: ['formatted_address'] });
    //autocomplete.setFields(['address_component']);
    if(current_pagejs != 'MyProfile'){
      autocomplete.addListener('place_changed', setAddress);  
    }    
}
// place changed after auto complete
function setAddress(){
    var place = autocomplete.getPlace();
    var geocoder = new google.maps.Geocoder();
    if(place){
      var address = place.formatted_address;  
    }
    
    var cart_total = $('#subtotal').val();
    geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      //get delivery charges
      if(current_pagejs == 'OrderFood') {
        $('#distance_filter').show();
        $('#distance_sort').show();
        $('#latitude').val(results[0].geometry.location.lat());
        $('#longitude').val(results[0].geometry.location.lng());
         getFavouriteResturants('');
      } else if(current_pagejs == 'HomePage') {
        $('#latitude').val(results[0].geometry.location.lat());
        $('#longitude').val(results[0].geometry.location.lng());
        addLatLong(results[0].geometry.location.lat(),results[0].geometry.location.lng(),address);
        setTimeout(function() {
          window.location.href = BASEURL+"restaurant";
          $('#quotes-main-loader').hide();
        }, 1000);
        //getPopularResturants(results[0].geometry.location.lat(),results[0].geometry.location.lng(),'scroll');
      } 
      else
      {
        var zipcode = '';
        $.each(results[0].address_components, function( index, value ) {
          $.each(value.types, function( index, types ) {
            if(current_pagejs == 'MyProfile' && types == 'administrative_area_level_2'){
               document.getElementById("city").value = value.long_name;
            }
            if(current_pagejs == 'MyProfile' && types == 'administrative_area_level_1'){
               document.getElementById("state").value = value.long_name;
            }
            if(current_pagejs == 'MyProfile' && types == 'country'){
               document.getElementById("country").value = value.long_name;
            }
            if(types == 'postal_code'){
               zipcode = value.long_name;
               document.getElementById("zipcode").value = value.long_name;
            }
          });
        });
        if(zipcode==''){
          document.getElementById("zipcode").value = zipcode;
        }
        if(current_pagejs != 'MyProfile'){ //added this else if so it won't go to else code block, no need to call delivery charges, 
        //redeempoints and get coupon in myprofile
          getDeliveryCharges(results[0].geometry.location.lat(),results[0].geometry.location.lng(),"get",cart_total);
          $('#add_latitude').val(results[0].geometry.location.lat());
          $('#add_longitude').val(results[0].geometry.location.lng());
        }
      }      
    } 
    });    
}

//get restaurant location function 
function geolocate(page) {
  //initAutocomplete('address_field');
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
    var geolocation = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };
    var circle = new google.maps.Circle({
      center: geolocation, radius: 1000
    });
    autocomplete.setBounds(circle.getBounds());
    if (page == "order_food" || current_pagejs == 'OrderFood') {
      $('#latitude').val(position.coords.latitude); 
      $('#longitude').val(position.coords.longitude); 
    } else if (page == "home_page" || current_pagejs == 'HomePage'){
      $('#latitude').val(position.coords.latitude); 
      $('#longitude').val(position.coords.longitude);
    }
    });
  }
}
// get location for every page from id
function getLocation(page,call_from='') {
  if (navigator.geolocation) {
    if (page == 'restaurant_details') {
      navigator.geolocation.getCurrentPosition(showPosition,locationFail);
    }
    else if (page == 'home_page') {
      navigator.geolocation.getCurrentPosition(function(position) {
            showPositionHome(position,call_from);
        },function(err) {
            locationFailHome(call_from);
        });
    }
    else if (page == 'order_food') {
      navigator.geolocation.getCurrentPosition(showPositionFood,locationFailFood);
    }
    else if (page == 'my_profile') {
      navigator.geolocation.getCurrentPosition(showPositionProfile,locationFailProfile);
    }
    else if (page == 'checkout') {
      navigator.geolocation.getCurrentPosition(showPositionCheckout,locationFailCheckout);
    }
  }
}
function getSearchedLocation(searched_lat,searched_long,searched_address,page,call_from=''){
  if (page == "home_page") { 
    $('#quotes-main-loader').show();
    $('#distance_filter').show();
    $('#distance_sort').show();
    $('#latitude').val(searched_lat); 
    $('#longitude').val(searched_long);
    $('#address').val(searched_address);
    getAddress(searched_lat,searched_long,'');
    addLatLong(searched_lat,searched_long,$('#address').val());
    if(call_from == ''){
      setTimeout(function() {
        window.location.href = BASEURL+"restaurant";
        $('#quotes-main-loader').hide();
      }, 1000);
    } else {
      getPopularResturants(searched_lat,searched_long,'');
      $('#quotes-main-loader').hide();
    }
  }
  else if (page == "order_food") { 
    $('#distance_filter').show();
    $('#distance_sort').show();
    $('#latitude').val(searched_lat); 
    $('#longitude').val(searched_long);  
    $('#address').val(searched_address);
    getAddress(searched_lat,searched_long,'');
    getFavouriteResturants('');
  }
  else if (page == "my_profile") {
    setMarker(searched_lat,searched_long);
  }
  else if (page == "checkout") { 
    $('#add_latitude').val(searched_lat); 
    $('#add_longitude').val(searched_long);  
    $('#add_address').val(searched_address);
    getAddress(searched_lat,searched_long,'checkout');
  }
}
// restaurant details functions
function showPosition(position) {
  getAddress(position.coords.latitude,position.coords.longitude,'restaurant_details');
}
function locationFail() {
  //getAddress(23.0751887,72.52568870000005,'restaurant_details');
}
// home page functions
function showPositionHome(position,call_from='') { 
  $('#quotes-main-loader').show();
  $('#latitude').val(position.coords.latitude); 
  $('#longitude').val(position.coords.longitude); 
  $('#distance_filter').show();
  $('#distance_sort').show();
  getAddress(position.coords.latitude,position.coords.longitude,'');
  addLatLong(position.coords.latitude,position.coords.longitude,$('#address').val());
  if(call_from == '') {
    setTimeout(function() {
      window.location.href = BASEURL+"restaurant";
      $('#quotes-main-loader').hide();
    }, 1000);
  } else {
    getPopularResturants(position.coords.latitude,position.coords.longitude,'');
    $('#quotes-main-loader').hide();
  }
}
function locationFailHome(call_from='') {
  //getAddress(23.0751887,72.52568870000005,'');
  $('#distance_filter').hide();
  $('#distance_sort').hide();
  $('#latitude').val(''); 
  $('#longitude').val('');
  if(call_from == '') {
    var box = bootbox.alert({
      message: ADDRESS_ERR,
      buttons: {
          ok: {
              label: OK_TEXT,
          }
      }
    });
    setTimeout(function() {
      box.modal('hide');
    }, 10000);
  } else {
    getPopularResturants('','','');
  }
}
// js location function for order Food page
function showPositionFood(position) {
  $('#latitude').val(position.coords.latitude); 
  $('#longitude').val(position.coords.longitude); 
  getAddress(position.coords.latitude,position.coords.longitude,'');
    getFavouriteResturants('');
  $('#distance_filter').show();
  $('#distance_sort').show();
}
function locationFailFood() {
  //$('#latitude').val(23.0751887); 
  //$('#longitude').val(72.52568870000005); 
  //getAddress(23.0751887,72.52568870000005,'');
  var box = bootbox.alert({
    message: ADDRESS_ERR,
    buttons: {
        ok: {
            label: OK_TEXT,
        }
    },
    callback: function () {
      getFavouriteResturants('');
    }
  });
  setTimeout(function() {
    box.modal('hide');
    getFavouriteResturants('');
  }, 10000);
  $('#distance_filter').hide();
  $('#distance_sort').hide();
}
// my profile 
function showPositionProfile(position) {
    setMarker(position.coords.latitude,position.coords.longitude);    
}
function locationFailProfile() {
  //find lat-long based on default country
  var address = default_country_fromheader;
  var default_latitude = 0;
  var default_longitude = 0;
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
  setMarker(default_latitude,default_longitude);
}
// home page js functions
function fillInAddress(page,err_msg,oktext) {  
  // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    var geocoder = new google.maps.Geocoder();
    var address = document.getElementById("address").value;
    var order_mode = $('#order_mode').val();
    geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      addLatLong(results[0].geometry.location.lat(),results[0].geometry.location.lng(),address);
      if (page == "home_page") {
        $('#quotes-main-loader').show();
        $('#distance_filter').show();
        $('#distance_sort').show();
        $('#latitude').val(results[0].geometry.location.lat()); 
        $('#longitude').val(results[0].geometry.location.lng()); 
        //getPopularResturants(results[0].geometry.location.lat(),results[0].geometry.location.lng(),'scroll');
        setTimeout(function() {
          window.location.href = BASEURL+"restaurant";
          $('#quotes-main-loader').hide();
        }, 5000);
      }
      else if (page == "order_food") {
        $('#distance_filter').show();
        $('#distance_sort').show();
        $('#latitude').val(results[0].geometry.location.lat()); 
        $('#longitude').val(results[0].geometry.location.lng()); 
        getFavouriteResturants('scroll');
      }
    }else if(order_mode!=""){
      if (page == "home_page") {
        $('#distance_filter').hide();
        $('#distance_sort').hide();
        $('#latitude').val(''); 
        $('#longitude').val(''); 
        var box = bootbox.alert({
          message: err_msg,
          buttons: {
              ok: {
                  label: oktext,
              }
          }
        });
        setTimeout(function() {
          box.modal('hide');
        }, 10000);
        //getPopularResturants("","",'scroll');
      }
      else if (page == "order_food") {
        getFavouriteResturants('scroll');
      }
    }else{
      var box = bootbox.alert({
        message: err_msg,
        buttons: {
            ok: {
                label: oktext,
            }
        }
      });
      setTimeout(function() {
        box.modal('hide');
      }, 10000);
    }
  });
}
// store the lat long in session
function addLatLong(lat,long,address){
  var order_mode = $('#order_mode').val();
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'home/addLatLong',
    data : {'lat':lat,'long':long,'address':address,'page':current_pagejs,'order_mode':order_mode},
    success: function(response) {
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
} 
// quick search menu items
function quickSearch(value){
  var order_mode = $("#order_mode").val();
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'home/quickCategorySearch',
    data : {'category_id':value,'order_mode':order_mode},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#popular-restaurants').html(response);
      $('html, body').animate({
            scrollTop: $(".popular-restaurants").offset().top
        }, 2000);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get the popular restaurants
function getPopularResturants(latitude,longitude,scroll){ 
  var order_mode = $('#order_mode').val();
  jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'home/getPopularResturants',
    data : {'latitude':latitude,'longitude':longitude,'order_mode':order_mode},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      var rtl = (SELECTED_LANG == 'ar')?true:false;
      $('#popular-restaurants').html(response.popular_restaurants);
      $('#foodtype_quicksearch').html(response.quick_searches);
      if(response.countcoupon && response.countcoupon>0)
      {
        $('#coupon_section').css('display','');
        $('#coupon_section').html(response.coupon_section_html);
      }
      else
      {
        $('#coupon_section').css('display','none');
        $('#coupon_section').html('');
      }
      $('.food_type').empty().append(response.foodtype_dropdown);
      $('select.food_type')[0].sumo.reload();
      if(response.quick_searches != '') {
        $('.quick-searches-slider').owlCarousel({
          loop: true,
          nav: true,
          rtl:rtl,
          autoplay: true,
          autoplayTimeout:3500,
          navSpeed:1300,
          touchDrag:true,
          slideBy:2,
          autoplaySpeed:1300,
          autoplayHoverPause: true,
          navContainer: '#customNav',
          responsive: {
            0: {
              items: 1,
            },
            480: {
              items: 2,
            },
            600: {
              items: 3,
            },
            1000: {
              items: 5,
            },
            1300: {
              items: 6,
            },
            1550: {
              items: 7
            }
          }
        });
      }
      if(response.coupon_section_html != '') {
        $('.best-offers-slider').owlCarousel({
          loop: true,
          nav: true,
          autoplay: true,
          rtl:rtl,
          autoplayTimeout:3500,
          navSpeed:1300,
          autoplaySpeed:1300,
          autoplayHoverPause: true,
          navContainer: '#customNav2',
          responsive: {
            0: {
              items: 1,
            },
            480: {
              items: 2,
            },
            600: {
              items: 3,
            },
            1550:{
              items: 4
            }
          }
        });
      }
      if($('.res-coupons-slider').length){
        $(".variable").slick({
          dots: false,
          infinite: true,
          autoplay: true,
          variableWidth: true,
          arrow: false,
          autoplaySpeed: 0,
          speed: 8000,
          pauseOnHover: false,
          cssEase: 'linear'
        });
      }
      if($('#address').val()!=''){
        $('#for_address').show();
      } else {
        $('#for_address').hide();
      }
      if($('#resdishes').val()!=''){
        $('#for_res_search').show();
      } else {
        $('#for_res_search').hide();
      }
      if(latitude == '' && longitude == ''){
        $('#distance_sort').hide();
        $('#distance_filter').hide();
      } else {
        $('#distance_sort').show();
        $('#distance_filter').show();
      }
      if (scroll == "scroll") {
        $('html, body').animate({
              scrollTop: $(".popular-restaurants").offset().top
          }, 2000);
      }
      $('#quotes-main-loader').hide();

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      //alert(errorThrown);
    }
    });
}
// get the favourite restaurants
function getFavouriteResturants(scroll){  
  var food_veg = ($('#food_veg').is(":checked"))?1:0;
  var food_non_veg = ($('#food_non_veg').is(":checked"))?1:0;
  var resdishes = $('#resdishes').val();
  var order_mode = $('#order_mode').val();
  var latitude = $('#latitude').val();
  var longitude = $('#longitude').val();
  var minimum_range = $('#minimum_range').val();
  var maximum_range = $('#maximum_range').val();
  var filter_by = $("input[name='filter_by']:checked").val();
  var page = page ? page : 0;
  var food_type = [];
    $('.food_typecls:checked').each(function(i, e) {
        food_type.push($(this).val());
    });

  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url: BASEURL+'restaurant/ajax_restaurants/'+page,
    data : {'latitude':latitude,'longitude':longitude,'resdishes':$.trim(resdishes),'page':page,'minimum_range':minimum_range,'maximum_range':maximum_range,'food_veg':food_veg,'food_non_veg':food_non_veg,'food_type': food_type.join(),'order_mode':order_mode,'filter_by': filter_by},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#order_from_restaurants').html(response);
      $('#resdishes').val($.trim(resdishes));
      if($('#address').val()!=''){
        $('#for_address').show();
      } else {
        $('#for_address').hide();
      }
      if($('#resdishes').val()!=''){
        $('#for_res_search').show();
      } else {
        $('#for_res_search').hide();
      }

      var scrolltopvar = 80;
      if( $(window).width() < 720){
        var scrolltopvar = 40;
      }
      
      if (scroll == "scroll") {
        $('html, body').animate({
              scrollTop: $("#order_from_restaurants").offset().top-scrolltopvar
          }, 2000);
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// recipe page
function searchRecipes(page,err_msg,oktext)
{
  var recipe = $('#recipe').val();
  if ($.trim(recipe) == '' || recipe == undefined) {
  {
    var box = bootbox.alert({
          message: err_msg,
          buttons: {
              ok: {
                  label: oktext,
              }
          }
        });
        setTimeout(function() {
          box.modal('hide');
        }, 10000);
    }
  }
  else
  {
      jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : BASEURL+'recipe/ajax_recipies',
      data : {'recipe':recipe,'page':''},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#sort_recipies').html(response);
        $('#quotes-main-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
      });        
  }
  
}
$('#recipe').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        event.preventDefault();
    }
});
// my profile page js functions
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      marker.formatted_address = responses[0].formatted_address;
    } else {
      marker.formatted_address = 'Cannot determine address at this location.';
    }
    infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
    infowindow.open(map, marker);
    $('#address_field').val(marker.formatted_address);
  });
}
function clearAddressArea(){
  $('#add_address').val('');
  // $('#add_address_area').val('');
  $('#address_field').val('');
}
// get the marker for the map
function getMarker(address_value){
    //initAutocomplete('address_field');
    var place = autocomplete.getPlace();
    var geocoder = new google.maps.Geocoder();
    if (address_value != '') {
        var address = address_value;
    }
    if(current_pagejs == 'MyProfile'){ 
      document.getElementById("city").value = '';
      document.getElementById("state").value = '';
      document.getElementById("country").value = '';
      document.getElementById("zipcode").value = '';
      document.getElementById("latitude").value = '';
      document.getElementById("longitude").value = '';
    }
    // else
    // {
    //     var address = document.getElementById("add_address_area").value;
    // }

    geocoder.geocode( { 'address': address}, function(results, status) {
      if(results && current_pagejs == 'MyProfile'){       
        $.each(results[0].address_components, function( index, value ) {
          $.each(value.types, function( index, types ) {
              if(types == 'administrative_area_level_2'){
                 document.getElementById("city").value = value.long_name;
              }
              if(types == 'administrative_area_level_1'){
                 document.getElementById("state").value = value.long_name;
              }
              if(types == 'country'){
                 document.getElementById("country").value = value.long_name;
              }
              if(types == 'postal_code'){
                 document.getElementById("zipcode").value = value.long_name;
              }
          });
        });
        $('#latitude').val(results[0].geometry.location.lat());
        $('#longitude').val(results[0].geometry.location.lng());  
      }

        // $('#address_field').val(document.getElementById("add_address_area").value);
        if (status == google.maps.GeocoderStatus.OK) {
            //set the map's marker
            var myLatlng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
            marker.setPosition(myLatlng);
            map.setCenter(myLatlng);
            if (address_value != '') {
                $('#latitude').val(results[0].geometry.location.lat());
                $('#longitude').val(results[0].geometry.location.lng());
            }
        }
    });
    return false;
}
// set marker on the map
function setMarker(latitude,longitude,flagedit=false){
    var myLatlng = new google.maps.LatLng(latitude,longitude);
    marker.setPosition(myLatlng);
    map.setCenter(myLatlng);
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
    getAddress(latitude,longitude,'my_profile',flagedit);
}
// add active class
function addActiveClass(value){
    $('.tabs').removeClass('active');
    $('#'+value).addClass('active');
}
// check email validation
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}
// check password validation
function customPassword(password) {
    var regex = /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/;
    return regex.test(password);
}
// check digits validation
function digitCheck(string) {
    // /^([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})$/
    var regex = /^\d{6,15}$/;
    return regex.test(string);
}
// form my profile validation on submit
$( "#form_my_profile" ).on("submit", function( event ) {   
    if ($('#form_my_profile').valid() && $('#first_name').val() != '' && $('#last_name').val() != '' && $('#email').val() != '' && isEmail($('#email').val()) && $('#phone_number').val() != '' && digitCheck($('#phone_number').val()) && (($('#password').val() != '' && $('#confirm_password').val() != '' && $('#password').val() == $('#confirm_password').val()) || ($('#password').val() == '' && $('#confirm_password').val() == ''))) 
    {  
        var formData = new FormData($("#form_my_profile")[0]);
        formData.append('submit_profile', 'Save');
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'myprofile',
            data : formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                location.reload();
                /*if (response == "success") {
                    location.reload();
                }
                else
                {
                    $('#quotes-main-loader').hide();
                    $('#error-msg').html(response);
                    $('#error-msg').show();
                }*/
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                alert(errorThrown);
            }
        });
    }
    event.preventDefault(); 
});
// form my address validation on submit
$( "#form_add_address" ).on("submit", function( event ) { 
    event.preventDefault();
    if ($('#address_field').val() != '' && $('#city').val() != '' && $('#zipcode').val() != '')
    {
      if($('#form_add_address').valid()) {
        var formData = new FormData($("#form_add_address")[0]);
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'myprofile/addAddress',
            data : formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                if (response == "success") {
                    window.location.href = BASEURL+"myprofile/view-my-addresses";
                }
                else
                {
                    $('#quotes-main-loader').hide();
                    $('#add-error-msg').html(response);
                    $('#add-error-msg').show();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                alert(errorThrown);
            }
        });
      }
    }
});
// form my addresses on hidden
$('#add-address').on('hidden.bs.modal', function (e) {  
    $('#add_entity_id').val('');
    $('#address_field').val('');
    $('#zipcode').val('');
    $('#landmark').val('');
    $('#city').val('');
    $('#state').val('');    
    $('#country').val('');
    $('#address_label').val('');
    $('#form_add_address').validate().resetForm();
    $('#add-error-msg').text('');
    $('#submit_address').val(ADD);
    $('#address-form-title').html(ADD);
    getLocation('my_profile');
});
// form my profile on hidden
$('#edit-profile').on('hidden.bs.modal', function (e) {
    $('#form_my_profile').validate().resetForm();
    $('#error-msg').text('');
    $('#error-msg').hide();
    $("#form_my_profile")[0].reset();
    $('#image').val('');
    $("#old").show();
});
// get more orders
function moreOrders(order_flag)
{
    /*if (order_flag == "process") {
        $('#all_current_orders').show();
        $('#more_in_process_orders').hide();
    }
    if (order_flag == "past") {
        $('#all_past_orders').show();
        $('#more_past_orders').hide();
    }*/
    if (order_flag == "past"){
      var page_no = $('#pord_page_no').val();
    }
    else
    {
      var page_no = $('#cord_page_no').val();
    }
    jQuery.ajax({
      type : "POST",
      dataType: 'json',
      url : BASEURL+'myprofile/getOrderPagination',
      data : {'page_no':page_no,'order_flag':order_flag},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if (response.review_html != '') {
          var page_count = parseInt(page_no) + 1;
          if(order_flag == "past")
          {
              $("#all_past_orders").append(response.order_html);
              $('#all_past_orders').show();
              if(response.next_page_count == 0){
                $('#more_past_orders').hide();                
              }
              $('#pord_page_no').val(page_count);
          }
          else
          {
              $("#all_current_orders").append(response.order_html);
              $('#all_current_orders').show();
              if(response.next_page_count == 0){
                $('#more_in_process_orders').hide();
              }
              $('#cord_page_no').val(page_count);
          }
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
// get more events
function moreEvents(order_flag){
    if (order_flag == "upcoming") {
        $('#all_upcoming_events').show();
        $('#more_upcoming_events').hide();
    }
    if (order_flag == "past") {
        $('#all_past_events').show();
        $('#more_past_events').hide();
    }
}
// get orders details
function order_details(order_id){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+'myprofile/getOrderDetails',
            data : {"order_id":order_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#order-details').html(response);
                $('#order-details').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// track orders
function track_order(order_id){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'order',
            data : {"order_id":order_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// get booking details
function booking_details(event_id){
    if (event_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+ 'myprofile/getBookingDetails',
            data : {"event_id":event_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#booking-details').html(response);
                $('#booking-details').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// edit address
function editAddress(address_id){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'myprofile/getEditAddress',
        data : {"address_id":address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            var address = JSON.parse(response);
            $('#user_entity_id').val(address.user_entity_id);
            $('#add_entity_id').val(address.address_id);
            $('#address_field').val(address.address);
            $('#zipcode').val(address.zipcode);
            $('#add_address_area').val(address.search_area);
            $('#latitude').val(address.latitude);
            $('#longitude').val(address.longitude);
            $('#landmark').val(address.landmark);
            $('#state').val(address.state);
            $('#city').val(address.city);
            $('#country').val(address.country);
            $('#address_label').val(address.address_label);
            $('#submit_address').val(EDIT);
            $('#address-form-title').html(EDIT);
            setMarker(address.latitude,address.longitude,true);
            $('#quotes-main-loader').hide();
            $('#add-address').modal('show');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
// show delete address popup
function showDeleteAddress(address_id){
    $('#delete_address_id').val(address_id);
    $('#delete-address').modal('show');
}
// delete address
function deleteAddress(){
    var address_id = $('#delete_address_id').val();
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+ 'myprofile/ajaxDeleteAddress' ,
        data : {'address_id':address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            window.location.href = BASEURL+"myprofile/view-my-addresses";
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#quotes-main-loader').hide();
            alert(errorThrown);
        }
    });
}
// show set main address popup
function showMainAddress(address_id){
    $('#main_address_id').val(address_id);
    $('#main-address').modal('show');
}
// set main address 
function setMainAddress(){
    var address_id = $('#main_address_id').val();
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'myprofile/ajaxSetAddress',
        data : {'address_id':address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            window.location.href = BASEURL+"myprofile/view-my-addresses";
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#quotes-main-loader').hide();
            alert(errorThrown);
        }
    });
}
/*event booking details*/
// get people value
/*function getPeople(value){ 
  var min_capacity = $('#min_people').val();
  var max_capacity = $('#max_people').val();
  var people = (parseInt(value) > 0)?parseInt(value):1; 
  if(people <= max_capacity && people >= min_capacity){
    $('#peepid').html('<strong>'+people+' People</strong>');
    $('#no_of_people').valid();
  }
  if(people > max_capacity){
    if(SELECTED_LANG == 'en') {
      bootbox.alert({
          message: "Maximum capacity allowed for restaurant is "+ max_capacity +".",
          buttons: {
              ok: {
                  label: "Ok",
              }
          }
      });
    } else if(SELECTED_LANG == 'fr') {
      bootbox.alert({
        message: "La capacité maximale autorisée pour le restaurant est"+" "+max_capacity +".",
        buttons: {
          ok: {
              label: "D'accord",
          }
        }
      });
    } else {
      bootbox.alert({
        message: "الحد الأقصى للسعة المسموح بها للمطعم هو" +" "+max_capacity+".",
        buttons: {
          ok: {
              label: "نعم",
          }
        }
      });
    }
  }
  if(people < min_capacity){
    if(SELECTED_LANG == 'en') {
      bootbox.alert({
          message: "Minimum capacity allowed for restaurant is "+ min_capacity +".",
          buttons: {
              ok: {
                  label: "Ok",
              }
          }
      });
    } else if(SELECTED_LANG == 'fr') {
      bootbox.alert({
        message: "La capacité minimale autorisée pour le restaurant est"+" "+min_capacity +".",
        buttons: {
          ok: {
              label: "D'accord",
          }
        }
      });
    } else {
      bootbox.alert({
        message: "الحد الأدنى للسعة المسموح بها للمطعم هو" +" "+min_capacity+".",
        buttons: {
          ok: {
              label: "نعم",
          }
        }
      });
    }
  }
  //$('#no_of_people').val(people);
}*/

$("#check_event_availability #no_of_people").on('keyup',function(e) {
    var people = (parseInt($(this).val()) > 0)?parseInt($(this).val()):1;
    $('#peepid').html('<strong>'+people+' People</strong>');
    if(e.which == 9) {
      var min_capacity = $('#min_people').val();
      var max_capacity = $('#max_people').val();
      //var people = (parseInt($(this).val()) > 0)?parseInt($(this).val()):1; 
      if(people <= max_capacity && people >= min_capacity){
        //$('#peepid').html('<strong>'+people+' People</strong>');
        $('#no_of_people').valid();
      }
      if(people > max_capacity){
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Maximum capacity allowed for restaurant is "+ max_capacity +".",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "La capacité maximale autorisée pour le restaurant est"+" "+max_capacity +".",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "الحد الأقصى للسعة المسموح بها للمطعم هو" +" "+max_capacity+".",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
      if(people < min_capacity){
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Minimum capacity allowed for restaurant is "+ min_capacity +".",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "La capacité minimale autorisée pour le restaurant est"+" "+min_capacity +".",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "الحد الأدنى للسعة المسموح بها للمطعم هو" +" "+min_capacity+".",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
    }
});

// show all the reviews
function showAllReviewsold(){
  $('#quotes-main-loader').show();
  setTimeout(function() {
    $('#quotes-main-loader').hide();
  }, 1000);
  $('#all_reviews').show();
  $('#review_button').hide();
}
function showAllReviews(){
  var page_no = $('#page_no').val();
  var res_content_id_val = $('#res_content_id_val').val();
  jQuery.ajax({
    type : "POST",
    dataType: 'json',
    url : BASEURL+'restaurant/getReviewsPagination',
    data : {'page_no':page_no,'restaurant_content_id':res_content_id_val},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response.review_html != '') {
        $("#all_reviews").append(response.review_html);
        $('#all_reviews').show();
        
        var page_count = parseInt(page_no) + 1;
        $('#page_no').val(page_count);

        if(response.next_page_count == 0){
          $('#review_button').hide();
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
}
// form check availability submit
$("#check_event_availability").on("submit", function(event) {
  if ($("#check_event_availability").valid()) {
    var validator = $("#check_event_availability").validate();
    event.preventDefault();
    var no_of_people = $('#no_of_people').val();
    var date_time = $('#datetimepicker1').val();
    var restaurant_id = $('#event_restaurant_id').val();
    var user_comment = $('#user_comment').val();
    if (restaurant_id != '' && date_time != '' && no_of_people != '') {
      jQuery.ajax({
        type : "POST",
        url : BASEURL+'restaurant/checkEventAvailability',
        data : $('#check_event_availability').serialize(),
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
          var response = JSON.parse(response);
          if (response.hasOwnProperty('incorrect_info')) {
            var box = bootbox.alert({
              message: response.show_message,
              buttons: {
                  ok: {
                      label: response.oktxt,
                  }
              },
            });
            setTimeout(function() {
              box.modal('hide');
            }, 10000);
          }
          if (response.hasOwnProperty('allow_event_booking')) {
            bootbox.confirm({ 
              message: response.allow_event_booking_text,
              buttons: {
                confirm: {
                  label: response.oktxt
                },
                cancel: {
                  label: response.canceltxt
                }
              },
              callback: function(result){
                if (result === true) {
                  location.reload();
                }
              }
            })
          }
          if(!response.hasOwnProperty('result') && response.hasOwnProperty('less_capacity') && response.hasOwnProperty('restaurant_capacity')){
            $('#booking-not-available-capicity').modal('show');
            $('#less').removeClass('display-yes');
            $('#less').addClass('display-no');
            $('#more').addClass('display-yes');
            $('#more').removeClass('display-no');
            $('#booking-not-available-capicity span').text(response['restaurant_capacity']);
          }
          if(!response.hasOwnProperty('result') && response.hasOwnProperty('more_capacity') && response.hasOwnProperty('restaurant_capacity')){
            $('#booking-not-available-capicity').modal('show');
            $('#more').removeClass('display-yes');
            $('#more').addClass('display-no');
            $('#less').removeClass('display-no');
            $('#less').addClass('display-yes');
            $('#booking-not-available-capicity span').text(response['restaurant_capacity']);
          }
          if (response.hasOwnProperty('result')) {
            if(response['result'] == "success"){
              $('#booking-available').modal('show');
            }
            if(response['result'] == "fail"){
              $('#booking-not-available').modal('show');
            }
          }
          $('#quotes-main-loader').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
        }); 
      }
      return false;
    }
});


// add package for event booking
function AddPackage(entity_id){  
  var action;
  if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
    action = "remove";
  }
  else
  {
    action = "add";
  }
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/add_package',
    data : {"entity_id":entity_id,"action":action},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == "success") {
        if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
          $("#addpackage-"+entity_id).removeClass("inpackage");
          $(".addpackage").html(ADD);
          $("#addpackage-"+entity_id).html(ADD);
        }
        else
        {
          $(".addpackage").removeClass("inpackage");
          $("#addpackage-"+entity_id).addClass("inpackage");
          $(".addpackage").html(ADD);
          $("#addpackage-"+entity_id).html(ADDED);
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
  return false;
}
// confirm event booking
function confirmBooking(){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/bookEvent',
    data : $('#check_event_availability').serialize(),
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      var response = JSON.parse(response);
      if (response.hasOwnProperty('incorrect_info')) {
        $('#booking-available').modal('hide');
        var box = bootbox.alert({
          message: response.show_message,
          buttons: {
              ok: {
                  label: response.oktxt,
              }
          },
        });
        setTimeout(function() {
          box.modal('hide');
        }, 10000);
      }
      if (response.hasOwnProperty('allow_event_booking')) {
        $('#booking-available').modal('hide');
        bootbox.confirm({ 
          message: response.allow_event_booking_text,
          buttons: {
            confirm: {
              label: response.oktxt
            },
            cancel: {
              label: response.canceltxt
            }
          },
          callback: function(result){
            if (result === true) {
              location.reload();
            }
          }
        })
      }
      if (response.hasOwnProperty('result')) {
        if(response['result'] == "success"){
          $('#booking-confirmation').modal('show');
        }
        if(response['result'] == "fail"){
          $('#booking-not-available').modal('show');  
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
    return false;
}
/*event booking details js end*/

/*event booking page js*/
function searchEvents(page,err_msg,oktext,flag=''){
  var searchEvent = $('#searchEvent').val();
  if (($.trim(searchEvent) == '' || searchEvent == undefined) && flag=='') {
    var box = bootbox.alert({
      message: err_msg,
      buttons: {
          ok: {
              label: oktext,
          }
      }
    });
    setTimeout(function() {
      box.modal('hide');
    }, 10000);
  } else {
    jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+"restaurant/ajax_events",
    data : {'searchEvent':searchEvent,'page':''},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#sort_events').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
  }
}
$('#searchEvent').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        event.preventDefault();
    }
});
/*event booking page js ends*/

/*checkout page*/
//get lat long
function getLatLong(cart_total)
{  
  var place = autocomplete.getPlace();
    /*var geocoder = new google.maps.Geocoder();
    var address = document.getElementById("add_address").value;    
    geocoder.geocode( { 'address': address}, function(results, status) {
      console.log("statu="+status);
      console.log("google="+google.maps.GeocoderStatus.OK);

    if (status == google.maps.GeocoderStatus.OK) {
      //get delivery charges
      getDeliveryCharges(results[0].geometry.location.lat(),results[0].geometry.location.lng(),"get",cart_total);
      $('#add_latitude').val(results[0].geometry.location.lat());
      $('#add_longitude').val(results[0].geometry.location.lng());
    } 
    });*/
}
// get delivery charges from the address
function getAddLatLong(address_id,cart_total){
  var cart_total = $('#subtotal').val();
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'checkout/getAddressLatLng',
    data : {"entity_id":address_id},
    success: function(response) {
      getDeliveryCharges(response.latitude,response.longitude,"get",cart_total);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get delivery charges
function getDeliveryCharges(latitude,longitude,action,cart_total){
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'checkout/getDeliveryCharges',
    data : {"latitude":latitude,"longitude":longitude,"action":action,'cart_total':cart_total,"payment_optionval":payment_optionval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response.ajax_order_summary);
      if (action == "get") {
        if (response.check != '' && response.check != null) {
          if(!$("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
            $("#submit_order").attr("disabled", false);
          }
        }
        else
        {
          if(cart_total>0){
            $('#delivery-not-avaliable').modal('show');
            $("#submit_order").attr("disabled", true);  
          }
          
        }
      }
      if($("#distance_filter").length == 1) {
        $("#distance_filter").show();
        $('#distance_sort').show();
      }
      $('#quotes-main-loader').hide();
      getCoupons(cart_total,'delivery','','');
      redeemPoints(cart_total,'from_getDeliveryCharges');
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// show delivery options
function showDelivery(cart_total_price,coupon_applied){   
  if (IS_USER_LOGIN == 1 || IS_GUEST_CHECKOUT==1){
    initAutocomplete('add_address');
    // auto detect location if even searched once.
    if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
      getLocation('checkout');
    }
    else
    {
      getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'checkout');
    }
    document.getElementById('delivery-form').style.display ='block';
    document.getElementById('driver-tip-form').style.display ='block';
  }
  handleDriverTip('showDelivery');
  // initAutocomplete('add_address_area');
  //initAutocomplete('add_address');
  jQuery( ".add_new_address" ).prop('required',true);
  if(coupon_applied!='yes')
  {
    getCoupons(cart_total_price,'delivery','');
    redeemPoints(cart_total_price,'from_show_delivery');
  }  
  $('#checkout_form').validate().resetForm();
  $('#checkout_form')[0].reset();
  $('#stripe_cod_btn').show();
  if(!$("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
    $("#submit_order").attr("disabled", false);
  }
  $("#delivery").prop("checked", true);
  $('.delivery-instructions').show();
  var is_guest_checkout = $("#is_guest_checkout").val();
  
  var add_new_address = '';
  if(is_guest_checkout == 'yes'){
    document.getElementById('add_address_content').style.display ='block';
    jQuery("#add_address").prop('required',true);
    jQuery("#zipcode").prop('required',true);
    //jQuery("#landmark").prop('required',true);
    add_new_address = $(".add_new_address").val();
  } 
  else if($('#is_agent').val()=='yes' && $('#consider_guest').val()=='yes'){
    $('#your_address').empty();
    $('.your_address_inp').css('display','none');
    jQuery("#add_address").prop('required',true);
    jQuery("#zipcode").prop('required',true);
    //jQuery("#landmark").prop('required',true);
    add_new_address = $("input[name='add_new_address']:checked").val();
  }else {
    $('#add_address_content').hide();
    $('#your_address_content').hide();
    add_new_address = $("input[name='add_new_address']:checked").val();
  }
  if(add_new_address!=''){
    $("input[name='add_new_address']").prop("checked", true);
    $('input[name="add_new_address"]:radio:first' ).click();
  }
  if(($("input[name='schedule_order']").is(':checked') && $("input[name='schedule_order']:checked").val() == 'yes') || $("input[name='schedule_order'][type='hidden']").val() == 'yes') {
    $('#schedule_delivery_content').removeClass('display-no');
  } else {
    $('#schedule_delivery_content').addClass('display-no');
  }
}
// show pickup options
function showPickup(cart_total_price){  
  document.getElementById('delivery-form').style.display = 'none';
  document.getElementById('driver-tip-form').style.display ='none';
  getCoupons(cart_total_price,'pickup','','');
  redeemPoints(cart_total_price,'from_show_pickup');
  $('#driver_tip').val('');
  $('#checkout_form').validate().resetForm();
  $('#checkout_form')[0].reset();
  $('#stripe_cod_btn').show();
  if(!$("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
    $("#submit_order").attr("disabled", false);
  }
  $("#pickup").prop("checked", true);
  $('.delivery-instructions').hide();
  if(($("input[name='schedule_order']").is(':checked') && $("input[name='schedule_order']:checked").val() == 'yes') || $("input[name='schedule_order'][type='hidden']").val() == 'yes') {
    $('#schedule_delivery_content').removeClass('display-no');
  } else {
    $('#schedule_delivery_content').addClass('display-no');
  }
}
function showsearchcoupon(cart_total_price,err_msg,oktext,is_search)
{ 
  var coupon_searchval = $('#coupon_searchval').val(); 
  /*if (($.trim(coupon_searchval) == '' || coupon_searchval == undefined) && is_search=='yes') {
  {
    var box = bootbox.alert({
          message: err_msg,
          buttons: {
              ok: {
                  label: oktext,
              }
          }
        });
        setTimeout(function() {
          box.modal('hide');
        }, 10000);
    }
  }
  else
  {*/
    var choose_order = $("input[name='choose_order']:checked").val();
    var coupon_searchval = $('#coupon_searchval').val();  
    getCoupons(cart_total_price,choose_order,coupon_searchval,'yes');
    redeemPoints(cart_total_price,'from_showsearchcoupon');
  //}
}
// remove delivery options
function removeDeliveryOptions(){
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'checkout/removeDeliveryOptions',
    data : {"payment_optionval":payment_optionval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get Coupons
function getCoupons(subtotal,order_mode,coupon_searchval,frmcoupon)
{
  var payment_optionval = $("input[name='payment_option']:checked").val();
  $(".card_dtl").hide();
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/getCoupons',
    data : {"subtotal":subtotal,"order_mode":order_mode,"coupon_searchval":coupon_searchval,"frmcoupon":frmcoupon,"payment_optionval":payment_optionval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response.ajax_order_summary);
      $('#coupon_detailid').html(response.html);
      $('#quotes-main-loader').hide();
      $('#coupon_searchval').val(coupon_searchval);
      if (order_mode == "pickup") {
         removeDeliveryOptions();
      }
      //Code for togle coupon detail :: Start
      $('.show-hidden-menu').click(function() {        
        $('.hidden-menu').hide("slow");
        $('.hhshow-hidden-menu').hide(0); //spnshow-hidden-menu
        $('.spnshow-hidden-menu').show(0);

        $('.show-hidden-menu').show(0); 
        if($('#sub'+this.id).is(":visible")){
          $('#sub'+this.id).hide(0);          
        }
        else
        {
          $('#sub'+this.id).show(0);          
        }
       if($('#sub'+this.id).is(":visible")){
          $('#'+this.id).hide(0);
          $('#hh'+this.id).show(0); //spnshow-hidden-menu
          $('#spn'+this.id).hide(0);
          
        }
        else
        {
          $('#'+this.id).show(0);
          $('#hh'+this.id).hide(0); //spnshow-hidden-menu
          $('#spn'+this.id).show(0);
        }        
      });
      $('.hhshow-hidden-menu').click(function()
      {
          $('.hidden-menu').hide(0);
          var dataval = $(this).attr("dataval");
          $('#'+dataval).show(0); 
          $('#spn'+dataval).show(0);         
          $('#'+this.id).hide(0);        
      });
      //Code for togle coupon detail :: End

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get Coupon details
function getCouponDetails(coupon_id,subtotal,order_mode,frm_apply='no')
{
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/addCoupon',
    data : {"coupon_id":coupon_id,"subtotal":subtotal,"order_mode":order_mode,"payment_optionval":payment_optionval,"frm_apply":frm_apply},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response)
    {
      if(response.error=='yes' && response.error_message!='')
      {
        bootbox.alert(response.coupon_error);        
      }
      else
      {
        $('#coupon_modal').modal('hide');
        $('#ajax_order_summary').html(response.ajax_order_summary);
        var temp_total = parseFloat(subtotal) - parseFloat(response.coupon_discount);
        redeemPoints(temp_total,'from_apply_coupon');
      }
      $('#quotes-main-loader').hide();      
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
function removeCouponOptions(coupon_id){
  var payment_optionval = $("input[name='payment_option']:checked").val();
  var subtotal = $('#subtotal').val();
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'checkout/removeCouponOptions',
    data : {"coupon_id":coupon_id,"payment_optionval":payment_optionval,'call_from':'remove_cpn_options'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response);
      redeemPoints(subtotal,'from_remove_cpn');
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// show address
function showAddAdress(){ 
    initAutocomplete('add_address');
    // auto detect location if even searched once.
    if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
      getLocation('checkout');
    }
    else
    {
      getSearchedLocation(SEARCHED_LAT,SEARCHED_LONG,SEARCHED_ADDRESS,'checkout');
    }
    document.getElementById('add_address_content').style.display ='block';
    jQuery("#add_address").prop('required',true);
    //jQuery("#landmark").prop('required',true);
    if($('#add_address').val()==''){
      $('#your_address').val('');
      getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'remove',$("#subtotal").val());
    }    
    // jQuery( "#add_address_area" ).prop('required',true);
    jQuery( "#zipcode" ).prop('required',true);    
    //jQuery( "#city" ).prop('required',true);  
    if($('#your_address_content').length){
      document.getElementById('your_address_content').style.display ='none';
    }
}
// show your already added address
function showYourAdress(){  
  document.getElementById('add_address_content').style.display ='none';
  document.getElementById('your_address_content').style.display = 'block';    
  if($('#your_address').val()==''){
    $('#add_latitude').val('');
    $('#add_longitude').val('');
    $('#add_address').val('');
    getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'remove',$("#subtotal").val());
  }
  jQuery( "#your_address" ).prop('required',true);
}
// show registration form
function showregister(){
  $('#form_front_login_checkout').validate().resetForm();
  $('#form_front_registration_checkout').validate().resetForm();
  $('.login-validations').html('');
  $('#login_form').hide();
  $('#signup_form').show();
}
//show login form
function showlogin(){
  $('#form_front_login_checkout').validate().resetForm();
  $('#form_front_registration_checkout').validate().resetForm();
  $('.register-validations').html('');
  $('#signup_form').hide();
  $('#login_form').show();
}
$( "#guest_checkout_form" ).on("submit", function( event ) { 
  event.preventDefault();
});
$( "#agent_order_form" ).on("submit", function( event ) { 
  event.preventDefault();
});
// submit checkout form
$( "#checkout_form" ).on("submit", function( event ) { 
  var guest_checkout_form_valid = 'yes';
  if($('#is_guest_checkout').val()=='yes'){
    $('#guest_checkout_form').submit(); 
    if($('#guest_checkout_form').valid()){
      guest_checkout_form_valid = 'yes';
      //$('#guest_checkout_form').submit(); 
    } else {
      guest_checkout_form_valid = 'no';
    }
  } else {
    guest_checkout_form_valid = 'yes';
  }
  var agent_order_form_valid = 'yes';
  if($('#is_agent').val()=='yes'){
    $('#agent_order_form').submit();
    if($('#agent_order_form').valid()){
      agent_order_form_valid = 'yes';
    } else {
      agent_order_form_valid = 'no';
    }
  } else {
    agent_order_form_valid = 'yes';
  }
  event.preventDefault();
  var choose_order = $("input[name='choose_order']:checked").val();
  var payment_option = $("input[name='payment_option']:checked").val(); 
  if($('#is_guest_checkout').val()=='yes'){
    var add_new_address = $(".add_new_address").val();
  } else {
    var add_new_address = $("input[name='add_new_address']:checked").val();
  }
  // Validate Google Address for lat - long
  if($('#add_address').val() != '' && choose_order == "delivery" && add_new_address=="add_new_address" && agent_order_form_valid=='yes' && guest_checkout_form_valid=='yes'){    
    if($('#add_latitude').val() == '' || $('#add_longitude').val() == ''){      
      $("#add_address").focus();
      $("#add_address_error").show();
      event.preventDefault();
      return false;
    } else {
      $("#add_address_error").hide();
    }
  }else if($('#add_address').val() != '' && choose_order == "delivery" && add_new_address=="add_new_address"){
    if($('#add_latitude').val() == '' || $('#add_longitude').val() == ''){      
      $("#add_address_error").show();      
      return false;
    } else {
      $("#add_address_error").hide();
    }
  }
  var is_scheduling_valid = 'yes';
  if(($("input[name='schedule_order']:checked").val() == 'yes' || $("input[name='schedule_order'][type='hidden']").val() == 'yes') && ($('#datetimepicker1').val() == '' || $('#time_slot').find('option:selected').val() == '')) {
    var is_scheduling_valid = 'no';
  }

  if (is_scheduling_valid == 'yes' && agent_order_form_valid == 'yes' && guest_checkout_form_valid == 'yes' && $('#checkout_form').valid() && ((choose_order == "delivery" && ((add_new_address == "add_your_address" && $('#your_address').val() != '') || (add_new_address == "add_new_address" && $('#add_address').val() != '' && $('#zipcode').val() != ''))) || choose_order == "pickup") && payment_option != '' && payment_option != undefined) 
  {
    var restaurant_id = $('#cart_restaurant').val();
    var menu_ids = $('#menuids').val();
    var menu_ids_arr = menu_ids.split(',');
    var scheduleddate_inp = ($('#datetimepicker1').val()) ? $('#datetimepicker1').val() : '';
    var scheduledtime_inp = ($('#slot_open_time').val()) ? $('#slot_open_time').val() : '';
    var is_scheduling_allowed = ($('#res_allow_scheduled_delivery').val() == '1') ? 1 : 0;
    //check if user mobile number is verified
    var order_user_id = 0;
    if(IS_GUEST_CHECKOUT == 1 || $('#consider_guest').val() == 'yes') {
      order_user_id = 0;
    } else if($('#consider_guest').val() == 'no' && $('#exist_user_id').val() != 0) {
      order_user_id = $('#exist_user_id').val();
    } else {
      order_user_id = (USER_ID) ? USER_ID : 0;
    }
    var guest_mobile_number = '';
    var guestphonecode = '';
    var guestfirstname = '';
    var guestlastname = '';
    var guestemail = '';
    if(($('#consider_guest').val() == 'yes' && $('#exist_user_id').val() == 0) || IS_GUEST_CHECKOUT == 1) {
      guest_mobile_number = $('#login_phone_number').val();
      guestphonecode = $('#phone_code').val();
      guestfirstname = $('#first_name').val();
      guestlastname = $('#last_name').val();
      guestemail = $('#email_inp').val();
    }
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : BASEURL+'checkout/checkUserVerified',
      data : {'order_user_id':order_user_id, 'guest_mobile_number':guest_mobile_number, 'guestphonecode':guestphonecode, 'guestfirstname':guestfirstname, 'guestlastname':guestlastname, 'guestemail': guestemail},
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if(response.status == 0 && response.message == 'add_mobile_number') {
          $('#user_otp').val('');
          $('#digit-1').val('');
          $('#digit-2').val('');
          $('#digit-3').val('');
          $('#digit-4').val('');
          $('#digit-5').val('');
          $('#digit-6').val('');
          $('#verifyotp_success').hide();
          $('#verifyotp_submit_page').val('add_phn_no');
          $('#add_number_from_checkout').val(1);
          $("#user_otp").css("display", "none");
          $(".user_otp_divmodal").css("display", "none");
          $("#digit-1").removeAttr("required");
          $("#digit-2").removeAttr("required");
          $("#digit-3").removeAttr("required");
          $("#digit-4").removeAttr("required");
          $("#digit-5").removeAttr("required");
          $("#digit-6").removeAttr("required");
          $('#verifyotp_error').text('');
          $('#verifyotp_error').hide();
          $('#verifyotp_modaltitle').text(ADD_MOBILE_NUMBER);
          $('#enter_otp_text').text(ENTER_YOUR_MOBILE_NUMBER);
          $("#verifyotp_submit_page").css("display", "block");
          $("#mobile_number").attr("required", "true");
          $(".mobile_number_divmodal").css("display", "inline-block");
          $('#verify-otp-modal').modal('show');
        } else if(response.status == 2 && response.message == 'add_guest_mobile_number') {
          $('#user_otp').val('');
          $('#digit-1').val('');
          $('#digit-2').val('');
          $('#digit-3').val('');
          $('#digit-4').val('');
          $('#digit-5').val('');
          $('#digit-6').val('');
          $("#mobile_number").removeAttr("required");
          $("#digit-1").attr("required", "true");
          $("#digit-2").attr("required", "true");
          $("#digit-3").attr("required", "true");
          $("#digit-4").attr("required", "true");
          $("#digit-5").attr("required", "true");
          $("#digit-6").attr("required", "true");

          $('#verifyotp_submit_page').val('Submit');
          $('#verify_guest_number_from_checkout').val(1);
          $(".user_otp_divmodal").css("display", "block");
          $("#verifyotp_submit_page").css("display", "block");
          $(".mobile_number_divmodal").css("display", "none");
          $('#verifyotp_error').text('');
          $('#verifyotp_error').hide();

          var set_mobile_number = (response.guestphonecode) ? '+'+response.guestphonecode+response.guest_mobile_number : '+1'+response.guest_mobile_number;
          $('#mobile_number').val('');
          $('#phone_code_otp').val('');
          // $('#login_phone_number').val(set_mobile_number);
          // $('#phone_code').val(response.guestphonecode);
          $("#login_phone_number").attr("readonly", "true");
          $('#verify-otp-modal').modal('show');
        } else if(response.status == 1) {
          jQuery.ajax({
            type : "POST",
            dataType : "json",
            url : BASEURL+'cart/checkResStat',
            data : {'restaurant_id':restaurant_id,'menu_ids':menu_ids_arr, 'scheduleddate_inp':scheduleddate_inp, 'scheduledtime_inp':scheduledtime_inp, 'is_scheduling_allowed':is_scheduling_allowed},
            beforeSend: function(){
              $('#quotes-main-loader').show();
            },
            success: function(response) {
              if(response.status == 'res_unavailable') {
                $('#quotes-main-loader').hide();
                var box = bootbox.alert({
                  message: response.show_message,
                  buttons: {
                      ok: {
                          label: response.oktxt,
                      }
                  },
                });
                setTimeout(function() {
                  box.modal('hide');
                }, 10000);
              } else {
                //check if total price is updated or not & reload summary
                var payment_optionval = $("input[name='payment_option']:checked").val();
                jQuery.ajax({
                  type : "POST",
                  dataType : 'json',
                  url : BASEURL+'checkout/checkoutItem_reload' ,
                  data : {"entity_id":0,"restaurant_id":0,"payment_optionval":payment_optionval,'call_from':'confirm_order'},
                  beforeSend: function(){
                      $('#quotes-main-loader').show();
                  },
                  success: function(checkout_reload_response) {
                    if($("input[name='choose_order']:checked").val() == 'delivery') {
                      $('#driver-tip-form').html(checkout_reload_response.ajax_driver_tips);
                    }
                    $('#ajax_order_summary').html(checkout_reload_response.ajax_order_summary);
                    if(parseFloat(checkout_reload_response.old_total_price) != parseFloat(new_total_price)){
                      $('#create_intent_stripe').val('no');
                      $('#ajax_your_items').html(checkout_reload_response.ajax_your_items);      
                      $('#ajax_your_suggestion').html(checkout_reload_response.ajax_your_suggestion);
                      $('#subtotal').val(checkout_reload_response.cart_total);
                      if($("input[name='choose_order']:checked").val() == 'delivery') {
                        if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
                          if($("#add_address").val() != '' && $("#add_latitude").val() != '' && $("#add_longitude").val() != '') {
                            getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'get',checkout_reload_response.cart_total);
                          }
                        }
                        else if($("input[name='add_new_address']:checked").val() == 'add_your_address' && $('#your_address').val() != ''){
                          getAddLatLong($('#your_address').val(),checkout_reload_response.cart_total)
                        }
                      }
                      $('#quotes-main-loader').hide();
                      $('#cart_total_updated').modal('show');
                    } else {
                      $('#cart_total_updated').modal('hide');
                      if($('#create_intent_stripe').val() == 'no'){
                        $('#create_intent_stripe').val('yes');
                        placeOrder();
                      } else {
                        if(payment_option == 'stripe'){ //stripe
                          $('#order-confirmation').modal('hide');
                          $('#quotes-main-loader').hide();
                          $('#user_details').modal('show');
                        } else { //cod and paypal
                          $('#order-confirmation').modal('hide');
                          $('#user_details').modal('hide');
                          var dataString = new FormData($("#checkout_form")[0]);
                          //var dataString = $("#checkout_form").serialize();
                          addOrder(dataString);
                        }
                      }
                    }
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                  }
                });
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert(errorThrown);
            }
          });
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  } else {
    event.preventDefault();
    if(is_scheduling_valid == 'no' && agent_order_form_valid == 'yes' && guest_checkout_form_valid == 'yes') {
      $('html, body').animate({
        scrollTop: $("#order_mode_btn").offset().top
      });
    }
  }
});
function placeOrder() {
  $('#create_intent_stripe').val('yes');
  $('#submit_order').click();
}
function closeCartUpdatedModal() {
  $('#create_intent_stripe').val('yes');
  $('#cart_total_updated').modal('hide');
}
function stripeAddOrder(dataString) {
  if($('#is_guest_checkout').val()=='yes'){
    var formData = $('#guest_checkout_form').serialize();
    dataString.append('guest_form', formData);
  } else if($('#is_agent').val()=='yes'){
    var formData = $('#agent_order_form').serialize();
    dataString.append('guest_form', formData);
  }
  jQuery.ajax({
      type : "POST",
      dataType: 'json',
      url : BASEURL+'checkout/addOrder',
      data : dataString,
      cache: false, 
      processData: false,
      contentType: false,
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('#user_details').modal('hide');
        $('#track_order').html(response.order_id);
        $('#earned_points').html(response.earned_points);
        $('#order-confirmation').modal('show');
        $('#order-not-placed').modal('hide');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
/* mobilPay payment integration : start */
function addOrder(dataString){
  if($('#is_guest_checkout').val()=='yes'){
    var formData = $('#guest_checkout_form').serialize();
    dataString.append('guest_form', formData);
  } else if($('#is_agent').val()=='yes'){
    var formData = $('#agent_order_form').serialize();
    dataString.append('guest_form', formData);
  }
  jQuery.ajax({
      type : "POST",
      dataType: 'json',
      url : BASEURL+'checkout/addOrder',
      data : dataString,
      cache: false, 
      processData: false,
      contentType: false,
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(response) {
              $('#quotes-main-loader').hide();
              if(response.payment_option == 'paypal'){
                $("#stripe_cod_btn").hide();
                $("#paypal-button").show();
                $('#earned_points').html(response.earned_points);
              } 
              else if (response.result == "success" &&  response.payment_status === '' &&  response.payment_option == 'cod') { //cod
                $('#track_order').html(response.order_id);
                $('#earned_points').html(response.earned_points);
                $('#order-confirmation').modal('show');
                $('#order-not-placed').modal('hide');
                window.stop();
              }
              window.stop();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
      });
}
/* mobilPay payment integration : end */
// check custom checkout item 
function EditCheckoutItemCount(customQuantity1,entity_id,restaurant_id,cart_key){
  /*if($("input[name='choose_order']:checked").val() == 'delivery'){
    if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
      jQuery("#add_address").val('');
      jQuery("#landmark").val('');
      jQuery("#add_address").prop('required',true);
      jQuery("#landmark").prop('required',true);
    }
    else if($("input[name='add_new_address']:checked").val() == 'add_your_address'){
      $("#your_address")[0].selectedIndex = 0;
    }
    else {
      $("#your_address")[0].selectedIndex = 0;
      jQuery("#add_address").val('');
      jQuery("#landmark").val('');
      jQuery("#add_address").prop('required',true);
      jQuery("#landmark").prop('required',true);
      $("input[name='add_new_address']").prop("checked", true);
      $('input[name="add_new_address"]:radio:first' ).click();
    }
  }*/
  if(customQuantity1!='')
  {
     var customQuantity=(customQuantity1=='0')?'1':customQuantity1;
     var choose_order = $("input[name='choose_order']:checked").val();
     var payment_optionval = $("input[name='payment_option']:checked").val();

      jQuery.ajax({
      type : "POST",
      dataType : 'json',
      url : BASEURL+'checkout/ajax_checkout',
      data : {"customQuantity":customQuantity,"entity_id":entity_id,"restaurant_id":restaurant_id,"cart_key":cart_key,'is_main_cart':'checkout',"payment_optionval":payment_optionval},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        //scheduling changes based on out of stock items
        $('.scheduling_mandatory_div').html(response.schedule_mandatory_html);
        if(response.is_out_of_stock_item_in_cart) {
          //scheduling mandatory
          $('#schedule_delivery_content').removeClass('display-no');
        } else {
          //scheduling not mandatory
          $('#schedule_delivery_content').addClass('display-no');
        }
        //$("#order_mode_btn").load(" #order_mode_btn > *");
        //$("#coupon_select").load(" #coupon_select > *");
        $('#ajax_your_items').html(response.ajax_your_items);
        if($("input[name='choose_order']:checked").val() == 'delivery') {
          $('#driver-tip-form').html(response.ajax_driver_tips);
        }
        $('#ajax_order_summary').html(response.ajax_order_summary);
        $('#ajax_your_suggestion').html(response.ajax_your_suggestion);
        $('#subtotal').val(response.cart_total);
        redeemPoints(response.cart_total,'from_ajax_checkout');
        //getCouponDetails('',$('#subtotal').val(),choose_order);
        if($("input[name='choose_order']:checked").val() == 'delivery') {
          if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
            if($("#add_address").val() != '' && $("#add_latitude").val() != '' && $("#add_longitude").val() != '') {
              getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'get',response.cart_total);
            }
          }
          else if($("input[name='add_new_address']:checked").val() == 'add_your_address' && $('#your_address').val() != ''){
            getAddLatLong($('#your_address').val(),response.cart_total)
          }
        }
        //$('#quotes-main-loader').hide();
        if ($('#total_cart_items').val() == null) { 
          $('#order_mode_method').hide();
        }
        else if ($('#total_cart_items').val() == null && $("input[name=item_count_check]").val() == null) { 
          $('#order_mode_method').hide();
        }
        else
        {
          if (IS_USER_LOGIN == 1 ){
            //document.getElementById('delivery-form').style.display ='block';
          }
        }
        $('#quotes-main-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  }
  
}
function customCheckoutItemCount(entity_id,restaurant_id,action,cart_key,delete_module,ok,cancel){
  /*if($("input[name='choose_order']:checked").val() == 'delivery'){
    if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
      jQuery("#add_address").val('');
      jQuery("#landmark").val('');
      jQuery("#add_address").prop('required',true);
      jQuery("#landmark").prop('required',true);
      // $("input[name='add_new_address']").prop("checked", true);
      // $('input[name="add_new_address"]:radio:first' ).click();
    }
    else if($("input[name='add_new_address']:checked").val() == 'add_your_address'){
      $("#your_address")[0].selectedIndex = 0;
    }
    else {
      $("#your_address")[0].selectedIndex = 0;
      jQuery("#add_address").val('');
      jQuery("#landmark").val('');
      jQuery("#add_address").prop('required',true);
      jQuery("#landmark").prop('required',true);
      $("input[name='add_new_address']").prop("checked", true);
      $('input[name="add_new_address"]:radio:first' ).click();
    }
  }*/
  if(!$("#agent_order_form").find(`[id='EmailExist']`).is(':visible')) {
    $("#submit_order").attr("disabled", false);
  }
  var payment_optionval = $("input[name='payment_option']:checked").val();
  if(action == 'remove')
  {
      bootbox.confirm({
        message: delete_module,
        buttons: {
            confirm: {
                label: ok,
            },
            cancel: {
                label: cancel,
            }
        },
        callback: function (removeitem) {         
          if (removeitem) {
            jQuery.ajax({
            type : "POST",
            dataType : 'json',
            url : BASEURL+'checkout/ajax_checkout',
            data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'checkout','payment_optionval':payment_optionval},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
              //scheduling changes based on out of stock items
              $('.scheduling_mandatory_div').html(response.schedule_mandatory_html);
              if(response.is_out_of_stock_item_in_cart) {
                //scheduling mandatory
                $('#schedule_delivery_content').removeClass('display-no');
              } else {
                //scheduling not mandatory
                $('#schedule_delivery_content').addClass('display-no');
              }
              $("#order_mode_btn").load(" #order_mode_btn > *");
              //$("#coupon_select").load(" #coupon_select > *");
              $('#ajax_your_items').html(response.ajax_your_items);
              if($("input[name='choose_order']:checked").val() == 'delivery') {
                $('#driver-tip-form').html(response.ajax_driver_tips);
              }
              $('#ajax_your_suggestion').html(response.ajax_your_suggestion);
              $('#ajax_order_summary').html(response.ajax_order_summary);
              $('#subtotal').val(response.cart_total);
              redeemPoints(response.cart_total,'from_ajax_checkout');
              if($("input[name='choose_order']:checked").val() == 'delivery') {
                if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
                  if($("#add_address").val() != '' && $("#add_latitude").val() != '' && $("#add_longitude").val() != '') {
                    getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'get',response.cart_total);
                  }
                }
                else if($("input[name='add_new_address']:checked").val() == 'add_your_address' && $('#your_address').val() != ''){
                  getAddLatLong($('#your_address').val(),response.cart_total)
                }
              }
              if (action == "remove" && $('#total_cart_items').val() == null) { 
                $('#order_mode_method').hide();
              }
              else
              {
                if (IS_USER_LOGIN == 1 ){
                  //document.getElementById('delivery-form').style.display ='block';
                }
              }
              $('#quotes-main-loader').hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
              alert(errorThrown);
            }
            });
        }
      }
    });
  }
  else
  {
    var choose_order = $("input[name='choose_order']:checked").val(); 
    var comment = $("#item_comment_"+entity_id+"_"+cart_key).val();
    var payment_optionval = $("input[name='payment_option']:checked").val(); 
    jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/ajax_checkout',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'checkout','comment':comment,'payment_optionval':payment_optionval},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      //scheduling changes based on out of stock items
      $('.scheduling_mandatory_div').html(response.schedule_mandatory_html);
      if(response.is_out_of_stock_item_in_cart) {
        //scheduling mandatory
        $('#schedule_delivery_content').removeClass('display-no');
      } else {
        //scheduling not mandatory
        $('#schedule_delivery_content').addClass('display-no');
      }
      //$("#order_mode_btn").load(" #order_mode_btn > *");
      //$("#coupon_select").load(" #coupon_select > *"); 
      $('#ajax_your_items').html(response.ajax_your_items);
      if($("input[name='choose_order']:checked").val() == 'delivery') {
        $('#driver-tip-form').html(response.ajax_driver_tips);
      }
      $('#ajax_your_suggestion').html(response.ajax_your_suggestion);
      $('#ajax_order_summary').html(response.ajax_order_summary);
      $('#subtotal').val(response.cart_total); 
      redeemPoints(response.cart_total,'from_ajax_checkout');
      if($("input[name='choose_order']:checked").val() == 'delivery') {
        if(($("#is_guest_checkout").val() == 'yes' && $(".add_new_address").val() == 'add_new_address') || $("input[name='add_new_address']:checked").val() == 'add_new_address'){
          if($("#add_address").val() != '' && $("#add_latitude").val() != '' && $("#add_longitude").val() != '') {
            getDeliveryCharges($("#add_latitude").val(),$("#add_longitude").val(),'get',response.cart_total);
          }
        }
        else if($("input[name='add_new_address']:checked").val() == 'add_your_address' && $('#your_address').val() != ''){
          getAddLatLong($('#your_address').val(),response.cart_total)
        }
      }
      if (action == "remove" && $('#total_cart_items').val() == null) { 
        $('#order_mode_method').hide();
      }
      else if (action == "minus" && $('#total_cart_items').val() == null && $("input[name=item_count_check]").val() == null) {
        //getCouponDetails('',$('#subtotal').val(),choose_order)
        $('#order_mode_method').hide();
      }
      else
      {
        //getCouponDetails('',$('#subtotal').val(),choose_order)
        if (IS_USER_LOGIN == 1 ){
          //document.getElementById('delivery-form').style.display ='block';
        }
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
  }
}
function redeemPoints(temp_total,call_from=''){
  var redeem = $('#submit_redeem').val();
  if(call_from != ''){
    if(redeem == 'Redeem' || redeem == undefined){
      redeem = 'Cancel Redeem';
    } else {
      redeem = 'Redeem';
    }
  }
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
      type : "POST",
      dataType: 'json',
      url : BASEURL+'checkout/redeemPoints',
      data : {'redeem':redeem,'temp_total':temp_total,"payment_optionval":payment_optionval},
      beforeSend: function(){
        if(call_from == ''){
          $('#quotes-main-loader').show();
        }
      },
      success: function(response) {
        $('#ajax_order_summary').html(response.ajax_order_summary);
        $('#quotes-main-loader').hide();
        $("#submit_redeem").attr('value', response.redeem_submit);
        if(call_from == ''){
          if(response.min_redeem_point_alert !=''){
            var box = bootbox.alert({
              message: response.min_redeem_point_alert,
              buttons: {
                  ok: {
                      label: response.oktxt,
                  }
              },
            });
            setTimeout(function() {
              box.modal('hide');
            }, 10000);
          }
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });   
}
/*checkout page js ends*/
//get item price
var totalPrice = 0;
var radiototalPrice = 0;
var checktotalPrice = 0;
function getItemPrice(id,price,is_multiple,item_id)
{
    var qtyval = $('#qtyaddtocart-'+item_id).val(); 
    if(qtyval != '' && (parseInt(qtyval) > 0)){
    }
    else
    {
      $('#qtyaddtocart-'+item_id).val(1);
      var qtyval = 1;
    } 
    
    if (is_multiple != 1) {
      radiototalPrice = 0;
      //$("#custom_items_form input[type=radio]:checked").each(function() { 
      $("input:radio.radio_addons:checked").each(function() {  
        var sThisVal = (this.checked ? $(this).attr("amount") : 0);
        radiototalPrice = parseFloat(radiototalPrice) + parseFloat(sThisVal);
      });
    }
    else
    {
      checktotalPrice = 0;
      $('.check_addons:checkbox:checked').each(function () { 
        var sThisVal = (this.checked ? $(this).attr("amount") : 0);
        checktotalPrice = parseFloat(checktotalPrice) + parseFloat(sThisVal);        
      });
    }
    totalPrice = radiototalPrice + checktotalPrice;
    var total_display = $('#subTotal_for_cal').val();
    totalPrice = totalPrice + parseFloat(total_display);
    totalPrice = parseFloat(totalPrice)*parseFloat(qtyval)
    $('#totalPrice').html(totalPrice.toFixed(2));
    $('#subTotal').val(totalPrice.toFixed(2));
}
// get the addons to cart
function AddAddonsToCart(menu_id,item_id,mandatory,mandatory_arr,reload){ 
  var restaurant_id = $("#restaurant_id").val();
  var user_id = $("#user_id").val();
  //var totalPrice = $('#subTotal').val();
  var totalPrice = ($('#subTotal').val()=='0')? $('#subTotal_for_cal').val() : $('#subTotal').val();
  //addons category mandatory changes :: start
  var addons_mandatory = JSON.parse(mandatory_arr);
  var checked_mandatory = 'yes';
  var cat_name_str = '';
  if(addons_mandatory) {
    for(let i=0; i<addons_mandatory.length; i++){
      var id_val = addons_mandatory[i];
      var get_category_elements = document.querySelectorAll('[addons_category_id="'+id_val+'"]');
      
      if(get_category_elements[0].type == 'radio') { //for radio
          var radioValue = $("input[name='"+get_category_elements[0].name+"']:checked").val(); 
          if(!radioValue){
            checked_mandatory = 'no';
            cat_name_str = document.getElementsByName(get_category_elements[0].name)[0].getAttribute("addons_category");
            break;
          }
      } else { //for checkbox
          checked_mandatory = 'no';
          for (let j = 0; j < get_category_elements.length; j++) {
            if(get_category_elements[j].checked){
              checked_mandatory = 'yes';
            }
          }
          if(checked_mandatory == 'no'){
            cat_name_str = document.getElementsByName(get_category_elements[0].name)[0].getAttribute("addons_category");
            break;
          }
      }
    }
  }
  //addons category mandatory changes :: end
    var valueArray = new Array();
    $('.check_addons:checkbox:checked').each(function () { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id");    
      if (valueArray.length > 0) {
        jQuery.each( valueArray, function( key, value ) { 
          var new_addons_list = new Array();
          if (value.addons_category_id == addons_category_id) {
            var addonslist = value.addons_list;
            if (Array.isArray(value.addons_list) == false) {
              new_addons_list.push({
                "add_ons_id": value.addons_list.add_ons_id, 
                "add_ons_name": value.addons_list.add_ons_name,
                "add_ons_price": value.addons_list.add_ons_price
              });
            }
            else
            { 
              if (addonslist.length > 0) {
                jQuery.each(addonslist, function( key, value ) { 
                  new_addons_list.push(value);
                });
              } 
            }
            
            new_addons_list.push(addonValue);
            value.addons_list = new_addons_list;
          }
          else
          {
            valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
            });
          }
        });
      }
      else
      {
        valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
        });
      }
    });
    $("#custom_items_form input[type=radio][class='radio_addons']:checked").each(function() { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id");
      var new_addons_list = new Array();
      if (valueArray.length > 0) { 
        jQuery.each( valueArray, function( key, value ) {
          if (value.addons_category_id == addons_category_id) {
            new_addons_list.push(value.addons_list);
            new_addons_list.push(addonValue);
            valueArray.splice(key, 1);
          }
        });
      }
      if (new_addons_list.length > 0) {
        addonValue = new_addons_list;
      }
      valueArray.push({
            'addons_category_id':addons_category_id,
            'addons_category':addons_category,
            'addons_list':addonValue
      });
    });
    var arr = [];
    var addons_category_id_arr = [];
      if (valueArray.length > 0) { 
        jQuery.each( valueArray, function( key, value ) { 
          var addons = value.addons_list;
          var addons_count = addons.length;
          addons_category_id_arr.push(value.addons_category_id);
          arr.push({
            'addons_category_id':value.addons_category_id,
            'key':key,
            'addons_count':(addons_count)?addons_count:0
          });
        });
      }
      var unique_addons_category = [];
      $.each(addons_category_id_arr, function(i, el){
          if($.inArray(el, unique_addons_category) === -1) unique_addons_category.push(el);
      });
      var maxval = [];
      var arrkeys = [];
      if (unique_addons_category.length > 0) {
        jQuery.each( unique_addons_category, function( key, value ) {
          var max = 0;
          var keyvalue = '';
          if (arr.length > 0) {
            jQuery.each( arr, function( arrkey, arrvalue ) {
              if (arrvalue.addons_category_id == value) {
                if(max <= arrvalue.addons_count){
                  max = arrvalue.addons_count;
                  keyvalue = arrvalue.key;
                }
              }
            });
            maxval.push({ 
              'id':value,
              'addons_count': max,
              'key': keyvalue
            });
            arrkeys.push(keyvalue);
          }
        });
      }
      var finalValueArray = [];
      // to unset the duplicate keys
      if (valueArray.length > 0) {
        jQuery.each( valueArray, function( key, value ) {
          if (arrkeys.length > 0) {
            if(jQuery.inArray(key, arrkeys) !== -1) { 
              finalValueArray.push(value);
            }
          }
          else
          {
            finalValueArray = valueArray;
          }
        });
      }

      var qtyval = $('#qty'+item_id).val();
      if (qtyval != '' && (parseInt(qtyval) > 0))
      {
      }
      else
      {
        var existingStyles = $('#qty'+item_id).attr("style");
        $('#qty'+item_id).attr("style",existingStyles+"border:1px solid red !important;");
        //alert('quantity required');
        return false;
      }

    // send addons array to cart
    if ((mandatory==0 && qtyval != '' && (parseInt(qtyval) > 0)) || (mandatory==1 && checked_mandatory=='yes' && finalValueArray.length > 0 && qtyval != '' && (parseInt(qtyval) > 0))) {
      //(finalValueArray.length > 0) { 
      jQuery.ajax({
        type : "POST",
        url : BASEURL+'cart/addToCart',
        data : {'menu_id':menu_id,'user_id':user_id,'restaurant_id':restaurant_id,'totalPrice':totalPrice,'add_ons_array':finalValueArray,'qtyval':qtyval},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
          if(reload=='checkout'){
              //window.location.replace(BASEURL+'checkout');
              //Added to load checkout cart item
              checkoutItem_reload(menu_id,restaurant_id)
          }else if(reload=='checkout_as_guest'){
              //window.location.replace(BASEURL+'checkout/checkout_as_guest');
              //Added to load checkout cart item
              checkoutItem_reload(menu_id,restaurant_id)
          }
          $('#quotes-main-loader').hide();
          $('#myModal').modal('hide');
          $('#your_cart').html(response);
          $('.'+item_id).html(ADDED);
          $('.'+item_id).removeClass('add');
          $('.'+item_id).addClass('added');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
        });
    } else {
      if(mandatory == 1){
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Please select atleast one item from "+ cat_name_str +".",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "Veuillez sélectionner au moins un élément de "+ cat_name_str +".",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "يرجى تحديد عنصر واحد على الأقل من " +" "+cat_name_str+".",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
    }
}
function addReview(restaurant_id,res_content_id,order_id){  
  $('#review_restaurant_id').val(restaurant_id);
  $('#review_res_content_id').val(res_content_id);
  $('#review_order_id').val(order_id);
  $('#reviewModal').modal('show');
}
// form check availability submit
$("#review_form").on("submit", function(event) {
  event.preventDefault();
  if ($("input[name=rating]:checked").val() != '' && $('#review_text').val() != '' && $("#review_form").valid()) {
    jQuery.ajax({
      type : "POST", 
      dataType: "html",
      url : BASEURL+'restaurant/addReview',  
      data : $('#review_form').serialize(),
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if (response == 'success') {
          location.reload();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  }
  else
  {
    return false;
  }
});
function all_couponShow(cart_total){
  var subtotal = $('#subtotal').val();
  showsearchcoupon(subtotal,'','','no');
  $('#coupon_modal').modal('show');
}
//new changes for menu details on image click :: start
function checkCartRestaurantDetails(entity_id,restaurant_id,is_closed,is_addon,item_id,recipe,recipe_page) {
  if(is_addon == '') {
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'cart/checkCartRestaurantDetails',
      data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"is_closed":is_closed,"recipe":recipe,"recipe_page":recipe_page},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('#menuDetailModal').html(response);
        $('#menuDetailModal').modal('show');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  } else {
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'restaurant/getCustomAddOnsDetails',
      data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"is_closed":is_closed,"recipe":recipe,"recipe_page":recipe_page},
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#addonsMenuDetailModal').html(response);
        $('#addonsMenuDetailModal').modal('show');
        $('#quotes-main-loader').hide();   
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  }
}
function checkRestaurantinCart(entity_id,restaurant_id,is_addon,item_id,is_closed,recipe_page)
{
  var qtyval = $('#qty'+item_id).val();
  if (qtyval != '' && (parseInt(qtyval) > 0))
  {    
  }
  else
  {
    var existingStyles = $('#qty'+item_id).attr("style");
    $('#qty'+item_id).attr("style",existingStyles+"border:1px solid red !important;");
    //alert('quantity required');
    return false;
  }

  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkCartRestaurant',
    data : {"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 0) {
        // another restaurant
        $('#menuDetailModal').modal('hide');
        $('#addonsMenuDetailModal').modal('hide');
        $('#rest_entity_id').val(entity_id);
        $('#rest_restaurant_id').val(restaurant_id);
        $('#rest_is_addon').val(is_addon);
        $('#item_id').val(item_id);
        $('#is_closed1').val(is_closed);
        $('#anotherRestModal').modal('show');
      }
      if (response == 1) {
        // same restaurant
        AddToCart(entity_id,restaurant_id,item_id,recipe_page,qtyval);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
//for customized items
function checkaddonsRestaurantinCart(entity_id,is_addon,item_id,is_closed="",mandatory,mandatory_arr,recipe_page) {
  var restaurant_id = $("#restaurant_id").val();
  var user_id = $("#user_id").val();
  var totalPrice = $('#subTotal1').val();
  //addons category mandatory changes :: start
  var addons_mandatory = JSON.parse(mandatory_arr);
  var checked_mandatory = 'no';
  var cat_name_str = '';
  if(addons_mandatory) {
    for(let i=0; i<addons_mandatory.length; i++){
      var id_val = addons_mandatory[i];
      var get_category_elements = document.querySelectorAll('[addons_category_id1="'+id_val+'"]');
      
      if(get_category_elements[0].type == 'radio') { //for radio
          var radioValue = $("input[name='"+get_category_elements[0].name+"']:checked").val(); 
          if(radioValue){
            checked_mandatory = 'yes';
          } else {
            checked_mandatory = 'no';
          }
      } else { //for checkbox
          checked_mandatory = 'no';
          for (let j = 0; j < get_category_elements.length; j++) {
            if(get_category_elements[j].checked){
              checked_mandatory = 'yes';
            }
          }
      }
      if(checked_mandatory == 'no'){
        cat_name_str = document.getElementsByName(get_category_elements[0].name)[0].getAttribute("addons_category");
        break;
      }
    }
  }
  //addons category mandatory changes :: end
  var qtyval = $('#qty'+item_id).val();
  if (qtyval != '' && (parseInt(qtyval) > 0))
  {
  }
  else
  {
    var existingStyles = $('#qty'+item_id).attr("style");
    $('#qty'+item_id).attr("style",existingStyles+"border:1px solid red !important;");
    //alert('quantity required');
    return false;
  }

  if((mandatory==0 && qtyval != '' && (parseInt(qtyval) > 0)) || (mandatory==1 && checked_mandatory=='yes' && totalPrice>0 && qtyval != '' && (parseInt(qtyval) > 0)))
  { 
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'cart/checkCartRestaurant',
      data : {"restaurant_id":restaurant_id},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if (response == 0) {
          // another restaurant
          $('#menuDetailModal').modal('hide');
          $('#addonsMenuDetailModal').modal('hide');
          $('#rest_entity_id').val(entity_id);
          $('#rest_restaurant_id').val(restaurant_id);
          $('#rest_is_addon').val(is_addon);
          $('#item_id').val(item_id);
          $('#is_closed1').val(is_closed);
          $('#anotherRestModal').modal('show');
        }
        if (response == 1) {
          customMenuDetails(entity_id,restaurant_id,item_id,is_closed,mandatory,recipe_page);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  } else {
    if(mandatory == 1){
      if(SELECTED_LANG == 'en') {
        bootbox.alert({
            message: "Please select atleast one item from "+ cat_name_str +".",
            buttons: {
                ok: {
                    label: "Ok",
                }
            }
        });
      } else if(SELECTED_LANG == 'fr') {
        bootbox.alert({
          message: "Veuillez sélectionner au moins un élément de "+ cat_name_str +".",
          buttons: {
            ok: {
                label: "D'accord",
            }
          }
        });
      } else {
        bootbox.alert({
          message: "يرجى تحديد عنصر واحد على الأقل من " +" "+cat_name_str+".",
          buttons: {
            ok: {
                label: "نعم",
            }
          }
        });
      }
    }
  }
}
//get item price on addons menu details popup
var totalPrice_addons = 0;
var radiototalPrice_addons = 0;
var checktotalPrice_addons = 0;
function getaddonsItemPrice(id,price,is_multiple,item_id)
{ 
    var qtyval = $('#qtyaddtocart-'+item_id).val(); 
    if(qtyval != '' && (parseInt(qtyval) > 0)){
    }
    else
    {
      $('#qtyaddtocart-'+item_id).val(1);
      var qtyval = 1;
    }

    if (is_multiple != 1) {
      radiototalPrice_addons = 0;
      //$("#custom_items_form1 input[type=radio]:checked").each(function() { 
      $("input:radio.radio_addons1:checked").each(function() {  
        var sThisVal = (this.checked ? $(this).attr("amount1") : 0);
        radiototalPrice_addons = parseFloat(radiototalPrice_addons) + parseFloat(sThisVal);
      });
    }
    else
    {
      checktotalPrice_addons = 0;
      $('.check_addons1:checkbox:checked').each(function () { 
        var sThisVal = (this.checked ? $(this).attr("amount1") : 0);
        checktotalPrice_addons = parseFloat(checktotalPrice_addons) + parseFloat(sThisVal);        
      });
    }
    totalPrice_addons = radiototalPrice_addons + checktotalPrice_addons;
    var total_display = $('#subTotal_for_cal').val();
    totalPrice_addons = totalPrice_addons + parseFloat(total_display);
    totalPrice_addons = parseFloat(totalPrice_addons)*parseFloat(qtyval)
    $('#totalPrice1').html(totalPrice_addons.toFixed(2));
    $('#subTotal1').val(totalPrice_addons.toFixed(2));
}
// check the item in cart if it's already added : only for addons items
function customMenuDetails(entity_id,restaurant_id,item_id,is_closed="",mandatory,recipe_page){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkMenuItem' ,
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 1) {
        $('#con_entity_id1').val(entity_id);
        $('#con_restaurant_id1').val(restaurant_id);
        $('#con_item_id1').val(item_id);
        $('#is_closed1').val(is_closed);
        $('#con_item_mandatory').val(mandatory);
        $('#addonsMenuDetailModal').modal('hide');
        $('#myconfirmModalDetails').modal('show');
      }
      else
      {
        AddAddonsToCartDetails(entity_id,item_id,mandatory,recipe_page);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
function AddAddonsToCartDetails(menu_id,item_id,mandatory,recipe_page){ 
    var restaurant_id = $("#restaurant_id").val();
    var user_id = $("#user_id").val();
    var totalPrice1 = ($('#subTotal1').val()=='0')? $('#subTotal_for_cal').val() : $('#subTotal1').val();
    var valueArray = new Array();
    $('.check_addons1:checkbox:checked').each(function () { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id1");    
      if (valueArray.length > 0) {
        jQuery.each( valueArray, function( key, value ) { 
          var new_addons_list = new Array();
          if (value.addons_category_id == addons_category_id) {
            var addonslist = value.addons_list;
            if (Array.isArray(value.addons_list) == false) {
              new_addons_list.push({
                "add_ons_id": value.addons_list.add_ons_id, 
                "add_ons_name": value.addons_list.add_ons_name,
                "add_ons_price": value.addons_list.add_ons_price
              });
            }
            else
            { 
              if (addonslist.length > 0) {
                jQuery.each(addonslist, function( key, value ) { 
                  new_addons_list.push(value);
                });
              } 
            }
            new_addons_list.push(addonValue);
            value.addons_list = new_addons_list;
          }
          else
          {
            valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
            });
          }
        });
      }
      else
      {
        valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
        });
      }
    });
    $("#custom_items_form1 input[type=radio][class='radio_addons1']:checked").each(function() { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id1");
      var new_addons_list = new Array();
      if (valueArray.length > 0) { 
        jQuery.each( valueArray, function( key, value ) {
          if (value.addons_category_id == addons_category_id) {
            new_addons_list.push(value.addons_list);
            new_addons_list.push(addonValue);
            valueArray.splice(key, 1);
          }
        });
      }
      if (new_addons_list.length > 0) {
        addonValue = new_addons_list;
      }
      valueArray.push({
            'addons_category_id':addons_category_id,
            'addons_category':addons_category,
            'addons_list':addonValue
      });
    });
    var arr = [];
    var addons_category_id_arr = [];
    if (valueArray.length > 0) { 
      jQuery.each( valueArray, function( key, value ) { 
        var addons = value.addons_list;
        var addons_count = addons.length;
        addons_category_id_arr.push(value.addons_category_id);
        arr.push({
          'addons_category_id':value.addons_category_id,
          'key':key,
          'addons_count':(addons_count)?addons_count:0
        });
      });
    }
    var unique_addons_category = [];
    $.each(addons_category_id_arr, function(i, el){
        if($.inArray(el, unique_addons_category) === -1) unique_addons_category.push(el);
    });
    var maxval = [];
    var arrkeys = [];
    if (unique_addons_category.length > 0) {
      jQuery.each( unique_addons_category, function( key, value ) {
        var max = 0;
        var keyvalue = '';
        if (arr.length > 0) {
          jQuery.each( arr, function( arrkey, arrvalue ) {
            if (arrvalue.addons_category_id == value) {
              if(max <= arrvalue.addons_count){
                max = arrvalue.addons_count;
                keyvalue = arrvalue.key;
              }
            }
          });
          maxval.push({ 
            'id':value,
            'addons_count': max,
            'key': keyvalue
          });
          arrkeys.push(keyvalue);
        }
      });
    }
    var finalValueArray = [];
    // to unset the duplicate keys
    if (valueArray.length > 0) {
      jQuery.each( valueArray, function( key, value ) {
        if (arrkeys.length > 0) {
          if(jQuery.inArray(key, arrkeys) !== -1) { 
            finalValueArray.push(value);
          }
        }
        else
        {
          finalValueArray = valueArray;
        }
      });
    }
    var qtyval = $('#qty'+item_id).val(); 
    if (mandatory==0 || (mandatory==1 && finalValueArray.length > 0)) {
      jQuery.ajax({
        type : "POST",
        url : BASEURL+'cart/addToCart',
        data : {'menu_id_m':menu_id,'user_id':user_id,'restaurant_id':restaurant_id,'totalPrice':totalPrice1,'add_ons_array':finalValueArray,'qtyval':qtyval},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
          /*$("body").load('#cart >*',function(){
            $('#quotes-main-loader').hide();
          });*/
          if (recipe_page=='recipe'){
            window.location.replace(BASEURL+'cart');
          }else if (recipe_page=='checkout_as_guest'){
            window.location.replace(BASEURL+'checkout/checkout_as_guest');
          }
          $('#quotes-main-loader').hide();
          $('#myModal').modal('hide');
          $('#addonsMenuDetailModal').modal('hide');
          
          $('#your_cart').html(response);
          $('.'+item_id).html(ADDED);
          $('.'+item_id).removeClass('add');
          $('.'+item_id).addClass('added');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
      });
   } else {
      if(mandatory==1) {
        if(SELECTED_LANG == 'en') {
          bootbox.alert({
              message: "Please select atleast one item.",
              buttons: {
                  ok: {
                      label: "Ok",
                  }
              }
          });
        } else if(SELECTED_LANG == 'fr') {
          bootbox.alert({
            message: "Veuillez sélectionner au moins un élément.",
            buttons: {
              ok: {
                  label: "D'accord",
              }
            }
          });
        } else {
          bootbox.alert({
            message: "الرجاء تحديد عنصر واحد على الأقل.",
            buttons: {
              ok: {
                  label: "نعم",
              }
            }
          });
        }
      }
    }
}
function ConfirmCartAddDetails(recipe_page){
  var entity_id = $('#con_entity_id1').val();
  var restaurant_id = $('#con_restaurant_id1').val();
  var item_id = $('#con_item_id1').val();
  var mandatory = $('#con_item_mandatory').val();
  var cart = $('input[name="addedToCart1"]:checked').val();
  var qtyval = $('#qty'+item_id).val();
  $('#myconfirmModalDetails').modal('hide');
  if (cart == "increaseitem1") {
    customItemCount(entity_id,restaurant_id,'plus','',recipe_page,qtyval);
  }
  else
  {
    AddAddonsToCartDetails(entity_id,item_id,mandatory,recipe_page);
  }
  return false;
}
//new changes for menu details on image click :: end
function ViewRecipe(menu_item_id){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/viewRecipe',
    data : {"menu_item_id":menu_item_id},
    beforeSend: function(){
        //$('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
    window.open(BASEURL+'recipe/recipe-detail/'+response,"_self");
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
//driver tip changes :: start (checkout)
function applyTip(action) {
  var tip_amount = $('#driver_tip').val();
  var tip_percent_val = $('.tip_selected').attr("data-val");
  tip_amount = parseFloat(tip_amount);
  var payment_optionval = $("input[name='payment_option']:checked").val();
  if(tip_amount>0){
    jQuery.ajax({
      type : "POST",
      dataType : 'html',
      url : BASEURL+'checkout/applyTip',
      data : {"tip_amount":tip_amount,"tip_percent_val":tip_percent_val,"action":action,"payment_optionval":payment_optionval},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('#ajax_order_summary').html(response);
        if(action=='apply'){
          $('#tip_submit_btn').attr('disabled',false);
          $("#tip_clear_btn").attr("disabled", false);
        } else {
          $('#driver_tip').val('');
          $('#tip_submit_btn').attr('disabled',true);
          $("#tip_clear_btn").attr("disabled", true);
          $("#custom_tip").val(null);
          $(".tip_row a").removeClass("tip_selected");
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  } else {
    $("#custom_tip_error").html(tip_greaterthan_zero);
    $("#custom_tip_error").show();
    $('#tip_submit_btn').attr('disabled',true);
    $("#tip_clear_btn").attr("disabled", true);
  }
}
function tip_selected(tip_amount,id_selected,currency_symbol='') {
 $("#custom_tip_error").hide();
  if(id_selected=='custom_tip'){
    if($("#"+id_selected).val() !=""){
      //if (/^\d{0,4}(\.\d{1,2})?$/.test($("#"+id_selected).val())) {
      // it allow 2 decimal points only
      if (/^(?:\d*\.\d{1,2}|\d+)$/.test($("#"+id_selected).val())) {
        $('#driver_tip').val(tip_amount);
        $('#display_tip').text(currency_symbol+tip_amount);
        $(".tip_row a").removeClass("tip_selected");
        if(id_selected!='custom_tip'){
          $('#'+id_selected).addClass('tip_selected');
          $("#custom_tip").val('');
        } else {
          $("#custom_tip").val(parseFloat(tip_amount));
        }
      
        $('#tip_submit_btn').attr('disabled',false);
        $("#tip_clear_btn").attr("disabled", false);
      }
      else {
        $("#custom_tip_error").html(custom_tip_decimal_error);
        $("#custom_tip_error").show();
        $('#tip_submit_btn').attr('disabled',true);
        $("#tip_clear_btn").attr("disabled", true);
      }
    }
  }  
  else {
    if(tip_amount){
      $('#driver_tip').val(tip_amount);
      $('#display_tip').text(currency_symbol+tip_amount);
      $(".tip_row a").removeClass("tip_selected");
      if(id_selected!='custom_tip'){
        $('#'+id_selected).addClass('tip_selected');
        $("#custom_tip").val('');
      } else {
        $("#custom_tip").val(parseFloat(tip_amount));
      }
    
      $('#tip_submit_btn').attr('disabled',false);
      $("#tip_clear_btn").attr("disabled", false);
    } else {
      $('#tip_submit_btn').attr('disabled',true);
      $("#tip_clear_btn").attr("disabled", true);
    }
  }
}
function handleDriverTip(call_from='') {
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/manage_driver_tip',
    data : {"call_from":call_from},
    beforeSend: function(){
      $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#driver-tip-form').html(response.ajax_driver_tips);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
//driver tip changes :: end (checkout)
//re-order changes :: start
function reorder_details(order_id){
  if (order_id) {
    jQuery.ajax({
      type : "POST",
      dataType : "html",
      url : BASEURL+'myprofile/getReOrderDetails',
      data : {"order_id":order_id},
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        $('#reorder-details').html(response);
        $('#reorder-details').modal('show');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  }
}
//check if cart has items
function checkCartOnReorder(restaurant_id, menu_arr){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkCartOnReorder',
    beforeSend: function(){
      $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      var user_id = $("#user_id").val();
      var menuDetailsArray = new Array();
      if(menu_arr.length>0){
        for (var i = 0; i < menu_arr.length; i++) {
          var itemTotal = jQuery.parseJSON(menu_arr[i].itemTotal);
          var menu_id = jQuery.parseJSON(menu_arr[i].menu_id);
          if(menu_arr[i].addonValue !=''){
            var addonValue = jQuery.parseJSON(menu_arr[i].addonValue);
          } else {
            var addonValue = '';
          }
          menuDetailsArray.push({
            'menu_id':menu_id,
            'menu_qty':menu_arr[i].menu_qty,
            'comment':menu_arr[i].comment,
            'itemTotal':itemTotal,
            'is_addon': menu_arr[i].is_addon,
            'addons_category_list':addonValue
          });
        }
      }
      if (response == 0) {
        //cart empty : proceed with addToCart
        addReorderItemsToCart(menuDetailsArray,restaurant_id,user_id);
      }else if (response == 1) {
        //cart not empty :: display modal
        $('#menuDetailsArray').val(JSON.stringify(menuDetailsArray));
        $('#rest_restaurant_id').val(restaurant_id);
        $('#rest_user_id').val(user_id);
        $('#reorder-details').modal('hide');
        $('#cartNotEmpty').modal('show');
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
function ConfirmCartItemsOnReorder(){
  var menuDetailsArray = JSON.parse($('#menuDetailsArray').val());
  var restaurant_id = $('#rest_restaurant_id').val();
  var user_id = $('#rest_user_id').val();
  var items = $('input[name="addNewItems"]:checked').val();
  $('#cartNotEmpty').modal('hide');
  if (items == "discardOld") {
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'cart/emptyCart',
      success: function(response) { 
        addReorderItemsToCart(menuDetailsArray,restaurant_id,user_id);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  }
  return false;
}
function addReorderItemsToCart(menuDetailsArray,restaurant_id,user_id) {
  jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'cart/addReorderItemsToCart',
    data : {'menuDetailsArray':menuDetailsArray,'user_id':user_id,'restaurant_id':restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      $('#reorder-details').modal('hide');
      $('#cart_count').html(response.cart_count);
      if(response.show_message != ''){
        var box = bootbox.alert({
          message: response.show_message,
          buttons: {
              ok: {
                  label: response.oktxt,
              }
          },
          callback: function () {
            if(response.cart_count>0) {
              window.location.href = BASEURL+"cart";
            }
          }
        });
        setTimeout(function() {
          box.modal('hide');
          if(response.cart_count>0) {
            window.location.href = BASEURL+"cart";
          }
        }, 10000);
      } else {
        if(response.cart_count>0) {
          window.location.href = BASEURL+"cart";
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
}
//re-order changes :: end
//cancel order changes :: start
function cancel_order(order_id){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            dataType : 'json',
            url : BASEURL+'myprofile/getCancelOrderReasons',
            data : {"order_id":order_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                if(response.is_cancel_order == 'yes'){
                  $('#cancel-order').html(response.ajax_cancel_reason);
                  $('#cancel-order').modal('show');
                } else {
                  var cancel_box = bootbox.alert({
                    message: response.cancel_msg,
                    buttons: {
                        ok: {
                            label: response.oktxt,
                        }
                    },
                  });
                  setTimeout(function() {
                    cancel_box.modal('hide');
                  }, 10000);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
function cancel_order_reason(order_id,entity_id){
  var reason_id ="";
  var reason = "";
  var element = $("input[name='filter_reason']:checked").val(); 
  if(element != 'all'){
    var reason = element;
  }else{
    var reason = $('#other_reason').val();
  }
  if($('#all').is(':checked')){
    $("#cancel_reason_form").validate({
      rules: { 
          other_reason: {
            required: true,
            maxlength:255
          },
      } ,
      errorElement : 'div',
      errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error);
      } else {
        error.insertAfter(element);
        }
      }  
    });
  }
  if(($('#all').is(':checked') && $("#other_reason").valid()==true) || ($('#all').is(':checked')==false)){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "json",
            url : BASEURL+'myprofile/OrderCancel',
            data : {"order_id":order_id,"reason":reason,"user_id":entity_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
              $('#quotes-main-loader').hide();
              $('#cancel-order').modal('hide');
              if(response.is_cancel_order == 'yes' && (response.error!='')){
                var cancel_box = bootbox.alert({
                  message: ORDER_CANCELED_REFUNDED,
                  buttons: {
                    ok: {
                        label: OK_TEXT,
                    },
                  },
                  callback: function(result){
                    location.href = BASEURL+'myprofile';
                  }
                });
                setTimeout(function() {
                  cancel_box.modal('hide');
                  location.href = BASEURL+'myprofile';
                }, 80000);
              }else if(response.error_message!=''){
                var refundbox = bootbox.alert({
                  message: response.error_message,
                  buttons: {
                      ok: {
                          label: OK_TEXT,
                      }
                  }
                });
                setTimeout(function() {
                  refundbox.modal('hide');
                }, 10000);
              }else if(response.is_cancel_order == 'yes'){
                var cancel_box = bootbox.alert({
                  message: ORDER_CANCELED,
                  buttons: {
                    ok: {
                        label: OK_TEXT,
                    },
                  },
                  callback: function(result){
                    location.href = BASEURL+'myprofile';
                  }
                });
                setTimeout(function() {
                  cancel_box.modal('hide');
                  location.href = BASEURL+'myprofile';
                }, 80000);
              } else {
                var cancel_box = bootbox.alert({
                  message: response.cancel_msg,
                  buttons: {
                    ok: {
                        label: response.oktxt,
                    }
                  },
                  callback: function(result){
                    location.href = BASEURL+'myprofile';
                  }
                });
                setTimeout(function() {
                  cancel_box.modal('hide');
                  location.href = BASEURL+'myprofile';
                }, 10000);
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    }
  } 
  return false;
}
//cancel order changes :: end
// get more notifications
function moreNotifications(){
  $('#all_notifications').show();
  $('#load_more_notifications').hide();
}
/*table booking page js*/
function searchTables(page,err_msg,oktext){
    var searchTable = $('#searchTable').val();
    if ($.trim(searchTable) == '' || searchTable == undefined) {
    {
      var box = bootbox.alert({
            message: err_msg,
            buttons: {
                ok: {
                    label: oktext,
                }
            }
          });
          setTimeout(function() {
            box.modal('hide');
          }, 10000);
      }
    }
    else{
      jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : BASEURL+"restaurant/ajax_table_booking",
      data : {'searchTable':searchTable,'page':''},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#sort_tables').html(response);
        $('#quotes-main-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
      });
    }
  }
  $('#searchEvent').keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          event.preventDefault();
      }
  });
/*table booking page js ends*/
// table check availability submit
$("#check_table_availability").on("submit", function(event) {
  if ($("#check_table_availability").valid()) {
    var validator = $("#check_table_availability").validate();
    event.preventDefault();
      jQuery.ajax({
        type : "POST",
        url : BASEURL+'restaurant/checkTableAvailability',
        data : $('#check_table_availability').serialize(),
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
          var response = JSON.parse(response);
          if (response.hasOwnProperty('incorrect_info')) {
            var box = bootbox.alert({
              message: response.show_message,
              buttons: {
                  ok: {
                      label: response.oktxt,
                  }
              },
            });
            setTimeout(function() {
              box.modal('hide');
            }, 10000);
          }
          if (response.hasOwnProperty('allow_table_booking')) {
            bootbox.confirm({ 
              message: response.allow_table_booking_text,
              buttons: {
                confirm: {
                  label: response.oktxt
                },
                cancel: {
                  label: response.canceltxt
                }
              },
              callback: function(result){
                if (result === true) {
                  location.reload();
                }
              }
            })
          }
          if(!response.hasOwnProperty('result') && (response.hasOwnProperty('more_capacity')) && response.hasOwnProperty('restaurant_capacity')){
            $('#booking-not-available-capicity').modal('show');
            $('#less').removeClass('display-yes');
            $('#less').addClass('display-no');
            $('#more').addClass('display-yes');
            $('#more').removeClass('display-no');
            $('#booking-not-available-capicity span').text(response['restaurant_capacity']);
          }
          if(!response.hasOwnProperty('result') && (response.hasOwnProperty('less_capacity')) && response.hasOwnProperty('restaurant_capacity')){
            $('#booking-not-available-capicity').modal('show');
            $('#more').removeClass('display-yes');
            $('#more').addClass('display-no');
            $('#less').removeClass('display-no');
            $('#less').addClass('display-yes');
            $('#booking-not-available-capicity span').text(response['restaurant_capacity']);
          }
          if (response.hasOwnProperty('result')) {
            if(response['result'] == "success"){
              $('#table-booking-available').modal('show');
            }
            if(response['result'] == "fail"){
              $('#booking-not-available').modal('show');
            }
          }
          if(response.hasOwnProperty('start_time_less') && response.hasOwnProperty('start_time_less_html')) {
            $('#start_time_less').html(response['start_time_less_html']);
            $('#booking-not-available-capicity').modal('show');
            $('#less').removeClass('display-yes');
            $('#less').addClass('display-no');
            $('#more').removeClass('display-yes');
            $('#more').addClass('display-no');
          }
          $('#quotes-main-loader').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
        }); 
      return false;
    }
});
// confirm table booking
function confirmTableBooking(){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/bookTable',
    data : $('#check_table_availability').serialize(),
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      var response = JSON.parse(response);
      if (response.hasOwnProperty('incorrect_info')) {
        $('#booking-available').modal('hide');
        var box = bootbox.alert({
          message: response.show_message,
          buttons: {
              ok: {
                  label: response.oktxt,
              }
          },
        });
        setTimeout(function() {
          box.modal('hide');
        }, 10000);
      }
      if (response.hasOwnProperty('allow_table_booking')) {
        $('#booking-available').modal('hide');
        bootbox.confirm({ 
          message: response.allow_table_booking_text,
          buttons: {
            confirm: {
              label: response.oktxt
            },
            cancel: {
              label: response.canceltxt
            }
          },
          callback: function(result){
            if (result === true) {
              location.reload();
            }
          }
        })
      }
      if (response.hasOwnProperty('result')) {
        if(response['result'] == "success"){
          $('#table-booking-confirmation').modal('show');
        }
        if(response['result'] == "fail"){
          $('#booking-not-available').modal('show');
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
    return false;
}
// get table booking details
function table_booking_details(table_id){
    if (table_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+ 'myprofile/getTableBookingDetails',
            data : {"table_id":table_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#table-booking-details').html(response);
                $('#table-booking-details').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// get more table bookings
function moreTables(order_flag){
    if (order_flag == "upcoming") {
        $('#all_upcoming_tables').show();
        $('#more_upcoming_tables').hide();
    }
    if (order_flag == "past") {
        $('#all_past_tables').show();
        $('#more_past_tables').hide();
    }
}
// js location function for checkout page
function showPositionCheckout(position) {
  getAddress(position.coords.latitude,position.coords.longitude,'checkout');
}
function locationFailCheckout() {
  /*getFavouriteResturants('');
  $('#distance_filter').hide();
  $('#distance_sort').hide();*/
}
//restaurant sort/filter section :: start
function getRestaurantsOnFilter(action,filter_sort = '',sort_heading_txt='',quicksearch_val = ''){
  if(action == 'apply'){
    if(filter_sort == 'quicksearch_foodtype') {
      $('select.food_type')[0].sumo.unSelectAll();
      if($('#foodtype_'+quicksearch_val).hasClass('selected')) {
        $('#foodtype_'+quicksearch_val).removeClass('selected');
        $('#foodtype_'+quicksearch_val).removeClass('borderClass');
      } else {
        $('#foodtype_'+quicksearch_val).addClass('selected');
        $('#foodtype_'+quicksearch_val).addClass('borderClass');
      }
    }
    var foodtype_quicksearch = [];
    $('.quick-searches-box.selected').each(function(){
      var quicksearch = $(this).find("input[name=quicksearch_foodtype]").val();
      foodtype_quicksearch.push(quicksearch);
    });
    var listed_foodtype = [];
    $('.quick-searches-box').each(function(){
      listed_foodtype.push($(this).find("input[name=quicksearch_foodtype]").val());
    });
    var resdishes = $('#resdishes').val();
    var order_mode = $('#order_mode').val();
    var latitude = $('#latitude').val();
    var longitude = $('#longitude').val();
    var minimum_range = ($('#minimum_range').val()) ? $('#minimum_range').val() : '';
    var maximum_range = ($('#maximum_range').val()) ? $('#maximum_range').val() : '';
    var filter_by = $("input[name='filter_by']:checked").val();
    var offers_free_delivery = $("input[name='offers_free_delivery']:checked").val();
    offers_free_delivery = (offers_free_delivery == '1') ? 1 : 0;
    var availability_filter = [];
    $("input[name='availability_filter']:checkbox:checked").each(function(i){
      availability_filter.push($(this).val());
    });
    var category_id = [];
    $('#category_id option:selected').each(function(i) {
        category_id.push($(this).val());
    });
    var food_type = [];
    $('#food_type option:selected').each(function(i) {
        food_type.push($(this).val());
    });    
    //range-slider :: start
    var moneyFormat = wNumb({
      decimals: 0,
      thousand: ',',
      prefix: ''
    });
    if(order_mode == 'PickUp') {
      var rangeSlider = document.getElementById('slider-range');
      var range_values = rangeSlider.noUiSlider.get();
      maximum_range = (maximum_range == '') ? maximum_range_pickup_for_slider : maximum_range;
      maximum_range = (maximum_range > maximum_range_pickup_for_slider) ? maximum_range_pickup_for_slider : maximum_range;
      minimum_range = (minimum_range == '') ? 0 : minimum_range;
      if(range_values[1] != maximum_range_pickup_for_slider) {
        rangeSlider.noUiSlider.updateOptions({
          range: {
            'min': 0,
            'max': maximum_range_pickup_for_slider
          }
        });
      }
      var new_range_values = rangeSlider.noUiSlider.get();
      $('#minimum_range').val(new_range_values[0]);
      $('#maximum_range').val(new_range_values[1]);
      var sliderpercentmaxval = (new_range_values[1] * 100) / maximum_range_pickup_for_slider;
      sliderpercentmaxval = sliderpercentmaxval+'%';
      var sliderpercentminval = (new_range_values[0] * 100) / maximum_range_pickup_for_slider;
      sliderpercentminval = sliderpercentminval+'%';
      $(".noUi-origin.noUi-background").css('left',sliderpercentmaxval);
      $(".noUi-origin.noUi-connect").css('left',sliderpercentminval);

      document.getElementById('slider-range-value1').innerHTML = new_range_values[0]+' Miles';
      document.getElementById('slider-range-value2').innerHTML = new_range_values[1]+' Miles';
      document.getElementsByName('min-value').value = moneyFormat.from(new_range_values[0]);
      document.getElementsByName('max-value').value = moneyFormat.from(new_range_values[1]);
    } else {
      var rangeSlider = document.getElementById('slider-range');
      var range_values = rangeSlider.noUiSlider.get();
      maximum_range = (maximum_range == '') ? maximum_range_for_slider : maximum_range;
      maximum_range = (maximum_range > maximum_range_for_slider) ? maximum_range_for_slider : maximum_range;
      minimum_range = (minimum_range == '') ? 0 : minimum_range;
      if(range_values[1] != maximum_range_for_slider) {
        rangeSlider.noUiSlider.updateOptions({
          range: {
            'min': 0,
            'max': maximum_range_for_slider
          }
        });
      }
      var new_range_values = rangeSlider.noUiSlider.get();
      $('#minimum_range').val(new_range_values[0]);
      $('#maximum_range').val(new_range_values[1]);
      var sliderpercentmaxval = (new_range_values[1] * 100) / maximum_range_for_slider;
      sliderpercentmaxval = sliderpercentmaxval+'%';
      var sliderpercentminval = (new_range_values[0] * 100) / maximum_range_for_slider;
      sliderpercentminval = sliderpercentminval+'%';
      $(".noUi-origin.noUi-background").css('left',sliderpercentmaxval);
      $(".noUi-origin.noUi-connect").css('left',sliderpercentminval);

      document.getElementById('slider-range-value1').innerHTML = new_range_values[0]+' Miles';
      document.getElementById('slider-range-value2').innerHTML = new_range_values[1]+' Miles';
      document.getElementsByName('min-value').value = moneyFormat.from(new_range_values[0]);
      document.getElementsByName('max-value').value = moneyFormat.from(new_range_values[1]);
    }
    //range-slider :: end
    jQuery.ajax({
      type : "POST",
      dataType :"json",
      url: BASEURL+'home/getRestaurantsOnFilter',
      data : {'latitude':latitude,'longitude':longitude,'resdishes':$.trim(resdishes),'minimum_range':minimum_range,'maximum_range':maximum_range,'food_type': food_type.join(),'filter_by': filter_by,'order_mode':order_mode,'foodtype_quicksearch': foodtype_quicksearch.join(),'category_id': category_id.join(),'listed_foodtype':listed_foodtype.join(),'offers_free_delivery':offers_free_delivery,'availability_filter':availability_filter.join()},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) { 
        var rtl = (SELECTED_LANG == 'ar')?true:false;
        $('#popular-restaurants').html(response.popular_restaurants);
        $('#foodtype_quicksearch').html(response.quick_searches);
        if(response.countcoupon && response.countcoupon>0)
        {
          $('#coupon_section').css('display','');
          $('#coupon_section').html(response.coupon_section_html);
        }
        else
        {
          $('#coupon_section').css('display','none');
          $('#coupon_section').html('');
        }
        $('.food_type').empty().append(response.foodtype_dropdown);
        $('select.food_type')[0].sumo.reload();
        if(foodtype_quicksearch.length != 0 || food_type.length != 0) {
          if(food_type.length != 0) {
            $.each(food_type, function(ftindex, ftvalue) {
              $('select.food_type')[0].sumo.selectItem(ftvalue);
            });
          } else {
            $.each(foodtype_quicksearch, function(ftindex, ftvalue) {
              $('select.food_type')[0].sumo.selectItem(ftvalue);
            });
          }
        }
        if(response.quick_searches != '') {
          $('.quick-searches-slider').owlCarousel({
            loop: true,
            nav: true,
            rtl:rtl,
            autoplay: true,
            autoplayTimeout:3500,
            navSpeed:1300,
            touchDrag:true,
            slideBy:2,
            autoplaySpeed:1300,
            autoplayHoverPause: true,
            navContainer: '#customNav',
            responsive: {
              0: {
                items: 1,
              },
              480: {
                items: 2,
              },
              600: {
                items: 3,
              },
              1000: {
                items: 5,
              },
              1300: {
                items: 6,
              },
              1550: {
                items: 7
              }
            }
          });
        }
        if(response.coupon_section_html != '') {
          $('.best-offers-slider').owlCarousel({
            loop: true,
            nav: true,
            autoplay: true,
            rtl:rtl,
            autoplayTimeout:3500,
            navSpeed:1300,
            autoplaySpeed:1300,
            autoplayHoverPause: true,
            navContainer: '#customNav2',
            responsive: {
              0: {
                items: 1,
              },
              480: {
                items: 2,
              },
              600: {
                items: 3,
              },
              1550:{
                items: 4
              }
            }
          });
        }
        if($('.res-coupons-slider').length){
          $(".variable").slick({
            dots: false,
            infinite: true,
            autoplay: true,
            variableWidth: true,
            arrow: false,
            autoplaySpeed: 0,
            speed: 8000,
            pauseOnHover: false,
            cssEase: 'linear'
          });
        }
        $('html, body').animate({
            scrollTop: $(".popular-restaurants").offset().top
        }, 2000);
        if(filter_by != '' && filter_by != undefined && sort_heading_txt != '' && offers_free_delivery == 1 && availability_filter.length > 0) {
          filter_by = filter_by.charAt(0).toUpperCase() + filter_by.slice(1);
          $('#sort_heading_txt span').text(sort_heading_txt+filter_by+', '+freedelivery_offer+', '+availability_filter_txt);
          $('#sort_heading_txt').attr('title', sort_heading_txt+filter_by+', '+freedelivery_offer+', '+availability_filter_txt);
        } else if(filter_by != '' && filter_by != undefined && sort_heading_txt != '' && offers_free_delivery == 1) {
          filter_by = filter_by.charAt(0).toUpperCase() + filter_by.slice(1);
          $('#sort_heading_txt span').text(sort_heading_txt+filter_by+', '+freedelivery_offer);
          $('#sort_heading_txt').attr('title', sort_heading_txt+filter_by+', '+freedelivery_offer);
        } else if(filter_by != '' && filter_by != undefined && sort_heading_txt != '' && availability_filter.length > 0) {
          filter_by = filter_by.charAt(0).toUpperCase() + filter_by.slice(1);
          $('#sort_heading_txt span').text(sort_heading_txt+filter_by+', '+availability_filter_txt);
          $('#sort_heading_txt').attr('title', sort_heading_txt+filter_by+', '+availability_filter_txt);
        } else if(sort_heading_txt != '' && availability_filter.length > 0 && offers_free_delivery == 1) {
          $('#sort_heading_txt span').text(sort_heading_txt+freedelivery_offer+', '+availability_filter_txt);
          $('#sort_heading_txt').attr('title', sort_heading_txt+freedelivery_offer+', '+availability_filter_txt);
        } else if(filter_by != '' && filter_by != undefined && sort_heading_txt != '') {
          filter_by = filter_by.charAt(0).toUpperCase() + filter_by.slice(1)
          $('#sort_heading_txt span').text(sort_heading_txt+filter_by);
          $('#sort_heading_txt').attr('title', sort_heading_txt+filter_by);
        } else if(offers_free_delivery == 1 && sort_heading_txt != '') {
          $('#sort_heading_txt span').text(sort_heading_txt+freedelivery_offer);
          $('#sort_heading_txt').attr('title', sort_heading_txt+freedelivery_offer);
        } else if(availability_filter.length > 0 && sort_heading_txt != '') {
          $('#sort_heading_txt span').text(sort_heading_txt+availability_filter_txt);
          $('#sort_heading_txt').attr('title', sort_heading_txt+availability_filter_txt);
        }
        $('#sort_heading_txt').addClass('collapsed');
        $('#collapseOne').removeClass('show');
        $('#quotes-main-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  } else if(action == 'clear'){
    $('#quotes-main-loader').show();
    if(filter_sort == 'res_search'){
      var resdishes = $('#resdishes').val('');
      if($('#resdishes').val()!=''){
        $('#for_res_search').show();
      } else {
        $('#for_res_search').hide();
      }
    }
    if(filter_sort == 'distance_rating'){
      $('#sort_heading_txt span').text(sort_heading_txt);
      $('#sort_heading_txt').attr('title', sort_heading_txt);
      $("input[name='offers_free_delivery']").prop('checked', false);
      $("input[name='availability_filter']").prop('checked', false);
      //reset slider : start
      var order_mode = $('#order_mode').val();
      var moneyFormat = wNumb({
        decimals: 0,
        thousand: ',',
        prefix: ''
      });
      $('#minimum_range').val(0);
      if(order_mode == 'PickUp') {
        var rangeSlider = document.getElementById('slider-range');
        var range_values = rangeSlider.noUiSlider.get();
        $('#maximum_range').val(maximum_range_pickup_for_slider);
        if(range_values[1] != maximum_range_pickup_for_slider) {
          rangeSlider.noUiSlider.updateOptions({
            range: {
              'min': 0,
              'max': maximum_range_pickup_for_slider
            }
          });
        }
        document.getElementById('slider-range-value2').innerHTML = maximum_range_pickup_for_slider+' Miles';
        document.getElementsByName('max-value').value = moneyFormat.from(maximum_range_pickup_for_slider);
      } else {
        var rangeSlider = document.getElementById('slider-range');
        var range_values = rangeSlider.noUiSlider.get();
        $('#maximum_range').val(maximum_range_for_slider);
        if(range_values[1] != maximum_range_for_slider) {
          rangeSlider.noUiSlider.updateOptions({
            range: {
              'min': 0,
              'max': maximum_range_for_slider
            }
          });
        }
        document.getElementById('slider-range-value2').innerHTML = maximum_range_for_slider+' Miles';
        document.getElementsByName('max-value').value = moneyFormat.from(maximum_range_for_slider);
      }
      document.getElementById('slider-range-value1').innerHTML = 0+' Miles';
      document.getElementsByName('min-value').value = moneyFormat.from(0);
      $(".noUi-origin.noUi-background").css('left','100%');
      $(".noUi-origin.noUi-connect").css('left','0%');
      $( ".value01" ).insertAfter( ".noUi-handle-lower" );
      $( ".value02" ).insertAfter( ".noUi-handle-upper" );
      //reset slider : end
      if($('input:radio[name=filter_by]').is(':checked')){
        $("input:radio[name=filter_by]:checked")[0].checked = false;
      }
    }
    setTimeout(function(){
      getRestaurantsOnFilter('apply');
    }, 500);
  }
}
//restaurant sort/filter section :: end
//clear search field function
function clearField(element_id,page,clear_icon_id,err_msg='') {
  $('#'+element_id).val('');
  if(element_id == 'address') {
    $('#latitude').val('');
    $('#longitude').val('');
    $('#distance_filter').hide();
    $('#distance_sort').hide();
  }
  if($('#'+element_id).val()!=''){
    $('#'+clear_icon_id).show();
  } else {
    $('#'+clear_icon_id).hide();
  }
  if(page=='home_page'){
    getPopularResturants('','','');
  } else if(page=='order_food'){
    getFavouriteResturants('');
  } else if(page=='event_booking'){
    searchEvents('restaurant',err_msg,OK_TEXT,'reset');
  }
}
function chkPaymentOptions(){
  var payment_optionval = $("input[name='payment_option']:checked").val();
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'checkout/removeCouponOptions',
    data : {"coupon_id":"","payment_optionval":payment_optionval,'call_from':'check_pay_options'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}

//Code for add/edit/remove stripe card :: Start
$( "#form_credit_card" ).on("submit", function( event ) {   
    if ($('#card_number').val() != '' && $('#card_month').val() != '' && $('#card_year').val() != '' && $('#card_cvv').val() != '' && $('#card_zip').val() != '') 
    {  
      if($('#form_credit_card').valid()){
        var formData = new FormData($("#form_credit_card")[0]);
        formData.append('submit_card', 'Save');
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'myprofile/save_stripecard',
            data : formData,
            dataType : 'json',
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                if (response == "success") {
                    $('#add-stripecard').modal('hide');
                    window.location.href = BASEURL+"myprofile/view_my_savecard";
                }
                else
                {
                  $('#quotes-main-loader').hide();
                  $('#carderrormsg').removeClass('display-no');
                  $('#carderrormsg').html(response.message);                  
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                //alert(errorThrown);
            }
        });
      }
    }
    event.preventDefault(); 
});
//Code for remove card from stripe :: Start
//show delete address popup
function showDeleteStipe(PaymentMethodid,stripecus_id)
{
    $('#delete_PaymentMethodid').val(PaymentMethodid);
    $('#delete_stripecus_id').val(stripecus_id);
    $('#delete-stripeaccount').modal('show');
}
$("#form_card_delete" ).on("submit", function( event ) { 
  event.preventDefault();
  var PaymentMethodid = $('#delete_PaymentMethodid').val();
  var stripecus_id = $('#delete_stripecus_id').val();
  removeStripeCard(PaymentMethodid,stripecus_id);
});
function removeStripeCard(PaymentMethodid,stripecus_id)
{
  $('#quotes-main-loader').show();
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'myprofile/removeStripeCard',
    data : {'PaymentMethodid':PaymentMethodid,'stripecus_id':stripecus_id},
    success: function(response) {           
          window.location.href = BASEURL+"myprofile/view_my_savecard";      
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      //alert(errorThrown);      
      $('#delete-stripeaccount').hide('show');
      $('#quotes-main-loader').hide();
    }
  });
}
function set_as_default_stripecard(PaymentMethodid,stripecus_id) {
  if(PaymentMethodid && stripecus_id) {
    $('#quotes-main-loader').show();
    jQuery.ajax({
      type : "POST",
      dataType : 'json',
      url : BASEURL+'myprofile/set_default_stripecard',
      data : {'PaymentMethodid':PaymentMethodid,'stripecus_id':stripecus_id},
      success: function(response) {           
        window.location.href = BASEURL+"myprofile/view_my_savecard";      
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        //alert(errorThrown);
        $('#quotes-main-loader').hide();
      }
    });
  }
}
function showeditcard()
{
  if($("input:radio[name='payment-source']").is(":checked"))
  {
    $('#form_credit_card')[0].reset();
    var element_radio = $("input[name='payment-source']:checked")                 
    var card_fingerprintval = element_radio.attr("card_fingerprint");
    var radio_paymentmethodid = element_radio.attr("paymentmethodid");

    var radio_exp_month = element_radio.attr("exp_month");
    var radio_exp_year = element_radio.attr("exp_year");
    var radio_postal_code = element_radio.attr("postal_code");
    var radio_card_last4 = element_radio.attr("card_last4");
    if(radio_exp_month<10)
    {
      radio_exp_month = '0'+radio_exp_month;
    }    
    $('#card_number').val("************"+radio_card_last4);
    $("#card_number").prop("readonly", true);
    $('#card_zip').val(radio_postal_code);
    $('#card_year').val(radio_exp_year);
    $('#card_month').val(radio_exp_month);
    $('#payment_method_id').val(radio_paymentmethodid);     
    $('#is_editcard').val('yes');
    $("#add_stripetitle").html(EDIT_CARDEXT);
    $('#add-stripecard').modal('show');
  }
  else
  {
    bootbox.alert({
        message: RADIO_CHECKTEXT,
        buttons: {
            ok: {
                label: OK_TEXT,
            }
        }
    });
  }  
}
//End
function cardFormValidate(){
    var cardValid = 0;
    //card number validation
    if($('#is_editcard').val() == 'no'){
      $('#card_number').validateCreditCard(function(result){
          if(result.valid){
              $("#card_number").removeClass('required');
              cardValid = 1;
          }else{
              $("#card_number").addClass('required');
              cardValid = 0;
          }
      });
    } else {
      cardValid = 1;
    }
    //card details validation
    var expMonth = $("#card_month").val();
    var expYear = $("#card_year").val();
    var cvv = $("#card_cvv").val();
    var regName = /^[a-z ,.'-]+$/i;
    var regMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
    var regYear = /^2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
    var regCVV = /^[0-9]{3,3}$/;
    if (cardValid == 0) {
        $("#card_number").addClass('required');
        //$("#card_number").focus();
        $("#card_number_err").text(VALID_CARD_NO);
        var card_validation_txt = $("#form_credit_card").find(`[for='card_number']`).is(':visible');
        if(card_validation_txt){
          $("#card_number_err").text('');
          $("#card_number_err").hide();
          $("#submit_card").attr("disabled", false);
        } else {
          if($("#card_number").val() != '') {
            $("#card_number_err").show();
            $("#submit_card").attr("disabled", true);
          } else {
            $("#card_number_err").text('');
            $("#card_number_err").hide();
            $("#submit_card").attr("disabled", false);
          }
        }
        return false;
    }else if (!regMonth.test(expMonth)) {
        $("#card_number").removeClass('required');
        $("#card_number_err").text('');
        $("#card_number_err").hide();

        $("#card_month").addClass('required');
        //$("#card_month").focus();
        $("#card_month_err").text(VALID_CARD_MONTH);
        var month_validation_txt = $("#form_credit_card").find(`[for='card_month']`).is(':visible');
        if(month_validation_txt){
          $("#card_month_err").text('');
          $("#card_month_err").hide();
          $("#submit_card").attr("disabled", false);
        } else {
          if($("#card_month").val() != '') {
            $("#card_month_err").show();
            $("#submit_card").attr("disabled", true);
          } else {
            $("#card_month_err").text('');
            $("#card_month_err").hide();
            $("#submit_card").attr("disabled", false);
          }
        }
        return false;
    }else if (!regYear.test(expYear)) {
        $("#card_number").removeClass('required');
        $("#card_number_err").text('');
        $("#card_number_err").hide();

        $("#card_month").removeClass('required');
        $("#card_month_err").text('');
        $("#card_month_err").hide();

        $("#card_year").addClass('required');
        //$("#card_year").focus();
        $("#card_year_err").text(VALID_CARD_YEAR);
        var year_validation_txt = $("#form_credit_card").find(`[for='card_year']`).is(':visible');
        if(year_validation_txt){
          $("#card_year_err").text('');
          $("#card_year_err").hide();
          $("#submit_card").attr("disabled", false);
        } else {
          if($("#card_year").val() != '') {
            $("#card_year_err").show();
            $("#submit_card").attr("disabled", true);
          } else {
            $("#card_year_err").text('');
            $("#card_year_err").hide();
            $("#submit_card").attr("disabled", false);
          }
        }
        return false;
    }else if (!regCVV.test(cvv)) {
        $("#card_number").removeClass('required');
        $("#card_number_err").text('');
        $("#card_number_err").hide();

        $("#card_month").removeClass('required');
        $("#card_month_err").text('');
        $("#card_month_err").hide();

        $("#card_year").removeClass('required');
        $("#card_year_err").text('');
        $("#card_year_err").hide();

        $("#card_cvv").addClass('required');
        //$("#card_cvv").focus();
        $("#card_cvv_err").text(VALID_CARD_CVV);
        var cvv_validation_txt = $("#form_credit_card").find(`[for='card_cvv']`).is(':visible');
        if(cvv_validation_txt){
          $("#card_cvv_err").text('');
          $("#card_cvv_err").hide();
          $("#submit_card").attr("disabled", false);
        } else {
          if($("#card_cvv").val() != '') {
            $("#card_cvv_err").show();
            $("#submit_card").attr("disabled", true);
          } else {
            $("#card_cvv_err").text('');
            $("#card_cvv_err").hide();
            $("#submit_card").attr("disabled", false);
          }
        }
        return false;
    } else {
        $("#card_number").removeClass('required');
        $("#card_number_err").text('');
        $("#card_number_err").hide();

        $("#card_month").removeClass('required');
        $("#card_month_err").text('');
        $("#card_month_err").hide();

        $("#card_year").removeClass('required');
        $("#card_year_err").text('');
        $("#card_year_err").hide();

        $("#card_cvv").removeClass('required');
        $("#card_cvv_err").text('');
        $("#card_cvv_err").hide();
        $("#submit_card").attr("disabled", false);
        return true;
    }
}
//Code for add/edit/remove stripe card :: End
//driver tip changes :: start (myprofile - past orders)
function tip_driver(order_id) {
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'myprofile/checkTipPaid',
    data : {"order_id":order_id},
    success: function(response) {
      if(response.tip_paid_status=='tip_paid'){
        var box = bootbox.alert({
          message: TIP_PAID,
          buttons: {
              ok: {
                  label: OK_TEXT,
              }
          },
          callback: function () {
            box.modal('hide');
            window.location.href = BASEURL+"myprofile";
          }
        });
        setTimeout(function() {
          box.modal('hide');
          window.location.href = BASEURL+"myprofile";
        }, 10000);
      }else{
        $('#driver-tip-form').html(response.ajax_driver_tips);
        $('#tip_order_id').val(order_id);
        $('#driver-tip').modal('show');
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
}
function applyTipForOrders(action,currency_symbol='',payment_option='')
{
  //Code for check the payment method checked or not :: Start
  if(!$('.payment_option').is(':checked') && action=='apply'){
    $('#blankmsg').html(field_required);
    $("#tip_clear_btn").attr("disabled", true);
    return false;
  }
  //Code for check the payment method checked or not :: End

  $("#custom_tip_error").hide();
  var tip_amount = $('#driver_tip').val();
  var tip_order_id = $('#tip_order_id').val();
  var tip_percent_val = $('.tip_selected').attr("data-val");
  tip_amount = parseFloat(tip_amount);
  if(tip_amount>0){
    jQuery.ajax({
      type : "POST",
      dataType : 'json',
      url : BASEURL+'myprofile/applyTipForOrders',
      data : {"tip_amount":tip_amount,"tip_percent_val":tip_percent_val,"action":action,"tip_order_id":tip_order_id,"payment_option":payment_option},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if(action=='apply')
        {
          $('#tip_submit_btn').attr('disabled',false);
          $("#tip_clear_btn").attr("disabled", false);          
          var payment_option = $("input[name='payment_option']:checked").val();
          $('#payment_option_val').val(payment_option);
          $('#driver-tip-form').addClass('display-no');
          $('.stripediv').removeClass('display-no');
          $('#listall_card').html(response.stripe_html);
          mount_stripe_element();
          
        } else {
          $('#driver_tip').val('');
          $('#display_tip').text(currency_symbol+0);
          $('#tip_submit_btn').attr('disabled',true);
          $("#tip_clear_btn").attr("disabled", true);
          $("#custom_tip").val(null);
          $(".tip_row a").removeClass("tip_selected");

          $('#driver-tip-form').removeClass('display-no');
          $('.stripediv').addClass('display-no');
          $('#drivertip_successmsg').html('');
          $('#drivertip_successmsg').addClass('display-no');
          $('#listall_card').html('');
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  } else {
    $("#custom_tip_error").html(tip_greaterthan_zero);
    $("#custom_tip_error").show();
    $('#tip_submit_btn').attr('disabled',true);
    $("#tip_clear_btn").attr("disabled", true);
  }
}
//driver tip changes :: end (myprofile - past orders)
//add wallet money :: start
function add_wallet_money(){
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'myprofile/populateSavedCards',
    beforeSend: function(){
      $(".add-wallet-topup-btn").attr("disabled", true);
      $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#listall_cards_fortopup').html(response.stripe_html);
      mount_stripe_element_for_wallettopup();
      if(!response.stripe_html) {
        $("input[name='payment-source-btn-forwallet'][value='newcard']").attr('checked', 'checked');
      }
      $('#add-wallet-money').modal('show');
      $('#quotes-main-loader').hide();
      $(".add-wallet-topup-btn").attr("disabled", false);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
}
// get more bookmarks
function moreBookmarkedRes(){
  $('#all_bookmarks').show();
  $('#load_more_bookmarks').hide();
}
//add wallet money :: end
function removeBookmark(restaurant_id){
    jQuery.ajax({
        type : "POST",
        url : BASEURL+'myprofile/removeBookmark',
        data : {'restaurant_id':restaurant_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            $('#bookmarks').load(' #bookmarks >* ');
            $('#quotes-main-loader').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
//add bookmark restaurant
function addBookmark(restaurant_id){
  jQuery.ajax({
        type : "POST",
        url : BASEURL+'restaurant/addBookmark',
        data : {'restaurant_id':restaurant_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response){
          $('.rest-detail-content > ul').load(' .rest-detail-content > ul >*');
          $('#quotes-main-loader').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
//Code for quanty with price calcualtion :: Start
function ItemqtyPlusMinus(id,action,price)
{
  var totalPrice = price;
  var qtyval = $('#qtyaddtocart-'+id).val(); 
  if(action=='minus')
  {
    var qtyval = parseInt(qtyval) - 1;
    qtyval = qtyval < 1 ? 1 : qtyval;
    if(qtyval < 1 || isNaN(parseInt(qtyval)) ){
      qtyval = 1;
    } 
  }
  else
  {
    var qtyval = parseInt(qtyval) + 1;
    if(qtyval < 1 || isNaN(parseInt(qtyval)) ){
      qtyval = 1;
    }
    if(qtyval > 999){
      qtyval = 999;
    }
  }
  $('#qtyaddtocart-'+id).val(qtyval);
  $('#qtyaddtocart-'+id).trigger("onblur");
  //onblur

  if(qtyval!=null && !isNaN(qtyval) && qtyval != '' && (parseInt(qtyval)>0)){ 
    totalPrice = parseFloat(totalPrice) * parseFloat(qtyval);  
  }    
  if(totalPrice!=null && !isNaN(totalPrice))
  {
    totalPrice = parseFloat(totalPrice).toFixed(2);
    $('#totalPrice').html(totalPrice);
  }
  else
  {
    $('#totalPrice').html(0.00);
  }  
}
function pricecalwithqty(id,qty,addon,priceval='',price)
{
  var existingStyles = $('#'+id).attr("style");  
  //$('#'+id).attr("style",existingStyles+"border:1px solid #E4E4E4 !important;");
  $('#'+id).attr("style","");
  var totalPrice = price;
  if(addon=='yes')
  {
    if(priceval=='yes')
    {
      //Code for add on seciton :: Start
      var radiototalPrice_addons = 0;    
      $("input:radio.radio_addons1:checked").each(function() {  
        var sThisVal = (this.checked ? $(this).attr("amount1") : 0);
        radiototalPrice_addons = parseFloat(radiototalPrice_addons) + parseFloat(sThisVal);
      });

      var checktotalPrice_addons = 0;
      $('.check_addons1:checkbox:checked').each(function () { 
        var sThisVal = (this.checked ? $(this).attr("amount1") : 0);
        checktotalPrice_addons = parseFloat(checktotalPrice_addons) + parseFloat(sThisVal);        
      });

      var totalPrice_addons = radiototalPrice_addons + checktotalPrice_addons;     
      totalPrice = totalPrice_addons + parseFloat(totalPrice);
      //Code for add on seciton :: End

      if(qty!=null && !isNaN(qty) && qty != '' && (parseInt(qty)>0)){ 
        totalPrice = parseFloat(totalPrice) * parseFloat(qty);  
      }    
      if(totalPrice!=null && !isNaN(totalPrice))
      {
        totalPrice = parseFloat(totalPrice).toFixed(2);
        $('#totalPrice1').html(totalPrice);
      }
      else
      {
        $('#totalPrice1').html(0.00);
      }
    }
    else
    {
      //Code for add on seciton :: Start
      var radiototalPrice = 0;
      //$("#custom_items_form input[type=radio]:checked").each(function() { 
      $("input:radio.radio_addons:checked").each(function() {  
        var sThisVal = (this.checked ? $(this).attr("amount") : 0);
        radiototalPrice = parseFloat(radiototalPrice) + parseFloat(sThisVal);
      });

      var checktotalPrice = 0;
      $('.check_addons:checkbox:checked').each(function () { 
        var sThisVal = (this.checked ? $(this).attr("amount") : 0);
        checktotalPrice = parseFloat(checktotalPrice) + parseFloat(sThisVal);        
      });

      var totalPrice_addons = radiototalPrice + checktotalPrice;     
      totalPrice = totalPrice_addons + parseFloat(totalPrice);
      //Code for add on seciton :: End

      if(qty!=null && !isNaN(qty) && qty != '' && (parseInt(qty)>0)){ 
        totalPrice = parseFloat(totalPrice) * parseFloat(qty);  
      }    
      if(totalPrice!=null && !isNaN(totalPrice))
      {
        totalPrice = parseFloat(totalPrice).toFixed(2);
        $('#totalPrice').html(totalPrice);
      }
      else
      {
        $('#totalPrice').html(0.00);
      }
    }
  }
  else
  {
      if(qty!=null && !isNaN(qty) && qty != '' && (parseInt(qty)>0)){ 
        totalPrice = parseFloat(totalPrice) * parseFloat(qty);  
      }    
      if(totalPrice!=null && !isNaN(totalPrice))
      {
        totalPrice = parseFloat(totalPrice).toFixed(2);
        $('#totalPrice').html(totalPrice);
      }
      else
      {
        $('#totalPrice').html(0.00);
      }
  }    
}
//Code for quanty with price calcualtion :: End