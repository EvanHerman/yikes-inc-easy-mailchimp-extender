<?php
/* The template file for displaying our stats in the Admin dashboard widget */

if ( isset( $list_data ) ) {
	if ( isset( $list_data['id'] ) ) {

	// When a user selects a list from the dropdown, capture the array value here
	$list = $list_data;
	} else {

		// On initial page load, we grab all of the lists and we need to default to the first item in the list

		// Get the list IDs, capture the first list ID
		$first_list_id = '';
		$list_ids = array_keys( $list_data );
		if ( is_array( $list_ids ) && isset( $list_ids[0] ) ) {
			$first_list_id = $list_ids[0];
		}

		// Set our $list value to the first list in the list_data array
		if ( isset( $list_data[ $first_list_id ] ) && is_array( $list_data[ $first_list_id ] ) && isset( $list_data[ $first_list_id ]['id'] ) ) {
			$list = $list_data[ $first_list_id ];
		}
	}
}

// Make sure we have our variables before continuing
if ( empty( $list_data ) || empty( $list ) ) {
	return;
}

?>
<section id="yikes-easy-mc-widget-stat-holder">
	<h3><?php echo $list['name']; ?> <small><a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list['id'] . '' ) ); ?>" title="<?php _e( 'view List' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'view list' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></h3>
	
	<table class="yikes-easy-mc-stats-table">
		<thead class="yikes-easy-mc-hidden">
			<tr>
				<th><?php _e( 'Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
				<th><?php _e( 'Unsubscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
				<th><?php _e( 'New Since Send' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>	
				<th><?php _e( 'Avg. Sub. Rate' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="yikes-easy-mc-table-stats-tr yikes-easy-mc-table-stats-tr-first">
				<td title="<?php _e( 'Number of active subscribers.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list['stats']['member_count']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
				<td title="<?php _e( 'Number of users who have unsusbscribed.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list['stats']['unsubscribe_count']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'unsubscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
			</tr>
			<tr class="yikes-easy-mc-table-stats-tr  yikes-easy-mc-table-stats-tr-second">
				<td title="<?php _e( 'Number of new subscribers since the last campaign was sent.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list['stats']['member_count_since_send']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'new since send' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
				<td title="<?php _e( 'Average number of subscribers per month.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list['stats']['avg_sub_rate']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'avg. sub. rate' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
</section>