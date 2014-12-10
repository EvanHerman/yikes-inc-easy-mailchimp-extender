<!-- 
	*
		Translation for Clicks , Click , Opens, and Opens need to be re-done 
	*
-->
<script type="text/javascript">
// switch out opens, views etc on hover
jQuery(document).ready(function() {

	jQuery('.yks_campaign_report_click_data').mouseenter(function() {
		jQuery(this).find('.original_view').hide();
		jQuery(this).find('.hover_view').show();
	});
	jQuery('.yks_campaign_report_click_data').mouseleave(function() {
		jQuery(this).find('.original_view').show();
		jQuery(this).find('.hover_view').hide();
	});
	
	// display the rss feed children
	jQuery('.rss_parent_button').click(function() {
		
		var campaign_id = jQuery(this).attr('alt');		
		jQuery('.'+campaign_id+'_child_report_row').stop().fadeToggle();
		jQuery('.'+campaign_id+'_child_report_row').next().stop().fadeToggle();

		return false;
	});
	
	
});
</script>
<?php
	// print campaign data for testing purposes
	// print_r($resp);
	echo '<h2>'.__('Previous Campaigns' , 'yikes-inc-easy-mailchimp-extender' ). '</h2>';
	$campaign_data = $resp['data'];
	$campaign_children_array = array();
	$timezone_offset = get_option('gmt_offset');
	echo '<hr />';

	// print_r($campaign_data);
	
	// view link thickbox url
	// echo $campaign['archive_url'];   class="thickbox
	?>
	<table id="yks-admin-campaign-stats-table">
       <tbody>  
	 <?php
	// loop over campaign data, and display it much in the same way MailChimp
	if ( !empty( $campaign_data ) ) {
		
		$child_campaign_array = array();
	
		foreach ( $campaign_data as $campaign ) {

				$campaign_type = $campaign['type'];
				
				$campaign_type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/stats-icons/'.$campaign_type.'_icon.png' . '" alt="'.$campaign_type.'" class="'.$campaign_type.' image" />';
				
				$campaign_id = $campaign['id'];
				$preview_campaign_link = $campaign['archive_url'];
				$total_emails_sent = $campaign['emails_sent'];
				$campaign_email_subject = $campaign['subject'];
				if ( isset($campaign['send_time'] ) && $campaign['send_time'] != '' ) {
					$send_time_explosion = explode( " " , $campaign['send_time'] );
						$send_time_explode = explode( ":" , $send_time_explosion[1] );
						$campaign_send_time = ( $send_time_explode[0] + $timezone_offset ).':'.$send_time_explode[1];
						$campaign_send_date = $send_time_explosion[0];
						$campaign_unique_opens = $campaign['summary']['unique_opens'];
						$campaign_clicks = $campaign['summary']['users_who_clicked'];
						$campaign_web_id = $campaign['web_id'];
						// prevent division by 0 errors thrown when
						// $total_emails_sent is equal to 0
						if ( $total_emails_sent == 0 ) {
							$campaign_open_percentage = '0%';
							$user_click_percentage = '0%';
						} else {
							$campaign_open_percentage = round((float)($campaign_unique_opens/$total_emails_sent) * 100 ) . '%';
							$user_click_percentage = round((float)($campaign_clicks/$total_emails_sent) * 100 ) . '%';
						}
				} else {
					$campaign_send_time = '';
					$campaign_send_date = '<strong>Not Sent</strong>';
					$campaign_unique_opens = '0';
					$campaign_clicks = '0';
					$campaign_web_id = '-';
					$campaign_open_percentage ='0 %';
					$user_click_percentage = '0 %';
				}
			
			if ( $campaign['is_child'] != 1 ) {
				?>
					<tr class="single_report_row <?php if ( $campaign_type == 'rss' ) { ?>rss_single_report_row <?php } ?>">
						<td <?php if ( !isset ( $campaign['send_time'] ) && !isset( $campaign['type_opts']['last_sent'] ) || $campaign['status'] == 'paused' ) {	?> style="opacity:.25;" <?php } ?>>
							<?php echo $campaign_type_image; ?>
						</td>
						<!-- column 1 -->
						<td>
							<?php if ( isset ( $campaign['send_time'] ) ) { ?>
								<a class="yks_campaign_report_link yks-mc-view-report-button" href="#" onclick="return false;" alt="<?php echo $campaign_id; ?>" title="<?php echo $campaign['title']; ?>"><?php echo $campaign['title']; ?></a>
							<?php } else {
								 echo '<span class="yks_campaign_report_disabled_link">'.$campaign['title'].'</span>';
							} ?>
							<div class="yks_mc_campaign_sent_time_container">
								<?php 
								if ( isset ( $campaign['send_time'] ) ) {	
									echo '<b>'.__("Sent" , "yikes-inc-easy-mailchimp-extender" ).'</b> '.__("on " , "yikes-inc-easy-mailchimp-extender" ).date( 'D, ' , strtotime($campaign_send_date)).date( 'M j, Y' , strtotime($campaign_send_date)) .__( " at " , "yikes-inc-easy-mailchimp-extender" ) . date( 'g:i a' , strtotime($campaign_send_time) );
								} else {
									if ( $campaign['status'] == 'sending' && isset( $campaign['type_opts']['last_sent'] ) ) {
										echo '<b>'.__( "Recurring Campaign" , "yikes-inc-easy-mailchimp-extender" ).'</b>';
									} else if ( $campaign['status'] == 'paused' ) {
										echo '<b>'.__( "Paused" , "yikes-inc-easy-mailchimp-extender" ).'</b>';
									} else {
										echo '<b>'.__( "Not Yet Sent" , "yikes-inc-easy-mailchimp-extender" ).'</b>';
									}								
								} 
								?>
							</div>
						</td>
						<!-- column 2 -->
						<td>
						<?php if ( $campaign_type != 'rss' ) { ?>
							<div class="yks_campaign_report_click_data" <?php if ( $campaign_type != 'rss' && !isset( $campaign['send_time'] ) || $campaign_type == 'rss' && $campaign['type_opts']['last_sent'] == '' ) { ?> style="opacity:.55;" <?php } ?> >
								<span class="campaign_summary_data"><?php echo $total_emails_sent . '<br />'; if ( $total_emails_sent == 1 ) {  echo __( ' Subscriber' , 'yikes-inc-easy-mailchimp-extender' ); } else { echo __( ' Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); } ?></span>
								<span class="campaign_summary_data"><span class="original_view"><?php echo $campaign_unique_opens . ' <br />'; if ( $campaign_unique_opens == 1 ) { echo __( ' Open', 'yikes-inc-easy-mailchimp-extender' ); } else { echo __( ' Opens' , 'yikes-inc-easy-mailchimp-extender' ); } ?></span><span class="hover_view"><?php echo $campaign_open_percentage  . ' <br />'.__(' Opens' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
								<span class="campaign_summary_data"><span class="original_view"><?php echo $campaign_clicks . ' <br />'; if ( $campaign_clicks == 1 ) { echo __( ' Click', 'yikes-inc-easy-mailchimp-extender' ); } else { echo __( ' Clicks', 'yikes-inc-easy-mailchimp-extender' ); } ?></span><span class="hover_view"><?php echo $user_click_percentage . ' <br />'.__( ' Clicks', 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
							</div>
						<?php } else { ?>
							<div class="yks_campaign_report_click_data" <?php if ( $campaign_type != 'rss' && !isset( $campaign['send_time'] ) || $campaign_type == 'rss' && $campaign['type_opts']['last_sent'] == '' ) { ?> style="opacity:.55;" <?php } ?> >
								<span class="campaign_summary_data <?php echo $campaign['id']; ?>_total_campaign_sent"><?php echo $total_emails_sent . ' <br />'.__( ' Campaigns Sent', 'yikes-inc-easy-mailchimp-extender' ); ?></span>
								<span class="campaign_summary_data <?php echo $campaign['id']; ?>_total_campaign_opened"><span class=""><?php echo $campaign_unique_opens . ' <br />'.__( ' Opens', 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
								<span class="campaign_summary_data <?php echo $campaign['id']; ?>_total_campaign_clicked"><span class=""><?php echo $campaign_clicks . ' <br />' .__( ' Clicks', 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
							</div>
						<?php } ?>
						</td>
						<!-- column 3 // view report -->
						<td>
							<?php if ( isset ( $campaign['send_time'] ) ) { ?>
								<input type="submit" value="View Report" alt="<?php echo $campaign_id; ?>" title="<?php echo $campaign['title']; ?>" class="yks-mc-view-report-button green-flat-button">
							<?php } else if ( !isset ( $campaign['send_time'] ) && $campaign_type == 'rss' && $campaign['type_opts']['last_sent'] != '' ) { ?>
								<input  type="submit" value="RSS" alt="<?php echo $campaign_id; ?>" title="<?php echo $campaign['title']; ?>" class="yks-mc-view-report-button orange-flat-button rss_parent_button">
							<?php } else { ?>
								<input disabled="disabled" type="submit" value="<?php _e( 'Not Yet Sent', 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php echo $campaign_id; ?>" title="<?php echo $campaign['title']; ?>" class="yks-mc-view-report-button green-flat-button">
							<?php } ?>
							<input type="hidden" class="yks-mc-campaign-email-subject" value="<?php echo $campaign_email_subject; ?>" />
							<input type="hidden" class="yks-mc-campaign-send-time" value="<?php echo $campaign_send_time; ?>" />
							<input type="hidden" class="yks-mc-campaign-send-date" value="<?php echo $campaign_send_date; ?>" />
							<input type="hidden" class="yks-mc-campaign-web-id" value="<?php echo $campaign_web_id; ?>" />
							<input type="hidden" class="yks-mc-campaign-view-email-link" value="<?php echo $preview_campaign_link; ?>?TB_iframe=true&width=900&height=650" />
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				<?php
				// place all our child elements below the parent, 
				// so we can use jQuery expand+collapse
					// to do
						// fix child campaign id's, sent time, send date etc. etc.
						// re-create the variables inside this foreach loop and condiitonal
						// nest, and hide children
						// update RSS campaign button to expand elements below etc.
				if ( !empty( $child_campaign_array ) ) {
									
					foreach ($child_campaign_array as $child_campaign ) {
					
						if ( $child_campaign['parent_id'] == $campaign['id'] ) {										
							$send_time_explosion = explode( " " , $child_campaign['send_time'] );
							$campaign_send_time = $send_time_explosion[1];
							
							$send_time_explode = explode( ":" , $campaign_send_time );
							$campaign_send_time = ( $send_time_explode[0] + $timezone_offset ).':'.$send_time_explode[1];

							$campaign_send_date = $send_time_explosion[0];
							$total_emails_sent = $child_campaign['emails_sent'];
							$campaign_unique_opens = $child_campaign['summary']['unique_opens'];
							$campaign_clicks = $child_campaign['summary']['users_who_clicked'];
							$campaign_web_id = $child_campaign['web_id'];
							// prevent division by 0 errors thrown when
							// $total_emails_sent is equal to 0
							if ( $total_emails_sent == 0 ) {
								$campaign_open_percentage = '0%';
								$user_click_percentage = '0%';
							} else {
								$campaign_open_percentage = round((float)($campaign_unique_opens/$total_emails_sent) * 100 ) . '%';
								$user_click_percentage = round((float)($campaign_clicks/$total_emails_sent) * 100 ) . '%';
							}
							?>
								<tr class="single_report_row <?php if ( $child_campaign['is_child'] == 1 ) { echo $child_campaign['parent_id'].'_child_report_row yks_mc_child_report'; } ?>">
									<td style="text-align:center;">
										<div class="dashicons dashicons-minus"></div>
									</td>
									<!-- column 1 -->
									<td>
										<?php if ( isset ( $child_campaign['send_time'] ) ) { ?>
											<a class="yks_campaign_report_link yks-mc-view-report-button" href="#" onclick="return false;" alt="<?php echo $child_campaign['id']; ?>" title="<?php echo $child_campaign['title']; ?>"><?php echo '--'.$child_campaign['title']; ?></a>
										<?php } else {
											 echo '<span class="yks_campaign_report_disabled_link">'.$child_campaign['title'].'</span>';
										} ?>
										<div class="yks_mc_campaign_sent_time_container">
											<?php 
											if ( isset ( $child_campaign['send_time'] ) ) {	
												echo '<b>' . __( 'Sent', 'yikes-inc-easy-mailchimp-extender' ) . '</b> on '.date( 'D, ' , strtotime($campaign_send_date)).date( 'M j, Y' , strtotime($campaign_send_date)) . ' at ' . date( 'g:i a' , strtotime($campaign_send_time) );
											} else {
												if ( $child_campaign['status'] == 'sending' ) {
													echo '<b>' . __( 'Recurring Campaign', 'yikes-inc-easy-mailchimp-extender' ) . '</b>';
												} else {
													echo '<b>' . __( 'Not Yet Sent', 'yikes-inc-easy-mailchimp-extender' ) . '</b>';
												}								
											} 
											?>
										</div>
									</td>
									<!-- column 2 -->
									<td>
										<div class="yks_campaign_report_click_data" <?php if ( !isset ( $child_campaign['send_time'] ) ) { ?> style="opacity:.55;" <?php } ?> >
											<span class="campaign_summary_data"><?php echo $total_emails_sent . '<br />'; if ( $total_emails_sent == 1 ) {  echo __( ' Subscriber', 'yikes-inc-easy-mailchimp-extender' ); } else { echo  __( ' Subscribers', 'yikes-inc-easy-mailchimp-extender' ); } ?></span>
											<span class="campaign_summary_data"><span class="original_view"><?php echo $campaign_unique_opens . ' <br />'; if ( $campaign_unique_opens == 1 ) { echo __( ' Open', 'yikes-inc-easy-mailchimp-extender' ); } else { echo __( ' Opens', 'yikes-inc-easy-mailchimp-extender' ); } ?></span><span class="hover_view"><?php echo $campaign_open_percentage  . ' <br />' . __ ( ' Opens', 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
											<span class="campaign_summary_data"><span class="original_view"><?php echo $campaign_clicks . ' <br />'; if ( $campaign_clicks == 1 ) { echo __( ' Click', 'yikes-inc-easy-mailchimp-extender' ); } else { echo __( ' Clicks' , 'yikes-inc-easy-mailchimp-extender' ); } ?></span><span class="hover_view"><?php echo $user_click_percentage . ' <br />'. __ ( ' Clicks', 'yikes-inc-easy-mailchimp-extender' ); ?></span></span>
										</div>
									</td>
									<!-- column 3 // view report -->
									<td>
										<?php if ( isset ( $child_campaign['send_time'] ) ) { ?>
											<input type="submit" value="View Report" alt="<?php echo $child_campaign['id']; ?>" title="<?php echo $child_campaign['title']; ?>" class="yks-mc-view-report-button green-flat-button">
										<?php } else if ( !isset ( $child_campaign['send_time'] ) && $campaign_type == 'rss' ) { ?>
											<input disabled="disabled" type="submit" value="RSS" alt="<?php echo $campaign_id; ?>" title="<?php echo $child_campaign['title']; ?>" class="yks-mc-view-report-button green-flat-button">
										<?php } else { ?>
											<input disabled="disabled" type="submit" value="<?php _e( 'Not Yet Sent', 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php echo $campaign_id; ?>" title="<?php echo $child_campaign['title']; ?>" class="yks-mc-view-report-button green-flat-button">
										<?php } ?>
										<input type="hidden" class="yks-mc-campaign-email-subject" value="<?php echo $campaign_email_subject; ?>" />
										<input type="hidden" class="yks-mc-campaign-send-time" value="<?php echo $campaign_send_time; ?>" />
										<input type="hidden" class="yks-mc-campaign-send-date" value="<?php echo $campaign_send_date; ?>" />
										<input type="hidden" class="yks-mc-campaign-web-id" value="<?php echo $campaign_web_id; ?>" />
										<input type="hidden" class="yks-mc-campaign-view-email-link" value="<?php echo $preview_campaign_link; ?>?TB_iframe=true&width=900&height=650" />
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?php
						}
					}
				}
			} else {
				// push the child campaign into an array
				$child_campaign_array[] = $campaign;
			}
		}
	// if the user hasn't sent any campaigns before
	} else {
	?>
			<tr class="single_report_row">
				<td style="width:100%;text-align:center !important;">
					<h2 class="no_data_found"><?php _e( "It looks like you haven't sent any campaigns yet." , "yikes-inc-easy-mailchimp-extender" ); ?></h2>
				</td>
			</tr>
	<?php
	}
	?>
		</tbody>  
	</table>
	
	<!-- jQuery script to count and populate the necessary parent fields for RSS feed campaigns -->
	<script>
		jQuery(document).ready(function() {
			
			var i = 0;
			
			jQuery('.rss_single_report_row').each(function() {
			
				var campaign_id = jQuery(this).find('.yks-mc-view-report-button').attr('alt');
				var campaign_length = jQuery('.'+campaign_id+'_child_report_row').length;
				
				// set the number of sent campaigns
				jQuery('.'+campaign_id+'_total_campaign_sent').html(campaign_length+'<br /> <?php _e("Campaigns Sent", "yikes-inc-easy-mailchimp-extender" ); ?>');
				
					var opens_sum = [];
					
					var clicks_sum = [];

					// loop over the children and add up the total number of opens and clicks
					jQuery('.'+campaign_id+'_child_report_row').each(function() {
						
						var total_opens = jQuery(this).find('.yks_campaign_report_click_data').find('span:nth-child(2)').find('.original_view').text().replace( '<?php _e(" Opens" , "yikes-inc-easy-mailchimp-extender"); ?>' , '' ).replace( '<?php _e(" Open" , "yikes-inc-easy-mailchimp-extender"); ?>' , '' );
						var total_clicks = jQuery(this).find('.yks_campaign_report_click_data').find('span:nth-child(3)').find('.original_view').text().replace( '<?php _e(" Clicks" , "yikes-inc-easy-mailchimp-extender"); ?>' , '' ).replace( '<?php _e(" Click" , "yikes-inc-easy-mailchimp-extender"); ?>' , '' );
						
						// testing stored variables
						/*
							console.log('The Campaign ID is : '+campaign_id+' and this childs open count is..... '+total_opens);
						
							console.log('The Campaign ID is : '+campaign_id+' and this childs click count is..... '+total_clicks);
						*/
						
						opens_sum.push(total_opens);
						
						clicks_sum.push(total_clicks);
						
					});
					
					/* 
					console.log('The opens sum array is : '+opens_sum);
					console.log('The clicks sum array is : '+clicks_sum);
					*/
					
					// calculating our total RSS campaign opens, 
					// for all campaigns sent
					var campaign_total_opens = 0;
					for (var i = 0; i < opens_sum.length; i++) {
						campaign_total_opens += opens_sum[i] << 0;
					}
					
					// calculating our total RSS campaign clicks, 
					// for all campaigns sent
					var campaign_total_clicks = 0;
					for (var i = 0; i < clicks_sum.length; i++) {
						campaign_total_clicks += clicks_sum[i] << 0;
					}
	
	
					// set the number of sent campaigns
					jQuery('.'+campaign_id+'_total_campaign_opened').html(campaign_total_opens+'<br /><?php _e( "Total Opens" , "yikes-inc-easy-mailchimp-extender" ); ?>');
					jQuery('.'+campaign_id+'_total_campaign_clicked').html(campaign_total_clicks+'<br /><?php _e( "Total Clicks" , "yikes-inc-easy-mailchimp-extender" ); ?>');
					// console.log(value['total']);
						
				// set the number of total campaign opens
				
				// set the number of total campaign clicks
				i++;
			});
			
		});
	</script>