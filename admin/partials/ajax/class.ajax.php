<?php
	/*
	* 	Main Ajax handler
	*
	* 	Handles many of the ajax functionality on the admin side (ie. Adding new field to form, updating fields, grabbing list data etc.)
	*
	*	@since 6.0.0
	*	Author: Yikes Inc. | https://www.yikesinc.com
	*/
	class YIKES_Inc_Easy_Mailchimp_Process_Ajax {

		public function __construct() {

			// Ajax send merge variable to form builder.
			add_action( 'wp_ajax_add_field_to_form', array( $this , 'send_field_to_form' ), 10 );

			// Ajax send interest group to form builder.
			add_action( 'wp_ajax_add_interest_group_to_form', array( $this, 'send_interest_group_to_form' ), 10 );

			// Ajax add a tag to the form.
			add_action( 'wp_ajax_add_tag_to_form', array( $this, 'add_tags_to_form' ), 10 );

			// Ajax remove tag from form.
			add_action( 'wp_ajax_remove_tag_from_form', array( $this, 'remove_tag_from_form' ), 10 );

			// Return new list data + activity (for dashboard widget).
			add_action( 'wp_ajax_get_new_list_data', array( $this, 'get_new_list_data' ), 10 );

			// Return new list data + activity (for dashboard widget).
			add_action( 'wp_ajax_check_list_for_interest_groups', array( $this, 'check_list_for_interest_groups' ), 10 );

			// Add a new notification to a form.
			add_action( 'wp_ajax_add_notification_to_form', array( $this, 'add_notification_to_form' ), 10, 1 );

			// Save field label edits.
			add_action( 'wp_ajax_save_field_label_edits', array( $this, 'save_field_label_edits' ), 10, 1 );
		}

		/*
		*	Assign a new notification to the form
		*	- return a single container
		*/
		public function add_notification_to_form() {
			if ( isset( $_POST['notification_name'] ) ) {
				include_once YIKES_MC_PATH . 'admin/partials/ajax/add_notification_to_form.php';
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
					__( "Mailchimp Widget", 'yikes-inc-easy-mailchimp-extender' )
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
				$error_logging->maybe_write_to_log( 
					$interest_groupings->get_error_code(), 
					__( "Get Interest Groups", 'yikes-inc-easy-mailchimp-extender' ), 
					"class.ajax.php" 
				);
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

		/**
		 * Add a tag to the form.
		 */
		public function add_tags_to_form() {
			// Verify Nonce.
			if ( ! check_ajax_referer( 'add-tag', 'nonce', false ) ) {
				wp_send_json_error( '1' );
			}
			$tags    = isset( $_POST['tags'] ) ? wp_unslash( $_POST['tags'] ) : array();
			$list_id = isset( $_POST['list_id'] ) ? filter_var( wp_unslash( $_POST['list_id'] ), FILTER_SANITIZE_STRING ) : '';
			$form_id = isset( $_POST['form_id'] ) ? filter_var( wp_unslash( $_POST['form_id'] ), FILTER_SANITIZE_NUMBER_INT ) : 0;

			if ( empty( $tags ) || empty( $list_id ) || empty( $form_id ) ) {
				wp_send_json_error( '2' );
			}

			$form_interface = yikes_easy_mailchimp_extender_get_form_interface();
			$form           = $form_interface->get_form( $form_id );
			$form_tags      = array();

			// This data came from $_POST so sanitize it.
			foreach ( $tags as $tag ) {
				$form_tags[ filter_var( $tag['tag_id'], FILTER_SANITIZE_NUMBER_INT ) ] = array(
					'name' => filter_var( $tag['tag_name'], FILTER_SANITIZE_STRING ),
					'id'   => filter_var( $tag['tag_id'], FILTER_SANITIZE_NUMBER_INT ),
				);
			}

			$form['tags'] = $form_tags + ( isset( $form['tags'] ) ? $form['tags'] : array() );
			$form_interface->update_form( $form_id, $form );
			wp_send_json_success( array( 'tags' => $form_tags ) );
		}

		/**
		 * Remove a tag from a form.
		 */
		public function remove_tag_from_form() {
			// Verify Nonce.
			if ( ! check_ajax_referer( 'remove-tag', 'nonce', false ) ) {
				wp_send_json_error( '1' );
			}
			$tag     = isset( $_POST['tag'] ) ? filter_var( wp_unslash( $_POST['tag'] ), FILTER_SANITIZE_NUMBER_INT ) : array();
			$list_id = isset( $_POST['list_id'] ) ? filter_var( wp_unslash( $_POST['list_id'] ), FILTER_SANITIZE_STRING ) : '';
			$form_id = isset( $_POST['form_id'] ) ? filter_var( wp_unslash( $_POST['form_id'] ), FILTER_SANITIZE_NUMBER_INT ) : 0;

			if ( empty( $tag ) || empty( $list_id ) || empty( $form_id ) ) {
				wp_send_json_error( '2' );
			}

			$form_interface = yikes_easy_mailchimp_extender_get_form_interface();
			$form           = $form_interface->get_form( $form_id );
			if ( isset( $form['tags'] ) && isset( $form['tags'][ $tag ] ) ) {
				unset( $form['tags'][ $tag ] );
			}
			$form_interface->update_form( $form_id, $form );
			wp_send_json_success();			
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

		/**
		* Save changes to a field's label
		*/ 
		public function save_field_label_edits() {

			// Capture our $_POST variables
			$list_id	= isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
			$field_data = isset( $_POST['field_data'] ) ? $_POST['field_data'] : array();
			$field_name = isset( $field_data['field_name'] ) ? $field_data['field_name'] : '';
			$field_id	= isset( $field_data['field_id'] ) ? $field_data['field_id'] : '';

			// Make sure we have our required variables before continuing
			if ( $list_id === '' || $field_name === '' || $field_id === '' ) {
				wp_send_json_error( array(
						'message' => __( 'Could not update the field label: missing required field.', 'yikes-inc-easy-mailchimp-extender' ),
						'developer-info' => "One of the following variables was empty: list_id: $list_id, field_name: $field_name, field_id: $field_id."
					)
				);
			}

			// Update the field!
			$merge_field = yikes_get_mc_api_manager()->get_list_handler()->update_merge_field( $list_id, $field_id, array( 'name' => $field_name ), true );
			
			// Check for an error. If error, log it and return error
			if ( is_wp_error( $merge_field ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log( 
					$merge_field->get_error_code(), 
					__( "Updating merge field", 'yikes-inc-easy-mailchimp-extender' ), 
					"class.ajax.php"
				);
				wp_send_json_error( array(
						'message' => __( 'Could not update the field label: API request failed.', 'yikes-inc-easy-mailchimp-extender' ),
						'developer-info' => $error
					)
				);
			}

			wp_send_json_success();
		}
	}
