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

/**
 * Class Hidden
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Hidden extends BaseField {

	/**
	 * Render the field.
	 *
	 * @since %VERSION%
	 */
	public function render() {
		$classes = 'emf-field-hidden';
		?>
		<input type="hidden"
			class=""
			name=""
			id=""
			value=""
		/>
		<?php
	}
}
