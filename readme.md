Easy MailChimp Forms
===========
Easy MailChimp Forms allows you to painlessly add MailChimp signup forms to your WordPress site. You can add forms to posts or pages with shortcodes or to template files with PHP tags. Simply copy and paste your MailChimp API Key into the plugin admin settings and it will pull in all your MailChimp lists. From there you can choose the lists you want to make forms for. For a single list you can check off the fields you want to include on your form and order them via an easy drag-and-drop interface. This plugin adds plenty of CSS selectors to the form code allowing you to completely customize the look of your forms.

Instructions on how to use the plugin can be [found on the FAQ](http://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/faq/ "found on the FAQ"). If you experience any problems, please submit a New Issue on our [Github Issue Tracker](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues "Github Issue Tracker") and we'll look in to it as soon as possible.

Installation
===========

1. Download the plugin .zip file
1. Log in to yourdomain.com/wp-admin
1. Click Plugins -> Add New -> Upload
1. Activate the plugin
1. You're finished!

Frequently Asked Questions
===========

### Do I need to have a MailChimp Account? 
Yes, you can register for one for free at [MailChimp](https://mailchimp.com/signup/ "MailChimp Signup").

### Do I need to have lists already set up in MailChimp? 
Yes, you have to have at least 1 list set up in MailChimp. 

For more help, visit the MailChimp Support article [How do I create a new list?](http://kb.mailchimp.com/article/where-can-i-find-my-api-key "How do I create a new list?")

### What do I do first? 
The first step is to add your MailChimp API key. This will allow your site to communicate with your MailChimp account. This is done on the plugin "MailChimp Settings" page.

### Where do I find my API Key? 
From your MailChimp Dashboard, click on your account name in the upper left hand corner of the screen under the MailChimp logo to expose the "Account Settings" menu. Click on "Account Settings" to go to the Account Settings screen and the click on the "Extras" menu. Under "Extras" choose "API keys." From there you can create a new key if you do not already have one.

For more help, visit the MailChimp Support article [Where can I find my API Key?](http://kb.mailchimp.com/article/where-can-i-find-my-api-key "Where can I find my API Key?")

### How do I add my MailChimp lists? 
After you add your API key, the plugin will fetch all of your MailChimp list information and load it into the plugin admin. Go to the plugin "Manage List Forms" page to choose the lists you want to make forms for.

### How do I add the MailChimp signup forms to my site? 
You can use a shortcode to add a form to a page or post. For each list on the plugin "Manage List Forms" page you will see a shortcode at the top. Copy the shortcode and paste it into any post or page where you want a form to appear. For example, if my form had the shortcode [yks-mailchimp-list id="1234567891"] I would copy and paste that into the page or post I wanted to add that form to.

Alternatively, you can use the PHP snippet to add the form to a template file.

### Don't I have to enter a list ID for each list I want to add to my site? 
Not anymore! With the 2.0 version of the MailChimp API all list information can be imported with just the API key.

### My Information isn't showing up when people subscribe, what gives? 
You're likely using an old version of the plugin. Please update to the latest version and import your list data.

### My list data was changed/the form isn't showing up since the 2.0 update, what's up with that?
Due to the code overhaul required for new version features, the structure we were using to save data had to be completely changed over to conform to the MailChimp schema. The old unique IDs the plugin assigned to lists have been deprecated. The custom fields are now pulled in from the MailChimp servers. If your form isn't showing up with the shortcode, just copy and paste it again to fix this issue.

Changes
===========
### 3.0.1
* Replaced missing Mail Chimp api wrapper class

### 3.0
* Update MailChimp API to v2.0
* Added API Validation Check on settings page

### 2.2.1
* Bugfix make sure label matches ID

### 2.2.0
* Change plugin name
* Add better MailChimp error messaging
* Add Advanced Error Messaging option
* Add required fields indication
* Improve admin UI
* Update FAQ and screenshots
* Add list name to forms on Manage List Forms page
* Implement fetching list fields via MailChimp API key
* Fix incorrect use of register_uninstall_hook()

### 2.1.0
* Fix more jquery conflict issues
* Clarification on how to add and manage list forms

### 2.0.3:
* Add labels to table option for form

### 2.0.2:
* Fix class MCAPI conflict issue
* Automatically check for jquery if not use version 1.7.1
* fix jquery conflict issue (specifically when multiple instance of "jQuery(document).ready..."
* fix jquery conflict with ".cycle" jquery command commonly used in slideshows.
* add jquery libraries 1.7.1 and prototype 
* use "noConflict" in each jquery instance to avoid future conflicts.  
* update Yikes about us info.

### 2.0.1:
* Now supports multiples of the same list
* Fixed the date format issue
* Removed prompt class from the field wrapper
* Updated plugin description
* Disallowed adding the same list twice on the admin side
* About page now links to the YIKES, Inc. page

### 2.0.0:
* Added import function to pull in existing custom fields
* Added new field handling to work with any list configuration
* Required fields in MailChimp are now reflected properly in the list view
* Added ability to choose Divs or Tables

### 1.3.1:
* Added nopriv ajax action for anonymous users*

### 1.3.0:
* Added custom merge_vars field*

### 1.2.0:
* Removed required from First Name and Last Name fields*
* Added update routines for future versions*

### 1.1.0:
* Changed the list logic and added a notice for the MERGE VAR naming schema*

### 1.0.1:
* Changed CSS paths from Absolute to Relative*

### 1.0.0:
* Initial Release