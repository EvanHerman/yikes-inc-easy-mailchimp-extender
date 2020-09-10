<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

use YIKES\EasyForms\Util\Debugger;

// Only run this within WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

?>

<!-- Form Description -->
<section class="yikes-mailchimp-form-description yikes-mailchimp-form-description-<?= esc_attr( $this->form_id ); ?>">
    <?= esc_html( $this->description ); ?>
</section>
