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
						
		<div id="kb-container">
			<?php include_once( YIKES_MC_PATH . 'admin/partials/helpers/knowledge-base-articles-RSS.php' ); ?>
		</div>
			
	</div>
	
</div>