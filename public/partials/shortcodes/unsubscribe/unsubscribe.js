( function( $ ) {

	$( document ).ready( function() {
		let body = $( 'body' );

		// Hook into form submission
		body.on( 'submit' , '.yikes-mailchimp-unsubscribe-form' , function() {

			// Get our variables
			let form     = $( this );
			let nonce    = form.find( '.yikes-mailchimp-unsubscribe-nonce' ).val();
			let email    = form.find( '.yikes-mailchimp-unsubscribe-email' ).val();
			let list_id  = form.find( '.yikes-mailchimp-unsubscribe-list-id' ).val();
			let hp       = form.find( '.yikes-mailchimp-honeypot' ).val();
			let feedback = form.siblings( '.yikes-mailchimp-unsubscribe-feedback' );
			let button   = form.find( '.yikes-mailchimp-unsubscribe-submit-button' );

			// Hide the feedback area
			feedback.removeClass( 'yikes-mailchimp-unsubscribe-error yikes-mailchimp-unsubscribe-success' ).fadeOut( function() {

				// Add the spinner gif
				button.after( '<img class="unsubscribe-loader" alt="form loading..." src="' + yikes_unsubscribe_data.loader + '"/>' );
			});

			let data = {
				nonce  : nonce,
				email  : email,
				list_id: list_id,
				hp     : hp,
				action : 'yikes_mailchimp_unsubscribe'
			}

			$.post( yikes_unsubscribe_data.ajax_url, data, function( response ) {
				console.log( response );

				// Remove spinner
				form.find( '.unsubscribe-loader' ).remove();

				if ( typeof response.success === 'boolean' ) {

					if ( response.success === true ) {

						// We good
						feedback.text( yikes_unsubscribe_data.success ).addClass( 'yikes-mailchimp-unsubscribe-success' ).fadeIn();

						// Remove input field value
						form.find( '.yikes-mailchimp-unsubscribe-email' ).val( '' );

					} else {

						if ( response.data === '1' || response.data === '2' || response.data === '3' ) {

							// 1 = Nonce
							// 2 = Honeypot
							// 3 = Empty email / list ID
							feedback.text( yikes_unsubscribe_data.error1 ).addClass( 'yikes-mailchimp-unsubscribe-error' ).fadeIn();

						} else if ( response.data === '4' ) {

							// Resource not found, e.g. subscriber doesn't exist
							feedback.text( yikes_unsubscribe_data.error2 ).addClass( 'yikes-mailchimp-unsubscribe-error' ).fadeIn();

						}
					}
				} else {

					// Something went wrong...
					// Show generic error message
					feedback.text( yikes_unsubscribe_data.error1 ).addClass( 'yikes-mailchimp-unsubscribe-error' ).fadeIn();
				}
			});

			// Prevent the form from submitting
			return false;
		});
	});

})( jQuery );