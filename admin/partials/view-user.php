<?php
	/*
	*	Get and store our variables
	*	@since 6.0
	*/
	$list_id = $_GET['mailchimp-list'];
	$email_id = (int) esc_attr( $_GET['email-id'] );

	/*
	*	Confirm that our data is set
	*	or abort...
	*/
	if( ! isset( $list_id ) || ! isset( $email_id ) ) {
		wp_die( "We've encountered an error. Please go back and try again", "yikes-inc-easy-mailchimp-extender" );
		exit;
	}

	// run our API call, to get list data..
	$api_key = yikes_get_mc_api_key();
	$dash_position = strpos( $api_key, '-' );

	// get this lists data
	if( $dash_position !== false ) {
		$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/member-info.json';
	}
	$user_data = wp_remote_post( $api_endpoint, array(
		'body' => array(
			'apikey' => $api_key,
			'id' => $list_id,
			'emails' => array(
				array( 'leid' => $email_id )
			),
		),
		'timeout' => 10,
		'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true ),
	) );
	$user_data = json_decode( wp_remote_retrieve_body( $user_data ), true );

	if( isset( $user_data['error'] ) ) {
		if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
			require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->yikes_easy_mailchimp_write_to_error_log( $user_data['error'], __( "Get Member Info" , 'yikes-inc-easy-mailchimp-extender' ), "View User Page" );
		}
	}

	/*
	*	Check for MailChimp returned errors
	*/
	if( isset( $user_data['error'] ) ) {
		echo '<h4>Error</h4>';
		echo $user_data['error'] . '.';
		return;
	}

	if( isset( $user_data['data'][0] ) ) {
		// reset our data so we can easily use it
		$user_data = $user_data['data'][0];

		$other_lists = ( isset( $user_data['lists'] ) && ! empty( $user_data['lists'] ) ) ? $user_data['lists'] : array();
		$merge_data_array = ( $user_data['merges'] && ! empty( $user_data['merges'] ) ) ? $user_data['merges'] : array();

		// print_r( $user_data );

		/* Empty array to populate with list names */
		$additional_lists = array();
		/* Merge Variable Fields */
		$merge_variable_fields = array();

		/* Build the array of mailing lists the user is subscribed to */
		if( isset( $other_lists ) && count( $other_lists ) >= 1 ) {
			foreach( $other_lists as $list ) {
				if( $list['status'] == 'subscribed' ) {
					if( $dash_position !== false ) {
						$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/list.json';
					}
					$list_data = wp_remote_post( $api_endpoint, array(
						'body' => array(
							'apikey' => $api_key,
							'filters' => array(
								'list_id' => $list['id']
							)
						),
						'timeout' => 10,
						'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
					) );
					$list_data = json_decode( wp_remote_retrieve_body( $list_data ), true );
					if( isset( $list_data['error'] ) ) {
						if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
							require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
							$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
							$error_logging->yikes_easy_mailchimp_write_to_error_log( $list_data['error'], __( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ), "View User Page" );
						}
					}
					if( $list_data && isset( $list_data['data'][0] ) ) {
						$additional_lists[$list_data['data'][0]['id']] = $list_data['data'][0]['name'];
					}
				}
			}
		}

		/* Build the array of merge variables => value */
		if( isset( $merge_data_array ) && count( $merge_data_array ) >= 1 ) {
			if( $dash_position !== false ) {
				$api_endpoint = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/lists/merge-vars.json';
			}
			$merge_variables = wp_remote_post( $api_endpoint, array(
				'body' => array(
					'apikey' => $api_key,
					'id' => array( $list_id ),
				),
				'timeout' => 10,
				'sslverify' => apply_filters( 'yikes-mailchimp-sslverify', true )
			) );
			$merge_variables = json_decode( wp_remote_retrieve_body( $merge_variables ), true );
			if( isset( $merge_variables['error'] ) ) {
				if( WP_DEBUG || get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
					require_once YIKES_MC_PATH . 'includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
					$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
					$error_logging->yikes_easy_mailchimp_write_to_error_log( $merge_variables['error'], __( "Get Merge Variables" , 'yikes-inc-easy-mailchimp-extender' ), "View User Page" );
				}
			}
			// loop and display
			if( $merge_variables ) {
				foreach( $merge_variables['data'][0]['merge_vars'] as $merge_variable ) {
					if( $merge_variable['tag'] != 'EMAIL' ) {
						$merge_variable_fields[$merge_variable['name']] = ( isset( $merge_data_array[$merge_variable['tag']] ) ) ? $merge_data_array[$merge_variable['tag']] : '';
					}
				}
			}
		}


		// store usable data
		$user_email = sanitize_email( $user_data['email'] );
		// prepend our email address to the beginning
		$merge_variable_fields = array( 'Email Address' => $user_email ) + $merge_variable_fields;
		$gravatar_image = get_avatar( $user_email, 120 );
		$email_type = $user_data['email_type'];
		$member_rating = ( ! empty( $user_data['member_rating'] ) ) ? (int) $user_data['member_rating'] : 0;
		$member_rating_stars = '';
		if( $member_rating > 0 ) {
			$x = 1;
			while( $x <= 5 ) {
				if( $x <= $member_rating ) {
					$member_rating_stars .= '<span class="yikes-mc-member-rating-star dashicons dashicons-star-filled"></span>';
				} else {
					$member_rating_stars .= '<span class="yikes-mc-member-rating-star dashicons dashicons-star-empty"></span>';
				}
				$x++;
			}
		} else {
			$y = 1;
			while( $y <= 5 ) {
				$member_rating_stars .= '<span class="yikes-mc-member-rating-star dashicons dashicons-star-empty"></span>';
				$y++;
			}
		}
		$last_changed = strtotime( $user_data['info_changed'] );
		$user_language = ( $user_data['language'] && $user_data['language'] != '' ) ? $user_data['language'] : '';
		$list_name = $user_data['list_name'];

		// Generate our display page
		?>
			<div class="wrap view-user-data-wrap">
				<!-- Freddie Logo -->
				<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php __( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

				<h1>YIKES Easy Forms for MailChimp | <?php _e( 'Subscriber Details' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>

				<!-- Settings Page Description -->
				<p class="yikes-easy-mc-about-text about-text"><?php printf( __( 'View %s subscriber details below.' , 'yikes-inc-easy-mailchimp-extender' ), $user_email ); ?></p>

				<section class="yikes-mc-view-list-breadcrumbs">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-lists' ) ); ?>" title="<?php _e( 'View List', 'yikes-inc-easy-mailchimp-extender' ); ?>">
						<?php _e( 'Optin Forms', 'yikes-inc-easy-mailchimp-extender' ); ?>
					</a>
					&nbsp;&#187;&nbsp;
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list_id ) ); ?>" title="<?php echo $list_name; ?>">
						<?php echo $list_name; ?>
					</a>
					&nbsp;&#187;&nbsp;
					<span title="<?php echo $user_email; ?>">
						<?php echo $user_email; ?>
					</span>
				</section>

				<!-- Customer Container -->
				<div id="yikes-mc-subscriber-card-wrapper">
					<section class="yikes-mc-card-top">
						<?php echo $gravatar_image; ?>
						<h2><?php echo $user_email; ?></h2>
						<?php echo '<span class="member-star-rating-container" title="' . sprintf( _n( 'Member Rating: %s star', 'Member Rating: %s stars', $member_rating, 'yikes-inc-easy-mailchimp-extender' ), $member_rating ) . '">' . $member_rating_stars . '</span>'; ?>
						<span class="member-subscription-date">
							<?php echo __( 'Subscribed:', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . get_date_from_gmt( $user_data['info_changed'], 'F jS, Y h:i a' ); ?>
						</span>
						<?php if( isset( $user_data['geo'] ) && ! empty( $user_data['geo'] ) ) { ?>
							<?php if( isset( $user_data['geo']['latitude'] ) && isset( $user_data['geo']['longitude'] ) ) { ?>
								<span class="member-location-data">
									<?php echo __( 'Location:', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . yikes_mc_geocode_subscriber_data( $user_data['geo']['latitude'], $user_data['geo']['longitude'] ); ?>
								</span>
							<?php } else { ?>
							<span class="member-location-data">
								<?php echo __( 'Location:', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . $user_data['geo']['region'] . ', ' . $user_data['geo']['cc']; ?>
							</span>
						<?php
								}
							}
						?>
					</section>

					<hr class="yikes-mc-subscriber-hr" />

					<?php
						if( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'subscriber-data' ) ) {
							?>
								<section class="yikes-mc-card-body merge-variable-section">
								<h3><?php _e( 'Fields:', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
								<?php
									if( ! empty( $merge_variable_fields ) ) {
										foreach( $merge_variable_fields as $field_name => $value ) { ?>
											<li>
												<label>
													<strong class="section-label"><?php echo $field_name; ?></strong>
										
													<?php if ( strtolower( $field_name ) === 'address' && is_array( $value ) ) { ?>
														<p class="section-value"><em><?php echo yikes_mc_format_address_field( $value ); ?></em></p>
													<?php } else { ?>
														<p class="section-value"><em><?php echo $value; ?></em></p>
												<?php } ?>
											</label>
										</li>
										<?php }
									} else {
										?>
											<strong><?php _e( 'No Subscriber Data Found', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										<?php
									}
									if( isset( $user_data['ip_signup'] ) && $user_data['ip_signup'] != '' ) {
										?>
											<li>
												<label>
													<strong class="section-label"><?php echo __( 'Signup IP', 'yikes-inc-easy-mailchimp-extender' ) . '</strong><p class="section-value"><em>' . $user_data['ip_signup']; ?></strong></p>
												</label>
											</li>
										<?php
									}
								}
								?>
								</section>
							<?php


						if( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) {
							?>
								<section class="yikes-mc-card-body">
								<?php
								if( ! empty( $additional_lists ) ) {
									// remove this list from the additional lists list
									unset( $additional_lists[$list_id] );
									if( ! empty( $additional_lists ) ) {
										?>
										<h3><?php _e( 'Additional Subscriptions:', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
										<?php foreach( $additional_lists as $listid => $name ) { ?>
											<?php
												$user_redirect_url = esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $listid ) );
											?>
											<li><a href="<?php echo $user_redirect_url; ?>"><?php echo $name; ?></a></li>
										<?php }
									}
								} else {
								?>
									<strong><?php _e( 'No Other Subscriptions Found.', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
								<?php
								}
							?>
								</section>
							<?php
						}
					?>

					<?php
						if( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) {
							$unsubscribe_subscriber_url = esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-unsubscribe-user', 'mailchimp-list' => $list_id , 'nonce' => wp_create_nonce( 'unsubscribe-user-'.$email_id ), 'email_id' => $email_id ) ) );
							?>
								<form id="delete_subscriber" method="POST" action="<?php echo $unsubscribe_subscriber_url; ?>">
									<p class="description">
										<?php printf( __( 'Deleting this subscriber will completely remove %s from the "%s" MailChimp list.', 'yikes-inc-easy-mailchimp-extender' ), '<strong>' . $user_email . '</strong>', '<strong>' . $list_name . '</strong>' ); ?>
									</p>
									<br />
									<label>
										<input type="checkbox" name="confirm_delete_subscriber" value="1" onclick="toggleDeleteSubscriberButton(jQuery(this));">
										<?php printf( __( 'Are you sure you want to delete "%s" from "%s?"', 'yikes-inc-easy-mailchimp-extender' ), '<strong>' . $user_email . '</strong>', '<strong>' . $list_name . '</strong>' ); ?>
									</label>
									<?php echo submit_button( __( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ), 'primary', 'delete-mailchimp-subscriber', true, array( 'disabled' => 'disabled' ) ); ?>
								</form>
							<?php
						}
					?>

				</div>

				<?php
					// setup the redirect url for our additional subscriptions
					$subscriber_details = esc_url_raw(
						add_query_arg(
							array(
								'section' => 'subscriber-data'
							)
						)
					);
					// setup the redirect url for our additional subscriptions
					$additional_subscription_url = esc_url_raw(
						add_query_arg(
							array(
								'section' => 'additional-subscriptions'
							)
						)
					);
					// setup the redirect url for our delete subscriber
					$delete_subscriber_url = esc_url_raw(
						add_query_arg(
							array(
								'section' => 'delete-subscriber'
							)
						)
					);
				?>
				<!-- Tabs -->
				<div id="customer-tab-wrapper">
						<ul id="customer-tab-wrapper-list">

							<?php if( isset( $_GET['section'] ) && $_GET['section'] != 'subscriber-data' ) { ?>
								<a title="<?php _e( 'Subscriber Details', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Subscriber Details', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $subscriber_details; ?>">
							<?php } ?>

								<li <?php if( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'subscriber-data' ) ) { ?>class="active"<?php } else { ?>class="inactive"<?php } ?>><span class="dashicons  dashicons-id"></span></li>

							<?php if( isset( $_GET['section'] ) && $_GET['section'] != 'subscriber-data' ) { ?>
								</a>
							<?php } ?>

							<?php if( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] != 'additional-subscriptions' ) ) { ?>
								<a title="<?php _e( 'Additional Subscriptions', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Additional Subscriptions', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $additional_subscription_url; ?>">
							<?php } ?>

								<li <?php if( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) { ?>class="active"<?php } else { ?>class="inactive"<?php } ?>><span class="dashicons dashicons-portfolio"></span></li>

							<?php if( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) ) { ?>
								</a>
							<?php } ?>

							<?php if( ! isset( $_GET['section'] ) || isset( $_GET['section'] ) && $_GET['section'] != 'delete-subscriber' ) { ?>
								<a title="<?php _e( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $delete_subscriber_url; ?>">
							<?php } ?>

								<li <?php if( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) { ?>class="active"<?php } else { ?>class="inactive"<?php } ?>><span class="dashicons dashicons-trash"></span></li>

							<?php if( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) ) { ?>
								</a>
							<?php } ?>

						</ul>
					</div>

			</div>
		<?php
	}


	function yikes_mc_geocode_subscriber_data( $latitude, $longitude ) {
		$geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude;
		$geocode_response = wp_remote_get( $geocode_url );
		if( is_wp_error( $geocode_response ) ) {
			return;
		}
		$geocode_response_body = json_decode( wp_remote_retrieve_body( $geocode_response ), true );
		if( is_wp_error( $geocode_response_body ) ) {
			return;
		}
		$city = $geocode_response_body['results'][0]['address_components'][2]['short_name'];
		$state = $geocode_response_body['results'][0]['address_components'][5]['short_name'];
		$country = $geocode_response_body['results'][0]['address_components'][6]['short_name'];
		return $link = '<a href="http://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '" target="_blank" title="' . __( 'View Google Map', 'yikes-inc-easy-mailchimp-extender' ) . '">' . $city . ', ' . $state . ', ' . $country . '</a>&nbsp;<span class="flag-icon flag-icon-' . strtolower( $country ) . '"></span>';
	}

	function yikes_mc_format_address_field( $address_array ) {
		$address_string = '';

		if ( isset( $address_array['addr1'] ) && ! empty( $address_array['addr1'] ) && ! trim( $address_array['addr1'] ) === '-' ) {
			$address_string .= $address_array['addr1'];
		}
		if ( isset( $address_array['addr2'] ) && ! empty( $address_array['addr2'] ) ) {
			$address_string .= ', ';
			$address_string .= $address_array['addr2'];
		}
		if ( isset( $address_array['city'] ) && ! empty( $address_array['city'] ) ) {
			$address_string .= ', ';
			$address_string .= $address_array['city'];
		}
		if ( isset( $address_array['state'] ) && ! empty( $address_array['state'] ) ) {
			$address_string .= ', ';
			$address_string .= $address_array['state'];
		}
		if ( isset( $address_array['zip'] ) && ! empty( $address_array['zip'] ) ) {
			$address_string .= $address_array['zip'];
		}
		if ( isset( $address_array['country'] ) && ! empty( $address_array['country'] ) ) {
			$address_string .= ', ';
			$address_string .= $address_array['country'];
		}
		
		//trim any initial whitespace and commas
		$address_string = ltrim( $address_string, ',' );

		return $address_string;	
	}
