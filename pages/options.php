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
                        $('#yks-status').html('<div class="updated"><p>The options were saved successfully!</p></div>');
                        $('#yks-status').slideDown('fast');
                    } else {
                        $('#yks-status').html('<div class="error"><p>The options could not be saved (or you did not change them).</p></div>');
                        $('#yks-status').slideDown('fast');
                    }
                }
            });
        }
        return false;
    });
	
	/*******************	Validate MailChimp API Key ****************************/
	function yikes_mc_api_key_validate() {
		jQuery('#submit').attr('disabled', 'disabled');
		jQuery('.mailChimp_api_key_validation_message').hide();
		// delay the function incase the user has deleted their API key
		setTimeout(function() {
			
			var thisLength = jQuery('#yks-mailchimp-api-key').val().length;
					
			// mailchimp api key is 36 characters, could be more. Usually not less.
			// checking the api key at 30 characters, maybe older api keys contain less characters
			if (thisLength >= 1) {	
					// store Mail Chimp API Key
					var apiKey = jQuery('#yks-mailchimp-api-key').val();
					// store datacenter value, from end of api key
					var dataCenter = apiKey.substr(apiKey.indexOf("-") + 1);
					
					if ( jQuery('.mailChimp_api_key_preloader').is(":visible")) {
						//
					} else {
						jQuery('.mailChimp_api_key_preloader').fadeIn();
					}
						// post the data to our api key validation function inside of lib.ajax.php
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								 action: 'yks_mailchimp_form',
								form_action: 'validate_api_key',
								api_key: apiKey,
								data_center: dataCenter
							},
							success: function(response) {
								if(response.indexOf('Everything\'s Chimpy!') > -1) {
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-checkmark.png'; ?>" alt=message > Valid API Key').css("color", "green").fadeIn();
										jQuery('#submit').removeAttr('disabled');
									});
								} else if (response.indexOf('Invalid Mailchimp API Key') > -1) {
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > Sorry, that is an invalid MailChimp API key.').css("color", "red").fadeIn();
									});								
								} else {
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > Sorry, that is an invalid MailChimp API key.').css("color", "red").fadeIn();
									});	
								};
							},
							error: function(response) {
								// alert('There was an error processing your request...');	
							}
						});	
			} else {
				jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
					jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > Error: Please enter a valid Mail Chimp API Key.').css("color", "red").fadeIn();
				});	
			}
		}, 1);
		
		
		
	}
	
	// run the validation on keyup
	jQuery('#yks-mailchimp-api-key').keyup(function() {
			stop();
			yikes_mc_api_key_validate();
	});
	
	// check the key on page load
	yikes_mc_api_key_validate();
	
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
					<td><input name="yks-mailchimp-api-key" type="text" id="yks-mailchimp-api-key" value="<?php echo $this->optionVal['api-key']; ?>" class="regular-text" /><span class="mailChimp_api_key_validation_message"></span><img class="mailChimp_api_key_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" ><span class="mailChimp_api_key_validation"></span>
					</td>
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
					<td><input type="submit" name="submit" id="submit" class="button-primary" value="Save Settings" disabled="disabled"></td>
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