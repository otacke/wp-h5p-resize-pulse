=== SNORDIAN's H5P Resize Pulse ===
Contributors: otacke
Tags: h5p, tab, accordion, lightbox
Donate link: https://www.rainforestcoalition.org/donations/
Requires at least: 4.0
Tested up to: 5.5
Stable tag: 0.1.1
License: MIT
License URI: https://github.com/otacke/wp-h5p-resize-pulse/blob/master/LICENSE

Provides you with a potential workaround for H5P content that won't show in tabs, accordions, lightboxes, etc.

== Description ==
PLEASE NOTE: I gave some of my free time to create this plugin. Using it and its source code is absolutely free. I don't want any money from you. However, if you like this plugin, I kindly ask you to make a one-time donation of 2.50 EUR to the Rainforest Coalition (https://www.rainforestcoalition.org/donations/) -- or more if you can afford to.

H5P is a versatile plugin to add interactive content to your website. You may try to run it inside some fields that are created by other plugins, e.g. inside tabs, accordions, lightboxes, etc. And with some of them it seems that H5P doesn't work although it is, but the content is set to a height of 0 pixels. That problem is described at https://h5p.org/manipulating-h5p-with-javascript.

The best solution would be to modify the plugin that's including tabs, etc. to your WordPress site, but that's not always possible. In some cases, this plugin may be a feasible workaround at least. It will trigger H5P to resize in regular intervals which should let it be displayed in many cases. However, if they appear too frequent, they may cause the browser to stall. Choose the time interval wisely!

If necessary, you can use the _manage_h5presizepulse_options_ capability to control access to the plugin settings.

PLEASE NOTE: H5P IS A REGISTERED TRADEMARK OF JOUBEL. THIS PLUGIN WAS NEITHER CREATED BY JOUBEL NOR IS IT ENDORSED BY THEM.

== Installation ==
Install H5P Resize Pulse from the Wordpress Plugin directory or upload it manually and activate it. Done. You may want to customize the time interval though.

== Frequently Asked Questions ==
None so far.

== Changelog ==
= 0.1.1 =
Fixed readystatechange listener to avoid conflicts with other plugins

= 0.1 =
Initial release

== Upgrade Notice ==
= 0.1.1 =
Upgrade if other plugins that change H5P behavior run into trouble, in particular the H5P.MathDisplay library

= 0.1 =
Initial release
