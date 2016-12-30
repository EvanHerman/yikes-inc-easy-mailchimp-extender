<?php
/*
*	Process Non-Ajax forms
*	@Updated for v6.0.3.5
*/

// set the global variable to 1, to trigger a successful submission
global $form_submitted, $process_submission_response;

// confirm we have a form id to work with
$form_id = ( ! empty( $_POST['yikes-mailchimp-submitted-form'] ) ) ? absint( $_POST['yikes-mailchimp-submitted-form'] ) : false;

if ( ! $form_id ) {
	return;
}

$form_interface    = yikes_easy_mailchimp_extender_get_form_interface();
$list_handler      = yikes_get_mc_api_manager()->get_list_handler();
$list_id           = sanitize_text_field( $_POST['yikes-mailchimp-associated-list-id'] );
$form_settings     = $interface->get_form( $form_id );
$replace_interests = isset( $form_settings['submission_settings']['replace_interests'] ) ? (bool) $form_settings['submission_settings']['replace_interests'] : true;
$groups            = array();

// If the user intends to replace existing interest groups, loop and set them all to false to start.
if ( $replace_interests ) {
	$interest_groupings = $list_handler->get_interest_categories( $_POST['yikes-mailchimp-associated-list-id'] );

	foreach ( $interest_groupings as $group_data ) {
		$item_ids = array_keys( $group_data['items'] );
		$keyed    = array_fill_keys( $item_ids, false );
		$groups   = array_merge( $groups, $keyed );
	}
}

// Process our form submissions (non ajax forms)
if ( ! isset( $_POST['yikes_easy_mc_new_subscriber'] ) || ! wp_verify_nonce( $_POST['yikes_easy_mc_new_subscriber'], 'yikes_easy_mc_form_submit' ) ) {
	$process_submission_response = '<p><small class="form_submission_error">' . __( "Error : Sorry, the nonce security check didn't pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt." , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';

	return;
}

// See if the Honeypot was filled, and maybe return an error.
$honey_pot_filled = ( isset( $_POST['yikes-mailchimp-honeypot'] ) && '' !== $_POST['yikes-mailchimp-honeypot'] ) ? true : false;
if ( $honey_pot_filled ) {
	$process_submission_response = '<p><small class="form_submission_error">' . __( 'Error: It looks like the honeypot was filled out and the form was not properly be submitted.' , 'yikes-inc-easy-mailchimp-extender' ) . '</small></p>';

	return;
}

// Check reCAPTCHA Response
if ( isset( $_POST['g-recaptcha-response'] ) ) {
	$url           = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'] );
	$response      = wp_remote_get( $url );
	$response_body = json_decode( $response['body'] , true );

	// if we've hit an error, lets return the error!
	if ( 1 !== $response_body['success'] ) {
		$recaptcha_error = array();

		foreach ( $response_body['error-codes'] as $error_code ) {
			if ( 'missing-input-response' === $error_code ) {
				$error_code = __( 'Please check the reCAPTCHA field.', 'yikes-inc-easy-mailchimp-extender' );
			}

			$recaptcha_error[] = $error_code;
		}

		$process_submission_response .= "<p class='yikes-easy-mc-error-message'>" . apply_filters( 'yikes-mailchimp-recaptcha-required-error', __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) . ': ' . implode( ' ' , $recaptcha_error ) ) . '</p>';

		return;
	}
}

/*
 * Confirm that all required checkbox groups were submitted.
 * No HTML5 validation, and don't want to use jQuery for non-ajax forms
*/
$missing_required_checkbox_interest_groups = array();
foreach ( $form_settings['fields'] as $merge_tag => $field_data ) {
	if ( ! isset( $field_data['group_id'] ) ) {
		continue;
	}

	// If this isn't a checkbox field, continue.
	if ( ! isset( $field_data['type'] ) || 'checkboxes' !== $field_data['type'] ) {
		continue;
	}

	// Determine if the group is required.
	if ( ! isset( $field_data['require'] ) || 1 !== $field_data['require'] ) {
		continue;
	}

	// If we've come this far, make sure we have the merge tag present.
	if ( ! isset( $_POST[ $merge_tag ] ) ) {
		$missing_required_checkbox_interest_groups[] = $merge_tag;
	}
}

