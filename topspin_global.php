<?php

### Pathing Constants
define('TOPSPIN_VERSION','3.0.4');
define('TOPSPIN_PLUGIN_PATH',dirname(__FILE__));
define('TOPSPIN_PLUGIN_URL',WP_PLUGIN_URL.'/'.basename(TOPSPIN_PLUGIN_PATH));
define('TOPSPIN_CURRENT_THEME_PATH',get_theme_root().'/'.get_stylesheet());
define('TOPSPIN_CURRENT_THEME_URL',dirname(get_stylesheet_uri()));
define('TOPSPIN_CURRENT_THEMEPARENT_PATH',get_theme_root().'/'.get_template());
define('TOPSPIN_CURRENT_THEMEPARENT_URL',get_template_directory());

### Include Plugin Classes
require_once('classes/Topspin_Store.php');

### Initializes Plugin
$store = new Topspin_Store();

### Topspin Constants
define('TOPSPIN_ARTIST_ID',$store->getSetting('topspin_artist_id'));
define('TOPSPIN_API_KEY',$store->getSetting('topspin_api_key'));
define('TOPSPIN_API_USERNAME',$store->getSetting('topspin_api_username'));

$store->setAPICredentials(TOPSPIN_ARTIST_ID,TOPSPIN_API_KEY,TOPSPIN_API_USERNAME);

### Include Plugin Files
require_once('topspin_activation.php');
require_once('topspin_upgrade.php');
require_once('topspin_cron.php');
require_once('topspin_shortcodes.php');
require_once('topspin_ajax.php');

##	Deprecated (version 2.0.0)
require_once('deprecated/topspin_2.0.php');

?>