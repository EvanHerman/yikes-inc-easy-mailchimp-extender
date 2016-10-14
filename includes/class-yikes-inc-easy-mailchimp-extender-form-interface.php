<?php

/**
 * Interface for dealing with our forms.
 */
interface Yikes_Inc_Easy_MailChimp_Extender_Form_Interface {

	/**
	 * Get a form with the given ID.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $form_id The ID of the form to retrieve.
	 *
	 * @return array The array of form data.
	 */
	public function get_form( $form_id );

	/**
	 * Get the IDs of all registered forms.
	 *
	 * @author Jeremy Pry
	 * @return array All form IDs.
	 */
	public function get_form_ids();

	/**
	 * Update the data for a particular form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int   $form_id The form ID to update.
	 * @param array $data    The form data to update.
	 *
	 * @return bool Whether the form was successfully updated.
	 */
	public function update_form( $form_id, $data );

	/**
	 * Update a given field for a form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int    $form_id The form ID to update.
	 * @param string $field   The form field to update.
	 * @param mixed  $data    The form data.
	 *
	 * @return bool Whether the form field was successfully updated.
	 */
	public function update_form_field( $form_id, $field, $data );

	/**
	 * Create a new form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data Data to apply to the new form.
	 *
	 * @return int|bool The new form ID, or false on failure.
	 */
	public function create_form( $form_data );

	/**
	 * Delete a form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $form_id The form ID to delete.
	 *
	 * @return bool Whether the form was successfully deleted.
	 */
	public function delete_form( $form_id );

	/**
	 * Get all data for all forms.
	 *
	 * @author Jeremy Pry
	 * @return array All form data, indexed by form ID.
	 */
	public function get_all_forms();

	/**
	 * Import forms in bulk.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data        Array of form data, indexed by form ID.
	 * @param bool  $replace_existing Whether to replace existing forms.
	 */
	public function import_forms( $form_data, $replace_existing );

	/**
	 * Get the default values for a form.
	 *
	 * @author Jeremy Pry
	 * @return array Array of default form data.
	 */
	public function get_form_defaults();
}
