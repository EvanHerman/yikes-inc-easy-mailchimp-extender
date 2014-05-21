=== Easy MailChimp Forms ===
Contributors: yikesinc, hiwhatsup, liljimmi, eherman24, seriouslysean
Donate link: http://yikesinc.com
Tags: mailchimp, marketing, email, mailing lists, newsletter, signup, forms, signup form
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 4.2
License: GPLv2 or later

Easy MailChimp Forms allows you to painlessly add MailChimp signup forms to your WordPress site.

== Description ==

Easy MailChimp Forms allows you to painlessly add MailChimp signup forms to your WordPress site. You can add forms to posts, pages or sidebars with shortcodes, widgets or template tags. Simply copy and paste your MailChimp API Key into the plugin admin settings and it will pull in all your MailChimp lists. From there you can choose the lists you want to make forms for. For a single list you can check off the fields you want to include on your form and order them via an easy drag-and-drop interface. This plugin adds plenty of CSS selectors to the form code allowing you to completely customize the look of your forms.

*Note:* You will need a MailChimp API key to allow this plugin to communicate with your MailChimp account. For help on retrieving your API key, check out [question #4 of the FAQ](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/faq/). You can also read the MailChimp knowledge base article [Where can I find my API key and how can I use the API](http://kb.mailchimp.com/article/where-can-i-find-my-api-key/).

**Features**

* Easily import MailChimp forms from a MailChimp account
* Use MailChimp Interest Group/Segments
* Add MailChimp forms sidebars/widgetized areas with widgets
* Set forms to single or double opt-in
* Customize the success message
* Customize the submit button text
* Redirect users to a page on submission
* Unsubscribe users From MailChimp lists
* View subscriber MailChimp profiles
* View individual form subscriber count
* Display multiple forms on a single page
* Add commenters to your MailChimp lists with a comment form opt-in check box 
* Easily add forms to pages and posts with a button in the page/post editor
* Use cURL error detection to troubleshoot MailChimp connection issues


Instructions on how to use the plugin can be [found in the FAQ](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/faq/). If you experience any problems, please submit a New Issue on our [Github Issue Tracker](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues) and we'll look in to it as soon as possible.


== Installation ==

1. Download the plugin .zip file
2. Log in to yourdomain.com/wp-admin
3. Click Plugins -> Add New -> Upload
4. Activate the plugin
5. Head over to [MailChimp.com](http://www.mailchimp.com) and login.
6. On the right hand menu, click your profile picture and select 'Account Settings' and then go to 'Extras > API Keys'.
7. Enter your API key into the text field inside 'MailChimp Forms > MailChimp Settings'
8. Go to the 'Manage List Forms' page, start importing forms from MailChimp and adding them to posts, pages and widgets!

== Frequently Asked Questions ==

= Do I need to have a MailChimp Account? =
Yes, you can register for one for free at [MailChimp](https://mailchimp.com/signup/ "MailChimp Signup").

= Do I need to have lists already set up in MailChimp? =
Yes, you have to have at least 1 list set up in MailChimp. 

For more help, visit the MailChimp Support article [How do I create a new list?](http://kb.mailchimp.com/article/where-can-i-find-my-api-key "How do I create a new list?")

= What do I do first? =
The first step is to add your MailChimp API key. This will allow your site to communicate with your MailChimp account. This is done on the plugin "MailChimp Settings" page.

= Where do I find my API Key? =
From your MailChimp Dashboard, click on your account name in the upper left hand corner of the screen under the MailChimp logo to expose the "Account Settings" menu. Click on "Account Settings" to go to the Account Settings screen and the click on the "Extras" menu. Under "Extras" choose "API keys." From there you can create a new key if you do not already have one.

For more help, visit the MailChimp Support article [Where can I find my API Key?](http://kb.mailchimp.com/article/where-can-i-find-my-api-key "Where can I find my API Key?")

= How to I add my MailChimp lists? =
After you add your API key, the plugin will fetch all of your MailChimp list information and load it into the plugin admin. Go to the plugin "Manage List Forms" page to choose the lists you want to make forms for.

= How do I add the MailChimp signup forms to my site? =
You can use a shortcode to add a form to a page or post, use the MailChimp form button in the visual editor or use a widget to add it to a sidebar. Each list on the plugin "Manage List Forms" displays a shortcode at the top. Copy the shortcode and paste it into any post or page where you want a form to appear. For example, if my form had the shortcode [yks-mailchimp-list id="1234567891"] I would copy and paste that into the page or post I wanted to add that form to.

= I input a valid MailChimp API key, but it returns invalid every time. I've already tried a new API key, but no dice. What's up? =

**Step 1:** Ensure that cURL is enabled at the server level. You will see an error at the top of the settings page if cURL is disabled. If you see no error, continue to step 2.

**Step 2:** If you have entered your MailChimp API key and are still getting the error "*Error: Please enter a valid Mail Chimp API Key*," please check the developer console inside your browser for further information. 

- Right-click in the API Key input field, and select 'Inpsect Element'
- Once the developer console is open, select the Console tab to see a more specific error.

For information on how to use your browser's developer console, read the WordPress Codex article, [Using Your Browser to Diagnose JavaScript Errors](http://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors).

<strong>Possible Errors And Resolutions</strong>

* Could not resolve host: xxxx.api.mailchimp.com  - the host you have provided is incorrect. The host is the string after the last dash (example: us2)
* Invalid Mailchimp API Key: xxxxxxxxxxxxxxxxxx-xxx - Your API key is invalid. You can confirm a valid key by logging into [MailChimp](http://mailchimp.com) and checking the active API key registered to your account.

= I don't want the form to be the 100% width. How can I adjust the width myself? =
You can adjust the width of the forms on your site by changing the width of the element with the class .yks-mailchimpFormContainer. This is the parent container for the form. Adjusting this width will control the width of the input fields inside of it.

= How can I translate this plugin?
Easy MailChimp Forms is now translated into multiple languages:

* Arabic
* Chinese
* English
* French
* German
* Greek
* Hebrew
* Hindi
* Hong Kong
* Italian
* Japanese
* Korean
* Persian
* Portuguese (Brazilian)
* Portuguese (European)
* Romanian
* Russian
* Spanish
* Swedish
* Taiwanese
* Tamil
* Urdu
* Vietnamese
* Welsh

Read the Codex article [Installing WordPress in Your Language](http://codex.wordpress.org/Installing_WordPress_in_Your_Language) for more information. Also, please refer to our [Developer Docs](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/).

= Do you provide any hooks, or filters for me to take advantage of? =
Yes! With the 4.0 version of our plugin we have added a few hooks that allow you to add or manipulate existing data. Check out the [Other Notes](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/) tab for more information.


== Developer Docs. ==

**Text Domain**

**Name:** 
`yikes-inc-easy-mailchimp-extender`

**Description:** 
90% of the plug-in is localized and ready for translation. This means that you can manipulate text using the provided text domain, using a gettext filter.

**Accepted Parameters:** 
N/A

**Example:** 
This example will alter text on the admin dashboard on the manage lists page.

`
<?php
	/**
	* Change Specific Test on the 'Manage List Forms' page.
	*
	*/
	function theme_change_comment_field_names( $translated_text, $text, $domain ) {

	  switch ( $translated_text ) {

		case 'Your Lists' :

		  $translated_text = __( 'MailChimp Lists', 'yikes-inc-easy-mailchimp-extender' );
		  break;

		case 'Save Form Settings' :

		  $translated_text = __( 'Save Form', 'yikes-inc-easy-mailchimp-extender' );
		  break;

		case 'Create a Form For This List' :

		  $translated_text = __( '<== Import This List', 'yikes-inc-easy-mailchimp-extender' );
		  break;

	  }

		return $translated_text;
	}
	add_filter( 'gettext', 'theme_change_comment_field_names', 20, 3 );
?>
`
<br />

**Hooks**
 
**Hook Name:** 
`yks_mc_before_form_$formID`

**Description:** 
Used to place content before a specific MailChimp form. Use the form id to specify which form to place text before.

**Accepted Parameters:**
N/A

**Example:** 
This example will print out a thank you message before a specific form.


`
<?php
	/**
	* Function to add text before the form with ID '0b071c0bd1'
	* You can get form ID's from the 'MailChimp List' page
	*/
	function custom_before_form_action() {
		echo '<p>Thanks for checking out our mailing list. Fill out the form below to get started!</p>';
	}
	add_action( 'yks_mc_before_form_0b071c0bd1' , 'custom_before_form_action' );
?>
`
*Note: in our add_action call we add the specific form ID to target a single form.*

<br />

**Hook Name:**
`yks_mc_after_form_$formID`

**Description:** 
Used to place content after a specific MailChimp form. Use the form id to specify which form to place text after.

**Accepted Parameters:**
N/A

**Example:**
This example will print out a disclaimer message after a specific form._


`
<?php
	/**
	* Function to add text after the form with ID '0b071c0bd1'
	* You can get form ID's from the 'MailChimp List' page
	*/
	function custom_after_form_action() {
		echo '<p><em>Your information is for internal use only, and will never be shared with or sold to anyone.</em></p>';
	}
	add_action( 'yks_mc_after_form_0b071c0bd1' , 'custom_after_form_action' );
?>
`

**Note**: in our add_action call we add the specific form ID to target a single form.

<br />

**Hook Name:**
`yks_mc_before_form`

**Description:** 
Used to place content before *all* MailChimp Forms.

**Accepted Parameters:** 
N/A

**Example:** 
`
<?php
	/**
	* This example will print out a disclaimer to the user,
	* above all MailChimp forms. 
	*/
	function custom_before_all_forms_action() {
		echo '<p><em>Your information is for internal use only, and will never be shared with or sold to anyone.</em></p>';
	}
	add_action( 'yks_mc_before_form' , 'custom_before_all_forms_action' );
?>
`

<br />

**Hook Name:**
`yks_mc_after_form`

**Description:** 
Used to place content after *all* MailChimp Forms.

**Accepted Parameters:**
N/A

**Example:**
`
<?php
	/**
	* This example will print out a disclaimer to the user,
	* below all MailChimp forms. 
	*/
	function custom_after_all_forms_action() {
		echo '<p><em>Your information is for internal use only, and will never be shared with or sold to anyone.</em></p>';
	}
	add_action( 'yks_mc_after_form' , 'custom_after_all_forms_action' );
?>
`
<br />

**Filters**

**Filter Name:**
`yikes_mc_get_form_data`

**Accepted Parameters:** 
`$Form_ID` and `$merge_variables`

**Parameter Info:**	

`$Form_ID` = the ID of the specific MailChimp Form ( can be retrieved from the 'MailChimp Forms > Manage List Forms' menu ).

`$merge_variables` = a multi-dimensional array containing all user entered data being sent to the MailChimp API (The email, first name, last name etc. will be contained here).

**Description:**
Used to catch user data, *from all forms*, before it gets sent to the mailchimp API. Useful when you want to manipulate data before being sent to the MailChimp API or if you'd like to use the entered data locally.

**Example:**
This example will catch the user submitted data, *of all forms*, store the users firstname in a variable and then update the current logged in user firstname profile field with the value in the First Name MailChimp field.

`
<?php
	/**
	* This example will catch the user submitted data, of all forms, store the users firstname in a variable and then update
	* the current logged in user firstname profile field with the value in the First Name MailChimp field. 
	*/
	function catch_user_data( $form_ID, $merge_variables ) {
	  // if the user is logged in
	  if ( is_user_logged_in() ) {

		// get the logged in user id
		$user_id = get_current_user_id();

		 // if the first name field is set
		 if ( isset( $merge_variables['FNAME'] ) ) { 

		 // update logged in users first name with the provided name in MC form
		 wp_update_user( array( 'ID' => $user_id, 'first_name' => $merge_variables['FNAME'] ) );

		 // can be used for any of the fields in the form + any fields in the user profile
		 }

	  }
	}
	add_filter( 'yikes_mc_get_form_data' , 'catch_user_data', 10, 2 );
?>
`
<br />

**Filter Name:**
`yikes_mc_get_form_data_$formID`

**Accepted Parameters:**  
`$Form_ID` and `$merge_variables`

**Parameter Info:**

`$Form_ID` = the ID of the specific MailChimp Form ( can be retrieved from the 'MailChimp Forms > Manage List Forms' menu ).

`$merge_variables` = a multi-dimensional array containing all user entered data being sent to the MailChimp API (The email, first name, last name etc. will be contained here).

**Description:**
Used to catch user data, of a specific form, before it gets sent to the mailchimp API. Useful when you want to manipulate data before being sent to the MailChimp API or if you'd like to use the entered data locally.

**Example:** 
This example will catch the user submitted data *from a specific form*, store the users firstname in a variable and then update the current logged in user firstname profile field with the value in the First Name MailChimp field.

`
<?php
	/**
	* This example will catch the user submitted data, store the users firstname in a variable and then update
	* the current logged in user firstname profile field with the value in the First Name MailChimp field. 
	* This catches data from ALL forms being submitted.
	*/
	function catch_user_data_from_specific_form( $form_ID, $merge_variables ) {
	  // if the user is logged in
	  if ( is_user_logged_in() ) {

		// get the logged in user id
		$user_id = get_current_user_id();

		 // if the first name field is set
		 if ( isset( $merge_variables['FNAME'] ) ) { 

		 // update logged in users first name with the provided name in MC form
		 wp_update_user( array( 'ID' => $user_id, 'first_name' => $merge_variables['FNAME'] ) );

		 // can be used for any of the fields in the form + any fields in the user profile
		 }

	  }
	}
	add_filter( 'yikes_mc_get_form_data_3d13f0f784' , 'catch_user_data_from_specific_form', 10, 2 );
?>
`

**Helper Functions**

** Description**
These functions are defined inside of the Easy MailChimp plugin and they exist to help test and view the user data that is being submitted by the user through the MailChimp form. These functions will prevent the default form functionality, so no user data will be sent to MailChimp while testing.

These functions should be used in conjunction with the `yikes_mc_get_form_data` or the `yikes_mc_get_form_data_$formID` filters. Whatever data the user has provided will be returned for viewing.

**Provided Functions**
`yks_mc_print_user_data( $form_ID, $merge_variables );` and `yks_mc_dump_user_data( $form_ID, $merge_variables );`


**How to Use**
*Print User Data*
`
<?php
	/**
	* This example will return all of the submitted 
	* user data In a nice readable format
	*/
	function print_user_data_from_form( $form_ID, $merge_variables ) {
		if( class_exists( 'yksemeBase' ) ) {
			$yikes_easy_mailchimp = new yksemeBase();
		}
		$yikes_easy_mailchimp->yks_mc_print_user_data( $form_ID, $merge_variables );
	}
	add_filter( 'yikes_mc_get_form_data' , 'print_user_data_from_form', 10, 2 );
?>
`
*Dump User Data*
`
<?php
	/**
	* This example will dump all of the submitted 
	* user data, so you can see the full array of data
	* being returned
	*/
	function dump_user_data_from_form( $form_ID, $merge_variables ) {
		if( class_exists( 'yksemeBase' ) ) {
			$yikes_easy_mailchimp = new yksemeBase();
		}
		$yikes_easy_mailchimp->yks_mc_dump_user_data( $form_ID, $merge_variables );
	}
	add_filter( 'yikes_mc_get_form_data' , 'dump_user_data_from_form', 10, 2 );
?>
`

== Screenshots ==

1. YIKES Easy MailChimp Settings Page
2. Yikes Easy MailChimp form listing page
3. View all subscribers of a given list, click to reveal individual subscriber info
4. Custom widget to easily add forms to sidebars and widgets
5. Form rendered on the front end of the site
6. Optional opt-in checkbox on the comment forms, easily add commenter's to your email list
7. Custom tinyMCE button allows you to easily add importer forms to pages and posts at the click of a button
8. About YIKES page

== Changelog ==

= 4.2 =
* Updated FAQ
* Re-worked the redirect for a better user experience
* Unified error messages into a single container on the front end
* Converted custom opt-in messages to utilize the WYSIWYG editors ( now allowing for html and images to be used in your success messages )
* Re-styled front end interest group containers

= 4.1 =
* Fixed JavaScript errors on when Address field is set to required
* Added user feedback on successful re-import of form
* Fixed some style issues
* Added animate.css
* Added class to required fields that were left empty
* Remove outdated jQuery
* Now error is appended to the form, instead of alerted through JavaScript 
* Fixed date picker field, and images associated to it
* Added ability to include html mark-up to confirmation fields

= 4.0 =
* Added Interest Group/Segment Support
* Ability To See Number of Subscriber Per List
* View Subscribers MailChimp Profile
* Customize Segment Group Label
* Customize Submit Button Text
* Redirect User to Specified Page On Submission
* Customize Success Message
* Added cURL Server Error Checking
* Added further error checking to pages
* Custom TinyMCE shortcode button

= 3.0 =
* Update Mail Chimp API to v2.0
* Added API Key Validation Check on settings page

= 2.2.1 =
* Bugfix make sure label matches ID

= 2.2.0 =
* Change plugin name
* Add better MailChimp error messaging
* Add Advanced Error Messaging option
* Add required fields indication
* Improve admin UI
* Update FAQ and screenshots
* Add list name to forms on Manage List Forms page
* Implement fetching list fields via MailChimp API key
* Fix incorrect use of register_uninstall_hook()

= 2.1.0 =
* Fix more jquery conflict issues
* Clarification on how to add and manage list forms

= 2.0.3 =
* Add labels to table option for form

= 2.0.2 =
* Fix class MCAPI conflict issue
* Automatically check for jquery if not use version 1.7.1
* fix jquery conflict issue (specifically when multiple instance of "jQuery(document).ready..."
* fix jquery conflict with ".cycle" jquery command commonly used in slideshows.
* add jquery libraries 1.7.1 and protype 
* use "noConflict" in each jquery instance to avoid future conflicts.  
* update Yikes about us info.

= 2.0.1 =
* Now supports multiples of the same list
* Fixed the date format issue
* Removed prompt class from the field wrapper
* Updated plugin description
* Disallowed adding the same list twice on the admin side
* About page now links to the YIKES, Inc. page

= 2.0.0 =
* Added import function to pull in existing custom fields
* Added new field handling to work with any list configuration
* Required fields in MailChimp are now reflected properly in the list view
* Added ability to choose Divs or Tables

= 1.3.1 =
* Added nopriv ajax action for anonymous users

= 1.3.0 =
* Added custom merge_vars field

= 1.2.0 =
* Removed required from First Name and Last Name fields
* Added update routines for future versions

= 1.1.0 =
* Changed the list logic and added a notice for the MERGE VAR naming schema

= 1.0.1 =
* Changed CSS paths from Absolute to Relative

= 1.0.0 =
* Initial Release

== Upgrade Notice ==
= 3.0 =
* Update Mail Chimp API to v2.0
* Added API Key Validation Check

= 2.2.0 =
* Added support for multiples of the same list
* Bug fixes

= 2.1.0 =
* Added support for multiples of the same list
* Bug fixes

= 2.0.0 =
* Supports custom merge vars now
* Allows import from MailChimp
* Allows Table or Div output

= 1.3.1 =
* Fixed form not submitting for anonymous users bug

= 1.3.0 =
* Custom merge vars allows more customized field configuration

= 1.2.0 =
* First Name and Last Name fields are no longer required