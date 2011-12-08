=== YIKES, Inc. Easy MailChimp Extender ===
Contributors: seriouslysean
Donate link: http://www.yikesinc.com
Tags: mailchimp, marketing, email, mailing lists, newsletter, signup
Requires at least: 3.2
Tested up to: 3.3
Stable tag: 2.0.0

The YIKES, Inc. MailChimp extender allows you to easily add a MailChimp signup form to a page or post using a shortcode or template tag.

== Description ==

The MailChimp extender allows you to easily control your eMarketing with the addition of a shortcode in a post or page or a PHP tag in your template file. Simply create your lists in MailChimp and copy and paste the list ID created by MailChimp into the plugin admin.

In addition, this plugin allows you to customize your signup forms with CSS. The plugin will pull in your mailchimp fields and allow you to order them via an easy drag-and-drop interface.

Targeted email messages are a more effective form of eMarketing for your business. The advantages of using MailChimp are vast and now you can expand upon its abilities in Wordpress easily. With simple installation and easy to follow instructions, this extender will considerably improve your email marketing campaigns.

If you notice any issues, please submit a bug on our [Github Issue Tracker](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues "Github Issue Tracker") and I'll look in to it as soon as possible.

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
Yes, you can configure them through mailchimp. All the plugin needs is your list id.

= Where do I find my API Key? =
From your MailChimp Dashboard go to Account > API Keys and Authorized Apps. There you will need to add a key if you do not already have one.
For more help visit [The API Key Help Article](http://kb.mailchimp.com/article/where-can-i-find-my-api-key/ "API Key Help Article")

= Where do I find my List ID? =
From your MailChimp Dashboard go to Lists. Under the specific list you want to make a signup form for, click settings > list settings and unique ID.
For more help visit [The List Id Help Article](http://kb.mailchimp.com/article/how-can-i-find-my-list-id/ "List Id Help Article")

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
