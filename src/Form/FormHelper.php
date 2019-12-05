<?php

namespace YIKES\EasyForms\Form;

trait FormHelper {
    public function form_title( $title, $custom_title, $form_name ) {
        $form_title = '';
        switch( true ) {
            case ! empty( $custom_title ) && isset( $custom_title ):
                $form_title = $custom_title;
            break;
            case ! empty( $title ) && isset( $title ):
                $form_title = $title;
            break;
            default:
                $form_title = $form_name;
            break;
        }

        return apply_filters( 'yikes-mailchimp-form-title', apply_filters( 'the_title', $form_title ), $this->form_id );
    }

    public function form_description( $description, $custom_description ) {
        $form_description = '';
        switch( true ) {
            case ! empty( $custom_description ) && isset( $custom_description ):
                $form_description = $custom_description;
            break;
            default:
                $form_description = $description;
            break;
        }

        return apply_filters( 'yikes-mailchimp-form-description', $form_description, $this->form_id );
    }

    protected function reduce_field_count() {
		$this->field_count = $this->field_count --;
	}

	protected function set_field_count() {
		return (int) count( $this->form_data['fields'] );
	}

	public function form_classes( bool $is_submitted ) {
        $form_classes = 'yikes-easy-mc-form yikes-easy-mc-form-' . $this->form_id;
        if ( isset( $this->form_inline ) && $this->form_inline != 0 ) {
            $form_classes .= ' yikes-mailchimp-form-inline';
        }

        if ( $is_submitted && $this->form_data['submission_settings']['hide_form_post_signup'] == 1 ) {
            $form_classes .=  ' yikes-easy-mc-display-none';
        }

        $form_classes .= $this->form_data['form_settings']['yikes-easy-mc-form-class-names'];
        return apply_filters( 'yikes-mailchimp-form-class', $form_classes, $this->form_id );
	}

	public function inline_form_override() {
		return isset( $this->has_recaptcha ) || ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'eu-opt-in-compliance-for-mailchimp/yikes-inc-easy-mailchimp-eu-law-compliance-extension.php' ) );
	}
    
    public function edit_form_link() {
		if( current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
			$edit_form_link = '<span class="edit-link">';
			$edit_form_link .= '<a class="post-edit-link" href="' . esc_url( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $this->form_id ) ) . '" title="' . __( 'Edit' , 'yikes-inc-easy-mailchimp-extender' ) . ' ' . ucwords( $this->form_data['form_name'] ) . '">' . __( 'Edit Form' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
			$edit_form_link .= '</span>';
			$edit_form_link = apply_filters( 'yikes-mailchimp-front-end-form-action-links', $edit_form_link, $this->form_id, ucwords( $this->form_data['form_name'] ) );
		} else {
			$edit_form_link = '';
		}
		return $edit_form_link;
	}
}
