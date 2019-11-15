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
 * Class Textarea
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Textarea extends BaseField {

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		$classes = array_merge( $this->classes, [ 'emf-field-textarea' ] );
		?>
		<div class="emf-field-container">
			<label class="emf-input-label"><?php $this->render_label(); ?></label>
			<textarea name="<?php echo esc_attr( $this->id ); ?>"
					  id="<?php echo esc_attr( $this->id ); ?>"
					  class="<?php esc_attr( join( ' ', $classes ) ); ?>"
					  rows="10"
					<?php $this->render_required(); ?>
				<?php $this->render_data_attributes(); ?>
				><?php echo esc_textarea( $this->value ); ?></textarea>
		</div>
		<?php
	}

	/**
	 * Get the type for use with errors.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_error_type() {
		return 'textarea';
	}
}
