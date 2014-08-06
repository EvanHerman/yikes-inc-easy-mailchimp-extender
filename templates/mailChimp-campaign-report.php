<?php
// get our data from the MailChimp API Response
$campaign = $resp;
// Load Thickbox
add_thickbox();
?>

<style>
.midnight-blue-flat-button {
  position: relative;
  vertical-align: top;
  width: 215px;
  height: 45px;
  padding: 0;
  font-size: 14px;
  color: white;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  background: #2c3e50;
  border: 0;
  border-bottom: 2px solid #22303f;
  cursor: pointer;
  -webkit-box-shadow: inset 0 -2px #22303f;
  box-shadow: inset 0 -2px #22303f;
  width: 215px;
}

.midnight-blue-flat-button:active {
  top: 1px;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.asbestos-flat-button {
  position: relative;
  vertical-align: top;
  width: 150px;
  height: 40px;
  padding: 0;
  font-size: 16px;
  color:white;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  background: #7f8c8d;
  border: 0;
  border-bottom: 2px solid #6d7b7c;
  cursor: pointer;
  -webkit-box-shadow: inset 0 -2px #6d7b7c;
  box-shadow: inset 0 -2px #6d7b7c;
}
.asbestos-flat-button:active {
  top: 1px;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}
*, *:before, *:after {
box-sizing: border-box;
}
</style>

<script type="text/javascript">
jQuery(function() {	

	// append our no print class to the adminmenuwrap
	jQuery('#adminmenuwrap').addClass('yks-mc-no-print');
	jQuery('#adminmenuback').addClass('yks-mc-no-print');
	jQuery('#wpfooter').addClass('yks-mc-no-print');
		
	jQuery('.progressbar').each(function(){
		var t = jQuery(this),
			dataperc = t.attr('data-perc'),
			barperc = Math.round(dataperc*5.56);
		t.find('.bar').animate({width:barperc}, dataperc*25);
		t.find('.label').append('<div class="perc"></div>');
		
		function perc() {
			var length = t.find('.bar').css('width'),
				perc = Math.round(parseInt(length)/5.56),
				labelpos = (parseInt(length)-2);
		}
		perc();
		setInterval(perc, 0); 
	});

});
			
jQuery('.campaign-stats-button').off().on( 'click' , function() {
	// clear our divs
	jQuery('#click_tracking_info').html('');			
	jQuery('#geo_data_link_map').html('');				
	
	// get and store the active parent button,
	// which dictates if we query Growth History Stats
	// or Campaign Stats
	var active_list_button = jQuery('.list_container_for_stats').find('.active_button').text();			
	// AJAX request to get campaign report data
	jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" style="padding-top:4em;" alt="preloader" >');
		 var apiKey = '<?php echo $this->optionVal['api-key']; ?>';  
		   jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'yks_get_campaign_data',
						api_key: apiKey
					},
					dataType: 'html',
					success: function(response) {
						// show the parent buttons
						jQuery('.statistics-sub-title').show();
						// re-add it to the clicked element
						jQuery('.statistics-sub-title.campaign-stats-button').addClass('active-parent-button');
						// reload the All campaigns page
						jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
					}
			});	
			

});


jQuery( '.yks-mc-view-recipients' ).off().on( 'click' , function() {

			var campaign_id = jQuery( '#yks-mc-this-campaign-id' ).val(); 
			 var apiKey = '<?php echo $this->optionVal['api-key']; ?>';  
			   jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'yks_get_campaign_emailed_to',
							api_key: apiKey,
							campaign_id: campaign_id
							// campaign_id: jQuery(this).attr('alt')
						},
						dataType: 'html',
						success: function(response) {
							jQuery('#TB_ajaxContent').html('<div>'+response+'</div>');
						}
				});	

});

/* Clicks Click */
jQuery( '.yks_mc_clicked_data' ).off().on( 'click' , function() {

			// smooth scroll down to click chart
			 jQuery('html, body').animate({
				scrollTop: jQuery("#yks-admin-link-data-table").offset().top - 220
			}, 800);

});

/* Opened Click */
jQuery( '.yks_mc_opened_data' ).off().on( 'click' , function() {

			// ajax request, get data for users who opened this campaign
			// display it in a thickbox popup
			var campaign_id = jQuery( '#yks-mc-this-campaign-id' ).val(); 
			var apiKey = '<?php echo $this->optionVal['api-key']; ?>';   
			   jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'yks_get_campaign_opened_data',
							api_key: apiKey,
							campaign_id: campaign_id
							// campaign_id: jQuery(this).attr('alt')
						},
						dataType: 'html',
						success: function(response) {
							jQuery('#TB_ajaxContent').html(response);
						}
				});	
			
			
});

