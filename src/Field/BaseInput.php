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
	 * Field ID.
	 *
	 * @var string
	 */
	private $field_id;

	/**
	 * Field's classes
	 *
	 * @var array
	 */
	private $classes = [];

	/**
	 * Field placeholder.
	 *
	 * @var string
	 */
	private $placeholder;

	/**
	 * Field name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Field value.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * Construct Field
	 *
	 * @param string $id          Fields ID.
	 * @param array  $classes     Field and label classes.
	 * @param string $placeholder Fields placeholder.
	 * @param string $name        Field name.
	 */
	public function __construct( $classes, $placeholder, $label, $value, $description, $merge, $form_id, $hidden ) {
		$this->classes     = $classes;
		$this->placeholder = $placeholder;
		$this->label       = $label;
		$this->value       = $value;
		$this->description = $this->set_description( $description );
		$this->merge       = $merge;
		$this->form_id     = $form_id;
		$this->hidden      = $hidden;
	}

	const TYPE     = 'text';
	const REQUIRED = false;

	/**
	 * Get Field Type
	 *
	 * @return string $field['type']
	 */
	public function get_type() {
		return static::TYPE;
	}

	public function get_placeholder() {
		return $this->placeholder;
	}

	public function field_classes() {
		return $this->classes['field_classes'];
	}

	public function label_classes() {
		if ( true === static::REQUIRED ) {
			$this->classes['label_classes'][] = 'yikes-mailchimp-field-required';
		}
		return $this->classes['label_classes'];
	}

	public function get_name() {
		return $this->merge;
	}

	public function get_id() {
        return 'yikes-easy-mc-form-' . $this->form_id . '-' . $this->merge;
    }

	public function get_value() {
		return $this->value;
	}

	public function set_description( $description ) {
		$this->show_desc   = $description['show_description'];
		$this->desc_above  = $description['description_above'];
		$this->description = $description['description'];
	}

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		?>
		<label for="<?= esc_attr( $this->get_id() ); ?>" class="<?= esc_html( implode( ' ' , $this->label_classes() ) ); ?>" <?= esc_html( implode( ' ' , $this->label['props'] ) ); ?> >

		<!-- dictate label visibility -->
		<?php if ( ! isset( $this->label['hide-label'] ) ) { ?>
			<span class="<?= esc_attr( $this->merge ) . '-label'; ?>">
				<?= esc_html( apply_filters( 'yikes-mailchimp-'. $this->merge .'-label' , esc_attr( $this->label['value'] ), $this->form_id ) ); ?>
			</span>
		<?php }

		if ( $this->show_desc === true && $this->desc_above === true ) :
		?>

        <p class="form-field-description" id="form-field-description-<?= esc_attr( $this->merge ); ?>">
			<?= esc_html( apply_filters( 'yikes-mailchimp-' . $this->merge . '-description', $this->description, $this->form_id ) ); ?>
		</p>

        <?php
        endif;
		?>
		<input type="<?= esc_attr( $this->get_type() ); ?>"
			class="<?= esc_attr( implode( ' ' , $this->field_classes() ) ); ?>"
			name="<?= esc_attr( $this->get_name() ); ?>"
			placeholder="<?= esc_attr( $this->get_placeholder() ); ?>"
			id="<?= esc_attr( $this->get_id() ); ?>"
			value="<?= esc_attr( $this->get_value() ); ?>"
			<?php if ( true === static::REQUIRED ) : ?>
			required="required"
			<?php endif; ?>
			<?php if ( true === $this->hidden ) : ?>
			style="display:none;"
			<?php endif; ?>
		/>
		<?php
		if ( $this->show_desc === true && $this->desc_above === false ) {
			$desc_value = apply_filters( 'yikes-mailchimp-' . $this->merge . '-description', $this->description, $this->form_id );
		?>
            <p class="form-field-description" id="form-field-description-<?= esc_attr( $this->merge ); ?>"><?=  esc_html( $desc_value ); ?></p>
        <?php
		}
		?>
		</label>
		<?php
	}
}
