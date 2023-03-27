// Import dependencies
import slugify from './slugify.js';
import constants from './constants.js';
import EasyFormsAPI from './api.js';

// Get functions / blocks / components
const Recaptcha = require( 'react-recaptcha' );
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls, PlainText } = wp.editor;
const { Spinner, TextControl, PanelBody, PanelRow, FormToggle, SelectControl } = wp.components;
const { Component } = wp.element;

export default class MailchimpForms extends Component {
  constructor( props ) {
    super( ...arguments );
    this.state = {
      api_key_status: 'valid',
      forms         : [],
      recaptcha_data: {
        data   : {},
        success: false
      },
      'forms_loaded': false
    }
    
    this.api = new EasyFormsAPI();

    this.address_fields = {
      'addr1'  : 'Address 1', 
      'addr2'  : 'Address 2', 
      'city'   : 'City', 
      'state'  : 'State', 
      'zip'    : 'Zip',
      'country': 'Country'
    }
  }

  /**
   * Run our API calls after the component has mounted. You can't use setState before a component is mounted.
   */
  componentDidMount() {
    this.api.get_api_key_status().then( status => {
      this.setState( { api_key_status: status.data } );
    });

    this.api.get_forms().then( forms => {
      this.setState( { forms: forms.data, forms_loaded: true } );
    });

    this.api.get_recaptcha().then( recaptcha_data => {
      this.setState( { recaptcha_data: recaptcha_data } );
      this.props.toggleRecaptchaAbstract( this.state.recaptcha_data.success );
    });
  }

  handleFormFieldChanges( event ) {
    // console.log( event );

    // console.log( value );
    // console.log( this );
    // console.log( typeof this.setState );

    // const target = event.target;
    //  const value  = target.type === 'checkbox' ? target.checked : target.value;
    //  const name   = target.name;

    // return this.setState( { [name]: value } );
  }

