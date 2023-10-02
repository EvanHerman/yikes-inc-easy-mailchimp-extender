<?php 
	/*
	* 	Standard Text Input Field
	*
	*	For help on using, see our documentation [https://codeparrots.com/support/knowledge-base/product/easy-forms-for-mailchimp/]
	* 	@since 6.0
	*/
	if ( is_string( $form_data['custom_fields'] ) ) {
		$field_data = json_decode( $form_data['custom_fields'] , true );
	} elseif ( is_array( $form_data['custom_fields'] ) ) {
		$field_data = $form_data['custom_fields'];
	}
?>
<label class="custom-field-section">
	<strong><?php echo $field['label']; ?></strong>
	<input type="text" class="widefat" name="custom-field[<?php echo $field['id']; ?>]" id="custom-field" value="<?php echo isset( $field_data[$field['id']] ) ? $field_data[$field['id']] : ''; ?>" placeholder="<?php echo isset( $field['placeholder'] ) ? $field['placeholder'] : ''; ?>">
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
	<p class="description"><?php echo $field['description']; ?></p>
	<?php } ?>
</label>