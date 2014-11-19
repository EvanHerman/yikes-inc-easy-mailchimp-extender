<?php
/**
 * YIKES Inc. MailChimp Template: Custom Template Boilerplate
 * MailChimp Template Author: YIKES Inc.
 * MailChimp Template Description: This is a starting template for users who would like to create their own custom optin templates.
													This boilerplate is good for widgets and forms placed within content. If you are looking to create
													an optin form in the header, I would recommend using the custom_header_template_boilerplate.php
													The file has been set up to easily customize for use in a header area. The confirmation message display 
													differs a bit as well.
													
	
	Developer Notes : 
		DO NOT remove the $this->getFrontendFormDisplay($list, $submit_text) call. This is what generates all of your input forms based on MailChimp data.
		Also, refrain from removing or altering any of the existing ID attributes as they are referenced by the JavaScript
	
 */
 
	// enqueue the associated styles for this template
	// found in the same directory, inside of the styles folder
	wp_enqueue_style( 'custom_template_boilerplate_css' , get_stylesheet_directory_uri() . '/yikes-mailchimp-user-templates/Custom_Boilerplate_-_Standard/custom_template_boilerplate.css' );
 
?>

<!-- Form Template -->
<div class="yks-mailchimpFormContainerInner custom_template_boilerplate" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
	
	<div id="wrapper">
    	<div id="box">
		
        	<div id="top_header">
          		<h3><?php _e( 'Sign Up Now!' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
          		<h5><?php _e( 'Sign up for our mailing list to receive weekly updates.' , 'yikes-inc-easy-mailchimp-extender' ); ?></h5>
        	</div>

			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
				<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
					<?php 
						/* Generate The Form Fields **/
						echo $this->getFrontendFormDisplay($list, $submit_text); 
					?>
			</form>
		
			<p class="disclaimer"><?php _e( 'We promise never to share or sell any of your personal information.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		
		</div>
	</div>
	
</div>