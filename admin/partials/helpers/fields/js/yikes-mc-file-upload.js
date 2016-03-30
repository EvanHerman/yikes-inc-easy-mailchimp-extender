jQuery(document).ready(function($){
	$('body').on('click','#upload-btn',function(e) {
		var clicked_incentive = parseInt( $( this ).attr( 'data-attr-position' ) );
		e.preventDefault();
		var image = wp.media({ 
			title: 'Upload Image',
			// mutiple: true if you want to upload multiple files at once
			multiple: false
		}).open()
		.on('select', function(e){
			// This will return the selected image from the Media Uploader, the result is an object
			var uploaded_image = image.state().get('selection').first();
			// We convert uploaded_image to a JSON object to make accessing it easier
			// Output to the console uploaded_image
			var image_url = uploaded_image.toJSON().url;
			if( uploaded_image.toJSON().hasOwnProperty( 'sizes' ) ) {	
				if( uploaded_image.toJSON().sizes.medium ) {
					var size = uploaded_image.toJSON().sizes.medium.width;
				} else if( uploaded_image.toJSON().sizes.full ) {
					var size = uploaded_image.toJSON().sizes.full.width;
				} else {
					var size = '';
				}
			}
			// Let's assign the url value to the input field
			$( 'input[data-attr-position="'+clicked_incentive+'"]' ).prev().val( image_url );
			populate_file_container( image_url, clicked_incentive );
			// adjust file container size
			$( 'a[data-attr-position="'+clicked_incentive+'"]' ).next().attr( 'width' , size+'px' ).css({ 'max-width' : size+'px', 'width' : '100%' });
			$( 'a[data-attr-position="'+clicked_incentive+'"]' ).parents( '.file-container' ).show()
		});
	});
});


function populate_file_container( image_url, clicked_incentive ) {
	var extension = image_url.substr( ( image_url.lastIndexOf('.') +1) );
	switch(extension) {
		case 'jpg':
		case 'png':
		case 'gif':
			var file_container = '<img src="'+image_url+'" class="incentive-image-preview">';  // There's was a typo in the example where
		break;                         // the alert ended with pdf instead of gif.
		case 'zip':
		case 'rar':
			var file_container = '<img src="'+localized_data.wp_includes_url+'/archive.png" class="incentive-image-preview archive-image">';  // There's was a typo in the example where
		break;
		case 'pdf':
			var file_container = '<img src="'+localized_data.wp_includes_url+'/document.png" class="incentive-image-preview pdf-image">';  // There's was a typo in the example where
		break;
		case 'mp3':
		case 'wav':
		case 'wma':
			var file_container = '<img src="'+localized_data.wp_includes_url+'/audio.png" class="incentive-image-preview audio-image">';  // There's was a typo in the example where
		break;
		case 'mp4':
		case 'avi':
		case 'mpg':
		case 'mov':
		case 'qt':
		case 'mpeg':
		case '3gp':
		case 'ogm':
		case 'ogg':
			var file_container = '<img src="'+localized_data.wp_includes_url+'/video.png" class="incentive-image-preview video-image">';  // There's was a typo in the example where
		break;
		default:
			alert('who knows');
	}
	jQuery( 'a[data-attr-position="'+clicked_incentive+'"]' ).parent( '.file-remove-wrapper' ).find( 'img.incentive-image-preview' ).remove()
	jQuery( 'a[data-attr-position="'+clicked_incentive+'"]' ).parent( '.file-remove-wrapper' ).append( file_container ).find( 'a' );
}