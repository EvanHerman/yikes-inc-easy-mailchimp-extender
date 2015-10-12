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
				<?php _e( 'Getting Started', 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'whats-new' ), 'admin.php' ) ) ); ?>">
				<?php _e( "What's New", 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url_raw( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'credits' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Credits', 'edd' ); ?>
			</a>
		</h2>
		<?php
	}
	
	function yikes_easy_mc_welcome_body() {
		$section = isset( $_GET['section'] ) ? $_GET['section'] : 'getting-started';
		if( isset( $section ) ) {
			include_once( plugin_dir_path( dirname( __FILE__ ) ) . '/welcome-page/welcome-sections/' . $section . '-section.php' );
		}
	}
		
	// Display custom PHP warning if <= 5.3
	do_action( 'yikes_easy_mc_php_warning' );
	?>
	<div class="wrap about-wrap welcome-page-about-wrap">
	
		<div id="yikes-mailchimp-logo"></div>
		
		<h2 class="welcome-title">
			Easy Forms for MailChimp by YIKES | <?php echo __( 'MailChimp Done Right' , 'yikes-inc-easy-mailchimp-extender' ); ?>
		</h2>		
		
		<p class="about-text">
			<?php echo sprintf( __( 'Thank you for updating to the latest version! Easy Forms for MailChimp by YIKES %s is ready to help take your mailing lists to the next level!' , 'yikes-inc-easy-mailchimp-extender' ) , $this->version ); ?>
		</p>
		
		<?php
			// Display our tabs	
			yikes_easy_mc_welcome_tabs();
			yikes_easy_mc_welcome_body();
		?>
		
	</div>