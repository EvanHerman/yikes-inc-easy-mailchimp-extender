<?php

class Yikes_Inc_Easy_Mailchimp_Import_Class {
	
	/*
	*	Export our forms 
	*	@parameters
	*	@csv_file - uploaded CSV file to parse and impot
	*	@table_name - table name to impot data to
	*/	
	public static function yikes_mailchimp_import_forms( $csv_file ) {
		if ( ! isset( $_REQUEST['action'] ) ||  $_REQUEST['action'] !== 'yikes-easy-mc-import-forms' ) {
			wp_die( __( 'There was an error during import. If you continue to run into issues, please reach out to the Yikes Inc. support team.', 'yikes-inc-easy-mailchimp-extender' ) );
		}

		$name      = $csv_file['csv']['name'];
		$ext_array = explode( '.', $name );
		$ext       = end( $ext_array );
		$tmp_name  = $csv_file['csv']['tmp_name'];

		// Ensure we have a valid file extension.
		if ( 'csv' !== $ext ) {
			wp_die( __( 'It is only possible to import a file with .csv as the extension. Please upload a .csv file.', 'yikes-inc-easy-mailchimp-extender' ) );
		}

		// Ensure we're actually able to open the file.
		$file = fopen( $tmp_name, 'r' );
		if ( false === $file ) {
			wp_die( __( 'There was a problem opening the file after it was uploaded. If this problem persists, please contact your hosting provider for assistance with file uploads.', 'yikes-inc-easy-mailchimp-extender' ) );
		}

		$row       = 1;
		$titles    = array();
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		while ( false !== ( $line = fgetcsv( $file, 10000, ',' ) ) ) {
			// Ensure we have more than one item in the current row, or else look for a new row
			if ( count( $line ) <= 1 ) {
				$row++;
				continue;
			}

			// Check if this is a settings import by confirming the first option is 'yikes-mc-api-key'
			// @todo: this should be a separate method.
			if( $line[0] == 'yikes-mc-api-key' ) {
				$options = fgetcsv($file, 10000, ',');
				$new_settings = array();
				$x = 0;
				// build our new array $key => $value pair
				foreach( $line as $option_name ) {
					$new_settings[$option_name] = $options[$x];
					$x++;
				}
				// update the options in the databse
				foreach( $new_settings as $option_name => $option_value ) {
					update_option( $option_name, $option_value );
				}
			} else { // if it's not, then it's an opt-in forms import
				// If this is the first row, then it should be title data.
				if ( 1 === $row ) {
					$titles = $line;
					$row++;
					continue;
				}

				// Combine the titles with the data from the row.
				$data = array_combine( $titles, $line );

				// Attempt to json_decode the rows that need it.
				foreach ( $data as $key => &$value ) {
					$_value = json_decode( $value, true );
					if ( JSON_ERROR_NONE === json_last_error() ) {
						$value = $_value;
					}
				}

				// Now store the data.
				$interface->create_form( $data );
			}

			$row++;
		}

		fclose($file);
	}
	
	/*
	*	Check if the export file is a settings or an optin forms export .csv file
	*	@since 1.0
	*	@returns $import_type
	*	@ optin-forms/settings
	*/	
	public static function yikes_mailchimp_import_type( $csv_file ) {
		// confirm
		if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-import-forms' ) {
			$name = $csv_file['csv']['name'];
			$ext_array = explode('.', $name);
			$ext = end( $ext_array );
			$tmpName = $csv_file['csv']['tmp_name'];
			if($ext === 'csv') {
				if(($handle = fopen($tmpName, 'r')) !== FALSE) {
					$num = count($handle);
					$file = fopen( $tmpName, 'r');
					$row = 1;
					$first_line_data = fgetcsv($file, 10000, ',');
					if( count( $first_line_data ) > 1 ) {
						// Check if this is a settings import by confirming the first option is 'yikes-mc-api-key'
						if( $first_line_data[0] == 'yikes-mc-api-key' ) {
							$import_type = 'import-settings';
						} else {
							$import_type = 'import-forms';
						}
					}
				}
			}
		}
		return $import_type;
	}
}
