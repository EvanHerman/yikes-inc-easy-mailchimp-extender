<?php

// To Do: Assign a static variable to allow for multiple forms on the same page to be submitted through ajax

// Add Shortcode ( [yikes-mailchimp] )
function process_mailchimp_shortcode( $atts ) {
	
	$text_domain = 'yikes-inc-easy-mailchimp-extender';
	
	// Attributes
	extract( shortcode_atts(
		array(
			'form' => '',
			'submit' => 'Submit',
			'title' => '0',
			'description' => '0',
		), $atts )
	);
	
	// if the user forgot to specify a form ID, lets kill of and warn them.
	if( !$form ) {
		return __( 'Woops, it looks like you forgot to specify a form ID.', $text_domain );
	}
	
	global $wpdb;

	// return it as an array, so we can work with it to build our form below
	$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form . '', ARRAY_A );
	
	// confirm we have some results, or return an error
	if( !$form_results ) {
		return __( 'Oh no, something went wrong. Try re-creating this form , or displaying a seperate form. If the error persists, please contact support.' , $text_domain );
	}
	
	// print_r( $form_results );
	
	// place our results into a seperate variable for easy looping
	$form_data = $form_results[0];
	
	// store our variables
	$form_id = $form_data['id']; // form id (the id of the form in the database)
	$list_id = $form_data['list_id']; // associated list id (users who fill out the form will be subscribed to this list)
	$form_name = $form_data['form_name']; // form name
	$form_description = apply_filters( 'the_content' , stripslashes( $form_data['form_description'] ) );
	$fields = json_decode( stripslashes( $form_data['fields'] ) , true );
	$styles = json_decode( stripslashes( $form_data['custom_styles'] ) , true );
	$send_welcome = $form_data['send_welcome_email'];
	$redirect_user = $form_data['redirect_user_on_submit'];
	$redirect_page = $form_data['redirect_page'];
	$submission_settings = json_decode( stripslashes( $form_data['submission_settings'] ) , true );
	$optin_settings = json_decode( stripslashes( $form_data['optin_settings'] ) , true );
	$error_message = json_decode( stripslashes( $form_data['error_messages'] ) , true );	
	
	// ensure there is an 'email' field the user can fill out
	// or else MailChimp throws errors at you
		// extract our array keys
		$array_keys = array_keys( $fields );
		// check for EMAIL in that array
		if( !in_array( 'EMAIL' , $array_keys ) ) {
			return __( "An email field is required for all MailChimp forms. Please add an email field to this form." , $text_domain );
		}	
	
	/*
	*  pre-form action hooks
	*  check readme for usage examples
	*/
	do_action( 'yikes-easy-mc-before-form-'.$form_id );
	do_action( 'yikes-easy-mc-before-form' );	
	
	
	// display the form description if the user 
	// has specified to do so
	if( !empty( $title ) && $title == 1 ) {
		echo apply_filters( 'yikes-easy-mc-form-title' , apply_filters( 'the_title' , $form_name ) );
	}
	
	
	// display the form description if the user 
	// has specified to do so
	if( !empty( $description ) && $description == 1 ) {
		echo apply_filters( 'yikes-easy-mc-form-description' ,  apply_filters( 'the_content' , $form_description ) );
	}
	
	// render the form!
	?>
		<form id="<?php echo sanitize_title( $form_name ); ?>" class="yikes-easy-mc-form">
			
			<?php 
				foreach( $fields as $field ) {
					
					// input array
					$field_array = array();
					// label array
					$label_array = array();
					
					if( $field['additional-classes'] != '' ) {
						$custom_classes = explode( ', ' , $field['additional-classes'] );
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
						} // input third left
						if( in_array( 'field-left-third' , $custom_classes ) ) {
							$label_array['class'] = 'class="field-left-third"';
							$key = array_search( 'field-left-half' , $custom_classes );
							unset( $custom_classes[$key] );
						} // input third right
						if( in_array( 'field-right-third' , $custom_classes ) ) {
							$label_array['class'] = 'class="field-right-third"';
							$key = array_search( 'field-right-half' , $custom_classes );
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
						}
					} else {
						$custom_classes = array();
					}
					
					// build up our array
					$field_array['id'] = 'id="' . $field['merge'] . '" ';
					$field_array['name'] = 'name="' . $field['merge'] . '" ';
					$field_array['placeholder'] = 'placeholder="' . stripslashes( $field['placeholder'] ) . '" ';
					$field_array['classes'] = 'class="yikes-easy-mc-'.$field['type'] . ' ' .  trim( implode( ' ' , $custom_classes ) ) . '" ';
					
					// email must always be required and visible
					if( $field['type'] == 'email' ) {
						$field_array['required'] = 'required="required"';
						$label_array['visible'] = '';
					} else {
						$field_array['required'] = isset( $field['require'] ) ? 'required="required"' : '';
						$label_array['visible'] = isset( $field['hide'] ) ? 'style="display:none;"' : '';
					}
					
					// loop over our fields by Type
					switch ( $field['type'] ) {
						
						default:
						case 'text':
							?>
							<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo $field['merge'] . '-label'; ?>"><?php echo stripslashes( $field['label'] ); ?></span>
								<input <?php echo implode( ' ' , $field_array ); ?> type="text">
							</label>
							<?php
							break;
							
						case 'dropdown':
							?>
							<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo $field['merge'] . '-label'; ?>"><?php echo stripslashes( $field['label'] ); ?></span>
								<select <?php echo implode( ' ' , $field_array ); ?>>
									<?php 	
										// decode for looping
										$choices = json_decode( $field['choices'] , true );
										foreach( $choices as $choice ) {
											?><option value="<?php echo $choice; ?>"><?php echo $choice; ?></option><?php
										} 
									?>
								</select>
							</label>
							<?php
							break;
							
						case 'radio':
							// remove the ID (as to not assign the same ID to every radio button)
							unset( $field_array['id'] );
							$choices = json_decode( $field['choices'] , true );
							$i = 1;
							?>
							<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>><span class="<?php echo $field['merge'] . '-label'; ?> checkbox-parent-label"><?php echo stripslashes( $field['label'] ); ?></span>
								<?php
									foreach( $choices as $choice ) {
										?>
										<label for="<?php echo $field['merge'] . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); ?>"><span class="<?php echo $field['merge'] . '-label'; ?>"><?php echo stripslashes( $choice ); ?></span>
											<input type="checkbox" id="<?php echo $field['merge'] . '-' . $i; ?>" value="<?php echo $choice; ?>">
										</label>
										<?php
										$i++;
									}
								?>
							</label>
							<?php
							break;
					
					}
				}
			
			?>
						
			<!-- Submit Button -->
			<?php echo apply_filters( 'yikes-easy-mc-submit-button' , '<input type="submit" value="' . stripslashes( $submit ) . '" class="yikes-easy-mc-submit-button yikes-easy-mc-submit-button-' . $form_data['id'] ); ?>
			
		</form>
	<?php
	
	/*
	*  post-form action hooks
	*  check readme for usage examples
	*/
	do_action( 'yikes-easy-mc-after-form-'.$form_id );
	do_action( 'yikes-easy-mc-after-form' );	
	
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
	
}
add_shortcode( 'yikes-mailchimp', 'process_mailchimp_shortcode' );









?>