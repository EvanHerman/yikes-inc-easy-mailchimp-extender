( function() {
    tinymce.PluginManager.add( 'yks_mc_tinymce_button', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'yks_mc_tinymce_button_key', {
		
            icon: 'dashicons-pressthis',
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'Select Your MailChimp Form',
					// should get all imported lists
					// add a dropdown here, populated with form ids+names
                    body: [
						{
							type: 'textbox',
							name: 'list_id',
							label: 'MailChimp List ID'
						},
						{
							type: 'textbox',
							name: 'submit_button_text',
							label: 'Submit Button Text'
						}
					],
					
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        editor.insertContent( '[yks-mailchimp-list id="'+e.data.list_id+'" submit_text="'+e.data.submit_button_text+'"] ' );
						// editor.insertContent( 'Title: ' + e.data.title );
                    }

                } );
            }

        } );

    } );

} )();