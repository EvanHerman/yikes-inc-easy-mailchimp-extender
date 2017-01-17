[![Build Status](https://travis-ci.org/yikesinc/yikes-inc-easy-mailchimp-extender.svg?branch=staging)](https://travis-ci.org/yikesinc/yikes-inc-easy-mailchimp-extender)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/quality-score.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/?branch=staging)
[![Code Coverage](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/coverage.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/?branch=staging)
[![Build Status](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/build.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/build-status/staging)
Easy Forms for MailChimp
=====================
[![WordPress](https://img.shields.io/wordpress/plugin/dt/yikes-inc-easy-mailchimp-extender.svg)]()
![Rating](https://img.shields.io/wordpress/plugin/r/yikes-inc-easy-mailchimp-extender.svg)
[![License](https://poser.pugx.org/yikesinc/yikes-inc-easy-mailchimp-extender/license)](https://packagist.org/packages/yikesinc/yikes-inc-easy-mailchimp-extender)
![WordPress plugin](https://img.shields.io/wordpress/plugin/v/yikes-inc-easy-mailchimp-extender.svg)
![WordPress](https://img.shields.io/wordpress/v/yikes-inc-easy-mailchimp-extender.svg)

<strong>Latest Stable Beta Release Candidate</strong>

This is the development repo for the [Easy Forms for MailChimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) plugin by [YIKES, Inc.](https://yikesinc.com/) where we work on new features and bug fixes.

Feel free to beta test this plugin now, but remember, it's not a stable release.


#### Minimum Requirements
- WordPress 3.8+
- PHP v5.3+

#### Plugin Preview Screenshots
[Cloudup Gallery](https://cloudup.com/cyfImk387Ez)


#### Documentation

For full documentation, please visit [our Knowledge Base](https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/).

####### Integrations
Easy Forms for MailChimp integrates well with many popular third party plugins for WordPress:

* WooCommerce
* Easy Digital Downloads
* BuddyPress
* BbPress
* Contact Form 7
* Visual Composer

#### Questions?
Have any questions? Feel free to open up an issue in the issue tracker and we'll get back to you as soon as possible.

<hr />

#### Grunt.js

To develop with Grunt place Gruntfile.js file inside the plugin root during development (/wp-content/yikes-inc-easy-mailchimp-extender/). The paths inside Gruntfile.js are setup relative to the plugin root, so placing it anywhere else without changing the paths will cause an error.

First, make sure you install the latest version of Grunt to the local project directory.

```bash
$ npm install -g grunt -cli
```

```bash
$ npm install -g grunt
```

Finally, run the [install](https://www.npmjs.com/package/grunt-auto-install) task to install the required dependencies.

```bash
$ npm install
```

##### Shortcode

`[yikes-mailchimp form="#"]`

##### Full List of Shortcode Parameters
- form - the ID of the form you want to display *(required)*
- submit - the text of the submit button below the form *(optional - defaults to "Submit")*
- title - display the title above the form (1 or 0) *(optional - defaults to 0)*
- description - display the description above the form (1 or 0) *(optional - defaults to 0)*

##### API Key Constant

- A few users requested the ability to store their API key in a PHP constant, inside of wp-config.php. With version 6.1.2, users can now define a new constant inside of wp-config.php, `YIKES_MC_API_KEY`, and assign the API key there, which will then be used throughout the plugin. <em>Note: When you define the constant, you'll still need to head into the settings page and update the plugin options.</em>

Example:
`define( 'YIKES_MC_API_KEY', '12345679-us2' );`

##### CSS Ready Classes

###### 2 Column Layout
- field-left-half / field-right-half - assign this class to place the field in the left/right column of a 2 column form layout.

###### 3 Column Layout
- field-left-third / field-right-third - assign this class to place the field in the left/right most column of a 3 column form layout.

###### 2/3/4 Colum Radio Buttons
- option-2/3/4-col - split the radio or checkbox options into 2, 3 or 4 columns


##### Filters + Hooks

###### Filters

- `yikes-mailchimp-form-title` - Alter the output of the form title of the specified form. @params: $title, $form_id
- `yikes-mailchimp-form-description` - Alter the output of the form description of the specified form. @params: $description, $form_id
- `yikes-mailchimp-redirect-timer` - Alter the amount of time in *milliseconds* the user sees the success message before being redirected. Default: `1500` - @params: $time, $form_id
- `yikes-mailchimp-redirect-url` - Alter the URL of the page that the user will be redirected to on a successful submission. @params: $url, $form_id, $page_data
- `yikes-mailchimp-filter-before-submission` and `yikes-mailchimp-filter-before-submission-{$form_id}` - Catch the merge variables before they get sent over to MailChimp. @params: $merge_variables
- `yikes-mailchimp-after-submission` and `yikes-mailchimp-after-submission-{$form_id}` - Catch the merge variables after they get sent over to MailChimp. @params: $merge_variables
- `yikes-mailchimp-user-role-access` - Alter who can access this plugin page by capability. Default: `manage_options` - @params: $capability
- `yikes-mailchimp-international-phone-pattern` - Alter the regex pattern for acceptable international phone number formats. Default: `'[0-9,-,+]{1,}'` - @params: $regex_pattern
- `yikes-mailchimp-us-phone-pattern` - Alter the regex pattern for acceptable US phone number formats. Default: `^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$` - @params: $regex_pattern
- `yikes-mailchimp-zip-pattern` - Alter the zip code regex pattern. Default: `\d{5,5}(-\d{4,4})?` - @params: $regex_pattern
- `yikes-mailchimp-process-default-tag` - Alter the default form field value for text fields. @params: $default_value
- `yikes-mailchimp-{$merge_tag}-label` - Alter the specified form field's or interest group's label text. @params: $label
- `yikes-mailchimp-{$merge_tag}-description` - Alter the specified form field's or interest group's description text (note: if targeting an interest group, it will be the group_id instead of the merge tag). @params: $description_content, $form_id.
- `yikes-mailchimp-address-{$type}-label` - Alter the field sub-label text for a specific address field (e.g. addr1). @params: $label
- `yikes-mailchimp-form-submit-button` - Alter the submit button to your liking. @params: $submit_button, $form_id
- `yikes-mailchimp-form-submit-button-text` - Alter the submit button text. Default: `submit` - @params: $submit_button_text, $form_id
- `yikes-mailchimp-form-submit-button-classes` - Add additional classes to the submit button for further styling. @params: $classes, $form_id
- `yikes-mailchimp-form-container-class` - Add additional classes to the `<section>` element container that wraps the form. @params: $class_name, $form_id
- `yikes-mailchimp-form-class` - Add additional classes to `<form>` element of your opt-in form. @params: $class_name, $form_id
- `yikes-mailchimp-front-end-form-action-links` - Add custom front end action links alongside Edit Form, Customize Form etc. @params: $form_action_links, $form_id, $form_name
- `yikes-mailchimp-custom-default-value-tags` - Define your own custom pre-defined tags to populate the default value fields with - these tags appear in the modal. @params: $pre_defined_tags_array
- `yikes-mailchimp-parse-custom-default-value` - Process your custom default merge tag into a custom value to populate the form field with (note: Goes hand in hand with `yikes-mailchimp-custom-default-value-tags` filter). @params: $default_tag
- `yikes-mailchimp-field-data` - Filter form field data such as placeholder, label, etc. @params: $field_array, $field, $form_id
- `yikes-mailchimp-subscriber-count-value` - Alter the returned value for the total subscriber count of a given MailChimp list. @params: $subscriber_count
- `yikes-mailchimp-frontend-content` - Custom content filter for this plugin to prevent others from hooking in where not wanted.
- `yikes-mailchimp-recaptcha-parameters` - Alter any of the possible reCAPTCHA shortcode parameters. @params: $recaptcha_parameter_array, $form_id
- `yikes-mailchimp-preloader` - Add a custom preloader image. Default: WordPress's wpspin_light.gif. @params: $url_to_spinner_gif
- `yikes-mailchimp-update-email-subject` - Filter the subject line for the email that is generated and sent when a user needs to update their subscription info. @params: $email_subject
- `yikes-mailchimp-update-email-content` - Filter the content of the email that is generated and sent when a user needs to update their subscription info. @params: $email_body
- `yikes-mailchimp-success-response` - Alter *any* success response text. @params: $response_text, $form_id, $submitted_form_variables
- `yikes-mailchimp-default-country-value` - Alter the default country selected in the country dropdown. Default: `US` - @params: $country_slug
- `yikes-mailchimp-recaptcha-required-error` - Filter the error displayed back to the user when an error occurs during the reCAPTCHA submission process. @params: $error_text, $form_id
- `yikes-mailchimp-sslverify` - Toggle sslverify on/off when attempting to validate your API key with the MailChimp API servers. Default: `true` (on) - @params: $use_ssl
- Version 6.3.0:
* `yikes-mailchimp-success-double-optin-response` - Filter the "Success: Double opt-in" custom message. @params: $message, $form_id
* `yikes-mailchimp-success-single-optin-response` - Filter the "Success: Single opt-in" custom message. @params: $message, $form_id
* `yikes-mailchimp-success-resubscribed-response` - Filter the "Success: Re-subscriber" custom message. @params: $message, $form_id
* `yikes-mailchimp-user-already-subscribed-link-text` - Filter the "Success: Re-subscriber with link to email profile update message" custom message. @params: $message, $form_id
* `yikes-mailchimp-general-error-response` - Filter the "Error: General" custom message. @params: $message, $form_id
* `yikes-mailchimp-user-already-subscribed-text` - Filter the "Error: Re-subscribers not permitted" custom message (note: this replaced `yikes-mailchimp-user-already-subscribed-error`). @params: $message, $form_id
* `yikes-mailchimp-filter-groups-before-submission` and `yikes-mailchimp-filter-groups-before-submission-{$form_id}` - Filter the interest groups before they're submitted. @params: $groups, $form_id
* `yikes-mailchimp-address-2-required` - Change the address 2 field's `required` value. Default: `''` (it is never required) - @params: $required, $form_id
* `yikes-mailchimp-filter-subscribe-request` and `yikes-mailchimp-filter-subscribe-request-{$form_id}` - Filter all of the fields sent over to the MailChimp API (not just the form fields). @params: $subscribe_body, $form_id
* `yikesinc_eme_default_api_version` - Filter the API version. Default: `3.0` - @params: $version
* `yikesinc_eme_api_url` - Filter the URL used for a request to the MailChimp API. @params: $full_path, $path
* `yikesinc_eme_api_user_agent` - Filter the user agent used in API request. @params: $user_agent
* `yikesinc_eme_api_auth_headers` - Filter the authentication headers used in the API request. @params: $auth_headers, $api_version
* `yikesinc_eme_api_headers` - Filter the headers used for a request to the MailChimp API. @params: $headers, $path, $method, $params
* `yikesinc_eme_api_timeout` - Filter the timeout (in *seconds*) used when sending an API request. Default: `15` @params: $timeout
* `yikesinc_eme_api_args` - Filter the arguments used for a request to the MailChimp API. @params: $args, $path, $method, $params

###### Hooks

- `yikes-mailchimp-form-submission` and `yikes-mailchimp-form-submission-{$form_id}` - Do something with the user email + form data on form submission. @params: $email, $merge_variables, $form_id, $notifications
- `yikes-mailchimp-after-submission` and `yikes-mailchimp-after-submission-{$form_id}` - Do something with only the $merge_variables (note: these actions are fired off directly after `yikes-mailchimp-form-submission`).
- `yikes-mailchimp-before-submission` and `yikes-mailchimp-before-submission-{$form_id}` - Do something with the $merge_variables before the API request. @params: $merge_variables
- `yikes-mailchimp-after-form` - Do something after a form has been loaded. @params: $form_id
- `yikes-mailchimp-before-checkbox` - Do something (e.g. output custom content) before the opt-in checkbox for all integrations. 
- `yikes-mailchimp-after-checkbox` - Do something (e.g. output custom content) after the opt-in checkbox for all integrations
- `yikes-mailchimp-support-page` - Do something (e.g. output custom content) on the support page (note: we use this to add our support form for premium users).
- `yikes-mailchimp-edit-form-section-links` - Add additional links to the edit form page next to 'Custom Messages'.
- `yikes-mailchimp-edit-form-sections` - Add custom section to the edit form page.
- `yikes-mailchimp-edit-form-notice` - Hook to display a custom notice on the edit form page.
- `yikes-mailchimp-shortcode-enqueue-scripts-styles` - Hook to enqueue custom scripts & styles wherever the shortcode is being used. @params: $form_id
- `yikes-mailchimp-additional-form-fields` - Define additional fields to add below all forms. @params: $form_data
- `yikes-mailchimp-custom-form-actions` - Add custom action links on the manage forms page (alongside Edit, Duplicate, Shortcode, Delete). @params: $form_id
- `yikes-mailchimp-list-form-fields-metabox` - Add additional content to the 'Form Fields' metabox on the view list page.
- `yikes-mailchimp-list-interest-groups-metabox` - Add additional content inside of the interest groups metabox on the view list page.

###### Hooks for Extensions

- `yikes-mailchimp-menu` - hook to add additional menu items inside of the "Easy MailChimp" menu item
- `yikes-mailchimp-settings-field` - hook to register additional settings fields for add-ons
- `yikes-mailchimp-ADDON-SLUG-options-path` - hook to load up a custom settings page


##### Helper Shortcodes

<strong>Display current number of subscribers</strong>
`[yikes-mailchimp-subscriber-count form="1"]`

Display the current number of subscribers for a given list. Pass in the form ID whos list you want to display. (also accepts list="mailchimp_list_id")

If you want to use the snippet inside of your form description, you can exclude the form ID and list ID altogether, and the shortcode will reference the list associated with the displayed form.

Example Form Description:
Join the `[yikes-mailchimp-subscriber-count]` happy subscribers who receive our mailing list!

which might display on the front end like:
Join the 1,543 happy subscribers who receive our mailign list!

###	Custom Edit Form Sections API
Since we've built out a few add-ons to extend the base functionality, we've also built out an API to allow users to rapidly define custom sections on the edit form screen. This allows you to assign additional data to your forms.

We use this API extensively to build out additional sections on the edit form page.

Example:

<em>First hook in to the proper locations, and attach a function:</em>
```php
add_action( 'yikes-mailchimp-edit-form-section-links' , 'add_custom_section_link' );
add_action( 'yikes-mailchimp-edit-form-sections' , 'render_custom_section' );
```
<em>Next , define your sections and fields by passing in a multi-dimensional array.</em>
```php
/* Add custom link to the links (next to fields & custom messages) */
public function add_custom_section_link() {
	// creating a new link on the edit form page
	Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section_link( array(
		'id' => 'custom-section', // section id
		'text' => 'Custom Section', // the text that will display in the link
		'icon' => 'admin-appearance' // dashicon icon class
	) );

	// creating a new link on the edit form page
	Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section_link( array(
		'id' => 'custom-section-2', // section id
		'text' => 'Custom Section 2', // the text that will display in the link
		'icon' => 'admin-appearance' // dashicon icon class
	) );
}

/* Add custom section associated with link created above */
public static function render_custom_section() {
	// defining a new section, associated with the link above
	Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section( array(
		'id' => 'custom-section',  // section id (must match link id above)
		'main_title' => 'Main Section Title', // title of the main block of this custom section
		'main_description' => __( 'This is a custom description for the main section' , 'test' ),
		'main_fields' => array(
			array(
				'label' => 'Custom Field #1', // label text for this field
				'placeholder' => 'Placeholder Value',  // placeholder value for the input field
				'type' => 'text', // type of field (text,select,checkbox,radio)
				'id' => 'custom-field-1', // field id - determines how data is saved in database
				// 'description' => __( 'Testing custom description for field #1' , 'test' ), // field description
			),
			array(
				'label' => 'Custom Field #2', // label text for this field
				'placeholder' => 'Placeholder Value #2', // placeholder value for the input field
				'type' => 'text', // type of field (text,select,checkbox,radio)
				'id' => 'custom-field-2', // field id - determines how data is saved in database
				'description' => __( 'Testing custom description for field #2' , 'test' ), // field description
			),
		),
		'sidebar_title' => 'Sidebar Section Title', // sidebar title of the sidebar section
		'sidebar_description' => __( 'This is a custom description for the sidebar section' , 'test' ),
		'sidebar_fields' => array(
			array(
				'label' => 'Dropdown Field',
				'type' => 'select',
				'options' => array(
					'1' => 'one',
					'2' => 'two',
					'3' => 'three',
				),
				'id' => 'select-field',
				'description' => __( 'this is a select field.' , 'test' ),
			),
		),
	) );
}
```
