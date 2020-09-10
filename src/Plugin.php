<?php
/**
 * YIKES Inc. Easy Mailchimp Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

use YIKES\EasyForms\Assets\AssetsAware;
use YIKES\EasyForms\Assets\AssetsHandler;
use YIKES\EasyForms\Exception\InvalidClass;
use YIKES\EasyForms\CustomPostType\EasyForms;

/**
 * Class Plugin.
 *
 * Main plugin controller class that hooks the plugin's functionality into the
 * WordPress request lifecycle.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 */
final class Plugin implements Registerable {

	use PluginHelper;

	const VERSION = '7.0.0';

	/**
	 * Assets handler instance.
	 *
	 * @since %VERSION%
	 *
	 * @var AssetsHandler
	 */
	protected $assets_handler;

	/**
	 * Container instance.
	 *
	 * @since %VERSION%
	 * @var Container
	 */
	protected $container;

	/**
	 * Array of registered services.
	 *
	 * @since %VERSION%
	 * @var Service[]
	 */
	private $services = [];

	/**
	 * Instantiate a Plugin object.
	 *
	 * @since %VERSION%
	 *
	 * @param Container     $container      The container object.
	 * @param AssetsHandler $assets_handler Optional. Instance of the assets handler to use.
	 */
	public function __construct( Container $container, AssetsHandler $assets_handler = null ) {
		$this->container      = $container;
		$this->assets_handler = $assets_handler ?: new AssetsHandler();
	}

	/**
	 * Register the plugin with the WordPress system.
	 *
	 * @since %VERSION%
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_assets_handler' ] );
		register_activation_hook( $this->get_main_file(), [ $this, 'activate' ] );
		register_deactivation_hook( $this->get_main_file(), [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'register_services' ], 20 );
		add_action( 'plugins_loaded', function() {
			/**
			 * Fires after the Easy Forms plugin has been loaded.
			 *
			 * This runs on the plugins_loaded hook so that other plugins have a chance to hook
			 * in. It also runs on an early priority of 0 so that other plugins hooking in have
			 * a chance to modify our early filters.
			 *
			 * @since %VERSION%
			 *
			 * @param Plugin $emf_plugin The main plugin instance.
			 */
			do_action( 'emf_loaded', $this );
		}, 0 );
	}

	/**
	 * Run activation logic.
	 */
	public function activate() {
		$this->register_services();
		foreach ( $this->services as $service ) {
			if ( $service instanceof Activateable ) {
				$service->activate();
			}
		}

		flush_rewrite_rules();
	}

	/**
	 * Run deactivation logic.
	 */
	public function deactivate() {
		foreach ( $this->services as $service ) {
			if ( $service instanceof Deactivateable ) {
				$service->deactivate();
			}
		}
	}

	/**
	 * Register the individual services of this plugin.
	 *
	 * @since %VERSION%
	 */
	public function register_services() {
		$services = $this->get_services();
		$services = array_map( [ $this, 'instantiate_service' ], $services );
		array_walk( $services, function( Service $service ) {
			$service->register();
		} );
		$this->services = $services;
	}

	/**
	 * Get the list of services to register.
	 *
	 * @since %VERSION%
	 *
	 * @return string[] Array of fully qualified class names.
	 */
	protected function get_services() {
		/**
		 * Fires right before the Easy Forms services are retrieved.
		 *
		 * @param Container $container The services container object.
		 */
		do_action( 'emf_pre_get_services', $this->container );

		return array_keys( $this->container->get_services() );
	}

	/**
	 * Register the assets handler.
	 *
	 * @since %VERSION%
	 */
	public function register_assets_handler() {
		$this->assets_handler->register();
	}

	/**
	 * Return the instance of the assets handler in use.
	 *
	 * @since %VERSION%
	 *
	 * @return AssetsHandler
	 */
	public function get_assets_handler() {
		return $this->assets_handler;
	}

	/**
	 * Instantiate a single service.
	 *
	 * @since %VERSION%
	 *
	 * @param string $class Service class to instantiate.
	 *
	 * @return Service
	 * @throws Exception\InvalidService If the service is not valid.
	 */
	protected function instantiate_service( $class ) {
		if ( ! class_exists( $class ) ) {
			throw InvalidClass::not_found( $class );
		}

		$service = new $class();

		if ( ! ( $service instanceof Service ) ) {
			throw InvalidClass::from_interface( $class, Service::class );
		}

		if ( $service instanceof AssetsAware ) {
			$service->with_assets_handler( $this->assets_handler );
		}

		return $service;
	}

}
