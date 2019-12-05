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
	public function __construct( $form_id, $form_data, $form_options ) {
		$this->form_id      = $form_id;
		$this->form_data    = $form_data;
		$this->field_count  = $this->set_field_count();
		$this->form_options = $form_options;
		$this->form_inline  = $form_data['form_settings']['yikes-easy-mc-inline-form'];
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
	 * Admin CSS Class
	 *
	 * @return string $admin_class Class to style if you want the admin to have a different look.
	 */
	private function admin_class() {
		$is_admin = is_user_logged_in() && current_user_can(
			apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' )
		);
		return $is_admin ? ' admin-logged-in' : '';
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
}