<?php


class Yikes_Easy_MC_Registration_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	// declare our integration type
	protected $type = 'registration_form';
	
	public function __construct() {
		parent::__construct();
		add_action( 'register_form', array( $this, 'output_checkbox' ), 20 );
		add_action( 'user_register', array( $this, 'subscribe_from_registration' ), 90, 1 );
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
		try{
			$this->subscribe_user_integration( sanitize_email( $user->user_email ) , $this->type , $merge_variables );
		} catch( Exception $e ) {
			return $e->getMessage();
		}
	}
	
	/* End registration form functions */
}
new Yikes_Easy_MC_Registration_Checkbox_Class;

?>