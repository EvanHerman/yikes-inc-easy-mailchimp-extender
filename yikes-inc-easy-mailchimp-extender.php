<?php
/*
#_________________________________________________ PLUGIN
Plugin Name: Easy MailChimp Forms
Plugin URI: http://www.yikesinc.com/services/yikes-inc-easy-mailchimp-extender/
Description: Mailchimp integration in the form of a shortcode, php snippet or widget. Now track account status, campaign stats, view subscribers and so much more!
Version: 5.0.6
Author: YIKES Inc
Author URI: http://yikesinc.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012-14 YIKES, Inc (email : tech@yikesinc.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#_________________________________________________ CONSTANTS

/** Configuration **/
if(!defined('YKSEME_DEBUG'))						define('YKSEME_DEBUG',		         false);
if(!defined('YKSEME_VERSION_CURRENT'))				define('YKSEME_VERSION_CURRENT',	'5.0.6');
if(!defined('YKSEME_REQ_PHP'))						define('YKSEME_REQ_PHP',			'5.0');
if(!defined('YKSEME_AUTHOR'))						define('YKSEME_AUTHOR',				'YIKES Inc');
if(!defined('YKSEME_SITE'))							define('YKSEME_SITE',				site_url().'/');
if(!defined('YKSEME_PREFIX'))						define('YKSEME_PREFIX',				'ykseme_');
if(!defined('YKSEME_PATH'))							define('YKSEME_PATH',				ABSPATH.'wp-content/plugins/yikes-inc-easy-mailchimp-extender/');
if(!defined('YKSEME_URL'))							define('YKSEME_URL',				plugins_url('yikes-inc-easy-mailchimp-extender/'));
if(!defined('YKSEME_URL_WP'))						define('YKSEME_URL_WP',				get_bloginfo('url'));
if(!defined('YKSEME_URL_WP_ADM'))					define('YKSEME_URL_WP_ADM',			YKSEME_URL_WP.'/wp-admin/');
/** Database Tables **/
if(!defined('YKSEME_OPTION'))						define('YKSEME_OPTION',				YKSEME_PREFIX.'storage');
// Conditional check for SSL enabled site
if(!defined('YKSEME_URL_WP_AJAX')) {
   if ( is_ssl() ) {
		define('YKSEME_URL_WP_AJAX', admin_url('admin-ajax.php', 'https'));
	} else {
		define('YKSEME_URL_WP_AJAX', admin_url('admin-ajax.php', 'http'));
	}
}
if(!defined('YKSEME_URL_CURRENT'))					define('YKSEME_URL_CURRENT',		$_SERVER['REQUEST_URI']);


/** Localization **/
// include translated files
function yks_mc_text_domain_init() {
	load_plugin_textdomain('yikes-inc-easy-mailchimp-extender', false, dirname(plugin_basename(__FILE__)) . '/languages'); 
}
add_action('init', 'yks_mc_text_domain_init');

/** Initial Configuration **/
if(YKSEME_DEBUG) error_reporting(E_ALL ^ E_NOTICE);

/** Include Required Plugin Files **/
require_once YKSEME_PATH.'classes/class.yksemeBase.php';
require_once YKSEME_PATH.'classes/MCAPI_2.0.class.php';
require_once YKSEME_PATH.'lib/lib.ajax.php';
require_once YKSEME_PATH.'lib/lib.func.php';


/** Initialize the plugin's base class **/
$yksemeBase	= new yksemeBase();


/** Activation Hooks **/
register_activation_hook(__FILE__,		array(&$yksemeBase, 'activate'));
register_deactivation_hook(__FILE__,	array(&$yksemeBase, 'deactivate'));
register_uninstall_hook(__FILE__,		array('yksemeBase', 'uninstall'));