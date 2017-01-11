window.Yikes_Mailchimp_Ajax = window.Yikes_Mailchimp_Ajax || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	app.l10n = window.yikes_mailchimp_ajax || {};

	$( document ).ready( function() {
		var body = $( 'body' );

		/* On Submission, run our ajax shtuff */
		body.on( 'submit' , '.yikes-easy-mc-form' , function() {

			/* store which form was submitted */
			var submitted_form = $( this );

			/* Fade down the form */
			submitted_form.find( 'input, label, button' ).not( ':hidden' ).fadeTo( 'fast', .5 );

			/* Append our preloader */
			submitted_form.append( '<img src="' + app.l10n.preloader_url + '" class="yikes-mailchimp-preloader" />')

			var original_submit_button_text = submitted_form.find( '.yikes-easy-mc-submit-button' ).text();

			submitted_form.find( '.yikes-easy-mc-submit-button' ).text( '' ).html( '<img src="' + app.l10n.loading_dots + '" class="loading-dots" />' );

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
				submitted_form.find( '.yikes-easy-mc-submit-button' ).html( '' ).text( original_submit_button_text );

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
					submitted_form.find( '.yikes-easy-mc-submit-button' ).html( '' ).text( original_submit_button_text );

					/* Success */
					if( response.success ) {
						response = response.data;
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
						if( response.redirection == 1 ) {
							submitted_form.before( response.redirect );
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
						if( $( '.yikes-easy-mc-form-description-' + form_id ).length > 0 ) {
							$( '.yikes-easy-mc-form-description-' + form_id ).before( '<p class="yikes-easy-mc-error-message yikes-easy-mc-error-message-' + form_id + '" yikes-easy-mc-hidden"> ' + response.response + '</p>' );
						} else {
							var response_message = ( typeof( response ) !== 'undefined' && typeof( response.response ) !== 'undefined' ) ? response.response : 'Error collecting the API response.'
							submitted_form.before( '<p class="yikes-easy-mc-error-message yikes-easy-mc-error-message-' + form_id + ' yikes-easy-mc-hidden">' + response_message + '</p>' );
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

		/**
		*	When the user clicks the 'send update email' eg: 'click here' link,
		*	-- fire off the send email function
		*	@since 6.0.4.1
		*/
		body.on( 'click', '.send-update-email', function() {
			/* Submit an ajax request to send off the update email */
			var data = {
				'action': 'easy_forms_send_email',
				'user_email': jQuery( this ).attr( 'data-user-email' ),
				'list_id': jQuery( this ).attr( 'data-list-id' ),
				'form_id': jQuery( this ).attr( 'data-form-id' ),
			};
			jQuery( this ).parent( 'p' ).fadeTo( 'fast', .75 ).append( '<img src="' + app.l10n.preloader_url + '" class="update-email-preloader" />' );
			/* We can also pass the url value separately from ajaxurl for front end AJAX implementations */
			jQuery.post( app.l10n.ajax_url, data, function( response ) {
				if( response.success ) {
					jQuery( '.yikes-easy-mc-error-message' ).removeClass( 'yikes-easy-mc-error-message' ).addClass( 'yikes-easy-mc-success-message' ).html( response.data.response_text );
				} else {
					jQuery( '.yikes-easy-mc-error-message' ).fadeTo( 'fast', 1 ).html( response.data.response_text );
				}
			});
			return false;
		});

	});

})( window, document, jQuery, Yikes_Mailchimp_Ajax );
