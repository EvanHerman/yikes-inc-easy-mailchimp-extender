<?php
/*
*	Process Non-Ajax forms
*	Overhauled for @6.3.0
*/

// Define our globals - $form_submitted is a flag, $process_submission_response is a string with a message
global $form_submitted, $process_submission_response;

// Instantiate our submission handler class
$submission_handler = new Yikes_Inc_Easy_MailChimp_Extender_Process_Submission_Handler( $is_ajax = false );

// Capture our form data
$data = $_POST;

// Confirm we have a form id to work with
$form_id = ( isset( $data['yikes-mailchimp-submitted-form'] ) ) ? absint( $data['yikes-mailchimp-submitted-form'] ) : false;

// Set the form id in our class
$submission_handler->set_form_id( $form_id );

// For non-AJAX, we need to wrap our 'empty' calls in an if statement and if false `return;` 

// Send an error if for some reason we can't find the $form_id
if ( $submission_handler->handle_empty_form_id( $form_id ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_form_id_message, $is_success = false );
	return;
}

// Check our nonce
if ( $submission_handler->handle_nonce( $_POST['yikes_easy_mc_new_subscriber'], 'yikes_easy_mc_form_submit' ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_nonce_message, $is_success = false );
	return;
}

// Get the form data
$interface = yikes_easy_mailchimp_extender_get_form_interface();
$form_data = $interface->get_form( $form_id );

// Send an error if for some reason we can't find the form.
if ( $submission_handler->handle_empty_form( $form_data ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_form_message, $is_success = false );
	return;
}

// Set up some variables from the form data -- these are required
$list_id             = isset( $form_data['list_id'] ) ? $form_data['list_id'] : null;
$submission_settings = isset( $form_data['submission_settings'] ) ? $form_data['submission_settings'] : null;
$optin_settings      = isset( $form_data['optin_settings'] ) ? $form_data['optin_settings'] : null;
$form_fields         = isset( $form_data['fields'] ) ? $form_data['fields'] : null;

// Send an error if for some reason we can't find the required form data
if ( $submission_handler->handle_empty_fields_generic( array( $list_id, $submission_settings, $optin_settings, $form_fields ) ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_fields_generic_message, $is_success = false );
	return;
}

// Check for required fields and send an error if a required field is empty
// This is a server side check for required fields because some browsers (e.g. Safari) do not recognize the `required` HTML 5 attribute
if ( $submission_handler->check_for_required_form_fields( $data, $form_fields ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_required_field_message, $is_success = false );
	return;
}
if ( $submission_handler->check_for_required_interest_groups( $data, $form_fields ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_required_interest_group_message, $is_success = false );
	return;
}

// Set the list id in our class
$submission_handler->set_list_id( $list_id );

// Set up some variables from the form data -- these are not required
$error_messages      = isset( $form_data['error_messages'] ) ? $form_data['error_messages'] : array();
$notifications       = isset( $form_data['custom_notifications'] ) ? $form_data['custom_notifications'] : array(); // Do we need this?

// Set the error messages in our class
$submission_handler->set_error_messages( $error_messages );

// Some other variables we'll need.
$merge_variables = array();
$list_handler    = yikes_get_mc_api_manager()->get_list_handler();

// Send an error if for some reason we can't find the list_handler
if ( $submission_handler->handle_empty_list_handler( $list_handler ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_list_handler_message, $is_success = false );
	return;
}

// Get and sanitize the email
$submitted_email = isset( $data['EMAIL'] ) ? $data['EMAIL'] : '';
$sanitized_email = $submission_handler->get_sanitized_email( $submitted_email ); 
$submission_handler->set_email( $sanitized_email );

// Send an error if for some reason we can't find the email
if ( $submission_handler->handle_empty_email( $sanitized_email ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_empty_email_message, $is_success = false );
	return;
}

// Check for Honeypot filled
$honey_pot_filled = ( isset( $data['yikes-mailchimp-honeypot'] ) && '' !== $data['yikes-mailchimp-honeypot'] ) ? true : false;

