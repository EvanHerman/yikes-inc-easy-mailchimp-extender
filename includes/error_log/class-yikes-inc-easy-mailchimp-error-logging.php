<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.yikesplugins.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 * @author     YIKES Inc. <plugins@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Error_Logging {

	/**
	 * Whether we're doing debugging.
	 *
	 * @var bool
	 */
	protected $is_debugging;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->is_debugging = WP_DEBUG || get_option( 'yikes-mailchimp-debug-status', '' ) == '1';
	}

	/**
	 * Maybe write to the error log.
	 *
	 * This will do nothing if debugging is not enabled.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $returned_error The returned error.
	 * @param string $error_type     The error type.
	 * @param string $page           The page information.
	 */
	public function maybe_write_to_log( $returned_error, $error_type, $page = '' ) {
		if ( ! $this->is_debugging ) {
			return;
		}

		$this->yikes_easy_mailchimp_write_to_error_log( $returned_error, $error_type, $page );
	}
	
	
	/* this will be used to write errors to our log
		Example:
		require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->yikes_easy_mailchimp_write_to_error_log( $wpdb->last_error , __( "Creating a new form" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Forms" , 'yikes-inc-easy-mailchimp-extender' ) );
	*/
	/*
	*	Parameters:
	*	@returned_error 
	*	@error_type - what was running when the error occured ie (new user subscription, remove user etc)
	*/
	public function yikes_easy_mailchimp_write_to_error_log( $returned_error , $error_type , $page='' ) {
		
		// confirm error logging is toggled on, else lets exit
		if( get_option( 'yikes-mailchimp-debug-status' , '' )  != '1' ) {
			return;
		}
		
		$contents = file_get_contents( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' , true );
		
		// if we pass in a custom page, don't set things up
		if( empty( $page ) ) {
			// get the current page, admin or front end?
			if( is_admin() ) {
				$page = __( 'Admin', 'yikes-inc-easy-mailchimp-extender' );
			} else {
				$page = __( 'Front End', 'yikes-inc-easy-mailchimp-extender' );
			}
		}
		
		ob_start();
		?>
			<tr>
				<td class="row-title">
					<label for="tablecell">
						<em><?php echo ucwords( stripslashes( $returned_error ) ); ?></em>
					</label>
				</td>
				<td>
					<?php _e( 'Page:' , 'yikes-inc-easy-mailchimp-extender' ); echo ' ' . $page; ?> || <?php _e( 'Type:' , 'yikes-inc-easy-mailchimp-extender' ); echo ' ' . $error_type; ?> || <?php _e( 'Time:' , 'yikes-inc-easy-mailchimp-extender' ); echo ' ' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , current_time('timestamp') ); ?>
				</td>
			</tr>
		<?php
		$new_contents = ob_get_clean() . $contents;
		
		// file put contents $returned error + other data
		if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
			file_put_contents( 
				YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php',
				$new_contents
			);
		}
	}
	
	/*
	*  ytks_mc_generate_error_log_table()
	*  generate our erorr log table on the options settings page
	*
	*  @since 5.6
	*/	
	public function yikes_easy_mailchimp_generate_error_log_table() {		
		// ensure file_get_contents exists
		if( function_exists( 'file_get_contents' ) ) {	
			// confirm that our file exists
			if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
				$error_log_contents = file_get_contents( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' , true );							
				if( $error_log_contents === FALSE ) {
					return _e( 'File get contents not available' , 'yikes-inc-easy-mailchimp-extender' );
				}
				if ( $error_log_contents != '' ) {
					// return $error_log_contents;
					print_r( $error_log_contents );
				} else {
					?>
						<!-- table body -->
						<tr class="error-log-tr">
							<td class="row-title colspanchange" colspan="2">
								<strong><span class='dashicons dashicons-no-alt'></span> <?php _e( 'No errors logged.', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
								<?php if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '' ) { ?>
									<br />
									<p class="to-start-logging-errors-notice"><em><?php _e( "To start logging errors toggle on the 'Enable Debugging' option above.", 'yikes-inc-easy-mailchimp-extender' ); ?></em></p>
								<?php } ?>
							</td>
						</tr>
					<?php
				}
			} else {
				?>
						<!-- table body -->
						<tr class="error-log-tr">
							<td class="row-title colspanchange" colspan="2">
								<strong><span class='dashicons dashicons-no-alt'></span> <?php _e( 'Error Log Missing', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
								<p class="error-log-missing-file">	
									<em><?php _e( "It looks like your error log file is missing. You can attempt to create one by clicking the button below.", 'yikes-inc-easy-mailchimp-extender' ); ?></em>
									
									<?php
									$url = esc_url_raw( 
										add_query_arg(
											array(
												'action' => 'yikes-easy-mc-create-error-log',
												'nonce' => wp_create_nonce( 'create_error_log' )
											)
										)
									);
									?>
									<form id="create-error-log" method="POST" action="<?php echo $url; ?>">
										<?php submit_button( __( 'Attempt to Create Error Log' , 'yikes-inc-easy-mailchimp-extender' ) , 'secondary' , '' , '' , array() ); ?>
									</form>
									
								</p>
							</td>
						</tr>
					<?php
			}
		} else { // if file_get_contents is disabled server side
			?>
				<!-- table body -->
				<tr>
					<td class="row-title colspanchange" colspan="2">
						<strong><?php _e( 'It looks like the function file_get_contents() is disabled on your server. We cannot retrieve the contents of the error log.', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
					</td>
				</tr>
			<?php
		}
	}
		
}
