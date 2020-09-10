<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

use YIKES\EasyForms\PluginHelper;
use YIKES\EasyForms\Shortcode\EasyFormsShortcode;
use YIKES\EasyForms\Util\Debugger;
use YIKES\EasyForms\Recaptcha\Recaptcha;

/**
 * Class PluginFactory
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 */
final class PluginFactory {

	use PluginHelper;

	/**
	 * Create and return an instance of the plugin.
	 *
	 * This always returns a shared instance.
	 *
	 * @since %VERSION%
	 *
	 * @return Plugin The plugin instance.
	 */
	public function create() {
		static $plugin = null;

		if ( null === $plugin ) {
			$plugin = new Plugin( $this->get_service_container() );
		}

		return $plugin;
	}

	/**
	 * Get the service container for our class.
	 *
	 * @since %VERSION%
	 * @return Container
	 */
	private function get_service_container() {

		$services = new Container();

		// Start Debugging
		$services->add_service( Debugger::class );

		// Register Shortcode
		$services->add_service( EasyFormsShortcode::class );

		// Register Recaptcha
		$services->add_service( Recaptcha::class );

		return $services;
	}
}