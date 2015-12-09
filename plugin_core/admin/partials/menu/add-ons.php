<?php
	/*
	*	Main page to list our current add-ons
	*	@since 6.0.0
	*/
	// RSS Feed https://yikesplugins.com/feed/?post_type=download&download_category=MailChimp&download_tag=add-on
?>
<div class="wrap">
	
	<!-- Freddie Logo -->
	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="<?php _e( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />
		
	<h1>YIKES Easy Forms for MailChimp | <?php echo __( 'Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?> &nbsp; <a href="https://yikesplugins.com/plugins/?plugins=MailChimp" target="_blank" class="button-primary coming-soon-button" title="<?php _e( 'View All Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'View All Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?> <span class="dashicons dashicons-external"></span></a></h1>				
		
	<!-- Addons Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php _e( "Below you'll find a list of add-ons available for YIKES Easy Forms for MailChimp. Each add-on extends the base functionality of the free plugin." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
	
	<!-- Add-On Container -->
	<section id="add-ons">

		<?php 
			/*
			*	SimplePie strips out all query strings
			* 	we had to implement a workaround
			*	https://github.com/simplepie/simplepie/issues/317
			*/
			// Get RSS Feed(s)
			require_once( ABSPATH . WPINC . '/class-feed.php' );
			/* Create the SimplePie object */
			$feed = new SimplePie(); 
			$feed_url = esc_url( 'https://yikesplugins.com/feed/?post_type=download&download_category=MailChimp&download_tag=add-on' );
			/* Set the URL of the feed you're retrieving */
			$feed->set_feed_url( $feed_url );
			/* Tell SimplePie to cache the feed using WordPress' cache class */
			$feed->set_cache_class( 'WP_Feed_Cache' );
			/* Tell SimplePie to use the WordPress class for retrieving feed files */
			$feed->set_file_class( 'WP_SimplePie_File' );
			$feed->enable_cache( false ); // temporary
			/* Tell SimplePie how long to cache the feed data in the WordPress database */
			$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 43200, $feed_url ) );
			/* Run any other functions or filters that WordPress normally runs on feeds */
			do_action_ref_array( 'wp_feed_options', array( $feed, $feed_url ) ); 
			/* Initiate the SimplePie instance */
			$feed->init(); 
			/* Tell SimplePie to send the feed MIME headers */
			$feed->handle_content_type(); 
			if ( $feed->error() ) {
				return $feed = new WP_Error( 'simplepie-error', $feed->error() );
			}
			// print_r( $feed->get_items() );
			foreach( $feed->get_items() as $add_on ) {
				// store the description
				$description_pre_split = $add_on->get_description();
				$permalink = esc_url( $add_on->get_permalink() );
				$add_on_title = $add_on->get_title();
				// If the returned add-on is this base plugin, skip it
				if( $add_on_title != 'Easy Forms for MailChimp' ) {
					// extract the image
					preg_match( '/<img[^>]+\>/i', $description_pre_split, $split_string );
					// store the content sans image tag
					$description = preg_replace( "/<img[^>]+\>/i", "", $add_on->get_description() ); 
					// store the image
					$image = ( count( $split_string ) > 0 ) ? $split_string[0] : '';
					// if {hidden} is found, exclude it
					if( strpos( $description_pre_split, '{hidden}' ) ) {
						continue;
					}
					?>
					<div class="type-download">

						<div class="featured-img">
							<a href="<?php echo $permalink; ?>" title="<?php _e( 'Add-on Title' , 'yikes-inc-easy-mailchimp-extender' ); ?>" target="_blank">
								<?php echo $image; ?>
							</a>		
						</div>

						<div class="addon-content">
							<h3 class="addon-heading">
								<a href="<?php echo $permalink; ?>" title="<?php echo $add_on_title; ?>" target="_blank"><?php echo $add_on_title; ?></a>
							</h3>
							<p><?php echo $description; ?></p>
						</div>

						<div class="addon-footer-wrap give-clearfix">
							<a href="<?php echo $permalink; ?>" title="<?php echo $add_on_title; ?>" class="button-secondary" target="_blank">
								<?php _e( 'View Add-on' , 'yikes-inc-easy-mailchimp-extender' ); ?>	
							<span class="dashicons dashicons-external"></span></a>
						</div>

					</div>
					<?php
				}
			}
		?>

	</section>
	
</div>