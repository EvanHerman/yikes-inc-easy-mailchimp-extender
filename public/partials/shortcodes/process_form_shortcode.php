<?php
// To Do: Assign a static variable to allow for multiple forms on the same page to be submitted through ajax
// Add Shortcode ( [yikes-mailchimp] )
function process_mailchimp_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'form'                       => '',
			'submit'                     => '',
			'title'                      => '0',
			'custom_title'               => '',
			'description'                => '0',
			'custom_description'         => '',
			'ajax'                       => '',
			'recaptcha'                  => '', // manually set googles recptcha state
			'recaptcha_lang'             => '', // manually set the recaptcha language in the shortcode - also available is the yikes-mailchimp-recaptcha-language filter
			'recaptcha_type'             => '', // manually set the recaptcha type - audio/image - default image
			'recaptcha_theme'            => '', // manually set the recaptcha theme - light/dark - default light
			'recaptcha_size'             => '', // set the recaptcha size - normal/compact - default normal
			'recaptcha_data_callback'    => '', // set a custom js callback function to run after a successful recaptcha response - default none
			'recaptcha_expired_callback' => '', // set a custom js callback function to run after the recaptcha has expired - default none
			'inline'                     => '0',
		), $atts, 'yikes-mailchimp' );

	// set globals
	global $form_submitted, $process_submission_response;

	// setup form submitted variable
	$form_submitted = isset( $form_submitted ) ? $form_submitted : 0;

	/* If the user hasn't authenticated yet, lets kill off */
	if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {
		return '<div class="invalid-api-key-error"><p>' . __( "Whoops, you're not connected to MailChimp. You need to enter a valid MailChimp API key." , 'yikes-inc-easy-mailchimp-extender' ) . '</p></div>';
	}

	// if the user forgot to specify a form ID, lets kill of and warn them.
	if( ! $atts['form'] ) {
		return __( 'Whoops, it looks like you forgot to specify a form to display.', 'yikes-inc-easy-mailchimp-extender' );
	}

	// store our variables
	$form_id = (int) $atts['form']; // form id (the id of the form in the database)
	$interface = yikes_easy_mailchimp_extender_get_form_interface();
	$form_data = $interface->get_form( $form_id );

	// confirm we have some results, or return an error
	if ( empty( $form_data ) ) {
		return __( "Oh no...This form doesn't exist. Head back to the manage forms page and select a different form." , 'yikes-inc-easy-mailchimp-extender' );
	}

	/*
	*	Check if the user wants to use reCAPTCHA Spam Prevention
	*/
	if ( get_option( 'yikes-mc-recaptcha-status' , '' ) == '1' ) {
		// allow users to manually set recaptcha (instead of globally - recaptcha="1"/recaptcha="0" - but still needs to be globally enabled on the settings page)
		if ( $atts['recaptcha'] != '0' ) {
			// if either of the Private the Secret key is left blank, we should display an error back to the user
			if( get_option( 'yikes-mc-recaptcha-site-key' , '' ) == '' ) {
				return __( "Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA site key!" , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>';
			}
			if( get_option( 'yikes-mc-recaptcha-secret-key' , '' ) == '' ) {
				return __( "Whoops! It looks like you enabled reCAPTCHA but forgot to enter the reCAPTCHA secret key!" , 'yikes-inc-easy-mailchimp-extender' ) . '<span class="edit-link yikes-easy-mc-edit-link"><a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) . '" title="' . __( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Edit ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '</a></span>';
			}

			if( ! empty( $atts['recaptcha_type'] ) ) {
				echo $atts['recaptcha_type'];
			}

			// Store the site language (to load recaptcha in a specific language)
			$locale = get_locale();
			$locale_split = explode( '_', $locale );
			// Setup reCAPTCHA parameters
			$lang = ( ! empty( $locale_split ) ? $locale_split[0] : $locale );
			$lang = ( ! empty( $atts['recaptcha_lang'] ) ) ? $atts['recaptcha_lang'] : $lang;
			$type = ( ! empty( $atts['recaptcha_type'] ) ) ? strtolower( $atts['recaptcha_type'] ) : 'image'; // setup recaptcha type
			$theme= ( ! empty( $atts['recaptcha_theme'] ) ) ? strtolower( $atts['recaptcha_theme'] ) : 'light'; // setup recaptcha theme
			$size = ( ! empty( $atts['recaptcha_size'] ) ) ? strtolower( $atts['recaptcha_size'] ) : 'normal'; // setup recaptcha size
			$data_callback = ( ! empty( $atts['recaptcha_data_callback'] ) ) ? $atts['recaptcha_data_callback'] : false; // setup recaptcha size
			$expired_callback = ( ! empty( $atts['recaptcha_expired_callback'] ) ) ? $atts['recaptcha_expired_callback'] : false; // setup recaptcha size
			// Pass the shortcode parameters through a filter
			$recaptcha_shortcode_params = apply_filters( 'yikes-mailchimp-recaptcha-parameters', array(
				'language' => $lang,
				'theme' => $theme,
				'type' => $type,
				'size' => $size,
				'success_callback' => $data_callback,
				'expired_callback' => $expired_callback,
			), $atts['form'] );
			// enqueue Google recaptcha JS
			wp_register_script( 'google-recaptcha-js' , 'https://www.google.com/recaptcha/api.js?hl=' . $recaptcha_shortcode_params['language'] . '&onload=renderReCaptchaCallback&render=explicit', array( 'jquery' ) , 'all' );
			wp_enqueue_script( 'google-recaptcha-js' );
			$recaptcha_site_key = get_option( 'yikes-mc-recaptcha-site-key' , '' );
			$recaptcha_box = '<div name="g-recaptcha" class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '" data-theme="' . $recaptcha_shortcode_params['theme'] . '" data-type="' . $recaptcha_shortcode_params['type'] . '" data-size="' . $recaptcha_shortcode_params['size'] . '" data-callback="' . $recaptcha_shortcode_params['success_callback'] . '" data-expired-callback="' . $recaptcha_shortcode_params['expired_callback'] . '"></div>';
			?>
			<script type="text/javascript">
				/* Script Callback to init. multiple recaptchas on a single page */
				function renderReCaptchaCallback() {
					var x = 1;
					jQuery( '.g-recaptcha' ).each( function() {
						jQuery( this ).attr( 'id', 'recaptcha-' + x );
						recaptcha_paramaters = {
							'sitekey' : '<?php echo $recaptcha_site_key; ?>',
							'lang' : '<?php echo $lang; ?>',
							'type' : '<?php echo $type; ?>',
							'theme' : '<?php echo $theme; ?>',
							'size' : '<?php echo $size; ?>',
							'data_callback' : '<?php echo $data_callback; ?>',
							'expired_callback' : '<?php echo $expired_callback; ?>'
						};
						grecaptcha.render( 'recaptcha-' + x, recaptcha_paramaters );
						x++;
					});
				}
			</script>
			<?php
		}
	}

	// place our results into a seperate variable for easy looping
	$additional_form_settings = ( isset( $form_data['form_settings'] ) ) ? $form_data['form_settings'] : false;
	// store our options from the additional form settings array
	$form_classes = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-class-names'] : '';
	$inline_form = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-inline-form'] : '';
	$submit_button_type = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-submit-button-type'] : 'text';
	$submit_button_text = ( $additional_form_settings && $additional_form_settings['yikes-easy-mc-submit-button-text'] != '' ) ? esc_attr( $additional_form_settings['yikes-easy-mc-submit-button-text'] ) : __( 'Submit', 'yikes-inc-easy-mailchimp-extender' );
	$submit_button_image = ( $additional_form_settings ) ? esc_url( $additional_form_settings['yikes-easy-mc-submit-button-image'] ) : '';
	$submit_button_classes = ( $additional_form_settings ) ? ' ' . esc_attr( $additional_form_settings['yikes-easy-mc-submit-button-classes'] ) : '';

	// scheuldes
	$form_schedule_state = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-schedule'] : false;
	$form_schedule_start = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-restriction-start'] : '';;
	$form_schedule_end = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-restriction-end'] : '';
	$form_pending_message = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-restriction-pending-message'] : '';
	$form_expired_message = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-restriction-expired-message'] : '';

	// register required
	$form_login_required = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-login-required'] : false;
	$form_login_message = ( $additional_form_settings ) ? $additional_form_settings['yikes-easy-mc-form-restriction-login-message'] : '';

	// store number of fields
	$field_count = (int) count( $form_data['fields'] );

	// confirm we actually have fields, before looping
	if ( isset( $form_data['fields'] ) && ! empty( $form_data['fields'] ) ) {
		// loop over each field, if it's set to hidden -- subtract it from the field count
		// this throws off the layout for inline forms setup below
		foreach ( $form_data['fields'] as $form_field ) {
			if ( isset( $form_field->hide ) && $form_field->hide == 1 ) {
				$field_count --;
			}
		}
	}

	/**
	*	If login is required, abort
	*	@since 6.0.3.8
	*/
	if( $form_login_required ) {
		if( apply_filters( 'yikes-mailchimp-required-login-requirement', ! is_user_logged_in() ) ) {
			ob_start();
				?>
					<div class="yikes-mailchimp-login-required yikes-mailchimp-form-<?php echo $form_id; ?>-login-required">
						<?php echo apply_filters( 'yikes-mailchimp-frontend-content', $form_login_message ); ?>
					</div>
				<?php
			$output = str_replace( '[login-form]', wp_login_form(), ob_get_clean() );
			return $output;
		}
	}

	/**
	*	Check if schedule is set for this form
	*	@since 6.0.3.8
	*/
	if( $form_schedule_state ) {
		// store current date
		$current_date = strtotime( current_time( 'm/d/Y g:iA' ) );

		// the the current date is less than the form scheduled start date
		if( $current_date < $form_schedule_start ) {
			echo apply_filters( 'yikes-mailchimp-frontend-content', $form_pending_message );
			return;
			// abort
		}

		// The current date is past or equal to the end date, aka form has now expired
		if( $current_date >= $form_schedule_end ) {
			echo apply_filters( 'yikes-mailchimp-frontend-content', $form_expired_message );
			return;
			// abort
		}
	}

	// setup the submit button text
	// shortcode parameter takes precedence over option
	$submit = ( ! empty( $atts['submit'] ) ) ? $atts['submit'] : $submit_button_text;

	// used in yikes-mailchimp-redirect-url filter
	global $post;
	$page_data = $post;

	// Remove the post_password from this for security
	if( isset( $page_data->post_password ) ) {
		unset( $page_data->post_password );
	}

	// grab the last enqueued style, so we can use it as a dependency of our styles (for override)
	global $wp_styles;
	
	$last_key = '';
	if ( isset( $wp_styles ) && isset( $wp_styles->groups ) ) {
		end( $wp_styles->groups );	
		$last_key = key( $wp_styles->groups );
	}

	/*
	*	Check for the constant to prevent styles from loading
	*	to exclude styles from loading, add `define( 'YIKES_MAILCHIMP_EXCLUDE_STYLES', true );` to functions.php
	*	@since 6.0.3.8
	*/
	if( ! defined( 'YIKES_MAILCHIMP_EXCLUDE_STYLES' ) ) {
		// enqueue the form styles
		wp_enqueue_style( 'yikes-inc-easy-mailchimp-public-styles', YIKES_MC_URL . 'public/css/yikes-inc-easy-mailchimp-extender-public.min.css', array( $last_key ) );
	}

	/**
	*	Check for form inline parameter
	*/
	$form_inline = ( $atts['inline'] == 1 || $atts['inline'] == 'true' );
	// recheck from our form options
	if ( ! $form_inline ) {
		$form_inline = (bool) $additional_form_settings['yikes-easy-mc-inline-form'];
	}

	/* If the current user is logged in, and an admin...lets display our 'Edit Form' link */
	if( is_user_logged_in() ) {
		if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
			$edit_form_link = '<span class="edit-link">';
			$edit_form_link .= '<a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $atts['form'] ) ) . '" title="' . __( 'Edit' , 'yikes-inc-easy-mailchimp-extender' ) . ' ' . ucwords( $form_data['form_name'] ) . '">' . __( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
			$edit_form_link .= '</span>';
			$edit_form_link = apply_filters( 'yikes-mailchimp-front-end-form-action-links', $edit_form_link, $atts['form'], ucwords( $form_data['form_name'] ) );
		} else {
			$edit_form_link = '';
		}
	}

	// ensure there is an 'email' field the user can fill out
	// or else MailChimp throws errors at you
	// extract our array keys
	// @todo Remove array_keys() and in_array() usage here.
	if( isset( $form_data['fields'] ) && ! empty( $form_data['fields'] ) ) {
		$array_keys = array_keys( $form_data['fields'] );
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

	if( $form_inline ) {
		$field_width = (float) ( 100 / $field_count );
		$submit_button_width = (float) ( 20 / $field_count );
		/*
		*	Add inline styles after calculating the percentage etc.
		*	@since 6.0.3.8
		*/
		 $inline_label_css = "
			.yikes-easy-mc-form label.label-inline {
				float: left;
				width: calc( {$field_width}% - {$submit_button_width}% );
				padding-right: 10px;
			 }
		";
		wp_add_inline_style( 'yikes-inc-easy-mailchimp-public-styles', $inline_label_css );
	}

	// custom action hook to enqueue scripts & styles wherever the shortcode is used
	do_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles', $form_id );

	// object buffer
	ob_start();

	?>

	<section id="yikes-mailchimp-container-<?php echo $form_id; ?>" class="yikes-mailchimp-container yikes-mailchimp-container-<?php echo $form_id; ?> <?php echo apply_filters( 'yikes-mailchimp-form-container-class', '', $form_id ); ?>">
	<?php
		/*
		*  pre-form action hooks
		*  check readme for usage examples
		*/
		do_action( 'yikes-mailchimp-before-form', $form_id );

		/*
		*	Set a custom title using custom_title="lorem ipsum" parameter in the shortcode
		*	- This takes precedence over the title set
		*/
		if ( $atts['title'] ) {
			if ( ! empty( $atts['custom_title'] ) ) {
				/**
				 * Filter the title that is displayed through the shortcode.
				 *
				 * @param string $title   The title to display.
				 * @param int    $form_id The form ID.
				 */
				$title = apply_filters( 'yikes-mailchimp-form-title', apply_filters( 'the_title', $atts['custom_title'] ), $form_id );
			} else {
				$title = apply_filters( 'yikes-mailchimp-form-title', apply_filters( 'the_title', $form_data['form_name'] ), $form_id );
			}

			echo sprintf( '<h3 class="yikes-mailchimp-form-title yikes-mailchimp-form-title-%1$s">%2$s</h3>', $form_id, $title );
		}

		/*
		*	Allow users to specify a custom description for this form, no html support
		*	@since 6.0.3.8
		*/
		if ( $atts['description'] ) {
			if ( ! empty( $atts['custom_description'] ) ) {
				/**
				 * Filter the description that is displayed through the shortcode.
				 *
				 * @param string $title   The title to display.
				 * @param int    $form_id The form ID.
				 */
				$description = apply_filters( 'yikes-mailchimp-form-description', $atts['custom_description'], $form_id );
			} else {
				$description = apply_filters( 'yikes-mailchimp-form-description', $form_data['form_description'], $form_id );
			}

			echo sprintf( '<section class="yikes-mailchimp-form-description yikes-mailchimp-form-description-%1$s">%2$s</section>', $form_id, $description );
		}

		// Check for AJAX
		if( ( ! empty( $atts['ajax'] ) && $atts['ajax'] == 1 ) || $form_data['submission_settings']['ajax'] == 1 ) {
			// enqueue our ajax script
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'yikes-easy-mc-ajax' , YIKES_MC_URL . "public/js/yikes-mc-ajax-forms{$min}.js" , array( 'jquery' ) , YIKES_MC_VERSION, false );
			wp_localize_script( 'yikes-easy-mc-ajax', 'yikes_mailchimp_ajax', array(
				'ajax_url'                      => esc_url( admin_url( 'admin-ajax.php' ) ),
				'page_data'                     => $page_data,
				'interest_group_checkbox_error' => apply_filters( 'yikes-mailchimp-interest-group-checkbox-error', __( 'This field is required.', 'yikes-inc-easy-mailchimp-extender' ), $form_id ),
				'preloader_url'                 => apply_filters( 'yikes-mailchimp-preloader', esc_url_raw( admin_url( 'images/wpspin_light.gif' ) ) ),
				'loading_dots'                  => apply_filters( 'yikes-mailchimp-loading-dots', YIKES_MC_URL . 'includes/images/loading-dots.gif' ),
				'ajax_security_nonce'			=> wp_create_nonce( 'yikes_mc_form_submission_security_nonce' ),
			) );
		}

		// If update account details is set, we need to include our script to send out the update email
		wp_enqueue_script( 'update-existing-subscriber.js', YIKES_MC_URL . 'public/js/yikes-update-existing-subscriber.min.js' , array( 'jquery' ), 'all' );
		wp_localize_script( 'update-existing-subscriber.js', 'update_subscriber_details_data', array(
			'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'preloader_url' => apply_filters( 'yikes-mailchimp-preloader', esc_url_raw( admin_url( 'images/wpspin_light.gif' ) ) ),
		) );

		/*
		*	If a form was submitted, and the response was returned
		*	let's display it back to the user
		*	@since 6.0.3.4
		*/
		echo $process_submission_response;

		// render the form!
		?>
			<form id="<?php echo sanitize_title( $form_data['form_name'] ); ?>-<?php echo $form_id; ?>" class="yikes-easy-mc-form yikes-easy-mc-form-<?php echo $form_id . ' '; if ( $form_inline )  { echo 'yikes-mailchimp-form-inline '; } echo ' ' . apply_filters( 'yikes-mailchimp-form-class', $form_classes, $form_id ); if( !empty( $_POST ) && $form_submitted == 1 && $form_data['submission_settings']['hide_form_post_signup'] == 1 ) { echo ' yikes-easy-mc-display-none'; } ?>" action="" method="POST" data-attr-form-id="<?php echo $form_id; ?>">

				<?php
				// Set a default constant for hidden fields
				$hidden_label_count = 0;

				// Loop over our form fields
				foreach( $form_data['fields'] as $field ) {
						// input array
						$field_array = array();
						// label array
						$label_array = array();
						// label classes array
						$label_class_array = array();
						if( $field['additional-classes'] != '' ) {
							// split custom classes at spaces
							$custom_classes = explode( ' ' , $field['additional-classes'] );
							// check our custom class array for field-left/field-right
							// if it's set we need to assign it to our label and remove it from the field classes
							 // input half left
							if( in_array( 'field-left-half' , $custom_classes ) ) {
								// $label_array['class'] = 'class="field-left-half"';
								$label_class_array[] = 'field-left-half';
								$key = array_search( 'field-left-half' , $custom_classes );
								unset( $custom_classes[$key] );
							} // input half right
							if( in_array( 'field-right-half' , $custom_classes ) ) {
								// $label_array['class'] = 'class="field-right-half"';
								$label_class_array[] = 'field-right-half';
								$key = array_search( 'field-right-half' , $custom_classes );
								unset( $custom_classes[$key] );
							} // input thirds (1/3 width, floated left)
							if( in_array( 'field-third' , $custom_classes ) ) {
								// $label_array['class'] = 'class="field-third"';
								$label_class_array[] = 'field-third';
								$key = array_search( 'field-third' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 2 column radio
							if( in_array( 'option-2-col' , $custom_classes ) ) {
								// $label_array['class'] = 'class="option-2-col"';
								$label_class_array[] = 'option-2-col';
								$key = array_search( 'option-2-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 3 column radio
							if( in_array( 'option-3-col' , $custom_classes ) ) {
								// $label_array['class'] = 'class="option-3-col"';
								$label_class_array[] = 'option-3-col';
								$key = array_search( 'option-3-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // 4 column radio
							if( in_array( 'option-4-col' , $custom_classes ) ) {
								// $label_array['class'] = 'class="option-4-col"';
								$label_class_array[] = 'option-4-col';
								$key = array_search( 'option-4-col' , $custom_classes );
								unset( $custom_classes[$key] );
							} // inline radio & checkboxes etc
							if( in_array( 'option-inline' , $custom_classes ) ) {
								// $label_array['class'] = 'class="option-inline"';
								$label_class_array[] = 'option-inline';
								$key = array_search( 'option-inline' , $custom_classes );
								unset( $custom_classes[$key] );
							}
						} else {
							$custom_classes = array();
						}

						// if the form is set to inline, add the inline class to our labels
						// since @6.0.3.8
						if( $form_inline ) {
							$label_class_array[] = 'label-inline';
						}

						if( isset( $field['hide-label'] ) ) {
							if( absint( $field['hide-label'] ) === 1 ) {
								$hidden_label_count++;
								$custom_classes[] = 'field-no-label';
							}
						}

					/* Store tag variable based on field type */
					if( isset( $field['merge'] ) ) {
						$group = '';
						$tag = 'merge';
					} else {
						$group = 'group-';
						$tag = 'group_id';
					}

					// build up our array
					$field_array['id'] = 'id="yikes-easy-mc-form-' . $form_id . '-' . esc_attr( $field[$tag] ) . '" ';
					$field_array['name'] = 'name="' . $group . esc_attr( $field[ $tag ] ) . '" ';
					$field_array['placeholder'] = isset( $field['placeholder'] ) ? 'placeholder="' . esc_attr( stripslashes( $field['placeholder'] ) ) . '" ' : '';
					$field_array['classes'] = 'class="yikes-easy-mc-'.$field['type'] . ' ' .  esc_attr( trim( implode( ' ' , $custom_classes ) ) ) . '" ';

					// email must always be required and visible
					if( $field['type'] == 'email' ) {
						$field_array['required'] = 'required="required"';
						$label_array['visible'] = '';
						// $label_array['required'] = 'class="' . $field['merge'] . '-label yikes-mailchimp-field-required"';
						$label_class_array[] = $field['merge'] . '-label';
						$label_class_array[] = 'yikes-mailchimp-field-required';
					} else {
						if( $tag == 'merge' ) {
							$field_array['required'] = isset( $field['require'] ) ? 'required="required"' : '';
							$label_array['visible'] = isset( $field['hide'] ) ? 'style="display:none;"' : '';
							// $label_array['required'] = isset( $field['require'] ) ? 'class="' . $field['merge'] . '-label yikes-mailchimp-field-required"' : 'class="' . $field['merge'] . '-label"';
							$label_class_array[] = isset( $field['require'] ) ? $field['merge'] . '-label yikes-mailchimp-field-required' : $field['merge'] . '-label';
						} else {
							$field_array['required'] = isset( $field['require'] ) ? 'required="required"' : '';
							$label_array['visible'] = isset( $field['hide'] ) ? 'style="display:none;"' : '';
							// $label_array['required'] = isset( $field['require'] ) ? 'class="' . $field['group_id'] . '-label yikes-mailchimp-field-required"' : 'class="' . $field['group_id'] . '-label"';
							$label_class_array[] = isset( $field['require'] ) ? $field['group_id'] . '-label yikes-mailchimp-field-required' : $field['group_id'] . '-label';
						}
					}

					// if both hide label and hide field are checked, we gotta hide the field!
					if( isset( $field['hide' ] ) && $field['hide'] == 1 ) {
						if( isset( $field['hide-label' ] ) && $field['hide-label'] == 1 ) {
							$field_array['visible'] = 'style="display:none;"';
						}
					}

					$label_array['classes'] = 'class="' . implode( ' ', $label_class_array ) . '"';

					// filter the field array data
					$field_array = apply_filters( 'yikes-mailchimp-field-data', $field_array, $field, $form_id );

					/* Loop Over Standard Fields (aka merge variables) */
					if( isset( $field['merge'] ) ) {

						// loop over our fields by Type
						switch ( $field['type'] ) {

							default:
							case 'email':
							case 'text':
							case 'number':

								// pass our default value through our filter to parse dynamic data by tag (used solely for 'text' type)
								$default_value = ( isset( $field['default'] ) ? esc_attr( $field['default'] ) : '' );
								$default_value = apply_filters( 'yikes-mailchimp-process-default-tag', $default_value );
									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( !isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
												<?php if( ! isset( $field['hide-label'] ) ) { echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ), $form_id ); } ?>
											</span>
										<?php } ?>

										<input <?php echo implode( ' ' , $field_array ); if( $field['type'] != 'email' && $field['type'] != 'number' ) { ?> type="text" <?php } else if( $field['type'] == 'email' ) { ?> type="email" <?php } else { ?> type="number" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">

										<!-- description -->
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( stripslashes( $field['description'] ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php

								break;

							case 'url':
							case 'imageurl':
								$default_value = ( isset( $field['default'] ) ) ? $field['default'] : '';
									?>

									<script type="text/javascript">
										function properlyFormatURLField( e ) {
											var url_value = jQuery( e ).val();

											if ( url_value.indexOf( "http://" ) === -1 && url_value.indexOf( "https://" ) === -1 ) {

												jQuery( e ).val( 'http://' + url_value );

											}
										}
									</script>

									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( !isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php } ?>

										<input <?php echo implode( ' ' , $field_array ); ?> type="url" <?php if( $field['type'] == 'url' ) { ?> title="<?php _e( 'Please enter a valid URL to the website.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" <?php } else { ?> title="<?php _e( 'Please enter a valid URL to the image.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>" onblur="properlyFormatURLField(this);return false;">

										<!-- description -->
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( stripslashes( $field['description'] ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php

							break;

							case 'phone':
								$default_value = ( isset( $field['default'] ) ? esc_attr( $field['default'] ) : '' );
								$phone_format = $field['phone_format'];
								?>
									<script type="text/javascript">
										/* Replace incorrect values and format it correctly for MailChimp API */
										function formatUSPhoneNumber( e ) {
											var phone_number = e.value;
											var new_phone_number = phone_number.replace(/\(|\)/g, "").replace(/-/g, "").trim(); // replace all '-,' '(' and ')'
											formatted_us_number = new_phone_number.substring( 0, 10 ); // strip all characters after 10th number (10 = length of US numbers 215-555-5555
											formatted_us_number = formatted_us_number.replace(/(\d\d\d)(\d\d\d)(\d\d\d\d)/, "$1-$2-$3"); // split the string into the proper format
											jQuery( e ).val( formatted_us_number );
										}
									</script>

									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( !isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php } ?>

										<input <?php echo implode( ' ' , $field_array ); ?> type="text" <?php if( $phone_format != 'US' ) { ?>  title="<?php _e( 'International Phone Number' , 'yikes-inc-easy-mailchimp-extender' ); ?>" pattern="<?php echo apply_filters( 'yikes-mailchimp-international-phone-pattern' , '[0-9,-,+]{1,}' ); ?>" <?php } else { ?> title="<?php _e( 'US Phone Number (###) ### - ####' , 'yikes-inc-easy-mailchimp-extender' ); ?>" pattern="<?php echo apply_filters( 'yikes-mailchimp-us-phone-pattern' , '^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$' ); ?>" onblur="formatUSPhoneNumber(this);"<?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">

										<!-- description -->
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . esc_attr( $field['merge'] ) . '-description', stripslashes( $field['description'] ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php
							break;

							case 'zip':
								$default_value = ( isset( $field['default'] ) ? esc_attr( $field['default'] ) : '' );

									?>
									<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?>>

									<!-- dictate label visibility -->
									<?php if( ! isset( $field['hide-label'] ) ) { ?>
										<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
											<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
										</span>
									<?php } ?>

									<input <?php echo implode( ' ' , $field_array ); ?> type="text" pattern="\d{5,5}(-\d{4,4})?" title="<?php _e( '5 digit zip code, numbers only' , 'yikes-inc-easy-mailchimp-extender' ); ?>" value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">

									<!-- description -->
									<?php if( isset( $field['description'] ) ) { ?>
										<p class="form-field-description">
											<small>
												<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( stripslashes( $field['description'] ) ), $form_id ); ?>
											</small>
										</p>
									<?php } ?>

									</label>
									<?php

							break;

							case 'address':
								// required fields
								$required_fields = array( 'addr1' => 'address' , 'addr2' => 'address 2', 'city' => 'city', 'state' =>'state', 'zip' =>'zip' , 'country' => 'country' );

								// setup the default country value
								$default_country = apply_filters( 'yikes-mailchimp-default-country-value', 'US' );

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

									// Never force addr2 to be required
									if ( $type === 'addr2' ) {
										$addr2_field_array = $field_array;
										$addr2_field_array['required'] = apply_filters( 'yikes-mailchimp-address-2-required', '', $form_id );
									}

									switch( $type ) {

										default:
										case 'addr1':
										case 'addr2':
										case 'city':


											?>
											<label for="<?php echo $field['merge']; ?>" data-attr-name="<?php echo esc_attr( $type ); ?>-field" <?php echo implode( ' ' , $label_array ); ?>>

												<!-- dictate label visibility -->
												<?php if( ! isset( $field['hide-label'] ) ) { ?>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php } ?>
												
												<input <?php if ( $type === 'addr2' ) { echo implode( ' ' , $addr2_field_array ); } else { echo implode( ' ' , $field_array ); } ?> type="text" value="<?php if( isset( $_POST[$field['merge']][$type] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']][$type]; } ?>">

											</label>
											<?php

										break;

										case 'state':

											?>
											<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?> data-attr-name="state-dropdown"<?php if( ! in_array( $default_country, array( 'US' ) ) ) { ?> style="display: none;"<?php } ?>>

												<!-- dictate label visibility -->
												<?php if( ! isset( $field['hide-label'] ) ) { ?>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php } ?>

												<select <?php echo implode( ' ' , $field_array ); ?>>
													<?php include( YIKES_MC_PATH . 'public/partials/shortcodes/templates/state-dropdown.php' ); ?>
												</select>


											</label>
											<?php

										break;

										case 'zip':

											?>
											<label for="<?php echo $field['merge']; ?>" <?php echo implode( ' ' , $label_array ); ?> data-attr-name="zip-input"<?php if( ! in_array( $default_country, array( 'US', 'GB' ) ) ) { ?> style="display: none;"<?php } ?>>

												<?php if( ! isset( $field['hide-label'] ) ) { ?>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php } ?>

												<?php 
													// If zip lookup plugin is installed, the ZIP field comes back as an array and we need to handle it differently...
													$zip_value_to_echo = '';
													if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) {
														if ( is_array( $_POST[$field['merge']] ) && isset( $_POST[$field['merge']]['zip'] ) ) {
															$zip_value_to_echo = $_POST[$field['merge']]['zip'];
														} else {
															$zip_value_to_echo = $_POST[$field['merge']]; 
														}
													} else { 
														$zip_value_to_echo = esc_attr( $default_value ); 
													}
												?>

												<input <?php echo implode( ' ' , $field_array ); ?> type="text" pattern="<?php echo apply_filters( 'yikes-mailchimp-zip-pattern', '\d{5,5}(-\d{4,4})?' ); ?>" title="<?php _e( '5 digit zip code, numbers only' , 'yikes-inc-easy-mailchimp-extender' ); ?>" value="<?php echo $zip_value_to_echo ?>">

											</label>
											<?php

										break;

										case 'country':
											?>

											<script type="text/javascript">
												function checkCountry( e ) {
													var country_value = jQuery( e ).val();
													if( country_value != 'US' ) {
														// fade out the non-US fields
														jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).fadeOut();
														// Great Britain / UK should allow 'zip/postal code'
														if( country_value != 'GB' ) {
															jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeOut();
														} else {
															jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeIn();
														}
													} else {
														if( country_value == 'GB' ) {
															jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeIn();
														} else {
															jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).fadeIn();
															jQuery( e ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeIn();
														}
													}
												}
											</script>

											<?php
												// setup the default country value
												$default_country = apply_filters( 'yikes-mailchimp-default-country-value', 'US' );
											?>

											<label for="<?php echo $field['merge']; ?>" data-attr-name="<?php echo esc_attr( $type ); ?>-field" <?php echo implode( ' ' , $label_array ); ?>>

												<!-- dictate label visibility -->
												<?php if( !isset( $field['hide-label'] ) ) { ?>
													<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
														<?php echo ucwords( apply_filters( 'yikes-mailchimp-address-'.$type.'-label' , esc_attr( $label ) ) ); ?>
													</span>
												<?php } ?>

												<select <?php echo implode( ' ' , $field_array ); ?> onchange="checkCountry(this);return false;">
													<?php include( YIKES_MC_PATH . 'public/partials/shortcodes/templates/country-dropdown.php' ); ?>
												</select>
											</label>
											<?php

									}
									$x++;
								}

								// description
								if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
									<p class="form-field-description">
										<small>
											<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( trim( stripslashes( $field['description'] ) ) ), $form_id ); ?>
										</small>
									</p>
									<?php }
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
										$date_format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'mm/dd';
										break;

									case 'birthday':
										$date_format = ( isset( $field['date_format'] ) ) ? strtolower( $field['date_format'] ) : 'mm/dd';
										break;
								}
								// initialize the datepicker
								?>
									<style>
										.datepicker-dropdown {
											width: 20%;
											padding: .85em .5em !important;
										}
										<?php
											if( wp_is_mobile() ) {
												?>
												.datepicker-dropdown {
													margin-top: 0px;
												}
												<?php
											}
											// isntantiate our admin class to localize our calendar data
											global $wp_locale;
											$admin_class = new Yikes_Inc_Easy_Mailchimp_Forms_Admin( '', '', yikes_easy_mailchimp_extender_get_form_interface() );
											$admin_class->hooks();
											$month_names = array_values( $wp_locale->month );
											$month_names_short = array_values( $wp_locale->month_abbrev );
											$day_names = array_values( $wp_locale->weekday );
											$day_names_short = array_values( $wp_locale->weekday_abbrev );
											$day_names_min = array_values( $wp_locale->weekday_initial );
											$date_format = $admin_class->yikes_jQuery_datepicker_date_format_php_to_js( $date_format, $field['type'] );
											$first_day = get_option( 'start_of_week' );
											$isRTL = $wp_locale->is_rtl();
										?>
									</style>
									<script type="text/javascript">
										jQuery(document).ready(function() {
											jQuery.fn.datepicker.dates['en'] = {
												months: <?php echo json_encode( $month_names ); ?>,
												monthsShort: <?php echo json_encode( $month_names_short ); ?>,
												days: <?php echo json_encode( $day_names ); ?>,
												daysShort: <?php echo json_encode( $day_names_short ); ?>,
												daysMin: <?php echo json_encode( $day_names_min ); ?>,
												dateFormat: <?php echo json_encode( $date_format ); ?>,
												firstDay: <?php echo json_encode( $first_day ); ?>,
												format: <?php echo json_encode( $date_format ); ?>,
												isRTL: <?php echo (int) $isRTL; ?>,
												showButtonPanel: true,
												numberOfMonths: 1,
												today: '<?php _e( 'Today', 'yikes-inc-easy-mailchimp-extender' ); ?>'
											};
											jQuery('input[data-attr-type="<?php echo $field['type']; ?>"]').datepicker().on( 'show', function( e ) {
												var date_picker_height = jQuery('input[data-attr-type="<?php echo $field['type']; ?>"]').css( 'height' );
												date_picker_height = parseInt( date_picker_height.replace( 'px', '' ) ) + parseInt( 15 ) + 'px';
												var date_picker_width = jQuery('input[data-attr-type="<?php echo $field['type']; ?>"]').css( 'width' ).replace( 'px', '' );
												if( date_picker_width > 500 ) {
													date_picker_width = 500;
												}
												jQuery( '.datepicker-dropdown' ).css( 'margin-top', date_picker_height  ).css( 'width', date_picker_width + 'px' );
											});
										});
									</script>
								<?php

								$default_value = ( isset( $field['default'] ) ? esc_attr( $field['default'] ) : '' );
								// store empty number for looping
								$x = 0;

								?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( !isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php } ?>

										<input <?php echo implode( ' ' , $field_array ); ?> type="text" <?php if( $field['type'] == 'date' ) { ?> data-attr-type="date" <?php } else { ?> data-attr-type="birthday" <?php } ?> value="<?php if( isset( $_POST[$field['merge']] ) && $form_submitted != 1 ) { echo $_POST[$field['merge']]; } else { echo esc_attr( $default_value ); } ?>">

										<!-- description -->
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( trim( stripslashes( $field['description'] ) ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php

							break;

							case 'dropdown':
								$default_choice = ( is_array( $field['default_choice'] ) ) ? $field['default_choice'] : array( $field['default_choice'] );
								// store empty number for looping
								$x = 0;
								// hidden labels

									?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>
										<!-- dictate label visibility -->
										<?php if( ! isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ) . '-label'; ?>">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php } ?>

										<select <?php echo implode( ' ' , $field_array ); ?>>
											<?php
												// decode for looping
												$choices = json_decode( $field['choices'], true );

												// If the form was submitted, but failed, let's default to the chosen option
												if( isset( $_POST[ $field['merge'] ] ) && $form_submitted === 0 ) {
													$default_choice = is_array( $_POST[ $field['merge'] ] ) ? $_POST[ $field['merge'] ] : array( $_POST[ $field['merge'] ] );
												}

												foreach( $choices as $choice ) { ?>
													<option 
														value="<?php echo $choice; ?>" 
														<?php if ( in_array( $x, $default_choice ) || in_array( $choice, $default_choice ) ) { echo 'selected="selected"'; } ?>>
														<?php echo esc_attr( stripslashes( $choice ) ); ?>
													</option><?php
													$x++;
												}
											?>
										</select>

										<!-- description -->
										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( trim( stripslashes( $field['description'] ) ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php


								break;

							case 'radio':
							case 'checkbox':
								// remove the ID (as to not assign the same ID to every radio button)
								unset( $field_array['id'] );
								$choices = json_decode( $field['choices'], true );

								// assign a default choice
								$default_choice = ( isset( $field['default_choice'] ) && ! empty( $field['default_choice'] ) ) ? $field['default_choice'] : $choices[0];
								$default_choice = ( is_array( $default_choice ) ) ? $default_choice : array( $default_choice );

								// If the form was submitted, but failed, let's default to the chosen option
								if( isset( $_POST[ $field['merge'] ] ) && $form_submitted === 0 ) {
									$default_choice = is_array( $_POST[ $field['merge'] ] ) ? $_POST[ $field['merge'] ] : array( $_POST[ $field['merge'] ] );
								}

								$count = count( $choices );
								$i = 1;
								$x = 0;

								// hidden labels

									?>
									<label for="<?php echo esc_attr( $field['merge'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( ! isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['merge'] ). '-label'; ?> checkbox-parent-label">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['merge'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php }

										foreach( $choices as $choice ) {
											?>
											<label for="<?php echo esc_attr( $field['merge'] ) . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $i === $count ) { ?> last-selection<?php } ?>" <?php if( $i == 1 ) { echo $field_array['required']; } ?>>
												<input 
													type="<?php echo esc_attr( $field['type'] ); ?>" 
													name="<?php echo $field['merge']; ?>" 
													id="<?php echo $field['merge'] . '-' . $i; ?>" 
													<?php if ( in_array( $x, $default_choice ) || in_array( $choice, $default_choice ) ) { echo 'checked="checked"'; } ?> 
													value="<?php echo esc_attr( $choice ); ?>">
												<span class="<?php echo esc_attr( $field['merge'] ). '-label'; ?>"><?php echo stripslashes( $choice ); ?></span>
											</label>
											<?php
											$i++;
											$x++;
										}

										// description
										if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-description', esc_attr( trim( stripslashes( $field['description'] ) ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php

								break;

						}

					} else { // Loop over interest groups

						// Get the default choice(s) from the field settings and turn them into an array if not already
						$default_choice = ( isset( $field['default_choice'] ) ) ? $field['default_choice'] : '';
						$default_choice = ( is_array( $default_choice ) ) ? $default_choice : array( $default_choice );

						// get our groups
						$groups = ( isset( $field['groups'] ) && ! empty( $field['groups'] ) ) ? json_decode( $field['groups'], true ) : array();

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

									?>
									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>
										<?php if( ! isset( $field['hide-label'] ) ) { ?>
											<!-- dictate label visibility -->
											<span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?> checkbox-parent-label">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( $field['label'] ) ); ?>
											</span>
									<?php
										}

										// Display Submission Errors
										if( ! empty( $missing_required_checkbox_interest_groups ) ) {
											if( in_array( $field['group_id'], $missing_required_checkbox_interest_groups ) ) {
												?>
													<p class="yikes-mailchimp-required-interest-group-error">
														<?php echo apply_filters( 'yikes-mailchimp-interest-group-checkbox-error', __( 'This field is required.', 'yikes-inc-easy-mailchimp-extender' ), $form_id ); ?>
													</p>
												<?php
											}
										}

										foreach ( $groups as $group_id => $name ) {

											// If the form was submitted and failed, set the submitted/chosen values as the default
											if( isset( $_POST[ 'group-' . $field['group_id'] ] ) && $form_submitted === 0 ) {

												// Format default choice as array
												$default_choice = ( is_array( $_POST[ 'group-' . $field['group_id'] ] ) ) ? $_POST[ 'group-' . $field['group_id'] ] : array( $_POST[ 'group-' . $field['group_id'] ] );
											}

											?>
											<label for="<?php echo $field['group_id'] . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $x === $count ) { ?> last-selection<?php } ?>">
												<input 
													<?php if( isset( $field['require'] ) && $field['require'] == 1 ) { if ( $field['type'] !== 'checkboxes' ) { ?> required="required" <?php } ?> 
													class="yikes-interest-group-required" <?php } ?> 
													type="<?php echo $type; ?>" 
													name="group-<?php echo $field['group_id']; ?>[]" 
													id="<?php echo $field['group_id'] . '-' . $i; ?>" 
													<?php if ( in_array( $group_id, $default_choice ) ) { echo 'checked="checked"'; } ?> 
													value="<?php echo $group_id; ?>">
													<?php echo $name; ?>
											</label>
											<?php
											$i++;
											$x++;
										}

										// description
										if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['group_id'] . '-description', esc_attr( trim( $field['description'] ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>

									</label>
									<?php

								break;

							case 'dropdown':

									?>

									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>
										<!-- dictate label visibility -->
										<?php if( ! isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?>">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( $field['label'] ) ); ?>
											</span>
										<?php } ?>

										<select <?php echo implode( ' ' , $field_array ); ?>>
											<?php
												$i = 0;
												foreach( $groups as $group_id => $name ) { 

													// If the form was submitted and failed, set the submitted/chosen values as the default
													if( isset( $_POST[ 'group-' . $field['group_id'] ] ) && $form_submitted === 0 ) {

														// Format default choice as array
														$default_choice = ( is_array( $_POST[ 'group-' . $field['group_id'] ] ) ) ? $_POST[ 'group-' . $field['group_id'] ] : array( $_POST[ 'group-' . $field['group_id'] ] );
													}
											?>
													<option 
														<?php if ( in_array( $group_id, $default_choice ) ) { echo 'selected="selected"'; } ?> 
														value="<?php echo $group_id; ?>">
														<?php echo esc_attr( $name ); ?>
													</option>
											<?php 
												$i++;
												}
											?>
										</select>

										<?php if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['group_id'] . '-description', esc_attr( trim( $field['description'] ) ), $form_id ); ?>
												</small>
											</p>
										<?php } ?>


									</label><?php


								break;

							case 'hidden':
								$i = 0; // used to select our checkboxes/radios
								$x = 1; // used to find the last item of our array

									?>

									<label for="<?php echo esc_attr( $field['group_id'] ); ?>" <?php echo implode( ' ' , $label_array ); ?>>

										<!-- dictate label visibility -->
										<?php if( ! isset( $field['hide-label'] ) ) { ?>
											<span class="<?php echo esc_attr( $field['group_id'] ) . '-label'; ?> checkbox-parent-label" style="display:none;">
												<?php echo apply_filters( 'yikes-mailchimp-'.$field['group_id'].'-label' , esc_attr( stripslashes( $field['label'] ) ) ); ?>
											</span>
										<?php }


										foreach( $groups as $group ) {
											?>
											<label for="<?php echo $field['group_id'] . '-' . $i; ?>" class="yikes-easy-mc-checkbox-label <?php echo implode( ' ' , $custom_classes ); if( $x === $count ) { ?> last-selection<?php } ?>" style="display:none;">
												<input type="radio" name="<?php echo $field['group_id']; ?>[]" id="<?php echo $field['group_id'] . '-' . $i; ?>" <?php if( $field['type'] == 'checkboxes' ) { if( in_array( $i , $default_choice ) ) { echo 'checked="checked"'; } } else { checked( ( isset( $default_choice ) && is_array( $default_choice ) ) ? $default_choice[0] : $default_choice , $i ); } ?> value="<?php echo esc_attr( $group['name'] ); ?>">
												<?php echo esc_attr( stripslashes( str_replace( '' , '\'', $group['name'] ) ) ); ?>
											</label>
											<?php
											$i++;
											$x++;
										}

										// description
										if( isset( $field['description'] ) && trim( $field['description'] ) != '' ) { ?>
											<p class="form-field-description">
												<small>
													<?php echo apply_filters( 'yikes-mailchimp-' . $field['group_id'] . '-description', esc_attr( trim( stripslashes( $field['description'] ) ) ), $form_id ); ?>
												</small>
											</p>
										<?php }


									?></label><?php

								break;

						}
					} // end interest groups
				}

				do_action( 'yikes-mailchimp-additional-form-fields', $form_data );

				/* if we've enabled reCAPTCHA protection */
				if( isset( $recaptcha_box ) ) {
					echo $recaptcha_box;
				}
				if( is_user_logged_in() ) {
					$admin_class = ( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) ? ' admin-logged-in' : '';
				} else {
					$admin_class = '';
				}
				?>

				<!-- Honeypot Trap -->
				<input type="hidden" name="yikes-mailchimp-honeypot" id="yikes-mailchimp-honeypot" value="">

				<!-- List ID -->
				<input type="hidden" name="yikes-mailchimp-associated-list-id" id="yikes-mailchimp-associated-list-id" value="<?php echo $form_data['list_id']; ?>">

				<!-- The form that is being submitted! Used to display error/success messages above the correct form -->
				<input type="hidden" name="yikes-mailchimp-submitted-form" id="yikes-mailchimp-submitted-form" value="<?php echo $form_id; ?>">

				<!-- Submit Button -->
				<?php
					if( $form_inline ) {
						$submit_button_label_classes = array( 'empty-label' );
						// If the number of fields, is equal to the hidden label count, add our class
						// eg: All field labels are set to hidden.
						if ( absint( $field_count ) === absint( $hidden_label_count ) ) {
							$submit_button_label_classes[] = 'labels-hidden';
						}
						echo '<label class="empty-form-inline-label submit-button-inline-label"><span class="' . implode( ' ', $submit_button_label_classes ) . '">&nbsp;</span>';
					}
					// display the image or text based button
					if( $submit_button_type == 'text' ) {
						echo apply_filters( 'yikes-mailchimp-form-submit-button', '<button type="submit" class="' . apply_filters( 'yikes-mailchimp-form-submit-button-classes', 'yikes-easy-mc-submit-button yikes-easy-mc-submit-button-' . esc_attr( $form_data['id'] ) . ' btn btn-primary' . $submit_button_classes . $admin_class, $form_data['id'] ) . '"> <span class="yikes-mailchimp-submit-button-span-text">' .  apply_filters( 'yikes-mailchimp-form-submit-button-text', esc_attr( stripslashes( $submit ) ), $form_data['id'] ) . '</span></button>', $form_data['id'] );
					} else {
						echo apply_filters( 'yikes-mailchimp-form-submit-button', '<input type="image" alt="' . apply_filters( 'yikes-mailchimp-form-submit-button-text', esc_attr( stripslashes( $submit ) ), $form_data['id'] ) . '" src="' . $submit_button_image . '" class="' . apply_filters( 'yikes-mailchimp-form-submit-button-classes', 'yikes-easy-mc-submit-button yikes-easy-mc-submit-button-image yikes-easy-mc-submit-button-' . esc_attr( $form_data['id'] ) . ' btn btn-primary' . $submit_button_classes . $admin_class, $form_data['id'] ) . '">', $form_data['id'] );
					}
					if( $form_inline ) {
						echo '</label>';
					}
				?>
				<!-- Nonce Security Check -->
				<?php wp_nonce_field( 'yikes_easy_mc_form_submit', 'yikes_easy_mc_new_subscriber' ); ?>

			</form>
			<!-- MailChimp Form generated by Easy Forms for MailChimp v<?php echo YIKES_MC_VERSION; ?> (https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) -->

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
		if ( ! current_user_can( 'manage_options' ) ) {
			$impressions = $form_data['impressions'] + 1;
			$interface->update_form_field( $form_id, 'impressions', $impressions );
		}

	?>
	</section>
	<?php

	return ob_get_clean();

}
add_shortcode( 'yikes-mailchimp', 'process_mailchimp_shortcode' );
