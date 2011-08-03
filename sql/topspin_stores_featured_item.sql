CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_stores_featured_items` (
  `store_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `order_num` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;