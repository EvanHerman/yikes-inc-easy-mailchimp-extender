			<?php
			
			/* 
				Main Template file which controls the heartbeat API
				this is used to populate the dashboard widget with live data
			*/
				
			$request_uri =  "$_SERVER[REQUEST_URI]";
			global $pagenow;

			// Only proceed if on the the my mailchimp page
			// and the chimp-chatter tab
			if( 'admin.php?page=yks-mailchimp-my-mailchimp&tab=chimp_chatter' != basename($request_uri) && 'index.php' != $pagenow )
				return;
			
			?>
			<script>
			  jQuery(document).ready(function() {	
			  
					//hook into heartbeat-send: client will send the message 'marco' in the 'client' var inside the data array
					jQuery(document).on('heartbeat-send', function(e, data) {
						<?php if(  'index.php' == $pagenow ) { ?>
							// send some data
							// to begin the ajax
							data['yks_mc_chimp_chatter_heartbeat'] = 'get_chimp_chatter_widget_data';
						<?php } else { ?>
							// send some data
							// to begin the ajax
							data['yks_mc_chimp_chatter_heartbeat'] = 'get_chimp_chatter_data';
						<?php } ?>
					});
					
					//hook into heartbeat-tick: client looks for a 'server' var in the data array and logs it to console
					jQuery(document).on('heartbeat-tick', function(e, data) {	
					
						// pass our API key along
						var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
														
						// store datacenter value, from end of api key
						var dataCenter = apiKey.substr(apiKey.indexOf("-") + 1);
					
						if(data['yks_mc_chimp_chatter_data'] == 'Get MailChimp Chatter Data' ) {
							
							// update the chimp chatter div with new info
							// heartbeat api
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yks_mailchimp_form',
									form_action: 'yks_get_chimp_chatter',
									api_key: apiKey,
									data_center: dataCenter
								},
									dataType: 'html',
									success: function(response) {
									
										// store the new response, in the new response hidden div, for comparison
										jQuery('#new_chimp_chatter_response').html(response);
										
										// wrap our emails in the hidden new response with
										// <a> to match the original response
										jQuery("#new_chimp_chatter_response").find("td:nth-child(4)").each(function() {
												jQuery(this).filter(function(){
												var html = jQuery(this).html();
												// regex email pattern,
												// to wrap our emails in a link
												var emailPattern = /[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/g;  
													var matched_str = jQuery(this).html().match(emailPattern);
													var matched_str = jQuery(this).html().match(emailPattern);
														if(matched_str){
															var text = jQuery(this).html();
																jQuery.each(matched_str, function(index, value){
																text = text.replace(value,"<a href='mailto:"+value+"'>"+value+"</a>");
															});
															jQuery(this).html(text);
															return jQuery(this)
														}        
												});
											});
										
										// checking if the response is new...
										if ( jQuery('#new_chimp_chatter_response').html() == jQuery('#original_chimp_chatter_response').html() ) {
										
											console.log('the data is the same. no action taken.');
											
										} else {
										
											// remove the new stars
											jQuery('.fadeInDown').each(function() {
												jQuery(this).removeClass('animated').removeClass('fadeInDown').removeClass('new-chatter-item');
											});
										
											// count the new chatter items ( divide by 2 , for the spacer tr )
											var new_chatter_count = parseInt( jQuery('#new_chimp_chatter_response').find('.chatter-table-row').length / 2 );
											// count the original chatter items ( divide by 2 , for the spacer tr )
											var original_chatter_count = parseInt( jQuery('#original_chimp_chatter_response').find('.chatter-table-row').length / 2 );
											
											// calculate the number of new items
											var number_of_new_items = parseInt( new_chatter_count - original_chatter_count );
											
																					
											// give feedback that new data was found
											console.log('new mailchimp chatter data found. Re-populating....');
											
											// store the new response, in the original response 
											// field for comparison when heartbeat runs again
											jQuery('#original_chimp_chatter_response').html(response);
																						
											var i = 1;

											function new_chatter_loop_and_append() {
												
												setInterval(function() { 
												
												// this code is executed every 5 seconds:
													// animate the new items in
														// .....badass....	
													while (i <= number_of_new_items) {
																											
														var item_to_append =  jQuery('#new_chimp_chatter_response').find('.chatter-content-row:nth-child('+i+')');
														
															jQuery('.mailChimpChatterDiv').find('.chatter-table-row:first-child').before('<tr class="chatter-table-row chatter-spacer-row"><td>&nbsp;</td></tr>');
															jQuery('.mailChimpChatterDiv').find('.chatter-table-row:first-child').before( item_to_append.addClass('fadeInDown animated new-chatter-item') );
															
															i++;
													
													}

												}, 6000 );
												
											}
											
											// loop over our new items and append them to the current page
											new_chatter_loop_and_append();
	
											// re-apply the link wrapping the new items
											// so the new items match the old items
											jQuery("#original_chimp_chatter_response table#yks-admin-chimp-chatter .chatter-table-row td:nth-child(4)").each(function() {
												jQuery(this).filter(function(){
												var html = jQuery(this).html();
												// regex email pattern,
												// to wrap our emails in a link
												var emailPattern = /[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/g;  
													var matched_str = jQuery(this).html().match(emailPattern);
													var matched_str = jQuery(this).html().match(emailPattern);
														if(matched_str){
															var text = jQuery(this).html();
																jQuery.each(matched_str, function(index, value){
																text = text.replace(value,"<a href='mailto:"+value+"'>"+value+"</a>");
															});
															jQuery(this).html(text);
															return jQuery(this)
														}        
												});
											});
											
											// give some feedback
											console.log( "Populated the chimpchatter div with new content." );
											
										}
									
										// let user know heartbeat is running
										console.log('heartbeat found...');

									},
									error: function(response) {
										// do nothing here, 
										// incase we inturrupt it with a page change
									}
									
							});
							
						// Run this on the Dashboard, to re-populate the
						// mailchimp activity widget!
						} else if(data['yks_mc_chimp_chatter_data'] == 'Get MailChimp Chatter Widget Data' ) { 
							
							// update the chimp chatter div with new info
							// heartbeat api
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
	
										// store the new response, in the new response hidden div, for comparison
										jQuery('#new_chimp_chatter_response').html(response);
										
										
										// checking if the response is new...
										if ( jQuery('#new_chimp_chatter_response').html() == jQuery('#original_chimp_chatter_response').html() ) {
										
											console.log('the data is the same. no action taken.');
											
										} else {
										
											// remove the new stars
											jQuery('.fadeInDown').each(function() {
												jQuery(this).removeClass('animated').removeClass('fadeInDown').removeClass('new-chatter-item');
											});
										
											// count the new chatter items ( divide by 2 , for the spacer tr )
											var new_chatter_count = parseInt( jQuery('#new_chimp_chatter_response').find('.chatter-content-row').length  );
											// count the original chatter items ( divide by 2 , for the spacer tr )
											var original_chatter_count = parseInt( jQuery('#original_chimp_chatter_response').find('.chatter-content-row').length );
											
											// calculate the number of new items
											var number_of_new_items = parseInt( new_chatter_count - original_chatter_count );
																								
											// give feedback that new data was found
											console.log('new mailchimp chatter data found. Re-populating....');
											
											// store the new response, in the original response 
											// field for comparison when heartbeat runs again
											jQuery('#original_chimp_chatter_response').html(response);
																																	
											var i = 1;

											function new_chatter_loop_and_append() {
												
												setInterval(function() { 
												
												// this code is executed every 5 seconds:
													// animate the new items in
														// .....badass....	
													while (i <= number_of_new_items) {
																											
														var item_to_append =  jQuery('#new_chimp_chatter_response').find('.chatter-content-row:nth-child('+i+')');
														
															jQuery('.yks_mailChimp_Chatter').find('.chatter-table-row:first-child').before( item_to_append.addClass('fadeInDown animated new-chatter-item') );
															
															i++;
															
													}

												}, 6000 );
												
											}
											
											// loop over our new items and append them to the current page
											new_chatter_loop_and_append();
		
											// re-apply the link wrapping the new items
											// so the new items match the old items
											jQuery("#original_chimp_chatter_response table#yks-admin-chimp-chatter .chatter-table-row td:nth-child(4)").each(function() {
												jQuery(this).filter(function(){
												var html = jQuery(this).html();
												// regex email pattern,
												// to wrap our emails in a link
												var emailPattern = /[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/g;  
													var matched_str = jQuery(this).html().match(emailPattern);
													var matched_str = jQuery(this).html().match(emailPattern);
														if(matched_str){
															var text = jQuery(this).html();
																jQuery.each(matched_str, function(index, value){
																text = text.replace(value,"<a href='mailto:"+value+"'>"+value+"</a>");
															});
															jQuery(this).html(text);
															return jQuery(this)
														}        
												});
											});
											
											// give some feedback
											console.log( "Populated the chimpchatter div with new content." );
											
										}
									
										// let user know heartbeat is running
										console.log('heartbeat found...');

									},
									error: function(response) {
										// do nothing here, 
										// incase we inturrupt it with a page change
									}
									
							});				
							
						}
						
					});
							
					//hook into heartbeat-error: in case of error, let's log some stuff
					jQuery(document).on('heartbeat-error', function(e, jqXHR, textStatus, error) {
						console.log('<< BEGIN ERROR');
						console.log(textStatus);
						console.log(error);			
						console.log('END ERROR >>');			
					});
					
				});		
			</script>
			<?php
