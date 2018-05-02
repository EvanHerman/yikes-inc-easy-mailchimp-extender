<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'gutenberg/gutenberg.php' ) ) {
	$YIKES_Easy_Forms_Blocks = new YIKES_Easy_Forms_Blocks();
}

/****************************************/
/**            Gutenberg               **/
/****************************************/

class YIKES_Easy_Forms_Blocks {

	public function __construct() {

		// Admin
		add_action( 'wp_ajax_yikes_get_forms', array( $this, 'get_forms' ) );
		add_action( 'wp_ajax_yikes_get_form', array( $this, 'get_form' ) );
		add_action( 'wp_ajax_yikes_get_recaptcha', array( $this, 'get_recaptcha' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'gutenberg_scripts' ) );

		// Front end
		add_action( 'init', array( $this, 'register_easy_forms_block' ) );
	}

	public function gutenberg_scripts() {

		// Localize the calendar
		global $wp_locale;
		$datepicker_options = array(
			'rtl'                 => $wp_locale->is_rtl(),
			'month_names'         => array_values( $wp_locale->month ),
			'month_names_short'   => array_values( $wp_locale->month_abbrev ),
			'day_names'           => array_values( $wp_locale->weekday ),
			'day_names_short'     => array_values( $wp_locale->weekday_abbrev ),
			'day_names_min'       => array_values( $wp_locale->weekday_initial ),
			'first_day'           => get_option( 'start_of_week' ),
			'change_month'        => false,
			'change_year'         => false,
			'min_date'            => null,
			'max_date'            => null,
			'default_date'        => null,
			'number_of_months'    => 1,
			'show_other_months'   => false,
			'select_other_months' => null,
			'show_anim'           => '',
			'show_button_panel'   => false,
		);

		wp_register_script( 'yikes-datepicker-scripts', YIKES_MC_URL . 'public/js/yikes-datepicker-scripts.min.js', array( 'jquery-ui-datepicker' ), YIKES_MC_VERSION, false );
		wp_localize_script( 'yikes-datepicker-scripts', 'datepicker_settings', $datepicker_options );
		wp_enqueue_script( 'yikes-datepicker-scripts' );
		wp_enqueue_style( 'jquery-datepicker-styles' , YIKES_MC_URL . 'public/css/jquery-ui.min.css' );
		wp_enqueue_style( 'yikes-datepicker-styles' , YIKES_MC_URL . 'public/css/yikes-datepicker-styles.min.css' );

		wp_register_script( 'yikes-easy-forms-blocks', YIKES_MC_URL . 'blocks/release/blocks.js', array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api' ), filemtime( plugin_dir_path( __FILE__ ) . 'release/blocks.js' ) );
		wp_localize_script( 'yikes-easy-forms-blocks', 'ez_forms_gb_data', array( 
			'ajax_url'              => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'fetch_form_nonce'      => wp_create_nonce( 'fetch_form_nonce' ),
			'fetch_forms_nonce'     => wp_create_nonce( 'fetch_forms_nonce' ),
			'fetch_recaptcha_nonce' => wp_create_nonce( 'fetch_recaptcha_nonce' ),
		) );
		wp_enqueue_script( 'yikes-easy-forms-blocks' );

		wp_enqueue_script( 'yikes-google-recaptcha', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ) );

		if ( ! defined( 'YIKES_MAILCHIMP_EXCLUDE_STYLES' ) ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-public-styles', YIKES_MC_URL . 'public/css/yikes-inc-easy-mailchimp-extender-public.min.css' );
		}

		// wp_enqueue_style( 'yikes-easy-forms-block-admin-css', YIKES_MC_URL . 'blocks/release/blocks.css' );
	}

