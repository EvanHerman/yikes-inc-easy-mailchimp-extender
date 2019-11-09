<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Shortcode;

use YIKES\EasyForms\Asset;
use YIKES\EasyForms\AssetAware;
use YIKES\EasyForms\ScriptAsset;
use YIKES\EasyForms\StyleAsset;
use YIKES\EasyForms\Exception;
use YIKES\EasyForms\View\FormEscapedView;
use YIKES\EasyForms\View\NoOverrideLocationView;

/**
 * Class EasyFormsShortcode
 *
 * @since %VERSION%
 * @package YIKES\EasyForms
 */

final class EasyFormsShortcode extends BaseShortcode {

    const TAG = 'easy_forms_shortcode';
    const VIEW_URI = 'views/easy-forms-shortcode';

	/**
	 * The view URI to use.
	 *
	 * This property is used so that the view can be switched dynamically
	 * as needed.
	 *
	 * @since %VERSION%
	 * @var string
	 */
    private $view_uri = self::VIEW_URI;
    
    /**
	 * Register the Shortcode.
	 *
	 * @since %VERSION%
	 */
	public function register() {
		parent::register();
		add_action( 'easy_forms_do_shortcode', function( $atts ) {
			echo $this->process_shortcode( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput
		} );
    }
    
    /**
	 * Get the default array of attributes for the shortcode.
	 *
	 * @since %VERSION%
	 * @return array
	 */
	public function get_default_atts() {
		return [
			'form_id' => 0,
		];
    }
    
   /**
	 * Get the View URI to use for rendering the shortcode.
	 *
	 * @since %VERSION%
	 *
	 * @return string View URI.
	 */
	protected function get_view_uri() {
		return $this->view_uri;
    }
    
    /**
	 * Set the view URI.
	 *
	 * @since %VERSION%
	 *
	 * @param string $uri The URI to use.
	 */
	private function set_view_uri( $uri ) {
		$this->view_uri = $uri;
    }
    
    /**
	 * Render the current Renderable.
	 *
	 * @since %VERSION%
	 *
	 * @param array $context Context in which to render.
	 *
	 * @return string Rendered HTML.
	 */
	public function render( array $context = [] ) {
		try {
			$this->enqueue_assets();
			$view = new FormEscapedView( new NoOverrideLocationView( $this->get_view_uri() ) );
			return $view->render( $context );
		} catch ( \Exception $e ) {
			return $this->exception_to_string( $e );
		}
    }
    
    /**
	 * Convert an exception to a string.
	 *
	 * @since %VERSION%
	 *
	 * @param \Exception $e The exception object.
	 *
	 * @return string
	 */
	private function exception_to_string( \Exception $e ) {
		return sprintf(
			/* translators: %s refers to the error message */
			esc_html__( 'There was an error displaying the form: %s', 'easy-forms-text-domain' ),
			$e->getMessage()
		);
	}
}
