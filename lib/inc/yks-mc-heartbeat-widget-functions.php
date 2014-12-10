					<!-- 
						** Trigger our ajax call, and then include our ChimpChatter template , to properly populate the data
						apply our styles on initial page load,
						this is for adding our icon to the widget title,
						for a little branding action
					-->
					<style>
					#yks_mc_account_activity_widget > h3 > span:before {
						content: url('<?php echo plugins_url(); ?>/yikes-inc-easy-mailchimp-extender/images/yikes_logo_widget_icon.png');
						width:33px;
						float:left;
						height:10px;
						margin: -3px 10px 0 0px;
					}
					</style>
					<script type="text/javascript">
					jQuery(document).ready(function() {
						// add the preloader to the widget
						jQuery('#yks-admin-chimp-chatter').html();
					
						var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
						jQuery('#yks-mailchimp-api-key').val();
						// store datacenter value, from end of api key
						var dataCenter = apiKey.substr(apiKey.indexOf("-") + 1);
							// post the data to our MailChimp Chatter function inside of lib.ajax.php
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yks_mailchimp_form',
								form_action: 'yks_get_widget_chimp_chatter',
								api_key: apiKey,
								data_center: dataCenter
							},
							dataType: 'html',
							success: function(response) {
																	
								// populate the original chimp chatter input with our original response
								jQuery('#yks_mc_account_activity_widget').find('.inside').html(response);
									
								// create hidden input fields to store our returned data for comparison
								// create our new chimp chatter response field
								jQuery('#yks-admin-chimp-chatter').before('<div style="display:none;" id="new_chimp_chatter_response"></div>');
								// create our original chimp chatter response
								jQuery('#yks-admin-chimp-chatter').before('<div style="display:none;" id="original_chimp_chatter_response"></div>');
									
								// populate the visible chimp chatter div with the content
								// on original page load
								jQuery('#yks-admin-chimp-chatter').not('#new_chimp_chatter_response').html(response);
								jQuery('#original_chimp_chatter_response').html(response);
																			
							},
							error: function(response) {
								jQuery('.nav-tab-wrapper').after('<p style="width:100%;text-align:center;margin:1em 0;">There was an error processing your request. Please try again. If this error persists, please open a support thread <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender" title="Yikes Inc Easy MailChimp GitHub Issue Tracker" target="_blank">here</a>.</p>');
							}
						});
					});
					</script>
					<img style="display:block;margin:0 auto;margin-top:2em;margin-bottom:1em;" class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >