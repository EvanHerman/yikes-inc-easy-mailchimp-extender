
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
                        $('#yks-status').html('<div class="yks-error"><p>The options could not be saved (or you forgot to change them)!</p></div>');
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
				
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-flavor">What's Your Flavor?</label></th>
					<td>
						<select name="yks-mailchimp-flavor" id="yks-mailchimp-flavor" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['flavor'] === '0' ? ' selected' : ''); ?>>Tables</option>
							<option value="1"<?php echo ($this->optionVal['flavor'] === '1' ? ' selected' : ''); ?>>Divs</option>
						</select>
						<span class="description">What you choose determines how the form is built with the shortcode (Default: Tables)</span>
					</td>
				</tr>
				
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Update Options"></p>
	
	</form>
	
	<div id="yks-mailchimp-debug-info">
		<table class="form-table">
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
	
</div>

<?php $this->getTrackingGif('options'); ?>