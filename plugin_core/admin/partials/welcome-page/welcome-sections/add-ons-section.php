<?php 
	// enqueue add-ons css
	wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-addons-styles', YIKES_MC_URL . 'admin/css/yikes-inc-easy-mailchimp-extender-addons.min.css', array(), '6.0.3.9', 'all' );
?>
<!-- we're just overriding the header size here -->
<style>
body .welcome-page-about-wrap .wrap img.yikes-mc-freddie-logo {
	display: none;
}
body .welcome-page-about-wrap .wrap h1 {
	margin: 1em 0;
	font-size: 25px;
}
body .welcome-page-about-wrap .wrap .yikes-easy-mc-about-text {
	margin: 0;
	margin-top: 10px;
}
body .welcome-page-about-wrap .wrap #add-ons {
	margin-top: 1em;
}
</style>	
<?php
	// include the add-ons here
	include_once( YIKES_MC_PATH . 'admin/partials/menu/add-ons.php' );
?>