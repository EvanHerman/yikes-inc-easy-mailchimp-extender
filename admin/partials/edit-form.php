<div class="wrap">
	<?php

	// if an error was returned in the most recent query
	if( isset( $_GET['sql_error'] ) ) {
		// if error logging is turned on, lets display a better error to help narrow things down
		// lets also log things to the error log
		if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
			wp_die( '<strong>' . __( 'Error Creating Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong> <p>' . stripslashes( urldecode( $_GET['sql_error'] ) ) . '</p>' , __( 'Error Creating Form' , 'yikes-inc-easy-mailchimp-extender' ) );
		} else {
			wp_die( '<strong>' . __( 'Error Creating Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong><p>' . __( "Please try again. If the error persists please get in contact with the YIKES Inc. support team." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>' );
		}
	}

	/* Get The Form ID we need to edit */
	if ( ! isset( $_GET['id'] ) ) {
		wp_die( __( 'Oh No!', 'yikes-inc-easy-mailchimp-extender' ), __( 'Error', 'yikes-inc-easy-mailchimp-extender' ) );
	}

	// grab and store the form ID
	$form_id = (int) $_GET['id'];

	// Get our form interface.
	$form_interface = yikes_easy_mailchimp_extender_get_form_interface();

	// return it as an array, so we can work with it to build our form below
	$form = $form_interface->get_form( $form_id );

	// Get all results for our form switcher
	$all_forms = $form_interface->get_all_forms();

	// if the form was not found return an error
	if ( empty( $form ) ) {
		wp_die( printf( __( "Whoops! It looks like this form doesn't exist. If this error persists you may want to toggle on debugging on the <a href='%s'>%s</a> " , 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings' ) ), __( 'debug settings page' , 'yikes-inc-easy-mailchimp-extender' ) ), __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) );
	}

	// set global form data, mainly for use in custom form field declarations
	$GLOBALS["form_data"] = $form;

	$custom_styles       = $form['custom_styles'];
	$optin_settings      = $form['optin_settings'];
	$submission_settings = $form['submission_settings'];
	$error_messages      = $form['error_messages'];

	if ( isset( $form['form_settings'] ) ) {
		$form_settings = $form['form_settings'];
	}

	// get defaults if none are saved in the database yet
	if ( empty( $form_settings ) ) {
		// setup defaults if none are saved
		$form_settings = array(
			'yikes-easy-mc-form-class-names'                 => '',
			'yikes-easy-mc-inline-form'                      => '0',
			'yikes-easy-mc-submit-button-type'               => 'text',
			'yikes-easy-mc-submit-button-text'               => __( 'Submit', 'yikes-inc-easy-mailchimp-extender' ),
			'yikes-easy-mc-submit-button-image'              => '',
			'yikes-easy-mc-submit-button-classes'            => '',
			'yikes-easy-mc-form-schedule'                    => '0',

			// current date & time
			'yikes-easy-mc-form-restriction-start'           => strtotime( current_time( 'm/d/Y g:iA' ) ),

			// current date & time + 1 day
			'yikes-easy-mc-form-restriction-end'             => strtotime( current_time( 'm/d/Y g:iA' ) ) + ( 3600 * 24 ),
			'yikes-easy-mc-form-restriction-pending-message' => sprintf( __( 'Signup is not yet open, and will be available on %s. Please come back then to signup.', 'yikes-inc-easy-mailchimp-extender' ), current_time( str_replace( '-', '/', get_option( 'date_format' ) ) ) . ' ' . __( 'at', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . current_time( 'g:iA' ) ),
			'yikes-easy-mc-form-restriction-expired-message' => sprintf( __( 'This signup for this form ended on %s.', 'yikes-inc-easy-mailchimp-extender' ), date( str_replace( '-', '/', get_option( 'date_format' ) ), strtotime( current_time( str_replace( '-', '/', get_option( 'date_format' ) ) ) ) + ( 3600 * 24 ) ) . ' ' . __( 'at', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . date( 'g:iA', strtotime( current_time( 'g:iA' ) ) + ( 3600 * 24 ) ) ),
			'yikes-easy-mc-form-login-required'              => '0',
			'yikes-easy-mc-form-restriction-login-message'   => __( 'You need to be logged in to sign up for this mailing list.', 'yikes-inc-easy-mailchimp-extender' ),
		);
	}

	// Set up our list_handler object.
	$list_handler = yikes_get_mc_api_manager()->get_list_handler();

	// Check for a transient, if not - set one up for one hour
	$list_data = $list_handler->get_lists();
	if ( is_wp_error( $list_data ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$list_data->get_error_code(),
			__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
			"Edit Form Page"
		);
		$list_data = array();
	}

	// Get the merge fields
	$available_merge_variables = $list_handler->get_merge_fields( $form['list_id'] );
	if ( is_wp_error( $available_merge_variables ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$available_merge_variables->get_error_code(),
			__( "Get Merge Variables", 'yikes-inc-easy-mailchimp-extender' ),
			"Edit Form Page"
		);
		$available_merge_variables = array();
	}

	// get the interest group data
	$interest_groupings = $list_handler->get_interest_categories( $form['list_id'] );
	if ( is_wp_error( $interest_groupings ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$interest_groupings->get_error_code(),
			__( 'Get Interest Groups', 'yikes-inc-easy-mailchimp-extender' ),
			'Edit Form Page'
		);
		$interest_groupings = array();
	}

	/* Build Our Update Form URL */
	// create a custom URL to allow for creating fields
	$url = esc_url_raw(
		add_query_arg(
			array(
				'action' => 'yikes-easy-mc-update-form',
				'nonce' => wp_create_nonce( 'update-mailchimp-form'.-$form['id'] )
			)
		)
	);
	/* Display Our Form */
	?>
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php __( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1>YIKES Easy Forms for MailChimp | <?php echo __( 'Edit' , 'yikes-inc-easy-mailchimp-extender' ) . ' ' . $form['form_name']; ?></h1>

	<!-- Settings Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Update this MailChimp form\'s fields, styles and settings below.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<?php
	if ( isset( $_REQUEST['updated-form'] ) && $_REQUEST['updated-form'] == 'true' ) {
		?>
		<div class="updated manage-form-admin-notice">
			<p><?php _e( 'Opt-in form successfully updated.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
	}
	// hooks to allow extensions to display notices
	do_action( 'yikes-mailchimp-edit-form-notice' );
	?>

	<div id="poststuff">
		<!-- BEGIN UPDATE FORM -->
		<form id="edit-yikes-mc-form" method="POST" action="<?php echo $url; ?>">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox yikes-easy-mc-postbox">
						<div class="inside">

							<label for="form-name">
								<h3 class="bg-transparent"><?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
								<input autocomplete="disabled" id="form-name" name="form-name" type="text" value="<?php echo stripslashes( esc_html( $form['form_name'] ) ); ?>" class="widefat" />
								<p class="description"><?php _e( "The title of this signup form." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
							</label>

							<label for="form-description">
								<h3 class="bg-transparent"><?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
								<textarea name="form-description" id="form-description" class="large-text edit-form-form-description"><?php echo isset( $form['form_description'] ) ? stripslashes( esc_textarea( $form['form_description'] ) ) : ''; ?></textarea>
								<p class="description"><?php _e( "Descriptions are optional and you may choose to display it to visitors to your site." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								<p class="description"><?php printf( __( 'To display the number of subscribers for the list associated with this form, use %s in the form description field above.', 'yikes-inc-easy-mailchimp-extender' ), '<code>[yikes-mailchimp-subscriber-count]</code>' ); ?><p>
							</label>

						</div>
						<!-- .inside -->
					</div>
					<!-- .postbox -->
				</div>
				<!-- .meta-box-sortables .ui-sortable -->

				<!-- TOOLBAR -->
					<div id="yikes_easy_mc_toolbar">

						<ul id="yikes_easy_mc_toolbar_links">
							<li class="hidden_setting_list">
								<a class="hidden_setting form-builder selected_hidden_setting" data-attr-container="form-builder" onclick="return false;" title="<?php esc_attr_e( 'Customize Form Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?>" href="#"> <?php _e( 'Form Builder' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
								<div class="selected_setting_triangle"></div>
							</li>
							<li class="hidden_setting_list">
								<a class="hidden_setting form-settings" onclick="return false;" data-attr-container="form-settings" title="<?php esc_attr_e( 'Form Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>" href="#"> <?php _e( 'Form Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							</li>
							<li class="hidden_setting_list">
								<a class="hidden_setting error-messages" onclick="return false;" data-attr-container="error-messages" title="<?php esc_attr_e( 'Customize Form Messages' , 'yikes-inc-easy-mailchimp-extender' ); ?>" href="#"> <?php _e( 'Custom Messages' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							</li>
							<?php do_action( 'yikes-mailchimp-edit-form-section-links' , $form ); ?>
						</ul>

					</div>
				<!-- END TOOLBAR -->

				<div class="meta-box-sortables ui-sortable" id="hidden-option-data-container">
					<div class="postbox yikes-easy-mc-postbox">

						<div class="inside">

							<!-- Form Builder Label -->
							<label for="form" class="hidden-setting-label" id="form-builder">

								<div id="poststuff">
									<div id="post-body" class="metabox-holder columns-2">
										<!-- main content -->
										<div id="post-body-content">
											<div class="meta-box-sortables ui-sortable">
												<div class="postbox yikes-easy-mc-postbox">
													<!-- container title -->
													<h3 class="edit-form-title" id="form-builder-div" data-list-id="<?php echo $form['list_id'] ?>" ><?php _e( 'Form Builder' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
													<p id="edit-form-description" class="description edit-form-description-form-builder"><?php _e( 'Click a field to show its advanced options or drag fields to re-arrange them. Click <span class="dashicons dashicons-edit"></span> to edit a field label. Make sure you hit "Update Form" to save all of your changes.' , 'yikes-inc-easy-mailchimp-extender' );?></p>
													<div id="form-builder-container" class="inside">
														<!-- #poststuff -->
														<?php echo $this->generate_form_editor( $form['fields'], $form['list_id'] , $available_merge_variables , isset( $interest_groupings ) ? $interest_groupings : array() ); ?>
													</div>

													<!-- Bulk Delete Form Fields -->
													<a href="#" class="clear-form-fields" <?php if( isset( $form['fields'] ) && count( $form['fields'] ) <= 0 ) { ?> style="display:none;" <?php } ?>><?php _e( 'Clear Form Fields', 'yikes-inc-easy-mailchimp-extender' ); ?></a>

													<?php
														$display_none = ( isset( $form['fields'] ) && count( $form['fields'] ) <= 0 ) ? 'display:none;' : '';
													?>

													<!-- Save Fields Button -->
													<?php submit_button( __( 'Update Form' ) , 'primary' , '' , false , array( 'onclick' => '', 'style' => 'float:right;margin-right:12px;'.$display_none ) ); ?>

													<!-- .inside -->
												</div>
												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables .ui-sortable -->
										</div>
										<!-- post-body-content -->
										<!-- sidebar -->
										<div id="postbox-container-1" class="postbox-container">
											<div class="meta-box-sortables">
												<div class="postbox yikes-easy-mc-postbox">
													<h3 class="edit-form-title"><span><?php _e( "Form Fields &amp; Interest Groups" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
													<div class="inside">

														<h3 class="nav-tab-wrapper mv_ig_list">
															<a href="#" class="nav-tab nav-tab-active" alt="merge-variables"><div class="arrow-down"></div><?php _e( 'Form Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
															<?php if( !isset( $merge_variable_error ) ) { ?>
																<a href="#" class="nav-tab"><?php _e( 'Interest Groups' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
															<?php } else { ?>
																<a href="#" class="nav-tab no-interest-groups-found-message" disabled="disabled" title="<?php _e( "No Interest Groups Exist" , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Interest Groups' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
															<?php } ?>
														</h3>

														<div id="container-container">

															<div id="merge-variables-container" class="list-container">
																<?php
																	if( ! isset( $merge_variable_error ) ) {
																		// build a list of available merge variables,
																		// but exclude the ones already assigned to the form
																		echo '<p class="description">' . __( "Select the fields below to add to the form builder." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
																		$this->build_available_merge_vars( $form['fields'] , $available_merge_variables );
																	} else {
																		echo $merge_variable_error;
																	}
																?>
															</div>

															<div id="interest-groups-container" class="list-container">
																<?php
																	if( isset( $interest_groupings ) && ! isset( $interest_groupings['error'] ) ) {
																		// build a list of available merge variables,
																		// but exclude the ones already assigned to the form
																		echo '<p class="description">' . __( "Select an interest group below to add to the form builder." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
																		// $this->build_available_merge_vars( $form['fields'] , $available_merge_variables );
																		$this->build_available_interest_groups( $form['fields'] , $interest_groupings , $form['list_id'] );
																	} else {
																		echo '<p class="description">' . $interest_groupings['error'] . '</p>';
																	}
																?>
															</div>

														</div>

													</div>
													<!-- .inside -->
												</div>

												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables -->
										</div>
										<!-- #postbox-container-1 .postbox-container -->
									</div>
									<!-- #post-body .metabox-holder .columns-2 -->
									<br class="clear">
								</div>

							</label>
							<!-- End Form Builder Label -->

							<!-- Form Settings Customizations -->
							<label class="hidden-setting-label yikes-easy-mc-hidden" for="form" id="form-settings">

								<div id="poststuff">
									<div id="post-body" class="metabox-holder columns-2">
										<!-- main content -->
										<div id="post-body-content">
											<div class="meta-box-sortables ui-sortable">
												<div class="postbox yikes-easy-mc-postbox">
													<h3 class="edit-form-title"><span><?php _e( "Additional Form Settings" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>

													<div class="inside form-settings-container">

														<p class="edit-form-description"><?php _e( "Adjust some additional form settings below." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

														<!-- begin form classes section -->
														<strong class="section-title first"><?php _e( 'Overall Form Classes', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
														<section class="section-interior">

															<!-- form classes -->
															<label for="yikes-easy-mc-form-class-names"><strong><?php _e( 'Form Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																<input type="text" class="widefat" name="yikes-easy-mc-form-class-names" id="yikes-easy-mc-form-class-names" value="<?php echo $form_settings['yikes-easy-mc-form-class-names']; ?>" placeholder="<?php _e( 'Add additional classes to this opt-in form.', 'yikes-inc-easy-mailchimp-extender' ); ?>" >
																<p class="description"><?php printf( __( 'Add additional class names to the %s element.', 'yikes-inc-easy-mailchimp-extender' ), '<code>' . htmlentities( '<form>' ) . '</code>' ); ?></p>
															</label>

														</section>
														<!-- end form classes section -->

														<!-- begin form layout section -->
														<strong class="section-title"><?php _e( 'Form Layout', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
														<section class="section-interior">

															<!-- setup the checked state here -->
															<!-- inline form -->
															<strong><?php _e( 'Inline Form' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<label class="inline-form-label">
																<input type="radio" name="yikes-easy-mc-inline-form[]" value="1" <?php checked( $form_settings['yikes-easy-mc-inline-form'], '1' ); ?>/><?php _e( 'Enable', 'yikes-inc-easy-mailchimp-extender' ); ?>
															</label>
															<label class="inline-form-label">
																<input type="radio" name="yikes-easy-mc-inline-form[]" value="0" <?php checked( $form_settings['yikes-easy-mc-inline-form'], '0' ); ?> /><?php _e( 'Disable', 'yikes-inc-easy-mailchimp-extender' ); ?>
															</label>
															<p class="description"><?php _e( 'Programatically setup this form so that all fields are on the same line.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
															<p class="description"><?php printf( __( 'If you are having issues with your theme not displaying the inline form properly, please see the following %s.', 'yikes-inc-easy-mailchimp-extender' ), '<a href="https://yikesplugins.com/support/knowledge-base/my-form-fields-are-not-fully-inline-after-enabling-the-inline-form-option-how-come/" target="_blank">' . __( 'knowledge base article', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' ); ?></p>

														</section>
														<!-- end form layout section -->

														<!-- begin submit button section -->
														<strong class="section-title"><?php _e( 'Submit Button', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
														<section class="section-interior">

															<!-- Submit button type -->
															<strong><?php _e( 'Submit Button Type' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<label class="inline-form-label">
																<input type="radio" onclick="toggle_nested_section( jQuery(this) );" name="yikes-easy-mc-submit-button-type[]" value="text" <?php checked( $form_settings['yikes-easy-mc-submit-button-type'], 'text' ); ?> /><?php _e( 'Text', 'yikes-inc-easy-mailchimp-extender' ); ?>
															</label>
															<label class="inline-form-label">
																<input type="radio" onclick="toggle_nested_section( jQuery(this) );" name="yikes-easy-mc-submit-button-type[]" value="image" <?php checked( $form_settings['yikes-easy-mc-submit-button-type'], 'image' ); ?> /><?php _e( 'Image', 'yikes-inc-easy-mailchimp-extender' ); ?>
															</label>
															<p class="description"><?php _e( 'Select the submit button type for this form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
															<!-- end submit button type -->

																<!-- Text submit button type -->
																<section class="submit-button-type-text nested-child<?php if( $form_settings['yikes-easy-mc-submit-button-type'] == 'image' ) { echo ' hidden'; } ?>">
																	<!-- submit button text -->
																	<label for="yikes-easy-mc-submit-button-text"><strong><?php _e( 'Submit Button Text' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-submit-button-text" id="yikes-easy-mc-submit-button-text" value="<?php echo $form_settings['yikes-easy-mc-submit-button-text']; ?>" placeholder="<?php _e( 'Submit', 'yikes-inc-easy-mailchimp-extender' ); ?>">
																		<p class="description"><?php printf( __( 'Set the submit button text. Leaving this blank will default to %s.', 'yikes-inc-easy-mailchimp-extender' ), '"' . __( 'Submit', 'yikes-inc-easy-mailchimp-extender' ) . '"' ); ?></p>

																		<p class="description"><?php _e( 'The submit button text set above, can be overwritten on a per-form basis using shortcodes.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																	</label>
																</section>
																<!-- end text submit button type -->

																<!-- Image submit button type -->
																<section class="submit-button-type-image nested-child<?php if( $form_settings['yikes-easy-mc-submit-button-type'] == 'text' ) { echo ' hidden'; } ?>">
																	<label for="yikes-easy-mc-submit-button-image"><strong><?php _e( 'Submit Button URL' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-submit-button-image" id="yikes-easy-mc-submit-button-image" value="<?php echo $form_settings['yikes-easy-mc-submit-button-image']; ?>" placeholder="<?php _e( 'http://', 'yikes-inc-easy-mailchimp-extender' ); ?>">
																		<p class="description"><?php _e( 'Enter the URL of an image you would like to use as the submit button for this form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																	</label>
																</section>
																<!-- end image submit button type -->


															<!-- submit button classes -->
															<label for="yikes-easy-mc-form-submit-button-classes"><strong style="float:left;"><?php _e( 'Submit Button Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																<input type="text" class="widefat" name="yikes-easy-mc-submit-button-classes" id="yikes-easy-mc-submit-button-classes" value="<?php echo $form_settings['yikes-easy-mc-submit-button-classes']; ?>" placeholder="<?php _e( 'Add additional classes to this submit button.', 'yikes-inc-easy-mailchimp-extender' ); ?>" >
																<p class="description"><?php _e( 'Add custom classes to the submit button.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
															</label>
															<!-- end submit button classes -->

														</section>
														<!-- end submit button section -->

														<!-- begin restrictions section -->
														<strong class="section-title"><?php _e( 'Form Restrictions', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
														<section class="section-interior">
															<!-- Schedule form -->
															<label class="inline-form-label">
																<input type="checkbox" onclick="toggle_nested_section( jQuery(this) );" name="yikes-easy-mc-form-schedule" value="1" <?php checked( $form_settings['yikes-easy-mc-form-schedule'], '1' ); ?>/><?php _e( 'Schedule Form', 'yikes-inc-easy-mailchimp-extender' ); ?>
															</label>
															<p class="description" style="margin-bottom:0;"><?php _e( 'Set a time period that this form should be active on your site. (mm/dd/yyyy)', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
															<p class="description" style="margin: 0 0 .5em 0;"><?php _e( 'Once the end date & time have passed, users will no longer be able to signup for your mailing list.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																<!-- Start Date Limitation Nested -->
																<section class="date-restirction-section nested-child<?php if( $form_settings['yikes-easy-mc-form-schedule'] == '0' ) { echo ' hidden'; } ?>">
																	<!-- Start Date -->
																	<label for="yikes-easy-mc-form-restriction-start-date"><strong><?php _e( 'Start Date' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="date-picker" name="yikes-easy-mc-form-restriction-start-date" id="yikes-easy-mc-form-restriction-start-date" value="<?php echo date( $this->yikes_jQuery_datepicker_date_format( get_option( 'date_format' ) ), $form_settings['yikes-easy-mc-form-restriction-start'] ); ?>" >
																		<?php _e( 'at', 'yikes-inc-easy-mailchimp-extender' ); ?>
																	</label>

																	<!-- Start Time -->
																	<label for="yikes-easy-mc-form-restriction-start-time"><strong><?php _e( 'Start Time' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="time-picker" name="yikes-easy-mc-form-restriction-start-time" id="yikes-easy-mc-form-restriction-start-time" value="<?php echo date( 'g:iA', $form_settings['yikes-easy-mc-form-restriction-start'] ); ?>" >
																	</label>
																	<p class="description"><?php _e( 'Set the dates that this form should display on your site.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																</section>
																<!-- end Start Date Limitation Nested -->

																<!-- End Date Limitation Nested -->
																<section class="date-restirction-section nested-child<?php if( $form_settings['yikes-easy-mc-form-schedule'] == '0' ) { echo ' hidden'; } ?> last">
																	<!-- End Date -->
																	<label for="yikes-easy-mc-form-restriction-end-date"><strong><?php _e( 'End Date' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="date-picker" name="yikes-easy-mc-form-restriction-end-date" id="yikes-easy-mc-form-restriction-end-date" value="<?php echo date( $this->yikes_jQuery_datepicker_date_format( get_option( 'date_format' ) ), $form_settings['yikes-easy-mc-form-restriction-end'] ); ?>" >
																		<?php _e( 'at', 'yikes-inc-easy-mailchimp-extender' ); ?>
																	</label>

																	<!-- End Time -->
																	<label for="yikes-easy-mc-form-restriction-end-time"><strong><?php _e( 'End Time' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="time-picker" name="yikes-easy-mc-form-restriction-end-time" id="yikes-easy-mc-form-restriction-end-time" value="<?php echo date( 'g:iA', $form_settings['yikes-easy-mc-form-restriction-end'] ); ?>" >
																	</label>
																	<p class="description"><?php _e( 'Set the dates that this form should no longer display on your site.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>

																	<!-- Form pending message -->
																	<label for="yikes-easy-mc-form-restriction-pending-message"><strong><?php _e( 'Pending Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<?php
																			wp_editor(
																				$form_settings['yikes-easy-mc-form-restriction-pending-message'],
																				'yikes-easy-mc-form-restriction-pending-message',
																				array(
																					'editor_class' => 'yikes-easy-mc-form-restriction-pending-message',
																					'editor_css' => '<style>.yikes-easy-mc-form-restriction-pending-message{ max-height: 150px; }</style>'
																				)
																			);
																		?>
																	</label>
																	<p class="description"><?php _e( 'Set the message that should display prior to the form being active.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>

																	<!-- form expired message -->
																	<label for="yikes-easy-mc-form-restriction-expired-message"><strong><?php _e( 'Expired Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<?php
																			wp_editor(
																				$form_settings['yikes-easy-mc-form-restriction-expired-message'],
																				'yikes-easy-mc-form-restriction-expired-message',
																				array(
																					'editor_class' => 'yikes-easy-mc-form-restriction-expired-message',
																					'editor_css' => '<style>.yikes-easy-mc-form-restriction-expired-message{ max-height: 150px; }</style>'
																				)
																			);
																		?>
																	</label>
																	<p class="description"><?php _e( 'Set the message that should display once the end date has passed for this form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																</section>
																<!-- end End Date Limitation Nested -->

																<!-- Require Users to be Logged In -->
																<label class="inline-form-label">
																	<input type="checkbox" onclick="toggle_nested_section( jQuery(this) );" name="yikes-easy-mc-form-login-required" value="1" <?php checked( $form_settings['yikes-easy-mc-form-login-required'], '1' ); ?> /><?php _e( 'Require Login', 'yikes-inc-easy-mailchimp-extender' ); ?>
																</label>
																<p class="description"><?php _e( 'Require users to be logged in before they can view and submit this opt-in form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>

																<!-- Require Login Message -->
																<section class="login-restirction-section nested-child<?php if( $form_settings['yikes-easy-mc-form-login-required'] == '0' ) { echo ' hidden'; } ?>">
																	<label for="yikes-easy-mc-form-restriction-login-message"><strong><?php _e( 'Required Login Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<?php
																			wp_editor(
																				$form_settings['yikes-easy-mc-form-restriction-login-message'],
																				'yikes-easy-mc-form-restriction-login-message',
																				array(
																					'editor_class' => 'yikes-easy-mc-form-restriction-login-message',
																					'editor_css' => '<style>.yikes-easy-mc-form-restriction-login-message{ max-height: 150px; }</style>'
																				)
																			);
																		?>
																	</label>
																	<p class="description"><?php _e( 'Set the message that non-logged in users should see when viewing this form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																	<p class="description"><?php printf( __( 'To display a login form, use %s', 'yikes-inc-easy-mailchimp-extender' ), '<code>[login-form]</code>' ); ?></p>
																</section>

														</section>
														<!-- end restrictions section -->

													</div>

													<!-- .inside -->
												</div>
												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables .ui-sortable -->
										</div>
										<!-- post-body-content -->
										<!-- sidebar -->
										<div id="postbox-container-1" class="postbox-container">
											<div class="meta-box-sortables">
												<div class="postbox yikes-easy-mc-postbox">
													<h3 class="edit-form-title"><span><?php _e( "Form Settings Explained" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
													<div class="inside">

														<ul>
															<li><strong><?php _e( 'Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'Add additional classes to this form, allowing you to target it more easily for customization via CSS.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li><strong><?php _e( 'Form Layout' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'Toggle the layout of this form between single column and an inline layout. The inline layout places all of your form fields and the submit button on a single line.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li><strong><?php _e( 'Submit Button' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'Adjust setting specific to the submit button. Change the submit button text, or set it to a specified image. Use the "Submit Button Classes" to  assign additional classes to your submit button - ensuring it fits better into your theme.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li><strong><?php _e( 'Form Restrictions' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'Adjust the restrictions for this form. Limit form visibility to a given time period, require users to be logged in to sign up or combine the two!' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
														</ul>

													</div>
													<!-- .inside -->
												</div>
												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables -->
										</div>
										<!-- #postbox-container-1 .postbox-container -->
									</div>
									<!-- #post-body .metabox-holder .columns-2 -->
									<br class="clear">
								</div>

							</label>
							<!-- End Form Settings Customizations -->

							<!-- Error Messages -->
							<label class="hidden-setting-label yikes-easy-mc-hidden" for="form" id="error-messages">

								<div id="poststuff">
									<div id="post-body" class="metabox-holder columns-2">
										<!-- main content -->
										<div id="post-body-content">
											<div class="meta-box-sortables ui-sortable">
												<div class="postbox yikes-easy-mc-postbox">
													<h3 class="edit-form-title"><span><?php _e( "Custom Messages" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>

													<div class="inside error-message-container">
														<?php
															// build our default options
															$error_message_array = array(
																'success' => __( 'Thank You for subscribing! Check your email for the confirmation message.' , 'yikes-inc-easy-mailchimp-extender' ),
																'success-single-optin' => __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' ),
																'success-resubscribed' => __( 'Thank you for already being a subscriber! Your profile info has been updated.', 'yikes-inc-easy-mailchimp-extender' ),
																'general-error' => __( "Whoops! It looks like something went wrong. Please try again." , 'yikes-inc-easy-mailchimp-extender' ),
																'email-exists-error' => __( "The email you entered is already a subscriber to this list." , 'yikes-inc-easy-mailchimp-extender' ),
																'update-link' => __( "You're already subscribed. To update your MailChimp profile, please [link]click to send yourself an update link[/link].", 'yikes-inc-easy-mailchimp-extender' ),
																'email-subject' => __( 'MailChimp Profile Update', 'yikes-inc-easy-mailchimp-extender' ),

															);
															$global_error_messages = get_option( 'yikes-easy-mc-global-error-messages' , $error_message_array );
														?>
														<p class="edit-form-description"><?php _e( "Customize the response messages for this form. Leave the field blank to use the default message. The messages shown below depend on the Opt-in Settings chosen." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

														<!-- Success Message (refactored @ 6.3.0 for double optin) -->
														<label for="yikes-easy-mc-success-message"><strong><?php _e( 'Success: Double opt-in' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-success-message" id="yikes-easy-mc-success-message" value="<?php echo isset( $error_messages['success'] ) ? stripslashes( esc_html( $error_messages['success'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['success']; ?>" >
														</label>
														<!-- Success Message (for single optin) -->
														<label for="yikes-easy-mc-success-single-optin-message"><strong><?php _e( 'Success: Single opt-in' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-success-single-optin-message" id="yikes-easy-mc-success-single-optin-message" value="<?php echo isset( $error_messages['success-single-optin'] ) ? stripslashes( esc_html( $error_messages['success-single-optin'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['success-single-optin']; ?>" >
														</label>
														<!-- Resubscribing users when updating your profile via the form is allowed -->
														<label for="yikes-easy-mc-user-resubscribed-success-message"><strong><?php _e( 'Success: Re-subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-user-resubscribed-success-message" id="yikes-easy-mc-user-resubscribed-success-message" value="<?php echo isset( $error_messages['success-resubscribed'] ) ? stripslashes( esc_html( $error_messages['success-resubscribed'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['success-resubscribed']; ?>">
														</label>
														<!-- Click the link to update user profile etc. etc. -->
														<label for="yikes-easy-mc-user-subscribed-update-link"><strong><?php _e( 'Success: Re-subscriber with link to email profile update message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-user-update-link" id="yikes-easy-mc-user-update-link" value="<?php echo isset( $error_messages['update-link'] ) ? stripslashes( esc_html( $error_messages['update-link'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['update-link']; ?>">
														</label>
														<!-- Email Address is already subscribed -->
														<label for="yikes-easy-mc-user-subscribed-message"><strong><?php _e( 'Error: Re-subscribers not permitted' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-user-subscribed-message" id="yikes-easy-mc-user-subscribed-message" value="<?php echo isset( $error_messages['already-subscribed'] ) ? stripslashes( esc_html( $error_messages['already-subscribed'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['email-exists-error']; ?>">
														</label>
														<!-- General Error Message -->
														<label for="yikes-easy-mc-general-error-message"><strong><?php _e( 'Error: General' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
															<input type="text" class="widefat" name="yikes-easy-mc-general-error-message" id="yikes-easy-mc-general-error-message" value="<?php echo isset( $error_messages['general-error'] ) ? stripslashes( esc_html( $error_messages['general-error'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['general-error']; ?>" >
														</label>

														<!-- Email Section -->

														<hr>
														<div class="yikes-easy-mc-custom-messages-email-section">
															<p class="edit-form-description"><?php _e( 'Customize the profile verification email sent to re-subscribers. Leave the text unedited to use the default message.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
															<!-- Email Subject -->
															<label for="yikes-easy-mc-user-email-subject"><strong><?php _e( 'Email Subject' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																<input type="text" class="widefat" name="yikes-easy-mc-user-email-subject" id="yikes-easy-mc-user-email-subject" value="<?php echo isset( $error_messages['email-subject'] ) ? stripslashes( esc_html( $error_messages['email-subject'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['email-subject']; ?>">
															</label>
															<!-- Email Body -->
															<label for="yikes-easy-mc-user-email-body"><strong><?php _e( 'Email Body' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																<?php 
																	$editor_content = ( isset( $error_messages['email-body'] ) && ! empty( $error_messages['email-body'] ) ) ? $error_messages['email-body'] : Yikes_Inc_Easy_Mailchimp_Forms_Admin::generate_default_email_body();
																	wp_editor( $editor_content, 'yikes-easy-mc-user-email-body', array( 'textarea_id' => 'yikes-easy-mc-user-email-body' ) ); 
																?>
															</label>
														</div>
													</div>

													<!-- .inside -->
												</div>
												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables .ui-sortable -->
										</div>
										<!-- post-body-content -->
										<!-- sidebar -->
										<div id="postbox-container-1" class="postbox-container yikes-easy-mc-custom-messages-section-help">
											<div class="meta-box-sortables">
												<div class="postbox yikes-easy-mc-postbox">
													<h3 class="edit-form-title"><span><?php _e( "Custom Message Help" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
													<div class="inside">
														<ul>
															<li class="yikes-easy-mc-success-message-help"><strong><?php _e( 'Success: Double opt-in' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed after a double opt-in form has been submitted.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-success-single-optin-message-help"><strong><?php _e( 'Success Message: Single opt-in' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed after a single opt-in form has been submitted.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-user-resubscribed-success-message-help"><strong><?php _e( 'Success: Re-subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed after a subscriber submits a form for a list they are already subscribed to.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-user-subscribed-update-link-help"><strong><?php _e( 'Success: Re-subscriber with link to email profile update message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed after a subscriber submits a form for a list they are already subscribed to. Wrap the text you want to be the link in <code>[link][/link]</code> tags.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-user-subscribed-message-help"><strong><?php _e( 'Error: Re-subscribers not permitted' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed after a subscriber tries to join a list they are already subscribed to. You can display the user\'s email in the message  using an <code>[email]</code> tag.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-general-error-message-help"><strong><?php _e( 'Error: General' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The message displayed if a form error has occurred.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
														</ul>

													</div>
													<!-- .inside -->
												</div>

												<div class="postbox yikes-easy-mc-postbox yikes-easy-mc-custom-messages-email-section-help">
													<h3 class="edit-form-title"><span><?php _e( "Email Message Help" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
													<div class="inside">

														<ul>
															<li class="yikes-easy-mc-user-email-subject-help"><strong><?php _e( 'Email Subject' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php _e( 'The subject of the email sent to the user.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
															<li class="yikes-easy-mc-user-email-body-help"><strong><?php _e( 'Email Body' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> <?php echo sprintf( __( 'The text in the profile update verification email sent to the subscriber. Wrap the text you want to be the link in <code>[link][/link]</code> tags. The link is required in the email, please don\'t leave these tags out. You can also use <code>[url]</code> tag to display your website\'s URL (e.g. %s).', 'yikes-inc-easy-mailchimp-extender' ), get_home_url() ); ?></li>
														</ul>

													</div>
													<!-- .inside -->
												</div>





												<!-- .postbox -->
											</div>
											<!-- .meta-box-sortables -->
										</div>
										<!-- #postbox-container-1 .postbox-container -->
									</div>
									<!-- #post-body .metabox-holder .columns-2 -->
									<br class="clear">
								</div>

							</label>
							<!-- End Error Messages -->

							<?php do_action( 'yikes-mailchimp-edit-form-sections' , $form ); ?>

						</div>
						<!-- .inside -->
					</div>
					<!-- .postbox -->
				</div>
				<!-- .meta-box-sortables .ui-sortable -->
			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container  yikes-easy-forms-sidebar">
				<div class="meta-box-sortables">
					<div class="postbox yikes-easy-mc-postbox">
						<h3><span><?php _e( 'Form Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>

						<div class="inside">
								<p class="inside-section-1">
									<label for="shortcode"><?php _e( 'Edit Another Form' , 'yikes-inc-easy-mailchimp-extender' ); ?><br />
										<select class="widefat" name="form_switcher" id="form_switcher" onchange="YIKES_Easy_MC_SwitchForm(jQuery(this).val());">
											<?php foreach( $all_forms as $single_form ) { ?>
												<option <?php selected( $form_id , $single_form['id'] ); ?>value="<?php echo $single_form['id']; ?>"><?php echo $single_form['form_name']; ?></option>
											<?php } ?>
										</select>
									</label>
								</p>

								<p class="inside-section-2">
									<label for="shortcode"><?php _e( 'Shortcode' , 'yikes-inc-easy-mailchimp-extender' ); ?><br />
										<input type="text" onclick="this.setSelectionRange(0, this.value.length)" class="widefat shortcode-input-field" readonly value='[yikes-mailchimp form="<?php echo $form['id']; ?>"]' />
									</label>
								</p>


							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus yikes-mc-expansion-toggle"></span><?php _e( 'Associated List Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</a>
							<div class="yikes-mc-settings-expansion-section">
								<!-- Associated List -->
								<p class="form-field-container">
									<!-- necessary to prevent skipping on slideToggle(); -->
									<label for="associated-list"><strong><?php _e( 'Associated List' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										<select name="associated-list" id="associated-list" <?php if ( empty( $list_data ) ) { echo 'disabled="disabled"'; } ?> onchange="jQuery('.view-list-link').attr( 'href', '<?php echo esc_url( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' ) ); ?>' + jQuery( this ).val() );">
											<?php
											if ( ! empty( $list_data ) ) {
												foreach( $list_data as $mailing_list ) {
													?>
													<option <?php selected( $form['list_id'] , $mailing_list['id'] ); ?> value="<?php echo $mailing_list['id']; ?>"><?php echo stripslashes( $mailing_list['name'] ) . ' (' . $mailing_list['stats']['member_count'] . ') '; ?></option>
													<?php
												}
											} else {
												?>
												<option value="no-forms"><?php _e( 'No Lists Found' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
												<?php
											}
											?>
										</select>
										<?php if( ! empty( $form['list_id'] ) ) { ?>
											<p class="description view-list">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $form['list_id'] ) ); ?>" class="view-list-link"><?php _e( 'View List', 'yikes-inc-easy-mailchimp-extender' ); ?></a>
											</p>
											<p class="description">
												<?php _e( "Users who sign up via this form will be added to the list selected above." , 'yikes-inc-easy-mailchimp-extender' ); ?>
											</p>
										<?php } else { ?>
											<p class="description">
												<?php _e( "It looks like you first need to create a list to assign this form to. Head over to" , 'yikes-inc-easy-mailchimp-extender' ); ?> <a href="http://www.MailChimp.com" title="<?php _e( 'Create a new list' , 'yikes-inc-easy-mailchimp-extender' ); ?>">MailChimp</a> <?php _e( 'to create your first list' , 'yikes-inc-easy-mailchimp-extender' ); ?>.
											</p>
										<?php } ?>

										<!-- Display our Clear API Cache button -->
										<?php if ( false === get_transient( 'yikes-easy-mailchimp-list-data' ) && false === get_transient( 'yikes-easy-mailchimp-profile-data' ) && false === get_transient( 'yikes-easy-mailchimp-account-data' ) && false === get_transient( 'yikesinc_eme_list_ids' ) && false === get_transient( 'yikes_eme_lists' ) ) { ?>
											<p><a href="#" class="button-secondary" disabled="disabled" title="<?php _e( 'No MailChimp data found in temporary cache storage.' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Clear MailChimp API Cache' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
										<?php } else { ?>
											<p><a href="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-clear-transient-data' , 'nonce' => wp_create_nonce( 'clear-mc-transient-data' ) ) ) ); ?>" class="button-primary"><?php _e( 'Clear MailChimp API Cache' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
										<?php } ?>
									</label>
								</p>
							</div>

							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus yikes-mc-expansion-toggle"></span><?php _e( 'Opt-in Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</a>
							<div class="yikes-mc-settings-expansion-section">

								<!-- Single or Double Opt-in -->
								<?php
									if( !isset( $optin_settings['optin'] ) ) {
										$optin_settings['optin'] = '1';
									}
								?>
								<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="single-double-optin"><strong><?php _e( 'Single or Double Opt-in' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="edit-form-field-container-span">
										<label for="single"><input id="single" type="radio" name="single-double-optin" value="0" <?php checked( $optin_settings['optin'] , '0' ); ?>><?php _e( 'Single' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="double"><input id="double" type="radio" name="single-double-optin" value="1" <?php checked( $optin_settings['optin'] , '1' ); ?>><?php _e( 'Double' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<p class="description"><?php _e( "Double opt-in requires users to confirm their email address before being added to a list (recommended)" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</label></p>

								<!-- Update Existing Users -->
								<?php
									if( !isset( $optin_settings['update_existing_user'] ) ) {
										$optin_settings['update_existing_user'] = '1';
									}
								?>
								<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="update-existing-user"><strong><?php _e( 'Update Existing Subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="update-user"><input type="radio" id="update-user" onchange="toggleUpdateEmailContainer(this);return false;" name="update-existing-user" value="1" <?php checked( $optin_settings['update_existing_user'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="do-not-update-user"><input type="radio" onchange="toggleUpdateEmailContainer(this);return false;" id="do-not-update-user"  name="update-existing-user" value="0" <?php checked( $optin_settings['update_existing_user'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<p class="description"><?php printf( __( "Update an existing subscriber's profile information instead of displaying a %s message." , "yikes-inc-easy-mailchimp-extender" ), __( '"user already subscribed"', 'yikes-inc-easy-mailchimp-extender' ) ); ?></p>
								</label></p>

								<!--
									Send Update Profile Email
									- Yes = send an update email
									- No = Just update the user profile without an email
								-->
								<?php
									$send_update_email = ( isset( $optin_settings['send_update_email'] ) && '' !== $optin_settings['send_update_email'] ) ? $optin_settings['send_update_email'] : 0;
								?>
								<p class="form-field-container send-update-email" <?php if ( 1 !== absint( $optin_settings['update_existing_user'] ) ) { ?>style="display:none;"<?php } ?>><!-- necessary to prevent skipping on slideToggle(); --><label for="update-existing-user"><strong><?php _e( 'Send Update Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="update-email"><input type="radio" id="update-email" name="update-existing-email" value="1" <?php checked( $send_update_email , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="do-not-update-email"><input type="radio" id="do-not-update-email"  name="update-existing-email" value="0" <?php checked( $send_update_email , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<em><?php printf( __( "Send an email to the user granting their permission to update their profile information. Otherwise, an existing subscriber filling out this form, will have their profile information updated without any further interaction." , "yikes-inc-easy-mailchimp-extender" ), __( '"user already subscribed"', 'yikes-inc-easy-mailchimp-extender' ) ); ?></em>
								</label></p>

							</div>

							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus yikes-mc-expansion-toggle"></span><?php _e( 'Submission Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							</a>
							<div class="yikes-mc-settings-expansion-section">
								<!-- AJAX form Submission -->
								<?php
									if( !isset( $submission_settings['ajax'] ) ) {
										$submission_settings['ajax'] = '1';
									}
								?>
								<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="form-ajax-submission"><strong><?php _e( 'Enable AJAX Submission' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="enable-ajax"><input type="radio" id="enable-ajax" name="form-ajax-submission" class="yikes-enable-disable-ajax" value="1" <?php checked( $submission_settings['ajax'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="disable-ajax"><input type="radio" id="disable-ajax"  name="form-ajax-submission" class="yikes-enable-disable-ajax" value="0" <?php checked( $submission_settings['ajax'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<p class="description"><?php _e( "AJAX form submissions transmit data without requiring the page to refresh." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</label></p>

								<!-- Redirect User On Submission -->
								<?php
									if( !isset( $submission_settings['redirect_on_submission'] ) ) {
										$submission_settings['redirect_on_submission'] = '0';
										$submission_settings['redirect_page'] = '';
									}
								?>
								<p><label for="redirect-user-on-submission"><strong><?php _e( 'Redirect On Submission' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="redirect-user"><input type="radio" id="redirect-user" onclick="togglePageRedirection( this );" name="redirect-user-on-submission" value="1" <?php checked( $submission_settings['redirect_on_submission'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="do-not-redirect-user"><input type="radio" id="do-not-redirect-user" onclick="togglePageRedirection( this );" name="redirect-user-on-submission" value="0" <?php checked( $submission_settings['redirect_on_submission'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<?php $this->generate_page_redirect_dropdown( $submission_settings['redirect_on_submission'] , $submission_settings['redirect_page'], ( isset( $submission_settings['custom_redirect_url'] ) ) ? esc_url( $submission_settings['custom_redirect_url'] ) : '' ); ?>
									<p class="description"><?php _e( "When the user signs up would you like to redirect them to another page?" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</label></p>

								<?php
									if ( ! isset( $submission_settings['redirect_new_window'] ) ) {
										$submission_settings['redirect_new_window'] = '0';
									}
								?>

								<!-- Option to open the redirect URL in a new window -->
								<div class="redirect-new-window-div" <?php if ( ( ! isset( $submission_settings['redirect_on_submission'] ) || $submission_settings['redirect_on_submission'] === '0' ) || ( ! isset( $submission_settings['ajax'] ) || $submission_settings['ajax'] !== '1' )  ) { echo 'style="display:none;"'; } ?>>
										<p><strong><?php _e( "Open Redirect URL in a New Window" , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>
										<label for="redirect-new-window-yes">
											<input type="radio" class="widefat custom-redirect-new-window" id="redirect-new-window-yes" name="redirect_new_window" value="1" <?php checked( $submission_settings['redirect_new_window'], '1' ); ?>/><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
										</label>
										&nbsp;
										<label for="redirect-new-window-no">
											<input type="radio" class="widefat redirect-new-window" id="redirect-new-window-no" name="redirect_new_window" value="0" <?php checked( $submission_settings['redirect_new_window'] , '0' ); ?>/><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?>
										</label>
										<p class="description"><?php _e( "Should the redirect URL open in a new window/tab?" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</div>

								<!-- Hide Form On Submission -->
								<?php
									if( !isset( $submission_settings['hide_form_post_signup'] ) ) {
										$submission_settings['hide_form_post_signup'] = '0';
									}
								?>
								<p><label for="hide-form-post-signup"><strong><?php _e( 'Hide Form After Sign Up' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="hide-form"><input type="radio"  id="hide-form" name="hide-form-post-signup" value="1" <?php checked( $submission_settings['hide_form_post_signup'] , '1' ); ?> checked><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="do-not-hide-form"><input type="radio" id="do-not-hide-form" name="hide-form-post-signup" value="0" <?php checked( $submission_settings['hide_form_post_signup'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>
									<p class="description"><?php _e( "Should the form be hidden after the user successfully signs up?" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</label></p>

								<!-- Append or Replace Interest Groups -->
								<?php
									if( !isset( $submission_settings['replace_interests'] ) ) {
										$submission_settings['replace_interests'] = '1'; // defaults to true
									}
								?>
								<p><label for="replace-interest-groups"><strong><?php _e( 'Existing Interest Groups' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<span class="form-field-container-span">
										<label for="replace-interest-groups"><input type="radio"  id="replace-interest-groups" name="replace-interest-groups" value="1" <?php checked( $submission_settings['replace_interests'] , '1' ); ?> checked><?php _e( 'Replace' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
										&nbsp;<label for="update-interest-groups"><input type="radio" id="update-interest-groups" name="replace-interest-groups" value="0" <?php checked( $submission_settings['replace_interests'] , '0' ); ?>><?php _e( 'Update' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
									</span>

										<p class="description"><small><?php _e( "<strong>Replace</strong>: Replace all interest groups with the new ones submitted." , 'yikes-inc-easy-mailchimp-extender' ); ?></small></p>


										<p class="description"><small><?php _e( "<strong>Update</strong>: Update <em>only</em> the ones submitted. Leave existing interest groups as is." , 'yikes-inc-easy-mailchimp-extender' ); ?></small></p>

								</label></p>

							</div>

						</div>
						<!-- .inside -->

							<span class="spinner update-form-spinner"></span>

							<span class="form-buttons-container" id="major-publishing-actions">
								<?php
									echo submit_button( __( 'Update Form' ) , 'primary' , '' , false , array( 'onclick' => 'jQuery(this).parent().prev().css({"display":"block","visibility":"inherit"});' ) );
									$url = esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-delete-form', 'mailchimp-form' => $form['id'] , 'nonce' => wp_create_nonce( 'delete-mailchimp-form-'.$form['id'] ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) );
									echo '<a href="' . $url . '" class="yikes-delete-mailchimp-form" onclick="return confirm(\'' . __( "Are you sure you want to delete this form? This cannot be undone." , 'yikes-inc-easy-mailchimp-extender' ) . '\');">' . __( "Delete Form" , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
								?>
							</span>

					</div>
					<!-- .postbox -->
					<?php
						// display info about Yikes
						echo $this->generate_show_some_love_container();
					?>

				</div>
				<!-- .meta-box-sortables -->
			</div>
			<!-- #postbox-container-1 .postbox-container -->
		</div>
		<!-- #post-body .metabox-holder .columns-2 -->
		<br class="clear">
		</form> <!-- END UPDATE FORM -->
	</div>
	<!-- #poststuff -->

</div>
