window.Yikes_Mailchimp_Ajax = window.Yikes_Mailchimp_Ajax || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	app.l10n = window.yikes_mailchimp_ajax || {};

	$( document ).ready( function() {
		var body = $( 'body' );

		/* On Submission, run our ajax shtuff */
		body.on( 'submit' , '.yikes-easy-mc-form' , function() {

			// Store which form was submitted
			var submitted_form = $( this );

			// Add a class to the form while it's submitted (as of 6.3.0)
			submitted_form.addClass( 'yikes-mc-submitted-form-loading' );

			// Fade down the form
			submitted_form.find( 'input, label, button' ).not( ':hidden' ).fadeTo( 'fast', .5 );

			// Append our preloader
			submitted_form.append( '<img src="' + app.l10n.preloader_url + '" class="yikes-mailchimp-preloader" />')

			// Remove the missing required fields class
			$( '.yikes-mc-required-field-not-filled' ).removeClass( 'yikes-mc-required-field-not-filled' );

			// As of 6.3.0 we just hide the button text instead of removing it, so hide:
			$( '.yikes-mailchimp-submit-button-span-text' ).hide();

			// And then append the loading dots gif
			submitted_form.find( '.yikes-easy-mc-submit-button' ).append( '<img src="' + app.l10n.loading_dots + '" class="loading-dots yikes-mc-loading-dots" />' );

			// Get the form id
			var form_id = submitted_form.attr( 'data-attr-form-id' );

			/* Checkbox Interest Group Error */
			var required_fields_left_blank = [];

			/* Check for any required interest groups */
			if( submitted_form.find( '.yikes-interest-group-required' ).length > 0 ) {
				/* loop and make sure that it's checked */
				submitted_form.find( '.yikes-interest-group-required' ).each( function() {
					var id = jQuery( this ).attr( 'name' );
					var interest_group_id = id.replace( '[]', '' );
					if( submitted_form.find( 'input[name="'+interest_group_id+'[]"]:checked' ).length == 0 ) {
						required_fields_left_blank[interest_group_id] = submitted_form.find( 'span.' + interest_group_id + '-label' ).text();
					}
				});
			}

			/* Loop, display the errors and prevent form submission from occuring */
			if ( required_fields_left_blank.length > 0 ) {
				/* Remove any visible checkbox group errors */
				if( submitted_form.find( '.yikes-mailchimp-required-interest-group-error' ).length > 0 ) {
					submitted_form.find( '.yikes-mailchimp-required-interest-group-error' ).fadeOut( 'fast', function() {
						submitted_form.find( '.yikes-mailchimp-required-interest-group-error' ).remove();
						for ( var field_id in required_fields_left_blank ) {
							submitted_form.find( 'span.'+field_id+'-label' ).after( '<p class="yikes-mailchimp-required-interest-group-error">'+app.l10n.interest_group_checkbox_error+'</p>' );
						}
					});
				} else {
					for ( var field_id in required_fields_left_blank ) {
						submitted_form.find( 'span.'+field_id+'-label' ).after( '<p class="yikes-mailchimp-required-interest-group-error">'+app.l10n.interest_group_checkbox_error+'</p>' );
					}
				}

				submitted_form.find( '.yikes-easy-mc-submit-button' ).removeAttr( 'disabled', 'disabled' );
				submitted_form.find( 'input, label, button' ).not( ':hidden' ).fadeTo( 'fast', 1 );
				submitted_form.find( '.yikes-mailchimp-preloader' ).remove();
				
				//submitted_form.find( '.yikes-easy-mc-submit-button' ).html( '' ).html( original_submit_button_text );

				// As of 6.3.0 we just show/hide the button text instead of removing it, so:
				// Remove loading dots && show button text
				$( '.yikes-mc-loading-dots' ).remove();
				$( '.yikes-mailchimp-submit-button-span-text' ).show();

				// As of 6.3.0 we add a class to the form, so remove it if we're here
				submitted_form.removeClass( 'yikes-mc-submitted-form-loading' );

				return false;
			}

			/* disable the button to prevent double click */
			submitted_form.find( '.yikes-easy-mc-submit-button' ).attr( 'disabled' , 'disabled' );

			/* hide our previously displayed success and error messages  */
			$( '.yikes-easy-mc-error-message' ).remove();
			$( '.yikes-easy-mc-success-message' ).remove();

			/* build our data */
			var data = {
				'action' : 'process_form_submission',
				'form_data' : submitted_form.serialize(),
				'form_id' : form_id,
				'page_data' : app.l10n.page_data,
				'ajax_security_nonce' : app.l10n.ajax_security_nonce
			};

			/* submit our ajax request */
			$.ajax({
				url: app.l10n.ajax_url,
				type: 'POST',
				data: data,
				success : function( response, textStatus, jqXHR) {

					submitted_form.find( 'input, label, button' ).not( ':hidden' ).fadeTo( 'fast', 1 );
					submitted_form.find( '.yikes-mailchimp-preloader' ).remove();

					// As of 6.3.0 we just show/hide the button text instead of removing it, so:
					// Remove loading dots && show button text
					$( '.yikes-mc-loading-dots' ).remove();
					$( '.yikes-mailchimp-submit-button-span-text' ).show();

					// As of 6.3.0 we add a class to the form, so remove it if we're here
					submitted_form.removeClass( 'yikes-mc-submitted-form-loading' );

					/* Success */
					if( response.success ) {
						response = response.data;

						// Fire off our Google Analytics for a successful submission
						if ( typeof( yikes_mailchimp_google_analytics_success ) === 'function' ) { 
							yikes_mailchimp_google_analytics_success( response ); 
						}

						if( response.hide == 1 ) {
							/* hide the description if visible */
							if( $( '.yikes-easy-mc-form-description-'+form_id ).length > 0 ) {
								$( '.yikes-easy-mc-form-description-'+form_id ).hide();
							}
							/* hide the form */
							submitted_form.hide();
						}
						if( $( '.yikes-easy-mc-form-description-'+form_id ).length > 0 ) {
							 $( '.yikes-easy-mc-form-description-'+form_id ).before( '<p class="yikes-easy-mc-success-message yikes-easy-mc-success-message-'+form_id+' yikes-easy-mc-hidden">'+response.response+'</p>' );
						} else {
							submitted_form.before( '<p class="yikes-easy-mc-success-message yikes-easy-mc-success-message-'+form_id+' yikes-easy-mc-hidden">'+response.response+'</p>' );
						}
						/* fade in our success message */
						$( '.yikes-easy-mc-success-message-'+form_id ).fadeIn();
						$( '.yikes-mailchimp-required-interest-group-error' ).remove();

						/* redirect if setup */
						if( response.redirection === 1 ) {

							var redirect_url 	= response.redirect;
							var redirect_timer  = response.redirect_timer;
							var new_window		= response.new_window;
							var new_window_code = new_window === '1' ? '_blank' : '_self';

							setTimeout( 
								function() {
									window.open( redirect_url, new_window_code );
								},
								redirect_timer
							);
						}
						/* clear the inputs - but don't clear submit button, radio, select, list_id, or form */
						submitted_form.find( 'input' ).not( '.yikes-easy-mc-submit-button, input[type="radio"], input[type="select"], input[type="checkbox"], #yikes-mailchimp-associated-list-id, #yikes-mailchimp-submitted-form' ).val( '' );
						/* ajax to increase submission count by 1 */
						var new_data = {
							'action' : 'increase_submission_count',
							'form_id' : form_id
						};
						$.ajax({
							url: app.l10n.ajax_url,
							type: 'POST',
							data: new_data,
							success : function( response, textStatus, jqXHR) {
								/* console.log( 'submission count increased by 1' ); */
							},
							error : function( jqXHR, textStatus, errorThrown ) {
								/* display the error back to the user in the console */
								console.error( errorThrown );
							}
						});
						/* console.log( 'Successfully submit subscriber data to MailChimp.' ); */
					} else {
						response = response.data;

						// Fire off our Google Analytics for an unsuccessful submission
						if ( typeof( yikes_mailchimp_google_analytics_failure ) === 'function' ) { 
							yikes_mailchimp_google_analytics_failure( response ); 
						}

						if( $( '.yikes-easy-mc-form-description-' + form_id ).length > 0 ) {
							$( '.yikes-easy-mc-form-description-' + form_id ).before( '<p class="yikes-easy-mc-error-message yikes-easy-mc-error-message-' + form_id + '" yikes-easy-mc-hidden"> ' + response.response + '</p>' );
						} else {
							var response_message = ( typeof( response ) !== 'undefined' && typeof( response.response ) !== 'undefined' ) ? response.response : 'Error collecting the API response.'
							submitted_form.before( '<p class="yikes-easy-mc-error-message yikes-easy-mc-error-message-' + form_id + ' yikes-easy-mc-hidden">' + response_message + '</p>' );
						}

						// Check if we found a required field that's missing (server side check)
						if ( typeof( response ) !== 'undefined' && typeof( response.missing_required_field ) !== 'undefined' && response.missing_required_field === true ) {
							if ( typeof ( response.missing_required_field_data ) !== 'undefined' ) {

								// Capture the field data and highlight the field
								var field_data = response.missing_required_field_data;
								var is_interest_group = ( typeof( response.is_interest_group ) !== 'undefined' ) ? response.is_interest_group : false;
								highlight_missing_required_fields( field_data, is_interest_group );
							}
						}

						// Fade in the error message
						$( '.yikes-easy-mc-error-message' ).fadeIn();
					}
				},
				error : function( jqXHR, textStatus, errorThrown ) {  /* someother error is happening, and should be investigated... */
					/* alert( errorThrown ); */
					console.error( errorThrown );
					console.log( jqXHR );
					console.log( textStatus );
				},
				complete : function( jqXHR, textStatus ) {
					/* console.log( 'Yikes Easy MailChimp AJAX submission complete.' ); */
					/* enable the button to prevent double click */
					submitted_form.find( '.yikes-easy-mc-submit-button' ).removeAttr( 'disabled' , 'disabled' );
				}
			});
			/* prevent default form action */
			return false;
		});

	});

	function highlight_missing_required_fields( field_data, is_interest_group ) {
		if ( typeof ( field_data ) !== 'undefined' ) {

			$.each( field_data, function( merge_label, field ) {
				if ( is_interest_group === true ) {

					// We might be hiding labels, so for interest groups we need to check if the label.span (label text) exists
					if ( $( 'span.' + merge_label + '-label' ).length > 0 ) {
						$( 'span.' + merge_label + '-label' ).addClass( 'yikes-mc-required-field-not-filled' );
					} else {
						// If it doesn't exist, then try to add it to the label (the label wraps the whole input/select/radio field)
						$( '.' + merge_label + '-label' ).addClass( 'yikes-mc-required-field-not-filled' );
					}
				} else {
					$( 'label[for="' + merge_label + '"]' ).children( 'input').addClass( 'yikes-mc-required-field-not-filled' );
				}
			});
		}
	}

})( window, document, jQuery, Yikes_Mailchimp_Ajax );
