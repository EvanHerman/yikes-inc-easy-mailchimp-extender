<?php

// Process our form submissions (non ajax forms)
if ( ! isset( $_POST['yikes_easy_mc_new_subscriber'] ) || ! wp_verify_nonce( $_POST['yikes_easy_mc_new_subscriber'], 'yikes_easy_mc_form_submit' ) ) {
   
	echo '<p><small class="form_submission_error">' . __( "Error : Sorry, the nonce security check didn't pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt." , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';
	return;
	
} else {

	/* Check for Honeypot filled */
	$honey_pot_filled = ( isset( $_POST['yikes-mailchimp-honeypot'] ) && $_POST['yikes-mailchimp-honeypot'] != '' ) ? true : false;
	// if it was filled out, return an error...
	if( $honey_pot_filled ) {
		echo '<p><small class="form_submission_error">' . __( "Error: It looks like the honeypot was filled out and the form was not properly be submitted." , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';
		return;
	}
	
	// Check reCaptcha Response
	if( isset( $_POST['g-recaptcha-response'] ) ) {
		$url = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER["REMOTE_ADDR"] );
		$response = wp_remote_get( $url );
		$response_body = json_decode( $response['body'] , true );
		// if we've hit an error, lets return the error!
		if( $response_body['success'] != 1 ) {
			$recaptcha_error = array(); // empty array to store error messages
			foreach( $response_body['erorr-codes'] as $error_code ) {
				$recaptcha_error[] = $error_code;
			}
			?>
			<p><?php _e( "It looks like we've run into a reCaptcha error. Please refresh the page and try again." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<p><?php echo __( 'Errors' , 'yikes-inc-easy-mailchimp-extender' ) . ': ' . implode( ' ' , $recaptcha_error ); ?></p>
			<?php
		}
	}
	
	/*
	*	Confirm that all required checkbox groups were submitted
	*	No HTML5 validation, and don't want to use jQuery for non-ajax forms
	*/
	$missing_required_checkbox_interest_groups = array();
	foreach( $fields as $merge_tag => $field_data ) {
		if( is_numeric( $merge_tag ) ) {
			// check if the checkbox group was set to required, if so return an error
			if( isset( $field_data['require'] ) && $field_data['require'] == 1 ) {
				if( $field_data['type'] == 'checkboxes' ) {
					if( ! isset( $_POST[$merge_tag] ) ) {
						$missing_required_checkbox_interest_groups[] = $merge_tag;
					}
				}
			}
		}
	}
	
	if( ! empty( $missing_required_checkbox_interest_groups ) ) {
		echo '<p class="yikes-easy-mc-error-message">' . apply_filters( 'yikes-mailchimp-interest-group-required-top-error', sprintf( _n( 'It looks like you forgot to fill in a required field.', 'It looks like you forgot to fill in %s required fields.', count( $missing_required_checkbox_interest_groups ), 'yikes-inc-easy-mailchimp-extender' ), count( $missing_required_checkbox_interest_groups ) ), count( $missing_required_checkbox_interest_groups ), $form_id ) . '</p>';
		return;
	}
	
	// Empty array to build up merge variables
	$merge_variables = array();	
	
	// loop to push variables to our array
	foreach ( $_POST as $merge_tag => $value ) {
		if( $merge_tag != 'yikes_easy_mc_new_subscriber' && $merge_tag != '_wp_http_referer' ) {
			if( is_numeric( $merge_tag ) ) { // this is is an interest group!
				$merge_variables['groupings'][] = array( 'id' => $merge_tag , 'groups' => ( is_array( $value ) ) ? $value : array( $value ) );
			} else { // or else it's just a standard merge variable
				$merge_variables[$merge_tag] = $value;
			}
		}
	}
	
	// store the opt-in time
	$merge_variables['optin_time'] = current_time( 'Y-m-d H:i:s', 1 );
	
	// Submit our form data
	$api_key = get_option( 'yikes-mc-api-key' , '' );
	// initialize MailChimp API
	$MailChimp = new MailChimp( $api_key );
	
	/*
	*	yikes-mailchimp-before-submission
	*	
	*	Catch the merge variables before they get sent over to MailChimp
	*	param @merge_variables - user submitted form data
	*	optional @form - the ID of the form to filter
	*	@since 6.0.0
	*/
	$merge_variables = apply_filters( 'yikes-mailchimp-before-submission' , $merge_variables );
	$merge_variables = apply_filters( 'yikes-mailchimp-before-submission-'.$form , $merge_variables );
	
	/*
	*	Allow users to check for submit value
	*	and pass back an error to the user
	*/
	if( isset( $merge_variables['error'] ) ) {
		echo apply_filters( 'yikes-mailchimp-frontend-content' , $merge_variables['message'] );
		return;
	}
	
	// submit the request & data, using the form settings
	try {
		
		$subscribe_response = $MailChimp->call('/lists/subscribe', apply_filters( 'yikes-mailchimp-user-subscribe-api-request', array( 
			'api_key' => $api_key,
			'id' => $_POST['yikes-mailchimp-associated-list-id'],
			'email' => array( 'email' => sanitize_email( $_POST['EMAIL'] ) ),
			'merge_vars' => $merge_variables,
			'double_optin' => $optin_settings['optin'],
			'update_existing' => $optin_settings['update_existing_user'],
			'send_welcome' => $optin_settings['send_welcome_email'],
			'replace_interests' => ( isset( $submission_settings['replace_interests'] ) ) ? $submission_settings['replace_interests'] : 1, // defaults to replace
		), $form, $_POST['yikes-mailchimp-associated-list-id'], $_POST['EMAIL'] ) );
		
		// set the global variable to 1, to trigger a successful submission
		$form_submitted = 1;
					
		?> <p class="yikes-easy-mc-success-message"><?php
			// Display the success message
			if( ! empty( $error_messages['success'] ) ) {
				echo  stripslashes( esc_html( $error_messages['success'] ) );
			} else {
				$default_success_response = ( $optin_settings['optin'] == 1 ) ? __( "Thank you for subscribing! Check your email for the confirmation message." , 'yikes-inc-easy-mailchimp-extender' ) : __( "Thank you for subscribing!" , 'yikes-inc-easy-mailchimp-extender' );
				echo $default_success_response;
			}
		?></p> <?php
		
		/*
		*	yikes-mailchimp-after-submission
		*	
		*	Catch the merge variables after they've been sent over to MailChimp
		*	param @merge_variables - user submitted form data
		* 	optional @form - the ID of the form to filter
		*	@since 6.0.0
		*/
		do_action( 'yikes-mailchimp-after-submission' , $merge_variables );
		do_action( 'yikes-mailchimp-after-submission-'.$form , $merge_variables );
		
		/*
		*	Non-AJAX redirects now handled in class-yikes-inc-easy-mailchimp-extender-public.php
		*	function: redirect_user_non_ajax_forms
		*/
		
		/*
		*	yikes-mailchimp-form-submission
		*	
		*	Do something with the email address, merge variables,
		*	form ID or notifications
		*	@$_POST['EMAIL'] - users email address
		*	@$merge_variables - the merge variables attached to the form ie. form field
		*	@$form - the form ID
		*	@$notifications - the notification array
		*	@since 6.0.0
		*/
		do_action( 'yikes-mailchimp-form-submission' , $_POST['EMAIL'] , $merge_variables , $form , $notifications );
		do_action( 'yikes-mailchimp-form-submission-' . $form , $_POST['EMAIL'] , $merge_variables , $form , $notifications );
		
		/*
		*	Increase the submission count for this form
		*	on a successful submission
		*	@since 6.0.0
		*/
		$form_data['submissions']++;
		$wpdb->update( 
			$wpdb->prefix . 'yikes_easy_mc_forms',
				array( 
					'submissions' => $form_data['submissions'],
				),
				array( 'ID' => $form ), 
				array(
					'%d',	// send welcome email
				), 
				array( '%d' ) 
			);
			
		// end successful submission
		
	} catch ( Exception $error ) { // Something went wrong...
		$error_response = $error->getMessage();
			?> <p class="yikes-easy-mc-error-message"><?php
			if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				// If a field exists on the form, is required but isn't being displayed (current displays like "8YBR1 must be provided" , should be more user friendly)
				if( strpos( $error_response, 'must be provided' ) !== false ) {
					$boom = explode( ' ', $error_response );
					$merge_variable = $boom[0];
					$api_key = get_option( 'yikes-mc-api-key' , '' );
					$MailChimp = new MailChimp( $api_key );
					try {	
						$available_merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $_POST['yikes-mailchimp-associated-list-id'] ) ) );
						foreach( $available_merge_variables['data'][0]['merge_vars'] as $merge_var ) {
							if( $merge_var['tag'] == $merge_variable ) {
								$field_name = $merge_var['name'];
							}
						}
						$error_response = str_replace( $merge_variable , '<strong>"' . $field_name . '"</strong>' , $error_response );
						echo $error_response;
					} catch ( Exception $e ) {
						echo $e->getMessage();
					}
				} else {
					echo $error_response;
				}
			} else {
				if ( strpos( $error_response, 'should include an email' ) !== false ) {  // include a valid email please
					echo !empty( $error_messages['invalid-email'] ) ? $error_messages['invalid-email'] :  __( 'Please enter a valid email address.' , 'yikes-inc-easy-mailchimp-extender' );
				} else if ( strpos( $error_response, 'already subscribed' ) !== false ) { // user already subscribed
					echo !empty( $error_messages['already-subscribed'] ) ? $error_messages['already-subscribed'] : __( "It looks like you're already subscribed to this list." , 'yikes-inc-easy-mailchimp-extender' );
				} else { // general error
					echo !empty( $error_messages['general-error'] ) ? $error_messages['general-error'] : __( "Whoops, something went wrong! Please try again." , 'yikes-inc-easy-mailchimp-extender' );
				}
			}
			?></p> <?php
	}	
	
}
?>