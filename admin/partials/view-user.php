<?php
/*
*	Get and store our variables
*	@since 6.0
*/
$list_id     = $_GET['mailchimp-list'];
$email_id    = esc_attr( $_GET['email-id'] );
$list_helper = yikes_get_mc_api_manager()->get_list_handler();

/*
*	Confirm that our data is set
*	or abort...
*/
if ( ! isset( $list_id ) || ! isset( $email_id ) ) {
	wp_die( "We've encountered an error. Please go back and try again", 'yikes-inc-easy-mailchimp-extender' );
	exit;
}

$user_data = $list_helper->get_member( $list_id, $email_id );
if ( is_wp_error( $user_data ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$user_data->get_error_code(),
		__( 'Get Member Info', 'yikes-inc-easy-mailchimp-extender' ),
		'View User Page'
	);

	echo '<h4>Error</h4>';
	echo $user_data->get_error_code() . '.';

	return;
}

if ( empty( $user_data ) ) {
	return;
}

$other_lists      = $list_helper->get_members_lists( $email_id );
$merge_data_array = $user_data['merge_fields'];

$additional_lists = array();
$merge_variable_fields = array();

/* Build the array of mailing lists the user is subscribed to */
foreach ( $other_lists as $id => $value ) {
	if ( 'subscribed' !== $value['status'] ) {
		continue;
	}

	$list_data = $list_helper->get_list( $id );
	if ( is_wp_error( $list_data ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$list_data->get_error_code(),
			__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
			"View User Page"
		);
		continue;
	}

	$additional_lists[ $list_data['id'] ] = $list_data['name'];
}

/* Build the array of merge variables => value */
$merge_variables = $list_helper->get_merge_fields( $list_id );
if ( is_wp_error( $merge_variables ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$merge_variables->get_error_code(),
		__( "Get Merge Variables", 'yikes-inc-easy-mailchimp-extender' ),
		"View User Page"
	);
	$merge_variables = array();
}
// loop and display
if ( $merge_variables ) {
	foreach ( $merge_variables['merge_fields'] as $merge_variable ) {
		if ( $merge_variable['tag'] != 'EMAIL' ) {
			$merge_variable_fields[ $merge_variable['name'] ] = ( isset( $merge_data_array[ $merge_variable['tag'] ] ) ) ? $merge_data_array[ $merge_variable['tag'] ] : '';
		}
	}
}


// store usable data
$user_email = sanitize_email( $user_data['email_address'] );
// prepend our email address to the beginning
$merge_variable_fields = array( 'Email Address' => $user_email ) + $merge_variable_fields;
$gravatar_image        = get_avatar( $user_email, 120 );
$email_type            = $user_data['email_type'];
$member_rating         = ( ! empty( $user_data['member_rating'] ) ) ? (int) $user_data['member_rating'] : 0;
$member_rating_stars   = '';

// Create member rating stars
for ( $i = 1; $i <= 5; $i++ ) {
	if ( $i <= $member_rating ) {
		$member_rating_stars .= '<span class="yikes-mc-member-rating-star dashicons dashicons-star-filled"></span>';
	} else {
		$member_rating_stars .= '<span class="yikes-mc-member-rating-star dashicons dashicons-star-empty"></span>';
	}
}

$last_changed  = strtotime( $user_data['last_changed'] );
$user_language = ( ! empty( $user_data['language'] ) ) ? $user_data['language'] : '';
$list_name     = $additional_lists[ $list_id ];

