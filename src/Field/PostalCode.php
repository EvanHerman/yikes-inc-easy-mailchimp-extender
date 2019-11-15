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
 * Class PostalCode
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class PostalCode extends BaseInput {

	const TYPE = 'text';

	/**
	 * Render any additional attributes.
	 *
	 * @since %VERSION%
	 */
	protected function render_extra_attributes() {
		parent::render_extra_attributes();
		echo 'autocomplete="postal-code" ';
	}
}
