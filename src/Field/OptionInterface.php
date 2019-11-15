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
 * Class SelectOption
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
interface OptionInterface {

	/**
	 * Render the current option.
	 *
	 * @since %VERSION%
	 *
	 * @param string $selected_value The currently selected value.
	 */
	public function render( $selected_value );

	/**
	 * Get the value for the option.
	 *
	 * @since %VERSION%
	 * @return string The option value.
	 */
	public function get_value();
}
