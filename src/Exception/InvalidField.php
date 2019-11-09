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
 * Class InvalidField
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class InvalidField extends \InvalidArgumentException implements Exception {

	/**
	 * Create a new instance of the exception for a field class name that is
	 * not recognized.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field Class name of the service that was not recognized.
	 *
	 * @return static
	 */
	public static function from_field( $field ) {
		$message = sprintf(
			/* translators: %s represents the class name */
			esc_html__( 'The field class "%s" is not recognized and cannot be used.', 'yikes-inc-easy-mailchimp-extender' ),
			is_object( $field )
				? get_class( $field )
				: (string) $field
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception for a field ID that is invalid.
	 *
	 * @since %VERSION%
	 *
	 * @param string $id The invalid field ID.
	 *
	 * @return InvalidField
	 */
	public static function invalid_id( $id ) {
		$message = sprintf(
			/* translators: %s represents the field ID */
			esc_html__( 'The ID "%s" is invalid. The ID must be a simple string, or a single depth array', 'yikes-inc-easy-mailchimp-extender' ),
			$id
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception when a form field is required.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field   The field name.
	 * @param string $context Optional additional context about the field.
	 *
	 * @return static
	 */
	public static function field_required( $field, $context = '' ) {
		$message = sprintf(
			/* translators: %1$s is the field label, %2$s is the optional additional context */
			esc_html__( 'The field %1$s is required. %2$s', 'yikes-inc-easy-mailchimp-extender' ),
			$field,
			$context
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception when the form field value is invalid.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field   The field label.
	 * @param string $context Optional additional context about the field.
	 *
	 * @return InvalidField
	 */
	public static function value_invalid( $field, $context = '' ) {
		$message = sprintf(
			/* translators: %1$s is the field label, %2$s is the optional additional context */
			esc_html__( 'The value submitted for the field %1$s is invalid. %2$s', 'yikes-inc-easy-mailchimp-extender' ),
			$field,
			$context
		);

		return new static( $message );
	}
}
