<?php

/**
 * Database helper class.
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Forms extends Yikes_Inc_Easy_Mailchimp_Extender_Forms_Abstract {

	/**
	 * The WP database object.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Our custom table name.
	 *
	 * @var string
	 */
	protected $table_name = 'yikes_easy_mc_forms';

	/**
	 * The custom table name with the WPDB prefix included.
	 *
	 * @var string
	 */
	protected $prefixed_table_name = '';

	/**
	 * Yikes_Inc_Easy_Mailchimp_Customizer_Extender_DB constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( $wpdb ) {
		$this->wpdb                = $wpdb;
		$this->prefixed_table_name = "{$wpdb->prefix}{$this->table_name}";
	}

	/**
	 * Get the IDs of all forms in our table.
	 *
	 * @author Jeremy Pry
	 * @return array Form IDs.
	 */
	public function get_form_ids() {
		return $this->wpdb->get_col( "SELECT `id` FROM {$this->prefixed_table_name}" );
	}

	/**
	 * Get the data for a particular form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $form_id The ID of the form to retrieve.
	 *
	 * @return array The array of form data.
	 */
	public function get_form( $form_id ) {
		// Retrieve the raw data from the DB.
		$form_results = $this->wpdb->get_row(
			$this->wpdb->prepare( "SELECT * FROM {$this->prefixed_table_name} WHERE id = %d", $form_id ),
			ARRAY_A
		);

		// If there were no results, then return an empty array.
		if ( null === $form_results ) {
			/**
			 * Filter the form data that is retrieved from the Database.
			 *
			 * @param array $form_settings The array of processed form data.
			 * @param int   $form_id       The form ID.
			 * @param array $form_results  The raw data from the database.
			 */
			return apply_filters( 'yikes-easy-mailchimp-extender-form-data', array(), $form_id, $form_results );
		}

		// Populate array with new settings.
		$form_settings = $this->prepare_data_for_display( $form_results );

		/** This filter is documented in this function above. */
		return apply_filters( 'yikes-easy-mailchimp-extender-form-data', $form_settings, $form_id, $form_results );
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
		// Prepare the data for the database.
		$data['id'] = $form_id;
		$save_data  = $this->prepare_data_for_db( $data );
		$formats    = $this->get_format_array( $save_data );

		return (bool) $this->wpdb->update(
			$this->prefixed_table_name,
			$save_data,
			array(
				'id' => $form_id,
			),
			$formats,
			'%d'
		);
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
		// Include default form data
		$form_data = yikes_deep_parse_args( $form_data, $this->get_form_defaults() );

		// If there is an ID set, remove it
		unset( $form_data['id'] );

		// Prepare the data for the database
		$save_data = $this->prepare_data_for_db( $form_data );
		$formats   = $this->get_format_array( $save_data );

		$result = $this->wpdb->insert(
			$this->prefixed_table_name,
			$save_data,
			$formats
		);

		if ( false === $result ) {
			return $result;
		}

		return $this->wpdb->insert_id;
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
		return (bool) $this->wpdb->delete(
			$this->prefixed_table_name,
			array(
				'id' => $form_id,
			),
			'%d'
		);
	}

	/**
	 * Process the raw data for a given form.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data The raw form data from the database.
	 *
	 * @return array The processed form data.
	 */
	protected function prepare_data_for_display( $form_data ) {
		$display_data = array();
		foreach ( (array) $form_data as $key => $value ) {
			switch ( $key ) {
				case 'list_id':
					$value = sanitize_key( $value );
					break;

				case 'form_name':
					$value = esc_attr( $value );
					break;

				case 'form_description':
					$value = esc_attr( stripslashes( $value ) );
					break;

				case 'fields':
					$value = json_decode( $value, true );
					break;

				case 'custom_notifications':
					$key = 'notifications';
				// Deliberately omit break, to fall through to the next group.

				case 'submission_settings':
				case 'optin_settings':
				case 'error_messages':
				case 'form_settings':
				case 'custom_fields':
					$value = json_decode( stripslashes( $value ), true );
					break;
			}

			$display_data[ $key ] = $value;
		}

		return $display_data;
	}

	/**
	 * Prepare the given form data for the database.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $data The raw data.
	 *
	 * @return array The prepared data.
	 */
	protected function prepare_data_for_db( $data ) {
		$save_data = array();
		foreach ( (array) $data as $key => $value ) {
			switch ( $key ) {
				case 'list_id':
					$value = sanitize_key( $value );
					break;

				case 'form_name':
					$value = sanitize_text_field( $value );
					break;

				case 'form_description':
					$value = wp_kses_post( $value );
					break;

				case 'fields':
					$value = json_encode( $value );
					break;

				case 'redirect_user_on_submit':
					$value = intval( (bool) $value );
					break;

				case 'redirect_page':
				case 'submissions':
				case 'impressions':
				case 'id':
					$value = intval( $value );
					break;

				case 'notifications':
					$key = 'custom_notifications';
				// Deliberately omit break, to fall through to the next group.

				case 'submission_settings':
				case 'optin_settings':
				case 'error_messages':
				case 'form_settings':
				case 'custom_notifications':
				case 'custom_fields':
					$value = wp_slash( json_encode( $value ) );
					break;

				default:
					break;
			}

			$save_data[ $key ] = $value;
		}

		// Ensure we don't have any extra columns that don't exist in the DB
		$save_data = array_intersect_key( $save_data, $this->get_form_defaults() );

		return $save_data;
	}

	/**
	 * Generate a format array based on the present form data.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $form_data The array of database-ready data.
	 *
	 * @return array An array of formats matching the given data.
	 */
	protected function get_format_array( $form_data ) {
		$formats = array();
		foreach ( $form_data as $key => $value ) {
			switch ( $key ) {
				case 'impressions':
				case 'submissions':
				case 'redirect_user_on_submit':
				case 'redirect_page':
				case 'id':
					$formats[] = '%d';
					break;

				default:
					$formats[] = '%s';
			}
		}

		return $formats;
	}

	/**
	 * Get all data for all forms.
	 *
	 * @author Jeremy Pry
	 * @return array All form data, indexed by form ID.
	 */
	public function get_all_forms() {
		$form_ids    = $this->get_form_ids();
		$return_data = array();

		foreach ( $form_ids as $form_id ) {
			$return_data[ $form_id ] = $this->get_form( $form_id );
		}

		return $return_data;
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
		foreach ( $form_data as $id => $data ) {
			$existing = $this->get_form( $id );

			if ( ! $replace_existing && ! empty( $existing ) ) {
				continue;
			}

			$data['id'] = $id;
			$_form_data = $this->prepare_data_for_db( $data );
			$formats    = $this->get_format_array( $_form_data );

			$this->wpdb->replace(
				$this->prefixed_table_name,
				$_form_data,
				$formats
			);
		}
	}
}
