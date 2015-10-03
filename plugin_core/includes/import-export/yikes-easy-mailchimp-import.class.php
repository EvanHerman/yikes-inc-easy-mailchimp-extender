<?php

class Yikes_Inc_Easy_MailChimp_Import_Class {
	
	/*
	*	Export our forms 
	*	@parameters
	*	@csv_file - uploaded CSV file to parse and impot
	*	@table_name - table name to impot data to
	*/	
	public static function yikes_mailchimp_import_forms( $csv_file ) {
	
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
					while (($line = fgetcsv($file, 10000, ',')) !== FALSE) {
						if( count( $line ) > 1 ) {
							// Check if this is a settings import by confirming the first option is 'yikes-mc-api-key'
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
								global $wpdb;		
								if( $row != 1 ) {
									$wpdb->insert(
										$wpdb->prefix . 'yikes_easy_mc_forms',
										array(
											'list_id' => $line[1],
											'form_name' => stripslashes( $line[2] ),
											'form_description' => stripslashes( $line[3] ),
											'fields' => $line[4],
											'custom_styles' => $line[5],
											'custom_template' => $line[6],
											'send_welcome_email' => $line[7],
											'redirect_user_on_submit' => $line[8],
											'redirect_page' => $line[9],
											'submission_settings' => $line[10],
											'optin_settings' => $line[11],
											'error_messages' => $line[12],
											'custom_notifications' => $line[13],
											'impressions' => $line[14],
											'submissions' => $line[15],
											'custom_fields' => $line[16],
										),
										array(
											'%s', // list id
											'%s', // form name
											'%s', // form description
											'%s', // fields
											'%s', // custom styles
											'%d',	// custom template
											'%d',	// send welcome email
											'%s',	// redirect user
											'%s',	// redirect page
											'%s',	// submission
											'%s',	// optin
											'%s', // error
											'%s', // custom notifications
											'%d',	// impressions #
											'%d',	// submissions #
											'%s', // custom fields
										)
									);
								}	
							}
						}	
						$row++;
					}
					fclose($file);
				} else {
					wp_die( __( 'There was an error during import. If you continue to run into issues, please reach out to the Yikes Inc. support team.' , 'yikes-inc-easy-mailchimp-extender' ) );
				}
			}
			
		}
		
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