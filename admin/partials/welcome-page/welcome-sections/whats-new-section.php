<?php
	// set our text domain
	$text_domain = 'yikes-inc-easy-mailchimp-extender';
?>
<div class="wrap about-wrap">

	<div class="feature-section">
		
		<h3><?php _e( 'All New Form Management' , $text_domain ); ?></h3>
		
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Welcome_Page/edit-form.jpg'; ?>" alt="" class="yikes-easy-mc-feature-image" style="float:none;width:100%;margin-left:0 !important;">
		
			
		<h4><a href="<?php echo admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ); ?>" title="<?php _e( 'Manage Forms' , $text_domain ); ?>">Easy MailChimp → Forms</a></h4>
		<p><?php _e( "Before you can start collecting users email addresses and building your mailing list, you'll need to create your first form. You can create as many forms as you'd like and assign each form to the same or different mailing lists.",  $text_domain ); ?></p>

		<h4><?php _e( 'Additional Options' , $text_domain ); ?></h4>
		<p><?php _e( "Once you create your form, you can pick and choose which fields you want to display and customize the success and error messages returned by the MailChimp API.",  $text_domain ); ?></p>
		
	</div>
	
</div>