<script>
	jQuery(document).ready(function() {
		jQuery(window).resize(function() {
			var slider_width = jQuery('#slider-wrapper').css('width');
			var slider_height = slider_width.replace( 'px' , '' ) * .25;
			jQuery('#slider-wrapper').css('height',slider_height);
			jQuery('.inner-wrapper').css('height',slider_height);
			console.log('The Slider Height is Currently : '+slider_height);
			console.log('The Slider Width is Currently : '+slider_width);
		});
	});
</script>
<style>
.yksme-page-welcome p {
	font-size: 18px;
}

.yksme-page-welcome .row p {
	font-size: 16px;
}

.yksme-page-welcome .new_feature_image {
	width: 100%;
}

.yksme-page-welcome .navbar-text:first-child {
	margin-left: 0 !important;
}

.yksme-page-welcome #welcome-page-content {
	border-right: 1px solid #cdcdcd;
}

.yksme-page-welcome  #welcome-page-sidebar {
	padding: 0 1.5em; 
	height: 100%;
}

.yksme-page-welcome #welcome-page-sidebar #yksme-yikes-logo {
	width: 100%;
	max-width: 222px;
	display:block;
	margin:0 auto;
}

.yksme-page-welcome  #resource-nav {
	list-style: none;
	font-size: 17px;
}

.yksme-page-welcome  .social-media-buttons {
	display:block;
	width: 100%;
	text-align:center;
	margin-top: 1.5em;
}

.yksme-page-welcome  #welcome-page-sidebar hr {
	display:block;
	margin: 2em 0;
}

body.admin_page_yks-mailchimp-welcome {
	background-color: transparent !important;
}

.yksme-page-welcome  hr {
	border-top: 1px solid rgba(213, 213, 213, 0.89);
}

.yksme-page-welcome #welcome-page-sidebar-resources {
	text-align: center !important;
}
	
.yksme-page-welcome #welcome-page-sidebar-resources h2 {
	padding-right: 0;
}

.yksme-page-welcome #welcome-page-sidebar-google-plus {
	text-align:center;
}

.yksme-page-welcome .enter-api-key-link {
	margin-left: 8px;
}

	.yksme-page-welcome .enter-api-key-link a {
			color: #428bca !important;
	}

.yksme-page-welcome .navbar-header {
	width: 100%;
}

.yksme-page-welcome .report-an-issue-link {
	color: rgba(255, 0, 20, 0.28) !important;
}
	
.yksme-page-welcome .minor-imporvements-notice {
	list-style: circle;
	margin-left: 1.5em;
}	

	.yksme-page-welcome .minor-imporvements-notice li p {
		font-size: 16px !important;
	}	


	
@media screen and (max-width:991px) {

	.yksme-page-welcome  #welcome-page-sidebar {
		border-left: none !important;
		border-top: 1px solid grey;
		padding-top: 2em;
		height: 100%;
	}
	
	.yksme-page-welcome  #welcome-page-sidebar #yksme-yikes-logo {
		width: 250px;
		display: block; 
		margin: 0 auto;
	}
	
}





#slider-wrapper{
			width: 100%;
			height: 306px;
			margin: .5em auto;
			position: relative;
			margin-bottom: 0px;
			background: rgba(0,0,0,0.5);
			overflow: hidden;
		}
		
				#s1{
					padding: 6px;
					background: #FFFFFF;
					position: absolute;
					left: 50%;
					bottom: 25px;
					margin-left: -36px;
					border-radius: 20px;
					opacity: 0.3;
					cursor: pointer;
					z-index: 999;
				}
				
				#s2{
					padding: 6px;
					background: #FFFFFF;
					position: absolute;
					left: 50%;
					bottom: 25px;
					margin-left: -12px;
					border-radius: 20px;
					opacity: 0.3;
					cursor: pointer;
					z-index: 999;
				}
				
				#s3{
					padding: 6px;
					background: #FFFFFF;
					position: absolute;
					left: 50%;
					bottom: 25px;
					margin-left: 12px;
					border-radius: 20px;
					opacity: 0.3;
					cursor: pointer;
					z-index: 999;
				}
				
				#s4{
					padding: 6px;
					background: #FFFFFF;
					position: absolute;
					left: 50%;
					bottom: 25px;
					margin-left: 36px;
					border-radius: 20px;
					opacity: 0.3;
					cursor: pointer;
					z-index: 999;
				}
				
				#s1:hover, #s2:hover, #s3:hover, #s4:hover{ opacity: 1;}
				
			.inner-wrapper{
				width: 100%;
				height: 306px;
				position: absolute;
				top: 0;
				left: 0;
				margin-bottom: 0px;
				overflow: hidden;
			}
				.control{ display: none;}
				
				#Slide1:checked ~ .overflow-wrapper{ margin-left: 0%; }
				#Slide2:checked ~ .overflow-wrapper{ margin-left: -100%; }
				#Slide3:checked ~ .overflow-wrapper{ margin-left: -200%; }
				#Slide4:checked ~ .overflow-wrapper{ margin-left: -300%; }
				
				#Slide1:checked + #s1 { opacity: 1; }
				#Slide2:checked + #s2 { opacity: 1; }
				#Slide3:checked + #s3 { opacity: 1; }
				#Slide4:checked + #s4 { opacity: 1; }
				
			.overflow-wrapper{
				width: 400%;
				height: 100%;
				position: absolute;
				top: 0;
				left: 0;
				overflow-y: hidden;
				z-index: 1;
				-webkit-transition: all 0.3s ease-in-out;
				-moz-transition: all 0.3s ease-in-out;
				-o-transition: all 0.3s ease-in-out;
				transition: all 0.3s ease-in-out;
			}
			
				.slide img{
					width: 25%;
					float: left;
				}
