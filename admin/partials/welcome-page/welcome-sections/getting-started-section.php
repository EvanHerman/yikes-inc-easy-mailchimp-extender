<?php if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) { ?>
	<div class="about-description">
		<?php printf( __( "Before you can create any forms, you first need to enter your MailChimp API key into our <a href='%s' title='Easy MailChimp Forms Settings'>settings page</a>" , 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=general-settings' ) ) ); ?>
	</div>
<?php } ?>

<div class="changelog">
		
	<h3><?php _e( 'Creating Your First Form' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
	
	<div class="feature-section">
		
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Welcome_Page/create-first-optin-form.jpg'; ?>" alt="<?php _e( 'Create first optin form screenshot', 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-easy-mc-feature-image">
					
		<h4><a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ); ?>" title="<?php _e( 'Manage Forms' , 'yikes-inc-easy-mailchimp-extender' ); ?>">Easy MailChimp → <?php _e( 'Forms', 'yikes-inc-easy-mailchimp-extender' ); ?></a></h4>
		<p><?php _e( "To start collecting email addresses and building your mailing list, you need to create your first form. You can create as many forms as you like and assign each form to one of your lists.",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
	
		<p>&nbsp;</p>
		
		<h4><?php _e( 'Additional Options' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "For each form, you can choose the fields you want added to it and customize the success and error messages returned by MailChimp.",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
		
	</div>
	
	<div class="feature-section">
		
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Welcome_Page/optin-settings.png'; ?>" alt="<?php _e( 'Optin settings screenshot', 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-easy-mc-feature-image yikes-easy-mc-feature-image-left">
					
		<h4><?php _e( 'Customize the Form' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "Once created, you can customize the form. This includes editing the success and error messages displayed to the user, choosing which fields or interest groups are displayed, adding CSS classes to fields, assigning default values amp; placeholders and tons more!",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
		
		<p>&nbsp;</p>
		
		<p><?php _e( "Quickly and easily switch which list the form is associated with, toggle single or double optin, whether the welcome email should be sent, toggle AJAX form submissions and more. Get customizing!",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
	</div>
	<div class="feature-section">
		
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Welcome_Page/add-field-to-page.png'; ?>" alt="<?php _e( 'Add field to form screenshot', 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-easy-mc-feature-image">
					
		<h4><?php _e( 'Add Form to Page/Post' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "When you're ready to add your MailChimp opt-in form to a page or post, you can click on the small MailChimp button in the content editor toolbar.",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
		
		<p>&nbsp;</p>
		
		<h4><?php _e( 'Add Form to Widget' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "We've created a MailChimp widget to easily add forms to any sidebar or widgetized area on your site. The widget allows you to select from any created forms.",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
	</div>
	
	<div class="feature-section">
		<h3><span class="dashicons dashicons-format-status need-support-icon"></span> <?php _e( "Need Support?" , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>

		<h4><?php _e( 'Knowledge Base' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php printf( __( 'For full plugin documentation, visit our <a href="%s" title="Knowledge Base" target="_blank">Knowledge Base</a> where we have step-by-step guides and troubleshooting help..' , 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( 'https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/' ) ); ?>			
		
		<h4><?php _e( 'Free Support' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "If you need help using the free version of the plugin, please post your questions to our support forum on the WordPress Plugin Directory.",  'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<p><strong><a href="https://wordpress.org/support/plugin/yikes-inc-easy-mailchimp-extender" title="<?php esc_attr_e( 'WordPress Plugin Directory' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Easy Forms for MailChimp Free Support Forum' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></strong></p>
		
		<h4><?php _e( 'Priority Support' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<p><?php _e( "If you want immediate support, please consider purchasing an add-on or Unlimited License. Not only will you get 1 full year of automatic updates and priority support, you will enjoy all of the features packed into our add-ons not available in the free version." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
	</div>
	
</div>