<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.yikesinc.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 * @author     YIKES Inc. <info@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		global $wpdb;
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
			`impressions` INT NOT NULL,
			`submissions` INT NOT NULL,
			UNIQUE KEY id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	
	}
	
}
