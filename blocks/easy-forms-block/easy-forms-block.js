/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./blocks/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./blocks/components/api.js":
/*!**********************************!*\
  !*** ./blocks/components/api.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var EasyFormsAPI = function () {
	function EasyFormsAPI() {
		_classCallCheck(this, EasyFormsAPI);

		this.ajaxurl = window.ajaxurl;
	}

	_createClass(EasyFormsAPI, [{
		key: 'get_api_key_status',
		value: function get_api_key_status() {
			var data = {
				action: 'yikes_get_api_key_status',
				nonce: ez_forms_gb_data.get_api_key_status
			};

			var status = $.post(this.ajaxurl, data);

			return status;
		}
	}, {
		key: 'get_recaptcha',
		value: function get_recaptcha() {
			var data = {
				action: 'yikes_get_recaptcha',
				nonce: ez_forms_gb_data.fetch_recaptcha_nonce
			};

			var recaptcha_data = $.post(this.ajaxurl, data);

			return recaptcha_data;
		}
	}, {
		key: 'get_forms',
		value: function get_forms() {
			var data = {
				action: 'yikes_get_forms',
				nonce: ez_forms_gb_data.fetch_forms_nonce
			};

			var forms = $.post(this.ajaxurl, data);

			return forms;
		}
	}, {
		key: 'get_form',
		value: function get_form(form_id) {
			var data = {
				action: 'yikes_get_form',
				form_id: form_id,
				nonce: ez_forms_gb_data.fetch_form_nonce
			};

			var form = $.post(this.ajaxurl, data);

			return form;
		}
	}]);

	return EasyFormsAPI;
}();

exports.default = EasyFormsAPI;

/***/ }),

/***/ "./blocks/components/class.MailchimpForms.js":
/*!***************************************************!*\
  !*** ./blocks/components/class.MailchimpForms.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classnames = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");

var _classnames2 = _interopRequireDefault(_classnames);

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _slugify = __webpack_require__(/*! ./slugify.js */ "./blocks/components/slugify.js");

var _slugify2 = _interopRequireDefault(_slugify);

var _constants = __webpack_require__(/*! ./constants.js */ "./blocks/components/constants.js");

var _constants2 = _interopRequireDefault(_constants);

var _api = __webpack_require__(/*! ./api.js */ "./blocks/components/api.js");

var _api2 = _interopRequireDefault(_api);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } // Import dependencies


// Get functions / blocks / components
var Recaptcha = __webpack_require__(/*! react-recaptcha */ "./node_modules/react-recaptcha/dist/react-recaptcha.js");
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$editor = wp.editor,
    RichText = _wp$editor.RichText,
    InspectorControls = _wp$editor.InspectorControls,
    PlainText = _wp$editor.PlainText;
var _wp$components = wp.components,
    Spinner = _wp$components.Spinner,
    TextControl = _wp$components.TextControl,
    PanelBody = _wp$components.PanelBody,
    PanelRow = _wp$components.PanelRow,
    FormToggle = _wp$components.FormToggle,
    SelectControl = _wp$components.SelectControl;
var Component = wp.element.Component;

