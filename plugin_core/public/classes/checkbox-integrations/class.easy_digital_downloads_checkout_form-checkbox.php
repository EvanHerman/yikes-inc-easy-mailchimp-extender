<?php
	/*
	* Takes care of all the sign-up checkboxes
	*
	*/
	class Yikes_Easy_MC_EDD_Checkbox_Class extends Yikes_Easy_MC_Checkbox_Integration_Class {
	
		/**
		 * @var string
		 */
		protected $type = 'edd_checkout';
		
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'edd_purchase_form_user_info', array( $this, 'output_checkbox' ) );
			add_action( 'edd_payment_meta', array( $this, 'save_checkbox_value' ) );
			add_action( 'edd_complete_purchase', array( $this, 'subscribe_from_edd_purchase'), 50 );
		}
		
		
		/**
		* Outputs a checkbox
		*/
		public function output_checkbox() {
			echo do_action( 'yikes-mailchimp-before-checkbox' , $this->type );
				echo $this->yikes_get_checkbox();
			echo do_action( 'yikes-mailchimp-after-checkbox' , $this->type );
		}	
		
		
		/**
		 * @param array $meta
		 *
		 * @return array
		 */
		public function save_checkbox_value( $meta ) {
			// don't save anything if the checkbox was not checked
			if( ! $this->was_checkbox_checked( $this->type ) ) {
				return $meta;
			}
			$meta['_yikes_easy_mc_optin'] = 1;
			return $meta;
		}
		/**
		 * @param int $payment_id The ID of the payment
		 *
		 * @return bool|string
		 */
		public function subscribe_from_edd_purchase( $payment_id ) {
			$meta = edd_get_payment_meta( $payment_id );
			if( ! is_array( $meta ) || ! isset( $meta['_yikes_easy_mc_optin'] ) || ! $meta['_yikes_easy_mc_optin'] ) {
				return false;
			}
			$email = (string) edd_get_payment_user_email( $payment_id );
			$merge_vars = array();
			// add first and last name to merge vars, if given
			$user_info = (array) edd_get_payment_meta_user_info( $payment_id );
			if( isset( $user_info['first_name'] ) && isset( $user_info['last_name'] ) ) {
				$merge_vars['NAME'] = $user_info['first_name'] . ' ' . $user_info['last_name'];
			}
			if( isset( $user_info['first_name'] ) ) {
				$merge_vars['FNAME'] = $user_info['first_name'];
			}
			if( isset( $user_info['last_name'] ) ) {
				$merge_vars['LNAME'] = $user_info['last_name'];
			}
			return $this->subscribe( $email, $merge_vars, $this->type, $payment_id );
			try {
				$this->subscribe_user_integration( sanitize_email( $email ) , $this->type , $merge_vars );
			} catch( Exception $e ) {
				return $e->getMessage();
			}
		}
	
	}
	new Yikes_Easy_MC_EDD_Checkbox_Class;

	
?>