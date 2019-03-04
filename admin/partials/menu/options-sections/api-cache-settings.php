<!--
	Mailchimp API Clear Stored Cache Template
	-	Clear Mailchimp transient data such as forms, form fields, list data, subscribers etc.
		*	transient cache is stored for 60 minutes.
-->
<h3><span><?php _e( 'API Cache Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
<div class="inside">
									
	<!-- Settings Form -->
	<form action="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-clear-transient-data' , 'nonce' => wp_create_nonce( 'clear-mc-transient-data' ) ) ) ); ?>" method="post">							
									
		<p><?php _e( "Delete all Mailchimp data stored in your sites cache. Most data is stored in the cache for 1 hour." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<!-- check if any of our transients contain data -->							
			<?php if ( false === get_transient( 'yikes-easy-mailchimp-list-data' ) && false === get_transient( 'yikes-easy-mailchimp-profile-data' ) && false === get_transient( 'yikes-easy-mailchimp-account-data' ) && false === get_transient( 'yikesinc_eme_list_ids' ) && false === get_transient( 'yikes_eme_lists' ) ) { ?>
				<p><a href="#" class="button-secondary" disabled="disabled" title="<?php _e( 'No Mailchimp data found in temporary cache storage.' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Clear Mailchimp API Cache' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
			<?php } else { ?>
				<p><input type="submit" class="button-primary" value="<?php _e( 'Clear Mailchimp API Cache' , 'yikes-inc-easy-mailchimp-extender' ); ?>" /></p>
			<?php } ?>
									
	</form>
</div> <!-- .inside -->
