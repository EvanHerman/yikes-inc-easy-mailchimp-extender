<!--
To Do - 11.8 :
	- add - sign to remove fields (need to unset from json array - on update)
	- update dropdown/radio fields

-->
<style>
#TB_ajaxContent {
	width: auto !important;
}
</style>
<?php
	// if blog is greater than or equal to WordPress 3.9
	// enqueue our new jQuery UI dialog styles
	wp_enqueue_script('jquery-ui-dialog');
	if ( get_bloginfo( 'version' ) >= '3.9' ) {
		wp_enqueue_style("wp-jquery-ui-dialog");
	}
?>
<script type="text/javascript">
        jQuery(document).ready(function($)
                {
				
				$('.yks-mc-color-picker').wpColorPicker();
			
                function noListsCheck()
                        {
                        if($('#yks-list-wrapper .yks-list-container').size() <= 0)
                                {
								var plugin_directory_url = '<?php echo plugin_dir_url( __FILE__ ); ?>';
								 $('body').find('.wrap').find('h3:first-child').hide();
								 $('#yks-lists-dropdown').next().css('opacity',0);
								 $('#yks-list-wrapper').css({ 'background' : 'url("'+plugin_directory_url+'../images/yks_mc_lets_get_started.png")', 'height' : '175px' , 'width' : '400px' , 'background-repeat' : 'no-repeat' , 'background-position' : 'center', 'margin-top' : '2em' });
								}
						}
                function EnterListID (lid, name)
                        {
                                if(lid !== '')
                                        {
                                        $.ajax({
                                                type:   'POST',
                                                url:    ajaxurl,
                                                data: {
                                                                        action:                 'yks_mailchimp_form',
                                                                        form_action:    'list_add',
                                                                        list_id:                lid,
                                                                        name:                   name
                                                                        },
                                                dataType: 'json',
                                                success: function(MAILCHIMP)
                                                        {
                                                        if(MAILCHIMP != '-1')
                                                                {
                                                                if($('#yks-list-wrapper .yks-list-container').size() <= 0)
                                                                        {
                                                                        $('#yks-list-wrapper').html('');
                                                                        }
																	$('#yks-list-wrapper').append(MAILCHIMP);
																	var container_length = $('#yks-list-wrapper').find('.panel-collapse').length;
																	$('#yks-list-wrapper').find('.panel-collapse').last().removeAttr('id').attr('id','coallpse_'+container_length);
																	$('#yks-list-wrapper').find('.panel-heading').last().removeAttr('href').attr('href','#coallpse_'+container_length);
																	$('#yks-lists-dropdown').next().css('opacity',1);
																	$('#yks-list-wrapper').css({ 'background' : 'transparent', 'height' : 'auto' , 'width' : 'auto', 'margin-top' : '0' });
																	scrollToElement($('#yks-list-wrapper .panel-heading').last());
																	$('#yks-list-wrapper').find('.panel-heading').last().removeClass( 'collapsed' );
																	$('#yks-list-wrapper').find('.panel-collapse').last().removeClass( 'collapse' ).addClass('collapse in');
																	initializeScrollableLists();
																	$('.yks-mc-color-picker').wpColorPicker();
																	$.ajax({
																		type:   'POST',
																		url:    ajaxurl,
																		data: {
																			action: 'yks_mailchimp_form',
																			form_action: 'get_list_data',
																		},
																		dataType: 'html',
																		success: function(new_list_data) {
																			jQuery( '#merge-field-data' ).val( new_list_data );
																			// update the list data at the top of the form
																			console.log(new_list_data);				              
																		}
																	});
                                                                }
                                                        else
                                                                {
																$("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-reset-warning-icon'></div><p><?php _e("Oops.. The list ID you entered appears to be incorrect.","yikes-inc-easy-mailchimp-extender"); ?></p></div>").dialog({
																	 title : "Incorrect List ID",
																	 buttons : {
																		"Ok" : function() {
																			 $(this).dialog("close");
																		}
																	  },
																	  modal: true,
																	  draggable: false,
																	  resizable: false
																	});
                                                                }
                                                        },
												error: function(MAILCHIMP) {
													alert('<?php _e('There was an error!','yikes-inc-easy-mailchimp-extender'); ?>');
													var mailChimpError = $(MAILCHIMP);
													console.log(mailChimpError);
												}
                                        });
                                        }
                                                
                        return false;
                        }
                function scrollToElement(e)
                        {
                        $('html,body').animate({
                                        scrollTop: $(e).offset().top
                                }, 'slow');
                        }
                function initializeScrollableLists()
                        {
						// initialize sortable on our row items
                        $('.yks-mailchimp-fields-list').sortable({
                                axis:                            'y',
                                handle:                         '.yks-mailchimp-sorthandle',
                                placeholder:    'yks-mailchimp-fields-placeholder',
                                update: function(event, ui) {
										
										// form id
                                        var i = $(this).attr('rel');
                                        var newCt       = 0;
                                        var updateString        = '';
                                        var fieldCt     = $('#yks-mailchimp-fields-list_'+i+' .yks-mailchimp-fields-list-row').size();
										
										// update number on the placeholder input box after re-sort to store variable in appropriate locations
										$('#yks-mailchimp-fields-list_'+i+' .yks-mailchimp-fields-list-row').each(function() {
											var thisIndex = ($(this).index() + 1);
											$(this).find('.yks-mailchimp-field-placeholder').find('input').attr('name', 'placeholder-'+i+'-'+thisIndex);
											$(this).find('.yks-mailchimp-field-custom-field-class').find('input').attr('name', 'placeholder-'+i+'-'+thisIndex);
										});
									
										
                                        $('#yks-mailchimp-fields-list_'+i+' .yks-mailchimp-fields-list-row').each(function(e){
                                                var fid                         = $(this).attr('rel');
                                                updateString    += fid+':'+newCt;
                                                if((newCt+1) < fieldCt) updateString    += ';';
                                                newCt++;
                                        });
										
                                        // Update the sort orders
                                        if(updateString !== '')
                                                {
                                                $.ajax({
                                                        type:   'POST',
                                                        url:    ajaxurl,
                                                        data: {
                                                                                action:                                 'yks_mailchimp_form',
                                                                                list_id:                                i,
                                                                                update_string:  updateString,
                                                                                form_action:            'list_sort'
                                                                                },
                                                        dataType: 'json',
                                                        success: function(MAILCHIMP)
                                                                {
                                                                if(MAILCHIMP != '-1')
                                                                        {
                                                                        $('#yks-list-container_'+i).yksYellowFade();
                                                                        }
                                                                else
                                                                        {
                                                                        
                                                                        }
                                                                }
                                                });
                                                }
                                        }
                        });
                        }
                noListsCheck();
                initializeScrollableLists();
				// lists page form submission
				// ajax retreive all lists from users MC account
                $('#yks-lists-dropdown').live("submit", function(e) {
                        e.preventDefault();
                        var lid = $("select#yks-list-select option:selected").val();
                        var name = $('select#yks-list-select option:selected').html();
                                if (lid)
                                        {
                                                EnterListID (lid, name);
                                                $('#yks-submit-list-add').attr("disabled", true);
                                                $("select#yks-list-select option[value='']").prop('selected',true);
                                                $("select#yks-list-select option[value='" + lid + "']").remove();
												
                                                setInterval(function() {
                                                     $('#yks-submit-list-add').removeAttr("disabled");
                                                  },3000);
                                        }
                                else 
                                        {
												 $("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-delete-form-warning-icon'></div><p><?php _e('You need to select a Mailchimp list in order to create a form for it','yikes-inc-easy-mailchimp-extender'); ?></p></div>").dialog({
													title : "Select a List",
													buttons : {
														"Ok" : function() {
															$(this).dialog("close");
														}
													},
													modal: true,
													draggable: false,
													resizable: false
												});
                                        }
                        return false; 
                });
				// function which saves all of our settings
                $('.yks-mailchimp-list-update').live('click', function(e){
                        var i       = $(this).attr('rel');
                        var f       = '#yks-mailchimp-form_'+i;
						var theButton = $(this);
	
                        $.ajax({
                                type:   'POST',
                                url:    ajaxurl,
                                data: {
									action: 'yks_mailchimp_form',
                                    form_action: 'list_update',
                                    form_data: $(f).serialize()
                                },
                                dataType: 'json',
                                success: function(MAILCHIMP)
                                        {
										// console.log(MAILCHIMP);	
										// alert($(f).serialize());
                                        if(MAILCHIMP == '-1')
                                                {
													if ( theButton.parents('.yks-list-container').find('.yks-status-error').is(':visible') ) {
														return;
													} else {
														theButton.parents('.yks-list-container').find('.yks-status-error').stop().slideDown().delay(3000).fadeOut();
													}
												console.log(MAILCHIMP);
                                                }
                                        else
                                                {
													if ( theButton.parents('.yks-list-container').find('.yks-status').is(':visible') ) {
														return;
													} else {
														theButton.parents('.yks-list-container').find('.yks-status').stop().slideDown().delay(3000).fadeOut();
													}
													// console.log(MAILCHIMP);
                                                }
                                        },
								error: function(MAILCHIMP) 
										{
											alert(MAILCHIMP);
											console.log(MAILCHIMP);
										}
                        });
                        return false;
                });
				// function which deletes a list from our lists page
                $('.yks-mailchimp-delete').live('click', function(e){
                        var i		= $(this).attr('rel');
                        var title	= $(this).data('title');						
						$("select#yks-list-select").append('<option value="' + i + '">' + title +'</option>');

						$("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-delete-form-warning-icon'></div><p><?php _e("Are you sure you want to delete this form?",'yikes-inc-easy-mailchimp-extender'); ?></p></div>").dialog({
							 title : "Delete Imported Form?",
							 buttons : {
								"Yes" : function() {
									 $(this).dialog("close");
									 $.ajax({
                                        type:   'POST',
                                        url:    ajaxurl,
                                        data: {
                                              action: 'yks_mailchimp_form',
                                              form_action: 'list_delete',
                                              id: i
                                         },
                                        dataType: 'json',
                                        success: function(MAILCHIMP)
                                                {
                                                if(MAILCHIMP == '1')
                                                        {
															$('#yks-list-container_'+i).parents('.panel-body').prev().fadeOut('fast');
															$('#yks-list-container_'+i).parents('.panel-body').fadeOut('fast',function() {
																$(this).remove();
																scrollToElement($('#yks-list-wrapper'));
																noListsCheck();
															});
                                                        }
                                                }
									});
								},
								"Cancel" : function() {
								  $(this).dialog("close");
								}
							  },
							  modal: true,
							  draggable: false,
							 resizable: false
							});
							
                        return false;
                });
				// function which imports a specified list from MailChimp
                $('.yks-mailchimp-import').live('click', function(e){
                        var i       = $(this).attr('rel');
						var form_name = $(this).parents('.panel-body').prev().find('.yks_mc_list_title').text().trim();
						var clicked_import_button = $(this);	
                 						
						$("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-delete-form-warning-icon'></div><p><?php _e("Are you sure you want to re-import this forms fields from MailChimp?",'yikes-inc-easy-mailchimp-extender'); ?></p></div>").dialog({
							 title : "Re-Import Form Fields?",
							 buttons : {
								"Yes" : function() {
									 $(this).dialog("close");
									 
									// display a temporary 'loading' modal for user feedback
									$('<div id="yks_mc_reimporting_fields_dialog"><img class="yks-mc-reset-plugin-settings-preloader" src="<?php echo plugin_dir_url(__FILE__).'../images/preloader.gif'; ?>" alt="preloader" /><p style="width:100%;text-align:center;"><?php _e( "Re-Importing Form Fields" , "yikes-inc-easy-mailchimp-extender" ); ?></p></div>').dialog({
										 title : "Please Hold",
											  modal: true,
											  draggable: false,
											  resizable: false
									});  
									 
									 var this_success_message = clicked_import_button.parents('.panel-body').find('#yks-status');
									 clicked_import_button.parents('.panel-body').find('#yks-status').remove();
									 $.ajax({
                                        type:   'POST',
                                        url:    ajaxurl,
                                        data: {
                                            action:  'yks_mailchimp_form',
                                            form_action: 'merge_variables_reImport',
                                            id: i
                                        },
                                        dataType: 'json',
                                        success: function(MAILCHIMP)
                                                {
                                                if(MAILCHIMP != '-1')
                                                        {
														$('#yks_mc_reimporting_fields_dialog').dialog("destroy");
                                                        $($('#yks-mailchimp-fields-td_'+i)).replaceWith(MAILCHIMP);
                                                        $('#yks-mailchimp-fields-td_'+i).yksYellowFade();
														clicked_import_button.parents('.form-table').find('.yks-mailchimp-list-update').click();
															setTimeout(function() {
																clicked_import_button.parents('.panel-body').find('#yks-status-error').before(this_success_message);
															}, 5000);
															// alert the user that it was a success															
															$('<div id="yks_mc_reset_plugin_settings"><div class="dashicons dashicons-yes yks-mc-success-icon"></div><p><?php _e( "Your MailChimp form" , "yikes-inc-easy-mailchimp-extender" ); ?><strong> "'+form_name+'" </strong><?php _e(" was successfully updated", "yikes-inc-easy-mailchimp-extender" ); ?></p></div>').dialog({

															 title : "Form Successfully Updated",
															 buttons : {
																"Ok" : function() {
																	$(this).dialog("close");
																	 return false;
																}
															  },
															  modal: true,
															  draggable: false,
															  resizable: false
															});      
                                                        }
                                                else
                                                        {															
															$('#yks_mc_reimporting_fields_dialog').dialog("destroy");
															$("<div id='yks_mc_reset_plugin_settings'><div class='dashicons dashicons-yes yks-mc-success-icon'></div><p><?php _e("It looks like this form is already up to date!", "yikes-inc-easy-mailchimp-extender" ); ?></p></div>").dialog({
															 title : "Form Up To Date",
															 buttons : {
																"Ok" : function() {
																	$(this).dialog("close");
																	 return false;
																}
															  },
															  modal: true,
															  draggable: false,
															  resizable: false
															});
															return false;
                                                        }
                                                }
									});
								},
								"Cancel" : function() {
								  $(this).dialog("close");
								  return false;
								}
							  },
							  modal: true,
							  draggable: false,
							  resizable: false
							});

                        return false;
                });
				// Function which hides the notification at the top of the lists page
                $('.yks-notice-close').live('click', function(e){
                        $.ajax({
                                type:   'POST',
                                url:    ajaxurl,
                                data: {
                                    action: 'yks_mailchimp_form',
                                    form_action: 'notice_hide'
                                },
                                dataType: 'json',
                                success: function(MAILCHIMP)
                                        {
                                        if(MAILCHIMP != '-1')
                                                {
                                                $('.yks-notice').slideUp('fast');
                                                }
                                        }
                        });
                return false;
                });
				// toggling the notification
                $('.yks-notice-toggle').live('click', function(e){
                        if($('.yks-notice').hasClass('yks-hidden'))
                                {
                                $('.yks-notice').css('display', 'none');
                                $('.yks-notice').removeClass('yks-hidden');
                                }
                        $('.yks-notice').slideDown('fast');
                        return false;
                });
		// AJAX request which sends data to our yks_get_list_subscribers function
		// populates our thickbox with subscribers
		// passes the list_id and list_name to the PHP function that makes the API call
		jQuery('#yks-list-wrapper').delegate('.displayListSubscribers', 'click', function() {
			var specifiedListID = jQuery(this).attr('rel');
			var thisListName = jQuery(this).parents('.panel-body').prev().find('.yks_mc_list_title').text();
						 
			// run our ajax function and pass along our clicked link ID
				$.ajax({
                       type:   "POST",
                       url:    ajaxurl,
                       data: {
                           action:                 "yks_mailchimp_form", // What function to send the data too
                           form_action:            "yks_get_list_subscribers", // PHP function name
                           list_id: specifiedListID, // the specified list ID (created above)
						   list_name: thisListName // the specified list name (created above)
						},
                        dataType: "html", // must pass the data along as HTML , not jSON
                        success: function(MAILCHIMP)
                              {
									// on a successfull call, hide the ajax preloader and populate the thickbox with our subscribers
									jQuery(".mailChimp_get_subscribers_preloader").fadeOut('fast',function() {
										jQuery("#TB_ajaxContent").find(".yks_mc_subscribers").html(MAILCHIMP).append('<input class="mailChimp_list_id" type="hidden" value="'+specifiedListID+'" />').delay(800);
										 // initialize pagination
										 // and change some of the default strings
										jQuery('#yikes-mailchimp-subscribers-table').DataTable( {
											"oLanguage": {
												"sLengthMenu":	   "Show _MENU_ subscribers",
												"sInfo":		   "Showing _START_ to _END_ of _TOTAL_ subscribers"
											  }									
										} );
										// update cached subscriber count on list view 
											var current_subscribers = jQuery("body").find('.number-of-subscribers-'+specifiedListID).text();
											var header_current_subscribers_count = jQuery("#TB_window").find('.yks_mc_subscribers').find('.subscriber-count').find('.number').text();
											jQuery("#TB_window").find('.yks_mc_subscribers').find('.subscriber-count').find('.number').text(header_current_subscribers_count);
											jQuery("body").find('.number-of-subscribers-'+specifiedListID).text(header_current_subscribers_count+'  ');
									});	
															
							},
						// display an error if one is returned	
						error: function(xhr) {
							var response = xhr.responseText;
							console.log(response);
							var statusMessage = xhr.status + ' ' + xhr.statusText;
							var message  = 'Query failed, php script returned this status: ';
							var message = message + statusMessage + ' response: ' + response;
							console.log(message);
							jQuery('#TB_ajaxContent').find('.yks_mc_subscribers').html(message);
						}
				});
		});		
		
		// append remove button to end of subscriber on hover
		jQuery('.yks_mc_subscribers').undelegate('.yks-mailchimp-subscribers-list-row', 'mouseenter' ).delegate('.yks-mailchimp-subscribers-list-row', 'mouseenter', function() {
			var user_email_href = jQuery(this).find('.subscriber-mail-link').attr('rel');
			jQuery(this).find('td:last-child').append('<span class="yks-remove-subscriber dashicons dashicons-no-alt"></span>');
			jQuery(this).find('td:last-child').append('<a href="'+user_email_href+'" class="yks-email-subscriber dashicons dashicons-email-alt"></a>');
			jQuery(this).find('td:last-child').append('<span class="yks-view-subscriber-profile dashicons dashicons-id" rel="'+user_email_href+'" ></span>');
		});
		
		// remove the remove subscriber button on mouse leave
		jQuery('.yks_mc_subscribers').delegate('.yks-mailchimp-subscribers-list-row', 'mouseleave', function() {
			jQuery('.yks-mailchimp-subscribers-list-row').find('.yks-remove-subscriber').remove();
			jQuery('.yks-mailchimp-subscribers-list-row').find('.yks-email-subscriber').remove();
			jQuery('.yks-mailchimp-subscribers-list-row').find('.yks-view-subscriber-profile').remove();
		});
		
		// remove the specified subscriber
		// function runs when the red 'x' is clicked next to their name
		jQuery('.yks_mc_subscribers').delegate('.yks-remove-subscriber', 'click', function() {
			// create our variables that will be passed over to MailChimp
			var user_email = jQuery(this).parents('.yks-mailchimp-subscribers-list-row').find('.subscriber-mail-link').text();
			var list_id = jQuery(this).parents('.yks_mc_subscribers').find('.mailChimp_list_id').attr('value');
			var confirm_delete_user = confirm("<?php _e("Are you sure you want to unsubscribe","yikes-inc-easy-mailchimp-extender"); ?> "+user_email+" <?php _e("from this list?","yikes-inc-easy-mailchimp-extender"); ?>");
			var parent_element = jQuery(this).parents('.yks-mailchimp-subscribers-list-row');
			if (confirm_delete_user) {
				$.ajax({
						   type:   "POST",
						   url:    ajaxurl,
						   data: {
							   action: "yks_mailchimp_form",
							   form_action: "yks_remove_subscriber", // our data will final be passed through this function
							   user_email: user_email, // the user email to remove (created above)
							   list_id: list_id // the list id to remove the subscriber from (created above)
							},
							dataType: "html", // again must pass as HTML and not JSON
							success: function(MAILCHIMP)
								  {	
									jQuery(parent_element).fadeOut('fast',function() { jQuery(this).remove(); });
									jQuery("#TB_ajaxContent").find("#yikes-mailchimp-subscribers-table").before("<div class='yks-status' id='yks-status' style='display: block;margin-bottom:16px;'><div class='updated'><p>"+user_email+"<?php _e('was successfully unsubscribed from this list.','yikes-inc-easy-mailchimp-extender'); ?></p></div></div>");	
								
									setTimeout(function() {
										jQuery("#TB_ajaxContent").find('.updated').fadeOut('fast', function() {	
											jQuery(this).remove();
										});	
									}, 4500);
									
									// create subscriber variables
									var current_subscribers = jQuery("body").find('.number-of-subscribers-'+list_id).text();
									var header_current_subscribers_count = jQuery("#TB_window").find('.yks_mc_subscribers').find('.subscriber-count').find('.number').text();
									// update subscriber count variables
									jQuery("#TB_window").find('.yks_mc_subscribers').find('.subscriber-count').find('.number').text(header_current_subscribers_count - 1);
									jQuery("body").find('.number-of-subscribers-'+list_id).text(header_current_subscribers_count - 1+'  ');
									
								},
							error: function(xhr) {
								var response = xhr.responseText;
								console.log(response);
								var statusMessage = xhr.status + ' ' + xhr.statusText;
								var message  = 'Query failed, php script returned this status: ';
								var message = message + statusMessage + ' response: ' + response;
								console.log(message);
								jQuery('#TB_ajaxContent').find('.yks_mc_subscribers').html(message);
							}
					});
				}	
		});
		
		// hack to remove thickbox content
		// run every half second to decide if the thickbox is visible or not
		// if it is, do nothing. If it isnt, remove all content and prepare it for the next set of data.
		setInterval(function() {
			var thickBox = jQuery('#TB_window');
			if ( thickBox.is(':visible')) {
				return;
			} else {
				jQuery(".yks_mc_subscribers").html('');
				jQuery(".mailChimp_get_subscribers_preloader").fadeIn('fast');
			}
		}, 150);	 
		
		// if the redirect value is checked, slide down and reveal the pages drop down, else hide it
		jQuery('#yks-list-wrapper').delegate('.yks_mailchimp_redirect', 'click', function() {
			if (jQuery(this).attr("checked")) {
				jQuery(this).parents('.yks-mailchimp-redirect-checkbox-holder').find('#pages').slideDown();
			} else {
				jQuery(this).parents('.yks-mailchimp-redirect-checkbox-holder').find('#pages').slideUp();
			}
		});
		
		// if the custom styles box is checked, slide down and reveal the custom styles options, else hide it
		jQuery('#yks-list-wrapper').delegate('.yks_mailchimp_custom_styles', 'click', function() {
			if (jQuery(this).attr("checked")) {
				jQuery(this).parents('.form-table').find('input.yks_mailchimp_custom_template').removeAttr( 'checked' ).attr( 'disabled','disabled' );
				jQuery(this).parents('.yks-mailchimp-custom-styles-holder').find('#custom-style-list').slideDown();
			} else {
				jQuery(this).parents('.yks-mailchimp-custom-styles-holder').find('#custom-style-list').slideUp();
				jQuery(this).parents('.form-table').find('input.yks_mailchimp_custom_template').removeAttr( 'disabled','disabled' );
			}
		});
		
		// if the custom template box is checked, slide down and reveal the custom template options, else hide it
		jQuery('#yks-list-wrapper').delegate('.yks_mailchimp_custom_template', 'click', function() {
			if (jQuery(this).attr("checked")) {
				jQuery(this).parents('.form-table').find('input.yks_mailchimp_custom_styles').removeAttr( 'checked' ).attr( 'disabled','disabled' );
				jQuery(this).parents('.yks-mailchimp-custom-template-holder').find('#custom-template-list').slideDown();
			} else {
				jQuery(this).parents('.yks-mailchimp-custom-template-holder').find('#custom-template-list').slideUp();
				jQuery(this).parents('.form-table').find('input.yks_mailchimp_custom_styles').removeAttr( 'disabled','disabled' );
			}
		});
		
		jQuery('.yks_mc_subscribers').delegate('.yks-view-subscriber-profile', 'click', function() {
			// create our variables that will be passed over to MailChimp
			var user_email = jQuery(this).attr('rel');
			var list_id = jQuery(this).parents('.yks_mc_subscribers').find('.mailChimp_list_id').attr('value');
				$.ajax({
						   type:   "POST",
						   url:    ajaxurl,
						   data: {
							   action: "yks_mailchimp_form", // pass data to this ajax function
							   form_action: "yks_get_subscriberInfo", // our data will finall be passed through this function
							   user_email: user_email, // the user email to remove (created above)
							   list_id: list_id // the list id to remove the subscriber from (created above)
							},
							dataType: "html", // again must pass as HTML and not JSON
							success: function(MAILCHIMP)
								  {	
									// hide our subscriber data table
									jQuery('#yikes-mailchimp-subscribers-table_wrapper').fadeOut('fast');
									// slide list to the left, to display the Users information
									// alert(MAILCHIMP);
									$('.yks_mc_subscribers').find('h2').fadeOut('fast');
									$('.yks_mc_subscribers').find('p').fadeOut('fast');
									$('#yikes-mailchimp-subscribers-table').fadeOut('fast', function() {
										$('#individual_subscriber_information').html(MAILCHIMP).fadeIn();
									});
									
								},
							error: function(xhr) {
								var response = xhr.responseText;
								console.log(response);
								var statusMessage = xhr.status + ' ' + xhr.statusText;
								var message  = 'Query failed, php script returned this status: ';
								var message = message + statusMessage + ' response: ' + response;
								console.log(message);
								jQuery('#TB_ajaxContent').find('.yks_mc_subscribers').html(message);
							}
					});	
		});
		
		// hide user profile, display the table again
		jQuery('.yks_mc_subscribers').delegate('.yks-mc-subscriber-go-back', 'click', function() {
			$('#individual_subscriber_information').fadeOut('fast', function() {
				// show our subscriber data table again
				jQuery('#yikes-mailchimp-subscribers-table_wrapper').fadeIn('fast');
				$('.yks_mc_subscribers').find('h2').fadeIn();
				$('.yks_mc_subscribers').find('p').fadeIn();
				$('#yikes-mailchimp-subscribers-table').fadeIn();
			});		
		});
		
		// remove the array option that is stored in the drop down list
		// this array is what holds the subscriber-count value
		// we don't want it included in the list selection drop down
		jQuery('#yks-list-select').find('option[value="subscriber-count"]').remove();

		jQuery('.populatePreviewFormContainer').click(function() {
		
			if ( jQuery(this).hasClass( 'custom_template' ) ) {
			
				var selected_form = jQuery(this).parents( '#custom-template-list' ).find( '.template-selection-dropdown' ).val();
				var selected_form = selected_form.substring(selected_form.lastIndexOf("/"));				
				var selected_form_screenshot = selected_form.replace( '/' , '' ).replace( '.php' , '.jpg');					
				var selected_form_text = jQuery(this).parents( '#custom-template-list' ).find( '.template-selection-dropdown option:selected' ).text();	
				var template_path = jQuery(this).parents( '#custom-template-list' ).find( '.template-selection-dropdown' ).val();
				
					$.ajax({
						type:   "POST",
						url:    ajaxurl,
						data: {
							action: "yks_mailchimp_form", // pass data to this ajax function
							form_action: "yks_mc_get_custom_template_preview", // our data will finall be passed through this function
							template_name : selected_form_text,
							selected_form_screenshot: selected_form_screenshot,
							template_path: template_path
						},
						dataType: "html", // again must pass as HTML and not JSON
						success: function(MAILCHIMP) {	
							jQuery('#TB_window').find('#TB_ajaxContent').html(MAILCHIMP);
						},
						error: function(xhr) {
							console.log('error : '+MAILCHIMP);
						}
					});
			
			} else {
		
					var form_shortcode = jQuery(this).parents('.form-table').find('.yks-mailchimp-shortcode').find('.shortcode-code').text();
					var form_title = jQuery(this).parents('.panel-body').prev().find('.yks_mc_list_title').text();
					// get our temp colors to pass to the modal
					var form_bg_color = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-background-color"]').val();
					var form_text_color = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-text-color"]').val();
					var form_submit_button_color = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-submit-button-color"]').val();
					var form_submit_button_text_color = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-submit-button-text-color"]').val();
					var form_padding = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-form-padding"]').val();
					var form_width = jQuery(this).parents('#custom-style-list').find('input[name="yks-mc-form-width"]').val();
					var form_alignment = jQuery(this).parents('#custom-style-list').find('select[name="yks-mc-form-alignment"]').val();
					
						$.ajax({
							   type:   "POST",
							   url:    ajaxurl,
							   data: {
								   action: "yks_mailchimp_form", // pass data to this ajax function
								   form_action: "yks_mc_get_form_preview", // our data will finall be passed through this function
								   shortcode: form_shortcode,
								   form_title: form_title,
								   form_bg_color : form_bg_color,
								   form_text_color : form_text_color,
								   form_submit_button_color : form_submit_button_color,
								   form_submit_button_text_color : form_submit_button_text_color,
								   form_padding : form_padding,
								   form_width : form_width,
								   form_alignment : form_alignment
								},
								dataType: "html", // again must pass as HTML and not JSON
								success: function(MAILCHIMP)
								{	
									jQuery('#TB_window').find('#TB_ajaxContent').html(MAILCHIMP);
								},
								error: function(xhr) {
									console.log('error : '+MAILCHIMP);
								}
						});

			}
			
		});
		
		// import the boilerplate files for the user!
		jQuery('.import_template_boilerplates').click(function() {
			$("<div id='yks_mc_reset_plugin_settings'><div style='float:left;margin-left:20px;margin-right:40px;margin-top:.25em;font-size:3em;' class='dashicons dashicons-portfolio'></div><p><?php _e("Are you sure you want to import the custom template boilerplate files? This will create a directory in your theme root with the necessary files to create your own custom MailChimp signup templates.","yikes-inc-easy-mailchimp-extender"); ?></p></div>").dialog({
					title : "Import Boilerplate Template Files?",
					buttons : {
						"Yes" : function() {
						
							$.ajax({
								   type:   "POST",
								   url:    ajaxurl,
								   data: {
									   action: "yks_mailchimp_form", // pass data to this ajax function
									   form_action: "copy_user_templates_to_theme" // our data will finall be passed through this function
									},
									dataType: "json", // again must pass as HTML and not JSON
									success: function(MAILCHIMP)
									{	
										$("<div id='yks_mc_reset_plugin_settings'><div style='float:left;margin-left:20px;margin-right:40px;margin-top:.25em;font-size:3em;' class='dashicons dashicons-portfolio'></div><p><?php _e("Custom template boilerplate files succesfully imported. You can find them inside your theme folder, inside the 'yikes-mailchimp-user-template' directory.","yikes-inc-easy-mailchimp-extender"); ?></p><p><img style='display:block;margin:0 auto;' class='yks-mc-reset-plugin-settings-preloader' src='<?php echo plugin_dir_url(__FILE__)."../images/preloader.gif"; ?>'</p></div>").dialog({
											modal: true,
											draggable: false,
											resizable: false,
											width: 400
										});
										setTimeout(function() {
											location.reload();
										}, 2400 );
									},
									error: function(xhr) {
										alert('Error Importing Boilerplate Files.');
										console.log(xhr);
									}
							});
							
							// run our ajax to import the boilerplate files
							$(this).dialog("close");
						},
						"Cancel" : function() {
							$(this).dialog("close");
						}
					},
					modal: true,
					draggable: false,
					resizable: false,
					width: 400
			});
		});
		
		// clear the update interest group modal div when invisible
		setInterval(function() {
			if ( jQuery( '#updateInterestGroupContianer' ).is(':visible') ) {
				return;
			} else {
				jQuery( '.option-ul-title' ).not( '.first' ).remove();
				jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.add-another-interest-group-option' ).hide();
				jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.option-ul-title' ).hide();
				jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.no-interest-group-options-found' ).hide();
			}
		} , 150 );
		
		// clear the update interest group div when hidden
		setInterval(function() {
			if ( jQuery( '.yks_mc_preview_form_container' ).is(':visible') ) {
				return;
			} else {
				jQuery( '.yks_mc_preview_form_container' ).html('<?php echo '<img src="' . admin_url() . '/images/wpspin_light.gif" alt="preloader" style="margin-left: 50%; margin-top: 25%;">'; ?>');
				jQuery( '.yks_mc_preview_form_container' ).prev().remove();
			}
		} , 150 );
		
		
		jQuery('.template-selection-dropdown').change(function() {
			var selected_dropdown = jQuery(this);
			var label = selected_dropdown.find('option:selected').parent().attr('label');
			if ( label == 'Custom Templates' ) {
				selected_dropdown.parents( '#custom-template-list' ).find( '.populatePreviewFormContainer' ).attr( 'disabled' , 'disabled' ).removeClass( 'thickbox' );
			} else {
				selected_dropdown.parents( '#custom-template-list' ).find( '.populatePreviewFormContainer' ).removeAttr( 'disabled' ).addClass( 'thickbox' );
			}
		});
		
		jQuery( '.template-selection-dropdown' ).each( function() {
			var label = jQuery(this).find('option:selected').parent().attr('label');
			if ( label == 'Custom Templates' ) {
				jQuery(this).parents( '#custom-template-list' ).find( '.populatePreviewFormContainer' ).attr( 'disabled' , 'disabled' ).removeClass( 'thickbox' );
			}
		});
		
		// clear the selected_template_preview_container modal when invisible
		setInterval(function() {
			if ( jQuery( '#selected_template_preview_container' ).is(':visible') ) {
				return;
			} else {
				jQuery( '#selected_template_preview_container' ).html('<?php echo '<img src="' . admin_url() . '/images/wpspin_light.gif" alt="preloader" style="margin-left: 50%; margin-top: 25%;">'; ?>');
			}
		} , 150 );
		
		
		
	/* 
		Populate the Default Value field with
		a selected pre-defined tag	
	*/
	jQuery( 'body' ).on( 'click' , '.pre-defined-post-tag' , function() {
		var clicked_tag = jQuery(this).text();
		existing_field_value = jQuery( this ).parents('form').find( '#add-field-default-value' ).val();
		if ( existing_field_value == '' ) {
			jQuery( this ).parents('form').find( '#add-field-default-value' ).val( clicked_tag );
		} else {
			jQuery( this ).parents('form').find( '#add-field-default-value' ).val( existing_field_value+' '+clicked_tag );
		}
	});
	
	/* 
		Add A New Field
		@since v5.2
	*/
	// pass our mailchimp list id into the hidden input
	// and populate the merge var name with a random string
	jQuery( '.add-new-field-thickbox-open' ).click(function() {
		var mailchimp_list_id = jQuery(this).attr('alt');
		setTimeout(function() {
			jQuery( '#TB_ajaxContent' ).find( '#mc-list-id' ).val( mailchimp_list_id );
			// ajax generate new merge tag variable and populate the input!
			$.ajax({
				type:   'POST',
				url:    ajaxurl,
				data: {
					action: 'yks_mailchimp_form',
					form_action: 'generate_random_merge_var_name'
				},
				dataType: 'html',
				success: function(merge_tag_name) {		
				    jQuery( '#TB_window' ).find( '#add-field-field-merge-tag' ).val( merge_tag_name );
				}, 
				error: function(error_response) {
					console.log( 'There was an error generating the new merge variable name. If this error persists, please open a new support thread.' );
					console.log( error_response );
				}
			});
		}, 150);
	});
	
	// pass our mailchimp list id into the hidden input
	// and populate the merge var name with a random string
	jQuery( '.add-new-group-thickbox-open' ).click(function() {
		var mailchimp_list_id = jQuery(this).attr('alt');
		setTimeout(function() {
			jQuery( '#TB_ajaxContent' ).find( '#mc-list-id' ).val( mailchimp_list_id );
		}, 150);
	});
	
	/* Toggle Visibilty State when Required state is changed */
	jQuery( 'body.mailchimp-forms_page_yks-mailchimp-form-lists' ).on( 'click' , 'input[name="add-field-field-required"]' , function() {
		if( jQuery( this ).val() == 'true' ) {
			jQuery( '#merge-variable-settings' ).find( '.add-field-public-yes' ).prop( 'checked' , true );
			 jQuery( '#merge-variable-settings' ).find( 'input[name="add-field-public"]' ).each(function() {
				jQuery( this ).attr( 'disabled' , 'disabled' );
			 });
		} else {
			jQuery( '#merge-variable-settings' ).find( 'input[name="add-field-public"]' ).each(function() {
				jQuery( this ).removeAttr( 'disabled' );
			 });
		}
	});
	
	// animate the box on add-new-field click
	jQuery( 'body.mailchimp-forms_page_yks-mailchimp-form-lists' ).on( 'click' , '.add-new-field' , function() {
		var clicked_button = jQuery(this).attr('alt');
		var required_setting = jQuery( '#merge-variable-settings' ).find( 'input[name="add-field-field-required"]:checked' ).val();
		
		/* On initial open, we want to disable visibility setting
			because it is always set to 'true' on initial load 
		*/
		jQuery( '#merge-variable-settings' ).find( 'input[name="add-field-public"]' ).each(function() {
				jQuery( this ).attr( 'disabled' , 'disabled' );
			 });
		
		
		/*
		if ( required_setting == true ) {
			 jQuery( '#merge-variable-settings' ).find( '.add-field-public-yes' ).prop( 'checked' , true );
			 jQuery( '#merge-variable-settings' ).find( 'input[name="add-field-public"]' ).each(function() {
				jQuery( this ).attr( 'disabled' );
			});
		}
		*/
		
		jQuery( '.add-new-field' ).removeClass( 'create-this-field' );
		jQuery(this).addClass( 'create-this-field' );
		jQuery( '#TB_window' ).animate({
			marginTop: '-18em'
		});
		jQuery( '#TB_ajaxContent' ).animate({
			height: '550px' 
		});
		
		jQuery( '#merge-variable-settings' ).slideDown();
	
		// if date is clicked, show the date format fields
		if ( clicked_button == 'date' ) {
			jQuery( '.yks-mc-add-field-dateformat' ).show();
			jQuery( '#add-field-dateformat' ).show();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '#add-field-dateformat > .option-date' ).show();
			jQuery( '#add-field-dateformat > .option-date[value="MM/DD/YYYY"]' ).prop( 'selected' , 'selected' );
			jQuery( '#add-field-dateformat > .option-birthday' ).hide();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).hide();
			jQuery( '.default-value-text-field' ).hide();
		} else // if date is clicked, show the date format fields
		if ( clicked_button == 'birthday' ) {
			jQuery( '.yks-mc-add-field-dateformat' ).show();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '#add-field-dateformat' ).show();
			jQuery( '#add-field-dateformat > .option-birthday' ).show();
			jQuery( '#add-field-dateformat > .option-birthday[value="MM/DD"]' ).prop( 'selected' , 'selected' );
			jQuery( '#add-field-dateformat > .option-date' ).hide();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).hide();
			jQuery( '.default-value-text-field' ).hide();
		}  else // if date is clicked, show the date format fields
		if ( clicked_button == 'radio' || clicked_button == 'dropdown' ) {
			jQuery( '.yks-mc-add-field-radio-dropdown' ).show();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '#add-field-dateformat' ).hide();
			jQuery( '#add-field-dateformat > .option-birthday' ).hide();
			jQuery( '#add-field-dateformat > .option-date' ).hide();
			jQuery( '.default-value-text-field' ).hide();
		} else // if phone is clicked, show the phone format fields
		if ( clicked_button == 'phone' ) {
			jQuery( '.yks-mc-add-field-phoneformat' ).show();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).hide();
			jQuery( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '.default-value-text-field' ).hide();
		} else
		if ( clicked_button == 'text' ) {
			jQuery( '.default-value-text-field' ).show();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).hide();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '#add-field-dateformat' ).hide();
		} else {
			jQuery( '.default-value-text-field' ).hide();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).hide();
		}
		
	});
	
	/* reset form on thickbox close */
	setInterval(function() {
		if ( jQuery('#TB_window' ).is( ':visible' ) ) {
			// do nothing
			return;
		} else {
			// reset the form
			jQuery( '#merge-variable-settings' ).hide();
			jQuery( '#add-field-field-name' ).val( '' );
			jQuery( '#add-field-field-merge-tag' ).val( '' );
			jQuery( '#add-field-default-value' ).val( '' );
			
			// prevents the erorr from being stripped
			// in jquery ui content box
			if( jQuery( '#merge-variable-update' ).is( ':visible' ) ) {
				return;
			} else {
				jQuery('#TB_window' ).find( '.yks-mc-error' ).remove();
			}
			jQuery( '.add-new-field' ).removeClass( 'create-this-field' );
			jQuery( '.add-field-field-required-yes' ).attr( 'checked' , 'checked' );
			jQuery( '.add-field-public-yes' ).attr( 'checked' , 'checked' );
			var number_of_dropdown_radio_options = jQuery( '.radio-dropdown-option' ).length;
			if ( number_of_dropdown_radio_options > 1 ) {
				jQuery('#TB_window' ).find( '.radio-dropdown-option' ).each(function() {
					jQuery(this).val('');
					if ( jQuery(this).hasClass('first') ) {
						return;
					} else {
						jQuery(this).remove();
					}
					
				});
			}
			jQuery( '#updateMergeVariableContainer' ).find( '.dashicons-no-alt' ).remove();
			
		}
		
		// reset the update merge variable container
		if ( jQuery( '#updateMergeVariableContainer' ).is( ':visible' ) ) {
			return;
		} else {
			jQuery( '#updateMergeVariableContainer' ).find( '.yks-mc-add-field-radio-dropdown' ).hide();
			jQuery( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '#updateMergeVariableContainer' ).find( '.yks-mc-add-field-dateformat' ).hide();
			jQuery( '.yks-mc-add-field-phoneformat' ).hide();
			jQuery( '#add-field-dateformat > .option-date[value="MM/DD/YYYY"]' ).prop( 'selected' , 'selected' );
			jQuery( '#add-field-dateformat > .option-birthday' ).hide();
		} 
		
	}, 150);
	
	
	/* 
		reset interest group form on close close
		@since v5.2	
	*/
	setInterval(function() {
		if ( jQuery( '#interest-group-settings' ).is( ':visible' ) || jQuery( '#update-interest-group-settings' ).is( ':visible' ) ) {
			return;
		} else {
			// reset the add new interest group form, and fix other changed attributes
			jQuery( '#yks-mailchimp-add-new-interest-group-form' ).find( '.radio-dropdown-option' ).each( function() {
				if ( jQuery(this).hasClass( 'first' ) ) {
					return;
				} else {
					jQuery(this).remove();
				}
			});
			jQuery( '#yks-mailchimp-add-new-interest-group-form' ).find( '.remove-radio-dropdown-option' ).remove();
			jQuery( '#yks-mailchimp-add-new-interest-group-form' ).trigger( 'reset' ).css( 'opacity' , '1' );
			jQuery( '#yks-mailchimp-add-new-interest-group-form' ).find( '.yks-mc-preloader' ).remove();
			jQuery( '#yks-mailchimp-add-new-interest-group-form' ).find( '#submit' ).removeAttr( 'disabled' );
			// reset the update interest group form as well.
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.radio-dropdown-option' ).each( function() {
				if ( jQuery(this).hasClass( 'first' ) ) {
					return;
				} else {
					jQuery(this).remove();
				}
			});
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.remove-radio-dropdown-option' ).remove();
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '#mc-list-id' ).val('');
			jQuery( '#yks-mailchimp-update-interest-group-form' ).trigger( 'reset' ).css( 'opacity' , '0.5' );
			jQuery( '#updateInterestGroupContianer' ).find( '.yks-mc-preloader-update-interest-groups' ).show();
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '#submit' ).removeAttr( 'disabled' );
		}
	} , 150);
	
	
	// add the new field!
	// @since v5.2
	jQuery('#yks-mailchimp-add-new-field-form').submit(function (e) {
		
		var mailchimp_list_id = jQuery( '#yks-mailchimp-add-new-field-form' ).find( '#mc-list-id' ).val();
		jQuery( '#yks-mailchimp-add-new-field-form' ).find( 'input[type="submit"]' ).attr( 'disabled' , 'disabled' );
		jQuery( '#yks-mailchimp-add-new-field-form' ).fadeTo( 'fast' , 0.5 );
		jQuery( '.yks-mc-error' ).remove();
		// append a preloader to the modal, for some feedback
		jQuery( '#yks-mailchimp-add-new-field-form' ).find( 'input[type="submit"]').after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="margin-left: .5em;">' );
		
		/* ajax create our new field! */
		jQuery.ajax({
			type:   "POST",
			url:    ajaxurl,
				data: {
					action: "yks_mailchimp_form", // pass data to this ajax function
					form_action: "add_new_field_to_list", // our data will finall be passed through this function
					form_data: $('#yks-mailchimp-add-new-field-form').serialize(),
					field_type: $( '.create-this-field' ).attr('alt')
				},
				dataType: "json", // again must pass as HTML and not JSON
				success: function(MAILCHIMP) {	
					// ajax re-populate form data input field
					$.ajax({
                        type:   'POST',
                        url:    ajaxurl,
                        data: {
							action: 'yks_mailchimp_form',
                            form_action: 'merge_variables_reImport',
                            id: mailchimp_list_id
                        },
                        dataType: 'json',
                        success: function(MAILCHIMP) {
							$.ajax({
								type:   'POST',
								url:    ajaxurl,
								data: {
									action: 'yks_mailchimp_form',
									form_action: 'get_list_data',
								},
								dataType: 'html',
								success: function(new_list_data) {
									jQuery( '#yks-mailchimp-add-new-field-form' ).fadeTo( 'fast' , 1 );
									jQuery( '.yks-mc-preloader' ).remove();
									jQuery( '#yks-mailchimp-add-new-field-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
									window.parent.tb_remove();
									// replace with the new field
									setTimeout(function() {
										$($('#yks-mailchimp-fields-td_'+mailchimp_list_id)).replaceWith(MAILCHIMP); 
										jQuery( '#yks-mailchimp-fields-list_'+mailchimp_list_id ).find( '.yks-mailchimp-fields-list-row' ).last().addClass( 'fadeInLeft animated' );
									}, 450);										
									jQuery( '#merge-field-data' ).val( new_list_data );
									// update the list data at the top of the form
									console.log(new_list_data);				              
								}
							});             
                        }
					});
				},
				error: function(xhr) {
					jQuery( '#yks-mailchimp-add-new-field-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
					jQuery( '#yks-mailchimp-add-new-field-form' ).fadeTo( 'fast' , 1 );
					jQuery( '.yks-mc-preloader' ).remove();
					jQuery( '#TB_ajaxContent' ).find( '.setup-the-field-title' ).before( '<span class="yks-mc-error" style="display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;"> Error : '+xhr.responseText+'</span>' );
					// append an error message above "Add a Field"
					console.log(xhr.responseText);
				}
		});
		return false;
	});
	
	
	/*
		Delete a merge variable	
		@since v5.2
	*/
	jQuery( 'body.mailchimp-forms_page_yks-mailchimp-form-lists' ).on( 'click' , '.yks-mc-delete-merge-var' , function() {
		
		var mailchimp_list_id = jQuery(this).parents('form').find('.yks-mailchimp-import').attr('rel');
		var merge_tag = jQuery(this).parents( '.yks-mailchimp-fields-list-row' ).attr( 'alt' );
		var clicked_merge_tag = jQuery(this).parents('.yks-mailchimp-fields-list-row').find('.yks-mailchimp-field-name').text();
				
		var dialogDiv = $('#dialogDiv');
		if (dialogDiv.length == 0) {
			dialogDiv = $("<div id='dialogDiv'><div/>").appendTo('body');
			$('<div><input id="mailchimp-list-id" type="hidden" value="'+mailchimp_list_id+'"><input id="mailchimp-merge-tag" type="hidden" value="'+merge_tag+'"><p class="delete-merge-var-message"><?php _e( "Are you sure you want to delete " , "yikes-inc-easy-mailchimp-extender" ); ?>"<strong>'+clicked_merge_tag+'</strong>"<?php _e( "  from this list?" , "yikes-inc-easy-mailchimp-extender" ); ?></p><span class="deleting-please-wait-message" style="display:none;width:100%;text-align:center;margin.5em 0;"><?php _e( "Deleting field, please hold..." , "yikes-inc-easy-mailchimp-extender" ); ?></span></div>').appendTo(dialogDiv).removeClass('hide')
			dialogDiv.attr("Title", "Delete Field?");
			dialogDiv.dialog({
				modal : true,
				draggable : false,
				resizable : false,
				buttons : [
					{
						text : "Yes",
						class : 'large',
						click : function() {  
							jQuery( '.deleting-please-wait-message' ).after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="display:block;margin:0 auto;margin-top:10px;">' );
							jQuery( '.delete-merge-var-message' ).hide();
							jQuery( '.deleting-please-wait-message' ).show();
							jQuery( '.ui-dialog-buttonpane' ).find( '.large' ).hide();
							/* ajax delete our new field! */
							jQuery.ajax({
								type:   "POST",
								url:    ajaxurl,
									data: {
										action: "yks_mailchimp_form", // pass data to this ajax function
										form_action: "delete_new_list_field", // our data will finally be passed through this function
										mailchimp_list_id : mailchimp_list_id,
										merge_tag : merge_tag
									},
									dataType: "json", // again must pass as HTML and not JSON
									success: function(MAILCHIMP) {
										// ajax re-import fields
										$.ajax({
											type: 'POST',
											url: ajaxurl,
											data: {
												action: 'yks_mailchimp_form',
												form_action: 'merge_variables_reImport',
												id: mailchimp_list_id
											},
											dataType: 'json',
											success: function(MAILCHIMP) {	
												jQuery( '.delete-merge-var-message' ).show();
												jQuery( '.deleting-please-wait-message' ).hide();
												jQuery( '.ui-dialog-buttonpane' ).find( '.large' ).show();
												jQuery( '.yks-mc-preloader' ).remove();
												$( '.ui-dialog-content:visible' ).dialog('close');
												// remove the dialog div, to prevent errors on next deletion
												dialogDiv.remove();
												// replace with the new field
												$($('#yks-mailchimp-fields-td_'+mailchimp_list_id)).replaceWith(MAILCHIMP);                  
											}
										});
									},
									error: function(xhr) {
										// append the error message to the modal dialog
										jQuery( '.delete-merge-var-message' ).text( xhr.responseText );
										jQuery( '.deleting-please-wait-message' ).hide();
										jQuery( '.delete-merge-var-message' ).show();
										jQuery( '#dialogDiv' ).find( '.yks-mc-preloader' ).hide();
									}
							});
						}
					},
					{
						text : "Cancel",
						class : 'large',
						click : function() {
							$(this).dialog('close');
							$( '#dialogDiv' ).remove();
						}
					} ]
			});
		}else{
			dialogDiv.dialog("open");
		}
	});
	
	
	/*
		Update a merge variable	
		@since v5.2
	*/
	jQuery( 'body.mailchimp-forms_page_yks-mailchimp-form-lists' ).on( 'click' , '.yks-mc-merge-var-change' , function() {
		
		// ajax get specific field data, to populate jQuery UI form with!
		jQuery( '#yks-mailchimp-update-existing-field-form' ).fadeTo( 'fast' , 1 );
		var list_data = $.parseJSON( jQuery( '#merge-field-data' ).val() );		
		var mailchimp_list_id = jQuery(this).parents('form').find('.yks-mailchimp-import').attr('rel');
		var merge_tag = jQuery(this).parents( '.yks-mailchimp-fields-list-row' ).attr( 'alt' ).toLowerCase();
		
		// hide any previously established error messages
		jQuery( '.yks-mc-update-error' ).remove();
		
		// test returned field data
		// console.log(list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag]);
		var merge_name = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].label;
		var merge_required = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].require;
		var field_type = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].type;
		
		// if the field type is 'email',
		// we should disable the "Field Name" field , as this can't be altered.
		// this needs to be changed via the 'yikes_mc_field_label' filter (see readme.txt or yks-mc-frontend-form-display.php ~line 148)
		if( field_type == 'email' ) {
			
			jQuery( '#updateMergeVariableContainer' ).find( 'label[for="add-field-field-name"]' ).parents( '.form-table' ).before( "<span class='yks-mc-update-error email-mv-error' style='display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;'>You cannot update the EMAIL merge variable. If you'd like to change the field name/label, please use the yikes_mc_field_label filter. ( see readme.txt for example )</span>" );
			
			jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-name' ).attr( 'disabled' , 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-merge-tag' ).attr( 'disabled' , 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '.update-field-field-required-yes' ).attr( 'disabled' , 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '.update-field-field-required-no' ).attr( 'disabled' , 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( 'input[type="submit"]' ).attr( 'disabled' , 'disabled' );
		} else {
			jQuery( '#updateMergeVariableContainer' ).find( '.email-mv-error' ).remove();
			jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-name' ).removeAttr( 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-merge-tag' ).removeAttr( 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '.update-field-field-required-yes' ).removeAttr( 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( '.update-field-field-required-no' ).removeAttr( 'disabled' );
			jQuery( '#updateMergeVariableContainer' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
		}
				
		console.log("TYPE : " + list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].type );
		console.log(list_data);
		
			var dialogDiv = $('#updateMergeVariableContainer');
			dialogDiv.attr("Title", "Update "+merge_name+" Field");
			dialogDiv.dialog({
				width: "50%",
				option: [ 'maxHeight' , 600 ],
				modal : true,
				draggable : false,
				resizable : false,
			});
			
		// populate the form with the retreived data...
		jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-name' ).val( merge_name ); // field name
		jQuery( '#updateMergeVariableContainer' ).find( '#add-field-field-merge-tag' ).val( merge_tag.toUpperCase() ); // field merge var
		jQuery( '#updateMergeVariableContainer' ).find( '#mc-list-id' ).val( mailchimp_list_id );
		jQuery( '#updateMergeVariableContainer' ).find( '#old-merge-tag' ).val( merge_tag.toUpperCase() );
		jQuery( '#updateMergeVariableContainer' ).find( '#field-type-text' ).text( field_type.charAt(0).toUpperCase() + field_type.substring(1) );
		
		if ( merge_required == true ) { // field merge var
			jQuery( '.ui-dialog' ).find( '.update-field-field-required-yes' ).prop( 'checked' , 'checked' );
		} else {
			jQuery( '.ui-dialog' ).find( '.update-field-field-required-no' ).prop( 'checked' , 'checked' );
		}
		
		if ( field_type == 'text' ) {
			var default_value = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].default;
			jQuery( '#updateMergeVariableContainer' ).find( '.default-value-text-field' ).show();
			jQuery( '#updateMergeVariableContainer' ).find( 'input#add-field-default-value' ).val(default_value);
		} else {
			jQuery( '#updateMergeVariableContainer' ).find( '.default-value-text-field' ).hide();
			jQuery( '#updateMergeVariableContainer' ).find( '.default-value-text-field' ).val('');
		}
		
		// toggle visibility of fields based on field_type
		if ( field_type == 'dropdown' || field_type == 'radio' ) {
			
			// store the options + number of options
			var dropdown_option_choices = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].choices;
			var dropdown_option_choices_length = list_data[mailchimp_list_id].fields[mailchimp_list_id+'-'+merge_tag].choices.length;
			
			var x = 0;
			// loop to build the input fields
			while( x <= ( parseInt(dropdown_option_choices_length) - parseInt(1) ) ) {
				jQuery('.radio-dropdown-option').last().clone().insertAfter( jQuery('.radio-dropdown-option').last() );
				jQuery( '.radio-dropdown-option:nth-child('+( x + parseInt(1) ) +')').val( dropdown_option_choices[x] );
				jQuery('.radio-dropdown-option').last().removeClass('first');

				// populate the input fields with the pre-set 
				if ( x == ( parseInt(dropdown_option_choices_length) - parseInt(1) ) ) {	
					jQuery('.radio-dropdown-option').last().remove();
					// should be appending before each element, but only does it on the last one
					jQuery( '.radio-dropdown-option').not(".first").before('<span class="dashicons dashicons-no-alt remove-radio-dropdown-option"></span>');
				}
				x++;
			}
							
			jQuery( '#updateMergeVariableContainer' ).find( '.yks-mc-add-field-radio-dropdown' ).slideDown();
			// jQuery( '#updateMergeVariableContainer' ).find( '.yks-mc-add-field-dateformat' ).hide();
			
		} else if ( field_type == 'date' ) {
			jQuery( '.yks-mc-add-field-dateformat' ).slideDown();
			jQuery( '#add-field-dateformat > .option-date' ).show();
			jQuery( '#add-field-dateformat > .option-date[value="MM/DD/YYYY"]' ).prop( 'selected' , 'selected' );
			// jQuery( '#add-field-dateformat > .option-birthday' ).hide();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).slideUp();
		} else if ( field_type == 'birthday' ) {
			jQuery( '.yks-mc-add-field-dateformat' ).slideDown();
			jQuery( '#add-field-dateformat' ).show();
			jQuery( '#add-field-dateformat > .option-birthday' ).show();
			jQuery( '#add-field-dateformat > .option-birthday[value="MM/DD"]' ).prop( 'selected' , 'selected' );
			// jQuery( '#add-field-dateformat > .option-date' ).hide();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).slideUp();
		} else if ( field_type == 'phone' ) {
			jQuery( '.yks-mc-add-field-phoneformat' ).slideDown();
			jQuery( '.yks-mc-add-field-radio-dropdown' ).slideUp();
			jQuery( '.yks-mc-add-field-dateformat' ).slideUp();
		}

	});
	
	// Update the existing field on form submission!
	// @since v5.2
	jQuery('#yks-mailchimp-update-existing-field-form').submit(function (e) {
		
		var mailchimp_list_id = jQuery( '#yks-mailchimp-update-existing-field-form' ).find( '#mc-list-id' ).val();
		var field_type = jQuery( '#field-type-text' ).text().toLowerCase();
		
		jQuery( '#yks-mailchimp-update-existing-field-form' ).find( 'input[type="submit"]' ).attr( 'disabled' , 'disabled' );
		jQuery( '#yks-mailchimp-update-existing-field-form' ).fadeTo( 'fast' , 0.5 );
		jQuery( '.yks-mc-error' ).remove();
		// append a preloader to the modal, for some feedback
		jQuery( '#yks-mailchimp-update-existing-field-form' ).find( 'input[type="submit"]').after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="margin-left: .5em;">' );

		/* ajax update our existing field! */
		jQuery.ajax({
			type:   "POST",
			url:    ajaxurl,
				data: {
					action: "yks_mailchimp_form", // pass data to this ajax function
					form_action: "update_list_field", // our data will finall be passed through this function
					form_data: $('#yks-mailchimp-update-existing-field-form').serialize(),
					field_type: field_type
				},
				dataType: "json", // again must pass as HTML and not JSON
				success: function(MAILCHIMP) {	
					// ajax re-import fields
					$.ajax({
                        type:   'POST',
                        url:    ajaxurl,
                        data: {
							action: 'yks_mailchimp_form',
                            form_action: 'merge_variables_reImport',
                            id: mailchimp_list_id
                        },
                        dataType: 'json',
                        success: function(MAILCHIMP) {
							if( MAILCHIMP == '-1' ) {
								jQuery( '#yks-mailchimp-update-existing-field-form' ).fadeTo( 'fast' , 1 );
								jQuery( '.yks-mc-preloader' ).remove();
								jQuery( '#yks-mailchimp-update-existing-field-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
								jQuery( '#updateMergeVariableContainer' ).prepend( '<span class="yks-mc-error" style="display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;"> No field settings have changed.</span>' );
							} else {
									
									$.ajax({
										type:   'POST',
										url:    ajaxurl,
										data: {
											action: 'yks_mailchimp_form',
											form_action: 'get_list_data',
										},
										dataType: 'html',
										success: function(new_list_data) {
											jQuery( '#yks-mailchimp-add-new-field-form' ).fadeTo( 'fast' , 1 );
											jQuery( '.yks-mc-preloader' ).remove();
											jQuery( '#yks-mailchimp-update-existing-field-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
											jQuery( '.yks-mc-update-error' ).remove();
											$( '.ui-dialog-content:visible' ).dialog('close');
											// replace with the new field
											$($('#yks-mailchimp-fields-td_'+mailchimp_list_id)).replaceWith(MAILCHIMP); 
											jQuery( '#merge-field-data' ).val( new_list_data );
											// update the list data at the top of the form
											console.log(new_list_data);				              
										}
									});
									
							}
                        }
					});
				},
				error: function(xhr) {
					jQuery( '#yks-mailchimp-update-existing-field-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
					jQuery( '#yks-mailchimp-update-existing-field-form' ).fadeTo( 'fast' , 1 );
					jQuery( '.yks-mc-preloader' ).remove();
					jQuery( '.ui-dialog' ).find( 'table.form-table' ).before( '<span class="yks-mc-update-error" style="display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;"> Error : '+xhr.responseText+'</span>' );
					// append an error message above "Add a Field"
					console.log(xhr.responseText);
				}
		});
		return false;
	});
	
	/*
		Remove an option from a drop down or radio field
		@since 5.2
	*/
	jQuery( 'body' ).on ( 'click' , '.remove-radio-dropdown-option' , function() {
		jQuery(this).next().andSelf().wrapAll('<div class="fadeOutRight animated" />' );
		setTimeout(function() {
			jQuery('.fadeOutRight').remove();
		},800);
	});
	
	
	/** Interest Groups **/
	
	// add the new interest group to our form!
	// @since v5.2
	jQuery('#yks-mailchimp-add-new-interest-group-form').submit(function (e) {
		
		var mailchimp_list_id = jQuery( '#TB_window' ).find( '#yks-mailchimp-add-new-interest-group-form' ).find( '#mc-list-id' ).val();
		
		jQuery( '#TB_window' ).find( '#yks-mailchimp-add-new-interest-group-form' ).find( 'input[type="submit"]' ).attr( 'disabled' , 'disabled' );
		jQuery( '#TB_window' ).find( '#yks-mailchimp-add-new-interest-group-form' ).fadeTo( 'fast' , 0.5 );
		jQuery( '#TB_window' ).find( '.yks-mc-error' ).remove();
		// append a preloader to the modal, for some feedback
		jQuery( '#TB_window' ).find( '#yks-mailchimp-add-new-interest-group-form' ).find( 'input[type="submit"]').after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="margin-left: .5em;">' );
		
		/* ajax create our new field! */
		jQuery.ajax({
			type:   "POST",
			url:    ajaxurl,
				data: {
					action: "yks_mailchimp_form", // pass data to this ajax function
					form_action: "add_new_interest_group", // our data will finall be passed through this function
					form_data: jQuery( '#TB_window' ).find('#yks-mailchimp-add-new-interest-group-form').serialize()
				},
				dataType: "json", // again must pass as HTML and not JSON
				success: function(MAILCHIMP) {	
					// ajax re-populate form data input field
					// to do : 
						// update interest group data
						// re-populate interest gorup area
						// aaaaand close thickbox
					$.ajax({
                        type:   'POST',
                        url:    ajaxurl,
                        data: {
							action: 'yks_mailchimp_form',
                            form_action: 'get_interest_group_data',
                            mailchimp_list_id: mailchimp_list_id
                        },
                        dataType: 'html',
                        success: function( interest_group_data ) {
							window.parent.tb_remove();
							setTimeout(function() {
								jQuery( '#yks-mailchimp-interest-groups-container_'+mailchimp_list_id ).html( interest_group_data );
								jQuery( '#yks-mailchimp-interest-groups-container_'+mailchimp_list_id ).removeClass( 'yks-mc-empty-interest-group-table' );
								jQuery( '#yks-mailchimp-interest-groups-container_'+mailchimp_list_id ).find( '.yks-mailchimp-fields-list-row' ).last().addClass( 'fadeInLeft animated' );
							},450);	
                        }
					});
					
				},
				error: function(xhr) {
					jQuery( '#yks-mailchimp-add-new-interest-group-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
					jQuery( '#yks-mailchimp-add-new-interest-group-form' ).fadeTo( 'fast' , 1 );
					jQuery( '.yks-mc-preloader' ).remove();
					jQuery( '#TB_ajaxContent' ).find( 'h4.interest-group-field-title' ).after( '<span class="yks-mc-error" style="display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;"> Error : '+xhr.responseText+'</span>' );
					// append an error message above "Add a Field"
					console.log(xhr.responseText);
				}
		});
		return false;
	});
	
	
	/*
		Delete an interest group from our form
		@since 5.2
	*/
	jQuery( 'body' ).on( 'click' , '.yks-mc-interest-group-delete' , function() {
		
		var mc_list_id = jQuery(this).parents('.yks-mailchimp-interest-groups-container').attr('id').split("_")[1];
		var interest_group_id = jQuery(this).parents('.yks-mailchimp-fields-list-row').attr('alt');
		var interest_group_name = jQuery(this).parents('.yks-mailchimp-fields-list-row').find('.yks-mc-interest-group-name').text();
		
		var dialogDiv = $('#deleteGroupDiv');
		if (dialogDiv.length == 0) {
			dialogDiv = $("<div id='deleteGroupDiv'><div/>").appendTo('body');
			$('<div><input id="mailchimp-interest-group-id" type="hidden" value="'+interest_group_id+'"><p class="delete-interest-group-message"><?php _e( "Are you sure you want to delete " , "yikes-inc-easy-mailchimp-extender" ); ?>"<strong>'+interest_group_name+'</strong>"<?php _e( "  from this list?" , "yikes-inc-easy-mailchimp-extender" ); ?></p><span class="deleting-please-wait-message" style="display:none;width:100%;text-align:center;margin.5em 0;"><?php _e( "Deleting group, please hold..." , "yikes-inc-easy-mailchimp-extender" ); ?></span></div>').appendTo(dialogDiv).removeClass('hide')
			dialogDiv.attr("Title", "Delete Group?");
			dialogDiv.dialog({
				modal : true,
				draggable : false,
				resizable : false,
				buttons : [
					{
						text : "Yes",
						class : 'large',
						click : function() {
							jQuery( '.deleting-please-wait-message' ).after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="display:block;margin:0 auto;margin-top:10px;">' );
							jQuery( '.delete-interest-group-message' ).hide();
							jQuery( '.deleting-please-wait-message' ).show();
							jQuery( '.ui-dialog-buttonpane' ).find( '.large' ).hide();
							
							// to do :
							// run ajax function here, show deleting please hold dialog
							// on success do the follow :
							/* ajax delete our new field! */
							jQuery.ajax({
								type:   "POST",
								url:    ajaxurl,
									data: {
										action: "yks_mailchimp_form", // pass data to this ajax function
										form_action: "delete_interest_group_from_list", // our data will finally be passed through this function
										mailchimp_list_id : mc_list_id,
										interest_group_id : interest_group_id
									},
									dataType: "json", // again must pass as HTML and not JSON
									success: function(MAILCHIMP) {
										var item_count = parseInt( jQuery( '#yks-mailchimp-interest-groups-container_'+mc_list_id ).find( '.yks-mailchimp-fields-list-row' ).length ) - parseInt(1);
										// delete the interest group from the list
										dialogDiv.remove();
										jQuery( 'div[alt="'+interest_group_id+'"]' ).addClass( 'fadeOutRight animated' );
											setTimeout(function() {
												if ( item_count <= 0 ) {
													jQuery( '#yks-mailchimp-interest-groups-container_'+mc_list_id ).addClass( 'yks-mc-empty-interest-group-table' );
													jQuery( '#yks-mailchimp-interest-groups-container_'+mc_list_id ).html ('<span class="no-interest-groups-found"><em>No interest groups have been setup for this form yet</em></span>');
												}
												jQuery('.fadeOutRight').remove();
											},800);
									},
									error: function(xhr) {
										// append the error message to the modal dialog
										jQuery( '.delete-interest-group-message' ).text( 'Error : ' + xhr.responseText );
										console.log(xhr);
									}
							});
						}
					},
					{
						text : "Cancel",
						class : 'large',
						click : function() {
							$(this).dialog('close');
							$( '#dialogDiv' ).remove();
						}
					} ]
			});
		}else{
			dialogDiv.dialog("open");
		}
	
	});
	
	/*
		Edit an interest group in a specific form
		@since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.yks-mc-interest-group-edit' , function() {
	
		var group_name = jQuery( this ).parents( '.yks-mailchimp-fields-list-row' ).find( '.yks-mc-interest-group-name' ).text();
		var mc_list_id = jQuery( this ).parents( '.yks-mailchimp-interest-groups-container' ).attr( 'id' ).split( '_' )[1];
		var mc_interest_group_id = jQuery( this ).parents( '.yks-mailchimp-fields-list-row' ).attr( 'alt' );
		
		// pass along the value
		setTimeout(function() {
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '#mc-list-id' ).val( mc_list_id );
			jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '#grouping-id' ).val( mc_interest_group_id );
		}, 250);
				
		var dialogDiv = jQuery('#updateInterestGroupContianer');
			dialogDiv.attr("Title", "Update "+group_name+" Group");
			dialogDiv.dialog({
				width: "50%",
				option: [ 'maxHeight' , 600 ],
				modal : true,
				draggable : false,
				resizable : false,
			});
			
			
		// ajax function get info	
		jQuery.ajax({
			type:   "POST",
			url:    ajaxurl,
			data: {
				action: "yks_mailchimp_form", // pass data to this ajax function
				form_action: "get_specific_interest_group_data", // our data will finally be passed through this function
				mailchimp_list_id : mc_list_id,
				mc_interest_group_id : mc_interest_group_id
			},
			dataType: "json", // again must pass as HTML and not JSON
			success: function(interest_group_data) {
						
				// test returned interest group data
				console.log( interest_group_data );
				
				var option_count = interest_group_data.groups.length;
				var options = interest_group_data.groups;
				
				jQuery( '#updateInterestGroupContianer' ).find( '#yks-mc-interest-group-title' ).text( interest_group_data.name );
				// set the dropdown value
				jQuery( '#updateInterestGroupContianer' ).find( '#yks-mc-interest-group-type-dropdown' ).find( 'option[value="'+interest_group_data.form_field+'"]' ).attr( 'selected' , 'selected' );
				
				console.log( interest_group_data.form_field );
				
				if ( interest_group_data.form_field != 'hidden' && interest_group_data.form_field != 'checkboxes' ) {
					jQuery( '#yks-mc-interest-group-toggle-type' ).attr( 'disabled' , 'disabled' );
					jQuery( '#yks-mc-interest-group-toggle-type' ).removeAttr('title').attr( 'title' , 'Dropdown and radio buttons can not be converted' );
					jQuery( '#yks-mc-interest-group-toggle-type' ).append( 'option[value="radio"]' );
					jQuery( '#yks-mc-interest-group-toggle-type' ).append( 'option[value="dropdown"]' );
				} else {
					jQuery( '#yks-mc-interest-group-toggle-type' ).find( 'option[value="radio"]' ).remove();
					jQuery( '#yks-mc-interest-group-toggle-type' ).removeAttr('title').attr( 'title' , 'Only hidden and checkbox fields may be converted between one another.' );
					jQuery( '#yks-mc-interest-group-toggle-type' ).find( 'option[value="dropdown"]' ).remove();
					jQuery( '#yks-mc-interest-group-toggle-type' ).removeAttr( 'disabled' , 'disabled' );
				}
				
				var x = 0;
				// loop to build the input fields
				if ( option_count > 0 ) {
					jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.option-ul-title' ).show();
					jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.no-interest-group-options-found' ).hide();
				} else {
					jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.option-ul-title' ).not( '.first' ).remove();
					jQuery( '#yks-mailchimp-update-interest-group-form' ).find( '.no-interest-group-options-found' ).show();
				}
				
				while( x < option_count) {	
										
					jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last().clone().insertAfter( jQuery( '.option-ul-title' ).last() );
					jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title:nth-child('+( x + parseInt(1) ) +')').html( '<h4 style="float:left;" alt="'+options[x].id+'">' + options[x].name + '</h4>' );
					jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last().removeClass('first');
					
					if ( x == ( parseInt( option_count ) - parseInt( 1 ) ) ) {
						jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last().not('.first').remove();
					}
					
					x++;
				}
				
				jQuery( '#updateInterestGroupContianer' ).find( '.yks-mc-preloader-update-interest-groups' ).hide();
				jQuery( '#updateInterestGroupContianer' ).find( '#yks-mailchimp-update-interest-group-form' ).css( 'opacity' , '1' );
				// show our add-new interest group button
				jQuery( '.add-another-interest-group-option' ).show();
				
			},
			error: function(xhr) {
				// append the error message to the modal dialog
				jQuery( '.delete-interest-group-message' ).text( 'Error : ' + xhr.responseText );
				console.log(xhr);
			}
		});
							
	});
		
		
	/* On Option Hover Append Edit + Delete Buttons */
	jQuery( 'body' ).on( 'mouseenter' , 'li.option-ul-title' , function() {
		if ( jQuery( '.active-edit-input' ).is( ':visible' ) ) {
			return;
		} else {
			jQuery( this ).append( '<span class="edit-this-group" style="float:left;margin-top:6px;margin-left:.5em;"><span class="yks-mc-interest-group-option-delete" title="Delete Group Option"><span class="dashicons dashicons-no-alt"></span></span><span class="yks-mc-interest-group-option-edit" title="Edit Group Option"><span class="dashicons dashicons-edit"></span></span></span>' );
		}
	});
	jQuery( 'body' ).on( 'mouseleave' , 'li.option-ul-title' , function() {
		if ( jQuery( '.active-edit-input' ).is( ':visible' ) ) {
			return;
		} else {	
			jQuery( '.edit-this-group' ).remove();
		}
	});
	
	/* On Option Hover Append Edit + Delete Buttons */
	jQuery( 'body' ).on( 'mouseenter' , '.yks-mc-update-interest-group-header' , function() {
		if ( jQuery( '.active-edit-input' ).is( ':visible' ) ) {
			return;
		} else {	
			jQuery( this ).append( '<span class="edit-this-group-title" style="float:left;margin-top:18px;margin-left:.5em;"><span class="yks-mc-interest-group-title-edit" title="Edit Group Title"><span class="dashicons dashicons-edit"></span></span></span>' );
		}
	});
	
	jQuery( 'body' ).on( 'mouseleave' , '.yks-mc-update-interest-group-header' , function() {
		jQuery( '.edit-this-group-title' ).remove();
	});

	
	/*
		Edit the interest group title
		@since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.edit-this-group-title' , function() {
		var previous_value = jQuery( this ).parents( '.yks-mc-update-interest-group-header' ).find( 'h3' ).text();
		jQuery( this ).parents( '.yks-mc-update-interest-group-header' ).find( '#yks-mc-interest-group-title' ).replaceWith( '<input type="text" value="'+previous_value+'" alt="'+previous_value+'" class="active-edit-input-title" style="display:block;float:left;font-size:23px;" />' );
		jQuery( '.yks-mc-update-interest-group-header' ).removeClass( 'yks-mc-update-interest-group-header' ).attr( 'style' , 'display:inline-block !important;width:100%;position:relative;' );
		jQuery( '.active-edit-input-title' ).focus();
		/* hack to get cursor to the end of the text in the field */
		jQuery( '.active-edit-input-title' ).val('');
		jQuery( '.active-edit-input-title' ).val(previous_value);
		jQuery( '.edit-this-group-title' ).remove();
	});
	
	jQuery( 'body' ).on( 'blur' , '.active-edit-input-title' , function() {
		var grouping_id =  jQuery( '#updateInterestGroupContianer' ).find( '#grouping-id' ).val();
		var mailchimp_list_id = jQuery( '#updateInterestGroupContianer' ).find( '#mc-list-id' ).val();
		var previous_value = jQuery( this ).attr( 'alt' );
		var new_value = jQuery( this ).val();
		jQuery( '.active-edit-input-title' ).after( '<img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" alt="yks-mc-update-preloader" class="yks-mc-update-preloader" style="float:left; margin-left:10px;">' );
		// ajax update the title 
		// run ajax update interest group
			$.ajax({
				type:   'POST',
				url:    ajaxurl,
				data: {
					action: 'yks_mailchimp_form',
					form_action: 'update_interest_grouping_title',
					mailchimp_list_id: mailchimp_list_id,
					grouping_id: grouping_id,
					value: new_value
				},
				dataType: 'json',
				success: function( response ) {
					jQuery( '.yks-mc-interest-group-header' ).addClass( 'yks-mc-update-interest-group-header' );
					jQuery( '.yks-mc-update-preloader' ).remove();
					jQuery( '.active-edit-input-title' ).after('<h3 style="float:left;" id="yks-mc-interest-group-title">' + new_value + '<span class="dashicons dashicons-yes successfuly-update-interest-group" style="line-height:.8;color:rgba(104, 200, 86, 0.59);margin-left:10px;"></span></h3>').remove();
					setTimeout(function() {
						jQuery( '#updateInterestGroupContianer' ).find( '.successfuly-update-interest-group' ).fadeOut();
					}, 2000 );	
					// ajax re-import this lists interest group data, to update title on the manage list forms page
					$.ajax({
						type:   'POST',
						url:    ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'get_interest_group_data',
							mailchimp_list_id: mailchimp_list_id
						},
						dataType: 'html',
						success: function( response ) {
							// re-populate interest grouping table
							jQuery( '#yks-mailchimp-interest-groups-container_'+mailchimp_list_id ).html(response);
						},
						error: function( error ) {
							console.log( 'Error re-importing interest group data' );
						}	
					});
				},
				error: function( error ) {
					console.log( error );
				}	
			});
		
	});
	
	/* 
		Delete Group Option 
		@since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.yks-mc-interest-group-option-delete' , function() {
		
		// create+store variables to pass to MailChimp API Request
		var mc_list_id = jQuery( '#updateInterestGroupContianer' ).find( '#mc-list-id' ).val();
		var grouping_id = jQuery( '#updateInterestGroupContianer' ).find( '#grouping-id' ).val();
		var group_name = jQuery( this ).parents( '.option-ul-title' ).find( 'h4' ).text();
		var group_option = jQuery( this ).parents( '.option-ul-title' ).find( 'h4' ).attr('alt');
		var clicked_button = jQuery( this );
		
		var dialogDiv = $('#deleteGroupOptionDiv');
		if (dialogDiv.length == 0) {
			dialogDiv = $("<div id='deleteGroupOptionDiv'><div/>").appendTo('body');
			$('<div><p class="delete-interest-group-message"><?php _e( "Are you sure you want to delete the " , "yikes-inc-easy-mailchimp-extender" ); ?>"<strong>'+group_name+'</strong>"<?php _e( " option from this list?" , "yikes-inc-easy-mailchimp-extender" ); ?></p><span class="deleting-please-wait-message" style="display:none;width:100%;text-align:center;margin.5em 0;"><?php _e( "Deleting group, please hold..." , "yikes-inc-easy-mailchimp-extender" ); ?></span></div>').appendTo(dialogDiv).removeClass('hide')
			dialogDiv.attr("Title", "Delete Group Option?");
			dialogDiv.dialog({
				modal : true,
				draggable : false,
				resizable : false,
				buttons : [
					{
						text : "Yes",
						class : 'large',
						click : function() {
							jQuery( '.deleting-please-wait-message' ).after( '<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="yks-mc-preloader" class="yks-mc-preloader" style="display:block;margin:0 auto;margin-top:10px;">' );
							jQuery( '.delete-interest-group-message' ).hide();
							jQuery( '.deleting-please-wait-message' ).show();
							jQuery( '.ui-dialog-buttonpane' ).find( '.large' ).hide();

								jQuery.ajax({
									type:   'POST',
									url:    ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'delete_interest_group_option',
										mailchimp_list_id: mc_list_id,
										group_name: group_name,
										grouping_id: grouping_id
									},
									dataType: 'json',
									success: function( response ) {
										var option_count = jQuery( '#option-ul' ).find( '.option-ul-title' ).length;
										jQuery('#deleteGroupOptionDiv').dialog('close');
										jQuery( '#deleteGroupOptionDiv' ).remove();
										/*	 fade in the empty message if == 1 */
											if ( option_count <= 1 ) {
												
												jQuery( 'h4[alt="'+group_option+'"]' ).parents( '.option-ul-title' ).addClass( 'animated fadeOutRight' );
												setTimeout(function() {
													jQuery( '.no-interest-group-options-found' ).fadeIn();
													jQuery( '.fadeOutRight' ).remove();
												},700);
											} else {
												jQuery( 'h4[alt="'+group_option+'"]' ).parents( '.option-ul-title' ).addClass( 'animated fadeOutRight' );
												setTimeout(function() {
													jQuery( '.fadeOutRight' ).remove();
												},700);
											}
									},
									error: function( error ) {
										jQuery( '.yks-mc-update-preloader' ).remove();
										jQuery( '.option-ul-title' ).last().after( '<span class="yks-mc-error" style="display:block;width:100%;color:rgb(249, 141, 141);margin:.5em 0;">'+error.responseText+'</span>' );
									}	
								});
						}
					},
					{
						text : "Cancel",
						class : 'large',
						click : function() {
							jQuery('#deleteGroupOptionDiv').dialog('close');
							jQuery( '#deleteGroupOptionDiv' ).remove();
						}
					} ]
			});
		}else{
			dialogDiv.dialog("open");
		}
				// confirm with the user they want to remove the option group
				// ajax request deletion - need group id etc.
				
				
		
		
	});
	
	
	/* Edit Group Option */
	jQuery( 'body' ).on( 'click' , '.yks-mc-interest-group-option-edit' , function() {
		// store variables to pass to MailChimp API request
		var previous_value = jQuery( this ).parents( '.option-ul-title' ).find( 'h4' ).text();
		var option_id = jQuery( this ).parents( '.option-ul-title' ).find( 'h4' ).attr( 'alt' );
		jQuery( '.yks-mc-update-option-error' ).remove();
		jQuery( this ).parents( 'li.option-ul-title' ).find('h4').unwrap( 'h4' ).wrap( '<input class="active-edit-input" type="text" value="'+previous_value+'" alt="'+option_id+'" rel="'+previous_value+'" />' );
		jQuery( this ).parents( '.edit-this-group' ).remove();
		jQuery( '.active-edit-input' ).focus();
		/* hack to get cursor to the end of the text in the field */
		jQuery( '.active-edit-input' ).val('');
		jQuery( '.active-edit-input' ).val(previous_value);
	});
	
	/* Edit Group Unfocus */
	jQuery( 'body' ).on( 'blur' , '.active-edit-input' , function() {
		
		var mc_list_id = jQuery( '#updateInterestGroupContianer' ).find( '#mc-list-id' ).val();
		jQuery( '.yks-mc-update-option-error' ).remove();
		
		jQuery( '#updateInterestGroupContianer' ).find( '.yks-mc-error' ).remove();
		jQuery( '.active-edit-input' ).after( '<img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" alt="yks-mc-update-preloader" class="yks-mc-update-preloader" style="margin-left:10px;">' );
				
		if ( jQuery( this ).hasClass( 'interest-group-new-option' ) ) {
			
			var group_name = jQuery( '.interest-group-new-option' ).val();
			var grouping_id = jQuery( '#updateInterestGroupContianer' ).find( '#grouping-id' ).val();
						
			if( group_name == '' ) {
				
				// if the new name is empty,
				// lets just simply remove the inputs
				jQuery( '.yks-mc-update-preloader' ).remove();
				jQuery( '.active-edit-input' ).remove();
				return false;
				
			} else {
				
				// run ajax to add an interest group option
				$.ajax({
					type:   'POST',
					url:    ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'add_interest_group_option',
						mailchimp_list_id: mc_list_id,
						group_name: group_name,
						grouping_id: grouping_id
					},
					dataType: 'json',
					success: function( response ) {
						jQuery( '.yks-mc-update-preloader' ).remove();
						jQuery( '.active-edit-input' ).before( '<li class="option-ul-title" style="display:block;float:left;width:100%;margin-top:0;margin-bottom:0;"><h4 style="float:left;" alt="">'+group_name+'<span class="dashicons dashicons-yes successfuly-update-interest-group" style="line-height:.8;color:rgba(104, 200, 86, 0.59);margin-left:10px;"></span></h4></li>' );
						jQuery( '.active-edit-input' ).remove();
						setTimeout(function() {
							jQuery( '#updateInterestGroupContianer' ).find( '.successfuly-update-interest-group' ).fadeOut( 'fast' , function() {
								jQuery(this).remove();
							});
						}, 2000 );	
					},
					error: function( error ) {
						jQuery( '.yks-mc-update-preloader' ).remove();
						jQuery( '.active-edit-input' ).last().after( '<span class="yks-mc-update-option-error" style="float:left;display:block;width:100%;color:rgb(249, 141, 141);margin: 1em 0 .5em 0;"><span class="dashicons dashicons-no-alt" style="color:rgb(249, 141, 141) !important;line-height:.8;"></span> Error : '+error.responseText+'</span>' );
					}	
				});
			
			}
			
		} else {
			
			var new_value = jQuery( '.active-edit-input' ).val();
			var option_id = jQuery( '.active-edit-input' ).attr( 'alt' );
			var previous_value = jQuery( '.active-edit-input' ).attr( 'rel' );
			var grouping_id = jQuery( '#grouping-id' ).val();
			
			<!-- if the new value is empty, we just need to revert -->
			if ( new_value == '' ) {
				jQuery( '.yks-mc-update-preloader' ).remove();
				jQuery( '.active-edit-input' ).before( '<li class="option-ul-title" style="display:block;float:left;width:100%;margin-top:0;margin-bottom:0;"><h4 style="float:left;" alt="'+option_id+'">'+previous_value+'</h4></li>' );
				jQuery( '.active-edit-input' ).remove();
				return false;
			}
			
			// run ajax update interest group
			$.ajax({
				type:   'POST',
				url:    ajaxurl,
				data: {
					action: 'yks_mailchimp_form',
					form_action: 'update_interest_group',
					mailchimp_list_id: mc_list_id,
					grouping_id: grouping_id,
					previous_value: previous_value,
					new_value: new_value
				},
				dataType: 'json',
				success: function( interest_group_data ) {
					jQuery( '.yks-mc-update-preloader' ).remove();
					jQuery( '.active-edit-input' ).before( '<li class="option-ul-title" style="display:block;float:left;width:100%;margin-top:0;margin-bottom:0;"><h4 style="float:left;" alt="'+option_id+'">'+new_value+'<span class="dashicons dashicons-yes successfuly-update-interest-group" style="line-height:.8;color:rgba(104, 200, 86, 0.59);margin-left:10px;"></span></h4></li>' );
					jQuery( '.active-edit-input' ).remove();
					setTimeout(function() {
						jQuery( '#updateInterestGroupContianer' ).find( '.successfuly-update-interest-group' ).fadeOut( 'fast' , function() {
							jQuery(this).remove();
						});
					}, 2000 );	
				},
				error: function( error ) {	
					jQuery( '.yks-mc-update-preloader' ).remove();
					jQuery( '.active-edit-input' ).before( '<li class="option-ul-title" style="display:block;float:left;width:100%;margin-top:0;margin-bottom:0;"><h4 style="float:left;" alt="'+option_id+'">'+new_value+'</h4></li>' );
					jQuery( '.active-edit-input' ).remove();
					jQuery( '.option-ul-title' ).last().after( '<span class="yks-mc-update-option-error" style="float:left;display:block;width:100%;color:rgb(249, 141, 141);margin: 1em 0 .5em 0;"><span class="dashicons dashicons-no-alt" style="color:rgb(249, 141, 141) !important;line-height:.8;"></span> Error : '+error.responseText+'</span>' );
				}	
			});
		} // end conditional to check for new option, or update
		
		
	});
	
	/*
		Add a new Interest Group Option
		@since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.add-another-interest-group-option' , function() {
		jQuery( '.yks-mc-update-option-error' ).remove();
		jQuery( '#updateInterestGroupContianer' ).find( '.interest-group-new-option' ).remove();
		jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last().clone().insertAfter( jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last() );
		jQuery( '#updateInterestGroupContianer' ).find( '#options-table' ).find('.option-ul-title').last().find( 'h4' ).val( '' ).unwrap( 'h4' ).wrap( '<input type="text" class="active-edit-input interest-group-new-option" />' ); // this is the new interest-group-add
		jQuery( '#updateInterestGroupContianer' ).find( '.interest-group-new-option' ).focus();
	});
	
	jQuery( 'body' ).on( 'submit' , '#yks-mailchimp-update-interest-group-form' , function() {
		return false;
	});
	
	
	/* 
	* Clear our Error Log 
	*
	* since v5.2
	*/
	jQuery( 'body' ).on( 'click' , '.clear-yt4wp-error-log' , function() {
		
		jQuery( '#yt4wp-error-log-table' ).fadeTo( 'fast' , .5 );
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'yks_mailchimp_form',
				form_action: 'clear_yks_mc_error_log'
			},
			dataType: 'json',
			success: function (response) {
				setTimeout(function() {	
					jQuery( '#yks-mc-error-log-table' ).fadeOut( 'fast' , function() {
						jQuery( '.clear-yks-mc-error-log' ).attr( 'disabled' , 'disabled' );
						setTimeout(function() {
							jQuery( '.yks-mc-error-log-table-row' ).html( '<em>no errors logged</em>' );
						}, 250 );
					});
				}, 1000 );
			},
			error : function(error_response) {
				alert( 'There was an error with your request. Unable to clear the erorr log!' );
				console.log(error_response.responseText);
				jQuery( '#yt4wp-error-log-table' ).fadeTo( 'fast' , 1 );
			}
		});
	});
	
	/** 
		Update The Interest Group Type On Dropdown Change 
		Changes the interest group type eg Checkboxes => Radio Buttons
		@since v5.2
	**/
	jQuery( 'body' ).on( 'change' , '#yks-mc-interest-group-toggle-type' , function() {
		var grouping_id = jQuery( '#update-interest-group-settings' ).find( '#grouping-id' ).val();
		var mailchimp_list_id = jQuery( '#update-interest-group-settings' ).find( '#mc-list-id' ).val();
		var new_type = jQuery( this ).val();
		jQuery( this ).before( '<img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" alt="yks-mc-update-preloader" class="yks-mc-update-preloader" style="margin-right:10px;">' );
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'yks_mailchimp_form',
				form_action: 'change_yikes_mc_interest_group_type',
				grouping_id : grouping_id,
				value: new_type
			},
			dataType: 'json',
			success: function (response) {
				console.log(response);
				jQuery( '.yks-mc-update-preloader' ).replaceWith( '<span class="dashicons dashicons-yes successfuly-update-interest-group" style="line-height:1.2;color:rgba(104, 200, 86, 0.59);margin-right:10px;"></span>' );
				setTimeout(function() { // evan here
					jQuery( '.successfuly-update-interest-group' ).fadeOut( 'slow' , function() {
						jQuery( this ).remove();
					});
				}, 2000 );
				// ajax re-import this lists interest group data, to update title on the manage list forms page
					$.ajax({
						type:   'POST',
						url:    ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'get_interest_group_data',
							mailchimp_list_id: mailchimp_list_id
						},
						dataType: 'html',
						success: function( response ) {
							// re-populate interest grouping table
							jQuery( '#yks-mailchimp-interest-groups-container_'+mailchimp_list_id ).html(response);
						},
						error: function( error ) {
							console.log( 'Error re-importing interest group data' );
						}	
					});
			},
			error : function(error_response) {
				console.log(error_response);
			}
		});
	});
	
	
});

	/* 
		Clone Input Fields
		@since 5.2
	*/
	function yikesMCCloneInputField(e) {
		jQuery(e).prev().clone().insertAfter( jQuery('.radio-dropdown-option').last() ); 
		jQuery('.radio-dropdown-option').last().removeClass('first').addClass('fadeInDown animated').before('<span class="dashicons dashicons-no-alt remove-radio-dropdown-option"></span>').val('').focus(); 
	}
	
	
