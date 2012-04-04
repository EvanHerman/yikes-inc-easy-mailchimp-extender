
<script type='text/javascript' src='<?php echo YKSEME_URL; ?>js/jquery.1.7.1.min.js'></script>
<script type="text/javascript">
$.noConflict(true);
jQuery.noConflict(true);
	jQuery(document).ready(function($){
		function blankFieldCheck()
			{
			err	= 0;
			msg	= '';
			<?php echo $this->getFrontendFormJavascript($list); ?>
			if(msg != '')
				{
				msg	= 'Please fix the following fields before submitting the form:'+"\n\n"+msg;
				alert(msg);
				}
			return (err > 0 ? false : true);
			}
		$('#yks-mailchimp-form_<?php echo $list['id']; ?>').submit(function(e){
			e.preventDefault();
			// Make sure the api key exists
			if(blankFieldCheck())
				{
				$('#ykfmc-submit_<?php echo $list['id']; ?>').attr('disabled', 'disabled');
				$('#yks-status-<?php echo $list['id']; ?>').slideUp('fast');
				$.ajax({
					type:	'POST',
					url:	'<?php echo YKSEME_URL_WP_AJAX; ?>',
					data: {
								action:					'yks_mailchimp_form',
								form_action:		'frontend_submit_form',
								form_data:			$(this).serialize()
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP == '1')
							{
							$('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>Thank You for subscribing! Check your email for the confirmation message.</p></div>');
							$('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').slideUp('fast', function(){
								$('#yks-status-<?php echo $list['id']; ?>').slideDown('fast');
							});
							}
						else
							{
							$('#ykfmc-submit_<?php echo $list['id']; ?>').removeAttr('disabled');
							$('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-error"><p>There was an error submiting the form -- please try again!</p></div>');
							$('#yks-status-<?php echo $list['id']; ?>').slideDown('fast');
							}
						}
				});
				}
			return false;
		});
		$('.yks-field-type-date').datepicker({
			changeMonth:	true,
			changeYear:		true,
			yearRange:		((new Date).getFullYear()-100)+':'+((new Date).getFullYear()),
			dateFormat: 	'yy-mm-dd'
		});
		$('#ui-datepicker-div').addClass('yks-mailchimpFormDatepickerContainer');
	});
</script>

<div class="yks-mailchimpFormContainer">
	<div class="yks-status" id="yks-status-<?php echo $list['id']; ?>"></div>
	
	<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
			<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
			<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
			<?php echo $this->getFrontendFormDisplay($list); ?>
		</form>
	</div>
	
</div>
