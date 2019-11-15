<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

/**
 * Class NullParent
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class NullParent extends BaseField {

	/**
	 * NullParent constructor.
	 */
	public function __construct() {
		$this->required = false;
	}

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		// nothing to do here.
	}

	/**
	 * Ensure we have a valid ID for the field.
	 */
	protected function validate_id() {
		return true;
	}

	/**
	 * Validate the submission for the given field.
	 *
	 * @since %VERSION%
	 *
	 * @return null A null placeholder.
	 */
	public function get_sanitized_value() {
		return null;
	}

	/**
	 * Get the type for use with errors.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_error_type() {
		return '';
	}
}
