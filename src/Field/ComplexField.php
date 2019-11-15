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
 * Abstract Class ComplexField
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
abstract class ComplexField extends BaseField {

	/**
	 * The base HTML class value.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $class_base = 'complexfield';

	/**
	 * Array of error messages.
	 *
	 * @since %VERSION%
	 * @var string[]
	 */
	protected $error_messages = [];

	/**
	 * Array of sub-fields.
	 *
	 * @since %VERSION%
	 * @var Field[]
	 */
	protected $sub_fields = [];

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
		parent::__construct( $id, $label, $classes, $required );
		$this->setup_sub_fields();
	}

	/**
	 * Set up the sub fields for this field.
	 *
	 * @since %VERSION%
	 * @throws InvalidField When an invalid field class is provided through the filter.
	 */
	protected function setup_sub_fields() {
		$this->sub_fields = $this->generate_sub_fields();
	}

	/**
	 * Get the ID base for sub-fields.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_id_base() {
		return $this->id;
	}

	/**
	 * Generate the sub-field objects for this field.
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidField When an invalid field class is provided through the filter.
	 */
	protected function generate_sub_fields() {
		$default_classes = $this->get_classes();
		$default_fields  = $this->get_default_fields();
		$id_base         = $this->get_id_base();
		$sub_fields      = [];

		foreach ( $default_fields as $field => $settings ) {
			$settings = wp_parse_args( $settings, [
				'label'    => ucwords( str_replace( [ '_', '-' ], ' ', $field ) ),
				'class'    => Types::TEXT,
				'required' => $this->required,
				'callback' => null,
				'options'  => [],
				'classes'  => [],
			] );
			$classes  = array_merge( $default_classes, $settings['classes'] );

			// Instantiate the sub field, using callback if available.
			if ( isset( $settings['callback'] ) && is_callable( $settings['callback'] ) ) {
				$sub_field = call_user_func_array( $settings['callback'], [
					$id_base,
					$field,
					$classes,
					$settings,
				] );
			} else {
				$sub_field = new $settings['class'](
					"{$id_base}[{$field}]",
					$settings['label'],
					$classes,
					(bool) $settings['required']
				);
			}

			$this->validate_sub_field( $sub_field, $settings['class'] );
			$sub_field->set_parent( $this );
			$sub_fields[ $field ] = $sub_field;
		}

		return $sub_fields;
	}

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		$this->render_container_open();
		$this->render_fieldset_group();
		$this->render_container_close();
	}

	/**
	 * Render an individual fieldset group.
	 *
	 * @since %VERSION%
	 */
	protected function render_fieldset_group() {
		$this->render_open_fieldset();
		$this->render_grouping_label();
		$this->render_sub_fields();
		$this->render_close_fieldset();
	}

	/**
	 * Render the opening of the main content container.
	 *
	 * @since %VERSION%
	 */
	protected function render_container_open() {
		echo '<div class="emf-field-container">';
	}

	/**
	 * Render the opening of a fieldset element.
	 *
	 * @since %VERSION%
	 */
	protected function render_open_fieldset() {
		$classes = [
			'emf-fieldset',
			"emf-fieldset-{$this->class_base}",
		];

		printf( '<fieldset class="%s">', esc_attr( join( ' ', $classes ) ) );
	}

	/**
	 * Render the sub-fields.
	 *
	 * @since %VERSION%
	 */
	protected function render_sub_fields() {
		/** @var Field $sub_field */
		foreach ( $this->sub_fields as $sub_field ) {
			$sub_field->render();
		}
	}

	/**
	 * Render the closing fieldset tag.
	 *
	 * @since %VERSION%
	 */
	protected function render_close_fieldset() {
		echo '</fieldset>';
	}

	/**
	 * Render the closing of the main container.
	 *
	 * @since %VERSION%
	 */
	protected function render_container_close() {
		echo '</div>';
	}

	/**
	 * Get the array of classes to merge in with the default field classes.
	 *
	 * @since %VERSION%
	 * @return array
	 */
	protected function get_classes() {
		return array_merge( $this->classes, [ "emf-field-{$this->class_base}" ] );
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
		// Validate the value as a whole.
		try {
			$this->raw_value = $data;
			$this->validate_raw_value();
		} catch ( InvalidField $e ) {
			$this->error_messages[] = $e->getMessage();
			throw $e;
		}

		// Validate each individual field.
		$values = [];
		foreach ( $this->sub_fields as $name => $field ) {
			try {
				$values[] = $field->set_submission( isset( $data[ $name ] ) ? $data[ $name ] : '' );
			} catch ( InvalidField $e ) {
				$this->error_messages[] = $e->getMessage();
				// todo: Store the exceptions in a chain?
			}
		}

		$this->value = $values;
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
		$values = [];
		foreach ( $this->sub_fields as $field ) {
			$id            = str_replace( [ '[', ']', $this->get_id() ], '', $field->get_id() );
			$values[ $id ] = $field->get_sanitized_value();
		}

		if ( empty( $values ) ) {
			throw InvalidField::value_invalid( $this->get_label() );
		}

		return $values;
	}

	/**
	 * Validate the raw value.
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidField When the raw value is empty but the field is required.
	 */
	protected function validate_raw_value() {
		// We cannot have an empty array.
		if ( empty( $this->raw_value ) ) {
			throw InvalidField::value_invalid( $this->get_label(), 'Empty array received for complex field.' );
		}
	}

	/**
	 * Get the array of default fields.
	 *
	 * This should return a multi-dimensional array of field data which will
	 * be used to construct Field objects.
	 *
	 * @since %VERSION%
	 * @return array
	 */
	abstract protected function get_default_fields();

	/**
	 * Render the grouping label for the sub-fields.
	 *
	 * This should echo the label directly.
	 *
	 * @since %VERSION%
	 */
	abstract protected function render_grouping_label();

	/**
	 * Validate that the object is an instance of the Field interface.
	 *
	 * @since %VERSION%
	 *
	 * @param object $sub_field  The sub-field object.
	 * @param string $from_class The class used to instantiate the field.
	 *
	 * @throws InvalidField When the field is not of the correct type.
	 */
	protected function validate_sub_field( $sub_field, $from_class ) {
		if ( ! ( $sub_field instanceof Field ) ) {
			throw InvalidField::from_field( $from_class );
		}
	}

	/**
	 * Get the type for use with errors.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_error_type() {
		return $this->get_id();
	}
}
