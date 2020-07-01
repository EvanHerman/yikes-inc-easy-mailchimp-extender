<?php
/*
*	AJAX Form Submission Processing
*	Begin below young grasshopper
*/

// Instantiate our submission handler class
$submission_handler = new Yikes_Inc_Easy_Mailchimp_Extender_Process_Submission_Handler( $is_ajax = true );

// parse our form data
parse_str( $_POST['form_data'], $data );

// Get the form_id
$form_id   = absint( $_POST['form_id'] );

// Send an error if for some reason we can't find the $form_id
$submission_handler->handle_empty_form_id( $form_id );

// Set the form id in our class
$submission_handler->set_form_id( $form_id ); 

// Check our nonce
$submission_handler->handle_nonce( $_POST['ajax_security_nonce'], 'yikes_mc_form_submission_security_nonce' );

// Get the form data
$interface = yikes_easy_mailchimp_extender_get_form_interface();
$form_data = $interface->get_form( $form_id );

// Send an error if for some reason we can't find the form.
$submission_handler->handle_empty_form( $form_data ); 

// Set up some variables from the form data -- these are required
$list_id             = isset( $form_data['list_id'] ) ? $form_data['list_id'] : null;
$submission_settings = isset( $form_data['submission_settings'] ) ? $form_data['submission_settings'] : null;
$optin_settings      = isset( $form_data['optin_settings'] ) ? $form_data['optin_settings'] : null;
$form_fields         = isset( $form_data['fields'] ) ? $form_data['fields'] : null;

// Send an error if for some reason we can't find the required form data
$submission_handler->handle_empty_fields_generic( array( $list_id, $submission_settings, $optin_settings, $form_fields ) );

// Set the list id in our class
$submission_handler->set_list_id( $list_id );

// Check for required fields and send an error if a required field is empty
// This is a server side check for required fields because some browsers (e.g. Safari) do not recognize the `required` HTML 5 attribute
$submission_handler->check_for_required_form_fields( $data, $form_fields );
$submission_handler->check_for_required_interest_groups( $data, $form_fields );

// Set up some variables from the form data -- these are not required
$error_messages      = isset( $form_data['error_messages'] ) ? $form_data['error_messages'] : array();
$notifications       = isset( $form_data['custom_notifications'] ) ? $form_data['custom_notifications'] : array();

// Set the error messages in our class
$submission_handler->set_error_messages( $error_messages );

// Some other variables we'll need.
$page_data       = isset( $_POST['page_data'] ) ? $_POST['page_data'] : '';
$merge_variables = array();
$error           = 0;
$list_handler    = yikes_get_mc_api_manager()->get_list_handler();

// As of 6.4 we no longer pass the post object, only the ID.
// For any users relying on the $post object for their `yikes-mailchimp-redirect-url` filter we'll grab the post object here.
// Eventually we should just pass the $post_id into the filter instead of the whole object.
$page_data       = ! empty( $page_data ) ? get_post( $page_data ) : '';

// Send an error if for some reason we can't find the list_handler
$submission_handler->handle_empty_list_handler( $list_handler ); 

// Get, sanitize and lowercasify the email
$submitted_email = isset( $data['EMAIL'] ) ? $data['EMAIL'] : '';
$sanitized_email = $submission_handler->get_sanitized_email( $submitted_email ); 
$submission_handler->set_email( $sanitized_email );

// Send an error if for some reason we can't find the email
$submission_handler->handle_empty_email( $sanitized_email );

// Check for Honeypot filled
$honey_pot_filled = ( isset( $data['yikes-mailchimp-honeypot'] ) && '' !== $data['yikes-mailchimp-honeypot'] ) ? true : false;

// Send an error if honey pot is not empty
$submission_handler->handle_non_empty_honeypot( $honey_pot_filled ); 

// Check if reCAPTCHA Response was submitted with the form data, and handle it if needed
if ( isset( $data['g-recaptcha-response'] ) ) {
	$recaptcha_response = $data['g-recaptcha-response'];
	$submission_handler->handle_recaptcha( $recaptcha_response );
}

if ( isset( $data['recaptcha_three_response'] ) ) {
	$submission_handler->handle_recaptcha( $data['recaptcha_three_response'], 3 );
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
	$submission_handler->handle_merge_variables_error( $merge_variables['error'], $merge_error_message );
}

