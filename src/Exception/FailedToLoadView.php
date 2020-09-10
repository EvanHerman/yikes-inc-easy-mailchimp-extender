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
 * Class FailedToLoadView.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\Exception
 * @author  Freddie Mixell
 */
class FailedToLoadView extends \RuntimeException implements Exception {

	/**
	 * Create a new instance of the exception if the view file itself created
	 * an exception.
	 *
	 * @since %VERSION%
	 *
	 * @param string     $uri       URI of the file that is not accessible or
	 *                              not readable.
	 * @param \Exception $exception Exception that was thrown by the view file.
	 *
	 * @return static
	 */
	public static function view_exception( $uri, $exception ) {
		$message = sprintf(
			'Could not load the View URI "%1$s". Reason: "%2$s".',
			$uri,
			$exception->getMessage()
		);

		return new static( $message, $exception->getCode(), $exception );
	}
}
