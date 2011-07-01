CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_stores_offer_type` (
  `store_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `order_num` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
