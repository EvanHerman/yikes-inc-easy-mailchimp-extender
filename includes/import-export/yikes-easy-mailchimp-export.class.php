<?php

class Yikes_Inc_Easy_MailChimp_Export_Class {

	/**
	 * Export our forms.
	 *
	 * @param string $file_name
	 * @param array  $form_ids
	 */
	public static function yikes_mailchimp_form_export( $file_name, $form_ids ) {
		$interface = yikes_easy_mailchimp_extender_get_form_interface();

		$form_ids = empty( $form_ids ) ? $interface->get_form_ids() : (array) $form_ids;
		$results  = array();
		foreach ( $form_ids as $form_id ) {
			$results[ $form_id ] = $interface->get_form( $form_id );
		}

		// Process report request
		$output_filename = $file_name . '-' . date( 'm-d-Y' ) . '.csv';
		$output_handle   = @fopen( 'php://output', 'w' );

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $output_filename );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		// Export the titles using form defaults
		$defaults = $interface->get_form_defaults();
		ksort( $defaults );
		$titles = array_keys( $defaults );
		fputcsv( $output_handle, $titles );

		// Parse results to csv format
		foreach ( $results as $row ) {

			// Ensure that we have all the data we're supposed to have.
			$row = array_intersect_key( $row, $interface->get_form_defaults() );
			$row = yikes_deep_parse_args( $row, $interface->get_form_defaults() );
			ksort( $row );

			// Possibly convert arrays to JSON.
			foreach ( $row as $key => &$value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}

				$value = json_encode( $value );
			}

			// Add row to file
			fputcsv( $output_handle, $row );
		}

		// Close output file stream
		fclose( $output_handle );
		die();
	}

	/**
	 * Export our plugin settings
	 *
	 * @param string $file_name The name of the file to create.
	 */
	public static function yikes_mailchimp_settings_export( $file_name ) {

		// get an array of all of our plugin settings (on the settings pages), to loop over
		$plugin_settings = array(
			'yikes-mc-api-key'              => yikes_get_mc_api_key(),
			'yikes-mc-api-validation'       => get_option( 'yikes-mc-api-validation', 'invalid_api_key' ),
			'optin-checkbox-init'           => get_option( 'optin-checkbox-init', '' ),
			'yikes-mc-recaptcha-status'     => get_option( 'yikes-mc-recaptcha-status', '' ),
			'yikes-mc-recaptcha-site-key'   => get_option( 'yikes-mc-recaptcha-site-key', '' ),
			'yikes-mc-recaptcha-secret-key' => get_option( 'yikes-mc-recaptcha-secret-key', '' ),
			'yikes-mailchimp-debug-status'  => get_option( 'yikes-mailchimp-debug-status', '' ),
		);

		$titles  = array();
		$content = array();
		foreach ( $plugin_settings as $option_name => $option_value ) {
			$titles[] = $option_name;
		}

		// Generate the output file.
		$output_filename = $file_name . '-' . date( 'm-d-Y' ) . '.csv';
		$output_handle   = @fopen( 'php://output', 'w' );

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $output_filename );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		// Parse results to csv format
		$first = true;
		foreach ( $plugin_settings as $option_name => $option_value ) {

			// Add table headers
			if ( $first ) {
				fputcsv( $output_handle, $titles );
				$first = false;
			}
			$content[] = $option_value;

		}
		fputcsv( $output_handle, $content );

		// Close output file stream
		fclose( $output_handle );
		die();
	}
}
