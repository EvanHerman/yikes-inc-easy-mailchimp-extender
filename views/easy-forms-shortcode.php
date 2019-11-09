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

$form->login_required();

?>
<?php 

$form->form_schedule();

ob_start();
?>
<section
	id="yikes-mailchimp-container-<?php echo $this->form_id; ?>"
	class="yikes-mailchimp-container yikes-mailchimp-container-<?php echo $this->form_id; ?> <?php echo apply_filters( 'yikes-mailchimp-form-container-class', '', $this->form_id ); ?>"
>
<?php
/*
*  pre-form action hooks
*  check readme for usage examples
*/
do_action( 'yikes-mailchimp-before-form', $this->form_id, $this->form_data );

?>
	<!-- Form Title -->
	<?php $this->form_title(); ?>

	<!-- Form Description -->
	<?php $this->form_description(); ?>

	<form method="POST"
		id="<?php $form->form_id_prop(); ?>"
		class="<?php $form->form_classes(); ?>"
	>
		<!-- Form Fields -->
		<?php $form->render(); ?>
		<?php
			// Show Recaptcha If Enabled.
			$form->recaptcha();
		?>
		<!-- Submit Button -->
		<?php $form->submit_button(); ?>
	</form>


<?php
// Form Edit Link
$form->edit_form_link();
?>

<?php
/*
*  post-form action hooks
*  check readme for usage examples
*/
do_action( 'yikes-mailchimp-after-form', $this->form_id, $this->form_data );

?>
</section>
<?php

return ob_get_clean();
