<style>
.remove-file-button {
	background: url('<?php echo plugin_dir_url( __FILE__ ); ?>/img/icon-delete.png');
	height: 16px;
	position: absolute;
	text-indent: -99999px;
	width: 16px;
}
</style>
<?php
	/*
	* Upload File Input Field
	* @since 6.0
	*/
	$field_data = json_decode( $form_data['custom_fields'] , true ); 
	// This will enqueue the Media Uploader script
	wp_enqueue_media();
	// And let's not forget the script we wrote earlier
	wp_enqueue_script( 'yikes-mailchimp-file-field-script', plugin_dir_url( __FILE__ ) . 'js/yikes-mc-file-upload.js', array( 'jquery' ), '1.0', false );
	// print_r( $field_data );
	wp_localize_script( 'yikes-mailchimp-file-field-script' , 'additional_data' , array(
		'wp_includes_image_url' => includes_url() . 'images/media/'
	) );
	$i = 1;
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