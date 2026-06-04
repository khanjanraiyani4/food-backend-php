$(document).on('ready', function() {
    Modernizr.on('webp', function(result) {
      if (result) {
        // supported
      } else {
          $(this).addClass('no-webp');
      }
    });
    $(".mobile-icon  button").on("click", function(e){
          $("#example-one").toggleClass("open");
          $(this).toggleClass('open');
          if($("#example-one").hasClass("open") === true && $(".mobile-icon button").hasClass("open") === true){
            $(".noti-popup").removeClass("open");
            $(".header-user-menu").removeClass("open");
          }
          e.stopPropagation()
    });
    $(".notification-btn").on("click", function(e){
          $(".noti-popup").toggleClass("open");
          $(this).toggleClass('open');
          if($(".noti-popup").hasClass("open") === true){
            $(".mobile-icon  button").removeClass("open");
            $("#example-one").removeClass("open");
            $(".header-user-menu").removeClass("open");
          }
          e.stopPropagation()
    });
    $(".for_header_popup").on("click", function(e){
          if($(".noti-popup").hasClass("open") === true || $("#example-one").hasClass("open") === true || $(".mobile-icon button")
            .hasClass("open") === true || $(".header-user-menu").hasClass("open") === true){
            $(".mobile-icon  button").removeClass("open");
            $(".noti-popup").removeClass("open");
            $("#example-one").removeClass("open");
            $(".header-user-menu").removeClass("open");
          }
    });
    $(".user-menu-btn").on("click", function(e){
          $(".header-user-menu").toggleClass("open");
          if($(".header-user-menu").hasClass("open") === true){
            $(".mobile-icon  button").removeClass("open");
            $(".noti-popup").removeClass("open");
            $("#example-one").removeClass("open");
          }
          e.stopPropagation()
    });
    /*$(".header-user-menu").on("click", function(e){
         e.stopPropagation();
    });*/
});
$(document).on('ready', function() {
  if(current_pagejs != 'OrderFood'){
    var $el, leftPos, newWidth,
        $mainNav = $("#example-one");
      $mainNav.append("<li id='magic-line'></li>");
      var $magicLine = $("#magic-line");
      if ($(".current_page_item").length > 0) {
        $magicLine
          .width($(".current_page_item").width())
          .css("left", $(".current_page_item a").position().left)
      }
    
      $magicLine
          //.width($(".current_page_item").width())
          //.css("left", $(".current_page_item a").position().left)
          .data("origLeft", $magicLine.position().left)
          .data("origWidth", $magicLine.width());
        
    $("#example-one li a").hover(function() {
        $el = $(this);
        leftPos = $el.position().left;
        newWidth = $el.parent().width();
          $magicLine.stop().animate({
              left: leftPos,
              width: newWidth
          });
    }, function() {
        $magicLine.stop().animate({
            left: $magicLine.data("origLeft"),
            width: $magicLine.data("origWidth")
        });   
    });
  }
});
new WOW().init();
/* ========================================== 
scrollTop() >= 100
Should be equal the the height of the header
========================================== */
$(window).scroll(function(){
    if ($(window).scrollTop() >= 180) {
        $('.header-area').addClass('fixed-header');
        $('header').parent('body').addClass('fixed');
    }
    else {
        $('.header-area').removeClass('fixed-header');
        $('header').parent('body').removeClass('fixed');
    }
});
$(document).ready(function() {
    $('.minus').on("click", function () {
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) - 1;
      count = count < 1 ? 1 : count;
      if(count < 1 || isNaN(parseInt(count)) ){
        count = 1;
      }      
      $input.val(count);
      $input.change();
      $('#peepid').html('<strong>'+count+' People</strong>');
      return false;
    });
    $('.plus').on("click", function () {
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) + 1;
      if(count < 1 || isNaN(parseInt(count)) ){
        count = 1;
      }
      if(count > 9999){
        count = 9999;
      }
      $input.val(count);
      $input.change();
      $('#peepid').html('<strong>'+count+' People</strong>');
      return false;
    });
});

