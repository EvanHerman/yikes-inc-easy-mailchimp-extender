<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms;

/**
 * Interface Renderable.
 *
 * An object that can be `render()`ed.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 */
interface Renderable {
	/**
	 * Render the current Renderable.
	 *
	 * @since %VERSION%
	 *
	 * @param array $context Context in which to render.
	 *
	 * @return string Rendered HTML.
	 */
	public function render( array $context = [] );
}
