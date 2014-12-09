			<!--
				Main template file for generating our container to update
				a merge variable on a given list
			-->
			<div id="updateMergeVariableContainer" style="display:none;">
				 <p>
					<div id="merge-variable-update">
						<form method="post" name="yks-mailchimp-update-existing-field-form" id="yks-mailchimp-update-existing-field-form">
							<input type="hidden" name="mc-list-id" id="mc-list-id" value="">
							<input type="hidden" name="old-merge-tag" id="old-merge-tag" value="">
							<table class="form-table" style="margin-top: 2em;">
								<tbody>
									<!-- Field Type -->
									<tr valign="top">
										<th scope="row"><label for="add-field-field-name"><?php _e('Field Type','yikes-inc-easy-mailchimp-extender'); ?></label></th>
										<td>
											<strong id="field-type-text"></strong> 
										</td>
									</tr>
									<!-- Field Name -->
									<tr valign="top">
										<th scope="row"><label for="add-field-field-name"><?php _e('Field Name','yikes-inc-easy-mailchimp-extender'); ?></label></th>
										<td>
											<input type="text" name="add-field-field-name" id="add-field-field-name" style="width:100%;">
										</td>
									</tr>
									<!-- Field Name Description -->
									<tr>
										<td></td>
										<td class="yks-settings-description">
											<?php _e('enter the name of your new field','yikes-inc-easy-mailchimp-extender'); ?><br />
										</td>
									</tr>
									<!-- Field Merge Tag -->
									<tr valign="top">
										<th scope="row"><label for="add-field-field-merge-tag"><?php _e('Merge Tag','yikes-inc-easy-mailchimp-extender'); ?></label></th>
										<td>
											<input type="text" name="add-field-field-merge-tag" id="add-field-field-merge-tag" style="width:100%;">
										</td>
									</tr>
									<!-- Field Merge Tag Description -->
									<tr>
										<td></td>
										<td class="yks-settings-description">
											<?php _e('auto generated merge tag. The merge tag to add, e.g. FNAME. 10 bytes max, valid characters: "A-Z 0-9 _" no spaces, dashes, etc. Some tags and prefixes are reserved.','yikes-inc-easy-mailchimp-extender'); ?><br />
										</td>
									</tr>
									<!-- New Field Default Value -->
									<tr valign="top" class="default-value-text-field" style="display:none;">
										<th scope="row"><label for="add-field-field-merge-tag"><?php _e('Default Value','yikes-inc-easy-mailchimp-extender'); ?></label></th>
										<td>
											<input type="text" id="add-field-default-value" name="add-field-default-value" style="width:100%;" value="">
										</td>
									</tr>
									<!-- New Field Default Value Description -->
									<tr class="default-value-text-field" style="display:none;">
										<td></td>
										<td class="yks-settings-description">
											<?php _e('enter a default value for this field.','yikes-inc-easy-mailchimp-extender'); ?><br />
											<em style="display:block;margin-top:.5em;"> <strong><?php _e('pre-defined tags: ','yikes-inc-easy-mailchimp-extender'); ?></strong><a href="#" onclick="return false;" class="pre-defined-post-tag">{post_title}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{post_id}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{page_url}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{blog_name}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{user_logged_in}</a><?php $user_defined_filters = apply_filters( 'yikes_mailchimp_default_value_tag' , $custom_tag_array=array() ); foreach( $user_defined_filters as $custom_default_tag ) { echo ' , <a href="#" onclick="return false;" class="pre-defined-post-tag">' . $custom_default_tag . '</a>'; } ?></em>
										</td>
									</tr>
									<!-- Field Required Setting -->
									<tr valign="top">
										<th scope="row"><label for="update-field-field-required"><?php _e('Required?','yikes-inc-easy-mailchimp-extender'); ?></label></th>
										<td>
											<span style="float:left;"><input type="radio" name="update-field-field-required" value="true" class="update-field-field-required-yes">Yes</span>
											<span style="float:left; margin-left:.5em;"><input type="radio" name="update-field-field-required" value="false" style="float:left;" class="update-field-field-required-no">No</span>
										</td>
									</tr>									
									<!-- New Field Date Format -->
										<tr valign="top" class="yks-mc-add-field-dateformat">
											<th scope="row"><label for="add-field-public"><?php _e('Date Format','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<select name="add-field-dateformat" id="add-field-dateformat">
													<option value="MM/DD" class="option-birthday" style="display:none;">MM/DD</option>
													<option value="DD/MM" class="option-birthday" style="display:none;">DD/MM</option>
													<option class="option-date" value="MM/DD/YYYY" style="display:none;">MM/DD/YYYY</option>
													<option class="option-date" value="DD/MM/YYYY" style="display:none;">DD/MM/YYYY</option>
												</select>
											</td>
										</tr>
										<!-- New Field Date Format -->
										<tr class="yks-mc-add-field-dateformat">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('select how the date should be formatted','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- Radio Button / Dropdown Button Selection Items -->
										<tr valign="top" class="yks-mc-add-field-radio-dropdown">
											<th scope="row"><label for="add-field-public"><?php _e('Options','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" name="radio-dropdown-option[]" class="radio-dropdown-option first">
												<!-- <input type="hidden" name="radio-dropdown-option[]" class="radio-dropdown-option-hidden"> -->
												<a href="#" class="yks-mc-add-new-radio-dropdown-option button-secondary" style="margin-top:-1px;" onclick="yikesMCCloneInputField(jQuery(this)); return false;">+</a>
											</td>
										</tr>
										<!-- New Field Date Format -->
										<tr class="yks-mc-add-field-radio-dropdown">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('add options for the user to select from.','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Update Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				 </p>
			</div>