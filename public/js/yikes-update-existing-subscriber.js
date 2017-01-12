/**
*	Trigger an email to be sent over to the user to update existing details
*	- fires when the 'click here' link is clicked
*	@since 6.0.4.1
*/
jQuery( document ).ready( function() {
	jQuery( 'body' ).on( 'click', '.send-update-email', function() {

		/* Submit an ajax request to send off the update email */
		var data = {
			'action': 'easy_forms_send_email',
			'user_email': jQuery( this ).attr( 'data-user-email' ),
			'list_id': jQuery( this ).attr( 'data-list-id' ),
			'form_id': jQuery( this ).attr( 'data-form-id' ),
		};
		jQuery( this ).parent( 'p' ).fadeTo( 'fast', .75 ).append( '<img src="' + update_subscriber_details_data.preloader_url + '" class="update-email-preloader" />' );
		jQuery.post( update_subscriber_details_data.ajax_url, data, function(response) {
			if( response.success ) {
				jQuery( '.yikes-easy-mc-error-message' ).removeClass( 'yikes-easy-mc-error-message' ).addClass( 'yikes-easy-mc-success-message' ).html( response.data.response_text );
			} else {
				jQuery( '.yikes-easy-mc-error-message' ).fadeTo( 'fast', 1 ).html( response.data.response_text );
			}
		});
		return false;
	});
});