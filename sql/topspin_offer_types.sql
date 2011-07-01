CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_offer_types` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
