<?php

// Set up our default $error values
$error_messages = '';
$error = 0;

// Get our $_POST variables
$list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
$interest_groups = isset( $_POST['interest_groups'] ) ? $_POST['interest_groups'] : array();

// Make sure our $_POST variables aren't empty
if ( empty( $list_id ) ) {
	$error = 1;
	$error_messages .= __( 'Could not find the list id. ', 'yikes-inc-easy-mailchimp-extender' );
}
if ( empty( $interest_groups ) ) {
	$error = 1;
	$error_messages .= __( 'Could not find interest group data. ', 'yikes-inc-easy-mailchimp-extender' );
}

// Get the list, interest groups
$list_helper = yikes_get_mc_api_manager()->get_list_handler();
$interest_groupings = $list_helper->get_interest_categories( $list_id );

// Make sure the interest groups aren't empty
if ( is_wp_error( $interest_groupings ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$interest_groupings->get_error_code(),
		__( "Get Interest Groups", 'yikes-inc-easy-mailchimp-extender' ),
		__( "Add Interest Group to Form", 'yikes-inc-easy-mailchimp-extender' )
	);
	$error = 1;
	$error_messages .= $interest_groupings->get_error_code();
}

// If we hit an error above at some point, then display the error(s) and return
if ( $error === 1 ) {
	$error_messages .= __( ' Please refresh and try again.', 'yikes-inc-easy-mailchimp-extender' );
	?>
	<section class="draggable" id="error-container">
		<p>
			<span class="dashicons dashicons-no-alt"></span> <?php printf( __( 'Error: %s', 'yikes-inc-easy-mailchimp-extender' ), $error_messages ); ?>
		</p>
	</section>
	<?php
	return;
}


// Loop through the interest groups data (group_id, field_name, field_type) and add the fields to the form
foreach( $interest_groups as $group ) {

	// find and return the location of this merge field in the array
	$index      = $this->findMCListIndex( $group['group_id'], $interest_groupings, 'id' );
	$field_data = $interest_groupings[ $index ];
	$groups     = wp_list_pluck( $field_data['items'], 'name' );

	?>
	<section class="draggable" id="<?php echo $group['group_id']; ?>">
		<!-- top -->
		<a href="#" class="expansion-section-title settings-sidebar">
			<span class="dashicons dashicons-plus yikes-mc-expansion-toggle" title="<?php _e( 'Expand Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></span>
			<?php echo stripslashes( $group['field_name'] ); ?>
			<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $group['field_type']; ?></small></span>
		</a>
		<!-- expansion section -->
		<div class="yikes-mc-settings-expansion-section">

			<!-- Single or Double Opt-in -->
			<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->
				<!-- store the label -->
				<input type="hidden" name="field[<?php echo $group['group_id']; ?>][label]" value="<?php echo htmlspecialchars( $group['field_name'] ); ?>" />
				<input type="hidden" name="field[<?php echo $group['group_id']; ?>][type]" value="<?php echo $group['field_type']; ?>" />
				<input type="hidden" name="field[<?php echo $group['group_id']; ?>][group_id]" value="<?php echo $group['group_id']; ?>" />
				<input type="hidden" name="field[<?php echo $group['group_id']; ?>][groups]" value='<?php echo esc_attr( json_encode( $groups, true ) ); ?>' />


				<table class="form-table form-field-container">

					<!-- Default Value -->
					<?php switch( $group['field_type'] ) {

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
									<?php
									foreach ( $field_data['items'] as $id => $interest_group ) {
										$pre_selected = ! empty( $field_data['default_choice'] ) ? $field_data['default_choice'] : '0';
										?>
										<input type="radio" name="field[<?php echo $group['group_id']; ?>][default_choice][]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $pre_selected, $id ); ?>><?php echo stripslashes( $interest_group['name'] ); ?>
										<?php

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
									<?php
									foreach ( $field_data['items'] as $id => $interest_group ) {
										$pre_selected = ! empty( $field_data['default_choice'] ) ? $field_data['default_choice'] : '0';
										?>
										<input type="checkbox" name="field[<?php echo $group['group_id']; ?>][default_choice][]" value="<?php echo $id; ?>" <?php checked( $pre_selected, $id ); ?>><?php echo stripslashes( $interest_group['name'] ); ?>
										<?php
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
									<select type="default" name="field[<?php echo $group['group_id']; ?>][default_choice]">
										<?php
										foreach ( $field_data['items'] as $id => $interest_group ) {
											$pre_selected = ! empty( $field_data['default_choice'] ) ? $field_data['default_choice'] : '0';
											?>
											<option value="<?php echo $id; ?>" <?php selected( $pre_selected, $id ); ?>><?php echo $interest_group['name']; ?></option>
											<?php
										} ?>
									</select>
									<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
								</td>
							</tr>

							<?php
							break;
						} ?>

					<!-- Field Description -->
					<tr valign="top">
						<td scope="row">
							<label for="placeholder">
								<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<textarea class="widefat field-description-input" name="field[<?php echo $group['group_id']; ?>][description]"></textarea>
							<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
						</td>
					</tr>

					<!-- Description Above Field -->
					<tr valign="top">
						<td scope="row">
							<label for="description_above_<?php echo esc_attr( $group['group_id'] ); ?>">
								<?php _e( 'Description Above Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</label>
						</td>
						<td>
							<input type="checkbox" id="description_above_<?php echo esc_attr( $group['group_id'] ); ?>" class="widefat field-description-input" name="field[<?php echo $group['group_id']; ?>][description_above]" value="1" />
							<span class="description"><small><?php _e( "By default the description will appear undearneath the field. Check this box if you'd like the description to appear above the field.", 'yikes-inc-easy-mailchimp-extender' );?></small></span>
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
							<input type="text" class="widefat" name="field[<?php echo $group['group_id']; ?>][additional-classes]" value="<?php echo isset( $group['classes'] ) ? stripslashes( wp_strip_all_tags( $group['classes'] ) ) : '' ; ?>" />
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
								<input type="checkbox" class="widefat" value="1" name="field[<?php echo $group['group_id']; ?>][require]">
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
								<input type="checkbox" class="widefat" value="1" name="field[<?php echo $group['group_id']; ?>][hide]">
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
								<input type="checkbox" name="field[<?php echo $group['group_id']; ?>][hide-label]" value="1" />
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
									<a href="#" class="remove-field" alt="<?php echo $group['group_id']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
								</span>
							</td>
						</tr>
				</table>
			</p>

		</div>
	</section>
<?php } // End our loop of interest group data ?>