<?php

class YIKES_Inc_Easy_Mailchimp_Public_Ajax {

	/**
	 * Thetext domain of this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    Used for internationalization
	 */
	public function __construct() {
		// ajax process form submission
		add_action( 'wp_ajax_nopriv_process_form_submission', array( $this , 'process_form_submission' ), 10 );
		add_action( 'wp_ajax_process_form_submission', array( $this , 'process_form_submission' ), 10 );

		// ajax send update emails
		add_action( 'wp_ajax_nopriv_easy_forms_send_email', array( $this , 'sendUpdateProfileEmail' ), 10 );
		add_action( 'wp_ajax_easy_forms_send_email', array( $this , 'sendUpdateProfileEmail' ), 10 );

		// increase submission count for a given form on successful submit
		add_action( 'wp_ajax_nopriv_increase_submission_count' , array( $this , 'increase_submission_count' ), 10 );
		add_action( 'wp_ajax_increase_submission_count' , array( $this , 'increase_submission_count' ), 10 );
	}

	/*
	*	Process form submisssions sent via ajax from the front end
	*	$form_data - serialized form data submitted
	*/
	public function process_form_submission() {
		// include our ajax processing file
		include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process/process_form_submission_ajax.php' );
		exit();
	}

	/**
	 * Increase the submission count for a given form.
	 */
	public function increase_submission_count() {
		$form_id   = intval( $_POST['form_id'] );
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form      = $interface->get_form( $form_id );

		// If we don't have a form to update, just bail.
		if ( empty( $form ) ) {
			exit();
		}

		// Update the form.
		$submission_count = isset( $form['submissions'] ) ? $form['submissions'] + 1 : 1;
		$interface->update_form_field( $form_id, 'submissions', $submission_count );

		exit();
	}

	/*
		Send Update Profile Email
		@since v6.0.4.1
	*/
	public function sendUpdateProfileEmail() {
		$user_email    = filter_var( $_POST['user_email'], FILTER_SANITIZE_STRING );
		$user_id       = md5( $user_email );
		$list_id       = filter_var( $_POST['list_id'], FILTER_SANITIZE_STRING );
		$form_id       = filter_var( $_POST['form_id'], FILTER_SANITIZE_NUMBER_INT );
		$page_id       = filter_var( $_POST['page_id'], FILTER_SANITIZE_NUMBER_INT );
		$full_site_url = get_bloginfo( 'url' );
		$manager       = yikes_get_mc_api_manager();

		// Possibly handle errors.
		$errors   = array();
		$is_error = false;

		// List details API call.
		$list_details = $manager->get_list_handler()->get_list( $list_id );
		if ( is_wp_error( $list_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $list_details->get_error_code(),
				__( 'Send Update Profile Email - Get Account Lists', 'yikes-inc-easy-mailchimp-extender' ),
				'class.public_ajax.php'
			);
			$is_error = true;
			$errors[] = $list_details->get_error_message();
		}

		// Subscriber details API call.
		$subscriber_account_details = $manager->get_list_handler()->get_member( $list_id, $user_id );
		if ( is_wp_error( $subscriber_account_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $subscriber_account_details->get_error_code(), __( 'Send Update Profile Email - Get Member Info.', 'yikes-inc-easy-mailchimp-extender' ), 'class.public_ajax.php' );
			$is_error = true;
			$errors[] = $subscriber_account_details->get_error_message();
		}

		// Form details API call.
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		if ( ! empty( $interface ) && method_exists( $interface, 'get_form' ) && ! empty( $form_id ) ) {
			$form_data = $interface->get_form( $form_id );
			if ( ! empty( $form_data ) ) {
				if ( isset( $form_data['error_messages'] ) ) {

					if ( isset( $form_data['error_messages']['email-body'] ) && ! empty( $form_data['error_messages']['email-body'] ) ) {
						$email_body = apply_filters( 'the_content', $form_data['error_messages']['email-body'] );
					}

					if ( isset( $form_data['error_messages']['email-subject'] ) && ! empty( $form_data['error_messages']['email-subject'] ) ) {
						$email_subject = $form_data['error_messages']['email-subject'];
					}

					if ( isset( $form_data['error_messages']['update-email-success'] ) && ! empty( $form_data['error_messages']['update-email-success'] ) ) {
						$update_email_success_message = $form_data['error_messages']['update-email-success'];
					}

					if ( isset( $form_data['error_messages']['update-email-failure'] ) && ! empty( $form_data['error_messages']['update-email-failure'] ) ) {
						$update_email_failed_message = $form_data['error_messages']['update-email-failure'];
					}
				}
			}
		}

		// Check for errors in any of the calls.
		if ( $is_error ) {
			$error_message = '<br>' . join( '<br>', $errors );
			/* translators: the placeholder is a string of errors returned from Mailchimp. */
			$error_message = sprintf( __( 'Error sending update profile email. <strong>Error(s): %s</strong>. Please contact the site administrator.', 'yikes-inc-easy-mailchimp-extender' ), $error_message );
			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">&#10005; ' . $error_message . '</div>',
				)
			);

