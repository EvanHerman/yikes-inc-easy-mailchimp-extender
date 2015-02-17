<?php 
if ( !is_admin() ) { 

		// custom action hooks to enqueue
		// styles and scripts, only on
		// pages where are forms are being display
		// ( performance enhancement :} )
		do_action( 'yks_mc_enqueue_styles' );
		do_action( 'yks_mc_enqueue_scripts' );
		
	// append vs prepend the error message 
	// check if the user has defined a constant
	if ( !defined( 'display_yikes_mc_form_messages' ) ) {
		$display = 'prepend';
		} else {
			if( display_yikes_mc_form_messages == 'before' ) {
				$display = 'prepend';
			} else if ( display_yikes_mc_form_messages == 'after' ) {
				$display = 'append';
			} else {
				$display = display_yikes_mc_form_messages;
			}											
		}		
?>

<script type="text/javascript">
$ymce = jQuery.noConflict();
	jQuery(document).ready(function($ymce){
	
		/* Blank Field Check */
		function blankFieldCheck(formID) {
				err	= 0;
				msg	= '';
				<?php echo $this->getFrontendFormJavascript($list); ?>
				if(msg != '')
					{
					jQuery('#yks_form_error_message').remove();
					// set up our alert for empty fields,
					msg	= "<?php _e('Error - The following fields are required, and may not be left blank ','yikes-inc-easy-mailchimp-extender'); ?>"+":\n\n"+'<ul>'+msg+'</ul>';
					// prepend the notification to the user instead of alerting it	
						// fade it in
						// and slide the user back up the the message so they don't miss it.
						jQuery('#yks-mailchimp-form_'+formID).<?php echo $display; ?>('<span id="yks_form_error_message">'+	msg+'</span>').delay(550).queue(function(next){
									jQuery('#yks_form_error_message').fadeIn();
									var offset_top = jQuery('#yks-mailchimpFormContainerInner_'+formID).offset().top;
									jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
									next();
								});			
						
					}
				return (err > 0 ? false : true);
			}
		
		/*
			Added event listener to form submission
			@since v5.2
		*/
		$ymce( 'body' ).on( 'submit' , '#yks-mailchimp-form_<?php echo $list['id']; ?>' , function(e) {	
	
			var singleOptinMessage = '<?php echo str_replace( array('\'',"\r","\n") , array('"',"\\r","\\n"), apply_filters('yks_mc_content' , $this->optionVal['single-optin-message'])); ?>';
			var doubleOptinMessage = '<?php echo str_replace( array('\'',"\r","\n") , array('"',"\\r","\\n"), apply_filters('yks_mc_content' , $this->optionVal['double-optin-message'])); ?>';
			var optinValue = '<?php echo $this->optionVal['optin']; ?>';
			
			e.preventDefault();
			
			// Make sure the api key exists
			if( blankFieldCheck( "<?php echo $list['id']; ?>" ) ) {
			
				// append pre-loader to submit button for some feedback
				$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input[type="submit"]' ).after( '<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" alt="yks-mc-submit-preloader" class="yks-mc-submit-preloader" style="margin-left:1em;box-shadow:none;">' );
				
				var form_data = $ymce(this).serialize();
				
				// disable all input fields while the data send...
				$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input' ).each(function() {
					$ymce(this).attr('disabled','disabled').css('opacity','.8');
				});
				// disable all select fields while the data send...
				$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
					$ymce(this).attr('disabled','disabled').css('opacity','.8');
				});
				
				$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').attr('disabled', 'disabled');
				$ymce('#yks-status-<?php echo $list['id']; ?>').fadeOut('fast');
				$ymce('#yks_form_error_message').fadeOut();
				$ymce('.preloader-confirmation-box').remove();
				
					$ymce.ajax({
						type:	'POST',
						url:	'<?php echo YKSEME_URL_WP_AJAX; ?>',
						data: {
									action:				'yks_mailchimp_form_submit',
									form_action:		'frontend_submit_form',
									form_data:			form_data
									},
						dataType: 'json',
						success: function(MAILCHIMP)
							{
							if( MAILCHIMP == 1 )
								{
									
									// remove the preloader
									jQuery( '.yks-mc-submit-preloader' ).remove();
									
									// re-enable all input fields while the data send...
									$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').children().each(function() {
										$ymce(this).not('#wrapper').removeAttr( 'disabled' ).css( 'opacity' , '1' );
									});
									
									// re-enable all select fields while the data send...
									$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
										$ymce(this).removeAttr('disabled').css('opacity','1');
									});
															
									// custom message based on opt-in settings value
									// single opt-in
									if ( optinValue == 'false' ) {
										$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>'+singleOptinMessage+'</p></div>');		
									} else { // double opt-in
										$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>'+doubleOptinMessage+'</p></div>');		
									}
									
									/** Header Call Out Submission **/
									if ( $ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').hasClass( 'header-callout-form' ) ) {
										
										var container_height = $ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( '#wrapper' ).css( 'height' );
										var container_width = $ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( '#wrapper' ).css( 'width' );
										var top_margin_confirmation = ( container_height.replace( 'px' , '' , container_height ) - 25 ) / 2;
										
										$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( '#wrapper' ).css( 'height' , container_height ).css( 'width' , container_width );
											$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( '#wrapper' ).children().each(function() {
												$ymce(this).fadeOut('fast');
											}).promise().done( function() {
												var confirmation_message = $ymce('#yks-status-<?php echo $list['id']; ?>');
												$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( '#wrapper' ).html( confirmation_message );
												$ymce( confirmation_message ).removeClass('yks-status').fadeIn('fast').attr( 'style' , 'text-align:center;vertical-align:middle;margin-top:19%;');
											});
											
										
									} else {
									
										// remove the preloader
										jQuery( '.yks-mc-submit-preloader' ).remove();
										
										// re-enable all input fields while the data send...
										$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find('input').each(function() {
											$ymce(this).not('#wrapper').removeAttr( 'disabled' ).css( 'opacity' , '1' );
										});
										
										// re-enable all select fields while the data send...
										$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
											$ymce(this).removeAttr('disabled').css('opacity','1');
										});
									
										/* reset the form, append the confirmation before the form */
										$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input[type="text"]', 'textarea' ).val( '' );
										$ymce('#yks-status-<?php echo $list['id']; ?>').fadeIn('fast');
										
									
									}
									
								} else {	
																							
									// bundle the MailChimp returned error
									// with our yks mc error messages
									$ymce('#yks_form_error_message').fadeOut('fast', function() {
										jQuery(this).remove();
									});
									
									$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').removeAttr('disabled');
																		
									if( MAILCHIMP.errorResponse.toLowerCase().indexOf( "is already subscribed to the list." ) >= 0 ) {
									
										$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').<?php echo $display; ?>('<span id="yks_form_error_message">'+MAILCHIMP.errorResponse+' <a href="#" class="update-email-profile-link" alt="'+extractEmails(MAILCHIMP.errorResponse)+'">Click Here</a> to send an email to update your profile.</span>').delay(1000).queue(function(next){
											// remove the preloader
											jQuery( '.yks-mc-submit-preloader' ).remove();
											
											// remove disable from all input fields while the data send...
											$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input' ).each(function() {
												$ymce(this).removeAttr( 'disabled' ).css('opacity','1');
											});
											// re-enable all select fields while the data send...
											$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
												$ymce(this).removeAttr('disabled').css('opacity','1');
											});
											
											jQuery('#yks_form_error_message').fadeIn();
											var offset_top = jQuery('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').offset().top;
											jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
											next();
										});
											
									} else {
									
										$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').<?php echo $display; ?>('<span id="yks_form_error_message">'+MAILCHIMP.errorResponse+'</span>').delay(1000).queue(function(next){
											// remove the preloader
											jQuery( '.yks-mc-submit-preloader' ).remove();
											
											// remove disable from all input fields while the data send...
											$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input' ).each(function() {
												$ymce(this).removeAttr( 'disabled' ).css('opacity','1');
											});
											// re-enable all select fields while the data send...
											$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
												$ymce(this).removeAttr('disabled').css('opacity','1');
											});
											
											jQuery('#yks_form_error_message').fadeIn();
											var offset_top = jQuery('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').offset().top;
											jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
											next();
										});
										
									}
									
																	
								}
							},
							// append our error up above, much like the others!
							error: function(error) {
								jQuery( '#yks_form_error_message' ).remove();
								jQuery( '.yks-mc-submit-preloader' ).remove();
								jQuery( '.ykfmc-submit' ).removeAttr( 'disabled' );
								jQuery('#yks_form_error_message').fadeIn();
								jQuery('#yks-mailchimp-form_<?php echo $list['id']; ?>').<?php echo $display; ?>('<span id="yks_form_error_message">'+error.responseText+'</span>').delay(1000).queue(function(next){
									// remove the preloader
									jQuery( '.yks-mc-submit-preloader' ).remove();
											
									// remove disable from all input fields while the data send...
									$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'input' ).each(function() {
										$ymce(this).removeAttr( 'disabled' ).css('opacity','1');
									});
									
									// re-enable all select fields while the data send...
									$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').find( 'select' ).each(function() {
										$ymce(this).removeAttr('disabled').css('opacity','1');
									});
												
									jQuery('#yks_form_error_message').fadeIn();
									var offset_top = jQuery('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').offset().top;
									jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
									next();
								});
								console.log( error );
							}	
					});
				}
			return false;
		});
		
		
		<?php // enqueue jQuery datepicker only when the user has specified to do so
		if( isset( $this->optionVal['yks-mailchimp-jquery-datepicker'] ) && $this->optionVal['yks-mailchimp-jquery-datepicker'] == '1' ) { ?>
			$ymce('.yks-field-type-date').datepicker({
				changeMonth:	true,
				changeYear:		true,
				yearRange:		((new Date).getFullYear()-100)+':'+((new Date).getFullYear()),
				dateFormat: 	'yy-mm-dd'
			});
		$ymce('#ui-datepicker-div').addClass('yks-mailchimpFormDatepickerContainer');
		<?php } ?>
		
		
		jQuery( 'body' ).on( 'click' , '.update-email-profile-link' , function() {
			jQuery( '.preloader-confirmation-box' ).remove();
			var user_email = jQuery(this).attr('alt');
			var list_id = jQuery(this).parents('form').attr('rel');
			var list_id_split = list_id.split( '-' );
			var list_id_final = list_id_split[1];
			jQuery( '#yks_form_error_message' ).after( '<span class="preloader-confirmation-box"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="yks-mc-resend-email-preloader" style="box-shadow:none;"></span>' );
			$ymce.ajax({
						type:	'POST',
						url:	'<?php echo YKSEME_URL_WP_AJAX; ?>',
						data: {
							action: 'yks_mailchimp_form_submit',
							form_action: 'send_update_email',
							user_email: user_email,
							list_id : list_id_final
						},
						dataType: 'html',
						success: function(response) {	
								jQuery( '.preloader-confirmation-box' ).html( response );
								console.log('success' + response);
							},
						error: function(errorResponse) {
								jQuery( '.preloader-confirmation-box' ).html( errorResponse );
								console.log(errorResponse);
							}
					});
			return false;
		});
		
	});
	
	function extractEmails(text) {
		return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
	}
		
