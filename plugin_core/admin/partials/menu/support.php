<?php 
/*
*	Main Support page 
*
*	@since 6.0.0
*	By: Yikes Inc. | https://www.yikesinc.com
*/
?>
<div class="wrap yikes-easy-mc-support-wrap"> 

	<div id="yikes-mailchimp-logo" class="support-page"></div>
	
	<h1><?php _e( 'Support' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
	<strong><?php _e( "You've got questions? We have answers!" , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
	<?php if( get_option( 'yikes-easy-mc-active-addons' , array() ) == array() ) { ?>
		<p><?php _e( 'Users of the free version of YIKES Inc. Easy Forms for MailChimp are limited to two methods of support. We respond to support requests for the free version of the plugin once a week.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<p><?php printf( __( 'Purchasers of our paid add-ons qualify for premium support, <a href="%s" target="_blank" title="' . __( 'check them out', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'check them out', 'yikes-inc-easy-mailchimp-extender' ) . '</a>!' , 'yikes-inc-easy-mailchimp-extender' ), esc_url( 'https://yikesplugins.com/' ) ); ?></p>
	<?php } else { ?>
		<p><?php _e( "It looks like you are one of our premium users! Fill out the form below to submit a priority support request." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<p><?php _e( "If you have any problems with the form, send an email to <a href='mailto:support@yikesinc.com'>support@yikesinc.com</a> and a ticket will be created." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
	<?php } ?>

		<p><?php printf( __( 'Before submitting a support request, visit our %s.' , 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/' ) . '" title="' . __( 'Knowledge Base', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'knowledge base', 'yikes-inc-easy-mailchimp-extender' ) . '</a> where we have step-by-step guides and troubleshooting help.' ); ?></p>

	<hr />
	
	<div id="col-container" class="free-support-container">

		<div id="col-right">

			<div class="col-wrap">
				<h1><span class="github-octocat"></span><?php _e( 'Github Issue Tracker' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
				<div class="inside">
					<p><?php _e( 'The fastest way to receive free support is to submit a new issue to our GitHub issue tracker. ', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					<img class="support-page-logo" src="<?php echo YIKES_MC_URL; ?>includes/images/Support_Page/github-issue-screenshot.png" title="<?php esc_attr_e( 'Github Issue Tracker Screenshot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" >
					<a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues" target="_blank" class="button-secondary support-page-button"><?php _e( 'Submit New Github.org Issue', 'yikes-inc-easy-mailchimp-extender' ); ?></a>
				</div>
			</div>
			<!-- /col-wrap -->

		</div>
		<!-- /col-right -->

		<div id="col-left">

			<div class="col-wrap">
				<h1><span class="dashicons dashicons-wordpress-alt support-page-wordpress-font"></span>&nbsp;<?php _e( 'WordPress.org Plugin Directory' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
				<div class="inside">
					<p><?php _e( 'Use your WordPress.org username to submit support requests on the WordPress Directory support forum.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					<img class="support-page-logo" src="<?php echo YIKES_MC_URL; ?>includes/images/Support_Page/wordpress-issue-screenshot.png" title="<?php esc_attr_e( 'WordPress.org Issue Tracker Screenshot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" >
					<a href="https://wordpress.org/support/plugin/yikes-inc-easy-mailchimp-extender" target="_blank" class="button-secondary support-page-button"><?php _e( 'Submit New WordPress.org Support Request', 'yikes-inc-easy-mailchimp-extender' ); ?></a>
				</div>
			</div>
			<!-- /col-wrap -->

		</div>
		<!-- /col-left -->
				
	</div>
	<!-- /col-container -->

	<!-- support container -->
	<div id="col-container">							
		<div id="col-left">
			<div class="col-wrap">		
				<?php do_action( 'yikes-mailchimp-support-page' ); ?>					
			</div>
		</div>
	</div>
	
</div> <!-- .wrap -->