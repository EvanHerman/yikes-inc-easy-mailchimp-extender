<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Exception;

use InvalidArgumentException;

/**
 * Class InvalidOption
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
class InvalidOption extends InvalidArgumentException implements Exception {

	/**
	 * Create a new Exception instance from an invalid option.
	 *
	 * @since %VERSION%
	 *
	 * @param string|object $option The invalid Option.
	 *
	 * @return InvalidOption
	 */
	public static function from_option( $option ) {
		$message = sprintf(
			'The option "%s" does not implement OptionInterface.',
			is_object( $option ) ? get_class( $option ) : (string) $option
		);

		return new static( $message );
	}
}
