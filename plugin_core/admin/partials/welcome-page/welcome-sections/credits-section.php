<?php
	// get the YIKES Inc. MailChimp contributors via the Github API
	if ( false === ( $contributors = get_transient( 'yikes-mailchimp-contributor-transient' ) ) ) {
		// It wasn't there, so regenerate the data and save the transient
		$contributors = wp_remote_get( 'https://api.github.com/repos/yikesinc/yikes-inc-easy-mailchimp-extender/contributors?per_page=100', array( 'timeout' => 120 ) );
		$contributors = json_decode( $contributors['body'] , true );
		set_transient( 'yikes-mailchimp-contributor-transient', $contributors, 60*60*12 );
	}
	// get the YIKES Inc. MailChimp translators via the Github API
	if ( false === ( $translators = get_transient( 'yikes-mailchimp-translators-transient' ) ) ) {
		/*
		*	If a new user contributes a translation, append them to the end of the 'translator_username' array in the following format
		*	github_username => language translated to,
		*/
		$translator_usernames = array(
			'mialevesque' => 'French', 
			'hiwhatsup' => 'Spanish', 
			'enxaqueca' => 'Portuguese (Brazilian)'
		);	
		// create an empty array to store with translator data to loop over
		$translators = array();
		foreach( $translator_usernames as $username => $language ) {
			$translator_data = wp_remote_get( 'https://api.github.com/users/' . $username, array( 'timeout' => 120 ) );
			// if( $translator_data['status'] == 200 ) {
				$translators_data_decoded = json_decode( $translator_data['body'] , true );
				$translators[] = array(
					'login' => $translators_data_decoded['login'],
					'avatar_url' => $translators_data_decoded['avatar_url'],
					'html_url' => $translators_data_decoded['html_url'],
					'name' => $translators_data_decoded['name'],
					'translation_language' => $language,
				);
			// }
		}		
		set_transient( 'yikes-mailchimp-translators-transient', $translators, 60*60*12 );
	}
?>
<div class="wrap about-wrap">
	
	<div class="feature-section">
				
		<p>
			<?php printf( __( 'YIKES Easy Forms for MailChimp is a free plugin licensed under GPL v2, and was meticulously constructed with a whole lot of love by the folks at <a href="%s" target="_blank" title="YIKES Inc.">YIKES Inc.</a> in Philadelphia, PA.', 'yikes-inc-easy-mailchimp-extender' ), esc_url( 'http://www.yikesinc.com' ) ); ?>
		</p>
		
		<div id="credit-container">
			<h2><?php _e( 'Developers', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
			<?php
				if( ! empty( $contributors ) ) {
					$old_contributors = array( 'seriouslysean' , 'Apfelbiss', 'hiwhatsup', 'mialevesque' );
						foreach( $contributors as $contributor ) {
							// skip contributors from our old plugin (this is a new re-write)
							if( ! in_array( $contributor['login'] , $old_contributors ) ) {
								?>
								<a href="<?php echo esc_url_raw( $contributor['html_url'] ); ?>" title="<?php echo $contributor['login']; ?>" target="_blank" class="github-avatar-url">
									<div class="team-member">
										<img src="<?php echo esc_url_raw( $contributor['avatar_url'] ); ?>" class="github-avatar-image">
										<p class="member-blurb">
											<p><strong><?php echo $contributor['login']; ?></strong></p>
										</p>
									</div>
								</a>
								<?php
							}
						}
				} else {
					?>
						<h4><?php _e( 'There was an error retrieving the contributors list. Please try again later.' , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
					<?php
				}
			?>
		</div>
		
		<?php if( ! empty( $translators ) ) { ?>
			<div id="translators-container">
				<h2><?php _e( 'Translators', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
				<?php
					foreach( $translators as $translator ) {
						?>
							<a href="<?php echo esc_url_raw( $translator['html_url'] ); ?>" title="<?php echo $translator['name']; ?>" target="_blank" class="github-avatar-url">
								<div class="translator">
									<img src="<?php echo esc_url_raw( $translator['avatar_url'] ); ?>" class="github-avatar-image">
									<p class="member-blurb">
										<p><strong><?php echo $translator['login']; ?></strong></p>
										<em class="translation-language"><?php echo $translator['translation_language']; ?></em>
									</p>
								</div>
							</a>
						<?php
					}
				?>
			</div>
		<?php } ?>
		
	</div>
	
</div>