  inspector_controls() {

    const inline = (
      <PanelRow>
        <label
          htmlFor="inline-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'Inline' ) }
        </label>
        <FormToggle
          id="inline-form-toggle"
          label={ __( 'Inline' ) }
          checked={ !! this.props.inline }
          onChange={ this.props.toggleInline }
        />
      </PanelRow>
    )

    const show_form_title = (
      <PanelRow>
        <label
          htmlFor="show-title-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'Show Form Title' ) }
        </label>
        <FormToggle
          id="show-title-form-toggle"
          label={ __( 'Show Form Title' ) }
          checked={ !! this.props.showTitle }
          onChange={ this.props.toggleShowTitle }
        />
      </PanelRow>
    )

    const show_form_description = (
      <PanelRow>
        <label
          htmlFor="show-description-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'Show Form Description' ) }
        </label>
        <FormToggle
          id="show-description-form-toggle"
          label={ __( 'Show Form Description' ) }
          checked={ !! this.props.showDescription }
          onChange={ this.props.toggleShowDescription }
        />
      </PanelRow>
    )

    const is_ajax = (
      <PanelRow>
        <label
          htmlFor="is-ajax-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'AJAX Submissions' ) }
        </label>
        <FormToggle
          id="is-ajax-form-toggle"
          label={ __( 'AJAX' ) }
          checked={ !! this.props.isAjax }
          onChange={ this.props.toggleIsAjax }
        />
      </PanelRow>
    )

    const recaptcha = (
      <PanelRow>
        <label
          htmlFor="recaptcha-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'reCAPTCHA' ) }
        </label>
        <FormToggle
          id="recaptcha-form-toggle"
          label={ __( 'reCAPTCHA' ) }
          checked={ !! this.props.recaptcha }
          onChange={ this.props.toggleRecaptcha }
        />
      </PanelRow>
    )

    const recaptcha_type = !! this.props.recaptcha ?
    (
      <PanelRow>
        <label
          htmlFor="recaptcha-type-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'reCAPTCHA Type' ) }
        </label>
        <SelectControl
          value={ this.props.recaptchaType }
          options={ [ { value: 'image', label: 'Image' }, { value: 'audio', 'label': 'Audio' } ] }
          onChange={ this.props.toggleRecaptchaType }
        />
      </PanelRow>
    )
    : '';

    const recaptcha_theme = !! this.props.recaptcha ?
    (
      <PanelRow>
        <label
          htmlFor="recaptcha-theme-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'reCAPTCHA Theme' ) }
        </label>
        <SelectControl
          value={ this.props.recaptchaTheme }
          options={ [ { value: 'light', label: 'Light' }, {value: 'dark', 'label': 'Dark' } ] }
          onChange={ this.props.toggleRecaptchaTheme }
        />
      </PanelRow>
    )
    : '';

    const recaptcha_lang = !! this.props.recaptcha ?
    (
      <PanelRow>
        <label
          htmlFor="recaptcha-language-form-toggle"
          className="blocks-base-control__label"
          title={ this.state.recaptcha_data.data ? 'The default language for your locale is ' + constants.locales[ this.state.recaptcha_data.data.locale ] : '' }
        >
          { __( 'reCAPTCHA Language' ) }
        </label>
        <SelectControl
          id="recaptcha-language-form-toggle"
          value={ this.props.recaptchaLang.length > 0 ? this.props.recaptchaLang : ( this.state.recaptcha_data.data ? this.state.recaptcha_data.data.locale : '' ) }
          onChange={ this.props.toggleRecaptchaLang }
          title={ this.state.recaptcha_data.data ? 'The default language for your locale is ' + constants.locales[ this.state.recaptcha_data.data.locale ] : '' }
          options={ Object.keys( constants.locales ).map( ( key ) => { return { value: key, label: constants.locales[key] } }) }
        />
      </PanelRow>
    )
    : '';

    const recaptcha_size = !! this.props.recaptcha ?
    (
      <PanelRow>
        <label
          htmlFor="recaptcha-size-form-toggle"
          className="blocks-base-control__label"
        >
          { __( 'reCAPTCHA Size' ) }
        </label>
        <SelectControl
          id="recaptcha-size-form-toggle"
          value={ this.props.recaptchaSize }
          onChange={ this.props.toggleRecaptchaSize }
          options={ [ { value: 'normal', label: 'Normal' }, { value: 'compact', label: 'Compact' } ] }
        />
      </PanelRow>
    )
    : '';

    const recaptcha_verify_callback = !! this.props.recaptcha ?
    (
      <TextControl
        id="recaptcha-verify-callback-form-toggle"
        label={ __( 'reCAPTCHA Verify Callback' ) }
        value={ this.props.recaptchaVerifyCallback }
        onChange={ this.props.toggleRecaptchaVerifyCallback }
      />
    )
    : '';

    const recaptcha_expired_callback = !! this.props.recaptcha ?
    (
      <TextControl
        id="recaptcha-expired-callback-form-toggle"
        label={ __( 'reCAPTCHA Expired Callback' ) }
        value={ this.props.recaptchaExpiredCallback }
        onChange={ this.props.toggleRecaptchaExpiredCallback }
      />
    )
    : '';

    const inspector_controls = 
     <InspectorControls key="easy-forms-inspector">

      <PanelBody title={ __( 'Settings' ) } >
        {inline}
        {show_form_title}
        {show_form_description}
        {is_ajax}
        {recaptcha}
        {recaptcha_type}
        {recaptcha_theme}
        {recaptcha_lang}
        {recaptcha_size}
        {recaptcha_verify_callback}
        {recaptcha_expired_callback}
      </PanelBody>

    </InspectorControls>

    return inspector_controls;
  }

  forms_dropdown() {
    return this.state.forms.length > 0 ? 
      <select 
        value={ this.props.formID } 
        onChange={ this.props.onChangeForm } 
        className="yikes-mailchimp-forms-dropdown"
        key="yikes-mailchimp-forms-dropdown"
      >
        <option key="select" value="">Select Mailchimp Form...</option>
        { this.state.forms.map( ( form ) => {
          return <option key={ 'yikes-select-form-' + form.id } value={ form.id }>{ form.form_name }</option>
        })}
      </select> 
    : '';
  }

  form_title() {
    return this.props.showTitle === true ?
      ( this.props.focus ? 
        <h3 
          className={ "yikes-mailchimp-form-title yikes-mailchimp-form-title-" + this.props.formID }
          key="yikes-mailchimp-form-title-wrapper"
         >
          <PlainText
            placeholder={ this.props.formTitle }
            key="yikes-mailchimp-form-title-editable"
            value={ this.props.formTitle }
            onChange={ this.props.toggleFormTitle }
          />
        </h3>
        :
        <h3 className={ "yikes-mailchimp-form-title yikes-mailchimp-form-title-" + this.props.formID } key="yikes-mailchimp-form-title">
          { this.props.formTitle }
        </h3> 
      ) : '';    
  }

