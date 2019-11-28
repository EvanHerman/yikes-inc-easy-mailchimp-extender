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
$form_data     = $this->form_data;
$title         = $this->title;
$description   = $this->description;
$form_id       = $this->form_id;
$form_settings = $this->form_settings;
$form_classes  = $this->form_classes;

?>
<section
	id="yikes-mailchimp-container-<?php echo $form_id; ?>"
	class="yikes-mailchimp-container yikes-mailchimp-container-<?php echo $form_id; ?> <?php echo apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ); ?>"
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
	<h3 class="yikes-mailchimp-form-title yikes-mailchimp-form-title-<?php echo absint( $form_id ); ?>"><?php echo esc_html( $title ); ?></h3>

	<!-- Form Description -->
	<section class="yikes-mailchimp-form-description yikes-mailchimp-form-description-<?php echo esc_attr( $form_id ); ?>"><?php echo esc_html( $description ); ?></section>

	<form method="POST"
		id="<?php echo esc_attr( $form_data['form_name'] ); ?>-<?php echo absint( $form_id ); ?>"
		class="<?php echo $form_classes; ?>"
		data-attr-form-id="<?php echo absint( $form_id ); ?>"
	>
		<!-- Form Fields -->
		<?php //$form->render(); ?>
		<?php
			// Show Recaptcha If Enabled.
			//$form->recaptcha();
		?>
		<!-- Submit Button -->
		<?php //$form->submit_button(); ?>
	</form>


<?php
// Form Edit Link
 echo $this->edit_form_link;
?>

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
	$this->form->update_form_field( $form_id, 'impressions', $impressions );
}

?>
</section>

