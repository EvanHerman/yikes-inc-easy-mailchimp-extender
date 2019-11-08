<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\View;

use YIKES\EasyForms\Exception\FailedToLoadView;
use YIKES\EasyForms\Exception\InvalidURI;
use YIKES\EasyForms\PluginHelper;

/**
 * Class BaseView.
 *
 * Very basic View class to abstract away PHP view rendering.
 *
 * Note: This should normally be done through a dedicated package.
 *
 * @since   %VERSION%
 *
 * @package Yikes\EasyForms
 * @author  Jeremy Pry
 */
class BaseView implements View {

	use PluginHelper;

	/**
	 * Extension to use for view files.
	 *
	 * @since %VERSION%
	 */
	const VIEW_EXTENSION = 'php';

	/**
	 * Contexts to use for escaping.
	 *
	 * @since %VERSION%
	 */
	const CONTEXT_HTML       = 'html';
	const CONTEXT_JAVASCRIPT = 'js';

	/**
	 * URI to the view file to render.
	 *
	 * @since %VERSION%
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * Internal storage for passed-in context.
	 *
	 * @since %VERSION%
	 *
	 * @var array
	 */
	protected $_context_ = [];

	/**
	 * Instantiate a View object.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri URI to the view file to render.
	 *
	 * @throws InvalidURI If an invalid URI was passed into the View.
	 */
	public function __construct( $uri ) {
		$this->uri = $this->validate( $uri );
	}

	/**
	 * Render a given URI.
	 *
	 * @since %VERSION%
	 *
	 * @param array $context Context in which to render.
	 *
	 * @return string Rendered HTML.
	 * @throws FailedToLoadView If the View URI could not be loaded.
	 */
	public function render( array $context = [] ) {

		// Add context to the current instance to make it available within the
		// rendered view.
		foreach ( $context as $key => $value ) {
			$this->$key = $value;
		}

		// Add entire context as array to the current instance to pass onto
		// partial views.
		$this->_context_ = $context;

		// Save current buffering level so we can backtrack in case of an error.
		// This is needed because the view itself might also add an unknown
		// number of output buffering levels.
		$buffer_level = ob_get_level();
		ob_start();

		try {
			include $this->uri;
		} catch ( \Exception $exception ) {
			// Remove whatever levels were added up until now.
			while ( ob_get_level() > $buffer_level ) {
				ob_end_clean();
			}
			throw FailedToLoadView::view_exception(
				$this->uri,
				$exception
			);
		}

		return ob_get_clean();
	}

	/**
	 * Render a partial view.
	 *
	 * This can be used from within a currently rendered view, to include
	 * nested partials.
	 *
	 * The passed-in context is optional, and will fall back to the parent's
	 * context if omitted.
	 *
	 * @since %VERSION%
	 *
	 * @param string     $uri     URI of the partial to render.
	 * @param array|null $context Context in which to render the partial.
	 *
	 * @return string Rendered HTML.
	 * @throws InvalidURI If the provided URI was not valid.
	 * @throws FailedToLoadView If the view could not be loaded.
	 */
	public function render_partial( $uri, array $context = null ) {
		$view = new static( $uri );

		return $view->render( $context ?: $this->_context_ );
	}

	/**
	 * Validate an URI.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri URI to validate.
	 *
	 * @return string Validated URI.
	 * @throws InvalidURI If an invalid URI was passed into the View.
	 */
	protected function validate( $uri ) {
		$uri = $this->check_extension( $uri, static::VIEW_EXTENSION );
		$uri = trailingslashit( $this->get_root_dir() ) . $uri;

		if ( ! is_readable( $uri ) ) {
			throw InvalidURI::from_uri( $uri );
		}

		return $uri;
	}

	/**
	 * Check that the URI has the correct extension.
	 *
	 * Optionally adds the extension if none was detected.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri       URI to check the extension of.
	 * @param string $extension Extension to use.
	 *
	 * @return string URI with correct extension.
	 */
	protected function check_extension( $uri, $extension ) {
		$detected_extension = pathinfo( $uri, PATHINFO_EXTENSION );

		if ( $extension !== $detected_extension ) {
			$uri .= '.' . $extension;
		}

		return $uri;
	}
}