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
 * Class EmptyArray
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
class EmptyArray extends InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of an exception when an empty array is provided.
	 *
	 * @since %VERSION%
	 *
	 * @param string $function The function name.
	 *
	 * @return static
	 */
	public static function from_function( $function ) {
		$message = sprintf( 'Function %s cannot receive an empty array.', $function );

		return new static( $message );
	}
}
