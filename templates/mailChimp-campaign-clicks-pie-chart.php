<?php

	$pie_chart_data = json_decode(stripslashes($pie_chart_link_data_array));
	
	$the_pie_chart_data_array = array();
		
	foreach ( $pie_chart_data as $chart_data ) {
		$chart_data_explosion = explode( '-----' , $chart_data );
			$chart_data_link = $chart_data_explosion[0];
			$chart_data_percent = $chart_data_explosion[1];
			
			array_push( $the_pie_chart_data_array , " ' " . $chart_data_link . " ' , " . str_replace( '%' , '' , $chart_data_percent) );
	}
	
	echo '<h2 style="width:100%;text-align:center;">' . __( "Link Click Percentage - Pie Chart" , "yikes-inc-easy-mailchimp-extender" ) . '</h2>';
	
?>
<script>
	
	
		
		setInterval(function() {
			var ajax_window_opacity = jQuery('#TB_window').css('opacity');
			if ( ajax_window_opacity < 1 ) {
				
			}
		}, 50 );
		
			
		var chart = null,
		pie_chart_options = {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: true,
				spacingTop: 50,
				renderTo: 'pie_chart_data'
			},
			title: {
				text: ''
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					size: "75%",
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b> <br /> % {point.percentage:.1f}',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
							width: 150
						}
					}
				}
			},
			series: [{
				type: 'pie',
				name: 'Click Percentage',
				data: [	
					<?php 
						foreach ( $the_pie_chart_data_array as $data ) {
							echo '[' . $data . '] ,';
						} 
					?>
				]
			}],
			credits: {
						enabled: false
					  }	
		}
		
		function drawDefaultChart() {
			chart = new Highcharts.Chart(pie_chart_options);
		}
		
		jQuery(document).ready(function() {
			// Radialize the colors
			/*
			Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
				return {
					radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
					stops: [
						[0, color],
						[1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
					]
				};
			});
			*/
			drawDefaultChart();
		});
</script>

<div id="pie_chart_data"></div>