<?php
/**
 * Class YIKES_Easy_Form.
 */
class YIKES_Easy_Form_Block extends YIKES_Easy_Forms_Blocks {

	const BLOCK = 'easy-forms-block';

	/**
	 * Enqueue our scripts.
	 */
	public function editor_scripts() {

		// Localize the calendar.
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

		wp_register_script( 'yikes-datepicker-scripts', YIKES_MC_URL . 'public/js/yikes-datepicker-scripts.min.js', array( 'jquery-ui-datepicker' ), YIKES_MC_VERSION, true );
		wp_localize_script( 'yikes-datepicker-scripts', 'datepicker_settings', $datepicker_options );

		// Enqueueing styles.
		wp_enqueue_script( 'yikes-datepicker-scripts' );
		wp_enqueue_style( 'jquery-datepicker-styles', YIKES_MC_URL . 'public/css/jquery-ui.min.css', array(), YIKES_MC_VERSION );
		wp_enqueue_style( 'yikes-datepicker-styles', YIKES_MC_URL . 'public/css/yikes-datepicker-styles.min.css', array(), YIKES_MC_VERSION );
		wp_enqueue_style( 'yikes-easy-forms-blocks-css', YIKES_MC_URL . 'blocks/easy-forms-block/build/style.css', array(), YIKES_MC_VERSION );

		wp_register_script( 'yikes-easy-forms-blocks', YIKES_MC_URL . 'blocks/easy-forms-block/build/easy-forms-blocks.js', array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api' ), time(), true );
		wp_localize_script( 'yikes-easy-forms-blocks', 'ez_forms_gb_data', array(
			'ajax_url'              => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'fetch_form_nonce'      => wp_create_nonce( 'fetch_form_nonce' ),
			'fetch_forms_nonce'     => wp_create_nonce( 'fetch_forms_nonce' ),
			'fetch_recaptcha_nonce' => wp_create_nonce( 'fetch_recaptcha_nonce' ),
			'get_api_key_status'    => wp_create_nonce( 'get_api_key_status' ),
			'block_namespace'       => parent::BLOCK_NAMESPACE,
			'block_name'            => static::BLOCK,
		) );
		wp_enqueue_script( 'yikes-easy-forms-blocks' );

		wp_enqueue_script( 'yikes-google-recaptcha', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), null, true );

		if ( ! defined( 'YIKES_MAILCHIMP_EXCLUDE_STYLES' ) ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-public-styles', YIKES_MC_URL . 'public/css/yikes-inc-easy-mailchimp-extender-public.min.css', array(), YIKES_MC_VERSION );
		}
	}

	/**
	 * Take the shortcode parameters from the Gutenberg block and render our shortcode.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 */
	public function render_block( $attributes, $content ) {

		if ( ! isset( $attributes['form_id'] ) ) {
			return;
		}

		$shortcode_attributes = array(
			'form'                       => $attributes['form_id'],
			'submit'                     => isset( $attributes['submit_button_text'] ) && ! empty( $attributes['submit_button_text'] ) ? $attributes['submit_button_text'] : '',
			'title'                      => isset( $attributes['show_title'] ) && true === $attributes['show_title'] ? '1' : '0',
			'custom_title'               => isset( $attributes['form_title'] ) ? $attributes['form_title'] : '',
			'description'                => isset( $attributes['show_description'] ) && true === $attributes['show_description'] ? '1' : '0',
			'custom_description'         => isset( $attributes['form_description'] ) ? $attributes['form_description'] : '',
			'ajax'                       => isset( $attributes['is_ajax'] ) && true === $attributes['is_ajax'] ? '1' : '0',
			'recaptcha'                  => ! isset( $attributes['recaptcha'] ) || isset( $attributes['recaptcha'] ) && false === $attributes['recaptcha'] ? '0' : '',
			'recaptcha_lang'             => isset( $attributes['recaptcha_lang'] ) ? $attributes['recaptcha_lang'] : '',
			'recaptcha_type'             => isset( $attributes['recaptcha_type'] ) ? $attributes['recaptcha_type'] : '',
			'recaptcha_theme'            => isset( $attributes['recaptcha_theme'] ) ? $attributes['recaptcha_theme'] : '',
			'recaptcha_size'             => isset( $attributes['recaptcha_size'] ) ? $attributes['recaptcha_size'] : '',
			'recaptcha_data_callback'    => isset( $attributes['recaptcha_verify_callback'] ) ? $attributes['recaptcha_verify_callback'] : '',
			'recaptcha_expired_callback' => isset( $attributes['recaptcha_expired_callback'] ) ? $attributes['recaptcha_expired_callback'] : '',
			'inline'                     => isset( $attributes['inline'] ) && true === $attributes['inline'] ? '1' : '0',
		);

		// We want to run process_mailchimp_shortcode() but we need to return the plaintext shortcode or Gutenberg will autop() the shortcode content.
		return sprintf(
			'[yikes-mailchimp form="%s" submit="%s" title="%s" custom_title="%s" description="%s" custom_description="%s" ajax="%s" recaptcha="%s"  recaptcha_lang="%s" recaptcha_type="%s" recaptcha_theme="%s" recaptcha_size="%s" recaptcha_data_callback="%s" recaptcha_expired_callback="%s" inline="%s"]',
			$shortcode_attributes['form'],
			$shortcode_attributes['submit'],
			$shortcode_attributes['title'],
			$shortcode_attributes['custom_title'],
			$shortcode_attributes['description'],
			$shortcode_attributes['custom_description'],
			$shortcode_attributes['ajax'],
			$shortcode_attributes['recaptcha'],
			$shortcode_attributes['recaptcha_lang'],
			$shortcode_attributes['recaptcha_type'],
			$shortcode_attributes['recaptcha_theme'],
			$shortcode_attributes['recaptcha_size'],
			$shortcode_attributes['recaptcha_data_callback'],
			$shortcode_attributes['recaptcha_expired_callback'],
			$shortcode_attributes['inline']
		);
	}
}