if ( ! empty( $missing_required_checkbox_interest_groups ) ) {
	$process_submission_response = '<p class="yikes-easy-mc-error-message">' . apply_filters( 'yikes-mailchimp-interest-group-required-top-error', sprintf( _n( 'It looks like you forgot to fill in %s required field.', 'It looks like you forgot to fill in %s required fields.', count( $missing_required_checkbox_interest_groups ), 'yikes-inc-easy-mailchimp-extender' ), count( $missing_required_checkbox_interest_groups ) ), count( $missing_required_checkbox_interest_groups ), $form_id ) . '</p>';

	return;
}

// Empty array to build up merge variables & interest groups
$merge_variables = array();

// loop to push variables to our array
foreach ( $_POST as $merge_tag => $value ) {
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
			$sanitized = ( '' != $sanitized ) ? absint( $sanitized ): '';
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
$merge_variables['optin_time'] = current_time( 'Y-m-d H:i:s', 1 );

/**
 * Filter the merge variables.
 *
 * Filter the merge variables before they get sent over to MailChimp.
 *
 * @since 6.0.0
 *
 * @param array $merge_variables The array of merge variables.
 */
$merge_variables = apply_filters( 'yikesinc_eme_merge_vars',            $merge_variables );
$merge_variables = apply_filters( "yikesinc_eme_merge_vars_{$form_id}", $merge_variables );

/**
 * Action hooks fired before API request
 *
 * @since 6.0.5.5
 *
 * @param $merge_variables array Array of merge variable to use
 */
do_action( 'yikes-mailchimp-before-submission',            $merge_variables );
do_action( "yikes-mailchimp-before-submission-{$form_id}", $merge_variables );

// Allow users to check for submit value and pass back an error to the user.
if ( isset( $merge_variables['error'] ) ) {
	$process_submission_response = apply_filters( 'yikes-mailchimp-frontend-content' , $merge_variables['message'] );

	return;
}

$email = sanitize_email( $_POST['EMAIL'] );
$member_data = array(
	'email_address' => $email,
	'merge_fields'  => $merge_variables,
	'interests'     => $groups,
	'status'        => 'subscribed',
);

$subscribe_response = $list_handler->member_subscribe( $list_id, md5( strtolower( sanitize_email( $_POST['EMAIL'] ) ) ), $member_data );

if ( is_wp_error( $subscribe_response ) ) {
	$error_message = $subscribe_response->get_error_message();
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log( $error_message, __( 'New Subscriber', 'yikes-inc-easy-mailchimp-extender' ), 'process_form_submission.php' );
	$process_submission_response = '<p class="yikes-easy-mc-error-message">' . $error_message . '</p>';

	return;
}

// setup our submission response
$form_submitted = 1;

// Display the success message
if ( ! empty( $form_settings['error_messages']['success'] ) ) {
	$process_submission_response = '<p class="yikes-easy-mc-success-message">' . apply_filters( 'yikes-mailchimp-success-response', stripslashes( esc_html( $form_settings['error_messages']['success'] ) ), $form_id, $merge_variables ) . '</p>';
} else {
	$default_success_response    = ( 1 === $form_settings['optin_settings']['optin'] ) ? __( 'Thank you for subscribing! Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' ) : __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' );
	$process_submission_response = '<p class="yikes-easy-mc-success-message">' . apply_filters( 'yikes-mailchimp-success-response', $default_success_response, $form_id, $merge_variables ) . '</p>';
}

/**
 * After the form submission.
 *
 * Catch the merge variables after they've been sent over to MailChimp.
 *
 * @since 6.0.0
 *
 * @param array $merge_variables
 */
do_action( 'yikes-mailchimp-after-submission' ,           $merge_variables );
do_action( "yikes-mailchimp-after-submission-{$form_id}", $merge_variables );

/**
 * Form Submission.
 *
 * Do something with the email address, merge variables, form ID or notifications.
 *
 * @since 6.0.0
 *
 * @param string $email           The user's email address.
 * @param array  $merge_variables Array of data that the user submitted.
 * @param int    $form_id         The form ID.
 * @param array  $notifications   The array of notifications settings for the form.
 */
do_action( 'yikes-mailchimp-form-submission',            $email, $merge_variables, $form_id, $form_settings['notifications'] );
do_action( "yikes-mailchimp-form-submission-{$form_id}", $email, $merge_variables, $form_id, $form_settings['notifications'] );

// Increase the submission count for this form on a successful submission.
$submissions = $form_settings['submissions'] + 1;
$form_interface->update_form_field( $form_id, 'submissions', $submissions );
