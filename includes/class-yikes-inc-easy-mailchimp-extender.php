<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.yikesplugins.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 * @author     YIKES Inc. <plugins@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Extender {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Yikes_Inc_Easy_Mailchimp_Extender_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $yikes_inc_easy_mailchimp_extender    The string used to uniquely identify this plugin.
	 */
	protected $yikes_inc_easy_mailchimp_extender = 'yikes-inc-easy-mailchimp-extender';
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Our form interface instance.
	 *
	 * @var Yikes_Inc_Easy_Mailchimp_Extender_Form_Interface
	 */
	protected $form_interface;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @param Yikes_Inc_Easy_Mailchimp_Extender_Form_Interface $form_interface
	 */
	public function __construct( Yikes_Inc_Easy_Mailchimp_Extender_Form_Interface $form_interface ) {
		$this->version = YIKES_MC_VERSION;
		$this->form_interface = $form_interface;
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Yikes_Inc_Easy_Mailchimp_Extender_Loader. Orchestrates the hooks of the plugin.
	 * - Yikes_Inc_Easy_Mailchimp_Extender_i18n. Defines internationalization functionality.
	 * - Yikes_Inc_Easy_Mailchimp_Extender_Admin. Defines all hooks for the admin area.
	 * - Yikes_Inc_Easy_Mailchimp_Extender_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yikes-inc-easy-mailchimp-extender-loader.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yikes-inc-easy-mailchimp-extender-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-yikes-inc-easy-mailchimp-extender-public.php';
		/**
		 * The class responsible for orchestrating the actions and filters for the Gutenberg blocks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'blocks/maybe-do-blocks.php';
		$this->loader = new Yikes_Inc_Easy_Mailchimp_Extender_Loader();
	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Yikes_Inc_Easy_Mailchimp_Forms_Admin( $this->get_yikes_inc_easy_mailchimp_extender(), $this->get_version(), $this->form_interface );
		$plugin_admin->hooks();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'maybe_enqueue_assets' );
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Yikes_Inc_Easy_Mailchimp_Extender_Public( $this->get_yikes_inc_easy_mailchimp_extender(), $this->get_version() );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_yikes_inc_easy_mailchimp_extender() {
		return $this->yikes_inc_easy_mailchimp_extender;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Yikes_Inc_Easy_Mailchimp_Extender_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
