<?php
/*
*	AJAX Form Submission Processing
*	Begin below young grasshopper
*/

// parse our form data
parse_str( $_POST['form_data'], $data );

// store the form ID to use in our hooks and filters
$form = $_POST['form_id'];

$interface = yikes_easy_mailchimp_extender_get_form_interface();
$form_data = $interface->get_form( $form );

if ( $form_data ) {

	// List ID
	$list_id = $form_data['list_id'];

	// decode our submission settings
	$submission_settings = $form_data['submission_settings'];

	// decode our optin settings
	$optin_settings = $form_data['optin_settings'];

	// decode our fields
	$form_fields = $form_data['fields'];

	/**
	 * Decode our error messages
	 * Workaround for international characters (cyrillic etc)
	 * See: https://wordpress.org/support/topic/custom-messages-do-not-support-cyrillic-characters?replies=11#post-7629620
	 */
	$error_messages = $form_data['error_messages'];

	/** Submit Process **/
	$notifications = isset( $form_data['custom_notifications'] ) ? $form_data['custom_notifications'] : array();

	/* Page Data */
	$page_data = $_POST['page_data'];

}

// Empty array to build up merge variables
$merge_variables = array();

// set variable
$error = 0;

/* Check for Honeypot filled */
$honey_pot_filled = ( isset( $data['yikes-mailchimp-honeypot'] ) && '' !== $data['yikes-mailchimp-honeypot'] );

// if it was filled out, return an error...
if ( $honey_pot_filled ) {

	wp_send_json_error( array(
		'hide' => '0',
		'error' => 1,
		'response' => __( 'Error: It looks like the honeypot was filled out and the form was not properly submitted.' , 'yikes-inc-easy-mailchimp-extender' ),
	) );

	return;

}

// Check reCAPTCHA Response was submitted with the form data
if ( isset( $data['g-recaptcha-response'] ) ) {

	$url           = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $data['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'] );
	$response      = wp_remote_get( $url );
	$response_body = json_decode( $response['body'] , true );

	// if we've hit an error, lets return the error!
	if ( 1 !== $response_body['success'] ) {

		$error_messages = array(); // empty array to store error messages

		if ( isset( $response_body['error-codes'] ) ) {

			foreach ( $response_body['error-codes'] as $error_code ) {

				if ( 'missing-input-response' === $error_code ) {

					$error_code = __( 'Please check the reCAPTCHA field.', 'yikes-inc-easy-mailchimp-extender' );

				}

				$error_messages[] = __( 'Error', 'yikes-inc-easy-mailchimp-extender' ) . ': ' . $error_code;

			}
		} else {

			$error_messages[] = __( 'Please refresh the page and try again.', 'yikes-inc-easy-mailchimp-extender' );

		}

		$error = 1;

		wp_send_json_error( array(
			'hide' => '0',
			'error' => $error,
			'response' => apply_filters( 'yikes-mailchimp-recaptcha-required-error', implode( ' ', $error_messages ) ),
		) );

		exit;

	}
}

$replace_interests = isset( $submission_settings['replace_interests'] ) ? (bool) $submission_settings['replace_interests'] : true;

$groups = array();

// If the user intends to replace existing interst groups, loop and set them all to false to start.
if ( $replace_interests ) {

	$list_handler = yikes_get_mc_api_manager()->get_list_handler();

	$interest_groupings = $list_handler->get_interest_categories( $list_id );

	foreach ( $interest_groupings as $group_id => $group_data ) {

		foreach ( $group_data['items'] as $item_id => $item_data ) {

			$groups[ $item_id ] = false;

		}
	}
}

