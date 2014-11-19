<?php 
	
	// setup the Data Variables to apply to our chart
	// build our arrays
	$data_array = array();
	$month_array = array();
	$total_subscriber_array = array();
	$new_subscriber_array = array();
	$optin_subscriber_array = array();
	
	// loop over and build month array using
	// $data_response['month']
	/*
	foreach ( $resp as $data_response ) {
		array_push( $month_array , date( 'M Y' , strtotime( $data_response['month'] ) ) );
	}
	*/
	// loop over and build the existing subscriber count array
	// using $data_response['existing'] 
	
	$i = 0;
	foreach ( $resp as $data_response ) {
		$data_array[$i]['month'] = strtotime( $data_response['month'] );
		$data_array[$i]['existing'] = $data_response['existing'];
		$data_array[$i]['imports'] = $data_response['imports'];
		$data_array[$i]['optins'] = $data_response['optins'];
		/*
		array_push( $total_subscriber_array , $data_response['existing'] );
		array_push( $new_subscriber_array , $data_response['imports'] );
		array_push( $optin_subscriber_array , $data_response['optins'] );
		*/
		$i++;
	}
	
	// sort the array based on the date...
	// should be earliest to latest
	// asort( $month_strtotime_array );
	
	array_multisort( $data_array , SORT_ASC );
	
	foreach ( $data_array as $mc_list_data ) {
		$month_array[] = date( 'F Y' , $mc_list_data['month']);
		$total_subscriber_array[] = $mc_list_data['existing'];
		$new_subscriber_array[] = $mc_list_data['imports'];
		$optin_subscriber_array[] = $mc_list_data['optins'];
	}


	
		// print_r($month_array);
		if ( !empty( $resp ) ) { 
			?>	
			<script type="text/javascript">
					// here is our ajax function to reload
					// and refresh the stats div, with the new 
					// list ID that we have selected
					
					
				jQuery(document).ready(function () {			
					
					// clicking on a stats name...
					jQuery('.stats_list_name').off().on( 'click' , function() {
						var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
						// remove the active_button class from all stats buttons
						jQuery('.stats_list_name').find('input').removeClass('active_button');
						
						// re-add the class to the clicked button
						// used in the chart title...
						jQuery(this).find('input').addClass('active_button');
							
						// get and store the active parent button,
						// which dictates if we query Growth History Stats
						// or Campaign Stats
						var active_parent_button = jQuery('.active-parent-button').text();
						
						/** Begin Conditional AJAX **/
						// check the active button is Growth History
						// if so, query Ajax for Growth History Data
						if ( active_parent_button == 'Growth History' ) {
							// smooth scroll back to chart
							 jQuery('html, body').animate({
								scrollTop: jQuery(".account-nav-tab").offset().top
							}, 350);
							
							// append our preloader , while we fetch the data from MailChimp
						   jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_growth_data',
										api_key: apiKey,
										list_id: jQuery(this).attr('alt')
									},
									dataType: 'html',
									success: function(response) {
										// reload the chart
										jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
									}
								});	
						// if the active parent button is Campaign Stats,
						// we should query for Campaign Stats for the specified List
						} else if ( active_parent_button == 'Campaign Stats' ) {
							var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
							 jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_campaign_data',
										api_key: apiKey,
										// campaign_id: jQuery(this).attr('alt')
									},
									dataType: 'html',
									success: function(response) {
										// reload the chart
										jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
									}
								});
						}
					});
					
					jQuery('.campaign-stats-button').off().on( 'click' , function() {
						var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
							// hide and show the correct divs
							if ( jQuery('#interactive_list_items').is(':visible') ) {
								// hide the selectable lists
								jQuery('#interactive_list_items').hide();
								// show our selectable campaigns
								jQuery('#interactive_campaigns').show();
							}					
					
							// remove active-parent-button class from all elements
							jQuery('.statistics-sub-title').each(function() {
								jQuery(this).removeClass('active-parent-button');
							});
							// re-add it to the clicked element
							jQuery(this).addClass('active-parent-button');	
							
							// get and store the active parent button,
							// which dictates if we query Growth History Stats
							// or Campaign Stats
							var active_list_button = jQuery('.list_container_for_stats').find('.active_button').text();			
							// AJAX request to get campaign data
							jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
								   jQuery.ajax({
										type: 'POST',
										url: ajaxurl,
										data: {
											action: 'yks_mailchimp_form',
											form_action: 'yks_get_campaign_data',
											api_key: apiKey,
											// campaign_id: jQuery(this).attr('alt')
										},
										dataType: 'html',
										success: function(response) {
											// reload the chart
											jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
										}
									});
						
					});
					
					// Growth History Click
					jQuery('.growth-history-button').off().on( 'click' , function() {
						var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
						// show and hide the correct div's
						if ( jQuery('#interactive_campaigns').is(':visible') ) {
							// hide the selectable lists
							jQuery('#interactive_campaigns').hide();
							// show our selectable campaigns
							jQuery('#interactive_list_items').show();
						}
						
						// remove active-parent-button class from all elements
						jQuery('.statistics-sub-title').each(function() {
							jQuery(this).removeClass('active-parent-button');
						});
						// re-add it to the clicked element
						jQuery(this).addClass('active-parent-button');
										
						// get and store the active parent button,
						// which dictates if we query Growth History Stats
						// or Campaign Stats
						var active_parent_button = jQuery('.active-parent-button').text();
						
						/** Begin Conditional AJAX **/
						// check the active button is Growth History
						// if so, query Ajax for Growth History Data
						if ( active_parent_button == 'Growth History' ) {
							// smooth scroll back to chart
							 jQuery('html, body').animate({
								scrollTop: jQuery(".account-nav-tab").offset().top
							}, 350);
							
							// append our preloader , while we fetch the data from MailChimp
						   jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_growth_data',
										api_key: apiKey,
										list_id: jQuery('.list_container_for_stats').find('.active_button').attr('alt')
									},
									dataType: 'html',
									success: function(response) {
										// reload the chart
										jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
									}
								});	
						// if the active parent button is Campaign Stats,
						// we should query for Campaign Stats for the specified List
						} else if ( active_parent_button == 'Campaign Stats' ) {
							var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
							 jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_campaign_data',
										api_key: apiKey,
										// campaign_id: jQuery(this).attr('alt')
									},
									dataType: 'html',
									success: function(response) {
										// reload the chart
										jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
									}
								});
						}			
						
					});
				
				
					// Ajax to load in our report data for a specific campaign
					// post the data to our get growth list data 
					jQuery('.mailChimpStatisticsDiv').off().on().delegate( '.yks-mc-view-report-button', 'click' , function() {	
							
							// #mailChimpStatisticsDiv
							var campaign_id = jQuery(this).attr('alt');
							var campaign_title = jQuery(this).attr('title');
							var campaign_subject  = jQuery(this).parents('.single_report_row').find('.yks-mc-campaign-email-subject').val();
							var campaign_send_date  = jQuery(this).parents('.single_report_row').find('.yks-mc-campaign-send-date').val();
							var campaign_send_time  = jQuery(this).parents('.single_report_row').find('.yks-mc-campaign-send-time').val();
							var campaign_view_email_link  = jQuery(this).parents('.single_report_row').find('.yks-mc-campaign-view-email-link').val();
							var campaign_web_id  = jQuery(this).parents('.single_report_row').find('.yks-mc-campaign-web-id').val();
							var apiKey = '<?php echo $this->optionVal['api-key']; ?>';	
							
							// hide the parent buttons
							jQuery('.statistics-sub-title').hide();
							
							// ajax request to get our specific campaign report data
							jQuery('.mailChimpStatisticsDiv').html('<img class="mailChimp_get_subscribers_preloader" style="padding-top:4em;" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >');
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_specific_campaign_data',
										api_key: apiKey,
										campaign_id: campaign_id,
										campaign_title: campaign_title,
										campaign_subject: campaign_subject ,
										campaign_send_date: campaign_send_date,
										campaign_send_time: campaign_send_time,
										campaign_view_email_link: campaign_view_email_link,
										campaign_web_Id: campaign_web_id
									},
									dataType: 'html',
									success: function(response) {
										jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
									}
								});
								
							// append our preloader , while we fetch the data from MailChimp
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_specific_campaign_link_data',
										api_key: apiKey,
										campaign_id: campaign_id
									},
									dataType: 'html',
									success: function(response) {
										// reload the chart
										jQuery('#click_tracking_info').html('<div>'+response+'</div>');
									},
									error: function(response) {
										console.log('Error Returned From MailChimp : '+response);
									}
								});
								
							// ajax request to get the geo data for opened links
							   jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yks_mailchimp_form',
										form_action: 'yks_get_campaign_links_geo_opens',
										api_key: apiKey,
										campaign_id: campaign_id
									},
									dataType: 'html',
									success: function(response) {
										jQuery('#geo_data_link_map').html('<div>'+response+'</div>');
									}
								});
								
					});
				
					var chart_title = jQuery('.active_button').val();
				
					if ( chart_title == '' || chart_title == 'All Lists' ) {
						chart_title = 'Account'
					} else {
						chart_title = chart_title+' List';
					}
					
					// to do - set up other buttons to load different charts
					
					var chart_description = jQuery('.active_chart_button').text();
					
					<?php if ( !empty($month_array) ) { ?>
					
					// Set up the chart
					
					var chart = new Highcharts.Chart({
						chart: {
							renderTo: 'overall_account_growth',
							type: 'column',
							margin: 75,
							options3d: {
								enabled: true,
								alpha: 0,
								beta: 0,
								depth: 50,
								viewDistance: 25
							}
						},
						xAxis: {
							// categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
							categories: [<?php echo '"' . implode( ' ", "' , $month_array ) .'"'; ?>]
						},
						title: {
							text: chart_title+' Growth'
						},
						subtitle: {
							text: 'subscribers gained by month.'
						},
						plotOptions: {
							column: {
								depth: 25
							}
						},
						yAxis: {        
								title: {
									text: 'Subscribers'
								}
							},
						series: [{
							// data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
							data: [<?php echo implode( ', ' , $total_subscriber_array ); ?>],
							showInLegend: true,               
								 name: "<b><?php _e( 'Total Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></b>",
						},
						{
							data: [<?php echo implode( ', ' , $new_subscriber_array ); ?>],
							showInLegend: true,               
								 name: "<b><?php _e( 'Imported Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></b>",
						},
						{
							data: [<?php echo implode( ', ' , $optin_subscriber_array ); ?>],
							showInLegend: true,               
								 name: "<b><?php _e( 'Opt-in Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></b>",
						}
						],
						credits: {
							enabled: false
						  },
					});
					
					<?php } else { ?>
						jQuery('.mailChimpStatisticsDiv').html("<div class=no_data_found><?php _e( 'There is no growth data for' , 'yikes-inc-easy-mailchimp-extender' ); ?>"+chart_title+"<?php _e( ' yet' , 'yikes-inc-easy-mailchimp-extender' ); ?></div>");
					<?php } ?>
					  
				});
			</script>
	<?php	} else {	?>

		<h2 class="no_data_found" style="width:100%;text-align:center;"><?php _e( "No growth data found yet. Please try again at a later time." , "yikes-inc-easy-mailchimp-extender" ); ?></h2>
	<?php } ?>
	<div id="overall_account_growth"></div>