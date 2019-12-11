<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Form;

trait FieldBuilder {
    protected function get_field_classes( $field ) {
        $field_classes = [];
        $label_classes = [];

        $field_classes[] = 'yikes-easy-mc-' . $field['type'];
        $label_classes[] = $field['merge'] . '-label';

        if( $field['additional-classes'] != '' ) {

            $field_classes = explode( ' ' , $field['additional-classes'] );

            if( in_array( 'field-left-half' , $field_classes ) ) {
                $$label_classes[] = 'field-left-half';
                $key = array_search( 'field-left-half' , $field_classes );
                unset( $field_classes[$key] );
            } // input half right
            if( in_array( 'field-right-half' , $field_classes ) ) {
                $$label_classes[] = 'field-right-half';
                $key = array_search( 'field-right-half' , $field_classes );
                unset( $field_classes[$key] );
            } // input thirds (1/3 width, floated left)
            if( in_array( 'field-third' , $field_classes ) ) {
                $$label_classes[] = 'field-third';
                $key = array_search( 'field-third' , $field_classes );
                unset( $field_classes[$key] );
            } // 2 column radio
            if( in_array( 'option-2-col' , $field_classes ) ) {
                $$label_classes[] = 'option-2-col';
                $key = array_search( 'option-2-col' , $field_classes );
                unset( $field_classes[$key] );
            } // 3 column radio
            if( in_array( 'option-3-col' , $field_classes ) ) {
                $$label_classes[] = 'option-3-col';
                $key = array_search( 'option-3-col' , $field_classes );
                unset( $field_classes[$key] );
            } // 4 column radio
            if( in_array( 'option-4-col' , $field_classes ) ) {
                $$label_classes[] = 'option-4-col';
                $key = array_search( 'option-4-col' , $field_classes );
                unset( $field_classes[$key] );
            } // inline radio & checkboxes etc
            if( in_array( 'option-inline' , $field_classes ) ) {
                $$label_classes[] = 'option-inline';
                $key = array_search( 'option-inline' , $field_classes );
                unset( $field_classes[$key] );
            }
        }

        // if the form is set to inline, add the inline class to our labels
        if( $this->form_inline ) {
            $label_classes[] = 'label-inline';
        }
        
        if( isset( $field['hide-label'] ) ) {
            if( absint( $field['hide-label'] ) === 1 ) {
                $this->increase_hidden_label_count();
                $field_classes[] = 'field-no-label';
            }
        }

        return [
            'field_classes' => $field_classes,
            'label_classes' => $label_classes,
        ];
    }

    protected function increase_hidden_label_count() {
        $this->hidden_label_count = $this->hidden_label_count++;
    }

    protected function get_label( $field ) {
        $label = [];
        if( $field['type'] == 'email' ) {
            $label['props']['visible'] = '';
        } else {
            $label['props']['visible'] = isset( $field['hide'] ) ? 'style="display:none;"' : '';
        }
        if ( isset( $field['hide-label'] ) ) {
            $label['hide-label'] = true;
        }
        if ( isset( $field['label'] ) ) {
            $label['value'] = $field['label'];
        }
        return $label;
    }

    protected function get_value( $field ) {
        // pass our default value through our filter to parse dynamic data by tag (used solely for 'text' type)
        $default_value = ( isset( $field['default'] ) ? esc_attr( $field['default'] ) : '' );
        $default_value = apply_filters( 'yikes-mailchimp-process-default-tag', $default_value );
        return apply_filters( 'yikes-mailchimp-' . $field['merge'] . '-default-value', $default_value, $field, $this->form_id );
    }

    protected function get_placeholder( $field ) {
        return isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    }

    protected function get_hidden( $field ) {
        $visible = true;
        // if both hide label and hide field are checked, we gotta hide the field!
        if( isset( $field['hide' ] ) && $field['hide'] == 1 ) {
            if( isset( $field['hide-label' ] ) && $field['hide-label'] == 1 ) {
                $visible = false;
            }
        }
        return $visible;
    }

    protected function get_description( $field ) {
        $show_description  = false;
        $description_above = false;
        $description       = '';

        if ( isset( $field['description'] ) && trim( $field['description'] ) !== '' ) {
            $show_description = true;
            $description = $field['description'];
        }

        if ( isset( $field['description_above'] ) && $field['description_above'] === '1' ) {
            $description_above = true;
        }
        
        return [
            'show_description'  => $show_description,
            'description_above' => $description_above,
            'description'       => $description,
        ];
    }
}
