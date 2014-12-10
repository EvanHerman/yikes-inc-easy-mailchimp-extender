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
			<h2>Form Builder</h2>
			<p>Build up or edit your forms fields and interest groups directly from the dashboard. </p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/Form-Builder-Screenshot.png" alt="New Form Builder" title="New Form Builder" ></p>
		  </div>
		  
		 <div class="col-md-4">
			<h2>New reCaptcha API</h2>
			<p>Brand new no captcha reCaptcha API provided by Google. Built stronger to stop more spammers!</p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/reCAPTCHA/recaptcha-demo.gif" alt="New reCAPTCHA API" title="New reCAPTCHA API" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>Pre-Populate Text Fields</h2>
			<p>Pre populate input fields with custom text, or pre-defined tags (or define our own tags!)</p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/default-value.png" alt="Default Value" title="Default Value" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>New Error Log</h2>
			<p>Help diagnose issues using the new error logging system!</p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/error-log.png" alt="Error Log" title="Error Log" ></p>
		  </div>

		  <div class="col-md-4">
			<h2>Browse Subscribers</h2>
			<p>View subscribers that have previously signed up, and remove them on the fly!</p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/browse_subscribers.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  
		  <div class="col-md-4">
			<h2>Track Campaign+Signup Statistics</h2>
			<p>View all types of statistics related to your signup forms and sent campaigns.</p>
			<p style="min-height:360px;"><img class="new_feature_image" src="<?php echo YKSEME_URL; ?>images/welcome_page/track_stats.jpg" alt="New Form Customizer" title="New Form Customizer" ></p>
		  </div>
		  	
		</div>
		
		<hr />

		<div class="row">
		
			<div class="col-md-6">
						
				<h4>What's New?</h4>
				<ul class="minor-imporvements-notice" style="list-style:circle;">
					<li><p><strong>New Feature:</strong> Added ability to add, edit or delete form fields directly from the WordPress dashboard</p></li>
					<li><p><strong>New Feature:</strong> Added ability to add, edit or delete interest groups directly from the WordPress dashboard</p></li>
					<li><p><strong>New Feature:</strong> Add "Update" link to forms when a user has previously subscribed</p></li>
					<li><p><strong>New Feature:</strong> Added 'default' option to text fields ( with custom pre-defined tags : {post_id} , {post_title} , {page_url} , {blog_name} , {user_logged_in} with the ability to define your own! )</p></li>
					<li><p><strong>New Feature:</strong> Added the ability to adjust required state, visibility state, merge tag and more</p></li>
					<li><p><strong>New Feature:</strong> Added the ability to toggle between ssl_verifypeer true/false</p></li>
					<li><p><strong>New Feature:</strong> Added an error log to help users diagnose errors happening within the plugin (and updated advanced debugging)</p></li>
					<li><p><strong>Bug Fix:</strong> Re-sorting fields that had a stored custom class name didn't store properly</p></li>
					<li><p><strong>Bug Fix:</strong> Wrapped bundled template text in filters</p></li>
					<li><p><strong>Bug Fix:</strong> Repaired some broken filters (get_form_data_before_send)</p></li>
					<li><p><strong>Bug Fix:</strong> Fixed labels on 'Manage List Forms' page and added field names to titles</p></li>
					<li><p><strong>Bug Fix:</strong> Fixed path to check box images on 'Clean Blue' bundled templates</p></li>
				</ul>
			
			</div>
			
			<div class="col-md-6">
			
				<h4>&nbsp;</h4>
				<ul class="minor-imporvements-notice" style="list-style:circle;">
					<li><p><strong>Enhancement:</strong> Remove JavaScript dependency to populate place holder values</p></li>
					<li><p><strong>Enhancement:</strong> Replaced Captcha with the all new No-Captcha-Re-Captcha API from Google</p></li>
					<li><p><strong>Enhancement:</strong> Introduced all new filters ( check documentation for examples )</p></li>
					<li><p><strong>Enhancement:</strong> Un-checking 'visibility' now hides the input field (instead of not generating it at all)</p></li>
					<li><p><strong>Enhancement:</strong> Re-defined YKSEME_PATH for users who have the plugins folder outside of wp-content</p></li>
					<li><p><strong>Enhancement:</strong> Added new classes to labels and input fields on the front end forms ( new classes yks-mc-label-field-label , yks-mc-form-row-field-label , yks-mc-input-field-row-field-label , yks-mc-input-field-label )</p></li>
					<li><p><strong>Bug Fix:</strong> Fixed empty API key from outputting any string (confused some users)</p></li>
					<li><p><strong>Other:</strong> Split main class file into multiple included files (help organize the main class file (sub-files located in /lib/inc/)</p></li>
					<li><p><strong>Other:</strong> Began to build up a Wiki on Github , for plug in installation/usage instructions</p></li>
					<li><p><strong>Other:</strong> Altered single/double opt-in strings inside shortcode_form.php</p></li>
				</ul>
			
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