  form_description() {
    return this.props.showDescription === true ?
      <section className={ "yikes-mailchimp-form-description yikes-mailchimp-form-description-" + this.props.formID } key="yikes-mailchimp-form-description">
        <PlainText
          className="mailchimpDescription"
          placeholder={ __( 'Enter your form\'s description here.' ) /*( this.props.formData && this.props.formData.form_description && this.props.formData.form_description.length > 0 || this.props.descriptionValue && this.props.descriptionValue.length > 0 ) ? '' : __( 'Enter your form\'s description here.' )*/ }
          value={ this.props.descriptionValue.length > 0 ? this.props.descriptionValue : ( this.props.formData && this.props.formData.form_description ? this.props.formData.form_description : '' ) }
          onChange={ this.props.onChangeDescription }
          key="mailchimpDescription"
         />
      </section> 
      : '';    
  }

  get_address_field( addr_field, field ) {
    switch( addr_field ) {

      case 'addr1':
      case 'addr2':
      case 'city':
      case 'zip':
        return (
          <TextControl
            id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field }
            onChange={ this.handleFormFieldChanges }
            type="text"
            name={ field.merge + '[' + addr_field + ']' }
            className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
            key= { 'yikes-mailchimp-field-' + field.merge + '-' + addr_field }
            required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
            placeholder={ field.placeholder === '1' ? this.address_fields[ addr_field ] : '' }
          />
        )
      break;

      case 'state':
      return (
        <select 
          id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field } 
          name={ field.merge + '[' + addr_field + ']' }
          className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
          onChange={ this.handleFormFieldChanges }
          key= { 'yikes-mailchimp-field-' + field.merge + '-' + addr_field }
          value=''
          required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
        >
          { Object.keys( constants.states ).map( ( key ) => {
            var choice = constants.states[ key ];
            return <option key={ 'state-' + key } value={ key }>{ choice }</option>
          })}
            
        </select>
      )
      break;

      case 'country':
        return (
          <select 
            id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field } 
            name={ field.merge + '[' + addr_field + ']' }
            className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
            onChange={ this.handleFormFieldChanges }
            key= { 'yikes-mailchimp-field-' + field.merge + '-' + addr_field }
            value=''
            required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
          >
            { Object.keys( constants.countries ).map( ( key ) => {
              var choice = constants.countries[ key ];
              return <option key={ 'country-' + key } value={ key }>{ choice }</option>
            })}
              
