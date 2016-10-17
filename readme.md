[![Build Status](https://travis-ci.org/yikesinc/yikes-inc-easy-mailchimp-extender.svg?branch=staging)](https://travis-ci.org/yikesinc/yikes-inc-easy-mailchimp-extender)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/quality-score.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/?branch=staging)
[![Code Coverage](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/coverage.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/?branch=staging)
[![Build Status](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/badges/build.png?b=staging)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-easy-mailchimp-extender/build-status/staging)
YIKES Inc. Easy Forms for MailChimp
=====================
[![WordPress](https://img.shields.io/wordpress/plugin/dt/yikes-inc-easy-mailchimp-extender.svg)]()
![Rating](https://img.shields.io/wordpress/plugin/r/yikes-inc-easy-mailchimp-extender.svg)
[![License](https://poser.pugx.org/yikesinc/yikes-inc-easy-mailchimp-extender/license)](https://packagist.org/packages/yikesinc/yikes-inc-easy-mailchimp-extender)
![WordPress plugin](https://img.shields.io/wordpress/plugin/v/yikes-inc-easy-mailchimp-extender.svg)
![WordPress](https://img.shields.io/wordpress/v/yikes-inc-easy-mailchimp-extender.svg)

<strong>Latest Stable Beta Release Candidate</strong>

This is the development repo for the [YIKES Inc Easy MailChimp Extender](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) plugin where we work on new features and bug fixes.

Feel free to beta test this plugin now, but remember, it's not a stable release.


#### Minimum Requirements
- WordPress 3.8+
- PHP v5.3+

#### Plugin Preview Screenshots
[Cloudup Gallery](https://cloudup.com/cyfImk387Ez)


#### Documentation

For full documentation, please visit [our Knowledge Base](https://yikesplugins.com/support/knowledge-base/product/easy-forms-for-mailchimp/).

####### Integrations
Easy Forms for MailChimp by YIKES integrates well with many popular third party plugins for WordPress:

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

To develop with grunt place Gruntfile.js file inside the plugin root during development (/wp-content/yikes-inc-easy-mailchimp-extender/). The paths inside Gruntfile.js are setup relative to the plugin root, so placing it anywhere else without changing the paths will cause an error.

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

- `yikes-mailchimp-form-title-FORM_ID` - alter the output of the form title of the specified form
- `yikes-mailchimp-form-description-FORM_ID` - alter the output of the form description of the specified form
- `yikes-mailchimp-redirect-timer` (ms : 1 second = 1000ms) alter the amount of time the user sees the success message before being redirected (default: 1500ms) (@parameters - $time, $form_id)
- `yikes-mailchimp-redirect-url` - Alter the URL of the page that the user will be redirected too on a successful submission. (@parameters - $url, $form_id, $page_data)
- `yikes-mailchimp-before-submission` - catch the merge variables of all forms before they get sent over to MailChimp
- `yikes-mailchimp-before-submission-FORM_ID` - catch the merge variables of the specified form before they get sent over to MailChimp
- `yikes-mailchimp-after-submission` - catch the merge variables of all forms after they get sent over to MailChimp
- `yikes-mailchimp-after-submission-FORM_ID` - catch the merge variables of the specified form after they get sent over to MailChimp
- `yikes-mailchimp-user-role-access` - Alter who can access this plugin page by capability (default 'manage_options' - admins)
- `yikes-mailchimp-admin-widget-capability` - Set the minimum user capability for users who can see/access dashboard widgets (Note: you can also use the `yikes-mailchimp-user-role-access` filter)
- `yikes-mailchimp-international-phone-pattern` - Alter the regex pattern for acceptable international phone number formats. (default: [0-9]{1,}) (process form shortcode.php - line 295)
- `yikes-mailchimp-us-phone-pattern` - Alter the regex pattern for acceptable US phone number formats. (default: [^(\([0-9]{3}\)|[0-9]{3}-)[0-9]{3}-[0-9]{4}$) (process form shortcode.php - line 295)
- `yikes-mailchimp-process-default-tag` - Pass the default tag through a filter to populate with dynamic content from the current site (process_form_shortcode.php - line 256)
- `yikes-mailchimp-MERGE_TAG-label` - Alter the specified field label text (affects standard fields & interest groups) (process_form_shortcode.php)
- `yikes-mailchimp-MERGE_TAG-description` - Alter the specified field description text (affects standard fields & interest groups) (process_form_shortcode.php)  (if targeting an interest group, it will be the group_id)
- `yikes-mailchimp-address-TYPE-label` - Alter the field sub-label text for a specified address field (affects only standard address fields) (process_form_shortcode.php)
- `yikes-mailchimp-form-submit-button` - Alter the submit button to your liking (process_form_shortcode.php line 660) (@parameters - $submit_button, $form_id)
- `yikes-mailchimp-form-submit-button-text` - Alter the submit button text value (process_form_shortcode.php line 660) (@parameters - $submit_button_text, $form_id )
- `yikes-mailchimp-form-submit-button-classes` - Add additional classes to the submit button for further styling (process_form_shortcode.php line 660) (@parameters - $classes, $form_id )
- `yikes-mailchimp-form-container-class` - add additional classes to the parent form container (process_form_shortcode.php line157) (@parameters - $content, $form_id)
- `yikes-mailchimp-form-class` - add additional classes to <form> element of your opt-in form (process_form_shortcode.php line157) (@parameters - $content, $form_id)
- `yikes-mailchimp-front-end-form-action-links` - Add custom front end action links alongside Edit Form, Customize Form etc. (@parameters - $form_action_links, $form_id, $form_name)
- `yikes-mailchimp-custom-default-value-tags` - Define your own custom pre-defined tags to populate the default value fields with - these tags appear in the modal - (@parameters - $pre_defined_tags_array)
- `yikes-mailchimp-parse-custom-default-value` - Process your custom default merge tag into a custom value to populate the form field with - (Goes hand in hand with `yikes-mailchimp-custom-default-value-tags` filter)
- `yikes-mailchimp-field-data` - Filter form field data such as placeholder, label, etc. (@parameters - $field_array, $field, $form_id) (process_form_shortcode.php - line 258 & process_preview_form_shortcode.php - line 258)
- `yikes-mailchimp-user-subscribe-api-request` - Alter the API request whenever a new user subscribed (@parameters - $api_request data, $form_id, $list_id, $email ) (process_form_shortcode.php/process_form_shortcode_ajax.php)
- `yikes-mailchimp-subscriber-count-value` - Adjust the returned value for the total subscriber count of a given MailChimp mailing list as needed. (@parameters - $subscriber_count) (yikes-mailchimp-subscriber-count.php- line 80)
- `yikes-mailchimp-interest-group-checkbox-error` - Alter the checkbox interest group required error response (displayed when a checkbox interest group is set to required, but is left blank)(@since 6.0.3)
- `yikes-mailchimp-frontend-content` - Custom content filter for this plugin to prevent others from hooking in where not wanted.
- `yikes-mailchimp-interest-group-required-top-error` - Alter the top of form error message. (displays only when interest group checkbox group is set to required and left blank (non-ajax only)) (@parameters - count of missing required fields (integer))
- `yikes-mailchimp-interest-group-checkbox-error` - Alter the error displayed above each required interest group checkbox group only. Effects checkbox groups only.
- `yikes-mailchimp-recaptcha-parameters` - Alter any of the possible reCaptcha shortcode parameters (process_form_shortcode.php line 68) (parameters: $recaptcha_parameter_array, $form_id)
- `yikes-mailchimp-checkbox-integration-subscribe-api-request` - Filter the API request sent to MailChimp. (parameters: @type - the integration type (ie: comment, contact_form_7 etc.)
- `yikes-mailchimp-frontend-date-picker-format` - Alter the date format. For additional help, see the [knowledge base](https://yikesplugins.com/support/knowledge-base/how-do-i-change-the-frontend-date-picker-format/).
- `yikes-mailchimp-preloader` - Filter allowing users to setup a custom preloader image.
- `yikes-mailchimp-update-email-subject` - Filter the subject line for the email that is generated and sent when a user needs to update their subscription info.
- `yikes-mailchimp-update-email-content` - Filter the content of the email that is generated and sent when a user needs to update their subscription info.
- `yikes-mailchimp-user-already-subscribed-error` - Filter the 'xxx@example.com' is already subscribed to the list. (@parameters - $response, $form_id, $user_email)

###### Hooks

- `yikes-mailchimp-form-submission` - do something with the user email + form data on form submission
- `yikes-mailchimp-form-submission-FORM_ID` - do something with the user email + form data on form submission (specific form)
- `yikes-mailchimp-before-form` - output content before all forms (@parameters - $form_id)
- `yikes-mailchimp-after-form` - output content after all forms (@parameters - $form_id)
- `yikes-mailchimp-before-checkbox` - output custom content before the opt-in checkbox for all integrations
- `yikes-mailchimp-after-checkbox` - output custom content after the opt-in checkbox for all integrations
- `yikes-mailchimp-support-page` - output custom content on the support page (used to add our support form for premium users)
- `yikes-mailchimp-edit-form-section-links` - add additional links to the edit form page next to 'Custom Messages'
- `yikes-mailchimp-edit-form-sections` - add custom section to the edit form page
- `yikes-mailchimp-edit-form-notice` - hook to display a custom notice on the edit form page
- `yikes-mailchimp-shortcode-enqueue-scripts-styles` - hook to enqueue custom scripts & styles wherever the shortcode is being used (@paremeter $form_id - pass the id of the current form)
- `yikes-mailchimp-additional-form-fields` - define additional fields to add below all forms (@parameter $form_data - all data associated with the given form)
- `yikes-mailchimp-custom-form-actions` - add custom action links on the manage forms page (alongside Edit, Duplicate, Shortcode, Delete ) (@parameter $form_id - the id of the form)
- `yikes-mailchimp-api-curl-request` - custom action hook to disable curl verification (not recommended - see the following [KB article](https://yikesplugins.com/support/knowledge-base/i-receive-the-error-ssl-certificate-problem-unable-to-get-local-issuer-certificate-why/))
- `yikes-mailchimp-list-form-fields-metabox` - action hook allowing additional content to be added to the 'Form Fields' metabox on the view list page.
- `yikes-mailchimp-list-interest-groups-metabox` - action hook allowing users to add additional content inside of the interest groups metabox on the view list page.

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
