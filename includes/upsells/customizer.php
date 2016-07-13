<?php
/**
 * Form Customizer Upsell Ad
 *
 * This is the template for the upsell ad banner.
 * @since 6.1
 */
$ad_title = esc_attr__( 'Form Customizer Add-On', 'yikes-inc-easy-mailchimp' );
$ad_permalink = esc_url( 'https://yikesplugins.com/plugin/form-customizer-for-easy-forms-for-mailchimp/' );
$ad_image = ( file_exists( YIKES_MC_PATH . 'includes/upsells/images/customizer.jpg' ) ) ? YIKES_MC_URL . 'includes/upsells/images/customizer.jpg' : false;
$ad_description = esc_attr__( 'Quickly and easily customize every aspect of your MailChimp opt-in forms using our powerful customizer add-on.', 'yikes-inc-easy-mailchimp' );
?>

<h3><?php echo esc_attr( $ad_title ); ?></h3>
<div class="inside">
	<!-- Ad Image -->
	<?php if ( $ad_image ) { ?>
		<a href="<?php echo esc_attr( $ad_permalink ); ?>">
			<img src="<?php echo esc_attr( $ad_image ); ?>" title="<?php echo esc_attr( $ad_title ); ?>" />
		</a>
	<?php } ?>

	<!-- Ad Description -->
	<?php echo '<p>' . esc_attr( $ad_description ) . '</p>'; ?>

	<!-- View Addon Link -->
	<a href="<?php echo esc_attr( $ad_permalink ); ?>" class="button-secondary view-ad-link">
		<?php echo esc_attr_e( 'View Add-on', 'yikes-inc-easy-mailchimp' ); ?>
	</a>
</div>
