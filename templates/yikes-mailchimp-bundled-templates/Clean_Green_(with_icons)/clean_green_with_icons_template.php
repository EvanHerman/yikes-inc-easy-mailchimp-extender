<?php
/**
 * YIKES Inc. MailChimp Template: Clean Green (with icons)
 * YIKES Inc. MailChimp Template Author: YIKES Inc.
 * YIKES Inc. MailChimp Template Description: YIKES Inc Bundled Template - Optin Form With Icons Example.
 */
 
	// enqueue the associated styles for this template
	wp_enqueue_style( 'optin_with_icons_example_css' , YKSEME_URL . 'templates/yikes-mailchimp-bundled-templates/Clean_Green_(with_icons)/clean_green_with_icons.css' );
 
?>

<!-- Form Template -->
<div class="yks-mailchimpFormContainerInner optin-form-icons-example" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
	<div id="wrapper">
    	<div id="box">
		
        	<div id="top_header">
          		<h3><?php apply_filters( 'yks_mc_template_header' , _e( "Join Our Mailing List!" , "yikes-inc-easy-mailchimp-extender" ) ); ?></h3>
          		<h5><?php apply_filters( 'yks_mc_template_sub_text' , _e( "Sign up for our mailing list below!" , "yikes-inc-easy-mailchimp-extender" ) ); ?></h5>
        	</div>

			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" class="yiks-mailchimp-custom-form" rel="<?php echo $list['id']; ?>">
				<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
				<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
					<?php 
						/* Generate The Form Fields **/
						echo $this->getFrontendFormDisplay($list, $submit_text); 
					?>
										
			</form>
	
		</div>
	</div>
</div>