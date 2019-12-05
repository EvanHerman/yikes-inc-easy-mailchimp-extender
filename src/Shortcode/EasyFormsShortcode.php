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
use YIKES\EasyForms\Form\OptinForm as EasyForm;
use YIKES\EasyForms\Model\Subscriber;
use YIKES\EasyForms\Model\SubscriberRepository;
use YIKES\EasyForms\Model\OptinForm as EasyFormsModel;
use YIkes\EasyForms\Model\OptinFormRepository;

/**
 * Class EasyFormsShortcode
 *
 * @since %VERSION%
 * @package YIKES\EasyForms
 */

final class EasyFormsShortcode extends BaseShortcode {

    const TAG           = 'yikes-mailchimp';
	const VIEW_URI      = 'views/easy-forms-shortcode';
	const SUBMITTED_URI = 'views/easy-forms-shortcode-completed';
	
	/**
	 * Whether a form has been submitted.
	 *
	 * @since %VERSION%
	 * @var bool
	 */
	private $is_submitted = false;

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
			'form'                       => '',
			'submit'                     => '',
			'title'                      => '',
			'custom_title'               => '',
			'description'                => '',
			'custom_description'         => '',
			'ajax'                       => '',
			'recaptcha'                  => '', // manually set googles recaptcha state
			'recaptcha_lang'             => '', // manually set the recaptcha language in the shortcode - also available is the yikes-mailchimp-recaptcha-language filter
			'recaptcha_type'             => '', // manually set the recaptcha type - audio/image - default image
			'recaptcha_theme'            => '', // manually set the recaptcha theme - light/dark - default light
			'recaptcha_size'             => '', // set the recaptcha size - normal/compact - default normal
			'recaptcha_data_callback'    => '', // set a custom js callback function to run after a successful recaptcha response - default none
			'recaptcha_expired_callback' => '', // set a custom js callback function to run after the recaptcha has expired - default none
			'inline'                     => '',
		];
	}

	/**
	 * Get the context to pass onto the view.
	 *
	 * Override to provide data to the view that is not part of the shortcode
	 * attributes.
	 *
	 * @since %VERSION%
	 *
	 * @param array $atts Array of shortcode attributes.
	 *
	 * @return array Context to pass onto view.
	 * @throws InvalidPostID When the post ID is not valid.
	 */
	protected function get_context( array $attr ) {
		$form_id = $attr['form'] ? $attr['form'] : '1';
		$form_data = ( new EasyFormsModel() )->find( $form_id );

		$this->is_submitted = $this->is_submitting_form();

		$form_options = [
			'recaptcha'                  => $attr['recaptcha'],
			'recaptcha_lang'             => $attr['recaptcha_lang'],
			'recaptcha_type'             => $attr['recaptcha_type'],
			'recaptcha_theme'            => $attr['recaptcha_theme'],
			'recaptcha_size'             => $attr['recaptcha_size'],
			'recaptcha_data_callback'    => $attr['recaptcha_data_callback'],
			'recaptcha_expired_callback' => $attr['recaptcha_expired_callback'],
		];

		// Set up the form object.
		$form = $this->get_optin_form( $form_id, $form_data, $form_options );

		return [
			'title'                 => $form->form_title( $attr['title'], $attr['custom_title'], $form_data['form_name'] ),
			'description'           => $form->form_description( $attr['description'], $attr['custom_description'] ),
			'form_classes'          => $form->form_classes( $this->is_submitted ),
			'edit_form_link'        => $form->edit_form_link(),
			'submit_button_props'   => $form->submit_button_props(),
			'submit_button_classes' => $form->submit_button_classes(),
			'submit_button_text'    => $form->submit_button_text( $attr['submit'] ),
			'ajax'                  => $attr['ajax'],
			'form_settings'         => $form_data['form_settings'],
			'form_data'             => $form_data,
			'form'                  => $form,
			'form_id'               => $form_id,
		];
	}

	/**
	 * Process the shortcode attributes and prepare rendering.
	 *
	 * @since %VERSION%
	 *
	 * @param array|string $atts Attributes as passed to the shortcode.
	 *
	 * @return string Rendered HTML of the shortcode.
	 */
	public function process_shortcode( $atts ) {
		try {
			// Determine if the form has been submitted.
			$this->is_submitted = $this->is_submitting_form();
			// Process the shortcode attributes.
			$atts    = $this->process_attributes( $atts );
			$context = $this->get_context( $atts );
			return $this->render( $context );
		} catch ( Exception $e ) {
			return $this->exception_to_string( $e );
		}
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
	 * Determine whether a form is currently being submitted.
	 *
	 * @since %VERSION%
	 * @return bool
	 */
	private function is_submitting_form() {
		return ! empty( $_POST );
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
	 * Get the form object.
	 *
	 * @since %VERSION%
	 *
	 * @param int              $form_id       The ID for the form.
	 * @param EasyFormsModel   $form_data  The form Object.
	 * @param array            $field_classes The classes for fields in the form.
	 *
	 * @return EasyForm
	 */
	private function get_optin_form( $form_id, $form_data, $form_options ) {
		$form = new EasyForm( $form_id, $form_data, $form_options );
		if ( $this->is_submitted ) {
			$this->handle_submission( $form );
		}
		return $form;
	}

	/**
	 * Handle the form submission.
	 *
	 * @since %VERSION%
	 *
	 * @param EasyForm $form The form object.
	 *
	 * @return Subscriber|null Returns a new Subscriber object, or null if one was not created.
	 * @throws InvalidURI When an invalid URI is set for the view.
	 */
	private function handle_submission( EasyForm $form ) {
		$form->set_submission( $_POST );
		$form->validate_submission();
		// Maybe update the view URI.
		if ( ! $form->has_errors() ) {
			$this->set_view_uri( self::SUBMITTED_URI );
			$subscriber = ( new SubscriberRepository() )->create_from_form( $form );
		}
		return isset( $subscriber ) ? $subscriber : null;
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
