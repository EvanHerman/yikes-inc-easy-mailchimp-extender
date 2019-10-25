import EasyFormsAPI from './components/api.js'

import MailchimpForms from './components/class.MailchimpForms.js';

import '../css/easy-forms-block.scss'

require( './components/enable-submit-button-editing.js' );

// Get just the __() localization function from wp.i18n
const { __ } = wp.i18n;

// Get registerBlockType and other methods from wp.blocks
const { registerBlockType } = wp.blocks;

const edit_easy_form = function( props ) {

  const onChangeForm = ( event ) => {
    props.setAttributes( { form_id: event.target.value } );

    if ( event.target.value.length > 0 ) {
      const api = new EasyFormsAPI();
      api.get_form( event.target.value ).then( function( form ) {
        props.setAttributes( { form: form.data } );
        props.setAttributes( { form_description: form.data.form_description } );
        props.setAttributes( { form_title: form.data.form_name } );
        props.setAttributes( { is_ajax: form.data.submission_settings.ajax === '1' } );
        props.setAttributes( { submit_button_text: form.data.form_settings['yikes-easy-mc-submit-button-text'] } );
      });
    }
  };

  const onChangeDescription = ( value ) => {
    props.setAttributes( { form_description: value } );
  };

  const toggleShowDescription = ( event ) => {
    props.setAttributes( { show_description: !! event.target.checked } );
  }

  const toggleInline = ( event ) => {
    props.setAttributes( { inline: !! event.target.checked } );
  }

  const toggleShowTitle = ( event ) => {
    props.setAttributes( { show_title: !! event.target.checked } );
  }

  const toggleFormTitle = ( value ) => {
    props.setAttributes( { form_title: value } );
  }

  const toggleIsAjax = ( event ) => {
    props.setAttributes( { is_ajax: !! event.target.checked } );
  }

  /* Allow this function to be called via a checkbox handler or directly by passing in a boolean */
  const toggleRecaptchaAbstract = ( checked ) => {
    props.setAttributes( { recaptcha: checked } );
  }

  const toggleRecaptcha = ( event ) => {
    toggleRecaptchaAbstract( !! event.target.checked )
  }

  const toggleRecaptchaTheme = ( value ) => {
    props.setAttributes( { recaptcha_theme: value } );
  }

  const toggleRecaptchaLang = ( value ) => {
    props.setAttributes( { recaptcha_lang: value } );
  }

  const toggleRecaptchaType = ( value ) => {
    props.setAttributes( { recaptcha_type: value } );
  }

  const toggleRecaptchaSize = ( value ) => {
    props.setAttributes( { recaptcha_size: value } );
  }

  const toggleRecaptchaVerifyCallback = ( value ) => {
    props.setAttributes( { recaptcha_verify_callback: value } );
  }

  const toggleRecaptchaExpiredCallback = ( value ) => {
    props.setAttributes( { recaptcha_expired_callback: value } );
  }

  const toggleSubmitButtonText = ( value ) => {
    props.setAttributes( { submit_button_text: value })
  }

  return (
    <MailchimpForms
      className={ props.className }
      onChangeForm={ onChangeForm }
      formID={ props.attributes.form_id }
      formData={ props.attributes.form }
      onChangeDescription={ onChangeDescription }
      descriptionValue={ props.attributes.form_description }
      showDescription={ props.attributes.show_description }
      toggleShowDescription={ toggleShowDescription }
      focus={ !! props.isSelected }
      inline={ props.attributes.inline }
      toggleInline={ toggleInline }
      formTitle={ props.attributes.form_title }
      toggleFormTitle={ toggleFormTitle }
      showTitle={ props.attributes.show_title }
      toggleShowTitle={ toggleShowTitle }
      isAjax={ props.attributes.is_ajax }
      toggleIsAjax={ toggleIsAjax }
      toggleRecaptchaAbstract={ toggleRecaptchaAbstract }
      recaptcha={ props.attributes.recaptcha }
      toggleRecaptcha={ toggleRecaptcha }
      recaptchaTheme={ props.attributes.recaptcha_theme }
      toggleRecaptchaTheme={ toggleRecaptchaTheme }
      recaptchaLang={ props.attributes.recaptcha_lang }
      toggleRecaptchaLang={ toggleRecaptchaLang }
      recaptchaType={ props.attributes.recaptcha_type }
      toggleRecaptchaType={ toggleRecaptchaType }
      recaptchaSize={ props.attributes.recaptcha_size }
      toggleRecaptchaSize={ toggleRecaptchaSize }
      recaptchaVerifyCallback={ props.attributes.recaptcha_verify_callback }
      toggleRecaptchaVerifyCallback={ toggleRecaptchaVerifyCallback }
      recaptchaExpiredCallback={ props.attributes.recaptcha_expired_callback }
      toggleRecaptchaExpiredCallback={ toggleRecaptchaExpiredCallback }
      submitButtonText={ props.attributes.submit_button_text }
      toggleSubmitButtonText={ toggleSubmitButtonText }
    />
  );

}

const save_easy_form = function( props ) {
  return null;
}

const settings = {
  title     : __( 'Easy Forms for Mailchimp' ),
  category  : 'easy-forms', // Options include "common", "formatting", "layout", "widgets" and "embed."
  icon      : 'email-alt',
  keywords  : ['mailchimp', 'easy forms for mailchimp', 'yikes'],
  attributes:  {
    form_id: {
      type: 'string',
      default: ''
    },
    form: {
      type: 'object'
    },
    form_description: {
      type: 'string',
      default: ''
    },
    show_description: {
      type: 'boolean',
      default: false
    },
    inline: {
      type: 'boolean',
      default: false
    },
    show_title: {
      type: 'boolean',
      default: false
    },
    form_title: {
      type: 'string',
      default: ''
    },
    is_ajax: {
      type: 'boolean',
      default: true,
    },
    recaptcha: {
      type: 'boolean',
      default: false,
    },
    recaptcha_theme: {
      type: 'string',
      default: 'light'
    },
    recaptcha_lang: {
      type: 'string',
      default: ''
    },
    recaptcha_type: {
      type: 'string',
      default: 'image'
    },
    recaptcha_size: {
      type: 'string',
      default: 'normal'
    },
    recaptcha_verify_callback: {
      type: 'string',
      default: ''
    },
    recaptcha_expired_callback: {
      type: 'string',
      default: ''
    },
    submit_button_text: {
      type: 'string',
      default: ''
    }
  },
  edit: edit_easy_form,
  save: save_easy_form,
}

const EasyFormsBlock = registerBlockType(

  // Name
  ez_forms_gb_data.block_namespace + ez_forms_gb_data.block_name,

  // Settings

  settings
);