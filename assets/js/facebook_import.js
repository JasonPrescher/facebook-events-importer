// JavaScript Document
jQuery.noConflict();
(function ($) {
    $(function () {
        $(document).ready(function () {

   

             $( "#event_starts_formatted" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function( selectedDate ) {
                    $( "#event_ends_formatted" ).datepicker( "option", "minDate", selectedDate );
                    }
                    });
                    $( "#event_ends_formatted" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function( selectedDate ) {
                    $( "#event_starts_formatted" ).datepicker( "option", "maxDate", selectedDate );
                    }
                    });
			
			$('.fetch').click(function() {
				   $('#facebook_event_import .loader').fadeIn();
					fb_page = $(this).data('id');
					$('#facebook_page_updated').val(fb_page);
                    fetch_button(fb_page);
					
                $.ajax({
                   url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
						action :'facebook_events_update',
						page: $('#facebook_page_updated').val(),
						
                    },
                    success: function (data) {		
			        $('#event_results').html(data);
                    $('#facebook_event_import .loader').fadeOut();
                    },
                    error: function () {
					$('#event_results').html('error')
                    }
                });

                return false;
            });



            function fetch_button(id){
                     element = $('[data-id=' +id+ ']');
            }
			
			$('.remove').click(function() {

					fb_page = $(this).data('id');
                    $('#facebook_page_updated').val(fb_page);
                    element = $('[data-id=' +fb_page+ ']');
                    $(element).parent().fadeOut();  
					
                $.ajax({
                   url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
						action :'facebook_events_remove',
						page: $('#facebook_page_updated').val(),
						
                    },
                    success: function (data) {	
						 
                    },
                    error: function () {
					$('#event_results').html('error');
                    }
                });

                return false;
            });
 
 
            $('#facebook_event_import').submit(function (event) {
                $('#facebook_event_import .loader').fadeIn();
                event.preventDefault();
                $.ajax({
                   url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
						action : 'facebook_events_request',
						page: $('#facebook_page').val(),
                        pages: $('#facebook_pages').val()
						
                    },
                    success: function (data) {
					    $('#facebook_event_import .loader').fadeOut();
						$('#event_results').html(data);
                        $('#event_results').fadeIn(300);
     
	
                    },
                    error: function () {
						$('#event_results').html('error');
		
                    }
                });

                return false;
            });


            $('#wfei_plugin_settings').submit(function (event) {
                                        event.preventDefault();
                    

                if ($('#facebook_events_template').is(":checked")){
                    var option = 'yes';
                    }else{ 
                    var option = 'no'; 
                     }
                 $('#facebook_events_template').val(option);
               
                $.ajax({
                   url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
                        action : 'wfei_plugin_settings',
                        slug: $('#slug').val(),
                        facebook_events_template: $('#facebook_events_template').val(),
                        fbe_posts_per_page:$('#fbe_posts_per_page').val(),
                
                    },
                    success: function (data) {
                        
                        $('#plugin_settings').html(data); 
                     
                    },
                    error: function () {
                        
        
                    }
                });

                return false;
            });



            $('#facebook_app').submit(function (event) {


                event.preventDefault();
                $.ajax({
                   url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
                        action : 'facebook_app_data',
                        app_id: $('#app_id').val(),
                        app_secret: $('#app_secret').val()
                        
                    },
                    success: function (data) {
                        $('#appdata_results').html(data);
                    },
                    error: function () {
                        $('#appdata_results').html('error');
        
                    }
                });

                return false;
            });




        });
    });
})(jQuery);