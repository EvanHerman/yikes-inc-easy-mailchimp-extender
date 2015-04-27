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
		$section = isset( $_GET['section'] ) ? $_GET['section'] : 'getting-started';
		?>
		<h2 class="nav-tab-wrapper" style="margin-top:1.5em;">
			<a class="nav-tab <?php echo $section == 'getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'getting-started' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'whats-new' ), 'admin.php' ) ) ); ?>">
				<?php _e( "What's New", 'edd' ); ?>
			</a>
			<a class="nav-tab <?php echo $section == 'credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'yikes-mailchimp-welcome' , 'section' => 'credits' ), 'admin.php' ) ) ); ?>">
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
	
	function custom_welcome_page_css() {	
		// Badge for welcome page
		$badge_url = YIKES_MC_URL . 'includes/images/Welcome_Page/yikes-mailchimp-welcome-logo.png';
		?>
			<style type="text/css" media="screen">
			#yikes-mailchimp-logo {
				height: 185px;
				width: 132px;
				font-size: 14px;
				text-align: center;
				background: url('<?php echo $badge_url; ?>') no-repeat;
			}
			.about-wrap #yikes-mailchimp-logo {
				position: absolute;
				top: 0;
				left: 0;
			}
			.edd-welcome-screenshots {
				float: right;
				margin-left: 10px!important;
			}
			.about-wrap .feature-section {
				margin-top: 20px;
			}
			.yikes-easy-mc-feature-image {
				float: right;
				margin-left: 30px !important;
			}
			#credit-container {
				display: inline-block;
				width: 100%;
			}
				#credit-container .team-member {
					width:40%;
					margin: 1em 0;
					margin-right: 5%;
					float: left;
					text-align: center;
				}
					#credit-container .team-member img {
						border-radius: 50%;
					}
				.about-wrap div.error, 
				.about-wrap div.updated {
					display: block !important;
					margin-left: 115px !important;
				}
			</style>
		<?php
	}
	
	// print our custom CSS
	custom_welcome_page_css();
	do_action( 'yikes_easy_mc_php_warning' );
	?>
	<div class="wrap about-wrap">
	
		<div id="yikes-mailchimp-logo"></div>
		
		<h2 class="welcome-title" style="margin-left:115px;">
			Easy MailChimp by Yikes Inc. | <?php echo __( 'MailChimp Done Right' , $text_domain ); ?>
		</h2>		
		
		<p class="about-text" style="margin-left:115px;">
			<?php echo __( 'Thank you for updating to the latest version! Easy MailChimp by Yikes Inc.' , $text_domain ) . ' ' . $this->version . __( ' is ready to help take your mailing lists to the next level!' , $text_domain ); ?>
		</p>
		
		<?php
			// Display our tabs	
			yikes_easy_mc_welcome_tabs();
			yikes_easy_mc_welcome_body();
		?>
		
	</div>