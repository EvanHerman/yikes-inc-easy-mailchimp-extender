<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use YIKES\EasyForms\Exception\InvalidAssetHandle;

/**
 * Trait AssetsAwareness
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 */
trait AssetsAwareness {

	/**
	 * Assets handler instance to use.
	 *
	 * @since %VERSION%
	 *
	 * @var AssetsHandler
	 */
	protected $assets_handler;

	/**
	 * Array of asset objects.
	 *
	 * @since %VERSION%
	 * @var Asset[]
	 */
	protected $assets = [];

	/**
	 * Get the array of known assets.
	 *
	 * @since %VERSION%
	 *
	 * @return Asset[]
	 */
	protected function get_assets() {
		if ( empty( $this->assets ) ) {
			$this->load_assets();
		}

		return $this->assets;
	}

	/**
	 * Register the known assets.
	 *
	 * @since %VERSION%
	 */
	protected function register_assets() {
		foreach ( $this->get_assets() as $asset ) {
			$this->assets_handler->add( $asset );
		}
	}

	/**
	 * Enqueue the known assets.
	 *
	 * @since %VERSION%
	 *
	 * @throws InvalidAssetHandle If the passed-in asset handle is not valid.
	 */
	protected function enqueue_assets() {
		foreach ( $this->get_assets() as $asset ) {
			$this->assets_handler->enqueue( $asset );
		}
	}

	/**
	 * Enqueue a single asset.
	 *
	 * @since %VERSION%
	 *
	 * @param string $handle Handle of the asset to enqueue.
	 *
	 * @throws InvalidAssetHandle If the passed-in asset handle is not valid.
	 */
	protected function enqueue_asset( $handle ) {
		$this->assets_handler->enqueue_handle( $handle );
	}

	/**
	 * Set the assets handler to use within this object.
	 *
	 * @since %VERSION%
	 *
	 * @param AssetsHandler $assets Assets handler to use.
	 */
	public function with_assets_handler( AssetsHandler $assets ) {
		$this->assets_handler = $assets;
	}

	/**
	 * Load asset objects for use.
	 *
	 * @since %VERSION%
	 */
	protected function load_assets() {
		$this->assets = [];
	}
}
