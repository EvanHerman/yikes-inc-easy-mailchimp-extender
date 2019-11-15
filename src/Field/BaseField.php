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

/**
 * Class BaseField
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 *
 * @property Field parent The Parent field object.
 */
abstract class BaseField implements Field {

	/**
	 * The filter for sanitizing.
	 *
	 * Override in child classes to use a different sanitize filter.
	 *
	 * @see http://php.net/manual/en/filter.filters.sanitize.php.
	 */
	const SANITIZE = FILTER_SANITIZE_STRING;

	/**
	 * An error message to display for the field.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $error_message;

	/**
	 * The field ID.
	 *
	 * Used in HTML for id and name tags.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $id;

	/**
	 * The field label.
	 *
	 * Used inside a <label> element.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $label;

	/**
	 * Classes to apply to the field.
	 *
	 * @since %VERSION%
	 * @var array
	 */
	protected $classes;

	/**
	 * Raw value submitted to the field.
	 *
	 * @since %VERSION%
	 * @var mixed
	 */
	protected $raw_value;

	/**
	 * Whether this field is read-only.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	protected $read_only = false;

	/**
	 * Whether the field is required.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	protected $required;

	/**
	 * Data attributes for the field.
	 *
	 * @since %VERSION%
	 * @var array
	 */
	protected $data = [];

	/**
	 * The parent field.
	 *
	 * @since %VERSION%
	 * @var Field
	 */
	protected $parent = null;

	/**
	 * The value for the field.
	 *
	 * @since %VERSION%
	 * @var null
	 */
	protected $value = null;

	/**
	 * The pattern used for matching an field's ID.
	 *
	 * @link  https://regex101.com/r/ZTgsNa/1
	 * @since %VERSION%
	 * @var string
	 */
	protected $id_pattern = '#^([\w-]+)(?:\[(\d+)?\])?(?:\[([\w-]+)\])?#';

	/**
	 * BaseField constructor.
	 *
	 * @param string $id       The field ID.
	 * @param string $label    The field label.
	 * @param array  $classes  Array of field classes.
	 * @param bool   $required Whether the field is required.
	 *
	 * @throws InvalidField When the provided ID is invalid.
	 */
	public function __construct( $id, $label, array $classes, $required = true ) {
		$this->label    = $label;
		$this->classes  = $classes;
		$this->required = (bool) $required;
		$this->set_id( $id );
	}

	/**
	 * Maybe return data from inaccessible members.
	 *
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
	 *
	 * @param string $name The property to retrieve.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'parent':
				if ( ! isset( $this->parent ) ) {
					$this->parent = new NullParent();
				}

				return $this->parent;

			default:
				$message = sprintf( 'Undefined property: %s::$%s', static::class, $name );
				trigger_error( esc_html( $message ), E_USER_NOTICE );

				return null;
		}
	}

	/**
	 * Get the field ID.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the ID for the field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $id The ID of the field.
	 *
	 * @throws InvalidField When the provided ID is invalid.
	 */
	public function set_id( $id ) {
		$this->id = $id;
		$this->validate_id();
	}

	/**
	 * Get whether this field is required or not.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	public function is_required() {
		return $this->required;
	}

	/**
	 * Set the parent field object for this field.
	 *
	 * @since %VERSION%
	 *
	 * @param Field $field The parent field object.
	 */
	public function set_parent( Field $field ) {
		$this->parent = $field;
	}

	/**
	 * Get the parent field object.
	 *
	 * @since %VERSION%
	 * @return Field
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 * Ensure we have a valid ID for the field.
	 *
	 * An ID is valid when it is a single word, or when it contains a single-depth array.
	 * Examples of valid IDs:
	 *
	 * foo
	 * foo[bar]
	 * foo_bar_baz
	 * foo-bar-baz
	 *
	 * Examples of invalid IDs:
	 *
	 * foo bar baz
	 * foo[bar][baz]
	 * foo[bar[baz]]
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidField When the provided ID is invalid for a form field.
	 */
	protected function validate_id() {
		// Make sure we match the pattern as a whole.
		if ( ! preg_match( $this->id_pattern, $this->id, $matches ) ) {
			throw InvalidField::invalid_id( $this->id );
		}

		// Make sure we matched the entire ID string.
		if ( $matches[0] !== $this->id ) {
			throw InvalidField::invalid_id( $this->id );
		}
	}

