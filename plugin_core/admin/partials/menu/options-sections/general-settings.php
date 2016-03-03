<!--
	General Settings Options Template
-->
<?php 
	/* Get and Store Option Values */
	if( get_option( 'yikes-mc-api-validation', 'invalid_api_key' ) == 'valid_api_key' ) {
		$api_connection = '<span id="connection-container" class="api-connected" title="' . __( "Your site is currently connected to the MailChimp API" , "yikes-inc-easy-mailchimp-extender" ) . '"><span class="dashicons dashicons-yes yikes-mc-api-connected"></span> ' . __( "Connected" , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
		$api_error_response = '';
	} else {
		$api_connection = '<span id="connection-container" class="api-not-connected"><span class="dashicons dashicons-no-alt yikes-mc-api-not-connected"></span>  ' . __( "Not Connected" , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
		if( get_option( 'yikes-mc-api-invalid-key-response' , '' ) != '' ) {	
			$api_error_response = '<p><small><i class="dashicons dashicons-no-alt"></i> ' . get_option( 'yikes-mc-api-invalid-key-response' , '' ) . '</small></p>';
		} else {
			$api_error_response = '';
		}
	}
?>
<h3><span><?php _e( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span><?php echo $api_connection; ?></h3>
<div class="inside">
									
	<!-- Settings Form -->
	<form action='options.php' method='post'>							
									
		<?php settings_fields( 'yikes_inc_easy_mc_general_settings_page' ); ?>
		
		<!-- Start Option Inputs -->
																					
		<!-- MailChimp API Input Field -->
		<label for="yks-mailchimp-api-key">
		
			<p><?php _e( 'Enter your API key in the field below to connect your site to your MailChimp account.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			
			<p><strong><?php _e( 'MailChimp API Key' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>
			
			<input autocomplete="off" <?php if( strlen( trim( get_option( 'yikes-mc-api-key' , '' ) ) ) > 0 ) { ?> type="password" <?php } else { ?> type="text" <?php } ?> value="<?php echo trim( get_option( 'yikes-mc-api-key' , '' ) ); ?>" placeholder="<?php _e( 'MailChimp API Key' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="yikes-mc-api-key" id="yikes-mc-api-key" class="settings-page-input" />
			
			<?php echo $api_error_response; ?>
			
			<p class="description"><small><a href="https://admin.mailchimp.com/account/api" target="_blank" title="<?php _e( 'Get your API key here' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Get your API key here' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></p>
		
		</label>
												
	<?php submit_button(); ?>
									
	</form>
</div> <!-- .inside -->