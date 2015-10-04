<?php
/**
 * Options page for storing our reCAPTCHA options
 *
 * Page template that houses our reCAPTCHA API keys.
 *
 * @since 6.0.0
 *
 * @package WordPress
 * @subpackage Component
*/

?>

<h3><span><?php _e( 'reCAPTCHA Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>

<div class="inside">
	
	<p>
		<?php _e( 'reCAPTCHA is a free CAPTCHA service, from Google, that helps protect your site against spam, malicious registrations and other forms of attacks where computers try to disguise themselves as a human. reCAPTCHA will help prevent spammers and bots from submitting data through your MailChimp forms.' , 'yikes-inc-easy-mailchimp-extender' ); ?>
	</p>
	
	<p>
		<?php echo '<a href="https://www.google.com/recaptcha/admin" target="_blank" title="' . __( 'Get your reCAPTCHA API Key' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Get Your reCAPTCHA API Key' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>'; ?>
	</p>
	
	<!-- Settings Form -->
	<form action='options.php' method='post'>		
		
		<?php settings_fields( 'yikes_inc_easy_mc_recaptcha_settings_page' ); ?>
		
			<label for="yikes-mc-recaptcha-setting"><strong><?php _e( 'Enable reCAPTCHA Protection' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
				<input type="checkbox" name="yikes-mc-recaptcha-status" class="recaptcha-setting-checkbox" value="1" <?php checked( get_option( 'yikes-mc-recaptcha-status' , '' ) , '1' ); ?>>
			</label>
			
			<label for="yikes-mc-recaptcha-api-key"><strong><?php _e( 'reCAPTCHA Site Key' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
				<input type="text" class="widefat recaptcha-setting-checkbox" name="yikes-mc-recaptcha-site-key" value="<?php echo esc_attr( get_option( 'yikes-mc-recaptcha-site-key' , '' ) ); ?>">
			</label>
			
			<label for="yikes-mc-recaptcha-private-api-key"><strong><?php _e( 'reCAPTCHA Secret Key' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
				<input type="text" class="widefat recaptcha-setting-checkbox" name="yikes-mc-recaptcha-secret-key" id="" value="<?php echo esc_attr( get_option( 'yikes-mc-recaptcha-secret-key' , '' ) ); ?>">
			</label>
			
			<a href="#" onclick="jQuery(this).next().slideToggle();return false;" class="recaptcha-preview-link"><?php _e( 'View reCAPTCHA Preview' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
				<span class="recaptcha-preview-container">
					<img src="<?php echo YIKES_MC_URL . 'includes/images/recaptcha/recaptcha-demo.gif'; ?>" alt="<?php _e( 'reCAPTCHA Preview' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="recaptcha-demo-gif">
				</span>
			
			
		<?php submit_button(); ?>
									
	</form>
	
</div> <!-- .inside -->