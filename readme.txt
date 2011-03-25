=== Official Topspin Wordpress Plugin ===
Contributors: theuprising
Donate link: http://theuprisingcreative.com/
Tags: Topspin,store,merchandise,shop,music
Tested up to: 3.1
Stable tag: 3.0.4.1
Requires at least: 3.0.2

Quickly and easily integrate your Topspin Offers into customized, sortable and dynamically generated Store Pages.


== Description ==
This plugin allows novice and pro Topspin users alike to quickly and easily create integrated, customized, sortable and dynamically generated stores from their Topspin Offers in a few minutes.  

= Live Examples =
http://awolnationmusic.com/shop
http://deepdarkrobot.com/store 

The plugin allows for the creation of any number of individual Store Pages and configurations of Offers based on Offer Types and Tags. Store Pages can be restricted to specific Offer Type(s) and/or Tag(s).  They can then be sorted by Offer Type(s), Tag(s), or Manually using a new drag-and-drop admin interface. 

Although these pages are called Store Pages, they aren't restricted to just the Buy Button Offer Type.  You can create dynamic pages featuring Buy Buttons, Email For Media Widgets, Streaming Player Widgets, Single Track Player Widgets, and any combination of them. 

(For example, a Store Page can be created that lists all Buy Buttons.  A second Store Page can be created that lists Streaming Players, or some manually sorted combination of Email-For-Media Widgets, Streaming Players, and Buy Buttons.  A third Store Page can be created that lists only Buy Buttons with the Apparel tag, effectively creating an Apparel sub-category for the main store.  Since Tags are custom and manually created, you can group your products in an way that works for your individual needs, such as Featured Items, On-Sale Items, etc.)

In addition to automatically creating Store Pages, Shortcodes are also generated for each Store Page that allow it's contents to be embedded on any other Page or Post.  

The possibilities are endless. 

This new version of the Official Topspin Wordpress Plugin is a complete rebuild from the ground up, simplified and optimized to make launching an integrated Topspin Wordpress store in minutes.  *This plugin is in active development and more features will be added regularly.  Please see the Roadmap for more information.*

= v2.0 USERS PLEASE NOTE =
This plugin will not automatically upgrade the v2.0 Topspin Wordpress Plugins - it's completely re-written and must be installed individually / new. Please check out the **Upgrade Instructions**. 

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

= Topspin Artist Account Required.  Please visit: http://topspinmedia.com to sign up. =

Plugin developed for Topspin Media by The Uprising Creative, http://theuprisingcreative.com

== Installation ==

This plugin requires a Topspin Artist account to function.  If you don't have an account, you can get one here: http://topspinmedia.com

= Initial Installation =
These instructions are specifically for new users who are NOT currently using the v2 Plugin.  If you are using the v2 Plugin currently, please see the Upgrade Instructions below. 

