<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Form;

use YIKES\EasyForms\Exception\InvalidClass;
use YIKES\EasyForms\Exception\InvalidField;
use YIKES\EasyForms\Exception\InvalidRecaptcha;
use YIKES\EasyForms\Field\Field;
use YIKES\EasyForms\Field\Hidden;
use YIKES\EasyForms\Field\Types;
use YIKES\EasyForms\Model\OptinForm as EasyFormsModel;
use YIKES\EasyForms\Recaptcha\Recaptcha;

/**
 * Class OptinForm
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 *
 * @property Field[] fields        The array of field objects.
 * @property array   field_classes The array of classes used for field objects.
 * @property array   form_classes  The array of classes used for the main form element.
 */
final class OptinForm {

	/**
	 * The Optin Form object.
	 *
	 * @since %VERSION%
	 * @var EasyFormsModel
	 */
	private $form_data;

	/**
	 * If recaptcha is enabled this will be our box.
	 */
	private $recaptcha = null;

	/**
	 * Field Count
	 */
	private $field_count = 0;

	/**
	 * Hidden Label Count
	 */
	private $hidden_label_count = 0;

	/**
	 * Whether the form has any errors.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	private $has_errors = false;

	/**
	 * Whether the form has been submitted.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	private $is_submitted = false;

	/**
	 * The ID of the form.
	 *
	 * @since %VERSION%
	 * @var int
	 */
	private $form_id = 0;

	/**
	 * The data submitted with this form.
	 *
	 * @since %VERSION%
	 * @var array
	 */
	private $submitted_data = [];

	/**
	 * The validated data for this form.
	 *
	 * @since %VERSION%
	 * @var array
	 */
	private $valid_data = [];

	/**
	 * OptinForm constructor.
	 *
	 * @param int            $form_id     The ID the optin form is for.
	 * @param EasyFormsModel $form_data   The optin form object.
	 */
	public function __construct( $form_id, EasyFormsModel $form_data, FormOptions $form_options ) {
		$this->form_id     = $form_id;
		$this->form_data   = $form_data;
		$this->field_count = $this->set_field_count();
		try {
			$this->recaptcha = new Recaptcha( $form_id, $form_options );
		} catch ( InvalidRecaptcha $e ) {}
	}

	/**
	 * Utilized for reading data from inaccessible members.
	 *
	 * @param string $name The property to retrieve.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'fields':
				$this->create_fields();
				return $this->fields;

			default:
				$message = sprintf( 'Undefined property: %s::$%s', static::class, $name );
				trigger_error( esc_html( $message ), E_USER_NOTICE );

				return null;
		}
	}

	private function reduce_field_count() {
		$this->field_count = $this->field_count --;
	}

	public function set_field_count() {
		return (int) count( $this->form_data['fields'] );
	}

	public function form_classes() {
		return $this->form_data['settings']['yikes-easy-mc-form-class-names'];
	}

	public function inline_form() {
		return $this->form_data['form_settings']['yikes-easy-mc-inline-form'];
	}

	public function inline_form_override() {
		return isset( $this->has_recaptcha ) || ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'eu-opt-in-compliance-for-mailchimp/yikes-inc-easy-mailchimp-eu-law-compliance-extension.php' ) );
	}

	public function submit_button_props() {
		return [
			'type' => $this->form_data['form_settings']['yikes-easy-mc-submit-button-type'],
			'text' => esc_attr( $this->form_data['form_settings']['yikes-easy-mc-submit-button-text'] ),
			'image' => esc_url( $this->form_data['form_settings']['yikes-easy-mc-submit-button-image'] ),
			'classes' => esc_attr( $this->form_data['form_settings']['yikes-easy-mc-submit-button-classes'] ),
		];
	}

	public function submit_button() {
		$button_props = $this->submit_button_props();
		$submit_button = '';

		if ( $this->form_inline && ! $this->inline_form_override ) {

			$submit_button_label_classes = [ 'empty-label' ];

			// If the number of fields, is equal to the hidden label count, add our class
			// eg: All field labels are set to hidden.
			if ( absint( $this->field_count ) === absint( $this->hidden_label_count ) ) {
				$submit_button_label_classes[] = 'labels-hidden';
			}
			$submit_button .= '<label class="empty-form-inline-label submit-button-inline-label"><span class="' . implode( ' ', $submit_button_label_classes ) . '">&nbsp;</span>';
		}
		// Display the image or text based button.
		if ( $button_props['type'] === 'text' ) {
			$submit_button .= apply_filters( 'yikes-mailchimp-form-submit-button', '<button type="submit" class="' . apply_filters( 'yikes-mailchimp-form-submit-button-classes', 'yikes-easy-mc-submit-button yikes-easy-mc-submit-button-' . esc_attr( $this->form_data['id'] ) . ' btn btn-primary' . $submit_button_classes . $admin_class, $this->form_data['id'] ) . '"> <span class="yikes-mailchimp-submit-button-span-text">' .  apply_filters( 'yikes-mailchimp-form-submit-button-text', esc_attr( stripslashes( $this->submit ) ), $this->form_data['id'] ) . '</span></button>', $this->form_data['id'] );
		} else {
			$submit_button .= apply_filters( 'yikes-mailchimp-form-submit-button', '<input type="image" alt="' . apply_filters( 'yikes-mailchimp-form-submit-button-text', esc_attr( stripslashes( $this->submit ) ), $this->form_data['id'] ) . '" src="' . $submit_button_image . '" class="' . apply_filters( 'yikes-mailchimp-form-submit-button-classes', 'yikes-easy-mc-submit-button yikes-easy-mc-submit-button-image yikes-easy-mc-submit-button-' . esc_attr( $form_data['id'] ) . ' btn btn-primary' . $submit_button_classes . $admin_class, $form_data['id'] ) . '">', $form_data['id'] );
		}
		if ( $this->form_inline && ! $this->inline_form_override ) {
			$submit_button .= '</label>';
		}

		echo $submit_button;
	}

	public function edit_form_link() {
		if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
			$edit_form_link = '<span class="edit-link">';
			$edit_form_link .= '<a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $atts['form'] ) ) . '" title="' . __( 'Edit' , 'yikes-inc-easy-mailchimp-extender' ) . ' ' . ucwords( $form_data['form_name'] ) . '">' . __( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
			$edit_form_link .= '</span>';
			$edit_form_link = apply_filters( 'yikes-mailchimp-front-end-form-action-links', $edit_form_link, $atts['form'], ucwords( $form_data['form_name'] ) );
		} else {
			$edit_form_link = '';
		}
		return $edit_form_link;
	}

	/**
	 * Create the array of fields.
	 *
	 * @since %VERSION%
	 */
	private function create_fields() {
		$fields = [];

		// Manually add the hidden nonce and referrer fields.
		$fields[] = new Hidden( 'lpf_nonce', wp_create_nonce( 'lpf_application_submit' ) );
		$fields[] = new Hidden( '_wp_http_referer', wp_unslash( $_SERVER['REQUEST_URI'] ) );

		// Manually add the hidden Job ID field.
		$fields[] = new Hidden( 'job_id', $this->job_id );

		// Add all of the active fields.
		foreach ( $this->form_data['fields'] as $field ) {
			if ( isset( $field['hide'] ) && (string) $field['hide'] === '1' ) {
				$this->reduce_field_count();
			}
			$fields = array_merge( $fields, $this->instantiate_field( $field ) );
		}

		$this->fields = $fields;
	}

