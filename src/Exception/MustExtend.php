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
 * Class MustExtend
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\Exception
 * @author  Freddie Mixell
 */
class MustExtend extends \LogicException implements Exception {

	/**
	 * Create a new exception when a slug needs extended.
	 *
	 * @author Freddie Mixell
	 *
	 * @param string $slug The default slug that needs extended.
	 *
	 * @return static
	 */
	public static function default_slug( $slug ) {
		$message = sprintf(
			/* translators: %s refers to the default slug */
			__( 'The default slug "%s" must be extended in a subclass.', 'yikes-inc-easy-mailchimp-extender' ),
			$slug
		);

		return new static( $message );
	}

	/**
	 * Create a new exception when a tag needs extended.
	 *
	 * @since %VERSION%
	 *
	 * @param string $tag The default tag that needs extended.
	 *
	 * @return static
	 */
	public static function default_tag( $tag ) {
		$message = sprintf(
			/* translators: %s refers to the default tag */
			__( 'The default tag "%s" must be extended in a subclass.', 'yikes-inc-easy-mailchimp-extender' ),
			$tag
		);

		return new static( $message );
	}

	/**
	 * Create a new exception when a view needs extended.
	 *
	 * @since %VERSION%
	 *
	 * @param string $view The default view that needs extended.
	 *
	 * @return static
	 */
	public static function default_view( $view ) {
		$message = sprintf(
			/* translators: %s refers to the default view */
			__( 'The default view "%s" must be extended in a subclass.', 'yikes-inc-easy-mailchimp-extender' ),
			$view
		);

		return new static( $message );
	}

	/**
	 * Create a new exception when a type needs extended.
	 *
	 * @since %VERSION%
	 *
	 * @param string $type The default type that needs extended.
	 *
	 * @return static
	 */
	public static function default_type( $type ) {
		$message = sprintf(
			/* translators: %s refers to the default type */
			__( 'The default type "%s" must be extended in a subclass.', 'yikes-inc-easy-mailchimp-extender' ),
			$type
		);

		return new static( $message );
	}

	/**
	 * Create a new exception when a name needs to be extended.
	 *
	 * @since %VERSION%
	 *
	 * @param string $name The default name.
	 *
	 * @return static
	 */
	public static function default_name( $name ) {
		$message = sprintf(
			/* translators: %s refers to the default name */
			__( 'The default name "%s" must be extended in a subclass.', 'yikes-inc-easy-mailchimp-extender' ),
			$name
		);

		return new static( $message );
	}
}