</script>

<?php } else { // prevent the form from submitting inside the preview window ?>
<script>
jQuery(document).ready(function() {
	jQuery('form[name="yks-mailchimp-form"]').submit(function() {
		console.log('false');
		return false;
	});
});
</script>
<?php } 
	// set up and store our custom style values 
	if ( isset( $list['custom_styles'] ) && $list['custom_styles']['active'] == '1' ) {
		$form_id = $list['id'];
		?>
		<style>	
			#ykfmc-submit_<?php echo $form_id; ?> {
				background: <?php echo $list['custom_styles']['yks_mc_submit_button_color']; ?>;
				color: <?php echo $list['custom_styles']['yks_mc_submit_button_text_color']; ?>;
			}
			.yks-mailchimpFormContainer-<?php echo $form_id; ?> {
				background: <?php echo $list['custom_styles']['yks_mc_form_background_color']; ?>;
				padding: <?php echo $list['custom_styles']['yks_mc_form_padding'].$list['custom_styles']['yks_mc_form_padding_measurement']; ?>;
				color: <?php echo $list['custom_styles']['yks_mc_form_text_color']; ?>;
				width: <?php echo $list['custom_styles']['yks_mc_form_width']; ?>;
				<?php	
					if ( $list['custom_styles']['yks_mc_form_alignment'] == 'left' ) {
						?>
							display: block;
							float: left;
						<?php
					} elseif ( $list['custom_styles']['yks_mc_form_alignment'] == 'center' ) {
						?>
							display: block;
							margin: 0 auto;
							float: none;
						<?php
					} elseif ( $list['custom_styles']['yks_mc_form_alignment'] == 'right' ) {
						?>
							display: block;
							float: right;
						<?php
					}
				?>
			}
			.yks-mailchimpFormDivRowLabel, .yks_mc_interest_group_label {
				color: <?php echo $list['custom_styles']['yks_mc_form_text_color']; ?>;
			}
		</style>
		<?php
	}
