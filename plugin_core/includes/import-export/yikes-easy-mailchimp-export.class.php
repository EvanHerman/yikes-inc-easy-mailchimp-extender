<?php

class Yikes_Inc_Easy_MailChimp_Export_Class {
	
	/*
	*	Export our forms 
	*	@parameters
	*	@table_name - the name of the table to export
	*	@form_ids - array of form ID's to export ie: array( 1,4,5,6 ) (user can select specific forms)
	*/	
	public static function yikes_mailchimp_export( $table_name, $form_ids , $file_name ) {
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
	
	
	
}