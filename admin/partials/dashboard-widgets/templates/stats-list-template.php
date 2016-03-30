<?php
/* The template file for displaying our stats in the Admin dashboard widget */
?>
<section id="yikes-easy-mc-widget-stat-holder">
	<h3><?php echo $list_data['data'][0]['name']; ?> <small><a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list_data['data'][0]['id'] . '' ) ); ?>" title="<?php _e( 'view List' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'view list' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></h3>
	
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
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list_data['data'][0]['stats']['member_count']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
				<td title="<?php _e( 'Number of users who have unsusbscribed.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list_data['data'][0]['stats']['unsubscribe_count']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'unsubscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
			</tr>
			<tr class="yikes-easy-mc-table-stats-tr  yikes-easy-mc-table-stats-tr-second">
				<td title="<?php _e( 'Number of new subscribers since the last campaign was sent.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list_data['data'][0]['stats']['member_count_since_send']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'new since send' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
				<td title="<?php _e( 'Average number of subscribers per month.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
					<p class="yikes-easy-mc-dashboard-stat"><?php echo $list_data['data'][0]['stats']['avg_sub_rate']; ?></p>
						<p class="yikes-easy-mc-stat-list-label"><?php _e( 'avg. sub. rate' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
</section>