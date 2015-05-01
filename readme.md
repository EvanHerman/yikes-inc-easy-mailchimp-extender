Yikes Inc. Easy MailChimp Free (*Beta - v1.1*)
=====================

[![License](https://poser.pugx.org/yikesinc/yikes-inc-easy-mailchimp-extender/license)](https://packagist.org/packages/yikesinc/yikes-inc-easy-mailchimp-extender)
[![Latest Stable Version](https://poser.pugx.org/yikesinc/yikes-inc-easy-mailchimp-extender/v/stable.svg)](https://packagist.org/packages/yikesinc/yikes-inc-easy-mailchimp-extender)
[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/yikes-inc-easy-mailchimp-extender.svg)
[![WordPress](https://img.shields.io/wordpress/v/yikes-inc-easy-mailchimp-extender.svg)
[![Rating](https://img.shields.io/wordpress/plugin/r/yikes-inc-easy-mailchimp-extender.svg)

<strong>Current settings migration works, but no forms will be migrated over. You'll need to re-create your opt-in forms.</strong>

This is a complete re-write of the original [Yikes Inc Easy MailChimp Extender](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) plugin.

The overall framework is in place, but the functionality is currently being built out. The staging repo will be the home to all versions of the re-write, for beta testing purposes, before we roll it out to the WordPress repository.

Readme.txt and documentation to follow.

<br />

#### Minimum Requirements
- WordPress 3.8+ 
- PHP v5.3+

#### Plugin Preview Screenshots
[Cloudup Gallery](https://cloudup.com/cDJtreQDIcJ)

#### Questions?
Have any questions? Feel free to open up an issue in the issue tracker and we'll get back to you as soon as possible.


#### Documentation In The Works...

##### Shortcode

`[yikes-mailchimp form="#"]`

##### Full List of Shortcode Parameters
- form - the ID of the form you want to display *(required)*
- submit - the text of the submit button below the form *(optional - defaults to "Submit")*
- title - display the title above the form (1 or 0) *(optional - defaults to 0)*
- description - display the description above the form (1 or 0) *(optional - defaults to 0)*

##### CSS Ready Classes

###### 2 Column Layout
- field-left-half / field-right-half - assign this class to place the field in the left/right column of a 2 column form layout.

###### 3 Column Layout
- field-left-third / field-right-third - assign this class to place the field in the left/right most column of a 3 column form layout. 

###### 2/3/4 Colum Radio Buttons
- option-2/3/4-col - split the radio or checkbox options into 2, 3 or 4 columns


##### Filters + Hooks

###### Filters
- yikes-mailchimp-form-title-FORM_ID - alter the output of the form title of the specified form
- yikes-mailchimp-form-description-FORM_ID - alter the output of the form description of the specified form
- yikes-easy-mc-redirect-timer - (ms : 1 second = 1000ms) alter the amount of time the user sees the success message before being redirected ( defaults to 1500ms );
- yikes-mailchimp-before-submission - catch the merge variables of all forms before they get sent over to MailChimp
- yikes-mailchimp-before-submission-FORM_ID - catch the merge variables of the specified form before they get sent over to MailChimp
- yikes-mailchimp-after-submission - catch the merge variables of all forms after they get sent over to MailChimp
- yikes-mailchimp-after-submission-FORM_ID - catch the merge variables of the specified form after they get sent over to MailChimp

###### Hooks
- yikes-inc-easy-mc-post-submission - do something with the user email + form data on form submission
- yikes-inc-easy-mc-post-submission-FORM_ID - do something with the user email + form data on form submission (specific form)
- yikes-easy-mc-before-form-FORM_ID - output content before a specific form
- yikes-easy-mc-before-form - output content before all forms

<strong>Widgets</strong>
- yikes-mailchimp-widget-before-form - insert custom content after the widget title and before the MailChimp form (effects ALL widget forms)
- yikes-mailchimp-widget-before-form-form_id - insert custom content after the widget title and before the MailChimp form (effects only the form whos ID you've specified in the hook)
- yikes-mailchimp-widget-after-form - insert custom content after the MailChimp form (effects ALL widget forms)
- yikes-mailchimp-widget-after-form-form_id - insert custom content after the MailChimp form (effects only the form whos ID you've specified in the hook)

###### Hooks for Extensions
- yikes-inc-mailchimp-pro-menu - hook to add additional menu items inside of the parent plugin menu