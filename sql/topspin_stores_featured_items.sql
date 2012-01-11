CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_stores_featured_items` (
  `store_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `order_num` int(11) NOT NULL,
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;