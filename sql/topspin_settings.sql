CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