</script>

<div class="wrap">
    <div id="ykseme-icon" class="icon32"></div>
        <h2 id="ykseme-page-header">
            <?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
        </h2>
		<?php echo $this->help_review_container(); ?>
	<!-- if no API key is set -->
    <?php  if (!$this->optionVal['api-key']) { ?>    
		<div class="error">	
			<p>
				<?php _e('Before you can add MailChimp forms to your site, you need to','yikes-inc-easy-mailchimp-extender'); ?> <a href="admin.php?page=yks-mailchimp-form" class="yks-mailchimp-list-add"><?php _e('go to the MailChimp Settings page','yikes-inc-easy-mailchimp-extender'); ?></a> <?php _e('and add your API Key.','yikes-inc-easy-mailchimp-extender'); ?>
			</p>
		</div>	
	<!-- if an Invalid API key is set -->
	<?php } else if ( get_option('api_validation') == 'invalid_api_key' ) { ?>
			<p>
				<div class="error">
					<p>
						<?php _e('You must enter a valid API key to import and manage your lists.','yikes-inc-easy-mailchimp-extender'); ?>
					</p>
				</div>
			</p>
	<!-- if there is an API key -->
	<?php } else {  //end if statement if no api key ?>
        	<form id="yks-lists-dropdown" name="yks-lists-dropdown">
            	<table class="form-table yks-admin-form">
                	<tbody>            
                        <tr valign="top">
                        	<th scope="row">
                                <?php _e('Your Lists','yikes-inc-easy-mailchimp-extender'); ?>
                            </th>
                        	<td>
                        		<?php $this->getLists(); ?>
                            </td>
                        </tr>   
                    </tbody>
                </table>
        	</form>
		<input type="hidden" value='<?php echo json_encode( $this->optionVal['lists'] ); ?>' id="merge-field-data">
        <h3 <?php if ( count( $this->optionVal['lists'] ) > 0) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?>></h3>
        	<div id="yks-list-wrapper">
        		<?php echo $this->generateListContainers(); ?>
        	</div> 
    <?php }  //end else statement if there is an api key ?>         
</div>

<?php
	// need to update javascript for this
	// generates our thickbox with new fields to add
	// ajax retreive from MailChimp
	echo $this->generateNewMergeVariableContainer();
	echo $this->generateFormPreviewContainer(); 
	echo $this->generateUserTemplateHowTo(); 
	echo $this->generateMergeVariableUpdateContainer();
	echo $this->generateCreateInterestGroupContainer(); 
?>