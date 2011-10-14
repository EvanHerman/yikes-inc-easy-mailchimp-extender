<?php
/*
#_________________________________________________ PLUGIN
Plugin Name: Yikes' Mailchimp Form
Plugin URI: http://www.yikesinc.com
Description: Mailchimp API integration in the form of a shortcode
Version: 1.0
Author: Sean Kennedy
Author URI: http://www.yikesinc.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2010 Sean Kennedy (email : sean@yikesinc.com)

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
if(!defined('YKSMF_DEBUG'))									define('YKSMF_DEBUG',				          true);
if(!defined('YKSMF_VERSION_CURRENT'))				define('YKSMF_VERSION_CURRENT',				'1.0');
if(!defined('YKSMF_REQ_PHP'))								define('YKSMF_REQ_PHP',								'5.0');
if(!defined('YKSMF_AUTHOR'))								define('YKSMF_AUTHOR',								'Sean Kennedy');
if(!defined('YKSMF_SITE'))									define('YKSMF_SITE',									site_url().'/');
if(!defined('YKSMF_PREFIX'))								define('YKSMF_PREFIX',								'yksmf_');
if(!defined('YKSMF_PATH'))									define('YKSMF_PATH',									ABSPATH.'wp-content/plugins/yks-mailchimp-form/');
if(!defined('YKSMF_URL'))										define('YKSMF_URL',										plugins_url('yks-mailchimp-form/'));
if(!defined('YKSMF_URL_WP'))								define('YKSMF_URL_WP',								get_bloginfo('url'));
if(!defined('YKSMF_URL_WP_ADM'))						define('YKSMF_URL_WP_ADM',						YKSMF_URL_WP.'/wp-admin/');
if(!defined('YKSMF_URL_WP_AJAX'))						define('YKSMF_URL_WP_AJAX',						admin_url('admin-ajax.php'));
if(!defined('YKSMF_URL_CURRENT'))						define('YKSMF_URL_CURRENT',						$_SERVER['REQUEST_URI']);

/** Database Tables **/
if(!defined('YKSMF_OPTION'))								define('YKSMF_OPTION',								YKSMF_PREFIX.'storage');

/** Include Required Plugin Files **/
require_once YKSMF_PATH.'classes/class.yksmfBase.php';
require_once YKSMF_PATH.'classes/MCAPI.class.php';
require_once YKSMF_PATH.'lib/lib.ajax.php';

/** Initial Configuration **/
if(YKSMF_DEBUG) error_reporting(E_ALL ^ E_NOTICE);

/** Initialize the plugin's base class **/
$yksmfBase			= new yksmfBase();

/** Activation Hooks **/
register_activation_hook(__FILE__,		array(&$yksmfBase, 'activate'));
register_deactivation_hook(__FILE__,	array(&$yksmfBase, 'deactivate'));
register_uninstall_hook(__FILE__,			array(&$yksmfBase, 'uninstall'));
?>