// JavaScript Document
jQuery.noConflict();
(function ($) {
    $(function () {

$( document ).ajaxComplete(function( event,request, settings ) {
                    $('#facebook_events_wrap .fbecol').hover(function() {
                           $(this).addClass('fbecolhover');  
                           $(this).removeClass('fbecolhoverOut');
                         }, function() {
                             $(this).removeClass('fbecolhover');
                             $(this).addClass('fbecolhoverOut');
                         });

                        $('.fbecol').click(function() {
                            url = $(this).data('id');
                            location.href = url;  
                         });



                         $('.fbe-sidebar-post').hover(function() {
                           $(this).addClass('fbecolhover');  
                           $(this).removeClass('fbecolhoverOut');
                         }, function() {
                             $(this).removeClass('fbecolhover');
                             $(this).addClass('fbecolhoverOut');
                         });

                        $('.fbe-sidebar-post').click(function() {
                            url = $(this).data('id');
                            location.href = url;  
                         });
});


        $(document).ready(function () {




        load_fbe_events();


        var mainColor = $('a').css("color");
        if($('.fbe_feat_event_link').length){
        $('.fbe_feat_event_link').css({"background-color":mainColor});
        }
         if($('#load_more_fbe').length){
         $('#load_more_fbe').hover(function() {
         $('#load_more_fbe').css({"background-color":mainColor});
         }, function() {
            $('#load_more_fbe').css({"background-color":'#222222'});
            });
         }
        


        $('.fbe_list_date').css({"background-color":mainColor});

         


function load_fbe_events(){
            $.ajax({
                   url: fbeAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action :'load_facebook_events',
                        page: 1,
                        
                    },
                    success: function (data) { 

                     $('#facebook_events_wrap').append(data);
                     $('.fbe_list_date').css({"background-color":mainColor}); 
                      $('#load_more_fbe').data("id",2);       
                    },
                    error: function () {
                    
                    }
                });

           }     





            $('#load_more_fbe').click(function() {

                $.ajax({
                   url: fbeAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action :'load_facebook_events',
                        //maxPosts: $('#load_more_fbe').data("show"),
                        page: $('#load_more_fbe').data("id")
                        
                    },
                    success: function (data) { 

                    // $('#facebook_events_wrap').append(data);
                     $(data).hide().appendTo("#facebook_events_wrap").fadeIn(600);



                     $('.fbe_list_date').css({"background-color":mainColor}); 
                      nxt = $('#load_more_fbe').data("id") +1;
                      maxPages = $('#maxPages').data("id");
                      $('#load_more_fbe').data("id",nxt);

                      if(nxt-1 >= maxPages){
                        $('#load_more_fbe').hide();
                      } 
                    
                    },
                    error: function () {
                    
                    }
                });

                return false;
            });



        });
    });
})(jQuery);