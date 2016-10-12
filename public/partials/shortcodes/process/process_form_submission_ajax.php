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

if( $form_data ) {
	// List ID
	$list_id = $form_data['list_id'];

	// decode our submission settings
	$submission_settings = $form_data['submission_settings'];

	// decode our optin settings
	$optin_settings = $form_data['optin_settings'];

	// decode our fields
	$form_fields = $form_data['fields'];

	/*	Decode our error messages
	*	Workaround for international characters (cyrillic etc)
	* 	See: https://wordpress.org/support/topic/custom-messages-do-not-support-cyrillic-characters?replies=11#post-7629620
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
$honey_pot_filled = ( isset( $data['yikes-mailchimp-honeypot'] ) && $data['yikes-mailchimp-honeypot'] != '' ) ? true : false;
// if it was filled out, return an error...
if( $honey_pot_filled ) {
	wp_send_json_error( array(
		'hide' => '0',
		'error' => 1,
		'response' => __( "Error: It looks like the honeypot was filled out and the form was not properly be submitted." , 'yikes-inc-easy-mailchimp-extender' )
	) );
	return;
}

// Check reCAPTCHA Response was submitted with the form data
if( isset( $data['g-recaptcha-response'] ) ) {
	$url = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $data['g-recaptcha-response'] . '&remoteip=' . $_SERVER["REMOTE_ADDR"] );
	$response = wp_remote_get( $url );
	$response_body = json_decode( $response['body'] , true );
	// if we've hit an error, lets return the error!
	if( $response_body['success'] != 1 ) {
		$error_messages = array(); // empty array to store error messages
		if( isset( $response_body['error-codes'] ) ) {
			foreach( $response_body['error-codes'] as $error_code ) {
				if( $error_code == 'missing-input-response' ) {
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
			'error' => $error ,
			'response' => apply_filters( 'yikes-mailchimp-recaptcha-required-error', implode( ' ', $error_messages ) ),
		) );
		exit();
	}
}

// loop to push variables to our array
foreach ( $data as $merge_tag => $value ) {
	if( $merge_tag != 'yikes_easy_mc_new_subscriber' && $merge_tag != '_wp_http_referer' ) {
		// check if the current iteration has a 'date_format' key set
		// (aka - date/birthday fields)
		if( isset( $form_fields[$merge_tag]['date_format'] ) ) {
			// check if EU date format
			if( $form_fields[$merge_tag]['date_format'] == 'DD/MM/YYYY' ) {
				// convert '/' to '.' and to UNIX timestamp
				$value = date( 'Y-m-d', strtotime( str_replace( '/', '.', $value ) ) );
			} else {
				// convert to UNIX timestamp
				$value = date( 'Y-m-d', strtotime( $value ) );
			}
		}
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
$api_key = yikes_get_mc_api_key();
$dash_position = strpos( $api_key, '-' );

// setup the end point
if( $dash_position !== false ) {
	$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/subscribe.json';
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
if( isset( $merge_variables['error'] ) ) {
	// send our error response back
	wp_send_json_error( array( 'hide' => '0', 'error' => $merge_variables['error']  , 'response' => $merge_variables['message']  ) );
	return;
}

/**
 * Setup whether or not we should update the user, or display the error with email generation
 * @since 6.1
 */
if ( isset( $optin_settings['update_existing_user'] ) && 1 === absint( $optin_settings['update_existing_user'] ) ) {
	// Should we send the update email
	if ( isset( $optin_settings['send_update_email'] ) && 1 === absint( $optin_settings['send_update_email'] ) ) {
		$update_existing_user = 0;
	} else {
		$update_existing_user = 1;
	}
} else {
	$update_existing_user = 0;
}

// submit the request & data, using the form settings
	// subscribe the user
	$subscribe_response = wp_remote_post( $api_endpoint, array(
		'body' => apply_filters( 'yikes-mailchimp-user-subscribe-api-request', array(
			'apikey' => $api_key,
			'id' => $list_id,
			'email' => array( 'email' => sanitize_email( $data['EMAIL'] ) ),
			'merge_vars' => $merge_variables,
			'double_optin' => $optin_settings['optin'],
			'update_existing' => $update_existing_user, // Decide if we should update the user or not
			'send_welcome' => $optin_settings['send_welcome_email'],
			'replace_interests' => ( isset( $submission_settings['replace_interests'] ) ) ? $submission_settings['replace_interests'] : 1, // defaults to replace
		), $form, $list_id, $data['EMAIL'] ),
		'timeout' => 10,
		'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
	) );

	$subscribe_response = json_decode( wp_remote_retrieve_body( $subscribe_response ), true );

	if( isset( $subscribe_response['error'] ) ) {

		if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
			require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->yikes_easy_mailchimp_write_to_error_log( $subscribe_response['error'], __( "Subscribe New User" , 'yikes-inc-easy-mailchimp-extender' ), "process_form_submission_ajax.php" );
		}

		$update_account_details_link = '';
		$error = 1;
		switch( $subscribe_response['code'] ) {
			// user already subscribed
			case '214':
				$custom_already_subscribed_text = apply_filters( 'yikes-easy-mailchimp-update-existing-subscriber-text', sprintf( __( ' To update your MailChimp profile, please %s.', 'yikes-inc-easy-mailchimp-extender' ), '<a class="send-update-email" data-list-id="' . $list_id . '" data-user-email="' . sanitize_email( $data['EMAIL'] ) . '" href="#">' . __( 'click to send yourself an update link', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' ), $form, '<a class="send-update-email" data-list-id="' . $_POST['yikes-mailchimp-associated-list-id'] . '" data-user-email="' . sanitize_email( $data['EMAIL'] ) . '" href="#">' . __( 'click to send yourself an update link', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );
				$update_account_details_link = ( 1 === absint( $optin_settings['update_existing_user'] ) && 1 === absint( $optin_settings['send_update_email'] ) ) ? $custom_already_subscribed_text : false;
				if( ! empty( $error_messages['already-subscribed'] ) ) {
					$error_response = apply_filters( 'yikes-easy-mailchimp-user-already-subscribed-text', $error_messages['already-subscribed'] , $form, $data['EMAIL'] ) . ' ' . $update_account_details_link;
				} else {
					$error_response = $subscribe_response['error'] . ' ' . $update_account_details_link;
				}
				break;
			// missing a required field
			case '250':
					// get all merge variables in array, loop and str_replace error code with field name
					$api_key = yikes_get_mc_api_key();
					$dash_position = strpos( $api_key, '-' );
					if( $dash_position !== false ) {
						$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/merge-vars.json';
					}
					$merge_variables = wp_remote_post( $api_endpoint, array(
						'body' => array(
							'apikey' => $api_key,
							'id' => array( $list_id ) ,
						),
						'timeout' => 10,
						'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true ),
					) );
					$merge_variables = json_decode( wp_remote_retrieve_body( $merge_variables ), true );
					if( is_wp_error( $merge_variables ) || isset( $merge_variables['error'] ) ) {
						if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
							require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
							$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
							$error_logging->yikes_easy_mailchimp_write_to_error_log( $merge_variables['error'], __( "Get Merge Variables" , 'yikes-inc-easy-mailchimp-extender' ), "process_form_submission_ajax.php" );
						}
					}
					// re-store our data
					$merge_variables = $merge_variables['data'][0]['merge_vars'];
					$merge_variable_name_array = array();
					foreach( $merge_variables as $merge_var ) {
						$merge_variables_name_array[$merge_var['tag']] = $merge_var['name'];
					}
					$error_message = $subscribe_response['error'];
					// replace tag with name in the error message.
					foreach( $merge_variables_name_array as $tag => $name ) {
						$error_message = str_replace( $tag, $name, $error_message );
					}
					$error_response = $error_message;
				break;
			// test@email.com is not allowed
			case '-99':
				// generic error
				$error_response = str_replace( ' and cannot be imported', '', str_replace( 'List_RoleEmailMember:', '', $subscribe_response['error'] ) );
				break;
			// invalid email (or no email at all)
				case '-100':
					$error_response = ( ! empty( $error_messages['invalid-email'] ) ) ? $error_messages['invalid-email'] : __( 'Please provide a valid email address.', 'yikes-inc-easy-mailchimp-extender' );
					break;
			default:
				$error_response = ( ! empty( $error_messages['general-error'] ) ) ? $error_messages['general-error'] : $subscribe_response['error'];
				break;
		}
		// send the response
		wp_send_json_error( array(
			'hide' => '0',
			'error' => $error,
			'response' => $error_response,
			'security_response' => $update_account_details_link
		) );
		return;
	}

	// set the global variable to 1, to trigger a successful submission
	$form_submitted = 1;
	/*
	*	Successful form submission redirect
	*/
	if( $submission_settings['redirect_on_submission'] == '1' ) {
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

	$default_success_response = ( 1 === $optin_settings['optin'] ) ? __( "Thank you for subscribing! Check your email for the confirmation message." , 'yikes-inc-easy-mailchimp-extender' ) : __( "Thank you for subscribing!" , 'yikes-inc-easy-mailchimp-extender' );

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
