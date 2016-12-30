<?php
	/* 
	*	Main Class file to handle bbPress Integrations
	*	used to add new users signing up via bbPress
	*	@since 6.0.0
	*/
	
	// Prevent direct access to the file
	defined('ABSPATH') or die( __( "Whoops, you shouldn't be accessing this file directly. Abort!" , 'yikes-inc-easy-mailchimp-extender' ) );
	
	class Yikes_Easy_MC_bbPress_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
	
		/**
		 * @var string
		 */
		protected $type = 'bbpress_forms';

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'bbp_theme_after_topic_form_subscriptions', array( $this, 'output_checkbox' ), 10 );
			add_action( 'bbp_theme_after_reply_form_subscription', array( $this, 'output_checkbox' ), 10 );
			add_action( 'bbp_theme_anonymous_form_extras_bottom', array( $this, 'output_checkbox' ), 10 );
			add_action( 'bbp_new_topic', array( $this, 'subscribe_from_bbpress_new_topic' ), 10, 4 );
			add_action( 'bbp_new_reply', array( $this, 'subscribe_from_bbpress_new_reply' ), 10, 5 );
		}

		/**
		* Outputs a checkbox
		*/
		public function output_checkbox() {
			// if the user is already subscribed, abort and don't render the checkbox
			if ( $this->is_user_already_subscribed( $this->type ) ) {
				return;
			}
			echo $this->yikes_get_checkbox();
		}
		
		/**
		 * @param array $anonymous_data
		 * @param int $user_id
		 * @param string $trigger
		 * @return boolean
		 */
		public function subscribe_from_bbpress( $anonymous_data, $user_id, $trigger ) {
			$user_data = get_userdata( $user_id );
			return $this->subscribe_user_integration( $user_data->user_email, $this->type , array(
				'FNAME' => $user_data->first_name,
				'LNAME' => $user_data->last_name,
				'NAE' => $user_data->first_name,
			) );
		}

		public function subscribe_from_bbpress_new_topic( $topic_id, $forum_id, $anonymous_data, $topic_author_id ) {
			return $this->subscribe_from_bbpress( $anonymous_data, $topic_author_id, 'bbpress_new_topic' );
		}

		public function subscribe_from_bbpress_new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author_id ) {
			return $this->subscribe_from_bbpress( $anonymous_data, $reply_author_id, 'bbpress_new_reply' );
		}
		
	}
	new Yikes_Easy_MC_bbPress_Checkbox_Class;
