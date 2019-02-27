<?php

/*
*	Custom class to allow Easy Forms for Mailchimp to extend visual composer
*	@since 6.0.3
*/

class YIKES_Mailchimp_Visual_Composer_Extension {

	/**
	 * Constructor
	 *
	 * @since 6.0.3
	 */
	function __construct() {
		
		add_action( 'admin_init', array( $this, 'extend_visual_composer' ) );

		if ( function_exists( 'vc_add_shortcode_param' ) ) {
			vc_add_shortcode_param( 'yikes_mailchimp_logo', array( $this, 'yikes_mailchimp_logo_vc_section' ) );
		}
	}

	/**
	 *    Extend Visual Composer with custom button
	 *
	 * @since 6.0.3
	 */
	public function extend_visual_composer() {

		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map( array(
			'name'        => __( 'Easy Forms for Mailchimp', 'yikes-inc-easy-mailchimp-extender' ),
			'base'        => 'yikes-mailchimp',
			'icon'        => YIKES_MC_URL . 'includes/images/Mailchimp_Assets/yikes-mailchimp-welcome-logo.png',
			'category'    => 'Content',
			'description' => __( 'Display a Mailchimp Opt-In form', 'yikes-inc-easy-mailchimp-extender' ),
			'params'      => array(
				array(
					'type'        => 'yikes_mailchimp_logo',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => '',
					'param_name'  => 'yikes_vc_logo',
					'value'       => '',
					'description' => '',
				),
				array(
					'type'        => 'dropdown',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Mailchimp Opt-In Form', 'yikes-inc-easy-mailchimp-extender' ),
					'param_name'  => 'form',
					'value'       => self::yikes_mailchimp_retreive_user_created_forms(),
					'description' => __( 'Select which form to display.', 'yikes-inc-easy-mailchimp-extender' ),
					'save_always' => true,
				),
				array(
					'type'        => 'checkbox',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Display Form Title', 'yikes-inc-easy-mailchimp-extender' ),
					'param_name'  => 'title',
					'value'       => array(
						__( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) => '1',
					),
					'description' => __( 'Should this form display the title.', 'yikes-inc-easy-mailchimp-extender' ),
				),
				array(
					'type'        => 'checkbox',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Display Form Description', 'yikes-inc-easy-mailchimp-extender' ),
					'param_name'  => 'description',
					'value'       => array(
						__( 'Yes', 'yikes-inc-easy-mailchimp-extender' ) => '1',
					),
					'description' => __( 'Should this form display the description.', 'yikes-inc-easy-mailchimp-extender' ),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Submit Button Text', 'yikes-inc-easy-mailchimp-extender' ),
					'param_name'  => 'submit',
					'value'       => __( 'Submit', 'yikes-inc-easy-mailchimp-extender' ),
					'description' => __( 'Enter a title to display', 'yikes-inc-easy-mailchimp-extender' ),
				),
			),
		) );
	}

	/**
	 * Custom Callback Section
	 *
	 * @since 6.0.3
	 */
	public function yikes_mailchimp_logo_vc_section() {
		return '<img style="width:250px;display:block;margin:0 auto;" src="' . YIKES_MC_URL . 'includes/images/Mailchimp_Assets/mailchimp-logo.png" title="' . __( 'Easy Forms for Mailchimp', 'yikes-inc-easy-mailchimp-extender' ) . '" />';
	}

	/**
	 *    Retreive a list of forms created by the user that they can select from in the dropdown
	 *
	 * @since 6.0.3
	 */
	public function yikes_mailchimp_retreive_user_created_forms() {
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$all_forms = $interface->get_all_forms();

		$lists = array();
		if ( ! empty( $all_forms ) ) {
			// build an array to pass to our javascript
			foreach ( $all_forms as $id => $form ) {
				$lists[ $form['form_name'] ] = $id;
			}
		} else {
			$lists[ __( 'Please Import Some Mailchimp Lists', 'yikes-inc-easy-mailchimp-extender' ) ] = '-';
		}

		return $lists;
	}
}
