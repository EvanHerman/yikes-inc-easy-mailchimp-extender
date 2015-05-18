<?php
	/*
	* Takes care of all the sign-up checkboxes
	*
	*/
	class Yikes_Easy_MC_Checkbox_Integration_Class {
	
		// declare our integration type
		protected $type = 'integration';
		private $text_domain = 'yikes-inc-easy-mailchimp-extender';
		
		public function __construct() {	
			
		}
					
		/*
		*	Check if a user is already subscribed to
		*	a given list, if so don't show the checkbox integration
		*/
		public function is_user_already_subscribed( $integration_type ) {
			// first check if the user is logged in
			if( is_user_logged_in() ) {
				$checkbox_options = get_option( 'optin-checkbox-init' , '' );
				$current_user = wp_get_current_user();
				$email = $current_user->user_email;
				try {
					$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
					// subscribe the user
					$already_subscribed = $MailChimp->call('/lists/member-info', array( 
						'api_key' => get_option( 'yikes-mc-api-key' , '' ),
						'id' => $checkbox_options[$integration_type]['associated-list'],
						'emails' => array( array( 'email' => sanitize_email( $email ) ) ),
					) );
					return $already_subscribed['success_count'];
				} catch ( Exception $error ) {
					return $error->getMessage();
				}
			} else {
				// if the user isn't logged in
				// we'll always display it
				return '0';
			}
		}			
		
		/*
		*	Check if a new user registration email already subscribed
		*	a given list, if so don't show the checkbox integration
		*/
		public function is_new_registration_already_subscribed( $email , $integration_type ) {
			// first check if the user is logged in
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );
			try {
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// subscribe the user
				$already_subscribed = $MailChimp->call('/lists/member-info', array( 
					'api_key' => get_option( 'yikes-mc-api-key' , '' ),
					'id' => $checkbox_options[$integration_type]['associated-list'],
					'emails' => array( array( 'email' => sanitize_email( $email ) ) ),
				) );
				return $already_subscribed['success_count'];
			} catch ( Exception $error ) {
				return $error->getMessage();
			}
		}
			
		/**
		* @param mixed $args Array or string
		* @return string
		*/
		public function yikes_get_checkbox( $args=array() ) {
			
			// if the user is already subscribed to the given list,
			// theres no need to display the checkbox to re-subscribe. abort!
			if( $this->is_user_already_subscribed( $this->type ) == '1' ) {
				return false;
			}
			
			// enqueue our checkbox styles whenever the checkbox is displayed
			wp_enqueue_style( 'yikes-easy-mailchimp-checkbox-integration-styles', plugin_dir_url( __FILE__ ) . '../css/yikes-inc-easy-mailchimp-checkbox-integration.min.css' );
			// store our options
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );
			
			if( isset( $checkbox_options[$this->type]['associated-list'] ) && $checkbox_options[$this->type]['associated-list'] == '-' ) {
				$error_response = '<p><em><input title="' . __( 'No valid list selected' , $this->text_domain ) . '" type="checkbox" name="yikes_mailchimp_checkbox_' . $this->type . '" value="1" disabled /> ' . __( 'Please select a valid list to assign users too.' , $this->text_domain ) . '</em></p>';
				/* If the current user is logged in, and an admin...lets display our 'Edit Form' link */
				if( is_user_logged_in() ) {
					if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
						$error_response .= '<span class="edit-link yikes-easy-mailchimp-edit-form-link">';
							$error_response .= '<a class="post-edit-link" href="' . admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=checkbox-settings' ) . '" title="' . __( 'Edit Integration Settings' , $this->text_domain ) . '">' . __( 'Edit Checkbox Integration Settings' , $this->text_domain ) . '</a>';
						$error_response .= '</span>';
					}
				}
				return $error_response;
			}	
			
			// set up the checkbox label
			$label = $checkbox_options[$this->type]['label'];
			
			// CF7 checkbox -
			// setup a different label if specified
			if( is_array( $args ) && isset( $args['options'] ) ) {
				// check if one was set
				if( isset( $args['raw_values'][0] ) ) {
					$label = $args['raw_values'][0];
				}
			}
			
			if( isset( $checkbox_options[$this->type]['associated-list'] ) && $checkbox_options[$this->type]['associated-list'] != '-' ) {
				$checked = ( $checkbox_options[$this->type]['precheck'] == 'true' ) ? 'checked' : '';
				// before checkbox HTML (comment, ...)
				$before = '<!-- Easy MailChimp Forms by Yikes Inc - https://www.yikesinc.com/ -->';
				$before .= apply_filters( 'yikes-mailchimp-before-checkbox-html', '' );
				// checkbox
				$content = '<p id="yikes-easy-mailchimp-' . $this->type . '-checkbox" class="yikes-easy-mailchimp-' . $this->type . '-checkbox">';
					$content .= '<label>';
						$content .= '<input type="checkbox" name="yikes_mailchimp_checkbox_' . $this->type . '" value="1" '. $checked . ' /> ';
						$content .= $label;
					$content .= '</label>';
				$content .= '</p>';
				// after checkbox HTML (..., honeypot, closing comment)
				$after = apply_filters( 'yikes-mailchimp-after-checkbox-html', '' );
				$after .= '<!-- Easy MailChimp Forms by Yikes Inc -->';
				return $before . $content . $after;
			}
		}	
	
		/**
		 *	Hook to submit the data to MailChimp when 
		 *	a new comment is submitted
		 *
		 *	@since 6.0.0
		**/
		public function subscribe_user_integration( $email , $type , $merge_vars ) {
			// get checkbox data
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );
			if( $type != 'registration_form' ) {
				$update = '1';
			} else {
				$update = '0';
			}
			// set ip address
			if( ! isset( $merge_vars['OPTIN_IP'] ) && isset( $_SERVER['REMOTE_ADDR'] ) ) {
				$merge_vars['OPTIN_IP'] = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
			}
			// initialize MailChimp API
			try {
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// subscribe the user
				$subscribe_response = $MailChimp->call('/lists/subscribe', array( 
					'api_key' => get_option( 'yikes-mc-api-key' , '' ),
					'id' => $checkbox_options[$type]['associated-list'],
					'email' => array( 'email' => sanitize_email( $email) ),
					'merge_vars' => $merge_vars,
					'double_optin' => 0,
					'update_existing' => $update,
					'send_welcome' => 1
				) );
			} catch( Exception $e ) { 
				// log to our error log
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $e->getMessage() , __( "User subscribe via" , $this->text_domain ) . ' ' . $type , __( "MailChimp Opt-in Form" , $this->text_domain ) );
			}
		}
		
		/**
		* Build merge varaibles array
		*	@since 6.0.0
		*/	
		public function user_merge_vars( WP_User $user ) {
			// start with user_login as name, since that's always known
			$merge_vars = array(
				'NAME' => $user->user_login,
			);
			if( '' !== $user->first_name ) {
				$merge_vars['NAME'] = $user->first_name;
				$merge_vars['FNAME'] = $user->first_name;
			}
			if( '' !== $user->last_name ) {
				$merge_vars['LNAME'] = $user->last_name;
			}
			if( '' !== $user->first_name && '' !== $user->last_name ) {
				$merge_vars['NAME'] = sprintf( '%s %s', $user->first_name, $user->last_name );
			}
			/**
			 * @filter `yikes-mailchimp-user-merge-vars`
			 * @expects array
			 * @param array $merge_vars
			 * @param WP_User $user
			 *
			 * Use this to filter the merge vars of a user
			 */
			$merge_vars = (array) apply_filters( 'yikes-mailchimp-user-merge-vars', $merge_vars, $user );
			return $merge_vars;
		}	
		
		/*
		*	Confirm the checkbox was checked
		*	before continuing
		*	@since 6.0.0
		*/
		public function was_checkbox_checked( $type ) {
			// was sign-up checkbox checked - return the value
			return ( isset( $_POST[ 'yikes_mailchimp_checkbox_'.$type ] ) && $_POST[ 'yikes_mailchimp_checkbox_'.$type ] == 1 );
		}
		
		
		/**
		*	Alter the registraton complete message	
		*	if the registration form checkbox integration is toggled on
		* 	@since 6.0.0
		**/
		public function yikes_reg_complete_msg( $errors, $redirect_to ) {
			if( isset( $errors->errors['registered'] ) ) {
				$email_error = get_option( 'yikes_register_subscription_error' , '' );
				if( isset( $email_error ) && $email_error != '' ) {	
					// Use the magic __get method to retrieve the errors array:
					$tmp = $errors->errors; 
					$old = 'Registration complete. Please check your e-mail.';
					foreach( $tmp['registered'] as $index => $msg ) {
						if( $msg === $old ) {
							$tmp['registered'][$index] = $old . ' <p class="message"><strong>' . __( 'Note' , $this->text_domain ) . '</strong>: ' . $email_error . '</p>';        
						}
					}
					// Use the magic __set method to override the errors property:
					$errors->errors = $tmp;
					// Cleanup:
					unset( $tmp );
					delete_option( 'yikes_register_subscription_error' );
				}
		   }
		   return $errors;
		}
		
		/**
		*	Attempt to subscribe a user
		*	from the current $_POST data (cf7 integration)
		*	@since 6.0.0
		**/
		public function attempt_subscription() {
			// start running..
			$email = null;
			$merge_vars = array(
				'GROUPINGS' => array()
			);
			
			foreach( $_POST as $key => $value ) {
				if( $key[0] === '_' || $key === 'yikes_mailchimp_checkbox_contact_form_7' ) {
					continue;
				} elseif( strtolower( substr( $key, 0, 7 ) ) === 'yikes_' ) {
					// find extra fields which should be sent to MailChimp
					$key = strtoupper( substr( $key, 6 ) );
					$value = ( is_scalar( $value ) ) ? sanitize_text_field( $value ) : $value;
					switch( $key ) {
						case 'EMAIL':
							$email = $value;
						break;
						case 'GROUPINGS':
							$groupings = (array) $value;
							foreach( $groupings as $grouping_id_or_name => $groups ) {
								$grouping = array();
								// group ID or group name given?
								if(is_numeric( $grouping_id_or_name ) ) {
									$grouping['id'] = absint( $grouping_id_or_name );
								} else {
									$grouping['name'] = sanitize_text_field( stripslashes( $grouping_id_or_name ) );
								}
								// comma separated list should become an array
								if( ! is_array( $groups ) ) {
									$groups = explode( ',', sanitize_text_field( $groups ) );
								}
								$grouping['groups'] = array_map( 'stripslashes', $groups );
								// add grouping to array
								$merge_vars['GROUPINGS'][] = $grouping;
							} // end foreach $groupings
						break;
						default:
							if( is_array( $value ) ) {
								$value = sanitize_text_field( implode( ',', $value ) );
							}
							$merge_vars[$key] = $value;
						break;
					}
				} elseif( ! $email && is_string( $value ) && is_email( $value ) ) {
					// if no email is found yet, check if current field value is an email
					$email = $value;
				} elseif( ! $email && is_array( $value ) && isset( $value[0] ) && is_string( $value[0] ) && is_email( $value[0] ) ) {
					// if no email is found yet, check if current value is an array and if first array value is an email
					$email = $value[0];
				} else {
					$simple_key = str_replace( array( '-', '_' ), '', strtolower( $key ) );
					if( ! $email && in_array( $simple_key, array( 'email', 'emailaddress' ) ) ) {
						$email = $value;
					} elseif( ! isset( $merge_vars['NAME'] ) && in_array( $simple_key, array( 'name', 'yourname', 'username', 'fullname' ) ) ) {
						// find name field
						$merge_vars['NAME'] = $value;
					} elseif( ! isset( $merge_vars['FNAME'] ) && in_array( $simple_key, array( 'firstname', 'fname', 'givenname', 'forename' ) ) ) {
						// find first name field
						$merge_vars['FNAME'] = $value;
					} elseif( ! isset( $merge_vars['LNAME'] ) && in_array( $simple_key, array( 'lastname', 'lname', 'surname', 'familyname' ) ) ) {
						// find last name field
						$merge_vars['LNAME'] = $value;
					}
				}
			}
			// unset groupings if not used
			if( empty( $merge_vars['GROUPINGS'] ) ) {
				unset( $merge_vars['GROUPINGS'] );
			}
			// if email has not been found by the smart field guessing, return false.. Sorry
			if ( ! $email ) {
				return false;
			}
			return $this->subscribe_user_integration( $email, $this->type, $merge_vars );
		}
		
	}
	
	new Yikes_Easy_MC_Checkbox_Integration_Class;
?>