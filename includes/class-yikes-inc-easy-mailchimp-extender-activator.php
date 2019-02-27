<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.yikesplugins.com/
 * @since      6.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      6.0.0
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 * @author     YIKES Inc. <plugins@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Activator {
	/**
	 * Short Description. Activation hook.
	 *
	 * Long Description. Checks for multisite and creates a table for each blog if necessary.
	 *
	 * @since    6.0.0
	 */
	public static function activate( $network_wide ) {
		global $wpdb;

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ( $network_wide ) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::_activate_yikes_easy_mailchimp( $wpdb );
					restore_current_blog();
				}
				switch_to_blog( $old_blog );
				return;
			}
			self::_activate_yikes_easy_mailchimp( $wpdb );
		} else { /* end network activate */
			self::_activate_yikes_easy_mailchimp( $wpdb );
		}
	}

	/**
	 * Short Description. Plugin Activation.
	 *
	 * Long Description. Creates our custom form tables on activation.
	 *
	 * @since    6.0.0
	 */
	static function _activate_yikes_easy_mailchimp( $wpdb ) {

		// single site
		$custom_table_name = $wpdb->prefix . 'yikes_easy_mc_forms';

		/*
		*	Setup charset to prevent ???s saved into the database for special charset
		*	@sinec 6.0.3.8
		*	Resource: http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
		*/
		$charset_collate = $wpdb->get_charset_collate();

		// create the Yikes Inc. Easy Mailchimp database table
		$sql = "CREATE TABLE $custom_table_name (
		id INT NOT NULL AUTO_INCREMENT,
		list_id TEXT NOT NULL,
		form_name TEXT NOT NULL,
		form_description TEXT NOT NULL,
		fields TEXT NOT NULL,
		custom_styles TEXT NOT NULL,
		custom_template TEXT NOT NULL,
		send_welcome_email INT NOT NULL,
		redirect_user_on_submit INT NOT NULL,
		redirect_page TEXT NOT NULL,
		submission_settings TEXT NOT NULL,
		optin_settings TEXT NOT NULL,
		form_settings TEXT NOT NULL,
		error_messages TEXT NOT NULL,
		custom_notifications TEXT NOT NULL,
		impressions INT NOT NULL,
		submissions INT NOT NULL,
		custom_fields TEXT NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		// create an option for the date that the user initially activated the plugin
		// used to display a two week notice, asking for a review or to upgrade
		if ( ! get_option( 'yikes_easy_mailchimp_activation_date' ) || get_option( 'yikes_easy_mailchimp_activation_date' ) == '' ) {
			update_option( 'yikes_easy_mailchimp_activation_date', strtotime( 'now' ) );
		}

		// Create an option for the forms.
		if ( class_exists( 'Yikes_Inc_Easy_Mailchimp_Extender_Option_Forms' ) ) {
			$option_class = new Yikes_Inc_Easy_Mailchimp_Extender_Option_Forms();
			$option_class->create_option();
		}

		// Add the DB version option.
		add_option( 'yikes_easy_mailchimp_extender_version', YIKES_MC_VERSION );
	}
}
