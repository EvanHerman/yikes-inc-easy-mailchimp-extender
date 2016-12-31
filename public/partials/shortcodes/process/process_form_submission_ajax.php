<?php
/*
*	AJAX Form Submission Processing
*	Begin below young grasshopper
*/

// parse our form data
parse_str( $_POST['form_data'], $data );

$form_id   = absint( $_POST['form_id'] );
$interface = yikes_easy_mailchimp_extender_get_form_interface();
$form_data = $interface->get_form( $form_id );

// Send an error if for some reason we can't find the form.
if ( empty( $form_data ) ) {
	wp_send_json_error( array(
		'hide'     => '0',
		'error'    => 1,
		'response' => __( 'Error: We were unable to find the form data.', 'yikes-inc-easy-mailchimp-extender' ),
	) );
}

// Set up some variables from the form data.
$list_id             = $form_data['list_id'];
$submission_settings = $form_data['submission_settings'];
$optin_settings      = $form_data['optin_settings'];
$form_fields         = $form_data['fields'];
$error_messages      = $form_data['error_messages'];
$notifications       = isset( $form_data['custom_notifications'] ) ? $form_data['custom_notifications'] : array();

// Some other variables we'll need.
$page_data       = $_POST['page_data'];
$merge_variables = array();
$error           = 0;
$list_handler    = yikes_get_mc_api_manager()->get_list_handler();

/* Check for Honeypot filled */
$honey_pot_filled = ( isset( $data['yikes-mailchimp-honeypot'] ) && '' !== $data['yikes-mailchimp-honeypot'] );

// if it was filled out, return an error...
if ( $honey_pot_filled ) {
	wp_send_json_error( array(
		'hide'     => '0',
		'error'    => 1,
		'response' => __( 'Error: It looks like the honeypot was filled out and the form was not properly submitted.', 'yikes-inc-easy-mailchimp-extender' ),
	) );
}

// Check reCAPTCHA Response was submitted with the form data
if ( isset( $data['g-recaptcha-response'] ) ) {
	$url           = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key', '' ) . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'] );
	$response      = wp_remote_get( $url );
	$response_body = json_decode( $response['body'], true );

	// if we've hit an error, lets return the error!
	if ( 1 !== $response_body['success'] ) {
		$recaptcha_error = array();

		foreach ( $response_body['error-codes'] as $error_code ) {
			if ( 'missing-input-response' === $error_code ) {
				$error_code = __( 'Please check the reCAPTCHA field.', 'yikes-inc-easy-mailchimp-extender' );
			}

			$recaptcha_error[] = $error_code;
		}

		$process_submission_response .= "<p class='yikes-easy-mc-error-message'>" . apply_filters( 'yikes-mailchimp-recaptcha-required-error', __( 'Error', 'yikes-inc-easy-mailchimp-extender' ) . ': ' . implode( ' ', $recaptcha_error ) ) . '</p>';
	} else {
		$recaptcha_error[] = __( 'Please refresh the page and try again.', 'yikes-inc-easy-mailchimp-extender' );
	}

	$error = 1;

	wp_send_json_error( array(
		'hide'     => '0',
		'error'    => $error,
		'response' => apply_filters( 'yikes-mailchimp-recaptcha-required-error', implode( ' ', $recaptcha_error ) ),
	) );
}

$replace_interests = isset( $submission_settings['replace_interests'] ) ? (bool) $submission_settings['replace_interests'] : true;

$groups = array();

// If the user intends to replace existing interest groups, loop and set them all to false to start.
if ( $replace_interests ) {
	$interest_groupings = $list_handler->get_interest_categories( $_POST['yikes-mailchimp-associated-list-id'] );

	foreach ( $interest_groupings as $group_data ) {
		$item_ids = array_keys( $group_data['items'] );
		$keyed    = array_fill_keys( $item_ids, false );
		$groups   = array_merge( $groups, $keyed );
	}
}

