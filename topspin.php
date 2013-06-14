<?php
/*
Plugin Name: Topspin Store Plugin 4.1
Plugin URI: http://wordpress.org/extend/plugins/official-topspin-wordpress-plugin/
Description: Integrate your Topspin Offers into customized, sortable and dynamically generated Store Pages using the Topspin API.<em><strong> If you are updating an existing Topspin Wordpress store, please test the update on a separate development server before updating your live store to ensure a successful transition. Every Wordpress installation is unique and updating may cause unforeseen issues on your particular setup.</strong></em>
Author: The Uprising Creative for Topspin Media
Author URI: http://theuprisingcreative.com
Version: 4.1
*/

define('TOPSPIN_PLUGIN_FILE', __FILE__);
define('TOPSPIN_PLUGIN_PATH', plugin_dir_path(TOPSPIN_PLUGIN_FILE));	// with trailing slash
define('TOPSPIN_PLUGIN_URL', plugins_url(null, TOPSPIN_PLUGIN_FILE));	// no trailing slash
define('TOPSPIN_VERSION', '4.1');

// Controllers
require_once(sprintf('%s/controllers/WP_Topspin_Hooks_Controller.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Hooks_Custom_Controller.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_CMS_Controller.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Upgrade_Controller.php', TOPSPIN_PLUGIN_PATH));

require_once(sprintf('%s/controllers/WP_Topspin_AJAX.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Cache.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Cron.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Notices.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Shortcodes.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/controllers/WP_Topspin_Template.php', TOPSPIN_PLUGIN_PATH));

// Classes
require_once(sprintf('%s/classes/topspin.api.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/topspinArtist.api.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/topspinOrder.api.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/topspinStore.api.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/ts_ajaxResponse.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/ts_query.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/ts_product.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/wp.mediaHandler.php', TOPSPIN_PLUGIN_PATH));
require_once(sprintf('%s/classes/wp.topspin.php', TOPSPIN_PLUGIN_PATH));

?>