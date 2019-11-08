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
 * Class NoDefault
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Exception
 * @author  Freddie Mixell
 */
class NoDefault extends \LogicException implements Exception {

	/**
	 * Create a new exception when a field needs a default.
	 *
	 * @author Freddie Mixell
	 *
	 * @param string $slug The field slug that needs a default value.
	 *
	 * @return static
	 */
	public static function default_value( $slug ) {
		$message = sprintf(
			/* translators: %s refers to a field's slug */
			__( 'The field "%s" must have a default value.', 'yikes-inc-easy-mailchimp-extender' ),
			$slug
		);

		return new static( $message );
	}
}
