<?php
	if( isset( $_REQUEST['list-id'] ) ) {	
		$list_id = sanitize_key( $_REQUEST['list-id'] );
		// run our API call, to get list data..
		$MailChimp	= new Mailchimp( get_option( 'yikes-mc-api-key' , '' ) );
		$api_key = get_option( 'yikes-mc-api-key' , '' );
		// get this lists data
		$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => $api_key, 'filters' => array( 'list_id' => $list_id ) ) );
		// reset our data so we can easily use it
		$list_data = $list_data['data'][0];
		
		// get the merge_variables
		$merge_variables = $MailChimp->call( 'lists/merge-vars' , array( 'apikey' => $api_key , 'id' => array( $list_id ) ) );
		// re-store our data
		$merge_variables = $merge_variables['data'][0]['merge_vars'];
		
		// get the interest group data
		try {
			$interest_groupings = $MailChimp->call( 'lists/interest-groupings' , array( 'apikey' => $api_key , 'id' => $list_id , 'counts' => true ) );
		} catch( Exception $error ) {
			$no_interest_groupings = $error->getMessage();
		}
		
		$no_segments = __( 'No segments set up for this list.' , 'yikes-inc-easy-mailchimp-extender' );
		// get the segment data
		try {
			$segments = $MailChimp->call( 'lists/segments' , array( 'apikey' => $api_key , 'id' => $list_id , 'type' => 'saved' ) );
		} catch( Exception $segment_error ) {
			$no_segments = $error->getMessage();
		}
		
		// setup pagination variables
		$paged = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : '0';
		
		$limit = apply_filters( 'yikes_admin_list_subscriber_limit' , '20' );
		
		$sort_dir = isset( $_REQUEST['sort'] ) ? $_REQUEST['sort'] : 'DESC';
		$column = isset( $_REQUEST['column'] ) ? $_REQUEST['column'] : 'optin_time';
		
		if( $sort_dir == 'DESC' ) {
			$opposite_sort_dir = 'ASC';
			$icon = '<span class="dashicons dashicons-arrow-down"></span>';
		} else {
			$opposite_sort_dir = 'DESC';
			$icon = '<span class="dashicons dashicons-arrow-up"></span>';
		}
		
		if( !isset( $_REQUEST['sort'] ) ) {	
			$icon = '';
		}
		
		// get all subscribed members
		$subscribers_list = $MailChimp->call('lists/members', 
			array(
				'id'	=>	$list_id,
				'opts'	=>	array(			
					'start' => $paged,
					'limit'	=>	$limit,
					'sort_field'	=>	$column,
					'sort_dir'	=>	$sort_dir
				)	
			)	
		);
		
		$total_pages = ceil( $subscribers_list['total'] / $limit );
		if( $total_pages == 0 ) {
			$total_pages = '1';
		}
		
	}
	
