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
		if( isset( $_GET['id'] ) ) {
			global $wpdb;
			// grab and store the form ID
			$form_id = (int) $_GET['id'];
			// return it as an array, so we can work with it to build our form below
			$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form_id . '', ARRAY_A );
			// Get all results for our form switcher
			$all_forms = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
						
			// if the form was not found return an error
			if( !$form_results ) {
				wp_die( printf( __( "Whoops! It looks like this form doesn't exist. If this error persists you may want to toggle on debugging on the <a href='%s'>%s</a> " , 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings' ) ), __( 'debug settings page' , 'yikes-inc-easy-mailchimp-extender' ) ), __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) );
			}
			
			// store our results
			$form = $form_results[0];
			// set global form data, mainly for use in custom form field declarations
			$GLOBALS["form_data"] = $form;

			$custom_styles = json_decode( $form['custom_styles'] , true );
			$optin_settings = json_decode( $form['optin_settings'] , true );
			$submission_settings = json_decode( $form['submission_settings'] , true );
			$error_messages = json_decode( $form['error_messages'] , true );
			$custom_notifications = json_decode( $form['custom_notifications'] , true );
			
			// Check for a transient, if not - set one up for one hour
			if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {
				// initialize MailChimp Class
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// retreive our list data
				$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ), 'limit' => 100 ) );
				// set our transient
				set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );
			}
			
			// get the list data
			try {
				$api_key = get_option( 'yikes-mc-api-key' , '' );
				$MailChimp = new MailChimp( $api_key );
				// retreive our list data
				$available_merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $form['list_id'] ) ) );
			} catch ( Exception $e ) {
				$merge_variable_error = '<p class="description error-descripion">' . __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $e->getMessage() . '.</p>';
				wp_die( __( "Uh Oh...It looks like we ran into an error! Please reload the page and try again. If the error persists, please contact the YIKES Inc. support team.", 'yikes-inc-easy-mailchimp-extender' ) , 500 );
			}
			
			// get the interest group data
			try {
				$interest_groupings = $MailChimp->call( 'lists/interest-groupings' , array( 'apikey' => $api_key , 'id' => $form['list_id'] ) );
				$no_interest_groupings = '<p class="description error-descripion">' . __( 'No Interest Groups Found' , 'yikes-inc-easy-mailchimp-extender' ) . '.</p>';
			} catch( Exception $error ) {
				$no_interest_groupings = '<p class="description error-descripion">' . $error->getMessage() . '.</p>';
			}
		} else {
			wp_die( __( 'Oh No!' , 'yikes-inc-easy-mailchimp-extender' ) , __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) );
		}
		
		/* Confirm we've retreived our form data */
		if( empty( $form ) ) { 
		
			wp_die( __( "We've encountered an error. Please try again. If the error persists, please contact support." , 'yikes-inc-easy-mailchimp-extender' ) , __( 'Error' , 'yikes-inc-easy-mailchimp-extender' ) );
		
		} else {
		
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
				if( isset( $_REQUEST['updated-form'] ) && $_REQUEST['updated-form'] == 'true' ) {
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
																<h3 class="edit-form-title"><?php _e( 'Form Builder' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
																<p id="edit-form-description" class="description"><?php _e( 'Select fields from the right to add to this form, you can click a field to reveal advanced options, or drag it to re-arrange its position in the form.' , 'yikes-inc-easy-mailchimp-extender' );?></p>
																<div id="form-builder-container" class="inside">
																	<!-- #poststuff -->
																	<?php echo $this->generate_form_editor( json_decode( $form['fields'] , true ) , $form['list_id'] , $available_merge_variables , isset( $interest_groupings ) ? $interest_groupings : array() ); ?>
																</div>
																																
																<!-- Bulk Delete Form Fields -->
																<a href="#" class="clear-form-fields" <?php if( isset( $form['fields'] ) && count( json_decode( $form['fields'] , true ) ) <= 0 ) { ?> style="display:none;" <?php } ?>><?php _e( 'Clear Form Fields', 'yikes-inc-easy-mailchimp-extender' ); ?></a>
																
																<?php 
																	$display_none = ( isset( $form['fields'] ) && count( json_decode( $form['fields'] , true ) ) <= 0 ) ? 'display:none;' : '';
																?>
																
																<!-- Save Fields Button -->
																<?php echo submit_button( __( 'Update Form' ) , 'primary' , '' , false , array( 'onclick' => '', 'style' => 'float:right;margin-right:12px;'.$display_none ) ); ?>
																
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
																					echo '<p class="description">' . __( "Select a field below to add to the form builder." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
																					$this->build_available_merge_vars( json_decode( $form['fields'] , true ) , $available_merge_variables );
																				} else {
																					echo $merge_variable_error;
																				}
																			?>
																		</div>
																		
																		<div id="interest-groups-container" class="list-container">
																			<?php
																				if( isset( $interest_groupings ) && count( $interest_groupings ) >= 1 ) {
																					// build a list of available merge variables,
																					// but exclude the ones already assigned to the form
																					echo '<p class="description">' . __( "Select an interest group below to add to the form builder." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
																					// $this->build_available_merge_vars( json_decode( $form['fields'] , true ) , $available_merge_variables );
																					$this->build_available_interest_groups( json_decode( $form['fields'] , true ) , $interest_groupings , $form['list_id'] );
																				} else {
																					echo $no_interest_groupings;
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
																			'general-error' => __( "Whoops! It looks like something went wrong. Please try again." , 'yikes-inc-easy-mailchimp-extender' ),
																			'invalid-email' => __( "Please provide a valid email address." , 'yikes-inc-easy-mailchimp-extender' ),
																			'email-exists-error' => __( "The provided email is already subscribed to this list." , 'yikes-inc-easy-mailchimp-extender' )
																		);
																		$global_error_messages = get_option( 'yikes-easy-mc-global-error-messages' , $error_message_array ); 
																	?>
																	<p class="edit-form-description"><?php _e( "Enter your custom messages for this form below. Leave the field blank to use the default global error message." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
																	
																	<!-- Success Message -->
																	<label for="yikes-easy-mc-success-message"><strong><?php _e( 'Success Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-success-message" id="yikes-easy-mc-success-message" value="<?php echo isset( $error_messages['success'] ) ? stripslashes( esc_html( $error_messages['success'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['success']; ?>" >
																	</label>	
																	<!-- General Error Message -->
																	<label for="yikes-easy-mc-general-error-message"><strong><?php _e( 'General Error Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-general-error-message" id="yikes-easy-mc-general-error-message" value="<?php echo isset( $error_messages['general-error'] ) ? stripslashes( esc_html( $error_messages['general-error'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['general-error']; ?>" >
																	</label>
																	<!-- Invalid Email Address Message -->
																	<label for="yikes-easy-mc-invalid-email-message"><strong><?php _e( 'Invalid Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-invalid-email-message" id="yikes-easy-mc-invalid-email-message" value="<?php echo isset( $error_messages['invalid-email'] ) ? stripslashes( esc_html( $error_messages['invalid-email'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['invalid-email']; ?>">
																	</label>	
																	<!-- Email Address is already subscribed -->
																	<label for="yikes-easy-mc-user-subscribed-message"><strong><?php _e( 'Email Already Subscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
																		<input type="text" class="widefat" name="yikes-easy-mc-user-subscribed-message" id="yikes-easy-mc-user-subscribed-message" value="<?php echo isset( $error_messages['already-subscribed'] ) ? stripslashes( esc_html( $error_messages['already-subscribed'] ) ) : ''; ?>" placeholder="<?php echo $global_error_messages['email-exists-error']; ?>">
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
																<h3 class="edit-form-title"><span><?php _e( "Error Message Explanation" , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
																<div class="inside">
																	
																	<ul>
																		<li><strong><?php _e( 'Success Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'The message displayed to the user after they have submitted the form and the data has been successfully sent to MailChimp.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
																		<li><strong><?php _e( 'General Error Message' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'The message displayed to the user after a generic error has occurred.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
																		<li><strong><?php _e( 'Invalid Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'The message displayed to the user after they have entered a non-valid email address.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
																		<li><strong><?php _e( 'Email Already Subscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong> : <?php _e( 'The message displayed to the user after they attempt to sign up for a mailing list using an email address that is already subscribed.' , 'yikes-inc-easy-mailchimp-extender' ); ?></li>
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
						<div id="postbox-container-1" class="postbox-container">
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
											<span class="dashicons dashicons-plus"></span><?php _e( 'Associated List Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
										</a>
										<div class="yikes-mc-settings-expansion-section">
											<!-- Associated List -->
											<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="associated-list"><strong><?php _e( 'Associated List' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
												<select name="associated-list" id="associated-list" <?php if( empty( $list_data['data'] ) ) { echo 'disabled="disabled"'; } ?>>
													<?php
													if( !empty( $list_data['data'] ) ) {
														foreach( $list_data['data'] as $mailing_list ) {
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
												<?php if( !empty( $list_data['data'] ) ) { ?>
													<p class="description">
														<?php _e( "Users who sign up via this form will be added to the list selected above." , 'yikes-inc-easy-mailchimp-extender' ); ?>
													</p>
												<?php } else { ?>
													<p class="description">
														<?php _e( "It looks like you first need to create a list to assign this form to. Head over to" , 'yikes-inc-easy-mailchimp-extender' ); ?> <a href="http://www.MailChimp.com" title="<?php _e( 'Create a new list' , 'yikes-inc-easy-mailchimp-extender' ); ?>">MailChimp</a> <?php _e( 'to create your first list' , 'yikes-inc-easy-mailchimp-extender' ); ?>.
													</p>
												<?php } ?>
											</label></p>
										</div>
										
										<a href="#" class="expansion-section-title settings-sidebar">
											<span class="dashicons dashicons-plus"></span><?php _e( 'Optin Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
										</a>
										<div class="yikes-mc-settings-expansion-section">
											
											<!-- Single or Double Optin -->
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
											
											<!-- Welcome Email -->
											<?php
												if( !isset( $optin_settings['send_welcome_email'] ) ) {
													$optin_settings['send_welcome_email'] = '1';
												}
											?>
											<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="send-welcome-email"><strong><?php _e( 'Send Welcome Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
												<span class="edit-form-field-container-span">
													<label for="send-welcome"><input id="send-welcome" type="radio" name="send-welcome-email" value="1" <?php checked( $optin_settings['send_welcome_email'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
													&nbsp;<label for="do-not-send-welcome"><input id="do-not-send-welcome" type="radio" name="send-welcome-email" value="0" <?php checked( $optin_settings['send_welcome_email'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
												</span>
												<p class="description"><?php _e( "When the user signs up, should they receive the default welcome email?" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
											</label></p>
											
											<!-- Update Existing Users -->
											<?php
												if( !isset( $optin_settings['update_existing_user'] ) ) {
													$optin_settings['update_existing_user'] = '1';
												}
											?>
											<p class="form-field-container"><!-- necessary to prevent skipping on slideToggle(); --><label for="update-existing-user"><strong><?php _e( 'Update Existing Subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
												<span class="form-field-container-span">
													<label for="update-user"><input type="radio" id="update-user" name="update-existing-user" value="1" <?php checked( $optin_settings['update_existing_user'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
													&nbsp;<label for="do-not-update-user"><input type="radio" id="do-not-update-user"  name="update-existing-user" value="0" <?php checked( $optin_settings['update_existing_user'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
												</span>
												<p class="description"><?php printf( __( "Update an existing subscriber's info when they attempt to re-subscribe instead of displaying an %s message." , "yikes-inc-easy-mailchimp-extender" ), '<em>"already subscribed"</em>' ); ?></p>
											</label></p>
												
										</div>
										
										<a href="#" class="expansion-section-title settings-sidebar">
											<span class="dashicons dashicons-plus"></span><?php _e( 'Submission Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?>
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
													<label for="enable-ajax"><input type="radio" id="enable-ajax" name="form-ajax-submission" value="1" <?php checked( $submission_settings['ajax'] , '1' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
													&nbsp;<label for="disable-ajax"><input type="radio" id="disable-ajax"  name="form-ajax-submission" value="0" <?php checked( $submission_settings['ajax'] , '0' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
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
												<?php $this->generate_page_redirect_dropdown( $submission_settings['redirect_on_submission'] , $submission_settings['redirect_page'] ); ?>
												<p class="description"><?php _e( "When the user signs up would you like to redirect them to another page?" , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
											</label></p>
											
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
				
			<?php
		}
	?>
	
</div>