<?php
/**
*	Knowledge Base Articles
*	Used on both the 'Suppor' page and the 'Welcome Page'
*	@since 6.0.3.8
*/
?>

<section class="knowledge-base-articles" id="knowledge-base-articles">
	<h1><?php _e( 'Knowledge Base Articles', 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
	<p class="description">
		<?php printf( __( "Below you''ll find a list of some of the most popular articles from our knowledge base. If you're running into any issues, or have any questions - you may first want to check out the %s for the answer", "yikes-inc-easy-mailchimp-extender" ), '<a href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/' ) . '">' . __( 'knowledge base', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' ) ?>
	</p>
	<hr />
	<?php 
		/*
		*	SimplePie strips out all query strings
		* 	we had to implement a workaround
		*	https://github.com/simplepie/simplepie/issues/317
		*/
		// Get RSS Feed(s)
		require_once( ABSPATH . WPINC . '/class-feed.php' );
		
		/** 
		*	KB Article Queries
		*	@since 6.0.3.8
		*/
		
		$kb_queries = array(
			/* __( 'Latest Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp' ), */
			__( 'How-To Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=how-tos' ),
			__( 'Usage Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=usage-easy-forms-for-mailchimp' ),
			__( 'Troubleshooting Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=troubleshooting' ),
			__( 'Code Snippets', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=snippet-library' ),
			__( 'Setting Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=settings' ),
			__( 'Integration Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=integrations' ),
			__( 'Designer Articles', 'yikes-inc-easy-mailchimp-extender' ) => esc_url_raw( 'https://yikesplugins.com/feed/?post_type=kbe_knowledgebase&kbe_taxonomy=easy-forms-for-mailchimp&kbe_taxonomy=designer-documentation' ),
		);
		
		/**
		*	Loop over all of our queries set above
		*	@sicne 6.0.3.8
		*/
		foreach( $kb_queries as $article_title => $rss_feed_url ) {
			$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 'yikes-inc-easy-mailchimp-support';
			/* Create the SimplePie object */
			$article_feed = new SimplePie(); 
			$article_feed->enable_cache( true ); // temporary
			/* Set the URL of the feed you're retrieving */
			$article_feed->set_feed_url( $rss_feed_url );
			/* Tell SimplePie to cache the feed using WordPress' cache class */
			$article_feed->set_cache_class( 'WP_Feed_Cache' );
			/* Tell SimplePie to use the WordPress class for retrieving feed files */
			$article_feed->set_file_class( 'WP_SimplePie_File' );
			/* Tell SimplePie how long to cache the feed data in the WordPress database - Cached for 8 hours */
			$article_feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 28800, $rss_feed_url ) );
			/* Run any other functions or filters that WordPress normally runs on feeds */
			do_action_ref_array( 'wp_feed_options', array( $article_feed, $rss_feed_url ) ); 
			/* Initiate the SimplePie instance */
			$article_feed->init(); 
			/* Tell SimplePie to send the feed MIME headers */
			$article_feed->handle_content_type(); 
			if ( $article_feed->error() ) {
				return $article_feed = new WP_Error( 'simplepie-error', $article_feed->error() );
			}
			// loop over latest items
			if( $article_feed->get_items() ) {
				?><ul class="knowledge-base-listing">
					<li class="list-title"><?php echo $article_title; ?></li>
				<?php
					foreach( $article_feed->get_items( 0, 5 ) as $kb_article ) {
						// store the description
						$description_pre_split = $kb_article->get_description();
						$kb_article_permalink = esc_url_raw( $kb_article->get_permalink() );
						$kb_article_permalink = add_query_arg(
							array(
								'utm_source' => $page,
								'utm_medium' => 'link',
								'utm_campaign' => 'easy_forms_for_mailchimp'
							),
							esc_url_raw( $kb_article_permalink )
						);
						$kb_article_title = $kb_article->get_title();
						// store the content sans image tag
						$description = $kb_article->get_description(); 
						?>
							<li class="kb-article">
								<a href="<?php echo $kb_article_permalink; ?>" title="<?php echo $kb_article_title; ?>" target="_blank"><?php echo $kb_article_title; ?></a>
								<p class="description"><?php echo $description; ?></p>
							</li>
						<?php
					}
				?></ul><?php
			}
		}
	?>
</section>