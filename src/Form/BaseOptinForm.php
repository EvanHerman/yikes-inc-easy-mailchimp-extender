<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Form;

use Closure;
use YIKES\EasyForms\Assets\AssetsAware;
use YIKES\EasyForms\Assets\AssetsAwareness;
use YIKES\EasyForms\Renderable;
use YIKES\EasyForms\Service;
use YIKES\EasyForms\View\FormEscapedView;
use YIKES\EasyForms\View\TemplatedView;

/**
 * Abstract Class BaseForm
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
abstract class BaseForm implements Renderable, Service, AssetsAware {

	use AssetsAwareness;

	/**
	 * Register the current Registerable.
	 *
	 * @since %VERSION%
	 */
	public function register() {
		$this->register_assets();
		$this->register_persistence_hooks();
	}

	/**
	 * Render the current Form.
	 *
	 * @since %VERSION%
	 *
	 * @param array $context Contextual arguments to pass to the view.
	 *
	 * @return string
	 */
	public function render( array $context = [] ) {
		try {
			$this->enqueue_assets();

			$view = new FormEscapedView( new TemplatedView( $this->get_view_uri() ) );
			return $view->render( $context );
		} catch ( \Exception $e ) {
			// Don't allow exceptions to bubble up. Render the exception message.
			return sprintf( '<pre>%s</pre>', $e->getMessage() );
		}
	}

	/**
	 * Register our hooks to use when saving data.
	 *
	 * @since %VERSION%
	 */
	protected function register_persistence_hooks() {
		$closure = $this->get_persistence_closure();
		add_action( 'save_post', $closure );
	}

	/**
	 * Get a closure that can verify and save the data submitted.
	 *
	 * @since %VERSION%
	 * @return Closure
	 */
	protected function get_persistence_closure() {
		return function ( $post_id ) {
			// Verify nonce and bail early if it doesn't verify.
			if ( ! $this->verify_nonce() ) {
				return $post_id;
			}

			// Bail early if this is an autosave.
			if ( wp_is_post_autosave( $post_id ) ) {
				return $post_id;
			}

			// Bail early if this is a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return $post_id;
			}

			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			// Check if there was a multisite switch before.
			if ( is_multisite() && ms_is_switched() ) {
				return $post_id;
			}

			$this->persist( $post_id );

			return $post_id;
		};
	}

	/**
	 * Verify the nonce and return the result.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	protected function verify_nonce() {
		$nonce_name = $this->get_nonce_name();

		if ( ! array_key_exists( $nonce_name, $_POST ) ) {
			return false;
		}

		$nonce = $_POST[ $nonce_name ];

		$result = wp_verify_nonce(
			$nonce,
			$this->get_nonce_action()
		);

		return false !== $result;
	}

	/**
	 * Get the name of the nonce.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_nonce_name() {
		return "{$this->get_id()}_nonce";
	}

	/**
	 * Get the ID for the nonce.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	abstract protected function get_id();

	/**
	 * Get the action for the nonce.
	 *
	 * @since %VERSION%
	 * @return string
	 */
	protected function get_nonce_action() {
		return "{$this->get_id()}_action";
	}

	/**
	 * Make sure the data is saved to the DB.
	 *
	 * @since %VERSION%
	 *
	 * @param int $post_id The post ID to save.
	 */
	abstract protected function persist( $post_id );

	/**
	 * Get the view URI to use when rendering the form.
	 *
	 * @since %VERSION%
	 * @return string The View URI.
	 */
	abstract protected function get_view_uri();
}
