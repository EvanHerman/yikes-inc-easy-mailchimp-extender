<?php
		
	/* 
	* 	Helper file to migrate our options from previous version to the proper WordPress settings API
	*	Helper File , called inside class-yikes-inc-easy-mailchimp-extender-admin.php ( migrate_old_yks_mc_options() )
	*	@since v6.0
	* 	@Author: Yikes Inc. 
	*	@Contact: https://www.yikesplugins.com/
	*/
		
	// enqueue the styles for our migration page..
	wp_enqueue_style( 'yikes_mc_migrate_option_styles' , YIKES_MC_URL . 'admin/css/yikes-inc-easy-mailchimp-migrate-option-styles.css' );
	wp_enqueue_style( 'animate-css' , YIKES_MC_URL . 'admin/css/animate.min.css' );
	
	// store our old options
	$old_plugin_options = get_option( 'ykseme_storage' );
	
	$global_error_messages = array(
		'success' => __( $old_plugin_options['single-optin-message'] , 'yikes-inc-easy-mailchimp-extender' ),
		'general-error' => __( "Whoops! It looks like something went wrong. Please try again." , 'yikes-inc-easy-mailchimp-extender' ),
		'email-exists-error' => __( "The email you entered is already a subscriber to this list." , 'yikes-inc-easy-mailchimp-extender' ),
		'success-single-optin' => __( 'Thank you for subscribing!' , 'yikes-inc-easy-mailchimp-extender' ),
		'success-resubscribed' => __( 'Thank you for already being a subscriber! Your profile info has been updated.', 'yikes-inc-easy-mailchimp-extender' ),
		'update-link' => __( "To update your MailChimp profile, please [link]click to send yourself an update link[/link].", 'yikes-inc-easy-mailchimp-extender' ),
		'email-subject' => __( 'MailChimp Profile Update', 'yikes-inc-easy-mailchimp-extender' ),
	);
	
	// if old options are defined...
	if( $old_plugin_options ) {
		
		// Verify the NONCE is valid
		check_admin_referer( 'yikes-mc-migrate-options' , 'migrate_options_nonce' );
		
		?>
			
		<div class="wrap" style="text-align:center;">
			<h3><?php _e( 'Migrating old plugin options' , 'yikes-inc-easy-mailchimp-extender' ); ?><span class="upgrading-ellipse-one">.</span><span class="upgrading-ellipse-two">.</span><span class="upgrading-ellipse-three">.</h3>
			<p><?php _e( 'please be patient while your options are updated and the process has completed' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<!-- empty list, populate when options  get updated -->
			<ul id="options-updated" class="yikes-easy-mc-hidden">
				<hr />
			</ul>
		</div>
				
		<script type="text/javascript">
			
			jQuery(document).ready(function($) {
				<?php
				
					// loop over our old options, and store them in a new option value
					$do_not_migrate_options = array(
						'ssl_verify_peer', 'api_validation' , 'widget_yikes_mc_widget' , 'flavor' , 'single-optin-message' , 'double-optin-message' ,
						'mailchimp-optIn-default-list' , 'version' , 'yks-mailchimp-jquery-datepicker' , 'ssl_verify_peer' , 'optIn-checkbox' , 'yks-mailchimp-optin-checkbox-text',
						'yks-mailchimp-required-text' , 'optin'
					);
							
					foreach( $old_plugin_options as $option_name => $option_value ) {
												
						if( ! in_array( $option_name , $do_not_migrate_options ) ) {
							// ajax request to update our options one by one..
							// if its an array, we need to json encode it
							if( is_array( $option_value ) ) {
								
								if( $option_name == 'lists' ) {
																	
									if( ! empty( $option_value ) ) {
										$settings = 1;
										$form_length = count( $option_value );
										foreach( $option_value as $mailchimp_form ) {
											// update and pass our placeholder value
											reset( $mailchimp_form );
											$form_id = $mailchimp_form['id'];
											
											$fields = $mailchimp_form['fields'];	
											reset( $fields );
											$first_field_key = key( $fields );
											$array_keys = array_keys( $fields );
											
											$x = 1;								
					
											foreach( $array_keys as $parent_key ) {
												
												// alter the field keys so they show up after an import
												$split_parent_key = explode( '-', $parent_key );

												$new_parent_key = ( isset( $split_parent_key[1] ) ) ? strtoupper( $split_parent_key[1] ) : $parent_key;

												$mailchimp_form['fields'][$new_parent_key] = $mailchimp_form['fields'][$parent_key];

												unset( $mailchimp_form['fields'][$parent_key] );			
										
												// update our placeholder key to be 'placeholder'
												$mailchimp_form['fields'][$new_parent_key]['placeholder'] = isset( $mailchimp_form['fields'][$new_parent_key]['placeholder-'.$form_id.'-'.$x] ) ? $mailchimp_form['fields'][$new_parent_key]['placeholder-'.$form_id.'-'.$x] : '';
												// update field classes
												$mailchimp_form['fields'][$new_parent_key]['additional-classes'] = isset( $mailchimp_form['fields'][$new_parent_key]['custom-field-class-'.$form_id.'-'.$x] ) ? $mailchimp_form['fields'][$new_parent_key]['custom-field-class-'.$form_id.'-'.$x] : '';
												// update help field - populate description
												$mailchimp_form['fields'][$new_parent_key]['description'] = isset( $mailchimp_form['fields'][$new_parent_key]['help'] ) ? $mailchimp_form['fields'][$new_parent_key]['help'] : ''; 
												// remove the old placeholder structure
												unset( $mailchimp_form['fields'][$new_parent_key]['placeholder-'.$form_id.'-'.$x] );
												// remove old custom class structure
												unset( $mailchimp_form['fields'][$new_parent_key]['custom-field-class-'.$form_id.'-'.$x] );
												// remove old help/description 
												unset( $mailchimp_form['fields'][$new_parent_key]['help'] );
																							
												// check if choices is set, and encode them
												if( isset( $mailchimp_form['fields'][$new_parent_key]['choices'] ) && ! empty( $mailchimp_form['fields'][$new_parent_key]['choices'] ) ) {
													$mailchimp_form['fields'][$new_parent_key]['choices'] = addslashes( addslashes( json_encode( $mailchimp_form['fields'][$new_parent_key]['choices'] ) ) );
												}	

												// update 'default' to 'default-choice' for radio/dropdown
												if( isset( $mailchimp_form['fields'][$new_parent_key]['type'] ) && in_array( $mailchimp_form['fields'][$new_parent_key]['type'], array( 'radio', 'dropdown' ) ) ) {
													$mailchimp_form['fields'][$new_parent_key]['default_choice'] = $mailchimp_form['fields'][$new_parent_key]['default'];
													unset( $mailchimp_form['fields'][$new_parent_key]['default'] );
												}
												
												// update 'date_format' on 'birthday' and 'date' fields
												if( isset( $mailchimp_form['fields'][$new_parent_key]['type'] ) && in_array( $mailchimp_form['fields'][$new_parent_key]['type'], array( 'date', 'birthday' ) ) ) {
													if( $mailchimp_form['fields'][$new_parent_key]['type'] == 'date' ) { // date
														$mailchimp_form['fields'][$new_parent_key]['date_format'] = 'MM/DD'; // mailchimp default (can be altered)
													} else { // birthday 
														$mailchimp_form['fields'][$new_parent_key]['date_format'] = 'MM/DD/YYYY'; // mailchimp default (can be altered)
													}
												}
												
												// update 'phone_format' on 'phone'
												if( isset( $mailchimp_form['fields'][$new_parent_key]['type'] ) && in_array( $mailchimp_form['fields'][$new_parent_key]['type'], array( 'phone' ) ) ) {
													$mailchimp_form['fields'][$new_parent_key]['phone_format'] = 'phone_format '; // phone format
												}
												
												$x++;
											}
											
											$done = ( $settings == $form_length ) ? 'done' : 'not-done';
											?>
												var mc_data = {
													'action': 'migrate_prevoious_forms',
													'option_name': 'yikes-mc-lists',
													'option_value': '<?php echo json_encode( $mailchimp_form ); ?>',
													'done_import': '<?php echo $done; ?>',
												};
																								
												$.post( ajaxurl, mc_data, function(response) {
													jQuery( '#options-updated' ).show();
													jQuery( '#options-updated' ).append( '<li class="animated fadeInDown"><?php echo '<strong>'; ?>' + response.form_name + '<?php echo '</strong> ' . __( "successfully imported." , 'yikes-inc-easy-mailchimp-extender' ); ?></li>' );	
													if( response.completed_import ) {
														setTimeout( function() {
															// finished with the loop...lets let the user know....and then redirect them....
															jQuery( '.wrap' ).find( 'h3' ).text( '<?php _e( 'Settings Successfuly Imported', 'yikes-inc-easy-mailchimp-extender' ); ?>' );
															jQuery( '.upgrading-ellipse-one' ).remove();
															jQuery( '.upgrading-ellipse-two' ).remove();
															jQuery( '.upgrading-ellipse-three' ).remove();
															jQuery( '.wrap' ).find( 'h3' ).next().fadeOut();
															jQuery( '#options-updated' ).append( '<li class="animated fadeInDown migration-complete-notification"><em><?php _e( "Migration Complete. Please wait..." , 'yikes-inc-easy-mailchimp-extender' ); ?> </em> <img src="<?php echo esc_url_raw( admin_url( "images/wpspin_light.gif" ) ); ?>" /></li>' );
															// redirect our user to the main plugin page...
															setTimeout( function() {
																<?php 
																	// migrate options that didnt make it (they were never stored in the 'ykseme_storage' options array)
																	add_option( 'yikes-mc-api-validation' , get_option( 'api_validation' , 'invalid_api_key' ) );
																	add_option( 'yikes-mc-error-messages' , $global_error_messages );
																	// delete our old options after a successful migration (and some new ones that are no longer needed)
																	delete_option( 'widget_yikes_mc_widget' );
																	delete_option( 'api_validation' );
																	delete_option( 'ykseme_storage' );
																	delete_option( 'yikes-mc-lists' );
																?>
																window.location.replace( "<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ); ?>" );
															}, 3000);
														}, 1500);
													}
												});
											<?php
											$settings++;
										}
									}
								}
		
							}
							/* Rename our ReCaptcha Options */
								/* Public Site Key */
								if( $option_name == 'recaptcha-api-key' ) {
									$option_name = 'recaptcha-site-key';
								}
								/* Private Key */
								if( $option_name == 'recaptcha-private-api-key'  ) {
									$option_name = 'recaptcha-secret-key';
								}
								/* Change 'recaptcha-setting' to 'recaptcha-status' */
								/* Status */
								if( $option_name == 'recaptcha-setting' ) {
									$option_name = 'recaptcha-status';
								}
							/* End  re-name ReCaptcha options */
							

							if( is_array( $option_value ) ) {
								$option_value = json_encode( $option_value );
							}
							// do noit migrate the lists option, it's not useful to us
							if( $option_name != 'lists' ) {
								?>
									var data = {
										'action': 'migrate_old_plugin_settings',
										'option_name': '<?php echo $option_name; ?>',
										'option_value': '<?php echo $option_value; ?>',
									};
													
									$.post( ajaxurl, data, function(response) {
										jQuery( '#options-updated' ).show();
										jQuery( '#options-updated' ).append( '<li class="animated fadeInDown"><?php echo '<strong>' . ucwords( str_replace( '_' , ' ' , str_replace( '-' , ' ' , $option_name ) ) ) . '</strong> ' . __( "successfully imported." , 'yikes-inc-easy-mailchimp-extender' ); ?></li>' );	
									});
								<?php
								
							}
						}
					}
				?>		
			});
		</script>
			
		<?php
		// delete the options after the import, as we no longer need them
		// delete_option( 'ykseme_storage' );
		// else, die and redirect the user to the main admin page
	} else {
		?>
		<div class="wrap">
			<script>
					setTimeout( function() {
						window.location.replace( "<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ); ?>" );
					}, 2000 );
			</script>
		<?php
			wp_die( '<strong>' . __( 'Old plugin options do not exist. Redirecting you...' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong>' , 500 );
		?>
		</div>
		<?php
	}