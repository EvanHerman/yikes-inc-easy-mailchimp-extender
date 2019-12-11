<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Model;

use YIKES\EasyForms\Field\Types;

interface OptinMeta {

    const EMAIL     = 'email';
    const TEXT      = 'text';
    const NUMBER    = 'number';
    const URL       = 'url';
    const IMAGE_URL = 'imageurl';
    const PHONE     = 'phone';
    const ZIP       = 'zip';
    const ADDRESS   = 'address';
    const DATE      = 'date';
    const BIRTHDAY  = 'birthday';
    const DROPDOWN  = 'dropdown';
    const RADIO     = 'radio';
    const CHECKBOX  = 'checkbox';

    const FIELD_MAP = [
		self::EMAIL     => Types::EMAIL,
		self::TEXT      => Types::TEXT,
	];
}
