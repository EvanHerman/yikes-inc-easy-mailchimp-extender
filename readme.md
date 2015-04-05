Yikes Inc. Easy MailChimp (*Alpha*)
=====================

<strong>Note: Not yet ready for a production environment.</strong>

This is a complete re-write of the original [Yikes Inc Easy MailChimp Extender](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) plugin.

The overall framework is in place, but the functionality is currently being built out. The staging repo will be the home to all versions of the re-write, for beta testing purposes, before we roll it out to the WordPress repository.

Readme.txt and documentation to follow.

<br />

#### Plugin Preview Screenshots
[Cloudup Gallery](https://cloudup.com/cDJtreQDIcJ)


#### Questions?
Have any questions? Feel free to open up an issue in the issue tracker and we'll get back to you as soon as possible.


#### Documentation In The Works...

##### Shortcode

`[yikes-mailchimp form="#"]`

##### Shortcode Parameters
- form - the ID of the form you want to display *(required)*
- submit - the text of the submit button below the form *(optional - defaults to "Submit")*
- title - display the title above the form (1 or 0) *(optional - defaults to 0)*
- description - display the description above the form (1 or 0) *(optional - defaults to 0)*

##### CSS Ready Classes

###### 2 Column Layout
- field-half-left / field-half-right - assign this class to place the field in the left/right column of a 2 column form layout.

###### 3 Column Layout
- field-third-left / field-third-right - assign this class to place the field in the left/right most column of a 3 column form layout. 

###### 2/3/4 Colum Radio Buttons
- option-2/3/4-col - split the radio or checkbox options into 2, 3 or 4 columns


##### Filters + Hooks

###### Filters
- yikes-easy-mc-form-title - alter the output of the form title
- yikes-easy-mc-form-description - alter the output of the form description
- yikes-easy-mc-redirect-timer - (ms : 1 second = 1000ms) alter the amount of time the user sees the success message before being redirected ( defaults to 1500ms );

###### Hooks
- yikes-easy-mc-before-form-FORM_ID - output content before a specific form
- yikes-easy-mc-before-form - output content before all forms