<?php

$screen_base = get_current_screen()->base;

if (  $screen_base == __( 'toplevel_page_yks-mailchimp-form', 'yikes-inc-easy-mailchimp-extender' ) || $screen_base == __( 'mailchimp-forms_page_yks-mailchimp-my-mailchimp' , 'yikes-inc-easy-mailchimp-extender' ) ) {

	// Everything else
	// load our scripts in the dashboard
	wp_enqueue_script( 'jquery-ui-core');
	wp_enqueue_script( 'thickbox');
	wp_enqueue_script( 'jquery-ui-sortable');
	wp_enqueue_script( 'jquery-ui-tabs');
	wp_enqueue_script( 'ykseme-base' , YKSEME_URL.'js/script.ykseme.js', array('jquery') );
	wp_enqueue_script( 'jquery-datatables-pagination' , YKSEME_URL . 'js/jquery.dataTables.js' , array( 'jquery' ) );
	wp_enqueue_script( 'jquery-highcharts-js' , YKSEME_URL.'js/highcharts.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery-highcharts-exporting-js' , YKSEME_URL . 'js/exporting.js' , array( 'jquery' ) );
	wp_enqueue_script( 'jquery-highcharts-3d-js', YKSEME_URL.'js/highcharts-3d.js',	array( 'jquery' ) );
	wp_enqueue_script('jquery-highmaps-js' , YKSEME_URL.'js/map.js', array( 'jquery' ) );
	wp_enqueue_script('jquery-map-data-js' , 'http://code.highcharts.com/mapdata/custom/world.js' ,	array( 'jquery' ) );
	wp_enqueue_script('jquery-highmaps-data-js' , YKSEME_URL . 'js/data.js', array( 'jquery' ) );

} else if ( $screen_base == 'admin_page_yks-mailchimp-welcome' ) {
	
	wp_enqueue_script('bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js', array( 'jquery' ) );
	
} else if ( $screen_base == 'mailchimp-forms_page_yks-mailchimp-form-lists' ) {

	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script('bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js', array( 'jquery' ) ) ;
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('ykseme-base', YKSEME_URL . 'js/script.ykseme.js' , array( 'jquery' ) );
	wp_enqueue_script('jquery-datatables-pagination' , YKSEME_URL . 'js/jquery.dataTables.js' , array( 'jquery' ) );
	wp_enqueue_script('jquery-highcharts-js' , YKSEME_URL . 'js/highcharts.js' , array( 'jquery' ) );
	wp_enqueue_script('jquery-highcharts-exporting-js' , YKSEME_URL.'js/exporting.js' , array( 'jquery' ) );
	wp_enqueue_script('jquery-highcharts-3d-js' , YKSEME_URL.'js/highcharts-3d.js',	array( 'jquery' ) );						
	wp_enqueue_script('jquery-highmaps-js' , YKSEME_URL.'js/map.js', array( 'jquery' ) );
	wp_enqueue_script('jquery-map-data-js' ,	'http://code.highcharts.com/mapdata/custom/world.js' , array( 'jquery' ) );
	wp_enqueue_script('jquery-highmaps-data-js', YKSEME_URL . 'js/data.js', array('jquery') );

} else {
	return;
}

?>