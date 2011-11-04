<?php
$yksFormReqJs		= '';
$yksFormFields	= '';
foreach($list['fields'] as $field) : if($field['active'] == 1) :
	// Setup javascript
	if($field['require'] == '1') $yksFormReqJs .= "\n".'if($(\'#'.$field['id'].'\').val() == \'\'){msg+= \'* '.$field['label'].'\'+"\n";err++;}'."\n";
	// Setup form fields
	$yksFormFields	.= '<tr>';
		$yksFormFields	.= '<td class="prompt">'.$field['label'].'</td>';
		$yksFormFields	.= '<td>';
			$yksFormFields	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" />';
		$yksFormFields	.= '</td>';
	$yksFormFields	.= '</tr>';
endif; endforeach;
?>	
<script type="text/javascript">
	jQuery(document).ready(function($){
		function blankFieldCheck()
			{
			err	= 0;
			msg	= '';
			<?php echo $yksFormReqJs; ?>
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
		})
	});
</script>

<div class="yks-mailchimpFormContainer">
	<div class="yks-status" id="yks-status-<?php echo $list['id']; ?>"></div>
	
	<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
			<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
			<table class="yks-mailchimpFormTable">
				<tbody>
					<?php echo $yksFormFields; ?>
					<tr>
						<td colspan="2" class="yks-mailchimpFormTableSubmit">
							<p><input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="Submit" /></p>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	
</div>
