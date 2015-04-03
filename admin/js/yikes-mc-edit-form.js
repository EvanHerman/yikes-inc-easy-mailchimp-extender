(function( $ ) {
	'use strict';
	
	 $( document ).ready( function() { 
		/* Initialize Sortable Container */
		/* Sortable Form Builder - re-arrange field order (edit-form.php) */
		$( 'body' ).find( '#form-builder-container' ).sortable({
			update: function( ) {
			  var i = 1;
			  jQuery( '#form-builder-container' ).find( '.draggable' ).each( function() {
					jQuery( this ).find( '.position-input' ).val( i );
					i++;
			  });
			}
		});
		
		/* 
		* Remove a field from the form builder
		* re-enable it in the available fields list
		*/
		$( 'body' ).on( 'click' , '.remove-field' , function() {
			var merge_tag = jQuery( this ).attr( 'alt' );
			jQuery( this ).parents( '.draggable' ).fadeOut( 'fast' , function() {
				// re-enable the field, to be added to the form
				jQuery( '#available-fields' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
				// remove the element from the DOM
				jQuery( this ).remove();
			});
			return false;
		});
		
		/* 
		* Send selected field to the form builder 
		* and disable it from the available fields list
		*/
		$( 'body' ).on( 'click' , '.add-field-to-editor' , function() {
			// get the length, to decide if we should clear the html and append, or just append
			var form_builder_length = jQuery( '#form-builder-container' ).find( '.draggable' ).length;
			
			var merge_tag = jQuery( '.field-to-add-to-form' ).attr( 'alt' );
			
			// build our data
			var data = {
				'action' : 'add_field_to_form',
				'field_name' : jQuery( '.field-to-add-to-form' ).text(),
				'merge_tag' : merge_tag,
				'field_type' : jQuery( '.field-to-add-to-form' ).attr( 'data-attr-field-type' ),
				'list_id' : jQuery( '.field-to-add-to-form' ).attr( 'data-attr-form-id' ) // grab the form ID to query the API for field data
			};
			
			// submit our ajax request
			$.ajax({
				url: object.ajax_url,
				type:'POST',
				data: data,
				dataType: 'html',
				success : function( response, textStatus, jqXHR) { 
					jQuery( '.field-to-add-to-form' ).removeClass( 'field-to-add-to-form' ).addClass( 'not-available' );
					jQuery( '.add-field-to-editor' ).hide();
					if( form_builder_length < 1 ) {
						jQuery( '#form-builder-container' ).html( '' ).append( response );
					} else {
						jQuery( '#form-builder-container' ).append( response );
					}
					// add a value to the position
					jQuery( '.field-'+merge_tag+'-position' ).val( parseInt( form_builder_length + 1 ) ); // add one :)
				},
				error : function( jqXHR, textStatus, errorThrown ) { 
					alert( textStatus+jqXHR.status+jqXHR.responseText+"..." ); 
				},
				complete : function( jqXHR, textStatus ) {
					console.log( 'ajax request complete..' );
				}
			});
			return false;
		}); // end add field to form builder
		
		
		// initiailize color pickers
		$('.color-picker').each(function() {
			$( this ).wpColorPicker();
		}); // end color picker initialization
		
		/* Toggle settings hidden containers */
		$( 'body' ).on( 'click' , '.expansion-section-title' , function() {
			$( this ).next().stop().slideToggle();
			$( this ).find( '.dashicons' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/* Toggle Selected Class (Available Merge Vars) */
		$( 'body' ).on( 'click' , '.available-form-field' , function() {
			if( $( this ).hasClass( 'not-available' ) ) {
				return false;
			} else {
				if( $( this ).hasClass( 'field-to-add-to-form' ) ) {
					$( this ).removeClass( 'field-to-add-to-form' );
					jQuery( '.add-field-to-editor' ).fadeOut();
				} else {
					$( '.field-to-add-to-form' ).removeClass( 'field-to-add-to-form' );
					$( this ).toggleClass( 'field-to-add-to-form' );
					jQuery( '.add-field-to-editor' ).fadeIn();
				}
			}
		});
			
		/* Toggle Additional Form Settings (customizer, builder, error messages) */
		$( 'body' ).on( 'click' , '.hidden_setting' , function() {
			$( '.hidden_setting' ).removeClass( 'selected_hidden_setting' );
			$( '.selected_setting_triangle' ).remove();
			$( this ).addClass( 'selected_hidden_setting' ).append( '<div class="selected_setting_triangle"></div>' );
			var container = $( this ).attr( 'alt' );
			$( '.hidden-setting-label' ).hide();
			$( '#'+container ).show();
		});
		
	});
	 
})( jQuery );


/* Toggle Page Slection for form submission redirection */
function togglePageRedirection( e ) {
	if( e.value == 1 ) {
		jQuery( '#redirect-user-to-selection-label' ).fadeIn();
	} else {
		jQuery( '#redirect-user-to-selection-label' ).fadeOut();
	}
}