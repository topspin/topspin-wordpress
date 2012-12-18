=== Official Topspin Wordpress Plugin ===
Contributors: theuprising
Donate link: http://theuprisingcreative.com/
Tags: Topspin,store,merchandise,shop,music
Tested up to: 3.5
Stable tag: 4.0.3
Requires at least: 3.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly, easily integrate Topspin Offers into customized, dynamically generated Store Pages; recoded and optimized for better Wordpress integration. 

== Description ==

This plugin allows novice and pro Topspin users alike to quickly and easily create integrated, customized, sortable and dynamically generated stores from their Topspin Offers in a few minutes.

Developed by [The Uprising Creative](http://theuprisingcreative.com)
 
This plugin is open-source and we offer limited support.  If you do have an issue or question, please [open a GitHub issue](https://github.com/topspin/topspin-wordpress/issues) and we'll work to help you out when time allows.

*PLEASE NOTE: This plugin is meant to be customized! It will work "out of the box" with just about any theme, but it may not match your site or be as "pretty" as you'd like.  Additionally, if your theme uses featured images in the header of posts, you may have to make adjustments to ensure the display of the products is to your liking.*

The plugin allows for the creation of any number of individual Store Pages and configurations of Topspin Offers based on Offer Types and Tags. Store Pages can be restricted to specific Offer Type(s) and/or Tag(s) and can then be sorted by Offer Type(s), Tag(s), or Manually using a new drag-and-drop admin interface. 

This new version of the plugin has been recoded from scratch to offer better performance and scalability, better integration with Wordpress post types, allows for better deep linking to individual products, support for multiple artists and Wordpress Multi Site, and much more. 

Although these pages are called Store Pages, they aren't restricted to just the Buy Button Offer Type.  You can create dynamic pages featuring Buy Buttons, Email For Media Widgets, Streaming Player Widgets, Single Track Player Widgets, and any combination of them. 

(For example, a Store Page can be created that lists all Buy Buttons.  A second Store Page can be created that lists Streaming Players, or some manually sorted combination of Email-For-Media Widgets, Streaming Players, and Buy Buttons.  A third Store Page can be created that lists only Buy Buttons with the Apparel tag, effectively creating an Apparel sub-category for the main store.  Since Tags are custom and manually created, you can group your products in an way that works for your individual needs, such as Featured Items, On-Sale Items, etc.)

In addition to automatically creating Store Pages, Shortcodes are also generated for each Store Page that allow it's contents to be embedded on any other Page or Post.  Shortcodes are also generated for individual offers, allowing any offer to be embedded in any post on your site. 

The possibilities are endless. 

(This new version of the Official Topspin Wordpress Plugin is a complete rebuild from the ground up, simplified and optimized to make launching an integrated Topspin Wordpress store in minutes. *This plugin is in active development and more features will be added regularly.  If you have a feature request or find a bug, please report it! Please see the Roadmap for more information.*)

Plugin developed for [Topspin Media](http://topspinmedia.com/) by [The Uprising Creative](http://theuprisingcreative.com)

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
* Immediate output of fully functional Store Pages will little or no CSS or Template customization (novice)
* Fully customizable Template and CSS files (pro)
* New template tags available to display additional images and/or descriptions in the product grid
* Ability for multiple Featured Items on a store page
* Ability to embed individual items into any post on the site using new Individual Item Shortcodes
* Additional Image added to display inside the More Info Lightbox (see notes)
* Ability to link directly to and share an Individual Item's More Info Lightbox using new individual item Google-indexable hash-based permalinks (see notes)

= New Features for v4.0 =

* Completely rebuilt from the ground up for better performance and Wordpress integration 
* Individual products, offers and stores are created as Wordpress posts in a custom post type, allowing for better integration, deep linking, SEO indexing, and performance 
* Ability to sync and create stores for multiple artists under one install 
* Support for Wordpress Multi Site installs
* API Prefetching (optional) 
* Product images are now cached in the Media Library for use elsewhere on the site 
* Ability to customize the custom post types used for offers, stores, and products 
* Ability to mark items On Sale or New with automatic timeouts 
* Ability to manually sync artists and offers 
* Ability to create a navigation menu using a drag-and-drop interface

== Installation ==

This plugin requires a Topspin Artist account to function.  If you don't have an account, you can get one here: [http://topspinmedia.com](http://topspinmedia.com)

= Before Installation =

It is highly recommended that you upgrade your WordPress installation to the latest version to take advantage on any new and upcoming features.

= Initial Installation =

1. Use Wordpress's automatic install functionality, or upload the `official-topspin-wordpress-plugin` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress. This will install your database tables and create a new top-level menu called *Topspin*.
3. Make sure your template is calling `wp_head()` and `wp_footer()` to allow loading of the necessary Topspin javascript libraries, template files and CSS. *(99.9% of them do so this shouldn't be an issue)* 
4. Go to *Settings -> Permalinks* and make sure you're using some form of Custom Permalinks. It can be set to anything other than *Default*.
5. Go to the *Topspin -> Settings* menu and add in your Topspin API User and API Key (you can get these from your Account settings at [http://app.topspin.net](http://app.topspin.net)) and select the Artist Account you wish to work with from the drop-down (if you have more than one account associated with your API User/Key)
6. Make sure the Offers you want to use have the *Offer API* checkbox selected.  If they don't, they won't be output by Topspin's API and won't show up as available Offers in the plugin.

== Upgrade Notice ==

= Upgrading from v3.0 or later =

These upgrade instructions are specifically for users of the new plugin from v3.0 or newer, NOT users who are still on the old v2 Plugin. If you are using the v2 Plugin currently, please see the v2 Upgrade Instructions below.

1. Backup your site and database (in case something goes wrong!). 
2. Automatically upgrade from within Wordpress OR download the new plugin and overwrite the `official-topspin-wordpress-plugin` folder in your `/wp-content/plugins/` directory
3. Check your pages to make sure they all look right
4. You're done!

*PLEASE NOTE: The plugin now uses the built-in Wordpress Custom Post Type system.  This allows for more flexibility, better SEO, better deep linking, better caching, etc but it may change the relationships your site has with the store pages and products.  Be sure to make a backup and if possible, test on a dev environment first!*

*ADDITIONAL NOTE: This plugin is meant to be customized! It will work "out of the box" with just about any theme, but it may not match your site or be as "pretty" as you'd like.  Additionally, if your theme uses featured images in the header of posts, you may have to make adjustments to ensure the display of the products is to your liking.*

= Upgrading from an v2.0 or earlier =

This plugin is a complete re-write.  Upgrading form a previous version will not save your existing Store settings and will disable some of the Theme settings used in the old plugin.  This is because this new version is designed to work with any Theme and Wordpress setup like any standard plugin instead of taking over parts of the core WordPress functionality.  

In order to upgrade from a previous version you will need to follow the steps below:

1. Backup your site and database (in case something goes wrong!). 
2. Make sure the Theme you are using is located in your site's Theme directory, NOT in the old plugin's directory. 
3. Deactivate the existing Topspin v2.0 Plugin.
4. Delete the existing Topspin v2.0 Plugin.
5. Delete your existing `Store` Page. 
6. Empty your Trash. 
7. Install the new v3 Plugin as described above. 
8. Configure your new Store Page(s) using the new plugin. 

Once installed, the easiest way to upgrade to future versions is to use the automatic upgrade feature built into WordPress.  You will be automatically notified of any updates to the plugin and given the option to install them with a single click.

== Usage ==

Please check out [GitHub page](https://github.com/theuprising/topspin-wordpress) for full usage documentation.

== Customization ==

All markup output by this plugin is XHTML-compliant and heavily classed to allow for easily styling with CSS. The plugin comes with two different 'templates' called `Standard` and `Simplified`.

*PLEASE NOTE: This plugin is meant to be customized! It will work "out of the box" with just about any theme, but it may not match your site or be as "pretty" as you'd like.  Additionally, if your theme uses featured images in the header of posts, you may have to make adjustments to ensure the display of the products is to your liking.*

The `Simplified Template` is designed to give the best out-of-the box store layout if you prefer to do little or no customization.  It uses HTML Tables to construct the Store Grid and therefore is less flexible for developers who wish to heavily customize the store's output. 

The `Standard Template` is designed as a skeleton framework with the Developer in mind, allowing for the most flexibility for template customizations. It uses floating HTML Divs to construct the Store Grid rather than Tables, making it easier to manipulate.

You select which `template` you wish to use on the *Topspin->Settings* page. 

If you wish to edit the style's CSS or Template, simply follow these directions:

1. Copy the template's sub-directory from the Plugin's `/templates/` directory to your Site's active theme folder (`/topspin-simplified/` for the Simplified Template and `/topspin-standard/` for the Standard Template)
2. Edit the new CSS or PHP Template file(s) in your Site's active theme folder as needed.

= CSS Customization Details =
These new CSS style sheets in your active theme's directory are considered "override" style sheets and only need to contain rules for elements, ids and classes that you wish to modify.  The remaining rules from the default CSS style sheets will automatically be used if no overriding rules are included.

= Template Customization Details =
If you are a pro user and are comfortable with creating and editing WordPress themes, the plugins template can be easily customized to suit your needs. Below is a list of important PHP variables used in these templates: 

= Additional Customization Details =
1. It is very important that you copy the Template files to your site's active theme directory.  If you edit them in the Plugin's `/templates/` directory, any changes you make will be overwritten when you upgrade the plugin! 
2. Backwards Compatibility: For users upgrading from a version pre-v3.1, the Plugin will still recognize your customized `topspin.css`, `topspin-ie7.css`, `featured-item.php` and `item-listings.php` files located in your site's active theme folder if you select the Standard Template, even if they aren't in the /topspin-standard/ sub-folder.)
3. The ability to use the new - as of v3.2 - additional images and permalink features requires an upgrade from the pre-v3.2 templates to the new Standard or Simplified templates.  If you are using the default templates, these changes will automatically be made on upgrade.  If you are using customized templates, you'll need to either a) update your template manually or b) copy over the new default template and re-style as needed.

== Frequently Asked Questions ==

**I'm using the v2.0 Plugin.  Can I just delete it and install this new version?**

> Yes and no.  Please check out the Installation section and refer to the detailed Upgrade Instructions to avoid the potential problems upgrading from v2.0 to this version of the plugin. 

**I'm not using the v2.0 Plugin.  Do I need to be aware of any potential issues installing this version?**

> No!  If you're not using v2.0, the Installation process is pretty painless and you can have a store up and running in as little as 5min. 

**I've installed the plugin, but none of my Offers are showing up.  Help!**

> First make sure your API User and API Key are correct.  If they are, make sure the offers you wish to display on your site all have the `Offers API` checkbox marked in the Topspin App.  If your info is correct and the Offers API checkboxes are marked but it's still not working, please contact Topspin for support with your Topspin Account: [http://www.topspinmedia.com/contact](http://www.topspinmedia.com/contact)

**Where do I get help with Topspin?**

> Lot's of resources are available.  This is a good place to start: [http://www.topspinmedia.com/help](http://www.topspinmedia.com/help)

**I found a bug!  Where can I report it?**

> [Here!](https://github.com/topspin/topspin-wordpress/issues)

**I want to add functionality to the plugin.  Is that OK?**

> More than OK!  This plugin is completely open-source and to top it off, Topspin pays $150 for each new commit to the plugin!  So head on over to the [GitHub repository](https://github.com/topspin/topspin-wordpress/), grab a fork, and get coding!

**I upgraded from a pre-v3.2 version of the plugin so that I can use multiple images and permalinks, but neither are working.  What do I need to do to enable them?** 

> These features require use of some new elements introduced in the v3.2 templates.  If you are using the default templates located in the Plugins directory (meaning you haven't copied the templates to your theme folder and customized them), these updates will automatically be usable.  However, if you ARE using customized templates located in your Theme folder, you'll need to either manually update the theme with the new code or copy over the new default theme and re-style it as needed.  

**Is it possible to use the plugin with a Topspin child account?**

> Unfortunately it's not. This is due to a limitation in the Topspin API regarding information being passed via the API for child accounts. If you enter your API information into the plugin and none of your products show up, and if your account details were setup or given to you by someone else, there's a good chance you're on a child account. If this happens, check with your Topspin account administrator and see if you are indeed using a child account. If you are, you'll need to get the parent account API details in order for the plugin to work.

== Other Notes ==

= Additional Suggestions =

It is highly suggested that anyone wishing to use this plugin *a) has a solid understanding of how to create Offers in Topspin* and *b) has adequately tested their Store Pages and the checkout flow prior to going live with any Store Pages created by this plugin.*

If you are unsure how to create Offers in Topspin, please visit Topspin's Knowledgebase, [https://docs.topspin.net/](https://docs.topspin.net/)

= All Topspin Offers must have the Offers API checkbox marked. =

Offers without this marked will not show up in the plugin.

= It is highly suggested that all Offer images are setup to be the same size for any particular Store Page if using the Standard Template. =

This is to ensure that the Buy Buttons for each product line up correctly in the Standard Template. In an effort to create the most versatile default template, we haven't forced the Offers into tables in the Standard Template or forced image resizing with Javascript but feel free to do so yourself.  In future updates we may be including phpthumb and the ability to normalize the size of the Offer images on the fly. 

= Performance & Optimization =

We also suggest the use of the following additional plugins to maximize performance and functionality of this plugin, and your site as a whole: 

* [WP Super Cache](http://wordpress.org/extend/plugins/wp-super-cache/) or [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/) - Very fast caching engines for WordPress that produces static html files. Can decrease load times on your site by up to 10x. 
* [Ultimate Google Analytics](http://wordpress.org/extend/plugins/ultimate-google-analytics/) - Quickly and easily add Google Analytics Tracking to all of your site's content

= A Word About Bugs =
As a plugin that's in active development, bugs are going to pop up from time to time as we don't have the ability to fully test on every potential server and/or browser configuration possible.  If you find a bug, please let us know over on [GitHub](https://github.com/topspin/topspin-wordpress/issues) and we'll do our best to get to it ASAP.  Or better yet, fix it yourself and get $150 from Topspin!

= Roadmap =

This plugin is in open development.  Over the weeks and months we hope to roll out regular updates with new features and optimizations, starting with: 

* Updated, Responsive, "Pretty" Front-End Templates
* Increased Inventory Management Functionality - notifications, color coding, etc
* Refined Inventory UI/UX
* Additional Code clean-up / debugging
* Automatic cropping and scaling of thumbnails
* Sidebar widget
* Further customizable page layouts for more dynamic and varied offer page creation without editing Theme or template files
* Addition of Redeem Code Offer Type 
* AJAX-based pagination
* Option to show the Topspin cart at all times, even when empty
* Option to display the Topspin cart on the left or right
* New Shortcodes - Most Popular Products, Custom Content Blocks
* Addition of optional Featured Items fading/sliding marquee 
* Admin Localization
* Optimized output for non-Buy Button Offers
* Plugin Site for further documentation, discussion, feature requests, etc

== Screenshots ==

1. Plugin Admin -> Settings Page
2. Plugin Admin -> Offers Listings
3. Plugin Admin -> Stores Listings
4. Plugin Admin -> Edit/Create Store Page
5. Plugin Admin -> Stores Nav Menu Editor
6. Front-End -> View of an out-of-the-box Store Page with a 3 column display and a Featured Item
7. Front-End -> View of a Store Page's "More Details" Colorbox overlay
8. Front-End -> View of the Topspin Checkout Flow overlay

== Changelog ==

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