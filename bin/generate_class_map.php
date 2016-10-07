<?php
/**
 * Utility file for generating a class map file.
 */

// Return if CLI isn't being used.
if ( 'cli' !== php_sapi_name() ) {
	die();
}

// Determine our project root.
$plugin_root_dir = dirname( __DIR__ );

// Load the composer autoloader.
if ( ! file_exists( $plugin_root_dir . '/vendor/autoload.php' ) ) {
	die( "You need to run `composer install` before attempting to use this file." );
}

require_once( $plugin_root_dir . '/vendor/autoload.php' );

use Symfony\Component\ClassLoader\ClassMapGenerator;

// Create the raw map.
$raw_map = array();
$locations = array( 'admin', 'includes', 'public' );
foreach ( $locations as $location ) {
	$raw_map = array_merge( $raw_map, ClassMapGenerator::createMap( "{$plugin_root_dir}/{$location}" ) );
}

// Modify the default class map.
$map = array();
foreach ( $raw_map as $class => $path ) {
	$map[ strtolower( $class ) ] = str_replace( $plugin_root_dir . DIRECTORY_SEPARATOR, '', $path );
}

// Save the map to the correct file.
file_put_contents( $plugin_root_dir . '/class-map.php', sprintf( "<?php\n\nreturn %s;", var_export( $map, true ) ) );
