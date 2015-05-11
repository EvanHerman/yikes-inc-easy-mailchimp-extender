<?php
	/*
	* Takes care of all the sign-up checkboxes
	*
	*/
	class Yikes_Easy_MC_Checkbox_Integration_Class {
	
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
				$before .= apply_filters( 'yikes-mailchimp-before-checkbox', '' );
				// checkbox
				$content = '<p id="yikes-easy-mailchimp-comment-checkbox" class="yikes-easy-mailchimp-comment-checkbox">';
					$content .= '<label>';
						$content .= '<input type="checkbox" name="yikes-easy-mailchimp-comment-checkbox" value="1" '. $checked . ' /> ';
						$content .= $checkbox_options['comment_form']['label'];
					$content .= '</label>';
				$content .= '</p>';
				// after checkbox HTML (..., honeypot, closing comment)
				$after = apply_filters( 'yikes-mailchimp-after-checkbox', '' );
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
		public function subscribe_user_integration( $email , $type ) {
			// get checkbox data
			$checkbox_options = get_option( 'optin-checkbox-init' , '' );	
			// initialize MailChimp API
			try {	
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
					// subscribe the user
					$subscribe_response = $MailChimp->call('/lists/subscribe', array( 
					'api_key' => get_option( 'yikes-mc-api-key' , '' ),
					'id' => $checkbox_options['comment_form']['associated-list'],
					'email' => array( 'email' => sanitize_email( $email) ),
					'merge_vars' => array(),
					'double_optin' => 0,
					'update_existing' => 1,
					'send_welcome' => 1
				) );
			} catch( Exception $e ) { 
				return $e->getMessage();
			}
		}
		
	}
?>