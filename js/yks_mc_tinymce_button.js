( function() {

		// grabbing our data from class.yksemeBase.php
		var values = [yks_mc_lists_data.lists][0];
		var values_length = values.length;
		
		if ( values_length == 0 ) {
			values = [
				{text: "Please Import Some MailChimp Lists", value: "-"}
			];
		}
		
		/* Styles Dropdown
		Will Be Included In Future Iterations
		style_values = [
				{text: "Default", value: "default"},
				{text: "Simple 1", value: "simple_1"},
				{text: "Simple 2", value: "simple_2"},
				{text: "Type 1", value: "type_1"},
				{text: "Type 2", value: "type_2"},
			];
		*/
		
		// test data
		/*
		console.log(values);
		console.log('The array is '+values[0].length+' deep');
			
		// working value
		var values = [
			{text: "evan herman personal site newsletter", value: "eff28bd985"}, 
			{text: "test mc 2", value: "0b071c0bd1"}, 
			{text: "testing list for mailchimp plugin", value: "057fb8cc2e"}
		];
		*/
	
    tinymce.PluginManager.add( 'yks_mc_tinymce_button', function( editor, url ) {
	
        // Add a button that opens a window
        editor.addButton( 'yks_mc_tinymce_button_key', {
			
            icon: 'yks-mc-icon-yikes-button-image',
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
								type: 'textbox',
								name: 'submit_button_text',
								label: 'Submit Button Text'
							},
							/* Commenting out Style Dropdown 
							{
								type: 'listbox',
								name: 'style_selection',
								label: 'Form Style',
								values: style_values
							}
							*/
						],
						id: 'yks_mc_tinyMCE_modal', // and an ID to the modal, to target it easier
						onsubmit: function( e ) {
							// Insert content when the window form is submitted
							// store the mailchimp list ID
							var mailChimp_form_id = e.data.list_id;
							// store the submit button text
							var submit_button_text = e.data.submit_button_text;
							// store the style type
							var style_type = e.data.style_selection;
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
								editor.insertContent( '[yks-mailchimp-list id="'+mailChimp_form_id+'" submit_text="'+submit_button_text+'"]' );
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