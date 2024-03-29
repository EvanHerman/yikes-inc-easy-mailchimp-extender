<?php 
/*
	* 	Standard Dropdown (select) Field
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
<div class="custom-field-section">
	<!-- title -->
	<strong><?php echo esc_html( $field['label'] ); ?></strong>

	<!-- Dropdown -->
	<select class="custom-select-field" name="custom-field[<?php echo esc_attr( $field['id'] ); ?>]">
	<option value="" disabled selected><?php echo isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : esc_attr__( 'Select...', 'yikes-inc-easy-mailchimp-extender' ); ?></option>
		<?php foreach( $field['options'] as $value => $label ) { ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php if( isset( $field_data[$field['id']] ) ) { selected( $field_data[$field['id']] , $value ); } ?>><?php echo esc_html( $label ); ?></option>
		<?php } ?>
	</select>

	<!-- description -->
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
	<?php } ?>
</div>