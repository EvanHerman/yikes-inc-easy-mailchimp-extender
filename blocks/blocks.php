<?php

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

		// The 'block_categories' filter has been deprecated in WordPress 5.8 and replaced by 'block_categories_all'.
		if ( !class_exists( 'WP_Block_Editor_Context' ) ) {
			add_filter( 'block_categories', array( $this, 'easy_forms_register_category' ), 10, 2 );
		} else {
			add_filter( 'block_categories_all', array( $this, 'easy_forms_register_category' ), 10, 2 );
		}
	}

	/**
	 * Enqueue our scripts.
	 */
	abstract public function editor_scripts();

	/**
	 * Register our Easy Forms block callback.
	 */
	public function register_blocks() {
		register_block_type(
			static::BLOCK_NAMESPACE . static::BLOCK,
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Take the shortcode parameters from the Gutenberg block and render our shortcode.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 *
	 * @return string Block output.
	 */
	abstract public function render_block( $attributes, $content );

	public function easy_forms_register_category( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'easy-forms',
					'title' => __( 'Easy Forms', 'easy-forms' ),
					'icon'  => 'email-alt2',
				),
			)
		);
	}

}
