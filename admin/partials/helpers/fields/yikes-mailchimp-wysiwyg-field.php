<?php
	/*
	*	WYSIWYG Input Field
	*	Used to extend the base functionality of the plugin
	*
	*	For help on using, see our documentation [https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/]
	* 	@since 6.0
	*/
	if ( is_string( $form_data['custom_fields'] ) ) {
		$field_data = json_decode( $form_data['custom_fields'] , true );
	} elseif ( is_array( $form_data['custom_fields'] ) ) {
		$field_data = $form_data['custom_fields'];
	}
	
	$content = ( isset( $field_data[$field['id']] ) ) ? $field_data[$field['id']] : ( isset( $field['default'] ) ? $field['default'] : '' );
	$wysiwyg_id = 'custom-field['.$field['id'].']';
?>

<div class="yikes-mailchimp-wysiwyg-field">
    <label for="image_url" class="widefat"><strong><?php echo $field['label']; ?></strong></label>
    <?php wp_editor( $content, $field['id'], array( 'textarea_name' => $wysiwyg_id ) ); ?>
    <p class="description"><?php echo $field['description']; ?></p>
</div>
