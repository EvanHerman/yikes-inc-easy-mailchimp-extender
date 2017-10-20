<?php

class Yikes_Inc_Easy_MailChimp_Extender_Process_Submission_Handler {

	/**
	* A flag signifying whether we're dealing with an AJAX submission or standard form submission
	* 
	* @since 6.3.0
	* @access protected
	* @var bool | $is_ajax
	*/
	protected $is_ajax;

	/**** Hardcoded Internal Variables ****/

	/**
	* An array of form fields we don't process
	* 
	* @since 6.3.0
	* @access protected
	* @var array | $skipped_form_fields
	*/
	protected $skipped_form_fields;

	/**
	* The default time in milliseconds to wait before redirecting a user
	* 
	* @since 6.3.0
	* @access protected
	* @var int | $default_redirect_time_ms
	*/
	protected $default_redirect_time_ms;

	/**** Form Variables ****/

	/**
	* The ID of the corresponding YIKES MailChimp form
	* 
	* @since 6.3.0
	* @access public
	* @var int | $form_id
	*/
	public $form_id;

	/**
	* The ID of the corresponding MailChimp list
	* 
	* @since 6.3.0
	* @access public
	* @var int | $list_id
	*/
	public $list_id;

	/**
	* The submitted email
	* 
	* @since 6.3.0
	* @access public
	* @var string | $email
	*/
	public $email;

	/**
	* The array of error messages defined by the user and attached to this form
	* 
	* @since 6.3.0
	* @access public
	* @var array | $error_messages
	*/
	public $error_messages;

	/**** Default Error Messages ****/

	/**
	* The error message for no form ID
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_form_id_message
	*/
	public $handle_empty_form_id_message;

	/**
	* The error message for no form found
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_form_message
	*/
	public $handle_empty_form_message;

	/**
	* The error message for missing form fields
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_fields_generic_message
	*/
	public $handle_empty_fields_generic_message;

	/**
	* The error message for missing $list_handler class
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_list_handler_message
	*/
	public $handle_empty_list_handler_message;

	/**
	* The error message for no email
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_email_message
	*/
	public $handle_empty_email_message;

	/**
	* The error message for a filled in honeypot
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_non_empty_honeypot_message
	*/
	public $handle_non_empty_honeypot_message;

	/**
	* The error message for existing users trying to update when it's disallowed
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_disallowed_existing_user_update_message
	*/
	public $handle_disallowed_existing_user_update_message;

	/**
	* The first half of the error message for updating an existing user when it's done via a profile link
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_updating_existing_user_message
	*/
	public $handle_updating_existing_user_message;

	/**
	* The second half of the error message (the link) for updating an existing user when it's done via a profile link
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_updating_existing_user_link_message
	*/
	public $handle_updating_existing_user_link_message;

	/**
	* A default, generic error message
	* 
	* @since 6.3.0
	* @access public
	* @var string | $default_error_response_message
	*/
	public $default_error_response_message;

	/**
	* The error message for not filling out a required form field
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_required_field_message
	*/
	public $handle_empty_required_field_message;

	/**
	* The error message for not filling out a required interest group
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_empty_required_interest_group_message
	*/
	public $handle_empty_required_interest_group_message;

	/**
	* The error message for nonce failures
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_nonce_message
	*/
	public $handle_nonce_message;

	/**
	* The error message for a recaptcha that is not checked/filled out
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_nonce_message
	*/
	public $handle_non_filled_recaptcha_message_message;

	/**
	* The error message for recaptcha errors that we're not sure of
	* 
	* @since 6.3.0
	* @access public
	* @var string | $handle_nonce_message
	*/
	public $generic_recaptcha_error_message;

	/**** Default Success Messages ****/

	/**
	* The success message for single-optin forms
	* 
	* @since 6.3.0
	* @access public
	* @var string | $default_response_single_optin_success_message
	*/
	public $default_response_single_optin_success_message;

	/**
	* The default success message for double-optin forms
	* 
	* @since 6.3.0
	* @access public
	* @var string | $default_response_double_optin_success_message
	*/
	public $default_response_double_optin_success_message;

	/**
	* The default success message for already subscribed users re-subscribing
	* 
	* @since 6.3.0
	* @access public
	* @var string | $existing_subscriber_profile_update_message
	*/
	public $existing_subscriber_profile_update_message;

