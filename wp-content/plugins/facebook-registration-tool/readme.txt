=== Plugin Name ===
Contributors: mikemarr
Tags: facebook, registration, facebook registration tool
Requires at least: 3.0
Tested up to: 3.0.4
Stable tag: 0.3

Integrates Facebook Registration Tool into Wordpress registration/user system.

== Description ==

This plugin integrates the Facebook Registration Tool (http://developers.facebook.com/docs/user_registration) into Wordpress. For more information and support of this plugin, see: http://beyondwp.com/2010/12/21/facebook-registration-plugin-for-wordpress/

From beyondwp.com:

The Facebook Registration Tool, as announced by Facebook Developer Paul Tarjan (http://developers.facebook.com/blog/post/440) provides a registration mechanism that is both Facebook user and Facebook hold-out friendly. Facebook Login (the tool formerly known as Facebook Connect) is a great solution to integrate Facebook into your site, but is not friendly to those users who don’t have or utilize their own Facebook accounts. By utilizing this registration tool, Facebook can automatically complete the registration form for Facebook users while supplying a good-ole fashioned form for those not wanting or able to utilize Facebook to register.

== Installation ==

After installing plugin from Wordpress Repository (or extracting to /plugins directory), go to Settings > Facebook Registration and enter your Facebook App ID and App Secret. That's it!

== Screenshots ==

1. Facebook Registration Tool in WordPress
2. Configuration Screen for plugin

== Frequently Asked Questions ==

= What is this Facebook App ID / Secret? =

These are codes given to your Facebook Application, and are required to run
this plugin. See: http://beyondwp.com/2010/12/23/whats-the-big-facebook-app-secret/

= Does this work with multisite? =

Yes! As of version 0.3, this plugin works with multisite!

== Changelog ==

= 0.3 =
* Added multisite (network) support

= 0.2 =
* Various error checking and control added for increased user experience
* &lt;fb:registration> now replaces &lt;form> instead of inside it

== Upgrade Notice ==

= 0.3 =
Multisite/network now supported.

= 0.2 =
Adds various UI/UX elements, including confirming Facebook App ID and Secret
are correct.