	/**
	 * Add a data attribute to the field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $key   The data key. Should NOT include data- prefix.
	 * @param string $value The data value.
	 */
	public function add_data( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Render any data attributes for this field.
	 *
	 * @since %VERSION%
	 */
	protected function render_data_attributes() {
		if ( empty( $this->data ) ) {
			return;
		}

		$pieces = [];
		foreach ( $this->data as $key => $datum ) {
			$key      = strtolower( str_replace( [ '_', ' ' ], '-', $key ) );
			$pieces[] = sprintf( 'data-%s="%s"', esc_html( $key ), esc_attr( $datum ) );
		}

		echo join( ' ', $pieces ), ' '; // XSS ok.
	}

	/**
	 * Render the required attribute.
	 *
	 * @since %VERSION%
	 */
	protected function render_required() {
		if ( $this->required ) {
			echo 'required="required" ';
		}
	}

	/**
	 * Set the data submitted to the field.
	 *
	 * @since %VERSION%
	 *
	 * @param mixed $data The submitted data for the field.
	 *
	 * @throws InvalidField When the field submission is invalid.
	 */
	public function set_submission( $data ) {
		try {
			$this->raw_value = $data;
			$this->validate_raw_value();
			$this->value = $this->get_sanitized_value();
		} catch ( InvalidField $e ) {
			$this->error_message = $e->getMessage();
			throw $e;
		}
	}

	/**
	 * Validate the submission for the given field.
	 *
	 * @since %VERSION%
	 *
	 * @return mixed The validated value.
	 * @throws InvalidField When the submission isn't valid.
	 */
	public function get_sanitized_value() {
		$filtered = $this->sanitize_value( $this->raw_value );
		if ( false === $filtered || ( empty( $filtered ) && $this->required ) ) {
			throw InvalidField::value_invalid( $this->get_label() );
		}

		return $filtered;
	}

	/**
	 * Validate the raw value.
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidField When the raw value is empty but the field is required.
	 */
	protected function validate_raw_value() {
		if ( empty( $this->raw_value ) && $this->required ) {
			throw InvalidField::field_required( $this->get_label() );
		}
	}

	/**
	 * Sanitize a submitted value.
	 *
	 * @since %VERSION%
	 *
	 * @param string $raw The raw value for the field.
	 *
	 * @return mixed
	 */
	protected function sanitize_value( $raw ) {
		return filter_var( $raw, static::SANITIZE, $this->get_filter_options() );
	}

	/**
	 * Return options to use when sanitizing a submitted value.
	 *
	 * @link  http://php.net/manual/en/function.filter-var.php
	 * @see   filter_var()
	 * @since %VERSION%
	 * @return null|callable|int|array Return null for no options, a callable, an int when using filter flags, or an
	 *                                 array when using additional options for the filter.
	 */
	protected function get_filter_options() {
		return null;
	}

	/**
	 * Determine if this is a child field.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	public function is_child() {
		return ( null !== $this->parent && ! ( $this->parent instanceof NullParent ) );
	}

	/**
	 * Render the error message for the field.
	 *
	 * @since %VERSION%
	 */
	protected function render_error_message() {
		if ( empty( $this->error_message ) ) {
			return;
		}

		printf(
			'<span class="error-text error-%1$s">%2$s</span>',
			esc_attr( $this->get_error_type() ),
			esc_html( $this->error_message )
		);
	}

	/**
	 * Render the label for the field.
	 *
	 * @since %VERSION%
	 */
	protected function render_label() {
		echo esc_html( $this->get_label() );
	}

	/**
	 * Render any additional attributes.
	 *
	 * @since %VERSION%
	 */
	protected function render_extra_attributes() {
		$this->render_required();
		$this->render_data_attributes();
	}

	/**
	 * Get the label for the field.
	 *
	 * @since %VERSION%
	 * @return string The label for the field.
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the type for use with errors.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	abstract protected function get_error_type();
}
