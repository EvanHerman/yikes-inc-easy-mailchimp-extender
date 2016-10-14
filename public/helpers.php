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

if ( ! function_exists( 'yikes_deep_parse_args' ) ) {
	/**
	 * Handle parsing multidimensional arrays of args.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $args     The arguments to parse.
	 * @param array $defaults The defaults to combine with the regular arguments.
	 *
	 * @return array The parsed arguments.
	 */
	function yikes_deep_parse_args( $args, $defaults ) {
		foreach ( $args as $key => $value ) {
			// If we don't have a corresponding default, just continue.
			if ( ! isset( $defaults[ $key ] ) ) {
				continue;
			}

			// For arrays, do another round of parsing args.
			if ( is_array( $value ) ) {
				$args[ $key ] = yikes_deep_parse_args( $value, $defaults[ $key ] );
			}
		}

		// Now we're ready for the regular wp_parse_args() function
		return wp_parse_args( $args, $defaults );
	}
}
