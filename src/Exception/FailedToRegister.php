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
 * Class FailedToRegister
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
final class FailedToRegister extends \LogicException implements Exception {

	/**
	 * Create an instance of this exception when an asset was not registered before being enqueued.
	 *
	 * @since %VERSION%
	 *
	 * @param string $handle The asset handle.
	 *
	 * @return FailedToRegister
	 */
	public static function asset_not_registered( $handle ) {
		return new static(
			sprintf(
				'The asset "%s" was not registered before it was enqueued. Make sure to call the register() method during init.',
				$handle
			)
		);
	}
}
