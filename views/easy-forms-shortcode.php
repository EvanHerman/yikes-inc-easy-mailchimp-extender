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
$form_data             = $this->form_data;
$title                 = $this->title;
$description           = $this->description;
$form_id               = $this->form_id;
$form_settings         = $this->form_settings;
$form_classes          = $this->form_classes;
$edit_form_link        = $this->edit_form_link;
$submit_button_classes = $this->submit_button_classes;
$submit_button_text    = $this->submit_button_text

?>
<section
	id="yikes-mailchimp-container-<?= esc_attr( $form_id ); ?>"
	class="yikes-mailchimp-container yikes-mailchimp-container-<?= esc_attr( $form_id ); ?> <?= esc_attr( apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ) ); ?>"
>
<?php
/*
*  pre-form action hooks
*  check readme for usage examples
*/
do_action( 'yikes-mailchimp-before-form', $form_id, $form_data );

?>
	<?php do_action( 'easy_forms_do_form_title', $this ); ?>

	<?php do_action( 'easy_forms_do_form_description', $this ); ?>

	<form method="POST"
		id="<?= esc_attr( sanitize_title( $form_data['form_name'] ) ); ?>-<?= esc_attr( $form_id ); ?>"
		class="<?= esc_attr( $form_classes ); ?>"
		data-attr-form-id="<?= esc_attr( $form_id ); ?>"
	>
		<!-- Form Fields -->
		<?php $this->form->render(); ?>

		<!-- Show Recaptcha If Enabled -->
		<?php do_action( 'easy_forms_do_recaptcha_box', $this ); ?>

		<button
			type="submit"
			class="<?= esc_attr( $submit_button_classes ); ?>"
		>
			<span class="yikes-mailchimp-submit-button-span-text">
				<?= esc_html( $submit_button_text ); ?>
			</span>
		</button>

	</form>


<?= $edit_form_link; ?>

<?php
/*
*  post-form action hooks
*  check readme for usage examples
*/
do_action( 'yikes-mailchimp-after-form', $this->form_id, $this->form_data );

/*
*	Update the impressions count
*	for non-admins
*/
if ( ! current_user_can( 'manage_options' ) ) {
	$impressions = $form_data['impressions'] + 1;

	$form_data->update_form_field( $form_id, 'impressions', $impressions );
}

?>
</section>

