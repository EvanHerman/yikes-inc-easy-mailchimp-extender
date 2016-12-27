<?php
/*
*	Process Non-Ajax forms
*	@Updated for v6.0.3.5
*/

// set the global variable to 1, to trigger a successful submission
global $form_submitted, $process_submission_response;

// confirm we have a form id to work with
$form_id = ( ! empty( $_POST['yikes-mailchimp-submitted-form'] ) ) ? $_POST['yikes-mailchimp-submitted-form'] : false;

if ( ! $form_id ) {

	return;

}

$list_id = $_POST['yikes-mailchimp-associated-list-id'];

$form_settings = Yikes_Inc_Easy_Mailchimp_Extender_Public::yikes_retrieve_form_settings( $_POST['yikes-mailchimp-submitted-form'] );

/**
 * Setup whether or not we should update the user, or display the error with email generation
 * @since 6.1
 */
$update_existing_user = isset( $form_settings['optin_settings']['update_existing_user'] ) ? $form_settings['optin_settings']['update_existing_user'] : 0;

$replace_interests    = isset( $form_settings['submission_settings']['replace_interests'] ) ? (bool) $form_settings['submission_settings']['replace_interests'] : true;

$groups = array();

// If the user intends to replace existing interst groups, loop and set them all to false to start.
if ( $replace_interests ) {

	$list_handler = yikes_get_mc_api_manager()->get_list_handler();

	$interest_groupings = $list_handler->get_interest_categories( $_POST['yikes-mailchimp-associated-list-id'] );

	foreach ( $interest_groupings as $group_id => $group_data ) {

		foreach ( $group_data['items'] as $item_id => $item_data ) {

			$groups[ $item_id ] = false;

		}
	}
}

