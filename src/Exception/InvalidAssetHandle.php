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
 * Class InvalidAssetHandle.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\Exception
 * @author  Freddie Mixell
 */
class InvalidAssetHandle extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a asset handle that is not
	 * valid.
	 *
	 * @since %VERSION%
	 *
	 * @param int $handle Asset handle that is not valid.
	 *
	 * @return static
	 */
	public static function from_handle( $handle ) {
		$message = sprintf(
			'The asset handle "%s" is not valid.',
			$handle
		);

		return new static( $message );
	}
}
