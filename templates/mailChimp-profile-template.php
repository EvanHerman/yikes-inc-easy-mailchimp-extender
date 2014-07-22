<?php

// print_r($profile_response);

?>


<div class="profile_section">

	<h2><?php _e('MailChimp Account Overview', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
	
			<div class="profile_information" style="margin: 1.5em 0;">
				<img src="<?php echo  $profile_response['avatar']; ?>" height=125 width=125 style="border-radius:5px;float:left;margin:0 2em 0 2.5em;"> 
				<span class="profile_info_span"><h3><?php _e('Username', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3><?php echo ucfirst($profile_response['username']); ?></span>
				<span class="profile_info_span"><h3><?php _e('Name', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3> <?php echo ucfirst($profile_response['name']); ?></span>
				<span class="profile_info_span"><h3><?php _e('Email', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3> <?php echo ucfirst($profile_response['email']); ?></span>
				<span class="profile_info_span"><h3><?php _e('Account Role', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3> <?php echo ucfirst($profile_response['role']); ?></span>
			</div>
			
</div>	
