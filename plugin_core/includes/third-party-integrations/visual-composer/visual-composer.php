<?php 
/*
*	Custom class to allow for YIKES MailChimp to extend visual composer
*	@since 6.0.3
*/
class YIKES_MailChimp_Visual_Composer_Extension {
	
	/**
	*	Constructor
	*	@since 6.0.3
	*/
	function __construct() {
		add_action( 'admin_init', array( $this, 'extend_visual_composer' ) );
		add_shortcode_param( 'yikes_mailchimp_logo', array( $this, 'yikes_mailchimp_logo_vc_section' ) );
	}
	
	/**
	*	Extend Visual Composer with custom button
	*	@since 6.0.3
	*/
	public function extend_visual_composer() {
		vc_map( array(
		   "name" => __( "Easy Forms for MailChimp", "yikes-inc-easy-mailchimp-extender" ),
		   "base" => "yikes-mailchimp",
   		   "icon" => YIKES_MC_URL . "includes/images/Welcome_Page/yikes-mailchimp-welcome-logo.png", // Simply pass url to your icon here
		   "category" => 'Content', // where is this buutton being displayed
		   "description" => __( "Display a MailChimp Opt-In form", "yikes-inc-easy-mailchimp-extender" ), // add a description
		   "params" => array(
				array(
					"type" => "yikes_mailchimp_logo",
					"holder" => "div",
					"class" => "",
					"heading" => __( "", "yikes-inc-easy-mailchimp-extender" ),
					"param_name" => "yikes_vc_logo",
					"value" => __( "", "yikes-inc-easy-mailchimp-extender" ),
					"description" => __( "", "yikes-inc-easy-mailchimp-extender" )
				),
				array(
				  "type" => "dropdown",
				  "holder" => "div",
				  "class" => "",
				  "heading" => __( "MailChimp Opt-In Form", "yikes-inc-easy-mailchimp-extender" ),
				  "param_name" => "form",
				  "value" => self::yikes_mailchimp_retreive_user_created_forms(),
				  "description" => __( "Select which form to display.", "yikes-inc-easy-mailchimp-extender" )
				),
				array(
				  "type" => "checkbox",
				  "holder" => "div",
				  "class" => "",
				  "heading" => __( "Display Form Title", "yikes-inc-easy-mailchimp-extender" ),
				  "param_name" => "title",
				  "value" => array(
					__( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) => '1',
				  ),
				  "description" => __( "Should this form display the title.", "yikes-inc-easy-mailchimp-extender" )
				),
				array(
				  "type" => "checkbox",
				  "holder" => "div",
				  "class" => "",
				  "heading" => __( "Display Form Description", "yikes-inc-easy-mailchimp-extender" ),
				  "param_name" => "description",
				  "value" => array(
					__( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) => '1',
				  ),
				  "description" => __( "Should this form display the description.", "yikes-inc-easy-mailchimp-extender" )
				),
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Submit Button Text", "yikes-inc-easy-mailchimp-extender" ),
					"param_name" => "submit",
					"value" => __( "Submit", "yikes-inc-easy-mailchimp-extender" ),
					"description" => __( "Enter a title to display", "yikes-inc-easy-mailchimp-extender" )
				)
		   )
		) );
	}
	
	/**
	*	Custom Callback Section
	*	@since 6.0.3
	*/
	public function yikes_mailchimp_logo_vc_section( $settings, $value ) {
		return '<img style="width:250px;display:block;margin:0 auto;" src="' . YIKES_MC_URL . 'includes/images/Welcome_Page/mailchimp-logo.png" title="' . __( 'Easy Forms for MailChimp by YIKES', 'yikes-inc-easy-mailchimp-extender' ) . '" />';
	}
	
	/**
	*	Retreive a list of forms created by the user that they can select from in the dropdown
	*	@since 6.0.3
	*/
	public function yikes_mailchimp_retreive_user_created_forms() {
		global $wpdb;
		$list_data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
		$lists = array();
		if( !empty( $list_data ) ) {	
			// build an array to pass to our javascript
			foreach( $list_data as $form ) {
				$lists[$form['form_name']] = $form['id'];
			}
		} else {
			$lists[__( 'Please Import Some MailChimp Lists' , 'yikes-inc-easy-mailchimp-extender' )] = '-';
		}
		return $lists;
	}
	
}
new YIKES_MailChimp_Visual_Composer_Extension;