// Send an error if honey pot is not empty
if ( $submission_handler->handle_non_empty_honeypot( $honey_pot_filled ) === false ) {
	$process_submission_response = $submission_handler->wrap_form_submission_response( $submission_handler->handle_non_empty_honeypot_message, $is_success = false );
	return;
}

// Check if reCAPTCHA Response was submitted with the form data, and handle it if needed
if ( isset( $data['g-recaptcha-response'] ) ) {
	$recaptcha_response = $data['g-recaptcha-response'];
	$recaptcha_handle = $submission_handler->handle_recaptcha( $recaptcha_response );
	if ( isset( $recaptcha_handle['success'] ) && $recaptcha_handle['success'] === false ) {
		$process_submission_response = $submission_handler->wrap_form_submission_response( $recaptcha_handle['message'], $is_success = false );
		return;
	}
}

// Loop through the submitted data to sanitize and format values
$merge_variables = $submission_handler->get_submitted_merge_values( $data, $form_fields );

// Submission Setting: Replace interest groups or update interest groups
$replace_interests = isset( $submission_settings['replace_interests'] ) ? (bool) $submission_settings['replace_interests'] : true;

// Get the default groups
$groups = $submission_handler->get_default_interest_groups( $replace_interests, $list_handler );

// Loop through the submitted data and update the default groups array
$groups = $submission_handler->get_submitted_interest_groups( $data, $form_fields, $groups );

/**
 * Action hooks fired before data is sent over to the API
 *
 * @since 6.0.5.5
 *
 * @param $merge_variables array Array of merge variable to use
 */
do_action( 'yikes-mailchimp-before-submission',            $merge_variables );
do_action( "yikes-mailchimp-before-submission-{$form_id}", $merge_variables );

// Allow users to check for form values (using the `yikes-mailchimp-filter-before-submission` filter hook in function `get_submitted_merge_values`) 
// and pass back an error and message to the user
// If error is set and no message, default to our class variable's default error message
if ( isset( $merge_variables['error'] ) ) {
	$merge_error_message = isset( $merge_variables['message'] ) ? $merge_variables['message'] : $submission_handler->default_error_response_message;
	$merge_vars_error_array = $submission_handler->handle_merge_variables_error( $merge_variables['error'], $merge_error_message );
	if ( $merge_vars_error_array['success'] === false ) {
		$process_submission_response = $submission_handler->wrap_form_submission_response( $merge_vars_error_array['message'], $is_success = false );
		return;
	}
}

// This is the array we're going to pass through to the MailChimp API
$member_data = array(
	'email_address' => $sanitized_email,
	'merge_fields'  => $merge_variables,
	'timestamp_opt' => current_time( 'Y-m-d H:i:s', 1 ),
);

// Only add groups if they exist
if ( ! empty( $groups ) ) {
	$member_data['interests'] = $groups;
}

// Check if this member already exists
$member_exists = $list_handler->get_member( $list_id, md5( strtolower( $sanitized_email ) ), $use_transient = false );

// If this member does not exist, then we need to add the status_if_new flag and set our $new_subscriber variable
// Likewise, if this member exists but their status is 'pending' it means we're dealing with a double opt-in list and they never confirmed
// Or, if this member but their status is 'unsubscribed' it means we're dealing with someone who unsubscribed and they need to re-subscribe
// Continue as if they're a new member to force another double opt-in email
$double_optin_resubscribe = is_array( $member_exists ) && isset( $member_exists['status'] ) && ( $member_exists['status'] === 'pending' || $member_exists['status'] === 'unsubscribed' );

