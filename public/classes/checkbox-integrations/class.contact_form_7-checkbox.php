<?php

/*
*	Main class file for the integration with Contact Form 7
*
*	@description
*	enables the use of a [yikes_mailchimp_checkbox] shortcode
*	for use in contact forms
*
*	@since 6.0.0
*/
	
// Prevent direct access to the file
defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );

class Yikes_Easy_MC_CF7_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
	/**
	 * @var string
	 */
	protected $type = 'contact_form_7';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// initialize
		$this->init();
		add_action( 'wpcf7_mail_sent', array( $this, 'new_cf7_subscription' ) );
		add_action( 'wpcf7_posted_data', array( $this, 'alter_cf7_data') );
	}
	
	/**
	* Registers the CF7 shortcode
	 *
	* @return boolean
	*/
	public function init() {
		if( ! function_exists( 'wpcf7_add_form_tag' ) ) {
			return false;
		}
		wpcf7_add_form_tag( 'yikes_mailchimp_checkbox', array( $this, 'yikes_get_checkbox' ) );
		return true;
	}
	
	/**
	* Alter Contact Form 7 data.
	*
	* Adds yikes_mailchimp_checkbox to post data so users can use `yikes_mailchimp_checkbox` in their email templates
	*
	* @param array $data
	* @return array
	*/
	public function alter_cf7_data( $data = array() ) {
		$data['yikes_mailchimp_checkbox'] = $this->was_checkbox_checked( $this->type ) ? __( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) : __( 'No', 'yikes-inc-easy-mailchimp-extender' );
		return $data;
	}
	
	/**
	* Subscribe from Contact Form 7 Forms
	*/
	public function new_cf7_subscription( $contact_form ) {
		// was sign-up checkbox checked?
		if ( $this->was_checkbox_checked( $this->type ) === false ) {
			return false;
		}
		// get the integration options
		$integration_options = get_option( 'optin-checkbox-init' , '' );
		// get the contact form 7 submission instance
		$submission = WPCF7_Submission::get_instance();
		// confirm the submission was received
		if ( $submission ) {
			// get the submission data
			$posted_data = $submission->get_posted_data();
			// store the email -- this needs to be more dynamic (find string with containing string email?)
			$email = ( isset( $posted_data['your-email'] ) ) ? $posted_data['your-email'] : '';
			// Default the merge_values
			$merge_values = array( 'email' => $email );
			// submit this subscriber
			return $this->subscribe_user_integration( $email, $this->type, apply_filters( 'yikes-mailchimp-contact-form-7', $merge_values, $posted_data ) );
		}
	}
	
}
new Yikes_Easy_MC_CF7_Checkbox_Class;

?>