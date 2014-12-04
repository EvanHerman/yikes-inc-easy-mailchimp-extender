			<!--
				Main Template File for generating a new merge variable on a given list
			-->	
			<div id="newMergeVariableContainer" style="display:none;">
				 <p>
					 <h4 style="width:100%;text-align:center;">Add a Field</h4>
						<p>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="text">Text</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="number">Number</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="radio">Radio Buttons</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="dropdown">Dropdown</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field "alt="date">Date</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="birthday">Birthday</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="address">Address</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="zip">Zip Code (US only)</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="phone">Phone</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="url">Website</a>
							  <a href="#" onclick="return false;" class="button-secondary add-new-field" alt="imageurl">Image</a>
						</p>
						<div id="merge-variable-settings">
							<hr />
							<strong class="setup-the-field-title">Setup The Field</strong>
							<form method="post" name="yks-mailchimp-add-new-field-form" id="yks-mailchimp-add-new-field-form">
								<input type="hidden" name="mc-list-id" id="mc-list-id" value="">
								<table class="form-table" style="margin-top: 2em;">
									<tbody>
										<!-- New Field Name -->
										<tr valign="top">
											<th scope="row"><label for="add-field-field-name"><?php _e('Field Name','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" id="add-field-field-name" name="add-field-field-name" style="width:100%;">
											</td>
										</tr>
										<!-- New Field Name Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('enter the name of your new field','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- New Field Merge Tag -->
										<tr valign="top">
											<th scope="row"><label for="add-field-field-merge-tag"><?php _e('Merge Tag','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" id="add-field-field-merge-tag" name="add-field-field-merge-tag" style="width:100%;" value="">
											</td>
										</tr>
										<!-- New Field Merge Tag Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('auto generated merge tag. The merge tag to add, e.g. FNAME. 10 bytes max, valid characters: "A-Z 0-9 _" no spaces, dashes, etc. Some tags and prefixes are reserved.','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- New Field Default Value -->
										<tr valign="top" class="default-value-text-field">
											<th scope="row"><label for="add-field-field-merge-tag"><?php _e('Default Value','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" id="add-field-default-value" name="add-field-default-value" style="width:100%;" value="">
											</td>
										</tr>
										<!-- New Field Default Value Description -->
										<tr class="default-value-text-field">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('enter a default value for this field.','yikes-inc-easy-mailchimp-extender'); ?><br />
												<em style="display:block;margin-top:.5em;"> <strong><?php _e('pre-defined tags: ','yikes-inc-easy-mailchimp-extender'); ?></strong><a href="#" onclick="return false;" class="pre-defined-post-tag">{post_title}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{post_id}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{page_url}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{blog_name}</a> , <a href="#" onclick="return false;" class="pre-defined-post-tag">{user_logged_in}</a><?php $user_defined_filters = apply_filters( 'yikes_mailchimp_default_value_tag' , $custom_tag_array=array() ); foreach( $user_defined_filters as $custom_default_tag ) { echo ' , <a href="#" onclick="return false;" class="pre-defined-post-tag">' . $custom_default_tag . '</a>'; } ?></em>
											</td>
										</tr>
										<!-- New Field Required Setting -->
										<tr valign="top">
											<th scope="row"><label for="add-field-field-required"><?php _e('Required?','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<span style="float:left;"><input type="radio" name="add-field-field-required" value="true" checked="checked" class="add-field-field-required-yes">Yes</span>
												<span style="float:left; margin-left:.5em;"><input type="radio" name="add-field-field-required" value="false" style="float:left;">No</span>
											</td>
										</tr>
										<!-- New Field Required Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('select weather this field should be required or not','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- New Field Visibility -->
										<tr valign="top">
											<th scope="row"><label for="add-field-public"><?php _e('Visible?','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<span style="float:left;"><input type="radio" name="add-field-public" value="true" checked="checked" style="float:left;" class="add-field-public-yes">Yes</span>
												<span style="float:left; margin-left:.5em;"><input type="radio" name="add-field-public" value="false" style="float:left;">No</span>
											</td>
										</tr>
										<!-- New Field Visibility Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('select weather this field should be visible.','yikes-inc-easy-mailchimp-extender'); ?><br />
												<em style="display:block;margin-top:.5em;"><?php _e('note: the form cannot be set to required and hidden.','yikes-inc-easy-mailchimp-extender'); ?></em>
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
										<!-- New Field Phone Format -->
										<tr valign="top" class="yks-mc-add-field-phoneformat">
											<th scope="row"><label for="add-field-public"><?php _e('Phone Format','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<select name="add-field-phoneformat" id="add-field-phoneformat">
													<option value="US" class="option-phoneformat">US - (xxx)xxx-xxxx</option>
													<option value="International" class="option-phoneformat">International - unformatted </option>
												</select>
											</td>
										</tr>
										<!-- New Field Phone Format -->
										<tr class="yks-mc-add-field-phoneformat">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('"US" is the default - "International" will cause the phone number to be unformatted.','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- Options (radio/dropdown) -->
										<tr valign="top" class="yks-mc-add-field-radio-dropdown">
											<th scope="row"><label for="add-field-public"><?php _e('Options','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" name="radio-dropdown-option[]" class="radio-dropdown-option first">
												<a href="#" class="yks-mc-add-new-radio-dropdown-option button-secondary" style="margin-top:-1px;" onclick="yikesMCCloneInputField(jQuery(this)); return false;">+</a>
											</td>
										</tr>
										<!-- Options (radio/dropdown) Description -->
										<tr class="yks-mc-add-field-radio-dropdown">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('add options for the user to select from.','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Add Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
				 </p>
			</div>