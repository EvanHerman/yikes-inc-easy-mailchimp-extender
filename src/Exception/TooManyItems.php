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
 * Class TooManyItems
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class TooManyItems extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new exception instance for a post type that is limited.
	 *
	 * @since %VERSION%
	 *
	 * @param string $post_type The post type.
	 * @param int    $limit     The limit for the post type.
	 *
	 * @return static
	 */
	public static function from_post_type( $post_type, $limit ) {
		$message = sprintf(
			/* translators: %1$s is the post type, %2$d is the item limit */
			_n(
				'%1$s do not support more than %2$d item.',
				'%1$s do not support more than %2$d items.',
				$limit,
				'yikes-inc-easy-mailchimp-extender'
			),
			$post_type,
			$limit
		);

		return new static( $message );
	}
}
