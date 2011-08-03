CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_stores_tag` (
  `store_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `order_num` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
