<?php
add_action( 'init', 'yikes_maybe_activate_blocks' );

/**
 * If Gutenberg is active && PHP Version is >= 5.6, activate our blocks.
 *
 * We check if Gutenberg is active by looking for the existence of the `gutenberg_init()` function (Gutenberg plugin) or WordPress version >= 5.0.0.
 */
function yikes_maybe_activate_blocks() {
	include ABSPATH . WPINC . '/version.php';
	if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) && ( function_exists( 'gutenberg_init' ) || isset( $wp_version ) && version_compare( $wp_version, '5.0.0', '>=' ) ) ) {

		// Wrap the init of block functionality into a Try/Catch until everything is stable.
		try {
			require_once YIKES_MC_PATH . 'blocks/api/api.php';
			require_once YIKES_MC_PATH . 'blocks/easy-forms-block/easy-forms-block.php';
			$yikes_easy_form_block      = new YIKES_Easy_Form_Block();
			$yikes_easy_form_blocks_api = new YIKES_Easy_Forms_Blocks_API();
		} catch ( Exception $e ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log( $e->getMessage(),
				__( 'Error initializing the Easy Forms\' Gutenberg block functions.', 'yikes-inc-easy-mailchimp-extender' ),
				'blocks.php'
			);
		}
	}
}

/**
 * Class YIKES_Easy_Forms_Blocks.
 */
abstract class YIKES_Easy_Forms_Blocks {

	const BLOCK_NAMESPACE = 'yikes-inc-easy-forms/';

	/**
	 * Register our hooks.
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_scripts' ) );
		add_action( 'init', array( $this, 'register_blocks' ), 11 );
	}

	/**
	 * Enqueue our scripts.
	 */
	abstract public function editor_scripts();

	/**
	 * Register our Easy Forms block callback.
	 */
	public function register_blocks() {
		register_block_type( static::BLOCK_NAMESPACE . static::BLOCK, array(
			'render_callback' => array( $this, 'render_block' ),
		));
	}

	/**
	 * Take the shortcode parameters from the Gutenberg block and render our shortcode.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 * @return string Block output.
	 */
	abstract public function render_block( $attributes, $content );
}