/* Bounced Click */
jQuery( '.yks_mc_bounced_data' ).off().on( 'click' , function() {

			// ajax request, get data for users who opened this campaign
			// display it in a thickbox popup
			var campaign_id = jQuery( '#yks-mc-this-campaign-id' ).val(); 
			 var apiKey = '<?php echo $this->optionVal['api-key']; ?>';  
			   jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'yks_get_bounced_email_data',
							api_key: apiKey,
							campaign_id: campaign_id
							// campaign_id: jQuery(this).attr('alt')
						},
						dataType: 'html',
						success: function(response) {
							jQuery('#TB_ajaxContent').html(response);
						}
				});	
			
			
});

/* Unsubscribes Click */
jQuery( '.yks_mc_unsubscribe_data' ).off().on( 'click' , function() {

			// ajax request, get data for users who opened this campaign
			// display it in a thickbox popup
			var campaign_id = jQuery( '#yks-mc-this-campaign-id' ).val(); 
			var apiKey = '<?php echo $this->optionVal['api-key']; ?>';   
			   jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'yks_get_unsubscribed_email_data',
							api_key: apiKey,
							campaign_id: campaign_id
							// campaign_id: jQuery(this).attr('alt')
						},
						dataType: 'html',
						success: function(response) {
							jQuery('#TB_ajaxContent').html(response);
						}
				});	
			
			
});


setInterval(function() {
	// re-appending our preloader to hidden thickbox
	if ( jQuery('#TB_window').is(':visible') ) {
		// do nothing
	} else {
		jQuery('#yks_mc_thickbox_data').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader">');
	}	
	
}, 150);
</script>

<?php
	
	$timezone = get_option('timezone_string');
	$web_id = $campaign_web_Id; 
	$open_percentage = round((float)($campaign['unique_opens']/$campaign['emails_sent']) * 100 ) . '%';
	$click_rate_percentage = round((float)($campaign['users_who_clicked']/$campaign['emails_sent']) * 100 ) . '%';
	$campaign_industry = $campaign['industry']['type'];
	$campaign_industry_open_rate = $campaign['industry']['open_rate'];
	$campaign_industry_click_rate = $campaign['industry']['click_rate'];
	
	$campaign_unsubscribes = $campaign['unsubscribes'];
	$total_opens = $campaign['opens'];
	$campaign_forwards = $campaign['forwards'];
	$campaign_abuse_reports = $campaign['abuse_reports'];
	
	// if ( isset ( $campaign['users_who_clicked'] ) && $campaign['users_who_clicked'] != '' ) {
		$campaign_clicks = $campaign['users_who_clicked'];
		$campaign_bounces = $campaign['hard_bounces'] + $campaign['soft_bounces'];
		$campaign_opens = $campaign['unique_opens'];
		if ( $campaign_opens == 0 ) {
			$campaign_clicks_per_unique_open = '0 %';
		} else {
			$campaign_clicks_per_unique_open = round((float)($campaign_clicks/$campaign_opens) * 100 ) . '%';
		}
	/* 
	} else {
		$campaign_clicks = $campaign['users_who_clicked'];
		$campaign_bounces = $campaign['hard_bounces'] + $campaign['soft_bounces'];
		$campaign_opens = $campaign['unique_opens'];
		$campaign_clicks_per_unique_open = round((float)($campaign_clicks/$campaign_opens) * 100 ) . '%';
	}
	*/
	
	$campaign_total_clicks = $campaign['clicks'];
	
	if ( isset ( $campaign['last_open'] ) && $campaign['last_open'] != '' ) {
		$last_opened_explosion = explode( ' ' , $campaign['last_open']);
		$last_opened_date = $last_opened_explosion[0];
		$last_opened_time = $last_opened_explosion[1];
	} else {
		$last_opened_date = 'Campaign Not Yet Opened';
		$last_opened_time = '';
	}
	
	if ( isset ( $campaign['last_click'] ) && $campaign['last_click'] != '' ) {
		$last_clicked_explosion = explode( ' ' , $campaign['last_click']);
		$last_clicked_date = $last_clicked_explosion[0];
		$last_clicked_time = $last_clicked_explosion[1];
	} else {
		$last_clicked_date = 'Campaign Not Yet Clicked';
		$last_clicked_time = '';
	}
	
	$campaign_successful_delivieries = $campaign['emails_sent'] - $campaign_bounces;
	
	$delivery_percentages = round((float)( $campaign_successful_delivieries / $campaign['emails_sent'] ) * 100 ) . '%';
	
	$industry_open_rate = round((float)$campaign['industry']['open_rate'] * 100 ) . '%';
	$industry_click_rate = round((float)$campaign['industry']['click_rate'] * 100 ) . '%';
	
	$campaign_24hr_time_data = $campaign['timeseries']
