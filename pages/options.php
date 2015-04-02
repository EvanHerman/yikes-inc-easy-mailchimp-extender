<?php 
	// check if cURL is enabled on the server level
	// if it is enabled, carry on...
	if ( $this->yikes_curl_check() ) { 
		wp_enqueue_script('jquery-ui-dialog');
		// if blog is greater than or equal to WordPress 3.9
		// enqueue our new jQuery UI dialog styles
		if ( get_bloginfo( 'version' ) >= '3.9' ) {
			wp_enqueue_style("wp-jquery-ui-dialog");
		}
?>	
<style>
input#yks-mailchimp-api-key {
	width: 45%;
	min-width:408px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {

	// check for blank fields
	// runs when we add or remove a list from the lists pages
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
	
	// ajax save the WordPress Plugin Options Page
	// Form Options Page
    $('#yks-mailchimp-form').submit(function (e) {	        
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
							$('#yks-status').html('<div class=updated><p><?php _e('The options were saved successfully!', 'yikes-inc-easy-mailchimp-extender'); ?></p></div>');
							$('#yks-status').slideDown('fast');
						} else {
							$('#yks-status').html("<div class=error><p><?php _e("The options could not be saved (or you did not change them).", "yikes-inc-easy-mailchimp-extender"); ?></p></div>");
							$('#yks-status').slideDown('fast');
							console.log(MAILCHIMP);
						}
					},
					error : function(MAILCHIMP2) {
						console.log(MAILCHIMP2.responseText);
					}
				});
       } 
       e.preventDefault();
    });
	
	// ajax save the WordPress Plugin ReCaptcha Options Page
	// ReCaptcha Options
    $('#yks-mailchimp-form-recaptcha-options').submit(function (e) {	        
        // Make sure the api key exists
            $('#yks-status').slideUp('fast');
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'update_recptcha_options',
						form_data: $('#yks-mailchimp-form-recaptcha-options').serialize()
					},
					dataType: 'json',
					success: function (MAILCHIMP) {
						if (MAILCHIMP == '1') {	
							$('#yks-status').html('<div class=updated><p><?php _e('The options were saved successfully!', 'yikes-inc-easy-mailchimp-extender'); ?></p></div>');
							$('#yks-status').slideDown('fast');
						} else {
							$('#yks-status').html("<div class=error><p><?php _e("The options could not be saved (or you did not change them).", "yikes-inc-easy-mailchimp-extender"); ?></p></div>");
							$('#yks-status').slideDown('fast');
							console.log(MAILCHIMP);
						}
					},
					error : function(MAILCHIMP2) {
						
						console.log(MAILCHIMP2.responseText);
					}
				});
       e.preventDefault();
    });
	
	// ajax save the WordPress Plugin Debug Options Page
	// Debug Options Page
    $('#yks-mailchimp-form-debug-options').submit(function (e) {	
        // Make sure the api key exists
            $('#yks-status').slideUp('fast');
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'update_debug_options',
						form_data: $('#yks-mailchimp-form-debug-options').serialize()
					},
					dataType: 'json',
					success: function (MAILCHIMP) {
						if (MAILCHIMP == '1') {	
							$('#yks-status').html('<div class=updated><p><?php _e('The options were saved successfully!', 'yikes-inc-easy-mailchimp-extender'); ?></p></div>');
							$('#yks-status').slideDown('fast');
						} else {
							$('#yks-status').html("<div class=error><p><?php _e("The options could not be saved (or you did not change them).", "yikes-inc-easy-mailchimp-extender"); ?></p></div>");
							$('#yks-status').slideDown('fast');
							console.log(MAILCHIMP);
						}
					},
					error : function(MAILCHIMP2) {
						console.log(MAILCHIMP2.responseText);
					}
				});
       e.preventDefault();
    });
	
	/*******************	Validate MailChimp API Key ****************************/
	// Ajax function which is fired when the user types in a value into the API input field
	function yikes_mc_api_key_validate() {
		jQuery('.mailChimp_api_key_validation_message').hide();
		jQuery('#yks-mailchimp-api-key').parents('td').find('.mailChimp_api_key_validation_message').last().hide();
		jQuery('#submit').attr('disabled','disabled');
		// delay the function incase the user has deleted their API key
			setTimeout(function() {
				
				if ( jQuery('#yks-mailchimp-api-key').is(':visible') ) {
				
					var thisLength = jQuery('#yks-mailchimp-api-key').val().length;
							
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
									dataType: 'html',
									success: function(response) {
										// if our response contains 'Everything's Chimpy' - everything's good to go
										if(response.indexOf('Everything\'s Chimpy!') > -1) {
											jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
												jQuery('#yks-mailchimp-api-key').parents('td').find('.mailChimp_api_key_validation_message').first().html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-checkmark.png'; ?>" alt=message > <?php _e('Valid API Key','yikes-inc-easy-mailchimp-extender'); ?>').css("color", "green").fadeIn();
												jQuery('#submit').removeAttr('disabled');
											});
										// if our response contains 'Invalid MailChimp API Key' - display an error	
										} else if (response.indexOf('Invalid Mailchimp API Key') > -1) {
											jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
												jQuery('#yks-mailchimp-api-key').parents('td').find('.mailChimp_api_key_validation_message').first().html("<img src=<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?> alt=message > <?php _e("Sorry, that is an invalid MailChimp API key.","yikes-inc-easy-mailchimp-extender"); ?>").css("color", "red").fadeIn();
												jQuery('#submit').attr('disabled','disabled');
											});
											console.log('MailChimp API Response : '+response);
										} else {
										// if our response contains anything else, other than whats above, just let them know its invalid
											jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
												jQuery('#yks-mailchimp-api-key').parents('td').find('.mailChimp_api_key_validation_message').first().html("<img src=<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?> alt=message > <?php _e("Sorry, that is an invalid MailChimp API key. Please check the error log on the debug options tab for further information.","yikes-inc-easy-mailchimp-extender"); ?>").css("color", "red").fadeIn();
											});	
											console.log('MailChimp API Response : '+response);
											jQuery('#submit').attr('disabled','disabled');
										};
									}
								});	
					} else {
						// if the length of the API input value is less than 1 (aka 0)
						jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
							jQuery('#yks-mailchimp-api-key').parents('td').find('.mailChimp_api_key_validation_message').first().html("<img src=<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?> alt=message > <?php _e("Error: Please enter a valid Mail Chimp API Key.", "yikes-inc-easy-mailchimp-extender"); ?>").css("color", "red").fadeIn();
						});	
					}
				}
			}, 1);
	}
	
	// run the validation on every keyup
	jQuery('#yks-mailchimp-api-key').keyup(function() {
			stop();
			yikes_mc_api_key_validate();
	});
	
	/** Enable our Button if a valid key was previously saved **/
	<?php if ( get_option( 'api_validation' ) == 'valid_api_key' ) { ?>
		jQuery('#submit').removeAttr('disabled');
	<?php } ?>
	
	// Reset Plugin Ajax Request
	$('#yks-mc-reset-plugin-settings').click(function(e) {
		$("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-reset-warning-icon'></div><p><?php _e("Are you sure you want to reset your MailChimp settings? This cannot be undone.", "yikes-inc-easy-mailchimp-extender" ); ?></p></div>").dialog({
		 title : "Reset MailChimp Settings?",
		 buttons : {
			"Yes" : function() {
				 $.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'yks_mc_reset_plugin_settings'
					},
					dataType: 'json',
					success: function () {
						$( "#yks_mc_reset_plugin_settings" ).html('<div class="dashicons dashicons-yes yks-mc-success-icon"></div><p><?php _e("MailChimp settings have successfully been reset", "yikes-inc-easy-mailchimp-extender" ); ?></p><span class="yks-mc-reset-plugin-settings-preloader-container"><img class="yks-mc-reset-plugin-settings-preloader" src="<?php echo plugin_dir_url(__FILE__).'../images/preloader.gif'; ?>" alt="preloader" /></span>');
						$( "#yks_mc_reset_plugin_settings" ).next().hide();
						$( "#yks_mc_reset_plugin_settings" ).prev().text("Success!");
						setTimeout(function() {	
							location.reload();
						}, 2000);
					},
					error: function() {
						alert('Error resetting plugin settings. If the error persists, uninstall and reinstall the plugin to reset your options.');
					}
				});
			},
			"Cancel" : function() {
			  $(this).dialog("close");
			}
		  },
		  modal: true,
		  resizable: false
		});
		e.preventDefault();
	});
	
	
	/* 
	* Clear our Error Log 
	*
	* since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.clear-yks-mc-error-log' , function() {
		
		jQuery( '#yks-mc-error-log-table' ).fadeTo( 'fast' , .5 );
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'yks_mailchimp_form',
				form_action: 'clear_yks_mc_error_log'
			}, 
			success: function (response) {
				setTimeout(function() {	
					jQuery( '#yks-mc-error-log-table' ).fadeOut( 'fast' , function() {
						jQuery( '.clear-yks-mc-error-log' ).attr( 'disabled' , 'disabled' );
						setTimeout(function() {
							jQuery( '.yks-mc-error-log-table-row' ).html( '<em style="display:none;">no errors logged</em>' );
							setTimeout(function() {
								jQuery( '.yks-mc-error-log-table-row' ).find( 'em' ).fadeIn('fast');
							}, 300);
						}, 250 );
					});
				}, 1000 );
				console.log( response );
			},
			error : function(error_response) {
				alert( 'There was an error with your request. Unable to clear the erorr log!' );
				console.log(error_response.responseText);
				jQuery( '#yks-mc-error-log-table' ).fadeTo( 'fast' , 1 );
			}
		});
	});
			
});

// function which runs when we change the OptIn value (from single to double, or double to single)
function changeOptinValue() {
	var newOptinValue = jQuery('#yks-mailchimp-optIn').val();
	if ( newOptinValue == 'true' ) {
		jQuery('label[for="single-optin-message"]').slideUp('fast',function() {
			jQuery('label[for="double-optin-message"]').slideDown('fast');
		});	
	} else {
		jQuery('label[for="double-optin-message"]').slideUp('fast',function() {
			jQuery('label[for="single-optin-message"]').slideDown('fast');
		});	
	}
}

function changeOptinSubscribeCheckbox() {
	var newOptinValue = jQuery('#yks-mailchimp-optIn-checkbox').val();
	if ( newOptinValue == '1' ) {
		jQuery('.optin-checkbox-default-list-container').fadeIn('fast');
	} else {
		jQuery('.optin-checkbox-default-list-container').fadeOut('fast');
	}
}
function changereCAPTCHAdropdown() {
	var newreCAPTCHAValue = jQuery('#yks-mailchimp-recaptcha-setting').val();
	if ( newreCAPTCHAValue == '1' ) {
		jQuery('.recaptcha-settings-hidden-container').fadeIn('fast');
	} else {
		jQuery('.recaptcha-settings-hidden-container').fadeOut('fast');
	}
}
jQuery(document).ready(function() {
	changeOptinSubscribeCheckbox();
});
</script>

<!-- get and store our api key option -->
<?php
	$api_key_option = get_option( 'api_validation' );
	$wordPress_version = get_bloginfo( 'version' );
		
	// used to dictate the active tab
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'form_options';
?>
<div class="wrap">
<!-- yikes logo on all settings pages -->
<?php echo $this->help_review_container(); ?>

<!-- tabs -->
<h2 class="nav-tab-wrapper">
    <a href="?page=yks-mailchimp-form&tab=form_options" class="nav-tab <?php echo $active_tab == 'form_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Form Options','yikes-inc-easy-mailchimp-extender'); ?></a>
    <a href="?page=yks-mailchimp-form&tab=recaptcha_settings" class="nav-tab <?php echo $active_tab == 'recaptcha_settings' ? 'nav-tab-active' : ''; ?>"><?php _e('ReCaptcha Options','yikes-inc-easy-mailchimp-extender'); ?></a>
	<a href="?page=yks-mailchimp-form&tab=debug_options" class="nav-tab <?php echo $active_tab == 'debug_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Debug Options','yikes-inc-easy-mailchimp-extender'); ?></a>
</h2>

	<h2 id="ykseme-page-header">
		<div id="ykseme-icon" class="icon32"></div><?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
	</h2>
	
	<div class="yks-status" id="yks-status"></div>
	
<?php if ( $active_tab == 'form_options' ) { ?>
	
	<h3><?php _e('Manage Mailchimp Forms Settings','yikes-inc-easy-mailchimp-extender'); ?></h3>

	<!-- WordPress version number and SSL error checking -->
	<!-- check WordPress version num. and display an error if its outdated -->
	<?php if ( $wordPress_version < '3.9' ) { ?>
		<div class="error">
			<h3><div class="dashicons dashicons-no yks_mc_error_x"></div><?php _e( 'WordPress Version Number Error', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
			<p><?php _e( "We're sorry, but it looks like your using an outdated version of WordPress. You won't be able to access the tinyMCE button to insert forms into pages and posts unless you update to 3.9 or later.", "yikes-inc-easy-mailchimp-extender" ); ?></p>
		</div>
	<?php } 
	
		
	if( !function_exists( 'mcrypt_module_open' ) ) {
		?>
			<div class="error">
				<h3><div class="dashicons dashicons-no yks_mc_error_x"></div><?php _e( 'Encryption Module Not Installed', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
				<p><?php _e( "We're sorry, but it looks like you don't have the php mcrpyt module installed. This module is used to encrpyt your API key for security purposes. Please install mcrypt. If you are unsure how, please contact your host provider.", "yikes-inc-easy-mailchimp-extender" ); ?></p>
			</div
		<?php
	}

	
	// check if the user is on localhost
	// if so, they need to enable SSL on localhost
	if ( $this->yks_mc_is_user_localhost() ) {
	?>
		<div class="update-nag">
			<span class="yks-mc-icon-notice"><h3><?php _e( 'LocalHost Detected :', 'yikes-inc-easy-mailchimp-extender' ); ?></h3></span>
			<p><?php _e( 'It looks like your using Easy MailChimp Forms by YIKES Inc. on localhost.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<p><?php _e( 'If you are unable to validate your API key, and/or receive the error message' , 'yikes-inc-easy-mailchimp-extender' );  ?><em> <?php _e('"SSL certificate problem: unable to get local issuer certificate"', 'yikes-inc-easy-mailchimp-extender' ); ?></em> <?php _e('head over to the', 'yikes-inc-easy-mailchimp-extender' ); ?> <a href="<?php echo admin_url('admin.php?page=yks-mailchimp-form&tab=debug_options' ); ?>" ><?php _e('Debug Options', 'yikes-inc-easy-mailchimp-extender' ); ?></a> <?php _e('tab and set the SSL Verify Peer option to "False" and try again.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
	<?php } ?>	
	
	<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form">
		<table class="form-table yks-admin-form">
			<tbody>
	
				<!-- MailChimp API Key Field -->
				<tr valign="top">
					<th scope="row">
						<label for="yks-mailchimp-api-key"><?php _e('Your Mailchimp API Key','yikes-inc-easy-mailchimp-extender'); ?></label>
					</th>
					<td>
						<input name="yks-mailchimp-api-key" id="yks-mailchimp-api-key" value="<?php echo isset( $this->optionVal['api-key'] ) && $this->optionVal['api-key'] != '' ? $this->yikes_mc_encryptIt($this->optionVal['api-key']) : ''; ?>" class="regular-text" /><span class="mailChimp_api_key_validation_message"></span><img class="mailChimp_api_key_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" ><span class="mailChimp_api_key_validation"></span><?php if ( get_option( 'api_validation' ) == 'valid_api_key' ) { echo '<span class="mailChimp_api_key_validation_message" style="color: green; display: inline;"><img src="'.plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-checkmark.png" alt="message">' . __(' Valid API Key','yikes-inc-easy-mailchimp-extender') . '</span>'; } else { echo '<span class="mailChimp_api_key_validation_message" style="display: inline; color: red;"><img src="' .plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png" alt="message">' . __(' Sorry, that is an invalid MailChimp API key. Please check the error log on the debug options tab for further information.','yikes-inc-easy-mailchimp-extender') . '</span>'; } ?>
					</td>
				</tr>
				<!-- MailChimp API Key Description -->
				<tr>
					<td></td>
					<td class="yks-settings-description" style="display:block;padding-bottom:5px !important;">
						<?php _e('Please enter your MailChimp API Key above. The API Key allows your WordPress site to communicate with your MailChimp account.','yikes-inc-easy-mailchimp-extender'); ?><br />
						<?php _e('For help, visit the MailChimp Support article :','yikes-inc-easy-mailchimp-extender'); ?> <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank"><?php _e('Where can I find my API Key?','yikes-inc-easy-mailchimp-extender'); ?></a><br />
						<strong style="display:block;margin-top:2em;"><?php _e( 'Issues? check the error log at the bottom of the ' , 'yikes-inc-easy-mailchimp-extender' ); ?><a href="?page=yks-mailchimp-form&amp;tab=debug_options#yks-mc-error-log-table-jump-point"><?php _e( 'debug options' , 'yikes-inc-easy-mailchimp-extender' ); ?></a> <?php _e( 'tab for more info.' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
					</td>
				</tr>
				<!-- Preferred Form Layout (table or div) -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-flavor"><?php _e('Preferred Form Layout','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-flavor" id="yks-mailchimp-flavor" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['flavor'] === '0' ? ' selected' : ''); ?>><?php _e('table','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['flavor'] === '1' ? ' selected' : ''); ?>><?php _e('div','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Preferred Form Layout Description -->
					<td class="yks-settings-description">
						<?php _e('Choose whether you want your forms to use a table or div layout.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>				
				<tr valign="top">
				<!-- Optin Value (single or double) -->
					<th scope="row"><label for="yks-mailchimp-optIn">Single or Double Opt-In</label></th>
					<td>
						<select name="yks-mailchimp-optin" id="yks-mailchimp-optIn" class="regular-text" onchange="changeOptinValue();" />
							<option value="false"<?php echo ($this->optionVal['optin'] === 'false' ? ' selected' : ''); ?>><?php _e('Single Opt-In','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="true"<?php echo ($this->optionVal['optin'] === 'true' ? ' selected' : ''); ?>><?php _e('Double Opt-In','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Optin Description -->
					<td class="yks-settings-description">
						<?php _e('A single opt-in will add the user to your list without any further interaction.','yikes-inc-easy-mailchimp-extender'); ?> <br /> 
						<?php _e('A double opt-in will send an email to the user asking them to confirm their subscription.','yikes-inc-easy-mailchimp-extender'); ?> <br />
						<?php _e('This will also dictate the opt-in settings for people leaving comments.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr valign="top">
					<!-- Custom Opt-In Message -->
					<th scope="row"><label for="yks-mailchimp-custom-optIn-message"><?php _e('Success Message','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<label for="double-optin-message" <?php if ($this->optionVal['optin'] == 'false') { echo 'style="display:none;"'; } ?>><b><?php _e('Double Opt-In Message','yikes-inc-easy-mailchimp-extender'); ?></b>
							<textarea id="double_optin_message" name="double-optin-message" style="display:block;width:450px;resize:vertical;min-height:150px;"><?php echo stripslashes( $this->optionVal['double-optin-message'] ); ?></textarea>
						</label>
						
						<label for="single-optin-message" <?php if ($this->optionVal['optin'] == 'true') { echo 'style="display:none;"'; } ?>><b><?php _e('Single Opt-In Message','yikes-inc-easy-mailchimp-extender'); ?></b>
							<textarea id="single_optin_message" name="single-optin-message" style="display:block;width:450px;resize:vertical;min-height:150px;"><?php echo stripslashes( $this->optionVal['single-optin-message'] ); ?></textarea>
						</label>
					
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Advanced Debug Description -->
					<td class="yks-settings-description">
						<em><?php _e('Note: This is the message that gets displayed back to the user upon successful submission of this form.','yikes-inc-easy-mailchimp-extender'); ?></em>
					</td>
				</tr>
				<tr valign="top">
					<!-- jQuery UI -->
					<th scope="row"><label for="yks-mailchimp-jquery-datepicker"><?php _e('Use JQuery UI Datepicker','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<input type="checkbox" name="yks-mailchimp-jquery-datepicker" class="yks-mailchimp-interest-group-label" value="1" <?php if ( isset( $this->optionVal['yks-mailchimp-jquery-datepicker'] ) && $this->optionVal['yks-mailchimp-jquery-datepicker']	== '1' ) { echo  'checked="checked"'; } ?>/>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Custom Interest Group Label Description -->
					<td class="yks-settings-description">
						<?php _e('Enable this setting to use the jQuery UI datepicker for all date fields on the front end of your site. Disable if you have no date fields (will help prevent conflicts).','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr valign="top">
					<!-- *= required field visibility -->
					<th scope="row"><label for="yks-mailchimp-required-text"><?php _e('Required Field Text','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-required-text" id="yks-mailchimp-required-text" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['yks-mailchimp-required-text'] === '0' ? ' selected' : ''); ?>><?php _e('Hide','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['yks-mailchimp-required-text'] === '1' ? ' selected' : ''); ?>><?php _e('Show','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- *= required field visibility Description -->
					<td class="yks-settings-description">
						<?php _e('Hide the "required field" text at the top of the opt-in forms. example:','yikes-inc-easy-mailchimp-extender'); ?> <em>* = <?php _e('required field','yikes-inc-easy-mailchimp-extender'); ?></em>
					</td>
				</tr>
				<tr valign="top">
					<!-- Display OPTIN CHECKBOX SETTING -->
					<th scope="row"><label for="yks-mailchimp-optIn-checkbox"><?php _e('Display opt-in checkbox on comment forms?','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-optIn-checkbox" id="yks-mailchimp-optIn-checkbox" class="regular-text" onchange="changeOptinSubscribeCheckbox();" />
							<option value="0"<?php echo ($this->optionVal['optIn-checkbox'] === '0' ? ' selected' : ''); ?>><?php _e('Hide','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['optIn-checkbox'] === '1' ? ' selected' : ''); ?>><?php _e('Show','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- OPTIN CHECKBOX SETTING Description -->
					<td class="yks-settings-description">
						<?php _e('This will display a checkbox just above the submit button on all comment forms. If selected, any users leaving comments will also be added to the mailing list.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr class="optin-checkbox-default-list-container">
					<!-- Custom Interest Group Label -->
					<th scope="row"><label for="yks-mailchimp-optin-checkbox-text"><?php _e('Custom Comment Checkbox Text','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<input type="text" name="yks-mailchimp-optin-checkbox-text" placeholder="Sign Me Up For <?php echo bloginfo('name'); ?>'s Newsletter" class="yks-mailchimp-interest-group-label" value="<?php echo $this->optionVal['yks-mailchimp-optin-checkbox-text']; ?>" />
					</td>
				</tr>
				<tr valign="top" class="optin-checkbox-default-list-container">
					<!-- Optin Checkbox Default List to Submit Subscribers Too -->
					<th scope="row"><label for="yks-mailchimp-custom-optIn-message"><?php _e('Default List','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<!-- get all lists from MailChimp -->
						<?php 
						if ( $api_key_option == 'invalid_api_key' ) {
							echo '<div id="ajax_list_replace"><select disabled="disabled"><option>Please Enter a Valid API Key</option></select></div>';
						} else {
							$this->getOptionsLists();
						}
						?>
					</td>
				</tr>
				<tr class="optin-checkbox-default-list-container">
					<td></td>
					<td class="yks-settings-description"> <!-- Description of optin checkbox default list-->
						<?php _e('This is the default list users will be subscribed to when submitting a comment.','yikes-inc-easy-mailchimp-extender'); ?><br />
						<em><?php _e('Note : Its best to select a form where only the email , first name and/or last name are required or you may run into issues.','yikes-inc-easy-mailchimp-extender'); ?></em><br />
						<em><strong><?php _e('Required Merge Variable Names : E-Mail : EMAIL , First Name : FNAME , Last Name : LNAME , Name : NAME','yikes-inc-easy-mailchimp-extender'); ?></strong></em>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Save Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>" disabled="disabled"><input type="submit" name="yks-mc-reset-plugin-settings" id="yks-mc-reset-plugin-settings" class="button yikes-mc-button-red" value="<?php _e( 'Reset Plugin Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></td>
				</tr>	
			
			</tbody>
		</table>
	</form>

	<?php } else if ( $active_tab == 'recaptcha_settings' ) { ?>			
			
			<h3><?php _e('ReCaptcha Settings','yikes-inc-easy-mailchimp-extender'); ?></h3>
			<p style="padding-bottom:0 !important;" class="yks-settings-description"><?php _e('reCAPTCHA is a free service to protect your website from spam and abuse. reCAPTCHA uses an advanced risk analysis engine and adaptive CAPTCHAs to keep automated software from engaging in abusive activities on your site. It does this while letting your valid users pass through with ease.','yikes-inc-easy-mailchimp-extender'); ?></p>
			<strong style="padding-left:5px;"><?php _e('ReCaptcha Demo' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong><br />
			<img class="reCaptcha-demo-gif" src="<?php echo YKSEME_URL . 'images/reCAPTCHA/recaptcha-demo.gif'; ?>" alt="reCAPTCHA Demo">
			<p><strong style="color: rgb(241, 5, 5);"><?php _e('Note' ,'yikes-inc-easy-mailchimp-extender'); ?> : </strong><?php _e("if you're displaying multiple forms on a single page, reCAPTCHA will only display on one form at a time." ,"yikes-inc-easy-mailchimp-extender"); ?></p>
			<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form-recaptcha-options">
				<table class="form-table yks-admin-form">
					<tbody>
				
					<!-- Recaptcha Enable/Disable (table or div) -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-recaptcha-setting"><?php _e('ReCaptcha Spam Protection','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-recaptcha-setting" id="yks-mailchimp-recaptcha-setting" class="regular-text" onchange="changereCAPTCHAdropdown();" />
							<option value="0"<?php echo ($this->optionVal['recaptcha-setting'] === '0' ? ' selected' : ''); ?>><?php _e('Disabled','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['recaptcha-setting'] === '1' ? ' selected' : ''); ?>><?php _e('Enabled','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Desecription for Recaptcha Option -->
					<td class="yks-settings-description">
						<em><?php _e('ReCaptcha prevents excessive form entires from spammers and bots.','yikes-inc-easy-mailchimp-extender'); ?></em>
					</td>
				</tr>
					<!-- ReCaptcha Input Fields -->
					<!-- hidden until ReCaptcha option is enabled -->
					<tr valign="top" class="recaptcha-settings-hidden-container" <?php if ( $this->optionVal['recaptcha-setting'] === '0' ) { ?> style="display:none;" <?php } ?>>
						<!-- Custom Opt-In Message -->
						<th scope="row"><label for="yks-mailchimp-recaptcha-api-key'"><?php _e('ReCaptcha Public API Key','yikes-inc-easy-mailchimp-extender'); ?></label></th>
						<td>
							<input type="text" name="yks-mailchimp-recaptcha-api-key" placeholder="reCAPTCHA Public API Key" class="yks-mailchimp-interest-group-label" value="<?php echo $this->optionVal['recaptcha-api-key']; ?>" />				
						</td>
					</tr>
					<!-- hidden until ReCaptcha option is enabled -->
					<tr valign="top" class="recaptcha-settings-hidden-container" <?php if ( $this->optionVal['recaptcha-setting'] === '0' ) { ?> style="display:none;" <?php } ?>>
						<!-- Custom Opt-In Message -->
						<th scope="row"><label for="yks-mailchimp-recaptcha-private-api-key'"><?php _e('ReCaptcha Private API Key','yikes-inc-easy-mailchimp-extender'); ?></label></th>
						<td>
							<input type="text" name="yks-mailchimp-recaptcha-private-api-key" placeholder="reCAPTCHA Private API Key" class="yks-mailchimp-interest-group-label" value="<?php echo $this->optionVal['recaptcha-private-api-key']; ?>" />				
						</td>
					</tr>
					<tr class="recaptcha-settings-hidden-container" <?php if ( $this->optionVal['recaptcha-setting'] === '0' ) { ?> style="display:none;" <?php } ?>>
						<td></td>
						<!-- Advanced Debug Description -->
						<td class="yks-settings-description">
							<em><?php _e('to retreive a recaptcha API key, sign up for an account','yikes-inc-easy-mailchimp-extender'); ?> <a href="https://www.google.com/recaptcha/admin" target="_blank" title="ReCaptcha API Key"><?php _e('here','yikes-inc-easy-mailchimp-extender'); ?></a></em>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Save Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>" ></td>
					</tr>
					
					</tbody>
				</table>
			</form>	
	
			<!-- END RECAPTCHA SETTINGS PAGE -->
	
	<?php } else { ?>
					
			<!-- START ADVANCED DEBUG SETTINGS PAGE -->
		
			<h3><?php _e('Debug Settings','yikes-inc-easy-mailchimp-extender'); ?></h3>
		
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form-debug-options">
			<table class="form-table yks-admin-form">
				<tbody>
				
				<!-- Advanced Debug -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-debug"><?php _e('Advanced Error Logging','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-debug" id="yks-mailchimp-debug" class="regular-text" />
							<option value="0" <?php echo ($this->optionVal['debug'] === '0' ? ' selected' : ''); ?>><?php _e('Disabled','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1" <?php echo ($this->optionVal['debug'] === '1' ? ' selected' : ''); ?>><?php _e('Enabled','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Advanced Debug Description -->
					<td class="yks-settings-description">
						<?php _e( "Enable if you're having problems with any aspect of the MailChimp API." , "yikes-inc-easy-mailchimp-extender" ); ?><br /><?php _e( "Enabling Advanced Error Logging will log exact error codes returned by MailChimp to the error log below." , "yikes-inc-easy-mailchimp-extender" ); ?>
					</td>
				</tr>
				<!-- Toggle Verify Peer -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-debug"><?php _e('SSL Verify Peer','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-ssl-verify-peer" id="yks-mailchimp-ssl-verify-peer" class="regular-text" />
							<option value="true" <?php echo ($this->optionVal['ssl_verify_peer'] === 'true' ? ' selected' : ''); ?>><?php _e('True','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="false" <?php echo ($this->optionVal['ssl_verify_peer'] === 'false' ? ' selected' : ''); ?>><?php _e('False','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Toggle Verify Peer Description -->
					<td class="yks-settings-description">
						<?php _e( "If you receive the following response from MailChimp API : 'SSL certificate problem, verify that the CA cert is OK' " , "yikes-inc-easy-mailchimp-extender" ); ?><br />
						<?php _e( "set this setting to false." , "yikes-inc-easy-mailchimp-extender" ); ?>
					</td>
				</tr>
				

				<table class="form-table yks-admin-form">
					<tbody>
						<!-- Plugin Info -->
						<h3><?php _e('Plugin Information','yikes-inc-easy-mailchimp-extender'); ?></h3>
						<!-- Issues? Contact Us. -->
						<p>
							<?php _e('If you experience any issues with our plugin, please','yikes-inc-easy-mailchimp-extender'); ?> <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues" target="_blank"><?php _e('submit a New Issue on our Github Issue Tracker','yikes-inc-easy-mailchimp-extender'); ?></a>. <?php _e('Please include the information below to help us troubleshoot your problem.','yikes-inc-easy-mailchimp-extender'); ?>
						</p>
						<!-- User Debug Section -->
						<!-- Plugin Version, Browser Version etc. -->
						<tr valign="top">
							<th scope="row"><label><?php _e('Plugin Version','yikes-inc-easy-mailchimp-extender'); ?></label></th>
							<td><?php echo YKSEME_VERSION_CURRENT; ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Wordpress Version','yikes-inc-easy-mailchimp-extender'); ?></label></th>
							<td><?php echo get_bloginfo( 'version' ); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Browser Information','yikes-inc-easy-mailchimp-extender'); ?></label></th>
							<td>
								<?php
								$theBrowser = $this->getBrowser();
								echo $theBrowser['name'].' '.$theBrowser['version'].' on '.$theBrowser['platform'];					
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Server Information','yikes-inc-easy-mailchimp-extender'); ?></label></th>
							<td>
								<?php echo $_SERVER['SERVER_SOFTWARE']; ?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Save Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></td>
						</tr>
						
						<!-- check contents of our error log -->
						<?php $error_file_contents = $this->yks_mc_generate_error_log_table(); ?>
						<tr valign="top">
						
							<th scope="row"><h3 style="display:block;width:100%;margin-bottom:1em;"><?php _e('Error Log','yikes-inc-easy-mailchimp-extender'); ?></h3><a href="#" onclick="return false;" class="button-secondary clear-yks-mc-error-log" <?php if ( !$error_file_contents ) { ?> disabled="disabled" <?php } ?>><?php _e( 'clear log' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></th>
							<td class="yks-mc-error-log-table-row" id="yks-mc-error-log-table-jump-point" >
								
								<?php 
									if ( $error_file_contents ) {
										wp_enqueue_style( 'yks-mc-error-log-table-styles' , YKSEME_URL . 'css/yks-mc-error-log-table-styles.css' , array() , 'all' );
										wp_enqueue_style( 'yks-mc-error-log-table-styles' );
								?>
										<!-- error log table -->
											<table cellspacing='0' id='yks-mc-error-log-table'> <!-- cellspacing='0' is important, must stay -->
												<!-- Table Header -->
												<thead>
													<tr>
														<th><?php _e( "Error Message" , "yikes-inc-easy-mailchimp-extender" ); ?></th>
														<th><?php _e( "Date/Time" , "yikes-inc-easy-mailchimp-extender" ); ?></th>
													</tr>
												</thead>
												<!-- Table Header -->

												<!-- Table Body -->
												<tbody>
													<?php 
														// dump the contents of the error log
														print_r( $error_file_contents );
													?>
												</tbody>
												<!-- Table Body -->
												
											</table>
										<?php
									} else {
										if ( function_exists( 'file_put_contents' ) && function_exists('file_get_contents' ) ) {
											echo '<em>' . __( 'no errors logged' , 'yikes-inc-easy-mailchimp-extender' ) . '</em>';
										} else {
											echo '<em>' . __( 'file_get_contents or file_get_contents is disabled and unable to write to the error log. Enable these functions to activate the error log.' , 'yikes-inc-easy-mailchimp-extender' ) . '</em>';
										}
									}	
								?>
							</td>
						</tr>
						<!-- end the erorr log -->
						
					</tbody>
				</table>
				
				</tbody>
			</table>
		</form>
	
	<?php } ?>
	
<?php

// if cURL is not enabled on the site
// we need to display an error and let the user know how to resolve the issue
} else {
?>

<div class="wrap">

<div id="ykseme-icon" class="icon32"></div>
	<h2 id="ykseme-page-header">
		<?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
	</h2>
	
	<div class="error">
		<h2><?php _e( 'Error', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
        <p><?php _e( "We're sorry, but cURL is disabled on your server. The MailChimp API utilizes cURL to send and retrieve data." , "yikes-inc-easy-mailchimp-extender" ); ?></p>
		<?php
			$this->yks_check_if_php_ini_exists();
		?>
		<hr />
		<input type="submit" name="submit" class="button-primary yikes_cURL_help_button" value="<?php _e('Help!', 'yikes-inc-easy-mailchimp-extender'); ?>" onclick=" jQuery('.yikes_phpinfo_container').slideUp(); jQuery('.yikes_cURL_instructions_hidden').slideToggle();" /> <input type="submit" name="submit" class="button-secondary yikes_cURL_info_button" value="<?php _e('What is cURL?', 'yikes-inc-easy-mailchimp-extender'); ?>" onclick="window.open('http://www.php.net/manual/en/book.curl.php');" /> <input type="submit" name="submit" class="button-secondary yikes_cURL_info_button" value="<?php _e('Check phpinfo()', 'yikes-inc-easy-mailchimp-extender'); ?>" onclick="jQuery('.yikes_phpinfo_container').slideToggle(); jQuery('.yikes_cURL_instructions_hidden').slideUp();" />
		<div class="yikes_cURL_instructions_hidden">	
			<strong><p><?php _e( 'Steps To Resolve The Issue', 'yikes-inc-easy-mailchimp-extender' ); ?> :</p></strong>
			<?php if ( $this->yks_mc_is_user_localhost() ) { // if user is on localhost, display localhost resolution ?>
				<p>It appears you are working off of a localhost installation. To get cURL working on your localhost instance, work through the following steps.</p>
				<ol style="margin-left:2em;">
					<li><?php _e( 'You can enable cURL by turning on the cURL module within your php.ini file', 'yikes-inc-easy-mailchimp-extender' ); ?> <em style="margin-left:.75em;">   <?php _e('You should find the php.ini file located here : ', 'yikes-inc-easy-mailchimp-extender' ); ?><?php echo $this->yks_display_php_ini_location(); ?></em></li>
					<li><?php _e( 'Once found, open up php.ini and locate the line ";extension=php_curl.dll".', 'yikes-inc-easy-mailchimp-extender' ); ?></li>
					<li><?php _e( 'Remove the semi colon before the line, to un-comment it and make the cURL module active.', 'yikes-inc-easy-mailchimp-extender' ); ?></li>
					<li><?php _e( 'Re-save and close the file.', 'yikes-inc-easy-mailchimp-extender' ); ?></li>
					<li><?php _e( 'Restart your Apache and MySQL services and re-load this page.', 'yikes-inc-easy-mailchimp-extender' ); ?></li>
				</ol>
			<?php } else { ?>
				<p>Please get in touch with your hosting provider, and let them know that you need cURL enabled on your server for the plugin to communicate with the MailChimp API.</p>
			<?php } ?>
		</div>
		<div class="yikes_phpinfo_container">
			<?php
				include_once (YKSEME_PATH . 'process/php_info.php');
			?>
		</div>
    </div>
	
</div>
<?php } ?>