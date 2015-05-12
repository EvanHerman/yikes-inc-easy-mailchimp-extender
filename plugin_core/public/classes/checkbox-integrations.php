<?php
	/*
	* Takes care of all the sign-up checkboxes
	*
	*/
	class Yikes_Easy_MC_Checkbox_Integration_Class {
	
		// declare our integration type
		protected $type = 'integration';
		
		public function __construct() {	
			
		}
			
		/**
		* @param mixed $args Array or string
		* @return string
		*/
		public function yikes_get_checkbox() {
			// store our options
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );
			if( isset( $checkbox_options['comment_form']['associated-list'] ) && $checkbox_options['comment_form']['associated-list'] != '-' ) {
				$checked = ( $checkbox_options['comment_form']['precheck'] == 'true' ) ? 'checked' : '';
				// before checkbox HTML (comment, ...)
				$before = '<!-- Easy MailChimp Forms by Yikes Inc - https://www.yikesinc.com/ -->';
				$before .= apply_filters( 'yikes-mailchimp-before-checkbox-html', '' );
				// checkbox
				$content = '<p id="yikes-easy-mailchimp-comment-checkbox" class="yikes-easy-mailchimp-comment-checkbox">';
					$content .= '<label>';
						$content .= '<input type="checkbox" name="yikes_mailchimp_checkbox_' . $this->type . '" value="1" '. $checked . ' /> ';
						$content .= $checkbox_options['comment_form']['label'];
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
					'id' => $checkbox_options['comment_form']['associated-list'],
					'email' => array( 'email' => sanitize_email( $email) ),
					'merge_vars' => $merge_vars,
					'double_optin' => 0,
					'update_existing' => 1,
					'send_welcome' => 1
				) );
			} catch( Exception $e ) { 
				return $e->getMessage();
			}
		}
		
		/**
		* Build merge varaibles array
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
		*/
		public function was_checkbox_checked( $type ) {
			// was sign-up checkbox checked - return the value
			return ( isset( $_POST[ 'yikes_mailchimp_checkbox_'.$type ] ) && $_POST[ 'yikes_mailchimp_checkbox_'.$type ] == 1 );
		}
		
	}
?>