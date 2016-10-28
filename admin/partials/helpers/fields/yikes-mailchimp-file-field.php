<?php
	/*
	* 	Upload File Input Field
	*
	*	For help on using, see our documentation [https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/]
	* 	@since 6.0
	*/
	if ( is_string( $form_data['custom_fields'] ) ) {
		$field_data = json_decode( $form_data['custom_fields'] , true );
	} elseif ( is_array( $form_data['custom_fields'] ) ) {
		$field_data = $form_data['custom_fields'];
	}
	 
	// This will enqueue the Media Uploader script
	wp_enqueue_media();
	// And let's not forget the script we wrote earlier
	wp_enqueue_script( 'yikes-mailchimp-file-field-script', plugin_dir_url( __FILE__ ) . 'js/yikes-mc-file-upload.js', array( 'jquery' ), '1.0', false );
	// print_r( $field_data );
	wp_localize_script( 'yikes-mailchimp-file-field-script' , 'additional_data' , array(
		'wp_includes_image_url' => includes_url() . 'images/media/'
	) );
	$i = 1;
	// create an single item array when nothing is stored yet, loop for one field 
	$field_data['incentive-attachment'] = ( isset( $field_data['incentive-attachment'] ) && !empty( $field_data['incentive-attachment'] ) )  ? $field_data['incentive-attachment'] : array( '' );
?>
	
	<div class="yikes-mailchimp-file-field">
		<label for="image_url" class="widefat"><strong><?php echo $field['label']; ?></strong></label>
		<?php foreach( $field_data['incentive-attachment'] as $attachment ) { ?>
			<input type="text" name="custom-field[<?php echo $field['id']; ?>][<?php echo $i; ?>]" id="custom-field[<?php echo $field['id']; ?>][<?php echo $i; ?>]" class="file-attachment" value="<?php echo isset( $field_data[$field['id']][$i] ) ? $field_data[$field['id']][$i] : ''; ?>">
			<input type="button" name="upload-btn" id="upload-btn" class="button-secondary" data-attr-position="<?php echo $i; ?>" value="<?php _e( 'Upload File' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
			<div class="file-container">
				<p class="file-remove-wrapper">
					<a href="#" class="remove-file-button" data-attr-position="<?php echo $i; ?>"><?php _e( 'Remove File' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
				</p>
			</div>
		<?php $i++; } ?>
		<?php if( isset( $field['repeat'] ) ) { ?>
			<a href="#" class="button-secondary add-new-incentive-attachment" data-attr-position="<?php echo $i; ?>"><span class="dashicons dashicons-plus"></span></a>
		<?php } ?>
		<p class="description"><?php echo $field['description']; ?></p>
	</div>