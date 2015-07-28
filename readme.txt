=== Nelio External Featured Image ===
Contributors: nelio, davilera
Donate link: http://neliosoftware.com
Tags: external, url, featured image, featured, featured images, image
Requires at least: 3.3
Tested up to: 4.1
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use external images from anywhere as the featured image of your pages and
posts.

== Description ==

Are you using an external service for storing your images? Then you'd probably
like to use those images as featured images for your pages and posts. This
plugin lets you do this easily!

**UPDATE!** NelioEFI is now compatible with virtually all themes! Read the FAQ
section for more information on how it works.


= Featured On =

* [10 Picture Perfect WordPress Thumbnail
Plugins](http://premium.wpmudev.org/blog/wordpress-thumbnail-plugins/)
(wpmudev.org)
* [How to Use an Image URL to set the Featured
Image](http://neliosoftware.com/use-image-url-set-featured-image/) (Nelio's
blog)


= Related Plugins by Nelio =

* [Nelio A/B Testing](http://nelioabtesting.com/?fp=wordpress.org) |
[Download](https://wordpress.org/plugins/nelio-ab-testing/)
* Nelio Featured Posts |
[Download](https://wordpress.org/plugins/nelio-featured-posts/)
* Nelio Related Posts |
[Download](https://wordpress.org/plugins/nelio-related-posts/)


_Featured image by
[Cubmundo](https://www.flickr.com/photos/cubmundo/6748759375)_



== Frequently Asked Questions ==

= How does the plugin work? =

Every time an image is uploaded in the media library, WordPress automatically
creates alternative versions of that image, each with a different size
(thumbnail, medium, large and full).  Themes may then choose among these
different versions when displaying the an image in a post.

Thumbnails do also exist for featured images, and themes may registerd their
own alternative image sizes. For example, WordPress' default theme
TwentyFourteen defines a thumbnail size called "twentyfourteen-full-width"
whose dimensions are 1038x576.

**Update with version 1.3.** With this version, NelioEFI now uses a transparent
Placeholder. The Placeholder is automatically set as the featured images of any
posts that use an external featured image.  Then, using JavaScript, NelioEFI
detects which images are using the Placeholder and adds a CSS property for
displaying the actual featured image (as a background property of the image
placeholder).


== Screenshots ==

1. **External Featured Image with URL.** Easily set the featured image of a
post by using the image's URL only!


== Changelog ==

= 1.3.0 =
* NelioEFI is now **compatible with virtually all themes**. In order to do
that, we use a transparent Placeholder that is set as the featured image of all
the posts that use an external featured image. Then, using JavaScript, the
actual featured image is set as the background of the transparent placeholder.
* Because of the new approach, we can no longer define custom ALT text. Future
version might fix this issue. In the meantime, though, we remove the support
for this field.


= 1.2.0 =
* Bug fix: Quick Edit (and possibly other edits) a post removed the external
featured image. This does no longer happen.
* External Featured Image is inserted using the `src` attribute of an `img` tag
in RSS feeds (instead of an inline CSS `background` property).


= 1.1.0 =
* Define the ALT attribute of your external featured images.


= 1.0.9 =
* Added a new filter ("nelioefi_post_meta_key") that will let you define a
custom post_meta_key to store the URL of the external featured image.


= 1.0.8 =
* Referencing our A/B testing service for WordPress.


= 1.0.7 =
* Added External Featured Image box for custom post types. If the custom
post type's template uses featured image.
* Some helper functions have been introduced in the plugin, so that adapting
themes becomes easier.


= 1.0.6 =
* Compatibility with the Genesis Framework. By default, external featured
images have a minimum size of 150x150px. Make sure you edit your CSS files
for proper image size.
* Some minor tweaks.


= 1.0.5 =
* Bug fix. One function for locating external featured images was missplaced.
I moved it to the proper file so that it loads when the user is not an admin.


= 1.0.4 =
* Bug fix. You can now set regular featured images under all circumstances
(thanks _rprose_ for reporting the bug!).


= 1.0.3 =
* Improved image sizing. Now, the plugin uses the sizes the theme defines and
tries to scale and crop the external image for its proper display.


= 1.0.0 =
* First release.


== Upgrade Notice ==

= 1.3.0 =
NelioEFI implements a completely new approach for inserting external featured
images that makes it compatible with virtually all themes.