	/**
	* The construct function - sets all of our hardcoded variables
	*
	* @param bool | $is_ajax | Flag signifying whether this submission request is coming from an AJAX response or basic form submission
	*/
	public function __construct( $is_ajax ) {

		// Set up our variables
		$this->is_ajax = $is_ajax;

		// Define our hardcoded fields
		$this->skipped_form_fields = array(
			'yikes_easy_mc_new_subscriber' => 1,
			'_wp_http_referer'             => 1,
		);
		$this->default_redirect_time_ms = 1500;

		// Define our error messages
		$this->handle_empty_form_id_message = __( 'Error: We were unable to find the form ID.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_form_message = __( 'Error: We were unable to find the form data.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_fields_generic_message = __( 'Error: We were unable to find the form fields.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_list_handler_message = __( 'Error: We were unable to find the list handler.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_email_message = __( 'Error: The email is invalid.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_non_empty_honeypot_message = __( 'Error: It looks like the honeypot was filled out and the form was not properly submitted.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_disallowed_existing_user_update_message = __( 'The email you entered is already a subscriber to this list.', 'yikes-inc-easy-mailchimp-extender' );
		$this->default_error_response_message =  __( 'Whoops! It looks like something went wrong. Please try again.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_updating_existing_user_message = __( 'You\'re already subscribed. ', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_updating_existing_user_link_message = __( 'To update your MailChimp profile, please click to send yourself an update link', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_required_field_message = __( 'A required field is missing.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_empty_required_interest_group_message = __( 'A required interest group is missing.', 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_nonce_message = __( 'Error: Sorry, the nonce security check didn\'t pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt.' , 'yikes-inc-easy-mailchimp-extender' );
		$this->handle_non_filled_recaptcha_message_message = __( 'Please check the reCAPTCHA field.', 'yikes-inc-easy-mailchimp-extender' );
		$this->generic_recaptcha_error_message =  __( 'Please refresh the page and try again.', 'yikes-inc-easy-mailchimp-extender' );

		// Define our success messages
		$this->default_response_single_optin_success_message = __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' );
		$this->default_response_double_optin_success_message = __( 'Thank you for subscribing. Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' );
		$this->existing_subscriber_profile_update_message = __( 'Thank you for already being a subscriber! Your profile info has been updated.', 'yikes-inc-easy-mailchimp-extender' );
	}

	/**** Setters for our Form Variables ****/

	/**
	* Set the form ID class property
	*
	* @since 6.3.0
	*
	* @param int | $form_id | ID of the corresponding YIKES MailChimp form
	*/
	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	* Set the list ID class property
	*
	* @since 6.3.0
	*
	* @param int | $list_id | ID of the corresponding MailChimp list
	*/
	public function set_list_id( $list_id ) {
		$this->list_id = $list_id;
	}

	/**
	* Set the email class property
	*
	* @since 6.3.0
	*
	* @param string | $sanitized_email | The sanitized email
	*/
	public function set_email( $sanitized_email ) {
		$this->email = $sanitized_email;
	}

	/**
	* Set the $error_messages class property
	*
	* @since 6.3.0
	*
	* @param array | $error_messages | The array of user-defined error messages for this form
	*/
	public function set_error_messages( $error_messages ) {
		$this->error_messages = $error_messages;
	}


	/**** Simple Return Functions ****/

	/**
	* Return a sanitized email
	*
	* @since 6.3.0
	*
	* @param string | $email | The user's email
	* @return string| $email | The user's email, sanitized
	*/
	public function get_sanitized_email( $email ) {
		return sanitize_email( $email );
	}

	/**
	* Return the class property $skipped_form_fields
	*
	* @since 6.3.0
	*
	* @return array | $skipped_form_fields
	*/
	protected function get_skipped_merge_tags() {
		return $this->skipped_form_fields;
	}


	/**** Collection of functions handling the incoming form and interest group data ****/

	/**
	* Loop through form data to sanitize, format, filter, and return.
	*
	* @since 6.3.0
	*
	* @param array | $data 			 | The array of user-submitted form values
	* @param array | $form_fields	 | The array of form field definitions for this YIKES MailChimp form
	* @return array| $merge_variables| The array of sanitized and formatted form values
	*/
	public function get_submitted_merge_values( $data, $form_fields ) {

		// Array to return
		$merge_variables = array();

		// loop to push variables to our array
		foreach ( $data as $merge_tag => $value ) {

			$skip_merge_tags = $this->get_skipped_merge_tags();

			// Skip any merge tags that aren't in the field settings, or that should be skipped.
			if ( ! isset( $form_fields[ $merge_tag ] ) || isset( $skip_merge_tags[ $merge_tag ] ) ) {
				continue;
			}

			// Sanitize the value to start with.
			$sanitized = $this->sanitize_form_values( $merge_tag, $value );

			// If the field is empty, don't include it.
			if ( empty( $sanitized ) ) {
				continue;
			}

			// Check if the current iteration has a 'date_format' key set (i.e. date/birthday fields)
			if ( isset( $form_fields[ $merge_tag ]['date_format'] ) ) {
				$sanitized = $this->handle_date_format_merge_values( $sanitized, $form_fields[ $merge_tag ]['date_format'] );
			}

			$merge_variables[ $merge_tag ] = $sanitized;
		}

		/**
		*	yikes-mailchimp-filter-before-submission || yikes-mailchimp-filter-before-submission-{$form_id}
		*
		*	Catch the merge variables before they get sent over to MailChimp
		*	@since 6.0.0
		*
		*	@param array | $merge_variables | The user submitted form data
		*/
		$merge_variables = apply_filters( 'yikes-mailchimp-filter-before-submission', $merge_variables );
		$merge_variables = apply_filters( 'yikes-mailchimp-filter-before-submission-{$this->form_id}', $merge_variables );

		return $merge_variables;
	}

	/**
	* Sanitize form values and return them
	*
	* @since 6.3.0
	* 
	* @param string | $key		 | The MERGE/Field-name for this value
	* @param mixed  | $value	 | The form value - this could be an array or a string
	* @return mixed | $sanitized | The $value sanitized
	*/ 
	protected function sanitize_form_values( $key, $value ) {
		if ( is_scalar( $value ) ) {
			$sanitized = sanitize_text_field( $value );
		} else {
			$sanitized = array();
			foreach ( $value as $key => $val ) {
				$sanitized[ sanitize_text_field( $key ) ] = sanitize_text_field( $val );
			}
		}
		return $sanitized;
	}

	/**
	* Check a date field's date format and pass it along to the appropriate function
	*
	* @since 6.3.0
	* 
	* @param string | $date			| The unformatted date value
	* @param string | $date_format	| The date format
	* @return string| $date			| The date formatted according to the $date_format
	*/ 
	protected function handle_date_format_merge_values( $date, $date_format ) {
		// Check if EU date format (for dates: 'DD/MM/YYYY', for birthdays: 'DD/MM')
		if ( 'DD/MM/YYYY' === $date_format ) {
			$date = $this->handle_dd_mm_yyyy_date( $date );
		} else if ( 'DD/MM' === $date_format ) {
			$date = $this->handle_dd_mm_birthday( $date );
		}

		return $date;
	}

	/**
	* Format a date field whose date format is dd/mm/yyyy
	*
	* @since 6.3.0
	*
	* @param string | $date | A date in the format dd/mm/yyyy
	* @return string| $date | A date in the format mm/dd/yyyy
	*/
	protected function handle_dd_mm_yyyy_date( $date ) {

		// MailChimp wants the dates as 'MM/DD/YYYY' regardless of user-specified format, so convert

		// Explode the date and then swap the pieces
		$pieces = explode( '/', $date );

		// Verify we have exactly three pieces
		if ( count( $pieces ) === 3 ) {

			// $pieces[1] = MM. $pieces[0] = DD. $date = MM/DD/YYYY
			$date = $pieces[1] . '/' . $pieces[0] . '/' . $pieces[2];
		}

		return $date;
	}

	/**
	* Format a birthday field whose date format is dd/mm
	*
	* @since 6.3.0
	*
	* @param string | $birthday | A date in the format dd/mm
	* @return string| $birthday | A date in the format mm/dd
	*/
	protected function handle_dd_mm_birthday( $birthday ) {

		// MailChimp wants the birthdays as 'MM/DD' regardless of user-specified format, so convert

		// Explode the date and then swap the pieces
		$pieces = explode( '/', $birthday );

		// Verify we have exactly two pieces
		if ( count( $pieces ) === 2 ) {

			// $pieces[1] = MM. $pieces[0] = DD. $birthday = MM/DD
			$birthday = $pieces[1] . '/' . $pieces[0];
		}

		return $birthday;
	}


	/**
	* Create an array of available interest groups based on the $replace_interests flag
	*
	* @since 6.3.0
	*
	* @param bool	| $replace_interests| True if we're replacing interest groups, false if updating interest groups
	* @param class 	| $list_class		| Class for interacting with the current list
	* @return array | $groups			| Array of interest groups 
	*/
	public function get_default_interest_groups( $replace_interests, $list_class ) {

		// If $replace_interests flag is true then loop through interest groups and set them all to false to start.
		// If $replace_interests flag is false, return an empty array

		// Set up our return array 
		$groups = array();

		// Check our replace interests setting
		if ( $replace_interests === true ) {

			// Get all the interest groups for this list
			$interest_groupings = $list_class->get_interest_categories( $this->list_id );

			// Loop through the interest groups and create a single array like {group_id} => false
			foreach ( $interest_groupings as $group_data ) {
				foreach ( $group_data['items'] as $item ) {
					$groups[$item['id']] = false;
				}
			}
			return $groups;
		} else {

			// If we're not replacing interest groups, simply return an array
			return $groups;
		}
	}


	/**
	* Loop through the interest group form data to sanitize, format, filter, and return.
	*
	* @since 6.3.0
	*
	* @param array | $data			| The array of user-submitted form values
	* @param array | $form_fields	| The array of form field definitions for this YIKES MailChimp form
	* @param array | $groups		| The array of interest groups created by `get_default_interest_groups()`
	* @return array| $groups		| The array of sanitized and formatted form values
	*/
	public function get_submitted_interest_groups( $data, $form_fields, $groups ) {

		// loop to push variables to our array
		foreach ( $data as $merge_tag => $value ) {

			// Only look for interest groups (data comes in as group-{$group_id})
			if ( strpos( $merge_tag, 'group-' ) !== false ) {

				// Sanitize form values
				$sanitized = $this->sanitize_form_values( $merge_tag, $value );

				if ( is_array( $sanitized ) ) {
					foreach ( $sanitized as $val ) {
						$groups[ $val ] = true;
					}
					continue;
				}

				$groups[ $sanitized ] = true;
				continue;
			}
		}

		/**
		*	yikes-mailchimp-filter-groups-before-submission
		*
		*	Catch the interest groups before they get sent over to MailChimp
		*	@param array | $groups | User submitted interest group data
		*	@optional int| $form_id| the ID of the form to filter
		*	@since 6.3.0
		*/
		$groups = apply_filters( 'yikes-mailchimp-filter-groups-before-submission', $groups, $this->form_id );
		$groups = apply_filters( 'yikes-mailchimp-filter-groups-before-submission-{$this->form_id}', $groups, $this->form_id );

		return $groups;
	}


	/**** Functions to Handle Subscribe API Response ****/

	/**
	* Handle the response to a successful subscribe request
	*
	* @since 6.3.0
	*
	* @param array | $submission_settings	| Array of the form's submission settings
	* @param array | $page_data				| Array of the page data
	* @param array | $merge_variables		| Array of the submitted form variables
	* @param array | $notifications			| Literally don't know what this is yet.
	* @param array | $optin_settings 		| Array of the form's optin settings
	* @param bool  | $new_subscriber 		| True if a new subscriber, false if an existing one
	*/
	public function handle_submission_response_success( $submission_settings, $page_data, $merge_variables, $notifications, $optin_settings, $new_subscriber ) {

		// Check if we should redirect, and collect the redirect info in an array
		$redirect_array = $this->handle_submission_response_success_redirect( $submission_settings, $page_data );

		// Fire off our actions

		/**
		*	yikes-mailchimp-after-submission || yikes-mailchimp-after-submission-{$form_id}
		*
		*	Catch the merge variables after they've been sent over to MailChimp
		*	@since 6.0.0
		*
		*	@param array | $merge_variables | The array of user submitted form data
		*/
		do_action( 'yikes-mailchimp-after-submission', $merge_variables );
		do_action( "yikes-mailchimp-after-submission-{$this->form_id}", $merge_variables );

		/**
		*	yikes-mailchimp-form-submission || yikes-mailchimp-form-submission-{$form_id}
		*
		*	Catch our notifications and other form data
		*
		*	@param string | $email			 | The user's email
		*	@param array  | $merge_variables | The array of user submitted form data
		*	@param string | $form_id		 | The form ID
		* 	@param array  | $notifications	 | Array of notification messages
		*
		*/
		do_action( 'yikes-mailchimp-form-submission', $this->email, $merge_variables, $this->form_id, $notifications );
		do_action( "yikes-mailchimp-form-submission-{$this->form_id}", $this->email, $merge_variables, $this->form_id, $notifications );

		// Get the optin value
		$optin = isset( $optin_settings['optin'] ) ? (int) $optin_settings['optin'] : 0;

		if ( 1 === $optin ) {

			// Allow the user-defined 'success' message to overwrite the default double opt-in response
			$default_response = $this->check_for_user_defined_response_message( 'success', $this->default_response_double_optin_success_message );
		} else {

			// Allow the user-defined 'success-single-optin' message to overwrite the default single opt-in response
			$default_response = $this->check_for_user_defined_response_message( 'success-single-optin', $this->default_response_single_optin_success_message );
		}

		// If they're not a new subscriber and we're updating their profile, then show them this message
		// Allow the user-defined 'success-resubscribed' message to overwrite the default already subscribed response
		$default_response = ( $new_subscriber === false ) ? $this->check_for_user_defined_response_message( 'success-resubscribed', $this->existing_subscriber_profile_update_message ) : $default_response;

		/**
		*	yikes-mailchimp-success-response
		*
		*	Filter the success message displayed to the user
		*
		*	@param string | $default_response	| The response message that will be shown to the user if unchanged (see above for logic)
		*	@param string | $form_id		 	| The form ID
		*	@param array  | $merge_variables 	| The array of user submitted form data
		*
		*/
		$response_message = apply_filters( 'yikes-mailchimp-success-response', $default_response, $this->form_id, $merge_variables );

		// Construct our success array variables
		$return_success_array = array(
			'hide'        		=> $submission_settings['hide_form_post_signup'],
			'error'       		=> 0,
			'response'    		=> $response_message,
			'redirection' 		=> $redirect_array['redirection'],
			'redirect'    		=> $redirect_array['redirect'],
			'new_window' 		=> $redirect_array['new_window'],
			'redirect_timer'	=> $redirect_array['redirect_timer'],
		);

		// Return success array
		return $this->yikes_success( $return_success_array );
	}

	/**
	* Handle an unsuccessful/error subscribe request
	*
	* @since 6.3.0
	*
	* @param object | $subscribe_response	| The response from the API
	* @param array  | $form_fields			| The array of form field definitions for this YIKES MailChimp form
	*/
	public function handle_submission_response_error( $subscribe_response, $form_fields ) {

		// Get the error data
		$error_data = $subscribe_response->get_error_data();
		$details    = '';

		// Loop through the error data and retrieve any fields and messages
		if ( isset( $error_data['data'] ) ) {
			foreach ( $error_data['data'] as $datum ) {
				if ( ! isset( $datum['field'], $datum['message'] ) ) {
					continue;
				}
				$details .= sprintf( '<br>Error with %1$s field: <strong>%2$s</strong>', $form_fields[ $datum['field'] ]['label'], $datum['message'] );
			}
		}

		// Get the error message and concat it to the error details string
		$error_message = $subscribe_response->get_error_message();
		if ( ! empty( $details ) ) {
			$error_message .= $details;
		}

		// Log the error
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log( $error_message, __( 'New Subscriber', 'yikes-inc-easy-mailchimp-extender' ), 'process_form_submission_ajax.php' );

		// Check for a user-defined 'general-error' message
		$error_message = $this->check_for_user_defined_response_message( 'general-error', $error_message );

		// Set up our return fields and send error
		$additional_response_fields = array( 'security_response' => 'test', 'data' => $subscribe_response->get_error_data() );

		return $this->yikes_fail( $hide = 0, $subscribe_response->get_error_code(), $error_message, $additional_response_fields, $return_response_non_ajax = true );
	}

	/**
	* Handle the redirect logic for successful submissions
	*
	* @since 6.3.0
	*
	* @param array | $submission_settings	| Array of the form's submission settings
	* @param array | $page_data				| Array of the page data
	* @return array| $redirect_array		| Array with two values: Redirection flag, Redirect URL
	*/
	protected function handle_submission_response_success_redirect( $submission_settings, $page_data ) {

		// Set up our return array with default values
		$redirect_array = array(
			'redirection' 	 => 0,
			'redirect'		 => '',
			'new_window'	 => false,
			'redirect_timer' => 1500,
		);

		// Let's confirm we have a value before trying to use it
		$redirect_setting = isset( $submission_settings['redirect_on_submission'] ) ? $submission_settings['redirect_on_submission'] : false;

		// Check the redirect flag
		if ( '1' === $redirect_setting ) {

			// Supply return array with default value of 1
			$redirect_array['redirection']	= 1;

			// Let's confirm we have redirect_page/custom_redirect_url/new_window values
			$redirect_page_setting	 = isset( $submission_settings['redirect_page'] ) ? $submission_settings['redirect_page'] : false;
			$custom_redirect_setting = isset( $submission_settings['custom_redirect_url'] ) ? $submission_settings['custom_redirect_url'] : false;
			$redirect_new_window	 = isset( $submission_settings['redirect_new_window'] ) ? $submission_settings['redirect_new_window'] : false;

			$redirect_array['new_window'] = $redirect_new_window;

			// Check if we're redirecting to a custom_url or just the redirect_page
			$redirect_url = ( 'custom_url' !== $redirect_page_setting ) ? get_permalink( $redirect_page_setting ) : $custom_redirect_setting;

			/**
			*	yikes-mailchimp-redirect-url
			*
			*	Catch the redirect URL before it's shown to the user
			*
			*	@param string | $redirect_url	| The URL that we will redirect to
			*	@param string | $form_id		| The ID of the current form being subscribed to
			*	@param array  | $page_data		| An array of data related to the page the form is on
			*/
			$redirect_url = apply_filters( 'yikes-mailchimp-redirect-url', $redirect_url, $this->form_id, $page_data );

			/**
			*	yikes-mailchimp-redirect-timer
			*
			*	Catch the redirect timer before it's sent to the JavaScript file
			*
			*	@param int | $default_redirect_time_ms | The default time (1500 milliseconds) to wait before redirecting
			*/
			$redirect_timer = apply_filters( 'yikes-mailchimp-redirect-timer', $this->default_redirect_time_ms, $this->form_id );

			$redirect_array['redirect_timer'] = $redirect_timer;

			$redirect_array['redirect'] = $redirect_url;
		}

		return $redirect_array;
	}


	/**** Handle empty fields / Check for required form data / Handle checks for things like honeypot, nonce, etc. ****/

	// Note: All of these functions return `return $this->yikes_fail()`. Check function for more info.

	/**
	* Check if the submitted form data is missing any required fields
	*
	* @since 6.3.0
	*
	* @param array | $data 			 | The array of user-submitted form values
	* @param array | $form_fields	 | The array of form field definitions for this YIKES MailChimp form
	*/
	public function check_for_required_form_fields( $data, $form_fields ) {

		// Set up our defaults
		$field_is_missing = false;
		$missing_fields = array();

		// Loop through submitted form data
		foreach( $data as $merge_tag => $value ) {

			// Skip interest groups
			if ( isset( $form_fields[ $merge_tag ]['group_id'] ) ) {
				continue;
			}

			// check if this field is required
			if ( isset( $form_fields[ $merge_tag ] ) && isset( $form_fields[ $merge_tag ]['require'] ) && $form_fields[ $merge_tag ]['require'] === '1' ) {

				// Check if the field(s) are empty
				if ( is_array( $value ) ) {

					// Loop through the data and check if any are empty
					foreach( $value as $field => $val ) {

						/**
						*	'yikes-mailchimp-ignore-required-array-field'
						*
						* 	Filter the default array of fields we're ignoring. As of now, this is only for address fields because no other field is an array.
						*
						*	@param array | Array of fields to ignore. Key of the array should be the field name.
						*	@param int   | $form_id
						*
						*	@return Array of fields to ignore.
						*/
						$ignored_fields = apply_filters( 'yikes-mailchimp-ignore-required-array-field', array( 'addr2' => true ), $this->form_id );

						if ( empty( $val ) && ! isset( $ignored_fields[ $field ] ) ) {
							$field_is_missing = true;

							// Set the merge label (e.g. MMERGE6) as the key so we don't get the same field multiple times
							// (e.g. For arrays, like an address, where multiple address fields are empty)
							$missing_fields[ $form_fields[ $merge_tag ]['merge'] ] = $form_fields[ $merge_tag ];
						}
					}

				} else if ( empty( $value ) ) {
					$field_is_missing = true;
					$missing_fields[ $form_fields[ $merge_tag ]['merge'] ] = $form_fields[ $merge_tag ];
				}
			}
		}

		// After we've looped through all the fields, check if we've found a missing field
		// Note: we do this at the end so we can highlight ALL of the missing fields, instead of the first one we found
		if ( $field_is_missing === true ) {

			// Construct our return array
			$additional_fields = array(
				'missing_required_field'		=> true,
				'missing_required_field_data'	=> $missing_fields,
				'is_interest_group'				=> false
			);

			/**
			*	yikes-mailchimp-required-form-field-missing
			*
			*	Alter the response message shown to the user for missing required form fields
			*
			*	@param string | $handle_empty_required_field_message	| The default message displayed to the user
			*	@param int	  | $form_id 								| The ID of the form
			*	@param array  | $missing_fields							| Array of the missing required fields
			*/
			$default_response = apply_filters( 'yikes-mailchimp-required-form-field-missing', $this->handle_empty_required_field_message, $this->form_id, $missing_fields );

			// If we've found a missing field, return the array of field data
			return $this->yikes_fail( $hide = 0, $error = 1, $default_response, $additional_fields );
		}
	}

	/**
	* Check if the submitted form interest group data is missing any required fields
	*
	* @since 6.3.0
	*
	* @param array | $data 			 | The array of user-submitted form values
	* @param array | $form_fields	 | The array of form field definitions for this YIKES MailChimp form
	*/
	public function check_for_required_interest_groups( $data, $form_fields ) {

		// Set up our defaults
		$field_is_missing = false;
		$missing_fields = array();

		// Loop through the form fields
		foreach ( $form_fields as $merge_tag => $field_data ) {

			// If an interest group and it's required
			if ( isset( $field_data['group_id'] ) && isset( $field_data['require'] ) && $field_data['require'] === '1' ) {
				
				// Check if it was submitted (meaning, check if it's set in our $data array)
				if ( ! isset( $data[ 'group-' . $merge_tag ] ) ) {

					$field_is_missing = true;
					$missing_fields[ $merge_tag ] = $field_data;
				}
			}
		}

		// After we've looped through all the fields, check if we've found a missing field
		// Note: we do this at the end so we can highlight ALL of the missing fields, instead of the first one we found
		if ( $field_is_missing === true ) {

			// Construct our return array
			$additional_fields = array(
				'missing_required_field'		=> true,
				'missing_required_field_data'	=> $missing_fields,
				'is_interest_group'				=> true
			);

			/**
			*	yikes-mailchimp-required-interest-group-missing
			*
			*	Alter the response message shown to the user for missing required form fields
			*
			*	@param string | $handle_empty_required_interest_group_message	| The default message displayed to the user
			*	@param int	  | $form_id 										| The ID of the form
			*	@param array  | $missing_fields									| Array of the missing required fields
			*/
			$default_response = apply_filters( 'yikes-mailchimp-required-interest-group-missing', $this->handle_empty_required_interest_group_message, $this->form_id, $missing_fields );

			// If we find a required interest group with an empty value, send an error
			return $this->yikes_fail( $hide = 0, $error = 1, $default_response, $additional_fields );
		}
	}

	/**
	* Handle the reCAPTCHA
	*
	* @since 6.3.0
	*
	* @param string | $recaptcha_response | The form value of the recaptcha field
	*/
	public function handle_recaptcha( $recaptcha_response ) {

		// Before we the hit the API, let's check that we actually got a response.
		// If the user did not fill anything in (e.g. did not hit the checkbox), then the response will be empty.
		if ( empty( $recaptcha_response ) ) {

			/**
			*	yikes-mailchimp-recaptcha-required-error
			*
			*	Catch the recaptcha errors before they're returned to the user
			*	@param string | $recaptcha_errors | A string of recaptcha errors separated by a space
			*/
			$response = apply_filters( 'yikes-mailchimp-recaptcha-required-error', $this->handle_non_filled_recaptcha_message_message, $this->form_id );
			return $this->yikes_fail( $hide = 0, $error = 1, $response, array(), $return_response_non_ajax = true );
		}

		// Construct the API URL
		$url           = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key', '' ) . '&response=' . $recaptcha_response . '&remoteip=' . $_SERVER['REMOTE_ADDR'] );
		$response      = wp_remote_get( $url );
		$response_body = json_decode( $response['body'], true );

		// Set up errors array
		$recaptcha_errors = array();

		// if we've hit an error, lets return the error!
		if ( true !== $response_body['success'] ) {

			if( isset( $response_body['error-codes'] ) ) {

				// Loop through response error codes
				foreach ( $response_body['error-codes'] as $error_code ) {
					if ( 'missing-input-response' === $error_code ) {
						$error_code = $this->handle_non_filled_recaptcha_message;
					}

					// Add our error_code to the errors array
					$recaptcha_errors[] = $error_code;
				}

			} else {
				$recaptcha_errors[] = $this->generic_recaptcha_error_message;
			}

			/**
			*	yikes-mailchimp-recaptcha-required-error
			*
			*	Catch the recaptcha errors before they're returned to the user
			*	@param string | $recaptcha_errors | A string of recaptcha errors separated by a space
			*/
			$response = apply_filters( 'yikes-mailchimp-recaptcha-required-error', implode( ' ', $recaptcha_errors ), $this->form_id );
			return $this->yikes_fail( $hide = 0, $error = 1, $response, array(), $return_response_non_ajax = true );
		}
	}

	/**
	* Handle the nonce field
	*
	* @since 6.3.0
	*
	* @param string | $nonce_value | The form value of the nonce
	* @param string | $nonce_name  | The name of the nonce
	*/
	public function handle_nonce( $nonce_value, $nonce_name ) {

		// First, check our option - this is set in the general settings page
		if ( get_option( 'yikes-mailchimp-use-nonce' ) === '1' ) {

			/**
			*	yikes-mailchimp-use-nonce-verification
			*
			*	Decide if we're going to check the nonce value.
			*	The reason we filter this is that some users are experiencing nonce issues repeatedly.
			*	The default will always be to use the nonce.
			*
			*	@param  int  | $form_id  | The form id
			*
			*	@return bool | True if we should check the nonce
			*/
			$use_nonce = apply_filters( 'yikes-mailchimp-use-nonce-verification', true, $this->form_id );

			// We let the filter override the option because the filter is on a per-form basis 
			if ( $use_nonce === true ) {
				if ( wp_verify_nonce( $nonce_value, $nonce_name ) === false ) {
					return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_nonce_message );
				}
			}
		}
	}

	/**
	* Handle a merge_variables error
	*
	* @since 6.3.0
	*
	* @param int	| $error	| Int $error = 1 if an error
	* @param string | $message  | The message shown to the user
	*/
	public function handle_merge_variables_error( $error, $message ) {
		return $this->yikes_fail( $hide = 0, $error, $message, array(), $return_response_non_ajax = true );
	}

	/**
	* Handle an empty email field and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param string | $email
	*/
	public function handle_empty_email( $email ) {
		if ( empty( $email ) ) {
			return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_empty_email_message );
		}
	}

	/**
	* Check if the form is empty and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param array | $form_data
	*/
	public function handle_empty_form( $form_data ) {
		if ( empty( $form_data ) ) {
			return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_empty_form_message );
		}
	}

	/**
	* Check if the honeypot is NOT empty and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param bool | $honey_pot_filled | True if the honeypot was filled out
	*/
	public function handle_non_empty_honeypot( $honey_pot_filled ) {
		if ( $honey_pot_filled === true ) {
			return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_non_empty_honeypot_message );
		}
	}

	/**
	* Loop through fields looking for null and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param array | $fields_array | An array of fields to loop through and make sure they're not null
	*/
	public function handle_empty_fields_generic( $fields_array ) {
		foreach( $fields_array as $field ) {
			if ( $field === null ) {
				return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_empty_fields_generic_message );
			}
		}
	}

