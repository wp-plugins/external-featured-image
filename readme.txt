=== Nelio External Featured Image ===
Contributors: nelio
Donate link: http://neliosoftware.com
Tags: external, url, featured image
Requires at least: 3.3
Tested up to: 3.9
Stable tag: 1.0.4
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
via CSS on your users' browsers.

_Featured image by
[Cubmundo](https://www.flickr.com/photos/cubmundo/6748759375)_


== Screenshots ==

1. **External Featured Image with URL.** Easily set the featured image of a
post by using the image's URL only!


== Changelog ==

= 1.0.4 =
* Bug fix. You can now set regular featured images under all circumstances
(thanks _rprose_ for reporting the bug!).


= 1.0.3 =
* Improved image sizing. Now, the plugin uses the sizes the theme defines and
tries to scale and crop the external image for its proper display.


= 1.0.0 =
* First release.


== Upgrade Notice ==

= 1.0.4 =
Bug fix. Setting regular featured image works again.

