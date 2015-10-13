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
				// hook in and display our chimp chatter dashboard widget
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
					
		/**
		* Create the function to output our list stats dashboard widget
		*/
		function list_stats_dashboard_widget() {
				// Get our list data!
				// Check for a transient, if not - set one up for one hour
				if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {
					// initialize MailChimp Class
					$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
					// retreive our list data
					$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ), 'limit' => 100 ) );
					// set our transient
					set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );
				}
			?>					
				<!-- Dropdown to Change the list -->
				<?php if( !empty( $list_data['data'] ) ) {
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
					// initialize MailChimp Class
					$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
					// retreive our list data
					$account_activity = $MailChimp->call('/helper/chimp-chatter', array( 'api_key' => get_option( 'yikes-mc-api-key' , '' ) ) );
					// set our transient for one hour
					set_transient( 'yikes-easy-mailchimp-account-activity', $account_activity, 1 * HOUR_IN_SECONDS );
				}
				if( !empty( $account_activity ) ) {
					include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/account-activity-template.php' ); 
				}
		} 
		
	} // end class
	new YIKES_Inc_Easy_MailChimp_Dashboard_Widgets();
?>