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
 * Class InvalidService.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Exception
 * @author  Freddie Mixell
 */
class InvalidService extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a service class name that is
	 * not recognized.
	 *
	 * @since %VERSION%
	 *
	 * @param string $service Class name of the service that was not recognized.
	 *
	 * @return static
	 */
	public static function from_service( $service ) {
		$message = sprintf(
			'The service "%s" is not recognized and cannot be registered.',
			is_object( $service )
				? get_class( $service )
				: (string) $service
		);

		return new static( $message );
	}
}