var MailchimpForms = function (_Component) {
  _inherits(MailchimpForms, _Component);

  function MailchimpForms(props) {
    _classCallCheck(this, MailchimpForms);

    var _this = _possibleConstructorReturn(this, (MailchimpForms.__proto__ || Object.getPrototypeOf(MailchimpForms)).apply(this, arguments));

    _this.state = {
      api_key_status: 'valid',
      forms: [],
      recaptcha_data: {
        data: {},
        success: false
      },
      'forms_loaded': false
    };

    _this.api = new _api2.default();

    _this.address_fields = {
      'addr1': 'Address 1',
      'addr2': 'Address 2',
      'city': 'City',
      'state': 'State',
      'zip': 'Zip',
      'country': 'Country'
    };
    return _this;
  }

  /**
   * Run our API calls after the component has mounted. You can't use setState before a component is mounted.
   */


  _createClass(MailchimpForms, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      var _this2 = this;

      this.api.get_api_key_status().then(function (status) {
        _this2.setState({ api_key_status: status.data });
      });

      this.api.get_forms().then(function (forms) {
        _this2.setState({ forms: forms.data, forms_loaded: true });
      });

      this.api.get_recaptcha().then(function (recaptcha_data) {
        _this2.setState({ recaptcha_data: recaptcha_data });
        _this2.props.toggleRecaptchaAbstract(_this2.state.recaptcha_data.success);
      });
    }
  }, {
    key: 'handleFormFieldChanges',
    value: function handleFormFieldChanges(event) {
      // console.log( event );

      // console.log( value );
      // console.log( this );
      // console.log( typeof this.setState );

      // const target = event.target;
      //  const value  = target.type === 'checkbox' ? target.checked : target.value;
      //  const name   = target.name;

      // return this.setState( { [name]: value } );
    }
  }, {
    key: 'inspector_controls',
    value: function inspector_controls() {

      var inline = wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'inline-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('Inline')
        ),
        wp.element.createElement(FormToggle, {
          id: 'inline-form-toggle',
          label: __('Inline'),
          checked: !!this.props.inline,
          onChange: this.props.toggleInline
        })
      );

      var show_form_title = wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'show-title-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('Show Form Title')
        ),
        wp.element.createElement(FormToggle, {
          id: 'show-title-form-toggle',
          label: __('Show Form Title'),
          checked: !!this.props.showTitle,
          onChange: this.props.toggleShowTitle
        })
      );

      var show_form_description = wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'show-description-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('Show Form Description')
        ),
        wp.element.createElement(FormToggle, {
          id: 'show-description-form-toggle',
          label: __('Show Form Description'),
          checked: !!this.props.showDescription,
          onChange: this.props.toggleShowDescription
        })
      );

      var is_ajax = wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'is-ajax-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('AJAX Submissions')
        ),
        wp.element.createElement(FormToggle, {
          id: 'is-ajax-form-toggle',
          label: __('AJAX'),
          checked: !!this.props.isAjax,
          onChange: this.props.toggleIsAjax
        })
      );

      var recaptcha = wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'recaptcha-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('reCAPTCHA')
        ),
        wp.element.createElement(FormToggle, {
          id: 'recaptcha-form-toggle',
          label: __('reCAPTCHA'),
          checked: !!this.props.recaptcha,
          onChange: this.props.toggleRecaptcha
        })
      );

      var recaptcha_type = !!this.props.recaptcha ? wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'recaptcha-type-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('reCAPTCHA Type')
        ),
        wp.element.createElement(SelectControl, {
          value: this.props.recaptchaType,
          options: [{ value: 'image', label: 'Image' }, { value: 'audio', 'label': 'Audio' }],
          onChange: this.props.toggleRecaptchaType
        })
      ) : '';

      var recaptcha_theme = !!this.props.recaptcha ? wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'recaptcha-theme-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('reCAPTCHA Theme')
        ),
        wp.element.createElement(SelectControl, {
          value: this.props.recaptchaTheme,
          options: [{ value: 'light', label: 'Light' }, { value: 'dark', 'label': 'Dark' }],
          onChange: this.props.toggleRecaptchaTheme
        })
      ) : '';

      var recaptcha_lang = !!this.props.recaptcha ? wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'recaptcha-language-form-toggle',
            className: 'blocks-base-control__label',
            title: this.state.recaptcha_data.data ? 'The default language for your locale is ' + _constants2.default.locales[this.state.recaptcha_data.data.locale] : ''
          },
          __('reCAPTCHA Language')
        ),
        wp.element.createElement(SelectControl, {
          id: 'recaptcha-language-form-toggle',
          value: this.props.recaptchaLang.length > 0 ? this.props.recaptchaLang : this.state.recaptcha_data.data ? this.state.recaptcha_data.data.locale : '',
          onChange: this.props.toggleRecaptchaLang,
          title: this.state.recaptcha_data.data ? 'The default language for your locale is ' + _constants2.default.locales[this.state.recaptcha_data.data.locale] : '',
          options: Object.keys(_constants2.default.locales).map(function (key) {
            return { value: key, label: _constants2.default.locales[key] };
          })
        })
      ) : '';

      var recaptcha_size = !!this.props.recaptcha ? wp.element.createElement(
        PanelRow,
        null,
        wp.element.createElement(
          'label',
          {
            htmlFor: 'recaptcha-size-form-toggle',
            className: 'blocks-base-control__label'
          },
          __('reCAPTCHA Size')
        ),
        wp.element.createElement(SelectControl, {
          id: 'recaptcha-size-form-toggle',
          value: this.props.recaptchaSize,
          onChange: this.props.toggleRecaptchaSize,
          options: [{ value: 'normal', label: 'Normal' }, { value: 'compact', label: 'Compact' }]
        })
      ) : '';

      var recaptcha_verify_callback = !!this.props.recaptcha ? wp.element.createElement(TextControl, {
        id: 'recaptcha-verify-callback-form-toggle',
        label: __('reCAPTCHA Verify Callback'),
        value: this.props.recaptchaVerifyCallback,
        onChange: this.props.toggleRecaptchaVerifyCallback
      }) : '';

      var recaptcha_expired_callback = !!this.props.recaptcha ? wp.element.createElement(TextControl, {
        id: 'recaptcha-expired-callback-form-toggle',
        label: __('reCAPTCHA Expired Callback'),
        value: this.props.recaptchaExpiredCallback,
        onChange: this.props.toggleRecaptchaExpiredCallback
      }) : '';

      var inspector_controls = wp.element.createElement(
        InspectorControls,
        { key: 'easy-forms-inspector' },
        wp.element.createElement(
          PanelBody,
          { title: __('Settings') },
          inline,
          show_form_title,
          show_form_description,
          is_ajax,
          recaptcha,
          recaptcha_type,
          recaptcha_theme,
          recaptcha_lang,
          recaptcha_size,
          recaptcha_verify_callback,
          recaptcha_expired_callback
        )
      );

      return inspector_controls;
    }
  }, {
    key: 'forms_dropdown',
    value: function forms_dropdown() {
      return this.state.forms.length > 0 ? wp.element.createElement(
        'select',
        {
          value: this.props.formID,
          onChange: this.props.onChangeForm,
          className: 'yikes-mailchimp-forms-dropdown',
          key: 'yikes-mailchimp-forms-dropdown'
        },
        wp.element.createElement(
          'option',
          { key: 'select', value: '' },
          'Select Mailchimp Form...'
        ),
        this.state.forms.map(function (form) {
          return wp.element.createElement(
            'option',
            { key: 'yikes-select-form-' + form.id, value: form.id },
            form.form_name
          );
        })
      ) : '';
    }
  }, {
    key: 'form_title',
    value: function form_title() {
      return this.props.showTitle === true ? this.props.focus ? wp.element.createElement(
        'h3',
        {
          className: (0, _classnames2.default)("yikes-mailchimp-form-title yikes-mailchimp-form-title-" + this.props.formID),
          key: 'yikes-mailchimp-form-title-wrapper'
        },
        wp.element.createElement(PlainText, {
          placeholder: this.props.formTitle,
          key: 'yikes-mailchimp-form-title-editable',
          value: this.props.formTitle,
          onChange: this.props.toggleFormTitle
        })
      ) : wp.element.createElement(
        'h3',
        { className: (0, _classnames2.default)("yikes-mailchimp-form-title yikes-mailchimp-form-title-" + this.props.formID), key: 'yikes-mailchimp-form-title' },
        this.props.formTitle
      ) : '';
    }
  }, {
    key: 'form_description',
    value: function form_description() {
      return this.props.showDescription === true ? wp.element.createElement(
        'section',
        { className: (0, _classnames2.default)("yikes-mailchimp-form-description yikes-mailchimp-form-description-" + this.props.formID), key: 'yikes-mailchimp-form-description' },
        wp.element.createElement(PlainText, {
          className: 'mailchimpDescription',
          placeholder: __('Enter your form\'s description here.') /*( this.props.formData && this.props.formData.form_description && this.props.formData.form_description.length > 0 || this.props.descriptionValue && this.props.descriptionValue.length > 0 ) ? '' : __( 'Enter your form\'s description here.' )*/,
          value: this.props.descriptionValue.length > 0 ? this.props.descriptionValue : this.props.formData && this.props.formData.form_description ? this.props.formData.form_description : '',
          onChange: this.props.onChangeDescription,
          key: 'mailchimpDescription'
        })
      ) : '';
    }
  }, {
    key: 'get_address_field',
    value: function get_address_field(addr_field, field) {
      switch (addr_field) {

        case 'addr1':
        case 'addr2':
        case 'city':
        case 'zip':
          return wp.element.createElement(TextControl, {
            id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field,
            onChange: this.handleFormFieldChanges,
            type: 'text',
            name: field.merge + '[' + addr_field + ']',
            className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
            key: 'yikes-mailchimp-field-' + field.merge + '-' + addr_field,
            required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false,
            placeholder: field.placeholder === '1' ? this.address_fields[addr_field] : ''
          });
          break;

        case 'state':
          return wp.element.createElement(
            'select',
            {
              id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field,
              name: field.merge + '[' + addr_field + ']',
              className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
              onChange: this.handleFormFieldChanges,
              key: 'yikes-mailchimp-field-' + field.merge + '-' + addr_field,
              value: '',
              required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false
            },
            Object.keys(_constants2.default.states).map(function (key) {
              var choice = _constants2.default.states[key];
              return wp.element.createElement(
                'option',
                { key: 'state-' + key, value: key },
                choice
              );
            })
          );
          break;

        case 'country':
          return wp.element.createElement(
            'select',
            {
              id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge + '-' + addr_field,
              name: field.merge + '[' + addr_field + ']',
              className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
              onChange: this.handleFormFieldChanges,
              key: 'yikes-mailchimp-field-' + field.merge + '-' + addr_field,
              value: '',
              required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false
            },
            Object.keys(_constants2.default.countries).map(function (key) {
              var choice = _constants2.default.countries[key];
              return wp.element.createElement(
                'option',
                { key: 'country-' + key, value: key },
                choice
              );
            })
          );
          break;
      }
    }
  }, {
    key: 'get_dropdown_field',
    value: function get_dropdown_field(field) {
      return wp.element.createElement(
        'select',
        {
          id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge,
          name: field.merge,
          className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
          onChange: this.handleFormFieldChanges,
          key: 'yikes-mailchimp-field-' + field.merge,
          value: JSON.parse(field.choices)[field.default_choice],
          required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false
        },
        Object.keys(JSON.parse(field.choices)).map(function (key) {
          var choice = JSON.parse(field.choices)[key];
          return wp.element.createElement(
            'option',
            { key: (0, _slugify2.default)(choice), value: choice },
            choice
          );
        })
      );
    }
  }, {
    key: 'get_radio_field',
    value: function get_radio_field(field) {
      var _this3 = this;

      var ii = 0;

      return JSON.parse(field.choices).map(function (key) {
        ii++;
        return wp.element.createElement(
          'label',
          {
            htmlFor: field.merge + '-' + ii,
            className: 'yikes-easy-mc-checkbox-label',
            key: field.merge + '-label-radio-key-' + ii
          },
          wp.element.createElement('input', {
            type: 'radio',
            name: field.merge,
            id: field.merge + '-' + ii,
            key: field.merge + '-' + ii + '-input-key',
            value: key,
            onChange: _this3.handleFormFieldChanges,
            checked: parseInt(field.default_choice) === ii - 1
          }),
          wp.element.createElement(
            'span',
            { key: field.merge + '-span-radio-key-' + ii, className: (0, _classnames2.default)(field.merge + '-label') },
            key
          )
        );
      });
    }
  }, {
    key: 'get_url_field',
    value: function get_url_field(field) {
      return wp.element.createElement(TextControl, {
        id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge,
        placeholder: field.placeholder,
        onChange: this.handleFormFieldChanges,
        type: 'url',
        value: field.default,
        name: field.merge,
        className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
        key: 'yikes-mailchimp-field-' + field.merge,
        required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false,
        title: field.type === 'url' ? __('Please enter a valid URL to the website.') : __('Please enter a valid URL to the image.')
      });
    }
  }, {
    key: 'get_default_field',
    value: function get_default_field(field) {

      var type = field.type === 'zip' || field.type === 'phone' ? 'text' : field.type;

      return wp.element.createElement(TextControl, {
        id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge,
        placeholder: field.placeholder,
        onChange: this.handleFormFieldChanges,
        type: type,
        value: field.default,
        name: field.merge,
        className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
        key: 'yikes-mailchimp-field-' + field.merge,
        required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false
      });
    }
  }, {
    key: 'get_date_field',
    value: function get_date_field(field) {
      return wp.element.createElement(TextControl, {
        id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.merge,
        placeholder: field.placeholder,
        onChange: this.handleFormFieldChanges,
        type: 'text',
        value: field.default,
        name: field.merge,
        className: (0, _classnames2.default)('yikes-easy-mc-' + field.type + ' ' + field['additional-classes']),
        key: 'yikes-mailchimp-field-' + field.merge,
        required: field.merge === 'EMAIL' || field.require === '1' ? 'required' : false,
        'data-attr-type': field.type,
        'data-date-format': field.date_format.toLowerCase()
      });
    }
  }, {
    key: 'get_radio_interest_group',
    value: function get_radio_interest_group(field) {
      var _this4 = this;

      var ii = -1;
      var cn = "yikes-easy-mc-checkbox-label " + field['additional-classes'];

      return Object.keys(JSON.parse(field.groups)).map(function (key) {
        var _classNames2;

        var choice = JSON.parse(field.groups)[key];
        ii++;
        return wp.element.createElement(
          'label',
          {
            htmlFor: field.group_id + '-' + ii,
            className: (0, _classnames2.default)((_classNames2 = {}, _defineProperty(_classNames2, cn, true), _defineProperty(_classNames2, 'field-no-label', field['hide-label'] === '1'), _classNames2)),
            key: field.group_id + '-' + ii + '-label-key'
          },
          wp.element.createElement('input', {
            type: 'radio',
            name: "group-" + field.group_id + '[]',
            id: field.group_id + '-' + ii,
            key: field.group_id + '-' + ii + '-input-key',
            value: key,
            onChange: _this4.handleFormFieldChanges,
            checked: key === field.default_choice,
            className: (0, _classnames2.default)({ 'yikes-interest-group-required': field.require === '1' })
          }),
          choice
        );
      });
    }
  }, {
    key: 'get_hidden_interest_group',
    value: function get_hidden_interest_group(field) {
      var _this5 = this;

      var ii = -1;

      return Object.keys(JSON.parse(field.groups)).map(function (key) {
        var choice = JSON.parse(field.groups)[key];
        ii++;
        return wp.element.createElement(
          'label',
          {
            htmlFor: field.group_id + '-' + ii,
            className: (0, _classnames2.default)('yikes-easy-mc-checkbox-label ' + field['additional-classes']),
            key: field.group_id + '-' + ii + '-label-key'
          },
          wp.element.createElement('input', {
            type: 'checkbox',
            name: "group-" + field.group_id + '[]',
            id: field.group_id + '-' + ii,
            key: field.group_id + '-' + ii + '-input-key',
            value: key,
            onChange: _this5.handleFormFieldChanges,
            checked: field.default_choice && field.default_choice.indexOf(key) !== -1
          }),
          choice
        );
      });
    }
  }, {
    key: 'get_checkboxes_interest_group',
    value: function get_checkboxes_interest_group(field) {
      var _this6 = this;

      var ii = -1;
      var cn = 'yikes-easy-mc-checkbox-label ' + field['additional-classes'];

      return Object.keys(JSON.parse(field.groups)).map(function (key) {
        var _classNames3;

        var choice = JSON.parse(field.groups)[key];
        ii++;
        return wp.element.createElement(
          'label',
          {
            htmlFor: field.group_id + '-' + ii,
            className: (0, _classnames2.default)((_classNames3 = {}, _defineProperty(_classNames3, cn, true), _defineProperty(_classNames3, 'field-no-label', field['hide-label'] === '1'), _classNames3)),
            key: field.group_id + '-' + ii + '-label-key'
          },
          wp.element.createElement('input', {
            className: (0, _classnames2.default)({ 'yikes-interest-group-required': field.require === '1' }),
            type: 'checkbox',
            name: 'group-' + field.group_id + '[]',
            id: field.group_id + '-' + ii,
            key: field.group_id + '-' + ii + '-input-key',
            value: key,
            onChange: _this6.handleFormFieldChanges,
            checked: typeof field.default_choice !== 'undefined' && field.default_choice.indexOf(key) !== -1,
            required: field.require === '1' ? 'required' : false
          }),
          choice
        );
      });
    }
  }, {
    key: 'get_dropdown_interest_group',
    value: function get_dropdown_interest_group(field) {
      var ii = -1;

      return wp.element.createElement(
        'select',
        {
          id: "yikes-easy-mc-form-" + this.props.formID + "-" + field.group_id,
          name: "group-" + field.group_id,
          className: (0, _classnames2.default)("yikes-easy-mc-dropdown " + field['additional-classes']),
          value: field.default_choice,
          onChange: this.handleFormFieldChanges,
          required: field.require === '1' ? 'required' : false
        },
        Object.keys(JSON.parse(field.groups)).map(function (key) {
          var choice = JSON.parse(field.groups)[key];
          ii++;
          return wp.element.createElement(
            'option',
            { key: field.group_id + '-' + ii + '-input-key', value: key },
            choice
          );
        })
      );
    }
  }, {
    key: 'form_fields',
    value: function form_fields() {
      var _this7 = this;

      return this.props.formData && this.props.formData.fields ? Object.keys(this.props.formData.fields).map(function (key) {

        var field = _this7.props.formData.fields[key];

        // Form Fields (aka merge variables)
        if (typeof field.merge !== 'undefined') {

          var desc = field.description.length > 0 ? wp.element.createElement(
            'p',
            { className: 'form-field-description', id: "form-field-description-" + field.merge, key: "form-field-description-" + field.merge },
            field.description
          ) : '';

          var label = field['hide-label'] !== '1' ? wp.element.createElement(
            'span',
            { className: (0, _classnames2.default)(field.merge + '-label'), key: field.merge + '-label-span-key' },
            field.label
          ) : '';

          var classes = { 'yikes-mailchimp-field-required': field.merge === 'EMAIL' || field.require === '1' };
          classes[field.merge + '-label'] = true;

          if (field.type === 'address') {

            return [field.description_above === '1' && field.description.length > 0 && desc, Object.keys(_this7.address_fields).map(function (addr_field) {
              label = field['hide-label'] !== '1' ? wp.element.createElement(
                'span',
                { className: (0, _classnames2.default)(field.merge + '-label'), key: field.merge + '-label-span-key-' + addr_field },
                _this7.address_fields[addr_field]
              ) : '';

              return wp.element.createElement(
                'label',
                {
                  htmlFor: "yikes-easy-mc-form-" + _this7.props.formID + "-" + field.merge + '-' + addr_field,
                  className: (0, _classnames2.default)(classes),
                  key: field.merge + '-label-key-' + addr_field,
                  style: field.hide === '1' ? { 'display': 'none' } : {}
                },
                label,
                _this7.get_address_field(addr_field, field)
              );
            }), field.description_above !== '1' && field.description.length > 0 && desc];
          } else {
            return wp.element.createElement(
              'label',
              {
                htmlFor: "yikes-easy-mc-form-" + _this7.props.formID + "-" + field.merge,
                className: (0, _classnames2.default)(classes),
                key: field.merge + '-label-key',
                style: field.hide === '1' ? { 'display': 'none' } : {}
              },
              label,
              field.description_above === '1' && field.description.length > 0 && desc,
              field.type === 'dropdown' ? _this7.get_dropdown_field(field) : field.type === 'radio' ? _this7.get_radio_field(field) : field.type === 'url' || field.type === 'imageurl' ? _this7.get_url_field(field) : field.type === 'date' || field.type === 'birthday' ? _this7.get_date_field(field) : _this7.get_default_field(field),
              field.description_above !== '1' && field.description.length > 0 && desc
            );
          }
        } else {

          var desc = field.description.length > 0 ? wp.element.createElement(
            'p',
            { className: 'form-field-description', id: "form-field-description-" + field.group_id, key: "form-field-description-" + field.group_id },
            field.description
          ) : '';

          var label = field['hide-label'] !== '1' ? wp.element.createElement(
            'span',
            { key: field.group_id + "-label-span-key", className: (0, _classnames2.default)(field.group_id + "-label checkbox-parent-label") },
            field.label
          ) : '';

          var classes = { 'yikes-mailchimp-field-required': field.require === '1' };
          classes[field.group_id + '-label'] = true;

          return wp.element.createElement(
            'label',
            {
              htmlFor: field.group_id,
              className: (0, _classnames2.default)(classes),
              key: field.group_id + "-label-span-key",
              style: field.type === 'hidden' || field.hide === '1' ? { 'display': 'none' } : {}
            },
            label,
            field.description_above === '1' && field.description.length > 0 && desc,
            field.type === 'radio' ? _this7.get_radio_interest_group(field) : field.type === 'hidden' ? _this7.get_hidden_interest_group(field) : field.type === 'checkboxes' ? _this7.get_checkboxes_interest_group(field) : field.type === 'dropdown' ? _this7.get_dropdown_interest_group(field) : '',
            field.description_above !== '1' && field.description.length > 0 && desc
          );
        }
      }) : '';
    }
  }, {
    key: 'form_recaptcha',
    value: function form_recaptcha() {

      return this.state.recaptcha_data.success === true && this.state.recaptcha_data.data.site_key && this.props.recaptcha === true ? wp.element.createElement(Recaptcha, {
        sitekey: this.state.recaptcha_data.data.site_key,
        key: 'easy-forms-recaptcha-1',
        elementID: 'google-recaptcha-id-' + Math.random().toString(36).slice(-8) // Need to use a unique ID for multiple reCAPTCHAs to work on the same page.
        , theme: this.props.recaptchaTheme,
        hl: this.props.recaptchaLang.length > 0 ? this.props.recaptchaLang : this.state.recaptcha_data.data.locale,
        type: this.props.recaptchaType,
        size: this.props.recaptchaSize,
        verifyCallback: '',
        expiredCallback: ''
      }) : '';
    }
  }, {
    key: 'submit_button',
    value: function submit_button() {
      return this.props.formData && this.props.formData.fields ? this.props.focus && this.props.formData.form_settings['yikes-easy-mc-submit-button-type'] === 'text' ? wp.element.createElement(
        'button',
        {
          type: 'submit',
          key: 'mailchimpSubmitButton',
          className: (0, _classnames2.default)("yikes-easy-mc-submit-button yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'])
        },
        wp.element.createElement(PlainText, {
          className: 'yikes-mailchimp-submit-button-span-text',
          key: 'yikes-mailchimp-submit-button-span-text',
          value: this.props.submitButtonText ? this.props.submitButtonText : '',
          onChange: this.props.toggleSubmitButtonText
        })
      ) : this.props.formData.form_settings['yikes-easy-mc-submit-button-type'] === 'text' ? wp.element.createElement(
        'button',
        {
          type: 'submit',
          key: 'mailchimpSubmitButton',
          className: (0, _classnames2.default)("yikes-easy-mc-submit-button yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'])
        },
        wp.element.createElement(
          'span',
          { className: 'yikes-mailchimp-submit-button-span-text', key: 'yikes-mailchimp-submit-button-span-text' },
          this.props.submitButtonText
        )
      ) : wp.element.createElement('input', {
        type: 'image',
        alt: this.props.submitButtonText,
        src: this.props.formData.form_settings['yikes-easy-mc-submit-button-image'],
        className: (0, _classnames2.default)("yikes-easy-mc-submit-button yikes-easy-mc-submit-button-image yikes-easy-mc-submit-button-" + this.props.formID + " btn btn-primary" + " " + this.props.formData.form_settings['yikes-easy-mc-submit-button-classes'])
      }) : '';
    }
  }, {
    key: 'get_form_section',
    value: function get_form_section() {
      return wp.element.createElement(
        'section',
        {
          id: "yikes-mailchimp-container-" + this.props.formID,
          className: (0, _classnames2.default)("yikes-mailchimp-container yikes-mailchimp-container-" + this.props.formID),
          key: 'yikes-mailchimp-container-section'
        },
        [

        // Form title
        [this.form_title()],

        // Form Description
        [this.form_description()],

        // Get the form
        [this.get_form()]]
      );
    }
  }, {
    key: 'get_form',
    value: function get_form() {
      return (
        // Form Wrapper
        wp.element.createElement(
          'form',
          {
            id: (0, _slugify2.default)(this.props.formTitle) + '-' + this.props.formID,
            className: (0, _classnames2.default)("yikes-easy-mc-form yikes-easy-mc-form-" + this.props.formID + " " + this.props.formData.form_settings['yikes-easy-mc-form-class-names']),
            key: 'yikes-mailchimp-container-form'
          },
          [

          // Form fields
          [this.form_fields()],

          // Recaptcha
          [this.form_recaptcha()],

          // Submit button
          [this.submit_button()]]
        )
      );
    }
  }, {
    key: 'render',
    value: function render() {

      if (this.state.forms.length > 0 && this.props.formData && Object.keys(this.props.formData).length > 0) {

        return wp.element.createElement(
          'div',
          { className: (0, _classnames2.default)(this.props.className) },
          // Show inspector controls when focused
          this.props.focus && this.inspector_controls(),
          // Show the forms dropdown
          this.forms_dropdown(),
          wp.element.createElement('hr', { key: 'easy-forms-dropdown-divider' }),
          // Show the form
          this.get_form_section()
        );
      } else if (this.state.forms.length > 0) {

        // If we don't have form data, show the forms dropdown
        return wp.element.createElement(
          'div',
          { className: (0, _classnames2.default)(this.props.className) },
          this.forms_dropdown()
        );
      } else if (this.state.api_key_status !== 'valid') {

        // If the API key is invalid, show a message.
        return wp.element.createElement(
          'p',
          { className: 'yikes-mailchimp-api-key-warning', key: 'yikes-mailchimp-api-key-warning' },
          wp.element.createElement(
            'em',
            null,
            this.state.api_key_status === 'empty' ? wp.element.createElement(
              'a',
              { href: _constants2.default.settings_url },
              ' ',
              __('To use this block, please enter an API key on the Easy Forms\' settings page.'),
              ' '
            ) : __('Your API key is invalid.')
          )
        );
      } else if (this.state.forms_loaded === true && this.state.forms.length === 0) {

        // No forms.
        return wp.element.createElement(
          'p',
          { key: 'no-forms-found', className: (0, _classnames2.default)(this.props.className) },
          wp.element.createElement(
            'em',
            null,
            __('No forms were found.')
          )
        );
      } else {

        // Show loading... & spinner.
        return wp.element.createElement(
          'p',
          { key: 'loading-easy-forms', className: (0, _classnames2.default)(this.props.className) },
          wp.element.createElement(
            'span',
            { key: 'yikes-easy-forms-loading-text' },
            __('Loading...')
          ),
          wp.element.createElement(Spinner, { key: 'yikes-easy-forms-loading-spinner' })
        );
      }
    }
  }]);

  return MailchimpForms;
}(Component);

