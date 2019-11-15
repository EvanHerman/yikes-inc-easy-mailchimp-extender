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
 * Class Number
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Number extends BaseInput {
	const TYPE     = 'number';
	const SANITIZE = FILTER_SANITIZE_NUMBER_INT;
}
