<?php

/*
* Our ajax is processed inside of class.ajax.php
*/

class YIKES_Inc_Easy_MailChimp_Dashboard_Widgets {

	// Construction
	public function __construct() {
		if ( yikes_get_mc_api_key() != '' && get_option( 'yikes-mc-api-validation', 'invalid_api_key' ) == 'valid_api_key' ) {
			// hook in and display our list stats dashboard widget
			add_action( 'wp_dashboard_setup', array( $this, 'yks_mc_add_chimp_chatter_dashboard_widget' ), 10 );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_widget_script' ) );
	}

	/**
	 * Enqueue our JS file on the main dashboard page.
	 *
	 * @param string $hook
	 */
	function enqueue_dashboard_widget_script( $hook ) {
		if ( 'index.php' !== $hook ) {
			return;
		}

		wp_register_script( 'yikes-easy-mc-dashboard-widget-script', YIKES_MC_URL . 'admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js', array( 'jquery' ), 'all', false );
		$data_array = array(
			'ajax_url'  => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'preloader' => '<img src="' . esc_url_raw( admin_url( 'images/wpspin_light.gif' ) ) . '" title="' . __( 'Preloader', 'yikes-inc-easy-mailchimp-extender' ) . '" alt="' . __( 'Preloader', 'yikes-inc-easy-mailchimp-extender' ) . '" class="yikes-easy-mc-widget-preloader">',
		);
		// localize our data, to pass along to JS file
		wp_localize_script( 'yikes-easy-mc-dashboard-widget-script', 'yikes_mailchimp_dsahboard_widget', $data_array );
		wp_enqueue_script( 'yikes-easy-mc-dashboard-widget-script' );
	}

	/*******************************************************
	 * Custom Dashboard MailChimp Account Activity Widget
	 ********************************************************/
	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	function yks_mc_add_chimp_chatter_dashboard_widget() {
		// If the current user is not an admin, abort
		if ( current_user_can( apply_filters( 'yikes-mailchimp-admin-widget-capability', apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ) ) ) ) {
			/* List Stats Dashboard Widget */
			wp_add_dashboard_widget(
				'yikes_easy_mc_list_stats_widget',         // Widget slug.
				__( 'MailChimp List Stats', 'yikes-inc-easy-mailchimp-extender' ),         // Title.
				array( $this, 'list_stats_dashboard_widget' ) // Display function.
			);
			/* Chimp Chatter Dashboard Widget */
			wp_add_dashboard_widget(
				'yikes_easy_mc_account_activity_widget',         // Widget slug.
				__( 'MailChimp Account Activity', 'yikes-inc-easy-mailchimp-extender' ),         // Title.
				array( $this, 'account_activity_dashboard_widget' ) // Display function.
			);
		}
	}

	/**
	 * Create the function to output our list stats dashboard widget
	 */
	function list_stats_dashboard_widget() {
		// Get our list data!
		$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_lists();
		
		if ( is_wp_error( $list_data ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $list_data['error'], __( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ), "Dashboard Activity Widget" );
		}

		?>
		<!-- Dropdown to Change the list -->
		<?php if ( ! empty( $list_data ) ) {
			?>
			<section class="inside-widget yikes-dashboard-widget-section">
			<strong class="select-list-title"><?php _e( 'Select a list', 'yikes-inc-easy-mailchimp-extender' ) ?>:</strong>
			<select id="yikes-easy-mc-dashboard-change-list" class="widefat">
				<?php
				foreach ( $list_data as $list ) {
					?>
					<option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
					<?php
				}
				?>
			</select>
			<p class="description"><?php _e( 'Select a list from the dropdown above. View statistics related to this list below.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			</section>
			<!-- display stats here! -->
			<section id="yikes-easy-mc-dashboard-widget-stats">
				<?php include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/stats-list-template.php' ); ?>
			</section>
		<?php } else { ?>
			<section id="yikes-easy-mc-dashboard-widget-stats">
				<p class="no-lists-error"><?php _e( "Whoops, you don't have any lists set up. Head over to MailChimp to set up lists.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			</section>
		<?php }
	}

	/**
	 * Create the function to output our account activity dashboard widget
	 */
	function account_activity_dashboard_widget() {
		$chimp_chatter    = yikes_get_mc_api_manager()->get_chimp_chatter();
		$account_activity = $chimp_chatter->chimp_chatter();

		if ( is_wp_error( $account_activity ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log(
				$account_activity->get_error_code(),
				__( "Get Account Activity", 'yikes-inc-easy-mailchimp-extender' ),
				"Dashboard Activity Widget"
			);
		}

		if ( ! empty( $account_activity ) ) {
			include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/account-activity-template.php' );
		}
	}
}
