CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_artists` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `avatar_image` text NOT NULL,
  `url` text NOT NULL,
  `description` text NOT NULL,
  `website` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
