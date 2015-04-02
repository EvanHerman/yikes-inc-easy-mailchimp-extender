<!--
	General Settings Options Template
	-- yea
-->
<h3><span><?php _e( 'General Settings' , $this->text_domain ); ?></span><?php echo $api_connection; ?></h3>
<div class="inside">
									
	<!-- Settings Form -->
	<form action='options.php' method='post'>							
									
		<?php settings_fields( 'yikes_inc_easy_mc_general_settings_page' ); ?>
		
		<!-- Start Option Inputs -->
																					
		<!-- MailChimp API Input Field -->
		<label for="yks-mailchimp-api-key">
			<p><strong><?php _e( 'MailChimp API Key' , $this->text_domain ); ?></strong></p>
			<input autocomplete="off" <?php if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'valid_api_key' ) { ?> type="password" <?php } else { ?> type="text" <?php } ?> value="<?php echo trim( get_option( 'yikes-mc-api-key' , '' ) ); ?>" placeholder="<?php _e( 'MailChimp API Key' , $this->text_domain ); ?>" name="yikes-mc-api-key" id="yikes-mc-api-key" class="settings-page-input" />
			<?php echo $api_error_response; ?>
			<p class="description" style="font-size:11px;"> <a href="https://admin.mailchimp.com/account/api" target="_blank" title="<?php _e( 'Get your API key here' , $this->text_domain ); ?>"><?php _e( 'Get your API key here' , $this->text_domain ); ?></a></p>
		</label>
												
		<!-- Single vs Double Optin
		<label for="api-connection-status">
			<p><strong>Single or Double Optin</strong></p>
													
			<select id="yks-mailchimp-optin" name="yks-mailchimp-optin" onchange="toggleOptinValue( this );">
				<option value="false" <?php echo selected( $optin , 'false' ); ?> ><?php _e( 'Single Optin' , $this->text_domain ); ?></option>
				<option value="true" <?php echo selected( $optin , 'true' ); ?> ><?php _e( 'Double Optin' , $this->text_domain ); ?></option>
			</select>
													
			<br />
													
			<label for="yks-mailchimp-single-optin-message" class="yks-mailchimp-single-optin-message" <?php if( $optin == 'false' ) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
				<p><strong>Single Optin Confirmation Message</strong></p>
				<textarea id="yks-mailchimp-single-optin-message" name="yks-mailchimp-single-optin-message"><?php echo $single_optin_message; ?></textarea>
			</label>
													
			<label for="yks-mailchimp-single-optin-message" class="yks-mailchimp-double-optin-message" <?php if( $optin == 'true' ) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
				<p><strong>Double Optin Confirmation Message</strong></p>
				<textarea id="yks-mailchimp-double-optin-message" name="yks-mailchimp-double-optin-message"><?php echo $double_optin_message; ?></textarea>
			</label>
			 -->
		</label>
												
	<?php submit_button(); ?>
									
	</form>

</div> <!-- .inside -->