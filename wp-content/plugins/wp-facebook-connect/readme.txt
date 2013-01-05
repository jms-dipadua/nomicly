=== Facebook Connect ===
Contributors: valentinas
Tags: facebook, facebook connect, fb connect, fbconnect, fb-connect, login.
Requires at least: 3.0
Tested up to: 3.11
Stable tag: 1.6

A beautifully crafted light weight Facebook Connect Plugin that uses the new Facebook API to create WordPress user accounts.

== Description ==

A beautifully crafted light weight Facebook Connect Plugin that uses the new Facebook API to create WordPress user accounts. 

When a user clicks the Facebook Connect button the Plugin checks to see if the user already has a WP profile in the website that corresponds with their Facebook email address. 

If so Facebook Connect logs the user in. If the user doesn't have an existing WordPress account then the Facebook Connect Plugin will create them new one and log the user in. 

Features include:
1) Shortcode - place the Shortcode anywhere on your site and the FB-connect button will appear.

2) Widget - place the widget in your sidebar and the FB-connect button will appear.


Inspired by these Plugins that use the old depreciated Facebook API:
http://wordpress.org/extend/plugins/wp-facebookconnect/
http://wordpress.org/extend/plugins/bp-fbconnect/

== Screenshots ==

1. Example of shortcodes added to post
2. Buttons rendered for users that are not logged in
3. Buttons rendered for users that are logged in

== Installation ==


1. Upload files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings->FB connect and set your facebook application API key and application secret
1. Add a widget, or place a shortcode and start connecting!

The shortcode is `[fb_login]`. You can also specify custom size (available options: small, medium, large, xlarge), login text, connect text and logout text. Example:
`[fb_login size='xlarge' login_text='Logout' logout_text='Logout' connect_text='Connect']`
You can place this anywhere in post or page. You can also place the shortcode in your template, however it's a bit different, example:
`<?php do_shortcode("[fb_login size='xlarge' login_text='Logout' logout_text='Logout']"); ?>`

== Changelog ==

= 1.6 =
* Minor improvements, mainly remove die() to prevent sites from crashing on users that have weird cookies (and maybe milk)
* Localization

= 1.4 =
* Fix scripts: load jquery before everything and use wp_enqueue_scripts for facebook JS

= 1.3 =
* Fix $user->nicename bug that caused strange buddypress behavior
* Fix "Login process failed bug" (when login process fails - echo that, but don't wp_die())
* Fix logout url bug
* Remove center style for widget and let theme handle that

= 1.2 =
* Add another hook after new user is created in case you want to set some additional meta
* Add Facebook avatar support
* Fix logout

= 1.1 =
* Split code to 5 files: fb-connect.php (main file), function.php, options.php, shortcode.php and widget.php. Makes it easier to maintain and read.
* Add login (or logout) button to all users. Also if you are logged in to WP, but not authorized with FB you will see "Connect" button which will link your WP account to your FB acccount
* Add few hooks for plugin developers (those of you who are good at WordPress will find hooks themselves, and for those who are not so good i will post instructions later)

= 1.0 =
* Big bang. Time and space starts here.