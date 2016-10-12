<?php

/**
 * Form interface class that uses the native Options API.
 */
class Yikes_Inc_Easy_MailChimp_Extender_Option_Forms implements Yikes_Inc_Easy_MailChimp_Extender_Form_Interface {

	/**
	 * The base name of our option in the options table.
	 *
	 * @var string
	 */
	protected $option = 'yikes_easy_mailchimp_extender_forms';

	/**
	 * Get a form with the given ID.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $form_id The ID of the form to retrieve.
	 *
	 * @return array The array of form data.
	 */
	public function get_form( $form_id ) {
		$all_forms = $this->get_all_forms();

		return isset( $all_forms[ $form_id ] ) ? $all_forms[ $form_id ] : array();
	}

	/**
	 * Get the IDs of all registered forms.
	 *
	 * @author Jeremy Pry
	 * @return array All form IDs.
	 */
	public function get_form_ids() {
		return array_keys( $this->get_all_forms() );
	}

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
	public function update_form( $form_id, $data ) {
		$all_forms = $this->get_all_forms();

		if ( ! isset( $all_forms[ $form_id ] ) ) {
			return false;
		}

		$all_forms[ $form_id ] = array_merge_recursive( $all_forms[ $form_id ], $data );

		return update_option( $this->option, $all_forms );
	}

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
	public function update_form_field( $form_id, $field, $data ) {
		return $this->update_form( $form_id, array( $field => $data ) );
	}

	/**
	 * Create a new form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data Data to apply to the new form.
	 *
	 * @return int|bool The new form ID, or false on failure.
	 */
	public function create_form( $form_data ) {
		// Remove any existing form ID.
		unset( $form_data['id'] );

		// Grab our existing IDs and determine what the next one should be.
		$all_ids = $this->get_form_ids();
		$last_id = end( $all_ids );
		$new_id  = false === $last_id ? 1 : $last_id + 1;

		// Store the new form in our array of forms.
		$form_data['id']      = $new_id;
		$all_forms            = $this->get_all_forms();
		$all_forms[ $new_id ] = $form_data;

		return update_option( $this->option, $all_forms );
	}

	/**
	 * Delete a form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $form_id The form ID to delete.
	 *
	 * @return bool Whether the form was successfully deleted.
	 */
	public function delete_form( $form_id ) {
		$all_forms = $this->get_all_forms();

		if ( ! isset( $all_forms[ $form_id ] ) ) {
			return false;
		}

		unset( $all_forms[ $form_id ] );

		return update_option( $this->option, $all_forms );
	}

	/**
	 * Get all data for all forms.
	 *
	 * @author Jeremy Pry
	 * @return array All form data, indexed by form ID.
	 */
	public function get_all_forms() {
		return get_option( $this->option );
	}

	/**
	 * Get the name of the option used for saving the forms.
	 *
	 * @author Jeremy Pry
	 * @return string
	 */
	public function get_option_name() {
		return $this->option;
	}

	/**
	 * Import forms in bulk.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data        Array of form data, indexed by form ID.
	 * @param bool  $replace_existing Whether to replace existing forms.
	 */
	public function import_forms( $form_data, $replace_existing ) {
		$existing = $replace_existing ? array() : get_option( $this->option, array() );
		$new_data = array();

		foreach ( $form_data as $id => $data ) {
			$new_data[ $id ] = isset( $existing[ $id ] ) ? $existing[ $id ] : $data;
		}

		update_option( $this->option, $new_data );
	}
}