?>
<section class="overview_information_section" style="float:right;width:auto;">
	<a href="#" onclick="return false;" class="campaign-stats-button yks-mc-no-print" title="<?php _e( 'Back to Campaigns' , 'yikes-inc-easy-mailchimp-extender' ); ?>" >
		<input type="button" href="#" onclick="return false;" class="midnight-blue-flat-button" value="<?php _e( 'Back to Campaigns' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
	</a>
</section>

<!-- 
	thickbox data for opened, bounced and unsubscribed data 
	populated via ajax
-->
<div id="yks_mc_thickbox_data" style="display:none;"></div>

<input type="hidden" id="yks-mc-this-campaign-id" value="<?php echo $campaign_id; ?>">

<div id="yks-mc-specific-campaign-report">

<h2><?php  _e('Campaign Statistics Report', 'yikes-inc-easy-mailchimp-extender'); ?></h2>

<h2><?php echo $campaign_title; ?></h2>

<!-- open tracking disabled warning -->
<div id="open_tracking_disabled_warning">
	<div class="dashicons dashicons-info"></div>
	<p><strong><?php  _e('Open tracking was disabled on this campaign', 'yikes-inc-easy-mailchimp-extender'); ?></strong></p>
	<p><?php  _e('You did not enable open tracking for this campaign, so the data in this report will be incomplete.', 'yikes-inc-easy-mailchimp-extender'); ?> <a href="http://kb.mailchimp.com/article/about-open-tracking?t=1403809717&v=170&enc=bffd7faa880038136e94f98b489a89a29b02f86fcfd1aafd1616b2ad4695fff3" target="_blank"><?php  _e('about open tracking', 'yikes-inc-easy-mailchimp-extender'); ?></a>.</p>
