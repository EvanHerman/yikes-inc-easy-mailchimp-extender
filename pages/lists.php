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
                function noListsCheck()
                        {
                        if($('#yks-list-wrapper .yks-list-container').size() <= 0)
                                {
								var plugin_directory_url = '<?php echo plugin_dir_url( __FILE__ ); ?>';
								 $('#yks-lists-dropdown').next().css('opacity',0);
								 $('#yks-list-wrapper').css({ 'background' : 'url("'+plugin_directory_url+'../images/yks_mc_lets_get_started.png")', 'height' : '175px' , 'width' : '400px' , 'background-repeat' : 'no-repeat' , 'background-position' : 'center', 'margin-top' : '-6em' });
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
																$('#yks-lists-dropdown').next().css('opacity',1);
																$('#yks-list-wrapper').css({ 'background' : 'transparent', 'height' : 'auto' , 'width' : 'auto', 'margin-top' : '0' });
                                                                scrollToElement($('#yks-list-wrapper .yks-list-container').last());
                                                                initializeScrollableLists();
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
													resizable: false
												});
                                        }
                        return false; 
                });
				// function which re-imports our specified MailChimp list and all form fields
                $('.yks-mailchimp-list-update').live('click', function(e){
                        var i       = $(this).attr('rel');
                        var f       = '#yks-mailchimp-form_'+i;
						var theButton = $(this);
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
															$('#yks-list-container_'+i).fadeOut('fast',function() {
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
							 resizable: false
							});
							
                        return false;
                });
				// function which imports a specified list from MailChimp
                $('.yks-mailchimp-import').live('click', function(e){
                        var i       = $(this).attr('rel');
						var form_name = $(this).parents('tbody').find('tr:first-child').find('.yks-mailchimp-listname').text();
						console.log(form_name);
                 						
						$("<div id='yks_mc_reset_plugin_settings'><div class='yks-mc-icon-yks-mc-warning yks-mc-delete-form-warning-icon'></div><p><?php _e("Are you sure you want to re-import this form and its fields from MailChimp?",'yikes-inc-easy-mailchimp-extender'); ?></p></div>").dialog({
							 title : "Re-Import Form?",
							 buttons : {
								"Yes" : function() {
									 $(this).dialog("close");
									 $.ajax({
                                        type:   'POST',
                                        url:    ajaxurl,
                                        data: {
                                            action:                         'yks_mailchimp_form',
                                            form_action:                    'list_import',
                                            id:                              i
                                        },
                                        dataType: 'json',
                                        success: function(MAILCHIMP)
                                                {
                                                if(MAILCHIMP != '-1')
                                                        {
                                                        $($('#yks-list-container_'+i)).replaceWith(MAILCHIMP);
                                                        $('#yks-list-container_'+i).yksYellowFade();
															// alert the user that it was a success
															$("<div id='yks_mc_reset_plugin_settings'><div class='dashicons dashicons-yes yks-mc-success-icon'></div><p><?php _e( "Your MailChimp form" , "yikes-inc-easy-mailchimp-extender" ); ?><strong>"+form_name+"</strong>\"<?php _e(' was successfully updated', 'yikes-inc-easy-mailchimp-extender' ); ?></p></div>").dialog({
															 title : "Form Successfully Updated",
															 buttons : {
																"Ok" : function() {
																	$(this).dialog("close");
																}
															  },
															  modal: true,
															  resizable: false
															});
                                                        initializeScrollableLists();
                                                        }
                                                else
                                                        {
                                                        $("<div id='yks_mc_reset_plugin_settings'><div class='dashicons dashicons-yes yks-mc-success-icon'></div><p><?php _e("It looks like this form is already up to date!", "yikes-inc-easy-mailchimp-extender" ); ?></p></div>").dialog({
														 title : "Form Up To Date",
														 buttons : {
															"Ok" : function() {
																$(this).dialog("close");
															}
														  },
														  modal: true,
														  resizable: false
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
			var thisListName = jQuery(this).parents('tbody').find('.yks-mailchimp-listname').text();
						 
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