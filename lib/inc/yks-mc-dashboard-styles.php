<?php

/*
* Enqueue all of our styles on the dashboard
* for YIKES Easy MailChimp Extender
*/

// store our screen bsae
$screen_base = get_current_screen()->base;

if (  $screen_base == __( 'toplevel_page_yks-mailchimp-form' , 'yikes-inc-easy-mailchimp-extender' ) || $screen_base == __( 'mailchimp-forms_page_yks-mailchimp-my-mailchimp', 'yikes-inc-easy-mailchimp-extender' )
	|| $screen_base == __( 'mailchimp-forms_page_yks-mailchimp-form-lists', 'yikes-inc-easy-mailchimp-extender' ) || $screen_base == 'widgets' || $screen_base == 'post'	|| $screen_base == __( 'mailchimp-forms_page_yks-mailchimp-about-yikes' , 'yikes-inc-easy-mailchimp-extender' ) ) {
	
	// Register Styles
	wp_register_style( 'ykseme-css-base' , YKSEME_URL . 'css/style.ykseme.min.css' , array() , '1.0.0' , 'all' );
	wp_register_style( 'jquery-datatables-pagination' , YKSEME_URL . 'css/jquery.dataTables.css' , array() , '1.0.0' , 'all' );	
	
	// Enqueue Styles
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'ykseme-css-base' );	
	wp_enqueue_style( 'jquery-datatables-pagination' );
	
	// just load the animate.css class on all admin pages
	wp_register_style( 'ykseme-animate-css' , YKSEME_URL . 'css/animate.css' , array() , '1.0.0' , 'all' );
	wp_enqueue_style( 'ykseme-animate-css' );
}
				
if ( $screen_base == 'admin_page_yks-mailchimp-welcome' || $screen_base == 'mailchimp-forms_page_yks-mailchimp-form-lists' ) {
	wp_enqueue_style( 'bootstrap-css' , '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' );
	wp_enqueue_style( 'wp-color-picker' );
}

?>
