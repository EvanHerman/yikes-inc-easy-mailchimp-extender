<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

// Don't allow loading outside of WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
// Load and register the autoloader.
require_once __DIR__ . '/Autoloader.php';
$yikes_easy_forms_autoloader = new Autoloader();
$yikes_easy_forms_autoloader->add_namespace( __NAMESPACE__, __DIR__ );
$yikes_easy_forms_autoloader->register();
