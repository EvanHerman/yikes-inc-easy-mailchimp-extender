<?php

function process_yikes_unsubscribe_shortcode( $args ) {

	$defaults = array(
		'list'              => '',
		'form'              => '',
		'title'             => '',
		'description'       => '',
		'email_label'       => 'Email Address',
		'submit_label'      => 'Unsubscribe',
		'email_placeholder' => ''
	);

	$values = is_array( $args ) ? array_merge( $defaults, $args ) : $defaults;

	if ( empty( $values['list'] ) && empty( $values['form'] ) ) {
		return '<!-- YIKES Easy Forms Error: no list ID / form ID -->';
	}

	if ( ! empty( $values['form'] ) ) {

		// Get the list ID from the form ID
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $values['form'] );
		$list_id   = isset( $form_data['list_id'] ) ? $form_data['list_id'] : null;

	} else if ( ! empty( $values['list'] ) ) {

		$list_id = $values['list'];
	}

	if ( empty( $list_id ) ) {
		return '<!-- YIKES Easy Forms Error: no list ID -->';
	}

	// Include our JS AJAX functions
	wp_register_script( 'yikes-mailchimp-unsubscribe-script', plugin_dir_url( __FILE__ ) . '/unsubscribe.js', array( 'jquery' ), YIKES_MC_VERSION, true );
	wp_localize_script( 'yikes-mailchimp-unsubscribe-script', 'yikes_unsubscribe_data',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'error1'   => apply_filters( 'yikes-mailchimp-unsubscribe-general-error', __( 'An error occurred.', 'yikes-inc-easy-mailchimp-extender' ) ),
			'error2'   => apply_filters( 'yikes-mailchimp-unsubscribe-not-found-error', __( 'It looks like you\'re already unsubscribed.', 'yikes-inc-easy-mailchimp-extender' ) ),
			'success'  => apply_filters( 'yikes-mailchimp-unsubscribe-success', __( 'Success! You\'ve been unsubscribed from this list.', 'yikes-inc-easy-mailchimp-extender' ) ),
			'loader'   => apply_filters( 'yikes-mailchimp-unsubscribe-loader', YIKES_MC_URL . 'includes/images/ripple.svg' ),
		)
	);
	wp_enqueue_script ( 'yikes-mailchimp-unsubscribe-script' );

	// Include our styles
	wp_enqueue_style( 'yikes-mailchimp-unsubscribe-styles', plugin_dir_url( __FILE__ ) . '/unsubscribe.css', array(), YIKES_MC_VERSION, 'all' );

	ob_start();
	?>
		<section id="yikes-mailchimp-unsubscribe-container-<?php echo esc_attr( $list_id ); ?>" class="yikes-mailchimp-unsubscribe-container">

			<div id="yikes-mailchimp-unsubscribe-title-<?php echo esc_attr( $list_id ); ?>" class="yikes-mailchimp-unsubscribe-title">
				<h2><?php echo apply_filters( 'yikes-mailchimp-unsubscribe-title', esc_html( $values['title'] ) ); ?></h2>
			</div>

			<div id="yikes-mailchimp-unsubscribe-description-<?php echo esc_attr( $list_id ); ?>" class="yikes-mailchimp-unsubscribe-description">
				<?php echo apply_filters( 'yikes-mailchimp-unsubscribe-description', esc_html( $values['description'] ) ); ?>
			</div>

			<?php do_action( 'yikes-mailchimp-unsubscribe-before-form' ); ?>

			<div class="yikes-mailchimp-unsubscribe-feedback" style="display: none;"></div>

			<form id="yikes-mailchimp-unsubscribe-form-<?php echo esc_attr( $list_id ); ?>" class="yikes-mailchimp-unsubscribe-form" method="POST">

				<!-- Email -->
				<?php do_action( 'yikes-mailchimp-unsubscribe-before-email' ); ?>
				<label for="yikes-mailchimp-unsubscribe-email" class="EMAIL-label">
					<span class="EMAIL-label"><?php echo esc_html( $values['email_label'] ); ?></span>
					<input name="EMAIL" placeholder="<?php echo esc_attr( $values['email_placeholder'] ); ?>" class="yikes-mailchimp-unsubscribe-email" id="yikes-mailchimp-unsubscribe-email" required="required" type="email" value="<?php echo esc_attr( apply_filters( 'yikes-mailchimp-unsubscribe-email-default', '' ) ); ?>">
				</label>
				<?php do_action( 'yikes-mailchimp-unsubscribe-after-email' ); ?>

				<!-- Honeypot Trap -->
				<input type="hidden" class="yikes-mailchimp-honeypot" name="yikes-mailchimp-honeypot" value="">

				<!-- List ID -->
				<input type="hidden" class="yikes-mailchimp-unsubscribe-list-id" name="yikes-mailchimp-unsubscribe-list-id" value="<?php echo esc_attr( $list_id ); ?>">

				<!-- Submit Button -->
				<button type="submit" class="yikes-mailchimp-unsubscribe-submit-button">
					<span class="yikes-mailchimp-submit-button-span-text"><?php echo esc_html( $values['submit_label'] ); ?></span>
				</button>

				<input type="hidden" class="yikes-mailchimp-unsubscribe-nonce" name="yikes-mailchimp-unsubscribe-nonce" value="<?php echo wp_create_nonce( 'yikes-mailchimp-unsubscribe' ); ?>">
			</form>

			<?php do_action( 'yikes-mailchimp-unsubscribe-after-form' ); ?>
		</section>
	<?php

	return ob_get_clean();
}

add_shortcode( 'yikes-mailchimp-unsubscribe', 'process_yikes_unsubscribe_shortcode' );