exports.default = MailchimpForms;

/***/ }),

/***/ "./blocks/components/constants.js":
/*!****************************************!*\
  !*** ./blocks/components/constants.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
var constants = {
	states: {
		'': '—',
		'AL': 'Alabama',
		'AK': 'Alaska',
		'AZ': 'Arizona',
		'AR': 'Arkansas',
		'CA': 'California',
		'CO': 'Colorado',
		'CT': 'Connecticut',
		'DE': 'Delaware',
		'DC': 'District Of Columbia',
		'FL': 'Florida',
		'GA': 'Georgia',
		'HI': 'Hawaii',
		'ID': 'Idaho',
		'IL': 'Illinois',
		'IN': 'Indiana',
		'IA': 'Iowa',
		'KS': 'Kansas',
		'KY': 'Kentucky',
		'LA': 'Louisiana',
		'ME': 'Maine',
		'MD': 'Maryland',
		'MA': 'Massachusetts',
		'MI': 'Michigan',
		'MN': 'Minnesota',
		'MS': 'Mississippi',
		'MO': 'Missouri',
		'MT': 'Montana',
		'NE': 'Nebraska',
		'NV': 'Nevada',
		'NH': 'New Hampshire',
		'NJ': 'New Jersey',
		'NM': 'New Mexico',
		'NY': 'New York',
		'NC': 'North Carolina',
		'ND': 'North Dakota',
		'OH': 'Ohio',
		'OK': 'Oklahoma',
		'OR': 'Oregon',
		'PA': 'Pennsylvania',
		'RI': 'Rhode Island',
		'SC': 'South Carolina',
		'SD': 'South Dakota',
		'TN': 'Tennessee',
		'TX': 'Texas',
		'UT': 'Utah',
		'VT': 'Vermont',
		'VA': 'Virginia',
		'WA': 'Washington',
		'WV': 'West Virginia',
		'WI': 'Wisconsin',
		'WY': 'Wyoming',
		'AB': 'Alberta',
		'BC': 'British Columbia',
		'MB': 'Manitoba',
		'NB': 'New Brunswick',
		'NL': 'Newfoundland and Labrador',
		'NS': 'Nova Scotia',
		'ON': 'Ontario',
		'PE': 'Prince Edward Island',
		'QC': 'Quebec',
		'SK': 'Saskatchewan',
		'NT': 'Northwest Territories',
		'NU': 'Nunavut',
		'YT': 'Yukon'
	},
	countries: {
		'US': 'United States',
		'GB': 'United Kingdom',
		'AF': 'Afghanistan',
		'AX': 'Aland Islands',
		'AL': 'Albania',
		'DZ': 'Algeria',
		'AS': 'American Samoa',
		'AD': 'Andorra',
		'AO': 'Angola',
		'AI': 'Anguilla',
		'AQ': 'Antarctica',
		'AG': 'Antigua and Barbuda',
		'AR': 'Argentina',
		'AM': 'Armenia',
		'AW': 'Aruba',
		'AU': 'Australia',
		'AT': 'Austria',
		'AZ': 'Azerbaijan',
		'BS': 'Bahamas',
		'BH': 'Bahrain',
		'BD': 'Bangladesh',
		'BB': 'Barbados',
		'BY': 'Belarus',
		'BE': 'Belgium',
		'BZ': 'Belize',
		'BJ': 'Benin',
		'BM': 'Bermuda',
		'BT': 'Bhutan',
		'BO': 'Bolivia',
		'BA': 'Bosnia and Herzegovina',
		'BW': 'Botswana',
		'BV': 'Bouvet Island',
		'BR': 'Brazil',
		'IO': 'British Indian Ocean Territory',
		'BN': 'Brunei Darussalam',
		'BG': 'Bulgaria',
		'BF': 'Burkina Faso',
		'BI': 'Burundi',
		'KH': 'Cambodia',
		'CM': 'Cameroon',
		'CA': 'Canada',
		'CV': 'Cape Verde',
		'KY': 'Cayman Islands',
		'CF': 'Central African Republic',
		'TD': 'Chad',
		'CL': 'Chile',
		'CN': 'China',
		'CX': 'Christmas Island',
		'CC': 'Cocos (Keeling) Islands',
		'CO': 'Colombia',
		'KM': 'Comoros',
		'CG': 'Congo',
		'CD': 'Congo, The Democratic Republic of The',
		'CK': 'Cook Islands',
		'CR': 'Costa Rica',
		'CI': 'Cote D’ivoire',
		'HR': 'Croatia',
		'CU': 'Cuba',
		'CY': 'Cyprus',
		'CZ': 'Czech Republic',
		'DK': 'Denmark',
		'DJ': 'Djibouti',
		'DM': 'Dominica',
		'DO': 'Dominican Republic',
		'EC': 'Ecuador',
		'EG': 'Egypt',
		'SV': 'El Salvador',
		'GQ': 'Equatorial Guinea',
		'ER': 'Eritrea',
		'EE': 'Estonia',
		'ET': 'Ethiopia',
		'FK': 'Falkland Islands (Malvinas)',
		'FO': 'Faroe Islands',
		'FJ': 'Fiji',
		'FI': 'Finland',
		'FR': 'France',
		'GF': 'French Guiana',
		'PF': 'French Polynesia',
		'TF': 'French Southern Territories',
		'GA': 'Gabon',
		'GM': 'Gambia',
		'GE': 'Georgia',
		'DE': 'Germany',
		'GH': 'Ghana',
		'GI': 'Gibraltar',
		'GR': 'Greece',
		'GL': 'Greenland',
		'GD': 'Grenada',
		'GP': 'Guadeloupe',
		'GU': 'Guam',
		'GT': 'Guatemala',
		'GG': 'Guernsey',
		'GN': 'Guinea',
		'GW': 'Guinea-bissau',
		'GY': 'Guyana',
		'HT': 'Haiti',
		'HM': 'Heard Island and Mcdonald Islands',
		'VA': 'Holy See (Vatican City State)',
		'HN': 'Honduras',
		'HK': 'Hong Kong',
		'HU': 'Hungary',
		'IS': 'Iceland',
		'IN': 'India',
		'ID': 'Indonesia',
		'IR': 'Iran, Islamic Republic of',
		'IQ': 'Iraq',
		'IE': 'Ireland',
		'IM': 'Isle of Man',
		'IL': 'Israel',
		'IT': 'Italy',
		'JM': 'Jamaica',
		'JP': 'Japan',
		'JE': 'Jersey',
		'JO': 'Jordan',
		'KZ': 'Kazakhstan',
		'KE': 'Kenya',
		'KI': 'Kiribati',
		'KP': 'Korea, Democratic People’s Republic of',
		'KR': 'Korea, Republic of',
		'KW': 'Kuwait',
		'KG': 'Kyrgyzstan',
		'LA': 'Lao People’s Democratic Republic',
		'LV': 'Latvia',
		'LB': 'Lebanon',
		'LS': 'Lesotho',
		'LR': 'Liberia',
		'LY': 'Libyan Arab Jamahiriya',
		'LI': 'Liechtenstein',
		'LT': 'Lithuania',
		'LU': 'Luxembourg',
		'MO': 'Macao',
		'MK': 'Macedonia, The Former Yugoslav Republic of',
		'MG': 'Madagascar',
		'MW': 'Malawi',
		'MY': 'Malaysia',
		'MV': 'Maldives',
		'ML': 'Mali',
		'MT': 'Malta',
		'MH': 'Marshall Islands',
		'MQ': 'Martinique',
		'MR': 'Mauritania',
		'MU': 'Mauritius',
		'YT': 'Mayotte',
		'MX': 'Mexico',
		'FM': 'Micronesia, Federated States of',
		'MD': 'Moldova, Republic of',
		'MC': 'Monaco',
		'MN': 'Mongolia',
		'ME': 'Montenegro',
		'MS': 'Montserrat',
		'MA': 'Morocco',
		'MZ': 'Mozambique',
		'MM': 'Myanmar',
		'NA': 'Namibia',
		'NR': 'Nauru',
		'NP': 'Nepal',
		'NL': 'Netherlands',
		'AN': 'Netherlands Antilles',
		'NC': 'New Caledonia',
		'NZ': 'New Zealand',
		'NI': 'Nicaragua',
		'NE': 'Niger',
		'NG': 'Nigeria',
		'NU': 'Niue',
		'NF': 'Norfolk Island',
		'MP': 'Northern Mariana Islands',
		'NO': 'Norway',
		'OM': 'Oman',
		'PK': 'Pakistan',
		'PW': 'Palau',
		'PS': 'Palestinian Territory, Occupied',
		'PA': 'Panama',
		'PG': 'Papua New Guinea',
		'PY': 'Paraguay',
		'PE': 'Peru',
		'PH': 'Philippines',
		'PN': 'Pitcairn',
		'PL': 'Poland',
		'PT': 'Portugal',
		'PR': 'Puerto Rico',
		'QA': 'Qatar',
		'RE': 'Reunion',
		'RO': 'Romania',
		'RU': 'Russian Federation',
		'RW': 'Rwanda',
		'SH': 'Saint Helena',
		'KN': 'Saint Kitts and Nevis',
		'LC': 'Saint Lucia',
		'PM': 'Saint Pierre and Miquelon',
		'VC': 'Saint Vincent and The Grenadines',
		'WS': 'Samoa',
		'SM': 'San Marino',
		'ST': 'Sao Tome and Principe',
		'SA': 'Saudi Arabia',
		'SN': 'Senegal',
		'RS': 'Serbia',
		'SC': 'Seychelles',
		'SL': 'Sierra Leone',
		'SG': 'Singapore',
		'SK': 'Slovakia',
		'SI': 'Slovenia',
		'SB': 'Solomon Islands',
		'SO': 'Somalia',
		'ZA': 'South Africa',
		'GS': 'South Georgia and The South Sandwich Islands',
		'ES': 'Spain',
		'LK': 'Sri Lanka',
		'SD': 'Sudan',
		'SR': 'Suriname',
		'SJ': 'Svalbard and Jan Mayen',
		'SZ': 'Swaziland',
		'SE': 'Sweden',
		'CH': 'Switzerland',
		'SY': 'Syrian Arab Republic',
		'TW': 'Taiwan, Province of China',
		'TJ': 'Tajikistan',
		'TZ': 'Tanzania, United Republic of',
		'TH': 'Thailand',
		'TL': 'Timor-leste',
		'TG': 'Togo',
		'TK': 'Tokelau',
		'TO': 'Tonga',
		'TT': 'Trinidad and Tobago',
		'TN': 'Tunisia',
		'TR': 'Turkey',
		'TM': 'Turkmenistan',
		'TC': 'Turks and Caicos Islands',
		'TV': 'Tuvalu',
		'UG': 'Uganda',
		'UA': 'Ukraine',
		'AE': 'United Arab Emirates',
		'UM': 'United States Minor Outlying Islands',
		'UY': 'Uruguay',
		'UZ': 'Uzbekistan',
		'VU': 'Vanuatu',
		'VE': 'Venezuela',
		'VN': 'Viet Nam',
		'VG': 'Virgin Islands, British',
		'VI': 'Virgin Islands, U.S.',
		'WF': 'Wallis and Futuna',
		'EH': 'Western Sahara',
		'YE': 'Yemen',
		'ZM': 'Zambia',
		'ZW': 'Zimbabwe'
	},
	locales: {
		"ar": "Arabic",
		"af": "Afrikaans",
		"am": "Amharic",
		"hy": "Armenian",
		"az": "Azerbaijani",
		"eu": "Basque",
		"bn": "Bengali",
		"bg": "Bulgarian",
		"ca": "Catalan",
		"zh-HK": "Chinese (Hong Kong)",
		"zh-CN": "Chinese (Simplified)",
		"zh-TW": "Chinese (Traditional)",
		"hr": "Croatian",
		"cs": "Czech",
		"da": "Danish",
		"nl": "Dutch",
		"en-GB": "English (UK)",
		"en": "English (US)",
		"et": "Estonian",
		"fil": "Filipino",
		"fi": "Finnish",
		"fr": "French",
		"fr-CA": "French (Canadian)",
		"gl": "Galician",
		"ka": "Georgian",
		"de": "German",
		"de-AT": "German (Austria)",
		"de-CH": "German (Switzerland)",
		"el": "Greek",
		"gu": "Gujarati",
		"iw": "Hebrew",
		"hi": "Hindi",
		"hu": "Hungarain",
		"is": "Icelandic",
		"id": "Indonesian",
		"it": "Italian",
		"ja": "Japanese",
		"kn": "Kannada",
		"ko": "Korean",
		"lo": "Laothian",
		"lv": "Latvian",
		"lt": "Lithuanian",
		"ms": "Malay",
		"ml": "Malayalam",
		"mr": "Marathi",
		"mn": "Mongolian",
		"no": "Norwegian",
		"fa": "Persian",
		"pl": "Polish",
		"pt": "Portuguese",
		"pt-BR": "Portuguese (Brazil)",
		"pt-PT": "Portuguese (Portugal)",
		"ro": "Romanian",
		"ru": "Russian",
		"sr": "Serbian",
		"si": "Sinhalese",
		"sk": "Slovak",
		"sl": "Slovenian",
		"es": "Spanish",
		"es-419": "Spanish (Latin America)",
		"sw": "Swahili",
		"sv": "Swedish",
		"ta": "Tamil",
		"te": "Telugu",
		"th": "Thai",
		"tr": "Turkish",
		"uk": "Ukrainian",
		"ur": "Urdu",
		"vi": "Vietnamese",
		"zu": "Zulu"
	},
	settings_url: 'wp-admin/admin.php?page=yikes-inc-easy-mailchimp-settings'
};

exports.default = constants;

/***/ }),

/***/ "./blocks/components/enable-submit-button-editing.js":
/*!***********************************************************!*\
  !*** ./blocks/components/enable-submit-button-editing.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function ($) {

	$(document).ready(function () {

		$('body').on('click', '.yikes-easy-mc-submit-button', function (event) {
			event.preventDefault();
			event.stopPropagation();
			$('.yikes-mailchimp-submit-button-span-text').focus();
		});

		$('body').on('click', '.yikes-mailchimp-submit-button-span-text', function (event) {
			event.preventDefault();
			event.stopPropagation();
		});
	});
})(jQuery);

/***/ }),

