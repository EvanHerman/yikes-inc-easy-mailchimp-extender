<?php
/**
 * Autoloader.
 */

// Bail if WordPress isn't loaded.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Custom autoloader for the Easy MailChimp Extender plugin.
 *
 * @author Jeremy Pry
 * @since 6.2.0
 *
 * @param string $class The name of the class to autoload.
 */
function yikes_inc_easy_mailchimp_extender_autoloader( $class ) {
	static $map = null;
	if ( null === $map ) {
		$map = require( dirname( __FILE__ ) . '/class-map.php' );
	}

	$class = strtolower( $class );
	if ( isset( $map[ $class ] ) ) {
		require_once( dirname( __FILE__ ) . "/{$map[ $class ]}" );
	}
}

spl_autoload_register( 'yikes_inc_easy_mailchimp_extender_autoloader' );
