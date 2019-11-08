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
 * Class InvalidURI.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Exception
 * @author  Freddie Mixell
 */
class InvalidURI extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a file that is not accessible
	 * or not readable.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri URI of the file that is not accessible or not
	 *                    readable.
	 *
	 * @return static
	 */
	public static function from_uri( $uri ) {
		$message = sprintf(
			'The View URI "%s" is not accessible or readable.',
			$uri
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception for a file that is not in the list.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri  The invalid URI.
	 * @param array  $list The list of valid URIs.
	 *
	 * @return InvalidURI
	 */
	public static function from_list( $uri, array $list ) {
		$message = sprintf(
			'The View URI "%1$s" is not one of the valid options: [%2$s]',
			$uri,
			join( ', ', $list )
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception for a path that is invalid.
	 *
	 * @since %VERSION%
	 *
	 * @param string $path The path that is invalid.
	 *
	 * @return static
	 */
	public static function from_asset_path( $path ) {
		$message = sprintf(
			'The path "%s" is not readable. Do you need to run gulp?',
			$path
		);

		return new static( $message );
	}
}
