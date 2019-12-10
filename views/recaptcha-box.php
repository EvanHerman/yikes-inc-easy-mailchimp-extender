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

<div
    class="g-recaptcha"
    data-sitekey="<?= esc_attr( $this->form->recaptcha['site_key'] ); ?>"
    data-theme="<?= esc_attr( $this->form->recaptcha['theme'] ); ?>"
    data-type="<?= esc_attr( $this->form->recaptcha['type'] ); ?>"
    data-size="<?= esc_attr( $this->form->recaptcha['size'] ); ?>"
    data-callback="<?= esc_attr( $this->form->recaptcha['success_callback'] ); ?>"
    data-expired-callback="<?= esc_attr( $this->form->recaptcha['expired_callback'] ); ?>"
></div>