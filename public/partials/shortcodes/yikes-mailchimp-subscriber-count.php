<?php
/*
*	Shortcode to display subscriber counts for a given list
*	@usage	[yikes-mailchimp-subscriber-count list="list_id"]
*	@since v6.0.2.4
*/

function yikes_mailchimp_subscriber_count_shortcode( $attributes ) {

	// Attributes
	shortcode_atts(
		array(
			'form' => '', // pass in a form, which will retreive the associated list ID -- takes precendence
			'list' => '', // pass in a specific list ID
		),
		$attributes,
		'yikes-mailchimp-subscriber-count'
	);

	/* If the user hasn't authenticated yet - bail */
	if ( get_option( 'yikes-mc-api-validation', 'invalid_api_key' ) != 'valid_api_key' ) {
		if ( WP_DEBUG ) {
			return '<strong>' . __( "You don't appear to be connected to MailChimp.", "yikes-inc-easy-mailchimp-extender" ) . '</strong>';
		}

		return '';
	}

	$form    = ( ! empty( $attributes['form'] ) ) ? str_replace( '&quot;', '', $attributes['form'] ) : false;
	$list_id = ( ! empty( $attributes['list'] ) ) ? $attributes['list'] : false;

	/* If no list ID was passed into the shortcode - bail */
	if ( ! $list_id && ! $form ) {
		if ( WP_DEBUG ) {
			return '<strong>' . __( 'You forgot to include the list or form ID.', 'yikes-inc-easy-mailchimp-extender' ) . '</strong>';
		}

		return '';
	}

	/* if a form ID and a list ID were passed in, use the form ID */
	if ( ( $form ) || ( $form && $list_id ) ) {
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form );

		// confirm we have some results, or return an error
		if ( ! $form_data ) {
			if ( WP_DEBUG ) {
				return __( "Oh no...This form doesn't exist. Head back to the manage forms page and select a different form.", 'yikes-inc-easy-mailchimp-extender' );
			}

			return '';
		}

		$list_id = sanitize_key( $form_data['list_id'] ); // associated list id (users who fill out the form will be subscribed to this list)
	}

	// object buffer
	ob_start();

	// submit the request the get the subscriber count
	$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_list( $list_id, array( 'stats.member_count' => true ) );

	if ( is_wp_error( $list_data ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$list_data->get_error_code(),
			__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
			"yikes-mailchimp-subscriber-count.php"
		);
		
		ob_clean();
		return;
	}

	/* type cast the returned value as an integer */
	echo (int) apply_filters( 'yikes-mailchimp-subscriber-count-value', $list_data['stats']['member_count'] );

	return ob_get_clean();

}

add_shortcode( 'yikes-mailchimp-subscriber-count', 'yikes_mailchimp_subscriber_count_shortcode' );
