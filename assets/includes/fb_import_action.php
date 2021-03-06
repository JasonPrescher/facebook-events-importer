<?php 
define('FACEBOOK_SDK_V4_SRC_DIR',  __DIR__ .'/facebook-php-sdk-v4-4.0-dev/src/Facebook/');
require __DIR__ .'/facebook-php-sdk-v4-4.0-dev/autoload.php';


use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;


function fbe_validate_session($appID,$appSecret){

FacebookSession::setDefaultApplication($appID, $appSecret);

// If you already have a valid access token:
$session = new FacebookSession($appID.'|'.$appSecret);


// To validate the session:
try {
  $session->validate();
  echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">Settings Saved</div>';	

} catch (FacebookRequestException $ex) {
  // Session not valid, Graph API returned an exception with the reason.
  echo '<div class="error" style="color:#222222; font-weight:700; font-size:1em; padding:10px">'.$ex->getMessage().'</div>';
} catch (\Exception $ex) {
  // Graph API returned info, but it may mismatch the current app or have expired.
  echo '<div class="error" style="color:#222222; font-weight:700; font-size:1em; padding:10px">'.$ex->getMessage().'</div>';
}

}

function fbe_facebook_sdk($facebook_page,$appID,$appSecret){


FacebookSession::setDefaultApplication($appID,$appSecret);

$session = FacebookSession::newAppSession();

try {
  $session->validate();
} catch (FacebookRequestException $ex) {
  echo $ex->getMessage();
} catch (\Exception $ex) {
  echo $ex->getMessage();
}


    $i=0;
    $u=0;

	try{
	
	$eventResponse = (new FacebookRequest($session, 'GET', '/'.$facebook_page.'/events?fields=place,cover,attending_count,description,end_time,id,name,owner,start_time,ticket_uri,timezone,is_date_only'))->execute()->getResponse();

	$events = $eventResponse->data;

  function offset_date_to_timezone( &$datetime, $timezone = 'GMT', $return_format = 'c' ){
  	if($datetime){
	$gmt_offset = get_option( 'gmt_offset' );
	$datetime_obj = new DateTime( $datetime, new DateTimeZone( $timezone ) );
	$datetime_obj->setTimezone(new DateTimeZone('GMT'));
	$datetime_obj->modify( $gmt_offset . ' hours' );
	return $datetime_obj->format( $return_format );
	}
}

	foreach ($events as $e) {	


$startDate = offset_date_to_timezone( $e->start_time, 'America/Los_Angeles' );
$endDate = offset_date_to_timezone( $e->end_time, 'America/Los_Angeles' );

	    $event_id = $e->id;
	    $eId = wp_strip_all_tags($e->id);	
		$city = $e->place->location->city;
		$state = $e->place->location->state;
		$zip = $e->place->zip;
		$street = $e->place->street;
		$region = $e->place->region;
		$country = $e->place->location->country;
		$ticket_uri = $e->ticket_uri;
		$eventImage = $e->cover->source;
		$name = $e->place->name;
		$fb_event_uri = 'https://www.facebook.com/events/'.$event_id;
  
   
    if($state){
    	$state = $state.'&nbsp;';
    }
    if($street){
    	$street = $street.',';
    }
    if($city){
    	$city = $city.',&nbsp;';
    }
    if($country){
    	$country = $country.'&nbsp;';
    }
    if($region){
    	$region = $region.'&nbsp;';
    }
     if($zip){
    	$zip = $zip.'&nbsp;';
    }

    $location = $street.$city.$state.$zip.$country.$region ;

    if($location == ''){$location = $name;}
   
	$args = array (
    'post_type' => 'facebook_events',
	'posts_per_page' => -1,
	'meta_key' => 'facebook_event_id',
    'meta_query' => array(
	        'key'		=> 'facebook_event_id',
	        'value'		=> $event_id,
    ),
);
	$loop = new WP_Query( $args );
   if( $loop->have_posts() ){ 
      $u++;	
   	  while ( $loop->have_posts() ) : $loop->the_post();
		
	  $post_id = get_the_ID();
	  $curEventImage = get_post_meta( $post_id, 'image_url', true );
	  $post_information = array(
	  	    'post_type' => 'facebook_events',
	        'ID' => $post_id,
			'post_title' => wp_strip_all_tags($e->name),
			'post_content' => wp_strip_all_tags($e->description),
					 );
             
             if($eventImage != $curEventImage ){insert_image($eventImage,$post_id); }
			 
	  		 wp_update_post( $post_information );
	   endwhile;
	 }else{ 
	 $post_information = array(
			'post_type' => 'facebook_events',
			'post_title' => wp_strip_all_tags($e->name),
			'post_content' => wp_strip_all_tags($e->description),
			'post_status' => 'publish'
					 );		
	        
	         $post_id = wp_insert_post( $post_information ); 
	         insert_image($eventImage,$post_id); 
	        
	     }

        $i++;
		 
  

		update_post_meta($post_id,'start_time',$startDate);
		update_post_meta($post_id,'event_starts_formatted',$startDate); 
		update_post_meta($post_id,'event_ends_formatted',$endDate);
		update_post_meta($post_id,'end_time', $endDate);
		update_post_meta($post_id,'facebook_event_id', $eId);
		update_post_meta($post_id,'location',$location);
		update_post_meta($post_id,'ticket_uri',$ticket_uri);
		update_post_meta($post_id,'fb_event_uri',$fb_event_uri);
	
	 }  



	 $a ='';
	 $c ='';
	 $n ='';

     $error ='';
	 if($u > 1){
	 	$c = $u;
	 	$a = $i-$u;
	 	$n = 'updated';
	 }else{
	 	$c = $i;
	 	$n = 'imported';
	 }

	} catch(Exception $ex){

	$error  = $ex->getCode();
	$errorMsg  = $ex->getMessage();

	if($error == 100 ){
		echo '<div class="error" style="color:#222222; font-weight:700; font-size:1em; padding:10px">Error '.$error.': <a href="https://www.facebook.com/'.$facebook_page.'/events" target="_blank">'.$facebook_page.'</a>. <i> Country or age restricted material. Check your app settings.</i> </div>';
	}else if ($error == 102){	
		echo '<div class="error" style="color:#222222; font-weight:700; font-size:1em; padding:10px">Error '.$error.': Session key invalid or no longer valid.</div>';
	}else{
		echo '<div class="error" style="color:#222222; font-weight:700; font-size:1em; padding:10px">Error '.$error.'&nbsp;'.$errorMsg .'&nbsp;: Troubleshooting tip <a href="https://developers.facebook.com/docs/marketing-api/error-reference" target="_blank">View API Error Codes</a></div>';
	}

	}
	if($a >= 1){
	echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">'.$a. ' events added</div>';
	}
	if($i > 1){
	echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">'.$c . ' events '.$n.'</div>';
	}
	else if($i == 0){
		 if($error != 100){
		 echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">There are <b>no upcoming</b> events to import at <a href="https://www.facebook.com/'.$facebook_page.'/events" target="_blank">'.$facebook_page.'</a></div>';
	    	}
	}else{
	echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">'.$c . ' event '.$n.'</div>';
	}

	}

	function insert_image($eventImage,$post_id){
	 	$image = $eventImage;

			$media = media_sideload_image($image, $post_id);

			if(!empty($media) && !is_wp_error($media)){
			    $args = array(
			        'post_type' => 'attachment',
			        'posts_per_page' => -1,
			        'post_status' => 'any',
			        'post_parent' => $post_id
			    );

			    $attachments = get_posts($args);

			    if(isset($attachments) && is_array($attachments)){
			        foreach($attachments as $attachment){
			            $image = wp_get_attachment_image_src($attachment->ID, 'full');
			            if(strpos($media, $image[0]) !== false){
			                set_post_thumbnail($post_id, $attachment->ID);
			                break;
			            }
			        }
			    }
			}
		update_post_meta($post_id,'image_url',$eventImage);

	 }

?>