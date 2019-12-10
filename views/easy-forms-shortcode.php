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
	id="yikes-mailchimp-container-<?= $form_id; ?>"
	class="yikes-mailchimp-container yikes-mailchimp-container-<?= $form_id; ?> <?= apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ); ?>"
>
<?php
/*
*  pre-form action hooks
*  check readme for usage examples
*/
do_action( 'yikes-mailchimp-before-form', $form_id, $form_data );

?>	
<?php
$debug = new Debugger();

$debug->pretty_log();
$debug->pretty_debug( '$this', $this );
$debug->pretty_debug( '$form_settings', $form_settings );
$debug->pretty_debug( '$form_data', $form_data );

?>
	<!-- Form Title -->
	<h3 class="yikes-mailchimp-form-title yikes-mailchimp-form-title-<?= absint( $form_id ); ?>"><?= esc_html( $title ); ?></h3>

	<!-- Form Description -->
	<section class="yikes-mailchimp-form-description yikes-mailchimp-form-description-<?= esc_attr( $form_id ); ?>"><?= esc_html( $description ); ?></section>

	<form method="POST"
		id="<?= esc_attr( $form_data['form_name'] ); ?>-<?= absint( $form_id ); ?>"
		class="<?= $form_classes; ?>"
		data-attr-form-id="<?= absint( $form_id ); ?>"
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

