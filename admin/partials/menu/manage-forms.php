<?php

// Get all of our forms
$form_interface = yikes_easy_mailchimp_extender_get_form_interface();

$all_forms = $form_interface->get_all_forms();

/* Store Data if User is Authorized */
if( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {
	$list_data = yikes_get_mc_api_manager()->get_list_handler()->get_lists();
	if ( is_wp_error( $list_data ) ) {
		$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
		$error_logging->maybe_write_to_log(
			$list_data->get_error_code(),
			__( "Get Account Lists" , 'yikes-inc-easy-mailchimp-extender' ),
			"Manage Forms Page"
		);
		$list_data = array();
	}
} else {
	$list_data = array();
}
?>
<div class="wrap yikes-easy-mc-wrap">
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/Mailchimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - Mailchimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1>Easy Forms for Mailchimp | <?php _e( 'Manage Forms' , 'yikes-inc-easy-mailchimp-extender' ) ?></h1>

	<!-- Settings Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Create and manage your Mailchimp forms.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<!-- Action Notices -->
	<?php

	/* If the user hasn't authenticated yet, lets kill off */
	if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {
		wp_die( '<div class="error"><p>' . sprintf( __( 'You need to connect to Mailchimp before you can start creating forms. Head over to the <a href="%s" title="Settings Page">Settings Page</a> and enter your API key.' , 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings' ) ) ) . '</p></div>' , 500 );
	}

	/* Display our admin notices here */
	// delete form success
	if( isset( $_REQUEST['deleted-form'] ) && $_REQUEST['deleted-form'] == 'true' ) {
		?>
		<div class="updated manage-form-admin-notice">
			<p><?php _e( 'Opt-in form successfully deleted.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
	}
	// duplicate form success
	if( isset( $_REQUEST['duplicated-form'] ) && $_REQUEST['duplicated-form'] == 'true' ) {
		?>
		<div class="updated manage-form-admin-notice">
			<p><?php _e( 'Mailchimp Form successfully cloned.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
	}
	// duplicate form error
	if( isset( $_REQUEST['duplicated-form'] ) && $_REQUEST['duplicated-form'] == 'false' ) {
		?>
		<div class="error manage-form-admin-notice">
			<p><?php _e( 'There was an error trying to clone your form. Please try again. If this error persists, please contact the YIKES Inc. support team.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
	}
	// reset form submission stats success
	if( isset( $_REQUEST['reset-stats'] ) && $_REQUEST['reset-stats'] == 'true' ) {
		?>
		<div class="updated manage-form-admin-notice">
			<p><?php _e( 'Form submission stats/rates successfully reset.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
	}
	// reset form submission stats error
	if( isset( $_REQUEST['reset-stats'] ) && $_REQUEST['reset-stats'] == 'false' ) {
		?>
		<div class="error manage-form-admin-notice">
			<p><?php _e( 'There was an error trying to reset the form submission stats/rates. Please try again. If this error persists, please contact the YIKES Inc. support team.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		</div>
		<?php
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
										<th id="cb" class="manage-column column-cb check-column num" scope="col"><input type="checkbox" /></th>
										<th id="columnname" class="manage-column column-columnname num yikes-form-id-number" scope="col"><?php _e( 'ID' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'List' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Impressions' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col" ><?php _e( 'Submissions' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Conversion Rate' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									</tr>
								</thead>
								<!-- end header -->

								<!-- FOOTER -->
								<tfoot>
									<tr>
										<th class="manage-column column-cb check-column num" scope="col"><input type="checkbox" /></th>
										<th id="columnname" class="manage-column column-columnname num yikes-form-id-number" scope="col"><?php _e( 'ID' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th class="manage-column column-columnname" scope="col"><?php _e( 'List' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Impressions' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Submissions' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Conversion Rate' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									</tr>
								</tfoot>
								<!-- end footer -->

								<!-- TABLE BODY -->
								<tbody>
									<?php
									if ( count( $all_forms ) > 0 ) {
										$i = 1;
										foreach( $all_forms as $id => $form ) {
										?>
										<tr class="<?php if( $i % 2 == 0 ) { echo 'alternate'; } ?>">
											<th class="check-column num" scope="row"><input type="checkbox" /></th>
											<td class="column-columnname num"><span class="form-id-container"><?php echo esc_html( intval( $id ) ); ?></span></td>
											<td class="column-columnname">
												<!-- row title/link -->
												<a href="<?php echo esc_url_raw( add_query_arg( array( 'id' => $id ) , admin_url( 'admin.php?page=yikes-mailchimp-edit-form' ) ) ); ?>" class="row-title">
													<?php echo esc_html( stripslashes( $form['form_name'] ) ); ?>
												</a>
												<div class="row-actions">
													<span><a href="<?php echo esc_url_raw( add_query_arg( array( 'id' => $id ) , admin_url( 'admin.php?page=yikes-mailchimp-edit-form' ) ) ); ?>"><?php esc_html_e( "Edit", 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
													<span><a href="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-duplicate-form', 'mailchimp-form' => $id , 'nonce' => wp_create_nonce( 'duplicate-mailchimp-form-'.$id ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?>"><?php esc_html_e( "Duplicate", 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
													<span><a href="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-reset-stats', 'mailchimp-form' => $id , 'nonce' => wp_create_nonce( 'reset-stats-mailchimp-form-'.$id ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?>"><?php esc_html_e( "Reset Stats", 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
													<span><a href="#" class="view-yikes-mc-form-shortcode" data-alt-text="<?php _e( 'Stats' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php esc_html_e( "Shortcode" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |</span>
													<?php
														/*
														*	Custom action to allow users to add additional action links
														*	to each form. We use this in our add-ons.
														*	- Delete should remain last
														*/
														do_action( 'yikes-mailchimp-custom-form-actions' , $id );
													?>
													<span><a href="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-delete-form', 'mailchimp-form' => $id , 'nonce' => wp_create_nonce( 'delete-mailchimp-form-'.$id ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?>" class="yikes-delete-mailchimp-form" onclick="return confirm('<?php printf( __( 'Are you sure you want to delete the %s form? This cannot be undone.' , 'yikes-inc-easy-mailchimp-extender' ), stripslashes( $form['form_name'] ) ); ?>');"><?php esc_html_e( "Delete", 'yikes-inc-easy-mailchimp-extender' ); ?></a></span>
												</div>
											</td>

											<td class="column-columnname"><?php echo isset( $form['form_description'] ) ? str_replace( '[yikes-mailchimp-subscriber-count]', do_shortcode( '[yikes-mailchimp-subscriber-count form="' . $id . '"]' ), $form['form_description'] ) : ''; ?></td>
											<td class="column-columnname">
												<?php
												if ( $list_data && count( $list_data ) > 0 ) {
													$parsed = wp_list_pluck( $list_data, 'name', 'id' );
													if ( isset( $parsed[ $form['list_id'] ] ) ) {
														echo esc_textarea( $parsed[ $form['list_id'] ] );
													} else {
														echo '<strong>' . esc_html__( 'List Not Found', 'yikes-inc-easy-mailchimp-extender' ) . '</strong>';
													}
												} ?>
											</td>

											<td class="column-columnname num stat-container">
												<?php
													$impressions = number_format( $form['impressions'] );
													echo '<span title="' . esc_attr__( 'Impressions' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . esc_html( $impressions ) . '</span>';
												?>
											</td>
											<td class="column-columnname num stat-container">
												<?php
													$submissions = number_format( $form['submissions'] );
													echo '<span title="' . esc_attr__( 'Submissions' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . esc_html( $submissions ) . '</span>';
												?>
											</td>
											<td class="column-columnname num stat-container">
												<?php
													if( $impressions != 0 ) {
														$conversion_rate = ( round( 100 * $form['submissions'] / $form['impressions'] , 3 ) );
														if( $conversion_rate >= '15' ) {
															$conversion_color = '#00cc00'; // green (unicorn!)
														} else if( $conversion_rate < '15' && $conversion_rate >= '10' ) {
															$conversion_color = '#0080FF'; // light blue (good)
														} else if( $conversion_rate < '10' && $conversion_rate >= '5' ) {
															$conversion_color = '#FFFF32'; // yellow (ok)
														} else {
															$conversion_color = '#FF0000'; // red (no bueno)
														}
													} else {
														$conversion_rate = '0';
														$conversion_color = '#333333';
													}
													echo '<span style="color:' . esc_attr( $conversion_color ) . ';" title="' . esc_attr__( 'Conversion Rate' , 'yikes-inc-easy-mailchimp-extender' ) . '">' . esc_html( $conversion_rate ) . '%</span>';
												?>
											</td>
											<!-- shortcode -->
											<td class="column-columnname shortcode hidden-class" colspan="3">
												<input type="text" class="yikes-mc-shortcode-input yikes-mc-shortcode-input-<?php echo esc_attr( $id ); ?>" onclick="this.setSelectionRange(0, this.value.length)" readonly value='[yikes-mailchimp form="<?php echo esc_attr( $id ); ?>"]' />
											</td>
										</tr>
									<?php
											$i++;
											}
										} else { ?>
										<tr class="no-items">
											<td class="colspanchange no-mailchimp-forms-found" colspan="8"><em><?php esc_html_e( 'No Mailchimp forms found. Use the form to the right to create a new one.', 'yikes-inc-easy-mailchimp-extender' ); ?></em></td>
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

					<div class="postbox yikes-easy-mc-postbox">

						<?php
							$this->generate_manage_forms_sidebar( $list_data );
						?>

					</div> <!-- .postbox -->

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
