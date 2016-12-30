<?php

class YIKES_Inc_Easy_MailChimp_Public_Ajax {

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
		$user_email    = $_POST['user_email'];
		$user_id       = md5( $user_email );
		$list_id       = $_POST['list_id'];
		$full_site_url = get_bloginfo( 'url' );
		$manager       = yikes_get_mc_api_manager();

		// Possibly handle errors.
		$errors   = array();
		$is_error = false;

		// list details api call
		$list_details = $manager->get_list_handler()->get_list( $list_id );
		if ( is_wp_error( $list_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $list_details->get_error_code(),
				__( "Send Update Profile Email - Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
				"class.public_ajax.php"
			);
			$is_error = true;
			$errors[] = $list_details->get_error_message();
		}

		// account details api call
		$account_details = $manager->get_account_handler()->get_account( false );
		if ( is_wp_error( $account_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $account_details->get_error_code(), __( "Send Update Profile Email - Get Account Details", 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			$is_error = true;
			$errors[] = $account_details->get_error_message();
		}

		// subscriber details api call
		$subscriber_account_details = $manager->get_list_handler()->get_member( $list_id, $user_id );
		if ( is_wp_error( $subscriber_account_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $subscriber_account_details->get_error_code(), __( "Send Update Profile Email - Get Member Info.", 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			$is_error = true;
			$errors[] = $subscriber_account_details->get_error_message();
		}

		// check for errors in any of the calls
		if ( $is_error ) {
			$error_message = '<br>' . join( '<br>', $errors );
			$errorMessage  = sprintf( __( 'Error sending update profile email. <strong>Error(s): %s</strong>. Please contact the site administrator.', 'yikes-inc-easy-mailchimp-extender' ), $error_message );
			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">&#10005; ' . $errorMessage . '</div>',
				)
			);

			return;
		}

		// send the email!
		$subscriber_id = $subscriber_account_details['unique_email_id'];
		$update_link_href = str_replace( '/subscribe', '/profile', $list_details['subscribe_url_long'] );
		$update_link_href = add_query_arg( 'e', $subscriber_id, $update_link_href );
		$subject          = 'MailChimp Profile Update';
		$headers          = 'From: ' . $list_details['campaign_defaults']['from_name'] . ' <' . $list_details['campaign_defaults']['from_email'] . '>' . "\r\n";
		$headers .= 'Content-type: text/html';
		$email_content = '<p>Greetings,</p> <p>A request has been made to update your MailChimp account profile information. To do so please use the following link: <a href="' . $update_link_href . '" title="Update MailChimp Profile">Update MailChimp Profile Info.</a>';
		$email_content .= "<p>If you did not request this update, please disregard this email.</p>";
		$email_content .= '<p>&nbsp;</p>';
		$email_content .= '<p>This email was sent from : ' . $full_site_url . '</p>';
		$email_content .= '<p>&nbsp;</p>';
		$email_content .= '<p>&nbsp;</p>';
		$email_content .= '<p style="font-size:13px;margin-top:5em;"><em>This email was generated by the <a href="http://www.wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" target="_blank">Easy Forms for MailChimp</a> plugin, created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></em></p>';
		/* Confirm that the email was sent */
		if ( wp_mail( $user_email, apply_filters( 'yikes-mailchimp-update-email-subject', $subject ), apply_filters( 'yikes-mailchimp-update-email-content', $email_content, $update_link_href ), $headers ) ) {
			wp_send_json_success(
				array(
					'response_text' => '<div class="yikes-easy-mc-success-message">' . sprintf( __( '%s Update email successfully sent. Please check your inbox for the message.', 'yikes-inc-easy-mailchimp-extender' ), '&#10004;' ) . '</div>',
				)
			);
		} else {
			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">' . sprintf( __( '%s Email failed to send. Please contact the site administrator.', 'yikes-inc-easy-mailchimp-extender' ), '&#10005;' ) . '</div>',
				)
			);
		}
	}
}
