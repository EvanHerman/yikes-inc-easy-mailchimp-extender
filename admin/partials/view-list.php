<?php
if ( ! isset( $_REQUEST['list-id'] ) ) {
	wp_die( __( 'Oops, we can\'t determine what List to view. Please go back and try again.' ) );
}

$list_id       = sanitize_key( $_REQUEST['list-id'] );
$list_helper   = yikes_get_mc_api_manager()->get_list_handler();
$api_key       = yikes_get_mc_api_key();
$dash_position = strpos( $api_key, '-' );


$list_data = $list_helper->get_list( $list_id );
if ( is_wp_error( $list_data ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$list_data->get_error_code(),
		__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
		"View Lists Page"
	);
	$list_data = array();
}

$merge_fields = $list_helper->get_merge_fields( $list_id );
if ( is_wp_error( $merge_fields ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$merge_fields->get_error_code(),
		__( "Get Merge Variables", 'yikes-inc-easy-mailchimp-extender' ),
		"View Lists Page"
	);
	$merge_fields = array();
}

// get the interest group data
$interest_groupings = $list_helper->get_interest_categories( $list_id );
if ( is_wp_error( $interest_groupings ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$interest_groupings->get_error_code(),
		__( "Get Interest Groups", 'yikes-inc-easy-mailchimp-extender' ),
		"View Lists Page"
	);
	$interest_groupings = array();
}