/***/ "./blocks/components/slugify.js":
/*!**************************************!*\
  !*** ./blocks/components/slugify.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = slugify;
/*
* This should have the same effect as WP/PHP's `sanitize_title()` function
*
* Source: https://gist.github.com/mathewbyrne/1280286
*/
function slugify(text) {

  // Use hash map for special characters 
  var specialChars = { "à": 'a', "ä": 'a', "á": 'a', "â": 'a', "æ": 'a', "å": 'a', "ë": 'e', "è": 'e', "é": 'e', "ê": 'e', "î": 'i', "ï": 'i', "ì": 'i', "í": 'i', "ò": 'o', "ó": 'o', "ö": 'o', "ô": 'o', "ø": 'o', "ù": 'o', "ú": 'u', "ü": 'u', "û": 'u', "ñ": 'n', "ç": 'c', "ß": 's', "ÿ": 'y', "œ": 'o', "ŕ": 'r', "ś": 's', "ń": 'n', "ṕ": 'p', "ẃ": 'w', "ǵ": 'g', "ǹ": 'n', "ḿ": 'm', "ǘ": 'u', "ẍ": 'x', "ź": 'z', "ḧ": 'h', "·": '-', "/": '-', "_": '-', ",": '-', ":": '-', ";": '-' };

  return text.toString().toLowerCase().replace(/\s+/g, '-') // Replace spaces with -
  .replace(/./g, function (target, index, str) {
    return specialChars[target] || target;
  }) // Replace special characters using the hash map
  .replace(/&/g, '-and-') // Replace & with 'and'
  .replace(/[^\w\-]+/g, '') // Remove all non-word chars
  .replace(/\-\-+/g, '-') // Replace multiple - with single -
  .replace(/^-+/, '') // Trim - from start of text
  .replace(/-+$/, ''); // Trim - from end of text
};

/***/ }),

/***/ "./blocks/easy-forms-block/dev-easy-forms-block.js":
/*!*********************************************************!*\
  !*** ./blocks/easy-forms-block/dev-easy-forms-block.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _classnames = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");

var _classnames2 = _interopRequireDefault(_classnames);

var _api = __webpack_require__(/*! ../components/api.js */ "./blocks/components/api.js");

var _api2 = _interopRequireDefault(_api);

var _classMailchimpForms = __webpack_require__(/*! ../components/class.MailchimpForms.js */ "./blocks/components/class.MailchimpForms.js");

var _classMailchimpForms2 = _interopRequireDefault(_classMailchimpForms);

__webpack_require__(/*! ./easy-forms-block.scss */ "./blocks/easy-forms-block/easy-forms-block.scss");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

__webpack_require__(/*! ../components/enable-submit-button-editing.js */ "./blocks/components/enable-submit-button-editing.js");

// Get just the __() localization function from wp.i18n
var __ = wp.i18n.__;

// Get registerBlockType and other methods from wp.blocks

var registerBlockType = wp.blocks.registerBlockType;


var edit_easy_form = function edit_easy_form(props) {

  var onChangeForm = function onChangeForm(event) {
    props.setAttributes({ form_id: event.target.value });

    if (event.target.value.length > 0) {
      var api = new _api2.default();
      api.get_form(event.target.value).then(function (form) {
        props.setAttributes({ form: form.data });
        props.setAttributes({ form_description: form.data.form_description });
        props.setAttributes({ form_title: form.data.form_name });
        props.setAttributes({ is_ajax: form.data.submission_settings.ajax === '1' });
        props.setAttributes({ submit_button_text: form.data.form_settings['yikes-easy-mc-submit-button-text'] });
      });
    }
  };

  var onChangeDescription = function onChangeDescription(value) {
    props.setAttributes({ form_description: value });
  };

  var toggleShowDescription = function toggleShowDescription(event) {
    props.setAttributes({ show_description: !!event.target.checked });
  };

  var toggleInline = function toggleInline(event) {
    props.setAttributes({ inline: !!event.target.checked });
  };

  var toggleShowTitle = function toggleShowTitle(event) {
    props.setAttributes({ show_title: !!event.target.checked });
  };

  var toggleFormTitle = function toggleFormTitle(value) {
    props.setAttributes({ form_title: value });
  };

  var toggleIsAjax = function toggleIsAjax(event) {
    props.setAttributes({ is_ajax: !!event.target.checked });
  };

  /* Allow this function to be called via a checkbox handler or directly by passing in a boolean */
  var toggleRecaptchaAbstract = function toggleRecaptchaAbstract(checked) {
    props.setAttributes({ recaptcha: checked });
  };

  var toggleRecaptcha = function toggleRecaptcha(event) {
    toggleRecaptchaAbstract(!!event.target.checked);
  };

  var toggleRecaptchaTheme = function toggleRecaptchaTheme(value) {
    props.setAttributes({ recaptcha_theme: value });
  };

  var toggleRecaptchaLang = function toggleRecaptchaLang(value) {
    props.setAttributes({ recaptcha_lang: value });
  };

  var toggleRecaptchaType = function toggleRecaptchaType(value) {
    props.setAttributes({ recaptcha_type: value });
  };

  var toggleRecaptchaSize = function toggleRecaptchaSize(value) {
    props.setAttributes({ recaptcha_size: value });
  };

  var toggleRecaptchaVerifyCallback = function toggleRecaptchaVerifyCallback(value) {
    props.setAttributes({ recaptcha_verify_callback: value });
  };

  var toggleRecaptchaExpiredCallback = function toggleRecaptchaExpiredCallback(value) {
    props.setAttributes({ recaptcha_expired_callback: value });
  };

  var toggleSubmitButtonText = function toggleSubmitButtonText(value) {
    props.setAttributes({ submit_button_text: value });
  };

  return wp.element.createElement(_classMailchimpForms2.default, {
    className: (0, _classnames2.default)(props.className),
    onChangeForm: onChangeForm,
    formID: props.attributes.form_id,
    formData: props.attributes.form,
    onChangeDescription: onChangeDescription,
    descriptionValue: props.attributes.form_description,
    showDescription: props.attributes.show_description,
    toggleShowDescription: toggleShowDescription,
    focus: !!props.isSelected,
    inline: props.attributes.inline,
    toggleInline: toggleInline,
    formTitle: props.attributes.form_title,
    toggleFormTitle: toggleFormTitle,
    showTitle: props.attributes.show_title,
    toggleShowTitle: toggleShowTitle,
    isAjax: props.attributes.is_ajax,
    toggleIsAjax: toggleIsAjax,
    toggleRecaptchaAbstract: toggleRecaptchaAbstract,
    recaptcha: props.attributes.recaptcha,
    toggleRecaptcha: toggleRecaptcha,
    recaptchaTheme: props.attributes.recaptcha_theme,
    toggleRecaptchaTheme: toggleRecaptchaTheme,
    recaptchaLang: props.attributes.recaptcha_lang,
    toggleRecaptchaLang: toggleRecaptchaLang,
    recaptchaType: props.attributes.recaptcha_type,
    toggleRecaptchaType: toggleRecaptchaType,
    recaptchaSize: props.attributes.recaptcha_size,
    toggleRecaptchaSize: toggleRecaptchaSize,
    recaptchaVerifyCallback: props.attributes.recaptcha_verify_callback,
    toggleRecaptchaVerifyCallback: toggleRecaptchaVerifyCallback,
    recaptchaExpiredCallback: props.attributes.recaptcha_expired_callback,
    toggleRecaptchaExpiredCallback: toggleRecaptchaExpiredCallback,
    submitButtonText: props.attributes.submit_button_text,
    toggleSubmitButtonText: toggleSubmitButtonText
  });
};

var save_easy_form = function save_easy_form(props) {
  return null;
};

var settings = {
  title: __('Easy Forms for Mailchimp'),
  category: 'common', // Options include "common", "formatting", "layout", "widgets" and "embed."
  icon: 'email-alt',
  keywords: ['mailchimp', 'easy forms for mailchimp', 'yikes'],
  attributes: {
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
      default: true
    },
    recaptcha: {
      type: 'boolean',
      default: false
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
  save: save_easy_form
};

var EasyFormsBlock = registerBlockType(

// Name
ez_forms_gb_data.block_namespace + ez_forms_gb_data.block_name,

// Settings
settings);

/***/ }),

/***/ "./blocks/easy-forms-block/easy-forms-block.scss":
/*!*******************************************************!*\
  !*** ./blocks/easy-forms-block/easy-forms-block.scss ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../node_modules/css-loader!../../node_modules/sass-loader/lib/loader.js!./easy-forms-block.scss */ "./node_modules/css-loader/index.js!./node_modules/sass-loader/lib/loader.js!./blocks/easy-forms-block/easy-forms-block.scss");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./blocks/index.js":
/*!*************************!*\
  !*** ./blocks/index.js ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(/*! ./easy-forms-block/dev-easy-forms-block.js */ "./blocks/easy-forms-block/dev-easy-forms-block.js");

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg === 'undefined' ? 'undefined' : _typeof(arg);

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if (typeof module !== 'undefined' && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if ("function" === 'function' && _typeof(__webpack_require__(/*! !webpack amd options */ "./node_modules/webpack/buildin/amd-options.js")) === 'object' && __webpack_require__(/*! !webpack amd options */ "./node_modules/webpack/buildin/amd-options.js")) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {
		window.classNames = classNames;
	}
})();

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/sass-loader/lib/loader.js!./blocks/easy-forms-block/easy-forms-block.scss":
/*!**************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/sass-loader/lib/loader.js!./blocks/easy-forms-block/easy-forms-block.scss ***!
  \**************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".wp-block-yikes-inc-easy-forms-easy-forms-block textarea.yikes-mailchimp-submit-button-span-text {\n  background: none;\n  text-align: center; }\n\n.wp-block-yikes-inc-easy-forms-easy-forms-block select {\n  height: auto; }\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/lib/css-base.js":
/*!*************************************************!*\
  !*** ./node_modules/css-loader/lib/css-base.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function (useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if (item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function (modules, mediaQuery) {
		if (typeof modules === "string") modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for (var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if (typeof id === "number") alreadyImportedModules[id] = true;
		}
		for (i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if (typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if (mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if (mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */';
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}

/***/ }),

/***/ "./node_modules/object-assign/index.js":
/*!*********************************************!*\
  !*** ./node_modules/object-assign/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */

var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc'); // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !== 'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};

/***/ }),

/***/ "./node_modules/prop-types/checkPropTypes.js":
/*!***************************************************!*\
  !*** ./node_modules/prop-types/checkPropTypes.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var printWarning = function printWarning() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "./node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};

  printWarning = function printWarning(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (typeSpecs.hasOwnProperty(typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error((componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' + 'it must be a function, usually from the `prop-types` package, but received `' + _typeof(typeSpecs[typeSpecName]) + '`.');
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning((componentName || 'React class') + ': type specification of ' + location + ' `' + typeSpecName + '` is invalid; the type checker ' + 'function must return `null` or an `Error` but returned a ' + (typeof error === 'undefined' ? 'undefined' : _typeof(error)) + '. ' + 'You may have forgotten to pass an argument to the type checker ' + 'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' + 'shape all require an argument).');
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning('Failed ' + location + ' type: ' + error.message + (stack != null ? stack : ''));
        }
      }
    }
  }
}

module.exports = checkPropTypes;

/***/ }),

/***/ "./node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!*************************************************************!*\
  !*** ./node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;

/***/ }),

