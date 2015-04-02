<?php

	
	add_action( 'wp_ajax_yikes_easy_mc_reate_form', 'yikes_easy_mc_reate_form' );
	
	function yikes_easy_mc_reate_form() {
		echo 'TEST!!!';
		die(); // this is required to return a proper result
	}


?>