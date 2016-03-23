<?php 
/*
*	All helper functions that users can use to access or alter data
*	@since 6.0.3.4
*/

/*
*	Legacy support for our PHP Snippet
*	- this snippet existed in previous versions, and hes been preserved
*	  to maintain backwards compatibility. The form ID needs to be updated.
*	
*	@since 6.0.0
*/
function yksemeProcessSnippet( $list=false, $submit_text ) {
	$submit_text = ( isset( $submit_text ) ) ? 'submit="' . $submit_text . '"' : '';
	return do_shortcode( '[yikes-mailchimp form="' . $list . '" ' . $submit_text . ']' );
}

/*
*	Some Useful Helper Functions for our users
*	@since 6.0.3.4
*/
function yikes_get_form_data( $form_id ) {
	if( ! $form_id ) {
		return __( 'Whoops, you forgot to specify a form ID.', 'yikes-inc-easy-mailchimp-extender' );
	}
	return Yikes_Inc_Easy_Mailchimp_Extender_Public::yikes_retrieve_form_settings( $form_id );
}

?>