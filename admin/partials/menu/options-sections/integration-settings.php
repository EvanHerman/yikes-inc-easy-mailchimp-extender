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

	// enqueue jquery qtip for our tooltip.
	wp_enqueue_script( 'jquery-qtip-tooltip', YIKES_MC_URL . 'admin/js/min/jquery.qtip.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'jquery-qtip-style', YIKES_MC_URL . 'admin/css/jquery.qtip.min.css' );

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
	// defaults: comments / registration.
	$active_plugins = array(
		'comment_form'      => __( 'WordPress Comment Form', 'yikes-inc-easy-mailchimp-extender' ),
		'registration_form' => __( 'WordPress Registration Form', 'yikes-inc-easy-mailchimp-extender' ),
	);

	$class_descriptions = array(
		'comment_form'                         => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/wordpress-banner-logo.png" title="' . __( 'WordPress', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WordPress core comment form opt-in checkbox will display a checkbox to your current users when leaving a comment (if they are not currently subscribed).', 'yikes-inc-easy-mailchimp-extender' ),
		'registration_form'                    => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/wordpress-banner-logo.png" title="' . __( 'WordPress', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WordPress core registration form opt-in checkbox will display a checkbox to new users when registering for your site.', 'yikes-inc-easy-mailchimp-extender' ),
		'woocommerce_checkout_form'            => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/woocommerce-banner.png" title="' . __( 'WooCommerce Store', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the WooCommerce checkout opt-in form allows you to capture email addresses from users who make purchases in your store. This option will add an opt-in checkbox to the checkout page.', 'yikes-inc-easy-mailchimp-extender' ),
		'easy_digital_downloads_checkout_form' => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/edd-banner.png" title="' . __( 'Easy Digital Downloads', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the Easy Digital Downloads checkout opt-in allows users who make a purchase to opt-in to your mailing list during checkout.', 'yikes-inc-easy-mailchimp-extender' ),
		'buddypress_form'                      => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/buddypress-banner.png" title="' . __( 'BuddyPress', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the BuddyPress opt-in allows users who register for your site to be automatically added to the mailing list of your choice.', 'yikes-inc-easy-mailchimp-extender' ),
		'bbpress_forms'                        => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/bbpress-banner.png" title="' . __( 'bbPress', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Enabling the bbPress opt-in enables users who register to use the forums on your site to be automatically added to the mailing list of your choice.', 'yikes-inc-easy-mailchimp-extender' ),
		'contact_form_7'                       => '<img class="tooltip-integration-banner" src="' . YIKES_MC_URL . 'includes/images/Checkbox_Integration_Logos/cf7-banner.png" title="' . __( 'Contact Form 7', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'Once the Contact Form 7 integration is active you can use our custom shortcode [yikes_mailchimp_checkbox] in your contact forms to subscribe users to a pre-selected list.', 'yikes-inc-easy-mailchimp-extender' ),
	);

	// Easy Digital Downloads.
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		$active_plugins['easy_digital_downloads_checkout_form'] = __( 'Easy Digital Downloads Checkout', 'yikes-inc-easy-mailchimp-extender' );
	}
	// WooCommerce.
	if ( class_exists( 'WooCommerce' ) ) {
		$active_plugins['woocommerce_checkout_form'] = __( 'WooCommerce Checkout', 'yikes-inc-easy-mailchimp-extender' );
	}
	// BuddyPress.
	if ( class_exists( 'BuddyPress' ) ) {
		$active_plugins['buddypress_form'] = __( 'BuddyPress Registration', 'yikes-inc-easy-mailchimp-extender' );
	}
	// bbPress.
	if ( class_exists( 'bbPress' ) ) {
		$active_plugins['bbpress_forms'] = __( 'bbPress', 'yikes-inc-easy-mailchimp-extender' );
	}
	// Contact Form 7.
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$active_plugins['contact_form_7'] = __( 'Contact Form 7', 'yikes-inc-easy-mailchimp-extender' );
	}

	// store our checkbox options.
	$options = get_option( 'optin-checkbox-init', '' );
