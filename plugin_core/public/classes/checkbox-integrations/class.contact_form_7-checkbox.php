<?php

/*
*	Main class file for the integration with Contact Form 7
*
*	@description
*	enables the use of a [yikes_mailchimp_checkbox label="xyz"] shortcode
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
	protected $text_domain = 'yikes-inc-easy-mailchimp-extender';
	
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
		if( ! function_exists( 'wpcf7_add_shortcode' ) ) {
			return false;
		}
		wpcf7_add_shortcode( 'yikes_mailchimp_checkbox', array( $this, 'yikes_get_checkbox' ) );
		return true;
	}
	/**
	* Alter Contact Form 7 data.
	*
	* Adds mc4wp_checkbox to post data so users can use `mc4wp_checkbox` in their email templates
	*
	* @param array $data
	* @return array
	*/
	public function alter_cf7_data( $data = array() ) {
		$data['yikes_mailchimp_checkbox'] = $this->was_checkbox_checked( $this->type ) ? __( 'Yes', $this->text_domain ) : __( 'No', $this->text_domain );
		return $data;
	}
	/**
	* Subscribe from Contact Form 7 Forms
	*/
	public function new_cf7_subscription() {
		// was sign-up checkbox checked?
		if ( $this->was_checkbox_checked( $this->type ) === false ) {
			return false;
		}
		return $this->attempt_subscription();
	}
}
new Yikes_Easy_MC_CF7_Checkbox_Class;

?>