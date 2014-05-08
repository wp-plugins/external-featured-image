=== Nelio External Featured Image ===
Contributors: nelio
Tags: external, url, featured image
Requires at least: 3.3
Tested up to: 3.9
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use external images from anywhere as the featured image of your pages and
posts.

== Description ==

Are you using an external service for storing your images? Then you'd probably
like to use those images as featured images for your pages and posts. This
plugin lets you do this easily!


**Notice**

Every time an image is uploaded in the media library, WordPress automatically
creates alternative sizes of that image (thumbnail, medium, large and full).
Themes may then choose among these different versions when displaying the an
image in a post. Thumbnails do also exist for featured images. Moreover, themes
may registerd their own alternative image sizes. For example, WordPress'
default theme TwentyFourteen defines a thumbnail size called
"twentyfourteen-full-width" whose dimensions are 1038x576.

This plugin uses the alternative-size information (which your theme uses for
rendering a featured image) for scaling and cropping external featured images
via CSS on your users' browsers. Usually, the results look great. However, we
detected certain situations in which the featured image looks strange.
Specifically, if the "expected aspect ratio" (the one specified by the
alternative size) and the "actual aspect ratio" (the image's) are very
different, then the results might not look great. If that occurs, you may be
able to fix it modifying the CSS of your theme.

_Featured image by
[Cubmundo](https://www.flickr.com/photos/cubmundo/6748759375)_


== Changelog ==

= 1.0.1 =
* Improved image sizing. Now, the plugin uses the sizes the theme defines and
tries to scale and crop the external image for its proper display.


= 1.0.0 =
* First release.


== Upgrade Notice ==

= 1.0.1 =
External images are now scaled to theme's thumbnail sizes.

