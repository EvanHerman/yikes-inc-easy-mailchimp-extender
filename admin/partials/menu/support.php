<?php 
/*
*	Main Support page 
*
*	@since 6.0.0
*	By: Yikes Inc. | https://www.yikesinc.com
*/
?>
<div class="wrap yikes-easy-mc-support-wrap"> 

	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1>Easy Forms for MailChimp | <?php _e( 'Support' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
		<!-- Support Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'If you have questions, we have answers!' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<?php if( get_option( 'yikes-easy-mc-active-addons' , array() ) == array() ) { ?>
		<p><?php _e( 'Users of the free version of Easy Forms for MailChimp can post questions to our support forum on the WordPress Plugin Directory. We aim to respond to support requests for the free version of the plugin within a week.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<p><?php printf( __( 'Purchasers of our paid add-ons qualify for premium support. <a href="%s" target="_blank" title="' . __( 'Check out our paid add-ons', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Check out our paid add-ons', 'yikes-inc-easy-mailchimp-extender' ) . '</a>!' , 'yikes-inc-easy-mailchimp-extender' ), esc_url( 'https://yikesplugins.com/' ) ); ?></p>
	<?php } else { ?>
		<p><?php _e( "It looks like you are one of our premium users! Fill out the form below to submit a priority support request." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<p><?php _e( "If you have any problems with the form, send an email to <a href='mailto:support@yikesinc.com'>support@yikesinc.com</a> and a ticket will be created." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
	<?php } ?>

		<p><?php printf( __( 'Before submitting a support request, please visit our %s.' , 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/' ) . '" title="' . __( 'Knowledge Base', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'knowledge base', 'yikes-inc-easy-mailchimp-extender' ) . '</a> where we have step-by-step guides and troubleshooting help' ); ?></p>

	<hr />
	


	<?php do_action( 'yikes-mailchimp-support-page' ); ?>
	
</div> <!-- .wrap -->