<?php
		
		/* 
			Main Initialization File, Included into class.yksemeBase.php 
			Around Line 63
		*/

				// If it's not already set up, initialize our plugin session
				if( session_id() == '' ) {
					@session_start();
				}
				
				if( !is_array( @$_SESSION[$this->sessName] ) ) {
					$_SESSION[$this->sessName]	= array();
				}
				
				// Add the CSS/JS files
				add_action('admin_print_styles',		array(&$this, 'addStyles'));
				add_action('admin_print_scripts',		array(&$this, 'addScripts'));
				add_action('admin_init', array( &$this, 'yks_easy_mc_plugin_activation_redirect' ) );
				
				// custom Dashboard MailChimp Account Activity Widget
				add_action( 'wp_dashboard_setup',  array( &$this , 'yks_mc_add_chimp_chatter_dashboard_widget' ) );
				
				if ( is_admin() ) {
					// add a filter for our heartbeat response
					// only add it to the admin screen, to prevent 
					// heartbeat from running on the front end
					add_filter('heartbeat_received', array( &$this , 'yks_mc_heartbeat_received' ) , 10, 2);
					add_action("init", array( &$this , "yks_mc_heartbeat_init" ) );
					add_filter( 'heartbeat_settings', array( &$this , 'yks_mc_tweak_heartbeat_settings') );
				}

				// adding our custom content action
				// used to prevent other plugins from hooking
				// into the_content (such as jetpack sharedadddy, sharethis etc.)
				add_action( 'init', array( &$this, 'yks_mc_content' ), 1 );
				
				// Custom Filter To Alter Submitted Data
				// before being sent off to MailChimp
				add_filter( 'yikes_mc_get_form_data', array( &$this , 'yikes_mc_get_form_data_filter' ) , 10 );
				
				// Custom Filter To Alter User Already Subscribed Message
				add_filter( 'yikes_mc_user_already_subscribed', array( &$this , 'yikes_mc_user_already_subscribed_error_message_filter' ) , 10 , 2 );
				
				
				// tinymce buttons
				// only add filters and actions on wp 3.9 and above
				if ( get_bloginfo( 'version' ) >= '3.9' ) {
					add_action( 'admin_head', array(&$this, 'yks_mc_add_tinyMCE') );
					add_filter( 'mce_external_plugins', array(&$this, 'yks_mc_add_tinymce_plugin') );
					add_filter( 'mce_buttons', array(&$this, 'yks_mc_add_tinymce_button') );
					// pass our lists data to tinyMCE button for use
					foreach( array('post.php','post-new.php') as $hook ) {
						add_action( "admin_head-$hook", array(&$this, 'yks_mc_js_admin_head') );
					}
				} else { 
					// if the WordPress is older than 3.9
					// load jQuery UI 1.10 CSS for dialogs
					wp_enqueue_style('yks_easy_mc_extender-admin-ui-css', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css' );
					wp_enqueue_style('yks_easy_mc_wp_3.8', plugin_dir_url(__FILE__).'../css/yks_easy_mc_wp_3point8.css');
				}
				
				if( !is_admin() ) {
					// custom hooks to print scripts and styles only on pages
					// where the form is being displayed (performace enhancement)
					// hooked into shortcode_form.php
					add_action('yks_mc_enqueue_styles', array( &$this, 'addStyles_frontend' ) );
					add_action('yks_mc_enqueue_scripts', array( &$this, 'addScripts_frontend') ) ;
				}
				
				// Setup the administration menus
				add_action('admin_menu', array(&$this, 'addAdministrationMenu'));
				
				// Make sure the option exists
				if( !$this->optionVal ) {
					$this->getOptionValue();
				}
				
				// Setup shortcodes
				$this->createShortcodes();
				
				// Initialize current list array
				$this->currentLists		= array();
				$this->currentListsCt	= array();
				
				// Do any update tasks if needed
				$this->runUpdateCheck();
				
				// Register Our Widget
				$this->registerMailChimpWidget($this->optionVal['lists']);	

?>