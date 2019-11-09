<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\View;

use YIKES\EasyForms\PluginHelper;

/**
 * Class NoOverrideLocationView
 *
 * This class works like TemplatedView, but does not allow overriding the
 * template file in a theme.
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class NoOverrideLocationView extends TemplatedView {

	use PluginHelper;

	/**
	 * Get the possible locations for the view.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri URI of the view to get the locations for.
	 *
	 * @return array Array of possible locations.
	 */
	protected function get_locations( $uri ) {
		return [
			trailingslashit( $this->get_root_dir() ) . $uri,
		];
	}
}
