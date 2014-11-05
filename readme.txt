=== Easy MailChimp Forms ===
Contributors: yikesinc, hiwhatsup, liljimmi, eherman24, seriouslysean
Donate link: http://yikesinc.com
Tags: mailchimp, marketing, email, mailing lists, newsletter, sign up, forms, sign up form
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 5.1.1
License: GPLv2 or later

Easy MailChimp Forms allows you to painlessly add MailChimp sign up forms to your WordPress site and track user activity with interactive reports.

== Description ==

Easy MailChimp Forms allows you to painlessly add MailChimp sign up forms to your WordPress site. You can add forms to posts, pages or sidebars with shortcodes, widgets or template tags. Simply copy and paste your MailChimp API Key into the plugin admin settings and it will pull in all your MailChimp lists. From there you can choose the lists you want to make forms for. For a single list you can check off the fields you want to include on your form and order them via an easy drag-and-drop interface. This plugin adds plenty of CSS selectors to the form code allowing you to completely customize the look of your forms. Now with the ability to track sent campaigns, you can view and print interactive reports from based on previously sent campaigns.

**Note:** You will need a MailChimp API key to allow this plugin to communicate with your MailChimp account. For help on retrieving your API key, check out [question #4 of the FAQ](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/faq/). You can also read the MailChimp knowledge base article [Where can I find my API key and how can I use the API](http://kb.mailchimp.com/article/where-can-i-find-my-api-key/).

**Features**

* Create your own signup form templates ( *new* )
* Style your forms without writing a single line of code ( *new* )
* Complete Integration of MailChimp into WordPress
* Easily import MailChimp forms from an active MailChimp account
* View/Print Interactive Account Growth Reports and Campaign Reports (Statistics Tracking)
* reCaptcha spam prevention baked in
* Monitor MailChimp account activity
* Use MailChimp Interest Group/Segments
* Add MailChimp forms sidebars/widgetized areas with widgets
* Set forms to single or double opt-in
* Customize the success message
* Customize the submit button text
* Redirect users to a page on submission
* Un-subscribe users from MailChimp lists
* View subscriber MailChimp profiles
* View individual form subscriber count
* Display multiple forms on a single page
* Add commenter's to your MailChimp lists with a comment form opt-in check box 
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
8. (Optional) Sign up for a reCAPTCHA API Key Head over to [https://www.google.com/recaptcha/admin#whyrecaptcha](https://www.google.com/recaptcha/admin#whyrecaptcha) and login.
9. (Optional) Register for an API key. Enter your public <em>and</em> private keys into the reCAPTCHA settings page and save your settings.
9. Go to the 'Manage List Forms' page, start importing forms from MailChimp and adding them to posts, pages and widgets!
10. Start collecting user emails and growing your subscriber list!

== Frequently Asked Questions ==

= Do I need to have a MailChimp Account? =
Yes, you can register for one for free at [MailChimp](https://mailchimp.com/sign up/ "MailChimp Signup").

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

= How do I add the MailChimp sign up forms to my site? =
You can use a shortcode to add a form to a page or post, use the MailChimp form button in the visual editor or use a widget to add it to a sidebar. Each list on the plugin "Manage List Forms" displays a shortcode at the top. Copy the shortcode and paste it into any post or page where you want a form to appear. For example, if my form had the shortcode [yks-mailchimp-list id="1234567891"] I would copy and paste that into the page or post I wanted to add that form to.

= I input a valid MailChimp API key, but it returns invalid every time. I've already tried a new API key, but no dice. What's up? =

**Step 1:** Ensure that cURL is enabled at the server level. You will see an error at the top of the settings page if cURL is disabled. If you see no error, continue to step 2.

**Step 2:** If you have entered your MailChimp API key and are still getting the error "*Error: Please enter a valid Mail Chimp API Key*," please check the developer console inside your browser for further information. 

- Right-click in the API Key input field, and select 'Inpsect Element'
- Once the developer console is open, select the Console tab to view a more specific error returned from the MailChimp API.

For information on how to use your browser's developer console, read the WordPress Codex article, [Using Your Browser to Diagnose JavaScript Errors](http://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors).

**Possible Errors And Resolutions**

* Could not resolve host: xxxx.api.mailchimp.com  - the host you have provided is incorrect. The host is the string after the last dash (example: us2)
* Invalid Mailchimp API Key: xxxxxxxxxxxxxxxxxx-xxx - Your API key is invalid. You can confirm a valid key by logging into [MailChimp](http://mailchimp.com) and checking the active API key registered to your account.

= I don't want the form to be the 100% width. How can I adjust the width myself? =
You can adjust the width of the forms on your site by changing the width of the element with the class .yks-mailchimpFormContainer. This is the parent container for the form. Adjusting this width will control the width of the input fields inside of it.

= How can I translate this plugin? =
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

= Do you offer integration with reCAPTCHA? I keep getting spam entries from bots and spammers. =
Yes! Since version 4.4 of Easy MailChimp Extender, we have added [reCAPTCHA](https://www.google.com/recaptcha/intro/index.html) support. You can sign up for a reCAPTCHA API key [here](https://www.google.com/recaptcha/admin#whyrecaptcha). You will need to provide your <em>public</em> key as well as the <em>private</em> key. Don't worry, its free :)

= When I input my API key and hit save, it immediately reverts to a much shorter string of text and returns invalid. What's going on? =
You most likely have a password manager installed and active (LastPass, OnePass, TeamPassword etc.). The API key input field is set to type="password" for security reasons. Most password managers auto-fill password fields based on the URL of the current page. So, what happens when you click save is that the password manager auto fills in the field with the password that you have saved for your site, and thus returning 'invalid API key'. To resolve the issue you'll need to temporarily disable any active password manager and re-enter and save your API key. Once the key has been successfully saved, you can re-enable your password manager.

= How do I create my own custom MailChimp template? =
**Step 1:** 
First you'll want to import the boilerplate template files bundled with the plugin. These are files that were created for easy customization. You can import the boilerplate files by going to the manage list forms page and clicking 'import boilerplate templates'. 

<em>Note :</em> Alternatively, you can manually import the template files by copying the 'yikes-mailchimp-user-templates' directory inside of the plugin root ( 'yikes-inc-easy-mailchimp-extender/templates/' ) over to your theme root. ( ie: /wp-content/themes/twentyfourteen/ ).

**Step 2:** 
Once imported you'll find the boilerplate files in your theme root. Each boilerplate template file comes with an associated .css file located inside of the styles directory. To get started on your first template, we recommend that you duplicate one of the existing boilerplate tempalate files and its associated .css file, and changing the name.

**Step 3:** 
Finally, change the MailChimp template, author and description headers at the top of each template file. The 'MailChimp Template' header dictates the name of your template, and what will appear in the drop down field. You'll also want to make sure that the correct .css file is properly being enqueued at the top of the template file.

**Step 4:**
Once setup, you can assign the template to your form from the 'Manage List' page. Make sure to select the checkbox 'use custom form' and then in the drop down select your newly created template.

= I'm trying to set up the opt-in commenting checkbox. When users opt-in and leave a comment, an error is thrown and I can't figure out why. What could be wrong? =
For the optin commenter checkbox, you'll want to make sure that the form you have set to assign users to only has the email field set to required.

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
`yikes_mc_user_already_subscribed`

**Accepted Parameters:** 
`$errorMessage` , `$update_user_link` and `$email`

**Parameter Info:**	

`$errorMessage` = the error message returned by the MailChimp API. By Default : "email@domain.com is already subscribed to the list."

`$update_user_link` = the link that will re-submit the form to update the current users info. Default link text : "Update My Info"

`$email` = the email address provided in the Email field

**Description:**
Used to display a custom error message back to the user, when a user attempts to subscribe to a list they already exist on.

**Example:**
The following example will alter the error returned from the MailChimp API. Useful when you would like to provide a more custom error message. The 'Update' link will re-submit the form using the 'update_existing' parameter to update the current users profile.

`
<?php
	/**
	* This example will return a custom error message to the user when they attempt to subscribe to 
	* a list they already exist on. The following will show something similar "Sorry, John@johnsmith.com is already subscribed to this list. Please Update My Information."
	*/
	function custom_user_already_subscribed_error( $errorMessage , $update_user_link , $email ) {
		$errorMessage = 'Sorry, ' . $email . ' is already subscribed to this list.';
		$errorMessage .= str_replace( 'Update My Info.' , 'Please Update My Information' , $update_user_link );
		return $errorMessage;
	}
	add_filter( 'yikes_mc_user_already_subscribed' , 'custom_user_already_subscribed_error' , 10 , 3 );
?>
`
<br />

**Filter Name:**
`yikes_mc_subscriber_update_message`

**Accepted Parameters:** 
`$success_message`

**Parameter Info:**	

`$success_message` = the success message to be displayed to the user, defaults to "Thanks, your information has been successfuly updated."

**Description:**
Used to alter the success message returned when a user updates their account information for a given list

**Example:**
The following example will alter success message displayed back to the user after they have updated their account information for a given list. Defualts to : "Thanks, your information has been successfuly updated."

`
<?php
	/**
	* The following example will alter the success message when a user re-submits 
	* the form data to update their information for a given form
	*/
	function custom_resubmit_info_message( $success_message ) {
		$success_message = "Thanks for updating your info! You're so awesome!";
		return $success_message;
	}
	add_filter( 'yikes_mc_subscriber_update_message' , 'custom_resubmit_info_message' , 10 );
?>
`
<br />

**Filter Name:**
`yks_redirect_add_post_types`

**Accepted Parameters:** 
`$post_array `

**Parameter Info:**	

`$post_array` = an array of post type names that you would like to be included into the drop down as a redirect option on the manage list page

**Description:**
Use this filter to add any number of custom post types to the redirect dropdown

**Example:**
The following example will add any and all posts with the custom post type 'portfolio' to the redirect dropdown 

`
<?php
	/**
	* This example will add the custom post type 'portfolio' to the
    * redirect dropdown on the manage list forms page	
	*/
	function add_portfolio_post_type_to_yikes_redirect_dropdown( $post_array ) {
		$post_array[] = 'portfolio';
		return $post_array;
	}
	add_filter( 'yks_redirect_add_post_types' , 'add_portfolio_post_type_to_yikes_redirect_dropdown' );
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

1. YIKES Easy MailChimp General Form Settings Page
2. YIKES Easy MailChimp reCAPTCHA settings page
3. YIKES Easy MailChimp Account Activity Overview
4. YIKES Easy MailChimp Campaign Overview
5. YIKES Easy MailChimp Campaign Statistics Reports
6. *New* Yikes Easy MailChimp Redesigned Form Listing Page
7. View all subscribers of a given list, click to reveal individual subscriber info
8. Custom widget to easily add forms to sidebars and widgets
9. Form rendered on the front end of the site
10. Optional opt-in checkbox on the comment forms, easily add commenter's to your email list
11. Custom tinyMCE button allows you to easily add imported forms to pages and posts at the click of a button
12. About YIKES page
13. Admin Dashboard Widget - Account Activity ( With Live Updating )
14. *New* Form customizer, with simple UI including color pickers

== Changelog ==

= 5.1.1 - November 5, 2014 =

* Fix: repair 'Opt In' default list drop down on options page
* Fix: repair errors being thrown when a commenter is added to a list

= 5.1.0.1 - November 4, 2014 =

**Updates**

* Remove update profile info link, replaced with user already subscribed error

**Fixes**

* Fix missing argument in getFrontendFormDisplay_field when using the table view

= 5.1 - November 3, 2014 =

**Updates**

* Release v5.1
* Added brand new templating framework, to allow users to create custom form templates for use with any MailChimp List
* Re-style Manage List page
* Added missing label to radio buttons inside the form
* Added missing closing parentheses on subscriber count inside view subscribers page
* Added custom color picker to easily stylize forms without writing a single line of code
* Added ability to specify a custom class names for each field of the form ( easily add custom class names to MailChimp form fields for further customization )
* Only run API Key check when a new key is entered ( not on each page load )
* Added new Welcome page with features+update notifications for users
* Security vulnerability re-patched - props @g0blinResearch for privately disclosing the potential vulnerability
* Added `yks_redirect_add_post_types` filter, to allow custom post types to be added to the redirect drop down (view [docs](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/) for example)
* Added `yikes_mc_user_already_subscribed` filter to allow users to alter the message displayed to the user when they are already subscribed to a given list (view [docs](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/) for example)
* Added `yikes_mc_subscriber_update_message` filter, to alter the success message when a user updates their account information for a given list (view [docs](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/) for example)
* Added a new link to allow users to update profile information if previously subscribed to a given list
* Added 5 bundled templates, and 2 boilerplate files for users to use as a starting point for new templates

= 5.0.9 - October 3rd, 2014 =

**Fixes**

* Added autocomplete="false" to API input field
* Added event listener to form submission (forms can now be placed in modals/slideout menus)

= 5.0.8 - September 23, 2014 =

**Fixes**

* Band-aid fix for the -1 response returned from ajax.php

= 5.0.7 - September 19, 2014 =

**Fixes**

* Security Vulnerability patched - props @g0blinResearch for privately disclosing the issue

= 5.0.6 - August 22, 2014 =

**Fixes**
* fix bug with nonce security check when using table 'Preferred Form Layout' setting

= 5.0.5 - August 21, 2014 =

**Fixes**
- added `stripslashes()` to the following settings fields :
	- Single Opt-in Confirmation Message
	- Double Opt-in Confirmation Message
	- Interest Group Label
	- Opt-in Checkbox Text

**Updates**
- Updated readme FAQ section

= 5.0.4 - August 6, 2014 =
* Update SSL conditional in main plugin file
* Added mcrypt module check, for API encryption
* Conditionally loaded scripts and styles in admin ( prevent conflicts )
* Included NONCE security checks on form submissions ( helps prevent CSRF and other attacks )
* Added proper update task function to add missing settings to the options page ( prevents undefined variables for new settings )
* Added missing styles to post page ( added missing icon to tinyMCE button )
* Added missing styles to widget page
* Adjusted Chimp Chatter activity time formatting to return correct time based on user time zone
* New Feature : Real time updates to Account Activity ( utilizing the heartbeat API )
* New Feature : Added account activity widget to dashboard ( with live updates using heartbeat API )
* Added screenshot of admin dashboard account activity widget

= 5.0.3 =
* Re-Apply missing styles to new 'My MailChimp' Section

= 5.0.2 =
* Remove extra spacing in provided short code, which caused parsing issues

= 5.0.1 =
* Remove global box-sizing CSS declaration

= 5.0 =
* Custom content filter to prevent other plugins from accidentally hooking in
* Updated translation files with new text and translations
* Added new screen shots
* Updated plugin activation hooks
* base64_encoded MailChimp API Key for privacy on multi-admin sites
* Added an entirely new "My MailChimp" section
* "My MailChimp" section now includes account overview details
* Added much needed campaign statistic tracking page
* Added ability to track Campaign Click/Opens with geo-data and interactive charts including world map
* Added account /listgrowth section with interactive bar chart
* Added "Chimp Chatter" feed to track account activity
* Ability to export interactive charts to png, jpg, svg
* Printable Campaign Reports
* View campaign
* Re-Branded entire Plugin


= 4.4 =
* Added reCAPTCHA support to prevent bots and spammers from submitting forms
* Split settings page into three seperate sections
* Removed Jetpack sharing icons from appearing in the success messages (removed sharedaddy filter)
* Updated translation files with new strings
* Update existing screen shots and add new screen shot
* Update FAQ
* Added "loaded server modules" to the debug page, to better aid in support requests

= 4.3 =
* Updated radio button fields
* Re-worked related JS and PHP functions

= 4.2.2 =
* Added open_basedir conditional check to toggle CURLOPT_FOLLOWLOCATION based on users server settings
* Prevents warning being thrown for users on shared hosts with an open_basedir set

= 4.2.1 - May 21, 2014 =
* Removed all unnecessary Google tracking codes and functions

= 4.2 - May 21, 2014 =
* Updated FAQ
* Re-worked the redirect for a better user experience
* Unified error messages into a single container on the front end
* Converted custom opt-in messages to utilize the WYSIWYG editors ( now allowing for html and images to be used in your success messages )
* Re-styled front end interest group containers

= 4.1 - May 20, 2014 =
* Fixed JavaScript errors on when Address field is set to required
* Added user feedback on successful re-import of form
* Fixed some style issues
* Added animate.css
* Added class to required fields that were left empty
* Remove outdated jQuery
* Now error is appended to the form, instead of alerted through JavaScript 
* Fixed date picker field, and images associated to it
* Added ability to include html mark-up to confirmation fields

= 4.0 - May 14, 2014 =
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

= 3.0 - February 22, 2014 =
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

= 5.1.1 - November 5, 2014 =
Fix: repair 'Opt In' default list drop down on options page
Fix: repair errors being thrown when a commenter is added to a list

= 5.1.0.1 - November 4, 2014 =

Removed the update profile info link, some users brought up security concerns.
Fixed missing argument when using table display formatting.

= 5.1 - November 3, 2014 =

We have added brand new functionality, allowing users to easily customize forms on the fly, through a familiar UI as well as a templating framework to allow users to design and implement their own custom forms. A minor security bug was patched in this release as well. We fixed an issue with the API key check running on each page load, and added a few new filters to allow users to alter text (please view the [docs](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/other_notes/) for example)

= 5.0.9 - October 3rd, 2014 =

**Fixes**

* Added autocomplete="false" to API input field
* Added event listener to form submission (forms can now be placed in modals/slideout menus)

= 5.0.8 - September 23, 2014 =

**Fixes**

* Band-aid fix for the -1 response returned from ajax.php

= 5.0.7 - September 19, 2014 =

**Fixes**

* Security Vulnerability patched - props @g0blinResearch for privately disclosing the issue

= 5.0.6 - August 22, 2014 =

**Fixes**

* fix bug with nonce security check when using table 'Preferred Form Layout' setting

= 5.0.5 - August 21, 2014 =

**Fixes**

- added `stripslashes()` to the following settings fields :
	- Single Opt-in Confirmation Message
	- Double Opt-in Confirmation Message
	- Interest Group Label
	- Opt-in Checkbox Text

**Updates**

- Updated readme FAQ section

= 5.0.4 - August 6, 2014 =
* Update SSL conditional in main plugin file
* Added mcrypt module check, for API encryption
* Conditionally loaded scripts and styles in admin ( prevent conflicts )
* Included NONCE security checks on form submissions ( helps prevent CSRF and other attacks )
* Added proper update task function to add missing settings to the options page ( prevents undefined variables for new settings )
* Added missing styles to post page ( added missing icon to tinyMCE button )
* Added missing styles to widget page
* Adjusted Chimp Chatter activity time formatting to return correct time based on user time zone
* New Feature : Real time updates to Account Activity ( utilizing the heartbeat API )
* New Feature : Added account activity widget to dashboard ( with live updates using heartbeat API )
* Added screenshot of admin dashboard account activity widget

= 4.5 =
* Added reCAPTCHA support to prevent bots and spammers from submitting forms
* Split settings page into three seperate sections
* Removed Jetpack sharing icons from appearing in the success messages (removed sharedaddy filter)
* Updated translation files with new strings
* Update existing screen shots and add new screen shot
* Update FAQ
* Added "loaded server modules" to the debug page, to better aid in support requests

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