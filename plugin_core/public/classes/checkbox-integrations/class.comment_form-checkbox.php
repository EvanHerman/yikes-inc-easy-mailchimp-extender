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
			add_action( 'comment_form_field_comment', array( $this, 'output_checkbox' ), 10 );
			// hooks for checking if we should subscribe the commenter
			add_action( 'comment_post', array( $this, 'subscribe_from_comment' ), 40, 2 );
			// setup the type
		}
			
		
		/**
		* Outputs a checkbox, if user is not already subscribed
		*/
		public function output_checkbox( $comment_field ) {
			if( $this->is_user_already_subscribed( $this->type ) == '1' ) {
				return $comment_field;
			}
				echo do_action( 'yikes-mailchimp-before-checkbox' , $this->type );
					echo $comment_field . $this->yikes_get_checkbox();
				echo do_action( 'yikes-mailchimp-after-checkbox' , $this->type );
		}		
	
		/**
		 *	Hook to submit the data to MailChimp when 
		 *	a new comment is submitted
		 *
		 *	@since 6.0.0
		**/
		public function subscribe_from_comment( $comment_id , $comment_approvided ) {	
			// was sign-up checkbox checked?
			if ( $this->was_checkbox_checked( $this->type ) === false ) {
				return false;
			}
			// is this a spam comment?
			if ( $comment_approved === 'spam' ) {
				return false;
			}
			// store comment data
			$comment_data = get_comment( $comment_id );
			// create merge variable array
			$merge_vars = array(
				'NAME' => $comment->comment_author,
				'OPTIN_IP' => $comment->comment_author_IP,
			);
			// subscribe the user 
			try{
				$this->subscribe_user_integration( sanitize_email( $comment_data->comment_author_email ) , $this->type , $merge_vars );
			} catch( Exception $e ) {
				return $e->getMessage();
			}
		}
		
	}
	new Yikes_Easy_MC_Comment_Checkbox_Class;
?>