<?php 
	/*
	*	Main class file that houses many of our
	*	Helper functions (adding custom sections to edit form page etc.)
	*	@since 6.0
	*/
	class Yikes_Inc_Easy_Mailchimp_Extender_Helper {
	
			/**
			*	Helper functions to help out with extensions (still fleshing out)
			*	@since 6.0
			*	@ Parameters (array of data)
			*		-	Section ID - id of the section, should be a slug style text ie: 'custom-section'
			*		-	Link Text - Visible text for this link ie: 'Custom Section'
			*		-	Dashicon - class of the icon you would like to use for this link
			**/
			public static function add_edit_form_section_link( $link_array=array() ) {
				if( !empty( $link_array ) ) {
					$link_data = wp_parse_args( array() , $link_array );
					if( !empty( $link_data['text'] ) && !empty( $link_data['id'] ) ) {
						if( !empty( $link_data['icon'] ) ) {
							if( !isset( $link_data['icon_family'] ) || $link_data['icon_family'] == 'dashicons' || $link_data['icon_family'] == 'dashicon' ) {
								$icon =  '<span class="dashicons dashicons-' . esc_attr__( $link_data['icon'] ) . ' yikes-easy-mailchimp-custom-content-icon"></span>';
							} else {
								$icon =  '<span class="' . esc_attr__( $link_data['icon'] ) . ' yikes-easy-mailchimp-custom-content-icon"></span>';
							}
						} else {
							$icon = '';
						}
						$link = '<li class="hidden_setting_list">';
							$link .= '<a class="hidden_setting ' . esc_attr__( $link_data['id'] ) . '" data-attr-container="' . esc_attr__( $link_data['id'] ) . '" onclick="return false;" title="' . esc_attr__( $link_data['text'] ) . '" href="#">' . $icon . esc_attr__( $link_data['text'] ) . '</a>';
						$link .= '</li>';
						echo $link;
					}
				}
			}
			
			/**
			*	Helper functions to help out with extensions (still fleshing out)
			*	@since 6.0
			*	@ Parameters:
			*		-	Section ID - id of the section, should be a slug style text ie: 'custom-section'
			*		-	Class - class file to call function from?
			*		-	Main Callback - call back for main section
			*		-	Main Section Title - main section title
			*		-	Sidebar Callback - callback for the sidebar section
			*		-	Sidebar Title - title of the sidebar section
			*		-	Class - class to reference funtions out of (optiona, if left blank functions should be defined in functions.php (or outside of a class))
			**/
			public static function add_edit_form_section( $section_array=array() ) {
				if( !empty( $section_array ) ) {
					$section_data = wp_parse_args( array() , $section_array );
					ob_start();
					include ( YIKES_MC_PATH . 'admin/partials/helpers/edit-form-hidden-section-template.php' );
					$section = ob_get_contents();
					ob_end_clean();
					echo $section;
				}
			}
			
			/**
			*	Check if the custom section is single or two columns (with sidebar)
			*	@since 6.0
			*	@Parameters:
			*		-	Section Data - the array of data associated with the custom field you've set up
			*/
			public static function is_custom_section_two_column( $custom_section_data ) {
				// print_r( $custom_section_data );
				$value = ( isset( $custom_section_data['sidebar_title'] ) && isset( $custom_section_data['sidebar_fields'] ) && !empty( $custom_section_data['sidebar_fields'] ) ) ?  true : false;
				return $value;
			}
			
	}
	new Yikes_Inc_Easy_Mailchimp_Extender_Helper;

?>