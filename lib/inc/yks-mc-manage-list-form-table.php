<?php

/*
Main template file which houses our entire 'Manage List Forms' page table
Included into class.yksemeBase.php around line 2541
*/

			ob_start();
				// loop over each lists and build the page
				$i = 1;
				foreach($listArr as $list) {
					$get_list_data = $this->getListsData();
					?>
					<!-- title -->
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $i; ?>" class="panel-heading yks-mc-list-title-toggle panel panel-default">
									
						<span class="yks_mc_list_title">
						<?php
							if ($list['name']) {
								$thename = $list['name'];
								//echo $thename;
								printf( __( '%1$s', 'yikes-inc-easy-mailchimp-extender' ), $thename );
							} else {
								foreach ($thelistdata as $lkey => $lval) {
									if ($lkey == $list['id']) {
										$thename = $lval;
										//echo $thename;
										printf( __( '%1$s', 'yikes-inc-easy-mailchimp-extender' ), $thename );
									}
								}
							}
						?>
						</span>
								
						<span class="yks_mc_subscriber_count"><?php if ( $get_list_data['subscriber-count']['subscriber-count-'.$list['id']] > 1 || $get_list_data['subscriber-count']['subscriber-count-'.$list['id']] == 0 ) { echo $get_list_data['subscriber-count']['subscriber-count-'.$list['id']] . __( ' subscribers' , 'yikes-inc-easy-mailchimp-extender' ); } else { echo $get_list_data['subscriber-count']['subscriber-count-'.$list['id']] . __( ' subscriber' , 'yikes-inc-easy-mailchimp-extender' ); } ?></span>
					
					</a>

					<div id="collapse_<?php echo $i; ?>" class="panel-collapse collapse panel-body">
						<div class="yks-list-container" id="yks-list-container_<?php echo $list['id']; ?>">
							<div class="yks-status" id="yks-status" style="display: none;">
								<div class="yks-success" style="padding:.25em;"><p>&nbsp;<?php _e( 'Your List Was Successfully Saved!' , 'yikes-inc-easy-mailchimp-extender' ); ?></p></div>
							</div>
							<div class="yks-status-error" id="yks-status-error" style="display: none;">
								<div class="yks-error" style="padding:.25em;"><p>&nbsp;<?php _e( 'Your settings were not saved (or you did not change them).' , 'yikes-inc-easy-mailchimp-extender' ); ?></p></div>
							</div>
							<span class="yikes-lists-error" style="display:none;"><?php _e( "I'm sorry there was an error with your request." , "yikes-inc-easy-mailchimp-extender" ); ?></span>
							<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
								<input type="hidden" name="yks-mailchimp-unique-id" id="yks-mailchimp-unique-id_<?php echo $list['id']; ?>" value="<?php echo $list['id']; ?>" />
								<table class="form-table  yks-admin-form">
									<tbody>
											<!-- display the specific MailChimp list ID back to the user -->							
											<tr valign="top">
												<th scope="row"><label for="yks-mailchimp-api-key"><?php _e( 'MailChimp List ID' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
												<td><?php echo $list['list-id'];  ?>
												</td>
											</tr>				
											<!-- display the shortcode with the specific list ID -->
											<tr valign="top">
												<th scope="row"><label for="yks-mailchimp-shortcode"><?php _e( 'Shortcode' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
												<td class="yks-mailchimp-shortcode">
													<span class="shortcode-code">[yks-mailchimp-list id="<?php echo $list['id']; ?>" submit_text="Submit"]</span>
													<span class="description yks-margin-left"><?php _e( 'Paste this shortcode into whatever page or post you want to add this form to' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
												</td>
											</tr>
											<!-- display the PHP snippet with the specific list ID -->
											<tr valign="top">
												<th scope="row"><label for="yks-mailchimp-api-key"><?php _e( 'PHP Snippet' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
												<td>
													<?php /* echo htmlentities('<?php echo yksemeProcessSnippet(\''.$list['id'].'\', \'Submit\'); ?>'); */ ?>
													<?php echo htmlentities('<?php echo yksemeProcessSnippet( "'.$list['id'].'" , "Submit" ); ?>'); ?>
													<span class="description yks-margin-left"><?php _e( 'Use this code to add this form to a template file' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
												</td>
											</tr>
											<!-- display subscriber count here -->
											<tr valign="top">
												<th scope="row"><label for="yks-mailchimp-api-key"><?php _e( 'Number of Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
												<td>
													<!-- rel contains list id that we pass along to our function to ajax retreive all subscribers -->
													<span class="number-of-subscribers-<?php echo $list['id']; ?>"><?php echo $get_list_data['subscriber-count']['subscriber-count-'.$list['id']]; ?>&nbsp;</span><a href="#TB_inline?width=600&height=550&inlineId=yikes-mailchimp-subscribers-box" class="thickbox displayListSubscribers button-secondary" rel="<?php echo $list['id']; ?>">View</a>	
												</td>
											</tr>
											<!-- display the forms fields, with options to customize -->
											<tr valign="top">
												<td scope="row">
													<label for="api-key"><strong><?php _e( 'Form Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label>
													<p class="description">
														<?php _e( 'Check the fields you want included in your form (Email Address is required).' , 'yikes-inc-easy-mailchimp-extender' ); ?>
													</p> 
													
													<p class="description">
														<?php _e( 'Use the green arrows to drag-and-drop the fields and rearrange their order.' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														<span class="yks-mailchimp-sorthandle-img"></span>
													</p>
													<p class="description">
														<a alt="<?php echo $list['id']; ?>" class="button-secondary thickbox add-new-field-thickbox-open" onclick="return false;" href="#TB_inline?width=auto&height=200&inlineId=newMergeVariableContainer">Add New Field</a>
													</p>
													
												</th>
												<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
													<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
														<legend class="screen-reader-text"><span><?php _e( 'Active Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></legend>
														<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
															<!-- create sortable rows populated with mailchimp data -->
															<?php 
															$num = 1;
															foreach($list['fields'] as $field) { ?>
																<div class="yks-mailchimp-fields-list-row" alt="<?php echo $field['merge']; ?>" rel="<?php echo $field['id']; ?>">
																	<label title="Delete <?php echo $field['label']; ?> Field">
																		<span class="yks-mc-delete-merge-var"><span class="dashicons dashicons-no-alt"></span></span>
																	</label>
																	<label title="Edit <?php echo $field['label']; ?> Field">
																		<span class="yks-mc-merge-var-change"><span class="dashicons dashicons-edit"></span></span>
																	</label>
																	<label title="Reorder <?php echo $field['label']; ?>">
																		<span class="yks-mailchimp-sorthandle"><?php _e( 'Drag' , 'yikes-inc-easy-mailchimp-extender' ); ?> &amp; <?php _e( 'drop' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
																	</label>
																	<label title="Toggle Visibility of <?php echo $field['label']; ?>">
																		<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['require'] == 1 ? 'disabled="disabled"' : ''); ?> />
																	</label>	
																		&nbsp;
																	<label>
																		<div class="yks-mailchimp-field-name"><?php echo $field['label']; ?></div>
																	</label>
																	<!-- <span class="yks-mailchimp-field-merge"><span class="description"><?php _e( 'Merge field' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" class="merge_field_input" name="<?php echo $field['name']; ?>-merge" id="<?php echo $field['id']; ?>-merge" value="<?php echo $field['merge']; ?>"<?php echo (($field['locked'] == 1 || $field['merge'] == false) ? ' disabled="disabled"' : ''); ?> /></span>-->
																	<span class="yks-mailchimp-field-placeholder"><span class="description"><?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" name="placeholder-<?php echo $list['id'].'-'.$num; ?>" id="<?php echo $field['id']; ?>-placeholder" placeholder="<?php echo $field['label']; ?>" value="<?php if(isset($field['placeholder-'.$list['id'].'-'.$num])) { echo $field['placeholder-'.$list['id'].'-'.$num]; } ?>" /></span>
																	<span class="yks-mailchimp-field-custom-field-class"><span class="description"><?php _e( 'Custom Class' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" name="custom-field-class-<?php echo $list['id'].'-'.$num; ?>" id="<?php echo $field['id']; ?>-custom-field-class" value="<?php if(isset($field['custom-field-class-'.$list['id'].'-'.$num])) { echo $field['custom-field-class-'.$list['id'].'-'.$num]; } ?>" /></span>
																</div>
																<?php 
																$num++;
															} ?>
														</div>
														
															<!-- display the Interest Grouping Info - if it exists for the given form -->
															<tr valign="top">
																<td>
																	<label for="yks-mailchimp-api-key"><?php _e( 'Interest Groups' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
																	<p class="description">
																		<a alt="<?php echo $list['id']; ?>" class="button-secondary thickbox add-new-group-thickbox-open" onclick="return false;" href="#TB_inline?width=auto&height=600&inlineId=newInterestGroupContianer">Add New Group</a>
																	</p>
																</td>
																
																	<td>
																		<fieldset class="yks-mailchimp-interest-groups-container" id="yks-mailchimp-interest-groups-container_<?php echo $list['id']; ?>">
																		<?php $this->getListInterestGroups( $list['id'] ); ?>
																		</fieldset>
																	</td>						
															</tr>
														
													</fieldset>
														<!-- send welcome message (on a per list basis) -->
														<tr valign="top">
															<th scope="row"><label for="yks-mailchimp-send-welcome"><?php _e( 'Disable Welcome Email?' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
															<td>
																<span class="yks-mailchimp-send-welcome-checkbox-holder">
																	<input type="checkbox" name="yks_mailchimp_send_welcome_<?php echo $list['id']; ?>" class="yks_mailchimp_send_welcome" id="yks-mailchimp-send-welcome-<?php echo $list['id']; ?>" value="1" <?php if(isset($field['yks_mailchimp_send_welcome_'.$list['id']])) { echo ($field['yks_mailchimp_send_welcome_'.$list['id']] == 1 ? 'checked="checked"' : ''); } ?> />
																	<?php if( isset( $field['yks_mailchimp_send_welcome_'.$list['id']] ) && $field['yks_mailchimp_send_welcome_'.$list['id']] == '1' ) { ?>
																		<span class="description yks-margin-left"><?php _e( 'the welcome email will ' , 'yikes-inc-easy-mailchimp-extender' ); ?><strong><?php _e( 'not' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong><?php _e( ' be sent for this mailing list' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
																	<?php } else { ?>
																		<span class="description yks-margin-left"><?php _e( 'the welcome email will be sent for this list.' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
																	<?php } ?>
																</span>
															</td>
														</tr>
														<!-- display redirect checkbox here -->
														<tr valign="top">
															<th scope="row"><label for="yks-mailchimp-url-redirect"><?php _e( 'Redirect User On Submission' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
															<td>
																<span class="yks-mailchimp-redirect-checkbox-holder">
																	<input type="checkbox" name="yks_mailchimp_redirect_<?php echo $list['id']; ?>" class="yks_mailchimp_redirect" id="yks-mailchimp-redirect-<?php echo $list['id']; ?>" value="1" <?php if(isset($field['yks_mailchimp_redirect_'.$list['id']])) { echo ($field['yks_mailchimp_redirect_'.$list['id']] == 1 ? 'checked="checked"' : ''); } ?> />
																	<span class="description yks-margin-left"><?php _e( 'choose a page to redirect the user to after they submit the form.' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
																		<!-- drop down of registered posts/pages -->
																		<li id="pages" class="yks_mc_pages_dropdown_<?php echo $list['id']; ?>"  <?php if(!isset($field['yks_mailchimp_redirect_'.$list['id']]) || $field['yks_mailchimp_redirect_'.$list['id']] == '' ) { echo 'style="list-style:none;display:none;"'; } else { echo 'style="list-style:none;"'; } ?> >
																			<h4><?php _e( 'Select A Post/Page' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
																					<form action="<? bloginfo('url'); ?>" method="get">
																						 <select id="page_id" name="page_id_<?php echo $list['id']; ?>" >
																							 <?php
																								// set up variables for the queries
																								 global $post;
																								 global $page;
																								 $post_types = apply_filters( 'yks_redirect_add_post_types' , array( 'post' ) , 10  );
																								 $args_posts = array( 'post_type' => $post_types , 'numberposts' => -1);
																								 $args_pages = array(
																									'sort_order' => 'ASC',
																									'sort_column' => 'post_title',
																									'hierarchical' => 1,
																									'exclude' => '',
																									'include' => '',
																									'meta_key' => '',
																									'meta_value' => '',
																									'authors' => '',
																									'child_of' => 0,
																									'parent' => -1,
																									'exclude_tree' => '',
																									'number' => '',
																									'offset' => 0,
																									'post_type' => 'page',
																									'post_status' => 'publish'
																								); 
																								$pages = get_pages($args_pages);
																								// print_r($pages);
																								
																								 $posts = get_posts($args_posts);
																								// print_r($posts);
																								?>
																								<optgroup label="Posts"><?php
																								
																								// throwing error -> must resolve
																								 foreach( $posts as $post ) : setup_postdata($post); ?>
																										<option <?php if(isset($field['page_id_'.$list['id']])) { selected( $field['page_id_'.$list['id']], $post->ID ); } ?> value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
																								 <?php endforeach; ?>
																								 
																								 </optgroup>
																								 <optgroup label="Pages">
																								  <?php 
																								  foreach( $pages as $page ) : ?>
																										<option <?php if(isset($field['page_id_'.$list['id']])) { selected( $field['page_id_'.$list['id']], $page->ID ); } ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
																								 <?php endforeach; ?>
																								 </optgroup>
																						 </select>
																					 </form>
																		</li>
																</span>														
															</td>
														</tr>
														<!-- display color picker here, to customize the form styles! -->
														<tr valign="top">
															<th scope="row"><label for="yks-mailchimp-url-redirect"><?php _e( 'Custom Styles' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
															<td>
																<span class="yks-mailchimp-custom-styles-holder">
																	<input type="checkbox" name="yks_mailchimp_custom_styles_<?php echo $list['id']; ?>" class="yks_mailchimp_custom_styles" id="yks-mailchimp-custom-styles-<?php echo $list['id']; ?>" value="1" <?php if ( isset( $list['custom_styles']['active'] ) && $list['custom_styles']['active'] == 1 ) { echo 'checked=checked'; } ?> <?php if ( isset( $list['custom_template']['active'] ) && $list['custom_template']['active'] == 1 ) { echo 'disabled=disabled'; } ?> />
																	<span class="description yks-margin-left"><?php _e( 'set custom styles for this form.' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
																		<!-- drop down of registered posts/pages -->
																		<li id="custom-style-list" class="yks_mc_pages_dropdown_<?php echo $list['id']; ?>"  <?php if( !isset($list['custom_styles']) || $list['custom_styles']['active'] == '0') { echo 'style="list-style:none;display:none;"'; } else { echo 'style="list-style:none;"'; } ?> >
																			
																			<h4><?php _e( 'Custom Form Styles' , 'yikes-inc-easy-mailchimp-extender' ); ?>&nbsp;<a href="#TB_inline?width=600&inlineId=formPreviewContainer" class="thickbox button-secondary populatePreviewFormContainer">preview form</a></h4>
																				
																				<table class="custom_style_table style_table_left yks-mailchimp-fields-container yks-mailchimp-fields-list">

																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Form Background Color' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_form_background_color'] ) ) { echo $list['custom_styles']['yks_mc_form_background_color']; } else { echo '#fff'; } ?>" class="yks-mc-color-picker" name="yks-mc-background-color" data-default-color="#fff" /></td> 
																						</tr>
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Text Color' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_form_text_color'] ) ) { echo $list['custom_styles']['yks_mc_form_text_color']; } else { echo '#333'; } ?>" class="yks-mc-color-picker" name="yks-mc-text-color" data-default-color="#333" /></td> 
																						</tr>
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Submit Button Color' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_submit_button_color'] ) ) { echo $list['custom_styles']['yks_mc_submit_button_color']; } else { echo '#3ed664'; } ?>" class="yks-mc-color-picker" name="yks-mc-submit-button-color" data-default-color="#3ed664" /></td> 
																						</tr>
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Submit Button Text Color' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_submit_button_text_color'] ) ) { echo $list['custom_styles']['yks_mc_submit_button_text_color']; } else { echo '#fff'; } ?>" class="yks-mc-color-picker" name="yks-mc-submit-button-text-color" data-default-color="#fff" /></td> 
																						</tr>
																				</table>
																				
																				
																				
																				<table class="custom_style_table style_table_right yks-mailchimp-fields-container yks-mailchimp-fields-list">
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Form Padding' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_form_padding'] ) ) { echo $list['custom_styles']['yks_mc_form_padding'].$list['custom_styles']['yks_mc_form_padding_measurement']; } else { echo '1em'; } ?>" name="yks-mc-form-padding"  /></td> 
																						</tr>
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Form Width (% works best)' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td"><input type="text" value="<?php if ( isset( $list['custom_styles']['yks_mc_form_width'] ) ) { echo $list['custom_styles']['yks_mc_form_width']; } else { echo '100%'; } ?>" name="yks-mc-form-width"  /></td> 
																						</tr>
																						<tr class="yks-mailchimp-fields-list-row"> 
																							<td><?php _e( 'Form Alignment' , 'yikes-inc-easy-mailchimp-extender' ); ?></td>
																							<td class="custom-styles-input-td">
																								<select name="yks-mc-form-alignment">
																									
																									<option value="none" <?php if ( !isset( $list['custom_styles']['yks_mc_form_alignment'] ) || $list['custom_styles']['yks_mc_form_alignment'] == 'none' ) { echo 'selected="selected"'; } ?>><?php _e( 'None' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
																									<option value="left" <?php if ( isset( $list['custom_styles']['yks_mc_form_alignment'] ) && $list['custom_styles']['yks_mc_form_alignment'] == 'left' ) { echo 'selected="selected"'; } ?>><?php _e( 'Left' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
																									<option value="center" <?php if ( isset( $list['custom_styles']['yks_mc_form_alignment'] ) && $list['custom_styles']['yks_mc_form_alignment'] == 'center' ) { echo 'selected="selected"'; } ?>><?php _e( 'Center' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
																									<option value="right" <?php if ( isset( $list['custom_styles']['yks_mc_form_alignment'] ) && $list['custom_styles']['yks_mc_form_alignment'] == 'right' ) { echo 'selected="selected"'; } ?>><?php _e( 'Right' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
																								</select>
																							</td>
																						</tr>
																				</table>
																				
																		</li>
																</span>														
															</td>
														</tr>
														<tr valign="top">
															<th scope="row"><label for="yks-mailchimp-url-redirect"><?php _e( 'Form Template' , 'yikes-inc-easy-mailchimp-extender' ); ?></label></th>
															<td>
																<span class="yks-mailchimp-custom-template-holder">
																	<input type="checkbox" name="yks_mailchimp_custom_template_<?php echo $list['id']; ?>" class="yks_mailchimp_custom_template" id="yks-mailchimp-custom-template-<?php echo $list['id']; ?>" value="1" <?php if ( isset( $list['custom_template']['active'] ) && $list['custom_template']['active'] == 1 ) { echo 'checked=checked'; } ?> <?php if ( isset( $list['custom_styles']['active'] ) && $list['custom_styles']['active']== 1 ) { echo 'disabled=disabled'; } ?> />
																	<span class="description yks-margin-left"><?php _e( 'set a template for this form.' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>																
																	<!-- template selection dropdown -->
																	<li id="custom-template-list" class="yks_mc_template_dropdown_<?php echo $list['id']; ?>"  <?php if( !isset($list['custom_template']) || $list['custom_template']['active'] == '0') { echo 'style="list-style:none;display:none;"'; } else { echo 'style="list-style:none;"'; } ?> >
																		<span class="template-dropdown-list">

																			<h4><?php _e( 'Select a Template' , 'yikes-inc-easy-mailchimp-extender' ); ?>&nbsp;<a href="#TB_inline?width=600&inlineId=formPreviewContainer" class="thickbox button-secondary populatePreviewFormContainer custom_template">preview form</a> <?php if( !is_dir( get_stylesheet_directory() . '/yikes-mailchimp-user-templates' ) ) { ?>&nbsp;<a href="#" onclick="return false;" class="button-secondary import_template_boilerplates">import boilerplate files</a><a href="#TB_inline?width=600&height=550&inlineId=user_template_how_to" class="thickbox whats-this-help">whats this?</a><?php } ?></h4>
																			<?php		
																				// build our custom template dropdown
																				$this->buildCustomTemplateDropdown($list); 
																			?>
																		</span>
																	</li>
																	<li style="list-style:none;margin-top:1em;">
																		<span class="description"><?php _e( 'note : some light css styling may be necessary to fit in with your theme.' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>	
																	</li>
																	
																</span>														
															</td>
															
			
														</tr>
														<!-- end custom template selection -->
												</td>
											</tr>
											<tr>
												<td></td>
												<td>
													<input type="submit" name="submit" class="yks-mailchimp-list-update button-primary" value="<?php _e( 'Save Form Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>" rel="<?php echo $list['id']; ?>" />
													<input type="button" name="delete" class="yks-mailchimp-delete button-primary" value="<?php _e( 'Delete Form' , 'yikes-inc-easy-mailchimp-extender' ); ?>" rel="<?php echo $list['id']; ?>" data-title="<?php echo $thename; ?>" />
													<input type="button" name="import" class="yks-mailchimp-import button-primary" value="<?php _e( 'Re-Import Form Fields from MailChimp' , 'yikes-inc-easy-mailchimp-extender' ); ?>" rel="<?php echo $list['id']; ?>" />
												</td>
											</tr>
									</tbody>
								</table>
							</form>
						</div>
					</div>
					<?php
						$i++;
					}
					?>
					<!-- run loop to display content here -->
					<!-- thickbox for our hidden content, we will display subscribed peoples here based on which link is clicked -->
					<?php add_thickbox(); ?>
					<div id="yikes-mailchimp-subscribers-box" style="display:none;">
						<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
							<div class="yks_mc_subscribers">
							</div>
					</div>