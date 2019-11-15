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
 * Class Email
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
class Email extends BaseInput {
	const TYPE     = 'email';
	const SANITIZE = FILTER_SANITIZE_EMAIL;
}
