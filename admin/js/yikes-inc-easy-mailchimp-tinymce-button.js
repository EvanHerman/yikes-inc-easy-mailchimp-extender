( function() {

	var forms = JSON.parse( localized_data.forms );

	/* loop over the stored options and decode them for display back to the user */
	/* used to escape quotes and add appropriate spaces */
	var array_length = parseInt( forms.length - 1 );

	var i = 0;
	while( i <= array_length ) {
		forms[i].text = decodeURI(forms[i].text.replace( /\+/g , ' ' ) );
		i++;
	}

    tinymce.PluginManager.add( 'yks_mc_tinymce_button', function( editor ) {

      /* Add a button that opens a window */
      editor.addButton( 'yks_mc_tinymce_button_key', {

				image: forms.tinymce_icon,

				title: localized_data.button_title,

				onclick: function() {

						/* Open window */
						editor.windowManager.open( {

							title: localized_data.popup_title,

							body: [
								{
									type: 'listbox',
									name: 'list_id',
									label: localized_data.list_id_label,
									values: forms
								},
								{
									type: 'checkbox',
									name: 'show_title',
									label: localized_data.show_title_label
								},
								{
									type: 'checkbox',
									name: 'show_description',
									label: localized_data.show_description_label
								},
								{
									type: 'textbox',
									name: 'submit_button_text',
									label: localized_data.submit_button_text_label
								},
								{
									type: 'container',
									name: 'submit_button_message',
									html: localized_data.submit_button_message
								}
							],

							id: 'yikes_mailchimp_tinyMCE_modal', /* and an ID to the modal, to target it easier */

							onsubmit: function( e ) {

								/* Insert content when the window form is submitted */
								/* store the mailchimp list ID */
								var mailChimp_form_id = e.data.list_id;

								/* store the submit button text */
								var submit_button_text = e.data.submit_button_text;

								/* store if we should show the description */
								var show_title = e.data.show_title;

								/* store if we should show the description */
								var show_description = e.data.show_description;

								if ( mailChimp_form_id == '-' ) {

									jQuery( '#yikes_mailchimp_tinyMCE_modal' ).find( '.mce-foot' ).find( '.mce-widget' ).hide();

									jQuery( '#yikes_mailchimp_tinyMCE_modal-absend' ).next().prepend( '<div class="error"><p>' + localized_data.alert_translated + '</p></div>' );

									return false;

								} else {

									var shortcode_atts = [];

									shortcode_atts.push( 'form="'+mailChimp_form_id+'"' );

									if( true === show_title ) {

										shortcode_atts.push( 'title="1"' );

									}

									if( true === show_description ) {

										shortcode_atts.push( 'description="1"' );

									}

									// If they didn't enter something for submit, don't add it to the shortcode.
									if ( '' !== submit_button_text ) {

										shortcode_atts.push('submit="' + submit_button_text + '"');

									}

									editor.insertContent( '[yikes-mailchimp '+shortcode_atts.join( ' ' )+']' );

								}

							}

						} );

					}

			} );

		} );

} )();
