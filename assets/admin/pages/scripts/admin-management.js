// Add CMS Validation
jQuery("#form_add_category").validate({  
  rules: {    
    CategoryName: {
      required: true
    }
  }  
});
//Reset password
jQuery('#newPasswordform').validate({
  rules:{
    password: {
      required: true,
    },
    confirm_pass: {
      required: true,
      equalTo: "#password"
    }
  }
});
jQuery('#form_add_user_fororder').validate({
  ignore:[],
  rules:{
    first_name:{
      required:true,
      //alpha:true
    },
    first_name_add:{
      required:true,
      //alpha:true
    },
    last_name_add:{
      required:true,
      //alpha:true
    },
    email_add:{
      required:true,
      emailcustom:true,
      emailcus: true,
    },
    last_name:{
      required:true,
      //alpha:true
    },
    mobile_number:{
      required:true,
      //digits:true,
      intlTelNumber: true,
      // minlength: 9,
      // maxlength:10,
      // startWithZero: true
    },
    mobile_number_add:{
      required:true,
      //digits:true,
      intlTelNumber: true,
      // minlength: 9,
      // maxlength:10,
      // startWithZero: true
    },
    address_field:{
      required:true,  
    },
    zipcode:{
      required:true,
      minlength: 5,
      maxlength:6,
      alphanumeric:true
    },
    city:{
      required:true,  
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "mobile_number" || element.attr("name") == "mobile_number_add"){
      error.insertAfter('.phn_err'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
//add user
jQuery('#form_add_us').validate({
  ignore:[],
  rules:{
    first_name:{
      required:true,
      //alpha:true
    },
    last_name:{
      required:true,
      //alpha:true
    },
    email:{
      required:true,
      emailcustom:true,
      emailcus: true,
    },
    mobile_number:{
      required:true,
      //digits:true,
      intlTelNumber: true,
      /*minlength: 9,
      maxlength:10,
      startWithZero: true*/
    },
    user_type:{
      required:true
    },
    parent_id:{
      required:{
        depends: function(){
          if($('#selected_role_name').val() == 'Branch Admin' && $('#loggedin_user_type').val() == 'MasterAdmin'){
              return true;
          }
        }
      }
    },
    /*Image:{
      required:{
        depends: function(){
          if($('#uploaded_image').val() == ''){
              return true;
          }
        }
      }
    },*/
    password:{
      required:{
        depends: function(){
          if($('#entity_id').val() == ''){
              return true;
          }
        }
      },
      minlength: 6,
    },
    confirm_password:{
      required:{
        depends: function(){
          if($('#entity_id').val() == ''){
              return true;
          }
        }
      },
      equalTo:'#password'
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "mobile_number"){
      error.insertAfter('.phn_err'); 
    }
    else if (element.attr("name") == "Image") 
    {
      error.insertAfter('#errormsg');
    } else if(element.attr("name") == "parent_id"){
      error.insertAfter('.parent_id_required');
    }
    else
    {
      error.insertAfter(element);
    }
  }
});
//add address
jQuery('#form_add_ad').validate({
  rules:{
    user_entity_id:{
      required:true
    },
    address:{
      required:true
    },
    latitude:{
      required:true,
      number:true
    },
    longitude:{
      required:true,
      number:true
    },
    country:{
      required:true,
    },
    state:{
      required:true,
    },
    city:{
      required:true,
    },
    zipcode:{
      required:true,
      minlength: 5,
      maxlength:6,
      alphanumeric:true
    },
  }
});
//add restaurant
jQuery('#form_add_re').validate({
  ignore: [],
  rules:{
    /*branch_admin_id:{
      required:true,
    },*/
    restaurant_owner_id:{
      required:true,
    },
    add_res_branch:{
      required:true
    },
    res_name:{
      required:{
        depends: function(){
          if($("input[name=add_res_branch]:checked").val() == "res" ){
            return true;
          }
        }
      },
      //alphanumeric:false
    },
    branch_name:{
      required:{
        depends: function(){
          if($("input[name=add_res_branch]:checked").val() == "branch" || $("input[name=add_res_branch]").val() == "branch" ){
            return true;
          }
        }
      },
      alphanumeric:false
    },
    branch_entity_id:{
      required:{
        depends: function(){
          if($("input[name=add_res_branch]:checked").val() == "branch" ){
            return true;
          }
        }
      }
    },
    phone_number:{
      required:true,
      intlTelNumber: true,
      /*digits:true,
      minlength: 9,
      maxlength:10*/
    },
    email:{
      required:true,
      emailcustom:true,
      emailcus: true,
    },
    capacity:{
      required:{
        depends: function(){
          if($("#allow_event_booking").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
      greaterThanEventMinCapacity: "#event_minimum_capacity",
    },
    address:{
      required:true,
    },
    latitude:{
      required:true,
    },
    longitude:{
      required:true,
    },
    state:{
      required:true,
    },
    country:{
      required:true,
    },
    city:{
      required:true,
    },
    zipcode:{
      required:true,
      minlength: 5,
      maxlength:6,
      alphanumeric:true
    },
    currency_id:{
      required:true
    },
    food_type:{
      required:true      
    },
    amount_type:{
      required:true,
    },
    amount: {
      required: true,
      number:true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=amount_type]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=amount_type]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    service_fee: {
      required: {
        depends: function(){
          if($("input[name=is_service_fee_enable]").val() == "1" ){
            return true;
          }
        }
      },
      number:true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=service_fee_type]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=service_fee_type]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    /*enable_hours:{
      required:true
    },*/
    printer_paper_height:{
      required: {
        depends: function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return true;
          }
        }
      },
      minlength: 1,
      maxlength:function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return 3;
          }
      },
      min:function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return 0;
          }
      }, 
      digits: {
        depends: function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return true;
          }
        }
      },
    },
    printer_paper_width:{
      required: {
        depends: function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return true;
          }
        }
      },
      minlength: 1,
      maxlength:function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return 3;
          }
      },
      min:function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return 0;
          }
      }, 
      digits: {
        depends: function(){
          if($("input[name=is_printer_available]").val() == "1" ){
            return true;
          }
        }
      },
    },
    message: {
      ckrequired:true
    },
    contractual_commission_type:{
      required:true,
    },
    contractual_commission: {
      required: true,
      number:true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=contractual_commission_type]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=contractual_commission_type]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    contractual_commission_type_delivery:{
      required:true,
    },
    contractual_commission_delivery: {
      required: true,
      number:true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=contractual_commission_type_delivery]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=contractual_commission_type_delivery]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    creditcard_fee: {
      required: {
        depends: function(){
          if($("input[name=is_creditcard_fee_enable]").val() == "1" ){
            return true;
          }
        }
      },
      number:true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=creditcard_fee_type]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=creditcard_fee_type]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    'payment_methods[]':{
      required:true
    },
    'order_mode[]':{
      required:true
    },
    event_online_availability:{
      required:{
        depends: function(){
          if($("#allow_event_booking").val() == "1"){
            return true;
          }
        }
      },
      number:true,
      min:1,
      max:100
    },
    event_minimum_capacity:{
      required:{
        depends: function(){
          if($("#allow_event_booking").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
      lesserThanEventBookingCapacity:"#capacity",
    },
    table_booking_capacity:{
      required:{
        depends: function(){
          if($("#enable_table_booking").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
    },
    table_online_availability:{
      required:{
        depends: function(){
          if($("#enable_table_booking").val() == "1"){
            return true;
          }
        }
      },
      number:true,
      min:1,
      max:100
    },
    table_minimum_capacity:{
      required:{
        depends: function(){
          if($("#enable_table_booking").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
    },
    allowed_days_table:{
      required:{
        depends: function(){
          if($("#enable_table_booking").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
    },    
    allowed_days_for_scheduling:{
      required:{
        depends: function(){
          if($("#allow_scheduled_delivery").val() == "1"){
            return true;
          }
        }
      },
      digits:true,
      min: 1,
    },
    restaurant_rating:{
      required:true,
      number:true,
      min: 0,
      max: 5
    },
    restaurant_rating_count:{
      required:true,
      number:true,
      min: 0,
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "service_fee"){
      error.insertAfter('.service-fee-error'); 
    }
    else if( element.attr("name") == "amount"){
      error.insertAfter('.service-tax-error'); 
    }    
    else if(element.attr("name") == "contractual_commission"){
      error.insertAfter('.commision-error'); 
    }
    else if(element.attr("name") == "contractual_commission_delivery"){
      error.insertAfter('.commision-delivery-error'); 
    }
    else if( element.attr("name") == "creditcard_fee"){
      error.insertAfter('.creditcard-fee-error'); 
    }
    else if(element.attr("name") == "branch_admin_id"){
      error.insertAfter('.sumo_branch_admin_id'); 
    }
    else if(element.attr("id") == "restaurant_owner_id") 
    {
      error.insertAfter('.sumo_restaurant_owner_id');
    }
    else if(element.next('p').length > 0){
          error.insertAfter(element.next('p'));
    } else if( element.attr("name") == "phone_number"){
      error.insertAfter('#phoneExist'); 
    } 
    else if(element.attr("name") == "payment_methods[]"){
      error.insertAfter('.checkbox_error'); 
    }
    else if( element.attr("name") == "order_mode[]"){
      error.insertAfter('#checkbox_error'); 
    }    
    else if (element.attr("name") == "message") 
    {
      error.insertAfter('#cke_message');
      element.next().css('border', '1px solid red');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }
});

//add recipe
jQuery('#form_add_recipe').validate({
  ignore: [],
  rules:{
    name:{
      required:true
    },
    detail:{
      required:true
    },
    ingredients:{
      ckrequired:true
    },
    recipe_detail:{
      ckrequired:true
    },
    recipe_time:{
      required : true,
      digits :true
    },
    food_type:{
      required:true      
    },
    youtube_video:{
      youtubecustom:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("name") == "recipe_detail") 
    {
      error.insertAfter('#cke_recipe_detail');
      element.next().css('border', '1px solid red');
    } 
    else if (element.attr("name") == "ingredients") {
      error.insertAfter('#cke_ingredients');
      element.next().css('border', '1px solid red');
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
//add category
jQuery('#form_add_cg').validate({
  rules:{
    name:{
      required:true
    }
  }
});
//add food type
jQuery('#form_add_fdt').validate({
  rules:{
    name:{
      required:true
    },
    is_veg:{
      required:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "is_veg"){
      error.insertAfter('#radiobtn_error'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
//add menu
jQuery('#form_add_menu').validate({
  ignore: [],
  rules:{
    restaurant_id:{
      required:true
    },
    category_id:{
      required:true
    },
    name:{
      required:true,
    },
    sku:{
      required:true
    },
    price:{
      required:true,
      number:true,
      min:0
    },
    menu_detail:{
      required:true
    },
    /*ingredients:{
      ckrequired:true
    },*/
    recipe_time:{
      required : true,
      digits :true
    },
    food_type:{
      required:true      
    },
    'availability[]':{
      required:true
    },
    'addons_category_id[]':{
      required:{
          depends: function(){
          if($('#check_add_ons').is(':checked')){
            return true;
          }
        }
      },
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "availability[]"){
      error.insertAfter('#checkbox_error'); 
    }
    /*else if (element.attr("name") == "ingredients") {
      error.insertAfter('#cke_ingredients');
      element.next().css('border', '1px solid red');
    }*/
    else if(element.next('p').length > 0){
      error.insertAfter(element.next('p'));
    }
    else if( element.attr("name") == "addons_category_id[]"){
      error.insertAfter('#checkbox_error2'); 
    }
    else{
      error.insertAfter(element);
    }
  }
});
//add package
jQuery('#form_add_pac').validate({
  ignore: [],
  rules:{
    name:{
      required:true
    },
    restaurant_id:{
      required:true
    },
    category_id:{
      required:true
    },
    price:{
      required:true,
      number:true,
      min:0
    },
    detail:{
      ckrequired:true
    },
    'availability[]':{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "availability[]"){
      error.insertAfter('#checkbox_error'); 
    }
    else if (element.attr("name") == "detail") 
    {
      error.insertAfter('#cke_detail');
      element.next().css('border', '1px solid red');
    } 
    else if(element.next('p').length > 0){
      error.insertAfter(element.next('p'));
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
//add coupon
jQuery('#form_add_cpn').validate({
  ignore:[],
  rules:{
    coupon_type:{
      required:true
    },
  name:{
      required:true
    },
    image:{
      required:{
          depends: function(){
          if($('#show_in_home').is(':checked') && $("#image_exist").val() != "1"){
            return true;
          }
        }
      },
    },
    'restaurant_id[]':{
      required:true
    },
    description:{
      ckrequired:true
    },
    amount_type:{
      required:true
    },
    'item_id[]':{
      required: {
        depends: function(){
          /*if($("#coupon_type").val() == "dine_in" || $("#coupon_type").val() == "discount_on_items" || $("#coupon_type").val() == "discount_on_combo"){*/
          if($("#coupon_type").val() == "discount_on_items" || $("#coupon_type").val() == "discount_on_combo"){
            return true;
          }
        }
      },
    },
    amount:{
      required: {
        depends: function(){
          if($("#coupon_type").val() != "free_delivery" && $("#coupon_type").val() != "discount_on_categories"){
            return true;
          }
        }
      },
      number: true,
      max: {
        param:100,
        depends: function(){
          if($("input[name=amount_type]:checked").val() == "Percentage" ){
            return true;
          }
        }
      },
      min: function(element){
          if($("input[name=amount_type]:checked").val() == "Percentage"){
              return 1;
          }else{
              return 0;
          }
      }
    },
    max_amount:{
      required: {
        depends: function(){
          if($("#coupon_type").val() != "discount_on_categories"){
            return true;
          }
        }
      },
      number:true,
      min:function(element){
        if($("#coupon_type").val() != "discount_on_categories"){
          return 1;
        }else{
          return 0;
        }
      }
    },
    start_date:{
      required:true
    },
    end_date:{
      required:true
    },
    maximaum_use_per_users:{
      required:{
        depends: function(){
          if($('#coupon_type').val() == 'discount_on_cart'){
              return true;
          }
        }
      },          
      maxlength:3,
      number:true,
      min:0,
      lesserThanCoupon:"#maximaum_use",
    },
    maximaum_use:{
      required:{
        depends: function(){
          if($('#coupon_type').val() == 'discount_on_cart'){
              return true;
          }
        }
      },      
      maxlength:3,
      number:true,
      min:0
    },
    'category_content_id[]':{
      required: {
        depends: function(){
          if($("#coupon_type").val() == "discount_on_categories"){
            return true;
          }
        }
      },
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("name") == "description") 
    {
      error.insertAfter('#cke_description');
      element.next().css('border', '1px solid red');
    } 
    else 
    {
      error.insertAfter(element);
    }
    if( element.attr("name") == "category_content_id[]"){
      error.insertAfter('#checkbox_error'); 
    }
    if (element.attr("id") == "restaurant_id") 
    {
      error.insertAfter('.sumo_restaurant_id');
    } 
    if (element.attr("id") == "item_id") 
    {
      error.insertAfter('.sumo_item_id');
    }
    if (element.attr("name") == "image") 
    {
      error.insertAfter('.coupon-img-error');
    } 
  }
});
//add category
jQuery('#form_add_order').validate({
  rules:{
    user_id:{
      required:true
    },
    restaurant_id:{
      required:true
    },
    address_id:{
      required:{
        depends: function(){
          if($("input[name='order_mode']:checked").val() == "Delivery" ){
            return true;
          }
        }
      }
    },
    ord_address_field:{
      required:{
        depends: function(){
          if($("input[name='order_mode']:checked").val() == "Delivery" && $('#address_id').val() == 'other'){
            return true;
          }
        }
      }
    },
    ord_zipcode:{
      required:{
        depends: function(){
          if($("input[name='order_mode']:checked").val() == "Delivery" && $('#address_id').val() == 'other'){
            return true;
          }
        }
      },
      minlength: 5,
      maxlength:6,
      digits: true
    },
    order_status:{
      required:true
    },
    order_date:{
      required:true
    },
    subtotal:{
      required:true
    },
    tax_rate_display:{
      required:true
    },
    total_rate:{
      required:true
    },
    delivery_charge:{
      required:{
        depends: function(){
          if($("input[name='order_mode']:checked").val() == "Delivery" ){
            return true;
          }
        }
      }
    },
    order_mode:{
      required:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("id") == "order_mode") 
    {
      error.insertAfter('#order_mode_err');
    }
    else if(element.attr("type") == "checkbox"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else if(element.attr("type") == "radio"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else if(element.next('p').length > 0){
      error.insertAfter(element.next('p'));
    }
    else if (element.attr("id") == "check_order_mode_val") 
    {
      error.insertAfter('#order_mode_err');
    }
    else {
      error.insertAfter(element);
    }
  }
});
jQuery('#form_adddine_order').validate({
  rules:{
    mobile_number:{
      required:true,
      digits:true,
      maxlength:14
    },
    first_name:{
      required:true,
      //alpha:true
    },
    email:{
      required:true,
      emailcustom:true,
      emailcus: true,
    },
    restaurant_id:{
      required:true
    },
    table_id:{
      required:true
    },   
    order_status:{
      required:true
    },
    order_date:{
      required:true
    },
    total_rate:{
      required:true
    }
  },
  errorPlacement: function (error, element) {
    var elm = $(element);
    if(elm.next('p').length > 0){
        error.insertAfter(elm.next('p'));
    }
    else if(element.attr("type") == "checkbox"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else if(element.attr("type") == "radio"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else {
        error.insertAfter(elm);
    }
  }
});
jQuery('#form_add_event').validate({
  rules:{
    name:{
      required:true
    },
    no_of_people:{
      required:true,
      digits:true
    },
    no_of_table:{
      required:true,
      digits:true
    },
    booking_date:{
      required:true
    },
    restaurant_id:{
      required:true
    },
    user_id:{
      required:true
    },
    end_date:{
      required:true
    }
  }
});
jQuery('#form_add_cms').validate({
  ignore:[],
  rules:{
    name:{
      required:true
    },
    description:{
      ckrequired:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("name") == "description") 
    {
      error.insertAfter('#cke_description');
      element.next().css('border', '1px solid red');
    } 
    if (element.attr("name") == "cms_icon") 
    {
      error.insertAfter('#icon_errormsg');
    }
    else if(element.next('p').length > 0){
        error.insertAfter(element.next('p'));
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
// Add Email Template Validation
jQuery("#form_add_email").validate({  
  ignore:[],
  rules: {    
    title: {
      required: true
    },
    subject: {
      required: true
    },
    message: {
      required: function() 
      {
        CKEDITOR.instances.message.updateElement();
      }
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("name") == "message") 
    {
      error.insertAfter('#cke_message');
      element.next().css('border', '1px solid red');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }
});
//add Amount
jQuery('#form_add_amount').validate({
  rules:{
    coupon_amount:{
      number:true,
      min:0
    },
    subtotal:{
      required:true,
      number:true,
      min:0
    }
  }
});
//add Amount
jQuery('#form_add_notification').validate({
  rules:{
    distance:{
      required:{
        depends: function(){
          if($('#restaurant').val() != ''){
              return true;
          }
        }
      }
    },
    restaurant:{
      required:{
        depends: function(){
          if($('#distance').val() != ''){
              return true;
          }
        }
      }
    },
    'user_id[]':{
      required:true
    },
    notification_title:{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("id") == "user_id") 
    {
      error.insertAfter('.sumo_user_id');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }
});
jQuery('#send_email').validate({
  rules:{
    'user_id[]':{
      required:true
    },
    template_id:{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("id") == "user_id") 
    {
      error.insertAfter('.sumo_user_id');
    } 
    if (element.attr("id") == "template_id") 
    {
      error.insertAfter('.sumo_template_id');
    } 
  }
});
jQuery('#send_noti').validate({
  rules:{
    'user_id_noti[]':{
      required:true
    },
    notification_title:{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("id") == "user_id_noti") 
    {
      error.insertAfter('.sumo_user_id_noti');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }
});
//generate Amount
jQuery('#generate_report').validate({
  rules:{
    restaurant_id:{
      required:true
    },
  }
});
//event generate report
jQuery('#event_generate_report').validate({
  ignore:[],
  rules:{
    'restaurant_id[]':{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("id") == "restaurant_id") 
    {
      error.insertAfter('.sumo_restaurant_id');
    } 
  }
});
//add addons category
jQuery('#form_add_acg').validate({
  rules:{
    name:{
      required:true
    },
    mandatory:{
      required:true
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) 
  { 
    var placement = $(element).data('error');
    if (element.attr("id") == "mandatory") 
    {
      error.insertAfter('#mandatory_err');
    } else {
      error.insertAfter(element);
    } 
  }
});
//add delviery charge
jQuery('#form_add_delivery').validate({
  rules:{
    restaurant_id:{
        required :true
      },
    area_name:{
      required:true
    },
    lat_long:{
      required:true
    },
    price_charge:{
      number:true,
      required:true
    },
    additional_delivery_charge:{
      number:true,
      required:true
    }
  },
  errorPlacement: function (error, element) {
    var elm = $(element);
    if(elm.next('p').length > 0){
        error.insertAfter(elm.next('p'));
    }
    else {
        error.insertAfter(elm);
    }
  }
});
//add menu deal
jQuery('#form_add_deal').validate({
  ignore: [],
  rules:{
    name:{
      required:true
    },
    restaurant_id:{
      required:true
    },
    category_id:{
      required:true
    },
    price:{
      required:true,
      number:true,
      min:0
    },
    menu_detail:{
      required:true
    },
    'availability[]':{
      required:true
    },
    check_add_ons:{
      required:true
    },
    'addons_category_id[]':{
      required:{
          depends: function(){
          if($('#check_add_ons').is(':checked')){
            return true;
          }
        }
      },
    }
  },
});
$.validator.addMethod("emailcustom",function(value,element)
{
  return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,10}\b$/i.test(value);
},"Please enter valid email address");
$.validator.addMethod("emailcus",function(value,element)
{
  return this.optional(element) || /^[\w%\+\-]+(\.[\w%\+\-]+)*@[\w%\+\-]+(\.[\w%\+\-]+)+$/.test(value);
},"Please enter a valid email address");
// custom password
$.validator.addMethod("passwordcustome",function(value,element)
{
  return this.optional(element) || /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/.test(value);
},"Passwords must contain at least 8 characters, including uppercase, lowercase letters, symbols and numbers.");
// end here

/* alphanumeric start */
$.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[\w ]+$/i.test(value);
  }, "Please enter only characters and numbers.");
/* alphanumeric end */

$.validator.addMethod("startWithZero",function(value,element)
{
  return this.optional(element) || value.indexOf('0')===0;
},"Phone number should start with 0");

// create a custom phone number rule called 'intlTelNumber'
jQuery.validator.addMethod("intlTelNumber", function(value, element) {
    return this.optional(element) || phoneInput.isValidNumber();
}, "Please enter valid phone number");

// custom code for lesser than
jQuery.validator.addMethod('lesserThan', function(value, element, param) { 
  if(value || jQuery(param).val()) {
    return ( parseInt(value) < parseInt(jQuery(param).val()) );
  } else {
    return true;
  }  
}, 'Must be less than close time' );

// custom code for lesser than
jQuery.validator.addMethod('lesserThanCoupon', function(value, element, param)
{
  var maximaum_use = ($('#maximaum_use').val())?$('#maximaum_use').val():0;
  maximaum_use = parseInt(maximaum_use);
  if(maximaum_use!=NaN && maximaum_use>0)
  {
    if(value || jQuery(param).val()) {
      return ( parseInt(value) <= parseInt(jQuery(param).val()) );
    } else {
      return true;
    }
  }
  else
  {
    return true;
  } 
}, 'Must be less than or equal to total number of coupons' );

//Code for add the amount for refund :: Start
(function(){
  var msg = "";
  var messager = function() {
    return msg;
  };
  jQuery.validator.addMethod("lesserThanRefund",
    function(value, element,param)
    {
      if(parseFloat(value) > parseFloat(jQuery(param).val())) {
        var refund_order_total = jQuery('#refund_order_totaldis').val();
        msg = 'Refund amount can not be greater than '+refund_order_total;
        //return ( parseInt(value) <= parseInt(jQuery(param).val()) );
      } else {
        return true;
      }
    },
    messager);
})();
//Code for add the amount for refund :: End

// custom code for greater than
$.validator.addMethod("greaterThan", function(value, element, param) {
  if(value || jQuery(param).val()) {
    return ( parseInt(value) > parseInt(jQuery(param).val()) );
  } else {
    return true;
  }
}, "Must be greater than open time");

// custom code for greater than
$.validator.addMethod("greater", function(value, element, param) {
  return ( parseFloat(value) > parseFloat(jQuery(param).val()));    
}, "Must be greater than Amount");

jQuery.validator.addMethod("ckrequired", function (value, element) {  
    var idname = $(element).attr('id');  
    var editor = CKEDITOR.instances[idname];  
    var ckValue = GetTextFromHtml(editor.getData()).replace(/<[^>]*>/gi, '').trim();  
    if (ckValue.length === 0) {  
//if empty or trimmed value then remove  extra spacing to current control  
        $(element).val(ckValue);  
    } else {  
//If not empty then leave the value as it is  
        $(element).val(editor.getData());  
    }  
    return $(element).val().length > 0;  
}, "This field is required");
function GetTextFromHtml(html) {  
  var dv = document.createElement("DIV");  
  dv.innerHTML = html;  
  return dv.textContent || dv.innerText || "";  
}
// custom code for price
$.validator.addMethod("customPrice",function(value,element)
{
  return this.optional(element) || /[^0-9\-]+/.test(value);
},"Please enter valid price");


// display currency in price
function getCurrency(value){ 
  if (value) {
    $.ajax({
      type: "POST",
      url: BASEURL+"backoffice/home/getCurrencySymbol",
      data: 'restaurant_id=' + value ,
      cache: false,
      success: function(response) {
        if (response) {
          $('#currency-symbol').html('('+response+')');
          $('.currency-symbol').html('('+response+')');
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {                 
        alert(errorThrown);
      }
    });
  }
  else
  {
    $('#currency-symbol').hide();
    $('.currency-symbol').hide();
  }
} 

// display currency in price
function getEventCurrency(entity_id){
  if (entity_id) {
    $.ajax({
      type: "POST",
      url: BASEURL+"backoffice/home/getEventCurrencySymbol",
      data: 'entity_id=' + entity_id ,
      cache: false,
      success: function(response) {
        if (response) {
          $('#currency-symbol').html('('+response+')');
          $('#currency-symbol').show();
          $('.currency-symbol').html('('+response+')');
          $('.currency-symbol').show();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {                 
        alert(errorThrown);
      }
    });
  }
  else
  {
    $('#currency-symbol').hide();
    $('.currency-symbol').hide();
  }
} 

/*
  Issue: Clientside my profile edit/update validations are not added.
  Solution: Required clientside Validations are added.
  Updated On: 29/10/2020
*/

$.validator.addMethod("alpha", function (value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z\s]*$/);
}, "Only alphabets and space are allowed.");

jQuery('#form_edit_editor').validate({
  rules:{
    first_name:{
      required:true,
      //alpha:true
    },
    last_name:{
      required:true,
      //alpha:true
    },
    email:{
      required:true,
      emailcustom:true,
      emailcus: true,
    },
    mobile_number:{
      //digits:true,
      intlTelNumber: true,
      // minlength: 9,
      // maxlength:10
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "mobile_number"){
      error.insertAfter('#phoneExist'); 
    }
    else
    {
      error.insertAfter(element);
    }
  }
});

jQuery('#userChangePass').validate({
  rules:{
    password:{
      required:true,
      //minlength: 6,
      passwordcustome:true
    },
    confirm_password:{
      required: true,
      equalTo:'#password'
    }
  }
});
//generate Amount
jQuery('#export_order').validate({
  rules:{
    restaurant_id:{
      required:true
    },
    order_delivery:{
      required:true
    },
  },
  errorPlacement: function (error, element) {
      var elm = $(element);
      if(elm.next('p').length > 0){
          error.insertAfter(elm.next('p'));
      }
      else {
          error.insertAfter(elm);
      }
  }
});
jQuery.validator.addMethod("greaterThanDate", function(value, element, params)
{
    if (!/Invalid|NaN/.test(new Date(value)))
    {
        return new Date(value) >= new Date($(params).val());
    }
    return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val())); 
},'Must be greater than from date.');

//add table
jQuery('#form_add_tb').validate({
  rules:{
    restaurant_id:{
      required:true
    },
    table_no:{
      required:true,
      digits: true,
      min: 1,
    },
    capacity:{
      required:true,
      digits: true,
      min: 1,
    }
  },
  errorPlacement: function (error, element) {
      var elm = $(element);
      if(elm.next('p').length > 0){
          error.insertAfter(elm.next('p'));
      }
      else {
          error.insertAfter(elm);
      }
  }
});

jQuery('#form_add_reson_mng').validate({
  rules:{
    reason:{
      required:true,
      maxlength:255
    },
    reason_type:{
      required:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "reason_type"){
      error.insertAfter('#radiobtn_error'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});

jQuery("#form_add_menu_suggestion").validate({  
  rules: {    
    restaurant_id: {
      required: true
    },
    /*'item_id[]':{
      required:true
    },*/
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "restaurant_id"){
      error.insertAfter('.sumo_restaurant_id'); 
    }
    else if(element.attr("name") == "item_id[]"){
      error.insertAfter('.sumo_item_id'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }  
});

jQuery('#form_payment_method').validate({
  rules:{
    display_name_en: {
      required: true,
      maxlength: 255
    },
    display_name_fr: {
      required: true,
      maxlength: 255
    },
    display_name_ar: {
      required: true,
      maxlength: 255
    },
    sorting: {
      required: true,
      min: 1,
      digits: true
    },
    sandbox_client_id: {
      required: true,
      maxlength: 255
    },
    sandbox_client_secret: {
      required: true,
      maxlength: 255
    },
    live_client_id: {
      required: true,
      maxlength: 255
    },
    live_client_secret: {
      required: true,
      maxlength: 255
    },
    test_publishable_key: {
      required: true,
      maxlength: 255
    },
    test_secret_key: {
      required: true,
      maxlength: 255
    },
    test_webhook_secret: {
      required: true,
      maxlength: 255
    },
    live_publishable_key: {
      required: true,
      maxlength: 255
    },
    live_secret_key: {
      required: true,
      maxlength: 255
    },
    live_webhook_secret: {
      required: true,
      maxlength: 255
    },
  }
});
//add user
jQuery('#food_type_form').validate({
  rules:{
    name:{
      required:true
    },
    is_veg:{
      required:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "is_veg"){
      error.insertAfter('#food_type_popup_error'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }
});
//initialize edit normal and dinein order validation
jQuery('#form_add_dinein').validate({
  rules:{
  },
  errorPlacement: function (error, element) {
    var elm = $(element);
    if(elm.next('p').length > 0){
        error.insertAfter(elm.next('p'));
    }
    else if(element.attr("type") == "checkbox"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else if(element.attr("type") == "radio"){
      /*error.appendTo(element.closest('.row').find('.control-label'));*/
      error.appendTo( element.closest('.row').find('.addon-error'));
    }
    else {
        error.insertAfter(elm);
    }
  }
});

jQuery('#form_add_faq_cat').validate({
  rules:{
    name:{
      required:true,
      maxlength:128
    },
    sequence:{
      required:true,
      digits:true,
      min:0
    }
  }
});

jQuery('#form_add_faqs').validate({
  rules:{
    question:{
      required:true,
      maxlength:512,
      minlength:5
    },
    answer:{
      required:true,
      maxlength:5120,
      minlength:5
    },
    faq_category_content_id:{
      required:true
    }
  },
  errorPlacement: function(error, element) 
  {
    if (element.attr("name") == "faq_category_content_id") 
    {
      error.insertAfter('.sumo_faq_category_content_id');
    } else {
      error.insertAfter(element);
    }
  }
});
//youtube link validation
$.validator.addMethod("youtubecustom",function(value,element)
{
  return this.optional(element) || /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/i.test(value);
},"Please enter valid Youtube link");


jQuery("#form_add_phone_number").validate({
  rules: {
    mobile_number: {
      required: true,
      intlTelNumber: true,
    },
  },
  errorPlacement: function(error, element)
  {
    if(element.attr("name") == "mobile_number") {
      error.insertAfter('.phn_err');
    } else {
      error.insertAfter(element);
    }
  }
});
jQuery("#form_add_paymethod_suggestion").validate({  
  rules: {    
    restaurant_id: {
      required: true
    },
    'payment_method_id[]':{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "restaurant_id"){
      error.insertAfter('.sumo_restaurant_id'); 
    }
    else if(element.attr("name") == "payment_method_id[]"){
      error.insertAfter('.sumo_payment_method_id'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }  
});
jQuery("#form_add_delivery_method").validate({  
  rules: {    
    restaurant_id: {
      required: true
    },
    'delivery_method_id[]':{
      required:true
    },
  },
  errorPlacement: function(error, element) 
  {
    if( element.attr("name") == "restaurant_id"){
      error.insertAfter('.sumo_restaurant_id'); 
    }
    else if(element.attr("name") == "delivery_method_id[]"){
      error.insertAfter('.sumo_delivery_method_id'); 
    }
    else 
    {
      error.insertAfter(element);
    }
  }  
});
//Code for refund validation :: start
jQuery('#form_add_refund_reason').validate({
  rules:{
    partial_refundedamt:{
      required:{
        depends: function(){
          if($("input[name=partial_refundedchk]:checked").val() == "partial" ){
            return true;
          }
        }
      },
      number:true,
      min:1,
      lesserThanRefund:"#refund_order_total",
    },
    refund_reason: {
      required: true,
    }    
  }
});
jQuery('#form_order_refund_reason').validate({
  rules:{
    refund_reasontext: {
      required: true,
    }    
  }
});
jQuery('#form_edititem_refund_reason').validate({
  rules:{
    itemrefund_reasontemp: {
      required: true,
    }    
  }
});
//End