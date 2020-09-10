<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

use YIKES\EasyForms\Exception\InvalidField;

interface Field {

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render();
}
