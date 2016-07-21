<?php
/**
 * Generaal Options Page Template
 * @since 1.0
 */

// Check if an API Constant is set
$yikes_mc_api_constant = ( defined( 'YIKES_MC_API_KEY' ) ) ? true : false;
?>

<h3><span><?php _e( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span><?php echo $api_connection; ?></h3>
<div class="inside">

	<!-- Settings Form -->
	<form action='options.php' method='post'>

		<?php settings_fields( 'yikes_inc_easy_mc_general_settings_page' ); ?>

		<!-- Start Option Inputs -->

		<!-- MailChimp API Input Field -->
		<label for="yks-mailchimp-api-key">

			<p><?php _e( 'Enter your API key in the field below to connect your site to your MailChimp account.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

			<p><strong><?php _e( 'MailChimp API Key' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>

			<input autocomplete="off" <?php if ( $yikes_mc_api_constant ) { echo 'readonly="readonly"'; } if( strlen( yikes_get_mc_api_key() ) > 0 ) { ?> type="password" <?php } else { ?> type="text" <?php } ?> value="<?php echo yikes_get_mc_api_key(); ?>" placeholder="<?php _e( 'MailChimp API Key' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="yikes-mc-api-key" id="yikes-mc-api-key" class="settings-page-input" />

			<?php if ( $yikes_mc_api_constant ) { ?>
				<p class="description"><?php printf( __( "Your MailChimp API key has been defined using the %s constant, in a PHP file.", "yikes-inc-easy-mailchimp-extender" ), '<code>YIKES_MC_API_KEY</code>' ); ?></p>
			<?php } ?>

			<?php echo $api_error_response; ?>

			<?php if ( ! $yikes_mc_api_constant ) { ?>
				<p class="description"><small><a href="https://admin.mailchimp.com/account/api" target="_blank" title="<?php _e( 'Get your API key here' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Get your API key here' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></p>
			<?php } ?>
		</label>

	<?php submit_button(); ?>

	</form>
</div> <!-- .inside -->
