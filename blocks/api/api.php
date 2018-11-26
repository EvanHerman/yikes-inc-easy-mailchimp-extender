<?php
/**
 * Class YIKES_Easy_Forms_Blocks_API.
 */
class YIKES_Easy_Forms_Blocks_API {

	/**
	 * Register our hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_yikes_get_forms', array( $this, 'get_forms' ) );
		add_action( 'wp_ajax_yikes_get_form', array( $this, 'get_form' ) );
		add_action( 'wp_ajax_yikes_get_recaptcha', array( $this, 'get_recaptcha' ) );
		add_action( 'wp_ajax_yikes_get_api_key_status', array( $this, 'get_api_key_status' ) );
	}

	/**
	 * Verify API Key.
	 */
	public function get_api_key_status() {

		// Verify Nonce.
		if ( ! check_ajax_referer( 'get_api_key_status', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		// Get our API key's status.
		$status = get_option( 'yikes-mc-api-validation', false );
		$status = empty( $status ) ? 'empty' : ( 'invalid_api_key' === $status ? 'invalid' : 'valid' );

		wp_send_json_success( $status );
	}

	/**
	 * Get all of our forms.
	 */
	public function get_forms() {

		// Verify Nonce.
		if ( ! check_ajax_referer( 'fetch_forms_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		// Get all of our forms.
		$form_interface = yikes_easy_mailchimp_extender_get_form_interface();
		$all_forms      = $form_interface->get_all_forms();

		wp_send_json_success( array_values( $all_forms ) );
	}

	/**
	 * Get a form's data.
	 */
	public function get_form() {

		// Verify Nonce.
		if ( ! check_ajax_referer( 'fetch_form_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		$form_id = isset( $_POST['form_id'] ) ? filter_var( wp_unslash( $_POST['form_id'] ), FILTER_SANITIZE_NUMBER_INT ) : '';

		if ( empty( $form_id ) ) {
			wp_send_json_error( '1' );
		}

		$form_interface = yikes_easy_mailchimp_extender_get_form_interface();

		$form = $form_interface->get_form( $form_id );

		wp_send_json_success( $form );
	}

	/**
	 * Get the reCAPTCHA variables.
	 */
	public function get_recaptcha() {

		// Verify Nonce.
		if ( ! check_ajax_referer( 'fetch_recaptcha_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		if ( get_option( 'yikes-mc-recaptcha-status', '' ) === '1' ) {

			$site_key   = get_option( 'yikes-mc-recaptcha-site-key', '' );
			$secret_key = get_option( 'yikes-mc-recaptcha-secret-key', '' );

			// If either of the Private the Secret key is left blank, we should display an error back to the user.
			if ( empty( $site_key ) || empty( $secret_key ) ) {
				wp_send_json_error();
			}

			$locale   = get_locale();
			$locale_a = explode( '_', $locale );
			$locale   = isset( $locale_a[0] ) ? $locale_a[0] : $locale;
			$return   = apply_filters( 'yikes_mailchimp_recaptcha_data', array(
				'site_key'   => $site_key,
				'secret_key' => $secret_key,
				'locale'     => $locale,
			));

			wp_send_json_success( $return );
		}

		wp_send_json_error();
	}
}
