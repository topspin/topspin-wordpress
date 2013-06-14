=== Official Topspin Wordpress Plugin ===
Contributors: theuprising
Donate link: http://theuprisingcreative.com/
Tags: Topspin,store,merchandise,shop,music
Tested up to: 3.5
Stable tag: 4.1
Requires at least: 3.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate Topspin Offers into customized, dynamically generated Store Pages; recoded and optimized for better Wordpress integration.

If you are updating an existing Topspin Wordpress store, please test the update on a separate development server before updating your live store to ensure a successful transition. Every Wordpress installation is unique and updating may cause unforeseen issues on your particular setup.

== Description ==

[Installation](https://github.com/topspin/topspin-wordpress/wiki/Installation)

[Upgrading](https://github.com/topspin/topspin-wordpress/wiki/Upgrading)

[Managing Stores](https://github.com/topspin/topspin-wordpress/wiki/Managing-Stores)

[Template Customization](https://github.com/topspin/topspin-wordpress/wiki/Template-Customization)

[Frequently Asked Questions](https://github.com/topspin/topspin-wordpress/wiki/Frequently-Asked-Questions)

[Roadmap](https://github.com/topspin/topspin-wordpress/wiki/Roadmap)

[Changelog](https://github.com/topspin/topspin-wordpress/wiki/Changelog)

Developed by [The Uprising Creative](http://theuprisingcreative.com)

***If you are updating an existing Topspin Wordpress store, please test the update on a separate development server before updating your live store to ensure a successful transition. Every Wordpress installation is unique and updating may cause unforeseen issues on your particular setup.***

***Not sure what a development server is? The Topspin Wordpress Plugin is designed for developers or users who are comfortable troubleshooting and customizing web technologies. If you don't fall in to either of these categories, we recommend embedding Topspin's fully supported and easy to use [Spinshop](http://labs.topspin.net/features/spinshop/) ***

***This plugin is no longer supported by The Uprising Creative and is now 100% open-source. We offer limited support- if you do have an issue or question, please email[wordpress@topspinmedia.com](mailto:wordpress@topspinmedia.com) and we'll work to help you out when time allows. GithHub users are encouraged to pull forks of the plugin and develop as needed.***
 
*PLEASE NOTE: This plugin is meant to be customized! It will work "out of the box" with just about any theme, but it may not match your site or be as "pretty" as you'd like.  Additionally, if your theme uses featured images in the header of posts, you may have to make adjustments to ensure the display of the products is to your liking.*

This plugin allows Topspin users to create integrated, customized, sortable and dynamically generated stores from their Topspin Offers.

The plugin allows for the creation of any number of individual Store Pages and configurations of Topspin Offers based on Offer Types and Tags. Store Pages can be restricted to specific Offer Type(s) and/or Tag(s) and can then be sorted by Offer Type(s), Tag(s), or Manually using a new drag-and-drop admin interface. 

This new version of the plugin has been recoded from scratch to offer better performance and scalability, better integration with Wordpress post types, allows for better deep linking to individual products, support for multiple artists and Wordpress Multi Site, and much more. 

Although these pages are called Store Pages, they aren't restricted to just the Buy Button Offer Type.  You can create dynamic pages featuring Buy Buttons, Email For Media Widgets, Streaming Player Widgets, Single Track Player Widgets, and any combination of them. 

(For example, a Store Page can be created that lists all Buy Buttons.  A second Store Page can be created that lists Streaming Players, or some manually sorted combination of Email-For-Media Widgets, Streaming Players, and Buy Buttons.  A third Store Page can be created that lists only Buy Buttons with the Apparel tag, effectively creating an Apparel sub-category for the main store.  Since Tags are custom and manually created, you can group your products in an way that works for your individual needs, such as Featured Items, On-Sale Items, etc.)

In addition to automatically creating Store Pages, Shortcodes are also generated for each Store Page that allow it's contents to be embedded on any other Page or Post.  Shortcodes are also generated for individual offers, allowing any offer to be embedded in any post on your site. 

The possibilities are endless. 

= Requirements =

* Topspin Artist Account ([Sign up for a new account!](http://topspinmedia.com))
* PHP 5.0 or higher
* MySQL 5.0 or higher

= Live Examples =

* [Beastie Boys](http://shop.beastieboys.com)
* [Circa Survive](http://circasurvive.com/shop/) 
* [Black Rebel Motorcycle Club](http://blackrebelmotorcycleclub.shopfirebrand.com)

= Features =

* Automatic Store Page creation based on Offer Types and Tags
* Display of Offers in some or all Offer Types and/or Tag groups
* Sorting by Offer Type(s), Tag(s), or Manually via the drag-and-drop admin interface
* Ability to select the # of columns for each Store Page from 1-6
* Ability to select the # of Offers to display on each page for Pagination
* Ability to set a Featured Item for each Store Page
* Customizable store name and slug
* Shortcodes for both Store and individual Featured Item content
* Immediate output of fully functional Store Pages 
* Fully customizable Template and CSS files 
* New template tags available to display additional images and/or descriptions in the product grid
* Ability for multiple Featured Items on a store page
* Ability to embed individual items into any post on the site using new Individual Item Shortcodes
* Additional Image added to display inside the More Info Lightbox (see notes)
* Ability to link directly to and share an Individual Item's More Info Lightbox using new individual item Google-indexable hash-based permalinks (see notes)

= Updates in v4.1 =

* Updated WP_MediaHandler class with new image copying method
* Secondary images (if available) are pulled into the WordPress Media Library
* new WP-cron is spawned to handle secondary image caching
* Added a progress notification message when offers are syncing in the CMS
* Misc. PHP warning fixes
* Now Supports Selling from Backorer Cap.


== Installation ==

Please chek out the [Installation Wiki](https://github.com/topspin/topspin-wordpress/wiki/Installation) for a full guide on setting up your WordPress Topspin store.

== Usage ==

Please check out [GitHub page](https://github.com/theuprising/topspin-wordpress) for full usage documentation.

== Frequently Asked Questions ==

Please refer to our [Wiki](https://github.com/topspin/topspin-wordpress/wiki/Frequently-Asked-Questions) for the FAQ's.


== Upgrade Notice ==

= 4.0.1 =
* This fixes several issues for version 3.0 plugin users who are upgrading to 4.0.

== Changelog ==

= 4.1 =

* Updated WP_MediaHandler class with new image copying method
* Secondary images (if available) are pulled into the WordPress Media Library
* new WP-cron is spawned to handle secondary image caching
* Added a progress notification message when offers are syncing in the CMS
* Misc. PHP warning fixes
* Now Supports Selling from Backorer Cap.

= 4.0.9.1 =
* Fixed manual sorting query bug
* Fixed inventory count for "other_media" offer types

= 4.0.9 =
* Added manual sorting show/hide toggling
* Video offers now bypasses sold out check

= 4.0.8 =
* Added an inventory metabox to Products post type
* Inventory bug fixes
* Several php warning fixes
* Purchase Flow lightbox bug fix

= 4.0.7 =
* Updated the admin to display non-editable fields for cached metadata.
* Fixed some PHP warnings.

= 4.0.6 =
* Bug fix: purge old unattached thumbnail files

= 4.0.5 =
* Fixed Artists and Offers not updating thumbnail meta data.
* Allowed tickets stock to bypass the sold-out check.
* Several PHP warning fixes.
* Other minor bug fixes.

= 4.0.4 =
* Updated PHP docblocks.
* Fixed ts_the_embed_code() and ts_get_the_embed_code().
* Fixed Group Panel features bug.
* Several bug fixes.

= 4.0.3 =
* Added the ability to change the default grid thumb size (for narrow or wider templates)

= 4.0.2 =
* Added the ability to group panels
* Added the ability to disable the WP admin bar shortcut	
* Fixed some PHP warnings

= 4.0.1 =
* Legacy store bug fixes
* Fixed some PHP warnings

= 4.0.0 =

* Complete rewrite from the ground-up for performance increases and better Wordpress integration
* Sync multiple artists
* Stores, and Offers are now pulled in as WordPress custom post types
* Products are now pulled into Wordpress
* Inventory for products is now displayed in the CMS
* New item tags (site level)
* On Sale item tags (store level)
* API prefetching (advanced settings)
* Photos are now cached into the WordPress Media Library
* Spin Tags are pulled in as a custom taxonomy made available for the new Offers custom post type
* Wordpress Multi Site support
* Improved WP-Cron integration
* Ability to resync an individual offer (via Edit Offer)
* Individual offer page templates 
* A wide array of various bug fixes

= 3.3.3.3 =

* Fixed lightbox handler with auto-filled anchor tags - @ezmiller

= 3.3.3.2 =

* Fixed JS handler to work with auto-filled anchor tags - @ezmiller
* Fixed Topspin_Store::getItem() multiple tag bug - @jackdaw4 

= 3.3.3.1 =

* Cleaned and updated several upgrade scripts
* Fixed several SQL import warnings

= 3.3.3 =

* Fixed caching issue where only the first 25 returned artists are cached
* Updated artist dropdown selector to be ordered by name
* Fixed Simplified item listing template to display streaming and embedded widgets
* Added new nav menu shortcode/settings panel

= 3.3.2.3 =

* Fixed WP-Cron issue

= 3.3.2.1 =

* Fixed plugins rerun upgrades script.

= 3.3.2 =

* Fixed/updated several initial SQL files.
* Updated Fix Upgrade function to rerun all initial SQL before re-running all upgrade scripts.

= 3.3.1 =

* Fixed plugin upgrade bug.
* Fixed some PHP warnings.
* Fixed format string for product_get_most_popular_list().

= 3.3 =

* Fixed some PHP warnings.
* Added caching from the Orders API.
* Added internal names for each Store for administrative purposes.
* Added new sections in the WordPress Admin: View Most Popular, and View Orders.
* Updated colorbox to the latest version and resolves how it is loaded. @blauwers
* Updated View Stores to now display store pages in a nested list, the full permalink, and the new internal name.
* Updated the items caching method store the campaign ID.
* Updated FAQ.
* Added new topspin template tags/functions: topspin_get_store_items(), and topspin_get_most_popular_items().
* Moved JS to footer for faster page load performance. @blauwers

= 3.2.3 =

* Fixed the Product Sorting and Sort By bug to now work with the Preview.
* Fixed some PHP warnings.

= 3.2.2 =

* Added the ability to set parent pages and template files for each store.

= 3.2.1 =

* Updated [topspin_featured_item] shortcode bug (returning empty item if no featured item is set).
* Updated the installation script to add unique key for the currency table.
* Updated the default standard/simplified featured item template css to work with fluid layouts.
* Fixed the colorbox issue where it loads on all URL hashes.

= 3.2 =

* Added the ability to add multiple featured items per Store Page
* Added notice that switching Artist ID in the settings will clear the content on all current stores
* Added a new Items section to show all of the available offers in the cache database
* Added a new Individual Items shortcode to allow individual items to be embedded anywhere on the site using the Feature Item template
* Fixed and updated various Tag-related output, cache, and sorting bugs
* Added output of a Store Preview on the Add and Edit Store pages based on offer types, tags, and sorting method selected (previously a preview was only shown if Manual sorting was selected) 
* Added a template function topspin_get_item_photos() to output all of an item's images instead of just the poster image 
* Added a template function topspin_get_items() to output all of an item's data to an array
* Added the output of all additional images in the More Info lightbox for each default theme 
* Added the ability to click a Featured Item to get More Info and see all the additional images
* Added Google-indexable hash-based permalinks for each item to allow the page to be opened directly to that item's More Info lightbox when shared
* Adjusted the size of the More Info Lightbox images to be larger (400px wide/tall instead of 280px wide/tall) 
* Adjusted the default layout of the More Info Lightbox for better UI and to include the new additional image thumbnails 
* Added the ability of the large image inside the More Info Lightbox to be clicked for a larger view inside the Lightbox
* Other miscellaneous bug fixes, php warning fixes, and typo corrections

= 3.1.3.6 =

* Changed the default image of each item to retrieve the large version instead of the medium
* Updated the standard template css item list to clear on the last item

= 3.1.3.5  =

* Removed <?= php shortcode for those php installations where shortcodes are turned off (via blauwers)
* Created a fallback method for when the curl imodule is not installed (via blauwers)

= 3.1.3 =

* Updated default simplified and standard template's default css
* Fixed duplicated item bug on manual sorting mode

= 3.1.2 =

* Fixed the PHP fatal error calling an undefined method: Topspin_Store::setError() when adding/editing a store
* Updated the version parsing string in the plugin upgrade functions (bug for PHP versions lower than 5.2)
* Added the ability to force re-run of all upgrade scripts (for users who are experiencing problems when upgrading their plugins)

= 3.1.1 =

* Fixed the plugin upgrading check function
* Added caching for the poster_image_source from the API to the database (new field in the items table)
* Updated the default item images to pull the size according to the poster_image_source (updated template files)

= 3.1 =

* Added automatic selection of the Artist ID based on the API User and API Key 
* Added ability for users with multiple Artist IDs related to their API User / Key combo to select which artist to use 
* Added Simplified Template option for table-based template structure (ideal for out-of-the-box usage with little or no customization requirements) 
* Added Topspin's additional images sizes to the local cache instead of just using the full-size image for everything.  Should give a significant increase in performance and decrease in load-times for users with large original images and/or lots of products.
* Updated template structure to include sub-directories and the ability to have and select from a set group of multiple templates
* Updated documentation with regards to new template structure and customization
* Updated CSS and Template files to avoid being overwritten by a theme's style.css file
* Fixed additional foreach() errors on lines 720 and 731 of Topspin_Store.php
* Fixed upgrading schema to make sure all necessary upgrades run regardless of which version you are upgrading to/from
* Fixed issue where the permalinks weren't working correctly if Wordpress's Home and Site URLs were different

= 3.0.4.1 =

* Updated template's default css
* Updated "API Username" field name to "API User" and moved before "API Key"

= 3.0.4 =

* Fixed additional foreach() warnings in Topspin_Store.php
* Fixed documentation reference to v2 header image title 
* Fixed documentation typos / formatting errors
* Replaced Rebuild function with a safer INSERT INTO MySQL operation in case of rebuild error which may have been causing database tables to be truncated and not rebuilt in rare cases.

= 3.0.3 =

* Fixed upgrading issue

= 3.0.2 =

* Added documentation for upgrading from v2.0 (Thanks to John Jacobus / TRICIL)
* Added FAQ and cleaned up documentation
* Added v2 functions necessary for the v2 Theme to work (Thanks to John Jacobus / TRICIL)
* Added error/success messages on Settings page
* Fixed version tagging
* Fixed plugin name and author info
* Fixed foreach() PHP Warning in Topspin_Store.php (Thanks to iamanadultnow)
* Fixed ability for custom CSS and template files to be used in Child Themes
* Fixed wp-cron issue related to expiration of Topspin CDN for Offer images
* Simplified Cache Rebuild on Settings page

= 3.0.1  =

* Added default CSS styling for the Topspin Cart
* Added screenshots
* Cleaned-up documentation

= 3.0 =

* Completely rewritten from the ground-up (based on the v3.0beta plugin originally started by New Black)

= 2.0 =

* Second version (by StageBloc)

= 1.0 =

* First version