<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use YIKES\EasyForms\Service;

/**
 * Class AdminStyles
 *
 * Handles registration of stylesheet for the entire admin area.
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class AdminStyles implements Service, AssetsAware {

	use AssetsAwareness;

	/**
	 * Register the current Registerable.
	 *
	 * @since %VERSION%
	 */
	public function register() {
		$this->register_assets();

		add_action( 'admin_enqueue_scripts', function() {
			$this->enqueue_assets();
		} );
	}

	/**
	 * Load asset objects for use.
	 *
	 * @since %VERSION%
	 */
	protected function load_assets() {
		$this->assets = [
			new StyleAsset( 'easy-forms-admin-css', 'assets/css/admin' ),
		];
	}
}
