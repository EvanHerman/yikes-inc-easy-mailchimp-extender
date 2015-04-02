<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.yikesinc.com/
 * @since             1.0.0
 * @package           Yikes_Inc_Easy_Mailchimp_Extender
 *
 * @wordpress-plugin
 * Plugin Name:       Easy MailChimp Forms by Yikes Inc.
 * Plugin URI:        http://www.yikesinc.com/services/yikes-inc-easy-mailchimp-extender/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            YIKES Inc.
 * Author URI:        http://www.yikesinc.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yikes-inc-easy-mailchimp-extender
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If this file is called directly, abort.
if ( ! defined( 'YIKES_MC_PATH' ) ) {
	define( 'YIKES_MC_PATH' , plugin_dir_path( __FILE__ ) );
}

// If this file is called directly, abort.
if ( ! defined( 'YIKES_MC_URL' ) ) {
	define( 'YIKES_MC_URL' , plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yikes-inc-easy-mailchimp-extender-activator.php
 */
function activate_yikes_inc_easy_mailchimp_extender() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yikes-inc-easy-mailchimp-extender-activator.php';
	Yikes_Inc_Easy_Mailchimp_Extender_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yikes-inc-easy-mailchimp-extender-deactivator.php
 */
function deactivate_yikes_inc_easy_mailchimp_extender() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yikes-inc-easy-mailchimp-extender-deactivator.php';
	Yikes_Inc_Easy_Mailchimp_Extender_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yikes_inc_easy_mailchimp_extender' );
register_deactivation_hook( __FILE__, 'deactivate_yikes_inc_easy_mailchimp_extender' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yikes-inc-easy-mailchimp-extender.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yikes_inc_easy_mailchimp_extender() {

	$plugin = new Yikes_Inc_Easy_Mailchimp_Extender();
	$plugin->run();

}
run_yikes_inc_easy_mailchimp_extender();