// loop to push variables to our array
foreach ( $data as $merge_tag => $value ) {

	if ( 'yikes_easy_mc_new_subscriber' !== $merge_tag && '_wp_http_referer' !== $merge_tag ) {

		// check if the current iteration has a 'date_format' key set
		// (aka - date/birthday fields)
		if ( isset( $form_fields[ $merge_tag ]['date_format'] ) ) {

			// check if EU date format
			if ( 'DD/MM/YYYY' === $form_fields[ $merge_tag ]['date_format'] || 'DD/MM' === $form_fields[ $merge_tag ]['date_format'] ) {

				// convert '/' to '.' and to UNIX timestamp
				$value = ( '' != $value ) ? str_replace( '/', '.', $value ) : '';

			} else {

				// convert to UNIX timestamp
				$value = ( '' != $value ) ? $value : '';
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
$merge_variables['timestamp_opt'] = current_time( 'Y-m-d H:i:s', 1 );

/*
*	yikes-mailchimp-before-submission
*
*	Catch the merge variables before they get sent over to MailChimp
*	param @merge_variables - user submitted form data
*	optional @form - the ID of the form to filter
*	@since 6.0.0
*/
$merge_variables = apply_filters( 'yikes-mailchimp-before-submission', $merge_variables );
$merge_variables = apply_filters( 'yikes-mailchimp-before-submission-' . $form, $merge_variables );

/**
* Action hooks fired before API request
* @since 6.0.5.5
*/
do_action( 'yikes-mailchimp-before-submission', $merge_variables );
do_action( 'yikes-mailchimp-before-submission-' . $form, $merge_variables );

/*
*	Allow users to check for submit value
*	and pass back an error to the user
*/
if ( isset( $merge_variables['error'] ) ) {

	// send our error response back
	wp_send_json_error(
		array(
			'hide'     => '0',
			'error'    => $merge_variables['error'],
			'response' => $merge_variables['message'],
		)
	);

	return;

}

$member_data = array(
	'email_address' => sanitize_email( $data['EMAIL'] ),
	'merge_fields'  => $merge_variables,
	'interests'     => $groups,
	'status'        => 'subscribed',
);

$subscribe_response = yikes_get_mc_api_manager()->get_list_handler()->member_subscribe( $list_id, md5( strtolower( sanitize_email( $data['EMAIL'] ) ) ), $member_data );

if ( is_wp_error( $subscribe_response ) ) {

	$error_message = $subscribe_response->get_error_message();

	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();

	$error_logging->maybe_write_to_log( $error_message, __( 'New Subscriber' , 'yikes-inc-easy-mailchimp-extender' ), 'process_form_submission.php' );

	$process_submission_response = '<p class="yikes-easy-mc-error-message">' . $error_message . '</p>';

	// send the response
	wp_send_json_error( array(
		'hide' => '0',
		'error' => $subscribe_response->get_error_code(),
		'response' => $error_message,
		'security_response' => 'test',
	) );

	return;

}

// set the global variable to 1, to trigger a successful submission
$form_submitted = 1;

/*
*	Successful form submission redirect
*/
if ( '1' === $submission_settings['redirect_on_submission'] ) {

	$redirection = '1';

	$redirect_url = ( 'custom_url' != $submission_settings['redirect_page'] ) ? get_permalink( $submission_settings['redirect_page'] ) : $submission_settings['custom_redirect_url'];

	$redirect = '<script type="text/javascript">setTimeout(function() { window.location="' . apply_filters( 'yikes-mailchimp-redirect-url', esc_url( $redirect_url ), $form, $page_data ) . '"; }, ' . apply_filters( 'yikes-mailchimp-redirect-timer', 1500 ) . ');</script>';

}

/*
*	yikes-mailchimp-after-submission
*
*	Catch the merge variables after they've been sent over to MailChimp
*	param @merge_variables - user submitted form data
* 	optional @form - the ID of the form to filter
*	@since 6.0.0
*/
do_action( 'yikes-mailchimp-after-submission', $merge_variables );
do_action( 'yikes-mailchimp-after-submission-' . $form, $merge_variables );

// send our notifications if setup (must go before wp_send_json())
do_action( 'yikes-mailchimp-form-submission' , sanitize_email( $data['EMAIL'] ), $merge_variables , $form , $notifications );
do_action( 'yikes-mailchimp-form-submission-' . $form, sanitize_email( $data['EMAIL'] ), $merge_variables , $form , $notifications );

$default_success_response = ( 1 === $optin_settings['optin'] ) ? __( 'Thank you for subscribing! Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' ) : __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' );

wp_send_json_success(
	array(
		'hide' => $submission_settings['hide_form_post_signup'],
		'error' => $error,
		'response' => apply_filters( 'yikes-mailchimp-success-response', ( ! empty( $error_messages['success'] ) ? $error_messages['success'] : $default_success_response ), $form, $merge_variables ),
		'redirection' => isset( $redirection ) ? '1' : '0',
		'redirect' => isset( $redirect ) ? $redirect : '',
	)
);

// end successful submission
