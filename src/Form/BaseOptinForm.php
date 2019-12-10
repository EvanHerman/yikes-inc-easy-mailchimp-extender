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
use YIKES\EasyForms\Service;
use YIKES\EasyForms\View\FormEscapedView;
use YIKES\EasyForms\View\TemplatedView;

/**
 * Abstract Class BaseForm
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
abstract class BaseForm implements Service, AssetsAware {

	use AssetsAwareness;

	/**
	 * Register the current Registerable.
	 *
	 * @since %VERSION%
	 */
	public function register() {
		$this->register_assets();
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
