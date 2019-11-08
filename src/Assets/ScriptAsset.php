<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use Yikes\EasyForms\Exception\FailedToRegister;
use Yikes\EasyForms\Plugin;
use Closure;

/**
 * Class ScriptAsset.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Assets
 * @author  Freddie Mixell
 */
class ScriptAsset extends BaseAsset {

	const ENQUEUE_HEADER = false;
	const ENQUEUE_FOOTER = true;

	const DEFAULT_EXTENSION = 'js';

	const VERSION = Plugin::VERSION;

	/**
	 * Source location of the asset.
	 *
	 * @since %VERSION%
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Dependencies of the asset.
	 *
	 * @since %VERSION%
	 *
	 * @var string[]
	 */
	protected $dependencies;

	/**
	 * Version of the asset.
	 *
	 * @since %VERSION%
	 *
	 * @var string|bool|null
	 */
	protected $version;

	/**
	 * Whether to enqueue the script in the footer.
	 *
	 * @since %VERSION%
	 *
	 * @var bool
	 */
	protected $in_footer;

	/**
	 * Localization data that is added to the JS space.
	 *
	 * @since %VERSION%
	 *
	 * @var array
	 */
	protected $localizations = [];

	/**
	 * Instantiate a ScriptAsset object.
	 *
	 * @since %VERSION%
	 *
	 * @param string           $handle       Handle of the asset.
	 * @param string           $source       Source location of the asset.
	 * @param array            $dependencies Optional. Dependencies of the
	 *                                       asset.
	 * @param string|bool|null $version      Optional. Version of the asset.
	 * @param bool             $in_footer    Whether to enqueue the asset in
	 *                                       the footer.
	 */
	public function __construct(
		$handle,
		$source,
		$dependencies = [],
		$version = self::VERSION,
		$in_footer = self::ENQUEUE_HEADER
	) {
		$this->handle       = $handle;
		$this->source       = $this->normalize_source( $source, static::DEFAULT_EXTENSION );
		$this->dependencies = (array) $dependencies;
		$this->version      = $version;
		$this->in_footer    = $in_footer;
	}

	/**
	 * Add a localization to the script.
	 *
	 * @since %VERSION%
	 *
	 * @param string $object_name Name of the object to create in JS space.
	 * @param array  $data_array  Array of data to attach to the object.
	 *
	 * @return static
	 */
	public function add_localization( $object_name, $data_array ) {
		$this->localizations[ $object_name ] = $data_array;

		return $this;
	}

	/**
	 * Get the enqueue closure to use.
	 *
	 * @since %VERSION%
	 *
	 * @return Closure
	 */
	protected function get_register_closure() {
		return function () {
			if ( wp_script_is( $this->handle, 'registered' ) ) {
				return;
			}

			wp_register_script(
				$this->handle,
				$this->source,
				$this->dependencies,
				$this->version,
				$this->in_footer
			);
		};
	}

	/**
	 * Get the enqueue closure to use.
	 *
	 * @since %VERSION%
	 *
	 * @return Closure
	 */
	protected function get_enqueue_closure() {
		return function () {
			if ( ! wp_script_is( $this->handle, 'registered' ) ) {
				throw FailedToRegister::asset_not_registered( $this->handle );
			}

			foreach ( $this->localizations as $object_name => $data_array ) {
				wp_localize_script( $this->handle, $object_name, $data_array );
			}

			wp_enqueue_script( $this->handle );
		};
	}

	/**
	 * Get the dequeue closure to use.
	 *
	 * @since %VERSION%
	 *
	 * @return Closure
	 */
	protected function get_dequeue_closure() {
		return function () {
			wp_dequeue_script( $this->handle );
		};
	}
}
