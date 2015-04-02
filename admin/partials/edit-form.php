<div class="wrap">

	<?php
		/* Get The Form ID we need to edit */
		if( isset( $_GET['id'] ) ) {
			global $wpdb;
			$form_id = $_GET['id'];
			// return it as an array, so we can work with it to build our form below
			$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form_id . '', ARRAY_A );
			// Get all results for our form switcher
			$all_forms = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
			if( !$form_results ) {
				wp_die( __( "Whoops! It looks like this form doesnt exist..." , $this->text_domain ) , __( 'Error' , $this->text_domain ) );
			}
			
			// store our results
			$form = $form_results[0];
			$custom_styles = json_decode( $form['custom_styles'] , true );
			$optin_settings = json_decode( $form['optin_settings'] , true );
			$submission_settings = json_decode( $form['submission_settings'] , true );
			$error_messages = json_decode( $form['error_messages'] , true );
			
			// Check for a transient, if not - set one up for one hour
			if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {
				// initialize MailChimp Class
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// retreive our list data
				$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ) ) );
				// set our transient
				set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );
			}
		} else {
			wp_die( __( 'Oh No!' , $this->text_domain ) , __( 'Error' , $this->text_domain ) );
		}
		
		/* Confirm we've retreived our form data */
		if( empty( $form ) ) { 
		
			wp_die( __( "We've encountered an error. Please try again. If the error persists, please contact support." , $this->text_domain ) , __( 'Error' , $this->text_domain ) );
		
		} else {
		
			/* Build Our Update Form URL */
			// create a custom URL to allow for creating fields
			$url = add_query_arg(
				array(
					'action' => 'yikes-easy-mc-update-form',
					'nonce' => wp_create_nonce( 'update-mailchimp-form'.-$form['id'] )
				)
			);
			/* Display Our Form */
			?>
				<!-- Freddie Logo -->
				<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="Freddie - MailChimp Mascot" style="float:left;margin-right:10px;" />
					
				<h2>Easy MailChimp by Yikes Inc. | <?php echo __( 'Edit' , $this->text_domain ) . ' ' . $form['form_name']; ?></h2>		
				
				<?php // print_r( $form ); ?>
				
				<!-- Settings Page Description -->
				<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Update this MailChimp forms settings and front end styles below.' , $this->text_domain ); ?></p>
				
				<?php
				if( isset( $_REQUEST['updated-form'] ) && $_REQUEST['updated-form'] == 'true' ) {
					?>
					<div class="updated manage-form-admin-notice">
						<p><?php _e( 'MailChimp Form successfully updated.', $this->text_domain ); ?></p>
					</div>
					<?php
				}
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
											<h3 class="bg-transparent"><?php _e( 'Form Name' , $this->text_domain ); ?></h3>
											<input autocomplete="disabled" id="form-name" name="form-name" type="text" value="<?php echo $form['form_name']; ?>" class="widefat" />
											<p class="description"><?php _e( "The title of this signup form." , $this->text_domain ); ?></p>
										</label>
										
										<label for="form-description">
											<h3 class="bg-transparent"><?php _e( 'Form Description' , $this->text_domain ); ?></h3>
											<textarea name="form-description" id="form-description" class="large-text" style="width:100%;resize:vertical;min-height:65px;max-height:100px;"><?php echo isset( $form['form_description'] ) ? $form['form_description'] : ''; ?></textarea>
											<p class="description"><?php _e( "Descriptions are optional and you may choose to display it to the user on the frontend." , $this->text_domain ); ?></p>
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
											<a class="hidden_setting form-builder selected_hidden_setting" onclick="return false;" title="<?php __( 'Customize Form Fields' , $this->text_domain ); ?>" href="#" alt="form-builder-label"> <?php _e( 'Form Builder' , $this->text_domain ); ?></a>
											<div class="selected_setting_triangle"></div>
										</li>	
										<li class="hidden_setting_list">
											<a class="hidden_setting form-customizer" onclick="return false;" title="<?php __( 'Customize Form Appearance' , $this->text_domain ); ?>" href="#" alt="form-customizer-label"> <?php _e( 'Form Customizer' , $this->text_domain ); ?></a>
										</li>
										<li class="hidden_setting_list">
											<a class="hidden_setting error-messages" onclick="return false;" title="<?php _e( 'Customize Form Error Messages' , $this->text_domain ); ?>" href="#" alt="form-error-messages"> <?php _e( 'Error Messages' , $this->text_domain ); ?></a>
										</li>					
									</ul>
									
								</div>
							<!-- END TOOLBAR -->
							
							<div class="meta-box-sortables ui-sortable" id="hidden-option-data-container">

								<div class="postbox yikes-easy-mc-postbox">
									
									
									<div class="inside">
									
										<!-- Form Builder Label -->										
										<label for="form" class="hidden-setting-label" id="form-builder-label">
										
											<div id="poststuff">
												<div id="post-body" class="metabox-holder columns-2">
													<!-- main content -->
													<div id="post-body-content">
														<div class="meta-box-sortables ui-sortable">
															<div class="postbox yikes-easy-mc-postbox">
																<!-- container title -->
																<h3 style="padding-left:12px;"><?php _e( 'Form Builder' , $this->text_domain ); ?></h3>
																<p  style="padding-left:12px;margin.75em 0;" class="description"><small><?php _e( 'Select a form field from the right to add to this form. Once added to the form, you can click it to reveal advanced options for the field, or drag it to re-arrange the position in the form.' , $this->text_domain );?></small></p>
																<div id="form-builder-container" class="inside">
																	<!-- #poststuff -->
																	<?php echo $this->generate_form_editor( json_decode( $form['fields'] , true ) , $form['list_id'] ); ?>
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
																<h3 style="padding-left:12px;"><span><?php _e( "Merge Variables &amp; Interest Groups" , $this->text_domain ); ?></span></h3>
																<div class="inside">
																	<?php
																		try {
																			$api_key = get_option( 'yikes-mc-api-key' , '' );
																			$MailChimp = new MailChimp( $api_key );
																			// retreive our list data
																			$available_merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $form['list_id'] ) ) );
																			// build a list of available merge variables,
																			// but exclude the ones already assigned to the form
																			echo '<p class="description">' . __( "Select a field below to add to the form builder to construct your form." , $this->text_domain ) . '</p>';
																			$this->build_available_merge_vars( json_decode( $form['fields'] , true ) , $available_merge_variables );
																		} catch ( Exception $e ) {
																			return __( 'Error' , $this->text_domain ) . ' : ' . $e->getMessage();
																		}
																	?>
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
										
										<!-- Form Customizer -->										
										<label for="form" class="hidden-setting-label" id="form-customizer-label" style="display:none;">
										
											<div id="poststuff">
												<div id="post-body" class="metabox-holder columns-2">
													<!-- main content -->
													<div id="post-body-content">
														<div class="meta-box-sortables ui-sortable">
															<div class="postbox yikes-easy-mc-postbox">
																<h3 style="padding-left:12px;"><span><?php _e( "Form Customizer" , $this->text_domain ); ?></span></h3>
																<div class="inside">
																	
																<!--
																<form>	
																	<textarea class="yikes-easy-mc-form-customizer"></textarea>
																</form>
																-->
																Form Preview Here....
																
																
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
																<h3 style="padding-left:12px;"><span><?php _e( "Style Adjustments" , $this->text_domain ); ?></span></h3>
																<div class="inside">
																	
																	<label for="custom-styles" style="display:block;margin-top:1.75em;"><?php _e( 'Enable Custom Styles' , $this->text_domain ); ?>	
																		&nbsp;<input type="checkbox" name="custom-styles" id="custom-styles" value="1" <?php checked( $custom_styles['active'] , 1 ); ?> onchange="jQuery('#style-list').slideToggle();" />
																		<p class="description" style="margin-top:.5em;"><?php _e( 'enable custom styles for this form by checking off the field above.' , $this->text_domain ); ?></p>
																	</label>
																																		
																	<ul id="style-list" <?php if( $custom_styles['active'] == 0 ) { echo 'style="display:none;"'; } ?>>
																		<hr style="margin:1.25em 0;" />
																		<li>
																			<label for="color-test"><?php _e( 'Form Background' , $this->text_domain ); ?>
																				<input type="text" name="form-background-color" value="<?php echo $custom_styles['background_color']; ?>" class="color-picker" />
																			</label>
																		</li>
																		<li>
																			<label for="color-test"><?php _e( 'Font Color' , $this->text_domain ); ?>
																				<input type="text" name="form-font-color" value="<?php echo $custom_styles['font_color']; ?>" class="color-picker" />
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Submit Button Background Color' , $this->text_domain ); ?>
																				<input type="text" name="form-submit-button-color" value="<?php echo $custom_styles['submit_button_color']; ?>" class="color-picker" />
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Submit Button Text Color' , $this->text_domain ); ?>
																				<input type="text" name="form-submit-button-text-color" value="<?php echo $custom_styles['submit_button_text_color']; ?>" class="color-picker" />
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Form Padding' , $this->text_domain ); ?> <small>(px, em, rem, %)</small>
																				<input type="text" name="form-padding" value="<?php echo $custom_styles['form_padding']; ?>" class="form-style-adjustment" />
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Form Width' , $this->text_domain ); ?> <small>(px, em, rem, %)</small>
																				<input type="text" name="form-width" value="<?php echo $custom_styles['form_width']; ?>" class="form-style-adjustment" />
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Form Alignment' , $this->text_domain ); ?>
																				<select name="form-alignment" style="display:block;width:100%;margin:.5em 0;">
																					<option value="none" <?php selected( $custom_styles['form_alignment'] , 'none' ); ?>><?php _e( "None" , $this->text_domain ); ?></option>
																					<option value="left" <?php selected( $custom_styles['form_alignment'] , 'left' ); ?>><?php _e( "Left" , $this->text_domain ); ?></option>
																					<option value="center" <?php selected( $custom_styles['form_alignment'] , 'center' ); ?>><?php _e( "Center" , $this->text_domain ); ?></option>
																					<option value="right" <?php selected( $custom_styles['form_alignment'] , 'right' ); ?>><?php _e( "Right" , $this->text_domain ); ?></option>
																				</select>
																			</label>
																		</li>
																		<li>	
																			<label for="color-test"><?php _e( 'Label Visibility' , $this->text_domain ); ?>
																				<select name="label-visible" style="display:block;width:100%;margin:.5em 0;">
																					<option value="visible" <?php selected( $custom_styles['label_visible'] , 'visible' ); ?>><?php _e( "Visible" , $this->text_domain ); ?></option>
																					<option value="hidden" <?php selected( $custom_styles['label_visible'] , 'hidden' ); ?>><?php _e( "Hidden" , $this->text_domain ); ?></option>
																				</select>
																			</label>
																		</li>
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
										<!-- End Form Customizer -->
										
										<!-- Error Messages -->										
										<label class="hidden-setting-label" for="form" id="form-error-messages" style="display:none;">
										
											<div id="poststuff">
												<div id="post-body" class="metabox-holder columns-2">
													<!-- main content -->
													<div id="post-body-content">
														<div class="meta-box-sortables ui-sortable">
															<div class="postbox yikes-easy-mc-postbox">
																<h3 style="padding-left:12px;"><span><?php _e( "Custom Error Messages" , $this->text_domain ); ?></span></h3>
																
																<div class="inside error-message-container">
																	<?php 
																		// build our default options
																		$error_message_array = array(
																			'success' => __( 'Thank You for subscribing! Check your email for the confirmation message.' , $this->text_domain ),
																			'general-error' => __( "Whoops! It looks like something went wrong. Please try again." , $this->text_domain ),
																			'invalid-email' => __( "Please provide a valid email address." , $this->text_domain ),
																			'email-exists-error' => __( "The provided email is already subscribed to this list." , $this->text_domain )
																		);
																		$global_error_messages = get_option( 'yikes-easy-mc-global-error-messages' , $error_message_array ); 
																	?>
																	<p class="description"><?php echo __( "Enter your custom error messages for this form below. Leave the field blank to use the global error message , set on the" , $this->text_domain ) . ' <a href="' . admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=form-settings' ) . '" title="' . __( 'Error Settings' , $this->text_domain ) . '">' . __( "error settings page" , $this->text_domain ) . '</a>.'; ?></p>
																	
																	<!-- Success Message -->
																	<label for="yikes-easy-mc-success-message"><strong><?php _e( 'Success Message' , $this->text_domain ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-success-message" id="yikes-easy-mc-success-message" value="<?php echo isset( $error_messages['success'] ) ? $error_messages['success'] : ''; ?>" placeholder="<?php echo $global_error_messages['success']; ?>" >
																	</label>	
																	<!-- General Error Message -->
																	<label for="yikes-easy-mc-general-error-message"><strong><?php _e( 'General Error Message' , $this->text_domain ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-general-error-message" id="yikes-easy-mc-general-error-message" value="<?php echo isset( $error_messages['general-error'] ) ? $error_messages['general-error'] : ''; ?>" placeholder="<?php echo $global_error_messages['general-error']; ?>" >
																	</label>
																	<!-- Invalid Email Address Message -->
																	<label for="yikes-easy-mc-invalid-email-message"><strong><?php _e( 'Invalid Email' , $this->text_domain ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-invalid-email-message" id="yikes-easy-mc-invalid-email-message" value="<?php echo isset( $error_messages['invalid-email'] ) ? $error_messages['invalid-email'] : ''; ?>" placeholder="<?php echo $global_error_messages['invalid-email']; ?>">
																	</label>	
																	<!-- Email Address is already subscribed -->
																	<label for="yikes-easy-mc-user-subscribed-message"><strong><?php _e( 'Email Already Subscribed' , $this->text_domain ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-user-subscribed-message" id="yikes-easy-mc-user-subscribed-message" value="<?php echo isset( $error_messages['subscribed-message'] ) ? $error_messages['subscribed-message'] : ''; ?>" placeholder="<?php echo $global_error_messages['email-exists-error']; ?>">
																	</label>	
																
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
																<h3 style="padding-left:12px;"><span><?php _e( "Error Message Sidebar Title" , $this->text_domain ); ?></span></h3>
																<div class="inside">
																	
																	<h4>Not sure...links to what messages get displayed when?</h4>
																	
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

									<h3><span><?php _e( 'Form Settings' , $this->text_domain ); ?></span></h3>
							
									<div class="inside">
											<p style="margin-top:0;margin-bottom:1.5em;">
												<label for="shortcode"><?php _e( 'Switch Forms' , $this->text_domain ); ?><br />
													<select class="widefat" name="form_switcher" id="form_switcher" onchange="YIKES_Easy_MC_SwitchForm(jQuery(this).val());">
														<?php foreach( $all_forms as $form ) { ?>
															<option <?php selected( $form_id , $form['id'] ); ?>value="<?php echo $form['id']; ?>"><?php echo $form['form_name']; ?></option>
														<?php } ?>
													</select>											
												</label>
											</p>
											
											<p style="margin-top:0;margin-bottom:2em;">
												<label for="shortcode"><?php _e( 'Shortcode' , $this->text_domain ); ?><br />
													<input type="text" class="widefat" disabled="disabled" value='[yikes-mailchimp form="<?php echo $form['id']; ?>"]' style="color:#333;" />												
												</label>
											</p>
										
											
										<a href="#" class="expansion-section-title settings-sidebar">
											<span class="dashicons dashicons-plus"></span><?php _e( 'Associated List Settings' , $this->text_domain ); ?>
										</a>
										<div class="yikes-mc-settings-expansion-section">
											<!-- Associated List -->
											<p style="margin-top:0;"><!-- necessary to prevent skipping on slideToggle(); --><label for="associated-list"><strong><?php _e( 'Associated List' , $this->text_domain ); ?></strong>
												<select name="associated-list" id="associated-list" style="width:100%;margin-top:5px;">
													<?php
														foreach( $list_data['data'] as $mailing_list ) {
															?>
																<option <?php selected( $form['list_id'] , $mailing_list['id'] ); ?>value="<?php echo $mailing_list['id']; ?>"><?php echo stripslashes( $mailing_list['name'] ) . ' (' . $mailing_list['stats']['member_count'] . ') '; ?></option>
															<?php
														}
													?>
												</select>
												<p class="description"><?php _e( "Users who sign up through this form will be added to the following list." , $this->text_domain ); ?></p>
											</label></p>
										</div>
										
										<a href="#" class="expansion-section-title settings-sidebar">
											<span class="dashicons dashicons-plus"></span><?php _e( 'Optin Settings' , $this->text_domain ); ?>
										</a>
										<div class="yikes-mc-settings-expansion-section">
											
											<!-- Single or Double Optin -->
											<p style="margin-top:0;"><!-- necessary to prevent skipping on slideToggle(); --><label for="single-double-optin"><strong><?php _e( 'Single or Double Opt-in' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" name="single-double-optin" value="1" <?php checked( $optin_settings['optin'] , '1' ); ?>><?php _e( 'Single' , $this->text_domain ); ?>
													&nbsp;<input type="radio" name="single-double-optin" value="0" <?php checked( $optin_settings['optin'] , '0' ); ?>><?php _e( 'Double' , $this->text_domain ); ?>
												</span>
												<p class="description"><?php _e( "Double opt-in requires users to confirm their email address before being added to a list (recommended)" , $this->text_domain ); ?></p>
											</label></p>
											
											<!-- Welcome Email -->
											<p style="margin-top:0;"><!-- necessary to prevent skipping on slideToggle(); --><label for="send-welcome-email"><strong><?php _e( 'Send Welcome Email' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" name="send-welcome-email" value="1" <?php checked( $optin_settings['send_welcome_email'] , '1' ); ?>><?php _e( 'Yes' , $this->text_domain ); ?>
													&nbsp;<input type="radio" name="send-welcome-email" value="0" <?php checked( $optin_settings['send_welcome_email'] , '0' ); ?>><?php _e( 'No' , $this->text_domain ); ?>
												</span>
												<p class="description"><?php _e( "When the user signs up, should they receive the default welcome email?" , $this->text_domain ); ?></p>
											</label></p>
											
											<!-- Update Existing Users -->
											<p style="margin-top:0;"><!-- necessary to prevent skipping on slideToggle(); --><label for="update-existing-user"><strong><?php _e( 'Update Existing Subscriber' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" name="update-existing-user" value="1" <?php checked( $optin_settings['update_existing_user'] , '1' ); ?>><?php _e( 'Yes' , $this->text_domain ); ?>
													&nbsp;<input type="radio" name="update-existing-user" value="0" <?php checked( $optin_settings['update_existing_user'] , '0' ); ?>><?php _e( 'No' , $this->text_domain ); ?>
												</span>
												<p class="description"><?php _e( 'Update an existing subscribers info when they attempt to re-subscribe instead of displayed "already subscribed" message.' , $this->text_domain ); ?></p>
											</label></p>
												
										</div>
										
										<a href="#" class="expansion-section-title settings-sidebar">
											<span class="dashicons dashicons-plus"></span><?php _e( 'Submission Settings' , $this->text_domain ); ?>
										</a>
										<div class="yikes-mc-settings-expansion-section">

											<!-- AJAX form Submission -->
											<p style="margin-top:0;"><!-- necessary to prevent skipping on slideToggle(); --><label for="form-ajax-submission"><strong><?php _e( 'Enable AJAX Submission' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" name="form-ajax-submission" value="1" <?php checked( $submission_settings['ajax'] , '1' ); ?>><?php _e( 'Yes' , $this->text_domain ); ?>
													&nbsp;<input type="radio" name="form-ajax-submission" value="0" <?php checked( $submission_settings['ajax'] , '0' ); ?>><?php _e( 'No' , $this->text_domain ); ?>
												</span>
												<p class="description"><?php _e( "AJAX form submissions transmit data while preventing the page from refreshing." , $this->text_domain ); ?></p>
											</label></p>
											
											<!-- Redirect User On Submission -->
											<p><label for="redirect-user-on-submission"><strong><?php _e( 'Redirect On Submission' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" onclick="togglePageRedirection( this );" name="redirect-user-on-submission" value="1" <?php checked( $submission_settings['redirect_on_submission'] , '1' ); ?>><?php _e( 'Yes' , $this->text_domain ); ?>
													&nbsp;<input type="radio" onclick="togglePageRedirection( this );" name="redirect-user-on-submission" value="0" <?php checked( $submission_settings['redirect_on_submission'] , '0' ); ?>><?php _e( 'No' , $this->text_domain ); ?>
												</span>
												<?php $this->generate_page_redirect_dropdown( $submission_settings['redirect_on_submission'] , $submission_settings['redirect_page'] ); ?>
												<p class="description"><?php _e( "When the user signs up, would you like to redirect them to another page?" , $this->text_domain ); ?></p>
											</label></p>
											
											<!-- Hide Form On Submission -->
											<p><label for="hide-form-post-signup"><strong><?php _e( 'Hide Form After Sign Up' , $this->text_domain ); ?></strong>
												<span style="display:block;margin:.5em 0;">
													<input type="radio" name="hide-form-post-signup" value="1" <?php checked( $submission_settings['hide_form_post_signup'] , '1' ); ?> checked><?php _e( 'Yes' , $this->text_domain ); ?>
													&nbsp;<input type="radio" name="hide-form-post-signup" value="0" <?php checked( $submission_settings['hide_form_post_signup'] , '0' ); ?>><?php _e( 'No' , $this->text_domain ); ?>
												</span>
												<p class="description"><?php _e( "Should the form be hidden after the user successfully signs up?" , $this->text_domain ); ?></p>
											</label></p>
											
										</div>
										
										<?php 
											echo submit_button( __( 'Update Form' ) , 'primary' , '' , false , array( 'style' => 'margin: 1.5em 0 .5em 0;' ) ); 
											$url = add_query_arg( array( 'action' => 'yikes-easy-mc-delete-form', 'mailchimp-form' => $form['id'] , 'nonce' => wp_create_nonce( 'delete-mailchimp-form-'.$form['id'] ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) );
											echo '<a href="' . $url . '" class="yikes-delete-mailchimp-form" style="display:inline-block;margin:2.25em 0 .5em 0;float:right;">' . __( "Delete Form" , $this->text_domain ) . '</a>';
										?>
										
									
										
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
					</form> <!-- END UPDATE FORM -->
				</div>
				<!-- #poststuff -->
				
			<?php
		}
	?>
	
</div>