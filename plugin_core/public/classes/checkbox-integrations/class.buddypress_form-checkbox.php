<?php 
	
	/* 
	*	Main Class file to handle Buddy Press Integrations
	*	used to add new users signing up via buddy press 
	*	@since 6.0.0
	*/
	class Yikes_Easy_MC_BuddyPress_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
		
		protected $type = 'buddypress_form';
		
		public function __construct() {
			add_action( 'bp_before_registration_submit_buttons', array( $this, 'output_checkbox' ), 20 );
			add_action( 'bp_core_signup_user', array( $this, 'subscribe_from_buddypress_form' ), 10, 4 );
		}
		
		/**
		* Outputs a checkbox
		*/
		public function output_checkbox() {
			echo do_action( 'yikes-mailchimp-before-checkbox' , $this->type );
				echo $this->yikes_get_checkbox();
			echo do_action( 'yikes-mailchimp-after-checkbox' , $this->type );
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
			return $this->subscribe( $email, $merge_vars, 'buddypress_registration', $user_id );
		}
		/* End BuddyPress functions */
	
	}
	new Yikes_Easy_MC_BuddyPress_Checkbox_Class;
	
?>