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

/**
 * Class FormEscapedView.
 *
 * This is a Decorator that decorates a given View with escaping meant for
 * HTML form output.
 *
 * @since   %VERSION%
 *
 * @package YIKES\EasyForms\View
 * @author  Freddie Mixell
 */
final class FormEscapedView implements View {

	/**
	 * Form tags that are allowed to be rendered.
	 *
	 * @var array
	 */
	protected $form_tags = [
		'form'     => [
			'id'     => true,
			'class'  => true,
			'action' => true,
			'method' => true,
		],
		'input'    => [
			'id'        => true,
			'class'     => true,
			'type'      => true,
			'name'      => true,
			'value'     => true,
			'required'  => true,
			'maxlength' => true,
		],
		'select'   => [
			'id'       => true,
			'class'    => true,
			'type'     => true,
			'name'     => true,
			'value'    => true,
			'required' => true,
		],
		'textarea' => [
			'id'       => true,
			'class'    => true,
			'type'     => true,
			'name'     => true,
			'value'    => true,
			'required' => true,
		],
		'option'   => [
			'id'       => true,
			'class'    => true,
			'type'     => true,
			'name'     => true,
			'value'    => true,
			'selected' => true,
		],
		'label'    => [
			'for' => true,
		],
		'fieldset' => [
			'data-add-new-label' => true,
		],
	];

	/**
	 * View instance to decorate.
	 *
	 * @since %VERSION%
	 *
	 * @var View
	 */
	private $view;

	/**
	 * Tags that are allowed to pass through the escaping function.
	 *
	 * @since %VERSION%
	 *
	 * @var array
	 */
	private $allowed_tags = [];

	/**
	 * Instantiate a FormEscapedView object.
	 *
	 * @since %VERSION%
	 *
	 * @param View       $view         View instance to decorate.
	 * @param array|null $allowed_tags Optional. Array of allowed tags to let
	 *                                 through escaping functions. Set to sane
	 *                                 defaults if none provided.
	 */
	public function __construct( View $view, $allowed_tags = null ) {
		$this->view         = $view;
		$this->allowed_tags = null === $allowed_tags
			? $this->prepare_allowed_tags( wp_kses_allowed_html( 'post' ) )
			: $allowed_tags;
	}

	/**
	 * Prepare an array of allowed tags by adding form elements to the existing
	 * array.
	 *
	 * This makes sure that the basic form elements always pass through the
	 * escaping functions.
	 *
	 * @since %VERSION%
	 *
	 * @param array $allowed_tags Allowed tags as fetched from the WordPress
	 *                            defaults.
	 *
	 * @return array Modified tags array.
	 */
	private function prepare_allowed_tags( $allowed_tags ) {
		return array_replace_recursive( $allowed_tags, $this->form_tags );
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
		return wp_kses( $this->view->render( $context ), $this->allowed_tags );
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
		return wp_kses(
			$this->view->render_partial( $uri, $context ),
			$this->allowed_tags
		);
	}
}
