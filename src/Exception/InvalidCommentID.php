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
 * Class InvalidPostID.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\Exception
 * @author  Freddie Mixell
 */
class InvalidCommentID extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a comment ID that is not valid.
	 *
	 * @since %VERSION%
	 *
	 * @param int $id Post ID that is not valid.
	 *
	 * @return static
	 */
	public static function from_id( $id ) {
		$message = sprintf(
			'The comment ID "%d" is not valid.',
			$id
		);

		return new static( $message );
	}
}
