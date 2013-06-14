# Overview

Integrate Topspin Offers into customized, dynamically generated Store Pages; recoded and optimized for better Wordpress integration.

[Installation](https://github.com/topspin/topspin-wordpress/wiki/Installation)

[Upgrading](https://github.com/topspin/topspin-wordpress/wiki/Upgrading)

[Managing Stores](https://github.com/topspin/topspin-wordpress/wiki/Managing-Stores)

[Template Customization](https://github.com/topspin/topspin-wordpress/wiki/Template-Customization)

[Frequently Asked Questions](https://github.com/topspin/topspin-wordpress/wiki/Frequently-Asked-Questions)

[Roadmap](https://github.com/topspin/topspin-wordpress/wiki/Roadmap)

[Changelog](https://github.com/topspin/topspin-wordpress/wiki/Changelog)

# Description

Developed by [The Uprising Creative](http://theuprisingcreative.com)
  
***If you are updating an existing Topspin Wordpress store, please test the update on a separate development server before updating your live store to ensure a successful transition. Every Wordpress installation is unique and updating may cause unforeseen issues on your particular setup.***

***Not sure what a development server is? The Topspin Wordpress Plugin is designed for developers or users who are comfortable troubleshooting and customizing web technologies. If you don't fall in to either of these categories, we recommend embedding Topspin's fully supported and easy to use [Spinshop](http://labs.topspin.net/features/spinshop/).***

***This plugin is no longer supported by The Uprising Creative and is now 100% open-source. We offer limited support- if you do have an issue or question, please email[wordpress@topspinmedia.com](mailto:wordpress@topspinmedia.com) and we'll work to help you out when time allows. GithHub users are encouraged to pull forks of the plugin and develop as needed.***
 
*PLEASE NOTE: This plugin is meant to be customized! It will work "out of the box" with just about any theme, but it may not match your site or be as "pretty" as you'd like.  Additionally, if your theme uses featured images in the header of posts, you may have to make adjustments to ensure the display of the products is to your liking.*

This plugin allows Topspin users to create integrated, customized, sortable and dynamically generated stores from their Topspin Offers.

The plugin allows for the creation of any number of individual Store Pages and configurations of Topspin Offers based on Offer Types and Tags. Store Pages can be restricted to specific Offer Type(s) and/or Tag(s) and can then be sorted by Offer Type(s), Tag(s), or Manually using a new drag-and-drop admin interface. 

This new version of the plugin has been recoded from scratch to offer better performance and scalability, better integration with Wordpress post types, allows for better deep linking to individual products, support for multiple artists and Wordpress Multi Site, and much more. 

Although these pages are called Store Pages, they aren't restricted to just the Buy Button Offer Type.  You can create dynamic pages featuring Buy Buttons, Email For Media Widgets, Streaming Player Widgets, Single Track Player Widgets, and any combination of them. 

(For example, a Store Page can be created that lists all Buy Buttons.  A second Store Page can be created that lists Streaming Players, or some manually sorted combination of Email-For-Media Widgets, Streaming Players, and Buy Buttons.  A third Store Page can be created that lists only Buy Buttons with the Apparel tag, effectively creating an Apparel sub-category for the main store.  Since Tags are custom and manually created, you can group your products in an way that works for your individual needs, such as Featured Items, On-Sale Items, etc.)

In addition to automatically creating Store Pages, Shortcodes are also generated for each Store Page that allow it's contents to be embedded on any other Page or Post.  Shortcodes are also generated for individual offers, allowing any offer to be embedded in any post on your site. 

The possibilities are endless. 

### Requirements

* Topspin Artist Account ([Sign up for a new account!](http://topspinmedia.com))
* PHP 5.0 or higher
* MySQL 5.0 or higher

## Live Examples

* [Beastie Boys](http://shop.beastieboys.com)
* [Circa Survive](http://circasurvive.com/shop/) 
* [Black Rebel Motorcycle Club](http://blackrebelmotorcycleclub.shopfirebrand.com)

## Features

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

### New for v4.1

*Updated WP_MediaHandler class with new image copying method
*Secondary images (if available) are pulled into the WordPress Media Library
*new WP-cron is spawned to handle secondary image caching
*Added a progress notification message when offers are syncing in the CMS
*Misc. PHP warning fixes
*Now Supports Selling from Backorer Cap.
