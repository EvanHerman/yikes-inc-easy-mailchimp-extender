

<div id="col-container">
	<div id="col-left">
		<div class="col-wrap">	
			<h2 class="premium-support-title"><?php _e( 'Priority & Add-on Support', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>

			<?php
				if ( isset( $_POST['submit-premium-support-request'] ) ) {
			?>
					<h4><?php _e( 'Success!', 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
					<p><?php _e( 'We have received your support request and will get in touch shortly regarding your issue.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<?php
				}
			?>

			<!-- Premium Support Form -->
			<form id="premium-support-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-support#premium-support-form' ) ); ?>" <?php echo (isset($_POST['submit-premium-support-request'])) ? 'class="support-form-hidden"' : ''; ?>>

				<label><?php _e( 'Add-On:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<select name="license_key" id="license-key">
						<?php
							foreach ( $addons as $extension ) {

								// get the license for the respective extension
								$options = get_option( 'yikes-mailchimp-' . $extension . '-settings', array() );

								if ( isset( $options['license'] ) && $options['status'] == 'valid' ) {
									echo '<option data-plugin-slug="' . esc_attr( $extension ) . '" data-plugin-name="' . esc_attr( ucwords( str_replace( '-', ' ', $extension ) ) ) . '" value="' . esc_attr( trim( $options['license'] ) ) . '">' . ucwords( str_replace( '-', ' ', $extension ) ) . ' ' . '</option>';
								}
							}
						?>
					</select>
					<p class="description"><?php _e( 'Select the add-on that you are looking for help with.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

					<input type="hidden" name="plugin-slug" id="plugin-slug" value="">
					<input type="hidden" name="plugin-name" id="plugin-name" value="">
				</label>

				<label><?php _e( 'Name:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<input type="text" name="user-name" required>
					<p class="description"><?php _e( 'Enter your name.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</label>

				<label><?php _e( 'Contact Email:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<input type="email" name="user-email" required>
					<p class="description"><?php _e( 'Enter the email address you would prefer to be contact at.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</label>

				<label><?php _e( 'Topic:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<input type="text" name="support-topic" required>
					<p class="description"><?php _e( 'Pleae enter the topic of your support request.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</label>

				<label><?php _e( 'Priority:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<select name="support-priority">
						<option value="1"><?php _e( 'Low' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
						<option value="2"><?php _e( 'Medium' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
						<option value="3"><?php _e( 'High' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
						<option value="4"><?php _e( 'Urgent' , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Select the priority of this ticket.' , 'yikes-inc-easy-mailchimp-extender' ); ?><em><?php _e( 'Note: Please do not abuse "urgent".' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></p>
				</label>
				<label><?php _e( 'Issue:', 'yikes-inc-easy-mailchimp-extender' ); ?>
					<?php wp_editor( '', 'support-content', array( 'textarea_name' => 'support-content', 'media_buttons' => false ) ); ?>
					<p class="description"><?php _e( 'Enter as much detail about the issue you are encontering as possible. After we make initial contact you can attach any screenshots necessary.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
				</label>

				<input type="hidden" name="action" value="yikes-support-request">
				<?php echo submit_button( 'Request Support', 'primary', 'submit-premium-support-request' ); ?>
				<p class="description"></p>
			</form>
		</div>
	</div>
</div>