<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

// use Yikes\EasyForms\PluginHelper;
// use Yikes\EasyForms\Roles\Administrator;
// use Yikes\EasyForms\AdminPage\SettingsPage;
// use Yikes\EasyForms\Settings\SettingsManager;

/**
 * Class PluginFactory
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms
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

		// Settings & Settings Page
		// $services->add_service( SettingsPage::class );
		// $services->add_service( SettingsManager::class );

		// Roles
		// $services->add_service( Administrator::class );

		return $services;
	}
}