</div>
	
	<hr />
	<hr />
	
	<h2><?php  _e('Overview', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
	<h3 class="yks-campaign-report-recipients"><a href="#TB_inline?width=975&height=650&inlineId=yks-campaign-report-email-recipients-table" onclick="return false;" class="yks-mc-view-recipients thickbox"><?php echo $campaign['emails_sent']; ?></a> <?php  _e('Recipients', 'yikes-inc-easy-mailchimp-extender'); ?></h3>
	
	<div id="yks-campaign-report-email-recipients-table" class="yks-mc-no-print" style="display:none;">
		<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" style="padding-top:4em;" alt="preloader" >
	</div>
	
	
	<div class="overview-info-container">
	
		<!-- Display our buttons to let the user print/download/view email of this specific report -->
		<span class="overview_information">
			<strong><?php  _e('Subject', 'yikes-inc-easy-mailchimp-extender'); ?> </strong> <?php echo $campaign_email_subject; ?><br />
			<strong><?php  _e('Delivered', 'yikes-inc-easy-mailchimp-extender'); ?> </strong> <?php echo date( "D, M j, Y" , strtotime($campaign_send_date) ) . ' ' . date( "g:i a" , strtotime($campaign_send_time) ); ?><br />
		</span>
		
			<!-- Display our buttons to let the user print/download/view email of this specific report -->
			<span class="overview_information" style="min-height:64px;text-align:right;float:right;">
				<a href="<?php echo $campaign_view_email_link; ?>" class="thickbox" onclick="return false;"><input type="button" href="#" onclick="return false;" class="asbestos-flat-button yks-mc-no-print" value="<?php _e( 'View Email' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></a>
				<a onclick="window.open('https://us3.admin.mailchimp.com/reports/excel?id=<?php echo $web_id; ?>');"><input type="button" href="#" onclick="return false;" class="asbestos-flat-button yks-mc-no-print" value="<?php _e( 'Download Report' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></a>
				<input type="button" href="#" onclick="window.print();" class="asbestos-flat-button yks-mc-no-print" value="<?php _e( 'Print Report' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></a>
			</span>
			
		
		<section class="overview_information_section">
		
			<!-- Display our buttons to let the user print/download/view email of this specific report -->
			<span class="overview_information overview_information_right">
				<span class="info_overview_avg_tag"><h3><?php  _e('Open Rate', 'yikes-inc-easy-mailchimp-extender'); ?> <strong class="info_overview_percentage"><?php echo $open_percentage; ?></strong></h3></span>
				<!-- progress bar -->
				<div class="progress-bar green stripes progress-bar-open-rate yks-mc-no-print">
					<span style="width: <?php echo $open_percentage; ?>"></span>
				</div>
				<!-- <span class="info_overview_avg_tag">List avg	<strong class="info_overview_percentage"></strong></span> -->
				<span class="info_overview_avg_tag"><?php  _e('Industry Avg', 'yikes-inc-easy-mailchimp-extender'); ?> (<?php echo $campaign_industry; ?>) <strong class="info_overview_percentage"><?php echo $industry_open_rate; ?></strong></span>
			</span>
			
				<!-- Display our buttons to let the user print/download/view email of this specific report -->
				<span class="overview_information">
					<span class="info_overview_avg_tag"><h3><?php  _e('Click Rate', 'yikes-inc-easy-mailchimp-extender'); ?> <strong class="info_overview_percentage"><?php echo $click_rate_percentage; ?></strong></h3></span>
					<!-- progress bar -->
					<div class="progress-bar green stripes shine progress-bar-click-rate yks-mc-no-print">
						<span style="width: <?php echo $click_rate_percentage; ?>"></span>
					</div>
					<div class="progressbar-click-rate"><div class="progress-click-rate" ><div class="pbaranim-click-rate"></div></div></div>
					<!-- <span class="info_overview_avg_tag">List avg	<strong class="info_overview_percentage"></strong></span> -->
					<span class="info_overview_avg_tag"><?php  _e('Industry Avg', 'yikes-inc-easy-mailchimp-extender'); ?> (<?php echo $campaign_industry; ?>) <strong class="info_overview_percentage"><?php echo $industry_click_rate; ?></strong></span>
				</span>
				
				<div id="opened_clicked_bounced_unsubscribed">
					<span class="centered_container">
						<a href="#TB_inline?width=600&height=550&inlineId=yks_mc_thickbox_data" class="thickbox yks_mc_opened_data">
							<span class="yks_mc_ocbu">
								<p class="yks_mc_ocbu_number"><?php echo $campaign_opens; ?></p>
								<strong><?php  _e('Opened', 'yikes-inc-easy-mailchimp-extender'); ?></strong>
							</span>
						</a>
						<span class="yks_mc_ocbu yks_mc_clicked_data">
							<p class="yks_mc_ocbu_number"><?php echo $campaign_clicks; ?></p>
							<strong><?php  _e('Clicked', 'yikes-inc-easy-mailchimp-extender'); ?></strong>
						</span>
						<a href="#TB_inline?width=600&height=550&inlineId=yks_mc_thickbox_data" class="thickbox yks_mc_bounced_data">
							<span class="yks_mc_ocbu yks_mc_bounced_data">
								<p class="yks_mc_ocbu_number"><?php echo $campaign_bounces; ?></p>
								<strong><?php  _e('Bounced', 'yikes-inc-easy-mailchimp-extender'); ?></strong>
							</span>
						</a>
						<a href="#TB_inline?width=600&height=550&inlineId=yks_mc_thickbox_data" class="thickbox yks_mc_unsubscribe_data">
							<span class="yks_mc_ocbu yks_mc_unsubscribed_data">
								<p class="yks_mc_ocbu_number"><?php echo $campaign_unsubscribes; ?></p>
								<strong><?php  _e('Unsubscribed', 'yikes-inc-easy-mailchimp-extender'); ?></strong>
							</span>
						</a>
					</span>
				</div>
				
				
				<span class="overview_information">
					<strong><?php  _e('Successful deliveries', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $campaign_successful_delivieries; ?> <span class="overview_percentage" style="font-size:15px;"> (<?php echo $delivery_percentages; ?>) </span><br />
					<strong><?php  _e('Total opens', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $total_opens; ?><br />
					<strong><?php  _e('Last opened', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php if ( isset ( $campaign['last_open'] ) && $campaign['last_open'] != '' ) { echo date( "m/d/y" , strtotime($last_opened_date) ) . ' at ' . date( "g:i a" , strtotime($last_opened_time) ); } else { echo 'Campaign Not Yet Opened'; } ?><br />
					<strong><?php  _e('Forwarded', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $campaign_forwards; ?><br />			
				</span>
				
				<span class="overview_information">
					<strong><?php  _e('Clicks per unique opens', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $campaign_clicks_per_unique_open; ?><br />
					<strong><?php  _e('Total clicks', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $campaign_total_clicks; ?><br />
					<strong><?php  _e('Last clicked', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php if ( isset ( $campaign['last_click'] ) && $campaign['last_click'] != '' ) { echo date( "m/d/y" , strtotime($last_clicked_date) ) . ' at ' . date( "g:i a" , strtotime($last_clicked_time) ); } else { echo 'No Links Clicked Yet'; } ?><br />
					<strong><?php  _e('Abuse reports', 'yikes-inc-easy-mailchimp-extender'); ?></strong> <?php echo $campaign_abuse_reports; ?><br />			
				</span>
			
		</section>
		
		<section class="overview_information_section">
		
			<h2><?php  _e('Campaign Link Performance', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
			<div class="overview_information" style="width:100%;">
				<?php
					// create an array of our returned data,
					// used in the chart
					$campaign_chart_time_array = array();
					$campaign_chart_unique_opens_array = array();
					$campaign_chart_unique_clicks_array = array();
					
					// print_r($campaign_24hr_time_data);
					foreach ( $campaign_24hr_time_data as $time_data ) {
						$time_explosion = explode( ' ' , $time_data['timestamp'] );
							array_push( $campaign_chart_time_array , date( 'g:i a' , strtotime( $time_explosion[1] ) ) );
							array_push( $campaign_chart_unique_opens_array , $time_data['unique_opens'] );
							array_push( $campaign_chart_unique_clicks_array, $time_data['recipients_click'] );
					}
										
					// print_r($campaign_chart_time_array);
					// print_r($campaign_chart_unique_opens_array);
					// print_r($campaign_chart_unique_clicks_array);
				?>

				<!-- 24 hour performance chart javascript -->
				<script type="text/javascript">			
					jQuery(window).on( 'scroll' , allInView);

					function isScrolledIntoView(elem) {
						var docViewTop = jQuery(window).scrollTop();
						var docViewBottom = docViewTop + jQuery(window).height();

						var elemTop = jQuery(elem).offset().top;
						var elemBottom = elemTop + jQuery(elem).height();

						return (elemBottom <= docViewBottom);
					}

					function allInView() {
									
						// function gets fired many times,
						// need to fire function one time
						// prevents function from running 
						// when the element doesn't exist 
						// (prevents errors from being thrown)
						if ( jQuery('#yks-admin-campaign-stats-table').is(':visible') ) {
							return false;
						} else if ( jQuery( '.mailChimp_get_subscribers_preloader' ).is(':visible') ) {
							return false;
						} else {
							if (isScrolledIntoView(jQuery("#24hr_line_chart"))) {	
								if ( jQuery('#24hr_line_chart').is(':empty') ) { 
									jQuery('#24hr_line_chart').stop().animate({
										height: '400px'
									}, 600, function() {
										jQuery('#24hr_line_chart').highcharts({
											chart: {
												type: 'line'
											},
											title: {
												text: "<?php  _e('24 Hour Campaign Performance', 'yikes-inc-easy-mailchimp-extender'); ?>"
											},
											subtitle: {
												text: "<?php  _e('begins from the time your campaign is sent', 'yikes-inc-easy-mailchimp-extender'); ?>"
											},
											xAxis: {
												// time array
												categories: [<?php echo '"' . implode( ' ", "' , $campaign_chart_time_array ) .'"'; ?>]
											},
											yAxis: {
												title: {
													text: "<?php  _e('Number of Opens/Clicks', 'yikes-inc-easy-mailchimp-extender'); ?>"
												},
												min: 0,
											},
											plotOptions: {
												line: {
													dataLabels: {
														enabled: true
													},
													enableMouseTracking: true
												}
											},
											credits: {
												enabled: false
											  },
											series: [{
												name:  "<?php  _e('Unique Opens', 'yikes-inc-easy-mailchimp-extender'); ?>",
												data: [<?php echo implode( ', ' , $campaign_chart_unique_opens_array ); ?>]
											}, {
												name:  "<?php  _e('Unique Clicks', 'yikes-inc-easy-mailchimp-extender'); ?>",
												data: [<?php echo implode( ', ' , $campaign_chart_unique_clicks_array ); ?>]
											}]
										});
									});	
								} 
							}
						}
					}	
				</script>

				<div id="24hr_line_chart" style="min-width: 100%; margin: 0 auto"></div>

			</div>
			

	
		</section>
		
		
			
	</div>
	


<strong></strong>

</div>