?>
<h3><span><?php _e( 'Integration Settings', 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>


	<?php
	// lets confirm the user has a valid API key stored.
	if ( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
		$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_lists();
		if ( is_wp_error( $list_data ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->maybe_write_to_log(
				$list_data->get_error_code(),
				__( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ),
				"Integration Settings Page"
			);
		}
	} else {
		?>
		<div class="inside">
			<?php
				echo sprintf( __( 'Please %s to setup your integrations.', 'yikes-inc-easy-mailchimp-extender' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=general-settings' ) ) . '" title="' . __( 'General Settings', 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( 'enter a valid Mailchimp API key', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );
			?>
		</div>
		<?php
		return;
	}
	?>

<div class="inside">

	<p>
		<?php _e( 'An opt-in checkbox will be added to the forms generated by the checked off items below. For example, checking off "WordPress Comment Form" will generate a checkbox below the WordPress comment form to add new commenters to your Mailchimp mailing lists.', 'yikes-inc-easy-mailchimp-extender' ); ?>
	</p>

	<!-- Settings Form -->
	<form action='options.php' method='post' id="checkbox-settings-form">

	<?php settings_fields( 'yikes_inc_easy_mc_checkbox_settings_page' ); ?>

	<ul>
		<?php
			if ( ! empty( $active_plugins ) ) {

				foreach( $active_plugins as $class => $value ) {

					$checked              = isset( $options[$class]['value'] ) ? 'checked="checked"' : '';
					$hidden               = ! isset( $options[$class]['value'] ) ? 'yikes-easy-mc-hidden' : '';
					$checkbox_label       = isset( $options[$class]['label'] ) ? esc_attr( $options[$class]['label'] ) : '';
					$precheck_checkbox    = isset( $options[$class]['precheck'] ) ? $options[$class]['precheck'] : '';
					$selected_list        = isset( $options[$class]['associated-list'] ) ? $options[$class]['associated-list'] : '-';
					$list_interest_groups = isset( $options[$class]['interest-groups'] ) ? $options[$class]['interest-groups'] : false;

					// Force the selected list to be an array (@since 6.4).
					$selected_list        = is_array( $selected_list ) ? $selected_list : array( $selected_list );
					?>
						<li class="yikes-mailchimp-checkbox-integration-item">
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

								<!-- Associated Lists -->
								<div class="checkbox-lists"><strong><?php _e( 'Choose Lists: ', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<?php
									if ( count( $list_data ) > 0 ) {
									?>
										<?php foreach( $list_data as $list ) { ?>

											<?php
												$list_interest_groups = isset( $list_interest_groups[ $list['id'] ] ) ? $list_interest_groups[ $list['id'] ] : $list_interest_groups;
											?>

											<label class="yikes-mailchimp-checkbox-integration-list" for="list-<?php echo $class ?>-<?php echo $list['id']; ?>">
												<input type="checkbox" class="checkbox-settings-list-item" data-integration="<?php echo $class; ?>" 
													name="optin-checkbox-init[<?php echo $class; ?>][associated-list][]"
													value="<?php echo $list['id']; ?>" <?php echo in_array( $list['id'], $selected_list ) ? 'checked="checked"' : ''; ?> 
													id="list-<?php echo $class ?>-<?php echo $list['id']; ?>">
												<?php echo $list['name']; ?>
											</label>

											<!-- If interest groups have been selected already, load them here -->
											<?php
											if ( in_array( $list['id'], $selected_list ) && $list_interest_groups ) {
												YIKES_Inc_Easy_Mailchimp_Process_Ajax::check_list_for_interest_groups( $list['id'], $class, true );
											}
											?>

										<?php } ?>
									<?php
								} else {
									echo '<p class="description no-lists-setup-notice"><strong>' . __( 'You have not setup any lists. Head over to Mailchimp and setup your first list.', 'yikes-inc-easy-mailchimp-extender' ) . '</strong></p>';
								}
								?>
								</div>

								<!-- checkbox text label -->
								<label class="optin-checkbox-label">
									<strong><?php _e( 'Opt-in Checkbox Label:', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<input type="text" class="optin-checkbox-init[<?php echo $class; ?>][label] optin-checkbox-label-input" name="optin-checkbox-init[<?php echo $class; ?>][label]" value="<?php echo $checkbox_label; ?>">
								</label>
								<!-- prechecked? -->
								<label class="optin-checkbox-label">
									<strong><?php _e( 'Precheck Checkbox?', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
									<select id="optin-checkbox-init[<?php echo $class; ?>][precheck]" name="optin-checkbox-init[<?php echo $class; ?>][precheck]" class="optin-checkbox-init[<?php echo $class; ?>][precheck] checkbox-settings-list-dropdown">
										<option value="true" <?php selected( $precheck_checkbox , 'true' ); ?>><?php _e( 'Yes', 'yikes-inc-easy-mailchimp-extender' ); ?></option>
										<option value="false" <?php selected( $precheck_checkbox , 'false' ); ?>><?php _e( 'No', 'yikes-inc-easy-mailchimp-extender' ); ?></option>
									</select>
								</label>
							</p>
						</li>
					<?php
			}
		} else {
			?>
				<li>
					<?php _e( 'Nothing is active.', 'yikes-inc-easy-mailchimp-extender' ); ?>
				</li>
			<?php
		}
		?>
	</ul>


	<?php submit_button(); ?>

	</form>
</div> <!-- .inside -->
