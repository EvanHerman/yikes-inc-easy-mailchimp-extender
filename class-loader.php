<?php
/**
 * Autoloader.
 *
 * @package easy-mailchimp-extender
 */

// Bail if WordPress isn't loaded.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Custom autoloader for the Easy Mailchimp Extender plugin.
 *
 * @author Jeremy Pry
 * @since 6.2.0
 *
 * @param string $class_name The name of the class to autoload.
 */
function yikes_inc_easy_mailchimp_extender_autoloader( $class_name ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	static $map = null;
	if ( null === $map ) {
		$map = require __DIR__ . '/class-map.php';
	}

	$class = strtolower( $class_name );
	if ( isset( $map[ $class ] ) ) {
		require_once __DIR__ . "/{$map[ $class ]}";
	}
}

spl_autoload_register( 'yikes_inc_easy_mailchimp_extender_autoloader' );
