
<!-- WordPress.org Support Container -->
<div id="col-container" class="free-support-container">

	<div id="col-left">

		<div class="col-wrap">
			<h1><span class="dashicons dashicons-wordpress-alt support-page-wordpress-font"></span>&nbsp;<?php _e( 'WordPress.org Plugin Directory' , 'yikes-inc-easy-mailchimp-extender' ); ?></h1>
			<div class="inside">
				<p><?php _e( 'Use your WordPress.org username to submit support requests on the WordPress Directory support forum.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				<img class="support-page-logo" src="<?php echo YIKES_MC_URL; ?>includes/images/Support_Page/wordpress-issue-screenshot.png" title="<?php esc_attr_e( 'WordPress.org Issue Tracker Screenshot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" >
				<a href="https://wordpress.org/support/plugin/yikes-inc-easy-mailchimp-extender" target="_blank" class="button-secondary support-page-button"><?php _e( 'Submit a New WordPress.org Support Request', 'yikes-inc-easy-mailchimp-extender' ); ?></a>
			</div>
		</div>
		<!-- /col-wrap -->

	</div>
	<!-- /col-left -->
			
</div>


	<?php
	
		/**
		 * List of Links to our Knowledge Base Articles
		 */
		$knowledge_base_links = array(
			array(
				'title' => esc_attr__( 'How do I change the submit button text?', 'yikes-inc-easy-mailchimp-extender' ),
				'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-change-the-submit-button-text/',
			),
			array(
				'title' => esc_attr__( 'How do I change the field labels?', 'yikes-inc-easy-mailchimp-extender' ),
				'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-change-the-field-labels/',
			),
			array(
				'title' => esc_attr__( "I don't see all of my MailChimp lists in the dropdown when I go to make a new form. Why?", 'yikes-inc-easy-mailchimp-extender' ),
				'href' => 'https://yikesplugins.com/support/knowledge-base/im-not-seeing-all-my-lists-on-the-drop-down-menu-when-i-go-to-make-a-new-form/',
			),
			array(
				'title' => esc_attr__( 'How do I add new fields to my opt-in form?', 'yikes-inc-easy-mailchimp-extender' ),
				'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-add-new-fields-to-my-form/',
			),
			array(
				'title' => esc_attr__( 'How do I place all of my form fields on one line?', 'yikes-inc-easy-mailchimp-extender' ),
				'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-place-all-of-my-form-fields-on-one-line/',
			),
		);

	?>
	<!-- support container -->
	<div id="col-container">							
		<div id="col-left">
			<div class="col-wrap">		
				<?php

					// Loop and display the knowledge base article links
					if ( $knowledge_base_links && ! empty( $knowledge_base_links ) ) {
						printf( '<h2>' . esc_attr__( 'Popular Knowledge Base Articles', 'yikes-inc-easy-mailchimp-extender' ) . '</h2>' );
						printf( '<ol>' );
						foreach ( $knowledge_base_links as $kb_link ) {
							echo wp_kses_post( '<li><a href="' . esc_url( $kb_link['href'] ) . '" title="' . esc_attr( $kb_link['title'] ) . '" target="_blank">' . esc_attr( $kb_link['title'] ) . '</a></li>' );
						}
						printf( '</ol>' );
					}
				?>
			</div>
		</div>
	</div>