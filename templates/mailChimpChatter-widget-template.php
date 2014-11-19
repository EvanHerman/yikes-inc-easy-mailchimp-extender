<?php
	// template for displaying the MailChimp
	// Chatter Widget on the Dashboard
	
	// loop over the response, to display
	// appripriate messages back to the user
	// style as best we can, similar to MailChimp
	?>
	
<!-- widget specific styles -->	
<style>
	#yks_mc_account_activity_widget .inside {
		padding: 0;
		margin: 0;
	}

	#yks-admin-chimp-chatter , .chatter-content-row {
		width: 100%;
		display: table;
		padding: .5em 0;
	}
	.chimp-chatter-image {
		margin-left: 10px !important;
	}
	
	.chatter-content-row td:nth-child(2), .chatter-content-row td:nth-child(1) {
		width: 65px !important;
		text-align: center;
	}
	
	#yks_mc_account_activity_widget > h3 > span:before {
		content: url('<?php echo plugins_url(); ?>/yikes-inc-easy-mailchimp-extender/images/yikes_logo_widget_icon.png');
		width:33px;
		float:left;
		height:10px;
		margin: -3px 10px 0 0px;
	}
</style>	
	
	
	<div class="yks_mailChimp_Chatter">
	<table id="yks-admin-chimp-chatter">
       <tbody>            
					<?php
						if ( !empty ( $resp ) ) {
							$i = 1;
						
							foreach ( $resp as $chatter ) {
								
								// specify the number of items you want
								// returned to the widget here
								if ( $i > 8 ) {
									break;
								} else {
								
									// get our timezone offset so we
									// can return the proper time to the user
									$timezone_offset = get_option('gmt_offset');
									
									// decide if the row should be even or odd
									// used for zebra striping the table
									if ( $i % 2 == 0 ) {
										echo '<tr class="chatter-table-row chatter-content-row alternate">';
									} else {
										echo '<tr class="chatter-table-row chatter-content-row">';
									}
										// set up the date and time variables
										$update_time_explode = explode( ' ', $chatter['update_time'] );
											$date = $update_time_explode[0];
											$time = $update_time_explode[1];
											$time_explode = explode( ":" , $time );
											$time = ( $time_explode[0] + $timezone_offset ).":".$time_explode[1];
											
										// get the type of action that was recorded
										$type = explode( ':' , $chatter['type']);
										
										// set up the image based on the chatter type
											// Known Possibilities Include -
												// lists:new-subscriber, lists:unsubscribes, lists:profile-updates, campaigns:facebook-likes, 
												// campaigns:facebook-comments, campaigns:forward-to-friend, lists:imports, or campaigns:inbox-inspections
										if ( $type[1] == 'new-subscriber' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/subscriber-added.png' . '" alt="New User Subscribed" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'unsubscribes' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/subscriber-removed.png' . '" alt="User Unsubscribed" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'profile-updates' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/profile-updated.png' . '" alt="Profile Updated" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'facebook-likes' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/facebook-like.png' . '" alt="Facebook Like" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'facebook-comments' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/facebook-comment.png' . '" alt="Facebook Comment" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'forward-to-friend' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/forward-to-friend.png' . '" alt="Forward To Friend" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'imports' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/import.png' . '" alt="Imports" class="chimp-chatter-image" />';
										} else if ( $type[1] == 'inbox-inspections' ) {
											$type_image = '<img src="' . plugins_url().'/yikes-inc-easy-mailchimp-extender/images/ChimpChatter/inbox-inspection.png' . '" alt="Inbox Inspection" class="chimp-chatter-image" />';
										}
										
										// get and store the returned message
										// split the message at ' - ' , which will
										// separate the title and messages into two strings
										$message = explode( ' - ' , $chatter['message'] );
											$message_title = $message[0];
											$message_content = $message[1];
												// visual representation of user action
												echo '<td>'.$type_image . '</td>';
												// date user subscribed
												echo '<td><b>'.date("M d", strtotime($date)).'</b><br /><em>'.date( 'g:i a', strtotime($time) ).'</em></td>';
												// message title of action taken
												echo '<td class="chimp-chatter-message"><strong class="chimp-chatter-message-title">'.$message_title.'</strong>';
									echo '</tr>';
									
								}
								$i++;
							}
						} else {
						?>
							<tr>
								<td style="width:100%;text-align:center !important;">
									<h2 class="no_data_found" style="font-size:1.25em;font-style:italic;"><?php _e( "No recent account activity. Check back again later." , "yikes-inc-easy-mailchimp-extender" ); ?></h2>
								</td>
							</tr>
						<?php
						}
						?>
        </tbody>
     </table>
	</div>