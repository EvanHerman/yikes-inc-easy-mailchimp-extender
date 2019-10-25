( function( $ ) {

	$( document ).ready( function() {
		
		$( 'body' ).on( 'click', '.yikes-easy-mc-submit-button', function( event ) { 
		  event.preventDefault();
		  event.stopPropagation();
		  $( '.yikes-mailchimp-submit-button-span-text' ).focus();
		}); 

		$( 'body' ).on( 'click', '.yikes-mailchimp-submit-button-span-text', function( event ) {
		  event.preventDefault();
		  event.stopPropagation();
		});
	});

})( jQuery );