/***/ "./node_modules/react-recaptcha/dist/react-recaptcha.js":
/*!**************************************************************!*\
  !*** ./node_modules/react-recaptcha/dist/react-recaptcha.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(module) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

!function (e, t) {
  "object" == ( false ? undefined : _typeof(exports)) && "object" == ( false ? undefined : _typeof(module)) ? module.exports = t(__webpack_require__(/*! react */ "./node_modules/react/index.js")) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! react */ "./node_modules/react/index.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(undefined, function (e) {
  return function (e) {
    function t(r) {
      if (a[r]) return a[r].exports;var n = a[r] = { exports: {}, id: r, loaded: !1 };return e[r].call(n.exports, n, n.exports, t), n.loaded = !0, n.exports;
    }var a = {};return t.m = e, t.c = a, t.p = "", t(0);
  }([function (e, t, a) {
    "use strict";
    function r(e) {
      return e && e.__esModule ? e : { default: e };
    }function n(e, t) {
      if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
    }function o(e, t) {
      if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return !t || "object" != (typeof t === "undefined" ? "undefined" : _typeof(t)) && "function" != typeof t ? e : t;
    }function i(e, t) {
      if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + (typeof t === "undefined" ? "undefined" : _typeof(t)));e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t);
    }Object.defineProperty(t, "__esModule", { value: !0 });var l = function () {
      function e(e, t) {
        for (var a = 0; a < t.length; a++) {
          var r = t[a];r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
        }
      }return function (t, a, r) {
        return a && e(t.prototype, a), r && e(t, r), t;
      };
    }(),
        s = a(6),
        c = r(s),
        p = a(4),
        u = r(p),
        d = { className: u.default.string, onloadCallbackName: u.default.string, elementID: u.default.string, onloadCallback: u.default.func, verifyCallback: u.default.func, expiredCallback: u.default.func, render: u.default.oneOf(["onload", "explicit"]), sitekey: u.default.string, theme: u.default.oneOf(["light", "dark"]), type: u.default.string, verifyCallbackName: u.default.string, expiredCallbackName: u.default.string, size: u.default.oneOf(["invisible", "compact", "normal"]), tabindex: u.default.string, hl: u.default.string, badge: u.default.oneOf(["bottomright", "bottomleft", "inline"]) },
        f = { elementID: "g-recaptcha", className: "g-recaptcha", onloadCallback: void 0, onloadCallbackName: "onloadCallback", verifyCallback: void 0, verifyCallbackName: "verifyCallback", expiredCallback: void 0, expiredCallbackName: "expiredCallback", render: "onload", theme: "light", type: "image", size: "normal", tabindex: "0", hl: "en", badge: "bottomright" },
        h = function h() {
      return "undefined" != typeof window && "undefined" != typeof window.grecaptcha && "function" == typeof window.grecaptcha.render;
    },
        y = void 0,
        b = function (e) {
      function t(e) {
        n(this, t);var a = o(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));return a._renderGrecaptcha = a._renderGrecaptcha.bind(a), a.reset = a.reset.bind(a), a.state = { ready: h(), widget: null }, a.state.ready || "undefined" == typeof window || (y = setInterval(a._updateReadyState.bind(a), 1e3)), a;
      }return i(t, e), l(t, [{ key: "componentDidMount", value: function value() {
          this.state.ready && this._renderGrecaptcha();
        } }, { key: "componentDidUpdate", value: function value(e, t) {
          var a = this.props,
              r = a.render,
              n = a.onloadCallback;"explicit" === r && n && this.state.ready && !t.ready && this._renderGrecaptcha();
        } }, { key: "componentWillUnmount", value: function value() {
          clearInterval(y);
        } }, { key: "reset", value: function value() {
          var e = this.state,
              t = e.ready,
              a = e.widget;t && null !== a && grecaptcha.reset(a);
        } }, { key: "execute", value: function value() {
          var e = this.state,
              t = e.ready,
              a = e.widget;t && null !== a && grecaptcha.execute(a);
        } }, { key: "_updateReadyState", value: function value() {
          h() && (this.setState({ ready: !0 }), clearInterval(y));
        } }, { key: "_renderGrecaptcha", value: function value() {
          this.state.widget = grecaptcha.render(this.props.elementID, { sitekey: this.props.sitekey, callback: this.props.verifyCallback ? this.props.verifyCallback : void 0, theme: this.props.theme, type: this.props.type, size: this.props.size, tabindex: this.props.tabindex, hl: this.props.hl, badge: this.props.badge, "expired-callback": this.props.expiredCallback ? this.props.expiredCallback : void 0 }), this.props.onloadCallback && this.props.onloadCallback();
        } }, { key: "render", value: function value() {
          return "explicit" === this.props.render && this.props.onloadCallback ? c.default.createElement("div", { id: this.props.elementID, "data-onloadcallbackname": this.props.onloadCallbackName, "data-verifycallbackname": this.props.verifyCallbackName }) : c.default.createElement("div", { id: this.props.elementID, className: this.props.className, "data-sitekey": this.props.sitekey, "data-theme": this.props.theme, "data-type": this.props.type, "data-size": this.props.size, "data-badge": this.props.badge, "data-tabindex": this.props.tabindex });
        } }]), t;
    }(s.Component);t.default = b, b.propTypes = d, b.defaultProps = f, e.exports = t.default;
  }, function (e, t) {
    "use strict";
    function a(e) {
      return function () {
        return e;
      };
    }var r = function r() {};r.thatReturns = a, r.thatReturnsFalse = a(!1), r.thatReturnsTrue = a(!0), r.thatReturnsNull = a(null), r.thatReturnsThis = function () {
      return this;
    }, r.thatReturnsArgument = function (e) {
      return e;
    }, e.exports = r;
  }, function (e, t, a) {
    "use strict";
    function r(e, t, a, r, o, i, l, s) {
      if (n(t), !e) {
        var c;if (void 0 === t) c = new Error("Minified exception occurred; use the non-minified dev environment for the full error message and additional helpful warnings.");else {
          var p = [a, r, o, i, l, s],
              u = 0;c = new Error(t.replace(/%s/g, function () {
            return p[u++];
          })), c.name = "Invariant Violation";
        }throw c.framesToPop = 1, c;
      }
    }var n = function n(e) {};e.exports = r;
  }, function (e, t, a) {
    "use strict";
    var r = a(1),
        n = a(2),
        o = a(5);e.exports = function () {
      function e(e, t, a, r, i, l) {
        l !== o && n(!1, "Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");
      }function t() {
        return e;
      }e.isRequired = e;var a = { array: e, bool: e, func: e, number: e, object: e, string: e, symbol: e, any: e, arrayOf: t, element: e, instanceOf: t, node: e, objectOf: t, oneOf: t, oneOfType: t, shape: t };return a.checkPropTypes = r, a.PropTypes = a, a;
    };
  }, function (e, t, a) {
    e.exports = a(3)();
  }, function (e, t) {
    "use strict";
    var a = "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED";e.exports = a;
  }, function (t, a) {
    t.exports = e;
  }]);
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/react/cjs/react.development.js":
/*!*****************************************************!*\
  !*** ./node_modules/react/cjs/react.development.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/** @license React v16.6.0
 * react.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

if (true) {
  (function () {
    'use strict';

    var _assign = __webpack_require__(/*! object-assign */ "./node_modules/object-assign/index.js");
    var checkPropTypes = __webpack_require__(/*! prop-types/checkPropTypes */ "./node_modules/prop-types/checkPropTypes.js");

    // TODO: this is special because it gets imported during build.

    var ReactVersion = '16.6.0';

    // The Symbol used to tag the ReactElement-like types. If there is no native Symbol
    // nor polyfill, then a plain number is used for performance.
    var hasSymbol = typeof Symbol === 'function' && Symbol.for;

    var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
    var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
    var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
    var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
    var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
    var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
    var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace;
    var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
    var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
    var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
    var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
    var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;

    var MAYBE_ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
    var FAUX_ITERATOR_SYMBOL = '@@iterator';

    function getIteratorFn(maybeIterable) {
      if (maybeIterable === null || (typeof maybeIterable === 'undefined' ? 'undefined' : _typeof(maybeIterable)) !== 'object') {
        return null;
      }
      var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];
      if (typeof maybeIterator === 'function') {
        return maybeIterator;
      }
      return null;
    }

    /**
     * Use invariant() to assert state which your program assumes to be true.
     *
     * Provide sprintf-style format (only %s is supported) and arguments
     * to provide information about what broke and what you were
     * expecting.
     *
     * The invariant message will be stripped in production, but the invariant
     * will remain to ensure logic does not differ in production.
     */

    var validateFormat = function validateFormat() {};

    {
      validateFormat = function validateFormat(format) {
        if (format === undefined) {
          throw new Error('invariant requires an error message argument');
        }
      };
    }

    function invariant(condition, format, a, b, c, d, e, f) {
      validateFormat(format);

      if (!condition) {
        var error = void 0;
        if (format === undefined) {
          error = new Error('Minified exception occurred; use the non-minified dev environment ' + 'for the full error message and additional helpful warnings.');
        } else {
          var args = [a, b, c, d, e, f];
          var argIndex = 0;
          error = new Error(format.replace(/%s/g, function () {
            return args[argIndex++];
          }));
          error.name = 'Invariant Violation';
        }

        error.framesToPop = 1; // we don't care about invariant's own frame
        throw error;
      }
    }

    // Relying on the `invariant()` implementation lets us
    // preserve the format and params in the www builds.

    /**
     * Forked from fbjs/warning:
     * https://github.com/facebook/fbjs/blob/e66ba20ad5be433eb54423f2b097d829324d9de6/packages/fbjs/src/__forks__/warning.js
     *
     * Only change is we use console.warn instead of console.error,
     * and do nothing when 'console' is not supported.
     * This really simplifies the code.
     * ---
     * Similar to invariant but only logs a warning if the condition is not met.
     * This can be used to log issues in development environments in critical
     * paths. Removing the logging code for production environments will keep the
     * same logic and follow the same code paths.
     */

    var lowPriorityWarning = function lowPriorityWarning() {};

    {
      var printWarning = function printWarning(format) {
        for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        var argIndex = 0;
        var message = 'Warning: ' + format.replace(/%s/g, function () {
          return args[argIndex++];
        });
        if (typeof console !== 'undefined') {
          console.warn(message);
        }
        try {
          // --- Welcome to debugging React ---
          // This error was thrown as a convenience so that you can use this stack
          // to find the callsite that caused this warning to fire.
          throw new Error(message);
        } catch (x) {}
      };

      lowPriorityWarning = function lowPriorityWarning(condition, format) {
        if (format === undefined) {
          throw new Error('`lowPriorityWarning(condition, format, ...args)` requires a warning ' + 'message argument');
        }
        if (!condition) {
          for (var _len2 = arguments.length, args = Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
            args[_key2 - 2] = arguments[_key2];
          }

          printWarning.apply(undefined, [format].concat(args));
        }
      };
    }

    var lowPriorityWarning$1 = lowPriorityWarning;

    /**
     * Similar to invariant but only logs a warning if the condition is not met.
     * This can be used to log issues in development environments in critical
     * paths. Removing the logging code for production environments will keep the
     * same logic and follow the same code paths.
     */

    var warningWithoutStack = function warningWithoutStack() {};

    {
      warningWithoutStack = function warningWithoutStack(condition, format) {
        for (var _len = arguments.length, args = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
          args[_key - 2] = arguments[_key];
        }

        if (format === undefined) {
          throw new Error('`warningWithoutStack(condition, format, ...args)` requires a warning ' + 'message argument');
        }
        if (args.length > 8) {
          // Check before the condition to catch violations early.
          throw new Error('warningWithoutStack() currently supports at most 8 arguments.');
        }
        if (condition) {
          return;
        }
        if (typeof console !== 'undefined') {
          var _args$map = args.map(function (item) {
            return '' + item;
          }),
              a = _args$map[0],
              b = _args$map[1],
              c = _args$map[2],
              d = _args$map[3],
              e = _args$map[4],
              f = _args$map[5],
              g = _args$map[6],
              h = _args$map[7];

          var message = 'Warning: ' + format;

          // We intentionally don't use spread (or .apply) because it breaks IE9:
          // https://github.com/facebook/react/issues/13610
          switch (args.length) {
            case 0:
              console.error(message);
              break;
            case 1:
              console.error(message, a);
              break;
            case 2:
              console.error(message, a, b);
              break;
            case 3:
              console.error(message, a, b, c);
              break;
            case 4:
              console.error(message, a, b, c, d);
              break;
            case 5:
              console.error(message, a, b, c, d, e);
              break;
            case 6:
              console.error(message, a, b, c, d, e, f);
              break;
            case 7:
              console.error(message, a, b, c, d, e, f, g);
              break;
            case 8:
              console.error(message, a, b, c, d, e, f, g, h);
              break;
            default:
              throw new Error('warningWithoutStack() currently supports at most 8 arguments.');
          }
        }
        try {
          // --- Welcome to debugging React ---
          // This error was thrown as a convenience so that you can use this stack
          // to find the callsite that caused this warning to fire.
          var argIndex = 0;
          var _message = 'Warning: ' + format.replace(/%s/g, function () {
            return args[argIndex++];
          });
          throw new Error(_message);
        } catch (x) {}
      };
    }

    var warningWithoutStack$1 = warningWithoutStack;

    var didWarnStateUpdateForUnmountedComponent = {};

    function warnNoop(publicInstance, callerName) {
      {
        var _constructor = publicInstance.constructor;
        var componentName = _constructor && (_constructor.displayName || _constructor.name) || 'ReactClass';
        var warningKey = componentName + '.' + callerName;
        if (didWarnStateUpdateForUnmountedComponent[warningKey]) {
          return;
        }
        warningWithoutStack$1(false, "Can't call %s on a component that is not yet mounted. " + 'This is a no-op, but it might indicate a bug in your application. ' + 'Instead, assign to `this.state` directly or define a `state = {};` ' + 'class property with the desired state in the %s component.', callerName, componentName);
        didWarnStateUpdateForUnmountedComponent[warningKey] = true;
      }
    }

    /**
     * This is the abstract API for an update queue.
     */
    var ReactNoopUpdateQueue = {
      /**
       * Checks whether or not this composite component is mounted.
       * @param {ReactClass} publicInstance The instance we want to test.
       * @return {boolean} True if mounted, false otherwise.
       * @protected
       * @final
       */
      isMounted: function isMounted(publicInstance) {
        return false;
      },

      /**
       * Forces an update. This should only be invoked when it is known with
       * certainty that we are **not** in a DOM transaction.
       *
       * You may want to call this when you know that some deeper aspect of the
       * component's state has changed but `setState` was not called.
       *
       * This will not invoke `shouldComponentUpdate`, but it will invoke
       * `componentWillUpdate` and `componentDidUpdate`.
       *
       * @param {ReactClass} publicInstance The instance that should rerender.
       * @param {?function} callback Called after component is updated.
       * @param {?string} callerName name of the calling function in the public API.
       * @internal
       */
      enqueueForceUpdate: function enqueueForceUpdate(publicInstance, callback, callerName) {
        warnNoop(publicInstance, 'forceUpdate');
      },

      /**
       * Replaces all of the state. Always use this or `setState` to mutate state.
       * You should treat `this.state` as immutable.
       *
       * There is no guarantee that `this.state` will be immediately updated, so
       * accessing `this.state` after calling this method may return the old value.
       *
       * @param {ReactClass} publicInstance The instance that should rerender.
       * @param {object} completeState Next state.
       * @param {?function} callback Called after component is updated.
       * @param {?string} callerName name of the calling function in the public API.
       * @internal
       */
      enqueueReplaceState: function enqueueReplaceState(publicInstance, completeState, callback, callerName) {
        warnNoop(publicInstance, 'replaceState');
      },

      /**
       * Sets a subset of the state. This only exists because _pendingState is
       * internal. This provides a merging strategy that is not available to deep
       * properties which is confusing. TODO: Expose pendingState or don't use it
       * during the merge.
       *
       * @param {ReactClass} publicInstance The instance that should rerender.
       * @param {object} partialState Next partial state to be merged with state.
       * @param {?function} callback Called after component is updated.
       * @param {?string} Name of the calling function in the public API.
       * @internal
       */
      enqueueSetState: function enqueueSetState(publicInstance, partialState, callback, callerName) {
        warnNoop(publicInstance, 'setState');
      }
    };

    var emptyObject = {};
    {
      Object.freeze(emptyObject);
    }

    /**
     * Base class helpers for the updating state of a component.
     */
    function Component(props, context, updater) {
      this.props = props;
      this.context = context;
      // If a component has string refs, we will assign a different object later.
      this.refs = emptyObject;
      // We initialize the default updater but the real one gets injected by the
      // renderer.
      this.updater = updater || ReactNoopUpdateQueue;
    }

    Component.prototype.isReactComponent = {};

    /**
     * Sets a subset of the state. Always use this to mutate
     * state. You should treat `this.state` as immutable.
     *
     * There is no guarantee that `this.state` will be immediately updated, so
     * accessing `this.state` after calling this method may return the old value.
     *
     * There is no guarantee that calls to `setState` will run synchronously,
     * as they may eventually be batched together.  You can provide an optional
     * callback that will be executed when the call to setState is actually
     * completed.
     *
     * When a function is provided to setState, it will be called at some point in
     * the future (not synchronously). It will be called with the up to date
     * component arguments (state, props, context). These values can be different
     * from this.* because your function may be called after receiveProps but before
     * shouldComponentUpdate, and this new state, props, and context will not yet be
     * assigned to this.
     *
     * @param {object|function} partialState Next partial state or function to
     *        produce next partial state to be merged with current state.
     * @param {?function} callback Called after state is updated.
     * @final
     * @protected
     */
    Component.prototype.setState = function (partialState, callback) {
      !((typeof partialState === 'undefined' ? 'undefined' : _typeof(partialState)) === 'object' || typeof partialState === 'function' || partialState == null) ? invariant(false, 'setState(...): takes an object of state variables to update or a function which returns an object of state variables.') : void 0;
      this.updater.enqueueSetState(this, partialState, callback, 'setState');
    };

    /**
     * Forces an update. This should only be invoked when it is known with
     * certainty that we are **not** in a DOM transaction.
     *
     * You may want to call this when you know that some deeper aspect of the
     * component's state has changed but `setState` was not called.
     *
     * This will not invoke `shouldComponentUpdate`, but it will invoke
     * `componentWillUpdate` and `componentDidUpdate`.
     *
     * @param {?function} callback Called after update is complete.
     * @final
     * @protected
     */
    Component.prototype.forceUpdate = function (callback) {
      this.updater.enqueueForceUpdate(this, callback, 'forceUpdate');
    };

    /**
     * Deprecated APIs. These APIs used to exist on classic React classes but since
     * we would like to deprecate them, we're not going to move them over to this
     * modern base class. Instead, we define a getter that warns if it's accessed.
     */
    {
      var deprecatedAPIs = {
        isMounted: ['isMounted', 'Instead, make sure to clean up subscriptions and pending requests in ' + 'componentWillUnmount to prevent memory leaks.'],
        replaceState: ['replaceState', 'Refactor your code to use setState instead (see ' + 'https://github.com/facebook/react/issues/3236).']
      };
      var defineDeprecationWarning = function defineDeprecationWarning(methodName, info) {
        Object.defineProperty(Component.prototype, methodName, {
          get: function get() {
            lowPriorityWarning$1(false, '%s(...) is deprecated in plain JavaScript React classes. %s', info[0], info[1]);
            return undefined;
          }
        });
      };
      for (var fnName in deprecatedAPIs) {
        if (deprecatedAPIs.hasOwnProperty(fnName)) {
          defineDeprecationWarning(fnName, deprecatedAPIs[fnName]);
        }
      }
    }

    function ComponentDummy() {}
    ComponentDummy.prototype = Component.prototype;

    /**
     * Convenience component with default shallow equality check for sCU.
     */
    function PureComponent(props, context, updater) {
      this.props = props;
      this.context = context;
      // If a component has string refs, we will assign a different object later.
      this.refs = emptyObject;
      this.updater = updater || ReactNoopUpdateQueue;
    }

    var pureComponentPrototype = PureComponent.prototype = new ComponentDummy();
    pureComponentPrototype.constructor = PureComponent;
    // Avoid an extra prototype jump for these methods.
    _assign(pureComponentPrototype, Component.prototype);
    pureComponentPrototype.isPureReactComponent = true;

    // an immutable object with a single mutable value
    function createRef() {
      var refObject = {
        current: null
      };
      {
        Object.seal(refObject);
      }
      return refObject;
    }

    /**
     * Keeps track of the current owner.
     *
     * The current owner is the component who should own any components that are
     * currently being constructed.
     */
    var ReactCurrentOwner = {
      /**
       * @internal
       * @type {ReactComponent}
       */
      current: null,
      currentDispatcher: null
    };

    var BEFORE_SLASH_RE = /^(.*)[\\\/]/;

    var describeComponentFrame = function describeComponentFrame(name, source, ownerName) {
      var sourceInfo = '';
      if (source) {
        var path = source.fileName;
        var fileName = path.replace(BEFORE_SLASH_RE, '');
        {
          // In DEV, include code for a common special case:
          // prefer "folder/index.js" instead of just "index.js".
          if (/^index\./.test(fileName)) {
            var match = path.match(BEFORE_SLASH_RE);
            if (match) {
              var pathBeforeSlash = match[1];
              if (pathBeforeSlash) {
                var folderName = pathBeforeSlash.replace(BEFORE_SLASH_RE, '');
                fileName = folderName + '/' + fileName;
              }
            }
          }
        }
        sourceInfo = ' (at ' + fileName + ':' + source.lineNumber + ')';
      } else if (ownerName) {
        sourceInfo = ' (created by ' + ownerName + ')';
      }
      return '\n    in ' + (name || 'Unknown') + sourceInfo;
    };

    var Resolved = 1;

    function refineResolvedLazyComponent(lazyComponent) {
      return lazyComponent._status === Resolved ? lazyComponent._result : null;
    }

    function getWrappedName(outerType, innerType, wrapperName) {
      var functionName = innerType.displayName || innerType.name || '';
      return outerType.displayName || (functionName !== '' ? wrapperName + '(' + functionName + ')' : wrapperName);
    }

    function getComponentName(type) {
      if (type == null) {
        // Host root, text node or just invalid type.
        return null;
      }
      {
        if (typeof type.tag === 'number') {
          warningWithoutStack$1(false, 'Received an unexpected object in getComponentName(). ' + 'This is likely a bug in React. Please file an issue.');
        }
      }
      if (typeof type === 'function') {
        return type.displayName || type.name || null;
      }
      if (typeof type === 'string') {
        return type;
      }
      switch (type) {
        case REACT_CONCURRENT_MODE_TYPE:
          return 'ConcurrentMode';
        case REACT_FRAGMENT_TYPE:
          return 'Fragment';
        case REACT_PORTAL_TYPE:
          return 'Portal';
        case REACT_PROFILER_TYPE:
          return 'Profiler';
        case REACT_STRICT_MODE_TYPE:
          return 'StrictMode';
        case REACT_SUSPENSE_TYPE:
          return 'Suspense';
      }
      if ((typeof type === 'undefined' ? 'undefined' : _typeof(type)) === 'object') {
        switch (type.$$typeof) {
          case REACT_CONTEXT_TYPE:
            return 'Context.Consumer';
          case REACT_PROVIDER_TYPE:
            return 'Context.Provider';
          case REACT_FORWARD_REF_TYPE:
            return getWrappedName(type, type.render, 'ForwardRef');
          case REACT_MEMO_TYPE:
            return getComponentName(type.type);
          case REACT_LAZY_TYPE:
            {
              var thenable = type;
              var resolvedThenable = refineResolvedLazyComponent(thenable);
              if (resolvedThenable) {
                return getComponentName(resolvedThenable);
              }
            }
        }
      }
      return null;
    }

    var ReactDebugCurrentFrame = {};

    var currentlyValidatingElement = null;

    function setCurrentlyValidatingElement(element) {
      {
        currentlyValidatingElement = element;
      }
    }

    {
      // Stack implementation injected by the current renderer.
      ReactDebugCurrentFrame.getCurrentStack = null;

      ReactDebugCurrentFrame.getStackAddendum = function () {
        var stack = '';

        // Add an extra top frame while an element is being validated
        if (currentlyValidatingElement) {
          var name = getComponentName(currentlyValidatingElement.type);
          var owner = currentlyValidatingElement._owner;
          stack += describeComponentFrame(name, currentlyValidatingElement._source, owner && getComponentName(owner.type));
        }

        // Delegate to the injected renderer-specific implementation
        var impl = ReactDebugCurrentFrame.getCurrentStack;
        if (impl) {
          stack += impl() || '';
        }

        return stack;
      };
    }

    var ReactSharedInternals = {
      ReactCurrentOwner: ReactCurrentOwner,
      // Used by renderers to avoid bundling object-assign twice in UMD bundles:
      assign: _assign
    };

    {
      _assign(ReactSharedInternals, {
        // These should not be included in production.
        ReactDebugCurrentFrame: ReactDebugCurrentFrame,
        // Shim for React DOM 16.0.0 which still destructured (but not used) this.
        // TODO: remove in React 17.0.
        ReactComponentTreeHook: {}
      });
    }

    /**
     * Similar to invariant but only logs a warning if the condition is not met.
     * This can be used to log issues in development environments in critical
     * paths. Removing the logging code for production environments will keep the
     * same logic and follow the same code paths.
     */

    var warning = warningWithoutStack$1;

    {
      warning = function warning(condition, format) {
        if (condition) {
          return;
        }
        var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
        var stack = ReactDebugCurrentFrame.getStackAddendum();
        // eslint-disable-next-line react-internal/warning-and-invariant-args

        for (var _len = arguments.length, args = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
          args[_key - 2] = arguments[_key];
        }

        warningWithoutStack$1.apply(undefined, [false, format + '%s'].concat(args, [stack]));
      };
    }

    var warning$1 = warning;

    var hasOwnProperty = Object.prototype.hasOwnProperty;

    var RESERVED_PROPS = {
      key: true,
      ref: true,
      __self: true,
      __source: true
    };

    var specialPropKeyWarningShown = void 0;
    var specialPropRefWarningShown = void 0;

    function hasValidRef(config) {
      {
        if (hasOwnProperty.call(config, 'ref')) {
          var getter = Object.getOwnPropertyDescriptor(config, 'ref').get;
          if (getter && getter.isReactWarning) {
            return false;
          }
        }
      }
      return config.ref !== undefined;
    }

    function hasValidKey(config) {
      {
        if (hasOwnProperty.call(config, 'key')) {
          var getter = Object.getOwnPropertyDescriptor(config, 'key').get;
          if (getter && getter.isReactWarning) {
            return false;
          }
        }
      }
      return config.key !== undefined;
    }

    function defineKeyPropWarningGetter(props, displayName) {
      var warnAboutAccessingKey = function warnAboutAccessingKey() {
        if (!specialPropKeyWarningShown) {
          specialPropKeyWarningShown = true;
          warningWithoutStack$1(false, '%s: `key` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://fb.me/react-special-props)', displayName);
        }
      };
      warnAboutAccessingKey.isReactWarning = true;
      Object.defineProperty(props, 'key', {
        get: warnAboutAccessingKey,
        configurable: true
      });
    }

    function defineRefPropWarningGetter(props, displayName) {
      var warnAboutAccessingRef = function warnAboutAccessingRef() {
        if (!specialPropRefWarningShown) {
          specialPropRefWarningShown = true;
          warningWithoutStack$1(false, '%s: `ref` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://fb.me/react-special-props)', displayName);
        }
      };
      warnAboutAccessingRef.isReactWarning = true;
      Object.defineProperty(props, 'ref', {
        get: warnAboutAccessingRef,
        configurable: true
      });
    }

    /**
     * Factory method to create a new React element. This no longer adheres to
     * the class pattern, so do not use new to call it. Also, no instanceof check
     * will work. Instead test $$typeof field against Symbol.for('react.element') to check
     * if something is a React Element.
     *
     * @param {*} type
     * @param {*} key
     * @param {string|object} ref
     * @param {*} self A *temporary* helper to detect places where `this` is
     * different from the `owner` when React.createElement is called, so that we
     * can warn. We want to get rid of owner and replace string `ref`s with arrow
     * functions, and as long as `this` and owner are the same, there will be no
     * change in behavior.
     * @param {*} source An annotation object (added by a transpiler or otherwise)
     * indicating filename, line number, and/or other information.
     * @param {*} owner
     * @param {*} props
     * @internal
     */
    var ReactElement = function ReactElement(type, key, ref, self, source, owner, props) {
      var element = {
        // This tag allows us to uniquely identify this as a React Element
        $$typeof: REACT_ELEMENT_TYPE,

        // Built-in properties that belong on the element
        type: type,
        key: key,
        ref: ref,
        props: props,

        // Record the component responsible for creating this element.
        _owner: owner
      };

      {
        // The validation flag is currently mutative. We put it on
        // an external backing store so that we can freeze the whole object.
        // This can be replaced with a WeakMap once they are implemented in
        // commonly used development environments.
        element._store = {};

        // To make comparing ReactElements easier for testing purposes, we make
        // the validation flag non-enumerable (where possible, which should
        // include every environment we run tests in), so the test framework
        // ignores it.
        Object.defineProperty(element._store, 'validated', {
          configurable: false,
          enumerable: false,
          writable: true,
          value: false
        });
        // self and source are DEV only properties.
        Object.defineProperty(element, '_self', {
          configurable: false,
          enumerable: false,
          writable: false,
          value: self
        });
        // Two elements created in two different places should be considered
        // equal for testing purposes and therefore we hide it from enumeration.
        Object.defineProperty(element, '_source', {
          configurable: false,
          enumerable: false,
          writable: false,
          value: source
        });
        if (Object.freeze) {
          Object.freeze(element.props);
          Object.freeze(element);
        }
      }

      return element;
    };

    /**
     * Create and return a new ReactElement of the given type.
     * See https://reactjs.org/docs/react-api.html#createelement
     */
    function createElement(type, config, children) {
      var propName = void 0;

      // Reserved names are extracted
      var props = {};

      var key = null;
      var ref = null;
      var self = null;
      var source = null;

      if (config != null) {
        if (hasValidRef(config)) {
          ref = config.ref;
        }
        if (hasValidKey(config)) {
          key = '' + config.key;
        }

        self = config.__self === undefined ? null : config.__self;
        source = config.__source === undefined ? null : config.__source;
        // Remaining properties are added to a new props object
        for (propName in config) {
          if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
            props[propName] = config[propName];
          }
        }
      }

      // Children can be more than one argument, and those are transferred onto
      // the newly allocated props object.
      var childrenLength = arguments.length - 2;
      if (childrenLength === 1) {
        props.children = children;
      } else if (childrenLength > 1) {
        var childArray = Array(childrenLength);
        for (var i = 0; i < childrenLength; i++) {
          childArray[i] = arguments[i + 2];
        }
        {
          if (Object.freeze) {
            Object.freeze(childArray);
          }
        }
        props.children = childArray;
      }

      // Resolve default props
      if (type && type.defaultProps) {
        var defaultProps = type.defaultProps;
        for (propName in defaultProps) {
          if (props[propName] === undefined) {
            props[propName] = defaultProps[propName];
          }
        }
      }
      {
        if (key || ref) {
          var displayName = typeof type === 'function' ? type.displayName || type.name || 'Unknown' : type;
          if (key) {
            defineKeyPropWarningGetter(props, displayName);
          }
          if (ref) {
            defineRefPropWarningGetter(props, displayName);
          }
        }
      }
      return ReactElement(type, key, ref, self, source, ReactCurrentOwner.current, props);
    }

    /**
     * Return a function that produces ReactElements of a given type.
     * See https://reactjs.org/docs/react-api.html#createfactory
     */

    function cloneAndReplaceKey(oldElement, newKey) {
      var newElement = ReactElement(oldElement.type, newKey, oldElement.ref, oldElement._self, oldElement._source, oldElement._owner, oldElement.props);

      return newElement;
    }

    /**
     * Clone and return a new ReactElement using element as the starting point.
     * See https://reactjs.org/docs/react-api.html#cloneelement
     */
    function cloneElement(element, config, children) {
      !!(element === null || element === undefined) ? invariant(false, 'React.cloneElement(...): The argument must be a React element, but you passed %s.', element) : void 0;

      var propName = void 0;

      // Original props are copied
      var props = _assign({}, element.props);

      // Reserved names are extracted
      var key = element.key;
      var ref = element.ref;
      // Self is preserved since the owner is preserved.
      var self = element._self;
      // Source is preserved since cloneElement is unlikely to be targeted by a
      // transpiler, and the original source is probably a better indicator of the
      // true owner.
      var source = element._source;

      // Owner will be preserved, unless ref is overridden
      var owner = element._owner;

      if (config != null) {
        if (hasValidRef(config)) {
          // Silently steal the ref from the parent.
          ref = config.ref;
          owner = ReactCurrentOwner.current;
        }
        if (hasValidKey(config)) {
          key = '' + config.key;
        }

        // Remaining properties override existing props
        var defaultProps = void 0;
        if (element.type && element.type.defaultProps) {
          defaultProps = element.type.defaultProps;
        }
        for (propName in config) {
          if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
            if (config[propName] === undefined && defaultProps !== undefined) {
              // Resolve default props
              props[propName] = defaultProps[propName];
            } else {
              props[propName] = config[propName];
            }
          }
        }
      }

      // Children can be more than one argument, and those are transferred onto
      // the newly allocated props object.
      var childrenLength = arguments.length - 2;
      if (childrenLength === 1) {
        props.children = children;
      } else if (childrenLength > 1) {
        var childArray = Array(childrenLength);
        for (var i = 0; i < childrenLength; i++) {
          childArray[i] = arguments[i + 2];
        }
        props.children = childArray;
      }

      return ReactElement(element.type, key, ref, self, source, owner, props);
    }

    /**
     * Verifies the object is a ReactElement.
     * See https://reactjs.org/docs/react-api.html#isvalidelement
     * @param {?object} object
     * @return {boolean} True if `object` is a ReactElement.
     * @final
     */
    function isValidElement(object) {
      return (typeof object === 'undefined' ? 'undefined' : _typeof(object)) === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
    }

    var SEPARATOR = '.';
    var SUBSEPARATOR = ':';

    /**
     * Escape and wrap key so it is safe to use as a reactid
     *
     * @param {string} key to be escaped.
     * @return {string} the escaped key.
     */
    function escape(key) {
      var escapeRegex = /[=:]/g;
      var escaperLookup = {
        '=': '=0',
        ':': '=2'
      };
      var escapedString = ('' + key).replace(escapeRegex, function (match) {
        return escaperLookup[match];
      });

      return '$' + escapedString;
    }

    /**
     * TODO: Test that a single child and an array with one item have the same key
     * pattern.
     */

    var didWarnAboutMaps = false;

    var userProvidedKeyEscapeRegex = /\/+/g;
    function escapeUserProvidedKey(text) {
      return ('' + text).replace(userProvidedKeyEscapeRegex, '$&/');
    }

    var POOL_SIZE = 10;
    var traverseContextPool = [];
    function getPooledTraverseContext(mapResult, keyPrefix, mapFunction, mapContext) {
      if (traverseContextPool.length) {
        var traverseContext = traverseContextPool.pop();
        traverseContext.result = mapResult;
        traverseContext.keyPrefix = keyPrefix;
        traverseContext.func = mapFunction;
        traverseContext.context = mapContext;
        traverseContext.count = 0;
        return traverseContext;
      } else {
        return {
          result: mapResult,
          keyPrefix: keyPrefix,
          func: mapFunction,
          context: mapContext,
          count: 0
        };
      }
    }

    function releaseTraverseContext(traverseContext) {
      traverseContext.result = null;
      traverseContext.keyPrefix = null;
      traverseContext.func = null;
      traverseContext.context = null;
      traverseContext.count = 0;
      if (traverseContextPool.length < POOL_SIZE) {
        traverseContextPool.push(traverseContext);
      }
    }

    /**
     * @param {?*} children Children tree container.
     * @param {!string} nameSoFar Name of the key path so far.
     * @param {!function} callback Callback to invoke with each child found.
     * @param {?*} traverseContext Used to pass information throughout the traversal
     * process.
     * @return {!number} The number of children in this subtree.
     */
    function traverseAllChildrenImpl(children, nameSoFar, callback, traverseContext) {
      var type = typeof children === 'undefined' ? 'undefined' : _typeof(children);

      if (type === 'undefined' || type === 'boolean') {
        // All of the above are perceived as null.
        children = null;
      }

      var invokeCallback = false;

      if (children === null) {
        invokeCallback = true;
      } else {
        switch (type) {
          case 'string':
          case 'number':
            invokeCallback = true;
            break;
          case 'object':
            switch (children.$$typeof) {
              case REACT_ELEMENT_TYPE:
              case REACT_PORTAL_TYPE:
                invokeCallback = true;
            }
        }
      }

      if (invokeCallback) {
        callback(traverseContext, children,
        // If it's the only child, treat the name as if it was wrapped in an array
        // so that it's consistent if the number of children grows.
        nameSoFar === '' ? SEPARATOR + getComponentKey(children, 0) : nameSoFar);
        return 1;
      }

      var child = void 0;
      var nextName = void 0;
      var subtreeCount = 0; // Count of children found in the current subtree.
      var nextNamePrefix = nameSoFar === '' ? SEPARATOR : nameSoFar + SUBSEPARATOR;

      if (Array.isArray(children)) {
        for (var i = 0; i < children.length; i++) {
          child = children[i];
          nextName = nextNamePrefix + getComponentKey(child, i);
          subtreeCount += traverseAllChildrenImpl(child, nextName, callback, traverseContext);
        }
      } else {
        var iteratorFn = getIteratorFn(children);
        if (typeof iteratorFn === 'function') {
          {
            // Warn about using Maps as children
            if (iteratorFn === children.entries) {
              !didWarnAboutMaps ? warning$1(false, 'Using Maps as children is unsupported and will likely yield ' + 'unexpected results. Convert it to a sequence/iterable of keyed ' + 'ReactElements instead.') : void 0;
              didWarnAboutMaps = true;
            }
          }

          var iterator = iteratorFn.call(children);
          var step = void 0;
          var ii = 0;
          while (!(step = iterator.next()).done) {
            child = step.value;
            nextName = nextNamePrefix + getComponentKey(child, ii++);
            subtreeCount += traverseAllChildrenImpl(child, nextName, callback, traverseContext);
          }
        } else if (type === 'object') {
          var addendum = '';
          {
            addendum = ' If you meant to render a collection of children, use an array ' + 'instead.' + ReactDebugCurrentFrame.getStackAddendum();
          }
          var childrenString = '' + children;
          invariant(false, 'Objects are not valid as a React child (found: %s).%s', childrenString === '[object Object]' ? 'object with keys {' + Object.keys(children).join(', ') + '}' : childrenString, addendum);
        }
      }

      return subtreeCount;
    }

    /**
     * Traverses children that are typically specified as `props.children`, but
     * might also be specified through attributes:
     *
     * - `traverseAllChildren(this.props.children, ...)`
     * - `traverseAllChildren(this.props.leftPanelChildren, ...)`
     *
     * The `traverseContext` is an optional argument that is passed through the
     * entire traversal. It can be used to store accumulations or anything else that
     * the callback might find relevant.
     *
     * @param {?*} children Children tree object.
     * @param {!function} callback To invoke upon traversing each child.
     * @param {?*} traverseContext Context for traversal.
     * @return {!number} The number of children in this subtree.
     */
    function traverseAllChildren(children, callback, traverseContext) {
      if (children == null) {
        return 0;
      }

      return traverseAllChildrenImpl(children, '', callback, traverseContext);
    }

    /**
     * Generate a key string that identifies a component within a set.
     *
     * @param {*} component A component that could contain a manual key.
     * @param {number} index Index that is used if a manual key is not provided.
     * @return {string}
     */
    function getComponentKey(component, index) {
      // Do some typechecking here since we call this blindly. We want to ensure
      // that we don't block potential future ES APIs.
      if ((typeof component === 'undefined' ? 'undefined' : _typeof(component)) === 'object' && component !== null && component.key != null) {
        // Explicit key
        return escape(component.key);
      }
      // Implicit key determined by the index in the set
      return index.toString(36);
    }

    function forEachSingleChild(bookKeeping, child, name) {
      var func = bookKeeping.func,
          context = bookKeeping.context;

      func.call(context, child, bookKeeping.count++);
    }

    /**
     * Iterates through children that are typically specified as `props.children`.
     *
     * See https://reactjs.org/docs/react-api.html#reactchildrenforeach
     *
     * The provided forEachFunc(child, index) will be called for each
     * leaf child.
     *
     * @param {?*} children Children tree container.
     * @param {function(*, int)} forEachFunc
     * @param {*} forEachContext Context for forEachContext.
     */
    function forEachChildren(children, forEachFunc, forEachContext) {
      if (children == null) {
        return children;
      }
      var traverseContext = getPooledTraverseContext(null, null, forEachFunc, forEachContext);
      traverseAllChildren(children, forEachSingleChild, traverseContext);
      releaseTraverseContext(traverseContext);
    }

    function mapSingleChildIntoContext(bookKeeping, child, childKey) {
      var result = bookKeeping.result,
          keyPrefix = bookKeeping.keyPrefix,
          func = bookKeeping.func,
          context = bookKeeping.context;

      var mappedChild = func.call(context, child, bookKeeping.count++);
      if (Array.isArray(mappedChild)) {
        mapIntoWithKeyPrefixInternal(mappedChild, result, childKey, function (c) {
          return c;
        });
      } else if (mappedChild != null) {
        if (isValidElement(mappedChild)) {
          mappedChild = cloneAndReplaceKey(mappedChild,
          // Keep both the (mapped) and old keys if they differ, just as
          // traverseAllChildren used to do for objects as children
          keyPrefix + (mappedChild.key && (!child || child.key !== mappedChild.key) ? escapeUserProvidedKey(mappedChild.key) + '/' : '') + childKey);
        }
        result.push(mappedChild);
      }
    }

    function mapIntoWithKeyPrefixInternal(children, array, prefix, func, context) {
      var escapedPrefix = '';
      if (prefix != null) {
        escapedPrefix = escapeUserProvidedKey(prefix) + '/';
      }
      var traverseContext = getPooledTraverseContext(array, escapedPrefix, func, context);
      traverseAllChildren(children, mapSingleChildIntoContext, traverseContext);
      releaseTraverseContext(traverseContext);
    }

    /**
     * Maps children that are typically specified as `props.children`.
     *
     * See https://reactjs.org/docs/react-api.html#reactchildrenmap
     *
     * The provided mapFunction(child, key, index) will be called for each
     * leaf child.
     *
     * @param {?*} children Children tree container.
     * @param {function(*, int)} func The map function.
     * @param {*} context Context for mapFunction.
     * @return {object} Object containing the ordered map of results.
     */
    function mapChildren(children, func, context) {
      if (children == null) {
        return children;
      }
      var result = [];
      mapIntoWithKeyPrefixInternal(children, result, null, func, context);
      return result;
    }

    /**
     * Count the number of children that are typically specified as
     * `props.children`.
     *
     * See https://reactjs.org/docs/react-api.html#reactchildrencount
     *
     * @param {?*} children Children tree container.
     * @return {number} The number of children.
     */
    function countChildren(children) {
      return traverseAllChildren(children, function () {
        return null;
      }, null);
    }

    /**
     * Flatten a children object (typically specified as `props.children`) and
     * return an array with appropriately re-keyed children.
     *
     * See https://reactjs.org/docs/react-api.html#reactchildrentoarray
     */
    function toArray(children) {
      var result = [];
      mapIntoWithKeyPrefixInternal(children, result, null, function (child) {
        return child;
      });
      return result;
    }

    /**
     * Returns the first child in a collection of children and verifies that there
     * is only one child in the collection.
     *
     * See https://reactjs.org/docs/react-api.html#reactchildrenonly
     *
     * The current implementation of this function assumes that a single child gets
     * passed without a wrapper, but the purpose of this helper function is to
     * abstract away the particular structure of children.
     *
     * @param {?object} children Child collection structure.
     * @return {ReactElement} The first and only `ReactElement` contained in the
     * structure.
     */
    function onlyChild(children) {
      !isValidElement(children) ? invariant(false, 'React.Children.only expected to receive a single React element child.') : void 0;
      return children;
    }

    function createContext(defaultValue, calculateChangedBits) {
      if (calculateChangedBits === undefined) {
        calculateChangedBits = null;
      } else {
        {
          !(calculateChangedBits === null || typeof calculateChangedBits === 'function') ? warningWithoutStack$1(false, 'createContext: Expected the optional second argument to be a ' + 'function. Instead received: %s', calculateChangedBits) : void 0;
        }
      }

      var context = {
        $$typeof: REACT_CONTEXT_TYPE,
        _calculateChangedBits: calculateChangedBits,
        // As a workaround to support multiple concurrent renderers, we categorize
        // some renderers as primary and others as secondary. We only expect
        // there to be two concurrent renderers at most: React Native (primary) and
        // Fabric (secondary); React DOM (primary) and React ART (secondary).
        // Secondary renderers store their context values on separate fields.
        _currentValue: defaultValue,
        _currentValue2: defaultValue,
        // These are circular
        Provider: null,
        Consumer: null
      };

      context.Provider = {
        $$typeof: REACT_PROVIDER_TYPE,
        _context: context
      };

      var hasWarnedAboutUsingNestedContextConsumers = false;
      var hasWarnedAboutUsingConsumerProvider = false;

      {
        // A separate object, but proxies back to the original context object for
        // backwards compatibility. It has a different $$typeof, so we can properly
        // warn for the incorrect usage of Context as a Consumer.
        var Consumer = {
          $$typeof: REACT_CONTEXT_TYPE,
          _context: context,
          _calculateChangedBits: context._calculateChangedBits
        };
        // $FlowFixMe: Flow complains about not setting a value, which is intentional here
        Object.defineProperties(Consumer, {
          Provider: {
            get: function get() {
              if (!hasWarnedAboutUsingConsumerProvider) {
                hasWarnedAboutUsingConsumerProvider = true;
                warning$1(false, 'Rendering <Context.Consumer.Provider> is not supported and will be removed in ' + 'a future major release. Did you mean to render <Context.Provider> instead?');
              }
              return context.Provider;
            },
            set: function set(_Provider) {
              context.Provider = _Provider;
            }
          },
          _currentValue: {
            get: function get() {
              return context._currentValue;
            },
            set: function set(_currentValue) {
              context._currentValue = _currentValue;
            }
          },
          _currentValue2: {
            get: function get() {
              return context._currentValue2;
            },
            set: function set(_currentValue2) {
              context._currentValue2 = _currentValue2;
            }
          },
          Consumer: {
            get: function get() {
              if (!hasWarnedAboutUsingNestedContextConsumers) {
                hasWarnedAboutUsingNestedContextConsumers = true;
                warning$1(false, 'Rendering <Context.Consumer.Consumer> is not supported and will be removed in ' + 'a future major release. Did you mean to render <Context.Consumer> instead?');
              }
              return context.Consumer;
            }
          }
        });
        // $FlowFixMe: Flow complains about missing properties because it doesn't understand defineProperty
        context.Consumer = Consumer;
      }

      {
        context._currentRenderer = null;
        context._currentRenderer2 = null;
      }

      return context;
    }

    function lazy(ctor) {
      return {
        $$typeof: REACT_LAZY_TYPE,
        _ctor: ctor,
        // React uses these fields to store the result.
        _status: -1,
        _result: null
      };
    }

    function forwardRef(render) {
      {
        if (typeof render !== 'function') {
          warningWithoutStack$1(false, 'forwardRef requires a render function but was given %s.', render === null ? 'null' : typeof render === 'undefined' ? 'undefined' : _typeof(render));
        } else {
          !(
          // Do not warn for 0 arguments because it could be due to usage of the 'arguments' object
          render.length === 0 || render.length === 2) ? warningWithoutStack$1(false, 'forwardRef render functions accept exactly two parameters: props and ref. %s', render.length === 1 ? 'Did you forget to use the ref parameter?' : 'Any additional parameter will be undefined.') : void 0;
        }

        if (render != null) {
          !(render.defaultProps == null && render.propTypes == null) ? warningWithoutStack$1(false, 'forwardRef render functions do not support propTypes or defaultProps. ' + 'Did you accidentally pass a React component?') : void 0;
        }
      }

      return {
        $$typeof: REACT_FORWARD_REF_TYPE,
        render: render
      };
    }

    function isValidElementType(type) {
      return typeof type === 'string' || typeof type === 'function' ||
      // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
      type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || (typeof type === 'undefined' ? 'undefined' : _typeof(type)) === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE);
    }

    function memo(type, compare) {
      {
        if (!isValidElementType(type)) {
          warningWithoutStack$1(false, 'memo: The first argument must be a component. Instead ' + 'received: %s', type === null ? 'null' : typeof type === 'undefined' ? 'undefined' : _typeof(type));
        }
      }
      return {
        $$typeof: REACT_MEMO_TYPE,
        type: type,
        compare: compare === undefined ? null : compare
      };
    }

    /**
     * ReactElementValidator provides a wrapper around a element factory
     * which validates the props passed to the element. This is intended to be
     * used only in DEV and could be replaced by a static type checker for languages
     * that support it.
     */

    var propTypesMisspellWarningShown = void 0;

    {
      propTypesMisspellWarningShown = false;
    }

    function getDeclarationErrorAddendum() {
      if (ReactCurrentOwner.current) {
        var name = getComponentName(ReactCurrentOwner.current.type);
        if (name) {
          return '\n\nCheck the render method of `' + name + '`.';
        }
      }
      return '';
    }

    function getSourceInfoErrorAddendum(elementProps) {
      if (elementProps !== null && elementProps !== undefined && elementProps.__source !== undefined) {
        var source = elementProps.__source;
        var fileName = source.fileName.replace(/^.*[\\\/]/, '');
        var lineNumber = source.lineNumber;
        return '\n\nCheck your code at ' + fileName + ':' + lineNumber + '.';
      }
      return '';
    }

    /**
     * Warn if there's no key explicitly set on dynamic arrays of children or
     * object keys are not valid. This allows us to keep track of children between
     * updates.
     */
    var ownerHasKeyUseWarning = {};

    function getCurrentComponentErrorInfo(parentType) {
      var info = getDeclarationErrorAddendum();

      if (!info) {
        var parentName = typeof parentType === 'string' ? parentType : parentType.displayName || parentType.name;
        if (parentName) {
          info = '\n\nCheck the top-level render call using <' + parentName + '>.';
        }
      }
      return info;
    }

    /**
     * Warn if the element doesn't have an explicit key assigned to it.
     * This element is in an array. The array could grow and shrink or be
     * reordered. All children that haven't already been validated are required to
     * have a "key" property assigned to it. Error statuses are cached so a warning
     * will only be shown once.
     *
     * @internal
     * @param {ReactElement} element Element that requires a key.
     * @param {*} parentType element's parent's type.
     */
    function validateExplicitKey(element, parentType) {
      if (!element._store || element._store.validated || element.key != null) {
        return;
      }
      element._store.validated = true;

      var currentComponentErrorInfo = getCurrentComponentErrorInfo(parentType);
      if (ownerHasKeyUseWarning[currentComponentErrorInfo]) {
        return;
      }
      ownerHasKeyUseWarning[currentComponentErrorInfo] = true;

      // Usually the current owner is the offender, but if it accepts children as a
      // property, it may be the creator of the child that's responsible for
      // assigning it a key.
      var childOwner = '';
      if (element && element._owner && element._owner !== ReactCurrentOwner.current) {
        // Give the component that originally created this child.
        childOwner = ' It was passed a child from ' + getComponentName(element._owner.type) + '.';
      }

      setCurrentlyValidatingElement(element);
      {
        warning$1(false, 'Each child in an array or iterator should have a unique "key" prop.' + '%s%s See https://fb.me/react-warning-keys for more information.', currentComponentErrorInfo, childOwner);
      }
      setCurrentlyValidatingElement(null);
    }

    /**
     * Ensure that every element either is passed in a static location, in an
     * array with an explicit keys property defined, or in an object literal
     * with valid key property.
     *
     * @internal
     * @param {ReactNode} node Statically passed child of any type.
     * @param {*} parentType node's parent's type.
     */
    function validateChildKeys(node, parentType) {
      if ((typeof node === 'undefined' ? 'undefined' : _typeof(node)) !== 'object') {
        return;
      }
      if (Array.isArray(node)) {
        for (var i = 0; i < node.length; i++) {
          var child = node[i];
          if (isValidElement(child)) {
            validateExplicitKey(child, parentType);
          }
        }
      } else if (isValidElement(node)) {
        // This element was passed in a valid location.
        if (node._store) {
          node._store.validated = true;
        }
      } else if (node) {
        var iteratorFn = getIteratorFn(node);
        if (typeof iteratorFn === 'function') {
          // Entry iterators used to provide implicit keys,
          // but now we print a separate warning for them later.
          if (iteratorFn !== node.entries) {
            var iterator = iteratorFn.call(node);
            var step = void 0;
            while (!(step = iterator.next()).done) {
              if (isValidElement(step.value)) {
                validateExplicitKey(step.value, parentType);
              }
            }
          }
        }
      }
    }

    /**
     * Given an element, validate that its props follow the propTypes definition,
     * provided by the type.
     *
     * @param {ReactElement} element
     */
    function validatePropTypes(element) {
      var type = element.type;
      var name = void 0,
          propTypes = void 0;
      if (typeof type === 'function') {
        // Class or function component
        name = type.displayName || type.name;
        propTypes = type.propTypes;
      } else if ((typeof type === 'undefined' ? 'undefined' : _typeof(type)) === 'object' && type !== null && type.$$typeof === REACT_FORWARD_REF_TYPE) {
        // ForwardRef
        var functionName = type.render.displayName || type.render.name || '';
        name = type.displayName || (functionName !== '' ? 'ForwardRef(' + functionName + ')' : 'ForwardRef');
        propTypes = type.propTypes;
      } else {
        return;
      }
      if (propTypes) {
        setCurrentlyValidatingElement(element);
        checkPropTypes(propTypes, element.props, 'prop', name, ReactDebugCurrentFrame.getStackAddendum);
        setCurrentlyValidatingElement(null);
      } else if (type.PropTypes !== undefined && !propTypesMisspellWarningShown) {
        propTypesMisspellWarningShown = true;
        warningWithoutStack$1(false, 'Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?', name || 'Unknown');
      }
      if (typeof type.getDefaultProps === 'function') {
        !type.getDefaultProps.isReactClassApproved ? warningWithoutStack$1(false, 'getDefaultProps is only used on classic React.createClass ' + 'definitions. Use a static property named `defaultProps` instead.') : void 0;
      }
    }

    /**
     * Given a fragment, validate that it can only be provided with fragment props
     * @param {ReactElement} fragment
     */
    function validateFragmentProps(fragment) {
      setCurrentlyValidatingElement(fragment);

      var keys = Object.keys(fragment.props);
      for (var i = 0; i < keys.length; i++) {
        var key = keys[i];
        if (key !== 'children' && key !== 'key') {
          warning$1(false, 'Invalid prop `%s` supplied to `React.Fragment`. ' + 'React.Fragment can only have `key` and `children` props.', key);
          break;
        }
      }

      if (fragment.ref !== null) {
        warning$1(false, 'Invalid attribute `ref` supplied to `React.Fragment`.');
      }

      setCurrentlyValidatingElement(null);
    }

    function createElementWithValidation(type, props, children) {
      var validType = isValidElementType(type);

      // We warn in this case but don't throw. We expect the element creation to
      // succeed and there will likely be errors in render.
      if (!validType) {
        var info = '';
        if (type === undefined || (typeof type === 'undefined' ? 'undefined' : _typeof(type)) === 'object' && type !== null && Object.keys(type).length === 0) {
          info += ' You likely forgot to export your component from the file ' + "it's defined in, or you might have mixed up default and named imports.";
        }

        var sourceInfo = getSourceInfoErrorAddendum(props);
        if (sourceInfo) {
          info += sourceInfo;
        } else {
          info += getDeclarationErrorAddendum();
        }

        var typeString = void 0;
        if (type === null) {
          typeString = 'null';
        } else if (Array.isArray(type)) {
          typeString = 'array';
        } else if (type !== undefined && type.$$typeof === REACT_ELEMENT_TYPE) {
          typeString = '<' + (getComponentName(type.type) || 'Unknown') + ' />';
          info = ' Did you accidentally export a JSX literal instead of a component?';
        } else {
          typeString = typeof type === 'undefined' ? 'undefined' : _typeof(type);
        }

        warning$1(false, 'React.createElement: type is invalid -- expected a string (for ' + 'built-in components) or a class/function (for composite ' + 'components) but got: %s.%s', typeString, info);
      }

      var element = createElement.apply(this, arguments);

      // The result can be nullish if a mock or a custom function is used.
      // TODO: Drop this when these are no longer allowed as the type argument.
      if (element == null) {
        return element;
      }

      // Skip key warning if the type isn't valid since our key validation logic
      // doesn't expect a non-string/function type and can throw confusing errors.
      // We don't want exception behavior to differ between dev and prod.
      // (Rendering will throw with a helpful message and as soon as the type is
      // fixed, the key warnings will appear.)
      if (validType) {
        for (var i = 2; i < arguments.length; i++) {
          validateChildKeys(arguments[i], type);
        }
      }

      if (type === REACT_FRAGMENT_TYPE) {
        validateFragmentProps(element);
      } else {
        validatePropTypes(element);
      }

      return element;
    }

    function createFactoryWithValidation(type) {
      var validatedFactory = createElementWithValidation.bind(null, type);
      validatedFactory.type = type;
      // Legacy hook: remove it
      {
        Object.defineProperty(validatedFactory, 'type', {
          enumerable: false,
          get: function get() {
            lowPriorityWarning$1(false, 'Factory.type is deprecated. Access the class directly ' + 'before passing it to createFactory.');
            Object.defineProperty(this, 'type', {
              value: type
            });
            return type;
          }
        });
      }

      return validatedFactory;
    }

    function cloneElementWithValidation(element, props, children) {
      var newElement = cloneElement.apply(this, arguments);
      for (var i = 2; i < arguments.length; i++) {
        validateChildKeys(arguments[i], newElement.type);
      }
      validatePropTypes(newElement);
      return newElement;
    }

    var React = {
      Children: {
        map: mapChildren,
        forEach: forEachChildren,
        count: countChildren,
        toArray: toArray,
        only: onlyChild
      },

      createRef: createRef,
      Component: Component,
      PureComponent: PureComponent,

      createContext: createContext,
      forwardRef: forwardRef,
      lazy: lazy,
      memo: memo,

      Fragment: REACT_FRAGMENT_TYPE,
      StrictMode: REACT_STRICT_MODE_TYPE,
      unstable_ConcurrentMode: REACT_CONCURRENT_MODE_TYPE,
      Suspense: REACT_SUSPENSE_TYPE,
      unstable_Profiler: REACT_PROFILER_TYPE,

      createElement: createElementWithValidation,
      cloneElement: cloneElementWithValidation,
      createFactory: createFactoryWithValidation,
      isValidElement: isValidElement,

      version: ReactVersion,

      __SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED: ReactSharedInternals
    };

    var React$2 = Object.freeze({
      default: React
    });

    var React$3 = React$2 && React || React$2;

    // TODO: decide on the top-level export form.
    // This is hacky but makes it work with both Rollup and Jest.
    var react = React$3.default || React$3;

    module.exports = react;
  })();
}

