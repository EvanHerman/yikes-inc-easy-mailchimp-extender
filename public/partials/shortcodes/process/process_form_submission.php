<?php

// Process our form submissions (both ajax and non ajax forms)

if ( ! isset( $_POST['yikes_easy_mc_new_subscriber'] ) || ! wp_verify_nonce( $_POST['yikes_easy_mc_new_subscriber'], 'yikes_easy_mc_form_submit' ) ) {
   
	echo '<p><small style="color:rgb(230, 114, 114);font-weight:800;">' . __( "Error" , $text_domain ) . ' : ' . __( "Sorry, the nonce security check didn't pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt." , $text_domain ) . '</small></p>';
	return;
	
} else {
	
	// Empty array to build up merge variables
	$merge_variables = array();
	
	// loop to push variables to our array
		// to do - loop over each file type to built the correct type of array
	foreach ( $_POST as $merge_tag => $value ) {
		if( $merge_tag != 'yikes_easy_mc_new_subscriber' && $merge_tag != '_wp_http_referer' ) {
			$merge_variables[$merge_tag] = $value;
		}
	}
  
	// Submit our form data
	$api_key = get_option( 'yikes-mc-api-key' , '' );
	// initialize MailChimp API
	$MailChimp = new MailChimp( $api_key );
	// submit the request & data, using the form settings
	try {
		
		$subscribe_response = $MailChimp->call('/lists/subscribe', array( 
			'api_key' => $api_key,
			'id' => $list_id,
			'email' => array( 'email' => sanitize_email( $_POST['EMAIL'] ) ),
			'merge_vars' => $merge_variables,
			'double_optin' => $optin_settings['optin'],
			'update_existing' => $optin_settings['update_existing_user'],
			'send_welcome' => $optin_settings['send_welcome_email']
		) );
		
		// set the global variable to 1, to trigger a successful submission
		$form_submitted = 1;
					
		?> <p class="yikes-easy-mc-success-message"><?php
			// Display the success message
			if( !empty( $error_messages['success-message'] ) ) {
				$error_messages['success-message'];
			} else {
				echo __( 'Thank you for subscribing!' , $text_domain );
			}
		?></p> <?php
		
		/*
		*	Successful form submission redirect
		*/
		if( $submission_settings['redirect_on_submission'] == '1' ) {
			 echo '<script type="text/javascript">setTimeout(function() { window.location="' . get_permalink( $submission_settings['redirect_page'] ) . '"; }, ' . apply_filters( 'yikes-easy-mc-redirect-timer' , 1500 ) . ');</script>';
		}
		
		// end successful submission
		
	} catch ( Exception $error ) { // Something went wrong...
		$error_response = $error->getMessage();
		?> <p class="yikes-easy-mc-error-message"><?php
		if ( strpos( $error_response, 'should include an email' ) !== false ) {  // include a valid email please
			echo !empty( $error_messages['invalid-email'] ) ? $error_messages['invalid-email'] :  __( 'Please enter a valid email address.' , $text_domain );
		} else if ( strpos( $error_response, 'already subscribed' ) !== false ) { // user already subscribed
			echo !empty( $error_messages['email-already-subscribed'] ) ? $error_messages['email-already-subscribed'] : __( "It looks like you're already subscribed to this list." , $text_domain );
		} else { // general error
			echo !empty( $error_messages['general-error'] ) ? $error_messages['general-error'] : __( "Whoops, something went wrong! Please try again." , $text_domain );
		}
		?></p> <?php
	}	
	
}

?>