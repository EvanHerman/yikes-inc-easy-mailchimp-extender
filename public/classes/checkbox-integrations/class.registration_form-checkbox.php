<?php

/*
*	Main class file for the integration with core WordPress Registration form
* 	Takes care of opt-in checkboxes to integrate with Registration forms on your site
*	
*	since @6.0.0
*/

// Prevent direct access to the file
defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );
	
class Yikes_Easy_MC_Registration_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	// declare our integration type
	protected $type = 'registration_form';
	
	public function __construct() {
		parent::__construct();
		add_action( 'register_form', array( $this, 'output_checkbox' ), 20 );
		add_action( 'user_register', array( $this, 'subscribe_from_registration' ), 90, 1 );
		// alter the error message, if there was an error with the users email address
		add_filter( 'wp_login_errors', array( $this , 'yikes_reg_complete_msg' ), 10,  2 );
	}

	/**
	* Outputs a checkbox
	*/
	public function output_checkbox() {
		echo $this->yikes_get_checkbox();
	}
				
	/**
	 * Subscribes from WP Registration Form
	 *
	 * @param int $user_id
	 *
	 * @return bool|string
	*/
	public function subscribe_from_registration( $user_id ) {
		// was sign-up checkbox checked?
		if ( $this->was_checkbox_checked( $this->type ) === false ) {
			return false;
		}
		// gather emailadress from user who WordPress registered
		$user = get_userdata( $user_id );
		// was a user found with the given ID?
		if ( ! $user ) {
			return false;
		}
		// build our merge vars
		$merge_variables = $this->user_merge_vars( $user );
		// only subscribe the user if they aren't already on the list
		if ( $this->is_user_already_subscribed( 'registration_form', $user->user_email ) === false ) {
			$this->subscribe_user_integration( sanitize_email( $user->user_email ) , $this->type , $merge_variables );
		} else {
			// add a temporary option to pass our email address and let the user know they are already subscribed
			$user_already_subscribed_message = sprintf( __( "You have not been subscribed to our mailing list. %s is already subscribed to this list.", "yikes-inc-easy-mailchimp-extender" ), $user->user_email  );
			add_option( 'yikes_register_subscription_error' , $user_already_subscribed_message );
		}
	}
	
	/* End registration form functions */
}
new Yikes_Easy_MC_Registration_Checkbox_Class;
