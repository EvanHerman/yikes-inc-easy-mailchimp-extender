<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use Closure;
use YIKES\EasyForms\Plugin;
use YIKES\EasyForms\Settings\DisableFrontEndCss;

/**
 * Class StyleAsset.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Assets
 * @author  Freddie Mixell
 */
final class StyleAsset extends BaseAsset {

	const MEDIA_ALL    = 'all';
	const MEDIA_PRINT  = 'print';
	const MEDIA_SCREEN = 'screen';
	const DEPENDENCIES = [];
	const VERSION      = Plugin::VERSION;
	const DISABLEABLE  = false;

	const DEFAULT_EXTENSION = 'css';

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
	 * Media for which the asset is defined.
	 *
	 * @since %VERSION%
	 *
	 * @var string
	 */
	protected $media;

	/**
	 * Whether this asset can be disabled.
	 *
	 * @since %VERSION%
	 *
	 * @var string
	 */
	protected $disableable;

	/**
	 * Instantiate a StyleAsset object.
	 *
	 * @since %VERSION%
	 *
	 * @param string           $handle       Handle of the asset.
	 * @param string           $source       Source location of the asset.
	 * @param array            $dependencies Optional. Dependencies of the asset.
	 * @param string|bool|null $version      Optional. Version of the asset.
	 * @param string           $media        Media for which the asset is defined.
	 * @param bool             $disableable  Whether this script can be disabled.
	 */
	public function __construct(
		$handle,
		$source,
		$dependencies = self::DEPENDENCIES,
		$version = self::VERSION,
		$media = self::MEDIA_ALL,
		$disableable = self::DISABLEABLE
	) {
		$this->handle       = $handle;
		$this->source       = $this->normalize_source( $source, static::DEFAULT_EXTENSION );
		$this->dependencies = (array) $dependencies;
		$this->version      = $version;
		$this->media        = $media;
		$this->disableable  = $disableable;
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

			if ( $this->is_disabled() ) {
				return;
			}

			wp_register_style(
				$this->handle,
				$this->source,
				$this->dependencies,
				$this->version,
				$this->media
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
			wp_enqueue_style( $this->handle );
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
			wp_dequeue_style( $this->handle );
		};
	}

	/**
	 * Whether the current style is disabled.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	private function is_disabled() {
		if ( ! $this->disableable ) {
			return false;
		}

		$setting = ( new DisableFrontEndCss() )->get();
		return isset( $setting[ $this->handle ] ) && true === $setting[ $this->handle ];
	}
}
