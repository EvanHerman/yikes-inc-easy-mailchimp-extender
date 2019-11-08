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
 * Class InvalidPostID.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Exception
 * @author  Freddie Mixell
 */
class InvalidPostID extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a post ID that is not valid.
	 *
	 * @since %VERSION%
	 *
	 * @param string $id   Post ID that is not valid.
	 * @param string $type The object type that is meant to be used.
	 *
	 * @return static
	 */
	public static function from_id( $id, $type ) {
		$message = sprintf(
			/* translators: %1$s: the post ID. %2$s is a post type */
			__( 'The post ID "%1$s" is not a valid %2$s.', 'yikes-inc-easy-mailchimp-extender' ),
			$id,
			$type
		);

		return new static( $message );
	}
}
