( function() {

		// grab the form data which was passed along
		var values = jQuery.parseJSON( forms.data );
		var values_length = forms.data.length;
		
		if ( values_length == 0 ) {
			values = [
				{text: "Please Import Some MailChimp Lists", value: "-"}
			];
		}
	
    tinymce.PluginManager.add( 'yks_mc_tinymce_button', function( editor ) {
	
        // Add a button that opens a window
        editor.addButton( 'yks_mc_tinymce_button_key', {
			
            image: forms.tinymce_icon,
			title: 'Yikes Easy MailChimp Forms',
            onclick: function() {
                // Open window
				if ( values_length != 0 ) {
					editor.windowManager.open( {
						title: 'Select Your MailChimp Form',
						body: [
							{
								type: 'listbox',
								name: 'list_id',
								label: 'MailChimp List',
								values: values
							},
							{
								type: 'checkbox',
								name: 'show_title',
								label: 'Display Form Title'
							},
							{
								type: 'checkbox',
								name: 'show_description',
								label: 'Display Form Description'
							},
							{
								type: 'textbox',
								name: 'submit_button_text',
								label: 'Submit Button Text'
							},
						],
						id: 'yikes_mailchimp_tinyMCE_modal', // and an ID to the modal, to target it easier
						onsubmit: function( e ) {
							// Insert content when the window form is submitted
							// store the mailchimp list ID
							var mailChimp_form_id = e.data.list_id;
							// store the submit button text
							var submit_button_text = e.data.submit_button_text;
							// store if we should show the description
							var show_title = e.data.show_title;
							// store if we should show the description
							var show_description = e.data.show_description;
							
							// check the submit button text
							// if empty, default it to Submit
							// if not empty, use the specified text
							if ( submit_button_text == '' ) {
								var submit_button_text = 'Submit';
							}
							if ( mailChimp_form_id == '-' ) {
								alert("Don't forget to import lists first!");
								return false;
							} else {
								var shortcode_atts = [];
								shortcode_atts.push( 'form="'+mailChimp_form_id+'"' );
								if( show_title == true ) { shortcode_atts.push( 'title="1"' ); }
								if( show_description == true ) { shortcode_atts.push( 'description="1"' ); }
								shortcode_atts.push( 'submit="'+submit_button_text+'"' );
								editor.insertContent( '[yikes-mailchimp '+shortcode_atts.join( ' ' )+']' );
								// editor.insertContent( '[yks-mailchimp-list id="'+mailChimp_form_id+'" submit_text="'+submit_button_text+'" style="'+style_type+'"]' );
							}
						}

					} );
					// if no lists have been imported
					// lets alert the user
				} else {
					tinyMCE.activeEditor.windowManager.alert("Error: You need to import some MailChimp lists before you can add any! Head over to 'MailChimp Forms > Manage List Forms' to get started.");	
				}
				
            }

        } );

    } );
	
} )();