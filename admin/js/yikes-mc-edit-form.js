(function( $ ) {
	'use strict';
	
	 $( document ).ready( function() {
		
		/* Initialize Trevor JS */
		$('.yikes-easy-mc-form-customizer').each( function() {
			new SirTrevor.Editor({
				el:  $(this),
				blockTypes: [
					"Heading",
					"Text",
					"List",
					"Quote",
					"Image",
					"Video",
					"Columns"
				]
			});
		});
		
		// initiailize color pickers
		$('.color-picker').each(function() {
			$( this ).wpColorPicker();
		});
			
	 });

})( jQuery );