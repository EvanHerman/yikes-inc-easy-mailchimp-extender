<?php
	if( isset( $_REQUEST['list-id'] ) ) {
		$list_id = $_REQUEST['list-id'];
		// run our API call, to get list data..
		$MailChimp	= new Mailchimp( get_option( 'yikes-mc-api-key' , '' ) );
		// get this lists data
		$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ), 'filters' => array( 'list_id' => $list_id ) ) );
		// reset our data so we can easily use it
		$list_data = $list_data['data'][0];
		// get all subscribed members
		$subscribers_list = $MailChimp->call('lists/members', 
			array(
				'id'	=>	$list_id,
				'opts'	=>	array(				
					'limit'	=>	'50',
					'sort_field'	=>	'optin_time',
					'sort_dir'	=>	'DESC'
				)	
			)	
		);
	}
	
	// print_r($list_data);
?>

<div class="wrap">

	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="Freddie - MailChimp Mascot" style="float:left;margin-right:10px;" />
		
	<h2>Easy MailChimp by Yikes Inc. | <?php echo $list_data['name']; ?></h2>				
		
	<!-- Settings Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'View all subscribers below. View additional subscriber info, or add additional fields to this list.' , $this->text_domain ); ?></p>
		
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
											<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'User Email' , $this->text_domain ); ?></th>
											<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'User Client' , $this->text_domain ); ?></th>
										</tr>
									</thead>
									<!-- end header -->
									
									<!-- FOOTER -->
									<tfoot>
										<tr>
											<th class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
											<th class="manage-column column-columnname" scope="col"><?php _e( 'User Email' , $this->text_domain ); ?></th>
											<th class="manage-column column-columnname num" scope="col"><?php _e( 'User Client' , $this->text_domain ); ?></th>
										</tr>
									</tfoot>
									<!-- end footer -->
									
									<!-- TABLE BODY -->
									<tbody>
										<?php if( count( $subscribers_list['total'] ) > 0 ) { 
												$i = 1;
												foreach( $subscribers_list['data'] as $subscriber ) { 
													// setup the email client name and icon
													if( !empty( $subscriber['clients'] ) ) {
														$user_email_client_name = $subscriber['clients']['name'];
														$user_email_client_icon = "<img src='" . $subscriber['clients']['icon_url'] . "' alt=" . $user_email_client_name . " title=" . $user_email_client_name . ">";
													} else {
														$path = YIKES_MC_URL . "includes/images/na.png";
														$user_email_client_icon = "<img width='35' src='" . $path . "' alt=" . __( 'not set' , $this->text_domain ) . " title=" .  __( 'not set' , $this->text_domain ) . ">";
													}
													
										?>
											<tr class="<?php if( $i % 2 == 0 ) { echo 'alternate'; } ?>">
												<th class="check-column" scope="row"><input type="checkbox" /></th>
												<td class="column-columnname"><?php echo sanitize_email( $subscriber['email'] ); ?>
													<div class="row-actions">
														<span><a href="#"><?php _e( "View" , $this->text_domain ); ?></a> |</span>
														<span><a href="#"><?php _e( "Add Fields" , $this->text_domain ); ?></a> |</span>
														<span><a href="#"><?php _e( "Stats" , $this->text_domain ); ?></a> |</span>
														<span><a href="#" class="view-yikes-mc-list-info"><?php _e( "List Info." , $this->text_domain ); ?></a></span>														
													</div>
												</td>
												<td class="column-columnname num"><?php echo $user_email_client_icon; ?></td>
											</tr>
										<?php 	
												$i++;
												}
											} else { ?>
											<tr class="no-items">
												<td class="colspanchange" colspan="3" style="padding:25px 0 25px 25px;"><em><?php _e( 'No MailChimp lists found. Head over to' , $this->text_domain ); ?></em></td>
											</tr>
										<?php } ?>
									</tbody>
								</table> 
								<!-- end table -->
													
						</div> <!-- .postbox -->
						
						<!-- pagination -->
						<div class="tablenav">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php esc_attr_e( 'Pagination', 'wp_admin_style' ); ?></span>
								<a class='first-page disabled' title='Go to the first page' href='#'>&laquo;</a>
								<a class='prev-page disabled' title='Go to the previous page' href='#'>&lsaquo;</a>
								<span class="paging-input"><input class='current-page' title='Current page' type='text' name='paged' value='1' size='1' /> of <span class='total-pages'>5</span></span>
								<a class='next-page' title='Go to the next page' href='#'>&rsaquo;</a>
								<a class='last-page' title='Go to the last page' href='#'>&raquo;</a>
							</div>
						</div>
						
					</div> <!-- .meta-box-sortables .ui-sortable -->
					
				</div> <!-- post-body-content -->
				
				<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container">
										
					<div class="meta-box-sortables">
						
						<div class="postbox yikes-easy-mc-postbox">
																		
							<h3><?php _e( 'List Overview' , $this->text_domain ); ?></h3>
							
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
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'List Rating' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo implode( ' ' , $star_array ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Subscriber Count' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['member_count'] ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'New Since Last Campaign' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['member_count_since_send'] ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Created' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo date( get_option('date_format') , strtotime( $list_data['date_created'] ) ); ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'List Fields' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo intval( $list_data['stats']['merge_var_count'] + 1 ); // add 1 for our email field.. ?></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Short Signup URL' , $this->text_domain ); ?></strong></label></td>
									<td><input style="color:#333;" type="text" class="widefat" value="<?php echo esc_url( $list_data['subscribe_url_short'] ); ?>" disabled="disabled"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Email' , $this->text_domain ); ?></strong></label></td>
									<td><input style="color:#333;" type="text" class="widefat" value="<?php echo sanitize_email( $list_data['default_from_email'] ); ?>" disabled="disabled"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="tablecell"><strong><?php  _e( 'Default From Name' , $this->text_domain ); ?></strong></label></td>
									<td><?php echo $list_data['default_from_name']; ?></td>
								</tr>
							</table>
					
						</div> <!-- .postbox -->
						
						
						<!-- Merge Field Info -->
						<div class="postbox yikes-easy-mc-postbox">
																		
							<h3><?php _e( 'Merge Variable Overview' , $this->text_domain ); ?></h3>
							
							<a style="margin:10px;" href="#" onclick="return false;" class="button-primary"><?php _e( 'Edit Fields' , $this->text_domain ); ?></a>
							
						</div>
						
						<!-- Interest Group Field Info -->
						<div class="postbox yikes-easy-mc-postbox">
																		
							<h3><?php _e( 'Interest Groups Overview' , $this->text_domain ); ?></h3>
							
						</div>
						
						
					</div> <!-- .meta-box-sortables -->
					
				</div> <!-- #postbox-container-1 .postbox-container -->
				
			</div> <!-- #post-body .metabox-holder .columns-2 -->
			
			<br class="clear">
		</div> <!-- #poststuff -->

</div>