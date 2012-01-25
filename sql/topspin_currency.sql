CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_currency` (
  `currency` varchar(50) NOT NULL,
  `symbol` varchar(50) NOT NULL,
  UNIQUE KEY `currency` (`currency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;