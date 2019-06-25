<?php
/**
 * Handle Contact Form 7 Integration: create a [yikes_mailchimp_checkbox] shortcode for use in CF7 form building.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle Contact Form 7 Integration.
 */
class Yikes_Easy_MC_CF7_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'contact_form_7';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
		add_action( 'wpcf7_mail_sent', array( $this, 'new_cf7_subscription' ) );
		add_action( 'wpcf7_posted_data', array( $this, 'alter_cf7_data' ) );
	}

	/**
	 * Registers the CF7 shortcode.
	 *
	 * @return boolean True if we were able to register the shortcode.
	 */
	public function init() {
		if ( ! function_exists( 'wpcf7_add_form_tag' ) ) {
			return false;
		}
		return wpcf7_add_form_tag( 'yikes_mailchimp_checkbox', array( $this, 'yikes_get_checkbox' ) );
	}

	/**
	 * Add yikes_mailchimp_checkbox to post data as "Yes" or "No."
	 *
	 * @param  array $data CF7 posted data.
	 * @return array $data CF7 posted data.
	 */
	public function alter_cf7_data( $data = array() ) {
		$data['yikes_mailchimp_checkbox'] = $this->was_checkbox_checked( $this->type ) ? __( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) : __( 'No', 'yikes-inc-easy-mailchimp-extender' );
		return $data;
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param Object $contact_form The CF7 object.
	 */
	public function new_cf7_subscription( $contact_form ) {
		if ( false === $this->was_checkbox_checked( $this->type ) ) {
			return false;
		}
		$integration_options = get_option( 'optin-checkbox-init', '' );
		$submission          = WPCF7_Submission::get_instance();
		if ( $submission ) {
			$data      = $submission->get_posted_data();
			$email     = isset( $data['your-email'] ) ? $data['your-email'] : '';
			$fields    = array( 'email' => $email );
			$addl_vars = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'cf7_data' => $data, 'contact_form' => $contact_form ), $this->type );
			$this->subscribe_user_integration( $email, $this->type, apply_filters( 'yikes-mailchimp-contact-form-7', $fields, $data ), $addl_vars );
		}
	}
}
$yikes_easy_mc_cf7_checkbox_class = new Yikes_Easy_MC_CF7_Checkbox_Class();
