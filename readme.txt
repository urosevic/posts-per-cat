=== Posts per Cat [Unmaintained] ===
Tags: category, categories, posts, archives, list, post list, recent, recent posts, excerpt, thumbnail, widget, shortcode
Contributors: urkekg
Donate link: http://urosevic.net/wordpress/donate/?donate_for=posts-per-cat
Requires at least: 3.8.0
Tested up to: 4.4
Stable tag: 1.4.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Group recent posts by category and show them inside boxes organized to columns.

== Description ==

Posts per Cat is a simple plugin that grab all or only selected categories from blog database, and then list recent N posts from each category, organised in 1-5 columns.

IMPORTANT NOTICE
Plugin Posts-per-Cat is unmaintained. That means no further updates or free plugin support will be available.

If you are interested to acquisition of plugin, please [contact us](http://urosevic.net/wordpress/contact/?subject=Posts-per-Cat%20Acquisition).

= Features =
* choose how many boxes per row will be displayed (one, two, three, four or five)
* define number of post titles to display per category
* define category ID's to exclude
* define category ID's to include
* toggle displaying of child categories
* ordering boxes by category ID, title or custom (manually entered category ID's as include list)
* toggle displaying sticky posts
* toggle usage of custom list CSS
* SEO optimized permalink URI's
* integrate to template file, use shortcode [ppc] with options or widget
* ready for localisation
* template system for single post line in box defined by user in plain HTML with macro keywords for post elements

= Shortcode options =
You can use shortcode [ppc], with options below (set option in shortcode to override default settings above):

* `columns=2` - Number of columns (1, 2, 3, 4 or 5)
* `minh=0` - Minimal height of box (in px, set to 0 for auto)
* `include=category_ID's` - Include category (comma separated category ID's)
* `exclude=category_ID's` - Exclude category (comma separated category ID's)
* `parent=0` - Only top level categories (0 or 1)
* `order=ID` - Order categories by (ID, name or custom)
* `catonly=0` - Only from displayed category archive (0 or 1)
* `noctlink=0` - Do not link category name (0 or 1)
* `more=0` - Standalone link to archives (0 or 1)
* `moretxt="More from"` - Archive link prefix
* `posts=5` - Number of headlines per category block
* `porderby=date` - Order posts by date, modified, title, name, ID, author
* `porder=DESC` - Order sorting DESC or ASC
* `titlelen=34` - Headline length (in characters)
* `shorten=0` - Shorten headline (0 or 1)
* `commnum=0` - Display comment number (0 or 1)
* `nosticky=0` - Hide sticky posts (0 or 1)
* `excerpts=none` - Show excerpt (none, first or all)
* `content=0` - Use post content as excerpt (0 or 1)
* `excleng=100` - Excerpt length
* `thumb=0` - Show thumbnail with excerpt (0 or 1)
* `tsize=60` - Thumbnail size, set size in px for thumbnail width (height is same); or set in format WIDTHxHEIGHT (example 220x123); or set predefined custom image size (thumbnail, small, medium, large, full or cusotm defined)

Since version 1.4.0 you can use template to display custom formatted output (post line element). Example:
`[ppc]
<h3><a href="%link%">%title_short%</a></h3>
<span class="comments-meta">(<a href="%comments_link%">%comments_num% comments</a>)</span>
<span class="date-meta">%date% @ %time%</span>
<span class="author-meta"><a href="%author_posts_url%">%author_displayname%</a></span>
%thumbnail%
%excerpt% <a href="%link%">[read more]</a>
[/ppc]`

Supported macros:

* `%title%`
* `%title_short%`
* `%post_content%`
* `%excerpt%`
* `%thumbnail%`
* `%link%`
* `%comments_num%`
* `%comments_link%`
* `%comments_form_link%`
* `%datetime%`
* `%date%`
* `%time%`
* `%author_displayname%`
* `%author_firstname%`
* `%author_lastname%`
* `%author_posts_url%`

== Installation ==

1. Login to your WordPress site and go to page `Plugins`-->`Add New`
2. Type `posts per cat` to `Search` field and press `Search Plugins` button
3. Click on `Install Now` link below `Posts per Cat` name
4. Click on `OK` as answer to question `Are you sure you want to install this plugin?`
5. Click `Activate Plugin` link after success installation
6. Go to `Settings`-->`Posts per Cat` page and configure plugin's options
7. Put code `<?php do_action('ppc'); ?>` in your template file (for example in index.php just before closing `</div><!-- #content -->` tag, or use widget `Posts per Cat` or shortcode `[ppc]`.

== Frequently Asked Questions ==

= I would like to get a list of posts but just from one category =

Enter category ID into `Include category` field, and leave unchecked `Only top level categories` checkbox.

= I set in template macro to display excerpt, but no excerpt displayed =

Enable option `Use post content as excerpt` in widget, or provide shortcode parameter `content=1`, because you did not have manually set excerpts for posts, so you must ask PPC to generate excerpt from post content.

== Screenshots ==
1. Posts per Cat general settings
2. Posts per Cat widget
3. Posts per Cat: custom cats, 3 column, w/o enabled CSS
4. Posts per Cat: all cats, 3 column, w/ enabled CSS

== Upgrade Notice ==
= 1.4.0 =
* Make backup before upgrade!
* Install and activate Redux Framework Plugin to motify global Posts-per-Cat settings.
* Review shortcode/widget settings after upgrade.
* Do not forget to make website backup before plugin upgrade!

= 1.3.0 =
* General settings page moved to Redux framework. Make backup before you update plugin, so you can go back if something go wrong.

= 1.2.0 =
* We changed options names for number of columns, number of posts and excerpts visibility. We recommend you to update plugin settings after update.

== Changelog ==
= 1.4.2 (2015-11-28) =
* Change plugin status to Unmaintained
* Intorduce acquisition of plugin for interested third parties
* Update donation link

= 1.4.1.2 (2015-04-16) =
* Fix missing mb_strlen and mb_substr that cause Fatal error on servers w/ PHP installed w/o --enable-mbstring option

= 1.4.1.1 (2015-03-30) =
* Fix warnings if no default settings defined

= 1.4.1 (2014-09-16) =
* Fix settings page initialization issue
* Fix output when do_action() is used for integration
* Add to Widget settings support for post ordering and sorting (already supported by shortcode since 1.3.0 - undocumented until now)

= 1.4.0 (2014-09-13) =
* Fix usage of predefined include/exclude categories in global settings if no include/exclude categories provided in widget or shortcode
* Fix non-working minimal box height set in widget or shortcode
* Add support for cusotm template output with post element macros
* Add support for non-square thumbnails with WIDTH, WIDTHxHEIGHT or image size name as value
* Change - Link to category can be applied to Category title and to "More" link for category in same time
* Change - Remove embedded Redux from plugin and use Redux Framework Plugin for global PPX settings page
* Change - pack PPC to class

= 1.3.0 (2014-06-01) =
* A lot of fixes made during framework change
* Improved basic block layouts
* Add 5th column for boxes
* Convert settings page to Redux Framework
* Update compatibility to 3.9.1

= 1.2.1 (2014-01-27) =
* Fix widget functionality
* Update compatibility to 3.8.1

= 1.2.0 (2013-11-24) =
* Add shortcode options to override default settings
* Add widget with settings
* Code optimization

= 1.1.0 (2012-04-05) =
* Adds option to disable link on category title
* Adds shortcode [ppc]
* Adds class to headline title and number of comments

= 1.0.0 (2012-01-16) =
* Adds option to toggle comments number with link
* Adds option to use post content in stead of post excerpt
* Adds option for custom category ordering (as listed in Include category)
* Adds option to display PPC in one, two, three or four columns
* Adds available category list in Options page
* All coments in code translated to English
* Follow WP coding standards
* Renamed from Posts-per-Cat to Posts per Cat

= 0.0.14 (2011-04-09) =
* Fixed debug errors
* Fixed (I hope I do) image URI's in CSS
* Adapted for WordPress 3.1
* Added French localisation thanks to Pepita Pop (2010-06-23)

= 0.0.13 (2010-05-27) =
* Fixed path to CSS on nonroot WP installations

= 0.0.12 (2010-05-18) =
* Fixed full width problem in IE6

= 0.0.11 (2010-04-24) =
* Added dirty hack for seervers w/o mb_strlen()
* Added option to filter categories on category archive
* Added option to display thumbnails with excerpts
* Added option to display standalone link to category archive
* Better terminology

= 0.0.10 (2010-01-07) =
* Added option to display two or only one column per row (full width)

= 0.0.9 (2009-10-09) =
* Fixed XHTML validation error for div class
* Added full post title as link title

= 0.0.8 (2009-09-26) =
* Replaced category and post URI with permalink
* Added option to display excerpt for first article only, for all articles or not display at all
* Added option to shorten excerpts to specified length (in characters)
* Added option to shorten post title to specified length (in characters)
* Added options for custom categories to include/exclude
* Moved Settings links in plugin actions
* Fixed and improved ppc.css and ppc-list.css StyleSheets

= 0.0.7 (2009-03-04) =
* Posts per Cat added to WordPress plugin repository
* Fixed broken category URI on non-index pages
* Added ppc action hook

= 0.0.6 (2009-03-03) =
* Fixed SCC URI on non-index pages

= 0.0.5 (2009-02-20) =
* Added plugin option to disable usage of CSS StyleSheet for list styles

= 0.0.4 (2009-02-20) =
* Fixed CSS positioning problem

= 0.0.3 (2009-02-16) =
* Published first public release
* Added Settings page
* Gettexturized (enabled localisation)

= 0.0.2 (2009-02-10) =
* Code packed from functions.php to plugin

= 0.0.1 (2009-02-02) =
* Project initialized
