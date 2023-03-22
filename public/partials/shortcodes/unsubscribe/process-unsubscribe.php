<?php

class YIKES_Mailchimp_Process_Unsubscribe {

	public function __construct() {
		$this->define_unsubscribe_ajax();
	}

	public function define_unsubscribe_ajax() {
		add_action( 'wp_ajax_nopriv_yikes_mailchimp_unsubscribe', array( $this, 'yikes_mailchimp_unsubscribe' ) );
		add_action( 'wp_ajax_yikes_mailchimp_unsubscribe', array( $this, 'yikes_mailchimp_unsubscribe' ) );
	}

	public function yikes_mailchimp_unsubscribe() {

		// Verify nonce:
		// First, check our option - this is set in the general settings page
		if ( get_option( 'yikes-mailchimp-use-nonce' ) === '1' ) {
			if ( check_ajax_referer( 'yikes-mailchimp-unsubscribe-nonce', 'nonce', false ) ) {
				wp_send_json_error( '1' );
			}
		}

		// Verify Honeypot
		if ( ! empty( $_POST['hp'] ) ) {
			wp_send_json_error( '2' );
		}

		// Get email, list ID
		$email   = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';

		if ( empty( $email ) || empty( $list_id ) ) {
			wp_send_json_error( '3' );
		}

		$email   = md5( strtolower( $email ) );
		$list_id = filter_var( $list_id, FILTER_SANITIZE_STRING );

		// Unsubscribe the member
		$list_handler  = yikes_get_mc_api_manager()->get_list_handler();
		$unsubscribe   = $list_handler->member_unsubscribe( $list_id, $email );

		// If error, log it.
		if ( is_wp_error( $unsubscribe ) && class_exists( 'Yikes_Inc_Easy_Mailchimp_Error_Logging' ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			if ( method_exists( $error_logging, 'maybe_write_to_log' ) ) {
				$error_logging->maybe_write_to_log( $unsubscribe->get_error_code(), __( "Member Unsubscribe", 'yikes-inc-easy-mailchimp-extender' ), 'process-unsubscribe.php' );
			}

			wp_send_json_error( '4' );
		}

		wp_send_json_success();
	}
}

$YIKES_Mailchimp_Process_Unsubscribe = new YIKES_Mailchimp_Process_Unsubscribe();
