<?php

	/* Confirm NONCES work before continuing... */

	class YIKES_Inc_Easy_MailChimp_Process_Ajax
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
			add_action( 'wp_ajax_add_field_to_form', array( $this , 'send_field_to_form' ) , 10 );
		}
		
		// Process our Ajax Request
		public function send_field_to_form() {
			$form_data_array = array(
				'field_name' => $_POST['field_name'],
				'merge_tag' => $_POST['merge_tag'],
				'field_type' => $_POST['field_type'],
				'list_id' => $_POST['list_id'],
			);
			include YIKES_MC_PATH . 'admin/partials/ajax/process_ajax.php';
			exit();
			wp_die();
		}
	
		/*
		*	Search through multi dimensional array
		*	and return the index ( used to find the list name assigned to a form )
		*	- http://stackoverflow.com/questions/6661530/php-multi-dimensional-array-search
		*/
		public function findMCListIndex($id, $array) {
		   foreach ($array as $key => $val) {
			   if ($val['tag'] === $id) {
				   return $key;
			   }
		   }
		   return null;
		} // end
					
	} // end class
	
	new YIKES_Inc_Easy_MailChimp_Process_Ajax();
	
?>