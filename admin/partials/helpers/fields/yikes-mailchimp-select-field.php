<?php 
	/*
	* 	Standard Dropdown (select) Field
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
<div class="custom-field-section">
	<!-- title -->
	<strong><?php echo $field['label']; ?></strong>
	
	<!-- Dropdown -->
	<select class="custom-select-field" name="custom-field[<?php echo $field['id']; ?>]">
		<?php foreach( $field['options'] as $value => $label ) { ?>
			<option value="<?php echo $value; ?>" <?php if( isset( $field_data[$field['id']] ) ) { selected( $field_data[$field['id']] , $value ); } ?>><?php echo $label; ?></option>
		<?php } ?>
	</select>
	
	<!-- description -->
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
		<p class="description"><?php echo $field['description']; ?></p>
	<?php } ?>
</div>