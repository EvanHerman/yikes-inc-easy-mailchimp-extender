<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.yikesinc.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/public
 * @author     YIKES Inc. <info@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Extender_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $yikes_inc_easy_mailchimp_extender    The ID of this plugin.
	 */
	private $yikes_inc_easy_mailchimp_extender;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $yikes_inc_easy_mailchimp_extender, $version ) {

		$this->yikes_inc_easy_mailchimp_extender = $yikes_inc_easy_mailchimp_extender;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yikes_Inc_Easy_Mailchimp_Extender_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yikes_Inc_Easy_Mailchimp_Extender_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->yikes_inc_easy_mailchimp_extender, plugin_dir_url( __FILE__ ) . 'css/yikes-inc-easy-mailchimp-extender-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yikes_Inc_Easy_Mailchimp_Extender_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yikes_Inc_Easy_Mailchimp_Extender_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->yikes_inc_easy_mailchimp_extender, plugin_dir_url( __FILE__ ) . 'js/yikes-inc-easy-mailchimp-extender-public.js', array( 'jquery' ), $this->version, false );

	}

}