	public function get_forms() {

		// Verify Nonce
		if ( ! check_ajax_referer( 'fetch_forms_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		// Get all of our forms
		$form_interface = yikes_easy_mailchimp_extender_get_form_interface();

		$all_forms = $form_interface->get_all_forms();

		wp_send_json_success( array_values( $all_forms ) );
	}

	public function get_form() {

		// Verify Nonce
		if ( ! check_ajax_referer( 'fetch_form_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		$form_id = isset( $_POST['form_id'] ) ? $_POST['form_id'] : '';

		if ( empty( $form_id ) ) {
			wp_send_json_error( '1' );
		}

		$form_interface = yikes_easy_mailchimp_extender_get_form_interface();

		$form = $form_interface->get_form( $form_id );

		wp_send_json_success( $form );	
	}

	public function get_recaptcha() {

		// Verify Nonce
		if ( ! check_ajax_referer( 'fetch_recaptcha_nonce', 'nonce', false ) ) {
			wp_send_json_error( '1' );
		}

		if ( get_option( 'yikes-mc-recaptcha-status' , '' ) == '1' ) {

			$site_key   = get_option( 'yikes-mc-recaptcha-site-key' , '' );
			$secret_key = get_option( 'yikes-mc-recaptcha-secret-key' , '' );

			// If either of the Private the Secret key is left blank, we should display an error back to the user
			if ( empty( $site_key ) || empty( $secret_key ) ) {
				wp_send_json_error();
			}

			$locale   = get_locale();
			$locale_a = explode( '_', $locale );
			$locale   = isset( $locale_a[0] ) ? $locale_a[0] : $locale;

			wp_send_json_success( array( 'site_key' => $site_key, 'secret_key' => $secret_key, 'locale' => $locale ) );
		}

		wp_send_json_error();
	}

	public function register_easy_forms_block() {

		// Hook server side rendering into render callback
		register_block_type( 'yikes-inc-easy-forms/easy-forms-block', array(
		    'render_callback' => array( $this, 'render_easy_forms_block' ),
		) );	
	}

	public function render_easy_forms_block( $attributes ) {

		// Prevent this from being run when in the admin/saving the block.
		// I'm not sure why this would run when we're saving a block but it is definitely being called.
		if ( is_admin() || isset( $_POST['status'] ) && $_POST['status'] === 'publish' ) {
			return null;
		}

		$shortcode_attributes = array(
			'form'                       => $attributes['form_id'],
			'submit'                     => isset( $attributes['submit_button_text'] ) && ! empty( $attributes['submit_button_text'] ) ? $attributes['submit_button_text'] : '',
			'title'                      => isset( $attributes['show_title'] ) && $attributes['show_title'] === true ? '1' : '0',
			'custom_title'               => isset( $attributes['form_title'] ) ? $attributes['form_title'] : '',
			'description'                => isset( $attributes['show_description'] ) && $attributes['show_description'] === true ? '1' : '0',
			'custom_description'         => isset( $attributes['form_description'] ) ? $attributes['form_description'] : '',
			'ajax'                       => isset( $attributes['is_ajax'] ) && $attributes['is_ajax'] === true ? '1' : '0',
			'recaptcha'                  => isset( $attributes['recaptcha'] ) && $attributes['recaptcha'] === false ? '0' : '',
			'recaptcha_lang'             => isset( $attributes['recaptcha_lang'] ) ? $attributes['recaptcha_lang'] : '',
			'recaptcha_type'             => isset( $attributes['recaptcha_type'] ) ? $attributes['recaptcha_type'] : '',
			'recaptcha_theme'            => isset( $attributes['recaptcha_theme'] ) ? $attributes['recaptcha_theme'] : '',
			'recaptcha_size'             => isset( $attributes['recaptcha_size'] ) ? $attributes['recaptcha_size'] : '',
			'recaptcha_data_callback'    => isset( $attributes['recaptcha_verify_callback'] ) ? $attributes['recaptcha_verify_callback'] : '',
			'recaptcha_expired_callback' => isset( $attributes['recaptcha_expired_callback'] ) ? $attributes['recaptcha_expired_callback'] : '',
			'inline'                     => isset( $attributes['inline'] ) && $attributes['inline'] === true ? '1' : '0',
		);

		return process_mailchimp_shortcode( $shortcode_attributes );
	}
}