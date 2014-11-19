<style>
.asbestos-flat-button {
  position: relative;
  vertical-align: top;
  width: 19.84%;
  height: 70px;
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
  margin-top: .5em;
}
.asbestos-flat-button:active {
  top: 1px;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}
.mailChimp_get_subscribers_preloader {
	margin-top: 0;
	padding-top:3.5em;
}
</style>
<?php
// My MailChimp Page
	// List Stats, Notifications, etc.
	// We can utilize chart.js to display
	// statistics etc.	

	// used to dictate the active tab
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'account_overview';
?>

<div class="wrap">
	
	<!-- yikes logo on all settings pages -->
	<div id="yks_mc_review_this_plugin_container">
		<a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues?state=open" target="_blank">
			<span class="yks_mc_need_support">
				<strong>
					<?php _e( 'Need Help?', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
					<?php _e( 'Get In Contact!', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
					<div class="dashicons dashicons-plus-alt"></div>
				</strong>
			</span>
		</a>
		<a href="http://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender" target="_blank">
			<span class="yks_mc_leave_us_a_review">
				<strong>
					<?php _e( 'Loving the plugin?', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
					<?php _e( 'Leave us a nice review', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
					<div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div>
				</strong>
			</span>
		</a>
		<a href="http://www.yikesinc.com" target="_blank" class="yks_header_logo">
			<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes_logo.png'; ?>" alt="YIKES Inc. Logo" width=85 title="YIKES Inc. Logo" />
		</a>
	</div>



    <div id="ykseme-icon" class="icon32"></div>
	
		<!-- Page Header -->
        <h2 id="ykseme-page-header">
            <?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
        </h2>		

			<h2 class="yks-mc-no-print"><?php _e( 'My MailChimp' , 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
			<p class="yks-mc-no-print"><?php _e( 'Here you will find recent activity for your MailChimp account, as well as statistics for lists and campaigns.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<br />
		
		<!-- tabs -->
		<h2 class="nav-tab-wrapper account-nav-tab yks-mc-no-print">
			<a href="?page=yks-mailchimp-my-mailchimp&tab=account_overview" class="nav-tab <?php echo $active_tab == 'account_overview' ? 'nav-tab-active' : ''; ?>"><?php _e('Account Overview','yikes-inc-easy-mailchimp-extender'); ?></a>
			<a href="?page=yks-mailchimp-my-mailchimp&tab=chimp_chatter" class="nav-tab <?php echo $active_tab == 'chimp_chatter' ? 'nav-tab-active' : ''; ?>"><?php _e('Account Activity','yikes-inc-easy-mailchimp-extender'); ?></a>
			<a href="?page=yks-mailchimp-my-mailchimp&tab=list_stats" class="nav-tab <?php echo $active_tab == 'list_stats' ? 'nav-tab-active' : ''; ?>"><?php _e('List Statistics','yikes-inc-easy-mailchimp-extender'); ?></a>
		</h2>
				
		<?php if ( $active_tab == 'account_overview' ) { ?>
		
			<script type="text/javascript">
				jQuery(document).ready(function() {
					var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
					
					// post the data to our MailChimp Get User Account Details function inside of lib.ajax.php
					jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yks_mailchimp_form',
							form_action: 'get_profile_details',
							api_key: apiKey
						},
							dataType: 'html',
							success: function(response) {
								jQuery('.mailChimpChatterDiv').prepend('<div>'+response+'</div>');		
								jQuery('.mailChimp_get_subscribers_preloader').remove();
							},
							error: function(response) {
								jQuery('.mailChimpChatterDiv').append('<p class="no_data_found">There was an error retreiving your account information.</p>');
							}
					});
										
					
				});
				
					
				
			</script>
		
				<!-- Recent Activity Header + Table -->
					<div class="mailChimpChatterDiv">
						<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
					</div>
				<?php
					echo '<span style="display:block;float:left;width:100%;font-size:1.25em;font-weight:bold;text-align:right;margin-top:2.5em;"><hr /> This plugin was created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></span>'; 
		}
		
		if ( $active_tab == 'chimp_chatter' ) { ?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
				jQuery('#yks-mailchimp-api-key').val();
				// store datacenter value, from end of api key
				var dataCenter = apiKey.substr(apiKey.indexOf("-") + 1);

				// post the data to our MailChimp Chatter function inside of lib.ajax.php
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'yks_get_chimp_chatter',
						api_key: apiKey,
						data_center: dataCenter
					},
						dataType: 'html',
						success: function(response) {
						
							// create hidden input fields to store our returned data for comparison
							// create our new chimp chatter response field
							jQuery('.mailChimpChatterDiv').before('<div style="display:none;" id="new_chimp_chatter_response"></div>');
							// create our original chimp chatter response
							jQuery('.mailChimpChatterDiv').before('<div style="display:none;" id="original_chimp_chatter_response"></div>');
							
							// populate the original chimp chatter input with our original response
							jQuery('#original_chimp_chatter_response').html(response);
							
							// populate the visible chimp chatter div with the content
							// on original page load
							jQuery('.mailChimpChatterDiv').not('#original_chimp_chatter_response').not('#new_chimp_chatter_response').html(response);
							
							// loop over the visible user facing table and wrap
							// each email with an <a> tag with mailto: attribute
							jQuery("table#yks-admin-chimp-chatter td:nth-child(4)").each(function() {
								jQuery(this).filter(function(){
								var html = jQuery(this).html();
								var emailPattern = /[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/g;  
									var matched_str = jQuery(this).html().match(emailPattern);
									var matched_str = jQuery(this).html().match(emailPattern);
										if(matched_str){
											var text = jQuery(this).html();
												jQuery.each(matched_str, function(index, value){
												text = text.replace(value,"<a href='mailto:"+value+"'>"+value+"</a>");
											});
											jQuery(this).html(text);
											return jQuery(this)
										}        
								});
							});
							
						},
						error: function(response) {
							jQuery('.nav-tab-wrapper').after('<p style="width:100%;text-align:center;margin:1em 0;">There was an error processing your request. Please try again. If this error persists, please open a support thread <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender" title="Yikes Inc Easy MailChimp GitHub Issue Tracker" target="_blank">here</a>.</p>');
						}
				});
			});
			</script>
				<!-- Recent Activity Header + Table -->
				<h3 class="statistics-sub-title"><?php _e('Recent Activity','yikes-inc-easy-mailchimp-extender'); ?></h3>
					<div class="mailChimpChatterDiv">
						<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
					</div>
					<?php
					echo '<span style="display:block;float:left;width:100%;font-size:1.25em;font-weight:bold;text-align:right;margin-top:2.5em;"><hr /> This plugin was created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></span>'; 
		 }
		
		if ( $active_tab == 'list_stats' ) { ?>			
		
			<script type="text/javascript">
			jQuery(document).ready(function() {
				var apiKey = '<?php echo $this->optionVal['api-key']; ?>';
				// post the data to our get growth list data 
				// function inside of lib.ajax.php
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yks_mailchimp_form',
						form_action: 'yks_get_growth_data',
						api_key: apiKey
					},
						dataType: 'html',
						success: function(response) {
							// on success refresh our stats chart
							jQuery('.mailChimpStatisticsDiv').html('<div>'+response+'</div>');
						}
				});	
			});
			</script>
		
			<!-- Recent Activity Header + Table -->
			<div style="margin:1em 0;">
				<h3 class="statistics-sub-title button-secondary growth-history-button active-parent-button"><?php _e('Growth History','yikes-inc-easy-mailchimp-extender'); ?></h3>
				<h3 class="statistics-sub-title button-secondary campaign-stats-button"><?php _e('Campaign Stats','yikes-inc-easy-mailchimp-extender'); ?></h3>
			</div>
			
			<div class="mailChimpStatisticsDiv" style="min-height:400px;width:100%;">
				<img class="mailChimp_get_subscribers_preloader" style="display:block;margin:0 auto;" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
			</div>
	
			<div id="click_tracking_info"></div>
			
			<div id="geo_data_link_map"></div>
			
				<div id="interactive_list_items">
					<?php
						$this->getListsForStats(); 
					?>
				</div>
				<div id="interactive_campaigns">
				
				</div>
				<?php
					echo '<span style="display:block;float:left;width:100%;font-size:1.25em;font-weight:bold;text-align:right;margin-top:2.5em;"><hr /> This plugin was created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></span>'; 
 				
			} // end list_stats page
		?>
     
</div>