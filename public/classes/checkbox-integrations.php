<?php
/**
 * Takes care of all the integration checkboxes.
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( esc_html_e( "Whoops, you shouldn't be accessing this file directly. Abort!", 'yikes-inc-easy-mailchimp-extender' ) );

/**
 * Main Checkbox Integration class.
 */
class Yikes_Easy_MC_Checkbox_Integration_Class {

	/**
	 * The integration type.
	 *
	 * @var string $type
	 */
	protected $type = '';

	/**
	 * Determine whether the current user is subscribed to all of the lists.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $type  The integration type to check.
	 * @param string $email The email address to check.
	 *
	 * @return bool Whether the current user is subscribed to a list.
	 */
	public function is_user_already_subscribed( $type, $email = '' ) {
		// Make sure we have an email address to use.
		if ( empty( $email ) ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$current_user = wp_get_current_user();
			$email        = $current_user->user_email;
		}

		// Ensure we have a valid email.
		if ( ! is_email( $email ) ) {
			return false;
		}

		// Convert the integration type to a list ID.
		$checkbox_options = get_option( 'optin-checkbox-init', '' );
		if ( empty( $checkbox_options ) || ! isset( $checkbox_options[ $type ] ) || ! isset( $checkbox_options[ $type ]['associated-list'] ) ) {
			return false;
		}

		$list_ids = $checkbox_options[ $type ]['associated-list'];
		$list_ids = is_array( $list_ids ) ? $list_ids : array( $list_ids );

		// Go through each list...
		foreach ( $list_ids as $list_id ) {
			if ( ! $this->is_user_subscribed( $email, $list_id, $type ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Determine whether a given email is subscribed to a given list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $email   The email address to check.
	 * @param string $list_id The list ID to check.
	 * @param string $type    The integration type.
	 *
	 * @return bool Whether the email is subscribed to the list.
	 */
	public function is_user_subscribed( $email, $list_id, $type ) {
		$email      = sanitize_email( $email );
		$email_hash = md5( $email );

		// Check the API to see the status.
		$response = yikes_get_mc_api_manager()->get_list_handler()->get_member( $list_id, $email_hash, false );
		if ( is_wp_error( $response ) ) {
			$data = $response->get_error_data();

			// If the error response is a 404, they are not subscribed.
			if ( isset( $data['status'] ) && 404 === (int) $data['status'] ) {
				return false;
			} else {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log(
					$response->get_error_code(),
					__( 'Get Member Info', 'yikes-inc-easy-mailchimp-extender' ),
					'Checkbox Integrations Page'
				);

				// If there was some other error, let's just assume they're not subscribed.
				return false;
			}
		}

		// Look at the status from the API.
		$subscribed = 'subscribed' === $response['status'];

		return apply_filters( 'yikes-mailchimp-integration-is-user-subscribed', $subscribed, $type );
	}

	/**
	 * Render the checkbox.
	 *
	 * @return string The HTML for the checkbox.
	 */
	public function yikes_get_checkbox() {

		// Get our options.
		$checkbox_options = get_option( 'optin-checkbox-init', array() );
		$has_list_ids     = isset( $checkbox_options[ $this->type ]['associated-list'] ) && '-' !== $checkbox_options[ $this->type ]['associated-list'] && is_array( $checkbox_options[ $this->type ]['associated-list'] );
		$has_list_ids     = $has_list_ids && ! in_array( '-', $checkbox_options[ $this->type ]['associated-list'], true );

		if ( $has_list_ids ) {
			$label    = isset( $checkbox_options[ $this->type ]['label'] ) && ! empty( $checkbox_options[ $this->type ]['label'] ) ? trim( $checkbox_options[ $this->type ]['label'] ) : __( 'Sign me up for your mailing list.', 'yikes-inc-easy-mailchimp-extender' );
			$checked  = 'true' === $checkbox_options[ $this->type ]['precheck'] ? 'checked="checked"' : '';
			$before   = apply_filters( 'yikes-mailchimp-before-checkbox-html', '' );
			$content  = '<p id="yikes-easy-mailchimp-' . esc_attr( $this->type ) . '-checkbox" class="yikes-easy-mailchimp-' . esc_attr( $this->type ) . '-checkbox">';
			$content .= '<label>';
			$content .= '<input type="checkbox" name="yikes_mailchimp_checkbox_' . esc_attr( $this->type ) . '" value="1" ' . $checked . '/>';
			$content .= apply_filters( 'yikes_mailchimp_checkbox_integration_checkbox_label', $label, $this->type, $checkbox_options );
			$content .= '</label>';
			$content .= '</p>';
			$content  = apply_filters( 'yikes_mailchimp_checkbox_integration_checkbox_html', $content, $this->type, $checkbox_options );
			$after    = apply_filters( 'yikes-mailchimp-after-checkbox-html', '' );
			$after   .= '<!-- Easy Forms for Mailchimp -->';
			$checkbox = $before . $content . $after;
			return apply_filters( 'yikes_mailchimp_checkbox_integration_html', $checkbox, $this->type, $checkbox_options );
		}
	}

	/**
	 * Hook to submit the data to Mailchimp when a new integration type is submitted.
	 *
	 * @since 6.0.0
	 *
	 * @param string $email            The email address.
	 * @param string $type             The integration type.
	 * @param array  $merge_vars       The array of form data to send.
	 * @param array  $integration_vars An array of additional information that can be used to filter the subscribe request.
	 */
	public function subscribe_user_integration( $email, $type, $merge_vars, $integration_vars = array() ) {
		$options = get_option( 'optin-checkbox-init', '' );

		// Make sure we have a list ID.
		if ( ! isset( $options[ $type ] ) || ! isset( $options[ $type ]['associated-list'] ) ) {
			// @todo: Throw some kind of error?
			return;
		}

		$email = sanitize_email( $email );

		// Check for an IP address.
		$user_ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
		if ( isset( $merge_vars['OPTIN_IP'] ) ) {
			$user_ip = sanitize_text_field( $merge_vars['OPTIN_IP'] );
		}

		// Build our request data.
		$list_ids = $options[ $type ]['associated-list'];
		$list_ids = is_array( $options[ $type ]['associated-list'] ) ? $options[ $type ]['associated-list'] : array( $options[ $type ]['associated-list'] );
		$id       = md5( $email );
		$data     = array(
			'email_address' => $email,
			'merge_fields'  => apply_filters( 'yikes-mailchimp-checkbox-integration-merge-variables', $merge_vars, $type, $integration_vars ),
			'status_if_new' => 'pending',
			'status'        => 'pending',
			'ip_signup'     => $user_ip,
		);

		foreach ( $list_ids as $list_id ) {

			$interests = isset( $options[ $type ]['interest-groups'] ) ? $options[ $type ]['interest-groups'] : array();
			$interests = isset( $interests[ $list_id ] ) ? $interests[ $list_id ] : $interests;

			// Only re-format and add interest groups if not empty.
			if ( ! empty( $interests ) ) {
				$groups = array();

				// Need to reformat interest groups array as $interest_group_ID => true.
				foreach ( $interests as $interest ) {
					if ( is_array( $interest ) ) {
						foreach ( $interest as $group_id ) {
							$groups[ $group_id ] = true;
						}
					}
				}

				$data['interests'] = $groups;
			}

			/**
			 * 'yikes-mailchimp-checkbox-integration-body'
			 *
			 * Filter the request body for a Mailchimp subscription via the checkbox integrations
			 *
			 * @param array  | $data    | The request body
			 * @param string | $type    | The integration type, e.g. 'contact_form_7'
			 * @param string | $list_id | The list ID
			 */
			$data = apply_filters( 'yikes-mailchimp-checkbox-integration-body', $data, $type, $list_id, $integration_vars );

			/**
			 * 'yikes-mailchimp-checkbox-integration-list-id'
			 *
			 * Filter the list ID for a Mailchimp subscription via the checkbox integrations
			 *
			 * @param string $list_id The list ID
			 * @param array  $data    The request body
			 * @param string $type    The integration type, e.g. 'contact_form_7'
			 */
			$list_id = apply_filters( 'yikes-mailchimp-checkbox-integration-list-id', $list_id, $data, $type, $integration_vars );

			// Don't send an empty merge fields array.
			if ( empty( $data['merge_fields'] ) ) {
				unset( $data['merge_fields'] );
			}

			// Subscribe the user to the list via the API.
			$response = yikes_get_mc_api_manager()->get_list_handler()->member_subscribe( $list_id, $id, $data );

			if ( is_wp_error( $response ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log(
					$response->get_error_code(),
					__( 'Checkbox Integration Subscribe User', 'yikes-inc-easy-mailchimp-extender' ),
					'Checkbox Integrations'
				);
			}
		}
	}

	/**
	 * Build merge varaibles array
	 *
	 * This is currently used in both the BuddyPress and WP Registration integrations.
	 *
	 * @param WP_User $user A WP User.
	 */
	public function user_merge_vars( WP_User $user ) {

		// Setup our array.
		$merge_vars = array();

		if ( ! empty( $user->first_name ) ) {
			$merge_vars['FNAME'] = $user->first_name;
		}
		if ( ! empty( $user->last_name ) ) {
			$merge_vars['LNAME'] = $user->last_name;
		}

		/**
		 * Filter the user variables passed to Mailchimp.
		 *
		 * @param array   $merge_vars Merge variables to pass to Mailchimp.
		 * @param WP_User $user       The WordPress user.
		 *
		 * @return array $merge_vars Merge variables to pass to Mailchimp.
		 */
		$merge_vars = apply_filters( 'yikes-mailchimp-user-merge-vars', $merge_vars, $user );

		return $merge_vars;
	}

	/**
	 * Confirm the checkbox was checked.
	 *
	 * @param string $type The integration type.
	 *
	 * @return bool True if the checkbox was checked.
	 */
	public function was_checkbox_checked( $type ) {
		return isset( $_POST[ 'yikes_mailchimp_checkbox_' . $type ] ) && '1' === filter_var( $_POST[ 'yikes_mailchimp_checkbox_' . $type ], FILTER_SANITIZE_STRING );
	}
}
