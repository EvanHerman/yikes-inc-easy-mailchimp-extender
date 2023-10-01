<?php
/**
 * Popups Add-On Container
 *
 * Setup the variables for this add-on
 * @since 6.1
 */
$addon_image  = YIKES_MC_URL . 'includes/add_ons/images/popups.png';
$add_on_title = esc_attr__( 'Popups for Easy Forms for Mailchimp', 'yikes-inc-easy-mailchimp-extender' );
$permalink    = esc_url( 'https://codeparrots.com/plugin/pop-ups-easy-forms-mailchimp/' );
$description  = esc_attr__( 'Create custom popups to grab user attention. Increase email subscriptions and user engagement immediately.', 'yikes-inc-easy-mailchimp-extender' );
?>

<div class="type-download">

	<div class="featured-img">
		<a href="<?php echo esc_attr( $permalink ); ?>" title="<?php echo esc_attr( $add_on_title ); ?>" target="_blank">
			<img src="<?php echo esc_attr( $addon_image ); ?>" title="<?php echo esc_attr( $add_on_title ); ?>" />
		</a>
	</div>

	<div class="addon-content">
		<h3 class="addon-heading">
			<a href="<?php echo esc_attr( $permalink ); ?>" title="<?php echo esc_attr( $add_on_title ); ?>" target="_blank">
				<?php echo esc_attr( $add_on_title ); ?>
			</a>
		</h3>
		<p>
			<?php echo esc_attr( $description ); ?>
		</p>
	</div>

	<div class="addon-footer-wrap give-clearfix">
		<a href="<?php echo esc_attr( $permalink ); ?>" title="<?php echo esc_attr( $add_on_title ); ?>" class="button-secondary" target="_blank">
			<?php esc_attr_e( 'View Add-on' , 'yikes-inc-easy-mailchimp-extender' ); ?>
		<span class="dashicons dashicons-external"></span></a>
	</div>

</div>
