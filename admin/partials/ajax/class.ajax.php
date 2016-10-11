<?php
	/*
	* 	Main Ajax handler
	*
	* 	Handles many of the ajax functionality on the admin side (ie. Adding new field to form, updating fields, grabbing list data etc.)
	*
	*	@since 6.0.0
	*	Author: Yikes Inc. | https://www.yikesinc.com
	*/
	class YIKES_Inc_Easy_MailChimp_Process_Ajax
	{

		public function __construct() {
			// ajax send merge variable to form builder
			add_action( 'wp_ajax_add_field_to_form', array( $this , 'send_field_to_form' ), 10 );
			// ajax send interest group to form builder
			add_action( 'wp_ajax_add_interest_group_to_form', array( $this , 'send_interest_group_to_form' ), 10 );
			// return new list data + activity (for dashboard widget )
			add_action( 'wp_ajax_get_new_list_data', array( $this , 'get_new_list_data' ), 10 );
			// return new list data + activity (for dashboard widget )
			add_action( 'wp_ajax_check_list_for_interest_groups', array( $this , 'check_list_for_interest_groups' ), 10 );
			// Add a new notification to a form
			add_action( 'wp_ajax_add_notification_to_form', array( $this , 'add_notification_to_form' ), 10 , 1 );
		}

		/*
		*	Assign a new notification to the form
		*	- return a single container
		*/
		public function add_notification_to_form() {
			if( $_POST['notification_name'] ) {
				include_once( YIKES_MC_PATH . 'admin/partials/ajax/add_notification_to_form.php' );
			}
			exit();
		}

		// Process our AJAX request,
		// when the user wants to switch which form data
		// is displayed on the dashboard
		public function get_new_list_data() {
			$list_id = $_POST['list_id'];
			$api_key = yikes_get_mc_api_key();
			$dash_position = strpos( $api_key, '-' );
			if( $dash_position !== false ) {
				$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/list.json';
			}
			$list_data = wp_remote_post( $api_endpoint, array(
				'body' => array(
					'apikey' => $api_key,
					'filters' => array( 'list_id' => $list_id )
				),
				'timeout' => 10,
				'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
			) );
			$list_data = json_decode( wp_remote_retrieve_body( $list_data ), true );
			if( isset( $list_data['error'] ) ) {
				if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $list_data['error'], __( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ) , __( "MailChimp Widget" , 'yikes-inc-easy-mailchimp-extender' ) );
				}
			}
			if( ! empty( $list_data['data'][0] ) ) {
				include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/stats-list-template.php' );
			}
			exit();
		}

		// Process our AJAX request,
		// when the user switches lists in the integration settings page
		// we want to return the interest groups associated with this list,
		// to allow users to pre-check anything they want to assign users appropriately
		/* note: this function is called statically from the integration settings page */
		public static function check_list_for_interest_groups( $list_id='', $integration_type='', $load=false ) {
			if( ! $list_id ) {
				$list_id = $_POST['list_id'];
			}
			if( ! $integration_type ) {
				$integration_type = $_POST['integration'];
			}
			$api_key = yikes_get_mc_api_key();
			// setup/check our transients
			if ( WP_DEBUG ||  false === ( $interest_groupings = get_transient( $list_id . '_interest_group' ) ) ) {
			  // It wasn't there, so regenerate the data and save the transient
				$dash_position = strpos( $api_key, '-' );
				if( $dash_position !== false ) {
					$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/interest-groupings.json';
				}
				$interest_groupings = wp_remote_post( $api_endpoint, array(
					'body' => array(
						'apikey' => $api_key,
						'id' => $list_id,
						'counts' => false
					),
					'timeout' => 10,
					'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
				) );
				$interest_groupings = json_decode( wp_remote_retrieve_body( $interest_groupings ), true );
				if( isset( $interest_groupings['error'] ) ) {
					if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
						require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
						$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
						$error_logging->yikes_easy_mailchimp_write_to_error_log( $interest_groupings['error'], __( "Get Interest Groups" , 'yikes-inc-easy-mailchimp-extender' ), "class.ajax.php" );
					}
				} else {
					// set the transient for 2 hours
					set_transient( $list_id . '_interest_group', $interest_groupings, 2 * HOUR_IN_SECONDS );
				}
			}
			if( isset( $interest_groupings ) && ! empty( $interest_groupings ) ) {
				require( YIKES_MC_PATH . 'admin/partials/menu/options-sections/templates/integration-interest-groups.php' );
			}
			// do not kill off execution on load, only on an ajax request
			if( ! $load ) {
				exit();
			}
		}

		// Process our Ajax Request
		// send a field to our form
		public function send_field_to_form() {
			$form_data_array = array(
				'field_name' => $_POST['field_name'],
				'merge_tag' => $_POST['merge_tag'],
				'field_type' => $_POST['field_type'],
				'list_id' => $_POST['list_id'],
			);
			include YIKES_MC_PATH . 'admin/partials/ajax/add_field_to_form.php';
			exit();
		}

		// send interest group to our form
		public function send_interest_group_to_form() {
			$form_data_array = array(
				'field_name' => $_POST['field_name'],
				'group_id' => $_POST['group_id'],
				'field_type' => $_POST['field_type'],
				'list_id' => $_POST['list_id'],
			);
			include YIKES_MC_PATH . 'admin/partials/ajax/add_interest_group_to_form.php';
			exit();
		}

		/*
		*	Search through multi dimensional array
		*	and return the index ( used to find the list name assigned to a form )
		*	- http://stackoverflow.com/questions/6661530/php-multi-dimensional-array-search
		*/
		public function findMCListIndex( $id, $array, $tag ) {
			if( $tag == 'tag' ) {
				foreach( $array as $key => $val ) {
					   if ( $val['tag'] === $id ) {
						   return $key;
					   }
				   }
			   return null;
			} else {
				foreach ( $array as $key => $val ) {
				   if ( $val['id'] == $id ) {
					   return $key;
				   }
			   }
			return null;
			}
	  	} // end

	} // end class

