<div class="page-footer">
    <div class="page-footer-inner">
          <?php echo $this->lang->line('copyright');?>&copy; <?php echo date('Y');?> <?php echo $this->lang->line('reserved');?> <?php echo $this->lang->line('site_footer');?>
    </div>
    <div class="page-footer-tools">
        <button onclick="topFunction()" id="myBtn" title="Go to top">
            <span class="go-top">
                <i class="fa fa-angle-up"></i>
            </span>
        </button>
    </div>
    <?php $notification = $this->common_model->is_notification_sound_enable(); ?>
    <input type="hidden" id="footer_notification_sound" value="<?php echo $notification->notification_sound; ?>">
</div>
<!-- END footer -->

<div id="safari-notification" style="display: none;"><p>You are using Safari on iOS. Tap <a href="javascript:void(0);" onclick="document.getElementById('safari-notification').style.display = 'none';">here</a> to autoplay notifications sound. </p></div>
</body>
<!-- END BODY -->
<script type="text/javascript">
/*function fakeClick(fn) {
    var $a = $('<a href="#" id="fakeClick"></a>');
        $a.bind("touchstart click", function(e) {
            e.preventDefault();
            fn();
        });

    $("body").append($a);

    var evt, 
        el = $("#fakeClick").get(0);

    if (document.createEvent) {
        evt = document.createEvent("MouseEvents");
        if (evt.initMouseEvent) {
            evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            el.dispatchEvent(evt);
        }
    }
    $(el).remove();
}*/
//Code for sound paly in safari :: Start
var chime = new Audio("<?php echo base_url() ?>assets/admin/img/notification_sound.wav")
    var allAudio = []                
    allAudio.push(chime);
    var tapped = function() {
        // Play all audio files on the first tap and stop them immediately.
        if(allAudio) {
            for(var audio of allAudio) {
                var playPromise = audio.play()
                audio.pause()
                if (playPromise !== undefined) {
                playPromise.then(_ => {
                  // Automatic playback started!
                  // Show playing UI.
                })
                .catch(error => {
                  // Auto-play was prevented
                  // Show paused UI.
                });
              }
              //audio.pause()
              audio.currentTime = 0
            }
            allAudio = null
        }        
        //chime.play()
    }
document.body.addEventListener('touchstart', tapped, false)
document.body.addEventListener('click', tapped, false)
//Code for sound paly in safari :: End
//var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
//var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

var standalone = window.navigator.standalone,
    userAgent = window.navigator.userAgent.toLowerCase(),
    isSafari = /safari/.test( userAgent ),
    isChrome = /chrome/.test( userAgent ),
    iOS = /iphone|ipod|ipad/.test( userAgent );

var visited = $.cookie("visited")
if (visited == null) {
    /*if ((isSafari && iOS)) {*/
    if ((iOS)) {    
        document.getElementById('safari-notification').style.display = 'block';
        //alert("You are using Safari on iOS! Close to enable notification sound!");
        //You are using Safari on iOS. Tap here to autoplay notifications sound.
    }         
}
// set cookie
$.cookie('visited', 'yes')

