<?php
/*
*	Options Page
*	@since 6.0
*/

	/* Get and Store Option Values */
	if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) === 'valid_api_key' ) {
		$api_connection = '<span id="connection-container" class="api-connected" title="' . __( "Your site is currently connected to the Mailchimp API" , "yikes-inc-easy-mailchimp-extender" ) . '"><span class="dashicons dashicons-yes yikes-mc-api-connected"></span> ' . __( "Connected" , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
		$api_error_response = '';
	} else {
		$api_connection = '<span id="connection-container" class="api-not-connected"><span class="dashicons dashicons-no-alt yikes-mc-api-not-connected"></span>  ' . __( "Not Connected" , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
		if( get_option( 'yikes-mc-api-invalid-key-response' , '' ) != '' ) {
			$api_error_response = '<p><small><i class="dashicons dashicons-no-alt"></i> ' . get_option( 'yikes-mc-api-invalid-key-response' , '' ) . '</small></p>';
		} else {
			$api_error_response = '';
		}
	}
	
	/* 
	* Properly Sanatize $_REQUEST['section'] variable 
	*
	* @since 6.2.2
	*
	*/
	if ( isset( $_REQUEST['section'] ) ) {
		$_REQUEST['section'] = preg_replace('/[^\w-]/', '', strip_tags ( $_REQUEST['section'] ) );
	}
?>
	<!-- Actual Settings Form -->
	<div class="wrap yikes-easy-mc-wrap">

		<!-- Freddie Logo -->
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Mailchimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - Mailchimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

		<h1>Easy Forms for Mailchimp | <?php if ( isset( $_REQUEST['section'] ) ) { echo ucwords( str_replace( '-', ' ', $_REQUEST['section'] ) ); } else { echo __( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ); } ?></h1>

		<!-- Settings Page Description -->
		<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Manage the overall settings for Easy forms for Mailchimp.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<?php
			/* Success Messages on Options Updated */
			if( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' ) {
				?>
				<div class="updated manage-form-admin-notice">
					<p><?php _e( 'Settings successfully updated.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</div>
				<?php
			}
			/* Mailchimp API Cleared Successfully message */
			if( isset( $_REQUEST['transient-cleared'] ) && $_REQUEST['transient-cleared'] == 'true' ) {
				?>
				<div class="updated manage-form-admin-notice">
					<p><?php _e( 'Mailchimp API Cache successfully cleared.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</div>
				<?php
			}
			/* Error Log Clear Messages */
				/* Success Messages on Options Updated */
				if( isset( $_REQUEST['error-log-cleared'] ) && $_REQUEST['error-log-cleared'] == 'true' ) {
					?>
					<div class="updated manage-form-admin-notice">
						<p><?php _e( 'Error log successfully cleared.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</div>
					<?php
				}
				/* Mailchimp API Cleared Successfully message */
				if( isset( $_REQUEST['error-log-cleared'] ) && $_REQUEST['error-log-cleared'] == 'false' ) {
					?>
					<div class="error manage-form-admin-notice">
						<p><?php _e( "Whoops! We've encountered an error while trying to clear the error log. Please refresh the page and try again. If the error persists please get in touch with the YIKES Inc. support team.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</div>
					<?php
				}

				/* Display a success message if our error log was sucessfully created, or display an error if not */
				if( isset( $_GET['error_log_created'] ) && $_GET['error_log_created'] == 'true' ) {
					?>
					<div class="updated">
						<p><?php _e( 'Error log successfully created. You may now start logging errors.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</div>
					<?php
				} else if( isset( $_GET['error_log_created'] ) && $_GET['error_log_created'] == 'false' ) {
					?>
					<div class="error">
						<p><?php echo esc_attr( urldecode( $_GET['error_message'] ) , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</div>
					<?php
				}

				/* Display a success message if the user successfully imported some forms */
				if( isset( $_GET['section'] ) && $_GET['section'] == 'import-export-forms' && isset( $_GET['import-forms'] ) && $_GET['import-forms'] == 'true' ) {
					?>
					<div class="updated">
						<p><?php printf( __( 'Opt-in forms successfully imported. <a href="%s" title="View Forms">View Forms</a>', 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?></p>
					</div>
					<?php
				} elseif( isset( $_GET['section'] ) && $_GET['section'] == 'import-export-forms' && isset( $_GET['import-settings'] ) && $_GET['import-settings'] == 'true' ) {
					?>
					<div class="updated">
						<p><?php printf( __( 'YIKES Easy Forms for Mailchimp settings successfully imported.', 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?></p>
					</div>
					<?php
				}
		?>

		<!-- entire body content -->
		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-2">

				<!-- main content -->
				<div id="post-body-content">

					<div class="meta-box-sortables ui-sortable">

						<div class="postbox yikes-easy-mc-postbox">

							<?php if( !isset( $_REQUEST['section'] ) || $_REQUEST['section'] == '' ) {
								include YIKES_MC_PATH . 'admin/partials/menu/options-sections/general-settings.php';
							} else {
								if( isset( $_REQUEST['addon'] ) && $_REQUEST['addon'] == 'true' ) {
									include apply_filters( 'yikes-mailchimp-'.$_REQUEST['section'].'-options-path' , '' );
								} else {
									// White list a set of files that are allowed to be included here
									$file_base = 'admin/partials/menu/options-sections/';
									$acceptable_files = array(
										$file_base . 'api-cache-settings.php',
										$file_base . 'debug-settings.php',
										$file_base . 'general-settings.php',
										$file_base . 'import-export-forms.php',
										$file_base . 'integration-settings.php',
										$file_base . 'recaptcha-settings.php',
									);
									// Ensure the included file is allowed and whitelisted above, before including it
									if ( 0 === validate_file( 'admin/partials/menu/options-sections/' . $_REQUEST['section'] . '.php', $acceptable_files ) ) {
										include YIKES_MC_PATH . 'admin/partials/menu/options-sections/' . $_REQUEST['section'] . '.php';
									} else {
										wp_die( esc_attr__( 'Invalid file. If this error persists, please contact support.', 'yikes-inc-easy-mailchimp' ) );
									}
								}
							}
							?>

						</div> <!-- .postbox -->

					</div> <!-- .meta-box-sortables .ui-sortable -->

				</div> <!-- post-body-content -->

				<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container options-sidebar">

					<div class="meta-box-sortables">

						<div class="postbox yikes-easy-mc-postbox">

							<?php
								// Render our sidebar menu
								// inside class-yikes-inc-easy-mailchimp-extender-admin.php
								$this->generate_options_pages_sidebar_menu();
							?>

						</div> <!-- .postbox -->

						<?php $this->generate_show_some_love_container(); ?>

					</div> <!-- .meta-box-sortables -->

				</div> <!-- #postbox-container-1 .postbox-container -->

			</div> <!-- #post-body .metabox-holder .columns-2 -->

			<br class="clear">
		</div> <!-- #poststuff -->

	</div>	<!-- .wrap -->
