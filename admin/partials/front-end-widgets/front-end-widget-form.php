<?php
/*
*	Front facing Mailchimp widget in sidebars
*	@since 6.0.0
*	By: Yikes Inc. | https://www.yikesinc.com
*/
class Yikes_Inc_Easy_Mailchimp_Extender_Widget extends WP_Widget {

	/**
	 * Our form interface.
	 *
	 * @var Yikes_Inc_Easy_Mailchimp_Extender_Form_Interface
	 */
	protected $form_interface;

	/**
	 * Yikes_Inc_Easy_Mailchimp_Extender_Widget constructor.
	 */
	public function __construct() {
		$this->form_interface = yikes_easy_mailchimp_extender_get_form_interface();

		parent::__construct(
			// Base ID of your widget
			'yikes_easy_mc_widget',
			// Widget name will appear in UI
			__( 'Easy Mailchimp Forms', 'yikes-inc-easy-mailchimp-extender' ),
			// Widget description
			array( 'description' => __( 'Mailchimp opt-in widget for your sidebar.', 'yikes-inc-easy-mailchimp-extender' ), )
		);
	}

	/**
	 * Display the widget on the front-end.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Get all form IDs so we can use the first one as a default.
		$form_ids = $this->form_interface->get_form_ids();

		$title              = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Mailchimp Signup Form', 'yikes-inc-easy-mailchimp-extender' );
		$form_id            = isset( $instance['form_id'] ) ? $instance['form_id'] : $form_ids[0];
		$form_description   = isset( $instance['form_description'] ) ? $instance['form_description'] : '';
		$submit_button_text = isset( $instance['submit_text'] ) ? $instance['submit_text'] : __( 'Submit', 'yikes-inc-easy-mailchimp-extender' );

		// Build our array based on settings chosen
		$shortcode_attributes = array(
			'form'        => $form_id,
			'submit'      => $submit_button_text,
			'description' => ( ! empty( $form_description ) ) ? '1' : '',
		);

		$shortcode_attributes = apply_filters( 'yikes_mailchimp_widget_shortcode_attributes', $shortcode_attributes, $instance );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Custom action hooks to place content in the widget before the form
		// See FAQ for examples on usage
		do_action( 'yikes-mailchimp-before-form-' . $form_id );
		do_action( 'yikes-mailchimp-before-form' );

		// This is where you run the code and display the output
		echo process_mailchimp_shortcode( $shortcode_attributes );

		// Custom action hooks to place content in the widget after the form
		// See FAQ for examples on usage
		do_action( 'yikes-mailchimp-after-form-' . $form_id );
		do_action( 'yikes-mailchimp-after-form' );

		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		$all_forms = $this->form_interface->get_all_forms();
	
		if ( empty( $all_forms ) ) {
			?>
				<section class="no-forms-widget">
				<strong><span class="dashicons dashicons-no-alt no-forms-found-icon"></span><?php echo sprintf( __( 'No forms found. It looks like you need to <a href="%s" title="%s">%s</a>.', 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ), __( 'Create a form' , 'yikes-inc-easy-mailchimp-extender' ), __( 'create a form' , 'yikes-inc-easy-mailchimp-extender' ) ); ?></strong>
				</section>
			<?php
			return;
		}
		
		// Title
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Mailchimp Signup Form', 'yikes-inc-easy-mailchimp-extender' );
		}

		// Selected Form
		if ( isset( $instance[ 'form_id' ] ) ) {
			$selected_form = $instance[ 'form_id' ];
		} else {
			$selected_form = '';
		}

		// Submit Button Text
		if ( isset( $instance[ 'submit_text' ] ) ) {
			$submit_text = $instance[ 'submit_text' ];
		} else {
			$submit_text = __( 'Submit', 'yikes-inc-easy-mailchimp-extender' );
		}

		 if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) {
			?>
				<p class="enter-valid-api-error-widget"><strong><?php _e( 'Please enter a valid Mailchimp API key to connect your site to Mailchimp.' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong></p>
			<?php
			return;
		}

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e( 'Form:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'form_id' ); ?>" name="<?php echo $this->get_field_name( 'form_id' ); ?>" class="widefat">
				<?php
				// build our array
				foreach( $all_forms as $id => $form ) {
					?>
						<option <?php selected( $selected_form , $id ); ?> name="<?php echo $this->get_field_name( 'form_id' ); ?>" value="<?php echo $id; ?>"><?php echo stripslashes( $form['form_name'] ); ?></option>
					<?php
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'form_description' ); ?>"><?php _e( 'Display Form Description:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'form_description' ); ?>" name="<?php echo $this->get_field_name( 'form_description' ); ?>" type="checkbox" value="1" <?php if( isset( $instance['form_description'] ) ) { checked( $instance['form_description'] , 1 ); } ?> />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'submit_button_text' ); ?>"><?php _e( 'Submit Button Text:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'submit_text' ); ?>" name="<?php echo $this->get_field_name( 'submit_text' ); ?>" type="text" value="<?php echo esc_attr( $submit_text ); ?>" />
		</p>
		<?php
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['form_id'] = $new_instance['form_id'];
		$instance['form_description'] = isset( $new_instance['form_description'] ) ? '1' : '';
		$instance['submit_text'] = ( ! empty( $new_instance['submit_text'] ) ) ? strip_tags( $new_instance['submit_text'] ) : 'Submit';
		return $instance;
	}
	
}
