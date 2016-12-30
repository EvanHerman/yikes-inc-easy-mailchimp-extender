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
			$list_id   = $_POST['list_id'];
			$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_list( $list_id );
			if ( is_wp_error( $list_data ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log(
					$list_data->get_error_code(),
					__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
					__( "MailChimp Widget", 'yikes-inc-easy-mailchimp-extender' )
				);
				exit();
			}

			include_once( YIKES_MC_PATH . 'admin/partials/dashboard-widgets/templates/stats-list-template.php' );
			exit();
		}

		// Process our AJAX request,
		// when the user switches lists in the integration settings page
		// we want to return the interest groups associated with this list,
		// to allow users to pre-check anything they want to assign users appropriately
		/* note: this function is called statically from the integration settings page */
		public static function check_list_for_interest_groups( $list_id = '', $integration_type = '', $load = false ) {
			if ( ! $list_id ) {
				$list_id = $_POST['list_id'];
			}
			if ( ! $integration_type ) {
				$integration_type = $_POST['integration'];
			}


			$interest_groupings = yikes_get_mc_api_manager()->get_list_handler()->get_interest_categories( $list_id );
			if ( is_wp_error( $interest_groupings ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log( $interest_groupings['error'], __( "Get Interest Groups" , 'yikes-inc-easy-mailchimp-extender' ), "class.ajax.php" );
				$interest_groupings = array();
			}

			if ( ! empty( $interest_groupings ) ) {
				require( YIKES_MC_PATH . 'admin/partials/menu/options-sections/templates/integration-interest-groups.php' );
			}
			// do not kill off execution on load, only on an ajax request
			if ( ! $load ) {
				exit();
			}
		}

		// Process our Ajax Request
		// send a field to our form
		public function send_field_to_form() {
			include YIKES_MC_PATH . 'admin/partials/ajax/add_field_to_form.php';
			exit();
		}

		// send interest group to our form
		public function send_interest_group_to_form() {
			include YIKES_MC_PATH . 'admin/partials/ajax/add_interest_group_to_form.php';
			exit();
		}

		/*
		*	Search through multi dimensional array
		*	and return the index ( used to find the list name assigned to a form )
		*	- http://stackoverflow.com/questions/6661530/php-multi-dimensional-array-search
		*/
		public function findMCListIndex( $id, $array, $tag ) {
			$mapping = array_flip( wp_list_pluck( $array, $tag ) );
			$index   = isset( $mapping[ $id ] ) ? $mapping[ $id ] : null;

			return $index;
		}
	}
