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
 * Class Checkbox
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Checkbox extends BaseInput {
	const TYPE = 'checkbox';

	/**
	 * The value for the field.
	 *
	 * Default checkbox values to 1.
	 *
	 * @since %VERSION%
	 * @var int.
	 */
	protected $value = 1;
}
