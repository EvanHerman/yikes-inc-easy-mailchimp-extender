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
	<strong><?php echo esc_html( $field['label'] ); ?></strong>
	<input type="text" class="widefat" name="custom-field[<?php echo esc_attr( $field['id'] ); ?>]" id="custom-field" value="<?php echo isset( $field_data[ $field['id'] ] ) ? esc_attr( $field_data[ $field['id'] ] ) : ''; ?>" placeholder="<?php echo isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : ''; ?>">
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
	<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
	<?php } ?>
</label>