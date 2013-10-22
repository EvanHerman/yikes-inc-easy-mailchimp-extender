
<script type="text/javascript">
jQuery(document).ready(function ($) {
    function blankFieldCheck() {
        err = 0;
        msg = '';
        if ($('#yks-mailchimp-api-key').val() == '') {
            msg += '* Enter a Mailchimp API Key!' + "\n";
            err++;
        }
        if (msg != '') {
            msg = 'Please fix the following before submitting the form:' + "\n\n" + msg;
            alert(msg);
        }
        return (err > 0 ? false : true);
    }
    $('#yks-mailchimp-form').submit(function (e) {
        e.preventDefault();
        // Make sure the api key exists
        if (blankFieldCheck()) {
            $('#yks-status').slideUp('fast');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'yks_mailchimp_form',
                    form_action: 'update_options',
                    form_data: $('#yks-mailchimp-form').serialize()
                },
                dataType: 'json',
                success: function (MAILCHIMP) {
                    if (MAILCHIMP == '1') {
                        $('#yks-status').html('<div class="yks-success"><p>The options were saved successfully!</p></div>');
                        $('#yks-status').slideDown('fast');
                    } else {
                        $('#yks-status').html('<div class="yks-error"><p>The options could not be saved (or you did not change them).</p></div>');
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
	
	<div id="ykseme-icon" class="icon32"></div>

	<h2 id="ykseme-page-header">
		Easy Mailchimp Forms by YIKES, Inc.
	</h2>

	<h3>Manage Mailchimp Forms Settings</h3>
	
	<div class="yks-status" id="yks-status"></div>
	
	<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form">
		
		<table class="form-table yks-admin-form">
			<tbody>
				
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-api-key">Your Mailchimp API Key</label></th>
					<td><input name="yks-mailchimp-api-key" type="text" id="yks-mailchimp-api-key" value="<?php echo $this->optionVal['api-key']; ?>" class="regular-text" /></td>
				</tr>

				<tr>
					<td></td>
					<td class="yks-settings-description">
						Please enter your MailChimp API Key above. The API Key allows your WordPress site to communicate with your MailChimp account.<br />
						For more help, visit the MailChimp Support article <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank">Where can I find my API Key?</a>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-flavor">Preferred Form Layout</label></th>
					<td>
						<select name="yks-mailchimp-flavor" id="yks-mailchimp-flavor" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['flavor'] === '0' ? ' selected' : ''); ?>>table</option>
							<option value="1"<?php echo ($this->optionVal['flavor'] === '1' ? ' selected' : ''); ?>>div</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="yks-settings-description">
						Choose whether you want your forms to use a table or div layout.
					</td>
				</tr>				
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-debug">Advanced Error Messaging</label></th>
					<td>
						<select name="yks-mailchimp-debug" id="yks-mailchimp-debug" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['debug'] === '0' ? ' selected' : ''); ?>>Disabled</option>
							<option value="1"<?php echo ($this->optionVal['debug'] === '1' ? ' selected' : ''); ?>>Enabled</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="yks-settings-description">
						Enable if you're having problems with your forms sending data to MailChimp. Enabling Advanced Error Messaging will show you the exact error codes MailChimp is returning. 
					</td>
				</tr>
				
				<tr>
					<td></td>
					<td><input type="submit" name="submit" id="submit" class="button-primary" value="Save Settings"></td>
				</tr>	
			</tbody>
		</table>

	</form>
	
	<h3>Plugin Information</h3>

	<p>
		If you experience any issues with our plugin, please <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues" target="_blank">submit a New Issue on our Github Issue Tracker</a>. Please include the information below to help us troubleshoot your problem.
	</p>

	<table class="form-table yks-admin-form">
		<tbody>
			
			<tr valign="top">
				<th scope="row"><label>Plugin Version</label></th>
				<td><?php echo YKSEME_VERSION_CURRENT; ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Wordpress Version</label></th>
				<td><?php echo get_bloginfo( 'version' ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Browser Information</label></th>
				<td>
					<?php
					$theBrowser = $this->getBrowser();
					echo $theBrowser['name'].' '.$theBrowser['version'].' on '.$theBrowser['platform'];
					?>
				</td>
			</tr>
			
		</tbody>
	</table>
</div>

<?php $this->getTrackingGif('options'); ?>