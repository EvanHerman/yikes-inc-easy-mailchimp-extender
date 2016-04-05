<?php
	/*
	* Our ajax is processed inside of class.ajax.php
	*/
	class YIKES_Inc_Easy_MailChimp_Dashboard_Widgets
	{

		// Construction
		public function __construct() {
			if( get_option( 'yikes-mc-api-key' , '' ) != '' && get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'valid_api_key' ) {
				// hook in and display our list stats dashboard widget
				add_action( 'wp_dashboard_setup', array( $this , 'yks_mc_add_chimp_chatter_dashboard_widget' ) , 10 );
			}
			add_action( 'admin_enqueue_scripts' , array( $this, 'enqueue_dashboard_widget_script' ) );
		}

		// enqueue our JS file on the main dashboard page
		function enqueue_dashboard_widget_script( $hook ) {
			if( 'index.php' == $hook ) { // default 'dashboard' page
				wp_register_script( 'yikes-easy-mc-dashboard-widget-script' , YIKES_MC_URL . 'admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js' , array( 'jquery' ) , 'all' , false );
				$data_array = array(
					'ajax_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
					'preloader' => '<img src="' . esc_url_raw( admin_url( 'images/wpspin_light.gif' ) ) . '" title="' . __( 'Preloader' , 'yikes-inc-easy-mailchimp-extender' ) . '" alt="' . __( 'Preloader' , 'yikes-inc-easy-mailchimp-extender' ) . '" class="yikes-easy-mc-widget-preloader">'
				);
				// localize our data, to pass along to JS file
				wp_localize_script( 'yikes-easy-mc-dashboard-widget-script' , 'object' , $data_array );
				wp_enqueue_script( 'yikes-easy-mc-dashboard-widget-script' );
			}
		}

		/*******************************************************
		Custom Dashboard MailChimp Account Activity Widget
		********************************************************/
		/**
		 * Add a widget to the dashboard.
		 *
		 * This function is hooked into the 'wp_dashboard_setup' action below.
		 */
		function yks_mc_add_chimp_chatter_dashboard_widget() {
			// If the current user is not an admin, abort
			if ( apply_filters( 'yikes-mailchimp-user-role-access', current_user_can( 'manage_options' ) ) ) {
				/* List Stats Dashboard Widget */
				wp_add_dashboard_widget(
					 'yikes_easy_mc_list_stats_widget',         // Widget slug.
					 __( 'MailChimp List Stats', 'yikes-inc-easy-mailchimp-extender' ),         // Title.
					array( $this , 'list_stats_dashboard_widget' ) // Display function.
				);
				/* Chimp Chatter Dashboard Widget */
				wp_add_dashboard_widget(
					 'yikes_easy_mc_account_activity_widget',         // Widget slug.
					 __( 'MailChimp Account Activity', 'yikes-inc-easy-mailchimp-extender' ),         // Title.
					array( $this , 'account_activity_dashboard_widget' ) // Display function.
				);
			}
		}

		/**
		* Create the function to output our list stats dashboard widget
		*/
		function list_stats_dashboard_widget() {
				// Get our list data!
				// Check for a transient, if not - set one up for one hour
				if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {
					$api_key = trim( get_option( 'yikes-mc-api-key' , '' ) );
					$dash_position = strpos( $api_key, '-' );
					if( $dash_position !== false ) {
						$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/list.json';
					}
					$list_data = wp_remote_post( $api_endpoint, array(
						'body' => array(
							'apikey' => $api_key,
							'limit' => 100
						),
						'timeout' => 10,
						'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
					) );
					$list_data = json_decode( wp_remote_retrieve_body( $list_data ), true );
					if( isset( $list_data['error'] ) ) {
						if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
							require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
							$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
							$error_logging->yikes_easy_mailchimp_write_to_error_log( $list_data['error'], __( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ), "Dashboard Activity Widget" );
						}
					} else {
						// set our transient
						set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );
					}
				}
			?>
				<!-- Dropdown to Change the list -->
				<?php if( ! empty( $list_data['data'] ) ) {
					?><section class="inside-widget yikes-dashboard-widget-section">
							<strong class="select-list-title"><?php _e( 'Select a list' , 'yikes-inc-easy-mailchimp-extender' ) ?>:</strong>
							<select id="yikes-easy-mc-dashboard-change-list" class="widefat">
								<?php
									foreach( $list_data['data'] as $list ) {
										?><option val="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option><?php
									}
								?>
							</select>
							<p class="description"><?php _e( 'Select a list from the dropdown above. View statistics related to this list below.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</section>
					<!-- display stats here! -->
					<section id="yikes-easy-mc-dashboard-widget-stats">
						<?php
							if( !empty( $list_data['data'] ) ) {
								include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/stats-list-template.php' );
							}
						?>
					</section>
				<?php } else { ?>
					<section id="yikes-easy-mc-dashboard-widget-stats">
						<p class="no-lists-error"><?php _e( "Whoops, you don't have any lists set up. Head over to MailChimp to set up lists." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					</section>
				<?php }
		}

		/**
		* Create the function to output our account activity dashboard widget
		*/
		function account_activity_dashboard_widget() {
				// Get our list data!
				// Check for a transient, if not - set one up for one hour
				if ( false === ( $account_activity = get_transient( 'yikes-easy-mailchimp-account-activity' ) ) ) {
					$api_key = trim( get_option( 'yikes-mc-api-key' , '' ) );
					$dash_position = strpos( $api_key, '-' );
					if( $dash_position !== false ) {
						$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/helper/chimp-chatter.json';
					}
					$account_activity = wp_remote_post( $api_endpoint, array(
						'body' => array(
							'apikey' => $api_key
						),
						'timeout' => 10,
						'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
					) );
					$account_activity = json_decode( wp_remote_retrieve_body( $account_activity ), true );
					if( isset( $account_activity['error'] ) ) {
						if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
							require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
							$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
							$error_logging->yikes_easy_mailchimp_write_to_error_log( $account_activity['error'], __( "Get Account Activity" , 'yikes-inc-easy-mailchimp-extender' ), "Dashboard Activity Widget" );
						}
					} else {
						// set our transient for one hour
						set_transient( 'yikes-easy-mailchimp-account-activity', $account_activity, 1 * HOUR_IN_SECONDS );
					}
				}
				if( ! empty( $account_activity ) ) {
					include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/account-activity-template.php' );
				}
		}

	} // end class
	new YIKES_Inc_Easy_MailChimp_Dashboard_Widgets();
?>
