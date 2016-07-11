<?php
/**
 * Reverse Zip Lookup Add-On Container
 *
 * Setup the variables for this add-on
 * @since 6.1
 */
$addon_image = YIKES_MC_URL . 'includes/add_ons/images/reverse-zip-lookup.png';
$add_on_title = esc_attr__( 'US Reverse Zip Lookup for Easy Forms for MailChimp', 'yikes-inc-easy-mailchimp-extender' );
$permalink = esc_url( 'https://yikesplugins.com/plugin/us-zip-lookup-extension-for-easy-mailchimp/' );
$description = esc_attr__( 'Reverse lookup the city and state of the user based on zip code. Integrates the Google Geocode API.', 'yikes-inc-easy-mailchimp-extender' );
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
