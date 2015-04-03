(function( $ ) {
	'use strict';
		
		$( document ).ready( function() {	
		
			/* Hide Stats, Display Shortcode */
			$( 'body' ).on( 'click' , '.view-yikes-mc-form-shortcode' , function() {
				var clicked = jQuery( this );
				var index = jQuery( this ).parents( 'tr' ).find('.form-id-container').text();
				clicked.parents( 'tr' ).find( '.yikes-mc-shortcode-input-'+index ).toggleClass( 'hidden-class' );
				clicked.parents( 'tr' ).find( '.stats-'+index ).toggleClass( 'hidden-class' );
				clicked.toggleText();
				return false;
			});
				 
			 /* Toggle Text - Stats/Shortcode (manage-forms.php)*/
			$.fn.toggleText = function() {
				var altText = this.data("alt-text");
				if (altText) {
					this.data("alt-text", this.html());
					this.html(altText);
				}
			};
			
		});
	 
})( jQuery );


/* Switch pages on form switch */
function YIKES_Easy_MC_SwitchForm( selected_value ) {
	// jQuery page redirect to selected for...
	window.location.replace( object_data.admin_url+'admin.php?page=yikes-mailchimp-edit-form&id='+selected_value );
}
