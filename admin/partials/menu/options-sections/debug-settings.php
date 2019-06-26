<!--
	Mailchimp API Debug Settings / Debug Log Table
	- display installation stuff here
	- php version, wp version, plugin version and debug log
-->
<h3><span><?php _e( 'Debug Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>

<div class="inside">

	<!-- Settings Form -->
	<form action='options.php' method='post'>
	
		<?php settings_fields( 'yikes_inc_easy_mc_debug_settings_page' ); ?>
	
		<label for="yikes-mailchimp-debug-status"><strong><?php _e( 'Enable Debugging' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
			<input type="checkbox" name="yikes-mailchimp-debug-status" id="yikes-mailchimp-debug-status" value="1" <?php checked( get_option( 'yikes-mailchimp-debug-status' , '' ) , '1' ); ?>>
		</label>
		<p class="description"><?php _e( "If you encounter an issue with Easy Forms for Mailchimp you can toggle on debugging to display advanced error messages and start logging errors." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		
		<?php submit_button(); ?>
									
	</form>
	
	<!-- debug log -->
	<section id="yikes-easy-mailchimp-debug-log">
	
		<?php
			$url = esc_url_raw( 
				add_query_arg(
					array(
						'action' => 'yikes-easy-mc-clear-error-log',
						'nonce' => wp_create_nonce( 'clear-yikes-mailchimp-error-log' )
					)
				)
			);
		?>
		<h2><?php _e( "Debug Log" , 'yikes-inc-easy-mailchimp-extender' ); ?> &nbsp; <a href="<?php echo $url; ?>" class="button-secondary"><?php _e( 'Clear Log', 'yikes-inc-easy-mailchimp-extender' ); ?></a></h2>
		
		<table class="widefat" id="yikes-mailchimp-error-log">
			<!-- table header -->
			<tr>
				<th class="row-title"><strong><?php _e( 'Error Message', 'yikes-inc-easy-mailchimp-extender' ); ?></strong></th>
				<th><strong><?php _e( 'Error Details', 'yikes-inc-easy-mailchimp-extender' ); ?></strong></th>
			</tr>
			
			<?php 
				/* Generate oure error logging table */
				require_once YIKES_MC_PATH . '/includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging(); 
				$error_logging->yikes_easy_mailchimp_generate_error_log_table(); 
			?>
			
			<!-- end table body -->
			
		</table>
		<!-- end debug table -->
		
	</section>
	
</div> <!-- .inside -->