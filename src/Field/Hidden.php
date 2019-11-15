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
 * Class Hidden
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Hidden extends BaseField {

	/**
	 * The value of the hidden field.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $value;

	/**
	 * Whether this field is read-only.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	protected $read_only = true;

	/**
	 * Hidden constructor.
	 *
	 * @param string $id      The ID for the field.
	 * @param string $value   The value for the field.
	 * @param array  $classes Array of classes to apply to the field.
	 */
	public function __construct( $id, $value, array $classes = [] ) {
		parent::__construct( $id, '', $classes, true );
		$this->value = $value;
	}

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		$classes = array_merge( $this->classes, [ 'emf-field-hidden' ] );
		?>
		<input type="hidden"
			   class="<?php echo esc_attr( join( ' ', $classes ) ); ?>"
			   name="<?php echo esc_attr( $this->id ); ?>"
			   id="<?php echo esc_attr( $this->id ); ?>"
			   value="<?php echo esc_attr( $this->value ); ?>"
			<?php $this->render_data_attributes(); ?>
		/>
		<?php
	}

	/**
	 * Validate the raw value.
	 *
	 * This validates by type-casting the values to strings.
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidField When the raw value is different from the provided value, or empty.
	 */
	protected function validate_raw_value() {
		if ( (string) $this->value !== (string) $this->raw_value ) {
			throw InvalidField::value_invalid(
				static::class,
				__( 'Hidden field values cannot be changed.', 'yikes-inc-easy-mailchimp-forms' )
			);
		}
	}

	/**
	 * Render the error message for the field.
	 *
	 * @since %VERSION%
	 */
	protected function render_error_message() {
		// Don't do anything.
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
