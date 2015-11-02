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
	if( ( $form ) || ( $form && $list_id ) ) {
		global $wpdb;
		// return it as an array, so we can work with it to build our form below
		$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form . '', ARRAY_A );
		// confirm we have some results, or return an error
		if( ! $form_results ) {
			if( WP_DEBUG ) {	
				return __( "Oh no...This form doesn't exist. Head back to the manage forms page and select a different form." , 'yikes-inc-easy-mailchimp-extender' );
			}
			return;
		}
		$form_data = $form_results[0];
		$list_id = sanitize_key( $form_data['list_id'] ); // associated list id (users who fill out the form will be subscribed to this list)
	}
	
	// object buffer 
	ob_start();	
	
	// submit the request the get the subscriber count
	try {
	
		// get the api key
		$api_key = get_option( 'yikes-mc-api-key' , '' );
		// initialize the MailChimp class
		$MailChimp = new MailChimp( $api_key );		
		// run the request
		$subscriber_count_response = $MailChimp->call( '/lists/list', apply_filters( 'yikes-mailchimp-user-subscriber-count-api-request', array( 
			'api_key' => $api_key,
			'filters' => array(
				'list_id' => $list_id,
			),
		), $list_id ) );
		
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

?>