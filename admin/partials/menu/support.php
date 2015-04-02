<div class="wrap">

	<h2>Support Me Ya'll</h2>
	
	<?php
		// testing mailchimp api calls
		// list all lists --
		$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );
		$lists = $MailChimp->call('lists/list');
	?>
</div>