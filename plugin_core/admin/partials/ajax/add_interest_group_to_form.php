<?php
	// lets run an ajax request to get all of our field data, to either prepopulate
	// or build our default selection arrays etc.
	$api_key = get_option( 'yikes-mc-api-key' , '' );
	$MailChimp = new MailChimp( $api_key );
	// get the interest group data
	try {
		$interest_groupings = $MailChimp->call( 'lists/interest-groupings' , array( 'apikey' => $api_key , 'id' => $form_data_array['list_id'] ) );
		if( $interest_groupings ) {
			// find and return the location of this merge field in the array
			$index = $this->findMCListIndex( $form_data_array['group_id'] , $interest_groupings , 'id' );
			// check for our index...
			if( isset( $index) ) {
				// store it and use it to pre-populate field data (only on initial add to form)
				$merge_field_data = $interest_groupings[$index];
			}
		}	
	} catch( Exception $error ) {
		$no_interest_groupings = $error->getMessage();
	}
?>
<section class="draggable" id="<?php echo $form_data_array['group_id']; ?>">
	<!-- top -->
	<a href="#" class="expansion-section-title settings-sidebar">
		<span class="dashicons dashicons-plus" title="<?php _e( 'Expand Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></span><?php echo stripslashes( $form_data_array['field_name'] ); ?>
		<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $form_data_array['field_type']; ?></small></span>
	</a>
	<!-- expansion section -->
	<div class="yikes-mc-settings-expansion-section">
					
		<!-- Single or Double Optin -->
		<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->
			<!-- store the label -->
			<input type="hidden" name="field[<?php echo $form_data_array['group_id']; ?>][label]" value="<?php echo $form_data_array['field_name']; ?>" />
			<input type="hidden" name="field[<?php echo $form_data_array['group_id']; ?>][type]" value="<?php echo $form_data_array['field_type']; ?>" />
			<input type="hidden" name="field[<?php echo $form_data_array['group_id']; ?>][group_id]" value="<?php echo $form_data_array['group_id']; ?>" />
			<input type="hidden" name="field[<?php echo $form_data_array['group_id']; ?>][groups]" value='<?php echo str_replace( '\'' , '~' , json_encode( $merge_field_data['groups'] ) ); ?>' />
	
				
			<table class="form-table form-field-container">
			
				<!-- Default Value -->
				<?php switch( $form_data_array['field_type'] ) { 
						
						default:
						case 'radio':
						?>
							<tr valign="top">
								<td scope="row">
									<label for="placeholder">
										<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									</label>
								</td>
								<td>
									<?php $i = 0; foreach( $merge_field_data['groups'] as $interest_group ) { 
											$pre_selected = !empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
									?>
										<input type="radio" name="field[<?php echo $form_data_array['group_id']; ?>][default_choice][]" value="<?php echo $i; ?>" <?php checked( $pre_selected , $i ); ?>><?php echo stripslashes( $interest_group['name'] ); ?>
									<?php 
										$i++;
										} 
									?>
									<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
								</td>
							</tr>
											
							<?php
							break;
						
						case 'checkboxes':
						?>
							<tr valign="top">
								<td scope="row">
									<label for="placeholder">
										<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									</label>
								</td>
								<td>
									<?php $i = 0; foreach( $merge_field_data['groups'] as $interest_group ) { 
											$pre_selected = !empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
									?>
										<input type="checkbox" name="field[<?php echo $form_data_array['group_id']; ?>][default_choice][]" value="<?php echo $i; ?>" <?php checked( $pre_selected , $i ); ?>><?php echo stripslashes( $interest_group['name'] ); ?>
									<?php 
										$i++;
										} 
									?>
									<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
								</td>
							</tr>
											
							<?php
							break;
									
						case 'dropdown':
							?>
							<tr valign="top">
								<td scope="row">
									<label for="placeholder">
										<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									</label>
								</td>
								<td>
									<select type="default" name="field[<?php echo $form_data_array['group_id']; ?>][default_choice]">
										<?php $i = 0; foreach( $merge_field_data['groups'] as $interest_group ) { 
												$pre_selected = !empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
										?>
											<option value="<?php echo $i; ?>" <?php selected( $pre_selected , $i ); ?>><?php echo stripslashes( $interest_group['name'] ); ?></option>
										<?php $i++; } ?>
									</select>
									<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
								</td>
							</tr>
									
						<?php
							break;
						?>
									
					<?php } // end switch field type ?>
				
				<!-- Field Description -->
				<tr valign="top">
					<td scope="row">
						<label for="placeholder">
							<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
						</label>
					</td>
					<td>
						<textarea class="widefat field-description-input" name="field[<?php echo $form_data_array['group_id']; ?>][description]"></textarea>
						<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
					</td>
				</tr>
				
				<!-- Additional Classes -->
				<tr valign="top">
					<td scope="row">
						<label for="placeholder">
							<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
						</label>
					</td>
					<td>
						<input type="text" class="widefat" name="field[<?php echo $form_data_array['group_id']; ?>][additional-classes]" value="<?php echo isset( $form_data_array['classes'] ) ? stripslashes( wp_strip_all_tags( $form_data_array['classes'] ) ) : '' ; ?>" />
						<p class="description"><small><?php printf( __( "Assign additional classes to this field. %s.", 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/bundled-css-classes/' ) . '">' . __( 'View bundled classes', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );?></small></p>
					</td>
					</tr>
					<!-- Required Toggle -->
					<tr valign="top">
						<td scope="row">
							<label for="field-required">
								<?php _e( 'Field Required?' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input type="checkbox" class="widefat" value="1" name="field[<?php echo $form_data_array['group_id']; ?>][require]">
							<p class="description"><small><?php _e( "Require this field to be filled in before the form can be submitted.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>
					<!-- Visible Toggle -->
					<tr valign="top">
						<td scope="row">
							<label for="hide-field">
								<?php _e( 'Hide Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input type="checkbox" class="widefat" value="1" name="field[<?php echo $form_data_array['group_id']; ?>][hide]">
							<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>
					<!-- Toggle Field Label Visibility -->
					<tr valign="top">
						<td scope="row">
							<label for="placeholder">
								<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="field[<?php echo $form_data_array['group_id']; ?>][hide-label]" value="1" />
							<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>
					<!-- Toggle Buttons -->
					<tr valign="top">
						<td scope="row">
							&nbsp;
						</td>
						<td>
							<span class="toggle-container">
								<a href="#" class="hide-field"><?php _e( "Close" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |
								<a href="#" class="remove-field" alt="<?php echo $form_data_array['group_id']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							</span>
						</td>
					</tr>
			</table>
		</p>		
												
	</div>
</section>