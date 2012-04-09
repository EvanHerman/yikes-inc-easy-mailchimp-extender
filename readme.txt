=== YIKES, Inc. Easy MailChimp Extender ===
Contributors: seriouslysean, yikesinc, hiwhatsup, liljimmi
Donate link: http://www.yikesinc.com
Tags: mailchimp, marketing, email, mailing lists, newsletter, signup
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 2.1.0

The YIKES, Inc. Easy MailChimp extender gives you the ability to create sign up forms that allow site visitors to join your MailChimp lists.

== Description ==

The YIKES, Inc. Easy MailChimp extender gives you the ability to create sign up forms that allow site visitors to join your MailChimp lists. You can add forms to posts or pages with shortcodes or to template files with PHP tags. Simply copy and paste your API Key and List IDs created by MailChimp into the plugin admin and the plugin will pull in all your MailChimp list fields. You can check off the fields you want to include on your form and order them via an easy drag-and-drop interface. In addition, this plugin adds plenty of CSS hooks to the form code allowing you to completely customize the look of your forms.

Targeted email messages are a more effective form of eMarketing for your business. The advantages of using MailChimp are vast and now you can expand upon its abilities in WordPress easily. With simple installation and easy to follow instructions, this extender will considerably improve your email marketing campaigns.

If you notice any issues, please submit a bug on our [Github Issue Tracker](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues "Github Issue Tracker") and we'll look in to it as soon as possible.

== Installation ==

1. Download the plugin .zip file
1. Log in to yourdomain.com/wp-admin
1. Click Plugins -> Add New -> Upload
1. Activate the plugin
1. You're finished!

== Frequently Asked Questions ==

= Do I need to have a MailChimp Account? =
Yes, you can register one for free at [MailChimp](https://mailchimp.com/signup/ "MailChimp Signup").

= Do I need to already have lists configured? =
Yes, you can configure them through mailchimp. All the plugin needs is your list id and api key.

= Where do I find my API Key? =
From your MailChimp Dashboard go to Account > API Keys and Authorized Apps. There you will need to add a key if you do not already have one.
For more help visit [The API Key Help Article](http://kb.mailchimp.com/article/where-can-i-find-my-api-key/ "API Key Help Article")

= Where do I find my List ID? =
From your MailChimp Dashboard go to Lists. Under the specific list you want to make a signup form for, click Settings > List Settings and Unique ID.
For more help visit [The List Id Help Article](http://kb.mailchimp.com/article/how-can-i-find-my-list-id/ "List Id Help Article")

= Hey, my checkboxes are not appearing in the active Fields options. What gives? =
Mailchimp only supports the following fields from the "FIELDS & MERGE tags"

* Text
* Number
* Radio Buttons
* Drop Down
* Date
* Birthday
* Address
* Zip Code
* Phone
* Website
* Image

Unfortunately, checkboxes is not currently an option. We will implement in our plugin as soon as MailChimp offers it as option from their API.


= My Information isn't showing up when people subscribe! =
You're likely using an old version of the plugin. Please update to the latest version and import your list data.

= My list data was changed/the form isn't showing up since the 2.0 update, what gives? =
Due to the code overhaul required for the features of the new version, the structure we were using to save data had to be completely changed over to conform to the MailChimp schema. The old unique ids that the plugin gave to lists have been deprecated. The custom fields that you have are now pulled in from the MailChimp servers. If your form isn't showing up with the shortcode, just copy and paste it again to fix this issue.

== Screenshots ==

1. Sidebar menu
2. Options page
3. List page
4. Sample list setup screen on MailChimp

== Changelog ==
= 2.1.0 =
* Fix more jquery conflict issues
* Clarification on how to add and manage list forms

= 2.0.3 =
* Add labels to table option for form

= 2.0.2 =
* Fix class MCAPI conflict issue
* Automtically check for jquery if not use version 1.7.1
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
