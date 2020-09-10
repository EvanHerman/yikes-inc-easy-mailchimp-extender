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
 * Interface Types
 *
 * These are the available field types and their class mappings.
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
interface Types {
	const TEXT   = Text::class;
	const EMAIL  = Email::class;
	const HIDDEN = Hidden::class;
}