1. Upload the `official-topspin-wordpress-plugin` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress. This will install your database tables and create a new top-level menu called *Topspin*.
3. Make sure your template is calling `wp_head()` and `wp_footer()` to allow loading of the necessary Topspin javascript libraries, template files and CSS. *(99.9% of them do so this shouldn't be an issue)* 
4. Go to *Settings->Permalinks* and make sure you're using some form of Custom Permalinks. It can be set to anything other than *Default*.
5. Go to the *Topspin -> Settings* menu and add in your Topspin Artist ID, API Key, and API Username (you can get these from your Account settings at http://app.topspin.net)
6. Make sure the Offers you want to use have the *Offer API* checkbox selected.  If they don't, they won't be output by Topspin's API and won't show up as available Offers in the plugin.

---

= Upgrading from an earlier version = 
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

= IF YOU ARE USING THE OLD PLUGIN'S THEME: =
Please be aware that you will lose the *Landing Page* it allowed you to create, along with some of the control over the theme from the admin panel.  You will still be able to change the header image by deleting the file in the *Media Library* called `topspin-store` and uploading a new file named `topspin-header`.  *However, we recommend migrating your site to an entirely new theme if at all possible to avoid future problems.*

---

= CSS Customization = 
All markup output by this plugin is XHTML-compliant and heavily classed to allow for easily styling with CSS. The plugin comes with basic CSS style sheets named `topspin.css` and `topspin-ie7.css` located in the `/css/` directory in the `/topspin/` plugin directory.  

If you wish to edit the CSS styling, simply follow these directions.  Following this process will ensure that your custom CSS is not overwritten when upgrading the plugin in the future: 

1. create a new `topspin.css` file in your active theme's directory. 
2. copy any rules you wish to modify from the `topspin.css` file in the plugin's `/css/` directory into your new `topspin.css` file in your active theme's directory.
3. if you need to create specific rules for IE7, create a new `topspin-ie7.css` in your active theme's directory and add in any necessary IE7-specific rules. 

These CSS style sheets in your active theme's directory are considered "override" style sheets and only need to contain rules for elements, ids and classes that you wish to modify.  The remaining rules from the default CSS style sheets will automatically be used if no overriding rules are included.

---

= Template Customization =
If you are a pro user and are comfortable with creating and editing WordPress themes, the plugins template can be easily customized to suit your needs.  Simply follow the steps below to copy the template files from the plugin's `/templates/` directory to your active theme's directory and begin your customization. Following this process will ensure that your custom template modifications are not overwritten when upgrading the plugin in the future: 

1. create a directory named `/topspin-templates/` in your active theme's directory. 
2. copy the template files from the plugin's `/templates/` directory to your newly created `/topspin-templates/` directory. 
3. have fun modifying! below is a list of important PHP variables used in these templates: 
	in featured-item.php:
	$featureditem (array) - the array that contains the Featured Item's info
	in item-listing.php:
	$storedata (array) - the array that contains the general Store Page data
	$storeitem (array) - the array that contains all of the items on the Store Page

---

= Topspin Artist Account Required.  Please visit: http://topspinmedia.com to sign up. =


== FAQ ==

= What's the deal with the Permalink Structure? = 
You need to make sure you are using a *Custom Permalink Structure*.  This can be any permalink structure found on the *Settings->Permalinks* admin page EXCEPT Default.  If you use Default, the plugin will not work at all. 

= The plugin isn't working. My site's links look like http://example.com/?p=123 - could this be a problem? =
YES!  It is not working because you are NOT using a Custom Permalink Structure.  The *Default* setting on the *Settings->Permalinks* admin page produces site links that look like http://example.com/?=123 instead of nice descriptive links. Please change your Permalink structure to one of the other settings. 

= I'm using the v2.0 Plugin.  Can I just delete it and install this new version? 
Yes and no.  Please check out the `Installation` page and refer to the detailed Upgrade Instructions to avoid the potential problems upgrading from v2.0 to this version of the plugin. 

= I'm not using the v2.0 Plugin.  Do I need to be aware of any potential issues installing this version? = 
No!  If you're not using v2.0, the Installation process is pretty painless and you can have a store up and running in as little as 5min. 

= I've installed the plugin, but none of my Offers are showing up.  Help! = 
First make sure your Artist ID, API Username, and API Key are correct.  If they are, make sure the offers you wish to display on your site all have the `Offers API` checkbox marked in the Topspin App.  If your info is correct and the Offers API checkboxes are marked but it's still not working, please contact Topspin for support with your Topspin Account: http://www.topspinmedia.com/contact

= Where do I get help with Topspin? = 
Lot's of resources are available.  This is a good place to start: http://www.topspinmedia.com/help

= I found a bug!  Where can I report it? =
Here! https://github.com/topspin/topspin-wordpress/issues


== Usage ==

= Adding Store Pages =
Once installed, use the *Topspin -> Add Store* menu to add a new Store Page.  Select from the available options, sort as desired, save the page, and your new Store Page will be automatically generated.  This new page's URL will be your site's home URL + `/slug/` (slug being the slug you gave it when creating it.  If no slug is entered, it is automatically generated using the Store's name).

= From this Add Store menu you can set the following: =
* Store Name (output name / title of the store) 
* Store Slug (the slug used in the URL for the store.  defaults to the Store Name if not inputted)
* Items Per Page (used to create a paginated Store Page, display as many Offers as you want per page) 
* Number Of Columns (specify the # of columns in your store output, from 1-5) 
* Default Product Sorting (specify if Offers should be sorted Alphabetically or Chronologically) 
* Sort By (specify if Offers should be sorted by Offer Type, Tags, or Manually)
* Offer Types (select which Offer Types to include on the Store Page and drag-and-drop sort if sorting by Offer Type.  Can be combined with Tags to further limit the Offers to display on the page.) 
* Tags (select to only display Offers with the selected Tags and drag-and-drop sort if sorting by Tags. If none are selected, all Offers are shown.  Can be combined with Offer Types to further limit the Offers to display on the page.) 
* Featured Item (set the Offer to display as a Featured Item on the Store Page.  Optional) 
* Manual Item Selection (used to manually sort the Offers on the store and manually show/hide Offers when Manual sorting is enabled)

---

= Viewing / Editing Existing Store Pages =
You can edit and view existing Store Pages by using the *Topspin -> View Stores* menu. This page also lists the shortcodes for each store and each store's featured content.  

---

= Shortcodes =
Shortcodes can be used on any page or post to output a Store's content or a Store's Featured Item.  All shortcodes use a Store Page's ID and can be found - along with the ID - in the *Topspin -> View Stores* menu. 

`[topspin_buy_buttons id=23]` will display the output of the Store Page with an ID of 23.
`[topspin_featured_item id=9]` will display just the Featured Item from the Store Page with an ID of 9.

(no other shortcode attributes are available at this time)

---

= Additional Suggestions = 
It is highly suggested that anyone wishing to use this plugin *a) has a solid understanding of how to create Offers in Topspin* and *b) has adequately tested their Store Pages and the checkout flow prior to going live with any Store Pages created by this plugin.*

If you are unsure how to create Offers in Topspin, please visit Topspin's Knowledgebase, https://docs.topspin.net/

= All Topspin Offers must have the Offers API checkbox marked. = 
Offers without this marked will not show up in the plugin.

= It is highly suggested that all Offer images are setup to be the same size for any particular Store Page. = 
This is to ensure that the Buy Buttons for each product line up correctly. In an effort to create the most versatile default template, we haven't forced the Offers into tables or forced image resizing with Javascript but feel free to do so yourself.  In future updates we will be including phpthumb and the ability to normalize the size of the Offer images on the fly. 

We also suggest the use of the following additional plugins to maximize performance and functionality of this plugin, and your site as a whole: 

* WP Super Cache - A very fast caching engine for WordPress that produces static html files. Can decrease load times on your site by up to 10x. 
* Ultimate Google Analytics - Quickly and easily add Google Analytics Tracking to all of your site's content

---

== Roadmap ==

This plugin is in open development.  Over the weeks and months we hope to roll out regular updates with new features and optimizations, starting with: 

* Code clean-up / debugging
* Automatic cropping and scaling of thumbnails using phpthumb
* Addition of Facebook Sharing 
* Addition of ReTweeting w/ custom ReTweet Message
* Addition of multiple and manually sortable Featured Items
* Sidebar widget
* Further customizable page layouts for more dynamic and varied offer page creation without editing Theme or template files
* Sorting by Offer Type and Tags together
* Addition of Redeem Code Offer Type 
* AJAX-based pagination
* Option to show the Topspin cart at all times, even when empty
* Option to display the Topspin cart on the left or right
* Allow the inclusion of multiple artist accounts
* Addition of "Most Popular" sorting option
* New Shortcodes - Most Popular Products, Custom Content Blocks
* Addition of optional Featured Items fading/sliding marquee 
* Admin Localization
* Optimized output for non-Buy Button Offers
* Plugin Site for further documentation, discussion, feature requests, etc
* Automatic selection of Artist ID using only API Username and API Key
* Option to display and sort by individual Artist IDs for Business Users who have access to multiple Artist IDs

== Screenshots ==

1. Plugin Admin -> Settings Page
2. Plugin Admin -> View Store Pages List
3. Plugin Admin -> Edit/Create Store Page
4. Plugin Admin -> View of the Edit/Create Store Page's Drag-And-Drop Manual Sort Interface
5. Front-End -> View of an out-of-the-box Store Page with a 3 column display and a Featured Item
6. Front-End -> View of a Store Page's "More Details" Colorbox overlay
7. Front-End -> View of the Topspin Checkout Flow overlay


== Changelog ==

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

= 3.0.1 = 
* Added default CSS styling for the Topspin Cart
* Added screenshots
* Cleaned-up documentation

= 3.0 =
* Completely rewritten from the ground-up (based on the v3.0beta plugin originally started by New Black)

= 2.0 =
* Second version (by StageBloc)

= 1.0 = 
* First version