<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Exception;

/**
 * Class InvalidRecaptcha
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class InvalidRecaptcha extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a field class name that is
	 * not recognized.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field Class name of the service that was not recognized.
	 *
	 * @return static
	 */
	public static function from_site_key() {
		$message = sprintf(
            __( 'Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA site key!' , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>'
		);

		return new static( $message );
	}

    /**
	 * Create a new instance of the exception for a field class name that is
	 * not recognized.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field Class name of the service that was not recognized.
	 *
	 * @return static
	 */
	public static function from_secret_key() {
		$message = sprintf(
            __( 'Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA site key!' , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>'
		);

		return new static( $message );
	}

}
