<?php

/**
 * Form interface class that uses the native Options API.
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Option_Forms extends Yikes_Inc_Easy_Mailchimp_Extender_Forms_Abstract {

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
		$data['id'] = $form_id;
		$all_forms  = $this->get_all_forms();

		if ( ! isset( $all_forms[ $form_id ] ) ) {
			return false;
		}

		$new_data = array_merge( $all_forms[ $form_id ], $data );

		// Data hasn't changed, so don't bother updating.
		if ( $new_data === $all_forms[ $form_id ] ) {

			return true;

		}

		ksort( $new_data );
		$all_forms[ $form_id ] = $new_data;

		return update_option( $this->option, $all_forms );
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
		// Sanitize the form name and description.
		if ( isset( $form_data['form_name'] ) ) {
			$form_data['form_name'] = sanitize_text_field( $form_data['form_name'] );
		}
		if ( isset( $form_data['form_description'] ) ) {
			$form_data['form_description'] = sanitize_textarea_field( $form_data['form_description'] );
		}
		// Include default form data
		$form_data = yikes_deep_parse_args( $form_data, $this->get_form_defaults() );

		// Grab our existing IDs and determine what the next one should be.
		$all_ids         = $this->get_form_ids();
		$last_id         = end( $all_ids );
		$new_id          = false === $last_id ? 1 : $last_id + 1;
		$form_data['id'] = $new_id;

		// Ensure our data is consistently sorted
		ksort( $form_data );

		// Store the new form in our array of forms.
		$all_forms            = $this->get_all_forms();
		$all_forms[ $new_id ] = $form_data;

		// Update our option with the new form.
		$result = update_option( $this->option, $all_forms );
		if ( false === $result ) {
			return $result;
		}

		return $new_id;
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
		return $this->get_option();
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
		$existing = $replace_existing ? array() : $this->get_option();
		$new_data = array();

		foreach ( $form_data as $id => $data ) {
			$new_data[ $id ] = isset( $existing[ $id ] ) ? $existing[ $id ] : $data;
			ksort( $new_data[ $id ] );
		}

		update_option( $this->option, $new_data );
	}

	/**
	 * Add our option to the database.
	 *
	 * @author Jeremy Pry
	 * @return bool
	 */
	public function create_option() {
		return add_option( $this->get_option_name(), array() );
	}

	/**
	 * Get our forms option from the database.
	 *
	 * @author Jeremy Pry
	 * @return array The array of form data.
	 */
	protected function get_option() {
		return get_option( $this->get_option_name(), array() );
	}
}
