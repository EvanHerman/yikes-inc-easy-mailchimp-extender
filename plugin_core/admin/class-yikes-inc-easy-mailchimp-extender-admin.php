<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Forms
 * @subpackage Yikes_Inc_Easy_Mailchimp_Forms/admin
 * @author     YIKES Inc. <info@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Forms_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $yikes_inc_easy_mailchimp_extender    The ID of this plugin.
	 */
	private $yikes_inc_easy_mailchimp_extender;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;			
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $yikes_inc_easy_mailchimp_extender, $version ) {
	
		$this->yikes_inc_easy_mailchimp_extender = $yikes_inc_easy_mailchimp_extender;
		$this->version = $version;
		// check for old plugin options and migrate if exist
		add_action( 'admin_menu' , array( $this , 'register_admin_pages' ) , 11 );
		// check for old plugin options and migrate if exist
		add_action( 'admin_init' , array( $this , 'check_for_old_yks_mc_options' ) );
		// Ajax function to update new options...
		add_action( 'wp_ajax_migrate_old_plugin_settings', array( $this , 'migrate_archived_options' ) );
		// Ajax function to migrate our forms
		add_action( 'wp_ajax_migrate_prevoious_forms', array( $this , 'migrate_previously_setup_forms' ) );
		// fix menu icon spacing
		add_action( 'admin_head' , array( $this , 'fix_menu_icon_spacing' ) );
		// register our plugin settings
		add_action( 'admin_init', array( $this , 'yikes_easy_mc_settings_init' ) );
		// plugin redirect on activation
		add_action( 'admin_init' , array( $this , 'yikes_easy_mc_activation_redirect' ) );
		// ensure the MailChimp class wasn't previously declared in another plugin
		if( ! class_exists( 'Mailchimp' ) ) {
			// Include our MailChimp API Wrapper
			include_once( YIKES_MC_PATH . 'includes/MailChimp/Mailchimp.php' );
		}
		// Include Third Party Extensions
		include_once( YIKES_MC_PATH . 'includes/third-party-integrations/third-party-init.php' );
		// Include our dashboard widget class
		include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/class.list-activity-widget.php' );
		// Include our front end widget class
		include_once( YIKES_MC_PATH . 'admin/partials/front-end-widgets/front-end-widget-form.php' );
		// Include our ajax processing class
		include_once( YIKES_MC_PATH . 'admin/partials/ajax/class.ajax.php' );
		// load up our helper class
		add_action( 'admin_init' , array( $this , 'yikes_mailchimp_load_helper_class' ) );
		// process the subscriber count shortcode in form descriptions
		add_action( 'yikes-mailchimp-form-description', array( $this, 'process_subscriber_count_shortcode_in_form_descriptions' ), 10, 2 );
		/***********************/
		/** Create A Form **/
		/**********************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-create-form' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_create_form' ) );
		}
		/***********************/
		/** Delete A Form **/
		/**********************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-delete-form' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_delete_form' ) );
		}
		/**********************************/
		/** Duplicate/Clone A Form 	**/
		/********************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-duplicate-form' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_duplicate_form' ) );
		}
		/*************************************/
		/**  Reset Form Impression Stats **/
		/***********************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-reset-stats' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_reset_impression_stats' ) );
		}
		/**********************************/
		/** 	     Update A Form 		**/
		/********************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-update-form' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_update_form' ) );
		}
		/**************************************************/
		/** 	 Clear Store MailChimp Transient Data   **/
		/*************************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-clear-transient-data' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_clear_transient_data' ) );
		}
		/*******************************************/
		/** Remove a user from a mailing list 	 **/
		/*****************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-unsubscribe-user' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_unsubscribe_user' ) );
		}	
		/*******************************************/
		/** 	Create misisng error log file  **/
		/*****************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-create-error-log' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_create_missing_error_log' ) );
		}		
		/*******************************************/
		/**   TinyMCE Initialization Functions	 **/
		/*****************************************/
		add_action( 'admin_head' , array( $this, 'add_tinyMCE_buttons' ) );
		// pass our lists data to tinyMCE button for use
		foreach ( array('post.php','post-new.php') as $hook ) {
			add_action( "admin_head-$hook", array( $this, 'tinymce_yikes_easy_mc' ) );
		}
		// display an admin notice for users on PHP < 5.3
		if( phpversion() < '5.3' ) {
			add_action( "admin_notices", array( $this, 'display_php_warning' ), 999 );
		}
		// two week , dismissable notification - check the users plugin installation date
		add_action( 'admin_init', array( $this , 'yikes_easy_mailchimp_check_installation_date' ) );
		// dismissable notice admin side
		add_action( 'admin_init', array( $this , 'yikes_easy_mailchimp_stop_bugging_me' ), 5 );
		/**************************************************/
		/** 	 	Clear MailChimp Error Log Data 	    **/
		/*************************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-clear-error-log' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_clear_error_log' ) );
		}
		/*********************************************/
		/** 		Export MailChimp Optin Forms   **/
		/*******************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-export-forms' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_export_forms' ) );
		}	
		/*********************************************/
		/** 				Export Plugin Settings    	   **/
		/*******************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-export-settings' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_export_plugin_settings' ) );
		}	
		/*******************************************/
		/** 		Import Class Inclusion	   **/
		/*****************************************/
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'yikes-easy-mc-import-forms' ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_import_forms' ) );
		}	
		/*******************************************/
		/** 	Premium Support Request     **/
		/*****************************************/
		if ( isset( $_POST[ 'submit-premium-support-request' ] ) ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_premium_support_request' ) );
		}	
		/****************************************/
		/**	Dismiss Options Migrations		**/
		/****************************************/
		if( isset( $_REQUEST['dismiss_migration_nonce'] ) ) {
			add_action( 'init' , array( $this , 'yikes_easy_mailchimp_dismiss_option_migrate' ) );	
		}
		/** Parse default value into usable dynamic data **/
		add_filter( 'yikes-mailchimp-process-default-tag' , array( $this , 'parse_mailchimp_default_tag' ) );
		/** Add a disclaimer to ensure that we let people know we are not endorsed/backed by MailChimp at all **/
		add_filter( 'admin_footer_text', array( $this, 'yikes_easy_forms_admin_disclaimer' ) );
		/** Add custom plugin action links **/
		add_filter( 'plugin_action_links_yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php', array( $this, 'easy_forms_plugin_action_links' ) );
		/* Alter the color scheme based on the users selection */
		add_action( 'admin_print_scripts', array( $this, 'alter_yikes_easy_mc_color_scheme' ) );
	}
				
		/*
		*	Add custom action links on plugins.php
		*	@ param 	array	$links 	Pre-existing plugin action links
		*	@ return	array	$links		New array of plugin actions
		*/
		public function easy_forms_plugin_action_links( $links ) {
		   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=yikes-inc-easy-mailchimp-settings') ) .'">' . __( 'Settings', 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
		   $links[] = '<a href="' . esc_url( 'http://www.yikesplugins.com?utm_source=plugins-page&utm_medium=plugin-row&utm_campaign=admin' ) . '" target="_blank">' . __( 'More plugins by YIKES' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
		   return $links;
		}
		
		/**
		 *	Add a disclaimer to the admin footer for all YIKES pages to ensure that users understand there is no coorelation between this plugin and MailChimp. 
		 *	This plugin simply provides the service of linking MailChimp with your site.
		 *
		 * @since        6.0
		 *
		 * @param       string   $footer_text The existing footer text
		 *
		 * @return      string
		 */
		public function yikes_easy_forms_admin_disclaimer( $footer_text ) {
			$page = get_current_screen();
			$base = $page->base;
			if ( strpos( $base, 'yikes-' ) !== false ) {
				$disclaimer_text = sprintf( '<em>' . __( 'Disclaimer: <strong>Easy Forms for MailChimp by YIKES</strong> is in no way endorsed, affiliated or backed by MailChimp, or its parent company Rocket Science Group.', 'yikes-inc-easy-mailchimp-extender' ), '<a href="https://wordpress.org/support/view/plugin-reviews/give?filter=5#postform" target="_blank" class="give-rating-link" data-rated="' . __( 'Thanks :)', 'yikes-inc-easy-mailchimp-extender' ) . '">', '</a></em>' );
				return $disclaimer_text;
			} else {
				return $footer_text;
			}
		}
			
		/*
		*	Parse our default tag into dynamic data
		*	to be passed to MailChimp
		*
		*	@since 6.0.0
		*	@return	parsed tag content
		*/
		public function parse_mailchimp_default_tag( $default_tag ) {
			if( ! $default_tag || $default_tag == '' ) {
				return $default_tag;
			}
			global $post;
			// page title
			if( $default_tag == '{page_title}' ) {
				$default_tag = get_the_title( $post->ID );
			}
			// page id
			if( $default_tag == '{page_title}' ) {
				$default_tag = $post->ID;
			}
			// page url
			if( $default_tag == '{page_url}' ) {
				$default_tag = get_permalink( $post->ID );
			}
			// blog name
			if( $default_tag == '{blog_name}' ) {
				$default_tag = get_bloginfo( 'name' );
			}
			// is user logged in
			if( $default_tag == '{user_logged_in}' ) {
				if( is_user_logged_in() ) {
					$default_tag = 'Registered User';
				} else {
					$default_tag = 'Guest User';
				}
			}
			/* Return our filtered tag */
			return apply_filters( 'yikes-mailchimp-parse-custom-default-value', $default_tag );
		}
				
		/* 
		*	Delete the contents of our error log
		*	
		*	When a user clicks 'Clear Log' on the debug settings page, this funciton
		*	is used to clear the data out of our php file.
		*/
		public function yikes_easy_mailchimp_clear_error_log() {
			// file put contents $returned error + other data
			if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
				$clear_log = file_put_contents( 
					YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php',
					''
				);
				if( $clear_log === false ) {
					// redirect the user to the manage forms page, display error message
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error-log-cleared=false' ) ) );
				} else {
					// redirect the user to the manage forms page, display confirmation
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error-log-cleared=true' ) ) );
				}
			}
		}

		/* 
		*	Custom export function to export all or specific forms
		*	to allow for easy transpot to other sites
		*	@since 		6.0.0
		*	@return 	CSV export file
		*/
		public function yikes_easy_mailchimp_export_forms() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// grab the forms
			$forms = isset( $_REQUEST['export_forms'] ) ? $_REQUEST['export_forms'] : 'all';
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'export-forms' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// include the export class
			if( ! class_exists( 'Yikes_Inc_Easy_MailChimp_Export_Class' ) ) {	
				include_once( YIKES_MC_PATH . 'includes/import-export/yikes-easy-mailchimp-export.class.php' );
			}
			// run the export function
			// parameters: ( $table_name, $form_ids, $file_name )
			Yikes_Inc_Easy_MailChimp_Export_Class::yikes_mailchimp_form_export( 'yikes_easy_mc_forms' , $forms, 'Yikes-Inc-Easy-MailChimp-Forms-Export' );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) );
			die();
		}
		
		/* 
		*	Custom export function to export YIKES Easy Forms for MailChimp Plugin Settings
		*	to allow for easy transpot to other sites
		*	@since 		6.0.0
		*	@return 	CSV export file
		*/
		public function yikes_easy_mailchimp_export_plugin_settings() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'export-settings' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// include the export class
			if( ! class_exists( 'Yikes_Inc_Easy_MailChimp_Export_Class' ) ) {	
				include_once( YIKES_MC_PATH . 'includes/import-export/yikes-easy-mailchimp-export.class.php' );
			}
			// run the export function
			// parameters: ( $table_name, $form_ids, $file_name )
			Yikes_Inc_Easy_MailChimp_Export_Class::yikes_mailchimp_settings_export( 'Yikes-Inc-Easy-MailChimp-Settings-Export' );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) );
			die();
		}
		
		/* 
		*	Custom import function to import all or specific forms
		*	@since 6.0.0
		*/
		public function yikes_easy_mailchimp_import_forms() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'import-forms' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// include the export class
			if( ! class_exists( 'Yikes_Inc_Easy_MailChimp_Import_Class' ) ) {	
				include_once( YIKES_MC_PATH . 'includes/import-export/yikes-easy-mailchimp-import.class.php' );
			}
			// run the import function
			// parameters: ( $_FILES )
			Yikes_Inc_Easy_MailChimp_Import_Class::yikes_mailchimp_import_forms( $_FILES );
			$import_query_arg = Yikes_Inc_Easy_MailChimp_Import_Class::yikes_mailchimp_import_type( $_FILES );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms&' . $import_query_arg . '=true' ) ) );
			die();
		}
		
		/* 
		*	Premium Support Request
		*	@since 6.0.0
		*/
		public function yikes_easy_mailchimp_premium_support_request() {
		
			if( isset( $_POST['action'] ) && $_POST['action'] != 'yikes-support-request' ) {
				return __( 'We encountered an error. Please contact the YIKES Inc. support team.' , 'yikes-inc-easy-mailchimp-extender' );
			}
						
			$license = $_POST['license_key'];
			$user_email = $_POST['user-email'];
			$support_topic = $_POST['support-topic'];
			$support_priority = $_POST['support-priority'];
			$support_content = $_POST['support-content'];
			
			// wp_die( print_r( $support_content) );
			
			$ticket_array = array( 
				'action' => 'yikes-support-request', 
				'license_key' => urlencode( base64_encode( $license ) ), 
				'user_email' => urlencode( $user_email ),
				'site_url' => urlencode( esc_url( home_url() ) ), 
				'support_topic' => urlencode( $support_topic ), 
				'support_priority' => $support_priority, 
				'support_content' => $support_content,
			);
			
			$yikes_plugin_support_url = 'https://yikesplugins.com';
			
			if( $license != 'plugin-core' ) {
				$split_license = explode( '|', $license );
				// first let's check that the license key is actually active
				$is_license_key_active = wp_remote_post( esc_url( $yikes_plugin_support_url ), array(
					'action' => 'check_license',
					'product_name' => urlencode( str_replace( '-', '', $split_license[1] ) . 'for Easy MailChimp' ),
					'license' => $split_license[0],
				) );	
				
				$response_body =  wp_remote_retrieve_body( $is_license_key_active );
				if( $response_body ) {
					if( $response_body->status != 'valid' ) {
						wp_die( 'Invalid License Key...' );
					}
				}
			}
			
			// Call the custom API.
			$response = wp_remote_post( esc_url( $yikes_plugin_support_url ), array(
				'timeout'   => 30,
				'sslverify' => false,
				'body'      => $ticket_array
			) );
									
			// catch the error
			if( is_wp_error( $response ) ) {
				wp_die( $create_ticket_request->getMessage() );
				return;
			}
			
			// retrieve our body
			$create_ticket_response = wp_remote_retrieve_body( $response );
			
			// display it
			if( $create_ticket_response )
				echo $create_ticket_response;

		}
		
		/**
		*	Dismiss the migrate options notice (incase the user wants to do things manually)
		*	
		*	@since 6.0.0
		**/
		public function yikes_easy_mailchimp_dismiss_option_migrate() {
			// delete the options and allow the user to manually updadte things
			
			// Verify the NONCE is valid
			check_admin_referer( 'yikes-mc-dismiss-migration' , 'dismiss_migration_nonce' );
						
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'index.php?yikes-mc-options-migration-dismissed="true"' ) ) );
			die();
		}
		
		/**
		 * Error logging class
		 *
		 * This is our main error logging class file, used to log errors to the error log.
		 *
		 * @since 6.0.0
		 */
		public function load_error_logging_class() {
			if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				// if error logging is enabled we should include our error logging class
				/* Generate oure error logging table */
				require_once YIKES_MC_PATH . '/includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging(); 
			}
		}
	
		/*
			yikes_easy_mailchimp_check_installation_date()
			checks the user installation date, and adds our action 
			- if it's past 2 weeks we ask the user for a review :)
			@since v6.0.0
		*/
		public function yikes_easy_mailchimp_check_installation_date() {	
			
			// add a new option to store the plugin activation date/time
			// @since v6.0.0
			// this is used to notify the user that they should review after 2 weeks
			if ( !get_option( 'yikes_easy_mailchimp_activation_date' ) ) {
				add_option( 'yikes_easy_mailchimp_activation_date', strtotime( "now" ) );
			}
			
			$stop_bugging_me = get_option( 'yikes_easy_mailchimp_review_stop_bugging_me' );
			
			if( !$stop_bugging_me ) {
				$install_date = get_option( 'yikes_easy_mailchimp_activation_date' );
				$past_date = strtotime( '-14 days' );
				if ( $past_date >= $install_date && current_user_can( 'install_plugins' ) ) {
					add_action( 'admin_notices', array( $this , 'yikes_easy_mailchimp_display_review_us_notice' ) );
				}
			}
		
		}
		
		/* 
			Display our admin notification
			asking for a review, and for user feedback 
			@since v6.0.0
		*/
		public function yikes_easy_mailchimp_display_review_us_notice() {	
			/* Lets only display our admin notice on YT4WP pages to not annoy the hell out of people :) */
			if ( in_array( get_current_screen()->base , array( 'dashboard' , 'post' , 'edit' ) ) || strpos( get_current_screen()->base ,'yikes-inc-easy-mailchimp') !== false ) {
				// Review URL - Change to the URL of your plugin on WordPress.org
				$reviewurl = 'https://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender';
				$addons_url = esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-addons' ) );
				$nobugurl = esc_url_raw( add_query_arg( 'yikes_easy_mc_icons_nobug', '1', admin_url() ) );
				global $current_user;
				get_currentuserinfo();
				if ( '' != $current_user->user_firstname ) {
					$review_message = '<div id="yikes-mailchimp-logo"></div>';
						$review_message .= sprintf( __( "Hi, %s, you've been using %s for 2 weeks now. We hope you're enjoying the features included with the free version. If so, please consider leaving us a review. Reviews provide us with feedback to grow and improve the plugin. If you're really enjoying the plugin, consider buying an add-on or developer license for some really awesome features and premium support." , 'yikes-inc-easy-mailchimp-extender' ) . "<span class='button-container'> <a href='%s' target='_blank' class='button-secondary'><span class='dashicons dashicons-star-filled'></span>" . __( "Leave A Review" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> <a href='%s' class='button-secondary'><span class='dashicons dashicons-upload'></span>" . __( "View Addons" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> <a href='%s' class='button-secondary'><span class='dashicons dashicons-no-alt'></span>" . __( "Dismiss" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> </span>",  $current_user->user_firstname, '<strong>Easy Forms for MailChimp by YIKES Inc.</strong>', $reviewurl, $addons_url, $nobugurl );
					$review_message .= '';
				} else {
					$review_message = '<div id="yikes-mailchimp-logo"></div>';
					$review_message .= sprintf( __( "It looks like you've been using %s for 2 weeks now. We hope you're enjoying the features included with the free version. If so, please consider leaving us a review. Reviews only help to catch other users attention as well as provide us with feedback to grow and improve upon. If you're really enjoying the plugin, consider buying an add-on or developer license for some really awesome features and premium support." , 'yikes-inc-easy-mailchimp-extender' ) . "<span class='button-container'> <a href='%s' target='_blank' class='button-secondary'><span class='dashicons dashicons-star-filled'></span>" . __( "Leave A Review" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> <a href='%s' class='button-secondary'><span class='dashicons dashicons-upload'></span>" . __( "View Addons" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> <a href='%s' class='button-secondary'><span class='dashicons dashicons-no-alt'></span>" . __( "Dismiss" , 'yikes-inc-easy-mailchimp-extender' ) . "</a> </span>", '<strong>Easy Forms for MailChimp by YIKES Inc.</strong>', $reviewurl, $addons_url, $nobugurl ) . '';
				}
				?>
					<div id="review-yikes-easy-mailchimp-notice">
						<?php echo $review_message; ?>
					</div>
				<?php
			}
		}
		
		/* 
			yikes_easy_mailchimp_stop_bugging_me()
			Remove the Review us notification when user clicks 'Dismiss'
			@since v3.1.1
		*/
		public function yikes_easy_mailchimp_stop_bugging_me() {
			$nobug = "";
			if ( isset( $_GET['yikes_easy_mc_icons_nobug'] ) ) {
				$nobug = (int) esc_attr( $_GET['yikes_easy_mc_icons_nobug'] );
			}
			if ( 1 == $nobug ) {
				add_option( 'yikes_easy_mailchimp_review_stop_bugging_me', TRUE );
			}
		}
	
	/* End Two Week Notificaition */
	
		/* Display a warning users who are using PHP < 5.3 */
		public function display_php_warning() {
			$message = __( 'YIKES Inc. Easy Forms for MailChimp requires a minimum of PHP 5.3. The plugin will not function properly until you update. Reach out to your host provider for assistance.' , 'yikes-inc-easy-mailchimp-extender' );
			echo "<div class='error'> <p><span class='dashicons dashicons-no-alt' style='color:rgb(231, 98, 98)'></span> $message</p></div>"; 
		}
	
	
		
	/* TinyMCE Functions */
		// load our button and pass in the JS form data variable
		public function add_tinyMCE_buttons() {	
			global $typenow;
			// only on Post Type: post and page
			if( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
				return;
			}
			add_filter( 'mce_buttons', array( $this, 'yks_mc_add_tinymce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'yks_mc_add_tinymce_plugin' ) );
		}
		
		// Add the button key for address via JS
		public function yks_mc_add_tinymce_button( $buttons ) {	
			array_push( $buttons, 'yks_mc_tinymce_button_key' );
			// Print all buttons
			return $buttons;
		}
		
		// inlcude the js for tinymce
		public function yks_mc_add_tinymce_plugin( $plugin_array ) {
			$plugin_array['yks_mc_tinymce_button'] = plugins_url( '/js/min/yikes-inc-easy-mailchimp-tinymce-button.min.js', __FILE__ );
			// Print all plugin js path
			// var_dump( $plugin_array );
			return $plugin_array;		
		}
		
		/**
		* Localize Script
		* Pass our imported list data, to the JS file
		* to build the drop down list in the modal
		*/
		public function tinymce_yikes_easy_mc() {
			// check capabilities
			if( ! current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
				return;
			}
			global $wpdb;
			$list_data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
			$lists = array();
			$i = 0;
			if( !empty( $list_data ) ) {	
				// build an array to pass to our javascript
				foreach( $list_data as $form ) {
						$lists[$i] = array( 
							'text' => urlencode( $form['form_name'] ),
							'value' => $form['id']
						);
						$i++;
				}
			} else {
				$lists[0] = array(
					'text' => __( 'Please Import Some MailChimp Lists' , 'yikes-inc-easy-mailchimp-extender' ),
					'value' => '-'
				);
			}
			/* Pass our form data to our JS file for use */
			?>
			<script type="text/javascript">
				var forms = {
					'data' : '<?php echo json_encode( $lists ); ?>'
				};
				var localized_data = {
					'button_title' : '<?php _e( 'Easy Forms for MailChimp by YIKES', 'yikes-inc-easy-mailchimp-extender' ); ?>',
					'popup_title' : '<?php _e( 'Easy Forms for MailChimp by YIKES', 'yikes-inc-easy-mailchimp-extender' ); ?>',
					'list_id_label' : '<?php _e( 'MailChimp Opt-In Form' , 'yikes-inc-easy-mailchimp-extender' ); ?>',
					'show_title_label' : '<?php _e( 'Display Form Title' , 'yikes-inc-easy-mailchimp-extender' ); ?>',
					'show_description_label' : '<?php _e( 'Display Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>',
					'submit_button_text_label' : '<?php _e( 'Submit Button Text' , 'yikes-inc-easy-mailchimp-extender' ); ?>',
				};
				<?php 
					$link = sprintf( __( 'You need to <a href="%s" title="%s">create a form</a> before you can add one to a page or post.', 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ), __( 'Create a form', 'yikes-inc-easy-mailchimp-extender' ) );
				?>
				var alert_translated = '<?php echo $link; ?>';
			</script>
			<?php
		}
	/* End TinyMCE Functions */
	
	/*
	*	Redirect the user to our Welcome page
	*	when they activate the plug in, if they haven't been redirected before
	*/
	public function yikes_easy_mc_activation_redirect() {
		if ( get_option( 'yikes_mailchimp_activation_redirect', 'true' ) == 'true' ) {
			update_option( 'yikes_mailchimp_activation_redirect', 'false' );
			wp_redirect( esc_url( admin_url( 'admin.php?page=yikes-mailchimp-welcome' ) ) );    
			exit();
		}
	}
	
	/*
	*  Fix the MailChimp icon spacing in the admin menu
	*/
	public function fix_menu_icon_spacing() {
		?>
			<style>
			a[href="admin.php?page=yikes-inc-easy-mailchimp"] .wp-menu-image img {
				padding-top: 5px !important;
			}
			</style>
		<?php
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    6.0.0
	 */
	public function enqueue_styles() {
		/**
		 *	Enqueue our global dashboard styles
		 */
		wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-admin', plugin_dir_url( __FILE__ ) . 'css/yikes-inc-easy-mailchimp-extender-admin.min.css', array(), $this->version, 'all' );
		/*
		*	Enqueue Add-ons styles
		*/	
		if ( get_current_screen()->base == 'easy-forms_page_yikes-inc-easy-mailchimp-addons' ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-addons-styles', plugin_dir_url( __FILE__ ) . 'css/yikes-inc-easy-mailchimp-extender-addons.min.css', array(), $this->version, 'all' );
		}
		/*
		*	Enqueue Subscriber Profile Flags
		*/	
		if ( get_current_screen()->base == 'admin_page_yikes-mailchimp-view-user' ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-subscriber-flags', plugin_dir_url( __FILE__ ) . 'css/flag-icon.min.css', array(), $this->version, 'all' );
		}
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    6.0.0
	 */
	public function enqueue_scripts() {
	
		/**
		 * Enqueue our scripts across the dashboard as needed 
		 */
		wp_register_script( 'yikes-inc-easy-mailchimp-extender-admin-js', plugin_dir_url( __FILE__ ) . 'js/min/yikes-inc-easy-mailchimp-extender-admin.min.js', array( 'jquery' , 'jquery-ui-sortable' ), $this->version, false );
		$localized_data = array(
			'admin_url' => esc_url_raw( admin_url() ),
			'ajax_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'locating_interest_groups' => __( 'Locating Interest Groups', 'yikes-inc-easy-mailchimp-extender' ),
			'search_preloader_url' => YIKES_MC_URL . 'includes/images/search-interest-group-preloader.gif',
			'preloader_url' => esc_url_raw( admin_url( '/images/wpspin_light.gif' ) )
		);
		wp_localize_script( 'yikes-inc-easy-mailchimp-extender-admin-js' , 'object_data' , $localized_data );
		wp_enqueue_script( 'yikes-inc-easy-mailchimp-extender-admin-js' );
		
	
		/*
		*	Enqueue required scripts for the form editor
		*/
		if( get_current_screen()->base == 'admin_page_yikes-mailchimp-edit-form' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_register_script( 'edit-form-js' , YIKES_MC_URL . 'admin/js/min/yikes-inc-easy-mailchimp-extender-edit-form.min.js' , array( 'jquery' ) , $this->version, false );
			$localized_data = array(
				'ajax_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
				'no_fields_assigned' => __( 'No fields assigned to this form. Select some fields to add to this form from the right hand column.', 'yikes-inc-easy-mailchimp-extender' ),
				'bulk_delete_alert' => __( 'Are you sure you want to delete all of the fields assigned to this form?', 'yikes-inc-easy-mailchimp-extender' ),
			);
			wp_localize_script( 'edit-form-js' , 'object' , $localized_data );
			wp_enqueue_script( 'edit-form-js' );
		}
		
	}
	
	/** Functionality **/
	/******************/
	
	/**
	*	Register our admin pages
	*	used to display data back to the user
	**/
	public function register_admin_pages() {	
				
		/* Top Level Menu 'Easy MailChimp' */
		add_menu_page( 
			__( 'Easy Forms' , 'yikes-inc-easy-mailchimp-extender' ), 
			'Easy Forms',
			apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
			'yikes-inc-easy-mailchimp', 
			'', // no callback,
			YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_wink_icon.png'
		);
		
		// Sub Pages
		/*************/
			
			/* YIKES Inc. Easy MailChimp Settings */
								
				/* YIKES Inc. Easy MailChimp Manage Forms */
				add_submenu_page(
					'yikes-inc-easy-mailchimp', 
					__( 'Opt-in Forms' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'Opt-in Forms' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-inc-easy-mailchimp', 
					array( $this, 'generateManageFormsPage' )
				);
				
				/* YIKES Inc. Easy MailChimp Manage Lists */
				add_submenu_page(
					'yikes-inc-easy-mailchimp', 
					__( 'Mailing Lists' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'Mailing Lists' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-inc-easy-mailchimp-lists', 
					array( $this, 'generateManageListsPage' )
				);
				
							
				/*
				*	Custom action hook to hook into to add additional
				*	menu items from extensions
				*/
				do_action( 'yikes-mailchimp-menu' );
					
					
			/* YIKES Inc. Easy MailChimp Account Overview */
			if ( get_option('yikes-mc-api-validation') == 'valid_api_key' ) {	
				/* YIKES Inc. Easy MailChimp Settings */
				add_submenu_page(
					'yikes-inc-easy-mailchimp', 
					__( 'Account' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'Account' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-inc-easy-mailchimp-account-overview', 
					array( $this, 'generateAccountDetailsPage' )
				);
			}
		
			
			/* YIKES Inc. Easy MailChimp Settings */
			add_submenu_page(
				'yikes-inc-easy-mailchimp', 
				__( 'Settings.' , 'yikes-inc-easy-mailchimp-extender' ), 
				__( 'Settings' , 'yikes-inc-easy-mailchimp-extender' ), 
				apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
				'yikes-inc-easy-mailchimp-settings', 
				array( $this, 'generatePageOptions' )
			);
							
			/* Support Page */
			add_submenu_page(
				'yikes-inc-easy-mailchimp', 
				__( 'Support' , 'yikes-inc-easy-mailchimp-extender' ), 
				__( 'Support' , 'yikes-inc-easy-mailchimp-extender' ), 
				apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
				'yikes-inc-easy-mailchimp-support', 
				array( $this, 'generateSupportPage' )
			);
						
			/* Add-Ons Page */
			add_submenu_page(
				'yikes-inc-easy-mailchimp', 
				__( 'Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ), 
				__( 'Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ), 
				apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
				'yikes-inc-easy-mailchimp-addons', 
				array( $this , 'generateAddOnsPage' )
			);
			
			/** Hidden Pages **/
			
				/* Add Hidden Edit Form Page */
				add_submenu_page(
					'options.php', 
					__( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-mailchimp-edit-form', 
					array( $this, 'generateEditFormPage' )
				);
				
				/* Add Hidden Migrate Options Page */
				add_submenu_page( 
					'options-writing.php', 
					__( 'YIKES Inc. Easy Forms for MailChimp Upgrade Options Structure' , 'yikes-inc-easy-mailchimp-extender' ), 
					'YIKES Inc. Easy Forms for MailChimp Upgrade Options Structure', 
					'manage_options', 
					'yikes-inc-easy-mailchimp-update' ,
					array( $this , 'migrate_old_yks_mc_options' )
				);
				
				/* Add Hidden Welcome Page */
				add_submenu_page(
					'options.php', 
					__( 'Welcome' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'Welcome' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-mailchimp-welcome', 
					array( $this, 'generateWelcomePage' )
				);
								
				/* Add Hidden 'View List' Page */
				add_submenu_page(
					'options.php', 
					__( 'View List' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'View List' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-mailchimp-view-list', 
					array( $this, 'generateViewListPage' )
				);
				
				/* Add Hidden View User Page */
				add_submenu_page(
					'options.php', 
					__( 'View User' , 'yikes-inc-easy-mailchimp-extender' ), 
					__( 'View User' , 'yikes-inc-easy-mailchimp-extender' ), 
					apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ), 
					'yikes-mailchimp-view-user', 
					array( $this, 'generateViewUserPage' )
				);
							
	}
	
	/*
	*	Redirect a user to an external page
	*	when they click 'Go Pro' in the admin menu
	*	to do: populate with sales URL
	*/
	public function generateAddOnsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/add-ons.php'; // include our add-ons page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp Manage Forms Page
	* 
	* @since    1.0.0
	*/
	function generateManageFormsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/manage-forms.php'; // include our manage forms page
	}
		
	/**
	* Generate YIKES Inc. Easy MailChimp Manage Lists Page
	* 
	* @since    1.0.0
	*/
	function generateManageListsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/manage-lists.php'; // include our lists page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp Account Details Page
	* 
	* @since    1.0.0
	*/
	function generateAccountDetailsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/account-details.php'; // include our account details page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp Support Page
	* 
	* @since    1.0.0
	*/
	function generateSupportPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/support.php'; // include our options page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp Edit Form Page
	* 
	* @since    1.0.0
	*/
	function generateEditFormPage() {
		require_once YIKES_MC_PATH . 'admin/partials/edit-form.php'; // include our options page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp Welcome Page
	* 
	* @since    1.0.0
	*/
	function generateWelcomePage() {
		require_once YIKES_MC_PATH . 'admin/partials/welcome-page/welcome.php'; // include our options page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp View List Page
	* 
	* @since    1.0.0
	*/
	function generateViewListPage() {
		require_once YIKES_MC_PATH . 'admin/partials/view-list.php'; // include our options page
	}
	
	/**
	* Generate YIKES Inc. Easy MailChimp View User Page
	* 
	* @since    1.0.0
	*/
	function generateViewUserPage() {
		require_once YIKES_MC_PATH . 'admin/partials/view-user.php'; // include our options page
	}
	
	/**
	*	Register our plugin settings, and display them on our settings page
	*
	* @since v.5.4
	**/
	function yikes_easy_mc_settings_init() { 	
				
		/* Register General Settings Section */
		
		register_setting( 'yikes_inc_easy_mc_general_settings_page', 'yikes-mc-api-key', array( $this , 'yikes_mc_validate_api_key' ) );
		
		add_settings_section(
			'yikes_easy_mc_settings_general_section_callback',
			'', 
			'', 
			'yikes_inc_easy_mc_general_settings_page'
		);
		
		/* Register Visual Representation of Connection */
		add_settings_field( 
			'connection', 
			__( 'API Connection', 'yikes-inc-easy-mailchimp-extender' ), 
			'yikes_inc_easy_mc_visual_representation_of_connection_callback', // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_general_settings_page', 
			'yikes_easy_mc_settings_general_section_callback' 
		);
		
		/* Register Check Box Setting */
		add_settings_field( 
			'yikes-mc-api-key', 
			__( 'MailChimp API Key', 'yikes-inc-easy-mailchimp-extender' ), 
			'yikes_inc_easy_mc_api_key_field_callback', // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_general_settings_page', 
			'yikes_easy_mc_settings_general_section_callback' 
		);
				
		/* End General Settings */
		
		/* Checkbox Settings */
		register_setting( 'yikes_inc_easy_mc_checkbox_settings_page', 'optin-checkbox-init' );
		
		/* Register General Settings Section */
		add_settings_section(
			'yikes_inc_easy_mc_checkbox_settings',
			'', 
			'', 
			'yikes_inc_easy_mc_checkbox_settings_page'
		);
		
		add_settings_field( 
			'optin-checkbox-init', 
			__( 'Select Checkboxes to Generate', 'yikes-inc-easy-mailchimp-extender' ), 
			'',  // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_checkbox_settings'
		);
		/* End Checkbox Settings */
		
		/* reCAPTCHA Settings */
		
			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-status' );
			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-site-key' );
			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-secret-key' );
		
			/* Register reCaptcha Settings Section */
			add_settings_section(
				'yikes_easy_mc_settings_recpatcha_section',
				'', 
				'', 
				'yikes_inc_easy_mc_recaptcha_settings_page'
			);
			
			add_settings_field( 
				'yikes-mc-recaptcha-site-key', 
				__( 'Enter reCaptcha Site Key', 'yikes-inc-easy-mailchimp-extender' ), 
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);
			
			add_settings_field( 
				'yikes-mc-recaptcha-secret-key', 
				__( 'Enter reCaptcha Secret Key', 'yikes-inc-easy-mailchimp-extender' ), 
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);
			
			add_settings_field( 
				'yikes-mc-recaptcha-status', 
				__( 'Enable ReCaptcha', 'yikes-inc-easy-mailchimp-extender' ), 
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);
			
		/* End reCAPTCHA Settings */
		
		/* Debug Settings */
			register_setting( 'yikes_inc_easy_mc_debug_settings_page' , 'yikes-mailchimp-debug-status' );
			
			/* Register Debug Settings Section */
			add_settings_section(
				'yikes_easy_mc_settings_debug_section',
				'', 
				'', 
				'yikes_inc_easy_mc_debug_settings_page'
			);
			
			add_settings_field( 
				'yikes-mailchimp-debug-status', 
				__( 'Enable Debugging', 'yikes-inc-easy-mailchimp-extender' ), 
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_debug_section'
			);
		
		/* Custom Action Hook For Addon Settings */
			// custom action hook to allow our add-ons to take
			// advantage of our base settings
			do_action( 'yikes-mailchimp-settings-field' );
			
	}
	
	/** 
	*	Options Sanitation & Validation
	*	@since complete re-write
	**/
	function yikes_mc_validate_api_key( $input ) {
		$old = get_option( 'yikes-mc-api-key' , '' );
		$api_key = trim( $input );
		// only re-run the API request if our API key has changed
		if( $old != $api_key ) {
			// initialize MailChimp Class
			try {
				$MailChimp = new MailChimp( $api_key );
				// retreive our list data
				$validate_api_key_response = $MailChimp->call( 'helper/ping' , array( 'apikey' => $api_key ) );
				update_option( 'yikes-mc-api-validation' , 'valid_api_key' );
			} catch ( Exception $e ) {
				// log to our error log
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $e->getMessage() , __( "Connecting to MailChimp" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Settings Page/General Settings" , 'yikes-inc-easy-mailchimp-extender' ) );
				update_option( 'yikes-mc-api-invalid-key-response' , $e->getMessage() );
				update_option( 'yikes-mc-api-validation' , 'invalid_api_key' );
			}	
		}
		// returned the api key
		return $api_key;
	}
	
	/**
	* Generate YIKES Inc. Easy Forms for MailChimp Options Page
	* 
	* @since    1.0.0
	*/
	function generatePageOptions() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/options.php'; // include our options page
	}
		
	/**
	*	Check if users API key is valid, if not	
	*	this function will apply a disabled attribute
	*	to form fields. (input, dropdowns, buttons etc.)
	* 	@since v5.5 re-write
	**/
	public function is_user_mc_api_valid_form( $echo=true ) {
		if( $echo == true ) {
			if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) {
				echo 'disabled="disabled"';
			}
		} else {
			if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	/**
	 * Check for existing plugin options
	 *	if they exist, we need to migrate our options to 
	 * the correct WordPress options API (old plugin stored options wierdly)
	 *
	 * @since    1.0.0
	 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function check_for_old_yks_mc_options() {
		$old_plugin_options = get_option( 'ykseme_storage' );
		// only perform options migrations if the site is not a multi-site setup
		if ( !is_multisite() ) {
			if( apply_filters( 'yikes_mc_old_options_filter' , $old_plugin_options ) ) {
				// display a notice to the user that they should 'migrate' 
				// from the old plugin settings to the new ones
				add_action( 'admin_notices', array( $this , 'display_options_migrate_notice' ) , 11 );
			}
		}
	}
	
	/**
	 * Migrate our old options , to the new options API
	 * moving from 5.5 and beyond..
	 * @since
	*/
	public function migrate_old_yks_mc_options() {
		// include our migrate options helper file
		include_once YIKES_MC_PATH . 'admin/partials/upgrade-helpers/upgrade-migrate-options.php';
	}
	
	/** 
		Admin Notices 
		- Notifications displayed at the top of admin pages, back to the user
	**/
	
		/**
		 * Check for existing plugin options
		 *	if they exist, we need to migrate our options to 
		 * the correct WordPress options API (old plugin stored options wierdly)
		 *
		 * @since    1.0.0
		 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function display_options_migrate_notice() {
			
			// Confirm that the necessary forms table in the database exists, else bail
			global $wpdb;
			if( $wpdb->get_var("show tables like '" . $wpdb->prefix . "yikes_easy_mc_forms'") != $wpdb->prefix . "yikes_easy_mc_forms" ) {
				return;
			}
			
			if( isset( $_GET['yikes-mc-options-migration-dismissed'] ) && $_GET['yikes-mc-options-migration-dismissed'] == 'true' ) {
					// Delete the options, start a-new! (this will disable the migration notice altogether)
					delete_option( 'widget_yikes_mc_widget' );
					delete_option( 'api_validation' );
					delete_option( 'ykseme_storage' );
					delete_option( 'yikes-mc-lists' );
				?>
					<div class="yikes-easy-mc-updated migrate-options-notice">
						<p><?php printf( __( "The previously stored options for %s have been cleared from the database. You should update the plugin options on the <a href='%s' title='Settings Page'>settings page</a> before continuing. You should also update the shortcodes used to generate your forms, and any widgets you may have previously set-up.", 'yikes-inc-easy-mailchimp-extender' ), '<strong>YIKES Inc. Easy Forms for MailChimp</strong>', admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings' ) ); ?></p>
					</div>
				<?php
			} else {
			?>
				<div class="yikes-easy-mc-updated migrate-options-notice">
					<p><?php printf( __( "It looks like you're upgrading from a previous version of %s.", 'yikes-inc-easy-mailchimp-extender' ), '<strong>YIKES Inc. Easy Forms for MailChimp</strong>' ); ?> <?php printf( __( "In the newest version of %s, the options data structure has changed. We've also moved the mailing lists into its own database table to allow for some higher level customization. Now you can easily create multiple forms and assign them to the same mailing list." , 'yikes-inc-easy-mailchimp-extender' ), '<strong>YIKES Inc. Easy Forms for MailChimp</strong>' ); ?></p>
					<p><?php _e( "Before you continue, it's strongly recommended you the perform the migration to ensure the plugin continues to function properly.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					<p><em><?php _e( "It's also strongly recommended that you take a backup of your database.", 'yikes-inc-easy-mailchimp-extender' ); ?></em></p>
					<section id="migration-buttons">
						<!-- migrate button -->
						<form>
							<input type="hidden" name="yikes-mc-update-option-structure" value="yikes-mc-update-option-structure" />
							<a href="<?php echo wp_nonce_url( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-update' ) ), 'yikes-mc-migrate-options' , 'migrate_options_nonce' ); ?>" class="button-secondary"><?php _e( 'Perform Migration' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
						</form>
						<!-- dismiss button -->
						<form>
							<a href="<?php echo wp_nonce_url( esc_url_raw( admin_url() ), 'yikes-mc-dismiss-migration' , 'dismiss_migration_nonce' ); ?>" class="button-secondary"><?php _e( 'Dismiss Notice' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
						</form>
					</section>
					
				</div>
			<?php
			}
		}
		
		/*
		*	Search through multi dimensional array
		*	and return the index ( used to find the list name assigned to a form )
		*	- http://stackoverflow.com/questions/6661530/php-multi-dimensional-array-search
		*/
		function findMCListID($id, $array) {
		   foreach ($array as $key => $val) {
			   if ($val['id'] === $id) {
				   return $key;
			   }
		   }
		   return null;
		} // end
		
		/* Ajax Migrate Options */
		function migrate_archived_options() {
			// all options prefixed with 'yikes-mc-'
			$option_name = 'yikes-mc-'.$_POST['option_name'];
			$option_value = $_POST['option_value'];	
			if( json_decode( $option_value ) ) {
				// decode our lists() array, and store it
				$opt_value = json_decode( $option_value, true );
			} else {
				$opt_value = $option_value;
			}
			update_option( $option_name, $opt_value );
			wp_die(); // this is required to terminate immediately and return a proper response
			exit;
		}
		
		/* Ajax Migrate Forms */
		function migrate_previously_setup_forms() {
			$option_name = $_POST['option_name'];
			$done = $_POST['done_import'];
			// Create some starter forms for the user
			// based on previously imported lists (to our old version)
			if( $option_name == 'yikes-mc-lists' ) {	
				global $wpdb;
				$option_value = $_POST['option_value'];	
				$new_options = json_decode( stripslashes_deep( $option_value ) , true );
				
				$list_id = $new_options['id'];
				$form_name = $new_options['name'];
				$fields = $new_options['fields']; // our fields array
					
				$custom_styles = isset( $new_options['custom_styles'] ) ? json_encode( $new_options['custom_styles'] ) : '0'; // store as an array with all of our styles
				$custom_template = isset( $new_options['custom_template'] ) ? json_encode( $new_options['custom_template'] ) : '0'; // store template data as an array ( active , template used )
				$send_welcome_email = isset( $new_options['yks_mailchimp_send_welcome_'.$list_id] ) ? '0' : '1';
				$redirect_user_on_submit = isset( $new_options['yks_mailchimp_redirect_'.$list_id] ) ? '1' : '0';
				$redirect_page = isset( $new_options['page_id_'.$list_id] ) ? $new_options['page_id_'.$list_id] : '';
						
				/* Insert Forms Function  */
				$wpdb->insert(
					$wpdb->prefix . 'yikes_easy_mc_forms',
					array(
						'list_id' => $list_id,
						'form_name' => $form_name,
						'form_description' => '',
						'fields' => json_encode( $fields ),
						'custom_styles' => $custom_styles,
						'custom_template' => $custom_template,
						'send_welcome_email' => $send_welcome_email,
						'redirect_user_on_submit' => $redirect_user_on_submit,
						'redirect_page' => $redirect_page,
						'submission_settings' => '',
						'optin_settings' => '',
						'error_messages' => '',
						'custom_notifications' => '',
						'impressions' => '0',
						'submissions' => '0',
						'custom_fields' => '',
					)
				);						
			}
			if( $done == 'done' ) {
				wp_send_json( array( 'form_name' => $form_name, 'completed_import' => true ) );
			} else {
				wp_send_json( array( 'form_name' => $form_name, 'completed_import' => false ) );
			}
			wp_die();
			exit;
		}
		
		/*
		*	generate_options_pages_sidebar_menu();
		*	Render our sidebar menu on all of the setings pages (general, form, checkbox, recaptcha, popup, debug etc. )
		*	@since v5.6 - complete re-write
		*/
		public function generate_options_pages_sidebar_menu() {
			if( isset( $_REQUEST['section'] ) ) {
				$selected = $_REQUEST['section'];
			}
			$installed_addons = get_option( 'yikes-easy-mc-active-addons' , array() );
			// sort our addons array alphabetically so they appear in similar orders across all sites
			asort( $installed_addons );
			?>
				<h3><span><?php _e( 'Additional Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
				<div class="inside">
					<ul id="settings-nav">
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'general-settings' || !isset( $_REQUEST['section'] ) ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'general-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=general-settings' ) ) ); ?>"><?php _e( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'integration-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'integration-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=integration-settings' ) ) ); ?>"><?php _e( 'Integration Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'recaptcha-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'recaptcha-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) ); ?>"><?php _e( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>						
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'api-cache-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'api-cache-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=api-cache-settings' ) ) ); ?>"><?php _e( 'API Cache Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  'debug-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'debug-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings' ) ) ); ?>"><?php _e( 'Debug Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  'import-export-forms' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'import-export-forms' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) ); ?>"><?php _e( 'Import/Export' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
					</ul>
					<?php 
						// create our add-on settings pages
						if( !empty( $installed_addons ) ) {
							?>
							<hr class="add-on-settings-divider" />
							<strong><?php _e( 'Addon Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<ul id="addon-settings-nav">
							<?php
							foreach( $installed_addons as $addon_name ) {
								?>
									<li>	
										<?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  $addon_name ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => $addon_name, 'addon' => 'true' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section='.$addon_name ) ) ); ?>"><?php echo ucwords( str_replace( '-' , ' ' , $addon_name ) ); ?></a></li>
								<?php
							}
							?>
							</ul>
							<?php
						}
						?>
				</div> <!-- .inside -->
			<?php
		}
		
		/*
		*	generate_manage_forms_sidebar();
		*	Render our sidebar menu on all of the setings pages (general, form, checkbox, recaptcha, popup, debug etc. )
		*	@since v5.6 - complete re-write
		*/
		public function generate_manage_forms_sidebar( $lists ) {
			// create a custom URL to allow for creating fields
			$url = esc_url_raw( 
				add_query_arg(
					array(
						'action' => 'yikes-easy-mc-create-form',
						'nonce' => wp_create_nonce( 'create_mailchimp_form' )
					)
				)
			);
			?>
				<h3><?php _e( 'Create a New Signup Form' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
				
				<div class="inside">
																
					<p class="description"><?php _e( "Give your form a name, select a MailChimp list to assign users to, then click 'Create'.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					
					<form id="import-list-to-site" method="POST" action="<?php echo $url; ?>">
						<input type="hidden" name="import-list-to-site" value="1" />
						<!-- Name your new form -->
						<label for="form-name"><strong><?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<input type="text" class="widefat input-field" placeholder="<?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="form-name" id="form-name" <?php $this->is_user_mc_api_valid_form( true ); ?> required>
						</label>
						<!-- Name your new form -->
						<label for="form-description"><strong><?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<textarea class="widefat input-field form-description" placeholder="<?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="form-description" id="form-description" <?php $this->is_user_mc_api_valid_form( true ); ?>></textarea>
						</label>
						<!-- Associate this form with a list! -->
						<label for="associated-list"><strong><?php _e( 'Associated List' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<select name="associated-list" id="associated-list" class=" input-field" <?php $this->is_user_mc_api_valid_form( true ); if( isset( $lists ) && empty( $lists ) ) { echo 'disabled="disabled"'; } ?>>
								<?php
									if( isset( $lists ) && !empty( $lists ) ) {
										foreach( $lists as $mailing_list ) {
											?>
												<option value="<?php echo $mailing_list['id']; ?>"><?php echo stripslashes( $mailing_list['name'] ) . ' (' . $mailing_list['stats']['member_count'] . ') '; ?></option>
											<?php
										}
									} else {
										if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) {
											?>
												<option><?php echo __( "Please enter a valid API key." , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
											<?php
										} else {
											?>
												<option><?php echo __( "No lists were found on the account." , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
											<?php

										}
									}
								?>
							</select>
							<?php
								if( isset( $lists ) && empty( $lists ) ) {
									if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'invalid_api_key' ) {
										?>
											<p class="description">
												<?php printf( __( 'Head over to <a href="http://www.MailChimp.com" title="%s">MailChimp</a> to create a new list.', 'yikes-inc-easy-mailchimp-extender' ) , __( 'Create a list' , 'yikes-inc-easy-mailchimp-extender' ) ); ?>
											</p>
										<?php
									}
								}
							?>
						</label>
						<?php 
							if( $this->is_user_mc_api_valid_form( false ) ) {
								echo submit_button( __( 'Create', 'yikes-inc-easy-mailchimp-extender' ) , 'primary' , '' , false , array( 'style' => 'margin:.75em 0 .5em 0;' ) ); 
							} else {
								echo '<p class="description">' . __( "Please enter a valid MailChimp API key to get started." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
								?>
									<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&settings-updated=true' ) ); ?>"><?php _e( 'general settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
								<?php
							}
						?>
					</form>	
				</div> <!-- .inside -->
			<?php
		}
		
		/*
		*	Generate a dropdown of post and pages
		*	so the user can send the user to on form submission
		*/
		public function generate_page_redirect_dropdown( $redirect, $redirect_page ) {
				$post_types = get_post_types();
				?>
				<label id="redirect-user-to-selection-label" for="redirect-user-to-selection" class="<?php if( $redirect == '0' ) { echo 'yikes-easy-mc-hidden'; } ?>">
					<?php _e( "Select A Page or Post" , 'yikes-inc-easy-mailchimp-extender' ); ?>
					<select id="redirect-user-to-selection" name="redirect-user-to-selection">
				<?php
					// loop over registered post types, and query!
						foreach( $post_types as $registered_post_type ) {
							// exclude a few built in custom post types
							if( !in_array( $registered_post_type , array( 'attachment' , 'revision' , 'nav_menu_item' ) ) ) {
								// run our query, to retreive the posts
								$pages = get_posts( array(
									'order' => 'ASC',
									'orderby' => 'post_title',
									'post_type' => $registered_post_type,
									'post_status' => 'publish',
									'numberposts' => -1
								) );
								// only show cpt's that have posts assigned
								if( !empty( $pages ) ) {
									?>
									<optgroup label="<?php echo ucwords( str_replace( '_' , ' ' , $registered_post_type ) ); ?>">
									<?php
										foreach( $pages as $page ) {
											?><option <?php selected( $redirect_page , $page->ID ); ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option><?php
										}
									?>
									</optgroup>
									<?php
								}
							}
						}
					?>
					</select>
				</label>
			<?php
		}
		
		/*
		*	generate_show_some_love_container()
		*	Generate a container, with some author info
		*
		* 	Displayed in sidebars
		*/
		public function generate_show_some_love_container() {
			// if no active add-ons are installed,
			// lets display our branding and add-on sidebar
			if( get_option( 'yikes-easy-mc-active-addons' , array() ) == array() ) {
			
				/* On Edit Forms Page Display Upsell to Customizer */
				$screen = get_current_screen();
				if( isset( $screen ) && $screen->base == 'admin_page_yikes-mailchimp-edit-form' ) {
				?>		
				 
					<div class="postbox yikes-easy-mc-postbox show-some-love-container">
					
						<?php $this->generate_edit_forms_upsell_ad(); ?>
					
					</div>
						
				<?php } else { ?>
				
					<div class="postbox yikes-easy-mc-postbox show-some-love-container">
					
						<!-- review us container -->
						<h3 data-alt-text="<?php _e( 'About YIKES Inc.', 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Show YIKES Inc. Some Love' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
						<div id="review-yikes-easy-mc" class="inside">
										
							<p>
								<?php _e( 'Leave a review' , 'yikes-inc-easy-mailchimp-extender' ); ?>
								<p class="star-container">
									<a href="https://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender" target="_blank">
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
									</a>
								</p>
							</p>
										
							<?php _e( 'Tweet about it' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							<p class="sidebar-container">
								<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" data-text="I'm using the Easy Forms for MailChimp plugin by @YikesInc to grow my mailing list - it's awesome! -" data-hashtags="MailChimp">Tweet</a>
								<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
							</p>
											
							<?php _e( 'Vote that the plugin works' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							<p class="sidebar-container">
								<a href="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" target="_blank">
									<?php _e( 'Vote for Compatibility' , 'yikes-inc-easy-mailchimp-extender' ); ?>
								</a>
							</p>
						</div>
						
						<p class="description sidebar-footer-text"><?php printf( __( "This plugin made with %s by %s" , 'yikes-inc-easy-mailchimp-extender' ), '<span class="dashicons dashicons-heart yikes-love"></span>', '<a href="http://www.yikesinc.com" target="_blank" title="YIKES Inc.">YIKES Inc.</a>' ); ?> </p>
						
						<section id="about-yikes-inc" class="inside">
							<a href="https://www.yikesinc.com" target="_blank" title="YIKES Inc.">
								<img src="<?php echo YIKES_MC_URL . 'includes/images/About_Page/yikes-logo.png'; ?>" class="about-sidebar-yikes-logo" />
							</a>
							<p><strong>YIKES Inc.</strong> &mdash; <?php _e( 'is a web design and development company located in Philadelphia, Pennsylvania, US. YIKES specializes in custom WordPress theme and plugin development, site maintenance, eCommerce, custom-built web-based applications and more.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
						</section>
						
						<p class="description sidebar-footer-text"><a href="#" class="about-yikes-inc-toggle" data-alt-text="<?php _e( 'Show YIKES Some Love', 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'About YIKES', 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
						
					</div>
				
				<?php } ?>
				
				<div class="postbox yikes-easy-mc-postbox">
								
					<!-- review us container -->
					<h3><?php _e( 'Easy Forms for MailChimp Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
					<div id="review-yikes-easy-mc" class="inside">
						<p><?php _e( "Check out available add-ons for some seriously enhanced features." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
						<p><a class="button-secondary" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-addons' ) ); ?>" title="<?php _e( 'View Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'View Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
					</div>
								
				</div>
				<?php
			}
		}
		
		/*
		*	generate_form_editor( $list_id )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function generate_form_editor( $form_fields, $list_id, $merge_variables, $interest_groups ) {
		
			// if no list id, die!
			if( ! $list_id ) {
				die( __( "We've encountered an error. No list ID was sent." , 'yikes-inc-easy-mailchimp-extender' ) );
			}		
			
			if( ! $merge_variables ) {
				die( __( "We've encountered an error. Reload the page and try again. If the error persists, please reach out to support." , 'yikes-inc-easy-mailchimp-extender' ) );
			}		
			
			if( ! empty( $form_fields ) ) {
			
				// find any fields that are assigned to this form, that don't exist in MailChimp
				// or else were going to run into issues when we submit the form
				$available_merge_variables = array();
				$available_interest_groups = array();
				
				$assigned_fields= array();
				
				// loop over merge variables
				if( ! empty( $merge_variables['data'][0]['merge_vars'] ) ) {
					foreach( $merge_variables['data'][0]['merge_vars'] as $merge_tag ) {
						$available_merge_variables[] = $merge_tag['tag'];
					}
				}
				
				// loop over interest groups
				foreach( $interest_groups as $interest_group ) {
					$available_interest_groups[] = $interest_group['id'];
				}
				
				// build our assigned fields
				foreach( $form_fields as $field => $value ) {
					$assigned_fields[] = $field;
				}
				
				$merged_fields = array_merge( $available_merge_variables , $available_interest_groups );
				$excluded_fields = array_diff( $assigned_fields , $merged_fields );
				
				$i = 1;
				foreach( $form_fields as $field ) {
					
					if( isset( $field['merge'] ) ) {
					?>
						<section class="draggable" id="<?php echo $field['merge']; ?>">
							<!-- top -->
							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus"></span><?php echo stripslashes( $field['label'] ); ?>
								<?php if( in_array( $field['merge'] , $excluded_fields ) ) { ?>
									<img src="<?php echo YIKES_MC_URL . 'includes/images/warning.svg'; ?>" class="field-doesnt-exist-notice" title="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
								<?php } ?>
								<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $field['type']; ?></small></span>
							</a>
							<!-- expansion section -->
							<div class="yikes-mc-settings-expansion-section">
								
								<?php if( in_array( $field['merge'] , $excluded_fields ) ) { ?>
									<p class="yikes-mc-warning-message"><?php _e( "This field no longer exists in this list. Delete this field from the form to prevent issues on your website." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								<?php } ?>
								
								<!-- store the label -->
								<input type="hidden" name="field[<?php echo $field['merge']; ?>][label]" value="<?php echo $field['label']; ?>" />
								<input type="hidden" name="field[<?php echo $field['merge']; ?>][type]" value="<?php echo $field['type']; ?>" />
								<input type="hidden" name="field[<?php echo $field['merge']; ?>][merge]" value="<?php echo $field['merge']; ?>" />
								<input type="hidden" class="field-<?php echo $field['merge']; ?>-position position-input" name="field[<?php echo $field['merge']; ?>][position]" value="<?php echo $i++; ?>" />
								
								<?php if ( $field['type'] == 'radio' || $field['type'] == 'dropdown' || $field['type'] == 'select' ) { ?>
									<input type="hidden" name="field[<?php echo $field['merge']; ?>][choices]" value='<?php echo stripslashes( $field['choices'] ); ?>' />			
								<?php } ?>
								
								<!-- Single or Double Optin -->
								<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->
									
									<table class="form-table form-field-container">
													
										<!-- Merge Tag -->
										<tr valign="top">
											<td scope="row">
												<label for="merge-tag">
													<?php _e( 'Merge Tag' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input class="widefat merge-tag-text" type="text" readonly value="<?php echo $field['merge']; ?>">
											</td>
										</tr>
										
										<!-- Placeholder Value -->
										<?php switch( $field['type'] ) { 
											
											case 'text':
											case 'email':
											case 'url':
											case 'number';
											case 'birthday':
											case 'date':
											case 'zip':
											case 'phone':
										?>
										<!-- Placeholder -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['merge']; ?>][placeholder]" value="<?php echo isset( $field['placeholder'] ) ? stripslashes( wp_strip_all_tags( $field['placeholder'] ) ) : '' ; ?>" />
												<p class="description"><small><?php _e( "Assign a placeholder value to this field.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<?php
											break;
										}
										?>
										
										<?php 
											/* 
											*	Loop over field types and store necessary formats
											*	( date, birthday - dateformat ; phone - phoneformat )
											*/
											switch( $field['type'] ) {
												/* Store the date format, for properly rendering dates on the front end */
												case 'date':
													$date_format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD/YYYY';
													?>
														<input type="hidden" name="field[<?php echo $field['merge']; ?>][date_format]" value="<?php echo strtolower( str_replace( 'YYYY' , 'YY' , $date_format ) ); ?>" />
													<?php
												break;
												
												case 'birthday':
													$date_format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD';
													?>
														<input type="hidden" name="field[<?php echo $field['merge']; ?>][date_format]" value="<?php echo strtolower( str_replace( 'YYYY' , 'YY' , ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD' ) ); ?>" />
													<?php
												break;
												
												/* Store the phone format, for properly regex pattern */
												case 'phone':
													?>
														<input type="hidden" name="field[<?php echo $field['merge']; ?>][phone_format]" value="<?php echo $field['phone_format']; ?>" />
													<?php
												break;
											}
										?>
										
										<!-- Default Value -->
										<?php switch( $field['type'] ) { 
											default:
											case 'text':
											case 'number':
											case 'url':
										?>
											<tr valign="top">
												<td scope="row">
													<label for="placeholder">
														<?php _e( 'Default Value' , 'yikes-inc-easy-mailchimp-extender' ); ?>
													</label>
												</td>
												<td>
													<input <?php if( $field['type'] != 'number' ) { ?> type="text" <?php } else { ?> type="number" <?php } ?> class="widefat" name="field[<?php echo $field['merge']; ?>][default]" <?php if( $field['type'] != 'url' ) { ?> value="<?php echo isset( $field['default'] ) ? stripslashes( wp_strip_all_tags( $field['default'] ) ) : ''; ?>" <?php } else { ?> value="<?php echo isset( $field['default'] ) ? stripslashes( wp_strip_all_tags( esc_url_raw( $field['default'] ) ) ) : ''; ?>" <?php } ?> />
													<p class="description"><small><?php _e( "Assign a default value to populate this field with on initial page load.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													<?php 
													switch( $field['type'] ) { 
														case 'text':
															?>
																<p><small class="pre-defined-tag-link"><a href="#TB_inline?width=600&height=550&inlineId=pre-defined-tag-container" onclick="storeGlobalClicked( jQuery( this ) );" class="thickbox"><?php _e( 'View Pre-Defined Tags' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></p>
															<?php
														break;
													} ?>
												</td>
											</tr>
										<?php 
												break;
											
											case 'radio':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<?php if( ! isset( $field['default_choice'] ) || empty( $field['default_choice'] ) ) { $decode = json_decode( stripslashes_deep( $field['choices'] ) , true ); $field['default_choice'] = $decode[0]; }
														$x = 0;
														foreach( json_decode( stripslashes( $field['choices'] ) , true ) as $choice => $value ) { ?>
															<label for="<?php echo $field['merge'].'-'.$x; ?>">	
																<input id="<?php echo $field['merge'].'-'.$x; ?>" type="radio" name="field[<?php echo $field['merge']; ?>][default_choice]" value="<?php echo $value; ?>" <?php checked( $field['default_choice'] , $value ); ?>><?php echo $value; ?>&nbsp;
															</label>
														<?php $x++; } ?>
														<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>
												
											<?php
												break;
												
											case 'dropdown':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<select type="default" name="field[<?php echo $field['merge']; ?>][default_choice]">
															<?php foreach( json_decode( stripslashes( $field['choices'] ) , true ) as $choice => $value ) { ?>
																<option value="<?php echo $choice; ?>" <?php selected( $field['default_choice'] , $choice ); ?>><?php echo stripslashes( $value ); ?></option>
															<?php } ?>
														</select>
														<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>
												
											<?php
												break;
												
												case "birthday":
												case "address":
													break;
													
										?>
										
										<?php } // end Default Value ?>
										
										
										<!-- Field Description -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<textarea class="widefat field-description-input" name="field[<?php echo $field['merge']; ?>][description]"><?php echo isset( $field['description'] ) ? stripslashes( esc_html( $field['description'] ) ) : '' ; ?></textarea>
												<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and will provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Additional Classes -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['merge']; ?>][additional-classes]" value="<?php echo isset( $field['additional-classes'] ) ? stripslashes( wp_strip_all_tags( $field['additional-classes'] ) ) : '' ; ?>" />
												<p class="description"><small><?php printf( __( "Assign additional classes to this field. %s.", 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/bundled-css-classes/' ) . '">' . __( 'View bundled classes', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );?></small></p>
											</td>
										</tr>
										<!-- Required Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="field-required">
													<?php _e( 'Field Required?' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $checked = isset( $field['require'] ) ? $field['require'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['merge']; ?>][require]" <?php checked( $checked , 1 ); ?> <?php if( $field['merge'] == 'EMAIL' ) {  ?> disabled="disabled" checked="checked" title="<?php echo __( 'Email is a required field.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
												<p class="description"><small><?php _e( "Require this field to be filled in before the form can be submitted.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Visible Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="hide-field">
													<?php _e( 'Hide Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide'] ) ? $field['hide'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['merge']; ?>][hide]" <?php checked( $hide , 1 ); ?> <?php if( $field['merge'] == 'EMAIL' ) {  ?> disabled="disabled" title="<?php echo __( 'Cannot toggle email field visibility.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
												<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Field Label Visibility -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide_label = isset( $field['hide-label'] ) ? $field['hide-label'] : '0'; ?>
												<input type="checkbox" name="field[<?php echo $field['merge']; ?>][hide-label]" value="1" <?php checked( $hide_label , 1 ); ?>/>
												<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Display Phone/Date Formats back to the user -->
										<?php 
											switch( $field['type'] ) {												
												/* Store the phone format, for properly regex pattern */
												case 'phone':
												case 'birthday':
												case 'date':
													?>
														<tr valign="top">
															<td scope="row">
																<label for="placeholder">		
																	<?php 
																		switch( $field['type'] ) {
																			default:
																			case 'birthday':
																				$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD';
																				break;
																			
																			case 'date':
																				$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD/YYYY';
																				break;
													
																			case 'phone':
																				$type = __( 'Phone Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = ( ( $field['phone_format'] == 'none' ) ? __( 'International', 'yikes-inc-easy-mailchimp-extender' ) : $field['phone_format'] . ' - (###) ### - ####' );
																				break;
																		}
																		echo $type;
																	?>
																</label>
															</td>
															<td>
																<strong><?php echo $format; ?></strong>
																<p class="description"><small>
																	<?php printf( __( 'To change the %s please head over to <a href="%s" title="MailChimp" target="_blank">MailChimp</a>. If you alter the format, you should re-import this field.', 'yikes-inc-easy-mailchimp-extender' ), strtolower( $type ), esc_url( 'http://www.mailchimp.com' ) ); ?>
																</small></p>
															</td>
														</tr>
													<?php
												break;
												// others..
												default:
													break;
											}
										?>
										<!-- End Date/Phone Formats -->
										<!-- Toggle Buttons -->
										<tr valign="top">
											<td scope="row">
												&nbsp;
											</td>
											<td>
												<span class="toggle-container">
													<a href="#" class="close-form-expansion"><?php _e( "Close" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |
													<a href="#" class="remove-field" alt="<?php echo $field['merge']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
												</span>
											</td>
										</tr>
									</table>
								</p>		
													
							</div>
						</section>
						<?php
						
						
						
					} else { // THIS IS AN INTEREST GROUP!
					
						?>
						<section class="draggable" id="<?php echo $field['group_id']; ?>">
							<!-- top -->
							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus"></span><?php echo stripslashes( $field['label'] ); ?>
								<?php if( in_array( $field['group_id'] , $excluded_fields ) ) { ?>
									<img src="<?php echo YIKES_MC_URL . 'includes/images/warning.svg'; ?>" class="field-no-longer-exists-warning" title="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
								<?php } ?>
								<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $field['type']; ?></small></span>
							</a>
							<!-- expansion section -->
							<div class="yikes-mc-settings-expansion-section">
								
								<!-- check if this field exists in the available interest group array -->
								<?php if( in_array( $field['group_id'] , $excluded_fields ) ) { ?>
									<p class="yikes-mc-warning-message"><?php _e( "This field no longer exists in this list. Delete this field from the form to prevent issues on the front end." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								<?php } ?>
								
								<!-- store the label -->
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][label]" value="<?php echo $field['label']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][type]" value="<?php echo $field['type']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][group_id]" value="<?php echo $field['group_id']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][groups]" value='<?php echo stripslashes( $field['groups'] ); ?>' />			
								
								<!-- Single or Double Optin -->
								<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->
									
									<table class="form-table form-field-container">
										<!-- Default Value -->
										<?php switch( $field['type'] ) { 
											
											default:
											case 'radio':
											case 'checkboxes':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<?php 	
															if( $field['type'] != 'checkboxes' ) {
																if( !isset( $field['default_choice'] ) ) { $field['default_choice'] =  json_decode( stripslashes( $field['groups'] ) , true ); }
															} else {
																if( !isset( $field['default_choice'] ) ) { $field['default_choice'] = array(); }
															}
														$i = 0; 
														foreach( json_decode( stripslashes( $field['groups'] ) , true ) as $group ) { ?>
															<label for="<?php echo $field['group_id'].'-'.$i; ?>">
																<input id="<?php echo $field['group_id'].'-'.$i; ?>" type="<?php if( $field['type'] == 'radio' || $field['type'] == 'hidden' ) { ?>radio<?php } else if( $field['type'] == 'checkboxes' ) { ?>checkbox<?php } ?>" name="field[<?php echo $field['group_id']; ?>][default_choice]<?php if( $field['type'] == 'checkboxes' ) {echo '[]';}?>" value="<?php echo $i; ?>" <?php if( $field['type'] == 'radio' || $field['type'] == 'hidden' ) { checked( $field['default_choice'][0] , $i ); } else if( $field['type'] == 'checkboxes' ) { if( in_array( $i , $field['default_choice'] ) ) { echo 'checked="checked"'; } }?>><?php echo stripslashes( str_replace( '~' , '\'' , $group['name'] ) ); ?>&nbsp;
															</label>
														<?php 
															$i++;
															} 
														?>
														<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>
												
											<?php
												break;
												
											case 'dropdown':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<select type="default" name="field[<?php echo $field['group_id']; ?>][default_choice]">
															<?php $i = 0; foreach( json_decode( stripslashes( $field['groups'] ) , true ) as  $group ) { ?>
																<option value="<?php echo $i; ?>" <?php selected( $field['default_choice'] , $i ); ?>><?php echo stripslashes( $group['name'] ); ?></option>
															<?php $i++; } ?>
														</select>
														<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>
												
											<?php
												break;
										?>
										
										<?php } // end Default Value ?>
										
										<!-- Field Description -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<textarea class="widefat field-description-input" name="field[<?php echo $field['group_id']; ?>][description]"><?php echo isset( $field['description'] ) ? stripslashes( esc_html( $field['description'] ) ) : '' ; ?></textarea>
												<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										
										<!-- Additional Classes -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['group_id']; ?>][additional-classes]" value="<?php echo isset( $field['additional-classes'] ) ? stripslashes( wp_strip_all_tags( $field['additional-classes'] ) ) : '' ; ?>" />
												<p class="description"><small><?php printf( __( "Assign additional classes to this field. %s.", 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/bundled-css-classes/' ) . '">' . __( 'View bundled classes', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );?></small></p>
											</td>
										</tr>
										<!-- Required Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="field-required">
													<?php _e( 'Field Required?' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $checked = isset( $field['require'] ) ? $field['require'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['group_id']; ?>][require]" <?php checked( $checked , 1 ); ?>>
												<p class="description"><small><?php _e( "Require this field to be filled in before the form can be submitted.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Visible Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="hide-field">
													<?php _e( 'Hide Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide'] ) ? $field['hide'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['group_id']; ?>][hide]" <?php checked( $hide , 1 ); ?>>
												<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Field Label Visibility -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide-label'] ) ? $field['hide-label'] : '0'; ?>
												<input type="checkbox" name="field[<?php echo $field['group_id']; ?>][hide-label]" value="1" <?php checked( $hide , 1 ); ?>/>
												<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Buttons -->
										<tr valign="top">
											<td scope="row">
												&nbsp;
											</td>
											<td>
												<span class="toggle-container">
													<a href="#" class="close-form-expansion"><?php _e( "Close" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |
													<a href="#" class="remove-field" alt="<?php echo $field['group_id']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
												</span>
											</td>
										</tr>
									</table>
								</p>		
													
							</div>
						</section>
						<?php
					}	// its an interest group!
				}		
			} else {
				?>
					<h4 class="no-fields-assigned-notice non-draggable-yikes"><em><?php _e( 'No fields are assigned to this form. Select fields from the right hand column to add to this form.' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></h4>
				<?php
			}
				/* Pre Defined Merge Tag Container - Always rendered so the modal appears and links are clickable on initial page load */
				add_thickbox();
				// enqueue jquery qtip for our tooltip
				wp_enqueue_script( 'jquery-qtip-tooltip' , YIKES_MC_URL . 'admin/js/min/jquery.qtip.min.js' , array( 'jquery' ) );
				wp_enqueue_style( 'jquery-qtip-style' ,  YIKES_MC_URL . 'admin/css/jquery.qtip.min.css' );
					$available_tags = array(
						array(
							'tag' => '{page_title}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page Title', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_title}</small></h4><hr />' . __( 'Pre-populate the field with the current page or post title that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page Title', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{page_id}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page ID', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_id}</small></h4><hr />' . __( 'Pre-populate the field with the current page or post ID that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page ID', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{page_url}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page URL', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_url}</small></h4><hr />' . __( 'Pre-populate the field with the current page URL that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page URL', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{blog_name}',
							'description' => '<h4 class="tooltip-title">' . __( 'Blog Name', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{blog_name}</small></h4><hr />' . __( 'Pre-populate the field with the current blog name that the user is on when opting in to your mailing list. This is especially helpful for multi-site networks.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Blog Name', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{user_logged_in}',
							'description' => '<h4 class="tooltip-title">' . __( 'User Logged In', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{user_logged_in}</small></h4><hr />' . __( 'Detects if a user is logged in and pre-populates the field with an appropriate value.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'User Logged In', 'yikes-inc-easy-mailchimp-extender' )
						),
					);
				?>
				<!-- tooltips -->
				<script type="text/javascript">
					/* Initialize Qtip tooltips for pre-defined tags */
					jQuery( document ).ready( function() {
						jQuery( '.dashicons-editor-help' ).each( function() {
							 jQuery( this ).qtip({
								 content: {
									 text: jQuery( this ).next( '.tooltiptext' ),
									 style: { 
										def: false
									 }
								 }
							 });
						 });
						 jQuery( '.qtip' ).each( function() {
							jQuery( this ).removeClass( 'qtip-default' );
						 });
					});
				</script>
				
				<div id="pre-defined-tag-container">
					<input type="hidden" value="" class="clicked-input">
					<div id="pre-defined-tag-interior-container">
						<h3><?php _e( 'Pre Defined Tags' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
						<p class="description"><?php _e( 'You can use any of the following tags to populate a MailChimp text field with dynamic content. This can be used to determine which page the user signed up on, if the user was logged in and more.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p> 
						<ul>
							<?php foreach( apply_filters( 'yikes-mailchimp-custom-default-value-tags' , $available_tags ) as $tag ) { ?>
								<li class="tooltop-tag">
									<!-- link/tag -->
									<a href="#" onclick="populateDefaultValue( '<?php echo $tag['tag']; ?>' );return false;" data-attr-tag="<?php echo $tag['tag']; ?>" title="<?php echo $tag['title']; ?>"><?php echo $tag['title']; ?></a>
									<!-- help icon -->
									<span class="dashicons dashicons-editor-help"></span>
									<!-- tooltip -->
									<div class="tooltiptext qtip-bootstrap yikes-easy-mc-hidden"><?php echo $tag['description']; ?></div>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>				
				<?php	
		}
		
		/*
		*	build_available_merge_vars( $list_id )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function build_available_merge_vars( $form_fields , $available_merge_variables ) {
			$fields_assigned_to_form = array();
			if( !empty( $form_fields ) ) {
				foreach( $form_fields as $assigned_field ) {
					// print_r( $assigned_field) ;
					// switch between merge variables and interest groups
					if( isset( $assigned_field['merge'] ) ) {
						$fields_assigned_to_form[] = $assigned_field['merge'];
					}
				}
			}
			if( !empty( $available_merge_variables['data'][0] ) ) {	
				?><ul id="available-fields"><?php
				foreach( $available_merge_variables['data'][0]['merge_vars'] as $merge_var ) {
					if( in_array( $merge_var['tag'] , $fields_assigned_to_form ) ) {
						?>
							<li class="available-form-field not-available" alt="<?php echo $merge_var['tag']; ?>" data-attr-field-type="<?php echo $merge_var['field_type']; ?>" data-attr-field-name="<?php echo $merge_var['name']; ?>" data-attr-form-id="<?php echo $available_merge_variables['data'][0]['id']; ?>" title="<?php _e( 'Already assigned to your form' , 'yikes-inc-easy-mailchimp-extender' ); ?>" disabled="disabled"><?php echo stripslashes( $merge_var['name'] ); if( $merge_var['req'] == '1' ) { echo ' <span class="field-required" title="' . __( 'required field' , 'yikes-inc-easy-mailchimp-extender' ) . '">*</span>'; } ?> <small class="field-type-text"><?php echo $merge_var['field_type']; ?></small></li>
						<?php
					} else {
						?>
							<li class="available-form-field" alt="<?php echo $merge_var['tag']; ?>" data-attr-field-type="<?php echo $merge_var['field_type']; ?>" data-attr-field-name="<?php echo $merge_var['name']; ?>" data-attr-form-id="<?php echo $available_merge_variables['data'][0]['id']; ?>"><?php echo stripslashes( $merge_var['name'] ); if( $merge_var['req'] == '1' ) { echo ' <span class="field-required" title="' . __( 'required field' , 'yikes-inc-easy-mailchimp-extender' ) . '">*</span>'; } ?> <small class="field-type-text"><?php echo $merge_var['field_type']; ?></small></li>
						<?php
					}
				}
				?></ul>
				<a href="#" class="add-field-to-editor button-secondary yikes-easy-mc-hidden" style="display:none;"><small><span class="dashicons dashicons-arrow-left-alt add-to-form-builder-arrow"></span> <?php _e( 'Add to Form Builder' , 'yikes-inc-easy-mailchimp-extender' ); ?></small></a>
				<?php
			}
		}
		
		/*
		*	build_available_interest_groups( $form_fields , $available_interest_groups )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function build_available_interest_groups( $form_fields , $available_interest_groups , $list_id ) {
			$fields_assigned_to_form = array();
			if( !empty( $form_fields ) ) {
					foreach( $form_fields as $assigned_interest_group ) {
					if( isset( $assigned_interest_group['group_id'] ) ) {
						$fields_assigned_to_form[] = $assigned_interest_group['group_id'];
					}
				}
			}
			if( !empty( $available_interest_groups) ) {	
				?><ul id="available-interest-groups"><?php
				foreach( $available_interest_groups as $interest_group ) {
					if( in_array( $interest_group['id'] , $fields_assigned_to_form ) ) {
						?>
							<li class="available-interest-group not-available" alt="<?php echo $interest_group['id']; ?>" data-attr-field-name="<?php echo stripslashes( $interest_group['name'] ); ?>" data-attr-field-type="<?php echo $interest_group['form_field']; ?>" data-attr-form-id="<?php echo $list_id; ?>" title="<?php _e( 'Already assigned to your form' , 'yikes-inc-easy-mailchimp-extender' ); ?>" disabled="disabled"><?php echo stripslashes( $interest_group['name'] ); ?> <small class="field-type-text"><?php echo $interest_group['form_field']; ?></small></li>
						<?php
					} else {
						?>
							<li class="available-interest-group" alt="<?php echo $interest_group['id']; ?>" data-attr-field-name="<?php echo stripslashes( $interest_group['name'] ); ?>" data-attr-field-type="<?php echo $interest_group['form_field']; ?>" data-attr-form-id="<?php echo $list_id; ?>"><?php echo stripslashes( $interest_group['name'] ); ?> <small class="field-type-text"><?php echo $interest_group['form_field']; ?></small></li>
						<?php
					}
				}
				?></ul>
				<a href="#" class="add-interest-group-to-editor button-secondary yikes-easy-mc-hidden" style="display:none;"><small><span class="dashicons dashicons-arrow-left-alt add-to-form-builder-arrow"></span> <?php _e( 'Add to Form Builder' , 'yikes-inc-easy-mailchimp-extender' ); ?></small></a>
				<?php
			}
		}
		
		/* 
		*	Create A New Form!
		*	Probably Move these to its own file, 
		*	and include it here for easy maintenance 
		*	- must clean up db tables , ensure what data is going in and what is needed...
		*/
		public function yikes_easy_mailchimp_create_form() {
			$nonce = $_REQUEST['nonce'];
			if( ! wp_verify_nonce( $nonce, 'create_mailchimp_form' ) ) {
				die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) );
			}
			global $wpdb;
				/* Default values */
				// setup our default submission settings serialized array
				$submission_settings = json_encode(
					array(
						'ajax' => 1,
						'redirect_on_submission' => 0,
						'redirect_page' => 1,
						'hide_form_post_signup' => 0
					)
				);
				// setup our default optin settings serialized array
				$optin_settings = json_encode(
					array(
						'optin' => 1,
						'update_existing_user' => 1,
						'send_welcome_email' => 1,
					)
				);
				// setup our default error message array
				$error_settings= json_encode(
					array(
						'success' => '',
						'general-error' => '',
						'invalid-email' => '',
						'email-already-subscribed' => ''
					)
				);
				/* End default values */
				/* Working Insert Function */
				$wpdb->insert(
					$wpdb->prefix . 'yikes_easy_mc_forms',
					array(
						'list_id' => $_POST['associated-list'],
						'form_name' => stripslashes( $_POST['form-name'] ),
						'form_description' => stripslashes( $_POST['form-description'] ),
						'fields' => '',
						'custom_styles' => 0,
						'custom_template' => 0,
						'send_welcome_email' => 1,
						'redirect_user_on_submit' => 0,
						'redirect_page' => '',
						'submission_settings' => $submission_settings,
						'optin_settings' => $optin_settings,
						'error_messages' => $error_settings,
						'custom_notifications' => '',
						'impressions' => 0,
						'submissions' => 0,
						'custom_fields' => '',
					),
					array(
						'%s', // list id
						'%s', // form name
						'%s', // form description
						'%s', // fields
						'%s', // custom styles
						'%d',	// custom template
						'%d',	// send welcome email
						'%s',	// redirect user
						'%s',	// redirect page
						'%s',	// submission
						'%s',	// optin
						'%s', // error
						'%s', // custom notifications
						'%d',	// impressions #
						'%d',	// submissions #
						'%s', // custom fields
					)
				);
				
			// if an error occurs during the form creation process
			if( $wpdb->insert_id == '0' ) {
				// write it to the error log
				// if the form was not created successfully
				if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $wpdb->last_error , __( "Creating a new form" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Forms" , 'yikes-inc-easy-mailchimp-extender' ) );
				}
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&sql_error='.urlencode( $wpdb->last_error ) ) ) );
			} else {
				// redirect the user to the new form edit page
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id='.$wpdb->insert_id ) ) );
			}
			exit();
			die();
		}
		
		/* 
		*	Delete A Form !
		*	Probably Move these to its own file, 
		*	and include it here for easy maintenance 
		*	- must clean up db tables , ensure what data is going in and what is needed...
		*/
		public function yikes_easy_mailchimp_delete_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$post_id_to_delete = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'delete-mailchimp-form-'.$post_id_to_delete ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			global $wpdb;
				/* Working Insert Function */
				$wpdb->delete(
					$wpdb->prefix . 'yikes_easy_mc_forms',
					array(
						'id' => $post_id_to_delete
					),
					array(
						'%d',
					)
				);
			// redirect the user to the manage forms page, display confirmation
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&deleted-form=true' ) ) );
			exit();
			die();
		}
		
		/* 
		*	Duplicate an entire form !
		*	Probably Move these to its own file, 
		*/
		public function yikes_easy_mailchimp_duplicate_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$post_id_to_clone = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'duplicate-mailchimp-form-'.$post_id_to_clone ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			global $wpdb;
				/* Working Insert Function */
				$form_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "yikes_easy_mc_forms WHERE id = ".$post_id_to_clone."");
				// create empty array to populate with row data
				$data = array();
				// build a data array to duplicate
				foreach( $form_data as $id => $value ) {
					// skip the ID field this is unique
					if( $id != 'id' ) {	
						// append -Copy- to our new form
						if( $id == 'form_name' ) {
							$value = $value . ' - Copy -';
						}
						// reset the impressions and submissions back to 0
						if( $id == 'impressions' || $id == 'submissions' ) {
							$value = '0';
						}
						
						// add data to our array
						$data[$id] = $value;
					}
				}
				// insert our new data
				if( $wpdb->insert(
					$wpdb->prefix . 'yikes_easy_mc_forms',
					apply_filters( 'yikes-mailchimp-duplicate-form-data', $data )
				)  === FALSE ) {
					// redirect the user to the manage forms page, display error
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&duplicated-form=false' ) ) );
				} else {
					// redirect the user to the manage forms page, display confirmation
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&duplicated-form=true' ) ) );
				}
				exit();
				die();
		}
		
		/* 
		*	Reset a forms impression stats
		*/
		public function yikes_easy_mailchimp_reset_impression_stats() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$form_id_to_reset = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'reset-stats-mailchimp-form-'.$form_id_to_reset ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			global $wpdb;
			/* Update 'Impressions/Submissions' */
			if( $wpdb->update( 
				$wpdb->prefix . 'yikes_easy_mc_forms',
				array( 
					'impressions' => 0,
					'submissions' => 0
				),
				array( 'ID' => $form_id_to_reset )
			) === FALSE ) {
				// redirect the user to the manage forms page, display error
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&reset-stats=false' ) ) );
			} else {
				// redirect the user to the manage forms page, display confirmation
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&reset-stats=true' ) ) );
			}
			exit();
			die();
		}
		
		/* 
		*	Update an entire form !
		*	Probably Move these to its own file, 
		*/
		public function yikes_easy_mailchimp_update_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$form_id = $_REQUEST['id'];
			
			// store our values!
			$list_id = $_POST['associated-list'];
			$form_name = stripslashes( $_POST['form-name'] );
			$form_description = sanitize_text_field( stripslashes( $_POST['form-description'] ) );
			$send_welcome_email = $_POST['send-welcome-email'];
			$redirect_user_on_submit = $_POST['redirect-user-on-submission'];
			$redirect_page = $_POST['redirect-user-to-selection'];
			if( isset( $_POST['custom-styles'] ) ) {
				$custom_styles = $_POST['custom-styles'];
			}
			$assigned_fields = isset( $_POST['field'] ) ? json_encode( $_POST['field'] ) : '';
			
			// setup our custom styles serialized array
			if( isset( $custom_styles ) ) {
				$custom_styles = json_encode( array(
					'active' => $_POST['custom-styles'],
					'background_color' => $_POST['form-background-color'],
					'font_color' => $_POST['form-font-color'],
					'submit_button_color' => $_POST['form-submit-button-color'],
					'submit_button_text_color' => $_POST['form-submit-button-text-color'],
					'form_padding' => $_POST['form-padding'],
					'form_width' => $_POST['form-width'],
					'form_alignment' => $_POST['form-alignment'],
					'label_visible' => $_POST['label-visible']
				) );
			} else {
				$custom_styles = 0;
			}
			
			// setup our submission settings serialized array
			$submission_settings = json_encode(
				array(
					'ajax' => $_POST['form-ajax-submission'],
					'redirect_on_submission' => $_POST['redirect-user-on-submission'],
					'redirect_page' => $_POST['redirect-user-to-selection'],
					'hide_form_post_signup' => $_POST['hide-form-post-signup'],
					'replace_interests' => $_POST['replace-interest-groups'],
				)
			);
			
			// setup our optin settings serialized array
			$optin_settings = json_encode(
				array(
					'optin' => $_POST['single-double-optin'],
					'update_existing_user' => $_POST['update-existing-user'],
					'send_welcome_email' => $_POST['send-welcome-email'],
				)
			);
			
			// setup our error settings serialized array
			$error_settings = json_encode(
				array(
					'success' => trim( $_POST['yikes-easy-mc-success-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-success-message'] ) ) : '',
					'general-error' => trim( $_POST['yikes-easy-mc-general-error-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-general-error-message'] ) ) : '',
					'invalid-email' => trim( $_POST['yikes-easy-mc-invalid-email-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-invalid-email-message'] ) ) : '',
					'already-subscribed' => trim( $_POST['yikes-easy-mc-user-subscribed-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-subscribed-message'] ) ) : '',
				)
			);
			
			// setup and store our notification array
			$custom_notifications = isset( $_POST['custom-notification'] ) ? stripslashes( json_encode( $_POST['custom-notification'] ) ) : '';
			
			// additional custom fields (extensions / user defined fields)
			if( isset( $_POST['custom-field'] ) ) {
				$custom_field_array = array();
				foreach( $_POST['custom-field'] as $custom_field => $custom_value ) {
					if( is_array( $custom_value ) ) { 
						$custom_field_array[$custom_field] = array_filter( stripslashes_deep( $custom_value ) ); // array_filters to remove empty items (don't save them!)
					} else {	
						$custom_field_array[$custom_field] = stripslashes( $custom_value );
					}
				}
				$custom_fields = json_encode( $custom_field_array );
			} else {
				$custom_fields = '';
			}
			
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'update-mailchimp-form-'.$form_id ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			
				global $wpdb;
				/* Working Insert Function */
				// $form_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "yikes_easy_mc_forms WHERE id = ".$form_id."");
				$wpdb->update( 
					$wpdb->prefix . 'yikes_easy_mc_forms',
					array( 
						'list_id' => $list_id,
						'form_name' => $form_name,
						'form_description' => $form_description,
						'fields' => $assigned_fields,
						'custom_styles' => $custom_styles,
						'custom_template' => 0,
						'send_welcome_email' => $send_welcome_email,
						'redirect_user_on_submit' => $redirect_user_on_submit,
						'redirect_page' => $redirect_page,
						'submission_settings' => $submission_settings,
						'optin_settings' => $optin_settings,
						'error_messages' => $error_settings,
						'custom_notifications' => $custom_notifications,
						'custom_fields' => $custom_fields,
					),
					array( 'ID' => $form_id ), 
					array(
						'%s', // list id
						'%s', // form name
						'%s', // form description
						'%s', // fields
						'%s', // custom styles
						'%d',	//custom template
						'%d',	// send welcome email
						'%s',	// redirect user
						'%s',	// redirect page
						'%s',	// submission
						'%s',	// optin
						'%s', // error
						'%s', // custom notifications
						'%s', // custom fields
					), 
					array( '%d' ) 
				);
			
			/* Custom action hook which allows users to update specific options when a form is updated - used in add ons */
			do_action( 'yikes-mailchimp-save-form', $form_id,  json_decode( $custom_fields, true ) );
			
			// redirect the user to the manage forms page, display confirmation
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $form_id . '&updated-form=true' ) ) );
			exit();
			die();
		}
		
		/* Unsubscribe a given user from our list */
		public function yikes_easy_mailchimp_unsubscribe_user() {
			$nonce = $_REQUEST['nonce'];
			$list_id = $_REQUEST['mailchimp-list'];
			$email_id = $_REQUEST['email_id'];
			// verify our nonce
			if( !wp_verify_nonce( $nonce, 'unsubscribe-user-' . $email_id ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// only re-run the API request if our API key has changed
			// initialize MailChimp Class
			try {
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// retreive our list data
				$unsubscribe_user = $MailChimp->call( 'lists/unsubscribe' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ), 'id' => $list_id, 'email' => array( 'leid' => $email_id ), 'send_goodbye' => false, 'send_notify' => false ) );
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list_id . '&user-unsubscribed=true' ) ) );
			} catch ( Exception $e ) {
				// an error was encountered.
				// advanced debug should return the exception
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list_id . '&user-unsubscribed=false' ) ) );
			}	
		}
		
		public function yikes_easy_mailchimp_create_missing_error_log() {
			// grab our nonnce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( !wp_verify_nonce( $nonce, 'create_error_log' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// setup the path to the error log
			$error_log = fopen( plugin_dir_path( __FILE__ ) . '../includes/error_log/yikes-easy-mailchimp-error-log.php' , 'w' );
			try {	
				// create the file
				fwrite( $error_log , '' );
				// close out
				fclose( $error_log );
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error_log_created=true' ) ) );
			} catch ( Exception $e ) {
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error_log_created=false&error_message='.urlencode( $e->getMessage() ) ) ) );
			}
		}
		
		/* 
		*	Clear Transient Data !
		*	Probably Move these to its own file, 
		*/
		public function yikes_easy_mailchimp_clear_transient_data() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'clear-mc-transient-data' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// delete all of the integration settings list data in the cache
			$list_ids = $this->get_mailchimp_list_ids_on_account();
			// confirm the list IDs was returned and is not empty
			if( isset( $list_ids ) && ! empty( $list_ids ) ) {
				foreach( $list_ids as $id ) {
					// loop over each interest group and delete the transient associated with it
					// this is created & stored on the integration list page
					// id = groupID_interest_group
					delete_transient( $id . '_interest_group' );
				}
			}
			// Delete list data transient
			delete_transient( 'yikes-easy-mailchimp-list-data' );
			// Delete list account data
			delete_transient( 'yikes-easy-mailchimp-account-data' );
			// Delete list account data
			delete_transient( 'yikes-easy-mailchimp-profile-data' );
			// redirect the user to the manage forms page, display confirmation
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=api-cache-settings&transient-cleared=true' ) ) );
			exit;
		}
				
		/**
		*	Return an array of MailChimp lists associated with this account
		*
		*	Used when deleting the sites MailChimp cache stored
		*	@since 6.0.2
		*	@return 	$list_id_array - array of list id's to loop over
		*/
		public function get_mailchimp_list_ids_on_account() {
			$api_key = trim( get_option( 'yikes-mc-api-key' , '' ) );
			if( ! $api_key ) {
				// if no api key is set/site is not connected, return an empty array
				return array();
			}
			try {
				$MailChimp = new MailChimp( $api_key );
				// retreive our list data
				$mailchimp_lists = $MailChimp->call( 'lists/list' , array( 'apikey' => $api_key ) );
				$mail_chimp_list_ids = array();
				if( $mailchimp_lists ) {
					foreach( $mailchimp_lists['data'] as $list ) {
						$mail_chimp_list_ids[] = $list['id'];
					}
					return $mail_chimp_list_ids;
				} else {
					return array();
				}
			} catch ( Exception $e ) {
				// log to our error log
				require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->yikes_easy_mailchimp_write_to_error_log( $e->getMessage() , __( "Retreiving List Data" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Delete MailChimp Site Cache" , 'yikes-inc-easy-mailchimp-extender' ) );
			}	
			return;
		}
		
		/*
		*	Include our main Helper class file
		*	@since 6.0
		*/
		public function yikes_mailchimp_load_helper_class() {
			// check to see if it's already loaded up
			if( !class_exists( 'Yikes_Inc_Easy_Mailchimp_Forms_Helper' ) ) {	
				// Include our main helper class file
				include_once( YIKES_MC_PATH . 'admin/partials/helpers/init.php' );
			}
		}
		
		/*
		*	Alter the color scheme based on the current user selection (this is done to help integrate the plugin into the dashboard more seamlessly)
		*
		*	@since 0.1
		*	@order 	requires that yikes-inc-easy-mailchimp-extender-admin.min.css be enqueued, so we can override the defaults (handle: yikes-inc-easy-mailchimp-extender-admin)
		* 	@retutrn print out custom styles to the admin header to alter the defualt blue color
		*/
		public function alter_yikes_easy_mc_color_scheme() {
			// get the current set color scheme for the logged in user
			$current_color_scheme = get_user_option( 'admin_color' );
			// switch over each color scheme, and set our variable
			switch( $current_color_scheme ) {
				default:
				case 'fresh': // default blue (defined by this plugin)
					$main_color = '#00a0d2';
					break;
				case 'light': // light grey
					$main_color = '#E5E5E5';
					break;
				case 'blue': // light blue
					$main_color = '#52ACCC';
					break;
				case 'coffee': // light brown-ish
					$main_color = '#59524C';
					break;	
				case 'ectoplasm': // purple
					$main_color = '#523F6D';
					break;
				case 'midnight': // black
					$main_color = '#363B3F';
					break;
				case 'ocean': // green/teal-ish
					$main_color = '#738E96';
					break;
				case 'sunrish': // red/orange
					$main_color = '#CF4944';
					break;	
			}
			ob_start();
			?>
				<style>
					.yikes-easy-mc-postbox h3,
					.column-columnname .form-id-container,
					.mv_ig_list .nav-tab-active { 
						background: <?php echo $main_color; ?>;
					}
					.mv_ig_list .arrow-down {
						border-top: 9pt solid <?php echo $main_color; ?>;
					}
				</style>
			<?php
			$override_admin_styles = ob_get_clean();
			// add our inline styles
			echo $override_admin_styles;
		}
		
		/*
		*	Process [yikes-mailchimp-form-description] into the shortcode
		*	@since 6.0.4.4
		*/
		public function process_subscriber_count_shortcode_in_form_descriptions( $form_description, $form_id ) {
			$form_description = str_replace( '[yikes-mailchimp-subscriber-count]', do_shortcode( '[yikes-mailchimp-subscriber-count form="' . $form_id . '"]' ), $form_description );
			return $form_description;
		}
				
		/*
		*	Generate the sidebar advertisment on the 'Edit Form' page
		*	@since 6.0.3
		*/
		public function generate_edit_forms_upsell_ad() {
			/*
			*	SimplePie strips out all query strings
			* 	we had to implement a workaround
			*	https://github.com/simplepie/simplepie/issues/317
			*/
			include_once( ABSPATH . WPINC . '/feed.php' );
			$rss = fetch_feed( esc_url( 'http://yikesplugins.com/feed/?post_type=product_ads&genre=easy-forms-for-mailchimp' ) );
			$maxitems = 0;
			if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly
				// Figure out how many total items there are, but limit it to 1. 
				$maxitems = $rss->get_item_quantity( 1 ); 
				// Build an array of all the items, starting with element 0 (first element).
				$rss_items = $rss->get_items( 0, $maxitems );
			} else {
				return $feed = new WP_Error( 'Simple Pie RSS Error', $feed->error() );
			}
			// loop over returned results
			foreach ( $rss_items as $add_on ) {
				$add_on_desc = $add_on->get_content();
				?>
					<h3><?php echo $add_on->get_title(); ?></h3>
					<div class="inside">
					<?php
						echo $add_on_desc;
					?>
					</div>
				<?php
			}
		}
		
}