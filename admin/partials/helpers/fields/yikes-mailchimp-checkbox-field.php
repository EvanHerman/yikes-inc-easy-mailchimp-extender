<?php 
/*
* 	Standard Checkbox Input Field
*
*	For help on using, see our documentation [https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/]
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
	<input type="checkbox" class="widefat" name="custom-field[<?php echo $field['id']; ?>]" id="custom-field" value="1" <?php checked( isset( $field_data[$field['id']] ) ? $field_data[$field['id']] : 0, 1, true ); ?>>
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
	<p class="description"><?php echo $field['description']; ?></p>
	<?php } ?>
</label>