// loop to push variables to our array
foreach ( $data as $merge_tag => $value ) {

	$skip_merge_tags = array(
		'yikes_easy_mc_new_subscriber' => 1,
		'_wp_http_referer'             => 1,
	);

	// Skip any merge tags that aren't in the field settings, or that should be skipped.
	if ( ! isset( $form_settings['fields'][ $merge_tag ] ) || isset( $skip_merge_tags[ $merge_tag ] ) ) {
		continue;
	}

	// Sanitize the value to start with.
	if ( is_scalar( $value ) ) {
		$sanitized = sanitize_text_field( $value );
	} else {
		$sanitized = array();
		foreach ( $value as $val ) {
			$sanitized[] = sanitize_text_field( $val );
		}
	}

	// If the field is empty, don't include it.
	if ( empty( $sanitized ) ) {
		continue;
	}

	// check if the current iteration has a 'date_format' key set (aka - date/birthday fields)
	if ( isset( $form_settings['fields'][ $merge_tag ]['date_format'] ) ) {

		// check if EU date format
		if ( 'DD/MM/YYYY' === $form_settings['fields'][ $merge_tag ]['date_format'] || 'DD/MM' === $form_settings['fields'][ $merge_tag ]['date_format'] ) {
			// convert '/' to '.' and to UNIX timestamp
			$sanitized = ( '' != $sanitized ) ? str_replace( '/', '.', $sanitized ) : '';
		} else {
			// convert to UNIX timestamp
			$sanitized = ( '' != $sanitized ) ? absint( $sanitized ) : '';
		}
	}

	// Possibly handle an interest group.
	if ( strpos( $merge_tag, 'group-' ) !== false ) {
		$tag = str_replace( 'group-', '', $merge_tag );

		if ( is_array( $sanitized ) ) {
			foreach ( $sanitized as $val ) {
				$groups[ $val ] = true;
			}

			continue;
		}

		$groups[ $sanitized ] = true;
		continue;
	}

	$merge_variables[ $merge_tag ] = $sanitized;
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
$merge_variables = apply_filters( 'yikes-mailchimp-before-submission-' . $form_id, $merge_variables );

/**
* Action hooks fired before API request
* @since 6.0.5.5
*/
do_action( 'yikes-mailchimp-before-submission', $merge_variables );
do_action( 'yikes-mailchimp-before-submission-' . $form_id, $merge_variables );

/*
*	Allow users to check for submit value
*	and pass back an error to the user
*/
if ( isset( $merge_variables['error'] ) ) {
	wp_send_json_error( array(
		'hide'     => '0',
		'error'    => $merge_variables['error'],
		'response' => $merge_variables['message'],
	) );
}

$member_data = array(
	'email_address' => sanitize_email( $data['EMAIL'] ),
	'merge_fields'  => $merge_variables,
	'interests'     => $groups,
	'status'        => 'subscribed',
);

$subscribe_response = $list_handler->member_subscribe( $list_id, md5( strtolower( sanitize_email( $data['EMAIL'] ) ) ), $member_data );

if ( is_wp_error( $subscribe_response ) ) {
	$error_data = $subscribe_response->get_error_data();
	$details    = '';
	if ( isset( $error_data['data'] ) ) {
		foreach ( $error_data['data'] as $datum ) {
			if ( ! isset( $datum['field'], $datum['message'] ) ) {
				continue;
			}

			$details .= sprintf( '<br>Error with %1$s field: <strong>%2$s</strong>', $form_fields[ $datum['field'] ]['label'], $datum['message'] );
		}
	}

	$error_message = $subscribe_response->get_error_message();
	if ( ! empty( $details ) ) {
		$error_message .= $details;
	}

	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log( $error_message, __( 'New Subscriber', 'yikes-inc-easy-mailchimp-extender' ), 'process_form_submission.php' );
	$process_submission_response = '<p class="yikes-easy-mc-error-message">' . $error_message . '</p>';

	// send the response
	wp_send_json_error( array(
		'hide'              => '0',
		'error'             => $subscribe_response->get_error_code(),
		'response'          => $error_message,
		'security_response' => 'test',
		'data'              => $subscribe_response->get_error_data(),
	) );
}

// set the global variable to 1, to trigger a successful submission
$form_submitted = 1;

/*
*	Successful form submission redirect
*/
if ( '1' === $submission_settings['redirect_on_submission'] ) {
	$redirection  = '1';
	$redirect_url = ( 'custom_url' != $submission_settings['redirect_page'] ) ? get_permalink( $submission_settings['redirect_page'] ) : $submission_settings['custom_redirect_url'];
	$redirect    = '<script type="text/javascript">setTimeout(function() { window.location="' . apply_filters( 'yikes-mailchimp-redirect-url', esc_url( $redirect_url ), $form_id, $page_data ) . '"; }, ' . apply_filters( 'yikes-mailchimp-redirect-timer', 1500 ) . ');</script>';
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
do_action( 'yikes-mailchimp-after-submission-' . $form_id, $merge_variables );

// send our notifications if setup (must go before wp_send_json())
do_action( 'yikes-mailchimp-form-submission' , sanitize_email( $data['EMAIL'] ), $merge_variables , $form_id , $notifications );
do_action( 'yikes-mailchimp-form-submission-' . $form_id, sanitize_email( $data['EMAIL'] ), $merge_variables , $form_id , $notifications );

$default_success_response = ( 1 === $optin_settings['optin'] ) ? __( 'Thank you for subscribing! Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' ) : __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' );

wp_send_json_success( array(
	'hide'        => $submission_settings['hide_form_post_signup'],
	'error'       => $error,
	'response'    => apply_filters( 'yikes-mailchimp-success-response', ( ! empty( $error_messages['success'] ) ? $error_messages['success'] : $default_success_response ), $form_id, $merge_variables ),
	'redirection' => isset( $redirection ) ? '1' : '0',
	'redirect'    => isset( $redirect ) ? $redirect : '',
) );
