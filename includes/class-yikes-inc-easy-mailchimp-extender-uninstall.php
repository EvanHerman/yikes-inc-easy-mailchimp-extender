<?php
class Yikes_Inc_Easy_Mailchimp_Extender_Uninstaller {

	public static function uninstall() {
		global $wpdb;
		// define global switched (required for switch_to_blog())
		global $switched;		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// users can only unisntall a plugin from the network dashboard page
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::_uninstall_yikes_easy_mailchimp( $wpdb );
				restore_current_blog();
			}
			switch_to_blog( $old_blog );
			return;
		}
		self::_uninstall_yikes_easy_mailchimp( $wpdb );
	}
	
	/**
	 * Short Description. Plugin Activation.
	 *
	 * Long Description. Creates our custom form tables on activation.
	 *
	 * @since    6.0.0
	 */
	static function _uninstall_yikes_easy_mailchimp( $wpdb ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		/* Clean up and delete our custom table from the databse */
		$table = $wpdb->prefix."yikes_easy_mc_forms";
		$sql = 'DROP TABLE IF EXISTS ' . $table;

		//Delete any options thats stored also?
		$wpdb->query( $sql );
		dbDelta($sql);

		/* Clear All Transient Data */
		delete_transient( 'yikes-easy-mailchimp-list-data' );
		delete_transient( 'yikes-easy-mailchimp-account-data' );
		delete_transient( 'yikes-easy-mailchimp-profile-data' );
		delete_transient( 'yikes-easy-mailchimp-account-activity' );
		delete_transient( 'yikes-mailchimp-contributor-transient' );

		/* Clear All Plugin Options */
		delete_option( 'yikes_easy_mailchimp_activation_date' );
		delete_option( 'widget_yikes_easy_mc_widget' );
		delete_option( 'yikes-mc-api-key' );
		delete_option( 'yikes-mc-api-validation' );
		delete_option( 'yikes-mailchimp-debug-status' );
		delete_option( 'yikes-mc-double-optin-message' );
		delete_option( 'yikes-mc-flavor' );
		delete_option( 'yikes-mc-lists' );
		delete_option( 'yikes-mc-optin' );
		delete_option( 'yikes-mc-optIn-checkbox' );
		delete_option( 'yikes-mc-recaptcha-api-key' );
		delete_option( 'yikes-mc-recaptcha-private-api-key' );
		delete_option( 'yikes-mc-recaptcha-setting' );
		delete_option( 'yikes-mc-single-optin-message' );
		delete_option( 'yikes-mc-yks-mailchimp-jquery-datepicker' );
		delete_option( 'yikes-mc-yks-mailchimp-optin-checkbox-text' );
		delete_option( 'yikes-mc-yks-mailchimp-optIn-default-list' );
		delete_option( 'yikes-mc-yks-mailchimp-required-text' );
		delete_option( 'yikes-mc-single-optin-message' );
		delete_option( 'yikes-mc-api-invalid-key-response' );
		delete_option( 'yikes-mc-recaptcha-status' );
		delete_option( 'yikes-mc-recaptcha-site-key' );
		delete_option( 'yikes-mc-recaptcha-secret-key' );
		delete_option( 'yikes-mc-error-messages' );
		delete_option( 'yikes_mc_database_version' );
		delete_option( 'yikes_mailchimp_activation_redirect' );
		delete_option( 'yikes_easy_mailchimp_extender_forms' );
		delete_option( 'yikes_easy_mailchimp_extender_version' );
	}
}
