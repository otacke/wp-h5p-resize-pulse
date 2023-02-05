# SNORDIAN's H5P Resize Pulse
WordPress plugin that provides you with a potential workaround for H5P content that won't show in tabs, accordions, lightboxes, etc.

## Please note!
I gave some of my free time to create this plugin. Using it and its source code is absolutely free. I don't want any money from you. However, if you like this plugin, I kindly ask you to make a one-time donation of 2.50 EUR to the [Rainforest Coalition](https://www.rainforestcoalition.org/donate) -- or more if you can afford to.

## Description
H5P is a versatile plugin to add interactive content to your website. You may try to run it inside some fields that are created by other plugins, e.g. inside tabs, accordions, lightboxes, etc. And with some of them it seems that H5P doesn't work although it is, but the content is set to a height of 0 pixels. That problem is described at https://h5p.org/manipulating-h5p-with-javascript.

The best solution would be to modify the plugin that's including tabs, etc. to your WordPress site, but that's not always possible. In some cases, this plugin may be a feasible workaround at least.

In "interval" mode, it will trigger H5P to resize in regular intervals which should let it be displayed in many cases. However, if they appear too frequent, they may cause the browser to stall. Choose the time interval wisely! Also, some H5P content types may
break that way. The only way around this is then the "selector" mode.

In "selector" mode, you need to determine a CSS selector that will identify all the elements that you interact with to display
other contents, e.g. the tab buttons. If that selector is set in the plugin's settings, it will trigger a resize pulse
when that element is clicked. This way, you prevent stalling the browser or breaking H5P content types. Depending on how the
other plugin was created, there may not ba a suitable CSS selector then.

If necessary, you can use the _manage_h5presizepulse_options_ capability to control access to the plugin settings.

*PLEASE NOTE: H5P IS A REGISTERED TRADEMARK OF H5P GROUP. THIS PLUGIN WAS NEITHER CREATED BY H5P GROUP NOR IS IT ENDORSED BY THEM.*

## Install/Usage
Install H5P Resize Pulse from the Wordpress Plugin directory or upload it manually and activate it. Done. You may want to customize the time interval though or ideally set a CSS selector that targets the element that you interact with to display other contents.

## License
H5P Resize Pulse is is licensed under the [MIT License](https://github.com/otacke/wp-h5p-resize-pulse/blob/master/LICENSE).
