<?php
/**
 * YIKES Inc. MailChimp Template: Clean Blue
 * YIKES Inc. MailChimp Template Author: YIKES Inc.
 * YIKES Inc. MailChimp Template Description: YIKES Inc Bundled Template. Clean blue layout.
 */
 
	// enqueue the associated styles for this template
	wp_enqueue_style( 'clean_blue_optin_styles' , YKSEME_URL . 'templates/yikes-mailchimp-bundled-templates/Clean_Blue/clean_blue_form.css' );
 
?>

<!-- Form Template -->
<div class="yks-mailchimpFormContainerInner clean-blue" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
	<div id="wrapper">
    	<div id="box">
		
        	<div id="top_header">
          		<h3>Sign Up Now!</h3>
          		<h5>Sign up for our super awesome mailing list.</h5>
        	</div>

			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
				<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
					<?php 
						/* Generate The Form Fields **/
						echo $this->getFrontendFormDisplay_placeholder_labels($list, $submit_text); 
					?>
					
					<p class="disclaimer">We promise never to share or sell any of your personal information.</p>
					
			</form>
	
		</div>
	</div>
</div>