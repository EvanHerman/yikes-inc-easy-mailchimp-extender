<?php
	/*
	* 	Hidden container section / content	
	*
	*	Lets run an ajax request to get all of our field data, to either pre-populate
	*	or build our default selection arrays etc.
	*
	*	@since 6.0.0
	*	Author: Yikes Inc. | https://www.yikesinc.com
	*/
	$api_key = get_option( 'yikes-mc-api-key' , '' );
	$MailChimp = new MailChimp( $api_key );
	// retreive our list data
	$available_merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $form_data_array['list_id'] ) ) );
	// find and return the location of this merge field in the array
	$index = $this->findMCListIndex( $form_data_array['merge_tag'] , $available_merge_variables['data'][0]['merge_vars'] , 'tag' );
	// store it and use it to pre-populate field data (only on initial add to form)
	$merge_field_data = $available_merge_variables['data'][0]['merge_vars'][$index];
?>
<section class="draggable" id="<?php echo $form_data_array['field_name']; ?>">
	<!-- top -->
	<a href="#" class="expansion-section-title settings-sidebar">
		<span class="dashicons dashicons-plus"></span><?php echo $form_data_array['field_name']; ?>
		<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $form_data_array['field_type']; ?></small></span>
	</a>
	<!-- expansion section -->
	<div class="yikes-mc-settings-expansion-section">
					
		<!-- Single or Double Optin -->
		<p class="type-container form-field-container"><!-- necessary to prevent skipping on slideToggle(); -->
			<!-- store the label -->
			<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][label]" value="<?php echo $form_data_array['field_name']; ?>" />
			<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][type]" value="<?php echo $form_data_array['field_type']; ?>" />
			<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][merge]" value="<?php echo $merge_field_data['tag']; ?>" />
			<input type="hidden" class="field-<?php echo $merge_field_data['tag']; ?>-position position-input" name="field[<?php echo $merge_field_data['tag']; ?>][position]" value="" />
			
			<?php if ( $form_data_array['field_type'] == 'radio' || $form_data_array['field_type'] == 'dropdown' ) { ?>
				<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][choices]" value='<?php echo stripslashes( json_encode( $merge_field_data['choices'] ) ); ?>' />
			<?php } ?>
				
			<table class="form-table form-field-container">
				<!-- Placeholder -->
				<tr valign="top">
					<td scope="row">
						<label for="placeholder">
							<?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?>
						</label>
					</td>
					<td>
					<input type="text" class="widefat" name="field[<?php echo $merge_field_data['tag']; ?>][placeholder]" value="<?php echo isset( $merge_field_data['placeholder'] ) ? stripslashes( wp_strip_all_tags( $merge_field_data['placeholder'] ) ) : '' ; ?>" />
						<p class="description"><small><?php _e( "Assign a placeholder value to this field.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
					</td>
				</tr>
				<!-- Default Value -->
				<?php switch( $form_data_array['field_type'] ) { 
						
						default:
						case 'text':
						?>
							<tr valign="top">
								<td scope="row">
									<label for="placeholder">
										<?php _e( 'Default Value' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									</label>
								</td>
							<td>
								<input type="text" class="widefat" name="field[<?php echo $merge_field_data['tag']; ?>][default]" <?php if( $form_data_array['field_type'] != 'url' ) { ?> value="<?php echo isset( $merge_field_data['default'] ) ? stripslashes( wp_strip_all_tags( $merge_field_data['default'] ) ) : ''; ?>" <?php } else { ?> value="<?php echo isset( $merge_field_data['default'] ) ? stripslashes( wp_strip_all_tags( esc_url_raw( $merge_field_data['default'] ) ) ) : ''; } ?>" />
								<p class="description"><small><?php _e( "Assign a default value to populate this field with on initial page load.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
							</td>
							</tr>
						<?php 
							break;
									
						case 'radio':
						?>
							<tr valign="top">
								<td scope="row">
									<label for="placeholder">
										<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									</label>
								</td>
								<td>
									<?php foreach( $merge_field_data['choices'] as $choice => $value ) { 
											$pre_selected = !empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
									?>
										<input type="radio" name="field[<?php echo $merge_field_data['tag']; ?>][default_choice]" value="<?php echo $choice; ?>" <?php checked( $pre_selected , $choice ); ?>><?php echo stripslashes( $value ); ?>
									<?php } ?>
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
									<select type="default" name="field[<?php echo $merge_field_data['tag']; ?>][default_choice]">
										<?php foreach( $merge_field_data['choices'] as $choice => $value ) { 
												$pre_selected = !empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
										?>
											<option value="<?php echo $choice; ?>" <?php selected( $pre_selected , $choice ); ?>><?php echo stripslashes( $value ); ?></option>
										<?php } ?>
									</select>
									<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
								</td>
							</tr>
									
						<?php
							break;
						?>
									
					<?php } // end switch field type ?>
					
				<!-- Additional Classes -->
				<tr valign="top">
					<td scope="row">
						<label for="placeholder">
							<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
						</label>
					</td>
					<td>
						<input type="text" class="widefat" name="field[<?php echo $merge_field_data['tag']; ?>][additional-classes]" value="<?php echo isset( $form_data_array['classes'] ) ? stripslashes( esc_html( $form_data_array['classes'] ) ) : '' ; ?>" />
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
							<input type="checkbox" class="widefat" value="1" name="field[<?php echo $merge_field_data['tag']; ?>][require]" <?php checked( $merge_field_data['req'] , 1 ); ?> <?php if( $merge_field_data['tag'] == 'EMAIL' ) {  ?> disabled="disabled" checked="checked" title="<?php echo __( 'Email is a required field.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
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
							<input type="checkbox" class="widefat" value="1" name="field[<?php echo $merge_field_data['tag']; ?>][hide]" <?php if( empty( $merge_field_data['show'] ) ) { echo 'checked="checked"'; } ?> <?php if( $merge_field_data['tag'] == 'EMAIL' ) {  ?> disabled="disabled" title="<?php echo __( 'Cannot toggle email field visibility.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
							<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
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
								<a href="#" class="remove-field" alt="<?php echo $merge_field_data['tag']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							</span>
						</td>
					</tr>
			</table>
		</p>		
												
	</div>
</section>