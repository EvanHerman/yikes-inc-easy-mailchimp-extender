<?php
/**
 * Handle Comment Integration: add a checkbox to the comments area.
 *
 * @since 6.0.0
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Handle Comment Integration.
 */
class Yikes_Easy_MC_Comment_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = 'comment_form';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'comment_post', array( $this, 'subscribe_from_comment' ), 40, 3 );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Allows us to filter the filter that we're adding.
	 */
	public function init() {

		/**
		 * Decide the placement of the subscription checkbox. Default ('comment_form_field_comment') is after the "Comment" box.
		 *
		 * @return string The name of a WP comment field's filter
		 */
		$checkbox_placement = apply_filters( 'yikes-mailchimp-wp-comment-integration-placement', 'comment_form_field_comment' );

		add_filter( $checkbox_placement, array( $this, 'output_checkbox' ), 10, 1 );
	}

	/**
	 * Outputs a checkbox, if user is not already subscribed
	 *
	 * @param string $comment_field The content of the comment textarea field.
	 */
	public function output_checkbox( $comment_field ) {
		if ( $this->is_user_already_subscribed( $this->type ) ) {
			return $comment_field;
		}

		return $comment_field . $this->yikes_get_checkbox();
	}

	/**
	 * Subscribe the user if they so chose.
	 *
	 * @param int    $comment_id       The comment's ID.
	 * @param string $comment_approved The comment's status.
	 * @param array  $comment_data     The comment data.
	 */
	public function subscribe_from_comment( $comment_id, $comment_approved, $comment_data ) {
		if ( false === $this->was_checkbox_checked( $this->type ) ) {
			return false;
		}

		// Is this a spam comment?
		if ( 'spam' === $comment_approved ) {
			return false;
		}

		// Create merge variables based on comment data.
		$merge_vars = array(
			'FNAME'    => $comment_data['comment_author'],
			'OPTIN_IP' => $comment_data['comment_author_IP'],
		);

		$addl_vars = apply_filters( 'yikes_mailchimp_checkbox_integration_additional_vars', array( 'comment_data' => $comment_data ), $this->type );

		// Subscribe the user.
		$this->subscribe_user_integration( $comment_data['comment_author_email'], $this->type, $merge_vars, $addl_vars );
	}
}
$yikes_easy_mc_comment_checkbox_class = new Yikes_Easy_MC_Comment_Checkbox_Class();
