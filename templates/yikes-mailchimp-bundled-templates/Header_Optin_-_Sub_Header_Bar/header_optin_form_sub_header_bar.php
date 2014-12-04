<?php
/**
 * YIKES Inc. MailChimp Template: Header Optin - Sub Header Bar
 * YIKES Inc. MailChimp Template Author: YIKES Inc.
 * YIKES Inc. MailChimp Template Description: YIKES Inc Bundled Template. Wide optin call out, best placed just below the header.
 * 																	  
 */
 
	// enqueue the associated styles for this template
	wp_enqueue_style( 'big_optin_form_styles' , YKSEME_URL . 'templates/yikes-mailchimp-bundled-templates/Header_Optin_-_Sub_Header_Bar/header_optin_form_sub_header_bar.css' ); 
 
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
		
			<span class="callout-text"><h4><?php apply_filters( 'yks_mc_template_header' , _e( "Newsletter Signup" , "yikes-inc-easy-mailchimp-extender" ) ); ?></h4></span>
		
    		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
					<?php 
						/* Generate The Form Fields **/
						echo $this->getFrontendFormDisplay($list, $submit_text); 
					?>
										
			</form>
	
		</div>
		
		
	</div>
	
</div>