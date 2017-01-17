<?php 
	/* 
	*	Main Class file to handle BuddyPress Integrations
	*	used to add new users signing up via Buddy Press 
	*	@since 6.0.0
	*/
	
	// Prevent direct access to the file
	defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );
	
	class Yikes_Easy_MC_BuddyPress_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
		
		protected $type = 'buddypress_form';
		
		public function __construct() {
			add_action( 'bp_before_registration_submit_buttons', array( $this, 'output_checkbox' ), 20 );
			add_action( 'bp_core_signup_user', array( $this, 'subscribe_from_buddypress_form' ), 10, 4 );
		}
		
		/**
		* Outputs a checkbox, if user is not already subscribed
		*/
		public function output_checkbox() {
			if ( $this->is_user_already_subscribed( $this->type ) ) {
				return;
			}
			?>
				<div id="yikes-mailchimp-checkbox-section" class="register-section yikes-mailchimp-bbpress-optin">  
					<?php
						echo $this->yikes_get_checkbox();
					?>
				</div>
			<?php
		}
		
		
		/**
		 * Subscribes from BuddyPress Registration Form
		 * @param int $user_id
		 * @param string $user_login
		 * @param string $user_password
		 * @param string $user_email
		 * @param array $usermeta
		 */
		public function subscribe_from_buddypress_form( $user_id, $user_login, $user_password, $user_email ) {
			if ( $this->was_checkbox_checked( $this->type ) === false ) {
				return false;
			}
			$user = get_userdata( $user_id );
			// was a user found with the given ID?
			if ( ! $user ) {
				return false;
			}
			// gather emailadress and name from user who BuddyPress registered
			$email = $user->user_email;
			$merge_vars = $this->user_merge_vars( $user );
			return $this->subscribe_user_integration( $email, $this->type, $merge_vars );
		}
		/* End BuddyPress functions */
	
	}
	new Yikes_Easy_MC_BuddyPress_Checkbox_Class;
