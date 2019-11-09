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
 * Class InvalidProperty
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class InvalidProperty extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the class when a property cannot be modified.
	 *
	 * @since %VERSION%
	 *
	 * @param string $property The property that cannot be modified.
	 *
	 * @return static
	 */
	public static function cannot_modify( $property ) {
		return new static( sprintf(
			'The property "%s" cannot be modified.',
			$property
		) );
	}

	/**
	 * Create a new instance of the class when the property does not allow multiple values.
	 *
	 * @since %VERSION%
	 *
	 * @param string $property The property name.
	 *
	 * @return static
	 */
	public static function not_multiple( $property ) {
		return new static( sprintf(
			'The property "%s" does not allow multiple values.',
			$property
		) );
	}
}