?>
<div class="wrap">
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php __( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />
		
	<h1>YIKES Easy Forms for MailChimp | <?php echo $list_data['name']; ?></h1>				
		
	<!-- Settings Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'View all subscribers below. View additional subscriber info, or add additional fields to this list.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
	<!-- <p class="add-new-subscriber-button"><a href="#" onclick="jQuery(this).parent().next().slideToggle();" class="add-new-h2"><?php _e( 'New Subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p> -->
	
	<?php
		/* Display our admin notices here */
		// Unsubscribe user confirmation message
		if( isset( $_REQUEST['user-unsubscribed'] ) && $_REQUEST['user-unsubscribed'] == 'true' ) {
			?>
			<div class="updated manage-form-admin-notice">
				<p><?php _e( 'User successfully unsubscribed.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			</div>
			<?php
		}
		if( isset( $_REQUEST['user-unsubscribed'] ) && $_REQUEST['user-unsubscribed'] == 'false' ) {
			?>
			<div class="error manage-form-admin-notice">
				<p><?php _e( "We've encountered an error trying to remove the subscriber. Please try again. If the error persists please get in contact with the YIKES Inc. support staff.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			</div>
			<?php
		}
	?>
	
	<section class="add-new-subscriber-form-container">
		<h4><?php _e( 'Add New Subscriber' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
		<form id="add-new-subcscriber">
			<input type="text" class="regular-text" placeholder="<?php _e( 'User Email Address' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></p>
			<p><?php echo submit_button( 'Add Subscriber' ); ?></p>
		</form>
	</section>
	
	<!-- entire body content -->
		<div id="poststuff">
	
			<div id="post-body" class="metabox-holder columns-2">
			
				<!-- main content -->
				<div id="post-body-content">
					
					<div class="meta-box-sortables ui-sortable">
						
						<div class="postbox yikes-easy-mc-postbox">
														
								<table class="wp-list-table widefat fixed posts" cellspacing="0" id="yikes-easy-mc-manage-forms-table">
		
									<!-- TABLE HEAD -->
									<thead>
										<tr>
											<th id="user-email columnname" class="manage-column column-columnname" scope="col"><a id="user-email-sort" href="<?php echo esc_url_raw( add_query_arg( array( 'column' => 'email' , 'sort' => $opposite_sort_dir ) ) ); ?>"><?php _e( 'User Email' , 'yikes-inc-easy-mailchimp-extender' ); echo $icon;?></a></th>
											<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Email Client' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										</tr>
									</thead>
									<!-- end header -->
									
									<!-- FOOTER -->
									<tfoot>
										<tr>
											<th class="manage-column column-columnname" scope="col"><?php _e( 'User Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
											<th class="manage-column column-columnname num" scope="col"><?php _e( 'Email Client' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										</tr>
									</tfoot>
									<!-- end footer -->
									
									<!-- TABLE BODY -->
									<tbody>
										<?php if( $subscribers_list['total'] > 0 ) {
												$i = 1;
												foreach( $subscribers_list['data'] as $subscriber ) { 
													$user_id = $subscriber['leid'];
													// setup the email client name and icon
													if( !empty( $subscriber['clients'] ) ) {
														$user_email_client_name = $subscriber['clients']['name'];
														$user_email_client_icon = "<img src='" . esc_url_raw( $subscriber['clients']['icon_url'] ) . "' alt=" . $user_email_client_name . " title=" . $user_email_client_name . ">";
													} else {
														$path = YIKES_MC_URL . "includes/images/na.png";
														$user_email_client_icon = "<img width='35' src='" . $path . "' alt='" . __( 'not set' , 'yikes-inc-easy-mailchimp-extender' ) . "' title='" .  __( 'not set' , 'yikes-inc-easy-mailchimp-extender' ) . "'>";
													}
													
										?>
											<tr class="<?php if( $i % 2 == 0 ) { echo 'alternate'; } ?>">
												<td class="column-columnname"><a class="user-email" href="mailto:<?php echo sanitize_email( $subscriber['email'] ); ?>"><?php echo sanitize_email( $subscriber['email'] ); ?></a>
													<div class="row-actions">
														<?php $view_user_info_url = esc_url_raw( add_query_arg( array( 'mailchimp-list' => $list_id , 'email-id' => $user_id ), admin_url() . 'admin.php?page=yikes-mailchimp-view-user' ) ); ?>
														<span><a href="<?php echo $view_user_info_url; ?>"><?php _e( "View Info." , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
														<?php $url = esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-unsubscribe-user', 'mailchimp-list' => $list_id , 'nonce' => wp_create_nonce( 'unsubscribe-user-'.$user_id ), 'email_id' => $user_id ) ) ); ?>
														<span><a href="<?php echo $url; ?>" onclick="return confirm('<?php _e( "Are you sure you want to unsubscribe" , 'yikes-inc-easy-mailchimp-extender' ); ?> ' + jQuery(this).parents('.row-actions').parent().find('.user-email').text() + ' <?php _e( 'from this mailing list?' , 'yikes-inc-easy-mailchimp-extender' ); ?>');" class="yikes-delete-subscriber"><?php _e( "Unsubscribe" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>													
													</div>
												</td>
												<td class="column-columnname num"><?php echo $user_email_client_icon; ?></td>
											</tr>
										<?php 	
												$i++;
												}
											} else { ?>
											<tr class="no-items">
												<td class="colspanchange no-current-subscriber-notice" colspan="3"><em><?php _e( 'No one is currently subscribed to this list.' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></td>
											</tr>
										<?php } ?>
									</tbody>
								</table> 
								<!-- end table -->
													
						</div> <!-- .postbox -->
						
						<!-- pagination -->
						<div class="tablenav">
							<div class="tablenav-pages">
								<a class='first-page <?php if( $paged == 0 ) { echo 'disabled'; } ?>' title='<?php _e( "Go to the first page" , 'yikes-inc-easy-mailchimp-extender' ); ?>' href='<?php echo esc_url_raw( add_query_arg( array( "paged" => 0 ) ) ); ?>'>&laquo;</a>
								<a class='prev-page <?php if( $paged == 0 ) { echo 'disabled'; } ?>' title='<?php _e( "Go to the previous page" , 'yikes-inc-easy-mailchimp-extender' ); ?>' href='<?php echo esc_url_raw( add_query_arg( array( "paged" => intval( $paged - 1 ) ) ) ); ?>'>&lsaquo;</a>
								<span class="paging-input"><input class='current-page' title='<?php _e( "Current page" , 'yikes-inc-easy-mailchimp-extender' ); ?>' type='text' name='paged' value='<?php if( $paged == 0 ) { echo '1'; } else { echo intval( $paged + 1 ); } ?>' size='1' /> <?php _e( 'of', 'yikes-inc-easy-mailchimp-extender' ); ?> <span class='total-pages'><?php echo $total_pages; ?></span></span>
								<a class='next-page <?php if( $paged == intval( $total_pages - 1 ) ) { echo 'disabled'; } ?>' title='<?php _e( "Go to the next page" , 'yikes-inc-easy-mailchimp-extender' ); ?>' href='<?php echo esc_url_raw( add_query_arg( array( "paged" => intval( $paged + 1 ) ) ) ); ?>'>&rsaquo;</a>
								<a class='last-page <?php if( $paged == intval( $total_pages - 1 ) ) { echo 'disabled'; } ?>' title='<?php _e( "Go to the last page" , 'yikes-inc-easy-mailchimp-extender' ); ?>' href='<?php echo esc_url_raw( add_query_arg( array( "paged" => intval( $total_pages - 1 ) ) ) ); ?>'>&raquo;</a>
							</div>
						</div>
						
					</div> <!-- .meta-box-sortables .ui-sortable -->
					
				</div> <!-- post-body-content -->
				
				<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container">
										
					<div class="meta-box-sortables">
						
						<div class="postbox yikes-easy-mc-postbox">
																		
							<h3><?php _e( 'List Overview' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							
							<?php 
								// store list rating
								$list_rating = $list_data['list_rating'];
								if( $list_rating > 0 ) {
									$list_rating_explosion = explode( '.' , $list_rating );
									$star_array = array();
									$x = 1; 
									while( $list_rating_explosion[0] >= $x ) {
										$star_array[] = '<span class="dashicons dashicons-star-filled list-rating-star"></span>';
										$x++;
									}
									if( $list_rating_explosion[1] == '5' ) {
										$star_array[] = '<span class="dashicons dashicons-star-half list-rating-star"></span>';
									}
								} else {
									$star_array = array( 'n/a' );
								}
							?>
							<table class="form-table">
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'List Rating' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo implode( ' ' , $star_array ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Average Subscribers' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo $list_data['stats']['avg_sub_rate']; ?><small> / <?php  _e( 'month' , 'yikes-inc-easy-mailchimp-extender' ); ?></small></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Subscriber Count' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['member_count'] ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'New Since Last Campaign' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['member_count_since_send'] ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Created' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo date( get_option('date_format') , strtotime( $list_data['date_created'] ) ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'List Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['merge_var_count'] + 1 ); // add 1 for our email field.. ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Short Signup URL' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><input type="text" class="widefat view-list-sidebar-input" value="<?php echo esc_url_raw( $list_data['subscribe_url_short'] ); ?>" readonly onclick="jQuery(this).select();"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><input type="text" class="widefat view-list-sidebar-input" value="<?php echo sanitize_email( $list_data['default_from_email'] ); ?>" readonly onclick="jQuery(this).select();"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo $list_data['default_from_name']; ?></td>
								</tr>
							</table>
					
						</div> <!-- .postbox -->
						
						
						<!-- Merge Field Info -->
						<div class="postbox yikes-easy-mc-postbox">
																		
							<h3><?php _e( 'Form Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php
								if( count( $merge_variables ) >= 1 ) {
									?><ul class="merge-variable-ul"><?php
										echo '<li class="interest-group-count">' . sprintf( _n( '%d Field', '%d Fields', intval( count( $merge_variables ) ), 'yikes-inc-easy-mailchimp-extender' ), intval( count( $merge_variables ) ) ) . '</li>';
									foreach( $merge_variables as $merge_variable ) {
										echo '<li><span class="dashicons dashicons-marker"></span>' . $merge_variable['name'] . '</li>';
									}
									?></ul><?php
								}
							?>
							
						</div>
						
						<!-- Interest Group Field Info -->
						<div class="postbox yikes-easy-mc-postbox">
							
							
							<h3><?php _e( 'Interest Groups Overview' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php
								if( isset( $interest_groupings ) && count( $interest_groupings ) >= 1 ) {
									?><ul class="interest-group-ul"><?php
										echo '<li class="interest-group-count">' . sprintf( _n( '%d Merge Variable', '%d Merge Variables', intval( count( $interest_groupings ) ), 'yikes-inc-easy-mailchimp-extender' ), intval( count( $interest_groupings ) ) ) . '</li>';
									foreach( $interest_groupings as $interest_group ) {
										echo '<li><span class="dashicons dashicons-marker"></span>' . $interest_group['name'] . '<span class="interest-group-title"></span><small title="' . $interest_group['groups'][0]['subscribers'] . ' ' . __( "subscribers assigned to this group" , 'yikes-inc-easy-mailchimp-extender' ) . '">(' . $interest_group['groups'][0]['subscribers'] . ')</small></li>';
									}
									?></ul><?php
								} else {
									?>
									<ul class="interest-group-ul">
										<li><?php echo $no_interest_groupings . '.'; ?></li>
									</ul>
									<?php
								}
							?>
							
						</div>
						
						<!-- Segments Info -->
						<div class="postbox yikes-easy-mc-postbox">
							
							
							<h3><?php _e( 'Segments Overview' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php
								if( isset( $segments['saved'] ) && count( $segments['saved'] ) >= 1 ) {
									$i = 1;
									?><ul class="segment-ul"><?php
										echo '<li class="segment-group-count">' . sprintf( _n( '%d Segment', '%d Segments', intval( count( $segments['saved'] ) ), 'yikes-inc-easy-mailchimp-extender' ), intval( count( $segments['saved'] ) ) ) . '</li>';
									foreach( $segments['saved'] as $segment ) {
										echo '<li><span class="dashicons dashicons-arrow-right"></span>' . $segment['name'] . ' <small><a href="#" onclick="jQuery(this).parent().parent().next().slideToggle();jQuery(this).toggleText();return false;" data-alt-text="' . __( 'hide conditions' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . __( "view conditions" , 'yikes-inc-easy-mailchimp-extender' ) . '</a></small></li>';
										?><div class="conditionals yikes-easy-mc-hidden"><?php
										foreach( $segment['segment_opts']['conditions'] as $condition ) {
											echo '<li><small>' . sprintf( __( 'condition #%s : If %s %s %s', 'yikes-inc-easy-mailchimp-extender' ), intval( $i ), $condition['field'],  $condition['op'], $condition['value'] ) . '</small></li>';
											$i++;
										}
										?></div><?php
									}
									?></ul><?php
								} else {
									?>
									<ul class="segment-ul">
										<li><?php echo $no_segments; ?></li>
									</ul>
									<?php
								}
							?>
							<!--
								<a class="edit-segments-button" href="#" onclick="return false;" class="button-primary"><?php _e( 'Edit Segments' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							-->
							<p class="description edit-segment-description"><?php _e( 'To edit this lists segments, head over to' , 'yikes-inc-easy-mailchimp-extender' ); ?> <a href="http://www.MailChimp.com" target="_blank">MailChimp</a></p>
							
						</div>
						
						
					</div> <!-- .meta-box-sortables -->
					
				</div> <!-- #postbox-container-1 .postbox-container -->
				
			</div> <!-- #post-body .metabox-holder .columns-2 -->
			
			<br class="clear">
		</div> <!-- #poststuff -->
</div>
<!-- JS -->
<script type="text/javascript">
	 /* Toggle Text - Stats/Shortcode (manage-forms.php)*/
	jQuery.fn.toggleText = function() {
		var altText = this.data("alt-text");
		if (altText) {
			this.data("alt-text", this.html());
			this.html('<small>'+altText+'</small>');
		}
	};
</script>