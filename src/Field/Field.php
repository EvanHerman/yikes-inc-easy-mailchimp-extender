<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

use YIKES\EasyForms\Exception\InvalidField;

interface Field {

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render();

	/**
	 * Set the data submitted to the field.
	 *
	 * @since %VERSION%
	 *
	 * @param mixed $data The submitted data for the field.
	 */
	public function set_submission( $data );

	/**
	 * Get the ID of the field.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	public function get_id();

	/**
	 * Set the ID for the field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $id The ID of the field.
	 *
	 * @throws InvalidField When the provided ID is invalid.
	 */
	public function set_id( $id );

	/**
	 * Get the sanitized value for the field.
	 *
	 * @since %VERSION%
	 *
	 * @return mixed The validated value.
	 * @throws InvalidField When the submission isn't valid.
	 */
	public function get_sanitized_value();

	/**
	 * Set the parent field object for this field.
	 *
	 * @since %VERSION%
	 *
	 * @param Field $field The parent field object.
	 */
	public function set_parent( Field $field );

	/**
	 * Determine if this is a child field.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	public function is_child();

	/**
	 * Get whether this field is required or not.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	public function is_required();

	/**
	 * Get the label for the field.
	 *
	 * @since %VERSION%
	 * @return string The label for the field.
	 */
	public function get_label();
}
