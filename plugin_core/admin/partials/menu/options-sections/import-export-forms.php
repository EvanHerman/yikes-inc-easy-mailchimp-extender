<?php
	// Query the database for form ID's/Names
	// Run our custom query to retreive our forms from the table we've created
	global $wpdb;
	// return it as an array, so we can work with it to build our form below
	$all_forms = $wpdb->get_results( 'SELECT id, form_name FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
?>
<!--
	MailChimp API Clear Stored Cache Template
-->
<h3><span><?php _e( 'Import/Export Forms' , 'yikes-inc-easy-mailchimp-extender' ); ?></span><?php echo $api_connection; ?></h3>
<div class="inside">
					
	<!-- Export Form -->
	<form action="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-export-forms' , 'nonce' => wp_create_nonce( 'export-forms' ) ) ) ); ?>" method="post">							
									
		<p><strong><?php _e( "Export Forms" , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>
			<?php if( !empty( $all_forms ) ) { ?>	
				<!-- custom list so users can export specific forms -->
				<a class="toggle-custom-lists button-secondary" onclick="jQuery(this).next().slideToggle();return false;"><?php _e( 'Select Forms' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
				<ul class="export-custom-forms-list">
					<p class="description"><?php _e( 'Select which forms to export. Leave all checkboxes unchecked to export all of your forms.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					<?php foreach( $all_forms as $form ) { ?>
						<li><label><input type="checkbox" name="export_forms[]" value="<?php echo (int) $form['id']; ?>"><?php echo esc_html( $form['form_name'] ); ?></label></li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p><em><?php _e( "It looks like you haven't created any forms yet." , "yikes-inc-easy-mailchimp-extender" ); ?></em></p>
			<?php } ?>
		<!-- check if any of our transients contain data -->							
		<p><input type="submit" class="button-primary" value="<?php _e( 'Export Forms' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></p>
					
	</form>
	
	<hr />
	
	<!-- Import Form -->	
	<form action="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-import-forms' , 'nonce' => wp_create_nonce( 'import-forms' ) ) ) ); ?>" method="post" enctype="multipart/form-data">							
									
		<p><strong><?php _e( "Import Forms" , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>
		<label>
			<p class="description"><?php _e( 'Select the .csv forms export file to import to the site.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<input type="file" name="csv" id="forms_to_import">
		</label>
		<!-- check if any of our transients contain data -->							
		<p><input type="submit" class="button-primary" value="<?php _e( 'Import Forms' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></p>
					
	</form>
	
</div> <!-- .inside -->