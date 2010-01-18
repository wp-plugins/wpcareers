=== WPCareers ===
name: Wordpress Careers plugin version 1.1-a
Contributors: Mohammad Forgani
Donate link: http://www.forgani.com/root/wordpress-careers-plugin/
Tags: job portal, careers, wpcareers, career, Career Portal, JobPortal
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.0


== Description ==

The plugin allows you to build an online jobs/resume website, where the applicants will be able to search, update, add/remove, and add or edit their resumes/profiles. 
This plugin is for standalone WordPress sites.
In addition, user can also add/delete/change descriptions, upload images/photos.

* In Admin Area the administrator will be able to:
* View and manage records in terms of add/modify/remove of entries 
* Approve or deny the posts.
* Inactive Applicants Convert to Active
* Delete Users profile, Delete Employer profiles

It is a complete ready to use as Job Board System

== Examples ==

You can see the plugin in action here: http://wpcareers.com


IMPORTANT NOTE:
We strongly recommend  you using themes that the front page will be displayed in one column otherwise you use our theme wpcareers.
This plugin is for a standalone WordPress site.


== Installation ==

IMPORTANT NOTE:
We strongly recommend you using themes that the front page will be displayed in one column otherwise you use our theme wpcareers.
This plugin is for a standalone WordPress site.

Please made a backup of files and database and test once before using in production.
Using a local environment and test the plugin before install it on your production environment.
Once again, I take no responsibility...

This section describes how to install the plugin and get it working.
Please test the plugin with your theme on a develop machine or a local machine, 
if the test is successful then install it on the production machine.

e.g.

1. Extract files
2. Upload 'wpcareers/' folder to your './wp-content/plugins/' directory
3. Login to the administration and go to "admin panel/plugins" and activate the plugin
4. Go to the "Manage" tab of your WP. 
You have now there a new tab called "wpcareers" to configure the plugin.

You will need to make the following folders writeable (chmod 777) :

= NEW in THIS RELEASE =

Add a public directory and upload the resume and images resources (the public folder must have write permission)

wp-content/public/wpcareers/resume
wp-content/public/wpcareers/public



== Frequently Asked Questions ==

= Is this compatible with the WP plugin auto-upgrade feature? =


For Uninstalling the plugin please run the plugin Uninstaller utility


== Screenshots ==

1. Screenshot input Area 
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

= bugfix version 1.1-a =

fixed for plugin auto-upgrade 
Note: This bugfix release hove to install Manually.
- fixed for the plugin auto-upgrade. (must test with the next coming version)
- moved directories public resources to wp-content
- fixed some bugs in administrators interface (redirect, brocken links, ...)
- the dashboard will show only for users with rolle >1

= version 1.0 =

* User friendly registration system
* Apply for jobs and submit resumes. 
* Search jobs by keywords, field, and location.
* Create - Edit a resume
* Manage the Posts
* Email the job to a friend


== To Do ==

The WP plugin auto-upgrade
