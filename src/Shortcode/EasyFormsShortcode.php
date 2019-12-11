<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Shortcode;

use YIKES\EasyForms\Assets\StyleAsset;
use YIKES\EasyForms\Assets\ScriptAsset;
use YIKES\EasyForms\Exception\Exception;
use YIKES\EasyForms\View\FormEscapedView;
use YIKES\EasyForms\View\NoOverrideLocationView;
use YIKES\EasyForms\Form\OptinForm as EasyForm;
use YIKES\EasyForms\Model\Subscriber;
use YIKES\EasyForms\Model\SubscriberRepository;
use YIKES\EasyForms\Model\OptinForm as EasyFormsModel;
use YIKES\EasyForms\Model\Recaptcha as RecaptchaModel;

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
	const TITLE_URI     = 'views/easy-forms-shortcode-title';
	const DESC_URI      = 'views/easy-forms-shortcode-description';
	const CSS_URI       = 'assets/css/shortcode-style';
	
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
			'ajax'                       => '', // set a custom js callback function to run after the recaptcha has expired - default none
			'recaptcha'                  => '', // manually set googles recptcha state
			'recaptcha_lang'             => '', // manually set the recaptcha language in the shortcode - also available is the yikes-mailchimp-recaptcha-language filter
			'recaptcha_type'             => '', // manually set the recaptcha type - audio/image - default image
			'recaptcha_theme'            => '', // manually set the recaptcha theme - light/dark - default light
			'recaptcha_size'             => '', // set the recaptcha size - normal/compact - default normal
			'recaptcha_data_callback'    => '', // set a custom js callback function to run after a successful recaptcha response - default none
			'recaptcha_expired_callback' => '', // set a custom js callback function to run after the recaptcha has expired - default none
			'inline'                     => '',
		];
	}

	public function register() {
		parent::register();
		$this->enqueue_assets();
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
		$form_id   = $attr['form'] ? $attr['form'] : '1';
		$form_data = ( new EasyFormsModel() )->find( $form_id );

		$this->is_submitted = $this->is_submitting_form();

		// Set up the form object.
		$form = $this->get_optin_form( $form_id, $form_data, $attr );

		$title = $form->form_title( $attr['title'], $attr['custom_title'], $form_data['form_name'] );

		if ( false !== $title ) {
			add_action( 'easy_forms_do_form_title', function( $view ) {
				echo $view->render_partial( static::TITLE_URI ); // phpcs:ignore WordPress.Security.EscapeOutput
			} );
		}

		$description = $form->form_description( $attr['description'], $attr['custom_description'] );
		
		if ( false !== $description ) {
			add_action( 'easy_forms_do_form_description', function( $view ) {
				echo $view->render_partial( static::DESC_URI ); // phpcs:ignore WordPress.Security.EscapeOutput
			} );
		}

		return [
			'title'                 => $title,
			'description'           => $description,
			'form_classes'          => $form->form_classes( $this->is_submitted ),
			'edit_form_link'        => $form->edit_form_link(),
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
	private function get_optin_form( $form_id, $form_data, $attr ) {
		$form = new EasyForm( $form_id, $form_data, $attr );
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

	private function skip_style() {
		return defined( 'YIKES_MAILCHIMP_EXCLUDE_STYLES' );
	}

	public function load_assets() {
		$submission_helper = new ScriptAsset(
            'form-submission-helpers',
            'assets/js/dev/form-submission-helpers',
            [ 'jquery' ],
            '1.0.0',
            ScriptAsset::ENQUEUE_HEADER
        );

        $submission_helper->add_localization( 'form_submission_helpers', array(
			'ajax_url'           => esc_url( admin_url( 'admin-ajax.php' ) ),
			'preloader_url'      => apply_filters( 'yikes-mailchimp-preloader', esc_url_raw( admin_url( 'images/wpspin_light.gif' ) ) ),
			'countries_with_zip' => $this->countries_with_zip(),
			'page_data'          => $this->page_data(),
		) );

		$assets = $this->skip_style() === false ? [
			new StyleAsset( 'yikes-inc-easy-mailchimp-public-styles', static::CSS_URI ),
			$submission_helper,
		] : [
			$submission_helper,
		];

		$this->assets = $assets;
	}

	public function countries_with_zip() {
        return [
            'US' => 'US', 'GB' => 'GB', 'CA' => 'CA', 
            'IE' => 'IE', 'CN' => 'CN', 'IN' => 'IN', 
            'AU' => 'AU', 'BR' => 'BR', 'MX' => 'MX',
            'IT' => 'IT', 'NZ' => 'NZ', 'JP' => 'JP',
            'FR' => 'FR', 'GR' => 'GR', 'DE' => 'DE',
            'NL' => 'NL', 'PT' => 'PT', 'ES' => 'ES'
        ];
    }

    public function page_data() {
        global $post;
		$page_data = isset( $post->ID ) ? $post->ID : 0;
		return apply_filters( 'yikes-mailchimp-page-data', $page_data );
    }
}
