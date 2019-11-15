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
 * Class Volunteer
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Volunteer extends RepeatableField {

	/** @var string */
	protected $class_base = 'volunteer';

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
		 * Filter the default volunteer fields.
		 *
		 * @param array $fields Array of fields for Volunteer data.
		 */
		return apply_filters( 'emf_field_volunteer_fields', [
			ApplicantMeta::ORGANIZATION     => [
				'label' => esc_html__( 'Organization', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::INDUSTRY         => [
				'label' => esc_html__( 'Industry', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::START_DATE       => [
				'label'   => esc_html__( 'Start Date', 'yikes-inc-easy-mailchimp-forms' ),
				'class'   => Types::DATE,
				'classes' => [ 'emf-datepicker' ],
			],
			ApplicantMeta::PRESENT_POSITION => [
				'label'    => esc_html__( 'Presently Volunteering', 'yikes-inc-easy-mailchimp-forms' ),
				'class'    => Types::CHECKBOX,
				'required' => false,
			],
			ApplicantMeta::END_DATE         => [
				'label'    => esc_html__( 'End Date', 'yikes-inc-easy-mailchimp-forms' ),
				'class'    => Types::DATE,
				'required' => false,
				'classes'  => [ 'emf-datepicker' ],
			],
			ApplicantMeta::POSITION         => [
				'label' => esc_html__( 'Position', 'yikes-inc-easy-mailchimp-forms' ),
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
			'<legend class="emf-field-volunteer emf-input-label">%s</legend>',
			esc_html__( 'Volunteer:', 'yikes-inc-easy-mailchimp-forms' )
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
			'<div class="emf-field-volunteer emf-fieldset-label">%1$s <span class="emf-fieldset-number">%2$s</span></div>',
			esc_html__( 'Volunteer', 'yikes-inc-easy-mailchimp-forms' ),
			esc_html__( '1', 'yikes-inc-easy-mailchimp-forms' )
		);
	}

	/**
	 * Get the label to use when rendering the "Add New" button.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_add_new_label() {
		return esc_html_x( 'Volunteer Position', 'for "add new" button', 'yikes-inc-easy-mailchimp-forms' );
	}
}
