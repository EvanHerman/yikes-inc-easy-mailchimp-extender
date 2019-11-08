<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Exception;

/**
 * Class InvalidKey
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
class InvalidKey extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of an exception when an empty key is provided.
	 *
	 * @since %VERSION%
	 *
	 * @param string $function The name of the calling function or method.
	 *
	 * @return InvalidKey
	 */
	public static function empty_key( $function ) {
		$message = sprintf( 'The key for "%s" cannot be empty.', $function );

		return new static( $message );
	}

	/**
	 * Create a new instance of an exception when a key is not found for the function.
	 *
	 * @since %VERSION%
	 *
	 * @param string $key      The key that wasn't found.
	 * @param string $function The function where the key isn't found.
	 *
	 * @return static
	 */
	public static function not_found( $key, $function ) {
		$message = sprintf( 'The key "%s" was not found for function "%s".', $key, $function );

		return new static( $message );
	}
}