$no_interest_groupings = '<p class="description">' . __( 'Interest groups are not enabled for this list.', 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
$no_segments = __( 'No segments set up for this list.', 'yikes-inc-easy-mailchimp-extender' );
$segments = $list_helper->get_segments( $list_id );

// Get the full list of members.
$members = $list_helper->get_members( $list_id );
if ( is_wp_error( $members ) ) {
	$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
	$error_logging->maybe_write_to_log(
		$members->get_error_code(),
		__( "Get Subscriber Count", 'yikes-inc-easy-mailchimp-extender' ),
		"View Lists Page"
	);
	$members = array();
}

// setup pagination variables
$paged			= isset( $_REQUEST['paged'] ) ? filter_var( $_REQUEST['paged'], FILTER_SANITIZE_NUMBER_INT ) : 0;
$limit			= apply_filters( 'yikes_admin_list_subscriber_limit', 20 );
$page_offset	= (int) $paged * (int) $limit;
$sort_dir		= isset( $_REQUEST['sort'] ) ? $_REQUEST['sort'] : 'DESC';

if ( $sort_dir === 'DESC' ) {
	$opposite_sort_dir = 'ASC';
	$icon              = '<span class="dashicons dashicons-arrow-down"></span>';
	$sort_function     = 'krsort';
} else {
	$opposite_sort_dir = 'DESC';
	$icon              = '<span class="dashicons dashicons-arrow-up"></span>';
	$sort_function     = 'ksort';
}

// Sort the array based on the sort direction.
$sort_function( $members );

// Maybe split the array into pages
$total_pages = ceil( count( $members ) / $limit );
if ( (int) $total_pages === 0 ) {
	$total_pages = '1';
}

// Segment the members based on the page and limit
$subscribers_list = array_slice( $members, $page_offset, $limit );

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
			<p><?php submit_button( 'Add Subscriber' ); ?></p>
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
										<?php if ( count( $subscribers_list ) > 0 ) {
											$i = 1;
											foreach ( $subscribers_list as $subscriber ) {
												$user_id           = $subscriber['id'];
												$path              = YIKES_MC_URL . "includes/images/na.png";
												$email_client_icon = "<img width='35' src='" . $path . "' alt='" . __( 'not set', 'yikes-inc-easy-mailchimp-extender' ) . "' title='" . __( 'not set', 'yikes-inc-easy-mailchimp-extender' ) . "'>";

												?>
												<tr class="<?php if ( $i % 2 == 0 ) { echo 'alternate'; } ?>">
													<td class="column-columnname">
														<a class="user-email row-title" href="mailto:<?php echo sanitize_email( $subscriber['email_address'] ); ?>">
															<?php echo sanitize_email( $subscriber['email_address'] ); ?>
														</a>
														<div class="row-actions">
															<?php $view_user_info_url = esc_url_raw( add_query_arg( array(
																'mailchimp-list' => $list_id,
																'email-id'       => $user_id,
															), admin_url() . 'admin.php?page=yikes-mailchimp-view-user' ) ); ?>
															<span><a href="<?php echo $view_user_info_url; ?>"><?php _e( "View Info.", 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
															<?php $url = esc_url_raw( add_query_arg( array(
																'action'         => 'yikes-easy-mc-unsubscribe-user',
																'mailchimp-list' => $list_id,
																'nonce'          => wp_create_nonce( 'unsubscribe-user-' . $user_id ),
																'email_id'       => $user_id,
															) ) ); ?>
															<span><a href="<?php echo $url; ?>" onclick="return confirm('<?php printf( __( "Are you sure you want to unsubscribe %s from this mailing list?", 'yikes-inc-easy-mailchimp-extender' ), sanitize_email( $subscriber['email_address'] ) ); ?>');" class="yikes-delete-subscriber"><?php _e( "Unsubscribe", 'yikes-inc-easy-mailchimp-extender' ); ?></a>
														</div>
													</td>
													<td class="column-columnname num"><?php echo $email_client_icon; ?></td>
												</tr>
												<?php
												$i ++;
											}
											} else { ?>
											<tr class="no-items">
												<td class="colspanchange no-current-subscriber-notice" colspan="2"><em><?php _e( 'No one is currently subscribed to this list.' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></td>
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
									<td><?php echo intval( $list_data['stats']['merge_field_count'] + 1 ); // add 1 for our email field.. ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Short Signup URL' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><input type="text" class="widefat view-list-sidebar-input" value="<?php echo esc_url_raw( $list_data['subscribe_url_short'] ); ?>" readonly onclick="jQuery(this).select();"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Email' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><input type="text" class="widefat view-list-sidebar-input" value="<?php echo sanitize_email( $list_data['campaign_defaults']['from_email'] ); ?>" readonly onclick="jQuery(this).select();"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></label></td>
									<td><?php echo $list_data['campaign_defaults']['from_name']; ?></td>
								</tr>
							</table>

						</div> <!-- .postbox -->


						<!-- Merge Field Info -->
						<div class="postbox yikes-easy-mc-postbox">

							<h3><?php _e( 'Form Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php
								if( count( $merge_fields['merge_fields'] ) >= 1 ) {
									?><ul class="merge-variable-ul"><?php
										echo '<li class="interest-group-count">' . sprintf( _n( '%d Field', '%d Fields', intval( count( $merge_fields['merge_fields'] ) ), 'yikes-inc-easy-mailchimp-extender' ), intval( count( $merge_fields['merge_fields'] ) ) ) . '</li>';
										foreach( $merge_fields['merge_fields'] as $merge_field ) {
											// new action hook @since 6.0.3.8
											echo '<li class="' . $merge_field['tag'] . '"><span class="dashicons dashicons-marker"></span>' . $merge_field['name'] . ' ' . do_action( 'yikes-mailchimp-list-field', $merge_field ) . '</li>';
										}
									?></ul><?php
								}
								/**
								*	Custom action hook for our add-ons to hook into
								*	@since 6.0.3.8
								*/
								do_action( 'yikes-mailchimp-list-form-fields-metabox' );
							?>

						</div>

						<!-- Interest Group Field Info -->
						<div class="postbox yikes-easy-mc-postbox">


							<h3><?php _e( 'Interest Groups Overview' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
							<?php
							if ( ! empty( $interest_groupings ) ) {
								?>
								<ul class="interest-group-ul"><?php
								echo '<li class="interest-group-count">' . sprintf( _n( '%d Interest Group', '%d Interest Groups', intval( count( $interest_groupings ) ), 'yikes-inc-easy-mailchimp-extender' ), intval( count( $interest_groupings ) ) ) . '</li>';
								foreach ( $interest_groupings as $interest_group ) {
									// Build up the total subscribers
									$count = array_sum( wp_list_pluck( $interest_group['items'], 'subscriber_count' ) );
									echo '<li><span class="dashicons dashicons-marker"></span>' . $interest_group['title'] . '<span class="interest-group-title"></span><small title="' . $count . ' ' . __( "subscribers assigned to this group", 'yikes-inc-easy-mailchimp-extender' ) . '">(' . $count . ')</small></li>';
								}
								?></ul><?php
							} else {
								?>
								<ul class="interest-group-ul">
									<li><?php echo $no_interest_groupings; ?></li>
								</ul>
								<?php
							}
							/**
							 *    Custom action hook for our add-ons to hook into
							 *
							 * @since 6.0.3.8
							 */
							do_action( 'yikes-mailchimp-list-interest-groups-metabox' );
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