			return;
		}

		// Construct the headers & email message content.
		$subscriber_id    = $subscriber_account_details['unique_email_id'];
		$update_link_href = str_replace( '/subscribe', '/profile', $list_details['subscribe_url_long'] );
		$update_link_href = add_query_arg( 'e', $subscriber_id, $update_link_href );
		$update_link_tag  = '<a href="' . $update_link_href . '">';
		$headers          = 'From: ' . $list_details['campaign_defaults']['from_name'] . ' <' . $list_details['campaign_defaults']['from_email'] . '>' . "\r\n";
		$headers         .= 'Content-type: text/html';

		if ( ! isset( $email_subject ) ) {
			$email_subject = __( 'Mailchimp Profile Update', 'yikes-inc-easy-mailchimp-extender' );
		}

		// Check if the email_body was set.
		if ( ! isset( $email_body ) || empty( $email_body ) ) {

			// The email_body should always be set, but we've made this function static just in case so we can grab our default.
			$email_body = Yikes_Inc_Easy_Mailchimp_Forms_Admin::generate_default_email_body();
		}

		if ( ! isset( $update_email_success_message ) ) {
			/* translators: the placeholder is a unicode checkmark */
			$update_email_success_message = sprintf( __( '%s Update email successfully sent. Please check your inbox for the message.', 'yikes-inc-easy-mailchimp-extender' ), '&#10004;' );
		}

		if ( ! isset( $update_email_failed_message ) ) {
			/* translators: the placeholder is a unicode X */
			$update_email_failed_message = sprintf( __( '%s Email failed to send. Please contact the site administrator.', 'yikes-inc-easy-mailchimp-extender' ), '&#10005;' );
		}

		/* Run our replacement strings for the email body. */

		// We let the user use [link] text [/link] for the update profile link so replace [link] with the <a> tag.
		$email_body = str_replace( array( '[link]', '[LINK]' ), $update_link_tag, $email_body );

		// And replace [/link] with the closing </a> tag.
		$email_body = str_replace( array( '[/link]', '[/LINK]' ), '</a>', $email_body );

		// We let the user use [url] for their website so replace [url] with get_home_url().
		$email_body = str_replace( array( '[url]', '[URL]' ), get_home_url(), $email_body );

		// We let the user use [email] for the subscriber's email so replace [email] with the subscriber's email.
		$email_body = str_replace( array( '[email]', '[EMAIL]' ), $user_email, $email_body );

		// We let the user use [subscriber_id] for the subscriber's unique email ID so replace [subscriber_id] with the subscriber's unique email ID.
		$email_body = str_replace( array( '[subscriber_id]', '[SUBSCRIBER_ID]' ), $subscriber_id, $email_body );

		// We let the user use [form_name] for the form's name so replace [form_name] with the form's name.
		$email_body = str_replace( array( '[form_name]', '[FORM_NAME]' ), $form_data['form_name'], $email_body );

		// We let the user use [fname] and [lname] so replace those.
		$email_body = str_replace( array( '[fname]', '[FNAME]' ), isset( $subscriber_account_details['merge_fields']['FNAME'] ) ? $subscriber_account_details['merge_fields']['FNAME'] : '', $email_body );
		$email_body = str_replace( array( '[lname]', '[LNAME]' ), isset( $subscriber_account_details['merge_fields']['LNAME'] ) ? $subscriber_account_details['merge_fields']['LNAME'] : '', $email_body );

		/* Confirm that the email was sent */
		if ( wp_mail( $user_email, apply_filters( 'yikes-mailchimp-update-email-subject', $email_subject ), apply_filters( 'yikes-mailchimp-update-email-content', $email_body, $update_link_href ), $headers ) ) {

			$update_email_success_message = apply_filters( 'yikes-mailchimp-update-email-success-message', $update_email_success_message, $form_id, $user_email );
			$submission_settings          = isset( $form_data['submission_settings'] ) ? $form_data['submission_settings'] : null;
			$redirect_settings            = Yikes_Inc_Easy_Mailchimp_Extender_Process_Submission_Handler::handle_submission_response_success_redirect( $form_id, $submission_settings, $page_id );

			wp_send_json_success(
				array(
					'response_text'  => '<div class="yikes-easy-mc-success-message">' . $update_email_success_message . '</div>',
					'redirection'    => $redirect_settings['redirection'],
					'redirect'       => $redirect_settings['redirect'],
					'redirect_timer' => $redirect_settings['redirect_timer'],
					'new_window'     => $redirect_settings['new_window'],
				)
			);
		} else {

			$update_email_failed_message = apply_filters( 'yikes-mailchimp-update-email-failed-message', $update_email_failed_message, $form_id, $user_email );

			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">' . $update_email_failed_message . '</div>',
				)
			);
		}
	}
}
