<?php

/**
 * Abstract form interface class.
 *
 * This implements a few of the interface methods that should be common between
 * all ancestors of this class.
 */
abstract class Yikes_Inc_Easy_MailChimp_Extender_Forms_Abstract implements Yikes_Inc_Easy_MailChimp_Extender_Form_Interface {

	/**
	 * Get the default values for a form.
	 *
	 * @author Jeremy Pry
	 * @return array Array of default form data.
	 */
	public function get_form_defaults() {
		return array(
			'id'                      => 0,
			'list_id'                 => '',
			'form_name'               => '',
			'form_description'        => '',
			'fields'                  => array(),
			'custom_styles'           => '',
			'custom_template'         => '',
			'redirect_user_on_submit' => 0,
			'redirect_page'           => '',
			'submission_settings'     => array(
				'ajax'                   => 1,
				'redirect_on_submission' => 0,
				'redirect_page'          => 1,
				'hide_form_post_signup'  => 0,
			),
			'optin_settings'          => array(
				'optin'                => 1,
				'update_existing_user' => 1,
				'send_update_email'    => 1,
			),
			'form_settings'           => array(
				'yikes-easy-mc-form-class-names'                 => '',
				'yikes-easy-mc-inline-form'                      => 0,
				'yikes-easy-mc-submit-button-type'               => 'text',
				'yikes-easy-mc-submit-button-text'               => __( 'Submit', 'yikes-inc-easy-mailchimp-extender' ),
				'yikes-easy-mc-submit-button-image'              => '',
				'yikes-easy-mc-submit-button-classes'            => '',
				'yikes-easy-mc-form-schedule'                    => 0,
				'yikes-easy-mc-form-restriction-start'           => 0,
				'yikes-easy-mc-form-restriction-end'             => 0,
				'yikes-easy-mc-form-restriction-pending-message' => sprintf( __( 'Signup is not yet open, and will be available on %s. Please come back then to signup.', 'yikes-inc-easy-mailchimp-extender' ), current_time( str_replace( '-', '/', get_option( 'date_format' ) ) ) . ' ' . __( 'at', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . current_time( 'g:iA' ) ),
				'yikes-easy-mc-form-restriction-expired-message' => sprintf( __( 'This signup for this form ended on %s.', 'yikes-inc-easy-mailchimp-extender' ), date( str_replace( '-', '/', get_option( 'date_format' ) ), strtotime( current_time( str_replace( '-', '/', get_option( 'date_format' ) ) ) ) + ( 3600 * 24 ) ) . ' ' . __( 'at', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . date( 'g:iA', strtotime( current_time( 'g:iA' ) ) + ( 3600 * 24 ) ) ),
				'yikes-easy-mc-form-login-required'              => 0,
				'yikes-easy-mc-form-restriction-login-message'   => __( 'You need to be logged in to sign up for this mailing list.', 'yikes-inc-easy-mailchimp-extender' ),
			),
			'error_messages'			=> array(
				'success'				=> '',
				'success-single-optin'	=> '',
				'success-resubscribed'	=> '',
				'general-error'			=> '',
				'already-subscribed'	=> '',
				'update-link'			=> '',
				'email-subject'			=> '',
			),
			'custom_notifications'    => '',
			'impressions'             => 0,
			'submissions'             => 0,
			'custom_fields'           => array(),
		);
	}

	/**
	 * Update a given field for a form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int    $form_id The form ID to update.
	 * @param string $field   The form field to update.
	 * @param mixed  $data    The form data.
	 *
	 * @return bool Whether the form field was successfully updated.
	 */
	public function update_form_field( $form_id, $field, $data ) {
		return $this->update_form( $form_id, array( $field => $data ) );
	}
}