if ( is_wp_error( $member_exists ) || $double_optin_resubscribe === true ) {
	$new_subscriber = true;

	// Check the opt-in value - is it double or single?
	// Double opt-in means 'status_if_new' => 'pending'
	$double_optin = isset( $optin_settings['optin'] ) ? (int) $optin_settings['optin'] : 0;

	// If the user was unsubscribed and is re-subscribing, we set the status to 'pending', which
	// causes Mailchimp to send them a confirmation email.  This is the only way Mailchimp will
	// allow us to re-subscribe the user.
	$was_unsubscribed = is_array( $member_exists ) && isset( $member_exists['status'] ) && $member_exists['status'] === 'unsubscribed';

	if ( $double_optin === 1 || $was_unsubscribed === true ) {

		// Double opt-in
		$member_data['status_if_new'] = 'pending';
		$member_data['status']        = 'pending';
	} else {

		// Single opt-in
		$member_data['status_if_new'] = 'subscribed';
		$member_data['status']        = 'subscribed';
	}

} else {

	// If this member already exists, then we need to go through our optin settings and run some more logic

	// But first let's set our flag, and set the MailChimp status flag
	$new_subscriber = false;
	$member_data['status'] = 'subscribed';

	// Check our update_existing_user optin setting
	$update_existing_user = ( $optin_settings['update_existing_user'] === '1' ) ? true : false;

	// If update_existing_user is false (not allowed) then simply fail and return a response message
	if ( $update_existing_user === false ) {
		$disallow_update_array = $submission_handler->handle_disallowed_existing_user_update();
		if ( $disallow_update_array['success'] === false ) {
			$process_submission_response = $submission_handler->wrap_form_submission_response( $disallow_update_array['message'], $is_success = false );
			return;
		}
	}

	// If update_existing_user is true, we need to check our 'send_update_email' option
	$send_update_email = ( $optin_settings['send_update_email'] === '1' ) ? true : false;

	// If $send_update_email is true (we send the email) then we need to fire off the 'send update email' logic
	if ( $send_update_email === true ) {
		$update_existing_user_array = $submission_handler->handle_updating_existing_user();
		if ( $update_existing_user_array['success'] === false ) {
			$process_submission_response = $submission_handler->wrap_form_submission_response( $update_existing_user_array['message'], $is_success = false );
			return;
		}
	}
	
	// If $send_update_email is false (we don't send the email) then simply continue (we allow them to update their profile via only an email)
}

/**
 * Filters for the subscribe body
 *
 * @since 6.3.0
 *
 * @param array  | $member_data | Array of all the variables sent to the MailChimp API
 * @param string | $form_id		| The form ID
 */
$member_data = apply_filters( 'yikes-mailchimp-filter-subscribe-request', $member_data, $form_id );
$member_data = apply_filters( "yikes-mailchimp-filter-subscribe-request-{$form_id}", $member_data, $form_id );

// Send the API request to create a new subscriber! (Or update an existing one)
$subscribe_response = $list_handler->member_subscribe( $list_id, md5( strtolower( $sanitized_email ) ), $member_data );

// Handle the response 

// Was our submission successful or did it create an error?
if ( is_wp_error( $subscribe_response ) ) {
	$success_array = $submission_handler->handle_submission_response_error( $subscribe_response, $form_fields );
} else {
	$submission_handler->handle_submission_response_success( $submission_settings, array(), $merge_variables, $notifications, $optin_settings, $new_subscriber );
}

// Handle errors in the response
if ( isset( $success_array ) && isset( $success_array['success'] ) && $success_array['success'] === false ) {
	$process_submission_response = isset( $success_array['message'] ) ? $success_array['message'] : '';
	$process_submission_response = $submission_handler->wrap_form_submission_response( $success_array['message'], $is_success = false );
	return;
}

// Set our global submission response
$form_submitted = 1;

// For non-AJAX submissions, if we have a new subscriber we need to increment our submissions count by 1
// For AJAX, this is an AJAX call that gets fired off after form submission
if ( $new_subscriber === true ) {
	$submissions = (int) $form_settings['submissions'] + 1;	
	$interface->update_form_field( $form_id, 'submissions', $submissions );
}

// End execution
return;

// That's all folks.
// :)
