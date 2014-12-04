<?php
/**
 * YIKES Inc. MailChimp Template: Custom Header Template Boilerplate
 * MailChimp Template Author: YIKES Inc.
 * MailChimp Template Description: This is a starting template for users who would like to create their own custom optin templates,
													specifically used within the header of your site. This template is great for full width pages, and header
													callouts where the form extends full width within, or just below, the header.
													
	Developer Notes : 
		DO NOT remove the $this->getFrontendFormDisplay($list, $submit_text) call. This is what generates all of your input forms based on MailChimp data.
		Also, refrain from removing or altering any of the existing ID attributes as they are referenced by the JavaScript
		Finally, DO NOT remove the class '
	
 */
 
	// enqueue the associated styles for this template
	// found in the same directory, inside of the styles folder
	wp_enqueue_style( 'custom_template_boilerplate_css' , get_stylesheet_directory_uri() . '/yikes-mailchimp-user-templates/Custom_Boilerplate_-_Header/custom_header_template_boilerplate.css' );
 
?>
<!--

	*** Note ***
			Currently, there are two styles of data submission. The standard sliding reveal, used in all forms.
			The new reveal is for ALL forms that are used in full width headers.
			To use the new fadeout/fadin confirmation message your parent div MUST contain the class header-callout-form
			
			Without the header-callout-form class, the sliding confirmation message will be used
			
			When using the new confirmation reveal, you MUST also include the <div id="wrapper"></div> element which is 
			used to populate the confirmation on submission
-->


<!-- Form Template -->
<div class="yks-mailchimpFormContainerInner large-optin-template header-callout-form" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
	
	<div id="wrapper">	
		
		<div id="box">
		
        	<div id="top_header">
          		<h3><?php _e( "Sign Up Now!" , "yikes-inc-easy-mailchimp-extender" ); ?></h3>
          		<h4><?php _e( "Receive updates directly to your inbox, daily!" , "yikes-inc-easy-mailchimp-extender" ); ?></h4>
        	</div>

			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
					<?php 
						/* Generate The Form Fields **/
						echo $this->getFrontendFormDisplay($list, $submit_text); 
					?>
					
					<p class="disclaimer"><em><?php _e( "We promise never to share or sell any of your personal information with anyone." , "yikes-inc-easy-mailchimp-extender" ); ?></em></p>
					
			</form>
	
		</div>
		
		
	</div>
	
</div>