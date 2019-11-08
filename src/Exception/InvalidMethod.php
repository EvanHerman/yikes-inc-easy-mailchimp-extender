<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Exception;

use LogicException;

/**
 * Class InvalidMethod
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
class InvalidMethod extends LogicException implements Exception {

	/**
	 * Create a new instance of this exception from an invalid method.
	 *
	 * @since %VERSION%
	 *
	 * @param string|object $class  The class that doesn't have the method.
	 * @param string        $method The method that is missing.
	 *
	 * @return static
	 */
	public static function from_method( $class, $method ) {
		$class = is_object( $class ) ? get_class( $class ) : $class;
		return new static( sprintf(
			'The class "%s" does not have the method "%s()".',
			$class,
			$method
		) );
	}
}
