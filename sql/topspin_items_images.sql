CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_items_images` (
  `item_id` int(11) NOT NULL,
  `source_url` text NOT NULL,
  `small_url` text NOT NULL,
  `medium_url` text NOT NULL,
  `large_url` text NOT NULL,
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;