// Process our form submissions (non ajax forms)
if ( ! isset( $_POST['yikes_easy_mc_new_subscriber'] ) || ! wp_verify_nonce( $_POST['yikes_easy_mc_new_subscriber'], 'yikes_easy_mc_form_submit' ) ) {

	$process_submission_response = '<p><small class="form_submission_error">' . __( "Error : Sorry, the nonce security check didn't pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt." , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';

	return;

} else {

	/* Check for Honeypot filled */
	$honey_pot_filled = ( isset( $_POST['yikes-mailchimp-honeypot'] ) && $_POST['yikes-mailchimp-honeypot'] != '' ) ? true : false;

	// if it was filled out, return an error...
	if ( $honey_pot_filled ) {

		$process_submission_response = '<p><small class="form_submission_error">' . __( "Error: It looks like the honeypot was filled out and the form was not properly be submitted." , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';

		return;

	}

	// Check reCAPTCHA Response
	if( isset( $_POST['g-recaptcha-response'] ) ) {
		$url = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER["REMOTE_ADDR"] );
		$response = wp_remote_get( $url );
		$response_body = json_decode( $response['body'] , true );

		// if we've hit an error, lets return the error!
		if( $response_body['success'] != 1 ) {
			$recaptcha_error = array(); // empty array to store error messages
			foreach( $response_body['error-codes'] as $error_code ) {
				if( $error_code == 'missing-input-response' ) {
					$error_code = __( 'Please check the reCAPTCHA field.', 'yikes-inc-easy-mailchimp-extender' );
				}
				$recaptcha_error[] = $error_code;
			}
			$process_submission_response .= "<p class='yikes-easy-mc-error-message'>" . apply_filters( 'yikes-mailchimp-recaptcha-required-error', __( 'Error' , 'yikes-inc-easy-mailchimp-extender' )  . ': ' . implode( ' ' , $recaptcha_error ) ) . "</p>";
			return;
		}
	}

	/*
	*	Confirm that all required checkbox groups were submitted
	*	No HTML5 validation, and don't want to use jQuery for non-ajax forms
	*/
	$missing_required_checkbox_interest_groups = array();
	foreach( $form_settings['fields'] as $merge_tag => $field_data ) {
		if( isset( $field_data['group_id'] ) ) {
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
		$process_submission_response = '<p class="yikes-easy-mc-error-message">' . apply_filters( 'yikes-mailchimp-interest-group-required-top-error', sprintf( _n( 'It looks like you forgot to fill in a required field.', 'It looks like you forgot to fill in %s required fields.', count( $missing_required_checkbox_interest_groups ), 'yikes-inc-easy-mailchimp-extender' ), count( $missing_required_checkbox_interest_groups ) ), count( $missing_required_checkbox_interest_groups ), $form_id ) . '</p>';
		return;
	}

	// Empty array to build up merge variables & interest groups
	$merge_variables = array();

	// loop to push variables to our array
	foreach ( $_POST as $merge_tag => $value ) {
		if( $merge_tag != 'yikes_easy_mc_new_subscriber' && $merge_tag != '_wp_http_referer' ) {
			// check if the current iteration has a 'date_format' key set
			// (aka - date/birthday fields)
			if( isset( $form_settings['fields'][$merge_tag]['date_format'] ) ) {
				// check if EU date format
				if( $form_settings['fields'][$merge_tag]['date_format'] == 'DD/MM/YYYY' ) {
					// convert '/' to '.' and to UNIX timestamp
					$value = ( '' != $value ) ? date( 'Y-m-d', strtotime( str_replace( '/', '.', $value ) ) ) : '';
				} else {
					// convert to UNIX timestamp
					$value = ( '' != $value ) ? date( 'Y-m-d', strtotime( $value ) ) : '';
				}
			}
			if ( strpos( $merge_tag, 'group-' ) !== false ) { // this is is an interest group!

				$tag = str_replace( 'group-', '', $merge_tag );

				if ( is_array( $value ) ) {

					foreach ( $value as $val ) {

						$groups[ $val ] = true;

					}

					continue;

				}

				$groups[ $value ] = true;

			} else { // or else it's just a standard merge variable

				$merge_variables[ $merge_tag ] = $value;

			}
		}
	}

	// store the opt-in time
	$merge_variables['optin_time'] = current_time( 'Y-m-d H:i:s', 1 );

	// Submit our form data
	$api_key = yikes_get_mc_api_key();
	$dash_position = strpos( $api_key, '-' );

	// setup the end point
	if( $dash_position !== false ) {

		$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5( strtolower( sanitize_email( $_POST['EMAIL'] ) ) );

	}

	/*
	*	yikes-mailchimp-before-submission
	*
	*	Catch the merge variables before they get sent over to MailChimp
	*	param @merge_variables - user submitted form data
	*	optional @form - the ID of the form to filter
	*	@since 6.0.0
	*/
	$merge_variables = apply_filters( 'yikes-mailchimp-before-submission', $merge_variables );
	$merge_variables = apply_filters( 'yikes-mailchimp-before-submission-' . $form_id, $merge_variables );

	/**
	* Action hooks fired before API request
	* @since 6.0.5.5
	* @param $merge_variables 	array 	Array of merge variable to use
	* @param $form_id						integer	The form ID to target (eg: 1, 2 etc.)
	*/
	do_action( 'yikes-mailchimp-before-submission', $merge_variables );
	do_action( 'yikes-mailchimp-before-submission-' . $form_id, $merge_variables );

	/*
	*	Allow users to check for submit value
	*	and pass back an error to the user
	*/
	if( isset( $merge_variables['error'] ) ) {
		$process_submission_response = apply_filters( 'yikes-mailchimp-frontend-content' , $merge_variables['message'] );
		return;
	}

	// submit the request & data, using the form settings
		// subscribe the user
		/* $subscribe_response = wp_remote_post( $api_endpoint, array(
			'body' => apply_filters( 'yikes-mailchimp-user-subscribe-api-request', array(
				'apikey' => $api_key,
				'id' => $_POST['yikes-mailchimp-associated-list-id'],
				'email' => array( 'email' => sanitize_email( $_POST['EMAIL'] ) ),
				'merge_vars' => $merge_variables,
				'double_optin' => $form_settings['optin_settings']['optin'],
				'update_existing' => $update_existing_user, // Decide if we should update the user or not
				'send_welcome' => $form_settings['optin_settings']['send_welcome_email'],
				'replace_interests' => ( isset( $form_settings['submission_settings']['replace_interests'] ) ) ? $form_settings['submission_settings']['replace_interests'] : 1, // defaults to replace
			), $form_id, $_POST['yikes-mailchimp-associated-list-id'], $_POST['EMAIL'] ),
			'timeout' => 10,
			'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
		) ); */

		// subscribe the user
		/* $subscribe_response = wp_remote_request( $api_endpoint, array(
			'method' => 'PATCH',
			'body' => apply_filters( 'yikes-mailchimp-user-subscribe-api-request', json_encode( array(
				'email_address' => sanitize_email( $_POST['EMAIL'] ),
				'status'        => 'subscribed',
				'merge_fields'  => $merge_variables,
			) ), $form, $list_id, $_POST['EMAIL'] ),
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'api_key:' . $api_key ),
			),
		) );
		*/

		$member_data = array(
			'email_address' => sanitize_email( $_POST['EMAIL'] ),
			'merge_fields'  => $merge_variables,
			'interests'     => $groups,
			'status'        => 'subscribed',
		);

		$subscribe_response = yikes_get_mc_api_manager()->get_list_handler()->member_subscribe( $list_id, md5( strtolower( sanitize_email( $_POST['EMAIL'] ) ) ), $member_data );

		if ( is_wp_error( $subscribe_response ) ) {

			$error_message = $subscribe_response->get_error_message();

			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();

			$error_logging->maybe_write_to_log( $error_message, __( 'New Subscriber' , 'yikes-inc-easy-mailchimp-extender' ), 'process_form_submission.php' );

			$process_submission_response = '<p class="yikes-easy-mc-error-message">' . $error_message . '</p>';

			return;

		}

		// setup our submission response
		$form_submitted = 1;

		// Display the success message
		if ( ! empty( $form_settings['error_messages']['success'] ) ) {

			$process_submission_response = '<p class="yikes-easy-mc-success-message">' . apply_filters( 'yikes-mailchimp-success-response', stripslashes( esc_html( $form_settings['error_messages']['success'] ) ), $form_id, $merge_variables ) . '</p>';
			// echo stripslashes( esc_html( $error_messages['success'] ) );

		} else {

			$default_success_response = ( 1 === $form_settings['optin_settings']['optin'] ) ? __( 'Thank you for subscribing! Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' ) : __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' );
			$process_submission_response = '<p class="yikes-easy-mc-success-message">' . apply_filters( 'yikes-mailchimp-success-response', $default_success_response, $form_id, $merge_variables ) . '</p>';
			// echo $default_success_response;

		}

		/*
		*	yikes-mailchimp-after-submission
		*
		*	Catch the merge variables after they've been sent over to MailChimp
		*	param @merge_variables - user submitted form data
		* 	optional @form - the ID of the form to filter
		*	@since 6.0.0
		*/
		do_action( 'yikes-mailchimp-after-submission' , $merge_variables );
		do_action( 'yikes-mailchimp-after-submission-' . $form_id , $merge_variables );

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
		*	@$form_id - the form ID
		*	@$notifications - the notification array
		*	@since 6.0.0
		*/
		do_action( 'yikes-mailchimp-form-submission' , $_POST['EMAIL'] , $merge_variables , $form_id , $form_settings['notifications'] );
		do_action( 'yikes-mailchimp-form-submission-' . $form_id , $_POST['EMAIL'] , $merge_variables , $form_id , $form_settings['notifications'] );

		/*
		*	Increase the submission count for this form
		*	on a successful submission
		*	@since 6.0.0
		*/
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$submissions = $form_settings['submissions'] + 1;
		$interface->update_form_field( $form_id, 'submissions', $submissions );
}
