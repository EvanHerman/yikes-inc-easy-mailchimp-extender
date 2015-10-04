<?php
/**
 * Fired during plugin activation
 *
 * @link       http://www.yikesinc.com/
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
 * @author     YIKES Inc. <info@yikesinc.com>
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
		// define global switched (required for switch_to_blog())
		global $switched;		
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
		
		// create the Yikes Inc. Easy MailChimp database table
		if( $wpdb->get_var("show tables like '$custom_table_name'") != $custom_table_name ) {
			$sql = "CREATE TABLE " . $custom_table_name . " (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`list_id` TINYTEXT NOT NULL,
			`form_name` MEDIUMTEXT NOT NULL,
			`form_description` LONGTEXT NOT NULL,
			`fields` LONGTEXT NOT NULL,
			`custom_styles` MEDIUMTEXT NOT NULL,
			`custom_template` TINYTEXT NOT NULL,
			`send_welcome_email` INT(1) NOT NULL,
			`redirect_user_on_submit` INT(1) NOT NULL,
			`redirect_page` MEDIUMTEXT NOT NULL,
			`submission_settings` LONGTEXT NOT NULL,
			`optin_settings` LONGTEXT NOT NULL,
			`error_messages` LONGTEXT NOT NULL,
			`custom_notifications` LONGTEXT NOT NULL,
			`impressions` INT NOT NULL,
			`submissions` INT NOT NULL,
			`custom_fields` LONGTEXT NOT NULL,
			UNIQUE KEY id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
			
		// create an option for the date that the user initially activated the plugin
		// used to display a two week notice, asking for a review or to upgrade
		if( !get_option( 'yikes_easy_mailchimp_activation_date' ) || get_option( 'yikes_easy_mailchimp_activation_date' ) == '' ) {
			update_option( 'yikes_easy_mailchimp_activation_date' , strtotime( 'now' ) );
		}
		
	}
	
}