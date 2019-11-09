<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use YIKES\EasyForms\Registerable;

/**
 * Interface Asset.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\Assets
 * @author  Freddie Mixell
 */
interface Asset extends Registerable {

	/**
	 * Enqueue the asset.
	 *
	 * @since %VERSION%
	 */
	public function enqueue();

	/**
	 * Dequeue the asset.
	 *
	 * @since %VERSION%
	 */
	public function dequeue();

	/**
	 * Get the handle of the asset.
	 *
	 * @since %VERSION%
	 *
	 * @return string
	 */
	public function get_handle();
}
