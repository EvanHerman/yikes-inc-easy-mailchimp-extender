<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.yikesinc.com/
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
 * @author     YIKES Inc. <info@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Error_Logging {
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
		$this->yikes_inc_easy_mailchimp_extender = 'yikes-inc-easy-mailchimp-extender';
		add_action( 'wp_head' , array( $this , 'testing_it_out' ) );
	}
	
	public function testing_it_out() {
		echo 'test';
	}

	/*
	*  ytks_mc_generate_error_log_table()
	*  generate our erorr log table on the options settings page
	*
	*  @since 5.2
	*/	
	public function yks_mc_generate_error_log_table() {					
		$error_log_contents = file_get_contents( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' , true );							
		if ( $error_log_contents != '' ) {
			return $error_log_contents;
		} else {
			return _e( 'Error Log Empty' , $this->yikes_inc_easy_mailchimp_extender );
		}
	}
	
}