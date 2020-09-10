<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms;

// Only run this within WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

?>

<!-- Form Title -->
<h3 class="yikes-mailchimp-form-title yikes-mailchimp-form-title-<?= absint( $this->form_id ); ?>"><?= esc_html( $this->title ); ?></h3>