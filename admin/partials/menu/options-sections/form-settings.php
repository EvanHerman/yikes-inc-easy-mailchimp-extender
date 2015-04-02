<!--
	MailChimp API Clear Stored Cache Template
-->
<h3><span><?php _e( 'Global Form Settings' , $this->text_domain ); ?></span></h3>
<div class="inside">
									
	<!-- Settings Form -->
	<form action="<?php echo add_query_arg( array( 'action' => 'yikes-easy-mc-clear-transient-data' , 'nonce' => wp_create_nonce( 'clear-mc-transient-data' ) ) ); ?>" method="post">							
									
		<p class="description"><?php _e( "Setup the global settings for your MailChimp forms. Anything set on the form will override the global settings here." , $this->text_domain ); ?></p>
			
			<h2>Add Some Settings Fields Here</h2>
									
	</form>

</div> <!-- .inside -->