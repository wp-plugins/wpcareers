=== WPCareers ===
name: Wordpress Careers plugin version 1.1.1-c
Contributors: Mohammad Forgani
Donate link: http://www.forgani.com/root/wordpress-careers-plugin/
Tags: job portal, careers, wpcareers, career, Career Portal, JobPortal
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.1


== Description ==

The plugin allows you to build an online jobs/resume website, where the applicants will be able to search, update, add/remove or edit their resumes/profiles.
This plugin is for standalone WordPress sites.
In addition, users can also add/delete/change the descriptions in addition to uploading images/photos.

* In Admin Area the administrator will be able to:
* View and manage records in terms of add/modify/remove of entries
* Approve or deny the posts.
* Convert the inactive Applicants to active
* Manage the jobs seekers as well as the employer profiles

It can be used completely as a Job Board System

== Examples ==

You can see the plugin in action here: http://wpcareers.com


IMPORTANT NOTE:
We strongly recommend you to use themes whose front page consisted only of one column, otherwise you should use our developed theme.
This plugin is for a standalone WordPress site.


== Installation ==

IMPORTANT NOTE:
We strongly recommend you to use themes whose front page consisted only of one column, otherwise you should use our developed theme.
This plugin is for a standalone WordPress site.

Please make a backup of files and database and test this plugin before using it in production.
Please test the plugin on your local machine before you install it in your production environment.

Once again, I take no responsibility...

The below section describes how to install the plugin and get it to work.

Please test the plugin with your theme on a test machine or a local machine,
if the test is successful then install it on the production machine.

1. Extract files
2. Upload 'wpcareers/' folder to your './wp-content/plugins/' directory
3. Login as administrator and go to "admin panel/plugins" and activate the plugin
4. Go to the "Manage" tab of your WP.

You have now there a new tab called "wpcareers" to configure the plugin.

You will need to make the following folders writeable (chmod 777) :

Add a public directory for uploading the resumes and images (the public folder must have write permission for all users)

wp-content/public/wpcareers/resume
wp-content/public/wpcareers/public

== Frequently Asked Questions ==

= Is this compatible with the WP plugin auto-upgrade feature? =

Yes, but to uninstall the plugin, please use the plugin Uninstaller utility

== Screenshots ==

1. Screenshot input area
2. Screenshot main


== Upgrade Notice ==

Manual Process

You will have to:

* Download the latest plugin version
* Uncompress the file
* Deactivate the plugin you currently have on your website (Admin Dashboard->Plugins->Deactivate)
* Upload/Replace the plugin in your wp-content->plugins
* Reactivate the plugin


This is a the initial version of plugin.


== Changelog ==

= version 1.1.1-c =
fixed for Wordpress 3.0


= version 1.1.1-a =
- fixed the bug in search by keywords,
- replaced the CAPTCHA module. The old captcha module does not worked well with the firefox

= bugfix version 1.1-a =

fixed for plugin auto-upgrade
Note: This bugfix release hove to install Manually.
- fixed for the plugin auto-upgrade. (must test with the next coming version)
- moved directories public resources to wp-content
- fixed some bugs in administrators interface (redirect, broken links, ...)
- the dashboard will show only for users with role >1

= version 1.0 =

* User friendly registration system
* Apply for jobs and submit resumes.
* Search jobs by keywords, field, and location.
* Create - Edit a resume
* Manage the Posts
* Email the job to a friend


== To Do ==

The WP plugin auto-upgrade