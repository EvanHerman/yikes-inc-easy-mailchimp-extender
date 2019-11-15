<?php
/**
 * YIKES Inc. Easy Mailchimp Forms Plugin.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms\Field;

/**
 * Class Phone
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Phone extends BaseInput {
	const TYPE         = 'tel';
	const SANITIZATION = FILTER_SANITIZE_NUMBER_INT;

	/**
	 * Render any additional attributes.
	 *
	 * @since %VERSION%
	 */
	protected function render_extra_attributes() {
		parent::render_extra_attributes();
		echo 'autocomplete="tel" ';
	}
}
