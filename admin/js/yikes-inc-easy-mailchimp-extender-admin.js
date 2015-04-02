(function( $ ) {
	'use strict';
	
	 $( document ).ready( function() { 
		 
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
		
		/* Send selected field to the form builder */
		$( 'body' ).on( 'click' , '.add-field-to-editor' , function() {
			alert( 'not yet set up ;) be patient...');
			return false;
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
					
		/* Sortable Form Builder - re-arrange field order (edit-form.php) */
		$( 'body' ).find( '#form-builder-container' ).sortable();
		
		/* Hide Stats, Display Shortcode */
		$( 'body' ).on( 'click' , '.view-yikes-mc-form-shortcode' , function() {
			var clicked = jQuery( this );
			var index = jQuery( this ).parents( 'tr' ).find('.form-id-container').text();
			clicked.parents( 'tr' ).find( '.yikes-mc-shortcode-input-'+index ).toggleClass( 'hidden-class' );
			clicked.parents( 'tr' ).find( '.stats-'+index ).toggleClass( 'hidden-class' );
			clicked.toggleText();
			return false;
		});
		
	 });

	 
	 /* Toggle Text - Stats/Shortcode (manage-forms.php)*/
	$.fn.toggleText = function() {
		var altText = this.data("alt-text");
		if (altText) {
			this.data("alt-text", this.html());
			this.html(altText);
		}
	};
	 
})( jQuery );



/* Toggle Page Slection for form submission redirection */
function togglePageRedirection( e ) {
	if( e.value == 1 ) {
		jQuery( '#redirect-user-to-selection-label' ).fadeIn();
	} else {
		jQuery( '#redirect-user-to-selection-label' ).fadeOut();
	}
}

/* Switch pages on form switch */
function YIKES_Easy_MC_SwitchForm( selected_value ) {
	// jQuery page redirect to selected for...
	window.location.replace( object_data.admin_url+'admin.php?page=yikes-mailchimp-edit-form&id='+selected_value );
}
