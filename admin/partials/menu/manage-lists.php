<?php
// lets confirm the user has a valid API key stored
if ( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
	$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_lists();
	if ( is_wp_error( $list_data ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$list_data->get_error_code(),
			__( "Get Account Lists", 'yikes-inc-easy-mailchimp-extender' ),
			"Manage Lists Page"
		);
		$list_data = array();
	}
} else {
	$list_data = array();
}
?>
<div class="wrap">
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php __( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1>Easy Forms for MailChimp | <?php _e( 'Manage Mailing Lists' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>

	<!-- Settings Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Make edits to your MailChimp lists.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<?php
		/* If the user hasn't authenticated yet, lets kill off */
		if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {

			$error_string = sprintf(
				esc_html__( 'You need to connect to MailChimp before you can start creating forms. Head over to the %s and enter your API key.', 'yikes-inc-easy-mailchimp-extender' ),
				sprintf(
					'<a href="%s" title="Settings Page">' . esc_html__( 'Settings Page', 'yikes-inc-easy-mailchimp-extender' ) . '</a>',
					admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings' )
				)
			);

			echo wp_kses_post(
				'<div class="error"><p>' . $error_string . '</p></div>'
			);

			exit;

		}
	?>

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
											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'List Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
											<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Subscriber Count' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										</tr>
									</thead>
									<!-- end header -->

									<!-- FOOTER -->
									<tfoot>
										<tr>
											<th class="manage-column column-columnname" scope="col"><?php _e( 'List Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
											<th class="manage-column column-columnname num" scope="col"><?php _e( 'Subscriber Count' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										</tr>
									</tfoot>
									<!-- end footer -->

									<!-- TABLE BODY -->
									<tbody>
										<?php if( count( $list_data ) > 0 ) {
											$i = 1;
											foreach( $list_data as $list ) {
												?>
												<tr class="<?php if( $i % 2 == 0 ) { echo 'alternate'; } ?>">
													<td class="column-columnname">
														<a href="<?php echo esc_url_raw( add_query_arg( array( 'list-id' => $list['id'] ) , admin_url( 'admin.php?page=yikes-mailchimp-view-list' ) ) ); ?>" class="row-title">
															<?php echo stripslashes( $list['name'] ); ?>
														</a>
														<div class="row-actions">
															<span><a href="<?php echo esc_url_raw( add_query_arg( array( 'list-id' => $list['id'] ) , admin_url( 'admin.php?page=yikes-mailchimp-view-list' ) ) ); ?>"><?php _e( "View" , 'yikes-inc-easy-mailchimp-extender' ); ?></a></span>
															<?php
																/*
																*	Custom action to allow users to add additional action links
																*	to each list. We use this in our add-ons.
																*/
																do_action( 'yikes-mailchimp-manage-lists-actions', $list );
															?>
														</div>
													</td>
													<td class="column-columnname num"><?php echo $list['stats']['member_count']; ?></td>
												</tr>
												<?php
												$i++;
												}
											} else {
											?>
											<tr class="no-items">
												<td class="colspanchange no-mailchimp-lists-found" colspan="3"><em><?php printf( __( 'No MailChimp lists found. Head over to <a href="%s" title="MailChimp.com">MailChimp.com</a> to setup your first mailing list. Once thats done you can head back here to customize it!' , 'yikes-inc-easy-mailchimp-extender' ), esc_url( 'http://mailchimp.com/' ) ); ?></em></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
								<!-- end table -->

						</div> <!-- .postbox -->

					</div> <!-- .meta-box-sortables .ui-sortable -->

				</div> <!-- post-body-content -->

				<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container">

					<div class="meta-box-sortables">

						<?php
							// display, show some love container
							$this->generate_show_some_love_container();
						?>

					</div> <!-- .meta-box-sortables -->

				</div> <!-- #postbox-container-1 .postbox-container -->

			</div> <!-- #post-body .metabox-holder .columns-2 -->

			<br class="clear">
		</div> <!-- #poststuff -->
</div>
