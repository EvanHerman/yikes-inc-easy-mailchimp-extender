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
								 $('#yks-lists-dropdown').next().css('opacity',0);
								 $('#yks-list-wrapper').css({ 'background' : 'url("'+plugin_directory_url+'../images/yks_mc_lets_get_started.png")', 'height' : '175px' , 'width' : '400px' , 'background-repeat' : 'no-repeat' , 'background-position' : 'center', 'margin-top' : '-2em' });
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
													alert(data);
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
                                update: function(event, ui)							
                                        {
										// form id
                                        var i = $(this).attr('rel');
                                        var newCt       = 0;
                                        var updateString        = '';
                                        var fieldCt     = $('#yks-mailchimp-fields-list_'+i+' label').size();
										// update number on the placeholder input box after re-sort to store variable in appropriate locations
										$('#yks-mailchimp-fields-list_'+i+' .yks-mailchimp-fields-list-row').each(function() {
											var thisIndex = ($(this).index() + 1);
											var thisName = $(this).find('.yks-mailchimp-field-placeholder').find('input').attr('name');
											$(this).find('.yks-mailchimp-field-placeholder').find('input').attr('name', 'placeholder-'+i+'-'+thisIndex);
										});
										
                                        $('#yks-mailchimp-fields-list_'+i+' label').each(function(e){
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
	
						// alert($(f).serialize());
                        $.ajax({
                                type:   'POST',
                                url:    ajaxurl,
                                data: {
                                                        action:                 'yks_mailchimp_form',
                                                        form_action:            'list_update',
                                                        form_data:               $(f).serialize()
                                                        },
                                dataType: 'json',
                                success: function(MAILCHIMP)
                                        {
										console.log(MAILCHIMP);	
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
												console.log(MAILCHIMP);
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
                                              action:                 'yks_mailchimp_form',
                                              form_action:            'list_delete',
                                              id:      i
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
                                            action:                         'yks_mailchimp_form',
                                            form_action:                    'merge_variables_reImport',
                                            id:                              i
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
                                                        action:                 'yks_mailchimp_form',
                                                        form_action:            'notice_hide'
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
							   action:                 "yks_mailchimp_form", // pass data to this Ajax function
							   form_action:            "yks_remove_subscriber", // our data will final be passed through this function
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
		}, 500);	 
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
							   action:                 "yks_mailchimp_form", // pass data to this ajax function
							   form_action:            "yks_get_subscriberInfo", // our data will finall be passed through this function
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
								   action:                 "yks_mailchimp_form", // pass data to this ajax function
								   form_action:            "yks_mc_get_custom_template_preview", // our data will finall be passed through this function
								   template_name : selected_form_text,
								   selected_form_screenshot: selected_form_screenshot,
								   template_path: template_path
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
								   action:                 "yks_mailchimp_form", // pass data to this ajax function
								   form_action:            "yks_mc_get_form_preview", // our data will finall be passed through this function
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
									   action:                 "yks_mailchimp_form", // pass data to this ajax function
									   form_action:            "copy_user_templates_to_theme" // our data will finall be passed through this function
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
		
		// clear the modal div when invisible
		setInterval(function() {
			if ( jQuery( '.yks_mc_preview_form_container' ).is(':visible') ) {
				return;
			} else {
				jQuery( '.yks_mc_preview_form_container' ).html('<?php echo '<img src="' . admin_url() . '/images/wpspin_light.gif" alt="preloader" style="margin-left: 50%; margin-top: 25%;">'; ?>');
				jQuery( '.yks_mc_preview_form_container' ).prev().remove();
			}
		} , 1200 );
		
		
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
		} , 1200 );
		
		
});
</script>

<div class="wrap">
    <div id="ykseme-icon" class="icon32"></div>
        <h2 id="ykseme-page-header">
            <?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
        </h2>
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
        <h3><?php _e('Manage Forms','yikes-inc-easy-mailchimp-extender'); ?></h3>
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
?>