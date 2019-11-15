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
 * Class Address
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Address extends ComplexField {

	/** @var string */
	protected $class_base = 'address';

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
		 * Filter the default address fields.
		 *
		 * @param array $fields Array of address fields.
		 */
		return apply_filters( 'emf_field_address_fields', [
			ApplicantMeta::LINE_1  => [
				'label' => esc_html__( 'Line 1', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::LINE_2  => [
				'label'    => esc_html__( 'Line 2', 'yikes-inc-easy-mailchimp-forms' ),
				'required' => false,
			],
			ApplicantMeta::CITY    => [
				'label' => esc_html__( 'City', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::STATE   => [
				'label' => esc_html__( 'State', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::COUNTRY => [
				'label' => esc_html__( 'Country', 'yikes-inc-easy-mailchimp-forms' ),
			],
			ApplicantMeta::ZIP     => [
				'label' => esc_html__( 'Postal Code', 'yikes-inc-easy-mailchimp-forms' ),
				'class' => Types::POSTAL_CODE,
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
			'<legend class="emf-field-address emf-input-label">%s</legend>',
			esc_html__( 'Address: ', 'yikes-inc-easy-mailchimp-forms' )
		);
	}
}
