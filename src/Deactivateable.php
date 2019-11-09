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
 * Interface Deactivateable
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
interface Deactivateable {

	/**
	 * Deactivate the service.
	 *
	 * @since %VERSION%
	 */
	public function deactivate();
}