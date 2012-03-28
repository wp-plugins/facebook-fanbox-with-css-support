=== Facebook Fanbox (with CSS Support) ===
Contributors: ppfeufer
Donate link:
Tags: Facebook, Fanbox, Likebox, CSS
Requires at least: 3.1
Tested up to: 3.4-alpha
Stable tag: 1.3.1

Add a sidebarwidget with a fully css-customisable facebook fanbox to your WordPress-Blog.

== Description ==

Adds a likebox for your facebook-fanpage to your wordpress-sidebar. I know, there are many plugins to do this, but here you can use your own CSS to style the box for your needs. Its fully customisable via css. Just add the ID of your facebook-fanpage and you are ready to start.

**Features**

* Style it with your own CSS and make fit with your own blogdesign.
* Show the stream from your fanpage.

**Available Langauges**

* English
* German

**Notes**

Thanks in advance to <a href="http://kkoepke.de/">Kai</a> for giving the idea for this plugin and also for testing it in his own <a href="http://kkoepke.de/blog/">blog</a> during the time of development.

**Important**

If you update to version 1.1.0 make sure to control your settings and save them. **This is needed to generate the new css-file.**

== Installation ==

1. Unzip the ZIP plugin file
2. Copy the `facebook-fanbox-with-css-support` folder into your `wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

**Where do I find the ID of my fanpage?**

Just go to your facebook-fanpage and take a look at the link "edit page". The link looks like `facebook.com/pages/edit/?id=xxxxxxxxxxxxxxx`. The number with the 15 digits after `?id`= is what you need.

== Screenshots ==

1. Widgetsettings
2. Widget

== Changelog ==

= 1.3.1 =
* (28.03.2012)
* removed a line of debug-code

= 1.3 =
* (28.03.2012)
* Ready for WordPress 3.4

= 1.2.1 =
* (09.11.2011)
* Ready for WordPress 3.3

= 1.2.0 =
* (16.03.2011)
* Fix: Check if all FB-Scripts are loaded correctly.

= 1.1.5 =
* (16.03.2011)
* Fix: Typo in function call.

= 1.1.4 =
* (09.03.2011)
* Fix: Corrected javascript-call. Type was missing.

= 1.1.3 =
* (09.02.2011)
* Fix: hide titlebar if its empty.

= 1.1.2 =
* (26.01.2011)
* Fix: corrected a name of an internal function call. It was not affecting any service but it was just a confusing name :-)

= 1.1.1 =
* (18.01.2011)
* Fix: fixed errormessage on firtst activation (thanks to <a href="http://bloggonaut.net/">Jonas</a> for reporting).

= 1.1.0 =
* (17.01.2011)
* Fix: Moved CSS to upload-dir, so its not effected on upadtes. **Please make sure to control and save your settings after this update**
* Update: German translation

= 1.0.1 =
* (12.01.2011)
* Fix: Setting the current locale (<em>must be defined in wp-config.php</em>)

= 1.0.0 =
* Initial Release
* Test: Ready for WordPress 3.1

== Upgrade Notice ==

If you update to version 1.1.0 make sure to control your settings and save them. **This is needed to generate the new css-file.**