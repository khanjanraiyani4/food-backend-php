// Front Login Validation
jQuery("#form_front_login").validate({  
  rules: {    
    phone_number_inp: {
      required:  {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'phone_number'){
            return true;
          }
        }
      },
      intlTelNumber: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'phone_number'){
            return true;
          }
        }
      }
    },
    email_inp: {
      required:  {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      },
      emailcustom: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      },
      emailcus: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      }
    },
    password: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "phone_number_inp") {
      error.insertAfter('.phn_err'); 
    }else {
    error.insertAfter(element);
    }
  }    
});
// Review Validation
jQuery("#review_form").validate({  
  rules: { 
    rating: {
      required: true
    }, 
    review_text: {
      required: true,
      maxlength: 250
    }, 
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});
// Front Forgot Password Validation
/*jQuery("#form_front_forgotpass").validate({  
  rules: {
    mobile_number_first: {
      required: true,      
      maxlength: 12,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});*/
jQuery("#form_front_change_pass").validate({  
  rules: {
    password_forgot_pwd: {
      required: true,
      minlength: 6,
    },
    confirm_password_forgot_pwd: {
      required: true,
      minlength: 6,
      equalTo:'#password_forgot_pwd'
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});
// Front Check Event Validation
jQuery("#check_event_availability").validate({  
  rules: {    
    no_of_people: {
      required: true,
      digits: true
    }, 
    date_time: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});
// Front Registration Validation
jQuery("#form_front_registration").validate({  
  rules: { 
    first_name: {
      required: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      //alpha:true
    },
    last_name: {
      required: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
     // alpha:true
    },   
    email: {
      required: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      emailcustom: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      emailcus: {
        depends: function(){
          if($("#otp_verified").val() != 'yes'){
            return true;
          }
        }
      }
    },   
    phone_number: {
      required: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      intlTelNumber: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      /*minlength: 9,
      maxlength: 10,
      startWithZero: true*/
    },
    password: {
      required: {
        depends: function(){
          if($('#otp_verified').val() != 'yes'){
            return true;
          }
        }
      },
      minlength: 6,
    }
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "phone_number") {
      error.insertAfter('.phn_err'); 
    }else{
      error.insertAfter(element);
    }
  }   
});
// Front Contact Us Validation
jQuery("#form_front_contact_us").validate({  
  ignore: ".ignore",
  rules: { 
    first_name: {
      required: true,
      minlength: 2,
      //alpha:true
    },
    last_name: {
      required: true,
      minlength: 2,
      //alpha:true
    },   
    email: {
      required: true,
      minlength: 2,
      emailcustom: true,
      emailcus: true,
    },
    res_phone_number: {
      required: true,
      digits: true,
      intlTelNumber: true,
      minlength: 10,
      maxlength: 10,
    },
    res_name: {
      required: true,
      minlength: 2,
    },
    owners_phone_number: {
      required: true,
      digits: true,
      intlTelNumber: true,
      minlength: 10,
      maxlength: 10,
    },
    res_zip_code: {
      required: true,
      digits: true,
      minlength: 5,
      maxlength: 5,
    },
    /*message: {
      required: true,
    }*/
    hiddenRecaptcha: {
      required: function () {
        if (grecaptcha.getResponse() == '') {
          return true;
        } else {
          return false;
        }
      }
    },
  },
  messages: {
    res_phone_number: {
      minlength: function () {
        return ["Please enter at least 10 digits."];
      },
      maxlength: function () {
        return ["Please enter no more than 10 digits."];
      }
    },
    owners_phone_number: {
      minlength: function () {
        return ["Please enter at least 10 digits."];
      },
      maxlength: function () {
        return ["Please enter no more than 10 digits."];
      }
    },
    res_zip_code: {
      minlength: function () {
        return ["Please enter at least 5 digits."];
      },
      maxlength: function () {
        return ["Please enter no more than 5 digits."];
      }
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "res_phone_number") {
      error.insertAfter('.phn_err'); 
    } else if(element.attr('id') == "owners_phone_number") {
      error.insertAfter('.phn_errown'); 
    } else {
    error.insertAfter(element);
    }
  } 
});
// Front Feedback Validation
jQuery("#form_front_feedback").validate({  
  rules: { 
    feedback_title: {
      required: true
    },
    feedback_message: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }  
});
// Front Contact Us Validation
jQuery("#form_my_profile").validate({  
  rules: { 
    first_name: {
      required: true,
      //alpha:true
    },
    last_name: {
      required: true,
      //alpha:true
    },
    email: {
      required: true,
      emailcustom: true,
      emailcus: true,
    },
    phone_number: {
      required: true,
      intlTelNumber:{
        depends: function(){
          if($('#phone_code').val() != ''){
              return true;
          }
        }
      },
      digits: true,
      minlength: function(){
        if($('#phone_code').val() == ''){
            return 9;
        }
      },
      maxlength: function(){
        if($('#phone_code').val() == ''){
            return 10;
        }
      },
    },
    password:{
      required:{
        depends: function(){
          if($('#confirm_password').val() != ''){
              return true;
          }
        }
      },
      minlength: 6,
    },
    confirm_password:{
      required:{
        depends: function(){
          if($('#password').val() != ''){
              return true;
          }
        }
      },
      equalTo:'#password'
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "phone_number") {
      error.insertAfter('.phn_err'); 
    }else {
    error.insertAfter(element);
    }
  }  
});
// Front Login Validation
jQuery("#form_front_login_checkout").validate({  
  rules: {    
    login_phone_number: {
      required:  {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'phone_number'){
            return true;
          }
        }
      },
      intlTelNumber: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'phone_number'){
            return true;
          }
        }
      }
    },
    email_inp: {
      required:  {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      },
      emailcustom: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      },
      emailcus: {
        depends: function(){
          if($("input[name=login_with]:checked").val() == 'email'){
            return true;
          }
        }
      }
    },
    login_password: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "login_phone_number") {
      error.insertAfter('.phn_err'); 
    }else {
    error.insertAfter(element);
    }
  }    
});
// Front Login Validation
jQuery("#guest_checkout_form").validate({  
  rules: { 
    first_name: {
      required: true,
      //alpha:true
    },
    last_name: {
      required: true,
      //alpha:true
    },
    login_phone_number: {
      required:  true,
      intlTelNumber: true,
    },
    email_inp: {
      //required:  true,
      emailcustom: true,
      emailcus: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "login_phone_number") {
      error.insertAfter('.phn_err'); 
    }else {
    error.insertAfter(element);
    }
  }    
});
jQuery("#agent_order_form").validate({  
  rules: { 
    first_name: {
      required: true,
      //alpha:true
    },
    last_name: {
      required: true,
      //alpha:true
    },
    login_phone_number: {
      required:  true,
      intlTelNumber: true,
    },
    email_inp: {
      //required:  true,
      emailcustom: true,
      emailcus: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else if(element.attr('id') == "login_phone_number") {
      error.insertAfter('.phn_err'); 
    }else {
    error.insertAfter(element);
    }
  }    
});
// Front Registration Validation
jQuery("#form_front_registration_checkout").validate({  
  rules: { 
    name: {
      required: true
    },   
    phone_number: {
      required: true,
      phoneNumber: true
    },
    password: {
      required: true,
      minlength: 6,
    }
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});
// Front Registration Validation
jQuery("#checkout_form").validate({  
  rules: { 
    choose_order: {
      required: true
    }, 
    payment_option__: {
      required: true
    },
    zipcode:{
      required:{
        depends: function(){
          if($("input[name='choose_order']:checked").val() == "delivery" && ($(".add_new_address").val() == 'add_new_address' || $("input[name='add_new_address']:checked").val() == 'add_new_address')) {
            return true;
          }
        }
      },
      minlength: 5,
      maxlength:6,
      digits: true
    },
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
      $(placement).append(error)
    } 
    else {
      if (element.attr('id') == "add_address_area" || element.attr('id') == "add_address" || element.attr('id') == "zipcode" || element.attr('id') == "your_address") {
        error.insertAfter(element);
      }
      else
      {
        error.insertBefore(element);
      }
    }
  }   
});
// Front Add Address Validation
jQuery("#form_add_address").validate({  
  rules: { 
    address_field: {
      required: true
    },
    zipcode: {
      required: true,
      minlength: 5,
      maxlength: 6,
      digits: true
    },
    city: {
      required: true,
    }
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});
jQuery("#frmPaymentRedirect").validate({  
  rules: {    
    billing_first_name: {
      required: true,
      //alpha:true
    },
    billing_mobile_phone: {
      required: true,
      //alpha:true
    },
    billing_email: {
      required: true,
    }
  }  
});
// admin email exist check
function checkEmailExist(email,entity_id){
  $.ajax({
    type: "POST",
    url: BASEURL+"backoffice/users/checkEmailExist",
    data: 'email=' + email +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html("User is already exist with this email id!");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}
$.validator.addMethod("alpha", function (value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z\s]*$/);
}, "Only alphabets and spaces are allowed.");
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
 /^[+-]?\d+$/
// custom code for lesser than
jQuery.validator.addMethod('lesserThan', function(value, element, param) {  
  return ( parseInt(value) <= parseInt(jQuery(param).val()) );
}, 'Must be less than close time' );
// custom code for greater than
$.validator.addMethod("greaterThan", function(value, element, param) {
  return ( parseInt(value) >= parseInt(jQuery(param).val()) );    
}, "Must be greater than open time");
// custom code for greater than
$.validator.addMethod("greater", function(value, element, param) {
  return ( parseInt(value) > parseInt(jQuery(param).val()));    
}, "Must be greater than Amount");
// custom password
$.validator.addMethod("phoneNumber",function(value,element)
{
  return this.optional(element) || /^[+]?\d+$/.test(value);
},"Please enter valid phone number");
// end here
// create a custom phone number rule called 'intlTelNumber'
jQuery.validator.addMethod("intlTelNumber", function(value, element) {
    if(typeof(phoneInput) !== "undefined") {
      var phoneInput_val = phoneInput.isValidNumber();
    } else {
      var phoneInput_val = false;
    }
    if(typeof(phoneInputOwn) !== "undefined") {
      var phoneInputOwn_val = phoneInputOwn.isValidNumber();
    } else {
      var phoneInputOwn_val = false;
    }
    return this.optional(element) || phoneInput_val || phoneInputOwn_val;
}, "Please enter valid phone number");
$.validator.addMethod("startWithZero",function(value,element)
{
  return this.optional(element) || value.indexOf('0')===0;
},"Phone number should start with 0");
const isNumericInput = (event) => {
    const key = event.keyCode;
    return ((key >= 48 && key <= 57) || // Allow number line
        (key >= 96 && key <= 105) // Allow number pad
    );
};
const isModifierKey = (event) => {
    const key = event.keyCode;
    return (event.shiftKey === true || key === 35 || key === 36) || // Allow Shift, Home, End
        (key === 8 || key === 9 || key === 13 || key === 46) || // Allow Backspace, Tab, Enter, Delete
        (key > 36 && key < 41) || // Allow left, up, right, down
        (
            // Allow Ctrl/Command + A,C,V,X,Z
            (event.ctrlKey === true || event.metaKey === true) &&
            (key === 65 || key === 67 || key === 86 || key === 88 || key === 90)
        )
};
const enforceFormat = (event) => {
    // Input must be of a valid number format or a modifier key, and not longer than ten digits
    if(!isNumericInput(event) && !isModifierKey(event)){
        event.preventDefault();
    }
};
const formatToPhone = (event) => {
    if(isModifierKey(event)) {return;}
    // I am lazy and don't like to type things more than once
    const target = event.target;
    const input = target.value.replace(/\D/g,'').substring(0,10); // First ten digits of input only
    const zip = input.substring(0,3);
    const middle = input.substring(3,6);
    const last = input.substring(6,10);
    if(input.length > 6){target.value = `(${zip}) ${middle}-${last}`;}
    else if(input.length > 3){target.value = `(${zip}) ${middle}`;}
    else if(input.length > 0){target.value = `(${zip}`;}
};
// Front Check Table Validation
jQuery("#check_table_availability").validate({  
  rules: {    
    datepicker: {
      required: true,
    }, 
    starttime: {
      required: true,
    },
    endtime: {
      required: true,
    },
    no_of_people: {
      required: true,
      digits:true,
    },
    first_name: {
      required: true,
      //alpha:true
    },
    last_name: {
      required: true,
      //alpha:true
    },
    phone_number_inp: {
      required: true,
      intlTelNumber: true,
    },
    email: {
      required: true,
      emailcustom: true,
      emailcus: true
    },
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    if (element.attr("id") == "datepicker") 
    {
      error.insertAfter('.sumo_datepicker');
    }else if(element.attr("id") == "starttime"){
      error.insertAfter('.sumo_starttime');
    }else if(element.attr("id") == "endtime"){
      error.insertAfter('.sumo_endtime');
    }else if(element.attr('id') == "phone_number_inp") {
      error.insertAfter('#event_phone_number_error'); 
    }else{
      var placement = $(element).data('error');
      if (placement) {
      $(placement).append(error)
      } else {
      error.insertAfter(element);
      }
    }
  }    
});
//add new card form
jQuery("#form_credit_card").validate({  
  rules: { 
    card_number: {
      required: true
    }, 
    card_month: {
      required: true,
      maxlength: 2,
      digits: true
    }, 
    card_year: {
      required: true,
      maxlength: 4,
      digits: true
    },
    card_cvv : {
      required: true,
      maxlength: 4,
      digits: true
    }, 
    card_zip : {
      required: true,
      maxlength: 6,
      minlength: 5,
      digits: true
    }, 
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});