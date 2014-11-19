<?php
// get our data from the MailChimp API Response
$click_report_data = $campaign_click_stats;

// Load Thickbox
add_thickbox();

?>

<style>
.green-flat-button {
  position: relative;
  vertical-align: top;
  width: 200px;
  height: 45px;
  padding: 0;
  font-size: 16px;
  color: white;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  background: #27ae60;
  border: 0;
  border-bottom: 2px solid #219d55;
  cursor: pointer;
  -webkit-box-shadow: inset 0 -2px #219d55;
  box-shadow: inset 0 -2px #219d55;
}
.green-flat-button:active {
  top: 1px;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}
#TB_window, #TB_ajaxContent {
	min-width: 60% !important;
}

#TB_ajaxContent {
	display:block  !important;
	margin:0 auto  !important;
	width:100% !important;
}
</style>



<div id="click-data">

	<table id="yks-admin-link-data-table">
       <tbody>
		
	<?php
		
		echo '<h2>Specific Link Data</h2>';
		
		// if the report data is not empty, display our clicked link data chart
		if ( !empty($click_report_data['total']) ) {
			?>		
				<a href="#" onclick="return false;" class=""><input class="green-flat-button view_clicks_as_piechart yks-mc-no-print" type="button" value="<?php _e( 'Visualize Me' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></a>
			
			<tr>
				  <td class="link_data_table_head"><strong><?php _e( 'URL' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Clicks' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Click Percent' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Unique Clicks' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Unique Percent' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
			</tr>
			<?php
		} else {
			?>
			<a href="#" onclick="return false;" class="" style="float:right;"><input disabled="disabled" class="green-flat-button yks-mc-no-print" type="button" value="<?php _e( 'Visualize Me' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></a>
			
			<tr style="opacity:.25;">
				  <td class="link_data_table_head"><strong><?php _e( 'URL' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Clicks' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Click Percent' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Unique Clicks' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
				  <td class="link_data_table_head"><strong><?php _e( 'Unique Percent' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></td>
			</tr>
			
			<tr><td class="yks-mc-report-error" style="position:absolute;width:98%;"><?php _e( 'No Links Clicked' , 'yikes-inc-easy-mailchimp-extender' ); ?></td></tr>
			
			<!-- add da top margin to our geo map to make room for the no clicked links error -->
			<style>
				#geo_data_link_map {
					display:block;
					margin-top:4em !important;
				}
			</style>
			<?php
		}
		
		// print_r($click_report_data);
		
		$links_array = array();
		$percentage_array = array();
		
		/** Test **/
		$data_array = array();
		
		foreach ( $click_report_data['total'] as $data ) {	
			// set variables 
			// explode the url, to clean it of any MalChimp tracking data
			$explode_url = explode( '?utm_source' , $data['url'] );
			// store the cleaned URL
			$data_clean_url = $explode_url[0];
			$url_clicks = $data['clicks'];
			$url_clicks_percent = round((float)$data['clicks_percent'] * 100 ) . '%';
			$url_unique = $data['unique'];
			$url_unique_percent =  round((float)$data['unique_percent'] * 100 ) . '%';
			
			// array_push( $links_array , $data_clean_url );
			// array_push( $percentage_array , $url_clicks_percent );
			
			array_push( $data_array , $data_clean_url.'-----'.$url_clicks_percent );

			
			echo '<tr><td>&nbsp;</td></tr>';
			echo '<tr>';
				echo '<td class="single_click_item first"><a href="' . $data_clean_url .'" target="_blank">'  . $data_clean_url . '</a></td>';
				echo '<td class="single_click_item">'  . $url_clicks . '</td>';
				echo '<td class="single_click_item">'  . $url_clicks_percent . '</td>';
				echo '<td class="single_click_item">'  . $url_unique . '</td>';
				echo '<td class="single_click_item last">'  . $url_unique_percent . '</td>';
			echo '</tr>';
			echo '<tr><td>&nbsp;</td></tr>';
		}
	?>
	
		</tbody>
	</table>
	
	<div id="click_data_pie_chart" style="width:950px; display:none;">
		<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
	</div>
	
</div>

<!-- javascript , ajax etc. -->
<script type="text/javascript">
jQuery(function () {
	
	// Ajax request to send our data away 
	// to be returned and rendered in a Pie Chart
	jQuery('.view_clicks_as_piechart').on( 'click' , function() {
			
		tb_show('Link Click Percentage - Pie Chart', '?type=extended&width=800&height=600');
		
		setTimeout(function() {
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'yks_mailchimp_form',
					form_action: 'yks_get_piechart',
					pie_chart_data_array: '<?php echo json_encode($data_array); ?>'
				},
				dataType: 'html',
				success: function(response) {	
					
				// reload the chart
					jQuery('#TB_ajaxContent').html(response).css('opacity',0).animate({
						opacity: 1
					}, 800);
					
				},
				error: function (error_response) {
					alert(error_response);
				}
			});	
		
		}, 350);
		
	});

});
</script>