$(document).ready(function(){
    <?php if(in_array('order~view',$this->session->userdata("UserAccessArray"))) { ?>
        var i = setInterval(function(){
          jQuery.ajax({
            type : "POST",
            dataType : "json",
            async: false,
            url : '<?php echo base_url().ADMIN_URL?>/dashboard/ajaxNotification',
            success: function(response) {
                var past_count = $('.notification span.count').html();
                if(response != null){
                    if(response.order_count != '' && response.order_count != null && response.order_count!='0'){
                        if(past_count < response.order_count){
                            /*var sound = $("#footer_notification_sound").val();
                            if(sound == 1){
                                chime.play();
                            }*/
                            var order_url = $(location).attr('href');
                            order_url = order_url.split('<?php echo base_url() ?>').pop();
                            order_url = order_url.split("/").splice(1, 3).join("/");
                            if(order_url == 'dashboard') {
                                jQuery.ajax({
                                    type : "POST",
                                    dataType : "json",
                                    async: false,
                                    url : '<?php echo base_url().ADMIN_URL?>/dashboard/refreshOrderData',
                                    success: function(result) {
                                        $('#dashboard_order_count').html(result.dashboard_order_count);
                                        $('#dashboard_statistics').html(result.dashboard_statistics);
                                        $('#dashboard_order_grid').html(result.dashboard_order_grid);
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    }
                                });
                            }
                        }
                        var count = (response.order_count >= 100)?'99+':response.order_count;
                        $('.notification span.count').html(count);
                        var delivery_pickup_count = (response.delivery_pickup_count >= 100)?'99+':response.delivery_pickup_count;
                        $('.notification-dropdown-content span.order-count').html(delivery_pickup_count);
                        var dinein_count = (response.dinein_count >= 100)?'99+':response.dinein_count;
                        $('.notification-dropdown-content span.dinein-count').html(dinein_count);
                        var event_count = (response.event_count >= 100)?'99+':response.event_count;
                        $('.notification-dropdown-content span.event-count').html(event_count);
                        var tablebooking_count = (response.tablebooking_count >= 100)?'99+':response.tablebooking_count;
                        $('.notification-dropdown-content span.tablebooking-count').html(tablebooking_count);
                    }
                } 
                if(response.order_count == '0') {
                    var count = (response.order_count >= 100)?'99+':response.order_count;
                    $('.notification span.count').html(count);
                    var delivery_pickup_count = (response.delivery_pickup_count >= 100)?'99+':response.delivery_pickup_count;
                    $('.notification-dropdown-content span.order-count').html(delivery_pickup_count);
                    var dinein_count = (response.dinein_count >= 100)?'99+':response.dinein_count;
                    $('.notification-dropdown-content span.dinein-count').html(dinein_count);
                    var event_count = (response.event_count >= 100)?'99+':response.event_count;
                    $('.notification-dropdown-content span.event-count').html(event_count);
                    var tablebooking_count = (response.tablebooking_count >= 100)?'99+':response.tablebooking_count;
                    $('.notification-dropdown-content span.tablebooking-count').html(tablebooking_count);
                }
                if(response.placed_order_count > 0) {
                    var sound = $("#footer_notification_sound").val();
                    if(sound == 1){
                        chime.play();
                    }
                } else {
                    chime.pause();
                    var past_count = $('.notification span.count').html();
                    if(past_count > 0){
                        changeDeliveryPickupOrderViewStatus();
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
            }
          });
        },10000);
        /*var k = setInterval(function(){
            var past_count = $('.notification span.count').html();
            //var event_count = $('.event-notification span.event_count').html();
            if(past_count >= 1){ 
                var sound = $("#footer_notification_sound").val();
                if(sound == 1){
                    chime.play();
                }
            }
        },20000);*/
    <?php } ?>
    //mark orders delayed
    var l = setInterval(function(){
        jQuery.ajax({
            type : "POST",
            dataType : "json",
            async: false,
            url : '<?php echo base_url().ADMIN_URL?>/dashboard/markDelayedOrders',
            success: function(response) {
                var order_url = $(location).attr('href');
                order_url = order_url.split('<?php echo base_url() ?>').pop();
                order_url = order_url.split("/").splice(1, 3).join("/");
                if(order_url == 'order/view' && response.status == 1 && response.reload_flag == 1) {
                    grid.getDataTable().fnDraw();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
            }
        });
    },30000); //30sec
    //auto cancel orders
    var m = setInterval(function(){
        jQuery.ajax({
            type : "POST",
            dataType : "json",
            async: false,
            url : '<?php echo base_url().ADMIN_URL?>/dashboard/autoCancelOrders',
            success: function(response) {
                var order_url = $(location).attr('href');
                order_url = order_url.split('<?php echo base_url() ?>').pop();
                order_url = order_url.split("/").splice(1, 3).join("/");
                if(order_url == 'order/view' && response.status == 1 && response.reload_flag == 1) {
                    grid.getDataTable().fnDraw();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {           
            }
        });
    },20000); //20sec

});
function changeDeliveryPickupOrderViewStatus(){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/change_delivery_pickup_order_view_status',
        success: function(response) {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
    });
}
function changeDineinOrderViewStatus(){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/change_dinein_order_view_status',
        success: function(response) {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
    });
}
function changeEventStatus(){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/changeEventStatus',
        success: function(response) {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
    });
}
function changeTableBookingStatus(){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/changeTableBookingStatus',
        success: function(response) {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
    });
}
$(document).ready(function(){ 
    $(window).scroll(function(){ 
        if ($(this).scrollTop() > 100) { 
            $('#myBtn').fadeIn(); 
        } else { 
            $('#myBtn').fadeOut(); 
        } 
    }); 
    $('#myBtn').click(function(){ 
        $("html, body").animate({ scrollTop: 0 }, 1000); 
        return false; 
    }); 
});
</script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
</html>