?>
<div class="yks-mailchimpFormContainer yks-mailchimpFormContainer-<?php echo $list['id']; ?>">

	<?php if ( !defined( 'display_yikes_mc_form_messages' ) ) { ?>
		<div class="yks-status" id="yks-status-<?php echo $list['id']; ?>"></div>
	<?php } ?>
	
	<?php 
	
		// custom action to print text before ALL forms
		do_action( 'yks_mc_before_all_forms');
	
		// custom action to print text for a specific form
		// using the form ID
		$form_id = explode('-', $list['id']);
		
		do_action( 'yks_mc_before_form_'.$form_id[1] );

		
		// load our custom MailChimp template here!
		if ( isset( $list['custom_template'] ) && $list['custom_template']['active'] == 1 ) {
			// Custom List form
			include $list['custom_template']['template_file'];
		} else {
			// include the form template
			include YKSEME_PATH.'templates/form_template.php';
		}
			
		// custom action to print text after ALL forms
		do_action("yks_mc_after_all_forms"); 
		
		// custom action to print text after a specific form
		// using the form ID set above
		do_action( 'yks_mc_after_form_'.$form_id[1] );
		
		if ( defined( 'display_yikes_mc_form_messages' ) ) { 
			if ( display_yikes_mc_form_messages == 'after' || display_yikes_mc_form_messages == 'append' ) { ?>
			<div class="yks-status" id="yks-status-<?php echo $list['id']; ?>" style="margin-top:0;"></div>
		<?php }
		} ?>
			
</div>