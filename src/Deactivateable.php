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
 * Interface Deactivateable
 *
 * @since   %VERSION%
 * @package Yikes\EasyForms
 */
interface Deactivateable {

	/**
	 * Deactivate the service.
	 *
	 * @since %VERSION%
	 */
	public function deactivate();
}