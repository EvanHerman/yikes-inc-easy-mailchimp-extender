<?php

class Yikes_Inc_Easy_MailChimp_Export_Class {
	
	/*
	*	Export our forms 
	*	@parameters
	*	@table_name 	the name of the table to export
	*	@form_ids 		array of form ID's to export ie: array( 1,4,5,6 ) (user can select specific forms)
	*	@file_name 	the name of the exported file
	*/	
	public static function yikes_mailchimp_form_export( $table_name, $form_ids , $file_name ) {
		global $wpdb;
		$wpdb->show_errors(); 															
		if( is_array( $form_ids ) ) {
			$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . $table_name . ' where ID in (' . implode( ', ' , $form_ids ) . ')' );
		} else {
			$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . $table_name );
		}
					
		// Process report request
		if (! $results) {
			$Error = $wpdb->print_error();
			die("The following error was found: $Error");
		} else {
			// Set header row values
			$output_filename = $file_name . '-'. date( 'm-d-Y' )  . '.csv';
			$output_handle = @fopen( 'php://output', 'w' );
			 
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename=' . $output_filename );
			header( 'Expires: 0' );
			header( 'Pragma: public' );	

			$first = true;
			// Parse results to csv format
			foreach ($results as $row) {
			 
			  // Add table headers
			  if($first){
				 $titles = array();
				 foreach($row as $key=>$val){
					$titles[] = $key;
				 }
				 fputcsv($output_handle, $titles);
				 $first = false;
			  }
			  
			   $leadArray = (array) $row; // Cast the Object to an array
			   // Add row to file
			   fputcsv( $output_handle, $leadArray );
			}
			 
			// Close output file stream
			fclose( $output_handle ); 
			die();
		}
	}
	
	/*
	*	Export our plugin settings 
	*	@parameters
	*	@file_name 	the name of the exported file
	*/	
	public static function yikes_mailchimp_settings_export( $file_name ) {
		
		// get an array of all of our plugin settings (on the settings pages), to loop over
		$plugin_settings = array();
		$plugin_settings['yikes-mc-api-key'] = get_option( 'yikes-mc-api-key' , '' ); // api key
		$plugin_settings['yikes-mc-api-validation'] = get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ); // api key validation
		$plugin_settings['optin-checkbox-init'] = get_option( 'optin-checkbox-init', '' ); // checkbox settings
		$plugin_settings['yikes-mc-recaptcha-status'] = get_option( 'yikes-mc-recaptcha-status' , '' ); // recaptcha status
		$plugin_settings['yikes-mc-recaptcha-site-key'] = get_option( 'yikes-mc-recaptcha-site-key', '' ); // recaptcha site key
		$plugin_settings['yikes-mc-recaptcha-secret-key'] = get_option( 'yikes-mc-recaptcha-secret-key', '' ); // recaptcha secret key
		$plugin_settings['yikes-mailchimp-debug-status'] = get_option( 'yikes-mailchimp-debug-status' , '' ); // debug settings
		$titles = array();
		$content = array();
		foreach ($plugin_settings as $option_name => $option_value ) {
			$titles[] = $option_name;
		}
		// Process report request
		if (! $plugin_settings) {
			$Error = $wpdb->print_error();
			die("The following error was found: $Error");
		} else {
			// Set header row values
			$output_filename = $file_name . '-'. date( 'm-d-Y' )  . '.csv';
			$output_handle = @fopen( 'php://output', 'w' );
			 
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename=' . $output_filename );
			header( 'Expires: 0' );
			header( 'Pragma: public' );	

			$first = true;
			// Parse results to csv format
			foreach ($plugin_settings as $option_name => $option_value ) {
			
			// Add table headers
			  if( $first ) {
				 fputcsv($output_handle, $titles);
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
	
}