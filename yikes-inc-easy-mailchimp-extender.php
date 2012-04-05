<?php
/*
#_________________________________________________ PLUGIN
Plugin Name: YIKES, Inc Easy Mailchimp Extender
Plugin URI: http://www.yikesinc.com
Description: Mailchimp API integration in the form of a shortcode or php snippet
Version: 2.0.3
Author: Yikes, Inc, Sean Kennedy, Tracy Levesque, Carlos Zuniga
Author URI: http://www.yikesinc.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012 Yikes, Inc (email : tech@yikesinc.com)

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
if(!defined('YKSEME_DEBUG'))						define('YKSEME_DEBUG',		         true);
if(!defined('YKSEME_VERSION_CURRENT'))				define('YKSEME_VERSION_CURRENT',	'2.0.3');
if(!defined('YKSEME_REQ_PHP'))						define('YKSEME_REQ_PHP',			'5.0');
if(!defined('YKSEME_AUTHOR'))						define('YKSEME_AUTHOR',				'Yikes, Inc, Sean Kennedy, Tracy Levesque, Carlos Zuniga');
if(!defined('YKSEME_SITE'))							define('YKSEME_SITE',				site_url().'/');
if(!defined('YKSEME_PREFIX'))						define('YKSEME_PREFIX',				'ykseme_');
if(!defined('YKSEME_PATH'))							define('YKSEME_PATH',				ABSPATH.'wp-content/plugins/yikes-inc-easy-mailchimp-extender/');
if(!defined('YKSEME_URL'))							define('YKSEME_URL',				plugins_url('yikes-inc-easy-mailchimp-extender/'));
if(!defined('YKSEME_URL_WP'))						define('YKSEME_URL_WP',				get_bloginfo('url'));
if(!defined('YKSEME_URL_WP_ADM'))					define('YKSEME_URL_WP_ADM',			YKSEME_URL_WP.'/wp-admin/');
if(!defined('YKSEME_URL_WP_AJAX'))					define('YKSEME_URL_WP_AJAX',		admin_url('admin-ajax.php'));
if(!defined('YKSEME_URL_CURRENT'))					define('YKSEME_URL_CURRENT',		$_SERVER['REQUEST_URI']);

/** Database Tables **/
if(!defined('YKSEME_OPTION'))						define('YKSEME_OPTION',				YKSEME_PREFIX.'storage');

/** Initial Configuration **/
if(YKSEME_DEBUG) error_reporting(E_ALL ^ E_NOTICE);

/** Include Required Plugin Files **/
require_once YKSEME_PATH.'classes/class.yksemeBase.php';
require_once YKSEME_PATH.'classes/MCAPI.class.php';
require_once YKSEME_PATH.'lib/lib.ajax.php';
require_once YKSEME_PATH.'lib/lib.func.php';

/** Initialize the plugin's base class **/
$yksemeBase			= new yksemeBase();

/** Activation Hooks **/
register_activation_hook(__FILE__,					array(&$yksemeBase, 'activate'));
register_deactivation_hook(__FILE__,				array(&$yksemeBase, 'deactivate'));
register_uninstall_hook(__FILE__,					array(&$yksemeBase, 'uninstall'));

//Check for jquery
$checkJQuery = true;

if(!function_exists('get_option'))
  require_once('../../../wp-config.php');


// Output jquery
add_action('wp_head','yikes_mailch_jquery_js');


function yikes_mailch_jquery_js() {?>
  <script type="ext/javascript" src="<?php echo YKSEME_URL; ?>js/prototype.js"></script>
  <script type="text/javascript">
  jQueryScriptOutputted = <?php echo ($checkJQuery===false?"true":"false");?>;
	function initJQuery() {
		if (typeof($) == 'undefined') {
		
		
			if (! jQueryScriptOutputted) {
				jQueryScriptOutputted = true;
				document.write("<scr" + "ipt type='text/javascript' src='<?php echo YKSEME_URL; ?>js/jquery.1.7.1.min.js'></scr" + "ipt>");
			}
			setTimeout("initJQuery()", 50);
		}
	}
	initJQuery();
  </script>
  
<?php } ?>