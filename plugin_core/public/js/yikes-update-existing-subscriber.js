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
		};
		jQuery( this ).parent( 'p' ).fadeTo( 'fast', .75 ).append( '<img src="' + update_subscriber_details_data.preloader_url + '" class="update-email-preloader" />' );
		/* We can also pass the url value separately from ajaxurl for front end AJAX implementations */
		jQuery.post( update_subscriber_details_data.ajax_url, data, function(response) {
			if( response.data.response_text ) {
				jQuery( '.yikes-easy-mc-error-message' ).fadeTo( 'fast', 1 ).html( response.data.response_text );
			}
		});
		return false;
	});
});