	/**
	 * Get the label for the form field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field The field name.
	 *
	 * @return string
	 */
	private function get_field_label( $field ) {
		$field_label = ucwords( str_replace( [ '-', '_' ], ' ', $field ) );

		/**
		 * Filter the label for the form field.
		 *
		 * @param string   $field_label The field label.
		 * @param string   $field       The field name.
		 * @param AppModel $application The application object.
		 */
		return apply_filters( 'lpf_application_form_field_label', $field_label, $field, $this->application );
	}

	/**
	 * Get the class type for a particular field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field The field name.
	 *
	 * @return string The class name to instantiate that field.
	 * @throws InvalidClass When a field type is returned to the filter that doesn't implement Field.
	 */
	private function get_field_type( $field ) {
		$type = array_key_exists( $field, Meta::FIELD_MAP ) ? Meta::FIELD_MAP[ $field ] : Types::TEXT;

		/**
		 * Filter the class used to instantiate the field.
		 *
		 * @param string $type  The field class name. Must extend implment the Field interface.
		 * @param string $field The field name.
		 */
		$type = apply_filters( 'lpf_application_form_field_type', $type, $field );

		// Ensure that the field implements the Field interface..
		$implements = class_implements( $type );
		if ( ! isset( $implements[ Field::class ] ) ) {
			throw InvalidClass::from_interface( $type, Field::class );
		}

		return $type;
	}

	/**
	 * Instantiate a field.
	 *
	 * @since %VERSION%
	 *
	 * @param string $field The raw field name.
	 *
	 * @return Field[] Array of Field objects.
	 */
	private function instantiate_field( $field ) {
		/**
		 * Short-circuit the instantiation of a field object.
		 *
		 * To effectively short-circuit normal instantiation, an array of Field objects must be returned.
		 *
		 * @param array|null $pre         Array of Field objects or null.
		 * @param string     $field       The raw field name.
		 * @param AppModel   $application The application object.
		 */
		$pre = apply_filters( 'lpf_application_instantiate_field', null, $field, $this->application );
		if ( is_array( $pre ) ) {
			foreach ( $pre as $object ) {
				$this->validate_is_field( $object );
			}

			return $pre;
		}

		$field_name  = $this->form_prefix( $field );
		$field_label = $this->get_field_label( $field );
		$type        = $this->get_field_type( $field );

		return [
			new $type(
				$field_name,
				$field_label,
				$this->field_classes,
				$this->application->is_required( $field )
			),
		];
	}

	/**
	 * Render the form fields.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		foreach ( $this->fields as $field ) {
			$field->render();
		}
	}

	/**
	 * Set the submission data.
	 *
	 * @since %VERSION%
	 *
	 * @param array $data Submitted data.
	 */
	public function set_submission( array $data ) {
		$this->is_submitted   = true;
		$this->submitted_data = $data;
	}

	/**
	 * Determine whether the form has errors.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	public function has_errors() {
		return $this->is_submitted && $this->has_errors;
	}

	/**
	 * Validate the submission.
	 *
	 * @since %VERSION%
	 */
	public function validate_submission() {
		$valid = [];
		foreach ( $this->fields as $field ) {
			try {
				$submitted = array_key_exists( $field->get_id(), $this->submitted_data )
					? $this->submitted_data[ $field->get_id() ]
					: '';

				$field->set_submission( $submitted );
				$valid[ $field->get_id() ] = $field->get_sanitized_value();
			} catch ( InvalidField $e ) {
				$this->has_errors = true;
			}
		}

		$this->valid_data = $valid;
	}

	/**
	 * Validate that the given object is a Field.
	 *
	 * @since %VERSION%
	 *
	 * @param object $maybe_field The object to validate.
	 *
	 * @throws InvalidClass When the object isn't a Field object.
	 */
	private function validate_is_field( $maybe_field ) {
		if ( ! $maybe_field instanceof Field ) {
			throw InvalidClass::from_interface( get_class( $maybe_field ), Field::class );
		}
	}
}