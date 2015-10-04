<?php
	// Template for our account activity dashboard widget
?>
<table class="form-table">
	<tr>
		<th class="row-title"><?php esc_attr_e( 'Type', 'yikes-inc-easy-mailchimp-extender' ); ?></th>
		<th class="row-title"><?php esc_attr_e( 'Message', 'yikes-inc-easy-mailchimp-extender' ); ?></th>
	</tr>
	<?php 
	$i = 1;
	foreach( $account_activity as $activity ) {
		if( $i <= 8 ) {
		$message = $activity['message'];
		$split_message = explode( ' - ' , $activity['message'] );
		$split_list = explode( '"' , $split_message[0] );
		if( isset( $split_list[1] ) ) {
			if( isset( $activity['list_id'] ) ) {
				$message = $split_list[0] . ' <a href="' . esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $activity['list_id'] ) ) . '"><strong>' . $split_list[1] . '</strong></a>'; 
			} else {
				$message = $split_list[0] . ' <strong>' . $split_list[1] . '</strong>'; 
			}
		} else {
			$message = $split_list[0];
		}
		?>
			<tr valign="top">
				<td><label for="tablecell"><?php echo ucwords( str_replace( '-' , ' ' , str_replace( 'lists:' , '' , $activity['type'] ) ) ); ?></label></td>
				<td><?php echo $message; ?></td>
			</tr>
		<?php 
			$i++;
			}
		} 
	?>
</table>