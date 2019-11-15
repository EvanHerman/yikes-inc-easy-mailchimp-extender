<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

use YIKES\EasyForms\Model\ApplicantMeta;

/**
 * Class Certifications
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Certifications extends RepeatableField {

	/** @var string */
	protected $class_base = 'certifications';

	/**
	 * Get the array of default fields.
	 *
	 * This should return a multi-dimensional array of field data which will
	 * be used to construct Field objects.
	 *
	 * @since %VERSION%
	 * @return array
	 */
	protected function get_default_fields() {
		/**
		 * Filter the default certification fields.
		 *
		 * @param array $fields Array of certification fields.
		 */
		return apply_filters( 'emf_field_certification_fields', [
			ApplicantMeta::INSTITUTION => [
				'label' => esc_html__( 'Institution', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::TYPE        => [
				'label' => esc_html__( 'Institution Type', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::YEAR        => [
				'label' => esc_html__( 'Year Certified', 'yikes-inc-easy-mailchimp-forms' ),
				'class' => Types::YEAR,
			],
			ApplicantMeta::CERT_TYPE   => [
				'label' => esc_html__( 'Certification Type', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::STATUS      => [
				'label' => esc_html__( 'Status', 'yikes-inc-easy-mailchimp-forms' ),
			],
		] );
	}

	/**
	 * Render the grouping label for the sub-fields.
	 *
	 * This should echo the label directly.
	 *
	 * @since %VERSION%
	 */
	protected function render_grouping_label() {
		printf(
			'<legend class="emf-field-certifications emf-input-label">%s</legend>',
			esc_html__( 'Certifications:', 'yikes-inc-easy-mailchimp-forms' )
		);
	}

	/**
	 * Render the label for the repeatable fields.
	 *
	 * This should echo the label directly.
	 *
	 * @since %VERSION%
	 */
	protected function render_repeatable_field_label() {
		printf(
			'<div class="emf-field-certifications emf-fieldset-label">%1$s <span class="emf-fieldset-number">%2$s</span></div>',
			esc_html__( 'Certification', 'yikes-inc-easy-mailchimp-forms' ),
			esc_html__( '1', 'yikes-inc-easy-mailchimp-forms' )
		);
	}

	/**
	 * Get the label to use when rendering the "Add New" button.
	 *
	 * Only needs to be overridden when the field is repeatable.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_add_new_label() {
		return esc_html_x( 'Certification', 'for "add new" button', 'yikes-inc-easy-mailchimp-forms' );
	}
}
