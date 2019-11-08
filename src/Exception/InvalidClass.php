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
 * Class InvalidClass
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
class InvalidClass extends InvalidArgumentException implements Exception {

	/**
	 * Return new instance of this exception when a class does not implement the necessary interface.
	 *
	 * @since %VERSION%
	 *
	 * @param string $class     The invalid class name.
	 * @param string $interface The interface that the class should implement.
	 *
	 * @return static
	 */
	public static function from_interface( $class, $interface ) {
		return new static( sprintf(
			'The "%s" class must implement the "%s" interface.',
			$class,
			$interface
		) );
	}

	/**
	 * Return a new instance of this exception when a class is not found.
	 *
	 * @since %VERSION%
	 *
	 * @param string $class The invalid class name.
	 *
	 * @return static
	 */
	public static function not_found( $class ) {
		return new static( sprintf(
			'The class "%s" could not be found.',
			$class
		) );
	}

	/**
	 * Return a new instance of this exception when we expected one class but got another.
	 *
	 * @since %VERSION%
	 *
	 * @param string $class    The class name we received.
	 * @param string $expected The Class name we expected.
	 *
	 * @return static
	 */
	public static function mismatch( $class, $expected ) {
		return new static( sprintf(
			'Invalid class "%s". The "%s" class was expected.',
			$class,
			$expected
		) );
	}
}
