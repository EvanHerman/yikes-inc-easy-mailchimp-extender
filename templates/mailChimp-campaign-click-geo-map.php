<?php 

// geo data array
// of cities and values
$geo_data_array = array();

// build our array of geo data
// to use to populate our SVG map
foreach ( $resp as $geo_data ) {
	// push to our geo data list
	array_push( $geo_data_array , '{ "value" : ' . $geo_data['opens'] . ', "code" : ' . '"' . $geo_data['code'] . '" } ,' );
}

// sorting function
// for top 5 countries
function cmp($a, $b) {
        return $a["opens"] - $b["opens"];
}

// sort our opened data
usort( $resp , "cmp"  );

// empty list data array
$list_data_array = array();
$i = 0;
// populate our list data array
foreach ( $resp as $data ) {
	$list_data_array[$i]['country'] = $data['code'];
	$list_data_array[$i]['opens'] = $data['opens'];
	// check if the name is set,
	// if its not we'll use the first region name
	// this happens in Great Britain (England)
	if ( isset ( $data['name'] ) ) {
		$list_data_array[$i]['name'] = $data['name'];
	} else {
		$list_data_array[$i]['name'] = $data['regions'][0]['name'];
	}
	$i++;
}

$reversed_data_array = array_reverse( $list_data_array );

// print_r($resp);

// print_r($geo_data_array_encode);

?>

<!-- Flag sprites service provided by Martijn Lafeber, https://github.com/lafeber/world-flags-sprite/blob/master/LICENSE -->
<link rel="stylesheet" type="text/css" href="http://cloud.github.com/downloads/lafeber/world-flags-sprite/flags32.css" />

		<style type="text/css">
		#geo_map {
			height: 500px; 
			min-width: 310px; 
			max-width: 100%; 
			margin: 0 auto; 
		}
		.loading {
			margin-top: 10em;
			text-align: center;
			color: gray;
		}
		</style>
		<script type="text/javascript">
		jQuery(function () {
		
			jQuery.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=world-population-density.json&callback=?', function (data) {
											
								
				// Initiate the geo map
				jQuery('#geo_map').highcharts('Map', {
					
					title : {
						text : 'Campaign Opens Around The World'
					},

					mapNavigation: {
						enabled: true,
						buttonOptions: {
							verticalAlign: 'bottom'
						}
					},

					colorAxis: {
						min: 1,
						max: 1000,
						type: 'logarithmic'
					},

					series : [{
						data : [
							<?php echo implode( ' ' , $geo_data_array ); ?>
						],
						mapData: Highcharts.maps['custom/world'],
						joinBy: ['iso-a2', 'code'],
						name: '<?php __( 'Number of Campaign Opens' , 'yikes-inc-easy-mailchimp-extender' ); ?>',
						states: {
							hover: {
								color: '#BADA55'
							}
						},
						tooltip: {
							valueSuffix: ' opens'
						},
						
					}],
					credits: {
						enabled: false
					},
				});
			});
		});
		</script>
		
		
<section class="overview_information_section">
	
	<div class="overview_information">

		<h2><?php  _e('Campaign Activity Geo Map', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
		
			<div id="geo_map" style="max-width: 100%;min-width: 100%;"></div>

			<div id="geo_data_top_clicks">
			
				<h3><?php  _e('Top Opens By Country', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3>
				
				<table id="yks-admin-link-data-table">
					<tbody>
					<!-- header -->
						<tr>
							<td class="link_data_table_head"><strong><?php  _e('Country', 'yikes-inc-easy-mailchimp-extender'); ?></strong></td>
							<td class="link_data_table_head"><strong><?php  _e('Opens', 'yikes-inc-easy-mailchimp-extender'); ?></strong></td>
						</tr>
								
						<?php 
						$limit = 0;
						foreach ($reversed_data_array as $list_data) {
							echo '<tr class="f32">';
							// limit the number of returned countries
							// to 5. Break when the limit hits 6
							if ( $limit == 5 ) {
								break;
							} else {
								echo '<td><li class="flag '.strtolower($list_data["country"]).'"></li>  '.$list_data['name'].'</td>';
								echo '<td>'.$list_data['opens'].'</td>';
							}
							$limit++;
							echo '</tr>';
						}
						?>
		
			</div>
			

	</div>

</section>