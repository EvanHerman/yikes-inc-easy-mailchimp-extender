<?php 
	// lets confirm the user has a valid API key stored
	if( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
		// storethe API key
		$api_key = get_option( 'yikes-mc-api-key' , '' );
		// initialize MailChimp API
		$MailChimp = new YIKES_MAILCHIMP_API( $api_key );
		if ( false === ( $account_details = get_transient( 'yikes-easy-mailchimp-account-data' ) ) ) {
			// retreive our list data
			$account_details =  $MailChimp->call( '/' );
			// set our transient for one hour
			set_transient( 'yikes-easy-mailchimp-account-data', $account_details, 1 * HOUR_IN_SECONDS );
		}		
		if ( false === ( $account_activity = get_transient( 'yikes-easy-mailchimp-account-activity' ) ) ) {
			// retreive our list data
			$account_activity = $MailChimp->call( 'helper/chimp-chatter' );
			// set our transient for one hour
			set_transient( 'yikes-easy-mailchimp-account-activity', $account_activity, 1 * HOUR_IN_SECONDS );
		}
	} else {
		wp_die( __( 'It looks like you need to re-validate your MailChimp API key before you can continue.' , 'yikes-inc-easy-mailchimp-extender' ), 500 );
	}
?>
<div class="wrap" id="account-details">
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />
	<h1><?php _e( 'Account Overview' , 'yikes-inc-easy-mailchimp-extender' ); echo ' | ' . $account_details->account_name; ?></h1>		
	<!-- Account Overview Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( "Below you'll find a brief overview of account activity as well as some account and profile info." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<h1><?php _e( 'Chimp Chatter' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">

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
							<h2 class="account-status"><?php if( $account_details->pro_enabled == 1 ) { ?><div class="circle-account-active" title="<?php _e( "Pro Account" , 'yikes-inc-easy-mailchimp-extender' ); ?>"></div><?php } else { ?><div class="circle-account-inactive" title="<?php _e( "Free Account" , 'yikes-inc-easy-mailchimp-extender' ); ?>"></div><?php } echo $account_details->account_name; ?> <small>(<?php echo $account_details->role; ?>)</small></h2>
							
							<table class="form-table" id="account-details-table">
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Company' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo $account_details->contact->company; ?><br /><?php echo $account_details->contact->city . ', ' . $account_details->contact->state; ?></td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Last Login' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo date( get_option( 'date_format' ), strtotime( $account_details->last_login ) ) . ' ' . __( 'at', 'yikes-inc-easy-mailchimp-extender' ) . ' ' . date( get_option( 'time_format' ), strtotime( $account_details->last_login ) ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label for="tablecell">
											<strong><?php _e( 'Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
										</label>
									</td>
									<td><?php echo number_format( $account_details->total_subscribers ); ?></td>
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