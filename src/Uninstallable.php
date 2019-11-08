<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package Yikes\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms;

/**
 * Interface Uninstallable.
 *
 * An object that can be uninstalled.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms
 * @author  Freddie Mixell
 */
interface Uninstallable {

	/**
	 * Uninstall the Uninstallable component.
	 *
	 * @since %VERSION%
	 */
	public function uninstall();
}
