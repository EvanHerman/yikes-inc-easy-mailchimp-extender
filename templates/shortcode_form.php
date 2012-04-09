<script type="text/javascript">
$ymce = jQuery.noConflict(true);
	jQuery(document).ready(function($ymce){
		function blankFieldCheck()
			{
			err	= 0;
			msg	= '';
			<?php echo $this->getFrontendFormJavascript($list); ?>
			if(msg != '')
				{
				msg	= 'Oops.. Don\'t forget the fill-in the following fields:'+"\n\n"+msg;
				alert(msg);
				}
			return (err > 0 ? false : true);
			}
		$ymce('#yks-mailchimp-form_<?php echo $list['id']; ?>').submit(function(e){
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
								form_data:			$ymce(this).serialize()
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP == '1')
							{
							$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-success"><p>Thank You for subscribing! Check your email for the confirmation message.</p></div>');
							$ymce('#yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>').slideUp('fast', function(){
							$ymce('#yks-status-<?php echo $list['id']; ?>').slideDown('fast');
							});
							}
						else
							{
							$ymce('#ykfmc-submit_<?php echo $list['id']; ?>').removeAttr('disabled');
							$ymce('#yks-status-<?php echo $list['id']; ?>').html('<div class="yks-error"><p>Sorry the email address you used has previously been submitted.</p></div>');
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
	
	<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
			<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
			<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
			<?php echo $this->getFrontendFormDisplay($list); ?>
		</form>
	</div>
	
</div>
