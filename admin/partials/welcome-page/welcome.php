<?php

	$text_domain = 'yikes-inc-easy-mailchimp-extender';

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	function yikes_easy_mc_welcome_tabs() {
		$section = isset( $_GET['section'] ) ? $_GET['section'] : 'whats-new';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $section == 'whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'whats-new' ), 'admin.php' ) ) ); ?>">
				<?php _e( "What's New", 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'getting-started' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'credits' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Credits', 'edd' ); ?>
			</a>
		</h2>
		<?php
	}
	
	function yikes_easy_mc_welcome_body() {
		$section = isset( $_GET['section'] ) ? $_GET['section'] : 'welcome';
		if( isset( $section ) ) {
			include_once( plugin_dir_path( dirname( __FILE__ ) ) . '/welcome-page/welcome-sections/' . $section . '-section.php' );
		}
	}
	
	?>
	<div class="wrap">
	
		<section style="display:block;margin-bottom:3em;"
			<!-- Freddie Logo -->
			<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="Freddie - MailChimp Mascot" style="float:left;margin-right:10px;" />	
			<h2>Easy MailChimp by Yikes Inc. | <?php echo __( 'Welcome to Version' , $this->text_domain ) . ' ' . $this->version; ?></h2>		
		</section>
		
		<?php
			// Display our tabs	
			yikes_easy_mc_welcome_tabs();
			yikes_easy_mc_welcome_body();
		?>
		
	</div>