</style>

<!-- used for the media query -->
<meta name="viewport" content="width=device-width" />

<div class="wrap yksme-page-welcome">

	<div id="ykseme-icon" class="icon32"></div>
	
	<div id="welcome-page-content" class="col-md-9">
		
	<?php if ( get_option( 'api_validation' ) == 'valid_api_key' ) { // update from a previous version (detected by viewing the api_validation option) ?>	
		<h1 id="ykseme-page-header">
			<?php _e('Welcome to YIKES Inc. Easy MailChimp Extender','yikes-inc-easy-mailchimp-extender'); ?> v<?php echo YKSEME_VERSION_CURRENT; ?>
			<p>Thanks for updating YIKES Inc. Easy MailChimp extender. We're sure you'll be pleased with the latest additions to the plugin!</p>
		</h1>
	 <?php } else { // fresh install ?>
			<h1 id="ykseme-page-header">
				<?php _e('Welcome to YIKES Inc. Easy MailChimp Extender','yikes-inc-easy-mailchimp-extender'); ?> v<?php echo YKSEME_VERSION_CURRENT; ?>
			</h1>
								
			<p>Thanks for installing YIKES Inc. Easy MailChimp extender. We know you're going to love this plugin! Check out some of the features below, and get started by entering your API key.</p>
	<?php } ?>	 
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
				  
				  <?php if ( get_option( 'api_validation' ) == 'valid_api_key' ) { // update from a previous version (detected by viewing the api_validation option) ?>
				  
					  <p class="navbar-text"><a href="<?php echo admin_url(); ?>admin.php?page=yks-mailchimp-form" class="navbar-link">MailChimp Settings</a></p>
					  <p class="navbar-text"><a href="<?php echo admin_url(); ?>admin.php?page=yks-mailchimp-my-mailchimp" class="navbar-link">My MailChimp</a></p>
					  <p class="navbar-text"><a href="<?php echo admin_url(); ?>admin.php?page=yks-mailchimp-form-lists" class="navbar-link">Manage Lists</a></p>
					  <p class="navbar-text"><a href="<?php echo admin_url(); ?>admin.php?page=yks-mailchimp-about-yikes" class="navbar-link">About YIKES Inc</a></p>
					   <p class="navbar-text navbar-right"><a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues?q=is%3Aopen+is%3Aissue"  target="_blank" class="navbar-link report-an-issue-link">Report an Issue</a></p>
				  
				  <?php } else { // fresh install ?>
												
					  <p class="navbar-text" style="margin-right:0;">Get Started : <p class="navbar-text enter-api-key-link"> <a href="<?php echo admin_url(); ?>admin.php?page=yks-mailchimp-form" class="navbar-link"> Enter Your API Key Now</a></p></p>
				  
				  <?php } ?>
				</div>
			</div>
		</nav>
		
		<hr />		
				
		<div class="row">
		  
		  <div class="col-md-4">
			<h2>Style Customization</h2>
			<p>Style imported forms on the fly, to fit the look and feel of your site. All without writing code!</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/Form_Customizer.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  
		 <div class="col-md-4">
			<h2>Templating Framework</h2>
			<p>All new templating framework that allows you to use a bundled template, or easily create your own!</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/Custom_Templates.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>Custom Classes</h2>
			<p>We've now added the ability to attach custom class names to specific form inputs for greater style control.</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/Custom_Class_Names.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>New Manage List Page Styles</h2>
			<p>All new styles for the manage list page, allowing you to collapse forms that you don't immediatly need.</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/Manage_List_styles.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>

		  <div class="col-md-4">
			<h2>Browse Subscribers</h2>
			<p>View subscribers that have previously signed up, and remove them on the fly!</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/browse_subscribers.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>Track Campaign+Signup Statistics</h2>
			<p>View all types of statistics related to your signup forms and sent campaigns.</p>
			<p><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/track_stats.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  	
		</div>
		
		<hr />

		<div class="row">
		
			<div class="col-md-6">
			
				<h4>Other Minor Improvements</h4>
				<ul class="minor-imporvements-notice" style="list-style:circle;">
					<li><p>Added missing label to radio buttons inside forms</p></li>
					<li><p>Added missing closing parentheses on subscriber count inside view subscribers page</p></li>
					<li><p>Only run API Key check when a new key is entered (not each page load)</p></li>
					<li><p>Repaired scripts+styles not properly loading when running a site not in English</p></li>
				</ul>
			
			</div>
			
			<div class="col-md-6">
			
				<h4>Performance Echancements</h4>
				<ul class="minor-imporvements-notice" style="list-style:circle;">
					<li><p>Removed enqueue of Google CDN jQuery</p></li>
					<li><p>Enqueue scripts+styles on front end, only when a form is displayed</p></li>
					<li><p>Performed various performance checks</p></li>
				</ul>
			
			</div>
		
		</div>
		
		<hr />
		
		<h2>Sample Bundled Header Opt-In Forms</h2>		
		<div id="slider-wrapper">
		<div class="inner-wrapper">
			<input checked type="radio" name="slide" class="control" id="Slide1"/>
				<label for="Slide1" id="s1"></label>
			<input type="radio" name="slide" class="control" id="Slide2"/>
				<label for="Slide2" id="s2"></label>
			<input type="radio" name="slide" class="control" id="Slide3"/>
				<label for="Slide3" id="s3"></label>
			<div class="overflow-wrapper">
				<a class="slide" href=""><img class="slide_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/slide_images/header_optin_template_city_skyline.jpg" alt="Header Optin Template - City Skyline" title="Header Optin Template - City Skyline" onclick="return false;" ></a>
				<a class="slide" href=""><img class="slide_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/slide_images/header_optin_template_vacation.jpg" alt="Header Optin Template - Vacation" title="Header Optin Template - Vacation" onclick="return false;" ></a>
				<a class="slide" href=""><img class="slide_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/slide_images/header_optin_template_sub-head-bar.jpg" alt="Header Optin Template - Vacation" title="Header Optin Template - Vacation" onclick="return false;" ></a>
			</div>
		</div>
	</div>
		
	</div>
	
	
	<div id="welcome-page-sidebar" class="col-md-3">
		
		<a href="http://www.yikesinc.com" title="YIKES, Inc." target="_blank"><img src="<?php echo YKSEME_URL; ?>/images/yikes_logo.png" alt="YIKES, Inc." id="yksme-yikes-logo" /></a>
				
		<hr />		
				
		<div class="b-sbn" style="margin-top:2em; text-align:center;">
			<a href="http://www.bcorporation.net/yikes" target="_blank"><img src="<?php echo YKSEME_URL; ?>/images/bcorp.jpg" alt="Certified B Corporation"></a>
			<a href="http://www.sbnphiladelphia.org/" target="_blank"><img src="<?php echo YKSEME_URL; ?>/images/sbn_logo.png" alt="Proud sponsor of the Sustainable Business Network of Philadelphia"></a>
		</div>
		
		<div class="social-media-buttons">
			<a href="http://facebook.com/yikesinc" target="_blank" class="yks_mc_about_icon"><img src="<?php echo YKSEME_URL; ?>/images/facebook.png" style="border: 0px none;" alt="YIKES Philadelphia Web design and Development Facebook" height="24" width="24"></a> 
			<a href="http://twitter.com/yikesinc" target="_blank" class="yks_mc_about_icon"><img src="<?php echo YKSEME_URL; ?>/images/twitter.png" style="border: 0px none;" alt="YIKES Philadelphia Web design and Development Twitter" height="24" width="24"></a> 
			<a href="http://www.linkedin.com/companies/yikes-inc" title="Linkedin" target="_blank" class="yks_mc_about_icon"><img src="<?php echo YKSEME_URL; ?>/images/linkedin.png" alt="YIKES Philadelphia Web design and Development Linkedin" border="0" height="24" width="24"></a>
		</div>
				
		<hr />
		
		<div id="welcome-page-sidebar-resources">
			<h2>Resources</h2>
			<ul id="resource-nav">
				<li><a href="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/" title="Documentation" target="_blank" >Documentation</a></li>
				<li><a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues?q=is%3Aopen+is%3Aissue" title="Report an Issue" target="_blank" >Report an Issue</a></li>
				<li><a href="http://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender" title="Review" target="_blank" >Leave a Review</a></li>
				<li><a href="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/faq/" target="_blank" title="FAQ">FAQ</a></li>
			</ul>
		</div>
		
		<hr />
		
		<div id="welcome-page-sidebar-google-plus">
			<!-- Google+ Company Info Follow Box -->
			<!-- Place this tag where you want the widget to render. -->
			<div class="g-page" data-width="273" data-href="https://plus.google.com/102712426677276794986" data-rel="publisher"></div>
		</div>

	</div>
	
</div>


<!-- Place this tag after the last widget tag. -->
<script type="text/javascript">
  (function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/platform.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>