<?php
/**
 * Handle bbPress Integration: add a checkbox for subscribers when signing up via bbPress.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle bbPress Integration.
 */
class Yikes_Easy_MC_bbPress_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'bbpress_forms';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bbp_theme_after_topic_form_subscriptions', array( $this, 'output_checkbox' ), 10 );
		add_action( 'bbp_theme_after_reply_form_subscription', array( $this, 'output_checkbox' ), 10 );
		add_action( 'bbp_theme_anonymous_form_extras_bottom', array( $this, 'output_checkbox' ), 10 );
		add_action( 'bbp_new_topic', array( $this, 'subscribe_from_bbpress_new_topic' ), 10, 4 );
		add_action( 'bbp_new_reply', array( $this, 'subscribe_from_bbpress_new_reply' ), 10, 5 );
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
	 * Subscribe the user. At this point, they chose.
	 *
	 * @param int $user_id The WP User ID.
	 */
	public function subscribe_from_bbpress( $user_id ) {
		$user = get_userdata( $user_id );

		if ( false === $user ) {
			return false;
		}

		$email      = $user->user_email;
		$merge_vars = $this->user_merge_vars( $user );
		$addl_vars  = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'user' => $user ), $this->type );
		$this->subscribe_user_integration( $email, $this->type, $merge_vars, $addl_vars );
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int   $topic_id        The topic's ID.
	 * @param int   $forum_id        The forum's ID.
	 * @param mixed $anonymous_data  Honestly I don't know.
	 * @param int   $topic_author_id The topic author's ID (WP User ID).
	 */
	public function subscribe_from_bbpress_new_topic( $topic_id, $forum_id, $anonymous_data, $topic_author_id ) {
		if ( false == $this->was_checkbox_checked( $this->type ) ) {
			return;
		}

		$this->subscribe_from_bbpress( $topic_author_id );
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int   $reply_id        The reply's ID.
	 * @param int   $topic_id        The topic's ID.
	 * @param int   $forum_id        The forum's ID.
	 * @param mixed $anonymous_data  Honestly I don't know.
	 * @param int   $reply_author_id The topic author's ID (WP User ID).
	 */
	public function subscribe_from_bbpress_new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author_id ) {
		if ( false == $this->was_checkbox_checked( $this->type ) ) {
			return;
		}

		$this->subscribe_from_bbpress( $reply_author_id );
	}
}
$yikes_easy_mc_bbpress_checkbox_class = new Yikes_Easy_MC_bbPress_Checkbox_Class();
