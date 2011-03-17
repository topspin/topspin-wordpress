<?php

### Pathing Constants
define('TOPSPIN_VERSION','3.0.0');
define('TOPSPIN_PLUGIN_PATH',dirname(__FILE__));
define('TOPSPIN_PLUGIN_URL',WP_PLUGIN_URL.'/'.basename(TOPSPIN_PLUGIN_PATH));
define('TOPSPIN_CURRENT_THEME_PATH',get_theme_root().'/'.get_template());
define('TOPSPIN_CURRENT_THEME_URL',get_bloginfo('template_directory'));

### Include Plugin Classes
require_once('classes/Topspin_Store.php');

### Include Plugin Files
require_once('topspin_activation.php');
require_once('topspin_shortcodes.php');
require_once('topspin_ajax.php');

### Initializes Plugin
$store = new Topspin_Store();

### Topspin Constants
define('TOPSPIN_ARTIST_ID',$store->getSetting('topspin_artist_id'));
define('TOPSPIN_API_KEY',$store->getSetting('topspin_api_key'));
define('TOPSPIN_API_USERNAME',$store->getSetting('topspin_api_username'));

$store->setAPICredentials(TOPSPIN_ARTIST_ID,TOPSPIN_API_KEY,TOPSPIN_API_USERNAME);

?>