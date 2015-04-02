<?php

	// Register Styles for the frontend of all sites
	wp_register_style( 'ykseme-css-base' , YKSEME_URL . 'css/style.ykseme.min.css' , array() , '1.0.0' , 'all' );
	// enqueue our jQuery UI styles only when 
	// the datepicker enqueue is enabled in the settings field
	if( isset( $this->optionVal['yks-mailchimp-jquery-datepicker'] ) && $this->optionVal['yks-mailchimp-jquery-datepicker']	== '1' ) {
		wp_register_style( 'ykseme-css-smoothness' , '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css' , array() , '1.0.0', 'all' );
		wp_enqueue_style( 'ykseme-css-smoothness' );
	}
	wp_register_style( 'ykseme-animate-css' , YKSEME_URL . 'css/animate.css' , array() , '1.0.0' , 'all' );
	
	// Enqueue Styles
	wp_enqueue_style( 'ykseme-css-base' );
	wp_enqueue_style( 'ykseme-animate-css' );

?>