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
			
		/*
		*	Check if a user is already subscribed to
		*	a given list, if so don't show the checkbox integration
		*	@since 6.0.0
		*	@$integration_type - pass in the type of checkbox integration
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
		*	@since 6.0.0
		*	@$email - users email address entered into the form
		*	@$integration_type - pass in the type of checkbox integration
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
				$before = '<!-- Easy Forms for MailChimp by YIKES, Inc. - https://www.yikesinc.com/ -->';
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
				$after .= '<!-- Easy Forms for MailChimp by YIKES, Inc. -->';
				return $before . $content . $after;
			}
		}	
	
		/**
		 *	Hook to submit the data to MailChimp when 
		 *	a new integration type is submitted
		 *
		 *	@since 6.0.0
		**/
		public function subscribe_user_integration( $email, $type, $merge_vars ) {			
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
			// set the optin time
			$merge_vars['OPTIN_TIME'] = current_time( 'Y-m-d H:i:s', 1 );
			// check for interest groups
			$interest_groups = ( isset( $checkbox_options[$type]['interest-groups'] ) ) ? $checkbox_options[$type]['interest-groups'] : false;
			// if interest groups were found, push them to the merge variable array
			if( $interest_groups ) {
				$merge_vars['groupings'] = array();
				foreach( $interest_groups as $interest_group_id => $interest_group_selections ) {
					// merge variable interest groups array
					$merge_vars['groupings'][] = array(
						'id' => $interest_group_id,
						'groups' => $interest_group_selections,
					); 	
				}
				// replace the interest groups - to avoid any errors thrown if the admin switches lists, or interest groups
				$merge_vars['replace_interests'] = 1;
			}
			// initialize MailChimp API
			try {
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// subscribe the user
				$subscribe_response = $MailChimp->call( '/lists/subscribe', apply_filters( 'yikes-mailchimp-checkbox-integration-subscibe-api-request', array( 
					'api_key' => get_option( 'yikes-mc-api-key' , '' ),
					'id' => $checkbox_options[$type]['associated-list'],
					'email' => array( 'email' => sanitize_email( $email ) ),
					'merge_vars' => apply_filters( 'yikes-mailchimp-checkbox-integration-merge-variables', $merge_vars, $type ), // filter merge variables
					'double_optin' => 1,
					'update_existing' => $update,
					'send_welcome' => 1
				), $type ) );
			} catch( Exception $e ) { 
				$e->getMessage();
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