	/**
	* Check if the list handler is empty and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param class | $list_handler | A class that handles list functions
	*/
	public function handle_empty_list_handler( $list_handler ) {
		if ( empty( $list_handler ) ) {
			return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_empty_list_handler_message );
		}
	}

	/**
	* Check if the form id is empty and return the corresponding error message
	*
	* @since 6.3.0
	*
	* @param int | $form_id | The form ID
	*/
	public function handle_empty_form_id( $form_id ) {
		if ( empty( $form_id ) ) {
			return $this->yikes_fail( $hide = 0, $error = 1, $this->handle_empty_form_id_message );
		}
	}

	/**
	* Construct and filter the error message related to user's re-subscribing when it's not allowed
	*
	* @since 6.3.0
	*/
	public function handle_disallowed_existing_user_update() {

		// Get the default response
		$default_response = $this->handle_disallowed_existing_user_update_message;

		// Run the default response through our function to check for a user-defined response message
		$response = $this->check_for_user_defined_response_message( 'already-subscribed', $default_response );

		return $this->yikes_fail( $hide = 0, $error = 1, $response, array(), $return_response_non_ajax = true );	
	}

	/**
	* Construct and filter the error message related to the profile link to update user's profile
	*
	* @since 6.3.0
	*/
	public function handle_updating_existing_user() {

		// Get the first half of the message
		$response = $this->handle_updating_existing_user_message;

		// Create our link variables
		$link_start_tag = '<a class="send-update-email" data-form-id="' . $this->form_id . '" data-list-id="' . $this->list_id . '" data-user-email="' . $this->email . '" href="#">';
		$link_close_tag = '</a>';
		$link_array = array( 'link_start_tag' => $link_start_tag, 'link_close_tag' => $link_close_tag );

		// Append our 'send-update-email' link and text
		$response .= $link_start_tag;
		$response .= 	$this->handle_updating_existing_user_link_message;
		$response .= $link_close_tag;

		// Check for a user-defined message
		$response = $this->check_for_user_defined_response_message( 'update-link', $response, $link_array );

		return $this->yikes_fail( $hide = 0, $error = 1, $response, array(), $return_response_non_ajax = true );
	}

	/**** Helper Functions ****/

	/**
	* Check the user-defined $error_messages array for a message, filter it, and return it. These messages overwrite the defaults.
	*
	* @since 6.3.0
	*
	* @param string | $slug 		| The type of message we're looking for
	* @param string | $response_text| The default response message
	* @param array  | $data			| An array of data that may be needed to construct the user's error message
	* @return string| $response_text| The $response_text (after it's potentially been changed)
	*/
	protected function check_for_user_defined_response_message( $slug, $response_text, $data = false ) {

		switch( $slug ) {
			case 'already-subscribed':

				// Check if this error message exists
				if ( isset( $this->error_messages['already-subscribed'] ) && ! empty( $this->error_messages['already-subscribed'] ) ) {

					// Check if the substring (that we replace) '[email]' is located in the string and replace it
					$response_text = str_replace( '[email]', $this->email, $this->error_messages['already-subscribed'] );
				}

				/**
				*	yikes-easy-mailchimp-user-already-subscribed-text
				*
				*	Catch the message for user's already subscrbed before we show it to the user
				*	@param string | $message  | The response message
				*	@param int	  | $form_id  | The form id
				*	@param string | $email	  | The user's email
				*/
				$response_text = apply_filters( 'yikes-mailchimp-user-already-subscribed-text', $response_text, $this->form_id, $this->email );

				return $response_text;
				break;

			case 'update-link':

				// Check if this error message exists
				if ( $data !== false && isset( $this->error_messages['update-link'] ) && ! empty( $this->error_messages['update-link'] ) ) {

					// Check if the substring (that we replace) '[link]' is located in the string and replace it
					$response_text = str_replace( '[link]', $data['link_start_tag'], $this->error_messages['update-link'] );

					// Remove [/link]
					$response_text = str_replace( '[/link]', $data['link_close_tag'], $response_text );
				}

				/**
				*	yikes-easy-mailchimp-user-already-subscribed-link-text
				*
				*	Catch the message for user's already subscrbed link text before we show it to the user
				*
				*	@param string | $response_text	| The response message that will be shown to the user
				*	@param string | $form_id		| The form ID
				*/
				$response_text = apply_filters( 'yikes-mailchimp-user-already-subscribed-link-text', $response_text, $this->form_id );

				return $response_text;
				break;

			case 'success':

				// 'success' is the user-defined success message for double opt-in
				if ( isset( $this->error_messages['success'] ) && ! empty( $this->error_messages['success'] ) ) {
					$response_text = $this->error_messages['success'];
				}

				/**
				*	yikes-mailchimp-success-double-optin-response
				*
				*	Filter the success message displayed to the user
				*
				*	@param string | $response_text	| The response message that will be shown to the user
				*	@param string | $form_id		| The form ID
				*
				*/
				$response_text = apply_filters( 'yikes-mailchimp-success-double-optin-response', $response_text, $this->form_id );

				return $response_text;
				break;

			case 'success-single-optin':

				if ( isset( $this->error_messages['success-single-optin'] ) && ! empty( $this->error_messages['success-single-optin'] ) ) {
					$response_text = $this->error_messages['success-single-optin'];
				}

				/**
				*	yikes-mailchimp-success-single-optin-response
				*
				*	Filter the success message displayed to the user
				*
				*	@param string | $response_text	| The response message that will be shown to the user
				*	@param string | $form_id		| The form ID
				*
				*/
				$response_text = apply_filters( 'yikes-mailchimp-success-single-optin-response', $response_text, $this->form_id );

				return $response_text;
			break;

			case 'success-resubscribed':

				if ( isset( $this->error_messages['success-resubscribed'] ) && ! empty( $this->error_messages['success-resubscribed'] ) ) {
					$response_text = $this->error_messages['success-resubscribed'];
				}

				/**
				*	yikes-mailchimp-success-resubscribed-response
				*
				*	Filter the success message displayed to the user
				*
				*	@param string | $response_text	| The response message that will be shown to the user
				*	@param string | $form_id 		| The form ID
				*
				*/
				$response_text = apply_filters( 'yikes-mailchimp-success-resubscribed-response', $response_text, $this->form_id );

				return $response_text;
			break;

			case 'general-error':

				$original_response_text = $response_text;

				if ( isset( $this->error_messages['general-error'] ) && ! empty( $this->error_messages['general-error'] ) ) {
					$user_defined_response_text = $this->error_messages['general-error'];
				}

				/**
				*	yikes-mailchimp-general-error-response
				*
				*	Filter the error message displayed to the user
				*
				*	@param string | $original_response_text     | The original response message returned from the API
				*	@param string | $user_defined_response_text | The response message defined by the user
				*	@param string | $form_id                    | The form ID
				*
				* 	@return string | $response_text | The message that will be shown to the user 
				*/
				$response_text = apply_filters( 'yikes-mailchimp-general-error-response', $original_response_text, $user_defined_response_text, $this->form_id );

				return $response_text;
			break;

			// Default to just returning the message supplied to us
			case 'default':
				return $response_text;
			break;
		}
	}

	/**
	* Wrap the response message in HTML for Non-AJAX form submissions
	*
	* @since 6.3.0
	*
	* @param string | $message		| The response message
	* @param bool	| $is_success	| Boolean signifying if we're returning a success message or an error message
	* @return string| The $message wrapping in HTML
	*/
	public function wrap_form_submission_response( $message, $is_success ) {

		// If we're successful, we wrap the $message differently
		if ( $is_success === true ) {
			return '<p class="yikes-easy-mc-success-message yikes-easy-mc-hidden">' . $message . '</p>';
		} else {
			return '<p class="yikes-easy-mc-error-message yikes-easy-mc-hidden">' . $message . '</p>';
		}
	}

	/**** Returning Success / Failure Functions ****/

	/**
	* Return success. Method of returning success based on the $is_ajax flag
	*
	* @since 6.3.0
	*
	* @param array | $success_array | Array of success values to return
	*
	* @return If AJAX, return wp_send_json_success(). If not AJAX, set the global $process_submission_response variable and simply `return`.
	*/
	protected function yikes_success( $success_array ) {
		if ( $this->is_ajax === true ) {
			wp_send_json_success( $success_array );
		} else {
			global $process_submission_response;

			$process_submission_response = isset( $success_array['response'] ) ? $success_array['response'] : ''; // DEFAULT SUCCESS?
			$process_submission_response = $this->wrap_form_submission_response( $process_submission_response, $is_success = true );
		}
	}

	/**
	* Return failure. Method of returning failure based on the $is_ajax flag
	*
	* @since 6.3.0
	*
	* @param int	| $hide						| Flag whether to hide the form (1 = hide, 0 = do not hide)
	* @param int	| $error					| Flag whether this is an error (1 = error, 0 = no error)
	* @param string | $response					| The response message to display to the user
	* @param array  | $additional_fields		| An array of additional fields to return
	* @param bool	| $return_response_non_ajax | Boolean deciding if we need to return a message
	*
	* @return If AJAX, return $this->yikes_send_json_error(). If not AJAX, return an array || false.
	*/	
	protected function yikes_fail( $hide, $error, $response, $additional_fields = array(), $return_response_non_ajax = false ) {
		if ( $this->is_ajax === true ) {
			$this->yikes_send_json_error( $hide, $error, $response, $additional_fields );
		} else {
			if ( $return_response_non_ajax === true ) {
				return array( 'success' => false, 'message' => $response );
			}
			return false;
		}
	}

	/**
	* Wrapper function for wp_send_json_error()
	*
	* @since 6.3.0
	*
	* @param int	| $hide						| Flag whether to hide the form (1 = hide, 0 = do not hide)
	* @param int	| $error					| Flag whether this is an error (1 = error, 0 = no error)
	* @param string | $translated_string		| The response message to display to the user
	* @param array  | $additional_fields		| An array of additional fields to return
	*
	* @return func  | wp_send_json_error()
	*/
	protected function yikes_send_json_error( $hide, $error, $translated_string, $additional_fields = array() ) {

		// Default response array
		$response_array = array(
			'hide'		=> $hide,
			'error'		=> $error,
			'response'	=> $translated_string	
		);

		// Add additional fields we've been supplied
		if ( ! empty( $additional_fields ) ) {

			foreach( $additional_fields as $key => $value ) {
				$response_array[$key] = $value;
			}
		}

		wp_send_json_error( $response_array );
	}

}
