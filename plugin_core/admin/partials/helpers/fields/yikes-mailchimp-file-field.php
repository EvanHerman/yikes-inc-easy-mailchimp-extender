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
?>

<div class="yikes-mailchimp-file-field">
    <label for="image_url" class="widefat"><strong><?php echo $field['label']; ?></strong></label>
    <input type="text" name="custom-field[<?php echo $field['id']; ?>]" id="custom-field[<?php echo $field['id']; ?>]" class="incentive-attachment" value="<?php echo isset( $field_data[$field['id']] ) ? $field_data[$field['id']] : ''; ?>">
    <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="<?php _e( 'Upload Incentive' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
    <p class="description"><?php echo $field['description']; ?></p>
</div>
