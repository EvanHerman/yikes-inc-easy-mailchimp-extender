<?php
/**
 * Popups Upsell Ad
 *
 * This is the template for the upsell ad banner.
 * @since 6.1
 */
$ad_title = esc_attr__( 'Popups Add-On', 'yikes-inc-easy-mailchimp' );
$ad_permalink = esc_url( 'https://yikesplugins.com/plugin/pop-ups-easy-forms-mailchimp/' );
$ad_image = ( file_exists( YIKES_MC_PATH . 'includes/upsells/images/popups.jpg' ) ) ? YIKES_MC_URL . 'includes/upsells/images/popups.jpg' : false;
$ad_description = esc_attr__( 'Effortlessly generate popups to capture user attention, grow your subscriber list and increase user engagement.', 'yikes-inc-easy-mailchimp' );
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
