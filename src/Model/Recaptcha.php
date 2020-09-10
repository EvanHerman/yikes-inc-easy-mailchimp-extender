<?php

namespace YIKES\EasyForms\Model;

use YIKES\EasyForms\Exception\InvalidRecaptcha;

final class Recaptcha {

    public $recaptcha_options;
    public $site_key;
    public $secret_key;

    const STATUS     = 'yikes-mc-recaptcha-status';
    const SITE_KEY   = 'yikes-mc-recaptcha-site-key';
    const SECRET_KEY = 'yikes-mc-recaptcha-secret-key';

    public function setup( $recaptcha_options = [] ) {
        if ( ! $this->has_recaptcha() ) {
            return false;
        }
        return $this->get_options( $recaptcha_options );
    }

    private function has_recaptcha() {
        if ( get_option( static::STATUS, '' ) == '1' ) {
            return true;
        }
        return false;
    }

    private function get_site_key() {
        $site_key = get_option( 'yikes-mc-recaptcha-secret-key' , '' );
        if ( ! $site_key ) {
            throw InvalidRecaptcha::from_site_key();
        }
        return $site_key;
    }

    private function get_secret_key() {
        $secret_key = get_option( 'yikes-mc-recaptcha-secret-key' , '' );
        if ( ! $secret_key ) {
            throw InvalidRecaptcha::from_secret_key();
        }
        return $secret_key;
    }

    private function get_options( $defaults ) {
        // Store the site language (to load recaptcha in a specific language).
        $locale       = get_locale();
        $locale_split = explode( '_', $locale );

        // Setup reCAPTCHA parameters.
        $lang       = ! empty( $locale_split ) ? $locale_split[0] : $locale;
        $lang       = ! empty( $defaults['recaptcha_lang'] ) ? $defaults['recaptcha_lang'] : $lang;
        $type       = ! empty( $defaults['recaptcha_type'] ) ? strtolower( $defaults['recaptcha_type'] ) : 'image'; // setup recaptcha type
        $theme      = ! empty( $defaults['recaptcha_theme'] ) ? strtolower( $defaults['recaptcha_theme'] ) : 'light'; // setup recaptcha theme
        $size       = ! empty( $defaults['recaptcha_size'] ) ? strtolower( $defaults['recaptcha_size'] ) : 'normal'; // setup recaptcha size
        $data_cb    = ! empty( $defaults['recaptcha_data_callback'] ) ? $defaults['recaptcha_data_callback'] : false; // setup recaptcha size
        $expired_cb = ! empty( $defaults['recaptcha_expired_callback'] ) ? $defaults['recaptcha_expired_callback'] : false; // setup recaptcha size

        $script_params = '?hl=' . $lang . '&onload=renderReCaptchaCallback&render=explicit';

        return [
            'language'         => $lang,
            'theme'            => $theme,
            'type'             => $type,
            'size'             => $size,
            'success_callback' => $data_cb,
            'expired_callback' => $expired_cb,
            'script_params'    => $script_params,
            'site_key'         => $this->get_site_key(),
            'secret_key'       => $this->get_secret_key(),
        ];
    }
}