<?php
// To Do: Assign a static variable to allow for multiple forms on the same page to be submitted through ajax
// Add Shortcode ( [yikes-mailchimp] )
function process_mailchimp_shortcode( $atts ) {
		
	// Attributes
	extract( shortcode_atts(
		array(
			'form' => '',
			'submit' => 'Submit',
			'title' => '0',
			'description' => '0', 
			'ajax' => '',
		), $atts , 'yikes-mailchimp' )
	);
		
	/* If the user hasn't authenticated yet, lets kill off */
	if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {
		return '<div class="invalid-api-key-error"><p>' . __( "Whoops, you're not connected to MailChimp. You need to enter a valid MailChimp API key." , 'yikes-inc-easy-mailchimp-extender' ) . '</p></div>';
	}
	
	// if the user forgot to specify a form ID, lets kill of and warn them.
	if( !$form ) {
		return __( 'Whoops, it looks like you forgot to specify a form to display.', 'yikes-inc-easy-mailchimp-extender' );
	}
	
	global $wpdb;
	// return it as an array, so we can work with it to build our form below
	$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form . '', ARRAY_A );
	
	// confirm we have some results, or return an error
	if( !$form_results ) {
		return __( "Oh no...This form doesn't exist. Head back to the manage forms page and select a different form." , 'yikes-inc-easy-mailchimp-extender' );
	}
		
	/*
	*	Check if the user wants to use reCAPTCHA Spam Prevention
	*/
	if( get_option( 'yikes-mc-recaptcha-status' , '' ) == '1' ) {
		// if either of the Private the Secret key is left blank, we should display an error back to the user
		if( get_option( 'yikes-mc-recaptcha-site-key' , '' ) == '' ) {
			return __( "Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA site key!" , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>';
		}
		if( get_option( 'yikes-mc-recaptcha-secret-key' , '' ) == '' ) {
			return __( "Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA secret key!" , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>';
		}
		// Store the site language (to load recaptcha in a specific language)
		$locale = get_locale();
		$locale_split = explode( '_', $locale );
		$lang = ( isset( $locale_split ) ? $locale_split[0] : $locale );
		// enqueue Google recaptcha JS
		wp_register_script( 'google-recaptcha-js' , 'https://www.google.com/recaptcha/api.js?hl=' . $lang , array( 'jquery' ) , 'all' );
		wp_enqueue_script( 'google-recaptcha-js' );
		$recaptcha_site_key = get_option( 'yikes-mc-recaptcha-site-key' , '' );
		$recaptcha_box = '<div name="g-recaptcha" class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '"></div>';
	}
	
	// place our results into a seperate variable for easy looping
	$form_data = $form_results[0];
	
	// store our variables
	$form_id = (int) $form_data['id']; // form id (the id of the form in the database)
	$list_id = sanitize_key( $form_data['list_id'] ); // associated list id (users who fill out the form will be subscribed to this list)
	$form_name = esc_attr( $form_data['form_name'] ); // form name
	$form_description = esc_attr( stripslashes( $form_data['form_description'] ) );
	$fields = json_decode( $form_data['fields'] , true );
	$styles = json_decode( stripslashes( $form_data['custom_styles'] ) , true );
	$send_welcome = $form_data['send_welcome_email'];
	$redirect_user = $form_data['redirect_user_on_submit'];
	$redirect_page = $form_data['redirect_page'];
	$submission_settings = json_decode( stripslashes( $form_data['submission_settings'] ) , true );
	$optin_settings = json_decode( stripslashes( $form_data['optin_settings'] ) , true );
	$error_messages = json_decode( $form_data['error_messages'] , true );	
	$notifications = isset( $form_data['custom_notifications'] ) ? json_decode( stripslashes( $form_data['custom_notifications'] ) , true ) : '';
	
	// used in yikes-mailchimp-redirect-url filter
	global $post;
	$page_data = $post;
	
	// enqueue the form styles
	wp_enqueue_style( 'yikes-inc-easy-mailchimp-public-styles', YIKES_MC_URL . 'public/css/yikes-inc-easy-mailchimp-extender-public.min.css' );
	
	// custom action hook to enqueue scripts & styles wherever the shortcode is used
	do_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles', $form_id );
	
	// object buffer 
	ob_start();	
		
	?>
	<!-- Easy Forms for MailChimp v<?php echo YIKES_MC_VERSION; ?> by YIKES Inc: https://yikesplugins.com/plugin/easy-forms-for-mailchimp/ -->
	<section class="yikes-mailchimp-container" id="yikes-mailchimp-container-<?php echo $form_id . ' ' . apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ); ?>">
	<?php
		
		/* If the current user is logged in, and an admin...lets display our 'Edit Form' link */
		if( is_user_logged_in() ) {
			if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
				$edit_form_link = '<span class="edit-link">';
					$edit_form_link .= '<a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $form ) ) . '" title="' . __( 'Edit' , 'yikes-inc-easy-mailchimp-extender' ) . ' ' . ucwords( $form_name ) . '">' . __( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
				$edit_form_link .= '</span>';
				$edit_form_link = apply_filters( 'yikes-mailchimp-front-end-form-action-links', $edit_form_link, $form, ucwords( $form_name ) );
			} else {
				$edit_form_link = '';
			}
		}
					
		// ensure there is an 'email' field the user can fill out
		// or else MailChimp throws errors at you
			// extract our array keys
			if( isset( $fields ) && !empty( $fields ) ) {	
				$array_keys = array_keys( $fields );
				// check for EMAIL in that array
				if( !in_array( 'EMAIL', $array_keys ) && !in_array( 'email', $array_keys ) ) {
					return '<p>' . __( "An email field is required for all MailChimp forms. Please add an email field to this form." , 'yikes-inc-easy-mailchimp-extender' ) . '</p><p>' . $edit_form_link . '</p>';
				}
			} else {
				$error = '<p>' . __( "Whoops, it looks like you forgot to assign fields to this form." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
				if( is_user_logged_in() ) {
					if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
						return $error . $edit_form_link;
					}
				} else {
					return $error;
				}
			}
		
		/*
		*  pre-form action hooks
		*  check readme for usage examples
		*/
		do_action( 'yikes-mailchimp-before-form', $form_id );	
		
		// used to hide the form, keep values in the form etc.
		$form_submitted = 0;
		
		// display the form description if the user 
		// has specified to do so
		if( !empty( $title ) && $title == 1 ) {
			echo '<h3 class="yikes-mailchimp-form-title yikes-mailchimp-form-title-'.$form_id.'">' . apply_filters( 'yikes-mailchimp-form-title', apply_filters( 'the_title', $form_name ), $form_id ) . '</h3>';
		}
		
		// display the form description if the user 
		// has specified to do so
		if( !empty( $description ) && $description == 1 ) {
			echo '<p class="yikes-mailchimp-form-description yikes-mailchimp-form-description-'.$form_id.'">' . apply_filters( 'the_content', apply_filters( 'yikes-mailchimp-form-description', $form_description, $form_id ) ) . '</p>';
		}
		
		// Check for AJAX
		if( ( !empty( $atts['ajax'] ) && $atts['ajax'] == 1 ) || $submission_settings['ajax'] == 1 ) {
			// enqueue our ajax script
			wp_register_script( 'yikes-easy-mc-ajax' , YIKES_MC_URL . 'public/js/yikes-mc-ajax-forms.min.js' , array( 'jquery' ) , 'yikes-inc-easy-mailchimp-extender', false );
			wp_localize_script( 'yikes-easy-mc-ajax' , 'object' , array( 
				'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ), 
				'list_id' => $list_id , 
				'optin_settings' => json_encode( $optin_settings ), 
				'submission_settings' => json_encode( $submission_settings ), 
				'error_messages' => json_encode( $error_messages ), 
				'notifications' => json_encode( $notifications ), 
				'form_id' => $form_id,
				'page_data' => $page_data,
			) );
			wp_enqueue_script( 'yikes-easy-mc-ajax' );
		}
		
		/*
		*	On form submission, lets include our form processing file
		*	- processes non-ajax forms
		*/
		if( isset( $_POST ) && !empty( $_POST ) && $submission_settings['ajax'] == 0 ) {
			// lets include our form processing file
			include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process/process_form_submission.php' );
		}
		
		// render the form!
		?>
			<form id="<?php echo sanitize_title( $form_name ); ?>-<?php echo $form_id; ?>" class="yikes-easy-mc-form yikes-easy-mc-form-<?php echo $form_id; echo ' ' . apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ); echo ' ' . apply_filters( 'yikes-mailchimp-form-class', '', $form_id ); if( !empty( $_POST ) && $form_submitted == 1 && $submission_settings['hide_form_post_signup'] == 1 ) { echo ' yikes-easy-mc-display-none'; } ?>" action="" method="POST">
							
				<?php 
				foreach( $fields as $field ) {
						// input array
						$field_array = array();
						// label array
						$label_array = array();
						
						if( $field['additional-classes'] != '' ) {
							// split custom classes at spaces
							$custom_classes = explode( ' ' , $field['additional-classes'] );
							// check our custom class array for field-left/field-right
							// if it's set we need to assign it to our label and remove it from the field classes
							 // input half left
							if( in_array( 'field-left-half' , $custom_classes ) ) {
								$label_array['class'] = 'class="field-left-half"';
								$key = array_search( 'field-left-half' , $custom_classes );
								unset( $custom_classes[$key] );
							} // input half right
							if( in_array( 'field-right-half' , $custom_classes ) ) {
								$label_array['class'] = 'class="field-right-half"';
								$key = array_search( 'field-right-half' , $custom_classes );
								unset( $custom_classes[$key] );
							} // input thirds (1/3 width, floated left)
							if( in_array( 'field-third' , $custom_classes ) ) {
								$label_array['class'] = 'class="field-third"';
								$key = array_search( 'field-third' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 2 column radio
							if( in_array( 'option-2-col' , $custom_classes ) ) {
								$label_array['class'] = 'class="option-2-col"';
								$key = array_search( 'option-2-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 3 column radio
							if( in_array( 'option-3-col' , $custom_classes ) ) {
								$label_array['class'] = 'class="option-3-col"';
								$key = array_search( 'option-3-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 4 column radio
							if( in_array( 'option-4-col' , $custom_classes ) ) {
								$label_array['class'] = 'class="option-4-col"';
								$key = array_search( 'option-4-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // inline radio & checkboxes etc
							if( in_array( 'option-inline' , $custom_classes ) ) {
								$label_array['class'] = 'class="option-inline"';
								$key = array_search( 'option-inline' , $custom_classes );
								unset( $custom_classes[$key] );
							}
						} else {
							$custom_classes = array();
						}
						
						if( isset( $field['hide-label'] ) ) {
							if( $field['hide-label'] == 1 ) {
								$custom_classes[] = 'field-no-label';
							}
						}
					
					/* Store tag variable based on field type */
					if( isset( $field['merge'] ) ) {
						$tag = 'merge';
					} else {
						$tag = 'group_id';
					}
					
					// build up our array
					$field_array['id'] = 'id="' . esc_attr( $field[$tag] ) . '" ';
					$field_array['name'] = 'name="' . esc_attr( $field[$tag] ) . '" ';
					$field_array['placeholder'] = isset( $field['placeholder'] ) ? 'placeholder="' . esc_attr( stripslashes( $field['placeholder'] ) ) . '" ' : '';
					$field_array['classes'] = 'class="yikes-easy-mc-'.$field['type'] . ' ' .  esc_attr( trim( implode( ' ' , $custom_classes ) ) ) . '" ';
						
					// email must always be required and visible
					if( $field['type'] == 'email' ) {
						$field_array['required'] = 'required="required"';
						$label_array['visible'] = '';
						$label_array['required'] = 'class="' . $field['merge'] . '-label yikes-mailchimp-field-required"';
					} else {
						$field_array['required'] = isset( $field['require'] ) ? 'required="required"' : '';
						$label_array['visible'] = isset( $field['hide'] ) ? 'style="display:none;"' : '';
						$label_array['required'] = isset( $field['require'] ) ? 'class="' . $field['merge'] . '-label yikes-mailchimp-field-required"' : 'class="' . $field['merge'] . '-label"';
					}
					
					// filter the field array data
					$field_array = apply_filters( 'yikes-mailchimp-field-data', $field_array, $field, $form_id );
					
					/* Loop Over Standard Fields (aka merge variables) */
					if( isset( $field['merge'] ) ) {
															
						// print_r( $field );
						
						// loop over our fields by Type
						switch ( $field['type'] ) {
							
							default:
							case 'email':
							case 'text':
							case 'number':				
							
								// pass our default value through our filter to parse dynamic data by tag (used solely for 'text' type)
								$default_value = esc_attr( apply_filters( 'yikes-mailchimp-process-default-tag' , $field['default'] ) );
								
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									?>
									<input <?php echo implode( ' ' , $field_array ); if( $field['type'] != 'email' && $field['type'] != 'number' ) { ?> type="text" <?php } else if( $field['type'] == 'email' ) { ?> type="email" <?php } else { ?> type="number" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
									
									<!-- description -->
									<?php if( isset( $field['description'] ) ) { ?><p class="form-field-description"><small><?php echo esc_attr( stripslashes( $field['description'] ) ); ?></small></p><?php } ?>
									
									<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
									</label>
									<?php
								}
								break;
							
							case 'url':
							case 'imageurl':
								$default_value = $field['default'];	
								
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									?>
									<input <?php echo implode( ' ' , $field_array ); ?> type="url" <?php if( $field['type'] == 'url' ) { ?> title="<?php _e( 'Please enter a valid URL to the website.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" <?php } else { ?> title="<?php _e( 'Please enter a valid URL to the image.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
									
									<!-- description -->
									<?php if( isset( $field['description'] ) ) { ?><p class="form-field-description"><small><?php echo esc_attr( stripslashes( $field['description'] ) ); ?></small></p><?php } ?>
									
									<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
									</label>
									<?php
								}
							break;
							
							case 'phone':
								$default_value = $field['default'];
								$phone_format = $field['phone_format'];		
								?>
									<script type="text/javascript">
										/* Replace incorrect values and format it correctly for MailChimp API */
										function formatUSPhoneNumber( e ) {
											var number = e.value;
											var new_phone_number = number.trim().replace( '(' , '' ).replace( ')', '-' ).replace(/(\d\d\d)(\d\d\d)(\d\d\d\d)/, "$1-$2-$3");
											jQuery( '.<?php echo "yikes-easy-mc-".$field['type']; ?>' ).val( new_phone_number );
										}
									</script>
								<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									?>
									<input <?php echo implode( ' ' , $field_array ); ?> type="text" <?php if( $phone_format != 'US' ) { ?>  title="<?php _e( 'International Phone number (eg: #-###-###-####)' , 'yikes-inc-easy-mailchimp-extender' ); ?>" pattern="<?php echo apply_filters( 'yikes-mailchimp-international-phone-pattern' , '[0-9]{1,}' ); ?>" <?php } else { ?> title="<?php _e( 'US Phone Number (###) ### - ####' , 'yikes-inc-easy-mailchimp-extender' ); ?>" pattern="<?php echo apply_filters( 'yikes-mailchimp-us-phone-pattern' , '^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$' ); ?>" onblur="formatUSPhoneNumber(this);"<?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
									
									<!-- description -->
									<?php if( isset( $field['description'] ) ) { ?><p class="form-field-description"><small><?php echo stripslashes( $field['description'] ); ?></small></p><?php } ?>
									
									<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
									</label>
									<?php
								}
							break;
							
							case 'zip':
								$default_value = $field['default'];
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									?>
									<input <?php echo implode( ' ' , $field_array ); ?> type="text" pattern="\d{5,5}(-\d{4,4})?" title="<?php _e( '5 digit zip code, numbers only' , 'yikes-inc-easy-mailchimp-extender' ); ?>" value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
									
									<!-- description -->
									<?php if( isset( $field['description'] ) ) { ?><p class="form-field-description"><small><?php echo esc_attr( stripslashes( $field['description'] ) ); ?></small></p><?php } ?>
									
									<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
									</label>
									<?php
								}
							break;
							
							case 'address':
								// required fields
								$required_fields = array( 'addr1' => 'address' , 'addr2' => 'address 2', 'city' => 'city', 'state' =>'state', 'zip' =>'zip' , 'country' => 'country' );
								// store number for looping
								$x = 1;
								foreach( $required_fields as $type => $label ) {
								
									// set the field names for the addrress fields
									$field_array['name'] = 'name="'.$field[$tag].'['.$type.']'.'"';
									
									// reset the label classes for left-half/right-half for addresses
									if( isset( $label_array['class'] ) ) {
										if ( $x % 2 == 0 ) {
											$label_array['class'] = str_replace( 'field-left-half', 'field-right-half', $label_array['class'] );
										} else {
											$label_array['class'] = str_replace( 'field-right-half', 'field-left-half', $label_array['class'] );
										}
									}
									
									switch( $type ) {
										
										default:
										case 'addr1':
										case 'addr2':
										case 'city':
											if( !isset( $field['hide-label'] ) ) {
												?>
												<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php
											}
											?>
											
												
													<input <?php echo implode( ' ' , $field_array ); ?> type="text"  value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } ?>">

											<?php
											if( !isset( $field['hide-label'] ) ) {
												?>
												</label>
												<?php
											}
										break;
										
										case 'state':
											if( !isset( $field['hide-label'] ) ) {
												?>
												<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php
											}
											?>
												
												<select <?php echo implode( ' ' , $field_array ); ?>>
													<?php include_once( YIKES_MC_PATH . 'public/partials/shortcodes/templates/state-dropdown.php' ); ?>
												</select>

											<?php
											if( !isset( $field['hide-label'] ) ) {
												?>
												</label>
												<?php
											}
										break;
										
										case 'zip':
											if( !isset( $field['hide-label'] ) ) {
												?>
												<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php
											}
											?>
												
												<input <?php echo implode( ' ' , $field_array ); ?> type="text" pattern="\d{5,5}(-\d{4,4})?" title="<?php _e( '5 digit zip code, numbers only' , 'yikes-inc-easy-mailchimp-extender' ); ?>" value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
												
											<?php
											if( !isset( $field['hide-label'] ) ) {
												?>
												</label>
												<?php
											}
										break;
										
										case 'country':
											if( !isset( $field['hide-label'] ) ) {
												?>
												<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php
											}
											?>
												
												<select <?php echo implode( ' ' , $field_array ); ?>>
													<?php include_once( YIKES_MC_PATH . 'public/partials/shortcodes/templates/country-dropdown.php' ); ?>
												</select>
												
											<?php
											if( !isset( $field['hide-label'] ) ) {
												?>
												</label>
												<?php
											}
									}
									$x++;
								}
								
								// description
								if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php }
																	
								break;	
								
							case 'date':
							case 'birthday':
							
								// bootstrap datepicker requirements
								wp_enqueue_script( 'bootstrap-hover-dropdown' , YIKES_MC_URL . 'public/js/bootstrap-hover-dropdown.min.js' , array( 'jquery' ) );
								wp_enqueue_script( 'bootstrap-datepicker-script' , YIKES_MC_URL . 'public/js/bootstrap-datepicker.min.js' , array( 'jquery' , 'bootstrap-hover-dropdown' ) );
								wp_enqueue_style( 'bootstrap-datepicker-styles' , YIKES_MC_URL . 'public/css/bootstrap-datepicker3.standalone.min.css' );
								wp_enqueue_style( 'override-datepicker-styles' , YIKES_MC_URL . 'public/css/yikes-inc-easy-mailchimp-datepicker-styles.css' , array( 'bootstrap-datepicker-styles' ) );
								
								switch ( $field['type'] ) {
									default:
									case 'date':
										$date_format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'mm/dd/yy';
										break;
										
									case 'birthday':
										$date_format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'mm/dd';
										break;
								}
								// initialize the datepicker
								?>
									<style>
										.datepicker-dropdown {
											width: 20%;
											margin-top: 35px;
										}
										<?php
											if( wp_is_mobile() ) {
												?>
												.datepicker-dropdown {
													margin-top: 0px;
												}
												<?php
											}
										?>
									</style>
									<script type="text/javascript">
										jQuery(document).ready(function() {
											jQuery('input[data-attr-type="<?php echo $field['type']; ?>"]').datepicker({
												format : '<?php echo $date_format; ?>'
											});
										});
									</script>	
								<?php
								
								$default_value = ( isset( $field_default ) ? esc_attr( $field['default'] ) : '' );
								// store empty number for looping
								$x = 0;
								
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}	
								
								?>
									<input <?php echo implode( ' ' , $field_array ); ?> type="text" <?php if( $field['type'] == 'date' ) { ?> data-attr-type="date" <?php } else { ?> data-attr-type="birthday" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">
								
								<?php
								if( !isset( $field['hide-label'] ) ) {
									?>
										</label>
									<?php
								}
							break;
								
							case 'dropdown':
								$default_value = $field['default_choice'];
								// store empty number for looping
								$x = 0;
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									?>
										<select <?php echo implode( ' ' , $field_array ); ?>>
											<?php 	
												// decode for looping
												$choices = json_decode( stripslashes( $field['choices'] ) , true );
												foreach( $choices as $choice ) {
													?><option value="<?php echo $choice; ?>" <?php selected( $default_value , $x ); ?>><?php echo esc_attr( stripslashes( $choice ) ); ?></option><?php
													$x++;
												} 
											?>
										</select>
									
									<!-- description -->
									<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php }
									
									if( !isset( $field['hide-label'] ) ) {
									?>
										</label>
									<?php
									}
					
								break;
								
							case 'radio':
							case 'checkbox':
								// remove the ID (as to not assign the same ID to every radio button)
								unset( $field_array['id'] );
								$choices = json_decode( stripslashes( $field['choices'] ) , true );
								// assign a default choice
								$default_value = ( isset( $field['default_choice'] ) && $field['default_choice'] != '' ) ? $field['default_choice'] : $choices[0];
								// if the form was submit, but failed, let's reset the post data
								if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) {
									$default_value = $_POST[$field['merge']];
								}
								$count = count( $choices );
								$i = 1;
								$x = 0;
								
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['merge'] ). '-label'; ?> checkbox-parent-label"><?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
								
									foreach( $choices as $choice ) {
										?>
										<label for="<?php echo esc_attr( $field['merge'] ) . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $i === $count ) { ?> last-selection<?php } ?>" <?php if( $i == 1 ) { echo $field_array['required']; } ?>>
											<input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo $field['merge']; ?>" id="<?php echo $field['merge'] . '-' . $i; ?>" <?php checked( $default_value , $choice ); ?> value="<?php echo esc_attr( $choice ); ?>">
											<span class="<?php echo esc_attr( $field['merge'] ). '-label'; ?>"><?php echo stripslashes( $choice ); ?></span>
										</label>
										<?php
										$i++;
										$x++;
									}
									
									// description
									if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php }
																
									// close label
									if( !isset( $field['hide-label'] ) ) {
									?>
										</label>
									<?php
									}
								break;
						
						}
						
					} else { // loop over interest groups
					
						// store default choice
						$default_choice = ( isset( $field['default_choice'] ) && ! empty( $field['default_choice'] ) ) ? ( is_array( $field['default_choice'] ) ? $field['default_choice'] : $field['default_choice'] ) : $field['default_choice'];
						
						// if the form was submit, but failed, let's reset the post data
						if( isset( $_POST[$field['group_id']] ) && $form_submitted != 1 ) {
							$default_value = $default_choice;
						}
						
						// get our groups
						$groups = ( isset( $field['groups'] ) && ! empty( $field['groups'] ) ) ? json_decode( stripslashes_deep( $field['groups'] ), true ) : array();
												
						$count = count( $groups );						
						
						if( $field['type'] == 'checkboxes' ) {
							$type = 'checkbox';
						} else if( $field['type'] == 'radio' ) {
							$type = 'radio';
						}
						
						// loop over the interest group field types
						switch ( $field['type'] ) {	
						
							case 'checkboxes':
							case 'radio':
								$i = 0; // used to select our checkboxes/radios
								$x = 1; // used to find the last item of our array
								
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?> checkbox-parent-label"><?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
										
										
										foreach( $groups as $group ) {
											?>
											<label for="<?php echo $field['group_id'] . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $x === $count ) { ?>last-selection<?php } ?>">
												<input type="<?php echo $type; ?>" name="<?php echo $field['group_id']; ?>[]" id="<?php echo $field['group_id'] . '-' . $i; ?>" <?php if( $field['type'] == 'checkboxes' ) { if( in_array( $i , $default_choice ) ) { echo 'checked="checked"'; } } else { checked( ( isset( $default_choice ) && is_array( $default_choice ) ) ? $default_choice[0] : $default_choice , $i ); } ?> value="<?php echo esc_attr( $group['name'] ); ?>">
												<?php echo esc_attr( stripslashes( str_replace( '~' , '\'', $group['name'] ) ) ); ?>
											</label>
											<?php
											$i++;
											$x++;
										}
											
										// description
										if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php } 
											
									// close label
									if( !isset( $field['hide-label'] ) ) {
									?>
										</label>
									<?php
									}

								break;
						
							case 'dropdown':
								
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?>"><?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
									
									?>
										<select <?php echo implode( ' ' , $field_array ); ?>>
											<?php 	
												$i = 0;
												foreach( $groups as $group ) {
													?><option <?php selected( $i , $default_choice ); ?> value="<?php echo $group['name']; ?>"><?php echo esc_attr( stripslashes( $group['name'] ) ); ?></option><?php
													$i++;
												} 
											?>
										</select>
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php } ?>
								
									<?php
									// hidden labels
									if( !isset( $field['hide-label'] ) ) {
										?>
										</label>
										<?php
									}

								break;
								
							case 'hidden':
								$i = 0; // used to select our checkboxes/radios
								$x = 1; // used to find the last item of our array
																
								// hidden labels
								if( !isset( $field['hide-label'] ) ) {
									?>
									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?> checkbox-parent-label" style="display:none;"><?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?></span>
									<?php
								}
										
										
										foreach( $groups as $group ) {
											?>
											<label for="<?php echo $field['group_id'] . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $x === $count ) { ?>last-selection<?php } ?>" style="display:none;">
												<input type="radio" name="<?php echo $field['group_id']; ?>[]" id="<?php echo $field['group_id'] . '-' . $i; ?>" <?php if( $field['type'] == 'checkboxes' ) { if( in_array( $i , $default_choice ) ) { echo 'checked="checked"'; } } else { checked( ( isset( $default_choice ) && is_array( $default_choice ) ) ? $default_choice[0] : $default_choice , $i ); } ?> value="<?php echo esc_attr( $group['name'] ); ?>">
												<?php echo esc_attr( stripslashes( str_replace( '~' , '\'', $group['name'] ) ) ); ?>
											</label>
											<?php
											$i++;
											$x++;
										}
											
										// description
										if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?><p class="form-field-description"><small><?php echo esc_attr( trim( stripslashes( $field['description'] ) ) ); ?></small></p><?php } 
											
									// close label
									if( !isset( $field['hide-label'] ) ) {
									?>
										</label>
									<?php
									}

								break;
								
						}
					} // end interest groups
				}
				
				do_action( 'yikes-mailchimp-additional-form-fields', $form_data );	
				
				/* if we've enabled reCaptcha protection */
				if( isset( $recaptcha_box ) ) {
					echo $recaptcha_box;
				}
				if( is_user_logged_in() ) {
					if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
						$admin_class = 'admin-logged-in';
					}
				} else {
					$admin_class = '';
				}
				?>
				
				<!-- Honepot Trap -->
				<input type="hidden" name="yikes-mailchimp-honeypot" id="yikes-mailchimp-honeypot" value="">
				
				<!-- Submit Button -->
				<?php echo apply_filters( 'yikes-mailchimp-form-submit-button', '<button type="submit" class="yikes-easy-mc-submit-button yikes-easy-mc-submit-button-' . esc_attr( $form_data['id'] ) . ' btn btn-primary ' . $admin_class . '">' .  apply_filters( 'yikes-mailchimp-form-submit-button-text', esc_attr( stripslashes( $submit ) ), $form_data['id'] ) . '</button>', $form_data['id'] ); ?>
				<!-- Nonce Security Check -->
				<?php wp_nonce_field( 'yikes_easy_mc_form_submit', 'yikes_easy_mc_new_subscriber' ); ?>
			
			</form>
			<!-- MailChimp Form generated using Easy Forms for MailChimp by YIKES, Inc. (https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) -->
			
		<?php
			/* If the current user is logged in, and an admin...lets display our 'Edit Form' link */
			if( is_user_logged_in() ) {
				if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
					echo $edit_form_link;
				}
			}
			
		/*
		*  post-form action hooks
		*  check readme for usage examples
		*/
		do_action( 'yikes-mailchimp-after-form', $form_id );	
		
		/*
		*	Update the impressions count
		*	for non-admins
		*/
		if( !current_user_can( 'manage_options' ) ) {
			$form_data['impressions']++;
			$wpdb->update( 
				$wpdb->prefix . 'yikes_easy_mc_forms',
					array( 
						'impressions' => $form_data['impressions'],
					),
					array( 'ID' => $form ), 
					array(
						'%d',	// send welcome email
					), 
					array( '%d' ) 
				);
		}
	
	?>
	</section>
	<?php
	
	return ob_get_clean();
	
}
add_shortcode( 'yikes-mailchimp', 'process_mailchimp_shortcode' ); ?>
