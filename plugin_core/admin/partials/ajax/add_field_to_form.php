<?php
	// lets run an ajax request to get all of our field data, to either prepopulate
	// or build our default selection arrays etc.
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
		<span class="dashicons dashicons-plus" title="<?php _e( 'Expand Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></span><?php echo stripslashes( $form_data_array['field_name'] ); ?>
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
				<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][choices]" value='<?php echo json_encode( stripslashes_deep( $merge_field_data['choices'] ) ); ?>' />
			<?php } ?>
				
			<table class="form-table form-field-container">
			
					<!-- Merge Tag -->
					<tr valign="top">
						<td scope="row">
							<label for="merge-tag">
								<?php _e( 'Merge Tag' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input class="widefat merge-tag-text" type="text" readonly value="<?php echo $merge_field_data['tag']; ?>">
						</td>
					</tr>

			<?php switch( $form_data_array['field_type'] ) {
					
					default:
						break;
						
					case 'text':
					case 'number':
					case 'url':
					case 'email':
					case 'address':
					case 'phone':
					case 'birthday':
					case 'zip':
			?>
					<!-- Placeholder -->
					<tr valign="top">
						<td scope="row">
							<label for="placeholder">
								<?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?> 
							</label>
						</td>
						<td>
						<input type="text" class="widefat" name="field[<?php echo $merge_field_data['tag']; ?>][placeholder]" value="<?php echo isset( $merge_field_data['placeholder'] ) ? stripslashes( wp_strip_all_tags( $merge_field_data['placeholder'] ) ): '' ; ?>" />
							<p class="description"><small><?php _e( "Assign a placeholder value to this field.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>
			
			<?php 
					/* 
					*	Loop over field types and store necessary formats
					*	( date, birthday - dateformat ; phone - phoneformat )
					*/
					switch( $form_data_array['field_type'] ) {
												
						/* Store the date format, for properly rendering dates on the front end */
						case 'date':
							$date_format = ( isset( $merge_field_data['dateformat'] ) ) ? $merge_field_data['dateformat'] : 'MM/DD/YYYY';
							?>
								<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][date_format]" value="<?php echo strtolower( $merge_field_data['dateformat'] ); ?>" />
							<?php
						break;
						
						case 'birthday':
							$date_format = ( isset( $merge_field_data['dateformat'] ) ) ? $merge_field_data['dateformat'] : 'MM/DD';
							?>
								<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][date_format]" value="<?php echo strtolower( $date_format ); ?>" />
							<?php
						break;
						
						/* Store the phone format, for properly regex pattern */
						case 'phone':
							?>
								<input type="hidden" name="field[<?php echo $merge_field_data['tag']; ?>][phone_format]" value="<?php echo $merge_field_data['phoneformat']; ?>" />
							<?php
						break;
					}
					
					break;
					
				}
			?>
			
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
								<?php 
								switch( $form_data_array['field_type'] ) { 
									case 'text':
										?>
											<p><small class="pre-defined-tag-link"><a href="#TB_inline?width=600&height=550&inlineId=pre-defined-tag-container" class="thickbox" onclick="storeGlobalClicked( jQuery( this ) );"><?php _e( 'View Pre-Defined Tags' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></p>
										<?php
									break;
								} ?>
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
										<label>
											<input type="radio" name="field[<?php echo $merge_field_data['tag']; ?>][default_choice]" value="<?php echo $value; ?>" <?php checked( $pre_selected , $choice ); ?>><?php echo stripslashes( $value ); ?>
										</label>
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
												$pre_selected = ! empty( $merge_field_data['default_choice'] ) ? $merge_field_data['default_choice'] : '0';
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
				
				<!-- Field Description -->
				<tr valign="top">
					<td scope="row">
						<label for="placeholder">
							<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
						</label>
					</td>
					<td>
						<textarea class="widefat field-description-input" name="field[<?php echo $merge_field_data['tag']; ?>][description]"></textarea>
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
						<input type="text" class="widefat" name="field[<?php echo $merge_field_data['tag']; ?>][additional-classes]" value="<?php echo isset( $form_data_array['classes'] ) ? stripslashes( wp_strip_all_tags( $form_data_array['classes'] ) ) : '' ; ?>" />
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
					<!-- Toggle Field Label Visibility -->
					<tr valign="top">
						<td scope="row">
							<label for="placeholder">
								<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="field[<?php echo $merge_field_data['tag']; ?>][hide-label]" value="1" />
							<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>
					<!-- Display Phone/Date Formats back to the user -->
					<?php 
						switch( $form_data_array['field_type'] ) {
							
							/* Store the phone format, for properly regex pattern */
							case 'phone':
							case 'birthday':
							case 'date':
							?>
								<tr valign="top">
									<td scope="row">
										<label for="placeholder">		
										<?php 
											switch( $form_data_array['field_type'] ) {
												default:
												case 'birthday':
													$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
													$format = $merge_field_data['dateformat'];
													break;
													
												case 'date':
													$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
													$format = $merge_field_data['dateformat'];
													break;
																				
												case 'phone':
													$type = __( 'Phone Format' , 'yikes-inc-easy-mailchimp-extender' );
													$format = ( ( $merge_field_data['phoneformat'] == 'none' ) ? __( 'International', 'yikes-inc-easy-mailchimp-extender' ) : $merge_field_data['phoneformat'] );
													break;
											}
											echo $type;
										?>
										</label>
									</td>
									<td>
										<strong><?php echo $format; ?></strong>
										<p class="description"><small>
											<?php printf( __( 'To change the %s please head over to <a href="%s" title="MailChimp" target="_blank">MailChimp</a>. If you alter the format, you should re-import this field.', 'yikes-inc-easy-mailchimp-extender' ), strtolower( $type ), esc_url( 'http://www.mailchimp.com' ) ); ?>
										</small></p>
									</td>
								</tr>
							<?php
							break;
							// others..
							default:
							break;
						}
					?>
										<!-- End Date/Phone Formats -->
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