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
use YIKES\EasyForms\Field\Field;
use YIKES\EasyForms\Field\Hidden;
use YIKES\EasyForms\Field\Types;
use YIKES\EasyForms\Renderable;
use YIKES\EasyForms\Assets\AssetsAware;
use YIKES\EasyForms\Assets\AssetsAwareness;
use YIKES\EasyForms\Assets\ScriptAsset;
use YIKES\EasyForms\Service;
use YIKES\EasyForms\Model\OptinForm as EasyFormsModel;
use YIKES\EasyForms\Model\Recaptcha as RecaptchaModel;
use YIKES\EasyForms\Model\OptinMeta as Meta;

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

	use FormHelper;
	use FieldBuilder;
	use SubmitButton;
	/**
	 * The Optin Form object.
	 *
	 * @since %VERSION%
	 * @var EasyFormsModel
	 */
	private $form_data;

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
	 * Admin CSS Class
	 *
	 * @since %VERSION%
	 * @var string
	 */
	private $admin_class = '';

	public $recaptcha;

	public $form_inline = false;

	/**
	 * OptinForm constructor.
	 *
	 * @param int            $form_id     The ID the optin form is for.
	 * @param EasyFormsModel $form_data   The optin form object.
	 */
	public function __construct( $form_id = 0, $form_data = array(), $attr = array() ) {
		$this->form_id           = $form_id;
		$this->form_data         = $form_data;
		$this->field_count       = $this->set_field_count();
		$this->form_inline       = $form_data['form_settings']['yikes-easy-mc-inline-form'];
		$this->recaptcha         = ( new RecaptchaModel() )->setup( $attr );
	}

	/**
	 * Admin CSS Class
	 *
	 * @return string
	 */
	private function admin_class() {
		$is_admin = is_user_logged_in() && current_user_can(
			apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' )
		);
		return $is_admin ? ' admin-logged-in' : '';
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

	/**
	 * Create the array of fields.
	 *
	 * @since %VERSION%
	 */
	private function create_fields() {
		$fields = [];

		// // Manually add the hidden nonce and referrer fields.
		// $fields[] = new Hidden( "yikes_easy_mc_new_subscriber", wp_create_nonce( 'yikes_easy_mc_form_submit' ), $this->form_id );
		// $fields[] = new Hidden( '_wp_http_referer', wp_unslash( $_SERVER['REQUEST_URI'] ), $this->form_id );

		// // Honeypot Trap field.
		// $fields[] = new Hidden( 'yikes-mailchimp-honeypot', $this->form_data['list_id'], $this->form_id );

		// // List ID field.
		// $fields[] = new Hidden( 'yikes-mailchimp-associated-list-id', $this->form_data['list_id'], $this->form_id );

		// // The form that is being submitted! Used to display error/success messages above the correct form.
		// $fields[] = new Hidden( 'yikes-mailchimp-submitted-form', $this->form_id, $this->form_id );

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
	 * Render the form fields.
	 *
	 * @since %VERSION%
	 */
	public function render( array $context = [] ) {
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
	 * Get the class type for a particular field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field The field name.
	 *
	 * @return string The class name to instantiate that field.
	 * @throws InvalidClass When a field type is returned to the filter that doesn't implement Field.
	 */
	private function get_field_type( $field ) {

		$type = Types::TEXT;

		/**
		 * Filter the class used to instantiate the field.
		 *
		 * @param string $type  The field class name. Must extend implment the Field interface.
		 * @param string $field The field name.
		 */
		$type = apply_filters( 'easy_forms_field_type', $type, $field );

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
	 * @since %SINCE%
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
		 * @param array|null     $pre         Array of Field objects or null.
		 * @param string         $field       The raw field name.
		 * @param EasyFormsModel $form_data The form object.
		 */
		$pre = apply_filters( 'yikes_easy_forms_instantiate_field', null, $field, $this->form_data );
		if ( is_array( $pre ) ) {
			foreach ( $pre as $object ) {
				$this->validate_is_field( $object );
			}
			return $pre;
		}

		$label       = $this->get_label( $field );
		$value       = $this->get_value( $field );
		$type        = $this->get_field_type( $field );
		$classes     = $this->get_field_classes( $field );
		$placeholder = $this->get_placeholder( $field );
		$description = $this->get_description( $field );
		$merge       = $field['merge'];
		$hidden      = $this->get_hidden( $field );
		return [
			new $type(
				$classes,
				$placeholder,
				$label,
				$value,
				$description,
				$merge,
				$this->form_id,
				$hidden
			),
		];
	}

	/**
	 * Validate that the given object is a Field.
	 *
	 * @since %SINCE%
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
