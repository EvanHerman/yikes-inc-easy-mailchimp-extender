<?php 
/*
*	All Third Party Integrations Loaded Here
*	@since 6.0.3
*/
class YIKES_MailChimp_ThirdParty_Integrations {

	function __construct() {
		add_action( 'init', array( $this, 'load_third_party_integrations' ) );
	}
	
	/*
	*	Load any third party integrations
	*
	*/
	public function load_third_party_integrations() {
		// required..*
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		/* Visual Composer */
		if( is_plugin_active( 'js_composer/js_composer.php' ) ) {
			include_once( YIKES_MC_PATH . 'includes/third-party-integrations/visual-composer/visual-composer.php' );
		}
	}

}

new YIKES_MailChimp_ThirdParty_Integrations;

?>