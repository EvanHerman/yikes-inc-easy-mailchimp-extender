<script type="text/javascript">
	jQuery(document).ready(function($)
		{
		function blankFieldCheck()
			{
			err	= 0;
			msg	= '';
			if($('#yks-mailchimp-api-key').val() == '')
				{
				msg	+= '* Enter a Mailchimp API Key!'+"\n";
				err++;
				}
			if(msg != '')
				{
				msg	= 'Please fix the following before submitting the form:'+"\n\n"+msg;
				alert(msg);
				}
			return (err > 0 ? false : true);
			}
		$('#yks-mailchimp-form').submit(function(e){
			e.preventDefault();
			// Make sure the api key exists
			if(blankFieldCheck())
				{
				$('#yks-status').slideUp('fast');
				$.ajax({
					type:	'POST',
					url:	ajaxurl,
					data: {
								action:					'yks_mailchimp_form',
								form_action:		'update_api_key',
								api_key:				$('#yks-mailchimp-api-key').val()
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP == '1')
							{
							$('#yks-status').html('<div class="yks-success"><p>The api key was saved successfully!</p></div>');
							$('#yks-status').slideDown('fast');
							}
						else
							{
							$('#yks-status').html('<div class="yks-error"><p>The api key could not be saved (or you forgot to change it)!</p></div>');
							$('#yks-status').slideDown('fast');
							}
						}
				});
				}
			return false;
		})
		});
</script>
<div class="wrap">
	<div id="ykseme-icon" class="icon32"><br /></div>
	
	<h2 id="ykseme-page-header">
		Easy Mailchimp Extender
	</h2>

	<h3>Manage the Mailchimp API Form Options</h3>
	
	<div class="yks-status" id="yks-status"></div>
	
	<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form">
		
		<table class="form-table">
			<tbody>
				
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-api-key">Mailchimp API Key</label></th>
					<td><input name="yks-mailchimp-api-key" type="text" id="yks-mailchimp-api-key" value="<?php echo $this->optionVal['api-key']; ?>" class="regular-text" /></td>
				</tr>
				
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save API Key"></p>
	
	</form>
	
</div>