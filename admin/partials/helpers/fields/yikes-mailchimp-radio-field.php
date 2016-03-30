<?php 
	/*	
	* 	Standard Radio Input Field
	*
	*	For help on using, see our documentation [https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/]
	* 	@since 6.0
	*/
	$field_data = json_decode( $form_data['custom_fields'] , true ); 
?>
<div class="custom-field-section">
	<!-- title -->
	<strong><?php echo $field['label']; ?></strong>
	<!-- radio buttons -->
	<section class="custom-radio-holder">
		<?php foreach( $field['options'] as $value => $label ) { ?>
			<label class="custom-radio-label">
				<input type="radio" name="custom-field[<?php echo $field['id']; ?>][]" id="custom-field" value="<?php echo $value; ?>" <?php if( isset( $field_data[$field['id']] ) ) { checked( $field_data[$field['id']] , $value ); } ?>>
				<?php echo $label; ?>
			</label>
		<?php } ?>
	</section>
	<!-- description -->
	<?php if( isset( $field['description'] ) && $field['description'] != '' ) { ?>
		<p class="description"><?php echo $field['description']; ?></p>
	<?php } ?>
</div>