<?php
	// get the YIKES knowledge base articles
	/* if ( false === ( $knowledge_base_articles = get_transient( 'yikes-mailchimp-knowledge-base-transient' ) ) ) {
		// It wasn't there, so regenerate the data and save the transient
		$knowledge_base_articles = wp_remote_get( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase', array( 'timeout' => 120 ) );
		$knowledge_base_articles = json_decode( $contributors['body'] , true );
		set_transient( 'yikes-mailchimp-knowledge-base-transient', $contributors, 60*60*12 );
	} */
	
	
?>
<div class="wrap about-wrap">
	
	<div class="feature-section">
						
		<div id="credit-container">
			<h2><?php _e( 'Latest Knowledge Base Articles', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
			<p class="description">
				<?php printf( __( 'YIKES Easy Forms for MailChimp is a free plugin licensed under GPL v2, and was meticulously constructed with a whole lot of love by the folks at <a href="%s" target="_blank" title="YIKES Inc.">YIKES Inc.</a> in Philadelphia, PA.', 'yikes-inc-easy-mailchimp-extender' ), esc_url( 'http://www.yikesinc.com' ) ); ?>
			</p>
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
				// $feed_url = esc_url( 'https://yikesplugins.com/feed/?post_type=download&download_category=MailChimp&download_tag=add-on' );
				$feed_url = esc_url( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase' );
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
				foreach( $feed->get_items() as $kb_article ) {
					// store the description
					$description_pre_split = $kb_article->get_description();
					$kb_article_permalink = esc_url( $kb_article->get_permalink() );
					$kb_article_title = $kb_article->get_title();
					// If the returned add-on is this base plugin, skip it
					
						// extract the image
						preg_match( '/<img[^>]+\>/i', $description_pre_split, $split_string );
						// store the content sans image tag
						$description = preg_replace( "/<img[^>]+\>/i", "", $kb_article->get_description() ); 
						// store the image
						$image = ( count( $split_string ) > 0 ) ? $split_string[0] : '';
						?>
							<div class="type-download">

								<div class="featured-img">
									<a href="<?php echo $kb_article_permalink; ?>" title="<?php echo $kb_article_title; ?>" target="_blank">
										<?php echo $image; ?>
									</a>		
								</div>

								<div class="addon-content">
									<h3 class="addon-heading">
										<a href="<?php echo $kb_article_permalink; ?>" title="<?php echo $kb_article_title; ?>" target="_blank"><?php echo $kb_article_title; ?></a>
									</h3>
									<p><?php echo $description; ?></p>
								</div>

								<div class="addon-footer-wrap give-clearfix">
									<a href="<?php echo $kb_article_permalink; ?>" title="<?php echo $kb_article_title; ?>" class="button-secondary" target="_blank">
										<?php _e( 'View Article' , 'yikes-inc-easy-mailchimp-extender' ); ?>	
									<span class="dashicons dashicons-external"></span></a>
								</div>

							</div>
						<?php
				}
			?>
		</div>
			
	</div>
	
</div>