/***/ }),

/***/ "./node_modules/react/index.js":
/*!*************************************!*\
  !*** ./node_modules/react/index.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


if (false) {} else {
  module.exports = __webpack_require__(/*! ./cjs/react.development.js */ "./node_modules/react/cjs/react.development.js");
}

/***/ }),

/***/ "./node_modules/style-loader/lib/addStyles.js":
/*!****************************************************!*\
  !*** ./node_modules/style-loader/lib/addStyles.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getTarget = function (target) {
  return document.querySelector(target);
};

var getElement = (function (fn) {
	var memo = {};

	return function(target) {
                // If passing function in options, then use it for resolve "head" element.
                // Useful for Shadow Root style i.e
                // {
                //   insertInto: function () { return document.querySelector("#foo").shadowRoot }
                // }
                if (typeof target === 'function') {
                        return target();
                }
                if (typeof memo[target] === "undefined") {
			var styleTarget = getTarget.call(this, target);
			// Special case to return head of iframe instead of iframe itself
			if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
				try {
					// This will throw an exception if access to iframe is blocked
					// due to cross-origin restrictions
					styleTarget = styleTarget.contentDocument.head;
				} catch(e) {
					styleTarget = null;
				}
			}
			memo[target] = styleTarget;
		}
		return memo[target]
	};
})();

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__(/*! ./urls */ "./node_modules/style-loader/lib/urls.js");

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton && typeof options.singleton !== "boolean") options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
        if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else if (typeof options.insertAt === "object" && options.insertAt.before) {
		var nextSibling = getElement(options.insertInto + " " + options.insertAt.before);
		target.insertBefore(style, nextSibling);
	} else {
		throw new Error("[Style Loader]\n\n Invalid value for parameter 'insertAt' ('options.insertAt') found.\n Must be 'top', 'bottom', or Object.\n (https://github.com/webpack-contrib/style-loader#insertat)\n");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	options.attrs.type = "text/css";

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	options.attrs.type = "text/css";
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = options.transform(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ }),

