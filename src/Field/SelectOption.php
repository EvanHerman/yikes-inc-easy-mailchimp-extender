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
final class SelectOption implements OptionInterface {

	/** @var string */
	private $label;

	/** @var string */
	private $value;

	/**
	 * SelectOption constructor.
	 *
	 * @param string $label The human-friendly label for the option.
	 * @param string $value The value to store when the option is selected.
	 */
	public function __construct( $label, $value ) {
		$this->label = $label;
		$this->value = $value;
	}

	/**
	 * Render the current option.
	 *
	 * @since %VERSION%
	 *
	 * @param string $selected_value The currently selected value.
	 */
	public function render( $selected_value ) {
		printf(
			'<option value="%1$s" %3$s>%2$s</option>',
			esc_attr( $this->value ),
			esc_html( $this->label ),
			selected( $selected_value, $this->value, false )
		);
	}

	/**
	 * Get the value for the option.
	 *
	 * @since %VERSION%
	 * @return string The option value.
	 */
	public function get_value() {
		return $this->value;
	}
}
