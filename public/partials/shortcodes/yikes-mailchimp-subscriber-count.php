<?php
/*
*	Shortcode to display subscriber counts for a given list
*	@usage	[yikes-mailchimp-subscriber-count list="list_id"]
*	@since v6.0.2.4
*/

function yikes_mailchimp_subscriber_count_shortcode( $attributes ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'form' => '', // pass in a form, which will retreive the associated list ID -- takes precendence
			'list' => '', // pass in a specific list ID
		), $attributes , 'yikes-mailchimp-subscriber-count' )
	);

	/* If the user hasn't authenticated yet - bail */
	if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {
		if( WP_DEBUG ) {
			return '<strong>' . __( "You don't appear to be connected to MailChimp.", "yikes-inc-easy-mailchimp-extender" ) . '</strong>';
		}
		return;
	}

	$form = ( ! empty( $attributes['form'] ) ) ? str_replace( '&quot;', '', $attributes['form'] ) : false; // replace the sanitize quotes to perform a proper query
	$list_id = ( ! empty( $attributes['list'] ) ) ? $attributes['list'] : false;

	/* If no list ID was passed into the shortcode - bail */
	if( ! $list_id  && ! $form) {
		if( WP_DEBUG ) {
			return '<strong>' . __( 'You forgot to include the list or form ID.', 'yikes-inc-easy-mailchimp-extender' ) . '</strong>';
		}
		return;
	}

	/* if a form ID and a list ID were passed in, use the form ID */
	if ( ( $form ) || ( $form && $list_id ) ) {
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form );

		// confirm we have some results, or return an error
		if( ! $form_data ) {
			if( WP_DEBUG ) {
				return __( "Oh no...This form doesn't exist. Head back to the manage forms page and select a different form." , 'yikes-inc-easy-mailchimp-extender' );
			}
			return;
		}

		$list_id = sanitize_key( $form_data['list_id'] ); // associated list id (users who fill out the form will be subscribed to this list)
	}

	// object buffer
	ob_start();

	// submit the request the get the subscriber count
	try {

		// get the api key
		$api_key = yikes_get_mc_api_key();
		$dash_position = strpos( $api_key, '-' );
		if( $dash_position !== false ) {
			$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/list.json';
		}

		// run the request
		$subscriber_count_response = wp_remote_post( $api_endpoint, array(
			'body' => apply_filters( 'yikes-mailchimp-user-subscriber-count-api-request', array(
				'apikey' => $api_key,
				'filters' => array(
					'list_id' => $list_id,
				),
			), $list_id ),
			'timeout' => 10,
			'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
		) );

		$subscriber_count_response = json_decode( wp_remote_retrieve_body( $subscriber_count_response ), true );
		if( isset( $subscriber_count_response['error'] ) ) {
			if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $subscriber_count_response['error'], __( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ), "yikes-mailchimp-subscriber-count.php" );
			}
		}
		// if more than one list is returned, something went wrong - bail
		if( $subscriber_count_response['total'] != 1 ) {
			if( WP_DEBUG ) {
				return '<strong>' . sprintf( __( "It looks like this list wasn't found. Double check the list with with ID '%s' exists.", "yikes-inc-easy-mailchimp-extender" ), $list_id ) . '</strong>';
			}
			return;
		}

		/* type cast the returned value as an integer */
		echo (int) apply_filters( 'yikes-mailchimp-subscriber-count-value', $subscriber_count_response['data'][0]['stats']['member_count'] );

	} catch ( Exception $error ) {
		echo $error->getMessage();
	}

	return ob_get_clean();

}
add_shortcode( 'yikes-mailchimp-subscriber-count', 'yikes_mailchimp_subscriber_count_shortcode' );
