<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

use YIKES\EasyForms\Exception\MustExtend;

/**
 * Class BaseInput
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class BaseInput extends BaseField {

	/**
	 * The Input type.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input
	 */
	const TYPE = '_basefield_';

	/**
	 * Get the type of field.
	 *
	 * @since %VERSION%
	 * @return string
	 * @throws MustExtend When the type is not defined.
	 */
	protected function get_type() {
		if ( self::TYPE === static::TYPE ) {
			throw MustExtend::default_type( self::TYPE );
		}

		return static::TYPE;
	}

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 * @throws MustExtend When the TYPE constant is not properly defined.
	 */
	public function render() {
		$type      = $this->get_type();
		$classes   = array_merge( $this->classes, [ "emf-field-{$type}" ] );
		$has_error = ! empty( $this->error_message );
		?>
		<div class="emf-field-container">
			<label class="emf-input-label emf-input-label-<?php echo esc_attr( $type ); ?><?php echo $has_error ? 'error-prompt' : ''; ?>" for="<?php echo esc_attr( $this->id ); ?>">
				<?php $this->render_label(); ?>
			</label>
			<input type="<?php echo esc_attr( $type ); ?>"
				class="<?php echo esc_attr( join( ' ', $classes ) ); ?>"
				name="<?php echo esc_attr( $this->id ); ?>"
				id="<?php echo esc_attr( $this->id ); ?>"
				value="<?php echo esc_attr( $this->value ); ?>"
				<?php $this->render_extra_attributes(); ?>
			/>
			<?php $this->render_error_message(); ?>
		</div>
		<?php
	}

	/**
	 * Get the type for use with errors.
	 *
	 * @since %VERSION%
	 * @return string
	 * @throws MustExtend When the type hasn't been defined correctly.
	 */
	protected function get_error_type() {
		return $this->get_type();
	}
}
