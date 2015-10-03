<?php
	class YIKES_Inc_Easy_MailChimp_Public_Ajax
	{
	
		/**
		 * Thetext domain of this plugin
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    Used for internationalization
		 */
		private $text_domain = 'yikes-inc-easy-mailchimp-extender';
	
		public function __construct() {				
			// ajax process form submission
			add_action( 'wp_ajax_nopriv_process_form_submission', array( $this , 'process_form_submission' ) , 10 );
			add_action( 'wp_ajax_process_form_submission', array( $this , 'process_form_submission' ) , 10 );
			
			// increase submission count for a given form on successful submit
			add_action( 'wp_ajax_nopriv_increase_submission_count' , array( $this , 'increase_submission_count' ), 10 );	
			add_action( 'wp_ajax_increase_submission_count' , array( $this , 'increase_submission_count' ), 10 );	
		}
		
		/*
		*	Process form submisssions sent via ajax from the front end
		*	$form_data - serialized form data submitted
		*/
		public function process_form_submission() {
			// include our ajax processing file
			include_once( YIKES_MC_PATH . 'public/partials/shortcodes/process/process_form_submission_ajax.php' );
			exit();
			wp_die();
		}
		
		/*
		*	Increase the submission count for a given 
		*	$form_id - id of the form to increase submission count by 1
		*/
		public function increase_submission_count() {
			// store our posted form ID
			$form_id = $_POST['form_id'];
			global $wpdb;
			// query the form
			$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms WHERE id = ' . $form_id . '', ARRAY_A );
			$form_data = $form_results[0];
			// increase the submission
			$form_data['submissions']++;
			// update the value in the database
			$wpdb->update( 
				$wpdb->prefix . 'yikes_easy_mc_forms',
					array( 
						'submissions' => $form_data['submissions'],
					),
					array( 'ID' => $form_id ), 
					array(
						'%d',
					), 
					array( '%d' ) 
				);	
			exit();
			wp_die();
		}
							
	} // end class
	
	new YIKES_Inc_Easy_MailChimp_Public_Ajax();
	
?>