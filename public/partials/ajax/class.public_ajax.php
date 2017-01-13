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
		$user_email		= $_POST['user_email'];
		$user_id		= md5( $user_email );
		$list_id		= $_POST['list_id'];
		$form_id		= $_POST['form_id'];
		$full_site_url	= get_bloginfo( 'url' );
		$manager		= yikes_get_mc_api_manager();

		// Possibly handle errors.
		$errors   = array();
		$is_error = false;

		// List details API call
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

		// Account details API call
		$account_details = $manager->get_account_handler()->get_account( false );
		if ( is_wp_error( $account_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $account_details->get_error_code(), __( "Send Update Profile Email - Get Account Details", 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			$is_error = true;
			$errors[] = $account_details->get_error_message();
		}

		// Subscriber details API call
		$subscriber_account_details = $manager->get_list_handler()->get_member( $list_id, $user_id );
		if ( is_wp_error( $subscriber_account_details ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $subscriber_account_details->get_error_code(), __( "Send Update Profile Email - Get Member Info.", 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			$is_error = true;
			$errors[] = $subscriber_account_details->get_error_message();
		}

		// Form details API call
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		if ( ! empty( $interface ) && method_exists( $interface, 'get_form' ) && isset( $form_id ) ) {
			$form_data = $interface->get_form( $form_id );
			if ( ! empty( $form_data ) ) {
				if ( isset( $form_data['error_messages'] ) ) {
					if ( isset( $form_data['error_messages']['email-body'] ) && ! empty( $form_data['error_messages']['email-body'] ) ) {
						$email_body = apply_filters( 'the_content', $form_data['error_messages']['email-body'] );
					}
					if ( isset( $form_data['error_messages']['email-subject'] ) && ! empty( $form_data['error_messages']['email-subject'] ) ) {
						$email_subject = $form_data['error_messages']['email-subject'];
					}
				}
			}
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

		// Construct the headers & email message content
		$subscriber_id 	  = $subscriber_account_details['unique_email_id'];
		$update_link_href = str_replace( '/subscribe', '/profile', $list_details['subscribe_url_long'] );
		$update_link_href = add_query_arg( 'e', $subscriber_id, $update_link_href );
		$update_link_tag  = '<a href="' . $update_link_href . '">';
		$headers          = 'From: ' . $list_details['campaign_defaults']['from_name'] . ' <' . $list_details['campaign_defaults']['from_email'] . '>' . "\r\n";
		$headers 		 .= 'Content-type: text/html';

		if ( ! isset( $email_subject ) ) {
			$email_subject = __( 'MailChimp Profile Update', 'yikes-inc-easy-mailchimp-extender' );
		}
		
		// Check if the email_body was set
		if ( ! isset( $email_body ) || empty( $email_body ) ) {

			// The email_body should always be set, but we've made this function static just in case so we can grab our default
			$email_body = Yikes_Inc_Easy_Mailchimp_Forms_Admin::generate_default_email_body();
		}

		// Run our replacement strings for the email body

		// We let the user use [link] text [/link] for the update profile link
		// So replace [link] with the <a> tag 
		$email_body = str_replace( '[link]', $update_link_tag, $email_body );

		// And replace [/link] with the closing </a> tag
		$email_body = str_replace( '[/link]', '</a>', $email_body );

		// We let the user use [url] for their website
		// So replace [url] with get_home_url()
		$email_body = str_replace( '[url]', get_home_url(), $email_body );

		/* Confirm that the email was sent */
		if ( wp_mail( $user_email, apply_filters( 'yikes-mailchimp-update-email-subject', $email_subject ), apply_filters( 'yikes-mailchimp-update-email-content', $email_body, $update_link_href ), $headers ) ) {
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
