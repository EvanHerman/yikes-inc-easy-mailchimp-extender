<?php
	/* 
	*	Main Class file to handle Comment form Integrations
	*	used to add new users who leave a comment
	*	@since 6.0.0
	*/
	
	// Prevent direct access to the file
	defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );
	
	class Yikes_Easy_MC_Comment_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
	
		// declare our integration type
		protected $type = 'comment_form';
	
		public function __construct() {	
			// hooks for outputting the checkbox
			add_action( 'thesis_hook_after_comment_box', array( $this, 'output_checkbox' ), 10 );
			
			// hooks for checking if we should subscribe the commenter
			add_action( 'comment_post', array( $this, 'subscribe_from_comment' ), 40, 2 );

			add_action( 'init', array( $this, 'init_filters' ) );
		}

		/**
		* Allows us to apply_filters for the filters we're adding
		*/
		public function init_filters() {

			/**
			*	yikes-mailchimp-wp-comment-integration-placement
			*
			*	Decide the placement of the subscription checkbox. Default is after the "Comment" box.
			*
			*	@return string | The name of a WP comment field's filter
			*/
			$checkbox_placement = apply_filters( 'yikes-mailchimp-wp-comment-integration-placement', 'comment_form_field_comment' );

			add_action( $checkbox_placement, array( $this, 'output_checkbox' ), 10 );
		}
			
		
		/**
		* Outputs a checkbox, if user is not already subscribed
		*/
		public function output_checkbox( $comment_field ) {
			if ( $this->is_user_already_subscribed( $this->type ) ) {
				return $comment_field;
			}

			do_action( 'yikes-mailchimp-before-checkbox', $this->type );
			echo $comment_field . $this->yikes_get_checkbox();
			do_action( 'yikes-mailchimp-after-checkbox', $this->type );
		}
	
		/**
		 *	Hook to submit the data to MailChimp when 
		 *	a new comment is submitted
		 *
		 *	@since 6.0.0
		**/
		public function subscribe_from_comment( $comment_id, $comment_approved ) {	

			// was sign-up checkbox checked?
			if ( $this->was_checkbox_checked( $this->type ) === false ) {
				return false;
			}

			// is this a spam comment?
			if ( $comment_approved === 'spam' ) {
				return false;
			}

			// Fetch comment data
			$comment_data = get_comment( $comment_id );

			// Create merge variables based on comment data
			$merge_vars = array(
				'FNAME' => $comment_data->comment_author,
				'OPTIN_IP' => $comment_data->comment_author_IP,
			);

			// Subscribe the user 
			$this->subscribe_user_integration( sanitize_email( $comment_data->comment_author_email ) , $this->type , $merge_vars );
		}
		
	}
	new Yikes_Easy_MC_Comment_Checkbox_Class;
