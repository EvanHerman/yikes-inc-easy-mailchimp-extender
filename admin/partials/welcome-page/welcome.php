<?php

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	function yikes_easy_mc_welcome_tabs() {
		$section = isset( $_GET['section'] ) ? $_GET['section'] : 'getting-started';
		?>
		<h2 class="nav-tab-wrapper welcome-page-tabs">
			<a class="nav-tab <?php echo $section == 'getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'getting-started' ), 'admin.php' ) ) ); ?>">
				<span class="dashicons dashicons-admin-home"></span> <?php _e( 'Getting Started', 'yikes-inc-easy-mailchimp-extender' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'whats-new' ), 'admin.php' ) ) ); ?>">
				<span class="dashicons dashicons-warning"></span> <?php _e( "What's New", 'yikes-inc-easy-mailchimp-extender' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'add-ons' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'add-ons' ), 'admin.php' ) ) ); ?>">
				<span class="dashicons dashicons-admin-plugins"></span> <?php _e( "Add-Ons", 'yikes-inc-easy-mailchimp-extender' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'knowledge-base' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'knowledge-base' ), 'admin.php' ) ) ); ?>">
				<span class="dashicons dashicons-welcome-learn-more"></span> <?php _e( 'Knowledge Base', 'yikes-inc-easy-mailchimp-extender' ); ?>
			</a>
		</h2>
		<?php
	}
	
	function yikes_easy_mc_welcome_body() {

		$allowed_sections = array( 
			'add-ons' => true, 
			'getting-started' => true,
			'knowledge-base' => true, 
			'whats-new' => true 
		);
		$section = isset( $_GET['section'], $allowed_sections[ $_GET['section'] ] ) ? $_GET['section'] : 'getting-started';
		include_once( plugin_dir_path( dirname( __FILE__ ) ) . '/welcome-page/welcome-sections/' . $section . '-section.php' );
	}
		
	// Display custom PHP warning if <= 5.3
	do_action( 'yikes_easy_mc_php_warning' );
	?>
	<div class="wrap about-wrap welcome-page-about-wrap">
	
		<div id="yikes-mailchimp-logo"></div>
		
		<h2 class="welcome-title">
			Easy Forms for MailChimp | <?php echo __( 'MailChimp Done Right' , 'yikes-inc-easy-mailchimp-extender' ); ?>
		</h2>		
		
		<p class="about-text">
			<?php 
				// check if this is a fresh install
				if( get_option( 'yikes_easy_mailchimp_activation_date', strtotime( 'now' ) ) == strtotime( 'now' ) ) {
					echo sprintf( __( 'Welcome to the most powerful MailChimp integration for WordPress. Easy Forms for MailChimp v%s is ready to help take your mailing lists to the next level!' , 'yikes-inc-easy-mailchimp-extender' ) , $this->version ); 
				} else {
					// else thank you for updating :)
					echo sprintf( __( 'Thank you for updating to the latest version! Easy Forms for MailChimp v%s is ready to help take your mailing lists to the next level!' , 'yikes-inc-easy-mailchimp-extender' ) , $this->version ); 
				}
			?>
		</p>
		
		<?php
			// Display our tabs	
			yikes_easy_mc_welcome_tabs();
			yikes_easy_mc_welcome_body();
		?>
		
	</div>