// This is the array we're going to pass through to the Mailchimp API
$member_data = array(
	'email_address' => $sanitized_email,
	'merge_fields'  => $merge_variables,
);

// Only add groups if they exist
if ( ! empty( $groups ) ) {
	$member_data['interests'] = $groups;
}

// Check if this member already exists
$member_exists            = $list_handler->get_member( $list_id, md5( $sanitized_email ), $use_transient = false );
$double_optin_resubscribe = false;

// If this member does not exist, then we need to add the status_if_new flag and set our $new_subscriber variable
// Likewise, if this member exists but their status is 'pending' it means we're dealing with a double opt-in list and they never confirmed
// Or, if this member but their status is 'unsubscribed' it means we're dealing with someone who unsubscribed and they need to re-subscribe
// Continue as if they're a new member to force another double opt-in email
if ( is_array( $member_exists ) && isset( $member_exists['status'] ) ) {
	$double_optin_resubscribe = $member_exists['status'] === 'pending' || $member_exists['status'] === 'unsubscribed';
} else {

	// If this member doesn't exist, set their timestamp signup to the current time.
	$member_data['timestamp_signup'] = current_time( 'Y-m-d H:i:s', 1 );
}

if ( is_wp_error( $member_exists ) || $double_optin_resubscribe === true ) {

	$new_subscriber = true;

	// Check the opt-in value - is it double or single?
	// Double opt-in means 'status_if_new' => 'pending'
	$double_optin = isset( $optin_settings['optin'] ) ? (int) $optin_settings['optin'] : 0;

	// If the user was unsubscribed and is re-subscribing, we set the status to 'pending', which
	// causes Mailchimp to send them a confirmation email.  This is the only way Mailchimp will
	// allow us to re-subscribe the user.
	$was_unsubscribed = is_array( $member_exists ) && isset( $member_exists['status'] ) && $member_exists['status'] === 'unsubscribed';
	$was_unsubscribed = apply_filters( 'yikes_mailchimp_single_resubscribe', $was_unsubscribed );

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

	// But first let's set our flag, and set the Mailchimp status flag
	$new_subscriber = false;
	$member_data['status'] = 'subscribed';

	// Check our update_existing_user optin setting
	$update_existing_user = ( $optin_settings['update_existing_user'] === '1' ) ? true : false;

	// If update_existing_user is false (not allowed) then simply fail and return a response message
	if ( $update_existing_user === false ) {
		$submission_handler->handle_disallowed_existing_user_update();
	}

	// If update_existing_user is true, we need to check our 'send_update_email' option
	$send_update_email = ( $optin_settings['send_update_email'] === '1' ) ? true : false;

	// If $send_update_email is true (we send the email) then we need to fire off the 'send update email' logic
	if ( $send_update_email === true ) {
		$submission_handler->handle_updating_existing_user();
	}
	
	// If $send_update_email is false (we don't send the email) then simply continue (we allow them to update their profile via the form using their email address)
}

/**
 * Filters for the subscribe body
 *
 * @since 6.3.0
 *
 * @param array  | $member_data | Array of all the variables sent to the Mailchimp API
 * @param string | $form_id		| The form ID
 */
$member_data = apply_filters( 'yikes-mailchimp-filter-subscribe-request', $member_data, $form_id );
$member_data = apply_filters( "yikes-mailchimp-filter-subscribe-request-{$form_id}", $member_data, $form_id );

// If this is the first time subscriber add the tags along with the subscribe request.
// This allows for double opt-in forms submissions to be tagged.
if ( ! empty( $form_data['tags'] ) && ! is_array( $member_exists ) ) {
	foreach( $form_data['tags'] as $tag ) {
		$member_data['tags'][] = $tag['name'];
	}
}

// Send the API request to create a new subscriber! (Or update an existing one)
$subscribe_response = $list_handler->member_subscribe( $list_id, md5( $sanitized_email ), $member_data );

// Handle the response 

// Was our submission successful or did it create an error?
if ( is_wp_error( $subscribe_response ) ) {
	$submission_handler->handle_submission_response_error( $subscribe_response, $form_fields );
} else {
	// Check if we have any tags to add.
	$tags_response = $submission_handler->maybe_add_tags( $form_data, $data );
	
	$submission_handler->handle_submission_response_success( $submission_settings, $page_data, $merge_variables, $notifications, $optin_settings, $new_subscriber );
}

// That's all folks.
// :)