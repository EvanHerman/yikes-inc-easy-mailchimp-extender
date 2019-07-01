<?php
/**
 * Handle WooCommerce Integration: add a checkbox for subscribers on WooCommerce's checkout page.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * WooCo Checkbox Integration.
 */
class Yikes_Easy_MC_WooCommerce_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'woocommerce_checkout_form';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'determine_checkbox_placement' ), 1000 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_woocommerce_checkout_checkbox_value' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'subscribe_from_woocommerce_checkout' ) );
	}

	/**
	 * Filter where the WooCo checkbox will go.
	 */
	public function determine_checkbox_placement() {

		$default_checkbox_placement = apply_filters( 'yikes-mailchimp-wooco-integration-checkbox-checkout-fields', true );

		if ( $default_checkbox_placement ) {
			add_filter( 'woocommerce_checkout_fields', array( $this, 'add_checkout_field' ), 20 );
		} else {
			$checkbox_location = apply_filters( 'yikes_mailchimp_wooco_integration_placement_filter', 'woocommerce_review_order_before_submit' );
			add_action( $checkbox_location, array( $this, 'output_checkbox' ) );
		}
	}

	/**
	 * Print the checkbox to the page.
	 */
	public function output_checkbox() {
		if ( $this->is_user_already_subscribed( $this->type ) ) {
			return;
		}
		echo $this->yikes_get_checkbox();
	}

	/**
	 * Add the checkbox to WooCommerce's checkout fields array.
	 *
	 * @param  array $fields WooCommerce's array of checkout fields.
	 * @return array $fields WooCommerce's array of checkout fields with our checkbox appended.
	 */
	public function add_checkout_field( $fields ) {

		// Get checkbox data.
		$checkbox_options = get_option( 'optin-checkbox-init', array() );

		// Only display the field if a list is set.
		if ( isset( $checkbox_options[ $this->type ] ) && isset( $checkbox_options[ $this->type ]['associated-list'] ) && '-' !== $checkbox_options[ $this->type ]['associated-list'] ) {

			if ( $this->is_user_already_subscribed( $this->type ) ) {
				return $fields;
			}

			$precheck = isset( $checkbox_options[ $this->type ]['precheck'] ) && 'true' === $checkbox_options[ $this->type ]['precheck'] ? '1' : '0';

			/**
			* Filter where the checkbox goes.
			*
			* See this WooCo article for possible values: https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
			*
			* @param string | Which set of fields the checkbox should go into
			*/
			$field_placement = apply_filters( 'yikes-mailchimp-wooco-integration-checkbox-placement', 'billing' );

			$yikes_checkbox = array(
				'id'      => 'yikes_mailchimp_checkbox_' . $this->type,
				'type'    => 'checkbox',
				'class'   => apply_filters( 'yikes-mailchimp-wooco-integration-checkbox-classes', array( 'form-row-wide' ) ),
				'label'   => $checkbox_options[ $this->type ]['label'],
				'default' => $precheck,
			);

			/**
			* Filter the checkbox data.
			*
			* See this WooCo article for possible values: https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
			*
			* @param  array $yikes_checkbox The checkbox's fields.
			* @return array $yikes_checkbox The checkbox's fields.
			*/
			$yikes_checkbox = apply_filters( 'yikes_mailchimp_wooco_integration_checkbox_field', $yikes_checkbox, $checkbox_options[ $this->type ] );

			$fields[ $field_placement ][ 'yikes_mailchimp_checkbox_' . $this->type ] = $yikes_checkbox;
		}

		return $fields;
	}

	/**
	 * Save the checkbox's status as post meta to the order.
	 *
	 * This allows us to run the subscription request after the order has been processed.
	 *
	 * @param int $order_id The WooCo order ID.
	 */
	public function save_woocommerce_checkout_checkbox_value( $order_id ) {
		update_post_meta( $order_id, 'yikes_easy_mailchimp_optin', $this->was_checkbox_checked( $this->type ) );
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int $order_id The WooCo Order ID.
	 */
	public function subscribe_from_woocommerce_checkout( $order_id ) {
		$do_optin = get_post_meta( $order_id, 'yikes_easy_mailchimp_optin', true );

		if ( '1' === $do_optin ) {
			$order      = new WC_Order( $order_id );
			$email      = $order->get_billing_email();
			$merge_vars = array(
				'FNAME' => $order->get_billing_first_name(),
				'LNAME' => $order->get_billing_last_name(),
			);

			$integration_vars = array(
				'order' => $order,
			);

			// Subscribe the user.
			$this->subscribe_user_integration( $email, $this->type, $merge_vars, $integration_vars );
		}
	}
}

$yikes_easy_mc_woocommerce_checkbox_class = new Yikes_Easy_MC_WooCommerce_Checkbox_Class();
