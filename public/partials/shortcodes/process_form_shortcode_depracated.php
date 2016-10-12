<?php
/*
*	Process the old shortcode
*	this is included for users who upgrade and don't switch over 
*	to the new shortcode. This is to maintain backwards compatability.
*/
// Add Shortcode ( [yikes-mailchimp] )
function process_depracated_mailchimp_shortcode( $atts ) {
		
	// Attributes
	extract( shortcode_atts(
		array(
			'form' => '',
			'submit' => 'Submit',
			'title' => '0',
			'description' => '0',
			'ajax' => '',
		), $atts )
	);
	
	return '<p><em>' . __( 'This MailChimp shortcode is now deprecated. Please insert the new shortcode to display this form.' , 'yikes-inc-easy-mailchimp-extender' ) . '</em></p>';
	
}
add_shortcode( 'yks-mailchimp-list', 'process_depracated_mailchimp_shortcode' );
