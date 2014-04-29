<script type="text/javascript">
$ymce = jQuery.noConflict();
	jQuery(document).ready(function($ymce){
		function blankFieldCheck()
			{
			err	= 0;
			msg	= '';
			<?php echo $this->getFrontendFormJavascript($list); ?>
			if(msg != '')
				{
				msg	= "<?php _e('Oops.. Don\'t forget to fill-in the following fields','yikes-inc-easy-mailchimp-extender'); ?>"+":\n\n"+msg;
				alert(msg);
				}
			return (err > 0 ? false : true);
			}
		$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').submit(function(e){
			var singleOptinMessage = '<?php echo $this->optionVal['single-optin-message']; ?>';
			var doubleOptinMessage = '<?php echo $this->optionVal['double-optin-message']; ?>';
			var optinValue = '<?php echo $this->optionVal['optin']; ?>';
			e.preventDefault();
			// Make sure the api key exists
			if(blankFieldCheck())
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
						if(MAILCHIMP == '1')
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
							}
						else
							{
							$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').removeAttr('disabled');
							$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-error"><p>'+ MAILCHIMP +'</p></div>');
							$ymce('#yks-status-<?php echo $list['id']; ?>').slideDown('fast');
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
	
	<div class="yks-require-description">
			<span class='yks-required-label'>*</span> = <?php _e('required field','yikes-inc-easy-mailchimp-extender'); ?>
	</div>
	
	<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
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
