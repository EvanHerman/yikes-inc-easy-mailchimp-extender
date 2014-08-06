<script type="text/javascript">
$ymce = jQuery.noConflict();
	jQuery(document).ready(function($ymce){
		function blankFieldCheck(formID)
			{
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
					jQuery('#yks-mailchimp-form_'+formID).prepend('<span id="yks_form_error_message">'+	msg+'</span>').delay(550).queue(function(next){
								jQuery('#yks_form_error_message').fadeIn();
								var offset_top = jQuery('#yks-mailchimpFormContainerInner_'+formID).offset().top;
								jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
								next();
							});			
					
				}
			return (err > 0 ? false : true);
			}
		$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').submit(function(e){	
			// remove sharedaddy if the user has it activated
			// it shouldn't be here :) 
			// maybe include a checkbox and give the user
			// an option if they'd like to display it or not?
			<?php 
				/* not sure this is needed anymore
				if( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'sharedaddy' ) ) {
					remove_filter( 'the_content', 'sharing_display', 19 ); 
				}
				*/
			?>
			var singleOptinMessage = '<?php echo str_replace( '\'' , '"' , preg_replace('/\r?\n/', '\\n', apply_filters('yks_mc_content' , $this->optionVal['single-optin-message']))); ?>';
			var doubleOptinMessage = '<?php echo str_replace( '\'' , '"' , preg_replace('/\r?\n/', '\\n', apply_filters('yks_mc_content' , $this->optionVal['double-optin-message']))); ?>';
			var optinValue = '<?php echo $this->optionVal['optin']; ?>';
			e.preventDefault();
			// Make sure the api key exists
			if(blankFieldCheck("<?php echo $list['id']; ?>"))
				{
				$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').attr('disabled', 'disabled');
				$ymce('#yks-status-<?php echo $list['id']; ?>').slideUp('fast');
				$ymce.ajax({
					type:	'POST',
					url:	'<?php echo YKSEME_URL_WP_AJAX; ?>',
					data: {
								action:				'yks_mailchimp_form',
								form_action:		'frontend_submit_form',
								form_data:			$ymce(this).serialize(),
								},
					dataType: 'text',
					success: function(MAILCHIMP)
						{
						if( MAILCHIMP.trim() == 1 )
							{
								// custom message based on opt-in settings value
								// single opt-in
								if ( optinValue == 'false' ) {
									$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>'+singleOptinMessage+'</p></div>');		
								} else { // double opt-in
									$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>'+doubleOptinMessage+'</p></div>');		
								}
								$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').slideUp('fast', function(){
									$ymce('#yks-status-<?php echo $list['id']; ?>').slideDown('fast');
								});
							} else {
								// bundle the MailChimp returned error
								// with our yks mc error messages
								$ymce('#yks_form_error_message').fadeOut('fast', function() {
									jQuery(this).remove();
								});
								$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').removeAttr('disabled');
								$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').prepend('<span id="yks_form_error_message">'+MAILCHIMP+'</span>').delay(1000).queue(function(next){
									jQuery('#yks_form_error_message').fadeIn();
									var offset_top = jQuery('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').offset().top;
									jQuery("html, body").animate({ scrollTop: offset_top - 50 }, 500 );
									next();
								});
							}
						}	
				});
				}
			return false;
		});
		$ymce('.yks-field-type-date').datepicker({
			changeMonth:	true,
			changeYear:		true,
			yearRange:		((new Date).getFullYear()-100)+':'+((new Date).getFullYear()),
			dateFormat: 	'yy-mm-dd'
		});
		$ymce('#ui-datepicker-div').addClass('yks-mailchimpFormDatepickerContainer');
	});
</script>



<div class="yks-mailchimpFormContainer">
	<div class="yks-status" id="yks-status-<?php echo $list['id']; ?>"></div>
	
	<?php 
	
		// custom action to print text before ALL forms
		do_action( 'yks_mc_before_all_forms');
	
		// custom action to print text for a specific form
		// using the form ID
		$form_id = explode('-', $list['id']);
		do_action( 'yks_mc_before_form_'.$form_id[1] );

	
	?>
	
	<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
		<div class="yks-require-description">
			<span class='yks-required-label'>*</span> = <?php _e('required field','yikes-inc-easy-mailchimp-extender'); ?>
		</div>
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
			<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
			<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
			<?php echo $this->getFrontendFormDisplay($list, $submit_text); ?>
		</form>
	</div>
	
	<?php 
	
		// custom action to print text after ALL forms
		do_action("yks_mc_after_all_forms"); 
		
		// custom action to print text after a specific form
		// using the form ID set above
		do_action( 'yks_mc_after_form_'.$form_id[1] );
	?>
	
</div>