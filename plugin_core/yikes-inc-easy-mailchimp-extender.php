<?php
/**
 *		Plugin Name:       Easy Forms for MailChimp by YIKES
 *		Plugin URI:        http://www.yikesinc.com/services/yikes-inc-easy-mailchimp-extender/
 * 		Description:       YIKES Easy Forms for MailChimp links your site to MailChimp and allows you to generate and display mailing list opt-in forms anywhere on your site with ease.
 * 		Version:           6.0.3.7
 * 		Author:            YIKES
 * 		Author URI:        http://www.yikesinc.com/
 * 		License:           GPL-3.0+
 * 		License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * 		Text Domain:       yikes-inc-easy-mailchimp-extender
 * 		Domain Path:       /languages
 *
 * 		YIKES Easy Forms for MailChimp is free software: you can redistribute it and/or modify
 * 		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation, either version 2 of the License, or
 * 		any later version.
 *
 * 		YIKES Easy Forms for MailChimp is distributed in the hope that it will be useful,
 * 		but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * 		GNU General Public License for more details.
 *
 * 		You should have received a copy of the GNU General Public License
 *		along with Easy Forms for MailChimp. If not, see <http://www.gnu.org/licenses/>.
 *
 *		We at Yikes Inc. embrace the open source philosophy on a daily basis. We donate company time back to the WordPress project,
 *		and constantly strive to improve the WordPress project and community as a whole. We eat, sleep and breath WordPress.
 *
 *		"'Free software' is a matter of liberty, not price. To understand the concept, you should think of 'free' as in 'free speech,' not as in 'free beer'."
 *		- Richard Stallman
 *
**/
 
// 	If accessed directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * 	Define path constant to our plugin directory.
 *
 * 	@since 6.0.0
 *	@return void
 */
if ( ! defined( 'YIKES_MC_PATH' ) ) {
	define( 'YIKES_MC_PATH' , plugin_dir_path( __FILE__ ) );
}

/**
 * 	Define URL constant to our plugin directory.
 *
 * 	@since 6.0.0
 *	@return void
 */
if ( ! defined( 'YIKES_MC_URL' ) ) {
	define( 'YIKES_MC_URL' , plugin_dir_url( __FILE__ ) );
}

/**
 *	activate_yikes_inc_easy_mailchimp_extender();
 * 	Fires during activation.
 *
 * 	This action is documented in includes/class-yikes-inc-easy-mailchimp-extender-activator.php
 * 	and carries out some important tasks such as creating our custom database table if it doesn't
 * 	already exist, and defining default options.
 *
 * 	@since 6.0.0
  *	@return void
 */
register_activation_hook( __FILE__, 'activate_yikes_inc_easy_mailchimp_extender' );
function activate_yikes_inc_easy_mailchimp_extender( $network_wide ) {
	require_once YIKES_MC_PATH . 'includes/class-yikes-inc-easy-mailchimp-extender-activator.php';
    add_option( 'yikes_mailchimp_activation_redirect', 'true' );
	Yikes_Inc_Easy_Mailchimp_Extender_Activator::activate( $network_wide );
}

/**
 *	uninstall_yikes_inc_easy_mailchimp_extender();
 * 	The code that runs during uninstall.
 *
 * 	This action is documented in includes/class-yikes-inc-easy-mailchimp-extender-uninstall.php
 *	and carries out the deletion of MailChimp transients, plugin options and MailChimp form tables.
 *
 * @since 6.0.0
  *	@return void
 */
register_deactivation_hook( __FILE__, 'deactivate_yikes_inc_easy_mailchimp_extender' ); 
function deactivate_yikes_inc_easy_mailchimp_extender() {
	// delete the activation re-driect option
	update_option( 'yikes_mailchimp_activation_redirect', 'true' );
}

/**
 *	uninstall_yikes_inc_easy_mailchimp_extender();
 * 	The code that runs during uninstall.
 *
 * 	This action is documented in includes/class-yikes-inc-easy-mailchimp-extender-uninstall.php
 *	and carries out the deletion of MailChimp transients, plugin options and MailChimp form tables.
 *
 * @since 6.0.0
  *	@return void
 */
register_uninstall_hook( __FILE__, 'uninstall_yikes_inc_easy_mailchimp_extender' ); 
function uninstall_yikes_inc_easy_mailchimp_extender() {
	require_once YIKES_MC_PATH . 'includes/class-yikes-inc-easy-mailchimp-extender-uninstall.php';
	Yikes_Inc_Easy_Mailchimp_Extender_Uninstaller::uninstall();
}

/**
 * 	Multi-site blog creation
 *
 *	If a new blog is created on a mutli-site network
 *	we should run our activation hook to create the necessary form table
 * 
 * 	@since 6.0.0
  *	@return void
 */
 add_action( 'wpmu_new_blog', 'yikes_easy_mailchimp_new_network_site', 10, 6); 
 function yikes_easy_mailchimp_new_network_site($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
	global $switched;
    if ( is_plugin_active_for_network( 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php' ) ) {
		require_once YIKES_MC_PATH . 'includes/class-yikes-inc-easy-mailchimp-extender-activator.php';
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        Yikes_Inc_Easy_Mailchimp_Extender_Activator::activate( $networkwide=null );
        switch_to_blog($old_blog);
    }
}

/**
 * The base plugin class
 * admin-specific hooks, filters and all functionality
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yikes-inc-easy-mailchimp-extender.php';

/**
 * 	Begins execution of the plugin.
 *
 * 	@since 6.0.0
 *	@return Yikes_Inc_Easy_Mailchimp_Extender
 */
function run_yikes_inc_easy_mailchimp_extender() {
	$plugin = new Yikes_Inc_Easy_Mailchimp_Extender();
	$plugin->run();
}
run_yikes_inc_easy_mailchimp_extender();


/*
*	Enjoy this wonderfully powerful (and free) plugin.
*	~<|:D
*/