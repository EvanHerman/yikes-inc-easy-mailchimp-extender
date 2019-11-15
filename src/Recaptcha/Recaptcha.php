<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Recaptcha;

use YIKES\EasyForms\Exception\InvalidRecaptcha;
use YIKES\EasyForms\Form\FormOptions;

class Recaptcha extends BaseRecaptcha {
    /**
     * Id for the form this recaptcha is used on.
     */
    private $form_id;

    /**
	 * Boolean value if the form has a recaptcha.
	 */
	private $has_recaptcha = false;

	/**
	 * If there's recaptcha these will be the settings.
	 */
    private $recaptcha_settings = [];
    
    /**
     * Construct Recaptcha
     */
    public function __construct( $form_id, FormOptions $form_options ) {
        $this->form_id = $form_id;
        $this->get_recaptcha_status( $form_options );
    }

    /**
     * Recaptcha Shortcode Params
     *
     * @return array Recaptcha shortcode params.
     */
    private function recaptcha_shortcode_params() {
        return apply_filters( 'yikes-mailchimp-recaptcha-parameters', array(
            'language'         => $this->recaptcha_settings['recaptcha_lang'],
            'theme'            => $theme,
            'type'             => $type,
            'size'             => $size,
            'success_callback' => $data_cb,
            'expired_callback' => $expired_cb,
        ), $this->form_id );
    }

    /**
     * Check to see if recaptcha has been set globally.
     */
    private function get_global_recaptcha_status() {
		if ( get_option( 'yikes-mc-recaptcha-status' , '' ) == '1' ) {
			$this->has_recaptcha = true;
		} else {
            throw new InvalidRecaptcha();
        }
	}

    /**
     * Check for global recaptcha and then double check for manual recaptcha.
     */
	private function get_recaptcha_status( FormOptions $form_options ) {
        $global_recaptcha = $this->get_global_recaptcha();

        if ( $global_recaptcha ) {
            $this->has_recaptcha = true;
        } else if ( $form_options['recaptcha'] == '0' ) {
            throw new InvalidRecaptcha();
        }

        $this->validate_recaptcha_keys();
        $this->set_recaptcha_options( $form_options );
	}

	private function get_recaptcha_site_key() {
		// If either of the Private the Secret key is left blank, we should display an error back to the user.
		$site_key = get_option( 'yikes-mc-recaptcha-site-key' , '' );
		if ( $site_key == '' ) {
			throw ( new InvalidRecaptcha )->from_site_key();
		}
		$this->recaptcha_settings['site-key'] = $site_key;
    }

    private function validate_recaptcha_keys() {
        // If either of the Private the Secret key is left blank, we should display an error back to the user.
        if ( get_option( 'yikes-mc-recaptcha-site-key' , '' ) == '' ) {
            throw ( new InvalidRecaptcha )->from_site_key(); 
        }
        if ( get_option( 'yikes-mc-recaptcha-secret-key' , '' ) == '' ) {
            throw ( new InvalidRecaptcha )->from_secret_key();
        }
    }

    private function set_recaptcha_options( FormOptions $form_options ) {
        $this->recaptcha_settings = [
            'recaptcha'                  => $form_options['recaptcha'],
            'recaptcha_lang'             => $this->set_locale( $form_options['recaptcha_lang'] ),
            'recaptcha_type'             => ! empty( $form_options['recaptcha_type'] ) ? strtolower( $form_options['recaptcha_type'] ) : 'image',
            'recaptcha_theme'            => ! empty( $form_options['recaptcha_theme'] ) ? strtolower( $form_options['recaptcha_theme'] ) : 'light',
            'recaptcha_size'             => ! empty( $form_options['recaptcha_size'] ) ? strtolower( $form_options['recaptcha_size'] ) : 'normal',
            'recaptcha_data_callback'    => ! empty( $form_options['recaptcha_data_callback'] ) ? $form_options['recaptcha_data_callback'] : false,
            'recaptcha_expired_callback' => ! empty( $form_options['recaptcha_expired_callback'] ) ? $form_options['recaptcha_expired_callback'] : false,
        ];
    }

    private function set_locale( $recaptcha_lang ) {
        // Store the site language (to load recaptcha in a specific language).
        $locale       = get_locale();
        $locale_split = explode( '_', $locale );

        // Setup reCAPTCHA parameters.
        $lang = ! empty( $locale_split ) ? $locale_split[0] : $locale;
        $lang = ! empty( $recaptcha_lang ) ? $recaptcha_lang : $lang;

        return $lang;
    }
    
    private function get_global_recaptcha() {
        return get_option( 'yikes-mc-recaptcha-status' , '' );
    }
}