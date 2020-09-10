<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Recaptcha;

use YIKES\EasyForms\Assets\AssetsAware;
use YIKES\EasyForms\Assets\AssetsAwareness;
use YIKES\EasyForms\Assets\ScriptAsset;
use YIKES\EasyForms\Service;
use YIKES\EasyForms\View\View;
use YIKES\EasyForms\Model\Recaptcha as RecaptchaModel;

/**
 * Class Recaptcha
 *
 * @since %VERSION%
 */
final class Recaptcha implements Service, AssetsAware {

    use AssetsAwareness;

    const VIEW_URI        = 'views/recaptcha-box';
    const JS_HANDLE       = 'google-recaptcha-js';
    const JS_URI          = 'https://www.google.com/recaptcha/api.js';
    const JS_DEPENDENCIES = [ 'jquery', 'form-submission-helpers' ];
    const JS_VERSION      = '1.0.0';

    public function register() {
        $this->register_assets();

        add_action( 'easy_forms_do_recaptcha_box', function( $view ) {
            $this->enqueue_assets();
            echo $view->render_partial( static::VIEW_URI ); // phpcs:ignore WordPress.Security.EscapeOutput
        } );
    }

     /**
	 * Get the context to pass onto the view.
	 *
	 * Override to provide data to the view.
	 *
	 * @since %VERSION%
	 *
	 * @return array Context to pass onto view.
	 */
	protected function get_context() {
		return $this->recaptcha['recaptcha_options'];
    }
    
    private function get_script_params() {
        $recaptcha_options = ( new RecaptchaModel() )->setup();
        return $recaptcha_options['script_params'];
    }

    public function __get( $name ) {
		switch ( $name ) {
			case 'script_params':
				return $this->get_script_params();

			default:
				return null;
		}
    }

    /**
	 * Load asset objects for use.
	 *
	 * @since %SINCE%
	 */
	protected function load_assets() {  
		$this->assets= [
			new ScriptAsset(
                self::JS_HANDLE,
                self::JS_URI . $this->script_params,
                self::JS_DEPENDENCIES,
                self::JS_VERSION,
                ScriptAsset::ENQUEUE_HEADER,
                true
            ),
		];
    }
}