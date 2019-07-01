<?php
/**
 * Handle Easy Digital Downloads Integration: add an opt-in checkbox to the EDD checkout page.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle Easy Digital Downloads Integration.
 */
class Yikes_Easy_MC_EDD_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'easy_digital_downloads_checkout_form';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'output_checkbox' ) );
		add_action( 'edd_insert_payment', array( $this, 'update_payment_post_meta' ), 99999 );
		add_action( 'edd_complete_purchase', array( $this, 'subscribe_from_edd_purchase' ), 50 );
	}

	/**
	 * Outputs the subscribe checkbox.
	 */
	public function output_checkbox() {
		if ( $this->is_user_already_subscribed( $this->type ) ) {
			return;
		}
		echo $this->yikes_get_checkbox();
	}


	/**
	 * Add the checkbox's checked value as post meta to the order.
	 *
	 * @param  int   $payment_id   The payment's ID.
	 * @param  array $payment_data Array of payment data.
	 */
	public function update_payment_post_meta( $payment_id = 0, $payment_data = array() ) {
		if ( ! $this->was_checkbox_checked( $this->type ) ) {
			return;
		}
		update_post_meta( $payment_id, '_yikes_easy_mc_optin', '1' );
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int $payment_id The ID of the payment.
	 */
	public function subscribe_from_edd_purchase( $payment_id ) {
		$meta = get_post_meta( $payment_id, '_yikes_easy_mc_optin', true );
		if ( empty( $meta ) ) {
			return;
		}

		if ( ! function_exists( 'edd_get_payment_user_email' ) ) {
			return;
		}

		$email = (string) edd_get_payment_user_email( $payment_id );

		if ( empty( $email ) ) {
			return;
		}

		if ( ! function_exists( 'edd_get_payment_meta_user_info' ) ) {
			return false;
		}

		$user_info  = (array) edd_get_payment_meta_user_info( $payment_id );
		$merge_vars = array();
		if ( isset( $user_info['first_name'] ) ) {
			$merge_vars['FNAME'] = $user_info['first_name'];
		}
		if ( isset( $user_info['last_name'] ) ) {
			$merge_vars['LNAME'] = $user_info['last_name'];
		}

		$addl_vars = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'user' => $user_info, 'payment_id' => $payment_id ), $this->type );

		// Subscribe the user.
		$this->subscribe_user_integration( $email, $this->type, $merge_vars, $addl_vars );
	}

}
$yikes_easy_mc_edd_checkbox_class = new Yikes_Easy_MC_EDD_Checkbox_Class();
