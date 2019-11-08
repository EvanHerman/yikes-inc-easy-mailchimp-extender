<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package Yikes\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace Yikes\EasyForms;

/**
 * Interface Registerable.
 *
 * An object that can be `register()`ed.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms
 * @author  Freddie Mixell
 */
interface Registerable {

	/**
	 * Register the current Registerable.
	 *
	 * @since %VERSION%
	 */
	public function register();
}