<?php
/**
 * YIKES Inc. MailChimp Template: Header Optin - Vacation
 * YIKES Inc. MailChimp Template Author: YIKES Inc.
 * YIKES Inc. MailChimp Template Description: YIKES Inc Bundled Template. Large optin template. Great for use in site headers or for large call out areas.
 * 																	  
 */
 
	// enqueue the associated styles for this template
	wp_enqueue_style( 'big_optin_form_styles' , YKSEME_URL . 'templates/yikes-mailchimp-bundled-templates/Header_Optin_-_Vacation/header_optin_form.css' ); 
 
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
    	
		<div id="large-callout-information">
			<h2><?php _e( "I'll Teach You FREE, How You Can..." , "yikes-inc-easy-mailchimp-extender" ); ?></h2>
			<ul>
				<li><p><?php _e( "Achieve your fitness level in no time!" , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
				<li><p><?php _e( "Grow in a healthy manner" , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
				<li><p><?php _e( "Enjoy whatever foods you want, and still lose weight!" , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
				<li><p><?php _e( "Create a workout routine that works for you" , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
				<li><p><?php _e( "See results faster than ever before" , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
			</ul>
		</div>
		
		
		<div id="box">
		
        	<div id="top_header">
          		<h3><?php _e( "Sign Up Now!" , "yikes-inc-easy-mailchimp-extender" ); ?></h3>
          		<h4><?php _e( "Sign up for our super awesome mailing list." , "yikes-inc-easy-mailchimp-extender" ); ?></h4>
        	</div>

			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
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