/***/ "./node_modules/style-loader/lib/urls.js":
/*!***********************************************!*\
  !*** ./node_modules/style-loader/lib/urls.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */

module.exports = function (css) {
	// get current location
	var location = typeof window !== "undefined" && window.location;

	if (!location) {
		throw new Error("fixUrls requires window.location");
	}

	// blank or null?
	if (!css || typeof css !== "string") {
		return css;
	}

	var baseUrl = location.protocol + "//" + location.host;
	var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
	/*
 This regular expression is just a way to recursively match brackets within
 a string.
 	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
    (  = Start a capturing group
      (?:  = Start a non-capturing group
          [^)(]  = Match anything that isn't a parentheses
          |  = OR
          \(  = Match a start parentheses
              (?:  = Start another non-capturing groups
                  [^)(]+  = Match anything that isn't a parentheses
                  |  = OR
                  \(  = Match a start parentheses
                      [^)(]*  = Match anything that isn't a parentheses
                  \)  = Match a end parentheses
              )  = End Group
              *\) = Match anything and then a close parens
          )  = Close non-capturing group
          *  = Match anything
       )  = Close capturing group
  \)  = Match a close parens
 	 /gi  = Get all matches, not the first.  Be case insensitive.
  */
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function (fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl.trim().replace(/^"(.*)"$/, function (o, $1) {
			return $1;
		}).replace(/^'(.*)'$/, function (o, $1) {
			return $1;
		});

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(unquotedOrigUrl)) {
			return fullMatch;
		}

		// convert the url to a full url
		var newUrl;

		if (unquotedOrigUrl.indexOf("//") === 0) {
			//TODO: should we add protocol?
			newUrl = unquotedOrigUrl;
		} else if (unquotedOrigUrl.indexOf("/") === 0) {
			// path should be relative to the base url
			newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
		} else {
			// path should be relative to current directory
			newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};

/***/ }),

/***/ "./node_modules/webpack/buildin/amd-options.js":
/*!****************************************!*\
  !*** (webpack)/buildin/amd-options.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* WEBPACK VAR INJECTION */(function(__webpack_amd_options__) {/* globals __webpack_amd_options__ */
module.exports = __webpack_amd_options__;

/* WEBPACK VAR INJECTION */}.call(this, {}))

/***/ }),

/***/ "./node_modules/webpack/buildin/module.js":
/*!***********************************!*\
  !*** (webpack)/buildin/module.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function (module) {
	if (!module.webpackPolyfill) {
		module.deprecate = function () {};
		module.paths = [];
		// module.parent = undefined by default
		if (!module.children) module.children = [];
		Object.defineProperty(module, "loaded", {
			enumerable: true,
			get: function get() {
				return module.l;
			}
		});
		Object.defineProperty(module, "id", {
			enumerable: true,
			get: function get() {
				return module.i;
			}
		});
		module.webpackPolyfill = 1;
	}
	return module;
};

/***/ })

/******/ });
//# sourceMappingURL=easy-forms-block.js.map