          </select>
        )
      break;
    }
  }

  get_dropdown_field( field ) {
    return (
      <select 
        id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge }
        name={ field.merge }
        className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
        onChange={ this.handleFormFieldChanges }
        key= { 'yikes-mailchimp-field-' + field.merge }
        value={ JSON.parse( field.choices )[ field.default_choice ] }
        required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
      >
          { Object.keys( JSON.parse( field.choices ) ).map( ( key ) => {
            var choice = JSON.parse( field.choices )[ key ];
            return <option key={ slugify( choice ) } value={ choice }>{ choice }</option>
          })}
      </select>
    )
  }

  get_radio_field( field ) {
    var ii = 0;

    return (
      JSON.parse( field.choices ).map( ( key ) => {
        ii++;
        return (
          <label 
            htmlFor={ field.merge + '-' + ii }
            className="yikes-easy-mc-checkbox-label"
            key={ field.merge + '-label-radio-key-' + ii }
          >
            <input
              type="radio"
              name={ field.merge }
              id={ field.merge + '-' + ii }
              key={ field.merge + '-' + ii + '-input-key' }
              value={ key }
              onChange={ this.handleFormFieldChanges }
              checked={ parseInt( field.default_choice ) === ( ii - 1 ) }
            />
            <span key={ field.merge + '-span-radio-key-' + ii } className={ field.merge + '-label' }>{ key }</span>
          </label>
        )
      })
    )
  }

  get_url_field( field ) {
    return (
      <TextControl
        id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge }
        placeholder={ field.placeholder }
        onChange={ this.handleFormFieldChanges }
        type='url'
        value={ field.default }
        name={ field.merge }
        className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
        key= { 'yikes-mailchimp-field-' + field.merge }
        required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
        title={ field.type === 'url' ? __( 'Please enter a valid URL to the website.' ) : __( 'Please enter a valid URL to the image.' ) }
      />
    )
  }

  get_default_field( field ) {

    var type = field.type === 'zip' || field.type === 'phone' ? 'text' : field.type;

    return (
      <TextControl
        id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge }
        placeholder={ field.placeholder }
        onChange={ this.handleFormFieldChanges }
        type={ type }
        value={ field.default }
        name={ field.merge }
        className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
        key= { 'yikes-mailchimp-field-' + field.merge }
        required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
      />
    )
  }

  get_date_field( field ) {
    return (
      <TextControl
        id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge }
        placeholder={ field.placeholder }
        onChange={ this.handleFormFieldChanges }
        type='text'
        value={ field.default }
        name={ field.merge }
        className={ 'yikes-easy-mc-' + field.type + ' ' + field['additional-classes'] }
        key= { 'yikes-mailchimp-field-' + field.merge }
        required={ field.merge === 'EMAIL' || field.require === '1' ? 'required' : false }
        data-attr-type={ field.type }
        data-date-format={ field.date_format.toLowerCase() }
      />
    )
  }

  get_radio_interest_group( field ) {
    var ii = -1;
    var cn = "yikes-easy-mc-checkbox-label " + field['additional-classes'];

    return (
      Object.keys( JSON.parse( field.groups ) ).map( ( key ) => {
        var choice = JSON.parse( field.groups )[ key ];
        ii++;
        return (
          <label
            htmlFor={ field.group_id + '-' + ii }
            className={ { [cn]: true, 'field-no-label': field['hide-label'] === '1' }  }
            key={ field.group_id + '-' + ii + '-label-key' }
          >
            <input
              type="radio"
              name={"group-" + field.group_id + '[]' }
              id={ field.group_id + '-' + ii }
              key={ field.group_id + '-' + ii + '-input-key' }
              value={ key }
              onChange={ this.handleFormFieldChanges }
              checked={ key === field.default_choice }
              className={ { 'yikes-interest-group-required': field.require === '1' } }
            />
            { choice }
          </label>
        )
      })
    )
  }

  get_hidden_interest_group( field ) {
    var ii = -1;

    return (
      Object.keys( JSON.parse( field.groups ) ).map( ( key ) => {
        var choice = JSON.parse( field.groups )[ key ];
        ii++;
        return (
          <label
            htmlFor={ field.group_id + '-' + ii }
            className={ 'yikes-easy-mc-checkbox-label ' + field['additional-classes'] }
            key={ field.group_id + '-' + ii + '-label-key' }
          >
            <input
              type="checkbox"
              name={"group-" + field.group_id + '[]' }
              id={ field.group_id + '-' + ii }
              key={ field.group_id + '-' + ii + '-input-key' }
              value={ key }
              onChange={ this.handleFormFieldChanges }
              checked={ field.default_choice && field.default_choice.indexOf( key ) !== -1 }
            />
            { choice }
          </label>
        )
      })
    )
  }

  get_checkboxes_interest_group( field ) {
    var ii = -1;
    var cn = 'yikes-easy-mc-checkbox-label ' + field['additional-classes'];

    return (
      Object.keys( JSON.parse( field.groups ) ).map( ( key ) => {
        var choice = JSON.parse( field.groups )[ key ];
        ii++;
        return (
          <label
            htmlFor={ field.group_id + '-' + ii }
            className={ { [cn]: true, 'field-no-label': field['hide-label'] === '1' }  }
            key={ field.group_id + '-' + ii + '-label-key' }
          >
            <input
              className={ { 'yikes-interest-group-required': field.require === '1' } }
              type="checkbox"
              name={ 'group-' + field.group_id + '[]' }
              id={ field.group_id + '-' + ii }
              key={ field.group_id + '-' + ii + '-input-key' }
              value={ key }
              onChange={ this.handleFormFieldChanges }
              checked={ typeof field.default_choice !== 'undefined' && field.default_choice.indexOf( key ) !== -1 }
              required={ field.require === '1' ? 'required' : false }
            />
            { choice }
          </label>
        )
      })
    )
  }

  get_dropdown_interest_group( field ) {
    var ii = -1;

    return (
      <select 
        id={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.group_id } 
        name={ "group-" + field.group_id }
        className={ "yikes-easy-mc-dropdown " + field['additional-classes'] }
        value={ field.default_choice }
        onChange={ this.handleFormFieldChanges }
        required={ field.require === '1' ? 'required' : false }
      >

      { Object.keys( JSON.parse( field.groups ) ).map( ( key ) => {
          var choice = JSON.parse( field.groups )[ key ];
          ii++;
          return (
            <option key={ field.group_id + '-' + ii + '-input-key' } value={ key }>{ choice }</option>
          )
        })}
      </select>
    )
  }

  form_fields() {

    return this.props.formData && this.props.formData.fields ?
      Object.keys( this.props.formData.fields ).map( ( key ) => {

          var field = this.props.formData.fields[ key ];

          // Form Fields (aka merge variables)
          if ( typeof field.merge !== 'undefined' ) {

            var desc    = field.description.length > 0 ? 
              <p className="form-field-description" id={ "form-field-description-" + field.merge } key={ "form-field-description-" + field.merge }>{ field.description }</p>
            : '';

            var label   = field['hide-label'] !== '1' ? <span className={ field.merge + '-label' } key={ field.merge + '-label-span-key' }>{ field.label }</span> : '';

            var classes = {'yikes-mailchimp-field-required' : field.merge === 'EMAIL' || field.require === '1' }
            classes[ field.merge + '-label'] = true;

            if ( field.type === 'address' ) {

              return ([
                field.description_above === '1' && field.description.length > 0 && desc,
                  Object.keys( this.address_fields ).map( ( addr_field ) => {
                    label = field['hide-label'] !== '1' ? <span className={ field.merge + '-label' } key={ field.merge + '-label-span-key-' + addr_field }>{ this.address_fields[ addr_field ] }</span> : '';

                    return (
                      <label 
                        htmlFor={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field } 
                        className={ classes } 
                        key={ field.merge + '-label-key-' + addr_field }
                        style={ field.hide === '1' ? { 'display': 'none' } : {} }
                      >
                        { label }
                        { this.get_address_field( addr_field, field ) }
                      </label>                  
                    )
                  }),
                field.description_above !== '1' && field.description.length > 0 && desc
              ])
              } else {
              return (
                <label 
                  htmlFor={ "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge } 
                  className={ classes } 
                  key={ field.merge + '-label-key' }
                  style={ field.hide === '1' ? { 'display': 'none' } : {} }
                >
                  { label }

                  { field.description_above === '1' && field.description.length > 0 && desc }

                  { 
                    ( field.type === 'dropdown' ? this.get_dropdown_field( field ) :
                    ( field.type === 'radio' ? this.get_radio_field( field ) : 
                    ( field.type === 'url' || field.type === 'imageurl' ? this.get_url_field( field ) :
                    ( field.type === 'date' || field.type === 'birthday' ? this.get_date_field( field ) :
                    this.get_default_field( field ) ) ) ) )
                  }

                  { field.description_above !== '1' && field.description.length > 0 && desc }

                </label>
              )
            }
          } else {

            var desc = field.description && field.description.length > 0 ? 
              <p className="form-field-description" id={ "form-field-description-" + field.group_id } key={ "form-field-description-" + field.group_id }>{ field.description }</p>
            : '';

            var label = field['hide-label'] !== '1' ?
              <span key={ field.group_id + "-label-span-key" } className={ field.group_id + "-label checkbox-parent-label" }>{ field.label }</span> 
            : '';

            var classes = { 'yikes-mailchimp-field-required' : field.require === '1' }
            classes[ field.group_id + '-label'] = true;

              return (            
                <label 
                  htmlFor={ field.group_id }
                  className= { classes }
                  key={ field.group_id + "-label-span-key" }
                  style={ field.type === 'hidden' || field.hide === '1' ? { 'display': 'none' } : {} }
                >

                  { label }

                  { field.description_above === '1' && field.description.length > 0 && desc }

                  {
                    ( field.type === 'radio' ? this.get_radio_interest_group( field ) :
                    ( field.type === 'hidden' ? this.get_hidden_interest_group( field ) : 
                    ( field.type === 'checkboxes' ? this.get_checkboxes_interest_group( field ) :
                    ( field.type === 'dropdown' ? this.get_dropdown_interest_group( field ) : '' ) ) ) )
                  }

                  { field.description && field.description_above !== '1' && field.description.length > 0 && desc }

                </label>
              )
            }
        })
    : '';
  }

  form_recaptcha() {

    return this.state.recaptcha_data.success === true && this.state.recaptcha_data.data.site_key && this.props.recaptcha === true ? 
      <Recaptcha
        sitekey={ this.state.recaptcha_data.data.site_key }
        key={ 'easy-forms-recaptcha-1' }
        elementID={ 'google-recaptcha-id-' + Math.random().toString(36).slice(-8) } // Need to use a unique ID for multiple reCAPTCHAs to work on the same page.
        theme={ this.props.recaptchaTheme }
        hl={ this.props.recaptchaLang.length > 0 ? this.props.recaptchaLang : this.state.recaptcha_data.data.locale }
        type={ this.props.recaptchaType }
        size={ this.props.recaptchaSize }
        verifyCallback=''
        expiredCallback=''
      />
      : '';    
  }

  submit_button() {
    return this.props.formData && this.props.formData.fields ?
      ( this.props.focus && this.props.formData.form_settings['yikes-easy-mc-submit-button-type'] === 'text' ?
        <button
          type="submit"
          key="mailchimpSubmitButton"
          className={ "yikes-easy-mc-submit-button yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'] }
        >
          <PlainText
            className="yikes-mailchimp-submit-button-span-text"
            key="yikes-mailchimp-submit-button-span-text"
            value={ this.props.submitButtonText ? this.props.submitButtonText : ''  }
            onChange={ this.props.toggleSubmitButtonText }
          />
        </button>
        :
        ( this.props.formData.form_settings['yikes-easy-mc-submit-button-type'] === 'text' ? 
          <button
            type="submit"
            key="mailchimpSubmitButton"
            className={ "yikes-easy-mc-submit-button yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'] }
          >
            <span className="yikes-mailchimp-submit-button-span-text" key="yikes-mailchimp-submit-button-span-text">{ this.props.submitButtonText }</span>
          </button>
        :
          <input
            type="image"
            alt={ this.props.submitButtonText }
            src={ this.props.formData.form_settings['yikes-easy-mc-submit-button-image'] }
            className={ "yikes-easy-mc-submit-button yikes-easy-mc-submit-button-image yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'] }
          />
        )
      )
      : '';
  }

  get_form_section() {
    return (
      <section 
        id={ "yikes-mailchimp-container-" + this.props.formID } 
        className={ "yikes-mailchimp-container yikes-mailchimp-container-" + this.props.formID }
        key="yikes-mailchimp-container-section"
      >
        {[

          // Form title
          [this.form_title()],

          // Form Description
          [this.form_description()],

          // Get the form
          [this.get_form()]      
        ]}
      </section>
    );
  }

  get_form() {
    return (
      // Form Wrapper
      <form
        id={ slugify( this.props.formTitle ) + '-' + this.props.formID }
        className={ "yikes-easy-mc-form yikes-easy-mc-form-" + this.props.formID + " " + this.props.formData.form_settings['yikes-easy-mc-form-class-names'] }
        key="yikes-mailchimp-container-form"
      >
        {[

          // Form fields
          [this.form_fields()],

          // Recaptcha
          [this.form_recaptcha()],

          // Submit button
          [this.submit_button()]
        ]}
      </form>
    );
  }

  render() {

    if ( this.state.forms.length > 0 && this.props.formData && Object.keys( this.props.formData ).length > 0 ) {

  	  return (

        <div className={ this.props.className }>

          {// Show inspector controls when focused
          this.props.focus && this.inspector_controls()}

          {// Show the forms dropdown
          this.forms_dropdown()}

          <hr key="easy-forms-dropdown-divider"/>

          {// Show the form
          this.get_form_section()}
        </div>
        
  	  );

  	} else if ( this.state.forms.length > 0 ) {

      // If we don't have form data, show the forms dropdown
  		return ( 
        <div className={ this.props.className }>
          {this.forms_dropdown()}
        </div>
      );

  	} else if ( this.state.api_key_status !== 'valid' ) {

      // If the API key is invalid, show a message.
      return ( 
        <p className="yikes-mailchimp-api-key-warning" key="yikes-mailchimp-api-key-warning">
         <em>{ this.state.api_key_status === 'empty' ? <a href={ constants.settings_url }> { __( 'To use this block, please enter an API key on the Easy Forms\' settings page.' ) } </a> : __( 'Your API key is invalid.' ) }</em>
        </p>
      );
    } else if ( this.state.forms_loaded === true && this.state.forms.length === 0 ) {

      // No forms.
      return (
        <p key="no-forms-found" className={ this.props.className }>
          <em>{ __( 'No forms were found.' ) }</em>
        </p>
      );
    } else {

      // Show loading... & spinner.
  		return (
        <p key="loading-easy-forms" className={ this.props.className }>
          <span key="yikes-easy-forms-loading-text">{ __( 'Loading...' ) }</span>
          <Spinner key="yikes-easy-forms-loading-spinner" />
        </p>
      );
  	}
  }
}