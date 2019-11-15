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
use YIKES\EasyForms\Model\ApplicantMetaDropdowns;

/**
 * Class Skills
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Skills extends RepeatableField {

	use ApplicantMetaDropdowns;

	/** @var string */
	protected $class_base = 'skills';

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
		return apply_filters( 'emf_field_skills_fields', [
			ApplicantMeta::SKILL       => [
				'label' => esc_html__( 'Skill', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::PROFICIENCY => [
				'label'    => esc_html__( 'Proficiency', 'yikes-inc-easy-mailchimp-forms' ),
				'callback' => $this->get_skills_callback(),
				'options'  => $this->get_skills_options(),
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
			'<legend class="emf-field-skills emf-input-label">%s</legend>',
			esc_html__( 'Skills:', 'yikes-inc-easy-mailchimp-forms' )
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
			'<div class="emf-field-skills emf-fieldset-label">%1$s <span class="emf-fieldset-number">%2$s</span></div>',
			esc_html__( 'Skill', 'yikes-inc-easy-mailchimp-forms' ),
			esc_html__( '1', 'yikes-inc-easy-mailchimp-forms' )
		);
	}

	/**
	 * Get a callback for generating a new Schooling field.
	 *
	 * @since %VERSION%
	 * @return \Closure
	 */
	private function get_skills_callback() {
		return function( $id_base, $field, $classes, $settings ) {
			$options = [];
			foreach ( $settings['options'] as $value => $label ) {
				$options[] = new SelectOption( $label, $value );
			}

			return new Select(
				"{$id_base}[{$field}]",
				$settings['label'],
				$classes,
				(bool) $settings['required'],
				$options
			);
		};
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
		return esc_attr_x( 'Skill', 'for "add new" button', 'yikes-inc-easy-mailchimp-forms' );
	}
}
