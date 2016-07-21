<?php
	// lets confirm the user has a valid API key stored
	if( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
		// store the API key
		$api_key = yikes_get_mc_api_key();
		$dash_position = strpos( $api_key, '-' );
		/// Check for a transients, if not - set them up
		if ( false === ( $profile_info = get_transient( 'yikes-easy-mailchimp-profile-data' ) ) ) {
			if( $dash_position !== false ) {
				$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/users/profile.json';
			}
			$profile_info = wp_remote_post( $api_endpoint, array(
				'body' => array(
					'apikey' => $api_key
				),
				'timeout' => 10,
				'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
			) );
			$profile_info = json_decode( wp_remote_retrieve_body( $profile_info ), true );
			if( isset( $profile_info['error'] ) ) {
				if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $profile_info['error'], __( "Get Profile Info." , 'yikes-inc-easy-mailchimp-extender' ), "Account Details Page" );
				}
				return;
			} else {
				// set our transient for one week
				set_transient( 'yikes-easy-mailchimp-profile-data', $profile_info, 1 * WEEK_IN_SECONDS );
			}
		}
		if ( false === ( $account_details = get_transient( 'yikes-easy-mailchimp-account-data' ) ) ) {
			if( $dash_position !== false ) {
				$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/helper/account-details.json';
			}
			$account_details = wp_remote_post( $api_endpoint, array(
				'body' => array(
					'apikey' => $api_key
				),
				'timeout' => 10,
				'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
			) );
			$account_details = json_decode( wp_remote_retrieve_body( $account_details ), true );
			if( isset( $account_details['error'] ) ) {
				if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $account_details['error'], __( "Get Account Details" , 'yikes-inc-easy-mailchimp-extender' ), "Account Details Page" );
				}
			} else {
				// set our transient for one hour
				set_transient( 'yikes-easy-mailchimp-account-data', $account_details, 1 * HOUR_IN_SECONDS );
			}
		}
		if ( false === ( $account_activity = get_transient( 'yikes-easy-mailchimp-account-activity' ) ) ) {
			// retreive our list data
			if( $dash_position !== false ) {
				$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/helper/chimp-chatter.json';
			}
			$account_activity = wp_remote_post( $api_endpoint, array(
				'body' => array(
					'apikey' => $api_key
				),
				'timeout' => 10,
				'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
			) );
			$account_activity = json_decode( wp_remote_retrieve_body( $account_activity ), true );
			if( isset( $account_activity['error'] ) ) {
				if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $account_activity['error'], __( "Get Chimp Chatter" , 'yikes-inc-easy-mailchimp-extender' ), "Account Details Page" );
				}
			} else {
				// set our transient for one hour
				set_transient( 'yikes-easy-mailchimp-account-activity', $account_activity, 1 * HOUR_IN_SECONDS );
			}
		}
	} else {
		wp_die( __( 'It looks like you need to re-validate your MailChimp API key before you can continue.' , 'yikes-inc-easy-mailchimp-extender' ) , 500 );
	}