// Generate our display page
?>
	<div class="wrap view-user-data-wrap yikes-easy-mc-wrap">
		<!-- Freddie Logo -->
		<img src="<?php echo YIKES_MC_URL . 'includes/images/Mailchimp_Assets/Freddie_60px.png'; ?>" alt="<?php __( 'Freddie - Mailchimp Mascot', 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

		<h1>YIKES Easy Forms for Mailchimp | <?php _e( 'Subscriber Details', 'yikes-inc-easy-mailchimp-extender' ); ?></h1>

		<!-- Settings Page Description -->
		<p class="yikes-easy-mc-about-text about-text"><?php printf( __( 'View %s subscriber details below.', 'yikes-inc-easy-mailchimp-extender' ), $user_email ); ?></p>

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
				<?php echo esc_html( $user_email ); ?>
			</span>
		</section>

		<!-- Customer Container -->
		<div id="yikes-mc-subscriber-card-wrapper">
			<section class="yikes-mc-card-top">
				<?php echo $gravatar_image; ?>
				<h2><?php echo esc_html( $user_email ); ?></h2>
				<?php /* translators: the placeholder is a number between 1-5 */ ?>
				<span class="member-star-rating-container" title="<?php echo esc_html( sprintf( _n( 'Member Rating: %s star', 'Member Rating: %s stars', esc_attr( $member_rating ), 'yikes-inc-easy-mailchimp-extender' ), esc_attr( $member_rating ) ) ); ?>">
					<?php echo $member_rating_stars; ?>
				</span>
				<span class="member-subscription-date">
						<?php
							/* translators: the placeholder is a datetime string. */
							echo sprintf( esc_html__( 'Subscribed: %1$1s', 'yikes-inc-easy-mailchimp-extender' ), esc_html( gmdate( 'F jS, Y h:i a', $last_changed ) ) );
						?>
					</span>
				<?php if ( isset( $user_data['location'] ) && isset( $user_data['location']['timezone'] ) && ! empty( $user_data['location']['timezone'] ) && isset( $user_data['location']['country_code'] ) && ! empty( $user_data['location']['country_code'] ) ) { ?>
					<span class="member-location-data">
						<?php
							/* translators: the placeholders are a timezone and a country code */
							echo sprintf( esc_html__( 'Location: %1$1s, %2$2s', 'yikes-inc-easy-mailchimp-extender' ), esc_html( $user_data['location']['timezone'] ), esc_html( $user_data['location']['country_code'] ) );
						?>
					</span>
				<?php } ?>
			</section>

			<hr class="yikes-mc-subscriber-hr" />

			<?php
			if ( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && filter_var( wp_unslash( $_GET['section'] ), FILTER_SANITIZE_STRING ) === 'subscriber-data' ) ) {
				?>
			<section class="yikes-mc-card-body merge-variable-section">
				<h3><?php esc_html_e( 'Fields:', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
				<?php
				if ( ! empty( $merge_variable_fields ) ) {
					?>
					<?php foreach ( $merge_variable_fields as $field_name => $value ) { ?>
						<li>
							<label>
								<strong class="section-label"><?php echo esc_html( $field_name ); ?></strong>
								<p class="section-value"><em><?php echo esc_html( $value ); ?></em></p>
							</label>
						</li>
					<?php }
				} else {
					?>
					<strong><?php esc_html_e( 'No Subscriber Data Found', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
					<?php
				}
				if ( isset( $user_data['ip_signup'] ) && ! empty( $user_data['ip_signup'] ) ) {
					?>
					<li>
						<label>
							<strong class="section-label"><?php esc_html_e( 'Signup IP', 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<p class="section-value"><em><?php esc_html_e( $user_data['ip_signup'] ); ?></em></p>
						</label>
					</li>
					<?php
				}
				}
				?>
			</section>
			<?php


			if ( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) {
				?>
				<section class="yikes-mc-card-body">
					<?php
					if ( ! empty( $additional_lists ) ) {
						// remove this list from the additional lists list
						unset( $additional_lists[ $list_id ] );
						if ( ! empty( $additional_lists ) ) {
							?>
							<h3><?php _e( 'Additional Subscriptions:', 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php foreach ( $additional_lists as $listid => $name ) { ?>
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
			if ( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) {
				$unsubscribe_subscriber_url = esc_url_raw( add_query_arg( array(
					'action'         => 'yikes-easy-mc-unsubscribe-user',
					'mailchimp-list' => $list_id,
					'nonce'          => wp_create_nonce( 'unsubscribe-user-' . $email_id ),
					'email_id'       => $email_id,
				) ) );
				?>
				<form id="delete_subscriber" method="POST" action="<?php echo $unsubscribe_subscriber_url; ?>">
					<p class="description">
						<?php printf( __( 'Deleting this subscriber will completely remove %s from the "%s" Mailchimp list.', 'yikes-inc-easy-mailchimp-extender' ), '<strong>' . $user_email . '</strong>', '<strong>' . $list_name . '</strong>' ); ?>
					</p>
					<br />
					<label>
						<input type="checkbox" name="confirm_delete_subscriber" value="1" onclick="toggleDeleteSubscriberButton(jQuery(this));">
						<?php printf( __( 'Are you sure you want to delete "%s" from "%s?"', 'yikes-inc-easy-mailchimp-extender' ), '<strong>' . $user_email . '</strong>', '<strong>' . $list_name . '</strong>' ); ?>
					</label>
					<?php submit_button( __( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ), 'primary', 'delete-mailchimp-subscriber', true, array( 'disabled' => 'disabled' ) ); ?>
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
					'section' => 'subscriber-data',
				)
			)
		);
		// setup the redirect url for our additional subscriptions
		$additional_subscription_url = esc_url_raw(
			add_query_arg(
				array(
					'section' => 'additional-subscriptions',
				)
			)
		);
		// setup the redirect url for our delete subscriber
		$delete_subscriber_url = esc_url_raw(
			add_query_arg(
				array(
					'section' => 'delete-subscriber',
				)
			)
		);
		?>
		<!-- Tabs -->
		<div id="customer-tab-wrapper">
			<ul id="customer-tab-wrapper-list">

				<?php if ( isset( $_GET['section'] ) && $_GET['section'] != 'subscriber-data' ) { ?>
				<a title="<?php _e( 'Subscriber Details', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Subscriber Details', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $subscriber_details; ?>">
					<?php } ?>

					<li <?php if ( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'subscriber-data' ) ) { ?>class="active" <?php } else { ?>class="inactive"<?php } ?>>
						<span class="dashicons  dashicons-id"></span></li>

					<?php if ( isset( $_GET['section'] ) && $_GET['section'] != 'subscriber-data' ) { ?>
				</a>
			<?php } ?>

				<?php if ( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] != 'additional-subscriptions' ) ) { ?>
				<a title="<?php _e( 'Additional Subscriptions', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Additional Subscriptions', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $additional_subscription_url; ?>">
					<?php } ?>

					<li <?php if ( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) { ?>class="active" <?php } else { ?>class="inactive"<?php } ?>>
						<span class="dashicons dashicons-portfolio"></span></li>

					<?php if ( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'additional-subscriptions' ) ) { ?>
				</a>
			<?php } ?>

				<?php if ( ! isset( $_GET['section'] ) || isset( $_GET['section'] ) && $_GET['section'] != 'delete-subscriber' ) { ?>
				<a title="<?php _e( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ); ?>" aria-label="<?php _e( 'Delete Subscriber', 'yikes-inc-easy-mailchimp-extender' ); ?>" href="<?php echo $delete_subscriber_url; ?>">
					<?php } ?>

					<li <?php if ( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) { ?>class="active" <?php } else { ?>class="inactive"<?php } ?>>
						<span class="dashicons dashicons-trash"></span></li>

					<?php if ( ! isset( $_GET['section'] ) || ( isset( $_GET['section'] ) && $_GET['section'] == 'delete-subscriber' ) ) { ?>
				</a>
			<?php } ?>

			</ul>
		</div>

	</div>
