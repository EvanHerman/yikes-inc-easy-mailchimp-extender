<?php
	/*
	* Takes care of all the sign-up checkboxes
	*	- Main class file that is used to extend all other extensions
	*/

	// Prevent direct access to the file
	defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );

	class Yikes_Easy_MC_Checkbox_Integration_Class {

		// declare our integration type
		protected $type = 'integration';
		private $text_domain = 'yikes-inc-easy-mailchimp-extender';

		public function __construct() {

		}

		/**
		 * Determine whether the current user is already subscribed to a given list.
		 *
		 * @author Jeremy Pry
		 *
		 * @param string $type  The integration type to check.
		 * @param string $email The email address to check.
		 *
		 * @return bool Whether the current user is subscribed to a list.
		 */
		public function is_user_already_subscribed( $type, $email = '' ) {
			// Make sure we have an email address to use.
			if ( empty( $email ) ) {
				if ( ! is_user_logged_in() ) {
					return false;
				}

				$current_user = wp_get_current_user();
				$email        = $current_user->user_email;
			}

			// Ensure we have a valid email.
			if ( ! is_email( $email ) ) {
				return false;
			}

			// Convert the integration type to a list ID
			$checkbox_options = get_option( 'optin-checkbox-init', '' );
			if ( empty( $checkbox_options ) ) {
				return false;
			}
			if ( ! isset( $checkbox_options[ $type ] ) || ! isset( $checkbox_options[ $type ]['associated-list'] ) ) {
				return false;
			}

			return $this->is_user_subscribed( $email, $checkbox_options[ $type ]['associated-list'], $type );
		}

		/**
		 * Determine whether a given email is subscribed to a given list.
		 *
		 * @author Jeremy Pry
		 *
		 * @param string $email   The email address to check.
		 * @param string $list_id The list ID to check.
		 * @param string $type    The integration type
		 *
		 * @return bool Whether the email is subscribed to the list.
		 */
		public function is_user_subscribed( $email, $list_id, $type ) {
			$email_hash = md5( $email );

			// Check the API to see the status
			$response = yikes_get_mc_api_manager()->get_list_handler()->get_member( $list_id, $email_hash, false );
			if ( is_wp_error( $response ) ) {
				$data = $response->get_error_data();

				// If the error response is a 404, they are not subscribed.
				if ( isset( $data['status'] ) && 404 == $data['status'] ) {
					return false;
				} else {
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->maybe_write_to_log(
						$response->get_error_code(),
						__( "Get Member Info", 'yikes-inc-easy-mailchimp-extender' ),
						"Checkbox Integrations Page"
					);

					// If there was some other error, let's just assume they're not subscribed
					return false;
				}
			}

			// Look at the status from the API
			$subscribed = 'subscribed' == $response['status'] ? true : false;

			return apply_filters( 'yikes-mailchimp-integration-is-user-subscribed', $subscribed, $type );
		}

		/**
		* 	Get the checkbox for the given integration and render it on the front end
		* 	@return string
		*	@since 6.0.0
		*/
		public function yikes_get_checkbox() {
			// enqueue our checkbox styles whenever the checkbox is displayed
			wp_enqueue_style( 'yikes-easy-mailchimp-checkbox-integration-styles', plugin_dir_url( __FILE__ ) . '../css/yikes-inc-easy-mailchimp-checkbox-integration.min.css' );
			// store our options
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );
			if( isset( $checkbox_options[$this->type]['associated-list'] ) && $checkbox_options[$this->type]['associated-list'] != '-' ) {
				$checked = ( $checkbox_options[$this->type]['precheck'] == 'true' ) ? 'checked' : '';
				// before checkbox HTML (comment, ...)
				$before = '<!-- Easy Forms for MailChimp - https://www.yikesplugins.com/ -->';
				$before .= apply_filters( 'yikes-mailchimp-before-checkbox-html', '' );
				// checkbox
				$content = '<p id="yikes-easy-mailchimp-' . $this->type . '-checkbox" class="yikes-easy-mailchimp-' . $this->type . '-checkbox">';
					$content .= '<label>';
						$content .= '<input type="checkbox" name="yikes_mailchimp_checkbox_' . $this->type . '" value="1" '. $checked . ' /> ';
						$content .= ( isset( $checkbox_options[$this->type]['label'] ) && trim( $checkbox_options[$this->type]['label'] ) != '' ) ? trim( $checkbox_options[$this->type]['label'] ) : __( 'Sign me up for your mailing list.', 'yikes-inc-easy-mailchimp-extender' );
					$content .= '</label>';
				$content .= '</p>';
				// after checkbox HTML (..., honeypot, closing comment)
				$after = apply_filters( 'yikes-mailchimp-after-checkbox-html', '' );
				$after .= '<!-- Easy Forms for MailChimp -->';
				return $before . $content . $after;
			}
		}

		/**
		 * Hook to submit the data to MailChimp when a new integration type is submitted.
		 *
		 * @since 6.0.0
		 *
		 * @param string $email      The email address.
		 * @param string $type       The integration type.
		 * @param array  $merge_vars The array of form data to send.
		 */
		public function subscribe_user_integration( $email, $type, $merge_vars ) {
			// get checkbox data
			$options = get_option( 'optin-checkbox-init', '' );

			// Make sure we have a list ID.
			if ( ! isset( $options[ $type ] ) || ! isset( $options[ $type ]['associated-list'] ) ) {
				// @todo: Throw some kind of error?
				return;
			}

			// Check for an IP address.
			$user_ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
			if ( isset( $merge_vars['OPTIN_IP'] ) ) {
				$user_ip = sanitize_text_field( $merge_vars['OPTIN_IP'] );
			}

			// Build our data
			$list_id   = $options[ $type ]['associated-list'];
			$interests = isset( $options[ $type ]['interest-groups'] ) ? $options[ $type ]['interest-groups'] : array();
			$id        = md5( $email );
			$data      = array(
				'email_address'    => sanitize_email( $email ),
				'merge_fields'     => apply_filters( 'yikes-mailchimp-checkbox-integration-merge-variables', $merge_vars, $type ),
				'status_if_new'    => 'pending',
				'timestamp_signup' => (string) current_time( 'timestamp', true ),
				'ip_signup'        => $user_ip,
			);

			// Only re-format and add interest groups if not empty
			if ( ! empty( $interests ) ) {

				$groups = array();

				// Need to reformat interest groups array as $interest_group_ID => true
				foreach( $interests as $interest ) {
					if ( is_array( $interest ) ) {
						foreach( $interest as $group_id ) {
							$groups[ $group_id ] = true;
						}
					}
				}

				$data['interests'] = $groups;
			}

			/**
			*	'yikes-mailchimp-checkbox-integration-body'
			*
			*	Filter the request body for a MailChimp subscription via the checkbox integrations
			*
			*	@param array  | $data | The request body
			*	@param string | $type | The integration type, e.g. 'contact_form_7'
			*/
			$data = apply_filters( 'yikes-mailchimp-checkbox-integration-body', $data, $type );

			// Subscribe the user to the list via the API.
			$response = yikes_get_mc_api_manager()->get_list_handler()->member_subscribe( $list_id, $id, $data );
			if ( is_wp_error( $response ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log(
					$response->get_error_code(),
					__( "Checkbox Integration Subscribe User", 'yikes-inc-easy-mailchimp-extender' ),
					"Checkbox Integrations"
				);
			}

			return;
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
							$tmp['registered'][$index] = $old . ' <p class="message"><strong>' . __( 'Note' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong>: ' . $email_error . '</p>';
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

	}
?>
