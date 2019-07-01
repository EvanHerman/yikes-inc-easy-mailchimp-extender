<?php
/**
 * Handle WordPress Registration Integration: add an opt-in checkbox to WordPress' native registration form.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle WordPress Registration Integration.
 */
class Yikes_Easy_MC_Registration_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'registration_form';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'register_form', array( $this, 'output_checkbox' ), 20 );
		add_action( 'user_register', array( $this, 'subscribe_from_registration' ), 90, 1 );
	}

	/**
	 * Outputs the subscribe checkbox.
	 */
	public function output_checkbox() {
		echo $this->yikes_get_checkbox();
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int $user_id The WP User's ID.
	 */
	public function subscribe_from_registration( $user_id ) {
		if ( false === $this->was_checkbox_checked( $this->type ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( false === $user ) {
			return false;
		}

		// Fetch the user's data.
		$merge_variables = $this->user_merge_vars( $user );
		$addl_vars       = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'user' => $user ), $this->type );

		if ( false === $this->is_user_already_subscribed( $this->type, $user->user_email ) ) {
			$this->subscribe_user_integration( $user->user_email, $this->type, $merge_variables, $addl_vars );
		}
	}
}
$yikes_easy_mc_registration_checkbox_class = new Yikes_Easy_MC_Registration_Checkbox_Class();
