<?php
/**
 * Options page for rendering checkboxes
 *
 * Page template that houses all of the checkbox settings.
 *
 * @since 6.0.0
 *
 * @package WordPress
 * @subpackage Component
*/
		
	// enqueue jquery qtip for our tooltip
	wp_enqueue_script( 'jquery-qtip-tooltip', YIKES_MC_URL . 'admin/js/min/jquery.qtip.min.js' , array( 'jquery' ) );
	wp_enqueue_style( 'jquery-qtip-style',  YIKES_MC_URL . 'admin/css/jquery.qtip.min.css' );
	
	?>
	<script>
		jQuery( document ).ready( function() {
		/* Initialize qtip tooltips */
			jQuery( '.dashicons-editor-help' ).each(function() {
				 jQuery(this).qtip({
					 content: {
						 text: jQuery(this).next('.tooltiptext'),
						 style: { 
							def: false
						 }
					 }
				 });
			 });
			 jQuery( '.qtip' ).each( function() {
				jQuery( this ).removeClass( 'qtip-default' );
			 });
		});
	</script>
	<?php
	
	// active plugins array
	// defaults: comments / registration
	$active_plugins = array(
		'comment_form' => __( 'WordPress Comment Form', 'yikes-inc-easy-mailchimp-extender' ),
		'registration_form' => __( 'WordPress Registration Form', 'yikes-inc-easy-mailchimp-extender' )
	);
	
	$class_descriptions = array(
		'comment_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/wordpress-banner-logo.png" title="' . __( 'WordPress' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WordPress core comment form opt-in checkbox will display a checkbox to your current users when leaving a comment (if they are not currently subscribed).' , 'yikes-inc-easy-mailchimp-extender' ),
		'registration_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/wordpress-banner-logo.png" title="' . __( 'WordPress' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WordPress core registration form opt-in checkbox will display a checkbox to new users when registering for your site.' , 'yikes-inc-easy-mailchimp-extender' ),
		'woocommerce_checkout_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/woocommerce-banner.png" title="' . __( 'WooCommerce Store' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WooCommerce checkout opt-in form allows you to capture email addresses from users who make purchases in your store. This option will add an opt-in checkbox to the checkout page.' , 'yikes-inc-easy-mailchimp-extender' ),
		'easy_digital_downloads_checkout_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/edd-banner.png" title="' . __( 'Easy Digital Downloads' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the Easy Digital Downloads checkout opt-in allows users who make a purchase to opt-in to your mailing list during checkout.' , 'yikes-inc-easy-mailchimp-extender' ),
		'buddypress_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/buddypress-banner.png" title="' . __( 'BuddyPress' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the BuddyPress opt-in allows users who register for your site to be automatically added to the mailing list of your choice.' , 'yikes-inc-easy-mailchimp-extender' ),
		'bbpress_forms' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/bbpress-banner.png" title="' . __( 'bbPress' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the bbPress opt-in enables users who register to use the forums on your site to be automatically added to the mailing list of your choice.' , 'yikes-inc-easy-mailchimp-extender' ),
		'contact_form_7' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/cf7-banner.png" title="' . __( 'Contact Form 7' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Once the Contact Form 7 integration is active you can use our custom shortcode [yikes_mailchimp_checkbox] in your contact forms to subscribe users to a pre-selected list.' , 'yikes-inc-easy-mailchimp-extender' ),
	);
		
	// Easy Digital Downloads
	if( class_exists( 'Easy_Digital_Downloads' ) ) {
		$active_plugins['easy_digital_downloads_checkout_form'] = __( 'Easy Digital Downloads Checkout', 'yikes-inc-easy-mailchimp-extender' );
	}
	// WooCommerce
	if( class_exists( 'WooCommerce' ) ) {
		$active_plugins['woocommerce_checkout_form'] = __( 'WooCommerce Checkout', 'yikes-inc-easy-mailchimp-extender' );
	}
	// BuddyPress
	if( class_exists( 'BuddyPress' ) ) {
		$active_plugins['buddypress_form'] = __( 'BuddyPress Registration', 'yikes-inc-easy-mailchimp-extender' );
	}
	// bbPress
	if( class_exists( 'bbPress' ) ) {
		$active_plugins['bbpress_forms'] = __( 'bbPress', 'yikes-inc-easy-mailchimp-extender' );
	}
	// Contact Form 7
	if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$active_plugins['contact_form_7'] = __( 'Contact Form 7', 'yikes-inc-easy-mailchimp-extender' );
	}
	
	// store our checkbox options
	$options = get_option( 'optin-checkbox-init' , '' );	
?>
<h3><span><?php _e( 'Integration Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>


	<?php
		// lets confirm the user has a valid API key stored
		if( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
			/// Check for a transient, if not - set one up for one hour
			if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {
				// initialize MailChimp Class
				$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
				// retreive our list data
				$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ), 'limit' => 100 ) );
				// set our transient
				set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );
			}
		} else {
			?>
			<div class="inside">
				<?php
					echo sprintf( __( 'Please %s to setup your integrations.', 'yikes-inc-easy-mailchimp-extender' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=general-settings' ) ) . '" title="' . __( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'enter a valid MailChimp API key' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );
				?>
			</div>
			<?php
			return;
		}
	?>
	
<div class="inside">
		
	<p>
		<?php _e( 'Select which plugins or features Easy Forms for MailChimp by Yikes Inc. should integrate with. Depending on which plugins or features you choose to integrate with, an optin checkbox will be generated. For example, the comment form checkbox will generate a checkbox below the standard WordPress comment form to add any new commenters to a pre-determined MailChimp mailing list.' , 'yikes-inc-easy-mailchimp-extender' ); ?>
	</p>
		
	<!-- Settings Form -->
	<form action='options.php' method='post' id="checkbox-settings-form">		
	
	<?php settings_fields( 'yikes_inc_easy_mc_checkbox_settings_page' ); ?>
	
	<ul>
		<?php 
			if( !empty( $active_plugins ) ) { 
				
				foreach( $active_plugins as $class => $value ) {
					// echo  $class;
					$checked = isset( $options[$class]['value'] ) ? 'checked="checked"' : '';
					$hidden =  !isset( $options[$class]['value'] ) ? 'yikes-easy-mc-hidden' : '';
					$checkbox_label = isset( $options[$class]['label'] ) ? esc_attr__( $options[$class]['label'] ) : '';
					$precheck_checkbox = isset( $options[$class]['precheck'] ) ? $options[$class]['precheck'] : '';
					$selected_list = isset( $options[$class]['associated-list'] ) ? $options[$class]['associated-list'] : '-';
					$list_interest_groups = isset( $options[$class]['interest-groups'] ) ? $options[$class]['interest-groups'] : false;
					?>
						<li>
							<label>
								<input type="checkbox" name="optin-checkbox-init[<?php echo $class; ?>][value]" value="on" <?php echo $checked; ?> onclick="jQuery(this).parents('li').next().stop().slideToggle();"><?php echo ucwords( $value ); ?><span class="dashicons dashicons-editor-help"></span><div class="tooltiptext qtip-bootstrap" style="display:none;"><?php echo $class_descriptions[$class]; ?></div>
							</label>
						</li>
						<!-- checkbox settings, text - associated list etc. -->
						<li class="optin-checkbox-init[<?php echo $class; ?>]-settings <?php echo $hidden; ?>">
							<?php if( $class == 'contact_form_7' ) { ?>
								<p style="margin-top:0;"><small class="contact-form-7-notice"><?php printf( __( 'Use %s in Contact Form 7 to display the checkbox.', 'yikes-inc-easy-mailchimp-extender' ), '<code>[yikes_mailchimp_checkbox]</code>' ); ?></small></p>
							<?php } ?>
							<p style="margin-top:0;padding-top:0;margin-bottom:0;padding-bottom:0;">
								<!-- checkbox associated list -->
								<label><?php _e( 'Associated List' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									<?php
										if( $list_data['total'] > 0 ) {
											?>
												<select class="optin-checkbox-init[<?php echo $class; ?>][associated-list] checkbox-settings-list-dropdown" data-attr-integration="<?php echo $class; ?>" name="optin-checkbox-init[<?php echo $class; ?>][associated-list]" onchange="checkForInterestGroups( jQuery( this ), jQuery( this ).find( 'option:selected' ).val(), jQuery( this ).attr( 'data-attr-integration' ) );return false;">
														<option value="-" <?php selected( $selected_list , '-' ); ?>><?php _e( 'Select a List' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
													<?php foreach( $list_data['data'] as $list ) { ?>
														<option value="<?php echo $list['id']; ?>" <?php selected( $selected_list , $list['id'] ); ?>><?php echo $list['name']; ?></option>
													<?php } ?>
												</select>
											<?php
										} else {
											echo '<p class="description no-lists-setup-notice"><strong>' . __( 'You have not setup any lists. You should head over to MailChimp and setup your first list.' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong></p>';
										}
									?>
								</label>
								<!-- checkbox text label -->
								<label><?php _e( 'Checkbox Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									<input type="text" class="optin-checkbox-init[<?php echo $class; ?>][label] optin-checkbox-label-input" name="optin-checkbox-init[<?php echo $class; ?>][label]" value="<?php echo $checkbox_label; ?>">
								</label>
								<!-- prechecked? -->
								<label><?php _e( 'Precheck Checkbox' , 'yikes-inc-easy-mailchimp-extender' ); ?>
									<select id="optin-checkbox-init[<?php echo $class; ?>][precheck]" name="optin-checkbox-init[<?php echo $class; ?>][precheck]" class="optin-checkbox-init[<?php echo $class; ?>][precheck] checkbox-settings-list-dropdown">
										<option value="true" <?php selected( $precheck_checkbox , 'true' ); ?>><?php _e( 'Yes' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
										<option value="false" <?php selected( $precheck_checkbox , 'false' ); ?>><?php _e( 'No' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
									</select>
								</label>
								
								<!-- Interest Group -- precheck/pre-select -->
								<div class="interest-groups-container">
									<?php 	
										if ( $selected_list != '-' && get_transient( $selected_list . '_interest_group' ) ) {
											$interest_groupings = get_transient( $selected_list . '_interest_group' );
											$integration_type = $class;
											require( YIKES_MC_PATH . 'admin/partials/menu/options-sections/templates/integration-interest-groups.php' );
										} else if( $selected_list != '-' && $list_interest_groups ) {
											$list_id = $options[$class]['associated-list'];
											$integration_type = $class;
											YIKES_Inc_Easy_MailChimp_Process_Ajax::check_list_for_interest_groups( $list_id, $integration_type, true ); 
										}
									?>
								</div>
								
							</p>
							<br />
						</li>
					<?php
				}
			} else {
				?>
					<li>
						<?php _e( 'Nothing is active.' , 'yikes-inc-easy-mailchimp-extender' ); ?>
					</li>
				<?php
			}
		?>
	</ul>
	
												
	<?php submit_button(); ?>
									
	</form>
</div> <!-- .inside -->