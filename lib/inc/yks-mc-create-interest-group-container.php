			<!--
				Main template file for our container which houses the form
				to create new interest groups on a given list
			-->
			<div id="newInterestGroupContianer" style="display:none;">
				 <p>
					 <h4 style="width:100%;text-align:center;" class="interest-group-field-title">Add an Interest Group</h4>
						<div id="interest-group-settings">
							<form method="post" name="yks-mailchimp-add-new-interest-group-form" id="yks-mailchimp-add-new-interest-group-form">
								<input type="hidden" name="mc-list-id" id="mc-list-id" value="">
								<table class="form-table" style="margin-top: 2em;">
									<tbody>
										<!-- New Interest Group Name -->
										<tr valign="top">
											<th scope="row"><label for="add-interest-group-name"><?php _e('Interest Group Label','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" id="add-interest-group--name" name="add-interest-group-name" style="width:100%;">
											</td>
										</tr>
										<!-- New Interest Group Name Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('enter the name of your new interest group','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- New Interest Group Type -->
										<tr valign="top">
											<th scope="row"><label for="add-interest-group-type"><?php _e('Display Type','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<span style="float:left;"><input type="radio" name="add-interest-group-type" value="checkboxes" checked="checked" class="add-field-field-required-yes yks-mc-input-type">Checkboxes</span>
												<span style="float:left; margin-left:.5em;"><input type="radio" name="add-interest-group-type" value="dropdown" style="float:left;" class="yks-mc-input-type">Dropdown</span>
												<span style="float:left; margin-left:.5em;"><input type="radio" name="add-interest-group-type" value="radio" style="float:left;" class="yks-mc-input-type">Radio Buttons</span>
												<span style="float:left; margin-left:.5em;"><input type="radio" name="add-interest-group-type" value="hidden" style="float:left;" class="yks-mc-input-type">Hidden</span>
											</td>
										</tr>
										<!-- New Interest Group Type Description -->
										<tr>
											<td></td>
											<td class="yks-settings-description">
												<?php _e('select which type of grouping this new interest group should be','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<!-- New Interest Group Options (if hidden is not selected) -->
										<tr valign="top" class="yks-mc-add-interest-group-radio-dropdown">
											<th scope="row"><label for="add-field-public"><?php _e('Group Names','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<input type="text" name="radio-dropdown-option[]" class="radio-dropdown-option first">
												<a href="#" class="yks-mc-add-new-radio-dropdown-option button-secondary" style="margin-top:-1px;" onclick="yikesMCCloneInputField(jQuery(this)); return false;">+</a>
											</td>
										</tr>
										<!-- New Interest Group Options (if hidden is not selected) -->
										<tr class="yks-mc-add-interest-group-radio-dropdown">
											<td></td>
											<td class="yks-settings-description">
												<?php _e('add options to your interest group','yikes-inc-easy-mailchimp-extender'); ?><br />
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Add Group' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
				 </p>
			</div>
			
			<div id="updateInterestGroupContianer" style="display:none;">
				<img class="yks-mc-preloader-update-interest-groups" src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" />
				 <p>
						<div id="update-interest-group-settings">
							<form method="post" name="yks-mailchimp-update-interest-group-form" id="yks-mailchimp-update-interest-group-form" style="opacity:.5; display:block;width:85%;margin:0 auto;">
								
								<input type="hidden" name="mc-list-id" id="mc-list-id" value="">
								<input type="hidden" name="grouping-id" id="grouping-id" value="">
								
								<span class="yks-mc-update-interest-group-header yks-mc-interest-group-header" style="position:relative;"> 
									<h3 style="float:left;" id="yks-mc-interest-group-title"></h3>  <span id="yks-mc-interest-group-type-dropdown" style="position:absolute;right:0;bottom:0;"><select id="yks-mc-interest-group-toggle-type"><option value="checkboxes">Checkboxes</option><option value="dropdown" disabled="disabled">Dropdown</option><option value="radio" disabled="disabled">Radio Buttons</option><option value="hidden">Hidden</option></select></span>
								</span>
								<hr />
								<span class="no-interest-group-options-found" style="display:none;display:block;width:100%;"><em>No interest group options set up</em></span>
								<div id="options-table">	
									<ul id="option-ul" style="display:block;width:100%;float:left;min-height:200px;">
										<li class="option-ul-title first" style="display:block;float:left;width:100%;margin-top:0;margin-bottom:0;"></li>
									</ul>
								</div>
								<a href="#" onclick="return false;" class="button-secondary add-another-interest-group-option" style="display:none;">+ <?php _e( 'Add New Option' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							</form>
						</div>
				 </p>
			</div>