<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.yikesplugins.com/
 * @since      6.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/public
 *
 * The public-facing functionality of the plugin.
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/public
 * @author     YIKES Inc. <plugins@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    6.0.0
	 * @access   private
	 * @var      string    $yikes_inc_easy_mailchimp_extender    The ID of this plugin.
	 */
	private $yikes_inc_easy_mailchimp_extender;
	/**
	 * The version of this plugin.
	 *
	 * @since    6.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    6.0.0
	 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $yikes_inc_easy_mailchimp_extender, $version ) {
		$this->yikes_inc_easy_mailchimp_extender = $yikes_inc_easy_mailchimp_extender;
		$this->version = $version;
		/*
		*	Include our helper functions
		*	@since 6.0.3.4
		*/
		include_once( YIKES_MC_PATH . 'public/helpers.php' );

		// Include our Shortcode & Processing functions (public folder)
		include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process_form_shortcode.php' );
		include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process_form_shortcode_depracated.php' );
		include_once( YIKES_MC_PATH . 'public/partials/shortcodes/yikes-mailchimp-subscriber-count.php' );

		// include our ajax processing class
		new YIKES_Inc_Easy_MailChimp_Public_Ajax();

		// Include our error logging class
		add_action( 'init' , array( $this , 'load_error_logging_class' ) , 1 );
		// load our checkbox classes
		add_action( 'init' , array( $this , 'load_checkbox_integration_classes' ) , 1 );
		// custom front end filter
		add_action( 'init', array( $this, 'yikes_custom_frontend_content_filter' ) );
		// Process non-ajax forms in the header
		add_action( 'init', array( $this, 'yikes_process_non_ajax_forms' ) );
		// Filter the user already subscribed response with a custom message
		add_filter( 'yikes-easy-mailchimp-update-existing-subscriber-text', array( $this, 'yikes_custom_already_subscribed_response' ), 10, 3 );
		// Filter the user already subscribed response with a custom message
		// add_filter( 'yikes-easy-mailchimp-user-already-subscribed-text', array( $this, 'yikes_custom_already_subscribed_text' ), 10, 3 );
	}

	/**
	*	Create our own custom the_content(); filter to prevent plugins and such from hooking in where not wanted
	*
	*	@since 6.0.3
	*/
	public function yikes_custom_frontend_content_filter() {
		add_filter( 'yikes-mailchimp-frontend-content', 'wptexturize' );
		add_filter( 'yikes-mailchimp-frontend-content', 'convert_smilies' );
		add_filter( 'yikes-mailchimp-frontend-content', 'convert_chars' );
		add_filter( 'yikes-mailchimp-frontend-content', 'wpautop' );
		add_filter( 'yikes-mailchimp-frontend-content', 'shortcode_unautop' );
		add_filter( 'yikes-mailchimp-frontend-content', 'prepend_attachment' );
	}

	/**
	 *	Load our checkbox integrations
	 *
	 *	Based on what the user has specified on the options page, lets
	 *	load our checkbox classes
	 *
	 *	@since 6.0.0
	**/
	public function load_checkbox_integration_classes() {
		// store our options
		$integrations = get_option( 'optin-checkbox-init' , array() );
		if( ! empty( $integrations ) && is_array( $integrations ) ) {
			// load our mail integrations class
			require_once YIKES_MC_PATH . 'public/classes/checkbox-integrations.php';
			// loop over selected classes and load them up!
			foreach( $integrations as $integration => $value ) {
				if( isset( $value['value'] ) && $value['value'] == 'on' ) {
					// load our class extensions
					require_once YIKES_MC_PATH . 'public/classes/checkbox-integrations/class.'.$integration.'-checkbox.php';
				}
			}
		}
	}

	/**
	 * Error logging class
	 *
	 * This is our main error logging class file, used to log errors to the error log.
	 *
	 * @since 6.0.0
	 */
	public function load_error_logging_class() {
		if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
			// if error logging is enabled we should include our error logging class
			require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging;
		}
	}

	/*
	*	On form submission, lets include our form processing file
	*	- processes non-ajax forms
	*	@since 6.0.3.4
	*/
	public function yikes_process_non_ajax_forms( $form_submitted ) {
		global $wpdb,$post;
		$form_id = ( ! empty( $_POST['yikes-mailchimp-submitted-form'] ) ) ? (int) $_POST['yikes-mailchimp-submitted-form'] : false; // store form id
		if( $form_id ) {
			$form_settings = self::yikes_retrieve_form_settings( $form_id );
			if( isset( $_POST ) && !empty( $_POST ) && isset( $form_id ) && $form_settings['submission_settings']['ajax'] == 0 ) {
				if( $_POST['yikes-mailchimp-submitted-form'] == $form_id ) { // ensure we only process the form that was submitted

					// Lets include our form processing file
					include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process/process_form_submission.php' );
					if( $form_settings['submission_settings']['redirect_on_submission'] == '1' ) {
						if( $form_submitted == 1 ) {
							// decode our settings
							$redirect_page = ( 'custom_url' != $form_settings['submission_settings']['redirect_page'] ) ? get_permalink( (int) $form_settings['submission_settings']['redirect_page'] ) : $form_settings['submission_settings']['custom_redirect_url'];
							wp_redirect( apply_filters( 'yikes-mailchimp-redirect-url', esc_url( $redirect_page ), $form_id, $post ) );
							exit;
						}
					}
				}
			}
		}
	}

	/**
	 * Get the given form data.
	 *
	 * This is a wrapper for the form interface get_form() method. It is recommended to use
	 * that method directly instead of this function.
	 *
	 * @author Jeremy Pry
	 * @deprecated
	 *
	 * @since 6.2.0 Use the new form interface.
	 * @since 6.0.3.4
	 *
	 * @param int $form_id The form ID to retrieve.
	 *
	 * @return array
	 */
	public static function yikes_retrieve_form_settings( $form_id ) {
		// if no form id, abort
		if( ! $form_id ) {
			return array();
		}

		$interface = yikes_easy_mailchimp_extender_get_form_interface();

		return $interface->get_form( $form_id );
	}

	/**
	 * Filter the unsubscribed response, allowing users to customize it
	 * Users can wrap text to create a custom update link, by wrapping text in [link]xxx[/link].
	 * @param  string   $response_text The default response.
	 * @param  int      $form_id       The form ID to retreive options from.
	 * @param  string   $link          The update profile link, when clicked this sends the user an email.
	 * @return string                  The final output for the update existing subscriber.
	 */
	public function yikes_custom_already_subscribed_response( $response_text, $form_id, $link ) {
		// if no form id found, abort
		if ( ! $form_id ) {
			return;
		}
		// retreive our form settings
		$form_settings = $form_settings = Yikes_Inc_Easy_Mailchimp_Extender_Public::yikes_retrieve_form_settings( $form_id );
		// if none, abort
		if ( ! $form_settings ) {
			return;
		}
		// trim trailing period
		if ( isset( $form_settings['error_messages']['update-link'] ) && ! empty( $form_settings['error_messages']['update-link'] ) ) {
			$response_text = $form_settings['error_messages']['update-link'];
			// extract the link text
			preg_match( '/\[link].*?\[\/link\]/', $response_text, $link_text );
			if ( $link_text && ! empty( $link_text ) ) {
				// Extract the custom link text ([link]*[/link])
				$custom_link_text = str_replace( '[/link]', '', str_replace( '[link]', '', str_replace( 'click to send yourself an update link', $link_text[0], $link ) ) );
				// Replace the link text, with our custom link text
				$response_text = str_replace( $link_text, $custom_link_text, $response_text );
			}
		}
		// Return our new string
		return $response_text;
	}

	/**
	 * Alter the beginning of the user already subscribed string
	 * Allowing users to use the email in the response, by adding [email] to the text
	 *
	 * @since 6.1
	 */
	public function yikes_custom_already_subscribed_text( $response_text, $form_id, $email ) {
		// if no form id found, abort
		if ( ! $form_id ) {
			return;
		}

		// retreive our form settings
		$form_settings = $form_settings = self::yikes_retrieve_form_settings( $form_id );
		// if none, abort
		if ( ! $form_settings ) {
			return;
		}

		// trim trailing period
		if ( isset( $form_settings['error_messages']['already-subscribed'] ) && ! empty( $form_settings['error_messages']['already-subscribed'] ) ) {
			$response_text = str_replace( '[email]', $email, $form_settings['error_messages']['already-subscribed'] );
		}
		// Return our new string
		return $response_text;
	}
}
