<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * All media assets should be defined as CONSTs in this class prior to usage.
 * Sample usage: <img src="<?php echo ( new MediaAsset() )->get_image( MediaAsset::BANNER ); ?>">
 *
 * @package   Yikes\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Assets;

use YIKES\EasyForms\PluginHelper;

/**
 * Class MediaAsset.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms\Assets
 * @author  Freddie Mixell
 */
final class MediaAsset {

	use PluginHelper;

	const IMG_ASSETS_DIR = '/assets/images/';
	const IMG_NOT_FOUND  = '';

	/**
	 * Get internal image URL.
	 *
	 * @param string $filename filename of image.
	 *
	 * @return string $file_url
	 */
	public function get_image( $filename ) {
		$relative_file = self::IMG_ASSETS_DIR . $filename;
		$file_url      = $this->get_plugin_url( $relative_file );

		// Build absolute path to file to confirm file exists.
		$file_path = $this->get_root_dir() . $relative_file;

		if ( ! file_exists( $file_path ) ) {
			return self::IMG_NOT_FOUND;
		}

		return $file_url;
	}
}
