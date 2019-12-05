<?php

namespace YIKES\EasyForms\Form;

trait SubmitButton {

    public function submit_button_props() {
		return [
			'type' => $this->form_data['form_settings']['yikes-easy-mc-submit-button-type'],
			'image' => esc_url( $this->form_data['form_settings']['yikes-easy-mc-submit-button-image'] ),
            'classes' => esc_attr( $this->form_data['form_settings']['yikes-easy-mc-submit-button-classes'] ),
		];
    }

    public function submit_button_classes() {
        $submit_button_classes = 'yikes-easy-mc-submit-button yikes-easy-mc-submit-button-';
        $submit_button_classes .= $this->form_id;
        $submit_button_classes .= ' btn btn-primary';
        // Adding additional space in front of these classes.
        $submit_button_classes .= ' ' . $this->form_data['form_settings']['yikes-easy-mc-submit-button-classes'];
        $submit_button_classes .= $this->admin_class;
        return apply_filters( 'yikes-mailchimp-form-submit-button-classes', $submit_button_classes, $this->form_id );
    }

    public function submit_button_text( $shortcode_prop ) {
        $submit_button_text = '';
        switch( true ) {
            case ! empty( $shortcode_prop ):
                $submit_button_text = $shortcode_prop;
            break;

            case $this->form_data['form_settings']['yikes-easy-mc-submit-button-text']:
                $submit_button_text = $this->form_data['form_settings']['yikes-easy-mc-submit-button-text'];
            break;

            default:
                $submit_button_text = __( 'Submit', 'yikes-inc-easy-mailchimp-extender' );
            break;
        }
        return apply_filters( 'yikes-mailchimp-form-submit-button-text', $submit_button_text, $this->form_id );
    }
}