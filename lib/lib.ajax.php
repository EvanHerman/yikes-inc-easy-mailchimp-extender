<?php
add_action('wp_ajax_yks_mailchimp_form', 'ykseme_ajaxActions');
add_action('wp_ajax_nopriv_yks_mailchimp_form', 'ykseme_ajaxActions');
function ykseme_ajaxActions()
	{
	global $yksemeBase;
	require_once YKSEME_PATH.'process/ajax.php';
	exit;
	}
	
// MailChimp API key validation function	
add_action('wp_ajax_yks_mailchimp_validate_api', 'ykseme_validateAPI');
add_action('wp_ajax_nopriv_yks_mailchimp_validate_api', 'ykseme_validateAPI');
function ykseme_validateAPI()
	{
		$apiKey = $_POST['api_key'];
		$dataCenter = $_POST['data_center'];
			
		$resp = wp_remote_get( "http://".$dataCenter.".api.mailchimp.com/2.0/?output=json&method=ping&apikey=".$apiKey); 
		
		// if there is an error with the $resp variable
		// display the error
		if ( is_wp_error( $resp ) ) {
		   $error_string = $resp->get_error_message();
		   echo $error_string;
		} else {	
			// Check errors with Mailchimp API
			if ( isset($resp) && 200 == $resp['response']['code'] ) {
				echo 'valid';
			}
		}
		
		wp_die();
	}	
	
	
?>