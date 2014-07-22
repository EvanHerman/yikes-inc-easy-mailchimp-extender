<?php
//
//	Widget Template
//

// Creating the widget 
class yikes_MC_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'yikes_MC_widget', 

			// Widget name will appear in UI
			__('MailChimp Signup Form', 'yikes-inc-easy-mailchimp-extender'), 

			// Widget description
			array( 'description' => __( 'MailChimp Signup Form', 'yikes-inc-easy-mailchimp-extender' ), ) 
		);
	}

		function get_imported_mc_lists($instance) {
			$imported_lists = get_option( 'imported_lists' );
			// print_r($imported_lists);
			if (!empty($imported_lists)) {
				?>
					<label for="<?php echo $this->get_field_id( 'selected_form' ); ?>"><?php _e('Select Which Form You Would Like To Display:','yikes-inc-easy-mailchimp-extender'); ?> <br />
							<select id="<?php echo $this->get_field_id('selected_form'); ?>" class="yikes_mc_widget_selected_form_dropdown" name="<?php echo $this->get_field_name('selected_form'); ?>" type="text">
									<!-- construct selectable options based on imported lists -->
									<option value="Select a Form to Display"><?php _e('Select a Form to Display','yikes-inc-easy-mailchimp-extender'); ?></option>
									<?php 
									foreach ($imported_lists as $list_id) { 
											$listID = $list_id['id'];
											$listName = $list_id['name'];
											// determine if instance['selected_form'] isset to avoid PHP warnings
											if ( isset ( $instance["selected_form"] ) ) {
												?><option value="<?php echo $listID; ?>" <?php selected($instance["selected_form"], $listID ); ?>><?php echo $listName; ?></option><?php
											} else {
												?><option value="<?php echo $listID; ?>"><?php echo $listName; ?></option><?php
											}
									 } ?>
							</select>
					</label>
			<?php
			} else {
			?>
				<label for="yikes-no-list-imported"></br ><?php _e( 'Please import MailChimp forms','yikes-inc-easy-mailchimp-extender'); ?> 
					<select id="" name="" class="yikes_mc_widget_selected_form_dropdown" type="text" disabled="disabled">
						<option><?php _e( 'Please import some lists from MailChimp','yikes-inc-easy-mailchimp-extender'); ?></option>
					</select>
				</label>
				<a href="admin.php?page=yks-mailchimp-form-lists"><?php _e('Import Lists Now','yikes-inc-easy-mailchimp-extender'); ?></a>
			<?php
			}	
		}

		// Creating widget front-end
		// This is where the action happens
		public function widget( $args, $instance ) {
			$imported_lists = get_option( 'imported_lists' );
			$submit_button_text = $instance["submit_button_text"];
			
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];

			// This is where you run the code and display the output
			// if we have imported lists, do the shortcode
			// else display an error
					if (empty($imported_lists)) {
						?>
							<div class="yikes-mailchimp-widget-error" style="text-align:center;">
								<img src="<?php echo plugins_url(); ?>/yikes-inc-easy-mailchimp-extender/images/yikes_logo.png" class="yikes_widget_logo" />
								<p><?php _e( "Oops! It looks like you haven't imported any lists yet. You must import at least one list to use the Easy MailChimp widget." , "yikes-inc-easy-mailchimp-extender" ); ?></p>
							</div>
						<?php	
						} elseif (!empty($imported_lists) && $instance['selected_form'] == 'Select a Form to Display' || !empty($imported_lists) && !$instance['selected_form'] ) {
						?>
							<div class="yikes-mailchimp-widget-error" style="text-align:center;">
								<img src="<?php echo plugins_url(); ?>/yikes-inc-easy-mailchimp-extender/images/yikes_logo.png" class="yikes_widget_logo" />
								<p><?php _e( "Oops! It looks like you forgot to select a form to display here. Go to 'Appearance > Widgets' and select a form to display." , "yikes-inc-easy-mailchimp-extender" ); ?></p>
							</div>
						<?php	
						
						} else {
			
							$title = apply_filters( 'widget_title', $instance['title'] );
							
							if ( !empty( $title ) ) {
								echo $args['before_title'] . $title . $args['after_title'];
							}
								if ( isset ( $instance['form_description'] ) ) {
									echo '<p class="yikes-mailchimp-form-description yikes-mailchimp-form-description-'.$instance['selected_form'].'">'.$instance['form_description'].'</p>';
								} 
					
							echo do_shortcode('[yks-mailchimp-list id="'.$instance["selected_form"].'" submit_text="'.$instance["submit_button_text"].'"]');
													
						}
				
			echo $args['after_widget'];
				
		}
			
		// Widget Backend 
		 function form($instance) {    			
		 
			$is_api_valid = get_option('api_validation');
		 
			if( $is_api_valid == 'valid_api_key' ) {
				
				$instance = wp_parse_args( (array) $instance );
				
				// check and store values for each field
					//title
					if ( isset( $instance[ 'title' ] ) ) {
						$title = $instance[ 'title' ];
					} else {
						$title = __( 'Sign Up For Our Newsletter', 'yikes-inc-easy-mailchimp-extender' );
					}
					// form description
					if ( isset( $instance[ 'form_description' ] ) ) {
						$form_description = $instance[ 'form_description' ];
					} else {
						$form_description = '';
					}
					// submit button text
					if ( isset( $instance[ 'submit_button_text' ] ) ) {
						$submit_button_text = $instance[ 'submit_button_text' ];
					} else {
						$submit_button_text = __( 'Sign Me Up', 'yikes-inc-easy-mailchimp-extender' );
					}
			 ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','yikes-inc-easy-mailchimp-extender'); ?>
						<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
					</label> 
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'form_description' ); ?>"><?php _e( 'Form Text:','yikes-inc-easy-mailchimp-extender'); ?>
						<textarea id="<?php echo $this->get_field_id( 'form_description' ); ?>" placeholder="Enter a short message to attract subscribers!" class="yikes_widget_form_description" name="<?php echo $this->get_field_name( 'form_description' ); ?>" type="text" value="<?php echo esc_attr( $form_description ); ?>"><?php echo esc_attr( $form_description ); ?></textarea>
					</label> 
				</p>
				<p>
					<?php $this->get_imported_mc_lists($instance); ?>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'submit_button_text' ); ?>"><?php _e( 'Submit Button Text:','yikes-inc-easy-mailchimp-extender'); ?><br />
						<input class="widefat" id="<?php echo $this->get_field_id( 'submit_button_text' ); ?>" name="<?php echo $this->get_field_name( 'submit_button_text' ); ?>" type="text" value="<?php echo esc_attr( $submit_button_text ); ?>" />
					</label> 
				</p>
			<?php
			} else {
				?>
					<div class="yikes-mailchimp-widget-error" style="text-align:center;">
						<br />
						<img src="<?php echo plugins_url(); ?>/yikes-inc-easy-mailchimp-extender/images/yikes_logo.png" class="yikes_widget_logo" />
						<p><?php _e( "Oops! It looks like you haven't added your API key! Head over to the" , "yikes-inc-easy-mailchimp-extender" ); ?> <a href="admin.php?page=yks-mailchimp-form" class="yks-mailchimp-list-add">MailChimp <?php _e('Settings page','yikes-inc-easy-mailchimp-extender'); ?></a> <?php _e('and add your API Key.','yikes-inc-easy-mailchimp-extender'); ?></p>
					</div>
				<?php
			}
		 }

		// Updating widget replacing old instances with new
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( trim($new_instance['title']) ) : 'Sign Up For Our Newsletter';
			$instance['selected_form'] = strip_tags( $new_instance['selected_form'] );
			$instance['submit_button_text'] = ( ! empty( $new_instance['submit_button_text'] ) ) ? strip_tags( trim($new_instance['submit_button_text']) ) : 'Sign Me Up';
			$instance['form_description'] = ( ! empty( $new_instance['form_description'] ) ) ? strip_tags( trim($new_instance['form_description']) ) : '';
			return $instance;
		}
	
} // Class yikes_MC_widget ends here

// Register and load the widget
function yikes_MC_load_widget() {
	register_widget( 'yikes_MC_widget' );
}
add_action( 'widgets_init', 'yikes_MC_load_widget' );