<?php
/**
 * GDPR Compliance Add-On Container
 *
 * Setup the variables for this add-on
 * @since 6.1
 */
$addon_image  = YIKES_MC_URL . 'includes/add_ons/images/eu-compliance.png';
$add_on_title = esc_attr__( 'GDPR Compliance for Mailchimp', 'yikes-inc-easy-mailchimp-extender' );
$permalink    = esc_url( 'https://codeparrots.com/plugin/eu-compliance-extension-for-easy-mailchimp/' );
$description  = esc_attr__( 'Add a check box below your Mailchimp signup forms with a custom message, to ensure compliance with GDPR laws.', 'yikes-inc-easy-mailchimp-extender' );
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