?>
<div class="wrap" id="account-details">
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1><?php _e( 'Account Overview' , 'yikes-inc-easy-mailchimp-extender' ); echo ' | ' . $profile_info['username']; ?></h1>
	<!-- Account Overview Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( "Below you'll find a brief overview of account activity as well as some account and profile info." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<!-- <div class="postbox yikes-easy-mc-postbox" id="chimp-chatter">
						<div class="inside"> -->

							<h1><?php _e( 'Chimp Chatter' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>

							<?php
								$date_format = get_option( 'date_format' );
								$time_format = get_option( 'time_format' );
								$x = 1;
								$end = count( $account_activity );
								foreach( $account_activity as $activity ) {
									$split_type = explode( ':',str_replace('-',' ',$activity['type']));
									switch( $activity['type'] ) {
										case 'lists:new-subscriber':
										case 'lists:profile-updates':
										case 'campaigns:facebook-likes':
										case 'campaigns:facebook-comments':
										case 'campaigns:forward-to-friend':
										case 'lists:import':
											$section_class = 'chimp-chatter-positive';
											break;

										case 'lists:unsubscribes':
											$section_class = 'chimp-chatter-negative';
											break;

										case 'campaigns:inbox-inspections':
											$section_class = 'chimp-chatter-neutral';
											break;
									}
									if( $x < 5 ) {
									?>
										<!--- <section class="chimp-chatter-container <?php echo esc_attr( $section_class ); ?>">
											<div class="chatter-type"><?php echo ucwords( $split_type[1] ); ?></div>
											<p class="chatter-message description">
												<?php echo $activity['message']; ?>
											</p>
											<p class="chatter-time">
												<?php echo get_date_from_gmt( $activity['update_time'], 'F jS, Y h:i a' ); ?>
											</p>
										</section> -->

										<section class="postbox yikes-easy-mc-postbox chimp-chatter-postbox <?php echo esc_attr( $section_class ); if( $x % 2 == 0 ) { echo ' even'; } ?>">
											<div class="inside">
												<div class="chatter-type"><?php echo ucwords( $split_type[1] ); ?></div>
												<p class="chatter-message description">
													<?php echo $activity['message']; ?>
												</p>
												<p class="chatter-time">
													<?php echo get_date_from_gmt( $activity['update_time'], 'F jS, Y h:i a' ); ?>
												</p>
											</div>
										</section>
									<?php
									} else {
										if( $x == 7 ) {
											?>
												<div id="hidden-chatter" class="yikes-easy-mc-hidden">
											<?php
										}
										?>
												<!-- <section class="chimp-chatter-container <?php echo esc_attr( $section_class ); ?>">
													<div class="chatter-type"><?php echo ucwords( $split_type[1] ); ?></div>
													<p class="chatter-message description">
														<?php echo $activity['message']; ?>
													</p>
													<p class="chatter-time">
														<?php echo get_date_from_gmt( $activity['update_time'], 'F jS, Y h:i a' ); ?>
													</p>
												</section> -->

												<section class="postbox yikes-easy-mc-postbox chimp-chatter-postbox <?php echo esc_attr( $section_class ); if( $x % 2 == 0 ) { echo ' even'; } ?>">
													<div class="inside">
														<div class="chatter-type"><?php echo ucwords( $split_type[1] ); ?></div>
														<p class="chatter-message description">
															<?php echo $activity['message']; ?>
														</p>
														<p class="chatter-time">
															<?php echo get_date_from_gmt( $activity['update_time'], 'F jS, Y h:i a' ); ?>
														</p>
													</div>
												</section>
										<?php
										if( $x == $end ) {
											?>
												</div>
											<?php
										}
									}
									$x++;
								}
								?>

								<div class="chimpchatter-button-container">
									<a href="#" onclick="jQuery(this).parents().find('#hidden-chatter').slideToggle();jQuery(this).fadeOut();return false;" class="button-primary"><?php _e( 'View All Activity' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
								</div>

						<!-- </div> -->
						<!-- .inside -->
					<!-- </div> -->
					<!-- .postbox -->
				</div>
				<!-- .meta-box-sortables .ui-sortable -->
			</div>
			<!-- post-body-content -->
			<!-- sidebar -->



			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">

					<div class="postbox yikes-easy-mc-postbox">
						<div class="inside">

							<a href="https://us3.admin.mailchimp.com/" title="<?php _e( 'MailChimp Site' , 'yikes-inc-easy-mailchimp-extender' ); ?>" target="_blank">
								<img src="<?php echo YIKES_MC_URL . 'includes/images/Welcome_Page/mailchimp-logo.png'; ?>" title="<?php _e( 'MailChimp Site' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="chimp-chatter-mailchimp-logo">
							</a>

						</div>
						<!-- .inside -->
					</div>

					<div class="postbox yikes-easy-mc-postbox chimp-chatter-sidebar">
						<div class="inside">

							<h2 class="account-status"><?php if( $account_details['has_activated'] == 1 ) { ?><div class="circle-account-active" title="<?php _e( "Account Active" , 'yikes-inc-easy-mailchimp-extender' ); ?>"></div><?php } else { ?><div class="circle-account-inactive" title="<?php _e( "Account Inactive" , 'yikes-inc-easy-mailchimp-extender' ); ?>"></div><?php } echo $profile_info['name']; ?> <small>(<?php echo $profile_info['role']; ?>)</small></h2>

							<img class="mailchimp-avatar" src="<?php echo esc_url_raw( $profile_info['avatar'] ); ?>" title="<?php echo $profile_info['username'] . ' ' . __( "MailChimp avatar" , 'yikes-inc-easy-mailchimp-extender' ); ?>">

							<table class="form-table" id="account-details-table">
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Company' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo $account_details['contact']['company']; ?><br /><?php echo $account_details['contact']['city'] . ', ' . $account_details['contact']['state']; ?></td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Industry' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo $account_details['industry']; ?></td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Member Since' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo date( get_option('date_format') , strtotime( $account_details['member_since'] ) ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Plan Type' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo ucwords( $account_details['plan_type'] ); ?></td>
								</tr>
								<?php if( $account_details['plan_type'] == 'payasyougo' || $account_details['plan_type'] == 'free' ) { ?>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Emails Left' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo number_format( $account_details['emails_left'] ); ?></td>
								</tr>
								<?php } ?>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Affiliate Link' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><a href="#" onclick="jQuery(this).hide().next().fadeIn('fast');return false;" class="button-secondary"><?php _e( 'View Link', 'yikes-inc-easy-mailchimp-extender' ); ?></a><input type="text" class="widefat mailchimp-affiliate-link" readonly value="<?php echo esc_url_raw( $account_details['affiliate_link'] ); ?>" onclick="jQuery(this).select();return false;"></td>
								</tr>
							</table>

						</div>
						<!-- .inside -->
					</div>
					<!-- .postbox -->
					<?php
						// Generate Show Some Love!
						$this->generate_show_some_love_container();
					?>

				</div>
				<!-- .meta-box-sortables -->
			</div>
			<!-- #postbox-container-1 .postbox-container -->
		</div>
		<!-- #post-body .metabox-holder .columns-2 -->
		<br class="clear">
	</div>
	<!-- #poststuff -->
</div> <!-- .wrap -->
