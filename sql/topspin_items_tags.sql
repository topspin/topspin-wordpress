CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_items_tags` (
  `item_id` int(11) NOT NULL,
  `tag_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
