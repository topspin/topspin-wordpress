CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_tags` (
  `id` int(11) NOT NULL auto_increment,
  `artist_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `artist_id` (`artist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;