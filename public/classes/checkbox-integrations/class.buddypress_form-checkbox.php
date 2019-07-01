<?php
/**
 * Handle BuddyPress Integration: add a checkbox for subscribers when signing up via BuddyPress.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle BuddyPress Integration.
 */
class Yikes_Easy_MC_BuddyPress_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'buddypress_form';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bp_before_registration_submit_buttons', array( $this, 'output_checkbox' ), 20 );
		add_action( 'bp_core_signup_user', array( $this, 'subscribe_from_buddypress_form' ), 10, 4 );
	}

	/**
	 * Outputs a checkbox if user is not already subscribed.
	 */
	public function output_checkbox() {
		if ( $this->is_user_already_subscribed( $this->type ) ) {
			return;
		}
		echo $this->yikes_get_checkbox();
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int    $user_id       The user's ID.
	 * @param string $user_login    The user's login.
	 * @param string $user_password The user's password.
	 * @param string $user_email    The user's email.
	 */
	public function subscribe_from_buddypress_form( $user_id, $user_login, $user_password, $user_email ) {
		if ( false === $this->was_checkbox_checked( $this->type ) ) {
			return;
		}

		$user = get_userdata( $user_id );

		if ( false === $user ) {
			return false;
		}

		$email      = $user->user_email;
		$merge_vars = $this->user_merge_vars( $user );
		$addl_vars  = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'user' => $user ), $this->type );
		$this->subscribe_user_integration( $email, $this->type, $merge_vars, $addl_vars );
	}
}
$yikes_easy_mc_buddypress_checkbox_class = new Yikes_Easy_MC_BuddyPress_Checkbox_Class();
