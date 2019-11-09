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

/**
 * These variables are included here for easy visibility, but they
 * can also be used as $this->var_name directly.
 */
/** @var \YIKES\EasyForms\Model\OptinForm $form */
$form_data = $this->form_data;

$form_settings = $this->form_settings;
// Set up the classes we'll use for the form and the individual fields.
$form_classes = $form_settings['yikes-easy-mc-form-class-names'];

$inline_form           = $form_settings['yikes-easy-mc-inline-form'];
$submit_button_type    = $form_settings['yikes-easy-mc-submit-button-type'];
$submit_button_text    = esc_attr( $form_settings['yikes-easy-mc-submit-button-text'] );
$submit_button_image   = esc_url( $form_settings['yikes-easy-mc-submit-button-image'] );
$submit_button_classes = esc_attr( $form_settings['yikes-easy-mc-submit-button-classes'] );

// schedules
$form_schedule_state  = $form_settings['yikes-easy-mc-form-schedule'];
$form_schedule_start  = $form_settings['yikes-easy-mc-form-restriction-start'];
$form_schedule_end    = $form_settings['yikes-easy-mc-form-restriction-end'];
$form_pending_message = $form_settings['yikes-easy-mc-form-restriction-pending-message'];
$form_expired_message = $form_settings['yikes-easy-mc-form-restriction-expired-message'];

// register required
$form_login_required = $form_settings['yikes-easy-mc-form-login-required'];
$form_login_message  = $form_settings['yikes-easy-mc-form-restriction-login-message'];

?>
<?php if ( $form->has_errors() ) : ?>
	<div class="emf-form-errors">
		<?php esc_html_e( 'Your form has errors. Please correct the errors below before resubmitting.', 'easy-forms-text-domain' ); ?>
	</div>
<?php endif; ?>
<form method="POST"
	id="<?php echo esc_attr( $this->form_id ); ?>"
	class="<?php echo esc_attr( join( ' ', $form_classes ) ); ?>"
>
	<?php $form->render(); ?>
	<button class="emf-submit" type="submit" name="emf_submit">
		<?php esc_html_e( 'Submit Form', 'easy-forms-text-domain' ); ?>
	</button>
</form>

<?php

// Display an edit link for the application.
$form_edit_link = get_edit_post_link( $this->form_id, 'link' );
if ( $form_edit_link ) {
	printf(
		'<br/><p><a href="%1$s">%2$s</a></p>',
		esc_url( $form_edit_link ),
		esc_html__( 'Edit Form', 'easy-forms-text-domain' )
	);
}