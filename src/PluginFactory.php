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
// use YIKES\EasyForms\Roles\Administrator;
// use YIKES\EasyForms\AdminPage\SettingsPage;
// use YIKES\EasyForms\Settings\SettingsManager;

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

		// Register Shortcode
		$services->add_service( EasyFormsShortcode::class );

		// Settings & Settings Page
		// $services->add_service( SettingsPage::class );
		// $services->add_service( SettingsManager::class );

		// Roles
		// $services->add_service( Administrator::class );

		return $services;
	}
}