<?php
	
	/*
	*	AJAX Form Submission Processing
	*	Begin below young grasshopper
	*/
					
			// parse our form data
			parse_str( $_POST['form_data'], $data );
			// store the form ID to use in our hooks and filters
			$form = $_POST['form_id'];
			
			// Retreive the form data from the database instead of posting it with the form-submission
			global $wpdb;
			// return it as an array, so we can work with it to build our form below
			$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form . '', ARRAY_A );
			
			if( $form_results ) {
				$form_data = $form_results[0];
				// List ID
				$list_id = $form_data['list_id']; 
				// decode our submission settings
				$submission_settings = json_decode( stripslashes( $form_data['submission_settings'] ), true );
				// decode our optin settings
				$optin_settings = json_decode( stripslashes( $form_data['optin_settings'] ), true );
				/*	Decode our error messages
				*	Workaround for international characters (cyrillic etc) 
				* 	See: https://wordpress.org/support/topic/custom-messages-do-not-support-cyrillic-characters?replies=11#post-7629620 
				*/
				$error_messages = ( get_magic_quotes_gpc() ) ? json_decode( stripslashes( $form_data['error_messages'] ), true ) : json_decode( $form_data['error_messages'], true );
				/** Submit Process **/
				$notifications = json_decode( stripslashes( $form_data['custom_notifications'] ), true );
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
				wp_send_json( array( 
					'hide' => '0', 
					'error' => 1,
					'response' => __( "Error: It looks like the honeypot was filled out and the form was not properly be submitted." , 'yikes-inc-easy-mailchimp-extender' )				
				) );
				return;
			}
	
			// Check reCaptcha Response was submitted with the form data
			if( isset( $data['g-recaptcha-response'] ) ) {
				$url = esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify?secret=' . get_option( 'yikes-mc-recaptcha-secret-key' , '' ) . '&response=' . $data['g-recaptcha-response'] . '&remoteip=' . $_SERVER["REMOTE_ADDR"] );
				$response = wp_remote_get( $url );
				$response_body = json_decode( $response['body'] , true );
				// if we've hit an error, lets return the error!
				if( $response_body['success'] != 1 ) {
					$error_messages = array(); // empty array to store error messages
					if( isset( $response_body['error-codes'] ) ) {	
						foreach( $response_body['error-codes'] as $error_code ) {
							$error_messages[] = __( 'Error', 'yikes-inc-easy-mailchimp-extender' ) . ': ' . $error_code;
						}
					} else {
						$error_messages[] = __( 'Please refresh the page and try again.', 'yikes-inc-easy-mailchimp-extender' );
					}
					$error = 1;
					wp_send_json( array( 
						'hide' => '0', 
						'error' => $error ,
						'response' => __( "It looks like we've run into a reCaptcha error." , 'yikes-inc-easy-mailchimp-extender' ) .' '. implode( ' ', $error_messages ),
					) );
					exit();
				}	
			}
			
			// loop to push variables to our array
			foreach ( $data as $merge_tag => $value ) {
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
				// send our error response back
				wp_send_json( array( 'hide' => '0', 'error' => $merge_variables['error']  , 'response' => $merge_variables['message']  ) );
				return;
			}			
						
			// submit the request & data, using the form settings
			try {
								
				$subscribe_response = $MailChimp->call('/lists/subscribe', apply_filters( 'yikes-mailchimp-user-subscribe-api-request', array( 
					'api_key' => $api_key,
					'id' => $list_id,
					'email' => array( 'email' => sanitize_email( $data['EMAIL'] ) ),
					'merge_vars' => $merge_variables,
					'double_optin' => $optin_settings['optin'],
					'update_existing' => $optin_settings['update_existing_user'],
					'send_welcome' => $optin_settings['send_welcome_email'],
					'replace_interests' => ( isset( $submission_settings['replace_interests'] ) ) ? $submission_settings['replace_interests'] : 1, // defaults to replace
				), $form, $list_id, $data['EMAIL'] ) );
								
				// set the global variable to 1, to trigger a successful submission
				$form_submitted = 1;
				/*
				*	Successful form submission redirect
				*/
				if( $submission_settings['redirect_on_submission'] == '1' ) {
					$redirection = '1';
					$redirect = '<script type="text/javascript">setTimeout(function() { window.location="' . apply_filters( 'yikes-mailchimp-redirect-url', get_permalink( $submission_settings['redirect_page'] ), $form, $page_data ) . '"; }, ' . apply_filters( 'yikes-mailchimp-redirect-timer', 1500 ) . ');</script>';
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
				do_action( 'yikes-mailchimp-after-submission-'.$form , $merge_variables );
				
				// send our notifications if setup (must go before wp_send_json())
				do_action( 'yikes-mailchimp-form-submission' , sanitize_email( $data['EMAIL'] ) , $merge_variables , $form , $notifications );
				do_action( 'yikes-mailchimp-form-submission-' . $form , sanitize_email( $data['EMAIL'] ) , $merge_variables , $form , $notifications );
				
				$default_success_response = ( $optin_settings['optin'] == 1 ) ? __( "Thank you for subscribing! Check your email for the confirmation message." , 'yikes-inc-easy-mailchimp-extender' ) : __( "Thank you for subscribing!" , 'yikes-inc-easy-mailchimp-extender' );
				
				wp_send_json( 
					array( 
						'hide' => $submission_settings['hide_form_post_signup'], 
						'error' => $error, 
						'response' => ! empty( $error_messages['success'] ) ? $error_messages['success'] : $default_success_response, 
						'redirection' => isset( $redirection ) ? '1' : '0', 
						'redirect' => isset( $redirect ) ? $redirect : '',
					) 
				);				
					
				// end successful submission
				
			} catch ( Exception $error ) { // Something went wrong...
				$error_response = $error->getMessage();
				$error = 1;
				if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					if( strpos( $error_response, 'must be provided' ) !== false ) {
						$boom = explode( ' ', $error_response );
						$merge_variable = $boom[0];
						$api_key = get_option( 'yikes-mc-api-key' , '' );
						$MailChimp = new MailChimp( $api_key );
						try {	
							$available_merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $list_id ) ) );
							foreach( $available_merge_variables['data'][0]['merge_vars'] as $merge_var ) {
								if( $merge_var['tag'] == $merge_variable ) {
									$field_name = $merge_var['name'];
								}
							}
							$error_response = str_replace( $merge_variable , '<strong>"' . $field_name . '"</strong>' , $error_response );
						} catch ( Exception $e ) {
							$error_response = $e->getMessage();
						}
					}
					// send our error response back
					wp_send_json( array( 'hide' => '0', 'error' => $error , 'response' => $error_response ) );
				} else {
					if ( strpos( $error_response, 'should include an email' ) !== false ) {  // include a valid email please
						wp_send_json( array( 'hide' => '0', 'error' => $error , 'response' => !empty( $error_messages['invalid-email'] ) ? $error_messages['invalid-email'] :  __( 'Please enter a valid email address.' , 'yikes-inc-easy-mailchimp-extender' ) ) );
					} else if ( strpos( $error_response, 'already subscribed' ) !== false ) { // user already subscribed
						wp_send_json( array( 'hide' => '0', 'error' => $error , 'response' => !empty( $error_messages['already-subscribed'] ) ? $error_messages['already-subscribed'] : __( "It looks like you're already subscribed to this list." , 'yikes-inc-easy-mailchimp-extender' ) ) );
					} else { // general error
						wp_send_json( array( 'hide' => '0', 'error' => $error , 'response' => !empty( $error_messages['general-error'] ) ? $error_messages['general-error'] : __( "Whoops, something went wrong! Please try again." , 'yikes-inc-easy-mailchimp-extender' ) ) );
					}
				}
			}