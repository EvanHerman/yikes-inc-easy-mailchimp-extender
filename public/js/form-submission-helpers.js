
jQuery( document ).ready( function() {

	// Listener for the country field
	jQuery( 'select[data-country="true"]' ).change( function() {
		var country_value = jQuery( this ).val();
		yikes_mc_toggle_state_fields_dropdown( this, country_value );
		yikes_mc_toggle_zip_field_visibility( this, country_value );
		yikes_mc_toggle_state_field_visibility( this, country_value );
	});
	jQuery( 'select[data-country="true"]' ).trigger( 'change' );


	/**
	*	Trigger an email to be sent over to the user to update existing details
	*	- fires when the 'click here' link is clicked
	*	@since 6.0.4.1
	*/
	jQuery( 'body' ).on( 'click', '.send-update-email', function() {

		/* Submit an ajax request to send off the update email */
		var data = {
			'action': 'easy_forms_send_email',
			'user_email': jQuery( this ).attr( 'data-user-email' ),
			'list_id': jQuery( this ).attr( 'data-list-id' ),
			'form_id': jQuery( this ).attr( 'data-form-id' ),
		};
		jQuery( this ).parent( 'p' ).fadeTo( 'fast', .75 ).append( '<img src="' + form_submission_helpers.preloader_url + '" class="update-email-preloader" />' );
		jQuery.post( form_submission_helpers.ajax_url, data, function(response) {
			if( response.success ) {
				jQuery( '.yikes-easy-mc-error-message' ).removeClass( 'yikes-easy-mc-error-message' ).addClass( 'yikes-easy-mc-success-message' ).html( response.data.response_text );
			} else {
				jQuery( '.yikes-easy-mc-error-message' ).fadeTo( 'fast', 1 ).html( response.data.response_text );
			}
		});
		return false;
	});
});

/**
* Show/Hide zip-address field based on the chosen country.
*
* @param object | clicked_element	| A reference to the clicked element - the country dropdown (JavaScript's `this`)
* @param string | country_value		| The value of the country dropdown
*/
function yikes_mc_toggle_zip_field_visibility( clicked_element, country_value ) {

	// form_submission_helpers.countries_with_zip is a filterable array, passed through via the wp_localize_script function
	var countries_with_zip_code_field = form_submission_helpers.countries_with_zip;

	if ( typeof( countries_with_zip_code_field[ country_value ] ) !== 'undefined' ) {
		jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeIn();
	} else {
		jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="zip-input"]' ) ).fadeOut();
	}
}

/**
* Show/Hide state-address field based on the chosen country.
*
* If the country has states in the dropdown: show states field
* If the country does not have states in the dropdown: do not show states field 
*
* @param object | clicked_element	| A reference to the clicked element - the country dropdown (JavaScript's `this`)
* @param string | country_value		| The value of the country dropdown
*/
function yikes_mc_toggle_state_field_visibility( clicked_element, country_value ) {
	var country_has_states = yikes_mc_does_country_have_states( clicked_element, country_value );
	if ( country_has_states === true ) {
		jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).fadeIn();
	} else {
		jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).fadeOut();
	}
}

/**
* Control which items display in the state dropdown.
*
*
* @param object | clicked_element	| A reference to the clicked element - the country dropdown (JavaScript's `this`)
* @param string | country_value		| The value of the country dropdown
*/
function yikes_mc_toggle_state_fields_dropdown( clicked_element, country_value ) {

	// Loop through all of the options in the state dropdown
	jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).children( 'select' ).children( 'option' ).each( function() {
		if ( jQuery( this ).data( 'country' ) === country_value ) {
			jQuery( this ).show();
		} else {
			jQuery( this ).hide();
		}
	});
}

/**
* Check if the country has states in the dropdown.
*
*
* @param object | clicked_element	| A reference to the clicked element - the country dropdown (JavaScript's `this`)
* @param string | country_value		| The value of the country dropdown
* @return bool	| 					| True if the country has states, false if the country does not
*/
function yikes_mc_does_country_have_states( clicked_element, country_value ) {
	var country_has_states = false;
	jQuery( clicked_element ).parents( '.yikes-mailchimp-container' ).find( jQuery( 'label[data-attr-name="state-dropdown"]' ) ).children( 'select' ).children( 'option' ).each( function() {
		if ( jQuery( this ).data( 'country' ) === country_value ) {
			country_has_states = true;

			// To exit the anonymous function (terminate the .each loop)
			return false;
		}
	});
	return country_has_states;
}