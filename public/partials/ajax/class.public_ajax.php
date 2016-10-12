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
		$user_email = $_POST['user_email'];
		$list_id = $_POST['list_id'];

		$api_key = yikes_get_mc_api_key();
		$dash_position = strpos( $api_key, '-' );
		$explode_key = explode( '-' , $api_key );
		$data_center = $explode_key[1];
		$full_site_url = get_bloginfo('url');

		// list details api call
		if( $dash_position !== false ) {
			$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/list.json';
		}
		$list_details = wp_remote_post( $api_endpoint, array(
			'body' => array(
				'apikey' => $api_key,
				'filters' => array(
					'list_id' => $list_id
				),
			),
			'timeout' => 10,
			'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
		) );
		$list_details = json_decode( wp_remote_retrieve_body( $list_details ), true );
		if( isset( $list_details['error'] ) ) {
			if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $list_details['error'], __( "Send Update Profile Email - Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			}
		}

		// account details api call
		if( $dash_position !== false ) {
			$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/helper/account-details.json';
		}
		$account_details = wp_remote_post( $api_endpoint, array(
			'body' => array(
				'apikey' => $api_key
			),
			'timeout' => 10,
			'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
		) );
		$account_details = json_decode( wp_remote_retrieve_body( $account_details ), true );
		if( isset( $account_details['error'] ) ) {
			if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $account_details['error'], __( "Send Update Profile Email - Get Account Details" , 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			}
		}

		// subscriber details api call
		if( $dash_position !== false ) {
			$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/member-info.json';
		}
		$subscriber_account_details = wp_remote_post( $api_endpoint, array(
			'body' => array(
				'apikey' => $api_key,
				'id'	=>	$list_id,
				'emails'	=> array(
					array( 'email' => $user_email ),
				),
			),
			'timeout' => 10,
			'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
		) );
		$subscriber_account_details = json_decode( wp_remote_retrieve_body( $subscriber_account_details ), true );
		if( isset( $subscriber_account_details['error'] ) ) {
			if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $subscriber_account_details['error'], __( "Send Update Profile Email - Get Member Info." , 'yikes-inc-easy-mailchimp-extender' ), "class.public_ajax.php" );
			}
		}

		// check for errors in any of the calls
		if( isset( $list_details['error'] ) || isset( $account_details['error'] ) || isset( $subscriber_account_details['error'] ) ) {
			$error_message = ( isset( $list_details['error'] ) ) ? $list_details['error'] : false;
			if( ! $error_message ) {
				$error_message = ( isset( $account_details['error'] ) ) ? $account_details['error'] : false;
				if( ! $error_message ) {
					$error_message = ( isset( $subscriber_account_details['error'] ) ) ? $subscriber_account_details['error'] : false;
					if( ! $error_message ) {
						$error_message = '';
					}
				}
			}
			$errorMessage = sprintf( __( 'Error sending update profile email. <strong>Error: %s</strong>. Please contact the site administrator.' , 'yikes-inc-easy-mailchimp-extender' ), $error_message );
			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">&#10005; ' . $errorMessage . '</div>',
				)
			);
			return;
		}

		// send the email!
		$subscriber_id = $subscriber_account_details['data'][0]['id'];
		$explode_url = explode( '.' , $account_details['contact']['url'] );
		$update_link_href = 'http://' . $explode_url[1] . '.' . $data_center . '.list-manage1.com/profile?u=' . $account_details['user_id'] . '&id=' . $list_id .'&e=' . $subscriber_id;
		$subject = 'MailChimp Profile Update';
		$headers = 'From: ' . $list_details['data'][0]['default_from_name'] . ' <' . $list_details['data'][0]['default_from_email'] . '>' . "\r\n";
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
					'response_text' => '<div class="yikes-easy-mc-success-message">' . sprintf( __( '%s Update email successfully sent. Please check your inbox for the message.' , 'yikes-inc-easy-mailchimp-extender' ), '&#10004;' ) . '</div>',
				)
			);
			exit;
		} else {
			wp_send_json_error(
				array(
					'response_text' => '<div class="yikes-easy-mc-error-message">' . sprintf( __( '%s Email failed to send. Please contact the site administrator.' , 'yikes-inc-easy-mailchimp-extender' ), '&#10005;' ) . '</div>',
				)
			);
			exit;
		}
	}
}
